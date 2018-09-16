$(document).ready(function(){
	InitiateEasyPieChart.init();
	updateGraphs();

	$("#divDons").click(function(event){
		window.location.href = `${jsLIB.rootDir}report/printTesteDons.php?`;
		updateGraphs('D');
	});
	$("#divMini").click(function(event){
		window.location.href = `${jsLIB.rootDir}report/printTesteMinisterios.php?`;
		updateGraphs('M');
	});
});

function updateGraphs(update = ''){
	jsLIB.ajaxCall( true, jsLIB.rootDir+"rules/ajaxDashboard.php", { MethodName : 'downloads', data : { update } }, data => {
		const setGraph = barId => {
			const values = data.downloads[barId];
			$(`#${barId}`).find("#divData").text(`${values.qt} downloads`);
			$(`#${barId}`).find("#divColor").data('easyPieChart').options.barColor = values.cl;
			$(`#${barId}`).find("#divColor").data('easyPieChart').update(values.pc);
		}
		// setGraph("divDons");
		setGraph("divMini");
	});
}