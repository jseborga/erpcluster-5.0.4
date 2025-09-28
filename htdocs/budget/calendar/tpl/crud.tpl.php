<?php


	// Action to add record
if ($action == 'addconf')
{
	$aWorkingday = GETPOST('working_day');
	$hourArray = array();
	$minArray = array();
	$chour = '';
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/calendar/list.php',1);
		header("Location: ".$urltogo);
		exit;
	}
	$error=0;
	foreach ($_POST AS $key => $value)
	{
		$aKey = explode('_',$key);
		if ($aKey[0] == 'hour')
			$hourArray[$aKey[1]] = $value;
		if ($aKey[0] == 'min')
			$minArray[$aKey[1]] = $value;
	}
	foreach ($hourArray AS $j => $value)
	{
		if ($chour) $chour.='|';
		$chour.= $j.';'.$value.':'.$minArray[$j];
	}
	for ($a =0; $a <=6;$a++)
	{
		if ($aWorkingday[$a] == 1)
		{
			if (isset($working_day)) $working_day.='|';
			$working_day.=$a;
		}
		else
		{
			if (isset($nonwork_day)) $nonwork_day.='|';
			$nonwork_day.=$a;
		}

	}
	/* object_prop_getpost_prop */

	$objectconf->fk_calendar=GETPOST('fk_calendar','int');
	$objectconf->working_day=$working_day;
	$objectconf->working_day_hours=$chour;
	$objectconf->nonwork_day=$nonwork_day;
	$objectconf->hours_day=GETPOST('hours_day','int');
	$objectconf->hours_week=GETPOST('hours_week','int');
	$objectconf->days_month=GETPOST('days_month','int');
	$objectconf->fk_user_create=$user->id;
	$objectconf->fk_user_mod=$user->id;
	$objectconf->datec = dol_now();
	$objectconf->datem = dol_now();
	$objectconf->tms = dol_now();
	$objectconf->status=1;

	if (! $error)
	{
		$result=$objectconf->create($user);
		if ($result > 0)
		{
				// Creation OK
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/calendar/card.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		{
				// Creation KO
			if (! empty($objectconf->errors)) setEventMessages(null, $objectconf->errors, 'errors');
			else  setEventMessages($objectconf->error, null, 'errors');
			$action='createconf';
		}
	}
	else
	{
		$action='createconf';
	}
}

	// Cancel
if ($action == 'updateconf' && GETPOST('cancel')) $action='view';

	// Action to update record
if ($action == 'updateconf' && ! GETPOST('cancel'))
{
	$objectconf->fetch(GETPOST('idr'));
	$error=0;
	$aWorkingday = GETPOST('working_day');
	$hourArray = array();
	$minArray = array();
	$chour = '';
	foreach ($_POST AS $key => $value)
	{
		$aKey = explode('_',$key);
		if ($aKey[0] == 'hour')
			$hourArray[$aKey[1]] = $value;
		if ($aKey[0] == 'min')
			$minArray[$aKey[1]] = $value;
	}
	foreach ($hourArray AS $j => $value)
	{
		if ($chour) $chour.='|';
		$chour.= $j.';'.$value.':'.$minArray[$j];
	}
	for ($a =0; $a <=6;$a++)
	{
		if ($aWorkingday[$a] == 1)
		{
			if (isset($working_day)) $working_day.='|';
			$working_day.=$a;
		}
		else
		{
			if (isset($nonwork_day)) $nonwork_day.='|';
			$nonwork_day.=$a;
		}

	}

	$objectconf->working_day=$working_day;
	$objectconf->working_day_hours=$chour;
	$objectconf->nonwork_day=$nonwork_day;
	$objectconf->hours_day=GETPOST('hours_day','int');
	$objectconf->hours_week=GETPOST('hours_week','int');
	$objectconf->days_month=GETPOST('days_month','int');
	$objectconf->fk_user_mod=$user->id;
	$objectconf->datem = dol_now();
	$objectconf->tms = dol_now();

	if (empty($objectconf->working_day))
	{
		$error++;
		setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldworkingday")), null, 'errors');
	}
	if (! $error)
	{
		$result=$objectconf->update($user);
		if ($result > 0)
		{
			$action='view';
		}
		else
		{
				// Creation KO
			if (! empty($objectconf->errors)) setEventMessages(null, $objectconf->errors, 'errors');
			else setEventMessages($objectconf->error, null, 'errors');
			$action='editconf';
		}
	}
	else
	{
		$action='editconf';
	}
}
	// Action to delete
if ($action == 'confirm_deleteconf')
{
	$result=$objectconf->delete($user);
	if ($result > 0)
	{
			// Delete OK
		setEventMessages("RecordDeleted", null, 'mesgs');
		header("Location: ".dol_buildpath('/budget/list.php',1));
		exit;
	}
	else
	{
		if (! empty($objectconf->errors)) setEventMessages(null, $objectconf->errors, 'errors');
		else setEventMessages($objectconf->error, null, 'errors');
	}
}

?>