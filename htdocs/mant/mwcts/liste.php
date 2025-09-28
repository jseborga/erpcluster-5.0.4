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
 *      \file       htdocs/mant/mwcts/liste.php
 *      \ingroup    Mantenimeinto clase tipo specialidad
 *      \brief      Page liste des clase tipo especialidad
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/mant/mwcts/class/mwcts.class.php");
require_once(DOL_DOCUMENT_ROOT."/mant/lib/mant.lib.php");

$langs->load("mant@mant");

if (!$user->rights->mant->teacher->leer)
  accessforbidden();

$object = new Mwcts($db);

$action = GETPOST('action');
$id     = GETPOST('id');

if ($action == 'add')
  {
    $object->entity = $conf->entity;
    $object->working_class = GETPOST('working_class');
    $object->typemant      = GETPOST('typemant');
    $object->speciality    = GETPOST('speciality');
    $object->fk_user_create= $user->id;
    $object->date_create   = dol_now();
    $object->tms           = dol_now();
    $object->statut        = 0; 
    $res = $object->create($user);
    if ($res>0)
      {
	header("Location: liste.php");
      }
    else
      {
	$mesg='<div class="error">'.$object->error.'</div>';
	$action = 'create';
      }
  }

if ($action == 'update')
  {
    if ($object->fetch($id))
      {
	$object->working_class = GETPOST('working_class');
	$object->typemant      = GETPOST('typemant');
	$object->speciality    = GETPOST('speciality');
	$res = $object->update($user);
	if ($res>0)
	  {
	    header("Location: liste.php");
	  }
	else
	  {
	    $mesg='<div class="error">'.$object->error.'</div>';
	    $action = 'edit';
	  }
      }
    $action='edit';
  }

// Delete jobs
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->mant->teacher->del)
{
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: liste.php");
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

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.rowid";
if (! $sortorder) $sortorder="ASC";

$page   = $_GET["page"];

if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$sql  = "SELECT p.rowid AS id, p.working_class, p.typemant, p.speciality ";
$sql.= " FROM ".MAIN_DB_PREFIX."m_wcts as p ";
$sql.= " WHERE p.entity = ".$conf->entity;

$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    $help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
    llxHeader("",$langs->trans("Liste property"),$help_url);
    
    print_barre_liste($langs->trans("Liste WCTS"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    print '<table class="noborder" width="100%">';
    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.id","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Workingclass"),"liste.php", "p.working_class","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Typemant"),"liste.php", "p.typemant","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Speciality"),"liste.php", "p.speciality","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Action"),"", "","","","");

    print "</tr>\n";

    // Confirm delete third party
    if ($action == 'delete')
      {
	$form = new Form($db);
	$object->fetch($id);
	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Delete"),$langs->trans("Confirmdelete"),"confirm_delete",'',0,2);
	if ($ret == 'html') print '<br>';
      }

    //nuevo
    if ($action == 'create')
      include_once DOL_DOCUMENT_ROOT.'/mant/mwcts/tpl/mwcts.tpl.php';
    if ($num) {
      $var=True;
      while ($i < min($num,$limit))
	{
	  $obj = $db->fetch_object($result);
	  if ($id == $obj->id && $action == 'edit')
	    {
	      $object = $obj;
	      include_once DOL_DOCUMENT_ROOT.'/mant/mwcts/tpl/mwcts.tpl.php';
	    }
	  else
	    {
	      $var=!$var;
	      print "<tr $bc[$var]>";
	      print '<td><a href="liste.php?id='.$obj->id.'&amp;action=edit">'.img_object($langs->trans("Ref"),'default').' '.$obj->id.'</a></td>';	      
	      print '<td>'.select_working_class($obj->working_class,'working_class','',0,1).'</td>';
	      print '<td>';
	      print select_typemant($obj->typemant,'typemant','',0,1);
	      print '</td>';
	      print '<td>'.select_speciality($obj->speciality,'speciality','',0,1).'</td>';
	      print '<td><a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->id.'&amp;action=delete">'.img_picto($langs->trans('Delete'),'delete').'</a></td>';
	      print "</tr>\n";
	    }
	  $i++;
	}
    }

    $db->free($result);
    
    print "</table>";
    print "<div class=\"tabsAction\">\n";
    
    if ($action == '')
      {
	if ($user->rights->mant->teacher->crear)
	  print "<a class=\"butAction\" href=\"liste.php?action=create\">".$langs->trans("Createnew")."</a>";
	else
	  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
      }
    print '</div>';
  }
 else
   {
     dol_print_error($db);
   }


$db->close();

llxFooter();
?>
