<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/salary/departament/card.php
 *	\ingroup    Departaments
 *	\brief      Page card salary departament
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/lib/departament.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("orgman@orgman");

$action=GETPOST('action');

$id        = GETPOST("id",'int');
$ref = GETPOST('ref');
$idr        = GETPOST("idr",'int');
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';

if (!$user->rights->orgman->dpto->lire) accessforbidden();

$object = new Pdepartamentext($db);
$objdepuser = new Pdepartamentuser($db);
$objAdherent = new Adherent($db);

if ($id>0 || (!empty($ref) && !is_null($ref)))
{
	$result = $object->fetch($id,(!is_null($ref) && !empty($ref)?$ref:NULL));
}
else
	print '<p>'.$langs->trans('No defined').'</p>';
if ($idr>0)
{
	$objdepuser->fetch($idr);
}

/*
 * Actions
 */

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$objdepuser,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($action == 'active' && $user->rights->orgman->dpto->write)
	{
		$objdepuser->active = ($objdepuser->active==1?0:1);
		$res = $objdepuser->update($user);
		if ($res<=0)
		{
			$error++;
			setEventMessages($objdepuser->error,$objdepuser->errors,'errors');
		}
		else
			setEventMessages(($objdepuser->active?$langs->trans('Activated'):$langs->trans('Deactivated')),null,'mesgs');
		$action = '';
	}
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/departament/listuser.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;
		/* object_prop_getpost_prop */

		$objdepuser->fk_departament=$id;
		$objdepuser->fk_user=GETPOST('fk_user','int');
		$objdepuser->fk_user_create=$user->id;
		$objdepuser->fk_user_mod=$user->id;
		$objdepuser->active=GETPOST('active','int');
		$objdepuser->privilege=GETPOST('privilege','int')+0;
		$objdepuser->datec=dol_now();
		$objdepuser->datem=dol_now();
		$objdepuser->tms=dol_now();


		if ($objdepuser->fk_user <=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_user")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objdepuser->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/departament/listuser.php?id='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objdepuser->errors)) setEventMessages(null, $objdepuser->errors, 'errors');
				else  setEventMessages($objdepuser->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;


		$objdepuser->fk_departament=$id;
		$objdepuser->fk_user=GETPOST('fk_user','int');
		$objdepuser->fk_user_mod=$user->id;
		$objdepuser->active=GETPOST('active','int');
		$objdepuser->privilege=GETPOST('privilege','int')+0;



		if ($objdepuser->fk_user<=0)
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_user")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objdepuser->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objdepuser->errors)) setEventMessages(null, $objdepuser->errors, 'errors');
				else setEventMessages($objdepuser->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete' && $user->rights->orgman->dpto->del)
	{
		$result=$objdepuser->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/orgman/departament/listuser.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($objdepuser->errors)) setEventMessages(null, $objdepuser->errors, 'errors');
			else setEventMessages($objdepuser->error, null, 'errors');
		}
	}
}




/*
 * View
 */

$form=new Formv($db);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Memberbydepartament"),$help_url);


if ($id || $ref)
{
	dol_htmloutput_mesg($mesg);

	$result = $object->fetch($id,(!is_null($ref) && !empty($ref)?$ref:NULL));
	if ($result < 0)
	{
		dol_print_error($db);
	}
	else
		$id = $object->id;

	$head = departament_prepare_head($object);

	dol_fiche_head($head, 'user', $langs->trans("Departament"), 0, 'orgman');

	print '<table class="border" width="100%">';

	print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';

	$linkback = '<a href="'.DOL_URL_ROOT.'/salary/departament/liste.php">'.$langs->trans("BackToList").'</a>';

	print '<td class="valeur"  colspan="2">';
	print $form->showrefnav($object, 'ref', $linkback,1,'ref','rowid',$object->ref);
	print '</td></tr>';

	  //ref
	print '<tr><td width="20%">'.$langs->trans('Ref').'</td><td colspan="2">';
	print $object->ref;
	print '</td></tr>';
	  //label
	print '<tr><td width="20%">'.$langs->trans('Label').'</td><td colspan="2">';
	print $object->label;
	print '</td></tr>';

	  // father
	$objectF = new Pdepartamentext($db);
	$objectF->fetch($object->fk_father);
	print '<tr><td>'.$langs->trans('Father').'</td><td colspan="2">';
	if ($objectF->id == $object->fk_father)
		print $objectF->getNomUrl(1);
	else
		print '&nbsp;';
	print '</td></tr>';

	  //respon
	$objAdherent->fetch($object->fk_user_resp);
	print '<tr><td>'.$langs->trans('Responsible').'</td><td colspan="2">';
	if ($objAdherent->id == $object->fk_user_resp)
	print $objAdherent->lastname.' '.$objAdherent->firstname;
	else
		print '&nbsp;';
	print '</td></tr>';

	print "</table>";

	dol_fiche_end();

			//listamos los usuarios que estan en el departamento
	include DOL_DOCUMENT_ROOT.'/orgman/tpl/pdepartamentuser.tpl.php';


}


llxFooter();

$db->close();
?>
