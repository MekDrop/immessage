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
	case 'mod':
		$xoopsOption['template_main'] = 'immessage_message.html';
		include(ICMS_ROOT_PATH."/header.php");

		$postObj = $immessage_message_handler->get($message_id);

		if (!$postObj->isNew()){
			$sform = $postObj->getForm(_CO_IMMESSAGE_MESSAGE_EDIT, 'addmessage');
			$sform->assign($xoopsTpl);
		}else{
			$sform = $postObj->getForm(_CO_IMMESSAGE_MESSAGE_CREATE, 'addmessage');
			$sform->assign($xoopsTpl);
		}
		include(ICMS_ROOT_PATH."/footer.php");
		break;
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
	case 'response':
		$xoopsOption['template_main'] = 'immessage_message.html';
		include(ICMS_ROOT_PATH."/header.php");
		$messageObj = $immessage_message_handler->get($message_id);
		$messageObj->setVar('message_id',0);
		$messageObj->setVar('message_title',"RE: ".$messageObj->getVar('message_title'));
		$messageObj->setVar('message_content',"[quote]".$messageObj->getVar('message_content')."[/quote]");
		$messageObj->setVar('message_to_uid', $messageObj->getVar('message_from_uid'));
		$messageObj->setVar('message_from_uid', \icms::$user->uid);
		$sform = $messageObj->getForm(_CO_IMMESSAGE_SEND_RESPONSE, 'addmessage');
		$sform->assign($xoopsTpl);
		include(ICMS_ROOT_PATH."/footer.php");
		break;
	case 'forward':
		$xoopsOption['template_main'] = 'immessage_message.html';
		include(ICMS_ROOT_PATH."/header.php");
		$messageObj = $immessage_message_handler->get($message_id);
		$messageObj->setVar('message_id',0);
		$messageObj->setVar('message_title',"FWD: ".$messageObj->getVar('message_title'));
		$messageObj->setVar('message_content',"[quote]".$messageObj->getVar('message_content')."[/quote]");
		$messageObj->setVar('message_from_uid', \icms::$user->uid);
		$sform = $messageObj->getForm(_CO_IMMESSAGE_SEND_FORWARD, 'addmessage');
		$sform->assign($xoopsTpl);
		include(ICMS_ROOT_PATH."/footer.php");
		break;
	case 'trash':
		include(ICMS_ROOT_PATH."/header.php");
		xoops_confirm(array('op' => 'dotrash','message_id'=> $message_id), IMMESSAGE_URL.'message.php', _CO_IMMESSAGE_MESSAGE_SEND_TRASH_CONFIRM);
		include(ICMS_ROOT_PATH."/footer.php");
		break;
	case 'dotrash':
		$messageObj = $immessage_message_handler->get($message_id);
		$messageObj->setVar('message_show_on_inbox',0);
		$messageObj->setVar('message_status',_IMMESSAGE_STATUS_TRASH);
		$immessage_message_handler->insert($messageObj);
		redirect_header(IMMESSAGE_URL.'inbox.php', 3 , _CO_IMMESSAGE_MESSAGE_SENT_TO_TRASH);
		break;
	case 'delete':
		include(ICMS_ROOT_PATH."/header.php");
		xoops_confirm(array('op' => 'dodelete','message_id'=> $message_id), IMMESSAGE_URL.'message.php', _CO_IMMESSAGE_MESSAGE_DELETE_FROM_TRASH_CONFIRM);
		include(ICMS_ROOT_PATH."/footer.php");
		break;
	case 'dodelete':
		$messageObj = $immessage_message_handler->get($message_id);
		$messageObj->setVar('message_show_on_inbox',0);
		$messageObj->setVar('message_status',_IMMESSAGE_STATUS_DELETED);
		$immessage_message_handler->insert($messageObj);
		redirect_header(IMMESSAGE_URL.'inbox.php', 3 , _CO_IMMESSAGE_MESSAGE_DELETED_FROM_TRASH);
		break;
	case 'dfsent':
		include(ICMS_ROOT_PATH."/header.php");
		xoops_confirm(array('op' => 'dodfsent','message_id'=> $message_id), IMMESSAGE_URL.'message.php', _CO_IMMESSAGE_MESSAGE_DELETE_FROM_SENT_CONFIRM);
		include(ICMS_ROOT_PATH."/footer.php");
		break;
	case 'dodfsent':
		$messageObj = $immessage_message_handler->get($message_id);
		$messageObj->setVar('message_show_on_sent',0);
		$immessage_message_handler->insert($messageObj);
		redirect_header(IMMESSAGE_URL.'sent.php', 3 , _CO_IMMESSAGE_MESSAGE_DELETED_FROM_SENT);
		break;
	default:
		$messageObj = $immessage_message_handler->get($message_id);
		$xoopsOption['template_main'] = 'immessage_message.html';
		include(ICMS_ROOT_PATH."/header.php");
		if($messageObj->getVar('message_status','e') != _IMMESSAGE_STATUS_READ){
			$messageObj->setVar('message_status',_IMMESSAGE_STATUS_READ);
			$immessage_message_handler->insert($messageObj, true);
		}
		$xoopsTpl->assign("message_title",$messageObj->getVar('message_title'));
		$xoopsTpl->assign("message_actions",$messageObj->getButtons());
		$xoopsTpl->assign("message_content",$messageObj->getVar('message_content'));
		$xoopsTpl->assign("message_from", _CO_IMMESSAGE_MESSAGE_FROM.": <b>".$messageObj->getVar('message_from_uid')."</b>");
		$xoopsTpl->assign("message_subject", _CO_IMMESSAGE_MESSAGE_SUBJECT.": <b>".$messageObj->getVar('message_title')."</b>");
		$xoopsTpl->assign("message_to", _CO_IMMESSAGE_MESSAGE_TO.": <b>".$messageObj->getVar('message_to_uid')."</b>");
		$xoopsTpl->assign("message_modification_date",_CO_IMMESSAGE_MESSAGE_DATE.": <b>".$messageObj->getVar('message_modification_date')."</b>");
		include(ICMS_ROOT_PATH."/footer.php");
}