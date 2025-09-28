function marcas(source)
{
	checkboxes=document.getElementsByTagName('input');
	//obtenemos todos los controles del tipo Input
	for(i=0;i<checkboxes.length;i++)
	{
		//recoremos todos los controles
		if(checkboxes[i].type == "checkbox")
		{
			//solo si es un checkbox entramos
			checkboxes[i].checked=source.checked;
			//si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
		}
	}
}

function ocultarFila(num,numfin,ver) {
	dis=ver?'':'none';
	tr=document.getElementById('tablelines').getElementsByTagName('tr')[num];
	tr.style.display=dis;
}
function ocultarColumna(num,numfin,ver) {
	dis= ver?'':'none';
	fila=document.getElementById('tablelines').getElementsByTagName('tr');
	for(j=num;j<=numfin;j++)
	{
		for(i=0;i<fila.length;i++)
			fila[i].getElementsByTagName('td')[j].style.display=dis;
	}
}
function revisaFrame(f){
	var idprod = document.getElementById( 'idprod' ).value;
	var lol = f;
	//cambiando el estado de
	document.getElementById('iframe').src= 'search_product.php?idprod='+idprod+'&ref='+lol;
}
function CambiaURLFrame(f){
	//var lol = document.getElementById('search_id_product').value;
	var lol = f;
	//cambiando el estado de
	document.getElementById('iframe').src= 'search_product.php?ref='+lol;
}
function CambiaURLFramep(f,id)
{
	var lol = f;
	var lid = id;
	document.getElementById('iframe').src= 'search_product_projet.php?ref='+lol+'&id='+lid;
}
function ocultarproj(){
	document.getElementById('tagprojet').style.display = 'none';
}
function mostrarproj(){
	document.getElementById('tagprojet').style.display = 'block';
}
function ocultarins(){
	document.getElementById('listins').style.display = 'none';
}
function mostrarins(){
	document.getElementById('listins').style.display = 'block';
}

function selydestodos(form,activa)
{
	for(i=0;i<form.elements.length;i++)
	{
		if(form.elements[i].type=="checkbox")
			form.elements[i].checked=activa;
	}
}

//Función que envía la petición ajax.
function recargasearch(e){
	var iddiv = e.id;
	var idvalue = e.value;
	if (idvalue == 'MAT')
	{
		$("#productnone").hide();
		$("#tagproduct").show();
		$("#tagproductbudget").hide();
		$("#tagsociete").hide();
		$("#tagmember").hide();
		$("#tagassets").hide();
		$("#product").focus();
	}
	if (idvalue == 'MAText')
	{
		$("#productnone").hide();
		$("#tagproduct").hide();
		$("#tagproductbudget").hide();
		$("#tagsociete").show();
		$("#tagmember").hide();
		$("#tagassets").hide();
		$("#search_fk_soc").focus();
	}
	if (idvalue == 'MOD')
	{
		$("#productnone").hide();
		$("#tagproduct").hide();
		$("#tagproductbudget").hide();
		$("#tagsociete").hide();
		$("#tagmember").show();
		$("#tagassets").hide();
		$("#fk_member").focus();
	}
	if (idvalue == 'MODext')
	{
		$("#productnone").hide();
		$("#tagproduct").hide();
		$("#tagproductbudget").hide();
		$("#tagsociete").show();
		$("#tagmember").hide();
		$("#tagassets").hide();
		$("#search_fk_soc").focus();
	}
	if (idvalue == 'MAQ')
	{
		$("#productnone").hide();
		$("#tagproduct").hide();
		$("#tagproductbudget").hide();
		$("#tagsociete").hide();
		$("#tagmember").hide();
		$("#tagassets").show();
		$("#fk_equipment").focus();
	}
	if (idvalue == 'MAQext')
	{
		$("#productnone").hide();
		$("#tagproduct").hide();
		$("#tagproductbudget").hide();
		$("#tagsociete").show();
		$("#tagmember").hide();
		$("#tagassets").hide();
		$("#search_fk_soc").focus();
	}
}
