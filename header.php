<?php

include_once "../../mainfile.php";
include_once(__DIR__ . '/include/common.php');

if(!is_object(\icms::$user)) {
	redirect_header(ICMS_URL . '/index.php', 3, _CO_IMMESSAGE_MESSAGE_NOTHING_TODO_HERE);
}