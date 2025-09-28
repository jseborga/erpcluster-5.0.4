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
require_once DOL_DOCUMENT_ROOT .'/contab/core/modules/contab/modules_contab.php';

/**	    \class      mod_almacen_ubuntubo
 *		\brief      Classe du modele de numerotation de reference de pedidos almacen ubuntubo
 */
class mod_contab_ubuntubo extends ModeleNumRefContab
{
	var $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	var $lotenum = '001000';
	var $sblotenum = '001';
	var $prefixinvoice='C';
	var $prefixcreditnote='A';
	var $error='';
	var $nom='ubuntubo';

	/**
	 *  Renvoi la description du modele de numerotation
	 *
	 *  @return     string      Texte descripif
	 */
	function info()
	{
		global $langs;
		$langs->load("contab");
		return $langs->trans('UbuntuboNumRefModelDescriptionseats',$this->lotenum,$this->sblotenum);
	}

	/**
	 *  Renvoi un exemple de numerotation
	 *
	 *  @return     string      Example
	 */
	function getExample()
	{
		return $this->lotenum."-".$this->sblotenum."-00001";
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
	  
	  $posindice=6;
	  $sql = "SELECT MAX(SUBSTRING(doc FROM ".$posindice.")) as max";	// This is standard SQL
	  $sql.= " FROM ".MAIN_DB_PREFIX."sol_almacen";
	  $sql.= " WHERE lote LIKE '".$this->lotenum."' ";
	  $sql.= " AND sblote LIKE '".$this->sblotenum."' ";
	  echo $sql.= " AND entity = ".$conf->entity;
	  
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
		$prefix = $this->lotenum.'-'.$this->sblotenum;
		// D'abord on recupere la valeur max
		$posindice=6;
		$sql = "SELECT MAX(SUBSTRING(doc FROM ".$posindice.")) as max";	// This is standard SQL
		$sql.= " FROM ".MAIN_DB_PREFIX."contab_seat";
		$sql.= " WHERE lote LIKE '".$this->lotenum."' ";
		$sql.= " AND sblote LIKE '".$this->sblotenum."' ";
		$sql.= " AND entity = ".$conf->entity;

		$resql=$db->query($sql);
		dol_syslog("mod_contab_ubuntubo::getNextValue sql=".$sql);
		if ($resql)
		{
		  $obj = $db->fetch_object($resql);
		  if ($obj) $max = intval($obj->max);
		  else $max=0;
		}
		else
		  {
		    dol_syslog("mod_contab_ubuntubo::getNextValue sql=".$sql, LOG_ERR);
		    return -1;
		  }
		
		if ($mode == 'last')
		  {
		    $num = sprintf("%06s",$max);
		    
		    $ref='';
		    $sql = "SELECT doc as doc";
		    $sql.= " FROM ".MAIN_DB_PREFIX."contab_seat";
		    $sql.= " WHERE lote LIKE '".$this->lotenum."' ";
		    $sql.= " AND sblote LIKE '".$this->sblotenum."' ";
		    $sql.= " AND entity = ".$conf->entity;
		    
		    dol_syslog("mod_contab_ubuntubo::getNextValue sql=".$sql);
		    $resql=$db->query($sql);
		    if ($resql)
		      {
			$obj = $db->fetch_object($resql);
			if ($obj) $doc = $obj->doc;
		      }
		    else dol_print_error($db);
		    
		    return array($this->lotenum,$this->sblotenum,$doc);
		  }
		else if ($mode == 'next')
		  {
		    $date=$facture->date_creation;	// This is invoice date (not creation date)
		    if (empty($date)) $date = date('d-m-Y');
		    $yymm = strftime("%y%m",$date);
		    $num = sprintf("%06s",$max+1);
		    
		    dol_syslog("mod_contab_ubuntubo::getNextValue return ".$prefix.$yymm."-".$num);
		    //return $prefix.$yymm."-".$num;
		    return array($this->lotenum,$this->sblotenum,$num);
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
