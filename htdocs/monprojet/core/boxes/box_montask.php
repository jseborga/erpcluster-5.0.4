<?php
/* Copyright (C) 2012-2014 Charles-François BENKE <charles.fr@benke.fr>
 * Copyright (C) 2015      Frederic France        <frederic.france@free.fr>
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
 *  \file       htdocs/core/boxes/box_task.php
 *  \ingroup    Projet
 *  \brief      Module to Task activity of the current year
 */

include_once(DOL_DOCUMENT_ROOT."/core/boxes/modules_boxes.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
//projectos
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';

if ($conf->budget->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/typeitem.class.php';
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

/**
 * Class to manage the box to show last task
 */
class box_montask extends ModeleBoxes
{
	var $boxcode="projet";
	var $boximg="object_projecttask";
	var $boxlabel;
	//var $depends = array("projet");
	var $db;
	var $param;

	var $info_box_head = array();
	var $info_box_contents = array();

	/**
	 *  Constructor
	 *
	 *  @param  DoliDB  $db         Database handler
	 *  @param  string  $param      More parameters
	 */
	function __construct($db,$param='')
	{
		global $langs;
		$langs->load("boxes");
		$langs->load("projects");
		$langs->load("monprojet@monprojet");
		$this->boxlabel="MyTasks";
		$this->db = $db;
	}

	/**
	 *  Load data for box to show them later
	 *
	 *  @param  int     $max        Maximum number of records to load
	 *  @return void
	 */
	function loadBox($max=5)
	{
		global $conf, $user, $langs, $db;
		if (!isset($conf->global->MONPROJET_USE_WITHPROJECT))
			$withprojet = $conf->global->MONPROJET_USE_WITHPROJECT+0;
		else
			$withprojet = 0;
		$form = new Form($this->db);
		$projectstatic = new Project($this->db);
		$taskstatic = new Taskext($this->db);
		$projettaskadd = new Projettaskadd($this->db);
		if ($conf->budget->enabled)
		{
			$items = new Items($this->db);
			$typeitem = new Typeitem($this->db);
		}
		$showproject = 1;
		// Security check
		$socid=0;
		if ($user->societe_id > 0) $socid = $user->societe_id;
		if ($user->rights->monprojet->leer)
		{

			$this->max=$max;
			$mine = 1;
		// Get list of project id allowed to user (in a string list separated by coma)
			$projectsListId = $projectstatic->getProjectsAuthorizedForUser($user,$mine,1,$socid);


			$lines=$taskstatic->getTasksArray(0, 0, $projectstatic->id, $socid, 0, $search_project, $search_status, $morewherefilter, $search_project_user, $search_task_user);
		// We load also tasks limited to a particular user
			$tasksrole=($mine ? $taskstatic->getUserRolesForProjectsOrTasks(0,$user,$projectstatic->id,0) : '');

		// If the user can view users
			if ($user->rights->user->user->lire)
			{
				$moreforfilter.='<div class="divsearchfield">';
				$moreforfilter.=$langs->trans('ProjectsWithThisUserAsContact'). ' ';
				$moreforfilter.=$form->select_dolusers($search_project_user, 'search_project_user', 1, '', 0, '', '', 0, 0, 0, '', 0, '', 'maxwidth300');
				$moreforfilter.='</div>';
			}
			if ($user->rights->user->user->lire)
			{
				$moreforfilter.='<div class="divsearchfield">';
				$moreforfilter.=$langs->trans('TasksWithThisUserAsContact'). ' ';
				$moreforfilter.=$form->select_dolusers($search_task_user, 'search_task_user', 1, '', 0, '', '', 0, 0, 0, '', 0, '', 'maxwidth300');
				$moreforfilter.='</div>';
			}

		// Show all lines in taskarray (recursive function to go down on tree)
			$j=0; $level=0;
			$aTaskmine = array();
			$aTaskmine=monprojectLinemine($j, 0, $lines,
				$level, true, $showproject,
				$tasksrole, $projectsListId, 0,
				1,$aTaskmine,$projectstatic,
				$taskstatic,$items,$typeitem);
			$i = 0;
			$textHead = $langs->trans("Mytasks");
			$this->info_box_head = array('text' => $textHead, 'limit'=> dol_strlen($textHead));
			foreach((array) $aTaskmine AS $j => $line)
			{
			//busco en projettaskadd
				$res = $projettaskadd->fetch ('',$line['id']);
				if ($res>0 && $projettaskadd->fk_task == $line['id'] &&
					$projettaskadd->c_grupo != 1)
				{
			// $this->info_box_contents[$i][0] = array(
			// 					    'td' => 'align="left" width="16"',
			// 					    'logo' => 'object_project',
			// 					    'tooltip' => $tooltip,
			// 					    'url' => DOL_URL_ROOT."/monprojet/tasks..php?id=".$line['id'],
			// );
					$tooltip = $langs->trans('Project') . ': ' . $line['projet'];

					$this->info_box_contents[$i][0] = array(
						'td' => 'align="left" width="16"',
						'text' => $line['projet'],
						'tooltip' => $tooltip,
						'url' => DOL_URL_ROOT."/projet/card.php?id=".$line['projetid'],
						);

					$tooltip = $langs->trans('Task') . ': ' . $line['ref'];

					$this->info_box_contents[$i][1] = array(
						'td' => 'align="left" width="16"',
						'text' => $line['label'],
						'tooltip' => $tooltip,
						'url' => DOL_URL_ROOT."/monprojet/task/task.php?id=".$line['id'].'&withproject='.($whitprojet?0:1),
						);
					$this->info_box_contents[$i][2] = array(
						'td' => 'align="left" width="16"',
						'text' => $line['date_start'],
						'tooltip' => $tooltip,
						);
					$this->info_box_contents[$i][3] = array(
						'td' => 'align="left" width="16"',
						'text' => $line['date_end'],
						'tooltip' => $tooltip,
						);
					$i++;
				}
			}
		}
	}


	/**
	 *	Method to show box
	 *
	 *	@param	array	$head       Array with properties of box title
	 *	@param  array	$contents   Array with properties of box lines
	 *	@return	void
	 */
	function showBoxs($head = null, $contents = null)
	{
		parent::showBox($this->info_box_head, $this->info_box_contents);
	}
}
