<?php
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

class FormOtherAdd extends FormOther
{

	/**
	 *  Return list of project and tasks presup
	 *
	 *  @param  int     $selectedtask           Pre-selected task
	 *  @param  int     $projectid              Project id
	 *  @param  string  $htmlname               Name of html select
	 *  @param  int     $modeproject            1 to restrict on projects owned by user
	 *  @param  int     $modetask               1 to restrict on tasks associated to user
	 *  @param  int     $mode                   0=Return list of tasks and their projects, 1=Return projects and tasks if exists
	 *  @param  int     $useempty               0=Allow empty values
	 *  @param  int     $disablechildoftaskid   1=Disable task that are child of the provided task id
	 *  @return void
	 */
	function selectProjectTasks_budget($selectedtask='', $projectid=0, $htmlname='task_parent', $modeproject=0, $modetask=0, $mode=0, $useempty=0, $disablechildoftaskid=0,$filter='')
	{
		global $user, $langs;

		//require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
		require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
		//print $modeproject.'-'.$modetask;
		$task=new Taskext($this->db);
		$linetask=$task->getTasksArray($modetask?$user:0, $modeproject?$user:0, $projectid, 0, $mode,'',-1,'',0,0,25,1,0,1);
		if ($linetask)
		{
			print '<select class="flat" name="'.$htmlname.'">';
			if ($useempty) print '<option value="0">&nbsp;</option>';
			$j=0;
			$level=0;
			$this->_pLineTaskSelect($j, 0, $linetask, $level, $selectedtask, $projectid, $disablechildoftaskid);
			print '</select>';
		}
		else
		{
			print '<div class="warning">'.$langs->trans("NoProject").'</div>';
		}
	}

	/**
	 *	Return list of project and tasks
	 *
	 *	@param  int		$selectedtask   		Pre-selected task
	 *  @param  int		$projectid				Project id
	 * 	@param  string	$htmlname    			Name of html select
	 * 	@param	int		$modeproject			1 to restrict on projects owned by user
	 * 	@param	int		$modetask				1 to restrict on tasks associated to user
	 * 	@param	int		$mode					0=Return list of tasks and their projects, 1=Return projects and tasks if exists
	 *  @param  int		$useempty       		0=Allow empty values
	 *  @param	int		$disablechildoftaskid	1=Disable task that are child of the provided task id
	 *  @return	void
	 */
	function selectProjectTasks_($selectedtask='', $projectid=0, $htmlname='task_parent', $modeproject=0, $modetask=0, $mode=0, $useempty=0, $disablechildoftaskid=0,$filter='')
	{
		global $user, $langs;

		//require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
		require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
		//print $modeproject.'-'.$modetask;
		$task=new Taskext($this->db);
		$linetask=$task->getTasksArray($modetask?$user:0, $modeproject?$user:0, $projectid, 0, $mode,'',-1,'',0,0,25,1,0,1);
		if ($linetask)
		{
			print '<select class="flat" name="'.$htmlname.'">';
			if ($useempty) print '<option value="0">&nbsp;</option>';
			$j=0;
			$level=0;
			echo 'envia ';
			$this->__pLineTaskSelect($j, 0, $linetask, $level, $selectedtask, $projectid, $disablechildoftaskid);
			print '</select>';
		}
		else
		{
			print '<div class="warning">'.$langs->trans("NoProject").'</div>';
		}
	}


	/**
	 * Write lines of a project (all lines of a project if parent = 0)
	 *
	 * @param 	int		$inc					Cursor counter
	 * @param 	int		$parent					Id of parent task we want to see
	 * @param 	array	$lines					Array of task lines
	 * @param 	int		$level					Level
	 * @param 	int		$selectedtask			Id selected task
	 * @param 	int		$selectedproject		Id selected project
	 * @param	int		$disablechildoftaskid	1=Disable task that are child of the provided task id
	 * @return	void
	 */
	private function __pLineTaskSelect(&$inc, $parent, $lines, $level=0, $selectedtask=0, $selectedproject=0, $disablechildoftaskid=0)
	{
		global $langs, $user, $conf;

		require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
		//print $modeproject.'-'.$modetask;
		$taskb=new Taskext($this->db);

		$lastprojectid=0;
		$numlines=count($lines);
		for ($i = 0 ; $i < $numlines ; $i++)
		{
			if ($lines[$i]->fk_parent == $parent)
			{
				$var = !$var;

				//var_dump($selectedproject."--".$selectedtask."--".$lines[$i]->fk_project."_".$lines[$i]->id);
				// $lines[$i]->id may be empty if project has no lines

				// Break on a new project
				if ($parent == 0)	// We are on a task at first level
				{
					if ($lines[$i]->fk_project != $lastprojectid)	// Break found on project
					{
						if ($i > 0) print '<option value="0" disabled>------x----</option>';
						print '<option value="'.$lines[$i]->fk_project.'_0"';
						if ($selectedproject == $lines[$i]->fk_project) print ' selected';
						print '>';	// Project -> Task
						print $langs->trans("Project").' '.$lines[$i]->projectref;
						if (empty($lines[$i]->public))
						{
							print ' ('.$langs->trans("Visibility").': '.$langs->trans("PrivateProject").')';
						}
						else
						{
							print ' ('.$langs->trans("Visibility").': '.$langs->trans("SharedProject").')';
						}
						//print '-'.$parent.'-'.$lines[$i]->fk_project.'-'.$lastprojectid;
						print "</option>\n";

						$lastprojectid=$lines[$i]->fk_project;
						$inc++;
					}
				}

				$newdisablechildoftaskid=$disablechildoftaskid;

				// Print task
				if (isset($lines[$i]->id))		
				// We use isset because $lines[$i]->id may be null if project has no task and are on root project (tasks may be caught by a left join). We enter here only if '0' or >0
				{
					  // Check if we must disable entry
					$disabled=0;
					if ($disablechildoftaskid && (($lines[$i]->id == $disablechildoftaskid || $lines[$i]->fk_parent == $disablechildoftaskid)))
					{
						$disabled++;
						if ($lines[$i]->fk_parent == $disablechildoftaskid) $newdisablechildoftaskid=$lines[$i]->id;	
			  			// If task is child of a disabled parent, we will propagate id to disable next child too
					}
					if ($lines[$i]->array_options['options_c_grupo'] == 1)
					{
						$taskb->fetch($lines[$i]->id);
						$resus = verifcontacttask($user,$taskb,$ret = 'res',0);
						if ($resus || $user->admin || $user->id == $object->fk_user_creat)
						{
							print '<option value="'.$lines[$i]->fk_project.'_'.$lines[$i]->id.'"';

							if (($lines[$i]->id == $selectedtask) || ($lines[$i]->fk_project.'_'.$lines[$i]->id == $selectedtask)) print ' selected';
							if ($disabled) print ' disabled';

							print '>';

							print $langs->trans("Project").' '.$lines[$i]->projectref;

							if (empty($lines[$i]->public))
							{
								print ' ('.$langs->trans("Visibility").':xx '.$langs->trans("PrivateProject").')';
							}
							else
							{
								print ' ('.$langs->trans("Visibility").':yy '.$langs->trans("SharedProject").')';
							}
							if ($lines[$i]->id) print ' > ';
							for ($k = 0 ; $k < $level ; $k++)
							{
								print "&nbsp;&nbsp;&nbsp;";
							}

							print $lines[$i]->label."</option>\n";
						}
					}
					$inc++;
				}

				$level++;
				if ($lines[$i]->id) $this->__pLineTaskSelect($inc, $lines[$i]->id, $lines, $level, $selectedtask, $selectedproject, $newdisablechildoftaskid);
				$level--;
			}
		}
	}

}
?>