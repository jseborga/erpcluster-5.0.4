<?php
//registro nuevo
print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
if ($action == 'editproduct')
  {
    print '<input type="hidden" name="action" value="updatepartidaprod">';
    print '<input type="hidden" name="idppp" value="'.$_GET['idppp'].'">';
  }
 else
   print '<input type="hidden" name="action" value="addpartidaprod">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
print '<input type="hidden" name="idp" value="'.$objpartidapre->id.'">';
print '<input type="hidden" name="ida" value="'.$objact->id.'">';
print '<input type="hidden" name="modal" value="fichepreventive">';


print '<tr class="color-product">';
// producto
print '<td colspan="2">';
print '<input id="detail" class="form-control" type="text" value="'.$objprevdetclon->detail.'" name="detail" maxlength="255" placeholder="'.$langs->trans('Nameproduct').'">';
print '</td>';
// Quant
print '<td  align="right">';
print '<input id="quant" class="form-control" type="number" value="'.$objprevdetclon->quant.'" name="quant" maxlength="12" placeholder="'.$langs->trans(Quantity).'">';
print '</td>';
// Amount
print '<td  align="right">';
print '<input id="amount_base" class="form-control" type="number" step="any" value="'.$objprevdetclon->amount_base.'" name="amount_base" maxlength="12" placeholder="'.$langs->trans('Amount').'">';
print '</td>';
print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/poa/img/save.png" width="14" height="14">';
print '</td>';
print '</tr>';
print '</form>';
?>