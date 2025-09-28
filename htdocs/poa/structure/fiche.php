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
 *	\file       htdocs/mant/charge/fiche.php
 *	\ingroup    Charges
 *	\brief      Page fiche mant charges
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.getutil.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/class/poastructureext.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/class/dictionarie.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("poa@poa");

$action=GETPOST('action');
$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$dol_hide_leftmenu = GETPOST('dol_hide_leftmenu');

$mesg = '';

$object  = new Poastructureext($db);
$objarea = new Poaarea($db);
$objdict = new Dictionarie($db);

$aType = array(1=>'Desarrollo',2=>'Funcionamiento');
if (empty($_SESSION['period_year']))
	$_SESSION['period_year'] = date('Y');
$period_year = $_SESSION['period_year'];
$nLevelstr = $conf->global->POA_NUMBER_LEVEL_STRUCTURE;
if (empty($nLevelstr)) $nLevelstr = 3;

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->poa->str->crear)
{
	$error = 0;
	$object->ref       	= $_POST["ref"];
	$object->label     	= GETPOST('label');
	$object->pseudonym 	= GETPOST('pseudonym');
	$object->fk_father 	= GETPOST('fk_father');
	$object->fk_area   	= GETPOST('fk_area');
	$object->fk_area_ej	= GETPOST('fk_area_ej')+0;
	$object->fk_poa_objetive = GETPOST('fk_poa_objetive')+0;
	$object->period_year= $_SESSION['period_year'];
	$object->type 		= GETPOST('type');
	$object->pos     	= 1;
	$object->version 	= 1;
	$object->fk_user_create=$user->id;
	$object->fk_user_mod=$user->id;
	$object->datec = dol_now();
	$object->datem = dol_now();
	$object->tms = dol_now();	

	//si tiene father
	$obj = new Poastructure($db);
	if ($obj->fetch($object->fk_father) && $object->fk_father > 0)
	{
		$object->pos = $obj->pos + 1;
		$object->sigla = $obj->sigla.$object->ref;
		$object->sigla = str_pad($obj->ref, $conf->global->POA_CODE_SIZE_STRUCTURE, "0", STR_PAD_LEFT).str_pad($object->ref, $conf->global->POA_CODE_SIZE_STRUCTURE, "0", STR_PAD_LEFT);
		$object->type = $obj->type;
	}
	else
	{
		$object->pos = 1;
		$object->sigla = str_pad($object->ref, $conf->global->POA_CODE_SIZE_STRUCTURE, "0", STR_PAD_LEFT);
	}

	$object->entity  = $conf->entity;
	$object->status  = 0;

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
	if (empty($error)) 
	{
		$id = $object->create($user);
		if ($id > 0)
		{
			header("Location: ".DOL_URL_ROOT."/poa/structure/liste.php"."?id=".$id."&action=sub&top=".$id);
			exit;
		}
		$action = 'create';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		if ($error)
			$action="create"; 
	}
}

// Add
if ($action == 'adduser' && $user->rights->poa->area->crear)
{

	$error = 0;
	$objuser->fk_area = $_POST["id"];
	$objuser->fk_user = GETPOST('fk_user');
	$objuser->date_create = date('Y-m-d');
	$objuser->tms = date('YmdHis');
	$objuser->active  = 1;
	if (empty($objuser->fk_user))
	{
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Erroruserisrequired").'</div>';
	}

	if (empty($error)) 
	{
		if ($objuser->create($user))
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
			$action="create";   
	  // Force retour sur page creation
	}
}


// Delete charge
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->str->del)
{
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/poa/structure/liste.php');
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
		$object->ref       = $_POST["ref"];
		$object->label     = GETPOST('label');
		$object->pseudonym = GETPOST('pseudonym');
		$object->type      = GETPOST('type');
		$object->fk_father = GETPOST('fk_father');
		$object->fk_area   = GETPOST('fk_area');

	//si tiene father
		$obj = new Poastructure($db);
		if ($obj->fetch($object->fk_father) && $object->fk_father > 0)
		{
			$object->pos = $obj->pos + 1;
			$object->sigla = $obj->sigla.$object->ref;
			$object->type = $obj->type;

		// print_r($obj);echo '<hr>';
		// print_r($object->sigla);exit;
		}
		else
		{
			$object->pos = 1;
			$object->sigla = $object->ref;
		}

		if ( $object->update($user) > 0)
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

$form=new Formv($db);
$getUtil = new getUtil($db);

// $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
// llxHeader("",$langs->trans("POA"),$help_url);
$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js','poa/js/poa.js','poa/js/scriptajax.js');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
llxHeader("",$langs->trans("Structure"),$help_url,'','','',$aArrjs,$aArrcss);

if ($action == 'create' && $user->rights->poa->str->crear)
{
	print_fiche_titre($langs->trans("Newstructure"));
	$fk_area = GETPOST('fk_area');
	if ($fk_area > 0)
	{
		$filter = " AND fk_father = ".$fk_area;
		$filter.= " OR rowid = ".$fk_area;
	}

	// ref
	if (empty($fk_father))
	{
		$object->fk_father = 0;
		$pos = 1;
	}
	else
	{
		$objtmp = new Poastructureext($db);
		$objtmp->fetch($fk_father);
		$object->fk_father = $fk_father;
		$pos = $objtmp->pos+1;
	}

	//recuperamos del diccionario
	$resd = $objdict->fecth_dictionarie($pos, 'c_name_structure',1,'rowid','label',"rowid",'i','');
	if ($resd==1) $namelevel = $objdict->label;

	$object->get_structurenext();
	$max = $object->max+1;


	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			$("#selectfk_area").change(function() {
				document.formstr.action.value="create";
				document.formstr.submit();
			});
		});';
		print '</script>'."\n";
	}
	print '<form id="formstr" name="formstr" action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
	print '<input type="hidden" name="pos" value="'.$pos.'">';

	dol_htmloutput_mesg($mesg);

	dol_fiche_head();

	print '<table class="border" width="100%">';


	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	if ($user->admin || $user->rights->poa->str->modref)
		print '<input id="ref" type="number" min="0" value="'.(GETPOST('ref')?GETPOST('ref'):$max).'" name="ref" maxlength="2">';
	else
	{
		print $max;
		print '<input id="ref" type="hidden" value="'.$max.'" name="ref">';
	}
	print '&nbsp;'.$namelevel;
	print '</td></tr>';

	//type
	if ($pos== 1)
	{
		print '<tr><td class="fieldrequired">'.$langs->trans('Type').'</td><td colspan="2">';
		print $form->selectarray('type',$aType,GETPOST('type'),1,0);
		//print '<input id="label" type="text" value="'.$object->label.'" name="label" size="50" maxlength="255">';
		print '</td></tr>';
	}
	else
		print '<input type="hidden" name="type" value="0">';

	//label
	print '<tr><td class="fieldrequired">'.$langs->trans('Label').'</td><td colspan="2">';
	print '<input id="label" type="text" value="'.GETPOST('label').'" name="label" size="50" maxlength="255">';
	print '</td></tr>';

	//pseudonym
	print '<tr><td class="fieldrequired">'.$langs->trans('Pseudonym').'</td><td colspan="2">';
	print '<input id="pseudonym" type="text" value="'.GETPOST('pseudonym').'" name="pseudonym" size="50" maxlength="255">';
	print '</td></tr>';

	//father
	print '<tr><td>'.$langs->trans('Father').'</td><td colspan="2">';
	print $object->select_structure(GETPOST('fk_father'),'fk_father','',75,1,0);
	print '</td></tr>';

	//area
	print '<tr><td>'.$langs->trans('Area').'</td><td colspan="2">';
	print $form->select_departament(GETPOST('fk_area'),'fk_area','',45,1);
	//print $objarea->select_area($object->fk_area,'fk_area','',75,1,0);
	print '</td></tr>';
	//area ej
	print '<tr><td>'.$langs->trans('Executing area').'</td><td colspan="2">';
	print $form->select_departament(GETPOST('fk_area_ej'),'fk_area_ej','',45,1,$filter);
	//print $objarea->select_area($object->fk_area,'fk_area','',75,1,0);
	print '</td></tr>';

	print '</table>';
	dol_fiche_end();

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


	   // Affichage fiche
		if ($action <> 'edit' && $action <> 're-edit')
		{
	  //$head = fabrication_prepare_head($object);

			dol_fiche_head($head, 'card', $langs->trans("Structure"), 0, 'mant');

	   // Confirmation de la validation

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
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deletecharge"),$langs->trans("Confirmdeletecharge",$object->ref.' '.$object->detail),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			print '<table class="border" width="100%">';


	  // ref
			$resd = $objdict->fecth_dictionarie($object->pos, 'c_name_structure',1,'rowid','label',"rowid",'i','');
			if ($resd==1) $namelevel = $objdict->label;
			print '<tr><td>'.$langs->trans('Ref').'</td><td colspan="2">';
			print $object->getNomUrl(1).' '.$namelevel;
			print '</td></tr>';

	  //type
			if ($object->type <> '0')
			{
				print '<tr><td>'.$langs->trans('Type').'</td><td colspan="2">';
				print $aType[$object->type];
				print '</td></tr>';
			}

	  //label
			print '<tr><td>'.$langs->trans('Label').'</td><td colspan="2">';
			print $object->label;
			print '</td></tr>';

	  //pseudonym
			print '<tr><td>'.$langs->trans('Pseudonym').'</td><td colspan="2">';
			print $object->pseudonym;
			print '</td></tr>';

	  //father
			if ($object->fk_father>0)
			{
				$obj = new Poastructure($db);
				$obj->fetch($object->fk_father);
				print '<tr><td>'.$langs->trans('Father').'</td><td colspan="2">';
				if ($obj->id == $object->fk_father)
					print $obj->label;
				else
					print '&nbsp;';
				print '</td></tr>';
			}
	  //area
			print '<tr><td>'.$langs->trans('Area').'</td><td colspan="2">';
	  		$getUtil->fetch_departament($object->fk_area);
			if ($getUtil->id == $object->fk_area)
				print $getUtil->label;
			else
				print 'No definido';
			print '</td></tr>';
			//area ej
			print '<tr><td>'.$langs->trans('Executing area').'</td><td colspan="2">';
	  		$getUtil->fetch_departament($object->fk_area_ej);
			if ($getUtil->id == $object->fk_area)
				print $getUtil->label;
			else
				print 'No definido';
			print '</td></tr>';

			print "</table>";

			print '</div>';


			/* ********************************** */
			/*                                    */
			/* Barre d'action                     */
			/*                                    */
			/* ********************************** */

			print "<div class=\"tabsAction\">\n";

			print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/structure/liste.php'.'?dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';

			if ($action == '')
			{
				if ($user->rights->poa->str->crear)
					print "<a class=\"butAction\" href=\"fiche.php?action=create&dol_hide_leftmenu=1\">".$langs->trans("Createnew")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

				if ($user->rights->poa->str->crear)
					print "<a class=\"butAction\" href=\"fiche.php?action=edit&dol_hide_leftmenu=1&id=".$object->id."\">".$langs->trans("Modify")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
				if ($user->rights->poa->str->val && $object->statut == 0)
					print "<a class=\"butAction\" href=\"fiche.php?action=validate&dol_hide_leftmenu=1&id=".$object->id."\">".$langs->trans("Validate")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";

				if ($user->rights->poa->str->del)
					print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&dol_hide_leftmenu=1&id=".$object->id."\">".$langs->trans("Delete")."</a>";
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
	  	print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

	  	print '<table class="border" width="100%">';

	  // ref
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	  	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="5" maxlength="2">';
	  	print '</td></tr>';

	 //type
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Type').'</td><td colspan="2">';
	  	print $form->selectarray('type',$aType,$object->type,1,0);
	//print '<input id="label" type="text" value="'.$object->label.'" name="label" size="50" maxlength="255">';
	  	print '</td></tr>';

	  //label
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Label').'</td><td colspan="2">';
	  	print '<input id="label" type="text" value="'.$object->label.'" name="label" size="50" maxlength="255">';
	  	print '</td></tr>';

	  //pseudonym
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Pseudonym').'</td><td colspan="2">';
	  	print '<input id="pseudonym" type="text" value="'.$object->pseudonym.'" name="pseudonym" size="50" maxlength="255">';
	  	print '</td></tr>';

	  //father
	  	print '<tr><td>'.$langs->trans('Father').'</td><td colspan="2">';
	  	print $object->select_structure($object->fk_father,'fk_father','',75,1,0);
	  	print '</td></tr>';

	  //area
	  	print '<tr><td>'.$langs->trans('Area').'</td><td colspan="2">';
	  	print $objarea->select_area($object->fk_area,'fk_area','',75,1,0);
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
