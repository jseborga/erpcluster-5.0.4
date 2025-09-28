<?php

	// Action to add record
if ($action == 'addprod')
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/poa/partida/card.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}

	$error=0;
	/* object_prop_getpost_prop */

	$partidaproduct->code_partida=$object->code;
	$partidaproduct->fk_product=GETPOST('idprod','int');
	$partidaproduct->import_key=GETPOST('import_key','alpha');
	$partidaproduct->fk_user_create=$user->id;
	$partidaproduct->fk_user_mod=$user->id;
	$partidaproduct->active=1;
	$partidaproduct->datec = dol_now();
	$partidaproduct->datem = dol_now();
	$partidaproduct->tms = dol_now();


	if (empty($partidaproduct->code_partida))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Codepartida")), null, 'errors');
	}
	if ($partidaproduct->fk_product <=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), null, 'errors');
	}

	if (! $error)
	{
		$result=$partidaproduct->create($user);
		if ($result > 0)
		{
				// Creation OK
			$urltogo=$backtopage?$backtopage:dol_buildpath('/poa/partida/card.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		{
				// Creation KO
			if (! empty($partidaproduct->errors)) setEventMessages(null, $partidaproduct->errors, 'errors');
			else  setEventMessages($partidaproduct->error, null, 'errors');
			$action='createprod';
		}
	}
	else
	{
		$action='createprod';
	}
}

	// Action to update record
if ($action == 'updateprod')
{
	$error=0;


	$partidaproduct->code_partida=$object->code;
	$partidaproduct->fk_product=GETPOST('fk_product','int');
	$partidaproduct->import_key=GETPOST('import_key','alpha');
	$partidaproduct->fk_user_mod=$user->id;
	$partidaproduct->datem=dol_now();
	$partidaproduct->tms=dol_now();

	if (empty($partidaproduct->code_partida))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Codepartida")), null, 'errors');
	}
	if ($partidaproduct->fk_product <=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), null, 'errors');
	}

	if (! $error)
	{
		$result=$partidaproduct->update($user);
		if ($result > 0)
		{
			$action='view';
		}
		else
		{
				// Creation KO
			if (! empty($partidaproduct->errors)) setEventMessages(null, $partidaproduct->errors, 'errors');
			else setEventMessages($partidaproduct->error, null, 'errors');
			$action='editprod';
		}
	}
	else
	{
		$action='editprod';
	}
}

	// Action to delete
if ($action == 'confirm_deleteprod')
{
	$result=$partidaproduct->delete($user);
	if ($result > 0)
	{
			// Delete OK
		setEventMessages("RecordDeleted", null, 'mesgs');
		header("Location: ".dol_buildpath('/poa/partida/card.php?id='.$id,1));
		exit;
	}
	else
	{
		if (! empty($partidaproduct->errors)) setEventMessages(null, $partidaproduct->errors, 'errors');
		else setEventMessages($partidaproduct->error, null, 'errors');
	}
}
?>