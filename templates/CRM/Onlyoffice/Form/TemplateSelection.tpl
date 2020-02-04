{*-------------------------------------------------------+
| SYSTOPIA OnlyOffice Integration                        |
| Copyright (C) 2020 SYSTOPIA                            |
| Author: B. Zschiedrich (zschiedrich@systopia.de)       |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+-------------------------------------------------------*}

{crmScope extensionKey='de.systopia.onlyoffice'}

  {if $showErrorMessage}
    {ts}There are no templates available for this account. Please create a template or select another account.{/ts}
  {else}
    <div class="crm-section">
      <div class="label">{$form.template_file_id.label}</div>
      <div class="content">{$form.template_file_id.html}</div>
      <div class="clear"></div>
    </div>
  {/if}

  {* FOOTER *}
  <br>
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>

{/crmScope}
