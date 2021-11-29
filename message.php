<?php
include('header.php');

$immessage_message_handler = \icms_getModuleHandler('message');

$op = '';
if (isset($_GET['op'])) $op = $_GET['op'];
if (isset($_POST['op'])) $op = $_POST['op'];

$message_id = 0;
if (isset($_GET['message_id'])) $message_id = $_GET['message_id'];
if (isset($_POST['message_id'])) $message_id = $_POST['message_id'];


if($message_id == 0 && $op != 'mod'){
	header("Location: inbox.php");
	die();
}

switch($op){
	case "addmessage":
        $controller = new IcmsPersistableController($immessage_message_handler);
		$controller->storeFromDefaultForm(_AM_IMMESSAGE_MESSAGE_CREATED, _AM_IMMESSAGE_MESSAGE_MODIFIED);
		break;
	case "del":
		include(ICMS_ROOT_PATH."/header.php");
	    include_once ICMS_ROOT_PATH."/kernel/icmspersistablecontroller.php";
        $controller = new IcmsPersistableController($immessage_message_handler);
		$controller->handleObjectDeletionFromUserSide();
		redirect_header(IMMESSAGE_URL."/drafts.php");
		include(ICMS_ROOT_PATH."/footer.php");
		break;
	case 'trash':
		include(ICMS_ROOT_PATH."/header.php");
		xoops_confirm(array('op' => 'dotrash','message_id'=> $message_id), IMMESSAGE_URL.'message.php', _CO_IMMESSAGE_MESSAGE_SEND_TRASH_CONFIRM);
		include(ICMS_ROOT_PATH."/footer.php");
		break;
	case 'delete':
		include(ICMS_ROOT_PATH."/header.php");
		xoops_confirm(array('op' => 'dodelete','message_id'=> $message_id), IMMESSAGE_URL.'message.php', _CO_IMMESSAGE_MESSAGE_DELETE_FROM_TRASH_CONFIRM);
		include(ICMS_ROOT_PATH."/footer.php");
		break;
	case 'dfsent':
		include(ICMS_ROOT_PATH."/header.php");
		xoops_confirm(array('op' => 'dodfsent','message_id'=> $message_id), IMMESSAGE_URL.'message.php', _CO_IMMESSAGE_MESSAGE_DELETE_FROM_SENT_CONFIRM);
		include(ICMS_ROOT_PATH."/footer.php");
		break;
}