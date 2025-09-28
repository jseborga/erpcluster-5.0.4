<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---2013 Ramiro Queso ramiro@ubuntu-bo.com---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
*   	\file       dev/Pregional/Pregional_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2013-09-06 20:51
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';
if (! $res && file_exists("../../../main.inc.php")) $res=@include '../../../main.inc.php';
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
dol_include_once('/salary/class/pregionalext.class.php');
dol_include_once('/salary/lib/salary.lib.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("salary@salary");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');

$object = new Pregionalext($db);

$error = 0;
$mesgerror = '';
// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if ($action == 'add' && $user->rights->salary->region->creer)
  {
    $object->ref    = $_POST["ref"];
    $object->label  = $_POST["label"];
    $object->entity = $conf->entity;
    $object->state  = 0;
    if (empty($object->ref))
      {
	$error++;
	$mesgerror.='<br>'.$langs->trans('Errorrefrequired');
	$action = 'create';
      }
    if (empty($object->label))
      {
	$error++;
	$mesgerror.='<br>'.$langs->trans('Errorlabelrequired');
	$action = 'create';
      }
    if (empty($error))
      {
	$id=$object->create($user);
	if ($id > 0)
	  {
	    header("Location: fiche.php?id=".$id);
	    exit;
	    // Creation OK
	  }
	else
	  {
	    // Creation KO
	    $mesg=$object->error;
	    $action="create";
	  }
      }
    else
      {
	$mesg='<div class="error">'.$mesgerror.'</div>';
	$action = 'create';
      }
  }
//update
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
  {
    $object->fetch($_REQUEST["id"]);
    $object->ref    = $_POST["ref"];
    $object->label  = $_POST["label"];
    $object->entity = $conf->entity;
    $object->state  = 0;
    if (empty($object->ref))
      {
	$error++;
	$mesgerror.='<br>'.$langs->trans('Errorrefrequired');
	$action = 'create';
      }
    if (empty($object->label))
      {
	$error++;
	$mesgerror.='<br>'.$langs->trans('Errorlabelrequired');
	$action = 'create';
      }
    if (empty($error))
      {
	$result=$object->update($user);
	if ($result > 0)
	  {
	    header("Location: fiche.php?id=".$id);
	    exit;
	    // Creation OK
	  }
	else
	  {
	    // Creation KO
	    $mesg=$object->error;
	    $action="edit";
	  }
      }
    else
      {
	$mesg='<div class="error">'.$mesgerror.'</div>';
	$action = 'edit';
      }
  }


// Delete
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salary->region->del)
  {
    $object->fetch($_REQUEST["id"]);
    $result=$object->delete($user);
    if ($result > 0)
      {
	header("Location: ".DOL_URL_ROOT.'/salary/regional/liste.php');
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


/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

$form=new Form($db);

if ($action == 'create' && $user->rights->salary->region->creer)
  {
    print_fiche_titre($langs->trans("Newregion"));

    print "<form action=\"fiche.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';

    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    // ref
    print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
    print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="8" maxlength="10">';
    print '</td></tr>';

    // label
    print '<tr><td class="fieldrequired">'.$langs->trans('Label').'</td><td colspan="2">';
    print '<input id="label" type="text" value="'.$object->label.'" name="label" size="38" maxlength="40">';
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

	  dol_fiche_head($head, 'Regional', $langs->trans("Regional"), 0, 'generic');

	  /*
	   * Confirmation de la validation
	   */
	  if ($action == 'validate')
	    {
	      $object->fetch(GETPOST('id'));
	      //cambiando a validado
	      $object->state = 1;
	      $object->ref = $object->codref;
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
	      $object->ref = $object->codref;
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
	      $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteregional"),$langs->trans("Confirmdeleteregional",$object->ref.' '.$object->label),"confirm_delete",'',0,2);
	      if ($ret == 'html') print '<br>';
	    }

	  print '<table class="border" width="100%">';

	  // ref
	  print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';
	  $linkback = '<a href="'.DOL_URL_ROOT.'/salary/regional/liste.php">'.$langs->trans("BackToList").'</a>';

	  print '<td class="valeur"  colspan="2">';
	  print $form->showrefnav($object, 'id', $linkback);
	  print '</td></tr>';

	  print '<tr><td>'.$langs->trans('Name').'</td><td colspan="2">';
	  print $object->codref;
	  print '</td></tr>';

	  // label
	  print '<tr><td>'.$langs->trans('Label').'</td><td colspan="2">';
	  print $object->label;
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
	      if ($user->rights->salary->region->creer)
		print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

	      if ($user->rights->salary->region->creer && $object->state==0)
		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

	      if ($user->rights->salary->region->val && $object->state == 0)
		print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Valid")."</a>";
	      elseif($user->rights->salary->region->val && $object->state == 1)
		print "<a class=\"butAction\" href=\"fiche.php?action=revalidate&id=".$object->id."\">".$langs->trans("Change")."</a>";

	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Valid")."</a>";

	      if ($user->rights->salary->region->del  && $object->state==0)
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
	  print '<input id="ref" type="text" value="'.$object->codref.'" name="ref" size="8" maxlength="10">';
	  print '</td></tr>';

	  // label
	  print '<tr><td class="fieldrequired">'.$langs->trans('Label').'</td><td colspan="2">';
	  print '<input id="label" type="text" value="'.$object->label.'" name="label" size="38" maxlength="40">';
	  print '</td></tr>';

	  print '</table>';

	  print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	  print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

	  print '</form>';

	}

	 /////
       }
   }

// End of page
llxFooter();
$db->close();
?>
