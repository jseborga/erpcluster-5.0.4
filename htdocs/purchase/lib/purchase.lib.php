<?php

/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @return  array				Array of tabs to show
 */
function purchase_request_prepare_head($object)
{
	global $langs, $conf;
	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/purchase/request/card.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Purchaserequest");
	$head[$h][2] = 'card';
	$h++;

		// Show more tabs from modules
		// Entries must be declared in modules descriptor with line
		// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
		// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'purchase_request');

	if ($object->status_process >= 1 && $conf->global->PURCHASE_INCLUDE_SUPPLIERPROPOSAL)
	{
		$head[$h][0] = DOL_URL_ROOT.'/purchase/request/supplier.php?id='.$object->id;
		$head[$h][1] = $langs->trans("Supplierproposal");
		$head[$h][2] = 'supplier';
		$h++;
	}


	return $head;
}

/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @return  array				Array of tabs to show
 */
function purchase_prepare_head($object)
{
	global $langs, $conf;
	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/purchase/commande/card.php?id='.$object->id;
	$head[$h][1] = $langs->trans("OrderCard");
	$head[$h][2] = 'card';
	$h++;
	if ($conf->global->PURCHASE_ADD_DETAIL_CONTRAT)
	{
		$head[$h][0] = DOL_URL_ROOT.'/purchase/commande/cardext.php?id='.$object->id;
		$head[$h][1] = $langs->trans("Contract details");
		$head[$h][2] = 'cardext';
		$h++;
	}

	if ($conf->stock->enabled && $conf->global->STOCK_CALCULATE_ON_SUPPLIER_DISPATCH_ORDER)
	//if (! empty($conf->stock->enabled))
	{
		$langs->load("stocks");
		$head[$h][0] = DOL_URL_ROOT.'/purchase/commande/dispatch.php?id='.$object->id;
		$head[$h][1] = $langs->trans("OrderDispatch");
		$head[$h][2] = 'dispatch';
		$h++;
	}

	if (empty($conf->global->MAIN_DISABLE_CONTACTS_TAB))
	{
		$head[$h][0] = DOL_URL_ROOT.'/purchase/commande/contact.php?id='.$object->id;
		$head[$h][1] = $langs->trans('ContactsAddresses');
		$head[$h][2] = 'contact';
		$h++;
	}
	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'purchase_order');

	if (empty($conf->global->MAIN_DISABLE_NOTES_TAB))
	{
		$nbNote = 0;
		if(!empty($object->note_private)) $nbNote++;
		if(!empty($object->note_public)) $nbNote++;
		$head[$h][0] = DOL_URL_ROOT.'/purchase/commande/note.php?id='.$object->id;
		$head[$h][1] = $langs->trans("Notes");
		if ($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
		$head[$h][2] = 'note';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	$upload_dir = $conf->fournisseur->dir_output . "/commande/" . dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
	$head[$h][0] = DOL_URL_ROOT.'/purchase/commande/document.php?id='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if($nbFiles > 0) $head[$h][1].= ' <span class="badge">'.$nbFiles.'</span>';
	$head[$h][2] = 'documents';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/purchase/commande/history.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$h++;

	return $head;
}


/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @return  array				Array of tabs to show
 */
function purchase_facturefourn_prepare_head($object)
{
	global $db, $langs, $conf;

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/purchase/facture/card.php?facid='.$object->id;
	$head[$h][1] = $langs->trans('CardBill');
	$head[$h][2] = 'card';
	$h++;

	if (empty($conf->global->MAIN_DISABLE_CONTACTS_TAB))
	{
		$nbContact = count($object->liste_contact(-1,'internal')) + count($object->liste_contact(-1,'external'));
		$head[$h][0] = DOL_URL_ROOT.'/purchase/facture/contact.php?facid='.$object->id;
		$head[$h][1] = $langs->trans('ContactsAddresses');
		if ($nbContact > 0) $head[$h][1].= ' <span class="badge">'.$nbContact.'</span>';
		$head[$h][2] = 'contact';
		$h++;
	}

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname);   												to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'purchase_invoice');

	if (empty($conf->global->MAIN_DISABLE_NOTES_TAB))
	{
		$nbNote = 0;
		if(!empty($object->note_private)) $nbNote++;
		if(!empty($object->note_public)) $nbNote++;
		$head[$h][0] = DOL_URL_ROOT.'/purchase/facture/note.php?facid='.$object->id;
		$head[$h][1] = $langs->trans('Notes');
		if ($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
		$head[$h][2] = 'note';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->fournisseur->facture->dir_output.'/'.get_exdir($object->id,2,0,0,$object,'invoice_supplier').$object->ref;
	$nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
	$nbLinks=Link::count($db, $object->element, $object->id);
	$head[$h][0] = DOL_URL_ROOT.'/purchase/facture/document.php?facid='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if (($nbFiles+$nbLinks) > 0) $head[$h][1].= ' <span class="badge">'.($nbFiles+$nbLinks).'</span>';
	$head[$h][2] = 'documents';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/purchase/facture/info.php?facid='.$object->id;
	$head[$h][1] = $langs->trans('Info');
	$head[$h][2] = 'info';
	$h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'supplier_invoice','remove');

	return $head;
}


/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @return  array				Array of tabs to show
 */
function purchase_ordersupplier_prepare_head($object)
{
	global $db, $langs, $conf;
	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/purchase/commande/card.php?id='.$object->id;
	$head[$h][1] = $langs->trans("OrderCard");
	$head[$h][2] = 'card';
	$h++;

	if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_SUPPLIER_DISPATCH_ORDER))
	{
		$langs->load("stocks");
		$head[$h][0] = DOL_URL_ROOT.'/purchase/commande/dispatch.php?id='.$object->id;
		$head[$h][1] = $langs->trans("OrderDispatch");
		$head[$h][2] = 'dispatch';
		$h++;
	}

	if (empty($conf->global->MAIN_DISABLE_CONTACTS_TAB))
	{
		$nbContact = count($object->liste_contact(-1,'internal')) + count($object->liste_contact(-1,'external'));
		$head[$h][0] = DOL_URL_ROOT.'/purchase/commande/contact.php?id='.$object->id;
		$head[$h][1] = $langs->trans('ContactsAddresses');
		if ($nbContact > 0) $head[$h][1].= ' <span class="badge">'.$nbContact.'</span>';
		$head[$h][2] = 'contact';
		$h++;
	}

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'supplier_order');

	if (empty($conf->global->MAIN_DISABLE_NOTES_TAB))
	{
		$nbNote = 0;
		if(!empty($object->note_private)) $nbNote++;
		if(!empty($object->note_public)) $nbNote++;
		$head[$h][0] = DOL_URL_ROOT.'/purchase/commande/note.php?id='.$object->id;
		$head[$h][1] = $langs->trans("Notes");
		if ($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
		$head[$h][2] = 'note';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->fournisseur->dir_output . "/commande/" . dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
	$nbLinks=Link::count($db, $object->element, $object->id);
	$head[$h][0] = DOL_URL_ROOT.'/purchase/commande/document.php?id='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if (($nbFiles+$nbLinks) > 0) $head[$h][1].= ' <span class="badge">'.($nbFiles+$nbLinks).'</span>';
	$head[$h][2] = 'documents';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/purchase/commande/info.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$h++;
	complete_head_from_modules($conf,$langs,$object,$head,$h,'supplier_order', 'remove');
	return $head;
}

function update_total_supplier($id)
{
	global $db;
	$objecttmp = new SupplierProposalLineext($db);
	$objecttmp->get_sum_taxes($id);
	$aData = $objecttmp->aData;
	$objtmp = new SupplierProposalext($db);
	$restmp = $objtmp->fetch($id);
	if ($objtmp->id == $id)
	{
		$objtmp->total_ttc 	= $aData['total_ttc'];
		$objtmp->tva 	= $aData['total_tva'];
		$objtmp->total 		= $aData['total_ht'];
		$objtmp->total_ht	= $aData['total_ht'];
		$objtmp->localtax1 	= $aData['total_localtax1'];
		$objtmp->localtax2 	= $aData['total_localtax2'];
		$objtmp->multicurrency_total_ht 	= $aData['multicurrency_total_ht'];
		$objtmp->multicurrency_total_tva	= $aData['multicurrency_total_tva'];
		$objtmp->multicurrency_total_ttc	= $aData['multicurrency_total_ttc'];
		$restot = $objtmp->update_total();
		if ($restot<=0)
		{
			$error++;
			setEventMessages($objtmp->error,$objtmp->errors,'errors');
			$action = '';
		}
	}
	else
	{
		$error++;
		setEventMessages($objtmp->error,$objtmp->errors,'errors');
		$action = '';
	}
	return $error;
}

function update_total_commande($id)
{
	global $db;
	$objecttmp = new CommandeFournisseurLigneext($db);
	$objecttmp->get_sum_taxes($id);
	$aData = $objecttmp->aData;
	$objtmp = new FournisseurCommandeext($db);
	$restmp = $objtmp->fetch($id);
	if ($objtmp->id == $id)
	{
		$objtmp->total_ttc 	= $aData['total_ttc']+0;
		$objtmp->tva 	= $aData['total_tva']+0;
		$objtmp->total 		= $aData['total_ht']+0;
		$objtmp->total_ht	= $aData['total_ht']+0;
		$objtmp->localtax1 	= $aData['total_localtax1']+0;
		$objtmp->localtax2 	= $aData['total_localtax2']+0;
		$objtmp->multicurrency_total_ht 	= $aData['multicurrency_total_ht']+0;
		$objtmp->multicurrency_total_tva	= $aData['multicurrency_total_tva']+0;
		$objtmp->multicurrency_total_ttc	= $aData['multicurrency_total_ttc']+0;
		$restot = $objtmp->update_total();
		if ($restot<=0)
		{
			$error++;
			setEventMessages($objtmp->error,$objtmp->errors,'errors');
			$action = '';
		}
	}
	else
	{
		$error++;
		setEventMessages($objtmp->error,$objtmp->errors,'errors');
		$action = '';
	}
	return $error;
}

function verify_year()
{
	global $conf,$db;
	if ($conf->global->PURCHASE_INTEGRATED_POA)
	{
		$aDate = dol_getdate(dol_now());
		$_SESSION['period_year'] = $aDate['year'];
	}
}

function getElementElement($element,$id,$type='target')
{
	global $db, $conf, $user;
	$sql = "SELECT e.rowid, e.fk_source, e.sourcetype, e.fk_target, e.targettype";
	$sql.= " FROM ".MAIN_DB_PREFIX."element_element AS e ";
	if ($type == 'target') $sql.= " WHERE e.targettype = '".trim($element)."' AND e.fk_target = ".$id;
	if ($type == 'source') $sql.= " WHERE e.sourcetype = '".trim($element)."' AND e.fk_source = ".$id;

	$resql=$db->query($sql);
	$data = array();
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num>0)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);

				$data[$i]['id']    = $obj->rowid;
				$data[$i]['fk_source'] = $obj->fk_source;
				$data[$i]['sourcetype'] = $obj->sourcetype;
				$data[$i]['fk_target'] = $obj->fk_target;
				$data[$i]['targettype'] = $obj->targettype;
				$i++;
			}
		}
	}
	return $data;
}
?>