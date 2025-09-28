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
 *  \file       dev/skeletons/poapartidapredet.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-06-12 17:08
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poapartidapredet extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poa_partida_pre_det';			//!< Id that identify managed objects
	var $table_element='poa_partida_pre_det';		//!< Name of table without prefix where object is stored

    var $id;

	var $fk_poa_partida_pre;
	var $fk_product;
	var $fk_contrat;
	var $fk_contrato;
	var $fk_poa_partida_com;
	var $quant;
	var $amount_base;
	var $detail;
	var $quant_adj;
	var $amount;
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

		if (isset($this->fk_poa_partida_pre)) $this->fk_poa_partida_pre=trim($this->fk_poa_partida_pre);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->fk_contrat)) $this->fk_contrat=trim($this->fk_contrat);
		if (isset($this->fk_contrato)) $this->fk_contrato=trim($this->fk_contrato);
		if (isset($this->fk_poa_partida_com)) $this->fk_poa_partida_com=trim($this->fk_poa_partida_com);
		if (isset($this->quant)) $this->quant=trim($this->quant);
		if (isset($this->amount_base)) $this->amount_base=trim($this->amount_base);
		if (isset($this->detail)) $this->detail=trim($this->detail);
		if (isset($this->quant_adj)) $this->quant_adj=trim($this->quant_adj);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->statut)) $this->statut=trim($this->statut);



		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_partida_pre_det(";

		$sql.= "fk_poa_partida_pre,";
		$sql.= "fk_product,";
		$sql.= "fk_contrat,";
		$sql.= "fk_contrato,";
		$sql.= "fk_poa_partida_com,";
		$sql.= "quant,";
		$sql.= "amount_base,";
		$sql.= "detail,";
		$sql.= "quant_adj,";
		$sql.= "amount,";
		$sql.= "statut";


        $sql.= ") VALUES (";

		$sql.= " ".(! isset($this->fk_poa_partida_pre)?'NULL':"'".$this->fk_poa_partida_pre."'").",";
		$sql.= " ".(! isset($this->fk_product)?'NULL':"'".$this->fk_product."'").",";
		$sql.= " ".(! isset($this->fk_contrat)?'NULL':"'".$this->fk_contrat."'").",";
		$sql.= " ".(! isset($this->fk_contrato)?'NULL':"'".$this->fk_contrato."'").",";
		$sql.= " ".(! isset($this->fk_poa_partida_com)?'NULL':"'".$this->fk_poa_partida_com."'").",";
		$sql.= " ".(! isset($this->quant)?'NULL':"'".$this->quant."'").",";
		$sql.= " ".(! isset($this->amount_base)?'NULL':"'".$this->amount_base."'").",";
		$sql.= " ".(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").",";
		$sql.= " ".(! isset($this->quant_adj)?'NULL':"'".$this->quant_adj."'").",";
		$sql.= " ".(! isset($this->amount)?'NULL':"'".$this->amount."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";


		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_partida_pre_det");

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

		$sql.= " t.fk_poa_partida_pre,";
		$sql.= " t.fk_product,";
		$sql.= " t.fk_contrat,";
		$sql.= " t.fk_contrato,";
		$sql.= " t.fk_poa_partida_com,";
		$sql.= " t.quant,";
		$sql.= " t.amount_base,";
		$sql.= " t.detail,";
		$sql.= " t.quant_adj,";
		$sql.= " t.amount,";
		$sql.= " t.tms,";
		$sql.= " t.statut";


        $sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre_det as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;

				$this->fk_poa_partida_pre = $obj->fk_poa_partida_pre;
				$this->fk_product = $obj->fk_product;
				$this->fk_contrat = $obj->fk_contrat;
				$this->fk_contrato = $obj->fk_contrato;
				$this->fk_poa_partida_com = $obj->fk_poa_partida_com;
				$this->quant = $obj->quant;
				$this->amount_base = $obj->amount_base;
				$this->detail = $obj->detail;
				$this->quant_adj = $obj->quant_adj;
				$this->amount = $obj->amount;
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

		if (isset($this->fk_poa_partida_pre)) $this->fk_poa_partida_pre=trim($this->fk_poa_partida_pre);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->fk_contrat)) $this->fk_contrat=trim($this->fk_contrat);
		if (isset($this->fk_contrato)) $this->fk_contrato=trim($this->fk_contrato);
		if (isset($this->fk_poa_partida_com)) $this->fk_poa_partida_com=trim($this->fk_poa_partida_com);
		if (isset($this->quant)) $this->quant=trim($this->quant);
		if (isset($this->amount_base)) $this->amount_base=trim($this->amount_base);
		if (isset($this->detail)) $this->detail=trim($this->detail);
		if (isset($this->quant_adj)) $this->quant_adj=trim($this->quant_adj);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->statut)) $this->statut=trim($this->statut);



		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."poa_partida_pre_det SET";

		$sql.= " fk_poa_partida_pre=".(isset($this->fk_poa_partida_pre)?$this->fk_poa_partida_pre:"null").",";
		$sql.= " fk_product=".(isset($this->fk_product)?$this->fk_product:"null").",";
		$sql.= " fk_contrat=".(isset($this->fk_contrat)?$this->fk_contrat:"null").",";
		$sql.= " fk_contrato=".(isset($this->fk_contrato)?$this->fk_contrato:"null").",";
		$sql.= " fk_poa_partida_com=".(isset($this->fk_poa_partida_com)?$this->fk_poa_partida_com:"null").",";
		$sql.= " quant=".(isset($this->quant)?$this->quant:"null").",";
		$sql.= " amount_base=".(isset($this->amount_base)?$this->amount_base:"null").",";
		$sql.= " detail=".(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").",";
		$sql.= " quant_adj=".(isset($this->quant_adj)?$this->quant_adj:"null").",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_partida_pre_det";
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

		$object=new Poapartidapredet($this->db);

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

		$this->fk_poa_partida_pre='';
		$this->fk_product='';
		$this->fk_contrat='';
		$this->fk_contrato='';
		$this->fk_poa_partida_com='';
		$this->quant='';
		$this->amount_base='';
		$this->detail='';
		$this->quant_adj='';
		$this->amount='';
		$this->tms='';
		$this->statut='';


	}

	//modificado
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
	function getlist($fk_poa_partida_pre,$idc=0,$statut=0,$type='N')
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_poa_partida_pre,";
		$sql.= " t.fk_product,";
		$sql.= " t.fk_contrat,";
		$sql.= " t.fk_contrato,";
		$sql.= " t.fk_poa_partida_com,";
		$sql.= " t.quant,";
		$sql.= " t.amount_base,";
		$sql.= " t.detail,";
		$sql.= " t.quant_adj,";
		$sql.= " t.amount,";
		$sql.= " t.tms,";
		$sql.= " t.statut";


        $sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre_det as t";
	$sql.= " WHERE t.fk_poa_partida_pre = ".$fk_poa_partida_pre;
	if ($statut == 1)
	  $sql.= " AND t.fk_contrat = ".$idc;
	else
	  if ($type=='S')
	    {
	      $sql.= " AND (";
	      $sql.= " t.fk_contrat = 0 OR t.fk_contrat IS NULL";
	      if ($idc)
		$sql.= " OR t.fk_contrat = ".$idc;
	      $sql.= ")";
	    }
    	dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
	$this->array= array();

        if ($resql)
	  {
	    $num = $this->db->num_rows($resql);
            if ($num)
	      {
		$i = 0;
		while ($i < $num)
		  {
		    $obj = $this->db->fetch_object($resql);
		    $objnew = new Poapartidapredet($this->db);

		    $objnew->id    = $obj->rowid;
		    $objnew->fk_poa_partida_pre = $obj->fk_poa_partida_pre;
		    $objnew->fk_product = $obj->fk_product;
		    $objnew->fk_contrat = $obj->fk_contrat;
		    $objnew->fk_contrato = $obj->fk_contrato;
		    $objnew->fk_poa_partida_com = $obj->fk_poa_partida_com;
		    $objnew->quant = $obj->quant;
		    $objnew->amount_base = $obj->amount_base;
		    $objnew->detail = $obj->detail;
		    $objnew->quant_adj = $obj->quant_adj;
		    $objnew->amount = $obj->amount;
		    $objnew->tms = $this->db->jdate($obj->tms);
		    $objnew->statut = $obj->statut;
		    $this->array[$obj->rowid] = $objnew;
		    $i++;
		  }
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
	function getlist2($fk_poa_partida_pre,$idc=0,$statut=0,$type='N')
    {
    	global $langs;

        $sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_poa_partida_pre,";
		$sql.= " t.fk_product,";
		$sql.= " t.fk_contrat,";
		$sql.= " t.fk_contrato,";
		$sql.= " t.fk_poa_partida_com,";
		$sql.= " t.quant,";
		$sql.= " t.amount_base,";
		$sql.= " t.detail,";
		$sql.= " t.quant_adj,";
		$sql.= " t.amount,";
		$sql.= " t.tms,";
		$sql.= " t.statut";


        $sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre_det as t";
	$sql.= " WHERE t.fk_poa_partida_pre = ".$fk_poa_partida_pre;
	// if ($statut == 1)
	//   $sql.= " AND t.fk_contrato = ".$idc;
	// else
	  if ($type=='S')
	    {
	      $sql.= " AND (";
	      $sql.= " t.fk_contrato = 0 OR t.fk_contrato IS NULL";
	      if ($idc)
		$sql.= " OR t.fk_contrato = ".$idc;
	      $sql.= ")";
	    }
	  //echo $sql;
	dol_syslog(get_class($this)."::getlist2 sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
	$this->array= array();

        if ($resql)
	  {
	    $num = $this->db->num_rows($resql);
            if ($num)
	      {
		$i = 0;
		while ($i < $num)
		  {
		    $obj = $this->db->fetch_object($resql);
		    $objnew = new Poapartidapredet($this->db);

		    $objnew->id    = $obj->rowid;
		    $objnew->fk_poa_partida_pre = $obj->fk_poa_partida_pre;
		    $objnew->fk_product = $obj->fk_product;
		    $objnew->fk_contrat = $obj->fk_contrat;
		    $objnew->fk_contrato = $obj->fk_contrato;
		    $objnew->fk_poa_partida_com = $obj->fk_poa_partida_com;
		    $objnew->quant = $obj->quant;
		    $objnew->amount_base = $obj->amount_base;
		    $objnew->detail = $obj->detail;
		    $objnew->quant_adj = $obj->quant_adj;
		    $objnew->amount = $obj->amount;
		    $objnew->tms = $this->db->jdate($obj->tms);
		    $objnew->statut = $obj->statut;
		    $this->array[$obj->rowid] = $objnew;
		    $i++;
		  }
	      }
	    $this->db->free($resql);

	    return 1;
	  }
        else
	  {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::getlist2 ".$this->error, LOG_ERR);
            return -1;
	  }
    }

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @param  int             1 amount; 0 amount_base
     *  @return int          	<0 if KO, >0 if OK
     */
	function getsum($fk_poa_partida_pre,$lAmount=1)
    {
    	global $langs;
        $sql = "SELECT";
	if ($lAmount == 1)
	  $sql.= " SUM(t.amount) AS total";
	if ($lAmount == 0)
	  $sql.= " SUM(t.amount_base) AS total";

        $sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre_det as t";
        $sql.= " WHERE t.fk_poa_partida_pre = ".$fk_poa_partida_pre;
	$sql.= " AND t.statut = 1";
    	dol_syslog(get_class($this)."::getsum sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
	  {
	    $num = $this->db->num_rows($resql);
            if ($num)
	      {
		$obj = $this->db->fetch_object($resql);
		return price2num($obj->total,'MT');
	      }
	    $this->db->free($resql);
	    return 0;
	  }
        else
	  {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::getsum ".$this->error, LOG_ERR);
            return -1;
	  }
    }

}
?>
