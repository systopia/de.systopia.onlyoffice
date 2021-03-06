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
    /** @var CRM_Onlyoffice_Object_TokenContext[] $tokenContexts */
    $tokenContexts = [];

    foreach ($this->_contactIds as $contactId)
    {
      $tokenContext = new CRM_Onlyoffice_Object_TokenContext();
      $tokenContext->contexts = [
        'contactId' => $contactId,
      ];
      $tokenContext->tokens = [];

      $tokenContexts[] = $tokenContext;
    }

    $data = CRM_Onlyoffice_PageManager::getData();

    $data->tokenContexts = $tokenContexts;
    $data->mainContext = 'contactId';

    CRM_Onlyoffice_PageManager::setData($data);
  }
}
