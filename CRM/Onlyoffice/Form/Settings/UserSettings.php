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
class CRM_Onlyoffice_Form_Settings_UserSettings extends CRM_Core_Form
{
  // TODO: We must make this settings page accessible.
  //       If we put the link to this page it under Administration it will be the only link there for non-admins.
  //       We will have to check how page URLs and user permissions work.

  protected const UsersCanConnectThemselvesSmartyVariableName = 'usersCanConnectThemselves';

  public function buildQuickForm()
  {
    $usersCanConnectThemselves = CRM_Onlyoffice_Configuration::getAdminSetting(CRM_Onlyoffice_Configuration::UsersCanConnectThemselvesKey);

    $this->assign(self::UsersCanConnectThemselvesSmartyVariableName, $usersCanConnectThemselves);

    if ($usersCanConnectThemselves)
    {
      $this->add(
        'text',
        CRM_Onlyoffice_Configuration::UserNameKey,
        E::ts('Onlyoffice user name'),
        ['class' => 'huge'],
        TRUE
      );

      $this->add(
        'password',
        CRM_Onlyoffice_Configuration::UserPasswordKey,
        E::ts('Onlyoffice user password'),
        ['class' => 'huge'],
        TRUE
      );

      $settings = CRM_Onlyoffice_Configuration::getUserSetting(CRM_Onlyoffice_Configuration::UserNameKey);
      $this->setDefaults($settings);

      $this->addButtons(array(
        array(
          'type' => 'submit',
          'name' => E::ts('Save'),
          'isDefault' => TRUE,
        ),
      ));
    }

    parent::buildQuickForm();
  }

  public function postProcess()
  {
    if (CRM_Onlyoffice_Configuration::getAdminSetting(CRM_Onlyoffice_Configuration::UsersCanConnectThemselvesKey))
    {
      $values = $this->exportValues(
        [
          CRM_Onlyoffice_Configuration::UserNameKey,
          CRM_Onlyoffice_Configuration::UserPasswordKey,
        ],
        true
      );

      CRM_Onlyoffice_Configuration::setUserSettings($values);
    }

    parent::postProcess();
  }
}
