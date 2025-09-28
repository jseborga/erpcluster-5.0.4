<?php

$k = 0;
print '<input type="hidden" name="fourn_nit" value="'.$myclass->fourn_nit.'">';			
print '<input type="hidden" name="fourn_numaut" value="'.$myclass->fourn_numaut.'">';
print '<input type="hidden" name="fourn_facture" value="'.$myclass->fourn_facture.'">';	
print '<input type="hidden" name="fourn_date" value="'.$myclass->fourn_date.'">';			
print '<input type="hidden" name="fourn_amount_ttc" value="'.$myclass->fourn_amount_ttc.'">';
print '<input type="hidden" name="fourn_amount" value="'.$myclass->fourn_amount.'">';
print '<input type="hidden" name="fourn_codecont" value="'.$myclass->fourn_codecont.'">';	
print '<input type="hidden" name="fourn_reg1" value="'.$myclass->fourn_reg1.'">';
print '<input type="hidden" name="fourn_reg2" value="'.$myclass->fourn_reg2.'">';	
print '<input type="hidden" name="fourn_reg3" value="'.$myclass->fourn_reg3.'">';		
print '<input type="hidden" name="fourn_reg4" value="'.$myclass->fourn_reg4.'">';		
print '<input type="hidden" name="fourn_reg5" value="'.$myclass->fourn_reg5.'">';
print '<input type="hidden" name="socid" value="'.$myclass->socid.'">';
print '<input type="hidden" name="operation" value="'.($myclass->operation?$myclass->operation:($object->courant == 2 ? 'LIQ' : 'LIQ')).'">';
print '<input type="hidden" name="quant" value="'.$myclass->quant.'">';

print '<tr>';
if ($lViewqr)
{
	print '<td>';
	print '<input id="codeqr" class="flat" size="5" type="text" name="codeqr" value="'.$myclass->codeqr.'">';
	print '</td>';
			//nit
	print '<td>';
	print '<input id="nit" class="flat" size="5" type="text" name="nit" value="'.$myclass->nit.'" required>';
	print '</td>';
}
//date
print '<td>';
//$newdate = dol_print_date($myclass->dateo, "%Y-%m-%d");
//print '<input type="date" name="do_" value="'.date($newdate).'">';
print $form->select_date($myclass->dateo,'do_','','','','transaction',1,0,0,0,'','','',$k);
print '</td>';

//num_chq
print '<td>';
print '<input id="num_chq" class="flat" size="4" type="text" name="num_chq" value="'.($myclass->nro_chq?$myclass->nro_chq:$myclass->facture).'">';
print '</td>';

if ($lViewproj)
{
	print '<td>';
	$filterkey = '';
	$numprojet = $formproject->select_projects(($user->societe_id>0?$user->societe_id:-1), $fk_projetsel, 'fk_projet__0', 0,0,1,0,0,0,0,$filterkey);
	print '</td>';
}
if ($lViewtask)
{
	if (empty($filtertask))
	{
		$filtertask = " t.fk_projet = 0";
	}
	print '<td>';
	print $formtask->select_task($fk_tasksel, 'fk_task', $filtertask, 1,0,0,array(),'',0,0,'','','','','rowid');
	print '</td>';
}
//qty
print '<td>';
print '<input name="dp_desc" class="flat" size="15" type="text" size="24"  value="'.$myclass->detail.'" required>';
print '</td>';
print '<td>';
print '<input name="amount" class="flat len80" type="number" step="any" min="0" value="'.price2num($myclass->amount_ttc,'MU').'" required>';
print '</td>';			
print '<td align="center" class="eliminar">';
//print img_picto($langs->trans('Delete'),'delete');
print '</td>';
print '<td>';
if ($lButtonadd)
	print '<input type="button" id="agregar" value="Agregar fila" />';
print '</td>';
print '</tr>';
$sumaup+= $myclass->amount_ttc;
?>