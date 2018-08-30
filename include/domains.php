<?php
function getDomain($table){
	$arr = array();
	$result = CONN::get()->Execute("
		SELECT id, ds, cd
		  FROM $table 
	  ORDER BY ds
	");
	foreach ($result as $k => $fields):
		$arr[] = array(
				"value"	=> $fields["id"],
				"label"	=> utf8_encode($fields["ds"]),
				"sub" => $fields["cd"]
		);
	endforeach;
	return $arr;
}
?>