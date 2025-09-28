<?php
/*
*Proceso de carga a solalmacendet si existe la orden de produccion
*/
if ($conf->fabrication->enabled)
{
	if ($object->fk_fabrication > 0 &&
		$object->statut == 0 )
	{
		$sql = "SELECT cd.rowid, pa.fk_product_son as prowid, p.ref, p.label as produit, p.fk_product_type as type, p.pmp as ppmp, p.price, p.price_ttc,";
		$sql.= " pa.qty_son AS qtyconvert, cd.qty as qty";
		$sql.= " FROM ".MAIN_DB_PREFIX."fabricationdet AS cd ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product_list AS pa ON cd.fk_product = pa.fk_product_father ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON p.rowid = pa.fk_product_son ";

		$sql.= " WHERE ";
		$sql.= " cd.fk_fabrication = '".$object->fk_fabrication."'";
		if (!empty($listId)) $sql .= " AND (p.rowid NOT IN ($listId) OR p.rowid IS NULL)";
		$sql.= $db->order($sortfield,$sortorder);
		dol_syslog('List products sql='.$sql);
		$resql = $db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			$var=True;
			while ($i < $num)
			{
				$objp = $db->fetch_object($resql);


				//verificando si existe el producto para el peido al almacen
				$query = "SELECT sa.rowid AS rowid";
				$query.= " FROM ".MAIN_DB_PREFIX."sol_almacendet AS sa ";
				$query.= " WHERE sa.fk_almacen = '".$object->id."'";
				$query.= " AND sa.fk_product = '".$objp->prowid."'";
				$resultsql = $db->query($query);
				$objres = $db->fetch_object($resultsql);
				$lRegnew = false;
				if (empty($objres->rowid))
				{
					$lRegnew = true;
					$objProduct = new Product($db);
		    		//buscamos el precio unitario del producto
					$objProduct->fetch($objp->prowid);
					$objProduct->load_stock();
					$pmp = 0;
					if ($objProduct->stock_warehouse[$object->fk_entrepot]->pmp)
						$pmp = $objProduct->stock_warehouse[$object->fk_entrepot]->pmp;

					$objSolalmdet = new Solalmacendetext($db);
					$objSolalmdet->fk_almacen = $object->id;
					$objSolalmdet->fk_product = $objp->prowid;
					$objSolalmdet->qty = $objp->qty*$objp->qtyconvert;
					$objSolalmdet->price = $pmp;
					$objSolalmdet->qtylivree = $objp->qty*$objp->qtyconvert;
					$idAlmdet = $objSolalmdet->create($user);

					$objectDetFab->fk_almacendet = $idAlmdet;
					$objectDetFab->fk_fabricationdet = $objp->rowid;
					$objectDetFab->qty=$objp->qty*$objp->qtyconvert;
					$objectDetFab->qtylivree = $objp->qty*$objp->qtyconvert;
					$objectDetFab->price = $pmp;
					$res = $objectDetFab->create($user);
				}
				else
				{
					$objSolalmdet = new Solalmacendetext($db);
					$res = $objSolalmdet->fetch($objres->rowid);
					if ($res > 0 && $objSolalmdet->id = $objres->rowid)
					{
						$idAlmdet = $objSolalmdet->id;
						$objSolalmdet->qty+=$objp->qty*$objp->qtyconvert;
						$objSolalmdet->qtylivree += $objp->qty*$objp->qtyconvert;
						$objSolalmdet->update($user);
					}

					$objectDetFab->fk_almacendet = $idAlmdet;
					$objectDetFab->fk_fabricationdet = $objp->rowid;
					$objectDetFab->qty=$objp->qty*$objp->qtyconvert;
					$objectDetFab->qtylivree = $objp->qty*$objp->qtyconvert;
					$objectDetFab->price = $pmp;
					$res = $objectDetFab->create($user);
				}
				$i++;
			}
			$db->free($resql);
		}
	}
}


?>