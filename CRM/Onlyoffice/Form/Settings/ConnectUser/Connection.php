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
 * Page for admin users to connect user accounts with Onlyoffice. -> Connection
 */
class CRM_Onlyoffice_Form_Settings_ConnectUser_Connection extends CRM_Core_Form
{
  // TODO: We must make this settings page accessible.
  //       If we put the link to this page it under Administration it will be the only link there for non-admins.
  //       We will have to check how page URLs and user permissions work.

  protected const NumberOfConnectionElements = 10;
  protected const HiddenPasswordPlaceholder = '**********';

  protected const NumberOfConnectionElementsVariableName = 'numberOfConnectionElements';
  protected const ShowConnectionElementsVariableName = 'showConnectionElements';

  protected const ContactIdElementName = 'contact_id';
  protected const UserNameElementName = 'user_name';
  protected const UserPasswordElementName = 'user_password';

  protected function getContactIdIfAllowed ()
  {
    $contactId = null;

    $values = $this->exportValues([self::ContactIdElementName], true);

    if (array_key_exists(self::ContactIdElementName, $values) && !empty($values[self::ContactIdElementName]))
    {
      $contactId = $values[self::ContactIdElementName];
    }
    else if (array_key_exists('cid', $_REQUEST))
    {
      $contactId = $_REQUEST['cid'];
    }

    if (!CRM_Core_Permission::check('admin')) // TODO: Should we define an "Onlyoffice admin" role? What about an "Onlyoffice user" role?
    {
      // If there is a contact ID set but we are no admin, we are not allowed to do that.
      // In this case, ignore the contact ID and use the current user insetad.
      $contactId = null;
    }

    return $contactId;
  }

  protected function buildConnectElements (&$defaults)
  {
    $contactId = $this->getContactIdIfAllowed();
    $connections = CRM_Onlyoffice_Configuration::getUserSetting(CRM_Onlyoffice_Configuration::UserConnectionsKey, $contactId);
    if (!$connections)
    {
      $connections = [];
    }

    // For the element creation it is easier to have a numeric list of name and password pairs instead of the map structure:
    $connectionList = [];
    foreach ($connections as $connectionName => $connectionPassword)
    {
      if (empty($connectionName) || empty($connectionPassword))
      {
        continue;
      }

      $connectionList[] = [
        'name' => $connectionName,
        'password' => $connectionPassword,
      ];
    }

    for ($i = 0; $i < self::NumberOfConnectionElements; $i++)
    {
      $userNameKey = self::UserNameElementName . '_' . $i;

      $this->add(
        'text',
        $userNameKey,
        E::ts('Onlyoffice user name'),
        ['class' => 'huge'],
        false
      );

      $userPasswordKey = self::UserPasswordElementName . '_' . $i;

      $this->add(
        'password',
        $userPasswordKey,
        E::ts('Onlyoffice user password'),
        ['class' => 'huge'],
        false
      );

      if (count($connectionList) > $i)
      {
        $defaults[$userNameKey] = $connectionList[$i]['name'];

        // Passwords shall not be shown to any user or admin:
        $defaults[$userPasswordKey] = self::HiddenPasswordPlaceholder;
      }
    }

    $this->assign(self::NumberOfConnectionElementsVariableName, self::NumberOfConnectionElements);
  }

  public function buildQuickForm ()
  {
    parent::buildQuickForm();

    $usersCanConnectThemselves = CRM_Onlyoffice_Configuration::getAdminSetting(CRM_Onlyoffice_Configuration::UsersCanConnectThemselvesKey);
    $userIsAdmin = CRM_Core_Permission::check('admin');

    $showConnectionElements = $usersCanConnectThemselves || $userIsAdmin;

    $this->assign(self::ShowConnectionElementsVariableName, $showConnectionElements);

    $defaults = [];

    // Hidden element for the contact ID because if given as GET parameter it will be lost in process():
    $this->add(
      'hidden',
      self::ContactIdElementName
    );

    $contactId = $this->getContactIdIfAllowed();
    $defaults[self::ContactIdElementName] = $contactId;

    if ($showConnectionElements)
    {
      $this->buildConnectElements($defaults);

      $this->addButtons(
        [
          [
            'type' => 'submit',
            'name' => E::ts('Save'),
            'isDefault' => true,
          ],
        ]
      );
    }
    else
    {
      $this->addButtons(
        [
          [
            'type' => 'back',
            'name' => E::ts('OK'),
            'isDefault' => true,
          ],
        ]
      );
    }

    $this->setDefaults($defaults);
  }

  public function postProcess ()
  {
    parent::postProcess();

    $values = $this->exportValues(null, true);

    $contactId = $this->getContactIdIfAllowed();
    $oldConnections = CRM_Onlyoffice_Configuration::getUserSetting(CRM_Onlyoffice_Configuration::UserConnectionsKey, $contactId);

    $connections = [];
    for ($i = 0; $i < self::NumberOfConnectionElements; $i++)
    {
      $nameElementName = self::UserNameElementName . '_' . $i;
      $passwordElementName = self::UserPasswordElementName . '_' . $i;

      if (array_key_exists($nameElementName, $values) && array_key_exists($passwordElementName, $values))
      {
        $name = $values[$nameElementName];
        $password = $values[$passwordElementName];

        // If the password is a placeholder, replace it with the old password for this connection:
        if ($password == self::HiddenPasswordPlaceholder)
        {
          if (array_key_exists($name, $oldConnections))
          {
            $password = $oldConnections[$name];
          }
          else
          {
            // If there was no connection with this name and the password is a placeholder something
            // went wrong with the user as cause. In this case, do not save the connection.
            continue;
          }
        }

        $connections[$name] = $password;
      }
    }

    CRM_Onlyoffice_Configuration::setUserSetting(CRM_Onlyoffice_Configuration::UserConnectionsKey, $connections, $contactId);

    // Redirect admins back to the user selection page if they changed the connections of another user:
    if (CRM_Core_Permission::check('admin') && $contactId)
    {
      $selectionUrl = CRM_Utils_System::url(CRM_Onlyoffice_Configuration::ConnectUserSelectionPagePath);
      CRM_Utils_System::redirect($selectionUrl);
    }
  }
}
