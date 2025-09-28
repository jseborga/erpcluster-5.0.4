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
 *	\file       htdocs/assistance/fiche.php
 *	\ingroup    Assistance
 *	\brief      Page fiche insert assistance
 */

// define("NOLOGIN",1);
// define("NOCSRFCHECK",1);

// $entity=(! empty($_GET['entity']) ? (int) $_GET['entity'] : (! empty($_POST['entity']) ? (int) $_POST['entity'] : 1));
// if (is_int($entity)) define("DOLENTITY", $entity);

require("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/assistance/class/assistance.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/assistance/lib/adherent.lib.php';
require_once DOL_DOCUMENT_ROOT.'/assistance/lib/contact.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("assistance@assistance");

$action=GETPOST('action');

$id        = GETPOST("id");
$idr       = GETPOST("idr");

$mesg = '';

$_SESSION['socid'] = 0;
if ($user->societe_id) 
  {
    $socid=$user->societe_id;
    $_SESSION['socid'] = $user->societe_id;
    $aData = getlist_contact($user->societe_id);
    $_SESSION['aData'] = $aData;
  }
 else
   {
     $aData = getlist_adherent();
    $_SESSION['aData'] = $aData;
   } 

$object  = new Assistance($db);
/*
 * Actions
 */

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
  $action = '';
  $_GET["id"] = $_POST["id"];
}

/*
 * View
 */

$form=new Form($db);

$aArrjs = array();
$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
$aArrcss = array('/assistance/css/style.css');
$conf->dol_hide_leftmenu = 0;
llxHeader("",$langs->trans("Managementmant"),$help_url,'','','',$aArrjs,$aArrcss);

//llxHeaderVierge($langs->trans("Assistance"),'',1);

print_fiche_titre($langs->trans("Assistance"));

dol_htmloutput_mesg($mesg);

//modificado para asistencia
print '<table class="border" width="100%">';
print '<tr>';
print '<td valign="top">';

// print '<script language="JavaScript">';
// print 'function reFresh()
// { 
//       location.reload(true)
// }
// /* Establece el tiempo 1 minuto = 60000 milliseconds. */
// window.setInterval("reFresh()",10000);';
// print '</script>';


print '<script type="text/javascript" src="j1.js"></script>';

    
print '<script type="text/javascript" src="webcam.js"></script>';
print '<script language="JavaScript">
    var code = prompt("Introduce tu codigo");
var direcc = "register.php?code=";
var newdir = direcc + code;

		webcam.set_api_url( newdir );
		webcam.set_quality( 90 ); // JPEG quality (1 - 100)
		webcam.set_shutter_sound( true ); // play shutter click sound

	</script>
	<script language="JavaScript">
		document.write( webcam.get_html(320, 240) );
	</script>';	

print '<form method="POST" name="enviar">';
print '<br>';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
//    print '<input type=button value="Configure..." onClick="webcam.configure()">';
print '<div class="left">';
print '<input class="button boton180" type=button value="'.$langs->trans('Registrarse').'" onClick="take_snapshot()"'.'">';
print '</div>';
print '<div class="left">';
print '<a class="button boton180" href="javascript:location.reload()">'.$langs->trans('Newregister').'</a>';
print '</div>';
print '<div class="clear"></div>';
print '<input type="hidden" id="var_code" name="var_code">';
print '</form>';

print '
	<script language="JavaScript">
		webcam.set_hook( '."'onComplete'".', '."'my_completion_handler'".' );
</script>';
//     print "<script language='javascript'>
//               document.enviar.submit();
// </script>";

    print '<br>';
    print '</td>';
    
    //resultado
    print '<td width="50px;">&nbsp;</td>';
    print '<td width="50%">';
print '<div id="upload_results" style="background-color:#eee;"></div>';
print '</td>';
    print '</tr>';
print '<tr>';
print '<td colspan="3">';
print $_SESSION['namereg'].' '.$_SESSION['mytime'].' '.date('H:i');
print '<hr>';
print_r($_SESSION['myreg']);
print '</td>';
print '</tr>';

    print '</table>';


//print llxFooterVierge();
llxFooter();

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
