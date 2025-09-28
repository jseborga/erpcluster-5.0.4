
function enviarDatos(dato,fk,idr)
{
	var formu = 'form_'+fk+idr;
	var newdata = dato;
	//Recogemos los valores introducimos en los campos de texto
	var formula = document.getElementById("formula"+fk+idr).value;
	nuevaformula = replaceAll(formula,"+","_mas_");
	nuevaformula = replaceAll(nuevaformula,"-","_menos_");
	var separador = "|";
	var cData = newdata.split(separador);
	var nData = '';
	for (var i=0; i<cData.length; i++)
	{
		cId = cData[i]+"_"+fk+idr;
		if (nData.length == 0)
			nData = cData[i] + '=' + document.getElementById(cId).value;
		else
			nData = nData + "&"+cData[i] + '=' + document.getElementById(cId).value;
	}
	nData = nData + '&formula='+nuevaformula;
	$.ajax({
		url: 'procesa.php',
		type: 'POST',
		timeout: 10000,
		data: nData,
		beforeSend: function(){
			$("#resultado"+fk+idr).html('Buscando registros...');
		},
		error: function(){
			$("#resultado"+fk+idr).html('');
			alert('Ha surgido un error.')
		},
		success: function(respuesta){
			$("#resultado"+fk+idr).html(respuesta);
		}
	});


}

function replaceAll(text, search, newstring ){
    while (text.toString().indexOf(search) != -1)
        text = text.toString().replace(search,newstring);
    return text;
}

//Función que envía la petición ajax.
function buscar_itemdet(e,id,idreg){
	var iddiv = e.id;
	if (iddiv == 'showMAT')
	{
		$("#halfrightshowMAT").show();
		$("#halfrightshowMDO").hide();
		$("#halfrightshowEMH").hide();
	}
	if (iddiv == 'showMDO')
	{
		$("#halfrightshowMAT").hide();
		$("#halfrightshowMDO").show();
		$("#halfrightshowEMH").hide();
	}
	if (iddiv == 'showEMH')
	{
		$("#halfrightshowMAT").hide();
		$("#halfrightshowMDO").hide();
		$("#halfrightshowEMH").show();
	}
	//alert(' '+task+' '+product+' '+qty+' '+action);
	$.ajax({
		url: 'search_itemdet.php',
		type: 'POST',
		timeout: 10000,
		data: "id="+id+"&idreg="+idreg+"&tag"+iddiv,
		beforeSend: function(){
			$("#halfright"+iddiv).html('Buscando registros...');
		},
		error: function(){
			$("#halfright"+iddiv).html('');
			alert('Ha surgido un error.')
		},
		success: function(respuesta){
			$("#halfright"+iddiv).html(respuesta);
		}
	});
}

function buscar_task(e,id,idreg){
	var iddiv = e.id;
	$.ajax({
		url: 'search_task.php',
		type: 'POST',
		timeout: 10000,
		data: "id="+id+"&idg="+idreg,
		beforeSend: function(){
			$("#listtask").html('Buscando registros...');
		},
		error: function(){
			$("#listtask").html('');
			alert('Ha surgido un error.');
		},
		success: function(respuesta){
			$("#listtask").html(respuesta);
		}
	});
}

function marcar_sel(e,id)
{
	var iddiv = e.id;
	var idsel = 'x'+e.id;
	var total=document.getElementById("tselect").value;
	total = total * 1;
	$('#formid input[type=checkbox]').each(function(){
            if (this.checked) {
                total = total + parseInt(1);
            }
        });
	document.getElementById(idsel).value = total;
	document.getElementById("tselect").value = total;
    $.ajax({
        url: "updatesel.php",
        type: "POST",
        data: { "id" : id, "idsel" : iddiv, "total" : total },
        ajaxSend: function( data ){
           $( "#listp" ).html( "Conectando..." );
        },
        success: function( data ) {
           $( "#listp" ).html( "Añadido!" );
        },
        error: function ( data ) {
            $( "#listp" ).html( "Error!" );
        }
    });
}

function marcar_selr(e,id)
{
	var iddiv = e.id;
	var idsel = 'r'+e.id;
	var total=document.getElementById("tselectr").value;
	total = total * 1;
	$('#formidr input[type=checkbox]').each(function(){
            if (this.checked) {
                total = total + parseInt(1);
            }
        });
	document.getElementById(idsel).value = total;
	document.getElementById("tselectr").value = total;

    $.ajax({
        url: "updatesel.php",
        type: "POST",
        data: { "id" : id, "idsel" : iddiv, "total" : total },
        ajaxSend: function( data ){
           $( "#listr" ).html( "Conectando..." );
        },
        success: function( data ) {
           $( "#listr" ).html( "Añadido!" );
        },
        error: function ( data ) {
            $( "#listr" ).html( "Error!" );
        }
    });

}

function marcar_selt(e,id)
{
	var iddiv = e.id;
	var idsel = 't'+e.id;
	var total=document.getElementById("tselectt").value;
	total = total * 1;
	$('#formidt input[type=checkbox]').each(function(){
            if (this.checked) {
                total = total + parseInt(1);
            }
        });
	document.getElementById(idsel).value = total;
	document.getElementById("tselectt").value = total;

    $.ajax({
        url: "updatesel.php",
        type: "POST",
        data: { "id" : id, "idsel" : iddiv, "total" : total },
        ajaxSend: function( data ){
           $( "#listt" ).html( "Conectando..." );
        },
        success: function( data ) {
           $( "#listt" ).html( "Añadido!" );
        },
        error: function ( data ) {
            $( "#listt" ).html( "Error!" );
        }
    });

}


function marcar_selxx(e,id){
	var iddiv = e.id;
	var idsel = 'x'+e.id;
	alert(id+' div '+iddiv+' sel '+idsel);
	$.ajax({
		url: 'updatesel.php',
		type: 'POST',
		timeout: 100,
		data: "id="+id+"&idsel="+idsel,
		beforeSend: function(){
			$("#x"+iddiv).html('actualizando...');
		},
		error: function(){
			$("#x"+iddiv).html('');
			alert('Ha surgido un error.');
		},
		success: function(respuesta){
			$("#x"+iddiv).html(respuesta);
		}
	});
}

function import_resource(e,id){
	var iddiv = e.id;
	var value = e.value;
	$.ajax({
		url: 'import_resource.php',
		type: 'POST',
		timeout: 10000,
		data: "id="+id+"&idsel="+value,
		beforeSend: function(){
			$("#listres").html('Buscando registros...');
		},
		error: function(){
			$("#listres").html('');
			alert('Ha surgido un error.')
		},
		success: function(respuesta){
			$("#listres").html(respuesta);
		}
	});
}

function import_product(e,id){
	var iddiv = e.id;
	var value = e.value;
	$.ajax({
		url: 'import_product.php',
		type: 'POST',
		timeout: 10000,
		data: "id="+id+"&idsel="+value,
		beforeSend: function(){
			$("#listres").html('Buscando registros...');
		},
		error: function(){
			$("#listres").html('');
			alert('Ha surgido un error.')
		},
		success: function(respuesta){
			$("#listres").html(respuesta);
		}
	});
}
function import_tasks(e,id){
	var iddiv = e.id;
	var value = e.value;
	//alert('valor '+value);
	$.ajax({
		url: 'import_tasks.php',
		type: 'POST',
		timeout: 10000,
		data: "id="+id+"&idsel="+value,
		beforeSend: function(){
			$("#listres").html('Buscando registros...');
		},
		error: function(){
			$("#listres").html('');
			alert('Ha surgido un error.')
		},
		success: function(respuesta){
			$("#listres").html(respuesta);
		}
	});
}

function import_items(e,id,fk_region,fk_sector){
	var iddiv = e.id;
	var iddivreg = e.fk_region;
	var iddivsec = e.fk_sector;
	var value = e.value;
	//alert('valor '+value);
	$.ajax({
		url: 'import_items.php',
		type: 'POST',
		timeout: 10000,
		data: "id="+id+"&idsel="+value+"&fk_region="+iddivreg+"&fk_sector="+iddivsec,
		beforeSend: function(){
			$("#listres").html('Buscando registros...');
		},
		error: function(){
			$("#listres").html('');
			alert('Ha surgido un error.')
		},
		success: function(respuesta){
			$("#listres").html(respuesta);
		}
	});
}


function search_resource(e,id,idr)
{
	/// Aqui podemos enviarle alguna variable a nuestro script PHP
	/// Invocamos a nuestro script PHP
	$.ajax({
		url: 'search_resource.php',
		type: 'POST',
		timeout: 10000,
		data: "id="+id+"&idreg="+idr,
		beforeSend: function(){
			$("#listresource").html('Buscando registros...');
		},
		error: function(){
			$("#listresource").html('');
			alert('Ha surgido un error.')
		},
		success: function(respuesta){
			$("#listresource").html(respuesta);
		}
	});
}

function CambiaURLFramei(f)
{
	var lol = f;
	document.getElementById('iframe').src= 'search_item.php?ref='+lol;
}
function CambiaURLFrame(f)
{
	var lol = f;
	document.getElementById('iframe').src= 'search_product.php?ref='+lol;
}
function CambiaURLFrameb(f,id)
{
	var lol = f;
	var lid = id;
	document.getElementById('iframe').src= 'search_product_budget.php?ref='+lol+'&id='+lid;
}

function checkTodos()
{
	var lol;
	if($(".check:checked").length)
	{
		lol = false;
	}
	else
	{
		lol = true;
	}

	$(".check").each(function()
	{
		$(this).prop('checked',lol);
	});

}

$(document).ready(function(){
	$(".check_todos").click(function(event){
		if($(this).is(":checked")) {
			$(".ck:checkbox:not(:checked)").attr("checked", "checked");
		}else{
			$(".ck:checkbox:checked").removeAttr("checked");
		}
	});
});

function revoperator(e){
	var iddiv = e.value;

	if (iddiv == '5')
	{
		$("#fk_pu_structure").show();
		$("#fk_formula").hide();
		$("#nValor").hide();
	}
	else
	{
		$("#fk_pu_structure").hide();
		$("#fk_formula").show();
		$("#nValor").show();

	}
}

function revisaFrame(f){
	var idprod = document.getElementById( 'idprod' ).value;
	var lol = f;
	//cambiando el estado de
	document.getElementById('iframe').src= 'search_product.php?idprod='+idprod+'&ref='+lol;
}

function objetoAjax()
{
	var xmlhttp = false;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {

		try {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false; }
		}

		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		return xmlhttp;
	}

