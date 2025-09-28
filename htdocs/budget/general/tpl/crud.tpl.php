<?php
$now = dol_now();
	// Action to add record
if ($subaction == 'add' && $user->rights->budget->par->write)
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/card.php?id='.$object->id,1);
		header("Location: ".$urltogo);
		exit;
	}

	$error=0;

	/* general_prop_getpost_prop */

	$general->fk_budget=$object->id;
	$general->exchange_rate=GETPOST('exchange_rate','alpha');
	$general->base_currency=GETPOST('base_currency','alpha');
	$general->second_currency=GETPOST('second_currency','alpha');
	$general->decimal_quant=GETPOST('decimal_quant','int');
	$general->decimal_pu=GETPOST('decimal_pu','int');
	$general->decimal_total=GETPOST('decimal_total','int');
	$general->fk_user_create=$user->id;
	$general->fk_user_mod=$user->id;
	$general->datec = $now;
	$general->datem = $now;
	$general->tms = $now;
	$general->status=1;

	if (! $error)
	{
		$result=$general->create($user);
		if ($result > 0)
		{
				// Creation OK
			$subaction = '';
			$idr = $result;
		}
		else
		{
				// Creation KO
			if (! empty($general->errors)) setEventMessages(null, $general->errors, 'errors');
			else  setEventMessages($general->error, null, 'errors');
			$subaction='create';
		}
	}
	else
	{
		$subaction='create';
	}
}

	// Cancel
if ($subaction == 'update' && GETPOST('cancel')) $subaction='view';

	// Action to update record
if ($subaction == 'update' && ! GETPOST('cancel') && $user->rights->budget->par->mod)
{
	$error=0;

		//$general->fk_budget=GETPOST('fk_budget','int');
	$general->exchange_rate=GETPOST('exchange_rate','alpha');
	$general->base_currency=GETPOST('base_currency','alpha');
	$general->second_currency=GETPOST('second_currency','alpha');
	$general->decimal_quant=GETPOST('decimal_quant','int');
	$general->decimal_pu=GETPOST('decimal_pu','int');
	$general->decimal_total=GETPOST('decimal_total','int');
		//$general->fk_user_create=GETPOST('fk_user_create','int');
	$general->fk_user_mod=$user->id;
	$general->datem = $now;
	$general->tms = $now;
	if (! $error)
	{
		$result=$general->update($user);
		if ($result > 0)
		{
			$action='gen';
			$subaction='view';
		}
		else
		{
				// Creation KO
			if (! empty($general->errors)) setEventMessages(null, $general->errors, 'errors');
			else setEventMessages($general->error, null, 'errors');
			$subaction='edit';
		}
	}
	else
	{
		$subaction='edit';
	}
}
	// Action to delete
if ($subaction == 'confirm_delete' && $user->rights->budget->par->del)
{
	$result=$general->delete($user);
	if ($result > 0)
	{
			// Delete OK
		setEventMessages("RecordDeleted", null, 'mesgs');
		header("Location: ".dol_buildpath('/budget/budget/card.php?id='.$object->id,1));
		exit;
	}
	else
	{
		if (! empty($general->errors)) setEventMessages(null, $general->errors, 'errors');
		else setEventMessages($general->error, null, 'errors');
	}
}

?>