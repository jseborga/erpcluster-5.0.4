function revisaFramefourn (f)
{
	var idprod = document.getElementById( 'idprodfournprice' ).value;
	var lol = f;
	//cambiando el estado de
	document.getElementById('iframe').src= 'search_product.php?idprod='+idprod+'&ref='+lol;
}
function revisaFrame (f)
{
	var idprod = document.getElementById( 'idprodfournprice' ).value;
	var lol = f;
	//cambiando el estado de
	document.getElementById('iframe').src= 'search_product.php?idprod='+idprod+'&ref='+lol;
}
function CambiaURLFrame(f)
{
	var idprod = document.getElementById('idprod').value;
	var lol = f;

	//cambiando el estado de
	document.getElementById('iframe').src= 'search_product.php?ref='+lol+'&idprod='+idprod;
}
//solonumeros
function checkIt(evt) {
	evt = (evt) ? evt : window.event
	var charCode = (evt.which) ? evt.which : evt.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		status = "This field accepts numbers only."
		return false
	}
	status = ""
	return true
}

