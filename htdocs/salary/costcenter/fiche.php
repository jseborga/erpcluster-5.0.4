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
 *	\file       htdocs/salary/costcenter/fiche.php
 *	\ingroup    Cost Center
 *	\brief      Page fiche salary Cost center
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcentrocosto.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("salary@salary");

$action=GETPOST('action');

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';
$error='';
$mesgerror= '';
$object  = new Pcentrocosto($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->salary->cc->creer)
  {
    $object->ref       = $_POST["ref"];
    $object->entity    = $conf->entity;
    $object->fk_cc_sup = GETPOST('fk_cc_sup');
    $object->label     = GETPOST('label');
    $object->stipulation = GETPOST('stipulation');
    $object->locked    = GETPOST('locked');
    $object->state     = 0;
    if (empty($object->label))
      {
	$error++;
	$mesgerror .= '<br>'.$langs->trans('Errorlabelrequired');
      }
    if ($object->stipulation<=0)
      {
	$error++;
	$mesgerror .= '<br>'.$langs->trans('Errorstipulationrequired');
      }
    if ($object->locked<=0)
      {
	$error++;
	$mesgerror .= '<br>'.$langs->trans('Errorlockedrequired');
      }
    if ($object->ref && empty($error)) 
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


// Delete period
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salary->cc->del)
  {
    $object->fetch($_REQUEST["id"]);
    $result=$object->delete($user);
    if ($result > 0)
      {
	header("Location: ".DOL_URL_ROOT.'/salary/costcenter/liste.php');
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
	$object->ref   = $_POST["ref"];
	$object->fk_cc_sup = GETPOST('fk_cc_sup');
	$object->label = GETPOST('label');
	$object->stipulation = GETPOST('stipulation');
	$object->locked = GETPOST('locked');
	$object->state    = 0;
	if (empty($object->label))
	  {
	    $error++;
	    $mesgerror .= '<br>'.$langs->trans('Errorlabelrequired');
	  }
	if ($object->stipulation<=0)
	  {
	    $error++;
	    $mesgerror .= '<br>'.$langs->trans('Errorstipulationrequired');
	  }
	if ($object->locked<=0)
	  {
	    $error++;
	    $mesgerror .= '<br>'.$langs->trans('Errorlockedrequired');
	  }
	
	if ( $object->update($_POST["id"], $user) > 0 && empty($error))
	  {
	    $action = '';
	    $_GET["id"] = $_POST["id"];
	    $mesg = '<div class="ok">'.$langs->trans('Updated record').'</div>';
	  }
	else
	  {
	    $action = 'edit';
	    $_GET["id"] = $_POST["id"];
	    if ($error)
	      $object->error = $mesgerror;
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

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

if ($action == 'create' && $user->rights->salary->cc->creer)
  {
    print_fiche_titre($langs->trans("Newcostcenter"));
  
    print "<form action=\"fiche.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    
    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    // ref
    print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
    print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="7" maxlength="9">';
    print '</td></tr>';
    
    // label
    print '<tr><td class="fieldrequired">'.$langs->trans('Label').'</td><td colspan="2">';
    print '<input id="label" type="text" value="'.$object->label.'" name="label" size="30" maxlength="40">';
    print '</td></tr>';

    // cc sup
    print '<tr><td>'.$langs->trans('Sup').'</td><td colspan="2">';
    print $object->select_cc($object->fk_cc_sup,'fk_cc_sup','','',1);
    print '</td></tr>';

    // stipulation
    print '<tr><td class="fieldrequired">'.$langs->trans('Condition').'</td><td colspan="2">';
    print select_cta_normal($object->stipulation,'stipulation','','',1);
    print '</td></tr>';

    // locked
    print '<tr><td class="fieldrequired">'.$langs->trans('Locked').'</td><td colspan="2">';
    print select_yesno($object->locked,'locked','','',1);
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
	  
	  dol_fiche_head($head, 'Costcenter', $langs->trans("Costcenter"), 0, 'proces');
	  
	  /*
	   * Confirmation de la validation
	   */
	  if ($action == 'validate')
	    {
	      $object->fetch(GETPOST('id'));
	      //cambiando a validado
	      $object->state = 1;
	      //update
	      $object->update($user);
	      $action = '';
	      //header("Location: fiche.php?id=".$_GET['id']);
	      
	    }

	  /*
	   * Confirmation de la validation
	   */
	  if ($action == 'revalidate')
	    {
	      $object->fetch(GETPOST('id'));
	      //cambiando a validado
	      $object->state = 0;
	      //update
	      $object->update($user);
	      $action = '';
	      //header("Location: fiche.php?id=".$_GET['id']);
	      
	    }
	  
	  // Confirm delete third party
	  if ($action == 'delete')
	    {
	      $form = new Form($db);
	      $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deletecostcenter"),$langs->trans("Confirmdeletecostcenter",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
	      if ($ret == 'html') print '<br>';
	    }
	  
	  print '<table class="border" width="100%">';

	  // ref
	  print '<tr><td>'.$langs->trans('Ref').'</td><td colspan="2">';
	  print $object->ref;
	  print '</td></tr>';
	  
	  // label
	  print '<tr><td>'.$langs->trans('Label').'</td><td colspan="2">';
	  print $object->label;
	  print '</td></tr>';
	  
	  // cc sup
	  print '<tr><td>'.$langs->trans('Sup').'</td><td colspan="2">';

	  $objects = new Pcentrocosto($db);
	  $objects->fetch($object->fk_cc_sup);
	  if ($objects->id == $object->fk_cc_sup)
	    print $objects->label;
	  else
	    print '&nbsp;';
	  print '</td></tr>';
	  
	  // stipulation
	  print '<tr><td>'.$langs->trans('Condition').'</td><td colspan="2">';
	  print select_cta_normal($object->stipulation,'stipulation','','',1,1);
	  print '</td></tr>';
	  
	  // locked
	  print '<tr><td>'.$langs->trans('Locked').'</td><td colspan="2">';
	  print select_yesno($object->locked,'locked','','',1,1);
	  print '</td></tr>';


	  // state
	  print '<tr><td>'.$langs->trans('Status').'</td><td colspan="2">';
	  print libState($object->state,5);
	  print '</td></tr>';
	  
	  print '</table>';
	  
	  print '</div>';
	  
	  
	  /* ************************************************************************** */
	  /*                                                                            */
	  /* Barre d'action                                                             */
	  /*                                                                            */
	  /* ************************************************************************** */
	  
	  print "<div class=\"tabsAction\">\n";
	  
	  if ($action == '')
	    {
	      if ($user->rights->salary->cc->creer)
		print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

	      if ($user->rights->salary->cc->creer && $object->state==0)
		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

	      if ($user->rights->salary->cc->val && $object->state == 0)
		print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Valid")."</a>";
	      elseif($user->rights->salary->cc->val && $object->state == 1)
		print "<a class=\"butAction\" href=\"fiche.php?action=revalidate&id=".$object->id."\">".$langs->trans("Change")."</a>";

	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Valid")."</a>";
	      
	      if ($user->rights->salary->cc->del  && $object->state==0)
		print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
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
	  //print_fiche_titre($langs->trans("ApplicationsEdit"),$mesg);

	  print_fiche_titre($langs->trans("ApplicationsEdit"));
	  
	  print '<form action="fiche.php" method="POST">';
	  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	  print '<input type="hidden" name="action" value="update">';
	  print '<input type="hidden" name="id" value="'.$object->id.'">';
	  
	  print '<table class="border" width="100%">';

	  // ref
	  print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	  print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="7" maxlength="9">';
	  print '</td></tr>';
	  
	  // label
	  print '<tr><td class="fieldrequired">'.$langs->trans('Label').'</td><td colspan="2">';
	  print '<input id="label" type="text" value="'.$object->label.'" name="label" size="30" maxlength="40">';
	  print '</td></tr>';
	  
	  // cc sup
	  print '<tr><td>'.$langs->trans('Sup').'</td><td colspan="2">';
	  print $object->select_cc($object->fk_cc_sup,'fk_cc_sup','','',1);
	  print '</td></tr>';
	  
	  // stipulation
	  print '<tr><td class="fieldrequired">'.$langs->trans('Condition').'</td><td colspan="2">';
	  print select_cta_normal($object->stipulation,'stipulation','','',1);
	  print '</td></tr>';
	  
	  // locked
	  print '<tr><td class="fieldrequired">'.$langs->trans('Locked').'</td><td colspan="2">';
	  print select_yesno($object->locked,'locked','','',1);
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
