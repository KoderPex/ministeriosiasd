<?php
function getDomain($table){
	$arr = array();
	$result = $GLOBALS['conn']->Execute("
		SELECT id, ds 
		  FROM $table 
	  ORDER BY ds
	");
	foreach ($result as $k => $fields):
		$arr[] = array(
				"value"	=> $fields["id"],
				"label"	=> utf8_encode($fields["ds"])
		);
	endforeach;
	return $arr;
}
?>