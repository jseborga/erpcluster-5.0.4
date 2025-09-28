<?php

print '<form name="fiche_dev" action="fiche_pas2.php" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="updatedev">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
print '<input type="hidden" name="idc" value="'.$idc.'">';
print '<input type="hidden" name="fk_contrat" value="'.$idc.'">';
print '<input type="hidden" name="idr" value="'.$idr.'">';
print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

// contratos
print '<tr>';
print '<td>'.$objcont->array_options['options_ref_contrato'].'</td>';
print '<td>'.$objsoc->nom.'</td>';

//fecha autorizacion
print '<td>';
print $form->select_date((empty($objdeve->date_dev)?dol_now():$objdeve->date_dev),'di_','','','','date_dev',1,1);
print '</td>';

//monto autorizado
print '<td align="right">';
if ($objcompr->lPartidadif == false)
  print '<input type="number" step="any" id="amount" name="amount" value="'.(empty($objdeve->amount)?price2num($saldo,'MT'):$objdeve->amount).'">';
 else
   print (empty($objdeve->amount)?price(price2num($saldo,'MT')):$objdeve->amount);
print '</td>';
print '<td align="center">';
if ($user->rights->poa->deve->mod)
  {
    print '<input type="text" id="nro_dev" name="nro_dev" value="'.$objdeve->nro_dev.'" size="2" maxlength="5">'.' / '.'<input type="text" id="gestion" name="gestion" value="'.(empty($objdeve->gestion)?date('Y'):$objdeve->gestion).'"  size="3" maxlength="4">';
  }
 else
   {
     print '<input type="hidden" name="nro_dev" value="'.$objdeve->nro_dev.'">';
     print '<input type="hidden" name="gestion" value="'.$objdeve->gestion.'">';
     print $objdeve->nro_dev.'/'.$objdeve->gestion;
   }
print '</td>';

// //nro partida
// print '<td><input type="text" id="partida" name="partida" value="'.(empty($objdev->partida)?$objcompr->partida:$objdev->partida).'" size="8" maxlength="10">';
// print '</td>';

//nro documento respaldo
print '<td>';
print '<input type="text" id="invoice" name="invoice" value="'.$objdeve->invoice.'" size="10" maxlength="30">';
print '</td>';

print '<td align="center">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="14" height="14">';

print '&nbsp;';
print '<button type="submit" name="cancel" value="'.$langs->trans('Cancel').'">'.img_picto($langs->trans('Cancel'),DOL_URL_ROOT.'/poa/img/cancel','',1).'</button>';

print '</td>';
print '</tr>';

print '</form>';

?>