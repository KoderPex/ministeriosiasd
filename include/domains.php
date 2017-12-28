<?php
function getDonsDomain(){
	$arr = array();
	$result = $GLOBALS['conn']->Execute("SELECT id, ds_dom FROM CON_CD_DONS ORDER BY ds_dom");
	foreach ($result as $k => $fields):
		$arr[] = array(
				"value"	=> $fields["id"],
				"label"	=> utf8_encode($fields["ds_dom"])
		);
	endforeach;
	return $arr;
}

function getAreaMinisteriosDomain(){
	$arr = array();
	$result = $GLOBALS['conn']->Execute("SELECT id, ds FROM CD_MINISTERIOS_GP ORDER BY ds");
	foreach ($result as $k => $fields):
	$arr[] = array(
			"value"	=> $fields["id"],
			"label"	=> utf8_encode($fields["ds"])
	);
	endforeach;
	return $arr;
}

function getMinisteriosDomain(){
	$arr = array();
	$result = $GLOBALS['conn']->Execute("SELECT id, ds FROM CON_CD_MINISTERIOS ORDER BY ds");
	foreach ($result as $k => $fields):
	$arr[] = array(
			"value"	=> $fields["id"],
			"label"	=> utf8_encode($fields["ds"])
	);
	endforeach;
	return $arr;
}
?>