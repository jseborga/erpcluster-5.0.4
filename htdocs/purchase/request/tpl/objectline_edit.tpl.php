<?php
/* Copyright (C) 2010-2012	Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2012	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2012		Christophe Battarel	<christophe.battarel@altairis.fr>
 * Copyright (C) 2013		Florian Henry		<florian.henry@open-concept.pro>
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
 * Need to have following variables defined:
 * $object (invoice, order, ...)
 * $conf
 * $langs
 * $dateSelector
 * $forceall (0 by default, 1 for supplier invoices/orders)
 * $senderissupplier (0 by default, 1 for supplier invoices/orders)
 * $inputalsopricewithtax (0 by default, 1 to also show column with unit price including tax)
 */

//include_once(DOL_DOCUMENT_ROOT.'/purchase/tpl/frames.tpl.php');

$lAddpurchase = true;
$usemargins=0;
if (! empty($conf->margin->enabled) && ! empty($object->element) && in_array($object->element,array('facture','propal','commande'))) $usemargins=1;

global $forceall, $senderissupplier, $inputalsopricewithtax;
if (empty($dateSelector)) $dateSelector=0;
if (empty($forceall)) $forceall=0;
if (empty($senderissupplier)) $senderissupplier=0;
if (empty($inputalsopricewithtax)) $inputalsopricewithtax=0;


// Define colspan for button Add
$colspan = 3;	// Col total ht + col edit + col delete
if (! empty($inputalsopricewithtax)) $colspan++;	// We add 1 if col total ttc
if (in_array($object->element,array('propal','facture','invoice','commande','order'))) $colspan++;	// With this, there is a column move button
?>

<!-- BEGIN PHP TEMPLATE objectline_edit.tpl.php -->

<?php
$coldisplay=-1; // We remove first td
?>
<tr <?php echo $bc[$var]; ?>>
	<td<?php echo (! empty($conf->global->MAIN_VIEW_LINE_NUMBER) ? ' colspan="2"' : ''); ?>><?php $coldisplay+=(! empty($conf->global->MAIN_VIEW_LINE_NUMBER))?2:1; ?>
	<div id="line_<?php echo $line->id; ?>"></div>

	<input type="hidden" name="lineid" value="<?php echo $line->id; ?>">
	<input type="hidden" id="product_type" name="type" value="<?php echo $line->product_type; ?>">
	<input type="hidden" id="product_id" name="productid" value="<?php echo (! empty($line->fk_product)?$line->fk_product:0); ?>" />
	<input type="hidden" id="special_code" name="special_code" value="<?php echo $line->special_code; ?>">

	<?php if ($line->fk_product > 0) { ?>

	<a href="<?php echo DOL_URL_ROOT.'/product/card.php?id='.$line->fk_product; ?>">
		<?php
		if ($line->product_type==1) echo img_object($langs->trans('ShowService'),'service');
		else print img_object($langs->trans('ShowProduct'),'product');
		echo ' '.$line->product_ref;
		?>
	</a>
	<?php
	echo ' - '.nl2br($line->product_label);
	?>

	<br>

	<?php }	?>

	<?php
	if (is_object($hookmanager))
	{
		$fk_parent_line = (GETPOST('fk_parent_line') ? GETPOST('fk_parent_line') : $line->fk_parent_line);
		$parameters=array('line'=>$line,'fk_parent_line'=>$fk_parent_line,'var'=>$var,'dateSelector'=>$dateSelector,'seller'=>$seller,'buyer'=>$buyer);
		$reshook=$hookmanager->executeHooks('formEditProductOptions',$parameters,$this,$action);
	}
	// editeur wysiwyg
	require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
	$nbrows=ROWS_2;
	if (! empty($conf->global->MAIN_INPUT_DESC_HEIGHT)) $nbrows=$conf->global->MAIN_INPUT_DESC_HEIGHT;
	$enable=(isset($conf->global->FCKEDITOR_ENABLE_DETAILS)?$conf->global->FCKEDITOR_ENABLE_DETAILS:0);
	$toolbarname='dolibarr_details';
	if (! empty($conf->global->FCKEDITOR_ENABLE_DETAILS_FULL)) $toolbarname='dolibarr_notes';
	$doleditor=new DolEditor('product_desc',$line->description,'',164,$toolbarname,'',false,true,$enable,$nbrows,'98%');
	$doleditor->Create();
	?>
</td>


<td align="right"><?php $coldisplay++; ?>
	<?php if (($line->info_bits & 2) != 2) {
		// I comment this because it shows info even when not required
		// for example always visible on invoice but must be visible only if stock module on and stock decrease option is on invoice validation and status is not validated
		// must also not be output for most entities (proposal, intervention, ...)
		//if($line->qty > $line->stock) print img_picto($langs->trans("StockTooLow"),"warning", 'style="vertical-align: bottom;"')." ";
		if ($user->societe->id > 0)
		{
			print $line->qty;
			print '<input type="hidden" name="qty" id="qty" value="'.$line->qty.'">';
		}
		else
		{
			print '<input size="3" type="text" class="flat" name="qty" id="qty" value="'.$line->qty.'">';
		}
	} else { ?>
	&nbsp;
	<?php } ?>
</td>
<?php if ($conf->global->PRODUCT_USE_UNITS) { ?>
<td align="right" nowrap><?php $coldisplay++; ?>
	<?php print $form->selectUnits($line->fk_unit,'fk_unit'); ?>
</td>
<?php } ?>

</td>
<?php if (!$conf->global->PRICE_TAXES_INCLUDED) { ?>
<td align="right"><?php $coldisplay++; ?><input type="number" min="0.00001" step="any" class="flat len80" size="8" id="price_ht" name="price_ht" value="<?php echo price2num($line->subprice,'MS'); ?>"></td>
<?php } ?>
<?php if ($inputalsopricewithtax || $conf->global->PRICE_TAXES_INCLUDED) { ?>
<td align="right"><?php $coldisplay++; ?><input type="number"  min="<?php ($conf->global->PURCHASE_INTEGRATED_POA?'0.00001':'0') ?>" step="any" class="flat len80" size="8" id="price_ttc" name="price_ttc" value="<?php echo price2num($line->pu_ttc,'MS'); ?>"></td>
<?php } ?>





<!-- colspan=4 for this td because it replace total_ht+3 td for buttons -->
<td align="center" colspan="<?php echo $colspan; ?>" valign="middle"><?php $coldisplay+=4; ?>
	<input type="submit" class="button" id="savelinebutton" name="save" value="<?php echo $langs->trans("Save"); ?>"><br>
	<input type="submit" class="button" id="cancellinebutton" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>">
</td>

<?php
	//Line extrafield
if (!empty($extrafieldsline)) {
	print $line->showOptionals($extrafieldsline,'edit',array('style'=>$bc[$var],'colspan'=>$coldisplay));
}
?>
</tr>

<?php if (! empty($conf->service->enabled) && $line->product_type == 1 && $dateSelector && $abc)	 { ?>
<tr id="service_duration_area" <?php echo $bc[$var]; ?>>
	<td colspan="11"><?php echo $langs->trans('ServiceLimitedDuration').' '.$langs->trans('From').' '; ?>
		<?php
		$hourmin=(isset($conf->global->MAIN_USE_HOURMIN_IN_DATE_RANGE)?$conf->global->MAIN_USE_HOURMIN_IN_DATE_RANGE:'');
		echo $form->select_date($line->date_start,'date_start',$hourmin,$hourmin,$line->date_start?0:1,"updateligne");
		echo ' '.$langs->trans('to').' ';
		echo $form->select_date($line->date_end,'date_end',$hourmin,$hourmin,$line->date_end?0:1,"updateligne");
		?>
	</td>
</tr>
<?php } ?>
<?php

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
	$numtask = $formproject->selectTasks_v(($user->societe_id>0?$soc->id:-1), $line->fk_projet_task, 'fk_task', 24, 0, 1, 0, 0, 0,$fk_projet,1,0);
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
	$fk_jobs = GETPOST('fk_jobs')?GETPOST('fk_jobs'):$line->fk_jobs;
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
			if ($fk_jobs == $linej->id) $selected = ' selected';
			$options.= '<option value="'.$linej->id.'" '.$selected.'>'.$linej->ref.' '.$linej->label.'</option>';
		}
	}
	print '<tr class="nodrag nodrop">';
	print '<td>'.$langs->trans('Workorder').'</td>';
	print '<td>';
	print '<select id="fk_jobs" name="fk_jobs">'.$options.'</select>';
	print '</td>';
	$filterjobp = " AND t.fk_jobs =".$fk_jobs;
	$resjobp = $objJobsprog->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterjobp);
	$options = '<option value="0">'.$langs->trans('Selectprogram').'</option>';
	$fk_jobsdet = GETPOST('fk_jobsdet')?GETPOST('fk_jobsdet'):$line->fk_jobsdet;
	if ($resjobp > 0)
	{
		$linesj = $objJobsprog->lines;
		foreach ($linesj AS $j => $linej)
		{
			$selected = '';
			if ($fk_jobsdet == $linej->id) $selected = ' selected';
			$options.= '<option value="'.$linej->id.'" '.$selected.'>'.$linej->ref.' '.$linej->description.'</option>';
		}
	}
	print '<td>'.$langs->trans('Taskprogramming').'</td>';
	print '<td colspan="2">';
	print '<select name="fk_jobsdet">'.$options.'</select>';
	print '</td>';
	print '</tr>';
}
//para presupuesto poa
if ($conf->poa->enabled)
{
	if ($conf->global->PURCHASE_INTEGRATED_POA)
	{
		require_once DOL_DOCUMENT_ROOT.'/poa/class/poareformulatedext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/poa/class/poareformulateddetext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/poa/class/poastructureext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/poa/class/poapoaext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/poa/class/poapartidapreext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/orgman/class/cpartidaext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/orgman/class/partidaproduct.class.php';
		$objPoareformulated = new Poareformulatedext($this->db);
		$objStr 		= new Poastructure($this->db);
		$objPoa 	= new Poapoaext($this->db);
		$objPP   	= new Partidaproduct($this->db);
		$objPre 	= new Poapartidapre($this->db);
		$objPartida 	= new Cpartida($this->db);
		//verficamos que formulaciones estan activas para saldos o poder seguir cargando datos
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
		$res = 0;
		$lPartida = false;
		$fk_structure = GETPOST('fk_structure')?GETPOST('fk_structure'):$line->fk_structure;
		$fk_poa = GETPOST('fk_poa')?GETPOST('fk_poa'):$line->fk_poa;
		$partida = GETPOST('partida')?GETPOST('partida'):$line->partida;
		$type = GETPOST('type')?GETPOST('type'):$line->product_type;
		if ($line->fk_product>0)
		{
			$res = $objPP->fetch($line->fk_product);
			$prod_entry_mode = 'predef';
			if ($res<=0)
			{
				setEventMessages($langs->trans('No esta definido la partida presupuestaria'),null,'errors');
			}

		}
		else
			$prod_entry_mode = 'free';
		$optionsstr = '<option value="0">'.$langs->trans('Selectstructure').'</option>';
		$optionspoa = '<option value="0">'.$langs->trans('Select').'</option>';
		$optionspartida = '<option value="0">'.$langs->trans('Selectpartida').'</option>';
		$filtertype = '';
		if ($prod_entry_mode == 'free')
		{
			if ($type == 1)
			{
				$aTypetmp = explode(',',$conf->global->POA_SERVICE_TYPE_CODEPARTIDA);
				foreach ((array) $aTypetmp AS $j => $value)
				{
					if (!empty($filtertype)) $filtertype.= ' OR ';
					$len = STRLEN($value);
					$filtertype.= " SUBSTR(t.partida,1,".$len.") = '".$value."'";
				}
			}
			else
			{
				$aTypetmp = explode(',',$conf->global->POA_ASSET_TYPE_CODEPARTIDA);
				foreach ((array) $aTypetmp AS $j => $value)
				{
					if (!empty($filtertype)) $filtertype.= ' OR ';
					$len = STRLEN($value);
					$filtertype.= " SUBSTR(t.partida,1,".$len.") = '".$value."'";
				}
			}
			$filtertype = " AND (".$filtertype.")";
		}

		if ($res > 0)
		{
			$objObjetive = new Poaobjetiveext($this->db);
			$lPartida = true;
			$aDate = dol_getdate($object->datec);
			$filterpartida = " AND t.gestion = ".$aDate['year'];
			$filterpartida.= " AND t.partida = '".$objPP->code_partida."'";
			$filterpartida.= $filtertype;
			if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;

			$respoa = $objPoa->fetchAll('ASC','ref',0,0,array('entity'=>$conf->entity),'AND',$filterpartida);
			$aOptionspoa=array();
			if ($respoa>0)
			{
				foreach ($objPoa->lines AS $j => $linepoa)
				{
					$selected = '';
					if ($fk_poa == $linepoa->id) $selected = ' selected';
					$objStr->fetch($linepoa->fk_structure);
					//buscamos la operacion
					$respoa = $objObjetive->fetch($linepoa->fk_poa_objetive);
					$aOptionsobj[$objObjetive->sigla]['label'] = $objObjetive->label;
					//$aOptionspoa[$objObjetive->sigla]['id'] = $linepoa->id;
					$aOptionspoa[$objObjetive->sigla][$linepoa->id] = $objStr->sigla.' : '.dol_trunc($linepoa->label,100);
				}
			}

			if ($respoa>0)
			{
				foreach ($aOptionspoa AS $sigla => $data)
				{
					$optionspoa.='<optgroup label="'.$sigla.' '.$aOptionsobj[$sigla]['label'].'">';
					foreach ($data AS $campoid => $value)
					{
						$selected = '';
						if ($fk_poa == $campoid) $selected = ' selected';
						$optionspoa.= '<option value="'.$campoid.'" '.$selected.' title="'.$value.'">'.$value.'</option>';
					}
				}
			}

			if ($respoa>0 && $abc)
			{
				foreach ($objPoa->lines AS $j => $linepoa)
				{
					$selected = '';
					if ($fk_poa == $linepoa->id) $selected = ' selected';
					$objStr->fetch($linepoa->fk_structure);
					//buscamos la operacion
					$respoa = $objObjetive->fetch($linepoa->fk_poa_objetive);
					$optionspoa.= '<option value="'.$linepoa->id.'" '.$selected.' title="'.$linepoa->label.'">'.' Op.: '. ($respoa?$objObjetive->sigla:'').' CatProg: '.$objStr->sigla.' : '.dol_trunc($linepoa->label,100).'</option>';

				}
			}
		}
		elseif ($res < 0)
		{
		//error fatal
		}
		else
		{
			$aStructure = array();
			$aPartida = array();
			$aDate = dol_getdate($object->datec);
			$filterpartida = " AND t.gestion = ".$_SESSION['period_year'];
			$filterpartida.= $filtertype;
		//$filterpartida.= " AND t.partida = '".$objPP->code_partida."'";
			if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;
			//echo '<hr>filter '.$filterpartida;
			$respoa = $objPoa->fetchAll('ASC','ref',0,0,array('entity'=>$conf->entity),'AND',$filterpartida);

			if ($respoa>0)
			{
				$objObjetive = new Poaobjetiveext($this->db);
				foreach ($objPoa->lines AS $j => $linepoa)
				{
					$objStr->fetch($linepoa->fk_structure);
					$resstr = $aStructure[$linepoa->fk_structure] = $objStr->sigla.' - '.$objStr->label;
				//buscamos la operacion
					$respoa = $objObjetive->fetch($linepoa->fk_poa_objetive);
					$aPoa[$linepoa->id] = ($respoa?'Op.: '.$objObjetive->sigla:'').($resstr?' CatProg.: '.$objStr->sigla:'').' : '.$linepoa->label;

				//buscamos el nombre de la partida
					$objPartida->fetch(0,$linepoa->partida,$_SESSION['period_year']);
					//echo '<hr>'.$fk_poa.' '.$linepoa->id;
					if ($fk_poa== $linepoa->id)
						$aPartida[$linepoa->partida] = $objPartida->code.' - '.dol_trunc($objPartida->label,40);
				//$optionspoa.= '<option value="'.$linepoa->fk_structure.'" '.$selected.'>'.$objStr->sigla.' - '.$objStr->label.'</option>';
				}
			//armamos el option para structure
				if(count($aStructure)>0)
				{
					foreach ($aStructure AS $fk => $label)
					{
						$selected = '';
						if ($fk_structure == $fk) $selected = ' selected';
						$optionstr.= '<option value="'.$fk.'" '.$selected.'>'.$label.'</option>';
					}
				}
			//armamos el option para poa
				if(count($aPoa)>0)
				{
					foreach ($aPoa AS $fk => $label)
					{
						$selected = '';
						if ($fk_poa == $fk) $selected = ' selected';
						$optionspoa.= '<option value="'.$fk.'" '.$selected.' title="'.$label.'">'.dol_trunc($label,100).'</option>';
					}
				}
			//armamos el option para partida
				if(count($aPartida)>0)
				{
					foreach ($aPartida AS $fk => $label)
					{
						$selected = '';
						if ($partida == $fk) $selected = ' selected';
						$optionspartida.= '<option value="'.$fk.'" '.$selected.'>'.$label.'</option>';
					}
				}
			}
		}
		if ($lAddpurchase)
		{
			print '<tr class="liste_titre nodrag nodrop">';
			print '<td>'.$langs->trans('Catprog').'</td>';
			print '<td>'.$langs->trans('Partida').'</td>';
			print '<td>'.$langs->trans('Aprobado').'</td>';
			print '<td>'.$langs->trans('Preventivo').'</td>';
			print '<td>'.$langs->trans('Saldo').'</td>';
			print '</tr>';
			print '<tr>';
			print '<td>';
			print '<select id="fk_poa" name="fk_poa">'.$optionspoa.'</select>';
			print '</td>';
			print '<td>';
			if ($lPartida)
			{
				print $objPP->code_partida;
				print '<input type="hidden" name="partida" value="'.$objPP->code_partida.'">';
			}
			else
			{
				print '<select id="partida" name="partida">'.$optionspartida.'</select>';
			}
			print '</td>';
			print '<td>';
			//mostramos el presupuesto
			$amount = 0;
			$amountpre = 0;
			//$fk_poa = 0;

			if ($fk_poa>0)
			{

				$aReformulated = array();
					//buscamos todas las reformulaciones aproabdas
				$objReformulated = new Poareformulatedext($this->db);
				$objReformulateddet = new Poareformulateddetext($this->db);
				$filterref = " AND t.statut = 2";
				$filterref.= " AND t.ref > 0 ";
				$resref = $objReformulated->fetchAll('','',0,0,array(1=>1),'AND',$filterref);
				if ($resref >0)
				{
					$lines = $objReformulated->lines;
					foreach ($lines AS $j => $line)
					{
						$filterdet = " AND t.fk_poa_reformulated = ".$line->id;
						$filterdet.= " AND t.fk_poa_poa = ".$fk_poa;
						$res = $objReformulateddet->fetchAll('','',0,0,array(1=>1),'AND',$filterdet);
						if ($res >0)
						{
							foreach ($objReformulateddet->lines AS $k => $lined)
							{
								$aReformulated[$lined->fk_poa_poa]+= $lined->amount;
							}
						}
					}
				}
				$aDate = dol_getdate($object->datec);
				$filterpartida = " AND t.gestion = ".$aDate['year'];
				$filterpartida.= " AND t.partida = '".$partida."'";
				$filterpartida.= " AND t.fk_structure = ".$fk_structure;
				if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;

				//$respoa = $objPoa->fetchAll('ASC','ref',0,0,array('entity'=>$conf->entity),'AND',$filterpartida,true);
				$respoa = $objPoa->fetch($fk_poa);
				if ($respoa==1)
				{
					$fk_structure = $objPoa->fk_structure;
					$fk_poa = $objPoa->id;
					if ($objPoa->version >0)
						$amount = $objPoa->amount;
					else
						$amount = $objPoa->amount+$aReformulated[$objPoa->id];

					//buscamos cuantos preventivos ya fueron emitidos
					$filterpartida = " AND t.fk_poa = ".$objPoa->id;
					$filterpartida.= " AND t.partida = '".$partida."'";
					$filterpartida.= " AND t.fk_structure = ".$fk_structure;
					//if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;
					$respre = $objPre->fetchAll('','',0,0,array('statut'=>1),'AND',$filterpartida);
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
			print '<input type="hidden" name="fk_structure" value="'.($fk_structure?$fk_structure:$line->fk_structure).'">';
			print '</td>';
			print '<td>';
			print price($amountpre);
			print '</td>';
			print '<td>';
			print price($balance);
			print '</td>';
			print '</tr>';
		}
		else
		{
			setEventMessages($langs->trans('No se tiene activado la formulación o reformulación en la presente gestión'),null,'warnings');
		}
	}
}

?>

<script type="text/javascript">

	<?php
	if (! empty($conf->margin->enabled))
	{
		?>
		jQuery(document).ready(function()
		{
			/* Add rule to clear margin when we change price_ht or buying_price, so when we change sell or buy price, margin will be recalculated after submitting form */
			jQuery("#price_ht").keyup(function() {
				jQuery("input[name='np_marginRate']:first").val('');
				jQuery("input[name='np_markRate']:first").val('');
			});
			jQuery("#buying_price").keyup(function() {
				jQuery("input[name='np_marginRate']:first").val('');
				jQuery("input[name='np_markRate']:first").val('');
			});

			/* Init field buying_price and fournprice */
			$.post('<?php echo DOL_URL_ROOT; ?>/fourn/ajax/getSupplierPrices.php', {'idprod': <?php echo $line->fk_product?$line->fk_product:0; ?>}, function(data) {
				if (data && data.length > 0) {
					var options = '';
					var trouve=false;
					$(data).each(function() {
						options += '<option value="'+this.id+'" price="'+this.price+'"';
						<?php if ($line->fk_fournprice > 0) { ?>
							if (this.id == <?php echo $line->fk_fournprice; ?>) {
								options += ' selected';
								$("#buying_price").val(this.price);
								trouve = true;
							}
							<?php } ?>
							options += '>'+this.label+'</option>';
						});
					options += '<option value=null'+(trouve?'':' selected')+'><?php echo $langs->trans("InputPrice"); ?></option>';
					$("#fournprice").html(options);
					if (trouve) {
						$("#buying_price").hide();
						$("#fournprice").show();
					} else {
						$("#buying_price").show();
					}
					$("#fournprice").change(function() {
						var selval = $(this).find('option:selected').attr("price");
						if (selval)
							$("#buying_price").val(selval).hide();
						else
							$('#buying_price').show();
					});
				} else {
					$("#fournprice").hide();
					$('#buying_price').show();
				}
			}, 'json');

			/* Add rules to reset price_ht from margin info */
			<?php
			if (! empty($conf->global->DISPLAY_MARGIN_RATES))
			{
				?>
				$('#savelinebutton').click(function (e) {
					return checkEditLine(e, "np_marginRate");
				});
				$("input[name='np_marginRate']:first").blur(function(e) {
					return checkEditLine(e, "np_marginRate");
				});
				<?php
			}
			if (! empty($conf->global->DISPLAY_MARK_RATES))
			{
				?>
				$('#savelinebutton').click(function (e) {
					return checkEditLine(e, "np_markRate");
				});
				$("input[name='np_markRate']:first").blur(function(e) {
					return checkEditLine(e, "np_markRate");
				});
				<?php
			}
			?>
		});


		/* If margin rate field empty, do nothing. */
		/* Force content of price_ht to 0 or if a discount is set recalculate it from margin rate */
		function checkEditLine(e, npRate)
		{
			var buying_price = $("input[name='buying_price']:first");
			var remise = $("input[name='remise_percent']:first");

			var rate = $("input[name='"+npRate+"']:first");
			if (rate.val() == '' || (typeof rate.val()) == 'undefined' ) return true;

			if (! $.isNumeric(rate.val().replace(',','.')))
			{
				alert('<?php echo $langs->trans("rateMustBeNumeric"); ?>');
				e.stopPropagation();
				setTimeout(function () { rate.focus() }, 50);
				return false;
			}
			if (npRate == "np_markRate" && rate.val() >= 100)
			{
				alert('<?php echo $langs->trans("markRateShouldBeLesserThan100"); ?>');
				e.stopPropagation();
				setTimeout(function () { rate.focus() }, 50);
				return false;
			}

			var price = 0;
			remisejs=price2numjs(remise.val());

			if (remisejs != 100)
			{
				bpjs=price2numjs(buying_price.val());
				ratejs=price2numjs(rate.val());

				/* console.log(npRate+" - "+bpjs+" - "+ratejs); */

				if (npRate == "np_marginRate")
					price = ((bpjs * (1 + ratejs / 100)) / (1 - remisejs / 100));
				else if (npRate == "np_markRate")
					price = ((bpjs / (1 - ratejs / 100)) / (1 - remisejs / 100));
			}
		$("input[name='price_ht']:first").val(price);	// TODO Must use a function like php price to have here a formated value

		return true;
	}

	/* Function similar to price2num in PHP */
	function price2numjs(num)
	{
		if (num == '') return '';

		<?php
		$dec=','; $thousand=' ';
		if ($langs->transnoentitiesnoconv("SeparatorDecimal") != "SeparatorDecimal")  $dec=$langs->transnoentitiesnoconv("SeparatorDecimal");
		if ($langs->transnoentitiesnoconv("SeparatorThousand")!= "SeparatorThousand") $thousand=$langs->transnoentitiesnoconv("SeparatorThousand");
		print "var dec='".$dec."'; var thousand='".$thousand."';\n";	// Set var in javascript
		?>

		var main_max_dec_shown = <?php echo $conf->global->MAIN_MAX_DECIMALS_SHOWN; ?>;
		var main_rounding_unit = <?php echo $conf->global->MAIN_MAX_DECIMALS_UNIT; ?>;
		var main_rounding_tot = <?php echo $conf->global->MAIN_MAX_DECIMALS_TOT; ?>;

		var amount = num.toString();

		// rounding for unit price
		var rounding = main_rounding_unit;
		var pos = amount.indexOf(dec);
		var decpart = '';
		if (pos >= 0) decpart = amount.substr(pos+1).replace('/0+$/i','');	// Remove 0 for decimal part
		var nbdec = decpart.length;
		if (nbdec > rounding) rounding = nbdec;
		// If rounding higher than max shown
		if (rounding > main_max_dec_shown) rounding = main_max_dec_shown;

		if (thousand != ',' && thousand != '.') amount=amount.replace(',','.');
		amount=amount.replace(' ','');			// To avoid spaces
		amount=amount.replace(thousand,'');		// Replace of thousand before replace of dec to avoid pb if thousand is .
		amount=amount.replace(dec,'.');

		return parseFloat(amount).toFixed(rounding);
	}

	<?php
}
?>

</script>
<!-- END PHP TEMPLATE objectline_edit.tpl.php -->
