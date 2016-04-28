<?php

function isExternalPage($page) {
	switch($page) {
		case "login":
		case "registration":
			return true;
			break;
		default:
			return false;
			break;
	}
}

function randString($length = 8) {
  return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

function clearFormDisplay(&$arr) {
  foreach ($arr as $a) {
    $a = "";
  }
}

?>