<?php
include("header.php");

include(ICMS_ROOT_PATH."/header.php");

$immenu_message_handler = xoops_getModuleHandler('message');

include_once ICMS_ROOT_PATH."/kernel/icmspersistabletable.php";
$criteriaOr = new CriteriaCompo();
$criteriaOr->add(new Criteria('message_status', _IMMESSAGE_STATUS_SEND));
$criteriaOr->add(new Criteria('message_status', _IMMESSAGE_STATUS_READ), 'OR');
$criteriaOr->add(new Criteria('message_status', _IMMESSAGE_STATUS_TRASH), 'OR');
$criteria = new CriteriaCompo();
$criteria->add($criteriaOr, 'AND');
$criteria->add(new Criteria('message_show_on_sent', 1), 'AND');
$criteria->add(new Criteria('message_from_uid', $xoopsUser->getVar('uid')),'AND');
$objectTable = new IcmsPersistableTable($immenu_message_handler, $criteria, array());
$objectTable->isForUserSide();
$objectTable->setDefaultOrder('message_modification_date');
$objectTable->addColumn(new IcmsPersistableColumn('message_status', 'center'));
$objectTable->addColumn(new IcmsPersistableColumn('message_to_uid', 'left'));
$objectTable->addColumn(new IcmsPersistableColumn('message_title', 'center'));
$objectTable->addColumn(new IcmsPersistableColumn('message_modification_date', 'center'));
$op = isset($_GET['op']) ? $_GET['op'] : "";
$objectTable->addIntroButton('addmenu', 'message.php?op=mod', _CO_IMMESSAGE_MESSAGE_COMPOSE);

$objectTable->addQuickSearch(array('menu_title',"menu_desc"));

$objectTable->addCustomAction('getMessageResponseButton');
$objectTable->addCustomAction('getMessageForwardButton');
$objectTable->addCustomAction('getMessageDeleteFromSentButton');

$objectTable->render();

include(ICMS_ROOT_PATH."/footer.php");
?>