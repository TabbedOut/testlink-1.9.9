{*
TestLink Open Source Project - http://testlink.sourceforge.net/

show Test Results by Status

@filesource	resultsByStatus.tpl
@internal revisions
@since 1.9.8
*}

{lang_get var='labels' 
          s='th_test_suite,test_case,version,th_build,th_run_by,th_bugs_not_linked,
          th_date,title_execution_notes,th_bugs,summary,generated_by_TestLink_on,
          th_assigned_to,th_platform,platform,info_failed_tc_report,
          info_blocked_tc_report,info_notrun_tc_report,export_as_spreadsheet'}

{include file="inc_head.tpl" openHead="yes"}
{foreach from=$gui->tableSet key=idx item=matrix name="initializer"}
  {assign var=tableID value=$matrix->tableID}
  {if $smarty.foreach.initializer.first}
    {$matrix->renderCommonGlobals()}
    {if $matrix instanceof tlExtTable}
        {include file="inc_ext_js.tpl" bResetEXTCss=1}
        {include file="inc_ext_table.tpl"}
    {/if}
  {/if}
  {$matrix->renderHeadSection()}
{/foreach}
</head>
<body>
<form name="resultsByStatus" id="resultsByStatus" METHOD="POST"
      action="lib/results/resultsByStatus.php?type={$gui->type}&format=3&tplan_id={$gui->tplan_id}&tproject_id={$gui->tproject_id}">
<h1 class="title">{$gui->title|escape}
  {if $gui->apikey != ''}
  <input type="hidden" name="apikey" id="apikey" value="{$gui->apikey}">
  {/if}
  <input type="image" name="exportSpreadSheet" id="exportSpreadSheet" 
         src="{$tlImages.export_excel}" title="{$labels.export_as_spreadsheet}">
</form>
</h1>



{* <h1 class="title">{$gui->title|escape}</h1> *}

<div class="workBack">
{include file="inc_result_tproject_tplan.tpl"
         arg_tproject_name=$gui->tproject_name arg_tplan_name=$gui->tplan_name}

{if $gui->warning_msg == ''}
	{foreach from=$gui->tableSet key=idx item=matrix}
		{assign var=tableID value="table_$idx"}
   		{$matrix->renderBodySection($tableID)}
	{/foreach}
	<br />
	
	{if $gui->bugs_msg != ''}
	  <h2 class="simple">{$gui->bugs_msg}{$gui->without_bugs_counter}</h2>
	  <br />
	{/if}


  <p class="italic">{$gui->report_context|escape}</p>
	<p class="italic">{$gui->info_msg|escape}</p>
	<br />
	{$labels.generated_by_TestLink_on} {$smarty.now|date_format:$gsmarty_timestamp_format}
{else}
	<br \>
	{$gui->warning_msg}
{/if}
</div>
</body>
</html>