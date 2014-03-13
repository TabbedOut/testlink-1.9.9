{*
TestLink Open Source Project - http://testlink.sourceforge.net/

@filesource	execSetResults.tpl
@internal smarty template - show tests to add results
@internal revisions
@since 1.9.9
*}
{$attachment_model=$cfg->exec_cfg->att_model}
{$title_sep=$smarty.const.TITLE_SEP}
{$title_sep_type3=$smarty.const.TITLE_SEP_TYPE3}

{$input_enabled_disabled="disabled"}
{$att_download_only=true}
{$enable_custom_fields=false}
{$draw_submit_button=false}

{$show_current_build=0}
{lang_get s='build' var='build_title'}

{lang_get 
  var='labels'
  s='edit_notes,build_is_closed,test_cases_cannot_be_executed,test_exec_notes,test_exec_result,
	th_testsuite,details,warning_delete_execution,title_test_case,th_test_case_id,
	version,has_no_assignment,assigned_to,execution_history,exec_notes,step_actions,
	execution_type_short_descr,expected_results,testcase_customfields,
  estimated_execution_duration,version,btn_save_and_exit,
	last_execution,exec_any_build,date_time_run,test_exec_by,build,exec_status,
	test_status_not_run,tc_not_tested_yet,last_execution,exec_current_build,
	attachment_mgmt,bug_mgmt,delete,closed_build,alt_notes,alt_attachment_mgmt,
	img_title_bug_mgmt,img_title_delete_execution,test_exec_summary,title_t_r_on_build,
	execution_type_manual,execution_type_auto,run_mode,or_unassigned_test_cases,
	no_data_available,import_xml_results,btn_save_all_tests_results,execution_type,
	testcaseversion,btn_print,execute_and_save_results,warning,warning_nothing_will_be_saved,
	test_exec_steps,test_exec_expected_r,btn_save_tc_exec_results,only_test_cases_assigned_to,
	deleted_user,click_to_open,reqs,requirement,show_tcase_spec,edit_execution, 
	btn_save_exec_and_movetonext,step_number,btn_export,btn_export_testcases,
  bug_link_tl_to_bts,bug_create_into_bts,execution_duration,execution_duration_short,
	preconditions,platform,platform_description,exec_not_run_result_note,remoteExecFeeback'}



{$cfg_section=$smarty.template|basename|replace:".tpl":""}
{config_load file="input_dimensions.conf" section=$cfg_section}

{$exportAction="lib/execute/execExport.php?tplan_id="}

{include file="inc_head.tpl" popup='yes' openHead='yes' jsValidate="yes" editorType=$gui->editorType}
<script language="JavaScript" src="gui/javascript/radio_utils.js" type="text/javascript"></script>
<script language="JavaScript" src="gui/javascript/expandAndCollapseFunctions.js" type="text/javascript"></script>

{if #ROUND_EXEC_HISTORY# || #ROUND_TC_TITLE# || #ROUND_TC_SPEC#}
  {$round_enabled=1}
  <script language="JavaScript" src="{$basehref}gui/niftycube/niftycube.js" type="text/javascript"></script>
{/if}

<script language="JavaScript" type="text/javascript">
var msg="{$labels.warning_delete_execution}";
var import_xml_results="{$labels.import_xml_results}";
</script>

{include file="inc_del_onclick.tpl"}

<script language="JavaScript" type="text/javascript">
{literal}

function load_notes(panel,exec_id)
{
  // solved ONLY for  $webeditorType == 'none'
  var url2load=fRoot+'lib/execute/getExecNotes.php?readonly=1&exec_id=' + exec_id;
  panel.load({url:url2load});
}
{/literal}
</script>

<script language="JavaScript" type="text/javascript">
{literal}
/*
Set value for a group of combo (have same prefix).
*/
function set_combo_group(formid,combo_id_prefix,value_to_assign)
{
	var f=document.getElementById(formid);
	var all_comboboxes = f.getElementsByTagName('select');
	var input_element;
	var idx=0;
		
	for(idx = 0; idx < all_comboboxes.length; idx++)
	{
		input_element=all_comboboxes[idx];
		if( input_element.type == "select-one" && 
		    input_element.id.indexOf(combo_id_prefix)==0 &&
		   !input_element.disabled)
		{
			input_element.value=value_to_assign;
		}	
	}
}
{/literal}
</script>



{literal}
<script type="text/javascript">
{/literal}
// Escape all messages (string)
var alert_box_title="{$labels.warning|escape:'javascript'}";
var warning_nothing_will_be_saved="{$labels.warning_nothing_will_be_saved|escape:'javascript'}";
{literal}
function validateForm(f)
{
  var status_ok=true;
  var cfields_inputs='';
  var cfValidityChecks;
  var cfield_container;
  var access_key;
  cfield_container=document.getElementById('save_button_clicked').value;
  access_key='cfields_exec_time_tcversionid_'+cfield_container; 
    
  if( document.getElementById(access_key) != null )
  {    
 	    cfields_inputs = document.getElementById(access_key).getElementsByTagName('input');
      cfValidityChecks=validateCustomFields(cfields_inputs);
      if( !cfValidityChecks.status_ok )
      {
          var warning_msg=cfMessages[cfValidityChecks.msg_id];
          alert_message(alert_box_title,warning_msg.replace(/%s/, cfValidityChecks.cfield_label));
          return false;
      }
  }
  return true;
}

/*
  function: checkSubmitForStatus
            if a radio (with a particular id, see code for details)
            with $statusCode has been checked, then false is returned to block form submit().
            
            Dev. Note - remember this:
            
            KO:
               onclick="foo();checkSubmitForStatus('n')"
            OK
               onclick="foo();return checkSubmitForStatus('n')"
                              ^^^^^^ 
            

  args :
  
  returns: 

*/
function checkSubmitForStatus($statusCode)
{
  var button_clicked;
  var access_key;
  var isChecked;
  
  button_clicked=document.getElementById('save_button_clicked').value;
  access_key='status_'+button_clicked+'_'+$statusCode; 
 	isChecked = document.getElementById(access_key).checked;
  if(isChecked)
  {
      alert_message(alert_box_title,warning_nothing_will_be_saved);
      return false;
  }
  return true;
}


/**
 * 
 * IMPORTANT DEVELOPMENT NOTICE
 * ATTENTION args is a GLOBAL Javascript variable, then be CAREFULL
 */
function openExportTestCases(windows_title,tsuite_id,tproject_id,tplan_id,build_id,platform_id,tcversion_set) 
{
  wargs = "tsuiteID=" + tsuite_id + "&tprojectID=" + tproject_id + "&tplanID=" + tplan_id; 
  wargs += "&buildID=" + build_id + "&platformID=" + platform_id;
  wargs += "&tcversionSet=" + tcversion_set;
	wref = window.open(fRoot+"lib/execute/execExport.php?"+wargs,
	                   windows_title,"menubar=no,width=650,height=500,toolbar=no,scrollbars=yes");
	wref.focus();
}
</script>
{/literal}





{* Initialize note panels. The array panel_init_functions is filled with init
functions from inc_exec_show_tc_exec.tpl and executed from onReady below *}
<script>
{literal}
panel_init_functions = new Array();
Ext.onReady(function() {
	for(var i=0;i<panel_init_functions.length;i++) {
		panel_init_functions[i]();
	}
});
{/literal}

</script>


</head>
{*
IMPORTANT: if you change value, you need to chang init_args() logic on execSetResults.php
*}
{$tplan_notes_view_memory_id="tpn_view_status"}
{$build_notes_view_memory_id="bn_view_status"}
{$bulk_controls_view_memory_id="bc_view_status"}
{$platform_notes_view_memory_id="platform_notes_view_status"}

<body onLoad="show_hide('tplan_notes','{$tplan_notes_view_memory_id}',{$gui->tpn_view_status});
              show_hide('build_notes','{$build_notes_view_memory_id}',{$gui->bn_view_status});
              show_hide('bulk_controls','{$bulk_controls_view_memory_id}',{$gui->bc_view_status});
              show_hide('platform_notes','{$platform_notes_view_memory_id}',{$gui->platform_notes_view_status});
              multiple_show_hide('{$tsd_div_id_list}','{$tsd_hidden_id_list}',
                                 '{$tsd_val_for_hidden_list}');
              {if $round_enabled}Nifty('div.exec_additional_info');{/if}
              {if #ROUND_TC_SPEC#}Nifty('div.exec_test_spec');{/if}
              {if #ROUND_EXEC_HISTORY#}Nifty('div.exec_history');{/if}
              {if #ROUND_TC_TITLE#}Nifty('div.exec_tc_title');{/if}"
      onUnload="storeWindowSize('TCExecPopup')">

<h1 class="title">
	{$labels.title_t_r_on_build} {$gui->build_name|escape}
	{if $gui->platform_info.name != ""}
	  {$title_sep_type3}{$labels.platform}{$title_sep}{$gui->platform_info.name|escape}
	{/if}
	{include file="inc_help.tpl" helptopic="hlp_executeMain" show_help_icon=true}
</h1>
<h1 class="title">
	{if $gui->ownerDisplayName != ""}
    {$labels.only_test_cases_assigned_to}{$title_sep}
	  {foreach from=$gui->ownerDisplayName item=assignedUser}
	    {$assignedUser|escape}
	  {/foreach}
	  {if $gui->include_unassigned}
	    <br />{$labels.or_unassigned_test_cases}
	  {/if}
	{/if}
</h1>


<div id="main_content" class="workBack">
	{if $gui->user_feedback != ''}
		<div class="error">{$gui->user_feedback}</div>
	{/if}
  {if $gui->build_is_open == 0}
  <div class="messages" style="align:center;">
     {$labels.build_is_closed}<br />
     {$labels.test_cases_cannot_be_executed}
  </div>
  <br />
  {/if}


<form method="post" id="execSetResults" name="execSetResults" 
      onSubmit="javascript:return validateForm(this);">

  <input type="hidden" id="save_button_clicked"  name="save_button_clicked" value="0" />
  <input type="hidden" id="do_delete"  name="do_delete" value="0" />
  <input type="hidden" id="exec_to_delete"  name="exec_to_delete" value="0" />

  {* -------------------------------------------------------------------------------- *}
  {* Test Plan notes show/hide management                                             *}
  {* -------------------------------------------------------------------------------- *}
  {lang_get s='test_plan_notes' var='container_title'}
  {$div_id='tplan_notes'}
  {$memstatus_id=$tplan_notes_view_memory_id}

  {include file="inc_show_hide_mgmt.tpl"
           show_hide_container_title=$container_title
           show_hide_container_id=$div_id
           show_hide_container_draw=false
           show_hide_container_class='exec_additional_info'
           show_hide_container_view_status_id=$memstatus_id}

  <div id="{$div_id}" class="exec_additional_info">
    {$gui->testplan_notes}
    {if $gui->testplan_cfields neq ''} <div id="cfields_testplan" class="custom_field_container">{$gui->testplan_cfields}</div>{/if}
  </div>
  {* -------------------------------------------------------------------------------- *}

  {* -------------------------------------------------------------------------------- *}
  {* Platforms notes show/hide management                                                 *}
  {* -------------------------------------------------------------------------------- *}
  {if $gui->platform_info.id > 0}
  {lang_get s='platform_description' var='container_title'}
  {$div_id='platform_notes'}
  {$memstatus_id=$platform_notes_view_memory_id}

  {include file="inc_show_hide_mgmt.tpl"
           show_hide_container_title=$container_title
           show_hide_container_id=$div_id
           show_hide_container_view_status_id=$memstatus_id
           show_hide_container_draw=true
           show_hide_container_class='exec_additional_info'
           show_hide_container_html=$gui->platform_info.notes}
  {/if}         
  {* -------------------------------------------------------------------------------- *}

  {* -------------------------------------------------------------------------------- *}
  {* Build notes show/hide management                                                 *}
  {* -------------------------------------------------------------------------------- *}
  {lang_get s='builds_notes' var='container_title'}
  {$div_id='build_notes'}
  {$memstatus_id=$build_notes_view_memory_id}

  {include file="inc_show_hide_mgmt.tpl"
           show_hide_container_title=$container_title
           show_hide_container_id=$div_id
           show_hide_container_view_status_id=$memstatus_id
           show_hide_container_draw=false
           show_hide_container_class='exec_additional_info'}

  <div id="{$div_id}" class="exec_additional_info">
    {$gui->build_notes}
    {if $gui->build_cfields != ''} <div id="cfields_build" class="custom_field_container">{$gui->build_cfields}</div>{/if}
  </div>

  {* -------------------------------------------------------------------------------- *}
  {if $gui->map_last_exec eq ""}
     <div class="messages" style="text-align:center"> {$labels.no_data_available}</div>
  {else}
      {if $gui->grants->execute == 1 and $gui->build_is_open == 1}
        {$input_enabled_disabled=""}
        {$att_download_only=false}
        {$enable_custom_fields=true}
        {$draw_submit_button=true}


        {if $cfg->exec_cfg->show_testsuite_contents && $gui->can_use_bulk_op}
            {lang_get s='bulk_tc_status_management' var='container_title'}
            {$div_id='bulk_controls'}
            {$memstatus_id="$bulk_controls_view_memory_id"}
            {include file="inc_show_hide_mgmt.tpl"
                     show_hide_container_title=$container_title
                     show_hide_container_id=$div_id
                     show_hide_container_draw=false
                     show_hide_container_class='exec_additional_info'
                     show_hide_container_view_status_id=$memstatus_id}

            <div id="{$div_id}" name="{$div_id}">
              {include file="execute/inc_exec_controls.tpl"
                       args_save_type='bulk'
                       args_input_enable_mgmt=$input_enabled_disabled
                       args_tcversion_id='bulk'
                       args_webeditor=$gui->bulk_exec_notes_editor
                       args_execution_time_cfields=$gui->execution_time_cfields
                       args_draw_save_and_exit=$gui->draw_save_and_exit
                       args_labels=$labels}
            </div>
        {/if}
    	{/if}


      {if !($cfg->exec_cfg->show_testsuite_contents && $gui->can_use_bulk_op)}
          <hr />
          <div class="groupBtn">
    	    	  <input type="button" name="print" id="print" value="{$labels.btn_print}"
    	    	         onclick="javascript:window.print();" />
    	    	  <input type="submit" id="toggle_history_on_off"
    	    	         name="{$gui->history_status_btn_name}"
    	    	         value="{lang_get s=$gui->history_status_btn_name}" />
    	    	  <input type="button" id="pop_up_import_button" name="import_xml_button"
    	    	         value="{$labels.import_xml_results}"
    	    	         onclick="javascript: openImportResult('import_xml_results',{$gui->tproject_id},
    	    	                                                {$gui->tplan_id},{$gui->build_id},{$gui->platform_id});" />
          
		          {if $tlCfg->exec_cfg->enable_test_automation}
		          <input type="submit" id="execute_cases" name="execute_cases"
		                 value="{$labels.execute_and_save_results}"/>
		          {/if}
    	    	  <input type="hidden" id="history_on"
    	    	         name="history_on" value="{$gui->history_on}" />
          </div>
      {/if}
      <hr />
	{/if}

  {if $cfg->exec_cfg->show_testsuite_contents && $gui->can_use_bulk_op}
      <div>
      <br />
      <input type="button" id="do_export_testcases" name="do_export_testcases"  value="{$labels.btn_export_testcases}"
    	         onclick="javascript: openExportTestCases('export_testcases',{$gui->node_id},{$gui->tproject_id},
    	                                                  {$gui->tplan_id},{$gui->build_id},{$gui->platform_id},
    	                                                  '{$gui->tcversionSet}');" />


      {if $tlCfg->exec_cfg->enable_test_automation}
        <input type="submit" id="execute_cases" name="execute_cases"
                     value="{$labels.execute_and_save_results}"/>
      {/if}

 	    <table class="mainTable-x" width="100%">
 	    <tr>
 	    <th>{$labels.th_testsuite}</th>
      <th>{$labels.title_test_case}</th>
 	    <th>{$labels.exec_status}</th>
      <th>{$labels.test_exec_result}</th>
 	    </tr>
 	    {foreach item=tc_exec from=$gui->map_last_exec name="tcSet"}
      	  {if $tc_exec.active == 1}
            {$tc_id=$tc_exec.testcase_id}
	          {$tcversion_id=$tc_exec.id}
	          {* IMPORTANT:
	             Here we use version_number, which is related to tcversion_id SPECIFICATION.
	             When we need to display executed version number, we use tcversion_number
	          *}
	          {$version_number=$tc_exec.version}
	      
	    	<input type="hidden" id="tc_version_{$tcversion_id}" name="tc_version[{$tcversion_id}]" value='{$tc_id}' />
	    	<input type="hidden" id="version_number_{$tcversion_id}" name="version_number[{$tcversion_id}]" value='{$version_number}' />
      
	        {* ------------------------------------------------------------------------------------ *}
	        <tr bgcolor="{cycle values="#eeeeee,#d0d0d0"}">       
	        <td>{$tsuite_info[$tc_id].tsuite_name}</td>{* <td>&nbsp;</td> *}
	        <td>
	        <a href="javascript:openTCaseWindow({$tc_exec.testcase_id},{$tc_exec.id},'editOnExec')" title="{$labels.show_tcase_spec}">
	        {$gui->tcasePrefix|escape}{$cfg->testcase_cfg->glue_character}{$tc_exec.tc_external_id|escape}::{$labels.version}: {$tc_exec.version}::{$tc_exec.name|escape}
	        </a>
	        </td>
	        <td class="{$tlCfg->results.code_status[$tc_exec.status]}">
	        {$gui->execStatusValues[$tc_exec.status]}
	        </td>
	   			<td>
	   			    {if $tc_exec.can_be_executed}
	   			    <select name="status[{$tcversion_id}]" id="status_{$tcversion_id}">
					    {html_options options=$gui->execStatusValues}
					    </select>
					    {else}
					      &nbsp;
					    {/if}
				  </td>	        </tr>
	    {/if}   {* Design only if test case version we want to execute is ACTIVE *}   
      {/foreach}
      </table>
      </div>
  {else}

	{if $tlCfg->exec_cfg->enable_test_automation && $gui->remoteExecFeedback != ''}
		<b>{$labels.remoteExecFeeback}</b>
		{if	$gui->remoteExecFeedback.system == ''}
			<br>{$gui->remoteExecFeedback.statusVerbose|escape}
			<br>{$gui->remoteExecFeedback.notes|escape}
			{if $gui->remoteExecFeedback.status == ''}	
				<br>{$gui->remoteExecFeedback.scheduled|escape}
				<br>{$gui->remoteExecFeedback.timestamp|escape}
			{/if}	
		{else}
			<br>{$gui->remoteExecFeedback.system.msg|escape}
		{/if}
		<p>
	{/if}
    {include file="execute/inc_exec_show_tc_exec.tpl"}
    {if isset($gui->refreshTree) && $gui->refreshTree}
	    {include file="inc_refreshTreeWithFilters.tpl"}
    {/if}
  {/if}
  
</form>
</div>
</body>
</html>
