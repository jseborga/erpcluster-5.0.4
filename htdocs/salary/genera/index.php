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
require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacen.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendet.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabrication.class.php");

$langs->load("stocks");
$langs->load("almacen@almacen");
$langs->load("fabrication@fabrication");

if (!$user->rights->almacen->leerpedido)
  accessforbidden();

llxHeader("",$langs->trans("ListStockToApplications"),$help_url);


print '<div><p>Modulo de Reportes de Planilla de Sueldos</p></div>';

$db->close();

llxFooter();
?>
