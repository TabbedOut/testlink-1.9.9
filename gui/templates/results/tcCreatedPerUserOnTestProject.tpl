{* 
TestLink Open Source Project - http://testlink.sourceforge.net/

@filesource tcCreatedPerUserOnTestProject.tpl
@since      1.9.6

Purpose: smarty template - Report of Test Cases created per tester
*}

{include file="inc_head.tpl" openHead='yes'}
{include file="inc_ext_js.tpl" bResetEXTCss=1}
                          
                          
{foreach from=$gui->tableSet key=idx item=matrix name="initializer"}
  {assign var="tableID" value="table_$idx"}
  {if $smarty.foreach.initializer.first}
    {$matrix->renderCommonGlobals()}
    {include file="inc_ext_js.tpl" bResetEXTCss=1}
    {include file="inc_ext_table.tpl"}
  {/if}
  {$matrix->renderHeadSection($tableID)}
{/foreach}
</head>

<body>
  <h1 class="title">{$gui->pageTitle}</h1>
  <div class="workBack">
  {if $gui->warning_msg == ''}
    {if $gui->resultSet}
      {foreach from=$gui->tableSet key=idx item=matrix}
        <p>
        {assign var="tableID" value="table_$idx"}
        {$matrix->renderBodySection($tableID)}
        <br />
        </p>
      {/foreach}
      <br />
      {$gui->l18n.generated_by_TestLink_on} {$smarty.now|date_format:$gsmarty_timestamp_format}
    {else}
      {$gui->l18n.no_records_found}
    {/if}
  {else}
    <div class="user_feedback">
    {$gui->warning_msg}
    </div>
  {/if}   
  </div>
</body>
</html>