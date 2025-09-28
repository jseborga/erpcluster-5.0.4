<?php
//grouptask.tpl.php
$params.='&id='.$object->id;
$params.='&action='.$action;

if ($action == 'viewcalendar')
{
	$sql1 = "select fk_budget_task FROM ".MAIN_DB_PREFIX."budget_task_add WHERE c_grupo = 1";
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_budget = ".$object->id;
	$filterstatic.= " AND t.rowid IN (".$sql1.")";

	$resgr = $objectdet->fetchAll($sortorder, $sortfield, $limit, $offset, $filter, 'AND',$filterstatic);
	$lines = $objectdet->lines;
	$num = count($lines);
	$aGroup = array();		
}
$newgroup = new StdClass();
$nbtotalofrecords=0;
$num = 0;
$nro = 1;
$rowjs = '';
foreach ((array) $lines AS $i => $line)
{
	$res0 = $objectdetadd->fetch(0,$line->id);
	$htmltabs = '';
	$tabs = 0;
	$lGroup = true;
	$nbtotalofrecords++;
	$num++; 
	$var = !$var;
	$row = $line;
	if ($lGroup && $line->fk_task_parent)
	{
		$tabs+=6;
		for($a=0;$a<=$tabs;$a++)
			$htmltabs .= '&nbsp;';
	}

	include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/line_calendar.tpl.php';

		//buscamos las tareas que dependen de cada grupo
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_budget = ".$object->id;
	$filterstatic.= " AND t.fk_task_parent IN (".$line->id.")";
	$restask = $objectdet->fetchAll($sortorder, $sortfield, $limit, $offset, $filter, 'AND',$filterstatic);
	$linestask = $objectdet->lines;
	if (count($linestask)>0)
	{
		$lGroup = false;
		$tabs+=3;
		for($a=0;$a<=$tabs;$a++)
			$htmltabs .= '&nbsp;';

		foreach ((array) $linestask AS $i => $row)
		{
				//buscamos para filtrar
			$res1 = $objectdetadd->fetch(0,$row->id);
			if ($objectdetadd->c_grupo==0)
			{
				$nro++;
				include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/line_calendar.tpl.php';

			}
		}
	}
	
	$aGroup[$line->id] = $line->label;
	$nro++;
}
$_SESSION['taskduration'][$object->id] = $aArray;
$_SESSION['taskdurationt'][$object->id] = $aArrayt;
$rowjs = '';
$htmlGroup = '';
$nro = 1;
$nbtotalofrecords=0;
$num=0; 
foreach ((array) $lines AS $i => $line)
{
	$res0 = $objectdetadd->fetch(0,$line->id);
	$htmltabs = '';
	$tabs = 0;
	$lGroup = true;
	$nbtotalofrecords++;
	$num++; 
	$var = !$var;
	$row = $line;
	if ($lGroup && $line->fk_task_parent)
	{
		$tabs+=6;
		for($a=0;$a<=$tabs;$a++)
			$htmltabs .= '&nbsp;';
	}

	include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/line_calendar.tpl.php';

		//buscamos las tareas que dependen de cada grupo
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_budget = ".$object->id;
	$filterstatic.= " AND t.fk_task_parent IN (".$line->id.")";
	$restask = $objectdet->fetchAll($sortorder, $sortfield, $limit, $offset, $filter, 'AND',$filterstatic);
	$linestask = $objectdet->lines;
	if (count($linestask)>0)
	{
		$lGroup = false;
		$tabs+=3;
		for($a=0;$a<=$tabs;$a++)
			$htmltabs .= '&nbsp;';
		$objdur = new Budgettaskduration($db);
		foreach ((array) $linestask AS $i => $row)
		{
				$var = !$var;

				//buscamos para filtrar
			$res1 = $objectdetadd->fetch(0,$row->id);
			if ($objectdetadd->c_grupo==0)
			{

				$resdur = $objdur->fetch(0,$row->id);
				$duration = '';
				$successor = '';
				$predecessor = '';
				if ($resdur == 1)
				{
					if (empty($duration)) $duration = $objdur->duration;
					if (empty($successor)) $successor = $objdur->successor;
					if (empty($predecessor)) $predecessor = $objdur->predecessor;
				}
				$nro++;
				include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/line_calendar.tpl.php';

			}
		}
	}
	
	$aGroup[$line->id] = $line->label;
	$nro++;
}

//mostramos los grupos
//if ($action == 'viewgr' || $action == 'creategr')

if (count($aGroup)>0 || $action == 'viewgr' || $action == 'editgroup')	
{
	dol_fiche_head();
		//recuperamos
	if ($user->rights->budget->cale->crear)
	{

		if (! empty($conf->use_javascript_ajax))
		{
			$htmljs = "\n".'<script type="text/javascript">';
			$htmljs.= '$(document).ready(function () {';
			$htmljs.= $rowjs;
			$htmljs.= '});';
			$htmljs.= '</script>'."\n";
			print $htmljs;
		}

		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		if ($action == 'editgroup')
		{
			print '<input type="hidden" name="idr" value="'.GETPOST('idr').'">';
			print '<input type="hidden" name="action" value="updateitem">';
		}
		else
			print '<input type="hidden" name="action" value="additem">';
		//else
		//	print '<input type="hidden" name="action" value="viewit">';
		
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" id="id" name="id" value="'.$object->id.'">';
		print '<input type="hidden" name="c_grupo" value="1">';		
		print '<input type="hidden" name="subaction" value="creategr">';	

	}
	//print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

	print '<table class="table border centpercent">'."\n";
	print '<thead>';
	print '<tr>';
	print_liste_field_titre($langs->trans('Nro'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fieldref'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fieldtitle'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fieldfather'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fieldduration'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fieldsuccessor'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fieldpredecessor'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fielddateini'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fielddatefin'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fieldcalendar'),$_SERVER['PHP_SELF'],'','',$params,'align="center"',$sortfield,$sortorder);
	print '<td>'.'<div id="listtask">aa</div>'.'</td>';
	print '</tr>';
	print '</thead>';

		//armamos en un array los grupos para uso posterior
	print $htmlGroup;
	print '</table>';
	if ($user->rights->budget->budg->crear)
		print '</form>';
	dol_fiche_end();

}

