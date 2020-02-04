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
 * Base class for all task entry points to Onlyoffice.
 */
abstract class CRM_Onlyoffice_Form_Task_BaseClass extends CRM_Contact_Form_Task
{
  public function buildQuickForm()
  {
    parent::buildQuickForm();

    CRM_Onlyoffice_PageManager::startSession();

    // TODO: Should we check here if there is a valid and working connection to Onlyoffice and show an error message
    //       if there is none? Otherwise this is only a pass-through page.

    $this->saveData();

    CRM_Onlyoffice_PageManager::openNextPage();
  }

  /**
   * Collect and save all relevant task specific data (token and token context). \
   * Must be implemented by the child class.
   */
  protected abstract function saveData();
}
