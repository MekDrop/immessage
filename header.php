<?php

include_once "../../mainfile.php";
include_once(dirname( __FILE__ ) . '/include/common.php');

if(!is_object($xoopsUser))
	redirect_header(ICMS_URL.'/index.php',3,_CO_IMMESSAGE_MESSAGE_NOTHING_TODO_HERE);
?>