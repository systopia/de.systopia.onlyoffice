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
 * Form to select the document used as template.
 */
class CRM_Onlyoffice_Form_TemplateSelection extends CRM_Core_Form
{
  public function buildQuickForm()
  {
    parent::buildQuickForm();

    if (!CRM_Onlyoffice_PageManager::openedPageIsCorrect(CRM_Onlyoffice_PageManager::TemplateSelectionPageName))
    {
      return;
    };

    $folderTrees = CRM_Onlyoffice_OnlyOffice::getSingleton()->getFolderTrees();

    $convertedFolderTrees = [];
    foreach ($folderTrees as $folderTree)
    {
      $convertedFolderTree = $this->convertFolderStructure($folderTree);
      if ($convertedFolderTree !== null)
      {
        $convertedFolderTrees[] = $convertedFolderTree;
      }
    }

    CRM_Core_Resources::singleton()->addVars('onlyoffice', ['templateTrees' => $convertedFolderTrees]);

    if (empty($convertedFolderTrees))
    {
      $this->assign('showErrorMessage', true);
    }
    else
    {
      // NOTE: This must be kept here for validation and availability in exportValues:
      $this->add(
        'select',
        'template_file_id',
        E::ts('Choose a template'),
        null,
        true
      );
    }

    $this->addButtons(
      [
        [
          'type' => 'back',
          'name' => E::ts('Back'),
          'isDefault' => false,
        ],
        [
          'type' => 'submit',
          'name' => E::ts('Continue'),
          'isDefault' => true,
        ],
      ]
    );

    CRM_Core_Resources::singleton()->addScriptFile('de.systopia.onlyoffice', 'js/jsLists/jsLists.js');
    CRM_Core_Resources::singleton()->addStyleFile('de.systopia.onlyoffice', 'css/jsLists/jsLists.css');

    CRM_Core_Resources::singleton()->addScriptFile('de.systopia.onlyoffice', 'js/templateSelection.js');
    CRM_Core_Resources::singleton()->addStyleFile('de.systopia.onlyoffice', 'css/templateSelection.css');
  }

  /**
   * Convert a folder structure by moving down it's tree and extract only the needed information.
   * @param object $folderTree The folder tree object.
   * @return object|null The converted folder structure.
   */
  private function convertFolderStructure(object $folderTree): ?object
  {
    if (empty($folderTree->folders) && empty($folderTree->files))
    {
      return null;
    }

    $result = new stdClass();

    $result->title = $folderTree->current->title;

    $files = [];
    foreach ($folderTree->files as $treeFile)
    {
      $file = new stdClass();

      $file->id = $treeFile->id;
      $file->title = $treeFile->title;

      $files[] = $file;
    }
    $result->files = $files;

    $folders = [];
    foreach ($folderTree->folders as $treeFolder)
    {
      $folder = $this->convertFolderStructure($treeFolder);

      if ($folder === null)
      {
        continue;
      }

      $folders[] = $folder;
    }
    $result->folders = $folders;

    return $result;
  }

  public function postProcess()
  {
    parent::postProcess();

    $values = $this->exportValues();

    $templateFileId = $values['template_file_id'];

    $this->saveAccountAndContinue($templateFileId);
  }

  private function saveAccountAndContinue(string $templateFileId): void
  {
    $data = CRM_Onlyoffice_PageManager::getData();

    $data->templateId = $templateFileId;

    CRM_Onlyoffice_PageManager::setData($data);

    CRM_Onlyoffice_PageManager::openNextPage();
  }
}
