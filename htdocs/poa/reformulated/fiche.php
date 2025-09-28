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
 *	\file       htdocs/poa/reformulated/fiche.php
 *	\ingroup    Reformulated
 *	\brief      Page fiche Poa reformulated
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/reformulated/class/poareformulated.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/reformulated/class/poareformulateddet.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/reformulated/class/poareformulatedof.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/reformulated/class/poareformulatedto.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/poa/class/poapoa.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/structure/class/poastructure.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaareauser.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("poa@poa");

$action=GETPOST('action');

$id        = GETPOST("id");
$idp       = GETPOST("idp");
$idof      = GETPOST("idof");
$idto      = GETPOST("idto");
$idpsearch = GETPOST("idpsearch");

$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$search    = GETPOST("search");
$mesg = '';

$object    = new Poareformulated($db);
$objectof  = new Poareformulatedof($db);
$objectto  = new Poareformulatedto($db);

$objectdet = new Poareformulateddet($db);
$objuser   = new User($db);
$objpoa    = new Poapoa($db);
$objarea   = new Poaarea($db);
$objstr    = new Poastructure($db);

$gestion   = date('Y');

$idFather = 0;
//areas a las que pertenece el usuario
if (!$user->admin)
  {
    $aArea = $objareauser->getuserarea($user->id);
    foreach((array) $aArea AS $idArea => $objAr)
      {
	//$idFather = $objarea->getfatherarea($idArea);
	$idFather = $idArea;
      }
    
  }
/*

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->poa->refo->crear)
  {
    $error = 0;
    $object->date_reform = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
    $object->ref       = $_POST["ref"];
    $object->gestion     = GETPOST('gestion');
    $object->fk_area   = GETPOST('fk_area');
    //buscamos la version en curso
    $object->fetch_version($object->gestion);
    if (count($object->aVersion) > 0)
      {
	$lVersion = true;
	foreach($object->aVersion AS $j => $objVer)
	  {
	    $nVersion = $j;
	  }
      }
    if (empty($nVersion))
      $nVersion = 1;

    $object->entity = $conf->entity;
    $object->version = $nVersion;
    $object->date_create = date('Y-m-d');
    $object->fk_user_create = $user->id;
    $object->tms = date('YmdHis');
    $object->statut  = 0;
    $object->active = 1;
    if (empty($object->ref))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorrefrequired").'</div>';
      }
    if (empty($object->gestion))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorgestionrequired").'</div>';
      }

    if (empty($error)) 
      {
	$id = $object->create($user);
	if ($id > 0)
	  {
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

// update
if ($action == 'update' && $user->rights->poa->refo->crear)
  {
    $error = 0;
    if ($object->fetch($_REQUEST['id']))
      {
	$object->date_reform = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$object->ref       = $_POST["ref"];
	$object->gestion     = GETPOST('gestion');
	$object->fk_area   = GETPOST('fk_area');
	//buscamos la version en curso
	$object->fetch_version($object->gestion);
	if (count($object->aVersion) > 0)
	  {
	    $lVersion = true;
	    foreach($object->aVersion AS $j => $objVer)
	      {
		$nVersion = $j;
	      }
	  }
	if (empty($nVersion))
	  $nVersion = 1;
	
	$object->version = $nVersion;
	$object->tms = date('YmdHis');
	$object->active = 1;
	if (empty($object->ref))
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("Errorrefrequired").'</div>';
	  }
	if (empty($object->gestion))
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("Errorgestionrequired").'</div>';
	  }
	
	if (empty($error)) 
	  {
	    $result = $object->update($user);
	    if ($result > 0)
	      {
		header("Location: fiche.php?id=".$id);
		exit;
	      }
	    $action = 'edit';
	    $mesg='<div class="error">'.$object->error.'</div>';
	  }
	else
	  {
	    if ($error)
	      $action="edit";   // Force retour sur page creation
	  }
      }
  }

// Add
if ($action == 'addpoaref' && $user->rights->poa->refo->crear)
  {
    $error = 0;

    $objectdet->fk_poa_reformulated = $id;
    $objectdet->fk_poa_poa = $idp;
    $objectdet->fk_poa_poa_des = GETPOST('fk_poa_poa_des');
    $objectdet->amount         = GETPOST("amount");
    //buscamos el poa
    $objpoa->fetch($objectdet->fk_poa_poa_des);
    if ($objpoa->id == $objectdet->fk_poa_poa_des)
      $objectdet->partida = $objpoa->partida;
    $objectdet->date_create = date('Y-m-d');
    $objectdet->fk_user_create = $user->id;
    $objectdet->tms = date('YmdHis');
    $objectdet->statut = 1;
    if (empty($objectdet->amount))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Erroramountisrequired").'</div>';
      }
    if (empty($object->fk_poa_poa_des))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorpoaisrequired").'</div>';
      }

    if (empty($error)) 
      {
	$iddet = $objectdet->create($user);
	if ($iddet > 0)
	  {
	    header("Location: fiche.php?id=".$id.'&idp='.$idp.'&action=sel');
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

// Delete charge
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->area->del)
{
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/poa/area/liste.php');
      exit;
    }
  else
    {
      $mesg='<div class="error">'.$object->error.'</div>';
      $action='';
    }
 }

// Delete charge
if ($action == 'confirm_delete_to' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->refo->del)
{
  $objectto->fetch($_REQUEST["idto"]);
  $result=$objectto->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/poa/reformulated/fiche.php?id='.$id.'&idof='.$idof.'&action=selpoato');
      exit;
    }
  else
    {
      $mesg='<div class="error">'.$object->error.'</div>';
      $action='selpoato';
    }
 }


// Addpoaof
if ($action == 'addpoaof' && $user->rights->poa->refo->crear)
  {
    $error = 0;

    $objectof->fk_poa_reformulated = $id;
    $objectof->fk_poa_poa   = GETPOST('fk_poa_poa');
    $objectof->fk_structure = GETPOST('fk_structure');
    $objectof->partida     = GETPOST('partida');
    $objectof->amount      = GETPOST("amount");
    $objectof->date_create = date('Y-m-d');
    $objectof->fk_user_create = $user->id;
    $objectof->tms = date('YmdHis');
    $objectof->statut = 0;
    if (empty($objectof->amount))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Erroramountisrequired").'</div>';
      }
    if (empty($objectof->fk_structure))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorstructureisrequired").'</div>';
      }

    if (empty($error)) 
      {
	$idof = $objectof->create($user);
	if ($idof > 0)
	  {
	    header("Location: fiche.php?id=".$id.'&idp='.$idp.'&idof='.$idof.'&action=selpoato');
	    exit;
	  }
	$action = 'selpoaof';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else
      {
	if ($error)
	  $action="selpoaof";   // Force retour sur page creation
      }
  }

// Addpoato
if ($action == 'addpoato' && $user->rights->poa->refo->crear)
  {
    $error = 0;

    $objectto->fk_poa_reformulated_of = $idof;
    $objectto->fk_poa_poa   = GETPOST('fk_poa_poa');
    $objectto->fk_structure = GETPOST('fk_structure');
    $objectto->partida     = GETPOST('partida');
    $objectto->amount      = GETPOST("amount");
    $objectto->date_create = date('Y-m-d');
    $objectto->fk_user_create = $user->id;
    $objectto->tms = date('YmdHis');
    $objectto->statut = 0;
    if (empty($objectto->amount))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Erroramountisrequired").'</div>';
      }
    if (empty($objectto->fk_structure))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorstructureisrequired").'</div>';
      }

    if (empty($error)) 
      {
	$idto = $objectto->create($user);
	if ($idto > 0)
	  {
	    header("Location: fiche.php?id=".$id.'&idp='.$idp.'&idof='.$idof.'&action=selpoato');
	    exit;
	  }
	$action = 'selpoato';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else
      {
	if ($error)
	  $action="selpoato";   // Force retour sur page creation
      }
  }

// Delete charge
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->area->del)
{
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/poa/area/liste.php');
      exit;
    }
  else
    {
      $mesg='<div class="error">'.$object->error.'</div>';
      $action='';
    }
 }

if ( ($action == 'createeditof') )
  {
    require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
    //$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
    $tmparray['fk_poa_poa'] = GETPOST('fk_poa_poa');
    if (! empty($tmparray['fk_poa_poa']))
      {
	$objectof->fk_poa_poa = $tmparray['fk_poa_poa'];
	$action='selpoaof';
	$_GET['id'] = $_POST['id'];
      }
  }

if ($_POST["cancel"] == $langs->trans("Cancel"))
  {
    $action = '';
    $_GET["id"] = $_POST["id"];
  }
if ($action == 'searchpoa')
  {
    $action = 'selpoaof';
    $idp = $idpsearch;
    $_GET["id"] = $_POST["id"];
  }
if ($action == 'searchpoato')
  {
    $action = 'selpoato';
    $idpto = $idpsearch;
    $_GET["id"] = $_POST["id"];
  }



/*
 * View
 */

$form=new Form($db);

$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
llxHeader("",$langs->trans("Reformulated"),$help_url,'','','',$aArrjs,$aArrcss); 

// $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
// llxHeader("",$langs->trans("POA"),$help_url);

if ($action == 'create' && $user->rights->poa->refo->crear)
  {
    print_fiche_titre($langs->trans("New reformulated"));
  
    print "<form action=\"fiche.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
    
    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    // ref
    print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
    print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="5" maxlength="3">';
    print '</td></tr>';
   
    //gestion
    print '<tr><td class="fieldrequired">'.$langs->trans('Gestion').'</td><td colspan="2">';
    print '<input id="gestion" type="text" value="'.(empty($object->gestion)?date('Y'):$object->gestion).'" name="gestion" size="3" maxlength="4">';
    print '</td></tr>';

    // area
    print '<tr><td class="fieldrequired">'.$langs->trans('Area').'</td><td colspan="2">';
    if (!$user->admin)
      {
	$objarea->fetch($idFather);
	print $objarea->label;
	print '<input type="hidden" name="fk_area" value="'.$idFather.'"';
      }
    else
      print $objarea->select_area((empty($object->fk_area)?$idFather:$object->fk_area),'fk_area','',120,1);
    print '</td></tr>';
    
    //date
    print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="2">';
    print $form->select_date($object->date_reform,'di_','','','','date',1,1);
    print '</td></tr>';

    print '</table>';

    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

    print '</form>';
  }
 else
   {
     if ($_GET["id"])
       {
      
	 $result = $object->fetch($_GET["id"]);
	 if ($result < 0)
	   {
	     dol_print_error($db);
	   }
	 
	 
	 /*
	  * Affichage fiche
	  */
	 if ($action <> 'edit' && $action <> 're-edit')
	   {
	     //$head = fabrication_prepare_head($object);
	     
	     dol_fiche_head($head, 'card', $langs->trans("Reformulated"), 0, 'poa');
	     
	     /*
	      * Confirmation de la validation
	      */
	     if ($action == 'validate')
	       {
		 $error = 0;
		 $object->fetch(GETPOST('id'));
		 //cambiando a validado
		 $object->statut = 1;
		 //procesamos registro a reformulatedet
		 $aReform = array();
		 $aReform[$id] = $id;
		 list($aOf,$aTo,$aOfone,$aToone) = $objectof->get_sumaref($aReform);
		 //recorremos aOf
		 $db->begin();
		 foreach ((array) $aOf AS $fk_structure => $aPoa)
		   {
		     foreach ((array) $aPoa AS $fk_poa_poa => $aPart)
		       {
			 foreach ((array) $aPart AS $fk_partida => $value)
			   {
			     $objectdet->initAsSpecimen();
			     $objectdet->fk_poa_reformulated = $id;
			     $objectdet->fk_structure = $fk_structure;
			     $objectdet->fk_poa_poa = $fk_poa_poa;
			     $objectdet->partida = $fk_partida;
			     $objectdet->amount = $value;
			     $objectdet->reform = $object->ref;
			     $objectdet->date_create = dol_now();
			     $objectdet->fk_user_create = $user->id;
			     $objectdet->tms = dol_now();
			     $objectdet->statut = 1;
			     $res = $objectdet->create($user);
			     if ($res <= 0)
			       $error++;
			   }
		    }
		   }
		 foreach ((array) $aTo AS $fk_structure => $aPoa)
		   {
		     foreach ((array) $aPoa AS $fk_poa_poa => $aPart)
		       {
			 foreach ((array) $aPart AS $fk_partida => $value)
			   {
			     $objectdet->initAsSpecimen();
			     $objectdet->fk_poa_reformulated = $id;
			     $objectdet->fk_structure = $fk_structure;
			     $objectdet->fk_poa_poa = $fk_poa_poa;
			     $objectdet->partida = $fk_partida;
			     $objectdet->amount = $value * -1;
			     $objectdet->reform = $object->ref;
			     $objectdet->date_create = dol_now();
			     $objectdet->fk_user_create = $user->id;
			     $objectdet->tms = dol_now();
			     $objectdet->statut = 1;
			     $res = $objectdet->create($user);
			     if ($res <= 0)
			       $error++;
			   }
		       }
		   }
		 
		 //$object->ref = $object->codref;
		 //update
		 $res = $object->update($user);
		 if ($res <=0)
		   $error++;
		 if (empty($error))
		   $db->commit();
		 else
		   {
		     $db->rollback();
		     $mesg ='<div class="error">'.$langs->trans("Errorsencounteredwhileprocessingthevalidation").'</div>';
		   }
		 $action = '';
		 //header("Location: fiche.php?id=".$_GET['id']);
		 
	       }
	  
	     // Confirm delete third party
	     if ($action == 'delete')
	       {
		 $form = new Form($db);
		 $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deletecharge"),$langs->trans("Confirmdeletecharge",$object->ref.' '.$object->detail),"confirm_delete",'',0,2);
		 if ($ret == 'html') print '<br>';
	       }
	     
	     // Confirm delete third party
	     if ($action == 'delto')
	       {
		 $objectto->fetch($idto);
		 
		 $form = new Form($db);
		 $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idof='.$idof.'&idto='.$idto,$langs->trans("DeleteReformulatedto"),$langs->trans("Confirmdeletereformulatedto",$objectto->partida.' '.$objectto->amount),"confirm_delete_to",'',0,2);
		 if ($ret == 'html') print '<br>';
	       }
	     dol_htmloutput_mesg($mesg);
	     
	     print '<table class="border" width="100%">';
	     
	     
	     // ref
	     print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';
	     $linkback = '<a href="'.DOL_URL_ROOT.'/poa/reformulated/liste.php&dol_hide_leftmenu=1">'.$langs->trans("BackToList").'</a>';
	     
	     print '<td class="valeur"  colspan="2">';
	     print $form->showrefnav($object, 'id', $linkback);
	     print '</td></tr>';
	     
	     //gestion
	     print '<tr><td>'.$langs->trans('Gestion').'</td><td colspan="2">';
	     print $object->gestion;
	     print '</td></tr>';
	     
	     //area
	     $objarea->fetch($object->fk_area);
	     print '<tr><td>'.$langs->trans('Area').'</td><td colspan="2">';
	     if ($objarea->id == $object->fk_area)
	       print $objarea->label;
	     else
	       print '&nbsp;';
	     print '</td></tr>';
	     
	     //date
	     print '<tr><td>'.$langs->trans('Date').'</td><td colspan="2">';
	     print dol_print_date($object->date_reform,'day');
	     print '</td></tr>';
	     
	     //statut
	     print '<tr><td>'.$langs->trans('Status').'</td><td colspan="2">';
	     print $object->LibStatut($object->statut,0);
	     print '</td></tr>';
	     
	     print "</table>";
	     
	     print '</div>';
	     
	  
	  /* ************************************************************************** */
	  /*                                                                            */
	  /* Barre d'action                                                             */
	  /*                                                                            */
	  /* ************************************************************************** */
	  
	     print "<div class=\"tabsAction\">\n";
	     
	     if ($action == '')
	       {
		 if ($user->rights->poa->refo->crear)
		   print "<a class=\"butAction\" href=\"fiche.php?action=create&dol_hide_leftmenu=1\">".$langs->trans("Createnew")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
		 
		 if ($user->rights->poa->refo->crear)
		   print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."&dol_hide_leftmenu=1\">".$langs->trans("Modify")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

		 if ($user->rights->poa->refo->val && $object->statut == 0)
		   print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."&dol_hide_leftmenu=1\">".$langs->trans("Validate")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";
		 
		 if ($user->rights->poa->refo->del && $object->statut == 0)
		   print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."&dol_hide_leftmenu=1\">".$langs->trans("Delete")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	       }	  
	     print "</div>";		
	     
	     $sumaOf = 0;
	     $sumaTo = 0;
	     //seleccion de origenes OF
	     $objectof->getlist($object->id,$idof);
	     
	     print_barre_liste($langs->trans("Of"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
	     
	     print '<table class="noborder" width="100%">';
	     
	     print "<tr class=\"liste_titre\">";
	     print_liste_field_titre($langs->trans("Structure"),"", "","","","");
	     print_liste_field_titre($langs->trans("Name"),"", "","","","");
	     print_liste_field_titre($langs->trans("Partida"),"", "","","","");
	     print_liste_field_titre($langs->trans("Add"),"", "","","","");
	     print_liste_field_titre($langs->trans("Subtract"),"", "","","","");
	     print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
	     print "</tr>\n";
	     if ($action == 'selpoaof')
	       {
		 $searchpoa = 'searchpoa';
		 $selpoades = 'selpoaof';
		 include_once DOL_DOCUMENT_ROOT.'/poa/reformulated/tpl/search.tpl.php';
		 if ($idpsearch)
		   $idp = $idpsearch;
		 if ($idp)
		   {
		     $objpoa->fetch($idp);
		     //buscador de poa
		     print '<form name="form_of" action="fiche.php" method="POST">';
		     print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		     print '<input type="hidden" name="action" value="addpoaof">';
		     print '<input type="hidden" name="id" value="'.$object->id.'">';
		     print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
		     
		     print '<tr>';
		     // // search
		     // print '<td>';
		     // print '<input id="partida" type="text" value="'.(empty($objectof->partida)?$objpoasearch->partida:$objectof->partida).'" name="partida" size="12" maxlength="10">';
		     // print '</td>';
		     
		     
		  // structure
		     print '<td width="120">';
		     $objstr->fetch($objpoa->fk_structure);
		     print $objstr->sigla;
		     print '<input id="fk_structure" type="hidden" value="'.$objpoa->fk_structure.'" name="fk_structure">';
		     print '</td>';
		     //poa
		     print '<td>';
		     
		     print $objpoa->label;
		     print '<input id="fk_poa_poa" type="hidden" value="'.$objpoa->id.'" name="fk_poa_poa">';
		     print '</td>';
		     
		     // partida
		     //	      $objpoasearch->get_partida($objpoasearch->fk_structure,$object->gestion);
		     print '<td>';
		     //	      print $form->selectarray('partida',$objpoasearch->array,$objpoasearch->partida);
		     print $objpoa->partida;
		     print '<input id="partida" type="hidden" value="'.$objpoa->partida.'" name="partida">';
		     print '</td>';
		     
		     // amount
		     print '<td>';
		     print '<input id="amount" type="text" value="'.(empty($objectof->amount)?$objpoa->amount:$objectof->amount).'" name="amount" size="12" maxlength="12">';
		     print '</td>';
		     
		     // amount
		     print '<td>';
		     print '&nbsp;';
		     print '</td>';
		     
		     print '<td align="right">';
		     print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/poa/img/save.png" width="14" height="14">';
		     print '</td>';
		     print '</tr>';
		     
		     print '</form>';
		   }
		 
	       }
	     if (count($objectof->array) > 0)
	       {
		 foreach ((array) $objectof->array AS $j => $objof)
		   {
		     if ($action == 'selpoato' && $idof == $objof->id)
		       print '<tr class="rowsel">';
		     else
		       print '<tr>';
		     //structure
		     print '<td>';
		     $objstr->fetch($objof->fk_structure);
		     if ($objstr->id == $objof->fk_structure)
		       print $objstr->sigla;
		     else
		       print '&nbsp;';
		     print '</td>';
		     
		     //nome
		     print '<td>';
		     $objpoa->fetch($objof->fk_poa_poa);
		     if ($objpoa->id == $objof->fk_poa_poa)
		       print $objpoa->label;
		     else
		       print '&nbsp;';
		     print '</td>';
		     
		     //partida
		     print '<td>';
		     print $objof->partida;
		     print '</td>';
		     //amount of
		     print '<td nowrap align="right">';
		     print price($objof->amount);
		     print '</td>';
		     $sumaOf+= $objof->amount;
		     //amount to
		     print '<td nowrap align="right">';
		     print '&nbsp;';
		     print '</td>';
		     
		     print '<td align="right">';
		     print '<a href="'.DOL_URL_ROOT.'/poa/reformulated/fiche.php?id='.$id.'&idof='.$objof->id.'&action=selpoato'.'">'.img_picto($langs->trans('Edit'),'edit').'</a>';		  
		     print '</td></tr>';
		     
		     if ($action == 'selpoato' && $idof == $objof->id)
		       {
			 //searchto
			 $searchpoa = 'searchpoato';
			 $selpoades = 'selpoato';
			 
			 include_once DOL_DOCUMENT_ROOT.'/poa/reformulated/tpl/search.tpl.php';
			 if ($idpsearch)
			   $idpto = $idpsearch;
			 
			 if ($idpto)
			   {
			     $objpoa->fetch($idpto);
			     //buscador de poa
			     print '<form name="form_of" action="fiche.php" method="POST">';
			     print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			     print '<input type="hidden" name="action" value="addpoato">';
			     print '<input type="hidden" name="id" value="'.$object->id.'">';
			     print '<input type="hidden" name="idof" value="'.$idof.'">';
			     print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
			     
			     print '<tr>';
			     // structure
			     print '<td width="120">';
			     $objstr->fetch($objpoa->fk_structure);
			     print $objstr->sigla;
			     print '<input id="fk_structure" type="hidden" value="'.$objpoa->fk_structure.'" name="fk_structure">';
			     
			     print '</td>';
			     //poa
			     print '<td>';
			     
			     print $objpoa->label;
			     print '<input id="fk_poa_poa" type="hidden" value="'.$objpoa->id.'" name="fk_poa_poa">';
			     print '</td>';
			     
			     // partida
			     //	      $objpoasearch->get_partida($objpoasearch->fk_structure,$object->gestion);
			     print '<td>';
			     //	      print $form->selectarray('partida',$objpoasearch->array,$objpoasearch->partida);
			     print $objpoa->partida;
			     print '<input id="partida" type="hidden" value="'.$objpoa->partida.'" name="partida">';
			     print '</td>';
			     // amountof
			     print '<td>';
			     print '&nbsp;';
			     print '</td>';
			     
			     // amountto
			     print '<td>';
			     print '<input id="amount" type="text" value="'.(empty($objectto->amount)?$objpoa->amount:$objectto->amount).'" name="amount" size="12" maxlength="12">';
			     print '</td>';
			     
			     
			     print '<td align="right">';
			     print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/poa/img/save.png" width="14" height="14">';
			     print '</td>';
			     print '</tr>';
			     
			     print '</form>';
			     
			   }
			 // //registro nuevo para TO
			 // print '<form action="fiche.php" method="POST">';
			 // print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			 // print '<input type="hidden" name="action" value="addto">';
			 // print '<input type="hidden" name="id" value="'.$object->id.'">';
			 // print '<input type="hidden" name="idp" value="'.$idp.'">';

		      // print '<tr>';
		      // //poa
		      // print '<td colspan="3">';
		      // print $objpoa->select_poa($objectto->fk_poa_poa_des,'fk_poa_poa_des','',120,1);
		      // print '</td>';
		      
		      // //amount of
		      // print '<td>';
		      // print '&nbsp;';;
		      // print '</td>';
		      // //amount to
		      // print '<td align="right">';
		      // print '<input type="number" id="amount" name="amount" value="'.$objto->amount.'" size="8" maxlegenth="12">';
		      // print '</td>';
		      
		      // print '<td align="right">';
		      // print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/poa/img/save.png" width="14" height="14">';		      
		      // print '</td></tr>';
		      // print '</form>';
		      
			 $objectto->getlist($objof->id);
			 foreach((array) $objectto->array AS $k => $objto)
			   {
			     //structure
			     print '<td>';
			     $objstr->fetch($objto->fk_structure);
			     if ($objstr->id == $objto->fk_structure)
			       print $objstr->sigla;
			     else
			       print '&nbsp;';
			     print '</td>';
			     
			     //nome
			     print '<td>';
			     $objpoa->fetch($objto->fk_poa_poa);
			     if ($objpoa->id == $objto->fk_poa_poa)
			       print $objpoa->label;
			     else
			       print '&nbsp;';
			     print '</td>';
			     //partida
			     print '<td>';
			     if ($objpoa->id == $objto->fk_poa_poa)
			       print $objpoa->partida;
			     else
			       print '&nbsp;';
			     print '</td>';
			     
			     //amount of
			     print '<td nowrap align="right">';
			     print '&nbsp;';
			     print '</td>';
			     
			     //amount to
			     print '<td nowrap align="right">';
			     print price($objto->amount);
			     print '</td>';
			     $sumaTo+= $objto->amount;
			     
			     print '<td align="right">';
			     print '<a href="'.DOL_URL_ROOT.'/poa/reformulated/fiche.php?id='.$id.'&idof='.$idof.'&idto='.$objto->id.'&action=delto'.'">'.img_picto($langs->trans('Delete'),'delete').'</a>';		  
			     print '</td></tr>';
			     
			   }
		       }
		   }
		 //totales
		 print '<tr class="liste_total"><td colspan="3">'.$langs->trans("Total").'</td>';
		 print '<td align="right">';
		 print price(price2num($sumaOf,'MT'));
		 print '</td>';
		 print '<td align="right">';
		 print price(price2num($sumaTo,'MT'));
		 print '</td>';
		 print '<td align="right">';
		 print '&nbsp;';
		 print '</td>';
		 print '</tr>';
		 
		 print '</table>';
		 
		 if ($action == 'createpoa')
		   {
		     //registro nuevo de poa
		     print '<form action="fiche.php" method="POST">';
		     print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		     print '<input type="hidden" name="action" value="addpoa">';
		     print '<input type="hidden" name="id" value="'.$object->id.'">';
		     print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
		     print '<tr>';
		     
		     // structure
		     print '<td width="120">';
		     print $objstr->select_structure($objpoa->fk_structure,'fk_structure','',3,1,3);
		     print '</td>';
		     
		     //label
		     print '<td>';
		     print '<input id="label" type="text" value="'.$objpoa->label.'" name="label" size="40" maxlength="255">';
		     print '</td>';
		     // pseudonym
		     print '<td>';
		     print '<input id="pseudonym" type="text" value="'.$objpoa->pseudonym.'" name="pseudonym" size="40" maxlength="255">';
		     print '</td>';
		     // structure mod
		     print '<td>';
		     print '&nbsp;';
		     print '</td>';
		     
		     //partida
		     print '<td>';
		     print '<input id="partida" type="text" value="'.$objprev->partida.'" name="partida" size="6" maxlength="10">';
		     print '</td>';		      
		     //amount
		     print '<td align="right">';
		     print '<input type="number" id="amount" name="amount" value="'.$objpoarefdet->amount.'" size="8" maxlegenth="12">';
		     print '</td>';
		     
		     print '<td align="right">';
		     print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/poa/img/save.png" width="14" height="14">';
		     
		     print '</td></tr>';
		     
		     print '</form>';
		   }
		 
		 /* ************************************************************************** */
	  /*                                                                            */
	  /* Barre d'action reformulation of to                                         */
	  /*                                                                            */
	  /* ************************************************************************** */
	  
		 print "<div class=\"tabsAction\">\n";
		 
		 if ($action == 'selpoato' || $action == 'selpoaof')
		   {
		     print '<a class="butAction" href="fiche.php?id='.$id.'&dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
		   }	  
		 print "</div>";		
		 

	      // print '<br>';
	      // //seleccion de poa para traspaso
	      // print '<form action="fiche.php" method="POST">';
	      // print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	      // print '<input type="hidden" name="action" value="addpoa">';
	      // print '<input type="hidden" name="id" value="'.$object->id.'">';
	      // print '<tr>';
	      
	      // // structure
	      // print '<td width="120">';
	      // print $objstr->select_structure($objpoa->fk_structure,'fk_structure','',3,1,3);
	      // print '</td>';
	      
	      // //label
	      // print '<td>';
	      // print '<input id="label" type="text" value="'.$objpoa->label.'" name="label" size="40" maxlength="255">';
	      // print '</td>';
	      // // pseudonym
	      // print '<td>';
	      // print '<input id="pseudonym" type="text" value="'.$objpoa->pseudonym.'" name="pseudonym" size="40" maxlength="255">';
	      // print '</td>';
	      // // structure mod
	      // print '<td>';
	      // print '&nbsp;';
	      // print '</td>';

	      // //partida
	      // print '<td>';
	      // print '<input id="partida" type="text" value="'.$objprev->partida.'" name="partida" size="6" maxlength="10">';
	      // print '</td>';		      
	      // //amount
	      // print '<td align="right">';
	      // print '<input type="number" id="amount" name="amount" value="'.$objpoarefdet->amount.'" size="8" maxlegenth="12">';
	      // print '</td>';
	      
	      // print '<td align="right">';
	      // print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/poa/img/save.png" width="14" height="14">';
	      
	      // print '</td></tr>';
	      
	      // print '</form>';
	      
	    }

	  /* ************************************************************************** */
	  /*                                                                            */
	  /* Barre d'action reformulation of to                                         */
	  /*                                                                            */
	  /* ************************************************************************** */
	  
	     print "<div class=\"tabsAction\">\n";
	     
	     if ($action == '')
	       {
		 if ($user->rights->poa->refo->crear)
		   print '<a class="butAction" href="fiche.php?id='.$id.'&action=selpoaof&dol_hide_leftmenu=1">'.$langs->trans("Selectpoa").'</a>';
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Selectpoa")."</a>";
		 
		 if ($user->rights->poa->refo->crear)
		   print "<a class=\"butAction\" href=\"fiche.php?action=createpoa&id=".$object->id."&dol_hide_leftmenu=1\">".$langs->trans("Createpoa")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createpoa")."</a>";
		 
		 if ($user->rights->poa->refo->del && $object->statut == 0)
		   print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."&dol_hide_leftmenu=1\">".$langs->trans("Delete")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	       }	  
	     print "</div>";		
	     
	     // //registro de reformulados origenes
	  // $objectdet->getlist($object->id);
	  // //encabezado
	  // print_barre_liste($langs->trans("Description"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
	  
	  // print '<table class="noborder" width="100%">';
	  
	  // print "<tr class=\"liste_titre\">";
	  // print_liste_field_titre($langs->trans("Structure"),"", "","","","");
	  // print_liste_field_titre($langs->trans("Actividated"),"", "","","","");
	  // print_liste_field_titre($langs->trans("Partida"),"", "","","","");
	  // print_liste_field_titre($langs->trans("Amount"),"", "","","","");

	  // print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
	  // print "</tr>\n";
	  
	  // print '<form action="fiche.php" method="POST">';
	  // print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	  // print '<input type="hidden" name="action" value="adduser">';
	  // print '<input type="hidden" name="id" value="'.$object->id.'">';
	  
          //   // On selectionne les users qui ne sont pas deja dans le groupe
          //   $exclude = array();

          //   // if (! empty($object->members))
          //   // {
          //   //     if (! (! empty($conf->multicompany->enabled) && ! empty($conf->multicompany->transverse_mode)))
          //   //     {
          //   //         foreach($object->members as $useringroup)
          //   //         {
          //   //             $exclude[]=$useringroup->id;
          //   //         }
          //   //     }
          //   // }

	  // // user
	  // print '<td>';
	  // print $form->select_dolusers('','fk_user',1,$exclude,0,'','',$object->entity);
	  // print '</td>';
	  
	  // //label
	  // print '<td>';
	  // print '&nbsp;';
	  // print '</td>';
	  // print '<td align="right">';
	  // print '<input type="submit" class="button" value="'.$langs->trans("Save").'">';

	  // print '</td></tr>';
	  // print '</form>';
	  // if (count($objuser->array) > 0)
	  //   {
	  //     foreach((array) $objuser->array AS $j => $objus)
	  // 	{
	  // 	  $obuser->fetch($objus->fk_user);
	  // 	  print '<tr>';
	  // 	  print '<td>';
	  // 	  print $obuser->lastname.' '.$obuser->firstname;
	  // 	  print '</td>';
		  
	  // 	  //active
	  // 	  print '<td>';
	  // 	  print $objuser->LibStatut($objus->active,2,0);
	  // 	  print '</td>';
	  // 	  print '<td align="right">';
	  // 	  print '<a href="'.DOL_URL_ROOT.'/poa/area/fiche.php?id='.$id.'&idus='.$objus->id.'&action=deluser'.'">'.img_picto($langs->trans('Delete'),'delete').'</a>';
		  
	  // 	  print '</td></tr>';
		  
	  // 	}
	  //   }
	  // print '</table>';
	  //fin registro usuarios
	   }
      /*
       * Edition fiche
       */
	 if (($action == 'edit' || $action == 're-edit') && 1)
	   {
	     print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);
	     
	     print '<form action="fiche.php" method="POST">';
	     print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	     print '<input type="hidden" name="action" value="update">';
	     print '<input type="hidden" name="id" value="'.$object->id.'">';
	     print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
	     
	     print '<table class="border" width="100%">';
	     
	     // ref
	     print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	     print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="5" maxlength="3">';
	     print '</td></tr>';
	     
	     //gestion
	     print '<tr><td class="fieldrequired">'.$langs->trans('Gestion').'</td><td colspan="2">';
	     print '<input id="gestion" type="text" value="'.(empty($object->gestion)?date('Y'):$object->gestion).'" name="gestion" size="3" maxlength="4">';
	     print '</td></tr>';
	     
	     // area
	     print '<tr><td class="fieldrequired">'.$langs->trans('Area').'</td><td colspan="2">';
	     if (!$user->admin)
	       {
		 $objarea->fetch($idFather);
		 print $objarea->label;
		 print '<input type="hidden" name="fk_area" value="'.$idFather.'"';
	       }
	     else
	       print $objarea->select_area((empty($object->fk_area)?$idFather:$object->fk_area),'fk_area','',120,1);
	     print '</td></tr>';
	     
	     //date
	     print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="2">';
	     print $form->select_date($object->date_reform,'di_','','','','date',1,1);
	     print '</td></tr>';
	     
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
