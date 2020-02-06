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
 * Page for starting the runner.
 */
class CRM_Onlyoffice_Form_Runner extends CRM_Core_Form
{
  public function buildQuickForm()
  {
    parent::buildQuickForm();

    if (!CRM_Onlyoffice_PageManager::openedPageIsCorrect(CRM_Onlyoffice_PageManager::RunnerPageName))
    {
      return;
    };

    $data = CRM_Onlyoffice_PageManager::getData();

    $currentContactId = CRM_Core_Session::singleton()->getLoggedInContactID();

    CRM_Onlyoffice_Queue_Generator_Launcher::prepare($data);

    CRM_Onlyoffice_PageManager::setData($data);

    CRM_Onlyoffice_PageManager::openNextPage(false);

    $targetPageUrl = CRM_Onlyoffice_PageManager::getUrlToCurrentPage();

    CRM_Onlyoffice_Queue_Generator_Launcher::launchRunner($data, $currentContactId, $targetPageUrl);
  }

}
