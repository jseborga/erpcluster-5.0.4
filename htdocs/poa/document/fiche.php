<?php
/* Copyright (C) 2015-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/poa/document/fiche.php
 *	\ingroup    Charges
 *	\brief      Page fiche mant charges
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

//require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/document/class/poadocuments.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';

// require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("poa@poa");

if (!$user->rights->poa->teacher->leer)
  accessforbidden();


$action=GETPOST('action');

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';

$object  = new Poadocuments($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->poa->area->crear)
  {
    $error = 0;
    $object->ref       = $_POST["ref"];
    $object->label     = GETPOST('label');
    $object->fk_father = GETPOST('fk_father');
    $object->code_actor  = GETPOST('code_actor');
    $object->pos     = 0;
    //si tiene father
    $obj = new Poaarea($db);
    if ($obj->fetch($object->fk_father) && $object->fk_father > 0)
      {
	$object->pos = $obj->pos + 1;
      }
    else
      $object->pos = 0;
    $object->entity  = $conf->entity;
    $object->active  = 1;
    if (empty($object->ref))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorrefrequired").'</div>';
      }
    if (empty($object->label))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorlabelrequired").'</div>';
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

// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
  {
    if ($object->fetch($_POST["id"]))
      {
	$object->ref       = $_POST["ref"];
	$object->label     = GETPOST('label');
	$object->fk_father = GETPOST('fk_father');
	$object->code_actor  = GETPOST('code_actor');
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

if ($action == 'create' && $user->rights->poa->area->crear)
  {
    print_fiche_titre($langs->trans("Newarea"));
  
    print "<form action=\"fiche.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    
    dol_htmloutput_mesg($mesg);

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
    print $object->select_area($object->fk_father,'fk_father','',40,1);
    print '</td></tr>';

    //actors
    print '<tr><td>'.$langs->trans('Actor').'</td><td colspan="2">';
    print select_actors($object->code_actor,'code_actor','',1);
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
	     
	     dol_fiche_head($head, 'card', $langs->trans("Area"), 0, 'mant');
	     
	     /*
	      * Confirmation de la validation
	      */
	     if ($action == 'validate')
	       {
		 $object->fetch(GETPOST('id'));
		 //cambiando a validado
		 $object->statut = 1;
		 $object->ref = $object->codref;
		 //update
		 $object->update($user);
		 $action = '';
		 //header("Location: fiche.php?id=".$_GET['id']);
		 
	       }
	     if ($action == 'deluser')
	       {
		 if($objuser->fetch(GETPOST('idus'))>0)
		    $objuser->delete($user);
		 $action = '';
	       }
	     
	     /*
	      * activate
	      */
	     if ($action == 'activate' || $action == 'deactivate')
	       {
		 if ($objuser->fetch(GETPOST('idr')))
		   {
		     if ($objuser->id == $idr && $objuser->fk_area == $id)
		       {
			 //cambiando a validado
			 $objuser->active = ($action=='activate'?1:0);
			 //update
			 $objuser->update($user);
			 $action = '';
			 $mesg.='<div class="ok">'.$langs->trans("Satisfactory update").'</div>';
		       }
		   }
	       }

	     /*
	      * privileges
	      */
	     if ($action == 'privadmin' || $action == 'privuser' || $action == 'notpriv' || $action == 'privvisit')
	       {
		 if ($objuser->fetch(GETPOST('idr')))
		   {
		     if ($objuser->id == $idr && $objuser->fk_area == $id)
		       {
			 //cambiando a validado
			 $objuser->privilege = ($action=='privadmin'?1:($action=='privuser'?2:($action=='privvisit'?3:0)));
			 //update
			 $objuser->update($user);
			 $action = '';
			 $mesg.='<div class="ok">'.$langs->trans("Satisfactory update").'</div>';
		       }
		   }
	       }
	     
	     // Confirm delete third party
	     if ($action == 'delete')
	       {
		 $form = new Form($db);
		 $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deletecharge"),$langs->trans("Confirmdeletecharge",$object->ref.' '.$object->detail),"confirm_delete",'',0,2);
		 if ($ret == 'html') print '<br>';
	       }
	     
	     dol_htmloutput_mesg($mesg);

	     print '<table class="border" width="100%">';

	     // ref
	     print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';
	     $linkback = '<a href="'.DOL_URL_ROOT.'/poa/area/liste.php">'.$langs->trans("BackToList").'</a>';
	     
	     print '<td class="valeur"  colspan="2">';
	     print $form->showrefnav($object, 'id', $linkback);
	     print '</td></tr>';
	     
	     //label
	     print '<tr><td>'.$langs->trans('Label').'</td><td colspan="2">';
	     print $object->label;
	     print '</td></tr>';
	     
	     //father
	     print '<tr><td>'.$langs->trans('Father').'</td><td colspan="2">';
	     $obj = new Poaarea($db);
	     $obj->fetch($object->fk_father);
	     if ($obj->id == $object->fk_father)
	       print $obj->label;
	     else
	       print '&nbsp;';
	     print '</td></tr>';

	     //actors
	     print '<tr><td>'.$langs->trans('Actor').'</td><td colspan="2">';
	     print select_actors($object->code_actor,'code_actor','',0,1);
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
		 if ($user->rights->poa->area->crear)
		   print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
		 
		 if ($user->rights->poa->area->crear)
		   print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
		 
		 if ($user->rights->poa->area->del)
		   print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	       }	  
	     print "</div>";		
	     
	     //registro de usuarios
	     $objuser->getlist($object->id);
	     //encabezado
	     print_barre_liste($langs->trans("User"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
	     
	     print '<table class="noborder" width="100%">';
	     
	     print "<tr class=\"liste_titre\">";
	     print_liste_field_titre($langs->trans("User"),"", "","","","");
	     print_liste_field_titre($langs->trans("Status"),"", "","","","");
	     print_liste_field_titre($langs->trans("Permissions"),"", "","","","");
	     print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
	     print "</tr>\n";
	     
	     if ($user->rights->poa->area->crear)
	       {
		 print '<form action="fiche.php" method="POST">';
		 print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		 print '<input type="hidden" name="action" value="adduser">';
		 print '<input type="hidden" name="id" value="'.$object->id.'">';
		 
		 // On selectionne les users qui ne sont pas deja dans le groupe
		 $exclude = array();
		 
		 // user
		 print '<td>';
		 print $form->select_dolusers('','fk_user',1,$exclude,0,'','',$object->entity);
		 print '</td>';
		 
		 //label
		 print '<td>';
		 print '&nbsp;';
		 print '</td>';
		 print '<td>';
		 print '&nbsp;';
		 print '</td>';
		 print '<td align="right">';
		 print '<input type="submit" class="button" value="'.$langs->trans("Save").'">';
		 
		 print '</td></tr>';
		 print '</form>';
	       }
	     if (count($objuser->array) > 0)
	       {
		 foreach((array) $objuser->array AS $j => $objus)
		   {
		     $obuser->fetch($objus->fk_user);
		     print '<tr>';
		     print '<td>';
		     print $obuser->lastname.' '.$obuser->firstname;
		     print '</td>';
		     
		     //active
		     print '<td>';
		     if ($user->rights->poa->area->crear)
		       {
			 print '<a href="fiche.php?id='.$id.'&idr='.$objus->id.'&action='.($objus->active?'deactivate':'activate').'">'.$objuser->LibStatut($objus->active,2,0).'</a>';
		       }
		     else
		       print $objuser->LibStatut($objus->active,2,0);

		     //privileges
		     print '<td>';
		     if ($user->rights->poa->area->crear)
		       {
			 print '<a href="fiche.php?id='.$id.'&idr='.$objus->id.'&action='.($objus->privilege==0?'privadmin':($objus->privilege==1?'privuser':($objus->privilege==2?'privvisit':'notpriv'))).'">'.$objuser->LibStatut($objus->privilege,4,0).'</a>';
		       }
		     else
		       print $objuser->LibStatut($objus->privilege,4,0);

		     print '</td>';
		     print '<td align="right">';
		     if ($user->rights->poa->area->crear)
		       {
			 print '<a href="'.DOL_URL_ROOT.'/poa/area/fiche.php?id='.$id.'&idus='.$objus->id.'&action=deluser'.'">'.img_picto($langs->trans('Delete'),'delete').'</a>';
		       }
		     print '</td></tr>';
		     
		   }
	       }
	     print '</table>';
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

	     //actors
	     print '<tr><td>'.$langs->trans('Actor').'</td><td colspan="2">';
	     print select_actors($object->code_actor,'code_actor','',1);
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
