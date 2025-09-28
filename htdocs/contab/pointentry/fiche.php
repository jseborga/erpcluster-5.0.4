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
 *	\file       htdocs/contab/pointentry/fiche.php
 *	\ingroup    Puntos Asiento
 *	\brief      Page fiche contab point entry
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabpointentry.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/lib/contab.lib.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");

$langs->load("contab@contab");

$action=GETPOST('action');

$id        = GETPOST("rowid");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

$object = new Contabpointentry($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->contab->crearperiod)
  {
    $object = new Contabpointentry($db);
    $object->ref  = $_POST["ref"];
    $object->entity        = $conf->entity;
    $object->description   = $_POST["description"];
    $object->cfglan        = $_POST["cfglan"];
    if ($object->ref && $object->description) {
      $id = $object->create($user);
      if ($id > 0)
	{
	  header("Location: fiche.php?id=".$id);
	  exit;
	}
      $action = 'create';
      $mesg='<div class="error">'.$object->error.'</div>';
    }
    else {
      $mesg='<div class="error">'.$langs->trans("Errorrefdescriptionrequired").'</div>';
      $action="create";   // Force retour sur page creation
    }
  }



// Delete period
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->contab->delperiod)
{
  $object = new Contabpointentry($db);
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/contab/pointentry/liste.php');
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
    
    $object = new Contabpointentry($db);
    if ($object->fetch($_POST["id"]))
      {
	$object->ref         = $_POST["ref"];
	$object->description = $_POST["description"];
	$object->cfglan      = $_POST["cfglan"];
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

$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementaccounting"),$help_url);

if ($action == 'create' && $user->rights->contab->crearperiod)
{
  print_fiche_titre($langs->trans("Newentrypoint"));
  
  print "<form action=\"fiche.php\" method=\"post\">\n";
  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
  print '<input type="hidden" name="action" value="add">';
  
  dol_htmloutput_mesg($mesg);

  print '<table class="border" width="100%">';


  // ref
  print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
  print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="2" maxlength="3">';
  print '</td></tr>';
  // description
  print '<tr><td class="fieldrequired">'.$langs->trans('Description').'</td><td colspan="2">';
  print '<input id="description" type="text" value="'.$object->description.'" name="description" size="30" maxlength="120">';
  print '</td></tr>';

  //cfglan
  print '<tr><td class="fieldrequired">'.$langs->trans('Cfglan').'</td><td colspan="2">';
  print select_cfglan($object->cfglan,'cfglan','','',1);
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
      
      $object = new Contabpointentry($db);
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
	  
	  dol_fiche_head($head, 'card', $langs->trans("Accountingperiods"), 0, 'contab');
	  
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
	  print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	  print $object->ref;
	  print '</td></tr>';
	  // description
	  print '<tr><td class="fieldrequired">'.$langs->trans('Description').'</td><td colspan="2">';
	  print $object->description;
	  print '</td></tr>';

	  //cfglan
	  print '<tr><td class="fieldrequired">'.$langs->trans('Cfglan').'</td><td colspan="2">';
	  print select_cfglan($object->cfglan,'cfglan','','',1,1);
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
	      if ($user->rights->contab->crearpoint)
		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
	      
	      if ($user->rights->contab->delpoint)
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
	  print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="2" maxlength="3">';
	  print '</td></tr>';
	  // description
	  print '<tr><td class="fieldrequired">'.$langs->trans('Description').'</td><td colspan="2">';
	  print '<input id="description" type="text" value="'.$object->description.'" name="description" size="30" maxlength="120">';
	  print '</td></tr>';
	  
	  //cfglan
	  print '<tr><td class="fieldrequired">'.$langs->trans('Cfglan').'</td><td colspan="2">';
	  print select_cfglan($object->cfglan,'cfglan','','',1);
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
