<?php
@require_once("../include/functions.php");
@require_once("../include/domains.php");
@include_once("../_dbconnect/connection.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getFilter( $parameters ) {
	$domain = array();
	
	fConnDB();

	$type = $parameters["type"];
	
	//SEXO
	if ( $type == "X" ):
		$domain = array(
			array( "value" => "F", "label" => "FEMININO" ),
			array( "value" => "M", "label" => "MASCULINO" )
		);
	
	//DONS
	elseif ( $type == "D" ):
		$domain = getDonsDomain();
		
	//NOTAS DE DONS - NI-IGUAL, NA-MAIOR, NE-MENOR
	elseif ( $type == "NI" || $type == "NA" || $type == "NE" ):
		$domain = array();
		for ($i=1;$i<=24;$i++):
			$domain[] = array( "value" => $i, "label" => $i );
		endfor;

	//MINISTERIOS
	elseif ( $type == "M" ):
		$domain = getMinisteriosDomain();
		
	//MINISTERIOS
	elseif ( $type == "AM" ):
		$domain = getAreaMinisteriosDomain();
		
		//NOTAS DE MINISTERIOS - MI-IGUAL, MA-MAIOR, ME-MENOR
	elseif ( $type == "MI" || $type == "MA" || $type == "ME" ):
		$domain = array();
		for ($i=1;$i<=10;$i++):
			$domain[] = array( "value" => $i, "label" => $i );
		endfor;
		
		endif;

	return array( "result" => true, "domain" => $domain );
}
?>