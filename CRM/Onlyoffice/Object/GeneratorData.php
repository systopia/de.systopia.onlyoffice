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
 * Contains the data stored and used by the generators.
 */
class CRM_Onlyoffice_Object_GeneratorData extends CRM_Onlyoffice_Object_BaseClass
{
    /** @var string $templateId */
    public $templateId;

    /** @var string $zipArchivePath The path to the zip archive containing all generated documents. */
    public $zipArchivePath;

    /** @var string $mainContext Defines the main context containing an unique identifier for the contexts. */
    public $mainContext;

    /** @var CRM_Onlyoffice_Object_TokenContext[] $tokenContexts */
    public $tokenContexts;

    /**
     * @param array $array The given array will be used to initialise the generator data.
     */
    public function __construct(array $array = [])
    {
        parent::__construct($array);

        // Manual initialisation of the object members.
        // NOTE: All object members are at this point either null (completely uninitialised) or an array (the serialised
        //       version of the object put in there by the parent constructor). So we can treat them as arrays which
        //       will, if casted, either do the necessary type hinting or convert the null to an actual empty array.

        if ($this->tokenContexts !== null) {
            /** @var CRM_Onlyoffice_Object_TokenContext[] $initialisedTokenContexts */
            $initialisedTokenContexts = [];

            foreach ($this->tokenContexts as $tokenContext) {
                $initialisedTokenContexts[] = new CRM_Onlyoffice_Object_TokenContext((array)$tokenContext);
            }

            $this->tokenContexts = $initialisedTokenContexts;
        }
    }
}
