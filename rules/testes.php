<?php
function fExistHistorico($id,$tp){
	return CONN::get()->Execute("SELECT * FROM HS_RESULTADO WHERE tp = ? AND id_cd_pessoa = ? ORDER BY dh_conclusao DESC", array($tp,$id) );
}

function fQueryResult($id){
	return CONN::get()->Execute("
	    SELECT p.cd_email, p.nm, r.dh_conclusao, r.dh_fim_validade, r.tp, i.id_origem, i.ds_item, i.nr_item, i.cd_origem
	      FROM HS_RESULTADO r
	INNER JOIN HS_RESULT_ITEM i ON (i.id_hs_resultado = r.id)
	INNER JOIN CD_PESSOA p ON (p.id = r.id_cd_pessoa)
	     WHERE r.id = ?
	  ORDER BY i.nr_item DESC, i.ds_item
	", array($id) );
}

function fRetornaTesteDonsQuantidades($id){
	$arr = array( "nr_qst" => 0, "nr_rsp" => 0, "pc_conc" => 0 );

	$qtds = CONN::get()->Execute("
	    SELECT 
	    (SELECT COUNT(*) FROM CON_QS_DONS) AS nr_qst, 
	    (SELECT COUNT(*) FROM RP_DONS WHERE id_cd_pessoa = ?) AS nr_rsp
	", array($id) );
	if (!$qtds->EOF):
		$arr["nr_qst"] = $qtds->fields['nr_qst'];
		$arr["nr_rsp"] = $qtds->fields['nr_rsp'];
		$arr["pc_conc"] = floor( ( $arr["nr_rsp"] / $arr["nr_qst"] ) * 100 );
	endif;
	return $arr;
}

function fRetornaTesteMinisteriosQuantidades($id){
	$arr = array( "nr_qst" => 0, "nr_rsp" => 0 );

	$qtds = CONN::get()->Execute("
	    SELECT 
	    (SELECT COUNT(*) FROM CON_CD_MINISTERIOS) AS nr_qst, 
	    (SELECT COUNT(*) FROM RP_MINISTERIOS WHERE id_cd_pessoa = ?) AS nr_rsp
	", array($id) );
	if (!$qtds->EOF):
		$arr["nr_qst"] = $qtds->fields['nr_qst'];
		$arr["nr_rsp"] = $qtds->fields['nr_rsp'];
	endif;
	return $arr;
}

function fCalculaValidade( $chave, $dhBaseCalculo ){
	$result = CONN::get()->Execute("SELECT * FROM TB_REGRAS WHERE ch = ? AND fg = 'S'", array("$chave|PZ_VALIDADE") );
	if (!$result->EOF):
		$retorno = new DateTime( strftime("%F %T", strtotime($dhBaseCalculo) ) );
		$vls =  explode(":", $result->fields['vl'] );
		if ( $vls[0] == "ANUAL" ):
			$retorno->modify("+".$vls[1]." year");
		endif;
		return $retorno->format('Y-m-d H:i:s');
	endif;
	return null;
}

function legendaDisposicao($nota){
	return $nota;
	// if ($nota >= 8):
	// 	return "SIM";
	// elseif ($nota >= 4):
	// 	return "TALVEZ";
	// endif;
	// return "NÃO";
}

function fVerificaTestes($id){
	$arr = array();

	//SE NAO TEM QUESTIONARIO DE DONS EM ABERTO.
	$dons = fRetornaTesteDonsQuantidades($id);
	if ( $dons["nr_rsp"] == 0 ):
	
		//SE PASSOU DO PRAZO DE VALIDADE, ABRE AUTOMATICAMENTE NOVO TESTE.
		$result = CONN::get()->Execute("SELECT 1 FROM HS_RESULTADO WHERE id_cd_pessoa = ? AND dh_fim_validade > NOW() AND tp = ?", array( $id, 'D' ) );
		if ($result ->EOF):
			CONN::get()->Execute("INSERT INTO RP_DONS(id_cd_pessoa, id_qs_dons) VALUES (?,?) ", array($id,1) );
			$arr["dons"] = fRetornaTesteDonsQuantidades($id);
		endif;
	else:
		$arr["dons"] = $dons;
	endif;

	//SE NAO TEM QUESTIONARIO DE MINISTERIOS EM ABERTO.
	$minis = fRetornaTesteMinisteriosQuantidades($id);
	if ( $minis["nr_rsp"] == 0 ):
		//SE PASSOU DO PRAZO DE VALIDADE, ABRE AUTOMATICAMENTE NOVO TESTE.
		$result = CONN::get()->Execute("SELECT 1 FROM HS_RESULTADO WHERE id_cd_pessoa = ? AND dh_fim_validade > NOW() AND tp = ?", array( $id, 'M' ) );
		if ($result ->EOF):
			CONN::get()->Execute("INSERT INTO RP_MINISTERIOS(id_cd_pessoa, id_cd_ministerios) VALUES (?,?) ", array($id,1) );
			$arr["minis"] = fRetornaTesteMinisteriosQuantidades($id);
		endif;
	else:
		$arr["minis"] = $minis;
	endif;
	
	return $arr;
}
?>