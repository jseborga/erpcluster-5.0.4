<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/poa/process/fiche_pas1.php
 *  \ingroup    Process
 *  \brief      Page fiche poa process register contrat.
 */


$action=GETPOST('action');

//$id        = GETPOST("id"); //proces
$idr       = GETPOST('idr'); //registro seguimiento

/*
 * View
 */
$form=new Form($db);
$display ='none';
if (isset($modal) && $modal == 'ficheseg')
{
	print '<script type="text/javascript">
	$(window).load(function(){
		$("#ficheseg").modal("show");
	});
</script>';
}

//print '<div class="modal-scrollable" style="z-index:1060;">';
print '<div id="ficheseg" class="modal" tabindex="-1" role="dialog" style="display: '.$display.'; margin-top:0px;" data-width="760" aria-hidden="false">';

//print '<form id="InfroText" class="form-horizontal col-sm-12"  name="fiche_comp" action="'.$_SERVER['PHP_SELF'].'" method="post">';
//		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
//		print '<input type="hidden" name="action" value="addcontrat">';
//		print '<input type="hidden" name="id" value="'.$object->id.'">';
//		print '<input type="hidden" name="ida" value="'.$ida.'">';
print '<div class="poa-modal">';
print '<div class="modal">';
print '<div class="modal-dialog modal-lg">';
print '<div class="modal-content">';

print '<div class="modal-header" style="background:#fff; color:#000; !important">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h4 class="modal-title">'.$langs->trans("Tracing").'</h4>
	  </div>';

print '<div class="modal-body" style="background:#fff; color:#000; !important">';
print '<div class="row">';
if ($objact->statut == 1 && $user->rights->poa->act->addm && $action!='editmon')
{
	print '<form name="form_meta" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
	if ($action=='editmon')
	{
		print '<input type="hidden" name="action" value="updatemon">';
		print '<input type="hidden" name="idr" value="'.$idr.'">';
	}
	else
		print '<input type="hidden" name="action" value="addmon">';
	print '<input type="hidden" name="ida" value="'.$ida.'">';
	print '<input type="hidden" name="modal" value="ficheactivity">';
}

print '<table class="table table-hower" id="tabla">';
print '<thead>';
print '<tr>';
print '<th>'.$langs->trans('Date').'</th>';
print '<th>'.$langs->trans('Location').'</th>';
print '<th>'.$langs->trans('Docverif').'</th>';
print '<th>'.$langs->trans('Followup').'</th>';
print '<th>'.$langs->trans('Followto').'</th>';
print '<th>'.$langs->trans('Action').'</th>';
print '</tr>';
print '</thead>';
print '<tbody>';

//registro nuevo
if ($objact->statut == 1 && $user->rights->poa->act->addm && $action!='editmon')
{
	include DOL_DOCUMENT_ROOT.'/poa/activity/tpl/formw.tpl.php';
}


//detalla el workflow

if (count($objectw->array) > 0)
{
	$var = true;
	$nLoop = 0;
	foreach ($objectw->array AS $j => $objectw_)
	{
		if ($action == 'editmon' && $objectw_->id == $idr)
		{
			//buscamos rwvisar
			$objectw->fetch($idr);
			$objectw_ = $objectw;
			include DOL_DOCUMENT_ROOT.'/poa/activity/tpl/formw.tpl.php';
		}
		else
		{
			if ($nLoop < 2 || $user->admin)
			{
				$var=!$var;
				print "<tr>";
				print '<td align="center">';
				print dol_print_date($objectw_->date_tracking,'day');
				print '</td>';
				print '<td>';
				print $objectw_->code_area_next;
				print '</td>';
				print '<td>';
				print $objectw_->doc_verif;
				print '</td>';
				print '<td>';
				print $objectw_->followup;
				print '</td>';
				print '<td>';
				print $objectw_->followto;
				print '</td>';
				//action
				print '<td align="center">';
				if ($objact->statut == 1 && $user->rights->poa->act->modm)
					print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&idr='.$objectw_->id.'&action=editmon&modal=ficheseg'.'">'.img_picto($langs->trans('Edit'),'edit').'</a>';
				print '&nbsp;&nbsp;';
				if ($objact->statut == 0 && $user->rights->poa->act->delm)
					print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&idr='.$objectw_->id.'&action=deletemon&modal=ficheseg'.'">'.img_picto($langs->trans('Delete'),'delete').'</a>';
				print '</td>';
				print '</tr>';
			}
		}
		$nLoop++;
	}
}
print '</tbody>';

print '</table>';
if ($objact->statut == 1 && $user->rights->poa->act->addm && $action!='editmon')
	print '</form>';
print '</div>';
print '</div>';
print '</div>';
print '</div>';

print '</div>';//modal-content
print '</div>';//modal-backdrop">';
print '</div>';//activityseg
?>
