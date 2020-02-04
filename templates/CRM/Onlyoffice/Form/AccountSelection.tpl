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
    {ts}No connections to Onlyoffice could be found for your account. Please contact a system administator.{/ts}
  {else}
    <div class="label">{$form.connection_radiobox.label}</div>
    <br>
    <div class="content">{$form.connection_radiobox.html}</div>
  {/if}

  {* FOOTER *}
  <br>
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>

{/crmScope}
