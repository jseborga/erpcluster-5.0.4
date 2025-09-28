<?php

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction') && $massaction != 'presend' && $massaction != 'confirm_presend') { $massaction=''; }

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$objobjetive,$action);    // Note that $action and $objobjetive may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	// Selection of new fields
	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	// Purge search criteria
	if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All tests are required to be compatible with all browsers
	{
		
		$search_fk_poa_strategic='';
		$search_fk_father='';
		$search_fk_area='';
		$search_ref='';
		$search_sigla='';
		$search_period_year='';
		$search_label='';
		$search_pseudonym='';
		$search_unit='';
		$search_pos='';
		$search_fk_user_create='';
		$search_fk_user_mod='';
		$search_status='';


		$search_date_creation='';
		$search_date_update='';
		$toselect='';
		$search_array_options=array();
	}

	// Mass actions
	$objobjetiveclass='Poaplanobjetive';
	$objobjetivelabel='Poa';
	$permtoread = $user->rights->poaobjetive->read;
	$permtodelete = $user->rights->poaobjetive->delete;
	$uploaddir = $conf->poa->dir_output;
	//include DOL_DOCUMENT_ROOT.'/core/actions_massactions.inc.php';


	if ($cancel) 
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/poa/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}		
		if ($id > 0 || ! empty($ref)) $ret = $objobjetive->fetch($id,$ref);
		$action='';
	}
	
	// Action to add record
	if ($action == 'add')
	{

		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/poa/plan/card.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;
		$aAreas = GETPOST('areas');
		$idsArea = '';
		foreach ((array) $aAreas AS $j => $fk_area)
		{
			if (!empty($idsArea)) $idsArea.=',';
			$idsArea.= $fk_area;
		}
		/* object_prop_getpost_prop */

		$objobjetive->fk_poa_strategic=GETPOST('id','int');
		$objobjetive->fk_father=GETPOST('fk_father','int')+0;
		$objobjetive->fk_area=GETPOST('fk_area','int')+0;
		$objobjetive->ref=GETPOST('ref','alpha');
		$objobjetive->sigla=GETPOST('sigla','alpha');
		$objobjetive->period_year=GETPOST('period_year','int');
		$objobjetive->label=GETPOST('label','alpha');
		$objobjetive->pseudonym=GETPOST('pseudonym','alpha');
		$objobjetive->expected_result=GETPOST('expected_result','alpha');
		$objobjetive->unit=$idsArea;
    	//si tiene father
		$obj = new Poaobjetive($db);
		if ($obj->fetch($objobjetive->fk_father) && $objobjetive->fk_father > 0)
		{
			$objobjetive->pos = $obj->pos + 1;
			$objobjetive->sigla = str_pad($obj->ref, $conf->global->POA_CODE_SIZE_OBJETIVE, "0", STR_PAD_LEFT).str_pad($objobjetive->ref, $conf->global->POA_CODE_SIZE_OBJETIVE, "0", STR_PAD_LEFT);
			$object->type = $obj->type;
		}
		else
		{
			$objobjetive->pos = 0;
			$objobjetive->sigla = str_pad($objobjetive->ref, $conf->global->POA_CODE_SIZE_OBJETIVE, "0", STR_PAD_LEFT);
		}
		$objobjetive->level = 0;
		$objobjetive->fk_user_create=$user->id;
		$objobjetive->fk_user_mod=$user->id;
		$objobjetive->status=1;
		$objobjetive->datec=dol_now();
		$objobjetive->datem=dol_now();
		$objobjetive->tms=dol_now();

		

		if (empty($objobjetive->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($objobjetive->label))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
		}

		
		if (! $error)
		{
			$result=$objobjetive->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/poa/objetive/objetive.php?id='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objobjetive->errors)) setEventMessages(null, $objobjetive->errors, 'errors');
				else  setEventMessages($objobjetive->error, null, 'errors');
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
		$error=0;

		
		$objobjetive->fk_poa_strategic=GETPOST('id','int');
		$objobjetive->fk_father=GETPOST('fk_father','int');
		$objobjetive->fk_area=GETPOST('fk_area','int');
		$objobjetive->ref=GETPOST('ref','alpha');
		$objobjetive->sigla=GETPOST('sigla','alpha');
		$objobjetive->period_year=GETPOST('period_year','int');
		$objobjetive->label=GETPOST('label','alpha');
		$objobjetive->pseudonym=GETPOST('pseudonym','alpha');
		$objobjetive->expected_result=GETPOST('expected_result','alpha');
		$objobjetive->unit=GETPOST('unit','alpha');
		$objobjetive->pos=GETPOST('pos','int');
    	//si tiene father
		$obj = new Poaobjetive($db);
		if ($obj->fetch($objobjetive->fk_father) && $objobjetive->fk_father > 0)
		{
			$objobjetive->pos = $obj->pos + 1;
			$objobjetive->sigla = str_pad($obj->ref, $conf->global->POA_CODE_SIZE_OBJETIVE, "0", STR_PAD_LEFT).str_pad($objobjetive->ref, $conf->global->POA_CODE_SIZE_OBJETIVE, "0", STR_PAD_LEFT);
			$object->type = $obj->type;
		}
		else
		{
			$objobjetive->pos = 1;
			$objobjetive->sigla = str_pad($objobjetive->ref, $conf->global->POA_CODE_SIZE_OBJETIVE, "0", STR_PAD_LEFT);
		}

		$objobjetive->fk_user_mod=$user->id;
		$objobjetive->datem=dol_now();

		

		if (empty($objobjetive->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($objobjetive->label))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objobjetive->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objobjetive->errors)) setEventMessages(null, $objobjetive->errors, 'errors');
				else setEventMessages($objobjetive->error, null, 'errors');
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
		$result=$objobjetive->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/poa/objetive/objetive.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($objobjetive->errors)) setEventMessages(null, $objobjetive->errors, 'errors');
			else setEventMessages($objobjetive->error, null, 'errors');
		}
	}

}

?>