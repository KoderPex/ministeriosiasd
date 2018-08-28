<?php
@include_once("../include/functions.php");
@include_once("testes.php");
responseMethod();

function painel() {
	session_start();
	
	
	$arr = array();

	//SE EXISTE TESTE DE DONS PENDENTE
	$rs = fRetornaTesteDonsQuantidades( $_SESSION['PESSOA']['id'] );
	$qtdDons = 0;
	if ( $rs["nr_rsp"] > 0 ):
		$qtdDons++;
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-themesecondary",
			"pc" => floor( $rs["nr_rsp"] / $rs["nr_qst"] ),
			"qt" => 1,
			"ds" => utf8_encode(utf8_decode("MEU TESTE DE DONS PENDENTE")),
			"rightBkTheme" => "databox-number themesecondary",
			"rightDecorate" => "databox-stat themesecondary radius-bordered",
			"ico" => "stat-icon icon-lg fa fa-tasks"
		);
	endif;
	
	//SE EXISTE TESTE DE DONS CONCLUIDOS
	$rs = fExistHistorico( $_SESSION['PESSOA']['id'], 'D' );
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-palegreen",
			"pc" => floor( ( $rs->RecordCount() / ( $rs->RecordCount() + $qtdDons ) ) * 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => utf8_encode(utf8_decode("MEUS TESTES DE DONS CONCLUÍDOS")),
			"rightBkTheme" => "databox-number green",
			"rightDecorate" => "databox-stat bg-palegreen radius-bordered",
			"ico" => "stat-icon fa fa-check"
		);
	endif;
	
	//SE EXISTE TESTE DE MINISTERIOS PENDENTE
	$rs = fRetornaTesteMinisteriosQuantidades( $_SESSION['PESSOA']['id'] );
	$qtdMinis = 0;
	if ( $rs["nr_rsp"] > 0 ):
		$qtdMinis++;
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-themesecondary",
			"pc" => 0,
			"qt" => 1,
			"ds" => utf8_encode(utf8_decode("MEU TESTE DE MINISTÉRIOS PENDENTE")),
			"rightBkTheme" => "databox-number themesecondary",
			"rightDecorate" => "databox-stat themesecondary radius-bordered",
			"ico" => "stat-icon icon-lg fa fa-tasks"
		);
	endif;

	//SE EXISTE TESTE DE MINISTERIOS CONCLUIDOS
	$rs = fExistHistorico( $_SESSION['PESSOA']['id'], 'M' );
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-palegreen",
			"pc" => floor( ( $rs->RecordCount() / ( $rs->RecordCount() + $qtdMinis ) ) * 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => utf8_encode(utf8_decode("MEUS TESTES DE MINISTÉRIOS CONCLUÍDOS")),
			"rightBkTheme" => "databox-number green",
			"rightDecorate" => "databox-stat bg-palegreen radius-bordered",
			"ico" => "stat-icon fa fa-check"
		);
	endif;
	
	//NUMERO DE PESSOAS CADASTRADAS
	$rs = CONN::get()->Execute("SELECT COUNT(*) AS qt FROM CD_PESSOA");
	$nrUsr = 0;
	if (!$rs->EOF):
		$nrUsr = $rs->fields["qt"];
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-themeprimary",
			"pc" => 100,
			"qt" => $nrUsr,
			"ds" => utf8_encode(utf8_decode("PESSOAS CADASTRADAS")),
			"rightBkTheme" => "databox-number themeprimary",
			"rightDecorate" => "databox-state bg-themeprimary",
			"ico" => "fa fa-users"
		);
	endif;

	//NUMERO DE PESSOAS COM RESULTADO DE DONS
	$rs = CONN::get()->Execute("SELECT DISTINCT id_cd_pessoa FROM HS_RESULTADO WHERE tp = ?", array('D') );
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-palegreen",
			"pc" => min( ceil( ( $rs->RecordCount() / $nrUsr ) * 100 ), 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => utf8_encode(utf8_decode("PESSOAS COM RESULTADO DE DONS")),
			"rightBkTheme" => "databox-number green",
			"rightDecorate" => "databox-stat bg-palegreen radius-bordered",
			"ico" => "stat-icon fa fa-check"
		);
	endif;

	//NUMERO DE PESSOAS COM RESULTADO DE MINISTERIOS
	$rs = CONN::get()->Execute("SELECT DISTINCT id_cd_pessoa FROM HS_RESULTADO WHERE tp = ?", array('M') );
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-palegreen",
			"pc" => min( ceil( ( $rs->RecordCount() / $nrUsr ) * 100 ), 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => utf8_encode(utf8_decode("PESSOAS COM RESULTADO DE MINISTÉRIOS")),
			"rightBkTheme" => "databox-number green",
			"rightDecorate" => "databox-stat bg-palegreen radius-bordered",
			"ico" => "stat-icon fa fa-check"
		);
	endif;

	//TESTES DE DONS CONCLUÍDOS
	$rs = CONN::get()->Execute("SELECT * FROM HS_RESULTADO WHERE tp = ?", array('D') );
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-palegreen",
			"pc" => min( ceil( ( $rs->RecordCount() / $nrUsr ) * 100 ), 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => utf8_encode(utf8_decode("TESTES DE DONS CONCLUÍDOS")),
			"rightBkTheme" => "databox-number green",
			"rightDecorate" => "databox-stat bg-palegreen radius-bordered",
			"ico" => "stat-icon fa fa-check"
		);
	endif;

	//TESTES DE MINISTÉRIOS CONCLUÍDOS
	$rs = CONN::get()->Execute("SELECT * FROM HS_RESULTADO WHERE tp = ?", array('M') );
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-palegreen",
			"pc" => min( ceil( ( $rs->RecordCount() / $nrUsr ) * 100 ), 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => utf8_encode(utf8_decode("TESTES DE MINISTÉRIOS CONCLUÍDOS")),
			"rightBkTheme" => "databox-number green",
			"rightDecorate" => "databox-stat bg-palegreen radius-bordered",
			"ico" => "stat-icon fa fa-check"
		);
	endif;

	//NUMERO DE PESSOAS COM TESTES PENDENTES
	$rs = CONN::get()->Execute("SELECT DISTINCT 
		* FROM (SELECT id_cd_pessoa FROM RP_DONS UNION SELECT id_cd_pessoa FROM RP_MINISTERIOS) A");
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-themethirdcolor",
			"pc" => min( ceil( ( $rs->RecordCount() / $nrUsr ) * 100 ), 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => utf8_encode(utf8_decode("PESSOAS COM TESTES PENDENTES")),
			"rightBkTheme" => "databox-number themethirdcolor",
			"rightDecorate" => "databox-stat themethirdcolor radius-bordered",
			"ico" => "stat-icon icon-lg fa fa-exclamation-circle"
		);
	endif;

	//NUMERO DE PESSOAS SEM TESTES
	$rs = CONN::get()->Execute("SELECT * FROM CD_PESSOA WHERE id NOT IN (SELECT id_cd_pessoa FROM HS_RESULTADO)");
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-themesecondary",
			"pc" => min( ceil( ( $rs->RecordCount() / $nrUsr ) * 100 ), 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => utf8_encode(utf8_decode("PESSOAS SEM NENHUM TESTE")),
			"rightBkTheme" => "databox-number themesecondary",
			"rightDecorate" => "databox-stat themesecondary radius-bordered",
			"ico" => "stat-icon icon-lg fa fa-question-circle"
		);
	endif;

	return array( "panels" => $arr );
}
?>