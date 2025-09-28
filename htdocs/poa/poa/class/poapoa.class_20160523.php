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
 *  \file       dev/skeletons/poapoa.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-04-16 15:42
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poapoa extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poapoa';			//!< Id that identify managed objects
	var $table_element='poapoa';		//!< Name of table without prefix where object is stored

    var $id;

	var $entity;
	var $gestion;
	var $fk_structure;
	var $ref;
	var $sigla;
	var $label;
	var $pseudonym;
	var $partida;
	var $amount;
	var $classification;
	var $source_verification;
	var $unit;
	var $responsible_one;
	var $responsible_two;
	var $responsible;
	var $m_jan;
	var $m_feb;
	var $m_mar;
	var $m_apr;
	var $m_may;
	var $m_jun;
	var $m_jul;
	var $m_aug;
	var $m_sep;
	var $m_oct;
	var $m_nov;
	var $m_dec;
	var $p_jan;
	var $p_feb;
	var $p_mar;
	var $p_apr;
	var $p_may;
	var $p_jun;
	var $p_jul;
	var $p_aug;
	var $p_sep;
	var $p_oct;
	var $p_nov;
	var $p_dec;
	var $fk_area;
	var $weighting;
	var $fk_poa_reformulated;
	var $version;
	var $statut;
	var $statut_ref;
	var $active;
	var $maxmin;
	var $array;


    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
        return 1;
    }


    /**
     *  Create object into database
     *
     *  @param	User	$user        User that creates
     *  @param  int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return int      		   	 <0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters

		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->fk_structure)) $this->fk_structure=trim($this->fk_structure);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->sigla)) $this->sigla=trim($this->sigla);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->pseudonym)) $this->pseudonym=trim($this->pseudonym);
		if (isset($this->partida)) $this->partida=trim($this->partida);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->classification)) $this->classification=trim($this->classification);
		if (isset($this->source_verification)) $this->source_verification=trim($this->source_verification);
		if (isset($this->unit)) $this->unit=trim($this->unit);
		if (isset($this->responsible_one)) $this->responsible_one=trim($this->responsible_one);
		if (isset($this->responsible_two)) $this->responsible_two=trim($this->responsible_two);
		if (isset($this->responsible)) $this->responsible=trim($this->responsible);
		if (isset($this->m_jan)) $this->m_jan=trim($this->m_jan);
		if (isset($this->m_feb)) $this->m_feb=trim($this->m_feb);
		if (isset($this->m_mar)) $this->m_mar=trim($this->m_mar);
		if (isset($this->m_apr)) $this->m_apr=trim($this->m_apr);
		if (isset($this->m_may)) $this->m_may=trim($this->m_may);
		if (isset($this->m_jun)) $this->m_jun=trim($this->m_jun);
		if (isset($this->m_jul)) $this->m_jul=trim($this->m_jul);
		if (isset($this->m_aug)) $this->m_aug=trim($this->m_aug);
		if (isset($this->m_sep)) $this->m_sep=trim($this->m_sep);
		if (isset($this->m_oct)) $this->m_oct=trim($this->m_oct);
		if (isset($this->m_nov)) $this->m_nov=trim($this->m_nov);
		if (isset($this->m_dec)) $this->m_dec=trim($this->m_dec);
		if (isset($this->p_jan)) $this->p_jan=trim($this->p_jan);
		if (isset($this->p_feb)) $this->p_feb=trim($this->p_feb);
		if (isset($this->p_mar)) $this->p_mar=trim($this->p_mar);
		if (isset($this->p_apr)) $this->p_apr=trim($this->p_apr);
		if (isset($this->p_may)) $this->p_may=trim($this->p_may);
		if (isset($this->p_jun)) $this->p_jun=trim($this->p_jun);
		if (isset($this->p_jul)) $this->p_jul=trim($this->p_jul);
		if (isset($this->p_aug)) $this->p_aug=trim($this->p_aug);
		if (isset($this->p_sep)) $this->p_sep=trim($this->p_sep);
		if (isset($this->p_oct)) $this->p_oct=trim($this->p_oct);
		if (isset($this->p_nov)) $this->p_nov=trim($this->p_nov);
		if (isset($this->p_dec)) $this->p_dec=trim($this->p_dec);
		if (isset($this->fk_area)) $this->fk_area=trim($this->fk_area);
		if (isset($this->weighting)) $this->weighting=trim($this->weighting);
		if (isset($this->fk_poa_reformulated)) $this->fk_poa_reformulated=trim($this->fk_poa_reformulated);
		if (isset($this->version)) $this->version=trim($this->version);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->statut_ref)) $this->statut_ref=trim($this->statut_ref);
		if (isset($this->active)) $this->active=trim($this->active);



		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_poa(";

		$sql.= "entity,";
		$sql.= "gestion,";
		$sql.= "fk_structure,";
		$sql.= "ref,";
		$sql.= "sigla,";
		$sql.= "label,";
		$sql.= "pseudonym,";
		$sql.= "partida,";
		$sql.= "amount,";
		$sql.= "classification,";
		$sql.= "source_verification,";
		$sql.= "unit,";
		$sql.= "responsible_one,";
		$sql.= "responsible_two,";
		$sql.= "responsible,";
		$sql.= "m_jan,";
		$sql.= "m_feb,";
		$sql.= "m_mar,";
		$sql.= "m_apr,";
		$sql.= "m_may,";
		$sql.= "m_jun,";
		$sql.= "m_jul,";
		$sql.= "m_aug,";
		$sql.= "m_sep,";
		$sql.= "m_oct,";
		$sql.= "m_nov,";
		$sql.= "m_dec,";
		$sql.= "p_jan,";
		$sql.= "p_feb,";
		$sql.= "p_mar,";
		$sql.= "p_apr,";
		$sql.= "p_may,";
		$sql.= "p_jun,";
		$sql.= "p_jul,";
		$sql.= "p_aug,";
		$sql.= "p_sep,";
		$sql.= "p_oct,";
		$sql.= "p_nov,";
		$sql.= "p_dec,";
		$sql.= "fk_area,";
		$sql.= "weighting,";
		$sql.= "fk_poa_reformulated,";
		$sql.= "version,";
		$sql.= "statut,";
		$sql.= "statut_ref,";
		$sql.= "active";


        $sql.= ") VALUES (";

		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->gestion)?'NULL':"'".$this->gestion."'").",";
		$sql.= " ".(! isset($this->fk_structure)?'NULL':"'".$this->fk_structure."'").",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->sigla)?'NULL':"'".$this->db->escape($this->sigla)."'").",";
		$sql.= " ".(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").",";
		$sql.= " ".(! isset($this->pseudonym)?'NULL':"'".$this->db->escape($this->pseudonym)."'").",";
		$sql.= " ".(! isset($this->partida)?'NULL':"'".$this->db->escape($this->partida)."'").",";
		$sql.= " ".(! isset($this->amount)?'NULL':"'".$this->amount."'").",";
		$sql.= " ".(! isset($this->classification)?'NULL':"'".$this->db->escape($this->classification)."'").",";
		$sql.= " ".(! isset($this->source_verification)?'NULL':"'".$this->db->escape($this->source_verification)."'").",";
		$sql.= " ".(! isset($this->unit)?'NULL':"'".$this->db->escape($this->unit)."'").",";
		$sql.= " ".(! isset($this->responsible_one)?'NULL':"'".$this->db->escape($this->responsible_one)."'").",";
		$sql.= " ".(! isset($this->responsible_two)?'NULL':"'".$this->db->escape($this->responsible_two)."'").",";
		$sql.= " ".(! isset($this->responsible)?'NULL':"'".$this->db->escape($this->responsible)."'").",";
		$sql.= " ".(! isset($this->m_jan)?'NULL':"'".$this->m_jan."'").",";
		$sql.= " ".(! isset($this->m_feb)?'NULL':"'".$this->m_feb."'").",";
		$sql.= " ".(! isset($this->m_mar)?'NULL':"'".$this->m_mar."'").",";
		$sql.= " ".(! isset($this->m_apr)?'NULL':"'".$this->m_apr."'").",";
		$sql.= " ".(! isset($this->m_may)?'NULL':"'".$this->m_may."'").",";
		$sql.= " ".(! isset($this->m_jun)?'NULL':"'".$this->m_jun."'").",";
		$sql.= " ".(! isset($this->m_jul)?'NULL':"'".$this->m_jul."'").",";
		$sql.= " ".(! isset($this->m_aug)?'NULL':"'".$this->m_aug."'").",";
		$sql.= " ".(! isset($this->m_sep)?'NULL':"'".$this->m_sep."'").",";
		$sql.= " ".(! isset($this->m_oct)?'NULL':"'".$this->m_oct."'").",";
		$sql.= " ".(! isset($this->m_nov)?'NULL':"'".$this->m_nov."'").",";
		$sql.= " ".(! isset($this->m_dec)?'NULL':"'".$this->m_dec."'").",";
		$sql.= " ".(! isset($this->p_jan)?'NULL':"'".$this->p_jan."'").",";
		$sql.= " ".(! isset($this->p_feb)?'NULL':"'".$this->p_feb."'").",";
		$sql.= " ".(! isset($this->p_mar)?'NULL':"'".$this->p_mar."'").",";
		$sql.= " ".(! isset($this->p_apr)?'NULL':"'".$this->p_apr."'").",";
		$sql.= " ".(! isset($this->p_may)?'NULL':"'".$this->p_may."'").",";
		$sql.= " ".(! isset($this->p_jun)?'NULL':"'".$this->p_jun."'").",";
		$sql.= " ".(! isset($this->p_jul)?'NULL':"'".$this->p_jul."'").",";
		$sql.= " ".(! isset($this->p_aug)?'NULL':"'".$this->p_aug."'").",";
		$sql.= " ".(! isset($this->p_sep)?'NULL':"'".$this->p_sep."'").",";
		$sql.= " ".(! isset($this->p_oct)?'NULL':"'".$this->p_oct."'").",";
		$sql.= " ".(! isset($this->p_nov)?'NULL':"'".$this->p_nov."'").",";
		$sql.= " ".(! isset($this->p_dec)?'NULL':"'".$this->p_dec."'").",";
		$sql.= " ".(! isset($this->fk_area)?'NULL':"'".$this->fk_area."'").",";
		$sql.= " ".(! isset($this->weighting)?'NULL':"'".$this->weighting."'").",";
		$sql.= " ".(! isset($this->fk_poa_reformulated)?'NULL':"'".$this->fk_poa_reformulated."'").",";
		$sql.= " ".(! isset($this->version)?'NULL':"'".$this->version."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'").",";
		$sql.= " ".(! isset($this->statut_ref)?'NULL':"'".$this->statut_ref."'").",";
		$sql.= " ".(! isset($this->active)?'NULL':"'".$this->active."'")."";


		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_poa");

			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_CREATE',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
			}
        }

        // Commit or rollback
        if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
            return $this->id;
		}
    }


    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_structure,";
		$sql.= " t.ref,";
		$sql.= " t.sigla,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.classification,";
		$sql.= " t.source_verification,";
		$sql.= " t.unit,";
		$sql.= " t.responsible_one,";
		$sql.= " t.responsible_two,";
		$sql.= " t.responsible,";
		$sql.= " t.m_jan,";
		$sql.= " t.m_feb,";
		$sql.= " t.m_mar,";
		$sql.= " t.m_apr,";
		$sql.= " t.m_may,";
		$sql.= " t.m_jun,";
		$sql.= " t.m_jul,";
		$sql.= " t.m_aug,";
		$sql.= " t.m_sep,";
		$sql.= " t.m_oct,";
		$sql.= " t.m_nov,";
		$sql.= " t.m_dec,";
		$sql.= " t.p_jan,";
		$sql.= " t.p_feb,";
		$sql.= " t.p_mar,";
		$sql.= " t.p_apr,";
		$sql.= " t.p_may,";
		$sql.= " t.p_jun,";
		$sql.= " t.p_jul,";
		$sql.= " t.p_aug,";
		$sql.= " t.p_sep,";
		$sql.= " t.p_oct,";
		$sql.= " t.p_nov,";
		$sql.= " t.p_dec,";
		$sql.= " t.fk_area,";
		$sql.= " t.weighting,";
		$sql.= " t.fk_poa_reformulated,";
		$sql.= " t.version,";
		$sql.= " t.statut,";
		$sql.= " t.statut_ref,";
		$sql.= " t.active";


        $sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;

				$this->entity = $obj->entity;
				$this->gestion = $obj->gestion;
				$this->fk_structure = $obj->fk_structure;
				$this->ref = $obj->ref;
				$this->sigla = $obj->sigla;
				$this->label = $obj->label;
				$this->pseudonym = $obj->pseudonym;
				$this->partida = $obj->partida;
				$this->amount = $obj->amount;
				$this->classification = $obj->classification;
				$this->source_verification = $obj->source_verification;
				$this->unit = $obj->unit;
				$this->responsible_one = $obj->responsible_one;
				$this->responsible_two = $obj->responsible_two;
				$this->responsible = $obj->responsible;
				$this->m_jan = $obj->m_jan;
				$this->m_feb = $obj->m_feb;
				$this->m_mar = $obj->m_mar;
				$this->m_apr = $obj->m_apr;
				$this->m_may = $obj->m_may;
				$this->m_jun = $obj->m_jun;
				$this->m_jul = $obj->m_jul;
				$this->m_aug = $obj->m_aug;
				$this->m_sep = $obj->m_sep;
				$this->m_oct = $obj->m_oct;
				$this->m_nov = $obj->m_nov;
				$this->m_dec = $obj->m_dec;
				$this->p_jan = $obj->p_jan;
				$this->p_feb = $obj->p_feb;
				$this->p_mar = $obj->p_mar;
				$this->p_apr = $obj->p_apr;
				$this->p_may = $obj->p_may;
				$this->p_jun = $obj->p_jun;
				$this->p_jul = $obj->p_jul;
				$this->p_aug = $obj->p_aug;
				$this->p_sep = $obj->p_sep;
				$this->p_oct = $obj->p_oct;
				$this->p_nov = $obj->p_nov;
				$this->p_dec = $obj->p_dec;
				$this->fk_area = $obj->fk_area;
				$this->weighting = $obj->weighting;
				$this->fk_poa_reformulated = $obj->fk_poa_reformulated;
				$this->version = $obj->version;
				$this->statut = $obj->statut;
				$this->statut_ref = $obj->statut_ref;
				$this->active = $obj->active;


            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }


    /**
     *  Update object into database
     *
     *  @param	User	$user        User that modifies
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return int     		   	 <0 if KO, >0 if OK
     */
    function update($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters

		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->fk_structure)) $this->fk_structure=trim($this->fk_structure);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->sigla)) $this->sigla=trim($this->sigla);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->pseudonym)) $this->pseudonym=trim($this->pseudonym);
		if (isset($this->partida)) $this->partida=trim($this->partida);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->classification)) $this->classification=trim($this->classification);
		if (isset($this->source_verification)) $this->source_verification=trim($this->source_verification);
		if (isset($this->unit)) $this->unit=trim($this->unit);
		if (isset($this->responsible_one)) $this->responsible_one=trim($this->responsible_one);
		if (isset($this->responsible_two)) $this->responsible_two=trim($this->responsible_two);
		if (isset($this->responsible)) $this->responsible=trim($this->responsible);
		if (isset($this->m_jan)) $this->m_jan=trim($this->m_jan);
		if (isset($this->m_feb)) $this->m_feb=trim($this->m_feb);
		if (isset($this->m_mar)) $this->m_mar=trim($this->m_mar);
		if (isset($this->m_apr)) $this->m_apr=trim($this->m_apr);
		if (isset($this->m_may)) $this->m_may=trim($this->m_may);
		if (isset($this->m_jun)) $this->m_jun=trim($this->m_jun);
		if (isset($this->m_jul)) $this->m_jul=trim($this->m_jul);
		if (isset($this->m_aug)) $this->m_aug=trim($this->m_aug);
		if (isset($this->m_sep)) $this->m_sep=trim($this->m_sep);
		if (isset($this->m_oct)) $this->m_oct=trim($this->m_oct);
		if (isset($this->m_nov)) $this->m_nov=trim($this->m_nov);
		if (isset($this->m_dec)) $this->m_dec=trim($this->m_dec);
		if (isset($this->p_jan)) $this->p_jan=trim($this->p_jan);
		if (isset($this->p_feb)) $this->p_feb=trim($this->p_feb);
		if (isset($this->p_mar)) $this->p_mar=trim($this->p_mar);
		if (isset($this->p_apr)) $this->p_apr=trim($this->p_apr);
		if (isset($this->p_may)) $this->p_may=trim($this->p_may);
		if (isset($this->p_jun)) $this->p_jun=trim($this->p_jun);
		if (isset($this->p_jul)) $this->p_jul=trim($this->p_jul);
		if (isset($this->p_aug)) $this->p_aug=trim($this->p_aug);
		if (isset($this->p_sep)) $this->p_sep=trim($this->p_sep);
		if (isset($this->p_oct)) $this->p_oct=trim($this->p_oct);
		if (isset($this->p_nov)) $this->p_nov=trim($this->p_nov);
		if (isset($this->p_dec)) $this->p_dec=trim($this->p_dec);
		if (isset($this->fk_area)) $this->fk_area=trim($this->fk_area);
		if (isset($this->weighting)) $this->weighting=trim($this->weighting);
		if (isset($this->fk_poa_reformulated)) $this->fk_poa_reformulated=trim($this->fk_poa_reformulated);
		if (isset($this->version)) $this->version=trim($this->version);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->statut_ref)) $this->statut_ref=trim($this->statut_ref);
		if (isset($this->active)) $this->active=trim($this->active);



		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."poa_poa SET";

		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " gestion=".(isset($this->gestion)?$this->gestion:"null").",";
		$sql.= " fk_structure=".(isset($this->fk_structure)?$this->fk_structure:"null").",";
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " sigla=".(isset($this->sigla)?"'".$this->db->escape($this->sigla)."'":"null").",";
		$sql.= " label=".(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").",";
		$sql.= " pseudonym=".(isset($this->pseudonym)?"'".$this->db->escape($this->pseudonym)."'":"null").",";
		$sql.= " partida=".(isset($this->partida)?"'".$this->db->escape($this->partida)."'":"null").",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
		$sql.= " classification=".(isset($this->classification)?"'".$this->db->escape($this->classification)."'":"null").",";
		$sql.= " source_verification=".(isset($this->source_verification)?"'".$this->db->escape($this->source_verification)."'":"null").",";
		$sql.= " unit=".(isset($this->unit)?"'".$this->db->escape($this->unit)."'":"null").",";
		$sql.= " responsible_one=".(isset($this->responsible_one)?"'".$this->db->escape($this->responsible_one)."'":"null").",";
		$sql.= " responsible_two=".(isset($this->responsible_two)?"'".$this->db->escape($this->responsible_two)."'":"null").",";
		$sql.= " responsible=".(isset($this->responsible)?"'".$this->db->escape($this->responsible)."'":"null").",";
		$sql.= " m_jan=".(isset($this->m_jan)?$this->m_jan:"null").",";
		$sql.= " m_feb=".(isset($this->m_feb)?$this->m_feb:"null").",";
		$sql.= " m_mar=".(isset($this->m_mar)?$this->m_mar:"null").",";
		$sql.= " m_apr=".(isset($this->m_apr)?$this->m_apr:"null").",";
		$sql.= " m_may=".(isset($this->m_may)?$this->m_may:"null").",";
		$sql.= " m_jun=".(isset($this->m_jun)?$this->m_jun:"null").",";
		$sql.= " m_jul=".(isset($this->m_jul)?$this->m_jul:"null").",";
		$sql.= " m_aug=".(isset($this->m_aug)?$this->m_aug:"null").",";
		$sql.= " m_sep=".(isset($this->m_sep)?$this->m_sep:"null").",";
		$sql.= " m_oct=".(isset($this->m_oct)?$this->m_oct:"null").",";
		$sql.= " m_nov=".(isset($this->m_nov)?$this->m_nov:"null").",";
		$sql.= " m_dec=".(isset($this->m_dec)?$this->m_dec:"null").",";
		$sql.= " p_jan=".(isset($this->p_jan)?$this->p_jan:"null").",";
		$sql.= " p_feb=".(isset($this->p_feb)?$this->p_feb:"null").",";
		$sql.= " p_mar=".(isset($this->p_mar)?$this->p_mar:"null").",";
		$sql.= " p_apr=".(isset($this->p_apr)?$this->p_apr:"null").",";
		$sql.= " p_may=".(isset($this->p_may)?$this->p_may:"null").",";
		$sql.= " p_jun=".(isset($this->p_jun)?$this->p_jun:"null").",";
		$sql.= " p_jul=".(isset($this->p_jul)?$this->p_jul:"null").",";
		$sql.= " p_aug=".(isset($this->p_aug)?$this->p_aug:"null").",";
		$sql.= " p_sep=".(isset($this->p_sep)?$this->p_sep:"null").",";
		$sql.= " p_oct=".(isset($this->p_oct)?$this->p_oct:"null").",";
		$sql.= " p_nov=".(isset($this->p_nov)?$this->p_nov:"null").",";
		$sql.= " p_dec=".(isset($this->p_dec)?$this->p_dec:"null").",";
		$sql.= " fk_area=".(isset($this->fk_area)?$this->fk_area:"null").",";
		$sql.= " weighting=".(isset($this->weighting)?$this->weighting:"null").",";
		$sql.= " fk_poa_reformulated=".(isset($this->fk_poa_reformulated)?$this->fk_poa_reformulated:"null").",";
		$sql.= " version=".(isset($this->version)?$this->version:"null").",";
		$sql.= " statut=".(isset($this->statut)?$this->statut:"null").",";
		$sql.= " statut_ref=".(isset($this->statut_ref)?$this->statut_ref:"null").",";
		$sql.= " active=".(isset($this->active)?$this->active:"null")."";


        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
	    	}
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
    }


 	/**
	 *  Delete object in database
	 *
     *	@param  User	$user        User that deletes
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return	int					 <0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$this->db->begin();

		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
		        // want this action calls a trigger.

		        //// Call triggers
		        //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
		        //$interface=new Interfaces($this->db);
		        //$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
		        //if ($result < 0) { $error++; $this->errors=$interface->errors; }
		        //// End call triggers
			}
		}

		if (! $error)
		{
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_poa";
    		$sql.= " WHERE rowid=".$this->id;

    		dol_syslog(get_class($this)."::delete sql=".$sql);
    		$resql = $this->db->query($sql);
        	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}



	/**
	 *	Load an object from its id and create a new one in database
	 *
	 *	@param	int		$fromid     Id of object to clone
	 * 	@return	int					New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Poapoa($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->id=0;
		$object->statut=0;

		// Clear fields
		// ...

		// Create clone
		$result=$object->create($user);

		// Other options
		if ($result < 0)
		{
			$this->error=$object->error;
			$error++;
		}

		if (! $error)
		{


		}

		// End
		if (! $error)
		{
			$this->db->commit();
			return $object->id;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 *	Initialise object with example values
	 *	Id must be 0 if object instance is a specimen
	 *
	 *	@return	void
	 */
	function initAsSpecimen()
	{
		$this->id=0;

		$this->entity='';
		$this->gestion='';
		$this->fk_structure='';
		$this->ref='';
		$this->sigla='';
		$this->label='';
		$this->pseudonym='';
		$this->partida='';
		$this->amount='';
		$this->classification='';
		$this->source_verification='';
		$this->unit='';
		$this->responsible_one='';
		$this->responsible_two='';
		$this->responsible='';
		$this->m_jan='';
		$this->m_feb='';
		$this->m_mar='';
		$this->m_apr='';
		$this->m_may='';
		$this->m_jun='';
		$this->m_jul='';
		$this->m_aug='';
		$this->m_sep='';
		$this->m_oct='';
		$this->m_nov='';
		$this->m_dec='';
		$this->p_jan='';
		$this->p_feb='';
		$this->p_mar='';
		$this->p_apr='';
		$this->p_may='';
		$this->p_jun='';
		$this->p_jul='';
		$this->p_aug='';
		$this->p_sep='';
		$this->p_oct='';
		$this->p_nov='';
		$this->p_dec='';
		$this->fk_area='';
		$this->weighting='';
		$this->fk_poa_reformulated='';
		$this->version='';
		$this->statut='';
		$this->statut_ref='';
		$this->active='';


	}

	//modificado
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function getlist($fk_father)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_structure,";
		$sql.= " t.ref,";
		$sql.= " t.sigla,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.classification,";
		$sql.= " t.source_verification,";
		$sql.= " t.unit,";
		$sql.= " t.responsible_one,";
		$sql.= " t.responsible_two,";
		$sql.= " t.responsible,";
		$sql.= " t.m_jan,";
		$sql.= " t.m_feb,";
		$sql.= " t.m_mar,";
		$sql.= " t.m_apr,";
		$sql.= " t.m_may,";
		$sql.= " t.m_jun,";
		$sql.= " t.m_jul,";
		$sql.= " t.m_aug,";
		$sql.= " t.m_sep,";
		$sql.= " t.m_oct,";
		$sql.= " t.m_nov,";
		$sql.= " t.m_dec,";
		$sql.= " t.p_jan,";
		$sql.= " t.p_feb,";
		$sql.= " t.p_mar,";
		$sql.= " t.p_apr,";
		$sql.= " t.p_may,";
		$sql.= " t.p_jun,";
		$sql.= " t.p_jul,";
		$sql.= " t.p_aug,";
		$sql.= " t.p_sep,";
		$sql.= " t.p_oct,";
		$sql.= " t.p_nov,";
		$sql.= " t.p_dec,";
		$sql.= " t.fk_area,";
		$sql.= " t.weighting,";
		$sql.= " t.fk_poa_reformulated,";
		$sql.= " t.version,";
		$sql.= " t.statut,";
		$sql.= " t.statut_ref";



        $sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
        $sql.= " WHERE t.fk_father = ".$fk_father;

    	dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
	  $num = $this->db->num_rows($resql);
            if ($num)
            {
	      $i = 0;
	      $array = array();
	      while ($i < $num)
		{
		  $obj = $this->db->fetch_object($resql);
		  $objNew = new Poapoa($this->db);
		  $objNew->id    = $obj->rowid;

		  $objNew->entity = $obj->entity;
		  $objNew->gestion = $obj->gestion;
		  $objNew->fk_structure = $obj->fk_structure;
		  $objNew->ref = $obj->ref;
		  $objNew->sigla = $obj->sigla;
		  $objNew->label = $obj->label;
		  $objNew->pseudonym = $obj->pseudonym;
		  $objNew->partida = $obj->partida;
		  $objNew->amount = $obj->amount;
		  $objNew->classification = $obj->classification;
		  $objNew->source_verification = $obj->source_verification;
		  $objNew->unit = $obj->unit;
		  $objNew->responsible_one = $obj->responsible_one;
		  $objNew->responsible_two = $obj->responsible_two;
		  $objNew->responsible = $obj->responsible;
		  $objNew->m_jan = $obj->m_jan;
		  $objNew->m_feb = $obj->m_feb;
		  $objNew->m_mar = $obj->m_mar;
		  $objNew->m_apr = $obj->m_apr;
		  $objNew->m_may = $obj->m_may;
		  $objNew->m_jun = $obj->m_jun;
		  $objNew->m_jul = $obj->m_jul;
		  $objNew->m_aug = $obj->m_aug;
		  $objNew->m_sep = $obj->m_sep;
		  $objNew->m_oct = $obj->m_oct;
		  $objNew->m_nov = $obj->m_nov;
		  $objNew->m_dec = $obj->m_dec;
		  $objNew->p_jan = $obj->p_jan;
		  $objNew->p_feb = $obj->p_feb;
		  $objNew->p_mar = $obj->p_mar;
		  $objNew->p_apr = $obj->p_apr;
		  $objNew->p_may = $obj->p_may;
		  $objNew->p_jun = $obj->p_jun;
		  $objNew->p_jul = $obj->p_jul;
		  $objNew->p_aug = $obj->p_aug;
		  $objNew->p_sep = $obj->p_sep;
		  $objNew->p_oct = $obj->p_oct;
		  $objNew->p_nov = $obj->p_nov;
		  $objNew->p_dec = $obj->p_dec;
		  $objNew->fk_area = $obj->fk_area;
		  $objNew->weighting = $obj->weighting;
		  $objNew->fk_poa_reformulated = $obj->fk_poa_reformulated;
		  $objNew->version = $obj->version;
		  $objNew->statut = $obj->statut;
		  $objNew->statut_ref = $obj->statut_ref;


		  $array[$obj->rowid] = $objNew;
		  $i++;
                }
	      return $array;
            }
            $this->db->free($resql);
            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function getlist_structure($fk_structure,$statut=1)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_structure,";
		$sql.= " t.ref,";
		$sql.= " t.sigla,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.classification,";
		$sql.= " t.source_verification,";
		$sql.= " t.unit,";
		$sql.= " t.responsible_one,";
		$sql.= " t.responsible_two,";
		$sql.= " t.responsible,";
		$sql.= " t.m_jan,";
		$sql.= " t.m_feb,";
		$sql.= " t.m_mar,";
		$sql.= " t.m_apr,";
		$sql.= " t.m_may,";
		$sql.= " t.m_jun,";
		$sql.= " t.m_jul,";
		$sql.= " t.m_aug,";
		$sql.= " t.m_sep,";
		$sql.= " t.m_oct,";
		$sql.= " t.m_nov,";
		$sql.= " t.m_dec,";
		$sql.= " t.p_jan,";
		$sql.= " t.p_feb,";
		$sql.= " t.p_mar,";
		$sql.= " t.p_apr,";
		$sql.= " t.p_may,";
		$sql.= " t.p_jun,";
		$sql.= " t.p_jul,";
		$sql.= " t.p_aug,";
		$sql.= " t.p_sep,";
		$sql.= " t.p_oct,";
		$sql.= " t.p_nov,";
		$sql.= " t.p_dec,";
		$sql.= " t.fk_area,";
		$sql.= " t.weighting,";
		$sql.= " t.fk_poa_reformulated,";
		$sql.= " t.version,";
		$sql.= " t.statut,";
		$sql.= " t.statut_ref";

        $sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
        $sql.= " WHERE t.fk_structure = ".$fk_structure;
		$sql.= " AND t.statut = ".$statut;
    	dol_syslog(get_class($this)."::getlist_structure sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        $this->array = array();
        if ($resql)
        {
	  $num = $this->db->num_rows($resql);
            if ($num)
            {
	      $i = 0;
	      while ($i < $num)
		{
		  $obj = $this->db->fetch_object($resql);
		  $objNew = new Poapoa($this->db);
		  $objNew->id    = $obj->rowid;

		  $objNew->entity = $obj->entity;
		  $objNew->gestion = $obj->gestion;
		  $objNew->fk_structure = $obj->fk_structure;
		  $objNew->ref = $obj->ref;
		  $objNew->sigla = $obj->sigla;
		  $objNew->label = $obj->label;
		  $objNew->pseudonym = $obj->pseudonym;
		  $objNew->partida = $obj->partida;
		  $objNew->amount = $obj->amount;
		  $objNew->classification = $obj->classification;
		  $objNew->source_verification = $obj->source_verification;
		  $objNew->unit = $obj->unit;
		  $objNew->responsible_one = $obj->responsible_one;
		  $objNew->responsible_two = $obj->responsible_two;
		  $objNew->responsible = $obj->responsible;
		  $objNew->m_jan = $obj->m_jan;
		  $objNew->m_feb = $obj->m_feb;
		  $objNew->m_mar = $obj->m_mar;
		  $objNew->m_apr = $obj->m_apr;
		  $objNew->m_may = $obj->m_may;
		  $objNew->m_jun = $obj->m_jun;
		  $objNew->m_jul = $obj->m_jul;
		  $objNew->m_aug = $obj->m_aug;
		  $objNew->m_sep = $obj->m_sep;
		  $objNew->m_oct = $obj->m_oct;
		  $objNew->m_nov = $obj->m_nov;
		  $objNew->m_dec = $obj->m_dec;
		  $objNew->p_jan = $obj->p_jan;
		  $objNew->p_feb = $obj->p_feb;
		  $objNew->p_mar = $obj->p_mar;
		  $objNew->p_apr = $obj->p_apr;
		  $objNew->p_may = $obj->p_may;
		  $objNew->p_jun = $obj->p_jun;
		  $objNew->p_jul = $obj->p_jul;
		  $objNew->p_aug = $obj->p_aug;
		  $objNew->p_sep = $obj->p_sep;
		  $objNew->p_oct = $obj->p_oct;
		  $objNew->p_nov = $obj->p_nov;
		  $objNew->p_dec = $obj->p_dec;
		  $objNew->fk_area = $obj->fk_area;
		  $objNew->weighting = $obj->weighting;
		  $objNew->fk_poa_reformulated = $obj->fk_poa_reformulated;
		  $objNew->version = $obj->version;
		  $objNew->statut = $obj->statut;
		  $objNew->statut_ref = $obj->statut_ref;


		  $this->array[$obj->rowid] = $objNew;
		  $i++;
                }
	      return count($this->array);
            }
            $this->db->free($resql);
            return 0;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function getlistref($fk_poa_reformulated)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_structure,";
		$sql.= " t.ref,";
		$sql.= " t.sigla,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.classification,";
		$sql.= " t.source_verification,";
		$sql.= " t.unit,";
		$sql.= " t.responsible_one,";
		$sql.= " t.responsible_two,";
		$sql.= " t.responsible,";
		$sql.= " t.m_jan,";
		$sql.= " t.m_feb,";
		$sql.= " t.m_mar,";
		$sql.= " t.m_apr,";
		$sql.= " t.m_may,";
		$sql.= " t.m_jun,";
		$sql.= " t.m_jul,";
		$sql.= " t.m_aug,";
		$sql.= " t.m_sep,";
		$sql.= " t.m_oct,";
		$sql.= " t.m_nov,";
		$sql.= " t.m_dec,";
		$sql.= " t.p_jan,";
		$sql.= " t.p_feb,";
		$sql.= " t.p_mar,";
		$sql.= " t.p_apr,";
		$sql.= " t.p_may,";
		$sql.= " t.p_jun,";
		$sql.= " t.p_jul,";
		$sql.= " t.p_aug,";
		$sql.= " t.p_sep,";
		$sql.= " t.p_oct,";
		$sql.= " t.p_nov,";
		$sql.= " t.p_dec,";
		$sql.= " t.fk_area,";
		$sql.= " t.weighting,";
		$sql.= " t.fk_poa_reformulated,";
		$sql.= " t.version,";
		$sql.= " t.statut,";
		$sql.= " t.statut_ref";



        $sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
        $sql.= " WHERE t.fk_poa_reformulated = ".$fk_poa_reformulated;

    	dol_syslog(get_class($this)."::getlistref sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
	$array = array();
        if ($resql)
	  {
	    $num = $this->db->num_rows($resql);
            if ($num)
	      {
		$i = 0;
		while ($i < $num)
		  {
		    $obj = $this->db->fetch_object($resql);
		    $objNew = new Poapoa($this->db);
		    $objNew->id    = $obj->rowid;

		    $objNew->entity = $obj->entity;
		    $objNew->gestion = $obj->gestion;
		    $objNew->fk_structure = $obj->fk_structure;
		    $objNew->ref = $obj->ref;
		    $objNew->sigla = $obj->sigla;
		    $objNew->label = $obj->label;
		    $objNew->pseudonym = $obj->pseudonym;
		    $objNew->partida = $obj->partida;
		    $objNew->amount = $obj->amount;
		    $objNew->classification = $obj->classification;
		    $objNew->source_verification = $obj->source_verification;
		    $objNew->unit = $obj->unit;
		    $objNew->responsible_one = $obj->responsible_one;
		    $objNew->responsible_two = $obj->responsible_two;
		    $objNew->responsible = $obj->responsible;
		    $objNew->m_jan = $obj->m_jan;
		    $objNew->m_feb = $obj->m_feb;
		    $objNew->m_mar = $obj->m_mar;
		    $objNew->m_apr = $obj->m_apr;
		    $objNew->m_may = $obj->m_may;
		    $objNew->m_jun = $obj->m_jun;
		    $objNew->m_jul = $obj->m_jul;
		    $objNew->m_aug = $obj->m_aug;
		    $objNew->m_sep = $obj->m_sep;
		    $objNew->m_oct = $obj->m_oct;
		    $objNew->m_nov = $obj->m_nov;
		    $objNew->m_dec = $obj->m_dec;
		    $objNew->p_jan = $obj->p_jan;
		    $objNew->p_feb = $obj->p_feb;
		    $objNew->p_mar = $obj->p_mar;
		    $objNew->p_apr = $obj->p_apr;
		    $objNew->p_may = $obj->p_may;
		    $objNew->p_jun = $obj->p_jun;
		    $objNew->p_jul = $obj->p_jul;
		    $objNew->p_aug = $obj->p_aug;
		    $objNew->p_sep = $obj->p_sep;
		    $objNew->p_oct = $obj->p_oct;
		    $objNew->p_nov = $obj->p_nov;
		    $objNew->p_dec = $obj->p_dec;
		    $objNew->fk_area = $obj->fk_area;
		    $objNew->weighting = $obj->weighting;
		    $objNew->fk_poa_reformulated = $obj->fk_poa_reformulated;
		    $objNew->version = $obj->version;
		    $objNew->statut = $obj->statut;
		    $objNew->statut_ref = $obj->statut_ref;
		    $array[$obj->rowid] = $objNew;
		    $i++;
		  }
		return $array;
	      }
            $this->db->free($resql);
            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *  Return combo list of activated countries, into language of user
     *
     *  @param	string	$selected       Id or Code or Label of preselected country
     *  @param  string	$htmlname       Name of html select object
     *  @param  string	$htmloption     Options html on select object
     *  @param	string	$maxlength	Max length for labels (0=no limit)
     *  @param	string	$showempty	View space labels (0=no view)

     *  @return string           		HTML string with select
     */
    function select_poa($selected='',$htmlname='fk_poa',$htmloption='',$maxlength=0,$showempty=0,$fk_structure='',$alist='')
    {
        global $conf,$langs;

	if (is_array($alist))
	  $filter = " AND c.rowid IN (".implode(',',$alist).")";
        $langs->load("poa@poa");

        $out='';
        $countryArray=array();
        $label=array();

        $sql = "SELECT c.rowid, c.label as label, c.partida AS partida, c.amount AS amount, ";
	$sql.= " s.sigla AS code_iso ";
        $sql.= " FROM ".MAIN_DB_PREFIX."poa_poa AS c ";
        $sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure AS s ON c.fk_structure = s.rowid ";

        $sql.= " WHERE c.entity = ".$conf->entity;
	$sql.= " AND c.statut = 1 ";
	$sql.= " AND s.gestion = ".$_SESSION['gestion'];
	$sql.= " AND s.pos = 3";
	if ($filter)
	  $sql.= $filter;
	// if ($fk_structure)
	//   $sql.= " AND c.fk_structure = ".$fk_structure;
	$sql.= " ORDER BY s.sigla ASC";
        dol_syslog(get_class($this)."::select_poa sql=".$sql);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $out.= '<select id="select'.$htmlname.'" class="form-control" name="'.$htmlname.'" '.$htmloption.'>';
	    if ($showempty)
	      {
		$out.= '<option value="-1"';
		if ($selected == -1) $out.= ' selected="selected"';
		$out.= '>&nbsp;</option>';
	      }

            $num = $this->db->num_rows($resql);
            $i = 0;
            if ($num)
            {
                $foundselected=false;

                while ($i < $num)
		  {
		    $obj = $this->db->fetch_object($resql);
                    $countryArray[$i]['rowid'] 	  = $obj->rowid;
		    $countryArray[$i]['partida']  = $obj->partida;
		    $countryArray[$i]['amount']  = $obj->amount;
                    $countryArray[$i]['code_iso'] = $obj->code_iso;
                    $countryArray[$i]['label']	  = ($obj->code_iso && $langs->transnoentitiesnoconv("Poa".$obj->code_iso)!="Poa".$obj->code_iso?$langs->transnoentitiesnoconv("Poa".$obj->code_iso):($obj->label!='-'?$obj->label:''));
                    $label[$i] 	= $countryArray[$i]['label'];
                    $i++;
                }

                array_multisort($label, SORT_ASC, $countryArray);

                foreach ($countryArray as $row)
                {
                    //print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
                    if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
                    {
                        $foundselected=true;
                        $out.= '<option value="'.$row['rowid'].'" selected="selected">';
                    }
                    else
                    {
                        $out.= '<option value="'.$row['rowid'].'">';
                    }
                    $out.= dol_trunc($row['label'],$maxlength,'middle');
                    if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')'.' '.$row['partida'].' '.price($row['amount']);
                    $out.= '</option>';
                }
            }
            $out.= '</select>';
        }
        else
        {
            dol_print_error($this->db);
        }

        return $out;
    }

	/**
	 *	Return label of status of object
	 *
	 *	@param      int	$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 *	@param      int	$type       0=Shell, 1=Buy
	 *	@return     string      	Label of status
	 */
	function getLibStatut($mode=0, $type=0)
	{
	  if($type==0)
	    return $this->LibStatut($this->statut,$mode,$type);
	  else
	    return $this->LibStatut($this->statut_ref,$mode,$type);
	}

	/**
	 *	Return label of a given status
	 *
	 *	@param      int		$status     Statut
	 *	@param      int		$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 *	@param      int		$type       0=Status "to sell", 1=Status "to buy"
	 *	@return     string      		Label of status
	 */
	function LibStatut($status,$mode=0,$type=0)
	{
	  global $langs;
	  $langs->load('mant@mant');

	  if ($mode == 0)
	    {
	      if ($status == 0) return ($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
	      if ($status == 1) return ($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
	    }
	  if ($mode == 2)
	    {
	      if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
	      if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
	    }

	  return $langs->trans('Unknown');
	}

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
	function get_partida($fk_structure,$gestion)
    {
    	global $langs;
	if (empty($gestion)) $gestion = date('Y');
        $sql = "SELECT";
	$sql.= " t.partida,";
	$sql.= " t.partida";

        $sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
        $sql.= " WHERE t.fk_structure = ".$fk_structure;
	$sql.= " AND t.gestion = ".$gestion;
	$sql.= " GROUP BY t.partida ";
	$sql.= " ORDER BY t.partida ";
    	dol_syslog(get_class($this)."::get_partida sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
	  $num = $this->db->num_rows($resql);
            if ($num)
            {
	      $i = 0;
	      $this->array = array();
	      while ($i < $num)
		{
		  $obj = $this->db->fetch_object($resql);
		  $this->array[$obj->partida] = $obj->partida;
		  $i++;
		}
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::get_partida ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
	function search($search,$gestion=0)
	{
	  global $langs,$conf;
	  if (empty($gestion)) $gestion = date('Y');
	  $sql = "SELECT t.rowid, ";
	  $sql.= " t.label, ";
	  $sql.= " t.partida, ";
	  $sql.= " s.sigla ";
	  $sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
	  $sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure as s ON t.fk_structure = s.rowid ";
	  $sql.= " WHERE t.entity = ".$conf->entity;
	  $sql.= " AND t.gestion = ".$gestion;
	  $sql .= " AND ( t.label LIKE '%".$this->db->escape($search)."%'";
	  $sql .= " OR t.pseudonym LIKE '%".$this->db->escape($search)."%'";
	  $sql .= " OR s.sigla LIKE '%".$this->db->escape($search)."%' )";
	  $sql.= " ORDER BY s.sigla, t.label ";
	  dol_syslog(get_class($this)."::search sql=".$sql, LOG_DEBUG);
	  $resql=$this->db->query($sql);
	  $this->array = array();
	  if ($resql)
	    {
	      $num = $this->db->num_rows($resql);
	      if ($num)
		{
		  $i = 0;
		  while ($i < $num)
		    {
		      $obj = $this->db->fetch_object($resql);
		      $this->array[$obj->rowid] = $obj->rowid;
		      $i++;
		    }
		}
	      $this->db->free($resql);
	      return 1;
	    }
	  else
	    {
	      $this->error="Error ".$this->db->lasterror();
	      dol_syslog(get_class($this)."::search ".$this->error, LOG_ERR);
	      return -1;
	    }
	}

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$gestion    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
	function get_maxmin($gestion,$vari='M')
    {
      global $langs,$conf;
	if (empty($gestion)) $gestion = date('Y');
        $sql = "SELECT t.gestion, ";
	if ($vari == 'M')
	  $sql.= " MAX(t.amount) as resultado ";
	if ($vari == 'm')
	  $sql.= " MIN(t.amount) as resultado ";

        $sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
        $sql.= " WHERE t.entity = ".$conf->entity;
	$sql.= " AND t.gestion = ".$gestion;
	$sql.= " AND t.statut = 1";
	 $sql.= " GROUP BY t.gestion";
    	dol_syslog(get_class($this)."::get_max sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
	  {
	    if ($this->db->num_rows($resql))
	      {
		$obj = $this->db->fetch_object($resql);
		$this->maxmin = $obj->resultado;
		$i++;
	      }
            $this->db->free($resql);

            return 1;
	  }
        else
	  {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::get_max ".$this->error, LOG_ERR);
            return -1;
	  }
    }

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$getion    object gestion
     *  @param	int		$fk_user    Id object user
     *  @param	varchar		$active    T=Todos, A=Activo, I=Inactivo

     *  @return int          	<0 if KO, >0 if OK
     */
	function getlist_user($gestion,$fk_user=0,$fk_area=0,$active='')
	{
	  //
	  global $langs,$conf;
	  $sql = "SELECT";
	  $sql.= " t.rowid,";
	  $sql.= " t.entity,";
	  $sql.= " t.gestion,";
	  $sql.= " t.fk_structure,";
	  $sql.= " t.ref,";
	  $sql.= " t.sigla,";
	  $sql.= " t.label,";
	  $sql.= " t.pseudonym,";
	  $sql.= " t.partida,";
	  $sql.= " t.amount,";
	  $sql.= " t.classification,";
	  $sql.= " t.source_verification,";
	  $sql.= " t.unit,";
	  $sql.= " t.responsible_one,";
	  $sql.= " t.responsible_two,";
	  $sql.= " t.responsible,";
	  $sql.= " t.m_jan,";
	  $sql.= " t.m_feb,";
	  $sql.= " t.m_mar,";
	  $sql.= " t.m_apr,";
	  $sql.= " t.m_may,";
	  $sql.= " t.m_jun,";
	  $sql.= " t.m_jul,";
	  $sql.= " t.m_aug,";
	  $sql.= " t.m_sep,";
	  $sql.= " t.m_oct,";
	  $sql.= " t.m_nov,";
	  $sql.= " t.m_dec,";
	  $sql.= " t.p_jan,";
	  $sql.= " t.p_feb,";
	  $sql.= " t.p_mar,";
	  $sql.= " t.p_apr,";
	  $sql.= " t.p_may,";
	  $sql.= " t.p_jun,";
	  $sql.= " t.p_jul,";
	  $sql.= " t.p_aug,";
	  $sql.= " t.p_sep,";
	  $sql.= " t.p_oct,";
	  $sql.= " t.p_nov,";
	  $sql.= " t.p_dec,";
	  $sql.= " t.fk_area,";
	  $sql.= " t.weighting,";
	  $sql.= " t.fk_poa_reformulated,";
	  $sql.= " t.version,";
	  $sql.= " t.statut,";
	  $sql.= " t.statut_ref,";
	  $sql.= " t.active,";
	  $sql.= " u.fk_user";

	  $sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
	  $sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure AS s ON t.fk_structure = s.rowid";

	  $sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_poa_user AS u ON t.rowid = u.fk_poa_poa";
	  $sql.= " INNER JOIN ".MAIN_DB_PREFIX."user AS us ON u.fk_user = us.rowid";
	  $sql.= " WHERE t.gestion = ".$gestion;
	  $sql.= " AND t.entity = ".$conf->entity;
	  $sql.= " AND u.active = 1 ";
	  $sql.= " AND u.statut = 1";
	  $sql.= " AND t.statut = 1";
	  $sql.= " AND t.statut_ref = 1";
	  if ($fk_user > 0)
	    $sql.= " AND u.fk_user = ".$fk_user;
	  if ($fk_area > 0)
	    $sql.= " AND s.fk_area = ".$fk_area;
	  if ($active == 'A') $sql.= " AND t.active = 1";
	  if ($active == 'I') $sql.= " AND t.active = 0";
	  $sql.= " ORDER BY us.lastname, us.firstname";
	  dol_syslog(get_class($this)."::getlist_user sql=".$sql, LOG_DEBUG);
	  $resql=$this->db->query($sql);
	  $this->array = array();
	  if ($resql)
	    {
	      $num = $this->db->num_rows($resql);
	      if ($this->db->num_rows($resql))
		{
		  $i = 0;
		  while ($i < $num)
		    {
		      $obj = $this->db->fetch_object($resql);
		      $objnew = new Poapoa($this->db);

		      $objnew->id    = $obj->rowid;

		      $objnew->entity = $obj->entity;
		      $objnew->gestion = $obj->gestion;
		      $objnew->fk_structure = $obj->fk_structure;
		      $objnew->ref = $obj->ref;
		      $objnew->sigla = $obj->sigla;
		      $objnew->label = $obj->label;
		      $objnew->pseudonym = $obj->pseudonym;
		      $objnew->partida = $obj->partida;
		      $objnew->amount = $obj->amount;
		      $objnew->classification = $obj->classification;
		      $objnew->source_verification = $obj->source_verification;
		      $objnew->unit = $obj->unit;
		      $objnew->responsible_one = $obj->responsible_one;
		      $objnew->responsible_two = $obj->responsible_two;
		      $objnew->responsible = $obj->responsible;
		      $objnew->m_jan = $obj->m_jan;
		      $objnew->m_feb = $obj->m_feb;
		      $objnew->m_mar = $obj->m_mar;
		      $objnew->m_apr = $obj->m_apr;
		      $objnew->m_may = $obj->m_may;
		      $objnew->m_jun = $obj->m_jun;
		      $objnew->m_jul = $obj->m_jul;
		      $objnew->m_aug = $obj->m_aug;
		      $objnew->m_sep = $obj->m_sep;
		      $objnew->m_oct = $obj->m_oct;
		      $objnew->m_nov = $obj->m_nov;
		      $objnew->m_dec = $obj->m_dec;
		      $objnew->p_jan = $obj->p_jan;
		      $objnew->p_feb = $obj->p_feb;
		      $objnew->p_mar = $obj->p_mar;
		      $objnew->p_apr = $obj->p_apr;
		      $objnew->p_may = $obj->p_may;
		      $objnew->p_jun = $obj->p_jun;
		      $objnew->p_jul = $obj->p_jul;
		      $objnew->p_aug = $obj->p_aug;
		      $objnew->p_sep = $obj->p_sep;
		      $objnew->p_oct = $obj->p_oct;
		      $objnew->p_nov = $obj->p_nov;
		      $objnew->p_dec = $obj->p_dec;
		      $objnew->fk_area = $obj->fk_area;
		      $objnew->weighting = $obj->weighting;
		      $objnew->fk_poa_reformulated = $obj->fk_poa_reformulated;
		      $objnew->version = $obj->version;
		      $objnew->statut = $obj->statut;
		      $objnew->statut_ref = $obj->statut_ref;
		      $objnew->active = $obj->active;
		      if ($fk_user == '')
			$this->array[$obj->fk_user][$obj->rowid] = $objnew;
		      else
			$this->array[$fk_user][$obj->rowid] = $objnew;
		      $i++;
		    }
		}
	      $this->db->free($resql);

	      return 1;
	    }
	  else
	    {
	      $this->error="Error ".$this->db->lasterror();
	      dol_syslog(get_class($this)."::getlist_user ".$this->error, LOG_ERR);
	      return -1;
	    }
	}

}
?>
