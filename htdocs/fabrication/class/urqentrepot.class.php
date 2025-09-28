<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 *  \file       dev/skeletons/urqentrepot.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2012-12-12 21:58
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Urqentrepot // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='urqentrepot';			//!< Id that identify managed objects
	//var $table_element='urqentrepot';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_entrepot;
	var $tipo;

    


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
     *  @param	User	$user        User that create
     *  @param  int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return int      		   	 <0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
	$error=0;

	// Clean parameters
        
	if (isset($this->fk_entrepot)) $this->fk_entrepot=trim($this->fk_entrepot);
	if (isset($this->tipo)) $this->tipo=trim($this->tipo);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
	$sql = "INSERT INTO ".MAIN_DB_PREFIX."urq_entrepot(";
	$sql.= "rowid,";
	$sql.= "fk_entrepot,";
	$sql.= "tipo";
		
        $sql.= ") VALUES (";
        
	$sql.= " ".(! isset($this->rowid)?'NULL':"'".$this->rowid."'").",";
	$sql.= " ".(! isset($this->fk_entrepot)?'NULL':"'".$this->fk_entrepot."'").",";
	$sql.= " ".(! isset($this->tipo)?'NULL':"'".$this->db->escape($this->tipo)."'")."";

        
	$sql.= ")";

	$this->db->begin();

	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

	if (! $error)
        {
	  $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."urq_entrepot");

	  if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action call a trigger.

	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
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
     *  Load object in memory from database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_entrepot,";
		$sql.= " t.tipo";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."urq_entrepot as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->fk_entrepot = $obj->fk_entrepot;
				$this->tipo = $obj->tipo;

                
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
     *  @param	User	$user        User that modify
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return int     		   	 <0 if KO, >0 if OK
     */
    function update($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        
		if (isset($this->fk_entrepot)) $this->fk_entrepot=trim($this->fk_entrepot);
		if (isset($this->tipo)) $this->tipo=trim($this->tipo);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."urq_entrepot SET";
        
		$sql.= " fk_entrepot=".(isset($this->fk_entrepot)?$this->fk_entrepot:"null").",";
		$sql.= " tipo=".(isset($this->tipo)?"'".$this->db->escape($this->tipo)."'":"null")."";

        
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
	            // want this action call a trigger.

	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
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
     *	@param  User	$user        User that delete
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
		        // want this action call a trigger.

		        //// Call triggers
		        //include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
		        //$interface=new Interfaces($this->db);
		        //$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
		        //if ($result < 0) { $error++; $this->errors=$interface->errors; }
		        //// End call triggers
			}
		}

		if (! $error)
		{
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."urq_entrepot";
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

		$object=new Urqentrepot($this->db);

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
		
		$this->fk_entrepot='';
		$this->tipo='';

		
	}
	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @return string           		HTML string with select
	 */
	function select_padre($selected='',$htmlname='fk_entrepot',$htmloption='')
	{
	  global $conf,$langs;
	  
	  $langs->load("dict");
	  
	  $out='';
	  $padreArray=array();
	  $label=array();
	  
	  $sql = "SELECT rowid, description, label";
	  $sql.= " FROM ".MAIN_DB_PREFIX."entrepot AS e ";
	  $sql.= " WHERE statut = 1";
	  $sql.= " ORDER BY description ASC";
	  
	  dol_syslog(get_class($this)."::select_country sql=".$sql);
	  $resql=$this->db->query($sql);
	  if ($resql)
	    {
	      $out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
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
                    $countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Country".$obj->code_iso)!="Country".$obj->code_iso?$langs->transnoentitiesnoconv("Country".$obj->code_iso):($obj->label!='-'?$obj->label:''));
                    $label[$i] 	= $countryArray[$i]['label'];
                    $i++;
		    }
		  
		  array_multisort($label, SORT_ASC, $countryArray);
		$out.='<option value="-1"'.($id==-1?' selected="selected"':'').'>&nbsp;</option>'."\n";
		
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
                    $out.= $row['label'];
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
	 *  Return list of possible payments modes
	 *
	 *  @param	int		$selected       Id du mode de paiement pre-selectionne
	 *  @param  string	$htmlname       Name of html select html
	 *  @param  string	$filtertype     For filter
	 *  @param  int		$empty			1=Can be empty, 0 if not
	 * 	@param	int		$disabled		1=Select is disabled
	 * 	@param	int		$fk_product		Add quantity of stock in label for product with id fk_product. Nothing if 0.
	 * 	@return	string					HTML select
	 */
	function selectWarehouses($selected='',$htmlname='idwarehouse',$filtertype='',$empty=0,$disabled=0,$fk_product=0,$filterId=array(),$event=array())
	{
	  global $langs,$user;
	  dol_syslog(get_class($this)."::selectWarehouses $selected, $htmlname, $filtertype, $empty, $disabled, $fk_product",LOG_DEBUG);
	  
	  $this->loadWarehouses($fk_product,$filterId);
	  
            if ($conf->use_javascript_ajax && $conf->global->COMPANY_USE_SEARCH_TO_SELECT && ! $forcecombo)
            {
                //$minLength = (is_numeric($conf->global->COMPANY_USE_SEARCH_TO_SELECT)?$conf->global->COMPANY_USE_SEARCH_TO_SELECT:2);
                //$out.= ajax_combobox($htmlname, $event, $conf->global->COMPANY_USE_SEARCH_TO_SELECT);
	    }
	  $out='<select class="flat"'.($disabled?' disabled="disabled"':'').' id="'.$htmlname.'" name="'.($htmlname.($disabled?'_disabled':'')).'">';
	  if ($empty) $out.='<option value="-1">&nbsp;</option>';
	  foreach($this->cache_warehouses as $id => $arraytypes)
	    {
	      $out.='<option value="'.$id.'"';
	      // Si selected est text, on compare avec code, sinon avec id
	      if ($selected == $id) $out.=' selected="selected"';
	      $out.='>';
	      $out.=$arraytypes['label'];
	      if ($fk_product) $out.=' ('.$langs->trans("Stock").': '.($arraytypes['stock']>0?$arraytypes['stock']:'?').')';
	      $out.='</option>';
	    }
	  $out.='</select>';
	  if ($disabled) $out.='<input type="hidden" name="'.$htmlname.'" value="'.$selected.'">';
	  
	  //count($this->cache_warehouses);
	  return $out;
	}

	/**
	 * Load in cache array list of warehouses
	 * If fk_product is not 0, we do not use cache
	 *
	 * @param	int		$fk_product		Add quantity of stock in label for product with id fk_product. Nothing if 0.
	 * @return  int  		    		Nb of loaded lines, 0 if already loaded, <0 if KO
	 */
	function loadWarehouses($fk_product=0,$filterId=array())
	{
	  global $conf, $langs;
	  
	  if (empty($fk_product) && count($this->cache_warehouses)) return 0;    // Cache already loaded and we do not want a list with information specific to a product
	  $sql = "SELECT e.rowid, e.label";
	  if ($fk_product) $sql.= ", ps.reel";
	  $sql.= " FROM ".MAIN_DB_PREFIX."entrepot as e";
	  if ($fk_product)
	    {
	      $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_stock as ps on ps.fk_entrepot = e.rowid";
	      $sql.= " AND ps.fk_product = '".$fk_product."'";
	    }
	  $sql.= " WHERE e.entity = ".$conf->entity;
	  $sql.= " AND e.statut = 1";
	  if (!empty($filterId))
	    $sql.= " AND e.rowid IN (".implode(',',$filterId).")";

	  $sql.= " ORDER BY e.label";
	  
	  dol_syslog(get_class($this).'::loadWarehouses sql='.$sql,LOG_DEBUG);
	  $resql = $this->db->query($sql);
	  if ($resql)
	    {
	      $num = $this->db->num_rows($resql);
	      $i = 0;
	      while ($i < $num)
		{
		  $obj = $this->db->fetch_object($resql);
		  
		  $this->cache_warehouses[$obj->rowid]['id'] =$obj->rowid;
		  $this->cache_warehouses[$obj->rowid]['label']=$obj->label;
		  if ($fk_product) $this->cache_warehouses[$obj->rowid]['stock']=$obj->reel;
		  $i++;
		}
	      return $num;
	    }
	  else
	    {
	      dol_print_error($this->db);
	      return -1;
	    }
	}

}
?>
