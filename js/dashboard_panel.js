$(window).bind("load", function () {
	InitiateEasyPieChart.init();
});

myApp.controller('dashboard', ['$scope', function ($scope) {
	var data = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/ajaxDashboard.php", { MethodName : 'painel' }, 'RETURN' );
	$scope.panels = data.panels
}]);