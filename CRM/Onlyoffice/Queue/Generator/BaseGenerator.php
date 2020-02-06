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
 * Base class for the generator queue/runner classes.
 */
abstract class CRM_Onlyoffice_Queue_Generator_BaseGenerator extends CRM_Onlyoffice_Queue_Generator_BaseClass
{
  /** @var string $title Will be set as title by the runner. */
  public $title;

  /** @var string $templateFilePath The path to the template file that shall be used. */
  protected $templateFilePath;

  /** @var CRM_Onlyoffice_Object_PageData $data The page data for this generator instance. */
  protected $data;

  public function __construct(string $templateFilePath, CRM_Onlyoffice_Object_PageData $data)
  {
    $this->templateFilePath = $templateFilePath;
    $this->data = $data;
  }

  abstract public function run(): bool;

  /**
   * Open a zip file.
   * @param string $filePath The path to the zip file that shall be opened/created.
   * @param bool $createNew If true, a new file will be created; an existing file will be overwritten.
   * @return ZipArchive The opened zip archive.
   */
  protected function openZipFile(string $filePath, bool $createNew = false): ZipArchive
  {
    $flags = $createNew ? ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE : null;

    $zipContainer = new ZipArchive();
    $zipContainer->open($filePath, $flags);
    // TODO: Check if there is an error when opening/creating the zip file.

    return $zipContainer;
  }
}
