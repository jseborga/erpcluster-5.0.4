<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/assets/core/modules/mod_assets_ubuntubo.php
 *	\ingroup    assets
 *	\brief      File containing class for numbering module Ubuntubo
 */
require_once DOL_DOCUMENT_ROOT .'/assets/core/modules/assets/modules_assets.php';

/**	    \class      mod_assets_ubuntubo
 *		\brief      Classe du modele de numerotation de reference de fixed assets ubuntubo
 */
class mod_assets_ubuntubo_assign extends ModeleNumRefAssets
{
	var $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	var $prefixasset='ASS';
	var $error='';
	var $nom='ubuntubo_assign';

	/**
	 *  Renvoi la description du modele de numerotation
	 *
	 *  @return     string      Texte descripif
	 */
	function info()
	{
		global $langs;
		$langs->load("assets@assets");
		return $langs->trans('UbuntuboNumRefModelDesc2',$this->prefixinvoice,$this->prefixcreditnote);
	}

	/**
	 *  Renvoi un exemple de numerotation
	 *
	 *  @return     string      Example
	 */
	function getExample()
	{
		return $this->prefixinvoice."ASS0501-0001";
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

		$nPosindice=8;
		$prefixasset = $this->prefixasset;
		$nLenprefix = STRLEN($prefixasset);
		$nLentext = $nPosindice - $nLenprefix;
		$cText = '';
		for ($a= $nLentext; $a <= $nLentext;$a--)
		  {
		    $cText.= '_';
		  }
		$sql = "SELECT MAX(SUBSTRING(ref FROM ".$nPosindice.")) as max";	// This is standard SQL
		$sql.= " FROM ".MAIN_DB_PREFIX."assets_assignment";
		$sql.= " WHERE ref LIKE '".$this->prefixasset."$cText-%'";
		$sql.= " AND entity = ".$conf->entity;

		$resql=$db->query($sql);
		if ($resql)
		  {
		    $row = $db->fetch_row($resql);
		    if ($row) { $fayymm = substr($row[0],0,6); $max=$row[0]; }
		  }
		if ($fayymm && ! preg_match('/'.$this->prefixasset.'[0-9][0-9][0-9][0-9]/i',$fayymm))
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
	  $prefix=$this->prefixasset; //prefijo y type_group
	  $nPosindice=9;
	  $prefixasset = $prefix;
	  $nLenprefix = STRLEN($prefixasset);
	  $nLentext = $nPosindice - $nLenprefix;
	  $cText = '';
	  for ($a= $nLentext; $a > 0; $a--)
	    {
	      $cText.= '_';
	    }
	  // D'abord on recupere la valeur max
	  //$posindice=$conf->global->ASSETS_INDICE;
	  $posindice=$nPosindice;

	  $sql = "SELECT MAX(SUBSTRING(ref FROM ".$posindice.")) as max";	// This is standard SQL
	  $sql.= " FROM ".MAIN_DB_PREFIX."assets_assignment";
	  //	  $sql.= " WHERE ref LIKE '".$prefix."____-%'";
	  $sql.= " WHERE ref LIKE '".$prefix.$cText."%'";
	  $sql.= " AND entity = ".$conf->entity;
	  $resql=$db->query($sql);
	  dol_syslog("mod_assets_ubuntubo_assign::getNextValue sql=".$sql);
	  if ($resql)
	    {
	      $obj = $db->fetch_object($resql);
	      if ($obj) $max = intval($obj->max);
	      else $max=0;
	    }
	  else
	    {
	      dol_syslog("mod_assets_ubuntubo_assign::getNextValue sql=".$sql, LOG_ERR);
	      return -1;
	    }
	  
	  if ($mode == 'last')
	    {
	      $num = sprintf("%04s",$max);
	      
	      $ref='';
	      $sql = "SELECT ref as ref";
	      $sql.= " FROM ".MAIN_DB_PREFIX."assets";
	      //$sql.= " WHERE ref LIKE '".$prefix."____-".$num."'";
	      $sql.= " WHERE ref LIKE '".$prefix.$cText."-%'";
	      $sql.= " AND entity = ".$conf->entity;
	      
	      dol_syslog("mod_assets_ubuntubo_assign::getNextValue sql=".$sql);
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
	      $date=$objsoc->date_assignment;	// This is date assignment (not creation date)
	      if (empty($date)) $date = strtotime(date('Y-m-d'));
	      $yymm = strftime("%y%m",$date);
	      $num = sprintf("%04s",$max+1);
	      
	      dol_syslog("mod_assets_ubuntubo_assign::getNextValue return ".$prefix.$yymm."-".$num);
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
