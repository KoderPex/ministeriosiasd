$(document).ready(function(){
	$("#register-form")
		.bootstrapValidator()
		.on('success.form.bv', function(e) {
			e.preventDefault();
		})
		.submit( function() {
			var parameter = {
				username: $('#email').val(),
				password: $.sha1($('#psw').val()),
				confirm: $.sha1($('#conf').val()),
				name: $('#nome').val()
			};
			jsLIB.ajaxCall( false, jsLIB.rootDir+'rules/login.php', { MethodName : 'register', data : parameter }, 
				function( data, jqxhr ){
					if ( data.register == true ) {
						window.location.replace(data.page);
					} else {
						registerError(data.message);
					}
				});
		});
});

function registerError( errorMessage ){
	BootstrapDialog.show({
		title: 'Registro não efetivado!',
		message: errorMessage,
		type: BootstrapDialog.TYPE_WARNING,
		size: BootstrapDialog.SIZE_SMALL,
		draggable: true,
		closable: true,
		closeByBackdrop: false,
		closeByKeyboard: false,
		buttons: [{
			label: 'Fechar',
			cssClass: 'btn-info',
			action: function(dialogRef){
				dialogRef.close();
			}
		}]	
	});	
}