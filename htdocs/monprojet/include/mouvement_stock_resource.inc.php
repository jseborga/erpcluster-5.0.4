<?php 
//procedimiento de verificación para registro en la tabla stock_mouvement
//
$fk_entrepot = 0;
if ($conf->almacen->enabled)
{
	if ($projectadd->fk_entrepot <=0)
	{
		require_once DOL_DOCUMENT_ROOT.'/almacen/local/class/entrepotrelationext.class.php';
		$objrel = new Entrepotrelationext($db);
		$res = $objrel->fetchAll('','',0,0,array(1=>1),'AND'," AND fk_projet = ".$projectstatic->id,true); 
		$fk_entrepot=$objrel->id;
		if (!$fk_entrepot)
		{
			$error++;
			setEventMessages($langs->trans('Error, no esta definido un almacen para el proyecto'),null,'errors');
			exit;
		}
	}
	else $fk_entrepot = $projectadd->fk_entrepot;

}
else
{
	setEventMessages($langs->trans('Error, no esta activado modulo de almacen'),null,'errors');
	exit;
}

//recuperamos el id del registro del material en objectptr
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';

//buscamos el precio unitario del producto
$product->fetch($objectptr->fk_product);
$product->load_stock();
$pmp = 0;
if ($product->stock_warehouse[$fk_entrepot]->pmp)
	$pmp = $product->stock_warehouse[$fk_entrepot]->pmp;
else
	$pmp = $product->pmp;

$qtyt = $objectptr->quant * -1;

$type = 1;
$label = $langs->trans("Outputforuseofresources").' '.$langs->trans('Task')." ".$object->ref;
$objMouvement = new MouvementStock($db);
$objMouvement->origin->element = ($objectptr->element?$objectptr->element:'projettaskresource');
$objMouvement->origin->id = $objectptr->id;

$result = $objMouvement->_create($user,$objectptr->fk_product,$fk_entrepot,$qtyt,$type,$pmp,$label);
$fk_stock_mouvement = $objMouvement->id;
if ($result == -1 || $result == 0)
{
	$error++;
	setEventMessages($objMouvement->error,$objMouvement->errors,'errors');
}



