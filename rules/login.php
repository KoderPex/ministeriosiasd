<?php
@include_once("../include/functions.php");
@include_once("../_dbconnect/connection.php");
@include_once("../_mailer/sendmail.php");
@include_once("testes.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function login( $parameters ) {
	unset($_SESSION);
	
	$usr = strtolower($parameters["username"]);
	$psw = strtolower($parameters["password"]);

	$arr = array();
	$arr['page'] = "";
	$arr['login'] = false;
	$arr['message'] = "Login inv&aacute;lido!";
	
	//Verificacao de Usuario/Senha
	if ( isset($usr) && !empty($usr) ):
		fConnDB();
		$result = $GLOBALS['conn']->Execute("SELECT * FROM CD_PESSOA WHERE cd_email = ?", Array( $usr ) );
		if (!$result->EOF):
			$ativo = $result->fields['fg_ativo'];

			//CADASTRO INATIVO
			if ( $ativo == 'N' ):
				$valido = $result->fields['cd_valido'];
			
				//FALTA DE VALIDACAO DO EMAIL
				if ( isset($valido) && !empty($valido) ):
					$arr['page'] = $GLOBALS['VirtualDir']."register.php?hint=$usr";
					$arr['login'] = true;
					
				//RECUPERACAO DE CADASTRO BLOQUEADO
				else:
					$arr['page'] = $GLOBALS['VirtualDir']."define.php?hint=$usr";
					$arr['login'] = true;
				endif;
			
			elseif ($ativo == 'S'):
				$idBD = $result->fields['id'];
				$tentBD = $result->fields['nr_tent'] + 1;
				$senhaBD = $result->fields['ds_senha'];
				
				//PESSOA JA CADASTRADA, BASTA PEDIR NOVA SENHA
				if ( is_null($senhaBD) || empty($senhaBD) ):
					$arr['page'] = $GLOBALS['VirtualDir']."define.php?hint=$usr";
					$arr['login'] = true;
				
				//ENTRAR NO DASHBOARD
				elseif ($senhaBD == $psw):
					$GLOBALS['conn']->Execute("UPDATE CD_PESSOA SET nr_tent = 0 WHERE id = ?", Array( $idBD ) );
					fVerificaTestes($idBD);
					fSetSessionLogin($result);
					$arr['page'] = $GLOBALS['VirtualDir']."dashboard.php";
					$arr['login'] = true;
					
				//SE ERROU A SENHA MAIS DO QUE 3 VEZES
				elseif ($tentBD > 3):
					$GLOBALS['conn']->Execute("UPDATE CD_PESSOA SET fg_ativo = 'N' WHERE id = ?", Array( $idBD ) );
					$arr['message'] = "Excedido numero de tentativas.<br/>Seu usuário foi bloqueado!";
				
				//SE ERROU A SENHA
				else:
					$GLOBALS['conn']->Execute("UPDATE CD_PESSOA SET nr_tent = ? WHERE id = ?", Array( $tentBD, $idBD ) );
				
				endif;
				
			endif;
			
		endif;
	endif;
	return $arr;
}

function logout() {
	session_start();
	session_destroy();
	unset($_SESSION);
	return array('logout' => true);
}

function register( $parameters ) {
	$usr = strtolower($parameters["username"]);
	$psw = strtolower($parameters["password"]);
	$cnf = strtolower($parameters["confirm"]);
	$nam = strtoupper($parameters["name"]);

	$arr = array();
	$arr['page'] = "";
	$arr['message'] = "Confira seus dados e tente novamente.";
	$arr['register'] = false;
		
	//Verificacao de Usuario/Senha
	if ( isset($usr) && !empty($usr) ):
		if ( $psw != $cnf ):
			$arr['message'] = "Senha e confirma&ccedil;&atilde;o da senha s&atilde;o diferentes.";
		else:
			fConnDB();
			$result = $GLOBALS['conn']->Execute("SELECT * FROM CD_PESSOA WHERE cd_email = ?",array($usr));
			
			//SE EXISTE CADASTRO
			if (!$result->EOF):
				$ativo = $result->fields['fg_ativo'];
				$idBD = $result->fields['id'];
				$nam = $result->fields['nm'];
				$senhaBD = $result->fields['ds_senha'];

				//CADASTRO INATIVO
				if ( $ativo == 'N' ):
					$arr = fSetRecover( $result, $arr );

				//CADASTRO ATIVO
				elseif ( $ativo == 'S' ):
				
					//NAO TEM SENHA NO BANCO
					if ( !empty($psw) && (is_null($senhaBD) || empty($senhaBD)) ):
						$GLOBALS['conn']->Execute("
							UPDATE CD_PESSOA SET
								ds_senha = ?,
								nr_tent = 0
							WHERE id = ?",
							Array( $psw, $idBD )
						);

						fSetVerificaPerfil($idBD);
						fVerificaTestes($idBD);
						fSetSessionLogin($result);
						$arr['page'] = $GLOBALS['VirtualDir']."dashboard.php";
						$arr['register'] = true;
					
					//TEM SENHA NO BANCO, MAS NAO LEMBRA A SENHA
					elseif ( empty($psw) ):
						$GLOBALS['conn']->Execute("
							UPDATE CD_PESSOA SET
								ds_senha = NULL,
								fg_ativo = 'N'
							WHERE id = ?",
							Array( $idBD )
						);
						$arr = fSetRecover( $result, $arr );
						
					endif;

				endif;	

			//SE CADASTRO NAO EXISTE
			else:
				$valido = md5($usr.$psw.strtotime("now"));
				$GLOBALS['mail']->ClearAllRecipients();
				$GLOBALS['mail']->AddAddress( $usr, $nam );
				$GLOBALS['mail']->Subject = "Bem vindo ao site ministeriosiasd.com.br";

				$linkValid = "http://www.ministeriosiasd.com.br/register.php?id=$valido";
				
				//Mensagem
				$body = "<html>";
				$body = "<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>";
				$body .= "<head>";
				$body .= "<title>Registro ministeriosiasd.com.br</title>";
				$body .= "</head>";
				$body .= "<body style=\"font-family:Arial;\">";
				$body .= utf8_decode("Caro(a) usuário(a),")."<br/>";
				$body .= "<br/>";
				$body .= utf8_decode("Esta mensagem refere-se a solicitação de registro no site ministeriosiasd.com.br.")."<br/>";
				$body .= "<br/>";
				$body .= utf8_decode("Desde já, agradecemos seu registro e esperamos que nosso site lhe ajude a encontrar seus dons, e que estes possam lhe guiar ao seu ministério, e sendo assim, que seu ministério possa ajudá-lo a ser feliz trabalhando para Deus.")."<br/>";
				$body .= "<br/>";
				$body .= utf8_decode("Para confirmar seu cadastro, acesse o endereço abaixo:")."<br/>";
				$body .= "<a href=\"$linkValid\">$linkValid</a>"."<br/>";
				$body .= "<br/>";
				$body .= utf8_decode("Um grande abraço de seu amigo,")."<br/>";
				$body .= "<br/>";
				$body .= utf8_decode("Pr. Orlando Jeronimo de Oliveira")."<br/>";
				$body .= utf8_decode("ministeriosiasd.com.br")."<br/>";
				$body .= "</body>";
				$body .= "</html>";
				$GLOBALS['mail']->MsgHTML($body);

				if ( $GLOBALS['mail']->Send() ):
					$GLOBALS['conn']->Execute("INSERT INTO CD_PESSOA (
						cd_email,
						ds_senha,
						nm,
						cd_valido,
						fg_ativo
					) VALUES(?,?,?,?,?)",Array( 
						$usr,
						$psw,
						$nam,
						$valido,
						'N'
					));

					$arr['page'] = $GLOBALS['VirtualDir']."register.php?hint=$usr";
					$arr['register'] = true;
				
				else:
					$arr['message'] = "Não foi possível validar seu email. Tente novamente mais tarde.";
					$arr['register'] = false;
				
				endif;
				
			endif;
		endif;

	endif;
	return $arr;
}

function fSetRecover( $result, $ret ) {
	$valido = md5( $result->fields["id"].$result->fields["ds_senha"].strtotime("now"));
	
	$GLOBALS['mail']->ClearAllRecipients();
	$GLOBALS['mail']->AddAddress( $result->fields["cd_email"], $result->fields["nm"] );
	$GLOBALS['mail']->Subject = "Bem vindo ao site ministeriosiasd.com.br";

	$linkValid = "http://www.ministeriosiasd.com.br/define.php?id=$valido";
	
	//Mensagem
	$body = "<html>";
	$body = "<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>";
	$body .= "<head>";
	$body .= "<title>Registro ministeriosiasd.com.br</title>";
	$body .= "</head>";
	$body .= "<body style=\"font-family:Arial;\">";
	$body .= utf8_decode("Caro(a) usuário(a),")."<br/>";
	$body .= "<br/>";
	$body .= utf8_decode("Esta mensagem refere-se a solicitação de recuperação e/ou ativação de sua conta no site ministeriosiasd.com.br.")."<br/>";
	$body .= "<br/>";
	$body .= utf8_decode("Desde já, agradecemos seu interesse em continuar conosco e esperamos que nosso site continue a lhe ajudar em sua caminhada cristã com seus dons e ministérios.")."<br/>";
	$body .= "<br/>";
	$body .= utf8_decode("Para confirmar seu pedido, acesse o endereço abaixo:")."<br/>";
	$body .= "<a href=\"$linkValid\">$linkValid</a>"."<br/>";
	$body .= "<br/>";
	$body .= utf8_decode("Um grande abraço de seu amigo,")."<br/>";
	$body .= "<br/>";
	$body .= utf8_decode("Pr. Orlando Jeronimo de Oliveira")."<br/>";
	$body .= utf8_decode("ministeriosiasd.com.br")."<br/>";
	$body .= "</body>";
	$body .= "</html>";
	$GLOBALS['mail']->MsgHTML($body);

	if ( $GLOBALS['mail']->Send() ):
		$GLOBALS['conn']->Execute("
			UPDATE CD_PESSOA SET
				cd_valido = ?
			WHERE id = ?",
			Array( $valido, $result->fields["id"] )
		);
		$ret['page'] = $GLOBALS['VirtualDir']."register.php?hint=".$result->fields["cd_email"];
		$ret['register'] = true;
	
	else:
		$ret['message'] = "Não foi possível validar seu email. Tente novamente mais tarde.";
		$ret['register'] = false;
	
	endif;
	return $ret;
}
?>