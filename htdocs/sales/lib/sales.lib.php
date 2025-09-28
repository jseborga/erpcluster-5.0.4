<?php

/**
 * Prepare array with list of tabs
 *
 * @param   Commande	$object		Object related to tabs
 * @return  array				Array of tabs to show
 */
function sales_commande_prepare_head(Commande $object)
{
	global $db, $langs, $conf, $user;
	if (! empty($conf->expedition->enabled)) $langs->load("sendings");
	$langs->load("orders");

	$h = 0;
	$head = array();

	if (! empty($conf->commande->enabled) && $user->rights->commande->lire)
	{
		$head[$h][0] = DOL_URL_ROOT.'/sales/commande/card.php?id='.$object->id;
		$head[$h][1] = $langs->trans("OrderCard");
		$head[$h][2] = 'order';
		$h++;
	}

	if (($conf->expedition_bon->enabled && $user->rights->expedition->lire)
		|| ($conf->livraison_bon->enabled && $user->rights->expedition->livraison->lire))
	{
		$head[$h][0] = DOL_URL_ROOT.'/expedition/shipment.php?id='.$object->id;
		if ($conf->expedition_bon->enabled) $text=$langs->trans("Shipments");
		if ($conf->expedition_bon->enabled && $conf->livraison_bon->enabled) $text.='/';
		if ($conf->livraison_bon->enabled)  $text.=$langs->trans("Receivings");
		$head[$h][1] = $text;
		$head[$h][2] = 'shipping';
		$h++;
	}

	if (! empty($conf->global->MAIN_USE_PREVIEW_TABS))
	{
		$head[$h][0] = DOL_URL_ROOT.'/sales/commande/apercu.php?id='.$object->id;
		$head[$h][1] = $langs->trans("Preview");
		$head[$h][2] = 'preview';
		$h++;
	}

	if (empty($conf->global->MAIN_DISABLE_CONTACTS_TAB))
	{
		$nbContact = count($object->liste_contact(-1,'internal')) + count($object->liste_contact(-1,'external'));
		$head[$h][0] = DOL_URL_ROOT.'/sales/commande/contact.php?id='.$object->id;
		$head[$h][1] = $langs->trans('ContactsAddresses');
		if ($nbContact > 0) $head[$h][1].= ' <span class="badge">'.$nbContact.'</span>';
		$head[$h][2] = 'contact';
		$h++;
	}

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);   												to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'order');

	if (empty($conf->global->MAIN_DISABLE_NOTES_TAB))
	{
		$nbNote = 0;
		if(!empty($object->note_private)) $nbNote++;
		if(!empty($object->note_public)) $nbNote++;
		$head[$h][0] = DOL_URL_ROOT.'/sales/commande/note.php?id='.$object->id;
		$head[$h][1] = $langs->trans('Notes');
		if ($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
		$head[$h][2] = 'note';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->commande->dir_output . "/" . dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
	$nbLinks=Link::count($db, $object->element, $object->id);
	$head[$h][0] = DOL_URL_ROOT.'/sales/commande/document.php?id='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if (($nbFiles+$nbLinks) > 0) $head[$h][1].= ' <span class="badge">'.($nbFiles+$nbLinks).'</span>';
	$head[$h][2] = 'documents';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/sales/commande/info.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'order','remove');

	return $head;
}

/**
 * Prepare array with list of tabs
 *
 * @param   object	$object		Object related to tabs
 * @return  array				Array of tabs to show
 */
function sales_propal_prepare_head($object)
{
	global $db, $langs, $conf, $user;
	$langs->load("propal");
	$langs->load("compta");

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/sales/propal/card.php?id='.$object->id;
	$head[$h][1] = $langs->trans('ProposalCard');
	$head[$h][2] = 'comm';
	$h++;

	if ((empty($conf->commande->enabled) &&	((! empty($conf->expedition_bon->enabled) && $user->rights->expedition->lire)
		|| (! empty($conf->livraison_bon->enabled) && $user->rights->expedition->livraison->lire))))
	{
		$langs->load("sendings");
		$head[$h][0] = DOL_URL_ROOT.'/expedition/propal.php?id='.$object->id;
		if ($conf->expedition_bon->enabled) $text=$langs->trans("Shipment");
		if ($conf->livraison_bon->enabled)  $text.='/'.$langs->trans("Receivings");
		$head[$h][1] = $text;
		$head[$h][2] = 'shipping';
		$h++;
	}
	if (! empty($conf->global->MAIN_USE_PREVIEW_TABS))
	{
		$head[$h][0] = DOL_URL_ROOT.'/sales/propal/apercu.php?id='.$object->id;
		$head[$h][1] = $langs->trans("Preview");
		$head[$h][2] = 'preview';
		$h++;
	}

	if (empty($conf->global->MAIN_DISABLE_CONTACTS_TAB))
	{
		$nbContact = count($object->liste_contact(-1,'internal')) + count($object->liste_contact(-1,'external'));
		$head[$h][0] = DOL_URL_ROOT.'/sales/propal/contact.php?id='.$object->id;
		$head[$h][1] = $langs->trans('ContactsAddresses');
		if ($nbContact > 0) $head[$h][1].= ' <span class="badge">'.$nbContact.'</span>';
		$head[$h][2] = 'contact';
		$h++;
	}

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);   												to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'sales');

	if (empty($conf->global->MAIN_DISABLE_NOTES_TAB))
	{
		$nbNote = 0;
		if(!empty($object->note_private)) $nbNote++;
		if(!empty($object->note_public)) $nbNote++;
		$head[$h][0] = DOL_URL_ROOT.'/sales/propal/note.php?id='.$object->id;
		$head[$h][1] = $langs->trans('Notes');
		if ($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
		$head[$h][2] = 'note';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->propal->dir_output . "/" . dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
	$nbLinks=Link::count($db, $object->element, $object->id);
	$head[$h][0] = DOL_URL_ROOT.'/sales/propal/document.php?id='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if (($nbFiles+$nbLinks) > 0) $head[$h][1].= ' <span class="badge">'.($nbFiles+$nbLinks).'</span>';
	$head[$h][2] = 'document';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/sales/propal/info.php?id='.$object->id;
	$head[$h][1] = $langs->trans('Info');
	$head[$h][2] = 'info';
	$h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'sales','remove');

	return $head;
}

/**
 * Prepare array with list of tabs
 *
 * @param   Contrat	$object		Object related to tabs
 * @return  array				Array of tabs to show
 */
function sales_contract_prepare_head(Contrat $object)
{
	global $db, $langs, $conf;

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/sales/contrat/card.php?id='.$object->id;
	$head[$h][1] = $langs->trans("ContractCard");
	$head[$h][2] = 'card';
	$h++;

	if (empty($conf->global->MAIN_DISABLE_CONTACTS_TAB))
	{
		$nbContact = count($object->liste_contact(-1,'internal')) + count($object->liste_contact(-1,'external'));
		$head[$h][0] = DOL_URL_ROOT.'/sales/contrat/contact.php?id='.$object->id;
		$head[$h][1] = $langs->trans("ContactsAddresses");
		if ($nbContact > 0) $head[$h][1].= ' <span class="badge">'.$nbContact.'</span>';
		$head[$h][2] = 'contact';
		$h++;
	}

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);   												to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'contract');

	if (empty($conf->global->MAIN_DISABLE_NOTES_TAB))
	{
		$nbNote = 0;
		if(!empty($object->note_private)) $nbNote++;
		if(!empty($object->note_public)) $nbNote++;
		$head[$h][0] = DOL_URL_ROOT.'/sales/contrat/note.php?id='.$object->id;
		$head[$h][1] = $langs->trans("Notes");
		if ($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
		$head[$h][2] = 'note';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->contrat->dir_output . "/" . dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
	$nbLinks=Link::count($db, $object->element, $object->id);
	$head[$h][0] = DOL_URL_ROOT.'/sales/contrat/document.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Documents");
	if (($nbFiles+$nbLinks) > 0) $head[$h][1].= ' <span class="badge">'.($nbFiles+$nbLinks).'</span>';
	$head[$h][2] = 'documents';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/sales/contrat/info.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'contract','remove');

	return $head;
}

/**
 * Initialize the array of tabs for customer invoice
 *
 * @param	Facture		$object		Invoice object
 * @return	array					Array of head tabs
 */
function sales_facture_prepare_head($object)
{
	global $db, $langs, $conf;

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture.php?facid='.$object->id;
	$head[$h][1] = $langs->trans('CardBill');
	$head[$h][2] = 'compta';
	$h++;

	if (empty($conf->global->MAIN_DISABLE_CONTACTS_TAB))
	{
		$nbContact = count($object->liste_contact(-1,'internal')) + count($object->liste_contact(-1,'external'));
		$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture/contact.php?facid='.$object->id;
		$head[$h][1] = $langs->trans('ContactsAddresses');
		if ($nbContact > 0) $head[$h][1].= ' <span class="badge">'.$nbContact.'</span>';
		$head[$h][2] = 'contact';
		$h++;
	}

	if (! empty($conf->global->MAIN_USE_PREVIEW_TABS))
	{
		$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture/apercu.php?facid='.$object->id;
		$head[$h][1] = $langs->trans('Preview');
		$head[$h][2] = 'preview';
		$h++;
	}

	//if ($fac->mode_reglement_code == 'PRE')
	if (! empty($conf->prelevement->enabled))
	{
		$nbStandingOrders=0;
		$sql = "SELECT COUNT(pfd.rowid) as nb";
		$sql .= " FROM ".MAIN_DB_PREFIX."prelevement_facture_demande as pfd";
		$sql .= " WHERE pfd.fk_facture = ".$object->id;
		$resql=$db->query($sql);
		if ($resql)
		{
			$obj=$db->fetch_object($resql);
			if ($obj) $nbStandingOrders = $obj->nb;
		}
		else dol_print_error($db);
		$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture/prelevement.php?facid='.$object->id;
		$head[$h][1] = $langs->trans('StandingOrders');
		if ($nbStandingOrders > 0) $head[$h][1].= ' <span class="badge">'.$nbStandingOrders.'</span>';
		$head[$h][2] = 'standingorders';
		$h++;
	}

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);   												to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'invoice');

	if (empty($conf->global->MAIN_DISABLE_NOTES_TAB))
	{
		$nbNote = 0;
		if(!empty($object->note_private)) $nbNote++;
		if(!empty($object->note_public)) $nbNote++;
		$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture/note.php?facid='.$object->id;
		$head[$h][1] = $langs->trans('Notes');
		if ($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
		$head[$h][2] = 'note';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->facture->dir_output . "/" . dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
	$nbLinks=Link::count($db, $object->element, $object->id);
	$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture/document.php?facid='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if (($nbFiles+$nbLinks) > 0) $head[$h][1].= ' <span class="badge">'.($nbFiles+$nbLinks).'</span>';
	$head[$h][2] = 'documents';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture/info.php?facid='.$object->id;
	$head[$h][1] = $langs->trans('Info');
	$head[$h][2] = 'info';
	$h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'invoice','remove');

	return $head;
}

/**
 * Initialize the array of tabs for customer invoice
 *
 * @param	Facture		$object		Invoice object
 * @return	array					Array of head tabs
 */
function sales_facturefiscal_prepare_head($object)
{
	global $db, $langs, $conf;

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture.php?facid='.$object->id;
	$head[$h][1] = $langs->trans('CardBill');
	$head[$h][2] = 'compta';
	$h++;

	if (empty($conf->global->MAIN_DISABLE_CONTACTS_TAB))
	{
		$nbContact = count($object->liste_contact(-1,'internal')) + count($object->liste_contact(-1,'external'));
		$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture/contact.php?facid='.$object->id;
		$head[$h][1] = $langs->trans('ContactsAddresses');
		if ($nbContact > 0) $head[$h][1].= ' <span class="badge">'.$nbContact.'</span>';
		$head[$h][2] = 'contact';
		$h++;
	}

	if (! empty($conf->global->MAIN_USE_PREVIEW_TABS))
	{
		$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture/apercu.php?facid='.$object->id;
		$head[$h][1] = $langs->trans('Preview');
		$head[$h][2] = 'preview';
		$h++;
	}

	//if ($fac->mode_reglement_code == 'PRE')
	if (! empty($conf->prelevement->enabled))
	{
		$nbStandingOrders=0;
		$sql = "SELECT COUNT(pfd.rowid) as nb";
		$sql .= " FROM ".MAIN_DB_PREFIX."prelevement_facture_demande as pfd";
		$sql .= " WHERE pfd.fk_facture = ".$object->id;
		$resql=$db->query($sql);
		if ($resql)
		{
			$obj=$db->fetch_object($resql);
			if ($obj) $nbStandingOrders = $obj->nb;
		}
		else dol_print_error($db);
		$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture/prelevement.php?facid='.$object->id;
		$head[$h][1] = $langs->trans('StandingOrders');
		if ($nbStandingOrders > 0) $head[$h][1].= ' <span class="badge">'.$nbStandingOrders.'</span>';
		$head[$h][2] = 'standingorders';
		$h++;
	}

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);   												to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'invoice');

	if (empty($conf->global->MAIN_DISABLE_NOTES_TAB))
	{
		$nbNote = 0;
		if(!empty($object->note_private)) $nbNote++;
		if(!empty($object->note_public)) $nbNote++;
		$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture/note.php?facid='.$object->id;
		$head[$h][1] = $langs->trans('Notes');
		if ($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
		$head[$h][2] = 'note';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->facture->dir_output . "/" . dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
	$nbLinks=Link::count($db, $object->element, $object->id);
	$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture/document.php?facid='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if (($nbFiles+$nbLinks) > 0) $head[$h][1].= ' <span class="badge">'.($nbFiles+$nbLinks).'</span>';
	$head[$h][2] = 'documents';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/sales/compta/facture/info.php?facid='.$object->id;
	$head[$h][1] = $langs->trans('Info');
	$head[$h][2] = 'info';
	$h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'invoice','remove');

	return $head;
}
function updatetotal_commande($id)
{
	global $db;
	$objecttmp = new OrderLineext($db);
	$objecttmp->get_sum_taxes($id);
	$aData = $objecttmp->aData;

	$objtmp = new Commandeext($db);
	$restmp = $objtmp->fetch($id);
	if ($objtmp->id == $id)
	{
		$objtmp->total_ht 	= $aData['total_ht'];
		$objtmp->tva 		= $aData['total_tva'];
		$objtmp->total_ttc	= $aData['total_ttc'];
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

function update_total_facture($id)
{
	global $db;
	$objecttmp = new FactureLigneext($db);
	$objecttmp->get_sum_taxes($id);
	$aData = $objecttmp->aData;

	$objtmp = new Factureext($db);
	$restmp = $objtmp->fetch($id);
	if ($objtmp->id == $id)
	{
		$objtmp->total_ttc 	= $aData['total_ttc'];
		$objtmp->total_tva 	= $aData['total_tva'];
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
?>