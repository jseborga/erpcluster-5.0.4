<?php

//MODIFICADO
$data = explode(';',$aTask);
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskaddext.class.php';
$objecttaskadd = new Projettaskaddext($db);

$now = dol_now();
if ($data[0])
{
	$label = $data[0];
	$error=0;
	$date_start = $object->date_start;
	$date_end = $object->date_end;

	$ref = '';
	//le damos un nuevo numero de referencia
	$obj = empty($conf->global->PROJECT_TASK_ADDON)?'mod_task_simple':$conf->global->PROJECT_TASK_ADDON;
	if (! empty($conf->global->PROJECT_TASK_ADDON) && is_readable(DOL_DOCUMENT_ROOT ."/core/modules/project/task/".$conf->global->PROJECT_TASK_ADDON.".php"))
	{
		require_once DOL_DOCUMENT_ROOT ."/core/modules/project/task/".$conf->global->PROJECT_TASK_ADDON.'.php';
		$modTask = new $obj;
		$ref = $modTask->getNextValue($soc,$object);
	}

	if (! $error)
	{
		$projectid = $object->id;
		if (empty($task_parent)) $task_parent = 0;

			$cunits = fetch_unit('',$data[1]);
			if (STRTOUPPER($cunits->code) == STRTOUPPER($data[1]))
			{
				$fk_unit = $cunits->rowid;
			}
			else
			{
				$error++;
				setEventMessages($langs->trans('Error, no existe la unidad de medida').' <b>'.$data[1].'</b>, '.$langs->trans('revise'),null,'errors');
			}

		$task = new Task($db);
		$extrafields_task = new ExtraFields($db);
		$extralabels_task=$extrafields_task->fetch_name_optionals_label($task->table_element);

		$task->fk_project = $projectid;
		//$task->ref = GETPOST('ref','alpha');
		$task->ref = $ref;
		$task->label = $label;
		$task->description = $description;
		$task->planned_workload = $planned_workload;
		$task->fk_task_parent = $task_parent;
		$task->date_c = $now;
		$task->date_start = $date_start;
		$task->date_end = $date_end;
		$task->progress = $progress+0;
		// Fill array 'array_options' with data from add form
		$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);

		$taskid = $task->create($user);
		if ($taskid<=0) $error++;
		if (!$error)
		{
			$objecttaskadd->fk_task = $taskid;
			$objecttaskadd->c_grupo = 0;
			$objecttaskadd->c_view = 0;
			$objecttaskadd->unit_program = 1;
			$objecttaskadd->unit_declared = 0;
			$objecttaskadd->fk_unit = $fk_unit;
			$objecttaskadd->fk_type = 0;
			$objecttaskadd->unit_price = 0;
			$objecttaskadd->unit_amount = 0;
			$objecttaskadd->fk_user_create = $user->id;
			$objecttaskadd->fk_user_mod = $user->id;
			$objecttaskadd->date_create = $now;
			$objecttaskadd->tms = $now;
			$objecttaskadd->statut = 1;
			$res = $objecttaskadd->create($user);
			if ($res<=0) $error++;

		}
		if (!$error)
		{
			$result = $task->add_contact($user->id, 'TASKEXECUTIVE', 'internal');
		}
		else
		{
			setEventMessages($task->error,$task->errors,'errors');
		}
	}
}
?>