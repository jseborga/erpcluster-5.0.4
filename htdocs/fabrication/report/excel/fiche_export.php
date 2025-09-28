<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/poa/process/fiche_export.php
 *	\ingroup    Process export excel
 *	\brief      Page fiche poa process export excel
 */
require("../../../main.inc.php");

$archivo = GETPOST('archive');
$ruta = DOL_DOCUMENT_ROOT.'/fabrication/report/excel/'.$archivo;
header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename="'.$archivo.'"');
header('Content-Transfer-Encoding: binary');
readfile($ruta);
?>
