<?php
@include_once("include/functions.php");
@require_once("_dbconnect/connection.php");

$id = fRequest("id");
$hint = fRequest("hint");

//INSTRUCAO PARA VALIDAR O EMAIL
if ( $hint != "" ):
	fConnDB();
	$result = $GLOBALS['conn']->Execute("SELECT * FROM CD_PESSOA WHERE cd_valido IS NOT NULL AND fg_ativo = 'N' AND cd_email = ?", Array( $hint ) );
	if ($result->EOF):
		header("location:".$GLOBALS['VirtualDir']."login.php");
		exit;
	else:
		$nome = ucwords(mb_strtolower($result->fields['nm']));
	endif;


//LINK POR EMAIL
elseif ( $id != "" ):
	@include_once("rules/testes.php");
	fConnDB();
	$result = $GLOBALS['conn']->Execute("SELECT * FROM CD_PESSOA WHERE cd_valido = ? AND fg_ativo = 'N'", Array( $id ) );
	if ($result->EOF):
		header("location:".$GLOBALS['VirtualDir']."login.php");
		exit;
	else:
		$nome = ucwords(mb_strtolower($result->fields['nm']));
		$id_cd_pessoa = $result->fields['id'];
		
		//ATUALIZA USUARIO, VALIDANDO DADOS.
		$GLOBALS['conn']->Execute("
			UPDATE CD_PESSOA SET
				fg_ativo = 'S',
				cd_valido = null,
				nr_tent = 0
			WHERE id = ?",
			Array( $id_cd_pessoa )
		);

		fSetVerificaPerfil($id_cd_pessoa);
		fVerificaTestes($id_cd_pessoa);
		fSetSessionLogin($result);
	endif;
endif;
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" ng-app="angular-app">
<!--Head-->
<head>
    <meta charset="utf-8"/>
    <title>Minist&eacute;rios IASD - Regisre-se</title>
    <meta name="description" content="Dashboard" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="<?php echo $GLOBALS['VirtualDir'];?>img/logo.png" type="image/x-icon">
    <link href="<?php echo $GLOBALS['VirtualDir'];?>assets/css/bootstrap.min.css" rel="stylesheet" />
    <link id="bootstrap-rtl-link" href="" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['VirtualDir'];?>assets/css/font-awesome.min.css" rel="stylesheet" />
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,600,700,300" rel="stylesheet" type="text/css">
    <link id="beyond-link" href="<?php echo $GLOBALS['VirtualDir'];?>assets/css/beyond.min.css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['VirtualDir'];?>assets/css/demo.min.css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['VirtualDir'];?>assets/css/animate.min.css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['VirtualDir'];?>assets/css/bootstrap-select.min.css" rel="stylesheet" />
    <link id="skin-link" href="" rel="stylesheet" type="text/css" />
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/skins.min.js"></script>
</head>
<!--Head Ends-->
<!--Body-->
<body>
	<?php 
	//AVISO DE QUE O LOGIN FOI ACEITO.
	if ( $id != ""):
	?>
    <div class="login-container animated fadeInDown">
		<div class="col-lg-12">
			<div class="well well-lg with-header with-footer">
				<div class="header bordered-palegreen">Boas vindas!</div>
				<p>
					<b><?php echo $nome;?></b>,<br/>
					<br/>
					Estou muito feliz porque voc&ecirc; completou a etapa de valida&ccedil;&atilde;o de seu cadastro e agora est&aacute; muito perto de descobrir seus dons e minist&eacute;rios.<br/>
					<br/>
					Minha intenção &eacute; que esta ferramenta possa lher ajudar a encontrar seus dons, encorajando-o a us&aacute;-los para desenvolver seu minist&eacute;rio e melhor servir ao Senhor.<br/>
					<br/>
					Com muito carinho fraternal, de seu amigo,<br/>
					<br/>
					Pr. Orlando Jer&ocirc;nimo de Oliveira<br/>
					ministeriosiasd.com.br
				</p>
				<br/>
				<div class="footer">
					<img src="<?php echo $GLOBALS['VirtualDir'];?>img/logo.png" width="80px" height="43px"/>
					<a href="<?php echo $GLOBALS['VirtualDir'];?>dashboard.php" class="btn btn-labeled btn-palegreen pull-right">
						<i class="btn-label glyphicon glyphicon-ok"></i>Entrar agora
					</a>
				</div>
			</div>
		</div>	
    </div>
	<?php 

	//AVISO DO SITE DE QUE FOI ENVIADO UM EMAIL
	elseif ( $hint != "" ):
	?>
    <div class="login-container animated fadeInDown">
		<div class="col-lg-12">
			<div class="well well-lg with-header with-footer">
				<div class="header bordered-palegreen">Valide seu cadastro</div>
				<p>
					<b><?php echo $nome;?></b>,<br/>
					<br/>
					Agora para validar seu cadastro, voc&ecirc; deve acessar seu email <u></i>(<?php echo $hint;?>)</i></u> e clicar no link que estar&aacute; na mensagem que enviamos pra voc&ecirc;.<br/>
					<br/>
					Fico no aguardo,<br/>
					<br/>
					Pr. Orlando Jer&ocirc;nimo de Oliveira<br/>
					ministeriosiasd.com.br
				</p>
				<br/>
				<div class="footer">
					<img src="<?php echo $GLOBALS['VirtualDir'];?>img/logo.png" width="80px" height="43px"/>
					<a href="<?php echo $GLOBALS['VirtualDir'];?>login.php" class="btn btn-labeled btn-blue pull-right">
						<i class="btn-label fa fa-exclamation"></i>Ir para o Login
					</a>					
				</div>
			</div>
		</div>	
    </div>
	<?php 

	else:
	?>
    <div class="login-container animated fadeInDown">
        <div class="loginbox bg-white">
            <div class="loginbox-title">Registre-se</div>
			<form lass="form-signin" method="post" id="register-form"
				data-bv-message="Conteúdo inválido"
				data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
				data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
				data-bv-feedbackicons-validating="glyphicon glyphicon-refresh">
				<div class="form-group">
					<div class="col-lg-12 loginbox-textbox">
						<span class="input-icon">
							<i class="fa fa-envelope blue"></i>
							<input class="form-control" name="email" id="email" type="email" placeholder="Email"
								data-bv-emailaddress="true"
								data-bv-emailaddress-message="Email inv&aacute;lido" />
						</span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-12 loginbox-textbox">
						<span class="input-icon">
							<i class="fa fa-lock blue"></i>
							<input type="password" class="form-control" name="psw" id="psw" placeholder="Digite sua Senha"
								data-bv-notempty="true"
								data-bv-notempty-message="Senha obrigat&oacute;ria"
								data-bv-identical="true"
								data-bv-identical-field="conf"
								data-bv-identical-message="A senha e a confirma&ccedil;&atilde;o n&atilde;o s&atilde;o iguais"
								data-bv-different="true"
								data-bv-different-field="email"
								data-bv-different-message="A senha n&atilde;o pode ser igual ao email"/>
						</span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-12 loginbox-textbox">
						<span class="input-icon">
							<i class="fa fa-lock blue"></i>
							<input type="password" class="form-control" name="conf" id="conf" placeholder="Confirme sua Senha"
								data-bv-notempty="true"
								data-bv-notempty-message="Confirmação obrigat&oacute;ria"
								data-bv-identical="true"
								data-bv-identical-field="psw"
								data-bv-identical-message="A senha e a confirma&ccedil;&atilde;o n&atilde;o s&atilde;o iguais"
								data-bv-different="true"
								data-bv-different-field="email"
								data-bv-different-message="A confirmacao não pode ser igual ao email" />
						</span>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<div class="col-lg-12 loginbox-textbox">
						<span class="input-icon">
						<input type="text" class="form-control" name="nome" id="nome" placeholder="Nome Completo"
							data-bv-notempty="true"
							data-bv-notempty-message="Campo nome n&atilde;o por estar vazio" />
							<i class="glyphicon glyphicon-user blue"></i>
						</span>
					</div>
				</div>
				<div class="loginbox-submit">
					<button type="submit" class="btn btn-blue btn-block"><span class="glyphicon glyphicon-off"></span>&nbsp;Registrar</button>
				</div>
				<div class="loginbox-signup">
					<p>J&aacute; sou <a href="login.php">registrado</a></p>
				</div>
			</form>
        </div>
        <div class="logobox">
			<center><img src="<?php echo $GLOBALS['VirtualDir'];?>img/logo.png" width="80px" height="43px"/></center>
        </div>
    </div>
<?php endif;?>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/jquery.min.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/angular.min.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/jquery.sha1.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/bootstrap.min.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/bootstrap-select.min.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/bootstrap-dialog.min.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/beyond.min.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/validation/bootstrapValidator.min.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>js/functions.lib.js"></script>
    <script>jsLIB.rootDir = '<?php echo $GLOBALS['VirtualDir'];?>';</script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>js/register.js"></script>
</body>
</html>