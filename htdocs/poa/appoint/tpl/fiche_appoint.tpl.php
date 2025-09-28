<?php

//desgination
$form=new Form($db);
$display ='none';
if (isset($modal) && $modal == 'fichedesign')
{
	print '<script type="text/javascript">
	$(window).load(function(){
		$("#designew").modal("show");
	});
</script>';
}

print '<div id="'.$tagid.'" class="modal" tabindex="-1" role="dialog" style="display: '.$display.'; margin-top:0px;" data-width="760" aria-hidden="false">';

print '<div class="poa-modal">';
print '<div class="modal">';
print '<div class="modal-dialog modal-lg">';
print '<div class="modal-content">';

print '<div class="modal-header" style="background:#fff; color:#000; !important">';
print '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
print '<h4 class="modal-title">'.$langs->trans("Designations").' '.$objsoc->nom.' '.$objcont->array_options["options_ref_contrato"].'</h4>';
print '</div>'; //modal-header

print '<div class="modal-body" style="background:#fff; color:#000; !important">';

if ($action == 'create' && $user->rights->poa->appoint->crear )
{
	$idc = GETPOST('idc','int');
	//print_fiche_titre($langs->trans("Newappoint"));
	print '<div class="content">';

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="ida" value="'.$ida.'">';
	print '<input type="hidden" name="fk_contrat" value="'.(!empty($idc)?$idc:$objcont->id).'">';
	print '<input type="hidden" name="modal" value="fichedesign">';

	dol_htmloutput_mesg($mesg);

	//type appoint
	print '<div class="form-group">';
	print '<label class="col-sm-3 control-label" for="socid">'.$langs->trans('Appointtype').'</label>';
	print '<div class="col-xs-8">';
	print select_code_appoint($object->code_appoint,'code_appoint','',1,0);
	print '</div>';
	print '</div>';
	//user
	print '<div class="form-group">';
	print '<label class="col-sm-3 control-label" for="socid">'.$langs->trans('User').'</label>';
	print '<div class="col-xs-8">';
	print $form->select_dolusers((empty($object->fk_user)?$user->id:$object->fk_user),'fk_user',1,$exclude,0,'','',$object->entity);
	print '</div>';
	print '</div>';
	//user replace
	print '<div class="form-group">';
	print '<label class="col-sm-3 control-label" for="socid">'.$langs->trans('Replacea').'</label>';
	print '<div class="col-xs-8">';
	print $form->select_dolusers($object->fk_user_replace,'fk_user_replace',1,$exclude,0,'','',$object->entity);
	print '</div>';
	print '</div>';
	//dateappoint
	print '<div class="form-group">';
	print '<label class="col-sm-3 control-label" for="socid">'.$langs->trans('Date').'</label>';
	print '<div class="col-xs-8">';

	print '<div class="well well-sm">';
	print '<div class="input-group date" id="divMiCalendario">';
	print '<input type="text" name="di_" id="txtFecha" class="form-control"  readonly/>';
	print '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>';
	print '</span>';
	print '</div>';
	print '</div>';
	print '</div>';
	print '</div>';

	print '<div class="modal-footer">';
	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
	print '</div>';
	print '</form>';
	print '</div>';

}
else
{
	if ($idapp)
	{
		$resapp = $objapp->fetch($idapp);
		if ($resapp < 0) dol_print_error($db);

		// Affichage fiche
		if ($action <> 'edit' && $action <> 're-edit')
		{
			//$head = fabrication_prepare_head($object);

			//dol_fiche_head($head, 'card', $langs->trans("Appoints"), 0, 'mant');


			dol_htmloutput_mesg($mesg);

			print '<dl class="dl-horizontal">';

		 	//type appoint
			print '<dt>'.$langs->trans('Appointtype').'</dt><dd>';
			print select_code_appoint($objapp->code_appoint,'code_appoint','',0,1);
			print '</dd>';

		 	//user
			$res = $objuser->fetch($objapp->fk_user);
			print '<dt>'.$langs->trans('User').'</dt><dd>';
			if ($res > 0 && $objuser->id == $objapp->fk_user)
				print $objuser->lastname.' '.$objuser->firstname;
			else
				print '&nbsp;';
			print '</dd>';

		 //user replace
			$res = $objuser->fetch($objapp->fk_user_replace);
			print '<dt>'.$langs->trans('Replacea').'</dt><dd>';
			if ($res > 0 && $objuser->id == $objapp->fk_user_replace)
				print $objuser->lastname.' '.$objuser->firstname;
			else
				print '&nbsp;';
			print '</dd>';

		 //dateappoint
			print '<dt>'.$langs->trans('Date').'</dt><dd>';
			print dol_print_date($objapp->date_appoint,'day');
			print '</dd>';


			print '</dl>';

			//print '</div>';

			// ********************************
			// Barre d'action
			// ********************************

			print '<div class="tabsAction">';

			if ($user->rights->poa->prev->leer)
				print '<a class="btn btn-default btn-flat margin" href="'.$_SERVER['PHP_SELF'].(isset($_GET['nopac'])?'?nopac=1&ida='.$ida:'?ida='.$ida).'">'.$langs->trans("Return").'</a>';
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";

			if ($action == '')
			{

				if ($user->rights->poa->guar->val)
				{
					if ($objapp->statut == 0)
						print '<a class="btn btn-success btn-flat margin" href="'.$_SERVER['PHP_SELF'].'?modal=fichedesign&action=validate&ida='.$ida.'&idr='.$objapp->id.'">'.$langs->trans("Validate")."</a>";
					else
						print '<a class="btn btn-success btn-flat margin" href="'.$_SERVER['PHP_SELF'].'?action=unvalidate&id='.$object->id.'&idpro='.$idpro.'">'.$langs->trans("Disavow")."</a>";
				}
				if ($user->rights->poa->guar->del && $objapp->statut == 1)
					print '<a class="btn btn-danger btn-flat margin" href="'.$_SERVER['PHP_SELF'].'?modal=fichedesign&action=confirm_delete&ida='.$ida.'&idr='.$objapp->id.'">'.$langs->trans("Delete")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
			}
			print "</div>";
			//dol_fiche_end();
				//fin registro preventivos
		}
			// Edition fiche
		if (($action == 'edit' || $action == 're-edit') && 1)
		{
			//print_fiche_titre($langs->trans("Appointedit"), $mesg);
			print '<div class="content">';
			print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="ida" value="'.$ida.'">';
			print '<input type="hidden" name="idapp" value="'.$objapp->id.'">';
			print '<input type="hidden" name="fk_contrat" value="'.$objapp->fk_contrat.'">';


			//type appoint
			print '<div class="form-group">';
			print '<label class="col-sm-3 control-label" for="socid">'.$langs->trans('Appointtype').'</label>';
			print '<div class="col-xs-8">';
			print select_code_appoint($objapp->code_appoint,'code_appoint','',1,0);
			print '</div>';
			print '</div>';
			//user
			print '<div class="form-group">';
			print '<label class="col-sm-3 control-label" for="socid">'.$langs->trans('User').'</label>';
			print '<div class="col-xs-8">';
			print $form->select_dolusers((empty($objapp->fk_user)?$user->id:$objapp->fk_user),'fk_user',1,$exclude,0,'','',$object->entity);
			print '</div>';
			print '</div>';
			//user replace
			print '<div class="form-group">';
			print '<label class="col-sm-3 control-label" for="socid">'.$langs->trans('Replacea').'</label>';
			print '<div class="col-xs-8">';
			print $form->select_dolusers($objapp->fk_user_replace,'fk_user_replace',1,$exclude,0,'','',$object->entity);
			print '</div>';
			print '</div>';
			//dateappoint
			print '<div class="form-group">';
			print '<label class="col-sm-3 control-label" for="socid">'.$langs->trans('Date').'</label>';
			print '<div class="col-xs-8">';

			print '<div class="well well-sm">';
			print '<div class="input-group date" id="divMiCalendario">';
			print '<input type="text" name="di_" id="txtFecha" class="form-control" value="'.$objapp->date_appoint.'" readonly/>';
			print '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>';
			print '</span>';
			print '</div>';
			print '</div>';
			print '</div>';
			print '</div>';
			/*
			print '<table class="border" width="100%">';

		 	// contrat
			print '<tr><td class="fieldrequired">'.$langs->trans('Contrat').'</td><td colspan="2">';
			print $form->selectarray('fk_contrat',$aArray,$objapp->fk_contrat);
			print '</td></tr>';

		 	//type appoint
			print '<tr><td class="fieldrequired">'.$langs->trans('Appointtype').'</td><td colspan="2">';
			print select_code_appoint($objapp->code_appoint,'code_appoint','',1,0);
			print '</td></tr>';

		 	//user
			print '<tr><td class="fieldrequired">'.$langs->trans('User').'</td><td colspan="2">';
			print $form->select_dolusers((empty($objapp->fk_user)?$user->id:$object->fk_user),'fk_user',1,$exclude,0,'','',$objapp->entity);
			print '</td></tr>';

		 	//user replace
			print '<tr><td>'.$langs->trans('Replacea').'</td><td colspan="2">';
			print $form->select_dolusers($objapp->fk_user_replace,'fk_user_replace',1,$exclude,0,'','',$object->entity);
			print '</td></tr>';

		 	//dateappoint
			print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="2">';
			$form->select_date($objapp->date_appoint,'di_','','','',"date",1,1);
			print '</td></tr>';

			print '</table>';
			*/
			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
			print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

			print '</form>';
			print '</div>';

		}
	}
}

print '</div>'; //modal-body
print '</div>'; //modal-content
print '</div>'; //modal-dialog
print '</div>'; //modal
print '</div>'; //poa-modal
print '</div>'; //modal tagid

?>