<?php
/**
* Common file of the module included on all pages of the module
*
* @copyright	The ImpressCMS Project <http://www.impresscms.org/>
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Gustavo Pilla <nekro@impresscms.org>
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

if(!defined("IMMESSAGE_DIRNAME"))		define("IMMESSAGE_DIRNAME", $modversion['dirname'] = basename(dirname(dirname(__FILE__))));
if(!defined("IMMESSAGE_URL"))			define("IMMESSAGE_URL", ICMS_URL.'/modules/'.IMMESSAGE_DIRNAME.'/');
if(!defined("IMMESSAGE_ROOT_PATH"))	define("IMMESSAGE_ROOT_PATH", ICMS_ROOT_PATH.'/modules/'.IMMESSAGE_DIRNAME.'/');
if(!defined("IMMESSAGE_IMAGES_URL"))	define("IMMESSAGE_IMAGES_URL", IMMESSAGE_URL.'images/');
if(!defined("IMMESSAGE_ADMIN_URL"))	define("IMMESSAGE_ADMIN_URL", IMMESSAGE_URL.'admin/');

// Include the common language file of the module
icms_loadLanguageFile('immessage', 'common');

include_once(IMMESSAGE_ROOT_PATH . "include/functions.php");

// Creating the module object to make it available throughout the module
$imMessageModule = icms_getModuleInfo(IMMESSAGE_DIRNAME);
if (is_object($imMessageModule)){
	$imMessage_moduleName = $imMessageModule->getVar('name');
}

// Find if the user is admin of the module and make this info available throughout the module
$imMessage_isAdmin = icms_userIsAdmin(IMMESSAGE_DIRNAME);

// Creating the module config array to make it available throughout the module
$imMessageConfig = icms_getModuleConfig(IMMESSAGE_DIRNAME);

// including the post class
include_once(IMMESSAGE_ROOT_PATH . 'class/message.php');
//include_once(IMMESSAGE_ROOT_PATH . 'class/account.php');

// creating the icmsPersistableRegistry to make it available throughout the module
global $icmsPersistableRegistry;
$icmsPersistableRegistry = IcmsPersistableRegistry::getInstance();

?>
