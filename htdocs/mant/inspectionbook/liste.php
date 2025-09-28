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
 *      \file       htdocs/mant/inspectionbook/liste.php
 *      \ingroup    Mant
 *      \brief      Page liste inspection book
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/mant/inspectionbook/class/mbookdet.class.php");

require_once(DOL_DOCUMENT_ROOT."/mant/lib/mant.lib.php");

$langs->load("mant@mant");

if (!$user->rights->mant->teacher->leer)
  accessforbidden();

$object = new Mbookdet($db);

$action = GETPOST('action');
$id = GETPOST('id');

// Add
if ($action == 'add' && $user->rights->mant->teacher->crear)
  {
    $error = 0;
    $object->code_insp_book = GETPOST('code');
    $object->ref          = $_POST["ref"];
    $object->entity       = $conf->entity;
    $object->detail       = $_POST["detail"];

    if (empty($object->ref)) 
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans('Error, ref is required').'</div>';
      }
    if (empty($object->detail)) 
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans('Error, detail is required').'</div>';
      }
    if (empty($error)) 
      {
	$object->statut = 1;
	$idsub = $object->create($user);
	if ($idsub > 0)
	  {
	    header("Location: liste.php?id=".$id.'&action=sub');
	    exit;
	  }
	$action = 'sub';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else
      {
	$action="sub";   // Force retour sur page creation
      }
  }

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.code";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
$limit1 = $conf->liste_limit;
$offset1 = $limit1 * $page;
$sql  = "SELECT p.rowid AS id, p.code, p.label, p.active ";
$sql.= " FROM ".MAIN_DB_PREFIX."c_inspection_book as p ";
$sql.= " WHERE p.active = 1";
if ($sref)
{
    $sql.= " AND p.code like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (p.code like '%".$sall."%' OR p.label like '%".$sall."%')";
}
if ($id)
  $sql.= " AND p.rowid = ".$id;

$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    $help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
    llxHeader("",$langs->trans("Liste inspection book"),$help_url);
    
    print_barre_liste($langs->trans("Liste inspection book"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    print '<table class="noborder" width="100%">';
    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Code"),"liste.php", "p.code","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Label"),"liste.php", "p.label","","",'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Type"),"", "","","",'');
    print_liste_field_titre($langs->trans("Status"),"liste.php", "p.active","","",'align="right"',$sortfield,$sortorder);
    print "</tr>\n";
    if ($num) {
      $var=True;
      while ($i < min($num,$limit))
	{
	  $obj = $db->fetch_object($result);
	  //$object = new Pdepartament($db);
	  //$object->fetch($obj->fk_father);
	  
	  $var=!$var;
	  print "<tr $bc[$var]>";
	  print '<td><a href="liste.php?id='.$obj->id.'&action=sub">'.img_picto($langs->trans("Ref"),'rightarrow').' '.$obj->code.'</a></td>';
	  print '<td>'.$obj->label.'</td>';
	  print '<td align="right">'.$object->LibStatut($obj->active,3).'</td>';

	  print "</tr>\n";
	  if ($action == 'deactivate')
	    {
	      $objectsub = new Mbookdet($db);
	      $objectsub->fetch(GETPOST('subid'));
	      if ($objectsub->code_insp_book == $obj->code)
		{
		  //desactivando
		  $objectsub->statut = 0;
		  $objectsub->update($user);
		  $action = 'sub';
		}
	    }
	  if ($action == 'activate')
	    {
	      $objectsub = new Mbookdet($db);
	      $objectsub->fetch(GETPOST('subid'));
	      if ($objectsub->code_insp_book == $obj->code)
		{
		  //desactivando
		  $objectsub->statut = 1;
		  $objectsub->update($user);
		  $action = 'sub';
		}
	    }
	  if ($action == 'sub')
	    {
	      $sortfield1 = isset($_GET["sortfield1"])?$_GET["sortfield1"]:$_POST["sortfield1"];
	      $sortorder1 = isset($_GET["sortorder1"])?$_GET["sortorder1"]:$_POST["sortorder1"];
	      if (! $sortfield1) $sortfield1="p.ref";
	      if (! $sortorder1) $sortorder1="ASC";
	      
	      //lista los dependientes
	      $sqls  = "SELECT p.rowid AS id, p.ref, p.detail, p.type_campo, p.statut ";
	      $sqls.= " FROM ".MAIN_DB_PREFIX."m_book_det as p ";
	      $sqls.= " WHERE p.code_insp_book = '".$obj->code."'"; 
	      $sqls.= " ORDER BY $sortfield1 $sortorder1";
	      $sqls.= $db->plimit($limit1+1, $offset1);
	      
	      $ressub = $db->query($sqls);
	      if ($ressub)
		{
		  $nsub = $db->num_rows($ressub);
		  $j = 0;
		  if ($user->rights->mant->teacher->crear)
		    {
		      $object->ref = $object->max_ref($obj->code);
		      if (is_numeric($object->ref))
			{
			  $nLen = strlen($object->ref);
			  $object->ref = str_pad($object->ref + 1,$nLen,'0',STR_PAD_LEFT);
			}
		      print '<form action="liste.php" method="post">';
		      print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		      print '<input type="hidden" name="action" value="add">';
		      print '<input type="hidden" name="id" value="'.$id.'">';
		      print '<input type="hidden" name="code" value="'.$obj->code.'">';

		      dol_htmloutput_mesg($mesg);
		      // ref
		      print '<tr>';
		      print '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.img_picto($langs->trans("Ref"),'rightarrow').' ';
		      print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="15" maxlength="13">';
		      print '</td>';
		      // detail
		      print '<td><input id="detail" type="text" value="'.$object->detail.'" name="detail" size="35">';
		      print '</td>';
		      // type
		      print '<td>';
		      print select_type_campo($object->type_campo,'type_campo','',1,0);
		      print '</td>';
		      print '<td align="right">';
		      print '<button type="submit">'.img_picto($langs->trans('Save'),'save').'</button>';
		      print '</td></tr>';
		      print '</form>';

		    }
		  if ($nsub)
		    {
		      $var1=True;
		      while ($j < min($nsub,$limit1))
			{
			  $objsub = $db->fetch_object($ressub);
			  $var1=!$var1;
			  print "<tr $bc[$var1]>";
			  print '<td><a href="fiche.php?id='.$objsub->id.'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.img_picto($langs->trans("Ref"),'rightarrow').' '.$objsub->ref.'</a></td>';
			  print '<td>'.$objsub->detail.'</td>';
			  print '<td>';
			  print select_type_campo($objsub->type_campo,'type_campo','',0,1);
			  print '</td>';

			  print '<td align="right"><a href="liste.php?id='.$id.'&subid='.$objsub->id.'&action='.(($objsub->statut==1)?'deactivate':'activate').'">'.$object->LibStatut($objsub->statut,2).'</a></td>';
			  print "</tr>\n";
			  $j++;
			}
		    }
		}
	    }
	  $i++;
	}
    }
    
    $db->free($result);
    
    print "</table>";    
    print "<div class=\"tabsAction\">\n";
    
    if ($action == 'sub')
      print "<a class=\"butAction\" href=\"liste.php\">".$langs->trans("Return")."</a>";
    print '</div>';

  }
 else
   {
     dol_print_error($db);
   }


$db->close();

llxFooter();
?>
