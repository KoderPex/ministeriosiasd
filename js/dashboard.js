// If you want to draw your charts with Theme colors you must run initiating charts after that current skin is loaded
$(window).bind("load", function () {
	$("#myBtnLogout").click(function(){
		jsLIB.ajaxCall( false, jsLIB.rootDir+'rules/login.php', { MethodName : 'logout' } );
		window.location.replace( jsLIB.rootDir+'index.php' );
	});
	mapPrintResults();
});

function mapPrintResults(){
	$("[name=printResult]").unbind('click').click(function(){
		var testeID = $(this).attr('id-teste');

		BootstrapDialog.show({
			title: 'Visualizar PDF',
			message: $(`<embed id="oEmbedPrint" width="100%" height="100%" src="${jsLIB.rootDir}report/printResult.php?id=${testeID}" type="application/pdf" />`),
			type: BootstrapDialog.TYPE_DEFAULT,
			size: BootstrapDialog.SIZE_WIDE,
			draggable: false,
			closable: false,
			closeByBackdrop: false,
			closeByKeyboard: false,
			buttons: [
				{
					icon: 'glyphicon glyphicon-print',
					label: 'Imprimir',
					cssClass: 'btn-success',
					action: function(dialogRef){
						const printW = window.open(`${jsLIB.rootDir}report/printResult.php?id=${testeID}`);
						printW.print();
					}
				},
				{
					icon: 'glyphicon glyphicon-ban-circle',
					label: 'Fechar',
					cssClass: 'btn-primary',
					action: function(dialogRef){
						dialogRef.close();
					}
				}
			],
			onshown: function(dialog) {
				$("#oEmbedPrint").css("height", (window.innerHeight-200)+"px")
            },
		});
	});
}