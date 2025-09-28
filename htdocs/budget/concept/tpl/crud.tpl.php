<?php

dol_include_once('/budget/class/budgetconcept.class.php');
dol_include_once('/budget/class/parametercalculation.class.php');
$concept = new Budgetconcept($db);
$parameter = new Parametercalculation($db);
if ($subaction == 'addconcept' && $user->rights->budget->par->write)
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/card.php?id='.$id.'&action=gen',1);
		header("Location: ".$urltogo);
		exit;
	}
	if (count($_SESSION['aConcept'][$id])>0)
	{
		$aData = $_SESSION['aConcept'][$id];

		foreach ($aData AS $code)
		{
			$parameter->fetch(0,$code);
			$concept->fk_budget = $id;
			$concept->ref = $code;
			$concept->label = $parameter->label;
			if (!empty($paramter->type)) $concept->type = $parameter->type;
			$concept->amount = 0;
			$concept->fk_user_create = $user->id;
			$concept->fk_user_mod = $user->id;
			$concept->date_create = dol_now();
			$concept->date_mod = dol_now();
			$concept->tms = dol_now();
			$concept->status = 2;
			if (empty($concept->ref))
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
			}
			if (empty($concept->label))
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
			}
			if (!$error)
			{
				$res = $concept->create($user);
				if ($res<=0)
				{
					setEventMessages($concept->error,$concept->errors,'errors');
					$subaction = 'createc';
				}
				else
				{
					setEventMessages($langs->trans('Saverecord'),null,'mesgs');
				}
			}
			setEventMessages($langs->trans('Precacución, se crearon parámetros de calculo para el presupuesto con valor 0'),null,'warning');
		}
		//vaciamos la session
		unset($_SESSION['aConcept']);
		if (!$error)
		{
			header('Location: '.$_SERVER['PHP_SELF'].'/budget/card.php?id='.$id.'&action=gen');
			exit;
		}
		else
			$subaction = '';
	}
	else
	{
		$parameter->fetch(0,GETPOST('code'));
		$concept->fk_budget = $id;
		$concept->ref = GETPOST('code');
		$concept->label = $parameter->label;
		if (!empty($paramter->type)) $concept->type = $parameter->type;
		$concept->amount = GETPOST('amount');
		$concept->fk_user_create = $user->id;
		$concept->fk_user_mod = $user->id;
		$concept->date_create = dol_now();
		$concept->date_mod = dol_now();
		$concept->tms = dol_now();
		$concept->status = 2;
		if (empty($concept->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($concept->label))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
		}
		if (!$error)
		{
			$res = $concept->create($user);
			if ($res<=0)
			{
				setEventMessages($concept->error,$concept->errors,'errors');
				$subaction = 'createc';
			}
			else
			{
				setEventMessages($langs->trans('Saverecord'),null,'mesgs');
				header('Location: '.$_SERVER['PHP_SELF'].'/budget/card.php?id='.$id.'&action=gen');
				exit;
			}
		}
		else
			$subaction = 'createc';
	}
}


if ($subaction == 'updateconcept' && $user->rights->budget->par->mod)
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/card.php?id='.$id.'&action=gen',1);
		header("Location: ".$urltogo);
		exit;
	}
	$db->begin();
	$aAmount = GETPOST('amount');
	foreach ((array) $aAmount AS $code => $value)
	{
		$res = $concept->fetch(0,$code,$id);
		if ($res > 0)
		{
			//$concept->fk_budget = $id;
			//$concept->ref = $conceptref;
			//$concept->label = GETPOST('label');
			$concept->amount = $value;
			$concept->fk_user_mod = $user->id;
			$concept->date_mod = dol_now();
			$concept->tms = dol_now();
			$concept->status = 2;
			$resup = $concept->update($user);
			if ($resup<=0)
			{
				$error++;
				setEventMessages($concept->error,$concept->errors,'errors');
			}
		}
	}
	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Updatesuccessfull'),null,'mesgs');
		$subaction='';
		//header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id.'&action=gen');
		//exit;
	}
	else
	{
		$db->rollback();
		$subaction = 'editc';
	}
}

if ($subaction == 'confirm_delete_concept' && $user->rights->budget->par->del)
{
	$concept->fetch(GETPOST('idr'));
	if ($concept->id == GETPOST('idr'))
	{
		$res = $concept->delete($user);
		if ($res<=0)
			setEventMessages($concept->error,$concept->errors,'errors');
		else
		{
			setEventMessages($langs->trans('Deletesuccessfull'),nulls,'mesgs');
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/parameters.php?id='.$id.'&action=gen',1);
			header("Location: ".$urltogo);
			exit;
		}
		$subaction = '';
	}
}
?>