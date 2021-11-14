<?php
include("header.php");

include(ICMS_ROOT_PATH."/header.php");

$immenu_message_handler = \icms_getModuleHandler('message');

$criteria = new CriteriaCompo();
$criteria->add(new Criteria('message_status', _IMMESSAGE_STATUS_DRAFT));
$criteria->add(new Criteria('message_from_uid', \icms::$user->uid));
$objectTable = new IcmsPersistableTable($immenu_message_handler, $criteria, array('edit'));
$objectTable->isForUserSide();
$objectTable->setDefaultOrder('message_modification_date');
$objectTable->addColumn(new IcmsPersistableColumn('message_status', 'center'));
$objectTable->addColumn(new IcmsPersistableColumn('message_to_uid', 'left'));
$objectTable->addColumn(new IcmsPersistableColumn('message_title', 'center'));
$objectTable->addColumn(new IcmsPersistableColumn('message_modification_date', 'center'));
$op = isset($_GET['op']) ? $_GET['op'] : "";
$objectTable->addIntroButton('addmenu', 'message.php?op=mod', _CO_IMMESSAGE_MESSAGE_COMPOSE);

$objectTable->addQuickSearch(array('menu_title',"menu_desc"));

//$objectTable->addCustomAction('getMenuItemListButton');

$objectTable->render();

include(ICMS_ROOT_PATH."/footer.php");