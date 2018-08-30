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
				"label"	=> $fields["ds"],
				"sub" => $fields["cd"]
		);
	endforeach;
	return $arr;
}
?>