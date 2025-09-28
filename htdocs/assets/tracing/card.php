<?php

// Get parameters
//$id			= GETPOST('id','int');
//$action		= GETPOST('action','alpha');

$extrafields = new ExtraFields($db);


// fetch optionals attributes and labels
//$extralabels = $extrafields->fetch_name_optionals_label($objectAsstra->table_element);

// Load object
/*Esto hacia el error de cargar no se que cosa*/
//include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
//$hookmanager->initHooks(array('assetstracing'));



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/






/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

//llxHeader('','MyPageName','');




// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


// Part to create
if ($action == 'create')
{
	
	$res = $objCassetsbeen->fetchAll("ASC","label",0,0,array(1=>1),"AND",$filtro);
	if($res){
		$opciones = "";
		foreach ($objCassetsbeen->lines as $key => $value) {
			$opciones .="<option value=".$value->id." >".$value->label."</option>";
		}
	}

	
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="idCard" value="'.$id.'">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fielddater").'</td><td><input class="flat" type="text" name="fk_asset" value="'.GETPOST('fk_asset').'"></td></tr>';
	
	print '<tr><td class="fieldrequired">'.$langs->trans('Fielddater').'</td><td colspan="2">';
	$form->select_date($date_ini,'date_ini','','','',"crea_seat",1,1);
	print '</td></tr>';


	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_resp").'</td><td>';
	print $form->select_member(($fk_user_resp?$fk_user_resp:GETPOST('fk_user_resp')), 'fk_user_resp', '', 1, 0, 0, array(), 0,'autofocus');
	//print '<input class="flat" type="text" size="36" name="Fieldfk_user_resp" value="'.$Fieldfk_user_resp.'">';
	print '</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbeen").'</td><td><input class="flat" type="text" name="been" value="'.GETPOST('been').'"></td></tr>'; 
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbeen").'</td><td><select name="been">'.$opciones.'</select></td></tr>'; 
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><textarea class="flat"  name="description"  rows="7" cols="30" value="'.GETPOST('description').'"></textarea></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';

	print '</table>'."\n";
	dol_fiche_end();
	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';
	print '</form>';
}


// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	
	
	$res = $objectAsstra->fetch($id);
	$resBeen = $objCassetsbeen->fetchAll("","",0,0,array(1=>1),"AND",$filtro);
	if($resBeen){
		$opciones = "";
		foreach ($objCassetsbeen->lines as $key => $value) {
			
			if($value->id == $objectAsstra->been){
				$opciones .="<option value=".$value->id." selected>".$value->label."</option>";
			}else{
				$opciones .="<option value=".$value->id." >".$value->label."</option>";
			}
		}
	}
	
	print load_fiche_titre($langs->trans("MyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="idCard" value="'.$idCard.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '<input class="flat" type="hidden" name="fk_asset" value="'.$objectAsstra->fk_asset.'">';
	print '<tr><td class="fieldrequired">'.$langs->trans('Fielddater').'</td><td colspan="2">';
	$form->select_date($objectAsstra->dater,'date_ini','','','',"crea_seat",1,1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_resp").'</td><td>';
	print $form->select_member($objectAsstra->fk_user_resp, 'fk_user_resp', '', 1, 0, 0, array(), 0,'autofocus');
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbeen").'</td><td><select name="been">'.$opciones.'</select></td></tr>'; 
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbeen").'</td><td><input class="flat" type="text" name="been" value="'.$objectAsstra->been.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><textarea class="flat"  name="description"  rows="7" cols="30" value="'.$objectAsstra->description.'">'.$objectAsstra->description.'</textarea></td></tr>';
	print '<input class="flat" type="hidden" name="fk_user_create" value="'.$objectAsstra->fk_user_create.'">';
	print '<input class="flat" type="hidden" name="fk_user_mod" value="'.$objectAsstra->fk_user_mod.'">';
	print '<input class="flat" type="hidden" name="status" value="'.$objectAsstra->status.'">';
	
	print '<input class="flat" type="hidden" name="rowid" value="'.$objectAsstra->id.'">';
	print '<input class="flat" type="hidden" name="datec" value="'.$objectAsstra->datec.'">';
	print '<input class="flat" type="hidden" name="datem" value="'.$objectAsstra->datem.'">';


	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}


// Part to show record
if ($objectAsstra->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	
	$res = $objectAsstra->fetch_optionals($objectAsstra->id, $extralabels);


	print load_fiche_titre($langs->trans("MyModule"));

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $objectAsstra->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objectAsstra->label.'</td></tr>';
	// 
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_asset").'</td><td>$objectAsstra->fk_asset</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_resp").'</td><td>$objectAsstra->fk_user_resp</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbeen").'</td><td>$objectAsstra->been</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td>$objectAsstra->description</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>$objectAsstra->fk_user_create</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>$objectAsstra->fk_user_mod</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td>$objectAsstra->status</td></tr>';

	print '</table>';

	dol_fiche_end();

	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$objectAsstra,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->assets->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$objectAsstra->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->assets->delete)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$objectAsstra->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";
}
// End of page

