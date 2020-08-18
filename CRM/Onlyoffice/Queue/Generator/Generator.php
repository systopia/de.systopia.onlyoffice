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
 * The queue/runner for generating documents.
 */
class CRM_Onlyoffice_Queue_Generator_Generator extends CRM_Onlyoffice_Queue_Generator_BaseGenerator
{
  /** @var int $offset The page data offset for this generator instance. */
  protected $offset;

  /** @var int $count The number of data sets this generator instance shall work on. */
  protected $count;

  public function __construct(string $templateFilePath, CRM_Onlyoffice_Object_GeneratorData $data, int $offset, int $count)
  {
    parent::__construct($templateFilePath, $data);

    $this->offset = $offset;
    $this->count = $count;

    $this->title = E::ts('Generating document number %1 to %2.', [1 => $offset + 1, 2 => $offset + $count]);
  }

  public function run(): bool
  {
    $onlyofficeSingleton = CRM_Onlyoffice_OnlyOffice::getSingleton();

    $zipContainer = $this->openZipFile($this->data->zipArchivePath);

    foreach ($this->data->tokenContexts as $tokenContext)
    {
      $tempFilePath = $this->copyFile($this->templateFilePath);

      $onlyofficeSingleton->makeReadyFileFromTemplateFile($tempFilePath, $tokenContext->contexts, $tokenContext->tokens);

      // Read the file completely:
      $tempFileString = file_get_contents($tempFilePath);
      // After that, the temporary file isn't needed anymore:
      unlink($tempFilePath);

      $readyFileString = $onlyofficeSingleton->convertDocxToPdf($tempFileString);

      $contextIdentifier = $tokenContext->contexts[$this->data->mainContext];
      $zipContainer->addFromString($contextIdentifier . '.pdf', $readyFileString);
    }

    $zipContainer->close();

    return true;
  }

  /**
   * Saves a string in a new file with unique name in the system temporary directory.
   * @param $fileString string The string to be written into the file.
   * @return bool|string The full name/path to the file.
   */
  private function copyFile(string $filePath): string
  {
    $copiedFilePath = self::createTempFilePath('ReadyFileInConversion');

    copy($filePath, $copiedFilePath);
    // TODO: Handle errors while copying the file.

    return $copiedFilePath;
  }
}
