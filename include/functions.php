<?php
ini_set('memory_limit','200M');
error_reporting(E_ALL & ~ E_NOTICE & ~ E_DEPRECATED);
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');

global $VirtualDir;
@require_once("_adodb5/adodb.inc.php");
@require_once("_virtualpath.php");
@require_once("entity.php");

function responseMethod(){
	error_reporting(E_ALL & ~ E_NOTICE); //& ~ E_DEPRECATED
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
    header('Content-type: application/json');
	// Getting the json data from the request
	$response = '';

	$json_data = json_decode( json_encode( empty($_POST) ? $_GET : $_POST ) );
	// Checking if the data is null..
	if ( is_null( $json_data ) ):
		$response = json_encode( array( "status" => -1, "message" => "Insufficient paramaters!") );
	elseif ( empty( $json_data->{'MethodName'} ) ):
		$response = json_encode( array( "status" => 0, "message" => "Invalid function name!" ) );
	else:
		$methodName = $json_data->MethodName;
		if ( isset( $json_data->{'data'} ) ):
			$response = $methodName( objectToArray( $json_data->{'data'} ) );
		else:
			$response = $methodName();
		endif;
	endif;
	echo json_encode($response);
}

function fRequest($pVar){
	if (isset($_GET[$pVar])) return $_GET[$pVar];
	if (isset($_POST[$pVar])) return $_POST[$pVar];
	return "";
}

function verificaLogin() {
	session_start();
	$temPerfil = isset($_SESSION['PESSOA']['ssid']);
	if (!$temPerfil):
		session_destroy();
		header("Location: ".$GLOBALS['VirtualDir']."index.php");
		exit;
	endif;
}

function fSetSessionLogin( $result ){
	session_start();
	$_SESSION['PESSOA']['ssid'] = session_id();
	$_SESSION['PESSOA']['cd_email'] = $result->fields['cd_email'];
	$_SESSION['PESSOA']['id'] = $result->fields['id'];
}

function fGetPerfil( $cd = NULL ) {
	$arr = array();
	$query = "SELECT DISTINCT td.id, td.cd, td.ds_icon, td.ds_menu, td.ds_url
			  FROM CD_PESSOA_PERFIL cpp
		INNER JOIN TB_PERFIL_ITEM tpi ON ( tpi.id_tb_perfil = cpp.id_tb_perfil ) 
		INNER JOIN TB_DASHBOARD td ON ( td.id = tpi.id_tb_dashboard ) 
			 WHERE cpp.id_cd_pessoa = ?";
	if ( isset($cd) && !empty($cd) ):
		$query .= " AND td.cd LIKE '$cd.%'";
	else:
		$query .= " AND LENGTH(td.cd) = 2";
	endif;
	$query .= " ORDER BY td.cd";
	$result = CONN::get()->Execute($query, array($_SESSION['PESSOA']['id']) );
	while (!$result->EOF):
		$child = fGetPerfil( $result->fields['cd'] );
		$arr[ $result->fields['id'] ] = array( 
			"opt"	 => $result->fields['ds_menu'],
			"ico"	 => $result->fields['ds_icon'],
			"url"	 => $result->fields['ds_url'],
			"active" => false,
			"child"  => $child
		);
		$result->MoveNext();
	endwhile;
	return $arr;
}

function fSetVerificaPerfil( $id_cd_pessoa ) {
	//VERIFICA SE TEM AO MENOS UM PERFIL, SE NAO INSERE PERFIL BASICO 0-GUEST.
	$resperf = CONN::get()->Execute("SELECT * FROM CD_PESSOA_PERFIL WHERE id_cd_pessoa = ?", Array( $id_cd_pessoa ) );
	if ($resperf->EOF):
		CONN::get()->Execute("
			INSERT INTO CD_PESSOA_PERFIL(
				id_cd_pessoa,
				id_tb_perfil
			) VALUES (
				?,
				?
			)",
			Array( $id_cd_pessoa, 0 )
		);
	endif;
}

function array_msort($array, $cols){
	$colarr = array();
	foreach ($cols as $col => $order) {
		$colarr[$col] = array();
		foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
	}
	$eval = 'array_multisort(';
	foreach ($cols as $col => $order) {
		$eval .= '$colarr[\''.$col.'\'],'.$order.',';
	}
	$eval = substr($eval,0,-1).');';
	eval($eval);
	$ret = array();
	foreach ($colarr as $col => $arr) {
		foreach ($arr as $k => $v) {
			$k = substr($k,1);
			if (!isset($ret[$k])) $ret[$k] = $array[$k];
			$ret[$k][$col] = $array[$k][$col];
		}
	}
	return $ret;
}

function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}

function fReturnStringNull($s,$default = null){
	if ( isset($s) && trim($s) !== "" ):
	return utf8_decode($s);
	endif;
	return $default;
}

function fReturnNumberNull($n,$default = null){
	if ( isset($n) && is_numeric($n) ):
	return $n;
	endif;
	return $default;
}

function getDateNull($vl){
	if ( !isset($vl) || empty($vl) || is_null($vl) ):
	return null;
	endif;
	return fStrToDate($vl,"Y-m-d");
}

function fDataFilters($param){
	$strFilter = "<div class=\"col-xs-8\" id=\"divFilters\" filter-to=\"".$param["filterTo"]."\"></div>";
	$strFilter .= "<div class=\"input-group col-xs-4 pull-right\">";
	$strFilter .= "<select class=\"selectpicker form-control input-sm\" id=\"addFilter\" onchange=\"jsFilter.addFilter(this);\" data-width=\"100%\" title=\"Adicionar filtros\" data-width=\"auto\" data-container=\"body\">";
	$arr = array_msort( $param["filters"], array('label' => SORT_ASC) );
	foreach ($arr as $key => $value):
		$strFilter .= "<option value=\"".$value["value"]."\"";
		if (isset($value["unique"])):
			$strFilter .= " data-tokens=\"unique\"";
		endif;
		$strFilter .= ">";
		$strFilter .= $value["label"]."</option>";
	endforeach;
	$strFilter .= "</select>";
	$strFilter .= "</div>";
	$strFilter .= "<div class=\"form-group col-xs-12\"><a role=\"button\" class=\"btn btn-info btn-sm\" id=\"applyFilter\" style=\"color:#ffffff;display:none\" onclick=\"jsFilter.apply();\"><i class=\"glyphicon glyphicon-cog\"></i>&nbsp;Aplicar Filtro</a></div>";
	$strFilter .= "<br/>";
	echo $strFilter;
}
?>