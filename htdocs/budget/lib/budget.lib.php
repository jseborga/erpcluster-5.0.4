<?php

function select_typeunit($selected='',$htmlname='fk_type',$htmloption='',$showempty=0,$showlabel=0,$campo='rowid')
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code, f.libelle FROM ".MAIN_DB_PREFIX."c_type_unit AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.libelle";
	$resql = $db->query($sql);
	$html = '';

	if ($selected <> 0 && $selected == '-1')
	{
		if ($showlabel > 0)
		{
			return $langs->trans('To be defined');
		}
	}

	if ($resql)
	{
		$html.= '<select class="flat" name="'.$htmlname.'">';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}
		if ($selected <> 0 && $selected == '-1')
		{
			$html.= '<option value="-1" selected="selected">'.$langs->trans('To be defined').'</option>';
			if ($showlabel)
			{
				return $langs->trans('To be defined');
			}
		}
		if (empty($selected) && $showlabel)
			return '';
      // else
      // 	$html.= '<option value="-1">'.$langs->trans('To be defined').'</option>';

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->$campo)
				{
					$html.= '<option value="'.$obj->code.'" selected="selected">'.$obj->libelle.'</option>';
					if ($showlabel)
					{
						return $obj->libelle;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->code.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		$html.= '</select>';
		if ($showlabel)
			return $langs->trans('to be defined');
		return $html;
	}
}

function select_reshuman($selected='',$htmlname='product',$htmloption='',$showempty=0,$campoid='rowid')
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_resources_human AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($resql)
		$html = htmlselect($resql,$selected,$htmlname,$htmloption,$showempty,$campoid);
	return $html;
}

function fetchAll_parameter_equipment($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
{
	global $db, $langs, $conf;
	$sql = 'SELECT';
	$sql .= ' t.rowid,';

	$sql .= " t.code,";
	$sql .= " t.label,";
	$sql .= " t.active";

	$sql .= ' FROM ' . MAIN_DB_PREFIX . 'c_parameter_equipment'. ' as t';

		// Manage filter
	$sqlwhere = array();
	if (count($filter) > 0) {
		foreach ($filter as $key => $value) {
			$sqlwhere [] = $key . ' LIKE \'%' . $db->escape($value) . '%\'';
		}
	}
	if (count($sqlwhere) > 0) {
		$sql .= ' WHERE ' . implode(' '.$filtermode.' ', $sqlwhere);
	}

	if ($filterstatic) $sql.= $filterstatic;

	if (!empty($sortfield)) {
		$sql .= $db->order($sortfield,$sortorder);
	}
	if (!empty($limit)) {
		$sql .=  ' ' . $db->plimit($limit + 1, $offset);
	}
	$lines = array();

	$resql = $db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql);

		while ($obj = $db->fetch_object($resql)) {
			$line = new stdClass();

			$line->id = $obj->rowid;
			$line->code = $obj->code;
			$line->label = $obj->label;
			$line->active = $obj->active;

			if ($lView)
			{
				if ($num == 1) return $line;
			}

			$lines[$line->id] = $line;
		}
		$db->free($resql);

		return $lines;
	} else {
		return - 1;
	}
}


function select_cunit($selected='',$htmlname='fk_unit',$htmloption='',$showempty=0,$campoid='rowid',$clabel="label")
{
	global $db, $langs, $conf;
	if ($clabel == 'label')
		$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_units AS f ";
	else
		$sql = "SELECT f.rowid, f.code, f.short_label AS libelle FROM ".MAIN_DB_PREFIX."c_units AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($resql)
	{
		$html = htmlselect($resql,$selected,$htmlname,$htmloption,$showempty,$campoid);
	}
	return $html;
}

/**
 *	\file       htdocs/mant/lib/mant.lib.php
 *	\ingroup    Librerias
 *	\brief      Page fiche mantenimiento
 */

function select_type_deduction($selected='',$htmlname='type_group',$htmloption='',$showempty=0,$campoid='rowid')
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code AS code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_deductions AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($resql)
		$html = htmlselect($resql,$selected,$htmlname,$htmloption,$showempty,$campoid);
	return $html;
}

/*
 campos obligatorios de la tabla
 rowid
 code
 libelle
*/
 function htmlselect($resql,$selected='',$htmlname='type_group',$htmloption='',$showempty=0,&$campoid='rowid')
 {
 	global $langs,$db,$conf;
 	$html.= '<select class="flat" name="'.$htmlname.'" id="select'.$htmlname.'">';
 	if ($showempty)
 		$html.= '<option value="0">&nbsp;</option>';
 	$num = $db->num_rows($resql);
 	$i = 0;
 	if ($num)
 	{
 		while ($i < $num)
 		{
 			$obj = $db->fetch_object($resql);
 			if (!empty($selected) && $selected == $obj->$campoid)
 			{
 				$html.= '<option value="'.$obj->$campoid.'" selected="selected">'.$obj->libelle.'</option>';
 			}
 			else
 			{
 				$html.= '<option value="'.$obj->$campoid.'">'.$langs->trans($obj->libelle);
 				//if (!empty($obj->code) && $campoid == 'rowid')
 				//	$html.= ' ('.$obj->code.')';
 				$html.= '</option>';

 			}
 			$i++;
 		}
 	}
 	$html.= '</select>';
 	return $html;

 }

//prepare_head budget item
/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @param	User	$user		Object user
 * @return  array				Array of tabs to show
 */
function incidents_prepare_head($object, $user)
{
	global $langs, $conf;
	$langs->load("budget@budget");

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT."/budget/incidents/card.php?id=".$object->id;
	$head[$h][1] = $langs->trans('Card');
	$head[$h][2] = 'card';
	$h++;
	if ($object->code_parameter == 'BENESOC')
	{
		//inactivity
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/inactivity.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Inactivity');
		$head[$h][2] = 'inactivity';
		$h++;

	//benefits
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/benefits.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Benefits');
		$head[$h][2] = 'benefits';
		$h++;
		//subsidies
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/subsidies.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Subsidies');
		$head[$h][2] = 'subsidies';
		$h++;
		//contributions
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/contribution.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Contribution');
		$head[$h][2] = 'contribution';
		$h++;
		//antiquity
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/antiquity.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Antiquity');
		$head[$h][2] = 'antiquity';
		$h++;
		//occupational
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/occupational.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Occupational');
		$head[$h][2] = 'occupational';
		$h++;
	}
	if ($object->code_parameter == 'HERMEN')
	{
		//inactivity
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/minortools.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Minortools');
		$head[$h][2] = 'minortools';
		$h++;
	}
	if ($object->code_parameter == 'COSTMO')
	{
		//costo horario directo mano de obra
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/costmo.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Laborcostdirecthours');
		$head[$h][2] = 'costmo';
		$h++;
	}
	if ($object->code_parameter == 'GASGEN')
	{
		//lictaciones
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/copiesplane.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Copiesofplane');
		$head[$h][2] = 'copiesplane';
		$h++;
		//preparacion de propuesta
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/propossal.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Preparationofproposal');
		$head[$h][2] = 'propossal';
		$h++;
		//documentos legales
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/legaldoc.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Legaldocuments');
		$head[$h][2] = 'legaldoc';
		$h++;
		//garantias
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/guarantees.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Guaranteesandinsurance');
		$head[$h][2] = 'guarantees';
		$h++;
		//operation office
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/operation.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Operationoffice');
		$head[$h][2] = 'operation';
		$h++;
		//administrative of work
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/administrative.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Administrativeexpensesofwork');
		$head[$h][2] = 'administrative';
		$h++;
		//movilization
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/mobilization.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Mobilizationexpenses');
		$head[$h][2] = 'mobilization';
		$h++;
		//traffic
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/traffic.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Maintenanceandtrafficcosts');
		$head[$h][2] = 'traffic';
		$h++;
		//risk
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/risk.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Riskmanagementcost');
		$head[$h][2] = 'risk';
		$h++;
		//faenas
		$head[$h][0] = DOL_URL_ROOT."/budget/incidents/faenas.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Expensesinstallationofauxiliaryfaenas');
		$head[$h][2] = 'faenas';
		$h++;
	}

	return $head;
}

//prepare_head budget item
/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @param	User	$user		Object user
 * @return  array				Array of tabs to show
 */
function budgetincidents_prepare_head($object, $user)
{
	global $langs, $conf;
	$langs->load("budget@budget");

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id;
	$head[$h][1] = $langs->trans('Card');
	$head[$h][2] = 'card';
	$h++;
	if ($object->code_parameter == 'BENESOC')
	{
		//inactivity
		$type='inactivity';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Inactivity');
		$head[$h][2] = 'inactivity';
		$h++;

	//benefits
		$type='benefits';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Benefits');
		$head[$h][2] = 'benefits';
		$h++;
		//subsidies
		$type='subsidies';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Subsidies');
		$head[$h][2] = 'subsidies';
		$h++;
		//contributions
		$type='contribution';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Contribution');
		$head[$h][2] = 'contribution';
		$h++;
		//antiquity
		$type='antiquity';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Antiquity');
		$head[$h][2] = 'antiquity';
		$h++;
		//occupational
		$type='occupational';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Occupational');
		$head[$h][2] = 'occupational';
		$h++;
	}
	if ($object->code_parameter == 'HERMEN')
	{
		//minortools
		$type='minortools';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Minortools');
		$head[$h][2] = 'minortools';
		$h++;
	}
	if ($object->code_parameter == 'COSTMO')
	{
		//costo horario directo mano de obra
		$type='costmo';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Laborcostdirecthours');
		$head[$h][2] = 'costmo';
		$h++;
	}
	if ($object->code_parameter == 'GASGEN')
	{
		//lictaciones
		$type='copiesplane';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Copiesofplane');
		$head[$h][2] = 'copiesplane';
		$h++;
		//preparacion de propuesta
		$type='propossal';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Preparationofproposal');
		$head[$h][2] = 'propossal';
		$h++;
		//documentos legales
		$type='legaldoc';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Legaldocuments');
		$head[$h][2] = 'legaldoc';
		$h++;
		//garantias
		$type='guarantees';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Guaranteesandinsurance');
		$head[$h][2] = 'guarantees';
		$h++;
		//operation office
		$type='operation';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Operationoffice');
		$head[$h][2] = 'operation';
		$h++;
		//administrative of work
		$type='administrative';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Administrativeexpensesofwork');
		$head[$h][2] = 'administrative';
		$h++;
		//movilization
		$type='mobilization';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Mobilizationexpenses');
		$head[$h][2] = 'mobilization';
		$h++;
		//traffic
		$type='traffic';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Maintenanceandtrafficcosts');
		$head[$h][2] = 'traffic';
		$h++;
		//risk
		$type='risk';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Riskmanagementcost');
		$head[$h][2] = 'risk';
		$h++;
		//faenas
		$type='faenas';
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->fk_budget.'&idr='.$object->id.'&type='.$type;
		$head[$h][1] = $langs->trans('Expensesinstallationofauxiliaryfaenas');
		$head[$h][2] = 'faenas';
		$h++;
	}

	return $head;
}
//prepare_head budget item
/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @param	User	$user		Object user
 * @return  array				Array of tabs to show
 */
function budgetitem_prepare_head($object, $user)
{
	global $langs, $conf;
	$langs->load("budget@budget");

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT."/budget/items/card.php?id=".$object->id;
	$head[$h][1] = $langs->trans('Card');
	$head[$h][2] = 'card';
	$h++;
	if (!$object->type)
	{
	//productos del item
		$head[$h][0] = DOL_URL_ROOT."/budget/items/supplies.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Supplies');
		$head[$h][2] = 'supplies';
		$h++;

		//productions items product
		if(!$object->manual_performance)
		{
			$head[$h][0] = DOL_URL_ROOT."/budget/items/production.php?id=".$object->id;
			$head[$h][1] = $langs->trans('Performanceequipment');
			$head[$h][2] = 'production';
			$h++;
		}
		//document items
		$head[$h][0] = DOL_URL_ROOT."/budget/items/document.php?id=".$object->id;
		$head[$h][1] = $langs->trans('Documents');
		$head[$h][2] = 'documents';
		$h++;

	}
	return $head;
}


//prepare_head productbudget
/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @param	User	$user		Object user
 * @return  array				Array of tabs to show
 */
function productbudget_prepare_head($object, $user,$action='')
{
	global $langs, $conf;
	$langs->load("budget@budget");

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT."/budget/budget/task.php?id=".$object->fk_budget.'&idr='.$object->id.($action?'&action='.$action:'');
	$head[$h][1] = $langs->trans('Card');
	$head[$h][2] = 'card';
	$h++;

	//productos del item
	$head[$h][0] = DOL_URL_ROOT."/budget/budget/supplies.php?id=".$object->fk_budget.'&idr='.$object->id.($action?'&action='.$action:'');
	$head[$h][1] = $langs->trans('Supplies');
	$head[$h][2] = 'supplies';
	$h++;

	//productions items product
	$head[$h][0] = DOL_URL_ROOT."/budget/budget/production.php?id=".$object->fk_budget.'&idr='.$object->id.($action?'&action='.$action:'');
	$head[$h][1] = $langs->trans('Performanceequipment');
	$head[$h][2] = 'production';
	$h++;


	return $head;
}



//prepare_head productbudget
/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @param	User	$user		Object user
 * @return  array				Array of tabs to show
 */
function budgettask_prepare_head($object, $user,$action='')
{
	global $langs, $conf;
	$langs->load("budget@budget");

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT."/budget/budget/fiche.php?id=".$object->id;
	$head[$h][1] = $langs->trans('Card');
	$head[$h][2] = 'card';
	$h++;

	//productos del item
	$head[$h][0] = DOL_URL_ROOT."/budget/budget/supplies.php?id=".$object->id;
	$head[$h][1] = $langs->trans('Supplies');
	$head[$h][2] = 'supplies';
	$h++;

	//productions items product
	$head[$h][0] = DOL_URL_ROOT."/budget/budget/production.php?id=".$object->id;
	$head[$h][1] = $langs->trans('Performanceequipment');
	$head[$h][2] = 'production';
	$h++;


	return $head;
}


//prepare_head budget
/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @param	User	$user		Object user
 * @return  array				Array of tabs to show
 */
function budget_budget_prepare_head_bck($object, $user, array $array = array())
{
	global $langs, $conf;
	$langs->load("budget@budget");

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT."/budget/budget/modules.php?id=".$object->fk_budget.'&action=viewgr';
	$head[$h][1] = $object->ref;
	$head[$h][2] = 'bud';
	$h++;

	foreach ((array) $array AS $j => $data)
	{
		$fklnk = $data['fklnk'];
		$fk = $data['fk'];
		$fklabel = $data['fklabel'];
		$action = $data['action'];
		$head1 = $data['head1'];
		$head2 = $data['head2'];
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/card.php?id=".$object->id.'&'.$fklnk.'='.$fk.($action?'&action='.$action:'');
		$head[$h][1] = $head1;
		$head[$h][2] = $head2;
		$h++;
	}

	//contactos de la tarea
	$head[$h][0] = DOL_URL_ROOT."/budget/budget/task/contact.php?id=".$object->id.'&'.$fklnk.'='.$fk.'&action=viewit';
	$head[$h][1] = $langs->trans('Contact');
	$head[$h][2] = 'contact';
	$h++;


	return $head;
}

function budget_task_prepare_head($object, $user)
{
	global $langs, $conf,$db;
	$langs->load("budget@budget");
	require_once DOL_DOCUMENT_ROOT.'/budget/class/budget.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettask.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskadd.class.php';
	$obj    = new Budget($db);
	$objtmp = new Budgettask($db);
	$objtmpadd = new Budgettaskadd($db);
	//buscamos
	$objtmp->fetch($object->id);
	$objtmpadd->fetch(0,$object->id);
	$act = 'viewit';
	$lnk = 'idr';
	if ($objtmpadd->c_grupo)
	{
		$lnk = 'idg';
		//$act = 'viewgr';
	}
	$h = 100;
	$head = array();

	//contactos de la tarea
	$head[$h][0] = DOL_URL_ROOT."/budget/budget/task/contact.php?id=".$object->id.'&'.$fklnk.'='.$fk.'&action=viewit';
	$head[$h][1] = $langs->trans('Contact');
	$head[$h][2] = 'contact';
	$h--;
	//link actual
	$head[$h][0] = DOL_URL_ROOT."/budget/budget/modules.php?id=".$object->fk_budget.'&'.$lnk.'='.$object->id.'&action='.$act;
	$head[$h][1] = $object->label;
	$head[$h][2] = 'b'.$object->id;
	$h--;
	$lView = true;
	$fk_task_parent = $object->fk_task_parent;
	if ($fk_task_parent>0)
	{
		while ($lView== true)
		{
			$objtmp->fetch($fk_task_parent);
			//buscamos
			$objtmpadd->fetch(0,$objtmp->id);
			$act = 'viewit';
			$lnk = 'idr';
			if ($objtmpadd->c_grupo == 1)
			{
				$lnk = 'idg';
			//$act = 'viewgr';
			}
			$head[$h][0] = DOL_URL_ROOT."/budget/budget/modules.php?id=".$object->fk_budget.'&'.$lnk.'='.$objtmp->id.'&action='.$act;
			$head[$h][1] = $objtmp->label;
			$head[$h][2] = 'b'.$objtmp->id;
			$h--;
			if ($objtmp->fk_task_parent>0) $fk_task_parent = $objtmp->fk_task_parent;
			else $lView = false;
		}
	}
	$h--;
	$obj->fetch($objtmp->fk_budget);
	$head[$h][0] = DOL_URL_ROOT."/budget/budget/modules.php?id=".$obj->id.'&action=viewgr';
	$head[$h][1] = $obj->ref;
	$head[$h][2] = 'bud';
	ksort($head);
	//armamos nuevamente para ordenar
	$headnew = array();
	$h=0;
	foreach ($head AS $j => $data)
	{
		$headnew[$h] = $data;
		$h++;
	}


	return $headnew;
}
/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @param	User	$user		Object user
 * @return  array				Array of tabs to show
 */
function budget_prepare_head($object, $user)
{
	global $langs, $conf;
	$langs->load("budget@budget");

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT."/budget/budget/card.php?id=".$object->id;
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;
	if ($user->rights->budget->bud->read)
	{
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/incidents.php?id=".$object->id;
		$head[$h][1] = $langs->trans("Incidents");
		$head[$h][2] = 'incidents';
		$h++;
	}
	if ($user->rights->budget->bud->read)
	{
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/parameters.php?id=".$object->id;
		$head[$h][1] = $langs->trans("Parameters");
		$head[$h][2] = 'parameters';
		$h++;
	}
	if ($user->rights->budget->budi->leer)
	{
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/resources.php?id=".$object->id;
		$head[$h][1] = $langs->trans("Resources");
		$head[$h][2] = 'resources';
		$h++;
	}
	if ($user->rights->budget->budi->leer)
	{
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/modules.php?id=".$object->id.'&action=viewgr';
		$head[$h][1] = $langs->trans("Modules");
		$head[$h][2] = 'modules';
		$h++;
	}
	if ($user->rights->budget->budi->com)
	{
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/compare.php?id=".$object->id;
		$head[$h][1] = $langs->trans("Compare");
		$head[$h][2] = 'compare';
		$h++;
	}
	if ($user->rights->budget->budi->leer)
	{
		$head[$h][0] = DOL_URL_ROOT."/budget/budget/summary.php?id=".$object->id;
		$head[$h][1] = $langs->trans("Summary");
		$head[$h][2] = 'summary';
		$h++;
	}
	$head[$h][0] = DOL_URL_ROOT."/budget/budget/calendar.php?id=".$object->id;
	$head[$h][1] = $langs->trans("Calendar");
	$head[$h][2] = 'calendar';
	$h++;

	$head[$h][0] = DOL_URL_ROOT."/budget/budget/contact.php?id=".$object->id;
	$head[$h][1] = $langs->trans("Contact");
	$head[$h][2] = 'Contact';
	$h++;

	// More tabs from canvas
	// TODO Is this still used ?
	if (isset($object->onglets) && is_array($object->onglets))
	{
		foreach ($object->onglets as $onglet)
		{
			$head[$h] = $onglet;
			$h++;
		}
	}

	complete_head_from_modules($conf,$langs,$object,$head,$h,'budget', 'remove');

	return $head;
}


/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @param	User	$user		Object user
 * @return  array				Array of tabs to show
 */
function budget_prepare_head_revisar($object, $user)
{
	global $langs, $conf;
	$langs->load("budget@budget");

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT."/budget/items/items.php?id=".$object->id.'&subaction=mat';
	$head[$h][1] = $langs->trans("Materials");
	$head[$h][2] = 'mat';
	$h++;

	$head[$h][0] = DOL_URL_ROOT."/budget/items/items.php?id=".$object->id.'&subaction=mo';
	$head[$h][1] = $langs->trans("Workforce");
	$head[$h][2] = 'mo';
	$h++;

	$head[$h][0] = DOL_URL_ROOT."/budget/items/items.php?id=".$object->id.'&subaction=me';
	$head[$h][1] = $langs->trans("Machineryandequipment");
	$head[$h][2] = 'me';
	$h++;

	// More tabs from canvas
	// TODO Is this still used ?
	if (isset($object->onglets) && is_array($object->onglets))
	{
		foreach ($object->onglets as $onglet)
		{
			$head[$h] = $onglet;
			$h++;
		}
	}

	complete_head_from_modules($conf,$langs,$object,$head,$h,'product', 'remove');

	return $head;
}
/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @param	User	$user		Object user
 * @return  array				Array of tabs to show
 */
function budget_group_prepare_head($object,$aGroup,$user)
{
	global $langs, $conf;
	$langs->load("budget@budget");

	$h = 0;
	$head = array();
	foreach ((array) $aGroup AS $fk => $label)
	{
		$head[$h][0] = $_SERVER['PHP_SELF']."?id=".$object->id.'&idg='.$fk.'&action=viewit';
		$head[$h][1] = $langs->trans($label);
		$head[$h][2] = 'g'.$fk;
		$h++;
	}
	return $head;
}

/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @param	User	$user		Object user
 * @return  array				Array of tabs to show
 */
function budget_task_prepare_headx($object,$aGroup,$user)
{
	global $langs, $conf;
	$langs->load("budget@budget");

	$h = 0;
	$head = array();
	foreach ((array) $aGroup AS $fk => $label)
	{
		$head[$h][0] = $_SERVER['PHP_SELF']."?id=".$object->id.'&idg='.$fk.'&action=viewit';
		$head[$h][1] = $langs->trans($label);
		$head[$h][2] = 'g'.$fk;
		$h++;
	}
	return $head;
}


//la funcion crea en session la estructura del presupuesto
//variable $id = rowid de budget
function get_structure_budget($id)
{
	global $db;

	require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetgeneral.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructureext.class.php';

	$general = new Budgetgeneral($db);
	$budget = new Budgetext($db);
	$pustr = new Pustructureext($db);

	$aStrtmp=array('MO'=>'MO','MQ'=>'MQ','MA'=>'MA');
	require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
	$categorie = new Categorie($db);
	//parametros generales
	$general->fetch(0,$id);
	$budget->fetch($id);
	//armamos la estructura a utilizar
	$filter = array(1=>1);
	$filterstatic = " AND t.type_structure = '".$budget->type_structure."'";
	$filterstatic.= " AND t.fk_categorie > 0";
		//$filterstatic.= " AND t.ordby = 1";
	$res = $pustr->fetchAll('ASC', 'ordby', 0, 0, $filter, 'AND',$filterstatic,false);

	foreach((array) $pustr->lines AS $i => $linestr)
	{
		$categorie->fetch($linestr->fk_categorie);
		$aStrid[$linestr->id] = $linestr->id;
		$aStridcat[$linestr->id] = $linestr->fk_categorie;
		$aStrcatid[$linestr->fk_categorie] = $linestr->id;
		$aStrcatcode[$linestr->fk_categorie] = $linestr->ref;
		$aStr[$linestr->ref] = $linestr->ref;
		$aStrref[$linestr->ref] = $linestr->detail;
		$aStrlabel[$linestr->fk_categorie] = $linestr->detail;
		$aStrgroupcat[$linestr->group_structure] = $linestr->fk_categorie;
		$aStrcatgroup[$linestr->fk_categorie] = $linestr->group_structure;
		$aStrcatcolor[$linestr->fk_categorie] = $categorie->color;
	}

	$_SESSION['strLines'] = serialize($pustr->lines);
	$_SESSION['aStrbudget'] = serialize(array($id=>array('aStrid'=>$aStrid,'aStridcat'=>$aStridcat,'aStrcatid'=>$aStrcatid,'aStr'=>$aStr,'aStrref'=>$aStrref,'aStrlabel'=>$aStrlabel,'aStrcatcode'=>$aStrcatcode,'aStrgroupcat'=> $aStrgroupcat, 'aStrcatgroup'=> $aStrcatgroup,'aStrcatcolor'=>$aStrcatcolor)));
	return $res;
}

/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @param	User	$user		Object user
 * @return  array				Array of tabs to show
 */
function budgetproductasset_prepare_head($object, $user)
{
	global $langs, $conf;
	$langs->load("budget@budget");

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT."/budget/productasset/card.php?id=".$object->id.'&tab=default';
	$head[$h][1] = $langs->trans("Calculateddata");
	$head[$h][2] = 'default';
	$h++;

	$head[$h][0] = DOL_URL_ROOT."/budget/productasset/card.php?id=".$object->id.'&tab=technical';
	$head[$h][1] = $langs->trans("Datatechnical");
	$head[$h][2] = 'technical';
	$h++;

	$head[$h][0] = DOL_URL_ROOT."/budget/productasset/card.php?id=".$object->id.'&tab=factor';
	$head[$h][1] = $langs->trans("Consumptionfactor");
	$head[$h][2] = 'factor';
	$h++;

	$head[$h][0] = DOL_URL_ROOT."/budget/productasset/card.php?id=".$object->id.'&tab=cost';
	$head[$h][1] = $langs->trans("Cost");
	$head[$h][2] = 'cost';
	$h++;


	//complete_head_from_modules($conf,$langs,$object,$head,$h,'budgetproductasset', 'remove');

	return $head;
}

function fetchAll_type_engine($db,$sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
{
	global $conf,$langs;
	$sql = " SELECT t.rowid, t.entity, t.code, t.label, t.active ";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_type_engine AS t ";

			// Manage filter
	$sqlwhere = array();
	if (count($filter) > 0) {
		foreach ($filter as $key => $value) {
			$sqlwhere [] = $key . ' LIKE \'%' . $db->escape($value) . '%\'';
		}
	}
	$sql.= ' WHERE 1 = 1';
	if (! empty($conf->multicompany->enabled)) {
		$sql .= " AND entity IN (" . getEntity("ctypeengine", 1) . ")";
	}
	if (count($sqlwhere) > 0) {
		$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
	}
	if ($filterstatic){
		$sql.= $filterstatic;
	}
	if (!empty($sortfield)) {
		$sql .= $db->order($sortfield,$sortorder);
	}
	if (!empty($limit)) {
		$sql .=  ' ' . $db->plimit($limit + 1, $offset);
	}

	$lines = array();

	$resql = $db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql);

		while ($obj = $db->fetch_object($resql)) {
			$line = new stdClass();

			$line->id = $obj->rowid;

			$line->entity = $obj->entity;
			$line->code = $obj->code;
			$line->label = $obj->label;
			$line->active = $obj->active;
			$lines[$line->id] = $line;
		}
		$db->free($resql);

		return $lines;
	} else {
		$errors[] = 'Error ' . $db->lasterror();
		dol_syslog(__METHOD__ . ' ' . implode(',', $errors), LOG_ERR);
		return - 1;
	}
	return array();
}
?>