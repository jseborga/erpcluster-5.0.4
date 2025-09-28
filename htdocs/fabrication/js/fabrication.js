function revisaFrame (f)
{
	var idprod = document.getElementById( 'idprod' ).value;
	var lol = f;
	//cambiando el estado de
	document.getElementById('iframe').src= 'search_product.php?idprod='+idprod+'&ref='+lol;
}

function verifFabrication ()
{
    var rowidd = document.getElementById('frmFabrication').rowidd.value;
    var total = parseFloat ( document.getElementById('frmFabrication').qty[rowidd].value );
    var merm = parseFloat ( document.getElementById('frmFabrication').qty_decrease[rowidd].value );
    var first = parseFloat ( document.getElementById('frmFabrication').qty_first[rowidd].value );

	if ( merm > 0 ) {

		resultat = Math.round ( (total - merm) * 100 ) / 100;
		document.getElementById('frmFabrication').qty_first[rowidd].value = resultat.toFixed(2);

	} else if ( first > 0 ) {

		resultat = Math.round ( (total - first) * 100 ) / 100;
		document.getElementById('frmFabrication').qty_decrease[rowidd].value = resultat.toFixed(2);

	} else {

		document.getElementById('frmFabrication').qty_first[rowidd].value = '-';

	}
}




