<?php
//registro nuevo
print "\n".'<script type="text/javascript" language="javascript">';
print '$(document).ready(function () {
	$("#selectfk_poa").change(function() {
		document.form_meta.action.value="createeditpar";
		document.form_meta.submit();
	});
});';
print '</script>'."\n";

print '<form name="form_meta" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
if ($action=='editpartida')
{
	print '<input type="hidden" name="action" value="updatepartida">';
	print '<input type="hidden" name="idp" value="'.$idp.'">';
}
else
	print '<input type="hidden" name="action" value="addpartida">';
print '<input type="hidden" name="ida" value="'.$objact->id.'">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
print '<input type="hidden" name="modal" value="fichepreventive">';

print '<tr>';
// poa
print '<td>';
print $objpoa->select_poa((empty($objpre->fk_poa)?(empty($objpac->fk_poa)?$objact->fk_poa:$objpac->fk_poa):$objpre->fk_poa),'fk_poa','',75,1,$objpre->fk_structure);
print '</td>';
//buscamos el poa
$objpoasearch = new Poapoa($db);
$objpoasearch->fetch((empty($objpre->fk_poa)?(empty($objpac->fk_poa)?$objact->fk_poa:$objpac->fk_poa):$objpre->fk_poa));
// structure
print '<td width="100">';
print $objstr->select_structure($objpoasearch->fk_structure,'fk_structure','',3,1,3);
print '</td>';
$gestion = date('Y');
// partida
$objpoasearch->get_partida($objpoasearch->fk_structure,$object->gestion);
print '<td width="90">';
print $form->selectarray('partida',$objpoasearch->array,$objpoasearch->partida,1,0,0,'style="width:90px;"',0,0,0,'',' form-control');
// print '<input id="partida" type="text" value="'.$objprev->partida.'" name="partida" size="12" maxlength="10">';
print '</td>';

// amount
print '<td width="90px">';
print '<input class="form-control" style="min-width:110px" id="amount" type="number" min="0" step="any" value="'.(empty($objpre->amount)?$objact->amount:$objpre->amount).'" name="amount" maxlength="12">';
print '</td>';

print '<td align="right">';
print '<button class="btn btn-default btn-flat" type="submit" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/poa/img/save.png" width="16" height="16">'.'<i class="fa fa-save" aria-hidden="true"></i>'.'</button>';
print '</td>';
print '</tr>';

print '</form>';

?>