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
 *	\file       htdocs/poa/process/fiche_exce.php
 *	\ingroup    Process export excel
 *	\brief      Page fiche poa process export excel
 */

$ruta = 'excel/exportbcb.xlsx';
header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename="exportbcb.xlsx"');
header('Content-Transfer-Encoding: binary');
//header('Content-Length: '.$filesize($ruta));

//header('Cache-Control: max-age=0');
readfile($ruta);
?>
