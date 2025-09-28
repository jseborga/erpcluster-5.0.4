<?php

print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST" name="form_o">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="addsupplies">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
print '<input type="hidden" name="idi" value="'.$obj->id.'">';

//registro nuevo
$typeid = 0;
$includearray =
  array($conf->global->PRICEUNITS_MACHINERY_DEF =>$conf->global->PRICEUNITS_MACHINERY_DEF,
	$conf->global->PRICEUNITS_MATERIALS_DEF =>$conf->global->PRICEUNITS_MATERIALS_DEF,
	$conf->global->PRICEUNITS_WORKFORCE_DEF =>$conf->global->PRICEUNITS_WORKFORCE_DEF,
);
print "<tr $bc[$var]>";
print '<td>';
print $formv->select_all_categories($typeid,'auto','fk_category','',0,$includearray,1);
print '</td>';
print '<td>';
print $form->select_produits($fk_product,'fk_product','','','',1,2,'',1);
print '</td>';

print '<td>';
print $objunit->select_unit($fk_unit,'fk_unit','',1,0,'rowid','code');
print '</td>';
print '<td>';
print $form->select_company($fk_company,'fk_company');
print '</td>';

print '<td>';
print '<input type="number" class="len50" min="0" step="any" name="quant" value="'.$quant.'">';
print '</td>';

print '<td>';
print '<input type="number" class="len50" min="0" step="any" name="price" value="'.$price.'">';
print '</td>';

print '<td>';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/budget/img/save.png" width="14" height="14">';
print '</td>';

print '</tr>';
?>