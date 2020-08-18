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
 * Starting queue/runner for generating documents.
 */
class CRM_Onlyoffice_Queue_Generator_GeneratorStart extends CRM_Onlyoffice_Queue_Generator_BaseGenerator
{
  public function __construct(string $templateFilePath, CRM_Onlyoffice_Object_GeneratorData $data)
  {
    parent::__construct($templateFilePath, $data);

    $this->title = E::ts('Starting document generation.');
  }

  public function run(): bool
  {
    $templateFileName = CRM_Onlyoffice_OnlyOffice::getSingleton()->getTemplateFileName($this->data->templateId);
    $templateFileString = CRM_Onlyoffice_OnlyOffice::getSingleton()->downloadTemplateFile($this->data->templateId);

    $zipContainer = $this->openZipFile($this->data->zipArchivePath, true);

    // Write the template file into the archive:
    $zipContainer->addFromString($templateFileName, $templateFileString); // TODO: Do we need this?

    // Write template string into temp file:
    $templateFile = fopen($this->templateFilePath, 'w');
    fwrite($templateFile, $templateFileString);
    fclose($templateFile);
    // TODO: Check if there is an error creating the file.

    $zipContainer->close();

    return true;
  }
}
