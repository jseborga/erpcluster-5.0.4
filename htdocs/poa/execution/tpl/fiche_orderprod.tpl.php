<?php
//addpreventive
$display ='none';
if (isset($modal) && $modal == 'ficheorderpro')
{
	print '<script type="text/javascript">
	$(window).load(function(){
		$("#'.$tagidd.'").modal("show");
	});
</script>';
}
//$display = 'block';
print '<div id="'.$tagidd.'" class="modal fade in" tabindex="-1" role="dialog" style="display: '.$display.'; margin-top:0px;" data-width="760;" aria-hidden="false">';

print '<div class="poa-modal">';
print '<div class="modal">';
print '<div class="modal-dialog modal-lg">';
print '<div class="modal-content">';

print '<div class="modal-header" style="background:#fff; color:#000; !important">';
print '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
print '<h4 class="modal-title">'.$langs->trans("Contrat").': '.$objcont->array_options['options_ref_contrato'].'</h4>';
print '</div>';

print '<div class="modal-body" style="overflow-y: auto; color:#000; !important">';

if ($idProcess>0 && $idrcreg > 0)
{
	if ($user->rights->poa->op->rd)
	{
		print '<form class="form-horizontal col-sm-12" name="form_fiche" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="updaterd">';
		print '<input type="hidden" name="modal" value="ficheorderpro">';
		print '<input type="hidden" name="ida" value="'.$ida.'">';
		print '<input type="hidden" name="idrc" value="'.$idrcreg.'">';

		dol_htmloutput_mesg($mesg);
	//buscamos el registro en poa process contrat
		$res = $objpcon->fetch($idrcreg);

		// orden proceder
		$aDate = dol_getdate(dol_now());
		if ($objpcon->date_final > 0)
			$aDate = dol_getdate($objpcon->date_final);
		$dateop = $aDate['year'].'-'.$aDate['mon'].'-'.$aDate['mday'];
	//date order proceed
		print '<div class="form-group">';
		print '<label class="col-sm-3 control-label" for="socid">'.$langs->trans('Definitive').'</label>';
		print '<div class="col-xs-8">';
		print '<div class="well well-sm">';
		print '<div class="input-group date" id="datepicinixx">';
		print '<input type="text" name="di_" id="datepicinid" class="flat" value="'.$dateop.'" readonly/>';
		print '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>';
		print '</span>';
		print '</div>';
		print '</div>';
		print '</div>';
		print '</div>';

		print '<center><br><input type="submit" class="btn btn-primary btn-flat" value="'.$langs->trans("Save").'"></center>';

		print '</form>';
	}
}
print '</div>'; //modal-body
print '</div>'; //modal-content
print '</div>'; //modal-dialog
print '</div>'; //modal modal-source
print '</div>'; //poa_modal
print '</div>'; //modal fade
?>