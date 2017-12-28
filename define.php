<?php
@include_once("include/functions.php");
@require_once("_dbconnect/connection.php");

$id = fRequest("id");
$hint = fRequest("hint");

if ( $id != "" ):
	fConnDB();
	$result = $GLOBALS['conn']->Execute("SELECT * FROM CD_PESSOA WHERE cd_valido = ? AND fg_ativo = 'N'", Array( $id ) );
	if ($result->EOF):
		header("location:".$GLOBALS['VirtualDir']."login.php");
		exit;
	else:
		$hint = $result->fields['cd_email'];
		$nome = ucwords(mb_strtolower($result->fields['nm']));
		$id_cd_pessoa = $result->fields['id'];

		//ATUALIZA USUARIO, VALIDANDO DADOS.
		$GLOBALS['conn']->Execute("
			UPDATE CD_PESSOA SET
				fg_ativo = 'S',
				ds_senha = NULL,
				cd_valido = null,
				nr_tent = 0
			WHERE id = ?",
			Array( $id_cd_pessoa )
		);
	endif;
endif;
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" ng-app="angular-app">
<!--Head-->
<head>
    <meta charset="utf-8"/>
    <title>Minist&eacute;rios IASD - Esqueci a senha</title>
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
    <div class="login-container animated fadeInDown">
        <div class="loginbox bg-white">
            <div class="loginbox-title">Recuperar Conta ou Senha</div>
			<br/>
			<form lass="form-signin" method="post" id="register-form"
				data-bv-message="Conteúdo inválido"
				data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
				data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
				data-bv-feedbackicons-validating="glyphicon glyphicon-refresh">
				<div class="form-group">
					<div class="col-lg-12 loginbox-textbox">
						<span class="input-icon">
							<?php if (!empty($hint)):?>
							<i class="fa fa-envelope"></i>
							<?php else:?>
							<i class="fa fa-envelope red"></i>
							<?php endif;?>
							<input class="form-control" name="email" id="email" type="email" placeholder="Email"
								data-bv-emailaddress="true"
								data-bv-emailaddress-message="Email inv&aacute;lido"
								<?php echo ( empty($hint) ? "" : "value=\"$hint\" disabled=\"true\"" )?>
								/>
						</span>
					</div>
				</div>
				<?php if (!empty($hint)):?>
				<div class="form-group">
					<div class="col-lg-12 loginbox-textbox">
						<span class="input-icon">
							<i class="fa fa-lock red"></i>
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
							<i class="fa fa-lock red"></i>
							<input type="password" class="form-control" name="conf" id="conf" placeholder="Confirme sua Senha"
								data-bv-notempty="true"
								data-bv-notempty-message="Confirma&ccedil;&atilde;o obrigatória"
								data-bv-identical="true"
								data-bv-identical-field="psw"
								data-bv-identical-message="A senha e a confirma&ccedil;&atilde;o n&atilde;o s&atilde;o iguais"
								data-bv-different="true"
								data-bv-different-field="email"
								data-bv-different-message="A confirmacao n&atilde;o pode ser igual ao email" />
						</span>
					</div>
				</div>
				<?php endif;?>
				<hr/>
				<div class="loginbox-submit">
					<button type="submit" class="btn btn-danger btn-block"><span class="glyphicon glyphicon-off"></span>&nbsp;Enviar solicita&ccedil;&atilde;o</button>
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
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/jquery.min.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/angular.min.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/jquery.sha1.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/bootstrap.min.js"></script>
	<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/bootstrap-dialog.min.js"></script>
	<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/bootstrap-select.min.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/beyond.min.js"></script>
    <script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/validation/bootstrapValidator.min.js"></script>
	<script src="<?php echo $GLOBALS['VirtualDir'];?>js/functions.lib.js"></script>
	<script>jsLIB.rootDir = '<?php echo $GLOBALS['VirtualDir'];?>';</script>
	<script src="<?php echo $GLOBALS['VirtualDir'];?>js/define.js"></script>

</body>
</html>