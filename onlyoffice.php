<?php

require_once 'onlyoffice.civix.php';
use CRM_Onlyoffice_ExtensionUtil as E;


function onlyoffice_civicrm_searchTasks($objectType, &$tasks)
{
  // add "Create PDFs via OnlyOffice" task to contact list
  if ($objectType == 'contact')
  {
    $tasks[] = [
      'title' => E::ts('Create PDFs via Onlyoffice'),
      'class' => 'CRM_Onlyoffice_Form_Task_ContactSearch',
      'result' => false
    ];
  }
}

function onlyoffice_civicrm_summaryActions(&$actions, $contactID)
{
  // add "Connect user with Onlyoffice account" action
  if (CRM_Core_Permission::check('administer CiviCRM'))
  {
    $actions['onlyoffice_connect_user_account'] = [
      'title'       => E::ts('Connect user with Onlyoffice account'),
      'weight'      => 2409,
      'ref'         => 'connect_user_with_onlyoffice',
      'key'         => 'onlyoffice_connect_user_account',
      'href'        => CRM_Utils_System::url('civicrm/onlyoffice/settings/connectuser/connection', "reset=1&cid={$contactID}"),
      'permissions' => ['administer CiviCRM']
    ];
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function onlyoffice_civicrm_config(&$config) {
  _onlyoffice_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function onlyoffice_civicrm_install() {
  _onlyoffice_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function onlyoffice_civicrm_enable() {
  _onlyoffice_civix_civicrm_enable();
}

// Polyfill for array_key_first (only needed for PHP < 7.3):
if (!function_exists('array_key_first'))
{
  function array_key_first(array $array)
  {
    foreach ($array as $key => $unused)
    {
      return $key;
    }

    return null;
  }
}
