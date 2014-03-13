<?php
/** 
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * @filesource	topLevelSuitesBarChart.php
 *
 * @author	Kevin Levy
 *
 * @internal revisions
 * @since 1.9.4
 *
 *
 */
require_once('../../config.inc.php');
require_once('common.php');
require_once('charts.inc.php');
testlinkInitPage($db,false,false,"checkRights");

$cfg = new stdClass();
$cfg->scale = new stdClass();

$chart_cfg = config_get('results');
$chart_cfg = $chart_cfg['charts']['dimensions']['topLevelSuitesBarChart'];

$cfg->chartTitle = lang_get($chart_cfg['chartTitle']);
$cfg->XSize = $chart_cfg['XSize'];
$cfg->YSize = $chart_cfg['YSize'];
$cfg->beginX = $chart_cfg['beginX'];
$cfg->beginY = $chart_cfg['beginY'];
$cfg->scale->legendXAngle = $chart_cfg['legendXAngle'];

$args = init_args();
$info = getDataAndScale($db,$args);
if( property_exists($args,'debug') )
{
	new dBug($info);
	die();
}
createChart($info,$cfg);


/*
  function: getDataAndScale

  args :
  
  returns: 

*/
function getDataAndScale(&$dbHandler,$argsObj)
{
    $obj = new stdClass(); 
    $totals = null; 
    $resultsCfg = config_get('results');
	$metricsMgr = new tlTestPlanMetrics($dbHandler);

    $dataSet = $metricsMgr->getRootTestSuites($argsObj->tplan_id,$argsObj->tproject_id);
    $dummy = $metricsMgr->getStatusTotalsByTopLevelTestSuiteForRender($argsObj->tplan_id);
    $obj->canDraw = !is_null($dummy->info);
    
	if( property_exists($argsObj,'debug') )
	{
    	new dBug($dummy->info);
    }
     
    if($obj->canDraw) 
    {    
        //// Process to enable alphabetical order
		$item_descr = array_flip($dataSet);
        ksort($item_descr);
        foreach($item_descr as $name => $tsuite_id)
        {
            if( isset($dummy->info[$tsuite_id]) )
            {
            	$items[]=htmlspecialchars($name);
	            $rmap = $dummy->info[$tsuite_id]['details'];
	        	foreach($rmap as $key => $value)
	        	{
	        		$totals[$key][]=$value['qty'];  
	        	}
        	}
        	else
        	{
        		// make things work, but create log this is not ok
        		tlog(__FILE__ . '::' . __FUNCTION__ . 'Missing item: name/id:' . 
        		     "$name/$tsuite_id", 'DEBUG');
        	}
        }
    }   
    
    $obj->xAxis = new stdClass();
    $obj->xAxis->values = $items;
    $obj->xAxis->serieName = 'Serie8';
    $obj->series_color = null;
    
    foreach($totals as $status => $values)
    {
       $obj->chart_data[] = $values;
       $obj->series_label[] = lang_get($resultsCfg['status_label'][$status]);
 	   if( isset($resultsCfg['charts']['status_colour'][$status]) )
       {	
			$obj->series_color[] = $resultsCfg['charts']['status_colour'][$status];
       }	
    }
 
    return $obj;
}


function init_args()
{
	$argsObj = new stdClass();
	$argsObj->tproject_id = intval($_REQUEST['tproject_id']);
	$argsObj->tplan_id = intval($_REQUEST['tplan_id']);
	if( isset($_REQUEST['debug']) )
	{
		$argsObj->debug = 'yes';
	}
	return $argsObj;
}

function checkRights(&$db,&$user)
{
	return $user->hasRight($db,'testplan_metrics');
}
?>