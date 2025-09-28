<?php
/* Copyright (C) 2006-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010      Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2011      Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2014      Ramiro Queso Cusi    <ramiroques@gmail.com>
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
 * or see http://www.gnu.org/
 */

/**
 *	    \file       htdocs/monprojet/lib/monproject.lib.php
 *		\brief      Functions used by monproject module
 *      \ingroup    monprojet
 */

function monprojetgroup_prepare_head($object,$lUser=false)
{
	global $langs, $conf, $user, $taskstatic, $taskadd, $objecttaskadd;
	$h = 0;
	$head = array();
	$langs->load('monprojet@monprojet');
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_projet = ".$object->id;
	$res = $taskadd->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic,false);
	if ($res > 0)
	{
		foreach ($taskadd->lines AS $i => $line)
		{
			$objecttaskadd->fetch('',$line->id);
			if ($objecttaskadd->fk_task == $line->id && $objecttaskadd->c_grupo == 1)
			{
				//verificamos permisos
				if ($lUser)
					{	$taskstatic->fetch($line->id);
						$resuser = verifcontacttask($user,$taskstatic,'res',0);
						if ($resuser)
						{
							$head[$h][0] = dol_buildpath("/monprojet/budget.php?id=".$object->id.'&tasksel='.$objecttaskadd->id."&amp;action=itemproj",1);
							$head[$h][1] = $line->ref;
							$head[$h][2] = $line->ref;
							$head[$h][3] = $line->ref.'xa';
							$head[$h][4] = $line->ref.'b3';
							$h++;
						}
					}
					else
					{
						$head[$h][0] = dol_buildpath("/monprojet/budget.php?id=".$object->id.'&tasksel='.$objecttaskadd->id."&amp;action=itemproj",1);
						$head[$h][1] = $line->ref;
						$head[$h][2] = $line->ref;
						$head[$h][3] = $line->ref.'xa';
						$head[$h][4] = $line->ref.'b3';
						$h++;
					}
				}
			}
		}
		complete_head_from_modules($conf,$langs,$object,$head,$h,'monprojet');
		return $head;
	}
/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @return  array				Array of tabs to shoc
 */
function monproject_prepare_head($object)
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/projet/fiche.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Project");
	$head[$h][2] = 'project';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/projet/contact.php?id='.$object->id;
	$head[$h][1] = $langs->trans("ProjectContact");
	$head[$h][2] = 'contact';
	$h++;

	if (! empty($conf->fournisseur->enabled) || ! empty($conf->propal->enabled) || ! empty($conf->commande->enabled)
		|| ! empty($conf->facture->enabled) || ! empty($conf->contrat->enabled)
		|| ! empty($conf->ficheinter->enabled) || ! empty($conf->agenda->enabled) || ! empty($conf->deplacement->enabled))
	{
		$head[$h][0] = DOL_URL_ROOT.'/projet/element.php?id='.$object->id;
		$head[$h][1] = $langs->trans("ProjectReferers");
		$head[$h][2] = 'element';
		$h++;
	}

  // Show more tabs from modules
  // Entries must be declared in modules descriptor with line
  // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
  // $this->tabs = array('entity:-tabname);   												to remove a tab
	moncomplete_head_from_modules($conf,$langs,$object,$head,$h,'project');

	if (empty($conf->global->MAIN_DISABLE_NOTES_TAB))
	{
		$nbNote = 0;
		if(!empty($object->note_private)) $nbNote++;
		if(!empty($object->note_public)) $nbNote++;
		$head[$h][0] = DOL_URL_ROOT.'/projet/note.php?id='.$object->id;
		$head[$h][1] = $langs->trans('Notes');
		if($nbNote > 0) $head[$h][1].= ' ('.$nbNote.')';
		$head[$h][2] = 'notes';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	$upload_dir = $conf->projet->dir_output . "/" . dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
	$head[$h][0] = DOL_URL_ROOT.'/projet/document.php?id='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if($nbFiles > 0) $head[$h][1].= ' ('.$nbFiles.')';
	$head[$h][2] = 'document';
	$h++;

  // Then tab for sub level of projet, i mean tasks
	$head[$h][0] = DOL_URL_ROOT.'/projet/tasks.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Tasks");
	$head[$h][2] = 'tasks';
	$h++;

  /* Now this is a filter in the Task tab.
   $head[$h][0] = DOL_URL_ROOT.'/projet/tasks.php?id='.$object->id.'&mode=mine';
   $head[$h][1] = $langs->trans("MyTasks");
   $head[$h][2] = 'mytasks';
   $h++;
  */

   $head[$h][0] = DOL_URL_ROOT.'/monprojet/ganttview.php?id='.$object->id;
   $head[$h][1] = $langs->trans("Gantt");
   $head[$h][2] = 'gantt';
   $h++;

  // $head[$h][0] = DOL_URL_ROOT.'/monprojet/summary.php?id='.$object->id;
  // $head[$h][1] = $langs->trans("Summary");
  // $head[$h][2] = 'gantt';
  // $h++;

   moncomplete_head_from_modules($conf,$langs,$object,$head,$h,'project','remove');

   return $head;
}

/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @return  array				Array of tabs to show
 */
function montask_prepare_head($object)
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/monprojet/task/task.php?id='.$object->id.(GETPOST('withproject')?'&withproject=1':'');;
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'task_task';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/projet/tasks/contact.php?id='.$object->id.(GETPOST('withproject')?'&withproject=1':'');;
	$head[$h][1] = $langs->trans("TaskRessourceLinks");
	$head[$h][2] = 'task_contact';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/projet/tasks/time.php?id='.$object->id.(GETPOST('withproject')?'&withproject=1':'');;
	$head[$h][1] = $langs->trans("TimeSpent");
	$head[$h][2] = 'task_time';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname);   												to remove a tab
	moncomplete_head_from_modules($conf,$langs,$object,$head,$h,'task');

	if (empty($conf->global->MAIN_DISABLE_NOTES_TAB))
	{
		$nbNote = 0;
		if(!empty($object->note_private)) $nbNote++;
		if(!empty($object->note_public)) $nbNote++;
		$head[$h][0] = DOL_URL_ROOT.'/projet/tasks/note.php?id='.$object->id.(GETPOST('withproject')?'&withproject=1':'');;
		$head[$h][1] = $langs->trans('Notes');
		if ($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
		$head[$h][2] = 'task_notes';
		$h++;
	}

	$head[$h][0] = DOL_URL_ROOT.'/projet/tasks/document.php?id='.$object->id.(GETPOST('withproject')?'&withproject=1':'');;
	$filesdir = $conf->projet->dir_output . "/" . dol_sanitizeFileName($object->project->ref) . '/' .dol_sanitizeFileName($object->ref);
	include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	$listoffiles=dol_dir_list($filesdir,'files',1,'','thumbs');
	$head[$h][1] = (count($listoffiles)?$langs->trans('DocumentsNb',count($listoffiles)):$langs->trans('Documents'));
	$head[$h][2] = 'task_document';
	$h++;

	moncomplete_head_from_modules($conf,$langs,$object,$head,$h,'task','remove');

	return $head;
}

function searcharrayini($aIni,$lineaini)
{
	global $task;
	$res = 0;
	//buscamos el inicio de cuantos dias es
	if($task->fetch($lineaini)>0)
	{
		if ($task->id == $lineaini)
		{
			if($task->date_end>$task->date_start)
				$numday = date('d',$task->date_end-$task->date_start);
			else
				$numday=0;
			$numday++;
		}
	}
	foreach ((array) $aIni AS $i => $value)
	{

		list($ini,$end) = explode('_',$i);
		if ($end == $lineaini)
		{
			$res = $value + $numday;
		}
	}
	return $res;
}

function searcharrayini2($aIni,$lineaini,$tiempo)
{
	$res = 0;
	foreach ((array) $aIni AS $i => $value)
	{
		if ($i == $lineaini)
		{
			if ($tiempo >= $value)
				$res = $tiempo;
			else
				$res = $value;
		}
	}
	return $res;
}

function searchini($aIni,$linea)
{
	$res = 0;
	foreach ((array) $aIni AS $i => $value)
	{
		list($ini,$fin) = explode('_',$i);
		if ($fin == $linea)
		{
	  //	  echo '<br>es igual '.$fin .' == '.$linea;
			$res = $value;
		}
	}
	return $res;
}

function searchinival($aIni,$linea)
{
	$res = 0;
	foreach ((array) $aIni AS $i => $value)
	{
	  //list($ini,$fin) = explode('_',$i);
		if ($i == $linea)
		{
	  //	  echo '<br>es igual '.$fin .' == '.$linea;
			$res = $value;
		}
	}
	return $res;
}

function search_mismo($aIni,$linea)
{
	$res = 0;
	foreach ((array) $aIni AS $i => $value)
	{
		list($ini,$fin) = explode('_',$i);
		if ($ini == $linea)
		{
	  //	  echo '<br>es igual '.$fin .' == '.$linea;
			$res = $value;
		}
	}
	return $res;
}

function searchfin($aIni,$linea)
{
	$res = 0;
	foreach ((array) $aIni AS $i => $value)
	{
		list($ini,$fin) = explode('_',$i);
		if ($fin == $linea)
		{
			$res = $value;
		}
	}
	return $res;
}
function searchfim($aIni,$linea)
{
	$res = 0;
	foreach ((array) $aIni AS $i => $value)
	{
		list($ini,$fin) = explode('_',$i);
		if ($fin == $linea)
		{
			$res = $value;
		}
	}
	return $res;
}

function searcharrayfin($aFin,$linea,$aFinmin)
{
	foreach ((array) $aFin AS $i => $value)
	{
	  //      echo '<br> '.$i.' '.$value;
		list($ini,$end) = explode('_',$i);
	  //echo '<br>antes '.$ini.' '.$end.' '.$value.' search '.$linea;

		if ($end == $linea)
		{
	  //echo '<br>def res  '.$res.' value '.$value;
	  //if ($res <= $value)
			$res = $value;
			if ($res >= $aFinmin[$linea])
			{
				$res = $aFinmin[$linea];
			}
			else
			{
				$aFinmin[$linea] = $res;
			}
		}
	  // else
	  // 	if (empty($aFinmin[$end]))
	  // 	  $aFinmin[$end] = $value;
	  // 	else
	  // 	  {
	  // 	    if ($aFinmin[$end] > $value)
	  // 	      $aFinmin[$end] = $value;
	  // 	  }
	}
	return array($res,$aFinmin);
}


/**
* Show task lines with a particular parent
*
* @param	string	   	$inc				Line number (start to 0, then increased by recursive call)
* @param   string		$parent				Id of parent project to show (0 to show all)
* @param   Task[]		$lines				Array of lines
* @param   int			$level				Level (start to 0, then increased/decrease by recursive call)
* @param 	string		$var				Color
* @param 	int			$showproject		Show project columns
* @param	int			$taskrole			Array of roles of user for each tasks
* @param	int			$projectsListId		List of id of project allowed to user (string separated with comma)
* @param	int			$addordertick		Add a tick to move task
* @return	void
*/
function monprojectLinesa(&$inc, $parent, &$lines, &$level, $var, $showproject, &$taskrole, $projectsListId='', $addordertick=0, $lVista=1,$err_mark=0)
{
	global $user, $bc, $langs, $db;
	global $projectstatic, $taskstatic;
	$lastprojectid=0;
	$projectsArrayId=explode(',',$projectsListId);

	$numlines=count($lines);

	// We declare counter as global because we want to edit them into recursive call
	global $total_projectlinesa_spent,$total_projectlinesa_planned,$total_projectlinesa_spent_if_planned;
	if ($level == 0)
	{
		$total_projectlinesa_spent=0;
		$total_projectlinesa_planned=0;
		$total_projectlinesa_spent_if_planned=0;
	}
	for ($i = 0 ; $i < $numlines ; $i++)
	{
		$lView = true;
		if ($user->societe_id>0)
			if ($lines[$i]->array_options['options_c_view'] == 1)
				$lView = false;
			if ($lView)
			{
				$var = !$var;
				if ($parent == 0) $level = 0;
				  // Process line
				//echo "<hr>i:".$i."-".$lines[$i]->fk_project.'<br>';
				//echo '<hr>'.$lines[$i]->fk_parent.'| == |'.$parent;
				if ($lines[$i]->fk_parent == $parent)
				{
					// Show task line.
					$showline=1;
					$showlineingray=0;
					  // If there is filters to use
					if (is_array($taskrole))
					{
						// If task not legitimate to show, search if a legitimate task exists later in tree
						if (! isset($taskrole[$lines[$i]->id]) && $lines[$i]->id != $lines[$i]->fk_parent)
						{
							// So search if task has a subtask legitimate to show
							$foundtaskforuserdeeper=0;
							searchTaskInChild($foundtaskforuserdeeper,$lines[$i]->id,$lines,$taskrole);
							//print '$foundtaskforuserpeeper='.$foundtaskforuserdeeper.'<br>';
							if ($foundtaskforuserdeeper > 0)
							{
								$showlineingray=1;		// We will show line but in gray
							}
							else
							{
								$showline=0;			// No reason to show line
							}
						}
					}
					else
					{
						// Caller did not ask to filter on tasks of a specific user (this probably means he want also tasks of all users, into public project
						// or into all other projects if user has permission to).
						if (empty($user->rights->projet->all->lire))
						{
							// User is not allowed on this project and project is not public, so we hide line
							if (! in_array($lines[$i]->fk_project, $projectsArrayId))
							{
								  // Note that having a user assigned to a task into a project user has no permission on, should not be possible
								// because assignement on task can be done only on contact of project.
								// If assignement was done and after, was removed from contact of project, then we can hide the line.
								$showline=0;
							}
						}
					}
					//echo '<hr>st '.$lines[$i]->taskstatut.' '.$lines[$i]->id;
					if ($lines[$i]->array_options['options_c_grupo'] != 1)
					{
						if ($_SESSION['selstatut'] == 2)
						{
							if ($lines[$i]->taskstatut == 2)
								$showline = true;
							else
								$showline = false;
						}
						if ($_SESSION['selstatut'] == 1)
						{
							if (empty($lines[$i]->taskstatut) || $lines[$i]->taskstatut == 0 || $lines[$i]->taskstatut == 1)
								$showline = true;
							else
								$showline = false;
						}
					}
					if ($showline)
					{
						$lTask = true;
						// Break on a new project
						if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
						{
							//$var = !$var;
							$lastprojectid=$lines[$i]->fk_project;
						}
						if ($lines[$i]->array_options['options_c_grupo'] == 1)
						{
							$lTask = false;
							print '<tr  '.'class="backgroup"'.' id="row-'.$lines[$i]->id.'">'."\n";
						}
						else
						{
							//validamos las fechas de inicio y fin
							print '<tr  '.$bc[$var].' id="row-'.$lines[$i]->id.'">'."\n";
						}

						if ($showproject)
						{
							// Project ref
							print "<td>";
							if ($showlineingray) print '<i>';
							$projectstatic->id=$lines[$i]->fk_project;
							$projectstatic->ref=$lines[$i]->projectref;
							$projectstatic->public=$lines[$i]->public;
							if ($lines[$i]->public || in_array($lines[$i]->fk_project,$projectsArrayId))
								print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'" >'.$lines[$i]->ref.'</a>';
								//$projectstatic->getNomUrl(1);
							else
								print 'dddd';
								//$projectstatic->getNomUrl(1,'nolink');
							if ($showlineingray) print '</i>';
							print "</td>";

							// Project status
							print '<td>';
							$projectstatic->statut=$lines[$i]->projectstatus;
							print $projectstatic->getLibStatut(2);
							print "</td>";
						}

						// // Ref of task
						print '<td>';
						if ($showlineingray)
						{
							print '<i>'.img_object('','projecttask').' '.$lines[$i]->ref.'</i>';
						}
						else
						{
							$taskstatic->id=$lines[$i]->id;
							$taskstatic->ref=$lines[$i]->ref;
							$taskstatic->label=($taskrole[$lines[$i]->id]?$langs->trans("YourRole").': '.$taskrole[$lines[$i]->id]:'');
							print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'&withproject=1" title="'.$langs->trans('ShowTask').'">'.img_object($langs->trans('ShowTask'),'projecttask').' '. $lines[$i]->ref.'</a>';
							  //print $taskstatic->getNomUrl(1,'withproject');
						}
						print '</td>';

						// Title of task
						print "<td>";
						if ($showlineingray) print '<i>';
						else print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'&withproject=1">';
						for ($k = 0 ; $k < $level ; $k++)
						{
							print "&nbsp; &nbsp; &nbsp;";
						}
						print $lines[$i]->label;
						if ($showlineingray) print '</i>';
						else print '</a>';
						print "</td>\n";

						// Date start
						if (empty($lines[$i]->date_start) || is_null($lines[$i]->date_start))
						{
							$err_mark++;
							print '<td  class="errormark">';
							print '<a href="#" title="'.$langs->trans('Informationismissing').'">'.$langs->trans('Fouldate').'</a>';
						}
						else
						{
							print '<td align="center">';
							print dol_print_date($lines[$i]->date_start,'dayhour');
						}
						print '</td>';

						// Date end
						if (empty($lines[$i]->date_end) || is_null($lines[$i]->date_end))
						{
							$err_mark++;
							print '<td class="errmark">';
							print '<a href="#" style="text-decoration:none " title="'.$langs->trans('Informationismissing').'">'.$langs->trans('Fouldate').'</a>';
						}
						else
						{
							print '<td align="center">';
							print dol_print_date($lines[$i]->date_end,'dayhour');
						}
						print '</td>';

						$plannedworkloadoutputformat='allhourmin';
						$timespentoutputformat='allhourmin';
						if (! empty($conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT)) $plannedworkloadoutputformat=$conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT;
						if (! empty($conf->global->PROJECT_TIMES_SPENT_FORMAT)) $timespentoutputformat=$conf->global->PROJECT_TIME_SPENT_FORMAT;
						//RQC CAMBIADO

						  //unit
						print '<td align="center">';
						if (!$lines[$i]->array_options['options_c_grupo'])
							print $lines[$i]->array_options['options_unit'];
						print '&nbsp;';
						print '</td>';
						if ($user->rights->monprojet->task->leerm)
						{
							//muestra el precio unitario
							print '<td align="right">';
							if (!$lines[$i]->array_options['options_c_grupo'])
								print price($lines[$i]->array_options['options_unit_amount']);
							else
								print '&nbsp;';
							print '</td>';
						}
						  // Planned Workload (in working hours)
						print '<td align="right">';
						//buscamos el total programado
						//recuperamos la tarea
						// $objtask1 = new Task($db);
						// $extrafield_task = new ExtraFields($db);
						// $extralabel_task=$extrafield_task->fetch_name_optionals_label($objtask1->table_element);
						// $objtask1->fetch($lines[$i]->id);
						// $res=$objtask1->fetch_optionals($objtask1->id,$extralabel_task);
						//buscamos el item
						//$items->fetch('',$objtask1->ref);

						/*armamos variables para el calculo*/
						$datehoy = dol_now();
						if ($lTask)
							list($lCalc,$PV,$EV,$AC,$CPI,$SPI,$CSI) = form_vg($lines[$i],$extralabel_task,$datehoy);





						  // $fullhour=convertSecondToTime($lines[$i]->planned_workload,$plannedworkloadoutputformat);
						// $workingdelay=convertSecondToTime($lines[$i]->planned_workload,'all',86400,7);	// TODO Replace 86400 and 7 to take account working hours per day and working day per weeks


						if ($lines[$i]->planned_workload != '')
						{
						  //print $fullhour;
							if (!$lines[$i]->array_options['options_c_grupo'])
								print $lines[$i]->array_options['options_unit_program'];
							else
								print '&nbsp;';
		  //print $objtask1->array_options['options_unit_program'];

		  // TODO Add delay taking account of working hours per day and working day per week
		  //if ($workingdelay != $fullhour) print '<br>('.$workingdelay.')';
						}
		  //else print '--:--';
						print '</td>';

		  // Progress declared
		  // EJECUCION REPORTADA
						print '<td align="right">';
						if (!$lines[$i]->array_options['options_c_grupo'])
							print $lines[$i]->array_options['options_unit_declared'];
						else '&nbsp;';
						print '</td>';

						/*validamos la variable $lVista*/
						if ($lVista == 1)
						{
		  //amount planed
							if ($user->rights->monprojet->task->leerm)
							{

								print '<td align="right">';
		  // if ($items->ref == $objtask1->ref)
		  //   {
								if (!$lines[$i]->array_options['options_c_grupo'])
								{
									$amountprog = $lines[$i]->array_options['options_unit_amount'] * $lines[$i]->array_options['options_unit_program'];
									print price(price2num($amountprog,'MT'));
								}
								else
									print '&nbsp;';
			  //}
								print '</td>';

		  //amount declared
		  //buscamos el item
								print '<td align="right">';
		  // if ($items->ref == $objtask1->ref)
		  //   {
								if (!$lines[$i]->array_options['options_c_grupo'])
								{
									$amountdec = $lines[$i]->array_options['options_unit_amount'] * $lines[$i]->array_options['options_unit_declared'];
									print price(price2num($amountdec,'MT'));
								}
								else
									print '&nbsp;';
		  //}
								print '</td>';
							}

		  //CPI
							//print '<td align="right">';
							//print ($lTask?$CPI:'');
							//print '</td>';
		  //SPI
							//print '<td align="right">';
							//print ($lTask?price2num($SPI,'MT'):'');
							//print '</td>';
		  //CSI
							//print '<td align="right">';
							//print ($lTask?price2num($CSI,'MT'):'');
							//print '</td>';
						}
						if ($lVista == 2)
						{
		  //quant programmed
							print '<td align="right">';
							print $lines[$i]->quantr;
							print '</td>';
		  //quant declared
							print '<td align="right">';
							print $lines[$i]->declaredr;
							print '</td>';


						}
						/*fin validacion la variable $lVista*/

		  //analisis de CSI
						if ($CSI > 0.9) $statut = 1;
						elseif($CSI >=0.8 AND $CSI <= 0.9) $statut = 0;
						else
							$statut = -1;
						$text = ($statut>0?$langs->trans('ProjetOK'):(empty($statut)?$langs->trans('Posiblearreglo'):$langs->trans('Lomasprobableesquenosearregle')));
		  //CSI
						print '<td align="right">';
						if ($lCalc && $lTask)
							print '<a href="#" class="classfortooltip" title="'.$text.'">'.img_picto('',DOL_URL_ROOT.'/monprojet/img/state'.$statut,'',true).'</a>';
						else
							print '&nbsp;';
						print '</td>';
		  // Time spent
		  // print '<td align="right">';
		  // if ($showlineingray) print '<i>';
		  // else print '<a href="'.DOL_URL_ROOT.'/projet/tasks/time.php?id='.$lines[$i]->id.($showproject?'':'&withproject=1').'">';
		  // if ($lines[$i]->duration) print convertSecondToTime($lines[$i]->duration,$timespentoutputformat);
		  // else print '--:--';
		  // if ($showlineingray) print '</i>';
		  // else print '</a>';
		  // print '</td>';

		  // // Progress calculated (Note: ->duration is time spent)
		  // print '<td align="right">';
		  // if ($lines[$i]->planned_workload || $lines[$i]->duration)
		  // 	{

		  // 	  if ($lines[$i]->planned_workload) print round(100 * $lines[$i]->duration / $lines[$i]->planned_workload,2).' %';
		  // 	  else print $langs->trans('WorkloadNotDefined');
		  // 	}
		  // print '</td>';

		  // Tick to drag and drop
						if ($addordertick)
						{
							print '<td align="center" class="tdlineupdown hideonsmartphone">&nbsp;</td>';
						}

						print "</tr>\n";

						if (! $showlineingray) $inc++;

						$level++;
						if ($lines[$i]->id) monprojectLinesa($inc, $lines[$i]->id, $lines, $level, $var, $showproject, $taskrole, $projectsListId, $addordertick,$lVista,$err_mark);
						$level--;
						$total_projectlinesa_spent += $lines[$i]->duration;
						$total_projectlinesa_planned += $lines[$i]->planned_workload;
						if ($lines[$i]->planned_workload) $total_projectlinesa_spent_if_planned += $lines[$i]->duration;
					}
				}
				else
				{
					//$level--;
				}
			}
		}

		if (($total_projectlinesa_planned > 0 || $total_projectlinesa_spent > 0) && $level==0)
		{
			print '<tr class="liste_total nodrag nodrop">';
			print '<td class="liste_total">'.$langs->trans("Total").'</td>';
			if ($showproject) print '<td></td><td></td>';
			print '<td></td>';
			print '<td></td>';
			print '<td></td>';
			print '<td align="right" class="nowrap liste_total">';
			print convertSecondToTime($total_projectlinesa_planned, 'allhourmin');
			print '</td>';
			print '<td></td>';
			print '<td align="right" class="nowrap liste_total">';
			print convertSecondToTime($total_projectlinesa_spent, 'allhourmin');
			print '</td>';
			print '<td align="right" class="nowrap liste_total">';
			if ($total_projectlinesa_planned) print round(100 * $total_projectlinesa_spent / $total_projectlinesa_planned,2).' %';
			print '</td>';
			if ($addordertick) print '<td class="hideonsmartphone"></td>';
			print '</tr>';
		}

		return $inc;
	}

//calculo para el valor ganado
/**
 * Show task lines with a particular parent
 *
 * @param	string	   	$inc				Line number (start to 0, then increased by recursive call)
 * @param   string		$parent				Id of parent project to show (0 to show all)
 * @param   Task[]		$lines				Array of lines
 * @param   int			$level				Level (start to 0, then increased/decrease by recursive call)
 * @param 	string		$var				Color
 * @param 	int			$showproject		Show project columns
 * @param	int			$taskrole			Array of roles of user for each tasks
 * @param	int			$projectsListId		List of id of project allowed to user (string separated with comma)
 * @param	int			$addordertick		Add a tick to move task
 * @return	void
 */
function monprojectLinesares(&$inc, $parent, &$lines, &$level, $var, $showproject, &$taskrole, $projectsListId='', $addordertick=0,array $aTotalVal=array())
{
	global $user, $bc, $langs, $db;
	global $projectstatic, $taskstatic;
	global $items;

	$lastprojectid=0;

	$projectsArrayId=explode(',',$projectsListId);

	$numlines=count($lines);
	global $total_projectlinesa_spent,$total_projectlinesa_planned,$total_projectlinesa_spent_if_planned;
	if ($level == 0)
	{
		$total_projectlinesa_spent=0;
		$total_projectlinesa_planned=0;
		$total_projectlinesa_spent_if_planned=0;
	}

	for ($i = 0 ; $i < $numlines ; $i++)
	{
		$lView = true;
		if ($user->societe_id>0)
		{
			if ($lines[$i]->array_options['options_c_view'] == 1)
				$lView = false;
		}
		if ($lView)
		{

			if ($parent == 0) $level = 0;
			$lTask = true;
			if ($lines[$i]->fk_parent == $parent)
			{
				$showline=1;
				$showlineingray=0;

				if (is_array($taskrole))
				{
					if (! isset($taskrole[$lines[$i]->id]) && $lines[$i]->id != $lines[$i]->fk_parent)
					{
						$foundtaskforuserdeeper=0;
						searchTaskInChild($foundtaskforuserdeeper,$lines[$i]->id,$lines,$taskrole);
						if ($foundtaskforuserdeeper > 0)
						{
							$showlineingray=1;
						}
						else
						{
							$showline=0;
						}
					}
				}
				else
				{
					if (empty($user->rights->projet->all->lire))
					{
						if (! in_array($lines[$i]->fk_project, $projectsArrayId))
						{
							$showline=0;
						}
					}
				}
				if ($showline)
				{
					if ($lines[$i]->array_options['options_c_grupo'] == 1)
						$lTask = false;
					if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
					{
						$var = !$var;
						$lastprojectid=$lines[$i]->fk_project;
					}

					if ($showproject)
					{
						if ($showlineingray) print '<i>';
						$projectstatic->id=$lines[$i]->fk_project;
						$projectstatic->ref=$lines[$i]->projectref;
						$projectstatic->public=$lines[$i]->public;
						$projectstatic->statut=$lines[$i]->projectstatus;
					}
					if ($showlineingray)
					{
					}
					else
					{
					}
					$plannedworkloadoutputformat='allhourmin';
					$timespentoutputformat='allhourmin';
					if (! empty($conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT))
						$plannedworkloadoutputformat=$conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT;
					if (! empty($conf->global->PROJECT_TIMES_SPENT_FORMAT))
						$timespentoutputformat=$conf->global->PROJECT_TIME_SPENT_FORMAT;
					$objtask1 = new Task($db);
					$extrafield_task = new ExtraFields($db);
					$extralabel_task=$extrafield_task->fetch_name_optionals_label($objtask1->table_element);
					$lVg = false;
					if ($lines[$i]->id >0)
					{
						$objtask1->fetch($lines[$i]->id);
						if ($objtask1->id == $lines[$i]->id)
						{
							$lVg = true;
							$res=$objtask1->fetch_optionals($objtask1->id,$extralabel_task);
						}
					}
					//$items->fetch('',$objtask1->ref);
					$datehoy = dol_now();
					if ($lTask && $lVg)
					{
						list($lCalc,$PV,$EV,$AC,$CPI,$SPI,$CSI) = form_vg($objtask1,$extralabel_task,$datehoy);
						if ($lCalc==true) $aTotalVal['lCalc'] = $lCalc;
						$aTotalVal['PV']  += price2num($PV,'MU');
						$aTotalVal['EV']  += price2num($EV,'MU');
						$aTotalVal['AC']  += price2num($AC,'MU');
						$aTotalVal['CPI'] += price2num($CPI,'MU');
						$aTotalVal['SPI'] += price2num($SPI,'MU');
						$aTotalVal['CSI'] += price2num($CSI,'MU');
					}
					if ($lines[$i]->planned_workload != '')
					{
					}

					if ($items->ref == $objtask1->ref)
					{
						$amountprog = $items->amount * $objtask1->array_options['options_unit_program'];
					}

					if ($items->ref == $objtask1->ref)
					{
						$amountdec = $items->amount * $objtask1->array_options['options_unit_declared'];
					}

					if ($CSI > 0.9) $statut = 1;
					elseif($CSI >=0.8 AND $CSI <= 0.9) $statut = 0;
					else
						$statut = -1;
					$text = ($statut>0?$langs->trans('ProjetOK'):(empty($statut)?$langs->trans('Posiblearreglo'):$langs->trans('Lomasprobableesquenosearregle')));
					if ($addordertick)
					{
					}
					if (! $showlineingray) $inc++;

					$level++;
					if ($lines[$i]->id)
					{
						$aTotalVal = monprojectLinesares($inc, $lines[$i]->id, $lines, $level, $var, $showproject, $taskrole, $projectsListId, $addordertick,$aTotalVal);
					}
					$level--;
					$total_projectlinesa_spent += $lines[$i]->duration;
					$total_projectlinesa_planned += $lines[$i]->planned_workload;
					if ($lines[$i]->planned_workload) $total_projectlinesa_spent_if_planned += $lines[$i]->duration;
				}
			}
			else
			{
					//$level--;
					//echo '<hr>revisalevel '.$level;
			}
		}
	}
	return $aTotalVal;
}

//fin calculo valor ganado


/**
 *  Complete or removed entries into a head array (used to build tabs) with value added by external modules.
 *  Such values are declared into $conf->modules_parts['tab'].
 *
 *  @param	Conf			$conf           Object conf
 *  @param  Translate		$langs          Object langs
 *  @param  object|null		$object         Object object
 *  @param  array			$head          	Object head
 *  @param  int				$h				New position to fill
 *  @param  string			$type           Value for object where objectvalue can be
 *                              			'thirdparty'       to add a tab in third party view
 *		                        	      	'intervention'     to add a tab in intervention view
 *     		                    	     	'supplier_order'   to add a tab in supplier order view
 *          		            	        'supplier_invoice' to add a tab in supplier invoice view
 *                  		    	        'invoice'          to add a tab in customer invoice view
 *                          			    'order'            to add a tab in customer order view
 *                      			        'product'          to add a tab in product view
 *                              			'propal'           to add a tab in propal view
 *                              			'user'             to add a tab in user view
 *                              			'group'            to add a tab in group view
 * 		        	               	     	'member'           to add a tab in fundation member view
 *      		                        	'categories_x'	   to add a tab in category view ('x': type of category (0=product, 1=supplier, 2=customer, 3=member)
 *      									'ecm'			   to add a tab for another ecm view
 *                                          'stock'            to add a tab for warehouse view
 *  @param  string		$mode  	        	'add' to complete head, 'remove' to remove entries
 *	@return	void
 */
function moncomplete_head_from_modules($conf,$langs,$object,&$head,&$h,$type,$mode='add')
{
	if (isset($conf->modules_parts['tabs'][$type]) && is_array($conf->modules_parts['tabs'][$type]))
	{
		foreach ($conf->modules_parts['tabs'][$type] as $value)
		{
			$values=explode(':',$value);

			if ($mode == 'add' && ! preg_match('/^\-/',$values[1]))
			{
				if (count($values) == 6)       // new declaration with permissions:  $value='objecttype:+tabname1:Title1:langfile@mymodule:$user->rights->mymodule->read:/mymodule/mynewtab1.php?id=__ID__'
				{
					if ($values[0] != $type) continue;

					if (verifCond($values[4]))
					{
						if ($values[3]) $langs->load($values[3]);
						if (preg_match('/SUBSTITUTION_([^_]+)/i',$values[2],$reg))
						{
							$substitutionarray=array();
							complete_substitutions_array($substitutionarray,$langs,$object);
							$label=make_substitutions($reg[1], $substitutionarray);
						}
						else $label=$langs->trans($values[2]);

						$head[$h][0] = dol_buildpath(preg_replace('/__ID__/i', ((is_object($object) && ! empty($object->id))?$object->id:''), $values[5]), 1);
						$head[$h][1] = $label;
						$head[$h][2] = str_replace('+','',$values[1]);
						$h++;
					}
				}
				else if (count($values) == 5)       // deprecated
				{
					if ($values[0] != $type) continue;
					if ($values[3]) $langs->load($values[3]);
					if (preg_match('/SUBSTITUTION_([^_]+)/i',$values[2],$reg))
					{
						$substitutionarray=array();
						complete_substitutions_array($substitutionarray,$langs,$object);
						$label=make_substitutions($reg[1], $substitutionarray);
					}
					else $label=$langs->trans($values[2]);

					$head[$h][0] = dol_buildpath(preg_replace('/__ID__/i', ((is_object($object) && ! empty($object->id))?$object->id:''), $values[4]), 1);
					$head[$h][1] = $label;
					$head[$h][2] = str_replace('+','',$values[1]);
					$h++;
				}
			}
			else if ($mode == 'remove' && preg_match('/^\-/',$values[1]))
			{
				if ($values[0] != $type) continue;
				$tabname=str_replace('-','',$values[1]);
				foreach($head as $key => $val)
				{
					$condition = (! empty($values[3]) ? verifCond($values[3]) : 1);
					if ($head[$key][2]==$tabname && $condition)
					{
						unset($head[$key]);
						break;
					}
				}
			}
		}
	}
}

function form_vg($objtask1,$extralabel_task,$date)
{
	global $db;
	global $objecttime;
	/*tiempo del proyecto*/
	//echo '<hr>'.dol_print_date($objtask1->date_start,'day').' '.dol_print_date($objtask1->date_end,'day');
	$ndiasprog = num_between_day($objtask1->date_start,$objtask1->date_end,1);
	if ($objtask1->date_start==$objtask1->date_end)
		$ndiasprog = 1;
	//echo '<br>diasprog |'.$ndiasprog.'|';
	//verificamos la fecha de conclusion reportada si las unidades declaradas
  //es igual o mayor al planificado
	//echo ' actual '.dol_print_date($date,'dayhour');
	if ($objtask1->array_options['options_unit_declared'] >=
		$objtask1->array_options['options_unit_program'])
	{
		$restime = $objecttime->last_advance($objtask1->id,0,-1);
		if ($restime==1)
			$date = $objecttime->task_date;
	}
	//echo ' actualmod  '.dol_print_date($date,'dayhour');

	if ($date > $objtask1->date_start)
		$lCalc = true;
	else
		$lCalc = false;
	$aDate = dol_getdate($objtask1->date_start);
	$ndiaseje  = num_between_day($objtask1->date_start,$date,1);
	if ($objtask1->date_start==$date)
		$ndiaseje = 1;
	//echo 'diaseje |'.$ndiaseje.'|';
	$npercentprog = 0;
	if ($ndiasprog>0)
		$npercentprog = $ndiaseje * 100 / $ndiasprog;
  //tarea programada a la fecha
	$PV = 0;
	if ($ndiasprog > 0)
	{
		//echo ' calc '.$objtask1->array_options['options_unit_program'].' * '.$objtask1->array_options['options_unit_amount'].' * '.$ndiaseje.' / '.$ndiasprog;
		$PV = $objtask1->array_options['options_unit_program'] * $objtask1->array_options['options_unit_amount'] * $ndiaseje / $ndiasprog;
	   //BCWS
	}
  //tarea ejecutada a la fecha
	//echo ' pv |'.$PV.'|';
	$npercenteje = 0;
	if ($objtask1->array_options['options_unit_program']>0)
		$npercenteje  = $objtask1->array_options['options_unit_declared']/
	($objtask1->array_options['options_unit_program']+0)*100;
  //obtener el valor de EV
	$EV = 0;
	if ($npercentprog<>0)
		$EV = $PV * $npercenteje / $npercentprog;
	 //BCWP
	//echo ' ev |'.$EV.'|';
  //costo real
	$AC = $objtask1->array_options['options_unit_declared'] * $objtask1->array_options['options_unit_amount'];
   //ACWP
	//echo ' AC |'.$AC.'|';
  //variaciones
	$CV = $EV - $AC;
	$SV = $EV - $PV;
	//echo ' cv |'.$CV.'| SV |'.$SV.'|';
  //indice de desempeno
	$CPI = 0;
	if ($AC<>0)
		$CPI = $EV/$AC;
	 //BCWP/ACWP
	$SPI = 0;
	if ($PV<>0)
		$SPI = $EV/$PV;
	 //BCWP/BCWS
  //calcular indicador relacion costo y cronograma
	$CSI = $CPI * $SPI;
	//echo '<br>cpi|'.$CPI.'|spi|'.$SPI.'|csi|'.$CSI.'|';
	return array($lCalc,$PV,$EV,$AC,$CPI,$SPI,$CSI);
}

function form_vgadd($objtask1,$extralabel_task,$date)
{
	global $db;
	global $objecttime;
	/*tiempo del proyecto*/
	//echo '<hr>'.dol_print_date($objtask1->date_start,'day').' '.dol_print_date($objtask1->date_end,'day');
	$ndiasprog = num_between_day($objtask1->date_start,$objtask1->date_end,1);
	if ($objtask1->date_start==$objtask1->date_end)
		$ndiasprog = 1;
	//echo '<br>diasprog |'.$ndiasprog.'|';
	//verificamos la fecha de conclusion reportada si las unidades declaradas
  //es igual o mayor al planificado
	//echo ' actual '.dol_print_date($date,'dayhour');
	if ($objtask1->unit_declared >=
		$objtask1->unit_program)
	{
		$restime = $objecttime->last_advance($objtask1->id,0,-1);
		if ($restime==1)
			$date = $objecttime->task_date;
	}
	//echo ' actualmod  '.dol_print_date($date,'dayhour');

	if ($date > $objtask1->date_start)
		$lCalc = true;
	else
		$lCalc = false;
	$aDate = dol_getdate($objtask1->date_start);
	$ndiaseje  = num_between_day($objtask1->date_start,$date,1);
	if ($objtask1->date_start==$date)
		$ndiaseje = 1;
	//echo 'diaseje |'.$ndiaseje.'|';
	$npercentprog = 0;
	if ($ndiasprog>0)
		$npercentprog = $ndiaseje * 100 / $ndiasprog;
  //tarea programada a la fecha
	$PV = 0;
	if ($ndiasprog > 0)
	{
		//echo ' calc '.$objtask1->array_options['options_unit_program'].' * '.$objtask1->array_options['options_unit_amount'].' * '.$ndiaseje.' / '.$ndiasprog;
		$PV = $objtask1->unit_program * $objtask1->unit_amount * $ndiaseje / $ndiasprog;
	   //BCWS
	}
  //tarea ejecutada a la fecha
	//echo ' pv |'.$PV.'|';
	$npercenteje = 0;
	if ($objtask1->unit_program>0)
		$npercenteje  = $objtask1->unit_declared/
	($objtask1->unit_program+0)*100;
  //obtener el valor de EV
	$EV = 0;
	if ($npercentprog<>0)
		$EV = $PV * $npercenteje / $npercentprog;
	 //BCWP
	//echo ' ev |'.$EV.'|';
  //costo real
	$AC = $objtask1->unit_declared * $objtask1->unit_amount;
   //ACWP
	//echo ' AC |'.$AC.'|';
  //variaciones
	$CV = $EV - $AC;
	$SV = $EV - $PV;
	//echo ' cv |'.$CV.'| SV |'.$SV.'|';
  //indice de desempeno
	$CPI = 0;
	if ($AC<>0)
		$CPI = $EV/$AC;
	 //BCWP/ACWP
	$SPI = 0;
	if ($PV<>0)
		$SPI = $EV/$PV;
	 //BCWP/BCWS
  //calcular indicador relacion costo y cronograma
	$CSI = $CPI * $SPI;
	//echo '<br>cpi|'.$CPI.'|spi|'.$SPI.'|csi|'.$CSI.'|';
	return array($lCalc,$PV,$EV,$AC,$CPI,$SPI,$CSI);
}

function convertdate($aDatef,$selvalue,$date)
{
	$sel = $aDatef[$selvalue];
	switch ($sel)
	{
		case 0:
		list($day,$mes,$anio) = explode('/',$date);
		break;
		case 0:
		list($day,$mes,$anio) = explode('-',$date);
		break;
		case 0:
		list($mes,$day,$anio) = explode('/',$date);
		break;
		case 0:
		list($mes,$day,$anio) = explode('-',$date);
		break;
		case 0:
		list($anio,$mes,$day) = explode('/',$date);
		break;
		case 0:
		list($anio,$mes,$day) = explode('-',$date);
		break;
	}
	$newdate = dol_mktime(12, 0, 0, $mes, $day, $anio);
	return $newdate;
}


/**
 * Show task lines with a particular parent
 *
 * @param	string	   	$inc				Line number (start to 0, then increased by recursive call)
 * @param   string		$parent				Id of parent project to show (0 to show all)
 * @param   Task[]		$lines				Array of lines
 * @param   int			$level				Level (start to 0, then increased/decrease by recursive call)
 * @param 	string		$var				Color
 * @param 	int			$showproject		Show project columns
 * @param	int			$taskrole			Array of roles of user for each tasks
 * @param	int			$projectsListId		List of id of project allowed to user (string separated with comma)
 * @param	int			$addordertick		Add a tick to move task
 * @return	void
 */

function monprojectLineejec(&$inc, $parent, &$lines, &$level, $var, $showproject, &$taskrole, $projectsListId='', $addordertick=0,$aLoop='')
{
	global $user, $bc, $langs, $db;
	global $projectstatic, $taskstatic, $taskadd;
	global $items, $subaction;

	require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskaddext.class.php';

	$lastprojectid=0;

	$projectsArrayId=explode(',',$projectsListId);

	$numlines=count($lines);

	// We declare counter as global because we want to edit them into recursive call
	global $total_projectlinesa_spent,$total_projectlinesa_planned,$total_projectlinesa_spent_if_planned;
	if ($level == 0)
	{
		$total_projectlinesa_spent=0;
		$total_projectlinesa_planned=0;
		$total_projectlinesa_spent_if_planned=0;
	}

	for ($i = 0 ; $i < $numlines ; $i++)
	{
		$lView = true;
		if ($user->societe_id>0)
			if ($lines[$i]->array_options['options_c_view'] == 1)
				$lView = false;
			if ($lView)
			{
				if ($parent == 0) $level = 0;

			// Process line
			// print "i:".$i."-".$lines[$i]->fk_project.'<br>';
				if ($lines[$i]->fk_parent == $parent)
				{
					// Show task line.
					$showline=1;
					$showlineingray=0;
					// If there is filters to use
					if (is_array($taskrole))
					{
						// If task not legitimate to show, search if a legitimate task exists later in tree
						if (! isset($taskrole[$lines[$i]->id]) && $lines[$i]->id != $lines[$i]->fk_parent)
						{
							// So search if task has a subtask legitimate to show
							$foundtaskforuserdeeper=0;
							searchTaskInChild($foundtaskforuserdeeper,$lines[$i]->id,$lines,$taskrole);
							//print '$foundtaskforuserpeeper='.$foundtaskforuserdeeper.'<br>';
							if ($foundtaskforuserdeeper > 0)
							{
								$showlineingray=1;		// We will show line but in gray
							}
							else
							{
								$showline=0;			// No reason to show line
							}
						}
					}
					else
					{
						// Caller did not ask to filter on tasks of a specific user (this probably means he want also tasks of all users, into public project
						// or into all other projects if user has permission to).
						if (empty($user->rights->projet->all->lire))
						{
							// User is not allowed on this project and project is not public, so we hide line
							if (! in_array($lines[$i]->fk_project, $projectsArrayId))
							{
								// Note that having a user assigned to a task into a project user has no permission on, should not be possible
								// because assignement on task can be done only on contact of project.
								// If assignement was done and after, was removed from contact of project, then we can hide the line.
								$showline=0;
							}
						}
					}
					if ($showline)
					{
						// Break on a new project
						if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
						{
							$var = !$var;
							$lastprojectid=$lines[$i]->fk_project;
						}
						if ($lines[$i]->array_options['options_c_grupo'] == 1)
						{
							$lTask = false;
							print '<tr  '.'class="backgroup"'.' id="row-'.$lines[$i]->id.'">'."\n";
						}
						else
							print '<tr  '.$bc[$var].' id="row-'.$lines[$i]->id.'">'."\n";

						if ($showproject)
						{
							// Project ref
							print "<td>";
							if ($showlineingray) print '<i>';
							$projectstatic->id=$lines[$i]->fk_project;
							$projectstatic->ref=$lines[$i]->projectref;
							$projectstatic->public=$lines[$i]->public;
							if ($lines[$i]->public || in_array($lines[$i]->fk_project,$projectsArrayId))
								print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'" >'.$lines[$i]->ref.'</a>';
							else
								print 'dddd';
							if ($showlineingray) print '</i>';
							print "</td>";

							// Project status
							print '<td>';
							$projectstatic->statut=$lines[$i]->projectstatus;
							print $projectstatic->getLibStatut(2);
							print "</td>";
						}


						// Title of task
						print "<td>";
						if ($showlineingray) print '<i>';
						else print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'&withproject=1">';
						for ($k = 0 ; $k < $level ; $k++)
						{
							print "&nbsp; &nbsp; &nbsp;";
						}
						print $lines[$i]->label;
						if ($showlineingray) print '</i>';
						else print '</a>';
						print "</td>\n";

						// Date start
						print '<td align="center">';
						print dol_print_date($lines[$i]->date_start,'dayhour');
						print '</td>';

						// Date end
						print '<td align="center">';
						print dol_print_date($lines[$i]->date_end,'dayhour');
						print '</td>';

						//unit
						print '<td align="center">';
						if (!$lines[$i]->array_options['options_c_grupo'])
							print $lines[$i]->array_options['options_unit'];
						print '&nbsp;';
						print '</td>';

						if ($user->rights->monprojet->task->leerm)
						{
							print '<td align="right">';
							if (!$lines[$i]->array_options['options_c_grupo'])
								print price($lines[$i]->array_options['options_unit_amount']);
							else
								print '&nbsp;';
							print '</td>';
						}

						//recuperamos los tiempos reportados
						$taskadd->getTimeSpent($lines[$i]->id);
						//armamos de acuerdo al tipo de recporte fecha
						$aTaskd = array();
						$aTaskw = array();
						$aTaskm = array();
						$aTasky = array();
						foreach ((array) $taskadd->lines AS $k => $linet)
						{
							//echo '<hr>line '.$linet->id.' == '.$lines[$i]->id;
							if ($linet->id == $lines[$i]->id)
							{
								$aZ = dol_getdate($linet->timespent_date);
								$x = dol_stringtotime($aZ['year'].''.(strlen($aZ['mon'])==1?'0'.$aZ['mon']:$aZ['mon']).''.(strlen($aZ['mday'])==1?'0'.$aZ['mday']:$aZ['mday']). '120000');
								//obtenemos la semana
								$aDatereg = dol_getdate($x);
								list($semana,$primerDia,$ultimoDia) = weekinifin($aDatereg['year'],$aDatereg['mon'],$aDatereg['mday']);
								$x1 = dol_stringtotime($primerDia);
								$x2 = dol_stringtotime($ultimoDia);

								//para diarios
								$aTaskd[$aDatereg['year']][$x]['value'] += $linet->unit_declared;
								$aTaskd[$aDatereg['year']][$x][0] = $x;
								//para semanal
								$aTaskw[$aDatereg['year']][$semana]['value'] += $linet->unit_declared;
								$aTaskw[$aDatereg['year']][$semana][0] = $x1;
								$aTaskw[$aDatereg['year']][$semana][1] = $x2;
								//para mensual
								$aTaskm[$aZ['year']][$aZ['mon']]['value'] += $linet->unit_declared;
								//para anual
								$aTasky[$aZ['year']][$aZ['year']]['value'] += $linet->unit_declared;
							}
						}

						//agregamos el tipo de reporte fecha
						foreach ($aLoop AS $year => $aDate)
						{
							foreach ($aDate AS $date => $aValue)
							{
								print '<td align="center">';
								if ($subaction == 'daily') print $aTaskd[$year][$date]['value'];
								if ($subaction == 'weekly') print $aTaskw[$year][$date]['value'];
								if ($subaction == 'monthly') print $aTaskm[$year][$date]['value'];
								if ($subaction == 'annual') print $aTasky[$year][$date]['value'];
								print '</td>';
							}
						}
						$plannedworkloadoutputformat='allhourmin';
						$timespentoutputformat='allhourmin';
						if (! empty($conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT)) $plannedworkloadoutputformat=$conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT;
						if (! empty($conf->global->PROJECT_TIMES_SPENT_FORMAT)) $timespentoutputformat=$conf->global->PROJECT_TIME_SPENT_FORMAT;
						//RQC CAMBIADO
						// Planned Workload (in working hours)
						print '<td align="right">';
						//buscamos el total programado
						//recuperamos la tarea
						$objtask1 = new Task($db);
						$objtask1add = new Projettaskaddext($db);

						$extrafield_task = new ExtraFields($db);
						$extralabel_task=$extrafield_task->fetch_name_optionals_label($objtask1->table_element);
						$objtask1->fetch($lines[$i]->id);
						$objtask1add->fetch(0,$lines[$i]->id);
						$objtask1->unit_declared = $objtask1add->unit_declared;
						$objtask1->unit_program = $objtask1add->unit_program;
						$objtask1->unit_amount = $objtask1add->unit_amount;
						$res=$objtask1->fetch_optionals($objtask1->id,$extralabel_task);
						//buscamos el item
						if ($conf->budget->enabled)
						{
							$items->fetch('',$objtask1->ref);
						}
						/*armamos variables para el calculo*/
						$datehoy = dol_now();
						list($lCalc,$PV,$EV,$AC,$CPI,$SPI,$CSI) = form_vgadd($objtask1,$extralabel_task,$datehoy);

						// $fullhour=convertSecondToTime($lines[$i]->planned_workload,$plannedworkloadoutputformat);
						// $workingdelay=convertSecondToTime($lines[$i]->planned_workload,'all',86400,7);	// TODO Replace 86400 and 7 to take account working hours per day and working day per weeks
						if ($lines[$i]->planned_workload != '')
						{
							if (!$lines[$i]->array_options['options_c_grupo'])
							//print $fullhour;
								print $objtask1add->unit_program;
								//print $objtask1->array_options['options_unit_program'];
							// TODO Add delay taking account of working hours per day and working day per week
							//if ($workingdelay != $fullhour) print '<br>('.$workingdelay.')';
						}
						//else print '--:--';
						print '</td>';

						// Progress declared
						// EJECUCION REPORTADA
						print '<td align="right">';
						if (!$lines[$i]->array_options['options_c_grupo'])
							print $objtask1add->unit_declared;
							//print $objtask1->array_options['options_unit_declared'];
						print '</td>';

						//amount planed
						print '<td align="right">';
						$amountprog = $lines[$i]->array_options['options_unit_amount'] * $objtask1add->unit_program;
						//$amountprog = $lines[$i]->array_options['options_unit_amount'] * $objtask1->array_options['options_unit_program'];
						if (!$lines[$i]->array_options['options_c_grupo'])
							print price(price2num($amountprog,'MT'));
						print '</td>';

						//amount declared
						//buscamos el item
						print '<td align="right">';
						//$amountdec = $lines[$i]->array_options['options_unit_amount'] * $objtask1->array_options['options_unit_declared'];
						$amountdec = $lines[$i]->array_options['options_unit_amount'] * $objtask1add->unit_declared;
						if (!$lines[$i]->array_options['options_c_grupo'])
							print price(price2num($amountdec,'MT'));
						print '</td>';

						//CPI
						print '<td align="right">';
						if (!$lines[$i]->array_options['options_c_grupo']) print $CPI;
						print '</td>';
						//SPI
						print '<td align="right">';
						if (!$lines[$i]->array_options['options_c_grupo']) print price2num($SPI,'MT');
						print '</td>';
						//CSI
						print '<td align="right">';
						if (!$lines[$i]->array_options['options_c_grupo']) print price2num($CSI,'MT');
						print '</td>';

						//analisis de CSI
						if ($CSI > 0.9) $statut = 1;
						elseif($CSI >=0.8 AND $CSI <= 0.9) $statut = 0;
						else $statut = -1;
						$text = ($statut>0?$langs->trans('ProjetOK'):(empty($statut)?$langs->trans('Posiblearreglo'):$langs->trans('Lomasprobableesquenosearregle')));
						//CSI
						print '<td align="right">';
						if ($lCalc)
							if (!$lines[$i]->array_options['options_c_grupo'])
								print '<a href="#" class="classfortooltip" title="'.$text.'">'.img_picto('',DOL_URL_ROOT.'/monprojet/img/state'.$statut,'',true).'</a>';
							else
								print '&nbsp;';
							print '</td>';
						// Tick to drag and drop
							if ($addordertick)
							{
								print '<td align="center" class="tdlineupdown hideonsmartphone">&nbsp;</td>';
							}

							print "</tr>\n";

							if (! $showlineingray) $inc++;

							$level++;
							if ($lines[$i]->id) monprojectLineejec($inc, $lines[$i]->id, $lines, $level, $var, $showproject, $taskrole, $projectsListId, $addordertick,$aLoop);
							$level--;
							$total_projectlinesa_spent += $lines[$i]->duration;
							$total_projectlinesa_planned += $lines[$i]->planned_workload;
							if ($lines[$i]->planned_workload) $total_projectlinesa_spent_if_planned += $lines[$i]->duration;
						}
					}
					else
					{
				//$level--;
					}
				}
			}

			if (($total_projectlinesa_planned > 0 || $total_projectlinesa_spent > 0) && $level==0)
			{
				print '<tr class="liste_total nodrag nodrop">';
				print '<td class="liste_total">'.$langs->trans("Total").'</td>';
				if ($showproject) print '<td></td><td></td>';
				print '<td></td>';
				print '<td></td>';
				print '<td></td>';
				print '<td align="right" class="nowrap liste_total">';
				print convertSecondToTime($total_projectlinesa_planned, 'allhourmin');
				print '</td>';
				print '<td></td>';
				print '<td align="right" class="nowrap liste_total">';
				print convertSecondToTime($total_projectlinesa_spent, 'allhourmin');
				print '</td>';
				print '<td align="right" class="nowrap liste_total">';
				if ($total_projectlinesa_planned) print round(100 * $total_projectlinesa_spent / $total_projectlinesa_planned,2).' %';
				print '</td>';
				if ($addordertick) print '<td class="hideonsmartphone"></td>';
				print '</tr>';
			}

			return $inc;
		}

//function lineejecarray para graficacion image
/**
 * Show task lines with a particular parent
 *
 * @param   string      $inc                Line number (start to 0, then increased by recursive call)
 * @param   string      $parent             Id of parent project to show (0 to show all)
 * @param   Task[]      $lines              Array of lines
 * @param   int         $level              Level (start to 0, then increased/decrease by recursive call)
 * @param   string      $var                Color
 * @param   int         $showproject        Show project columns
 * @param   int         $taskrole           Array of roles of user for each tasks
 * @param   int         $projectsListId     List of id of project allowed to user (string separated with comma)
 * @param   int         $addordertick       Add a tick to move task
 * @return  void
 */

function monprojectLineejecarray(&$inc, $parent, &$lines, &$level, $var, $showproject, &$taskrole, $projectsListId='', $addordertick=0,$aLoop='',$aLabel='',$aPlan='',$aTask='')
{
	global $user, $bc, $langs, $db;
	global $projectstatic, $taskstatic, $taskadd;
	global $items, $subaction;

	$lastprojectid=0;

	$projectsArrayId=explode(',',$projectsListId);

	$numlines=count($lines);
	//array para graficos
	// We declare counter as global because we want to edit them into recursive call
	global $total_projectlinesa_spent,$total_projectlinesa_planned,$total_projectlinesa_spent_if_planned;
	if ($level == 0)
	{
		$total_projectlinesa_spent=0;
		$total_projectlinesa_planned=0;
		$total_projectlinesa_spent_if_planned=0;
	}

	for ($i = 0 ; $i < $numlines ; $i++)
	{
		$lView = true;
		if ($user->societe_id>0)
			if ($lines[$i]->array_options['options_c_view'] == 1)
				$lView = false;
			if ($lView)
			{
				if ($parent == 0) $level = 0;

			// Process line
				if ($lines[$i]->fk_parent == $parent)
				{

				// Show task line.
					$showline=1;
					$showlineingray=0;
				// If there is filters to use
					if (is_array($taskrole))
					{
					// If task not legitimate to show, search if a legitimate task exists later in tree
						if (! isset($taskrole[$lines[$i]->id]) && $lines[$i]->id != $lines[$i]->fk_parent)
						{
						// So search if task has a subtask legitimate to show
							$foundtaskforuserdeeper=0;
							searchTaskInChild($foundtaskforuserdeeper,$lines[$i]->id,$lines,$taskrole);
						//print '$foundtaskforuserpeeper='.$foundtaskforuserdeeper.'<br>';
							if ($foundtaskforuserdeeper > 0)
							{
							$showlineingray=1;      // We will show line but in gray
						}
						else
						{
							$showline=0;            // No reason to show line
						}
					}
				}
				else
				{
					// Caller did not ask to filter on tasks of a specific user (this probably means he want also tasks of all users, into public project
					// or into all other projects if user has permission to).
					if (empty($user->rights->projet->all->lire))
					{
						// User is not allowed on this project and project is not public, so we hide line
						if (! in_array($lines[$i]->fk_project, $projectsArrayId))
						{
							$showline=0;
						}
					}
				}
				if ($showline)
				{
					// Break on a new project
					if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
					{
						$var = !$var;
						$lastprojectid=$lines[$i]->fk_project;
					}
					if ($lines[$i]->array_options['options_c_grupo'] == 1)
					{
						$lTask = false;
					}

					if ($showproject)
					{
						$projectstatic->id=$lines[$i]->fk_project;
						$projectstatic->ref=$lines[$i]->projectref;
						$projectstatic->public=$lines[$i]->public;
						$projectstatic->statut=$lines[$i]->projectstatus;
					}

					//recuperamos los tiempos reportados
					if ($lines[$i]->array_options['options_c_grupo'] != 1)
					{
						$taskadd->getTimeSpent($lines[$i]->id);
						//armamos de acuerdo al tipo de recporte fecha
						//armamnos la tarea planificada
						//$aZ = dol_getdate($taskadd->date_end);
						$aZ = dol_getdate($lines[$i]->date_end);

						$x = dol_stringtotime($aZ['year'].''.(strlen($aZ['mon'])==1?'0'.$aZ['mon']:$aZ['mon']).''.(strlen($aZ['mday'])==1?'0'.$aZ['mday']:$aZ['mday']). '120000');
						//obtenemos la semana
						$aDatereg = dol_getdate($x);
						list($semana,$primerDia,$ultimoDia) = weekinifin($aZ['year'],$aZ['mon'],$aZ['mday']);
						$x1 = dol_stringtotime($primerDia);
						$x2 = dol_stringtotime($ultimoDia);
						//para diarios
						$aLabel['d'][$aDatereg['year']][$x] = $x;
						$aPlan['d'][$aDatereg['year']][$x] += $lines[$i]->array_options['options_unit_amount'] * $lines[$i]->array_options['options_unit_program'];
					//$aPlan['d'][$aDatereg['year']][$x][0] = $x;
						//para semanal
						$aLabel['w'][$aDatereg['year']][$semana] = $aDatereg['year'].'_'.$semana;
						$aPlan['w'][$aDatereg['year']][$semana] += $lines[$i]->array_options['options_unit_amount'] * $lines[$i]->array_options['options_unit_program'];
					//$aPlan['w'][$aDatereg['year']][$semana][0] = $x1;
					//$aPlan['w'][$aDatereg['year']][$semana][1] = $x2;
						//para mensual
						$aLabel['m'][$aZ['year']][$aZ['mon']] = $aZ['year'].'_'.$aZ['mon'];
						$aPlan['m'][$aZ['year']][$aZ['mon']] += $lines[$i]->array_options['options_unit_amount'] * $lines[$i]->array_options['options_unit_program'];
						//para anual
						$aLabel['y'][$aZ['year']] = $aZ['year'];
						$aPlan['y'][$aZ['year']][$aZ['year']] += $lines[$i]->array_options['options_unit_amount'] * $lines[$i]->array_options['options_unit_program'];
						foreach ((array) $taskadd->lines AS $k => $linet)
						{
							//echo '<hr>line '.$linet->id.' == '.$lines[$i]->id;
							if ($linet->id == $lines[$i]->id)
							{
								//echo '<hr>';
								//armamnos las tareas ejecutadas
								$aZ = dol_getdate($linet->timespent_date);
								//print_r($aZ);
								$x = dol_stringtotime($aZ['year'].''.(strlen($aZ['mon'])==1?'0'.$aZ['mon']:$aZ['mon']).''.(strlen($aZ['mday'])==1?'0'.$aZ['mday']:$aZ['mday']). '120000');
								//obtenemos la semana
								$aDatereg = dol_getdate($x);
								list($semana,$primerDia,$ultimoDia) = weekinifin($aDatereg['year'],$aDatereg['mon'],$aDatereg['mday']);
								$x1 = dol_stringtotime($primerDia);
								$x2 = dol_stringtotime($ultimoDia);
								//para diarios
								$aTask['d'][$aDatereg['year']][$x] += $linet->unit_declared*$lines[$i]->array_options['options_unit_amount'];
								//para semanal
								$aTask['w'][$aDatereg['year']][$semana] += $linet->unit_declared*$lines[$i]->array_options['options_unit_amount'];
								//para mensual
								//echo '<br>'.$aZ['year'].' '.$aZ['mon'].' value  += '.$linet->unit_declared.' * '.$lines[$i]->array_options['options_unit_amount'];
								$aTask['m'][$aZ['year']][$aZ['mon']] += $linet->unit_declared*$lines[$i]->array_options['options_unit_amount'];
								//para anual
								$aTask['y'][$aZ['year']][$aZ['year']] += $linet->unit_declared*$lines[$i]->array_options['options_unit_amount'];
							}
						}
					}
					$aResult['aLabel'] = $aLabel;
					$aResult['aPlan']  = $aPlan;
					$aResult['aTask']  = $aTask;

					if (! $showlineingray) $inc++;
					$level++;
					if ($lines[$i]->id)
					{
						$aResult = monprojectLineejecarray($inc, $lines[$i]->id, $lines, $level, $var, $showproject, $taskrole, $projectsListId, $addordertick,$aLoop,$aLabel,$aPlan,$aTask);
						$aLabel = $aResult['aLabel'];
						$aPlan = $aResult['aPlan'];
						$aTask = $aResult['aTask'];
					}
					$level--;
					$total_projectlinesa_spent += $lines[$i]->duration;
					$total_projectlinesa_planned += $lines[$i]->planned_workload;
					if ($lines[$i]->planned_workload) $total_projectlinesa_spent_if_planned += $lines[$i]->duration;
				}
			}
			else
			{
				//$level--;
			}
		}
	}
	$aResult['aLabel'] = $aLabel;
	$aResult['aPlan']  = $aPlan;
	$aResult['aTask']  = $aTask;

	return $aResult;
}

function weekinifin($year,$month,$day)
{
# Obtenemos el numero de la semana
	$semana=date("W",mktime(0,0,0,$month,$day,$year));
# Obtenemos el día de la semana de la fecha dada
	$diaSemana=date("w",mktime(0,0,0,$month,$day,$year));
# el 0 equivale al domingo...
	if($diaSemana==0)
		$diaSemana=7;
# A la fecha recibida, le restamos el dia de la semana y obtendremos el lunes
	$primerDia=date("YmdHis",mktime(12,0,0,$month,$day-$diaSemana+1,$year));
# A la fecha recibida, le sumamos el dia de la semana menos siete y obtendremos el domingo
	$ultimoDia=date("YmdHis",mktime(12,0,0,$month,$day+(7-$diaSemana),$year));

	return array($semana,$primerDia,$ultimoDia);
}

function select_attachment($selected='',$htmlname='code',$htmloption='',$showempty=0,$showlabel=0,$campo='code',$label='label')
{
	global $db, $langs, $conf,$user;
	$sql = "SELECT f.rowid, f.code, f.label FROM ".MAIN_DB_PREFIX."c_element_task AS f ";
	$sql.= " WHERE ";
	$sql.= " f.entity = ".$conf->entity;
	$sql.= " AND f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';
  //echo '<br>sel '.$selected;
	if ($selected <> 0 && $selected == '-1')
	{
		if ($showlabel > 0)
		{
			return $langs->trans('To be defined');
		}
	}

	if ($resql)
	{
		$html.= '<select class="flat" name="'.$htmlname.'" id="select'.$htmlname.'">';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->$campo)
				{
					$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->$label.'</option>';
					if ($showlabel)
					{
						return $obj->$label;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->$campo.'">'.$obj->$label.'</option>';
				}
				$i++;
			}
		}
		else
		{
			return '';
		}
		if ($showlabel)
			return '';
		$html.= '</select>';
		if ($user->admin) $html.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);

		return $html;
	}
}

/**
 * Show task lines with a particular parent
 *
 * @param	string	   	$inc				Line number (start to 0, then increased by recursive call)
 * @param   string		$parent				Id of parent project to show (0 to show all)
 * @param   Task[]		$lines				Array of lines
 * @param   int			$level				Level (start to 0, then increased/decrease by recursive call)
 * @param 	string		$var				Color
 * @param 	int			$showproject		Show project columns
 * @param	int			$taskrole			Array of roles of user for each tasks
 * @param	int			$projectsListId		List of id of project allowed to user (string separated with comma)
 * @param	int			$addordertick		Add a tick to move task
 * @return	void
*/
function monprojectLinepay(&$inc, $parent, &$lines, &$level, $var, $showproject, &$taskrole, $projectsListId='', $addordertick=0,$lVista=1,$lPay=false)
{
	global $user, $bc, $langs, $db;
	global $projectstatic, $taskstatic, $objecttime;
	global $taskpay;
	$lastprojectid=0;

	$projectsArrayId=explode(',',$projectsListId);


  // We declare counter as global because we want to edit them into recursive call
	global $total_projectlinesa_spent,$total_projectlinesa_planned,$total_projectlinesa_spent_if_planned;
	if ($level == 0)
	{
		$total_projectlinesa_spent=0;
		$total_projectlinesa_planned=0;
		$total_projectlinesa_spent_if_planned=0;
	}
	$numlines=count($lines);
	$sumPayment = 0;
	$sumApprove = 0;
	$sumPayable = 0;
	for ($i = 0 ; $i < $numlines ; $i++)
	{
		$lView = true;
		if ($user->societe_id>0)
		{
			if ($lines[$i]->array_options['options_c_view'] == 1)
				$lView = false;
		}
		if ($lView)
		{
			if ($parent == 0) $level = 0;
				// Process line
				// print "i:".$i."-".$lines[$i]->fk_project.'<br>';
			$js = 1;
			if ($js)
			{
					// Show task line.
				$showline=true;
				$showlineingray=0;
					// If there is filters to use
				if (is_array($taskrole))
				{
						// If task not legitimate to show, search if a legitimate task exists later in tree
					if (! isset($taskrole[$lines[$i]->id]) && $lines[$i]->id != $lines[$i]->fk_parent)
					{
							// So search if task has a subtask legitimate to show
						$foundtaskforuserdeeper=0;
						searchTaskInChild($foundtaskforuserdeeper,$lines[$i]->id,$lines,$taskrole);
							//print '$foundtaskforuserpeeper='.$foundtaskforuserdeeper.'<br>';
						if ($foundtaskforuserdeeper > 0)
						{
							$showlineingray=1;
								// We will show line but in gray
						}
						else
						{
							$showline=false;
								// No reason to show line
						}
					}
				}
				else
				{
					if (empty($user->rights->projet->all->lire))
					{
						if (! in_array($lines[$i]->fk_project, $projectsArrayId))
						{
							$showline=false;
						}
					}
				}
				if ($lPay)
				{
					if (!$_SESSION['aSelectlast'][$lines[$i]->idpay])
						$showline = false;
				}
				//revisamos lo que se debe pagar
										 //obtenemos la suma de la tarea
				$taskpay->getadvance($lines[$i]->id,'');
				//
				// 0 y 1= por pagar
				// 2= aprobado
				// 3= Pagado
				//
				//cantidades
				$nDif = $lines[$i]->array_options['options_unit_declared']-$taskpay->aArray[3];
				//if (price2num($taskpay->aArray[0]+$taskpay->aArray[1],'MT')<=0)
				//	$showline = false;
				//if ($lines[$i]->statutpay > 0) $showline = false;
				if ($nDif <= 0) $showline = false;
				if ($showline)
				{
					$lTask = true;
					$var = !$var;
					if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
					{
						$lastprojectid=$lines[$i]->fk_project;
					}
					if ($lines[$i]->array_options['options_c_grupo'] == 1)
					{
						$lTask = false;
						print '<tr  '.'class="backgroup"'.' id="row-'.$lines[$i]->id.'">'."\n";
					}
					else
						print '<tr  '.$bc[$var].' id="row-'.$lines[$i]->id.'">'."\n";

					if ($showproject)
					{
						print "<td>";
						if ($showlineingray) print '<i>';
						$projectstatic->id=$lines[$i]->fk_project;
						$projectstatic->ref=$lines[$i]->projectref;
						$projectstatic->public=$lines[$i]->public;
						if ($lines[$i]->public || in_array($lines[$i]->fk_project,$projectsArrayId))
						{
							print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'" >'.$lines[$i]->ref.'</a>';
								//$projectstatic->getNomUrl(1);
						}
						else
							print 'dddd';
							//$projectstatic->getNomUrl(1,'nolink');
						if ($showlineingray) print '</i>';
						print "</td>";

							// Project status
						print '<td>';
						$projectstatic->statut=$lines[$i]->projectstatus;
						print $projectstatic->getLibStatut(2);
						print "</td>";
					}

						// Ref of task
					print '<td>';
					if ($showlineingray)
					{
						print '<i>'.img_object('','projecttask').' '.$lines[$i]->ref.'</i>';
					}
					else
					{
						$taskstatic->id=$lines[$i]->id;
						$taskstatic->ref=$lines[$i]->ref;
						$taskstatic->label=($taskrole[$lines[$i]->id]?$langs->trans("YourRole").': '.$taskrole[$lines[$i]->id]:'');
						print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'&withproject=1" title="'.$langs->trans('ShowTask').'">'.img_object($langs->trans('ShowTask'),'projecttask').' '. $lines[$i]->ref.'</a>';
							//print $taskstatic->getNomUrl(1,'withproject');
					}
					print '</td>';

						// Title of task
					print "<td>";
					if ($showlineingray) print '<i>';
					else print '<a href="'.DOL_URL_ROOT.'/monprojet/task/payment.php?id='.$lines[$i]->id.'&withproject=1">';
					for ($k = 0 ; $k < $level ; $k++)
					{
						print "&nbsp; &nbsp; &nbsp;";
					}
					print $lines[$i]->label;
					if ($showlineingray) print '</i>';
					else print '</a>';
					print "</td>\n";

						// Date start
					print '<td align="center">';
					print dol_print_date($lines[$i]->date_start,'dayhour');
					print '</td>';

						// Date end
					print '<td align="center">';
					print dol_print_date($lines[$i]->date_end,'dayhour');
					print '</td>';

						//unit
					print '<td align="center">';
					if (!$lines[$i]->array_options['options_c_grupo'])
						print $lines[$i]->array_options['options_unit'];
					print '&nbsp;';
					print '</td>';
					if ($user->rights->monprojet->task->leerm)
					{
						print '<td align="right">';
						if (!$lines[$i]->array_options['options_c_grupo'])
							print price($lines[$i]->array_options['options_unit_amount']);
						else
							print '&nbsp;';
						print '</td>';
					}

					$plannedworkloadoutputformat='allhourmin';
					$timespentoutputformat='allhourmin';
					if (! empty($conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT)) $plannedworkloadoutputformat=$conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT;
					if (! empty($conf->global->PROJECT_TIMES_SPENT_FORMAT)) $timespentoutputformat=$conf->global->PROJECT_TIME_SPENT_FORMAT;

						//RQC CAMBIADO
						// Planned Workload (in working hours)
					print '<td align="right">';
					if ($lines[$i]->planned_workload != '')
					{
						print price($lines[$i]->array_options['options_unit_program']);
					}
					print '</td>';

						 // EJECUCION REPORTADA
					print '<td align="right">';
					print price($lines[$i]->array_options['options_unit_declared']);
					print '</td>';

						 //obtenemos la suma de la tarea
					//$taskpay->getadvance($lines[$i]->id,'');
						//
					   // 0 y 1= por pagar
						// 2= aprobado
						// 3= Pagado
						//
						//cantidades

						//quant payment total
					print '<td align="right">';
					print price(price2num($taskpay->aArray[3],'MT'));
					print '</td>';

						//quant present
					print '<td align="right">';
					print price(price2num($taskpay->aArray[2],'MT'));
					print '</td>';

						//quant for payment
					print '<td align="right">';
					print price(price2num($taskpay->aArray[0]+$taskpay->aArray[1],'MT'));
					print '</td>';

						//amount payment total
					print '<td align="right">';
					print price(price2num($taskpay->aArray[3]*$lines[$i]->array_options['options_unit_amount'],'MT'));
					$sumPayment+=price2num($taskpay->aArray[3]*$lines[$i]->array_options['options_unit_amount'],'MT');
					print '</td>';

						//amount present
					print '<td align="right">';
					print price(price2num($taskpay->aArray[2]*$lines[$i]->array_options['options_unit_amount'],'MT'));
					$sumApprove+=price2num($taskpay->aArray[2]*$lines[$i]->array_options['options_unit_amount'],'MT');
					print '</td>';

						//amount previus
					print '<td align="right">';
					print price(price2num(($taskpay->aArray[0]+$taskpay->aArray[1])*$lines[$i]->array_options['options_unit_amount'],'MT'));
					$sumPayable+=price2num(($taskpay->aArray[0]+$taskpay->aArray[1])*$lines[$i]->array_options['options_unit_amount'],'MT');
					print '</td>';

					if ($lVista != 4)
					{
						print '<td align="right">';
						if ($lVista == 3)
						{
								//para la seleccion del pago
							print '<input type="checkbox" name="selpay['.$lines[$i]->idpay.']" '.($_SESSION['aSelectlast'][$lines[$i]->idpay]?'checked':'').'>';
						}
						print '&nbsp;';
						print '</td>';
					}
						// // Tick to drag and drop
						// if ($addordertick)
						// 	{
						// 	  print '<td align="center" class="tdlineupdown hideonsmartphone">&nbsp;</td>';
						// 	}

					print "</tr>\n";

					if (! $showlineingray) $inc++;

						//$level++;
				}
			}
			else
			{
					//$level--;
			}
		}
	}
	if ($sumPayment > 0 || $sumApprove > 0 || $sumPayable > 0)
	{
		print '<tr class="liste_total nodrag nodrop">';
		print '<td class="liste_total">'.$langs->trans("Total").'</td>';
		if ($showproject) print '<td></td><td></td>';
		print '<td></td>';
		print '<td></td>';
		print '<td></td>';
		if ($user->rights->monprojet->task->leerm)
			print '<td align="right"></td>';

		print '<td></td>';
		print '<td></td>';

		print '<td align="right"></td>';
		print '<td align="right"></td>';
		print '<td align="right"></td>';
		print '<td align="right"></td>';

		print '<td align="right" class="nowrap liste_total">';
		print price(price2num($sumPayment,'MT'));
		print '</td>';
		print '<td align="right" class="nowrap liste_total">';
		print price(price2num($sumApprove,'MT'));
		print '</td>';
		print '<td align="right" class="nowrap liste_total">';
		print price(price2num($sumPayable,'MT'));
		print '</td>';
	  //if ($addordertick) print '<td class="hideonsmartphone"></td>';
		print '</tr>';
	}
	return array($sumPayment,$sumApprove,$sumPayable);
}

/*
 * function para listar los programados
*/
/**
 * Show task lines with a particular parent
 *
 * @param	string	   	$inc				Line number (start to 0, then increased by recursive call)
 * @param   string		$parent				Id of parent project to show (0 to show all)
 * @param   Task[]		$lines				Array of lines
 * @param   int			$level				Level (start to 0, then increased/decrease by recursive call)
 * @param 	string		$var				Color
 * @param 	int			$showproject		Show project columns
 * @param	int			$taskrole			Array of roles of user for each tasks
 * @param	int			$projectsListId		List of id of project allowed to user (string separated with comma)
 * @param	int			$addordertick		Add a tick to move task
 * @return	void
*/
function monprojectLineprog(&$inc, $parent, &$lines, &$level, $var, $showproject, &$taskrole, $projectsListId='', $addordertick=0,$lVista=1,$lPay=false)
{
	global $user, $bc, $langs, $db;
	global $projectstatic, $taskstatic, $objecttime;
	global $request;

	$lastprojectid=0;

	$projectsArrayId=explode(',',$projectsListId);


  // We declare counter as global because we want to edit them into recursive call
	global $total_projectlinesa_spent,$total_projectlinesa_planned,$total_projectlinesa_spent_if_planned;
	if ($level == 0)
	{
		$total_projectlinesa_spent=0;
		$total_projectlinesa_planned=0;
		$total_projectlinesa_spent_if_planned=0;
	}
	$numlines=count($lines);
	$sumPayment = 0;
	$sumApprove = 0;
	$sumPayable = 0;
	for ($i = 0 ; $i < $numlines ; $i++)
	{
		$lView = true;
		if ($user->societe_id>0)
			if ($lines[$i]->array_options['options_c_view'] == 1)
				$lView = false;
			if ($lView)
			{

				if ($parent == 0) $level = 0;
	  // Process line
	  // print "i:".$i."-".$lines[$i]->fk_project.'<br>';
				$js = 1;
				if ($js)
				{
	  // Show task line.
					$showline=1;
					$showlineingray=0;
	  // If there is filters to use
					if (is_array($taskrole))
					{
		  // If task not legitimate to show, search if a legitimate task exists later in tree
						if (! isset($taskrole[$lines[$i]->id]) && $lines[$i]->id != $lines[$i]->fk_parent)
						{
		  // So search if task has a subtask legitimate to show
							$foundtaskforuserdeeper=0;
							searchTaskInChild($foundtaskforuserdeeper,$lines[$i]->id,$lines,$taskrole);
		  //print '$foundtaskforuserpeeper='.$foundtaskforuserdeeper.'<br>';
							if ($foundtaskforuserdeeper > 0)
							{
			  $showlineingray=1;		// We will show line but in gray
			}
			else
			{
			  $showline=0;			// No reason to show line
			}
		}
	}
	else
	{
		  // Caller did not ask to filter on tasks of a specific user (this probably means he want also tasks of all users, into public project
		  // or into all other projects if user has permission to).
		if (empty($user->rights->projet->all->lire))
		{
		  // User is not allowed on this project and project is not public, so we hide line
			if (! in_array($lines[$i]->fk_project, $projectsArrayId))
			{
			  // Note that having a user assigned to a task into a project user has no permission on, should not be possible
			  // because assignement on task can be done only on contact of project.
			  // If assignement was done and after, was removed from contact of project, then we can hide the line.
				$showline=0;
			}
		}
	}
	if ($lPay)
	{
		if (!$_SESSION['aSelectlast'][$lines[$i]->idpay])
			$showline = false;
	}
	if ($showline)
	{
		$lTask = true;
		  // Break on a new project
		if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
		{
			$var = !$var;
			$lastprojectid=$lines[$i]->fk_project;
		}
		if ($lines[$i]->array_options['options_c_grupo'] == 1)
		{
			$lTask = false;
			print '<tr  '.'class="backgroup"'.' id="row-'.$lines[$i]->id.'">'."\n";
		}
		else
			print '<tr  '.$bc[$var].' id="row-'.$lines[$i]->id.'">'."\n";

		if ($showproject)
		{
		  // Project ref
			print "<td>";
			if ($showlineingray) print '<i>';
			$projectstatic->id=$lines[$i]->fk_project;
			$projectstatic->ref=$lines[$i]->projectref;
			$projectstatic->public=$lines[$i]->public;
			if ($lines[$i]->public || in_array($lines[$i]->fk_project,$projectsArrayId))
				print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'" >'.$lines[$i]->ref.'</a>';
			  //$projectstatic->getNomUrl(1);
			else
				print 'dddd';
			$projectstatic->getNomUrl(1,'nolink');
			if ($showlineingray) print '</i>';
			print "</td>";

		  // Project status
			print '<td>';
			$projectstatic->statut=$lines[$i]->projectstatus;
			print $projectstatic->getLibStatut(2);
			print "</td>";
		}

		   // Ref of task
		print '<td>';
		if ($showlineingray)
		{
			print '<i>'.img_object('','projecttask').' '.$lines[$i]->ref.'</i>';
		}
		else
		{
			$taskstatic->id=$lines[$i]->id;
			$taskstatic->ref=$lines[$i]->ref;
			$taskstatic->label=($taskrole[$lines[$i]->id]?$langs->trans("YourRole").': '.$taskrole[$lines[$i]->id]:'');
			print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'&withproject=1" title="'.$langs->trans('ShowTask').'">'.img_object($langs->trans('ShowTask'),'projecttask').' '. $lines[$i]->ref.'</a>';
			  //print $taskstatic->getNomUrl(1,'withproject');
		}
		print '</td>';

		  // Title of task
		print "<td>";
		if ($showlineingray) print '<i>';
		else print '<a href="'.DOL_URL_ROOT.'/monprojet/task/time.php?id='.$lines[$i]->id.'&withproject=1&riid='.$lines[$i]->requestitemid.'">';
		for ($k = 0 ; $k < $level ; $k++)
		{
			print "&nbsp; &nbsp; &nbsp;";
		}
		print $lines[$i]->label;
		if ($showlineingray) print '</i>';
		else print '</a>';
		print "</td>\n";

		  // Date start
		print '<td align="center">';
		print dol_print_date($lines[$i]->date_startr,'dayhour');
		print '</td>';

		  // Date end
		print '<td align="center">';
		print dol_print_date($lines[$i]->date_endr,'dayhour');
		print '</td>';

		  //unit
		print '<td align="center">';
		if (!$lines[$i]->array_options['options_c_grupo'])
			print $lines[$i]->array_options['options_unit'];
		print '&nbsp;';
		print '</td>';
		  //unitario
		if ($user->rights->monprojet->task->leerm)
		{
			print '<td align="right">';
			if (!$lines[$i]->array_options['options_c_grupo'])
				print price($lines[$i]->array_options['options_unit_amount']);
			else
				print '&nbsp;';
			print '</td>';
		}

		$plannedworkloadoutputformat='allhourmin';
		$timespentoutputformat='allhourmin';
		if (! empty($conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT)) $plannedworkloadoutputformat=$conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT;
		if (! empty($conf->global->PROJECT_TIMES_SPENT_FORMAT)) $timespentoutputformat=$conf->global->PROJECT_TIME_SPENT_FORMAT;

		  //unidades planificadas
		  // Planned Workload (in working hours)
		print '<td align="right">';
		print price(price2num($lines[$i]->array_options['options_unit_program'],'MT'));
		print '</td>';

		  // EJECUCION REPORTADA
		print '<td align="right">';
		print price(price2num($lines[$i]->array_options['options_unit_declared'],'MT'));
		print '</td>';

		  //obtenemos la suma de la tarea
		  //$objecttime->getadvance($lines[$i]->id,'');
		  /*
		   * 1= por pagar
		   * 2= aprobado
		   * 3= Pagado
		   */

		  //quant programadas
		  print '<td align="right">';
		  print price(price2num($lines[$i]->quantr,'MT'));
		  print '</td>';

		  //quant declared present
		  print '<td align="right">';
		  print price(price2num($lines[$i]->declaredr,'MT'));
		  print '</td>';


		  print '<td align="right">';
		  //habilitamos icono para ir a la solicitud
		  if ($user->rights->request->req->clo)
		  {
		  	$request->fetch($lines[$i]->requestid);
		  	if ($request->id == $lines[$i]->requestid)
		  		print $request->getNomUrlname(1,'',2);
		  }
		  //para la seleccion del pago
		  // print '<input type="checkbox" name="selpay['.$lines[$i]->id.']" '.($_SESSION['aSelectlast'][$lines[$i]->id]?'checked':'').'>';
		  print '&nbsp;';
		  print '</td>';

		  // // Tick to drag and drop
		  // if ($addordertick)
		  // 	{
		  // 	  print '<td align="center" class="tdlineupdown hideonsmartphone">&nbsp;</td>';
		  // 	}

		  print "</tr>\n";

		  if (! $showlineingray) $inc++;

		  //$level++;
		}
	}
	else
	{
	  //$level--;
	}
}
}
return array($sumPayment,$sumApprove,$sumPayable);
}

function select_code_guarantees($selected='',$htmlname='code_guarantee',$htmloption='',$showempty=0,$showlabel=0,$campo='code')
{
	global $db, $langs, $conf,$user;
	if ($showlabel && empty($selected)) return '';
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_guarantees AS f ";
	// $sql.= " WHERE ";
	// $sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($resql)
	{
		$html.= '<select class="flat" name="'.$htmlname.'">';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->$campo)
				{
					$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->libelle.'</option>';
					if ($showlabel)
					{
						return $obj->libelle;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->$campo.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		$html.= '</select>';
		if ($user->admin) $html.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);

		return $html;
	}
}

/**
 * Show task lines with a particular parent
 *
 * @param	string	   	$inc				Line number (start to 0, then increased by recursive call)
 * @param   string		$parent				Id of parent project to show (0 to show all)
 * @param   Task[]		$lines				Array of lines
 * @param   int			$level				Level (start to 0, then increased/decrease by recursive call)
 * @param 	string		$var				Color
 * @param 	int			$showproject		Show project columns
 * @param	int			$taskrole			Array of roles of user for each tasks
 * @param	int			$projectsListId		List of id of project allowed to user (string separated with comma)
 * @param	int			$addordertick		Add a tick to move task
 * @return	void
*/
function monprojectLinecontrat(&$inc, $parent, &$lines, &$level, $var, $showproject, &$taskrole, $projectsListId='', $addordertick=0,$lVista=1,$lPay=false)
{
	global $user, $bc, $langs, $db;
	global $projectstatic, $taskstatic, $objecttime;
	$lastprojectid=0;

	$projectsArrayId=explode(',',$projectsListId);


  // We declare counter as global because we want to edit them into recursive call
	global $total_projectlinesa_spent,$total_projectlinesa_planned,$total_projectlinesa_spent_if_planned;
	if ($level == 0)
	{
		$total_projectlinesa_spent=0;
		$total_projectlinesa_planned=0;
		$total_projectlinesa_spent_if_planned=0;
	}
	$numlines=count($lines);
	$sumPayment = 0;
	$sumApprove = 0;
	$sumPayable = 0;
	for ($i = 0 ; $i < $numlines ; $i++)
	{
		$lView = true;
		if ($user->societe_id>0)
		{
			if ($lines[$i]->array_options['options_c_view'] == 1)
				$lView = false;
		}
		if ($lView)
		{
			if ($parent == 0) $level = 0;
	  // Process line
	  // print "i:".$i."-".$lines[$i]->fk_project.'<br>';
			$js = 1;
			if ($js)
			{
	  // Show task line.
				$showline=1;
				$showlineingray=0;
	  // If there is filters to use
				if (is_array($taskrole))
				{
		  // If task not legitimate to show, search if a legitimate task exists later in tree
					if (! isset($taskrole[$lines[$i]->id]) && $lines[$i]->id != $lines[$i]->fk_parent)
					{
		  // So search if task has a subtask legitimate to show
						$foundtaskforuserdeeper=0;
						searchTaskInChild($foundtaskforuserdeeper,$lines[$i]->id,$lines,$taskrole);
		  //print '$foundtaskforuserpeeper='.$foundtaskforuserdeeper.'<br>';
						if ($foundtaskforuserdeeper > 0)
						{
							$showlineingray=1;
					// We will show line but in gray
						}
						else
						{
							$showline=0;
						// No reason to show line
						}
					}
				}
				else
				{
		  // Caller did not ask to filter on tasks of a specific user (this probably means he want also tasks of all users, into public project
		  // or into all other projects if user has permission to).
					if (empty($user->rights->projet->all->lire))
					{
		  // User is not allowed on this project and project is not public, so we hide line
						if (! in_array($lines[$i]->fk_project, $projectsArrayId))
						{
			  // Note that having a user assigned to a task into a project user has no permission on, should not be possible
			  // because assignement on task can be done only on contact of project.
			  // If assignement was done and after, was removed from contact of project, then we can hide the line.
							$showline=0;
						}
					}
				}
				if ($lVista == 1)
				{
					if (!$lines[$i]->array_options['options_fk_contrat']>0)
						$showline = 1;
					else
						$showline = 0;
				}
				if ($lVista == 2)
				{
					if ($lines[$i]->array_options['options_fk_contrat']>0)
						$showline = 1;
					else
						$showline = 0;
				}
				if ($lines[$i]->array_options['options_c_grupo']>0)
					$showline = 0;

				if ($showline)
				{
					$lTask = true;
		  // Break on a new project
					if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
					{
						$var = !$var;
						$lastprojectid=$lines[$i]->fk_project;
					}
					if ($lines[$i]->array_options['options_c_grupo'] == 1)
					{
						$lTask = false;
						print '<tr  '.'class="backgroup"'.' id="row-'.$lines[$i]->id.'">'."\n";
					}
					else
						print '<tr  '.$bc[$var].' id="row-'.$lines[$i]->id.'">'."\n";

		  // if ($showproject)
		  // 	{
		  // 	  // Project ref
		  // 	  print "<td>";
		  // 	  if ($showlineingray) print '<i>';
		  // 	  $projectstatic->id=$lines[$i]->fk_project;
		  // 	  $projectstatic->ref=$lines[$i]->projectref;
		  // 	  $projectstatic->public=$lines[$i]->public;
		  // 	  if ($lines[$i]->public || in_array($lines[$i]->fk_project,$projectsArrayId))
		  // 	    print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'" >'.$lines[$i]->ref.'</a>';
		  // 	      //$projectstatic->getNomUrl(1);
		  // 	  else
		  // 	    print 'dddd';
		  // 	      //$projectstatic->getNomUrl(1,'nolink');
		  // 	  if ($showlineingray) print '</i>';
		  // 	  print "</td>";

		  // 	  // Project status
		  // 	  print '<td>';
		  // 	  $projectstatic->statut=$lines[$i]->projectstatus;
		  // 	  print $projectstatic->getLibStatut(2);
		  // 	  print "</td>";
		  // 	}

		  // // Ref of task
		  // print '<td>';
		  // if ($showlineingray)
		  // 	{
		  // 	  print '<i>'.img_object('','projecttask').' '.$lines[$i]->ref.'</i>';
		  // 	}
		  // else
		  // 	{
		  // 	  $taskstatic->id=$lines[$i]->id;
		  // 	  $taskstatic->ref=$lines[$i]->ref;
		  // 	  $taskstatic->label=($taskrole[$lines[$i]->id]?$langs->trans("YourRole").': '.$taskrole[$lines[$i]->id]:'');
		  // 	  print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'&withproject=1" title="'.$langs->trans('ShowTask').'">'.img_object($langs->trans('ShowTask'),'projecttask').' '. $lines[$i]->ref.'</a>';
		  // 	  //print $taskstatic->getNomUrl(1,'withproject');
		  // 	}
		  // print '</td>';

		  // Title of task
					print "<td>";
					if ($showlineingray) print '<i>';
					else print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'&withproject=1">';
					for ($k = 0 ; $k < $level ; $k++)
					{
						print "&nbsp; &nbsp; &nbsp;";
					}
					print $lines[$i]->label;
					if ($showlineingray) print '</i>';
					else print '</a>';
					print "</td>\n";

		  // Date start
					print '<td align="center">';
					print dol_print_date($lines[$i]->date_start,'dayhour');
					print '</td>';

		  // Date end
					print '<td align="center">';
					print dol_print_date($lines[$i]->date_end,'dayhour');
					print '</td>';

		  //unit
					print '<td align="center">';
					if (!$lines[$i]->array_options['options_c_grupo'])
						print $lines[$i]->array_options['options_unit'];
					print '&nbsp;';
					print '</td>';

		  // if ($user->rights->monprojet->task->leerm)
		  // 	{
		  // 	  print '<td align="right">';
		  // 	  if (!$lines[$i]->array_options['options_c_grupo'])
		  // 	    print price($lines[$i]->array_options['options_unit_amount']);
		  // 	  else
		  // 	    print '&nbsp;';
		  // 	  print '</td>';
		  // 	}

					$plannedworkloadoutputformat='allhourmin';
					$timespentoutputformat='allhourmin';
					if (! empty($conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT)) $plannedworkloadoutputformat=$conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT;
					if (! empty($conf->global->PROJECT_TIMES_SPENT_FORMAT)) $timespentoutputformat=$conf->global->PROJECT_TIME_SPENT_FORMAT;

		  //RQC CAMBIADO
		  // // Planned Workload (in working hours)
		  // print '<td align="right">';
		  // if ($lines[$i]->planned_workload != '')
		  // 	{
		  // 	  print $lines[$i]->array_options['options_unit_program'];
		  // 	}
		  // print '</td>';

		  // // EJECUCION REPORTADA
		  // print '<td align="right">';
		  // print $lines[$i]->array_options['options_unit_declared'];
		  // print '</td>';

		  //obtenemos la suma de la tarea
		  //	      $objecttime->getadvance($lines[$i]->id,'');
		  //
		  // * 1= por pagar
		  // * 2= aprobado
		  // * 3= Pagado
		  //

		  // //quant payment total
		  // print '<td align="right">';
		  // print price(price2num($objecttime->aArray[3],'MT'));
		  // print '</td>';

		  // //quant present
		  // print '<td align="right">';
		  // print price(price2num($objecttime->aArray[2],'MT'));
		  // print '</td>';

		  // //quant previus
		  // print '<td align="right">';
		  // print price(price2num($objecttime->aArray[1],'MT'));
		  // print '</td>';

		  // //amount payment total
		  // print '<td align="right">';
		  // print price(price2num($objecttime->aArray[3]*$lines[$i]->array_options['options_unit_amount'],'MT'));
		  // $sumPayment+=price2num($objecttime->aArray[3]*$lines[$i]->array_options['options_unit_amount'],'MT');
		  // print '</td>';

		  // //amount present
		  // print '<td align="right">';
		  // print price(price2num($objecttime->aArray[2]*$lines[$i]->array_options['options_unit_amount'],'MT'));
		  // $sumApprove+=price2num($objecttime->aArray[2]*$lines[$i]->array_options['options_unit_amount'],'MT');
		  // print '</td>';

		  // //amount previus
		  // print '<td align="right">';
		  // print price(price2num($objecttime->aArray[1]*$lines[$i]->array_options['options_unit_amount'],'MT'));
		  // $sumPayable+=price2num($objecttime->aArray[1]*$lines[$i]->array_options['options_unit_amount'],'MT');
		  // print '</td>';

					print '<td align="right">';
		  //para la seleccion del pago
					if ($lVista == 1)
						print '<input type="checkbox" name="selcon['.$lines[$i]->id.']" '.($_SESSION['aSelectcont'][$lines[$i]->id]?'checked':'').'>';
					print '&nbsp;';
					print '</td>';

		  // // Tick to drag and drop
		  // if ($addordertick)
		  // 	{
		  // 	  print '<td align="center" class="tdlineupdown hideonsmartphone">&nbsp;</td>';
		  // 	}

					print "</tr>\n";

					if (! $showlineingray) $inc++;

		  //$level++;
				}
			}
			else
			{
	  //$level--;
			}
		}
	}

	if ($sumPayment > 0 || $sumApprove > 0 || $sumPayable > 0)
	{
		print '<tr class="liste_total nodrag nodrop">';
		print '<td class="liste_total">'.$langs->trans("Total").'</td>';
		if ($showproject) print '<td></td><td></td>';
		print '<td></td>';
		print '<td></td>';
		print '<td></td>';
		if ($user->rights->monprojet->task->leerm)
			print '<td align="right"></td>';

		print '<td></td>';
		print '<td></td>';

		print '<td align="right"></td>';
		print '<td align="right"></td>';
		print '<td align="right"></td>';

		print '<td align="right" class="nowrap liste_total">';
		print price(price2num($sumPayment,'MT'));
		print '</td>';
		print '<td align="right" class="nowrap liste_total">';
		print price(price2num($sumApprove,'MT'));
		print '</td>';
		print '<td align="right" class="nowrap liste_total">';
		print price(price2num($sumPayable,'MT'));
		print '</td>';
	  //if ($addordertick) print '<td class="hideonsmartphone"></td>';
		print '</tr>';
	}
	return array($sumPayment,$sumApprove,$sumPayable);
}


function actualiza($object,$extralabels_task,$projecttaskadd)
{
	global $db,$user,$conf;
	global $taskadd;
	$modetask = 0;
	$socid = 0;
  //recuperamos la lista de tareas
	$lines=$taskadd->getTasksArray(0, 0, $object->id, $socid, $modetask);
	$numlines = count ($lines);
	for ($i=0; $i < $numlines; $i++)
	{
		$taskadd->fetch ($lines[$i]->id);
		$res=$taskadd->fetch_optionals($taskadd->id,$extralabels_task);
	  //buscamos la tarea
		$res = $projecttaskadd->fetch('',$taskadd->id);
		if ($res == 0)
		{
	  //nuevo
			$projecttaskadd->initAsSpecimen();
			$projecttaskadd->fk_task = $lines [$i]->id;
			$projecttaskadd->fk_contrat = $taskadd->array_options['options_fk_contrat']+0;
			$projecttaskadd->c_grupo = $taskadd->array_options['options_c_grupo']+0;
			$projecttaskadd->fk_unit = $taskadd->array_options['options_fk_unit']+0;
			$projecttaskadd->fk_type = $taskadd->array_options['options_fk_type']+0;
			$projecttaskadd->fk_item = $taskadd->array_options['options_fk_item']+0;
			$projecttaskadd->unit_program = $taskadd->array_options['options_unit_program']+0;
			$projecttaskadd->unit_declared = $taskadd->array_options['options_unit_declared']+0;
			$projecttaskadd->unit_ejecuted = $taskadd->array_options['options_unit_ejecuted']+0;
			$projecttaskadd->unit_amount = $taskadd->array_options['options_unit_amount']+0;
			$projecttaskadd->detail_close = $taskadd->array_options['options_detail_close'];
			$projecttaskadd->fk_user_create = $user->id;
			$projecttaskadd->fk_user_mod = $user->id;
			$projecttaskadd->date_create = (!$taskadd->array_options['options_date_creaete']?dol_now():$taskadd->array_options['options_date_create']);
			$projecttaskadd->tms = dol_now();
			$projecttaskadd->statut = 1;
			$result = $projecttaskadd->create($user);
			if (!$result>0)
			{
				setEventMessages($projecttaskadd->error,$projecttaskadd->errors,'errors');
				exit;
			}
		}
		else
		{
	  //modificacion
			if (empty($projecttaskadd->c_grupo))
			{
				$projecttaskadd->c_grupo = $taskadd->array_options['options_c_grupo']+0;
				$projecttaskadd->fk_user_mod = $user->id;
				$projecttaskadd->update($user);
			}
		}
	}
}


//proceso para exportacion a excel
/**
 * Show task lines with a particular parent
 *
 * @param	string	   	$inc				Line number (start to 0, then increased by recursive call)
 * @param   string		$parent				Id of parent project to show (0 to show all)
 * @param   Task[]		$lines				Array of lines
 * @param   int			$level				Level (start to 0, then increased/decrease by recursive call)
 * @param 	string		$var				Color
 * @param 	int			$showproject		Show project columns
 * @param	int			$taskrole			Array of roles of user for each tasks
 * @param	int			$projectsListId		List of id of project allowed to user (string separated with comma)
 * @param	int			$addordertick		Add a tick to move task
 * @return	void
 */
function monprojectLineexport(&$inc, $parent, &$lines, &$level, $var, $showproject, &$taskrole, $projectsListId='', $addordertick=0,$lVista=1,array $aData=array(),array $aLevel=array(),array $aNlevel=array())
{
	global $user, $bc, $langs, $db;
	global $projectstatic, $taskstatic;
	global $items,$typeitem;
	$lastprojectid=0;
	$projectsArrayId=explode(',',$projectsListId);

	$numlines=count($lines);

  // We declare counter as global because we want to edit them into recursive call
	global $total_projectlinesa_spent,$total_projectlinesa_planned,$total_projectlinesa_spent_if_planned;
	if ($level == 0)
	{
		$total_projectlinesa_spent=0;
		$total_projectlinesa_planned=0;
		$total_projectlinesa_spent_if_planned=0;
	}

	for ($i = 0 ; $i < $numlines ; $i++)
	{
		$var = !$var;
		if ($parent == 0) $level = 0;
	  // Process line
	  // print "i:".$i."-".$lines[$i]->fk_project.'<br>';
		if ($lines[$i]->fk_parent == $parent)
		{
	  // Show task line.
			$showline=1;
			$showlineingray=0;
	  // If there is filters to use
			if (is_array($taskrole))
			{
		  // If task not legitimate to show, search if a legitimate task exists later in tree
				if (! isset($taskrole[$lines[$i]->id]) && $lines[$i]->id != $lines[$i]->fk_parent)
				{
		  // So search if task has a subtask legitimate to show
					$foundtaskforuserdeeper=0;
					searchTaskInChild($foundtaskforuserdeeper,$lines[$i]->id,$lines,$taskrole);
					if ($foundtaskforuserdeeper > 0)
					{
			  $showlineingray=1;		// We will show line but in gray
			}
			else
			{
			  $showline=0;			// No reason to show line
			}
		}
	}
	else
	{
		  // Caller did not ask to filter on tasks of a specific user (this probably means he want also tasks of all users, into public project
		  // or into all other projects if user has permission to).
		if (empty($user->rights->projet->all->lire))
		{
		  // User is not allowed on this project and project is not public, so we hide line
			if (! in_array($lines[$i]->fk_project, $projectsArrayId))
			{
			  // Note that having a user assigned to a task into a project user has no permission on, should not be possible
			  // because assignement on task can be done only on contact of project.
			  // If assignement was done and after, was removed from contact of project, then we can hide the line.
				$showline=0;
			}
		}
	}
	if ($showline)
	{
		$lTask = true;
		  // Break on a new project
		if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
		{
			$lastprojectid=$lines[$i]->fk_project;
		}
		if ($lines[$i]->array_options['options_c_grupo'] == 1)
		{
			$lTask = false;
		}

		if ($showproject)
		{
		  // Project ref
		  //print_r($projectstatic);
		  //echo '<hr>id '.$lines[$i]->fk_project;
			$projectstatic->id=$lines[$i]->fk_project;
			$projectstatic->ref=$lines[$i]->projectref;
			$projectstatic->public=$lines[$i]->public;
			$aData[$lines[$i]->id]['projet'] = $projectstatic->getNomUrl(1);
			$projectstatic->statut=$lines[$i]->projectstatus;
			$aData[$lines[$i]->id]['projetstatus'] = $projectstatic->getLibStatut(2);
		}
		//echo '<hr>iniciando con line '.$lines[$i]->id;
		//echo ' '.$lines[$i]->label.' numlevel '.$level;
		//buscamos el aLevel
		$numberLevel = 1;
		if ($aLevel[$level])
		{
			$numberLevel = $aLevel[$level]+1;
			$aLevel[$level] = $numberLevel;
		}
		else
			$aLevel[$level] = $numberLevel;

		//armando el aNlevel
		$aNlevel[$lines[$i]->id] = $numberLevel;
		//armamos el line_number hasta que fk_parent = 0;
		$lLoop = true;
		//registro del idprincipal
		$text = $numberLevel;
		//echo '<br>parent_ '.
		$fkParent = $lines[$i]->fk_parent+0;
		if ($fkParent>0)
		{
			//echo '<hr>buscando '.$fkParent;
			$text = $aNlevel[$fkParent].'.'.$text;
			$taskstatic->fetch($fkParent);
			if ($taskstatic->fk_task_parent > 0)
			{
				//echo '<br>parent_1 '.$taskstatic->fk_task_parent;
				while ($lLoop ==true)
				{
					$taskstatic->fetch($fkParent);
					$fkParent = $taskstatic->fk_task_parent;
					//echo '<br>parent_2 '.$fkParent;
					if ($fkParent > 0)
						$text = $aNlevel[$fkParent].'.'.$text;
					if (empty($fkParent)) $lLoop = false;
				}
			}
			else
			{
				//echo '<br>armando el textfinal '.$text = $aNlevel[$fkParent].'.'.$text;
			}
		}
		//echo '<br>text '.$text;
		//print_r($aLevel);
		//print_r($aNlevel);
		  // Ref of task
		$aData[$lines[$i]->id]['id'] = $lines[$i]->id;
		$aData[$lines[$i]->id]['level'] = $level+1;
		$aData[$lines[$i]->id]['line_number'] = $text;
		$aData[$lines[$i]->id]['order_ref'] = $lines[$i]->order_ref;
		$aData[$lines[$i]->id]['numberLevel'] = $numberLevel;
		$aData[$lines[$i]->id]['fk_parent'] = $lines[$i]->fk_parent;

		$aData[$lines[$i]->id]['ref'] = $lines[$i]->ref;
		$aData[$lines[$i]->id]['label'] = $lines[$i]->label;
		$aData[$lines[$i]->id]['date_start'] = dol_print_date($lines[$i]->date_start,'dayhour');
		$aData[$lines[$i]->id]['date_end'] = dol_print_date($lines[$i]->date_end,'dayhour');
		$aData[$lines[$i]->id]['datestart'] = $lines[$i]->date_start;
		$aData[$lines[$i]->id]['dateend'] = $lines[$i]->date_end;

		$plannedworkloadoutputformat='allhourmin';
		$timespentoutputformat='allhourmin';
		if (! empty($conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT)) $plannedworkloadoutputformat=$conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT;
		if (! empty($conf->global->PROJECT_TIMES_SPENT_FORMAT)) $timespentoutputformat=$conf->global->PROJECT_TIME_SPENT_FORMAT;
		  //unit
		if (!$lines[$i]->array_options['options_c_grupo'])
		{
			$aData[$lines[$i]->id]['unit'] = $lines[$i]->array_options['options_unit'];
			$aData[$lines[$i]->id]['unit_code'] = $lines[$i]->array_options['options_unitc'];
		}
		$aData[$lines[$i]->id]['group'] = $lines[$i]->array_options['options_c_grupo'];
		  //buscamos el parent
		$taskstatic->fetch($lines[$i]->fk_parent);
		$aData[$lines[$i]->id]['fk_parent'] = $lines[$i]->fk_parent;

		if ($taskstatic->id == $lines[$i]->fk_parent)
			$aData[$lines[$i]->id]['parent'] = $taskstatic->ref;
		  //detail
		$aData[$lines[$i]->id]['detail'] = $lines[$i]->description;
		$aData[$lines[$i]->id]['unit_program'] = $lines[$i]->array_options['options_unit_program'];
		$aData[$lines[$i]->id]['fk_unit'] = $lines[$i]->array_options['options_fk_unit'];
		$aData[$lines[$i]->id]['unit_amount'] = $lines[$i]->array_options['options_unit_amount'];
		$aData[$lines[$i]->id]['fk_item'] = $lines[$i]->array_options['options_fk_item'];
		$aData[$lines[$i]->id]['fk_type'] = $lines[$i]->array_options['options_fk_type'];

		if ($conf->budget->enabled)
		{
			if ($lines[$i]->array_options['options_fk_item']>0)
			{
				$items->fetch($lines[$i]->array_options['options_fk_item']);
				if ($items->id == $lines[$i]->array_options['options_fk_item'])
					$aData[$lines[$i]->id]['item'] = $items->ref;

			}
			if ($lines[$i]->array_options['options_fk_type']>0)
			{
				$typeitem->fetch($lines[$i]->array_options['options_fk_type']);
				if ($typeitem->id == $lines[$i]->array_options['options_fk_type'])
				{
					$aData[$lines[$i]->id]['type'] = $typeitem->ref;
					$aData[$lines[$i]->id]['typename'] = $typeitem->detail;
				}

			}
		}
		if (! $showlineingray) $inc++;

		$level++;
		if ($lines[$i]->id)
			$aData = monprojectLineexport($inc, $lines[$i]->id, $lines, $level, $var, $showproject, $taskrole, $projectsListId, $addordertick,$lVista,$aData,$aLevel,$aNlevel);
		$level--;
		$total_projectlinesa_spent += $lines[$i]->duration;
		$total_projectlinesa_planned += $lines[$i]->planned_workload;
		if ($lines[$i]->planned_workload) $total_projectlinesa_spent_if_planned += $lines[$i]->duration;
	}
}
}
return $aData;
}

//linesataskmine
/**
 * Show task lines with a particular parent
 *
 * @param	string	   	$inc				Line number (start to 0, then increased by recursive call)
 * @param   string		$parent				Id of parent project to show (0 to show all)
 * @param   Task[]		$lines				Array of lines
 * @param   int			$level				Level (start to 0, then increased/decrease by recursive call)
 * @param 	string		$var				Color
 * @param 	int			$showproject		Show project columns
 * @param	int			$taskrole			Array of roles of user for each tasks
 * @param	int			$projectsListId		List of id of project allowed to user (string separated with comma)
 * @param	int			$addordertick		Add a tick to move task
 * @return	void
 */
//proceso para exportacion a excel
/**
 * Show task lines with a particular parent
 *
 * @param	string	   	$inc				Line number (start to 0, then increased by recursive call)
 * @param   string		$parent				Id of parent project to show (0 to show all)
 * @param   Task[]		$lines				Array of lines
 * @param   int			$level				Level (start to 0, then increased/decrease by recursive call)
 * @param 	string		$var				Color
 * @param 	int			$showproject		Show project columns
 * @param	int			$taskrole			Array of roles of user for each tasks
 * @param	int			$projectsListId		List of id of project allowed to user (string separated with comma)
 * @param	int			$addordertick		Add a tick to move task
 * @return	void
 */
function monprojectLinemine(&$inc, $parent, &$lines,
	&$level, $var, $showproject,
	&$taskrole, $projectsListId='', $addordertick=0,
	$lVista=1,array $aData=array(),$projectstatic,
	$taskstatic,$items,$typeitem)
{
	global $conf, $user, $bc, $langs, $db;
  // global $projectstatic, $taskstatic;
  // global $items,$typeitem;
	$lastprojectid=0;
	$projectsArrayId=explode(',',$projectsListId);

	$numlines=count($lines);

  // We declare counter as global because we want to edit them into recursive call
	global $total_projectlinesa_spent,$total_projectlinesa_planned,$total_projectlinesa_spent_if_planned;
	if ($level == 0)
	{
		$total_projectlinesa_spent=0;
		$total_projectlinesa_planned=0;
		$total_projectlinesa_spent_if_planned=0;
	}

	for ($i = 0 ; $i < $numlines ; $i++)
	{
		$lView = true;
		if ($user->societe_id>0)
			if ($lines[$i]->array_options['options_c_view'] == 1)
				$lView = false;
			if ($lView)
			{
				$var = !$var;
				if ($parent == 0) $level = 0;
	  // Process line
	  // print "i:".$i."-".$lines[$i]->fk_project.'<br>';
				if ($lines[$i]->fk_parent == $parent)
				{
	  // Show task line.
					$showline=1;
					$showlineingray=0;
	  // If there is filters to use
					if (is_array($taskrole))
					{
		  // If task not legitimate to show, search if a legitimate task exists later in tree
						if (! isset($taskrole[$lines[$i]->id]) && $lines[$i]->id != $lines[$i]->fk_parent)
						{
		  // So search if task has a subtask legitimate to show
							$foundtaskforuserdeeper=0;
							searchTaskInChild($foundtaskforuserdeeper,$lines[$i]->id,$lines,$taskrole);
							if ($foundtaskforuserdeeper > 0)
							{
			  $showlineingray=1;		// We will show line but in gray
			}
			else
			{
			  $showline=0;			// No reason to show line
			}
		}
	}
	else
	{
		  // Caller did not ask to filter on tasks of a specific user (this probably means he want also tasks of all users, into public project
		  // or into all other projects if user has permission to).
		if (empty($user->rights->projet->all->lire))
		{
		  // User is not allowed on this project and project is not public, so we hide line
			if (! in_array($lines[$i]->fk_project, $projectsArrayId))
			{
			  // Note that having a user assigned to a task into a project user has no permission on, should not be possible
			  // because assignement on task can be done only on contact of project.
			  // If assignement was done and after, was removed from contact of project, then we can hide the line.
				$showline=0;
			}
		}
	}

	if ($showline)
	{
		$lTask = true;
		  // Break on a new project
		if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
		{
			$lastprojectid=$lines[$i]->fk_project;
		}
		if ($lines[$i]->array_options['options_c_grupo'] == 1)
		{
			$lTask = false;
		}

		if ($showproject)
		{
		  // Project ref
			$projectstatic->id=$lines[$i]->fk_project;
			$projectstatic->ref=$lines[$i]->projectref;
			$projectstatic->public=$lines[$i]->public;
			$aData[$lines[$i]->id]['projetid'] = $lines[$i]->fk_project;

			$aData[$lines[$i]->id]['projet'] = $projectstatic->getNomUrl(1);
			$projectstatic->statut=$lines[$i]->projectstatus;
			$aData[$lines[$i]->id]['projetstatus'] = $projectstatic->getLibStatut(2);
		}

		  // Ref of task
		$aData[$lines[$i]->id]['id'] = $lines[$i]->id;

		$aData[$lines[$i]->id]['ref'] = $lines[$i]->ref;
		$aData[$lines[$i]->id]['label'] = $lines[$i]->label;
		$aData[$lines[$i]->id]['date_start'] = dol_print_date($lines[$i]->date_start,'dayhour');
		$aData[$lines[$i]->id]['date_end'] = dol_print_date($lines[$i]->date_end,'dayhour');

		$plannedworkloadoutputformat='allhourmin';
		$timespentoutputformat='allhourmin';
		if (! empty($conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT)) $plannedworkloadoutputformat=$conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT;
		if (! empty($conf->global->PROJECT_TIMES_SPENT_FORMAT)) $timespentoutputformat=$conf->global->PROJECT_TIME_SPENT_FORMAT;
		  //unit
		if (!$lines[$i]->array_options['options_c_grupo'])
		{
			$aData[$lines[$i]->id]['unit'] = $lines[$i]->array_options['options_unit'];
			$aData[$lines[$i]->id]['unit_code'] = $lines[$i]->array_options['options_unitc'];
		}
		$aData[$lines[$i]->id]['group'] = $lines[$i]->array_options['options_c_grupo'];
		  //buscamos el parent
		$taskstatic->fetch($lines[$i]->fk_parent);
		$aData[$lines[$i]->id]['fk_parent'] = $lines[$i]->fk_parent;

		if ($taskstatic->id == $lines[$i]->fk_parent)
			$aData[$lines[$i]->id]['parent'] = $taskstatic->ref;
		  //detail
		$aData[$lines[$i]->id]['detail'] = $lines[$i]->description;
		$aData[$lines[$i]->id]['unit_program'] = $lines[$i]->array_options['options_unit_program'];
		$aData[$lines[$i]->id]['fk_unit'] = $lines[$i]->array_options['options_fk_unit'];
		$aData[$lines[$i]->id]['unit_amount'] = $lines[$i]->array_options['options_unit_amount'];
		$aData[$lines[$i]->id]['fk_item'] = $lines[$i]->array_options['options_fk_item'];
		$aData[$lines[$i]->id]['fk_type'] = $lines[$i]->array_options['options_fk_type'];

		if ($conf->budget->enabled)
		{
			if ($lines[$i]->array_options['options_fk_item']>0)
			{
				$items->fetch($lines[$i]->array_options['options_fk_item']);
				if ($items->id == $lines[$i]->array_options['options_fk_item'])
					$aData[$lines[$i]->id]['item'] = $items->ref;

			}
			if ($lines[$i]->array_options['options_fk_type']>0)
			{
				$typeitem->fetch($lines[$i]->array_options['options_fk_type']);
				if ($typeitem->id == $lines[$i]->array_options['options_fk_type'])
					$aData[$lines[$i]->id]['type'] = $typeitem->ref;

			}
		}
		if (! $showlineingray) $inc++;

		$level++;
		if ($lines[$i]->id)
			$aData = monprojectLinemine($inc, $lines[$i]->id, $lines,
				$level, $var, $showproject,
				$taskrole, $projectsListId, $addordertick,
				$lVista,$aData,$projectstatic,
				$taskstatic,$items,$typeitem);
		$level--;
		$total_projectlinesa_spent += $lines[$i]->duration;
		$total_projectlinesa_planned += $lines[$i]->planned_workload;
		if ($lines[$i]->planned_workload) $total_projectlinesa_spent_if_planned += $lines[$i]->duration;
	}
}
}
}
return $aData;
}

/**
 * Return HTML table with list of projects and number of opened tasks
 *
 * @param	DoliDB	$db					Database handler
 * @param	Form	$form				Object form
 * @param   int		$socid				Id thirdparty
 * @param   int		$projectsListId     Id of project i have permission on
 * @param   int		$mytasks            Limited to task i am contact to
 * @param	int		$statut				-1=No filter on statut, 0 or 1 = Filter on status
 * @param	array	$listofoppstatus	List of opportunity status
 * @return	void
 */

//no validamos la societe
function print_monprojecttasks_array($db, $form, $socid, $projectsListId, $mytasks=0, $statut=-1, $listofoppstatus=array())
{
	global $langs,$conf,$user,$bc;

	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

	$projectstatic=new Project($db);

	$sortfield='';
	$sortorder='';
	$project_year_filter=0;

	$title=$langs->trans("Projects");
	if (strcmp($statut, '') && $statut >= 0) $title=$langs->trans("Projects").' '.$langs->trans($projectstatic->statuts_long[$statut]);

	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print_liste_field_titre($title,"index.php","","","","",$sortfield,$sortorder);
	if (! empty($conf->global->PROJECT_USE_OPPORTUNITIES))
	{
		print_liste_field_titre($langs->trans("OpportunityAmount"),"","","","",'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("OpportunityStatus"),"","","","",'align="right"',$sortfield,$sortorder);
	}
	if (empty($conf->global->PROJECT_HIDE_TASKS)) print_liste_field_titre($langs->trans("Tasks"),"","","","",'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Status"),"","","","",'align="right"',$sortfield,$sortorder);
	print "</tr>\n";

	$sql = "SELECT p.rowid as projectid, p.ref, p.title, p.fk_user_creat, p.public, p.fk_statut as status, p.fk_opp_status as opp_status, p.opp_amount, COUNT(DISTINCT t.rowid) as nb";	// We use DISTINCT here because line can be doubled if task has 2 links to same user
	$sql.= " FROM ".MAIN_DB_PREFIX."projet as p";
	if ($mytasks)
	{
		$sql.= ", ".MAIN_DB_PREFIX."projet_task as t";
		$sql.= ", ".MAIN_DB_PREFIX."element_contact as ec";
		$sql.= ", ".MAIN_DB_PREFIX."c_type_contact as ctc";
	}
	else
	{
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet_task as t ON p.rowid = t.fk_projet";
	}
	$sql.= " WHERE p.entity = ".$conf->entity;
	$sql.= " AND p.rowid IN (".$projectsListId.")";
	//if ($socid) $sql.= "  AND (p.fk_soc IS NULL OR p.fk_soc = 0 OR p.fk_soc = ".$socid.")";
	if ($mytasks)
	{
		$sql.= " AND p.rowid = t.fk_projet";
		$sql.= " AND ec.element_id = t.rowid";
		$sql.= " AND ctc.rowid = ec.fk_c_type_contact";
		$sql.= " AND ctc.element = 'project_task'";
		$sql.= " AND ec.fk_socpeople = ".$user->id;
	}
	if ($statut >= 0)
	{
		$sql.= " AND p.fk_statut = ".$statut;
	}
	if (!empty($conf->global->PROJECT_LIMIT_YEAR_RANGE))
	{
		$project_year_filter = GETPOST("project_year_filter");
		//Check if empty or invalid year. Wildcard ignores the sql check
		if ($project_year_filter != "*")
		{
			if (empty($project_year_filter) || !ctype_digit($project_year_filter))
			{
				$project_year_filter = date("Y");
			}
			$sql.= " AND (p.dateo IS NULL OR p.dateo <= ".$db->idate(dol_get_last_day($project_year_filter,12,false)).")";
			$sql.= " AND (p.datee IS NULL OR p.datee >= ".$db->idate(dol_get_first_day($project_year_filter,1,false)).")";
		}
	}
	$sql.= " GROUP BY p.rowid, p.ref, p.title, p.fk_user_creat, p.public, p.fk_statut, p.fk_opp_status, p.opp_amount";
	$sql.= " ORDER BY p.title, p.ref";

	$var=true;
	$resql = $db->query($sql);
	if ( $resql )
	{
		$total_task = 0;
		$total_opp_amount = 0;
		$ponderated_opp_amount = 0;

		$num = $db->num_rows($resql);
		$i = 0;

		while ($i < $num)
		{
			$objp = $db->fetch_object($resql);

			$projectstatic->id = $objp->projectid;
			$projectstatic->user_author_id = $objp->fk_user_creat;
			$projectstatic->public = $objp->public;

			// Check is user has read permission on project
			$userAccess = $projectstatic->restrictedProjectArea($user);
			if ($userAccess >= 0)
			{
				$var=!$var;
				print "<tr ".$bc[$var].">";
				print '<td class="nowrap">';
				$projectstatic->ref=$objp->ref;
				print $projectstatic->getNomUrl(1);
				print ' - '.dol_trunc($objp->title,24).'</td>';
				if (! empty($conf->global->PROJECT_USE_OPPORTUNITIES))
				{
					print '<td align="right">';
					if ($objp->opp_amount) print price($objp->opp_amount, 0, '', 1, -1, -1, $conf->currency);
					print '</td>';
					print '<td align="right">';
					$code = dol_getIdFromCode($db, $objp->opp_status, 'c_lead_status', 'rowid', 'code');
					if ($code) print $langs->trans("OppStatus".$code);
					print '</td>';
				}
				$projectstatic->statut = $objp->status;
				if (empty($conf->global->PROJECT_HIDE_TASKS)) print '<td align="right">'.$objp->nb.'</td>';
				print '<td align="right">'.$projectstatic->getLibStatut(3).'</td>';
				print "</tr>\n";

				$total_task = $total_task + $objp->nb;
				$total_opp_amount = $total_opp_amount + $objp->opp_amount;
				$ponderated_opp_amount = $ponderated_opp_amount + price2num($listofoppstatus[$objp->opp_status] * $objp->opp_amount / 100);
			}

			$i++;
		}

		print '<tr><td>'.$langs->trans("Total")."</td>";
		if (! empty($conf->global->PROJECT_USE_OPPORTUNITIES))
		{
			print '<td align="right">'.price($total_opp_amount, 0, '', 1, -1, -1, $conf->currency).'</td>';
			print '<td align="right">'.$form->textwithpicto(price($ponderated_opp_amount, 0, '', 1, -1, -1, $conf->currency), $langs->trans("OpportunityPonderatedAmount"), 1).'</td>';
		}
		if (empty($conf->global->PROJECT_HIDE_TASKS)) print '<td align="right">'.$total_task.'</td>';

		$db->free($resql);
	}
	else
	{
		dol_print_error($db);
	}

	print "</table>";

	if (!empty($conf->global->PROJECT_LIMIT_YEAR_RANGE))
	{
		//Add the year filter input
		print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">';
		print '<table width="100%">';
		print '<tr>';
		print '<td>'.$langs->trans("Year").'</td>';
		print '<td style="text-align:right"><input type="text" size="4" class="flat" name="project_year_filter" value="'.$project_year_filter.'"/>';
		print "</tr>\n";
		print '</table></form>';
	}
}

//function para reasignar las fechas de grupos
//segun las tareas
function updatedategroup($fk_projet)
{
	global $db, $taskadd, $objecttaskadd, $taskstatic;
	$aData = array();
	$filterstatic = " AND t.fk_projet = ".$fk_projet;
	$numtask = $taskadd->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,false);
	if ($numtask > 0)
	{
		$nLoop = count($taskadd->lines);
		$lines = $taskadd->lines;
		for ($i = 0; $i < $nLoop; $i++)
		{
			//buscamos sus datos adicionales de la tarea
			$objecttaskadd->fetch('',$lines[$i]->id);
			if ($objecttaskadd->fk_task == $lines[$i]->id)
			{
				if (empty($objecttaskadd->c_grupo))
				{
					//validamos las fechas
					if (empty($aData[$lines[$i]->fk_task_parent]))
					{
						$aData[$lines[$i]->fk_task_parent]['ini'] = $lines[$i]->date_start;
						$aData[$lines[$i]->fk_task_parent]['fin'] = $lines[$i]->date_end;
					}
					else
					{
						if ($aData[$lines[$i]->fk_task_parent]['ini'] > $lines[$i]->date_start)
							$aData[$lines[$i]->fk_task_parent]['ini'] = $lines[$i]->date_start;

						if ($aData[$lines[$i]->fk_task_parent]['fin'] < $lines[$i]->date_end)
							$aData[$lines[$i]->fk_task_parent]['fin'] = $lines[$i]->date_end;

					}
				}
			}
		}

		//actualizamos las fechas de grupos
		$aDataf = array();
		$aDatares = array();
		foreach ((array) $aData AS $j => $data)
		{
			//buscamos la tarea grupo
			$taskadd->fetch($j);
			if ($taskadd->id == $j)
			{
				$aDataf[$taskadd->fk_task_parent] = $taskadd->fk_task_parent;
				//echo '<hr>date '.dol_print_date($data['ini'],'day').' '.dol_print_date($data['fin'],'day');
				$taskadd->date_start = $data['ini'];
				$taskadd->date_end = $data['fin'];
				$aDatares[$taskadd->fk_task_parent][$j]['ini'] = $data['ini'];
				$aDatares[$taskadd->fk_task_parent][$j]['fin'] = $data['fin'];
				$res = $taskadd->update_dategroup($user);
				if ($res <=0)
				{
					//echo '<hr>errrrrr ';exit;
				}
			}
		}
		//procesamos hasta que el array este vacio
		$nLoop = count($aDataf);
		//print_r($aDataf);
		while ($nLoop > 0)
		{
			foreach ($aDataf AS $j => $value)
			{
				$nnLoop = true;
				$jj = $j;
				while ($nnLoop == true)
				{
					//echo '<hr>datos '.$j;
					//print_r($aDatares);
					$dateini = '';
					$datefin = '';
					$aNew = $aDatares[$jj];
					foreach((array) $aNew AS $k => $data)
					{
						//validamos las fechas
						if (empty($dateini)) $dateini = $data['ini'];
						else
						{
							if ($dateini > $data['ini'])
								$dateini = $data['ini'];
						}
						if (empty($datefin)) $datefin = $data['fin'];
						else
						{
							if ($datefin < $data['fin'])
								$datefin = $data['fin'];
						}
					}
					//actualizamos con el resultado
					//cambiamos al padre
					$res = $taskadd->fetch($jj);
					if ($res > 0 && $taskadd->id == $jj)
					{
						$taskadd->date_start = $dateini;
						$taskadd->date_end = $datefin;
						$taskadd->update_dategroup($user);
						$aDatares[$taskadd->fk_task_parent][$jj]['ini'] = $dateini;
						$aDatares[$taskadd->fk_task_parent][$jj]['fin'] = $datefin;
						$jj = $taskadd->fk_task_parent;
					}
					else
						$nnLoop = false;
				}
				unset($aDataf[$j]);
			}
			//echo '<hr>nLoop '.
			$nLoop = count($aDataf);
		}
//		echo '<hr>termina todo';
//		exit;
	}
}

//actualiza advance
//function para reasignar las fechas de grupos
//segun las tareas
function updatetaskadvance($fk_projet)
{
	global $db, $taskadd, $objecttaskadd, $taskstatic,$objecttime,$user;
	$aData = array();
	$filterstatic = " AND t.fk_projet = ".$fk_projet;
	$numtask = $taskadd->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,false);
	if ($numtask > 0)
	{
		$nLoop = count($taskadd->lines);
		$lines = $taskadd->lines;
		for ($i = 0; $i < $nLoop; $i++)
		{
			//buscamos sus datos adicionales de la tarea
			$objecttaskadd->fetch('',$lines[$i]->id);
			if ($objecttaskadd->fk_task == $lines[$i]->id)
			{			//volvemos a sumar los declarados
				$objecttime->getadvance($lines[$i]->id);
				$totaladvance = 0;
				foreach ((array) $objecttime->aArray AS $statutad => $value)
					$totaladvance+=$value;
				//echo '<hr>total adv '.$totaladvance;
				if ($totaladvance != $objecttaskadd->unit_declared)
				{
					//actualizamos
					$objtemp = new Projettaskadd($db);
					$objtemp->fetch($objecttaskadd->id);
					if ($objtemp->id == $objecttaskadd->id)
					{
						$objtemp->unit_declared = $totaladvance;
						$res = $objtemp->update_declared($user);
						//echo '<hr>resultado '.$res.' paraid '.$objecttaskadd->id;
						//$objecttaskadd->unit_declared = $totaladvance;
					}
				}
			}
		}
	}
}

//fin actualiza advance
//actualiza numeracion de order_ref
/**
 * Show task lines with a particular parent
 *
 * @param	string	   	$inc				Line number (start to 0, then increased by recursive call)
 * @param   string		$parent				Id of parent project to show (0 to show all)
 * @param   Task[]		$lines				Array of lines
 * @param   int			$level				Level (start to 0, then increased/decrease by recursive call)
 * @param 	string		$var				Color
 * @param 	int			$showproject		Show project columns
 * @param	int			$taskrole			Array of roles of user for each tasks
 * @param	int			$projectsListId		List of id of project allowed to user (string separated with comma)
 * @param	int			$addordertick		Add a tick to move task
 * @return	void
*/
function monprojectLinecontrat_orderref(&$inc, $parent, &$lines, &$level, $var, $showproject, &$taskrole, $projectsListId='', $addordertick=0,$lVista=1,$lPay=false)
{
	global $user, $bc, $langs, $db;
	global $projectstatic, $taskstatic, $objecttime;
	$lastprojectid=0;

	$projectsArrayId=explode(',',$projectsListId);


  // We declare counter as global because we want to edit them into recursive call
	global $total_projectlinesa_spent,$total_projectlinesa_planned,$total_projectlinesa_spent_if_planned;
	if ($level == 0)
	{
		$total_projectlinesa_spent=0;
		$total_projectlinesa_planned=0;
		$total_projectlinesa_spent_if_planned=0;
	}
	$numlines=count($lines);
	$sumPayment = 0;
	$sumApprove = 0;
	$sumPayable = 0;
	for ($i = 0 ; $i < $numlines ; $i++)
	{
		$lView = true;
		if ($user->societe_id>0)
		{
			if ($lines[$i]->array_options['options_c_view'] == 1)
				$lView = false;
		}
		if ($lView)
		{
			if ($parent == 0) $level = 0;
	  // Process line
	  // print "i:".$i."-".$lines[$i]->fk_project.'<br>';
			$js = 1;
			if ($js)
			{
	  // Show task line.
				$showline=1;
				$showlineingray=0;
	  // If there is filters to use
				if (is_array($taskrole))
				{
		  // If task not legitimate to show, search if a legitimate task exists later in tree
					if (! isset($taskrole[$lines[$i]->id]) && $lines[$i]->id != $lines[$i]->fk_parent)
					{
		  // So search if task has a subtask legitimate to show
						$foundtaskforuserdeeper=0;
						searchTaskInChild($foundtaskforuserdeeper,$lines[$i]->id,$lines,$taskrole);
		  //print '$foundtaskforuserpeeper='.$foundtaskforuserdeeper.'<br>';
						if ($foundtaskforuserdeeper > 0)
						{
							$showlineingray=1;
					// We will show line but in gray
						}
						else
						{
							$showline=0;
						// No reason to show line
						}
					}
				}
				else
				{
		  // Caller did not ask to filter on tasks of a specific user (this probably means he want also tasks of all users, into public project
		  // or into all other projects if user has permission to).
					if (empty($user->rights->projet->all->lire))
					{
		  // User is not allowed on this project and project is not public, so we hide line
						if (! in_array($lines[$i]->fk_project, $projectsArrayId))
						{
			  // Note that having a user assigned to a task into a project user has no permission on, should not be possible
			  // because assignement on task can be done only on contact of project.
			  // If assignement was done and after, was removed from contact of project, then we can hide the line.
							$showline=0;
						}
					}
				}
				if ($lVista == 1)
				{
					if (!$lines[$i]->array_options['options_fk_contrat']>0)
						$showline = 1;
					else
						$showline = 0;
				}
				if ($lVista == 2)
				{
					if ($lines[$i]->array_options['options_fk_contrat']>0)
						$showline = 1;
					else
						$showline = 0;
				}
				if ($lines[$i]->array_options['options_c_grupo']>0)
					$showline = 0;

				if ($showline)
				{
					$lTask = true;
		  // Break on a new project
					if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
					{
						$var = !$var;
						$lastprojectid=$lines[$i]->fk_project;
					}
					if ($lines[$i]->array_options['options_c_grupo'] == 1)
					{
						$lTask = false;
						print '<tr  '.'class="backgroup"'.' id="row-'.$lines[$i]->id.'">'."\n";
					}
					else
						print '<tr  '.$bc[$var].' id="row-'.$lines[$i]->id.'">'."\n";

		  // if ($showproject)
		  // 	{
		  // 	  // Project ref
		  // 	  print "<td>";
		  // 	  if ($showlineingray) print '<i>';
		  // 	  $projectstatic->id=$lines[$i]->fk_project;
		  // 	  $projectstatic->ref=$lines[$i]->projectref;
		  // 	  $projectstatic->public=$lines[$i]->public;
		  // 	  if ($lines[$i]->public || in_array($lines[$i]->fk_project,$projectsArrayId))
		  // 	    print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'" >'.$lines[$i]->ref.'</a>';
		  // 	      //$projectstatic->getNomUrl(1);
		  // 	  else
		  // 	    print 'dddd';
		  // 	      //$projectstatic->getNomUrl(1,'nolink');
		  // 	  if ($showlineingray) print '</i>';
		  // 	  print "</td>";

		  // 	  // Project status
		  // 	  print '<td>';
		  // 	  $projectstatic->statut=$lines[$i]->projectstatus;
		  // 	  print $projectstatic->getLibStatut(2);
		  // 	  print "</td>";
		  // 	}

		  // // Ref of task
		  // print '<td>';
		  // if ($showlineingray)
		  // 	{
		  // 	  print '<i>'.img_object('','projecttask').' '.$lines[$i]->ref.'</i>';
		  // 	}
		  // else
		  // 	{
		  // 	  $taskstatic->id=$lines[$i]->id;
		  // 	  $taskstatic->ref=$lines[$i]->ref;
		  // 	  $taskstatic->label=($taskrole[$lines[$i]->id]?$langs->trans("YourRole").': '.$taskrole[$lines[$i]->id]:'');
		  // 	  print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'&withproject=1" title="'.$langs->trans('ShowTask').'">'.img_object($langs->trans('ShowTask'),'projecttask').' '. $lines[$i]->ref.'</a>';
		  // 	  //print $taskstatic->getNomUrl(1,'withproject');
		  // 	}
		  // print '</td>';

		  // Title of task
					print "<td>";
					if ($showlineingray) print '<i>';
					else print '<a href="'.DOL_URL_ROOT.'/monprojet/task/task.php?id='.$lines[$i]->id.'&withproject=1">';
					for ($k = 0 ; $k < $level ; $k++)
					{
						print "&nbsp; &nbsp; &nbsp;";
					}
					print $lines[$i]->label;
					if ($showlineingray) print '</i>';
					else print '</a>';
					print "</td>\n";

		  // Date start
					print '<td align="center">';
					print dol_print_date($lines[$i]->date_start,'dayhour');
					print '</td>';

		  // Date end
					print '<td align="center">';
					print dol_print_date($lines[$i]->date_end,'dayhour');
					print '</td>';

		  //unit
					print '<td align="center">';
					if (!$lines[$i]->array_options['options_c_grupo'])
						print $lines[$i]->array_options['options_unit'];
					print '&nbsp;';
					print '</td>';

		  // if ($user->rights->monprojet->task->leerm)
		  // 	{
		  // 	  print '<td align="right">';
		  // 	  if (!$lines[$i]->array_options['options_c_grupo'])
		  // 	    print price($lines[$i]->array_options['options_unit_amount']);
		  // 	  else
		  // 	    print '&nbsp;';
		  // 	  print '</td>';
		  // 	}

					$plannedworkloadoutputformat='allhourmin';
					$timespentoutputformat='allhourmin';
					if (! empty($conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT)) $plannedworkloadoutputformat=$conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT;
					if (! empty($conf->global->PROJECT_TIMES_SPENT_FORMAT)) $timespentoutputformat=$conf->global->PROJECT_TIME_SPENT_FORMAT;

		  //RQC CAMBIADO
		  // // Planned Workload (in working hours)
		  // print '<td align="right">';
		  // if ($lines[$i]->planned_workload != '')
		  // 	{
		  // 	  print $lines[$i]->array_options['options_unit_program'];
		  // 	}
		  // print '</td>';

		  // // EJECUCION REPORTADA
		  // print '<td align="right">';
		  // print $lines[$i]->array_options['options_unit_declared'];
		  // print '</td>';

		  //obtenemos la suma de la tarea
		  //	      $objecttime->getadvance($lines[$i]->id,'');
		  //
		  // * 1= por pagar
		  // * 2= aprobado
		  // * 3= Pagado
		  //

		  // //quant payment total
		  // print '<td align="right">';
		  // print price(price2num($objecttime->aArray[3],'MT'));
		  // print '</td>';

		  // //quant present
		  // print '<td align="right">';
		  // print price(price2num($objecttime->aArray[2],'MT'));
		  // print '</td>';

		  // //quant previus
		  // print '<td align="right">';
		  // print price(price2num($objecttime->aArray[1],'MT'));
		  // print '</td>';

		  // //amount payment total
		  // print '<td align="right">';
		  // print price(price2num($objecttime->aArray[3]*$lines[$i]->array_options['options_unit_amount'],'MT'));
		  // $sumPayment+=price2num($objecttime->aArray[3]*$lines[$i]->array_options['options_unit_amount'],'MT');
		  // print '</td>';

		  // //amount present
		  // print '<td align="right">';
		  // print price(price2num($objecttime->aArray[2]*$lines[$i]->array_options['options_unit_amount'],'MT'));
		  // $sumApprove+=price2num($objecttime->aArray[2]*$lines[$i]->array_options['options_unit_amount'],'MT');
		  // print '</td>';

		  // //amount previus
		  // print '<td align="right">';
		  // print price(price2num($objecttime->aArray[1]*$lines[$i]->array_options['options_unit_amount'],'MT'));
		  // $sumPayable+=price2num($objecttime->aArray[1]*$lines[$i]->array_options['options_unit_amount'],'MT');
		  // print '</td>';

					print '<td align="right">';
		  //para la seleccion del pago
					if ($lVista == 1)
						print '<input type="checkbox" name="selcon['.$lines[$i]->id.']" '.($_SESSION['aSelectcont'][$lines[$i]->id]?'checked':'').'>';
					print '&nbsp;';
					print '</td>';

		  // // Tick to drag and drop
		  // if ($addordertick)
		  // 	{
		  // 	  print '<td align="center" class="tdlineupdown hideonsmartphone">&nbsp;</td>';
		  // 	}

					print "</tr>\n";

					if (! $showlineingray) $inc++;

		  //$level++;
				}
			}
			else
			{
	  //$level--;
			}
		}
	}

	if ($sumPayment > 0 || $sumApprove > 0 || $sumPayable > 0)
	{
		print '<tr class="liste_total nodrag nodrop">';
		print '<td class="liste_total">'.$langs->trans("Total").'</td>';
		if ($showproject) print '<td></td><td></td>';
		print '<td></td>';
		print '<td></td>';
		print '<td></td>';
		if ($user->rights->monprojet->task->leerm)
			print '<td align="right"></td>';

		print '<td></td>';
		print '<td></td>';

		print '<td align="right"></td>';
		print '<td align="right"></td>';
		print '<td align="right"></td>';

		print '<td align="right" class="nowrap liste_total">';
		print price(price2num($sumPayment,'MT'));
		print '</td>';
		print '<td align="right" class="nowrap liste_total">';
		print price(price2num($sumApprove,'MT'));
		print '</td>';
		print '<td align="right" class="nowrap liste_total">';
		print price(price2num($sumPayable,'MT'));
		print '</td>';
	  //if ($addordertick) print '<td class="hideonsmartphone"></td>';
		print '</tr>';
	}
	return array($sumPayment,$sumApprove,$sumPayable);
}

?>