<?php
/* Copyright (C) 2010-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/projet/ganttchart.inc.php
 *	\ingroup    projet
 *	\brief      Gantt diagram of a project
 */

?>

<?php
$level=0;
echo '<hr>tcnums '.$tnums = count($tasks);

for ($tcursor=0; $tcursor < $tnums; $tcursor++) {
	$t = $tasks[$tcursor];
	if ($t["task_parent"] == 0) {
	  constructGanttLine($tasks,$t,$project_dependencies,$level,$project_id);
	  findChildGanttLine($tasks,$t["task_id"],$project_dependencies,$level+1);
	}
}
?>


<?php
/**
 * Add a gant chart line
 *
 * @param 	string	$tarr					tarr
 * @param	Task	$task					Task object
 * @param 	Project	$project_dependencies	Project object
 * @param 	int		$level					Level
 * @param 	int		$project_id				Id of project
 * @return	void
 */
function constructGanttLine($tarr,$task,$project_dependencies,$level=0,$project_id=null)
{
echo '<br>adentro constr '.    $start_date = $task["task_start_date"];
    $end_date = $task["task_end_date"];
    $start_dateb = $task["task_start_dateb"];
    $end_dateb = $task["task_end_dateb"];
    if (!$end_date) $end_date = $start_date;
    if (!$end_dateb) $end_dateb = $start_dateb;
    $start_date = dol_print_date($start_date,"%m/%d/%Y");
    $end_date = dol_print_date($end_date,"%m/%d/%Y");
    //base
    $start_dateb = dol_print_date($start_dateb,"%m/%d/%Y");
    $end_dateb = dol_print_date($end_dateb,"%m/%d/%Y");
    $depend = $task['task_depend'];
    $group  = $task['task_group'];
    // Resources
    $resources = $task["task_resources"];
    // Define depend (ex: "", "4,13", ...)
    $depend = '';
    //$depend = "\"";
    $count = 0;
    foreach ($project_dependencies as $value) {
        // Not yet used project_dependencies = array(array(0=>idtask,1=>idtasktofinishfisrt))
        if ($value[0] == $task['task_id']) {
            $depend.=($count>0?",":"").$value[1];
            $count ++;
        }
    }
    //$depend .= "\"";
    // Define parent
    if ($project_id && $level < 0)
      $parent = 'p'.$project_id;
    else
      $parent = $task["task_parent"];
    //$caption = $task["task_caption"];
    // Define percent
    $percent = $task['task_percent_complete']?$task['task_percent_complete']:0;
    // Link
    $link=DOL_URL_ROOT.'/projet/tasks/task.php?withproject=1&id='.$task["task_id"];
    // Name
    $name=$task['task_name'];
    for($i=0; $i < $level; $i++) {
      $name=' &nbsp; &nbsp; '.$name;
    }

 echo '<hr>'.    $s = $task['task_id'].",'".dol_escape_js($name)."','".$start_date."', '".$end_date."', '".$task['task_color']."', '".$link."', ".$task['task_milestone'].", '".$resources."', ".$percent.", ".($task["task_is_group"]>0?1:0).", ".$parent.", 1 ".($depend?",'".$depend."'":'')." ));";

    // Add line to gantt
    // $s = "// Add taks id=".$task["task_id"]." level = ".$level."\n";
    // $s = "g.AddTaskItem(new JSGantt.TaskItem(".$task['task_id'].",'".dol_escape_js($name)."','".$start_date."', '".$end_date."', '".$task['task_color']."', '".$link."', ".$task['task_milestone'].", '".$resources."', ".$percent.", ".($task["task_is_group"]>0?1:0).", '".$parent."', 1, '".($depend?$depend:"")."', '".$caption."' ));";
    // echo $s."\n";
}

/**
 * Find child Gantt line
 *
 * @param 	string	$tarr					tarr
 * @param	int		$parent					Parent
 * @param 	Project	$project_dependencies	Project object
 * @param 	int		$level					Level
 * @return	void
 */
function findChildGanttLine($tarr,$parent,$project_dependencies,$level)
{
    $n=count($tarr);
    for ($x=0; $x < $n; $x++)
    {
        if($tarr[$x]["task_parent"] == $parent && $tarr[$x]["task_parent"] != $tarr[$x]["task_id"])
        {
	  constructGanttLine($tarr,$tarr[$x],$project_dependencies,$level,null);
	  findChildGanttLine($tarr,$tarr[$x]["task_id"],$project_dependencies,$level+1);
        }
    }
}

?>