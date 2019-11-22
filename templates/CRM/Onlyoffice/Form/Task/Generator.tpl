{*-------------------------------------------------------+
| SYSTOPIA OnlyOffice Integration                        |
| Copyright (C) 2019 SYSTOPIA                            |
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

<div id="help">
  {ts domain="de.systopia.onlyoffice"}You can select any of the documents on your OnlyOffice instance as a template.{/ts}
  {ts domain="de.systopia.onlyoffice" 1=$oolink 2=$oouser 3=$oopass}You'll find our OnlyOffice instance here:
    <ul>
      <li>URL: <a target="_blank" href="%1">%1</a></li>
      <li>User: <code>%2</code></li>
      <li>Password: <code>%3</code></li>
    </ul>
    {/ts}
</div>

<div class="crm-section">
  <div class="label">{$form.template_file_id.label}</div>
  <div class="content">{$form.template_file_id.html}</div>
  <div class="clear"></div>
</div>


{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
