<?php
@include_once("../include/functions.php");
@include_once("testes.php");
responseMethod();

function getPeople() {
	$arr = array();

	
	$result = CONN::get()->Execute( "
		SELECT p.id, p.nm, p.cd_email, crd.id AS id_rd, crm.id AS id_rm
		  FROM CD_PESSOA p
	 LEFT JOIN CON_RESULTADO_LAST crd ON (crd.id_cd_pessoa = p.id AND crd.tp = 'D')
	 LEFT JOIN CON_RESULTADO_LAST crm ON (crm.id_cd_pessoa = p.id AND crm.tp = 'M')
	  ORDER BY p.nm
	");
	foreach ($result as $k => $fields):
		$arr[] = array(
			"id" => $fields["id"],
			"nm" => $fields["nm"],
			"em" => $fields["cd_email"],
			"rd" => $fields["id_rd"],
			"rm" => $fields["id_rm"]
		);
	endforeach;

	return array( "result" => true, "people" => $arr );
}

function updateMember( $parameters ) {
	$arr = array();
	$arr["result"] = false;
	
	
	
	$id = $parameters["id"];
	$vl = $parameters["val"];
	$tf = explode("-",$parameters["field"]);
	
	$table = mb_strtoupper($tf[0]);
	$field = mb_strtoupper($tf[1]);
	
	$str = "UPDATE $table SET $field = ? WHERE ID = ?";
	
	CONN::get()->Execute( $str, array( fReturnStringNull( $vl ), $id ) );
	
	$arr["result"] = true;
	return $arr;
}

function insertMember( $parameters ) {
	$arr = array();
	$arr["result"] = false;
	
	if ( isset($parameters["id"]) && $parameters["id"] == "Novo" ):
		if (isset($parameters["nm"])):
			
			CONN::get()->Execute("
				INSERT INTO CD_PESSOA(
					NM
				) VALUES (
					?
				)
			", array( $parameters["nm"] ) );
			$id = CONN::get()->Insert_ID();
			return getMember( array( "id" => $id ) );
		endif;
	endif;
	return $arr;
}

function getMember( $parameters ) {
	$arr = array();
	$arr["result"] = false;

	

	$result = CONN::get()->Execute("
		SELECT  *
		  FROM CD_PESSOA
		 WHERE ID = ?
	", Array( $parameters["id"] ) );
	if (!$result->EOF):
		$arr["result"] = true;
		
		$arr["membro"] = array(
			"cd_pessoa-id"		=> $result->fields['id'],
			"cd_pessoa-nm"		=> trim($result->fields['nm']),
			"cd_pessoa-cd_email"	=> trim($result->fields['cd_email'])
		);
	endif;
	$arr["testes"] = fVerificaTestes($parameters["id"]);
	return $arr;
}
?>