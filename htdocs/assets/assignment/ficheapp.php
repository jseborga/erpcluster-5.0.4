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
if ($action == 'confirm_noapproved' && $_REQUEST["confirm"] == 'yes')
{
	$error = 0;
	if ($object->fetch($id))
	{
		//iniciamos el guardado
		$db->begin();
			//cambiando a borrador
		$object->status = -1;
		$object->mark = ' ';
		//update
		$res = $object->update($objuser);
		if ($res <= 0)
		{
			setEventMessages($object->error,$object->errors,'errors');
			$error++;
		}
		if (empty($error))
		{
			//iniciamos quitar la marca de los activos seleccionados
			$objassigndet->getlistassignment($object->id,0);
			$aArray = $objassigndet->array;
			foreach ((array) $aArray AS $j => $objdet)
			{
				//quitamos la marka del activo para habilitar
				if ($objass->fetch($objdet->fk_asset) > 0)
				{
					$objass->mark = ' ';
					$objass->statut = 9;
					$res = $objass->update($objuser);
					if ($res <= 0)
					{
						setEventMessages($objass->error,$objass->errors,'errors');
						$error++;
					}
				}
			}
		}
		if (empty($error))
		{
			$db->commit();
			unset($_SESSION['aTemp']);
			setEventMessages($langs->trans('Proceso concluido'),null,'mesgs');
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
if ($action == 'confirm_approved' && $_REQUEST["confirm"] == 'yes')
{
	$error = 0;
	if ($object->fetch($_REQUEST["id"]))
	{
		$array = unserialize($_SESSION['aPost']);
		$_POST = $array[$object->id];
		$selasset = GETPOST('selasset');
		//iniciamos el guardado
		$db->begin();
			//cambiando a validado
		$object->status = 3;
		$object->mark = ' ';
			//update
		$result = $object->update($objuser);
		if ($result > 0)
		{
				//iniciamos la asignacion de los activos y su respectiva baja del anterior responsable
			$objassigndet->getlistassignment($object->id,0);
			$aArray = $objassigndet->array;
			foreach ((array) $aArray AS $j => $objdet)
			{
				if ($selasset[$objdet->id])
				{
					//buscamos el ultimo activo asignado
					if (empty($error) && $objassigndet->fetch_active($objdet->fk_asset)>0)
					{
						if ($objassigndet->fk_asset == $objdet->fk_asset && !empty($objassigndet->id))
						{
							//damos de baja
							$objassigndet->date_end = dol_now();
							$objassigndet->status = 2;
							$res = $objassigndet->update($objuser);
							if ($res <=0)
							{
								setEventMessages($objassigndet->error,$objassigndet->errors,'errors');
								$error++;
							}
						}
					}
					if (empty($error))
					{
						if ($objassigndet->fetch($objdet->id)>0)
						{
							//damos de alta
							$objassigndet->status = 1;
							$res = $objassigndet->update($objuser);
							if ($res <=0)
							{
								setEventMessages($objassigndet->error,$objassigndet->errors,'errors');
								$error++;
							}
							//quitamos la marka del activo para habilitar
							if ($objass->fetch($objdet->fk_asset)> 0)
							{
								$objass->mark = ' ';
								$objass->statut = 3;
								$res = $objass->update($objuser);
								if ($res <=0)
								{
									setEventMessages($objass->error,$objass->errors,'errors');
									$error++;
								}
							}
						}
					}
				}
			}
			if (empty($error))
			{
				unset($_SESSION['aTemp']);
				$db->commit();
				header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
			else
			{
				$db->rollback();
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

if ($object->id && $object->status == 2)
{

	dol_htmloutput_mesg($mesg);

	if (isset($_POST['Toaccept']) && $action == 'accept') $action = 'approved';
	if (isset($_POST['Notaccept']) && $action == 'accept') $action = 'noapproved';

		// Affichage fiche
	if ($action <> 'edit' && $action <> 're-edit')
	{
		print '<form name="fiche_assig" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="accept">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		print '<input type="hidden" name="code" value="'.$object->mark.'">';

		dol_fiche_head();
			//approved
		if ($action == 'approved')
		{
			$array = $_POST;
			$aPost[$object->id] = $array;
			$_SESSION['aPost'] = serialize($aPost);
			$qtysel = count(GETPOST('selasset'));
			$text = $qtysel.' '.$langs->trans('The').' '.GETPOST('quantassets').' '.$langs->trans('Assets');
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
				$langs->trans("Accept assignment"),
				$langs->trans("Confirm accept assignment").': '.$text,
				"confirm_approved",
				'',
				0,
				2);
			if ($ret == 'html') print '<br>';
		}
		if ($action == 'noapproved')
		{
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
				$langs->trans("Do not accept assignment"),
				$langs->trans("Confirm not accepting assignment"),
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
		
		$objassigd->getlistassignment($id,'',"1,2");
		
			//activos asignados
		$objassigdact = new Assetsassignmentdetext($db);
		$objassigdact->getlistassignmentact($id,(!empty($object->fk_property)?$object->fk_property:''),0);
		if (count($objassigdact->array) > 0) 
		{
			foreach ((array) $objassigdact->array AS $i => $objn)
				$aExclude[$objn->fk_asset] = $objn->fk_asset;
		}
		$userWriter = false;
		if ($user->admin || $objuser->id == $object->fk_user)
			$userWriter = true;
		include_once DOL_DOCUMENT_ROOT."/assets/assignment/tpl/assetassignment.tpl.php";

		dol_fiche_end();


		/* ************************************** */
		/*                                        */
		/* Barre d'action                         */
		/*                                        */
		/* ************************************** */

		print "<div class=\"tabsAction\">\n";
		if ($object->status == 2)
		{
			print '<center>';
			print '<input type="submit" class="butAction" name="Toaccept" value="'.$langs->trans("Toaccept").'">';
			print '&nbsp;';
			print '<input type="submit" class="butActionDelete" name="Notaccept" value="'.$langs->trans("Notaccept").'">';
			print '</center>';

		}
		print "</div>";
		print '</form>';
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
