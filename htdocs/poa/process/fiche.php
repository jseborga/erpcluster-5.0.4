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

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';
//require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprevprocess.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivity.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/pac/class/poapac.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';
require_once DOL_DOCUMENT_ROOT.'/poa/lib/doc.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';

$langs->load("poa@poa");

$action=GETPOST('action');

$id        = GETPOST("id");
$ida       = GETPOST("ida");//actividad
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$gestion   = GETPOST("gestion");
$fk_poa_prev = GETPOST("fk_poa_prev");
$action    = GETPOST('action');

if (empty($gestion)) $gestion = date('Y');
$idArea = 3; //generar funcion para recuperar por usuario


$mesg = '';

$object  = new Poaprocess($db);
$objarea = new Poaarea($db);
$objuser = new User($db);
$objprev = new Poaprev($db);
$objpac  = new Poapac($db);
$objact  = new Poaactivity($db);

if ($action == 'search')
  $action = 'createedit';
/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->poa->proc->crear)
  {
	$error = 0;
	$object->date_process = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	if (!$user->admin)
	  $object->date_process = dol_now();
	$object->gestion   = GETPOST('gestion');
	$object->ref   = 0;

	$object->fk_area   = GETPOST('fk_area');
	$object->fk_poa_prev   = GETPOST('fk_poa_prev');
	$object->fk_area   = GETPOST('fk_area');
	$object->amount   = GETPOST('amount');
	$object->fk_type_con  = GETPOST('fk_type_con'); //tipo contrato
	$object->fk_type_adj   = GETPOST('fk_type_adj');
	$object->label   = GETPOST('label');
	$object->justification   = GETPOST('justification');
	$object->term    = GETPOST('term')+0;
	$object->ref_pac    = GETPOST('ref_pac');


	$object->doc_precio_referencial   = GETPOST('doc_precio_referencial')+0;
	$object->doc_certif_presupuestaria   = GETPOST('doc_certif_presupuestaria')+0;
	$object->doc_especific_tecnica   = GETPOST('doc_especific_tecnica')+0;
	$object->doc_modelo_contrato   = GETPOST('doc_modelo_contrato')+0;
	$object->doc_informe_lega   = GETPOST('doc_informe_lega')+0;
	$object->doc_precio_referencial   = GETPOST('doc_precio_referencial')+0;
	$object->doc_pac   = GETPOST('doc_pac')+0;
	$object->doc_prop  = GETPOST('doc_prop')+0;
	$object->fk_soc    = GETPOST('fk_soc')+0;

	$object->metodo_sel_anpe   = GETPOST('metodo_sel_anpe')+0;
	$object->metodo_sel_lpni   = GETPOST('metodo_sel_lpni')+0;
	$object->metodo_sel_cae    = GETPOST('metodo_sel_pemb')+0;
	//$object->metodo_sel_cae    = GETPOST('metodo_sel_cae')+0;

	$object->entity = $conf->entity;
	$object->date_create = dol_now();
	$object->fk_user_create = $user->id;
	$object->tms = dol_now();
	$object->statut = 0;
	if (empty($object->fk_area))
	  {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorareaisrequired").'</div>';
	  }
	if (empty($object->label))
	  {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorlabelrequired").'</div>';
	  }
	if (empty($object->justification))
	  {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorjustificationrequired").'</div>';
	  }
	//analizamos el tipo de contrato
	$aTable = fetch_tables($object->fk_type_con);
	if ($aTable['id'] == $object->fk_type_con)
	  {
	if ($object->amount >= $aTable['range_ini'] &&
		$object->amount <= $aTable['range_fin'])
	  {
		//esta en los rangos
	  }
	else
	  {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Errortypecontratnotvalid").'</div>';
	  }
	  }
	//revisamos si el tipo contrato del pac esta idem al seleccionado
	if ($objprev->fetch($fk_poa_prev))
	  {
	if ($objprev->fk_poa > 0)
	  {
		//buscamos el pac
		if ($objpac->fetch($objprev->fk_poa))
		  {
		//verificamos
		if ($objpac->fk_type_modality != $object->fk_type_con)
		  {
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorthetypeofcontratrequiresupdateingpac").'</div>';
		  }
		  }
		else
		  {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Errorpacnotexist").'</div>';
		  }
	  }
	  }

	if (empty($error))
	  {
	$id = $object->create($user);
	if ($id > 0)
	  {
		$_SESSION['aListip'][$id]['idPrev'] = $fk_poa_prev;
		$_SESSION['aListip'][$id]['idAct'] = $ida;
		header("Location: fiche.php?id=".$id);
		exit;
	  }
	$action = 'create';
	$mesg='<div class="error">'.$object->error.'</div>';
	  }
	else
	  {
	if ($error)
	  $action="create";   // Force retour sur page creation
	  }
  }

//uppdf
if ($action == 'uppdfprocess')
{
	$linklast = GETPOST('linklast','alpha');
	if ($object->fetch($_POST["idreg"])>0)
	{
		// Logo/Photo save
		$dir = $conf->poa->dir_output.'/process/pdf';
		$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
		if ($file_OKfin)
		{
			if (doc_format_supported($_FILES['docpdf']['name']) > 0)
			{
				dol_mkdir($dir);
				if (@is_dir($dir))
				{
					$newfile=$dir.'/'.dol_sanitizeFileName($_FILES['docpdf']['name']);
					$newfile=$dir.'/'.dol_sanitizeFileName($object->fk_poa_prev.'.pdf');
					$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
					if (! $result > 0) $errors[] = "ErrorFailedToSaveFile";
					else
					{
						$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
						// Create mini thumbs for company (Ratio is near 16/9)
						// Used on menu or for setup page for example
						$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
					}
					if ($linklast)
					{
						header('Location: '.$linklast);
						exit;
					}
					header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
					exit;
				}
			}
			else
				$errors[] = "ErrorBadImageFormat";
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
	if ($linklast)
	{
		header('Location: '.$linklast);
		exit;
	}
}

// Delete process
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->proc->del)
  {
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	  {
	header("Location: ".DOL_URL_ROOT.'/poa/process/liste.php');
	exit;
	  }
	else
	  {
	$mesg='<div class="error">'.$object->error.'</div>';
	$action='';
	  }
  }

// Cancel process

if ($action == 'confirm_cancel' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->proc->nul)
  {
	$object->fetch($_REQUEST["id"]);
	$object->statut = -1;
	$result=$object->update($user);
	if ($result > 0)
	  {
	header("Location: ".DOL_URL_ROOT.'/poa/execution/ficheprev.php?id='.$object->fk_poa_prev);
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
	$error = 0;
	$object->date_process = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$object->gestion   = GETPOST('gestion');
	$object->ref   = GETPOST('ref');

	$object->fk_area   = GETPOST('fk_area');
	$object->fk_poa_prev   = GETPOST('fk_poa_prev');
	$object->fk_area   = GETPOST('fk_area');
	$object->amount   = GETPOST('amount');
	$object->fk_type_con  = GETPOST('fk_type_con');
	$object->fk_type_adj   = GETPOST('fk_type_adj');
	$object->label   = GETPOST('label');
	$object->justification   = GETPOST('justification');
	$object->term    = GETPOST('term')+0;
	$object->ref_pac    = GETPOST('ref_pac');


	$object->doc_precio_referencial   = GETPOST('doc_precio_referencial')+0;
	$object->doc_certif_presupuestaria   = GETPOST('doc_certif_presupuestaria')+0;
	$object->doc_especific_tecnica   = GETPOST('doc_especific_tecnica')+0;
	$object->doc_modelo_contrato   = GETPOST('doc_modelo_contrato')+0;
	$object->doc_informe_lega   = GETPOST('doc_informe_lega')+0;
	$object->doc_precio_referencial   = GETPOST('doc_precio_referencial')+0;
	$object->doc_pac   = GETPOST('doc_pac')+0;
	$object->doc_prop  = GETPOST('doc_prop')+0;
	$object->fk_soc    = GETPOST('fk_soc')+0;

	$object->metodo_sel_anpe   = GETPOST('metodo_sel_anpe')+0;
	$object->metodo_sel_lpni   = GETPOST('metodo_sel_lpni')+0;
	$object->metodo_sel_cae    = GETPOST('metodo_sel_pemb')+0;
	$object->metodo_sel_cae    = GETPOST('metodo_sel_cae')+0;

	$object->tms = date('YmdHis');
	$object->statut = 0;
	if (empty($object->fk_area))
	  {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Errorareaisrequired").'</div>';
	  }
	if (empty($object->label))
	  {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Errorlabelrequired").'</div>';
	  }
	if (empty($object->justification))
	  {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Errorjustificationrequired").'</div>';
	  }
	//revisamos si el tipo contrato del pac esta idem al seleccionado
	if ($objprev->fetch($fk_poa_prev))
	  {
		if ($objprev->fk_pac > 0)
		  {
		//buscamos el pac
		if ($objpac->fetch($objprev->fk_pac))
		  {
			//verificamos
			if ($objpac->fk_type_modality != $object->fk_type_con)
			  {
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorrequiresupdatingthepac").'</div>';
			  }
		  }
		else
		  {
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorpacnotexist").'</div>';
		  }
		  }
	  }
	if (empty($error))
	  {
		if ( $object->update($_POST["id"], $user) > 0)
		  {
		$action = '';
		$_GET["id"] = $_POST["id"];
		//$mesg = '<div class="ok">Fiche mise a jour</div>';
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

if ( ($action == 'createedit') )
  {
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	//$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
	$tmparray['fk_poa_prev'] = GETPOST('fk_poa_prev');
	$tmparray['amount'] = GETPOST('amount');

	if (!empty($tmparray['fk_poa_prev']))
	  {
	//buscamos
	if ($objprev->fetch($tmparray['fk_poa_prev']))
	  {
		$fk_poa_prev = $tmparray['fk_poa_prev'];
		$tmparray['gestion'] = $objprev->gestion;
		$tmparray['fk_pac'] = $objprev->fk_pac;
		$tmparray['fk_area'] = $objprev->fk_area;
		//recuperamos la gerencia, subgerencia y dpto
		$tmparray = $objarea->getarea($objprev->fk_area,$tmparray);
		$tmparray['label'] = $objprev->label;
		$tmparray['nro_preventive'] = $objprev->nro_preventive;
		if (empty($tmparray['amount']))
		  $tmparray['amount'] = $objprev->amount;
		$tmparray['fk_user_create'] = $objprev->fk_user_create;

		//buscamos el pac si corresponde
		if ($objprev->fk_pac > 0)
		  {
		if ($objpac->fetch($objprev->fk_pac))
		  {
			$tmparray['fk_poa'] = $objpac->fk_poa;

			$tmparray['fk_type_con'] = $objpac->fk_type_modality;
			$tmparray['fk_type_object'] = $objpac->fk_type_object;
			$tmparray['partida'] = $objpac->partida;
			$tmparray['ref_pac'] = $objpac->ref.': '.$objpac->nom;
		  }
		else
		  $tmparray['ref_pac'] = $langs->trans('Notrequired');

		  }
		$object->fk_poa_prev = $tmparray['fk_poa_prev'];
		$object->gestion = $tmparray['gestion'];
		$object->fk_pac = $tmparray['fk_pac'];
		$object->fk_type_con = $tmparray['fk_type_con'];
		$object->fk_area = $tmparray['fk_area'];
		$object->label = $tmparray['label'];
		$object->nro_preventive = $tmparray['nro_preventive'];
		$object->area = $tmparray['area'];
		$object->fk_user_create = $tmparray['fk_user_create'];
		$object->ref_pac = $tmparray['ref_pac'];

		$object->amount = $tmparray['amount'];
	  }
	  }
	$action='create';
  }



if ($_POST["cancel"] == $langs->trans("Cancel"))
  {
	$action = '';
	$_GET["id"] = $_POST["id"];
  }
// print_r($_POST);
// exit;
if (!empty($id))
  {
	$ida = $_SESSION['aListip'][$id]['idAct'];
	$idp = $_SESSION['aListip'][$id]['idPrev'];
	$idc = $_SESSION['aListip'][$id]['idContrat'];
  }

/*
 * View
 */

$form=new Form($db);

//$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
//$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviaproc.js','poa/js/poa.js','poa/js/scriptajax.js');

// $aArrcss= array('poa/css/style.css');
// $aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviaproc.js');
//$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
//llxHeader("",$langs->trans("POA"),$help_url,'','','',$aArrjs,$aArrcss);
//cabecera
header("Content-type: text/html; charset=".$conf->file->character_set_client);

$aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/bootstrap-responsive.min.css','poa/css/style-responsive.css','poa/css/AdminLTE.css');
$aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/dist/css/AdminLTE.css','poa/css/dist/css/AdminLTE.min.css','poa/css/dist/css/skins/_all-skins.min.css');
$aArrayofjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');

top_htmlhead($head,$langs->trans("POA"),0,0,$aArrayofjs,$aArrayofcss);

//impresion de submenu segun seleccion
include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/menup.tpl.php';
print '<br>';
print '<br>';
print '<br>';
?>

<iframe id="iframe" src="actualiza_proc.php" width="0" height="0" frameborder="0"></iframe>

<script type="text/javascript">
  function CambiarURLFramecuce(id,idReg,rowid,di_){
	  var idTwo = idReg;
	  var idOne = id;
	  if (di_ ==="")
	{

	}
	  else
	{
	  document.getElementById(idTwo).innerHTML = di_;
	}
	  //cambiando el estado de
	  visual_four(idReg,id);
	  document.getElementById('iframe').src= 'actualiza_proc.php?id='+rowid+'&di_'+rowid+'='+di_+'&action=updateproc';
	  window.location.reload();
}
</script>

<script type="text/javascript">
  function CambiarURLFramecode(id,idReg,rowid,di_){
	  var idTwo = idReg;
	  var idOne = id;
	  if (di_ ==="")
	{

	}
	  else
	{
	  document.getElementById(idTwo).innerHTML = di_;
	}
	  //cambiando el estado de
	  visual_four(idReg,id);
	  document.getElementById('iframe').src= 'actualiza_proc.php?id='+rowid+'&df_'+rowid+'='+di_+'&action=updatecode';
	  window.location.reload();
}
</script>

<?php
  //definimos fecha para sacar un tipo de formulario
  //hasta el 31/08/215  $lForm = true;
  //desde el 1/09/2015 $lForm = false;
$aDatea = dol_getdate(dol_now());
$aDateact = dol_mktime(23, 59, 59, $aDatea['mon'],$aDatea['mday'],$aDatea['year']);

$aDatelim = dol_mktime(23, 59, 59, 8,31,2015);
if ($aDateact > $aDatelim)
  $lForm = false;
 else
   $lForm = true;
if ($action == 'create' && $user->rights->poa->proc->crear)
  {
	print_fiche_titre($langs->trans("Newprocess"));
	//search
	print '<form  action="fiche.php" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="create">';
	print '<input type="hidden" name="ida" value="'.$ida.'">';
	print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

	print '<table class="noborder">';
	// search
	print '<tr><td width="12%" class="fieldrequired">'.$langs->trans('Searchpreventive').'</td><td>';
	print '<input id="search" type="text" value="'.$search.'" name="search" size="75" maxlength="70">';
	print '</td>';
	print '<td width="60%" align="left"><input type="submit" class="button" value="'.$langs->trans("Search").'">';
	print '</td>';

	print '</tr>';
	print '</table>';
	print '</form>';
	$search = GETPOST('search');
	if (!empty($search))
	  {
	$objprev->search($search,$gestion,1);
	if (count($objprev->aArray) > 0)
	  {
		print '<table class="border" style="min-width=1000px" width="100%">';
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Number"),"", "","","",'');
		print_liste_field_titre($langs->trans("Label"),"", "","","",'');
		print_liste_field_titre($langs->trans("Status"),"", "","","",'');
		print '</tr>';

		$var = true;
		foreach ($objprev->aArray AS $idPrev)
		  {
		$objsprev = new Poaprev($db);
		$objsprev->fetch($idPrev);
		if ($objsprev->id == $idPrev)
		  {
			$var=!$var;
			print "<tr $bc[$var]>";
			if ($objsprev->statut == 1)
			  print '<td>'.'<a href="fiche.php?action=search&fk_poa_prev='.$objsprev->id.'">'.$objsprev->nro_preventive.'</a></td>';
			else
			  print '<td>'.$objsprev->nro_preventive.'</td>';

			if ($objsprev->statut == 1)
			  print '<td>'.'<a href="fiche.php?action=search&fk_poa_prev='.$objsprev->id.'">'.$objsprev->label.'</a></td>';
			else
			  print '<td>'.$objsprev->id.' '.$objsprev->label.'</td>';
			print '<td>'.$objsprev->LibStatut($objsprev->statut).'</td>';

			print '</tr>';
		  }
		  }
		print '<table>';
	  }

	  }
	if ($fk_poa_prev)
	  {
	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
				$("#selectfk_type_con").change(function() {
				  document.fiche_process.action.value="createedit";
				  document.fiche_process.submit();
				});
				$("#amount").change(function() {
				  document.fiche_process.action.value="createedit";
				  document.fiche_process.submit();
				});

			});';
	print '</script>'."\n";

	print '<form id="fiche_process" name="fiche_process" action="fiche.php" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="fk_poa_prev" value="'.$fk_poa_prev.'">';
	print '<input type="hidden" name="ida" value="'.$ida.'">';
	print '<input type="hidden" name="fk_area" value="'.$object->fk_area.'">';
	print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" style="min-width=1000px" width="100%">';
	// // preventivo
	// print '<tr><td class="fieldrequired">'.$langs->trans('Preventive').'</td><td colspan="3">';
	// print $objprev->select_poa_prev($object->fk_poa_prev,'fk_poa_prev','',40,1,$gestion,$idArea,1);
	// print '</td></tr>';
	if ($object->fk_poa_prev)
	  {
		//mostramos
		//preventivo seleccionado
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Number"),"", "","","",'');
		print_liste_field_titre($langs->trans("Preventive"),"", "","","",'colspan="3"');
		print '</tr>';
		if ($objprev->fetch($object->fk_poa_prev))
		  {
		print '<tr>';
		print '<td>'.$objprev->nro_preventive.'</td>';
		print '<td colspan="3">'.$objprev->label.'</td>';
		print '</tr>';
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

		//ref
		print '<tr><td width="150px" class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="3">';
		print '(PROV)';
		print '<input type="hidden" name="ref" value="0">';
		print ' / ';
		print '<input type="year" name="gestion" value="'.$object->gestion.'" size="2" maxlenght="4">';
		print '</td></tr>';

		//date
		print '<tr><td width="150px" class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="3">';
		if ($user->admin)
		  $form->select_date($object->date_process,'di_','','','',"date",1,1);
		print '</td></tr>';

		//amount
		print '<tr><td width="12%" class="fieldrequired">'.$langs->trans('Reference price').'</td><td colspan="3">';
		print '<input id="amount" type="text" value="'.$object->amount.'" name="amount" size="15" maxlength="12">';
		print '</td></tr>';

		//type modality
		print '<tr><td class="fieldrequired">'.$langs->trans('Modality').'</td><td colspan="3">';
		if ($_POST['fk_type_con'] != $object->fk_type_con && !empty($_POST['fk_type_con']))
		  $object->fk_type_con = $_POST['fk_type_con'];
		print select_tables($object->fk_type_con,'fk_type_con','',1,0,'05',$object->amount);
		print '</td></tr>';

		//label
		print '<tr><td class="fieldrequired">'.$langs->trans('Title').'</td><td colspan="3">';
		print '<input id="label" type="text" value="'.$object->label.'" name="label" size="120" maxlength="255">';
		print '</td></tr>';

		//justification
		print '<tr><td class="fieldrequired">'.$langs->trans('Justification').'</td><td colspan="3">';
		print '<input id="justification" type="text" value="'.$object->justification.'" name="justification" rows="2" cols="40" >';
		print '</td></tr>';

		//type adj
		print '<tr><td class="fieldrequired">'.$langs->trans('Type of adjudication').'</td><td>';
		print select_tables((empty($object->fk_type_adj)?3:$object->fk_type_adj),'fk_type_adj','',0,0,'01');
		print '</td>';
		print '<td colspan="2">'.$langs->trans('Refpac').': ';
		print '<input id="ref_pac" type="text" value="'.$object->ref_pac.'" name="ref_pac" size="90" maxlength="255">';
		print '</td>';

		print '</tr>';

		//respaldo de documentos segun tipo de contratacion
		//buscamos el tipo de contratacion
		$aTable = fetch_tables($object->fk_type_con);
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Necessary documentation"),"", "","","",'colspan="4"');
		print '</tr>';
		print '<tr class="liste_titre">';
		print_liste_field_titre('&nbsp;',"", "","","",'colspan="3"');
		print_liste_field_titre($aTable['label'],"", "","","",'align="center" width="20%"');
		print '</tr>';
		//generico
		if ($aTable['type'] == 'MENSPAC' || $aTable['type'] == 'MEN')
		  $value = 1;
		elseif (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY')
		  $value = 2;
		elseif (STRTOUPPER($aTable['type']) == 'LP')
		  $value = 3;
		elseif (STRTOUPPER($aTable['type']) == 'DIREC' || STRTOUPPER($aTable['type']) == 'EXCEP')
		  $value = 4;
		elseif (STRTOUPPER($aTable['type']) == 'CAE')
		  $value = 5;

		//type certif presup
		print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_cp').'</td><td align="center">';
		print '<input type="checkbox" name="doc_certif_presupuestaria" value="'.$value.'" checked="checked">';
		print '</td></tr>';

		//precio referencial
		print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_pr').'</td><td align="center">';
		print '<input type="checkbox" name="doc_precio_referencial" value="'.$value.'" checked="checked">';
		print '</td></tr>';

		//type especif tecnica
		print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_et').'</td><td align="center">';
		print '<input type="checkbox" name="doc_especific_tecnica" value="'.$value.'" checked="checked">';
		print '</td></tr>';

		//modelo contrato
		if ($lForm)
		  {
		print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Modelo de Contrato elaborado por la GAL').'</td><td nowrap align="center">';
		print '<input type="checkbox" name="doc_modelo_contrato" value="'.$value.'">';
		  }
		else
		  {
		if (STRTOUPPER(trim($aTable['type'])) == 'ANPEMEN' ||
			STRTOUPPER(trim($aTable['type'])) == 'ANPEMAY' ||
			STRTOUPPER(trim($aTable['type'])) == 'LP' ||
			STRTOUPPER(trim($aTable['type'])) == 'CEA' )
		  {
			print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Modelo de Contrato elaborado por la GAL').'</td><td nowrap align="center">';
			print '<input type="checkbox" name="doc_modelo_contrato" value="'.$value.'">';
		  }
		  }
		if ($lForm)
		  {
		if (STRTOUPPER(trim($aTable['type'])) == 'ANPEMEN' || STRTOUPPER(trim($aTable['type'])) == 'ANPEMAY')
		  print '&nbsp;<a href="#">'.img_picto($langs->trans("help_anpe"),'help').'</a>';
		elseif (STRTOUPPER(trim($aTable['type'])) == 'DIREC')
		  print '&nbsp;<a href="#">'.img_picto($langs->trans("help_direc"),'help').'</a>';
		elseif (STRTOUPPER(trim($aTable['type'])) == 'MENSPAC' || STRTOUPPER(trim($aTable['type'])) == 'MEN')
		  print '&nbsp;<a href="#">'.img_picto($langs->trans("help_men"),'help').'</a>';
		  }
		print '</td></tr>';

		//fotocopia PAC
		if (!$lForm)
		  {
		$checked = '';
		if (STRTOUPPER(trim($aTable['type'])) == 'ANPEMEN' ||
			STRTOUPPER(trim($aTable['type'])) == 'ANPEMAY' ||
			STRTOUPPER(trim($aTable['type'])) == 'LP' )
		  $checked = ' checked="checked"';
		print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Fotocopia hoja PAC donde se encuentra incluido proceso de contratacion').'</td><td nowrap align="center">';
		print '<input type="checkbox" name="doc_pac" value="'.$value.'"'.$checked.'>';
		  }
		if (!$lForm)
		  {
		if (STRTOUPPER($aTable['type']) == 'DIREC' || STRTOUPPER($aTable['type']) == 'EXCEP')
		  {
			//informe tecnico LEGAL
			print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_it').'</td><td align="center">';
			print '<input type="checkbox" name="doc_informe_lega" value="'.$value.'">';
			print '</td></tr>';
			if (!$lForm)
			  {
			//Seleccion de mepresa proponente
			print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Proponente Seleccionado').'</td><td align="center">';
			print $form->select_company('','fk_soc','',1,0,0);
			print '</td></tr>';
			  }
		  }
		  }
		else
		  {
		if (STRTOUPPER($aTable['type']) != 'MENSPAC' &&
			STRTOUPPER($aTable['type']) != 'MEN')
		  {
			//informe tecnico LEGAL
			print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_it').'</td><td align="center">';
			print '<input type="checkbox" name="doc_informe_lega" value="'.$value.'">';
			print '</td></tr>';
		  }
		  }
		if (!$lForm)
		  {
		//lista de proponentes para CM
		if (STRTOUPPER($aTable['type']) == 'MENSPAC')
		  {
			print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Lista de proponentes').'</td><td align="center">';
			print '<input type="checkbox" name="doc_prop" value="'.$value.'">';
			print '</td></tr>';
		  }
		  }
		//metodo de seleccion
		if (STRTOUPPER($aTable['type']) != 'MENSPAC' &&
		STRTOUPPER($aTable['type']) != 'MEN' &&
		STRTOUPPER($aTable['type']) != 'DIREC' && !empty($object->fk_type_con))
		  {

		// print '<tr class="liste_titre">';
		// print_liste_field_titre($langs->trans("Selection method"),"", "","","",'colspan="4"');
		// print '</tr>';
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans('Method selection and award'),"", "","","",'colspan="3"');
		print_liste_field_titre('',"", "","","",'colspan="3"');
		print '</tr>';

		if (STRTOUPPER($aTable['type']) != 'CEA')
		  {
			include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/title_cea.tpl.php';
			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
			STRTOUPPER($aTable['type']) == 'ANPEMAY' ||
			STRTOUPPER($aTable['type']) == 'LP' )
			  {
			//calidad propuesta tecnica y costo
			print '<tr>';
			include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/cptc.tpl.php';
			print '<td align="center">';
			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			  {
				print '<input type="checkbox" name="metodo_sel_anpe" value="1">';
			  }
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			  {
				print '<input type="checkbox" name="metodo_sel_lpni" value="1">';
			  }
			elseif (STRTOUPPER($aTable['type']) == 'CAE' )
			  {
				print '<input type="checkbox" name="metodo_sel_cae" value="1">';
			  }
			print '</td></tr>';

			//calidad
			print '<tr>';
			include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/c.tpl.php';
			print '<td align="center">';

			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			  {
				print '<input type="checkbox" name="metodo_sel_anpe" value="2">';
			  }
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			  {
				print '<input type="checkbox" name="metodo_sel_lpni" value="2">';
			  }
			elseif (STRTOUPPER($aTable['type']) == 'CAE' )
			  {
				print '<input type="checkbox" name="metodo_sel_cae" value="1">';
			  }
			print '</td></tr>';

			//Presupuesto Fijo
			print '<tr>';
			include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/pf.tpl.php';

			print '<td align="center">';
			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			  {
				print '<input type="checkbox" name="metodo_sel_anpe" value="3">';
			  }
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			  {
				print '<input type="checkbox" name="metodo_sel_lpni" value="3">';
			  }
			elseif (STRTOUPPER($aTable['type']) == 'CAE' )
			  {
				print '<input type="checkbox" name="metodo_sel_cae" value="1">';
			  }
			print '</td></tr>';

			//Menor Costo
			print '<tr>';
			include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/mc.tpl.php';

			print '<td align="center">';

			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			  {
				print '<input type="checkbox" name="metodo_sel_anpe" value="4">';
			  }
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			  {
				print '<input type="checkbox" name="metodo_sel_lpni" value="4">';
			  }
			elseif (STRTOUPPER($aTable['type']) == 'CAE' )
			  {
				print '<input type="checkbox" name="metodo_sel_cae" value="1">';
			  }
			print '</td></tr>';

			//Prcio evaluado mas bajo (PEMB)
			print '<tr>';
			include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/pemb.tpl.php';

			print '<td align="center">';

			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			  {
				print '<input type="checkbox" name="metodo_sel_anpe" value="5">';
			  }
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			  {
				print '<input type="checkbox" name="metodo_sel_lpni" value="5">';
			  }
			elseif (STRTOUPPER($aTable['type']) == 'CAE' )
			  {
				print '<input type="checkbox" name="metodo_sel_cae" value="1">';
			  }
			print '</td></tr>';

			//formulario de condiciones... (PEMB)
			print '<tr><td  colspan="3">'.$langs->trans('Formulario de Condiciones Adicionales (Excepto para el metodo de PEMB)').'</td>';
			print '<td align="center">';

			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			  {
				print '<input type="checkbox" name="condicion_adicional_anpe" value="2">';
			  }
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			  {
				print '<input type="checkbox" name="condicion_adicional_lpni" value="3">';
			  }
			print '</td></tr>';

			  }
		  }
		else
		  {
			//modelo CAE
			print '<tr><td  colspan="3" class="fieldrequired">'.$langs->trans('mod_cae').'</td>';
			print '<td align="center">';

			print '<input type="checkbox" name="metodo_sel_cae" value="5">';
			print '</td></tr>';

		  }
		  }
	  }
	print '</table>';
	//echo '<hr>'.$object->fk_type_con.') && !empty('.$object->amount.') && !empty('.$object->label;
	if ( !empty($object->amount) && !empty($object->label))
	  print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
	print '</form>';

	print "<div class=\"tabsAction\">\n";
	if ($user->rights->poa->prev->leer)
	  {
		if ($objact->fetch('',$fk_poa_prev)>0)
		  {
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php'.(isset($_GET['nopac'])?'?nopac=1&ida='.$objact->id:'?ida='.$objact->id).'">'.$langs->trans("Return").'</a>';
		  }
	  }
	else
	  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";
	print '</div>';

	  }
  }
 else
   {
	 if ($id || $_GET['id'])
	   {
	 //dol_htmloutput_mesg($mesg);
	 if (empty($id)) $id = $_GET['id'];
	 $result = $object->fetch($id);
	 if ($result < 0)
	   {
		 dol_print_error($db);
	   }
	 //definimos fecha para sacar un tipo de formulario
	 //hasta el 31/08/215  $lForm = true;
	 //desde el 1/09/2015 $lForm = false;
	 $aDatelim = dol_mktime(23, 59, 59, 8,31,2015);
	 $aDateobj = dol_getdate($object->date_process);
	 $aDateobj = dol_mktime(0, 0, 1, $aDateobj['mon'],$aDateobj['mday'],$aDateobj['year']);
	 if ($aDateobj <= $aDatelim)
	   $lForm = true;
	 else
	   $lForm = false;
	 if ( ($action == 'createeditdos') )
	   {
		 $object->fetch($id);
		 require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
		 $tmparray['fk_type_con'] = GETPOST('fk_type_con');
		 $tmparray['fk_poa_prev'] = GETPOST('fk_poa_prev');
		 $tmparray['gestion'] = GETPOST('gestion');
		 $tmparray['ref'] = GETPOST('ref');
		 $tmparray['amount'] = GETPOST('amount');

		 $fk_poa_prev = GETPOST('fk_poa_prev');
		 if (!empty($tmparray['fk_poa_prev']))
		   {
		 //buscamos
		 if ($objprev->fetch($tmparray['fk_poa_prev']))
		   {
			 $tmparray['gestion'] = $objprev->gestion;
			 $tmparray['fk_pac'] = $objprev->fk_pac;
			 $tmparray['fk_area'] = $objprev->fk_area;
			 //recuperamos la gerencia, subgerencia y dpto
			 $tmparray = $objarea->getarea($objprev->fk_area,$tmparray);
			 $tmparray['label'] = $objprev->label;
			 $tmparray['nro_preventive'] = $objprev->nro_preventive;
			 if (empty($tmparray['amount']))
			   $tmparray['amount'] = $objprev->amount;
			 $tmparray['fk_user_create'] = $objprev->fk_user_create;
			 //buscamos el pac si corresponde
			 if ($objprev->fk_pac > 0)
			   {
			 if ($objpac->fetch($objprev->fk_pac))
			   {
				 $tmparray['fk_poa'] = $objpac->fk_poa;
				 //$tmparray['fk_type_con'] = $objpac->fk_type_modality;
				 $tmparray['fk_type_object'] = $objpac->fk_type_object;
				 $tmparray['partida'] = $objpac->partida;
				 $tmparray['ref_pac'] = $objpac->ref.': '.$objpac->nom;
			   }
			 else
			   $tmparray['ref_pac'] = $langs->trans('Notrequired');

			   }
			 $object->fk_poa_prev = $tmparray['fk_poa_prev'];
			 $object->gestion = $tmparray['gestion'];
			 $object->fk_pac = $tmparray['fk_pac'];
			 $object->fk_type_con = $tmparray['fk_type_con'];
			 $object->fk_area = $tmparray['fk_area'];
			 $object->label = $tmparray['label'];
			 $object->nro_preventive = $tmparray['nro_preventive'];
			 $object->area = $tmparray['area'];
			 $object->amount = $tmparray['amount'];
			 $object->gestion = $tmparray['gestion'];
			 $object->ref = $tmparray['ref'];
			 $object->fk_user_create = $tmparray['fk_user_create'];

			 $object->ref_pac = $tmparray['ref_pac'];
		   }
		   }
		 $action='edit';
	   }



	 /*
	  * Affichage fiche
	  */
	 if ($action <> 'edit' && $action <> 're-edit')
	   {
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
		 //update
		 if ($object->ref == 0)
		   {
			 $objectproces = new Poaprocess($db);
			 $objectproces->get_maxref($object->gestion);
			 $object->ref   = $objectproces->maximo;
		   }
		 $object->statut = 1;
		 $res = $object->update($user);
		 //VERIFICAR SI ES CORRECTO EL NO REGISTRO
		 // //creando la relacion de preventivo y proceso
		 // $objpp = new Poaprevprocess($db);
		 // $objpp->fk_poa_prev = $object->fk_poa_prev;
		 // $objpp->fk_poa_process = $object->id;
		 // $objpp->date_create = $object->date_process;
		 // $objpp->tms = date('YmdHis');
		 // $objpp->fk_user_create = $user->id;
		 // $objpp->statut = 1;
		 // echo $idpp = $objpp->create($user);
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

		 print '<table class="border" style="min-width=1000px" width="100%">';

		 // Confirm cancel proces
		 if ($action == 'anulate')
		   {
		 $form = new Form($db);
		 $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Cancelprocess"),$langs->trans("Confirmcancelprocess",$object->ref.' '.$object->detail),"confirm_cancel",'',0,2);
		 if ($ret == 'html') print '<br>';
		   }

		 print '<table class="border" style="min-width=1000px" width="100%">';

		 //mostramos
		 //preventivo seleccionado
		 print '<tr class="liste_titre">';
		 print_liste_field_titre($langs->trans("Number"),"", "","","",'');
		 print_liste_field_titre($langs->trans("Preventive"),"", "","","",'colspan="3"');
		 print '</tr>';
		 if ($objprev->fetch($object->fk_poa_prev))
		   {
		 print '<tr>';
		 print '<td>'.$objprev->nro_preventive.'</td>';
		 print '<td colspan="3">'.$objprev->label.'</td>';
		 print '</tr>';
		   }
		 //gerencia subgerencia departamento
		 print '<tr class="liste_titre">';
		 print_liste_field_titre($langs->trans("Management learn"),"", "","","",'colspan="2" width="33%"');
		 print_liste_field_titre($langs->trans("Submanagement"),"", "","","",'width="33%"');
		 print_liste_field_titre($langs->trans("Departament"),"", "","","",'width="33%"');
		 print '</tr>';
		 print '<tr>';
		 //fatherarea
		 $aArea = $objarea->getarea($object->fk_area);
		 $aArea = $aArea['area'];
		 print '<td colspan="2">'.$aArea[0].'</td>';
		 print '<td>'.$aArea[1].'</td>';
		 print '<td>'.$aArea[2].'</td>';
		 print '</tr>';

		 //ref
		 print '<tr><td width="150px">'.$langs->trans('Ref').'</td><td colspan="3">';
		 print $object->ref;
		 print ' / ';
		 print $object->gestion;
		 print '</td></tr>';

		 //date
		 print '<tr><td width="150px">'.$langs->trans('Date').'</td><td colspan="3">';
		 print dol_print_date($object->date_process,'day');
		 print '</td></tr>';
		 $date1 = $object->date_process;
		 $numDay = $conf->global->POA_PREVENTIVE_DAY_DELAY;
		 $date0 = strtotime("+$numDay day",$date1);

		 //amount
		 print '<tr><td width="12%">'.$langs->trans('Reference price').'</td><td colspan="3">';
		 print number_format(price2num($object->amount,'MT'),2);
		 print '</td></tr>';

		 //type modality
		 //revisamos si el tipo contrato del pac esta idem al seleccionado
		 $typecontrat = '';
		 $lValid = true;
		 if ($objprev->fk_pac > 0)
		   {
		 //buscamos el pac
		 if ($objpac->fetch($objprev->fk_pac))
		   {
			 //verificamos
			 if ($objpac->fk_type_modality != $object->fk_type_con)
			   {
			 //analizamos el tipo de contrato
			 $aTable = fetch_tables($objpac->fk_type_modality);
			 $typecontrat = $aTable['label'];
			   }
		   }
		 else
		   $typecontrat = $langs->trans('Notdefined');
		   }

		 print '<tr><td>'.$langs->trans('Modality').'</td><td colspan="3">';
		 print select_tables($object->fk_type_con,'fk_type_con','',0,1,'05');
		 if (!empty($typecontrat))
		   {
		 $lValid = false;
		 print '&nbsp;';
		 print ' <> ';
		 print '<span class="textred">'.$typecontrat.'</span>';
		   }
		 print '</td></tr>';

		 //label
		 print '<tr><td>'.$langs->trans('Title').'</td><td colspan="3">';
		 print $object->label;
		 print '</td></tr>';

		 //justification
		 print '<tr><td>'.$langs->trans('Justification').'</td><td colspan="3">';
		 print $object->justification;
		 print '</td></tr>';

		 //type adj
		 print '<tr><td>'.$langs->trans('Type of adjudication').'</td><td>';
		 print select_tables($object->fk_type_adj,'fk_type_adj','',0,1,'01');
		 print '</td>';
		 print '<td colspan="2">'.$langs->trans('Refpac').': ';
		 print $object->ref_pac;
		 print '</td>';
		 print '</tr>';

		 //registro del cuce y codigo del proceso
		 if ($object->amount > $conf->global->POA_PAC_MINIMUM)
		   {
		 print '<tr>';
		 print '<td>'.$langs->trans('CUCE').'</td><td colspan="3">';
		 $idTagps = 'di_'.$object->id;
		 $idTagps2 = 'di_'.$object->id.'_';
		 $idTagps3 = 'dpp'.$object->id;
		 if (($user->rights->poa->proc->mod && $object->statut == 1 && $user->id == $objprev->fk_user_create) || $user->admin)
		   {
			 print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">';
			 print '<form id="fiche_pasa" name="fiche_pasa" onsubmit="enviaproc()" return="false" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			 print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			 print '<input type="hidden" name="action" value="updateproc">';
			 print '<input type="hidden" name="id" value="'.$object->id.'">';
			 print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

			 // //original
			 print '<input type="text" name="di_'.$object->id.'" id="di_'.$object->id.'" value="'
			   .$object->cuce
			   .'" onblur='."'".'CambiarURLFramecuce("'.$idTagps.'","'.$idTagps2.'","'
			   .$object->id.'",'.'this.value);'."'". 'size="14" maxlength="16" placeholder="'
			   .$langs->trans('CUCE').'">';
			 print ' '.info_admin($langs->trans("Recordthenumbersnodashes"),1);
			 print '</form>';
			 print '</span>';


			 print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick='."'".'visual_four("'.$idTagps.'" , "'.$idTagps2.'")'."'".'>';
			 print (empty($object->cuce)?img_picto($langs->trans('Register'),'edit').'&nbsp;':$object->cuce);
			 print '</span>';
		   }
		 else
		   {
			 print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;">';
			 print (empty($object->cuce)?img_picto($langs->trans('Register'),'edit').'&nbsp;':$object->cuce);
			 print '</span>';
		   }
		 print '</td>';
		 print '</tr>';


		 //registro del codigo del proceso
		 print '<tr>';
		 print '<td>'.$langs->trans('Codeprocess').'</td><td colspan="3">';

		 $idTagps = 'df_'.$object->id;
		 $idTagps2 = 'df_'.$object->id.'_';
		 $idTagps3 = 'dpf'.$object->id;
		 if (($user->rights->poa->proc->mod && $object->statut == 1 && $user->id == $objprev->fk_user_create) || $user->admin)
		   {
			 print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">';
			 print '<form id="fiche_pasa" name="fiche_pasa" onsubmit="enviaproc()" return="false" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			 print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			 print '<input type="hidden" name="action" value="updatecode">';
			 print '<input type="hidden" name="id" value="'.$object->id.'">';
			 print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

			 // //original
			 print '<input type="text" name="df_'.$object->id.'" id="df_'.$object->id.'" value="'
			   .$object->code_process
			   .'" onblur='."'".'CambiarURLFramecode("'.$idTagps.'","'.$idTagps2.'","'
			   .$object->id.'",'.'this.value);'."'". 'size="20" maxlength="30" placeholder="'
			   .$langs->trans('Codeprocess').'">';
			 print ' '.info_admin($langs->trans("Registerthecodeoftheentitytoidentifytheprocess"),1);
			 print '</form>';
			 print '</span>';


			 print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick='."'".'visual_four("'.$idTagps.'" , "'.$idTagps2.'")'."'".'>';
			 print (empty($object->code_process)?img_picto($langs->trans('Register'),'edit').'&nbsp;':$object->code_process);
			 print '</span>';
		   }
		 else
		   {
			 print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;">';
			 print (empty($object->code_process)?img_picto($langs->trans('Register'),'edit').'&nbsp;':$object->code_process);
			 print '</span>';
		   }
		 print '</td>';
		 print '</tr>';
		   }
			  //subir imagen
		 print '<tr><td>'.$langs->trans('PDF').'</td><td colspan="2">';
		 $dir = $conf->poa->dir_output.'/process/pdf/'.$object->id.'.pdf';
		 $url = DOL_URL_ROOT.'/documents/poa/process/pdf/'.$object->id.'.pdf';
		 if ($user->admin || $user->rights->poa->proc->mod)
		   if ($action !='upload')
		 {
		   print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=upload'.'&dol_hide_leftmenu=1">'.img_picto($langs->trans('Uploaddoc'),DOL_URL_ROOT.'/poa/img/subir.png','',1).'</a>';
		   //mostramos el archivo
		   if (file_exists($dir))
			 {
			   print '&nbsp;&nbsp;';
			   print '<a href="'.$url.'" target="_blank">'.img_picto($langs->trans('PDF'),'pdf2').'</a>';
			 }
		 }
		   else
		 {
		 	$idreg = $object->id;
		   include DOL_DOCUMENT_ROOT.'/poa/process/tpl/addpdf.tpl.php';
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
		 print '</td></tr>';

		 //respaldo de documentos segun tipo de contratacion
		 //buscamos el tipo de contratacion
		 $aTable = fetch_tables($object->fk_type_con);
		 print '<tr class="liste_titre">';
		 print_liste_field_titre($langs->trans("Necessary documentation"),"", "","","",'colspan="4"');
		 print '</tr>';
		 print '<tr class="liste_titre">';
		 print_liste_field_titre('&nbsp;',"", "","","",'colspan="3"');
		 print_liste_field_titre($aTable['label'],"", "","","",'align="center" width="20%"');
		 print '</tr>';
		 //generico
		 if ($aTable['type'] == 'MENSPAC' || $aTable['type'] == 'MEN')
		   $value = 1;
		 elseif (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY')
		   $value = 2;
		 elseif (STRTOUPPER($aTable['type']) == 'LP')
		   $value = 3;
		 elseif (STRTOUPPER($aTable['type']) == 'DIREC')
		   $value = 4;
		 elseif (STRTOUPPER($aTable['type']) == 'CAE')
		   $value = 5;

		 //type certif presup
		 print '<tr><td colspan="3">'.$langs->trans('doc_cp').'</td><td align="center">';
		 if ($object->doc_certif_presupuestaria > 0)
		   print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
		 else
		   print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
		 print '</td></tr>';
		 //precio referencial
		 print '<tr><td colspan="3">'.$langs->trans('doc_pr').'</td><td align="center">';
		 if ($object->doc_precio_referencial > 0)
		   print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
		 else
		   print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
		 print '</td></tr>';
		 //type especif tecnica
		 print '<tr><td colspan="3">'.$langs->trans('doc_et').'</td><td align="center">';
		 if ($object->doc_especific_tecnica > 0)
		   print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
		 else
		   print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
		 print '</td></tr>';
		 //modelo contrato
		 if ($lForm)
		   {
		 print '<tr><td colspan="3">'.$langs->trans('Modelo de Contrato elaborado por la GAL').'</td><td nowrap align="center">';
		 if ($object->doc_modelo_contrato > 0)
		   print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
		 else
		   print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
		   }
		 else
		   {
		 //nuevo formulario
		 if (STRTOUPPER(trim($aTable['type'])) == 'ANPEMEN' ||
			 STRTOUPPER(trim($aTable['type'])) == 'ANPEMAY' ||
			 STRTOUPPER(trim($aTable['type'])) == 'LP' ||
			 STRTOUPPER(trim($aTable['type'])) == 'CEA' )
		   {
			 print '<tr><td colspan="3">'.$langs->trans('Modelo de Contrato elaborado por la GAL').'</td><td nowrap align="center">';
			 if ($object->doc_modelo_contrato > 0)
			   print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
			 else
			   print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
		   }

		   }
		 // if (STRTOUPPER(trim($aTable['type'])) == 'ANPEMEN' || STRTOUPPER(trim($aTable['type'])) == 'ANPEMAY')
		 //   print '&nbsp;&nbsp;<a href="#">'.img_picto($langs->trans("help_anpe"),'help').'</a>';
		 // elseif (STRTOUPPER(trim($aTable['type'])) == 'DIREC')
		 //   print '&nbsp;&nbsp;<a href="#">'.img_picto($langs->trans("help_direc"),'help').'</a>';
		 // elseif (STRTOUPPER(trim($aTable['type'])) == 'MENSPAC' || STRTOUPPER(trim($aTable['type'])) == 'MEN')
		 //   print '&nbsp;&nbsp;<a href="#">'.img_picto($langs->trans("help_men"),'help').'</a>';
		 print '</td></tr>';

		//fotocopia PAC
		if (!$lForm)
		  {
		print '<tr><td colspan="3">'.$langs->trans('Fotocopia hoja PAC donde se encuentra incluido proceso de contratacion').'</td><td nowrap align="center">';
		if ($object->doc_pac > 0)
			print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
			else
			  print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
		  }

		if (!$lForm)
		  {
		if (STRTOUPPER($aTable['type']) == 'DIREC')
		  {
			//informe tecnico LEGAL
			print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_it').'</td><td align="center">';
			if ($object->doc_informe_lega > 0)
			print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
			else
			  print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			print '</td></tr>';
		  }
		  }
		else
		  {
		if (STRTOUPPER($aTable['type']) != 'MENSPAC' &&
			STRTOUPPER($aTable['type']) != 'MEN')
		  {
			//informe tecnico LEGAL
			print '<tr><td colspan="3">'.$langs->trans('doc_it').'</td><td align="center">';
			if ($object->doc_informe_lega > 0)
			print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
			else
			  print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			print '</td></tr>';
		  }
		  }
		if (!$lForm)
		  {
		//Seleccion de mepresa proponente
		if (STRTOUPPER($aTable['type']) == 'DIREC' ||
			STRTOUPPER($aTable['type']) == 'EXCEP')
		  {
			print '<tr><td colspan="3">'.$langs->trans('Proponente Seleccionado').'</td><td align="center">'.'|'.$lForm.'|';
			;
			print $form->select_company('','fk_soc','',1,0,0);
			print '</td></tr>';
		  }
		  }
		if (!$lForm)
		  {
		//lista de proponentes para CM
		if (STRTOUPPER($aTable['type']) == 'MENSPAC')
		  {
			print '<tr><td colspan="3">'.$langs->trans('Lista de proponentes').'</td><td align="center">';
			if ($object->doc_prop > 0)
			print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
			else
			  print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			print '</td></tr>';
		  }
		  }

		   //metodo de seleccion
		   if (STRTOUPPER($aTable['type']) != 'MENSPAC' &&
		   STRTOUPPER($aTable['type']) != 'MEN' &&
		   STRTOUPPER($aTable['type']) != 'DIREC' && !empty($object->fk_type_con))
		 {

		   // print '<tr class="liste_titre">';
		   // print_liste_field_titre($langs->trans("Selection method"),"", "","","",'colspan="4"');
		   // print '</tr>';
		   print '<tr class="liste_titre">';
		   print_liste_field_titre($langs->trans('Method selection and award'),"", "","","",'colspan="3"');
		   print_liste_field_titre('',"", "","","",'colspan="3"');
		   print '</tr>';

		   if (STRTOUPPER($aTable['type']) != 'CEA')
			 {
			   include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/title_cea.tpl.php';
			   if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
			   STRTOUPPER($aTable['type']) == 'ANPEMAY' ||
			   STRTOUPPER($aTable['type']) == 'LP' )
			 {
			   //calidad propuesta tecnica y costo
			   print '<tr>';
			   include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/cptc.tpl.php';
			   print '<td align="center">';
			   if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				   STRTOUPPER($aTable['type']) == 'ANPEMAY' )
				 {
				   if ($object->metodo_sel_anpe == 1)
				 print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				   else
				 print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);

				 }
			   elseif (STRTOUPPER($aTable['type']) == 'LP' )
				 {
				   if ($object->metodo_sel_lpni == 1)
				 print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				   else
				 print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
				 }
			   print '</td></tr>';

			//calidad
			print '<tr>';
			include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/c.tpl.php';
			print '<td align="center">';

			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			  {
				if ($object->metodo_sel_anpe == 2)
				 print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				   else
				 print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			  }
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			  {
				if ($object->metodo_sel_lpni == 2)
				 print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				   else
				 print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			  }
			print '</td></tr>';

			//Presupuesto Fijo
			print '<tr>';
			include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/pf.tpl.php';
			print '<td align="center">';
			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			  {
				if ($object->metodo_sel_anpe == 3)
				 print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				   else
				 print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			  }
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			  {
				if ($object->metodo_sel_lpni == 3)
				 print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				   else
				 print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			  }
			print '</td></tr>';

			//Menor Costo
			print '<tr>';
			include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/mc.tpl.php';

			print '<td align="center">';

			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			  {
				if ($object->metodo_sel_anpe == 4)
				 print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				   else
				 print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			  }
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			  {
				if ($object->metodo_sel_lpni == 4)
				 print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				   else
				 print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			  }
			print '</td></tr>';

			//Prcio evaluado mas bajo (PEMB)
			print '<tr>';
			include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/pemb.tpl.php';

			print '<td align="center">';

			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			  {
				if ($object->metodo_sel_anpe == 5)
				 print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				   else
				 print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			  }
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			  {
				if ($object->metodo_sel_lpni == 5)
				 print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				   else
				 print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			  }
			print '</td></tr>';

			//formulario de condiciones... (PEMB)
			print '<tr><td  colspan="3">'.$langs->trans('Formulario de Condiciones Adicionales (Excepto para el metodo de PEMB)').'</td>';
			print '<td align="center">';

			if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				STRTOUPPER($aTable['type']) == 'ANPEMAY' )
			  {
				if ($object->condicion_adicional_anpe > 0)
				 print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				   else
				 print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			  }
			elseif (STRTOUPPER($aTable['type']) == 'LP' )
			  {
				if ($object->condicion_adicional_lpni > 0)
				 print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
				   else
				 print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			  }
			print '</td></tr>';

			  }
		  }
		else
		  {
			//modelo CAE
			print '<tr><td  colspan="3" class="fieldrequired">'.$langs->trans('mod_cae').'</td>';
			print '<td align="center">';
			if ($object->metodo_sel_cae > 0)
			  print img_picto('mark',DOL_URL_ROOT.'/poa/img/mark.png','',true);
			else
			  print img_picto('umark',DOL_URL_ROOT.'/poa/img/umark.png','',true);
			print '</td></tr>';

		  }
		}
	  print '</table>';


	  print '</div>';


	  /* ************************************** */
	  /*                                        */
	  /* Barre d'action                         */
	  /*                                        */
	  /* ************************************** */

	  print "<div class=\"tabsAction\">\n";

	  if ($user->rights->poa->prev->leer)
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php'.(isset($_GET['nopac'])?'?nopac=1&ida='.$ida:'?ida='.$ida).'&dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
	  else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";
	  if ($action == '')
		{
		  // if ($user->rights->poa->proc->crear &&
		  // 	  ($user->admin || $objprev->fk_user_create == $user->id))
		  // 	print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
		  // else
		  // 	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";


		  if ($user->admin ||
		  ($user->rights->poa->proc->mod && $object->statut == 0))
		print '<a class="butAction" href="fiche.php?action=edit&id='.$object->id.'&dol_hide_leftmenu=1">'.$langs->trans("Modify").'</a>';
		  else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

		  if ($user->rights->poa->proc->del && $object->statut == 0)
		print '<a class="butActionDelete" href="fiche.php?action=delete&id='.$object->id.'&dol_hide_leftmenu=1">'.$langs->trans("Delete")."</a>";
		  else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
		  if ($user->rights->poa->proc->val && $object->statut == 0 && $lValid)
		print '<a class="butAction" href="fiche.php?action=validate&id='.$object->id.'&dol_hide_leftmenu=1">'.$langs->trans("Validate")."</a>";
		  else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
		  if ($user->rights->poa->proc->nul && $object->statut == 1)
		print '<a class="butAction" href="fiche.php?action=anulate&id='.$object->id.'&dol_hide_leftmenu=1">'.$langs->trans("Cancel")."</a>";
		  else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Cancel")."</a>";

		  if ($user->rights->poa->comp->crear && $object->statut == 1 &&
		  ($user->admin || $objprev->fk_user_create == $user->id))
		print '<a class="butAction" href="fiche_pas1.php?action=create&id='.$object->id.'&dol_hide_leftmenu=1">'.$langs->trans("Createcontrat")."</a>";
		  else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createcontrat")."</a>";

		  if ($object->statut >= 1)
		{
		  if ($lForm)
			print '<a  href="fiche_iniproc.php?id='.$object->id.'&dol_hide_leftmenu=1">'.img_picto($langs->trans("Excel"),DOL_URL_ROOT.'/poa/img/excel','',1)."</a>";
		  else
			print '<a  href="fiche_iniproc_20150901.php?id='.$object->id.'&dol_hide_leftmenu=1">'.img_picto($langs->trans("Excel"),DOL_URL_ROOT.'/poa/img/excel','',1)."</a>";
		}
		  else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Excel")."</a>";

		}
	  print "</div>";

	  // print '<div class="tabsActionleft">';

	  // if ($action == '')
	  //   {
	  //     if ($object->statut >= 1)
	  // 	{
	  // 	  print '<span>'.$langs->trans('The export excel file should be stored with the same name in the same folder where the file is located form_iniproceso.xlsx').'</span>';
	  // 	  print '<br><span>'.$langs->trans('If you do not have to download the same file form_iniproceso.xlsx clicking on the following link').' ';

	  // 	  print '<a  href="fiche_base.php">'.img_picto($langs->trans("Excel"),DOL_URL_ROOT.'/poa/img/excelbase','',1)."</a>";
	  // 	  print '</span>';
	  // 	}

	  //   }
	  // print "</div>";
	   }

	 /*
	  * Edition fiche
	  */
	 if (($action == 'edit' || $action == 're-edit') && 1)
	{
	  print_fiche_titre($langs->trans("Edit"), $mesg);

	  print "\n".'<script type="text/javascript" language="javascript">';
	  print '$(document).ready(function () {
				$("#selectfk_type_con").change(function() {
				  document.fiche_process.action.value="createeditdos";
				  document.fiche_process.submit();
				});

			});';
	  print '</script>'."\n";

	  print '<form name="fiche_process" action="fiche.php" method="post">';
	  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	  print '<input type="hidden" name="action" value="update">';
	  print '<input type="hidden" name="id" value="'.$object->id.'">';
	  print '<input type="hidden" name="fk_poa_prev" value="'.$object->fk_poa_prev.'">';
	  print '<input type="hidden" name="fk_area" value="'.$object->fk_area.'">';
	  print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

	  dol_htmloutput_mesg($mesg);

	  print '<table class="border" style="min-width=1000px" width="100%">';
	  // // preventivo
	  // print '<tr><td class="fieldrequired">'.$langs->trans('Preventive').'</td><td colspan="3">';
	  // print $objprev->select_poa_prev($object->fk_poa_prev,'fk_poa_prev','',40,1,$gestion,$idArea,1);
	  // print '</td></tr>';
	  if ($object->fk_poa_prev)
		{
		  //mostramos
		  //preventivo seleccionado
		  print '<tr class="liste_titre">';
		  print_liste_field_titre($langs->trans("Number"),"", "","","",'');
		  print_liste_field_titre($langs->trans("Preventive"),"", "","","",'colspan="3"');
		  print '</tr>';
		  if ($objprev->fetch($object->fk_poa_prev))
		{
		  print '<tr>';
		  print '<td>'.$objprev->nro_preventive.'</td>';
		  print '<td colspan="3">'.$objprev->label.'</td>';
		  print '</tr>';
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

		  //ref
		  print '<tr><td width="150px" class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="3">';
		  print '<input type="ref" name="ref" value="'.$object->ref.'" size="3" maxlenght="6">';
		  print ' / ';
		  print '<input type="year" name="gestion" value="'.$object->gestion.'" size="2" maxlenght="4">';
		  print '</td></tr>';

		  //date
		  print '<tr><td width="150px" class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="3">';
		  $form->select_date($object->date_process,'di_','','','',"date",1,1);
		  print '</td></tr>';

		  //amount
		  print '<tr><td width="12%" class="fieldrequired">'.$langs->trans('Reference price').'</td><td colspan="3">';
		  print '<input id="amount" type="text" value="'.$object->amount.'" name="amount" size="15" maxlength="12">';
		  print '</td></tr>';

		  //type modality
		  print '<tr><td class="fieldrequired">'.$langs->trans('Modality').'</td><td colspan="3">';
		  if ($_POST['fk_type_con'] != $object->fk_type_con && !empty($_POST['fk_type_con']))
		$object->fk_type_con = $_POST['fk_type_con'];
		  print select_tables($object->fk_type_con,'fk_type_con','',1,0,'05',$object->amount);
		  print '</td></tr>';

		  //label
		  print '<tr><td class="fieldrequired">'.$langs->trans('Title').'</td><td colspan="3">';
		  print '<input id="label" type="text" value="'.$object->label.'" name="label" size="120" maxlength="255">';
		  print '</td></tr>';

		  //justification
		print '<tr><td class="fieldrequired">'.$langs->trans('Justification').'</td><td colspan="3">';
		print '<textarea id="justification" name="justification" rows="2" cols="60" >'.$object->justification.'</textarea>';
		print '</td></tr>';

		  //type adj
		  print '<tr><td class="fieldrequired">'.$langs->trans('Type of adjudication').'</td><td>';
		  print select_tables((empty($object->fk_type_adj)?3:$object->fk_type_adj),'fk_type_adj','',0,0,'01');
		  print '</td>';
		  print '<td colspan="2">'.$langs->trans('Refpac').': ';
		  print '<input id="ref_pac" type="text" value="'.$object->ref_pac.'" name="ref_pac" size="90" maxlength="255">';
		  print '</td>';

		  print '</tr>';

		  //respaldo de documentos segun tipo de contratacion
		  //buscamos el tipo de contratacion
		  $aTable = fetch_tables($object->fk_type_con);
		  print '<tr class="liste_titre">';
		  print_liste_field_titre($langs->trans("Necessary documentation"),"", "","","",'colspan="4"');
		  print '</tr>';
		  print '<tr class="liste_titre">';
		  print_liste_field_titre('&nbsp;',"", "","","",'colspan="3"');
		  print_liste_field_titre($aTable['label'],"", "","","",'align="center" width="20%"');
		  print '</tr>';
		  //generico
		  if ($aTable['type'] == 'MENSPAC' || $aTable['type'] == 'MEN')
		$value = 1;
		  elseif (STRTOUPPER($aTable['type']) == 'ANPEMEN' || STRTOUPPER($aTable['type']) == 'ANPEMAY')
		$value = 2;
		  elseif (STRTOUPPER($aTable['type']) == 'LP')
		$value = 3;
		  elseif (STRTOUPPER($aTable['type']) == 'DIREC')
		$value = 4;
		  elseif (STRTOUPPER($aTable['type']) == 'CAE')
		$value = 5;

		  //type certif presup
		  print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_cp').'</td><td align="center">';
		  print '<input type="checkbox" name="doc_certif_presupuestaria" value="'.$value.'" checked="checked">';
		  print '</td></tr>';
		  //precio referencial
		  print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_pr').'</td><td align="center">';
		  print '<input type="checkbox" name="doc_precio_referencial" value="'.$value.'" checked="checked">';
		  print '</td></tr>';
		  //type especif tecnica
		  print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_et').'</td><td align="center">';
		  print '<input type="checkbox" name="doc_especific_tecnica" value="'.$value.'" checked="checked">';
		  print '</td></tr>';
		  //modelo contrato
		  if ($lForm)
		{
		  print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Modelo de Contrato elaborado por la GAL').'</td><td nowrap align="center">';
		  $checked = '';
		  if ($object->doc_modelo_contrato > 0) $checked = 'checked="checked"';

		  print '<input type="checkbox" name="doc_modelo_contrato" value="'.$value.'" '.$checked.'>';
		}
		  else
		{
		  if (STRTOUPPER(trim($aTable['type'])) == 'ANPEMEN' ||
			  STRTOUPPER(trim($aTable['type'])) == 'ANPEMAY' ||
			  STRTOUPPER(trim($aTable['type'])) == 'LP' ||
			  STRTOUPPER(trim($aTable['type'])) == 'CEA' )
			{
			  print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Modelo de Contrato elaborado por la GAL').'</td><td nowrap align="center">';
			  print '<input type="checkbox" name="doc_modelo_contrato" value="'.$value.'">';
		  }

		}
		  if ($lForm)
		{
		  if (STRTOUPPER(trim($aTable['type'])) == 'ANPEMEN' || STRTOUPPER(trim($aTable['type'])) == 'ANPEMAY')
			print '&nbsp;<a href="#">'.img_picto($langs->trans("help_anpe"),'help').'</a>';
		  elseif (STRTOUPPER(trim($aTable['type'])) == 'DIREC')
			print '&nbsp;<a href="#">'.img_picto($langs->trans("help_direc"),'help').'</a>';
		  elseif (STRTOUPPER(trim($aTable['type'])) == 'MENSPAC' || STRTOUPPER(trim($aTable['type'])) == 'MEN')
			print '&nbsp;<a href="#">'.img_picto($langs->trans("help_men"),'help').'</a>';
		  print '</td></tr>';

		  if (STRTOUPPER($aTable['type']) != 'MENSPAC' && STRTOUPPER($aTable['type']) != 'MEN')
			{
			  //informe tecnico
			  print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_it').'</td><td align="center">';
			  print '<input type="checkbox" name="doc_informe_lega" value="'.$value.'">';
			}
		  print '</td></tr>';
		}

		  //fotocopia PAC
		  if (!$lForm)
		{
		  $checked = '';
		  if ($object->doc_pac > 0) $checked = 'checked="checked"';
		  print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Fotocopia hoja PAC donde se encuentra incluido proceso de contratacion').'</td><td nowrap align="center">';
		  print '<input type="checkbox" name="doc_pac" value="'.$value.'" '.$checked.'>';
		}
		  if (STRTOUPPER($aTable['type']) == 'DIREC')
		{
		  //informe tecnico LEGAL
		  $checked = '';
		  if ($object->doc_informe_lega > 0) $checked = 'checked="checked"';
		  print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('doc_it').'</td><td align="center">';
		  print '<input type="checkbox" name="doc_informe_lega" value="'.$value.'" '.$checked.'>';
		  print '</td></tr>';
		  if (!$lForm)
			{
			  //Seleccion de mepresa proponente
			  print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Proponente Seleccionado').'</td><td align="center">';
			  print $form->select_company($object->fk_soc,'fk_soc','',1,0,0);
			  print '</td></tr>';
			}
		}
		  if (!$lForm)
		{
		  //lista de proponentes para CM
		  $checked = '';
		  if ($object->doc_prop > 0) $checked = 'checked="checked"';
		  if (STRTOUPPER($aTable['type']) == 'MENSPAC')
			{
			  print '<tr><td colspan="3" class="fieldrequired">'.$langs->trans('Lista de proponentes').'</td><td align="center">';
			  print '<input type="checkbox" name="doc_prop" value="'.$value.'" '.$checked.'>';
			  print '</td></tr>';
			}
		}

		  //metodo de seleccion
		  if (STRTOUPPER($aTable['type']) != 'MENSPAC' &&
		  STRTOUPPER($aTable['type']) != 'MEN' &&
		  STRTOUPPER($aTable['type']) != 'DIREC' && !empty($object->fk_type_con))
		{

		  // print '<tr class="liste_titre">';
		  // print_liste_field_titre($langs->trans("Selection method"),"", "","","",'colspan="4"');
		  // print '</tr>';
		  print '<tr class="liste_titre">';
		  print_liste_field_titre($langs->trans('Method selection and award'),"", "","","",'colspan="3"');
		  print_liste_field_titre('',"", "","","",'colspan="3"');
		  print '</tr>';

		  if (STRTOUPPER($aTable['type']) != 'CAE')
			{
			  include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/title_cea.tpl.php';

			  if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
			  STRTOUPPER($aTable['type']) == 'ANPEMAY' ||
			  STRTOUPPER($aTable['type']) == 'LP' )
			{
			  //calidad propuesta tecnica y costo
			  print '<tr>';
			  include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/cptc.tpl.php';

			  print '<td align="center">';
			  if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				  STRTOUPPER($aTable['type']) == 'ANPEMAY' )
				{
				  $checked = '';
				  if ($object->metodo_sel_anpe == 1) $checked = 'checked="checked"';
				  print '<input type="checkbox" name="metodo_sel_anpe" value="1" '.$checked.'>';
				}
			  elseif (STRTOUPPER($aTable['type']) == 'LP' )
				{
				  $checked = '';
				  if ($object->metodo_sel_lpni == 1) $checked = 'checked="checked"';

				  print '<input type="checkbox" name="metodo_sel_lpni" value="1" '.$checked.'>';
				}
			  print '</td></tr>';

			  //calidad
			  print '<tr>';
			  include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/c.tpl.php';

			  print '<td align="center">';

			  if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				  STRTOUPPER($aTable['type']) == 'ANPEMAY' )
				{
				  $checked = '';
				  if ($object->metodo_sel_anpe == 2) $checked = 'checked="checked"';

				  print '<input type="checkbox" name="metodo_sel_anpe" value="2" '.$checked.'>';
				}
			  elseif (STRTOUPPER($aTable['type']) == 'LP' )
				{
				  $checked = '';
				  if ($object->metodo_sel_lpni == 2) $checked = 'checked="checked"';

				  print '<input type="checkbox" name="metodo_sel_lpni" value="2" '.$checked.'>';
				}
			  print '</td></tr>';

			  //Presupuesto Fijo
			  print '<tr>';
			  include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/pf.tpl.php';

			  print '<td align="center">';
			  if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				  STRTOUPPER($aTable['type']) == 'ANPEMAY' )
				{
				  $checked = '';
				  if ($object->metodo_sel_anpe == 3) $checked = 'checked="checked"';

				  print '<input type="checkbox" name="metodo_sel_anpe" value="3" '.$checked.'>';
				}
			  elseif (STRTOUPPER($aTable['type']) == 'LP' )
				{
				  $checked = '';
				  if ($object->metodo_sel_lpni == 3) $checked = 'checked="checked"';

				  print '<input type="checkbox" name="metodo_sel_lpni" value="3" '.$checked.'>';
				}
			  print '</td></tr>';

			  //Menor Costo
			  print '<tr>';
			  include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/mc.tpl.php';

			  print '<td align="center">';

			  if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				  STRTOUPPER($aTable['type']) == 'ANPEMAY' )
				{
				  $checked = '';
				  if ($object->metodo_sel_anpe == 4) $checked = 'checked="checked"';

				  print '<input type="checkbox" name="metodo_sel_anpe" value="4" '.$checked.'>';
				}
			  elseif (STRTOUPPER($aTable['type']) == 'LP' )
				{
				  $checked = '';
				  if ($object->metodo_sel_lpni == 4) $checked = 'checked="checked"';

				  print '<input type="checkbox" name="metodo_sel_lpni" value="4" '.$checked.'>';
				}
			  print '</td></tr>';

			  //Prcio evaluado mas bajo (PEMB)
			  print '<tr>';
			  include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/pemb.tpl.php';

			  print '<td align="center">';

			  if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				  STRTOUPPER($aTable['type']) == 'ANPEMAY' )
				{
				  $checked = '';
				  if ($object->metodo_sel_anpe == 5) $checked = 'checked="checked"';

				  print '<input type="checkbox" name="metodo_sel_anpe" value="5" '.$checked.'>';
				}
			  elseif (STRTOUPPER($aTable['type']) == 'LP' )
				{
				  $checked = '';
				  if ($object->metodo_sel_lpni == 5) $checked = 'checked="checked"';

				  print '<input type="checkbox" name="metodo_sel_lpni" value="5" '.$checked.'>';
				}
			  print '</td></tr>';

			  //formulario de condiciones... (PEMB)
			  print '<tr><td  colspan="3">'.$langs->trans('Formulario de Condiciones Adicionales (Excepto para el metodo de PEMB)').'</td>';
			  print '<td align="center">';

			  if (STRTOUPPER($aTable['type']) == 'ANPEMEN' ||
				  STRTOUPPER($aTable['type']) == 'ANPEMAY' )
				{
				  $checked = '';
				  if ($object->condicion_adicional_anpe >0) $checked = 'checked="checked"';

				  print '<input type="checkbox" name="condicion_adicional_anpe" value="2" '.$checked.'>';
				}
			  elseif (STRTOUPPER($aTable['type']) == 'LP' )
				{
				  $checked = '';
				  if ($object->condicion_adicional_lpni >0) $checked = 'checked="checked"';

				  print '<input type="checkbox" name="condicion_adicional_lpni" value="3" '.$checked.'>';
				}
			  print '</td></tr>';

			}
			}
		  else
			{
			  //modelo CAE
			  print '<tr><td  colspan="3" class="fieldrequired">'.$langs->trans('mod_cae').'</td>';
			  print '<td align="center">';

			  print '<input type="checkbox" name="metodo_sel_cae" value="5">';
			  print '</td></tr>';

			}
		}
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
