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
		window.open(jsLIB.rootDir+'report/printResult.php?id='+$(this).attr('id-teste'),'_blank','top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',true);
	});
}