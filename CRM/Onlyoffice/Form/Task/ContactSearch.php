<?php
/*-------------------------------------------------------+
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
+-------------------------------------------------------*/

use CRM_Onlyoffice_ExtensionUtil as E;

/**
 * Task entry point to Onlyoffice for contact searches.
 */
class CRM_Onlyoffice_Form_Task_ContactSearch extends CRM_Onlyoffice_Form_Task_BaseClass
{
  /**
   * Collect and save all relevant task specific data (token and token context). \
   * Must be implemented by the child class.
   */
  protected function saveData()
  {
    $tokenContext = [
      'contactId' => $this->_contactIds
    ];
    $tokens = [];

    CRM_Onlyoffice_PageManager::setData(CRM_Onlyoffice_PageManager::TokenContextDataKey, $tokenContext);
    CRM_Onlyoffice_PageManager::setData(CRM_Onlyoffice_PageManager::TokensDataKey, $tokens);
  }
}
