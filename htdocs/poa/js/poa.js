//<![CDATA[
function toggle(cual) {
    var elElemento=document.getElementById(cual);
    if(elElemento.style.display == 'block') {
	elElemento.style.display = 'none';
    } else {
	elElemento.style.display = 'block';
    }
}
//]]>


//<![CDATA[
function toggleEnlace(accion, cual, valor) {
    if (accion == "mostrar") {
	document.getElementById("pre"+cual).style.display = "block";
	document.getElementById("miEnlace"+cual).href="javascript:toggleEnlace('ocultar',"+cual+")";
	//document.getElementById("miEnlace"+cual).innerHTML = ""+valor;
    }
    if (accion == "ocultar") {
	document.getElementById("pre"+cual).style.display = "none";
	document.getElementById("miEnlace"+cual).href="javascript:toggleEnlace('mostrar',"+cual+","+valor+")";
	//document.getElementById("miEnlace"+cual).innerHTML = ""+valor;
    }
}
//]]>

//<![CDATA[
function toggleEnlacep(accion, cual, valor) {
    if (accion == "mostrar") {
	document.getElementById("pac"+cual).style.display = "block";
	document.getElementById("miEnlacep"+cual).href="javascript:toggleEnlacep('ocultar',"+cual+")";
	//document.getElementById("miEnlace"+cual).innerHTML = ""+valor;
    }
    if (accion == "ocultar") {
	document.getElementById("pac"+cual).style.display = "none";
	document.getElementById("miEnlacep"+cual).href="javascript:toggleEnlacep('mostrar',"+cual+","+valor+")";
	//document.getElementById("miEnlace"+cual).innerHTML = ""+valor;
    }
}
//]]>
window.onload = function(){/*hace que se cargue la función lo que predetermina que div estará oculto hasta llamar a la función nuevamente*/
muestra_oculta('menu_mostrar');/* "contenido_a_mostrar" es el nombre que le dimos al DIV */
}
