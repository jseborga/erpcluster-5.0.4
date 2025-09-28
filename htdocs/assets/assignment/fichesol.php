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
 *	\file       htdocs/assets/assignment/fiche.php
 *	\ingroup    Assets
 *	\brief      Page fiche assets assignment 
 */

define("NOLOGIN",1);
define("NOCSRFCHECK",1);

$entity=(! empty($_GET['entity']) ? (int) $_GET['entity'] : (! empty($_POST['entity']) ? (int) $_POST['entity'] : 1));
if (is_int($entity)) define("DOLENTITY", $entity);

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mpropertyuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

require_once DOL_DOCUMENT_ROOT.'/assets/lib/assets.lib.php';
require_once DOL_DOCUMENT_ROOT.'/assets/lib/adherent.lib.php';
require_once DOL_DOCUMENT_ROOT.'/assets/lib/email.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

if ($conf->projet->enabled && $conf->monprojet->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formprojetext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';
}

//para envio correo
require_once DOL_DOCUMENT_ROOT.'/core/lib/emailing.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/mailing/class/mailing.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

$langs->load("assets@assets");

$action = GETPOST('action');
$id     = GETPOST("id");
$idr    = GETPOST("idr");
$code   = GETPOST('code');

if (isset($_GET['tab']) || isset($_POST['tab']))
	$_SESSION['tabasset'] = (!empty($_GET['tab'])?$_GET['tab']:$_POST['tab']);
$tab = $_SESSION['tabasset'];

$mesg = '';

$object = new Assetsassignmentext($db);
$objass  = new Assetsext($db);
$objpro  = new Mproperty($db);
$objprouser = new Mpropertyuser($db);
$objloc  = new Mlocation($db);
$objassigndet = new Assetsassignmentdetext($db);
$objuser = new User($db);
$objadh  = new Adherent($db);
$projet = new Project($db);


$filteruser = '';

if (isset($_SESSION['aTemp'][$id]) && empty($code)) $code = $_SESSION['aTemp'][$id];

if ($id)
{
	$res = $object->fetch($id);
	if ($res<=0) exit;
	if (!empty($code) && $object->mark != $code)
		exit;
	$objuser->fetch($object->fk_user);
	$_SESSION['aTemp'][$id] = $object->mark;
}
else exit;

/*
 * Actions
 */

// confirm no accept
if ($action == 'confirm_novalidate' && $_REQUEST["confirm"] == 'yes')
{
	$error = 0;
	if ($object->fetch($id))
	{
		//iniciamos el guardado
		$db->begin();
			//cambiando a borrador
		$object->status = 0;
		$object->mark = ' ';
		//update
		$result = $object->update($objuser);
		if ($result <= 0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}

		if (empty($error))
		{
			$db->commit();
			unset($_SESSION['aTemp']);
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			$db->rollback();
			$action = '';
		}
	}
}

// confirm aproved
if ($action == 'confirm_validate' && $_REQUEST["confirm"] == 'yes')
{
	$error = 0;
	if ($object->fetch($_REQUEST["id"]))
	{
		//iniciamos el guardado
		$db->begin();
		//cambiando a validado
		$object->status = 1;
		$object->mark = generarcodigo(5);
		//update
		$result = $object->update($objuser);
		if ($result > 0)
		{
			//enviamos correo para aceptacion
			$url = dol_buildpath('/assets/assignment/ficheapp.php?id='.$id,1);
			$url = 'pruebavisual.cluster.com.bo/assets/assignment/ficheapp.php';
			$objuser->fetch($object->fk_user);			
			$object->email = $objuser->email;
			$res = send_email_assignment($object,$url);
			if ($res<0) $error++;
			if (empty($error))
			{
				$db->commit();
				header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
			else
			{
				$db->rollback();
				$mesg='<div class="error">'.$langs->trans('It is not possible to validate this assignment, please try again').'</div>';
				$action = '';
			}
		}
		else
		{
			$db->rollback();
			setEventMessages($object->error,$object->errors,'errors');
			$action='';
		}
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
$form=new Form($db);
if ($conf->monprojet->enabled)
	$formproject = new FormProjetsext($db);


$arrayofcss= array('assets/css/style.css');
llxHeaderVierge($langs->trans("Confirmapproved"), "", 0, 0, $arrayofjs, $arrayofcss);

if ($object->id)
{

	dol_htmloutput_mesg($mesg);

		// Affichage fiche
	if ($action <> 'edit' && $action <> 're-edit')
	{
		dol_fiche_head();
			//approved
		if ($action == 'validate')
		{
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
				$langs->trans("Validateassetassignment"),
				$langs->trans("Confirmvalidateassetassignment"),
				"confirm_validate",
				'',
				0,
				2);
			if ($ret == 'html') print '<br>';
		}
		if ($action == 'novalidate')
		{
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
				$langs->trans("DonotValidateAssetAllocation"),
				$langs->trans("ConfirmDonotValidateAssetAllocation"),
				"confirm_noapproved",
				'',
				0,
				2);
			if ($ret == 'html') print '<br>';
		}

		 	//vista del registro
		print '<table class="border" style="min-width=1000px" width="100%">';

		 	//ref
		print '<tr><td width="15%">'.$langs->trans('Code').'</td><td colspan="2">';
		print $object->ref;    
		print '</td></tr>';

		 	//date assignment
		print '<tr><td width="15%">'.$langs->trans('Date assignment').'</td><td colspan="2">';
		print dol_print_date($object->date_assignment,'day');
		print '</td></tr>';

		 	//projet
		if ($conf->projet->enabled && $conf->browser->layout != 'phone')
		{
			$projet->fetch($object->fk_projet);
			print '<tr><td width="15%">'.$langs->trans('Project').'</td><td colspan="2">';
			print $projet->getNomUrl(1,'',1);
			print '</td></tr>';
		}


		 	//property from
		print '<tr><td width="15%">'.$langs->trans('Propertyfrom').'</td><td colspan="2">';
		if ($objpro->fetch($object->fk_property_from)>0)
			print $objpro->ref;
		else
			print '&nbsp;';
		print '</td></tr>';
		 	//user_from
		print '<tr><td width="15%">'.$langs->trans('Usersends').'</td><td colspan="2">';
		$objuser->fetch($object->fk_user_from);
		if ($objuser->id == $object->fk_user_from)
		{
			if ($conf->browser->layout != 'phone')
				print $objuser->getNomUrl(1);
			else
				print $objuser->lastname.' '.$objuser->firstname;
		}
		else
			print '&nbsp;';
		print '</td></tr>';	     
		 	//property
		print '<tr><td width="15%">'.$langs->trans('Propertyto').'</td><td colspan="2">';
		if ($objpro->fetch($object->fk_property)>0)
			print $objpro->ref;
		else
			print '&nbsp;';
		print '</td></tr>';

		 	//location
		print '<tr><td width="15%">'.$langs->trans('Location').'</td><td colspan="2">';
		if ($objloc->fetch($object->fk_location)>0)
			print $objloc->detail;
		else
			print '&nbsp;';
		print '</td></tr>';

		 	//user 
		print '<tr><td width="15%">'.$langs->trans('Responsible').'</td><td colspan="2">';
		if ($objuser->fetch($object->fk_user)>0)
		{
			if ($conf->browser->layout != 'phone')
				print $objuser->getNomUrl(1);
			else
				print $objuser->lastname.' '.$objuser->firstname;
		}
		else
			print '&nbsp;';
		print '</td></tr>';	     
		 	//detail
		print '<tr><td width="15%" >'.$langs->trans('Detail').'</td><td colspan="2">';
		print $object->detail;    
		print '</td></tr>';

		 	//statut
		print '<tr><td width="15%" >'.$langs->trans('Statut').'</td><td colspan="2">';
		print $object->getLibStatut(2);    
		print '</td></tr>';

		print '</table>';

		 	//activos disponibles para asignacion
		$aInclude = array();
		$aExclude = array();

		$objassigd = new Assetsassignmentdetext($db);
		if ($object->statut>0)
			$objassigd->getlistassignment($id,'',"1,2");
		else
		{
				//if ($user->admin)
				//{
			$objassigd->getlistassetsassignment((!empty($object->fk_property_from)?$object->fk_property_from:''),1);
			if ($object->fk_property_from > 0)
			{
				if (count($objassigd->array) > 0) 
				{
					foreach ((array) $objassigd->array AS $i => $objn)
					{
						$aInclude[$objn->fk_asset] = $objn->fk_asset;
					}
				}
			}
			if ($object->fk_property_from <=0)
			{
				if (count($objassigd->array) > 0) 
				{
					foreach ((array) $objassigd->array AS $i => $objn)
					{
						$aExclude[$objn->fk_asset] = $objn->fk_asset;
					}
				}
			}

				//}
				//else
				//{
					//$objassigd->getlistassignment_user($user,$object->fk_property_from,1);
					//$aInclude = $objassigd->aAsset;
				//}
		}
			//activos asignados
		$objassigdact = new Assetsassignmentdetext($db);
		$objassigdact->getlistassignmentact($id,(!empty($object->fk_property)?$object->fk_property:''),0);
		if (count($objassigdact->array) > 0) 
		{
			foreach ((array) $objassigdact->array AS $i => $objn)
				$aExclude[$objn->fk_asset] = $objn->fk_asset;
		}
		$userWriter = false;
		if ($user->admin || $user->id == $object->fk_user_from)
			$userWriter = true;
		include_once DOL_DOCUMENT_ROOT."/assets/assignment/tpl/assetassignment.tpl.php";

		dol_fiche_end();


		/* ************************************** */
		/*                                        */
		/* Barre d'action                         */
		/*                                        */
		/* ************************************** */

		print '<div class="tabsAction">';
		if ($object->statut == 1)
		{
			print '<a class="butAction" href="fichesol.php?action=validate&id='.$object->id.'">'.$langs->trans("Validate").'</a>';
			print '<a class="butActionDelete" href="fichesol.php?action=novalidate&id='.$object->id.'">'.$langs->trans("Torefuse").'</a>';
		}	
		print "</div>";
	}
	else
	{
		print '<div>'.$langs->trans('Accion concluida').'</div>';		
	}
}

print llxFooterVierge();



function htmlemail($id)
{
	global $object,$langs,$objCharge,$objDepartament;
	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="http://192.168.43.98/bcb/mant/css/style-email.css">';
	$html.= '</head>';
	$html.= '<body>';
	$html.= '<form class="contact_form" action="http://192.168.43.98/bcb/mant/jobs/fichereg.php" method="POST">';
	$html.= '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	$html.= '<input type="hidden" name="action" value="updateemail">';
	$html.= '<input type="hidden" name="rowid" value="'.$id.'">';


	dol_htmloutput_mesg($mesg);

	$html.= '<table class="border" width="100%">';
	$html.= '<ul>';
  // ref numeracion automatica de la OT
	$html.= '<li>';
	$html.= '<label>'.$langs->trans('Jobsordernumber').'</label>';
	$html.= $object->ref;
  //  $html.= '</td></tr>';
	$html.= '</li>';
	$html.= '<li>';
  // solicitante
	$html.= '<label for="fk_member">'.$langs->trans('Solicitante').'</label>';
	$html.= select_adherent($user->fk_member,'fk_member','',0,1);
  //  $html.= '</td></tr>';
	$html.= '</li>';
	$html.= '<li>';

  // charge
	$html.= '<label for="fk_charge">'.$langs->trans('Charge').'</label>';
	$html.= $objCharge->select_charge($object->fk_charge,'fk_charge','',0,1);
  //$html.= '</td></tr>';
	$html.= '</li>';
	$html.= '<li>';

  // departament
	$html.= '<label for="fk_departament">'.$langs->trans('Departament').'</label>';
	$html.= $objDepartament->select_departament($object->fk_departament,'fk_departament','',0,1);
  //  $html.= '</td></tr>';
	$html.= '</li>';
	$html.= '<li>';

  // Especiality
	$html.= '<label>'.$langs->trans('Speciality').'</label>';
	$html.= select_speciality($object->speciality,'speciality','',1);
  //  $html.= '</td></tr>';
	$html.= '</li>';
	$html.= '<li>';

  //  $html.= '</table>';

	$html.= '<button class="submit" type="submit">'.$langs->trans("Register").'</button>';
	$html.= '</li>';
	$html.= '</ul>';

	$html.= '</form>';
	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

$db->close();


/**
 * Show header for new member
 *
 * @param 	string		$title				Title
 * @param 	string		$head				Head array
 * @param 	int    		$disablejs			More content into html header
 * @param 	int    		$disablehead		More content into html header
 * @param 	array  		$arrayofjs			Array of complementary js files
 * @param 	array  		$arrayofcss			Array of complementary css files
 * @return	void
 */
function llxHeaderVierge($title, $head="", $disablejs=0, $disablehead=0, $arrayofjs='', $arrayofcss='')
{
	global $user, $conf, $langs, $mysoc;
    top_htmlhead($head, $title, $disablejs, $disablehead, $arrayofjs, $arrayofcss); // Show html headers
    print '<body id="mainbody" class="publicnewmemberform" style="margin-top: 10px;">';

    // Print logo
    $urllogo=DOL_URL_ROOT.'/theme/login_logo.png';

    if (! empty($mysoc->logo_small) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_small))
    {
    	$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode('thumbs/'.$mysoc->logo_small);
    }
    elseif (! empty($mysoc->logo) && is_readable($conf->mycompany->dir_output.'/logos/'.$mysoc->logo))
    {
    	$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode($mysoc->logo);
    	$width=128;
    }
    elseif (is_readable(DOL_DOCUMENT_ROOT.'/theme/dolibarr_logo.png'))
    {
    	$urllogo=DOL_URL_ROOT.'/theme/dolibarr_logo.png';
    }
    // print '<center>';
    // print '<img alt="Logo" id="logosubscribe" title="" src="'.$urllogo.'" />';
    // print '</center><br>';

    print '<div style="margin-left: 50px; margin-right: 50px;">';
}

/**
 * Show footer for new member
 *
 * @return	void
 */
function llxFooterVierge()
{
	print '</div>';

	printCommonFooter('public');

	print "</body>\n";
	print "</html>\n";
}

?>
