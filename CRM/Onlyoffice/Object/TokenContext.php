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
 * Contains contexts and tokens used to tokenise a document.
 */
class CRM_Onlyoffice_Object_TokenContext extends CRM_Onlyoffice_Object_BaseClass
{
  /** @var array $contexts A list of contexts to apply in the tokenisation process. */
  public $contexts;

  /** @var array $tokens A list of tokens used directly in the tokenisation process. */
  public $tokens;
}
