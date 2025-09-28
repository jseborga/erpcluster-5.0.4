<?php

print_barre_liste($langs->trans("Assignedassets"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);

if ($object->status == 0 && $userWriter)
{
	print '<form name="fo1" method="POST" id="fo1" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="addassign">';
	print '<input type="hidden" name="id" value="'.$id.'">';
}
elseif ($action == 'liberate' && $object->status == 3 && $userWriter)
{
	print '<form name="fo1" method="POST" id="fo1" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="free">';
	print '<input type="hidden" name="id" value="'.$id.'">';
}

print '<table class="noborder centPercent" >';

print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Code"),"", "","","","");
if ($conf->browser->layout != 'phone')
	print_liste_field_titre($langs->trans("Group"),"", "","","","");
print_liste_field_titre($langs->trans("Description"),"", "","","","");
if ($conf->browser->layout != 'phone')
	print_liste_field_titre($langs->trans("Number plaque"),"", "","","","");
print_liste_field_titre($langs->trans('Lastlocation'),"","","","","");
//if ($object->status == 0)
//	print_liste_field_titre($langs->trans("Action"),"", "","","","");
if ($object->status == 3)
	print_liste_field_titre($langs->trans("In use"),"", "","","","");
if ($object->status == 3)
	print_liste_field_titre($langs->trans("Statut"),"", "","","","");
else
	print_liste_field_titre($langs->trans("Action"),"", "","","","");
print "</tr>\n";
$var=True;

if ($object->status == 0  && $userWriter)
{
	$filter = '';
	$status = 9;
	$finished = 2;
	//echo '<hr>type'.$object->type_assignment.' '.$object->fk_user_from;
	if ($object->type_assignment == 1)
	{
		$filter = " fk_user = ".$object->fk_user;
		$status = 2;
		$finished = $object->fk_user_from;
	}
	if ($object->type_assignment == 0)
	{
    	//registro nuevo
		print "<tr $bc[$var]>";
		print '<td nowrap colspan="3">';

		if ($fk_equipment) $selected = $fk_equipment;
		//print $objass->select_assets((!empty($objass->fk_asset)?$objass->fk_asset:(isset($_GET['fk_equipment'])?$_GET['fk_equipment']:'')),'fk_asset','enabled',0,1,'','',$aExclude,$aInclude,1);
		//print $form->select_asset((GETPOST('fk_asset')?GETPOST('fk_asset'):$objass->fk_asset), 'fk_asset', '', 0, 1, 2, '', 1, array(),0,'','');
		print $form->select_asset($selected, $htmlname='fk_asset', '', 0, 0, $status, $finished, '', 1, array(),0,'',$filter);
		print '&nbsp;'.'<a href="'.DOL_URL_ROOT.'/assets/assets/liste.php?idot='.$id.'&amp;ssearch=3">'.img_picto($langs->trans('Search'),'search').'</a>';

		print '</td>';
		print '<td>';
		print '&nbsp;';
		print '</td>';
		print '<td>';
		print '&nbsp;';
		print '</td>';
		print '<td align="center">';
		print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/assets/img/save.png" width="14" height="14">';
		print '</td>';
		print "</tr>\n";
	}
}
if ((count($objassigdact->array)>0))
{
	if ($object->status == 2 ||($object->status == 3 && $action == 'liberate'))
		print '<input type="hidden"  name="quantassets" value="'.count($objassigdact->array).'">';

	foreach ((array) $objassigdact->array AS $i => $obj)
	{
		$var=!$var;
		if ($obj->status == 1 && $obj->active == 1) $lLibre = true;
		//recuperando assets
		$objass = new Assetsext($db);
		$objassigndet->status = $obj->status;
		if ($objass->fetch($obj->fk_asset)>0)
		{
			print "<tr $bc[$var]>";
			print '<td nowrap>';
			print $objass->getNomUrl(1);
			print '</td>';
	    	//group
			if ($conf->browser->layout != 'phone')
			{
				$resgr = $objgroup->fetch(0,$objass->type_group);
				print '<td>';
				if ($resgr>0)
					print $objgroup->getNomUrl();
				else
					print '';
				//print select_type_group($objass->type_group,'type_group','',0,1,'code');
				print '</td>';
			}
	    	//description
			print '<td>';
			print $objass->descrip;
			print '</td>';
	    	//number plaque
			if ($conf->browser->layout != 'phone')
			{
				print '<td>';
				print $objass->number_plaque;
				print '</td>';
			}
			//location
			//status=1 ultima ubicacion aprobada
			$aLocation = $objass->fetch_location(1,1);
			print '<td>';
			if ($aLocation['link'])
				print $aLocation['link'].' '.$aLocation['label'];

			elseif ($aLocation['ref'])
				print $aLocation['ref'].' '.$aLocation['label'];
			else
				print $langs->trans('Nomovement');
			print '</td>';

			if ($object->status == 3)
			{
				print '<td>';
				if ($obj->status == 1)
				{
					if ($obj->active == 1 && (( $user->rights->assets->alloc->lall && $user->rights->assets->alloc->lib && count($objassigdact->array) > 0)  || ($user->rights->assets->alloc->lib && $user->id == $object->fk_user_from && count($objassigdact->array) > 0)))
						print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$obj->id.'&action=uliberate">'.($obj->active?img_picto('','switch_on'):img_picto('','switch_off')).'</a>';
					else
						print ($obj->active?img_picto('','switch_on'):img_picto('','switch_off'));
				}
				print '</td>';
			}
			if ($object->status == 0 && $userWriter)
			{
				//action
				print '<td align="center">';
				print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&amp;idr='.$obj->id.'&amp;action=delasset">'.img_picto($langs->trans('Delete'),'delete').'</a>';
				print '</td>';
			}
			elseif ($object->status == 2 && $userWriter)
			{
				//action
				if (empty($action))
					$check = 'checked';
				else
				{
					$check = '';
					$selasset = GETPOST('selasset');
					if ($selasset[$obj->id]) $check = 'checked';
				}
				print '<td align="center">';
				print '<input type="checkbox" '.$check.' name="selasset['.$obj->id.']">';
				print '</td>';
			}
			elseif ($object->status == 3 && $userWriter && ($action == 'liberate' || $action == 'free' ))
			{
				//action
				if (empty($action))
					$check = 'checked';
				else
				{
					$check = '';
					$selasset = GETPOST('selasset');
					if ($selasset[$obj->id]) $check = 'checked';
				}
				if ($obj->active == 1 && $obj->status == 1)
				{
					print '<td align="center">';
					print '<input type="checkbox" '.$check.' name="selasset['.$obj->id.']">';
					print '</td>';
				}
			}
			elseif ($object->status == 3)
			{
				//$objassigdact->id = $obj->id;
				//$objassigdact->status = $obj->status;
				print '<td align="left">';
				//print $objassigdact->getLibStatut(1);
				print $objassigndet->getLibStatut(4);
				print '</td>';
			}
			print "</tr>\n";
			$i++;
		}
	}
}
print '</table>';
if ($object->status == 0 && $userWriter) print '</form>';
elseif ($action=='liberate' && $object->status == 3 && $userWriter && $lLibre)
{
	print '<center>';
	print '<input type="submit" class="butAction" name="leavefree" value="'.$langs->trans("Leave free").'">';
	print '&nbsp;';
	print '<input type="submit" class="butActionDelete" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</center>';

	print '</form>';
}

?>