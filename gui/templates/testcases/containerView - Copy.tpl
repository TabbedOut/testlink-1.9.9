{* 
TestLink Open Source Project - http://testlink.sourceforge.net/

View test specification containers

@filesource	containerView.tpl
@internal revisions
@since 1.9.7
*}

{lang_get var='labels' 
          s='th_product_name,edit_testproject_basic_data,th_notes,test_suite,details,none,
             keywords,alt_del_testsuite, alt_edit_testsuite, alt_move_cp_testcases, alt_move_cp_testsuite, 
             btn_new_testsuite, btn_reorder,btn_execute_automatic_testcases,
	           btn_edit_testsuite,btn_del_testsuite,btn_move_cp_testsuite,btn_testcases_table_view,
	           btn_del_testsuites_bulk,btn_delete_testcases,btn_reorder_testcases_alpha,
	           btn_reorder_testcases_externalid,btn_reorder_testsuites_alpha,
	           btn_export_testsuite, btn_export_all_testsuites, btn_import_testsuite, 
	           btn_new_tc,btn_move_cp_testcases, btn_import_tc, btn_export_tc, th_testplan_name,
	           testsuite_operations, testcase_operations,btn_create_from_issue_xml'}

{assign var="container_id" value=$gui->container_data.id}
{assign var="tcImportAction"
        value="lib/testcases/tcImport.php?containerID=$container_id"}
{assign var="importToTProjectAction"  value="$basehref$tcImportAction&amp;bIntoProject=1&amp;useRecursion=1&amp;"}
{assign var="importToTSuiteAction"  value="$basehref$tcImportAction&amp;useRecursion=1"}
{assign var="importTestCasesAction"  value="$basehref$tcImportAction"}
{assign var="tcExportAction"
        value="lib/testcases/tcExport.php?containerID=$container_id"}
{assign var="exportTestCasesAction"  value="$basehref$tcExportAction"}
{assign var="tsuiteExportAction" value="$basehref$tcExportAction&amp;useRecursion=1"}

{assign var="tcMantisXMLAction"
        value="lib/testcases/tcCreateFromIssueMantisXML.php?containerID=$container_id"}
{assign var="createTCFromIssueMantisXMLAction"  value="$basehref$tcMantisXMLAction"}


{include file="inc_head.tpl" openHead="yes"}
{assign var="ext_location" value=$smarty.const.TL_EXTJS_RELATIVE_PATH}
<link rel="stylesheet" type="text/css" href="{$basehref}{$ext_location}/css/ext-all.css" />

{literal}
<script type="text/javascript">
{/literal}
//BUGID 3943: Escape all messages (string)
var alert_box_title = "{$labels.warning|escape:'javascript'}";
var warning_empty_container_name = "{$labels.warning_empty_testsuite_name|escape:'javascript'}";
{literal}
function validateForm(f)
{
  if (isWhitespace(f.container_name.value)) 
  {
      alert_message(alert_box_title,warning_empty_container_name);
      selectField(f, 'container_name');
      return false;
  }
  
  /* Validation of a limited type of custom fields */
  var cf_designTime = document.getElementById('cfields_design_time');
	if (cf_designTime)
 	{
 		var cfields_container = cf_designTime.getElementsByTagName('input');
 		var cfieldsChecks = validateCustomFields(cfields_container);
		if(!cfieldsChecks.status_ok)
	  	{
	    	var warning_msg = cfMessages[cfieldsChecks.msg_id];
	      	alert_message(alert_box_title,warning_msg.replace(/%s/, cfieldsChecks.cfield_label));
	      	return false;
		}

 		cfields_container = cf_designTime.getElementsByTagName('textarea');
 		cfieldsChecks = validateCustomFields(cfields_container);
		if(!cfieldsChecks.status_ok)
	  	{
	    	var warning_msg = cfMessages[cfieldsChecks.msg_id];
	      	alert_message(alert_box_title,warning_msg.replace(/%s/, cfieldsChecks.cfield_label));
	      	return false;
		}
	}
  
  
  
  return true;
}
</script>

{/literal}

{include file="inc_del_onclick.tpl" openHead="yes"}
{if $tlCfg->gui->checkNotSaved}
  <script type="text/javascript">
  var unload_msg = "{$labels.warning_unsaved|escape:'javascript'}";
  var tc_editor = "{$editorType}";
  </script>
  <script src="gui/javascript/checkmodified.js" type="text/javascript"></script>
{/if}
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
 <script src="//code.jquery.com/jquery-1.8.1.js"></script>
<script language="javascript" src="gui/javascript/jquery-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="gui/javascript/jquery-ui.css">
<style>
.ui-resizable{
    width:auto !important;
    margin:0 auto !important;
} 
#ui-id-1{
    color: #15428b;
    font: bold 11px tahoma,arial,verdana,sans-serif;
}
#outputDiv{
    background-color: #ccd8e7;
}
</style>
  <script src="gui/javascript/checkmodified.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
       
        $( "#outputDiv" ).dialog({
            autoOpen:false
        });
        
        $('#new_testsuite_for_popup').click(function(){
            $('#outputDiv').html('');
            var CSRFName = 'CSRFGuard_1542399035';
            var CSRFToken = '9d5a1fc86e4e09ba7c5d1c3c625caacdfae8fe78c2e7ab63ffd4aea8617dc0629840942433d4275bd1ead9d63447f0a7e3317b994521e64ee20e7345e682a453';
            var doAction = '';
            var containerID = '132';
            var new_testsuite = 'Create';
            $.post( "lib/testcases/containerEdit.php", { CSRFName: CSRFName, CSRFToken: CSRFToken, doAction: doAction, containerID: containerID, new_testsuite:new_testsuite  })
            .done(function( data ) {
                //alert( "Data Loaded: " + data );
                $('#outputDiv').html(data);
            });
                                   
            $('#outputDiv').dialog('open');
            });
        
        
        $('#edit_testsuite_popup').click(function(){
            $('#outputDiv').html('');
            var action = 'lib/testcases/containerEdit.php';
            var CSRFName = 'CSRFGuard_1958576732';
            var CSRFToken = '8a915fdb9fa47c71ef6fe538ab8fe4167827ab76583da443462eef88d73ab4a30324dfa8d8d0c94c9fe78b3cfb0ed3bad10a01f2558e6682a4af64cad859f9b2';
            var testsuiteID = '{$gui->container_data.id}';
            var testsuiteName = '{$gui->container_data.name|escape}';
            var edit_testsuite = 'Edit';
            $.post( action, { CSRFName: CSRFName, CSRFToken: CSRFToken, testsuiteID: testsuiteID, testsuiteName: testsuiteName, edit_testsuite:edit_testsuite  })
            .done(function( data ) {
                //alert( "Data Loaded: " + data );
                $('#outputDiv').html(data);
            });
            $( "#outputDiv" ).dialog('open');
        });
        
        $('#create_tc_popup').click(function(){
            $('#outputDiv').html('');
            var action = 'lib/testcases/tcEdit.php';
            var CSRFName = 'CSRFGuard_1034551198';
            var CSRFToken = 'e52924262950daed8a106c7bcd8128765508daa154eead921419e00fdf99b49ea6914df3cc51349b488c0297ec7217396304c521775fcd8ad7dd21bb82e90430';
            var containerID = '{$gui->container_data.id}';
            var create_tc = 'Create';
            $.post( action, { CSRFName: CSRFName, CSRFToken: CSRFToken, containerID: containerID, create_tc: create_tc  })
            .done(function( data ) {
                //alert( "Data Loaded: " + data );
                $('#outputDiv').html(data);
            });
            $( "#outputDiv" ).dialog('open');
        });
        
        
    });
</script>
</head>

<body>

<div id="outputDiv" title="Test Suite"  style='width:300px !important;'>

</div>
<h1 class="title">{$gui->page_title}{$tlCfg->gui_title_separator_1}{$gui->container_data.name|escape}</h1>

<div class="workBack">
{include file="inc_update.tpl" result=$gui->sqlResult item=$gui->level
         name=$gui->moddedItem.name refresh=$gui->refreshTree user_feedback=$gui->user_feedback}

{assign var="bDownloadOnly" value=true}
{assign var="drawReorderButton" value=true}
{assign var="drawReorderButton" value=false}

{if $gui->level == 'testproject'}

	{if $gui->modify_tc_rights == 'yes'}
		{assign var="bDownloadOnly" value=false}

	<fieldset class="groupBtn">
	<h2>{$labels.testsuite_operations}</h2>
        
	<form method="post" action="lib/testcases/containerEdit.php">
            
		<input type="hidden" name="doAction" id="doAction" value="" />
		<input type="hidden" name="containerID" value="{$gui->container_data.id}" />
		
		<input type="submit" name="new_testsuite" value="{$labels.btn_new_testsuite}" />

                <input type="button" id="new_testsuite_for_popup" name="new_testsuite_for_popup" value="{'Create link for test'}" />
                
		<input type="submit" name="reorder_testproject_testsuites_alpha" value="{$labels.btn_reorder_testsuites_alpha}"
				     title="{$labels.btn_reorder_testsuites_alpha}" />

		<input type="button" onclick="location='{$importToTProjectAction}'"
			                       value="{$labels.btn_import_testsuite}" />

    {if $gui->canDoExport}
		<input type="button" onclick="location='{$tsuiteExportAction}'" value="{$labels.btn_export_all_testsuites}" />
    {/if}
	</form>
	</fieldset>
	{/if}

	<table class="simple" >
		<tr>
			<th>{$labels.th_product_name}</th>
		</tr>
		<tr>
			<td>
	    {if $gui->mgt_modify_product == 'yes'}
			  <a href="lib/project/projectView.php"  target="mainframe"
			          title="{$labels.edit_testproject_basic_data}">{$gui->container_data.name|escape}</a>
			{else}
			   {$gui->container_data.name|escape}
			{/if}
			</td>
		</tr>
		<tr>
			<th>{$labels.th_notes}</th>
		</tr>
		<tr>
			<td>{$gui->container_data.notes}</td>
		</tr>

	</table>
	{include file="inc_attachments.tpl" 
	         attach_id=$gui->id attach_tableName="nodes_hierarchy"
	         attach_attachmentInfos=$gui->attachmentInfos
	         attach_downloadOnly=$bDownloadOnly}

{* ----- TEST SUITE ----------------------------------------------------- *}
{elseif $gui->level == 'testsuite'}

	{if $gui->modify_tc_rights == 'yes' || $gui->sqlResult neq ''}
		<fieldset class="groupBtn">

		<h2>{$labels.testsuite_operations}</h2>
		{* Add a new testsuite children for this parent *}
		<span style="float: left; margin-right: 5px;">
		<form method="post" action="lib/testcases/containerEdit.php">
			<input type="hidden" name="containerID" value="{$gui->container_data.id}" />
			<input type="submit" name="new_testsuite" value="{$labels.btn_new_testsuite}" />
                        <input type="button" id="new_testsuite_for_popup" name="new_testsuite_for_popup" value="{'Create link for test'}" />
		</form>
		</span>

		<form method="post" action="lib/testcases/containerEdit.php">
                    
			<input type="hidden" name="testsuiteID" value="{$gui->container_data.id}" />
			<input type="hidden" name="testsuiteName" value="{$gui->container_data.name|escape}" />
			<input type="submit" name="edit_testsuite" value="{$labels.btn_edit_testsuite}"
				     title="{$labels.alt_edit_testsuite}" />
                        <input type="button" name="edit_testsuite_popup" value="{'edit_testsuite_popup'}"
				     title="{'edit_testsuite_popup'}" id="edit_testsuite_popup" />
			<input type="submit" name="move_testsuite_viewer" value="{$labels.btn_move_cp_testsuite}"
				     title="{$labels.alt_move_cp_testsuite}" />
			<input type="submit" name="delete_testsuite" value="{$labels.btn_del_testsuite}"
				     title="{$labels.alt_del_testsuite}" />
		    <input type="submit" name="reorder_testsuites_alpha" value="{$labels.btn_reorder_testsuites_alpha}"
				   title="{$labels.btn_reorder_testsuites_alpha}" />
			
			<input type="submit" name="testcases_table_view" value="{$labels.btn_testcases_table_view}"
			       title="{$labels.btn_testcases_table_view}" />



			<input type="button" onclick="location='{$importToTSuiteAction}'" value="{$labels.btn_import_testsuite}" />
			<input type="button" onclick="location='{$tsuiteExportAction}'" value="{$labels.btn_export_testsuite}" />

		</form>
	    </fieldset>

		{* ----- Work with test cases ----------------------------------------------- *}
		<fieldset class="groupBtn">
		<h2>{$labels.testcase_operations}</h2>
		<form method="post" action="lib/testcases/tcEdit.php">
		  <input type="hidden" name="containerID" value="{$gui->container_data.id}" />
			<input type="submit" accesskey="t" id="create_tc" name="create_tc" value="{$labels.btn_new_tc}" />
                        <input type="button" accesskey="t" id="create_tc_popup" name="create_tc_popup" value="{'creat_tc_popup'}" />
		</form>

		<form method="post" action="lib/testcases/containerEdit.php">
			<input type="hidden" name="testsuiteID" value="{$gui->container_data.id}" />
			<input type="hidden" name="testsuiteName" value="{$gui->container_data.name|escape}" />
	    <input type="submit" name="move_testcases_viewer" value="{$labels.btn_move_cp_testcases}"
         		 title="{$labels.alt_move_cp_testcases}" />
			<input type="submit" name="delete_testcases" value="{$labels.btn_delete_testcases}"
				     title="{$labels.btn_delete_testcases}" />
			<input type="submit" name="reorder_testcases" value="{$gui->btn_reorder_testcases}"
				     title="{$gui->btn_reorder_testcases}" />
		</form>

		<form method="post" action="lib/testcases/tcEdit.php">
			<input type="button" onclick="location='{$importTestCasesAction}'" value="{$labels.btn_import_tc}" />
			<input type="button" onclick="location='{$exportTestCasesAction}'" value="{$labels.btn_export_tc}" />
      <input type="button" onclick="location='{$createTCFromIssueMantisXMLAction}'" value="{$labels.btn_create_from_issue_xml}" />
		</form>

		</fieldset>
	{/if}
	
	{* ----- show Test Suite data --------------------------------------------- *}
	{include file="testcases/inc_testsuite_viewer_ro.tpl"}

	{if $gui->modify_tc_rights eq 'yes'}
		{assign var="bDownloadOnly" value=false}
	{/if}
	{include file="inc_attachments.tpl" 
	         attach_attachmentInfos=$gui->attachmentInfos
	         attach_id=$gui->id attach_tableName="nodes_hierarchy" 
	         attach_downloadOnly=$bDownloadOnly}

{/if} {* test suite *}

</div>
{if $gui->refreshTree}
   	{include file="inc_refreshTreeWithFilters.tpl"}
	{*include file="inc_refreshTree.tpl"*}
{/if}
</body>
</html>