<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---2013 Ramiro Queso ramiro@ubuntu-bo.com---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
*   	\file       dev/Subsidiary/Subsidiary_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2013-09-06 20:51
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';
if (! $res && file_exists("../../../main.inc.php")) $res=@include '../../../main.inc.php';
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
dol_include_once('/fiscal/class/subsidiary.class.php');
dol_include_once('/fiscal/class/entityaddext.class.php');
//dol_include_once('/fiscal/class/entity.class.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("ventas@ventas");

// Get parameters
$id		= GETPOST('id','int');
$rid		= GETPOST('rid','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

$object   = new Subsidiary($db);
//$entity = new Entity($db);
$entity = new Entityaddext($db);


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if ($action == 'add' && $user->rights->fiscal->suc->creer)
{
	$error=0;
	$object->ref     = $_POST["ref"];
	$object->label   = $_POST["label"];
	$object->socialreason   = $_POST["socialreason"];
	$object->subsidiary_number = GETPOST('subsidiary_number');
	$object->subsidiary_matriz = GETPOST('subsidiary_matriz')+0;
	$object->nit   = $_POST["nit"];
	$object->message   = $_POST["message"];
	$object->address = $_POST["address"];
	$object->city = $_POST["city"];
	$object->phone   = $_POST["phone"];
	$object->activity   = $_POST["activity"];
	$object->serie   = $_POST["serie"];
	$object->entity  = (GETPOST('fk_entity')?GETPOST('fk_entity'):$conf->entity);
	$object->matriz_def= GETPOST('matriz_def','int');
	$object->matriz_name   = $_POST["matriz_name"];
	$object->matriz_address= $_POST["matriz_address"];
	$object->matriz_address_two= GETPOST('matriz_address_two');
	$object->matriz_zone= GETPOST('matriz_zone');
	$object->matriz_phone   = $_POST["matriz_phone"]+0;
	$object->matriz_city   = $_POST["matriz_city"];
	$object->fk_user_create = $user->id;
	$object->fk_user_mod = $user->id;
	$object->date_create = dol_now();
	$object->date_mod = dol_now();
	$object->tms = dol_now();
	$object->status  = 0;

	//revision
	if (empty($object->ref))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans('ErrorRefisrequired').'</div>';
	}
	if (empty($object->label))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans('ErrorLabelisrequired').'</div>';
	}
	if (empty($object->socialreason))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans('ErrorSocialreasonisrequired').'</div>';
	}
	if (empty($object->nit))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans('ErrorNitisrequired').'</div>';
	}
	if (empty($error))
	{
		$id=$object->create($user);
		if ($id > 0)
		{
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		// Creation OK
		}
		else
		{
			setEventMessages($object->error,$object->errors,'errors');
			$action="create";
		}
	}
	else
		$action = 'create';
}
//update
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	$object->fetch($_REQUEST["id"]);
	$object->ref     = $_POST["ref"];
	$object->label   = $_POST["label"];
	$object->subsidiary_number = GETPOST('subsidiary_number');
	$object->subsidiary_matriz = GETPOST('subsidiary_matriz')+0;
	$object->socialreason   = $_POST["socialreason"];
	$object->nit   = $_POST["nit"];
	$object->message   = $_POST["message"];
	$object->address = $_POST["address"];
	$object->matriz_def= GETPOST('matriz_def','int');
	$object->matriz_address_two= GETPOST('matriz_address_two');
	$object->matriz_zone= GETPOST('matriz_zone');
	$object->city = $_POST["city"];
	$object->activity = $_POST["activity"];
	$object->phone   = $_POST["phone"];
	$object->matriz_name   = $_POST["matriz_name"];
	$object->matriz_address= $_POST["matriz_address"];
	$object->matriz_phone   = $_POST["matriz_phone"];
	$object->matriz_city   = $_POST["matriz_city"];
	$object->entity  = (GETPOST('fk_entity')?GETPOST('fk_entity'):$conf->entity);
	//$object->serie   = $_POST["serie"];
	$object->state  = 0;
	$result=$object->update($user);
	if ($result > 0)
	{
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
		exit;
	// Creation OK
	}
	else
	{
	// Creation KO
		$mesg=$object->error;
		$action="edit";
	}
}


// Delete
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->fiscal->suc->del)
{
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/fiscal/subsidiary/list.php');
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}



if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

if (!$conf->gobal->FISCAL_ALL_COMPANY)
{
	$filterentity = " AND t.rowid = ".getEntity('subsidiary');
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$help_url='EN:Module_Ventas_En|FR:Module_Ventas|ES:M&oacute;dulo_Ventas';
llxHeader("",$langs->trans("Managementsubsidiary"),$help_url);

$form=new Form($db);

if ($action == 'create' && $user->rights->fiscal->suc->creer)
{
	print_fiche_titre($langs->trans("Newsubsidiary"));

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// ref
	print '<tr><td width="20%" class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="18" maxlength="20" required>';
	print '</td></tr>';

	// label
	print '<tr><td class="fieldrequired">'.$langs->trans('Socialreason').'</td><td colspan="2">';
	print '<input id="label" type="text" value="'.$object->label.'" name="label" size="45" required>';
	print '</td></tr>';

	// socialreazon
	print '<tr><td class="fieldrequired">'.$langs->trans('Nombre comercial').'</td><td colspan="2">';
	print '<input id="socialreason" type="text" value="'.$object->socialreason.'" name="socialreason" size="45" required>';
	print '</td></tr>';

	// nit
	print '<tr><td class="fieldrequired">'.$langs->trans('NIT').'</td><td colspan="2">';
	print '<input id="nit" type="text" value="'.$object->nit.'" name="nit" size="27" required>';
	print '</td></tr>';

	// subsidiary number
	print '<tr><td class="fieldrequired">'.$langs->trans('Sucursal Fiscal').'</td><td colspan="2">';
	print '<input id="subsidiary_number" type="text" value="'.$object->subsidiary_number.'" name="subsidiary_number" size="27" required>';
	print '<input type="checkbox" value="'.$object->subsidiary_matriz.'" name="subsidiary_matriz">';
	print ' '.$langs->trans('Marcar si desea imprimir informacion de la casa matriz');
	print '</td></tr>';

	// address
	print '<tr><td>'.$langs->trans('Address').'</td><td colspan="2">';
	print '<input id="address" type="text" value="'.$object->address.'" name="address" size="45">';
	print '</td></tr>';

	// city
	print '<tr><td>'.$langs->trans('City').'</td><td colspan="2">';
	print '<input id="city" type="text" value="'.$object->city.'" name="city" size="45">';
	print '</td></tr>';

	// activity
	//print '<tr><td>'.$langs->trans('Activity').'</td><td colspan="2">';
	//print '<input id="activity" type="text" value="'.$object->activity.'" name="activity" size="45">';
	//print '</td></tr>';

	// phone
	print '<tr><td>'.$langs->trans('Phone').'</td><td colspan="2">';
	print '<input id="phone" type="text" value="'.$object->phone.'" name="phone" size="45">';
	print '</td></tr>';
	// // serie
	// print '<tr><td>'.$langs->trans('Serie').'</td><td colspan="2">';
	// print '<input id="serie" type="text" value="'.$object->serie.'" name="serie" size="45">';
	// print '</td></tr>';

	// Message
	print '<tr><td>'.$langs->trans('Message').'</td><td colspan="2">';
	print '<input id="message" type="text" value="'.$object->message.'" name="message" size="45">';
	print '</td></tr>';

	// casa matriz por defecto
	print '<tr><td>'.$langs->trans('Itsmotherhouse').'</td><td colspan="2">';
	print $form->selectyesno('matriz_def',$object->matriz_def,1);
	print '</td></tr>';

	print '</table>';
	print '<h3>'.$langs->trans('Casa Matriz').'</h3>';

	print '<table class="border" width="100%">';
	// name
	//print '<tr><td width="20%">'.$langs->trans('Name').'</td><td colspan="2">';
	//print '</td></tr>';

	// address
	print '<tr><td>'.$langs->trans('Address').'</td><td colspan="2">';
	print '<input id="matriz_name" type="hidden" value="ninguno" name="matriz_name"  maxlength="200" >';
	print '<input id="matriz_address" type="text" value="'.$object->matriz_address.'" name="matriz_address" maxlength="255">';
	print '</td></tr>';
	// address
	print '<tr><td>'.$langs->trans('Additionaladdress').'</td><td colspan="2">';
	print '<input id="matriz_address_two" type="text" value="'.$object->matriz_address_two.'" name="matriz_address_two" maxlength="120">';
	print '</td></tr>';
	// address
	print '<tr><td>'.$langs->trans('Zone').'</td><td colspan="2">';
	print '<input id="matriz_zone" type="text" value="'.$object->matriz_zone.'" name="matriz_zone" maxlength="120">';
	print '</td></tr>';
	// phone
	print '<tr><td>'.$langs->trans('Phone').'</td><td colspan="2">';
	print '<input id="matriz_phone" type="text" value="'.$object->matriz_phone.'" name="matriz_phone" size="9"  maxlength="11" >';
	print '</td></tr>';

	// city
	print '<tr><td>'.$langs->trans('City').'</td><td colspan="2">';
	print '<input id="matriz_city" type="text" value="'.$object->matriz_city.'" name="matriz_city" size="40" maxlength="50">';
	print '</td></tr>';
	//entity
	print '<tr><td>'.$langs->trans('Company').'</td><td colspan="2">';
	list($nb,$options) = $entity->select_entity('fk_entity','',1,0);
	print '<select name="fk_entity">'.$options.'</select>';
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($id)
	{
		dol_htmloutput_mesg($mesg);
		$result = $object->fetch($id);
		if ($result < 0)
		{
			dol_print_error($db);
		}

		// Affichage fiche
		if ($action <> 'edit' && $action <> 're-edit')
		{
	  //$head = fabrication_prepare_head($object);

			dol_fiche_head($head, 'Subsidiary', $langs->trans("Subsidiary"), 0, 'generic');

		// Confirmation de la validation
			if ($action == 'validate')
			{
				$object->fetch(GETPOST('id'));
		  //cambiando a validado
				$object->status = 1;
		  //update
				$object->update($user);
				$action = '';
		  //header("Location: fiche.php?id=".$_GET['id']);

			}

		// Confirmation de la validation
			if ($action == 'revalidate')
			{
				$object->fetch(GETPOST('id'));
		  //cambiando a validado
				$object->status = 0;
		  //update
				$object->update($user);
				$action = '';
		  //header("Location: fiche.php?id=".$_GET['id']);

			}


	  // Confirm delete third party
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deletesubsidiary"),$langs->trans("Confirmdeletesubsidiary",$object->ref.' '.$object->label),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			print '<table class="border" width="100%">';

	  // ref
			print '<tr><td width="20%">'.$langs->trans('Ref').'</td><td colspan="2">';
			print $object->ref;
			print '</td></tr>';

	  // label
			print '<tr><td>'.$langs->trans('Socialreason').'</td><td colspan="2">';
			print $object->label;
			print '</td></tr>';


	  // socialreazon
			print '<tr><td>'.$langs->trans('Nombre comercial').'</td><td colspan="2">';
			print $object->socialreason;
			print '</td></tr>';

	  // nit
			print '<tr><td>'.$langs->trans('NIT').'</td><td colspan="2">';
			print $object->nit;
			print '</td></tr>';

	  // sucursal fiscal
			print '<tr><td>'.$langs->trans('Sucursal fiscal').'</td><td colspan="2">';
			print $object->subsidiary_number;
			print '</td></tr>';

	  // address
			print '<tr><td>'.$langs->trans('Address').'</td><td colspan="2">';
			print $object->address;
			print '</td></tr>';

	  // city
			print '<tr><td>'.$langs->trans('City').'</td><td colspan="2">';
			print $object->city;
			print '</td></tr>';

	// activity
			//print '<tr><td>'.$langs->trans('Activity').'</td><td colspan="2">';
			//print $object->activity;
			//print '</td></tr>';

	  // phone
			print '<tr><td>'.$langs->trans('Phone').'</td><td colspan="2">';
			print $object->phone;
			print '</td></tr>';
	  // // serie
	  // print '<tr><td>'.$langs->trans('Serie').'</td><td colspan="2">';
	  // print $object->serie;
	  // print '</td></tr>';

	  // Message
			print '<tr><td>'.$langs->trans('Message').'</td><td colspan="2">';
			print $object->message;
			print '</td></tr>';
	  // Matrizdef
			print '<tr><td>'.$langs->trans('Itsmotherhouse').'</td><td colspan="2">';
			print ($object->matriz_def?$langs->trans('Yes'):$langs->trans('No'));
			print '</td></tr>';
			print '</table>';
			print '<h3>'.$langs->trans('Casa Matriz').'</h3>';
			print '<table class="border" width="100%">';
	// name
			//print '<tr><td width="20%">'.$langs->trans('Name').'</td><td colspan="2">';
			//print $object->matriz_name;
			//print '</td></tr>';

	// address
			print '<tr><td width="20%">'.$langs->trans('Address').'</td><td colspan="2">';
			print $object->matriz_address;
			print '</td></tr>';
	// address two
			print '<tr><td width="20%">'.$langs->trans('Additionaladdress').'</td><td colspan="2">';
			print $object->matriz_address_two;
			print '</td></tr>';
	// zone
			print '<tr><td width="20%">'.$langs->trans('Zone').'</td><td colspan="2">';
			print $object->matriz_zone;
			print '</td></tr>';
	// phone
			print '<tr><td>'.$langs->trans('Phone').'</td><td colspan="2">';
			print $object->matriz_phone;
			print '</td></tr>';

	// city
			print '<tr><td>'.$langs->trans('City').'</td><td colspan="2">';
			print $object->matriz_city;
			print '</td></tr>';

	  // state
			print '<tr><td>'.$langs->trans('Status').'</td><td colspan="2">';
			print $object->getLibStatut($object->status,5);
			print '</td></tr>';

			print '<tr><td>'.$langs->trans('Company').'</td><td colspan="2">';
			if ($object->entity == 1)
			{
				$entity->id = 1;
				$entity->nit = $conf->global->MAIN_INFO_TVAINTRA;
				$entity->socialreason = $conf->global->MAIN_INFO_SOCIETE_NOM;
			}
			else
			{
				$res = $entity->fetch($object->entity);
			}

			print $entity->socialreason;
			print '</td></tr>';

			print '</table>';

			print '</div>';


		//************************************ */
		// Barre d'action        */
	  	//                       */
	  	//************************************ */

			print "<div class=\"tabsAction\">\n";

			if ($action == '')
			{
				if ($user->rights->fiscal->suc->creer)
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create">'.$langs->trans("Createnew").'</a>';
				else
					print '<a class="butActionRefused" href="#">'.$langs->trans("Createnew").'</a>';

				if ($user->rights->fiscal->suc->mod && $object->status==0)
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&id='.$object->id.'">'.$langs->trans("Modify").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if ($user->rights->fiscal->suc->val && $object->status == 0)
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=validate&id='.$object->id.'">'.$langs->trans("Valid").'</a>';
				elseif($user->rights->fiscal->suc->val && $object->status == 1)
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=revalidate&id='.$object->id.'">'.$langs->trans("Change").'</a>';

				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Valid")."</a>";

				if ($user->rights->fiscal->suc->del  && $object->status==0)
					print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&id='.$object->id.'">'.$langs->trans("Delete").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
			}
			print "</div>";
		}
		// Edition fiche
		if (($action == 'edit' || $action == 're-edit') && 1)
		{
			$options = '';
			foreach ((array) $entity->lines AS $i => $line)
			{
				$selected = '';
				if ($line->id == $object->entity) $selected = 'selected';
				$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
			}

	  		//print_fiche_titre($langs->trans("ApplicationsEdit"),$mesg);
			print_fiche_titre($langs->trans("ApplicationsEdit"));

			print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';

			print '<table class="border" width="100%">';

	  // ref
			print '<tr><td width="20%" class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
			print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="8" maxlength="10" required>';
			print '</td></tr>';

	  // label
			print '<tr><td class="fieldrequired">'.$langs->trans('Label').'</td><td colspan="2">';
			print '<input id="label" type="text" value="'.$object->label.'" name="label" size="38" maxlength="40" required>';
			print '</td></tr>';

	  // socialreazon
			print '<tr><td class="fieldrequired">'.$langs->trans('Socialreason').'</td><td colspan="2">';
			print '<input id="socialreason" type="text" value="'.$object->socialreason.'" name="socialreason" size="45" required>';
			print '</td></tr>';

	  // nit
			print '<tr><td class="fieldrequired">'.$langs->trans('NIT').'</td><td colspan="2">';
			print '<input id="nit" type="text" value="'.$object->nit.'" name="nit" size="27" required>';
			print '</td></tr>';

	  // sucursal fiscal
			print '<tr><td>'.$langs->trans('Sucursal fiscal').'</td><td colspan="2">';
			print '<input id="subsidiary_number" type="text" value="'.$object->subsidiary_number.'" name="subsidiary_number" size="45">';
			$checked = '';
			if ($object->subsidiary_matriz == 1) $checked = 'checked';
			print '<input type="checkbox" name="subsidiary_matriz" '.$checked.'>';
			print '</td></tr>';

	  // address
			print '<tr><td>'.$langs->trans('Address').'</td><td colspan="2">';
			print '<input id="address" type="text" value="'.$object->address.'" name="address" size="45">';
			print '</td></tr>';

	  // city
			print '<tr><td>'.$langs->trans('City').'</td><td colspan="2">';
			print '<input id="city" type="text" value="'.$object->city.'" name="city" size="45">';
			print '</td></tr>';

	  // activity
			//print '<tr><td>'.$langs->trans('Activity').'</td><td colspan="2">';
			//print '<input id="activity" type="text" value="'.$object->activity.'" name="activity" size="45">';
			//print '</td></tr>';

	  // phone
			print '<tr><td>'.$langs->trans('Phone').'</td><td colspan="2">';
			print '<input id="phone" type="text" value="'.$object->phone.'" name="phone" size="45">';
			print '</td></tr>';
	  // // serie
	  // print '<tr><td>'.$langs->trans('Serie').'</td><td colspan="2">';
	  // print '<input id="serie" type="text" value="'.$object->serie.'" name="serie" size="45">';
	  // print '</td></tr>';

	  // Message
			print '<tr><td>'.$langs->trans('Message').'</td><td colspan="2">';
			print '<input id="message" type="text" value="'.$object->message.'" name="message" size="45">';
			print '</td></tr>';

	  // casa matriz por defecto
			print '<tr><td>'.$langs->trans('Itsmotherhouse').'</td><td colspan="2">';
			print $form->selectyesno('matriz_def',$object->matriz_def,1);
			print '</td></tr>';


			print '</table>';
			print '<h3>'.$langs->trans('Casa Matriz').'</h3>';
			print '<table class="border" width="100%">';
	// name
			//print '<tr><td width="20%">'.$langs->trans('Name').'</td><td colspan="2">';
			//print '<input id="matriz_name" type="text" value="'.$object->matriz_name.'" name="matriz_name"  maxlength="200" >';
			//print '</td></tr>';


	// address
			print '<tr><td>'.$langs->trans('Address').'</td><td colspan="2">';
			print '<input id="matriz_address" type="text" value="'.$object->matriz_address.'" name="matriz_address" maxlength="255">';
			print '</td></tr>';
	// address
			print '<tr><td>'.$langs->trans('Additionaladdress').'</td><td colspan="2">';
			print '<input id="matriz_address_two" type="text" value="'.$object->matriz_address_two.'" name="matriz_address_two" maxlength="120">';
			print '</td></tr>';
	// address
			print '<tr><td>'.$langs->trans('Zone').'</td><td colspan="2">';
			print '<input id="matriz_zone" type="text" value="'.$object->matriz_zone.'" name="matriz_zone" maxlength="120">';
			print '</td></tr>';
	// phone
			print '<tr><td>'.$langs->trans('Phone').'</td><td colspan="2">';
			print '<input id="matriz_phone" type="text" value="'.$object->matriz_phone.'" name="matriz_phone" size="9"  maxlength="11" >';
			print '</td></tr>';

	// city
			print '<tr><td>'.$langs->trans('City').'</td><td colspan="2">';
			print '<input id="matriz_city" type="text" value="'.$object->matriz_city.'" name="matriz_city" size="40" maxlength="50">';
			print '</td></tr>';

	//entity
			print '<tr><td>'.$langs->trans('Company').'</td><td colspan="2">';
			list($nb,$options) = $entity->select_entity('fk_entity','',1,0);
			print '<select name="fk_entity">'.$options.'</select>';
			print '</td></tr>';

			print '</table>';

			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
			print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

			print '</form>';
		}
	}
}

// End of page
llxFooter();
$db->close();
?>
