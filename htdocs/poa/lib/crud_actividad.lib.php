<?php

// Addmon
if ($action == 'addmon' && $user->rights->poa->act->addm)
 {
	$error = 0;
	if ($object->fetch($id)>0)
	{
		$date_tracking = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
		if (!$user->admin) $date_tracking = dol_now();
		$objectw->fk_activity = $id;
		$objectw->followup = GETPOST('followup');
		$objectw->followto = GETPOST('followto');
		$objectw->date_tracking = $date_tracking;
		$objectw->date_create = dol_now();
		$objectw->fk_user_create  = $user->id;
		$objectw->tms     = date('YmdHis');
		$objectw->statut  = 1;
		if (empty($objectw->followup))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorfollowupisrequired").'</div>';
		}
		if (empty($error))
		{
			$res = $objectw->create($user);
			if ($res > 0)
			{
				header("Location: ".$_SERVER['PHP_SELF'].'?$ida='.$ida.'&id='.$id.'&dol_hide_leftmenu=1');
				exit;
			}
			$action = '';
			$mesg='<div class="error">'.$objectw->error.'</div>';
		}
		else
			$action="";   // Force retour sur page creation
	}
}

// updatemon
if ($action == 'updatemon' && $user->rights->poa->act->modm)
{
	$error = 0;
	if ($object->fetch($id)>0)
	{
		if ($objectw->fetch($idr)>0)
		{
			$date_tracking = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
			$objectw->followup = GETPOST('followup');
			$objectw->followto = GETPOST('followto');
			if ($user->admin) $objectw->date_tracking = $date_tracking;
			$objectw->tms     = date('YmdHis');
			$objectw->statut  = 1;
			if (empty($objectw->followup))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errorfollowupisrequired").'</div>';
			}
			if (empty($error))
			{
				$res = $objectw->update($user);
				if ($res > 0)
				{
					header("Location: ".$_SERVER['PHP_SELF'].'?ida='.$ida.'&id='.$id.'&dol_hide_leftmenu=1');
					exit;
				}
				$action = '';
				$mesg='<div class="error">'.$objectw->error.'</div>';
			}
			else
				$action="";   // Force retour sur page creation
		}
	}
}

?>