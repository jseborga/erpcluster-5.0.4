<?php
/* Copyright (C) 2010-2012	Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2012	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2012		Christophe Battarel	<christophe.battarel@altairis.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * Need to have following variables defined:
 * $conf
 * $langs
 * $dateSelector
 * $this (invoice, order, ...)
 * $line defined
 */


//include_once(DOL_DOCUMENT_ROOT.'/almacen/tpl/frame.tpl.php');
//para busqueda y reeemplazo de unidades del producto

$usemargins=0;
$idprod = GETPOST('idprod');
if (! empty($conf->margin->enabled) && ! empty($object->element) && in_array($object->element,array('facture','propal','commande'))) $usemargins=1;


print '<!-- BEGIN PHP TEMPLATE predefinedproductline_create.tpl.php -->';

print '<tr class="liste_titre nodrag nodrop">';
print '<td '.(!empty($conf->global->MAIN_VIEW_LINE_NUMBER) ? ' colspan="4"' : ' colspan="3"').'>';
print  $langs->trans("AddNewLine");
print '</td>';

print '<td>'.$langs->trans('Unit').'</td>';

print '<td align="right">'.$langs->trans('Qty').'</td>';

$colspan = 4;
print '<td>&nbsp;</td>';
print '</tr>';

if (GETPOST('action') == 'refresh')
{
	$idprod = GETPOST('idprod');
	$fk_projet = GETPOST('fk_projet');
}
if (! empty($conf->use_javascript_ajax))
{
	if ($conf->global->ALMACEN_INTEGER_WITH_POA)
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			$("#fk_projet").change(function() {
				document.addpredefinedproduct.action.value="refresh";
				document.addpredefinedproduct.submit();
			});
			$("#fk_jobs").change(function() {
				document.addpredefinedproduct.action.value="refresh";
				document.addpredefinedproduct.submit();
			});
			$("#idprod").change(function() {
				document.addpredefinedproduct.action.value="refresh";
				document.addpredefinedproduct.submit();
			});
			$("#fk_structure").change(function() {
				document.addpredefinedproduct.action.value="refresh";
				document.addpredefinedproduct.submit();
			});
		});';
		print '</script>'."\n";
	}
}

print '<form name="addpredefinedproduct" id="addpredefinedproduct" action="'.$_SERVER["PHP_SELF"].'?id='.$this->id.'#add" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="addline">';
print '<input type="hidden" name="mode" value="predefined">';
print '<input type="hidden" name="id" value="'.$this->id.'">';

print '<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#idprod").change(function() {
		if (jQuery("#idprod").val() > 0) jQuery("#np_desc").focus();
	}); });
	</script>';

	print '<tr '.$bcnd[$var].'>';
	print '<td valign="top" '.(! empty($conf->global->MAIN_VIEW_LINE_NUMBER) ? ' colspan="4"' : ' colspan="3"').'>';

	print '<span>';

	$filtertype='';

	if (! empty($object->element) && $object->element == 'contrat') $filtertype='1';
	if (! empty($object->element) && $object->element == 'solalmacen') $filtertype='0';
	$form->select_produits_v($idprod,'idprod',$filtertype,$conf->product->limit_size,$buyer->price_level,-1,2,'',1);
			//($selected='', $htmlname='productid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$element='product',$fk_entrepot=0)
	print '</span>';

	if (is_object($hookmanager))
	{
		$parameters=array('fk_parent_line'=>GETPOST('fk_parent_line','int'));
		$reshook=$hookmanager->executeHooks('formCreateProductOptions',$parameters,$object,$action);
	}

	// Editor wysiwyg
	require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
	$nbrows=ROWS_2;
	$enabled=(! empty($conf->global->FCKEDITOR_ENABLE_DETAILS)?$conf->global->FCKEDITOR_ENABLE_DETAILS:0);
	if (! empty($conf->global->MAIN_INPUT_DESC_HEIGHT)) $nbrows=$conf->global->MAIN_INPUT_DESC_HEIGHT;
//$doleditor=new DolEditor('np_desc',GETPOST('np_desc'),'',60,'dolibarr_details','',false,true,$enabled,1,20);
//$doleditor->Create();
	print '<textarea name="np_desc" id="np_desc">'.GETPOST('np_desc').'</textarea>';
	print '</td>';

	print '<td>';
	print $form->selectUnits(GETPOST('fk_unit'),'fk_unit',1);
	print '</td>';

	print '<td align="right">';
	print '<input type="text" size="2" name="qty" value="'.(GETPOST('qty')?GETPOST('qty'):1).'">';
	print '</td>';
	$rowspan = 1;
	if ($conf->monprojet->enabled)
	{
		$rowspan++;
		$rowspan++;
	}
	if ($conf->mant->enabled)
	{
		$rowspan++;
		$rowspan++;
	}
	if ($conf->poa->enabled)
	{
		$rowspan++;
		$rowspan++;
	}

	print '<td align="center" valign="middle" rowspan="'.$rowspan.'">';
	print '<input type="submit" class="button" value="'.$langs->trans("Add").'" name="addline">';
	print '</td>';
	print '</tr>';

	if (! empty($conf->service->enabled) && $dateSelector)
	{
		if (! empty($conf->global->MAIN_VIEW_LINE_NUMBER)) $colspan = 10;
		else $colspan = 9;
		if (! empty($usemargins))
		{
		$colspan++; // For the buying price
		if (! empty($conf->global->DISPLAY_MARGIN_RATES)) $colspan++;
		if (! empty($conf->global->DISPLAY_MARK_RATES))   $colspan++;
	}
	print '<tr '.$bcnd[$var].'>';
	print '<td colspan="'.$colspan.'">';
	if (! empty($object->element) && $object->element == 'contrat')
	{
		print $langs->trans("DateStartPlanned").' ';
		$form->select_date('',"date_start",$usehm,$usehm,1,"addline");
		print ' &nbsp; '.$langs->trans("DateEndPlanned").' ';
		$form->select_date('',"date_end",$usehm,$usehm,1,"addline");
	}
	else
	{
		print $langs->trans('ServiceLimitedDuration').' '.$langs->trans('From').' ';
		print $form->select_date('','date_start_predef',$conf->global->MAIN_USE_HOURMIN_IN_DATE_RANGE,$conf->global->MAIN_USE_HOURMIN_IN_DATE_RANGE,1,"addpredefinedproduct");
		print ' '.$langs->trans('to').' ';
		print $form->select_date('','date_end_predef',$conf->global->MAIN_USE_HOURMIN_IN_DATE_RANGE,$conf->global->MAIN_USE_HOURMIN_IN_DATE_RANGE,1,"addpredefinedproduct");
	}
	print '</td>';
	print '</tr>';
}

//para proyectos
if ($conf->monprojet->enabled)
{
	print '<tr class="liste_titre nodrag nodrop">';
	print '<td>'.$langs->trans('Project').'</td>';
	print '<td>'.$langs->trans('Tasks').'</td>';
	print '</tr>';

	print '<tr>';
	if ($object->fk_projet) $fk_projet = $object->fk_projet;
	dol_include_once('/monprojet/class/html.formprojetext.class.php');
	$formproject = new FormProjetsext($this->db);
		//projet
	print '<td>';
	if ($object->fk_projet>0)
	{
		require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
		$objProjet = new Projectext($this->db);
		$objProjet->fetch($object->fk_projet);
		print $objProjet->getNomUrladd(1);
		print '<input type="hidden" name="fk_projet" value="'.$object->fk_projet.'">';
	}
	else
	{
		$filterkey = '';
		$numprojet = $formproject->select_projects(($user->societe_id>0?$soc->id:-1), $fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);
	}
	print '</td>';
	print '<td>';
	$numtask = $formproject->selectTasks_v(($user->societe_id>0?$soc->id:-1), '', 'fk_task', 24, 0, 1, 0, 0, 0,$fk_projet,1,0);
	print '</td>';
	print '</tr>';
}
//para mantenimiento
if ($conf->mant->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsprogram.class.php';
	$langs->load('mant');
	$objJobs = new Mjobsext($this->db);
	$objJobsprog = new Mjobsprogram($this->db);

	$filterjob = " AND status IN (2,3,4)";
	$filterjob.= " AND entity = ".$conf->entity;
	$resjob = $objJobs->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterjob);
	$options = '<option value="0">'.$langs->trans('Select').'</option>';
	if ($resjob > 0)
	{
		$linesj = $objJobs->lines;
		foreach ($linesj AS $j => $linej)
		{
			$selected = '';
			if (GETPOST('fk_jobs') == $linej->id) $selected = ' selected';
			$options.= '<option value="'.$linej->id.'" '.$selected.'>'.$linej->ref.' '.$linej->label.'</option>';
		}
	}
	print '<tr class="nodrag nodrop">';
	print '<td>'.$langs->trans('Workorder').'</td>';
	print '<td>';
	print '<select id="fk_jobs" name="fk_jobs">'.$options.'</select>';
	print '</td>';
	$filterjobp = " AND t.fk_jobs =".GETPOST('fk_jobs');
	$resjobp = $objJobsprog->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterjobp);
	$options = '<option value="0">'.$langs->trans('Selectprogram').'</option>';
	if ($resjobp > 0)
	{
		$linesj = $objJobsprog->lines;
		foreach ($linesj AS $j => $linej)
		{
			$options.= '<option value="'.$linej->id.'">'.$linej->ref.' '.$linej->description.'</option>';
		}
	}
	print '<td>'.$langs->trans('Taskprogramming').'</td>';
	print '<td colspan="2">';
	print '<select name="fk_jobsdet">'.$options.'</select>';
	print '</td>';
	print '</tr>';
}
//para presupuesto poa
if ($conf->poa->enabled && $conf->global->ALMACEN_INTEGER_WITH_POA)
{
	require_once DOL_DOCUMENT_ROOT.'/poa/class/poareformulatedext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/poa/class/poastructureext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/poa/class/poapoaext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/poa/class/poapartidapreext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/cpartidaext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/partidaproduct.class.php';
	$objPoareformulated = new Poareformulatedext($this->db);
	$objStr 	= new Poastructure($this->db);
	$objPoa 	= new Poapoaext($this->db);
	$objPP   	= new Partidaproduct($this->db);
	$objPre 	= new Poapartidapre($this->db);
	$objPartida 	= new Cpartida($this->db);
	$filterreform = " AND t.active = 1 AND t.statut = 2";
	$resref = $objPoareformulated->fetchAll('ASC','t.ref',0,0,array(1=>1),'AND',$filterreform);
	if ($resref==0)
	{
		$lAddpurchase = false;
	}
	elseif($resref>0)
	{
		$linesref = $objPoareformulated->lines;
		foreach ((array)$linesref AS $j => $lineref)
		{
			$version = $lineref->ref;
		}
	}
	else
	{
		setEventMessages($langs->trans('Error en poa reformulación'),null,'errors');
		exit;
	}
	$langs->load('Poa');
	$res = $objPP->fetch($idprod);
	$optionspoa = '<option value="0">'.$langs->trans('Selectpoa').'</option>';
	if ($res > 0)
	{
		$aDate = dol_getdate($object->date_creation);
		$filterpartida = " AND t.gestion = ".$aDate['year'];
		$filterpartida.= " AND t.partida = '".$objPP->code_partida."'";
		if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;
		$respoa = $objPoa->fetchAll('ASC','ref',0,0,array('entity'=>$conf->entity),'AND',$filterpartida);
		if ($respoa>0)
		{
			foreach ($objPoa->lines AS $j => $linepoa)
			{
				$selected = '';
				if (GETPOST('fk_structure') == $linepoa->fk_structure) $selected = ' selected';
				$objStr->fetch($linepoa->fk_structure);
				$optionspoa.= '<option value="'.$linepoa->fk_structure.'" '.$selected.'>'.$objStr->sigla.' - '.$objStr->label.'</option>';
			}
		}
	}
	print '<tr class="liste_titre nodrag nodrop">';
	print '<td>'.$langs->trans('Catprog').'</td>';
	print '<td>'.$langs->trans('Partida').'</td>';
	print '<td>'.$langs->trans('Aprobado').'</td>';
	print '<td>'.$langs->trans('Preventivo').'</td>';
	print '<td>'.$langs->trans('Saldo').'</td>';
	print '</tr>';
	print '<tr>';
	print '<td>';
	print '<select id="fk_structure" name="fk_structure">'.$optionspoa.'</select>';
	print '</td>';
	print '<td>';
	print $objPP->code_partida;
	print '</td>';
	print '<td>';
	//mostramos el presupuesto
	$amount = 0;
	$amountpre = 0;

	if (GETPOST('fk_structure')>0)
	{
		$aDate = dol_getdate($object->date_creation);
		$filterpartida = " AND t.gestion = ".$aDate['year'];
		$filterpartida.= " AND t.partida = '".$objPP->code_partida."'";
		echo $filterpartida.= " AND t.fk_structure = ".GETPOST('fk_structure');
		if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;
		$respoa = $objPoa->fetchAll('ASC','ref',0,0,array('entity'=>$conf->entity),'AND',$filterpartida,true);
		if ($respoa==1)
		{
			$amount = $objPoa->amount;
			//buscamos cuantos preventivos ya fueron emitidos
			$filterpartida = " AND t.fk_poa = ".$objPoa->id;
			$filterpartida.= " AND t.partida = '".$objPP->code_partida."'";
			$filterpartida.= " AND t.fk_structure = ".GETPOST('fk_structure');
			//if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;
			$respre = $objPre->fetchAll('ASC','ref',0,0,array('statut'=>1),'AND',$filterpartida);
			if ($respre>0)
			{
				foreach ($objPre->lines AS $j => $linepre)
				{
					$amountpre+= $linepre->amount;
				}
			}
		}
	}
	$balance = $amount - $amountpre;
	print price($amount);
	print '</td>';
	print '<td>';
	print price($amountpre);
	print '</td>';
	print '<td>';
	print price($balance);
	print '</td>';
	print '</tr>';
}

print '</form>';

?>
