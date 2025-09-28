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
 *  \file       dev/skeletons/poaprocess.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-09-28 12:10
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poaprocess extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poaprocess';			//!< Id that identify managed objects
	var $table_element='poaprocess';		//!< Name of table without prefix where object is stored

    var $id;

	var $entity;
	var $gestion;
	var $ref;
	var $fk_area;
	var $fk_poa_prev;
	var $date_process='';
	var $amount;
	var $fk_type_adj;
	var $fk_type_con;
	var $term;
	var $label;
	var $justification;
	var $fk_poa_pac;
	var $ref_pac;
	var $cuce;
	var $code_process;
	var $doc_precio_referencial;
	var $doc_certif_presupuestaria;
	var $doc_especific_tecnica;
	var $doc_modelo_contrato;
	var $doc_informe_lega;
	var $doc_pac;
	var $doc_prop;
	var $fk_soc;
	var $metodo_sel_anpe;
	var $metodo_sel_lpni;
	var $metodo_sel_cae;
	var $condicion_adicional_anpe;
	var $condicion_adicional_lpni;
	var $date_create='';
	var $fk_user_create;
	var $tms='';
	var $statut;




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
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->fk_area)) $this->fk_area=trim($this->fk_area);
		if (isset($this->fk_poa_prev)) $this->fk_poa_prev=trim($this->fk_poa_prev);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->fk_type_adj)) $this->fk_type_adj=trim($this->fk_type_adj);
		if (isset($this->fk_type_con)) $this->fk_type_con=trim($this->fk_type_con);
		if (isset($this->term)) $this->term=trim($this->term);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->justification)) $this->justification=trim($this->justification);
		if (isset($this->fk_poa_pac)) $this->fk_poa_pac=trim($this->fk_poa_pac);
		if (isset($this->ref_pac)) $this->ref_pac=trim($this->ref_pac);
		if (isset($this->cuce)) $this->cuce=trim($this->cuce);
		if (isset($this->code_process)) $this->code_process=trim($this->code_process);
		if (isset($this->doc_precio_referencial)) $this->doc_precio_referencial=trim($this->doc_precio_referencial);
		if (isset($this->doc_certif_presupuestaria)) $this->doc_certif_presupuestaria=trim($this->doc_certif_presupuestaria);
		if (isset($this->doc_especific_tecnica)) $this->doc_especific_tecnica=trim($this->doc_especific_tecnica);
		if (isset($this->doc_modelo_contrato)) $this->doc_modelo_contrato=trim($this->doc_modelo_contrato);
		if (isset($this->doc_informe_lega)) $this->doc_informe_lega=trim($this->doc_informe_lega);
		if (isset($this->doc_pac)) $this->doc_pac=trim($this->doc_pac);
		if (isset($this->doc_prop)) $this->doc_prop=trim($this->doc_prop);
		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->metodo_sel_anpe)) $this->metodo_sel_anpe=trim($this->metodo_sel_anpe);
		if (isset($this->metodo_sel_lpni)) $this->metodo_sel_lpni=trim($this->metodo_sel_lpni);
		if (isset($this->metodo_sel_cae)) $this->metodo_sel_cae=trim($this->metodo_sel_cae);
		if (isset($this->condicion_adicional_anpe)) $this->condicion_adicional_anpe=trim($this->condicion_adicional_anpe);
		if (isset($this->condicion_adicional_lpni)) $this->condicion_adicional_lpni=trim($this->condicion_adicional_lpni);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);



		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_process(";

		$sql.= "entity,";
		$sql.= "gestion,";
		$sql.= "ref,";
		$sql.= "fk_area,";
		$sql.= "fk_poa_prev,";
		$sql.= "date_process,";
		$sql.= "amount,";
		$sql.= "fk_type_adj,";
		$sql.= "fk_type_con,";
		$sql.= "term,";
		$sql.= "label,";
		$sql.= "justification,";
		$sql.= "fk_poa_pac,";
		$sql.= "ref_pac,";
		$sql.= "cuce,";
		$sql.= "code_process,";
		$sql.= "doc_precio_referencial,";
		$sql.= "doc_certif_presupuestaria,";
		$sql.= "doc_especific_tecnica,";
		$sql.= "doc_modelo_contrato,";
		$sql.= "doc_informe_lega,";
		$sql.= "doc_pac,";
		$sql.= "doc_prop,";
		$sql.= "fk_soc,";
		$sql.= "metodo_sel_anpe,";
		$sql.= "metodo_sel_lpni,";
		$sql.= "metodo_sel_cae,";
		$sql.= "condicion_adicional_anpe,";
		$sql.= "condicion_adicional_lpni,";
		$sql.= "date_create,";
		$sql.= "fk_user_create,";
		$sql.= "statut";


        $sql.= ") VALUES (";

		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->gestion)?'NULL':"'".$this->gestion."'").",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->ref."'").",";
		$sql.= " ".(! isset($this->fk_area)?'NULL':"'".$this->fk_area."'").",";
		$sql.= " ".(! isset($this->fk_poa_prev)?'NULL':"'".$this->fk_poa_prev."'").",";
		$sql.= " ".(! isset($this->date_process) || dol_strlen($this->date_process)==0?'NULL':$this->db->idate($this->date_process)).",";
		$sql.= " ".(! isset($this->amount)?'NULL':"'".$this->amount."'").",";
		$sql.= " ".(! isset($this->fk_type_adj)?'NULL':"'".$this->fk_type_adj."'").",";
		$sql.= " ".(! isset($this->fk_type_con)?'NULL':"'".$this->fk_type_con."'").",";
		$sql.= " ".(! isset($this->term)?'NULL':"'".$this->term."'").",";
		$sql.= " ".(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").",";
		$sql.= " ".(! isset($this->justification)?'NULL':"'".$this->db->escape($this->justification)."'").",";
		$sql.= " ".(! isset($this->fk_poa_pac)?'NULL':"'".$this->fk_poa_pac."'").",";
		$sql.= " ".(! isset($this->ref_pac)?'NULL':"'".$this->db->escape($this->ref_pac)."'").",";
		$sql.= " ".(! isset($this->cuce)?'NULL':"'".$this->db->escape($this->cuce)."'").",";
		$sql.= " ".(! isset($this->code_process)?'NULL':"'".$this->db->escape($this->code_process)."'").",";
		$sql.= " ".(! isset($this->doc_precio_referencial)?'NULL':"'".$this->doc_precio_referencial."'").",";
		$sql.= " ".(! isset($this->doc_certif_presupuestaria)?'NULL':"'".$this->doc_certif_presupuestaria."'").",";
		$sql.= " ".(! isset($this->doc_especific_tecnica)?'NULL':"'".$this->doc_especific_tecnica."'").",";
		$sql.= " ".(! isset($this->doc_modelo_contrato)?'NULL':"'".$this->doc_modelo_contrato."'").",";
		$sql.= " ".(! isset($this->doc_informe_lega)?'NULL':"'".$this->doc_informe_lega."'").",";
		$sql.= " ".(! isset($this->doc_pac)?'NULL':"'".$this->doc_pac."'").",";
		$sql.= " ".(! isset($this->doc_prop)?'NULL':"'".$this->doc_prop."'").",";
		$sql.= " ".(! isset($this->fk_soc)?'NULL':"'".$this->fk_soc."'").",";
		$sql.= " ".(! isset($this->metodo_sel_anpe)?'NULL':"'".$this->metodo_sel_anpe."'").",";
		$sql.= " ".(! isset($this->metodo_sel_lpni)?'NULL':"'".$this->metodo_sel_lpni."'").",";
		$sql.= " ".(! isset($this->metodo_sel_cae)?'NULL':"'".$this->metodo_sel_cae."'").",";
		$sql.= " ".(! isset($this->condicion_adicional_anpe)?'NULL':"'".$this->condicion_adicional_anpe."'").",";
		$sql.= " ".(! isset($this->condicion_adicional_lpni)?'NULL':"'".$this->condicion_adicional_lpni."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";


		$sql.= ")";
echo $sql;

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_process");

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
		$sql.= " t.ref,";
		$sql.= " t.fk_area,";
		$sql.= " t.fk_poa_prev,";
		$sql.= " t.date_process,";
		$sql.= " t.amount,";
		$sql.= " t.fk_type_adj,";
		$sql.= " t.fk_type_con,";
		$sql.= " t.term,";
		$sql.= " t.label,";
		$sql.= " t.justification,";
		$sql.= " t.fk_poa_pac,";
		$sql.= " t.ref_pac,";
		$sql.= " t.cuce,";
		$sql.= " t.code_process,";
		$sql.= " t.doc_precio_referencial,";
		$sql.= " t.doc_certif_presupuestaria,";
		$sql.= " t.doc_especific_tecnica,";
		$sql.= " t.doc_modelo_contrato,";
		$sql.= " t.doc_informe_lega,";
		$sql.= " t.doc_pac,";
		$sql.= " t.doc_prop,";
		$sql.= " t.fk_soc,";
		$sql.= " t.metodo_sel_anpe,";
		$sql.= " t.metodo_sel_lpni,";
		$sql.= " t.metodo_sel_cae,";
		$sql.= " t.condicion_adicional_anpe,";
		$sql.= " t.condicion_adicional_lpni,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";


        $sql.= " FROM ".MAIN_DB_PREFIX."poa_process as t";
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
				$this->ref = $obj->ref;
				$this->fk_area = $obj->fk_area;
				$this->fk_poa_prev = $obj->fk_poa_prev;
				$this->date_process = $this->db->jdate($obj->date_process);
				$this->amount = $obj->amount;
				$this->fk_type_adj = $obj->fk_type_adj;
				$this->fk_type_con = $obj->fk_type_con;
				$this->term = $obj->term;
				$this->label = $obj->label;
				$this->justification = $obj->justification;
				$this->fk_poa_pac = $obj->fk_poa_pac;
				$this->ref_pac = $obj->ref_pac;
				$this->cuce = $obj->cuce;
				$this->code_process = $obj->code_process;
				$this->doc_precio_referencial = $obj->doc_precio_referencial;
				$this->doc_certif_presupuestaria = $obj->doc_certif_presupuestaria;
				$this->doc_especific_tecnica = $obj->doc_especific_tecnica;
				$this->doc_modelo_contrato = $obj->doc_modelo_contrato;
				$this->doc_informe_lega = $obj->doc_informe_lega;
				$this->doc_pac = $obj->doc_pac;
				$this->doc_prop = $obj->doc_prop;
				$this->fk_soc = $obj->fk_soc;
				$this->metodo_sel_anpe = $obj->metodo_sel_anpe;
				$this->metodo_sel_lpni = $obj->metodo_sel_lpni;
				$this->metodo_sel_cae = $obj->metodo_sel_cae;
				$this->condicion_adicional_anpe = $obj->condicion_adicional_anpe;
				$this->condicion_adicional_lpni = $obj->condicion_adicional_lpni;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_user_create = $obj->fk_user_create;
				$this->tms = $this->db->jdate($obj->tms);
				$this->statut = $obj->statut;


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
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->fk_area)) $this->fk_area=trim($this->fk_area);
		if (isset($this->fk_poa_prev)) $this->fk_poa_prev=trim($this->fk_poa_prev);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->fk_type_adj)) $this->fk_type_adj=trim($this->fk_type_adj);
		if (isset($this->fk_type_con)) $this->fk_type_con=trim($this->fk_type_con);
		if (isset($this->term)) $this->term=trim($this->term);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->justification)) $this->justification=trim($this->justification);
		if (isset($this->fk_poa_pac)) $this->fk_poa_pac=trim($this->fk_poa_pac);
		if (isset($this->ref_pac)) $this->ref_pac=trim($this->ref_pac);
		if (isset($this->cuce)) $this->cuce=trim($this->cuce);
		if (isset($this->code_process)) $this->code_process=trim($this->code_process);
		if (isset($this->doc_precio_referencial)) $this->doc_precio_referencial=trim($this->doc_precio_referencial);
		if (isset($this->doc_certif_presupuestaria)) $this->doc_certif_presupuestaria=trim($this->doc_certif_presupuestaria);
		if (isset($this->doc_especific_tecnica)) $this->doc_especific_tecnica=trim($this->doc_especific_tecnica);
		if (isset($this->doc_modelo_contrato)) $this->doc_modelo_contrato=trim($this->doc_modelo_contrato);
		if (isset($this->doc_informe_lega)) $this->doc_informe_lega=trim($this->doc_informe_lega);
		if (isset($this->doc_pac)) $this->doc_pac=trim($this->doc_pac);
		if (isset($this->doc_prop)) $this->doc_prop=trim($this->doc_prop);
		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->metodo_sel_anpe)) $this->metodo_sel_anpe=trim($this->metodo_sel_anpe);
		if (isset($this->metodo_sel_lpni)) $this->metodo_sel_lpni=trim($this->metodo_sel_lpni);
		if (isset($this->metodo_sel_cae)) $this->metodo_sel_cae=trim($this->metodo_sel_cae);
		if (isset($this->condicion_adicional_anpe)) $this->condicion_adicional_anpe=trim($this->condicion_adicional_anpe);
		if (isset($this->condicion_adicional_lpni)) $this->condicion_adicional_lpni=trim($this->condicion_adicional_lpni);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);



		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."poa_process SET";

		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " gestion=".(isset($this->gestion)?$this->gestion:"null").",";
		$sql.= " ref=".(isset($this->ref)?$this->ref:"null").",";
		$sql.= " fk_area=".(isset($this->fk_area)?$this->fk_area:"null").",";
		$sql.= " fk_poa_prev=".(isset($this->fk_poa_prev)?$this->fk_poa_prev:"null").",";
		$sql.= " date_process=".(dol_strlen($this->date_process)!=0 ? "'".$this->db->idate($this->date_process)."'" : 'null').",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
		$sql.= " fk_type_adj=".(isset($this->fk_type_adj)?$this->fk_type_adj:"null").",";
		$sql.= " fk_type_con=".(isset($this->fk_type_con)?$this->fk_type_con:"null").",";
		$sql.= " term=".(isset($this->term)?$this->term:"null").",";
		$sql.= " label=".(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").",";
		$sql.= " justification=".(isset($this->justification)?"'".$this->db->escape($this->justification)."'":"null").",";
		$sql.= " fk_poa_pac=".(isset($this->fk_poa_pac)?$this->fk_poa_pac:"null").",";
		$sql.= " ref_pac=".(isset($this->ref_pac)?"'".$this->db->escape($this->ref_pac)."'":"null").",";
		$sql.= " cuce=".(isset($this->cuce)?"'".$this->db->escape($this->cuce)."'":"null").",";
		$sql.= " code_process=".(isset($this->code_process)?"'".$this->db->escape($this->code_process)."'":"null").",";
		$sql.= " doc_precio_referencial=".(isset($this->doc_precio_referencial)?$this->doc_precio_referencial:"null").",";
		$sql.= " doc_certif_presupuestaria=".(isset($this->doc_certif_presupuestaria)?$this->doc_certif_presupuestaria:"null").",";
		$sql.= " doc_especific_tecnica=".(isset($this->doc_especific_tecnica)?$this->doc_especific_tecnica:"null").",";
		$sql.= " doc_modelo_contrato=".(isset($this->doc_modelo_contrato)?$this->doc_modelo_contrato:"null").",";
		$sql.= " doc_informe_lega=".(isset($this->doc_informe_lega)?$this->doc_informe_lega:"null").",";
		$sql.= " doc_pac=".(isset($this->doc_pac)?$this->doc_pac:"null").",";
		$sql.= " doc_prop=".(isset($this->doc_prop)?$this->doc_prop:"null").",";
		$sql.= " fk_soc=".(isset($this->fk_soc)?$this->fk_soc:"null").",";
		$sql.= " metodo_sel_anpe=".(isset($this->metodo_sel_anpe)?$this->metodo_sel_anpe:"null").",";
		$sql.= " metodo_sel_lpni=".(isset($this->metodo_sel_lpni)?$this->metodo_sel_lpni:"null").",";
		$sql.= " metodo_sel_cae=".(isset($this->metodo_sel_cae)?$this->metodo_sel_cae:"null").",";
		$sql.= " condicion_adicional_anpe=".(isset($this->condicion_adicional_anpe)?$this->condicion_adicional_anpe:"null").",";
		$sql.= " condicion_adicional_lpni=".(isset($this->condicion_adicional_lpni)?$this->condicion_adicional_lpni:"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " statut=".(isset($this->statut)?$this->statut:"null")."";


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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_process";
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

		$object=new Poaprocess($this->db);

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
		$this->ref='';
		$this->fk_area='';
		$this->fk_poa_prev='';
		$this->date_process='';
		$this->amount='';
		$this->fk_type_adj='';
		$this->fk_type_con='';
		$this->term='';
		$this->label='';
		$this->justification='';
		$this->fk_poa_pac='';
		$this->ref_pac='';
		$this->cuce='';
		$this->code_process='';
		$this->doc_precio_referencial='';
		$this->doc_certif_presupuestaria='';
		$this->doc_especific_tecnica='';
		$this->doc_modelo_contrato='';
		$this->doc_informe_lega='';
		$this->doc_pac='';
		$this->doc_prop='';
		$this->fk_soc='';
		$this->metodo_sel_anpe='';
		$this->metodo_sel_lpni='';
		$this->metodo_sel_cae='';
		$this->condicion_adicional_anpe='';
		$this->condicion_adicional_lpni='';
		$this->date_create='';
		$this->fk_user_create='';
		$this->tms='';
		$this->statut='';


	}


	//MODIFICADO
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
	  $langs->load('poa@poa');

	  if ($mode == 0)
	    {
	      if ($status == -1) return img_picto($langs->trans('Anulled'),'statut8').' '.($type==0 ? $langs->trans('Anulled'):$langs->trans('Reformulation Anulled'));
	      if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
	      if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
	      if ($status == 2) return img_picto($langs->trans('Paid'),'statut4').' '.($type==0 ? $langs->trans('Paid'):$langs->trans('Paid'));
	    }

	  if ($mode == 2)
	    {
	      if ($status == -1) return img_picto($langs->trans('Anulled'),'statut8').' '.($type==0 ? $langs->trans('Anulled'):$langs->trans('Reformulation Anulled'));
	      if ($status == 0) return img_picto($langs->trans('Notvalidated'),'statut0').' '.($type==0 ? $langs->trans('Notvalidated'):$langs->trans('Notvalidated'));
	      if ($status == 1) return img_picto($langs->trans('Validated'),'statut1').' '.($type==0 ? $langs->trans('Validated'):$langs->trans('Validated'));
	      if ($status == 2) return img_picto($langs->trans('Paid'),'statut4').' '.($type==0 ? $langs->trans('Paid'):$langs->trans('Paid'));
	    }

	  if ($mode == 3)
	    { //si proceso o no

	      if ($status == 1) return img_picto($langs->trans('Not'),'switch_off');

	      if ($status == 2) return img_picto($langs->trans('Yes'),'switch_on');
	    }
	  return $langs->trans('Unknown');
	}

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function get_maxref($gestion)
    {
      global $langs,$conf;
        $sql = "SELECT";
		$sql.= " MAX(t.ref) AS maximo";


        $sql.= " FROM ".MAIN_DB_PREFIX."poa_process as t";
        $sql.= " WHERE t.entity = ".$conf->entity;
	$sql.= " AND t.gestion = ".$gestion;

    	dol_syslog(get_class($this)."::get_maxref sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
	$this->maximo = 1;
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
		$this->maximo = $obj->maximo + 1;
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::get_maxref ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$fk_poa_prev    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_prev($fk_poa_prev,$statut = '')
    {
      global $langs;
      $sql = "SELECT";
      $sql.= " t.rowid,";

      $sql.= " t.gestion,";
      $sql.= " t.ref,";
      $sql.= " t.entity,";
      $sql.= " t.fk_area,";
      $sql.= " t.fk_poa_prev,";
      $sql.= " t.date_process,";
      $sql.= " t.amount,";
      $sql.= " t.fk_type_adj,";
      $sql.= " t.fk_type_con,";
      $sql.= " t.term,";
      $sql.= " t.label,";
      $sql.= " t.justification,";
      $sql.= " t.fk_poa_pac,";
      $sql.= " t.ref_pac,";
      $sql.= " t.cuce,";
      $sql.= " t.code_process,";
      $sql.= " t.doc_precio_referencial,";
      $sql.= " t.doc_certif_presupuestaria,";
      $sql.= " t.doc_especific_tecnica,";
      $sql.= " t.doc_modelo_contrato,";
      $sql.= " t.doc_informe_lega,";
      $sql.= " t.doc_pac,";
      $sql.= " t.doc_prop,";
      $sql.= " t.fk_soc,";
      $sql.= " t.metodo_sel_anpe,";
      $sql.= " t.metodo_sel_lpni,";
      $sql.= " t.metodo_sel_cae,";
      $sql.= " t.condicion_adicional_anpe,";
      $sql.= " t.condicion_adicional_lpni,";
      $sql.= " t.date_create,";
      $sql.= " t.fk_user_create,";
      $sql.= " t.tms,";
      $sql.= " t.statut";


      $sql.= " FROM ".MAIN_DB_PREFIX."poa_process as t";
      $sql.= " WHERE t.fk_poa_prev = ".$fk_poa_prev;
      if (!empty($statut))
	$sql.= " AND t.statut IN (".$statut.")";
      dol_syslog(get_class($this)."::fetch_prev sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;

		$this->gestion = $obj->gestion;
		$this->ref = $obj->ref;
		$this->entity = $obj->entity;
		$this->fk_area = $obj->fk_area;
		$this->fk_poa_prev = $obj->fk_poa_prev;
		$this->date_process = $this->db->jdate($obj->date_process);
		$this->amount = $obj->amount;
		$this->fk_type_adj = $obj->fk_type_adj;
		$this->fk_type_con = $obj->fk_type_con;
		$this->term = $obj->term;
		$this->label = $obj->label;
		$this->justification = $obj->justification;
		$this->fk_poa_pac = $obj->fk_poa_pac;
		$this->ref_pac = $obj->ref_pac;
		$this->cuce = $obj->cuce;
		$this->code_process = $obj->code_process;
		$this->doc_precio_referencial = $obj->doc_precio_referencial;
		$this->doc_certif_presupuestaria = $obj->doc_certif_presupuestaria;
		$this->doc_especific_tecnica = $obj->doc_especific_tecnica;
		$this->doc_modelo_contrato = $obj->doc_modelo_contrato;
		$this->doc_informe_lega = $obj->doc_informe_lega;
		$this->doc_pac = $obj->doc_pac;
		$this->doc_prop = $obj->doc_prop;
		$this->fk_soc = $obj->fk_soc;
		$this->metodo_sel_anpe = $obj->metodo_sel_anpe;
		$this->metodo_sel_lpni = $obj->metodo_sel_lpni;
		$this->metodo_sel_cae = $obj->metodo_sel_cae;
		$this->condicion_adicional_anpe = $obj->condicion_adicional_anpe;
		$this->condicion_adicional_lpni = $obj->condicion_adicional_lpni;
		$this->date_create = $this->db->jdate($obj->date_create);
		$this->fk_user_create = $obj->fk_user_create;
		$this->tms = $this->db->jdate($obj->tms);
		$this->statut = $obj->statut;
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch_prev ".$this->error, LOG_ERR);
            return -1;
        }
    }


    /**
     *  Load object in memory from the database
     *
     *  @param	int		$fk_poa_prev    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_process_ini($fk_pac)
    {
      global $langs;
      $sql = "SELECT";
      $sql.= " t.rowid,";

      $sql.= " t.gestion,";
      $sql.= " t.ref,";
      $sql.= " t.entity,";
      $sql.= " t.fk_area,";
      $sql.= " t.fk_poa_prev,";
      $sql.= " t.date_process,";
      $sql.= " t.amount,";
      $sql.= " t.fk_type_adj,";
      $sql.= " t.fk_type_con,";
      $sql.= " t.term,";
      $sql.= " t.label,";
      $sql.= " t.justification,";
      $sql.= " t.fk_poa_pac,";
      $sql.= " t.ref_pac,";
      $sql.= " t.cuce,";
      $sql.= " t.code_process,";
      $sql.= " t.doc_precio_referencial,";
      $sql.= " t.doc_certif_presupuestaria,";
      $sql.= " t.doc_especific_tecnica,";
      $sql.= " t.doc_modelo_contrato,";
      $sql.= " t.doc_informe_lega,";
      $sql.= " t.doc_pac,";
      $sql.= " t.doc_prop,";
      $sql.= " t.fk_soc,";
      $sql.= " t.metodo_sel_anpe,";
      $sql.= " t.metodo_sel_lpni,";
      $sql.= " t.metodo_sel_cae,";
      $sql.= " t.condicion_adicional_anpe,";
      $sql.= " t.condicion_adicional_lpni,";
      $sql.= " t.date_create,";
      $sql.= " t.fk_user_create,";
      $sql.= " t.tms,";
      $sql.= " t.statut";


      $sql.= " FROM ".MAIN_DB_PREFIX."poa_process as t";
      $sql.= " WHERE t.fk_poa_prev = ".$fk_poa_prev;

      dol_syslog(get_class($this)."::fetch_process_ini sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;

		$this->gestion = $obj->gestion;
		$this->ref = $obj->ref;
		$this->entity = $obj->entity;
		$this->fk_area = $obj->fk_area;
		$this->fk_poa_prev = $obj->fk_poa_prev;
		$this->date_process = $this->db->jdate($obj->date_process);
		$this->amount = $obj->amount;
		$this->fk_type_adj = $obj->fk_type_adj;
		$this->fk_type_con = $obj->fk_type_con;
		$this->term = $obj->term;
		$this->label = $obj->label;
		$this->justification = $obj->justification;
		$this->fk_poa_pac = $obj->fk_poa_pac;
		$this->ref_pac = $obj->ref_pac;
		$this->cuce = $obj->cuce;
		$this->code_process = $obj->code_process;
		$this->doc_precio_referencial = $obj->doc_precio_referencial;
		$this->doc_certif_presupuestaria = $obj->doc_certif_presupuestaria;
		$this->doc_especific_tecnica = $obj->doc_especific_tecnica;
		$this->doc_modelo_contrato = $obj->doc_modelo_contrato;
		$this->doc_informe_lega = $obj->doc_informe_lega;
		$this->doc_pac = $obj->doc_pac;
		$this->doc_prop = $obj->doc_prop;
		$this->fk_soc = $obj->fk_soc;
		$this->metodo_sel_anpe = $obj->metodo_sel_anpe;
		$this->metodo_sel_lpni = $obj->metodo_sel_lpni;
		$this->metodo_sel_cae = $obj->metodo_sel_cae;
		$this->condicion_adicional_anpe = $obj->condicion_adicional_anpe;
		$this->condicion_adicional_lpni = $obj->condicion_adicional_lpni;
		$this->date_create = $this->db->jdate($obj->date_create);
		$this->fk_user_create = $obj->fk_user_create;
		$this->tms = $this->db->jdate($obj->tms);
		$this->statut = $obj->statut;


            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch_process_ini ".$this->error, LOG_ERR);
            return -1;
        }
    }

}
?>
