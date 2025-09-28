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
 *	\file       htdocs/mant/equipment/fiche.php
 *	\ingroup    Equipment
 *	\brief      Page fiche mant equipment
 */

require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/mant/equipment/class/mequipment.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';

// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
// require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

// require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("mant");

$action=GETPOST('action');

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';

$object = new Mequipment($db);
$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->mant->teacher->crear)
  {
    $object->initAsSpecimen();
    $object->ref          = $_POST["ref"];
    $object->entity       = $conf->entity;

    $object->ref_ext    = $_POST["ref_ext"];
    $object->nom    = $_POST["nomact"];
    $object->trademark    = $_POST["trademark"];
    $object->model    = $_POST["model"];
    $object->anio    = $_POST["anio"];
    $object->fk_location    = $_POST["fk_location"];
    $object->tms    = date('YmdHis');
    $object->statut    = 0;
    if ($object->ref && !empty($object->nom)) 
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
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->mant->teacher->del)
{
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/mant/equipment/liste.php');
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
	$object->ref          = $_POST["ref"];
	$object->ref_ext      = $_POST["ref_ext"];
	$object->nom          = GETPOST('nomact','alpha',2);
	$object->trademark    = $_POST["trademark"];
	$object->model        = $_POST["model"];
	$object->anio         = GETPOST('anio','int')+0;
	$object->fk_location  = $_POST["fk_location"];
	$object->tms          = date('YmdHis');
	if (!empty($object->nom))
	  {
	    if ( $object->update($user) > 0)
	      {
		$action = '';
		$_GET["id"] = $_POST["id"];
		$mesg = '<div class="ok">'.$langs->trans('The update was performed correctly').'</div>';
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
	    $mesg = '<div class="error">'.$langs->trans('Errornomerequired').'</div>';
	    $action = 'edit';
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

if ( ($action == 'createedit') )
  {
    require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
    //$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
    $tmparray['fk_property'] = GETPOST('fk_property');
    $tmparray['action'] = GETPOST('actionant');
    $tmparray['ref'] = GETPOST('ref');
    $tmparray['ref_ext'] = GETPOST('ref_ext');
    $tmparray['nom'] = GETPOST('nomact');
    $tmparray['trademark'] = GETPOST('trademark');
    $tmparray['model'] = GETPOST('model');
    $tmparray['anio'] = GETPOST('anio')+0;

    if (! empty($tmparray['fk_property']))
      {
	$fk_property = $tmparray['fk_property'];
	$object->ref = $tmparray['ref'];
	$object->ref_ext = $tmparray['ref_ext'];
	$object->nom = $tmparray['nom'];
	$object->trademark = $tmparray['trademark'];
	$object->model = $tmparray['model'];
	$object->anio = $tmparray['anio'];
	$action='create';
      }
  }


/*
 * View
 */

$form=new Form($db);

$help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
llxHeader("",$langs->trans("Managementmant"),$help_url);

if ($action == 'create' && $user->rights->mant->teacher->crear)
  {
    print_fiche_titre($langs->trans("Newequipment"));

    print "\n".'<script type="text/javascript" language="javascript">';
    print '$(document).ready(function () {
              $("#selectfk_property").change(function() {
                document.form_index.action.value="createedit";
                document.form_index.submit();
              });
          });';
    print '</script>'."\n";
  
    print '<form action="fiche.php" method="post" name="form_index">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="actionant" value="create">';
    
    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    // ref
    print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
    print '<input id="ref" type="text" value="(PROV)" name="ref" size="33" maxlength="30">';
    print '</td></tr>';
    // ref_ext
    print '<tr><td class="fieldrequired">'.$langs->trans('Refext').'</td><td colspan="2">';
    print '<input id="ref_ext" type="text" value="'.$object->ref.'" name="ref_ext" size="33" maxlength="30">';
    print '</td></tr>';

    // name
    print '<tr><td class="fieldrequired">'.$langs->trans('Name').'</td><td colspan="2">';
    print '<input id="nomact" type="text" value="'.$object->nom.'" name="nomact" size="50" maxlength="255">';
    print '</td></tr>';

    // trademark
    print '<tr><td class="fieldrequired">'.$langs->trans('Trademark').'</td><td colspan="2">';
    print '<input id="trademark" type="text" value="'.$object->trademark.'" name="trademark" size="50" maxlength="150">';
    print '</td></tr>';
    // model
    print '<tr><td class="fieldrequired">'.$langs->trans('Model').'</td><td colspan="2">';
    print '<input id="model" type="text" value="'.$object->model.'" name="nom" size="50" maxlength="150">';
    print '</td></tr>';

    // anio
    print '<tr><td class="fieldrequired">'.$langs->trans('Year').'</td><td colspan="2">';
    print '<input id="anio" type="text" value="'.$object->anio.'" name="anio" size="6" maxlength="4">';
    print '</td></tr>';

    // property
    print '<tr><td class="fieldrequired">'.$langs->trans('Property').'</td><td colspan="2">';
    print $objProperty->select_property($fk_property,'fk_property','',40,1);
    print '</td></tr>';
    
    // location
    print '<tr><td class="fieldrequired">'.$langs->trans('Location').'</td><td colspan="2">';
    print $objLocation->select_location($object->fk_location,'fk_location','',40,1,$fk_property);
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
      
      if ( ($action == 'updateedit') )
	{
	  require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	  //$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
	  $tmparray['id'] = GETPOST('id');
	  $tmparray['fk_property'] = GETPOST('fk_property');
	  $tmparray['action'] = GETPOST('actionant');
	  $tmparray['ref'] = GETPOST('ref');
	  $tmparray['ref_ext'] = GETPOST('ref_ext');
	  $tmparray['nom'] = GETPOST('nomact');
	  $tmparray['trademark'] = GETPOST('trademark');
	  $tmparray['model'] = GETPOST('model');
	  $tmparray['anio'] = GETPOST('anio');
	  
	  if (! empty($tmparray['id']))
	    {
	      $fk_property = $tmparray['fk_property'];
	      $object->ref = $tmparray['ref'];
	      $object->ref_ext = $tmparray['ref_ext'];
	      $object->nom = $tmparray['nom'];
	      $object->trademark = $tmparray['trademark'];
	      $object->model = $tmparray['model'];
	      $object->anio = $tmparray['anio'];
	    }
	  $action='edit';
	}
      
      /*
       * Affichage fiche
       */
      if ($action <> 'edit' && $action <> 're-edit')
	{
	  $head = equipment_prepare_head($object);
	  dol_fiche_head($head, 'card', $langs->trans("Equipment"), 0, 'mant');
	  
	  /*
	   * Confirmation de la validation
	   */

	  if ($action == 'validate')
	    {
	      // on verifie si l'objet est en numerotation provisoire
	      $ref = substr($object->ref, 1, 4);
	      if ($ref == 'PROV')
		{
		  $numref = $object->getNextNumRef($soc);
		}
	      else
		{
		  $numref = $object->ref;
		}
	      
	      //$object = new Solalmacen($db);
	      $object->fetch(GETPOST('id'));
	      //cambiando a validado
	      $object->ref = $numref;

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
	      $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteperiodaccounting"),$langs->trans("Confirmdeleteperiodaccounting",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
	      if ($ret == 'html') print '<br>';
	    }
	  
	  print '<table class="border" width="100%">';

	  // ref
	  print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';
	  $linkback = '<a href="'.DOL_URL_ROOT.'/mant/equipment/liste.php">'.$langs->trans("BackToList").'</a>';

	  print '<td class="valeur"  colspan="2">';
	  //print $form->showrefnav($object, 'socid', $linkback, ($user->societe_id?0:1), 'rowid', 'ref');
	  //print $form->showrefnav($object, 'id', $linkback,1,'rowid','regid');
	  print $form->showrefnav($object, 'id', $linkback,1,'rowid');

	  print '</td></tr>';

	  // ref_ext
	  print '<tr><td>'.$langs->trans('Refext').'</td><td colspan="2">';
	  print $object->ref_ext;
	  print '</td></tr>';
	  
	  // name
	  print '<tr><td>'.$langs->trans('Name').'</td><td colspan="2">';
	  print $object->nom;
	  print '</td></tr>';
	  
	  // trademark
	  print '<tr><td>'.$langs->trans('Trademark').'</td><td colspan="2">';
	  print $object->trademark;
	  print '</td></tr>';
	  // model
	  print '<tr><td>'.$langs->trans('Model').'</td><td colspan="2">';
	  print $object->model;
	  print '</td></tr>';
	  
	  // anio
	  print '<tr><td>'.$langs->trans('Year').'</td><td colspan="2">';
	  print $object->anio;
	  print '</td></tr>';
	  
  
	  // location
	  if ($objLocation->fetch($object->fk_location))
	    {
	      print '<tr><td>'.$langs->trans('Location').'</td><td colspan="2">';
	      print $objLocation->detail;
	      print '</td></tr>';	  
	      if ($objProperty->fetch($objLocation->fk_property))
		{
		  print '<tr><td>'.$langs->trans('Property').'</td><td colspan="2">';
		  print $objProperty->ref;
		  print '</td></tr>';	  
		}
	    }
	  else
	    {
	      print '<tr><td class="fieldrequired">'.$langs->trans('Location').'</td><td colspan="2">';
	      print '&nbsp;';
	      print '</td></tr>';	  

	    }

	  // STATUS
	  print '<tr><td>'.$langs->trans('Status').'</td><td colspan="2">';
	  print $object->getLibStatut();
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

	      if ($user->rights->mant->teacher->val && $object->statut == 0)
		print "<a class=\"butActionDelete\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Validate")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";
	      
	      if ($user->rights->mant->teacher->del  && $object->statut == 0)
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
	  print_fiche_titre($langs->trans("Editingequipment"), $mesg);

	  print "\n".'<script type="text/javascript" language="javascript">';
	  print '$(document).ready(function () {
              $("#selectfk_property").change(function() {
                document.form_index.action.value="updateedit";
                document.form_index.submit();
              });
          });';
	  print '</script>'."\n";
	  
	  print '<form action="fiche.php" method="POST" name="form_index">';
	  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	  print '<input type="hidden" name="action" value="update">';
	  print '<input type="hidden" name="id" value="'.$object->id.'">';
	  
	  print '<table class="border" width="100%">';
	  

    // ref
    print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
    print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="33" maxlength="30">';
    print '</td></tr>';
    // ref_ext
    print '<tr><td>'.$langs->trans('Refext').'</td><td colspan="2">';
    print '<input id="ref_ext" type="text" value="'.$object->ref_ext.'" name="ref_ext" size="33" maxlength="30">';
    print '</td></tr>';

    // name
    print '<tr><td class="fieldrequired">'.$langs->trans('Name').'</td><td colspan="2">';
    print '<input id="nomact" type="text" value="'.$object->nom.'" name="nomact" size="50" maxlength="255">';
    print '</td></tr>';

    // trademark
    print '<tr><td>'.$langs->trans('Trademark').'</td><td colspan="2">';
    print '<input id="trademark" type="text" value="'.$object->trademark.'" name="trademark" size="50" maxlength="150">';
    print '</td></tr>';
    // model
    print '<tr><td>'.$langs->trans('Model').'</td><td colspan="2">';
    print '<input id="model" type="text" value="'.$object->model.'" name="nom" size="50" maxlength="150">';
    print '</td></tr>';

    // anio
    print '<tr><td>'.$langs->trans('Year').'</td><td colspan="2">';
    print '<input id="anio" type="text" value="'.$object->anio.'" name="anio" size="6" maxlength="4">';
    print '</td></tr>';

    // property
    $objLocation->fetch($object->fk_location);
    if (empty($fk_property)) 
      $fk_property = $objLocation->fk_property;

    print '<tr><td>'.$langs->trans('Property').'</td><td colspan="2">';
    print $objProperty->select_property($fk_property,'fk_property','',40,1);
    print '</td></tr>';
    
    // location
    print '<tr><td>'.$langs->trans('Location').'</td><td colspan="2">';
    print $objLocation->select_location($object->fk_location,'fk_location','',40,1,$fk_property);
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
