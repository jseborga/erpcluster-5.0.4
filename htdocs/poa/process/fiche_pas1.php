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
//require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprevprocess.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivity.class.php';
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
// if ($conf->contratadd->enabled)
//   require_once DOL_DOCUMENT_ROOT.'/contratadd/class/contratadd.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

$langs->load("poa@poa");

$action=GETPOST('action');

$id        = GETPOST("id"); //proces
$ida       = GETPOST('ida'); //actividad
$idc       = GETPOST("idc"); //registro rowid contrato
$idrc      = GETPOST('idrc'); //registro rowid poaprocescontrat
$cid       = GETPOST("cid"); //contrato
$idp       = GETPOST("idp"); //preventivo
$idpd      = GETPOST("idpd"); //partidapredet

if (!empty($id))
  {
    $ida = $_SESSION['aListip'][$id]['idAct'];
    $idp = $_SESSION['aListip'][$id]['idPrev'];
    $idpa= $_SESSION['aListip'][$id]['idPrevant'];
    $idc = $_SESSION['aListip'][$id]['idContrat'];
    $gestionact = $_SESSION['aListip'][$id]['gestion'];
  }
//echo '<hr>activ '.$ida.' prev '.$idp;
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
$objact  = new Poaactivity($db);
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

//print_r($_POST);
//exit;
// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($objcont->table_element);
if ($action == 'search')
  $action = 'createedit';
/*
 * Actions
 */
//deletecomp
if ($action=='deletecomp' &&
    $user->rights->poa->comp->crear &&
    $_POST["cancel"] <> $langs->trans("Cancel")
    )
{
    $error = 0;
    //buscamos en poapartidapredet
    $objppd->fetch($idpd);
    if ($idpd == $objppd->id)
    {
        if ($objppd->fk_contrat == $idrc)
        {
            $db->begin();
            $fk_poa_partida_comp = $objppd->fk_poa_partida_comp;
            $objppd->fk_contrat = 0;
            $objppd->fk_contrato = 0;
            $objppd->fk_poa_partida_com = 0;
            $objppd->quant_adj = 0;
            $objppd->amount = 0;
            $objppd->statut = 0;
            $res = $objppd->update($user);
            if (!$res >0) $error++;
            if (!$error)
            {
                //buscamos el comprometido
                $objcom->fetch($fk_poa_partida_comp);
                if ($objcom->id == $fk_poa_partida_comp)
                {
                    $res = $objcom->delete($user);
                    if (!$res >0) $error++;
                }
            }
            if (!$error)
            {
                $db->commit();
                header("Location: fiche_pas1.php?id=".$id.'&idrc='.$idrc.'&action=selcon');
                exit;
            }
            else
                $db->rollback();
        }
    }
    $action = '';
}

// Addcontrat
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
    $objpcon->date_create = dol_now();
    $objpcon->fk_user_create = $user->id;
    $objpcon->tms = dol_now();
    $objpcon->statut = 0;
    if (empty($objpcon->fk_contrat))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorcontratisrequired").'</div>';
      }

    //exit;
    if (empty($error))
      {
	//buscamos si existe poa_process y contrat con estado 0
	$objprocon = new Poaprocesscontrat($db);
	//echo '<hr>poa_process '.$objpcon->fk_poa_process.' '.$objpcon->fk_contrat;
	//echo '<hr>revisando si existe';
	if ($objprocon->fetch('',$objpcon->fk_poa_process,$objpcon->fk_contrat)>0)
	  {
	    //echo '<hr>objrocon';
	    //print_r($objprocon);
	    if ($objprocon->fk_poa_process == $objpcon->fk_poa_process &&
		$objprocon->fk_contrat == $objpcon->fk_contrat)
	      {
		$idrc = $objprocon->id;
		//header("Location: fiche_pas1.php?id=".$id.'&idrc='.$idrc.'&action=selcon');
		//exit;
	      }
	    else
	      {
		//registro nuevo
		$db->begin();
		$idrc = $objpcon->create($user);
	      }
	  }
	else
	  {
	    //registro nuevo
	    $db->begin();
	    $idrc = $objpcon->create($user);
	  }
	// echo '<hr>idrc '.$idrc;
	// 	exit;
	if ($idrc > 0)
	  {
	    $_SESSION['aListcont'][$idrc] = $objpcon->fk_contrat;
	    //buscamos el proceso
	    if ($object->fetch($id))
	      {
		if ($objprev->fetch($idp))
		  {
		    $objprev->statut = 2; //2 comprometido
		    if ($objprev->update($user) > 0)
		      {
			//exito;
		      }
		    else
		      {
			$error++;
		      }
		  }
	      }
	    //echo '<hr>errrint  '.$error;
	    if (empty($error))
	      {
		$db->commit();
		//echo '<hr>antes de salir con '.$id.' '.$idrc;
		//exit;
		header("Location: fiche_pas1.php?id=".$id.'&idrc='.$idrc.'&action=selcon');
		exit;
	      }
	  }
	//echo '<hr>errr '.$error;
	//exit;
	$db->rollback();
	$action = 'create';
	$mesg='<div class="error">'.$obpcon->error.'</div>';
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
			$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
			$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
		      }
		    header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&idc='.$idc.'&idrc='.$idrc.'&action=selcon&dol_hide_leftmenu=1');
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

	      if ($objpcon->fetch($idrc)>0)
		{
		  //cambiamos de estado tabla poa_process_contrat
		  $objpcon->statut = 2; //anulado
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
				      $objppdc->fk_contrato = 0;
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
		  if ($objcomp->fk_contrat == GETPOST('idrc'))
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

//confirm updateadvance
if ($action == 'confirm_updateadvance' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->comp->mod)
  {
    if ($object->fetch($_REQUEST["id"])>0)
      if ($objpcon->fetch($_REQUEST['idrc'])>0)
	if ($objcont->fetch($objpcon->fk_contrat)>0)
	  {
	    $_POST['options_advance'] = $_GET['options_advance'];
	    $extralabels = $extrafields->fetch_name_optionals_label($objcont->table_element);
	    $ret = $extrafields->setOptionalsFromPost($extralabels, $objcont, 'advance');
	    if ($ret < 0)
	      $error ++;
	    if (! $error)
	      {
		$result = $objcont->insertExtraFields();
		if ($result < 0) {
		  $error ++;
		}
	      }
	    else if ($reshook < 0)
	      $error ++;
	    //exit;

	    $action='';
	    if ($error) {
	      $action = 'edit_extras';
	      setEventMessage($object->error,'errors');
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
    $idp = $_SESSION['aListip'][$id]['idPrev'];
    //$idc = $_SESSION['aListcont'][$id];
    $objprev->fetch($idp);
    $db->begin();
    $error = 0;
    $aQuant_adj = GETPOST('quant_adj');
    $aAmount = GETPOST('amount');
    $aAmountPart = GETPOST('amountPart');
    if($objpcon->fetch(GETPOST('idrc')))
    {
        $idc = $objpcon->fk_contrat;
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
            				$objppd->fk_contrat = $idrc;
			             	$objppd->fk_contrato = $idc;
            				$objppd->statut=2;
    			         	if($objppd->update($user) <= 0) $error++;
				            //buscamos la partida para registro en comprometido
				            if ($objpp->fetch($objppd->fk_poa_partida_pre))
				            {
				                $objpp->getsum_str_part_det2($objprev->gestion,
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
				                if ($objcom->fetch_pcp2($objppd->fk_poa_partida_pre,
							     $idp,
							     $idc,
							     $objpp->partida) )
				                {
                                    if ($objcom->fk_poa_partida_pre == $objppd->fk_poa_partida_pre &&
				            	    $objcom->fk_poa_prev == $idp &&
					                $objcom->fk_contrato == $idc &&
					                $objcom->partida == $objpp->partida )
                                    {
					                   //existe y actualizamos
					                   $objcom->fk_poa = $objpp->fk_poa;
					                   $objcom->amount = $total;
					                   $objcom->fk_contrat = $idrc;
					                   $objcom->fk_contrato = $idc;
					                   if(!$objcom->update($user)>0) $error++;
					                }
					                else
                                    {
					                   //registramos en comprometido
					                    $objcom->fk_poa_partida_pre = $objppd->fk_poa_partida_pre;
                                        $objcom->fk_poa_prev = $objpp->fk_poa_prev;
                                        $objcom->fk_structure = $objpp->fk_structure;
                                        $objcom->fk_poa = $objpp->fk_poa;
                                        $objcom->fk_contrat = $idrc;
                                        $objcom->fk_contrato = $idc;
                                        $objcom->partida = $objpp->partida;
                                        $objcom->amount = $total;
                                        $objcom->date_create = date('Y-m-d');
                                        $objcom->tms = date('YmdHis');
                                        $objcom->statut = 1;
                                        $objcom->active = 1;
                                        $idcom = $objcom->create($user);
                                        if ($idcom > 0)
                                        {
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
                                $error++;
                            }
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
if ($action == 'updatecomp' && $user->rights->poa->comp->mod)
{
    $object->fetch($id);//process
    $objprev->fetch($idp);
    $objpcon->fetch($idrc); //poaprocescontrat
    if ($objpcon->id == $idrc)
        $idc = $objpcon->fk_contrat;
    $db->begin();
    $error = 0;
    $aQuant_adj = GETPOST('quant_adj');
    $aAmount = GETPOST('amount');
    $aAmountPart = GETPOST('amountPart');
    if (is_array($aAmount))
    {
        foreach((array) $aAmount AS $idreg => $value)
        {
            //buscamos el registro
            if ($objppd->fetch($idreg)>0)
            {
                if ($value > 0)
                {
                    if ($value <= $aAmountPart[$idreg])
                    {
                        $objppd->quant_adj = $aQuant_adj[$idreg];
                        $objppd->amount = $value;
                        $objppd->fk_contrat = $idrc;
                        $objppd->fk_contrato = $idc;
                        $objppd->statut=2;
                        if($objppd->update($user) <= 0) $error++;


                        //buscamos la partida para registro en comprometido
                        //echo '<hr>'.$objppd->fk_poa_partida_pre;
                        if ($objpp->fetch($objppd->fk_poa_partida_pre)>0)
                        {
                            // echo '<hr>'.$objppd->fk_poa_partida_pre;
                            // print_r($objpp);
                            $objpp->getsum_str_part_det2($objprev->gestion,
							 $objpp->fk_structure,
							 $objpp->fk_poa,
							 $objpp->id,
							 $idc,
							 $objpp->partida);
                            // echo '<hr>';
                            // print_r($objpp);
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
                            if ($objcom->fetch_pcp2($objppd->fk_poa_partida_pre,
						    $idp,
						    $idc,
						    $objpp->partida) )
			                {
				                // echo '<hr>comp ';
				                // print_r($objcom);
				                if ($objcom->fk_poa_partida_pre == $objppd->fk_poa_partida_pre &&
				                    $objcom->fk_poa_prev == $idp &&
				                    $objcom->fk_contrato == $idc &&
				                    $objcom->partida == $objpp->partida )
				                {
				                    // echo '<br>existe';
				                    //existe y actualizamos
				                    $objcom->fk_poa = $objpp->fk_poa;
				                    $objcom->amount = $total;
				                    $objcom->fk_contrat = $idc;
				                    if($objcom->update($user)>0)
				                    {
					                   $objppd->fk_poa_partida_com=$objcom->id;
					                   if($objppd->update($user) <= 0) $error++;
				                    }
				                    else
				                        $error++;
				                }
				                else
				                {
                				    //es necesario agregar el registro
				                    $objcom->fk_poa_partida_pre = $objppd->fk_poa_partida_pre;
			                 	    $objcom->fk_poa_prev = $idp;
				                    $objcom->fk_structure = $objpp->fk_structure;
				                    $objcom->fk_poa = $objpp->fk_poa;
				                    $objcom->fk_contrat = $idrc;
				                    $objcom->fk_contrato = $idc;
				                    $objcom->partida = $objpp->partida;
				                    $objcom->amount = $value;
				                    $objcom->date_create = dol_now();
				                    $objcom->statut = 1;
				                    $objcom->active = 1;
				                    $res = $objcom->create($user);
				                    // echo '<hr>res '.$res;print_r($objcom);
				                    if (!$res > 0) $error++;
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
		          }
	       }
	       else
	           $error++;
	   }
    }
    if (empty($error))
    {
	   $db->commit();
	   header("Location: fiche_pas1.php?id=".$id.'&dol_hide_leftmenu=1');
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
//$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
//$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');
//$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
//llxHeader("",$langs->trans("Commited"),$help_url,'','','',$aArrjs,$aArrcss);

header("Content-type: text/html; charset=".$conf->file->character_set_client);

$aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/bootstrap-responsive.min.css','poa/css/style-responsive.css','poa/css/AdminLTE.css');
$aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/dist/css/AdminLTE.css','poa/css/dist/css/AdminLTE.min.css','poa/css/dist/css/skins/_all-skins.min.css');
$aArrayofjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');

top_htmlhead($head,$langs->trans("POA"),0,0,$aArrayofjs,$aArrayofcss);

//impresion de submenu segun seleccion
include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/menup.tpl.php';


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
	$res = $object->update($user);
	if ($res > 0)
	  $db->commit();
	else
	  $db->rollback();
	$action = '';
      }

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
	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idrc='.$idrc.'&idp='.$idp,$langs->trans("Cancelcommited"),$langs->trans("Confirmcancelcommited",$object->ref.' '.$object->detail),"confirm_cancel",'yes',0,2);
	if ($ret == 'html') print '<br>';
      }


    // Confirm udpate advance
    if ($action == 'updateadvance')
      {
	$form = new Form($db);
	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idrc='.$idrc.'&options_advance='.$_GET['options_advance'],$langs->trans("Updateadvance"),$langs->trans("Confirmupdateadvance",$object->ref.' '.$object->detail),"confirm_updateadvance",'yes',0,2);
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
	$total = $objpp->getsum($object->fk_poa_prev);
	print '<tr>';
	print '<td width="5%">'.$objprev->nro_preventive.'</td>';
	print '<td width="5%">'.$objprev->gestion.'</td>';
	print '<td width="90%">'.$objprev->label.'</td>';
	print '<td align="right" width="5%">'.price($total).'</td>';
	print '</tr>';
	$aPrev[$objprev->id] = $objprev->gestion;
	//verificamos si tiene hijos
	$objprevh = new Poaprev($db);

	$objprevh->getlistfather($object->fk_poa_prev);

	foreach ((array) $objprevh->arrayf AS $j => $objp)
	  {
	    $total = $objpp->getsum($objp->id);
	    print '<tr>';
	    print '<td width="5%">'.$objp->nro_preventive.'</td>';
	    print '<td width="5%">'.$objp->gestion.'</td>';
	    print '<td width="90%">'.$objp->label.'</td>';
	    print '<td align="right" width="5%">'.price($total).'</td>';
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
    //revisar urgente
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

    if ($user->rights->poa->prev->leer)
      print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php'.(isset($_GET['nopac'])?'?nopac=1&ida='.$ida:'?ida='.$ida).'&dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
    else
      print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";

    if ($action == '')
      {
	if ($user->rights->poa->comp->crear)
	  if ($user->admin || $user->id == $objprev->fk_user_create)
	    print '<a class="butAction" href="fiche_pas1.php?id='.$object->id.'&action=create&dol_hide_leftmenu=1">'.$langs->trans("Createcontrat").'</a>';
	else
	    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createcontrat")."</a>";
      }
    // if ($action == 'selcon')
    //   {
    // 	if ($user->rights->poa->prev->leer)
    // 	  print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php'.(isset($_GET['nopac'])?'?nopac=1&idp='.$idp:'?id='.$idp).'">'.$langs->trans("Return").'</a>';
    // 	else
    // 	  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";
    //   }

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

	print '<form name="fiche_comp" action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="addcontrat">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

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

	// print "<div class=\"tabsAction\">\n";
	// print '<a class="butAction" href="'.$_SESSION['localuri'].'">'.$langs->trans("Return").'</a>';
	// print '</div>';

      }
    else
      {
	//idrc +> $idc;
        //echo '<hr>idrc '.$idrc;
	if ($idrc && ($action=='selcon' || $action=='editc' || $action=='editop'))
	  {
	    //buscamos el processcontrat
	    $objpcon->fetch($idrc);
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
		  print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idc='.$idc.'&idrc='.$objpcon->id.'&action=selcon&dol_hide_leftmenu=1&actionsub=upload'.'">'.img_picto($langs->trans('Uploaddoc'),DOL_URL_ROOT.'/poa/img/subir.png','',1).'</a>';
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

	    //$objpp->getlist($object->fk_poa_prev);
	    $objpp->getlist($idp);
	    //suma por fk_structure,fk_poa,partida
	    $objpp->getsumpartida($idp);
	    //buscamos si tiene dependientes
	    $objectd = new Poaprev($db);

	    //$objectd->getlistfather($object->fk_poa_prev);
	    $objectd->getlistfather($idp);
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
		print '<input type="hidden" name="idrc" value="'.$idrc.'">';
	      }
	    else
	      {
		if ($idpa>0 && $action != 'editc' && $action != 'editop')
		  {
		    print '<form name="fiche_pas1" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		    print '<input type="hidden" name="action" value="updateprod">';
		    print '<input type="hidden" name="id" value="'.$object->id.'">';
		    print '<input type="hidden" name="idrc" value="'.$idrc.'">';
		    print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
		  }
	      }
	    if ($action == 'editc')
	      {
		print '<form name="fiche_pas1" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="updatecomp">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		// print '<input type="hidden" name="idc" value="'.$idc.'">';
		print '<input type="hidden" name="idrc" value="'.$idrc.'">';
		// print '<input type="hidden" name="idp" value="'.$idp.'">';
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
		print_liste_field_titre($langs->trans("Action"),"", "","",'','align="center"');

		print '</tr>';
		//recuperamos las partidas del preventivo seleccionado $idp
		if (count($objpp->array) > 0)
		  {
		    $lRegcont = false;
		    $var = true;
		    //echo $idc;
			// echo '<pre>';
			// print_r($objpp->array);
			// echo '</pre>';
			// echo '<hr>idc '.$idc;

		    foreach((array) $objpp->array AS $j => $objpart)
		      {
			//buscamos la suma de la partida
			$amount = 0;
			foreach ((array) $objpp->arraysum AS $k => $arrvalue)
			  {
			    if ($arrvalue['fk_structure'] == $objpart->fk_structure &&
				$arrvalue['fk_poa'] == $objpart->fk_poa &&
				$arrvalue['partida'] == $objpart->partida)
			      $amount+= $arrvalue['amount'];
			  }
			//buscamos los productos con esta partida
			//echo '<hr>idc '.$idc.' '.$objpart->id.' '.$objpcon->statut;
			if ($objpcon->fetch($idrc)>0)
			  if ($objpcon->id == $idrc)
			    $idc = $objpcon->fk_contrat;

			if ($idpa>0)
			  $statutcon = 0;
			else
			  $statutcon = $objpcon->statut;
			//$objppd->getlist2($objpart->id,$idc,$statutcon,'S');
			$objppd->getlist2($objpart->id,$idc,$objpcon->statut,'S');
			// echo '<pre>';
			// print_r($objppd->array);
			// echo '</pre>';
			// echo '<hr>count '.count($objppd->array).' id ' .$objpart->id;
			if (count($objppd->array) > 0)
			  {
			    foreach((array) $objppd->array AS $k => $objpartdet)
			      {
				//sumamos si tiene dependientes el preventivo
				// $amount = $aPartida[$objpart->fk_structure][$objpart->fk_poa][$objpart->partida] + $objpart->amount;
				$amount += $aPartida[$objpart->fk_structure][$objpart->fk_poa][$objpart->partida];
				if ($objpartdet->quant_adj > 0)
				  $lRegcont = true;
				$var=!$var;
				print "<tr $bc[$var]>";
				print '<td>'.$objpart->partida.'</td>';
				print '<td align="right">'.number_format(price2num($amount,'MT'),2).'</td>';
				print '<input type="hidden" name="amountPart['.$objpartdet->id.']" value="'.$amount.'">';

				print '<td>'.$objpartdet->detail.'</td>';
				print '<td align="right">'.$objpartdet->quant.'</td>';
				//revisar que ocurre si son varios items y son de la anterior gestion
				//if ($statutcon == 0 || $action == 'editc')
				if ($objpcon->statut == 0 || $action == 'editc')
				  {
				    print '<td align="right">'.'<input type="number" class="width50" name="quant_adj['.$objpartdet->id.']" value="'.(empty($objpartdet->quant_adj)?$objpartdet->quant:$objpartdet->quant_adj).'" maxlength="12">'.'</td>';
				    print '<td align="right">'.'<input type="number" class="width100" step="any" name="amount['.$objpartdet->id.']" value="'.$objpartdet->amount.'" maxlength="15">'.'</td>';
                    print '<td align="center">'.'<a href="'.$_PHP['SELF'].'?id='.$id.'&idrc='.$idrc.'&idpd='.$objpartdet->id.'&action=deletecomp">'.img_picto($langs->trans('Delete'),'delete').'</a>'.'</td>';
				  }
				else
				  {
				    print '<td align="right">'.$objpartdet->quant_adj.'</td>';
				    print '<td align="right">'.number_format(price2num($objpartdet->amount,'MT'),2).'</td>';
				  }
				print '</tr>';
			      }
			  }
			else
			  {

			  }
		      }
		  }
	      }
	    print '</table>';

	    if ($objpcon->statut == 0 || ($objpcon->statut > 0 && $idpa > 0) &&( $action == 'editc' || $action == 'editop'))
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
    //se cambio idp por idpa segun corresponda
    $objpcon->getlist2(($idpa?$idpa:$idp));
    if (count($objpcon->array) > 0 && $action == '')
      {
	$lOrderproceed = false;
	$lWell = false;
	print '<table class="border" style="min-width=1000px" width="100%">';
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Contrat"),"", "","","",'');
	print_liste_field_titre($langs->trans("Date"),"", "","","",'');
	print_liste_field_titre($langs->trans("Advance"),"", "","","",'');
	print_liste_field_titre($langs->trans("Term"),"", "","","",'');
	print_liste_field_titre($langs->trans("Typeterm"),"", "","","",'');
	$ncolspan = 5;
	if ($objprev->code_requirement != 'WELL' &&
	    $objprev->code_requirement != 'DIRECTEXPENS' &&
	    $objprev->code_requirement != 'PERSONALSERVICE')
	  {
	    //rqc
	    print_liste_field_titre($langs->trans("Orderproceed"),"", "","","",'align="center"');
	    $ncolspan++;
	    $lOrderproceed = true;
	  }
	else
	  $lWell = true;

	print_liste_field_titre($langs->trans("Dateend"),"", "","","",'');
	$ncolspan++;
	if ($lOrderproceed)
	  {
	    print_liste_field_titre($langs->trans("Provisional"),"", "","","",'align="center"');
	    print_liste_field_titre($langs->trans("Definitive"),"", "","","",'align="center"');
	    print_liste_field_titre($langs->trans("Nonconformity"),"", "","","",'align="center"');
	    print_liste_field_titre($langs->trans("Motif"),"", "","","",'align="center"');
	    $ncolspan+=4;
	  }
	if ($lWell)
	  {
	    print_liste_field_titre($langs->trans("Definitive"),"", "","","",'align="center"');
	    print_liste_field_titre($langs->trans("Nonconformity"),"", "","","",'align="center"');
	    print_liste_field_titre($langs->trans("Motif"),"", "","","",'align="center"');
	    $ncolspan+=3;
	  }
	print_liste_field_titre($langs->trans("Amount"),"", "","","",'align="right"');
	$ncolspan++;
	print_liste_field_titre($langs->trans("Gestion"),"", "","","",'align="right"');
	print_liste_field_titre($langs->trans("Committed"),"", "","","",'align="right"');
	print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');

	print '</tr>';
	$var = true;
	foreach((array) $objpcon->array AS $j => $objpc)
	  {
	    //////REVISAR RQC
	    $total_ht = 0;
	    $total_tva = 0;
	    $total_localtax1 = 0;
	    $total_localtax2 = 0;
	    $total_ttc = 0;
	    $total_plazo = 0;
	    $date_final = '';
	    $contratAdd = '';
	    $objpcontrat = $objpc;//copiando
	    $objcont = new Contrat($db);
	    $res = $objcont->fetch($objpcontrat->fk_contrat);
	    $objcont->fetch_lines();
	    if ($res < 0) { dol_print_error($db,$objcont->error); exit; }
	    $res=$objcont->fetch_optionals($objcont->id,$extralabels);
	    //echo '<hr>'.$objcont->id .' '.$objpcontrat->fk_contrat;
	    if ($objcont->id == $objpcontrat->fk_contrat)
	      {
		$_SESSION['aListcont'][$object->id] = $objcont->id;
		$total_plazo += $objcont->array_options['options_plazo'];
		$datecontrat= $objcont->date_contrat;
		//buscamos si tiene addendum
		if ($conf->addendum->enabled)
		  {
		    $objadden = new Addendum($db);
		    $objadden->getlist($objpcontrat->fk_contrat);
		    $total_ht += $objadden->aSuma['total_ht'];
		    $total_tva += $objadden->aSuma['total_tva'];
		    $total_localtax1 += $objadden->aSuma['total_localtax1'];
		    $total_localtax2 += $objadden->aSuma['total_localtax2'];
		    $total_ttc += $objadden->aSuma['total_ttc'];
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
		print '<td>'.'<a href="fiche_pas1.php?action=selcon&dol_hide_leftmenu=1&id='.$object->id.'&idrc='.$objpcontrat->id.(isset($_GET['nopac'])?'&nopac=1&idp='.$_GET['idp']:'').'">'.$objcont->array_options['options_ref_contrato'].'</a></td>';

		print '<td>'.dol_print_date($objcont->date_contrat,'day').'</td>';
		//advance
		print '<td align="center">';
		if (($user->rights->poa->comp->mod && $objprev->statut < 3 && $user->id == $objprev->fk_user_create) || $user->admin)
		  print '<a title="'.$langs->trans('Updateadvance').'" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idrc='.$objpcontrat->id.'&action=updateadvance'.'&options_advance='.($objcont->array_options['options_advance']?0:1).'">'.($objcont->array_options['options_advance']?'Si':'No').'</a>';
		else
		  print ($objcont->array_options['options_advance']?'Si':'No');
		print '</td>';

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
		print '<td align="right">'.price($total_ttc).'</td>';
		//recuperamos los comprometidos por gestion
		//commited
		$nLoop = 1;
		//$ncolspan = 10;
		foreach ((array) $aPrev AS $fk_poaprev => $gest)
		  {
		    if ($nLoop>1)
		      {
			print '<tr>';
			print '<td colspan="'.$ncolspan.'"></td>';
		      }
		    $amountc = 0;
		    //echo '<hr>ncolspan '.$ncolspan.' '.$fk_poaprev.' '.$objpcontrat->id.' '.$gest;

		    $rescom = $objcom->getlist2($fk_poaprev,$objpcontrat->fk_contrat);
		    if (count($objcom->array)>0)
		      foreach((array) $objcom->array AS $k => $objc)
			$amountc += $objc->amount;
		    print '<td align="right">';
		    print $gest;
		    print '</td>';
		    print '<td align="right">';
		    print price($amountc);
		    print '</td>';

		    if (empty($action) && $objpcontrat->statut == 1)
		      {
			print '<td align="right">';
			//modify
			if ($user->rights->poa->comp->mod && $gest == $gestion)
			  {
			    //para la edicion del comprometido
			    print '<a href="fiche_pas1.php?id='.$id.'&amp;idrc='.$objpcontrat->id.'&dol_hide_leftmenu=1'.'&action=editc'.(isset($_GET['nopac'])?'&nopac=1&idp='.$fk_poaprev:'&idp='.$fk_poaprev).'">'.img_picto($langs->trans('Edit'),DOL_URL_ROOT.'/poa/img/edit.png','',1).'</a>';
			    print '&nbsp';
			    print '&nbsp';
			  }
			else
			  print '&nbsp;';
			//cancel
			$objdev->getlist($object->fk_poa_prev,$objpcontrat->id/*$idc*/);
			$lCancel = true;
			if (count($objdev->array) > 0)
			  $lCancel = false;
			if ($user->rights->poa->comp->nul && $lCancel)
			  print '<a href="fiche_pas1.php?id='.$id.'&amp;idrc='.$objpcontrat->id.'&idp='.$objprev->id.'&action=anulate'.(isset($_GET['nopac'])?'&nopac=1&idp='.$_GET['idp']:'').'">'.img_picto($langs->trans('Cancel'),DOL_URL_ROOT.'/poa/img/cancel.png','',1).'</a>';
			else
			  print '&nbsp;';
			print '&nbsp';
			print '&nbsp';
			if ($lOrderproceed)
			  {
			    $objdev->getlist($object->fk_poa_prev,$objpcontrat->id/*$idc*/);
			    $nLen = count($objdev->array);

			    if (!empty($objpcontrat->date_order_proceed) && !is_null($objpcontrat->date_order_proceed))
			      {
				if ($user->admin || $user->id == $objprev->fk_user_create)
				  print '<a href="fiche_pas2.php?id='.$id.'&idrc='.$objpcontrat->id.'&idc='.$objpcontrat->fk_contrat.'&action=create'.(isset($_GET['nopac'])?'&nopac=1&idp='.$_GET['idp']:'').'">'.img_picto($langs->trans('Payment'),DOL_URL_ROOT.'/poa/img/payment.png','',1).'</a>';
				else
				  print '&nbsp;';
			      }
			  }
			else
			  {
			    if ($user->admin || $user->id == $objprev->fk_user_create)
			      print '<a href="fiche_pas2.php?id='.$id.'&idrc='.$objpcontrat->id.'&idc='.$objpcontrat->fk_contrat.'&action=create'.(isset($_GET['nopac'])?'&nopac=1&idp='.$_GET['idp']:'').'">'.img_picto($langs->trans('Payment'),DOL_URL_ROOT.'/poa/img/payment.png','',1).'</a>';
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
		    $nLoop++;
		  }
	      }

	  }
	print '</table>';
      }
  }

llxFooter();

$db->close();
?>
