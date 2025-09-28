<?php
//proceso para actualizar el order_ref
	//primero actualizamos el order_ref
if ($user->admin)
{
	//condicion principal definir variable $fk_projet

	$aTasknumref = unserialize($_SESSION['aTasknumref'][$object->id]);


	//ordenamos y actualizamos
	$aRef = array();
	$aNumberref = array();
	$aRefnumber = array();
	foreach((array) $aTasknumref AS $i => $data)
	{
		//verificamos el orden donde debe estar la tarea
		list($aRef,$aNumberref,$aRefnumber) = get_orderlastnew($i,$fk_projet,$data,$aRef,$aNumberref,$aRefnumber);
	}
	//una vez que se tenga el aRefnumber
	//actualizar el order_ref
	$error = 0;
	$db->begin();
	foreach ((array) $aNumberref AS $i => $value)
	{
		$objecttaskadd->fetch('',$i);
		if ($objecttaskadd->fk_task == $i)
		{
			//echo '<br>'.$i.' '.$value;
			$objecttaskadd->order_ref = $value;
			$res = $objecttaskadd->update_orderref();
			if ($res <=0) $error++;
		}
		else
		{
			$error++;
			echo '<br>no encuentra '.$i;
		}
	}
	//ordenamos las tareas por el order_ref
	if (empty($error))
	{
		//echo '<hr>antes de actualizar el order '.$error;
		$taskadd->get_ordertask($fk_projet);
		$taskaddnew = new Taskext($db);
		//echo '<br>cuentalines '.count($taskadd->lines).' del id '.$projectstatic->id;
		if (count($taskadd->lines)>0)
		{
			$j = 1;
			foreach($taskadd->lines AS $i => $data)
			{
				$fk = $data->id;
				$res = $taskaddnew->fetch($fk);
				if ($res >0 && $taskaddnew->id == $fk)
				{
					$taskaddnew->rang = $j;
					$resup = $taskaddnew->update_rang($user);
					if ($resup <= 0) $error++;
					$j++;
				}
				else
					$error++;
			}
		}
	}
	if (empty($error))
	{
		$db->commit();
	}
	else
	{
		setEventMessage($langs->trans("Errorupdate",$langs->transnoentitiesnoconv("Task")),'errors');
		$db->rollback();
	}
}
?>