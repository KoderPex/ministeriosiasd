$(document).ready(function(){
	$('#simpledatatable')
		.on( 'init.dt', function () {
			mapQuestao();
		})
		.dataTable({
			pageLength: 23,
			lengthChange: false,
			ordering: false,
			paging: true,
			searching: false,
			processing: true,
			language: {
				info: "_START_ a _END_ de _TOTAL_ quest&otilde;es",
				infoEmpty: "N&atilde;o h&aacute; respostas pendentes",
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
	
	$('#btnFinishDons').click( function() {
		jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/ajaxTestes.php", { MethodName : 'finalizarDons' } );
		window.location.reload(true);
	});
	
	$('[name=detalheDom]').click( function() {
		jsLIB.ajaxCall( true, jsLIB.rootDir+"rules/ajaxTestes.php", { MethodName : 'getDetailGift', data : { cd : $(this).attr('cd-ref') } }, 
			function( data, jqxhr ){
				if ( data.return == true ) {
					BootstrapDialog.show({
						title: data.result.ds_dom,
						type: BootstrapDialog.TYPE_INFO,
						message: function(dialogRef){
					                var $desc = $("<div>"+data.result.ds+"</div>");
					                var $ref  = $("<div><b><u>Referências Bíblicas</u></b>:"+data.result.ds_ref_biblica+"</div>");
					                var $task = $("<div><b><u>Tarefas</u></b>:"+data.result.ds_tarefas+"</div>");
					                $desc.append($ref).append($task);
					                return $desc;
						},
						draggable: true,
						closable: true,
						closeByBackdrop: true,
						closeByKeyboard: true,
						buttons: [{
							label: 'Fechar',
							cssClass: 'btn-info',
							action: function(dialogRef){
								dialogRef.close();
							}
						}]
					});
				}
			}
		 );
	});
	
});

function mapQuestao(){
	$("[name=questao]").change(function(e){
		var value = $(this).val();
		
		jsLIB.ajaxCall( true, jsLIB.rootDir+"rules/ajaxTestes.php", 
			{ MethodName : 'setRsDons', data : { id_qs : $(this).attr('id-questao'), id_rs : value } },
			function( data, jqxhr ){
				if ( data.return == true ) {
					e.preventDefault();
					fSetControle(data.result.pc_conc);
				}
			}
		);
		
		if ( value != '' ) {
			$(this).parent().removeClass('has-error').addClass('has-success');
		} else {
			$(this).parent().removeClass('has-success').addClass('has-error');
		}
	});
}

function prepareData(){
	data = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/ajaxTestes.php", { MethodName : 'questoesDons' }, 'RETURN' );
	fSetControle(data.result.pc_conc);
	return data.questoes;
	
}

function fSetControle(pcConc){
	$('#myProgressbar').progressbar(pcConc);
	if ( pcConc < 100 ) {
		$('#myProgressbar').show();
		$('#btnFinishDons').hide();
	} else {
		$('#myProgressbar').hide();
		$('#btnFinishDons').show();
	}
}