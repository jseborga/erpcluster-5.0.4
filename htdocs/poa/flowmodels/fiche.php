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
 *      \file       htdocs/poa/flowmodels/fiche.php
 *      \ingroup    POA
 *      \brief      Page form flow models
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/poa/flowmodels/class/cflowmodels.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");
//require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';

$langs->load("poa@poa");

if (!$user->rights->poa->teacher->leer)
  accessforbidden();

$object = new Cflowmodels($db);
//$objarea = new Poaarea($db);
$objproc = new Poaprocess($db);

$id = GETPOST('id');
$action = GETPOST('action');

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$filterf = GETPOST('filterf');
$filter = GETPOST('filter');
$filtro = GETPOST('filtro');

/*actions*/
if ($action == 'add' && $user->rights->poa->teacher->crear)
  {
    $object->entity = $conf->entity;
    $object->groups = GETPOST('groups');
    $object->code = GETPOST('code');
    $object->code0 = GETPOST('code0');
    $object->code1 = GETPOST('code1');
    $object->code2 = GETPOST('code2');
    $object->code3 = GETPOST('code3');
    $object->code4 = GETPOST('code4');
    $object->deadlines = GETPOST('deadlines');
    $object->label = GETPOST('label');
    $object->label1 = GETPOST('label1');
    $object->label2 = GETPOST('label2');
    $object->label3 = GETPOST('label3');
    $object->label4 = GETPOST('label4');
    $object->sequen = GETPOST('sequen');
    $object->quant = GETPOST('quant');
    $object->code_actor_last = GETPOST('code_actor_last');
    // $object->code_actor_next = GETPOST('code_actor_next');
    // $object->code_actor_next1 = GETPOST('code_actor_next1');
    // $object->code_actor_next2 = GETPOST('code_actor_next2');
    // $object->code_actor_next3 = GETPOST('code_actor_next3');
    // $object->code_actor_next4 = GETPOST('code_actor_next4');
    $object->active = 1;
    //validaci'on
    if ($object->groups <=0)
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errortypehiringisrequired").'</div>';
      }
    if (empty($object->code))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errortypeprocedureisrequired").'</div>';
      }
    if ($object->deadlines <=0)
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errortermisrequired").'</div>';
      }
    if (empty($object->label))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorlabelisrequired").'</div>';
      }
    if ($object->sequen <=0)
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorsequenisrequired").'</div>';
      }
    if ($object->quant <=0)
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errordaysisrequired").'</div>';
      }
    if (empty($object->code_actor_last))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorofareaisrequired").'</div>';
      }
    // if (empty($object->code_area_next) || $object->code_area_next <=0)
    //   {
    // 	$error++;
    // 	$mesg.='<div class="error">'.$langs->trans("Errortoareaisrequired").'</div>';
    //   }

    if (empty($error))
      {
	$id = $object->create($user);
	if ($id <=0)
	  {
	    $mesg='<div class="error">'.$object->error.'</div>';
	    $action = 'create';
	  }
	else
	  {
	    header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
	    exit;
	  }
      }
    else
      $action = 'create';
  }

if ($action == 'update'  && $_POST["cancel"] <> $langs->trans("Cancel") && $user->rights->poa->teacher->crear)
  {
    if ($object->fetch($id)>0)
      {
	$object->groups = GETPOST('groups');
	$object->code = GETPOST('code');
	$object->code0 = GETPOST('code0');
	$object->code1 = GETPOST('code1');
	$object->code2 = GETPOST('code2');
	$object->code3 = GETPOST('code3');
	$object->code4 = GETPOST('code4');
	$object->deadlines = GETPOST('deadlines');
	$object->label = GETPOST('label');
	$object->label1 = GETPOST('label1');
	$object->label2 = GETPOST('label2');
	$object->label3 = GETPOST('label3');
	$object->label4 = GETPOST('label4');
	$object->sequen = GETPOST('sequen');
	$object->quant = GETPOST('quant');
	$object->code_actor_last = GETPOST('code_actor_last');
	// $object->code_actor_next = GETPOST('code_actor_next');
	// $object->code_actor_next1 = GETPOST('code_actor_next1');
	// $object->code_actor_next2 = GETPOST('code_actor_next2');
	// $object->code_actor_next3 = GETPOST('code_actor_next3');
	// $object->code_actor_next4 = GETPOST('code_actor_next4');

	$object->active = 1;
	$result = $object->update($user);
	if ($result <=0)
	  {
	    $mesg='<div class="error">'.$object->error.'</div>';
	    $action = 'edit';
	  }
	else
	  {
	    header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
	    exit;
	  }
      }
    else
      {
	$mesg='<div class="error">'.$object->error.'</div>';
	$action = 'edit';
      }
  }

// Delete charge
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->teacher->del)
{
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/poa/flowmodels/liste.php');
      exit;
    }
  else
    {
      $mesg='<div class="error">'.$object->error.'</div>';
      $action='';
    }
 }

if ($_POST["cancel"] == $langs->trans("Cancel"))
  {
    $action = '';
    $_GET["id"] = $_POST["id"];
  }

// if ($action == 'sub')
//   $filter.= ','.$id;

$form = new Form($db);

$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
llxHeader("",$langs->trans("Liste flow models"),$help_url);

if ($action == 'create' && $user->rights->poa->teacher->crear)
  {

    print_fiche_titre($langs->trans("Newarea"));
  
    print "<form action=\"fiche.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    
    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    // group
    print '<tr><td class="fieldrequired">'.$langs->trans('Group').'</td><td colspan="2">';
    print select_tables($object->groups,'groups','',1,0,"05",0);
    print '</td></tr>';

    // code
    print '<tr><td class="fieldrequired">'.$langs->trans('Proceduretype').'</td><td colspan="2">';
    print select_typeprocedure($object->code,'code','',1,0,"code");
    print '</td></tr>';
   
    //deadlines
    print '<tr><td class="fieldrequired">'.$langs->trans('Deadlines').'</td><td colspan="2">';
    print '<input id="deadlines" type="text" value="'.$object->deadlines.'" name="deadlines" size="4" maxlength="6">';
    print '</td></tr>';

    // // label
    // print '<tr><td class="fieldrequired">'.$langs->trans('Label').'</td><td colspan="2">';
    // print '<input id="label" type="text" value="'.$obj->label.'" name="label" size="35" maxlength="255">';
    // print '</td></tr>';

    // sequen
    print '<tr><td class="fieldrequired">'.$langs->trans('Sequen').'</td><td colspan="2">';
    print '<input id="sequen" type="text" value="'.$object->sequen.'" name="sequen" size="4" maxlength="6">';
    print '</td></tr>';

    // quant
    print '<tr><td class="fieldrequired">'.$langs->trans('Quant').'</td><td colspan="2">';
    print '<input id="quant" type="number" min="1" value="'.$object->quant.'" name="quant" size="4" maxlength="6">';
    print '</td></tr>';

    // last
    print '<tr><td class="fieldrequired">'.$langs->trans('Ofactor').'</td><td colspan="2">';
    print select_actors($obj->code_actor_last,'code_actor_last','',1,0,'code','libelle');
    print '</td></tr>';

    // next
    print '<tr><td class="fieldrequired">'.$langs->trans('Destinationone').'</td><td colspan="2">';
    print select_typeprocedure($object->code0,'code0','',1,0,"code");
    print '<input id="label" type="text" value="'.$object->label.'" name="label" size="25" maxlength="255">';
    print '</td></tr>';
    
    // actor1
    print '<tr><td>'.$langs->trans('Destinationtwo').'</td><td colspan="2">';
    print select_typeprocedure($object->code1,'code1','',1,0,"code");
    print '<input id="label1" type="text" value="'.$object->label1.'" name="label1" size="25" maxlength="255">';
    print '</td></tr>';
    // actor2
    print '<tr><td>'.$langs->trans('Destinationthree').'</td><td colspan="2">';
    print select_typeprocedure($object->code2,'code2','',1,0,"code");
    print '<input id="label2" type="text" value="'.$object->label2.'" name="label2" size="25" maxlength="255">';
    print '</td></tr>';
    // actor3
    print '<tr><td>'.$langs->trans('Destinationfour').'</td><td colspan="2">';
    print select_typeprocedure($object->code3,'code3','',1,0,"code");
    print '<input id="label3" type="text" value="'.$object->label3.'" name="label3" size="25" maxlength="255">';
    print '</td></tr>';
    // actor4
    print '<tr><td>'.$langs->trans('Destinationfive').'</td><td colspan="2">';
    print select_typeprocedure($object->code4,'code4','',1,0,"code");
    print '<input id="label4" type="text" value="'.$object->label4.'" name="label4" size="25" maxlength="255">';
    print '</td></tr>';
    
    print '</table>';

    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

    print '</form>';
  }
 else
   {
     if ($id)
       {
	 $result = $object->fetch($id);
	 if ($result < 0)
	   dol_print_error($db);

	 // Confirm delete third party
	 if ($action == 'delete')
	   {
	     $form = new Form($db);
	     $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteflowmodel"),$langs->trans("Confirmdeleteflowmodel".' '.$object->label),"confirm_delete",'',0,2);
	     if ($ret == 'html') print '<br>';
	   }

	 /*
	  * Affichage fiche
	  */
	 if ($action <> 'edit' && $action <> 're-edit')
	   {
	     //$head = fabrication_prepare_head($object);
	     dol_fiche_head($head, 'card', $langs->trans("Flowmodel"), 0, 'mant');

	     dol_htmloutput_mesg($mesg);

	     print '<table class="border" width="100%">';
	     
	     // group
	     print '<tr><td>'.$langs->trans('Group').'</td><td colspan="2">';
	     print select_tables($object->groups,'groups','',0,1,"05",0);
	     print '</td></tr>';
	     
	     // code
	     print '<tr><td>'.$langs->trans('Proceduretype').'</td><td colspan="2">';
	     print select_typeprocedure($object->code,'code','',0,1,"code");
	     print '</td></tr>';
	     
	     //deadlines
	     print '<tr><td>'.$langs->trans('Deadlines').'</td><td colspan="2">';
	     print $object->deadlines;
	     print '</td></tr>';
	     
	     // label
	     print '<tr><td>'.$langs->trans('Label').'</td><td colspan="2">';
	     print $object->label;
	     print '</td></tr>';
	     
	     // sequen
	     print '<tr><td>'.$langs->trans('Sequen').'</td><td colspan="2">';
	     print $object->sequen;
	     print '</td></tr>';
	     
	     // quant
	     print '<tr><td>'.$langs->trans('Days').'</td><td colspan="2">';
	     print $object->quant;
	     print '</td></tr>';

	         // last
	     print '<tr><td>'.$langs->trans('Ofactor').'</td><td colspan="2">';
	     print select_actors($object->code_actor_last,'code_actor_last','',0,1,'code','libelle');
	     print '</td></tr>';

	     // next
	     print '<tr><td>'.$langs->trans('Destinationone').'</td><td colspan="2">';
	     print select_typeprocedure($object->code0,'code0','',0,1,"code");
	     print ' ; '.$object->label;
	     print '</td></tr>';

	     // actor1
	     print '<tr><td>'.$langs->trans('Destinationtwo').'</td><td colspan="2">';
	     print select_typeprocedure($object->code1,'code1','',0,1,"code");
	     print ' ; '.$object->label1;
	     print '</td></tr>';
	     // actor2
	     print '<tr><td>'.$langs->trans('Destinationthree').'</td><td colspan="2">';
	     print select_typeprocedure($object->code2,'code2','',0,1,"code");
	     print ' ; '.$object->label2;
	     print '</td></tr>';
	     // actor3
	     print '<tr><td>'.$langs->trans('Destinationfour').'</td><td colspan="2">';
	     print select_typeprocedure($object->code3,'code3','',0,1,"code");
	     print ' ; '.$object->label3;
	     print '</td></tr>';
	     // actor4
	     print '<tr><td>'.$langs->trans('Destinationfive').'</td><td colspan="2">';
	     print select_typeprocedure($object->code4,'code4','',0,1,"code");
	     print ' ; '.$object->label4;
	     print '</td></tr>';
	     
	     print '</table>';

	     print '</div>';
	     	     /* ************************************** */
	     /*                                        */
	     /* Barre d'action                         */
	     /*                                        */
	     /* ************************************** */
	     
	     print "<div class=\"tabsAction\">\n";

	     if ($user->rights->poa->teacher->leer)
	       print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/flowmodels/liste.php'.'">'.$langs->trans("Return").'</a>';
	     else
	       print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Return")."</a>";
	     
	     if ($action == '')
	       {
		 if ($user->rights->poa->teacher->crear)
		   print '<a class="butAction" href="fiche.php?action=create">'.$langs->trans("Createnew")."</a>";
		 //close workflow
		 if ($user->rights->poa->teacher->mod)
		   print '<a class="butAction" href="fiche.php?id='.$id.'&action=edit">'.$langs->trans("Modify")."</a>";
		 else
	       print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
		 if ($user->rights->poa->teacher->del)
		   print '<a class="butAction" href="fiche.php?id='.$id.'&action=delete">'.$langs->trans("Delete")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";		 		 
	       }
	     print "</div>";		
	     

	   }
	 	 /*
	  * Edition fiche
	  */
	 if (($action == 'edit' || $action == 're-edit') && 1)
	   {
	     print_fiche_titre($langs->trans("Edit"));
	     
	     print "<form action=\"fiche.php\" method=\"post\">\n";
	     print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	     print '<input type="hidden" name="action" value="update">';
	     print '<input type="hidden" name="id" value="'.$id.'">';
    
	     dol_htmloutput_mesg($mesg);

	     print '<table class="border" width="100%">';
	     
	     // group
	     print '<tr><td class="fieldrequired">'.$langs->trans('Group').'</td><td colspan="2">';
	     print select_tables($object->groups,'groups','',1,0,"05",0);
	     print '</td></tr>';
	     
	     // code
	     print '<tr><td class="fieldrequired">'.$langs->trans('Proceduretype').'</td><td colspan="2">';
	     print select_typeprocedure($object->code,'code','',1,0,"code");
	     print '</td></tr>';
	     
	     //deadlines
	     print '<tr><td class="fieldrequired">'.$langs->trans('Deadlines').'</td><td colspan="2">';
	     print '<input id="deadlines" type="text" value="'.$object->deadlines.'" name="deadlines" size="4" maxlength="6">';
	     print '</td></tr>';
	     
	     // // label
	     // print '<tr><td class="fieldrequired">'.$langs->trans('Label').'</td><td colspan="2">';
	     // print '<input id="label" type="text" value="'.$object->label.'" name="label" size="35" maxlength="255">';
	     // print '</td></tr>';
	     
	     // sequen
	     print '<tr><td class="fieldrequired">'.$langs->trans('Sequen').'</td><td colspan="2">';
	     print '<input id="sequen" type="text" value="'.$object->sequen.'" name="sequen" size="4" maxlength="6">';
	     print '</td></tr>';
	     
	     // quant
	     print '<tr><td class="fieldrequired">'.$langs->trans('Quant').'</td><td colspan="2">';
	     print '<input id="quant" type="number" min="1" value="'.$object->quant.'" name="quant" size="4" maxlength="6">';
	     print '</td></tr>';

	     // last
	     print '<tr><td class="fieldrequired">'.$langs->trans('Responsible').'</td><td nowrap colspan="2">';
	     print select_actors($object->code_actor_last,'code_actor_last','',1,0,'code','libelle');

	     print '</td></tr>';
	     
	     // next
	     print '<tr><td class="fieldrequired">'.$langs->trans('Destinationone').'</td><td colspan="2">';
	     print select_typeprocedure($object->code0,'code0','',1,0,"code");
	     print '<input id="label" type="text" value="'.$object->label.'" name="label" size="25" maxlength="255">';
	     print '</td></tr>';

	     // actor1
	     print '<tr><td>'.$langs->trans('Destinationtwo').'</td><td colspan="2">';
	     print select_typeprocedure($object->code1,'code1','',1,0,"code");
	     print '<input id="label1" type="text" value="'.$object->label1.'" name="label1" size="25" maxlength="255">';
	     print '</td></tr>';
	     // actor2
	     print '<tr><td>'.$langs->trans('Destinationthree').'</td><td colspan="2">';
	     print select_typeprocedure($object->code2,'code2','',1,0,"code");
	     print '<input id="label2" type="text" value="'.$object->label2.'" name="label2" size="25" maxlength="255">';
	     print '</td></tr>';
	     // actor3
	     print '<tr><td>'.$langs->trans('Destinationfour').'</td><td colspan="2">';
	     print select_typeprocedure($object->code3,'code3','',1,0,"code");
	     print '<input id="label3" type="text" value="'.$object->label3.'" name="label3" size="25" maxlength="255">';
	     print '</td></tr>';
	     // actor4
	     print '<tr><td>'.$langs->trans('Destinationfive').'</td><td colspan="2">';
	     print select_typeprocedure($object->code4,'code4','',1,0,"code");
	     print '<input id="label4" type="text" value="'.$object->label4.'" name="label4" size="25" maxlength="255">';
	     print '</td></tr>';

	     print '</table>';
	     
	     print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;<input type="submit" class="button" value="'.$langs->trans("Cancel").'"></center>';
	     
	     print '</form>';
	     
	   }
       }
   }


$db->close();

llxFooter();
?>
