<?php
  //user
$objectuser->fetch_active($obj->id);
if ($objectuser->id > 0 && $obj->id == $objectuser->fk_poa_poa)
{
	$objuser->fetch($objectuser->fk_user);
	$newNombre = $objuser->login;
	if ($filtromenu['f1']==True)
		$aHtml[$i]['user'] = $newNombre;
	if ($filtromenu['f1'])
		print '<td '.$newClase.'"><a href="fiche_user.php?idp='.$obj->id.'&id='.$objectuser->id.'" title="'.$nombre.'">'.$newNombre.'</a></td>';
}
else
{
	if ($filtromenu['f1'])
	{
		print '<td '.$newClase.'">';
		if ($user->rights->poa->poa->crear)
			print '<a href="fiche_user.php?idp='.$obj->id.'&action=create">'.img_picto($langs->trans('Create'),'edit_add').'</a>';
		else
			print '&nbsp;';
		print '</td>';

	}
}
//seguimiento
if ($filtromenu['f1']==true)
{
	if ($numCol[321])
		print '<td '.$newClase.'">&nbsp;</td>';
	if ($numCol[322])
		print '<td '.$newClase.'">&nbsp;</td>';
	if ($numCol[323])
		print '<td '.$newClase.'">&nbsp;</td>';
}

//deberia ingresar instruccion
//deberia ingresar pac
$newClase = $newClaseor;

//action
if ($filtromenu['f1']==True)
{
	print '<td '.$newClase.'">';
	//if ($user->admin || ($obj->statut == 0 && $user->rights->poa->poa->crear))
	//	print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->id.'&action=edit&dol_hide_leftmenu=1">'.img_picto($langs->trans('Edit'),'edit').'</a>';

	//print '&nbsp;&nbsp;';
	//crear una nueva actividad
	if ($user->admin || ($obj->statut == 1 && $user->rights->poa->act->crear))
	{
		//print '<a href="'.DOL_URL_ROOT.'/poa/activity/fiche.php'.'?fk_poa='.$obj->id.'&action=create&dol_hide_leftmenu=1">'.img_picto($langs->trans('Createactivity'),DOL_URL_ROOT.'/poa/img/a','',1).'</a>';
		print '<a href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php'.'?fk_poa='.$obj->id.'&action=create&modal=ficheactivity">'.img_picto($langs->trans('Createactivity'),DOL_URL_ROOT.'/poa/img/a','',1).'</a>';
	}
	print '</td>';

}
?>