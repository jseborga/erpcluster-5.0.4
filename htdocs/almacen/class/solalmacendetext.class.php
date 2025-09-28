<?php
require_once DOL_DOCUMENT_ROOT.'/almacen/class/solalmacendet.class.php';

class Solalmacendetext extends Solalmacendet
{
	/**
	 *  Add an order line into database (linked to product/service or not)
	 *
	 *  @param      int             $commandeid         Id of line
	 *  @param      string          $desc               Description of line
	 *  @param      double          $pu_ht              Unit price (without tax)
	 *  @param      double          $qty                Quantite
	 *  @param      double          $txtva              Taux de tva force, sinon -1
	 *  @param      double          $txlocaltax1        Local tax 1 rate
	 *  @param      double          $txlocaltax2        Local tax 2 rate
	 *  @param      int             $fk_product         Id du produit/service predefini
	 *  @param      double          $remise_percent     Pourcentage de remise de la ligne
	 *  @param      int             $info_bits          Bits de type de lignes
	 *  @param      int             $fk_remise_except   Id remise
	 *  @param      string          $price_base_type    HT or TTC
	 *  @param      double          $pu_ttc             Prix unitaire TTC
	 *  @param      timestamp       $date_start         Start date of the line - Added by Matelli (See http://matelli.fr/showcases/patchs-dolibarr/add-dates-in-order-lines.html)
	 *  @param      timestamp       $date_end           End date of the line - Added by Matelli (See http://matelli.fr/showcases/patchs-dolibarr/add-dates-in-order-lines.html)
	 *  @param      int             $type               Type of line (0=product, 1=service)
	 *  @param      int             $rang               Position of line
	 *  @param      int             $special_code       Special code (also used by externals modules!)
	 *  @param      int             $fk_parent_line     Parent line
	 *  @param      int             $fk_fournprice      Id supplier price
	 *  @param      int             $pa_ht              Buying price (without tax)
	 *  @param      string          $label              Label
	 *  @return     int                                 >0 if OK, <0 if KO
	 *
	 *  @see        add_product
	 *
	 *  Les parametres sont deja cense etre juste et avec valeurs finales a l'appel
	 *  de cette methode. Aussi, pour le taux tva, il doit deja avoir ete defini
	 *  par l'appelant par la methode get_default_tva(societe_vendeuse,societe_acheteuse,produit)
	 *  et le desc doit deja avoir la bonne valeur (a l'appelant de gerer le multilangue)
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
				$this->id=$commandeid;  
				// TODO A virer
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
	 *  @param  int     $fk_solalmacen llave padre
	 *  @return array           <0 if KO, len(aArray) >0 if OK
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

	public function select_line($array,$htmlname='fk_id',$showempty=1)
	{
		$out = '';
		$nb = 0;
		$aSort = array_sort($array,'ref',SORT_ASC);
		if ($showempty)
			$out.='<option value="0"> </option>';
		foreach ($aSort AS $i => $data)
		{
			$out.= '<option value="'.$data['idline'].'" >'.$data['ref'].' '.$data['label'].'</option>';
			$nb++;
		}
		return array($nb,$out);
	}

}
?>