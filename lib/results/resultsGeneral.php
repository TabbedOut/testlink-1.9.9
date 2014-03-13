<?php
/** 
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later.
 * 
 * @filesource resultsGeneral.php
 * @author	Martin Havlat <havlat at users.sourceforge.net>
 * 
 * Show Test Results over all Builds.
 *
 * @internal revisions
 * @since 1.9.6
 * 
 */
require('../../config.inc.php');
require_once('common.php');
require_once('displayMgr.php');

$timerOn = microtime(true);
$templateCfg = templateConfiguration();

$args = init_args($db);
$tplan_mgr = new testplan($db);
$gui = initializeGui($db,$args,$tplan_mgr);
$mailCfg = buildMailCfg($gui);
$metricsMgr = new tlTestPlanMetrics($db);
$dummy = $metricsMgr->getStatusTotalsByTopLevelTestSuiteForRender($args->tplan_id);

if(is_null($dummy))
{
	// no test cases -> no report
	$gui->do_report['status_ok'] = 0;
	$gui->do_report['msg'] = lang_get('report_tspec_has_no_tsuites');
	tLog('Overall Metrics page: no test cases defined');
}
else
{
	 // do report
	$gui->statistics->testsuites = $dummy->info;
	$gui->do_report['status_ok'] = 1;
	$gui->do_report['msg'] = '';

	$items2loop = array('testsuites','keywords');
	$keywordsMetrics = $metricsMgr->getStatusTotalsByKeywordForRender($args->tplan_id);
	$gui->statistics->keywords = !is_null($keywordsMetrics) ? $keywordsMetrics->info : null; 
                              
	if( $gui->showPlatforms )
	{
		$items2loop[] = 'platform';
		$platformMetrics = $metricsMgr->getStatusTotalsByPlatformForRender($args->tplan_id);
		$gui->statistics->platform = !is_null($platformMetrics) ? $platformMetrics->info : null; 
	}

	if($gui->testprojectOptions->testPriorityEnabled)
	{
		$items2loop[] = 'priorities';
		$filters = null;
		$opt = array('getOnlyAssigned' => false);
		$priorityMetrics = $metricsMgr->getStatusTotalsByPriorityForRender($args->tplan_id,$filters,$opt);
		$gui->statistics->priorities = !is_null($priorityMetrics) ? $priorityMetrics->info : null; 
	}

	foreach($items2loop as $item)
	{
    if( !is_null($gui->statistics->$item) )
    {
      $gui->columnsDefinition->$item = array();
      
     	// Get labels
     	$dummy = current($gui->statistics->$item);
     	if(isset($dummy['details']))
      {  
        foreach($dummy['details'] as $status_verbose => $value)
       	{
          $dummy['details'][$status_verbose]['qty'] = lang_get($tlCfg->results['status_label'][$status_verbose]);
          $dummy['details'][$status_verbose]['percentage'] = "[%]";
        }
        $gui->columnsDefinition->$item = $dummy['details'];
      }
    }
  } 

 	/* BUILDS REPORT */
	$colDefinition = null;
	$results = null;
	if($gui->do_report['status_ok'])
	{
		$gui->statistics->overallBuildStatus = $metricsMgr->getOverallBuildStatusForRender($args->tplan_id);
		$gui->displayBuildMetrics = !is_null($gui->statistics->overallBuildStatus);
	}  
	
  /* MILESTONE & PRIORITY REPORT */
	$milestonesList = $tplan_mgr->get_milestones($args->tplan_id);
	if (!empty($milestonesList))
	{
		$gui->statistics->milestones = $metricsMgr->getMilestonesMetrics($args->tplan_id,$milestonesList);
  }
} 

$timerOff = microtime(true);
$gui->elapsed_time = round($timerOff - $timerOn,2);
$smarty = new TLSmarty;
$smarty->assign('gui', $gui);
displayReport($templateCfg->template_dir . $templateCfg->default_template, $smarty, $args->format,$mailCfg);



/*
  function: init_args 
  args: none
  returns: array 
*/
function init_args(&$dbHandler)
{
  $iParams = array("apikey" => array(tlInputParameter::STRING_N,32,32),
                   "tproject_id" => array(tlInputParameter::INT_N), 
	                 "tplan_id" => array(tlInputParameter::INT_N),
                   "format" => array(tlInputParameter::INT_N));

	$args = new stdClass();
	$pParams = R_PARAMS($iParams,$args);
  if( !is_null($args->apikey) )
  {
    $cerbero = new stdClass();
    $cerbero->args = new stdClass();
    $cerbero->args->tproject_id = $args->tproject_id;
    $cerbero->args->tplan_id = $args->tplan_id;
    $cerbero->args->getAccessAttr = true;
    $cerbero->method = 'checkRights';
    setUpEnvForRemoteAccess($dbHandler,$args->apikey,$cerbero);  
  }
  else
  {
    testlinkInitPage($dbHandler,true,false,"checkRights");  
	  $args->tproject_id = isset($_SESSION['testprojectID']) ? intval($_SESSION['testprojectID']) : 0;
  }

  if($args->tproject_id <= 0)
  {
  	$msg = __FILE__ . '::' . __FUNCTION__ . " :: Invalid Test Project ID ({$args->tproject_id})";
  	throw new Exception($msg);
  }

  if (is_null($args->format))
	{
		tlog("Parameter 'format' is not defined", 'ERROR');
		exit();
	}
	
	$args->user = $_SESSION['currentUser'];

  return $args;
}


/**
 * 
 *
 */
function buildMailCfg(&$guiObj)
{
	$labels = array('testplan' => lang_get('testplan'), 'testproject' => lang_get('testproject'));
	$cfg = new stdClass();
	$cfg->cc = ''; 
	$cfg->subject = $guiObj->title . ' : ' . $labels['testproject'] . ' : ' . $guiObj->tproject_name . 
	                ' : ' . $labels['testplan'] . ' : ' . $guiObj->tplan_name;
	                 
	return $cfg;
}


function initializeGui(&$dbHandler,$argsObj,&$tplanMgr)
{
  $gui = new stdClass();
  $gui->title = lang_get('title_gen_test_rep');
  $gui->do_report = array();
  $gui->showPlatforms=true;
  $gui->columnsDefinition = new stdClass();
  $gui->columnsDefinition->keywords = null;
  $gui->columnsDefinition->testers = null;
  $gui->columnsDefinition->platform = null;
  $gui->statistics = new stdClass();
  $gui->statistics->keywords = null;
  $gui->statistics->testers = null;
  $gui->statistics->milestones = null;
  $gui->statistics->overalBuildStatus = null;
  $gui->elapsed_time = 0; 
  $gui->displayBuildMetrics = false;
  $gui->buildMetricsFeedback = lang_get('buildMetricsFeedback');

  $mgr = new testproject($dbHandler);
  $dummy = $mgr->get_by_id($argsObj->tproject_id);
  $gui->testprojectOptions = new stdClass();
  $gui->testprojectOptions->testPriorityEnabled = $dummy['opt']->testPriorityEnabled;
  $gui->tproject_name = $dummy['name'];

  $info = $tplanMgr->get_by_id($argsObj->tplan_id);
  $gui->tplan_name = $info['name'];

  $gui->platformSet = $tplanMgr->getPlatforms($argsObj->tplan_id,array('outputFormat' => 'map'));
  if( is_null($gui->platformSet) )
  {
  	$gui->platformSet = array('');
  	$gui->showPlatforms = false;
  }

  return $gui;
}


function checkRights(&$db,&$user,$context = null)
{
  if(is_null($context))
  {
    $context = new stdClass();
    $context->tproject_id = $context->tplan_id = null;
    $context->getAccessAttr = false; 
  }

  $check = $user->hasRight($db,'testplan_metrics',$context->tproject_id,$context->tplan_id,$context->getAccessAttr);
  return $check;
}
?>