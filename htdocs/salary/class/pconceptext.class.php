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
 *  \file       dev/skeletons/pconcept.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-02-14 18:41
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/salary/class/pconcept.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Pconceptext extends Pconcept
{

	//MODIFICACIONES
	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength		Max length for labels (0=no limit)
	 *  @return string           		HTML string with select
	 */
	function select_concept($selected='',$htmlname='fk_concept',$htmloption='',$maxlength=0,$showempty=0,$filter="")
	{
		global $conf,$langs;
		$langs->load("salary@salary");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT c.rowid, c.ref as code_iso, c.detail as label";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_concept AS c ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_type_fol AS d ON c.fk_codfol = d.rowid ";
		$sql.= " WHERE d.entity = ".$conf->entity;
		$sql.= " AND c.entity = ".$conf->entity;
		If (!empty($filter))
		$sql.= " AND ".$filter;
		$sql.= " ORDER BY c.ref ASC";

		dol_syslog(get_class($this)."::select_concept sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out = select_generic($resql,$showempty,$htmlname,$htmloption,'Concept',$selected);
		}
		else
		{
			dol_print_error($this->db);
		}

		return $out;
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	varchar		$ref    ref object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_ref($ref)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.ref,";
		$sql.= " t.detail,";
		$sql.= " t.details,";
		$sql.= " t.type_cod,";
		$sql.= " t.type_mov,";
		$sql.= " t.ref_formula,";
		$sql.= " t.calc_oblig,";
		$sql.= " t.calc_afp,";
		$sql.= " t.calc_rciva,";
		$sql.= " t.calc_agui,";
		$sql.= " t.calc_vac,";
		$sql.= " t.calc_indem,";
		$sql.= " t.calc_afpvejez,";
		$sql.= " t.calc_contrpat,";
		$sql.= " t.calc_afpriesgo,";
		$sql.= " t.calc_aportsol,";
		$sql.= " t.calc_quin,";
		$sql.= " t.print,";
		$sql.= " t.print_input,";
		$sql.= " t.fk_codfol,";
		$sql.= " t.contab_account_ref,";
		$sql.= " t.income_tax,";
		$sql.= " t.percent";


		$sql.= " FROM ".MAIN_DB_PREFIX."p_concept as t";
		$sql.= " WHERE t.ref = ".$ref;
		$sql.= " AND t.entity = ".$conf->entity;

		dol_syslog(get_class($this)."::fetch_ref sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->entity = $obj->entity;
				$this->codref = $obj->ref;
				$this->ref = $obj->rowid;
				$this->detail = $obj->detail;
				$this->details = $obj->details;
				$this->type_cod = $obj->type_cod;
				$this->type_mov = $obj->type_mov;
				$this->ref_formula = $obj->ref_formula;
				$this->calc_oblig = $obj->calc_oblig;
				$this->calc_afp = $obj->calc_afp;
				$this->calc_rciva = $obj->calc_rciva;
				$this->calc_agui = $obj->calc_agui;
				$this->calc_vac = $obj->calc_vac;
				$this->calc_indem = $obj->calc_indem;
				$this->calc_afpvejez = $obj->calc_afpvejez;
				$this->calc_contrpat = $obj->calc_contrpat;
				$this->calc_afpriesgo = $obj->calc_afpriesgo;
				$this->calc_aportsol = $obj->calc_aportsol;
				$this->calc_quin = $obj->calc_quin;
				$this->print = $obj->print;
				$this->print_input = $obj->print_input;
				$this->fk_codfol = $obj->fk_codfol;
				$this->contab_account_ref = $obj->contab_account_ref;
				$this->income_tax = $obj->income_tax;
				$this->percent = $obj->percent;

				$this->db->free($resql);
				return 1;

			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_ref ".$this->error, LOG_ERR);
			return -1;
		}
	}

}
?>
