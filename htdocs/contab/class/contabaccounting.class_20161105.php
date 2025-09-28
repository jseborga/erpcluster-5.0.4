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
 *  \file       dev/skeletons/contabaccounting.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-12-06 17:54
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Contabaccounting extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='contabaccounting';			//!< Id that identify managed objects
	var $table_element='contab_accounting';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $ref;
	var $entity;
	var $cta_class;
	var $cta_normal;
	var $cta_top;
	var $cta_name;
	var $statut;
	var $tms='';

    


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
        
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->cta_class)) $this->cta_class=trim($this->cta_class);
		if (isset($this->cta_normal)) $this->cta_normal=trim($this->cta_normal);
		if (isset($this->cta_top)) $this->cta_top=trim($this->cta_top);
		if (isset($this->cta_name)) $this->cta_name=trim($this->cta_name);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."contab_accounting(";
		
		$sql.= "ref,";
		$sql.= "entity,";
		$sql.= "cta_class,";
		$sql.= "cta_normal,";
		$sql.= "cta_top,";
		$sql.= "cta_name,";
		$sql.= "statut,";
		$sql.= "tms";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->cta_class)?'NULL':"'".$this->cta_class."'").",";
		$sql.= " ".(! isset($this->cta_normal)?'NULL':"'".$this->cta_normal."'").",";
		$sql.= " ".(! isset($this->cta_top)?'NULL':"'".$this->cta_top."'").",";
		$sql.= " ".(! isset($this->cta_name)?'NULL':"'".$this->db->escape($this->cta_name)."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'").",";
		$sql.= " ".(date('YmdHis'));

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."contab_accounting");

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
    function fetch($id,$ref='')
    {
    	global $langs;
	if (empty($id) && empty($ref))
	  return -1;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.ref,";
		$sql.= " t.entity,";
		$sql.= " t.cta_class,";
		$sql.= " t.cta_normal,";
		$sql.= " t.cta_top,";
		$sql.= " t.cta_name,";
		$sql.= " t.statut,";
		$sql.= " t.tms";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."contab_accounting as t";
	if (!empty($id))
	  $sql.= " WHERE t.rowid = ".$id;
	elseif (!empty($ref))
	  $sql.= " WHERE t.ref = ".$ref;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->ref = $obj->ref;
				$this->entity = $obj->entity;
				$this->cta_class = $obj->cta_class;
				$this->cta_normal = $obj->cta_normal;
				$this->cta_top = $obj->cta_top;
				$this->cta_name = $obj->cta_name;
				$this->statut = $obj->statut;
				$this->tms = $this->db->jdate($obj->tms);

                
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
        
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->cta_class)) $this->cta_class=trim($this->cta_class);
		if (isset($this->cta_normal)) $this->cta_normal=trim($this->cta_normal);
		if (isset($this->cta_top)) $this->cta_top=trim($this->cta_top);
		if (isset($this->cta_name)) $this->cta_name=trim($this->cta_name);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."contab_accounting SET";
        
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " cta_class=".(isset($this->cta_class)?$this->cta_class:"null").",";
		$sql.= " cta_normal=".(isset($this->cta_normal)?$this->cta_normal:"null").",";
		$sql.= " cta_top=".(isset($this->cta_top)?$this->cta_top:"null").",";
		$sql.= " cta_name=".(isset($this->cta_name)?"'".$this->db->escape($this->cta_name)."'":"null").",";
		$sql.= " statut=".(isset($this->statut)?$this->statut:"null").",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null')."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."contab_accounting";
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

		$object=new Contabaccounting($this->db);

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
		
		$this->ref='';
		$this->entity='';
		$this->cta_class='';
		$this->cta_normal='';
		$this->cta_top='';
		$this->cta_name='';
		$this->statut='';
		$this->tms='';

		
	}


    /**
     *  Return combo list of activated countries, into language of user
     *
     *  @param	string	$selected       Id or Code or Label of preselected country
     *  @param  string	$htmlname       Name of html select object
     *  @param  string	$htmloption     Options html on select object
     *  @param	string	$maxlength		Max length for labels (0=no limit)
     *  @return string           		HTML string with select
     */
	function select_account($selected='',$htmlname='fk_account',$htmloption='',$maxlength=0,$showempty=0,$type=0,$mode=1)
    {
        global $conf,$langs;

        $langs->load("contab@contab");

        $out='';
        $countryArray=array();
        $label=array();
	if ($mode == 1)
	  $sql = "SELECT rowid, ref as code_iso, cta_name as label";
	else
	  $sql = "SELECT rowid, cta_name as code_iso, ref as label";

        $sql.= " FROM ".MAIN_DB_PREFIX."contab_accounting";
        $sql.= " WHERE statut = 1";
	if ($type)
	  $sql.= " AND cta_class = ".$type;
        $sql.= " ORDER BY ref ASC";
        dol_syslog(get_class($this)."::select_account sql=".$sql);
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
                    $countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Accounting".$obj->code_iso)!="Accounting".$obj->code_iso?$langs->transnoentitiesnoconv("Accounting".$obj->code_iso):($obj->label!='-'?$obj->label:''));
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
     *	Return statut label of Order
     *
     *	@param      int		$mode       0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *	@return     string      		Libelle
     */
    function getLibStatut($mode)
    {
        return $this->LibStatut($this->statut,$mode);
    }

    /**
     *	Return label of statut
     *
     *	@param		int		$statut      	Id statut
     *  @param      int		$facturee    	if invoiced
     *	@param      int		$mode        	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *  @return     string					Label of statut
     */
    function LibStatut($statut,$mode)
    {
        global $langs;
        //print 'x'.$statut.'-'.$facturee;
        if ($mode == 0)
        {
            if ($statut==-1) return $langs->trans('StatusOrderCanceled');
            if ($statut==0) return $langs->trans('StatusOrderDraft');
            if ($statut==1) return $langs->trans('StatusOrderValidated');
        }
        elseif ($mode == 1)
        {
            if ($statut==-1) return $langs->trans('StatusOrderCanceledShort');
            if ($statut==0) return $langs->trans('StatusOrderDraftShort');
            if ($statut==1) return $langs->trans('StatusOrderValidatedShort');
        }
        elseif ($mode == 2)
        {
            if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceledShort');
            if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraftShort');
            if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1').' '.$langs->trans('StatusOrderValidatedShort');
        }
        elseif ($mode == 3)
        {
            if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5');
            if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0');
            if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1');
            if ($statut==2) return img_picto($langs->trans('StatusOrderSentShort'),'statut3');
        }
        elseif ($mode == 4)
        {
    	  if ($statut==0) return img_picto($langs->trans('Statusnotlocked'),'statut0').' '.$langs->trans('Statusnotlocked');
    	  if ($statut==1) return img_picto($langs->trans('Statuslocked'),'statut1').' '.$langs->trans('Statuslocked');
        }
        elseif ($mode == 5)
    	  {
            if ($statut==0) return $langs->trans('StatusnotlockedShort').' '.img_picto($langs->trans('StatusOrderDraft'),'statut0');
            if ($statut==1) return $langs->trans('StatuslockedShort').' '.img_picto($langs->trans('StatusOrderValidated'),'statut1');
        }
        elseif ($mode == 6)
        {
    	  if ($statut==0) return img_picto($langs->trans('Statusnotactived'),'statut0').' '.$langs->trans('Statusnotactived');
    	  if ($statut==1) return img_picto($langs->trans('Statusactived'),'statut1').' '.$langs->trans('Statusactived');
        }
    }

    /**
     *  Return list of orders (eventuelly filtered on a user) into an array
     *
     *  @param      int		$brouillon      0=non brouillon, 1=brouillon
     *  @param      User	$user           Objet user de filtre
     *  @return     int             		-1 if KO, array with result if OK
     */
    function liste_array($empty="")
    {
      global $conf,$langs;
      
      $ga = array();
      if ($empty == 1)
	$ga[0] = $langs->trans("Select");
      $sql = "SELECT ca.rowid, ca.ref, ca.cta_name";
      $sql.= " FROM ".MAIN_DB_PREFIX."contab_accounting AS ca ";
      $sql.= " ORDER BY ca.ref ";
      
      $result=$this->db->query($sql);
      if ($result)
	{
	  $numc = $this->db->num_rows($result);
	  if ($numc)
	    {
	      $i = 0;
	      while ($i < $numc)
		{
		  $obj = $this->db->fetch_object($result);
		  $ga[$obj->rowid] = $obj->ref.' '.$obj->cta_name;
		  $i++;
		}
	    }
	  return $ga;
	}
      else
	{
	  dol_print_error($this->db);
	  return -1;
	}
    }

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$account    account
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_account($account)
    {
      global $langs,$conf;
      $sql = "SELECT";
      $sql.= " t.rowid,";
      
      $sql.= " t.ref,";
      $sql.= " t.entity,";
      $sql.= " t.cta_class,";
      $sql.= " t.cta_normal,";
      $sql.= " t.cta_top,";
      $sql.= " t.cta_name,";
      $sql.= " t.tms,";
      $sql.= " t.statut";
      
		
      $sql.= " FROM ".MAIN_DB_PREFIX."contab_accounting as t";
      $sql.= " WHERE t.ref = ".$account;
      $sql.= " AND entity = ".$conf->entity;
      dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
      $resql=$this->db->query($sql);
      if ($resql)
        {
	  if ($this->db->num_rows($resql))
            {
	      $obj = $this->db->fetch_object($resql);
	      
	      $this->id    = $obj->rowid;
              
	      $this->ref = $obj->ref;
	      $this->entity = $obj->entity;
	      $this->cta_class = $obj->cta_class;
	      $this->cta_normal = $obj->cta_normal;
	      $this->cta_top = $obj->cta_top;
	      $this->cta_name = $obj->cta_name;
	      $this->tms = $obj->tms;
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

    function list_account($accountini,$accountfin)
    {
      global $conf;
      $sql = "SELECT ca.ref, ca.cta_normal ";
      $sql.= " FROM ".MAIN_DB_PREFIX."contab_accounting AS ca ";
      $sql.= " WHERE ca.entity = ".$conf->entity;
      $sql.= " AND ca.ref BETWEEN '".$accountini."' AND '".$accountfin."' ";
      $sql.= " ORDER BY ca.ref ";
      $aArray = array();
      $result=$this->db->query($sql);
      if ($result)
	{
	  $numc = $this->db->num_rows($result);
	  if ($numc)
	    {
	      $i = 0;
	      while ($i < $numc)
		{
		  $obj = $this->db->fetch_object($result);
		  $aArray[$obj->ref] = $obj->cta_normal;
		  $i++;
		}
	    }
	  return $aArray;
	}
      else
	{
	  dol_print_error($this->db);
	  return -1;
	}
    }

}
?>
