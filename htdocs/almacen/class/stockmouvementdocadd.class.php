<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *  \file       dev/skeletons/stockmouvementdoc.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2016-08-21 10:22
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementdoc.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Stockmouvementdocadd extends Stockmouvementdoc
{


	//modificado
		/**
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into ALMACEN_ADDON
	 *
	 *  @param	Societe		$soc  	Object thirdparty
	 *  @return string      		Order free reference
	 */
	function getNextNumRef($soc)
	{
		global $db, $langs, $conf;
		$langs->load("almacen@almacen");

		$dir = DOL_DOCUMENT_ROOT . "/almacen/core/modules";

	  // if (! empty($conf->global->ALMACEN_ADDON))
	  //   {
		$file = "mod_almacen_ubuntubo_stockdoc.php";
	  // Chargement de la classe de numerotation
		$classname = "mod_almacen_ubuntubo_stockdoc";
		$result=include_once $dir.'/'.$file;
		if ($result)
		{
			$obj = new $classname();
			$numref = "";
			$numref = $obj->getNextValue($soc,$this);

			if ( $numref != "")
			{
				return $numref;
			}
			else
			{
				dol_print_error($db,"Stockmouvementdoc::getNextNumRef ".$obj->error);
				return "";
			}
		}
		else
		{
			print $langs->trans("Error")." ".$langs->trans("Error_TRANSFDOC_ADDON_NotDefined");
			return "";
		}
	}

	function fetchlines($filter='',$statut='')
	{
		require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtemp.class.php';
		$temp = new Stockmouvementtemp($this->db);
		$res = $temp->getlist($this->ref,$statut,$filter,$nameorder,$order);
		if ($res>0)
			return $temp->array;
		return array();
	}
}

?>
