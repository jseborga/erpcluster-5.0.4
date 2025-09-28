<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *	\file       htdocs/fabrication/fiche.php
 *	\ingroup    fabrication
 *	\brief      Page fiche fabrication
 */

require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once(DOL_DOCUMENT_ROOT."/fabrication/units/class/units.class.php");

$langs->load("fabrication@fabrication");

$action=GETPOST('action');

$warehouseid = GETPOST("warehouseid");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
if (! $sortfield) $sortfield="u.ref";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

$object = new Units($db);

/*
 * Actions
 */

// Ajout entrepot
if ($action == 'add' && $user->rights->fabrication->crearunidad)
  {
    $object = new Units($db);
    $object->ref           = GETPOST("ref");
    $object->description   = GETPOST("description");

    if ($object->ref) {
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
      $mesg='<div class="error">'.$langs->trans("ErrorRefRequired").'</div>';
      $action="create";   // Force retour sur page creation
    }
  }

// Delete warehouse
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->fabrication->delunidad)
  {
    $object = new Units($db);
    $object->fetch($_REQUEST["id"]);
    $result=$object->delete($user);
    if ($result > 0)
      {
	header("Location: ".DOL_URL_ROOT.'/fabrication/units/liste.php');
	exit;
      }
    else
      {
	$mesg='<div class="error">'.$object->error.'</div>';
	$action='';
      }
  }

// Modification units
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
  {
    $object = new Units($db);
    if ($object->fetch($_POST["id"]))
      {
	$object->ref     = $_POST["ref"];
	$object->description = $_POST["description"];
	
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

$help_url='EN:Module_Fabrication_En|FR:Module_Fabrication|ES:M&oacute;dulo_Fabrication';
llxHeader("",$langs->trans("ApplicationsUnits"),$help_url);


if ($action == 'create')
  {
    print_fiche_titre($langs->trans("NewApplicationsUnits"));
    
    print "<form action=\"fiche.php\" method=\"post\">\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    
    dol_htmloutput_mesg($mesg);
    
    print '<table class="border" width="100%">';
    
    // Ref
    print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Ref").'</td><td colspan="3"><input name="ref" size="12" value=""></td></tr>';
    
    //description
    print '<tr><td width="25%" class="field">'.$langs->trans("Description").'</td><td colspan="3">';
    print '<textarea wrap="soft" name="description" rows="3" cols="40"></textarea>';
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
	 
	 $object = new Units($db);
	 
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
	     
	     dol_fiche_head($head, 'card', $langs->trans("ApplicationUnits"), 0, 'stock');
	     
	     // Confirm delete third party
	     if ($action == 'delete')
	       {
		 $form = new Form($db);
		 $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("DeleteAWarehouse"),$langs->trans("ConfirmDeleteWarehouse",$object->libelle),"confirm_delete",'',0,2);
		 if ($ret == 'html') print '<br>';
	       }
	     
	     print '<table class="border" width="100%">';
	     
	     // Ref
	     print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td colspan="3">';
	     print $object->ref;
	     print '</td>';
	     
	     // Description
	     print '<tr><td valign="top">'.$langs->trans("Description").'</td><td colspan="3">'.nl2br($object->description).'</td></tr>';
	     
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
		 if ($user->rights->fabrication->crearunidad && $object->statut == 0)
		   print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
		 
		 if ($user->rights->fabrication->delunidad)
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
	     print_fiche_titre($langs->trans("WarehouseEdit"), $mesg);
	     
	     print '<form action="fiche.php" method="POST">';
	     print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	     print '<input type="hidden" name="action" value="update">';
	     print '<input type="hidden" name="id" value="'.$object->id.'">';
	     
	     print '<table class="border" width="100%">';
	     
	     // Ref
	     print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Ref").'</td><td colspan="3"><input name="ref" size="20" value="'.$object->ref.'"></td></tr>';
	     // Description
	     print '<tr><td valign="top">'.$langs->trans("Description").'</td><td colspan="3">';
	     // Editeur wysiwyg
	     require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
	     $doleditor=new DolEditor('description',$object->description,'',180,'dolibarr_notes','In',false,true,$conf->fckeditor->enabled,5,70);
	     $doleditor->Create();
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
