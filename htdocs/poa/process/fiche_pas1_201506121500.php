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
 *	\file       htdocs/poa/process/fiche_pas1.php
 *	\ingroup    Process
 *	\brief      Page fiche poa process register contrat.
 */

require("../../main.inc.php"); 
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprevprocess.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/pac/class/poapac.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocesscontrat.class.php';
if ($conf->addendum->enabled)
  require_once DOL_DOCUMENT_ROOT.'/addendum/class/addendum.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapre.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidacom.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidadev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapredet.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';
require_once DOL_DOCUMENT_ROOT.'/poa/lib/contrat.lib.php';
require_once DOL_DOCUMENT_ROOT.'/poa/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/poa/lib/doc.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
if ($conf->contratadd->enabled)
  require_once DOL_DOCUMENT_ROOT.'/contratadd/class/contratadd.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

$langs->load("poa@poa");

$action=GETPOST('action');

$id        = GETPOST("id"); //proces
$idc       = GETPOST("idc"); //contrato objpcon
$cid       = GETPOST("cid"); //contrato
$idp       = GETPOST("idp"); //preventivo

$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$gestion   = GETPOST("gestion");
$fk_poa_prev = GETPOST("fk_poa_prev");
$action    = GETPOST('action');
$actionsub = GETPOST('actionsub');
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
$objdev  = new Poapartidadev($db);
$extrafields = new ExtraFields($db);
if ($conf->addendum->enabled)
  $objadden = new Addendum($db);

// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($objcont->table_element);
if ($action == 'search')
  $action = 'createedit';
/*
 * Actions
 */
// Add
if ($action == 'addcontrat' && 
    $user->rights->poa->comp->crear &&
    $_POST["cancel"] <> $langs->trans("Cancel")
    )
  {
    $error = 0;
    $objpcon->fk_poa_process = $id;
    $objpcon->fk_contrat = GETPOST('fk_contrat');
    $fk_contrat_exist    = GETPOST('fk_contrat_exist');
    if (empty($objpcon->fk_contrat))
      {
	$objpcon->fk_contrat = $fk_contrat_exist;
      }
    $objpcon->date_create = date('Y-m-d');
    $objpcon->fk_user_create = $user->id;
    $objpcon->tms = date('YmdHis');
    $objpcon->statut = 0;
    if (empty($objpcon->fk_contrat))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorcontratisrequired").'</div>';
      }
    if (empty($error)) 
      {
	$idc = $objpcon->create($user);
	if ($idc > 0)
	  {
	    //buscamos el proceso
	    if ($object->fetch($id))
	      {
		if ($objprev->fetch($object->fk_poa_prev))
		  {
		    $objprev->statut = 2; //2 comprometido
		    if ($objprev->update($user) > 0)
		      {
			//exito;
		      }
		    else
		      {
			//se debe cambiar el estado manualmente
		      }
		  }
	      }
	    header("Location: fiche_pas1.php?id=".$id.'&idc='.$idc.'&action=selcon');
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
if ($action == 'uppdf')
  {
    if ($objcont->fetch($_POST["cid"])>0)
      {
	// Logo/Photo save
	$dir     = $conf->contrat->dir_output.'/'.$objcont->ref;
 
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
		    $newfile=$dir.'/'.dol_sanitizeFileName($cid.'.pdf');
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
		    header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&idc='.$idc.'&action=selcon');
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
  }

// updateorder
if ($action == 'updateorder' && 
    $user->rights->poa->comp->crear &&
    $_POST["cancel"] <> $langs->trans("Cancel")
    )
  {
    $error = 0;
    if($objpcon->fetch(GETPOST('idc')))
      {
	$objpcon->date_order_proceed = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$objpcon->tms = date('YmdHis');
	//$objpcon->statut = 0;
	if (empty($error)) 
	  {
	    $res = $objpcon->update($user);
	    if ($res > 0)
	      {
		header("Location: fiche_pas1.php?id=".$id);
		exit;
	      }
	    $action = 'editop';
	    $mesg='<div class="error">'.$objpcon->error.'</div>';
	  }
	else
	  {
	    $mesg='<div class="error">'.$objpcon->error.'</div>';
	    if ($error)
	      $action="editop";   // Force retour sur page creation
	  }
      }
    else
      {
	$mesg='<div class="error">'.$objpcon->error.'</div>';
	if ($error)
	  $action="editop";   // Force retour sur page creation
      }
  }

// Cancel process
if ($action == 'confirm_cancel' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->comp->nul)
  {
    if ($object->fetch($_REQUEST["id"])>0)
      if ($objprev->fetch($idp)>0)
	{
	  $objcom->getlist($idp);
	  if (count($objcom->array) > 0)
	    {
	      $array = $objcom->array;
	      $db->begin();
	      $error = 0;

	      if ($objpcon->fetch($idc)>0)
		{
		  //cambiamos de estado tabla poa_process_contrat
		  $objpcon->statut = 2;
		  $result = $objpcon->update($user);
		  if ($result <= 0) $error++;
		}
	      //buscamos los items del preventivo para actualizar el contrato registrado
	      $objpp->getlist($idp,'S'); //que tenga valor en amount
	      if (count($objpp->array) > 0)
		{
		  foreach((array) $objpp->array AS $l => $objpp_)
		    {
		      //buscamos los productos de la partida preventivo
		      $objppd->getlist($objpp_->id,$idc,1);
		      if (count($objppd->array)>0)
			{
			  foreach((array) $objppd->array AS $k => $objppd_)
			    {
			      //vamos actualizando cada registro quitando el fk_contrat, quant_adj, amount, statut = 1
			      $objppdc = new Poapartidapredet($db);
			      if ($objppdc->fetch($objppd_->id)>0)
				{
				  if ($objppdc->id == $objppd_->id)
				    {
				      $objppdc->fk_contrat = 0;
				      $objppdc->quant_adj = 0;
				      $objppdc->amount = 0;
				      $objppdc->statut = 1;
				      $result = $objppdc->update($user);
				      if ($result <= 0)
					$error++;
				    }
				  else
				    $error++;
				}
			      else
				$error++;
			    }
			}
		    }
		}
	      //actualizamos los comprometidos a anulados
	      foreach ((array) $array AS $i => $objcomp)
		{
		  if ($objcomp->fk_contrat == GETPOST('idc'))
		    {
		      $objcom->fetch($objcomp->id);
		      $objcom->statut = -1;
		      $result = $objcom->update($user);
		      if ($result <=0)
			$error++;
		    }
		}
	      if (empty($error))
		{
		  $db->commit();
		  header("Location: ".DOL_URL_ROOT.'/poa/process/fiche_pas1.php?id='.$id);
		  exit;
		}
	      else
		{
		  $db->rollback();
		  $mesg='<div class="error">'.$langs->trans('Error, no complete process').'</div>';
		  $action='';
		}

	    }
	}
  }

// updateprod
if ($action == 'updateprod' && 
    $user->rights->poa->comp->crear &&
    $_POST["cancel"] <> $langs->trans("Cancel") && 
    $_POST["approved"] <> $langs->trans("Save and approve"))
  {
    $error = 0;
    $aQuant_adj = GETPOST('quant_adj');
    $aAmount = GETPOST('amount');
    $aAmountPart = GETPOST('amountPart');

    if (is_array($aQuant_adj))
      {
	$db->begin();
	foreach((array) $aQuant_adj AS $idreg => $value)
	  {
	    //buscamos el registro
	    if ($objppd->fetch($idreg))
	      {
		if ($aAmount[$idreg]>0)
		  {
		    if ($aAmount[$idreg] <= $aAmountPart[$idreg])
		      {
			$objppd->quant_adj = $value;
			$objppd->amount = $aAmount[$idreg];
			//$objppd->fk_contrat = $idc;
			$objppd->update($user);
		      }
		    else
		      $error++;
		  }
	      }
	    else
	      $error++;
	  }
	if (empty($error))
	  $db->commit();
	else
	  $db->rollback();
      }
    $action = 'selcon';
  }

// updateprod aprobacion
if ($action == 'updateprod' && 
    $user->rights->poa->comp->crear && 
    $_POST["approved"] == $langs->trans("Save and approve"))
  {
    
    $object->fetch($id);
    $objprev->fetch($object->fk_poa_prev);

    $db->begin();
    $error = 0;
    $aQuant_adj = GETPOST('quant_adj');
    $aAmount = GETPOST('amount');
    $aAmountPart = GETPOST('amountPart');
    if($objpcon->fetch(GETPOST('idc')))
      {
	$objpcon->statut = 1;
	if ($objpcon->update($user) > 0)
	  {
	    if (is_array($aAmount))
	      {
		foreach((array) $aAmount AS $idreg => $value)
		  {
		    //buscamos el registro
		    if ($objppd->fetch($idreg))
		      {
			if ($value > 0)
			  {
			    if ($value <= $aAmountPart[$idreg])
			      {
				$objppd->quant_adj = $aQuant_adj[$idreg];
				$objppd->amount = $value;
				$objppd->fk_contrat = $idc;
				$objppd->statut=2;
				if($objppd->update($user) <= 0)
				  {
				    $error++;
				  }
				//buscamos la partida para registro en comprometido
				if ($objpp->fetch($objppd->fk_poa_partida_pre))
				  {
				    $objpp->getsum_str_part_det($objprev->gestion,
								$objpp->fk_structure,
								$objpp->fk_poa,
								$objpp->id,
								$idc,
								$objpp->partida);
				    
				    if ($objpp->fk_poa > 0)
				      {
					//esta correcto
				      }
				    else
				      {
					$error++;
					$mesg.='<div class="error">'.$langs->trans("Error, corregir el preventivo").'</div>';
				      }
				    
				    $total = $objpp->total;
				    //buscamos en comprometido
				    if ($objcom->fetch_pcp($objppd->fk_poa_partida_pre,
							   $object->fk_poa_prev,
							   $idc,
							   $objpp->partida) )
				      {
					if ($objcom->fk_poa_partida_pre == $objppd->fk_poa_partida_pre &&
					    $objcom->fk_poa_prev == $object->fk_poa_prev &&
					    $objcom->fk_contrat == $idc &&
					    $objcom->partida == $objpp->partida )
					  {
					    //existe y actualizamos
					    $objcom->fk_poa = $objpp->fk_poa;
					    $objcom->amount = $total;
					    $objcom->fk_contrat = $idc;
					    if($objcom->update($user)>0)
					      {
						//actualizacion con exito
					      }
					    else
					      $error++;
					  }
					else
					  {
					    //registramos en comprometido
					    $objcom->fk_poa_partida_pre = $objppd->fk_poa_partida_pre;
					    $objcom->fk_poa_prev = $objpp->fk_poa_prev;
					    $objcom->fk_structure = $objpp->fk_structure;
					    $objcom->fk_poa = $objpp->fk_poa;
					    $objcom->fk_contrat = $idc;
					    $objcom->partida = $objpp->partida;
					    $objcom->amount = $total;
					    $objcom->date_create = date('Y-m-d');
					    $objcom->tms = date('YmdHis');
					    $objcom->statut = 1;
					    $objcom->active = 1;
					    $idcom = $objcom->create($user);
					    if ($idcom > 0)
					      {
						//registro con exito
						//actualizamos el id de compromedito en partida_pre_det
						$objppd->fk_poa_partida_com=$idcom;
						if($objppd->update($user) <= 0)
						    $error++;
					      }
					    else
					      $error++;
					  }
				      }
				    else
				      $error++;
				    
				    
				  }
				else
				  {
				    $error++;
				  }
			      }
			    // else
			    //   $error++;
			  }
		      }
		    else
		      $error++;
		  }
	      }
	  }
	else
	  $error++;
      }
    if (empty($error))
      {
	$db->commit();
	header("Location: fiche_pas1.php?id=".$id);
	exit;
      }
    else
      {
	$db->rollback();
	$action = 'selcon';
      }
  }

// updatecomp modificacion del comprometido
if ($action == 'updatecomp' && 
    $user->rights->poa->comp->mod)
  {
    $object->fetch($id);
    $objprev->fetch($object->fk_poa_prev);
    $db->begin();
    $error = 0;
    $aQuant_adj = GETPOST('quant_adj');
    $aAmount = GETPOST('amount');
    $aAmountPart = GETPOST('amountPart');
    if($objpcon->fetch(GETPOST('idc')))
      {
	$objpcon->statut = 1;
	if ($objpcon->update($user) > 0)
	  {
	    if (is_array($aAmount))
	      {
		//print_r($aAmount);exit;
		foreach((array) $aAmount AS $idreg => $value)
		  {
		    //buscamos el registro
		    if ($objppd->fetch($idreg))
		      {
			if ($value > 0)
			  {
			    if ($value <= $aAmountPart[$idreg])
			      {
				$objppd->quant_adj = $aQuant_adj[$idreg];
				$objppd->amount = $value;
				$objppd->fk_contrat = $idc;
				$objppd->statut=2;
				if($objppd->update($user) <= 0)
				  {
				    $error++;
				  }
				//buscamos la partida para registro en comprometido
				if ($objpp->fetch($objppd->fk_poa_partida_pre))
				  {
				    $objpp->getsum_str_part_det($objprev->gestion,
								$objpp->fk_structure,
								$objpp->fk_poa,
								$objpp->id,
								$idc,
								$objpp->partida);
				    
				    if ($objpp->fk_poa > 0)
				      {
					//esta correcto
				      }
				    else
				      {
					$error++;
					$mesg.='<div class="error">'.$langs->trans("Error, corregir el preventivo").'</div>';
				      }
				    
				    $total = $objpp->total;
				    //buscamos en comprometido
				    if ($objcom->fetch_pcp($objppd->fk_poa_partida_pre,
							   $object->fk_poa_prev,
							   $idc,
							   $objpp->partida) )
				      {
					if ($objcom->fk_poa_partida_pre == $objppd->fk_poa_partida_pre &&
					    $objcom->fk_poa_prev == $object->fk_poa_prev &&
					    $objcom->fk_contrat == $idc &&
					    $objcom->partida == $objpp->partida )
					  {
					    //existe y actualizamos
					    $objcom->fk_poa = $objpp->fk_poa;
					    $objcom->amount = $total;
					    $objcom->fk_contrat = $idc;
					    if($objcom->update($user)>0)
					      {
						//actualizacion con exito
						//actualizamos el id de compromedito en partida_pre_det
						$objppd->fk_poa_partida_com=$objcom->id;
						if($objppd->update($user) <= 0)
						    $error++;
					      }
					    else
					      $error++;
					  }
					else
					  {
					    //registramos en comprometido
					    //al ser una modificacion no se registra como nuevo
					    $error++;
					    // $objcom->fk_poa_partida_pre = $objppd->fk_poa_partida_pre;
					    // $objcom->fk_poa_prev = $objpp->fk_poa_prev;
					    // $objcom->fk_structure = $objpp->fk_structure;
					    // $objcom->fk_poa = $objpp->fk_poa;
					    // $objcom->fk_contrat = $idc;
					    // $objcom->partida = $objpp->partida;
					    // $objcom->amount = $total;
					    // $objcom->date_create = date('Y-m-d');
					    // $objcom->tms = date('YmdHis');
					    // $objcom->statut = 1;
					    // $objcom->active = 1;
					    // $idcom = $objcom->create($user);
					    // if ($idcom > 0)
					    //   {
					    // 	//registro con exito
					    // 	//actualizamos el id de compromedito en partida_pre_det
					    // 	$objppd->fk_poa_partida_com=$idcom;
					    // 	if($objppd->update($user) <= 0)
					    // 	    $error++;
					    //   }
					    // else
					    //   $error++;
					  }
				      }
				    else
				      $error++;
				    
				    
				  }
				else
				  {
				    $error++;
				  }
			      }
			    // else
			    //   $error++;
			  }
		      }
		    else
		      $error++;
		  }
	      }
	  }
	else
	  $error++;
      }
    if (empty($error))
      {
	$db->commit();
	header("Location: fiche_pas1.php?id=".$id);
	exit;
      }
    else
      {
	$db->rollback();
	$action = 'selcon';
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
// print_r($_POST);
// exit;

/*
 * View
 */

$form=new Form($db);
$aArrcss = array('/poa/css/style-desktop.css');
$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviaoc.js','poa/js/jquery-1.3.min.js');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
//llxHeader("",$langs->trans("POA"),$help_url);
llxHeader("",$langs->trans("POA"),$help_url,'','','',$aArrjs,$aArrcss);

?>

<iframe id="iframe" src="actualiza_contrat.php" width="900" height="30" frameborder="0"></iframe>

<script type="text/javascript">
  function CambiarURLFramedp(id,idReg,idf,rowid,nday,di_){
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
      document.getElementById('iframe').src= 'actualiza_contrat.php?id='+idf+'&idc='+rowid+'&dp_'+rowid+'='+di_+'&action=updatedp';      
      window.location.reload();
}
</script>
<script type="text/javascript">
  function CambiarURLFramedf(id,idReg,idf,rowid,nday,di_){
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
      document.getElementById('iframe').src= 'actualiza_contrat.php?id='+idf+'&idc='+rowid+'&df_'+rowid+'='+di_+'&action=updatedf';      
      window.location.reload();
}
</script>
<script type="text/javascript">
  function CambiarURLFramedn(id,idReg,idf,rowid,nday,di_){
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
      document.getElementById('iframe').src= 'actualiza_contrat.php?id='+idf+'&idc='+rowid+'&dn_'+rowid+'='+di_+'&action=updatedn';  
      window.location.reload();    
}
</script>
<script type="text/javascript">
  function CambiarURLFramenc(id,idReg,idf,rowid,nday,di_){
      var idTwo = idReg;
      var idOne = id;
      document.getElementById(idTwo).innerHTML = di_;
      //cambiando el estado de
      visual_four(idReg,id);
      document.getElementById('iframe').src= 'actualiza_contrat.php?id='+idf+'&idc='+rowid+'&motif_'+rowid+'='+di_+'&action=updatenc';      
}
</script>

<script type="text/javascript">
  function CambiarURLFrametwo(id,idReg,idf,rowid,nday,di_){
      var idTwo = idReg;
      var idOne = id;
      var newdate = sumarfecha(nday,di_);
      if (di_ ==="")
	{
	}
      else
	{
	  document.getElementById(idTwo).innerHTML = di_;
	  document.getElementById(idf).innerHTML = newdate;
	}
      //cambiando el estado de
      visual_four(idReg,id);
      document.getElementById('iframe').src= 'actualiza_contrat.php?id='+idf+'&idc='+rowid+'&di_'+rowid+'='+di_+'&action=updateop';
      window.location.reload();
}
</script>

<?php
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
	$object->update($user);
	//creando la relacion de preventivo y proceso
	$objpps = new Poaprevprocess($db);
	$objpps->fk_poa_prev = $object->fk_poa_prev;
	$objpps->fk_poa_process = $object->id;
	$objpps->date_create = $object->date_process;
	$objpps->tms = date('YmdHis');
	$objpps->fk_user_create = $user->id;
	$objpps->statut = 1;
	$idpp = $objpps->create($user);
	if ($idpp > 0)
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

    // Confirm cancel proces
    if ($action == 'anulate')
      {
	$form = new Form($db);
	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idc='.$idc.'&idp='.$object->fk_poa_prev,$langs->trans("Cancelcommited"),$langs->trans("Confirmcancelcommited",$object->ref.' '.$object->detail),"confirm_cancel",'yes',0,2);
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
    print '<tr><td width="15%">'.$langs->trans('Date').'</td><td colspan="3">';
    print dol_print_date($object->date_process,'day');
    print '</td></tr>';
    
    //amount
    print '<tr><td width="12%">'.$langs->trans('Reference price').'</td><td colspan="3">';
    print number_format(price2num($object->amount,'MT'),2);
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
    
    if ($action == '')
      {
	if ($user->rights->poa->prev->leer)
	  print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/liste.php'.(isset($_GET['nopac'])?'?nopac=1&idp='.$_GET['idp']:'').'">'.$langs->trans("Return").'</a>';
	else
	  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";

	if ($user->rights->poa->comp->crear)
	  if ($user->admin || $user->id == $objprev->fk_user_create)
	    print '<a class="butAction" href="fiche_pas1.php?id='.$object->id.'&action=create">'.$langs->trans("Createcontrat").'</a>';
	else
	    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createcontrat")."</a>";
      }
    if ($action == 'selcon')
      {
	if ($user->rights->poa->prev->leer)
	  print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/liste.php'.(isset($_GET['nopac'])?'?nopac=1&idp='.$_GET['idp']:'').'">'.$langs->trans("Return").'</a>';
	else
	  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";
      }	  
	  
    print "</div>";
    
    /*
     * Edition fiche
     */
    if (($action == 'create') && $user->rights->poa->comp->crear )
      {
	print_fiche_titre($langs->trans("Committed"), $mesg);
	
	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
	            $("#fk_socid").change(function() {
	              document.fiche_comp.action.value="createedit";
	              document.fiche_comp.submit();
	            });
	
	        });';
	print '</script>'."\n";

	print '<form name="fiche_comp" action="fiche_pas1.php" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="addcontrat">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	
	dol_htmloutput_mesg($mesg);
	
	print '<table class="border" style="min-width=1000px" width="100%">';

	// societe
	print '<tr><td class="fieldrequired">'.$langs->trans('Company').'</td><td colspan="3">';
	print $form->select_company($objpcon->fk_socid,'fk_socid','',1);
	print '</td></tr>';
	
	// contratos
	print '<tr><td class="fieldrequired">'.$langs->trans('Newcontrat').'</td><td colspan="3">';
	$objcont->socid = $objpcon->fk_socid;
	$aContrat = getListOfContracts($objcont->socid);
	$aArray = array();
	$aArray[] = '';
	//contratos ya registrados
	$objpcon->getidscontrat();
	$aCon = $objpcon->array;
	foreach((array) $aContrat AS $j => $dataContrat)
	  {
	    if (empty($aCon[$dataContrat->id]))
	      if (!empty($dataContrat->array_options['options_ref_contrato']))
		$aArray[$dataContrat->id] = $dataContrat->array_options['options_ref_contrato'];
	  }
	print $form->selectarray('fk_contrat',$aArray);
	print '</td></tr>';

	//contratos vigentes
	print '<tr><td class="fieldrequired">'.$langs->trans('Existingcontracts').'</td><td colspan="3">';
	$objcont->socid = $objpcon->fk_socid;
	$aContrat = getListOfContracts($objcont->socid);
	$aArray = array();
	$aArray[] = '';
	//contratos ya registrados
	$objpcon->getidscontrat();
	$aCon = $objpcon->array;
	foreach((array) $aContrat AS $j => $dataContrat)
	  {
	    if (!empty($dataContrat->array_options['options_ref_contrato']))
	      $aArray[$dataContrat->id] = $dataContrat->array_options['options_ref_contrato'];
	  }
	print $form->selectarray('fk_contrat_exist',$aArray);
	print '</td></tr>';

	
	print '</table>';
	
	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
	
	print '</form>';
	print '</div>';
	
      }
    else
      {
	if ($idc && ($action=='selcon' || $action=='editc' || $action=='editop'))
	  {
	    $objpcon->fetch($idc);
	    print_fiche_titre($langs->trans("Committed"), $mesg);
	    dol_htmloutput_mesg($mesg);
	    
	    print '<table class="border" style="min-width=1000px" width="100%">';
	    
	    // contratos
	    print '<tr><td width="15%">'.$langs->trans('Contrat').'</td><td colspan="3">';
	    $objcont->fetch($objpcon->fk_contrat);
	    
	    print $objcont->ref.' '.$objcont->array_options['options_ref_contrato'];
	    print '</td></tr>';

	    print '<tr><td>'.$langs->trans('Status').'</td><td colspan="3">';
	    print $objpcon->LibStatut($objpcon->statut);
	    print '</td></tr>';

	  //subir imagen
	  print '<tr><td>'.$langs->trans('PDF').'</td><td colspan="2">';
	  $dir = $conf->contrat->dir_output.'/'.$objcont->ref."/".$objpcon->fk_contrat.'.pdf';
	  $url = DOL_URL_ROOT.'/documents/contracts/'.$objcont->ref."/".$objpcon->fk_contrat.'.pdf';

	  if ($user->admin || $user->rights->poa->proc->mod)
	    if ($actionsub !='upload')
	      {
		print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idc='.$idc.'&action=selcon&actionsub=upload'.'">'.img_picto($langs->trans('Uploaddoc'),DOL_URL_ROOT.'/poa/img/subir.png','',1).'</a>';
		//mostramos el archivo
		if (file_exists($dir))
		  {
		    print '&nbsp;&nbsp;';
		    print '<a href="'.$url.'" target="_blank">'.img_picto($langs->trans('PDF'),'pdf2').'</a>';
		  }
	      }
	    else
	      {
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
	    
	    print '</table>';

	    //lista las partidas
	    //echo $object->fk_poa_prev.' '.$idc.' '.$objpcon->statut;
	    //$objpp->getlist($object->fk_poa_prev,$idc);
	    $objpp->getlist($object->fk_poa_prev);
	    //buscamos si tiene dependientes
	    $objectd = new Poaprev($db);
	    $objectd->getlistfather($object->fk_poa_prev);
	    $aPartida = array();
	    if (count($objectd->arrayf)>0)
	      {
		$objppf = new Poapartidapre($db);
		//obtenemos en un array las partidas que se sumaran
		foreach((array) $objectd->arrayf AS $id1 => $objectp)
		  {
		    $objppf->getlist($id1);
		    if (count($objppf->array) > 0)
		      {
			foreach((array) $objppf->array AS $j => $objpart)
			  {
			    $aPartida[$objpart->fk_structure][$objpart->fk_poa][$objpart->partida]+=$objpart->amount;
			  }
		      }
		  }
	      }
	    print '<br>';
	    if ($objpcon->statut == 0)
	      {
		print '<form name="fiche_pas1" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="updateprod">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		print '<input type="hidden" name="idc" value="'.$idc.'">';
	      }
	    if ($action == 'editc')
	      {
		print '<form name="fiche_pas1" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="updatecomp">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		print '<input type="hidden" name="idc" value="'.$idc.'">';
	      }
	    if ($action == 'editop')
	      {
		print '<form name="fiche_pas1" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="updateorder">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		print '<input type="hidden" name="idc" value="'.$idc.'">';
	      }

	    print '<table class="border" style="min-width=1000px" width="100%">';
	    
	    if ($action == 'editop')
	      {
		//date
		print '<tr><td width="150px" class="fieldrequired">'.$langs->trans('Dateorderproceed').'</td><td colspan="3">';
		if ($user->admin)
		  $form->select_date($objpcon->date_order_proceed,'di_','','','',"date",1,1);
		print '</td></tr>';
	      }
	    else
	      {
		// partidas
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Partida"),"", "","","");
		print_liste_field_titre($langs->trans("Amount"),"", "","","");
		print_liste_field_titre($langs->trans("Product"),"", "","","");
		print_liste_field_titre($langs->trans("Quant"),"", "","",'','align="right"');
		print_liste_field_titre($langs->trans("Adjudicado"),"", "","",'','align="right"');
		print_liste_field_titre($langs->trans("Contract amount"),"", "","",'','align="right"');
		
		print '</tr>';
		if (count($objpp->array) > 0)
		  {
		    $lRegcont = false;
		    $var = true;
		    foreach((array) $objpp->array AS $j => $objpart)
		      {
			//buscamos los productos con esta partida
			$objppd->getlist($objpart->id,$idc,$objpcon->statut,'S');
			// echo '<pre>';
			// print_r($objppd->array);
			// echo '</pre>';
			//echo '<hr>count '.count($objppd->array).' id ' .$objpart->id;
			if (count($objppd->array) > 0)
			  {
			    foreach((array) $objppd->array AS $k => $objpartdet)
			      {
				//sumamos si tiene dependientes el preventivo
				$amount = $aPartida[$objpart->fk_structure][$objpart->fk_poa][$objpart->partida] + $objpart->amount;
				if ($objpartdet->quant_adj > 0)
				  $lRegcont = true;
				$var=!$var;
				print "<tr $bc[$var]>";
				print '<td>'.$objpartdet->id.' '.$objpart->partida.'</td>';
				print '<td align="right">'.number_format(price2num($amount,'MT'),2).'</td>';
				print '<input type="hidden" name="amountPart['.$objpartdet->id.']" value="'.$amount.'">';
				
				print '<td>'.$objpartdet->detail.'</td>';
				print '<td align="right">'.$objpartdet->quant.'</td>';
				if ($objpcon->statut == 0 || $action == 'editc')
				  {
				    print '<td align="right">'.'<input type="number" class="width50" name="quant_adj['.$objpartdet->id.']" value="'.(empty($objpartdet->quant_adj)?$objpartdet->quant:$objpartdet->quant_adj).'" maxlength="12">'.'</td>';
				    print '<td align="right">'.'<input type="number" class="width100" step="any" name="amount['.$objpartdet->id.']" value="'.$objpartdet->amount.'" maxlength="15">'.'</td>';
				  }
				else
				  {
				    print '<td align="right">'.$objpartdet->quant_adj.'</td>';
				    print '<td align="right">'.number_format(price2num($objpartdet->amount,'MT'),2).'</td>';
				  }
				print '</tr>';
			      }
			  }
		      }
		  }
	      }
	    print '</table>';

	    if ($objpcon->statut == 0 || $action == 'editc' || $action == 'editop')
	      {
		print '<center><br>';
		print '<input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
		print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">&nbsp;';
		if ($action == 'selcon' && $lRegcont)
		  {
		    
		    print '<input type="submit" class="button" name="approved" value="'.$langs->trans("Save and approve").'"></center>';
		  }
		print '</form>';
	      }
	    // //lista documentos
	    // include DOL_DOCUMENT_ROOT.'/poa/process/tpl/doc_add.tpl.php';
	    print '</div>';

	  }
      }

    //lista los contratos registrados
    $objpcon->getlist($object->id);
    if (count($objpcon->array) > 0 && $action == '')
      {
	$lOrderproceed = false;
	$lWell = false;
	print '<table class="border" style="min-width=1000px" width="100%">';
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Contrat"),"", "","","",'');
	print_liste_field_titre($langs->trans("Date"),"", "","","",'');
	print_liste_field_titre($langs->trans("Term"),"", "","","",'');
	print_liste_field_titre($langs->trans("Typeterm"),"", "","","",'');
	if ($objprev->code_requirement != 'WELL' &&
	    $objprev->code_requirement != 'DIRECTEXPENS' &&
	    $objprev->code_requirement != 'PERSONALSERVICE')
	  {
	    //rqc
	    print_liste_field_titre($langs->trans("Orderproceed"),"", "","","",'align="center"');

	    $lOrderproceed = true;
	  }
	else
	  $lWell = true;

	print_liste_field_titre($langs->trans("Dateend"),"", "","","",'');
	if ($lOrderproceed)
	  {
	    print_liste_field_titre($langs->trans("Provisional"),"", "","","",'align="center"');
	    print_liste_field_titre($langs->trans("Definitive"),"", "","","",'align="center"');
	    print_liste_field_titre($langs->trans("Nonconformity"),"", "","","",'align="center"');
	    print_liste_field_titre($langs->trans("Motif"),"", "","","",'align="center"');
	  }
	if ($lWell)
	  {
	    print_liste_field_titre($langs->trans("Definitive"),"", "","","",'align="center"');
	    print_liste_field_titre($langs->trans("Nonconformity"),"", "","","",'align="center"');
	    print_liste_field_titre($langs->trans("Motif"),"", "","","",'align="center"');
	  }
	print_liste_field_titre($langs->trans("Amount"),"", "","","",'align="right"');
	print_liste_field_titre($langs->trans("Committed"),"", "","","",'align="right"');
	print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
	print '</tr>';
	
	$var = true;
	foreach((array) $objpcon->array AS $j => $objpc)
	  {
	    $total_ht = 0;
	    $total_tva = 0;
	    $total_localtax1 = 0;
	    $total_localtax2 = 0;
	    $total_ttc = 0;
	    $total_plazo = 0;
	    $date_final = '';
	    $contratAdd = '';

	    $objpcontrat = $objpc;
	    $objcont = new Contrat($db);
	    $objcont->fetch($objpcontrat->fk_contrat);
	    $objcont->fetch_lines();
	    
	    if ($res < 0) { dol_print_error($db,$objcont->error); exit; }
	    $res=$objcont->fetch_optionals($objcont->id,$extralabels);

	    if ($objcont->id == $objpcontrat->fk_contrat)
	      {
		$total_plazo += $objcont->array_options['options_plazo'];
		//recuperamos el valor de contrato
		foreach ($objcont->lines AS $olines)
		  {
		    $total_ht += $olines->total_ht;
		    $total_tva += $olines->total_tva;
		    $total_localtax1 += $olines->total_localtax1;
		    $total_localtax2 += $olines->total_localtax2;
		    $total_ttc += $olines->total_ttc;
		  }

		$datecontrat= $objcont->date_contrat;
		//buscamos si tiene addendum
		if ($conf->addendum->enabled)
		  {
		    $objadden = new Addendum($db);
		    $objadden->getlist($objpcontrat->fk_contrat);
		    $total_ht += $objadden->total_ht;
		    $total_tva += $objadden->total_tva;
		    $total_localtax1 += $objadden->total_localtax1;
		    $total_localtax2 += $objadden->total_localtax2;
		    $total_ttc += $objadden->total_ttc;
		    //verificamos los plazos adicionales
		    foreach ((array) $objadden->array AS $j1 => $obja)
		      {
			$objcontade = new Contrat($db);
			$objcontade->fetch($obja->fk_contrat_son);
			if ($objcontade->id == $obja->fk_contrat_son)
			  $total_plazo += $objcontade->array_options['options_plazo'];
			if (!empty($contratAdd))$contratAdd.=', ';
			$contratAdd.= $objcontade->array_options['options_ref_contrato'];
		      }
		  }
		
		//si el registro de orden proceder, recepcion esta vacio en objpcontrat reemplazamos por el contrato
		
		if (empty($objpcontrat->date_order_proceed) ||
		    is_null($objpcontrat->date_order_proceed))
		  {
		    foreach ((array) $objcont->lines AS $k => $objl)
		      {
			$objcontline = new Contratligne($db);
			$objcontline->fetch($objl->id);
			//objpcontratnew
			$objpcnew = new Poaprocesscontrat($db);
			if ($objpcnew->fetch($objpcontrat->id)>0)
			  {
			    if ($objl->date_ouverture>0)
			      $objpcnew->date_order_proceed = $objl->date_ouverture;
			    if ($objl->date_validate>0)
			      $objpcnew->date_provisional = $objl->date_fin_validite;
			    if ($objl->date_cloture>0)
			      $objpcnew->date_final = $objl->date_cloture;
			    $objpcnew->update($user);
			    $objpcontrat->fetch($objpc->id);
			  }
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
		print '<td>'.'<a href="fiche_pas1.php?action=selcon&id='.$object->id.'&idc='.$objpcontrat->id.(isset($_GET['nopac'])?'&nopac=1&idp='.$_GET['idp']:'').'">'.$objcont->array_options['options_ref_contrato'].'</a></td>';

		print '<td>'.dol_print_date($objcont->date_contrat,'day').'</td>';

		//term
		print '<td>'.$total_plazo.'</td>';
		//typeterm
		if ($objcont->array_options['options_cod_plazo']==1)
		  print '<td>'.$langs->trans('D.C.').'</td>';
		if ($objcont->array_options['options_cod_plazo']==2)
		  print '<td>'.$langs->trans('D.H').'</td>';
		if ($objcont->array_options['options_cod_plazo']==3)
		  print '<td>'.$langs->trans('AC.').'</td>';
		if ($lOrderproceed)
		  {
		    //rqc
		    print '<td align="center">';
		    // print dol_print_date($objpcontrat->date_order_proceed,'day');
		    // print '</td>';
		    $idTagps = 'di_'.$objpcontrat->id;
		    $idTagps2 = 'di_'.$objpcontrat->id.'_';
		    $idTagps3 = 'dpp'.$objpcontrat->id;
		    if (($user->rights->poa->comp->mod && $objprev->statut < 3 && $user->id == $objprev->fk_user_create) || $user->admin)
		      {
			print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">';
			print '<form id="fiche_pasa" name="fiche_pasa" onsubmit="enviaoc()" return="false" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="updateop">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="idc" value="'.$objpcontrat->id.'">';
			$aDate = dol_getdate($objpcontrat->date_order_proceed);

			// //original			
			print '<input type="date" name="di_'.$objpcontrat->id.'" id="di_'.$objpcontrat->id.'" value="'
			  .dol_print_date($objpcontrat->date_order_proceed,'day')
			  // .(stristr(STRTOUPPER($_SERVER['HTTP_USER_AGENT']),'FIREF')===FALSE?
			  //   $objpcontrat->date_order_proceed:
			  //   $aDate['mday'].'-'.$aDate['mon'].'-'.$aDate['year'])
			  .'" onblur='."'".'CambiarURLFrametwo("'.$idTagps.'","'.$idTagps2.'","'.$idTagps3
			  .'","'.$objpcontrat->id.'","'.$objcont->array_options['options_plazo']
			  .'",'.'this.value);'."'". 'size="12" placeholder="'
			  .(stristr(STRTOUPPER($_SERVER['HTTP_USER_AGENT']),'FIREF')===FALSE?'':'YYYY-MM-DD').'">';

			print '</form>';
			print '</span>';

			
			print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick='."'".'visual_four("'.$idTagps.'" , "'.$idTagps2.'")'."'".'>';
			print (empty($objpcontrat->date_order_proceed)?img_picto($langs->trans('Register'),'edit').'&nbsp;':dol_print_date($objpcontrat->date_order_proceed,'day'));
			print '</span>';
		      }
		    else
		      {
			print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;">';
			print (empty($objpcontrat->date_order_proceed)?img_picto($langs->trans('Register'),'edit'):dol_print_date($objpcontrat->date_order_proceed,'day'));
			print '</span>';
		      }
		    print '</td>';
		  }

		//plazo final
		if ($lOrderproceed)
		  $datecontrat = (!empty($objpcontrat->date_order_proceed)?$objpcontrat->date_order_proceed:$objcont->date_contrat);
		//echo '<hr>|'.$datecontrat.'| |'.$objcont->array_options['options_cod_plazo'].'| '.$lOrderproceed;
		$datefinal = date_end($datecontrat,$objcont->array_options['options_cod_plazo'],$objcont->array_options['options_plazo']);
		print '<td><span id="dpp'.$objpcontrat->id.'">'.dol_print_date($datefinal,'day').'</span></td>';
		
		// if ($objcont->array_options['options_cod_plazo']==1)
		//   {
		//     $datecontrat = $objcont->date_contrat;
		//     if ($lOrderproceed)
		//       $datecontrat = (!empty($objpcontrat->date_order_proceed)?$objpcontrat->date_order_proceed:$objcont->date_contrat);
		//     $datefinal = diacalend($datecontrat,$objcont->array_options['options_plazo']);
		//     print '<td><span id="dpp'.$objpcontrat->id.'">'.dol_print_date($datefinal,'day').'</span></td>';
		//   }
		// else if ($objcont->array_options['options_cod_plazo']==2)
		//   {
		//     $datecontrat = $objcont->date_contrat;
		//     if ($lOrderproceed)
		//       $datecontrat = (!empty($objpcontrat->date_order_proceed)?$objpcontrat->date_order_proced:$objcont->date_contrat);			
		//     $datefinal = diahabil($datecontrat,$objcont->array_options['options_plazo']);
		//     print '<td><span id="dpp'.$objpcontrat->id.'">'.dol_print_date($datefinal,'day').'</span></td>';
		//   }
		// else
		//   print '<td>AC</td>';

		if ($lOrderproceed)
		  {
		    if (($user->rights->poa->comp->mod && $objprev->statut < 3 && $user->id == $objprev->fk_user_create) || $user->admin)
		      {

			$lreception = true;
			if ($objpcontrat->date_nonconformity>0)
			  $lreception = false;
			if (empty($objpcontrat->date_order_proceed) || is_null($objpcontrat->date_order_proceed))
			  $lreception = false;
			//rqc
			$idTagps = 'dp_'.$objpcontrat->id;
			$idTagps2 = 'dp_'.$objpcontrat->id.'_';
			print '<td align="center">';
			//registro de la fecha provisinal
			print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">';
			print '<form id="fiche_pasa" name="fiche_pasa" onsubmit="enviaoc()" return="false" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="updatedp">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="idc" value="'.$objpcontrat->id.'">';
			$aDate = dol_getdate($objpcontrat->date_provisional);
			
			//original			
			print '<input type="date" name="dp_'.$objpcontrat->id.'" id="dp_'.$objpcontrat->id.'" value="'
			  .dol_print_date($objpcontrat->date_provisional,'day')
			  .'" onblur='."'".'CambiarURLFramedp("'.$idTagps.'","'.$idTagps2.'","'.$idTagps3
			  .'","'.$objpcontrat->id.'","'.$objcont->array_options['options_plazo']
			  .'",'.'this.value);'."'". 'size="12" placeholder="'
			  .(stristr(STRTOUPPER($_SERVER['HTTP_USER_AGENT']),'FIREF')===FALSE?'':'DD/MM/YYYY').'">';
			
			print '</form>';
			print '</span>';
			
			if ($lreception)
			  {
			    print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick='."'".'visual_four("'.$idTagps.'" , "'.$idTagps2.'")'."'".'>';
			    print (empty($objpcontrat->date_provisional)?img_picto($langs->trans('Register'),'edit').'&nbsp;':dol_print_date($objpcontrat->date_provisional,'day'));
			    print '</span>';
			  }
			else
			  print '&nbsp;';
			//print dol_print_date($objpcontrat->date_provisional,'day');
			print '</td>';
			
			//FINAL
			$idTagps = 'df_'.$objpcontrat->id;
			$idTagps2 = 'df_'.$objpcontrat->id.'_';
			print '<td align="center">';
			//registro de la fecha final
			print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">';
			print '<form id="fiche_pasa" name="fiche_pasa" onsubmit="enviaoc()" return="false" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="updatedf">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="idc" value="'.$objpcontrat->id.'">';
			$aDate = dol_getdate($objpcontrat->date_final);
			
			//original			
			print '<input type="date" name="df_'.$objpcontrat->id.'" id="df_'.$objpcontrat->id.'" value="'
			  .dol_print_date($objpcontrat->date_final,'day')
			  .'" onblur='."'".'CambiarURLFramedf("'.$idTagps.'","'.$idTagps2.'","'.$idTagps3
			  .'","'.$objpcontrat->id.'","'.$objcont->array_options['options_plazo']
			  .'",'.'this.value);'."'". 'size="12" placeholder="'
			  .(stristr(STRTOUPPER($_SERVER['HTTP_USER_AGENT']),'FIREF')===FALSE?'':'DD/MM/YYYY').'">';
			
			print '</form>';
			print '</span>';
			
			if ($lreception)
			  {
			    print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick='."'"
			      .'visual_four("'.$idTagps.'" , "'.$idTagps2.'")'."'".'>';
			    print (empty($objpcontrat->date_final)?img_picto($langs->trans('Register'),'edit').'&nbsp;':dol_print_date($objpcontrat->date_final,'day'));
			    print '</span>';
			  }
			else
			  print '&nbsp;';
			// print dol_print_date($objpcontrat->date_final,'day');
			print '</td>';
			//no conformity
			$lnonconformity = true;
			if ($objpcontrat->date_provisional>0 || $objpcontrat->date_final > 0)
			  $lnonconformity = false;
			if (empty($objpcontrat->date_order_proceed) || is_null($objpcontrat->date_order_proceed))
			  $lnonconformity = false;
			
			$idTagps = 'dn_'.$objpcontrat->id;
			$idTagps2 = 'dn_'.$objpcontrat->id.'_';
			print '<td align="center">';
			//registro de la fecha final
			print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">';
			print '<form id="fiche_pasa" name="fiche_pasa" onsubmit="enviaoc()" return="false" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="updatedn">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="idc" value="'.$objpcontrat->id.'">';
			$aDate = dol_getdate($objpcontrat->date_nonconformity);
			//original			
			print '<input type="date" name="dn_'.$objpcontrat->id.'" id="dn_'.$objpcontrat->id.'" value="'
			  .dol_print_date($objpcontrat->date_nonconformity,'day')
			  .'" onblur='."'".'CambiarURLFramedn("'.$idTagps.'","'.$idTagps2.'","'.$idTagps3
			  .'","'.$objpcontrat->id.'","'.$objcont->array_options['options_plazo']
			  .'",'.'this.value);'."'". 'size="12" placeholder="'
			  .(stristr(STRTOUPPER($_SERVER['HTTP_USER_AGENT']),'FIREF')===FALSE?'':'DD/MM/YYYY').'">';
			print '</form>';
			print '</span>';
			
			if ($lnonconformity)
			  {
			    print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick='."'"
			      .'visual_four("'.$idTagps.'" , "'.$idTagps2.'")'."'".'>';
			    print (empty($objpcontrat->date_nonconformity)?img_picto($langs->trans('Register'),'edit').'&nbsp;':dol_print_date($objpcontrat->date_nonconformity,'day'));
			    print '</span>';
			  }
			else
			  print '&nbsp;';
			//print dol_print_date($objpcontrat->nonconformity,'day');
			print '</td>';
			
			//motif
			$idTagps = 'nc_'.$objpcontrat->id;
			$idTagps2 = 'nc_'.$objpcontrat->id.'_';
			print '<td align="center">';
			//registro de la fecha final
			print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">';
			print '<form id="fiche_pasa" name="fiche_pasa" onsubmit="enviaoc()" return="false" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="updatenc">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="idc" value="'.$objpcontrat->id.'">';
			//original			
			print '<input type="text" name="nc_'.$objpcontrat->id.'" id="nc_'.$objpcontrat->id.'" value="'
			  .$objpcontrat->motif
			  .'" onblur='."'".'CambiarURLFramenc("'.$idTagps.'","'.$idTagps2.'","'.$idTagps3
			  .'","'.$objpcontrat->id.'","'.$objcont->array_options['options_plazo']
			  .'",'.'this.value);'."'". 'size="25" placeholder="'.$langs->trans('Motif').'">';
			print '</form>';
			print '</span>';
			
			if ($objpcontrat->date_nonconformity>0)
			  {
			    print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick='."'"
			      .'visual_four("'.$idTagps.'" , "'.$idTagps2.'")'."'".'>';
			    print (empty($objpcontrat->motif)?img_picto($langs->trans('Register'),'edit').'&nbsp;':$objpcontrat->motif);
			    print '</span>';
			  }
			else
			  print '&nbsp;';
			
			print '</td>';
		      }
		    else
		      {
			print '<td>';
			print dol_print_date($objpcontrat->date_provisional,'day');
			print '</td>';
			print '<td>';
			print dol_print_date($objpcontrat->date_final,'day');
			print '</td>';
			print '<td>';
			print dol_print_date($objpcontrat->date_nonconformity,'day');
			print '</td>';
			print '<td>';
			print $objpcontrat->motif;
			print '</td>';

		      }
		  }
		//lWell
		if ($lWell)
		  {
		    if (($user->rights->poa->comp->mod && $objprev->statut < 3 && $user->id == $objprev->fk_user_create) || $user->admin)
		      {
			
			$lreception = true;
			if ($objpcontrat->date_nonconformity>0)
			  $lreception = false;
			//rqc
			
			//FINAL
			$idTagps = 'df_'.$objpcontrat->id;
			$idTagps2 = 'df_'.$objpcontrat->id.'_';
			print '<td align="center">';
			//registro de la fecha final
			print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">';
			print '<form id="fiche_pasa" name="fiche_pasa" onsubmit="enviaoc()" return="false" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="updatedf">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="idc" value="'.$objpcontrat->id.'">';
			$aDate = dol_getdate($objpcontrat->date_final);
			
			//original			
			print '<input type="date" name="df_'.$objpcontrat->id.'" id="df_'.$objpcontrat->id.'" value="'
			  .dol_print_date($objpcontrat->date_final,'day')
			  .'" onblur='."'".'CambiarURLFramedf("'.$idTagps.'","'.$idTagps2.'","'.$idTagps3
			  .'","'.$objpcontrat->id.'","'.$objcont->array_options['options_plazo']
			  .'",'.'this.value);'."'". 'size="12" placeholder="'
			  .(stristr(STRTOUPPER($_SERVER['HTTP_USER_AGENT']),'FIREF')===FALSE?'':'DD/MM/YYYY').'">';
			
			print '</form>';
			print '</span>';
			
			if ($lreception)
			  {
			    print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick='."'"
			      .'visual_four("'.$idTagps.'" , "'.$idTagps2.'")'."'".'>';
			    print (empty($objpcontrat->date_final)?img_picto($langs->trans('Register'),'edit').'&nbsp;':dol_print_date($objpcontrat->date_final,'day'));
			    print '</span>';
			  }
			else
			  print '&nbsp;';
			print '</td>';
			
			//no conformity
			$lnonconformity = true;
			if ($objpcontrat->date_final > 0)
			  $lnonconformity = false;
			
			$idTagps = 'dn_'.$objpcontrat->id;
			$idTagps2 = 'dn_'.$objpcontrat->id.'_';
			print '<td align="center">';
			//registro de la fecha final
			print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">';
			print '<form id="fiche_pasa" name="fiche_pasa" onsubmit="enviaoc()" return="false" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="updatedn">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="idc" value="'.$objpcontrat->id.'">';
			$aDate = dol_getdate($objpcontrat->date_nonconformity);
			//original			
			print '<input type="date" name="dn_'.$objpcontrat->id.'" id="dn_'.$objpcontrat->id.'" value="'
			  .dol_print_date($objpcontrat->date_nonconformity,'day')
			  .'" onblur='."'".'CambiarURLFramedn("'.$idTagps.'","'.$idTagps2.'","'.$idTagps3
			  .'","'.$objpcontrat->id.'","'.$objcont->array_options['options_plazo']
			  .'",'.'this.value);'."'". 'size="12" placeholder="'
			  .(stristr(STRTOUPPER($_SERVER['HTTP_USER_AGENT']),'FIREF')===FALSE?'':'DD/MM/YYYY').'">';
			print '</form>';
			print '</span>';
			
			if ($lnonconformity)
			  {
			    print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick='."'"
			      .'visual_four("'.$idTagps.'" , "'.$idTagps2.'")'."'".'>';
			    print (empty($objpcontrat->date_nonconformity)?img_picto($langs->trans('Register'),'edit').'&nbsp;':dol_print_date($objpcontrat->date_nonconformity,'day'));
			    print '</span>';
			  }
			else
			  print '&nbsp;';
			print '</td>';
			
			//motif
			$idTagps = 'nc_'.$objpcontrat->id;
			$idTagps2 = 'nc_'.$objpcontrat->id.'_';
			print '<td align="center">';
			//registro de la fecha final
			print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">';
			print '<form id="fiche_pasa" name="fiche_pasa" onsubmit="enviaoc()" return="false" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="updatenc">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="idc" value="'.$objpcontrat->id.'">';
			//original			
			print '<input type="text" name="nc_'.$objpcontrat->id.'" id="nc_'.$objpcontrat->id.'" value="'
			  .$objpcontrat->motif
			  .'" onblur='."'".'CambiarURLFramenc("'.$idTagps.'","'.$idTagps2.'","'.$idTagps3
			  .'","'.$objpcontrat->id.'","'.$objcont->array_options['options_plazo']
			  .'",'.'this.value);'."'". 'size="25" placeholder="'.$langs->trans('Motif').'">';
			print '</form>';
			print '</span>';
			
			if ($objpcontrat->date_nonconformity>0)
			  {
			    print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick='."'"
			      .'visual_four("'.$idTagps.'" , "'.$idTagps2.'")'."'".'>';
			    print (empty($objpcontrat->motif)?img_picto($langs->trans('Register'),'edit').'&nbsp;':$objpcontrat->motif);
			    print '</span>';
			  }
			else
			  print '&nbsp;';
			
			print '</td>';
		      }
		    else
		      {
			print '<td>';
			print dol_print_date($objpcontrat->date_final,'day');
			print '</td>';
			print '<td>';
			print dol_print_date($objpcontrat->date_nonconformity,'day');
			print '</td>';
			print '<td>';
			print $objpcontrat->motif;
			print '</td>';
		      }
		  }
		//fin lWell
		//importe contrat
		// if ($conf->contratadd->enabled)//REVISAR URGENTE RQC
		//   {
		//     $objcontratadd = new Contratadd($db);
		//     $objcontratadd->get_suma_contratdet($objpcontrat->fk_contrat);
		    
		//     print '<td align="right">'.price($objcontratadd->total_ttc).'</td>';
		//   }
		// else
		  print '<td align="right">'.price($total_ttc).'</td>';
		//commited
		$amountc = 0;
		$rescom = $objcom->getlist($object->fk_poa_prev,$objpcontrat->id);
		if (count($objcom->array)>0)
		  foreach((array) $objcom->array AS $k => $objc)
		    $amountc += $objc->amount;
		print '<td align="right">';
		print price($amountc);
		print '</td>';

		if (empty($action) && $objpcontrat->statut == 1)
		  {
		    print '<td align="right">';
		    //modify
		    if ($user->rights->poa->comp->mod)
		      {
			print '<a href="fiche_pas1.php?id='.$id.'&amp;idc='.$objpcontrat->id.'&action=editc'.(isset($_GET['nopac'])?'&nopac=1&idp='.$_GET['idp']:'').'">'.img_picto($langs->trans('Edit'),DOL_URL_ROOT.'/poa/img/edit.png','',1).'</a>';
			print '&nbsp';
		    print '&nbsp';
		      }
		    //cancel
		    $objdev->getlist($object->fk_poa_prev,$objpcontrat->id/*$idc*/);
		    $lCancel = true;
		    if (count($objdev->array) > 0)
		      $lCancel = false;
		    if ($user->rights->poa->comp->nul && $lCancel)
		      print '<a href="fiche_pas1.php?id='.$id.'&amp;idc='.$objpcontrat->id.'&idp='.$objprev->id.'&action=anulate'.(isset($_GET['nopac'])?'&nopac=1&idp='.$_GET['idp']:'').'">'.img_picto($langs->trans('Cancel'),DOL_URL_ROOT.'/poa/img/cancel.png','',1).'</a>';
		    else
		      print '&nbsp;';
		    print '&nbsp';
		    print '&nbsp';
		    if ($lOrderproceed)
		      {
			$objdev->getlist($object->fk_poa_prev,$objpcontrat->id/*$idc*/);
			$nLen = count($objdev->array);

			// if ($user->admin || ($user->id == $objprev->fk_user_create && $nLen <=0))
			//   print '<a href="fiche_pas1.php?id='.$id.'&amp;idc='.$objpcontrat->id.'&action=editop'.(isset($_GET['nopac'])?'&nopac=1&idp='.$_GET['idp']:'').'">'.img_picto($langs->trans('Orderproceed'),DOL_URL_ROOT.'/poa/img/proceed.png','',1).'</a>';
			// else
			//   print '&nbsp;';
			
			if (!empty($objpcontrat->date_order_proceed) && !is_null($objpcontrat->date_order_proceed))			  
			  {
			    if ($user->admin || $user->id == $objprev->fk_user_create)
			      print '<a href="fiche_pas2.php?id='.$id.'&idc='.$objpcontrat->id.'&action=create'.(isset($_GET['nopac'])?'&nopac=1&idp='.$_GET['idp']:'').'">'.img_picto($langs->trans('Payment'),DOL_URL_ROOT.'/poa/img/payment.png','',1).'</a>';
			    else
			      print '&nbsp;';
			  }
		      }
		    else
		      {
		    if ($user->admin || $user->id == $objprev->fk_user_create)
		      print '<a href="fiche_pas2.php?id='.$id.'&idc='.$objpcontrat->id.'&action=create'.(isset($_GET['nopac'])?'&nopac=1&idp='.$_GET['idp']:'').'">'.img_picto($langs->trans('Payment'),DOL_URL_ROOT.'/poa/img/payment.png','',1).'</a>';
		    else
		      print '&nbsp;';
			      }
		    print '</td>';
		  }
		if ($objpcontrat->statut == 2)
		  {
		    print '<td align="right">';
		    print $objpcontrat->LibStatut($objpcontrat->statut,0);
		    print '</td>';
		  }
		print '</tr>';
	      }
	    
	  }
	print '</table>';
      }
  }

llxFooter();

$db->close();
?>
