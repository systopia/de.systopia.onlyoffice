<?php
/*-------------------------------------------------------+
| SYSTOPIA OnlyOffice Integration                        |
| Copyright (C) 2019-2020 SYSTOPIA                       |
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
class CRM_Onlyoffice_Configuration
{
  // Internal setting groups keys:
  private const AdminSettingsKey = 'onlyoffice_admin_settings';
  private const UserSettingsKey = 'onlyoffice_user_settings';
  // Global settings keys:
  public const BaseUrlKey = 'base_url';
  public const UsersCanConnectThemselvesKey = 'user_can_connect_themselves';
  public const UserConnectionsKey = 'user_connections';
  // Global element constants:
  // Global Page/Form paths:
  public const ConnectUserSelectionPagePath = 'civicrm/onlyoffice/settings/connectuser/selection';
  public const ConnectUserConnectionPagePath = 'civicrm/onlyoffice/settings/connectuser/connection';

  public static function getAdminSetting(string $name)
  {
    $settings = self::getAdminSettings();
    return CRM_Utils_Array::value($name, $settings, NULL);
  }

  public static function getAdminSettings(): array
  {
    $settings = Civi::settings()->get(self::AdminSettingsKey);
    if ($settings && is_array($settings))
    {
      return $settings;
    }
    else
    {
      return [];
    }
  }

  public static function setAdminSetting(string $name, $value): void
  {
    $settings = self::getAdminSettings();
    $settings[$name] = $value;
    self::setAdminSettings($settings);
  }

  public static function setAdminSettings(array $settings): void
  {
    Civi::settings()->set(self::AdminSettingsKey, $settings);
  }

  public static function getUserSetting(string $name, ?int $contactID = null)
  {
    $settings = self::getUserSettings($contactID);
    return CRM_Utils_Array::value($name, $settings, null);
  }

  public static function getUserSettings(?int $contactID = null): array
  {
    $settings = Civi::contactSettings($contactID)->get(self::UserSettingsKey);
    if ($settings && is_array($settings))
    {
      return $settings;
    }
    else
    {
      return [];
    }
  }

  public static function setUserSetting(string $name, $value, ?int $contactID = null): void
  {
    $settings = self::getUserSettings($contactID);
    $settings[$name] = $value;
    self::setUserSettings($settings);
  }

  public static function setUserSettings(array $settings, ?int $contactID = null): void
  {
    Civi::contactSettings($contactID)->set(self::UserSettingsKey, $settings);
  }
}
