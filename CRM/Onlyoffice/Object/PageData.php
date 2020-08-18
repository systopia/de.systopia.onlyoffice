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

/**
 * Contains the data stored and used by the pages.
 */
class CRM_Onlyoffice_Object_PageData extends CRM_Onlyoffice_Object_GeneratorData
{
  /** @var CRM_Onlyoffice_Object_Account $account */
  public $account;

  /**
   * @param array $array The given array will be used to initialise the page data.
   */
  public function __construct(array $array = [])
  {
    parent::__construct($array);

    // Manual initialisation of the object members.
    // NOTE: All object members are at this point either null (completely uninitialised) or an array (the serialised
    //       version of the object put in there by the parent constructor). So we can treat them as arrays which will,
    //       if casted, either do the necessary type hinting or convert the null to an actual empty array.

    $this->account = new CRM_Onlyoffice_Object_Account((array)$this->account);
  }
}
