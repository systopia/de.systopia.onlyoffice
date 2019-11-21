<?php
/*-------------------------------------------------------+
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
+-------------------------------------------------------*/

use CRM_Onlyoffice_ExtensionUtil as E;

/**
 * Page for admin users to set general configurations and connect users with Onlyoffice.
 */
class CRM_Onlyoffice_Form_Settings_AdminSettings extends CRM_Core_Form
{
  public function buildQuickForm()
  {
    $this->add(
      'text',
      CRM_Onlyoffice_Configuration::BaseUrlKey,
      E::ts('Onlyoffice URL'),
      ['class' => 'huge'],
      TRUE
    );
    $this->addRule(
      CRM_Onlyoffice_Configuration::BaseUrlKey,
      E::ts('Enter a valid web address beginning with \'http://\' or \'https://\'.'),
      'url'
    );

    $this->add(
      'checkbox',
      CRM_Onlyoffice_Configuration::UsersCanConnectThemselvesKey,
      E::ts('Users are allowed to connect themselves with Onlyoffice.')
    );

    $settings = CRM_Onlyoffice_Configuration::getAdminSettings();
    $this->setDefaults($settings);

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ),
    ));

    parent::buildQuickForm();
  }

  public function postProcess()
  {
    $values = $this->exportValues(
      [
        CRM_Onlyoffice_Configuration::BaseUrlKey,
        CRM_Onlyoffice_Configuration::UsersCanConnectThemselvesKey,
      ],
      true
    );
    CRM_Onlyoffice_Configuration::setAdminSettings($values);
    parent::postProcess();
  }
}
