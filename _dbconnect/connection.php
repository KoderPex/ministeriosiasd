<?php
require_once("adodb5/adodb.inc.php");

global $conn, $DBType, $DBServerHost, $DBUser, $DBPassWord, $DBDataBase, $DBRegras;
@require_once($_SERVER["SERVER_ADDR"]."_".$_SERVER["SERVER_NAME"].".ini.php");

function fConnDB(){
	try{
		$GLOBALS['conn'] = ADONewConnection($GLOBALS['DBType']);
		$GLOBALS['conn']->SetCharSet('utf8');
		$GLOBALS['conn']->Connect($GLOBALS['DBServerHost'],$GLOBALS['DBUser'],$GLOBALS['DBPassWord'],$GLOBALS['DBDataBase']);
		$GLOBALS['conn']->SetFetchMode(ADODB_FETCH_ASSOC);
		return true;
	}catch (Exception $e){
		return false;
	}
}
?>