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
if(isset($_GET['ref']) && $_GET['ref'] != null )
{
	//account max
	$sql = "SELECT s.rowid, s.name_vision, s.sequence, s.account ";
	$sql.= " FROM ".MAIN_DB_PREFIX."contab_vision as s";
	$sql.= " WHERE s.ref = '".strtoupper(trim($_GET['ref']))."'";
	$sql.= " AND entity = ".$conf->entity;
	$sql.= " ORDER BY account DESC ";
	$result=$db->query($sql);
	if ($result)
	{
		$num = $db->num_rows($result);
		if ($db->num_rows($result))
		{
			$obj = $db->fetch_object($result);
			$valor = $obj->name_vision;
			$account = $obj->account + 1;
		}
		else
		{
			$valor = '';
			$sequence= '';
			$account = '';
		}
	//$db->free($result);

	}
	else
	{
		$valor = '22222';
	//dol_print_error($db);
	}

	//secuencial
	$sql = "SELECT s.rowid, s.name_vision, s.sequence, s.account ";
	$sql.= " FROM ".MAIN_DB_PREFIX."contab_vision as s";
	$sql.= " WHERE s.ref = '".strtoupper(trim($_GET['ref']))."'";
	$sql.= " AND entity = ".$conf->entity;
	$sql.= " ORDER BY sequence DESC ";
	$result=$db->query($sql);
	if ($result)
	{
		$num = $db->num_rows($result);
		if ($db->num_rows($result))
		{
			$obj = $db->fetch_object($result);
			$valor = $obj->name_vision;
			$sequence = $obj->sequence + 10;
		}
		else
		{
			$valor = '';
			$sequence= 1;
			$account = '';
		}
	//$db->free($result);

	}
	else
	{
		$valor = '22222';
	//dol_print_error($db);
	}

	/*
	 * Aquí haces el resto de script, asegúrate de validar bien
	 * la cédula con la función mysql_real_escape_string() de php
	 * para evitar todo tipo de injección posible.
	 */
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'sequence'".').value = "'. $sequence.'"';
	print '</script>';
	if ($sequence == 1)
	{
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'sequence'".').readOnly = true;';
		print '</script>';
	}
	else
	{
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'sequence'".').readOnly = false;';
		print '</script>';
	}
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'name_vision'".').value = "'. $valor.'"';
	print '</script>';
	if (!empty($valor))
	{
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'name_vision'".').readOnly = true;';
		print '</script>';
	}
	else
	{
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'name_vision'".').readOnly = false;';
		print '</script>';
	}
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'account'".').value = "'. $account.'"';
	print '</script>';

}

?>
