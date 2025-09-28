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
 *  \file       dev/skeletons/contabseatdet.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-06-03 13:29
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Contabseatdet // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='contabseatdet';			//!< Id that identify managed objects
	//var $table_element='contabseatdet';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_contab_seat;
	var $debit_account;
	var $debit_detail;
	var $credit_account;
	var $credit_detail;
	var $dcd;
	var $dcc;
	var $amount;
	var $history;
	var $sequence;
	var $fk_standard_seat;
	var $type_seat;
	var $routines;
	var $value02;
	var $value03;
	var $value04;
	var $date_rate='';
	var $rate;
	var $fk_user_creator;
	var $fk_date_creator='';
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
        
		if (isset($this->fk_contab_seat)) $this->fk_contab_seat=trim($this->fk_contab_seat);
		if (isset($this->debit_account)) $this->debit_account=trim($this->debit_account);
		if (isset($this->debit_detail)) $this->debit_detail=trim($this->debit_detail);
		if (isset($this->credit_account)) $this->credit_account=trim($this->credit_account);
		if (isset($this->credit_detail)) $this->credit_detail=trim($this->credit_detail);
		if (isset($this->dcd)) $this->dcd=trim($this->dcd);
		if (isset($this->dcc)) $this->dcc=trim($this->dcc);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->history)) $this->history=trim($this->history);
		if (isset($this->sequence)) $this->sequence=trim($this->sequence);
		if (isset($this->fk_standard_seat)) $this->fk_standard_seat=trim($this->fk_standard_seat);
		if (isset($this->type_seat)) $this->type_seat=trim($this->type_seat);
		if (isset($this->routines)) $this->routines=trim($this->routines);
		if (isset($this->value02)) $this->value02=trim($this->value02);
		if (isset($this->value03)) $this->value03=trim($this->value03);
		if (isset($this->value04)) $this->value04=trim($this->value04);
		if (isset($this->rate)) $this->rate=trim($this->rate);
		if (isset($this->fk_user_creator)) $this->fk_user_creator=trim($this->fk_user_creator);
		if (isset($this->state)) $this->state=trim($this->state);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."contab_seat_det(";
		
		$sql.= "fk_contab_seat,";
		$sql.= "debit_account,";
		$sql.= "debit_detail,";
		$sql.= "credit_account,";
		$sql.= "credit_detail,";
		$sql.= "dcd,";
		$sql.= "dcc,";
		$sql.= "amount,";
		$sql.= "history,";
		$sql.= "sequence,";
		$sql.= "fk_standard_seat,";
		$sql.= "type_seat,";
		$sql.= "routines,";
		$sql.= "value02,";
		$sql.= "value03,";
		$sql.= "value04,";
		$sql.= "date_rate,";
		$sql.= "rate,";
		$sql.= "fk_user_creator,";
		$sql.= "fk_date_creator,";
		$sql.= "state";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_contab_seat)?'NULL':"'".$this->fk_contab_seat."'").",";
		$sql.= " ".(! isset($this->debit_account)?'NULL':"'".$this->debit_account."'").",";
		$sql.= " ".(! isset($this->debit_detail)?'NULL':"'".$this->db->escape($this->debit_detail)."'").",";
		$sql.= " ".(! isset($this->credit_account)?'NULL':"'".$this->credit_account."'").",";
		$sql.= " ".(! isset($this->credit_detail)?'NULL':"'".$this->db->escape($this->credit_detail)."'").",";
		$sql.= " ".(! isset($this->dcd)?'NULL':"'".$this->dcd."'").",";
		$sql.= " ".(! isset($this->dcc)?'NULL':"'".$this->dcc."'").",";
		$sql.= " ".(! isset($this->amount)?'NULL':"'".$this->amount."'").",";
		$sql.= " ".(! isset($this->history)?'NULL':"'".$this->db->escape($this->history)."'").",";
		$sql.= " ".(! isset($this->sequence)?'NULL':"'".$this->db->escape($this->sequence)."'").",";
		$sql.= " ".(! isset($this->fk_standard_seat)?'NULL':"'".$this->fk_standard_seat."'").",";
		$sql.= " ".(! isset($this->type_seat)?'NULL':"'".$this->type_seat."'").",";
		$sql.= " ".(! isset($this->routines)?'NULL':"'".$this->db->escape($this->routines)."'").",";
		$sql.= " ".(! isset($this->value02)?'NULL':"'".$this->value02."'").",";
		$sql.= " ".(! isset($this->value03)?'NULL':"'".$this->value03."'").",";
		$sql.= " ".(! isset($this->value04)?'NULL':"'".$this->value04."'").",";
		$sql.= " ".(! isset($this->date_rate) || dol_strlen($this->date_rate)==0?'NULL':$this->db->idate($this->date_rate)).",";
		$sql.= " ".(! isset($this->rate)?'NULL':"'".$this->rate."'").",";
		$sql.= " ".(! isset($this->fk_user_creator)?'NULL':"'".$this->fk_user_creator."'").",";
		$sql.= " ".(! isset($this->fk_date_creator) || dol_strlen($this->fk_date_creator)==0?'NULL':$this->db->idate($this->fk_date_creator)).",";
		$sql.= " ".(! isset($this->state)?'NULL':"'".$this->state."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."contab_seat_det");

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
		
		$sql.= " t.fk_contab_seat,";
		$sql.= " t.debit_account,";
		$sql.= " t.debit_detail,";
		$sql.= " t.credit_account,";
		$sql.= " t.credit_detail,";
		$sql.= " t.dcd,";
		$sql.= " t.dcc,";
		$sql.= " t.amount,";
		$sql.= " t.history,";
		$sql.= " t.sequence,";
		$sql.= " t.fk_standard_seat,";
		$sql.= " t.type_seat,";
		$sql.= " t.routines,";
		$sql.= " t.value02,";
		$sql.= " t.value03,";
		$sql.= " t.value04,";
		$sql.= " t.date_rate,";
		$sql.= " t.rate,";
		$sql.= " t.fk_user_creator,";
		$sql.= " t.fk_date_creator,";
		$sql.= " t.state";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."contab_seat_det as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->fk_contab_seat = $obj->fk_contab_seat;
				$this->debit_account = $obj->debit_account;
				$this->debit_detail = $obj->debit_detail;
				$this->credit_account = $obj->credit_account;
				$this->credit_detail = $obj->credit_detail;
				$this->dcd = $obj->dcd;
				$this->dcc = $obj->dcc;
				$this->amount = $obj->amount;
				$this->history = $obj->history;
				$this->sequence = $obj->sequence;
				$this->fk_standard_seat = $obj->fk_standard_seat;
				$this->type_seat = $obj->type_seat;
				$this->routines = $obj->routines;
				$this->value02 = $obj->value02;
				$this->value03 = $obj->value03;
				$this->value04 = $obj->value04;
				$this->date_rate = $this->db->jdate($obj->date_rate);
				$this->rate = $obj->rate;
				$this->fk_user_creator = $obj->fk_user_creator;
				$this->fk_date_creator = $this->db->jdate($obj->fk_date_creator);
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
        
		if (isset($this->fk_contab_seat)) $this->fk_contab_seat=trim($this->fk_contab_seat);
		if (isset($this->debit_account)) $this->debit_account=trim($this->debit_account);
		if (isset($this->debit_detail)) $this->debit_detail=trim($this->debit_detail);
		if (isset($this->credit_account)) $this->credit_account=trim($this->credit_account);
		if (isset($this->credit_detail)) $this->credit_detail=trim($this->credit_detail);
		if (isset($this->dcd)) $this->dcd=trim($this->dcd);
		if (isset($this->dcc)) $this->dcc=trim($this->dcc);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->history)) $this->history=trim($this->history);
		if (isset($this->sequence)) $this->sequence=trim($this->sequence);
		if (isset($this->fk_standard_seat)) $this->fk_standard_seat=trim($this->fk_standard_seat);
		if (isset($this->type_seat)) $this->type_seat=trim($this->type_seat);
		if (isset($this->routines)) $this->routines=trim($this->routines);
		if (isset($this->value02)) $this->value02=trim($this->value02);
		if (isset($this->value03)) $this->value03=trim($this->value03);
		if (isset($this->value04)) $this->value04=trim($this->value04);
		if (isset($this->rate)) $this->rate=trim($this->rate);
		if (isset($this->fk_user_creator)) $this->fk_user_creator=trim($this->fk_user_creator);
		if (isset($this->state)) $this->state=trim($this->state);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."contab_seat_det SET";
        
		$sql.= " fk_contab_seat=".(isset($this->fk_contab_seat)?$this->fk_contab_seat:"null").",";
		$sql.= " debit_account=".(isset($this->debit_account)?$this->debit_account:"null").",";
		$sql.= " debit_detail=".(isset($this->debit_detail)?"'".$this->db->escape($this->debit_detail)."'":"null").",";
		$sql.= " credit_account=".(isset($this->credit_account)?$this->credit_account:"null").",";
		$sql.= " credit_detail=".(isset($this->credit_detail)?"'".$this->db->escape($this->credit_detail)."'":"null").",";
		$sql.= " dcd=".(isset($this->dcd)?$this->dcd:"null").",";
		$sql.= " dcc=".(isset($this->dcc)?$this->dcc:"null").",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
		$sql.= " history=".(isset($this->history)?"'".$this->db->escape($this->history)."'":"null").",";
		$sql.= " sequence=".(isset($this->sequence)?"'".$this->db->escape($this->sequence)."'":"null").",";
		$sql.= " fk_standard_seat=".(isset($this->fk_standard_seat)?$this->fk_standard_seat:"null").",";
		$sql.= " type_seat=".(isset($this->type_seat)?$this->type_seat:"null").",";
		$sql.= " routines=".(isset($this->routines)?"'".$this->db->escape($this->routines)."'":"null").",";
		$sql.= " value02=".(isset($this->value02)?$this->value02:"null").",";
		$sql.= " value03=".(isset($this->value03)?$this->value03:"null").",";
		$sql.= " value04=".(isset($this->value04)?$this->value04:"null").",";
		$sql.= " date_rate=".(dol_strlen($this->date_rate)!=0 ? "'".$this->db->idate($this->date_rate)."'" : 'null').",";
		$sql.= " rate=".(isset($this->rate)?$this->rate:"null").",";
		$sql.= " fk_user_creator=".(isset($this->fk_user_creator)?$this->fk_user_creator:"null").",";
		$sql.= " fk_date_creator=".(dol_strlen($this->fk_date_creator)!=0 ? "'".$this->db->idate($this->fk_date_creator)."'" : 'null').",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."contab_seat_det";
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

		$object=new Contabseatdet($this->db);

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
		
		$this->fk_contab_seat='';
		$this->debit_account='';
		$this->debit_detail='';
		$this->credit_account='';
		$this->credit_detail='';
		$this->dcd='';
		$this->dcc='';
		$this->amount='';
		$this->history='';
		$this->sequence='';
		$this->fk_standard_seat='';
		$this->type_seat='';
		$this->routines='';
		$this->value02='';
		$this->value03='';
		$this->value04='';
		$this->date_rate='';
		$this->rate='';
		$this->fk_user_creator='';
		$this->fk_date_creator='';
		$this->state='';

		
	}
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_sequence($id)
    {
    	global $langs;
        $sql = "SELECT";
	$sql.= " t.fk_contab_seat,";
	$sql.= " MAX(t.sequene) AS sequence ";
	
        $sql.= " FROM ".MAIN_DB_PREFIX."contab_seat_det as t";
        $sql.= " WHERE t.fk_contab_seat = ".$id;
	$sql.= " GROUP BY fk_contab_seat ";

    	dol_syslog(get_class($this)."::fetch_sequence sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
	  {
            if ($this->db->num_rows($resql))
	      {
                $obj = $this->db->fetch_object($resql);
		return $obj->sequence + 1;
	      }
            $this->db->free($resql);
            return 1;
	  }
        else
	  {
	    return 1;
	  }
    }

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function double_entry($id)
    {
    	global $langs;
        $sql = "SELECT";
	$sql.= " t.type_seat,";
	$sql.= " SUM(t.amount) AS amount ";
	
        $sql.= " FROM ".MAIN_DB_PREFIX."contab_seat_det as t";
        $sql.= " WHERE t.fk_contab_seat = ".$id;
	$sql.= " GROUP BY t.type_seat ";

    	dol_syslog(get_class($this)."::double_entry sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
	  {
	    $num = $this->db->num_rows($resql);
            if ($num)
	      {
		$i = 0;
		$amountDebit  = 0;
		$amountCredit = 0;
		while ($i < $num)
		  {
		    $obj = $this->db->fetch_object($resql);
		    if ($obj->type_seat == 1)
		      $amountDebit += price2num($obj->amount,'MT');
		    if ($obj->type_seat == 2)
		      $amountCredit += price2num($obj->amount,'MT');
		    if ($obj->type_seat == 3)
		      {
			$amountCredit += price2num($obj->amount,'MT');
			$amountDebit  += price2num($obj->amount,'MT');
		      }
		    $i++;
		  }
		if ($amountDebit != $amountCredit)
		  return -1;
		else
		  return 1;
	      }
            $this->db->free($resql);
            return 1;
	  }
        else
	  {
	    return -1;
	  }
    }

    /**
     *  Load object in memory from the database
     *
     *  @param	varchar		$ref    Ref object
     *  @return int          	<0 if KO, >0 if OK
     */
    function get_list_account($ref,$dateini='',$datefin='')
    {
      global $langs,$conf;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.fk_contab_seat,";
		$sql.= " t.debit_account,";
		$sql.= " t.debit_detail,";
		$sql.= " t.credit_account,";
		$sql.= " t.credit_detail,";
		$sql.= " t.dcd,";
		$sql.= " t.dcc,";
		$sql.= " t.amount,";
		$sql.= " t.history,";
		$sql.= " t.sequence,";
		$sql.= " t.fk_standard_seat,";
		$sql.= " t.type_seat,";
		$sql.= " t.routines,";
		$sql.= " t.value02,";
		$sql.= " t.value03,";
		$sql.= " t.value04,";
		$sql.= " t.date_rate,";
		$sql.= " t.rate,";
		$sql.= " t.fk_user_creator,";
		$sql.= " t.fk_date_creator,";
		$sql.= " t.state, ";
		$sql.= " s.date_seat";
		
        $sql.= " FROM ".MAIN_DB_PREFIX."contab_seat_det as t";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."contab_seat AS s ON s.rowid = t.fk_contab_seat ";
        $sql.= " WHERE (t.debit_account = '".$ref."' ";
	$sql.= " OR t.credit_account = '".$ref."') ";
	$sql.= " AND s.entity = ".$conf->entity;
	if (!empty($dateini) && !empty($datefin))
	  $sql.= " AND s.date_seat BETWEEN '".$this->db->idate($dateini)."' AND '".$this->db->idate($datefin)."' ";
    	dol_syslog(get_class($this)."::get_list_account sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
	$aArray = array();
	$aArrayDet = array();
        if ($resql)
	  {
	    $num = $this->db->num_rows($resql);
            if ($num)
	      {
		$i = 0;
		while ($i < $num)
		  {
		    $obj = $this->db->fetch_object($resql);
		    if (!empty($obj->debit_account) && empty($obj->credit_account))
		      {
			$aArrayDet[$obj->fk_contab_seat]['debit_account']=$obj->amount;

			$aArray['debit_amount'] += $obj->amount;
		      }
		    elseif (empty($obj->debit_account) && !empty($obj->credit_account))
		      {
			$aArrayDet[$obj->fk_contab_seat]['credit_account']=$obj->amount;
			$aArray['credit_amount'] += $obj->amount;
		      }
		    elseif (!empty($obj->debit_account) && !empty($obj->credit_account))
		      {
			$aArray['debit_amount'] += $obj->amount;
			$aArray['credit_amount'] += $obj->amount;
		      }
		      $i++;
		  }
	      }
            $this->db->free($resql);

            return array($aArray,$aArrayDet);
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }

}
?>
