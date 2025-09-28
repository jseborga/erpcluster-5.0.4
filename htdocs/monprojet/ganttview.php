<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/projet/ganttview.php
 *	\ingroup    projet
 *	\brief      Gantt diagramm of a project
 */

require ("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskbase.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskdepends.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
include_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';

$id=GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$project_id = $id;
$mode = GETPOST('mode', 'alpha');
$mine = ($mode == 'mine' ? 1 : 0);
//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$object = new Project($db);
if ($ref)
{
	$object->fetch(0,$ref);
	$id=$object->id;
}

// Security check
$socid=0;
if ($user->societe_id > 0) $socid=$user->societe_id;
$result = restrictedArea($user, 'projet', $id);
if (!$user->rights->monprojet->gantt->leer)
	accessforbidden();
$langs->load("users");
$langs->load("projects");


/*
 * Actions
 */

// None


/*
 * View
 */

$form=new Form($db);
$formother=new FormOther($db);
$userstatic=new User($db);
$companystatic=new Societe($db);
$task        = new Taskext($db);
$object      = new Projectext($db);
$taskbase    = new Projettaskbase($db);
$taskdepends = new Projettaskdepends($db);
$extrafields = new ExtraFields($db);

$extralabels=$extrafields->fetch_name_optionals_label($task->table_element);

$arrayofcss=array('/monprojet/css/style.css','/monprojet/css/jsgantt.css');

if (! empty($conf->use_javascript_ajax))
{
	$arrayofjs=array(
		'/monprojet/js/jsgantt.js',
		'/monprojet/js/graphics.js',

		'/projet/jsgantt_language.js.php?lang='.$langs->defaultlang
		);
}

$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
llxHeader("",$langs->trans("Tasks"),$help_url,'',0,0,$arrayofjs,$arrayofcss);

if ($id > 0 || ! empty($ref))
{
	$object->fetch($id,$ref);
	if ($object->societe->id > 0)  $result=$object->societe->fetch($object->societe->id);

	// To verify role of users
	//$userAccess = $object->restrictedProjectArea($user,'read');
	$userWrite  = $object->restrictedProjectArea($user,'write');
	//$userDelete = $object->restrictedProjectArea($user,'delete');
	//print "userAccess=".$userAccess." userWrite=".$userWrite." userDelete=".$userDelete;


	$tab='Gantt';

	$head=project_prepare_head($object);
	dol_fiche_head($head, $tab, $langs->trans("Project"),0,($object->public?'projectpub':'project'));

	$param=($mode=='mine'?'&mode=mine':'');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/monprojet/list.php">'.$langs->trans("BackToList").'</a>';

	// Ref
	print '<tr><td width="30%">';
	print $langs->trans("Ref");
	print '</td><td>';
	// Define a complementary filter for search of next/prev ref.
	if (! $user->rights->projet->all->lire)
	{
		$projectsListId = $object->getMonProjectsAuthorizedForUser($user,$mine,0);
		$object->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
	}
	print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref', '', $param);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Label").'</td><td>'.$object->title.'</td></tr>';

	// print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
	// if (! empty($object->societe->id)) print $object->societe->getNomUrl(1);
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

	// 	// Date start
	// 	print '<tr><td>'.$langs->trans("DateStart").'</td><td>';
	// 	print dol_print_date($object->date_start,'day');
	// 	print '</td></tr>';

	// 	// Date end
	// 	print '<tr><td>'.$langs->trans("DateEnd").'</td><td>';
	// 	print dol_print_date($object->date_end,'day');
	// 	print '</td></tr>';


	print '</table>';

	print '</div>';
}


/*
 * Actions
 */
print '<div class="tabsAction">';

if ($user->rights->projet->all->creer || $user->rights->projet->creer)
{
	if ($object->public || $userWrite > 0)
	{
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/projet/tasks.php?id='.$object->id.'&action=create'.$param.'&tab=gantt&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('AddTask').'</a>';
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
if ($object->public || $userWrite > 0)
{
	print '<a class="butAction" href="'.DOL_URL_ROOT.'/monprojet/export_xml.php?id='.$object->id.'">'.$langs->trans('Exportprojet').'</a>';
}
else
{
	print '<a class="butActionRefused" href="#" title="'.$langs->trans("NotOwnerOfProject").'">'.$langs->trans('Exportprojet').'</a>';
}


print '</div>';

print '<br>';

// Get list of tasks in tasksarray and taskarrayfiltered
// We need all tasks (even not limited to a user because a task to user
// can have a parent that is not affected to him).
$tasksarray = $task->getTasksArray(0, 0, $object->id, $socid, 0);
// We load also tasks limited to a particular user
//$tasksrole=($_REQUEST["mode"]=='mine' ? $task->getUserRolesForProjectsOrTasks(0,$user,$object->id,0) : '');
//var_dump($tasksarray);
//var_dump($tasksrole);
// for ($x=0;$x < 1000; $x++)
//   $aAbc[$x] = $x;
$aTaskinicio = array();
$aTaskcero = array();
$aTaskda = array();
if (count($tasksarray)>0)
{
	//vaciamos array para ruta critica
	$aRuta = array();
	$aHito = array();
	// Show Gant diagram from $taskarray using JSGantt

	$dateformat=$langs->trans("FormatDateShort");				// Used by include ganttchart.inc.php later
	$dateformat=$langs->trans("FormatDateShortJQuery");			// Used by include ganttchart.inc.php later
	$array_contacts=array();
	$tasks=array();
	$aTaskref = array();
	$project_dependencies=array();
	$taskcursor=0;
	// echo '<pre>';
	// print_r($tasksarray);
	// echo '</pre>';
	foreach($tasksarray as $key => $val)
	{
		$aDatearray[$val->date_start][$val->date_end] = $val;
	}
	ksort($aDatearray);
	$last = 0;
	foreach ((array) $aDatearray AS $datei => $aD)
	{
		foreach($aD AS $datef => $oj)
	  //echo '<hr> '.$datei.' '.$datef.' '.$oj->id;
			$last = $oj->id;

	}
	foreach($tasksarray as $key => $val)
	{
	//echo '<hr>INICIANDO CIN ID '.$val->id;
	//buscamos en el taskbase
		$taskbase->fetch('',$val->id);
		$task->fetch($val->id);
		$res=$task->fetch_optionals($task->id,$extralabels);
		if ($val->date_end > $val->date_start)
		{
			$dif = $val->date_end-$val->date_start;
			$numday = floor(abs($dif /(60*60*24)));
		//$numday = date('d',$val->date_end-$val->date_start);
		}
		else
			$numday = 0;
		$numday++;
	//puntos con tiempo
		$aTask[$val->id] = $numday;
		$respons = '';
		foreach(array('internal','external') as $source)
		{
			$tab = $task->liste_contact(-1,$source);
		//echo '<hr>num '.$num=count($tab);

			$i = 0;
			while ($i < $num)
			{
				if ($tab[$i]['libelle'] == 'Responsable')
				{
					$respons = $tab[$i]['lastname'].' '.$tab[$i]['firstname'];
				}
				$i ++;
			}
		}

		$tasks[$taskcursor]['task_resp'] = $respons;

		$tasks[$taskcursor]['task_id']=$val->id;
		$tasks[$taskcursor]['task_parent']=$val->fk_parent;
	//campo extrafield
		$tasks[$taskcursor]['task_is_group']=($task->array_options['options_c_grupo']>0?$task->array_options['options_c_grupo']:0);
		$tasks[$taskcursor]['task_depend'] = $task->array_options['options_c_depend'];
		$tasks[$taskcursor]['task_caption'] = $task->fk_task_parent;
		$tasks[$taskcursor]['task_milestone']=0;
		$tasks[$taskcursor]['task_percent_complete']=$val->progress;
	//$tasks[$taskcursor]['task_name']=$task->getNomUrl(1);
		$label = $val->label;
		if (strlen($val->label)>30) $label = substr($val->label,0,30).'...';
		$tasks[$taskcursor]['task_name']=$label;
		$tasks[$taskcursor]['task_name_lg']=$val->label;
		$tasks[$taskcursor]['task_start_date']=$val->date_start;
		$tasks[$taskcursor]['task_end_date']=$val->date_end;
		$tasks[$taskcursor]['task_color']='b4d1ea';
		$tasks[$taskcursor]['task_start_dateb']=$taskbase->date_start;
		$tasks[$taskcursor]['task_end_dateb']=$taskbase->date_end;
		$tasks[$taskcursor]['task_colorb']='ffff00';


		$idofusers=$task->getListContactId('internal');
		///print_r($idofusers);
		$idofthirdparty=$task->getListContactId('external');

	//dependencies
	// if ($task->fk_task_parent)
	//   $project_dependencies[]=array(0=>$val->id, 1=>$task->fk_task_parent);
	// //$project_dependencies[]=array(0=>$task->fk_task_parent, 1=>$val->id);

	//DEPENDENCIAS

		$taskdepends->getlist($val->id);
	//tarea con inicio 0
		if (count($taskdepends->array)<=0 &&
			$task->array_options['options_c_grupo'] != 1)
		{
			$aTaskinicio[$val->id]= array(0,$numday);
			$aTaskcero[$val->id]= array(0,$numday);
		}
		if (count($taskdepends->array) > 0)
		{
			foreach ((array) $taskdepends->array AS $j => $objdep)
			{
				$project_dependencies[]=array(0=>$val->id, 1=>$objdep->fk_task_depends);

		//armamos los camimos a seguir para la ruta critica
		// $aHito[$objdep->fk_task_depends] = 0;
		// $aHito[$val->id] = 0;
				if ($val->date_end > $val->date_start)
				{
			//echo '<br>dif '.
					$dif = $val->date_end-$val->date_start;
					$numday = floor(abs($dif /(60*60*24)));
			//$numday = date('d',$val->date_end-$val->date_start);
				}
				else
					$numday = 0;
				$numday++;
		// $aRuta[$objdep->fk_task_depends][$val->id] = $numday;
				$aRutaini[$objdep->fk_task_depends.'_'.$val->id] = $numday;
		//nueva forma de dependencia
				$aDeptask[$val->id][$objdep->fk_task_depends]=$objdep->fk_task_depends;
				$aTaskda[$objdep->fk_task_depends][$val->id] = $val->id;
				$aTaskad[$val->id][$objdep->fk_task_depends] = $objdep->fk_task_depends;
		//buscamos de quien depende
				$aTaskinicio[$val->id] = array(0,
					$numday);
		//$aTaskfin[$val->id] = array(0,0);
			}
		}

	//FIN DEPENDENCIA

		$s='';
		if (count($idofusers)>0)
		{
			$s.=$langs->trans("Internals").': ';
			$i=0;
			foreach($idofusers as $valid)
			{
				$userstatic->fetch($valid);
				if ($i) $s.=',';
				$s.=$userstatic->login;
				$i++;
			}
		}
		if (count($idofusers)>0 && (count($idofthirdparty)>0)) $s.=' - ';
		if (count($idofthirdparty)>0)
		{
			if ($s) $s.=' - ';
			$s.=$langs->trans("Externals").': ';
			$i=0;
			foreach($idofthirdparty as $valid)
			{
				$companystatic->fetch($valid);
				if ($i) $s.=',';
				$s.=$companystatic->name;
				$i++;
			}
		}
		if ($s) $tasks[$taskcursor]['task_resources']='<a href="'.DOL_URL_ROOT.'/projet/tasks/contact.php?id='.$val->id.'&withproject=1" title="'.dol_escape_htmltag($s).'">'.$langs->trans("List").'</a>';
		$tasks[$taskcursor]['task_resources'] = $respons;
	//print "xxx".$val->id.$tasks[$taskcursor]['task_resources'];
		$taskcursor++;
	}

	// echo '<hr>ultima tarea '.$last;
	// echo '<br>Inicio<pre>';
	// print_r($aTaskcero);
	// print_r($aTask);
	// // echo '<hr>tarkinicio';
	// // print_r($aTaskinicio);
	// // echo '<hr>adeptask';
	// // //print_r($aDeptask);
	// echo 'DA<br>';
	// print_r($aTaskda);
	// echo 'AD<br>';
	// print_r($aTaskad);
	// echo '</pre>';

	//procesamos el inicio del array
	foreach ((array) $aTaskcero AS $j => $data)
	{
		$k = $j;
		$valor = $aTask[$j];

		list($k,$aTaskinicio) = nextsearch($j,
			$aTaskinicio,
			$aTask,
			$aTaskda);
	}
	//ruta critica de inicio a fin
	// echo '<br>ruta critica de inicio a fin';
	// echo '<pre>';
	// print_r($aTaskinicio);
	// echo '</pre>';
	//retrocedemos buscando el mejor camino
	//empezamos por el ultimo
	//$aTaskfin = $aTaskinicio;
	$nResta = $aTaskinicio[$last][1];
	$k = $last;
	//valor1 es del inicio
	$kobackup = $ko;
	$lOk = false;
	$aTaskfin = array();
	$valor = $nResta;
	$nIni = $aTask[$k];
	//restamos
	$nDif = $valor - $nIni;
	// echo '<br> el id '.$k. ' tiene el valor '.$valor.' valordelmismo '.$nIni;
	// echo '<br>reemplazamos la resta en 0: '.$nDif;
	$aTaskfin[$k][0] = $nDif;
	$aTaskfin[$k][1] = $valor;
	//recorremos a su dependiente
	$valor = $nDif;

	//    echo '<hr>empezamos el retorno con '.$last.' enviandovalor '.$nResta;
	list($k,$aTaskfin) = lastsearch($last,
		$last,
		$nDif,
		$aTaskfin,
		$aTask,
		$aTaskad);
	// echo '<br>ruta critica de retorno';
	// echo '<pre>';
	// print_r($aTaskfin);
	// echo '</pre>';
	//con el resultado obtenido
	//por ultimo defino la ruta critica
	//primero encuentro quien tiene resultado 0
	$nInicio = 0;
	$nvuelta = 0;
	foreach ((array) $aTaskcero AS $j => $aNx)
	{
		$nvuelta++;
		if ($aTaskfin[$j][0] == 0)
		{
			if ($nInicio>0)
		  $ax = 1; //echo '<hr>error, revise existen mas de 1 en '.$nvuelta;
		else
			$nInicio = $j;
	}
}
	//    echo '<hr>Ruta de inicio '.$nInicio;
if ($nInicio>0)
{
	$nf = 0;
	$aFinal[$nInicio] = $nInicio;
	$nValor = $aTaskfin[$nInicio][1];
	$lLoop = true;
	while ($lLoop == true)
	{
		//busco nInicio
		$aSearch = $aTaskda[$nInicio];
		if (count($aSearch)>0)
		{
			foreach ($aSearch AS $k => $aValx)
			{
				$aVal = $aTaskfin[$k];
			//echo '<br>ninicio '.$nInicio.' | '.$k.' | '.$aVal[0].'|'.$nValor;
				if (price2num($aVal[0]) == price2num($nValor))
				{
			//echo '<br>igualan los valores ';
					$aFinal[$k] = $k;
					$nValor = $aVal[1];
					$nInicio= $k;
				}
				else
				{
			//echo '<br>no iguala';
				}
			}
			$nf++;
			if ($nf > 30)
				$lLoop = false;
		}
		else
			$lLoop = false;
	}
}
	// echo '<hr>RUTA CRITICA FINaL';
	// echo '<pre>';
	// print_r($aFinal);
	// echo '</pre>';
	// exit;
	//$aRutad = $aRutaini;
	//verificamos valores de dependencia final
$aNew = array();

	// foreach ($aRutaini AS $k => $val)
	//   {
	// 	// echo '<hr>anew ';
	// 	// print_r($aNew);
	// 	list($ini,$fin) = explode('_',$k);
	// 	//buscamos en aTask
	// 	echo '<br>buscamos el ini '.$ini;
	// 	echo ' resultado valor ini '. $value = $aTask[$ini];
	// 	echo ' buscamos si no esta en Anew ';

	// 	if (!empty($aNew[$ini]))
	// 	  {

	// 	    echo '<hr>antes val '.$val;
	// 	    echo '<br>nuevo val '. $value = $aNew[$ini];
	// 	  }
	// 	else
	// 	  echo '<br>no esta ';
	// 	// if (empty($aNew[$fin]))
	// 	//   $aNew[$ini_$fin] = $value+$val;
	// 	// else
	// 	//   {
	// 	//     if ($aNew[$fin]< $value+$val)
	// 	//       $aNew[$fin] = $value+$val;
	// 	//   }
	//   }
	// echo '<hr>Depe<pre>';
	// print_r($aNew);
	// echo '</pre>';

	// //completamos los que no tienen dependencia
	// foreach ((array) $aRutaini AS $j => $value)
	//   {
	// 	//extraemos el indice
	// 	list($ini,$fin) = explode('_',$j);
	// 	// echo '<hr>buscamos '.$ini;
	// 	//echo '<br>res '.
	// 	$res = searchini($aRutaini,$ini);
	// 	//echo '<br>res '.$res = searchinival($aTask,$ini);
	// 	if ($res == 0)
	// 	  $aRutaini['0_'.$ini] = 0;
	//   }

	// if (count($aRutaini) > 0)
	//   ksort($aRutaini);

	// echo '<hr>primerarutaini valida<pre>';
	// print_r($aRutaini);
	// echo '</pre>';

	// //vamos a validar cual es la ruta mas larga
	// $aRes1 = array();
	// $aFin = array();
	// foreach ($aRutaini AS $j => $value)
	//   {
	// 	list($ini,$fin) = explode('_',$j);
	// 	//buscamos el valor de ini y fin
	// 	$nini = $aTask[$ini]+0;
	// 	$nfin = $aTask[$fin]+0;
	// 	if ($aFin[$ini])
	// 	  $nini = $aFin[$ini];
	// 	$nValor = $nini + $nfin;
	// 	//agregamos solo finales a un array
	// 	if (empty($aFin[$fin]))
	// 	    $aFin[$fin]=$nValor;
	// 	else
	// 	  {
	// 	    if ($aFin[$fin] > $nValor)
	// 	      {
	// 		//no hacemos nada
	// 	      }
	// 	    else
	// 	      {
	// 		$aFin[$fin] = $nValor;
	// 		$nValor = $aFin[$fin] + $nfin;
	// 	      }
	// 	  }
	// 	if ($ini == 0)
	// 	  $aRes1[$j] = 0;
	// 	else
	// 	  $aRes1[$j] = $nValor;
	//   }
	// //nuevamente revisar el resulta ares1 con el aFin
	// $aRes2 = array();
	// $aReversa = array();
	// foreach ((array) $aRes1 AS $j =>$val)
	//   {
	// 	list($ini,$fin) = explode('_',$j);
	// 	if ($val >= $aFin[$fin])
	// 	  $aRes2[$j] = $val;
	// 	$aReversa[$val][] = $j;
	//   }
	// foreach ((array) $aRes1 AS $j =>$val)
	//   {
	// 	list($ini,$fin) = explode('_',$j);
	// 	$aRes1f[$fin.'_'.$ini] = $val;
	//   }

	// echo '<hr>primeraruta sumada  valida<pre>';
	// print_r($aFin);
	// print_r($aRes1);
	// print_r($aRes1f);
	// //print_r($aRes2);
	// if (count($aReversa)>0)
	//   krsort($aReversa);
	// echo '<hr>reversa ';
	// print_r($aReversa);
	// echo '</pre>';
	// $aRev = $aReversa;
	//    print_r($aRev);
	//volvemos a armar el array aReversa
	// $aRes3 = array();
	// foreach ((array) $aRev AS $j => $aVal)
	// {
	//   foreach ($aVal AS $k => $val)
	//   	{
	//   	  echo '<br>val '.$val.' '.$j.' '.$k;
	//   	  $aRes3[$val] = $j;
	//   	}
	// }
	// echo '<pre>';
	// print_r($aRes3);
	// echo '</pre>';
	// $aRutainidef = array();
	// //resultado con sus valores sumados
	// foreach ((array) $aRutaini AS $k => $val)
	//   {
	// 	list($ini,$fin) = explode('_',$k);
	// 	//buscamos si se tiene eL $fin en la rutina
	// 	$res = search_mismo($aRutai,$fin);
	// 	//buscamos en Atask
	// 	$value = $aTask[$ini];
	// 	$aRutainidef[$k] = $val+$value;
	//   }
	// echo '<hr>primer resultado<pre>';
	// print_r($aRutainidef);
	// echo '</pre>';

	//$aRutafinc = $aRutaini;//ORIGINAL
$aRutafinc = $aRes1;
	//analizamos el resultado de aRuta
$aIni = array();
	//    $last = 0;


	// $aFindep = array();
	// $aInidep = array();
	// foreach ((array) $aRutaini AS $j => $valor)
	//   {
	// 	//buscamos en el array aINi la lineadest = lineaini
	// 	list($ini,$fin) = explode('_',$j);

	// 	if ($ini > 0)
	// 	  {
	// 	    if ($task->fetch($ini)>0)
	// 	      if ($task->id == $ini)
	// 		{
	// 		if ($task->date_end > $task->date_start)
	// 		  {
	// 		    $dif = $task->date_end-$task->date_start;
	// 		    $numday = floor(abs($dif /(60*60*24)));
	// 		  }
	// 		else
	// 		  $numday = 0;
	// 		  // if ($task->date_end > $task->date_start)
	// 		  //   $numday = date('d',$task->date_end-$task->date_start);
	// 		  // else
	// 		  //   $numday = 0;
	// 		}
	// 	    $tiempo = $numday + 1;

	// 	    $res = $aFindep[$ini]; //0  //4
	// 	    $result = $res + $tiempo;
	// 	    //	    echo '<br>calc ini= '.$ini.' fin '.$fin.' '.$result.' = '.$res.' + '.$tiempo;
	// 	    if ($aFindep[$fin] > 0)
	// 	      {
	// 		if ($aFindep[$fin] <= $result)
	// 		  $aFindep[$fin] = $result;
	// 	      }
	// 	    else
	// 	      {
	// 		$aFindep[$fin] = $result;
	// 	      }
	// 	  }
	// 	else
	// 	  {
	// 	    if (empty($aFindep[$fin]))
	// 	      {
	// 		$aFindep[$fin] = 0;
	// 	      }
	// 	  }
	// 	$aInidep[$ini]= $ini;
	// 	//	$last = $fin;
	//   }
	// echo '<hr>last '.$last;
	// echo '<hr>afinDep<pre>';
	// print_r($aInidep);
	// print_r($aFindep);

	// echo '</pre>';
	//    echo '<hr>last '.$last;
	//obtenemos el valor final
	// $valueMax = 0;
	// if ($task->fetch($last)>0)
	//   if ($task->id == $last)
	// 	{
	// 	  if ($task->date_end > $task->date_start)
	// 	    {
	// 	      $dif = $task->date_end-$task->date_start;
	// 	      $numday = floor(abs($dif /(60*60*24)));
	// 	    }
	// 	  else
	// 	    $numday = 0;
	// 	  // if ($task->date_end > $task->date_start)
	// 	  //   $numday = date('d',$task->date_end-$task->date_start);
	// 	  // else
	// 	  //   $numday = 0;
	// 	  $numday++;
	// 	  $valueMax = $aFindep[$last] + $numday;
	// 	}
	// echo '<hr>valuemax '.$valueMax;
	//       $aInimax = $aFindep;


	//INICIO FIN INICIAL
	// $aFindep = array();
	// foreach ((array) $aRutaini AS $j => $valor)
	//   {
	// 	//buscamos en el array aINi la lineadest = lineaini
	// 	list($ini,$fin) = explode('_',$j);
	// 	$res = searchini($aIni,$ini);
	// 	//obtenemos el valor del ini
	// 	if ($ini > 0)
	// 	  {
	// 	    if ($task->fetch($ini)>0)
	// 	      if ($task->id == $ini)
	// 		{
	// 		  if ($task->date_end > $task->date_start)
	// 		    $numday = date('d',$task->date_end-$task->date_start);
	// 		  else
	// 		    $numday = 0;
	// 		}
	// 	    $tiempo = $numday + 1;
	// 	  }
	// 	else
	// 	  $tiempo = 0;
	// 	echo '<hr>search '.$ini.' '.$fin. ' resultado '.$res.' tiempo calculado '.$tiempo;

	// 	$aIni[$ini.'_'.$fin] = $res + $tiempo;
	// 	If ($aFindep[$fin] <= ($res + $tiempo))
	// 	  $aFindep[$fin]= $res + $tiempo;
	// 	$aInidep[$ini]= $ini;
	// 	$last = $fin;
	//   }
	//FIN OPCION INICIAL

	// //para la conclusion del array
	// //verificamos quienes no tienen dependencia final
	// foreach ((array) $aFindep AS $j => $value)
	//   {
	// 	//buscamos en el array aInidep
	// 	if (empty($aInidep[$j]))
	// 	  {
	// 	    //buscamos el valor en task
	// 	    if ($task->fetch($j)>0)
	// 	      if ($task->id == $j)
	// 		{
	// 		  if ($task->date_end > $task->date_start)
	// 		    $numday = date('d',$task->date_end-$task->date_start);
	// 		  else
	// 		    $numday = 0;
	// 		}
	// 	    $tiempo = $numday + 1;
	// 	    //$aIni[$j.'_0'] = $value + $tiempo;
	// 	    $aFindep[$j] = $value + $tiempo;
	// 	  }
	//   }
	// echo '<pre>ini antes ';
	// print_r($aFindep);
	// echo '</pre>';
	// exit;
	// //completamos los saldos mayores a aIni
	// foreach ($aFindep AS $j => $maximo)
	//   {
	// 	foreach ($aIni AS $k => $value)
	// 	  {
	// 	    list($ini,$fin) = explode('_',$k);
	// 	    if ($j == $fin && $maximo >= $value)
	// 	      {
	// 		$aIni[$ini.'_'.$fin] = $maximo;
	// 	      }
	// 	  }
	//   }
$aInimax = $aFindep;


	// ///PARA EL RETORNO
	// $aRutafin = array();
	// foreach ((array) $aRutafinc AS $j => $value)
	//   {
	// 	list($ini,$fin) = explode('_',$j);
	// 	$aRutafin[$fin.'_'.$ini] = $value;
	//   }
	// krsort($aRutafin);
	// //completamos los que no tienen dependencia
	// foreach ((array) $aRutafin AS $j => $value)
	//   {
	// 	//extraemos el indice
	// 	list($ini,$fin) = explode('_',$j);
	// 	$res = searchini($aRutafin,$ini);
	// 	if ($res == 0)
	// 	  $aRutafin = array('0_'.$ini => $aInimax[$last]) + $aRutafin;
	//   }
	// echo '<hr>primerarutaFINvalida<pre>';
	// print_r($aRutafin);
	// echo '</pre>';

$aFindep = array();
$aInidep = array();
$aFin = array();

	// foreach ((array) $aRutafin AS $j => $valor)
	//   {
	// 	//buscamos en el array aINi la lineadest = lineaini
	// 	list($ini,$fin) = explode('_',$j);
	// 	if ($fin == 0)
	// 	  {
	// 	    if($aFindep[$ini] > 0)
	// 	      {
	// 		$res = $aFindep[$ini];
	// 		$result = $res - $valor;
	// 		if (!empty($aFindep[$fin]))
	// 		  {
	// 		    if ($aFindep[$fin] > $result)
	// 		      {
	// 			$aFindep[$fin] = $result;
	// 			$aFinmin[$ini] = $result;
	// 		      }
	// 		  }
	// 		else
	// 		  {
	// 		    $aFindep[$fin] = $result;
	// 		    $aFinmin[$ini] = $result;
	// 		  }
	// 	      }
	// 	  }
	// 	else
	// 	  {
	// 	    if (empty($aFindep[$fin]))
	// 	      {
	// 		if ($ini == 0)
	// 		  {
	// 		    // $aFindep[$fin] = $aInimax[$last];
	// 		    // $aFinmin[$ini] = $aInimax[$last];
	// 		    $aFindep[$fin] = $valueMax;
	// 		    $aFinmin[$ini] = $valueMax;
	// 		  }
	// 		else
	// 		  {
	// 		    $res = $aFindep[$ini];
	// 		    $aFindep[$fin] = $res - $valor;
	// 		    $aFinmin[$ini] = $res - $valor;;
	// 		  }
	// 	      }
	// 	    else
	// 	      {
	// 		$ant = $aFindep[$fin]; // 83 -> 20
	// 		$res = $aFindep[$ini]; // 85 -> 33
	// 		$result = $res - $valor; // 33 - 10 = 22
	// 		if ($ant > $result)
	// 		  {
	// 		    $aFindep[$fin] = $result;
	// 		    $aFinmin[$ini] = $result;
	// 		  }
	// 	      }
	// 	  }
	// 	$aInidep[$fin]= $fin;
	//   }

print "\n";

$aRes = $aFinal;
	// 	//definimos la ruta critica
	// $aRes = array();
	// foreach($aInimax AS $j => $value)
	//   {
	// 	if ($aFinmin[$j] == $value)
	// 	  $aRes[$j] = $j;
	//   }

	// echo '<hr><hr><pre>';
	// print_r($aInimax);
	// print_r($aFinmin);
	// print '<hr>resultdo';
	// print_r($aRes);
	// echo '</pre>';
	// $aFinres = array();
	// $nLoop = 1;
	// foreach((array) $aFin AS $linea => $value)
	//   {
	//     list($end,$ini) = explode('_',$linea);

	//     if ($nLoop == 1) $aFinres[$end.'_'.$ini] =
	//     $nLoop++;
	//   }

	// $nLoop = 1;
	// foreach ((array) $aRuta AS $lineaini => $aDest)
	//   {
	//     foreach ((array) $aDest AS $lineadest => $tiempo)
	//       {
	// 	//buscamos en el array aFin la lineadest = lineaini
	// 	echo '<br>'.$linedest;
	// 	if ($nLoop == 1) $res = $aInimax[$last];
	// 	$res = searcharrayfin($aFin,$lineaini,$res);
	// 	$aFin[$lineadest.'_'.$lineaini]-=$tiempo-$res;
	//       }
	//     $nLoop++;
	//   }
	// //	print_r($aFin);
	// //revisamos el aFin para eliminar los mayores en linedest
	// $aFinmax = array();
	// $aFinmax[$last] = $aInimax[$last];
	// foreach ($aFin AS $j => $val)
	//   {
	//     list($ini,$end) = explode('_',$j);
	//     if ($aFinmax[$end] <= $val)
	//       $aInimax[$end] = $val;
	//   }








if (! empty($conf->use_javascript_ajax))
{
	  //var_dump($_SESSION);
	print '<div id="tabs" class="ganttcontainer" style="border: 1px solid #ACACAC;">'."\n";

	include_once DOL_DOCUMENT_ROOT.'/monprojet/ganttchart.inc.php';
		//include_once DOL_DOCUMENT_ROOT.'/monprojet/ganttchart.inc1.php';
	print '</div>'."\n";
}
else
{
	$langs->load("admin");
	print $langs->trans("AvailableOnlyIfJavascriptAndAjaxNotDisabled");
}
}
else
{
	print $langs->trans("NoTasks");
}


llxFooter();

$db->close();

function nextsearch($k,$aTaskinicio,$aTask,$aTaskda)
{
	$aDea = $aTaskda[$k];
  //  print_r($aDea);
	if (count($aDea)>1)
	{
		$nBackup = $nSuma;
		$kBackup = $k;
		foreach ((array) $aDea AS $l)
		{
	  //busco el valor en aTask
			$valor = $aTaskinicio[$kBackup][1];
	  // echo '<br> el id '.$k. ' tiene el valor '.$valor;
	  //buscamos que valor tiene $l
			$nIni = $aTaskinicio[$l][0];
	  //echo '<br>valor de inicio '.$l.' '.$nIni;
			if ($valor > $nIni)
			{
		  //  echo '<br>se cumple '.$valor.' > '.$nIni.' y reemplazamos ';
		  //reemplazamos
				$aTaskinicio[$l][0] = $valor;
				$nValue = $aTask[$l];

		  //el valor final es la suma
				$nSuma = $valor + $nValue;
		  //echo '<br>la suma es '.$nSuma;
				$aTaskinicio[$l][1] = $nSuma;
			}
	  //el nuevo valor de $k
			$k = $l;
	  // echo '<br>el nuevo valor de k es '.$k;
	  // echo '<hr>'.$k.' 1entramos de nuevo al nextsearch';
			list($k,$aTaskinicio) = nextsearch($k,
				$aTaskinicio,
				$aTask,
				$aTaskda);

		}
	}
	else
	{
		foreach ((array) $aDea AS $l)
		{
	  //busco el valor en aTask
			$valor = $aTaskinicio[$k][1];
	  // echo '<br> el id '.$k. ' tiene el valor '.$valor;
	  //buscamos que valor tiene $l
			$nIni = $aTaskinicio[$l][0];
	  //echo '<br>valor de inicio '.$l.' '.$nIni;
			if ($valor > $nIni)
			{
		  //  echo '<br>se cumple '.$valor.' > '.$nIni.' y reemplazamos ';
		  //reemplazamos
				$aTaskinicio[$l][0] = $valor;
				$nValue = $aTask[$l];

		  //el valor final es la suma
				$nSuma = $valor + $nValue;
		  //echo '<br>la suma es '.$nSuma;
				$aTaskinicio[$l][1] = $nSuma;
			}
	  //el nuevo valor de $k
			$k = $l;
	  // echo '<br>el nuevo valor de k es '.$k;
	  // echo '<hr>'.$k.' 1entramos de nuevo al nextsearch';
			list($k,$aTaskinicio) = nextsearch($k,
				$aTaskinicio,
				$aTask,
				$aTaskda);
		}

	}
	return array($k,$aTaskinicio);
}

function lastsearch($ko,$k,$nResta,
	$aTaskfin,$aTask,$aTaskad)
{
	$valor = $nResta;
	$aDea = $aTaskad[$k];
  //  print_r($aDea);
  //  print_r($aDea);
	if (count($aDea)>1)
	{
		$nBackup = $valor;
		$kBackup = $k;
	  //  echo '<br>son varios '.$lOk;
		foreach ((array) $aDea AS $l)
		{
	  //busco el valor en aTask
			$valor = $aTaskfin[$kBackup][0];
			$nval = $aTask[$l];
			$nDif = $valor - $nval;
	  //busco si existe en el array
			if (count($aTaskfin[$l])>0)
			{
				$nFin = $aTaskfin[$l][1];
		  //existe y analizamos
				if ($valor < $nFin)
				{
		  //echo '<br>buscamos el valor '.$l;
		  //echo '<br>valor backup '.
					$valor = $nBackup;
		  // echo '<br> el id '.$l. ' tiene el valor0 '.$valor;
		  // echo '<br>valor de inicio '.$l.' '.$nval;
		  //reemplazamos
		  // echo '<br>la resta '.$nDif;
					$aTaskfin[$l][0] = $nDif;
					$aTaskfin[$l][1] = $valor;
				}
			}
			else
			{
		  //no existe y agregamos sin analizar
				$aTaskfin[$l][0] = $nDif;
				$aTaskfin[$l][1] = $valor;
			}
	  //el nuevo valor de $k
			$k = $l;
	  // echo '<br>el nuevo valor de k es '.$k;
	  // echo '<hr>'.$k.' 1entramos de nuevo al lasttsearch';
	  // if ($lOk)
	  // 	$nResta = $nBackup;
	  // else
	  // 	{
			$nResta = $nDif;
	  //}
			list($k,$aTaskfin) = lastsearch($ko,
				$k,
				$nResta,
				$aTaskfin,
				$aTask,
				$aTaskad);
		}
	}
	else
	{
	  //echo '<hr>es unico';
		foreach ((array) $aDea AS $l)
		{
	  //busco el valor en aTask
			$valor = $aTaskfin[$k][0];
			$nval = $aTask[$l];
			$nDif = $valor - $nval;
	  //si existe recuperamos el valor de $l
			if (count($aTaskfin[$l])>0)
			{
		  //existe y analizamos
				$nFin = $aTaskfin[$l][1];
				if ($valor < $nFin)
				{
		  // echo '<br>buscamos el id '.$l;
		  // echo '<br> el valor mayor es '.$valor;
		  // //buscamos que valor tiene $l
		  // echo '<br>valor menor de inicio '.$l.' '.$nval;
		  // //reemplazamos
		  // echo '<br>la resta '.$nDif;
					$aTaskfin[$l][0] = $nDif;
					$aTaskfin[$l][1] = $valor;
				}
			}
			else
			{
		  //agregamos sin analizar
				$aTaskfin[$l][0] = $nDif;
				$aTaskfin[$l][1] = $valor;
			}

	  //el nuevo valor de $k
			$k = $l;
	  // echo '<br>el nuevo valor de k es '.$k;
	  // echo '<hr>'.$k.' 1entramos de nuevo al lasttsearch';
			list($k,$aTaskfin) = lastsearch($ko,
				$k,
				$nDif,
				$aTaskfin,
				$aTask,
				$aTaskad);
		}
	}

	return array($k,$aTaskfin);
}

?>