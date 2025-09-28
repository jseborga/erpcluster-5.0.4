<?php

/*
 * @param	string	$modulepart			Module of document ('module', 'module_user_temp', 'module_user' or 'module_temp')
 * @param	string	$original_file		Relative path with filename
 * @param	string	$entity				Restrict onto entity
 * @param  	User	$fuser				User object (forced)
 * @param	string	$refname			Ref of object to check permission for external users (autodetect if not provided)
 * @return	mixed						Array with access information : accessallowed & sqlprotectagainstexternals & original_file (as full path name)
 */
function mdol_check_secure_access_document($modulepart,$original_file,$entity,$fuser='',$refname='',$direcc='')
{

	//if (! is_object($fuser)) $fuser=$user;

	if (empty($modulepart)) return 'ErrorBadParameter';
	if (empty($entity)) $entity=0;
	//dol_syslog('modulepart='.$modulepart.' original_file='.$original_file);
	// We define $accessallowed and $sqlprotectagainstexternals
	$accessallowed=0;
	$sqlprotectagainstexternals='';
	$ret=array();

	// find the subdirectory name as the reference
	if (empty($refname)) $refname=basename(dirname($original_file)."/");

	// Wrapping for some images
	if ($modulepart == 'companylogo')
	{
		$accessallowed=1;
		$original_file=$conf->mycompany->dir_output.'/logos/'.$original_file;
	}
	// Wrapping for users photos
	elseif ($modulepart == 'userphoto')
	{
		$accessallowed=1;
		$original_file=$conf->user->dir_output.'/'.$original_file;
	}
	// Wrapping for members photos
	elseif ($modulepart == 'memberphoto')
	{
		$accessallowed=1;
		$original_file=$conf->adherent->dir_output.'/'.$original_file;
	}
	// Wrapping pour les apercu factures
	elseif ($modulepart == 'apercufacture')
	{
		if ($fuser->rights->facture->lire) $accessallowed=1;
		$original_file=$conf->facture->dir_output.'/'.$original_file;
	}
	// Wrapping pour les apercu propal
	elseif ($modulepart == 'apercupropal')
	{
		if ($fuser->rights->propale->lire) $accessallowed=1;
		$original_file=$conf->propal->dir_output.'/'.$original_file;
	}
	// Wrapping pour les apercu commande
	elseif ($modulepart == 'apercucommande')
	{
		if ($fuser->rights->commande->lire) $accessallowed=1;
		$original_file=$conf->commande->dir_output.'/'.$original_file;
	}
	// Wrapping pour les apercu intervention
	elseif ($modulepart == 'apercufichinter')
	{
		if ($fuser->rights->ficheinter->lire) $accessallowed=1;
		$original_file=$conf->ficheinter->dir_output.'/'.$original_file;
	}
	// Wrapping pour les images des stats propales
	elseif ($modulepart == 'propalstats')
	{
		if ($fuser->rights->propale->lire) $accessallowed=1;
		$original_file=$conf->propal->dir_temp.'/'.$original_file;
	}
	// Wrapping pour les images des stats commandes
	elseif ($modulepart == 'orderstats')
	{
		if ($fuser->rights->commande->lire) $accessallowed=1;
		$original_file=$conf->commande->dir_temp.'/'.$original_file;
	}
	elseif ($modulepart == 'orderstatssupplier')
	{
		if ($fuser->rights->fournisseur->commande->lire) $accessallowed=1;
		$original_file=$conf->fournisseur->dir_output.'/commande/temp/'.$original_file;
	}
	// Wrapping pour les images des stats factures
	elseif ($modulepart == 'billstats')
	{
		if ($fuser->rights->facture->lire) $accessallowed=1;
		$original_file=$conf->facture->dir_temp.'/'.$original_file;
	}
	elseif ($modulepart == 'billstatssupplier')
	{
		if ($fuser->rights->fournisseur->facture->lire) $accessallowed=1;
		$original_file=$conf->fournisseur->dir_output.'/facture/temp/'.$original_file;
	}
	// Wrapping pour les images des stats expeditions
	elseif ($modulepart == 'expeditionstats')
	{
		if ($fuser->rights->expedition->lire) $accessallowed=1;
		$original_file=$conf->expedition->dir_temp.'/'.$original_file;
	}
	// Wrapping pour les images des stats expeditions
	elseif ($modulepart == 'tripsexpensesstats')
	{
		if ($fuser->rights->deplacement->lire) $accessallowed=1;
		$original_file=$conf->deplacement->dir_temp.'/'.$original_file;
	}
	// Wrapping pour les images des stats expeditions
	elseif ($modulepart == 'memberstats')
	{
		if ($fuser->rights->adherent->lire) $accessallowed=1;
		$original_file=$conf->adherent->dir_temp.'/'.$original_file;
	}
	// Wrapping pour les images des stats produits
	elseif (preg_match('/^productstats_/i',$modulepart))
	{
		if ($fuser->rights->produit->lire || $fuser->rights->service->lire) $accessallowed=1;
		$original_file=(!empty($conf->product->multidir_temp[$entity])?$conf->product->multidir_temp[$entity]:$conf->service->multidir_temp[$entity]).'/'.$original_file;
	}
	// Wrapping for products or services
	elseif ($modulepart == 'tax')
	{
		if ($fuser->rights->tax->charges->lire) $accessallowed=1;
		$original_file=$conf->tax->dir_output.'/'.$original_file;
	}
	// Wrapping for products or services
	elseif ($modulepart == 'actions')
	{
		if ($fuser->rights->agenda->myactions->read) $accessallowed=1;
		$original_file=$conf->agenda->dir_output.'/'.$original_file;
	}
	// Wrapping for categories
	elseif ($modulepart == 'category')
	{
		if ($fuser->rights->categorie->lire) $accessallowed=1;
		$original_file=$conf->categorie->multidir_output[$entity].'/'.$original_file;
	}
	// Wrapping pour les prelevements
	elseif ($modulepart == 'prelevement')
	{
		if ($fuser->rights->prelevement->bons->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->prelevement->dir_output.'/'.$original_file;
	}
	// Wrapping pour les graph energie
	elseif ($modulepart == 'graph_stock')
	{
		$accessallowed=1;
		$original_file=$conf->stock->dir_temp.'/'.$original_file;
	}
	// Wrapping pour les graph fournisseurs
	elseif ($modulepart == 'graph_fourn')
	{
		$accessallowed=1;
		$original_file=$conf->fournisseur->dir_temp.'/'.$original_file;
	}
	// Wrapping pour les graph des produits
	elseif ($modulepart == 'graph_product')
	{
		$accessallowed=1;
		$original_file=$conf->product->multidir_temp[$entity].'/'.$original_file;
	}
	// Wrapping pour les code barre
	elseif ($modulepart == 'barcode')
	{
		$accessallowed=1;
		// If viewimage is called for barcode, we try to output an image on the fly,
		// with not build of file on disk.
		//$original_file=$conf->barcode->dir_temp.'/'.$original_file;
		$original_file='';
	}
	// Wrapping pour les icones de background des mailings
	elseif ($modulepart == 'iconmailing')
	{
		$accessallowed=1;
		$original_file=$conf->mailing->dir_temp.'/'.$original_file;
	}
	// Wrapping pour les icones de background des mailings
	elseif ($modulepart == 'scanner_user_temp')
	{
		$accessallowed=1;
		$original_file=$conf->scanner->dir_temp.'/'.$fuser->id.'/'.$original_file;
	}
	// Wrapping pour les images fckeditor
	elseif ($modulepart == 'fckeditor')
	{
		$accessallowed=1;
		$original_file=$conf->fckeditor->dir_output.'/'.$original_file;
	}

	// Wrapping for third parties
	else if ($modulepart == 'company' || $modulepart == 'societe')
	{
		if ($fuser->rights->societe->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->societe->multidir_output[$entity].'/'.$original_file;
		$sqlprotectagainstexternals = "SELECT rowid as fk_soc FROM ".MAIN_DB_PREFIX."societe WHERE rowid='".$db->escape($refname)."' AND entity IN (".getEntity('societe', 1).")";
	}

	// Wrapping for contact
	else if ($modulepart == 'contact')
	{
		if ($fuser->rights->societe->lire)
		{
			$accessallowed=1;
		}
		$original_file=$conf->societe->multidir_output[$entity].'/contact/'.$original_file;
	}

	// Wrapping for invoices
	else if ($modulepart == 'facture' || $modulepart == 'invoice')
	{
		if ($fuser->rights->facture->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->facture->dir_output.'/'.$original_file;
		$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."facture WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
	}

	else if ($modulepart == 'unpaid')
	{
		if ($fuser->rights->facture->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->facture->dir_output.'/unpaid/temp/'.$original_file;
	}

	// Wrapping pour les fiches intervention
	else if ($modulepart == 'ficheinter')
	{
		if ($fuser->rights->ficheinter->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->ficheinter->dir_output.'/'.$original_file;
		$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."fichinter WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
	}

	// Wrapping pour les deplacements et notes de frais
	else if ($modulepart == 'deplacement')
	{
		if ($fuser->rights->deplacement->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->deplacement->dir_output.'/'.$original_file;
		//$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."fichinter WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
	}
	// Wrapping pour les propales
	else if ($modulepart == 'propal')
	{
		if ($fuser->rights->propale->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}

		$original_file=$conf->propal->dir_output.'/'.$original_file;
		$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."propal WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
	}

	// Wrapping pour les commandes
	else if ($modulepart == 'commande' || $modulepart == 'order')
	{
		if ($fuser->rights->commande->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->commande->dir_output.'/'.$original_file;
		$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."commande WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
	}

	// Wrapping pour les projets
	else if ($modulepart == 'project')
	{
		if ($fuser->rights->projet->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->projet->dir_output.'/'.$original_file;
		$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."projet WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
	}
	else if ($modulepart == 'project_task')
	{
		$accessallowed=1;
		$original_file=$direcc.'/'.$original_file;
		//$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."projet WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
		$sqlprotectagainstexternals = '';
	}
	else if ($modulepart == 'monprojet')
	{
		$accessallowed=1;
		$original_file=$direcc.'/'.$original_file;

		//$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."projet WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
		$sqlprotectagainstexternals = '';
	}
	// Wrapping for interventions
	else if ($modulepart == 'fichinter')
	{
		if ($fuser->rights->ficheinter->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->ficheinter->dir_output.'/'.$original_file;
		$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."fichinter WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
	}

	// Wrapping pour les commandes fournisseurs
	else if ($modulepart == 'commande_fournisseur' || $modulepart == 'order_supplier')
	{
		if ($fuser->rights->fournisseur->commande->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->fournisseur->commande->dir_output.'/'.$original_file;
		$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."commande_fournisseur WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
	}

	// Wrapping pour les factures fournisseurs
	else if ($modulepart == 'facture_fournisseur' || $modulepart == 'invoice_supplier')
	{
		if ($fuser->rights->fournisseur->facture->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->fournisseur->facture->dir_output.'/'.$original_file;
		$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."facture_fourn WHERE facnumber='".$db->escape($refname)."' AND entity=".$conf->entity;
	}

	// Wrapping pour les rapport de paiements
	else if ($modulepart == 'facture_paiement')
	{
		if ($fuser->rights->facture->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		if ($fuser->societe_id > 0) $original_file=$conf->facture->dir_output.'/payments/private/'.$fuser->id.'/'.$original_file;
		else $original_file=$conf->facture->dir_output.'/payments/'.$original_file;
	}

	// Wrapping for accounting exports
	else if ($modulepart == 'export_compta')
	{
		if ($fuser->rights->accounting->ventilation->dispatch || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->accounting->dir_output.'/'.$original_file;
	}

	// Wrapping pour les expedition
	else if ($modulepart == 'expedition')
	{
		if ($fuser->rights->expedition->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->expedition->dir_output."/sending/".$original_file;
	}

	// Wrapping pour les bons de livraison
	else if ($modulepart == 'livraison')
	{
		if ($fuser->rights->expedition->livraison->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->expedition->dir_output."/receipt/".$original_file;
	}

	// Wrapping pour les actions
	else if ($modulepart == 'actions')
	{
		if ($fuser->rights->agenda->myactions->read || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->agenda->dir_output.'/'.$original_file;
	}

	// Wrapping pour les actions
	else if ($modulepart == 'actionsreport')
	{
		if ($fuser->rights->agenda->allactions->read || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file = $conf->agenda->dir_temp."/".$original_file;
	}

	// Wrapping pour les produits et services
	else if ($modulepart == 'product' || $modulepart == 'produit' || $modulepart == 'service')
	{
		if (($fuser->rights->produit->lire || $fuser->rights->service->lire) || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		if (! empty($conf->product->enabled)) $original_file=$conf->product->multidir_output[$entity].'/'.$original_file;
		elseif (! empty($conf->service->enabled)) $original_file=$conf->service->multidir_output[$entity].'/'.$original_file;
	}

	// Wrapping pour les contrats
	else if ($modulepart == 'contract')
	{
		if ($fuser->rights->contrat->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->contrat->dir_output.'/'.$original_file;
	}

	// Wrapping pour les dons
	else if ($modulepart == 'donation')
	{
		if ($fuser->rights->don->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->don->dir_output.'/'.$original_file;
	}

	// Wrapping pour les remises de cheques
	else if ($modulepart == 'remisecheque')
	{
		if ($fuser->rights->banque->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}

		$original_file=$conf->banque->dir_output.'/bordereau/'.$original_file;		// original_file should contains relative path so include the get_exdir result
	}

	// Wrapping for bank
	else if ($modulepart == 'bank')
	{
		if ($fuser->rights->banque->lire)
		{
			$accessallowed=1;
		}
		$original_file=$conf->bank->dir_output.'/'.$original_file;
	}

	// Wrapping for export module
	else if ($modulepart == 'export')
	{
		// Aucun test necessaire car on force le rep de download sur
		// le rep export qui est propre a l'utilisateur
		$accessallowed=1;
		$original_file=$conf->export->dir_temp.'/'.$fuser->id.'/'.$original_file;
	}

	// Wrapping for import module
	else if ($modulepart == 'import')
	{
		// Aucun test necessaire car on force le rep de download sur
		// le rep export qui est propre a l'utilisateur
		$accessallowed=1;
		$original_file=$conf->import->dir_temp.'/'.$original_file;
	}

	// Wrapping pour l'editeur wysiwyg
	else if ($modulepart == 'editor')
	{
		// Aucun test necessaire car on force le rep de download sur
		// le rep export qui est propre a l'utilisateur
		$accessallowed=1;
		$original_file=$conf->fckeditor->dir_output.'/'.$original_file;
	}

	// Wrapping pour les backups
	else if ($modulepart == 'systemtools')
	{
		if ($fuser->admin)
		{
			$accessallowed=1;
		}
		$original_file=$conf->admin->dir_output.'/'.$original_file;
	}

	// Wrapping for upload file test
	else if ($modulepart == 'admin_temp')
	{
		if ($fuser->admin)
			$accessallowed=1;
		$original_file=$conf->admin->dir_temp.'/'.$original_file;
	}

	// Wrapping pour BitTorrent
	else if ($modulepart == 'bittorrent')
	{
		$accessallowed=1;
		$dir='files';
		if (dol_mimetype($original_file) == 'application/x-bittorrent') $dir='torrents';
		$original_file=$conf->bittorrent->dir_output.'/'.$dir.'/'.$original_file;
	}

	// Wrapping pour Foundation module
	else if ($modulepart == 'member')
	{
		if ($fuser->rights->adherent->lire || preg_match('/^specimen/i',$original_file))
		{
			$accessallowed=1;
		}
		$original_file=$conf->adherent->dir_output.'/'.$original_file;
	}

	// Wrapping for Scanner
	else if ($modulepart == 'scanner_user_temp')
	{
		$accessallowed=1;
		$original_file=$conf->scanner->dir_temp.'/'.$fuser->id.'/'.$original_file;
	}

    // GENERIC Wrapping
    // If modulepart=module_user_temp	Allows any module to open a file if file is in directory called DOL_DATA_ROOT/modulepart/temp/iduser
    // If modulepart=module_temp		Allows any module to open a file if file is in directory called DOL_DATA_ROOT/modulepart/temp
    // If modulepart=module_user		Allows any module to open a file if file is in directory called DOL_DATA_ROOT/modulepart/iduser
    // If modulepart=module				Allows any module to open a file if file is in directory called DOL_DATA_ROOT/modulepart
    else
	{
		// Define $accessallowed
		if (preg_match('/^([a-z]+)_user_temp$/i',$modulepart,$reg))
		{
			if ($fuser->rights->{$reg[1]}->lire || $fuser->rights->{$reg[1]}->read || ($fuser->rights->{$reg[1]}->download)) $accessallowed=1;
			$original_file=$conf->{$reg[1]}->dir_temp.'/'.$fuser->id.'/'.$original_file;
		}
		else if (preg_match('/^([a-z]+)_temp$/i',$modulepart,$reg))
		{
			if ($fuser->rights->{$reg[1]}->lire || $fuser->rights->{$reg[1]}->read || ($fuser->rights->{$reg[1]}->download)) $accessallowed=1;
			$original_file=$conf->{$reg[1]}->dir_temp.'/'.$original_file;
		}
		else if (preg_match('/^([a-z]+)_user$/i',$modulepart,$reg))
		{
			if ($fuser->rights->{$reg[1]}->lire || $fuser->rights->{$reg[1]}->read || ($fuser->rights->{$reg[1]}->download)) $accessallowed=1;
			$original_file=$conf->{$reg[1]}->dir_output.'/'.$fuser->id.'/'.$original_file;
		}
		else
		{
			if (empty($conf->$modulepart->dir_output))	// modulepart not supported
			{
				//dol_print_error('','Error call dol_check_secure_access_document with not supported value for modulepart parameter ('.$modulepart.')');
				echo '<br>Error, modulepart vacio';
				exit;
			}

			$perm=GETPOST('perm');
			$subperm=GETPOST('subperm');
			if ($perm || $subperm)
			{
				if (($perm && ! $subperm && $fuser->rights->$modulepart->$perm) || ($perm && $subperm && $fuser->rights->$modulepart->$perm->$subperm)) $accessallowed=1;
				$original_file=$conf->$modulepart->dir_output.'/'.$original_file;
			}
			else
			{
				if ($fuser->rights->$modulepart->lire || $fuser->rights->$modulepart->read) $accessallowed=1;
				$original_file=$conf->$modulepart->dir_output.'/'.$original_file;
			}
		}
		if (preg_match('/^specimen/i',$original_file))	$accessallowed=1;    // If link to a specimen
		if ($fuser->admin) $accessallowed=1;    // If user is admin

		// For modules who wants to manage different levels of permissions for documents
		$subPermCategoryConstName = strtoupper($modulepart).'_SUBPERMCATEGORY_FOR_DOCUMENTS';
		if (! empty($conf->global->$subPermCategoryConstName))
		{
			$subPermCategory = $conf->global->$subPermCategoryConstName;
			if (! empty($subPermCategory) && (($fuser->rights->$modulepart->$subPermCategory->lire) || ($fuser->rights->$modulepart->$subPermCategory->read) || ($fuser->rights->$modulepart->$subPermCategory->download)))
			{
				$accessallowed=1;
			}
		}

		// Define $sqlprotectagainstexternals for modules who want to protect access using a SQL query.
		$sqlProtectConstName = strtoupper($modulepart).'_SQLPROTECTAGAINSTEXTERNALS_FOR_DOCUMENTS';
		if (! empty($conf->global->$sqlProtectConstName))	// If module want to define its own $sqlprotectagainstexternals
		{
			// Example: mymodule__SQLPROTECTAGAINSTEXTERNALS_FOR_DOCUMENTS = "SELECT fk_soc FROM ".MAIN_DB_PREFIX.$modulepart." WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
			eval('$sqlprotectagainstexternals = "'.$conf->global->$sqlProtectConstName.'";');
		}
	}

	$ret = array(
		'accessallowed' => $accessallowed,
		'sqlprotectagainstexternals'=>$sqlprotectagainstexternals,
		'original_file'=>$original_file
	);

	return $ret;
}
?>