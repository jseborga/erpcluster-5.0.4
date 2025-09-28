<?php
//crud poaprocesscontrat

if ($action == 'updateop' && ($user->rights->poa->op->crear || $user->rights->poa->op->mod))
{
	$mesg = '';
	$res = $objpcon->fetch(GETPOST('idrc'));
	if ($res && $objpcon->fk_poa_process == $idProcess)
	{
		$aDate = dol_getdate(dol_now());
		$objpcon->date_order_proceed = dol_stringtotime(substr(GETPOST('di_'),0,10).' '.$aDate['hours'].':'.$aDate['minutes']);
		if ($objpcon->date_order_proceed>0)
		{
			$res = $objpcon->update($user);
			if ($res > 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida);
				exit;
			}
			else
			{
				$error++;
				$mesg.='<div class="error">'.$objpcon->error.'</div>';

			}
		}
		else
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("ErrorDateisrequired").'</div>';
		}
	}
	else
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorRegisternotexist").'</div>';
	}
}

if ($action == 'updaterp' && ($user->rights->poa->op->rp))
{
	$mesg = '';
	$res = $objpcon->fetch(GETPOST('idrc'));
	if ($res && $objpcon->fk_poa_process == $idProcess)
	{
		$aDate = dol_getdate(dol_now());
		$objpcon->date_provisional = dol_stringtotime(substr(GETPOST('di_'),0,10).' '.$aDate['hours'].':'.$aDate['minutes']);

		if ($objpcon->date_provisional>0)
		{
			$res = $objpcon->update($user);
			if ($res > 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida);
				exit;
			}
			else
			{
				$error++;
				$mesg.='<div class="error">'.$objpcon->error.'</div>';

			}
		}
		else
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("ErrorDateisrequired").'</div>';
		}
	}
	else
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorRegisternotexist").'</div>';
	}
}
if ($action == 'updaterd' && ($user->rights->poa->op->rd))
{
	$mesg = '';
	$res = $objpcon->fetch(GETPOST('idrc'));
	if ($res && $objpcon->fk_poa_process == $idProcess)
	{
		$aDate = dol_getdate(dol_now());
		$objpcon->date_final = dol_stringtotime(substr(GETPOST('di_'),0,10).' '.$aDate['hours'].':'.$aDate['minutes']);

		if ($objpcon->date_final>0)
		{
			$res = $objpcon->update($user);
			if ($res > 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida);
				exit;
			}
			else
			{
				$error++;
				$mesg.='<div class="error">'.$objpcon->error.'</div>';

			}
		}
		else
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("ErrorDateisrequired").'</div>';
		}
	}
	else
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorRegisternotexist").'</div>';
	}
}
?>