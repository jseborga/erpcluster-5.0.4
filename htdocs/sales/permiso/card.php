<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *	\file       htdocs/cajachica/fiche.php
 *	\ingroup    cajachica
 *	\brief      Page fiche cajachicaentrepot
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/ventas/permiso/class/entrepotbanksoc.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/compta/bank/class/account.class.php");
require_once(DOL_DOCUMENT_ROOT."/ventas/class/subsidiary.class.php");
require_once(DOL_DOCUMENT_ROOT."/ventas/class/vdosing.class.php");
require_once(DOL_DOCUMENT_ROOT."/ventas/class/Facturation.class.php");

require_once(DOL_DOCUMENT_ROOT."/core/class/html.form.class.php");

require_once(DOL_DOCUMENT_ROOT."/ventas/lib/ventas.lib.php");
require_once(DOL_DOCUMENT_ROOT."/ventas/permiso/lib/permiso.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

// if ($conf->almacen->enabled)
//   {
//     require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelation.class.php");
//     $lAlmacen = true;
//   }
//  else
//    $lAlmacen = false;

$langs->load("societes");
$langs->load("others");
$langs->load("ventas@ventas");


$id = GETPOST('id','int');
$action=GETPOST('action');
$users		= GETPOST("adduser","int",3);

$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
if (! $sortfield) $sortfield="u.login";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

if (!$user->rights->ventas->leerPermiso)
	accessforbidden();

$objectsu = new Subsidiary($db);
$objfact  = new Facturation($db);
$objuser = new User($db);
$objdosing = new Vdosing($db);
$form = new Form($db);

$modPermission = $conf->global->VENTA_PERMISSIONS_PDV_MOD;
/*
 * Actions
 */

// Ajout entrepot
if ($action == 'add' && $user->rights->ventas->crearPermiso)
{
	$object = new Entrepotbanksoc($db);

	if (!$modPermission)
		$object->numero_ip     = $_POST["ref"];
	else
		$object->fk_user = GETPOST('userid','int');
	$object->fk_entrepotid = GETPOST("fk_entrepotid",'int');
	$object->fk_socid      = GETPOST("fk_socid",'int');
	$object->fk_cajaid     = GETPOST("fk_cajaid",'int');
	$object->fk_subsidiaryid = GETPOST("fk_subsidiaryid",'int');
	$object->series = GETPOST('series','alpha');
	$object->entity = $conf->entity;

	$object->status        = "1";

	if (!$modPermission && empty($object->numero_ip))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorIPRequired").'</div>';
	}
	if ($modPermission && $object->fk_user <=0)
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorUserRequired").'</div>';
	}
	if ($object->fk_entrepotid <=0)
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorEntrepotRequired").'</div>';
	}
	if ($object->fk_cajaid <=0)
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorBankRequired").'</div>';
	}
	if ($object->fk_subsidiaryid <=0)
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorSubsidiaryRequired").'</div>';
	}
	//buscamos la relacion subsidiaryid y serie
	$resdosing = $objdosing->fetch_sub_serie($object->fk_subsidiaryid,$object->series);
	if ($resdosing<=0)
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("ErrorSubsidiarySeriesnotexist").'</div>';
	}
	//buscamos si existe registro
	if ($modPermission)
	{
		$objnew = new Entrepotbanksoc($db);
		$res = $objnew->fetch('','',GETPOST('userid','int'));
		if ($res>0 && $objnew->fk_user == GETPOST('userid','int'))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("ErrorExists2Register").'</div>';
		}
	}
	if (!$modPermission)
	{
		$objnew = new Entrepotbanksoc($db);
		$ip = GETPOST('ref','alpha');
		echo '<hr>resnew '.$res = $objnew->fetch('',$ip,'');
		//echo '<hr>'.$objnew->numero_ip.' == '.$ip;
		if ($res>0 && $objnew->numero_ip == $ip)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("ErrorExists1Register").'</div>';
		}
	}
	if (empty($error))
	{
		$id = $object->create($user);
		if ($id > 0)
		{
			header("Location: liste.php");
			exit;
		}
		$action = 'create';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	$action="create";   // Force retour sur page creation
}

// Action ajout groupe utilisateur
// if (($action == 'adduser' || $action == 'removeuser'))
// {
//     // if ($users)
//     // {
//         $editUser = new Cajachicauser($db);
//         //$editUser->fetch($users);
// 	//        $editUser->oldcopy=dol_clone($editUser);
//         $editUser->fetch($id);
//         if ($action == 'adduser')
// 	  $editUser->fk_cajachica = $_POST['fk_cajachica'];
// 	  $editUser->fk_user      = $_POST['fk_user'];

// 	  $result = $editUser->create($user);
//         if ($action == 'removeuser')

// 	  $result = $editUser->delete($user);

//         if ($result > 0)
//         {
//             header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
//             exit;
//         }
//         else
//         {
//             $message.=$object->error;
//         }
// 	//    }
// }

// Delete warehouse
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->ventas->crearPermiso)
{
	$object = new Entrepotbanksoc($db);
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/ventas/permiso/liste.php');
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}

// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	$object = new Entrepotbanksoc($db);
	if ($object->fetch($_POST["id"]))
	{
		if (!$modPermission)
			$object->numero_ip     = $_POST["ref"];
		else
			$object->fk_user     = GETPOST("userid",'int');
		$object->fk_entrepotid = $_POST["fk_entrepotid"];
		$object->fk_socid      = $_POST["fk_socid"];
		$object->fk_cajaid     = $_POST["fk_cajaid"];
		$object->fk_subsidiaryid = $_POST["fk_subsidiaryid"];
		$object->series = GETPOST('series','alpha');
		//buscamos la relacion subsidiaryid y serie
		$resdosing = $objdosing->fetch_sub_serie($object->fk_subsidiaryid,$object->series);
		if ($resdosing<=0)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("ErrorSubsidiarySeriesnotexist").'</div>';
		}

		if ( !$error)
		{
			if ( $object->update($user) > 0)
			{
				$action = '';
				$_GET["id"] = $_POST["id"];
				$mesg = '<div class="ok">'.$langs->trans('Sucessfulupgrade').'</div>';
			}
			else
			{
				$action = 'edit';
				$_GET["id"] = $_POST["id"];
				$mesg = '<div class="error">'.$object->error.'</div>';
			}
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
		}
	}
	else
	{
		$action = 'edit';
		$_GET["id"] = $_POST["id"];
		$mesg = '<div class="error">'.$object->error.'</div>';
	}
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}



/*
 * View
 */

//$productstatic=new Product($db);
$form=new Form($db);
$formcompany=new FormCompany($db);
$formproduct=new FormProduct($db);

$help_url='EN:Module_cajachica_En|FR:Module_cajachica|ES:M&oacute;dulo_cajachica';
llxHeader("",$langs->trans("WarehouseCard"),$help_url);

// if (!$lAlmacen)
//   {
//     echo $mesg='<div class="error">'.$langs->trans('Modulo almacen es requerido').'</div>';
//     exit;
//   }
if ($action == 'create')
{
	$object = new Entrepotbanksoc($db);

	// if ($conf->almacen->enabled)
	//   $objectUr = new Entrepotrelation($db);

	print_fiche_titre($langs->trans("NewPermission"));

	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="type" value="'.$type.'">'."\n";

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	if (!$modPermission)
	{
		// IP
		$ip = $objfact->verificaIP();
		print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("IP").'</td><td colspan="3"><input name="ref" size="20" value="'.$ip.'"></td></tr>';
	}
	else
	{
		print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("User").'</td><td colspan="3">';
		print $form->select_users('','userid',1);
		print '</td></tr>';
	}
	// Societe
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Societe").'</td><td colspan="3">';
	print $form->select_company('','fk_socid');
	print '</td></tr>';

	// Entrepot Almacen
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Entrepot').'</td><td colspan="3">';
	//print select_entrepot('','fk_entrepotid',1);
	print $formproduct->selectWarehouses(($_GET["fk_entrepotid"]?$_GET["fk_entrepotid"]:GETPOST('fk_entrepotid')),'fk_entrepotid','',1);

	//print $objectUr->select_padre('','fk_entrepotid',1);
	print '</td></tr>';

	// Caja
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Caja").'</td><td colspan="3">';

	print $form->select_comptes(((GETPOST('bankid_cash') > 0)?GETPOST('bankid_cash'):$conf->global->CASHDESK_ID_BANKACCOUNT_CASH),'fk_cajaid',0,"courant=2",($defaultknown?0:2));
	print '</td>';
	print '</tr>';

	// subsidiary
	print '<tr><td width="25%">'.$langs->trans('Subsidiary').'</td><td colspan="3">';
	print $objectsu->select_subsidiary($object->fk_subsidiaryid,'fk_subsidiaryid',1,0,1);
	print '</td></tr>';

	// series
	print '<tr><td width="25%">'.$langs->trans('Serie').'</td><td colspan="3">';
	print '<input type="text" name="series" value="'.$object->series.'" maxlength="4" size="3">';
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($_GET["id"])
	{
		dol_htmloutput_mesg($mesg);

		$object = new Entrepotbanksoc($db);
		//$objectUr = new Entrepotrelation($db);
		$result = $object->fetch($_GET["id"]);

		if ($result < 0)
		{
			dol_print_error($db);
		}

		/*
		 * Affichage fiche
		 */
		if ($action <> 'edit' && $action <> 're-edit')
		{
		  //$head = cajachica_prepare_head($object);

			dol_fiche_head($head, 'card', $langs->trans("Permissions"), 0, 'stock');
			// Confirm delete third party
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("DeletePermission"),$langs->trans("ConfirmDeletePermission",$object->ref),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			print '<table class="border" width="100%">';

			if (!$modPermission)
			{
				// IP
				$ip = $objfact->verificaIP();
				print '<tr><td width="25%">'.$langs->trans("IP").'</td><td colspan="3">'.$object->numero_ip.'</td></tr>';
			}
			else
			{
				print '<tr><td width="25%">'.$langs->trans("User").'</td><td colspan="3">';
				$objuser->fetch($object->fk_user);
				print $objuser->lastname.' '.$objuser->firstname;
				print '</td></tr>';
			}

			// Ref
			print '<tr><td width="25%">'.$langs->trans("IP").'</td><td colspan="3">';
			//			print $form->showrefnav($object,'id','',1,'rowid','cod_caja');
			print $object->numero_ip;

			print '</td>';

			//Societe
			$objSociete = new Societe($db);
			$objSociete->fetch($object->fk_socid);
			print '<tr><td valign="top">'.$langs->trans("Societe").'</td><td colspan="3">'.$objSociete->name.'</td></tr>';

			//Entrepot
			$objEntrepot = new Entrepot($db);
			$objEntrepot->fetch($object->fk_entrepotid);
			print '<tr><td valign="top">'.$langs->trans("Entrepot").'</td><td colspan="3">'.$objEntrepot->libelle.' - '.$objEntrepot->lieu.'</td></tr>';

			//Caja
			$objBank = new Account($db);
			$objBank->fetch($object->fk_cajaid);
			print '<tr><td valign="top">'.$langs->trans("Caja").'</td><td colspan="3">'.$objBank->label.'</td></tr>';

			// subsidiary
			print '<tr><td width="25%">'.$langs->trans('Subsidiary').'</td><td colspan="3">';
			$objectsu->fetch($object->fk_subsidiaryid);
			if ($objectsu->id == $object->fk_subsidiaryid)
				print $objectsu->label.' ('.$objectsu->ref.')';
			else
				print '&nbsp;';
			print '</td></tr>';

			// series
			print '<tr><td width="25%">'.$langs->trans('Serie').'</td><td colspan="3">';
			print $object->series;
			print '</td></tr>';

			print "</table>";

			print '</div>';


			/* ************************************************************************** */
			/*                                                                            */
			/* Barre d'action                                                             */
			/*                                                                            */
			/* ************************************************************************** */

			print "<div class=\"tabsAction\">\n";

			if ($action == '')
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

			print "</div>";
			/*
			//muestra los usuarios para la caja
			$caneditUser = true;
			if ($caneditUser)
			  {
				$form = new Form($db);
				print '<form action="'.$_SERVER['PHP_SELF'].'?id='.$_GET['id'].'" method="POST">'."\n";
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
				print '<input type="hidden" name="action" value="adduser" />';
				print '<input type="hidden" name="fk_cajachica" value="'.$object->id.'" />';
				print '<table class="noborder" width="100%">'."\n";
				print '<tr class="liste_titre"><th class="liste_titre" width="25%">'.$langs->trans("UsersToAdd").'</th>'."\n";
				print '<th>';
				print $form->select_users('', 'fk_user', 1, $exclude, 0, '', '', $object->entity);
				print ' &nbsp; ';
				// Multicompany
				if (! empty($conf->multicompany->enabled))
				  {
				if ($conf->entity == 1 && $conf->multicompany->transverse_mode)
				  {
					print '</td><td valign="top">'.$langs->trans("Entity").'</td>';
					print "<td>".$mc->select_entities($conf->entity);
				  }
				else
				  {
					print '<input type="hidden" name="entity" value="'.$conf->entity.'" />';
				  }
				  }
				else
				  {
				print '<input type="hidden" name="entity" value="'.$conf->entity.'" />';
				  }
				print '<input type="submit" class="button" value="'.$langs->trans("Add").'" />';
				print '</th></tr>'."\n";
				print '</table></form>'."\n";

				print '<br>';
			  }
			*/
			  /* ************************************************************************** */
			  /*                                                                            */
			  /* Affichage de la liste des produits de l'entrepot                           */
			  /*                                                                            */
			  /* ************************************************************************** */
			/*
			print '<br>';

			print '<table class="noborder" width="100%">';
			print "<tr class=\"liste_titre\">";
			print_liste_field_titre($langs->trans("User"),"", "u.login","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			// if ($user->rights->stock->mouvement->creer) print '<td>&nbsp;</td>';
			// if ($user->rights->stock->creer)            print '<td>&nbsp;</td>';
			print "</tr>";

			$totalunit=0;
			$totalvalue=$totalvaluesell=0;

			$sql = "SELECT ccu.rowid as rowid, u.login";
			$sql.= " FROM ".MAIN_DB_PREFIX."cajachicauser ccu, ".MAIN_DB_PREFIX."user u";
			$sql.= " WHERE ccu.fk_cajachica = u.rowid";
			$sql.= " AND ccu.rowid = '".$_GET['id']."'";
			$sql.= $db->order($sortfield,$sortorder);

			dol_syslog('List products sql='.$sql);
			$resql = $db->query($sql);
			if ($resql)
			{
				$num = $db->num_rows($resql);
				$i = 0;
				$var=True;
				while ($i < $num)
				{
					$objp = $db->fetch_object($resql);

					$var=!$var;
					//print '<td>'.dol_print_date($objp->datem).'</td>';
					print "<tr ".$bc[$var].">";
					print '<td>'.$objp->login.'</td>';
					print "</tr>";
					$i++;
				}
				$db->free($resql);


			}
			else
			{
				dol_print_error($db);
			}
			print "</table>\n";
			*/
		}

		/*
		 * Edition fiche
		 */
		if (($action == 'edit' || $action == 're-edit') && 1)
		{
			print_fiche_titre($langs->trans("Edit"));

			print '<form action="fiche.php" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';

			print '<table class="border" width="100%">';

			if (!$modPermission)
			{
				// IP
				$ip = $objfact->verificaIP();
				print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("IP").'</td><td colspan="3"><input name="ref" size="20" value="'.$object->numero_ip.'"></td></tr>';
			}
			else
			{
				print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("User").'</td><td colspan="3">';
				print $form->select_users($object->fk_user,'userid',1);
				print '</td></tr>';
			}

			//societe
			print '<tr><td width="20%">'.$langs->trans("Societe").'</td><td colspan="3">';
			print $form->select_company($object->fk_socid,'fk_socid');
			print '</td></tr>';

			// Entrepot Almacen
			print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Entrepot').'</td><td colspan="3">';
			print select_entrepot($object->fk_entrepotid,'fk_entrepotid',1);
			//			print $objectUr->select_padre($object->fk_entrepotid,'fk_entrepotid',1);
			print '</td></tr>';

			// Caja
			print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Caja").'</td><td colspan="3">';

			print $form->select_comptes($object->fk_cajaid,'fk_cajaid',0,"courant=2",($defaultknown?0:2));
			print '</td>';
			print '</tr>';
			// subsidiary
			print '<tr><td width="25%">'.$langs->trans('Subsidiary').'</td><td colspan="3">';
			print $objectsu->select_subsidiary($object->fk_subsidiaryid,'fk_subsidiaryid',1,0,1);
			print '</td></tr>';

			// series
			print '<tr><td width="25%">'.$langs->trans('Serie').'</td><td colspan="3">';
			print '<input type="text" name="series" value="'.$object->series.'" maxlength="4" size="3">';
			print '</td></tr>';

			print '</table>';

			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
			print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

			print '</form>';

		}
	}
}


llxFooter();

$db->close();
?>
