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

require '../main.inc.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobs.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';

$type=isset($_GET["type"])?$_GET["type"]:(isset($_POST["type"])?$_POST["type"]:'');

$langs->load("finint@finint");

//$object = new Mjobs($db);

/*
 * View
 */

$transAreaType = $langs->trans("Finint");
$helpurl='';
$helpurl='EN:Module_Mant|FR:Module_Mant|ES:M&oacute;dulo_Mant';

llxHeader("",$langs->trans("Finint"),$helpurl);

print_fiche_titre($transAreaType);

//print '<table border="0" width="100%" class="notopnoleftnoright">';
//print '<tr><td valign="top" width="30%" class="notopnoleft">';
print '<div class="fichecenter"><div class="fichethirdleft">';


/*
 * Zone recherche produit/service
 */
$rowspan=2;




//print '</td></tr></table>';
print '</div>';

print '</div>';

llxFooter();

$db->close();


?>
