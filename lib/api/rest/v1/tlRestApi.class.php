<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *  
 * @filesource 	tlRestApi.class.php
 *
 * @author 		Francisco Mancardi <francisco.mancardi@gmail.com>
 * @package 	TestLink
 * @since     1.9.7             
 * 
 * References
 * http://ericbrandel.com/2013/01/14/quickly-build-restful-apis-in-php-with-slim-part-2/
 * https://developer.atlassian.com/display/JIRADEV/JIRA+REST+API+Example+-+Add+Comment
 * http://confluence.jetbrains.com/display/YTD4/Create+New+Work+Item
 * http://www.redmine.org/projects/redmine/wiki/Rest_api
 * http://coenraets.org/blog/2011/12/restful-services-with-jquery-php-and-the-slim-framework/
 * https://github.com/educoder/pest/blob/master/examples/intouch_example.php
 * http://stackoverflow.com/questions/9772933/rest-api-request-body-as-json-or-plain-post-data
 *
 * http://phptrycatch.blogspot.it/
 * http://nitschinger.at/A-primer-on-PHP-exceptions
 *

 *
 *
 * @internal revisions 
 * @since 1.9.8
 * 20130817 - franciscom - TICKET 5865: REST response header should define content-type application/json
 * 20130722 - franciscom - added array($this,'authenticate') on each route
 *                         to launch always automatically the authentication
 */

require_once('../../../../config.inc.php');
require_once('common.php');
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

/**
 * @author    Francisco Mancardi <francisco.mancardi@gmail.com>
 * @package   TestLink 
 */
class tlRestApi
{
  public static $version = "1.0";
    
    
  /**
   * The DB object used throughout the class
   * 
   * @access protected
   */
  protected $db = null;
  protected $tables = null;

  protected $tcaseMgr =  null;
  protected $tprojectMgr = null;
  protected $tsuiteMgr = null;
  protected $tplanMgr = null;
  protected $tplanMetricsMgr = null;
  protected $reqSpecMgr = null;
  protected $reqMgr = null;
  protected $platformMgr = null;

  /** userID associated with the apiKey provided */
  protected $userID = null;
  
  /** UserObject associated with the userID */
  protected $user = null;

  /** array where all the args are stored for requests */
  protected $args = null;  

  /** array where error codes and messages are stored */
  protected $errors = array();

  /** The api key being used to make a request */
  protected $apiKey = null;
  
  /** boolean to allow a method to invoke another method and avoid double auth */
  protected $authenticated = false;

  /** The version of a test case that is being used */
  /** This value is setted in following method:     */
  /** _checkTCIDAndTPIDValid()                      */
  protected $tcVersionID = null;
  protected $versionNumber = null;
  
  
 
  public $statusCode;
  public $codeStatus;
  
  
  /**
   */
  public function __construct()
  {    
    // We are following Slim naming convention
    $this->app = new \Slim\Slim();
    $this->app->contentType('application/json');


    // test route with anonymous function 
    $this->app->get('/who', function () { echo __CLASS__ . ' : Get Route /who';});

    $this->app->get('/whoAmI', array($this,'authenticate'), array($this,'whoAmI'));
    $this->app->get('/testprojects', array($this,'authenticate'), array($this,'getProjects'));

    $this->app->get('/testprojects/:id', array($this,'authenticate'), array($this,'getProjects'));

    $this->app->post('/testprojects', array($this,'authenticate'), array($this,'createTestProject'));
    $this->app->post('/executions', array($this,'authenticate'), array($this,'createTestCaseExecution'));
    $this->app->post('/testplans', array($this,'authenticate'), array($this,'createTestPlan'));
    $this->app->post('/testplans/:id', array($this,'authenticate'), array($this,'updateTestPlan'));

    $this->app->post('/testsuites', array($this,'authenticate'), array($this,'createTestSuite'));
    $this->app->post('/testcases', array($this,'authenticate'), array($this,'createTestCase'));

    // $this->app->get('/testprojects/:id', array($this,'getProjects'));
    // $this->app->get('/testprojects/:id/testplans/', array($this,'getTestProjectTestPlans'));
    // $this->app->get('/testplans/:id', array($this,'getTestPlan'));


    $this->db = new database(DB_TYPE);
    $this->db->db->SetFetchMode(ADODB_FETCH_ASSOC);
    doDBConnect($this->db,database::ONERROREXIT);


    $this->tcaseMgr=new testcase($this->db);
    $this->tprojectMgr=new testproject($this->db);
    $this->tsuiteMgr=new testsuite($this->db);

    $this->tplanMgr=new testplan($this->db);
    $this->tplanMetricsMgr=new tlTestPlanMetrics($this->db);
    $this->reqSpecMgr=new requirement_spec_mgr($this->db);
    $this->reqMgr=new requirement_mgr($this->db);
    
    $this->tables = $this->tcaseMgr->getDBTables();
    
    $resultsCfg = config_get('results');
    foreach($resultsCfg['status_label_for_exec_ui'] as $key => $label )
    {
        $this->statusCode[$key]=$resultsCfg['status_code'][$key];  
    }
    
    if( isset($this->statusCode['not_run']) )
    {
      unset($this->statusCode['not_run']);  
    }   
    $this->codeStatus=array_flip($this->statusCode);
  }  


  /**
   *
   */
  function authenticate(\Slim\Route $route)
  {
    $apiKey = null;
    if(is_null($apiKey))
    {  
      $request = $this->app->request();
      $apiKey  = $request->headers('PHP_AUTH_USER');
    } 

    $sql = "SELECT id FROM {$this->tables['users']} " .
           "WHERE script_key='" . $this->db->prepare_string($apiKey) . "'";

    // DEBUG file_put_contents('/tmp/tlRestApi.class.authenticate.txt', $sql);
    $this->userID = $this->db->fetchFirstRowSingleColumn($sql, "id");
    if( ($ok=!is_null($this->userID)) )
    {
      $this->user = tlUser::getByID($this->db,$this->userID);  
    }  
    else
    {
      $this->app->status(400);
      echo json_encode(array('status' => 'ko', 'message' => 'authentication error'));  
      $this->app->stop();
    }  

    return $ok;
  }



  /**
   *
   */
  public function whoAmI()
  {    
    echo json_encode(array('name' => __CLASS__ . ' : Get Route /whoAmI'));
  }
  

  /**
   *
   */
  public function getProjects($id=null)
  {
    $op = array('status' => 'ok', 'message' => 'ok');
    if(is_null($id))
    {
      $opt = array('output' => 'array_of_map', 'order_by' => " ORDER BY name ", 'add_issuetracker' => true,
                   'add_reqmgrsystem' => true);
      $op['item'] = $this->tprojectMgr->get_accessible_for_user($this->userID,$opt);
    }  
    else
    {
      $opt = array('output' => 'map','field_set' => 'id', 'format' => 'simple');
      $zx = $this->tprojectMgr->get_accessible_for_user($this->userID,$opt);
      if( ($safeID = intval($id)) > 0)
      {
        if( isset($zx[$safeID]) )
        {
          $op['item'] = $this->tprojectMgr->get_by_id($safeID);  
        } 
      } 
      else
      {
        // Will consider id = name
        foreach( $zx as $key => $value ) 
        {
          if( strcmp($value['name'],$id) == 0 )
          {
            $safeID = $this->db->prepare_string($id);
            $op['item'] = $this->tprojectMgr->get_by_name($safeID);
            break;   
          }  
        }
      } 
    } 

    // Developer (silly?) information
    // json_encode() transforms maps in objects.
    echo json_encode($op);
  }




// ==============================================
  /**
   * 
   *        $item->name               
   *        $item->prefix
   *        $item->notes
   *        $item->active
   *        $item->public
   *        $item->options
   *        $item->options->requirementsEnabled
   *        $item->options->testPriorityEnabled
   *        $item->options->automationEnabled
   *        $item->options->inventoryEnabled
   */
  public function createTestProject()
  {
    $op = array('status' => 'ko', 'message' => 'ko', 'id' => -1);  
    try 
    {
      $request = $this->app->request();
      $item = json_decode($request->getBody());
      $op['id'] = $this->tprojectMgr->create($item,array('doChecks' => true));
      $op = array('status' => 'ok', 'message' => 'ok');
    } 
    catch (Exception $e) 
    {
      $op['message'] = $e->getMessage();   
    }
    echo json_encode($op);
  }



  /**
   *
   * Request Body
   *
   * $ex->testPlanID
   * $ex->buildID
   * $ex->platformID
   * $ex->testCaseExternalID
   * $ex->notes
   * $ex->statusCode
   *
   *
   * Checks to be done
   * 
   * A. User right & Test plan existence
   * user has right to execute on target Test plan?
   * this means also that: Test plan ID exists ?
   *   
   * B. Build
   * does Build ID exist on target Test plan ?
   * is Build enable to execution ?
   *
   * C. Platform
   * do we need a platform ID in order to execute ?
   * is a platform present on provided data ?
   * does this platform belong to target Test plan ?
   *
   * D. Test case identity
   * is target Test case part of Test plan ?
   *
   *
   * Z. Other mandatory information
   * We are not going to check for other mandatory info
   * like: mandatory custom fields. (if we will be able in future to manage it)
   *
   * 
   */
  public function createTestCaseExecution()
  {
    $op = array('status' => ' ko', 'message' => 'ko', 'id' => -1);  
    try 
    {
      $request = $this->app->request();
      $ex = json_decode($request->getBody());
      $util = $this->checkExecutionEnvironment($ex);

      // If we are here this means we can write execution status!!!
      $ex->testerID = $this->userID;
      foreach($util as $prop => $value)
      {
        $ex->$prop = $value;
      }  
      $op = array('status' => 'ok', 'message' => 'ok');
      $op['id'] = $this->tplanMgr->writeExecution($ex);
    } 
    catch (Exception $e) 
    {
      $op['message'] = $e->getMessage();   
    }
    echo json_encode($op);
  }



  //
  // Support methods
  //
  private function checkExecutionEnvironment($ex)
  {
    // throw new Exception($message, $code, $previous);

    // Test plan ID exists and is ACTIVE    
    $msg = 'invalid Test plan ID';
    $getOpt = array('output' => 'testPlanFields','active' => 1,
                    'testPlanFields' => 'id,testproject_id,is_public');
    $status_ok = !is_null($testPlan=$this->tplanMgr->get_by_id($ex->testPlanID,$getOpt));
    
    if($status_ok)
    {
      // user has right to execute on Test plan ID
      // hasRight(&$db,$roleQuestion,$tprojectID = null,$tplanID = null,$getAccess=false)
      $msg = 'user has no right to execute';
      $status_ok = $this->user->hasRight($this->db,'testplan_execute',
                                         $testPlan['testproject_id'],$ex->testPlanID,true); 
    }  

    if($status_ok)
    {
      // Check if couple (buildID,testPlanID) is valid
      $msg = '(buildID,testPlanID) couple is not valid';
      $getOpt = array('fields' => 'id,active,is_open', 'buildID' => $ex->buildID, 'orderBy' => null);
      $status_ok = !is_null($build = $this->tplanMgr->get_builds($ex->testPlanID,null,null,$getOpt));

      if($status_ok)
      {
        // now check is execution can be done againts this build
        $msg = 'Build is not active and/or closed => execution can not be done';
        $status_ok = $build[$ex->buildID]['active'] && $build[$ex->buildID]['is_open'];
      }  
    }  

    if($status_ok)
    {
      // Get Test plan platforms
      $getOpt = array('outputFormat' => 'mapAccessByID' , 'addIfNull' => false);
      $platformSet = $this->tplanMgr->getPlatforms($ex->testPlanID,$getOpt);

      if( !($hasPlatforms = !is_null($platformSet)) && $ex->platformID !=0)
      {
        $status_ok = false;
        $msg = 'You can not execute against a platform, because Test plan has no platforms';
      }  

      if($status_ok)
      {
        if($hasPlatforms)
        {  
          if($ex->platformID == 0)
          {
            $status_ok = false;
            $msg = 'Test plan has platforms, you need to provide one in order to execute';
          }
          else if (!isset($platformSet[$ex->platformID]))
          {
            $status_ok = false;
            $msg = '(platform,test plan) couple is not valid';
          }  
        }
      }  
    } 

    if($status_ok)
    {
      // Test case check
      $msg = 'Test case does not exist';

      $tcaseID = $this->tcaseMgr->getInternalID($ex->testCaseExternalID);
      $status_ok = ($tcaseID > 0);
      if( $status_ok = ($tcaseID > 0) )
      {
        $msg = 'Test case doesn not belong to right test project';
        $testCaseTestProject = $this->tcaseMgr->getTestProjectFromTestCase($tcaseID,0);
        $status_ok = ($testCaseTestProject == $testPlan['testproject_id']);
      }  
      if($status_ok)
      {
        // Does this test case is linked to test plan ?
        $msg = 'Test case is not linked to (test plan,platform) => can not be executed';
        $getFilters = array('testplan_id' => $ex->testPlanID, 'platform_id' => $ex->platformID);
        $getOpt = array('output' => 'simple');
        $links = $this->tcaseMgr->get_linked_versions($tcaseID,$getFilters,$getOpt);
        $status_ok = !is_null($links);
      }  
    }  
    if($status_ok)
    {
      // status code is OK ?
      $msg = 'not run status is not a valid execution status (can not be written to DB)';
      $status_ok = ($ex->statusCode != 'n');  // Sorry for the MAGIC;

      if($status_ok)
      {
        $msg = 'Requested execution status is not configured on TestLink';
        $status_ok = isset($this->codeStatus[$ex->statusCode]);
      }  
    }  

    if($status_ok)
    {
      $ret = new stdClass();
      $ret->testProjectID = $testPlan['testproject_id'];
      $ret->testCaseVersionID = key($links);
      $ret->testCaseVersionNumber = $links[$ret->testCaseVersionID][$ex->testPlanID][$ex->platformID]['version'];
    }
      
    if(!$status_ok)
    {
      throw new Exception($msg);
    }  
    return $ret;
  }
 


  /**
   * 'name'
   * 'testProjectID'
   * 'notes'
   * 'active'
   * 'is_public'
   *
   */
  public function createTestPlan()
  {
    $op = array('status' => 'ko', 'message' => 'ko', 'id' => -1);  
    try 
    {
      $request = $this->app->request();
      $item = json_decode($request->getBody());
      $op = array('status' => 'ok', 'message' => 'ok');
      $op['id'] = $this->tplanMgr->createFromObject($item,array('doChecks' => true));
    } 
    catch (Exception $e) 
    {
      $op['message'] = $e->getMessage();   
    }
    echo json_encode($op);
  }

  /**
   * 'name'
   * 'testProjectID'
   * 'notes'
   * 'active'
   * 'is_public'
   *
   */
  public function updateTestPlan($id)
  {
    $op = array('status' => 'ko', 'message' => 'ko', 'id' => -1);  
    try 
    {
      $op = array('status' => 'ok', 'message' => 'ok');

      $request = $this->app->request();
      $item = json_decode($request->getBody());
      $item->id = $id;
      $op['id'] = $this->tplanMgr->updateFromObject($item);
    } 
    catch (Exception $e) 
    {
      $op['message'] = $e->getMessage();   
    }
    echo json_encode($op);
  }


  /**
   * 'name'
   * 'testProjectID'
   * 'parentID'
   * 'notes'
   * 'order'
   */
  public function createTestSuite()
  {
    $op = array('status' => 'ko', 'message' => 'ko', 'id' => -1);  
    try 
    {
      $request = $this->app->request();
      $item = json_decode($request->getBody());
      $op = array('status' => 'ok', 'message' => 'ok');
      $op['id'] = $this->tsuiteMgr->createFromObject($item,array('doChecks' => true));
    } 
    catch (Exception $e) 
    {
      $op['message'] = $e->getMessage();   
    }
    echo json_encode($op);
  }

  /**
   * "name"
   * "testSuiteID"
   * "testProjectID"
   * "authorLogin"
   * "authorID"
   * "summary"
   * "preconditions"
   * "importance" - see const.inc.php for domain
   * "executionType"  - see ... for domain
   * "order"
   *
   * "estimatedExecutionDuration"  // to be implemented
   */
  public function createTestCase()
  {
    $op = array('status' => 'ko', 'message' => 'ko', 'id' => -1);  
    try 
    {
      $request = $this->app->request();
      $item = json_decode($request->getBody());
      $op = array('status' => 'ok', 'message' => 'ok');

      // default management
      $this->createTestCaseDefaultManagement($item);

      $this->checkRelatives($item->testProjectID,$item->testSuiteID);

      $ou = $this->tcaseMgr->createFromObject($item);
      if( ($op['id']=$ou['id']) <= 0)
      {
        $op['status'] = 'ko';
        $op['message'] = $ou['msg'];
      }

    } 
    catch (Exception $e) 
    {
      $op['message'] = $e->getMessage();   
    }
    echo json_encode($op);
  }

  /**
   *
   *
   */ 
  private function getUserID($login)
  {


  }

  /**
   *
   *
   */ 
  private function createTestCaseDefaultManagement(&$obj)
  {
    if(property_exists($obj, 'authorLogin'))
    {
      $obj->authorID = $this->getUserID($obj->authorLogin);
      if( $obj->authorID <= 0 )
      {
        // will use user that do the call ?
      }  
    }  

    if(!property_exists($obj, 'steps'))
    {
      $obj->steps = null;
    }
  }


  /**
   *
   *
   */ 
  private function checkRelatives($testProjectID,$testSuiteID)
  {
    if($testProjectID <= 0)
    {
      throw new Exception("Test Project ID is invalid (<=0)");
    }  

    if($testSuiteID <= 0)
    {
      throw new Exception("Test Suite ID is invalid (<=0)");
    }  

    $pinfo = $this->tprojectMgr->get_by_id($testProjectID);
    if( is_null($pinfo) )
    {
      throw new Exception("Test Project ID is invalid (does not exist)");
    }  

    $pinfo = $this->tsuiteMgr->get_by_id($testSuiteID);
    if( is_null($pinfo) )
    {
      throw new Exception("Test Suite ID is invalid (does not exist)");
    }  


    if( $testProjectID != $this->tsuiteMgr->getTestProjectFromTestSuite($testSuiteID,$testSuiteID) )
    {
      throw new Exception("Test Suite does not belong to Test Project ID");
    }  
  }



} // class end