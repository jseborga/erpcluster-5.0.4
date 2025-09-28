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
 *	\file       htdocs/addendum/fichelink.php
 *	\ingroup    Addendum link contract
 *	\brief      Page fiche addendum link contract
 */

require("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/addendum/class/addendum.class.php';
require_once DOL_DOCUMENT_ROOT.'/addendum/lib/addendum.lib.php';
require_once DOL_DOCUMENT_ROOT.'/addendum/lib/contrat.lib.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';


$langs->load("addendum@addendum");

if (!$user->rights->addendum->leer)
  accessforbidden();
//$db = $this->db;

$action=GETPOST('action');

$cid       = GETPOST('cid');
$id        = GETPOST("id");
$idr       = GETPOST("idr");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
//recuperando direccion para fiche de contrat
//version 3.7 <
$dir = DOL_DOCUMENT_ROOT.'/contrat/fiche.php';
if (file_exists($dir)) $cFile = 'fiche.php';
//version 3.8 >=
$dir = DOL_DOCUMENT_ROOT.'/contrat/card.php';
if (file_exists($dir)) $cFile = 'card.php';

$mesg = '';

$object  = new Addendum($db);
$objcon  = new Contrat($db);
$obuser  = new User($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objcon->table_element);

//buscamos el contrato actual
$objcon->fetch($cid);
$fk_soc = $objcon->fk_soc;
/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->addendum->crear && $_POST['Cancel'] != $langs->trans('Cancel'))
  {
    $error = 0;
    $object->fk_contrat_son = $_POST["cid"];
    $object->fk_contrat_father = GETPOST('fk_contrat');
    $object->fk_user_create = $user->id;
    $object->date_create    = dol_now();
    $object->tms = dol_now();
    $object->statut = 1;
    if (empty($object->fk_contrat_father))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorcontratfatherisrequired").'</div>';
      }
    if (empty($object->fk_contrat_son))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorcontratsonisrequired").'</div>';
      }

    if (empty($error)) 
      {
	$id = $object->create($user);
	if ($id > 0)
	  {
	    header("Location: ".DOL_URL_ROOT.'/contrat/'.$cfile.'?id='.$object->fk_contrat_son);
	    exit;
	  }
	$action = 'confirm_add_contrat';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else
      {
	if ($error)
	  $action="create";   // Force retour sur page creation
      }
  }
//cancel
if ($_POST[$langs->trans('Cancel')] == $langs->trans("Cancel"))
  {
    $action = '';
    $_GET["id"] = $_POST["id"];
    header('Location: '.DOL_URL_ROOT.'/addendum/liste.php?id='.$cid);
    exit;
  }



/*
 * View
 */

$form=new Form($db);

//$help_url='EN:Module_Addendum_En|FR:Module_Addendum|ES:M&oacute;dulo_Addendum';
llxHeader("",$langs->trans("Addendum"),$help_url);
$aContrat = getListOfContracts($fk_soc,'others',0,$cid);

if ($action=='create' && $user->rights->addendum->crear)
  {
    print_fiche_titre($langs->trans("Linkaddendum"));
    //$cid = $_SESSION['fk_contrat_father'];
    print "<form action=\"fichelink.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="cid" value="'.$cid.'">';
    
    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    //contrat father
    print '<tr><td class="fieldrequired" width="15%">'.$langs->trans('Hostcontrat').'</td><td colspan="2">';

    foreach((array) $aContrat AS $j => $dataContrat)
      {
	if ($dataContrat->id != $cid)
	  {
	    if (empty($aCon[$dataContrat->id]))
	      //buscamos si no es hijo
	      $res = $object->getlist_son($dataContrat->id);
	    if ($res <= 0)
	      {
		if (!empty($dataContrat->array_options['options_ref_contrato']))
		  $aArray[$dataContrat->id] = $dataContrat->array_options['options_ref_contrato'];
		else
		  $aArray[$dataContrat->id] = $dataContrat->ref;
	      }
	  }
      }
    print $form->selectarray('fk_contrat',$aArray,$_SESSION['fk_contrat_father']);    
    print '</td></tr>';
    
    
    print '</table>';
    
    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'">&nbsp;';
    print '<input type="submit" class="button" name="'.$langs->trans('Cancel').'" value="'.$langs->trans("Cancel").'"></center>';
    
    print '</form>';
  }
 // else
 //   {
 //     if ($_GET["id"])
 //       {
	 
 // 	 $result = $object->fetch($_GET["id"]);
 // 	 if ($result < 0)
 // 	   {
 // 	     dol_print_error($db);
 // 	   }
	 
	 
 // 	 /*
 // 	  * Affichage fiche
 // 	  */
 // 	 if ($actionl <> 'edit' && $actionl <> 're-edit')
 // 	   {
 // 	     //$head = fabrication_prepare_head($object);
	     
 // 	     dol_fiche_head($head, 'card', $langs->trans("Addendum"), 0, 'mant');
	     
 // 	     // Confirm delete third party
 // 	     if ($actionl == 'delete')
 // 	       {
 // 		 $form = new Form($db);
 // 		 $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteaddendum"),$langs->trans("Confirmdeleteaddendum"),"confirm_delete",'',0,2);
 // 		 if ($ret == 'html') print '<br>';
 // 	       }
	 
 // 	     dol_htmloutput_mesg($mesg);

 // 	     print '<table class="border" width="100%">';

 // 	     // ref
 // 	     print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';
 // 	     $linkback = '<a href="'.DOL_URL_ROOT.'/poa/area/liste.php">'.$langs->trans("BackToList").'</a>';
	     
 // 	     print '<td class="valeur"  colspan="2">';
 // 	     print $form->showrefnav($object, 'id', $linkback);
 // 	     print '</td></tr>';
	     
 // 	     //label
 // 	     print '<tr><td>'.$langs->trans('Label').'</td><td colspan="2">';
 // 	     print $object->label;
 // 	     print '</td></tr>';
	     
 // 	     //father
 // 	     print '<tr><td>'.$langs->trans('Father').'</td><td colspan="2">';
 // 	     $obj = new Poaarea($db);
 // 	     $obj->fetch($object->fk_father);
 // 	     if ($obj->id == $object->fk_father)
 // 	       print $obj->label;
 // 	     else
 // 	       print '&nbsp;';
 // 	     print '</td></tr>';

 // 	     //actors
 // 	     print '<tr><td>'.$langs->trans('Actor').'</td><td colspan="2">';
 // 	     print select_actors($object->code_actor,'code_actor','',0,1);
 // 	     print '</td></tr>';

 // 	     print "</table>";
	     
 // 	     print '</div>';
	     
	     
 // 	     /* ************************************************************************** */
 // 	     /*                                                                            */
 // 	     /* Barre d'action                                                             */
 // 	     /*                                                                            */
 // 	     /* ************************************************************************** */
	     
 // 	     print "<div class=\"tabsAction\">\n";
	     
 // 	     if ($actionl == '')
 // 	       {
 // 		 if ($user->rights->poa->area->crear)
 // 		   print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
 // 		 else
 // 		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
		 
 // 		 if ($user->rights->poa->area->crear)
 // 		   print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
 // 		 else
 // 		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
		 
 // 		 if ($user->rights->poa->area->del)
 // 		   print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
 // 		 else
 // 		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
 // 	       }	  
 // 	     print "</div>";		
	     
 // 	     //registro de usuarios
 // 	     $objuser->getlist($object->id);
 // 	     //encabezado
 // 	     print_barre_liste($langs->trans("User"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
	     
 // 	     print '<table class="noborder" width="100%">';
	     
 // 	     print "<tr class=\"liste_titre\">";
 // 	     print_liste_field_titre($langs->trans("User"),"", "","","","");
 // 	     print_liste_field_titre($langs->trans("Status"),"", "","","","");
 // 	     print_liste_field_titre($langs->trans("Permissions"),"", "","","","");
 // 	     print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
 // 	     print "</tr>\n";
	     
 // 	     if ($user->rights->poa->area->crear)
 // 	       {
 // 		 print '<form action="fiche.php" method="POST">';
 // 		 print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
 // 		 print '<input type="hidden" name="action" value="adduser">';
 // 		 print '<input type="hidden" name="id" value="'.$object->id.'">';
		 
 // 		 // On selectionne les users qui ne sont pas deja dans le groupe
 // 		 $exclude = array();
		 
 // 		 // user
 // 		 print '<td>';
 // 		 print $form->select_dolusers('','fk_user',1,$exclude,0,'','',$object->entity);
 // 		 print '</td>';
		 
 // 		 //label
 // 		 print '<td>';
 // 		 print '&nbsp;';
 // 		 print '</td>';
 // 		 print '<td>';
 // 		 print '&nbsp;';
 // 		 print '</td>';
 // 		 print '<td align="right">';
 // 		 print '<input type="submit" class="button" value="'.$langs->trans("Save").'">';
		 
 // 		 print '</td></tr>';
 // 		 print '</form>';
 // 	       }
 // 	     if (count($objuser->array) > 0)
 // 	       {
 // 		 foreach((array) $objuser->array AS $j => $objus)
 // 		   {
 // 		     $obuser->fetch($objus->fk_user);
 // 		     print '<tr>';
 // 		     print '<td>';
 // 		     print $obuser->lastname.' '.$obuser->firstname;
 // 		     print '</td>';
		     
 // 		     //active
 // 		     print '<td>';
 // 		     if ($user->rights->poa->area->crear)
 // 		       {
 // 			 print '<a href="fiche.php?id='.$id.'&idr='.$objus->id.'&action='.($objus->active?'deactivate':'activate').'">'.$objuser->LibStatut($objus->active,2,0).'</a>';
 // 		       }
 // 		     else
 // 		       print $objuser->LibStatut($objus->active,2,0);

 // 		     //privileges
 // 		     print '<td>';
 // 		     if ($user->rights->poa->area->crear)
 // 		       {
 // 			 print '<a href="fiche.php?id='.$id.'&idr='.$objus->id.'&action='.($objus->privilege==0?'privadmin':($objus->privilege==1?'privuser':($objus->privilege==2?'privvisit':'notpriv'))).'">'.$objuser->LibStatut($objus->privilege,4,0).'</a>';
 // 		       }
 // 		     else
 // 		       print $objuser->LibStatut($objus->privilege,4,0);

 // 		     print '</td>';
 // 		     print '<td align="right">';
 // 		     if ($user->rights->poa->area->crear)
 // 		       {
 // 			 print '<a href="'.DOL_URL_ROOT.'/poa/area/fiche.php?id='.$id.'&idus='.$objus->id.'&action=deluser'.'">'.img_picto($langs->trans('Delete'),'delete').'</a>';
 // 		       }
 // 		     print '</td></tr>';
		     
 // 		   }
 // 	       }
 // 	     print '</table>';
 // 	     //fin registro usuarios
 //       }     
//}



//llxFooter();

//$db->close();
?>
