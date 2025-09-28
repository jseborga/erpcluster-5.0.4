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
 *      \file       htdocs/mant/charge/liste.php
 *      \ingroup    Mantenimeinto cargos
 *      \brief      Page liste des charges
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/poa/flowmodels/class/cflowmodels.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';

$langs->load("poa@poa");

if (!$user->rights->poa->teacher->leer)
  accessforbidden();

$object = new Cflowmodels($db);
$objcod = new Cflowmodels($db);
$objarea = new Poaarea($db);

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
    $object->deadlines = GETPOST('deadlines');
    $object->label = GETPOST('label');
    $object->sequen = GETPOST('sequen');
    $object->quant = GETPOST('quant');
    $object->active = 1;
    $id = $object->create($user);
    if ($id <=0)
      {
	$mesg='<div class="error">'.$object->error.'</div>';
	$action = 'create';
      }
    else
      {
	header("Location: liste.php");
	exit;
      }
  }

if ($action == 'update' && $user->rights->poa->teacher->crear)
  {
    if ($object->fetch($id)>0)
      {
	$object->groups = GETPOST('groups');
	$object->code = GETPOST('code');
	$object->deadlines = GETPOST('deadlines');
	$object->label = GETPOST('label');
	$object->sequen = GETPOST('sequen');
	$object->quant = GETPOST('quant');
	$object->active = 1;
	$result = $object->update($user);
	if ($result <=0)
	  {
	    $mesg='<div class="error">'.$object->error.'</div>';
	    $action = 'edit';
	  }
	else
	  {
	    header("Location: liste.php");
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

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
// if (empty($filter))
//   $filter = -1;
//filtros
$result = $object->getlist(/*groups*/'',$limit+1,$offset,/*filter*/'',/*order*/'');
if ($result || !empty($action))
  {
    $num = count($object->array);

    // Confirm delete third party
    if ($action == 'delete')
      {
	$object->fetch($id);
	$form = new Form($db);
	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteflowmodel"),$langs->trans("Confirmdeleteflowmodel",$object->code.' '.$object->label),"confirm_delete",'',0,2);
	if ($ret == 'html') print '<br>';
      }

    $i = 0;
    $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
    llxHeader("",$langs->trans("Liste flow models"),$help_url);
    
    print_barre_liste($langs->trans("Liste flow models"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    print '<table class="noborder" width="100%">';

    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Group"),"liste.php", "p.groups","","","");
    print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.code","","","");
    print_liste_field_titre($langs->trans("Deadlines"),"liste.php", "p.deadlines","","","");
    print_liste_field_titre($langs->trans("Label"),"liste.php", "p.label","","","");
    print_liste_field_titre($langs->trans("Sequen"),"liste.php", "p.sequen","","","");
    print_liste_field_titre($langs->trans("Days"),"liste.php", "p.quant","","","");
    print_liste_field_titre($langs->trans("Resp."),"liste.php", "p.code_actor_last","","","");
    print_liste_field_titre($langs->trans("Dest_one"),"liste.php", "p.code0","","","");
    print_liste_field_titre($langs->trans("Dest_two"),"liste.php", "p.code1","","","");
    print_liste_field_titre($langs->trans("Dest_three"),"liste.php", "p.code2","","","");
    print_liste_field_titre($langs->trans("Dest_four"),"liste.php", "p.code3","","","");
    print_liste_field_titre($langs->trans("Dest_five"),"liste.php", "p.code4","","","");
    
    print_liste_field_titre($langs->trans("Active"),"liste.php", "p.active","","","");
    print_liste_field_titre($langs->trans("Action"),"", "","","","");
    print "</tr>\n";

    $var=!$var;
    
    if ($action == 'create')
      {
	$obj = $object;
	include_once DOL_DOCUMENT_ROOT.'/poa/flowmodels/tpl/edit.tpl.php';
      }
    
    if ($num)
      {
	$var=True;
	foreach ((array) $object->array AS $i => $obj)
	  {
	    if ($action == 'edit' && $obj->id == $id)
	      {		
		include_once DOL_DOCUMENT_ROOT.'/poa/flowmodels/tpl/edit.tpl.php';
	      }
	    else
	      {
		print "<tr $bc[$var]>";
		print '<td>'.select_tables($obj->groups,'groups','',0,1,"05",0).'</td>';
		print '<td><a href="fiche.php?id='.$obj->id.'">'.select_typeprocedure($obj->code,'code','',0,1,"code").'</a></td>';
		print '<td>'.$obj->deadlines.'</td>';
		print '<td>'.$obj->label.'</td>';
		print '<td>'.$obj->sequen.'</td>';
		print '<td>'.$obj->quant.'</td>';
		//		if ($objarea->fetch($obj->code_area_last)>0)
		$title = '';
		if ($obj->code_actor_last)
		  {
		    $title = select_actors($obj->code_actor_last,'code_actor_last','',0,1,'code','libelle');
		    print '<td>'.'<a href="#" title="'.$title.'">'.select_actors($obj->code_actor_last,'code_actor_last','',0,1,'code','code').'</td>';
		  }
		 else
		   print '<td>&nbsp;</td>';
		//code0
		if ($obj->code0)
		  {
		    if ($objcod->fetch_code($obj->groups,$obj->code0)>0)
		      {
			$title = $objcod->label;
			print '<td>'.'<a href="#" title="'.$title.'">'.$objcod->code_actor_last.'</td>';
		      }
		    else
		      print '<td>&nbsp;'.$obj->code0.'</td>';
		     
		  }
		else
		  print '<td>&nbsp;'.$obj->code_area_next.'</td>';
		//code1
		if ($obj->code1)
		  {
		    if ($objcod->fetch_code($obj->groups,$obj->code1)>0)
		      {
			$title = $objcod->label;
			print '<td>'.'<a href="#" title="'.$title.'">'.$objcod->code_actor_last.'</td>';
		      }
		    else
		      print '<td>&nbsp;'.$obj->code0.'</td>';
		     
		  }
		else
		  print '<td>&nbsp;'.$obj->code_area_next.'</td>';
		//code2
		if ($obj->code2)
		  {
		    if ($objcod->fetch_code($obj->groups,$obj->code2)>0)
		      {
			$title = $objcod->label;
			print '<td>'.'<a href="#" title="'.$title.'">'.$objcod->code_actor_last.'</td>';
		      }
		    else
		      print '<td>&nbsp;'.$obj->code0.'</td>';
		     
		  }
		else
		  print '<td>&nbsp;'.$obj->code_area_next.'</td>';
		//code3
		if ($obj->code3)
		  {
		    if ($objcod->fetch_code($obj->groups,$obj->code3)>0)
		      {
			$title = $objcod->label;
			print '<td>'.'<a href="#" title="'.$title.'">'.$objcod->code_actor_last.'</td>';
		      }
		    else
		      print '<td>&nbsp;'.$obj->code0.'</td>';
		     
		  }
		else
		  print '<td>&nbsp;'.$obj->code_area_next.'</td>';
		//code4
		if ($obj->code4)
		  {
		    if ($objcod->fetch_code($obj->groups,$obj->code4)>0)
		      {
			$title = $objcod->label;
			print '<td>'.'<a href="#" title="'.$title.'">'.$objcod->code_actor_last.'</td>';
		      }
		    else
		      print '<td>&nbsp;'.$obj->code0.'</td>';
		     
		  }
		else
		  print '<td>&nbsp;'.$obj->code_area_next.'</td>';

		print '<td>'.$obj->active.'</td>';
		print '<td><a href="list.php?id='.$obj->id.'">'.img_picto($langs->trans("Delete"),'delete').'</a></td>';
		print "</tr>\n";
	      }
	  }
      }
    
    $db->free($result);
    
    print "</table>";
    print "<div class=\"tabsAction\">\n";
    
    if ($action == '')
      {
	if ($user->rights->poa->teacher->crear)
	  print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
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
