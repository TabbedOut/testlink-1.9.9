{* 
TestLink Open Source Project - http://testlink.sourceforge.net/
@filesource attributes.inc.tpl
*}

<div>
<span style="display:none" class="labelHolder">{$labels.status}</span>
<span>
<select style="display:none" name="tc_status" id="tc_status" 
    onchange="content_modified = true">
{html_options options=$gui->domainTCStatus selected=$gui->tc.status}
</select>
<input type="hidden" name="tc_status" id="tc_status" value="6" />
</span>

{if $session['testprojectOptions']->testPriorityEnabled}
  <span class="labelHolder" style="margin-left:20px;">{$labels.importance}</span>
  <span>
  <select name="importance" onchange="content_modified = true">
    {html_options options=$gsmarty_option_importance selected=$gui->tc.importance}
  </select>
  </span>
{/if}


{if $session['testprojectOptions']->automationEnabled}
  <span class="labelHolder" style="margin-left:20px;">{$labels.execution_type}</span>
  <span>
  <select name="exec_type" onchange="content_modified = true">
      {html_options options=$gui->execution_types selected=$gui->tc.execution_type}
  </select>
  </span>
{/if}

<span style="display:none" class="labelHolder" style="margin-left:20px;">{$labels.estimated_execution_duration}</span>
<span>
<input style='display:none' type="text" name="estimated_execution_duration" id="estimated_execution_duration"
     size="{#EXEC_DURATION_SIZE#}" maxlength="{#EXEC_DURATION_MAXLEN#}"
     title="{$labels.estimated_execution_duration}" 
     value={$gui->tc.estimated_exec_duration}>
</span>
</div>
