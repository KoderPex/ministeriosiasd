$(document).ready(function(){
	$("#login-form")
		.bootstrapValidator()
		.on('success.form.bv', function(e) {
			e.preventDefault();
		})
		.submit(  function(e) {
			e.preventDefault();
			
			var parameter = {
				username: $('#email').val(),
				password: $.sha1($('#psw').val())
			};
			jsLIB.ajaxCall( false, jsLIB.rootDir+'rules/login.php', { MethodName : 'login', data : parameter }, 
				function( data, jqxhr ){
					if ( data.login == true ) {
						window.location.replace(data.page);
					} else {
						loginError(data.message);
					}
				}, loginError );
		});
});

function loginError( errorMessage ){
	BootstrapDialog.show({
		title: 'Erro',
		message: ( errorMessage != '' ? errorMessage : 'Acesso negado!' ),
		type: BootstrapDialog.TYPE_DANGER,
		size: BootstrapDialog.SIZE_SMALL,
		draggable: true,
		closable: true,
		closeByBackdrop: false,
		closeByKeyboard: false,
		buttons: [{
			label: 'Fechar',
			cssClass: 'btn-danger',
			action: function(dialogRef){
				dialogRef.close();
			}
		}]	
	});	
}