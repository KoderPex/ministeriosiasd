<?php
@include_once("../include/functions.php");
responseMethod();

function getQueryByFilterGifts( $parameters ) {
	$where = "";
	$aWhere = array("D");
	if ( isset($parameters["filters"]) ):
		$keyAnt = "";
		foreach ($parameters["filters"] as $key => $v):
			$not = false;
			if ( isset($v["fg"]) ):
				$not = strtolower($v["fg"]) == "true";
			endif;
			$notStr = ( $not ? "NOT " : "" );
			if ( $key == "D" ):
				$where .= " AND cd.ID ".$notStr."IN";
			elseif ( $key == "NI" ):
				$where .= " AND hri.nr_item ".$notStr."IN";
			elseif ( $key == "NA" ):
				$where .= " AND hri.nr_item > ";
			elseif ( $key == "NE" ):
				$where .= " AND hri.nr_item < ";
			else:
				$where .= " AND";
			endif;

			$prim = true;
			$where .= " (";
			if ( is_array( $v["vl"] ) ):
				foreach ($v["vl"] as $value):
					if ( empty($value) ):
						$aWhere[] = "NULL";
						$where .= (!$prim ? "," : "" )."?";
					else:
						$aWhere[] = $value;
						$where .= (!$prim ? "," : "" )."?";
					endif;
					$prim = false;
				endforeach;
			elseif ( empty($v["vl"]) ):
				$aWhere[] = "NULL";
				$where .= (!$prim ? "," : "" )."?";
			else:
				$aWhere[] = $v["vl"];
				$where .= (!$prim ? "," : "" )."?";
			endif;
			$where .= ")";
		endforeach;
	endif;

	//print_r($aWhere);
	//exit($where);

	if (!empty($where)):
		$query = "
		SELECT hri.nr_item, cr.nm, cd.ds, cd.cd
		FROM CON_RESULTADO cr
		INNER JOIN HS_RESULT_ITEM hri ON (hri.id_hs_resultado = cr.id)
		INNER JOIN CON_CD_DONS cd ON (cd.id = hri.id_origem)  
		WHERE cr.TP = ? $where
		";
		return CONN::get()->Execute( $query, $aWhere );
	endif;
	return null;
}

function getDons( $parameters ) {
	$arr = array();
	$result = getQueryByFilterGifts( $parameters );
	if (!is_null($result)):
		foreach ($result as $k => $fields):
			$arr[] = array(
					"nm" => utf8_encode($fields["nm"]),
					"cd" => $fields["cd"],
					"dm" => utf8_encode($fields["ds"]),
					"nt" => $fields["nr_item"]
			);
		endforeach;
	endif;
	return array( "result" => true, "dons" => $arr );
}

function getQueryByFilterMinisters( $parameters ) {
	$where = "";
	$aWhere = array("M");
	if ( isset($parameters["filters"]) ):
		$keyAnt = "";
		foreach ($parameters["filters"] as $key => $v):
			$not = false;
			if ( isset($v["fg"]) ):
				$not = strtolower($v["fg"]) == "true";
			endif;
			$notStr = ( $not ? "NOT " : "" );
			if ( $key == "M" ):
				$where .= " AND cm.ID ".$notStr."IN";
			elseif ( $key == "MI" ):
				$where .= " AND hri.nr_item ".$notStr."IN";
			elseif ( $key == "MA" ):
				$where .= " AND hri.nr_item > ";
			elseif ( $key == "ME" ):
				$where .= " AND hri.nr_item < ";
			else:
				$where .= " AND";
			endif;

			$prim = true;
			$where .= " (";
			if ( is_array( $v["vl"] ) ):
				foreach ($v["vl"] as $value):
					if ( empty($value) ):
						$aWhere[] = "NULL";
						$where .= (!$prim ? "," : "" )."?";
					else:
						$aWhere[] = $value;
						$where .= (!$prim ? "," : "" )."?";
					endif;
					$prim = false;
				endforeach;
			elseif ( empty($v["vl"]) ):
				$aWhere[] = "NULL";
				$where .= (!$prim ? "," : "" )."?";
			else:
				$aWhere[] = $v["vl"];
				$where .= (!$prim ? "," : "" )."?";
			endif;
			$where .= ")";
		endforeach;
	endif;

	//print_r($aWhere);
	//exit($where);

	if (!empty($where)):
		$query = "
		SELECT hri.nr_item, cr.nm, cm.ds, cm.cd
		FROM CON_RESULTADO cr
		INNER JOIN HS_RESULT_ITEM hri ON (hri.id_hs_resultado = cr.id)
		INNER JOIN CON_CD_MINISTERIOS cm ON (cm.id = hri.id_origem)  
		WHERE cr.TP = ? $where
		";
		return CONN::get()->Execute( $query, $aWhere );
	endif;
	return null;
}

function getMinisterios( $parameters ) {
	$arr = array();
	$result = getQueryByFilterMinisters( $parameters );
	if (!is_null($result)):
		foreach ($result as $k => $fields):
			$arr[] = array(
					"nm" => utf8_encode($fields["nm"]),
					"cd" => $fields["cd"],
					"mn" => utf8_encode($fields["ds"]),
					"nt" => $fields["nr_item"]
			);
		endforeach;
	endif;
	return array( "result" => true, "ministers" => $arr );
}
?>