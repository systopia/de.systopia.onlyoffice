<?php
/*-------------------------------------------------------+
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
+-------------------------------------------------------*/

use CRM_Onlyoffice_ExtensionUtil as E;

/**
 * Page for normal users to connect their account with Onlyoffice.
 */
class CRM_Onlyoffice_Form_Settings_UserSettings extends CRM_Core_Form {

  public function buildQuickForm() {

    $this->add(
      'text',
      'user_name',
      E::ts("OnlyOffice user name"),
      ['class' => 'huge'],
      TRUE
    );

    $this->add(
      'password',
      'user_password',
      E::ts("OnlyOffice user password"),
      ['class' => 'huge'],
      TRUE
    );

    $settings = CRM_Onlyoffice_Configuration::getUserSetting('user_name');
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

  public function postProcess() {
    $values = $this->exportValues(
      [
        'user_name',
        'user_password',
      ],
      true
    );
    CRM_Onlyoffice_Configuration::setUserSettings($values);
    parent::postProcess();
  }

}
