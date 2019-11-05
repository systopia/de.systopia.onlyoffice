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
class CRM_Onlyoffice_Configuration
{
  private const AdminSettingsKey = 'onlyoffice_admin_settings';
  private const UserSettingsKey = 'onlyoffice_user_settings';

  public static function getAdminSetting($name)
  {
    $settings = self::getAdminSettings();
    return CRM_Utils_Array::value($name, $settings, NULL);
  }

  public static function getAdminSettings()
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

  public static function setAdminSetting($name, $value)
  {
    $settings = self::getAdminSettings();
    $settings[$name] = $value;
    self::setAdminSettings($settings);
  }

  public static function setAdminSettings($settings)
  {
    Civi::settings()->set(self::AdminSettingsKey, $settings);
  }

  public static function getUserSetting($name)
  {
    $settings = self::getUserSettings();
    return CRM_Utils_Array::value($name, $settings, NULL);
  }

  public static function getUserSettings()
  {
    $settings = Civi::contactSettings()->get(self::UserSettingsKey);
    if ($settings && is_array($settings))
    {
      return $settings;
    }
    else
    {
      return [];
    }
  }

  public static function setUserSetting($name, $value)
  {
    $settings = self::getUserSettings();
    $settings[$name] = $value;
    self::setUserSettings($settings);
  }

  public static function setUserSettings($settings)
  {
    Civi::userSettings()->set(self::UserSettingsKey, $settings);
  }
}
