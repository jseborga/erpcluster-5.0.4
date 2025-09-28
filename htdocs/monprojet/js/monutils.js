
function CambiaURLFrame(f)
{
    //var lol = document.getElementById('search_id_product').value;
    var lol = f;
    //alert(f);
    //cambiando el estado de
    document.getElementById('iframe').src= 'search_product.php?ref='+lol;
}



//Función que envía la petición ajax.
function buscar_legajo(){

	var id = $('#id').val();
	var task = $('#task').val();
	var product = $('#product').val();
	var qty = $('#qty').val();
	var action = 'addmat';
	var ref = $('#ref').val();
	//alert(' '+task+' '+product+' '+qty+' '+action);
	$.ajax({
		url: 'dame-datos.php',
		type: 'POST',
		timeout: 10000,
		data: "id="+id+"&task="+task+"&product="+product+"&qty="+qty+"&action="+action+"&ref="+ref,
         beforeSend: function(){
         	$("#carga"+ref).html('Buscando legajo...');
         },
         error: function(){
         	$("#carga"+ref).html('');
         	alert('Ha surgido un error.')
         },
         success: function(respuesta){
         	$("#carga"+ref).html(respuesta);
         }
     });
}
//Función que envía la petición ajax.
function borrar_legajo(e,ref){
	var id = $('#id').val();
	var action = 'delmat';
	//var elemento = document.querySelector('.delete');
	var idreg = e.id;
	$.ajax({
		url: 'dame-datos.php',
		type: 'POST',
		timeout: 10000,
		data: "id="+id+"&idreg="+idreg+"&action="+action+"&ref="+ref,
         beforeSend: function(){
         	$("#carga"+ref).html('Borrando...');
         },
         error: function(){
         	$("#carga"+ref).html('');
         	alert('Ha surgido un error.')
         },
         success: function(respuesta){
         	$("#carga"+ref).html(respuesta);
         }
     });

}
$(document).ready(function(){
	$('.btn_enviar').click(function(){
		buscar_legajo();
	});
	$('.delete').click(function(){
		borrar_legajo();
	});
});