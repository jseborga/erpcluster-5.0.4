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
 *	\file       htdocs/salary/concept/fiche.php
 *	\ingroup    Concept
 *	\brief      Page fiche salary concept
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/ptypefolext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pconceptext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pformulas.class.php';
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
$mesgerror = '';
$error = '';

$object  = new Pconceptext($db);
$objectT = new Ptypefolext($db);
$objectF = new Pformulas($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->salary->crearconcept)
  {
    $object->ref        = $_POST["ref"];
    $object->detail     = GETPOST('detail');
    $object->details    = GETPOST('details');
    $object->type_cod   = GETPOST('type_cod');
    $object->type_mov   = GETPOST('type_mov');
    $object->fk_formula = GETPOST('fk_formula');
    $object->print      = GETPOST('print');
    $object->fk_codfol  = GETPOST('fk_codfol');
    $object->income_tax = GETPOST('income_tax');
    $object->percent    = GETPOST('percent') + 0;

    if (empty($object->detail))
      {
	$error++;
	$mesgerror.= '<br>'.$langs->trans('Errordetailrequired');
      }
    if ($object->type_cod<=0)
      {
	$error++;
	$mesgerror.= '<br>'.$langs->trans('Errortypecodrequired');
      }
    if ($object->print<=0)
      {
	$error++;
	$mesgerror.= '<br>'.$langs->trans('Errorprintrequired');
      }
    if ($object->fk_codfol<=0)
      {
	$error++;
	$mesgerror.= '<br>'.$langs->trans('Errortypefolrequired');
      }
    if ($object->type_mov<=0)
      {
	$error++;
	$mesgerror.= '<br>'.$langs->trans('Errortypemovrequired');
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
	if ($error)
	  $mesg='<div class="error">'.$mesgerror.'</div>';
	else
	  $mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
	$action="create";   // Force retour sur page creation
      }
  }


// Delete concept
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salary->deldpto)
{
  $object->fetch($id);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/salary/charge/liste.php');
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
	$object->ref        = $_POST["ref"];
	$object->detail     = GETPOST('detail');
	$object->details    = GETPOST('details');
	$object->type_cod   = GETPOST('type_cod');
	$object->type_mov   = GETPOST('type_mov');
	$object->fk_formula = GETPOST('fk_formula');
	$object->print      = GETPOST('print');
	$object->fk_codfol  = GETPOST('fk_codfol');
	$object->income_tax = GETPOST('income_tax');
	$object->percent    = GETPOST('percent') + 0;
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

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

if ($action == 'create' && $user->rights->salary->creardpto)
  {
    print_fiche_titre($langs->trans("Newconcept"));

    print "<form action=\"fiche.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';

    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    // ref
    print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
    print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="3" maxlength="3">';
    print '</td></tr>';
    // detail
    print '<tr><td class="fieldrequired">'.$langs->trans('Detail').'</td><td colspan="2">';
    print '<input id="detail" type="text" value="'.$object->detail.'" name="detail" size="30" maxlength="40">';
    print '</td></tr>';

    //details
    print '<tr><td>'.$langs->trans('Details').'</td><td colspan="2">';
    print '<textarea class="flat" name="details" id="details" cols="40" rows="'.ROWS_3.'">';
    print $object->details;
    print '</textarea>';
    print '</td></tr>';

    //tipocod
    print '<tr><td class="fieldrequired">'.$langs->trans('Typecod').'</td><td colspan="2">';
    print select_typecod($object->type_cod,'type_cod','','',1);
    print '</td></tr>';

    //print
    print '<tr><td class="fieldrequired">'.$langs->trans('Print').'</td><td colspan="2">';
    print select_yesno($object->print,'print','','',1);
    print '</td></tr>';

    //type fol
    print '<tr><td class="fieldrequired">'.$langs->trans('Typefol').'</td><td colspan="2">';
    print $objectT->select_typefol($object->fk_codfol,'fk_codfol','','',1);
    print '</td></tr>';

    //tipomov
    print '<tr><td class="fieldrequired">'.$langs->trans('Typemov').'</td><td colspan="2">';
    print select_typemov($object->type_mov,'type_mov','','',1);
    print '</td></tr>';

   //formula
    print '<tr><td>'.$langs->trans('Formula').'</td><td colspan="2">';
    print $objectF->select_formula($object->fk_formula,'fk_formula','','',1);
    print '</td></tr>';

    //income tax
    print '<tr><td>'.$langs->trans('Incometax').'</td><td colspan="2">';
    print select_incometax($object->income_tax,'income_tax','','',1);
    print '</td></tr>';
    // percent
    print '<tr><td>'.$langs->trans('Percent').'</td><td colspan="2">';
    print '<input id="percent" type="text" value="'.$object->percent.'" name="percent" size="5" maxlength="15">';
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
	  $head = concept_prepare_head($object);

	  dol_fiche_head($head, 'card', $langs->trans("Concept"), 0, 'salary');

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

	  // Confirm delete third party
	  if ($action == 'delete')
	    {
	      $form = new Form($db);
	      $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteperiodaccounting"),$langs->trans("Confirmdeleteperiodaccounting",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
	      if ($ret == 'html') print '<br>';
	    }
	  print '<table class="border" width="100%">';

	  // ref
	  print '<tr><td>'.$langs->trans('Ref').'</td><td colspan="2">';
	  print $object->ref;
	  print '</td></tr>';
	  // detail
	  print '<tr><td>'.$langs->trans('Detail').'</td><td colspan="2">';
	  print $object->detail;
	  print '</td></tr>';

	  //details
	  print '<tr><td>'.$langs->trans('Details').'</td><td colspan="2">';
	  print '<textarea class="flat" name="details" id="details" cols="40" rows="'.ROWS_3.'" disabled="disabled">';
	  print $object->details;
	  print '</textarea>';
	  print '</td></tr>';

	  //tipocod
	  print '<tr><td>'.$langs->trans('Typecod').'</td><td colspan="2">';
	  print select_typecod($object->type_cod,'type_cod','','',1,1);
	  print '</td></tr>';

	  //print
	  print '<tr><td>'.$langs->trans('Print').'</td><td colspan="2">';
	  print select_yesno($object->print,'print','','',1,1);
	  print '</td></tr>';

	  //type fol
	    print '<tr><td>'.$langs->trans('Typefol').'</td><td colspan="2">';
	  If ($objectT->fetch($object->fk_codfol))
	    print $objectT->ref;
	  else
	    print "";
	  print '</td></tr>';

	  //tipomov
	  print '<tr><td>'.$langs->trans('Typemov').'</td><td colspan="2">';
	  print select_typemov($object->type_mov,'type_mov','','',1,1);
	  print '</td></tr>';

	  //formula
	  $objectF->fetch($object->fk_formula);
	  print '<tr><td>'.$langs->trans('Formula').'</td><td colspan="2">';
	  if ($objectF->id == $object->fk_formula)
	    print $objectF->ref.' '.$objectF->detail;
	  else
	    print '';
	  print '</td></tr>';

	  //income tax
	  print '<tr><td>'.$langs->trans('Incometax').'</td><td colspan="2">';
	  print select_incometax($object->income_tax,'income_tax','','',1,1);
	  print '</td></tr>';
	  // percent
	  print '<tr><td>'.$langs->trans('Percent').'</td><td colspan="2">';
	  print $object->percent;
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
	      if ($user->rights->salary->crearconcept)
		print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

	      if ($user->rights->salary->crearconcept)
		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

	      if ($user->rights->salary->delconcept)
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


	  // ref
	  print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	  print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="3" maxlength="3">';
	  print '</td></tr>';
	  // detail
	  print '<tr><td class="fieldrequired">'.$langs->trans('Detail').'</td><td colspan="2">';
	  print '<input id="detail" type="text" value="'.$object->detail.'" name="detail" size="30" maxlength="40">';
	  print '</td></tr>';

	  //details
	  print '<tr><td>'.$langs->trans('Details').'</td><td colspan="2">';
	  print '<textarea class="flat" name="details" id="details" cols="40" rows="'.ROWS_3.'">';
	  print $object->details;
	  print '</textarea>';
	  print '</td></tr>';

	  //tipocod
	  print '<tr><td class="fieldrequired">'.$langs->trans('Typecod').'</td><td colspan="2">';
	  print select_typecod($object->type_cod,'type_cod','','',1);
	  print '</td></tr>';

	  //print
	  print '<tr><td class="fieldrequired">'.$langs->trans('Print').'</td><td colspan="2">';
	  print select_yesno($object->print,'print','','',1);
	  print '</td></tr>';

	  //type fol
	  print '<tr><td class="fieldrequired">'.$langs->trans('Typefol').'</td><td colspan="2">';
	  print $objectT->select_typefol($object->fk_codfol,'fk_codfol','','',1);
	  print '</td></tr>';

	  //tipomov
	  print '<tr><td class="fieldrequired">'.$langs->trans('Typemov').'</td><td colspan="2">';
	  print select_typemov($object->type_mov,'type_mov','','',1);
	  print '</td></tr>';
	  //formula
	  print '<tr><td>'.$langs->trans('Formula').'</td><td colspan="2">';
	  print $objectF->select_formula($object->fk_formula,'fk_formula','','',1);
	  print '</td></tr>';

	  //income tax
	  print '<tr><td>'.$langs->trans('Incometax').'</td><td colspan="2">';
	  print select_incometax($object->income_tax,'income_tax','','',1);
	  print '</td></tr>';
	  // percent
	  print '<tr><td class="fieldrequired">'.$langs->trans('Percent').'</td><td colspan="2">';
	  print '<input id="percent" type="text" value="'.$object->percent.'" name="percent" size="5" maxlength="15">';
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
