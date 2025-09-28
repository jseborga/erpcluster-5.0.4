/*
	Minimaxing 3.0 by HTML5 UP
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
	*/

/*
function ocultarFila(num,ver) {
  dis= ver ? '' : 'none';
  tab=document.getElementById('tabla');
  tab.getElementsByTagName('tr')[num].style.display=dis;
}
*/

function getElement(id)
{
	if (document.getElementById)
	{
		return document.getElementById(id);
	}
	else if (document.all)
	{
		return window.document.all[id];
	}
	else if (document.layers)
	{
		return window.document.layers[id];
	}
}

function visual_menu(id,idtwo)
{
	var idone = id;
	var idtwoo = idtwo;
//mostrar
document.getElementById(idone).style.visibility="visible";
document.getElementById(idone).style.display="block";
//ocultar
document.getElementById(idtwoo).style.visibility="hidden";
document.getElementById(idtwoo).style.display="none";
}

function visual_one(id,idtwo)
{
	var idone = id;
	var idtwo = idtwo;
	var idone_ = id + '_';
	var idtwo_ = idtwo + '_';
//mostrar
document.getElementById(idone).style.visibility="visible";
document.getElementById(idone).style.display="block";
document.getElementById(idone_).style.visibility="visible";
document.getElementById(idone_).style.display="block";
//ocultar
document.getElementById(idtwo).style.visibility="hidden";
document.getElementById(idtwo).style.display="none";
document.getElementById(idtwo_).style.visibility="hidden";
document.getElementById(idtwo_).style.display="none";
}
function visual_two(id,idtwo)
{
	var idone = id;
	var idtwo = idtwo;
	var idone_ = id+'x';
	var idtwo_ = idtwo+'x';
//mostrar
document.getElementById(idone).style.visibility="visible";
document.getElementById(idone).style.display="block";
document.getElementById(idone_).style.visibility="visible";
document.getElementById(idone_).style.display="block";
//ocultar
document.getElementById(idtwo).style.visibility="hidden";
document.getElementById(idtwo).style.display="none";
document.getElementById(idtwo_).style.visibility="hidden";
document.getElementById(idtwo_).style.display="none";
}
function visual_tree(id,idtwo)
{
	var idone = id;
	var idtwoo = idtwo;
//mostrar
document.getElementById(idone).style.visibility="visible";
document.getElementById(idone).style.display="block";
//ocultar
document.getElementById(idtwoo).style.visibility="hidden";
document.getElementById(idtwoo).style.display="none";
}
function visual_str(ida,idb,idc,i)
{
	if (i > 0)
	{
		var idone = 'pl'+ida+'_'+idb+''+idc;
		var idtwoo = idone+'p';
	}
	else
	{
		var idtwoo = 'pl'+ida+'_'+idb+''+idc;
		var idone = idtwoo+'p';	
	}

	//mostrar
	document.getElementById(idone).style.visibility="visible";
	document.getElementById(idone).style.display="block";
//ocultar
document.getElementById(idtwoo).style.visibility="hidden";
document.getElementById(idtwoo).style.display="none";
}

function visual_four(id,idtwo)
{
	var idone = id;
	var idtwoo = idtwo;
//mostrar
document.getElementById(idone).style.visibility="visible";
document.getElementById(idone).style.display="block";
//ocultar
document.getElementById(idtwoo).style.visibility="hidden";
document.getElementById(idtwoo).style.display="none";
}

function visual_five(id,idtwo)
{
	var idone = id;
	var idtwoo = idtwo;
//mostrar
document.getElementById(idone).style.visibility="visible";
document.getElementById(idone).style.display="block";
//ocultar
document.getElementById(idtwoo).style.visibility="hidden";
document.getElementById(idtwoo).style.display="none";
}


function ocultarColumna(num,ver) 
{
	dis= ver ? 'block' : 'none';
	fila=document.getElementById('section-body').getElementsByTagName('span');
	for(i=0;i<fila.length;i++)
	{
		fila[i].getElementsByTagName('div')[num].style.display=dis;
	}
}

function muestra_oculta(id,num)
{
	var dis = '';
	if(document.getElementById)
	{ //se obtiene el id
		var el = document.getElementById(id);
		el.style.display = (el.style.display = 'none') ? 'block' : 'none';
		//var div1 = document.getElementById(id);
		//var fila=document.getElementsByTagName('span');
		//for(i=0;i<fila.length;i++)
		//{
		//	if (fila[i].getAttribute("style") = 'block')
		//	{
		//		fila[i].setAttribute("style") = 'none';
		//	}
		//	else
		//	{
		//		fila[i].setAttribute("style") = 'block';
		//	}
		//}
	}
}

// Función que suma o resta días a la fecha indicada

function sumarfecha(d, fecha)
{
	var Fecha = new Date();
	var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
	var sep = sFecha.indexOf('/') != -1 ? '/' : '-'; 
	var aFecha = sFecha.split(sep);
	var fecha = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
	fecha= new Date(fecha);
	fecha.setDate(fecha.getDate()+parseInt(d));
	var anno=fecha.getFullYear();
	var mes= fecha.getMonth()+1;
	var dia= fecha.getDate();
	mes = (mes < 10) ? ("0" + mes) : mes;
	dia = (dia < 10) ? ("0" + dia) : dia;
	var fechaFinal = dia+sep+mes+sep+anno;
	return (fechaFinal);
}

function formsubmit()
{
	document.myform.submit();
}
window.onload = function()
{

}

