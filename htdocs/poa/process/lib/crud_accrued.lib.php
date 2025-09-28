<?php
//payment

// Adddev
if ($action == 'adddev' && $user->rights->poa->deve->crear)
{
	$fk_prev = GETPOST('fk_prev');
	$fk_contrat = GETPOST('fk_contrat');
	$error = 0;
	$object->fetch($fk_prev);
	//$objprev->fetch($idp);
	$date_dev = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$invoice = GETPOST('invoice');
	if ($lAnticipo && empty($invoice))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Error, numberinvoiceisrequired").'</div>';
	}
	$aPartida = GETPOST('partida');
	$aAmount = GETPOST('amount');
	//$nro_dev = GETPOST('nro_dev');

	//recuperamos el ultimo numero de autorizacion
	$objectdev = new Poapartidadev($db);
	$nro_dev = 0;
	if ($objectdev->get_maxref($gestion,$object->fk_area))
		$nro_dev = $objectdev->maximo;
	if ($nro_dev <= 0) $error++;

	$db->begin();
	if (empty($nro_dev))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Error, numberauthisrequired").'</div>';
	}
	if (empty($error))
	{
		foreach((array) $aAmount AS $fk_poa_partida_com => $value)
		{
			//registro nuevo
			if ($value > 0)
			{
				//obtenemos el comprometido
				$objcom->fetch($fk_poa_partida_com);
				$objdev->fk_poa_partida_com = $fk_poa_partida_com;
				$objdev->fk_poa_prev = $fk_prev;
				$objdev->fk_structure = $objcom->fk_structure;
				$objdev->fk_contrat = $objcom->fk_contrat;
				$objdev->fk_contrato = $objcom->fk_contrato;
				$objdev->fk_poa = $objcom->fk_poa;

				// $objdev->fk_structure = GETPOST('fk_structure');
				// $objdev->fk_contrat = GETPOST('fk_contrat');
				// $objdev->fk_poa = GETPOST('fk_poa');

				$objdev->date_dev = $date_dev;
				$objdev->nro_dev = $nro_dev;
				$objdev->gestion = GETPOST('gestion');
				$objdev->invoice = GETPOST('invoice');
				$objdev->date_create = dol_now();
				$objdev->fk_user_create = $user->id;
				$objdev->amount = $value;
				$objdev->partida = $aPartida[$fk_poa_partida_com];
				$objdev->tms = dol_now();
				$objdev->statut = 1;
				$objdev->active = 1;
				$iddev = $objdev->create($user);
				if ($iddev <= 0) $error++;
			}
		}
		if (empty($error))
		{
			if ($object->id == $fk_prev)
			{
				$object->statut = 3; //3 devengado
				if ($object->update($user) > 0)
				{
					//exito;
					$db->commit();
					header('Location: '.$_SERVER['PHP_SELF'].'?ida='.$ida);
					exit;
				}
				else
				{
					$error++;
					$db->rollback();
					//se debe cambiar el estado manualmente
		  		}
	  		}
			else
			{
				$db->rollback();
				$action="createpayment";
				//se debe cambiar el estado manualmente
	  		}
		}
		else
		{
			$db->rollback();
			$action="createpayment";   // Force retour sur page creation
	  	}
  	}
}

?>