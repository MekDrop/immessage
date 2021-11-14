<?php
include("header.php");

include(ICMS_ROOT_PATH."/header.php");

$immenu_message_handler = \icms_getModuleHandler('message');

$criteriaOr = new CriteriaCompo();
$criteriaOr->add(new Criteria('message_status', _IMMESSAGE_STATUS_TRASH));
$criteria = new CriteriaCompo();
$criteria->add($criteriaOr, 'AND');
$criteria->add(new Criteria('message_to_uid', \icms::$user->getVar('uid')),'AND');
$criteria->add(new Criteria('message_show_on_inbox', 0), 'AND');
$objectTable = new IcmsPersistableTable($immenu_message_handler, $criteria, array());
$objectTable->isForUserSide();
$objectTable->setDefaultOrder('message_modification_date');
$objectTable->addColumn(new IcmsPersistableColumn('message_status', 'center'));
$objectTable->addColumn(new IcmsPersistableColumn('message_from_uid', 'left'));
$objectTable->addColumn(new IcmsPersistableColumn('message_title', 'center'));
$objectTable->addColumn(new IcmsPersistableColumn('message_modification_date', 'center'));
$op = isset($_GET['op']) ? $_GET['op'] : "";
$objectTable->addIntroButton('addmenu', 'message.php?op=mod', _CO_IMMESSAGE_MESSAGE_COMPOSE);

$objectTable->addQuickSearch(array('message_title',"message_content"));

$objectTable->addCustomAction('getMessageDeleteFromTrashButton');

$objectTable->render();

include(ICMS_ROOT_PATH."/footer.php");