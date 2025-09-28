<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
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
 */

/**
 *	\file       htdocs/projet/tasks.php
 *	\ingroup    projet
 *	\brief      List all tasks of a project
 */

require ("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';

require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';


require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
dol_include_once('/monprojet/class/html.formtask.class.php');

require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/unit/class/units.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/doc.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/utils.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formaddmon.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskresourceext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/productprojetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskresourcealmacendetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
if ($conf->categorie->enabled)
	dol_include_once('/categories/class/categorie.class.php');
else
	exit;
if ($conf->budget->enabled)
{
	//require_once DOL_DOCUMENT_ROOT.'/budget/class/html.formadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
	dol_include_once('/budget/class/budget.class.php');
	dol_include_once('/budget/class/pustructureext.class.php');
	dol_include_once('/budget/class/pustructuredetext.class.php');
	dol_include_once('/budget/class/productbudgetext.class.php');
	dol_include_once('/budget/class/putypestructureext.class.php');
}
	//dol_include_once('/budget/class/html.formv.class.php');

if ($conf->request->enabled)
	require_once DOL_DOCUMENT_ROOT.'/request/class/requestitem.class.php';
if ($conf->product->enabled)
	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
if ($conf->almacen->enabled)
	require_once DOL_DOCUMENT_ROOT.'/almacen/local/class/entrepotrelationext.class.php';
if ($conf->assets->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdetext.class.php';
}
if ($conf->purchase->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseur.factureext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefournadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefourndetadd.class.php';
}


//images
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

$langs->load("users");
$langs->load("projects");
$langs->load('monprojet@monprojet');

$action = GETPOST('action', 'alpha');
$subaction = GETPOST('subaction', 'alpha');
if (empty($subaction)) $subaction='monthly';

$id 		= GETPOST('id', 'int');
$idr 		= GETPOST('idr', 'int');
$ref 		= GETPOST('ref', 'alpha');
$selgroup	= GETPOST('group', 'alpha');
$backtopage	= GETPOST('backtopage','alpha');
$cancel		= GETPOST('cancel');
$newsel		= GETPOST('newsel', 'int');
$mode 		= GETPOST('mode', 'alpha');
$mine 		= ($mode == 'mine' ? 1 : 0);

$table = 'llx_projet_task';
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$object 	= new Projectext($db);
$objectadd 	= new Projectext($db);
$objecttime = new Projettasktimedoc($db);
//regisro de avances
$taskstatic = new Task($db);
$taskadd 	= new Taskext($db);
//nueva clase para listar tareas
$objuser 	= new User($db);
//$cunits = new Cunits($db);
if ($conf->budget->enabled)
	$pustr 		= new Pustructure($db);
$product 	= new Product($db);
$objectptr 	= new Projettaskresourceext($db);
$objectpp 	= new Productprojetext($db);
$adherent    = new Adherent($db);
$societe 	 = new Societe($db);

if ($conf->budget->enabled) $budget = new Budget($db);
if ($conf->categorie->enabled) $categorie = new Categorie($db);

if ($conf->monprojet->enabled) $formtask=new FormTask($db);

$extrafields_project = new ExtraFields($db);
$extrafields_task = new ExtraFields($db);
if ($conf->budget->enabled)
	$items = new Items($db);
if ($conf->assets->enabled)
{
	$assets = new Assetsext($db);
	$assign = new Assetsassignmentext($db);
}

include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';
// Must be include, not include_once

if ($id > 0 || ! empty($ref))
{
	// fetch optionals attributes and labels
	$extralabels_projet=$extrafields_project->fetch_name_optionals_label($object->table_element);
}
$extralabels_task=$extrafields_task->fetch_name_optionals_label($taskstatic->table_element);
// Security check
$socid=0;
if ($user->societe_id > 0) $socid = $user->societe_id;
if (!$user->rights->monprojet->task->crear)
	$result = restrictedArea($user, 'projet', $id);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('projecttaskcard','globalcard','formObjectOptions'));

$progress=GETPOST('progress', 'int');
$label=GETPOST('label', 'alpha');
$description=GETPOST('description');
$planned_workload=(GETPOST('planned_workloadhour')?GETPOST('planned_workloadhour'):0)*3600+(GETPOST('planned_workloadmin')?GETPOST('planned_workloadmin'):0)*60;

$userAccess=0;
$lDisabled = false;

$aTypeResource = load_type_resource();
	//verificamos si tiene budget
list($fk_budget,$aCat) = get_categorie($id);
$aStrbudget = unserialize($_SESSION['aStrbudget']);
$aStrgroupcat = $aStrbudget[$fk_budget]['aStrgroupcat'];

/*
 * Actions
 */

if ($user->rights->monprojet->task->crear)
	$userWrite = true;

if ($action == 'delmat')
{
	$aDatamat = unserialize($_SESSION['aDatamat']);
	unset($aDatamat[$id][$_POST['idregmat']]);
	$_SESSION['aDatamat'] = serialize($aDatamat);
}
/*
 * View
 */

// Example : Adding jquery code

$form=new Formv($db);
//$formadd = new FormAdd($db);
$formother=new FormOther($db);
$taskstatic = new Task($db);
$userstatic=new User($db);

$title=$langs->trans("Project").' - '.$langs->trans("Tasks").' - '.$object->ref.' '.$object->name;
if (! empty($conf->global->MAIN_HTML_TITLE) && preg_match('/projectnameonly/',$conf->global->MAIN_HTML_TITLE) && $object->name) $title=$object->ref.' '.$object->name.' - '.$langs->trans("Tasks");
$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
$aCss = array('monprojet/bootstrap/css/bootstrap.min.css','monprojet/dist/css/AdminLTE.min.css','monprojet/dist/css/skins/_all-skins.min.css');
$aCss = array('monprojet/bootstrap/css/bootstrap.min.css');
$aJse = array();
//llxHeader("",$ittle,$help_url,'','','',$aJs,$aCss);
$aJs = array('monprojet/js/monutils.js');
$aCss = array('monprojet/css/monutils.css');
llxHeader("",$title,$help_url,'','','',$aJs,$aCss);

if ($id > 0 || ! empty($ref))
{
	$object->fetch($id, $ref);
	$objectadd->fetch($id, $ref);
	$object->fetch_thirdparty();
	$res=$object->fetch_optionals($object->id,$extralabels_projet);
	$res=$objectadd->fetch_optionals($objectadd->id,$extralabels_projet);


	// To verify role of users
	//$userAccess = $object->restrictedProjectArea($user,'read');
	$userWrite  = $objectadd->restrictedProjectAreaadd($user,'write');
	//$userDelete = $object->restrictedProjectArea($user,'delete');
	//print "userAccess=".$userAccess." userWrite=".$userWrite." userDelete=".$userDelete;


	$tab=GETPOST('tab')?GETPOST('tab'):'consumption';

	$head=project_prepare_head($object);
	dol_fiche_head($head, $tab, $langs->trans("Project"),0,($object->public?'projectpub':'project'));

	$param=($mode=='mine'?'&mode=mine':'');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/projet/list.php">'.$langs->trans("BackToList").'</a>';

	// Ref
	print '<tr><td width="30%">';
	print $langs->trans("Ref");
	print '</td><td>';
	// Define a complementary filter for search of next/prev ref.
	if (! $user->rights->projet->all->lire)
	{
		$projectsListId = $object->getProjectsAuthorizedForUser($user,$mine,0);
		$object->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
	}
	print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref', '', $param);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Label").'</td><td>'.$object->title.'</td></tr>';

	// print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
	// if (! empty($object->thirdparty->id)) print $object->thirdparty->getNomUrl(1);
	// else print '&nbsp;';
	// print '</td>';
	// print '</tr>';

	// // Visibility
	// print '<tr><td>'.$langs->trans("Visibility").'</td><td>';
	// if ($object->public) print $langs->trans('SharedProject');
	// else print $langs->trans('PrivateProject');
	// print '</td></tr>';

	// // Statut
	// print '<tr><td>'.$langs->trans("Status").'</td><td>'.$object->getLibStatut(4).'</td></tr>';

	// // Date start
	// print '<tr><td>'.$langs->trans("DateStart").'</td><td>';
	// print dol_print_date($object->date_start,'day');
	// print '</td></tr>';

	// // Date end
	// print '<tr><td>'.$langs->trans("DateEnd").'</td><td>';
	// print dol_print_date($object->date_end,'day');
	// print '</td></tr>';

	// Other options
	//$parameters=array();
	//$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action); // Note that $action and $object may have been modified by hook
	//if (empty($reshook) && ! empty($extrafields_project->attribute_label))
	//{
	//	print $object->showOptionals($extrafields_project);
	//}

	print '</table>';

	dol_fiche_end();

	/*
	 * Fiche projet en mode visu
	 */

	/*
	 * Actions
	 */
	print '<div class="tabsAction">';

	if ($user->rights->projet->all->creer ||
		$user->rights->projet->creer ||
		$user->rights->monprojet->task->crear)
	{
		if ($user->rights->monprojet->task->crear)
			$userWrite = true;
		if ($object->public || $userWrite > 0 && $action != 'createup')
		{
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=createres'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Create').'</a>';
		}
		else
		{
			print '<a class="butActionRefused" href="#" title="'.$langs->trans("NotOwnerOfProject").'">'.$langs->trans('AddTask').'</a>';
		}
	}
	else
	{
		print '<a class="butActionRefused" href="#" title="'.$langs->trans("NotEnoughPermissions").'">'.$langs->trans('AddTask').'</a>';
	}
	if ($conf->assets->enabled)
	{
		if ($user->rights->assets->alloc->crear)
		{
			$langs->load('assets@assets');
			print '<div class="inline-block divButAction"><a class="butAction" href="'.DOL_URL_ROOT.'/assets/assignment/fiche.php?origin=projet&originid='.$object->id.'&amp;action=create">'.$langs->trans("Requireassets").'</a></div>';
		}
		else
		{
			print '<div class="inline-block divButAction"><a class="butActionRefused" href="#" title="'.$langs->trans("NotOwnerAssets").'">'.$langs->trans('Requireassets').'</a></div>';
		}

	}
	if ($conf->almacen->enabled)
	{
		if ($user->rights->almacen->pedido->write)
		{
			$langs->load('almacen@almacen');
			print '<div class="inline-block divButAction"><a class="butAction" href="'.DOL_URL_ROOT.'/almacen/fiche.php?origin=projet&originid='.$object->id.'&amp;action=create">'.$langs->trans("Requirematerial").'</a></div>';
		}
		else
		{
			print '<div class="inline-block divButAction"><a class="butActionRefused" href="#" title="'.$langs->trans("NotOwnerAssets").'">'.$langs->trans('Requirematerial').'</a></div>';
		}

	}

	print '</div>';

	//recuperamos las tareas del proyecto
	$filterstatic = " AND t.fk_projet = ".$object->id;
	$filteradd = '';
	$taskadd->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,false,$filteradd);
	$idsTask = '';
	foreach ((array) $taskadd->lines As $j => $line)
	{
		if (!empty($idsTask)) $idsTask.= ',';
		$idsTask.= $line->id;
	}
	print '<div class="fichecenter fichecenterbis">';
	$lBudget = false;
	if ($conf->budget->enabled && $fk_budget)
	{
		$lBudget = true;
		print '<div class="fichehalfleft">';
		print '<div class="box">';
		print '<h2>'.$langs->trans('Programmed').'</h2>';
		print '<table class="noborder boxtable" width="100%">';
		print '<thead>';
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Ref"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Amount"),"", "","","","",$sortfield,$sortorder);
		print '</tr>';
		print '</thead>';
		print '<tbody>';
		foreach((array) $aStrgroupcat AS $group => $fk_categorie)
		{
			$categorie->fetch($fk_categorie);
			$opt = $linestr->fk_categorie;
			$code_structure = $fk_categorie;
			$fk_task_parent = $id;
			$loop++;

				//$objprodb->fetch($lineb->fk_product_budget);
			print "<tr $bc[$var] id=".'"'.$lineb->id.'"'.">";
		//print '<td align="center" class="none">'.($lProduct?$product->getNomUrl(1):img_picto($langs->trans('Product or service not registered'),DOL_URL_ROOT.'/budget/img/interrogacion.png','',1)).'</td>';
			print '<td style="background-color:#'.$categorie->color.';">'.$categorie->label.'</td>';
		//print '<td class="detail">'.$lineb->detail.'</td>';

		//include DOL_DOCUMENT_ROOT.'/monprojet/task/tpl/resource.tpl.php';
			print '<td align="right">'.price(0).'</td>';
			print '</tr>';
		}
		print '</tbody>';
		print '</table>';
		print '</div>';
		print '</div>';
	}

	print '<div class="'.($lBudget?'fichehalfright':'fichehalfleft').'">';
	print '<div class="box">';
	print '<h2>'.$langs->trans('Executed').'</h2>';
	print '<table class="noborder boxtable" width="100%">';
	print '<thead>';
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Ref"),"", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Amount"),"", "","","","",$sortfield,$sortorder);
	print '</tr>';
	print '</thead>';
	print '<tbody>';
	foreach((array) $aStrgroupcat AS $group => $fk_categorie)
	{
		$categorie->fetch($fk_categorie);
		$opt = $linestr->fk_categorie;
		$code_structure = $fk_categorie;
		$fk_task_parent = $id;
		$loop++;
		//recuperamos en uso recursos
		$filterstatic = ' AND t.fk_projet_task IN ('.$idsTask.')';
		$filterstatic.= " AND t.group_resource = '".$group."'";
		$objectptr->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic);
		//sumamos los valores amount
		$total = 0;
		$lines = $objectptr->lines;
		foreach ((array) $lines AS $j => $line)
		{
			$total+= $line->amount;
			$aGroup[$group][$line->id] = $line;
			$aProduct[$line->fk_product] = $line->detail;
			$unit = '';
			$objectptr->fetch($line->id);
			$unit = $langs->trans($objectptr->getLabelOfUnit());
			$fkproduct = $line->fk_product;
			$aGroupdet[$group][$line->detail]['quant'] += $line->quant;
			$aGroupdet[$group][$line->detail]['fk_product'] = $fkproduct;
			$aGroupdet[$group][$line->detail]['amount'] += $line->amount;
			$aGroupdet[$group][$line->detail]['unit'] = $unit;

			//detalle
			if ($line->object == 'product')
			{
				$product->fetch($line->fk_object);
				$nomobject = $product->getNomUrl(1);
			}
			elseif ($line->object == 'societe')
			{
				$societe->fetch($line->fk_object);
				$nomobject = $societe->getNomUrl(1);
			}
			elseif ($line->object == 'adherent')
			{
				$adherent->fetch($line->fk_object);
				$nomobject = $adherent->getNomUrl(1).' '.$adherent->getFullName($langs);
			}
			elseif ($line->object == 'assignment')
			{
				$assignment->fetch($line->fk_object);
				$nomobject = $assignment->getNomUrl(1);
			}
			elseif ($line->object == 'assets')
			{
				$assets->fetch($line->fk_object);
				$nomobject = $assets->getNomUrl(1);
				$fkproduct = $line->fk_objectdet;
				$aGroupdet[$group][$line->detail]['fk_product'] = $fkproduct;
			}
			else
			{
				$nomobject = '';
			}

			$aGroupdetl[$group][$fkproduct][$line->id]['object'] = $nomobject;
			$aGroupdetl[$group][$fkproduct][$line->id]['quant'] += $line->quant;
			$aGroupdetl[$group][$fkproduct][$line->id]['amount'] += $line->amount;
			$aGroupdetl[$group][$fkproduct][$line->id]['unit'] = $unit;
		}
		print "<tr $bc[$var] id=".'"'.$lineb->id.'"'.">";
		print '<td style="background-color:#'.$categorie->color.';">'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.($group == $selgroup?'':'&group='.$group.'&action=group').'">'.($group == $selgroup?img_picto('','edit_remove'):img_picto('','edit_add')).' '.$categorie->label.'</a></td>';
		print '<td align="right">'.price(price2num($total,'MT')).'</td>';
		$stotal+= $total;
		print '</tr>';
		if ($selgroup == $group)
		{
			print '<tr>';
			print '<td colspan="2">';
			include DOL_DOCUMENT_ROOT.'/monprojet/task/tpl/rep_resource.tpl.php';
			print '</td>';
			print '</tr>';
		}
	}
	print '<tr class="liste_total">';
	print '<td>'.$langs->trans('Total').'</td>';
	print '<td align="right">'.price(price2num($stotal,'MT')).'</td>';
	print '</tr>';

	print '</tbody>';
	print '</table>';
	print '</div>';
	print '</div>';



	//listamos los activos asignados
	if ($conf->finint->enabled)
	{
		require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcash.class.php';
		require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashdeplacement.class.php';
		$rcash = new Requestcash($db);
		$rcashdep = new Requestcashdeplacement($db);
		$langs->load('finint');
		print '<div class="fichehalfleft">';
		print '<div class="box">';
		print '<h2>'.$langs->trans('Purchase').'/'.$langs->trans('Expenses').'</h2>';
		print '<table class="noborder boxtable" width="100%">';
		print '<thead>';
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Ref"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Label"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Amount"),"", "","","",'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Statut"),"", "","","",'align="right"',$sortfield,$sortorder);
		print '</tr>';
		print '</thead>';
		print '<tbody>';
		$filterdep = " AND t.fk_projet_dest IN (".$id.")";
		$filterdep.= " AND t.status = 3";
		$rcashdep->fetchAll('', '', 0, 0,array(1=>1), 'AND', $filterdep);
		$lines = $rcashdep->lines;
		$total = 0;
		foreach((array) $lines AS $j => $line)
		{
			$rcash->fetch($line->fk_request_cash);
			$rcashdep->id = $line->id;
			$rcashdep->status = $line->status;
			print "<tr $bc[$var]>";
			if($user->rights->finint->efe->leer)
				print '<td>'.$rcash->getNomUrl(1).'</td>';
			else
				print '<td>'.$rcash->ref.'</td>';
			print '<td>'.$line->detail.'</td>';
			print '<td align="right">'.price2num($line->amount).'</td>';
			print '<td align="right">'.$rcashdep->getLibStatut(1).'</td>';
			print '</tr>';
			$total+=$line->amount;
		}
		print '<tr class="liste_total">';
		print '<td colspan="2">'.$langs->trans('Total').'</td>';
		print '<td align="right">'.price(price2num($total,'MT')).'</td>';
		print '<td>&nbsp;</td>';
		print '</tr>';
		print '</tbody>';
		print '</table>';
		print '</div>';
		print '</div>';
		//fin finint
	}
	print '</div>';




	if ($conf->purchase->enabled)
	{
		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';
		require_once DOL_DOCUMENT_ROOT.'/purchase/class/ctypepurchase.class.php';
		print '<div class="fichecenter fichecenterbis">';
	//mostramos un resumen de recursos utilizados
		print '<div class="fichehalfleft">';
		print '<div class="box">';
		$totalfinal = $total + $stotal;
		print '<h2>'.$langs->trans('Total recursos utilizados').' = '.price(price2num($totalfinal,'MT')).'</h2>';
		print '</div>';
		print '</div>';
		print '</div>';


		print '<div class="fichecenter fichecenterbis">';


		print '<div class="box">';
		print '<h2>'.$langs->trans('Gastos ejecutados').'</h2>';
		print '<table class="noborder boxtable" width="100%">';
		print '<thead>';
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Account"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Fase de la obra"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Item"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Detalle del gasto"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Unidad"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Quant"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("PU"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Total"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Proveedor"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Tipo"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("N°"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Fecha"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Responsable"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Forma Pago"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Fecha Rendición"),"", "","","","",$sortfield,$sortorder);
		print '</tr>';
		print '</thead>';
		print '<tbody>';

	//recursos utilizados y aprobados desde el modulo de compra
		$facture = new FactureFournisseurext($db);
		$factureadd = new FactureFournadd($db);
		$task = new Taskext($db);
		$tasktmp = new Taskext($db);
		$facturedet = new SupplierInvoiceLine($db);
		$facturedetadd = new Facturefourndetadd($db);
		$paymentstatic=new PaiementFourn($db);
		$ctypepurchase=new Ctypepurchase($db);

	//recursos por modulo, items, proveedor
		$filterstatic = " AND t.fk_projet = ".$id;
		$facture->fetchAll('ASC', 'ref', 0, 0, array(1=>1), 'AND',$filterstatic,false,'1');
		$num = count($facture->lines);
		if ($num>0)
		{
			//recorremos cada grupo para luego obtener los items
			$lines = $facture->lines;
			foreach ($lines AS $j => $line)
			{
				$facture->fetch($line->id);
				$labeldet = '';
				$unit = 'Global';
				$quant = 1;
				$pu = 0;
				$total = 0;
				$linesdet = $line->lines;
				$resp = '';
				$pays = '';
				//verificamos el pago
				$sql = 'SELECT p.datep as dp, p.ref, p.num_paiement, p.rowid, p.fk_bank,';
				$sql.= ' c.id as paiement_type,';
				$sql.= ' pf.amount,';
				$sql.= ' ba.rowid as baid, ba.ref as baref, ba.label';
				$sql.= ' FROM '.MAIN_DB_PREFIX.'paiementfourn as p';
				$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'bank as b ON p.fk_bank = b.rowid';
				$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'bank_account as ba ON b.fk_account = ba.rowid';
				$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_paiement as c ON p.fk_paiement = c.id';
				$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'paiementfourn_facturefourn as pf ON pf.fk_paiementfourn = p.rowid';
				$sql.= ' WHERE pf.fk_facturefourn = '.$line->id;
				$sql.= ' ORDER BY p.datep, p.tms';
				$result = $db->query($sql);
				if ($result)
				{
					$nump = $db->num_rows($result);
					$i = 0; $totalpaye = 0;
					if ($nump > 0)
					{
						while ($i < $nump)
						{
							if ($pays) $pays.= '<br>';
							$objp = $db->fetch_object($result);

							$paymentstatic->id=$objp->rowid;
							$paymentstatic->datepaye=$db->jdate($objp->dp);
							$paymentstatic->ref=$objp->ref;
							$paymentstatic->num_paiement=$objp->num_paiement;
							$paymentstatic->payment_code=$objp->payment_code;
							$pays.=$paymentstatic->getNomUrl(1);
							$form->load_cache_types_paiements();
							$pays.= '<br>'.$form->cache_types_paiements[$objp->paiement_type]['label'];
							$pays.= '<br>'.$objp->num_paiement;
							$pays.= '<br>'.dol_print_date($db->jdate($objp->dp), 'day');
							$i++;
						}
					}
					$db->free($result);
				}
				$nrodet = 0;
				if (count($linesdet)>0)
				{

					foreach ($linesdet AS $k => $row)
					{
						$nrodet++;
						//verificamos en facturefourndetadd
						$res = $facturedetadd->fetch(0,$row->id);
						if ($res>0)
						{
							$element = $facturedetadd->object;
							if ($element == 'requestcashdeplacement')
							{
								$element = 'finint';
								$subelement = 'requestcashdeplacementext';
								$elementfather = 'finint';
								$subelementfather = 'requestcashext';

							require_once DOL_DOCUMENT_ROOT.'/'.$element.'/class/'.$subelement.'.class.php';
							$classname = ucfirst($subelement);
							$srcobject = new $classname($db);
							$result=$srcobject->fetch($facturedetadd->fk_object);
							require_once DOL_DOCUMENT_ROOT.'/'.$elementfather.'/class/'.$subelementfather.'.class.php';
							$classname = ucfirst($subelementfather);
							$result=$srcobject->fetch($srcobject->fk_request_cash);
							$objuser->fetch($srcobject->fk_user_create);
							$resp = $objuser->getNomUrl(1);
							}
						}
						if ($row->fk_product>0)
						{
							$product->fetch($row->fk_product);
							$labeldet = $product->getNomUrl(1).' ';
						}
						$facturedet->fetch($row->id);
						$labeldet .= $row->description;
						$unit = $facturedet->getLabelOfUnit('short');
						$quant = $row->qty;
						$pu = $row->subprice;
						$total += $row->total_ht;
					}
					if (count($linesdet)>1)
					{
						$labeldet = $langs->trans('Varios items');
						$unit = $langs->trans('Global');
						$quant = $nrodet;
						$pu = '';
					}
				}
				$factureadd->fetch(0,$line->id);
				$va = !$var;
				print "<tr $bc[$var]>";
				$resc = $ctypepurchase->fetch(0,$factureadd->code_type_purchase);
				if ($resc == 1)
				{
					$categorie->fetch($ctypepurchase->fk_categorie);
					print '<td>'.$categorie->label.'</td>';
				}
				else
					print '<td></td>';
			//verificamos si tiene item
				if ($factureadd->fk_projet_task>0)
				{
					$task->fetch($factureadd->fk_projet_task);
					if ($task->fk_task_parent>0)
					{
						$tasktmp->fetch($task->fk_task_parent);
						print '<td>'.$tasktmp->ref.' '.$tasktmp->label.'</td>';
					}
					else
						print '<td>&nbsp;</td>';
				}
				else
					print '<td>&nbsp;</td>';
				if ($factureadd->fk_projet_task>0)
					print '<td>'.$task->ref.' '.$task->label.'</td>';
				else
					print '<td>&nbsp;</td>';

				print '<td>'.$labeldet.'</td>';
				print '<td>'.$unit.'</td>';
				print '<td>'.$quant.'</td>';
				if (count($linesdet)>1)
					print '<td align="right">&nbsp;</td>';
				else
					print '<td align="right">'.price($pu).'</td>';
				print '<td align="right">'.price($total).'</td>';
				$suma += $total;
				if ($line->fk_soc>0)
				{
					$societe->fetch($line->fk_soc);
					print '<td>'.$societe->getNomUrl(1).'</td>';
				}
				else
					print '<td></td>';
				print '<td>'.$factureadd->code_facture.'</td>';
				print '<td>'.$facture->getNomUrl(1).'</td>';
				print '<td>'.dol_print_date($facture->date,'day').'</td>';
				//responsable de la compra
				print '<td>'.$resp.'</td>';
				print '<td>'.$pays.'</td>';


				print '</tr>';
			}
		}
		//imprimimos totales
		print '<tr class="liste_total">';
		print '<td class="liste_total" colspan="7">'.$langs->trans('Total').'</td>';
		print '<td class="liste_total" align="right">'.price(price2num($suma,'MT')).'</td>';
		print '<td class="liste_total" colspan="6"></td>';
		print '</tr>';
		print '</tbody>';
		print '</table>';

		print '</div>';
		print '</div>';
	}




	print '<div class="fichecenter fichecenterbis">';

	//listamos los activos asignados
	if ($conf->assets->enabled)
	{
		$langs->load('assets@assets');
		print '<div class="fichehalfleft">';
		print '<div class="box">';
		print '<h2>'.$langs->trans('Assets').'</h2>';
		print '<table class="noborder boxtable" width="100%">';
		print '<thead>';
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Doc"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Assets"),"", "","","","",$sortfield,$sortorder);
		print '</tr>';
		print '</thead>';
		print '<tbody>';
		$sql1 = "SELECT fk_asset FROM ".MAIN_DB_PREFIX."assets_assignment AS a ";
		$sql1.= " INNER JOIN ".MAIN_DB_PREFIX."assets_assignment_det AS ad ON ad.fk_asset_assignment = a.rowid ";
		$sql1.= " INNER JOIN ".MAIN_DB_PREFIX."projet AS p ON a.fk_projet = p.rowid ";
		$sql1.= " WHERE p.rowid = ".$object->id;
		$sql1.= " AND a.status > 0";
		$sql1.= " AND ad.status >= 0";
		$filterass = " AND t.rowid IN (".$sql1.")";
		$assets->fetchAll('ASC', 'ref', 0, 0,array(1=>1), 'AND', $filterass);
		$lines = $assets->lines;
		foreach((array) $lines AS $j => $line)
		{
			$objassigndet = new Assetsassignmentdetext($db);
			$objassign    = new Assetsassignmentext($db);
			$res = $objassigndet->fetch_active($line->id);
			$resa = 0;
			if ($res==1)
				$resa = $objassign->fetch($objassigndet->fk_asset_assignment);
			$assets->fetch($line->id);
			print "<tr $bc[$var]>";
			print '<td>'.($resa?$objassign->getNomUrl(1):$langs->trans('Pending')).'</td>';
			print '<td align="left">'.$assets->getNomUrl(1).' '.$assets->descrip.'</td>';
			print '</tr>';
		}
		print '</tbody>';
		print '</table>';
		print '</div>';
		print '</div>';
		//fin activos
	}

	//listamos los pedidos asignados
	if ($conf->almacen->enabled)
	{
		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		require_once DOL_DOCUMENT_ROOT.'/almacen/class/solalmacenext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/almacen/class/solalmacendetext.class.php';
		$solalmdet = new Solalmacendetext($db);
		$langs->load('almacen@almacen');
		print '<div class="fichehalfleft">';
		print '<div class="box">';
		print '<h2>'.$langs->trans('Products').'</h2>';
		print '<table class="noborder boxtable" width="100%">';
		print '<thead>';
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Label"),"", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Qty"),"", "","","",'align="right"',$sortfield,$sortorder);
		print '</tr>';
		print '</thead>';
		print '<tbody>';
		$sql1 = "SELECT a.rowid FROM ".MAIN_DB_PREFIX."entrepot_relation AS a ";
		$sql1.= " WHERE a.fk_projet = ".$object->id;
		$sql2 = "SELECT b.rowid FROM ".MAIN_DB_PREFIX."sol_almacen AS b ";
		$sql2.= " WHERE b.fk_projet IN (".$object->id.")";
		$filterass = " AND t.fk_almacen IN (".$sql2.")";
		$solalmdet->fetchAll('', '', 0, 0,array(1=>1), 'AND', $filterass);
		$lines = $solalmdet->lines;
		foreach((array) $lines AS $j => $line)
		{
			$product = new Product($db);
			$resa = $product->fetch($line->fk_product);
			print "<tr $bc[$var]>";
			print '<td>'.$product->getNomUrl(1).' '.$product->label.'</td>';
			print '<td align="right">'.price2num($line->qty).'</td>';
			print '</tr>';
		}
		print '</tbody>';
		print '</table>';
		print '</div>';
		print '</div>';
		//fin activos
	}


	print '</div>';


}

llxFooter();

$db->close();
