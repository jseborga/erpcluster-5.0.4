<?php

			   //inicio agregar precio al producto prooveedor
$id_fourn=$object->socid;

$ref_fourn=dol_now();
if (empty($ref_fourn)) $ref_fourn=GETPOST("search_ref_fourn");
$quantity=1;
$remise_percent=0;
$npr = preg_match('/\*/', $_POST['tva_tx']) ? 1 : 0 ;
$tva_tx = str_replace('*','', GETPOST('tva_tx','alpha'));
$tva_tx = price2num($tva_tx);

if ($tva_tx == '')
{
	$error++;
	setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("VATRateForSupplierProduct")), 'errors');
}
if (empty($quantity))
{
	$error++;
	setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("Qty")), 'errors');
}
if (empty($ref_fourn))
{
	$error++;
	setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("RefSupplier")), 'errors');
}
if ($id_fourn <= 0)
{
	$error++;
	setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("Supplier")), 'errors');
}
$product = new ProductFournisseur($db);
$retarray = $product->list_product_fournisseur_price($idprod, '', '');
			//verificamos si tiene precios el proveedor
$lProduct = true;

foreach ((array) $retarray AS $k => $array)
{
	if ($array->fourn_id == $object->socid)
	{
		$idprodfournprice = $array->product_fourn_price_id;
		$fourn_ref = $array->fourn_ref;
		$lProduct = false;
	}
}
$result=$product->fetch($idprod);
if ($result <= 0)
{
	$error++;
	setEventMessage($product->error, 'errors');
}
if (! $error)
{
	if ($lProduct)
	{
		$db->begin();

		if (! $error)
		{
			$ret=$product->add_fournisseur($user, $id_fourn, $ref_fourn, $quantity);
			$idprodfournprice = $product->product_fourn_price_id;
			$fourn_ref = $product->fourn_ref;
			$_POST['idprodfournprice'] = $idprodfournprice;
				   // This insert record with no value for price. Values are update later with update_buyprice
			if ($ret == -3)
			{
				$error++;

				$product->fetch($product->product_id_already_linked);
				$productLink = $product->getNomUrl(1,'supplier');

				setEventMessage($langs->trans("ReferenceSupplierIsAlreadyAssociatedWithAProduct",$productLink), 'errors');
			}
			else if ($ret < 0)
			{
				$error++;
				setEventMessage($product->error, 'errors');
			}
		}
		if (! $error)
		{
			$supplier=new Fournisseur($db);
			$result=$supplier->fetch($id_fourn);
			if (isset($_POST['ref_fourn_price_id']))
			{
				$product->fetch_product_fournisseur_price($_POST['ref_fourn_price_id']);
				$ret=$product->update_buyprice($quantity, $ttc, $user, $price_base_type, $supplier, $_POST["oselDispo"], $ref_fourn, $tva_tx, $_POST["charges"], $remise_percent, $npr);
			}
			if ($ret < 0)
			{
				$error++;
				setEventMessage($product->error, 'errors');
			}
		}

		if (! $error)
		{
			$db->commit();
				//$action='';
		}
		else
		{
			$db->rollback();
		}
	}
}

?>