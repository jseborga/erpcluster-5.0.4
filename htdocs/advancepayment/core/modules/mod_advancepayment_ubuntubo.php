<?php
/* Copyright (C) 2005-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 * or see http://www.gnu.org/
 */

/**
 *	\file       htdocs/almacen/core/modules/mod_almacen_ubuntubo.php
 *	\ingroup    almacenes
 *	\brief      File containing class for numbering module Ubuntubo
 */
require_once DOL_DOCUMENT_ROOT .'/advancepayment/core/modules/advancepayment/modules_advancepayment.php';

/**	    \class      mod_almacen_ubuntubo
 *		\brief      Classe du modele de numerotation de reference de pedidos almacen ubuntubo
 */
class mod_advancepayment_ubuntubo extends ModeleNumRefAdvancePayment
{
	var $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	var $prefixinvoice='ADVP';
	var $prefixcreditnote='SV';
	var $error='';
	var $nom='ubuntubosol';

	/**
	 *  Renvoi la description du modele de numerotation
	 *
	 *  @return     string      Texte descripif
	 */
	function info()
	{
		global $langs;
		$langs->load("advancepayment@advancepayment");
		return $langs->trans('Numeración para anticipos a proveedores',$this->prefixinvoice,$this->prefixcreditnote);
	}

	/**
	 *  Renvoi un exemple de numerotation
	 *
	 *  @return     string      Example
	 */
	function getExample()
	{
		return $this->prefixinvoice."0501-0001";
	}

	/**
	 *  Test si les numeros deja en vigueur dans la base ne provoquent pas de
	 *  de conflits qui empechera cette numerotation de fonctionner.
	 *
	 *  @return     boolean     false si conflit, true si ok
	 */
	function canBeActivated()
	{
		global $langs,$conf;

		$langs->load("bills");
		$langs->load("advancepayment@advancepayment");

		// Check invoice num
		$fayymm=''; $max='';
		$aDate = dol_getdate(dol_now());
		$fayymm = substr($aDate['year'],2,2).(strlen($aDate['mon'])==1?'0'.$aDate['mon']:$aDate['mon']);

		$posindice=10;
		$sql = "SELECT MAX(SUBSTRING(ref FROM ".$posindice.")) as max";	// This is standard SQL
		$sql.= " FROM ".MAIN_DB_PREFIX."paiementfourn_advance";
		$sql.= " WHERE ref LIKE '".$this->prefixinvoice.$fayymm."-%'";
		$sql.= " AND entity = ".$conf->entity;

		$resql=$db->query($sql);
		if ($resql)
		{
			$row = $db->fetch_row($resql);
			if ($row) { $fayymm = substr($row[0],0,6); $max=$row[0]; }
		}
		if ($fayymm && ! preg_match('/'.$this->prefixinvoice.'[0-9][0-9][0-9][0-9]/i',$fayymm))
		{
			$langs->load("errors");
			$this->error=$langs->trans('ErrorNumRefModel',$max);
			return false;
		}

		// Check credit note num
		$fayymm='';
		$aDate = dol_getdate(dol_now());
		$fayymm = substr($aDate['year'],2,2).(strlen($aDate['mon'])==1?'0'.$aDate['mon']:$aDate['mon']);

		$posindice=10;
		$sql = "SELECT MAX(SUBSTRING(ref FROM ".$posindice.")) as max";	// This is standard SQL
		$sql.= " FROM ".MAIN_DB_PREFIX."paiementfourn_advance";
		$sql.= " WHERE ref LIKE '".$this->prefixcreditnote.$fayymm."-%'";
		$sql.= " AND entity = ".$conf->entity;

		$resql=$db->query($sql);
		if ($resql)
		{
			$row = $db->fetch_row($resql);
			if ($row) { $fayymm = substr($row[0],0,6); $max=$row[0]; }
		}
		if ($fayymm && ! preg_match('/'.$this->prefixcreditnote.'[0-9][0-9][0-9][0-9]/i',$fayymm))
		{
			$this->error=$langs->trans('ErrorNumRefModel',$max);
			return false;
		}

		return true;
	}

	/**
	 * Return next value not used or last value used
	 *
	 * @param	Societe		$objsoc		Object third party
	 * @param   Facture		$facture	Object invoice
	 * @param   string		$mode       'next' for next value or 'last' for last value
	 * @return  string       			Value
	 */
	function getNextValue($objsoc,$facture,$mode='next')
	{
		global $db,$conf;
		if ($facture->type == 2) $prefix=$this->prefixcreditnote;
		else $prefix=$this->prefixinvoice;

		// D'abord on recupere la valeur max
		$date=$facture->date_creation;
		if (!empty($date))
			$aDateday = dol_getdate($date);
		else
			$aDateday = dol_getdate(dol_now());
		$fayymm = substr($aDateday['year'],2,2);
		$fayymm .= (strlen($aDateday['mon']) <=1?'0'.$aDateday['mon']:$aDateday['mon']);
		$posindice=10;
		$sql = "SELECT MAX(SUBSTRING(ref FROM ".$posindice.")) as max";	// This is standard SQL
		$sql.= " FROM ".MAIN_DB_PREFIX."paiementfourn_advance";
		$sql.= " WHERE ref LIKE '".$prefix."____-%'";
		$sql.= " AND entity = ".$conf->entity;
		$resql=$db->query($sql);
		dol_syslog("mod_advancepayment_ubuntubo::getNextValue sql=".$sql);
		if ($resql)
		{
			$obj = $db->fetch_object($resql);
			if ($obj) $max = intval($obj->max);
			else $max=0;
		}
		else
		{
			dol_syslog("mod_advancepayment_ubuntubo::getNextValue sql=".$sql, LOG_ERR);
			return -1;
		}
		
		if ($mode == 'last')
		{
			$num = sprintf("%04s",$max);

			$ref='';
			$sql = "SELECT ref as ref";
			$sql.= " FROM ".MAIN_DB_PREFIX."paiementfourn_advance";
			$sql.= " WHERE ref LIKE '".$prefix."____-".$num."'";
			$sql.= " AND entity = ".$conf->entity;
			
			dol_syslog("mod_advancepayment_ubuntubo::getNextValue sql=".$sql);
			$resql=$db->query($sql);
			if ($resql)
			{
				$obj = $db->fetch_object($resql);
				if ($obj) $ref = $obj->ref;
			}
			else dol_print_error($db);
			
			return $ref;
		}
		else if ($mode == 'next')
		{
			$date=$facture->date_create;
				// This is invoice date (not creation date)
			if (empty($date)) $date = dol_now();
			$yymm = strftime("%y%m",$date);
			$num = sprintf("%04s",$max+1);
			
			dol_syslog("mod_advancepayment_ubuntubo::getNextValue return ".$prefix.$yymm."-".$num);
			return $prefix.$yymm."-".$num;
		}
		else dol_print_error('','Bad parameter for getNextValue');
	}
	
	/**
	 * Return next free value
	 *
	 * @param	Societe		$objsoc     	Object third party
	 * @param	string		$objforref		Object for number to search
	 * @param   string		$mode       	'next' for next value or 'last' for last value
	 * @return  string      				Next free value
	 */
	function getNumRef($objsoc,$objforref,$mode='next')
	{
		return $this->getNextValue($objsoc,$objforref,$mode);
	}

}

?>
