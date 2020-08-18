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
 * The launcher for a queue/runner generating documents.
 */
abstract class CRM_Onlyoffice_Queue_Generator_Launcher extends CRM_Onlyoffice_Queue_Generator_BaseClass
{
  public static function prepare(CRM_Onlyoffice_Object_PageData $data): void
  {
    $data->zipArchivePath = self::createTempFile('ZipResultFile');
  }

  /**
   * Launch the runner.
   * @param CRM_Onlyoffice_Object_PageData $pageData The data used by the runner.
   * @param string $contactId The contact ID of the user launching the runner.
   * @param string $targetUrl The URL we shall redirect after the runner has been finished.
   */
  public static function launchRunner(CRM_Onlyoffice_Object_PageData $pageData, string $contactId, string $targetUrl): void
  {
    $queue = CRM_Queue_Service::singleton()->create(
      [
        'type' => 'Sql',
        'name' => 'onlyoffice_generator_' . $contactId,
        'reset' => true,
      ]
    );

    $templateFilePath = self::createTempFile('TemplateFile');

    $generatorStartData = new CRM_Onlyoffice_Object_GeneratorData();
    $generatorStartData->templateId = $pageData->templateId;
    $generatorStartData->zipArchivePath = $pageData->zipArchivePath;
    $generatorStartData->mainContext = $pageData->mainContext;
    $generatorStartData->tokenContexts = [];

    $dataCount = count($pageData->tokenContexts);

    $queue->createItem(new CRM_Onlyoffice_Queue_Generator_GeneratorStart($templateFilePath, $generatorStartData, $dataCount));

    for ($offset = 0; $offset < $dataCount; $offset += self::BatchSize)
    {
      $batchedTokenContext = array_slice($pageData->tokenContexts, $offset, self::BatchSize);

      $generatorData = new CRM_Onlyoffice_Object_GeneratorData();
      $generatorData->templateId = $pageData->templateId;
      $generatorData->zipArchivePath = $pageData->zipArchivePath;
      $generatorData->mainContext = $pageData->mainContext;
      $generatorData->tokenContexts = $batchedTokenContext;

      $queue->createItem(
        new CRM_Onlyoffice_Queue_Generator_Generator($templateFilePath, $generatorData, $offset, self::BatchSize)
      );
    }

    $runner = new CRM_Queue_Runner(
      [
        'title' => E::ts('Generating documents'),
        'queue' => $queue,
        'errorMode' => CRM_Queue_Runner::ERROR_ABORT,
        'onEndUrl' => $targetUrl,
      ]
    );

    $runner->runAllViaWeb();
  }

  /**
   * Create an empty temporary file.
   * @param string|null $prefix An optional prefix for the temporary file.
   * @return string The full path to the file.
   */
  private static function createTempFile (?string $prefix): string
  {
    $tempFilePath = self::createTempFilePath($prefix);

    $file = fopen($tempFilePath, 'w');
    fclose($file);
    // TODO: Check if there is an error creating the file.

    return $tempFilePath;
  }
}
