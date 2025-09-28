<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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

/**
 *      \file       htdocs/salary/index.php
 *      \ingroup    Salary
 *      \brief      Page index de salary
 */

require("../../main.inc.php");
//dol_include_once('/wages/class/csindexes.class.php');

$langs->load("stocks");
$langs->load("almacen@almacen");

if (!$user->rights->contab->lire)
  accessforbidden();

list($country,$countrycod,$countryname) = explode(':',$conf->global->MAIN_INFO_SOCIETE_COUNTRY) ;
//search last exchange rate
// $objectcop = new Csindexes($db);
// $objectcop->fetch_last($country);

// if ($objectcop->date_ind <> $db->jdate(date('Y-m-d')))
//   {
//     header("Location: ".DOL_URL_ROOT.'/wages/exchangerate/fiche.php?action=create');
//     exit;
//   }

llxHeader("",$langs->trans("Books"),$help_url);


print '<div><p>'.$langs->trans('Books').'</p></div>';

$db->close();

llxFooter();
?>
