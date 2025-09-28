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
class Stockmouvementdocext extends Stockmouvementdoc
{

		/**
	 *  Create a document onto disk according to template model.
	 *
	 *  @param	    string		$modele			Force template to use ('' to not force)
	 *  @param		Translate	$outputlangs	Object lang to use for traduction
	 *  @param      int			$hidedetails    Hide details of lines
	 *  @param      int			$hidedesc       Hide description
	 *  @param      int			$hideref        Hide ref
	 *  @return     int          				0 if KO, 1 if OK
	 */
		public function generateDocument($modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
		{
			global $conf, $user, $langs;

			$langs->load("almacen");

		// Sets the model on the model name to use
			if (! dol_strlen($modele))
			{
				$modele = $this->model_pdf;
			}

			$modelpath = "almacen/core/modules/doc/";

			return $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref);
		}

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

			$file = "mod_almacen_ubuntubo_stockdoc.php";

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
			require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtempext.class.php';
			$temp = new Stockmouvementtempext($this->db);
			$res = $temp->getlist($this->ref,$statut,$filter,$nameorder,$order);
			if ($res>0) return $temp->array;
			else
			{
				//buscamos si se realizo en stock_mouvement
				require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementaddext.class.php';
				require_once DOL_DOCUMENT_ROOT.'/almacen/class/mouvementstockext.class.php';
				$temp = new Stockmouvementaddext($this->db);
				$obj = new MouvementStockext($this->db);
				$filtertmp = " AND t.fk_stock_mouvement_doc = ".$this->id;
				$res = $temp->fetchAll('','',0,0,array(1=>1),'AND',$filtertmp);
				if ($res >0)
				{
					$array = array();
					//vamos a obtener el movimiento que se genera en stock_mouvement en base a la lista de $temp
					$lines = $temp->lines;
					foreach ($lines AS $j => $line)
					{
						$ress = $obj->fetch($line->fk_stock_mouvement);
						if ($ress>0)
						{

							$objnew = new Stdclass();
							$objnew->id    = $obj->rowid;
							$objnew->entity = $this->entity;
							$objnew->ref = $this->ref;
							$objnew->tms = $this->db->jdate($obj->tms);
							$objnew->datem = $this->db->jdate($obj->datem);
							$objnew->fk_product = $obj->product_id;
							$objnew->fk_entrepot = $obj->warehouse_id;
							$objnew->fk_type_mov = $obj->fk_type_mov;
							$objnew->value = $obj->qty;
							$objnew->quant = $obj->qty;
							$objnew->price = $obj->price;
							$objnew->type_mouvement = $obj->type;
							$objnew->fk_user_author = $obj->fk_user_author;
							$objnew->label = $obj->label;
							$objnew->fk_origin = $obj->fk_origin;
							$objnew->origintype = $obj->origintype;
							$objnew->inventorycode = $obj->inventorycode;
							$objnew->batch = $obj->batch;
							$objnew->eatby = $this->db->jdate($obj->eatby);
							$objnew->sellby = $this->db->jdate($obj->sellby);
							$objnew->statut = $obj->statut;
							$array[$obj->rowid] = $objnew;

						}
					}
					return $array;
				}
			}
			return array();
		}

		function fetchlinesadd($filter='',$statut='')
		{
			require_once DOL_DOCUMENT_ROOT.'/almacen/class/mouvementstockext.class.php';
			$temp = new MouvementStockext($this->db);
			$res = $temp->fetchAll('', '', 0, 0, array(1=>1), 'AND', $filter);
			if ($res>0)
				return $temp->lines;
			return array();
		}

	/**
	 * Load object in memory from the database
	 *
	 * @param string $sortorder Sort Order
	 * @param string $sortfield Sort field
	 * @param int    $limit     offset limit
	 * @param int    $offset    offset limit
	 * @param array  $filter    filter array
	 * @param string $filtermode filter mode (AND or OR)
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function fetchAll_type($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.ref_ext,";
		$sql .= " t.fk_entrepot_from,";
		$sql .= " t.fk_entrepot_to,";
		$sql .= " t.fk_departament,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_type_mov,";
		$sql .= " t.fk_source,";
		$sql .= " t.model_pdf,";
		$sql .= " t.datem,";
		$sql .= " t.label,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.statut";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."c_type_mouvement AS m ON t.fk_type_mov = m.rowid";
		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' WHERE ' . implode(' '.$filtermode.' ', $sqlwhere);
		}

		if ($filterstatic) $sql.= $filterstatic;

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new StockmouvementdocLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->ref_ext = $obj->ref_ext;
				$line->fk_entrepot_from = $obj->fk_entrepot_from;
				$line->fk_entrepot_to = $obj->fk_entrepot_to;
				$line->fk_departament = $obj->fk_departament;
				$line->fk_soc = $obj->fk_soc;
				$line->fk_type_mov = $obj->fk_type_mov;
				$line->fk_source = $obj->fk_source;
				$line->model_pdf = $obj->model_pdf;
				$line->datem = $this->db->jdate($obj->datem);
				$line->label = $obj->label;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->date_mod = $this->db->jdate($obj->date_mod);
				$line->tms = $this->db->jdate($obj->tms);
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->statut = $obj->statut;

				if ($lView)
				{
					if ($num == 1) $this->fetch($obj->rowid);
				}

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}


	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->statut,$mode);
	}
	/**
	 *  Renvoi le libelle d'un status donne
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return string 			       	Label of status
	 */
	function LibStatut($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Pending');
			if ($status == 0) return $langs->trans('Returned');
			if ($status == 2) return $langs->trans('Closed');
			if ($status == -1) return $langs->trans('Annulled');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Pending');
			if ($status == 0) return $langs->trans('Returned');
			if ($status == 2) return $langs->trans('Closed');
			if ($status == -1) return $langs->trans('Annulled');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Pending');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Returned');
			if ($status == 2) return img_picto($langs->trans('Disabled'),'statut8').' '.$langs->trans('Closed');
			if ($status == -1) return img_picto($langs->trans('Annulled'),'statut0').' '.$langs->trans('Annulled');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Pending'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Returned'),'statut5');
			if ($status == 2) return img_picto($langs->trans('Closed'),'statut8');
			if ($status == -1) return img_picto($langs->trans('Annulled'),'statut0');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Pending'),'statut4').' '.$langs->trans('Pending');
			if ($status == 0) return img_picto($langs->trans('Returned'),'statut5').' '.$langs->trans('Returned');
			if ($status == 2) return img_picto($langs->trans('Closed'),'statut8').' '.$langs->trans('Close');
			if ($status == -1) return img_picto($langs->trans('Annulled'),'statut0').' '.$langs->trans('Annulled');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Pending').' '.img_picto($langs->trans('Pending'),'statut4');
			if ($status == 0) return $langs->trans('Returned').' '.img_picto($langs->trans('Returned'),'statut5');
			if ($status == 2) return $langs->trans('Closed').' '.img_picto($langs->trans('Closed'),'statut8');
			if ($status == -1) return $langs->trans('Annulled').' '.img_picto($langs->trans('Annulled'),'statut0');
		}
	}

}
?>
