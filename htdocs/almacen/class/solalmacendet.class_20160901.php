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
 *  \file       dev/skeletons/solalmacendet.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-12-24 13:48
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");

/**
 *	Put here description of your class
 */
class Solalmacendet extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='solalmacendet';			//!< Id that identify managed objects
	var $table_element='solalmacendet';		//!< Name of table without prefix where object is stored

    var $id;

	var $fk_almacen;
	var $fk_product;
	var $qty;
	var $qty_livree;
	var $price;
	var $date_shipping='';




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

		if (isset($this->fk_almacen)) $this->fk_almacen=trim($this->fk_almacen);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->qty)) $this->qty=trim($this->qty);
		if (isset($this->qty_livree)) $this->qty_livree=trim($this->qty_livree);
		if (isset($this->price)) $this->price=trim($this->price);



		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."sol_almacendet(";

		$sql.= "fk_almacen,";
		$sql.= "fk_product,";
		$sql.= "qty,";
		$sql.= "qty_livree,";
		$sql.= "price,";
		$sql.= "date_shipping";


        $sql.= ") VALUES (";

		$sql.= " ".(! isset($this->fk_almacen)?'NULL':"'".$this->fk_almacen."'").",";
		$sql.= " ".(! isset($this->fk_product)?'NULL':"'".$this->fk_product."'").",";
		$sql.= " ".(! isset($this->qty)?'NULL':"'".$this->qty."'").",";
		$sql.= " ".(! isset($this->qty_livree)?'NULL':"'".$this->qty_livree."'").",";
		$sql.= " ".(! isset($this->price)?'NULL':"'".$this->price."'").",";
		$sql.= " ".(! isset($this->date_shipping) || dol_strlen($this->date_shipping)==0?'NULL':$this->db->idate($this->date_shipping))."";


		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."sol_almacendet");

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

		$sql.= " t.fk_almacen,";
		$sql.= " t.fk_product,";
		$sql.= " t.qty,";
		$sql.= " t.qty_livree,";
		$sql.= " t.price,";
		$sql.= " t.date_shipping";


        $sql.= " FROM ".MAIN_DB_PREFIX."sol_almacendet as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;

				$this->fk_almacen = $obj->fk_almacen;
				$this->fk_product = $obj->fk_product;
				$this->qty = $obj->qty;
				$this->qty_livree = $obj->qty_livree;
				$this->price = $obj->price;
				$this->date_shipping = $this->db->jdate($obj->date_shipping);


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

		if (isset($this->fk_almacen)) $this->fk_almacen=trim($this->fk_almacen);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->qty)) $this->qty=trim($this->qty);
		if (isset($this->qty_livree)) $this->qty_livree=trim($this->qty_livree);
		if (isset($this->price)) $this->price=trim($this->price);



		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."sol_almacendet SET";

		$sql.= " fk_almacen=".(isset($this->fk_almacen)?$this->fk_almacen:"null").",";
		$sql.= " fk_product=".(isset($this->fk_product)?$this->fk_product:"null").",";
		$sql.= " qty=".(isset($this->qty)?$this->qty:"null").",";
		$sql.= " qty_livree=".(isset($this->qty_livree)?$this->qty_livree:"null").",";
		$sql.= " price=".(isset($this->price)?$this->price:"null").",";
		$sql.= " date_shipping=".(dol_strlen($this->date_shipping)!=0 ? "'".$this->db->idate($this->date_shipping)."'" : 'null')."";


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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."sol_almacendet";
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

		$object=new Solalmacendet($this->db);

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

		$this->fk_almacen='';
		$this->fk_product='';
		$this->qty='';
		$this->qty_livree='';
		$this->price='';
		$this->date_shipping='';


	}


    /**
     *	Add an order line into database (linked to product/service or not)
     *
     *	@param      int				$commandeid      	Id of line
     *	@param      string			$desc            	Description of line
     *	@param      double			$pu_ht    	        Unit price (without tax)
     *	@param      double			$qty             	Quantite
     *	@param      double			$txtva           	Taux de tva force, sinon -1
     *	@param      double			$txlocaltax1		Local tax 1 rate
     *	@param      double			$txlocaltax2		Local tax 2 rate
     *	@param      int				$fk_product      	Id du produit/service predefini
     *	@param      double			$remise_percent  	Pourcentage de remise de la ligne
     *	@param      int				$info_bits			Bits de type de lignes
     *	@param      int				$fk_remise_except	Id remise
     *	@param      string			$price_base_type	HT or TTC
     *	@param      double			$pu_ttc    		    Prix unitaire TTC
     *	@param      timestamp		$date_start       	Start date of the line - Added by Matelli (See http://matelli.fr/showcases/patchs-dolibarr/add-dates-in-order-lines.html)
     *	@param      timestamp		$date_end         	End date of the line - Added by Matelli (See http://matelli.fr/showcases/patchs-dolibarr/add-dates-in-order-lines.html)
     *	@param      int				$type				Type of line (0=product, 1=service)
     *	@param      int				$rang             	Position of line
     *	@param		int				$special_code		Special code (also used by externals modules!)
     *	@param		int				$fk_parent_line		Parent line
     *  @param		int				$fk_fournprice		Id supplier price
     *  @param		int				$pa_ht				Buying price (without tax)
     *  @param		string			$label				Label
     *	@return     int             					>0 if OK, <0 if KO
     *
     *	@see        add_product
     *
     *	Les parametres sont deja cense etre juste et avec valeurs finales a l'appel
     *	de cette methode. Aussi, pour le taux tva, il doit deja avoir ete defini
     *	par l'appelant par la methode get_default_tva(societe_vendeuse,societe_acheteuse,produit)
     *	et le desc doit deja avoir la bonne valeur (a l'appelant de gerer le multilangue)
     */
	function addline($almacenid, $fk_product=0, $qty)
	{
	  dol_syslog(get_class($this)."::addline almacenid=$almacenid, fk_product=$fk_product,  qty=$qty", LOG_DEBUG);

        // Clean parameters
        if (empty($qty)) $qty=0;

        $qty=price2num($qty);

        if ($this->statut == 0)
        {
            $this->db->begin();

            // Insert line
            $this->line=new OrderLine($this->db);

            $this->line->fk_commande=$commandeid;
            $this->line->label=$label;
            $this->line->desc=$desc;
            $this->line->qty=$qty;
            $this->line->tva_tx=$txtva;
            $this->line->localtax1_tx=$txlocaltax1;
            $this->line->localtax2_tx=$txlocaltax2;
            $this->line->fk_product=$fk_product;
            $this->line->fk_remise_except=$fk_remise_except;
            $this->line->remise_percent=$remise_percent;
            $this->line->subprice=$pu_ht;
            $this->line->rang=$rangtouse;
            $this->line->info_bits=$info_bits;
            $this->line->total_ht=$total_ht;
            $this->line->total_tva=$total_tva;
            $this->line->total_localtax1=$total_localtax1;
            $this->line->total_localtax2=$total_localtax2;
            $this->line->total_ttc=$total_ttc;
            $this->line->product_type=$type;
            $this->line->special_code=$special_code;
            $this->line->fk_parent_line=$fk_parent_line;

            $this->line->date_start=$date_start;
            $this->line->date_end=$date_end;

			// infos marge
			$this->line->fk_fournprice = $fk_fournprice;
			$this->line->pa_ht = $pa_ht;

            // TODO Ne plus utiliser
            $this->line->price=$price;
            $this->line->remise=$remise;

            $result=$this->line->insert();
            if ($result > 0)
            {
                // Reorder if child line
                if (! empty($fk_parent_line)) $this->line_order(true,'DESC');

                // Mise a jour informations denormalisees au niveau de la commande meme
                $this->id=$commandeid;	// TODO A virer
                $result=$this->update_price(1);
                if ($result > 0)
                {
                    $this->db->commit();
                    return $this->line->rowid;
                }
                else
                {
                    $this->db->rollback();
                    return -1;
                }
            }
            else
            {
                $this->error=$this->line->error;
                dol_syslog(get_class($this)."::addline error=".$this->error, LOG_ERR);
                $this->db->rollback();
                return -2;
            }
        }
    }
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$fk_solalmacen llave padre
     *  @return array         	<0 if KO, len(aArray) >0 if OK
     */
    function list_item($fk_solalmacen)
    {
      global $conf,$langs;
        $sql = "SELECT";
	$sql.= " t.rowid,";

	$sql.= " t.fk_almacen,";
	$sql.= " t.fk_product,";
	$sql.= " t.qty,";
	$sql.= " t.qty_livree,";
	$sql.= " t.price,";
	$sql.= " t.date_shipping";

        $sql.= " FROM ".MAIN_DB_PREFIX."sol_almacendet as t";
        $sql.= " WHERE t.fk_almacen = ".$fk_solalmacen;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
	$limit = $conf->liste_limit;
        if ($resql)
        {
	  $this->aArray = array();
	  if ($this->db->num_rows($resql))
            {
	      $num = $this->db->num_rows($resql);
	      $i = 0;
	      while ($i < min($num,$limit))
		{
		  $obj = $this->db->fetch_object($resql);
		  $this->aArray[$obj->rowid] =
		    array(
			  'id'            => $obj->rowid,
			  'fk_almacen'    => $obj->fk_almacen,
			  'fk_product'    => $obj->fk_product,
			  'qty'           => $obj->qty,
			  'qty_livree'    => $obj->qty_livree,
			  'price'         => $obj->price,
			  'date_shipping' => $this->db->jdate($obj->date_shipping)
			  );
		    $i++;
		}
	      return $this->aArray;
            }
	  $this->db->free($resql);
	  return array();
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
