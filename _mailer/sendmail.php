<?php
global $mail;
@require_once("phpmailer/PHPMailerAutoload.php");
$GLOBALS['mail'] = new PHPMailer();
$GLOBALS['mail']->SetLanguage('br','phpmailer/language/');
$GLOBALS['mail']->IsSMTP();
$GLOBALS['mail']->Host = "mail.ministeriosiasd.com.br";
$GLOBALS['mail']->Port = 465;
$GLOBALS['mail']->SMTPAuth = true;
$GLOBALS['mail']->SMTPSecure = 'ssl';
$GLOBALS['mail']->CharSet = "iso-8859-1";
//$GLOBALS['mail']->CharSet = "UTF-8";
$GLOBALS['mail']->Username = "contato@ministeriosiasd.com.br";
$GLOBALS['mail']->Password = "Menudo#123";
$GLOBALS['mail']->IsHTML(true);
$GLOBALS['mail']->SetFrom("contato@ministeriosiasd.com.br", utf8_decode("Contato MinistériosIASD"));
$GLOBALS['mail']->AddReplyTo("contato@ministeriosiasd.com.br", utf8_decode("Contato MinistériosIASD"));
$GLOBALS['mail']->Subject = "Contato ministeriosiasd.com.br";
?>