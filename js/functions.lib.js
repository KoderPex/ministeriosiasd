// JavaScript Document
/**
 *
 * PROTOTYPES
 *
 **/
Number.PAD_LEFT  = 0;
Number.PAD_RIGHT = 1;
Number.PAD_BOTH  = 2;

/**
 * 
 */
String.prototype.isEmpty = function() {
	return ( this.lenght == 0 ) || ( this.trim().length == 0 );
};

/**
 * 
 */
String.prototype.toInt = function() {
	return this.isEmpty() ? 0 : parseInt( this.replaceAll( ".", "" ), 10 );
};

/**
 * 
 * @param oldVal
 * @param newVal
 * @return
 */
String.prototype.replaceAll = function( oldVal, newVal ) {
	var str = this;

	while ( str.indexOf( oldVal ) > -1 ) {
		str = str.replace( oldVal, newVal );
	};

	return str;
};

if (!String.prototype.startsWith) {
    String.prototype.startsWith = function(searchString, position){
      position = position || 0;
      return this.substr(position, searchString.length) === searchString;
  };
}

/**
 *
 * @param size
 * @param pad
 * @param side
 */
Number.prototype.toPadString = function( size, pad, side ) {
	if ( !pad ) {
		pad = "0";
	};
	
	if ( !side ) {
		side = Number.PAD_LEFT;
	};
	
  	var str    = "" + this, 
  	    append = "", 
  	    size   = ( size - str.length );
 	var pad = ( ( pad != null ) ? pad : " " );
	
  	if ( side == Number.PAD_BOTH ) {
    	str = str.pad((Math.floor(size / 2) + str.length), pad, String.PAD_LEFT);
		
    	return str.pad((Math.ceil(size / 2) + str.length), pad, String.PAD_RIGHT);
  	};
	
  	while ((size -= pad.length) > 0) {
    	append += pad;
  	};
	
  	append += pad.substr(0, (size + pad.length));
	
  	return ((side == Number.PAD_LEFT) ? append.concat(str) : str.concat(append));
};

/**
 * 
 */
Date.prototype.toFormattedDate = function() {
	var month = this.getMonth() + 1;
	
	return this.getDate().toPadString(2) + "/" + 
		   month.toPadString(2) + "/" + 
		   this.getFullYear();
};

/**
 * 
 */
Date.prototype.toDateTime = function() {
	var month = this.getMonth() + 1;
	
	return this.getFullYear() + "-" +
	       month.toPadString(2) + "-" +
		   this.getDate().toPadString(2) + " " +
		   this.getHours().toPadString(2) + ":" +
		   this.getMinutes().toPadString(2) + ":" +
		   this.getSeconds().toPadString(2);
		   // + "." +this.getMilliseconds().toPadString(3)
};

var SPMaskBehavior = function (val) {
  return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
},
spOptions = {
  onKeyPress: function(val, e, field, options) {
	  field.mask(SPMaskBehavior.apply({}, arguments), options);
	}
};

$.fn.visible = function(lVisible) {
	if (lVisible) {
		this.show();
	} else {
		this.hide();
	}
	return this;
};
$.fn.isEnabled = function() {
	return this.attr( "disabled" ) == undefined || this.attr( "disabled" ).isEmpty();
};
$.fn.hasAttr = function(attr) {
	return this.attr( attr ) !== undefined && !this.attr( attr ).isEmpty();
};
$.fn.enable = function(lEnable) {
	if (lEnable) {
		this.removeAttr('disabled');
	} else {
		this.attr('disabled','disabled');
	}
	return this;
};
$.fn.selectpicker.defaults = {
	noneSelectedText: '(NENHUM)',
	noneResultsText: 'Nada encontrado contendo {0}',
	countSelectedText: 'Selecionado {0} de {1}',
	maxOptionsText: ['Limite excedido (máx. {n} {var})', 'Limite do grupo excedido (máx. {n} {var})', ['itens', 'item']],
	multipleSeparator: ', ',
	deselectAllText: 'Desmarcar Todos',
	selectAllText: 'Marcar Todos'
};

var jsLIB = {
	rootDir : undefined,
		
	watingDialog : new BootstrapDialog({
		size: BootstrapDialog.SIZE_SMALL,
		closable: false,
		draggable: false,
		message: function(dialogRef){
			var $message = $('<div align="center"><i class="fa fa-spinner fa-spin" style="font-size:200px"></i></div>');
			return $message;
		}
	}),

	modalWaiting : function( show ) {
		if ( !jsLIB.watingDialog.opened ) {
			jsLIB.watingDialog.realize();
			jsLIB.watingDialog.getModalHeader().hide();
			jsLIB.watingDialog.getModalFooter().hide();
			jsLIB.watingDialog.getModalBody().css('background-color', '#0088cc');
			jsLIB.watingDialog.getModalBody().css('color', '#fff');
		}
		if (show) {
			jsLIB.watingDialog.open();
		} else {
			jsLIB.watingDialog.close();
		}
	},
		
	ajaxCall : function( pasync, url, data, callBackSucess, callBackError ) {
		var retorno;
		if (pasync === false) {
			jsLIB.modalWaiting(true);
		} else if (!pasync) {
			pasync = false;
		}
		$.ajax({
			url		: url,
			async		: pasync,
			type		: 'post',
			data		: data,
			dataType	: 'json',
			
			success	: function( data, textStatus, jqxhr ) {
				if (!pasync) {
					jsLIB.modalWaiting(false);
				}
				if ( typeof( callBackSucess ) == 'function' ) {
					callBackSucess( data, jqxhr );
				} else if ( callBackSucess === 'RETURN' ) {
					retorno = data;
				}
			},
			
			error	: function( jqxhr, textStatus, errorMessage ) {
				if (!pasync) {
					jsLIB.modalWaiting(false);
				}
				if ( typeof( callBackError ) == 'function' ) {
					callBackError( jqxhr, errorMessage );
				}
			}               
		});
		if (!pasync) {
			jsLIB.modalWaiting(false);
		}
		return retorno;
	},
			
	populateForm : function( frm, data ) {
		jsLIB.resetForm(frm);
		$.each( data, function( key, value ) {
			var ctrl = $('[field='+key+']', frm.id );
			switch ( ctrl.attr("type") ) {
				case "radio":
				case "checkbox":
					if ( ctrl.attr("value-on") == value ) {
						ctrl.prop('checked', true).change();
					} else {
						ctrl.prop('checked', false).change();
					}
					break;
				case "text":
					if ( ctrl.parent().attr("datatype") == 'datetimepicker' ) {
						ctrl.parent().data("DateTimePicker").setDate( new Date(value.toInt()) );
						break;
					}
				case "hidden":
				default:
					ctrl.val(value).change();
			}  
		}); 
	},
		
	getJSONFields : function( frm ) {
		var retorno = {};
		frm.find( $('[field]') ).each( function() {
			retorno[$(this).attr("field")] = jsLIB.getValueFromField($(this));
		});
		return retorno;
	},
	
	getValueFromField : function( inputField ) {
		var value = "";
		switch ( inputField.attr("type") ) {
			case "radio":
			case "checkbox":
				if ( inputField.prop('checked') ) {
					value = inputField.attr('value-on');
				} else {
					value = inputField.attr('value-off');
				};
				break;
			default:
				value = inputField.val();
		}
		return value;
	},
	
	resetForm : function( frm ) {
		frm.find( $('[field]') ).each( function() {
			$(this).parents('.form-group').removeClass('has-success');
			var value = '';
			if ( $(this).attr('default-value') !== undefined && $(this).attr('default-value') != '' ) {
				value = $(this).attr('default-value');
			}
			switch ( $(this).attr("type") ) {
				case "radio":
				case "checkbox":
					$(this).prop('checked', false).change();
					break;
				case "text":
					if ( $(this).parent().attr("datatype") == 'datetimepicker' ) {
						$(this).parent().data("DateTimePicker").setDate( null );
						$(this).val(value);
						$(this).change();
						break;
					}
				default:
					$(this).val(value);
					$(this).change();
					if ( $(this).hasClass("selectpicker") ) {
						$(this).selectpicker('refresh');
					}
					break;
			}
		});
	},
	
	populateOptions : function( objSelect, source ) {
		var value = ( objSelect.hasAttr("opt-value") ? objSelect.attr("opt-value") : "value" );
		var label = ( objSelect.hasAttr("opt-label") ? objSelect.attr("opt-label") : "label" );
		var search = ( objSelect.hasAttr("opt-search") ? objSelect.attr("opt-search") : label );
		var links = ( objSelect.hasAttr("opt-links") ? objSelect.attr("opt-links").split(";") : null );
		var selected = ( objSelect.hasAttr("opt-selected") ? objSelect.attr("opt-selected") : null );
		objSelect.children().remove();
		if ( !objSelect.hasClass("selectpicker") ) {
			 objSelect.append( $("<option></option>")
				.attr("value","").text("(NENHUM)"));
		}
		$.each(source, function(idx, option) {   
			obj = $("<option></option>")
					.attr("value",option[value])
					.text(option[label]);
			if (search && search != label){
				obj.attr("data-tokens",option[search]+' '+option[label]);
			}
			if (selected && option[selected] == 'S'){
				obj.attr("selected","selected");
			}
			if (links) {
				for (var i=0;i<links.length;i++){
					var link = links[i];
					obj.attr(link,option[link]);
				}
			}
			objSelect.append(obj)
		});
		if ( objSelect.hasClass("selectpicker") ) {
			if ( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
				objSelect.selectpicker('mobile');
			} else {
				objSelect.selectpicker('refresh');
			}
		}
	}
};

var jsFilter = {
	filtered : false,

	apply: function (){
		jsFilter.filtered = true;
		var obj = $( $("#divFilters").attr("filter-to") );
		if ( obj ) {
			if ( obj.is( "SELECT" ) ){
				obj.trigger("reload.options.bs.select");
			} else if ( obj.is( "TABLE" ) ){
				obj.DataTable().ajax.reload();
			}
		}
	},

	jSON: function (){
		var retorno = {};
		$("#divFilters select").each(function(i,obj){
			var reg = {
				vl : $(obj).val(),
				fg : $("#notFilter"+$(obj).attr("filter-field")).prop('checked')
			};
			retorno[$(obj).attr("filter-field")] = reg;
		});
		return retorno;
	},
	
	removeAll : function(){
		$("[filter-value]").each(function(){
			jsFilter.removeFilter(this);
		});
	},

	removeFilter : function (objFilter){
		var obj = $(objFilter);
		var value = obj.attr("filter-value");
		var label = obj.attr("filter-label");
		var unique = obj.attr("filter-unique");
		
		var option = $("<option></option>") 
			.attr("value",value)
			.text(label);
		if (unique == 'true'){
			option.attr("data-tokens","unique");
		}

		$("#addFilter").append(option);
		$("#addFilter").html($("#addFilter").children('option').sort(function(x, y) {
			return $(x).text().toUpperCase() < $(y).text() ? - 1 : 1;
		}));
		$("#addFilter").val("").selectpicker('refresh');
		$("#optFilter"+value).selectpicker('destroy');
		$("#divFilter"+value).remove();
		if ( jsFilter.filtered ) {
			jsFilter.apply();
		}
		if ( $("#divFilters select").length == 0 ) {
			$("#applyFilter").hide();
			jsFilter.filtered = false;
		}
	},

	addFilter : function (objFilter){
		var obj = $(objFilter);
		var label = obj.find('option:selected').text();
		var unique = obj.find('option:selected').attr('data-tokens') == 'unique';
		var value = obj.val();

		if (value != ""){
			var flt = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/addFilter.php", { MethodName : 'getFilter', data : { type : value } }, 'RETURN' );
			if ( flt.result ) {
				var strAppend = 
					"<div class=\"input-group input-group-sm col-xs-12 col-md-12 col-sm-12 col-lg-12\" id=\"divFilter"+value+"\" style=\"padding-bottom:10px\">"+
					"<label for=\"optFilter"+value+"\" class=\"pull-left\">"+label+":&nbsp;</label>"+
					"<span class=\"label label-danger pull-right\" style=\"cursor:pointer\" onclick=\"jsFilter.removeFilter(this);\" filter-unique=\""+unique+"\" filter-value=\""+value+"\" filter-label=\""+label+"\"><i class=\"glyphicon glyphicon-remove\"></i>&nbsp;Remover</span>";
					if (flt.domain.length > 5 && !unique){
						strAppend += "<span class=\"pull-right\"><label for=\"notFilter"+value+"\"><input type=\"checkbox\" id=\"notFilter"+value+"\"><span class=\"text\">N&atilde;o</span></label>&nbsp;&nbsp;</span>";
					}
					strAppend += "<select class=\"selectpicker form-control input-sm\" id=\"optFilter"+value+"\" filter-field=\""+value+"\""+ (!unique?" multiple data-selected-text-format=\"count > 3\"":"") +" title=\"Escolha uma ou mais op&ccedil;&otilde;es\" data-width=\"100%\" data-container=\"body\"";
					if (flt.domain.length > 8 && !unique){
						strAppend += " data-live-search=\"true\"";
						strAppend += " data-actions-box=\"true\"";
					}
					strAppend += "></select>"+
					"</div>";

				$("#divFilters").append(strAppend);
				jsLIB.populateOptions( $("#optFilter"+value), flt.domain );
				$("#optFilter"+value).selectpicker();
				$("#addFilter option[value='"+value+"']").remove();
				$("#addFilter").val("").selectpicker('refresh');
				$("#applyFilter").show();
			}
		}
	}
	
};

var myApp = angular.module('angular-app',[]);