<?php
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
$form = new  Formv($db);

print '<input type="hidden" name="fourn_nit['.$k.']" value="'.$myclass->fourn_nit.'">';			
print '<input type="hidden" name="fourn_numaut['.$k.']" value="'.$myclass->fourn_numaut.'">';
print '<input type="hidden" name="fourn_facture['.$k.']" value="'.$myclass->fourn_facture.'">';	
print '<input type="hidden" name="fourn_date['.$k.']" value="'.$myclass->fourn_date.'">';			
print '<input type="hidden" name="fourn_amount_ttc['.$k.']" value="'.$myclass->fourn_amount_ttc.'">';
print '<input type="hidden" name="fourn_amount['.$k.']" value="'.$myclass->fourn_amount.'">';
print '<input type="hidden" name="fourn_codecont['.$k.']" value="'.$myclass->fourn_codecont.'">';	
print '<input type="hidden" name="fourn_reg1['.$k.']" value="'.$myclass->fourn_reg1.'">';
print '<input type="hidden" name="fourn_reg2['.$k.']" value="'.$myclass->fourn_reg2.'">';	
print '<input type="hidden" name="fourn_reg3['.$k.']" value="'.$myclass->fourn_reg3.'">';		
print '<input type="hidden" name="fourn_reg4['.$k.']" value="'.$myclass->fourn_reg4.'">';		
print '<input type="hidden" name="fourn_reg5['.$k.']" value="'.$myclass->fourn_reg5.'">';
print '<input type="hidden" name="socid['.$k.']" value="'.$myclass->socid.'">';
print '<input type="hidden" name="operation['.$k.']" value="'.($myclass->operation?$myclass->operation:($object->courant == 2 ? 'LIQ' : 'LIQ')).'">';
print '<input type="hidden" name="quant['.$k.']" value="1">';

print '<tr>';
if ($lViewqr)
{
	print '<td>';
	print '<input id="codeqr" class="flat" size="5" type="text" name="codeqr['.$k.']" value="'.$myclass->codeqr.'">';
	print '</td>';
			//nit
	print '<td>';
	print '<input id="nit" class="flat" size="5" type="text" name="nit['.$k.']" value="'.$myclass->nit.'">';
	print '</td>';
}
//date
print '<td>';
//$form->select_date_v(($myclass->fourn_date?$myclass->fourn_date:($myclass->dateo?$myclas->dateo:$myclass->date)),"_do",'','','','transaction',1,0,0,0,'','','',$k);
print '<input type="date" name="_do[]" value="'.date('Y-m-d').'">';
print '</td>';

//num_chq
print '<td>';
print '<input id="num_chq" class="flat" size="4" type="text" name="num_chq['.$k.']" value="'.($myclass->num_chq?$myclass->num_chq:$myclass->facture).'">';
print '</td>';

if ($lViewproj)
{
	print '<td>';
	$filterkey = '';
	$numprojet = $formproject->select_projects(($user->societe_id>0?$user->societe_id:-1), $varprojet[$k], 'fk_projet__'.$k, 0,0,1,0,0,0,0,$filterkey);
	print '</td>';
}
if ($lViewtask)
{
	if (empty($filtertask))
	{
		$fk_tasksel = 0;
		$filtertask = " t.fk_projet = 0";
	}
	print '<td>';
	print $formtask->select_task($vartask[$k], 'fk_task__'.$k, $filtertask, 1,0,0,array(),0,'',0);
	print '</td>';
}
//qty
print '<td>';
print '<input name="dp_desc['.$k.']" class="flat" size="7" type="text" size="24"  value="" required>';
print '</td>';
print '<td>';
print '<input name="amount['.$k.']" class="flat len80" type="number" step="any" min="0" value="'.$myclass->amount_ttc.'" required>';
print '</td>';			
print '<td class="eliminar">';
print img_picto($langs->trans('Delete'),'delete');
print '</td>';
print '<td>';
if ($lButtonadd)
	print '<input type="button" id="agregar" value="Agregar fila" />';
print '</td>';
print '</tr>';
$sumaup+= $myclass->amount_ttc;
?>