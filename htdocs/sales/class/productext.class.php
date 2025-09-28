<?php
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

class Productext extends Product
{
	var $origin;
	var $originid;
	/**
	*  Adjust stock in a warehouse for product
	*
	*  @param  	User	$user           user asking change
	*  @param  	int		$id_entrepot    id of warehouse
	*  @param  	double	$nbpiece        nb of units
	*  @param  	int		$movement       0 = add, 1 = remove
	* 	@param		string	$label			Label of stock movement
	* 	@param		double	$price			Unit price HT of product, used to calculate average weighted price (PMP in french). If 0, average weighted price is not changed.
	*  @param		string	$inventorycode	Inventory code
	* 	@return     int     				<0 if KO, >0 if OK
	*/
	function add_transfer($user, $id_entrepot, $ref, $nbpiece, $movement, $label='', $price=0, $inventorycode='',$fk_type_mov=0,$date='',$balance_peps=0,$balance_ueps=0,$price_peps=0,$price_ueps=0)
	{
		if ($id_entrepot)
		{
			$this->db->begin();
			require_once DOL_DOCUMENT_ROOT .'/almacen/class/stockmouvementtempext.class.php';

			$op[0] = "+".trim($nbpiece);
			$op[1] = "-".trim($nbpiece);
			$movementstock=new Stockmouvementtempext($this->db);
			$result=$movementstock->_create($user,$this->id,$id_entrepot,$ref,$op[$movement],$movement,$price,$label,$inventorycode,$date,'','','',false,$fk_type_mov,$balance_peps,$balance_ueps,$price_peps,$price_ueps);

			if ($result >= 0)
			{
				$this->db->commit();
				return $result;
			}
			else
			{
				$this->error=$movementstock->error;
				$this->errors=$movementstock->errors;

				$this->db->rollback();
				return -1;
			}
		}
		else
			return -1;
	}

	/**
	*  Adjust stock in a warehouse for product
	*
	*  @param  	User	$user           user asking change
	*  @param  	int		$id_entrepot    id of warehouse
	*  @param  	double	$nbpiece        nb of units
	*  @param  	int		$movement       0 = add, 1 = remove
	* 	@param		string	$label			Label of stock movement
	* 	@param		double	$price			Unit price HT of product, used to calculate average weighted price (PMP in french). If 0, average weighted price is not changed.
	*  @param		string	$inventorycode	Inventory code
	* 	@return     int     				<0 if KO, >0 if OK
	*/

	function add_transfer_ok($user, $id_entrepot, $nbpiece, $movement, $label='', $price=0, $idtemp='',$fk_type_mov=0,$datem='')
	{
		if ($id_entrepot)
		{
			$this->db->begin();
			require_once DOL_DOCUMENT_ROOT .'/product/stock/class/mouvementstock.class.php';

			$op[0] = "+".trim($nbpiece);
			$op[1] = "-".trim($nbpiece);
			$movementstock=new MouvementStock($this->db);
			//origin
			if (!empty($this->origin))
			{
				$movementstock->origin->element = $this->origin;
				$movementstock->origin->id = $this->originid;
			}

			$result=$movementstock->_create($user,$this->id,$id_entrepot,$op[$movement],$movement,$price,$label,'',$datem,'','',false);

			if ($result >= 0)
			{
				$this->db->commit();
				return $result;
			}
			else
			{
				$this->error=$movementstock->error;
				$this->errors=$movementstock->errors;

				$this->db->rollback();
				return -1;
			}
		}
	}
}

?>