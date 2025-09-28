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
 *	\file       htdocs/salary/proces/fiche.php
 *	\ingroup    Proces
 *	\brief      Page fiche salary proces
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/psalaryaprob.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pcharge.class.php");
require_once(DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php");
require_once DOL_DOCUMENT_ROOT.'/salary/lib/adherent.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/approver/lib/approver.lib.php';


require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';



$langs->load("salary@salary");

$action=GETPOST('action');

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

if (!$user->rights->salary->salapr->lire)
  accessforbidden();

$mesg = '';
$error = '';
$mesgerror = '';
$object   = new Psalaryaprob($db);
$objectch = new Pcharge($db);
$objectad = new Adherent($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->salary->salapr->creer)
  {
    $object->entity = $conf->entity;
    $object->type = GETPOST('type');
    $fk_adherent  = $_POST['fk_adherent'];
    $fk_charge    = $_POST['fk_charge'];
    If ($object->type == 1)
      $object->fk_value = $fk_adherent;
    If ($object->type == 2)
      $object->fk_value = $fk_charge;

    $object->fk_aprobsup = GETPOST('fk_aprobsup');
    If (empty($object->type))
      {
	$error++;
	$mesgerror .= '<br>'.$langs->trans('Errortyperequired');
      }
    If ($fk_adherent <=0 && $fk_charge <= 0)
      {
	$error++;
	$mesgerror .= '<br>'.$langs->trans('Errorchargeemployeerequired');
      }
    $object->state    = 0;

    if ($object->type && empty($error)) 
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
	  $mesg='<div class="error">'.$mesgerror.'</div>';
	else
	  $mesg='<div class="error">'.$langs->trans("Errortyperequired").'</div>';
	$action="create";   // Force retour sur page creation
      }
  }


// Delete approver
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salary->salapr->del)
{
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/salary/approver/liste.php');
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
	$object->entity = $conf->entity;
	$object->type = GETPOST('type');
	$fk_adherent  = $_POST['fk_adherent'];
	$fk_charge    = $_POST['fk_charge'];
	If ($object->type == 1)
	  $object->fk_value = $fk_adherent;
	If ($object->type == 2)
	  $object->fk_value = $fk_charge;
	
	$object->aprobsup = GETPOST('aprobsup');

	if ( $object->update($_POST["id"], $user) > 0)
	  {
	    $action = '';
	    $_GET["id"] = $_POST["id"];
	    $mesg = '<div class="ok">'.$langs->trans('Updated record').'</div>';
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

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

if ($action == 'create' && $user->rights->salary->salapr->creer)
  {
    print_fiche_titre($langs->trans("Newapprover"));
 
    print '<script language="javascript">
function comprobar()
{
   var type = document.formu.selecttype.value;

   if (type == 1)
   {
      document.getElementById("selectfk_adherent").disabled = false;
      document.getElementById("selectfk_charge").disabled = true;
      return true;
   }
   
   if (type == 2)
   {
      document.getElementById("selectfk_charge").disabled = false;
      document.getElementById("selectfk_adherent").disabled = true;
      return true;
   }
   
   return true;
}
</script>';

  
    print '<form action="fiche.php" method="post" name="formu">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    
    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    // type
    print '<tr><td class="fieldrequired">'.$langs->trans('Type').'</td><td colspan="2">';
    print select_typeapprov($object->type,'type','onclick="return comprobar()"','',1);
    print '</td></tr>';

    // Empleado
    print '<tr><td>'.$langs->trans('Employee').'</td><td colspan="2">';
    print select_adherent($object->fk_adherent,'fk_adherent','enabled','',1);
    print '</td></tr>';

    // Charge
    print '<tr><td>'.$langs->trans('Charge').'</td><td colspan="2">';
    print $objectch->select_charge($object->fk_charge,'fk_charge','enabled','',1);
    print '</td></tr>';

    //superior
    print '<tr><td>'.$langs->trans('Superior').'</td><td colspan="2">';
    print select_aprob($object->fk_aprobsup,'fk_aprobsup','',0,1);
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
	  
	  dol_fiche_head($head, 'approver', $langs->trans("Approver"), 0, 'generic');
	  
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
	      $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteapprover"),$langs->trans("Confirmdeleteapprover",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
	      if ($ret == 'html') print '<br>';
	    }
	  
	  print '<table class="border" width="100%">';

	  $linkback = '<a href="'.DOL_URL_ROOT.'/salary/approver/liste.php">'.$langs->trans("BackToList").'</a>';

	  print '<td class="valeur"  colspan="2">';
	  print $form->showrefnav($object, 'id', $linkback);
	  print '</td></tr>';

	  
	  // type
	  print '<tr><td width="20%">'.$langs->trans('Type').'</td><td colspan="2">';
	  print select_typeapprov($object->type,'type','','',1,1);
	  print '</td></tr>';
	  
	  If ($object->type == 1)
	    {
	      // Empleado
	      print '<tr><td>'.$langs->trans('Employee').'</td><td colspan="2">';
	      $objectad->fetch($object->fk_value);
	      if ($objectad->id == $object->fk_value)
		print $objetad->lastname.' '.$objectad->firstname;
	      else
		print '&nbsp;';
	      print '</td></tr>';
	    }

	  If ($object->type == 2)
	    {
	      // Empleado
	      print '<tr><td>'.$langs->trans('Charge').'</td><td colspan="2">';
	      $objectch->fetch($object->fk_value);
	      if ($objectch->id == $object->fk_value)
		print $objectch->codref;
	      else
		print '&nbsp;';
	      print '</td></tr>';
	    }

	  $objectsup = new Psalaryaprob($db);
	  $objectsup->fetch($object->fk_aprobsup);
	  // Superior
	  If ($objectsup->id == $object->fk_aprobsup)
	    {
	      If ($objectsup->type == 1)
		{
		  // Empleado
		  print '<tr><td>'.$langs->trans('Superioremployee').'</td><td colspan="2">';
		  $objectad->fetch($object->fk_value);
		  if ($objectad->id == $objectsup->fk_value)
		    print $objetad->lastname.' '.$objectad->firstname;
		  else
		    print '&nbsp;';
		  print '</td></tr>';
		}

	      If ($objectsup->type == 2)
		{
		  // Empleado
		  print '<tr><td>'.$langs->trans('Superiorcharge').'</td><td colspan="2">';
		  $objectch->fetch($objectsup->fk_value);
		  if ($objectch->id == $object->fk_value)
		    print $objectch->codref;
		  else
		    print '&nbsp;';
		  print '</td></tr>';
		}
	    }
	  else
	    {
	      print '<tr><td>'.$langs->trans('Superior').'</td><td colspan="2">';
	      print '&nbsp;';
	      print '</td></tr>';
	    }
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
	      if ($user->rights->salary->salapr->creer)
	      	print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	      else
	      	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

	      if ($user->rights->salary->salapr->creer && $object->state==0)
		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

	      if ($user->rights->salary->salapr->val && $object->state == 0)
		print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Valid")."</a>";
	      elseif($user->rights->salary->salapr->val && $object->state == 1)
		print "<a class=\"butAction\" href=\"fiche.php?action=revalidate&id=".$object->id."\">".$langs->trans("Change")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Valid")."</a>";
	      
	      if ($user->rights->salary->salapr->del  && $object->state==0)
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
	  print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);
	  
	  print '<form action="fiche.php" method="POST">';
	  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	  print '<input type="hidden" name="action" value="update">';
	  print '<input type="hidden" name="id" value="'.$object->id.'">';
	  
	  print '<table class="border" width="100%">';

	  // type
	  print '<tr><td class="fieldrequired">'.$langs->trans('Type').'</td><td colspan="2">';
	  print select_typeapprov($object->type,'type','','',1);
	  print '</td></tr>';
	  If ($object->type == 1)
	    $object->fk_adherent = $object->fk_value;
	  If ($object->type == 2)
	    $object->fk_charge = $object->fk_value;

	  // Empleado
	  print '<tr><td>'.$langs->trans('Employee').'</td><td colspan="2">';
	  print select_adherent($object->fk_adherent,'fk_adherent','','',1);
	  print '</td></tr>';
	  
	  // Charge
	  print '<tr><td>'.$langs->trans('Charge').'</td><td colspan="2">';
	  print $objectch->select_charge($object->fk_charge,'fk_charge','','',1);
	  print '</td></tr>';

	  //superior
	  print '<tr><td>'.$langs->trans('Superior').'</td><td colspan="2">';
	  print select_aprob($object->fk_aprobsup,'fk_aprobsup','',0,1);
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
