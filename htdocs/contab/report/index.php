<?php
/* Copyright (C) 2001-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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

/**
 *	\file       htdocs/product/index.php
 *  \ingroup    product
 *  \brief      Page accueil des produits et services
 */

require '../../main.inc.php';

$type=isset($_GET["type"])?$_GET["type"]:(isset($_POST["type"])?$_POST["type"]:'');

// Security check
//$result=restrictedArea($user,'contab');

$langs->load("contab");


llxHeader('',$langs->trans('Reports'));

print_barre_liste($langs->trans("Contabilidad Dolibarr"), $page, "index.php","&socid=$socid",$sortfield,$sortorder,'',$num);


print '<table width="auto">';

//colonne gauche
print '<tr><td width=auto>';
print '<table class="noborder" width="400px">';
print '<tr class="liste_titre"><td colspan=4>'.$langs->trans("Contabilidad").'</td></tr>';

print '<tr><td>Seleccione una de ls opciones del menu izquierdo';
// fin colonne droite
print '</td></tr></table>';
$db->close();

llxFooter('$Date: 2010-03-28 19:06:42 +0200 (dim. 28 mars 2010) $ - $Revision: 51 $');

?>
