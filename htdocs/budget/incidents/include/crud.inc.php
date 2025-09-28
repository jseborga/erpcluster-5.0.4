<?php

$now = dol_now();
	// Action to add record
if ($action == 'add')
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/incidents/'.$type.'.php?id='.$id,1);
		header("Location: ".$urltogo);
		exit;
	}

	$error=0;

	/* object_prop_getpost_prop */

	$objIncidentsdet->fk_incident=$id;
	$objIncidentsdet->type=GETPOST('type','alpha');
	$objIncidentsdet->fk_object=GETPOST('fk_object','int');
	if (empty($objIncidentsdet->fk_object))$objIncidentsdet->fk_object=0;
	$objIncidentsdet->object=GETPOST('object','int');
	if (empty($objIncidentsdet->object))$objIncidentsdet->object=' ';
	$objIncidentsdet->label=GETPOST('label','alpha');
	$objIncidentsdet->fk_unit=GETPOST('fk_unit','int');
	if (empty($objIncidentsdet->fk_unit))$objIncidentsdet->fk_unit=0;
	$objIncidentsdet->sequen=GETPOST('sequen','int');
	$objIncidentsdet->type=($typetwo?$typetwo:$type);
	$objIncidentsdet->value_one=GETPOST('value_one','alpha');
	$objIncidentsdet->value_two=GETPOST('value_two','alpha');
	$objIncidentsdet->value_three=GETPOST('value_three','alpha');
	$objIncidentsdet->value_four=GETPOST('value_four','alpha');
	$objIncidentsdet->value_five=GETPOST('value_five','alpha');
	$objIncidentsdet->value_six=GETPOST('value_six','alpha');
	$objIncidentsdet->value_seven=GETPOST('value_seven','alpha');
	$objIncidentsdet->quantity=GETPOST('quantity','int');
	if (empty($objIncidentsdet->value_one))$objIncidentsdet->value_one=0;
	if (empty($objIncidentsdet->value_two))$objIncidentsdet->value_two=0;
	if (empty($objIncidentsdet->value_three))$objIncidentsdet->value_three=0;
	if (empty($objIncidentsdet->value_four))$objIncidentsdet->value_four=0;
	if (empty($objIncidentsdet->value_five))$objIncidentsdet->value_five=0;
	if (empty($objIncidentsdet->value_six))$objIncidentsdet->value_six=0;
	if (empty($objIncidentsdet->value_seven))$objIncidentsdet->value_seven=0;
	if (empty($objIncidentsdet->quantity))$objIncidentsdet->quantity=0;

	$objIncidentsdet->fk_user_create=$user->id;
	$objIncidentsdet->fk_user_mod=$user->id;
	$objIncidentsdet->datec = $now;
	$objIncidentsdet->datem = $now;
	$objIncidentsdet->tms = $now;
	$objIncidentsdet->status=1;

	if (empty($objIncidentsdet->label))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
	}

	if (! $error)
	{
		$result=$objIncidentsdet->create($user);
		if ($result > 0)
		{
				// Creation OK
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/incidents/'.$type.'.php?id='.$object->id.'&action='.($typetwo?'createtwo&subaction='.$subaction:'create'),1);
			header("Location: ".$urltogo);
			exit;
		}
		{
				// Creation KO
			if (! empty($objIncidentsdet->errors)) setEventMessages(null, $objIncidentsdet->errors, 'errors');
			else  setEventMessages($objIncidentsdet->error, null, 'errors');
			$action='create';
		}
	}
	else
	{
		$action='create';
	}
}

	// Action to update record
if ($action == 'update')
{
	$typetwo = GETPOST('typetwo','alpha');
	$error=0;
	$objIncidentsdet->fetch($idr);

	$objIncidentsdet->fk_incident=$id;
	$objIncidentsdet->type=GETPOST('type','alpha');
	if (!empty($typetwo))$objIncidentsdet->type=GETPOST('typetwo','alpha');
	$objIncidentsdet->fk_unit=GETPOST('fk_unit','int');
	if (empty($objIncidentsdet->fk_unit))$objIncidentsdet->fk_unit=0;
	$objIncidentsdet->fk_object=GETPOST('fk_object','int');
	if (empty($objIncidentsdet->fk_object))$objIncidentsdet->fk_object=0;
	$objIncidentsdet->object=GETPOST('object','int');
	if (empty($objIncidentsdet->object))$objIncidentsdet->object='';
	$objIncidentsdet->label=GETPOST('label','alpha');
	$objIncidentsdet->sequen=GETPOST('sequen','int');
	$objIncidentsdet->value_one=GETPOST('value_one','alpha');
	$objIncidentsdet->value_two=GETPOST('value_two','alpha');
	$objIncidentsdet->value_three=GETPOST('value_three','alpha');
	$objIncidentsdet->value_four=GETPOST('value_four','alpha');
	$objIncidentsdet->value_five=GETPOST('value_five','alpha');
	$objIncidentsdet->value_six=GETPOST('value_six','alpha');
	$objIncidentsdet->value_seven=GETPOST('value_seven','alpha');
	$objIncidentsdet->quantity=GETPOST('quantity','int');
	if (empty($objIncidentsdet->value_one))$objIncidentsdet->value_one=0;
	if (empty($objIncidentsdet->value_two))$objIncidentsdet->value_two=0;
	if (empty($objIncidentsdet->value_three))$objIncidentsdet->value_three=0;
	if (empty($objIncidentsdet->value_four))$objIncidentsdet->value_four=0;
	if (empty($objIncidentsdet->value_five))$objIncidentsdet->value_five=0;
	if (empty($objIncidentsdet->value_six))$objIncidentsdet->value_six=0;
	if (empty($objIncidentsdet->value_seven))$objIncidentsdet->value_seven=0;
	if (empty($objIncidentsdet->quantity))$objIncidentsdet->quantity=0;

	$objIncidentsdet->fk_user_mod=$user->id;
	$objIncidentsdet->datem = $now;
	$objIncidentsdet->tms = $now;
	$objIncidentsdet->status=1;

	if (empty($objIncidentsdet->label))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
	}

	if (! $error)
	{
		$result=$objIncidentsdet->update($user);
		if ($result > 0)
		{
			$action='view';
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
				// Creation KO
			if (! empty($objIncidentsdet->errors)) setEventMessages(null, $objIncidentsdet->errors, 'errors');
			else setEventMessages($objIncidentsdet->error, null, 'errors');
			$action='edit';
		}
	}
	else
	{
		$action='edit';
	}
}

	// Action to delete
if ($action == 'confirm_delete')
{
	$res = $objIncidentsdet->fetch($idr);
	if ($res==1)
	{
		$result=$objIncidentsdet->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/incidents/'.$type.'.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($objIncidentsdet->errors)) setEventMessages(null, $objIncidentsdet->errors, 'errors');
			else setEventMessages($objIncidentsdet->error, null, 'errors');
		}
	}
}
?>