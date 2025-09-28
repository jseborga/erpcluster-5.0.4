<?php
print '<table class="table">';
$aTable = fetch_tables($fk_type_con);
print '<tr>';
print '<th colspan="4">'.$langs->trans("Necessary documentation").'</th>';
print '</tr>';
print '<tr>';
print '<th colspan="3">'.'</th>';
print '<th>'.$langs->trans('label').'</th>';
print '</tr>';
			//generico
if ($aTable['type'] == 'MENSPAC' || $aTable['type'] == 'MEN') $value = 1;
elseif (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY') $value = 2;
elseif (STRTOUPPER($aTable['type']) == 'LP') $value = 3;
elseif (STRTOUPPER($aTable['type']) == 'DIREC' || STRTOUPPER($aTable['type']) == 'EXCEP') $value = 4;
elseif (STRTOUPPER($aTable['type']) == 'CAE') $value = 5;

//type certif presup
print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_cp').'</td><td class="text-center">';
print '<div class="col-md-1">';
print '<input type="checkbox" name="doc_certif_presupuestaria" value="'.$value.'" checked="checked">';
print '</div>';
print '</td></tr>';

//precio referencial
print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_pr').'</td><td class="text-center">';
print '<div class="col-md-1">';
print '<input type="checkbox" name="doc_precio_referencial" value="'.$value.'" checked="checked">';
print '</div>';
print '</td></tr>';

//type especif tecnica
print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_et').'</td><td class="text-center">';
print '<div class="col-md-1">';
print '<input type="checkbox" name="doc_especific_tecnica" value="'.$value.'" checked="checked">';
print '</div>';
print '</td></tr>';

			//modelo contrato
if ($lForm)
{
	$checked = '';
	if ($objproc->doc_modelo_contrato > 0) $checked = 'checked="checked"';
	print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Modelo de Contrato elaborado por la GAL').'</td><td class="text-center">';
	print '<div class="col-md-1">';
	print '<input type="checkbox" name="doc_modelo_contrato" value="'.$value.'" '.$checked.'>';
	print '</div>';
}
else
{
	if (STRTOUPPER(trim($aTable['type'])) == 'ANPEMEN' || STRTOUPPER(trim($aTable['type'])) == 'ANPEMAY' || STRTOUPPER(trim($aTable['type'])) == 'LP' || STRTOUPPER(trim($aTable['type'])) == 'CEA' )
	{
		$checked = '';
		if ($objproc->doc_modelo_contrato > 0) $checked = 'checked="checked"';
		print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Modelo de Contrato elaborado por la GAL').'</td><td class="text-center">';
		print '<div class="col-md-1">';
		print '<input type="checkbox" name="doc_modelo_contrato" value="'.$value.'" '.$checked.'>';
		print '</div>';
	}
}
if ($lForm)
{
	print '<div class="col-md-1">';
	if (STRTOUPPER(trim($aTable['type'])) == 'ANPEMEN' || STRTOUPPER(trim($aTable['type'])) == 'ANPEMAY')
		print '<a href="#">'.img_picto($langs->trans("help_anpe"),'help').'</a>';
	elseif (STRTOUPPER(trim($aTable['type'])) == 'DIREC')
		print '<a href="#">'.img_picto($langs->trans("help_direc"),'help').'</a>';
	elseif (STRTOUPPER(trim($aTable['type'])) == 'MENSPAC' || STRTOUPPER(trim($aTable['type'])) == 'MEN')
		print '<a href="#">'.img_picto($langs->trans("help_men"),'help').'</a>';
	print '</div>';
}
print '</td></tr>';

			//fotocopia PAC
if (!$lForm)
{
	$checked = '';
	if (STRTOUPPER(trim($aTable['type'])) == 'ANPEMEN' || STRTOUPPER(trim($aTable['type'])) == 'ANPEMAY' || STRTOUPPER(trim($aTable['type'])) == 'LP' )
		$checked = ' checked="checked"';
	print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Fotocopia hoja PAC donde se encuentra incluido proceso de contratacion').'</td><td class="text-center">';
	print '<div class="col-md-1">';
	print '<input type="checkbox" name="doc_pac" value="'.$value.'"'.$checked.'>';
	print '</div>';
}
if (!$lForm)
{
	if (STRTOUPPER($aTable['type']) == 'DIREC' || STRTOUPPER($aTable['type']) == 'EXCEP')
	{
		//informe tecnico LEGAL
		$checked = '';
		if ($objproc->doc_informe_lega > 0) $checked = 'checked="checked"';

		print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_it').'</td><td class="text-center">';
		print '<div class="col-md-1">';
		print '<input type="checkbox" name="doc_informe_lega" value="'.$value.'" '.$ckecked.'>';
		print '</div>';
		print '</td></tr>';
		if (!$lForm)
		{
						//Seleccion de mepresa proponente
			print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Proponente Seleccionado').'</td><td class="text-center">';
			print $form->select_company('','fk_soc','',1,0,0);
			print '</td></tr>';
		}
	}
}
else
{
	if (STRTOUPPER($aTable['type']) != 'MENSPAC' && STRTOUPPER($aTable['type']) != 'MEN')
	{
					//informe tecnico LEGAL
		$checked = '';
		if ($objproc->doc_informe_lega > 0) $checked = 'checked="checked"';

		print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_it').'</td><td class="text-center">';
		print '<div class="col-md-1">';
		print '<input type="checkbox" name="doc_informe_lega" value="'.$value.'" '.$checked.'>';
		print '</div>';
		print '</td></tr>';
	}
}
if (!$lForm)
{
				//lista de proponentes para CM
	if (STRTOUPPER($aTable['type']) == 'MENSPAC')
	{
		$checked = '';
		if ($objproc->doc_prop > 0) $checked = 'checked="checked"';
		print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Lista de proponentes').'</td><td class="text-center">';
		print '<div class="col-md-1">';
		print '<input type="checkbox" name="doc_prop" value="'.$value.'">';
		print '</div>';
		print '</td></tr>';
	}
}
			//metodo de seleccion
if (STRTOUPPER($aTable['type']) != 'MENSPAC' && STRTOUPPER($aTable['type']) != 'MEN' && STRTOUPPER($aTable['type']) != 'DIREC' && !empty($fk_type_con))
{

	print '<tr>';
	print '<th colspan="3">'.$langs->trans('Method selection and award').'</th>';
	print '<th colspan="3">&nbsp;</th>';
	print '</tr>';

	if (STRTOUPPER($aTable['type']) != 'CEA')
	{
		include DOL_DOCUMENT_ROOT.'/poa/process/tpl/title_cea.tpl.php';
		if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' || STRTOUPPER($aTable['type']) == 'LP' )
		{
			//calidad propuesta tecnica y costo
			print '<tr>';
			include DOL_DOCUMENT_ROOT.'/poa/process/tpl/cptc.tpl.php';
			print '<td align="center">';
			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			{
				$checked = '';
				if ($objproc->metodo_sel_anpe == 1) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_anpe" value="1" '.$checked.'>';
			}
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			{
				$checked = '';
				if ($objproc->metodo_sel_lpni == 1) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_lpni" value="1" '.$checked.'>';
			}
			elseif (STRTOUPPER($aTable['type']) == 'CAE' )
			{
				$checked = '';
				if ($objproc->metodo_sel_cae == 1) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_cae" value="1" '.$checked.'>';
			}
			print '</td></tr>';

						//calidad
			print '<tr>';
			include DOL_DOCUMENT_ROOT.'/poa/process/tpl/c.tpl.php';
			print '<td align="center">';

			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			{
				$checked = '';
				if ($objproc->metodo_sel_anpe == 2) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_anpe" value="2" '.$checked.'>';
			}
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			{
				$checked = '';
				if ($objproc->metodo_sel_lpni == 2) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_lpni" value="2" '.$checked.'>';
			}
			elseif (STRTOUPPER($aTable['type']) == 'CAE' )
			{
				$checked = '';
				if ($objproc->metodo_sel_cae == 1) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_cae" value="1" '.$checked.'>';
			}
			print '</td></tr>';

						//Presupuesto Fijo
			print '<tr>';
			include DOL_DOCUMENT_ROOT.'/poa/process/tpl/pf.tpl.php';

			print '<td align="center">';
			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			{
				$checked = '';
				if ($objproc->metodo_sel_anpe == 3) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_anpe" value="3" '.$checked.'>';
			}
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			{
				$checked = '';
				if ($objproc->metodo_sel_lpni == 3) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_lpni" value="3" '.$checked.'>';
			}
			elseif (STRTOUPPER($aTable['type']) == 'CAE' )
			{
				$checked = '';
				if ($objproc->metodo_sel_cae == 1) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_cae" value="1" '.$checked.'>';
			}
			print '</td></tr>';

						//Menor Costo
			print '<tr>';
			include DOL_DOCUMENT_ROOT.'/poa/process/tpl/mc.tpl.php';

			print '<td align="center">';

			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			{
				$checked = '';
				if ($objproc->metodo_sel_anpe == 4) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_anpe" value="4" '.$checked.'>';
			}
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			{
				$checked = '';
				if ($objproc->metodo_sel_lpni == 4) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_lpni" value="4" '.$checked.'>';
			}
			elseif (STRTOUPPER($aTable['type']) == 'CAE' )
			{
				$checked = '';
				if ($objproc->metodo_sel_cae == 1) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_cae" value="1" '.$checked.'>';
			}
			print '</td></tr>';

						//Prcio evaluado mas bajo (PEMB)
			print '<tr>';
			include DOL_DOCUMENT_ROOT.'/poa/process/tpl/pemb.tpl.php';

			print '<td align="center">';

			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			{
				$checked = '';
				if ($objproc->metodo_sel_anpe == 5) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_anpe" value="5" '.$checked.'>';
			}
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			{
				$checked = '';
				if ($objproc->metodo_sel_lpni == 5) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_lpni" value="5" '.$checked.'>';
			}
			elseif (STRTOUPPER($aTable['type']) == 'CAE' )
			{
				$checked = '';
				if ($objproc->metodo_sel_cae == 1) $checked = 'checked="checked"';
				print '<input type="checkbox" name="metodo_sel_cae" value="1" '.$checked.'>';
			}
			print '</td></tr>';

						//formulario de condiciones... (PEMB)
			print '<tr><td  colspan="3">'.$langs->trans('Formulario de Condiciones Adicionales (Excepto para el metodo de PEMB)').'</td>';
			print '<td align="center">';

			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			{
				$checked = '';
				if ($objproc->condicion_adicional_anpe == 2) $checked = 'checked="checked"';
				print '<input type="checkbox" name="condicion_adicional_anpe" value="2" '.$checked.'>';
			}
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			{
				$checked = '';
				if ($objproc->condicion_adicional_lpni == 3) $checked = 'checked="checked"';
				print '<input type="checkbox" name="condicion_adicional_lpni" value="3" '.$checked.'>';
			}
			print '</td></tr>';
		}
	}
	else
	{
					//modelo CAE
		$checked = '';
		if ($objproc->metodo_sel_cae == 5) $checked = 'checked="checked"';

		print '<tr><td  colspan="3" class="fieldrequired">'.$langs->trans('mod_cae').'</td>';
		print '<td align="center">';
		print '<input type="checkbox" name="metodo_sel_cae" value="5" '.$checked.'>';
		print '</td></tr>';
	}
}
print '</table>';

?>