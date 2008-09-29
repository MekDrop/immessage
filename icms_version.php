<?php
/**
* imMenu version infomation
*
* This file holds the configuration information of this module
*
* @copyright	ImpressCMS Project 2008
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Gustavo Pilla <nekro@impresscms.org>
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

/**
 * General Information
 */
$modversion['name'] = _MI_IMMESSAGE_MD_NAME;
$modversion['version'] = 1.0;
$modversion['description'] = _MI_IMMESSAGE_MD_DESC;
$modversion['author'] = "Gustavo Pilla <nekro@impresscms.org>";
$modversion['credits'] = "ImpressCMS Project <http://www.impresscms.org>";
$modversion['help'] = "";
$modversion['license'] = "GNU General Public License (GPL)";
$modversion['official'] = 1; // This module is official
$modversion['dirname'] = basename( dirname( __FILE__ ) ) ;

/**
 * Images information
 */
$modversion['iconsmall'] = "images/icon_small.png";
$modversion['iconbig'] = "images/icon_big.png";
// for backward compatibility
$modversion['image'] = $modversion['iconbig'];

/**
 * Development information
 */
$modversion['status_version'] = "Alpha 1";
$modversion['status'] = "Alpha";
$modversion['date'] = "?";
$modversion['author_word'] = "This current version, is a test of concept, and a prove of the power of the IPF (ImpressCMS Persistable Framework)";

/**
 * Contributors
 */
$modversion['developer_website_url'] = "http://www.nubee.com.ar";
$modversion['developer_website_name'] = "Nubee Software Developments";
$modversion['developer_email'] = "info@nubee.com.ar";
$modversion['people']['developers'][] = "Gustavo Pilla nekro@impresscms.org";

$modversion['people']['testers'][] = "davidl";
//$modversion['people']['translators'][] = "";
//$modversion['people']['documenters'][] = "";
$modversion['people']['other'][] = "sato-san";
//$modversion['warning'] = _IM_SOBJECT_WARNING_ALPHA;

/**
 * Administrative information
 */
$modversion['hasAdmin'] = 0;
//$modversion['adminindex'] = "admin/index.php";
//$modversion['adminmenu'] = "include/icms_menu.php";// Renamed... it could be a good future option not?

/**
 * Database information
 */
$modversion['object_items'][1] = 'message';
$modversion["tables"] = icms_getTablesArray($modversion['dirname'], $modversion['object_items']);

/**
 * Install and update informations
 */
$modversion['onInstall'] = "include/icms_onupdate.php"; // Also renamed... i think that is a better way.
$modversion['onUpdate'] = "include/icms_onupdate.php";


/**
 * Search information
 */
// TODO: If is posible to search in a symlink. Also should be posible in a menu.
//$modversion['hasSearch'] = 1;
//$modversion['search']['file'] = "include/icms_search.php";
//$modversion['search']['func'] = "immenu_search";

/**
 * Menu information
 */
$modversion['hasMain'] = 1;
$i = 0 ;
$modversion['sub'][$i]['name'] = _IM_IMMESSAGE_COMPOSE;
$modversion['sub'][$i]['url'] = "inbox.php";
$i = 0 ;
$modversion['sub'][$i]['name'] = _IM_IMMESSAGE_INBOX;
$modversion['sub'][$i]['url'] = "inbox.php";
$i++ ;
$modversion['sub'][$i]['name'] = _IM_IMMESSAGE_SENT;
$modversion['sub'][$i]['url'] = "sent.php";
$i++ ;
$modversion['sub'][$i]['name'] = _IM_IMMESSAGE_DRAFTS;
$modversion['sub'][$i]['url'] = "drafts.php";
$i++ ;
$modversion['sub'][$i]['name'] = _IM_IMMESSAGE_TRASH;
$modversion['sub'][$i]['url'] = "trash.php";
$i++ ;

/**
 * Templates information
 */

$i = 0;

$i++;
$modversion['templates'][$i]['file'] = 'immessage_message.html';
$modversion['templates'][$i]['description'] = _IM_IMMESSAGE_COMPOSE;

$i++;
$modversion['templates'][$i]['file'] = 'immessage_inbox.html';
$modversion['templates'][$i]['description'] = _IM_IMMESSAGE_INBOX;

$i++;
$modversion['templates'][$i]['file'] = 'immessage_sent.html';
$modversion['templates'][$i]['description'] = _IM_IMMESSAGE_SENT;

$i++;
$modversion['templates'][$i]['file'] = 'immessage_drafts.html';
$modversion['templates'][$i]['description'] = _IM_IMMESSAGE_DRAFTS;

$i++;
$modversion['templates'][$i]['file'] = 'immessage_trash.html';
$modversion['templates'][$i]['description'] = _IM_IMMESSAGE_TRASH;

?>