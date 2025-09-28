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
 *	\file       htdocs/salary/typefol/fiche.php
 *	\ingroup    Type fol
 *	\brief      Page fiche salary type fol
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/ptypefol.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/ptypefoldet.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/ptypefolseq.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pconcept.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("salary@salary");

$action=GETPOST('action');

$id        = GETPOST("id");
$rid       = GETPOST("rid");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';

$object   = new Ptypefol($db);
$objectd  = new Ptypefoldet($db);
$objects  = new Ptypefolseq($db);
$objconcept = new Pconcept($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->salary->creartypefol)
{
	$object->ref     = $_POST["ref"];
	$object->entity  = $conf->entity;
	$object->detail  = GETPOST('detail');
	$object->details = GETPOST('details');
	$object->state   = 1;

	if ($object->ref) 
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
		$mesg='<div class="error">'.$langs->trans("Errorrefdescriptionrequired").'</div>';
	$action="create";   // Force retour sur page creation
}
}

// Adddet
if ($action == 'adddet' && $user->rights->salary->creartypefol)
{
	$error = 0;
	$objects->sequen       = GETPOST('sequen');
	$objects->fk_type_fol  = $_POST['id'];
	if ($objconcept->fetch($_POST['fk_concept'])>0)
	{
		if ($objconcept->id == $_POST['fk_concept'])
			$objects->ref_concept = $objconcept->ref;
		else
		{
			$error++;
		}
	}
	else
	{
		$error++;
		$mesg.='<div class="error">'.$objconcept->error.'</div>';
	}
	$objects->state       = GETPOST('state');
	$objects->details     = GETPOST('details');
	if (empty($objects->sequen)) 
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans('Errorsequenisrequired').'</div>';
	}
	if (empty($error))
	{
		$rid = $objects->create($user);
		if ($rid > 0)
		{
			header("Location: fiche.php?id=".$id);
			exit;
		}
		$action = 'createdet';
		$mesg='<div class="error">'.$objectd->error.'</div>';
	}
	else
	{
	$action="createdet";   // Force retour sur page creation
}
}

// Updatedet
if ($action == 'updatedet' && $user->rights->salary->creartypefol)
{
	$error = 0;
	if ($objects->fetch($_POST["rid"])>0)
	{
		if ($objects->fk_type_fol == $_POST["id"])
		{
			$objects->sequen      = GETPOST('sequen');
			if ($objconcept->fetch($_POST['fk_concept']>0))
			{
				if ($objconcept->id == $_POST['fk_concept'])
					$objects->ref_concept = $objconcept->ref;
				else
				{
					echo 'errr';
					$error++;
				}
			}
			else
			{
				$error++;
				$mesg.='<div class="error">'.$objconcept->error.'</div>';
			}
			$objects->state       = GETPOST('state');
			$objects->details     = GETPOST('details');
			if (empty($objects->sequen)) 
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans('Errorsequenisrequired').'</div>';
			}
			if (empty($error)) 
			{
				$objects->update($user);
				header("Location: fiche.php?id=".$id);
				exit;
			}
			else
			{
		$action="editdet";   // Force retour sur page creation
	}
}
}
}

// Delete period
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salary->deldpto)
{
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/salary/typefol/liste.php');
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
		$object->ref     = $_POST["ref"];
		$object->detail  = GETPOST('detail');
		$object->details = GETPOST('details');
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
	$id         = $_POST["id"];
	$rid=0;
}



/*
 * View
 */

$form=new Form($db);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

if ($action == 'create' && $user->rights->salary->creartypefol)
{
	print_fiche_titre($langs->trans("Newtypefol"));
	
	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	
	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// ref
	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="3" maxlength="4">';
	print '</td></tr>';
	//detail
	print '<tr><td class="fieldrequired">'.$langs->trans('Description').'</td><td colspan="2">';
	print '<input id="detail" type="text" value="'.$object->detail.'" name="detail" size="35" maxlength="40">';
	print '</td></tr>';
	
	//details
	print '<tr><td>'.$langs->trans('Details').'</td><td colspan="2">';
	print '<textarea class="flat" name="details" id="details" cols="40" rows="'.ROWS_3.'">';
	print $object->details;
	print '</textarea>';
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
	  	
	  	dol_fiche_head($head, 'card', $langs->trans("Typefol"), 0, 'salary');
	  	
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
	  	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deletetypefol"),$langs->trans("Confirmdeletetypefol",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
	  	if ($ret == 'html') print '<br>';
	  }
	  
	  print '<table class="border" width="100%">';


	  // ref
	  print '<tr><td>'.$langs->trans('Ref').'</td><td colspan="2">';
	  print $object->ref;
	  print '</td></tr>';
	  //detail
	  print '<tr><td>'.$langs->trans('Ref').'</td><td colspan="2">';
	  print $object->detail;
	  print '</td></tr>';
	  
	  //details
	  print '<tr><td>'.$langs->trans('Details').'</td><td colspan="2">';
	  print '<textarea class="flat" name="details" id="details" cols="40" rows="'.ROWS_3.'" disabled="disabled">';
	  print $object->details;
	  print '</textarea>';
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
	  	if ($user->rights->salary->creartypefol)
	  		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
	  	
	  	if ($user->rights->salary->deltypefol)
	  		print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	  }	  
	  print "</div>";		
	}
	$main_user = $user->admin;
	If ($main_user == true)
	{
		if (! $sortfield) $sortfield="p.ref";
		if (! $sortorder) $sortorder="ASC";
		$page = $_GET["page"];
		if ($page < 0) $page = 0;
		$limit = $conf->liste_limit;
		$offset = $limit * $page;
		
	  //detalle de los procedimientos de calculo
		$sql = "SELECT a.rowid AS id, a.sequen, a.ref_concept, a.details, a.state ";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_type_fol_seq AS a ";
		$sql.= " WHERE a.fk_type_fol = ".$id;
		$sql.= " ORDER BY a.sequen";
	  //$sql.= $db->plimit($limit+1, $offset);
		$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
		print_barre_liste($langs->trans("Liste procedim details"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);
		
		print '<table class="noborder" width="100%">';
		
		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Sequen"),"fiche.php", "","","","");
		print_liste_field_titre($langs->trans("Ref"),"fiche.php", "","","","");
		print_liste_field_titre($langs->trans("Details"),"fiche.php", "","","","");
		print_liste_field_titre($langs->trans("Enabled"),"fiche.php", "","","","");
		print_liste_field_titre($langs->trans("Action"),'','','','','align="center"');
		print "</tr>\n";
		if ($action == 'createdet')
		{
			$objects->initAsSpecimen();
			$objnew = $objects;
			include_once DOL_DOCUMENT_ROOT.'/salary/tpl/add_typefolseq.tpl.php';
		}
		$result = $db->query($sql);
		if ($result)
		{
			$num = $db->num_rows($result);
			$i = 0;
			if ($num) 
			{
				$var=True;
				while ($i < $num)
				{
					$obj = $db->fetch_object($result);
					if ($obj->id == $rid)
					{
						$objects->fetch($obj->id);
						$objnew = $objects;
						include_once DOL_DOCUMENT_ROOT.'/salary/tpl/add_typefolseq.tpl.php';
					}
					else
					{
						$var=!$var;
						print "<tr $bc[$var]>";
						print '<td><a href="fiche.php?id='.$object->id.'&rid='.$obj->id.'">'.img_picto($langs->trans("Ref"),DOL_URL_ROOT.'/salary/img/next','',1).' '.sprintf("%04d",$obj->sequen).'</a></td>';
						print '<td>'.$obj->ref_concept;
			  //buscamos el concepto
						if ($objconcept->fetch_ref($obj->ref_concept))
						{
							if ($objconcept->codref == $obj->ref_concept)
							{
								print ' - '.$objconcept->detail;
							}
							else
								print '';
						}
						else
							print ' err';

						print '</td>';
						print '<td>'.$obj->details.'</td>';
						print '<td>'.select_yesno($obj->state,'state','',0,1,1).'</td>';
						
			  //action
						print '<td>';
						print '<center>';
						print '<a href="fiche.php?action=editdet&id='.$object->id.'&rid='.$obj->id.'">'.img_picto($langs->trans("Edit"),'edit').'</a>';
						print '&nbsp;&nbsp;';
						print '<a href="fiche.php?action=deletedet&id='.$object->id.'&rid='.$obj->id.'">'.img_picto($langs->trans("Delete"),'delete').'</a>';
						
						print '</center>';
						print '</td>';
						
						print "</tr>\n";
					}
					$i++;
				}
			}
			
			$db->free($result);
		}  
		print "</table>";
		
		/* ************************************************************************** */
		/*                                                                            */
		/* Barre d'action                                                             */
		/*                                                                            */
		/* ************************************************************************** */
		
		print "<div class=\"tabsAction\">\n";
		
		if ($action == '' && !empty($id))
		{
			if ($user->rights->salary->creartypefol)
				print '<a class="butAction" href="fiche.php?action=createdet&id='.$object->id.'">'.$langs->trans("Create").'</a>';
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Create")."</a>";
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
	  	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="3" maxlength="4">';
	  	print '</td></tr>';
	  //detail
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Detail').'</td><td colspan="2">';
	  	print '<input id="detail" type="text" value="'.$object->detail.'" name="detail" size="35" maxlength="40">';
	  	print '</td></tr>';
	  	
	  //details
	  	print '<tr><td>'.$langs->trans('Details').'</td><td colspan="2">';
	  	print '<textarea class="flat" name="details" id="details" cols="40" rows="'.ROWS_3.'">';
	  	print $object->details;
	  	print '</textarea>';
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
