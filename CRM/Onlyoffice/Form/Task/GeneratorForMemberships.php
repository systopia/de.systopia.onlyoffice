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
class CRM_Onlyoffice_Form_Task_GeneratorForMemberships extends CRM_Member_Form_Task {

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

    // pass login information
    $this->assign('oopass', CRM_Onlyoffice_Configuration::getSetting('user_password'));
    $this->assign('oouser', CRM_Onlyoffice_Configuration::getSetting('user_name'));
    $this->assign('oolink', CRM_Onlyoffice_Configuration::getSetting('base_url'));
  }

  /**
   * Process user confirmation/update
   */
  function postProcess() {
    $params = $this->exportValues();

    $templateFileString = CRM_Onlyoffice_OnlyOffice::getSingleton()->downloadTemplateFile($params['template_file_id']);

    $zipContainer = $this->createZipFile();

    $zipContainer->addFromString('template.docx', $templateFileString);

    $membershipDataList = $this->getMembershipDataList($this->_memberIds);

    foreach ($membershipDataList as $membershipData) {
      $tempFileName = $this->stringToTempFile($templateFileString);

      $tokenContext = [
        'contactId' => $membershipData['contact_id'],
      ];
      $tokens = [
        'membership' => $membershipData,
      ];
      CRM_Onlyoffice_OnlyOffice::getSingleton()->makeReadyFileFromTemplateFile($tempFileName, $tokenContext, $tokens);

      $tempFileString = $this->tempFileToString($tempFileName, true);

      $readyFileString = CRM_Onlyoffice_OnlyOffice::getSingleton()->convertDocxToPdf($tempFileString);

      $zipContainer->addFromString($membershipData['id'] . '.pdf', $readyFileString);
    }

    $zipContainerFilename = $zipContainer->filename;
    $zipContainer->close();
    $resultZip = $this->tempFileToString($zipContainerFilename, true);

    // TODO: Rename "Test.zip" to something with the name of the template.
    CRM_Utils_System::download('Test.zip', 'application/zip', $resultZip);
  }

  protected function getTemplates() {
     return CRM_Onlyoffice_OnlyOffice::getSingleton()->getTemplates();
  }

  private function getMembershipDataList($memberIds)
  {
    $result = civicrm_api3(
      'Membership',
      'get',
      [
        'sequential' => 1,
        'id' => [
          'IN' => $memberIds
        ],
      ]
    );

    return $result['values'];
  }

  /**
   * Creates a zip file with unique name in the system temporary directory.
   * @return \ZipArchive The created zip archive object.
   */
  private function createZipFile() {
    $tempFileName = tempnam(sys_get_temp_dir(), 'CiviCRM_OnlyOffice_ZipResultFile_');

    $zip = new ZipArchive();
    $zip->open($tempFileName, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
    // TODO: Check if there is an error when creating the zip file.

    return $zip;
  }

  /**
   * Saves a string in a new file with unique name in the system temporary directory.
   * @param $fileString string The string to be written into the file.
   * @return bool|string The full name/path to the file.
   */
  private function stringToTempFile($fileString) {
    $tempFileName = tempnam(sys_get_temp_dir(), 'CiviCRM_OnlyOffice_TemplateFile_');

    $handle = fopen($tempFileName, "w");
    fwrite($handle, $fileString);
    fclose($handle);

    return $tempFileName;
  }

  /**
   * Reads a string from a (temporary) file.
   * @param $tempFileName string The full name/path to the file.
   * @param $deleteAfterReadout boolean If true, the file will be deleted after readout.
   * @return bool|string The content of the file as string.
   */
  private function tempFileToString($tempFileName, $deleteAfterReadout = false) {
    $handle = fopen($tempFileName, "r");
    $content = fread($handle, filesize($tempFileName));
    fclose($handle);

    if ($deleteAfterReadout)
      unlink($tempFileName);

    return $content;
  }
}
