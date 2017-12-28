var oTable = undefined;
var formPopulated = false;

$(document).ready(function(){
	
	oTable = $('#peopleDatatable').DataTable({
		sDom: "Tflt<'row DTTTFooter'<'col-sm-6'i><'col-sm-6'p>>",
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 300,
		searching: true,
		processing: true,
		language: {
			info: "_END_ pessoas",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type	: "POST",
			url	: jsLIB.rootDir+"rules/ajaxPeople.php",
			data	: function (d) {
				d.MethodName = "getPeople"
			},
			dataSrc: "people"
		},
		columns: [
			{	data: "id",
				width: "5%",
				render: function (data, type, full, meta ) {
					return "<a href=\"#\" name=\"btnEdit\" id-pessoa=\""+data+"\" class=\"btn btn-info btn-xs\"><i class=\"fa fa-pencil\"></i> Editar</a>";
				}				
			},
			{	data: "nm",
				width: "45%"
			},
			{	data: "em",
				width: "30%"
			},
			{	width: "10%",
		            data: "rd",
		            	render: function (data, type, full, meta ) {
						return (data == null 
							? "<a href=\"#\" name=\"btnDons\" class=\"btn btn-warning btn-xs\" id-pessoa=\""+full.id+"\"><i class=\"fa fa-plus\"></i> Adicionar</a>"
							: "<a href=\"#\" name=\"printResult\" id-teste="+data+" class=\"btn btn-success shiny btn-xs\"><i class=\"fa fa-print\"></i> Imprimir</a>" 
						);
					}
				},
			{	width: "10%",
	            data: "rm",
	            	render: function (data, type, full, meta ) {
					return (data == null 
						? "<a href=\"#\" name=\"btnMini\" class=\"btn btn-warning btn-xs\" id-pessoa=\""+full.id+"\"><i class=\"fa fa-plus\"></i> Adicionar</a>"
						: "<a href=\"#\" name=\"printResult\" id-teste="+data+" class=\"btn btn-success shiny btn-xs\"><i class=\"fa fa-print\"></i> Imprimir</a>" 
					);
				}				
			},
		],
		select: {
			style: 'multi',
			selector: 'td:first-child'
		}
	})
	.sort( [ 0, 'asc' ] )
	.on( 'draw.dt', function() {
		mapBtns();
	})		
	.on( 'search.dt', function() {
		ruleBtnNovo();
	});
	
	$("#cadMembrosForm")
		.on('success.form.fv', function(e) {
			e.preventDefault();
		})
		.on('err.field.fv', function(e, data) {
			data.element.attr('valid','not-ok');
		})
		.on('success.field.fv', function(e, data) {
			data.element.attr('valid','ok');
		})
		.on('init.field.fv', function(e, data) {
			if (data.element.attr('type') == 'checkbox' ) {
				data.element.attr('valid','ok');
			} else {
				data.element.attr('valid','not-ok');
			}
		})
		.formValidation({
			framework: 'bootstrap',
			fields: {
				nmCompleto:		{validators: {
						notEmpty: {
							message: 'O nome completo &eacute; obrigat&oacute;rio'
						},
						regexp: {
							regexp: /^([a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ\']{2,})+(?:\s[a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ\']{1,})+$/,
							message: 'Digite no m&iacute;nimo o nome e sobrenome sem espa&ccedil;os no final'
						}
				}},
				dsEmail:		{validators: {
					regexp: {
						regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
						message: 'Formato de email inv&aacute;lido'
					}
				}}
			}
		})
		.on("change", "[field]", function(e) {
			$("#cadMembrosForm")
				.formValidation('revalidateField', this.id);
			
			if (formPopulated) {
				var membroID = $("#membroID").val();
				var input = $(this);
				var field = input.attr('field');
				var value = jsLIB.getValueFromField(input);

				if (field && input.attr('valid') == 'ok') {
					formPopulated = false;
					if (field == "cd_pessoa-cd_email" ){
						value = value.toLowerCase();
					} else {
						value = value.toUpperCase();
					}
					input.val(value);
					formPopulated = true;
					
					if (membroID == 'Novo'){
						var nome = jsLIB.getValueFromField($("#nmCompleto"));

						if ( nome != "" ) {
							var parameters = {
								id: membroID,
								nm: nome
							}
							var mb = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/ajaxPeople.php", { MethodName : 'insertMember', data : parameters }, 'RETURN' );
							if (mb.result == true){
								populateMember(mb);
							}
						}
						
					} else {
						var parameters = {
							id	: membroID,
							field	: field,
							val	: value
						}
						//gravar
						var mb = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/ajaxPeople.php", { MethodName : 'updateMember', data : parameters }, 'RETURN' );
					}
				}
			}
		})
	;
	
	$('#membrosModal .modal-footer').click(function(e){
		e.preventDefault();
		e.stopPropagation();
	});
	$('#membrosModal').on('hidden.bs.modal', function(e){
		reloadGrid();
	});
	
	$('#btnNovo').click(function(e){
        	e.preventDefault();
        	e.stopPropagation();
        
		formPopulated = false;
		jsLIB.resetForm( $("#cadMembrosForm") );
		$("#membroID").val("Novo");
		formPopulated = true;
		$("#membrosModal").modal();
	});
	
	$('#btnBack').click(function(e){
		$('[name=divHead]').visible(false);
		$('#divTest').visible(false);
		$('#divGridSearch').visible(true);
	});
	
	$('#btnFinishTest').click( function() {
		var parameters = { 
			id : $('#divTestBody').attr('id-pessoa')
		};
		
		if ($('#divGiftTest').is(':visible')){
			jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/ajaxTestes.php", { MethodName : 'finalizarDonsDirect', data : parameters } );
		} else if ($('#divMiniTest').is(':visible')){
			jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/ajaxTestes.php", { MethodName : 'finalizarMiniDirect', data : parameters } );
		}
		window.location.reload(function(){
			$('#btnBack').click();
		});
	});
	
	$('#btnDonsDlg').click(function(e){
        	e.preventDefault();
        	e.stopPropagation();
        	populateGiftTest( $(this).attr('id-pessoa') );
	});
	
	$('#btnMiniDlg').click(function(e){
        	e.preventDefault();
        	e.stopPropagation();
        	populateMiniTest( $(this).attr('id-pessoa') );
	});
	
});

function populateMiniTest(pessoaID){
	$('#divGridSearch').visible(false);
	$('[name=divHead]').visible(false);
	$('#divMiniTest').visible(true);
	$('#divTest').visible(true);	
	$("#membrosModal").modal('hide');
	
	$('#divTestBody').attr('id-pessoa',pessoaID).html( prepareData( pessoaID ) );
	mapCodigoQuestaoMini();
	mapQuestaoMini();
}

function populateGiftTest(pessoaID){
	$('#divGridSearch').visible(false);
	$('[name=divHead]').visible(false);
	$('#divGiftTest').visible(true);
	$('#divTest').visible(true);
	$("#membrosModal").modal('hide');
		
	$('#divTestBody').attr('id-pessoa',pessoaID).html( prepareData( pessoaID ) );
	mapQuestaoDons();
}

function prepareData(pessoaID){
	var parameters = { 
		id : pessoaID
	};
	if ($('#divGiftTest').is(':visible')){
		var data = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/ajaxTestes.php", { MethodName : 'questoesDonsDirect', data : parameters }, 'RETURN' );
		fSetControleGift(data.result.pc_conc);
		return data.questoes;
		
	} else if ($('#divMiniTest').is(':visible')){
		var data = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/ajaxTestes.php", { MethodName : 'questoesMinisDirect', data : parameters }, 'RETURN' );
		return data.questoes;
	}
}

function mapBtns(){
	$("[name=btnDons]").unbind('click').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		populateGiftTest( $(this).attr('id-pessoa') );
	});
    
	$("[name=btnMini]").unbind('click').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		populateMiniTest( $(this).attr('id-pessoa') );
	});
	
	$("[name=btnEdit]").unbind('click').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		populateMember( getMember( $(this).attr('id-pessoa') ) );
		$("#membrosModal").modal();
	});
	
	mapPrintResults();
}

function reloadGrid(){
	oTable.ajax.reload( function(){
		ruleBtnNovo();
	});
}

function getMember( pessoaID ) {
	jsLIB.modalWaiting(true);
	var parameters = { 
		id : pessoaID
	};
	var retorno = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/ajaxPeople.php", { MethodName : 'getMember', data : parameters }, 'RETURN' );
	jsLIB.modalWaiting(false);
	return retorno;
}

function populateMember( mb ) {
	formPopulated = false;
	jsLIB.populateForm( $("#cadMembrosForm"), mb.membro );
	
	if ( $("#membroID").val() != '' && $("#membroID").val() != 'Novo' ) {
		$("#btnDonsDlg").attr('id-pessoa', $("#membroID").val());
		$("#btnDonsDlg").visible( mb.testes && mb.testes.dons && mb.testes.dons.nr_rsp != 0 );
		
		$("#btnMiniDlg").attr('id-pessoa', $("#membroID").val());
		$("#btnMiniDlg").visible( mb.testes && mb.testes.minis && mb.testes.minis.nr_rsp != 0 );
	}
	formPopulated = true;
}

function ruleBtnNovo(){
	$("#btnNovo").visible( oTable.page.info().recordsDisplay == 0 );
}

function mapQuestaoMini(){
	$("[name=questao]").unbind('change').change(function(e){
		var obj = $(this);
		jsLIB.ajaxCall( true, jsLIB.rootDir+"rules/ajaxTestes.php", 
			{ MethodName : 'setRsMinisteriosDirect', data : { id_pessoa : $('#divTestBody').attr('id-pessoa'), id_qs : obj.attr('id-questao'), nr_nota : obj.val() } },
			function( data, jqxhr ){
				if ( data.return == true ) {
					e.preventDefault();
					if (obj.val() != ""){
						var line = obj.parent().parent().parent();
						line.append(data.result);
						mapCodigoQuestaoMini();
						mapQuestaoMini();
					}
				}
			}
		);
	});
}

function mapQuestaoDons(){
	$("[name=questao]").unbind('change').change(function(e){
		var value = $(this).val();
		if (value != '1' && value != '2' && value != '3' && value != '4' && value != '5'){
			value = '';
			$(this).val('');
		}
		
		jsLIB.ajaxCall( true, jsLIB.rootDir+"rules/ajaxTestes.php", 
			{ MethodName : 'setRsDonsDirect', data : { id : $('#divTestBody').attr('id-pessoa'), qs : $(this).attr('id-questao'), col : value } },
			function( data, jqxhr ){
				if ( data.return == true ) {
					e.preventDefault();
					fSetControleGift(data.result.pc_conc);
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

function mapCodigoQuestaoMini(){
	$("[name=cdQuestao]").unbind('change').change(function(e){
		var obj = $(this);
		jsLIB.ajaxCall( true, jsLIB.rootDir+"rules/ajaxTestes.php", 
			{ MethodName : 'getQstMiniCode', data : { cd : obj.val() } }, 
			function( data, jqxhr ){
				if ( data.return !== true ) {
					e.preventDefault();
					BootstrapDialog.show({
						title: "Ministérios",
						type: BootstrapDialog.TYPE_INFO,
						message: function(dialogRef){
					                return 'Código de ministério inválido!';
						},
						draggable: true,
						closable: true,
						closeByBackdrop: true,
						closeByKeyboard: true,
						buttons: [{
							label: 'Fechar',
							cssClass: 'btn-info',
							action: function(dialogRef){
								obj.val("");
								dialogRef.close();
							}
						}]
					});
					obj.val("");
					return;
				}
				var line = obj.parent().parent();				
				line.find("[name=lblQuestao]").html(data.result.ds);
				line.find("[name=questao]").attr("id-questao",data.result.id);
				obj.enable(false);
			}
		);
	});
}

function fSetControleGift(pcConc){
	$('#myProgressbar').progressbar(pcConc);
	if ( pcConc < 100 ) {
		$('#myProgressbar').show();
		$('#btnFinishTest').hide();
	} else {
		$('#myProgressbar').hide();
		$('#btnFinishTest').show();
	}
}