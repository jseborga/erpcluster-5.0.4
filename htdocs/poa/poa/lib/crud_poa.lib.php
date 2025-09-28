<?php
if ($modal == 'fichepoa')
{
	// Add
	if ($action == 'add' && $user->rights->poa->poa->crear)
	{
		$error = 0;
		$object->fk_structure = $_POST["fk_structure"];
		//buscamos structure
		$objstr->fetch($object->fk_structure);
		if ($objstr->id == $object->fk_structure)
			$object->ref = $objstr->sigla;
		$object->label     = GETPOST('label');
		$object->partida   = GETPOST('partida');
		$object->gestion   = $gestion;
		$object->amount    = GETPOST('amount');

		$object->entity  = $conf->entity;
		$object->active  = 1;
		$object->statut  = 1;
		$object->version = GETPOST('version');
		if ($object->fk_structure <=0)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorstructureisrequired").'</div>';
		}
		if (empty($object->label))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorlabelisrequired").'</div>';
		}

		if (empty($error))
		{
			$id = $object->create($user);
			if ($id > 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']);
				exit;
			}
			$action = 'create';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		else
		{
			if ($error) $action="create";
		}
	}

	//update
	if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel") &&
		$user->rights->poa->poa->mod)
	{
		$error = 0;
		if ($object->fetch(GETPOST('id'))>0)
		{
			$object->fk_structure = GETPOST("fk_structure");
			//buscamos structure
			$objstr->fetch($object->fk_structure);
			if ($objstr->id == $object->fk_structure)
				$object->ref = $objstr->sigla;
			$object->label     = GETPOST('label');
			$object->partida   = GETPOST('partida');
			//$object->gestion   = $gestion;
			$object->amount    = GETPOST('amount');

			//$object->entity  = $conf->entity;
			$object->active  = 1;
			$object->statut  = 1;
			$object->version = GETPOST('version');
			if ($object->fk_structure <=0)
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errorstructureisrequired").'</div>';
			}
			if (empty($object->label))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errorlabelisrequired").'</div>';
			}

			if (empty($error))
			{
				$res = $object->update($user);
				if ($res > 0)
				{
					header("Location: ".$_SERVER['PHP_SELF']);
					exit;
				}
				$action = 'edit';
				$mesg='<div class="error">'.$object->error.'</div>';
			}
			else
			{
				if ($error) $action="edit";
			}
		}
	}
	if ($_POST["cancel"] == $langs->trans("Cancel"))
	{
		$action = '';
		$_GET["id"] = $_POST["id"];
	}
}
?>
