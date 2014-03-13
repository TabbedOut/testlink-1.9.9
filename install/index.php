<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Navigation for installation scripts
 *
 * @package     TestLink
 * @copyright   2007,2013 TestLink community
 * @filesource  index.php
 *
 * @internal revisions
 */

if(!isset($tlCfg))
{
  $tlCfg = new stdClass();  
} 
require_once("../cfg/const.inc.php");

session_start();
$_SESSION['session_test'] = 1;
$_SESSION['testlink_version'] = TL_VERSION;

$prev_ver = '1.9.3/4/5/6/7/8';
$forum_url = 'forum.testlink.org';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>Testlink <?php echo $_SESSION['testlink_version'] ?> Installation procedure</title>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <link href="../gui/themes/default/images/favicon.ico" rel="icon" type="image/gif"/>
  <style type="text/css">@import url('./css/style.css');</style>
</head>

<body>
<div class="tlPager">
<h1><img src="./img/dot.gif" alt="Dot" style="margin: 0px 10px;" />
    TestLink <?php echo $_SESSION['testlink_version'] ?> Installation</h1>
<div class="tlLiner">&nbsp;</div>
<div class="tlStory">
    <p>You are installing TestLink <?php echo $_SESSION['testlink_version'] ?> </p>
    <p><b>Migration from <?php echo $prev_ver ?>  to  <?php echo $_SESSION['testlink_version'] ?> require Database changes that has to be done MANUALLY.
          Please read README file provided with installation.</b></p> 
    <p><b>For information about Migration from older version please read README file provided with installation.</b></p> 
    <p><b>Please read Section on README file or go to <?php echo 'http://' .$forum_url ?> (Forum: TestLink 1.9.4 and greater News,changes, etc)</b> </p>
    <p>Open <a target="_blank" href="../docs/testlink_installation_manual.pdf">Installation manual</a>
    for more information or troubleshooting. You could also look at
    <a href="../README">README</a> or <a href="../CHANGELOG">Changes Log</a>.
    You are welcome to visit our <a target="_blank" href="http://forum.testlink.org">
    forum</a> to browse or discuss.
    </p>
    <p><ul>
    <li><a href="installIntro.php?type=new">New installation</a></li>
    </ul></p>
</div>
<div class="tlLiner">&nbsp;</div>

</div>
</body>
</html>