<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 *
 * @filesource  jiradbInterface.class.php
 * @since 1.9.6
 *
 * @internal revision
 * @since 1.9.8
 * 20130805 - franciscom - canCreateViaAPI()
**/
class jiradbInterface extends issueTrackerInterface
{
  var $defaultResolvedStatus;
  var $dbSchema;
  var $support;


  /**
   * Construct and connect to BTS.
   *
   * @param str $type (see tlIssueTracker.class.php $systems property)
   * @param xml $cfg
   **/
  function __construct($type,$config)
  {
    // connect() to DATABASE is done here
    parent::__construct($type,$config);  

    $this->methodOpt['buildViewBugLink'] = array('addSummary' => true, 'colorByStatus' => true);
    $this->interfaceViaDB = true;

    $this->support = new jiraCommons();
    $this->support->guiCfg = array('use_decoration' => true);

    $this->dbSchema = new stdClass();
    $this->dbSchema->issues = 'jiraissue';
    $this->dbSchema->status = 'issuestatus';
        

    $this->getStatuses();
    if( property_exists($this->cfg, 'statuscfg') )
    {
      $this->setStatusCfg();
    }

    $this->defaultResolvedStatus = $this->support->initDefaultResolvedStatus($this->statusDomain);
    $this->setResolvedStatusCfg();
    
  }

  
  /**
   *
   **/
  function getIssue($issueID)
  {
    $debugMsg = 'Class:' . __CLASS__ . ' - Method: ' . __FUNCTION__;
    if (!$this->isConnected())
    {
      return false;
    }
    
    // ATTENTION:
    // Field names on Jira tables seems to be sometimes on CAPITALS
    $sql = "/* $debugMsg */ " .
           " SELECT ISSUES.ID AS id,ISSUES.pkey, ISSUES.summary, ISSUES.issuestatus AS status_code, " .
           " ST.pname AS status_verbose " .
           " FROM {$this->dbSchema->issues} ISSUES " .
           " JOIN {$this->dbSchema->status} ST ON ST.ID = ISSUES.issuestatus" .
           " WHERE ISSUES.pkey='{$this->dbConnection->prepare_string($issueID)}'";
    try
    {
      $rs = $this->dbConnection->fetchRowsIntoMap($sql,'id');
    }
    catch (Exception $e)
    {
      $rs = null;
      $msg = "JIRA DB - Ticket ID $issueID - " . $e->getMessage();
      tLog($msg, 'WARNING');
    }
    
    $issue = null;
    if( !is_null($rs) ) 
    {
      $issueOnDB = current($rs);
      $issue = new stdClass();
      $issue->IDHTMLString = "<b>{$issueID} : </b>";

      $issue->summary = $issueOnDB['summary'];
      $issue->statusCode = $issueOnDB['status_code']; 
      $issue->statusVerbose = $issueOnDB['status_verbose']; 

      $issue->statusHTMLString = $this->support->buildStatusHTMLString($issue->statusVerbose);
      $issue->summaryHTMLString = $this->support->buildSummaryHTMLString($issue);

      $issue->isResolved = isset($this->resolvedStatus->byCode[$issue->statusCode]); 
    }
    return $issue;  
  }

  function getMyInterface()
  {
    return $this->cfg->interfacePHP;
  }

  function getStatuses()
  {
    $debugMsg = 'Class:' . __CLASS__ . ' - Method: ' . __FUNCTION__;
    if (!$this->isConnected())
    {
      return false;
    }
    
    // ATTENTION:
    // Field names on Jira tables seems to be sometimes on CAPITALS
    $sql = "/* $debugMsg */ " .
           " SELECT ST.ID AS id,ST.pname AS name FROM {$this->dbSchema->status} ST";
    try
    {
      $rs = $this->dbConnection->fetchRowsIntoMap($sql,'id');
      foreach ($rs as $id => $elem)
      {
        $this->statusDomain[$elem['name']]=$id;
      }
    }
    catch (Exception $e)
    {
      tLog("JIRA DB " . __METHOD__  . $e->getMessage(), 'WARNING');
    }
  }

  /**
   *
   * @author francisco.mancardi@gmail.com>
   **/
  public function getStatusDomain()
  {
    return $this->statusDomain;
  }

  /**
   * checks id for validity
   *
   * @param string issueID
   *
   * @return bool returns true if the bugid has the right format, false else
   **/
  function checkBugIDSyntax($issueID)
  {
    return $this->checkBugIDSyntaxString($issueID);
  }


  public static function getCfgTemplate()
  {
    
    $template = "<!-- Template " . __CLASS__ . " -->\n" .
                "<issuetracker>\n" .
                "<dbhost>DATABASE SERVER NAME</dbhost>\n" .
                "<dbname>DATABASE NAME</dbname>\n" .
                "<dbtype>mysql</dbtype>\n" .
                "<dbuser>USER</dbuser>\n" .
                "<dbpassword>PASSWORD</dbpassword>\n" .
                "<uriview>http://localhost:8080/development/mantisbt-1.2.5/view.php?id=</uriview>\n" .
                "<uricreate>http://localhost:8080/development/mantisbt-1.2.5/</uricreate>\n" .
                "<!-- Configure This if you want NON STANDARD BEHAIVOUR for considered issue resolved -->\n" .
                "<resolvedstatus>\n" .
                "<status><code>80</code><verbose>resolved</verbose></status>\n" .
                "<status><code>90</code><verbose>closed</verbose></status>\n" .
                "</resolvedstatus>\n" .
                "</issuetracker>\n";
    return $template;
  }

 /**
  *
  **/
  function canCreateViaAPI()
  {
    return false;
  }
}
