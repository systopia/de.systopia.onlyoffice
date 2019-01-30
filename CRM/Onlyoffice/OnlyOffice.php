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
  private $websiteHandler = NULL;

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

    $baseUrl = $settings['base_url'];
    $userName = $settings['user_name'];
    $userPassword = $settings['user_password'];

    $this->apiHandler = new CRM_Onlyoffice_ApiHandler();
    $this->apiHandler->setBaseUrl($baseUrl);
    $this->apiHandler->authenticate($userName, $userPassword);

    $this->websiteHandler = new CRM_Onlyoffice_WebsiteHandler();
    $this->websiteHandler->setBaseUrl($baseUrl);
    $this->websiteHandler->setSessionCookies($this->apiHandler->getSessionCookies());
  }

  /**
   * Get all templates.
   * @return array  An array of all templates in the form "id => title".
   */
  public function getTemplates() {
    $files = $this->apiHandler->files();

    $templates = [];
    foreach ($files as $file) {
      $templates[$file->id] = $file->title;
    }

    return $templates;
  }

  public function downloadTemplateFile($fileId) {
    // TODO: Change direct download for testing with something sane.

    $fileStream = $this->websiteHandler->downloadFile($fileId);

    CRM_Utils_System::download('Test.docx', 'application/docx', $fileStream);
  }

}
