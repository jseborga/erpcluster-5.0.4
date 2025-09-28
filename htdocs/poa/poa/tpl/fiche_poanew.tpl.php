<?php
//recibiendo valores
$idp = GETPOST('idp');
$idpp = GETPOST('idpp');
//addpreventive
$display ='none';
if (isset($modal) && $modal == 'fichepoa')
{
	print '<script type="text/javascript">
	$(window).load(function(){
		$("#fichepoa").modal("show");
	});
</script>';
}
//$display = 'block';
print '<div id="fichepoa" class="modal modal-info fade in" tabindex="-1" role="dialog" style="display: '.$display.'; margin-top:0px;" data-width="760;" aria-hidden="false">';
print '<div class="poa-modal">';
print '<div class="modal-dialog modal-lg">';
print '<div class="modal-content">';

if ($user->rights->poa->poa->crear)
{

	print '<form class="form-horizontal" name="form_fiche" action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="modal" value="fichepoa">';

	dol_htmloutput_mesg($mesg);

print '<div class="modal-header" style="background:#fff; color:#000; !important">';
print '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
print '<h4 class="modal-title">'.$langs->trans("POA Insumos").'</h4>';
print '</div>';

print '<div class="modal-body" style="overflow-y: auto; color:#000; !important">';


		// structure
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Structure').'</label>';
	print '<div class=" col-sm-10">';
	print $objstr->select_structure($object->fk_structure,'fk_structure','',0,1);
	print '</div>';
	print '</div>';

		// Name
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Name').'</label>';
	print '<div class=" col-sm-10">';
	print '<input class="form-control" type="text" name="label" value="'.$object->label.'" >';
	print '</div>';
	print '</div>';

		// Pseudonim
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Pseudonym').'</label>';
	print '<div class=" col-sm-10">';
	print '<input class="form-control" type="text" name="pseudonym" value="'.$object->pseudonym.'">';
	print '</div>';
	print '</div>';

		// Patida
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Partida').'</label>';
	print '<div class=" col-sm-10">';
	print '<input class="form-control" type="text" name="partida" value="'.$object->partida.'">';
	print '</div>';
	print '</div>';

		// Amount
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Amount').'</label>';
	print '<div class=" col-sm-10">';
	print '<input class="form-control" type="number" min="0" step="any" name="amount" value="'.$object->amount.'">';
	print '</div>';
	print '</div>';

		// Patida
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Version').'</label>';
	print '<div class=" col-sm-10">';
	print '<input class="form-control" type="text" name="version" value="'.$object->version.'">';
	print '</div>';
	print '</div>';

	print '</div>'; //modal-body

	print '<div class="modal-footer">';
	print '<center><input type="submit" class="btn btn-primary btn-flat" value="'.$langs->trans("Create").'"></center>';
	print '</div>';

	print '</form>';
}

print '</div>'; //modal-content
print '</div>'; //modal-dialog
print '</div>'; //modal modal-source
print '</div>'; //modal fade
?>