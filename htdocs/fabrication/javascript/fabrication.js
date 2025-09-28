
/* Copyright (C) 2007-2008 Jeremie Ollivier <jeremie.o@laposte.net>
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

function revisaFrame (f)
{
	var idprod = document.getElementById( 'idprod' ).value;
	var lol = f;
	//cambiando el estado de
	document.getElementById('iframe').src= 'search_product.php?idprod='+idprod+'&ref='+lol;
}
// Verification de la coherence des informations saisies dans le formulaire de calcul de la difference
function verifFabrication (rowidd) {
	var campoqty = 'qty' + rowidd;
	var qtyd = 'qty_decrease'+rowidd;
	var qtyf = 'qty_first'+rowidd;
	var total = parseFloat(document.getElementById(campoqty).value);
	var merm = parseFloat ( document.getElementById(qtyd).value );
	var first = parseFloat ( document.getElementById(qtyf).value );
	if ( merm >= 0 ) {
		resultat = Math.round ( (total - merm) * 100 ) / 100;
		document.getElementById(qtyf).value = resultat.toFixed(2);
	} else if ( first > 0 ) {
		resultat = Math.round ( (total - first) * 100 ) / 100;
		document.getElementById(qtyd).value = resultat.toFixed(2);
	} else {
		document.getElementById(qtyf).value = '-';
	}
}

