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

if ( ($action == 'createedit') )
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	//$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
	$tmparray['fk_socid'] = GETPOST('fk_socid');
	if (!empty($tmparray['fk_socid']))
	{
		$objpcon->fk_socid = $tmparray['fk_socid'];
	}
	$action='create';
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}
// print_r($_POST);
// exit;

/*
 * View
 */
$form=new Form($db);
$display ='none';
if (isset($modal) && $modal == 'fichecommitted')
{
	print '<script type="text/javascript">
	$(window).load(function(){
		$("#fichecontrat").modal("show");
	});
</script>';
}

print '<div id="fichecontrat" class="modal modal-success" tabindex="-1" role="dialog" style="display: '.$display.'; margin-top:0px;" data-width="760" aria-hidden="false">';
print '<div class="modal-dialog modal-lg">';
print '<div class="modal-content">';

print '<div class="modal-header">';
print '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
print '<h4 class="modal-title">'.$langs->trans("Committed").'</h4>';
print '</div>';

if ($user->rights->poa->comp->crear)
{
	print '<form id="InfroText" class="form-horizontal"  name="fiche_comp" action="'.$_SERVER['PHP_SELF'].'" method="post" />';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="ida" value="'.$ida.'">';
	print '<input type="hidden" name="modal" value="fichecommitted">';

	print '<div class="modal-body">';
	print '<div>';
	//div vacio
	dol_htmloutput_mesg($mesg);
	// societe
	print '<div class="form-group">';
	print '<label class="col-sm-3 control-label" for="socid">'.$langs->trans('Company').'</label>';
	$ajaxoptions = array('onChange="cargatodos(this.id)"');
	print '<div class="col-xs-8">';
	print $formadd->select_thirdpartyadd($selected, 'socid', '', 0, $ajaxoptions, 0);
	print '</div>';
	print '</div>';

	// contratos
	print '<div class="form-group">';
	print '<label class="col-sm-3 control-label" for="fk_contrat">'.$langs->trans('Newcontrat').'</label>';
	print '<div class="col-xs-8">';
	print '<div id="demoDer"><select disabled="disabled" name="fk_contrat" id="fk_contrat">
	<option value="0">Selecciona opci&oacute;n...</option></select></div>';
	print '</div>';
	print '</div>';

	//contratos vigentes
	print '<div class="form-group">';
	print '<label class="col-sm-3 control-label" for="fk_contrat">'.$langs->trans('Existingcontracts').'</label>';
	print '<div class="col-xs-8">';
	print '<div id="demoDer"><select disabled="disabled" name="fk_contrat_exist" id="fk_contrat_exist">
	<option value="0">Selecciona opci&oacute;n...</option></select></div>';
	print '</div>';
	print '</div>';

	print '</div>'; 
	//fin div vacio

	print '<div id="listapart" style="display:none;">';
	print '<div>';

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
	print '<div class="table-responsive">';
	print '<table class="table">';

	// partidas
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
			//echo $idc;
			// echo '<pre>';
			// print_r($objpp->array);
			// echo '</pre>';
			// echo '<hr>idc '.$idc;

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
					}
					else
					{
						print '<td align="right">'.$objpartdet->quant_adj.'</td>';
						print '<td align="right">'.number_format(price2num($objpartdet->amount,'MT'),2).'</td>';
					}
					print '</tr>';
				}
			}
		}
	}
	print '</tbody>';
	print '</table>';
	print '</div>';
	print '</div>';
	print '</div>';
	print '</div>';

	print '<div class="modal-footer">';
	if ($objpcon->statut == 0 || ($objpcon->statut > 0 && $idpa > 0) &&( $action == 'editc' || $action == 'editop'))
	{
		print '<center>';
		print '<input type="submit" class="btn btn-primary btn-flat" value="'.$langs->trans("Save").'">&nbsp;';
		print '<input type="submit" class="btn btn-danger btn-flat" name="cancel" value="'.$langs->trans("Cancel").'">&nbsp;';
		if ($action == 'selcon' && $lRegcont)
		{
			print '<input type="submit" class="btn btn-success btn-flat" name="approved" value="'.$langs->trans("Save and approve").'">';
		}
		print '</center>';
	}
	print '</div>';
	print '</form>';
}
else
{
	//si existe idrc
	$idrc = GETPOST('idrc');
	//buscamos el registro
	$objpcon->fetch($idrc);

}
print '</div>'; //modal-content
print '</div>'; //modal-dialog
//print '</div>'; //poa-modal
print '</div>'; //modal fichecontratl


?>
