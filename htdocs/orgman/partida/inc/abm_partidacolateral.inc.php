<?php

$hookmanager->initHooks(array('partidacolateral'));



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$objPartidacolateral,$action);    // Note that $action and $objPartidacolateral may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/partida/card.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($idr > 0) $ret = $objPartidacolateral->fetch($id,$ref);
		$action='';
	}
	$now = dol_now();
	// Action to add record
	if ($action == 'addcolateral')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/partida/card.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;
		/* object_prop_getpost_prop */
		//buscamos si existe duplicado
		$res = $objPartidacolateral->fetchAll('','',0,0,array('code_partida'=>$object->code,'code_colateral'=>GETPOST('code_colateral')),'AND');
		if ($res>0)
		{
			$error++;
			setEventMessages($langs->trans('There is the record'),null,'errors');
		}
		$objPartidacolateral->code_partida=$object->code;
		$objPartidacolateral->code_colateral=GETPOST('code_colateral','alpha');
		$objPartidacolateral->label=GETPOST('label','alpha');
		$objPartidacolateral->percent=GETPOST('percent','alpha');
		$objPartidacolateral->fk_user_create=$user->id;
		$objPartidacolateral->fk_user_mod=$user->id;
		$objPartidacolateral->active=GETPOST('active','int');
		$objPartidacolateral->datec=$now;
		$objPartidacolateral->datem=$now;
		$objPartidacolateral->tms=$now;


		if (empty($objPartidacolateral->code_colateral))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldcode_colateral")), null, 'errors');
		}else
		{
			//buscamos para validar
			$filter = " AND t.code = '".$objPartidacolateral->code_colateral."'";
			$res = $objecttmp->fetchAll('','',0,0,array(1=>1),'AND',$filter);
			if ($res <=0)
			{
				$error++;
				setEventMessages($langs->trans("Doesnotexistpartidacolateral"), null, 'errors');
			}
		}
		if (empty($objPartidacolateral->percent))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldpercent")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objPartidacolateral->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/partida/card.php?id='.$id.'&action=createcolateral',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objPartidacolateral->errors)) setEventMessages(null, $objPartidacolateral->errors, 'errors');
				else  setEventMessages($objPartidacolateral->error, null, 'errors');
				$action='createcolateral';
			}
		}
		else
		{
			$action='createcolateral';
		}
	}

	// Action to update record
	if ($action == 'updatecolateral')
	{
		$error=0;
		$objPartidacolateral->code_colateral=GETPOST('code_colateral','alpha');
		$objPartidacolateral->label=GETPOST('label','alpha');
		$objPartidacolateral->percent=GETPOST('percent','alpha');
		$objPartidacolateral->fk_user_mod=$user->id;
		$objPartidacolateral->active=GETPOST('active','int');
		$objPartidacolateral->datem=$now;
		$objPartidacolateral->tms=$now;

		if (empty($objPartidacolateral->code_colateral))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldcode_colateral")), null, 'errors');
		}
		if (empty($objPartidacolateral->percent))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldpercent")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objPartidacolateral->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objPartidacolateral->errors)) setEventMessages(null, $objPartidacolateral->errors, 'errors');
				else setEventMessages($objPartidacolateral->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete_colateral')
	{
		$result=$objPartidacolateral->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/orgman/partida/card.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($objPartidacolateral->errors)) setEventMessages(null, $objPartidacolateral->errors, 'errors');
			else setEventMessages($objPartidacolateral->error, null, 'errors');
		}
	}
}


?>