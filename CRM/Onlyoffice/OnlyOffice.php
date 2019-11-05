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

  /**
   * CRM_Onlyoffice_OnlyOffice constructor.
   */
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
   * @return array An array of all templates in the form "id => title".
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
   * @return false|string The downloaded file as string.
   */
  public function downloadTemplateFile($fileId) {
    $fileString = $this->websiteHandler->downloadFile($fileId);

    return $fileString;
  }

  /**
   * Files a template file via token resolving with contact specific content.
   * @param $tempFileName string The full name/path to the file.
   * @param $contexts array An array in the form "contextName => contextData" with different token contexts
   *                        and their needed data (for example, contact IDs).
   */
  public function makeReadyFileFromTemplateFile($tempFileName, $contexts, $tokens=[]) {
    // TODO: Give better name.

    $zip = new ZipArchive();
    $zip->open($tempFileName);
    // TODO: Check if there is an error when opening the zip file.

    $processor = new \Civi\Token\TokenProcessor(Civi::service('dispatcher'), array(
      'controller' => __CLASS__,
      'smarty' => FALSE,
    ));

    $fileList = [];

    $numberOfFiles = $zip->numFiles;
    for ($i = 0; $i < $numberOfFiles; $i++) {
      $fileContent = $zip->getFromIndex($i);
      $fileName = $zip->getNameIndex($i);

      $processor->addMessage($fileName, $fileContent , 'text/plain');

      $fileList[] = $fileName;
    }

    $tokenRow = $processor->addRow();

    $tokenRow->context($contexts);
    $tokenRow->tokens($tokens);

    $processor->evaluate();

    foreach ($processor->getRows() as $row) {
      foreach ($fileList as $fileName) {
        $fileContent = $row->render($fileName);

        $zip->addFromString($fileName, $fileContent);
      }
    }

    $zip->close();
  }

  /**
   * Converts a DocX file to PDF.
   * @param $inputFileString string The input file as string.
   * @return false|string The output file as string.
   */
  public function convertDocxToPdf($inputFileString) {
    $uploadedFileData = $this->apiHandler->uploadDocx(sha1(rand()) . '.docx', $inputFileString);

    $outputFileString = $this->websiteHandler->downloadFileAsPdf($uploadedFileData->id);

    $this->apiHandler->deleteFile($uploadedFileData->id);

    return $outputFileString;
  }
}
