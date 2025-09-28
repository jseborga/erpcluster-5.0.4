<?php

/* 29-06-17 1er Cambio Laiwett*/

	// Action to add record
if ($action == 'addprod')
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/partida/card.php?id='.$id,1);
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
	/*if ($partidaproduct->fk_product <=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), null, 'errors');
	}*/

	// verificar que no exista
	// verificar si esta el producto y continuar
	/* mi codigo Laiwett */
    /*$result=$objectdet->fetch($partidaproduct->fk_product=GETPOST('idprod','int'));
	
	if ($result > 0)
	{
		$error++;
		setEventMessages($langs->trans("Error el producto no puede ser incluido",$langs->transnoentitiesnoconv("Product")), null, 'errors');
	}*/
	/* Laiwett Revisar*/

	if (! $error)
		{
			$result=$partidaproduct->create($user);

			if ($result > 0)
			{
				// Creation OK

				$sw = 'true';
				$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/partida/card.php?id='.$id,1);
				header("Location: ".$urltogo);
				
			}
			{
					// Creation KO codLaiwett $partidaproduct->errors
				if (! empty($partidaproduct->errors)) setEventMessages("Error el producto no puede ser incluido",null , 'warnings');
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


	/* codLaiwett */
    // Accion de editar el switch
if ($action == 'checkingPro')
{

	$error=0;
	$res = $partidaproduct->fetch(GETPOST('idr','int'),$object->code);
	//echo 'lucho2'.$res;
	if ($res >0)
	{
	//$object->entity=$conf->entity;
	$active = ($partidaproduct->active?0:1);
	$partidaproduct->active=$active;

	if (! $error)
	{
		/* codLaiwett
		Aqui hacemos la prueba para ver que resusltado nos muestra 
		echo 'result '.$result=$partidaproduct->checkingPro($user);
		exit;*/
		$result=$partidaproduct->checkingPro($user);
		
		if ($result > 0)
		{
			//setEventMessages("Registro ". $l = $product->label ." ".$u = $product->ref." modificado con exito", null, 'mesgs');
			setEventMessages("Registro ".$partidaproduct->label ." ".$u = $partidaproduct->ref." modificado con exito", null, 'mesgs');
			$action='';
		}
		else
		{
                     // Creation KO
			if (! empty($partidaproduct->errors)) setEventMessages(null, $partidaproduct->errors, 'label');
			else setEventMessages($partidaproduct->error, null, 'errors');
			$action='';
		}
	}
	}
	else
	{
		$error++;
		setEventMessages($partidaproduct->error,$partidaproduct->errors,'errors');
	}
	$action='';
}

/* endCodLaiwett */


	// Action to delete
if ($action == 'confirm_deleteprod')
//if ($action == 'deleteprod')
{	
	// codLaiwett ver estos y Estudiar
	//$partidaproduct->fetch(GETPOST('idr','int'),$object->code);
	$partidaproduct->fetch(GETPOST('idr','int'),null);
	// endCodLaiwett
	print_r($_REQUEST);
	$result=$partidaproduct->delete($user);
	echo 'lucho entra al confirmar '.$result;
	if ($result > 0)
	{
			// Delete OK
		setEventMessages("RecordDeleted", null, 'mesgs');
		header("Location: ".dol_buildpath('/orgman/partida/card.php?id='.$id,1));
		exit;
	}
	else
	{
		if (! empty($partidaproduct->errors)) setEventMessages(null, $partidaproduct->errors, 'errors');
		else setEventMessages($partidaproduct->error, null, 'errors');
	}
}
?>