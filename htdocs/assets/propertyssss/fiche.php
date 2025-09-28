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
 *	\file       htdocs/mant/charge/fiche.php
 *	\ingroup    Charges
 *	\brief      Page fiche mant charges
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/property/class/mpropertyadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/property/class/mpropertyuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/property/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/lib/assets.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("assets");

$action=GETPOST('action');

$id        = GETPOST("id");

$mesg = '';

$object  = new Mpropertyadd($db);
$objLocation = new Mlocation($db);
/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->assets->teach->creer)
{
	$object->ref     = $_POST["ref"];
	$object->address  = GETPOST('address');
	$object->entity  = $conf->entity;
	$object->fk_country  = GETPOST('country_id');
	$object->fk_state  = GETPOST('state_id');
	$object->fk_user_create = $user->id;
	$object->date_create = dol_now();
	$object->tms = dol_now();
	$object->status = 0;

	if ($object->ref) 
	{
		$id = $object->create($user);
		if ($id > 0)
		{
			header("Location: fiche.php?id=".$id);
			exit;
		}
		$action = 'create';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
		$action="create";   
	// Force retour sur page creation
	}
}


// Delete charge
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->assets->teach->del)
{
	$object->fetch($id);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/assets/property/liste.php');
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}

// Delete charge
if ($action == 'deleteloc' && $user->rights->assets->teach->del)
{
	$objLocation->fetch(GETPOST('idr'));
	echo 'res '.$result=$objLocation->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/assets/property/fiche.php?id='.$id);
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
	if ($object->fetch($_POST["id"]))
	{
		$object->ref          = $_POST["ref"];
		$object->detail  = GETPOST('detail');
		$object->skills  = GETPOST('skills');
		$object->fk_country  = GETPOST('country_id');
		$object->fk_state  = GETPOST('state_id');

		if ( $object->update($user) > 0)
		{
			$action = '';
			$_GET["id"] = $_POST["id"];
			setEventMessages($langs->trans('Saverecord'), null, 'mesgs');
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			setEventMessages($langs->trans('ErrorRecordNotSave'), null, 'errors');
		}
	}
	else
	{
		$action = 'edit';
		$_GET["id"] = $_POST["id"];
		setEventMessages($langs->trans('ErrorRecordNotSave'), null, 'errors');
	}
}


if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

// Add
if ($action == 'addloc' && $user->rights->assets->teach->creer && !empty($id))
{
	$objLocation->fk_property = $id;
	$objLocation->detail      = $_POST["detail"];
	if (!empty($objLocation->detail)) 
	{
		$idloc = $objLocation->create($user);
		if ($idloc <= 0)
		{
			$action = '';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		else
			$action = '';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
		$action="";  
	 // Force retour sur page creation
	}
}

/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);
$formadmin = new FormAdmin($db);
$formcompany = new FormCompany($db);

$help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
llxHeader("",$langs->trans("Property"),$help_url);

if ($action == 'create' && $user->rights->assets->teach->creer)
{
	print_fiche_titre($langs->trans("Newproperty"));

	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			id_te_private=8;
			id_ef15=1;
			$("#selectcountry_id").change(function() {
				document.formsoc.action.value="create";
				document.formsoc.submit();
			});
		});';
		print '</script>'."\n";

	}

	$object->fk_country=GETPOST('country_id')?GETPOST('country_id'):$mysoc->country_id;
	if ($object->fk_country)
	{
		$tmparray=getCountry($object->fk_country,'all');
		$object->country_code=$tmparray['code'];
		$object->country=$tmparray['label'];
	}
	print '<form action="'.$_SERVER["PHP_SELF"].'" method="post" name="formsoc">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

    // ref
	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="50" maxlength="50">';
	print '</td></tr>';

    //address
	print '<tr><td>'.$langs->trans('Address').'</td><td colspan="2">';
	print '<textarea class="flat" name="address" id="address" cols="40" rows="'.ROWS_3.'">';
	print $object->address;
	print '</textarea>';
	print '</td></tr>';

        // Country
	print '<tr><td width="25%">'.fieldLabel('Country','selectcountry_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
	print $form->select_country((GETPOST('country_id')!=''?GETPOST('country_id'):$object->fk_country));
	if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
	print '</td></tr>';

        // State
	if (empty($conf->global->SOCIETE_DISABLE_STATE))
	{
		print '<tr><td>'.fieldLabel('State','state_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
		if ($object->fk_country) print $formcompany->select_state($object->fk_state,$object->country_code);
		else print $countrynotdefined;
		print '</td></tr>';
	}

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
		//armamos los grupos del presupuesto
		$head = assetsprop_prepare_head($object);
		$tab = 'property';
		dol_fiche_head($head, $tab, $langs->trans("Assets"),0,($object->public?'projectpub':'project'));


       // Affichage fiche
		if ($action <> 'edit' && $action <> 're-edit')
		{
	  //$head = fabrication_prepare_head($object);

			//dol_fiche_head($head, 'card', $langs->trans("Charge"), 0, 'mant');

	   // Confirmation de la validation
			if ($action == 'validate')
			{
				$object->fetch(GETPOST('id'));
	      //cambiando a validado
				$object->statut = 1;
	      //update
				$object->update($user);
				$action = '';
			}

	  // Confirm delete third party
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteproperty"),$langs->trans("Confirmdeleteproperty",$object->ref.' '.$object->detail),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			print '<table class="border" width="100%">';

	  // ref
			print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';
			$linkback = '<a href="'.DOL_URL_ROOT.'/assets/property/liste.php">'.$langs->trans("BackToList").'</a>';

			print '<td class="valeur"  colspan="2">';
			print $form->showrefnav($object, 'id', $linkback);
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

			if ($action == '')
			{
				if ($user->rights->assets->teach->creer)
					print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

				if ($user->rights->assets->teach->creer)
					print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if ($user->rights->assets->teach->del)
					print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
			}	  
			print "</div>";		

	  //listando las ubicaciones
			$objLocation->list_location($id);

	  //dol_fiche_head($head, 'card', $langs->trans("Location"), 0, 'mant');
			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
			print_liste_field_titre($langs->trans("Location"));
			print_liste_field_titre($langs->trans("Action"),'','','','','align="center"');
			print '</tr>';

			print '<form action="'.$_SERVER['PHP_SELF'].'?id='.$id.'" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="addloc">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';

	  // location
			print '<tr>';
			print '<td>';
			print '<input id="detail" type="text" value="" name="detail" size="50" maxlength="50">';
			print '</td>';
			print '<td align="right">';
			print '<input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
			print '</td>';
			print '</tr>';
			print '</form>';
			$num = count($objLocation->aArray);
			if ($num)
			{ 

				foreach((array) $objLocation->aArray AS $i => $obj)
				{
					print '<tr><td>';
					print $obj->detail;
					print '</td>';
					print '<td align="right">';
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$obj->id.'&action=deleteloc">'.img_picto($langs->trans('Delete'),'delete').'</a>';
					print '</td>';
					print '</tr>';
				}
			}
			print '</table>';
		}


      /*
       * Edition fiche
       */
      if (($action == 'edit' || $action == 're-edit') && 1)
      {
      	print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);
      	if (! empty($conf->use_javascript_ajax))
      	{
      		print "\n".'<script type="text/javascript">';
      		print '$(document).ready(function () {
      			id_te_private=8;
      			id_ef15=1;
      			$("#selectcountry_id").change(function() {
      				document.formsoc.action.value="create";
      				document.formsoc.submit();
      			});
      		});';
      		print '</script>'."\n";

      	}

      	$object->fk_country=GETPOST('country_id')?GETPOST('country_id'):$mysoc->country_id;
      	if ($object->fk_country)
      	{
      		$tmparray=getCountry($object->fk_country,'all');
      		$object->country_code=$tmparray['code'];
      		$object->country=$tmparray['label'];
      	}

      	print '<form action="fiche.php" method="POST">';
      	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
      	print '<input type="hidden" name="action" value="update">';
      	print '<input type="hidden" name="id" value="'.$object->id.'">';

      	print '<table class="border" width="100%">';

	  // ref
      	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
      	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="50" maxlength="50">';
      	print '</td></tr>';

	  //address
      	print '<tr><td>'.$langs->trans('Details').'</td><td colspan="2">';
      	print '<textarea class="flat" name="adress" id="address" cols="40" rows="'.ROWS_3.'">';
      	print $object->address;
      	print '</textarea>';
      	print '</td></tr>';

	  	//Skills
      	//print '<tr><td>'.$langs->trans('Skills').'</td><td colspan="2">';
      	//print '<textarea class="flat" name="skills" id="skills" cols="40" rows="'.ROWS_3.'">'; 
      	//print $object->skills;
      	//print '</textarea>';
      	//print '</td></tr>';

        // Country
      	print '<tr><td width="25%">'.fieldLabel('Country','selectcountry_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
      	print $form->select_country((GETPOST('country_id')!=''?GETPOST('country_id'):$object->fk_country));
      	if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
      	print '</td></tr>';

        // State
      	if (empty($conf->global->SOCIETE_DISABLE_STATE))
      	{
      		print '<tr><td>'.fieldLabel('State','state_id').'</td><td colspan="3" class="maxwidthonsmartphone">';
      		if ($object->fk_country) print $formcompany->select_state($object->fk_state,$object->country_code);
      		else print $countrynotdefined;
      		print '</td></tr>';
      	}


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
