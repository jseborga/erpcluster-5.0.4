<?php
/* Copyright (C) 2009-2010	Erick Bullier	<eb.dev@ebiconsulting.fr>
 * Copyright (C) 2010-2011	Regis Houssin	<regis@dolibarr.fr>
* Copyright (C) 2012       Florian Henry   <florian.henry@open-concept.pro>
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
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*/

/**
 * 	\file		/agefodd/index.php
 * 	\brief		Tableau de bord du module de formation pro. (Agefodd).
* 	\Version	$Id$
*/

/*error_reporting(E_ALL);
 ini_set('display_errors', true);
ini_set('html_errors', false);*/

$res=@include("../main.inc.php");					// For root directory
if (! $res) $res=@include("../../main.inc.php");	// For "custom" directory
if (! $res) die("Include of main fails");

dol_include_once('/contable/class/contable.class.php');

// Security check
//if (!$user->rights->agefodd->lire) accessforbidden();

$langs->load('contable@contable');

llxHeader('',$langs->trans('Contabilidad'));

print_barre_liste($langs->trans("Produccion Dolibarr"), $page, "index.php","&socid=$socid",$sortfield,$sortorder,'',$num);


print '<table width="auto">';

//colonne gauche
print '<tr><td width=auto>';
print '<table class="noborder" width="400px">';
print '<tr class="liste_titre"><td colspan=4>'.$langs->trans("Produccion").'</td></tr>';

print '<tr><td>Seleccione una de ls opciones del menu izquierdo';
// fin colonne droite
print '</td></tr></table>';
$db->close();

llxFooter('$Date: 2010-03-28 19:06:42 +0200 (dim. 28 mars 2010) $ - $Revision: 51 $');

?>