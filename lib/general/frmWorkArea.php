<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * @filesource  frmWorkArea.php
 * @author Martin Havlat
 *
 *
**/
require_once('../../config.inc.php');
require_once("common.php");
testlinkInitPage($db);

$args = init_args();

// --------------------------------------------------------------------------------------
// Important Notes for Developers
// --------------------------------------------------------------------------------------
//
// if key found in this map, at User Interface level, screen will be divided 
// vertically in two frames.
// Normally on left will exists a tree menu. 
// On right frame an html named $key.html will be launched.
// Example:
//         if key = printTc, an html page printTc.html must exists on help directory
//
// (aa_tfp -> Associative Array TreeFramePath)
// key  : feature
// value: page to lauch
//
$req_cfg = config_get('req_cfg');

$aa_tfp = array( 
     'editTc' => 'lib/testcases/listTestCases.php?feature=edit_tc',
     'assignReqs' => 'lib/testcases/listTestCases.php?feature=assignReqs',
     'searchTc' => 'lib/testcases/tcSearchForm.php',

     'searchReq' => 'lib/requirements/reqSearchForm.php',
     'searchReqSpec' => 'lib/requirements/reqSpecSearchForm.php',
   
     'printTestSpec' => 'lib/results/printDocOptions.php?type=testspec',
     'printReqSpec' => 'lib/results/printDocOptions.php?type=reqspec',
     'keywordsAssign' => 'lib/testcases/listTestCases.php?feature=keywordsAssign',
     'planAddTC'    => 'lib/plan/planAddTCNavigator.php',
     'planRemoveTC' => 'lib/plan/planTCNavigator.php?feature=removeTC&help_topic=planRemoveTC',
     'planUpdateTC'    => 'lib/plan/planTCNavigator.php?feature=planUpdateTC',
     'show_ve' => 'lib/plan/planTCNavigator.php?feature=show_ve',  
     'newest_tcversions' => '../../lib/plan/newest_tcversions.php',
     'test_urgency' => 'lib/plan/planTCNavigator.php?feature=test_urgency',
     'tc_exec_assignment' => 'lib/plan/planTCNavigator.php?feature=tc_exec_assignment',
     'executeTest' => 'lib/execute/execNavigator.php',
     'showMetrics' => 'lib/results/resultsNavigator.php',
     'reqSpecMgmt' => 'lib/requirements/reqSpecListTree.php'
);

$full_screen = array('newest_tcversions' => 1);

//cleanup session var
$_SESSION['currentSrsId'] = null;

/** feature to display */
$showFeature = $args->feature;
if (isset($aa_tfp[$showFeature]) === FALSE)
{
  // argument is wrong
  tLog("Wrong page argument feature = ".$showFeature, 'ERROR');
  exit();
}

// features that need to run the validate build function
if (in_array($showFeature,array('executeTest','showMetrics','tc_exec_assignment')))
{
  // Check if for test project selected at least a test plan exist (BUGID 623)
  if( isset($_SESSION['testplanID']) )
  {
    $open = ($showFeature == 'executeTest') ? true : null;
      validateBuildAvailability($db,$_SESSION['testplanID'],
          $_SESSION['testplanName'], $_SESSION['testprojectName'], $open);
  }
    else
  {
      redirect('../plan/planView.php');
    exit();
  }   
}

/// 1. get path from global var
/// 2. the URL made easier after setting some rules for help/instruction files
///    naming convention.
/// </enhancement>
$smarty = new TLSmarty();


if(isset($full_screen[$showFeature]))
{
  redirect($aa_tfp[$showFeature]);
}
else
{

  $smarty->assign('treewidth', TL_FRMWORKAREA_LEFT_FRAME_WIDTH);
  $smarty->assign('treeframe', $aa_tfp[$showFeature]);
  $smarty->assign('workframe', 'lib/general/staticPage.php?key='.$showFeature);
  $smarty->display('frmInner.tpl');
}


/** 
 *  validate that some build exists (for Test Plan related features).
 *  If no valid build is found give feedback to user and exit.
 *
 *  @author Martin Havlat
 *  20101013 - asimon - new parameter $open: if execution is wanted, check for open builds
 *  20060809 - franciscom - check if user can create builds,
 *                          then put a link on the message page
 *                          to create link feature
 *
 **/
function validateBuildAvailability(&$db,$tpID, $tpName, $prodName, $open)
{
  $tp = new testplan($db);
  if (!$tp->getNumberOfBuilds($tpID, $open, $open))
  {            
    $message = '<p>'  . lang_get('no_build_warning_part1') . 
            "<b> " . htmlspecialchars($tpName) . "</b>";
    
    $link_to_op = '';
    $hint_text = '';
    if(has_rights($db,"testplan_create_build") == 'yes')
    { 
      // final url will be composed adding to $basehref 
      // (one TL variable available on smarty templates) to $link_to_op
      $link_to_op = "lib/plan/buildEdit.php?do_action=create";
      $hint_text = lang_get('create_a_build');
    }  
      else
      {
        $message .= '</p><p>' . lang_get('no_build_warning_part2') . '</p>';
      }
      
    // show info and exit
    $smarty = new TLSmarty;
    $smarty->assign('content', $message);
    $smarty->assign('link_to_op', $link_to_op);
    $smarty->assign('hint_text', $hint_text);
    $smarty->display('workAreaSimple.tpl');
    exit();
  }
}

function init_args()
{
  $iParams = array("feature" => array(tlInputParameter::STRING_N));
  $args = new stdClass();
  $pParams = G_PARAMS($iParams,$args);
  return $args;
}