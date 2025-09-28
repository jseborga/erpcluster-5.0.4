<?php
  //tab2 //asignaciones
if ($user->rights->assets->hist->read)
{
	$objassigd = new Assetsassignmentdetext($db);
	$objassigd->getlist($id);

	print_barre_liste($langs->trans("Listeassignment"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);
	print '<table class="border" style="min-width=1000px" width="100%">';

	print '<tr><td width="15%">'.$langs->trans('Code').'</td>';
	print '<td colspan="2">';
	print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref','');
	print '</td>';
	print '</tr>';
	//ref_ext
	print '<tr><td width="15%">'.$langs->trans('Refext').'</td>';
	print '<td colspan="2">';
	print $object->ref_ext;
	print '</td>';
	print '</tr>';


	print '</table>';

	if ($action == 'createassign')
	{
		print "\n".'<script type="text/javascript" language="javascript">';
		print '$(document).ready(function () {
			$("#fk_property").change(function() {
				document.fo1.action.value="createassignsearch";
				document.fo1.submit();
			});
		});';
		print '</script>'."\n";

		print '<form name="fo1" method="POST" id="fo1" action="'.$_SERVER["PHP_SELF"].'">'."\n";
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="addassign">';
		print '<input type="hidden" name="id" value="'.$id.'">';
	}

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	if (!$confg->global->ASSETS_INTEGRATION_WITH_DEPARTAMENT_MEMBER)
	{
		print_liste_field_titre($langs->trans("Office"),"", "","","","");
		print_liste_field_titre($langs->trans("Name"),"", "","","","");
	}
	else
	{
		print_liste_field_titre($langs->trans("Responsible"),"", "","","","");
		print_liste_field_titre($langs->trans("Dateassignment"),"", "","","","");
		print_liste_field_titre($langs->trans("Document"),"", "","","","");
		print_liste_field_titre($langs->trans("Project"),"", "","","","");
		print_liste_field_titre($langs->trans("Property"),"", "","","","");
		print_liste_field_titre($langs->trans("Location"),"", "","","","");
		print_liste_field_titre($langs->trans("Dateend"),"", "","","","");
		print_liste_field_titre($langs->trans("Statut"),"", "","","","");
		if ($user->rights->assets->alloc->null)
			print_liste_field_titre($langs->trans("Action"),"", "","","","");
		print "</tr>\n";
	}
	$var=True;

	if ($action == 'createassign')
	{
		include_once DOL_DOCUMENT_ROOT.'/assets/lib/adherent.lib.php';
		//registro nuevo
		print "<tr $bc[$var]>";
		print '<td nowrap>';
		print select_adherent($objassigndet->fk_adherent,'fk_adherent','enabled','',1);
		print '</td>';
		print '<td>';
		$form->select_date($objassigndet->date_assignment,'da_','','','',"date",1,1);
		print '</td>';
		print '<td>';
		$fk_property = GETPOST('fk_property','int')?GETPOST('fk_property','int'):$objassigndet->fk_property;
		$filter = " AND t.entity = ".$conf->entity;
		$res = $objProperty->fetchAll('ASC','label',0,0,array('status'=>1),'AND',$filter);
		$options = '<option value="-1">'.$langs->trans('Selectproperty').'</option>';
		$lines =$objProperty->lines;
		foreach ((array) $lines AS $j => $line)
		{
			$selected = '';
			if ($fk_property == $line->id) $selected = ' selected';
			$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.' ('.$line->ref.')'.'</option>';
		}
		print '<select id="fk_property" name="fk_property">'.$options.'</select>';

		//print $objpro->select__property($objassigndet->fk_property,'fk_property','',40,1);
		print '</td>';
		print '<td>';
		$fk_location = GETPOST('fk_location','int')?GETPOST('fk_location','int'):$objassigndet->fk_location;
		$filter = " AND t.fk_property = ".$fk_property;
		$res = $objLocation->fetchAll('ASC','detail',0,0,array('status'=>1),'AND',$filter);
		$options = '';
		$lines =$objLocation->lines;
		foreach ((array) $lines AS $j => $line)
		{
			$selected = '';
			if ($fk_location == $line->id) $selected = ' selected';
			$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->detail.'</option>';
		}
		print '<select id="fk_location" name="fk_location">'.$options.'</select>';

		//print $objloc->select__location($objassigndet->fk_location,'fk_location','',40,1,$objassigndet->fk_property);
		print '</td>';
		print '<td>';
		print '';
		print '<td>';
		print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/assets/img/save.png" width="14" height="14">';
		print '</td>';

		print "</tr>\n";

	}

	if (!$confg->global->ASSETS_INTEGRATION_WITH_DEPARTAMENT_MEMBER)
	{
		$var=!$var;
		print "<tr $bc[$var]>";
		print '<td nowrap>';
		print $object->departament_name;
		print '</td>';
		print '<td nowrap>';
		print $object->resp_name;
		print '</td>';
		print '</tr>';
	}
	else
	{

		if (count($objassigd->array) > 0)
		{
			foreach ((array) $objassigd->array AS $i => $obj)
			{
			//recuperando assignment
				$objassig = new Assetsassignment($db);
				$objassigdet = new Assetsassignmentdetext($db);
				$objassig->fetch($obj->fk_asset_assignment);
				$objassigdet->fetch($obj->id);
				$var=!$var;

				print "<tr $bc[$var]>";
				print '<td nowrap>';
				if ($objUser->fetch($objassig->fk_user))
					print $objUser->getNomUrl(1);
				else
					print $langs->trans('not defined');
				print '</td>';
				print '<td>'.dol_print_date($obj->date_assignment,'day').'</td>';
				print '<td>'.$objassig->getNomUrl(1).'</td>';

				print '<td>';
				if ($objassig->fk_projet>0)
				{
					if ($projet->fetch($objassig->fk_projet))
						print $projet->getNomUrl(1);
					else
						print $langs->trans('not defined');
				}
				print '</td>';

				print '<td>';
				if ($objProperty->fetch($objassig->fk_property))
					print $objProperty->getNomUrl(1);
				else
					print $langs->trans('not defined');
				print '</td>';
				print '<td>';
				if ($objLocation->fetch($objassig->fk_location))
					print $objLocation->detail;
				else
					print $langs->trans('not defined');
				print '<td>'.dol_print_date($obj->date_end,'day').'</td>';
				print '<td>';
				print $objassigdet->getLibStatut(1);
				print '</td>';
				if ($user->rights->assets->alloc->null && $objassigdet->status == 0)
				{
					print '<td align="center">';
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objassigdet->id.'&action=annulled">'.img_picto('','delete').'</a>';
					print '</td>';
				}
				print "</tr>\n";
				$i++;
			}
		}
	}
	print '</table>';
	if ($action == 'createassign')
		print '</form>';
}
?>