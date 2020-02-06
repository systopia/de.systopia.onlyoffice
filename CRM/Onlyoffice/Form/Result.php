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
 * Page for showing the result of the generator.
 */
class CRM_Onlyoffice_Form_Result extends CRM_Core_Form
{
  public function buildQuickForm()
  {
    parent::buildQuickForm();

    if (!CRM_Onlyoffice_PageManager::openedPageIsCorrect(CRM_Onlyoffice_PageManager::ResultPageName))
    {
      return;
    };

    // TODO: Show a result page with a download button.

    $data = CRM_Onlyoffice_PageManager::getData();

    $templateFileName = CRM_Onlyoffice_OnlyOffice::getSingleton()->getTemplateFileName($data->templateId);

    // Read the file completely:
    $resultFileString = file_get_contents($data->zipArchivePath);

    CRM_Utils_System::download($templateFileName . '.zip', 'application/zip', $resultFileString);

    CRM_Onlyoffice_PageManager::endSession();
  }

}
