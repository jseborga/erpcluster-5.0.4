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
 *	\file       htdocs/mant/core/modules/mod_mant_numberone.php
 *	\ingroup    Mant
 *	\brief      File containing class for numbering module nomberone
 */
require_once DOL_DOCUMENT_ROOT .'/mant/core/modules/mant/modules_mant.php';

/**	    \class      mod_mant_numberone
 *		\brief      Classe du modele de numerotation de reference de pedidos mant numberone
 */
class mod_mant_numbertwo extends ModeleNumRefMant
{
	var $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	var $prefix='OT';
	var $prefixcreditnote='AV';
	var $error='';
	var $nom='Opcion numeracion 2';

	/**
	 *  Renvoi la description du modele de numerotation
	 *
	 *  @return     string      Texte descripif
	 */
	function info()
	{
		global $langs;
		$langs->load("mant@mant");
		return $langs->trans('Number two Order jobs',$this->prefix,$this->prefixcreditnote);
	}

	/**
	 *  Renvoi un exemple de numerotation
	 *
	 *  @return     string      Example
	 */
	function getExample()
	{
	  return $this->prefix.substr(date('Y'),2,2).(strlen(date('m'))==1?'0'.date('m'):date('m'))."-0001";
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

		// Check invoice num
		$fayymm=''; $max='';
		$aDateday = dol_getdate(dol_now());
		$fayymm = substr($aDateday['year'],2,2);
		$fayymm .= (strlen($aDateday['mon']) <=1?'0'.$aDateday['mon']:$aDateday['mon']);

		$posindice=8;
		$sql = "SELECT MAX(SUBSTRING(ref FROM ".$posindice.")) as max";	// This is standard SQL
		$sql.= " FROM ".MAIN_DB_PREFIX."m_jobs";
		//$sql.= " WHERE ref LIKE '".$this->prefix."____-%'";
		$sql.= " WHERE ref LIKE '".$this->prefix.$fayymm."-%'";
		$sql.= " AND entity = ".$conf->entity;

		$resql=$db->query($sql);
		if ($resql)
		{
			$row = $db->fetch_row($resql);
			if ($row) { $fayymm = substr($row[0],0,6); $max=$row[0]; }
		}
		if ($fayymm && ! preg_match('/'.$this->prefix.'[0-9][0-9][0-9][0-9]/i',$fayymm))
		{
			$langs->load("errors");
			$this->error=$langs->trans('ErrorNumRefModel',$max);
			return false;
		}

		// Check credit note num
		// $fayymm='';

		// $posindice=9;
		// $sql = "SELECT MAX(SUBSTRING(ref FROM ".$posindice.")) as max";	// This is standard SQL
		// $sql.= " FROM ".MAIN_DB_PREFIX."m_equipment";
		// $sql.= " WHERE ref LIKE '".$this->prefixcreditnote."____-%'";
		// $sql.= " AND entity = ".$conf->entity;

		// $resql=$db->query($sql);
		// if ($resql)
		// {
		// 	$row = $db->fetch_row($resql);
		// 	if ($row) { $fayymm = substr($row[0],0,6); $max=$row[0]; }
		// }
		// if ($fayymm && ! preg_match('/'.$this->prefixcreditnote.'[0-9][0-9][0-9][0-9]/i',$fayymm))
		// {
		// 	$this->error=$langs->trans('ErrorNumRefModel',$max);
		// 	return false;
		// }
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
		$prefix=$this->prefix;
		$fayymm=''; $max='';
		$aDateday = dol_getdate(dol_now());
		$fayymm = substr($aDateday['year'],2,2);
		$fayymm .= (strlen($aDateday['mon']) <=1?'0'.$aDateday['mon']:$aDateday['mon']);

		// D'abord on recupere la valeur max
		$posindice=8;
		$sql = "SELECT MAX(SUBSTRING(ref FROM ".$posindice.")) as max";	// This is standard SQL
		$sql.= " FROM ".MAIN_DB_PREFIX."m_jobs";
		//$sql.= " WHERE ref LIKE '".$prefix."____-%'";
		$sql.= " WHERE ref LIKE '".$prefix.$fayymm."-%'";
		$sql.= " AND entity = ".$conf->entity;

		$resql=$db->query($sql);
		dol_syslog("mod_mant_numbertwo::getNextValue sql=".$sql);
		if ($resql)
		{
		  $obj = $db->fetch_object($resql);
		  if ($obj) $max = intval($obj->max);
		  else $max=0;
		}
		else
		  {
		    dol_syslog("mod_mant_numbertwo::getNextValue sql=".$sql, LOG_ERR);
		    return -1;
		  }
		
		if ($mode == 'last')
		  {
		    $num = sprintf("%04s",$max);
		    
		    $ref='';
		    $sql = "SELECT ref as ref";
		    $sql.= " FROM ".MAIN_DB_PREFIX."m_jobs";
		    //$sql.= " WHERE ref LIKE '".$prefix."____-".$num."'";
		    $sql.= " WHERE ref LIKE '".$prefix.$fayymm."-".$num."'";
		    $sql.= " AND entity = ".$conf->entity;
		    
		    dol_syslog("mod_mant_numbertwo::getNextValue sql=".$sql);
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
		    $date=$objsoc->date_create;	// This is invoice date (not creation date)
		    if (empty($date)) $date = strtotime(date('Y-m-d'));
		    $yymm = strftime("%y%m",$date);
		    $num = sprintf("%04s",$max+1);
		    
		    dol_syslog("mod_mant_numbertwo::getNextValue return ".$prefix.$yymm."-".$num);
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
