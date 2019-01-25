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
 * General handling class
 */
class CRM_Onlyoffice_OnlyOffice {

  private static $singleton = NULL;
  private $apiHandler = NULL;

  /**
  * Get the Onlyoffice controller singleton
  */
  public static function getSingleton() {
    if (self::$singleton === NULL) {
      self::$singleton = new CRM_Onlyoffice_OnlyOffice();
    }
    return self::$singleton;
  }

  function __construct() {
    $settings = CRM_Onlyoffice_Configuration::getSettings();

    $this->apiHandler = new CRM_Onlyoffice_ApiHandler();
    $this->apiHandler->setBaseUrl($settings['base_url']);

    $this->apiHandler->authenticate($settings['user_name'], $settings['user_password']);
  }

  /**
   * Get all templates.
   * @return An array of all templates in the form "id => title".
   */
  public function getTemplates() {
    $files = $this->apiHandler->files();

    $templates = [];
    foreach ($files as $file) {
      $templates[$file->id] = $file->title;
    }

    return $templates;
  }
}
