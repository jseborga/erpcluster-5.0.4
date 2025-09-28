<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/mant/program/fiche.php
 *	\ingroup    Mantenimiento
 *	\brief      Page fiche add programming
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once(DOL_DOCUMENT_ROOT."/mant/class/mantprogramming.class.php");
if ($conf->assets->enabled)
	require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/user.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/request/class/mworkrequest.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobs.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobscontact.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsorder.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsuser.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mproperty.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mlocation.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/charge/class/pcharge.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/departament/class/pdepartament.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mequipmentext.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

// require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/mant/lib/adherent.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/lib/societe.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/lib/user.lib.php';

// require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';


//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

// require_once DOL_DOCUMENT_ROOT.'/core/lib/emailing.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
// require_once DOL_DOCUMENT_ROOT.'/comm/mailing/class/mailing.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

$langs->load("assets");
$langs->load("companies");
$langs->load("commercial");
$langs->load("bills");
$langs->load("banks");
$langs->load("users");
$langs->load("other");
$langs->load("mant");

$action=GETPOST('action');

$id        = GETPOST("id");
$ref       = GETPOST('ref','alpha');
if (! empty($user->societe_id)) $socid=$user->societe_id;
$url = $dolibarr_main_url_root;

// $sortfield = GETPOST("sortfield");
// $sortorder = GETPOST("sortorder");

// if (! $sortfield) $sortfield="p.period_month";
// if (! $sortorder) $sortorder="DESC";

$mesg = '';

$object      = new Mantprogramming($db);
if ($conf->assets->enabled) $objass      = new Assetsext($db);
$objSoc = new Societe($db);
$objadh = new Adherent($db);
$objcontact = new Contact($db);
$objUser = new User($db);
$objEquipment = new Mequipment($db);

$aMonth = monthArray($langs,0);
/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->mant->prog->crear)
{
	$error=0;
	$date_ini = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$detailvalue = '';
	$object->fk_asset    = GETPOST('fk_asset');
	$object->fk_equipment    = GETPOST('fk_equipment');
	//verificamos que no exista
	$filter = " AND t.entity = ".$conf->entity;
	$filter.= " AND t.fk_equipment = ".GETPOST('fk_equipment');
	$filter.= " AND t.typemant = '".GETPOST('typemant')."'";
	$filter.= " AND t.frequency = '".GETPOST('frequency')."'";
	$objecttmp = new Mantprogramming($db);
	$res = $objecttmp->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
	if ($res == 0)
	{
		if (empty($object->fk_asset)) $object->fk_asset = 0;
		else
		{
			if ($conf->global->MANT_EQUIPMENT_INTEGRATED_WITH_ASSET)
			{
			//vamos a buscar al activo
				$res = $objEquipment->fetch(0,null,$object->fk_asset);
				if ($res >0)
					$object->fk_equipment = $objEquipment->id;
			}
		}
		$object->typemant    = GETPOST('typemant');
		$object->frequency   = GETPOST('frequency');
		$adetail             = GETPOST('detail_value');
		$object->fk_soc      = GETPOST('fk_soc');
		if (empty($object->fk_soc)) $object->fk_soc = 0;
		$object->fk_member   = GETPOST('fk_member');
		if (empty($object->fk_member)) $object->fk_member = 0;
		$object->description = GETPOST('description');
		$object->internal    = GETPOST('internal');
		$object->speciality = GETPOST('speciality');
		$object->date_ini    = $date_ini;

		if ($object->fk_asset<=0 && $object->fk_equipment <=0)
		{
			$error++;
			setEventMessages($langs->trans('Seleccione un activo o un equipo'),null,'errors');
		}
		if ($object->frequency == 'BYMONTHLY' || $object->frequency == 'SEMIANNUAL' || $object->frequency == 'QUARTERLY'  || $object->frequency == 'WEEKLY')
		{
			foreach ((array) $adetail AS $j => $value)
			{
				if (!empty($detailvalue)) $detailvalue.= ',';
				$detailvalue.=$value;
			}
		}
		elseif ($object->frequency == 'ANNUAL' || $object->frequency == 'MONTHLY' || $object->frequency == 'DAILY')
			$detailvalue = $adetail;
		$object->detail_value   = $detailvalue;
		$object->entity         = $conf->entity;
		$object->date_create    = dol_now();
		$object->fk_user_create = $user->id;
		$object->fk_user_mod = $user->id;
		$object->datec = dol_now();
		$object->datem = dol_now();
		$object->statut         = 0;
		$object->active         = 0;

		$object->tms            = dol_now();
	//validacion

		if (empty($object->typemant) || $object->typemant == -1)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Error, typemant is required').'</div>';
		}
		if (empty($object->frequency) || $object->frequency == -1)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Error, frequency is required').'</div>';
		}

		if (empty($error))
		{
			echo $id = $object->create($user);
			if ($id > 0)
			{
				header("Location: fiche.php?id=".$id);
				exit;
			}
			setEventMessages($object->error,$object->errors,'errors');
			$action = 'create';
		}
		else
		{
			$action="create";
		}
	}
	else
	{
		setEventMessages($langs->trans('Thereisregistration'),null,'errors');
		$action = 'create';
	}
}

// Update
if ($action == 'update' && ($user->rights->mant->prog->crear || $user->rights->mant->prog->mod))
{
	$error=0;
	$detailvalue = '';
	if ($object->fetch($_REQUEST['id']) > 0)
	{
		$date_ini = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
		$object->fk_asset    = GETPOST('fk_asset');
		$object->typemant    = GETPOST('typemant');
		$object->frequency   = GETPOST('frequency');
		$object->fk_soc      = GETPOST('fk_soc');
		$object->fk_member   = GETPOST('fk_member');
		$adetail             = GETPOST('detail_value');
		$object->description = GETPOST('description');
		$object->internal    = GETPOST('internal');
		$object->speciality  = GETPOST('speciality');
		$object->date_ini    = $date_ini;

		if ($object->frequency == 'BYMONTHLY' ||
			$object->frequency == 'SEMIANNUAL' ||
			$object->frequency == 'QUARTERLY'  ||
			$object->frequency == 'WEEKLY')
		{
			foreach ((array) $adetail AS $j => $value)
			{
				if (!empty($detailvalue)) $detailvalue.= ',';
				$detailvalue.=$value;
			}
		}
		elseif ($object->frequency == 'ANNUAL' ||
			$object->frequency == 'MONTHLY' ||
			$object->frequency == 'DAILY')
			$detailvalue = $adetail;

		$object->detail_value = $detailvalue;
		$object->tms          = date('YmdHis');
	//validacion
		if ($object->fk_asset <=0)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Error, asset is required').'</div>';
		}
		if (empty($object->typemant) || $object->typemant == -1)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Error, typemant is required').'</div>';
		}
		if (empty($object->frequency) || $object->frequency == -1)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Error, frequency is required').'</div>';
		}

		if (empty($error))
		{
			$res = $object->update($user);
			if ($res > 0)
			{
				header("Location: fiche.php?id=".$id);
				exit;
			}
			$action = 'edit';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		else
		{
		$action="edit";   // Force retour sur page creation
	}
}
}

/*
 * Confirmation de la re validation
 */
if ($action == 'revalidate')
{
	if ($object->fetch(GETPOST('id'))>0)
	{
	//cambiando a revalidado
		$object->active = 0;
		$object->statut = 0;
	//update
		$object->update($user);
		header("Location: fiche.php?id=".$id);
	}
}

// confirm validate
if ($action == 'confirm_validate' && $_REQUEST["confirm"] == 'yes' && $user->rights->mant->prog->val)
{
	if ($object->fetch($_REQUEST["id"])>0)
	{
		$object->statut = 1;
		$result=$object->update($user);
		if ($result > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			$mesg='<div class="error">'.$object->error.'</div>';
			$action='';
		}
	}
}

// confirm activate
if ($action == 'confirm_activate' && $_REQUEST["confirm"] == 'yes' && $user->rights->mant->prog->val)
{
	if ($object->fetch($_REQUEST["id"])>0)
	{
		$object->active = 1;
		$result=$object->update($user);
		if ($result > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			$mesg='<div class="error">'.$object->error.'</div>';
			$action='';
		}
	}
}

// Confirm delete
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->mant->prog->del)
{
	if ($object->fetch($_REQUEST["id"])>0)
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			header("Location: ".DOL_URL_ROOT.'/mant/program/liste.php');
			exit;
		}
		else
		{
			$mesg='<div class="error">'.$object->error.'</div>';
			$action='';
		}
	}
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

if ( ($action == 'createnew') )
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	//$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
	$date_ini = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

	$tmparray['frequency'] = GETPOST('frequency');
	$tmparray['fk_asset'] = GETPOST('fk_asset');
	$tmparray['fk_equipment'] = GETPOST('fk_equipment');
	$tmparray['typemant'] = GETPOST('typemant');
	$tmparray['description'] = GETPOST('description');
	$tmparray['date_ini'] = $date_ini;
	$tmparray['detail_value'] = GETPOST('detail_value');
	$tmparray['fk_soc'] = GETPOST('fk_soc');

	//if (! empty($tmparray['frequency']))
	//{
	$object->frequency = $tmparray['frequency'];
	$object->fk_asset = $tmparray['fk_asset'];
	$object->fk_equipment = $tmparray['fk_equipment'];
	$object->typemant = $tmparray['typemant'];
	$object->description = $tmparray['description'];
	$object->date_ini = $tmparray['date_ini'];
	$object->detail_value = $tmparray['detail_value'];

	$object->fk_soc = $tmparray['fk_soc'];

	$action='create';
	//}
	$action='create';
}


/*
 * View
 */

$form=new Formv($db);

$aArrjs = array();
$help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
$aArrcss = array('/mant/css/style-desktop.css');
$conf->dol_hide_leftmenu = 0;
llxHeader("",$langs->trans("Managementmant"),$help_url,'','','',$aArrjs,$aArrcss);
//create
if ($action == 'create' && $user->rights->mant->prog->crear)
{
	print_fiche_titre($langs->trans("Newprogramming"));

	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
		$("#frequency").change(function() {
			document.form_index.action.value="createnew";
			document.form_index.submit();
		});
		$("#fk_soc").change(function() {
			document.form_index.action.value="createnew";
			document.form_index.submit();
		});
		$("#fk_equipment").change(function() {
			document.form_index.action.value="createnew";
			document.form_index.submit();
		});
		$("#fk_asset").change(function() {
			document.form_index.action.value="createnew";
			document.form_index.submit();
		});

	});';
	print '</script>'."\n";

	print '<form action="fiche.php" method="post" name="form_index">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	if ($conf->global->MANT_INTEGRATED_WITH_ASSET && $conf->assets->enabled)
	{
		// asset
		print '<tr><td class="fieldrequired" for="selectfk_asset" width="20%">'.$langs->trans('Asset').'</td><td colspan="2">';
		//print $objass->select_assets($object->fk_asset,'fk_asset','',40,1,0,1,'','');
		print $form->select_asset($object->fk_asset,'fk_asset','',0,0,1,2,'',1,array(),0,'','',0);
		print '</td></tr>';
	}
	else
	{
	// equipment
		print '<tr><td class="fieldrequired" for="selectfk_asset" width="20%">'.$langs->trans('Equipment').'</td><td colspan="2">';
	//print $objass->select_assets($object->fk_asset,'fk_asset','',40,1,0,1,'','');
		print $form->select_equipment($object->fk_equipment,'fk_equipment','',0,0,'1,9',2,'',1,array(),0,'','',0);
		print '</td></tr>';

	}
	// typemant
	print '<tr><td class="fieldrequired">'.$langs->trans('Typemant').'</td><td colspan="2">';
	print select_typemant($object->typemant,'typemant','',1,0,1);
	print '</td></tr>';

	// frequency
	print '<tr><td class="fieldrequired">'.$langs->trans('Frequency').'</td><td colspan="2">';
	print select_frequency($object->frequency,'frequency','required',1,0,1);
	print '</td></tr>';

	// // detail_value
	// print '<tr><td  class="fieldrequired" width="20%">'.$langs->trans('Detail value').'</td><td colspan="2">';
	// print '<input id="detail_value" type="text" value="'.$object->detail_value.'" required name="detail_value" size="30">';
	// print '</td></tr>';

	// detail_value
	print '<tr><td  class="fieldrequired" width="20%">'.$langs->trans('Select').'</td><td colspan="2">';
	if ($object->frequency == 'BYMONTHLY' || $object->frequency == 'SEMIANNUAL' || $object->frequency == 'QUARTERLY')
	{
		foreach((array) $object->detail_value AS $k => $value)
			$aDetail[$value] = $value;
		print select_month($aDetail,'detail_value','multiple',30,0,0);
		//print $form->multiselectarray('detail_value', $aMonth, 0, 0, $morecss='', $translate=0, $width=230, $moreattrib='',$elemtype='');
	}
	elseif ($object->frequency == 'ANNUAL')
		print select_month($object->detail_value,'detail_value','',30,0,0);
	elseif($object->frequency == 'WEEKLY')
	{
		if (!empty($object->detail_value) && !is_array($object->detail_value))
			$aDetail = explode(',',$object->detail_value);
		print select_days($aDetail,'detail_value','multiple',30,0,0);
	}
	elseif($object->frequency == 'MONTHLY')
		print select_days($object->detail_value,'detail_value','',30,0,0);
	else
	{
		print $langs->trans('Daily');
		print '<input id="detail_value" type="hidden" value="1" name="detail_value">';
	}
	print '</td></tr>';

	//societe
	print '<tr><td>'.$langs->trans('Company').'</td><td colspan="2">';
	print $form->select_company($object->fk_soc,'fk_soc','',1);
	print '</td></tr>';

	if (!empty($object->fk_soc))
	{
		$objSoc->fetch($object->fk_soc);
		if ($objSoc->id == $object->fk_soc)
		{
			if ($objSoc->typent_code == 'TE_BCB' )
		//asignacion interna
			{
				print '<tr><td>'.$langs->trans('User').'</td><td colspan="2">';
				$aArrayMember = list_user_member('fk_member > 0');
				print $form->select_users($iduser,'fk_member',1,'',0,$aArrayMember);
			}
			else
			{
		//asignacion externa
				print '<tr><td>'.$langs->trans('Contact').'</td><td colspan="2">';
				$aContact = $objSoc->contact_array();

				$aArrayMember = list_user_member('fk_member > 0');
				print $form->selectarray('fk_member',$aContact,GETPOST('fk_member'),1);
			}
		}
	}
	print '</td></tr>';

	//  internal
	print '<tr><td width="20%">'.$langs->trans('Internal').'</td><td colspan="2">';
	print '<input id="internal" type="text" value="'.$object->internal.'" name="internal" size="5">';
	print '</td></tr>';

	// Especiality
	print '<tr><td>'.$langs->trans('Speciality').'</td><td colspan="2">';
	print select_speciality($object->speciality,'speciality','',1);
	print '</td></tr>';

	//descripcion
	print '<tr><td class="fieldrequired">'.$langs->trans('Description').'</td><td colspan="2">';
	print '<textarea name="description" cols="80" rows="5" required>'.$object->description.'</textarea>';
	print '</td></tr>';

	// date_ini
	print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
	$form->select_date($object->date_ini,'di_','','','',"date",1,1);
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($id || $_GET['id'])
	{
		dol_htmloutput_mesg($mesg);
		$result = $object->fetch($id);
		if ($result < 0)
		{
			dol_print_error($db);
		}

		if ( ($action == 'editmod') )
		{
			require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
			$date_ini = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

			$tmparray['frequency'] = GETPOST('frequency');
			$tmparray['fk_asset'] = GETPOST('fk_asset');
			$tmparray['typemant'] = GETPOST('typemant');
			$tmparray['description'] = GETPOST('description');
			$tmparray['detail_value'] = GETPOST('detail_value');

			$tmparray['date_ini'] = $date_ini;
			$tmparray['fk_soc'] = GETPOST('fk_soc');

			if (! empty($tmparray['frequency']))
			{
				$object->frequency = $tmparray['frequency'];
				$object->fk_asset = $tmparray['fk_asset'];
				$object->typemant = $tmparray['typemant'];
				$object->description = $tmparray['description'];
				$object->detail_value = $tmparray['detail_value'];
				$object->date_ini = $tmparray['date_ini'];
				$object->fk_soc = $tmparray['fk_soc'];
				$action='edit';
			}
		}

	 /*
	  * Affichage fiche
	  */
	 if ($action <> 'edit' && $action <> 're-edit')
	 {
		 //$head = fabrication_prepare_head($object);

	 	dol_fiche_head($head, 'card', $langs->trans("Ticket"), 0, 'mant');


		 // Confirm delete
	 	if ($action == 'delete')
	 	{
	 		$form = new Form($db);
	 		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteprogramming"),$langs->trans("Confirmdeleteprogramming"),"confirm_delete",'',0,2);
	 		if ($ret == 'html') print '<br>';
	 	}
		 // Confirm validate
	 	if ($action == 'validate')
	 	{
	 		$form = new Form($db);
	 		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Validateprogramming"),$langs->trans("Confirmvalidateprogramming"),"confirm_validate",'',0,2);
	 		if ($ret == 'html') print '<br>';
	 	}
		 // Confirm activate
	 	if ($action == 'activate')
	 	{
	 		$form = new Form($db);
	 		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Activateprogramming"),$langs->trans("Confirmactivateprogramming"),"confirm_activate",'',0,2);
	 		if ($ret == 'html') print '<br>';
	 	}

	 	print '<table class="border" width="100%">';

		 // asset
	 	if ($object->fk_asset>0 && $conf->assets->enabled)
	 	{
	 		print '<tr><td width="20%">'.$langs->trans('Asset').'</td><td colspan="2">';
	 		if ($objass->fetch($object->fk_asset)>0)
	 			print $objass->getNomUrl().' - '.$objass->descrip;
	 		else
	 			print '&nbsp;';
	 		print '</td></tr>';
	 	}
		 // equipment
	 	if ($object->fk_equipment>0)
	 	{
	 		print '<tr><td width="20%">'.$langs->trans('Equipment').'</td><td colspan="2">';
	 		if ($objEquipment->fetch($object->fk_equipment)>0)
	 			print $objEquipment->getNomUrl().' - '.$objEquipment->label;
	 		else
	 			print '&nbsp;';
	 		print '</td></tr>';
	 	}

		 // typemant
	 	print '<tr><td>'.$langs->trans('Typemant').'</td><td colspan="2">';
	 	print select_typemant($object->typemant,'typemant','required',1,1);
	 	print '</td></tr>';

		 // frequency
	 	print '<tr><td>'.$langs->trans('Frequency').'</td><td colspan="2">';
	 	print select_frequency($object->frequency,'frequency','required',1,1);
	 	print '</td></tr>';

		 // detail_value
	 	if (!empty($object->detail_value))
	 	{
	 		print '<tr><td width="20%">'.$langs->trans('Detail value').'</td><td colspan="2">';
	 		print $object->detail_value;
	 		print '</td></tr>';
	 	}

		 //societe
	 	print '<tr><td>'.$langs->trans('Company').'</td><td colspan="2">';
	 	$objSoc->fetch($object->fk_soc);
	 	if ($objSoc->id == $object->fk_soc)
	 		print $objSoc->nom;
	 	else
	 		print '&nbsp;';
	 	print '</td></tr>';

	 	if ($objSoc->id == $object->fk_soc)
	 	{
		 if ($objSoc->typent_code == 'TE_BCB' ) //asignacion interna
		 {
		 	print '<tr><td>'.$langs->trans('User').'</td><td colspan="2">';
		 	$objUser->fetch($object->fk_member);
		 	if ($objUser->id == $object->fk_member)
		 		print $objUser->lastname.' '.$objUser->firstname;
		 	else
		 		print '&nbsp;';
		 }
		 else
		 {
			 //asignacion externa
		 	print '<tr><td>'.$langs->trans('Contact').'</td><td colspan="2">';
		 	$objcontact->fetch($object->fk_member);
		 	if ($objcontact->id == $object->fk_member)
		 		print $objcontact->lastname.' '.$objcontact->firstname;
		 	else
		 		print '&nbsp;';
		 }
		}
		print '</td></tr>';

		 //  internal
		print '<tr><td width="20%">'.$langs->trans('Internal').'</td><td colspan="2">';
		print $object->internal;
		print '</td></tr>';

		 // Especiality
		print '<tr><td>'.$langs->trans('Speciality').'</td><td colspan="2">';
		print select_speciality($object->speciality,'speciality','',0,1);
		print '</td></tr>';

		 //descripcion
		print '<tr><td>'.$langs->trans('Description').'</td><td colspan="2">';
		print $object->description;
		print '</td></tr>';

		 // date_ini
		print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
		print dol_print_date($object->date_ini,'day');
		print '</td></tr>';

		 // date_last
		print '<tr><td>'.$langs->trans('Last ejecution').'</td><td colspan="2">';
		print dol_print_date($object->date_last,'day');
		print '</td></tr>';

		 // date_ini
		print '<tr><td>'.$langs->trans('Next ejecution').'</td><td colspan="2">';
		print dol_print_date($object->date_next,'day');
		print '</td></tr>';

		print '<tr><td>'.$langs->trans('Active').'</td><td colspan="2">';
		print ($object->active?img_picto($langs->trans('Active'),'switch_on'):img_picto($langs->trans('Active'),'switch_off'));
		print '</td></tr>';

		 // Statut
		print '<tr><td>'.$langs->trans("Status").'</td><td colspan="2">'.$object->getLibStatut(1).'</td></tr>';

		print "</table>";

		print '</div>';


		/* ****************************************** */
		/*                                            */
		/* Barre d'action                             */
		/*                                            */
		/* ****************************************** */

		print "<div class=\"tabsAction\">\n";

		if ($action == '')
		{
			if ($user->rights->mant->prog->crear && $object->statut == 0)
				print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

			if (($object->statut==0 ) && $user->rights->mant->prog->del)
				print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
		 // Valid
			if ($object->statut == 0 && $user->rights->mant->prog->val)
			{
				print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a>';
			}
		 // Notvalidate
			if ($object->statut == 1 && $user->rights->mant->prog->val)
			{
				print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=revalidate">'.$langs->trans('Notvalidate').'</a>';
			}
		 // Activate
			if ($object->statut == 1 && $object->active == 0 &&$user->rights->mant->prog->val)
			{
				print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=activate">'.$langs->trans('Activate').'</a>';
			}

		}

		print '</div>';

	}


	 /*
	  * Edition fiche
	  */
	 if (($action == 'edit' || $action == 're-edit') && 1)
	 {
	 	print_fiche_titre($langs->trans("Editprogramming"), $mesg);

	 	print "\n".'<script type="text/javascript" language="javascript">';
	 	print '$(document).ready(function () {
	 		$("#frequency").change(function() {
	 			document.form_index.action.value="editmod";
	 			document.form_index.submit();
	 		});
	 	});';
	 	print '</script>'."\n";

	 	print "\n".'<script type="text/javascript" language="javascript">';
	 	print '$(document).ready(function () {
	 		$("#fk_soc").change(function() {
	 			document.form_index.action.value="editmod";
	 			document.form_index.submit();
	 		});
	 	});';
	 	print '</script>'."\n";

	 	print '<form action="fiche.php" method="post" name="form_index">';
	 	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	 	print '<input type="hidden" name="action" value="update">';
	 	print '<input type="hidden" name="id" value="'.$id.'">';

	 	print '<table class="border" width="100%">';

	 	if ($conf->global->MANT_INTEGRATED_WITH_ASSET && $conf->assets->enabled)
	 	{
		// asset
	 		print '<tr><td class="fieldrequired" for="selectfk_asset" width="20%">'.$langs->trans('Asset').'</td><td colspan="2">';
		//print $objass->select_assets($object->fk_asset,'fk_asset','',40,1,0,1,'','');
	 		print $form->select_asset($object->fk_asset,'fk_asset','',0,0,1,2,'',1,array(),0,'','',0);
	 		print '</td></tr>';
	 	}
	 	else
	 	{
			// equipment
	 		print '<tr><td class="fieldrequired" for="selectfk_asset" width="20%">'.$langs->trans('Equipment').'</td><td colspan="2">';
			//print $objass->select_assets($object->fk_asset,'fk_asset','',40,1,0,1,'','');
	 		print $form->select_equipment($object->fk_equipment,'fk_equipment','',0,0,'1,9',2,'',1,array(),0,'','',0);
	 		print '</td></tr>';

	 	}

	 	print '</td></tr>';

		 // typemant
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Typemant').'</td><td colspan="2">';
	 	print select_typemant($object->typemant,'typemant','required',1,0,1);
	 	print '</td></tr>';

		 // frequency
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Frequency').'</td><td colspan="2">';
	 	print select_frequency($object->frequency,'frequency','required',1,0,1);
	 	print '</td></tr>';


		 // detail_value
	 	print '<tr><td  class="fieldrequired" width="20%">'.$langs->trans('Detail value').'</td><td colspan="2">';
	 	if ($object->frequency == 'BYMONTHLY' ||
	 		$object->frequency == 'SEMIANNUAL' ||
	 		$object->frequency == 'QUARTERLY')
	 	{
	 		foreach((array) $object->detail_value AS $k => $value)
	 			$aDetail[$value] = $value;
	 		print select_month($aDetail,'detail_value','multiple',30,0,0);
	 	}
	 	elseif ($object->frequency == 'ANNUAL')
	 		print select_month($object->detail_value,'detail_value','',30,0,0);
	 	elseif($object->frequency == 'WEEKLY')
	 	{
	 		foreach((array) $object->detail_value AS $k => $value)
	 			$aDetail[$value] = $value;
	 		print select_days($aDetail,'detail_value','multiple',30,0,0);
	 	}
	 	elseif($object->frequency == 'MONTHLY')
	 		print select_days($object->detail_value,'detail_value','',30,0,0);
	 	else
	 	{
	 		print $langs->trans('Daily');
	 		print '<input id="detail_value" type="hidden" value="1" name="detail_value">';
	 	}


		 // if ($object->frequency == 'BYMONTHLY' ||
		 // 	 $object->frequency == 'SEMIANNUAL' ||
		 // 	 $object->frequency == 'QUARTERLY' ||
		 // 	 $object->frequency == 'WEEKLY')
		 //   {
		 // 	 $aDetail = explode(',',$object->detail_value);
		 // 	 foreach((array) $aDetail AS $k => $value)
		 // 	   $aNewDetail[$value] = $value;
		 // 	 print select_month($aNewDetail,'detail_value','multiple',30,0,0);
		 //   }
		 // elseif ($object->frequency == 'ANNUAL' ||
		 // 	     $object->frequency == 'MONTHLY')
		 //   print select_month($object->detail_value,'detail_value','',30,0,0);
		 // else
		 //   {
		 // 	 print $langs->trans('Daily');
		 // 	 print '<input id="detail_value" type="hidden" value="DAILY" name="detail_value">';
		 //   }
	 	print '</td></tr>';

		 //societe
	 	print '<tr><td>'.$langs->trans('Company').'</td><td colspan="2">';
	 	print $form->select_company($object->fk_soc,'fk_soc','',1);
	 	print '</td></tr>';

	 	if (!empty($object->fk_soc))
	 	{
	 		$objSoc->fetch($object->fk_soc);
	 		if ($objSoc->id == $object->fk_soc)
	 		{
			 if ($objSoc->typent_code == 'TE_BCB' ) //asignacion interna
			 {
			 	print '<tr><td>'.$langs->trans('User').'</td><td colspan="2">';
			 //$aArrayMember = list_user_member('fk_member > 0',$object->fk_soc);
			 	$aExclued = list_user_member('',$object->fk_soc,'E');
			 	print $form->select_users($object->fk_member,'fk_member',1,$aExclued,0,$aArrayMember);
			 }
			 else
			 {
			 //asignacion externa
			 	print '<tr><td>'.$langs->trans('Contact').'</td><td colspan="2">';
			 	$aContact = $objSoc->contact_array();

			 	$aArrayMember = list_user_member('fk_member > 0');
			 	print $form->selectarray('fk_member',$aContact,$object->fk_member,1);
			 }
			}
		}
		print '</td></tr>';

		 //  internal
		print '<tr><td width="20%">'.$langs->trans('Internal').'</td><td colspan="2">';
		print '<input id="internal" type="text" value="'.$object->internal.'" name="internal" size="5">';
		print '</td></tr>';

		 // Especiality
		print '<tr><td>'.$langs->trans('Speciality').'</td><td colspan="2">';
		print select_speciality($object->speciality,'speciality','',1);
		print '</td></tr>';

		 //descripcion
		print '<tr><td>'.$langs->trans('Description').'</td><td colspan="2">';
		print '<textarea name="description" cols="80" rows="5">'.$object->description.'</textarea>';
		print '</td></tr>';

		 // date_ini
		print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
		$form->select_date($object->date_ini,'di_','','','',"date",1,1);
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
