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
 *   	\file       /request/card_page.php
 *		\ingroup
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-10-13 18:11
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
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php');

dol_include_once('/finint/class/requestcashext.class.php');
dol_include_once('/finint/class/requestcashdet.class.php');
dol_include_once('/finint/class/requestcashdeplacementext.class.php');
if (! empty($conf->monprojet->enabled))
{
	dol_include_once('/monprojet/class/projectext.class.php');
	dol_include_once('/monprojet/class/taskext.class.php');
	dol_include_once('/monprojet/class/html.formprojetext.class.php');
	dol_include_once('/monprojet/class/html.formtask.class.php');
}

if (! empty($conf->societe->enabled))
{
	require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
}
require_once DOL_DOCUMENT_ROOT.'/finint/class/accountuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

dol_include_once('/monprojet/lib/verifcontact.lib.php');
dol_include_once('/finint/lib/utils.lib.php');
dol_include_once('/finint/lib/finint.lib.php');
//dol_include_once('/finint/lib/discharg.lib.php');

require_once(DOL_DOCUMENT_ROOT."/core/lib/bank.lib.php");
require_once(DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php");
require_once(DOL_DOCUMENT_ROOT."/compta/sociales/class/chargesociales.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/compta/tva/class/tva.class.php");

require_once(DOL_DOCUMENT_ROOT."/finint/class/deplacementext.class.php");
require_once(DOL_DOCUMENT_ROOT."/finint/class/accountext.class.php");
require_once(DOL_DOCUMENT_ROOT."/finint/core/modules/finint/modules_finint.php");
require_once(DOL_DOCUMENT_ROOT."/finint/lib/account.lib.php");
require_once(DOL_DOCUMENT_ROOT."/finint/lib/doc.lib.php");
require_once(DOL_DOCUMENT_ROOT."/finint/lib/facturefourn.lib.php");
dol_include_once('/finint/class/request/carddet.class.php');


//images
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("finint@finint");
$langs->load("errors");
$langs->load("admin");
$langs->load("holiday");
$langs->load("bills");
$langs->load('banks');
$langs->load('main');
// Get parameters
$id			= GETPOST('id','int');//id finint_cash
$idrcd		= GETPOST('idrcd','int');//id deplacement
$ref		= GETPOST('ref','alpha');
$action		= GETPOST('action','alpha');
$confirm   	= GETPOST('confirm','alpha');
$backtopage	= GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_proj=GETPOST('search_proj','alpha');
$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_account=GETPOST('search_fk_account','int');
$search_user=GETPOST('search_user','alpha');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_detail=GETPOST('search_detail','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_amount_authorized=GETPOST('search_amount_authorized','alpha');
$search_status=GETPOST('search_status','int');

$sortfield = GETPOST("sortfield","alpha");
$sortorder = GETPOST("sortorder");
$page = GETPOST("page");
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;

if (! $sortfield) $sortfield="t.ref";
if (! $sortorder) $sortorder="DESC";
$offset = $conf->liste_limit * $page ;

$aStatut = array(9=>$langs->trans('All'),
	-1=>$langs->trans('Torefused'),
	0=>$langs->trans('Draft'),
	1=>$langs->trans('Validated'),
	2=>$langs->trans('Approved'),
	3=>$langs->trans('Disbursed'),
	4=>$langs->trans('Approveclosure'),
	5=>$langs->trans('Closed'),);

// Purge criteria
if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter")) // Both test are required to be compatible with all browsers
{
	$search_ref='';
	$search_proj="";
	$search_user="";
	$search_detail = "";
	$search_status="";
}

if ($conf->societe->enabled)
	$soc = new Societe($db);
if ($conf->monprojet->enabled)
	$projet = new Project($db);


// Protection if external user
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	$soc->fetch($user->societe_id);
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='create';

// Load object if id or ref is provided as parameter
$object 		= new Requestcashext($db);
$objectdet 		= new Requestcashdet($db);
$objaccount 	= new Account($db);
$categorie 		= new Categorie($db);
$deplacement 	= new Requestcashdeplacementext($db);

if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$resultp=$object->fetch($id,(!empty($ref)?$ref:null));
	if ($resultp < 0) dol_print_error($db);
	//verificamos la cuenta del requerimiento
	$objaccount->fetch($object->fk_account);
	$minallowed = $objaccount->min_allowed;
	$maxallowed = $object->amount;
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('request/card'));
$objuser = new User($db);
$extrafields = new ExtraFields($db);

if ($conf->monprojet->enabled)
{
	$formproject = new FormProjetsext($db);
	$formtask = new FormTask($db);
}


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
if (empty($reshook))
{

	if ($action == "validate_dep" && $user->rights->finint->desc->val)
	{
		$res = $deplacement->fetch(GETPOST('idrcd'));
		if ($res>0)
		{
			if ($deplacement->id == GETPOST('idrcd'))
			{
				if ($deplacement->status == 1)
					$deplacement->status = 2;
				else
					$deplacement->status = 1;
				$res = $deplacement->update_status($user);
				if ($res<=0)
				{
					$error++;
					setEventMessages($deplacement->error,$deplacement->errors,'errors');
				}
			}
			if (!$error)
				setEventMessages($langs->trans('Validatesuccessfull'),null,'mesgs');
		}
		$action = 'discharg';
	}


	if ($action == "modifyfourn" && ($user->rights->finint->desc->crear || $user->rights->finint->desc->val))
	{
		$db->begin();
		$error=0;
		$aDateoo = explode('-',$_POST['do_']);
		$dateo = dol_mktime(12,0,0,$aDateoo[1],$aDateoo[2],$aDateoo[0]);
		$lViewqr = false;
		$cashdeplac = new Requestcashdeplacementext($db);
		$cashdeplac->fetch(GETPOST('idrcd'));
		if ($cashdeplac->id ==GETPOST('idrcd'))
		{
			if (!empty(GETPOST('code_f')))
			{
				if ($conf->fiscal->enabled) require_once DOL_DOCUMENT_ROOT.'/fiscal/lib/fiscal.lib.php';
				$objcfact = fetch_type_facture(0,GETPOST('code_f'));
				if ($objcfact->nit_required) $lViewqr = true;
			}

			$cashdeplac->fk_projet_dest = $_POST['fk_projet__0'];
			$cashdeplac->fk_projet_task_dest = $_POST['fk_task'];
			$cashdeplac->amount  = $_POST['amount'];
			$cashdeplac->dateo = $dateo;
			$cashdeplac->nro_chq = $_POST['num_chq'];
			$cashdeplac->detail = $_POST['dp_desc'];
			$cashdeplac->code_facture = $_POST['code_f'];
			if ($lViewqr)
			{
				$aDateoo = explode('-',$_POST['do_']);
				$fourn_date = dol_mktime(12,0,0,$aDateoo[1],$aDateoo[2],$aDateoo[0]);

				$cashdeplac->codeqr = GETPOST('codeqr');
				$cashdeplac->fourn_nit = GETPOST('fourn_nit');
				$cashdeplac->fourn_date = $fourn_date;
				$cashdeplac->fourn_facture = GETPOST('fourn_facture');
				$cashdeplac->fourn_numaut = GETPOST('fourn_numaut');
				$cashdeplac->fourn_codecont = GETPOST('fourn_codecont');						
			}
			//fijo por la operacion de transferencia
			$cashdeplac->date_dest = $dateo;
			$cashdeplac->tms = dol_now();
			$cashdeplac->status = 1;
			$resc = $cashdeplac->update($user);
			
			if ($resc<=0)
			{
				setEventMessages($cashdeplac->error,$cashdeplac->errors,'errors');
				$error++;
			}
		}
		
		if (!$error)
		{
			unset($_SESSION['aUpload']);
			unset($_SESSION['aPost']);
			$db->commit();
			setEventMessages($langs->trans('Updatesuccessfull'),null,'mesgs');
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id.'&action=discharg');
			exit;
		}
		else
		{
			$db->rollback();
		}
	}	

	if ($action == "confirm_deplacement" && $_REQUEST['confirm'] == 'yes')
	{
		$db->begin();
		$error=0;
		$aPost = unserialize($_SESSION['aPost']);
		$_POST = $aPost[$id];
		foreach ($_POST['amount'] AS $j => $value)
		{
			$k = $j +1;
			$data = array();
			$dateo = dol_mktime(12,0,0,GETPOST($j.'_domonth','int'),GETPOST($j.'_doday','int'),GETPOST($j.'_doyear','int'));
			$aDateoo = explode('-',$_POST['_do'][$k]);
			$dateo = dol_mktime(12,0,0,$aDateoo[1],$aDateoo[2],$aDateoo[0]);
			if (!$error)
			{
				$cashdeplac = new Requestcashdeplacementext($db);
				$numref = $cashdeplac->getNextNumRef($soc);

				$cashdeplac->fk_request_cash = $id;
				$cashdeplac->entity = $object->entity;
				$cashdeplac->ref = $numref;
				$cashdeplac->fk_request_cash_dest = 0;
				$cashdeplac->url_id = 0;
				$cashdeplac->fk_bank = 0;
				$cashdeplac->fk_account_from = $object->fk_account;
				$cashdeplac->fk_account_dest = 0;
				$cashdeplac->fk_type = $object->fk_type;
				$cashdeplac->fk_categorie = $object->fk_categorie;
				$cashdeplac->fk_projet = $_POST['fk_projet'][$j]+0;
				$cashdeplac->amount  = $_POST['amount'][$j];
				$cashdeplac->concept = 'deplacement';
				$cashdeplac->dateo = $dateo;
				$cashdeplac->quant = $_POST['quant'][$j];
				$cashdeplac->fk_unit = 0;
				$cashdeplac->nro_chq = $_POST['num_chq'][$j];
				$cashdeplac->detail = $_POST['dp_desc'][$j];
				$cashdeplac->code_facture = $_POST['code_f'];
				$cashdeplac->fourn_facture = isset($_POST['fourn_facture'][$j])?$_POST['fourn_facture'][$j]:$_POST['num_chk'][$j];
				
						//fijo por la operacion de transferencia
				$cashdeplac->fk_user_from = $user->id;
				$cashdeplac->fk_user_to = 0;
				$cashdeplac->fk_user_create = $user->id;
				$cashdeplac->fk_user_approved = $user->id;
				$cashdeplac->date_dest = $dateo;
				$cashdeplac->date_create = dol_now();
				$cashdeplac->tms = dol_now();
				$cashdeplac->status = 1;
				$resc = $cashdeplac->create($user);
				if ($resc<=0)
				{
					setEventMessages($cashdeplac->error,$cashdeplac->errors,'errors');
					$error++;
				}
			}
		}
		if (!$error)
		{
			unset($_SESSION['aUpload']);
			unset($_SESSION['aPost']);
			$db->commit();
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id.'&action=discharg',1));
			exit;
		}
		else
		{
			$db->rollback();
		}
	}	

	if ($action == "confirm_addfourn" && $_REQUEST['confirm'] == 'yes')
	{
		$db->begin();
		$error=0;
		$aPost = unserialize($_SESSION['aPost']);
		$_POST = $aPost[$id];

		foreach ($_POST['quant'] AS $j => $value)
		{
			$data = array();
			if ($value>0)
			{
				$data['dateo'] = dol_mktime(12,0,0,GETPOST($j.'_domonth','int'),GETPOST($j.'_doday','int'),GETPOST($j.'_doyear','int'));
				$data['fk_soc'] = $_POST['socid'][$j]+0;
				if (empty($data['fk_soc']))
					$data['fk_soc'] = $conf->global->PETTYCASH_ID_SOCIETE_DEFAULT;
				//$data['fk_soc'] = 1;
				
				$data['ref_soc'] = ($_POST['fourn_facture'][$j]?$_POST['fourn_facture'][$j]:$_POST['num_chq'][$j]);
				$data['type'] = $_POST['type'][$j];
				$data['datefacture'] = $data['dateo'];
				$data['datedue'] = $data['dateo'];
				$data['fk_projet_dest'] = $_POST['fk_projet'][$j]+0;
				$data['fk_projet_task_dest'] = $_POST['fk_task'][$j]+0;
				$data['fk_account'] = $object->fk_account;
				$data['label'] = $_POST['dp_desc'][$j];
				$data['mode_reglement_id'] = $_POST['operation'][$j];
				$data['operation'] = $_POST['operation'][$j];
				$data['paiementid'] = $_POST['paiementid'][$j];
				$data['num_paiement'] = $_POST['num_chq'][$j];
				$data['comment'] = $_POST['label'][$j];
				$data['closepaidinvoices'] = 'on';

				$data['aProduct'][] = array('dp_desc' =>$_POST['dp_desc'][$j],
					'prod_entry_mode' => 'free',
					'price_ht'=>$_POST['amount'][$j],
					'price_ttc'=>$_POST['amount'][$j],
					'tva_tx' => 0,
					'idprod' =>0,
					'qty' => $_POST['quant'][$j],
					'type' => 0,
					'fk_unit' => $_POST['fk_unit'][$j]);
				$data['amount']+=$_POST['amount'][$j];
			}

			list($fk_facture,$paiement_id,$idBank,$error) = facturefourn($data);
			
			if (!$error)
			{
				$cashdet = new Pettycashdetadd($db);
				$cashdet->fk_petty_cash = $id;
				$cashdet->fk_petty_cash_dest = 0;
				$cashdet->url_id = 0;
				$cashdet->fk_bank = $idBank;
				$cashdet->fk_account_from = $object->fk_account;
				$cashdet->fk_account_dest = 0;
				$cashdet->fk_facture_fourn = $fk_facture;
				$cashdet->fk_projet = $data['fk_projet']+0;
				$cashdet->amount  = $data['amount']+0;
				$cashdet->concept = 'deplacement';
				$cashdet->dateo = $data['dateo'];
				$cashdet->nro_chq = ($data['num_paiement']?$data['num_paiement']:$object->ref);
				$cashdet->detail = $data['label'];
						//fijo por la operacion de transferencia
				$cashdet->fk_user_from = $user->id;
				$cashdet->fk_user_to = GETPOST('user_to','int')+0;
				$cashdet->fk_user_create = $user->id;
				$cashdet->fk_user_approved = $user->id;
				$cashdet->date_dest = $data['dateo'];
				$cashdet->date_create = dol_now();
				$cashdet->tms = dol_now();
				$cashdet->statut = 1;

				$cashdet->codeqr =$_POST['codeqr'][$j];
				if (isset($_POST['codeqr'][$j]))
				{
					$cashdet->fourn_nit =$_POST['fourn_nit'][$j];
					$cashdet->fourn_facture =$_POST['fourn_facture'][$j];
					$cashdet->fourn_numaut =$_POST['fourn_numaut'][$j];
					$cashdet->fourn_date =$_POST['fourn_date'][$j];
					$cashdet->fourn_amount_ttc =$_POST['fourn_amount_ttc'][$j]+0;
					$cashdet->fourn_amount =$_POST['fourn_amount'][$j]+0;
					$cashdet->fourn_codecont =$_POST['fourn_codecont'][$j];
					$cashdet->fourn_reg1 =$_POST['fourn_reg1'][$j];
					$cashdet->fourn_reg2 =$_POST['fourn_reg2'][$j];
					$cashdet->fourn_reg3 =$_POST['fourn_reg3'][$j];
					$cashdet->fourn_reg4 =$_POST['fourn_reg4'][$j];
					$cashdet->fourn_reg5 =$_POST['fourn_reg5'][$j];
				}
				$resc = $cashdet->create($user);
				if ($resc<=0) $error++;
			}
		}
		if (!$error)
		{
			unset($_SESSION['aUpload']);
			unset($_SESSION['aPost']);
			$db->commit();
		}
		else
		{
			$db->rollback();
		}

		if (!$error)
		{
			// OK
			setEventMessages("Saverecord", null, 'mesgs');
			header("Location: ".dol_buildpath('/pettycash/card.php?id='.$id.'&action=discharg',1));
			exit;
		}
		else
		{
			if (! empty($cashdet->errors)) 
				setEventMessages(null, $cashdet->errors, 'errors');
			else 
				setEventMessages($cashdet->error, null, 'errors');
		}
	}	


	if ($action == 'updateprojet' && $user->rights->finint->efe->mod)
	{
		$object->fetch($id);
		if ($object->id == $id)
		{
			$object->fk_projet = GETPOST('fk_projet','int');
			$res = $object->update($user);
			if ($res <= 0)
			{
				setEventMessages($langs->trans('Error en actualizacion de proyecto'),$object->errors,'errors');
			}
			else
			{
				setEventMessages("Recordsave", null, 'mesgs');				
			}			
		}
		$action = '';
	}
	if ($action == 'updateassign' && $user->rights->finint->efe->mod)
	{
		$object->fetch($id);
		if ($object->id == $id && $object->status < 5)
		{
			$object->fk_user_assigned = GETPOST('fk_user_assigned','int');
			$res = $object->update_user_assigned($user);
			if ($res <= 0)
			{
				setEventMessages($langs->trans('Error en actualizacion de Usuario'),$object->errors,'errors');
			}
			else
			{
				setEventMessages("Recordsave", null, 'mesgs');				
			}			
		}
		$action = '';
	}
	if ($action == 'deldoc' && $user->rights->finint->efe->del)
	{
		$object->fetch($id);
		if ($user->admin || ($user->id == $object->fk_user_create && $object->status == 0))
		{
			$error=0;
			if ($object->id == $id)
			{
				$namefile = GETPOST('namedoc');
		   		//buscamos
				$db->begin();

				$aDoc = explode(';',$object->document);
				$document = '';
				foreach ((array) $aDoc AS $i => $name)
				{
					if ($name != $namefile)
					{
						if ($document) $document.=';';
						$document .= $name;
					}
				}
				$object->document = $document;
				$res = $object->update($user);
				if (!$res>0) $error++;

		    //del photo
				$dirproj = $projectstatic->ref;
				$dir     = $conf->finint->multidir_output[$conf->entity].'/'.$id.'/cash';
				$fileimg=$dir.'/'.$namefile;
				dol_delete_file($fileimg);
				if (!$error) $db->commit();
				else $db->rollback();
			}
		}
		$action = '';
	}

	if ($action == 'builddoc')	// En get ou en post
	{
		$object->fetch($id);
		if (empty($object->id))
			$object->id = $id;
		$object->fetch_thirdparty();
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
		$result=finint_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
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

	if ($action == 'adddeplacement' && $id && $user->rights->finint->desc->crear)
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/finint/request/card.php?id='.$id.'&action=discharg',1);
			header("Location: ".$urltogo);
			exit;
		}

		$objuser = new User($db);

		$fk_account= $object->fk_account;
		$user_to = $object->fk_user_create;
		$fk_projet = $object->fk_projet;
		$error =0;
		$balance = GETPOST('balance');
		$fk_categorie = GETPOST('fk_categorie')+0;
		if (price2num($_POST["credit"]) > 0)
		{
			$amount = price2num($_POST["credit"]);
			if ($_POST["credit"] > $balance)
			{
				$error++;
				setEventMessages($langs->trans('El monto es superior al saldo'),$task->errors,'errors');
			}
		}
		else
		{
			$amount = - price2num($_POST["debit"]);
			if ($_POST["debit"] > $balance)
			{
				$error++;
				setEventMessages($langs->trans('El monto es superior al saldo'),$task->errors,'errors');
			}
		}
		if ($user->admin)
		{
			$objuser->fetch(($_POST['fk_user']?$_POST['fk_user']:$user->id));
		}
		else
		{
			$objuser->fetch($user->id);
			//$dateop = dol_now();
		}
		$dateop = dol_mktime(12,0,0,GETPOST('domonth','int'),GETPOST('doday','int'),GETPOST('doyear','int'));
		$operation=$_POST["operation"];
		$type = $_POST["type"];
		$num_chq=$_POST["num_chq"];
		$label=$_POST["label"];
		$cat1=$_POST["cat1"]+0;

		if (!$fk_account >0)
		{
			$error++;
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Account")),'errors');
		}
		if (!$fk_projet >0)
		{
			$error++;
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Project")),'errors');
		}
		if (! $dateop)
		{
			$error++;
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Date")),'errors');
		}
		if (! $type || $type < 0)
		{
			$error++;
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Type")),'errors');
		}
		if (! $label)
		{
			$error++;
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")),'errors');
		}
		if (! $operation)
		{
			$error++;
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Operation")),'errors');
		}
		if (! $amount)
		{
			$error++;
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Amount")),'errors');
		}
		if (! $error)
		{
			$db->begin();
			$account = new Account($db);
			$account->fetch($fk_account);
			$account->fk_user_author = $objuser->id;
			if ($account->courant == 2)
			{
				$operation='LIQ';
			}

			$insertid = $account->addline($dateop, $operation, $label, $amount, $num_chq, $fk_categorie, $objuser);
			 //agrega a bank
			if ($insertid > 0)
			{
				$url='';
				$mode = 'spending';
				$url=DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=';
				$result=$account->add_url_line($insertid, $insertid, $url, '(spending)', $mode);
				if ($result <= 0)
				{
					$error++;
					dol_print_error($db);
				}

				//agregando a lista honorarios
				//tabla llx_deplacement
				require_once DOL_DOCUMENT_ROOT.'/compta/deplacement/class/deplacement.class.php';
				$objectDep = new Deplacement($db);
				$objectDep->date	   = $dateop;
				$objectDep->fk_project = $fk_projet;
				$objectDep->km	       = $amount * -1;
				$objectDep->type	   = GETPOST('type','alpha');
				$objectDep->socid	   = GETPOST('socid','int')+0;
				$objectDep->fk_user	   = $user_to;
				$objectDep->note_private = GETPOST('label','alpha');
				$objectDep->note_public  = GETPOST('note_public','alpha');
				$objectDep->status    = 1;
				$idDep = $objectDep->create($objuser);
				if (!$idDep > 0)
				{
					$errmsg=$objectDep->error;
					$error++;
				}

				if (! $error)
				{
					//deplacement
					$url='';
					$mode = 'deplacement';
					$url=DOL_URL_ROOT.'/compta/deplacement/'.($lVersion==true?'card':'fiche').'.php?id=';
					$result=$account->add_url_line($insertid, $idDep, $url,
						GETPOST('type','alpha'), $mode);
					if ($result <= 0)
					{
						$error++;
						dol_print_error($db);
					}
				}
				if (!$error)
				{
					//agregamos a llx_finint_cash_deplacement;
					$cashdeplac = new Requestcashdeplacementext($db);
					$cashdeplac->fk_finint_cash = $object->id;
					$cashdeplac->url_id = $idDep;
					$cashdeplac->fk_bank = $insertid;
					$cashdeplac->quant = GETPOST('quant');
					$cashdeplac->fk_unit = GETPOST('fk_unit');
					$cashdeplac->amount  = $amount * -1;
					$cashdeplac->concept = 'deplacement';
					$cashdeplac->fk_type = $operation;
					$cashdeplac->nro_chq = $nro_chq;
					$cashdeplac->detail  = $label;
					$cashdeplac->fk_categorie = $fk_categorie;
					$cashdeplac->dateo = $dateop;
					 //fijo por la operacion de gastos
					$cashdeplac->fk_user_to = 0;
					$cashdeplac->fk_user_from = $user->id;

					$cashdeplac->fk_user_create = $user->id;
					$cashdeplac->date_create = dol_now();
					$cashdeplac->tms = dol_now();
					$cashdeplac->status = 1;
					$res = $cashdeplac->create($user);
					if ($res<=0) $error++;
				}
				if (! $error)
				{
					setEventMessages($langs->trans('Recordsaved'), null, 'mesgs');
					$db->commit();
					// header("Location: ".$_SERVER['PHP_SELF']);
					// exit;
				}
				else
				{
					setEventMessages($langs->trans('Errorexist'), null, 'errors');
					$db->rollback();
					$mesg=$errmsg;
				}
				Header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id.'&action=discharg');
				exit;
			}
			else
			{
				$db->rollback();
				$mesg=$object->error;
			}
		}
	}
	if ($action == 'addline')
	{
		$objectdet->fk_finint = $id;
		$objectdet->detail = GETPOST('detail','alpha');
		$objectdet->amount = GETPOST('amount','int');
		$objectdet->amount_approved = 0;
		$objectdet->quant = GETPOST('quant');
		$objectdet->fk_unit = GETPOST('fk_unit');
		$objectdet->date_create = dol_now();
		$objectdet->active = 0;
		$objectdet->status = 1;
		$objectdet->fk_user_create = $user->id;
		$res = $objectdet->create($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($objectdet->error,null,'errors');
		}
		else
		{
			setEventMessages("RecordSaved", null, 'mesgs');
			header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
			exit;
		}
	}
	if ($action == 'delline')
	{
		$objectdet->fetch(GETPOST('lineid','int'));
		if ($objectdet->id == GETPOST('lineid','int') && $objectdet->fk_finint == $id)
		{
			$res = $objectdet->delete($user);
			if ($res > 0)
			{
				// Delete OK
				setEventMessages("RecordDeleted", null, 'mesgs');
				header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
				exit;
			}
			else
			{
				if (! empty($objectdet->errors)) setEventMessages(null,$objectdet->errors,'errors');
				else setEventMessages($objectdet->error,null,'errors');
			}
		}
	}

	if ($action == 'confirm_delete_deplacement' && $confirm != 'yes')
	{
		$action = 'discharg';
	}
	if ($action == 'confirm_delete_deplacement' && $confirm == 'yes' && $user->rights->finint->desc->del)
	{
	//verificamos primero el finint_cash_deplacement
		$cashdeplac = new Requestcashdeplacementext($db);
		$res = $cashdeplac->fetch($idrcd);
		if ($res > 0 && $cashdeplac->id == $idrcd)
		{
			$idDep = $cashdeplac->url_id;
			$idBank = $cashdeplac->fk_bank;
			$db->begin();
			$accline=new AccountLine($db);
			$accline->fetch($idBank);
			$result=$accline->delete();
			if (!$result>0) $error++;
			if (!$error)
			{
				$res = $cashdeplac->delete($user);
				if (!$res>0) $error++;
			}
			if (!$error)
			{
				$deplacement = new Deplacement($db);
				$res = $deplacement->fetch($idDep);
				if (!$res > 0) $error++;
				if (!$error)
				{
					$res = $deplacement->delete($idDep);
					if (!$res > 0) $error++;
				}
			}
			if (!$error)
				$db->commit();
			else
				$db->rollback();
		}
		$action='discharg';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/finint/request/card.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		$file = '';
		/* object_prop_getpost_prop */
		$code= generarcodigo(4);

		$object->entity=$conf->entity;
		$object->ref='(PROV)'.$code;
		$object->fk_projet=GETPOST('fk_projet','int')+0;
		$object->fk_account=GETPOST('fk_account','int')+0;
		$object->fk_type_cash = GETPOST('fk_type_cash','int');
		if ($user->admin)
		{
			$object->fk_user_create = GETPOST('user_to');
			$object->fk_user_assigned = GETPOST('user_to');
		}
		else
		{
			$object->fk_user_create=$user->id;
			$object->fk_user_assigned=$user->id;
		}
		$object->document = $file;
		$object->fk_user_mod=$user->id;
		$object->detail=GETPOST('detail','alpha');
		$object->description=GETPOST('description','alpha');
		$object->amount=GETPOST('amount','int');
		$object->date_create = dol_now();
		$object->tms = dol_now();
		$object->amount_authorized=GETPOST('amount_authorized','int')+0;
		$object->status=0;

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")),'errors');
		}

		if (! $error)
		{
			$id=$object->create($user);
			if ($id<=0)
			{
				setEventMessages($object->error,$object->errors,'errors');
				$error++;
			}

			if ($id>0)
			{
				//subida de archivo
				$dir     = $conf->finint->multidir_output[$conf->entity].'/'.$id.'/cash';
				$file = '';
				$namefile = dol_sanitizeFileName($_FILES['docpdf']['name']);
				$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
				if ($file_OKfin)
				{
					$newDir = $res;
					//verificamos permisos para el modo de subida de archivos
					$mode = 0;
					$mode = $user->rights->finint->pho->up4;
					if ($user->rights->finint->pho->up3) $mode = 3;
					if ($user->rights->finint->pho->up2) $mode = 2;
					if ($user->rights->finint->pho->up1) $mode = 1;
					if ($user->rights->finint->pho->up5) $mode = 5;

					if (GETPOST('deletedocfin'))
					{
						$fileimg=$dir.'/'.$namefile;
						$dirthumbs=$dir.'/thumbs';
						dol_delete_file($fileimg);
						dol_delete_dir_recursive($dirthumbs);
					}
					if (doc_format_supported($_FILES['docpdf']['name'],$mode) > 0)
					{
						dol_mkdir($dir);
						if (@is_dir($dir))
						{
							$aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
							$file = $namefile;
							$newfile = $dir.'/'.$file;
							$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
							if (! $result > 0)
							{
								$error++;
								$errors[] = "ErrorFailedToSaveFile";
							}
							else
							{
								$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
								$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
							}
						}
						else
						{
							$error++;
						}
					}
					else
					{
						$error++;
						$errors[] = "ErrorBadImageFormat";
					}

				}

				//fin subida de archivo
				//actualizamos el object
				if (!$error)
				{
					$object->document = $file;
					$res = $object->update($user);
					if ($res<=0)
					{
						setEventMessages($object->error,$object->errors,'errors');
						$error++;
					}
				}

			}
			if (!$error)
			{
				$db->commit();
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/finint/request/card.php?id='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			else
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
		$db->rollback();
	}

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;

		//subida de archivo
		$dir     = $conf->finint->multidir_output[$conf->entity].'/'.$id.'/cash';
		$namefile = dol_sanitizeFileName($_FILES['docpdf']['name']);
		$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
		if ($file_OKfin)
		{
			$newDir = $res;
			//verificamos permisos para el modo de subida de archivos
			$mode = 0;
			$mode = $user->rights->finint->pho->up4;
			if ($user->rights->finint->pho->up3) $mode = 3;
			if ($user->rights->finint->pho->up2) $mode = 2;
			if ($user->rights->finint->pho->up1) $mode = 1;
			if ($user->rights->finint->pho->up5) $mode = 5;

			if (GETPOST('deletedocfin'))
			{
				$fileimg=$dir.'/'.$namefile;
				$dirthumbs=$dir.'/thumbs';
				dol_delete_file($fileimg);
				dol_delete_dir_recursive($dirthumbs);
			}
			if (doc_format_supported($_FILES['docpdf']['name'],$mode) > 0)
			{
				dol_mkdir($dir);
				if (@is_dir($dir))
				{
					$aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
					$file = $namefile;
			// foreach ((array) $aFile AS $j => $val)
			//   {
			// 	if (empty($file))
			// 	  $file = $newDir;
			// 	else
			// 	  $file.= '.'.$val;
			//   }
					$newfile = $dir.'/'.$file;
					$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
					if (! $result > 0)
					{
						$error++;
						$errors[] = "ErrorFailedToSaveFile";
					}
					else
					{
			// Create small thumbs for company (Ratio is near 16/9)
			// Used on logon for example
						$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
			// Create mini thumbs for company (Ratio is near 16/9)
			// Used on menu or for setup page for example
						$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
					}
				}
				else
				{
					$error++;
				}
			}
			else
			{
				$error++;
				$errors[] = "ErrorBadImageFormat";
			}

		}

		// $object->entity=GETPOST('entity','int');
		// $object->ref=GETPOST('ref','alpha');
		$object->fk_projet=GETPOST('fk_projet','int')+0;
		$object->fk_type_cash = GETPOST('fk_type_cash');
		if (!empty($object->document))
			$file = $object->document.';'.$file;
		$object->document = $file;
		if ($user->admin)
			$object->fk_user_create = GETPOST('user_to','int');
		$object->fk_account=GETPOST('fk_account','int')+0;
		$object->fk_user_mod=GETPOST('fk_user_mod','int');
		$object->detail=GETPOST('detail','alpha');
		$object->amount=GETPOST('amount','int')+0;
		$object->amount_authorized=GETPOST('amount_authorized','int')+0;

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")),null,'errors');
		}

		if (! $error)
		{
			$result=$object->update($user);
			if ($result > 0)
			{
				setEventMessages($langs->trans('Updatesucessfull'),null,'mesgs');
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

	// aprobacion del desembolso
	if ($id && $action == 'confirm_outlay' && $_REQUEST["confirm"] == 'yes' && $object->status == 2)
	{
		$langs->load('errors');
		$error= 0;
		$mesg='';

		$_POST = $_SESSION['aPost'];
		// on verifie si l'objet est en numerotation provisoire
		$fk_type = $_POST['fk_type'];
		$numdoc = $_POST['numdoc'];
		if ($object->id == $id)
		{
			$dateo = dol_mktime(12,0,0,GETPOST('domonth','int'),GETPOST('doday','int'),GETPOST('doyear','int'));
			$label = GETPOST('label','alpha');
			$amount= GETPOST('amount','int');

			if (! $label)
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("Description"))."</div>";
			}
			if (! $amount)
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("Amount"))."</div>";
			}
			if (! GETPOST('account_from','int'))
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("TransferFrom"))."</div>";
			}
			if (! GETPOST('account_to','int'))
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("TransferTo"))."</div>";
			}
			if (! GETPOST('fk_type','alpha'))
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("Type"))."</div>";
			}
			if (! GETPOST('cat1','int'))
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("Categorie"))."</div>";
			}
			if (! GETPOST('user_to','int'))
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("UserTo"))."</div>";
			}
			if (! $error)
			{
				require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

				$accountfrom=new Account($db);
				$accountfrom->fetch(GETPOST('account_from','int'));

				$accountto=new Account($db);
				$accountto->fetch(GETPOST('account_to','int'));
				if ($accountto->id != $accountfrom->id)
				{
					$db->begin();

					$error=0;
					$bank_line_id_from=0;
					$bank_line_id_to=0;
					$result=0;
					$cat1 = '';
					if (GETPOST('cat1','int')) $cat1 = GETPOST('cat1','int');

					// By default, electronic transfert from bank to bank
					$typefrom='PRE';
					$typeto='VIR';
					if (!empty(GETPOST('fk_type','alpha')))
					{
						// This is transfert of change
						$fk_type = GETPOST('fk_type','alpha');
						$typefrom=$fk_type;
						$typeto=$fk_type;
					}
					if ($accountto->courant == 2)
					{
						$typeto='LIQ';
					}
					if ($accountfrom->courant == 2)
					{
						$typefrom='LIQ';
					}
					if (! $error) 
						$bank_line_id_from = $accountfrom->addline($dateo, $typefrom, $label, -1*price2num($amount), ($numdoc?$numdoc:$object->ref), $cat1, $user);
					if ($bank_line_id_from <= 0) $error++;
					
					//cambiamos de id al usuario
					$fk_user_id_from = $user->id;
					$objuser = new User($db);
					$objuser->fetch(GETPOST('user_to','int'));
					//$user->id = GETPOST('user_to','int');
					if (! $error)
						$bank_line_id_to = $accountto->addline($dateo, $typeto, $label, price2num($amount), ($numdoc?$numdoc:$object->ref), $cat1, $objuser);
					if ($bank_line_id_to <= 0) $error++;

					if (! $error) 
						$result=$accountfrom->add_url_line($bank_line_id_from, $bank_line_id_to, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
					if ($result <= 0) $error++;
					if (! $error) $result=$accountto->add_url_line($bank_line_id_to, $bank_line_id_from, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
					if ($result <= 0) $error++;
					
					//cambiando a aprobado
					if (empty($error))
					{
						$object->status = 3;
						$object->fk_type = $fk_type;
						$object->fk_user_authorized = $user->id;
						$object->fk_categorie = $cat1;
						$object->date_authorized = dol_now();
						$object->tms = dol_now();
						if (!empty($numdoc)) $object->nro_chq = $numdoc;
						$object->fk_account = $accountto->id;
						$object->fk_account_from = $accountfrom->id;
						$object->amount_authorized = $amount;
						//update
						$res = $object->update($user);
						if ($res <=0)
						{
							setEventMessages($object->error,$object->errors,'errors');
							$error++;
						}
					}
					//creamos un registro en request/carddeplacement
					if (empty($error))
					{
						$cashdeplac = new Requestcashdeplacementext($db);
						$numref = $cashdeplac->getNextNumRef($soc);
						$cashdeplac->fk_request_cash = 0;
						$cashdeplac->entity = $object->entity;
						$cashdeplac->ref = $numref;
						$cashdeplac->fk_request_cash_dest = $object->id;
						$cashdeplac->url_id = $bank_line_id_to;
						$cashdeplac->fk_bank = $bank_line_id_from;
						$cashdeplac->fk_type = $fk_type;
						$cashdeplac->fk_categorie = $cat1;
						$cashdeplac->fk_account_from = $accountfrom->id;
						$cashdeplac->fk_account_dest = $accountto->id;
						$cashdeplac->fk_projet = $object->fk_projet;
						$cashdeplac->amount  = $amount;
						$cashdeplac->concept = 'banktransfert';
						$cashdeplac->dateo = $dateo;
						$cashdeplac->nro_chq = ($numdoc?$numdoc:$object->ref);
						$cashdeplac->detail = $label;
						//fijo por la operacion de transferencia
						$cashdeplac->fk_user_from = $user->id;
						$cashdeplac->fk_user_to = GETPOST('user_to','int')+0;
						$cashdeplac->fk_user_create = $user->id;
						$cashdeplac->fk_user_approved = $user->id;
						$cashdeplac->date_dest = $dateo;
						$cashdeplac->date_create = dol_now();
						$cashdeplac->tms = dol_now();
						$cashdeplac->status = 1;
						$resc = $cashdeplac->create($user);
						if ($resc<=0)
						{
							setEventMessages($cashdeplac->error,$cashdeplac->errors,'errors');
							$error++;
						}
					}
					if (! $error)
					{
						$mesg.="<div class=\"ok\">";
						$mesg.=$langs->trans("TransferFromToDone","<a href=\"account.php?account=".$accountfrom->id."\">".$accountfrom->label."</a>","<a href=\"account.php?account=".$accountto->id."\">".$accountto->label."</a>",$amount,$langs->transnoentities("Currency".$conf->currency));
						$mesg.="</div>";
						$db->commit();
					}
					else
					{
						$mesg.="<div class=\"error\">".$accountfrom->error.' '.$accountto->error."</div>";
						$db->rollback();
					}
				}
				else
				{
					setEventMessages(null, $langs->trans("ErrorFromToAccountsMustDiffers"), 'errors');
					$action = 'outlay';
				}
			}
			if (!$error)
			{
				unset($_POST);
				header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
				exit;
			}
		}
	//setEventMessages($langs->trans('Errornoexist'), null, 'errors');

	}

	//confirm_apprecharge
	if ($id && $action == 'confirm_apprecharge' && $_REQUEST["confirm"] == 'yes' && $object->status == 4 && $user->rights->finint->efe->appr)
	{
		$langs->load('errors');
		$error= 0;
		$mesg='';

		$_POST = $_SESSION['aPost'];
		if ($object->id == $id)
		{
			$dateo = dol_now();
			$label = GETPOST('label','alpha');
			$amount= GETPOST('amount','int');
			$fk_type= GETPOST('fk_type','int');
			$cat1 = $object->fk_categorie;
			$numdoc= GETPOST('nro_chq','alpha');
			$account_to = $object->fk_account;
			if (! $amount)
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("Amount"))."</div>";
			}
			if (! GETPOST('account_from','int'))
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("TransferFrom"))."</div>";
			}
			if (! GETPOST('fk_type','alpha'))
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("Type"))."</div>";
			}
			if (! $error)
			{
				require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

				$accountfrom=new Account($db);
				$accountfrom->fetch(GETPOST('account_from','int'));

				$accountto=new Account($db);
				$accountto->fetch(GETPOST('account_to','int'));
				if ($accountto->id != $accountfrom->id)
				{
					$db->begin();

					$error=0;
					$bank_line_id_from=0;
					$bank_line_id_to=0;
					$result=0;
					// By default, electronic transfert from bank to bank
					$typefrom='PRE';
					$typeto='VIR';
					if (!empty(GETPOST('fk_type','alpha')))
					{
						// This is transfert of change
						$fk_type = GETPOST('fk_type','alpha');
						$typefrom=$fk_type;
						$typeto=$fk_type;
					}
					if ($accountto->courant == 2)
					{
						$typeto='LIQ';
					}
					if ($accountfrom->courant == 2)
					{
						$typefrom='LIQ';
					}
					if (! $error) 
						$bank_line_id_from = $accountfrom->addline($dateo, $typefrom, $label, -1*price2num($amount), ($numdoc?$numdoc:$object->ref), $cat1, $user);
					if ($bank_line_id_from <= 0)
						$error++;
					
					//cambiamos de id al usuario
					$fk_user_id_from = $user->id;
					$objuser = new User($db);
					$objuser->fetch(GETPOST('user_to','int'));
					if (! $error)
						$bank_line_id_to = $accountto->addline($dateo, $typeto, $label, price2num($amount), ($numdoc?$numdoc:$object->ref), $cat1, $objuser);
					if ($bank_line_id_to <= 0)
						$error++;

					if (! $error) 
						$result=$accountfrom->add_url_line($bank_line_id_from, $bank_line_id_to, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
					if ($result <= 0)
						$error++;
					if (! $error) $result=$accountto->add_url_line($bank_line_id_to, $bank_line_id_from, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
					if ($result <= 0)
						$error++;
					
					//cambiando a aprobado
					if (empty($error))
					{
						$object->status = 3;
						$object->fk_user_authorized = $user->id;
						$object->amount_authorized+=$amount;
						$object->tms = dol_now();
						//update
						$res = $object->update($user);
						if ($res <=0)
						{
							setEventMessages($object->error,$object->errors,'errors');
							$error++;
						}
					}
					//creamos un registro en request/carddeplacement
					if (empty($error))
					{
						$cashdeplac = new Requestcashdeplacementext($db);
						$numref = $cashdeplac->getNextNumRef($soc);
						$cashdeplac->fk_request_cash = 0;
						$cashdeplac->entity = $object->entity;
						$cashdeplac->ref = $numref;
						$cashdeplac->fk_request_cash_dest = $object->id;
						$cashdeplac->url_id = $bank_line_id_to;
						$cashdeplac->fk_bank = $bank_line_id_from;
						$cashdeplac->fk_type = $fk_type;
						$cashdeplac->fk_categorie = $cat1;
						$cashdeplac->fk_account_from = $accountfrom->id;
						$cashdeplac->fk_account_dest = $accountto->id;
						$cashdeplac->fk_projet_dest = $object->fk_projet;
						$cashdeplac->amount  = $amount;
						$cashdeplac->concept = 'banktransfert';
						$cashdeplac->dateo = $dateo;
						$cashdeplac->nro_chq = ($numdoc?$numdoc:$object->ref);
						$cashdeplac->detail = $label;
						//fijo por la operacion de transferencia
						$cashdeplac->fk_user_from = $user->id;
						$cashdeplac->fk_user_to = $object->fk_user_create;
						$cashdeplac->fk_user_create = $user->id;
						$cashdeplac->fk_user_approved = $user->id;
						$cashdeplac->date_dest = $dateo;
						$cashdeplac->date_create = dol_now();
						$cashdeplac->tms = dol_now();
						$cashdeplac->status = 1;
						$resc = $cashdeplac->create($user);
						if ($resc<=0)
						{
							setEventMessages($cashdeplac->error,$cashdeplac->errors,'errors');
							$error++;
						}
					}
					//cambiamos de estado los descargos realizados
					$deplacement = new Requestcashdeplacementext($db);
					$filterstatic = " AND t.fk_account_from = ".$object->fk_account;
					$filterstatic.= " AND t.fk_request_cash = ".$object->id;
					$filterstatic.= " AND t.entity = ".$object->entity;
					$filterstatic.= " AND t.status = 2";
					$res = $deplacement->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterstatic,false);
					$lines = $deplacement->lines;
					foreach ((array) $lines AS $j => $line)
					{
						//procesamos el registro en la cuenta bancaria
						$dateo 	= $line->dateo;
						$amount = $line->amount;
						$label 	= $line->detail;
						$numdoc	= $line->nro_chq;
						$fk_type = $line->fk_type;
						$fk_account_from = $line->fk_account_from;
						$fk_account_dest = $line->fk_account_dest;
						$cat1 = $line->fk_categorie;
						$aTypeop = array('ou'=>1,'in'=>0);
						$concept = $line->concept;
						if ($concept == 'deplacement')
						{
							//registramos como factura y gasto
							$data = array();
							if ($line->fourn_nit)
							{
								$data['datefacture'] = $line->fourn_date;
								$data['datedue'] = $line->fourn_datedue;
								$data['ref_soc'] = $line->fourn_facture.'|'.$line->fourn_nit.'|'.$line->fourn_numaut;
								//recuperamos al proveedor
								$filterstatic = " AND t.tva_intra = '".$line->fourn_nit."'";
								$filterstatic.= " AND t.entity = ".$conf->entity;
								$res = $societe->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);
								if ($res == 1)
									$fk_soc = $societe->id;
								elseif($res > 1)
								{
									foreach($societe->lines AS $s => $linesoc)
										$fk_soc = $linesoc->id;
								}
								else
									$fk_soc = $conf->global->FININT_ID_SOCIETE_DEFAULT;	
							}
							else
							{
								//recuperamos de la variable global al proveedor por defecto
								$fk_soc = $conf->global->FININT_ID_SOCIETE_DEFAULT;
								$data['ref_soc'] = $line->ref;
								$data['datefacture'] = $line->dateo;
							}
							$data['fk_soc'] = $fk_soc;
							$data['type']=0;
							$data['fk_projet'] = $line->fk_projet_dest;
							$data['fk_account'] = $line->fk_account_from;
							$data['label'] = $line->detail;


							$data['mode_reglement_id'] = $line->fk_type;
							$data['operation'] = $line->fk_type;

							$data['paiementid'] = $_POST['paiementid'][$j];
							$data['num_paiement'] = $line->nro_chq;
							$data['comment'] = $line->detail;
							$data['closepaidinvoices'] = 'on';

							$data['aProduct'][] = array('dp_desc' =>$line->detail,
								'prod_entry_mode' => 'free',
								'price_ht'=>$line->amount,
								'price_ttc'=>$line->amount,
								'tva_tx' => 0,
								'idprod' =>0,
								'qty' => $line->quant,
								'type' => 0,
								'fk_unit' => $line->fk_unit);
							$data['amount']+=$line->amount;

							list($id,$paiement_id,$idBank,$error) = facturefourn($data);
							echo '<hr>id '.$id.' pay '.$paiement_id.' bank '.$idBank.' errr '.$error;
						}
						else
						{
							//registra transferencias bancarias
							include DOL_DOCUMENT_ROOT.'/finint/include/add_bank_account.inc.php';
						}


						$deplacement->fetch($line->id);
						if ($concept == 'deplacement')
						{
							$deplacement->url_id = $paiement_id;
							$deplacement->fk_bank = $idBank;
						}
						$deplacement->status = 3;
						$res = $deplacement->update($user);
						if ($res<=0)
						{
							$error++;
							setEventMessages($deplacement->error,$deplacement->errors,'errors');
						}
					}
					if (! $error)
					{
						$mesg = $langs->trans("TransferFromToDone").' '.$amount.' '.$langs->transnoentities("Currency".$conf->currency);
						setEventMessages($mesg,null,'mesgs');
						$db->commit();
					}
					else
					{
						$mesg.="<div class=\"error\">".$accountfrom->error.' '.$accountto->error."</div>";
						$db->rollback();
					}
				}
				else
				{
					setEventMessages(null, $langs->trans("ErrorFromToAccountsMustDiffers"), 'errors');
					$action = 'discharg';
				}
			}
			if (!$error)
			{
				unset($_POST);
				header("Location: ".$_SERVER['PHP_SELF'].'?id='.$object->id);
				exit;
			}
		}
	}

	//confirm_noapprecharge
	if ($id && $action == 'confirm_noapprecharge' && $_REQUEST["confirm"] == 'yes' && $object->status == 4 && $user->rights->finint->efe->appr)
	{
		$langs->load('errors');
		$error= 0;
		$mesg='';

		$_POST = $_SESSION['aPost'];
		$lRev = GETPOST('lRev');
		if ($object->id == $id)
		{
			//se devuelve al anterior estado
			$object->status = 3;
			$res = $object->update($user);

			if ($lRev)
			{
				//cambiamos de estado los descargos realizados
				$filterstatic = " AND t.fk_account_from = ".$object->fk_account;
				$filterstatic.= " AND t.fk_request_cash = ".$object->id;
				$filterstatic.= " AND t.entity = ".$object->entity;
				$filterstatic.= " AND t.status = 2";
				$res = $deplacement->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterstatic,false);
				$lines = $deplacement->lines;
				foreach ((array) $lines AS $j => $line)
				{
					$deplacement->fetch($line->id);
					$deplacement->status = 1;
					$res = $deplacement->update_status($user);
					if ($res<=0)
					{
						$error++;
						setEventMessages($deplacement->error,$deplacement->errors,'errors');
					}
				}
			}
			if (! $error)
			{
				$mesg = $langs->trans("Rejectrechargesuccessfull");
				setEventMessages($mesg,null,'mesgs');
				$db->commit();
			}
			else
			{
				$mesg.="<div class=\"error\">".$accountfrom->error.' '.$accountto->error."</div>";
				$db->rollback();
			}
		}
		if (!$error)
		{
			unset($_POST);
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
	}

	//subimos el archivo ya que no hay otra forma
	if ($id && $action == 'closeconf' && $abc)
	{
		//subida de archivo
		$dir     = $conf->finint->multidir_output[$conf->entity].'/'.$id.'/cash';
		$file = '';
		$namefile = dol_sanitizeFileName($_FILES['docpdf']['name']);
		$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
		if ($file_OKfin)
		{
			//$newDir = $res;
			//verificamos permisos para el modo de subida de archivos
			$mode = 0;
			$mode = $user->rights->finint->pho->up4;
			if ($user->rights->finint->pho->up3) $mode = 3;
			if ($user->rights->finint->pho->up2) $mode = 2;
			if ($user->rights->finint->pho->up1) $mode = 1;
			if ($user->rights->finint->pho->up5) $mode = 5;

			if (GETPOST('deletedocfin'))
			{
				$fileimg=$dir.'/'.$namefile;
				$dirthumbs=$dir.'/thumbs';
				dol_delete_file($fileimg);
				dol_delete_dir_recursive($dirthumbs);
			}
			if (doc_format_supported($_FILES['docpdf']['name'],$mode) > 0)
			{
				dol_mkdir($dir);
				if (@is_dir($dir))
				{
					$aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
					$file = $namefile;
					$newfile = $dir.'/'.$file;
					$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
					if (! $result > 0)
					{
						$error++;
						$errors[] = "ErrorFailedToSaveFile";
					}
					else
					{
						$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
						$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
					}
				}
				else
				{
					$error++;
				}
			}
			else
			{
				$error++;
				$errors[] = "ErrorBadImageFormat";
			}
		}
		if ($error)
		{
			echo $error;exit;
		}
	// else
	//   echo $file;
	//fin subida de archivo

	}

	//confirm_closetmp
	if ($id && $action == 'confirm_closetmp' && $_REQUEST["confirm"] == 'yes' && $object->status == 3)
	{
		$langs->load('errors');
		$error= 0;
		$mesg='';

		$_POST = $_SESSION['aPost'];
		$_FILES = $_SESSION['aFile'];
		$numdoc = $_POST['nro_chq'];
		//$dateo = dol_mktime(12,0,0,$_POST['do_month'],$_POST['do_day'],$_POST['do_year']);
		$label = $_POST['label'];
		$amount= $_POST['amount'];
		$account_from = $object->fk_account;
		$account_to = $_POST['account_to'];
		$user_to = $_POST['user_to'];
		$fk_type = $_POST['fk_type'];
		$numdoc = $_POST['nro_chq'];
		$cat1 = $_POST['cat1'];
		$fk_projet = $_POST['fk_projet'];
		// on verifie si l'objet est en numerotation provisoire
		if ($object->id == $id)
		{
			$dateo = dol_mktime(12,0,0,GETPOST('domonth','int'),GETPOST('doday','int'),GETPOST('doyear','int'));

			$file = $_POST['file'];
			//actualizamos el object para cerrar
			$amount = $_POST['amount'];

			//revisamos valores
			if (! $label)
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("Description"))."</div>";
			}
			if (! $account_from )
			{
				$error=3;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("TransferFrom"))."</div>";
			}
			if ($amount > 0 && ! GETPOST('account_to','int'))
			{
				$error=4;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("TransferTo"))."</div>";
			}

			if (! $error)
			{
				$db->begin();
					//agregamos a llx_finint_cash_deplacement;
				$fk_finint_cash_to = $object->id;
				$cashdeplac = new Requestcashdeplacementext($db);
				$cashdeplac->entity = $object->entity;
				$numref = $cashdeplac->getNextNumRef($soc);
				$cashdeplac->ref = $numref;
				$cashdeplac->fk_request_cash = 0;
				$cashdeplac->fk_request_cash_dest = $fk_finint_cash_to;
				$cashdeplac->fk_projet_dest = 0;
				$cashdeplac->fk_account_from = $object->fk_account;
				$cashdeplac->fk_account_dest = GETPOST('account_to')+0;
				$cashdeplac->fk_categorie = $cat1+0;
				$cashdeplac->dateo = $dateo;
				$cashdeplac->url_id = $bank_line_id_from+0;
					//de
				$cashdeplac->fk_bank = $bank_line_id_to+0;
					//a
				$cashdeplac->fk_type = $fk_type;
				$cashdeplac->nro_chq = $numdoc;
				$cashdeplac->detail = $label;
				$cashdeplac->amount  = $amount + 0;
				$cashdeplac->concept = 'banktransfert';
					//fijo por la operacion de gastos
				$cashdeplac->fk_user_from = $object->fk_user_create+0;
				$cashdeplac->fk_user_to = GETPOST('user_to','int')+0;

				$cashdeplac->fk_user_create = $object->fk_user_create;
				$cashdeplac->date_dest = $dateo;
				$cashdeplac->date_create = dol_now();
				$cashdeplac->tms = dol_now();
				$cashdeplac->status = 4;
				$resc = $cashdeplac->create($user);
				if ($resc<=0)
				{
					$error++;
					setEventMessages($cashdeplac->error,$cashdeplac->errors,'errors');
				}
				//cambiamos de estado pendiente de cierre el object 
				$object->status = 4;
				$object->document_discharg = $file;
				$res = $object->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages(null, $langs->trans("Errortoclose"), 'errors');
			}

			if (!$error)
			{
				$db->commit();
				unset($_POST);
				header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
				exit;
			}
			else
			{
				$db->rollback();
				$action = 'close';
			}
		}
	}

	//confirm_rechargetmp
	if ($id && $action == 'confirm_rechargetmp' && $_REQUEST["confirm"] == 'yes' && $object->status == 3)
	{
		$langs->load('errors');
		$error= 0;
		$mesg='';

		$_POST = $_SESSION['aPost'];
		$_FILES = $_SESSION['aFile'];
		$numdoc = $_POST['nro_chq'];
		$label = $_POST['label'];
		$amount= $_POST['amount'];
		$account_from = $object->fk_account;
		$account_to = $_POST['account_to'];
		$user_to = $_POST['user_to'];
		$fk_type = $_POST['fk_type'];
		$numdoc = $_POST['nro_chq'];
		$cat1 = $_POST['cat1'];
		$fk_projet = $_POST['fk_projet'];
		if ($object->id == $id)
		{
			$dateo = dol_mktime(12,0,0,GETPOST('domonth','int'),GETPOST('doday','int'),GETPOST('doyear','int'));

			$file = $_POST['file'];
			//actualizamos el object para cerrar
			$amount = $_POST['amount'];

			//revisamos valores
			if (! $label)
			{
				$label = $langs->trans('Requestrecharge');
			}

			if (! $error)
			{
				$db->begin();

				//cambiamos de estado pendiente de cierre el object 
				$object->status = 4;
				$object->document_discharg = $file;
				$res = $object->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages(null, $langs->trans("Errortoclose"), 'errors');
			}

			if (!$error)
			{
				$db->commit();
				unset($_POST);
				header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
				exit;
			}
			else
			{
				$db->rollback();
				$action = 'recharge';
			}
		}
	}

	//confirm_closenoapp
	if ($id && $action == 'confirm_closenoapp' && $_REQUEST["confirm"] == 'yes' && $object->status == 4)
	{
		$langs->load('errors');
		$error= 0;
		$mesg='';

		$_GET = $_SESSION['aGet'];
		if ($object->id == $id && !empty($_SESSION['aGet']))
		{
			//recuperamos 
			$cashdeplac = new Requestcashdeplacementext($db);
			$cashdeplac->fetch($_GET['idr']);
			if ($cashdeplac->id == $_GET['idr'] && $cashdeplac->fk_finint_cash_dest == $id)
			{
				$db->begin();
				$cashdeplac->status = -1;
				$cashdeplac->tms = dol_now();
				$res = $cashdeplac->update($user);
				if ($res <= 0) $error++;
				//actualizamos el object
				$object->status = 3;
				$object->tms = dol_now();
				$res = $object->update($user);
				if ($res <= 0) $error++;
				if (empty($error))
				{
					setEventMessages("Recordsave", null, 'mesgs');
					$db->commit();
				}
				else
				{
					$mesg = $langs->trans('Errorclosenoapp');
					setEventMessages($mesg,null,'errors');
					$db->rollback();
				}
				$action = '';
			}
		}
	}
	//confirm_closeapp
	if ($id && $action == 'confirm_closeapp' && $_REQUEST["confirm"] == 'yes' && $object->status == 4)
	{
		$langs->load('errors');
		$error= 0;
		$mesg='';

		$_GET = $_SESSION['aGet'];
		$_POST = $_SESSION['aPost'];
		$db->begin();
		// on verifie si l'objet est en numerotation provisoire
		if ($object->id == $id && !empty($_SESSION['aPost']))
		{
			$cashdeplac = new Requestcashdeplacementext($db);
			//recuperamos todos los descargos de tipo gasto
			//para generar el movimiento bancario
			$filterstatic = " AND t.fk_account_from = ".$object->fk_account;
			//$filterstatic.= " AND t.fk_projet = ".$object->fk_projet;
			$filterstatic.= " AND t.fk_request_cash = ".$object->id;
			$filterstatic.= " AND t.entity = ".$object->entity;
			$res = $cashdeplac->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterstatic,false);
			foreach ((array) $cashdeplac->lines AS $j => $line)
			{
				$dateo 	= $line->dateo;
				$amount = $line->amount;
				$label 	= $line->detail;
				$numdoc	= $line->nro_chq;
				$fk_type = $line->fk_type;
				$fk_account_from = $line->fk_account_from;
				$fk_account_dest = $line->fk_account_dest;
				$cat1 = $line->fk_categorie;
				$aTypeop = array('ou'=>1,'in'=>0);
				include DOL_DOCUMENT_ROOT.'/finint/include/add_bank_account.inc.php';
			}
			//recuperamos 
			$cashdeplac->fetch($_POST['idr']);

			if ($cashdeplac->id == $_POST['idr'] && $cashdeplac->fk_request_cash_dest == $id)
			{
				$dateo = $cashdeplac->dateo;
				$amount = $cashdeplac->amount;
				$label = $cashdeplac->detail;
				$numdoc = $cashdeplac->nro_chq;
				$fk_type = $cashdeplac->fk_type;
				$cat1 = $cashdeplac->fk_categorie;
				$fk_account_from = $cashdeplac->fk_account_from;
				$fk_account_dest = $cashdeplac->fk_account_dest;
				$aTypeop = array('ou'=>1,'in'=>1);
				include DOL_DOCUMENT_ROOT.'/finint/include/add_bank_account.inc.php';

				if ($amount > 0 && $abc)
				{
					require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

					$accountfrom=new Account($db);
					$accountfrom->fetch($cashdeplac->fk_account_from);

					$accountto=new Account($db);
					$accountto->fetch($cashdeplac->fk_account_dest);
					if ($accountto->id != $accountfrom->id)
					{
						$error=0;
						$bank_line_id_from=0;
						$bank_line_id_to=0;
						$result=0;

						// By default, electronic transfert from bank to bank
						$typefrom='PRE';
						$typeto='VIR';
						if (!empty($fk_type))
						{
							// This is transfert of change
							$typefrom=$cashdeplac->fk_type;
							$typeto=$cashdeplac->fk_type;
						}
						if ($accountto->courant == 2)
						{
							$typeto='LIQ';
						}
						if ($accountfrom->courant == 2)
						{
							$typefrom='LIQ';
						}
						if (! $error) $bank_line_id_from = $accountfrom->addline($dateo, $typefrom, $label, -1*price2num($amount), $numdoc, '', $user);
						if (! ($bank_line_id_from > 0))
						{
							$error++;
							setEventMessages($langs->trans('Error, al crear el movimiento '),null,'errors');
						}
						//cambiamos de id al usuario
						$fk_user_id_from = $user->id;
						if (! $error) $bank_line_id_to = $accountto->addline($dateo, $typeto, $label, price2num($amount), $numdoc, '', $user);
						if (! ($bank_line_id_to > 0))
						{
							$error++;
							setEventMessages($langs->trans('Error, al crear el movimiento 2'),null,'errors');
						}

						if (! $error) $result=$accountfrom->add_url_line($bank_line_id_from, $bank_line_id_to, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
						if (! ($result > 0))
						{
							$error++;
							setEventMessages($langs->trans('Error, al crear el movimiento 11 '),null,'errors');
						}

						if (! $error) $result=$accountto->add_url_line($bank_line_id_to, $bank_line_id_from, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
						if (! ($result > 0))
						{
							$error++;
							setEventMessages($langs->trans('Error, al crear el movimiento 22'),null,'errors');
						}

					}
					else
					{
						$error++;
						setEventMessages(null, $langs->trans("ErrorFromToAccountsMustDiffers"), 'errors');
						$action = 'close';
					}
				}
			}
		}
				//cambiando a cerrado
		if (empty($error))
		{
			$object->status = 5;
					//cerrado
			$object->amount_out = $amount;
			$object->amount_close = $amount;
					//$object->document_discharg = $file;
			$res = $object->update($user);
			if (!$res>0) $error++;
		}

		if (! $error)
		{
			setEventMessages("Recordsave", null, 'mesgs');
			$db->commit();
		}
		else
		{
			$mesg = $langs->trans('Errorcloseapp');
			setEventMessages($mesg,null,'errors');
			$db->rollback();
		}
		if (!$error)
		{
			unset($_GET);
			header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
			exit;
		}

	}

	// confirm close //SE ELIMINA YA QUE ESTARA CON CONFIRM_CLOSEAPP
	if ($id && $action == 'confirm_close' && $_REQUEST["confirm"] == 'yes' && $object->status == 3)
	{
		$langs->load('errors');
		$error= 0;
		$mesg='';

		$_POST = $_SESSION['aPost'];
		$_FILES = $_SESSION['aFile'];
		$numdoc = $_POST['nro_chq'];
		// on verifie si l'objet est en numerotation provisoire
		if ($object->id == $id)
		{
			$dateo = dol_mktime(12,0,0,GETPOST('domonth','int'),GETPOST('doday','int'),GETPOST('doyear','int'));

			$file = $_POST['file'];
			//actualizamos el object para cerrar
			$amount = $_POST['amount'];

			if (! $error)
			{
				$db->begin();
				//subimos archivo de cierre
				//subida de archivo
				$dir     = $conf->finint->multidir_output[$conf->entity].'/'.$id.'/cash';
				$file = '';
				$namefile = dol_sanitizeFileName($_FILES['docpdf']['name']);
				$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
				$file_OKfin = true;
				if ($file_OKfin)
				{
					$newDir = $res;
					//verificamos permisos para el modo de subida de archivos
					$mode = 0;
					$mode = $user->rights->finint->pho->up4;
					if ($user->rights->finint->pho->up3) $mode = 3;
					if ($user->rights->finint->pho->up2) $mode = 2;
					if ($user->rights->finint->pho->up1) $mode = 1;
					if ($user->rights->finint->pho->up5) $mode = 5;

					if (GETPOST('deletedocfin'))
					{
						$fileimg=$dir.'/'.$namefile;
						$dirthumbs=$dir.'/thumbs';
						dol_delete_file($fileimg);
						dol_delete_dir_recursive($dirthumbs);
					}
					if (doc_format_supported($_FILES['docpdf']['name'],$mode) > 0)
					{
						dol_mkdir($dir);
						if (@is_dir($dir))
						{
							$aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
							$file = $namefile;
							$newfile = $dir.'/'.$file;
							$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
							if (! $result > 0)
							{
								$error++;
								$errors[] = "ErrorFailedToSaveFile";
							}
							else
							{
								$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
								$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
							}
						}
						else
						{
							$error++;
						}
					}
					else
					{
						$error++;
						$errors[] = "ErrorBadImageFormat";
					}

				}
				//fin subida de archivo
				//actualizamos el object
				if (!$error)
				{
					$object->document_discharg = $file;
				}

				if ($amount > 0)
				 //cuando se traspasa fondos
				{
					require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

					$accountfrom=new Account($db);
					$accountfrom->fetch(GETPOST('account_from','int'));

					$accountto=new Account($db);
					$accountto->fetch(GETPOST('account_to','int'));
					if ($accountto->id != $accountfrom->id)
					{

						$error=0;
						$bank_line_id_from=0;
						$bank_line_id_to=0;
						$result=0;

						// By default, electronic transfert from bank to bank
						$typefrom='PRE';
						$typeto='VIR';
						if ($accountto->courant == 2 || $accountfrom->courant == 2)
						{
							// This is transfert of change
							$typefrom='LIQ';
							$typeto='LIQ';
						}
						if (! $error) $bank_line_id_from = $accountfrom->addline($dateo, $typefrom, $label, -1*price2num($amount), $numdoc, '', $user);
						if (! ($bank_line_id_from > 0)) $error++;

						//cambiamos de id al usuario
						$fk_user_id_from = $user->id;
						if (! $error) $bank_line_id_to = $accountto->addline($dateo, $typeto, $label, price2num($amount), $numdoc, '', $user);
						if (! ($bank_line_id_to > 0)) $error++;

						if (! $error) $result=$accountfrom->add_url_line($bank_line_id_from, $bank_line_id_to, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
						if (! ($result > 0)) $error++;

						if (! $error) $result=$accountto->add_url_line($bank_line_id_to, $bank_line_id_from, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
						if (! ($result > 0)) $error++;

			// //creamos un registro en request/carddeplacement
			// if (empty($error))
			//   {
			// 	$cashdeplac = new Requestcashdeplacement($db);
			// 	$cashdeplac->fk_finint_cash = 0;
			// 	$cashdeplac->fk_finint_cash_dest = $object->id;
			// 	$cashdeplac->url_id = $bank_line_id_to;
			// 	$cashdeplac->fk_bank = $bank_line_id_from;
			// 	$cashdeplac->amount  = $amount;
			// 	$cashdeplac->concept = 'banktransfert'; //fijo por la operacion de transferencia
			// 	$cashdeplac->fk_user_to = $user->id;
			// 	$cashdeplac->fk_user_create = $user->id;
			// 	$cashdeplac->date_create = dol_now();
			// 	$cashdeplac->tms = dol_now();
			// 	$cashdeplac->status = 1;
			// 	$resc = $cashdeplac->create($user);
			// 	if (!$resc>0)
			// 	  $error++;
			//   }
					}
					else
					{
						setEventMessages(null, $langs->trans("ErrorFromToAccountsMustDiffers"), 'errors');
						$action = 'close';
					}
				}
				else
				{
					//cerramos y creamos una nueva solictiud desembolsada por el saldo
					$amount_close = $_POST['amount_close'];
					//nuevo
					if ($amount_close > 0)
					{
						$objectnew = new Requestcashadd($db);

						$objectnew->initAsSpecimen();
						/* object_prop_getpost_prop */
						$code= generarcodigo(4);
						$numref = $objectnew->getNextNumRef($soc);
						$objectnew->entity=$conf->entity;
						$objectnew->ref=$numref;
						$objectnew->fk_projet=$object->fk_projet;
						$objectnew->fk_account=$object->fk_account+0;
						$objectnew->detail=$langs->trans('Applicationclosingbalance').': '.$_POST['label'];
						$objectnew->amount=$amount_close;
						$objectnew->amount_authorized=$amount_close;
						$objectnew->fk_user_create=$user->id;
						$objectnew->fk_user_mod=$user->id;
						$objectnew->date_create = dol_now();
						$objectnew->tms = dol_now();
						$objectnew->status=3;
						$idres = $objectnew->create($user);
						if (!$idres > 0) $error++;
					}
				}
				//cambiando a cerrado
				if (empty($error))
				{
					$object->status = 5;
					//cerrado
					$object->amount_out = $_POST['amount_out'];
					$object->amount_close = $_POST['amount_close'];
					$object->document_discharg = $file;
					$res = $object->update($user);
					if (!$res>0)
						$error++;
				}
				if (! $error)
				{
					$mesg.="<div class=\"ok\">";
					$mesg.=$langs->trans("TransferFromToDone","<a href=\"account.php?account=".$accountfrom->id."\">".$accountfrom->label."</a>","<a href=\"account.php?account=".$accountto->id."\">".$accountto->label."</a>",$amount,$langs->transnoentities("Currency".$conf->currency));
					$mesg.="</div>";
					$db->commit();
				}
				else
				{
					$mesg.="<div class=\"error\">".$accountfrom->error.' '.$accountto->error."</div>";
					$db->rollback();
				}
			}
			if (!$error)
			{
				unset($_POST);
				header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
				exit;
			}
		}
	}

	// confirm transfd
	if ($id && $action == 'confirm_transfd' && $_REQUEST["confirm"] == 'yes' && $object->status == 3)
	{
		$langs->load('errors');
		$error= 0;
		$mesg='';

		$_POST = $_SESSION['aPost'];
		$_FILES = $_SESSION['aFile'];
		$label = $_POST['label'];
		// on verifie si l'objet est en numerotation provisoire
		if ($object->id == $id)
		{
			$file = $_POST['file'];
			//actualizamos el object para cerrar
			$amount = $_POST['amount'];
			if (! $error)
			{
				$db->begin();
				//fin subida de archivo

				if ($amount > 0)
				{
					$amount_close = $_POST['amount_close'];
					//nuevo
					if ($amount_close > 0)
					{
						$dateo = dol_mktime(12,0,0,GETPOST('domonth','int'),GETPOST('doday','int'),GETPOST('doyear','int'));

						//creamos un registro en request/carddeplacement
						$cashdeplac = new Requestcashdeplacementext($db);
						$cashdeplac->fk_finint_cash = $object->id;
						$cashdeplac->fk_finint_cash_dest = 0;
						$cashdeplac->url_id = 0;
						//$cashdeplac->fk_bank = $bank_line_id_from;
						$cashdeplac->fk_bank = 0;
						$cashdeplac->amount  = $amount;
						$cashdeplac->concept = 'banktransfert'; 
						$cashdeplac->detail = $label;
						$cashdeplac->dateo = $dateo;
	 					//fijo por la operacion de transferencia
						$cashdeplac->fk_account_from = $object->fk_account;
						$cashdeplac->fk_account_dest = $object->fk_account;
						$cashdeplac->fk_user_from = $object->fk_user_create;
						$cashdeplac->fk_user_to = $object->fk_user_create;
						$cashdeplac->fk_user_create = $object->fk_user_create;
						$cashdeplac->date_create = dol_now();
						$cashdeplac->tms = dol_now();
						$cashdeplac->status = 1;
						$resc = $cashdeplac->create($user);
						if (!$resc>0) $error++;

						$accountfrom=new Account($db);
						$accountfrom->fetch($object->fk_account);

						$accountto=new Account($db);
						$accountto->fetch($object->fk_account);
						if ($accountto->id == $accountfrom->id)
						{
							$amount = $amount_close;
							$bank_line_id_from=0;
							$bank_line_id_to=0;
							$result=0;

							// By default, electronic transfert from bank to bank
							$typefrom='PRE';
							$typeto='VIR';
							if ($accountto->courant == 2 || $accountfrom->courant == 2)
							{
							// This is transfert of change
								$typefrom='LIQ';
								$typeto='LIQ';
							}
							if (! $error) $bank_line_id_from = $accountfrom->addline($dateo, $typefrom, $label, -1*price2num($amount), '', '', $user);
							if (! ($bank_line_id_from > 0)) $error++;

							//cambiamos de id al usuario
							$fk_user_id_from = $user->id;
							if (! $error) $bank_line_id_to = $accountto->addline($dateo, $typeto, $label, price2num($amount), '', '', $user);
							if (! ($bank_line_id_to > 0)) $error++;

							if (! $error) $result=$accountfrom->add_url_line($bank_line_id_from, $bank_line_id_to, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
							if (! ($result > 0)) $error++;

							if (! $error) $result=$accountto->add_url_line($bank_line_id_to, $bank_line_id_from, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
							if (! ($result > 0)) $error++;

						}
						else
						{
							setEventMessages(null, $langs->trans("ErrorFromToAccountsMustDiffers"), 'errors');
							$action = 'close';
						}	
						//////////////					
						$idres = GETPOST('fk_finint_cash_to');
						//actualizamos el monto
						$objectnew = new Requestcashadd($db);
						$objtemp->url_id = $bank_line_id_to;
							//de
						$objtemp->fk_bank = $bank_line_id_from;
							//a
						$res = $objectnew->fetch($idres);
						if ($res > 0)						
						{
							$objectnew->amount_authorized = $objectnew->amount_authorized + $amount_close;
							$res = $objectnew->update($user);
							if ($res <=0) $error++;
						}
						else $error++;

						//actualizamos
						$objtemp = new Requestcashdeplacementext($db);
						$objtemp->fetch($resc);
						$objtemp->url_id = $bank_line_id_to;
							//de
						$objtemp->fk_bank = $bank_line_id_from;
							//a
						$objtemp->fk_finint_cash_dest = $idres;
						$objtemp->date_dest = $dateo;
						$res = $objtemp->update($user);
						if ($res <= 0) $error++;
					}
				}
				//cambiando a cerrado
				if (empty($error))
				{
					$object->status = 5;
					//cerrado
					$object->amount_out = $_POST['amount'];
					$object->amount_close = $_POST['amount'];
					$object->document_discharg = $file;
					$res = $object->update($user);
					if (!$res>0) $error++;
				}
				//echo '<hr>idres '.$idres;
				//confirmamos la operacion
				if (! $error)
				{
					$mesg.="<div class=\"ok\">";
					$mesg.=$langs->trans("TransferFromToDone","<a href=\"account.php?account=".$accountfrom->id."\">".$accountfrom->label."</a>","<a href=\"account.php?account=".$accountto->id."\">".$accountto->label."</a>",$amount,$langs->transnoentities("Currency".$conf->currency));
					$mesg.="</div>";
					setEventMessages($mesg, null, 'mesgs');
					$db->commit();
				}
				else
				{
					$mesg.="<div class=\"error\">".$accountfrom->error.' '.$accountto->error."</div>";
					setEventMessages($mesg, null, 'errors');
					$db->rollback();
				}
			}
			if (!$error)
			{
				unset($_POST);
				header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
				exit;
			}
		}
	}

	/*
	 * confirmacion de transferencia
	 */
	if ($id && $action == 'confirm_transfer' && $_REQUEST["confirm"] == 'yes' && $user->rights->finint->trans->crear)
	{
		$langs->load('errors');
		$error= 0;
		$mesg='';

		$_POST = $_SESSION['aPost'];
		// on verifie si l'objet est en numerotation provisoire
		if ($object->id == $id)
		{

			$dateo = dol_mktime(12,0,0,$_POST['do_month'],$_POST['do_day'],$_POST['do_year']);
			$label = $_POST['label'];
			$amount= $_POST['amount'];
			$account_from = $object->fk_account;
			$account_to = $_POST['account_to'];
			$user_to = $_POST['user_to'];
			$fk_type = $_POST['fk_type'];
			$numdoc = $_POST['nro_chq'];
			$cat1 = $_POST['cat1'];
			$fk_projet = $_POST['fk_projet'];
			if (! $label)
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("Description"))."</div>";
			}
			if (! $amount)
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("Amount"))."</div>";
			}
			if (! $account_from )
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("TransferFrom"))."</div>";
			}
			if (! GETPOST('account_to','int'))
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("TransferTo"))."</div>";
			}
			if (! GETPOST('user_to','int'))
			{
				$error=1;
				$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("UserTo"))."</div>";
			}

			if (! $error)
			{
					//agregamos a llx_finint_cash_deplacement;
				$fk_finint_cash_to = $_POST['fk_finint_cash_to'];
				$cashdeplac = new Requestcashdeplacementext($db);
				$cashdeplac->fk_finint_cash = $object->id;
				$cashdeplac->fk_finint_cash_dest = $fk_finint_cash_to;
				$cashdeplac->fk_projet_dest = $fk_projet;
				$cashdeplac->fk_account_from = $object->fk_account;
				$cashdeplac->fk_account_dest = GETPOST('account_to');
				$cashdeplac->fk_categorie = $cat1+0;
				$cashdeplac->dateo = $dateo;
				$cashdeplac->url_id = $bank_line_id_from;
					//de
				$cashdeplac->fk_bank = $bank_line_id_to;
					//a
				$cashdeplac->fk_type = $fk_type;
				$cashdeplac->nro_chq = $numdoc;
				$cashdeplac->detail = $label;
				$cashdeplac->amount  = $amount;
				$cashdeplac->concept = 'banktransfert';
					//fijo por la operacion de gastos
				$cashdeplac->fk_user_from = $object->fk_user_create+0;
				$cashdeplac->fk_user_to = GETPOST('user_to','int')+0;

				$cashdeplac->fk_user_create = $object->fk_user_create;
				$cashdeplac->date_dest = $dateo;
				$cashdeplac->date_create = dol_now();
				$cashdeplac->tms = dol_now();
				$cashdeplac->status = 0;
				$resc = $cashdeplac->create($user);
				if (!$resc>0) $error++;
				

				//esta transferencia o se agrega a una solicitud con estado desembolsado o se crea una nueva
				$objectnew = new Requestcashadd($db);
				if (empty($error))
				{
					//vemos si se selecciono el request/card
					if ($fk_finint_cash_to>0)
					{
						$res = $objectnew->fetch($fk_finint_cash_to);
						if ($objectnew->id == $fk_finint_cash_to)
						{
							//actualizamos si se aprueba
							$objectnew->amount_authorized = $objectnew->amount_authorized + $amount;
							$objectnew->detail.= '; '.$label.' '.$amount;
							$res = $objectnew->update($user);
							if (!$res>0) $error++;
							//actualizamos el finint_cash_deplacement con el destino
							$cashdeplac->fetch($resc);
							if ($cashdeplac->id == $resc)
							{
								$cashdeplac->fk_finint_cash_dest = $fk_finint_cash_to;
								$cashdeplac->dateo = $dateo;
								$res = $cashdeplac->update($user);
								if (!$res>0) $error++;
							}
							else $error++;
						}
						else $error++;
					}
					else
					{
							//nuevo
						$objectnew->initAsSpecimen();
						/* object_prop_getpost_prop */
						$code= generarcodigo(4);
						$numref = $objectnew->getNextNumRef($soc);

						$objectnew->entity=$conf->entity;
						$objectnew->ref=$numref;
						$objectnew->fk_projet=$fk_projet;
						$objectnew->fk_account=$account_to+0;
						$objectnew->detail=$label;
						$objectnew->amount=$amount;
						$objectnew->amount_approved=$amount;
						$objectnew->amount_authorized=$amount;
						$objectnew->amount_out=$amount;
						$objectnew->amount_close=0;

						$objectnew->fk_user_create=$user_to;
						$objectnew->fk_user_authorized=$user_to;
						$objectnew->fk_user_approved=$user_to;
						$objectnew->fk_categorie = $cat1+0;
						$objectnew->fk_user_mod=$user_to;
						$objectnew->date_create = dol_now();
						$objectnew->tms = dol_now();
						$objectnew->status=3;
						$idres = $objectnew->create($user);
						if ($idres <= 0) $error++;
							//actualizamos al request/carddeplacement
						if (!$error)
						{
								//agregamos el detalle
							$objectdet->initAsSpecimen();

							$objectdet->fk_finint = $idres;
							$objectdet->quant = 1;
							$objectdet->fk_unit = 0;
							$objectdet->detail = $label;
							$objectdet->amount = $amount;
							$objectdet->amount_approved = $amount;
							$objectdet->date_create = dol_now();
							$objectdet->active = 1;
							$objectdet->status = 1;
							$objectdet->fk_user_create = $user->id;
							$objectdet->fk_user_approved = $user->id;
							$res = $objectdet->create($user);
							if ($res <=0)
							{
								$error++;
								setEventMessages($objectdet->error,null,'errors');
							}
							//$fk_finint_cash_to = $res;
						}
							//actualizamos el finint_cash_deplacement con el destino
						$cashdeplac->fetch($resc);
						if ($cashdeplac->id == $resc && $idres>0)
						{
							$cashdeplac->fk_finint_cash_dest = $idres;
							$cashdeplac->dateo = $dateo;
							$res = $cashdeplac->update($user);
							if ($res<=0) $error++;
						}
						else $error++;
					}
				}
				if (! $error)
				{
					$mesg.="<div class=\"ok\">";
					$mesg.=$langs->trans("TransferFromToDone","<a href=\"account.php?account=".$accountfrom->id."\">".$accountfrom->label."</a>","<a href=\"account.php?account=".$accountto->id."\">".$accountto->label."</a>",$amount,$langs->transnoentities("Currency".$conf->currency));
					$mesg.="</div>";
					$db->commit();
				}
				else
				{
					$action='discharg';
					$mesg.="<div class=\"error\">".$accountfrom->error.' '.$accountto->error."</div>";
					$db->rollback();
				}
			}
			if (!$error)
			{
				unset($_POST);
				header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id.'&action=discharg',1));
				exit;
			}
		}
	}

	//confirmacion de rechazo
	if ($id && $action == 'confirm_reject' && $_REQUEST["confirm"] == 'yes' && $user->rights->finint->trans->aarr)
	{
		$langs->load('errors');
		$error= 0;
		$mesg='';

		$_POST = $_SESSION['aPost'];
		$_GET = $_SESSION['aGet'];
		// on verifie si l'objet est en numerotation provisoire
		if ($object->id == $id)
		{
			//recuperamos el registro de transferencia
			$cashdeplac = new Requestcashdeplacementext($db);
			$res = $cashdeplac->fetch(GETPOST('idr','int'));
			if ($res > 0 && $cashdeplac->id == GETPOST('idr'))
			{
				$cashdeplac->status = -1;
				$res = $cashdeplac->update($user);
				if ($res<=0)
				{
					setEventMessages($cashdeplac->error, null, 'errors');
					header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
					exit;
				}
				else
				{
					setEventMessages("Recordrejected", null, 'mesgs');
					header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
					exit;					
				}
			}
		}
	}

	// confirmacion de aceptacion
	if ($id && $action == 'confirm_accept' && $_REQUEST["confirm"] == 'yes' && $user->rights->finint->trans->aarr)
	{
		$langs->load('errors');
		$error= 0;
		$mesg='';

		$_POST = $_SESSION['aPost'];
		$_GET = $_SESSION['aGet'];
		// on verifie si l'objet est en numerotation provisoire
		if ($object->id == $id)
		{
			//recuperamos el registro de transferencia
			$cashdeplac = new Requestcashdeplacementext($db);
			$res = $cashdeplac->fetch(GETPOST('idr','int'));
			if ($res > 0 && $cashdeplac->id == GETPOST('idr'))
			{
				$dateo = $cashdeplac->date_dest;
				if (empty($dateo)) $dateo = dol_now();

				$label = $cashdeplac->detail;
				$amount= $cashdeplac->amount;
				$account_from = $cashdeplac->fk_account_from;
				$user_to = $cashdeplac->fk_user_to;
				$user_from = $cashdeplac->fk_user_from;
				$account_to = $cashdeplac->fk_account_dest;
				$fk_projet = $cashdeplac->fk_projet_dest;
				$fk_type = $cashdeplac->fk_type;
				$cat1 = $cashdeplac->fk_categorie;
				$nro_chq = $cashdeplac->nro_chq;
				if (! $label)
				{
					$error++;
					$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("Description"))."</div>";
				}
				if (! $amount)
				{
					$error++;
					$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("Amount"))."</div>";
				}
				if (! $account_from )
				{
					$error++;
					$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("TransferFrom"))."</div>";
				}
				if (! $account_to)
				{
					$error++;
					$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("TransferTo"))."</div>";
				}
				if (! $user_to)
				{
					$error++;
					$mesg.="<div class=\"error\">".$langs->trans("ErrorFieldRequired",$langs->transnoentities("UserTo"))."</div>";
				}
				if (! $error)
				{
					//solo guardamos la transferencia para que sea aprobado
					//una vez aprobado se procede a generar el movimiento en bancos

					require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

					$accountfrom=new Account($db);
					$accountfrom->fetch($account_from);

					$accountto=new Account($db);
					$accountto->fetch($account_to);
					// if ($accountto->id != $accountfrom->id)
					//   {
					$db->begin();

					$error=0;
					$bank_line_id_from=0;
					$bank_line_id_to=0;
					$result=0;
					// By default, electronic transfert from bank to bank
					$typefrom='PRE';
					$typeto='VIR';
					if (!empty($fk_type))
					{
						// This is transfert of change
						$typefrom=$fk_type;
						$typeto=$fk_type;
					}
					if ($accountto->courant == 2)
					{
						$typeto='LIQ';
					}
					if ($accountfrom->courant == 2)
					{
						$typefrom='LIQ';
					}
					$objuser->fetch($user_from);
					if (! $error) $bank_line_id_from = $accountfrom->addline($dateo, $typefrom, $label, -1*price2num($amount), $nro_chq, $cat1, $objuser);
					if (! ($bank_line_id_from > 0)) $error++;
					//cambiamos de id al usuario

					$objuser->fetch($user_to);
					//$user->id = GETPOST('user_to','int');
					if (! $error) $bank_line_id_to = $accountto->addline($dateo, $typeto, $label, price2num($amount), $nro_chq, $cat1, $objuser);
					if (! ($bank_line_id_to > 0)) $error++;
					if (! $error) $result=$accountfrom->add_url_line($bank_line_id_from, $bank_line_id_to, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
					if (! ($result > 0)) $error++;
					if (! $error) $result=$accountto->add_url_line($bank_line_id_to, $bank_line_id_from, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
					if (! ($result > 0)) $error++;

					if (empty($error))
					{
					//agregamos a llx_finint_cash_deplacement;
						$cashdeplac->url_id = $bank_line_id_from;
					//de
						$cashdeplac->fk_bank = $bank_line_id_to;
					//a
						$cashdeplac->tms = dol_now();
						$cashdeplac->status = 1;
						$resc = $cashdeplac->update($user);
						if (!$resc>0) $error++;
					}
					if (empty($error))
					{
						$object->amount_authorized = $object->amount_authorized + $amount;
						$res = $object->update($user);
						if (!$res>0) $error++;
					}
					if (!$error)
					{
						unset($_POST);
						$db->commit();
						setEventMessages("Recordsave", null, 'mesgs');
						header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
						exit;
					}
					else
					{
						setEventMessages($langs->trans('Errorexist'), null, 'errors');
						$action = '';
						$db->rollback();
					}
				}
				else
				{
					setEventMessages($langs->trans('Errorexist'), null, 'errors');
					$action = '';
				}
			}
			else
			{
				setEventMessages($cashdeplac->errors, null, 'errors');
				header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
				exit;
			}
		}
		else
		{
			setEventMessages($langs->trans('Errornoexistrecord'), null, 'errors');
			header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
			exit;
		}
	}
	/*
	 * Confirmation de la approval
	 */
	if ($id && $action == 'confirm_approval' && $_REQUEST["confirm"] == 'yes' && $object->status == 1 && $user->rights->finint->efe->apo)
	{
		$error=0;
		//recuperamos de session el post
		$_POST = $_SESSION['aPost'][$id];
		// on verifie si l'objet est en numerotation provisoire
		//actualizamos los valores
		/*
		$aAmountapp = $_POST['amount_approved'];
		$aSel = $_POST['sel'];
		$db->begin();
		$nApproved = 0;
		foreach ((array) $aAmountapp AS $idr => $value )
		{
			$objectdet->fetch($idr);
			if ($objectdet->id == $idr && $aSel[$idr])
			{
				$objectdet->amount_approved = $value;
				$objectdet->active = 1;
				$objectdet->tms = dol_now();
				$resup = $objectdet->update($user);
				$nApproved += $value;
				if ($resup <= 0) $error++;
			}
		}
		*/
		if (empty($error))
		{

			if ($object->id == $id)
			{
				//cambiando a aprobado
				//$object->amount_approved = price2num($nApproved,'MT');
				$object->amount_approved = $object->amount;
				$object->fk_user_approved = $user->id;
				$object->date_approved = dol_now();
				$object->tms = dol_now();
				$object->status = 2;
				//update
				$res = $object->update($user);
				if ($res>0)
				{
					setEventMessages($langs->trans('Requestapproved'),null,'mesgs');
					$db->commit();
					header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
					exit;
				}
				else
				{
					setEventMessages($object->error,$object->errors,'errors');
					$error++;
				}
			}
			else
			{
				setEventMessages($langs->trans('Errornoexist'), null, 'errors');		
				$error++;
			}
		}
		if ($error)
			$db->rollback();
		$action = 'approval';
	}

	/*
	 * Confirmation de la validation
	 */
	if ($id && $action == 'confirm_reject' && $_REQUEST["confirm"] == 'yes' && $object->status == 1)
	{
		// on verifie si l'objet est en numerotation provisoire
		if ($object->id == $id)
		{
			//cambiando a desaprobado
			$object->status = 0;
			//update
			$res = $object->update($user);
			if ($res>0)
			{
				header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
				exit;
			}
		}
		setEventMessages($langs->trans('Errornoexist'), null, 'errors');
		$action = 'view';
	}

	/*
	 * Confirmation de la validation
	 */
	if ($id && $action == 'confirm_validate' && $_REQUEST["confirm"] == 'yes' && $object->status == 0 && $user->rights->finint->efe->val)
	{
		//$filter = array(1=>1);
		//$filterstatic = " AND t.fk_finint = ".$id;
		//$res = $objectdet->fetchAll('ASC', 'detail', 0, 0, $filter, 'AND',$filterstatic,false);
		//$lines = $objectdet->lines;
		//$num = count($lines);
		//$nTotal = 0;
		//for ($i=0;$i<$num;$i++)
		//{
		//	$nTotal+= $lines[$i]->amount;
		//}

		// on verifie si l'objet est en numerotation provisoire
		$ref = substr($object->ref, 1, 4);
		$sendemail = GETPOST('sendemail');
		if ($ref == 'PROV')
		{
			$numref = $object->getNextNumRef($soc);
		}
		else
		{
			$numref = $object->ref;
		}
		if (!empty($numref))
		{
			//cambiando a validado
			//$object->amount = price2num($nTotal,'MT');
			$object->status = 1;
			$object->fk_user_authorized = 0;
			$object->fk_user_approved = 0;
			$object->tms = dol_now();
			$object->ref = $numref;
			//update
			$res = $object->update($user);
			if ($res>0)
			{
				setEventMessages($langs->trans('Validatesuccessfull'),null,'mesgs');
				header("Location: ".dol_buildpath('/finint/request/card.php?id='.$id,1));
				exit;
			}
		}
		else
			setEventMessages($langs->trans('Errornodefinednumber'), null, 'errors');
		$action = 'view';
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/finint/request/card.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
		}
	}
}


/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/
$morecss=array('/finint/css/style.css','/finint/css/bootstrap.min.css');
$morecss=array('/finint/css/style.css');
$morejs=array("/finint/js/finint.js");
llxHeader('',$langs->trans('Request'),'','','','',$morejs,$morecss,0,0);

$form=new Formv($db);
$formfile=new Formfile($db);

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


// Part to show a list
//if ($action == 'list' || (empty($id) && empty($ref)))
if ($action == 'list')
{
	$object = new Requestcashext($db);
	//$objectdep = new Deplacementext($db);	
	// Put here content of your page
	print load_fiche_titre($langs->trans('Requestcash'));

	$sql = "SELECT";
	$sql.= " t.rowid,";
	$sql.= " t.rowid AS id,";

	$sql .= " t.entity,";
	$sql .= " t.ref,";
	$sql .= " t.fk_projet,";
	$sql .= " t.fk_account,";
	$sql .= " t.fk_user_create,";
	$sql .= " t.fk_user_mod,";
	$sql .= " t.detail,";
	$sql .= " t.amount,";
	$sql .= " t.amount_authorized,";
	$sql .= " t.date_create,";
	$sql .= " t.date_delete,";
	$sql .= " t.tms,";
	$sql .= " t.status, ";
	$sql .= " p.title AS titleprojet, p.ref AS refprojet, p.description, ";
	$sql .= " u.lastname, u.firstname, u.login ";
	// Add fields for extrafields
	foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
	// Add fields from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;
	$sql.= " FROM ".MAIN_DB_PREFIX."request_cash as t";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."user as u ON t.fk_user_create = u.rowid ";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet as p ON t.fk_projet = p.rowid ";
	$sql.= " WHERE t.entity = ".$conf->entity;
	//filtro por tipo usuario y permiso
	if (!$user->admin && !$user->rights->finint->efe->all)
		$sql.= " AND (t.fk_user_create = ".$user->id." OR t.fk_user_assigned = ".$user->id.")";

	if ($search_entity) $sql.= natural_search("t.entity",$search_entity);
	if ($search_ref) $sql.= natural_search("t.ref",$search_ref);
	if ($search_proj) $sql.= natural_search(array("p.ref","p.title","p.description"),$search_proj);
	if ($search_fk_projet) $sql.= natural_search("t.fk_projet",$search_fk_projet);
	if ($search_fk_account) $sql.= natural_search("t.fk_account",$search_fk_account);
	if ($search_user) $sql.= natural_search(array("u.lastname","u.firstname","u.login"),$search_user);
	if ($search_fk_user_mod) $sql.= natural_search("t.fk_user_mod",$search_fk_user_mod);
	if ($search_detail) $sql.= natural_search("t.detail",$search_detail);
	if ($search_amount) $sql.= natural_search("t.amount",$search_amount);
	if ($search_amount_authorized) $sql.= natural_search("t.amount_authorized",$search_amount_authorized);
	if (isset($search_status) && $search_status != 9) $sql.= natural_search("t.status",$search_status);

	// Add where from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;

	// Count total nb of records
	$nbtotalofrecords = 0;

	if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
	{
		$result = $db->query($sql);
		$nbtotalofrecords = $db->num_rows($result);
	}

	$sql.= $db->order($sortfield, $sortorder);
	$sql.= $db->plimit($conf->liste_limit+1, $offset);
	//echo $sql;
	dol_syslog($script_file, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);

		$params='';
		$params='&amp;action=list';
		$params.= '&amp;search_ref='.urlencode($search_ref);
		$params.= '&amp;search_proj='.urlencode($search_proj);
		$params.= '&amp;search_user='.urlencode($search_user);
		$params.= '&amp;search_detail='.urlencode($search_detail);
		$params.= '&amp;search_status='.urlencode($search_status);

		print_barre_liste($title, $page, $_SERVER["PHP_SELF"],$params,$sortfield,$sortorder,'',$num,$nbtotalofrecords,'title_companies');


		print '<form method="GET" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="list">';
		if (! empty($moreforfilter))
		{
			print '<div class="liste_titre">';
			print $moreforfilter;
			$parameters=array();
			$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
			print $hookmanager->resPrint;
			print '</div>';
		}

		print '<table class="noborder centpercent">'."\n";

		// Fields title
		print '<tr class="liste_titre">';

		print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Projet'),$_SERVER['PHP_SELF'],'t.fk_projet','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('User'),$_SERVER['PHP_SELF'],'u.lastname','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'t.date_create','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Detail'),$_SERVER['PHP_SELF'],'t.detail','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'t.amount','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Authorized'),$_SERVER['PHP_SELF'],'t.amount_authorized','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Discharge'),$_SERVER['PHP_SELF'],'t.amount_authorized','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Balance'),$_SERVER['PHP_SELF'],'','',$param,'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Status'),$_SERVER['PHP_SELF'],'t.status','',$param,'align="right"',$sortfield,$sortorder);

		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		print '</tr>'."\n";

		// Fields title search
		print '<tr class="liste_titre">';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_proj" value="'.$search_proj.'" size="10"></td>';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_user" value="'.$search_user.'" size="10"></td>';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_date" value="'.$search_date.'" size="10"></td>';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
		print '<td class="liste_titre">&nbsp;</td>';
		print '<td class="liste_titre">&nbsp;</td>';
		print '<td class="liste_titre">&nbsp;</td>';
		print '<td class="liste_titre">&nbsp;</td>';
		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;

		print '<td align="right">';

		print $form->selectarray('search_status',$aStatut,$search_status,0);
		print '<br>';
		print '<input type="image" class="liste_titre" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
		print '<input type="image" class="liste_titre" name="button_removefilter" src="'.img_picto($langs->trans("RemoveFilter"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
		print '</td>';

		print '</tr>'."\n";

		$var = false;
		$i = 0;
		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
			if ($obj)
			{
				$var = !$var;
				//buscamos los gastos
				//$filter = " AND r.fk_finint_cash = ".$obj->id;
				//$objectdep->getlist(0, $filter);
				$totalg = 0;
				$cashdeplac = new Requestcashdeplacementext($db);
				$filter = array(1=>1);
				$filterstatic = " AND t.fk_request_cash = ".$obj->id;
				$filterstatic.= " AND t.concept = 'deplacement'";
				$res = $cashdeplac->fetchAll('ASC','rowid',0,0,$filter,'AND',$filterstatic,true);
				foreach ((array) $cashdeplac->lines AS $j => $lined)
				{
					$totalg+= $lined->amount;
				}


				// You can use here results
				print "<tr $bc[$var]>";

				$object->fetch($obj->id);
				$objtypecash = fetch_typecash($object->fk_type_cash);

				//solo para actualizar informacion de autorized
				if ($object->id == $obj->id && $object->status >= 3)
				{
					//$object->date_authorized = $object->tms;
					//$object->fk_user_authorized = $object->approved;
					//$object->update($user);
					//vamos a buscar con que numero de documento se hizo la transferencia
					if (empty($object->nro_chq))
					{
						$cashdeplac = new Requestcashdeplacementext($db);
						$filter = array(1=>1);
						$filterstatic = " AND t.fk_request_cash_dest = ".$obj->id;
						$filterstatic.= " AND t.concept = 'banktransfert'";
						$res = $cashdeplac->fetchAll('ASC','rowid',0,0,$filter,'AND',$filterstatic,true);
						if (!empty($cashdeplac->fk_bank))
						{
							$objbank = new Accountline($db);
							$objbank->fetch($cashdeplac->fk_bank);
							if ($objbank->id == $cashdeplac->fk_bank)
							{
								$object->nro_chq = $objbank->num_chq;
								$object->update($user);
							}
						}
					}
				}
				print '<td>'.$object->getNomUrl(1).'</td>';
				if ($conf->monprojet->enabled)
					$projet->fetch($obj->fk_projet);

				if ($projet->id == $obj->fk_projet)
					print '<td>'.$projet->getNomUrl(1,'',1).'</td>';
				else
					print '<td>&nbsp;</td>';
				$objuser->fetch($obj->fk_user_create);
				if ($objuser->id == $obj->fk_user_create)
					print '<td>'.$objuser->getNomUrl(1).'</td>';
				else
					print '<td>'.$obj->lastname.' '.$obj->firstname.'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->date_create),'day').'</td>';
				print '<td>'.$obj->detail.'</td>';
				print '<td align="right">'.price($obj->amount).'</td>';
				print '<td align="right">'.price($obj->amount_authorized).'</td>';
				print '<td align="right">'.price($totalg).'</td>';
				$balance = price2num($obj->amount_authorized - $totalg,'MT');
				$style = '';
				if ($balance >0) $style = 'style="color:#ff0000;"';
				print '<td align="right" '.$style.'>'.price(price2num($obj->amount_authorized - $totalg,'MT')).'</td>';

				print '<td align="right">'.$object->getLibStatut($objtypecash->recharge?3:2).'</td>';


				$parameters=array('obj' => $obj);
				$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);
		    // Note that $action and $object may have been modified by hook
				print $hookmanager->resPrint;
				print '</tr>';
			}
			$i++;
		}

		$db->free($resql);

		$parameters=array('sql' => $sql);
		$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    
		// Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;

		print "</table>\n";
		print "</form>\n";

	}
	else
	{
		$error++;
		dol_print_error($db);
	}
}



// Part to create
if ($action == 'create' && $user->rights->finint->efe->crear)
{
	print_fiche_titre($langs->trans("Newrequest"));

	print '<form  enctype="multipart/form-data" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	list($nbtypecash,$options) = get_seltypecash('rowid');

	print '<tr><td class="fieldrequired">'.$langs->trans("Typeofrequest").'</td><td>';
	if ($nbtypecash)
		print '<select name="fk_type_cash">'.$options.'</select>';
	else
		print img_picto($langs->trans('Error'),'error').' '.$langs->trans('Define the types in the Dictionary');
	print '</td></tr>';

	if ($user->admin)
	{
		print '<tr><td class="fieldrequired">'.$langs->trans('For').'</td>';
		print "<td>";
		print $form->select_users($object->fk_user_create,'user_to',0,array(1),0);
		print "</td>";
		print '</tr>';
	}
	print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>';
	print '<input class="flat" type="text" size="36" name="detail" value="'.GETPOST('detail').'">';
	print '</td></tr>';
	print '<tr><td>'.$langs->trans("Description").'</td><td>';
	print '<textarea class="flat" name="description" cols="50" rows="2">'.GETPOST('description').'</textarea>';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Amount").'</td><td>';
	print '<input class="flat" type="number" min="0" step="any" name="amount" value="'.$amount.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Attachment").'</td><td>';
	print '<input type="file" class="file" name="docpdf" id="docpdf"/>';
	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print '<form  enctype="multipart/form-data" method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'">';

	dol_fiche_head();

	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	print '<table class="border centpercent">'."\n";

	list($nbtypecash,$options) = get_seltypecash('rowid',$object->fk_type_cash);

	print '<tr><td class="fieldrequired">'.$langs->trans("Typeofrequest").'</td><td>';
	if ($nbtypecash)
		print '<select name="fk_type_cash">'.$options.'</select>';
	else
		print img_picto($langs->trans('Error'),'error').' '.$langs->trans('Define the types in the Dictionary');
	print '</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Projet").'</td><td>';
	//$filterkey = '';
	//$numprojet = $formproject->select_projects(($user->societe_id>0?$user->societe_id:-1), $object->fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);

	// $filterkey = '';
	// $numprojet = $formproject->select_projects($soc->id, $object->fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);
	//print '</td></tr>';

	if ($user->admin)
	{
		print '<tr><td>'.$langs->trans('For').'</td>';
		print "<td>";
		print $form->select_users($object->fk_user_create,'user_to',0,array(1),0);
		print "</td>";
		print '</tr>';
	}

	print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>';
	print '<input class="flat" type="text" size="36" name="detail" value="'.$object->detail.'">';
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Description").'</td><td>';
	print '<textarea class="flat" name="description" cols="50" rows="2">'.$object->description.'</textarea>';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Amount").'</td><td>';
	print '<input class="flat" type="number" min="0" step="any" name="amount" value="'.$object->amount.'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Attachment").'</td><td>';
	print '<input type="file" class="file" name="docpdf" id="docpdf"/>';
	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}
// Part to show record
$lView = false;
if (($id || $ref ) && (empty($action) || $action == 'view' ||
	$action == 'validate' || $action == 'approval' || $action=='approvalend' ||
	$action == 'outlay' || $action == 'addconf' || $action=='editassigned' ||
	$action == 'discharg' || $action=='createrefr' || $action=='createtransfd' || $action =='modifyrefr' || $action =='modifyvalrefr' ||
	$action == 'delete_dep' || $action=='close' || $action == 'editproj' || $action == 'recharge' ||
	$action == 'closeconf' || $action=='reject' || $action=='listdet'|| $action == 'rechargeconf' ||
	$action == 'closeapp' || $action == 'closenoapp' || $action == 'mod_dep' || $action == 'mod_depval' ||
	$action == 'apprecharge' || $action == 'noapprecharge' || 
	$action == 'transfd' || $action == 'transfdconf' || $action == 'builddoc' || 
	$action == 'addfourn' || $action == 'formapprecharge' || 
	$action == 'accept' || $action == 'noaccept' || $action == 'transfconf' || $action == 'delete'))
{
	$lView = true;
	if (!$user->admin)
	{
		if ($object->fk_user_assigned != $user->id) $lView = false;
		if ($object->fk_user_create == $user->id) $lView = true;
		if ($user->rights->finint->efe->leer || $user->rights->finint->efe->all) $lView = true;
	}
}
if ($lView)
{
	// Confirm accept
	unset($_SESSION['aGet']);
	// Confirm apprecharge
	if ($action == 'apprecharge')
	{
		$_SESSION['aPost'] = $_POST;
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Approverecharge"),$langs->trans("Confirmapproverecharge",$object->ref),"confirm_apprecharge",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	// Confirm noapprecharge
	if ($action == 'noapprecharge')
	{
		$_SESSION['aPost'] = $_POST;
		$array = array(0=>$langs->trans('No'),1=>$langs->trans('Yes'));
		$form_question['lVal'] = array (
			'name' => 'lVal',
			'type' => 'select',
			'label' => $langs->trans('Changevalidationdischarge'),
			'values' => $array,
			'default' => 0
			);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Rejectrecharge"),$langs->trans("Confirmrejectrecharge",$object->ref),"confirm_noapprecharge",$form_question,0,2);
		if ($ret == 'html') print '<br>';
	}

	// Confirm closeapp request/card
	if ($action == 'closeapp')
	{
		$idr = GETPOST('idr');
		$_SESSION['aGet'] = $_GET;
		$_SESSION['aPost'] = $_POST;
		//recuperamos el registro de cash_deplacement
		$cashdeplac = new Requestcashdeplacementext($db);
		$cashdeplac->fetch($idr);
		$amount = 0;
		if ($cashdeplac->id == $idr)
		{ 
			$amount = $cashdeplac->amount;
			//$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Approveclosure"),$langs->trans("Confirmapproveclosure",$object->ref).' '.$langs->trans('The').' '.price($amount),"confirm_closeapp",'',0,2);
			if ($ret == 'html') print '<br>';
		}
		else
			$action = '';
	}

	if ($action == 'addfourn')
	{
		$aPost[$id] = $_POST;
		$_SESSION['aPost'] = serialize($aPost);
		
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Saverecordpending"),$langs->trans("Confirmsaverecordspending",$object->ref),"confirm_deplacement",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	if ($action == 'closenoapp')
	{
		$idr = GETPOST('idr');
		$_SESSION['aGet'] = $_GET;
		//recuperamos el registro de cash_deplacement
		$cashdeplac = new Requestcashdeplacementext($db);
		$cashdeplac->fetch($idr);
		$amount = 0;
		if ($cashdeplac->id == $idr)
		{ 
			$amount = $cashdeplac->amount;
			//$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Rejectclose"),$langs->trans("Confirmrejectclose",$object->ref).' '.$langs->trans('The').' '.price($amount),"confirm_closenoapp",'',0,2);
			if ($ret == 'html') print '<br>';
		}
		else
			$action = '';
	}
	if ($action == 'accept')
	{
		//buscamos el registro
		$idr = GETPOST('idr','int');
		if ($user->admin || $object->fk_user_create == $user->id)
		{
			$cashdeplac = new Requestcashdeplacementext($db);
			$res = $cashdeplac->fetch($idr);
			if ($user->admin || $cashdeplac->fk_user_to == $user->id)
			{	
				$_SESSION['aGet'] = $_GET;
				//$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Accept"),$langs->trans("Confirmacceptrequest/card",$object->ref),"confirm_accept",'',0,2);
				if ($ret == 'html') print '<br>';
			}
		}
	}
	if ($action == 'noaccept')
	{
		//buscamos el registro
		$idr = GETPOST('idr','int');
		if ($user->admin || $object->fk_user == $user->id)
		{
			$cashdeplac = new Requestcashdeplacementext($db);
			$res = $cashdeplac->fetch($idr);
			if ($user->admin || $cashdeplac->fk_user_to == $user->id)
			{	
				$_SESSION['aGet'] = $_GET;
				//$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Reject"),$langs->trans("Confirmrejectrequest/card",$object->ref),"confirm_reject",'',0,2);
				if ($ret == 'html') print '<br>';
			}
		}
	}
	// Confirm delete
	if ($action == 'delete')
	{
		//$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Delete"),$langs->trans("Confirmdeleterequest/card",$object->ref),"confirm_delete",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	// Confirm approval
	if ($action == 'approval')
	{
		$_SESSION['aPost'][$id] = $_POST;
		//$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Approval"),$langs->trans("Confirmapprovalrequest",$object->ref),"confirm_approval",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	// Confirm approval
	if ($action == 'reject')
	{
		//$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Reject"),$langs->trans("Confirmrejectrequest/card").' '.$object->ref,"confirm_reject",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	// Confirm validate request/card
	if ($action == 'validate')
	{
		//verificamos la suma del detalle
		//$filter = array(1=>1);
		//$filterstatic = " AND t.fk_finint = ".$id;
		//$res = $objectdet->fetchAll('ASC', 'detail', 0, 0, $filter, 'AND',$filterstatic,false);
		//$lines = $objectdet->lines;
		//$num = count($lines);
		$nTotal = 0;
		$nTotal = $object->amount;
		//for ($i=0;$i<$num;$i++)
		//{
		//	$nTotal+= $lines[$i]->amount;
		//}
		$formquestion = '';
		//$formquestion = array(0=>array('type'=>'number',
	 	//		       'name'=>'sendemail',
	 	//		       'label'=>$langs->trans('Sendemail')));
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
			$langs->trans("Validate"),
			$langs->trans("Confirmvalidaterequestcash").' '.$langs->trans('Foratotalamount').' <span style="font-size:2em;">'.price(price2num($nTotal,'MT'),$object->ref).'</span>', "confirm_validate", $formquestion,
			0,2);
		if ($ret == 'html') print '<br>';
	}
	// Confirm validate request/card
	if ($action == 'addconf')
	{
		$_SESSION['aPost'] = $_POST;
		//$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Outlay"),$langs->trans("Confirmoutlaycash",$object->ref),"confirm_outlay",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	// Confirm validate request/card
	if ($action == 'transfconf')
	{
		$_SESSION['aPost'] = $_POST;
		//$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Transfer"),$langs->trans("Confirmtransfer",$object->ref).' '.$langs->trans('The').' '.price($_POST['amount']),"confirm_transfer",'',0,2);
		if ($ret == 'html') print '<br>';
	}

	// Confirm delete deplacement
	if ($action == 'delete_dep')
	{
		//$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idrcd='.$idrcd,$langs->trans("Deletespending"),$langs->trans("Confirmdeletespending",$object->ref),"confirm_delete_deplacement",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	// Confirm rechargeconf
	if ($action == 'rechargeconf')
	{
		//se sube el archivo si existe
		//subida de archivo
		$dir     = $conf->finint->multidir_output[$conf->entity].'/'.$id.'/cash';
		$file = '';
		$namefile = dol_sanitizeFileName($_FILES['docpdf']['name']);
		$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
		if ($file_OKfin)
		{
			$newDir = $res;
					//verificamos permisos para el modo de subida de archivos
			$mode = 0;
			$mode = $user->rights->finint->pho->up4;
			if ($user->rights->finint->pho->up3) $mode = 3;
			if ($user->rights->finint->pho->up2) $mode = 2;
			if ($user->rights->finint->pho->up1) $mode = 1;
			if ($user->rights->finint->pho->up5) $mode = 5;

			if (GETPOST('deletedocfin'))
			{
				$fileimg=$dir.'/'.$namefile;
				$dirthumbs=$dir.'/thumbs';
				dol_delete_file($fileimg);
				dol_delete_dir_recursive($dirthumbs);
			}
			if (doc_format_supported($_FILES['docpdf']['name'],$mode) > 0)
			{
				dol_mkdir($dir);
				if (@is_dir($dir))
				{
					$aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
					$file = $namefile;
					$newfile = $dir.'/'.$file;
					$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
					if ($result <= 0)
					{
						$error++;
						$errors[] = "ErrorFailedToSaveFile";
					}
					else
					{
						$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
						$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
					}
				}
				else
				{
					$error++;
				}
			}
			else
			{
				$error++;
				$errors[] = "ErrorBadImageFormat";
			}
		}
		$_POST['file'] = $file;
		$_SESSION['aPost'] = $_POST;
		$_SESSION['aFile'] = $_FILES;

		//$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Close"),$langs->trans("Confirmrecharge",$object->ref).' '.$langs->trans('The').' '.price($_POST['amount']),"confirm_rechargetmp",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	// Confirm closeconf 
	if ($action == 'closeconf')
	{
		//se sube el archivo si existe
		//subida de archivo
		$dir     = $conf->finint->multidir_output[$conf->entity].'/'.$id.'/cash';
		$file = '';
		$namefile = dol_sanitizeFileName($_FILES['docpdf']['name']);
		$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
		if ($file_OKfin)
		{
			$newDir = $res;
					//verificamos permisos para el modo de subida de archivos
			$mode = 0;
			$mode = $user->rights->finint->pho->up4;
			if ($user->rights->finint->pho->up3) $mode = 3;
			if ($user->rights->finint->pho->up2) $mode = 2;
			if ($user->rights->finint->pho->up1) $mode = 1;
			if ($user->rights->finint->pho->up5) $mode = 5;

			if (GETPOST('deletedocfin'))
			{
				$fileimg=$dir.'/'.$namefile;
				$dirthumbs=$dir.'/thumbs';
				dol_delete_file($fileimg);
				dol_delete_dir_recursive($dirthumbs);
			}
			if (doc_format_supported($_FILES['docpdf']['name'],$mode) > 0)
			{
				dol_mkdir($dir);
				if (@is_dir($dir))
				{
					$aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
					$file = $namefile;
					$newfile = $dir.'/'.$file;
					$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
					if ($result <= 0)
					{
						$error++;
						$errors[] = "ErrorFailedToSaveFile";
					}
					else
					{
						$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
						$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
					}
				}
				else
				{
					$error++;
				}
			}
			else
			{
				$error++;
				$errors[] = "ErrorBadImageFormat";
			}
		}
		$_POST['file'] = $file;
		$_SESSION['aPost'] = $_POST;
		$_SESSION['aFile'] = $_FILES;

		//$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Close"),$langs->trans("Confirmclose",$object->ref).' '.$langs->trans('The').' '.price($_POST['amount']),"confirm_closetmp",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	// Confirm close transfd
	if ($action == 'transfdconf')
	{
		//se sube el archivo si existe
		//subida de archivo
		$dir     = $conf->finint->multidir_output[$conf->entity].'/'.$id.'/cash';
		$file = '';
		$namefile = dol_sanitizeFileName($_FILES['docpdf']['name']);
		$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
		if ($file_OKfin)
		{
			$newDir = $res;
					//verificamos permisos para el modo de subida de archivos
			$mode = 0;
			$mode = $user->rights->finint->pho->up4;
			if ($user->rights->finint->pho->up3) $mode = 3;
			if ($user->rights->finint->pho->up2) $mode = 2;
			if ($user->rights->finint->pho->up1) $mode = 1;
			if ($user->rights->finint->pho->up5) $mode = 5;

			if (doc_format_supported($_FILES['docpdf']['name'],$mode) > 0)
			{
				dol_mkdir($dir);
				if (@is_dir($dir))
				{
					$aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
					$file = $namefile;
					$newfile = $dir.'/'.$file;
					$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
					if ($result <= 0)
					{
						$error++;
						$errors[] = "ErrorFailedToSaveFile";
					}
					else
					{
						$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
						$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
					}
				}
				else
				{
					$error++;
				}
			}
			else
			{
				$error++;
				$errors[] = "ErrorBadImageFormat";
			}
		}
		//echo '<hr>err '.$error;
				//fin subida de archivo
		$_POST['file'] = $file;
		$_POST['dir'] = $dir;
		$_SESSION['aPost'] = $_POST;
		$_SESSION['aFile'] = $_FILES;

		//fin se sube el archivo si existe
		$objprojet = new Project($db);
		$objprojet->fetch(GETPOST('fk_projet'));
		//$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Closetransfer"),$langs->trans("Confirmclose",$object->ref).' '.$langs->trans('The').' '.price($_POST['amount']).' '.$langs->trans('Sendto').' '.$langs->trans('Project').' '.$objprojet->ref.' '.$objprojet->title,"confirm_transfd",'',0,2);
		if ($ret == 'html') print '<br>';
	}

	//$projet = new Project($db);
	//$projet->fetch($object->fk_projet);
	dol_fiche_head();
	print '<table class="border centpercent">'."\n";

	print '<tr><td width="15%">'.$langs->trans("Ref").'</td><td>';
	$param='';
	$linkback='<a href="'.DOL_URL_ROOT.'/finint/request/card.php">'.$langs->trans("BackToList").'</a>';

	print $form->showrefnav($object,'ref',$linkback,1,'ref','ref','',$param);

	//print $object->ref;
	print '</td></tr>';

	//print '<tr><td>'.$langs->trans("Projet").'</td><td>';
	//if ($action != 'editproj')
	//	print $projet->getNomUrl(1).' '.$projet->title.'</a>';
	//else
	//{
	//	print '<form  enctype="multipart/form-data" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	//	print '<input type="hidden" name="action" value="updateprojet">';
	//	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	//	print '<input type="hidden" name="id" value="'.$object->id.'">';
	//	$filterkey = '';
	//	$numprojet = $formproject->select_projects(($user->societe_id>0?$user->societe_id:-1), $object->fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);
	//	print '<input type="submit" name="save" value="'.$langs->trans('Save').'">';	
	//	print '</form>';
	//}
	//if ($user->rights->finint->efe->mod && $action != 'editproj')
	//{
	//	print '&nbsp;';
	//	print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=editproj">'.img_picto($langs->trans('Edit'),'edit').'</a>';
	//}
	//print '</td></tr>';
	print '<tr><td>'.$langs->trans('Typeofrequest').'</td><td>';
	$objtypecash = fetch_typecash($object->fk_type_cash);
	if ($objtypecash->rowid == $object->fk_type_cash)
		print $objtypecash->label;
	else
		print $object->fk_type_cash;
	print '</td></tr>';	
	//name
	print '<tr><td>'.$langs->trans("Userapplicant").'</td><td>';
	$objuser = new User($db);
	$objuser->fetch($object->fk_user_create);
	print $objuser->getNomUrl(1).' ';
	//mostramos la cuenta
	$objaccount->fetch($object->fk_account);
	if ($objaccount->id == $object->fk_account)
		print $objaccount->getNomUrl(1);
	print '</td></tr>';

	//assigned
	print '<tr><td>'.$langs->trans("Userassign").'</td><td>';
	$objuser->fetch($object->fk_user_assigned);
	if ($action == 'editassigned')
	{
		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="updateassign">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		print $form->select_users($object->fk_user_assigned,'fk_user_assigned',0,array(1,$fk_user_create),0);
		print '<input type="submit" name="save" value="'.$langs->trans('Save').'">';	
		print '</form>';
	}
	else
	{
		if ($objuser->id == $object->fk_user_assigned)
			print $objuser->getNomUrl(1).' ';
		else
			print '&nbsp;';
		if ($user->admin || ($user->rights->finint->efe->mod && $action != 'editassigned' && $user->id == $object->fk_user_create && $object->status < 5))
		{
			print '&nbsp;';
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=editassigned">'.img_picto($langs->trans('Edit'),'edit').'</a>';
		}
	}
	print '</td></tr>'; 
	//mostramos la cuenta
	//$objaccount->fetch($object->fk_account);
	//	if ($objaccount->id == $object->fk_account)
	//		print $objaccount->getNomUrl(1);
	//print '</td></tr>';
	if ($action != 'mod_depval' && $action != 'recharge' && $action != 'discharg' && $action != 'createrefr' )
	{
		print '<tr><td>'.$langs->trans("Title").'</td><td>';
		print $object->detail;
		print '</td></tr>';

		print '<tr><td>'.$langs->trans("Description").'</td><td>';
		print $object->description;
		print '</td></tr>';

		print '<tr><td>'.$langs->trans("Date").'</td><td>';
		print dol_print_date($object->date_create,'day');
		print '</td></tr>';

		//photo
		$aPhoto = explode(';',$object->document);
		print '<tr><td>';
		print $langs->trans('Attachment');
		print '</td><td>';
		foreach ((array) $aPhoto AS $j => $doc)
		{
			$doc = trim($doc);
			$aFile = explode('.',$doc);
		//extension
			$docext = STRTOUPPER($aFile[count($aFile)-1]);
			$typedoc = 'doc';
			if ($docext == 'BMP' || $docext == 'GIF' ||$docext == 'JPEG' || $docext == 'JPG' || $docext == 'PNG' || $docext == 'CDR' ||$docext == 'CDT' || $docext == 'XCF' || $docext == 'TIF')
				$typedoc = 'fin';
			if ($docext == 'DOC' || $docext == 'DOCX' ||$docext == 'XLS' || $docext == 'XLSX' || $docext == 'PDF')
				$typedoc = 'doc';
			elseif($docext == 'ARJ' || $docext == 'BZ' ||$docext == 'BZ2' || $docext == 'GZ' || $docext == 'GZ2' || $docext == 'TAR' ||$docext == 'TGZ' || $docext == 'ZIP')
				$typedoc = 'doc';
			if (!empty($doc))
			{
				print '&nbsp;&nbsp;'.$object->showphoto($typedoc,$object,$doc, 100,$docext);
				if ($user->admin || ($user->id == $object->fk_user_create && $user->rights->finint->efe->del))
					print '&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&namedoc='.$doc.'&action=deldoc'.'">'.img_picto($langs->trans('Deleteattachment'),'edit_remove').'</a>';
			}
		}
		print '</td></tr>';
		print '<tr><td>'.$langs->trans("Amount").'</td><td>';
		print price($object->amount);
		if ($object->status >= 1)
		{
			//print '&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=listdet'.'">'.$langs->trans('Viewlist').'</a>';
		}
		print '</td></tr>';
		if ($object->status >=2)
		{
			print '<tr><td>'.$langs->trans("Amountapproved").'</td><td>';
			print price($object->amount_approved);
			print '</td></tr>';
		}
	}
	//if ($object->status == 3)
	//{
	//	print '<tr><td>'.$langs->trans("Disbursed").'</td><td>';
	//	print price($object->amount_authorized);
	//	print '</td></tr>';
	//}

	//close
	if ($object->status >= 4)
	{
		//photo
		$aPhoto = explode(';',$object->document_discharg);
		print '<tr><td>';
		print $langs->trans('Attachmentclose');
		print '</td><td>';
		foreach ((array) $aPhoto AS $j => $doc)
		{
			$doc = trim($doc);
			$aFile = explode('.',$doc);
		//extension
			$docext = STRTOUPPER($aFile[count($aFile)-1]);
			$typedoc = 'doc';
			if ($docext == 'BMP' || $docext == 'GIF' ||$docext == 'JPEG' || $docext == 'JPG' || $docext == 'PNG' || $docext == 'CDR' ||$docext == 'CDT' || $docext == 'XCF' || $docext == 'TIF')
				$typedoc = 'fin';
			if ($docext == 'DOC' || $docext == 'DOCX' ||$docext == 'XLS' || $docext == 'XLSX' || $docext == 'PDF')
				$typedoc = 'doc';
			elseif($docext == 'ARJ' || $docext == 'BZ' ||$docext == 'BZ2' || $docext == 'GZ' || $docext == 'GZ2' || $docext == 'TAR' ||$docext == 'TGZ' || $docext == 'ZIP')
				$typedoc = 'doc';
			if ($doc)
				print '&nbsp;&nbsp;'.$object->showphoto($typedoc,$object,$doc, 100,$docext);
		}
		print '</td></tr>';
		print '<tr><td>'.$langs->trans("Amountout").'</td><td>';
		print price($object->amount_out);
		print '</td></tr>';
		print '<tr><td>'.$langs->trans("Amountclose").'</td><td>';
		print price($object->amount_close);
		print '</td></tr>';
	}
	print '</table>';

	//mostraremos las transferencias recibidas
	if ($object->status >=3 )
	{
		$accountadd = new Accountext($db);
		$accountadd->getlist($object->fk_account,$object->id,1);

		$objdeplac = new Requestcashdeplacementext($db);
		$objdeplac->getlisttransfer(0,$object->id);
		$lViewres = true;
		$lViewdischarg = true;
		if ($objtypecash->recharge && ($action == 'discharg' || $action == 'recharge' )) $lViewres = false;			
		if ($objtypecash->recharge && ( $action == 'recharge' )) $lViewdischarg = false;			

		if ($lViewres)
		{
			print '<table class="noborder centpercent">'."\n";
		// Fields title
			print '<tr class="liste_titre centpercent">';

			print_liste_field_titre($langs->trans('Usertransfers'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans('From'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
			if($conf->browser->layout=='classic')
			{
				print_liste_field_titre($langs->trans('To'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Type'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
			}
			print_liste_field_titre($langs->trans('Doc'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
			if($conf->browser->layout=='classic')
				print_liste_field_titre($langs->trans('Detail'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'','',$param,'align="right"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans('Status'),$_SERVER['PHP_SELF'],'','',$param,'align="right"',$sortfield,$sortorder);
			print '</tr>';
		}
			//se debe verificar si existe algo por aprobar
		$lTransfer = true;
		$lRequestnull = true;
		$sumatransf = 0;
		$var = true;
		foreach ((array) $objdeplac->lines AS $j => $line)
		{
			$var = !$var;
			if ($line->status == 0 || $line->status == 1) $lTransfer = false;
			if ($line->status != 0 && $line->status != -1)
				$lRequestnull = false;
			if ($line->status == 4) $idr = $line->id;

			if ($action != 'discharg' && $action != 'createrefr')
			{
				if ($lViewres)
				{
					print "<tr $bc[$var]>";
					print '<td>';
					$objuser->fetch(($line->fk_user_from?$line->fk_user_from:$object->fk_user_authorized));
					if ($objuser->id == $line->fk_user_from)
					{
						$objreq = new Requestcashext($db);
						if ($line->fk_finint_cash>0)
							$objreq->fetch($line->fk_finint_cash);
						print ($line->fk_finint_cash>0?$objreq->getNomUrl(1).' ':'').$objuser->lastname.' '.$objuser->firstname;
					}
					else
						print 'Noexist';
					print '</td>';
					print '<td>';
					print dol_print_date($line->dateo,'day');
					print '</td>';
					print '<td>';
					$objaccount = new Account($db);
					$fkacc = ($line->fk_account_from?$line->fk_account_from:$line->fk_account);
					$objaccount->fetch($fkacc);
					if ($objaccount->id == $fkacc)
						print $objaccount->getNomUrl(1);
					else
						print '';
					print '</td>';
					if($conf->browser->layout=='classic')
					{
						print '<td>';
						$objaccount = new Account($db);
						$fkacc = ($line->fk_account_dest?$line->fk_account_dest:$object->fk_account);
						$objaccount->fetch($fkacc);
						if ($objaccount->id == $fkacc)
							print $objaccount->getNomUrl(1);
						else
							print '';
						print '</td>';
						print '<td>';
						//$objtype = fetch_paiement($line->fk_type,'code');
						print $langs->trans('PaymentType'.STRTOUPPER(($line->fk_type?$line->fk_type:'LIQ')));
						print '</td>';
					}
					print '<td>';
					print ($line->num_chq?$line->num_chq:$line->nro_chq);
					print '</td>';
					if($conf->browser->layout=='classic')
					{
						print '<td>';
						print $line->detail;
						print '</td>';
					}
					print '<td align="right">';
					print price($line->amount);
					print '</td>';
					print '<td align="right">';
					if ($line->status ==0)
					{
						if ($user->admin || ($user->id == $line->fk_user_to && $user->rights->finint->trans->aarr))
						{
							print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$line->id.'&action=accept">'.img_picto($langs->trans('Toaccept'),DOL_URL_ROOT.'/finint/img/ok','',1).'</a>';
							print '&nbsp;&nbsp;&nbsp;&nbsp;';
							print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$line->id.'&action=noaccept">'.img_picto($langs->trans('Torefuse'),DOL_URL_ROOT.'/finint/img/ko','',1).'</a>';
						}
						else
						{
							print $langs->trans('Pendiente');
						}
					}
					if ($line->status == -1)
						print img_picto($langs->trans('Rejected'),DOL_URL_ROOT.'/finint/img/ko','',1);
					if ($line->status == 1)
						print img_picto($langs->trans('Accepted'),DOL_URL_ROOT.'/finint/img/ok','',1);
					if ($line->status == 4)
					{
						$idr = $line->id;
						print img_picto($langs->trans('Approveclosure'),'off');
					}
					if ($line->status == 5)
						print img_picto($langs->trans('Closed'),'on');

					print '</td>';
					print '</tr>';
				}
			}
			if ($line->status != -1 && $line->status != 4)
			{
				$sumatransf+=$line->amount;
				$sumadep += $line->amount;
			}
		}
		if ($lViewres)
		{
			print '<tr class="liste_total">';
			print '<td colspan="'.($conf->browser->layout=='classic'?'7':'5').'">'.$langs->trans('Total').'</td>';
			print '<td align="right">'.price(price2num($sumatransf,'MT')).'</td>';
			print '<td></td>';
			print '</tr>';
			print '</table>'."\n";
		}
	}
	//mostramos el resumen del rquerimiento
	//listamos los gastos realizados por el usuario
	//mostraremos las transferencias recibidas
	if ($object->status >=3 )
	{
		//gastos
		$deplacement = new Requestcashdeplacementext($db);

		$filter = " AND k.fk_account = ".$object->fk_account;
		//$filter.= " AND t.fk_projet = ".$object->fk_projet;
		//$filter.= " AND r.fk_finint_cash = ".$object->id;
		$filterstatic.= " AND t.entity = ".$object->entity;
		$filterstatic.= " AND t.concept = 'deplacement'";
		$filterstatic.= " AND t.fk_request_cash = ".$object->id;
		$deplacement->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterstatic,false);
		$sumadep = 0;
		foreach ((array) $deplacement->lines AS $j => $line)
		{
			$sumadep -= $line->amount;
		}
		$objdeplac = new Requestcashdeplacementext($db);
		$objdeplac->getlisttransfer($object->id);
		$sumapar = 0;
		$sumapar0 = 0;
		foreach ((array) $objdeplac->lines AS $j => $line)
		{
			if ($line->status == 1)
			{
				$sumapar += $line->amount*-1;
			}
			if ($line->status == 0)
			{
				$sumapar0 += $line->amount*-1;
			}
		}
		$sumexpense = $sumadep;
		$sumexpense+=$sumapar0;
		//revisamos si existe cierre de caja
		$objdeplac->getlisttransfer(0,$object->id);
		$sumaparc = 0;
		$sumaparc0 = 0;
		$sumaparcierre = 0;
		$lCierre = false;
		foreach ((array) $objdeplac->lines AS $j => $line)
		{
			if ($line->status == 1)
			{
				$sumaparc += $line->amount*-1;
			}
			if ($line->status == 0)
			{
				$sumaparc0 += $line->amount*-1;
			}
			if ($line->status == 4)
			{
				$lCierre = true;
				$sumaparcierre += $line->amount*-1;
			}
		}
		$balance = price2num($sumatransf+$sumadep+$sumapar+$sumapar0+$sumaparcierre,'MT');

		//nueva forma de calculo
		$aBalance = saldoreq($object);
		$sumadep = $aBalance['sumadep'];
		$sumaappdep = $aBalance['sumaappdep'];
		$sumanoappdep = $aBalance['sumanoappdep'];
		$sumapar = $aBalance['sumapar'];
		$sumapar0 = $aBalance['sumapar0'];
		$sumaparcierrre = $aBalance['sumaparcierre'];
		$lCierre = $aBalance['lCierre'];

		print '<table class="noborder centpercent">'."\n";
		// Fields title
		print '<tr class="centpercent">';
		print '<td>'.($conf->browser->layout=='classic'?$langs->trans('Efectivo'):$langs->trans('Ef.')).'= <b>';
		print price($sumatransf).'</b></td>';
		print '<td>'.($conf->browser->layout=='classic'?$langs->trans('Gasto'):$langs->trans('G.')).'= <b>';
		print price($sumadep).'</b></td>';
		if (!$objtypecash->recharge)
		{
			print '<td>'.($conf->browser->layout=='classic'?$langs->trans('Transfer'):$langs->trans('T.')).'= <b>';
			print price($sumapar).'</b></td>';
		}
		//echo '<hr>'.$objtypecash->recharge.' '.$sumapar0;
		if (!$objtypecash->recharge || ($objtypecash->recharge && $sumapar0<>0) || $lCierre)
		{		
			print '<td>'.($conf->browser->layout=='classic'?$langs->trans('Transferpending'):$langs->trans('T.P.')).'= <b>';
			print '<span style="color:#ff0000;">'.price(!$lCierre?$sumapar0:$sumaparcierre).'</span></b></td>';
		}
		print '<td>'.$langs->trans('Balance').'= <b>';
		print price($balance).'</b></td>';
		print '</tr>';
		print '</table>';
		if ($objtypecash->recharge && $balance < $minallowed && $object->status < 5)
		{
			$sumrecharge = $object->amount - $balance+$sumanoappdep;
			if ($user->id == $object->fk_user_assigned || $user->id == $object->fk_user_create)
				setEventMessages($langs->trans('It is necessary to request recharge of funds'),null,'mesgs');
		}
	}
	dol_fiche_end();

	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);   
	 // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		$lContinue = true;
		if ($lTransfer || $lRequestnull) $lContinue = false;
		if ($lContinue)
		{
			if ($user->rights->finint->efe->mod && $object->status == 0)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
			}
			if ($user->rights->finint->efe->val && $object->status == 0)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans("Validate").'</a></div>'."\n";
			}

			if ($user->rights->finint->efe->del && $object->status == 0)
			{
				if ($conf->use_javascript_ajax && !empty($conf->dol_use_jmobile))
				// We can't use preloaded confirm form with jmobile
				{
					print '<div class="inline-block divButAction"><span id="action-delete" class="butActionDelete">'.$langs->trans('Delete').'</span></div>'."\n";
				}
				else
				{
					print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
				}
			}
			if ($user->rights->finint->efe->apo && $object->status == 1)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=approval">'.$langs->trans('Approve').'</a></div>'."\n";
			}
			if ($user->rights->finint->efe->napo && $object->status == 1)
			{
				print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=reject">'.$langs->trans('Reject').'</a></div>'."\n";
			}
			if ($user->rights->finint->efe->des && $object->status == 2)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=outlay">'.$langs->trans('Outlay').'</a></div>'."\n";
			}
			if ($user->rights->finint->desc->leer && $object->status == 3 && ($action!='discharg' && $action!='createrefr' && $action!='close' && $action!='transfd') || $user->rights->finint->desc->all && ($action!='discharg' && $action!='createrefr' && $action!='close' && $action!='transfd'))
			{
				if ($user->rights->finint->desc->all || ($user->id == $object->fk_user_assigned || $user->id == $object->fk_user_create))
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=discharg">'.$langs->trans('Discharges').'</a></div>'."\n";
			}
			if ($objtypecash->recharge && $object->status == 3 && $action != 'recharge')
			{
				if ($user->id == $object->fk_user_assigned || $user->id == $object->fk_user_create)
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=recharge">'.$langs->trans('Requestrecharge').'</a></div>'."\n";

			}

			if (!$objtypecash->recharge && $user->rights->finint->trans->leer)
			{
				if ($object->status == 3 && ($action!='transfer' && $action!='createtransfer' && $action!='discharg' && $action!='close' && $action != 'transfd'))
				{
					//if ($user->rights->finint->efe->all || ($user->id == $object->fk_user_assigned || $user->id == $object->fk_user_create))
					//	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=totransfer">'.$langs->trans('ToTransfer').'</a></div>'."\n";
				}
			}
			if (($object->status >=3 &&$object->status < 4) && ($user->admin || ($user->id == $object->fk_user_assigned || $user->id == $object->fk_user_create)) )
			{
				if (empty($sumapar0) && $action!='discharg' && $action != 'close' && $action !='transfd')
				{
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=close">'.$langs->trans('Close').'</a></div>'."\n";

				}
			}
			if (!$objtypecash->recharge && $user->rights->finint->desc->leer && $object->status == 3)
			{
				if ($user->admin || ($user->id == $object->fk_user_assigned || $user->id == $object->fk_user_create))
				{
					if (empty($sumapar0) && $action!='discharg' && $action != 'close' && $action !='transfd')
					{
					//	if ($balance>0)
					//		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=transfd">'.$langs->trans('Closetransfer').'</a></div>'."\n";				
					}
				}
			}
			if ($user->rights->finint->efe->valc && $object->status == 4)
			{
				//print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;idr='.$idr.'&amp;action=discharg">'.$langs->trans('Discharges').'</a></div>'."\n";				
			}			
		}
		if ($action=='discharg' || $action=='close' || $action=='transfd' || $action=='totransfer')
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">'.$langs->trans("Return").'</a></div>'."\n";
		else
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=list">'.$langs->trans("Return").'</a></div>'."\n";
	}
	print '</div>'."\n";

	
	if ($object->status == 0 || $action == 'listdet' || $action == 'outlay' || ($object->status == 1 && $action == 'approval'))
	{
		//include DOL_DOCUMENT_ROOT.'/finint/tpl/viewcashdet.tpl.php';
	}
	//action outlay - desembolso
	if ($action == 'outlay')
	{
		//buscamos la cuenta del que desembolsa
		$accountuserf = new Accountuser($db);
		$filterfrom = '';
		$filter = array(1=>1);

		$filterstatic = " AND t.fk_user = ".$user->id;
		$accountuserf->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
		$ids = '';
		foreach($accountuserf->lines AS $j => $line)
		{
			if(!empty($ids)) $ids.= ',';
			$ids.=$line->fk_account;
			if (empty($account_from)) $account_from = $line->fk_account;
		}
		if (!empty($ids))
			$filterfrom = " rowid IN (".$ids.")";
		$accountusert = new Accountuser($db);
		$filterto = '';
		$filter = array(1=>1);
		$filterstatic = " AND t.fk_user = ".$object->fk_user_create;
		$accountusert->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
		$ids = '';
		foreach($accountusert->lines AS $j => $line)
		{
			if(!empty($ids)) $ids.= ',';
			$ids.=$line->fk_account;
			if (empty($account_to)) $account_to = $line->fk_account;
		}
		if (!empty($ids))
			$filterto = " rowid IN (".$ids.")";

		if ($user->admin)
		{
			$filterfrom = '';
			$filterto = '';
		}
		//categories
		// Chargement des categories bancaires dans $options
		//list($nbcategories,$options) = getselcategorie();

		print_fiche_titre($langs->trans("Newoutlay"));

		dol_fiche_head();
		print '<form name="add" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$id.'">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

		print '<input type="hidden" name="action" value="addconf">';

		print '<table class="border centpercent">';

		print '<tr><td class="fieldrequired">'.$langs->trans('TransferForm').'</td>';
		print '<td>';
		print $form->select_comptes($account_from,'account_from',0,$filterfrom,0);
		print "</td>";
		print '</tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans('TransferTo').'</td>';
		print "<td>";
		print $form->select_comptes($account_to,'account_to',0,$filterto,0);
		print "</td>";
		print '</tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans('Type').'</td>';
		print '<td class="nowrap">';
		$form->select_types_paiements((GETPOST('fk_type')?GETPOST('fk_type'):($object->fk_type == 2 ? 'LIQ' : '')),'fk_type','1,2',2,1);
		print '</td>';
		print '</tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans('Categories').'</td>';
		print '<td class="nowrap">';
		print $form->select_all_categories('5','','cat1');
		if ($nbcategories)
		{
			print '<select class="flat" name="cat1">'.$options.'</select>';
		}
		print '</td>';
		print '</tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans('Number').'</td>';
		print '<td class="nowrap">';
		print '<input type="text" name="numdoc" value="'.(!GETPOST('numdoc')?'':GETPOST('numdoc')).'" size="5">';
		print '</td>';
		print '</tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans('For').'</td>';
		print "<td>";
		if ($object->fk_user_create == $user->id)
		{
			print $user->lastname.' '.$user->firstname;
			print '<input type="hidden" name = "user_to" value="'.$user->id.'">';
		}
		else
			print $form->select_users($object->fk_user_create,'user_to',(!$object->fk_user_create==1?1:0),array(1),0,array($object->fk_user_create));
		print "</td>";
		print '</tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td>';
		print "<td>";

		$form->select_date((empty($dateo)?dol_now():$dateo),'do','','',1,'add',1);
		print "</td>\n";
		print '</tr>';
		print '<tr><td>'.$langs->trans('Label').'</td>';
		print '<td><input name="label" class="flat" type="text" size="40" value="'.(empty($label)?$object->detail:$label).'"></td>';
		print '</tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans('Amount').'</td>';
		print '<td><input name="amount" class="flat" type="number" min="0" step="any" value="'.(empty($amount)?$object->amount_approved:$amount).'"></td>';
		print '</tr>';
		print "</table>";

		print '<br><center><input type="submit" class="button" value="'.$langs->trans("Disburse").'"></center>';

		print "</form>";
		dol_fiche_end();

	}


	//action discharg || createrefr
	$subaction = 0;
	if ($object->status == 5 || $action == 'discharg' || $action == 'createrefr' || $action == 'modifyrefr' || $action == 'modifyvalrefr' || $action=='close' || $action=='mod_dep' || $action=='mod_depval' || $action=='recharge' || $action == 'transfd' || $action == 'formapprecharge')
	{
		//action 'createrefr';
		if ($action == 'createrefr')
		{
			$dateo = dol_mktime(12,0,0,GETPOST('domonth','int'),GETPOST('doday','int'),GETPOST('doyear','int'));
			$user_to = GETPOST('user_to');
			$label = GETPOST('label');
			$amount = GETPOST('amount');
			$fk_projetsel = GETPOST('fk_proj');
			$fk_tasksel = GETPOST('fk_tas');
			$code_facture = GETPOST('code_f');
			foreach ($_POST AS $key => $value)
			{
				//echo '<hr>key '.$key.' => '.$value;
				$aKey = explode('__',$key);
				//echo '<hr>';
				//print_r($aKey);
				if ($aKey[0] == 'fk_projet')
				{
					$varprojet[$aKey[1]] = $_POST[$key];
				}
				elseif ($aKey[0] == 'fk_task')
				{
					$vartask[$aKey[1]] = $_POST[$key];
				}
				elseif ($aKey[0]== 'num_chq')
				{
					$_POST['num_chq__'.$aKey[1]] = $_POST[$key];
				}
				elseif ($aKey[0]== 'do__')
				{
					$date_o = dol_mktime(12,0,0,GETPOST('do__'.$aKey[1].'month','int'),GETPOST('do__'.$aKey[1].'day','int'),GETPOST('do__'.$aKey[1].'year','int'));
					$_POST['do__'.$aKey[1]] = $date_o;
				}
				elseif ($aKey[0]== 'dp_desc')
				{
					$_POST['dp_desc__'.$aKey[1]] = $_POST[$key];
				}
				elseif ($aKey[0]== 'amount')
				{
					$_POST['amount__'.$aKey[1]] = $_POST[$key];
				}
			}
			//echo '<hr>res <pre>';
			//print_r($aKey);
			//echo '</pre>';
		}
		if ($action == 'modifyrefr')
		{
			$myclass = new Requestcashdeplacementext($db);
			$myclass->fetch(GETPOST('idrcd'));
			$myclass->dateo = dol_mktime(12,0,0,GETPOST('do_month','int'),GETPOST('do_day','int'),GETPOST('do_year','int'));
			$aDateoo = explode('-',$_POST['do_']);
			$myclass->dateo = dol_mktime(12,0,0,$aDateoo[1],$aDateoo[2],$aDateoo[0]);

			$myclass->user_to = GETPOST('user_to');
			$myclass->detail = GETPOST('dp_desc');
			$myclass->amount = GETPOST('amount');
			$myclass->amount_ttc = GETPOST('amount');
			$myclass->fk_projet_dest = GETPOST('fk_projet__0');
			$fk_projetsel = GETPOST('fk_projet__0');
			if ($fk_projetsel>0) $filtertask = " t.fk_projet = ".$fk_projetsel;
			$myclass->fk_projet_task_dest = GETPOST('fk_task');
			$myclass->code_facture = GETPOST('code_f');
		}
		if ($action == 'modifyvalrefr')
		{
			$myclass = new Requestcashdeplacementext($db);
			$myclass->fetch(GETPOST('idrcd'));
			$myclass->dateo = dol_mktime(12,0,0,GETPOST('do_month','int'),GETPOST('do_day','int'),GETPOST('do_year','int'));
			$aDateoo = explode('-',$_POST['do_']);
			$myclass->dateo = dol_mktime(12,0,0,$aDateoo[1],$aDateoo[2],$aDateoo[0]);

			$myclass->user_to = GETPOST('user_to');
			$myclass->detail = GETPOST('dp_desc');
			$myclass->amount = GETPOST('amount');
			$myclass->amount_ttc = GETPOST('amount');
			$myclass->fk_projet_dest = GETPOST('fk_projet__0');
			$fk_projetsel = GETPOST('fk_projet__0');
			if ($fk_projetsel>0) $filtertask = " t.fk_projet = ".$fk_projetsel;
			$myclass->fk_projet_task_dest = GETPOST('fk_task');
			$myclass->code_facture = GETPOST('code_f');
			$action = 'mod_depval';
			$subaction = 1;
		}
		include DOL_DOCUMENT_ROOT.'/finint/tpl/discharg.tpl.php';
	}
	//documentos ver
	if ($object->status >=1)
	{
		//revisar
		print '<div class="tabsAction">';
		//documents
		print '<table width="100%"><tr><td width="50%" valign="top">';
		print '<a name="builddoc"></a>'; // ancre
		$object->fetch($id);
		// Documents generes
		$filename=dol_sanitizeFileName($object->ref);
		//cambiando de nombre al reporte
		$filedir=$conf->finint->dir_output . '/' . dol_sanitizeFileName($object->ref);
		$urlsource=$_SERVER['PHP_SELF'].'?id='.$id;
		$genallowed=$user->rights->finint->docpdf->crear;
		$delallowed=$user->rights->finint->docpdf->del;
		$object->model_pdf = 'finint';
		print $formfile->showdocuments('finint',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->model_pdf,1,0,0,28,0,'','','',$soc->default_lang);
		$somethingshown=$formfile->numoffiles;
		print '</td></tr></table>';

		print "</div>";
	}

}


// End of page
llxFooter();
$db->close();
