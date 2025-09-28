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
 *  \file       dev/skeletons/pperiod.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-07-05 23:14
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/salary/class/commonobject_.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Pperiod extends CommonObject_
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='pperiod';			//!< Id that identify managed objects
	var $table_element='p_period';	//!< Name of table without prefix where object is stored
	protected $ismultientitymanaged = 1;
    var $id;
    
	var $entity;
	var $fk_proces;
	var $fk_type_fol;
	var $ref;
	var $mes;
	var $anio;
	var $date_ini='';
	var $date_fin='';
	var $date_pay='';
	var $date_court='';
	var $date_close='';
	var $state;

    


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
		if (isset($this->fk_proces)) $this->fk_proces=trim($this->fk_proces);
		if (isset($this->fk_type_fol)) $this->fk_type_fol=trim($this->fk_type_fol);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->mes)) $this->mes=trim($this->mes);
		if (isset($this->anio)) $this->anio=trim($this->anio);
		if (isset($this->state)) $this->state=trim($this->state);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."p_period(";
		
		$sql.= "entity,";
		$sql.= "fk_proces,";
		$sql.= "fk_type_fol,";
		$sql.= "ref,";
		$sql.= "mes,";
		$sql.= "anio,";
		$sql.= "date_ini,";
		$sql.= "date_fin,";
		$sql.= "date_pay,";
		$sql.= "date_court,";
		$sql.= "date_close,";
		$sql.= "state";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->fk_proces)?'NULL':"'".$this->fk_proces."'").",";
		$sql.= " ".(! isset($this->fk_type_fol)?'NULL':"'".$this->fk_type_fol."'").",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->mes)?'NULL':"'".$this->mes."'").",";
		$sql.= " ".(! isset($this->anio)?'NULL':"'".$this->anio."'").",";
		$sql.= " ".(! isset($this->date_ini) || dol_strlen($this->date_ini)==0?'NULL':$this->db->idate($this->date_ini)).",";
		$sql.= " ".(! isset($this->date_fin) || dol_strlen($this->date_fin)==0?'NULL':$this->db->idate($this->date_fin)).",";
		$sql.= " ".(! isset($this->date_pay) || dol_strlen($this->date_pay)==0?'NULL':$this->db->idate($this->date_pay)).",";
		$sql.= " ".(! isset($this->date_court) || dol_strlen($this->date_court)==0?'NULL':$this->db->idate($this->date_court)).",";
		$sql.= " ".(! isset($this->date_close) || dol_strlen($this->date_close)==0?'NULL':$this->db->idate($this->date_close)).",";
		$sql.= " ".(! isset($this->state)?'NULL':"'".$this->state."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."p_period");

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
		$sql.= " t.fk_proces,";
		$sql.= " t.fk_type_fol,";
		$sql.= " t.ref,";
		$sql.= " t.mes,";
		$sql.= " t.anio,";
		$sql.= " t.date_ini,";
		$sql.= " t.date_fin,";
		$sql.= " t.date_pay,";
		$sql.= " t.date_court,";
		$sql.= " t.date_close,";
		$sql.= " t.state";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."p_period as t";
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
				$this->fk_proces = $obj->fk_proces;
				$this->fk_type_fol = $obj->fk_type_fol;
				$this->ref = $obj->ref;
				$this->mes = $obj->mes;
				$this->anio = $obj->anio;
				$this->date_ini = $this->db->jdate($obj->date_ini);
				$this->date_fin = $this->db->jdate($obj->date_fin);
				$this->date_pay = $this->db->jdate($obj->date_pay);
				$this->date_court = $this->db->jdate($obj->date_court);
				$this->date_close = $this->db->jdate($obj->date_close);
				$this->state = $obj->state;

                
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
		if (isset($this->fk_proces)) $this->fk_proces=trim($this->fk_proces);
		if (isset($this->fk_type_fol)) $this->fk_type_fol=trim($this->fk_type_fol);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->mes)) $this->mes=trim($this->mes);
		if (isset($this->anio)) $this->anio=trim($this->anio);
		if (isset($this->state)) $this->state=trim($this->state);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."p_period SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " fk_proces=".(isset($this->fk_proces)?$this->fk_proces:"null").",";
		$sql.= " fk_type_fol=".(isset($this->fk_type_fol)?$this->fk_type_fol:"null").",";
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " mes=".(isset($this->mes)?$this->mes:"null").",";
		$sql.= " anio=".(isset($this->anio)?$this->anio:"null").",";
		$sql.= " date_ini=".(dol_strlen($this->date_ini)!=0 ? "'".$this->db->idate($this->date_ini)."'" : 'null').",";
		$sql.= " date_fin=".(dol_strlen($this->date_fin)!=0 ? "'".$this->db->idate($this->date_fin)."'" : 'null').",";
		$sql.= " date_pay=".(dol_strlen($this->date_pay)!=0 ? "'".$this->db->idate($this->date_pay)."'" : 'null').",";
		$sql.= " date_court=".(dol_strlen($this->date_court)!=0 ? "'".$this->db->idate($this->date_court)."'" : 'null').",";
		$sql.= " date_close=".(dol_strlen($this->date_close)!=0 ? "'".$this->db->idate($this->date_close)."'" : 'null').",";
		$sql.= " state=".(isset($this->state)?$this->state:"null")."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."p_period";
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

		$object=new Pperiod($this->db);

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
		$this->fk_proces='';
		$this->fk_type_fol='';
		$this->ref='';
		$this->mes='';
		$this->anio='';
		$this->date_ini='';
		$this->date_fin='';
		$this->date_pay='';
		$this->date_court='';
		$this->date_close='';
		$this->state='';

		
	}

    /**
     *  Return combo list of activated countries, into language of user
     *
     *  @param	string	$selected       Id or Code or Label of preselected country
     *  @param  string	$htmlname       Name of html select object
     *  @param  string	$htmloption     Options html on select object
     *  @param	string	$maxlength		Max length for labels (0=no limit)
     *  @param	string	$showempty		show line 
     *  @param	string	$cClose		0 = todos, 1 = no cerrados, 2= cerrados
     *  @return string           		HTML string with select
     */
	function select_period($selected='',$htmlname='fk_period',$htmloption='',$maxlength=0,$showempty=0,$cClose=0)
    {
        global $conf,$langs;
	$filtro = '';
	if (!empty($cClose))
	  {
	    if ($cClose == 1)
	      $filtro = " AND (d.date_close IS NULL OR d.date_close = '' ) ";
	    if ($cClose == 2)
	      $filtro = " AND (d.date_close IS NOT NULL AND d.date_close != '' ) ";
	  }
        $langs->load("salary@salary");

        $out='';
        $countryArray=array();
        $label=array();
	if (STRTOUPPER($conf->db->type) == 'PGSQL')
	  $sql = "SELECT d.rowid, d.ref as code_iso, (d.mes || ' ' || d.anio) AS label ";
	else
	  $sql = "SELECT d.rowid, d.ref as code_iso, CONCAT(d.mes,'-',d.anio,': ',e.label) as label";
        $sql.= " FROM ".MAIN_DB_PREFIX."p_period AS d ";
        $sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_proces AS e ";
	$sql.= " ON d.fk_proces = e.rowid AND d.entity = e.entity ";
        $sql.= " WHERE d.entity = ".$conf->entity;
	$sql.= $filtro;
	$sql.= " ORDER BY d.ref ASC";
        dol_syslog(get_class($this)."::select_period sql=".$sql);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
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
                    $countryArray[$i]['rowid'] 		= $obj->rowid;
                    $countryArray[$i]['code_iso'] 	= $obj->code_iso;
                    $countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Typefol".$obj->code_iso)!="Typefol".$obj->code_iso?$langs->transnoentitiesnoconv("Typefol".$obj->code_iso):($obj->label!='-'?$obj->label:''));
                    $label[$i] 	= $countryArray[$i]['label'];
                    $i++;
                }

                array_multisort($label, SORT_ASC, $countryArray);

                foreach ($countryArray as $row)
                {
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
                    if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
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
     *  Load object in memory from the database
     *
     *  @param	int		$month    number object
     *  @param	int		$year    number object
     *  @return int          	<0 if KO, >0 if OK
     */
	function fetch_month_year($month,$year)
	{
	  global $langs;
	  $sql = "SELECT";
	  $sql.= " t.rowid,";
	  $sql.= " t.entity,";
	  $sql.= " t.fk_proces,";
	  $sql.= " t.fk_type_fol,";
	  $sql.= " t.ref,";
	  $sql.= " t.mes,";
	  $sql.= " t.anio,";
	  $sql.= " t.date_ini,";
	  $sql.= " t.date_fin,";
	  $sql.= " t.date_pay,";
	  $sql.= " t.date_court,";
	  $sql.= " t.date_close,";
	  $sql.= " t.state";
	  
	  
	  $sql.= " FROM ".MAIN_DB_PREFIX."p_period as t";
	  $sql.= " WHERE t.mes = ".$month;
	  $sql.+ " AND t.anio = ".$year;
	  $sql.= " AND t.state = 1";	  
	  dol_syslog(get_class($this)."::fetch_month_year sql=".$sql, LOG_DEBUG);
	  $resql=$this->db->query($sql);
	  if ($resql)
	    {
	      if ($this->db->num_rows($resql))
		{
		  $obj = $this->db->fetch_object($resql);
		  
		  $this->id    = $obj->rowid;
		  $this->entity = $obj->entity;
		  $this->fk_proces = $obj->fk_proces;
		  $this->fk_type_fol = $obj->fk_type_fol;
		  $this->ref = $obj->ref;
		  $this->mes = $obj->mes;
		  $this->anio = $obj->anio;
		  $this->date_ini = $this->db->jdate($obj->date_ini);
		  $this->date_fin = $this->db->jdate($obj->date_fin);
		  $this->date_pay = $this->db->jdate($obj->date_pay);
		  $this->date_court = $this->db->jdate($obj->date_court);
		  $this->date_close = $this->db->jdate($obj->date_close);
		  $this->state = $obj->state;
		  
		  return 1;
		}
	      $this->db->free($resql);
	      
	      return 0;
	    }
	  else
	    {
	      $this->error="Error ".$this->db->lasterror();
	      dol_syslog(get_class($this)."::fetch_month_year ".$this->error, LOG_ERR);
	      return -1;
	    }
	}
	
}
?>
