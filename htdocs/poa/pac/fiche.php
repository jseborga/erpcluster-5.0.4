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
 *	\file       htdocs/poa/pac/fiche.php
 *	\ingroup    Programa Anual de Contrataciones
 *	\brief      Page fiche mant PAC
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/poa/class/poapoa.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/pac/class/poapac.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("poa@poa");

$action=GETPOST('action');

// //require_once DOL_DOCUMENT_ROOT.'/poa/class/poapacemail.class.php';
// // $objaaa = new Poapacemail($db);
// // $objaaa->list_pac_email($gestion='');
// require_once DOL_DOCUMENT_ROOT.'/poa/class/poapoaemail.class.php';
// $objaaa = new Poapoaemail($db);
// $objaaa->list_poa_email($gestion='');

// // require_once DOL_DOCUMENT_ROOT.'/poa/class/poapreemail.class.php';

// // $objaaa = new Poapreemail($db);
// // $objaaa->list_pre_email($gestion='');
// exit;

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';

$object = new Poapac($db);
$objpoa = new Poapoa($db);
$objuser = new User($db);
$gestion = $_SESSION['gestion'];

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->poa->pac->crear)
  {
    $error = 0;
    $object->entity = $conf->entity;
    $object->fk_poa      = GETPOST('fk_poa');
    $object->gestion     = GETPOST('gestion');
    $object->fk_type_modality = GETPOST('fk_type_modality');
    $object->fk_type_object   = GETPOST('fk_type_object');

    $object->ref          = $_POST["ref"];
    $object->nom          = GETPOST('nom');
    $object->fk_financer  = GETPOST('fk_financer');
    $object->month_init   = GETPOST('month_init');
    $object->month_public = GETPOST('month_public');
    $object->amount       = GETPOST('amount');
    $object->fk_user_resp = GETPOST('fk_user_resp');
    $object->tms          = dol_now();
    $object->statut = 0;
    //buscamos el poa para recuperar la partida
    $objpoa = new Poapoa($db);
    $objpoa->fetch($object->fk_poa);
    if ($objpoa->id == $object->fk_poa)
      {
	$object->partida = $objpoa->partida;
      }
    if (empty($object->ref))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorrefrequired").'</div>';
      }
    if (empty($object->nom))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errornomrequired").'</div>';
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

// adduser
if ($action == 'adduser' && $user->rights->poa->area->crear)
  {
    
    $error = 0;
    $objuser->fk_area = $_POST["id"];
    $objuser->fk_user = GETPOST('fk_user');
    $objuser->date_create = date('Y-m-d');
    $objuser->tms = date('YmdHis');
    $objuser->active  = 1;
    if (empty($objuser->fk_user))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Erroruserisrequired").'</div>';
      }

    if (empty($error)) 
      {
	if ($objuser->create($user))
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


// Delete charge
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->pac->del)
{
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/poa/pac/liste.php');
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
	$object->fk_poa      = GETPOST('fk_poa');
	$object->gestion     = GETPOST('gestion');
	$object->fk_type_modality = GETPOST('fk_type_modality');
	$object->fk_type_object   = GETPOST('fk_type_object');
	
	$object->ref          = $_POST["ref"];
	$object->nom          = GETPOST('nom');
	$object->fk_financer  = GETPOST('fk_financer');
	$object->month_init   = GETPOST('month_init');
	$object->month_public = GETPOST('month_public');
	$object->amount       = GETPOST('amount');
	$object->fk_user_resp = GETPOST('fk_user_resp');
	$object->tms = date('YmdHis');
	//buscamos el poa para recuperar la partida
	$objpoa = new Poapoa($db);
	$objpoa->fetch($object->fk_poa);
	if ($objpoa->id == $object->fk_poa)
	  {
	    $object->partida = $objpoa->partida;
	  }
	
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
	$mesg = '<div class="error">'.$object->error.'</div>';
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

$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js','poa/js/poa.js','poa/js/scriptajax.js');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
llxHeader("",$langs->trans("PAC"),$help_url,'','','',$aArrjs,$aArrcss);

if ($action == 'create' && $user->rights->poa->pac->crear)
  {
    print_fiche_titre($langs->trans("Newpac"));
  
    print "<form action=\"fiche.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
    
    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';


    //select poa
    print '<tr><td class="fieldrequired">'.$langs->trans('Poa').'</td><td colspan="2">';   
    print $objpoa->select_poa($object->fk_poa,'fk_poa','',0,1);
    print '</td></tr>';

    // gestion
    if (empty($object->gestion))
      $object->gestion = $_SESSION['gestion'];
    print '<tr><td class="fieldrequired">'.$langs->trans('Gestion').'</td><td colspan="2">';
    print '<input id="gestion" type="text" value="'.$object->gestion.'" name="gestion" size="6" maxlength="4">';
    print '</td></tr>';
   
    //type modality
    print '<tr><td class="fieldrequired">'.$langs->trans('Modality').'</td><td colspan="2">';   
    print select_tables($object->fk_type_modality,'fk_type_modality','',1,0,'05');
    print '</td></tr>';

    //type object
    print '<tr><td class="fieldrequired">'.$langs->trans('Object').'</td><td colspan="2">';   
    print select_tables($object->fk_type_object,'fk_type_object','',1,0,'06');
    print '</td></tr>';

    // ref
    print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
    print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="6" maxlength="3">';
    print '</td></tr>';

    // nom
    print '<tr><td class="fieldrequired">'.$langs->trans('Name').'</td><td colspan="2">';
    print '<input id="nom" type="text" value="'.$object->nom.'" name="nom" size="55" maxlength="255">';
    print '</td></tr>';

    //type financer
    print '<tr><td class="fieldrequired">'.$langs->trans('Financer').'</td><td colspan="2">';   
    print select_financer($object->fk_financer,'fk_financer','',1,0);
    print '</td></tr>';

    //month_init
    print '<tr><td class="fieldrequired">'.$langs->trans('Monthinit').'</td><td colspan="2">';   
    print select_month($object->month_init,'month_init','',15,1);
    print '</td></tr>';
    
    //month_public
    print '<tr><td>'.$langs->trans('Monthpublic').'</td><td colspan="2">';   
    print select_month($object->month_public,'month_public','',15,1);
    print '</td></tr>';

    // amount
    print '<tr><td class="fieldrequired">'.$langs->trans('Amount').'</td><td colspan="2">';
    print '<input id="amount" type="text" value="'.$object->amount.'" name="amount" size="30" maxlength="12">';
    print '</td></tr>';
    //respon
    print '<tr><td class="fieldrequired">'.$langs->trans('Responsible').'</td><td colspan="2">';
    $exclude = array();
    if (empty($object->entity)) $object->entity = $conf->entity;
    print $form->select_dolusers((empty($object->fk_user_resp)?$user->id:$object->fk_user_resp),'fk_user_resp',1,$exclude,0,'','',$object->entity);

    print '</table>';

    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

    print '</form>';
    print "<div class=\"tabsAction\">\n";
    print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/pac/liste.php?dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
    print '</div>';
  }
 else
   {
     if ($_GET["id"])
       {
      dol_htmloutput_mesg($mesg);
      
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
	  
	  dol_fiche_head($head, 'card', $langs->trans("PAC"), 0, 'mant');
	  
	  /*
	   * Confirmation de la validation
	   */
	  if ($action == 'validate')
	    {
	      $object->fetch(GETPOST('id'));
	      //cambiando a validado
	      $object->statut = 1;
	      //update
	      $object->update($user);
	      $action = '';
	      //header("Location: fiche.php?id=".$_GET['id']);
	      
	    }

	  /*
	   * Confirmation de la anulacion
	   */
	  if ($action == 'nullify')
	    {
	      $object->fetch(GETPOST('id'));
	      //cambiando a validado
	      $object->statut = 2;
	      //update
	      $object->update($user);
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
	  
	  print '<table class="border" width="100%">';

	  // ref numeracion automatica de la OT
	  print '<tr><td width="20%">'.$langs->trans('Id').'</td><td class="valeur" colspan="2">';
	  $linkback = '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php">'.$langs->trans("BackToList").'</a>';

	  print $form->showrefnav($object, 'id', $linkback,1,'rowid','rowid');
	  print '</td></tr>';

	  //select poa
	  print '<tr><td>'.$langs->trans('POA').'</td><td colspan="2">';   
	  $objpoa->fetch($object->fk_poa);
	  if ($object->fk_poa == $objpoa->id)
	    print $objpoa->label;
	  else
	    print $langs->trans('Notrequired');


	  print '</td></tr>';
	  //gestion
	  print '<tr><td>'.$langs->trans('Gestion').'</td><td colspan="2">';
	  print $object->gestion;
	  print '</td></tr>';
	  
	  //type modality
	  print '<tr><td>'.$langs->trans('Modality').'</td><td colspan="2">';   
	  print select_tables($object->fk_type_modality,'fk_type_modality','',0,1,'05');
	  print '</td></tr>';
	  
	  //type object
	  print '<tr><td>'.$langs->trans('Object').'</td><td colspan="2">';  
	   
	  print select_tables($object->fk_type_object,'fk_type_object','',0,1,'06');
	  print '</td></tr>';
	  
	  // ref
	  print '<tr><td>'.$langs->trans('Ref').'</td><td colspan="2">';
	  print $object->ref;
	  print '</td></tr>';
	  
	  // nom
	  print '<tr><td>'.$langs->trans('Name').'</td><td colspan="2">';
	  print $object->nom;
	  print '</td></tr>';
	  
	  //type financer
	  print '<tr><td>'.$langs->trans('Financer').'</td><td colspan="2">';
	  print select_financer($object->fk_financer,'fk_financer','',0,1);
	  print '</td></tr>';
	  
	  //month_init
	  print '<tr><td>'.$langs->trans('Monthinit').'</td><td colspan="2">';   
	  print select_month($object->month_init,'month_init','',15,0,1);
	  print '</td></tr>';
	  
	  //month_public
	  print '<tr><td>'.$langs->trans('Monthpublic').'</td><td colspan="2">';   
	  print select_month($object->month_public,'month_public','',15,0,1);
	  print '</td></tr>';

	  // amount
	  print '<tr><td>'.$langs->trans('Amount').'</td><td colspan="2">';
	  print number_format(price2num($object->amount,'MT'),2);
	  print '</td></tr>';
	  //respon
	  print '<tr><td>'.$langs->trans('Responsible').'</td><td colspan="2">';
	  if ($objuser->fetch($object->fk_user_resp))
	    print $objuser->lastname.' '.$objuser->firstname;
	  else
	    print '&nbsp;';
	  print '</td></tr>';
	  //statut
	  print '<tr><td>'.$langs->trans('Status').'</td><td colspan="2">';   
	  print $object->LibStatut($object->statut,2);
	  print '</td></tr>';

	  if ($object->statut != 1)
	    {
	      //status ref
	      print '<tr><td>'.$langs->trans('Statusref').'</td><td colspan="2">';   
	      print $object->LibStatut($object->statut_ref,2);
	      print '</td></tr>';
	    }
	  print "</table>";
	  
	  print '</div>';
	  
	  
	  /* ************************************************************************** */
	  /*                                                                            */
	  /* Barre d'action                                                             */
	  /*                                                                            */
	  /* ************************************************************************** */
	  
	  print "<div class=\"tabsAction\">\n";
	  print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/pac/liste.php?dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
	  if ($action == '')
	    {
	      if ($user->rights->poa->pac->crear)
		print "<a class=\"butAction\" href=\"fiche.php?action=create&dol_hide_leftmenu=1\">".$langs->trans("Createnew")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

	      if ($user->rights->poa->pac->crear )
		print "<a class=\"butAction\" href=\"fiche.php?action=edit&dol_hide_leftmenu=1&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
	      
	      if ($user->rights->poa->pac->del  && $object->statut == 0)
		print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&dol_hide_leftmenu=1&id=".$object->id."\">".$langs->trans("Delete")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	      if ($user->rights->poa->pac->val && $object->statut == 0)
		print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Validate")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";
	      if ($user->rights->poa->pac->del && $object->statut == 1)
		print "<a class=\"butAction\" href=\"fiche.php?action=nullify&dol_hide_leftmenu=1&id=".$object->id."\">".$langs->trans("Nullify")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Nullify")."</a>";
	    }	  
	  print "</div>";		

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


	  //select poa
	  print '<tr><td class="fieldrequired">'.$langs->trans('Poa').'</td><td colspan="2">';   
	  print $objpoa->select_poa($object->fk_poa,'fk_poa','',0,1);
	  print '</td></tr>';
	  
	  // gestion
	  if (empty($object->gestion))
	    $object->gestion = date('Y');
	  print '<tr><td class="fieldrequired">'.$langs->trans('Gestion').'</td><td colspan="2">';
	  print '<input id="gestion" type="text" value="'.$object->gestion.'" name="gestion" size="6" maxlength="4">';
	  print '</td></tr>';
	  
	  //type modality
	  print '<tr><td class="fieldrequired">'.$langs->trans('Modality').'</td><td colspan="2">';   
	  print select_tables($object->fk_type_modality,'fk_type_modality','',1,0,'05');
	  print '</td></tr>';
	  
	  //type object
	  print '<tr><td class="fieldrequired">'.$langs->trans('Object').'</td><td colspan="2">';   
	  print select_tables($object->fk_type_object,'fk_type_object','',1,0,'06');
	  print '</td></tr>';
	  
	  // ref
	  print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	  print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="6" maxlength="3">';
	  print '</td></tr>';
	  
	  // nom
	  print '<tr><td class="fieldrequired">'.$langs->trans('Name').'</td><td colspan="2">';
	  print '<input id="nom" type="text" value="'.$object->nom.'" name="nom" size="120" maxlength="255">';
	  print '</td></tr>';
	  
	  //type financer
	  print '<tr><td class="fieldrequired">'.$langs->trans('Financer').'</td><td colspan="2">';   
	  print select_financer($object->fk_financer,'fk_financer','',1,0);
	  print '</td></tr>';
	  
	  //month_init
	  print '<tr><td class="fieldrequired">'.$langs->trans('Monthinit').'</td><td colspan="2">';   
	  print select_month($object->month_init,'month_init','',15,1);
	  print '</td></tr>';
	  
	  //month_public
	  print '<tr><td class="fieldrequired">'.$langs->trans('Monthpublic').'</td><td colspan="2">';   
	  print select_month($object->month_public,'month_public','',15,1);
	  print '</td></tr>';

	  // amount
	  print '<tr><td class="fieldrequired">'.$langs->trans('Amount').'</td><td colspan="2">';
	  print '<input id="amount" type="text" value="'.$object->amount.'" name="amount" size="30" maxlength="12">';
	  print '</td></tr>';

	  //user resp
	  print '<tr><td class="fieldrequired">'.$langs->trans('Responsible').'</td><td colspan="2">';
	  $exclude = array();
	  if (empty($object->entity)) $object->entity = $conf->entity;
	  print $form->select_dolusers($object->fk_user_resp,'fk_user_resp',1,$exclude,0,'','',$object->entity);
	  
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
