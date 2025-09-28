
/* Copyright (C) 2007-2008 Jeremie Ollivier <jeremie.o@laposte.net>
 * 2013-2013 Ramiro Queso <ramiro@ubuntu-bo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

 function recarga_item(){
 	var variable_post="Mi texto recargado";
 	$.post("carga_item.php", { variable: variable_post }, function(data){
 		$("#recarga_item").html(data);
 	});
 }

 function recarga_cesta(){
 	var variable_post="Mi texto recargado";
 	$.post("carga_cesta.php", { variable: variable_post }, function(data){
 		$("#recarga_cesta").html(data);
 	});
 }
 function recarga_total(){
 	var variable_post="total";
 	$.post("total.php", { variable: variable_post }, function(data){
 		$("#recarga_cesta").html(data);
 	});
 }
 function CambiaURLFrame(f){
	//var lol = document.getElementById('search_id_product').value;
	var lol = f;
	//cambiando el estado de
	document.getElementById('iframe').src= 'search_product.php?ref='+lol;
}
function CambiaURLFramenit(f){
	var nit = document.getElementById('cnit').value;
	//cambiando el estado de
	document.getElementById('iframenit').src= 'consultanit.php?nit=' + nit;
}
function calcula(f){
	var total = 0;
	var quant = document.getElementById('quant').value;
	var price = document.getElementById('price').value;
	total = quant * price;
	window.parent.document.getElementById('total').value = total
}
function verifrowid(f){
	var rowid = document.getElementById('id').value;
	if (rowid===0 || rowid == 0 || rowid == "")
	{
		window.parent.document.getElementById('search_id_product').focus();
	}
}
function viewquant(){
	window.parent.document.getElementById('quant').focus();
}
function sendForm() {
	document.myform.submit()
}
function sendFormv() {
	document.myformv.submit()
}
function sendFormp() {
	document.form_pay.submit()
}
function sino(cual) {
	var elElemento=document.getElementById(cual);
	if(elElemento.style.display == 'block') {
		elElemento.style.display = 'none';
	} else {
		elElemento.style.display = 'block';
	}
}
//onkeypres
//chCode enter =  13
function GetChar (event)
{
	var chCode = ('charCode' in event) ? event.charCode : event.keyCode;
	//alert (" carater es: " + chCode);
	if (chCode == 13)
	{
	//alert ("El codigo de carater es: " + chCode);
	sendFormp();
}
if (chCode == 0)
{
	//alert ("El codigo de carater es: " + chCode);
	sendFormp();
}
if (chCode == 17)
{
	window.parent.document.getElementById('fk_payment').focus();
	//alert ("El codigo de carater es: " + chCode);
	//sendFormp();
}
}

function numeros(e){
	key = e.keyCode || e.which;
	tecla = String.fromCharCode(key).toLowerCase();
	letras = " 0123456789";
	especiales = [8,37,39,46];

	tecla_especial = false
	for(var i in especiales){
		if(key == especiales[i]){
			tecla_especial = true;
			break;
		}
	}

	if(letras.indexOf(tecla)==-1 && !tecla_especial)
		return false;
}

/*funcion para sumar en resumen*/

/**
 * Funcion que se ejecuta cada vez que se añade una letra en un cuadro de texto
 * Suma los valores de los cuadros de texto
 */

 function sumar()

 {
 	var valor200=verificar("lo200");
 	var valor100=verificar("lo100");
 	var valor50=verificar("lo50");
 	var valor20=verificar("lo20");
 	var valor10=verificar("lo10");
 	var valor5=verificar("lo5");
 	var valor2=verificar("lo2");
 	var valor1=verificar("lo1");
 	var valor_50=verificar("lo0,50") * parseFloat(0.50);
 	var valor_20=verificar("lo0,20") * parseFloat(0.20);
 	var valor_10=verificar("lo0,10") * parseFloat(0.10);
	// realizamos la suma de los valores y los ponemos en la casilla del

	// formulario que contiene el total

	document.getElementById("totallo").value=(parseFloat(valor200*200)+parseFloat(valor100*100)+parseFloat(valor50*50)+parseFloat(valor20*20)+parseFloat(valor10*10)+parseFloat(valor5*5)+parseFloat(valor2*2)+parseFloat(valor1*1)+valor_50+valor_20+valor_10).toFixed(2);

}

function sumarx()

{
	var valorx200=verificar("lx200");
	var valorx100=verificar("lx100");
	var valorx50=verificar("lx50");
	var valorx20=verificar("lx20");
	var valorx10=verificar("lx10");
	var valorx5=verificar("lx5");
	var valorx2=verificar("lx2");
	var valorx1=verificar("lx1");
	var valorx_50=verificar("lx0,50") * parseFloat(0.50);
	var valorx_20=verificar("lx0,20") * parseFloat(0.20);
	var valorx_10=verificar("lx0,10") * parseFloat(0.10);
		// realizamos la suma de los valores y los ponemos en la casilla del

		// formulario que contiene el total

		document.getElementById("totallx").value=(parseFloat(valorx200*200)+parseFloat(valorx100*100)+parseFloat(valorx50*50)+parseFloat(valorx20*20)+parseFloat(valorx10*10)+parseFloat(valorx5*5)+parseFloat(valorx2*2)+parseFloat(valorx1*1)+valorx_50+valorx_20+valorx_10).toFixed(2);

	}


/**
* Funcion para verificar los valores de los cuadros de texto. Si no es un
* valor numerico, cambia de color el borde del cuadro de texto
*/

function verificar(id)

{

	var obj=document.getElementById(id);

	if(obj.value=="")

		value="0";

	else

		value=obj.value;

	if(validate_importe(value,0))

	{

			// marcamos como erroneo

			obj.style.borderColor="#808080";
			return value;

		}else{

			// marcamos como erroneo
			obj.style.borderColor="#f00";
			obj.focus();
			obj.value = parseInt(obj.value);
			return 0;

		}

	}



/**
* Funcion para validar el importe
* Tiene que recibir:
*  El valor del importe (Ej. document.formName.operator)
*  Determina si permite o no decimales [1-si|0-no]
* Devuelve:
*  true-Todo correcto
*  false-Incorrecto
*/

function validate_importe(value,decimal)

{

	if(decimal==undefined)

		decimal=0;



	if(decimal==1)

	{

		// Permite decimales tanto por . como por ,

		var patron=new RegExp("^[0-9]+((,|\.)[0-9]{1,2})?$");

	}else{

		// Numero entero normal

		var patron=new RegExp("^([0-9])*$")

	}



	if(value && value.search(patron)==0)

	{

		return true;

	}

	return false;

}

function isNumberKey(evt)
{
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;

	return true;
}

function llenarnumero(e)
{
	obj=document.getElementById('num_fin');

	if(obj.value=="" || obj.value==0)
	{
		value="9999999999";
		window.parent.document.getElementById('num_fin').readOnly = true;
	}
	else
	{
		value=0;
		window.parent.document.getElementById('num_fin').readOnly = false;
	}
	window.parent.document.getElementById('num_fin').value = value;

}