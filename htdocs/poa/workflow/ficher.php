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
 *	\file       htdocs/poa/process/fiche.php
 *	\ingroup    Process
 *	\brief      Page fiche poa process
 */

define("NOLOGIN",1);
define("NOCSRFCHECK",1);

$entity=(! empty($_GET['entity']) ? (int) $_GET['entity'] : (! empty($_POST['entity']) ? (int) $_POST['entity'] : 1));
if (is_int($entity)) define("DOLENTITY", $entity);

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/workflow/class/poaworkflow.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/workflow/class/poaworkflowdet.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/workflow/class/poaworkflowuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/poa/class/poapoa.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaareauser.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/pac/class/poapac.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';
require_once DOL_DOCUMENT_ROOT.'/poa/lib/areauser.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';


$langs->load("other");
$langs->load("poa@poa");

$action=GETPOST('action');

$id        = GETPOST("id");
$idr       = GETPOST("idr");
$gestion   = GETPOST("gestion");
$fk_poa_prev = GETPOST("fk_poa_prev");
$gestion = $_SESSION['gestion'];
if (empty($gestion)) $gestion = date('Y');
$idArea = 3; //generar funcion para recuperar por usuario

$mesg = '';

$object  = new Poaworkflow($db);
$objworkd= new Poaworkflowdet($db);
$objworku= new Poaworkflowuser($db);
$objpoa  = new Poapoa($db);
$objarea = new Poaarea($db);
$objareau= new Poaareauser($db);
$objuser = new User($db);
$objprev = new Poaprev($db);
$objpac  = new Poapac($db);
$objnew  = new Poaworkflowdet($db);

/*
 * Actions
 */


// Adddet
if ($action == 'confread' && !empty($id) && !empty($idr))
  {
    $error = 0;
    if ($object->fetch($id)>0)
      {
	if ($objworkd->fetch($idr)>0)
	  {
	    if ($objworkd->id == $idr && $id == $object->id)
	      {
		if (is_null($objworkd->date_read))
		  {
		    $objworkd->date_read = dol_now();
		    $objworkd->tms = dol_now();
		    $result = $objworkd->update($user);
		    if ($result <= 0)
		      {
			$action = '';
			$mesg.='<div class="error">'.$langs->trans("Errorupdate").'</div>';
		      }
		    else
		      $mesg='<div class="ok">'.$langs->trans('Satisfactory update').'</div>';
		  }
	      }
	  }
	else
	  {
	    $mesg='<div class="error">'.$objworkd->error.'</div>';
	  }
      }
    else
      {
	$mesg='<div class="error">'.$object->error.'</div>';
      }
  }


/*
 * View
 */

$form=new Form($db);

llxHeaderVierge($langs->trans("Workflow"));

if ($id || $_GET['id'])
  {
    //dol_htmloutput_mesg($mesg);
    if (empty($id)) $id = $_GET['id'];
    $result = $object->fetch($id);
    if ($result < 0)
      {
	dol_print_error($db);
      }
    
    dol_htmloutput_mesg($mesg);
    
    /*
     * Affichage fiche
     */
    if ($action <> 'edit' && $action <> 're-edit')
      {
	//$head = fabrication_prepare_head($object);
	
	dol_fiche_head($head, 'card', $langs->trans("Workflow"), 0, DOL_URL_ROOT.'/poa/img/workflows.png',1);
	
	     
	print '<table class="border" style="min-width=1000px" width="100%">';
	
	$objprev->fetch($object->fk_poa_prev);
	     
	// preventive
	print '<tr><td width="20%">'.$langs->trans('Preventive').'</td><td colspan="7">';
	print $objprev->nro_preventive.' '.$objprev->label;
	print '</td>';
	print '</tr>';
	print '<tr><td>'.$langs->trans('Date').'</td><td colspan="7">';
	print dol_print_date($objprev->date_preventive,'day');
	print '</td>';
	print '</tr>';
	print '<tr><td>'.$langs->trans('Contractisrequired').'</td><td colspan="7">';
	print ($object->contrat?$langs->trans('Yes'):$langs->trans('Not'));
	print '</td>';
	print '</tr>';
	print '<tr><td>';
	print $langs->trans('Doclink');
	print '</td>';
	print '<td colspan="7">';
	if ($object->doclink)
	  {
	    $aDocnew = explode('/',$object->doclink);
	    $cDocnew = $aDocnew[count($aDocnew)-1];
	    $cDocnew = str_replace("%20"," ",$cDocnew);
	    print '<a href="'.$object->doclink.'" target="_blank">'.$cDocnew.'</a>';
	  }
	else
	  print $object->doclink;
	print '</td>';
	print '</tr>';
	//lista de workflow assign
	$objworku->getlist($id);
	if (count($objworku->array) > 0)
	  {
	    foreach((array) $objworku->array AS $m => $objwu)
	      {
		print '<tr><td>'.$objwu->code_area.'</td><td colspan="7">';
		print '<table class="nobordernopadding">';
		if ($objuser->fetch($objwu->fk_user) > 0)
		  {
		    print '<tr><td>';
		    print $objuser->lastname.' '.$objuser->firstname;
		    print '</td>';
		    print '</td></tr>';
		  }
		else
		  print $langs->trans('Notdefined');
		print '</table>';
		print '</td>';
		print '</tr>';
	      }
	  }

	print '<tr><td colspan="7">&nbsp;</td></tr>';

	//mostramos el seguimiento al workflow
	
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Sequen"),"", "","","",'align="center"');
	print_liste_field_titre($langs->trans("Date"),"", "","","",'');
	print_liste_field_titre($langs->trans("Ofarea"),"", "","","",'');
	print_liste_field_titre($langs->trans("Toarea"),"", "","","",'');
	print_liste_field_titre($langs->trans("Procedure"),"", "","","",'');
	print_liste_field_titre($langs->trans("Detail"),"", "","","",'');
	print_liste_field_titre($langs->trans("Days"),"", "","","",'align="center"');
	print '</tr>';
	
	//buscamos el contenido del workflow det
	$objworkd->getlist($id);
	$codearea = '';		 
	$sequen = 0;
	$datetracking = '';
	if (count($objworkd->array) > 0)
	  {
	    foreach ($objworkd->array AS $i => $objwd)
	      {
		$codearea = $objwd->code_area_next;
		$sequen = $objwd->sequen;
		print '<tr>';
		print '<td align="center">'.$objwd->sequen.'</td>';
		print '<td>'.dol_print_date($objwd->date_tracking,'day').'</td>';
		//last area
		print '<td>'.$objwd->code_area_last.'</td>';
		print '<td>'.$objwd->code_area_next.'</td>';
		//typeprocedure
		print '<td>';
		print select_typeprocedure($objwd->code_procedure,'code_procedure','',0,1,'code');
		print '</td>';
		//detail
		print '<td>';
		print $objwd->detail;
		print '</td>';
		//day
		print '<td align="center">';
		if (!empty($datetracking))
		  {
		    $daydelay = resta_fechas($datetracking,$objwd->date_tracking,1);
		    print $daydelay;
		  }
		$datetracking = $objwd->date_tracking;
		print '</td>';		
		print '</tr>';		     
	      }
	  }
	print '</table>';
	print '</div>';
	     
      }
  }
print llxFooterVierge();

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
