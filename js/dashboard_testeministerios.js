$(document).ready(function(){
	$('#simpledatatable')
	.on( 'init.dt', function () {
		mapQuestao();
	})
	.dataTable({
		pageLength: 10,
		lengthChange: false,
		ordering: false,
		paging: true,
		searching: false,
		processing: true,
		language: {
			info: "_START_ a _END_ de _TOTAL_ sugest&otilde;es de minist&eacute;rios",
			loadingRecords: "Aguarde - carregando...",
			paginate: {
				first: '<<',
				previous: '<',
				next:     '>',
				last:     '>>'
			}
		},
		data: prepareData(),
		columns: [
			{	data: 'ds_qst',
				sortable: false
			}
		]
	})
	.on( 'draw.dt', function () {
		mapQuestao();
	});
	
	$('#btnFinishMinisterios').click( function() {
		jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/ajaxTestes.php", { MethodName : 'finalizarMinisterios' } );
		window.location.reload(true);
	});

});

function prepareData(){
	data = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/ajaxTestes.php", { MethodName : 'questoesMinisterios' }, 'RETURN' );
	return data.questoes;
}

function mapQuestao(){
	$("[name=questao]").change(function(e){
		var value = $(this).val();
		jsLIB.ajaxCall( true, jsLIB.rootDir+"rules/ajaxTestes.php", 
			{ MethodName : 'setRsMinisterios', data : { id_qs : $(this).attr('id-questao'), nr_nota : value } },
			function( data, jqxhr ){
				if ( data.return == true ) {
					e.preventDefault();
				}
			}
		);
	});
}