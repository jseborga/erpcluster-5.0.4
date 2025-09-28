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
 *	\file       htdocs/mant/charge/fiche.php
 *	\ingroup    Charges
 *	\brief      Page fiche mant charges
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mlocation.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("mant@mant");

$action=GETPOST('action');

$id        = GETPOST("id");
$idr       = GETPOST("idr");

$mesg = '';

$object  = new Mproperty($db);
$objLocation = new Mlocation($db);
/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->mant->teacher->crear)
  {
    $object->ref     = $_POST["ref"];
    $object->address  = GETPOST('address');
    $object->entity  = $conf->entity;

    if ($object->ref) 
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
	$mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
	$action="create";   // Force retour sur page creation
      }
  }


// Delete charge
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->mant->teacher->del)
{
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/mant/charge/liste.php');
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
	$object->ref     = $_POST["ref"];
	$object->detail  = GETPOST('detail');
	$object->skills  = GETPOST('skills');
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

// Add
if ($action == 'addloc' && $user->rights->mant->teacher->crear && !empty($id))
  {
    $objLocation->fk_property = $id;
    $objLocation->detail      = GETPOST("detail");
    $objLocation->safety = GETPOST('safety');
    $objLocation->fk_user_modify = $user->id;
    $objLocation->tms = dol_now();
    $objLocation->statut = 1;
    
    if (!empty($objLocation->detail)) 
      {
	$idloc = $objLocation->create($user);
	if ($idloc <= 0)
	  {
	    $action = '';
	    $mesg='<div class="error">'.$object->error.'</div>';
	  }
	else
	  $action = '';
      }
    else
      {
	$mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
	$action="";   // Force retour sur page creation
      }
  }

// updateloc
if ($action == 'updateloc' && $user->rights->mant->teacher->crear && !empty($id) && !empty($idr))
  {
    if ($objLocation->fetch($idr)>0)
      {
	if ($objLocation->fk_property == $id && $objLocation->id == $idr)
	  {
	    $objLocation->detail = GETPOST("detail");
	    $objLocation->safety = GETPOST('safety');
	    $objLocation->fk_user_modify = $user->id;
	    $objLocation->tms = dol_now();
	    $objLocation->statut = 1;

	    $res = $objLocation->update($user);
	    if ($res <= 0)
	      {
		$action = 'locmod';
		$mesg='<div class="error">'.$object->error.'</div>';
	      }
	    else
	      {
		$action = '';
	      }
	  }
	else
	  {
	    $mesg='<div class="error">'.$langs->trans("Errorincorrectinformation").'</div>';
	    $action="locmod";   // Force retour sur page creation
	  }
      }
    else
      {
	$mesg='<div class="error">'.$langs->trans("Errordoesnotexist").'</div>';
	$action="locmod";   // Force retour sur page creation
      }
  }


/*
 * View
 */

$form=new Form($db);

$help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
llxHeader("",$langs->trans("Managementjobs"),$help_url);

if ($action == 'create' && $user->rights->mant->teacher->crear)
  {
    print_fiche_titre($langs->trans("Newproperty"));
  
    print "<form action=\"fiche.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    
    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    // ref
    print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
    print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="50" maxlength="50">';
    print '</td></tr>';
   
    //address
    print '<tr><td>'.$langs->trans('Address').'</td><td colspan="2">';
    print '<textarea class="flat" name="address" id="address" cols="40" rows="'.ROWS_3.'">';
    print $object->address;
    print '</textarea>';
    print '</td></tr>';
    
    print '</table>';

    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

    print '</form>';
  }
 else
   {
     if ($id)
       {
	 dol_htmloutput_mesg($mesg);
	 
	 $result = $object->fetch($id);
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
	     
	     dol_fiche_head($head, 'card', $langs->trans("Property"), 0, 'mant');
	     
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
	       }
	     
	     // Confirm delete third party
	     if ($action == 'delete')
	       {
		 $form = new Form($db);
		 $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteproperty"),$langs->trans("Confirmdeleteproperty",$object->ref.' '.$object->detail),"confirm_delete",'',0,2);
		 if ($ret == 'html') print '<br>';
	       }
	     
	     print '<table class="border" width="100%">';
	     
	     // ref
	     print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';
	     $linkback = '<a href="'.DOL_URL_ROOT.'/mant/property/liste.php">'.$langs->trans("BackToList").'</a>';
	     
	     print '<td class="valeur"  colspan="2">';
	     print $form->showrefnav($object, 'id', $linkback);
	     print '</td></tr>';
	     
	     
	     //address
	     print '<tr><td>'.$langs->trans('Address').'</td><td colspan="2">';
	     print $object->address;
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
		 if ($user->rights->mant->teacher->crear)
		   print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
		 
		 if ($user->rights->mant->teacher->crear)
		   print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
		 
		 if ($user->rights->mant->teacher->del)
		   print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	       }	  
	     print "</div>";		
	     
	     //listando las ubicaciones
	     $objLocation->list_location($id);
	     
	     //dol_fiche_head($head, 'card', $langs->trans("Location"), 0, 'mant');
	     
	     print '<form action="fiche.php" method="POST">';
	     print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	     print '<input type="hidden" name="id" value="'.$object->id.'">';
	     if ($action == 'locmod')
	       {
		 print '<input type="hidden" name="action" value="updateloc">';
		 print '<input type="hidden" name="idr" value="'.$idr.'">';
	       }
	     else
	       print '<input type="hidden" name="action" value="addloc">';
	     
	     print '<table class="noborder" width="100%">';
	     print '<tr class="liste_titre">';
	     print_liste_field_titre($langs->trans("Location"));
	     print_liste_field_titre($langs->trans("Safety"),'','','','','align="center"');
	     print_liste_field_titre($langs->trans("Statut"),'','','','','align="center"');
	     print_liste_field_titre($langs->trans("Action"),'','','','','align="center"');
	     print '</tr>';
	     
	     
	     // location
	     if ($action != 'locmod')
	       {
		 print '<tr>';
		 print '<td>';
		 print '<input id="detail" type="text" value="" name="detail" size="50" maxlength="50">';
		 print '</td>';
		 print '<td align="center">';
		 print '<input id="safety" type="checkbox" value="1" name="safety">';
		 print '</td>';
		 print '<td align="center">';
		 print '&nbsp;';
		 print '</td>';
		 print '<td align="center">';
		 print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="14" height="14">';
		 print '</td>';
	      
		 print '</tr>';
	       }
	     $num = count($objLocation->aArray);
	     if ($num)
	       { 	      
		 foreach((array) $objLocation->aArray AS $i => $obj)
		   {
		     if ($action == 'locmod' && $obj->id == $idr)
		       {
			 print '<tr>';
			 print '<td>';
			 print '<input id="detail" type="text" value="'.$obj->detail.'" name="detail" size="50" maxlength="50">';
			 print '</td>';
			 print '<td align="center">';
			 print '<input id="safety" type="checkbox" value="1" '.(!empty($obj->safety)?'checked':'').' name="safety">';
			 print '</td>';
			 print '<td align="center">';
			 print '&nbsp;';
			 print '</td>';
			 print '<td align="center">';
			 print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="14" height="14">';
			 print '</td>';
			 
			 print '</tr>';
			 
		       }
		     else
		       {
			 print '<tr><td>';
			 print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&amp;idr='.$obj->id.'&amp;action=locmod">'.$obj->detail.'</a>';
			 print '<td align="center">'.($obj->safety==1?$langs->trans('Activesafety'):$langs->trans('Nosecurity')).'</td>';
			 print '<td align="center">'.$obj->statut.'</td>';
			 print '</td></tr>';
		       }
		   }
	       }
	     print '</table>';
	     //print '<center><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	     print '</form>';
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
	     print '<input id="ref" type="text" value="'.$object->codref.'" name="ref" size="50" maxlength="50">';
	     print '</td></tr>';
	     
	     //detail
	     print '<tr><td>'.$langs->trans('Details').'</td><td colspan="2">';
	     print '<textarea class="flat" name="detail" id="detail" cols="40" rows="'.ROWS_3.'">';
	     print $object->detail;
	     print '</textarea>';
	     print '</td></tr>';
	     
	     //Skills
	     print '<tr><td>'.$langs->trans('Skills').'</td><td colspan="2">';
	     print '<textarea class="flat" name="skills" id="skills" cols="40" rows="'.ROWS_3.'">'; 
	     print $object->skills;
	     print '</textarea>';
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
