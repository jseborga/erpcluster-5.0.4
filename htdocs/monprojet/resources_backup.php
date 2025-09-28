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
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
//budget
if ($conf->budget->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/budget/class/html.formadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/items/class/items.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/cunits.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructure.class.php';
}
else
	return '';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
dol_include_once('/monprojet/class/html.formtask.class.php');
dol_include_once('/monprojet/class/html.formv.class.php');

$langs->load("users");
$langs->load("projects");
$langs->load('monprojet@monprojet');

$action = GETPOST('action', 'alpha');
$subaction = GETPOST('subaction', 'alpha');
if (empty($subaction)) $subaction='monthly';
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$backtopage=GETPOST('backtopage','alpha');
$cancel=GETPOST('cancel');
$newsel=GETPOST('newsel', 'int');

$mode = GETPOST('mode', 'alpha');
$mine = ($mode == 'mine' ? 1 : 0);

$table = 'llx_projet_task';
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$object = new Project($db);
$objectadd = new ProjectAdd($db);
$objecttime = new Projettasktimedoc($db); //regisro de avances
$taskstatic = new Task($db);
$taskadd = new Taskext($db); //nueva clase para listar tareas
$objuser = new User($db);
$cunits = new Cunits($db);
$pustr = new Pustructure($db);
$product = new Product($db);

if ($conf->monprojet->enabled) $formtask=new FormTask($db);

$extrafields_project = new ExtraFields($db);
$extrafields_task = new ExtraFields($db);
if ($conf->budget->enabled)
	$items = new Items($db);

include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once

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
$planned_workload=GETPOST('planned_workloadhour')*3600+GETPOST('planned_workloadmin')*60;

$userAccess=0;
$lDisabled = false;


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
$formadd = new FormAdd($db);
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
	$parameters=array();
	$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action); // Note that $action and $object may have been modified by hook
	if (empty($reshook) && ! empty($extrafields_project->attribute_label))
	{
		print $object->showOptionals($extrafields_project);
	}

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

	print '</div>';

	//recuperamos de 
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_projet = ".$id;
	$filterstatic.= " AND t.fk_categorie > 0";
	//$filterstatic.= " AND t.ordby = 1";
	$pustr->fetchAll('ASC', 'ordby', 0, 0, $filter, 'AND',$filterstatic,false);
	foreach((array) $pustr->lines AS $i => $line)
	{
		$aStr[$line->ref] = $line->ref;
		$aStrref[$line->ref] = $line->detail;
		$aStrlabel[$line->fk_categorie] = $line->detail;
	}
	$_SESSION['aStrref'] = serialize($aStrref);
	$_SESSION['aStrlabel'] = serialize($aStrlabel);
	$aDatamat = unserialize($_SESSION['aDatamat']);
	if ($action == 'createres')
	{
		include_once DOL_DOCUMENT_ROOT.'/monprojet/tpl/frames.tpl.php';
		//registro de avances
		print '<div class="fichecenter fichecenterbis">';
		
		print '<form id="formulario" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input id="id" type="hidden" name="id" value="'.$object->id.'">';
		print '<input type="hidden" name="action" value="addmat">';

		print '<div class="contenedor-tabla width100">';

		print '<div class="contenedor-fila">';
		print '<div class="contenedor-columna width05" >'.$langs->trans('Type').'</div>';
		print '<div class="contenedor-columna width25">'.$langs->trans('Seleccione').'</div>';
		print '<div class="contenedor-columna width10">'.$langs->trans('Product').'</div>';
		print '<div class="contenedor-columna width05">'.$langs->trans('Qty').'</div>';
		print '<div class="contenedor-columna width05">'.$langs->trans('Action').'</div>';
		print '</div>';

		$vclass = !$vclass;
		print '<div class="contenedor-fila" id="service'.$i.'" data="'.$i.'">';

		print '<div class="contenedor-columna">';
		print $form->selectarray('ref',$aStr,'',1);
		print '</div>';

			//print '<div id="carga0">';
		$filter = " t.fk_projet = ".$object->id;
		print '<div class="contenedor-columna">';
		print $formtask->select_task(($refite?$refite:$_POST['ref']),'task',$filter,1,0,0,'','',1,1);
		print '</div>';

		print '<div class="contenedor-columna">';
		print $form->select_produits_v('','product','',$conf->product->limit_size,0,-1,2,'',1,'','');
		print '</div>';

		print '<div class="contenedor-columna">';
		print '<input  id="qty" type="text"  name="qty"  style="width:200px;" />';
		print '</div>';

		print '<div class="contenedor-columna">';
		print '<input id="btn_enviar" name="btn_enviar" class="btn_enviar button" type="button" value="+" />';
		print '</div>';

		print '</div>';
		print '</div>';
		print '</form>';
		print '<div class="clearleft"></div>';
		$vclass = false;

		foreach((array) $pustr->lines AS $i => $line)
		{
			//print '<h2>'.$line->ref.'</h2>';
			print '<div id="carga'.$line->ref.'" class="width100">';
			print '<div class="contenedor-tabla width100">';

			print '<div class="contenedor-fila">';
			print '<div class="contenedor-columna width25">'.$line->detail.'</div>';
			print '<div class="contenedor-columna width10">'.$langs->trans('Product').'</div>';
			print '<div class="contenedor-columna width10">'.$langs->trans('Unit').'</div>';
			print '<div class="contenedor-columna width05">'.$langs->trans('Qty').'</div>';
			print '<div class="contenedor-columna width05">'.$langs->trans('Action').'</div>';
			print '</div>';

			$var = true;
			foreach ((array) $aDatamat[$id][$line->ref] AS $i => $data)
			{
				$var = !$var;
				print '<div class="contenedor-fila" id="service'.$i.'" data="'.$i.'">';
				$taskadd->fetch($data['fk_task']);
				if ($taskadd->id == $data['fk_task'])
					print '<div class="contenedor-columna">'.$taskadd->ref.'</div>';
				else
					print '<div class="contenedor-columna">&nbsp;</div>';
				$product->fetch($data['fk_product']);
				$unit = '';
				if ($product->id == $data['fk_product'])
				{
					$unit = $product->getLabelOfUnit('short');
					print '<div class="contenedor-columna">'.$product->label.'</div>';
				}
				else
					print '<div class="contenedor-columna">&nbsp;</div>';
				print '<div class="contenedor-columna" align="right">'.$unit.'</div>';
				print '<div class="contenedor-columna" align="right">'.$data['qty'].'</div>';
				print '<div class="contenedor-columna">';
				print '<a href="#" class="delete" onclick="javascript: borrar_legajo(this,'."'".$line->ref."'".')" id="delete'.$i.'">'.$langs->trans('Eliminar').'</a>';
				print '</div>';
				print '</div>';
			}
			print '</div>';
			print '</div>';
		}
		print '</div>';
	}
	else
	{


	//mostramos el resumen del consumo global
	//dol_fiche_head();

		print '<div class="fichecenter fichecenterbis">';

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
		foreach((array) $pustr->lines AS $i => $line)
		{
			print '<tr>';
			print '<td>'.$line->detail.'</td>';
			print '<td>'.price(0).'</td>';
			print '</tr>';
		}
		print '</tbody>';
		print '</table>';
		print '</div>';
		print '</div>';

		print '<div class="fichehalfright">';
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

		foreach((array) $pustr->lines AS $i => $line)
		{
			print '<tr>';
			print '<td>'.$line->detail.'</td>';
			print '<td>'.price(0).'</td>';
			print '</tr>';
		}
		print '</tbody>';
		print '</table>';
		print '</div>';
		print '</div>';

		print '</div>';
	//dol_fiche_end();
	}

}

llxFooter();

$db->close();
