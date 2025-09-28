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
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/stock.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotbanksoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/lib/almacen.lib.php';

$langs->load("products");
$langs->load("stocks");
$langs->load("companies");

$action=GETPOST('action');

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$id = GETPOST("id",'int');
$idr = GETPOST("idr",'int');
if (! $sortfield) $sortfield="u.lastname";
if (! $sortorder) $sortorder="ASC";

$mesg = '';

// Security check
//$result=restrictedArea($user,'stock');
if (!$user->rights->almacen->entr->crear) accessforbidden();

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('warehousecard'));

$id=GETPOST("id",'int');
$fk_user = GETPOST('fk_user');

$objEntrepotuser = new Entrepotuserext($db);
/*
 * Actions
 */
if ($action == 'adduser' && $user->rights->almacen->local->write)
{
	$error = 0;
	if ($fk_user > 0 && !empty($id))
	{
		$obj = new Entrepotuserext($db);
		$obj->fk_entrepot = $id;
		$obj->fk_user = $fk_user;
		$obj->type = GETPOST('type','int');
		$obj->typeapp = GETPOST('typeapp','int');
		$obj->date_create = dol_now();
		$obj->fk_user_mod = $user->id;
		$obj->active = 1;
		$obj->tms = dol_now();
		$obj->statut = 1;
		if ($obj->type<0)
		{
			$error++;
			setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Wharehouseuser')),null, 'errors');
		}
		if ($obj->typeapp<0)
		{
			$error++;
			setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Applicant')),null, 'errors');
		}
		if (!$error)
		{
			$res = $obj->create($user);
			if ($res > 0)
			{
				header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
			else
			{
				$mesg='<div class="error">'.$obj->error.'</div>';
				$action = 'createuser';
			}
		}
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans('ErrorSelectuser').'</div>';
	}
	$action = 'createuser';

}
//update
if ($action == 'updateuser' && $user->rights->almacen->local->write)
{
	$error = 0;
	if ($fk_user > 0 && !empty($id))
	{
		$obj = new Entrepotuserext($db);
		$obj->fetch($idr);
		$obj->fk_entrepot = $id;
		$obj->fk_user = GETPOST('fk_user');
		$obj->type = GETPOST('type','int');
		$obj->typeapp = GETPOST('typeapp','int');
		$obj->fk_user_mod = $user->id;
		$obj->active = 1;
		$obj->tms = dol_now();
		$obj->statut = 1;
		if ($obj->type<0)
		{
			$error++;
			setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Wharehouseuser')),null, 'errors');
		}
		if ($obj->typeapp<0)
		{
			$error++;
			setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Applicant')),null, 'errors');
		}
		if (!$error)
		{
			$res = $obj->update($user);
			if ($res > 0)
			{
				setEventMessages($langs->trans('Satisfactoryupdate'),null,'mesgs');
				header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
			else
			{
				setEventMessages($obj->error,$obj->errors,'errors');
				$action = 'edituser';
			}
		}
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans('ErrorSelectuser').'</div>';
	}
	$action = 'edituser';
}

if ($action == 'deleteuser' && $user->rights->almacen->local->del)
{
	$error = 0;
	$obj = new Entrepotuserext($db);
	$obj->fetch(GETPOST('idr','int'));
	if ($obj->id == GETPOST('idr','int') && $obj->fk_entrepot == $id)
	{
		$res = $obj->delete($user);
		if ($res > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			$mesg='<div class="error">'.$obj->error.'</div>';
			$action = '';
		}
	}
	else
		$action = '';
}


/*
 * View
 */

$productstatic=new Product($db);
$form=new Form($db);
$formcompany=new FormCompany($db);

$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
llxHeader("",$langs->trans("WarehouseCard"),$help_url);


if ($id)
{
	dol_htmloutput_mesg($mesg);

	$object = new Entrepot($db);
	$result = $object->fetch($id);
	if ($result < 0)
	{
		dol_print_error($db);
	}

		// Affichage fiche
	if ($action <> 'edit' && $action <> 're-edit')
	{
		$head = almacen_prepare_head($object);

		dol_fiche_head($head, 'Permission', $langs->trans("Warehouse"), 0, 'stock');

			// Confirm delete third party
		if ($action == 'delete')
		{
			print $form->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("DeleteAWarehouse"),$langs->trans("ConfirmDeleteWarehouse",$object->libelle),"confirm_delete",'',0,2);
		}

		print '<table class="border" width="100%">';

		$linkback = '<a href="'.DOL_URL_ROOT.'/almacen/local/liste.php">'.$langs->trans("BackToList").'</a>';

			// Ref
		print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td colspan="3">';
		print $form->showrefnav($object, 'id', $linkback, 1, 'rowid', 'libelle');
		print '</td>';

		print '<tr><td>'.$langs->trans("LocationSummary").'</td><td colspan="3">'.$object->lieu.'</td></tr>';

			// Description
		print '<tr><td valign="top">'.$langs->trans("Description").'</td><td colspan="3">'.nl2br($object->description).'</td></tr>';

			// Address
		print '<tr><td>'.$langs->trans('Address').'</td><td colspan="3">';
		print $object->address;
		print '</td></tr>';

			// Town
		print '<tr><td width="25%">'.$langs->trans('Zip').'</td><td width="25%">'.$object->zip.'</td>';
		print '<td width="25%">'.$langs->trans('Town').'</td><td width="25%">'.$object->town.'</td></tr>';

			// Country
		print '<tr><td>'.$langs->trans('Country').'</td><td colspan="3">';
		if (! empty($object->country_code))
		{
			$img=picto_from_langcode($object->country_code);
			print ($img?$img.' ':'');
		}
		print $object->country;
		print '</td></tr>';

			// Status
		print '<tr><td>'.$langs->trans("Status").'</td><td colspan="3">'.$object->getLibStatut(4).'</td></tr>';

		$calcproductsunique=$object->nb_different_products();
		$calcproducts=$object->nb_products();

			// Total nb of different products
		print '<tr><td valign="top">'.$langs->trans("NumberOfDifferentProducts").'</td><td colspan="3">';
		print empty($calcproductsunique['nb'])?'0':$calcproductsunique['nb'];
		print "</td></tr>";

			// Nb of products
		print '<tr><td valign="top">'.$langs->trans("NumberOfProducts").'</td><td colspan="3">';
		print empty($calcproducts['nb'])?'0':$calcproducts['nb'];
		print "</td></tr>";

			// Value
		print '<tr><td valign="top">'.$langs->trans("EstimatedStockValueShort").'</td><td colspan="3">';
		print empty($calcproducts['value'])?'0':$calcproducts['value'];
		print "</td></tr>";

			// Last movement
		$sql = "SELECT max(m.datem) as datem";
		$sql .= " FROM ".MAIN_DB_PREFIX."stock_mouvement as m";
		$sql .= " WHERE m.fk_entrepot = '".$object->id."'";
		$resqlbis = $db->query($sql);
		if ($resqlbis)
		{
			$obj = $db->fetch_object($resqlbis);
			$lastmovementdate=$db->jdate($obj->datem);
		}
		else
		{
			dol_print_error($db);
		}
		print '<tr><td valign="top">'.$langs->trans("LastMovement").'</td><td colspan="3">';
		if ($lastmovementdate)
		{
			print dol_print_date($lastmovementdate,'dayhour').' ';
			print '(<a href="'.DOL_URL_ROOT.'/product/stock/mouvement.php?id='.$object->id.'">'.$langs->trans("FullList").'</a>)';
		}
		else
		{
			print $langs->trans("None");
		}
		print "</td></tr>";

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
		if (empty($reshook))
		{
			if (empty($action) && $abc)
			{
				//if ($user->rights->stock->creer)
				//	print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
				//else
				//	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

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
		print_liste_field_titre($langs->trans("User"),"", "u.lastname","&amp;id=".$id,"","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Login"),"", "u.login","&amp;id=".$id,"","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Wharehouseuser"),"", "p.type","&amp;id=".$id,"",'align="center"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Applicant"),"", "p.type","&amp;id=".$id,"",'align="center"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
		print "</tr>";

		$totalunit=0;
		$totalvalue=$totalvaluesell=0;

		$sql = "SELECT p.rowid, p.fk_user, p.type, p.typeapp, u.lastname, u.firstname, u.login ";
		$sql.= " FROM ".MAIN_DB_PREFIX."entrepot_user AS p ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."user AS u ON p.fk_user = u.rowid ";
		$sql.= " WHERE p.fk_entrepot = ".$object->id;
		$sql.= $db->order($sortfield,$sortorder);

		$aType = array(1=>$langs->trans('Wharehouseuser'),2=>$langs->trans('Applicant'));
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
				if ($action == 'edituser' && $idr == $objp->rowid)
				{
					$objnew = $objp;
					include DOL_DOCUMENT_ROOT.'/almacen/permission/tpl/permission.tpl.php';
				}
				else
				{
					if ($objp->type == 2)
					{
						//modificamos por actualizacion de campo typeapp
						$objEntrepotuser->fetch($objp->rowid);
						$objEntrepotuser->type = 0;
						$objEntrepotuser->typeapp = 1;
						$resup = $objEntrepotuser->update($user);
						if ($resup>0)
						{
							$objp->type = $objEntrepotuser->type;
							$objp->typeapp = $objEntrepotuser->typeapp;
						}
					}
					//print '<td>'.dol_print_date($objp->datem).'</td>';
					print "<tr ".$bc[$var].">";
					print "<td>";
					print $objp->lastname.' '.$objp->firstname;
					print '</td>';
					print '<td>'.$objp->login.'</td>';
					print '<td align="center" '.(!$objp->type?'style="color:#ff0000 !Important;";':'').'>'.($objp->type?$langs->trans('Yes'):$langs->trans('No')).'</td>';
					print '<td align="center" '.(!$objp->typeapp?'style="color:#ff0000 !Important;";':'').'>'.($objp->typeapp?$langs->trans('Yes'):$langs->trans('No')).'</td>';
					//print '<td>'.$aType[$objp->type].'</td>';
					print '<td align="right">';
					if ($user->rights->almacen->local->write)
						print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objp->rowid.'&action=edituser">'.img_picto($langs->trans('Edit'),'edit').'</a>';
					print '&nbsp;&nbsp;';
					if ($user->rights->almacen->local->del)
						print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objp->rowid.'&action=deleteuser">'.img_picto($langs->trans('Delete'),'delete').'</a>';
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
			$objnew = new Entrepotbanksoc($db);
			include DOL_DOCUMENT_ROOT.'/almacen/permission/tpl/permission.tpl.php';
		}
		print "</table>\n";

			//ACTIONS USUARIO
		print "<div class=\"tabsAction\">\n";

		if (empty($action))
		{
			if ($user->rights->almacen->local->write)
				print "<a class=\"butAction\" href=\"permission.php?action=createuser&id=".$object->id."\">".$langs->trans("Adduser")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Adduser")."</a>";
		}
		print "</div>";
	}

}



llxFooter();

$db->close();
