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



$mesg = '';


/*
 * View
 */
$idrc = GETPOST('idrc','int');

$form=new Form($db);
$display ='none';
if (isset($modal) && $modal == 'fichecommitted' && $idrc == $idrcreg)
{
	print '<script type="text/javascript">
	$(window).load(function(){
		$("#'.$tagid.'").modal("show");
	});
</script>';
}

print '<div id="'.$tagid.'" class="modal modal-success" tabindex="-1" role="dialog" style="display: '.$display.'; margin-top:0px;" data-width="760" aria-hidden="false">';

print '<div class="poa-modal">';
print '<div class="modal-dialog modal-lg">';
print '<div class="modal-content">';

print '<div class="modal-header" style="background:#fff; color:#000; !important">';
print '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
print '<h4 class="modal-title"><i>'.$langs->trans("Contrat").':</i> '.$aContratname[$i].' <i>'.$langs->trans('Date').':</i> '.dol_print_date($datecontrat,'day').' <i>'.$langs->trans('Name').':</i> '.$objsoc->nom.'</h4>';
print '</div>';


//if ($action == 'selcon' && $user->rights->poa->comp->crear)
if ($user->rights->poa->comp->crear)
{
	print '<form id="InfroText" class="form-horizontal col-sm-12"  name="fiche_comp" action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="updateprod">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="ida" value="'.$ida.'">';
	print '<input type="hidden" name="idrc" value="'.$idrcreg.'">';
	print '<input type="hidden" name="modal" value="fichecommitted">';

	dol_htmloutput_mesg($mesg);

	print '<div class="modal-body" style="background:#fff; color:#000; !important">';
	print '<div id="listapart" style="display:block;">';

		//lista las partidas
		//carga de datos para registro nuevo
	$objpre->getlist($id);
		//suma por fk_structure,fk_poa,partida
	$objpre->getsumpartida($id);
		//buscamos si tiene dependientes
	$objectd = new Poaprev($db);
		//$objectd->getlistfather($object->fk_poa_prev);
	$objectd->getlistfather($id);
	$aPartida = array();
	if (count($objectd->arrayf)>0)
	{
		$objppf = new Poapartidapre($db);
		//obtenemos en un array las partidas que se sumaran
		foreach((array) $objectd->arrayf AS $id1 => $objectp)
		{
			$objppf->getlist($id1);
			if (count($objppf->array) > 0)
			{
				foreach((array) $objppf->array AS $j => $objpart)
				{
					$aPartida[$objpart->fk_structure][$objpart->fk_poa][$objpart->partida]+=$objpart->amount;
				}
			}
		}
	}
	print '<br>';
	print '<div class="table-responsive">';
	print '<table class="table">';
	print '<thead>';
	print '<tr>';
	print '<th>'.$langs->trans("Partida").'</th>';
	print '<th>'.$langs->trans("Amount").'</th>';
	print '<th>'.$langs->trans("Product").'</th>';
	print '<th>'.$langs->trans("Q").'</th>';
	print '<th>'.$langs->trans("Adj.").'</th>';
	print '<th>'.$langs->trans("Contract amount").'</th>';
	print '</tr>';
	print '</thead>';
	print '<tbody>';
	//recuperamos las partidas del preventivo seleccionado $idp
	if (count($objpre->array) > 0)
	{
		$lRegcont = false;
		$var = true;
		foreach((array) $objpre->array AS $j => $objpart)
		{
			//buscamos la suma de la partida
			$amount = 0;
			foreach ((array) $objpre->arraysum AS $k => $arrvalue)
			{
				if ($arrvalue['fk_structure'] == $objpart->fk_structure && $arrvalue['fk_poa'] == $objpart->fk_poa && $arrvalue['partida'] == $objpart->partida)
					$amount+= $arrvalue['amount'];
			}
			$idc = $objpcon->fk_contrat;

			if ($idpa>0) $statutcon = 0;
			else $statutcon = $objpcon->statut;
			//$objppd->getlist2($objpart->id,$idc,$statutcon,'S');
			$objppd->getlist2($objpart->id,$idc,$objpcon->statut,'S');
			// echo '<pre>';
			// print_r($objppd->array);
			// echo '</pre>';
			// echo '<hr>count '.count($objppd->array).' id ' .$objpart->id;
			if (count($objppd->array) > 0)
			{
				foreach((array) $objppd->array AS $k => $objpartdet)
				{
					//sumamos si tiene dependientes el preventivo
					// $amount = $aPartida[$objpart->fk_structure][$objpart->fk_poa][$objpart->partida] + $objpart->amount;
					$amount += $aPartida[$objpart->fk_structure][$objpart->fk_poa][$objpart->partida];
					if ($objpartdet->quant_adj > 0) $lRegcont = true;
					$var=!$var;
					print "<tr>";
					print '<td>'.$objpart->partida.'</td>';
					print '<td align="right">'.number_format(price2num($objpartdet->amount_base,'MT'),2).'</td>';
					print '<input type="hidden" name="amountPart['.$objpartdet->id.']" value="'.$objpartdet->amount_base.'">';

					print '<td>'.$objpartdet->detail.'</td>';
					print '<td align="right">'.$objpartdet->quant.'</td>';
					//revisar que ocurre si son varios items y son de la anterior gestion
					//if ($statutcon == 0 || $action == 'editc')
					if ($objpcon->statut == 0 || $action == 'editc')
					{
						print '<td align="right">'.'<input type="number" class="form-control" name="quant_adj['.$objpartdet->id.']" step="any" max="'.$objpartdet->quant.'" value="'.(empty($objpartdet->quant_adj)?$objpartdet->quant:$objpartdet->quant_adj).'" maxlength="12">'.'</td>';
						print '<td align="right">'.'<input type="number" class="form-control" step="any" name="amount['.$objpartdet->id.']" step="any" max="'.$objpartdet->amount_base.'" value="'.$objpartdet->amount.'" maxlength="15">'.'</td>';
						//print '<td align="center">'.'<a href="'.$_PHP['SELF'].'?id='.$id.'&idrc='.$idrc.'&idpd='.$objpartdet->id.'&action=deletecomp">'.img_picto($langs->trans('Delete'),'delete').'</a>'.'</td>';
					}
					else
					{
						print '<td align="right">'.$objpartdet->quant_adj.'</td>';
						print '<td align="right">'.number_format(price2num($objpartdet->amount,'MT'),2).'</td>';
					}
					print '</tr>';
				}
			}
			else
			{
			}
		}
	}
	print '</tbody>';
	print '</table>';
	print '</div>';
	print '</div>';
	print '</div>';

	print '<div class="modal-footer">';

	if ($objpcon->statut == 0 || ($objpcon->statut > 0 && $idpa > 0) &&( $action == 'editc' || $action == 'editop'))
	{
		print '<center>';
		print '<input type="submit" class="btn btn-primary btn-flat" value="'.$langs->trans("Save").'">';
		print '<input type="submit" class="btn btn-danger btn-flat" name="cancel" value="'.$langs->trans("Cancel").'">&nbsp;';
		if ($action == 'selcon' && $lRegcont)
		{
			print '<input type="submit" class="btn btn-success btn-flat" name="approved" value="'.$langs->trans("Save and approve").'"></center>';
		}
		//print '</form>';
	}
	if ($user->rights->poa->comp->mod && $objpcon->statut > 0 && $action == 'editc')
	{
		print '<center>';
		print '<input type="submit" class="btn btn-primary btn-flat" value="'.$langs->trans("Save").'">';
		print '<input type="submit" class="btn btn-danger btn-flat" name="cancel" value="'.$langs->trans("Cancel").'">&nbsp;';
		if ($action == 'selcon' && $lRegcont)
		{
			print '<input type="submit" class="btn btn-success btn-flat" name="approved" value="'.$langs->trans("Save and approve").'"></center>';
		}
		//print '</form>';
	}
	//boton para modificar
	if ($user->rights->poa->comp->mod && $action != 'editc')
	{
		print '<a class="btn btn-primary" href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&idrc='.$objpcon->id.'&modal=fichecommitted&action=editc">'.$langs->trans('Modify').'</a>';
	}
	print '</div>';

	//print '<div class="modal-footer">';
	//print '<center><input type="submit" class="btn btn-primary btn-flat" value="'.$langs->trans("Save").'">';
	//print '<input type="submit" class="btn btn-danger btn-flat" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
	//print '</div>';
	print '</form>';
}
else
{
	//si existe idrc
	$idrc = GETPOST('idrc');
	//buscamos el registro
	$objpcon->fetch($idrc);

}
print '</div>'; //modal-body
print '</div>'; //modal-content

print '</div>'; //modal-dialog
print '</div>'; //modal fichecontratl


?>
