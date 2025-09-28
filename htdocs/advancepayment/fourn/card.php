<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *   	\file       advance/paiementfournadvance_card.php
 *		\ingroup    advance
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-12-29 08:44
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
dol_include_once('/advancepayment/class/paiementfournadvanceext.class.php');
dol_include_once('/advancepayment/core/modules/advancepayment/modules_advancepayment.php');
dol_include_once('/user/class/user.class.php');
dol_include_once('/societe/class/societe.class.php');
dol_include_once('/compta/bank/class/account.class.php');
if ($conf->purchase->enabled)
	dol_include_once('/purchase/class/fournisseurcommandeext.class.php');
else
	dol_include_once('/fourn/class/fournisseur.commande.class.php');
// Load traductions files requiredby by page
$langs->load("advancepayment@advancepayment");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_ref=GETPOST('search_ref','alpha');
$search_entity=GETPOST('search_entity','int');
$search_amount=GETPOST('search_amount','alpha');
$search_fk_user_author=GETPOST('search_fk_user_author','int');
$search_fk_soc=GETPOST('search_fk_soc','int');
$search_origin=GETPOST('search_origin','alpha');
$search_originid=GETPOST('search_originid','int');
$search_fk_paiement=GETPOST('search_fk_paiement','int');
$search_num_paiement=GETPOST('search_num_paiement','alpha');
$search_note=GETPOST('search_note','alpha');
$search_fk_bank=GETPOST('search_fk_bank','int');
$search_statut=GETPOST('search_statut','int');
$search_multicurrency_amount=GETPOST('search_multicurrency_amount','alpha');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Paiementfournadvanceext($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
if ($conf->purchase->enabled)
	$commande = new FournisseurCommandeext($db);
else
	$commande = new CommandeFournisseur($db);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('paiementfournadvance'));
$extrafields = new ExtraFields($db);



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
	if ($action == 'builddoc')
	{
		$object = new Paiementfournadvanceext($db);
		$id = GETPOST('id');
		$object->fetch($id);
		$object->fetch_thirdparty();

		if (GETPOST('model'))
		{
			$object->setDocModel($user, GETPOST('model'));
		}
		if (GETPOST('model') == 'pedido')
			$object->model_pdf = 'pedido';
		if (GETPOST('model') == 'entrega')
			$object->model_pdf = 'entrega';
		$object->model_pdf = GETPOST('model');
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
		$result=advancepayment_pdf_create($db, $object, $object->model_pdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
		if ($result <= 0)
		{
			dol_print_error($db,$result);
			exit;
		}
		else
		{
			header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id.(empty($conf->global->MAIN_JUMP_TAG)?'':'#builddoc'));
			exit;
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('AdvancePayment'),'');

$form=new Form($db);
$formfile = new FormFile($db);


// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';







// Part to show record
if ($id && (empty($action) || $action == 'view' || $action == 'delete'))
{
	print load_fiche_titre($langs->trans("AdvancePayment"));
	$objuser = new User($db);
	$objuser->fetch($object->fk_user_author);
	$societe = new Societe($db);
	$societe->fetch($object->fk_soc);
	$account = new Account($db);
	$accountline = new AccountLine($db);
	$accountline->fetch($object->fk_bank);
	if ($accountline->id == $object->fk_bank)
	{
		$account->fetch($accountline->fk_account);
	}
	print '<td>'.$account->getNomUrl($db).'</td>';


	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	$commande->fetch($object->originid);

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_user_author").'</td><td>'.$objuser->getNomUrl(1).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_soc").'</td><td>'.$societe->getNomUrl(1).'</td></tr>';
	print '<tr><td>'.$langs->trans("Supplierorder").'</td><td>'.($conf->purchase->enabled?$commande->getNomUrladd(1):$commande->getNomUrl(1)).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_paiement").'</td><td>'.$object->fk_paiement.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldnum_paiement").'</td><td>'.$object->num_paiement.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldnote").'</td><td>'.$object->note.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_bank").'</td><td>'.$account->getNomUrl(1).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldamount").'</td><td>'.$object->amount.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatut").'</td><td>'.$object->statut.'</td></tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->advance->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->advance->delete)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";

	/* ************************************* */
	/*                                       */
	/* Affichage de la liste des produits    */
	/*                                       */
	/* ************************************* */
	print "<div class=\"tabsAction\">\n";
		  	//documents
	if ($object->statut>=1 && $action!='deliver')
	{
		print '<table width="100%"><tr><td width="50%" valign="top">';
		print '<a name="builddoc"></a>';
		  		// ancre
		   		// Documents generes
		$filename=dol_sanitizeFileName($object->ref);
		$filedir=$conf->advancepayment->dir_output . '/' . dol_sanitizeFileName($object->ref);
		$urlsource=$_SERVER['PHP_SELF'].'?id='.$object->id;
		$genallowed=$user->rights->advancepayment->fourn->creerdoc;
		$delallowed=$user->rights->advancepayment->fourn->deldoc;
		$object->modelpdf = 'advancepayment';
		print '<br>';
		print $formfile->showdocuments('advancepayment',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
		$somethingshown=$formfile->numoffiles;
		print '</td></tr></table>';
	}

	print "</div>";

	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

}


// End of page
llxFooter();
$db->close();
