<?php
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
$form = new  Formv($db);

if (!$lViewqr)
	print '<input type="hidden" name="fourn_nit__'.$k.'" value="'.$myclass->fourn_nit.'">';			
print '<input type="hidden" name="fourn_numaut__'.$k.'" value="'.$myclass->fourn_numaut.'">';
print '<input type="hidden" name="fourn_facture__'.$k.'" value="'.$myclass->fourn_facture.'">';	
print '<input type="hidden" name="fourn_date__'.$k.'" value="'.$myclass->fourn_date.'">';			
print '<input type="hidden" name="fourn_amount_ttc__'.$k.'" value="'.$myclass->fourn_amount_ttc.'">';
print '<input type="hidden" name="fourn_amount__'.$k.'" value="'.$myclass->fourn_amount.'">';
print '<input type="hidden" name="fourn_codecont__'.$k.'" value="'.$myclass->fourn_codecont.'">';	
print '<input type="hidden" name="fourn_reg1__'.$k.'" value="'.$myclass->fourn_reg1.'">';
print '<input type="hidden" name="fourn_reg2__'.$k.'" value="'.$myclass->fourn_reg2.'">';	
print '<input type="hidden" name="fourn_reg3__'.$k.'" value="'.$myclass->fourn_reg3.'">';		
print '<input type="hidden" name="fourn_reg4__'.$k.'" value="'.$myclass->fourn_reg4.'">';		
print '<input type="hidden" name="fourn_reg5__'.$k.'" value="'.$myclass->fourn_reg5.'">';
print '<input type="hidden" name="socid__'.$k.'" value="'.$myclass->socid.'">';
print '<input type="hidden" name="operation__'.$k.'" value="'.($myclass->operation?$myclass->operation:($object->courant == 2 ? 'LIQ' : 'LIQ')).'">';
print '<input type="hidden" name="quant__'.$k.'" value="1">';

print '<tr>';
if ($lViewqr)
{
	print '<td>';
	print '<input id="codeqr" class="flat" size="5" type="text" name="codeqr__'.$k.'" value="'.$myclass->codeqr.'">';
	print '</td>';
			//nit
	print '<td>';
	print '<input id="nit" class="flat" size="5" type="text" name="fourn_nit__'.$k.'" value="'.GETPOST('fourn_nit__'.$k).'">';
	print '</td>';
}
//date
print '<td>';
$date_o = GETPOST('do__'.$k.'__');
if (empty($date_o))
	$date_o = dol_now();
else
{
	$date_o = dol_mktime(12,0,0,GETPOST('do__'.$k.'__month','int'),GETPOST('do__'.$k.'__day','int'),GETPOST('do__'.$k.'__year','int'));
}
print $form->select_date($date_o,'do__'.$k.'__','','','','transaction',1,0,0,0,'','','',$k);
//
//print '<input type="date" name="do__'.$k.'" value="'.$date_o.'">';
print '</td>';

//num_chq
print '<td>';
print '<input id="num_chq" class="flat" size="4" type="text" name="num_chq__'.$k.'" value="'.GETPOST('num_chq__'.$k).'">';
print '</td>';

if ($lViewproj)
{
	print '<td>';
	$filterkey = '';
	$numprojet = $formproject->select_projects(($user->societe_id>0?$user->societe_id:-1), GETPOST('fk_projet__'.$k), 'fk_projet__'.$k, 0,0,1,0,0,0,0,$filterkey);
	print '</td>';
}
else
{
	print '<input id="fk_projet'.$k.'" type="hidden" name="fk_projet__'.$k.'" value="'.$fk_projetsel.'">';
}
if ($lViewtask)
{
	$fk_proj = GETPOST('fk_projet__'.$k);
	if ($fk_proj>0)
		$filtertask = " t.fk_projet = ".$fk_proj;
	if (empty($filtertask))
	{
		$fk_tasksel = 0;
		$filtertask = " t.fk_projet = 0";
	}
	print '<td>';
	print $formtask->select_task(GETPOST('fk_task__'.$k), 'fk_task__'.$k, $filtertask, 1,0,0,array(),'',0,0,'','','','','rowid');
	print '</td>';
}
else
{
	print '<input id="fk_task'.$k.'" type="hidden" name="fk_task__'.$k.'" value="'.$fk_tasksel.'">';	
}
//qty
print '<td>';
print '<input name="dp_desc__'.$k.'" class="flat" type="text" size="24"  value="'.GETPOST('dp_desc__'.$k).'">';
print '</td>';
print '<td>';
print '<input id="amount__'.$k.'" name="amount__'.$k.'" class="flat len80" type="number" step="any" min="0" value="'.GETPOST('amount__'.$k).'">';
print '</td>';			
print '<td>';
//print '<button id="bsubd" name="bsubd" value="'.$k.'">'.img_picto('','delete').'</button>';
print '<input id="bsubd__'.$k.'" name="butdel_'.$k.'" value="'.$k.'" type="image" src="'.DOL_URL_ROOT.'/finint/img/delete.png">';
//print img_picto($langs->trans('Delete'),'delete');
print '</td>';
print '<td>';
if ($k==0)
	print '<input type="button" id="bsubm" name="bsubm" value="Agregar fila" />';
print '</td>';
print '</tr>';
$sumaup+= $myclass->amount_ttc;
?>