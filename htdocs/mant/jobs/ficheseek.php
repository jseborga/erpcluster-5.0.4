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
 *	\file       htdocs/mant/jobs/ficheseek.php
 *	\ingroup    Ordenes de Trabajo busqueda
 *	\brief      Page fiche mantenimiento 
 */
define("NOLOGIN",1);
define("NOCSRFCHECK",1);

$entity=(! empty($_GET['entity']) ? (int) $_GET['entity'] : (! empty($_POST['entity']) ? (int) $_POST['entity'] : 1));
if (is_int($entity)) define("DOLENTITY", $entity);

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobs.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobscontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsuser.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/charge/class/pcharge.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/departament/class/pdepartament.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/equipment/class/mequipment.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/adherent.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/societe.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/user.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';


//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("companies");
$langs->load("commercial");
$langs->load("bills");
$langs->load("banks");
$langs->load("users");
$langs->load("other");
$langs->load("mant");

$action=GETPOST('action');

$id   = GETPOST("id");
$ref  = GETPOST("ref");

$mesg = '';

$object      = new Mjobs($db);
$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);
$objSoc      = new Societe($db);
$objEquipment= new Mequipment($db);
$objCharge = new Pcharge($db);
$objDepartament = new Pdepartament($db);

$objUser = new User($db);
$objContact = new Contact($db);
$objJobscontact = new Mjobscontact($db);
$objJobsuser = new Mjobsuser($db);

/*
 * View
 */

$form=new Form($db);

llxHeaderVierge($langs->trans("Newticket"));

// $aArrjs = array();
// $help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
// $aArrcss = array('/mant/css/style-desktop.css');
// $conf->dol_hide_leftmenu = 0;
// llxHeader("",$langs->trans("Managementmant"),$help_url,'','','',$aArrjs,$aArrcss);

if ($action == '')
  {
    print_titre($langs->trans("Searchticket"));
    print '<br>';
    print '<form action="ficheseek.php" method="post">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="result">';
    
    dol_htmloutput_errors($mesg);
    
    print '<table class="border" width="100%">';
    
    print '<tr><td class="fieldrequired" width="20%">'.$langs->trans('Ticket').'</td><td colspan="2">';
    print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="33" maxlength="30">';
    print '</td></tr>';
        
    print '</table>';
    
    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Search").'"></center>';
    
    print '</form>';

  }
 else
   {
     if ($id || $ref)
       {
	 dol_htmloutput_mesg($mesg);      
	 $result = $object->fetch($id,$ref,$entity);
	 $objAdherent = new Adherent($db);
	 if ($result < 0)
	   {
	     dol_print_error($db);
	     exit;
	   }
	 else
	   {
	     /*
	      * Affichage fiche
	      */
	     if ($action <> 'edit' && $action <> 're-edit')
	       {
		 //$head = fabrication_prepare_head($object);
		 $head = '';
		 dol_fiche_head($head, 'card', $langs->trans("Jobs"), 0, 'mant');
		 
		 print '<table class="border" width="100%">';
		 
		 // ref numeracion automatica de la OT
		 print '<tr><td width="20%">'.$langs->trans('Jobsordernumber').'</td><td colspan="2">';
		 print $object->ref;
		 print '</td></tr>';
		 
		 //email
		 print '<tr><td width="20%">'.$langs->trans('Email').'</td><td colspan="2">';
		 print $object->email;
		 print '</td></tr>';
		 
		 // solicitante
		 $objAdherent->fetch($object->fk_member);
		 print '<tr><td >'.$langs->trans('Solicitante').'</td><td colspan="2">';
		 IF ($objAdherent->id == $object->fk_member)
		   print $objAdherent->lastname.' '.$objAdherent->firstname;
		 else
		   print '&nbsp;';
		 print '</td></tr>';
		 
		 //internal
		 print '<tr><td width="20%">'.$langs->trans('Internal').'</td><td colspan="2">';
		 print $object->internal;
		 print '</td></tr>';
		 
		 // // charge
		 // $objCharge->fetch($object->fk_charge);
		 // print '<tr><td >'.$langs->trans('Charge').'</td><td colspan="2">';
		 // print $objCharge->ref;
		 // print '</td></tr>';
		 
		 // // departament
		 // $objDepartament->fetch($object->fk_departament);
		 // print '<tr><td >'.$langs->trans('Departament').'</td><td colspan="2">';
		 // print $objDepartament->ref;
		 // print '</td></tr>';
		 
		 // property
		 $objProperty->fetch($object->fk_property);
		 print '<tr><td >'.$langs->trans('Property').' '.$object->fk_property.'</td><td colspan="2">';
		 if ($objProperty->id == $object->fk_property)
		   print $objProperty->ref;
		 else
		   print '&nbsp;';
		 print '</td></tr>';
		 
		 // location
		 $objLocation->fetch($object->fk_location);
		 print '<tr><td >'.$langs->trans('Location').' '.$object->fk_location.'</td><td colspan="2">';
		 if ($objLocation->id == $object->fk_location)
		   print $objLocation->detail;
		 else
		   print '&nbsp;';
		 print '</td></tr>';
		 
		 // Especiality
		 print '<tr><td >'.$langs->trans('Speciality').'</td><td colspan="2">';
		 if (!empty($object->speciality))
		   print select_speciality($object->speciality,'speciality','',1,1);
		 else
		   print '&nbsp;';

		 print '</td></tr>';
		 
		 // fk_soc
		 print '<tr><td >'.$langs->trans('Assigned').'</td><td colspan="2">';
		 
		 $objSoc->fetch($object->fk_soc);
		 if ($objSoc->id == $object->fk_soc)
		   {
		     //validamos el typent
		     $objTypent = fetch_typent($objSoc->typent_id);
		     
		     if ($objTypent->id == $objSoc->typent_id)
		       {
			 $object->typent = $objTypent->code;
			 $object->typent_id = $objTypent->id;
		       }
		     else
		       {
			 $object->typent = '';
			 $object->typent_id = '';
		       }
		     print $objSoc->name;
		   }
		 else
		   {
		     if ($object->fk_soc == -2)
		       print $langs->trans('Internalassignment');
		     else
		       print '&nbsp;';
		   }
		 print '</td></tr>';
		 
		 // Statut
		 print '<tr><td>'.$langs->trans("Status").'</td><td colspan="2">'.$object->getLibStatut(6).'</td></tr>';
		 
		 print "</table>";		 
		 print '</div>';
		 
 
		 if ($object->statut >= 2 && $object->statut != 9)
		   {
		     $head = '';
		     dol_fiche_head($head, 'card', $langs->trans("Jobstomake"), 0,'mant');
		     
		     print '<table class="border" width="100%">';
		     
		     //tecnico asignado
		     //buscamos a la compania
		     $lUser = false;
		     
		     if ($object->fk_soc == -2 || 
			 ($objTypent->id == $objSoc->typent_id && 
			  $objTypent->code == 'TE_BCB' )) //asignacion interna
		       $lUser = true;
		     if (!$lUser)
		       {
			 //buscamos si esta asignado el contacto responsable
			 $aArray = $objJobscontact->list_contact($object->id);
			 
			 if (count($aArray) > 0)
			   {
			     $a = 0;
			     foreach((array) $aArray AS $j => $objc)
			       {
				 if ($objContact->fetch($objc->fk_contact))
				   {
				     if (empty($a))
				       print '<tr><td width="20%">'.$langs->trans('Responsibletechnical').'</td><td colspan="2">';
				     else
				       print '<tr><td width="20%">'.$langs->trans('Technical').'</td><td colspan="2">';
				     
				     print $objContact->lastname.' '.$objContact->firstname;
				     print '</td></tr>';
				     $a++;
				   }
				 //$aArray = array();
			       }
			   }
		       }
		     else
		       {
			 //buscamos si esta asignado el usuario responsable
			 $aArray = $objJobsuser->list_jobsuser($object->id);
			 if (count($aArray) > 0)
			   {
			     $a = 0;
			     foreach((array) $aArray As $j => $obju)
			       {
				 if ($objUser->fetch($obju->fk_user))
				   {
				     if (empty($a))
				       print '<tr><td width="20%">'.$langs->trans('Responsibletechnical').'</td><td colspan="2">';
				     else
				       print '<tr><td width="20%">'.$langs->trans('Technical').'</td><td colspan="2">';
				     
				     print $objUser->lastname.' '.$objUser->firstname;
				     print '</td></tr>';
				     $a++;
				   }
			       }
			   }
		       }
		     

		     //trabajo programado
		     // Especiality
		     print '<tr><td width="20%">'.$langs->trans('Speciality').'</td><td colspan="2">';
		     if (!empty($object->speciality_prog))
		       print select_speciality($object->speciality_prog,'speciality_prog','',1,1);
		     else
		       print '&nbsp;';

		     print '</td></tr>';
		     //descripcion
		     print '<tr><td >'.$langs->trans('Description').'</td><td colspan="2">';
		     print $object->description_prog;
		     print '</td></tr>';
		     // dateini
		     print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
		     print dol_print_date($object->date_ini_prog,'daytext');
		     
		     print '</td></tr>';
		     
		     // datefin
		     print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
		     print dol_print_date($object->date_fin_prog,'daytext');
		     print '</td></tr>';

		     // imagen ini
		     
		     print '<tr class="hideonsmartphone">';
		     print '<td>'.$langs->trans("Photobeforestartingwork").'</td>';
		     print '<td colspan="2">';
		     if ($object->image_ini) print $object->showphoto('ini',$object,100);
		     print '</td>';
		     print '</tr>';

		     print '</table>';

		     print '</div>';

		   }

		     //trabajo realizado
		 if ($object->statut == 4)
		   {
		     $head = '';
		     dol_fiche_head($head, 'card', $langs->trans("Workcarriedout"), 0, 'mant');
		     
		     print '<table class="border" width="100%">';
		     // Especiality
		     print '<tr><td width="20%">'.$langs->trans('Speciality').'</td><td colspan="2">';
		     if (!empty($object->speciality_job))
		       print select_speciality($object->speciality_job,'speciality_job','',1,1);
		     else
		       print '&nbsp;';
		     print '</td></tr>';
		     
		     
		     //equipment
		     $objEquipment->fetch($object->fk_equipment);
		     
		     print '<tr><td >'.$langs->trans('Equipment').'</td><td colspan="2">';
		     if ($objEquipment->id == $object->fk_equipment)
		       print $objEquipment->nom;
		     else
		       print '&nbsp;';
		     print '</td></tr>';
		     
		     // dateini
		     print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
		     print dol_print_date($object->date_ini,'daytext');
		     
		     print '</td></tr>';
		     
		     // datefin
		     print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
		     print dol_print_date($object->date_fin,'daytext');
		     print '</td></tr>';
		     
		     //descripcion job
		     print '<tr><td >'.$langs->trans('Descriptionofworkperformed').'</td><td colspan="2">';
		     print $object->description_job;
		     print '</td></tr>';
		     
		     // typemant
		     print '<tr><td width="20%">'.$langs->trans('Typeofwork').'</td><td colspan="2">';
		     select_typemant($object->typemant,'typemant','',1,1);
		     print '</td></tr>';
		     
		     
		     // imagen fin
		     print '<tr class="hideonsmartphone">';
		     print '<td>'.$langs->trans("Phototocompletethework").'</td>';
		     print '<td colspan="2">';
		     if ($object->image_fin) print $object->showphoto('fin',$object,100);
		     print '</td>';
		     print '</tr>';
		     print "</table>";
		     
		     print '</div>';
		     
		   }
	       }
	   }
       }
   }

print
llxFooterVierge();

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
