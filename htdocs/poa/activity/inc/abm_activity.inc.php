<?php 

//addicion de actigvidad segun purchase_request
if ($subaction == 'addactivity')
{
	$error = 0;
	$period_year = $_SESSION['period_year'];
    //obtenemos el ultimo numero
	$nro_activity = $objactivity->fetch_next_nro($period_year);

	$objactivity->initAsSpecimen();
	$date_activity = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$date_activity = $object->datec;

	$objactivity->gestion 	= $period_year;
	$objactivity->fk_prev 		= GETPOST('fk_prev')+0;
	$objactivity->fk_father 	= GETPOST('fk_father')+0;
	if (!empty($objactivity->fk_father))
	{
		$objnew = new Poaprev($db);
		if ($objnew->fetch('',$objactivity->fk_father)>0)
		{
			if ($objnew->nro_preventive == $objactivity->fk_father)
				$objactivity->fk_father = $objnew->id;
			else
				$objactivity->fk_father = 0;
		}
		else
			$objactivity->fk_father = 0;
	}
	else
		$objactivity->fk_father = 0;
    	//preventive period_year pasada
	$nro_preventive_ant = GETPOST('nro_preventive_ant')+0;
	$period_year_ant = GETPOST('period_year_ant')+0;
	if (!empty($nro_preventive_ant) && !empty($period_year_ant))
	{
		$objnew = new Poaprevext($db);
		if ($objnew->fetch('',$nro_preventive_ant,$period_year_ant)>0)
		{
			if ($objnew->nro_preventive == $nro_preventive_ant && $objnew->period_year == $period_year_ant)
				$objactivity->fk_prev_ant = $objnew->id;
			else
				$objactivity->fk_prev_ant = 0;
		}
		else
			$objactivity->fk_prev_ant = 0;
	}
	else
		$objactivity->fk_prev_ant = 0;

	$objactivity->fk_poa          = GETPOST('fk_poa');
	$objactivity->fk_pac          = GETPOST('fk_pac')+0;
	$objactivity->fk_area         = $object->fk_departament;
	if ($user->admin)
		$objactivity->nro_activity  = (GETPOST('nro_activity')?GETPOST('nro_activity'):$nro_activity);
	else
		$objactivity->nro_activity  = $nro_activity;
	$objactivity->priority        = GETPOST('priority')+0;
	$objactivity->code_requirement= GETPOST('code_requirement');
	$objactivity->date_activity   = $date_activity;
	$objactivity->fk_user_create  = $user->id;
	$objactivity->label           = $object->note_public;
	$objactivity->partida         = GETPOST('partida')+0;
	$objactivity->amount          = GETPOST('amount')+0;
	$objactivity->pseudonym       = $object->note_public;

	if (empty($objactivity->label))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
	}

	$objactivity->datec = dol_now();
	$objactivity->datem = dol_now();
	$objactivity->tms = dol_now();
	$objactivity->entity = $conf->entity;
	$objactivity->statut = 0;
	$objactivity->active = 1;

	if (empty($objactivity->nro_activity))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Nroactivity")), null, 'errors');
	}

	if (empty($error))
	{
		$idact = $objactivity->create($user);
		if ($idact <= 0)
		{
			$mesg='<div class="error">'.$objactivity->error.'</div>';
		}
	}
}

?>