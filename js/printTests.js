$(window).bind("load", function () {
	InitiateEasyPieChart.init();
});

myApp.controller('dashboard', ['$scope', function ($scope) {
	var data = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/ajaxDashboard.php", { MethodName : 'downloads' }, 'RETURN' );
	$scope.downloads = data.downloads
}]);