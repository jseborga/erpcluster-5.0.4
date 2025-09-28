<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/product/stock/fiche.php
 *	\ingroup    stock
 *	\brief      Page fiche entrepot
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/orgman/lib/orgman.lib.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mpropertyuser.class.php';

$langs->load("orgman");

$action=GETPOST('action');

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$id = GETPOST("id",'int');
if (! $sortfield) $sortfield="u.lastname";
if (! $sortorder) $sortorder="ASC";

$mesg = '';

// Security check
//$result=restrictedArea($user,'stock');
if (!$user->rights->orgman->prop->lire)
	accessforbidden();

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('property'));

$id=GETPOST("id",'int');
$fk_user = GETPOST('fk_user');

/*
 * Actions
 */
if ($action == 'adduser' && $user->rights->orgman->prop->write)
{
	$error = 0;
	if ($fk_user > 0 && !empty($id))
	{
		$obj = new Mpropertyuser($db);
		$obj->fk_property = $id;
		$obj->fk_user = $fk_user;
		$obj->date_create = dol_now();
		$obj->fk_user_mod = $user->id;
		$obj->active = 1;
		$obj->tms = dol_now();
		$obj->status = 1;
		$res = $obj->create($user);
		if ($res > 0)
		{
			setEventMessages($langs->trans('Saverecord'), null, 'mesgs');
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			setEventMessages($langs->trans('Error').' '.$obj->error, null, 'error');
			$action = 'createuser';
		}
	}
	else
	{
		setEventMessages($langs->trans('ErrorSelectuser'), null, 'error');
		$action = 'createuser';
	}

}

if ($action == 'deleteuser' && $user->rights->orgman->prop->del)
{
	$error = 0;
	$obj = new Mpropertyuser($db);
	$obj->fetch(GETPOST('idr','int'));
	if ($obj->id == GETPOST('idr','int') && $obj->fk_property == $id)
	{
		$res = $obj->delete($user);
		if ($res > 0)
		{
			setEventMessages($langs->trans('Deleterecord'), null, 'mesgs');
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			setEventMessages($langs->trans('Error').' '.$obj->error, null, 'error');
			$action = '';
		}
	}
	else
		$action = '';
}


/*
 * View
 */

$form=new Form($db);
$formcompany=new FormCompany($db);

$help_url='EN:Module_Assets_En|FR:Module_Asstes|ES:M&oacute;dulo_Assets';
llxHeader("",$langs->trans("Propertyuser"),$help_url);


if ($id)
{
	dol_htmloutput_mesg($mesg);

	$object = new Mproperty($db);
	$result = $object->fetch($id);
	if ($result < 0)
	{
		dol_print_error($db);
	}

		// Affichage fiche
	if ($action <> 'edit' && $action <> 're-edit')
	{
		//armamos los grupos del presupuesto
		$head = property_prepare_head($object);
		$tab = 'permission';
		dol_fiche_head($head, $tab, $langs->trans("Assets"),0,($object->public?'projectpub':'project'));


		print '<table class="border" width="100%">';

	  	// ref
		print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';
		$linkback = '<a href="'.DOL_URL_ROOT.'/orgman/property/liste.php">'.$langs->trans("BackToList").'</a>';

		print '<td class="valeur"  colspan="2">';
		dol_banner_tab($object, 'id', $linkback, ($user->societe_id?0:1), 'rowid', 'label');
		print '</td></tr>';


	 	//address
		print '<tr><td>'.$langs->trans('Address').'</td><td colspan="2">';
		print $object->address;
		print '</td></tr>';

		if ($object->fk_country)
		{
			$tmparray=getCountry($object->fk_country,'all');
			$object->country_code=$tmparray['code'];
			$object->country=$tmparray['label'];

			print '<tr><td>'.$langs->trans('Country').'</td><td colspan="2">';
			print $object->country;
			print '</td></tr>';
		}


		print "</table>";

		print '</div>';


		/* ************************************************************************** */
		/*                                                                            */
		/* Barre d'action                                                             */
		/*                                                                            */
		/* ************************************************************************** */

		print "<div class=\"tabsAction\">\n";

		$parameters=array();
		$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
			// Note that $action and $object may have been modified by hook
		if (empty($reshook))
		{
			if (empty($action))
			{
				if ($user->rights->stock->creer)
					print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if ($user->rights->stock->supprimer)
					print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
			}
		}

		print "</div>";


		/* ************************************************************************** */
		/*                                                                            */
		/* Affichage de la liste des produits de l'entrepot                           */
		/*                                                                            */
		/* ************************************************************************** */
		print '<br>';



		print '<table class="noborder" width="100%">';
		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("User"),"", "p.ref","&amp;id=".$id,"","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Login"),"", "p.label","&amp;id=".$id,"","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
		print "</tr>";

		$totalunit=0;
		$totalvalue=$totalvaluesell=0;

		$sql = "SELECT p.rowid, p.fk_user, u.lastname, u.firstname, u.login ";
		$sql.= " FROM ".MAIN_DB_PREFIX."m_property_user AS p ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."user AS u ON p.fk_user = u.rowid ";
		$sql.= " WHERE p.fk_property = ".$object->id;
		$sql.= $db->order($sortfield,$sortorder);

		dol_syslog('List entrepot sql='.$sql);
		$resql = $db->query($sql);
		$aFilter = array(1=>1);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			$var=True;
			while ($i < $num)
			{
				$var=!$var;
				$objp = $db->fetch_object($resql);
				$aFilter[$objp->fk_user] = $objp->fk_user;
				if ($action == 'edituser' && $idr == $objp->id)
				{
					$objnew = $objp;
					include DOL_DOCUMENT_ROOT.'/orgman/property/tpl/permission.tpl.php';
				}
				else
				{
						//print '<td>'.dol_print_date($objp->datem).'</td>';
					print "<tr ".$bc[$var].">";
					print "<td>";
					print $objp->lastname.' '.$objp->firstname;
					print '</td>';
					print '<td>'.$objp->login.'</td>';
					print '<td align="right">';
					if ($user->rights->orgman->prop->del)
						print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objp->rowid.'&action=deleteuser">'.img_picto($langs->trans('Edit'),'delete').'</a>';
					print '</td>';
					print "</tr>";
				}
				$i++;
			}
			$db->free($resql);
		}
		else
		{
			dol_print_error($db);
		}
			//registro nuevo
		if ($action == 'createuser')
		{
			$objnew = new mpropertyuser($db);
			include DOL_DOCUMENT_ROOT.'/orgman/property/tpl/permission.tpl.php';
		}
		print "</table>\n";

			//ACTIONS USUARIO
		print "<div class=\"tabsAction\">\n";

		if (empty($action))
		{
			if ($user->rights->orgman->prop->write)
				print "<a class=\"butAction\" href=\"permission.php?action=createuser&id=".$object->id."\">".$langs->trans("Adduser")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Adduser")."</a>";
		}
		print "</div>";
	}

}



llxFooter();

$db->close();
