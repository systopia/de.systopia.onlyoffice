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
    $files = $this->apiHandler->listFiles();

    $templates = [];
    foreach ($files as $file) {
      $templates[$file->id] = $file->title;
    }

    return $templates;
  }

  /**
   * Downloads a template file as string.
   * @param $fileId string The id of the file to download.
   * @return false|string The file stream of the downloaded file.
   */
  public function downloadTemplateFile($fileId) {
    $fileStream = $this->websiteHandler->downloadFile($fileId);

    return $fileStream;

    //CRM_Utils_System::download('Test.pdf', 'application/pdf', $fileStream);
  }

  public function makeReadyFileFromTemplateFile($tempFileName, $tokenList) {
    // TODO: Give better name.

    // TODO: Are keys and values both in the same order?
    $tokenKeys = array_keys($tokenList);
    $tokenValues = array_values($tokenList);

    $zip = new ZipArchive();
    $zip->open($tempFileName);
    // TODO: Check if there is an error when opening the zip file.

    $numberOfFiles = $zip->numFiles;
    for ($i = 0; $i < $numberOfFiles; $i++) {
      $fileContent = $zip->getFromIndex($i);
      $fileContent = str_replace($tokenKeys, $tokenValues, $fileContent);
      $zip->addFromString($zip->getNameIndex($i), $fileContent);
    }

    $zip->close();
  }

  public function convertDocxToPdf($inputFileStream) {
    $uploadedFileData = $this->apiHandler->uploadDocx(sha1(rand()) . '.docx', $inputFileStream);

    $outputFileStream = $this->websiteHandler->downloadFileAsPdf($uploadedFileData->id);

    $this->apiHandler->deleteFile($uploadedFileData->id);

    return $outputFileStream;
  }
}
