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
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function onlyoffice_civicrm_xmlMenu(&$files) {
  _onlyoffice_civix_civicrm_xmlMenu($files);
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
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function onlyoffice_civicrm_postInstall() {
  _onlyoffice_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function onlyoffice_civicrm_uninstall() {
  _onlyoffice_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function onlyoffice_civicrm_enable() {
  _onlyoffice_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function onlyoffice_civicrm_disable() {
  _onlyoffice_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function onlyoffice_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _onlyoffice_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function onlyoffice_civicrm_managed(&$entities) {
  _onlyoffice_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function onlyoffice_civicrm_caseTypes(&$caseTypes) {
  _onlyoffice_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function onlyoffice_civicrm_angularModules(&$angularModules) {
  _onlyoffice_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function onlyoffice_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _onlyoffice_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function onlyoffice_civicrm_entityTypes(&$entityTypes) {
  _onlyoffice_civix_civicrm_entityTypes($entityTypes);
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
