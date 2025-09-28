<?php

$deplacement 	= new Requestcashdeplacementext($db);
$deplacementtmp = new Requestcashdeplacementext($db);
$deplacementtemp = new Requestcashdeplacementext($db);
$filterstatic='';
//$filterstatic = " AND t.fk_account_from = ".$object->fk_account;
	//$filterstatic.= " AND t.fk_projet = ".$object->fk_projet;
$filterstatic.= " AND t.fk_request_cash = ".$object->id;
$filterstatic.= " AND t.entity = ".$object->entity;
if ($action == 'listdep')
	//$filterstatic.= " AND t.status >= 1";
	$filterstatic.= " AND t.status = 3";
if ($idd>0)
	$filterstatic.= " AND t.fk_parent = ".$idd;

$res = $deplacement->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterstatic,false);

$lines = $deplacement->lines;

$lUpdatedep = false;
if($user->admin && $conf->global->FININT_UPDATE_DEPLACEMENT)
{
	print '<form name="modadmin" id="modadmin" action="'.$_SERVER['PHP_SELF'].'" method="POST">'."\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="updateparent">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	$lUpdatedep=true;
}

dol_fiche_head();
print '<div style="min-width:450px;overflow-x: auto; white-space: nowrap;">';

print '<table class="noborder centpercent">'."\n";
print '<thead>';
print '<tr class="liste_titre">';

if ($user->admin && $lUpdatedep)
	print_liste_field_titre($langs->trans('Parent'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Surrender'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Typedocument'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Categorie'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
if ($conf->monprojet->enabled)
	print_liste_field_titre($langs->trans('Project').'/'.$langs->trans('Task'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Doc'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
if($conf->browser->layout=='classic')
	print_liste_field_titre($langs->trans('Detail'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'','',$param,'align="right"',$sortfield,$sortorder);
if($conf->browser->layout=='classic')
{
	if ($action == 'listdep')
	{
		print_liste_field_titre($langs->trans('Doc'),'','','',$param,'align="right"');
		print_liste_field_titre($langs->trans('Status'),'','','',$param,'align="right"');
	}
	else
		print_liste_field_titre($langs->trans('Action'),'','','',$param,'align="right"');
}

print '</tr>';
print '</thead>';
print '<tbody>';
$var = true;
foreach ((array) $lines AS $j => $line)
{
	$lFacture = false;
	if ($line->status !=2 ) $lEfeclose = false;
	if ($line->status == 2) $lEfeval = true;
	if ($action == 'mod_dep'&& GETPOST('idrcd') == $line->id)
	{
		$myclass = $line;
		$myclass->amount_ttc = $myclass->amount;
		$fk_projetsel = $myclass->fk_projet_dest+0;
		if ($fk_projetsel>0) $filtertask = " t.fk_projet = ".$fk_projetsel;
		$lModify = true;
	}
	if ($action == 'modifyrefr' && GETPOST('idrcd') == $line->id)
	{
		$lModify = true;
	}

	$deplacementtmp->fetch($line->id);
	$var = !$var;
	if ($line->status == 1 || $line->status == 2 || $line->status == 3)
	{
		if ($lViewdischarg)
		{
			$linkapp = '';
			if ($line->fk_parent_app>0)
			{
				$restmp = $deplacementtemp->fetch($line->fk_parent_app);
				if ($restmp>0)
				{
					$linkapp = $deplacementtemp->ref;
				}
			}
			if ($action == 'formapprecharge' && $line->status ==2)
				print '<tr class="colortext">';
			else
				print "<tr $bc[$var]>";
			if ($user->admin && $lUpdatedep)
			{
				print '<td nowrap>';
				print $form->selectarray('aParent['.$line->id.']',$aTransfer,$deplacementtmp->fk_parent,1);
				print $deplacementtmp->fk_parent.' == '.$deplacementtmp->fk_parent_app.' || '.$line->fk_parent.' '.$line->fk_parent_app;
				
				print '</td>';
			}
			print '<td>';
			print $deplacementtmp->ref;
			print '</td>';
			print '<td>';
			print dol_print_date($line->dateo,'day');
			print '</td>';
			print '<td>';
			print $linkapp;
			print '</td>';
			print '<td>';
			print ($line->type_operation==1?$langs->trans('Invoice'):$langs->trans('Receipt'));
			print '</td>';
			print '<td>';
			$resbcat = $bankcateg->fetch($line->fk_categorie);
			//$categorie->fetch($line->fk_categorie);
			//print $categorie->label;
			if ($resbcat>0)
				print $bankcateg->label;
			print '</td>';
			if ($conf->monprojet->enabled)
			{
				print '<td>';
				if ($line->fk_projet_dest>0)
					$projet->fetch($line->fk_projet_dest);
				if ($line->fk_projet_task_dest>0)
					$task->fetch($line->fk_projet_task_dest);
				print ($line->fk_projet_dest>0?$projet->getNomUrl(1):'').($line->fk_projet->task_dest>0?'/'.$task->getNomUrl(1):'');
				print '</td>';
			}
			print '<td>';
			if ($line->fk_facture_fourn > 0)
			{
				//buscamos la factura
				if ($conf->purchase->enabled)
				{
					require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseur.factureext.class.php';
					$objfacture = new FactureFournisseurext($db);
					$objfacture->fetch($line->fk_facture_fourn);
					print $objfacture->getNomUrladd(1);
				}
				else
				{
					require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
					$objfacture = new FactureFournisseur($db);
					$objfacture->fetch($line->fk_facture_fourn);
					print $objfacture->getNomUrl(1);
				}
			}
			else
				print $line->nro_chq;
			print '</td>';
			if($conf->browser->layout=='classic')
			{
				print '<td>';
				print $line->detail;
				print '</td>';
			}
			print '<td align="right">';
			print price($line->amount);
			$aSummary[$line->fk_projet_dest][$line->type_operation]+=$line->amount;
			print '</td>';
			if($conf->browser->layout=='classic')
			{
				if ($action == 'listdep')
				{

				print '<td align="right">';
				if ($object->status == 4)
				{				
					if ($user->rights->finint->desc->val)
					{
						$img = 'switch_off';
						$img = 'statut8';
						$text = $langs->trans('Validate');
						if ($line->status == 2)
						{
							$text = $langs->trans('Invalidate');
							$img = 'switch_on';
							$img = 'statut4';
						}
						$lViewapp = true;
						if ($conf->fiscal->enabled)
						{
							require_once DOL_DOCUMENT_ROOT.'/fiscal/lib/fiscal.lib.php';
							if (!empty($line->code_facture))
							{
								$objcfact = fetch_type_facture(0,$line->code_facture);
								$objcfact->nit_required;
								if ($objcfact->nit_required && (empty($line->fk_facture_fourn) || is_null($line->fk_facture_fourn)))
								{
									$lFacture = true;
									if (empty($line->fourn_nit) || is_null($line->fourn_nit)) $lFacture = false;
									if (empty($line->fourn_soc) || is_null($line->fourn_soc)) $lFacture = false;
									if (empty($line->fourn_facture) || is_null($line->fourn_facture)) $lFacture = false;
									if (empty($line->fourn_numaut) || is_null($line->fourn_numaut)) $lFacture = false;
									$lViewapp = false;
								}
							}
						}
						if ($line->fk_facture_fourn)
						{
							require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseur.factureext.class.php';
							$facturefourn = new FactureFournisseurext($db);
							$res = $facturefourn->fetch($line->fk_facture_fourn);
							if ($res> 0)
							{
								$facturefourn->ref_ext = $line->fourn_facture;
									//verificamos los montos
								$classerr='';
								if ($facturefourn->total_ttc != $line->amount)
								{
									$classerr = 'errmark';
									$lViewapp = false;
								}
								print '&nbsp;'.$facturefourn->getNomUrladd(0,'',0,1,$classerr);
							}
						}
					}
				}
				else
				{
				}
				print '</td>';
				print '<td align="right">';
				print $deplacementtmp->getLibStatutext(3);
				print '</td>';
				}
			}

			print '</tr>';
		}
		$sumaactual+=$line->amount;
		if ($line->status == 2) $sumarecharge+= $line->amount;
	}
	$sumadep -= $line->amount;
	$sumagas += $line->amount*-1;
}
if ($lViewdischarg)
{
	print '</tbody>';
	//armamos el total
	print '<tr class="liste_total">';
	print '<td colspan="'.($conf->browser->layout=='classic'?'8':'3').'">'.$langs->trans('Total').'</td>';
	print '<td align="right">'.price($sumaactual).'</td>';
	print '</tr>';
}
print '</table>';
print '</div>';
dol_fiche_end();

if ($user->admin && $lUpdatedep)
{
	print '<div class="center"><input type="submit" class="button" value="'.$langs->trans("Update").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';
	print '</form>';
}

?>