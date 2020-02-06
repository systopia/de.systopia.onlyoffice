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
 * Form to select the account that shall be used in the following process.
 */
class CRM_Onlyoffice_Form_AccountSelection extends CRM_Core_Form
{
  private const ConnectionRadioboxElementId = 'connection_radiobox';

  public function buildQuickForm()
  {
    parent::buildQuickForm();

    if (!CRM_Onlyoffice_PageManager::openedPageIsCorrect(CRM_Onlyoffice_PageManager::AccountSelectionPageName))
    {
      return;
    };

    $connections = CRM_Onlyoffice_Configuration::getUserSetting(CRM_Onlyoffice_Configuration::UserConnectionsKey);
    $connectionCount = count($connections);

    if ($connectionCount == 0)
    {
      $this->assign('showErrorMessage', true);
    }
    else if ($connectionCount == 1)
    {
      $userName = array_key_first($connections);
      $userPassword = $connections[$userName];

      $this->saveAccountAndContinue($userName, $userPassword);

      return;
    }
    else
    {
      $values = [];

      foreach (array_keys($connections) as $userName)
      {
        $values[$userName] = $userName;
      }

      $this->addRadio(
        self::ConnectionRadioboxElementId,
        E::ts('Select the account that shall be used in the PDF generation:'),
        $values,
        [],
        '<br><br>',
        true
      );
    }

    $this->addButtons(
      [
        [
          'type' => 'submit',
          'name' => E::ts('Continue'),
          'isDefault' => true,
        ],
      ]
    );
  }

  public function postProcess()
  {
    parent::postProcess();

    $values = $this->exportValues();

    $connections = CRM_Onlyoffice_Configuration::getUserSetting(CRM_Onlyoffice_Configuration::UserConnectionsKey);

    $userName = $values[self::ConnectionRadioboxElementId];
    $userPassword = $connections[$userName];

    $this->saveAccountAndContinue($userName, $userPassword);
  }

  private function saveAccountAndContinue(string $userName, string $userPassword): void
  {
    $account = new CRM_Onlyoffice_Object_Account();
    $account->name = $userName;
    $account->password = $userPassword;

    $data = CRM_Onlyoffice_PageManager::getData();
    $data->account = $account;

    CRM_Onlyoffice_PageManager::setData($data);

    CRM_Onlyoffice_PageManager::openNextPage();
  }
}
