$(document).ready(function(){

	let groupColumn = 0;
	$('#simpledatatable')
	.on( 'init.dt', function () {
		mapQuestao();
	})
	.DataTable({
		sDom: "Tflt<'row DTTTFooter'<'col-sm-6'i><'col-sm-6'p>>",
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 470,
		searching: true,
		processing: true,
		language: {
			search: "",
			searchPlaceholder: "Procurar...",
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
			{	data: 'da',
				sortable: false
			},
			{	data: 'cd',
				width: "6%",
				sortable: false
			},
			{	data: 'ds',
				width: "94%",
				sortable: false
			}
		],
		drawCallback: function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last = null;
 
            api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td colspan="2">'+group+'</td></tr>'
                    );
 
                    last = group;
                }
            } );
        },
		columnDefs: [
            { "visible": false, "targets": groupColumn }
        ],
		select: {
			style: 'multi',
			selector: 'td:first-child'
		}
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