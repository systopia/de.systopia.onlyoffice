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
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Onlyoffice_Form_Settings extends CRM_Core_Form {

  public function buildQuickForm() {

    // add form elements
    $this->add(
      'text',
      'base_url',
      E::ts("OnlyOffice URL"),
      ['class' => 'huge'],
      TRUE
    );
    $this->addRule('base_url', E::ts('Enter a valid web address beginning with \'http://\' or \'https://\'.'), 'url');

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

    $settings = CRM_Onlyoffice_Configuration::getSettings();
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
    $values = $this->exportValues();
    CRM_Onlyoffice_Configuration::setSettings($values);
    parent::postProcess();
  }

}
