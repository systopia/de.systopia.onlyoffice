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

    CRM_Onlyoffice_OnlyOffice::getSingleton()->downloadTemplateFile($params['template_file_id']);

    // get tokens
    //$tokens = CRM_Onlyoffice_OnlyOffice::getSingleton()->extractTokes($params['template_file_id']);

    foreach ($this->_contactIds as $contactId) {
      // TODO: generate PDF for $params['template_file_id']
      //$contact_tokens = Civi::fillTokens($tokens, $contactId);
      // $pdf = CRM_Onlyoffice_OnlyOffice::getSingleton()->renderPDF($contact_tokens);
      // ziparchive pdf
    }

    //$data = "sadkhsaldas";
    //CRM_Utils_System::download('Test.txt', 'application/text', $data);

    //CRM_Utils_System::redirect("https://sustainability.asda.com/sites/default/files/Green%20Britain%20Index%202016%20web_2.pdf");
  }


  protected function getTemplates() {
     return CRM_Onlyoffice_OnlyOffice::getSingleton()->getTemplates();
  }
}
