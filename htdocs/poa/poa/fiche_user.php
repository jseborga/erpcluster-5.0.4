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
 *	\file       htdocs/poa/poa//fiche_user.php
 *	\ingroup    Poa
 *	\brief      Page fiche poa user
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/poa/class/poapoa.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/poa/class/poapoauser.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/structure/class/poastructure.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("poa@poa");

$action=GETPOST('action');

$idp       = GETPOST("idp"); //idpoa
$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';

$object = new Poapoauser($db);
$objpoa = new Poapoa($db);
$objstr = new Poastructure($db);
$objuser = new User($db);
/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->poa->area->crear)
  {
    $error = 0;
    $object->fk_user       = $_POST["fk_user"];
    $object->fk_poa_poa     = GETPOST('idp');
    $object->order_user     = GETPOST('order_user');
    $object->date_create = date('Y-m-d');
    $object->statut     = 1;
    $object->active = 0;
    //si tiene father
    if (empty($object->fk_user))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Erroruserisrequired").'</div>';
      }
    if (empty($error)) 
      {
	$id = $object->create($user);
	if ($id > 0)
	  {
	    //actualizamos la numeracion
	    $object->update_number($user,$idp,$id,$object->order_user);
	    header("Location: fiche_user.php?id=".$id.'&idp='.$idp);
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


// Delete poa
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->area->del)
{
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/poa/poa/fiche_user.php?idp='.$idp.'&id='.$id);
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
	$object->ref       = $_POST["ref"];
	$object->label     = GETPOST('label');
	$object->fk_father = GETPOST('fk_father');
	//si tiene father
	$obj = new Poaarea($db);
	if ($obj->fetch($object->fk_father) && $object->fk_father > 0)
	  {
	    $object->pos = $obj->pos + 1;
	  }
	else
	  $object->pos = 0;

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

$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
llxHeader("",$langs->trans("Managementjobs"),$help_url);

if ($action == 'create' && $user->rights->poa->poa->crear)
  {
    print_fiche_titre($langs->trans("Newpoauser"));
  
    print '<form action="fiche_user.php" method="post">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="idp" value="'.$idp.'">';
    
    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    //poa
    $objpoa->fetch($idp);
    $objstr->fetch($objpoa->fk_structure);

    // meta
    print '<tr><td>'.$langs->trans('Meta').'</td><td colspan="2">';
    print $objstr->sigla;
    print '</td></tr>';

    // actividade
    print '<tr><td>'.$langs->trans('Meta').'</td><td colspan="2">';
    print $objpoa->label;
    print '</td></tr>';

    // user
    print '<tr><td>'.$langs->trans('User').'</td><td colspan="2">';
    $exclude = array();
    if (empty($object->entity)) $object->entity = $conf->entity;
    print $form->select_dolusers((empty($object->fk_user)?$user->id:$object->fk_user),'fk_user',1,$exclude,0,'','',$object->entity);
    print '</td></tr>';

    // order
    print '<tr><td>'.$langs->trans('Order').'</td><td colspan="2">';
    print '<input type="number" name="order_user" value="'.$object->order_user.'" size="1">';
    print '</td></tr>';


    print '</table>';

    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

    print '</form>';
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
	  
	  dol_fiche_head($head, 'card', $langs->trans("Area"), 0, 'mant');
	  
	  /*
	   * Confirmation de la validation
	   */
	  if ($action == 'activate')
	    {
	      $object->fetch(GETPOST('id'));
	      //cambiando a validado
	      $object->active = 1;
	      //update
	      $object->update($user);

	      //desactivamos los demas
	      $object->getlist($idp);
	      foreach((array) $object->array AS $j => $objnew)
		{
		  if ($object->id != $objnew->id)
		    {
		      $objdeact = new Poapoauser($db);
		      $objdeact->fetch($objnew->id);
		      $objdeact->active = 0;
		      $objdeact->update($user);
		    }
		}
	      $action = '';
	      //header("Location: fiche.php?id=".$_GET['id']);
	      
	    }
	  /*
	   * Confirmation de la validation
	   */
	  if ($action == 'deactivate')
	    {
	      $object->fetch(GETPOST('id'));
	      //cambiando a validado
	      $object->active = 0;
	      //update
	      $object->update($user);
	      $action = '';
	      //header("Location: fiche.php?id=".$_GET['id']);
	      
	    }
	  
	  // Confirm delete third party
	  if ($action == 'delete')
	    {
	      $form = new Form($db);
	      $ret=$form->form_confirm($_SERVER["PHP_SELF"].'?idp='.$idp."&id=".$object->id,$langs->trans("Deleteuser"),$langs->trans("Confirmdeleteuser",$object->ref.' '.$object->detail),"confirm_delete",'',0,2);
	      if ($ret == 'html') print '<br>';
	    }
	  
	  print '<table class="border" width="100%">';

	  //poa
	  $objpoa->fetch($idp);
	  $objstr->fetch($objpoa->fk_structure);
	  
	  // meta
	  print '<tr><td>'.$langs->trans('Structure').'</td><td colspan="2">';
	  print $objstr->sigla;
	  print '</td></tr>';
	  
	  // actividade
	  print '<tr><td>'.$langs->trans('Meta').'</td><td colspan="2">';
	  print $objpoa->label;
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
	      if ($user->rights->poa->poa->crear)
		print "<a class=\"butAction\" href=\"fiche_user.php?idp=".$idp."&action=create\">".$langs->trans("Createnew")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

	    }	  
		print "<a class=\"butAction\" href=\"liste.php\">".$langs->trans("Return")."</a>";

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
	  
	  print '<table class="border" width="100%">';
	  
	  // ref
	  print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	  print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="33" maxlength="30">';
	  print '</td></tr>';
	  
	  //label
	  print '<tr><td class="fieldrequired">'.$langs->trans('Label').'</td><td colspan="2">';
	  print '<input id="label" type="text" value="'.$object->label.'" name="label" size="50" maxlength="255">';
	  print '</td></tr>';
	  
	  //father
	  print '<tr><td>'.$langs->trans('Father').'</td><td colspan="2">';
	  print $object->select_area($object->fk_father,'fk_father','',40,1,$object->id);
	  print '</td></tr>';  
	  
	  print '</table>';
	  
	  print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	  print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
	  
	  print '</form>';
	  
	}
    }
}
//liste
$object->getlist($idp);
if (count($object->array))
  {
    print_barre_liste($langs->trans("Listeuser"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);

    print '<table class="noborder" width="100%">';
    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("User"),"", "","","","");
    print_liste_field_titre($langs->trans("Order"),"", "","","","");
    print_liste_field_titre($langs->trans("Active"),"", "","","","");
    print_liste_field_titre($langs->trans("Action"),"", "","","","");
    print "</tr>\n";

    foreach ((array) $object->array AS $i => $objlist)
      {
	print '<tr>';
	print '<td>';
	if ($objuser->fetch($objlist->fk_user))
	  print $objuser->lastname.' '.$objuser->firstname;
	else
	  print 'no existe';
	print '</td>';
	print '<td>';
	print $objlist->order_user;
	print '</td>';

	print '<td>';
	$newaction = 'activate';
	if ($objlist->active == 1)$newaction = 'deactivate';
	if ($user->rights->poa->poa->crear)
	  print '<a href="fiche_user.php?idp='.$idp.'&id='.$objlist->id.'&action='.$newaction.'">'.(empty($objlist->active)?img_picto($langs->trans('No active'),'switch_off'):img_picto($langs->trans('Activate'),'switch_on')).'</a>';
	else
	  print '<a href="#">'.(empty($objlist->active)?img_picto($langs->trans('No active'),'switch_off'):img_picto($langs->trans('Activate'),'switch_on')).'</a>';

	print '</td>';

	print '<td>';
	if ($objlist->active == 0)
	  print '<a href="fiche_user.php?idp='.$idp.'&id='.$objlist->id.'&action=delete">'.img_picto($langs->trans('Delete'),'delete').'</a>';
	else
	  print '&nbsp;';
	print '</td>';
	print '</tr>';
      }
    print '</table>';
    print '</div>';
}

llxFooter();

$db->close();
?>
