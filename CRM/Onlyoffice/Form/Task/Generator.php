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
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Onlyoffice_Form_Task_Generator extends CRM_Contact_Form_Task {

  function buildQuickForm() {
    CRM_Utils_System::setTitle(E::ts("Generate PDFs via OnlyOffice"));

    $this->add(
        'select',
        'template_file_id',
        E::ts("Template"),
        $this->getTemplates(),
        TRUE);


    $this->addButtons(array(
        array(
            'type' => 'submit',
            'name' => E::ts('Generate!'),
            'isDefault' => FALSE,
        ),
    ));
  }

  /**
   * Process user confirmation/update
   */
  function postProcess() {
    $params = $this->exportValues();
    $this->_contactIds;

    $templateFileString = CRM_Onlyoffice_OnlyOffice::getSingleton()->downloadTemplateFile($params['template_file_id']);

    $zipContainer = $this->createZipFile();

    $zipContainer->addFromString('template.docx', $templateFileString);

    foreach ($this->_contactIds as $contactId) {
      $tempFileName = $this->stringToTempFile($templateFileString);

      CRM_Onlyoffice_OnlyOffice::getSingleton()->makeReadyFileFromTemplateFile($tempFileName, $contactId);

      $tempFileString = $this->tempFileToString($tempFileName, true);

      $readyTemplatedFileString = CRM_Onlyoffice_OnlyOffice::getSingleton()->convertDocxToPdf($tempFileString);

      $zipContainer->addFromString($contactId . '.pdf', $readyTemplatedFileString);

      // TODO: generate PDF for $params['template_file_id']
      //$contact_tokens = Civi::fillTokens($tokens, $contactId);
      // $pdf = CRM_Onlyoffice_OnlyOffice::getSingleton()->renderPDF($contact_tokens);
      // ziparchive pdf
    }

    $zipContainerFilename = $zipContainer->filename;
    $zipContainer->close();
    $resultZip = $this->tempFileToString($zipContainerFilename, true);

    CRM_Utils_System::download('Test.zip', 'application/zip', $resultZip);
  }

  protected function getTemplates() {
     return CRM_Onlyoffice_OnlyOffice::getSingleton()->getTemplates();
  }

  private function createZipFile() {
    $tempFileName = tempnam(sys_get_temp_dir(), 'CiviCRM_OnlyOffice_ZipResultFile_');

    $zip = new ZipArchive();
    $zip->open($tempFileName, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
    // TODO: Check if there is an error when creating the zip file.

    return $zip;
  }

  private function stringToTempFile($fileString) {
    $tempFileName = tempnam(sys_get_temp_dir(), 'CiviCRM_OnlyOffice_TemplateFile_');

    $handle = fopen($tempFileName, "w");
    fwrite($handle, $fileString);
    fclose($handle);

    return $tempFileName;
  }

  private function tempFileToString($tempFileName, $deleteAfterReadout = false) {
    $handle = fopen($tempFileName, "r");
    $content = fread($handle, filesize($tempFileName));
    fclose($handle);

    if ($deleteAfterReadout)
      unlink($tempFileName);

    return $content;
  }
}
