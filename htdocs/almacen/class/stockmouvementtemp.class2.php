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
 *  \file       dev/skeletons/stockmouvementtemp.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-09-21 09:44
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Stockmouvementtemp extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='stock_mouvement_temp';			//!< Id that identify managed objects
	var $table_element='stock_mouvement_temp';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $ref;
	var $tms='';
	var $datem='';
	var $fk_product;
	var $fk_entrepot;
	var $value;
	var $quant;
	var $price;
	var $type_mouvement;
	var $fk_user_author;
	var $label;
	var $fk_origin;
	var $origintype;
	var $inventorycode;
	var $batch;
	var $eatby='';
	var $sellby='';

    


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
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->fk_entrepot)) $this->fk_entrepot=trim($this->fk_entrepot);
		if (isset($this->value)) $this->value=trim($this->value);
		if (isset($this->quant)) $this->quant=trim($this->quant);
		if (isset($this->price)) $this->price=trim($this->price);
		if (isset($this->type_mouvement)) $this->type_mouvement=trim($this->type_mouvement);
		if (isset($this->fk_user_author)) $this->fk_user_author=trim($this->fk_user_author);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->fk_origin)) $this->fk_origin=trim($this->fk_origin);
		if (isset($this->origintype)) $this->origintype=trim($this->origintype);
		if (isset($this->inventorycode)) $this->inventorycode=trim($this->inventorycode);
		if (isset($this->batch)) $this->batch=trim($this->batch);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."stock_mouvement_temp(";
		
		$sql.= "entity,";
		$sql.= "ref,";
		$sql.= "datem,";
		$sql.= "fk_product,";
		$sql.= "fk_entrepot,";
		$sql.= "value,";
		$sql.= "quant,";
		$sql.= "price,";
		$sql.= "type_mouvement,";
		$sql.= "fk_user_author,";
		$sql.= "label,";
		$sql.= "fk_origin,";
		$sql.= "origintype,";
		$sql.= "inventorycode,";
		$sql.= "batch,";
		$sql.= "eatby,";
		$sql.= "sellby";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->datem) || dol_strlen($this->datem)==0?'NULL':$this->db->idate($this->datem)).",";
		$sql.= " ".(! isset($this->fk_product)?'NULL':"'".$this->fk_product."'").",";
		$sql.= " ".(! isset($this->fk_entrepot)?'NULL':"'".$this->fk_entrepot."'").",";
		$sql.= " ".(! isset($this->value)?'NULL':"'".$this->value."'").",";
		$sql.= " ".(! isset($this->quant)?'NULL':"'".$this->quant."'").",";
		$sql.= " ".(! isset($this->price)?'NULL':"'".$this->price."'").",";
		$sql.= " ".(! isset($this->type_mouvement)?'NULL':"'".$this->type_mouvement."'").",";
		$sql.= " ".(! isset($this->fk_user_author)?'NULL':"'".$this->fk_user_author."'").",";
		$sql.= " ".(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").",";
		$sql.= " ".(! isset($this->fk_origin)?'NULL':"'".$this->fk_origin."'").",";
		$sql.= " ".(! isset($this->origintype)?'NULL':"'".$this->db->escape($this->origintype)."'").",";
		$sql.= " ".(! isset($this->inventorycode)?'NULL':"'".$this->db->escape($this->inventorycode)."'").",";
		$sql.= " ".(! isset($this->batch)?'NULL':"'".$this->db->escape($this->batch)."'").",";
		$sql.= " ".(! isset($this->eatby) || dol_strlen($this->eatby)==0?'NULL':$this->db->idate($this->eatby)).",";
		$sql.= " ".(! isset($this->sellby) || dol_strlen($this->sellby)==0?'NULL':$this->db->idate($this->sellby))."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."stock_mouvement_temp");

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
		$sql.= " t.ref,";
		$sql.= " t.tms,";
		$sql.= " t.datem,";
		$sql.= " t.fk_product,";
		$sql.= " t.fk_entrepot,";
		$sql.= " t.value,";
		$sql.= " t.quant,";
		$sql.= " t.price,";
		$sql.= " t.type_mouvement,";
		$sql.= " t.fk_user_author,";
		$sql.= " t.label,";
		$sql.= " t.fk_origin,";
		$sql.= " t.origintype,";
		$sql.= " t.inventorycode,";
		$sql.= " t.batch,";
		$sql.= " t.eatby,";
		$sql.= " t.sellby";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement_temp as t";
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
				$this->ref = $obj->ref;
				$this->tms = $this->db->jdate($obj->tms);
				$this->datem = $this->db->jdate($obj->datem);
				$this->fk_product = $obj->fk_product;
				$this->fk_entrepot = $obj->fk_entrepot;
				$this->value = $obj->value;
				$this->quant = $obj->quant;
				$this->price = $obj->price;
				$this->type_mouvement = $obj->type_mouvement;
				$this->fk_user_author = $obj->fk_user_author;
				$this->label = $obj->label;
				$this->fk_origin = $obj->fk_origin;
				$this->origintype = $obj->origintype;
				$this->inventorycode = $obj->inventorycode;
				$this->batch = $obj->batch;
				$this->eatby = $this->db->jdate($obj->eatby);
				$this->sellby = $this->db->jdate($obj->sellby);

                
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
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->fk_entrepot)) $this->fk_entrepot=trim($this->fk_entrepot);
		if (isset($this->value)) $this->value=trim($this->value);
		if (isset($this->quant)) $this->quant=trim($this->quant);
		if (isset($this->price)) $this->price=trim($this->price);
		if (isset($this->type_mouvement)) $this->type_mouvement=trim($this->type_mouvement);
		if (isset($this->fk_user_author)) $this->fk_user_author=trim($this->fk_user_author);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->fk_origin)) $this->fk_origin=trim($this->fk_origin);
		if (isset($this->origintype)) $this->origintype=trim($this->origintype);
		if (isset($this->inventorycode)) $this->inventorycode=trim($this->inventorycode);
		if (isset($this->batch)) $this->batch=trim($this->batch);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."stock_mouvement_temp SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " datem=".(dol_strlen($this->datem)!=0 ? "'".$this->db->idate($this->datem)."'" : 'null').",";
		$sql.= " fk_product=".(isset($this->fk_product)?$this->fk_product:"null").",";
		$sql.= " fk_entrepot=".(isset($this->fk_entrepot)?$this->fk_entrepot:"null").",";
		$sql.= " value=".(isset($this->value)?$this->value:"null").",";
		$sql.= " quant=".(isset($this->quant)?$this->quant:"null").",";
		$sql.= " price=".(isset($this->price)?$this->price:"null").",";
		$sql.= " type_mouvement=".(isset($this->type_mouvement)?$this->type_mouvement:"null").",";
		$sql.= " fk_user_author=".(isset($this->fk_user_author)?$this->fk_user_author:"null").",";
		$sql.= " label=".(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").",";
		$sql.= " fk_origin=".(isset($this->fk_origin)?$this->fk_origin:"null").",";
		$sql.= " origintype=".(isset($this->origintype)?"'".$this->db->escape($this->origintype)."'":"null").",";
		$sql.= " inventorycode=".(isset($this->inventorycode)?"'".$this->db->escape($this->inventorycode)."'":"null").",";
		$sql.= " batch=".(isset($this->batch)?"'".$this->db->escape($this->batch)."'":"null").",";
		$sql.= " eatby=".(dol_strlen($this->eatby)!=0 ? "'".$this->db->idate($this->eatby)."'" : 'null').",";
		$sql.= " sellby=".(dol_strlen($this->sellby)!=0 ? "'".$this->db->idate($this->sellby)."'" : 'null')."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."stock_mouvement_temp";
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

		$object=new Stockmouvementtemp($this->db);

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
		$this->ref='';
		$this->tms='';
		$this->datem='';
		$this->fk_product='';
		$this->fk_entrepot='';
		$this->value='';
		$this->quant='';
		$this->price='';
		$this->type_mouvement='';
		$this->fk_user_author='';
		$this->label='';
		$this->fk_origin='';
		$this->origintype='';
		$this->inventorycode='';
		$this->batch='';
		$this->eatby='';
		$this->sellby='';

		
	}

  /**
   *	Add a movement of stock (in one direction only)
   *
   *	@param		User	$user			User object
   *	@param		int		$fk_product		Id of product
   *	@param		int		$entrepot_id	Id of warehouse
   *	@param		int		$qty			Qty of movement (can be <0 or >0 depending on parameter type)
   *	@param		int		$type			Direction of movement:
   *										0=input (stock increase after stock transfert), 1=output (stock decrease after stock transfer),
   *										2=output (stock decrease), 3=input (stock increase)
   *                                      Note that qty should be > 0 with 0 or 3, < 0 with 1 or 2.
   *	@param		int		$price			Unit price HT of product, used to calculate average weighted price (PMP in french). If 0, average weighted price is not changed.
   *	@param		string	$label			Label of stock movement
   *	@param		string	$inventorycode	Inventory code
   *	@param		string	$datem			Force date of movement
   *	@param		date	$eatby			eat-by date
   *	@param		date	$sellby			sell-by date
   *	@param		string	$batch			batch number
   *	@param		boolean	$skip_batch		If set to true, stock movement is done without impacting batch record
   *	@return		int						<0 if KO, 0 if fk_product is null, >0 if OK
   */
	function _create($user, $fk_product, $entrepot_id, $ref, $qty, $type, $price=0, $label='', $inventorycode='', $datem='',$eatby='',$sellby='',$batch='',$skip_batch=false)
	{
	  global $conf, $langs;
	  
	  require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
	  $error = 0;
	  dol_syslog(get_class($this)."::_create start userid=$user->id, fk_product=$fk_product, warehouse=$entrepot_id, qty=$qty, type=$type, price=$price, label=$label, inventorycode=$inventorycode, datem=".$datem.", eatby=".$eatby.", sellby=".$sellby.", batch=".$batch.", skip_batch=".$skip_batch);
	  
	  // Clean parameters
	  if (empty($price)) $price=0;
	  $now=(! empty($datem) ? $datem : dol_now());
	  // Check parameters
	  if (empty($fk_product)) return 0;
	  if ($eatby < 0)
	    {
	      $this->errors[]='ErrorBadValueForParameterEatBy';
	      return -1;
	    }
	  if ($sellby < 0)
	    {
	      $this->errors[]='ErrorBadValueForParameterEatBy';
	      return -1;
	    }
	  
	  // Set properties of movement
	  $this->entity = $conf->entity;
	  $this->ref = $ref;
	  $this->product_id = $fk_product;
	  $this->entrepot_id = $entrepot_id;
	  $this->qty = $qty;
	  $this->type = $type;
    
	  $mvid = 0;
	  
	  $product = new Product($this->db);
	  $result=$product->fetch($fk_product);
	  if ($result < 0)
	    {
	      dol_print_error('',"Failed to fetch product");
	      return -1;
	    }
	  
	  $this->db->begin();
	  
	  $product->load_stock();
	  
	  // Test if product require batch data. If yes, and there is not, we throw an error.
	  if (! empty($conf->productbatch->enabled) && $product->hasbatch() && ! $skip_batch)
	    {
	      //if (empty($batch) && empty($eatby) && empty($sellby))
	      if (empty($batch))
		{
		  $this->errors[]=$langs->trans("ErrorTryToMakeMoveOnProductRequiringBatchData", $product->name);
		  dol_syslog("Try to make a movement of a product with status_batch on without any batch data");
		  
		  $this->db->rollback();
		  return -2;
		}
	      
	      // If a serial number is provided, we check that sellby and eatby match already existing serial
	      $sql = "SELECT pb.rowid, pb.batch, pb.eatby, pb.sellby FROM ".MAIN_DB_PREFIX."product_batch as pb, ".MAIN_DB_PREFIX."product_stock as ps";
	      $sql.= " WHERE pb.fk_product_stock = ps.rowid AND ps.fk_product = ".$fk_product." AND pb.batch = '".$this->db->escape($batch)."'";
	      dol_syslog(get_class($this)."::_create scan serial for this product to check if eatby and sellby match", LOG_DEBUG);
	      $resql = $this->db->query($sql);
	      if ($resql)
		{
		  $num = $this->db->num_rows($resql);
		  $i=0;
		  while ($i < $num)
		    {
		      $obj = $this->db->fetch_object($resql);
		      if ($this->db->jdate($obj->eatby) != $eatby)
			{
			  $this->errors[]=$langs->trans("ThisSerialAlreadyExistWithDifferentDate", $batch, $this->db->jdate($obj->eatby), $eatby);
			  dol_syslog($langs->trans("ThisSerialAlreadyExistWithDifferentDate", $batch, $this->db->jdate($obj->eatby), $eatby));
			  $this->db->rollback();
			  return -3;
			}
		      if ($this->db->jdate($obj->sellby) != $sellby)
			{
			  $this->errors[]=$langs->trans("ThisSerialAlreadyExistWithDifferentDate", $batch, $this->db->jdate($obj->sellby), $sellby);
			  dol_syslog($langs->trans("ThisSerialAlreadyExistWithDifferentDate", $batch, $this->db->jdate($obj->sellby), $sellby));
			  $this->db->rollback();
			  return -3;
			}
		      $i++;
		    }
		}
	      else
		{
		  dol_print_error($this->db);
		  $this->db->rollback();
		  return -1;
		}
	    }
	  
	  // TODO Check qty is ok for stock move.
	  if (! empty($conf->productbatch->enabled) && $product->hasbatch() && ! $skip_batch)
	    {
	      
	    }
	  else
	    {
	      
	    }
	  
	  // Define if we must make the stock change (If product type is a service or if stock is used also for services)
	  $movestock=0;
	  if ($product->type != Product::TYPE_SERVICE || ! empty($conf->global->STOCK_SUPPORTS_SERVICES)) $movestock=1;
	  if ($movestock && $entrepot_id > 0)	// Change stock for current product, change for subproduct is done after
	    {
	      if(!empty($this->origin)) {			// This is set by caller for tracking reason
		$origintype = $this->origin->element;
		$fk_origin = $this->origin->id;
	      } else {
		$origintype = '';
		$fk_origin = 0;
	      }
	      
	      $sql = "INSERT INTO ".MAIN_DB_PREFIX."stock_mouvement_temp(";
	      $sql.= " datem, fk_product, entity, ref, batch, eatby, sellby,";
	      $sql.= " fk_entrepot, value, type_mouvement, fk_user_author, label, inventorycode, price, fk_origin, origintype";
	      $sql.= ")";
	      $sql.= " VALUES ('".$this->db->idate($now)."', ".$this->product_id.", ";
	      $sql.= $this->entity.", ";
	      $sql.= " '".$this->ref."', ";
	      $sql.= " ".($batch?"'".$batch."'":"null").", ";
	      $sql.= " ".($eatby?"'".$this->db->idate($eatby)."'":"null").", ";
	      $sql.= " ".($sellby?"'".$this->db->idate($sellby)."'":"null").", ";
	      $sql.= " ".$this->entrepot_id.", ".$this->qty.", ".$this->type.",";
	      $sql.= " ".$user->id.",";
	      $sql.= " '".$this->db->escape($label)."',";
	      $sql.= " ".($inventorycode?"'".$this->db->escape($inventorycode)."'":"null").",";
	      $sql.= " '".price2num($price)."',";
	      $sql.= " '".$fk_origin."',";
	      $sql.= " '".$origintype."'";
	      $sql.= ")";
	      dol_syslog(get_class($this)."::_create", LOG_DEBUG);
	      $resql = $this->db->query($sql);
	      if ($resql)
		{
		  $mvid = $this->db->last_insert_id(MAIN_DB_PREFIX."stock_mouvement");
		  $this->id = $mvid;
		}
	      else
		{
		  $this->errors[]=$this->db->lasterror();
		  $error = -1;
		}
	      
	      // Define current values for qty and pmp
	      $oldqty=$product->stock_reel;
	      $oldpmp=$product->pmp;
	      $oldqtywarehouse=0;
	      //$oldpmpwarehouse=0;
	      
	      // Test if there is already a record for couple (warehouse / product)
	      $num = 0;
	      // if (! $error)
	      //   {
	      //     $sql = "SELECT rowid, reel FROM ".MAIN_DB_PREFIX."product_stock";
	      //     $sql.= " WHERE fk_entrepot = ".$entrepot_id." AND fk_product = ".$fk_product;		// This is a unique key
	      
	      //     dol_syslog(get_class($this)."::_create", LOG_DEBUG);
	      //     $resql=$this->db->query($sql);
	      //     if ($resql)
	      //       {
	      // 	$obj = $this->db->fetch_object($resql);
	      // 	if ($obj)
	      // 	  {
	      // 	    $num = 1;
	      // 	    $oldqtywarehouse = $obj->reel;
	      // 	    //$oldpmpwarehouse = $obj->pmp;
	      // 	    $fk_product_stock = $obj->rowid;
	      // 	  }
	      // 	$this->db->free($resql);
	      //       }
	      //     else
	      //       {
	      // 	$this->errors[]=$this->db->lasterror();
	      // 	$error = -2;
	      //       }
	      //   }
	      
	      // // Calculate new PMP.
	      // $newpmp=0;
	      // //$newpmpwarehouse=0;
	      // if (! $error)
	      //   {
	      //     // Note: PMP is calculated on stock input only (type of movement = 0 or 3). If type == 0 or 3, qty should be > 0.
	      //     // Note: Price should always be >0 or 0. PMP should be always >0 (calculated on input)
	      //     if (($type == 0 || $type == 3) && $price > 0)
	      //       {
	      // 	// If we will change PMP for the warehouse we edit and the product, we must first check/clean that PMP is defined
	      // 	// on every stock entry with old value (so global updated value will match recalculated value from product_stock)
	      // 	/*		$sql = "UPDATE ".MAIN_DB_PREFIX."product_stock SET pmp = ".($oldpmp?$oldpmp:'0');
	      // 	 $sql.= " WHERE pmp = 0 AND fk_product = ".$fk_product;
	      // 	 dol_syslog(get_class($this)."::_create", LOG_DEBUG);
	      // 	 $resql=$this->db->query($sql);
	      // 	 if (! $resql)
	      // 	 {
	      // 	 $this->errors[]=$this->db->lasterror();
	      // 	 $error = -4;
	      // 	 }
	      // 	*/
	      // 	$oldqtytouse=($oldqty >= 0?$oldqty:0);
	      // 	// We make a test on oldpmp>0 to avoid to use normal rule on old data with no pmp field defined
	      // 	if ($oldpmp > 0) $newpmp=price2num((($oldqtytouse * $oldpmp) + ($qty * $price)) / ($oldqtytouse + $qty), 'MU');
	      // 	else
	      // 	  {
	      // 	    $newpmp=$price; // For this product, PMP was not yet set. We set it to input price.
	      // 	  }
	      // 	/*
	      // 	 $oldqtywarehousetouse=$oldqtywarehouse;
	      // 	 if ($oldpmpwarehouse > 0) $newpmpwarehouse=price2num((($oldqtywarehousetouse * $oldpmpwarehouse) + ($qty * $price)) / ($oldqtywarehousetouse + $qty), 'MU');
	      // 	 else $newpmpwarehouse=$price;
	      // 	*/
	      // 	//print "oldqtytouse=".$oldqtytouse." oldpmp=".$oldpmp." oldqtywarehousetouse=".$oldqtywarehousetouse." oldpmpwarehouse=".$oldpmpwarehouse." ";
	      // 	//print "qty=".$qty." newpmp=".$newpmp." newpmpwarehouse=".$newpmpwarehouse;
	      // 	//exit;
	      //       }
	      //     else if ($type == 1 || $type == 2)
	      //       {
	      // 	// After a stock decrease, we don't change value of PMP for product.
	      // 	$newpmp = $oldpmp;
	      //       }
	      //     else
	      //       {
	      // 	$newpmp = $oldpmp;
	      // 	//$newpmpwarehouse = $oldpmpwarehouse;
	      //       }
	      //   }
	      
	      // // Update stock quantity
	      // if (! $error)
	      //   {
	      //     if ($num > 0)
	      //       {
	      // 	//$sql = "UPDATE ".MAIN_DB_PREFIX."product_stock SET pmp = ".$newpmpwarehouse.", reel = reel + ".$qty;
	      // 	$sql = "UPDATE ".MAIN_DB_PREFIX."product_stock SET reel = reel + ".$qty;
	      // 	$sql.= " WHERE fk_entrepot = ".$entrepot_id." AND fk_product = ".$fk_product;
	      //       }
	      //     else
	      //       {
	      // 	$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_stock";
	      // 	//$sql.= " (pmp, reel, fk_entrepot, fk_product) VALUES ";
	      // 	//$sql.= " (".$newpmpwarehouse.", ".$qty.", ".$entrepot_id.", ".$fk_product.")";
	      // 	$sql.= " (reel, fk_entrepot, fk_product) VALUES ";
	      // 	$sql.= " (".$qty.", ".$entrepot_id.", ".$fk_product.")";
	      //       }
	      
	      //     dol_syslog(get_class($this)."::_create", LOG_DEBUG);
	      //     $resql=$this->db->query($sql);
	      //     if (! $resql)
	      //       {
	      // 	$this->errors[]=$this->db->lasterror();
	      // 	$error = -3;
	      //       }
	      //     else if (empty($fk_product_stock))
	      //       {
	      // 	$fk_product_stock = $this->db->last_insert_id(MAIN_DB_PREFIX."product_stock");
	      //       }
	      
	      //   }
	      
	      // // Update detail stock for batch product
	      // if (! $error && ! empty($conf->productbatch->enabled) && $product->hasbatch() && ! $skip_batch)
	      //   {
	      //     $param_batch=array('fk_product_stock' =>$fk_product_stock, 'eatby'=>$eatby, 'sellby'=>$sellby, 'batchnumber'=>$batch);
	      //     $result=$this->_create_batch($param_batch, $qty);
	      //     if ($result<0) $error++;
	      //   }
	      
	      // // Update PMP and denormalized value of stock qty at product level
	      // if (! $error)
	      //   {
	      //     $sql = "UPDATE ".MAIN_DB_PREFIX."product SET pmp = ".$newpmp.", stock = ".$this->db->ifsql("stock IS NULL", 0, "stock") . " + ".$qty;
	      //     $sql.= " WHERE rowid = ".$fk_product;
	      //     // May be this request is better:
	      //     // UPDATE llx_product p SET p.stock= (SELECT SUM(ps.reel) FROM llx_product_stock ps WHERE ps.fk_product = p.rowid);
	      
	      //     dol_syslog(get_class($this)."::_create", LOG_DEBUG);
	      //     $resql=$this->db->query($sql);
	      //     if (! $resql)
	      //       {
	      // 	$this->errors[]=$this->db->lasterror();
	      // 	$error = -4;
	      //       }
	      //   }
	    }
	  
	  // Add movement for sub products (recursive call)
	  if (! $error && ! empty($conf->global->PRODUIT_SOUSPRODUITS) && empty($conf->global->INDEPENDANT_SUBPRODUCT_STOCK))
	    {
	      $error = $this->_createSubProduct($user, $fk_product, $entrepot_id, $qty, $type, 0, $label, $inventorycode);	// we use 0 as price, because pmp is not changed for subproduct
	    }
	  
	  if ($movestock && ! $error)
	    {
	      // Call trigger
	      $result=$this->call_trigger('STOCK_MOVEMENT',$user);
	      if ($result < 0) $error++;
	      // End call triggers
	    }
	  
	  if (! $error)
	    {
	      $this->db->commit();
	      return $mvid;
	    }
	  else
	    {
	      $this->db->rollback();
	      dol_syslog(get_class($this)."::_create error code=".$error, LOG_ERR);
	      return -6;
	    }
	}

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
	  
	  // if (! empty($conf->global->ALMACEN_ADDON))
	  //   {
	  $file = "mod_almacen_ubuntubo_transf.php";
	  // Chargement de la classe de numerotation
	  $classname = "mod_almacen_ubuntubo_transf";
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
		      dol_print_error($db,"Stockmouvementtemp::getNextNumRef ".$obj->error);
		      return "";
		    }
		}
	      else
		{
		  print $langs->trans("Error")." ".$langs->trans("Error_ALMACEN_ADDON_NotDefined");
		  return "";
		}
	  //   }
	  // else
	  //   {
	  //     print $langs->trans("Error")." ".$langs->trans("Error_ALMACEN_ADDON_NotDefined");
	  //     return "";
	  //   }
	}
	
}
?>
