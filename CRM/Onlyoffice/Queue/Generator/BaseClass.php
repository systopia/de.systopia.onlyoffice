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
 * Base class for the queue/runner generating documents.
 */
abstract class CRM_Onlyoffice_Queue_Generator_BaseClass
{
  protected const BatchSize = 10;

  /**
   * Create an unused path for a temp file to be created.
   * @param string|null $userPrefix An optional prefix for the file name.
   * @return string The file path.
   */
  protected static function createTempFilePath(?string $userPrefix = null): string
  {
    $prefix = 'CiviCRM_OnlyOffice_';
    if ($userPrefix !== null)
    {
      $prefix .= $userPrefix . '_';
    }

    $tempFilePath = tempnam(sys_get_temp_dir(), $userPrefix);

    return $tempFilePath;
  }
}
