<?php
/* Copyright (C) 2010-2012	Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2014	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2012-2013	Christophe Battarel	<christophe.battarel@altairis.fr>
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
//include_once(DOL_DOCUMENT_ROOT.'/purchase/tpl/framesfourn.tpl.php');

$lAddpurchase = true;
$idprodfournprice = GETPOST('idprodfournprice');
$partida = GETPOST('partida');
if (GETPOST('action') == 'refresh')
{
	$idprodfournprice = GETPOST('idprodfournprice');
	$fk_projet = GETPOST('fk_projet');
}
$type = GETPOST('type');
if (isset($_POST['type']))
{
	if ($type != -1)
	{
		if ($idprodfournprice <=0)
		{
			$_POST['prod_entry_mode'] = 'free';
			unset($idprodfournprice);
			unset($_POST['idprodfournprice']);
			unset($_GET['idprodfournprice']);
		}
	}
	//else
	//	$_POST['prod_entry_mode'] = 'free';
}
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
<!-- BEGIN PHP TEMPLATE objectline_create.tpl.php -->
<tr class="liste_titre nodrag nodrop">
	<td<?php echo (! empty($conf->global->MAIN_VIEW_LINE_NUMBER) ? ' colspan="2"' : ''); ?>>
	<div id="add"></div><span class="hideonsmartphone"><?php echo $langs->trans('AddNewLine'); ?></span><?php // echo $langs->trans("FreeZone"); ?>
</td>

<td align="left"><?php echo $langs->trans('Unit'); ?></td>
<td align="right"><?php echo $langs->trans('Qty'); ?></td>
<td align="right"><?php echo $langs->trans('Reference price'); ?></td>
<td align="right"><?php echo $langs->trans('Total'); ?></td>
<td colspan="<?php echo $colspan; ?>">&nbsp;</td>
</tr>

<tr <?php echo $bcnd[$var]; ?>>
	<?php
	if (! empty($conf->global->MAIN_VIEW_LINE_NUMBER)) {
		$coldisplay=2; }
		else {
			$coldisplay=0; }
			?>

			<td<?php echo (! empty($conf->global->MAIN_VIEW_LINE_NUMBER) ? ' colspan="2"' : ''); ?>>

			<?php

			$forceall=1;
		// We always force all type for free lines (module product or service means we use predefined product or service)
			if ($object->element == 'contrat')
			{
				if (empty($conf->product->enabled) && empty($conf->service->enabled) && empty($conf->global->CONTRACT_SUPPORT_PRODUCTS)) $forceall=-1;
			// With contract, by default, no choice at all, except if CONTRACT_SUPPORT_PRODUCTS is set
				else $forceall=0;
			}
	//if (!$conf->global->PURCHASE_INTEGRATED_POA)
	//{
	// Free line
			echo '<span>';
	// Show radio free line
			if ($forceall >= 0 && (! empty($conf->product->enabled) || ! empty($conf->service->enabled)))
			{
				echo '<label for="prod_entry_mode_free">';
				echo '<input type="radio" name="prod_entry_mode" id="prod_entry_mode_free" value="free"';
		//echo (GETPOST('prod_entry_mode')=='free' ? ' checked="true"' : ((empty($forceall) && (empty($conf->product->enabled) || empty($conf->service->enabled)))?' checked="true"':'') );
				echo (GETPOST('prod_entry_mode')=='free' ? ' checked="true"' : '');
				echo '> ';
		// Show type selector
				echo $langs->trans("FreeLineOfType");
				echo '</label>';
				echo ' ';
			}
			else
			{
				echo '<input type="hidden" id="prod_entry_mode_free" name="prod_entry_mode" value="free">';
		// Show type selector
				if ($forceall >= 0)
				{
					echo $langs->trans("FreeLineOfType");
					echo ' ';
				}
			}

			echo $form->select_type_of_lines_add(isset($type)?$type:-1,'type',1,1,$forceall);

			echo '</span>';
	//}
	// Predefined product/service
			if (! empty($conf->product->enabled) || ! empty($conf->service->enabled))
			{
				if ($forceall >= 0) echo '<br>';
				echo '<span>';
				echo '<label for="prod_entry_mode_predef">';
				echo '<input type="radio" name="prod_entry_mode" id="prod_entry_mode_predef" value="predef"'.(GETPOST('prod_entry_mode')=='predef'?' checked="true"':'').'> ';
				if (empty($senderissupplier))
				{
					if (! empty($conf->product->enabled) && empty($conf->service->enabled)) echo $langs->trans('PredefinedProductsToSell');
					else if (empty($conf->product->enabled) && ! empty($conf->service->enabled)) echo $langs->trans('PredefinedServicesToSell');
					else echo $langs->trans('PredefinedProductsAndServicesToSell');
				}
				else
				{
					if (! empty($conf->product->enabled) && empty($conf->service->enabled)) echo $langs->trans('PredefinedProductsToPurchase');
					else if (empty($conf->product->enabled) && ! empty($conf->service->enabled)) echo $langs->trans('PredefinedServicesToPurchase');
					else echo $langs->trans('PredefinedProductsAndServicesToPurchase');
				}
				echo '</label>';
				echo ' ';

				$filtertype='';
				if (! empty($object->element) && $object->element == 'contrat' && empty($conf->global->CONTRACT_SUPPORT_PRODUCTS)) $filtertype='1';

				if (empty($senderissupplier))
				{
			//$form->select_produits_v($idprod, 'idprod', $filtertype, $conf->product->limit_size, $buyer->price_level, 1, 2, '', 1, array(),$buyer->id);
					$form->select_produits_v($idprod, 'idprod', $filtertype, $conf->product->limit_size,$buyer->price_level,-1,2,'',1);
				}
				else
				{
					$ajaxoptions=array(
					'update' => array('qty'=>'qty','remise_percent' => 'discount'),	// html id tags that will be edited with which ajax json response key
					'option_disabled' => 'addPredefinedProductButton',	// html id to disable once select is done
					'warningx' => $langs->trans("NoPriceDefinedForThisSupplier") // translation of an error saved into var 'error'
				);
			//$form->select_produits_fournisseurs_c($object->socid, GETPOST('idprodfournprice'), 'idprodfournprice', '', '', $ajaxoptions, 1);
					$form->select_produits_v($idprodfournprice, 'idprodfournprice', $filtertype, $conf->product->limit_size,$buyer->price_level,-1,2,'',1);
				}
				echo '</span>';
			}
			$prod_entry_mode = GETPOST('prod_entry_mode');
			if (is_object($hookmanager) && empty($senderissupplier))
			{
				$parameters=array('fk_parent_line'=>GETPOST('fk_parent_line','int'));
				$reshook=$hookmanager->executeHooks('formCreateProductOptions',$parameters,$object,$action);
			}
			if (is_object($hookmanager) && ! empty($senderissupplier))
			{
				$parameters=array('htmlname'=>'addproduct');
				$reshook=$hookmanager->executeHooks('formCreateProductSupplierOptions',$parameters,$object,$action);
			}


			if (! empty($conf->product->enabled) || ! empty($conf->service->enabled)) echo '<br>';

	// Editor wysiwyg
			require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
			$nbrows=ROWS_2;
			$enabled=(! empty($conf->global->FCKEDITOR_ENABLE_DETAILS)?$conf->global->FCKEDITOR_ENABLE_DETAILS:0);
			if (! empty($conf->global->MAIN_INPUT_DESC_HEIGHT)) $nbrows=$conf->global->MAIN_INPUT_DESC_HEIGHT;
			$toolbarname='dolibarr_details';
			if (! empty($conf->global->FCKEDITOR_ENABLE_DETAILS_FULL)) $toolbarname='dolibarr_notes';
			$doleditor=new DolEditor('dp_desc',GETPOST('dp_desc'),'',100,$toolbarname,'',false,true,$enabled,$nbrows,'98%');
			$doleditor->Create();
			?>
		</td>

		<td>
			<?php print $form->selectUnits((GETPOST('fk_unit')?GETPOST('fk_unit'):$fk_unit),'fk_unit'); ?>
		</td>
		<td align="right"><input type="text" size="2" name="qty" id="qty" class="flat" value="<?php echo (GETPOST('qty')?GETPOST('qty'):1); ?>">
		</td>

		<td align="right"><input type="number" min="0" step="any" name="price" id="price" class="flat" value="<?php echo (GETPOST('price')?GETPOST('price'):0); ?>" <?php  if($conf->global->PURCHASE_INTEGRATED_POA) print 'required'; ?> />
		</td>

		<td></td>
		<td align="center" valign="middle" colspan="<?php echo $colspan; ?>">
			<input type="submit" class="button" value="<?php echo $langs->trans('Add'); ?>" name="addline" id="addline">
		</td>
		<?php
	// Lines for extrafield
		if (!empty($extrafieldsline)) {
			if ($this->table_element_line=='commandedet') {
				$newline = new OrderLine($this->db);
			}
			elseif ($this->table_element_line=='propaldet') {
				$newline = new PropaleLigne($this->db);
			}
			elseif ($this->table_element_line=='facturedet') {
				$newline = new FactureLigne($this->db);
			}
			if (is_object($newline)) {
				print $newline->showOptionals($extrafieldsline, 'edit', array('style'=>$bcnd[$var], 'colspan'=>$coldisplay+8));
			}
		}
		?>
	</tr>

	<?php
if ((! empty($conf->service->enabled) || ($object->element == 'contrat')) && $dateSelector && GETPOST('type') != '0')	// We show date field if required
{
	if(! empty($conf->global->MAIN_VIEW_LINE_NUMBER)) $colspan = 10;
	else $colspan = 9;
	if (! empty($inputalsopricewithtax)) $colspan++;	// We add 1 if col total ttc
	if (in_array($object->element,array('propal','facture','invoice','commande','order'))) $colspan++;	// With this, there is a column move button

	if (! empty($usemargins))
	{
		$colspan++; // For the buying price
		if (! empty($conf->global->DISPLAY_MARGIN_RATES)) $colspan++;
		if (! empty($conf->global->DISPLAY_MARK_RATES))   $colspan++;
	}
	?>

	<?php
}

//para proyectos
if ($conf->monprojet->enabled)
{
	print '<tr class="liste_titre nodrag nodrop">';
	print '<td>'.$langs->trans('Project').'</td>';
	print '<td colspan="4">'.$langs->trans('Tasks').'</td>';
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
	print '<td colspan="4">';
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
			$selected = '';
			if (GETPOST('fk_jobsdet') == $linej->id) $selected = ' selected';
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
		$fk_structure = 0;
		$type = GETPOST('type');
		if ($idprodfournprice>0)
			$res = $objPP->fetch($idprodfournprice);

		$optionsstr = '<option value="0">'.$langs->trans('SelectStructure').'</option>';
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
			if ($respoa>0)
			{
				foreach ($objPoa->lines AS $j => $linepoa)
				{
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
						if (GETPOST('fk_poa') == $campoid) $selected = ' selected';
						$optionspoa.= '<option value="'.$campoid.'" '.$selected.' title="'.$value.'">'.$value.'</option>';
					}
				}
			}

			if ($respoa>0 && $abc)
			{
				foreach ($objPoa->lines AS $j => $linepoa)
				{
					$selected = '';
					//para poa
					if (GETPOST('fk_poa') == $linepoa->id) $selected = ' selected';
					//para structure
					//if (GETPOST('fk_structure') == $linepoa->fk_structure) $selected = ' selected';
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
			$objObjetive = new Poaobjetiveext($this->db);
			$aStructure = array();
			$aPoa = array();
			$aPartida = array();
			$aDate = dol_getdate($object->datec);
		//$filterpartida = " AND t.gestion = ".$aDate['year'];
			$filterpartida = " AND t.gestion = ".$_SESSION['period_year'];
			$filterpartida.= $filtertype;
		//$filterpartida.= " AND t.partida = '".$objPP->code_partida."'";
			if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;
			$respoa = $objPoa->fetchAll('ASC','ref',0,0,array('entity'=>$conf->entity),'AND',$filterpartida);
			if ($respoa>0)
			{
				foreach ($objPoa->lines AS $j => $linepoa)
				{
					$selected = '';
					if ($fk_poa == $linepoa->id) $selected = ' selected';
					$objStr->fetch($linepoa->fk_structure);
					$aStructure[$linepoa->fk_structure] = $objStr->sigla.' - '.$objStr->label;
					//buscamos la operacion
					$respoa = $objObjetive->fetch($linepoa->fk_poa_objetive);
					$aOptionsobj[$objObjetive->sigla]['label'] = $objObjetive->label;
					$aOptionspoa[$objObjetive->sigla][$linepoa->id] = $objStr->sigla.' : '.dol_trunc($linepoa->label,100);

					if (GETPOST('fk_poa')== $linepoa->id)
					{
						$respartida = $objPartida->fetch(0,$linepoa->partida,$_SESSION['period_year']);
						$aPartida[$linepoa->partida] = $objPartida->code.' - '.dol_trunc($objPartida->label,40);
					}

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
						if (GETPOST('fk_poa') == $campoid) $selected = ' selected';
						$optionspoa.= '<option value="'.$campoid.'" '.$selected.' title="'.$value.'">'.$value.'</option>';
					}
				}
				//armamos el option para partida
				if(count($aPartida)>0)
				{
					if (count($aPartida) ==1) $optionspartida = '';
					foreach ($aPartida AS $fk => $label)
					{
						$selected = '';
						if ($partida == $fk) $selected = ' selected';
						$optionspartida.= '<option value="'.$fk.'" '.$selected.'>'.$label.'</option>';
					}
				}

			}

			if ($respoa>0 && $abc)
			{
				$objObjetive = new Poaobjetiveext($this->db);
				foreach ($objPoa->lines AS $j => $linepoa)
				{
					$fk_structure = $linepoa->fk_structure;
					$resstr = $objStr->fetch($linepoa->fk_structure);
					$aStructure[$linepoa->fk_structure] = $objStr->sigla.' - '.$objStr->label;
					//buscamos la operacion
					$respoa = $objObjetive->fetch($linepoa->fk_poa_objetive);
					$aPoa[$linepoa->id] = ($respoa?'Op.: '.$objObjetive->sigla:'').($resstr?' CatProg.: '.$objStr->sigla:'').' : '.$linepoa->label;
					//buscamos el nombre de la partida
					$respartida = $objPartida->fetch(0,$linepoa->partida,$_SESSION['period_year']);
					//con poa
					if (GETPOST('fk_poa')== $linepoa->id)
						$aPartida[$linepoa->partida] = $objPartida->code.' - '.dol_trunc($objPartida->label,40);

				//con structure
				//if (GETPOST('fk_structure')== $linepoa->fk_structure)
				//	$aPartida[$linepoa->partida] = $objPartida->code.' - '.dol_trunc($objPartida->label,40);
				//$optionspoa.= '<option value="'.$linepoa->fk_structure.'" '.$selected.'>'.$objStr->sigla.' - '.$objStr->label.'</option>';
				}
			//armamos el option para structure
				if(count($aStructure)>0)
				{
					foreach ($aStructure AS $fk => $label)
					{
						$selected = '';
						if (GETPOST('fk_structure') == $fk) $selected = ' selected';
						$optionsstr.= '<option value="'.$fk.'" '.$selected.'>'.$label.'</option>';
					}
				}
			//armamos el option para poa
				//if(count($aPoa)>0)
				//{
				//	foreach ($aPoa AS $fk => $label)
				//	{
				//		$selected = '';
				//		if (GETPOST('fk_poa') == $fk) $selected = ' selected';
				//		$optionspoa.= '<option value="'.$fk.'" '.$selected.' title="'.$label.'">'.dol_trunc($label,100).'</option>';
				//	}
				//}
				//armamos el option para partida
				if(count($aPartida)>0)
				{
					if (count($aPartida) ==1) $optionspartida = '';
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
			//print '<select id="fk_structure" name="fk_structure">'.$optionsstr.'</select>';
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
			if (GETPOST('fk_poa')>0)
			{
				$aReformulated = array();
					//buscamos todas las reformulaciones aproabdas
				$objReformulated = new Poareformulatedext($this->db);
				$objReformulateddet = new Poareformulateddetext($this->db);
				$filterref = " AND t.statut = 2";
				$filterref.= " AND t.ref > 0 ";
				$res = $objReformulated->fetchAll('','',0,0,array(1=>1),'AND',$filterref);
				if ($res >0)
				{
					$lines = $objReformulated->lines;
					foreach ($lines AS $j => $line)
					{
						$filterdet = " AND t.fk_poa_reformulated = ".$line->id;
						$filterdet.= " AND t.fk_poa_poa = ".GETPOST('fk_poa');
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



		//$aDate = dol_getdate($object->datec);
		//$filterpartida = " AND t.gestion = ".$aDate['year'];
		//$filterpartida = " AND t.gestion = ".$_SESSION['period_year'];
		//$filterpartida.= " AND t.partida = '".GETPOST('partida')."'";
		//$filterpartida.= " AND t.fk_structure = ".GETPOST('fk_structure');
		//if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;

		//$respoa = $objPoa->fetchAll('ASC','ref',0,0,array('entity'=>$conf->entity),'AND',$filterpartida,true);
				$respoa = $objPoa->fetch(GETPOST('fk_poa'));
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
					$filterpartida.= " AND t.fk_structure = ".$objPoa->fk_structure;
					//$filterpartida.= " AND t.fk_structure = ".GETPOST('fk_structure');

					//if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;
					$respre = $objPre->fetchAll('','',0,0,array('active'=>1,'statut'=>1,),'AND',$filterpartida);
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
			print '<input type="hidden" name="fk_structure" value="'.$fk_structure.'">';
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
	if ($prod_entry_mode == 'free')
	{
		?>
		jQuery(function() {
			jQuery("#search_idprodfournprice").hide();
			jQuery('#dp_desc').focus();
		});
		<?php
	}
	else
	{
		?>
		jQuery(function() {
			jQuery("#search_idprodfournprice").show();
			jQuery("#dp_desc").focus();
		});
		<?php

	}


	if (! empty($usemargins) && $user->rights->margins->creer)
	{
		?>

		/* Some js test when we click on button "Add" */
		jQuery(document).ready(function() {
			<?php
			if (! empty($conf->global->DISPLAY_MARGIN_RATES)) { ?>
				$('#addline').click(function (e) {
					return checkFreeLine(e, "np_marginRate");
				});
				$("input[name='np_marginRate']:first").blur(function(e) {
					return checkFreeLine(e, "np_marginRate");
				});
				<?php
			}
			if (! empty($conf->global->DISPLAY_MARK_RATES)) { ?>
				$('#addline').click(function (e) {
					return checkFreeLine(e, "np_markRate");
				});
				$("input[name='np_markRate']:first").blur(function(e) {
					return checkFreeLine(e, "np_markRate");
				});
				<?php
			}
			?>
		});

		/* TODO This does not work for number with thousand separator that is , */
		function checkFreeLine(e, npRate)
		{
			var buying_price = $("input[name='buying_price']:first");
			var remise = $("input[name='remise_percent']:first");

			var rate = $("input[name='"+npRate+"']:first");
			if (rate.val() == '')
				return true;

			if (! $.isNumeric(rate.val().replace(',','.')))
			{
				alert('<?php echo dol_escape_js($langs->trans("rateMustBeNumeric")); ?>');
				e.stopPropagation();
				setTimeout(function () { rate.focus() }, 50);
				return false;
			}
			if (npRate == "np_markRate" && rate.val() >= 100)
			{
				alert('<?php echo dol_escape_js($langs->trans("markRateShouldBeLesserThan100")); ?>');
				e.stopPropagation();
				setTimeout(function () { rate.focus() }, 50);
				return false;
			}

			var price = 0;
			remisejs=price2numjs(remise.val());

		if (remisejs != 100)	// If a discount not 100 or no discount
		{
			if (remisejs == '') remisejs=0;

			bpjs=price2numjs(buying_price.val());
			ratejs=price2numjs(rate.val());

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
		if (pos >= 0) decpart = amount.substr(pos+1).replace('/0+$/i','');	// Supprime les 0 de fin de partie decimale
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

/* JQuery for product free or predefined select */
jQuery(document).ready(function() {
	$("#prod_entry_mode_free").on( "click", function() {
		setforfree();
	});
	$("#select_type").change(function()
	{
		setforfree();
		if (jQuery('#select_type').val() >= 0) jQuery('#dp_desc').focus();
		if (jQuery('#select_type').val() == '0') jQuery('#trlinefordates').hide();
		else jQuery('#trlinefordates').show();
	});

	$("#prod_entry_mode_predef").on( "click", function() {
		setforpredef();
		jQuery('#trlinefordates').show();
	});
	$("#idprod, #idprodfournprice").change(function()
	{
		setforpredef();
		jQuery('#trlinefordates').show();

		<?php if (! empty($usemargins) && $user->rights->margins->creer) { ?>

			/* Code for margin */
			$("#fournprice_predef").find("option").remove();
			$("#fournprice_predef").hide();
			$("#buying_price").val("").show();
			$.post('<?php echo DOL_URL_ROOT; ?>/purchase/ajax/getPurchasePrices.php', { 'idprod': $(this).val() }, function(data) {
				if (data && data.length > 0)
				{
					var options = '';
					var i = 0;
					$(data).each(function() {
						i++;
						options += '<option value="'+this.id+'" price="'+this.price+'"';
						if (i == 1) {
							options += ' selected';
							$("#buying_price").val(this.price);
						}
						options += '>'+this.label+'</option>';
					});
					options += '<option value=""><?php echo $langs->trans("InputPrice"); ?></option>';
					$("#buying_price").hide();
					$("#fournprice_predef").html(options).show();
					$("#fournprice_predef").change(function() {
						var selval = $(this).find('option:selected').attr("price");
						if (selval)
							$("#buying_price").val(selval).hide();
						else
							$('#buying_price').show();
					});
				}
			},
			'json');

			<?php } ?>

			/* To set focus */
			if (jQuery('#idprod').val() > 0) jQuery('#dp_desc').focus();
			if (jQuery('#idprodfournprice').val() > 0) jQuery('#dp_desc').focus();
		});

	<?php if (GETPOST('prod_entry_mode') == 'predef') { // When we submit with a predef product and it fails we must start with predef ?>
		setforpredef();
		<?php } ?>

	});

/* Function to set fields from choice */
function setforfree() {
	jQuery("#search_idprod").val('');
	jQuery("#idprod").val('');
	jQuery("#search_idprodfournprice").hide();
	jQuery("#idprodfournprice").val('0');	// Set cursor on not selected product
	jQuery("#search_idprodfournprice").val('');
	jQuery("#prod_entry_mode_free").attr('checked',true);
	jQuery("#prod_entry_mode_predef").attr('checked',false);
	jQuery("#price_ht").show();
	jQuery("#price_ttc").show();	// May no exists
	jQuery("#tva_tx").show();
	jQuery("#buying_price").val('').show();
	jQuery("#fournprice_predef").hide();
	jQuery("#title_vat").show();
	jQuery("#title_up_ht").show();
	jQuery("#title_up_ttc").show();
	jQuery("#np_marginRate").show();	// May no exists
	jQuery("#np_markRate").show();	// May no exists
	jQuery(".np_marginRate").show();	// May no exists
	jQuery(".np_markRate").show();	// May no exists
}
function setforpredef() {
	jQuery("#select_type").val(-1);
	jQuery("#search_idprodfournprice").show();
	jQuery("#prod_entry_mode_free").attr('checked',false);
	jQuery("#prod_entry_mode_predef").attr('checked',true);
	jQuery("#price_ht").hide();
	jQuery("#price_ttc").hide();	// May no exists
	jQuery("#tva_tx").hide();
	jQuery("#buying_price").show();
	jQuery("#title_vat").hide();
	jQuery("#title_up_ht").hide();
	jQuery("#title_up_ttc").hide();
	jQuery("#np_marginRate").hide();	// May no exists
	jQuery("#np_markRate").hide();	// May no exists
	jQuery(".np_marginRate").hide();	// May no exists
	jQuery(".np_markRate").hide();	// May no exists
}

</script>

<!-- END PHP TEMPLATE objectline_create.tpl.php -->
