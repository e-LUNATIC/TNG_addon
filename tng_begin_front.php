<?php
include("begin.php");

include($cms['tngpath'] . "genlib.php");
include($cms['tngpath'] . "getlang.php");
include($cms['tngpath'] . "$mylanguage/text.php");

include($cms['tngpath'] . "tngdblib.php");

if((strpos($_SERVER['SCRIPT_NAME'],"/changelanguage.php") === FALSE && (strpos($_SERVER['SCRIPT_NAME'],"/suggest.php") === FALSE || $enttype)) || $_SESSION['currentuser'])
	include($cms['tngpath'] . "");

include($cms['tngpath'] . "log.php");
?>