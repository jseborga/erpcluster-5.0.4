<?php
/* Copyright (C) 2007-2008 Jeremie Ollivier <jeremie.o@laposte.net>
 * Copyright (C) 2008-2011 Laurent Destailleur   <eldy@uers.sourceforge.net>
 * Copyright (C) 2011 Juanjo Menent			  	 <jmenent@2byte.es>
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
 *	\file       htdocs/ventas/consultanit.php
 *	\ingroup    cashdesk
 *	\brief      Include to show main page for cashdesk module
 */

require_once("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryhistoryext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontractext.class.php';

if(
	isset($_GET['fk_payment']) &&
	$_GET['fk_payment'] != null
	)
{
	$objHistory = new Psalaryhistoryext($db);
	$objHistory->fetch($_GET['fk_payment']);
	echo '<hr>user '.$objHistory->fk_user;

	$objContract = new Pcontractext($db);
	$objContract->fetch_vigent($objHistory->fk_user);

	echo '<hr>account '.$objContract->fk_account;
	/*
	 * Aquí haces el resto de script, asegúrate de validar bien
	 * la cédula con la función mysql_real_escape_string() de php 
	 * para evitar todo tipo de injección posible.
	 */
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'fk_account'".').value = "'. $objContract->fk_account.'"'; 
	print '</script>';
	
}

?>
