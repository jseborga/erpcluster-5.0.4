/*
	ajax by HTML5 UP
	ubuntu-bo.com | @rqc
*/

function enviaproc()
{
    document.forms["fiche_pasa"].submit();
    
     //Recogemos los valores introducimos en los campos de texto
    reform = document.forml.gestion.value;
    alert(reform);
    //Aquí será donde se mostrará el resultado
    //reformtwo = document.getElementById('jugador');
    
    //instanciamos el objetoAjax
    ajax = objetoAjax();
    
    //Abrimos una conexión AJAX pasando como parámetros el método de envío, y el archivo que realizará las operaciones deseadas
    ajax.open("POST", "actualiza_proc.php", true);
    alert("concluido");
    //cuando el objeto XMLHttpRequest cambia de estado, la función se inicia
    ajax.onreadystatechange = function() {
	
        //Cuando se completa la petición, mostrará los resultados
        if (ajax.readyState == 4){
	    
            //El método responseText() contiene el texto de nuestro 'consultar.php'. Por ejemplo, cualquier texto que mostremos por un 'echo'
            jugador.value = (ajax.responseText)
        }
    }
    
    //Llamamos al método setRequestHeader indicando que los datos a enviarse están codificados como un formulario.
    ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    
    //enviamos las variables a 'consulta.php'
    ajax.send("&reform="+reform)
    
}

function submitform( nameform )
{
    var nform = nameform;
    document.forms["fiche_pasa"].submit();
}
