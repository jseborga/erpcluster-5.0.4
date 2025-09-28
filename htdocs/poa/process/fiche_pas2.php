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
 *	\file       htdocs/poa/process/fiche_pas2.php
 *	\ingroup    Process
 *	\brief      Page fiche poa process register contrat devengados.
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';
//require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprevprocess.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/pac/class/poapac.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocesscontrat.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapre.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidacom.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidadev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapredet.class.php';
// if ($conf->contratadd->enabled)
//   require_once DOL_DOCUMENT_ROOT.'/contratadd/class/contratadd.class.php';
if ($conf->addendum->enabled)
  require_once DOL_DOCUMENT_ROOT.'/addendum/class/addendum.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';
require_once DOL_DOCUMENT_ROOT.'/poa/lib/doc.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

$langs->load("poa@poa");

$action=GETPOST('action');

$id        = GETPOST('id'); //proces
$idr       = GETPOST('idr'); //item devengado
$idrc      = GETPOST('idrc'); //contrato
$idp       = GETPOST('idp'); //idpreventivo
$clid      = GETPOST('clid');//clear idprevdev

if (!empty($id))
{
    $ida = $_SESSION['aListip'][$id]['idAct'];
    $idp = $_SESSION['aListip'][$id]['idPrev'];
    $idpa= $_SESSION['aListip'][$id]['idPrevant'];
    $idc = $_SESSION['aListip'][$id]['idContrat'];
    $gestionact = $_SESSION['aListip'][$id]['gestion'];

    $lAnticipo = $_SESSION['aListip'][$id]['anticipo'];
  }
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$gestion   = GETPOST("gestion");
$fk_poa_prev = GETPOST("fk_poa_prev");
$action    = GETPOST('action');

$aPlazo = array(1=>'DC',
		2=>'DH',
		3=>'AC');
if (empty($gestion)) $gestion = date('Y');
$idArea = 3; //generar funcion para recuperar por usuario

$mesg = '';

$objpcon = new Poaprocesscontrat($db);
$object  = new Poaprocess($db);
$objarea = new Poaarea($db);
$objuser = new User($db);
$objprev = new Poaprev($db);
$objpac  = new Poapac($db);
$objcont = new Contrat($db);
$objpp   = new Poapartidapre($db);
$objppd  = new Poapartidapredet($db);
$objcom  = new Poapartidacom($db);
$objsoc  = new Societe($db);
$objdev  = new Poapartidadev($db);
$extrafields = new ExtraFields($db);


// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($objcont->table_element);

if ($action == 'search')
  $action = 'createedit';
/*
 * Actions
 */

//uppdf
if ($action == 'uppdf')
{
    if ($object->fetch($_POST["id"])>0)
    {
        // Logo/Photo save
        $dir     = $conf->poa->dir_output.'/payment/pdf';
        $file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
        if ($file_OKfin)
        {
	       // if (GETPOST('deletedocfin'))
	       //   {
	       // 	$fileimg=$dir.'/'.$object->image_fin;
	       // 	$dirthumbs=$dir.'/thumbs';
	       // 	dol_delete_file($fileimg);
	       // 	dol_delete_dir_recursive($dirthumbs);
	       //   }
            if (doc_format_supported($_FILES['docpdf']['name']) > 0)
            {
                dol_mkdir($dir);
                if (@is_dir($dir))
                {
                    $newfile=$dir.'/'.dol_sanitizeFileName($_FILES['docpdf']['name']);
                    $newfile=$dir.'/'.dol_sanitizeFileName($idr.'.pdf');
                    $result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
                    if (! $result > 0)
                    {
                        $errors[] = "ErrorFailedToSaveFile";
                    }
                    else
                    {
			             // Create small thumbs for company (Ratio is near 16/9)
			             // Used on logon for example
			             $imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
	               		// Create mini thumbs for company (Ratio is near 16/9)
			             // Used on menu or for setup page for example
                        $imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
                    }
                    header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
                    exit;
                }
            }
            else
            {
                $errors[] = "ErrorBadImageFormat";
            }
        }
        else
        {
            switch($_FILES['docpdf']['error'])
            {
                case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
                case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
                    $errors[] = "ErrorFileSizeTooLarge";
                    break;
                case 3: //uploaded file was only partially uploaded
                    $errors[] = "ErrorFilePartiallyUploaded";
                    break;
            }
        }
    }
    print_r($errors);
    echo $error;
    echo '<hr>dir '.$dir;
    exit;
}

// Adddev
if ($action == 'adddev' && $user->rights->poa->deve->crear)
  {
    $error = 0;
    $object->fetch($id);
    $objprev->fetch($idp);
    $date_dev = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
    $invoice = GETPOST('invoice');
    if ($lAnticipo && empty($invoice))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Error, numberinvoiceisrequired").'</div>';
      }
    $aPartida = GETPOST('partida');
    $aAmount = GETPOST('amount');
    //$nro_dev = GETPOST('nro_dev');

    //recuperamos el ultimo numero de autorizacion
    $objectdev = new Poapartidadev($db);
    $nro_dev = 0;
    if ($objectdev->get_maxref($gestion,$objprev->fk_area))
      $nro_dev = $objectdev->maximo;
    if ($nro_dev <= 0)
      $error++;

    $db->begin();
    if (empty($nro_dev))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Error, numberauthisrequired").'</div>';
      }
    if (empty($error))
      {
	foreach((array) $aAmount AS $fk_poa_partida_com => $value)
	  {
	    //registro nuevo
	    if ($value > 0)
	      {
		//obtenemos el comprometido
		$objcom->fetch($fk_poa_partida_com);
		$objdev->fk_poa_partida_com = $fk_poa_partida_com;
		$objdev->fk_poa_prev = $idp;
		$objdev->fk_structure = $objcom->fk_structure;
		$objdev->fk_contrat = $objcom->fk_contrat;
		$objdev->fk_contrato = $objcom->fk_contrato;
		$objdev->fk_poa = $objcom->fk_poa;

		// $objdev->fk_structure = GETPOST('fk_structure');
		// $objdev->fk_contrat = GETPOST('fk_contrat');
		// $objdev->fk_poa = GETPOST('fk_poa');

		$objdev->date_dev = $date_dev;
		$objdev->nro_dev = $nro_dev;
		$objdev->gestion = GETPOST('gestion');
		$objdev->invoice = GETPOST('invoice');
		$objdev->date_create = dol_now();
		$objdev->fk_user_create = $user->id;
		$objdev->amount = $value;
		$objdev->partida = $aPartida[$fk_poa_partida_com];
		$objdev->tms = dol_now();
		$objdev->statut = 1;
		$objdev->active = 1;
		$iddev = $objdev->create($user);
		if ($iddev > 0)
		  {
		  }
		else
		  $error++;
	      }
	  }
      }
    if (empty($error))
      {
	if ($objprev->fetch($object->fk_poa_prev))
	  {
	    $objprev->statut = 3; //3 devengado
	    if ($objprev->update($user) > 0)
	      {
		//exito;
		$db->commit();
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id.'&action=create');
		exit;
	      }
	    else
	      {
		$error++;
		$db->rollback();
		//se debe cambiar el estado manualmente
	      }
	  }
	else
	  {
	    $db->rollback();
	    $action="create";
	    //se debe cambiar el estado manualmente
	  }
      }
    else
      {
	$db->rollback();
	$action="create";   // Force retour sur page creation

      }
  }

// updatedev
if ($action == 'updatedev' && $_POST["cancel"] != $langs->trans("Cancel"))
  {
    $error = 0;
    $object->fetch($id);
    $objprev->fetch($idp);
    $objdev->fetch($idr);
    if ($objdev->id == $idr && ($user->rights->poa->deve->mod || $user->id == $objprev->fk_user_create))
      {
	$date_dev = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

	$aPartida = GETPOST('partida');
	$aAmount = GETPOST('amount');
	$nro_dev = GETPOST('nro_dev');
	$db->begin();
	if (empty($nro_dev))
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("Error, numberauthisrequired").'</div>';
	  }
	if (empty($error))
	  {

	    $objdev->date_dev = $date_dev;
	    if ($user->rights->poa->deve->mod)
	      {
		$objdev->nro_dev  = $nro_dev;
		$objdev->gestion  = GETPOST('gestion');
	      }
	    $objdev->invoice  = GETPOST('invoice');
	    $objdev->amount   = GETPOST('amount');
	    $objdev->tms      = dol_now();
	    $objdev->statut   = 1;
	    $objdev->active   = 1;
	    $iddev = $objdev->update($user);
	    if ($iddev > 0)
	      {
	      }
	    else
	      $error++;
	  }
      }
    if (empty($error))
      {
	//exito;
	$db->commit();
	header("Location: fiche_pas2.php?id=".$id.'&idrc='.$idrc.'&action=create');
	exit;
      }
    else
      {
	$db->rollback();
	$action="editdev";   // Force retour sur page creation
      }
  }

// Delete process
if ($action == 'confirm_delete_accrued' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->deve->del)
  {
    $objdev->fetch($_REQUEST["idr"]);
    $result=$objdev->delete($user);
    if ($result > 0)
      {
	header("Location: ".DOL_URL_ROOT.'/poa/process/fiche_pas2.php?id='.$id);
	exit;
      }
    else
      {
	$mesg='<div class="error">'.$objdev->error.'</div>';
	$action='';
      }
  }

if ( ($action == 'createedit') )
  {
    require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
    //$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
    $tmparray['fk_socid'] = GETPOST('fk_socid');
    if (!empty($tmparray['fk_socid']))
      {
	$objpcon->fk_socid = $tmparray['fk_socid'];
      }
    $action='create';
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

//cabecera
//$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
//$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');
//$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
//llxHeader("",$langs->trans("Payments"),$help_url,'','','',$aArrjs,$aArrcss);

header("Content-type: text/html; charset=".$conf->file->character_set_client);

$aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/bootstrap-responsive.min.css','poa/css/style-responsive.css','poa/css/AdminLTE.css');
$aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/dist/css/AdminLTE.css','poa/css/dist/css/AdminLTE.min.css','poa/css/dist/css/skins/_all-skins.min.css');
$aArrayofjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');

top_htmlhead($head,$langs->trans("POA"),0,0,$aArrayofjs,$aArrayofcss);

//impresion de submenu segun seleccion
include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/menup.tpl.php';

if ($id || $_GET['id'])
  {
    dol_htmloutput_mesg($mesg);
    if (empty($id)) $id = $_GET['id'];
    $result = $object->fetch($id);
    if ($result < 0)
      {
	dol_print_error($db);
      }

    /*
     * Affichage fiche
     */
    // if ($action <> 'edit' && $action <> 're-edit')
    //   {
    //$head = fabrication_prepare_head($object);

    dol_fiche_head($head, 'card', $langs->trans("Process"), 0, 'mant');

    /*
     * Confirmation de la validation
     */
    if ($action == 'validate')
      {
	$object->fetch(GETPOST('id'));
	//cambiando a validado
	$db->begin();
	//cambiando el preventivo a statut 1
	if ($objprev->fetch($object->fk_poa_prev))
	  {
	    $objprev->active = 2;
	    $objprev->update($db);
	  }

	$object->statut = 1;
	//update
	$res = $object->update($user);
	//creando la relacion de preventivo y proceso
	// $objpp = new Poaprevprocess($db);
	// $objpp->fk_poa_prev = $object->fk_poa_prev;
	// $objpp->fk_poa_process = $object->id;
	// $objpp->date_create = $object->date_process;
	// $objpp->tms = dol_now();
	// $objpp->fk_user_create = $user->id;
	// $objpp->statut = 1;
	// $idpp = $objpp->create($user);
	if ($res > 0)
	  $db->commit();
	else
	  $db->rollback();
	$action = '';
	//header("Location: fiche.php?id=".$_GET['id']);

      }


    // Confirm delete third party
    if ($action == 'delete')
      {
	$form = new Form($db);
	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteprocess"),$langs->trans("Confirmdeleteprocess",$object->ref.' '.$object->detail),"confirm_delete",'',0,2);
	if ($ret == 'html') print '<br>';
      }

    // Confirm delete devengado
    if ($action == 'deletedev')
      {
	$form = new Form($db);
	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$id.'&idr='.$idr,$langs->trans("Deleteaccrued"),$langs->trans("Confirmdeleteaccrued",$object->ref.' '.$object->detail),"confirm_delete_accrued",'',0,2);
	if ($ret == 'html') print '<br>';
      }

    print '<table class="border" style="min-width=1000px" width="100%">';

    //mostramos
    //preventivo seleccionado
    $aPrev = array();
    print '<tr class="liste_titre">';
    print_liste_field_titre($langs->trans("Number"),"", "","","",'');
    print_liste_field_titre($langs->trans("Gestion"),"", "","","",'');
    print_liste_field_titre($langs->trans("Preventive"),"", "","","",'');
    print_liste_field_titre($langs->trans("Amount"),"", "","","",'');
    print '</tr>';
    //buscamos el preventivo

    if ($objprev->fetch($object->fk_poa_prev)>0)
      {
	echo '<hr>buscando preventivo';
	print '<tr>';
	print '<td width="5%">'.$objprev->nro_preventive.'</td>';
	print '<td width="5%">'.$objprev->gestion.'</td>';
	print '<td width="90%">'.$objprev->label.'</td>';
	print '<td align="right" width="5%">'.price($objprev->amount).'</td>';
	print '</tr>';
	$aPrev[$objprev->id] = $objprev->gestion;
	//verificamos si tiene hijos
	$objprevh = new Poaprev($db);

	$objprevh->getlistfather($object->fk_poa_prev);

	foreach ((array) $objprevh->arrayf AS $j => $objp)
	  {
	    print '<tr>';
	    print '<td width="5%">'.$objp->nro_preventive.'</td>';
	    print '<td width="5%">'.$objp->gestion.'</td>';
	    print '<td width="90%">'.$objp->label.'</td>';
	    print '<td align="right" width="5%">'.price($objp->amount).'</td>';
	    print '</tr>';
	  }
	$objprevh->getlistant($object->fk_poa_prev);

	foreach ((array) $objprevh->arraya AS $j => $objp)
	  {
	    print '<tr>';
	    print '<td width="5%">'.$objp->nro_preventive.'</td>';
	    print '<td width="5%">'.$objp->gestion.'</td>';
	    print '<td width="90%">'.$objp->label.'</td>';
	    print '<td align="right" width="5%">'.price($objp->amount).'</td>';
	    print '</tr>';
	    $aPrev[$objp->id] = $objp->gestion;

	    	//verificamos si tiene hijos
	    $objprevh2 = new Poaprev($db);
	    $objprevh2->getlistfather($objp->id);
	    $arrayf = $objprevh2->arrayf;
	    foreach ((array) $arrayf AS $j => $objp2)
	      {
		print '<tr>';
		print '<td width="5%">'.$objp2->nro_preventive.'</td>';
		print '<td width="5%">'.$objp2->gestion.'</td>';
		print '<td width="90%">'.$objp2->label.'</td>';
		print '<td align="right" width="5%">'.price($objp2->amount).'</td>';
		print '</tr>';
	      }
	  }

      }
    //gerencia subgerencia departamento
    print '<tr class="liste_titre">';
    print_liste_field_titre($langs->trans("Management learn"),"", "","","",'colspan="2" width="33%"');
    print_liste_field_titre($langs->trans("Submanagement"),"", "","","",'width="33%"');
    print_liste_field_titre($langs->trans("Departament"),"", "","","",'width="33%"');
    print '</tr>';
    print '<tr>';
    print '<td colspan="2">'.$object->area[0].'</td>';
    print '<td>'.$object->area[1].'</td>';
    print '<td>'.$object->area[2].'</td>';
    print '</tr>';

    //date
    print '<tr><td width="150px">'.$langs->trans('Date').'</td><td colspan="3">';
    print dol_print_date($object->date_process,'day');
    print '</td></tr>';

    //amount
    print '<tr><td width="12%">'.$langs->trans('Reference price').'</td><td colspan="3">';
    print price(price2num($object->amount,'MT'));
    print '</td></tr>';

    //type modality
    print '<tr><td>'.$langs->trans('Modality').'</td><td colspan="3">';
    print select_tables($object->fk_type_con,'fk_type_con','',0,1,'05');
    print '</td></tr>';

    //label
    print '<tr><td>'.$langs->trans('Title').'</td><td colspan="3">';
    print $object->label;
    print '</td></tr>';

    //type adj
    print '<tr><td>'.$langs->trans('Type of adjudication').'</td><td>';
    print select_tables($object->fk_type_adj,'fk_type_adj','',0,1,'01');
    print '</td>';
    print '<td colspan="2">'.$langs->trans('Refpac').': ';
    print $object->ref_pac;
    print '</td>';
    print '</tr>';

    print '</table>';
    print '</div>';

    /* ********************************* */
    /*                                   */
    /* Barre d'action                    */
    /*                                   */
    /* ********************************* */

    print "<div class=\"tabsAction\">\n";

    if ($user->rights->poa->prev->leer)
      print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php'.(isset($_GET['nopac'])?'?nopac=1&ida='.$ida:'?ida='.$ida).'&dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
    else
      print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";

    if (isset($_GET['nopac']))
      {
	if ($user->rights->poa->prev->leer)
	  print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/liste.php'.(isset($_GET['nopac'])?'?nopac=1&idp='.$_GET['idp']:'').'&dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
	else
	  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";
      }
    print "</div>";

    //lista los contratos registrados
    //se cambio idp por idpa segun corresponda
    $objpcon->getlist2(($idpa?$idpa:$idp));

    if (count($objpcon->array) > 0)
      {
	print '<table class="border" style="min-width=1000px" width="100%">';
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Contrat"),"", "","","",'');
	print_liste_field_titre($langs->trans("Company"),"", "","","",'');
	print_liste_field_titre($langs->trans("Date"),"", "","","",'');
	print_liste_field_titre($langs->trans("Amount"),"", "","","",'align="right"');
	print_liste_field_titre($langs->trans("Nro.Aut"),"", "","","",'align="center"');
	print_liste_field_titre($langs->trans("Invoice"),"", "","","",'align="center"');
	print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
	print '</tr>';

	$var = true;
	foreach((array) $objpcon->array AS $j => $objpcontrat)
	  {
	    $total_ht = 0;
	    $total_tva = 0;
	    $total_localtax1 = 0;
	    $total_localtax2 = 0;
	    $total_ttc = 0;
	    $total_plazo = 0;
	    $date_final = '';
	    $contratAdd = '';
	    $objcont = new Contrat($db);
	    $objcont->fetch($objpcontrat->fk_contrat);
	    $objcont->fetch_lines();
	    if ($res < 0) { dol_print_error($db,$objcont->error); exit; }
	    $res=$objcont->fetch_optionals($objcont->id,$extralabels);
	    if ($objcont->id == $objpcontrat->fk_contrat)
	      {
		$total_plazo += $objcont->array_options['options_plazo'];
		$advance = $objcont->array_options['options_advance'];
		//recuperamos el valor de contrato
		foreach ($objcont->lines AS $olines)
		  {
		    $total_ht += $olines->total_ht;
		    $total_tva += $olines->total_tva;
		    $total_localtax1 += $olines->total_localtax1;
		    $total_localtax2 += $olines->total_localtax2;
		    //$total_ttc += $olines->total_ttc;
		  }
		//definimos fecha de vencimiento
		$dateini = $objcont->date_contrat;
		//buscamos si tiene addendum
		if ($conf->addendum->enabled)
		  {
		    $objadden = new Addendum($db);
		    if ($objadden->getlist($objpcontrat->fk_contrat)>0)
		      {
			$total_ht += $objadden->total_ht;
			$total_tva += $objadden->total_tva;
			$total_localtax1 += $objadden->total_localtax1;
			$total_localtax2 += $objadden->total_localtax2;
			$total_ttc += $objadden->aSuma['parcial_ttc'][$objpcontrat->fk_contrat];
			//verificamos los plazos adicionales
			foreach ((array) $objadden->array AS $j1 => $obja)
			  {
			    $objcontade = new Contrat($db);
			    $objcontade->fetch($obja->fk_contrat_son);
			    if ($objcontade->id == $obja->fk_contrat_son)
			      $total_plazo += $objcontade->array_options['options_plazo'];
			    if (!empty($contratAdd))$contratAdd.=', ';
			    $contratAdd.= $objcontade->array_options['options_ref_contrato'];
			    $total_ttc+= $objadden->aSuma['parcial_ttc'][$obja->fk_contrat_son];
			  }
		      }
		    else
		      {
			//recuperamos el valor de contrato
			foreach ($objcont->lines AS $olines)
			  {
			    $total_ht += $olines->total_ht;
			    $total_tva += $olines->total_tva;
			    $total_localtax1 += $olines->total_localtax1;
			    $total_localtax2 += $olines->total_localtax2;
			    $total_ttc += $olines->total_ttc;
			  }
		      }
		  }
		else
		  {
		    //recuperamos el valor de contrato
		    foreach ($objcont->lines AS $olines)
		      {
			$total_ht += $olines->total_ht;
			$total_tva += $olines->total_tva;
			$total_localtax1 += $olines->total_localtax1;
			$total_localtax2 += $olines->total_localtax2;
			$total_ttc += $olines->total_ttc;
		      }
		  }
		//procesamos el tiempo de entrega
		if ($objcont->array_options['options_cod_plazo']==1)
		  {
		    $datefinal = diacalend($objcont->date_contrat,$total_plazo);
		  }
		else if ($objcont->array_options['options_cod_plazo']==2)
		  {
		    $datefinal = diahabil($objcont->date_contrat,$total_plazo);
		  }
		else
		  $datefinal = '';

		$var=!$var;
		print "<tr $bc[$var]>";
		print '<td>'.'<a href="fiche_pas1.php?action=selcon&id='.$object->id.'&idrc='.$objpcontrat->id.(isset($_GET['nopac'])?'&nopac=1&idp='.$_GET['idp']:'').'&dol_hide_leftmenu=1">'.$objcont->array_options['options_ref_contrato'].($contratAdd?', '.$contratAdd:'').'</a></td>';
		if ($objsoc->fetch($objcont->fk_soc))
		  print '<td>'.$objsoc->nom.'</a></td>';
		else
		  print '<td>&nbsp;</td>';

		print '<td>'.dol_print_date($objcont->date_contrat,'day').' / Plazo '.$total_plazo.' '.$aPlazo[$objcont->array_options['options_cod_plazo']].'; '.$langs->trans('Delivery date').': '.dol_print_date($datefinal,'day').'</td>';

		// if ($conf->contratadd->enabled)
		//   {
		//     $objcontratadd = new Contratadd($db);
		//     $objcontratadd->get_suma_contratdet($objpcontrat->fk_contrat);

		//     print '<td align="right">'.price($objcontratadd->total_ttc).'</td>';
		//   }
		// else
		//   print '<td align="right">'.price(0).'</td>';

		// if ($conf->addendum->enabled)
		//   {
		//     $objadden = new Addendum($db);
		//     $objadden->getlist($objpcontrat->fk_contrat);
		//     print '<td align="right">AD '.price($objadden->total_ttc).'</td>';
		//   }
		// else
		print '<td align="right"> '.price($total_ttc).'</td>';

		//		print '<td>&nbsp;</td>';
		print '<td>&nbsp;</td>';
		print '<td>&nbsp;</td>';

		if (empty($action) && $user->rights->poa->deve->crear)
		  if ($user->admin || $user->id == $objprev->fk_user_create)
		    print '<td align="center">'.'<a href="fiche_pas2.php?id='.$id.'&idrc='.$objpcontrat->id.'&idp='.$idp.'&action=create'.(isset($_GET['nopac'])?'&nopac=1&idp='.$_GET['idp']:'').'&dol_hide_leftmenu=1">'.img_picto($langs->trans('Pay'),DOL_URL_ROOT.'/poa/img/payment.png','',1).'</a>'.'</td>';
		  else
		    print '<td align="right">'.'<a href="#">'.img_picto($langs->trans('Payment'),DOL_URL_ROOT.'/poa/img/payment.png','',1).'</a>'.'</td>';

		print '</tr>';
		/*
		 * new authorization payment
		 */
		if ($action == 'create' && $idrc == $objpcontrat->id && $user->rights->poa->deve->crear)
		  {

		    $saldo = 0;
		    foreach ((array) $aPrev AS $fk_poaprev => $gest)
		      {
			$objcompr = new Poapartidacom($db);
			if ($objcompr->get_sum_pcp2($fk_poaprev,$objpcontrat->fk_contrat))
			  {
			    //total comprometido
			    $totalcomp = $objcompr->total;
			    $aTotalcomp[$gest] = $objcompr->aTotal[$gest];
			    //array de comprom
			    $aObjcomp[$gest] = $objcompr;
			  }
			$objdeveng = new Poapartidadev($db);
			$lAdvance = false;
			if ($advance>0) $lAdvance = true;
			if ($objdeveng->get_sum_pcp2($fk_poaprev,$objpcontrat->fk_contrat,$lAdvance))
			  {
			    //total devengado
			    $totaldev += $objdeveng->total;
			    $aTotaldev[$gest]+= $objdeveng->aTotal[$gest];
			    $aObjdev[$gest] = $objdeveng;
			  }
		      }
		    $saldo = price2num($totalcomp - $totaldev,'MT');
		    // echo '<pre>';
		    // print_r($aTotalcomp);
		    // print_r($aTotaldev);
		    // echo '</pre>';
		    // echo '<hr>'.$aTotalcomp[$gestion].' '.$totaldev;

 		    $saldo = price2num($aTotalcomp[$gestion] - $aTotaldev[$gestion],'MT');
		    //cambiar
		    //$saldo = 1000;
		    // //determinamos saldo
		    // foreach ($objcompr->array AS $icomp => $objComppart)
		    //   {
		    // 	$saldo += $objComppart->amount;
		    // 	if (count($objdeveng->array)>0)
		    // 	  {
		    // 	    foreach($objdeveng->array AS $ideve => $objDevepart)
		    // 	      {
		    // 		if ($objDevepart->partida == $objComppart->partida &&
		    // 		    $objDevepart->fk_poa_partida_com == $objComppart->rowid)
		    // 		  {
		    // 		    $saldo-= $objDevepart->amount;
		    // 		  }
		    // 	      }
		    // 	  }
		    //   }
		    //		    echo '<hr>saldo '.$saldo;
		    if (price2num($saldo,'MT') > 0)
		      {
			include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/addpayment.tpl.php';
		      }
		    else
		      {
			//actualizamos el estado del proceso a pagado
			if ($object->statut == 1)
			  {
			    $object->statut = 2;
			    $object->update($user);
			  }
		      }
		  }

		//listamos todos los pagos
		//echo $object->fk_poa_prev;
		//listamos todos los pagos de todas las gestiones
		foreach ((array) $aPrev AS $fk_poaprev => $gest)
		  {
		    //echo '<hr>'.$gest.' '.$fk_poaprev;
		    $objprevnew = new Poaprev($db);
		    $objprevnew->fetch($fk_poaprev);
		    $objdev->getlist2($fk_poaprev,$objpcontrat->fk_contrat/*$idc*/);
		    $nLen = count($objdev->array);
		    if (count($objdev->array) > 0)
		      {
			$nLoop = 1;
			$var_ = true;
			foreach((array) $objdev->array AS $j => $objdeve)
			  {
			    //echo '<br> | x'.$gest;
			    $var_ = !$var_;
			    if ($idr == $objdeve->id && $action == 'editdev')
			      {
				$objdev = $objdeve;
				include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/editpayment.tpl.php';
			      }
			    else
			      {
				print '<tr '.classpoa($var_).'>';
				// contratos
				print '<tr>';
				print '<td>'.$objcont->array_options['options_ref_contrato'].'</td>';
				print '<td>'.$objsoc->nom.'</td>';

				//fecha autorizacion
				print '<td>';
				print dol_print_date($objdeve->date_dev,'day');
				print '</td>';

				//monto autorizado
				print '<td align="right">';
				print price(price2num($objdeve->amount,'MT'));
				print '</td>';

				//nro autorizacion
				print '<td align="center">';
				print $objdeve->nro_dev.'/'.$objdeve->gestion;
				print '</td>';

				// //nro partida
				// print '<td align="center">';
				// print $objdeve->partida;
				// print '</td>';

				//nro documento respaldo
				print '<td align="center">';
				print $objdeve->invoice;
				print '</td>';

				print '<td align="center">';
				if ($user->rights->poa->deve->mod ||
				    ($nLoop == $nLen && $user->id == $objprevnew->fk_user_create))
				  {
				    if ($gestion == $gest)
				      print '<a href="fiche_pas2.php?id='.$id.'&idr='.$objdeve->id.'&idp='.$objprevnew->id.'&action=editdev'.'&dol_hide_leftmenu=1">'.img_picto($langs->trans("Edit"),'edit').'</a>';
				    print '&nbsp;';
				  }
				if ($user->rights->poa->deve->del)
				  {
				    if ($gestion == $gest)
				      print '<a href="fiche_pas2.php?id='.$id.'&idr='.$objdeve->id.'&action=deletedev'.'&dol_hide_leftmenu=1">'.img_picto($langs->trans("Delete"),'delete').'</a>';
				  }
				else
				  print '&nbsp;';
				//exporta excel
				print '&nbsp;';

				print '<a href="fiche_autpay.php?id='.$id.'&idr='.$objdeve->id.'&dol_hide_leftmenu=1">'.img_picto($langs->trans("Authorization by payment"),DOL_URL_ROOT.'/poa/img/excel-icon','',1).'</a>';
				print '&nbsp;';

				//imagen

				$dir = $conf->poa->dir_output.'/payment/pdf/'.$objdeve->id.'.pdf';
				$url = DOL_URL_ROOT.'/documents/poa/payment/pdf/'.$objdeve->id.'.pdf';
				//$idr = $objdeve->id;
				if ($user->admin || $user->rights->poa->deve->mod)
				  if ($action == 'upload' && $idr == $objdeve->id)
				    {
				      include DOL_DOCUMENT_ROOT.'/poa/process/tpl/addpdf.tpl.php';
				    }
				  else
				    {
				      print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objdeve->id.'&action=upload'.'&dol_hide_leftmenu=1">'.img_picto($langs->trans('Uploaddoc'),DOL_URL_ROOT.'/poa/img/subir.png','',1).'</a>';
				      //mostramos el archivo
				      if (file_exists($dir))
					{
					  print '&nbsp;&nbsp;';
					  print '<a href="'.$url.'" target="_blank">'.img_picto($langs->trans('PDF'),'pdf2').'</a>';
					}
				    }
				else
				  {
				    //mostramos el archivo
				    if (file_exists($dir))
				      {
					print '&nbsp;&nbsp;';
					print '<a href="'.$url.'" target="_blank">'.img_picto($langs->trans('PDF'),'pdf2').'</a>';
				      }
				  }

				print '</td>';
				print '</tr>';
			      }
			    $nLoop++;
			  }
		      }
		  }
	      }

	  }
	print '</table>';
      }
  }

llxFooter();

$db->close();
?>
