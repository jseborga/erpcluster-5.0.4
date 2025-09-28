<?php
if ($subaction == 'verifup' || $subaction == 'verifuptask')
{
	dol_fiche_head();

	print '<form  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="action" value="addup">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="table" value="'.$table.'">';
	print '<input type="hidden" name="seldate" value="'.$seldate.'">';
	print '<input type="hidden" name="camposdate" value="'.$camposdate.'">';
	print '<input type="hidden" name="separator" value="'.$separator.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	if ($subaction == 'verifuptask')
		print '<input type="hidden" name="idg" value="'.$idg.'">';

	print '<table class="noborder" width="100%">';

	//encabezado
	$table = 'llx_projet_task';
	foreach($aHeaders AS $i => $value)
	{
		$aHeadersOr[trim($value)] = trim($value);
	}
	$aValHeader = array();
	foreach($aHeaderTpl[$table] AS $i => $value)
	{
		if (!$aHeadersOr[trim($value)])
			$aValHeader[$value] = $value;
	}
	print '<tr class="liste_titre">';
	foreach($aHeaders AS $i => $value)
	{
		print_liste_field_titre($langs->trans($value),'fiche.php','','','','');
	}
	print '</tr>';
	if (!empty($aValHeader))
	{
		$lSave = false;
		print "<tr class=\"liste_titre\">";
		print '<td>'.$langs->trans('Missingfieldss').'</td>';
		foreach ((array) $aValHeader AS $j => $value)
		{
			print '<td>'.$value.'</td>';
		}
		print '</tr>';
	}
	else
	{
		$lSave = true;
		$var=True;
		$c = 0;
		foreach($data AS $key)
		{
			$var=!$var;
			print "<tr $bc[$var]>";
			$c++;
			foreach($aHeaders AS $i => $keyname)
			{
				if (empty($keyname))
					$keyname = "none";
				$phone = $key->$keyname;
				$aArrData[$c][$keyname] = $phone;
				print '<td>'.$phone.'</td>';
			}
			print '</tr>';
		}
	}
	print '</table>';

	if ($lSave)
	{
		$_SESSION['aArrData'] = $aArrData;
		print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';
	}
	//validando el encabezado
	print '</form>';

	dol_fiche_end();
}