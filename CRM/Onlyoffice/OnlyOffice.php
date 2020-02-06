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
 * General handling class
 */
final class CRM_Onlyoffice_OnlyOffice
{
  private static $singleton = null;

  /**
   * @var CRM_Onlyoffice_ApiHandler $apiHandler
   */
  private $apiHandler = null;
  /**
   * @var CRM_Onlyoffice_WebsiteHandler $websiteHandler
   */
  private $websiteHandler = null;

  /**
  * Get the Onlyoffice controller singleton
  */
  public static function getSingleton(): CRM_Onlyoffice_OnlyOffice
  {
    if (self::$singleton === null)
    {
      self::$singleton = new CRM_Onlyoffice_OnlyOffice();
    }

    return self::$singleton;
  }

  /**
   * CRM_Onlyoffice_OnlyOffice constructor.
   */
  function __construct()
  {
    $baseUrl = CRM_Onlyoffice_Configuration::getAdminSetting(CRM_Onlyoffice_Configuration::BaseUrlKey);

    $pageData = CRM_Onlyoffice_PageManager::getData();

    $this->apiHandler = new CRM_Onlyoffice_ApiHandler();
    $this->apiHandler->setBaseUrl($baseUrl);
    $this->apiHandler->authenticate($pageData->account->name, $pageData->account->password);

    $this->websiteHandler = new CRM_Onlyoffice_WebsiteHandler();
    $this->websiteHandler->setBaseUrl($baseUrl);
    $this->websiteHandler->setSessionCookies($this->apiHandler->getSessionCookies());
  }

  /**
   * Get all folders as a tree containing the information about folders and their files.
   * @return array A list of trees, one for each main folder (private, common, shared).
   */
  public function getFolderTrees(): array
  {
    $folderIds = [$this->apiHandler::PrivateFolderId, $this->apiHandler::CommonFolderId, $this->apiHandler::SharedFolderId];

    $result = [];

    foreach ($folderIds as $folderId)
    {
      $tree = $this->buildFolderAsTree($folderId);

      $result[] = $tree;
    }

    return $result;
  }

  private function buildFolderAsTree(string $folderId): object
  {
    $result = new stdClass();

    $folder = $this->apiHandler->listAll($folderId);

    $result->current = $folder->current;

    $result->files = $folder->files;

    $folders = [];
    foreach ($folder->folders as $childFolder)
    {
      $folders[] = $this->buildFolderAsTree($childFolder->id);
    }
    $result->folders = $folders;

    return $result;
  }

  /**
   * Downloads a template file as string.
   * @param string $fileId The id of the file to download.
   * @return false|string The downloaded file as string.
   */
  public function downloadTemplateFile(string $fileId)
  {
    $fileString = $this->websiteHandler->downloadFile($fileId);

    return $fileString;
  }

  /**
   * Get the name/title of a template file by it's ID.
   * @param string $fileID The ID to look for.
   * @return string The name/title of the given file.
   */
  public function getTemplateFileName(string $fileId): string
  {
    $fileData = $this->apiHandler->getFileInformation($fileId);

    return $fileData->title;
  }

  /**
   * Files a template file via token resolving with contact specific content.
   * @param $tempFileName string The full name/path to the file.
   * @param $contexts array An array in the form "contextName => contextData" with different token contexts
   *                        and their needed data (for example, contact IDs).
   */
  public function makeReadyFileFromTemplateFile($tempFileName, $contexts, $tokens=[])
  {
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
    for ($i = 0; $i < $numberOfFiles; $i++)
    {
      $fileContent = $zip->getFromIndex($i);
      $fileName = $zip->getNameIndex($i);

      $processor->addMessage($fileName, $fileContent , 'text/plain');

      $fileList[] = $fileName;
    }

    $tokenRow = $processor->addRow();

    $tokenRow->context($contexts);
    $tokenRow->tokens($tokens);

    $processor->evaluate();

    foreach ($processor->getRows() as $row)
    {
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
  public function convertDocxToPdf($inputFileString)
  {
    $uploadedFileData = $this->apiHandler->uploadDocx(sha1(rand()) . '.docx', $inputFileString);

    $outputFileString = $this->websiteHandler->downloadFileAsPdf($uploadedFileData->id);

    $this->apiHandler->deleteFile($uploadedFileData->id);

    return $outputFileString;
  }
}
