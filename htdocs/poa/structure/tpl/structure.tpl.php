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
 *      \file       htdocs/poa/poa/liste.php
 *      \ingroup    Plan Operativo Anual
 *      \brief      Page liste des poa
 */

require_once(DOL_DOCUMENT_ROOT."/core/class/html.formv.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.getutil.class.php");

require_once(DOL_DOCUMENT_ROOT."/poa/class/poastructureext.class.php");
require_once DOL_DOCUMENT_ROOT.'/poa/class/dictionarie.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/class/cnamestructure.class.php';
if ($conf->orgman->enabled)
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
$langs->load("poa@poa");

if (!$user->rights->poa->poa->leer)
	accessforbidden();

$object = new Poastructureext($db);
$objtmp = new Poastructureext($db);
$objdict = new Dictionarie($db);

$id = GETPOST('id');
$action = GETPOST('action');
$fk_father = GETPOST('fk_father');

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$filter = GETPOST('filter');
$filterf = GETPOST('filterf');
$filtro  = GETPOST('filtro');

$aType = array(1=>'Desarrollo',2=>'Funcionamiento');

if ($action == 'sub')
{
    // if ($filter == -1)
    //   {
    // 	$filterf = $id;
    // 	$filter = '';
    //   }
    // if (!empty($filter)) $filter .= ',';
    // $filter.= $id;
}
$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
if (empty($_GET['top']))
	$_SESSION['arrayPoa'] = array();
if ($_GET['top'] == 1)
	$_SESSION['filterrowid'] = $_GET['id'];
// if (isset($_GET['id']))
//   {
//     $filterrowid = $_SESSION['filterrowid'];
//     if (!empty($filterrowid)) $filterrowid .= ',';
//     $filterrowid .= $_GET['id'];
//     $_SESSION['filterrowid'] = $filterrowid;
//   }
if (empty($_SESSION['period_year']))
	$_SESSION['period_year'] = date('Y');
$period_year = $_SESSION['period_year'];

if ($id > 0 && $action != 'add')
	$object->fetch($id);
//verificamos el numero de niveles de la categoria proogramatica
$objcnamestr = new Cnamestructure($db);
$filterstatic = "";
$nStructure = $objcnamestr->fetchAll('ASC', 'code', 0, 0, array('entity'=>$conf->entity, 'active'=>1), 'AND');


// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('poastructure'));
if ($conf->orgman->enabled)
	$departament = new Pdepartamentext($db);


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel) 
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/poa/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}		
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}
	
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/poa/structure/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error = 0;
		$object->ref       	= GETPOST('ref');
		$object->label     	= GETPOST('label');
		$object->pseudonym 	= GETPOST('pseudonym');
		$object->fk_father 	= GETPOST('fk_father')+0;
		$object->fk_area   	= GETPOST('fk_area');
		$object->fk_area_ej	= GETPOST('fk_area_ej')+0;
		$object->fk_poa_objetive = GETPOST('fk_poa_objetive')+0;
		$object->gestion= $_SESSION['period_year'];
		$object->type 		= GETPOST('type');
		$object->pos     	= 1;
		$object->version 	= 1;
		$object->fk_user_create=$user->id;
		$object->fk_user_mod=$user->id;
		$object->datec = dol_now();
		$object->datem = dol_now();
		$object->tms = dol_now();	

		//si tiene father
		if ($objtmp->fetch($object->fk_father) && $object->fk_father > 0)
		{
			$object->pos = $objtmp->pos + 1;
			$object->sigla = $objtmp->sigla.str_pad($object->ref, $conf->global->POA_CODE_SIZE_STRUCTURE, "0", STR_PAD_LEFT);
			$object->type = $objtmp->type;
		}
		else
		{
			$object->pos = 1;
			$object->sigla = str_pad($object->ref, $conf->global->POA_CODE_SIZE_STRUCTURE, "0", STR_PAD_LEFT);
		}

		$object->entity  = $conf->entity;
		$object->statut  = 0;

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($object->label))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
		}

		if ($object->fk_area <= 0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Area")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/poa/structure/list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}

	// Action to update record
	if ($action == 'update')
	{
		$error=0;
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/poa/structure/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		$error = 0;
		$object->ref       	= GETPOST('ref');
		$object->label     	= GETPOST('label');
		$object->pseudonym 	= GETPOST('pseudonym');
		$object->fk_father 	= GETPOST('fk_father')+0;
		$object->fk_area   	= GETPOST('fk_area');
		$object->fk_area_ej	= GETPOST('fk_area_ej')+0;
		$object->fk_poa_objetive = GETPOST('fk_poa_objetive')+0;
		$object->gestion= $_SESSION['period_year'];
		$object->type 		= GETPOST('type');
		$object->pos     	= 1;
		$object->version 	= 1;
		$object->fk_user_mod=$user->id;
		$object->datem = dol_now();
		$object->tms = dol_now();	

		//si tiene father
		if ($objtmp->fetch($object->fk_father) && $object->fk_father > 0)
		{
			$object->pos = $objtmp->pos + 1;
			$object->sigla = $objtmp->sigla.str_pad($object->ref, $conf->global->POA_CODE_SIZE_STRUCTURE, "0", STR_PAD_LEFT);
			$object->type = $objtmp->type;
		}
		else
		{
			$object->pos = 1;
			$object->sigla = str_pad($object->ref, $conf->global->POA_CODE_SIZE_STRUCTURE, "0", STR_PAD_LEFT);
		}
		$object->entity  = $conf->entity;
		$object->statut  = 0;

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($object->label))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
		}

		if ($object->fk_area <= 0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Area")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/poa/structure/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
}
if ($ido>0)
	$filter.= " AND p.fk_poa_objetive = ".$ido;
else
{
	//mostramos todos los objetivos del pei seleccionado
	$filterstatic = " AND t.fk_poa_strategic = ".$id;
	$resobj = $objobjetive->fetchAll('','',0,0,array(),'AND',$filterstatic);
	$idObjetive = '';
	if ($resobj>0)
	{
		foreach($objobjetive->lines AS $j => $line)
		{
			if (!empty($idObjetive)) $idObjetive.=',';
			$idObjetive.= $line->id;
		}
	}
	else
		$idObjetive = '0';
	$filter.= " AND p.fk_poa_objetive IN (".$idObjetive.")";
}
$sql  = "SELECT p.rowid AS id, p.ref, p.label, p.type, p.pseudonym, p.fk_area, p.fk_area_ej, p.statut, p.fk_father, p.sigla, p.pos, p.fk_poa_objetive, ";
$sql.= " p.version ";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure as p ";
$sql.= " WHERE p.entity = ".$conf->entity;
$sql.= " AND p.gestion = ".$period_year;
if ($_SESSION['sel_area'])
	$sql.= " AND p.fk_area = ".$_SESSION['sel_area'];

$sql.= $filter;

$form = new Formv($db);

if ($sref)
{
	$sql.= " AND p.ref like '%".$sref."%'";
}
if ($sall)
{
	$sql.= " AND (p.ref like '%".$sall."%' OR p.label like '%".$sall."%' OR p.active like '%".$sall."%')";
}

$sql.= " ORDER BY p.sigla, p.pos";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);

dol_htmloutput_mesg($mesg);

if ($result)
{
	if ($_GET['top'] == 1)
	{
		$array = array();
		$_SESSION['arrayPoa'] = array();
		$array[1] = $_GET['father'];
		$array[2] = $_GET['id'];

	}
	elseif($_GET['top'] == 2)
	{
		$array = $_SESSION['arrayPoa'];
		$array[3] = $_GET['id'];
	}
	elseif($_GET['top'] == 3)
	{
		$array = $_SESSION['arrayPoa'];
		$array[4] = $_GET['id'];
	}
      //      $array[$id] = 0;

	$_SESSION['arrayPoa'] = $array;
    // }
	$num = $db->num_rows($result);
	$i = 0;
	if ($num == 0 && $user->rights->poa->str->write) $action = 'create';

	print_barre_liste($langs->trans("Liste structure"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	if ($action == 'create' || $action == 'edit')
	{
		if (! empty($conf->use_javascript_ajax))
		{
			print "\n".'<script type="text/javascript">';
			print '$(document).ready(function () {
				$("#selectfk_area").change(function() {
					document.formsoc.action.value="'.$action.'";
					document.formsoc.submit();
				}); 
			});';
			print '</script>'."\n";
		}
	}
	print '<form id="formsoc" name="formsoc" action="'.$_SERVER["PHP_SELF"].'" method="post" >';
	if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
	if ($action=='create' || $action == 'createsub')
	{
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="fk_father" value="'.$fk_father.'">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '<input type="hidden" name="ido" value="'.$ido.'">';
		print '<input type="hidden" name="fk_poa_objetive" value="'.$ido.'">';
	}
	elseif($action == 'edit')
	{
		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '<input type="hidden" name="ido" value="'.$ido.'">';
		print '<input type="hidden" name="fk_poa_objetive" value="'.$ido.'">';
		print '<input type="hidden" name="fk_father" value="'.$object->fk_father.'">';
	}
	else
		print '<input type="hidden" name="action" value="list">';

	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
	print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';


	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","",'width="10%"');
	print_liste_field_titre($langs->trans("Type"),"liste.php", "p.label","","","");
	print_liste_field_titre($langs->trans("Label"),"liste.php", "p.label","","","");
	print_liste_field_titre($langs->trans("Pseudonym"),"liste.php", "p.fk_father","","","");
	print_liste_field_titre($langs->trans("Area"),"liste.php", "p.fk_father","","","");
	print_liste_field_titre($langs->trans("Executing area"),"liste.php", "p.fk_father","","","");
	print_liste_field_titre($langs->trans("Status"),"liste.php", "p.active","","","");
	print_liste_field_titre($langs->trans("Action"),"", "","","","");
	print "</tr>\n";
	$espacio0 = '';
	$espacio1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	$espacio2 = $espacio1.$espacio1;
	$espacio3 = $espacio2.$espacio1;

	if ($num) 
	{
	    //WYSIWYG Editor
		require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

		$var=True;
		if (!empty($_GET['top']))
		{
			print "<tr $bc[$var]>";
			print '<td><a href="liste.php">'.img_picto($langs->trans("Ref"),'rightarrow').' '.$langs->trans('All').'</a></td>';

			print '<td>&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print "</tr>\n";
		}
		while ($i < min($num,$limit))
		{	      
			$obj = $db->fetch_object($result);
			$espacio = $espacio0;
			if ($obj->pos>0)
			{
				for ($a = 1; $a < $obj->pos; $a++)
				{
					$espacio.= $espacio1;
				}
			}

				//recuperamos del diccionario
			$namelevel = '';
			$resd = $objdict->fecth_dictionarie($obj->pos, 'c_name_structure',1,'rowid','label',"rowid",'i','');
			if ($resd==1) $namelevel = $objdict->label;

			$var=!$var;
			if ($id == $obj->id && $action == 'edit')
			{
				$sigla = '';
				if ($obj->fk_father>0)
				{
					$objtmp->fetch($obj->fk_father);
					$sigla = $objtmp->sigla;
				}
				print '<td nowrap>';
				if ($user->admin || $user->rights->poa->str->modref)
				{
					print $espacio.$sigla;
					print '<input id="ref" class="width50" type="number" value="'.(GETPOST('ref')?GETPOST('ref'):$obj->ref).'" name="ref" maxlength="2">';
				}
				else
				{
					print $espacio.$obj->sigla.str_pad($max, $conf->global->POA_CODE_SIZE_STRUCTURE, "0", STR_PAD_LEFT);
					print '<input id="ref" type="hidden" value="'.$obj->ref.'" name="ref">';
				}
				print '&nbsp;'.$namelevel;
				print '</td>';

				//type
				print '<td>';
				print $aType[trim($obj->type)];
				print '<input type="hidden" name="type" value="'.$obj->type.'">';
				print '</td>';
				//label
				print '<td class="fieldrequired">';
				$doleditor = new DolEditor('label', $obj->label, '', 60, 'dolibarr_details', '', false, true, $conf->global->FCKEDITOR_ENABLE_PRODUCTDESC, ROWS_2, '90%');
				$doleditor->Create();
				//print '<input id="label" type="text" value="'.(GETPOST('label')?GETPOST('label'):$obj->label).'" name="label" maxlength="255">';
				print '</td>';

				//pseudonym
				print '<td class="fieldrequired">';
				$doleditor = new DolEditor('pseudonym', $obj->pseudonym, '', 60, 'dolibarr_details', '', false, true, $conf->global->FCKEDITOR_ENABLE_PRODUCTDESC, ROWS_2, '90%');
				$doleditor->Create();
				print '</td>';

				//area
				$filtersub = "";
				if ($obj->fk_area > 0)
				{
					$filtersub = " AND fk_father = ".$obj->fk_area;
					$filtersub.= " OR rowid = ".$obj->fk_area;
				}
				print '<td>';
				print $form->select_departament((GETPOST('fk_area')?GETPOST('fk_area'):$obj->fk_area),'fk_area','',45,1,$filtersub);
				print '</td>';
				//area ej
				$fk_area = GETPOST('fk_area');
				if ($fk_area>0)
				{
					$filtersub2 = " AND fk_father = ".$fk_area;
					$filtersub2.= " OR rowid = ".$fk_area;					
				}
				else
					$filtersub2 = $filtersub;
				print '<td>';
				print $form->select_departament(GETPOST('fk_area_ej'),'fk_area_ej','',45,1,$filtersub2);
				print '</td>';
				print '<td>';
				print '</td>';

				print '<td>';
				print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
				print '</td>';

			}
			else
			{
				print "<tr $bc[$var]>";
				$filtro = $obj->id;
				$father = $obj->fk_father;
				print '<td><a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->id.'&action=edit">'.$espacio.$obj->sigla.'</a> '.'</td>';
				print '<td>'.$aType[trim($obj->type)].'</td>';

				print '<td>'.$obj->label.'</td>';
				print '<td>'.$obj->pseudonym.'</td>';

				if ($conf->orgman->enabled)
				{
					print '<td>';
					$departament->fetch($obj->fk_area);
					print $departament->getNomUrl(0, '', 0, 24, '',1);
					print '</td>';
					print '<td>';
					$departament->fetch($obj->fk_area_ej);
					print $departament->getNomUrl(0, '', 0, 24, '',1);
					print '</td>';
				}
				else
				{
					$getUtil->fetch_departament($obj->fk_area);
					print '<td>';
					print $getUtil->ref;
					print '</td>';
					print '<td>';
					$getUtil->fetch_departament($obj->fk_area_ej);
					print $getUtil->ref;
					print '</td>';
				}

				print '<td nowrap>'.$object->LibStatut($obj->status).'</td>';
				print '<td align="center">';
				if ($obj->pos == $nStructure)
					print '<a href="'.DOL_URL_ROOT.'/poa/partida/partida.php?id='.$id.'&ido='.$obj->fk_poa_objetive.'&ids='.$obj->id.'">'.img_picto($langs->trans("Planification"),DOL_URL_ROOT.'/poa/img/planif.png','',1).'</a>';
				if ($obj->pos < $nStructure && $user->rights->poa->plan->write)
					print '&nbsp;&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&ido='.$obj->fk_poa_objetive.'&fk_father='.$obj->id.'&action=create" title="'.$langs->trans('Createnewlevel').'">'.img_picto($langs->trans("Createnewlevel"),'rightarrow').'</a>';
				print '</td>';
			}
			print "</tr>\n";

			if ($fk_father > 0 && $fk_father == $obj->id && $action == 'create')
			{
				$objtmp = new Poastructureext($db);
				$objtmp->fetch($fk_father);
				$objtmp->get_structurenext($period_year);
				$newref = $objtmp->max+1;
				$max = $objtmp->max + 1;
				$espacio = $espacio0;
				if ($obj->pos>0)
				{
					for ($a = 1; $a <= $obj->pos; $a++)
					{
						$espacio.= $espacio1;
					}
				}
				//recuperamos del diccionario
				$namelevel = '';
				$resd = $objdict->fecth_dictionarie($obj->pos+1, 'c_name_structure',1,'rowid','label',"rowid",'i','');
				if ($resd==1) $namelevel = $objdict->label;

				print '<tr>';
				print '<td nowrap>';
				if ($user->admin || $user->rights->poa->str->modref)
				{
					print $espacio.$obj->sigla.str_pad($max, $conf->global->POA_CODE_SIZE_STRUCTURE, "0", STR_PAD_LEFT);
					print '<input id="ref" type="hidden" value="'.(GETPOST('ref')?GETPOST('ref'):$max).'" name="ref" maxlength="2">';
				}
				else
				{
					print $espacio.$obj->sigla.str_pad($max, $conf->global->POA_CODE_SIZE_STRUCTURE, "0", STR_PAD_LEFT);
					print '<input id="ref" type="hidden" value="'.$max.'" name="ref">';
				}
				print '&nbsp;'.$namelevel;
				print '</td>';

				//type
				print '<td>';
				print $aType[trim($obj->type)];
				print '<input type="hidden" name="type" value="'.$obj->type.'">';
				print '</td>';
				//label
				print '<td class="fieldrequired">';
				print '<input id="label" type="text" value="'.GETPOST('label').'" name="label" maxlength="255">';
				print '</td>';

				//pseudonym
				print '<td class="fieldrequired">';
				print '<input id="pseudonym" type="text" value="'.GETPOST('pseudonym').'" name="pseudonym" maxlength="255">';
				print '</td>';

				//father
				print '<input type="hidden" name="fk_father" value="'.$fk_father.'">';
				//area
				$filtersub = "";
				if ($obj->fk_area > 0)
				{
					$filtersub = " AND fk_father = ".$obj->fk_area;
					$filtersub.= " OR rowid = ".$obj->fk_area;
				}
				print '<td>';
				print $form->select_departament((GETPOST('fk_area')?GETPOST('fk_area'):$obj->fk_area),'fk_area','',45,1,$filtersub,1);
				print '</td>';
				$resdep = $departament->liste_son($obj->fk_area);
				if ($resdep>0)
				{
					$idsArea = implode(',',$departament->arrayson);
				}
				//area ej
				//$fk_area = GETPOST('fk_area');
				//if ($fk_area>0)
				//{
					//$filtersub2 = " AND fk_father = ".$fk_area;
				$filtersub2.= " AND rowid IN(".$idsArea.")";					
				//}
				//else
				//	$filtersub2 = $filtersub;
				print '<td>';
				print $form->select_departament(GETPOST('fk_area_ej'),'fk_area_ej','',45,1,$filtersub2,1);
				print '</td>';
				print '<td>';
				print '</td>';

				print '<td>';
				print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
				print '</td>';
				print '</tr>';
			}
			$i++;
		}
	}
	if (empty($fk_father) && $action == 'create')
	{
		$objtmp = new Poastructureext($db);
		$objtmp->get_structurenext($period_year);
		$newref = $objtmp->max+1;
		$max = $objtmp->max + 1;
		$espacio = $espacio0;
		if ($obj->pos>0)
		{
			for ($a = 1; $a <= $obj->pos; $a++)
			{
				$espacio.= $espacio1;
			}
		}
		$namelevel = '';
		$resd = $objdict->fecth_dictionarie(1, 'c_name_structure',1,'rowid','label',"rowid",'i','');
		if ($resd==1) $namelevel = $objdict->label;
		print '<tr>';
		print '<td nowrap>';
		if ($user->admin || $user->rights->poa->str->modref)
		{
			print str_pad($max, $conf->global->POA_CODE_SIZE_STRUCTURE, "0", STR_PAD_LEFT);
			print '<input id="ref" type="hidden" value="'.(GETPOST('ref')?GETPOST('ref'):$max).'" name="ref" maxlength="2">';
		}
		else
		{
			print str_pad($max, $conf->global->POA_CODE_SIZE_STRUCTURE, "0", STR_PAD_LEFT);
			print '<input id="ref" type="hidden" value="'.$max.'" name="ref">';
		}
		print '&nbsp;'.$namelevel;
		print '</td>';

	//type
		print '<td>';
		print $form->selectarray('type',$aType,GETPOST('type'),1,0);
		print '</td>';
	//label
		print '<td class="fieldrequired">';
		print '<input id="label" type="text" value="'.GETPOST('label').'" name="label" maxlength="255">';
		print '</td>';

	//pseudonym
		print '<td class="fieldrequired">';
		print '<input id="pseudonym" type="text" value="'.GETPOST('pseudonym').'" name="pseudonym" maxlength="255">';
		print '</td>';

	//father
		print '<input type="hidden" name="fk_father" value="'.$fk_father.'">';
	//area
		$filtersub = "";
		if ($obj->fk_area > 0)
		{
			//	$filtersub = " AND fk_father = ".$obj->fk_area;
			//	$filtersub.= " OR rowid = ".$obj->fk_area;
		}
		print '<td>';
		print $form->select_departament((GETPOST('fk_area')?GETPOST('fk_area'):$obj->fk_area),'fk_area','',45,1,$filtersub);
		print '</td>';
	//area ej
		$fk_area = GETPOST('fk_area');
		if ($fk_area>0)
		{
			$filtersub2 = " AND fk_father = ".$fk_area;
			$filtersub2.= " OR rowid = ".$fk_area;					
		}
		else
			$filtersub2 = $filtersub;
		print '<td>';
		print $form->select_departament(GETPOST('fk_area_ej'),'fk_area_ej','',45,1,$filtersub2);
		print '</td>';
		print '<td>';
		print '</td>';

		print '<td>';
		print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
		print '</td>';
		print '</tr>';
	}

	$db->free($result);

	print "</table>";
	print '</form>';

	print "<div class=\"tabsAction\">\n";

	if ($action == '' || $action == 'menu2' || $action== 'sub')
	{
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/poa/liste.php'.'?dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
		if ($user->rights->poa->str->crear && $ido > 0)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&ido='.$ido.'&action=create">'.$langs->trans("Createnew").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
	}
	print '</div>';
}
else
{
	dol_print_error($db);
}

?>
