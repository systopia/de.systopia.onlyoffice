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
 * Page for admin users to connect user accounts with Onlyoffice. -> User selection
 */
class CRM_Onlyoffice_Form_Settings_ConnectUser_Selection extends CRM_Core_Form
{
  // TODO: Check that the contact reference is working!

  protected const ContactReferenceElementName = 'user_reference';

  public function buildQuickForm()
  {
    parent::buildQuickForm();

    $this->addEntityRef(
      self::ContactReferenceElementName,
      E::ts('CiviCRM user'),
      [
        'api' => [
          'params' => [
            'contact_type' => 'Individual'
          ]
        ]
      ],
      true
    );

    $this->addButtons(
      [
        [
          'type' => 'submit',
          'name' => E::ts('Select user'),
          'isDefault' => true,
        ],
      ]
    );
  }

  public function postProcess()
  {
    parent::postProcess();

    $values = $this->exportValues([self::ContactReferenceElementName], true);
    $contactId = $values[self::ContactReferenceElementName];
    $connectionUrl = CRM_Utils_System::url(CRM_Onlyoffice_Configuration::ConnectUserConnectionPagePath, 'cid=' . $contactId);
    CRM_Utils_System::redirect($connectionUrl);
  }
}
