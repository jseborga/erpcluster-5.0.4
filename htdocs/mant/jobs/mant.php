<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/poa/process/fiche.php
 *	\ingroup    Process
 *	\brief      Page fiche poa process
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';

require_once DOL_DOCUMENT_ROOT.'/assets/lib/assets.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';


require_once(DOL_DOCUMENT_ROOT."/mant/class/mjobsext.class.php");
require_once(DOL_DOCUMENT_ROOT."/mant/class/mjobsresource.class.php");
require_once(DOL_DOCUMENT_ROOT."/mant/class/mequipmentext.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/assets.class.php");


require_once DOL_DOCUMENT_ROOT.'/contab/class/contab.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entity.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/lib/contab.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

$langs->load("mant");
$langs->load("assets");

$action = GETPOST('action');
$id     	= GETPOST("id",'int');
$ref    	= GETPOST('ref');
$dater = dol_mktime(12, 0, 0, GETPOST('dr_month'),GETPOST('dr_day'),GETPOST('dr_year'));

if (isset($_GET['tab']) || isset($_POST['tab']))
	$_SESSION['tabasset'] = (!empty($_GET['tab'])?$_GET['tab']:$_POST['tab']);
$tab = $_SESSION['tabasset'];

$mesg = '';
$object  = new Assetsext($db);
$objUser = new User($db);
$extrafields = new ExtraFields($db);

$objectDos      		= new Mjobsext($db);
$objEntity   		= new Entity($db);
$objEquipos  		= new Mequipmentext($db);
$objActivos  		= new Assets($db);
$objRecursos 		= new Mjobsresource($db);
$formfile    		= new FormFile($db);
$form        	    = new Form($db);
$MjobsresourceLine  = new MjobsresourceLine($db);
$entity = $conf->entity;

if ($id>0)
{
	$res = $object->fetch($id,((empty($id) && !empty($ref))?$ref:null));
	if ($res>0) $id = $object->id;
}
if ($action == 'search')
	$action = 'createedit';
$now = dol_now();

/*
 * Actions
 */

if ($action == 'builddoc')	// En get ou en post
{
	$object->fetch_lines();

	if (GETPOST('model'))
	{
		$object->setDocModel($user, GETPOST('model'));
	}
	// Define output language
	$outputlangs = $langs;
	$newlang='';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
	if (! empty($newlang))
	{
		$outputlangs = new Translate("",$conf);
		$outputlangs->setDefaultLang($newlang);
	}
	if (empty($object->model_pdf))
		$result=assets_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
	if ($result <= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
	else
	{
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
		exit;
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
$formfile = new Formfile($db);

$aArrcss= array('assets/css/style.css');
$help_url='EN:Module_Assets_En|FR:Module_Assets|ES:M&oacute;dulo_Assets';
llxHeader("",$langs->trans("Assets"),$help_url,'','','','',$aArrcss);

if ($id>0)
{
	$aAssetId = unserialize($_SESSION['aAssetId']);

	//echo "<br> Id ".$id;

	if (empty($aAssetId['id']) && ($id || $ref))
	{

		$head=assets_prepare_head($object);
		$tabn = 'Maintenance';

		dol_fiche_head($head, $tabn, $langs->trans("Assets"),0,($object->public?'projectpub':'project'));

		//Vista Principal La que se muestra en la view
		// Affichage fiche
		/*if ($action <> 'edit' && $action <> 're-edit')
		{

			if (empty($tab)) include_once DOL_DOCUMENT_ROOT."/assets/assets/tpl/tab0.tpl.php";
			if ($tab == 1) include_once DOL_DOCUMENT_ROOT."/assets/assets/tpl/tab1.tpl.php";
			if ($tab == 2) include_once DOL_DOCUMENT_ROOT."/assets/assets/tpl/tab2.tpl.php";
			if ($tab == 3) include_once DOL_DOCUMENT_ROOT."/assets/assets/tpl/tab3.tpl.php";
			if ($tab == 4) include_once DOL_DOCUMENT_ROOT."/assets/assets/tpl/tab4.tpl.php";
			if ($tab == 5) include_once DOL_DOCUMENT_ROOT."/assets/assets/tpl/tab5.tpl.php";

			//incluir el list del resultado del mantenimiento
		}*/

		print '<table class="border" style="min-width=1000px" width="100%">';

		// ref

		$linkback = '<a href="'.DOL_URL_ROOT.'/assets/assets/liste.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';


		print '<tr><td width="15%">'.$langs->trans('Code').'</td>';
		print '<td colspan="2">';
		print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref','');
		print '</td>';
		print '</tr>';

		//group type
		print '<tr><td width="15%">'.$langs->trans('Group').'</td><td colspan="2">';
		print select_type_group($object->type_group,'type_group','',1,1,'code');
		print '</td></tr>';

		//ref item
		print '<tr><td width="15%">'.$langs->trans('Item').'</td><td colspan="2">';
		print $object->item_asset;
		print '</td></tr>';

		//patrim type
		print '<tr><td width="15%">'.$langs->trans('Clasification').'</td><td colspan="2">';
		if (!empty($object->type_patrim))
			print select_type_patrim($object->type_patrim,'type_patrim','',0,1,'code');
		else
			print '&nbsp;';
		print '</td></tr>';

		//detail
		print '<tr><td width="15%">'.$langs->trans('Detail').'</td><td colspan="2">';
		print $object->descrip;
		print '</td></tr>';


		//Status
		print '<tr><td width="15%">'.$langs->trans('Statut').'</td><td colspan="2">';
		print $object->getLibStatut(4);
		print '</td></tr>';

		print '</table>';


		include_once DOL_DOCUMENT_ROOT."/mant/jobs/tpl/mantresource.tpl.php";

		//dol_fiche_end();

		/* ************************************** */
		/*                                        */
		/* Barre d'action                         */
		/*                                        */
		/* ************************************** */

		print "<div class=\"tabsAction\">\n";
		//Cambios de Luis Miguel
		/*if ($action == '')
		{
			if ($user->rights->assets->ass->crear && ($tab ==0))
				print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
			if ($user->rights->assets->ass->mod && $tab==0)
				// && $object->statut == 0)
				print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

			if ($user->rights->assets->ass->act &&
				$object->statut == 0 && $tab == 0)
				print "<a class=\"butAction\" href=\"fiche.php?action=re-edit&id=".$object->id."\">".$langs->trans("Upgrade")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Upgrade")."</a>";

			if ($user->rights->assets->ass->del && $object->statut == 0 && ($tab == 1 || $tab == 1))
				print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";

			if ($user->rights->assets->ass->val && $object->statut == 0 && ($tab == 0 || $tab == 1))
				print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Validate")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
				//tab2 asignacion
			if ($user->rights->assets->ass->crear && $object->statut == 1 && ($tab == 2))
			{
				if (!empty($object->mark))
					print '<a class="butAction" href="fiche.php?action=createassign&id='.$id.'">'.$langs->trans("Create assignment").'</a>';
			}
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Create assignment")."</a>";

		}*/
		print "</div>";

	}

}
llxFooter();

$db->close();
?>
