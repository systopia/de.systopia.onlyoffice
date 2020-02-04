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
 * Form to select the document used as template.
 */
class CRM_Onlyoffice_Form_TemplateSelection extends CRM_Core_Form
{
  public function buildQuickForm ()
  {
    parent::buildQuickForm();

    if (!CRM_Onlyoffice_PageManager::openedPageIsCorrect(CRM_Onlyoffice_PageManager::TemplateSelectionPageName))
    {
      return;
    };

    $this->add(
      'select',
      'template_file_id',
      E::ts('Template'),
      CRM_Onlyoffice_OnlyOffice::getSingleton()->getTemplates(),
      true
    );

    $this->addButtons(
      [
        [
          'type' => 'back',
          'name' => E::ts('Back'),
          'isDefault' => false,
        ],
        [
          'type' => 'submit',
          'name' => E::ts('Continue'),
          'isDefault' => true,
        ],
      ]
    );
  }

  public function postProcess ()
  {
    parent::postProcess();

    $values = $this->exportValues();

    $templateFileId = $values['template_file_id'];

    CRM_Onlyoffice_PageManager::setData(CRM_Onlyoffice_PageManager::TemplateDataKey, $templateFileId);

    CRM_Onlyoffice_PageManager::openNextPage();
  }
}
