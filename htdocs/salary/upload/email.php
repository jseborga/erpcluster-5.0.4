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
 *	\file       htdocs/salary/upload/fiche.php
 *	\ingroup    salary subida archivos
 *	\brief      Page fiche upload 
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

//para envio email
require_once DOL_DOCUMENT_ROOT.'/core/lib/emailing.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/comm/mailing/class/mailing.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
//require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");


$langs->load("members");
$langs->load("others");

$action=GETPOST('action');

$id         = GETPOST("rowid");
$rid        = GETPOST("rid");
$fk_period  = GETPOST("fk_period");
$fk_concept = GETPOST("fk_concept");
$docum      = GETPOST('docum');

$mesg = '';

//params docum
/*
 1 = Id
 2 = Login
 3 = Docum
*/

/*
 * Actions
 */

if ($action == 'addSave')
  {
    $aArr = $_SESSION['aArrData'];
    //$aArr[] = array ('correo'=> 'rqc7000@gmail.com');
    //$aArr[] = array ('correo'=> 'lufedero@gmail.com');
    foreach ($aArr AS $i => $data)
      {
	//buscamos a quien enviamos
	$fkUsersup = '';
	$nameto = '';
	$emailcc = '';
	$emailbcc = '';
	echo '<hr>i '.$i;
	echo ' | emailto '.$emailto = $data['correos'];
	echo ' | nameto '.$nameto = $data['correos'];

	//$emailcc = '';
	//parametros de envio email
	// Define output language
	$outputlangs = $langs;
	$newlang='';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
	if (! empty($newlang))
	  {
	    $outputlangs = new Translate("",$conf);
	    $outputlangs->setDefaultLang($newlang);
	  }
	//informacion del correo
	$arr_file = array();
	$arr_mime = array();
	$arr_name = array();
	$arr_mime[] = 'text/html';
	
	$sendto   = $emailto;
	$email_from = 'info@ubuntu-bo.com';
	$tmpbody = bodyemail();
	$msgishtml = 1;
	$email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
	$arr_css = array('bgcolor' => '#A5FFAE');
	$tmpsujet = 'Requerimiento de Ingeniero(a) Civil para el Banco Central de Bolivia';
	$mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,$emailcc, $emailbcc, 0, $msgishtml,$email_errorsto,$arr_css);
	echo '<br>result '.$result=$mailfile->sendfile();
	if ($result <= 0)
	  {
	    echo '<hr>error, revisar';
	    exit;
	  }
	else
	  $action = 'create';
      }
  }
// Add
if ($action == 'add')
  {
    $nombre_archivo = $_FILES['archivo']['name'];
    $tipo_archivo   = $_FILES['archivo']['type'];
    $tamano_archivo = $_FILES['archivo']['size'];
    $tmp_name       = $_FILES['archivo']['tmp_name'];
    
    $tempdir = "tmp/";
    //compruebo si la extension es correcta
    
    if(move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
      {
	
	//  echo "file uploaded<br>"; 
      }
    else
      {
	echo 'no se puede mover';
	exit;
      }

    $csvfile = $tempdir.$nombre_archivo;

    $fh = fopen($csvfile, 'r');
    $headers = fgetcsv($fh);
    $aHeaders = explode(';',$headers[0]);
    $data = array();
    $aData = array();
    while (! feof($fh))
    {
        $row = fgetcsv($fh);
        if (!empty($row))
        {
	  $aData = explode(';',$row[0]);
	  $obj = new stdClass;
	  $obj->none = "";
	  foreach ($aData as $i => $value)
            {
	      $key = $aHeaders[$i];
	      if (!empty($key))
		$obj->$key = $value;
	      else
		$obj->none = $value." xx";
            }
	  $data[] = $obj;
        }
    }
    fclose($fh);
    $c=0;
    $action = "edit";
  }




if ($_POST["cancel"] == $langs->trans("Cancel"))
  {
    $action = '';
    $_GET["id"] = $_POST["id"];
  }
//campos principales tabla  m
$aHeaderTpl = array('correos' => 'correos');

//$action = "create";

/*
 * View
 */

$form=new Form($db);
$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

if ($action == 'create' || empty($action) && $user->rights->salary->crearuser)
  {
    print_fiche_titre($langs->trans("Upload archive"));  
    print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    
    dol_htmloutput_mesg($mesg);


    print '<table class="border" width="100%">';

    print '<tr><td>';
    print $langs->trans('Selectarchiv');
    print '</td>';
    print '<td>';
    print '<input type="file" name="archivo" size="40">';
    print '</td></tr>';
    print '</table>';

    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Upload").'"></center>';

    print '</form>';
  }
 else
   {
     If ($action == 'exit')
       {
	 print_barre_liste($langs->trans("Uploadarchivesave"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);
	 print '<table class="noborder" width="100%">';
	 //encabezado
	 print '<tr class="liste_titre">';
	 
	 print '</tr>';
	 print '</table>';
       }
     else
       {
	 print_barre_liste($langs->trans("Uploadarchive"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);
	 print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	 print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	 print '<input type="hidden" name="action" value="addSave">';
	 print '<input type="hidden" name="fk_period" value="'.$fk_period.'">';
	 print '<input type="hidden" name="fk_concept" value="'.$fk_concept.'">';
	 print '<input type="hidden" name="docum" value="'.$docum.'">';
	 
	 print '<table class="noborder" width="100%">';
	 //encabezado
	 foreach($aHeaders AS $i => $value)
	   {
	     $aHeadersOr[trim($value)] = trim($value);
	   }
	 
	 $aValHeader = array();
	 
	 foreach($aHeaderTpl AS $i => $value)
	   {
	     if (!$aHeadersOr[trim($value)])
	       $aValHeader[$value] = $value;
	   }
	 print '<tr class="liste_titre">';
	 foreach($aHeaders AS $i => $value)
	   {
	     print_liste_field_titre($langs->trans($value),'fiche.php','','','','');
	   }
	 print '</tr>';
	 if (!empty($aValHeader))
	   {
	     $lSave = false;
	     print "<tr class=\"liste_titre\">";
	     print '<td>'.$langs->trans('Missingfields').'</td>';
	     foreach ((array) $aValHeader AS $j => $value)
	       {
		 print '<td>'.$value.'</td>';
	       }
	     print '</tr>';
	   }
	 else
	   {
	     $lSave = true;
	     $var=True;
	     $c = 0;
	     foreach($data AS $key){
	       $var=!$var;
	       print "<tr $bc[$var]>";
	       $c++;
	       foreach($aHeaders AS $i => $keyname)
		 {
		   if (empty($keyname))
		     $keyname = "none";
		   $phone = $key->$keyname;
		   $aArrData[$c][$keyname] = $phone;
		   print '<td>'.$phone.'</td>';
		 }
	       
	       print '</tr>';
	     }

	   }
	 print '</table>';
	 $lSave = True;
	 If ($lSave)
	   {
	     $_SESSION['aArrData'] = $aArrData;
	     
	     print '<center><input type="submit" class="button" value="'.$langs->trans("Send").'"></center><br><br>';
	     	   }
	 //validando el encabezado
	 print '</form>';
       }
   }
llxFooter();
$db->close();

function bodyemail()
{
  global $langs;
  //  $url = $dolibarr_main_url_root;
  $outputlangs = $langs;
  
  $html = '<!DOCTYPE HTML>';
  $html.= '<html>';
  $html.= '<head>';
  //  $html.= '<link rel="stylesheet" media="screen" href="'.$url.'/poa/css/style-email.css">';
  $html.= '<meta http-equiv="Content-type" content="text/html; charset=UTF-8">';
  $html.= '</head>';
  $html.= '<body>';
  $html.= '<p style="color:#ff0000;">'.'El Banco Central de Bolivia ha publicado un aviso de requerimiento de un Ingeniero(a) civil para su Subgerencia de Proyectos de Infraestructura.</p>';
  $html.= '<p>Si está interesado por favor visite la página del BCB (Institucional/Recursos humanos/Oportunidades de empleo) o sigua el enlace siguiente:</p>';
  $html.= '<p><a href="http://www.bcb.gob.bo/webdocs/oportunidad_empleo/CE%2018-2015%20CONVOCATORIA.pdf">http://www.bcb.gob.bo/webdocs/oportunidad_empleo/CE%2018-2015%20CONVOCATORIA.pdf</a></p>';

  $html.= '<p>Atentamente,</p>';
  $html.= '<p>Ubuntu Bolivia</p>';
  $html.= '</body>';
  $html.= '</html>';
  return $html;
}

?>
