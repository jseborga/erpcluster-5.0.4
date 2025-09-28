<?php

/*
 * Actions
 */
require_once DOL_DOCUMENT_ROOT.'/poa/appoint/class/poacontratappoint.class.php';
$objapp  = new Poacontratappoint($db);

// Add
if ($action == 'add' && $user->rights->poa->appoint->crear)
{
	$objapp->date_appoint = dol_stringtotime(GETPOST('di_'));
	$error = 0;
	//$objapp->date_appoint = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

	$objapp->fk_user       = $_POST["fk_user"];
	$objapp->fk_user_replace = $_POST["fk_user_replace"];
	$objapp->code_appoint = GETPOST('code_appoint');
	$objapp->fk_contrat = GETPOST('fk_contrat');
	$objapp->fk_user_create = $user->id;
	$objapp->date_create = dol_now();
	$objapp->tms = dol_now();
	$objapp->statut     = 0;

	if ($objapp->fk_user <=0)
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Erroruserisrequired").'</div>';
	}
	if (empty($objapp->code_appoint))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorTypeisrequired").'</div>';
	}
	if ($objapp->fk_user == $objapp->fk_user_replace)
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Erroruserandreplaceisidentical").'</div>';
	}
	if ($objapp->fk_contrat <= 0)
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorContratisrequired").'</div>';
	}
	if (empty($error))
	{
		$id = $objapp->create($user);
		if ($id > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida);
			exit;
		}
	}
	else
		$action = '';
}

//update
if ($action == 'update' && $user->rights->poa->appoint->mod && $_POST["cancel"] != $langs->trans("Cancel"))
{
	if ($objapp->fetch($id) > 0)
	{
		$error = 0;
		//$objapp->date_appoint = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
		$objapp->date_appoint = dol_stringtotime(GETPOST('di_'));

		$objapp->fk_user         = $_POST["fk_user"];
		$objapp->fk_user_replace = $_POST["fk_user_replace"];
		$objapp->code_appoint    = GETPOST('code_appoint');
		$objapp->fk_contrat      = GETPOST('fk_contrat');

		if ($objapp->fk_user <=0)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Erroruserisrequired").'</div>';
		}
		if (empty($objapp->code_appoint))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("ErrorTypeisrequired").'</div>';
		}
		if ($objapp->fk_user == $objapp->fk_user_replace)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Erroruserandreplaceisidentical").'</div>';
		}
		if ($objapp->fk_contrat <= 0)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("ErrorContratisrequired").'</div>';
		}

		if (empty($error))
		{
			$res = $objapp->update($user);
			if ($res > 0)
			{
				header("Location: fiche.php?id=".$id);
				exit;
			}
			$action = 'edit';
			$mesg='<div class="error">'.$objapp->error.'</div>';
		}
		else
		{
			if ($error)
				$action="edit";
		  // Force retour sur page creation
		}
	}
}

			// Confirmation de la validation
if ($action == 'validate')
{
	$objapp->fetch(GETPOST('idr'));
		 //cambiando a validado
	$objapp->statut = 1;
		 //update
	$objapp->update($user);
	$action = '';
}
if ($action == 'unvalidate')
{
	$objapp->fetch(GETPOST('idr'));
		 //cambiando a validado
	$objapp->statut = 0;
		 //update
	$objapp->update($user);
	$action = '';
}

		 // Confirm delete third party
if ($action == 'deletex')
{
	$form = new Form($db);
	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?ida=".$ida.'&idr'.$objapp->id.'&idpro='.$idpro,$langs->trans("Deleteguarantee"),$langs->trans("Confirmdeleteguarante",$object->ref.' '.$object->issuer),"confirm_delete",'',0,2);
	if ($ret == 'html') print '<br>';
}

// Delete
if ($action == 'confirm_delete' && $user->rights->poa->guar->del)
{
	$objapp->fetch($_REQUEST["idr"]);
	$result=$objapp->delete($user);
	if ($result > 0)
	{
		header("Location: ".$_SERVER['PHP_SELF'].'?ida='.$ida);
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$objapp->error.'</div>';
		$action='';
	}
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
	$idpro = $_POST['idpro'];
}

?>