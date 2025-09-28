<?php
$params.='&id='.$object->id;
$params.='&action='.$action;
if ($action == 'editgroup' || $action == 'viewgr' || isset($_POST['idg']) || isset($_GET['idg']))
{
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_budget = ".$object->id;
	if (isset($_POST['idg']) || isset($_GET['idg']))

		$filterstatic.= " AND t.fk_task_parent = ".GETPOST('idg');

	//$resgr = $objectdet->fetchAll($sortorder, $sortfield, 0, $offset, $filter, 'AND',$filterstatic);
	//$nbtotalofrecords = count($objectdet->lines);
	$resgr = $objectdet->fetchAll($sortorder, $sortfield, $limit, $offset, $filter, 'AND',$filterstatic);
	$linesdet = $objectdet->lines;
	$num = count($linesdet);
	$aGroup = array();
}
$newgroup = new StdClass();
$nbtotalofrecords=0;
$num = 0;
$aGroup = array();
foreach ((array) $linesdet AS $i => $line)
{

	//buscamos para filtrar
	$res1 = $objectdetadd->fetch(0,$line->id);
	if ($objectdetadd->c_grupo)
	{
		$nbtotalofrecords++;
		$num++;
		$var = !$var;
		if ($action == 'editgroup' && GETPOST('idr') == $line->id)
		{
			$newgroup = $line;
		}
		else
		{
			$htmlGroup.= "<tr $bc[$var]>";;

			$htmlGroup.= '<td>'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idg='.$line->id.'&action=viewit">'.$line->ref.'</a></td>';
			$htmlGroup.= '<td>'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idg='.$line->id.'&action=viewit">'.$line->label.'</a>'.'</td>';
			$objectdettmp->fetch($line->fk_task_parent);
			$htmlGroup.= '<td>'.($objectdettmp->id == $line->fk_task_parent?$objectdettmp->label:'').'</td>';
			$htmlGroup.= '<td align="right">'.price($objectdetadd->total_amount).'</td>';
			$htmlGroup.= '<td align="right">';
			if ($user->rights->budget->budm->mod && $object->fk_statut == 0)
				$htmlGroup.= '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$line->id.'&action=editgroup">'.img_picto($langs->trans('Edit'),'edit').'</a>';
			if ($user->rights->budget->budm->del && $object->fk_statut == 0)
				$htmlGroup.= '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$line->id.'&action=deletegroup">'.img_picto($langs->trans('Delete'),'delete').'</a>';

			$htmlGroup.= '</td>';
			$htmlGroup.= '</tr>';
		}
		$aGroup[$line->id] = $line->label;
	}
}
//mostramos los grupos
//if ($action == 'viewgr' || $action == 'creategr')

if (count($aGroup)>0 || $action == 'viewgr' || $action == 'editgroup')
{
	dol_fiche_head();
		//recuperamos
	if ($user->rights->budget->budi->crear)
	{
		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		if ($action == 'editgroup')
		{
			print '<input type="hidden" name="idr" value="'.GETPOST('idr').'">';
			print '<input type="hidden" name="action" value="updateitem">';
		}
		else
		{
			print '<input type="hidden" name="action" value="additem">';
		}
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		print '<input type="hidden" name="c_grupo" value="1">';
		print '<input type="hidden" name="subaction" value="creategr">';
	}
	print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

	print '<table class="table border centpercent">'."\n";
	print '<thead>';
	print '<tr>';
	print_liste_field_titre($langs->trans('Fieldref'),$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fieldtitle'),$_SERVER['PHP_SELF'],'t.label','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fieldfather'),$_SERVER['PHP_SELF'],'t.fk_task_parent','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Fieldamount'),$_SERVER['PHP_SELF'],'t.total_ttc','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Action'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
	print '</tr>';
	print '</thead>';

	if ($user->rights->budget->budm->crear && $object->fk_statut == 0)
	{
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/add_group.tpl.php';
	}
		//armamos en un array los grupos para uso posterior
	print $htmlGroup;
	print '</table>';
	if ($user->rights->budget->budg->crear)
		print '</form>';
	dol_fiche_end();

	if ($user->rights->budget->budi->up)
	{
		print '<div class="tabsAction">';
		print '<div class="butAction"><a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=viewgr&subaction=createup'.'">'.$langs->trans('Uploaditems').'</a></div>';
		print '</div>';
	}
}

if ($action =='viewit' || $action == 'editres' || $action == 'edititem' || $action == 'deleteitem')
{

	//print '<div class="table-responsive">';
	$resg = $objectdet->fetch($idg);
		//if ($user->rights->budget->budi->crear && !GETPOST('idr'))

	if ((empty(GETPOST('idr')) && $user->rights->budget->budi->crear && $action != 'editres') || (GETPOST('idr') && $user->rights->budget->budi->mod && $action == 'edititem'))
	{
		if (! empty($conf->use_javascript_ajax))
		{
			print "\n".'<script type="text/javascript">';
			print '$(document).ready(function () { $("#itemid").change(function() { document.formitem.action.value="'.$action.'";
	document.formitem.submit(); }); });';
			print '</script>'."\n";
		}


		print '<form name="formitem" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		if ($action == 'edititem')
		{
			print '<input type="hidden" name="action" value="updateitem">';
			print '<input type="hidden" name="idr" value="'.GETPOST('idr').'">';
		}
		else
			print '<input type="hidden" name="action" value="additem">';
		//else
		//	print '<input type="hidden" name="action" value="viewit">';

		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		if ($action == 'viewit')
			print '<input type="hidden" name="idg" value="'.$idg.'">';
		print '<input type="hidden" name="subaction" value="item">';
			//print '<input type="hidden" name="c_grupo" value="0">';
	}
	//$head = budget_group_prepare_head($object,$aGroup,$user);
	$title=$langs->trans("Group");
	if($action=='viewit') $title=$langs->trans('Item');
	$poscard = 'g'.$idg;
	//dol_fiche_head($head, $poscard, $title, 0, 'budget');
		//print '<h3>'.$objectdet->label.'</h3>';
	$fk_task_parent = $idg;
	//print '<div id="listtask">';

	if (empty($htmlGroup))
	{
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/task.tpl.php';
	}
	//print '</div>';
	dol_fiche_end();
	if ((empty(GETPOST('idr')) && $user->rights->budget->budi->crear && $action != 'editres') || (GETPOST('idr') && $user->rights->budget->budi->mod && $action == 'edititem'))
	{
		print '</form>';
	}
	//print '</div>';

		    	//armamos boton apra importar items y grupos
    	//condicion  se debe tener seleccionado un grupo
	if ($lWriteitem)
	{
		if ($idg>0 && empty(GETPOST('idr')))
		{
			print '<a data-toggle="modal" href="#addgroup" class="btn btn-primary btn-large">'.$langs->trans('Import').'</a>';

			print '<div id="addgroup" class="modal modal-wide fade " style="display: none;">';

			print '<div class="modal-dialog">';

			print '<div class="modal-content">';
			print '<form id="formidt" name="formidt" action="'.DOL_URL_ROOT.'/budget/budget/card.php'.'" method="POST">';
			print '<input type="hidden" name="id" value="'.$id.'">';
			print '<input type="hidden" name="idg" value="'.$idg.'">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="import_group">';

			print '<div class="modal-header">';
			print '<a data-dismiss="modal" class="close">×</a>';
			print '<h3>'.$langs->trans('Modules/Tasks').'</h3>';
			print '</div>';
			print '<div class="modal-body">';
			include DOL_DOCUMENT_ROOT.'/budget/tpl/import_tasks.tpl.php';
			print '</div>';
			print '<div class="modal-footer">';
			print '<input type="submit" class="btn btn-success" value="'.$langs->trans('Import').'"/>';
			print '<a href="#" data-dismiss="modal" class="btn">Cerrar</a>';
			print '</div>';

			print '</form>';
			print '</div>';

			print '</div>';

			print '</div>';
		}
	}
}
if ($action == 'viewre')
{

	//include DOL_DOCUMENT_ROOT.'/budget/budget/productbudget_list.php';
	include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/productbudget_list.tpl.php';
}