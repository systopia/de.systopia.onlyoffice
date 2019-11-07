{*-------------------------------------------------------+
| SYSTOPIA OnlyOffice Integration                        |
| Copyright (C) 2018 SYSTOPIA                            |
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

{if $usersCanConnectThemselves}
  <div class="crm-section">
    <div class="label">{$form.user_name.label}</div>
    <div class="content">{$form.user_name.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.user_password.label}</div>
    <div class="content">{$form.user_password.html}</div>
    <div class="clear"></div>
  </div>
{else}
  <div>
    {ts}The administrator disabled the ability for users to connect their accounts with Onlyoffice. Please contact your administrator.{/ts}
    <br><br>
  </div>
{/if}


{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
