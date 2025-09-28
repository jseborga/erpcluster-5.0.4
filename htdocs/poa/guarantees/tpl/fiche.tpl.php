<?php

$aContrat = getListOfContracts();
$aArray = array();
//contratos ya registrados
$objpcon->getidscontrat();
$aCon = $objpcon->array;
foreach((array) $aContrat AS $j => $dataContrat)
{
	if (!empty($dataContrat->array_options['options_ref_contrato']) && $i == $dataContrat->id)
		$aArray[$dataContrat->id] = $dataContrat->array_options['options_ref_contrato'];
}
asort($aArray);
if ($action == 'createguarantee' && $user->rights->poa->guar->crear)
{
    print_fiche_titre($langs->trans("Newguarantee"));

	print '<form class="form-inline" action="'.DOL_URL_ROOT.'/poa/guarantees/fiche.php?ida='.$ida.'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="lastlink" value="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'">';

	dol_htmloutput_mesg($mesg);

	print '<div class="form-group">';
	// contrat
	print '<label class="sr-only" for="fk_contrat">'.$langs->trans('Contrat').'</label>';
	print $form->selectarray('fk_contrat',$aArray,$idc,(count($aArray)>0?0:1),0,0,'',0,0,0,'','form-control');
	print '</div>';

	//type guarantee
	print '<div class="form-group">';
	print '<label class="sr-only" for="code_guarantee">'.$langs->trans('Guaranteetype').'</label>';
	print select_code_guarantees($object->code_guarantee,'code_guarantee','',1,0);
	'<input id="label" type="text" value="'.$object->label.'" name="label" size="50" maxlength="255">';
	print '</div>';

	//Ref
	print '<div class="form-group">';
	print '<label class="sr-only" for="ref">'.$langs->trans('Ref').'</label>';
	print '<input id="ref" class="form-control" type="text" value="'.$object->ref.'" placeholder="'.$langs->trans('Ref').'" name="ref" size="20" maxlength="30">';
	print '</div>';

	//Issuer
	print '<div class="form-group">';
	print '<label class="sr-only" for="issuer">'.$langs->trans('Issuer').'</label>';
	print '<input id="issuer" class="form-control" type="text" placeholder="'.$langs->trans('Issuer').'" value="'.$object->issuer.'" name="issuer" maxlength="150">';
	print '</div>';

	//concept
	print '<div class="form-group">';
	print '<label class="sr-only" for="concept">'.$langs->trans('Concept').'</label>';
	print '<input id="concept" class="form-control" type="text" placeholder="'.$langs->trans('Concept').'" value="'.$object->concept.'" name="concept" size="50">';
	print '</div>';

	//dateini
	print '<div class="form-group">';
	print '<label class="sr-only" for="di_">'.$langs->trans('Dateini').'</label>';
	$formadd->select_dateadd($object->date_ini,'di_','','','',"date",1,0);
	print '</div>';

	//datefin
	print '<div class="form-group">';
	print '<label class="sr-only" for="df_">'.$langs->trans('Datefin').'</label>';
	$formadd->select_dateadd($object->date_fin,'df_','','','',"date",1,0);
	print '</div>';

	//amount
	print '<div class="form-group">';
	print '<label class="sr-only" for="amount">'.$langs->trans('Amount').'</label>';
	print '<input id="amount" class="form-control" type="number" placeholder="'.$langs->trans('Amount').'" step="any" value="'.$object->amount.'" name="amount" size="15">';
	print '</div>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'">';
	if ($user->rights->poa->prev->leer)
		print '&nbsp;<a class="button" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php?ida='.$ida.'">'.$langs->trans("Return").'</a>';
	else
		print "&&nbsp;<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";
	print '</center>';
	print '</form>';
}
else
{
	if ($id)
	{
		$result = $object->fetch($_GET["id"]);
		if ($result < 0) dol_print_error($db);
		if ($action <> 'edit' && $action <> 're-edit')
		{
			dol_fiche_head($head, 'card', $langs->trans("Guarantee"), 0, 'mant');

			//Confirmation de la validation
			if ($action == 'validate')
			{
				$object->fetch(GETPOST('id'));
				//cambiando a validado
				$object->statut = 1;
				//update
				$object->update($user);
				$action = '';
			}
			if ($action == 'unvalidate')
			{
				$object->fetch(GETPOST('id'));
				//cambiando a validado
				$object->statut = 0;
				//update
				$object->update($user);
				$action = '';
			}

			// Confirm delete third party
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteguarantee"),$langs->trans("Confirmdeleteguarante",$object->ref.' '.$object->issuer),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			dol_htmloutput_mesg($mesg);

			print '<table class="border" width="100%">';
		 	// contrat
			$objcont->fetch($object->fk_contrat);
			print '<tr><td width="12%">'.$langs->trans('Contrat').'</td><td colspan="2">';
			print $objcont->array_options["options_ref_contrato"];
			print '</td></tr>';

			 //type guarantee
			print '<tr><td>'.$langs->trans('Guaranteetype').'</td><td colspan="2">';
			print select_code_guarantees($object->code_guarantee,'code_guarantee','',0,1);
			print '</td></tr>';

		 	//Ref
			print '<tr><td>'.$langs->trans('Ref').'</td><td colspan="2">';
			print $object->ref;
			print '</td></tr>';

		 	//Issuer
			print '<tr><td>'.$langs->trans('Issuer').'</td><td colspan="2">';
			print $object->issuer;
			print '</td></tr>';

		 	//concept
			print '<tr><td>'.$langs->trans('Concept').'</td><td colspan="2">';
			print $object->concept;
			print '</td></tr>';

		 	//dateini
			print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
			print dol_print_date($object->date_ini,'day');
			print '</td></tr>';

		 	//datefin
			print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
			print dol_print_date($object->date_fin,'day');
			print '</td></tr>';

		 	//amount
			print '<tr><td>'.$langs->trans('Amount').'</td><td colspan="2">';
			print price($object->amount);
			print '</td></tr>';

			print '</table>';

			print '</div>';


			/* ************************************************************************** */
			/*                                                                            */
			/* Barre d'action                                                             */
			/*                                                                            */
			/* ************************************************************************** */

			print "<div class=\"tabsAction\">\n";
			if ($user->rights->poa->prev->leer)
				print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php'.(isset($_GET['nopac'])?'?nopac=1&ida='.$ida:'?ida='.$ida).'">'.$langs->trans("Return").'</a>';
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";

			if ($action == '')
			{
				if ($user->rights->poa->guar->crear)
					print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

				if ($user->rights->poa->guar->mod && $object->statut == 0)
					print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if ($user->rights->poa->guar->val)
				{
					if ($object->statut == 0)
						print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Authorize")."</a>";
					else
						print "<a class=\"butAction\" href=\"fiche.php?action=unvalidate&id=".$object->id."\">".$langs->trans("Disavow")."</a>";
				}
				if ($user->rights->poa->guar->del && $object->statut == 1)
					print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
			}
			print "</div>";

		 	//primero los procesos contrato
			$res = $objpcon->fetch_contrat($object->fk_contrat);
			if ($res>0)
			{
				//encabezado
				print_barre_liste($langs->trans("Preventives"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);

				print '<table class="noborder" width="100%">';

				print "<tr class=\"liste_titre\">";
				print_liste_field_titre($langs->trans("Nro."),"", "","","","");
				print_liste_field_titre($langs->trans("Name"),"", "","","","");
				print_liste_field_titre($langs->trans("Respon"),"", "","","","");
				print "</tr>\n";

				$aArray = $objpcon->array;
				foreach ((array) $aArray AS $i => $objdata)
				{
					//comprometidos
					$res1 = $objpcom->fetch_contrat($objdata->id);
					if ($res1 >0)
					{
						//preventivo
						$aComp = $objpcom->array;
						foreach ((array) $aComp AS $j => $objcom)
						{
							$res2 = $objprev->fetch($objcom->fk_poa_prev);
							if ($res2 > 0 && $objprev->id == $objcom->fk_poa_prev)
							{
								print '<tr>';
								print '<td>';
								print '<a href="'.DOL_URL_ROOT.'/poa/execution/fiche.php?id='.$objcom->fk_poa_prev.'">'.img_picto($langs->trans("Preventive"),DOL_URL_ROOT.'/poa/img/prev.png','',1).'&nbsp;'.$objprev->nro_preventive.'</a>';
								print '</td>';
								print '<td>';
								print $objprev->label;
								print '</td>';
								print '<td>';
								$obuser->fetch($objprev->fk_user_create);
								print $obuser->lastname.' '.$obuser->firstname;
								print '</td>';

								print '</tr>';

							}
						}
					}
				}
				print '</table>';
			}
		 	//fin registro preventivos
		}
	 	//Edition fiche
		if (($action == 'edit' || $action == 're-edit') && 1)
		{
			print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

			print '<form action="fiche.php" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';

			print '<table class="border" width="100%">';

			print '<table class="border" width="100%">';

		 // contrat
			print '<tr><td class="fieldrequired">'.$langs->trans('Contrat').'</td><td colspan="2">';
			print $form->selectarray('fk_contrat',$aArray,$object->fk_contrat);
			print '</td></tr>';

		 //type guarantee
			print '<tr><td class="fieldrequired">'.$langs->trans('Guaranteetype').'</td><td colspan="2">';
			print select_code_guarantees($object->code_guarantee,'code_guarantee','',1,0);
			'<input id="label" type="text" value="'.$object->label.'" name="label" size="50" maxlength="255">';
			print '</td></tr>';

		 //Ref
			print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
			print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="20" maxlength="30">';
			print '</td></tr>';

		 //Issuer
			print '<tr><td class="fieldrequired">'.$langs->trans('Issuer').'</td><td colspan="2">';
			print '<input id="issuer" type="text" value="'.$object->issuer.'" name="issuer" size="50" maxlength="150">';
			print '</td></tr>';

		 //concept
			print '<tr><td class="fieldrequired">'.$langs->trans('Concept').'</td><td colspan="2">';
			print '<input id="concept" type="text" value="'.$object->concept.'" name="concept" size="50">';
			print '</td></tr>';

		 //dateini
			print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
			$form->select_date($object->date_ini,'di_','','','',"date",1,1);
			print '</td></tr>';

		 //datefin
			print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
			$form->select_date($object->date_fin,'df_','','','',"date",1,1);
			print '</td></tr>';

		 //amount
			print '<tr><td>'.$langs->trans('Amount').'</td><td colspan="2">';
			print '<input id="amount" type="number" steep="any" value="'.$object->amount.'" name="amount" size="15">';
			print '</td></tr>';

			print '</table>';

			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
			print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

			print '</form>';
		}
	}
}

?>