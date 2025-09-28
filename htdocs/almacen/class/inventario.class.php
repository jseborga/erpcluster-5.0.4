<?php

class inventario extends MouvementStock
{

	/**
   * Count number of product in stock before a specific date
   *
   * @param 	int			$productidselected		Id of product to count
   * @param 	timestamp	$datebefore				Date limit
   * @return	int			Number
   */
	function calculateBalanceForProductEntrepotBefore($fk_entrepot,$productidselected, $datebefore)
	{
		$nb=0;

		$sql = 'SELECT SUM(value) as nb from '.MAIN_DB_PREFIX.'stock_mouvement';
		$sql.= ' WHERE fk_product = '.$productidselected;
		$sql.= " AND fk_entrepot = ".$fk_entrepot;
		$sql.= " AND datem < '".$this->db->idate($datebefore)."'";

		dol_syslog(get_class($this).__METHOD__.' sql='.$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$obj=$this->db->fetch_object($resql);
			if ($obj) $nb = $obj->nb;
			return (empty($nb)?0:$nb);
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	/**
   *  Create a document onto disk according to template module.
   *
   *  @param      string    $modele     Force model to use ('' to not force)
   *  @param    Translate $outputlangs  Object langs to use for output
   *  @param      int     $hidedetails    Hide details of lines
   *  @param      int     $hidedesc       Hide description
   *  @param      int     $hideref        Hide ref
   *  @return     int                 0 if KO, 1 if OK
   */
	public function generateDocument($modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
	{
		global $conf,$user,$langs;

		$langs->load("almacen@almacen");

		// Positionne le modele sur le nom du modele a utiliser
		if (! dol_strlen($modele))
		{
			if (! empty($conf->global->ALMACEN_ADDON_PDF))
			{
				$modele = $conf->global->ALMACEN_ADDON_PDF;
			}
			else
			{
				$modele = 'inventario';
			}
		}

		$modelpath = "almacen/core/modules/doc/";

		return $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref);
	}
}
?>