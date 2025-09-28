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
 *   	\file       purchase/purchaserequest_card.php
 *		\ingroup    purchase
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-10 09:46
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
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.getutil.class.php');
require_once(DOL_DOCUMENT_ROOT.'/user/class/user.class.php');
require_once(DOL_DOCUMENT_ROOT.'/product/class/product.class.php');
dol_include_once('/purchase/class/purchaserequestext.class.php');
dol_include_once('/purchase/class/purchaserequestdetext.class.php');
dol_include_once('/purchase/lib/purchase.lib.php');
if ($conf->monprojet->enabled)
{
	dol_include_once('/monprojet/class/projectext.class.php');
	dol_include_once('/monprojet/class/html.formprojetext.class.php');
	dol_include_once('/monprojet/lib/verifcontact.lib.php');
}
if ($conf->poa->enabled)
{
	dol_include_once('/poa/class/configstructure.class.php');
	dol_include_once('/poa/class/poaobjetiveext.class.php');
	dol_include_once('/poa/class/poastructureext.class.php');
	dol_include_once('/poa/class/poaactivityext.class.php');
	dol_include_once('/poa/class/poapoaext.class.php');
	dol_include_once('/poa/class/poapoauserext.class.php');
	dol_include_once('/poa/class/poaprevext.class.php');
	dol_include_once('/poa/class/poaprocessext.class.php');
	dol_include_once('/poa/class/poapartidapreext.class.php');
	dol_include_once('/poa/class/poapartidapredetext.class.php');
	dol_include_once('/poa/class/poareformulatedext.class.php');
	dol_include_once('/poa/class/poareformulateddetext.class.php');
	dol_include_once('/poa/class/poaprevlog.class.php');
	dol_include_once('/poa/lib/poa.lib.php');
}
if ($conf->orgman->enabled)
{
	dol_include_once('/orgman/class/partidaproduct.class.php');
	dol_include_once('/orgman/class/cpartida.class.php');
	dol_include_once('/orgman/class/pdepartamentext.class.php');
	dol_include_once('/orgman/class/pdepartamentuserext.class.php');
	dol_include_once('/orgman/lib/departament.lib.php');
}
// Load traductions files requiredby by page
$langs->load("poa");
$langs->load("purchase");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$lineid		= GETPOST('lineid','int');
$fk_poa 	= GETPOST('fk_poa','int');
$action		= GETPOST('action','alpha');
$confirm	= GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$date_delivery = dol_mktime(GETPOST('dd_hour'), GETPOST('dd_min'), 0, GETPOST('dd_month'), GETPOST('dd_day'), GETPOST('dd_year'));
if (empty($date_delivery)) $date_delivery = dol_now();

$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_ref_int=GETPOST('search_ref_int','alpha');
$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_departament=GETPOST('search_fk_departament','int');
$search_fk_user_author=GETPOST('search_fk_user_author','int');
$search_fk_user_modif=GETPOST('search_fk_user_modif','int');
$search_fk_user_valid=GETPOST('search_fk_user_valid','int');
$search_fk_user_cloture=GETPOST('search_fk_user_cloture','int');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_model_pdf=GETPOST('search_model_pdf','alpha');
$search_fk_shipping_method=GETPOST('search_fk_shipping_method','int');
$search_import_key=GETPOST('search_import_key','alpha');
$search_extraparams=GETPOST('search_extraparams','alpha');
$search_status=GETPOST('search_status','int');

verify_year();
$period_year = $_SESSION['period_year'];

$aTypeprocess = array(1=>array('WELL' => $langs->trans('Goods')),0=>array('OTHERSERVICE'=>$langs->trans('Otherservice'),'SERVICE'=>$langs->trans('Service')));
// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object = new Purchaserequestext($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objuser = new User($db);
$objectdet=new Purchaserequestdetext($db);
$product = new Product($db);
if ($conf->orgman->enabled)
{
	$objDepartament = new Pdepartamentext($db);
	$objDepartamentuser = new Pdepartamentuserext($db);
}
// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('purchaserequest'));
$extrafields = new ExtraFields($db);

//filtro por area
$aFilterent = array();
list($aAreadirect,$fk_areaasign,$filterarea,$aFilterarea, $fk_user_resp, $fk_departament_sup)= verif_departament($user->id);
//echo '<br>fkarea '.$fk_areaasign.' fk_user_resp '.$fk_user_resp.' filter '.$filterarea.' sup '.$fk_departament_sup;
//si esta integrado a poa vamos a determinar si su departamento cuenta o no con presupuesto
//si no cuenta vamos a ver si su superior cuenta
$lViewareasup=true;
if ($conf->global->PURCHASE_INTEGRATED_POA)
{
	$objPoaobjetive = new Poaobjetive($db);
	$objCname = new Configstructure($db);
	//verificamos el numero de niveles de los objetivos
	$filterstatic = "";
	$nObjetive = $objCname->fetchAll('ASC','ref',0,0,array('active'=>1,''),'AND'," AND t.entity = ".$conf->entity." AND t.type='".$conf->global->POA_CODE_OBJ."'" );
	$aCname = array();
	$aIndicator = array();
	$aDigits = array();
	if ($nObjetive > 0)
	{
		foreach ($objCname->lines AS $j => $line)
		{
			$aCname[$line->ref] = $line->label;
			$aIndicator[$line->ref] = $line->indicator;
			$aDigits[$line->ref] = $line->ndigits;
		}
	}
	//vamos a ver que objetivos con el nivel maximo estan destinados al departamento
	$filterObjetive = " AND t.fk_area = ".$fk_areaasign;
	$filterObjetive.= " AND t.level = ".$nObjetive;
	$restmp = $objPoaobjetive->fetchAll('','',0,0,array(),'AND',$filterObjetive);
	if ($restmp>0)
	{
		//solo puede ver de su area
		$lViewareasup = false;
	}
}



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
	if ($action == 'createval' && $_REQUEST['addvalidate'])
	{
		$action = 'validate';
	}

	if ($action == 'confirm_valplan' && $_REQUEST['confirm'] == 'yes')
	{
		$now = dol_now();
		$object->fetch($id);
		if (GETPOST('idr') == $object->fk_poa_prev)
		{
			$objPoaprev = new Poaprevext($db);
			$res = $objPoaprev->fetch($object->fk_poa_prev);
			if ($res > 0)
			{
				$objPoaprev->status_plan = 1;
				$objPoaprev->fk_user_valplan = $user->id;
				$objPoaprev->date_valplan = dol_now();
				if ($objPoaprev->status_pres == 1)
					$objPoaprev->statut = 1;
				$res = $objPoaprev->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objPoaprev->error,$objPoaprev->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages($objPoaprev->error,$objPoaprev->errors,'errors');
			}
			if (!$error)
			{
				//procesamos registro de log
				$objPoaprevlog = new Poaprevlog($db);
				$objPoaprevlog->fk_poa_prev = $object->fk_poa_prev;
				$objPoaprevlog->refaction = $action;
				$objPoaprevlog->description = GETPOST('description','alpha');
				$objPoaprevlog->status = $objPoaprev->statut;
				$objPoaprevlog->fk_user_create = $user->id;
				$objPoaprevlog->fk_user_mod = $user->id;
				$objPoaprevlog->datec = $now;
				$objPoaprevlog->datem = $now;
				$objPoaprevlog->tms = $now;
				$res = $objPoaprevlog->create($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objPoaprevlog->error,$objPoaprevlog->errors,'errors');
				}
			}
			//verificamos si status_plan y status_pres estan en 1
			//si es afirmativo cambiamos statut = 1
			//verificamos si esta aprobado por los dos
			if ($objPoaprev->status_plan && $objPoaprev->status_pres)
			{
				// Define output language
				if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
				{
					$outputlangs = $langs;
					$newlang = '';
					if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
					if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
					if (! empty($newlang)) {
						$outputlangs = new Translate("", $conf);
						$outputlangs->setDefaultLang($newlang);
					}
					$modelpdf = 'preventive';

					$result=$objPoaprev->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
					if ($result < 0) dol_print_error($db,$result);
				}
			}
			if (!$error)
			{
				setEventMessages($langs->trans('Successfullvalidation'),null,'mesgs');
				header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
		}
		$action = '';
	}
	if ($action == 'confirm_valpres' && $_REQUEST['confirm'] == 'yes')
	{
		$now = dol_now();
		$object->fetch($id);
		if (GETPOST('idr') == $object->fk_poa_prev)
		{
			$objPoaprev = new Poaprevext($db);
			$res = $objPoaprev->fetch($object->fk_poa_prev);
			if ($res > 0)
			{
				$objPoaprev->status_pres = 1;
				$objPoaprev->fk_user_valpres = $user->id;
				$objPoaprev->date_valpres = dol_now();
				if ($objPoaprev->status_plan == 1)
					$objPoaprev->statut = 1;
				$res = $objPoaprev->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objPoaprev->error,$objPoaprev->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages($objPoaprev->error,$objPoaprev->errors,'errors');
			}
			if (!$error)
			{
				//procesamos registro de log
				$objPoaprevlog = new Poaprevlog($db);
				$objPoaprevlog->fk_poa_prev = $object->fk_poa_prev;
				$objPoaprevlog->refaction = $action;
				$objPoaprevlog->description = GETPOST('description','alpha');
				$objPoaprevlog->status = $objPoaprev->statut;
				$objPoaprevlog->fk_user_create = $user->id;
				$objPoaprevlog->fk_user_mod = $user->id;
				$objPoaprevlog->datec = $now;
				$objPoaprevlog->datem = $now;
				$objPoaprevlog->tms = $now;
				$res = $objPoaprevlog->create($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objPoaprevlog->error,$objPoaprevlog->errors,'errors');
				}
			}
			//verificamos si esta aprobado por los dos
			if ($objPoaprev->status_plan && $objPoaprev->status_pres)
			{
				// Define output language
				if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
				{
					$outputlangs = $langs;
					$newlang = '';
					if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
					if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
					if (! empty($newlang)) {
						$outputlangs = new Translate("", $conf);
						$outputlangs->setDefaultLang($newlang);
					}
					$modelpdf = 'preventive';

					$result=$objPoaprev->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
					if ($result < 0) dol_print_error($db,$result);
				}
			}
			if (!$error)
			{
				setEventMessages($langs->trans('Successfullvalidation'),null,'mesgs');
				header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
		}
		$action = '';
	}

	if ($action == 'confirm_validate' && $_REQUEST['confirm'] == 'yes')
	{
		$now = dol_now();
		$aPost = unserialize($_SESSION['aPurchaseValidate']);
		$_POST = $aPost[$id];
		$object->fetch($id);
		//vamos a verificar varios puntos
		$db->begin();
		if ($conf->global->PURCHASE_INTEGRATED_POA)
		{
			$objpoa = new Poapoaext($db);
			$objPoauser = new Poapoauser($db);
			//recuperamos el poa para obtener la estructura del mismo
			$fk_structure = GETPOST('fk_structure');
			//$res = $objpoa->fetch(GETPOST('fk_poa'));
			//if ($res<=0)
			//{
			//	$error=98;
			//	setEventMessages($objpoa->error,$objpoa->errors,'errors');
			//}
			$objactivity = new Poaactivityext($db);
			$objprev = new Poaprevext($db);
			$objppp = new Poapartidapreext($db);
			//debemos crear la actividad si no esta creado
			$fk_departament = $object->fk_departament;
			$note_public = $object->note_public;
			$datec = $object->datec;
			$subaction = 'addactivity';
			$aPoa = GETPOST('aPoa');
			foreach ($aPoa AS $fk_poa => $j)
			{

			}
			include DOL_DOCUMENT_ROOT.'/poa/activity/inc/abm_activity.inc.php';

			if ($idact <=0)
			{
				$error=99;
			}
			if (!$error && $idact > 0)
			{
				//creamos el preventivo
				$objprev = new Poaprevext($db);

				$objprev->entity = $conf->entity;
				$objprev->gestion = $period_year;
				$objprev->fk_pac = GETPOST('fk_pac')+0;
				$objprev->fk_father = GETPOST('fk_father')+0;
				$objprev->fk_area = $object->fk_departament;
				$objprev->fk_poa_activity = $idact;
				$objprev->origin = 'purchaserequest';
				$objprev->originid = $object->id;
				$objprev->code_requirement = $objactivity->code_requirement;
				$objprev->label = $objactivity->label;
				$objprev->pseudonym = $objactivity->pseudonym;
				$objprev->nro_preventive = $objactivity->nro_activity;
				$objprev->date_preventive = $objactivity->date_activity;
				$objprev->amount = $objactivity->amount+0;
				$objprev->priority = $objactivity->priority;
				$objprev->fk_user_create = $user->id;
				$objprev->fk_user_mod = $user->id;
				$objprev->datec = $now;
				$objprev->datem = $now;
				$objprev->statut = -2;
				$objprev->status_plan = 0;
				$objprev->status_pres = 0;
				$objprev->active = 1;

				$idprev = $objprev->create($user);
				if ($idprev <=0)
				{
					$error=100;
					setEventMessages($objprev->error,$objprev->errors,'errors');
				}
				//agregamos el vinculo
				if (!$error)
				{
					$resprev = $objprev->add_object_linked($object->element, $object->id);
					if ($resprev <=0)
					{
						$error++;
						setEventMessages($objprev->error,$objprev->errors,'errors');
					}
				}

				//agregamos como poa user
				if (!$error)
				{

					$aPoa = GETPOST('aPoa');
					foreach ($aPoa AS $fk_poa => $j)
					{
						$respu = $objPoauser->fetch(0,$fk_poa,$user->id);
						if ($respu == 0)
						{
							$objPoauser->fk_poa_poa = $fk_poa;
							$objPoauser->fk_user = $user->id;
							$objPoauser->order_user = 1;
							$objPoauser->date_create = $now;
							$objPoauser->fk_user_create = $user->id;
							$objPoauser->fk_user_mod = $user->id;
							$objPoauser->datec = $now;
							$objPoauser->datem = $now;
							$objPoauser->tms = $now;
							$objPoauser->statut = 1;
							$objPoauser->active = 1;
							$res = $objPoauser->create($user);
							if ($res <= 0)
							{
								$error=101;
								setEventMessages($objPoauser->error,$objPoauser->errors,'errors');
							}
						}
					}
				}
				if (!$error)
				{
					//procesamos registro de log
					$objPoaprevlog = new Poaprevlog($db);
					$objPoaprevlog->fk_poa_prev = $idprev;
					$objPoaprevlog->refaction = $action;
					$objPoaprevlog->description = GETPOST('description','alpha');
					$objPoaprevlog->status = $objprev->statut;
					$objPoaprevlog->fk_user_create = $user->id;
					$objPoaprevlog->fk_user_mod = $user->id;
					$objPoaprevlog->datec = $now;
					$objPoaprevlog->datem = $now;
					$objPoaprevlog->tms = $now;
					$res = $objPoaprevlog->create($user);
					if ($res <=0)
					{
						$error=102;
						setEventMessages($objPoaprevlog->error,$objPoaprevlog->errors,'errors');
					}
				}
				if (!$error)
				{

					//cargamos las partidas afectadas
					$aPartida = GETPOST('aPartida');
					$aPartidadet = GETPOST('aPartidadet');
					$aPartidaorig = GETPOST('aPartidaorig');
					$aPoapartida = GETPOST('aPoapartida');
					$aPoapartidadet = GETPOST('aPoapartidadet');
					$aPoapartidaorig = GETPOST('aPoapartidaorig');

					$sumAmount = 0;
					foreach ($aPoapartida AS $fk_poa => $aPart)
					{
						foreach ($aPart AS $codepartida => $valuepar)
						{
							$sumAmount+= $valuepar;
							$objppp->fk_poa_prev = $idprev;
							$objppp->fk_structure = $fk_structure;
							$objppp->fk_poa = $fk_poa;
							$objppp->partida = $codepartida;
							$objppp->amount = $valuepar;
							$objppp->fk_user_create = $user->id;
							$objppp->fk_user_mod = $user->id;
							$objppp->datec = $now;
							$objppp->datem = $now;
							$objppp->tms = $now;
							$objppp->statut = 1;
							$objppp->active = 1;
							$idppp = $objppp->create($user);
							if ($idppp<=0)
							{
								$error=103;
								setEventMessages($objppp->error,$objppp->errors,'errors');
							}
						//guardamos el detallde de la partida
							if (!$error)
							{
								$objpppd = new Poapartidapredetext($db);
								foreach ($aPoapartidaorig[$fk_poa][$codepartida] AS $fk_product => $aData)
								{
									foreach ($aData AS $lineid => $value)
									{
									//recuperamos la linea de objectdet para obtener el detalle
										$objectdet->fetch($lineid);
										$objpppd->fk_poa_partida_pre = $idppp;
										$objpppd->fk_product = $fk_product;
										$objpppd->fk_contrat = 0;
										$objpppd->fk_contrato = 0;
										$objpppd->fk_poa_partida_com = 0;
										$objpppd->origin = 'purchaserequestdet';
										$objpppd->originid = $lineid;
										$objpppd->quant = $objectdet->qty;
										$objpppd->amount_base = $value;
										$objpppd->detail = $objectdet->description;
										$objpppd->quant_adj = 0;
										$objpppd->amount = 0;
										$objpppd->fk_user_create = $user->id;
										$objpppd->fk_user_mod = $user->id;
										$objpppd->datec = $now;
										$objpppd->datem = $now;
										$objpppd->tms = $now;
										$objpppd->statut = 1;

										$idpppd = $objpppd->create($user);
										if ($idpppd<=0)
										{
											$error=104;
											setEventMessages($objpppd->error,$objpppd->errors,'errors');
										}
									}
								}
							}
						}
					}
				}
				//actualizamos el idprev en la actividad
				if ($idact>0)
				{
					$objactivity->fetch($idact);
					$objactivity->fk_prev = $idprev;
					$objactivity->amount = $sumAmount;
					$resact = $objactivity->update($user);
					if ($resact <=0)
					{
						$error=105;
						setEventMessages($objactivity->error,$objactivity->errors,'errors');
					}
				}

				if (!$error)
				{
					$res = $objprev->fetch($idprev);
					if ($res > 0)
					{
						$objprev->amount = $sumAmount;
						$res = $objprev->update($user);
						if ($res <=0)
						{
							$error=106;
							setEventMessages($obprev->error,$objprev->errors,'errors');
						}
					}
					else
					{
						$error=107;
						setEventMessages($obprev->error,$objprev->errors,'errors');
					}
				}
			}
		}
		if (!$error)
		{
			if ($object->id == $id)
			{
				$ref = substr($object->ref, 1, 4);
				if ($ref == 'PROV')
				{
					$numref = $object->getNextNumRef($soc);
				}
				else
				{
					$numref = $object->ref;
				}
				$object->ref = $numref;
				$object->status = 1;
				$object->fk_poa_prev = $idprev+0;
				$object->fk_user_mod = $user->id;
				$object->datem = dol_now();
				$object->tms = dol_now();

				$res = $object->update($user);

				if ($res <=0)
				{
					$error=108;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages($langs->trans('Los registros no son iguales'),null,'errors');
			}
		}
		//echo '<hr>errqc '.$error;
		if (!$error)
		{
			$db->commit();
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			$db->rollback();
			$action = '';
		}
	}

	if ($action == 'confirm_validate_original' && $_REQUEST['confirm'] == 'yes')
	{
		$now = dol_now();
		$aPost = unserialize($_SESSION['aPurchaseValidate']);
		$_POST = $aPost[$id];
		$object->fetch($id);
		//vamos a verificar varios puntos
		$db->begin();
		if ($conf->global->PURCHASE_INTEGRATED_POA)
		{
			$objpoa = new Poapoaext($db);
			$objPoauser = new Poapoauser($db);
			//recuperamos el poa para obtener la estructura del mismo
			$res = $objpoa->fetch(GETPOST('fk_poa'));
			if ($res<=0)
			{
				$error=98;
				setEventMessages($objpoa->error,$objpoa->errors,'errors');
			}
			$objactivity = new Poaactivityext($db);
			$objprev = new Poaprevext($db);
			$objppp = new Poapartidapreext($db);
			//debemos crear la actividad si no esta creado
			$fk_departament = $object->fk_departament;
			$note_public = $object->note_public;
			$datec = $object->datec;
			$subaction = 'addactivity';
			include DOL_DOCUMENT_ROOT.'/poa/activity/inc/abm_activity.inc.php';
			if ($idact <=0)
			{
				$error=99;
			}
			if (!$error && $idact > 0)
			{
				//creamos el preventivo
				$objprev = new Poaprevext($db);

				$objprev->entity = $conf->entity;
				$objprev->gestion = $period_year;
				$objprev->fk_pac = GETPOST('fk_pac')+0;
				$objprev->fk_father = GETPOST('fk_father')+0;
				$objprev->fk_area = $object->fk_departament;
				$objprev->fk_poa_activity = $idact;
				$objprev->origin = 'purchaserequest';
				$objprev->originid = $object->id;
				$objprev->code_requirement = $objactivity->code_requirement;
				$objprev->label = $objactivity->label;
				$objprev->pseudonym = $objactivity->pseudonym;
				$objprev->nro_preventive = $objactivity->nro_activity;
				$objprev->date_preventive = $objactivity->date_activity;
				$objprev->amount = $objactivity->amount+0;
				$objprev->priority = $objactivity->priority;
				$objprev->fk_user_create = $user->id;
				$objprev->fk_user_mod = $user->id;
				$objprev->datec = $now;
				$objprev->datem = $now;
				$objprev->statut = -2;
				$objprev->status_plan = 0;
				$objprev->status_pres = 0;
				$objprev->active = 1;

				$idprev = $objprev->create($user);
				if ($idprev <=0)
				{
					$error=100;
					setEventMessages($objprev->error,$objprev->errors,'errors');
				}
				//agregamos como poa user
				if (!$error)
				{
					$respu = $objPoauser->fetch(0,GETPOST('fk_poa'),$user->id);
					if ($respu == 0)
					{
						$objPoauser->fk_poa_poa = GETPOST('fk_poa');
						$objPoauser->fk_user = $user->id;
						$objPoauser->order_user = 1;
						$objPoauser->date_create = $now;
						$objPoauser->fk_user_create = $user->id;
						$objPoauser->fk_user_mod = $user->id;
						$objPoauser->datec = $now;
						$objPoauser->datem = $now;
						$objPoauser->tms = $now;
						$objPoauser->statut = 1;
						$objPoauser->active = 1;
						$res = $objPoauser->create($user);
						if ($res <= 0)
						{
							$error=101;
							setEventMessages($objPoauser->error,$objPoauser->errors,'errors');
						}
					}
				}
				if (!$error)
				{
					//procesamos registro de log
					$objPoaprevlog = new Poaprevlog($db);
					$objPoaprevlog->fk_poa_prev = $idprev;
					$objPoaprevlog->refaction = $action;
					$objPoaprevlog->description = GETPOST('description','alpha');
					$objPoaprevlog->status = $objprev->statut;
					$objPoaprevlog->fk_user_create = $user->id;
					$objPoaprevlog->fk_user_mod = $user->id;
					$objPoaprevlog->datec = $now;
					$objPoaprevlog->datem = $now;
					$objPoaprevlog->tms = $now;
					$res = $objPoaprevlog->create($user);
					if ($res <=0)
					{
						$error=102;
						setEventMessages($objPoaprevlog->error,$objPoaprevlog->errors,'errors');
					}
				}
				if (!$error)
				{

					//cargamos las partidas afectadas
					$aPartida = GETPOST('aPartida');
					$aPartidadet = GETPOST('aPartidadet');
					$aPartidaorig = GETPOST('aPartidaorig');
					$sumAmount = 0;
					foreach ($aPartida AS $codepartida => $valuepar)
					{
						$sumAmount+= $valuepar;
						$objppp->fk_poa_prev = $idprev;
						$objppp->fk_structure = $objpoa->fk_structure;
						$objppp->fk_poa = $objpoa->id;
						$objppp->partida = $codepartida;
						$objppp->amount = $valuepar;
						$objppp->fk_user_create = $user->id;
						$objppp->fk_user_mod = $user->id;
						$objppp->datec = $now;
						$objppp->datem = $now;
						$objppp->tms = $now;
						$objppp->statut = 1;
						$objppp->active = 1;
						$idppp = $objppp->create($user);
						if ($idppp<=0)
						{
							$error=103;
							setEventMessages($objppp->error,$objppp->errors,'errors');
						}
						//guardamos el detallde de la partida
						if (!$error)
						{
							$objpppd = new Poapartidapredetext($db);
							foreach ($aPartidaorig[$codepartida] AS $fk_product => $aData)
							{
								foreach ($aData AS $lineid => $value)
								{
									//recuperamos la linea de objectdet para obtener el detalle
									$objectdet->fetch($lineid);
									$objpppd->fk_poa_partida_pre = $idppp;
									$objpppd->fk_product = $fk_product;
									$objpppd->fk_contrat = 0;
									$objpppd->fk_contrato = 0;
									$objpppd->fk_poa_partida_com = 0;
									$objpppd->origin = 'purchaserequestdet';
									$objpppd->originid = $lineid;
									$objpppd->quant = $objectdet->qty;
									$objpppd->amount_base = $value;
									$objpppd->detail = $objectdet->description;
									$objpppd->quant_adj = 0;
									$objpppd->amount = 0;
									$objpppd->fk_user_create = $user->id;
									$objpppd->fk_user_mod = $user->id;
									$objpppd->datec = $now;
									$objpppd->datem = $now;
									$objpppd->tms = $now;
									$objpppd->statut = 1;

									$idpppd = $objpppd->create($user);
									if ($idpppd<=0)
									{
										$error=104;
										setEventMessages($objpppd->error,$objpppd->errors,'errors');
									}
								}
							}
						}
					}
				}
				//actualizamos el idprev en la actividad
				if ($idact>0)
				{
					$objactivity->fetch($idact);
					$objactivity->fk_prev = $idprev;
					$objactivity->amount = $sumAmount;
					$resact = $objactivity->update($user);
					if ($resact <=0)
					{
						$error=105;
						setEventMessages($objactivity->error,$objactivity->errors,'errors');
					}
				}

				if (!$error)
				{
					$res = $objprev->fetch($idprev);
					if ($res > 0)
					{
						$objprev->amount = $sumAmount;
						$res = $objprev->update($user);
						if ($res <=0)
						{
							$error=106;
							setEventMessages($obprev->error,$objprev->errors,'errors');
						}
					}
					else
					{
						$error=107;
						setEventMessages($obprev->error,$objprev->errors,'errors');
					}
				}
			}
		}
		if (!$error)
		{
			if ($object->id == $id)
			{
				$ref = substr($object->ref, 1, 4);
				if ($ref == 'PROV')
				{
					$numref = $object->getNextNumRef($soc);
				}
				else
				{
					$numref = $object->ref;
				}
				$object->ref = $numref;
				$object->status = 1;
				$object->fk_poa_prev = $idprev+0;
				$object->fk_user_mod = $user->id;
				$object->datem = dol_now();
				$object->tms = dol_now();

				$res = $object->update($user);

				if ($res <=0)
				{
					$error=108;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages($langs->trans('Los registros no son iguales'),null,'errors');
			}
		}
		//echo '<hr>errqc '.$error;
		if (!$error)
		{
			$db->commit();
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			$db->rollback();
			$action = '';
		}
	}





	//confirm_approve
	//SI conf->global->PURCHASE_INTEGRADE_POA no esta definido
	if ($action == 'confirm_approve' && $_REQUEST['confirm'] == 'yes')
	{
		$now = dol_now();
		$res = $object->fetch($id);
		if ($res <=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		//vamos a verificar varios puntos
		$db->begin();
		if (!$error)
		{
			$ref = substr($object->ref, 1, 4);
			if ($ref == 'PROV')
			{
				$numref = $object->getNextNumRef($soc);
			}
			else
			{
				$numref = $object->ref;
			}
			$object->ref = $numref;
			$object->status = 1;
			$object->status_process = 1;
			$object->fk_poa_prev = 0;
			$object->fk_user_mod = $user->id;
			$object->datem = dol_now();
			$object->tms = dol_now();

			$res = $object->update($user);

			if ($res <=0)
			{
				$db->rollback();
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
				$action = '';
			}
			else
			{
				$db->commit();
				header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
		}
	}

	// Remove a product line
	if ($action == 'confirm_deleteline' && $confirm == 'yes' && $user->rights->purchase->req->del)
	{
		$objectdet->fetch($lineid);
		$result = $objectdet->delete($user);
		if ($result <=0) $error++;

		if ($result > 0 && !$error)
		{
			// Define output language
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id'))
				$newlang = GETPOST('lang_id');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))
				$newlang = $object->thirdparty->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE)) {
			//	$ret = $object->fetch_($object->id);
				// Reload to get new records
			//	$object->generateDocument($object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
			}

			header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);
			exit;
		}
		else
		{
			setEventMessages($object->error, $object->errors, 'errors');
			/* Fix bug 1485 : Reset action to avoid asking again confirmation on failure */
			$action='';
		}
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/purchase/request/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;
		/* object_prop_getpost_prop */
		$date_delivery = dol_mktime(GETPOST('dd_hour'), GETPOST('dd_min'), 0, GETPOST('dd_month'), GETPOST('dd_day'), GETPOST('dd_year'));

		$object->entity=$conf->entity;
		$object->ref='(PROV)';
		$object->ref_ext=GETPOST('ref_ext','alpha');
		$object->ref_int=GETPOST('ref_int','alpha');
		$object->fk_projet=GETPOST('fk_projet','int')+0;
		$object->fk_departament=GETPOST('fk_departament','int')+0;
		$object->fk_user_author=$user->id;
		$object->fk_user_modif=$user->id;
		$object->fk_user_valid=0;
		$object->fk_user_cloture=0;
		$object->note_private=GETPOST('note_private','alpha');
		$object->note_public=GETPOST('note_public','alpha');
		$object->model_pdf='fractal';
		$object->origin=GETPOST('origin','alpha');
		$object->originid=GETPOST('originid','int');
		if (empty($object->originid)) $object->originid = 0;
		$object->fk_shipping_method=GETPOST('fk_shipping_method','int');
		if (empty($object->fk_shipping_method)) $object->fk_shipping_method = 0;
		$object->import_key=GETPOST('import_key','alpha');
		$object->extraparams=GETPOST('extraparams','alpha');
		$object->status=0;
		$object->status_process=0;
		$object->status_purchase=0;
		$object->date_delivery = $date_delivery;
		$object->datec = dol_now();
		$object->datem = dol_now();
		$object->tms = dol_now();

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if ($object->fk_departament <=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Area solicitante")), null, 'errors');
		}

		if (! $error)
		{
			$db->begin();
			$result=$object->create($user);
			if ($result > 0)
			{
				$object->ref = $object->ref.$result;
				$object->update($user);
				if (!empty($object->origin))
				{
					if ($object->origin == 'solalmacen')
					{
						$element = 'almacen';
					}
					// For compatibility
					if ($element == 'almacen')    {
						$subelement = 'solalmacenext';
					}
					require_once DOL_DOCUMENT_ROOT.'/'.$element.'/class/'.$subelement.'.class.php';
					$classname = ucfirst($subelement);
					$objectsrc = new $classname($db);
					$objectsrc->fetch(GETPOST('originid'));
					$objectsrc->fetch_lines();
					if (count($objectsrc->lines)>0)
					{
						//insertamos todos los productos para el proceso de compra
						$lines = $objectsrc->lines;
						//el ref se codificara segun sea cargado
						$a = 1;
						foreach ($lines AS $j => $line)
						{
							if (!$error)
							{
								$product->fetch($line->fk_product);

								$objectdet->fk_purchase_request=$object->id;
								$objectdet->ref=$a;
								$objectdet->fk_parent_line=0;
								$objectdet->fk_product=$line->fk_product;
								$objectdet->label=$product->label;
								$objectdet->description=$line->description;
								$objectdet->qty=$line->qty;
								$objectdet->fk_unit=$line->fk_unit;
								$objectdet->tva_tx=0;
								$objectdet->subprice=$product->subprice;
								$objectdet->price=$product->price;
								$objectdet->total_ht=$product->subprice * $line->qty;
								$objectdet->total_ttc=$product->price * $line->qty;
								$objectdet->product_type=$product->type;
								$objectdet->info_bits=0;
								$objectdet->special_code=0;
								$objectdet->rang=$a;
								$objectdet->origin='solalmacendet';
								$objectdet->originid=$line->id;
								//$objectdet->ref_fourn=GETPOST('ref_fourn','alpha');
								$objectdet->fk_user_create=$user->id;
								$objectdet->fk_user_mod=$user->id;
								$objectdet->status=1;
								$objectdet->datec = dol_now();
								$objectdet->datem = dol_now();
								$objectdet->tms = dol_now();

								$res = $objectdet->create($user);
								if ($res <=0)
								{
									$error++;
									setEventMessages($objectdet->error,$objectdet->errors,'errors');
								}
							}
							$a++;
						}
					}
				}
			}
			else
			{
				$error++;
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='create';
			}
			if (!$error)
			{
				$db->commit();
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/purchase/request/card.php?id='.$result,1);
				header("Location: ".$urltogo);
				exit;
			}

		}
		else
		{
			$action='create';
		}
	}

	if ($action == 'addline' && ! GETPOST('cancel'))
	{
		//creamos registro nuevo
		$filterstatic = " AND t.fk_purchase_request = ".$id;
		$res = $objectdet->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic);
		if ($res>0) $rang = $res + 1;
		else $rang = 1;
		$date_start = dol_mktime(GETPOST('date_starthour'), GETPOST('date_startmin'), 0, GETPOST('date_startmonth'), GETPOST('date_startday'), GETPOST('date_startyear'));
		$date_end = dol_mktime(GETPOST('date_endhour'), GETPOST('date_endmin'), 0, GETPOST('date_endmonth'), GETPOST('date_endday'), GETPOST('date_endyear'));


		//revisamos si es un producto
		if (GETPOST('prod_entry_mode')!='free' && GETPOST('idprodfournprice')>0)
			$resp=$product->fetch(GETPOST('idprodfournprice'));
		if ($resp>0 && GETPOST('idprodfournprice') == $product->id)
		{
			$idprod = $product->id;
			$label = $product->label;
		}
		else
			$idprod = 0;
		$objectdet->fk_purchase_request=$id;
		$objectdet->ref=$rang;
		$objectdet->fk_parent_line=0;
		$objectdet->fk_product=$idprod;
		$objectdet->fk_fabrication = GETPOST('fk_fabrication')+0;
		$objectdet->fk_fabricationdet = GETPOST('fk_fabricationdet')+0;
		$objectdet->fk_projet = GETPOST('fk_projet')+0;
		$objectdet->fk_projet_task = GETPOST('fk_task')+0;
		$objectdet->fk_jobs = GETPOST('fk_jobs')+0;
		$objectdet->fk_jobsdet = GETPOST('fk_jobsdet')+0;
		$objectdet->fk_structure = GETPOST('fk_structure')+0;
		$objectdet->fk_poa = GETPOST('fk_poa')+0;
		$objectdet->partida = GETPOST('partida');
		$objectdet->date_start = $date_start;
		$objectdet->date_end = $date_end;
		$objectdet->label=$label;
		$objectdet->description=GETPOST('dp_desc');
		$objectdet->qty=GETPOST('qty');
		$objectdet->fk_unit=GETPOST('fk_unit');
		$objectdet->tva_tx=0;
		$objectdet->price=GETPOST('price');
		$objectdet->total_ttc=GETPOST('price') * GETPOST('qty');
		//el mismo precio de ttc
		$objectdet->subprice=$objectdet->price;
		$objectdet->total_ht=$objectdet->total_ttc;
		$objectdet->product_type=GETPOST('type')+0;
		$objectdet->info_bits=0;
		$objectdet->special_code=0;
		$objectdet->rang=$rang;
		$objectdet->origin='';
		$objectdet->originid=0;
							//$objectdet->ref_fourn=GETPOST('ref_fourn','alpha');
		$objectdet->fk_user_create=$user->id;
		$objectdet->fk_user_mod=$user->id;
		$objectdet->status=1;
		$objectdet->datec = dol_now();
		$objectdet->datem = dol_now();
		$objectdet->tms = dol_now();
		//validamos
		if ($conf->global->PURCHASE_INTEGRATED_POA)
		{
			if ($objectdet->price <=0)
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Reference price")), null, 'errors');
			}
			if (empty($objectdet->partida))
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Partida")), null, 'errors');
			}
		}
		if (!$error)
		{
			$res = $objectdet->create($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objectdet->error,$objectdet->errors,'errors');
			}
			else
			{
				$urltogo=$backtopage?$backtopage:dol_buildpath('/purchase/request/card.php?id='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
		}
	}

	if ($action == 'updateline' && ! GETPOST('cancel'))
	{
		//actualizamos registro
		$date_start = dol_mktime(GETPOST('date_starthour'), GETPOST('date_startmin'), 0, GETPOST('date_startmonth'), GETPOST('date_startday'), GETPOST('date_startyear'));
		$date_end = dol_mktime(GETPOST('date_endhour'), GETPOST('date_endmin'), 0, GETPOST('date_endmonth'), GETPOST('date_endday'), GETPOST('date_endyear'));

		$res = $objectdet->fetch(GETPOST('lineid'));
		if ($res > 0)
		{

			$objectdet->fk_parent_line=0;
			$objectdet->description=GETPOST('product_desc');
			$objectdet->qty=GETPOST('qty');
			$objectdet->fk_unit=GETPOST('fk_unit');
			$objectdet->date_start = $date_start;
			$objectdet->date_end = $date_end;
			$objectdet->fk_fabrication = GETPOST('fk_fabrication');
			if (empty($objectdet->fk_fabrication)) $objectdet->fk_fabrication = 0;
			$objectdet->fk_fabricationdet = GETPOST('fk_fabricationdet');
			if (empty($objectdet->fk_fabricationdet)) $objectdet->fk_fabricationdet = 0;
			$objectdet->fk_projet = GETPOST('fk_projet');
			if (empty($objectdet->fk_projet)) $objectdet->fk_projet = 0;
			$objectdet->fk_projet_task = GETPOST('fk_task');
			if (empty($objectdet->fk_projet_task)) $objectdet->fk_projet_task = 0;
			$objectdet->fk_jobs = GETPOST('fk_jobs');
			if (empty($objectdet->fk_jobs)) $objectdet->fk_jobs = 0;
			$objectdet->fk_jobsdet = GETPOST('fk_jobsdet');
			if (empty($objectdet->fk_jobsdet)) $objectdet->fk_jobsdet = 0;
			$objectdet->fk_structure = GETPOST('fk_structure');
			if (empty($objectdet->fk_structure)) $objectdet->fk_structure = 0;

			$objectdet->fk_poa = GETPOST('fk_poa')+0;
			$objectdet->partida = GETPOST('partida');
			$objectdet->tva_tx=0;
			$objectdet->subprice=GETPOST('subprice');
			if (empty($objectdet->subprice)) $objectdet->subprice = 0;
			$objectdet->price=GETPOST('price_ttc');
			$objectdet->total_ht=$objectdet->subprice * GETPOST('qty');
			$objectdet->total_ttc=GETPOST('price_ttc') * GETPOST('qty');
			//el mismo precio de ttc
			$objectdet->subprice=$objectdet->price;
			$objectdet->total_ht=$objectdet->total_ttc;
			$objectdet->product_type=GETPOST('type');
			if (empty($objectdet->product_type)) $objectdet->product_type = 0;

			$objectdet->info_bits=0;
			$objectdet->special_code=0;
			$objectdet->origin='';
			$objectdet->originid=0;
							//$objectdet->ref_fourn=GETPOST('ref_fourn','alpha');
			$objectdet->fk_user_mod=$user->id;
			$objectdet->status=1;
			$objectdet->datem = dol_now();
			$objectdet->tms = dol_now();
			//validamos
			if ($conf->global->PURCHASE_INTEGRATED_POA)
			{
				if ($objectdet->price <=0)
				{
					$error++;
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Reference price")), null, 'errors');
				}
				if (empty($objectdet->partida))
				{
					$error++;
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Partida")), null, 'errors');
				}
			}
			if (!$error)
			{
				$res = $objectdet->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objectdet->error,$objectdet->errors,'errors');
					$action = 'editline';
				}
				else
					$action = '';
			}
			else
				$action = '';
		}

	}
	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;
		$object->ref_ext=GETPOST('ref_ext','alpha');
		$object->ref_int=GETPOST('ref_int','alpha');
		$object->fk_projet=GETPOST('fk_projet','int')+0;
		$object->fk_departament=GETPOST('fk_departament','int')+0;
		$object->date_delivery = $date_delivery;
		$object->fk_user_modif=$user->id;
		$object->note_private=GETPOST('note_private','alpha');
		$object->note_public=GETPOST('note_public','alpha');
		$object->fk_shipping_method=GETPOST('fk_shipping_method','int')+0;
		$object->import_key=GETPOST('import_key','alpha');
		$object->extraparams=GETPOST('extraparams','alpha');



		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if ($object->fk_departament <=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Area solicitante")), null, 'errors');
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
		//iniciamos y verificamos
		$db->begin();
		if ($object->fk_poa_prev && $conf->global->PURCHASE_INTEGRATED_POA && $conf->poa->enabled)
		{
			$objPoaprev = new Poaprevext($db);
			$res = $objPoaprev->fetch($object->fk_poa_prev);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objPoaprev->error,$objPoaprev->errors,'errors');
			}
			if (!$error)
			{
				$objPoaprev->statut = -1;
				$objPoaprev->active = 0;
				$objPoaprev->fk_user_mod = $user->id;
				$objPoaprev->datem = dol_now();
				$res = $objPoaprev->update_status($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objPoaprev->error,$objPoaprev->errors,'errors');
				}
			}
			if (!$error)
			{
				$res = $objPoaprev->fetch_lines();
				if ($res > 0)
				{
					$objPoapartidapre = new Poapartidapreext($db);
					$lines = $objPoaprev->lines;
					foreach ($lines AS $J => $line)
					{
						$res = $objPoapartidapre->fetch($line->id);
						if ($res == 1)
						{
							$objPoapartidapre->statut = -1;
							$objPoapartidapre->active = 0;
							$objPoapartidapre->fk_user_mod = $user->id;
							$objPoapartidapre->datem = dol_now();
							$resup = $objPoapartidapre->update_status($user);
							if ($resup <=0)
							{
								$error++;
								setEventMessages($objPoapartidapre->error,$objPoapartidapre->errors,'errors');
							}
						}
						else
						{
							$error++;
							setEventMessages($objPoapartidapre->error,$objPoapartidapre->errors,'errors');
						}
					}
				}
			}
		}
		//eliminamos los dependientes del request
		if (!$error)
		{
			$filter = " AND t.fk_purchase_request = ".$object->id;
			$resdet = $objectdet->fetchAll('','',0,0,array(),'AND',$filter);
			if ($resdet> 0)
			{
				$lines = $objectdet->lines;
				foreach ($lines AS $k => $line)
				{
					$resdet = $objectdet->fetch($line->id);
					if ($resdet>0)
					{
						$resdel = $objectdet->delete($user);
						if ($resdel<=0)
						{
							$error++;
							setEventMessages($objectdet->error,$objectdet->errors,'errors');
						}
					}
				}
			}
		}
		if (!$error)
		{
			$result=$object->delete($user);
			if ($result <=0)
			{
				$error++;
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
			}
		}

		if (!$error)
		{
			$db->commit();
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/purchase/request/list.php',1));
			exit;
		}
		else
		{
			$db->rollback();
		}
		$action = '';
	}

	if ($action == 'updateline' && !GETPOST('cancel'))
	{
		$res = $objectdet->fetch(GETPOST('lineid','int'));
		$objectdet->qty = GETPOST('qty','int');
		$objectdet->description = GETPOST('product_desc','alpha');
		$objectdet->fk_unit = GETPOST('fk_unit','int');
		$objectdet->price = GETPOST('price_ttc','int');
		$objectdet->total_ht = 0;
		$objectdet->total_ttc = $objectdet->price * $objectdet->qty;
		$res = $objectdet->update($user);
		if ($res <=0)
		{
			setEventMessages($objectdet->error,$objectdet->errors,'errors');
		}
		else
			setEventMessages($langs->trans('Updatesuccessfull'),null,'mesgs');
		$action = '';
	}
}



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/
$morejs = array('/purchase/js/purchase.js');
$morecss = array('/purchase/css/style.css','/includes/jquery/plugins/datatables/media/css/dataTables.bootstrap.css','/includes/jquery/plugins/datatables/media/css/jquery.dataTables.css',);
llxHeader('',$title,'','','','',$morejs,$morecss,0,0);

//llxHeader('',$langs->trans('Purchaserequest'),'');

$form=new Formv($db);
$getUtil = new getUtil($db);

if ($conf->monprojet->enabled)
{
	$formproject = new FormProjetsext($db);
}


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

// Part to create
if ($action == 'create')
{

	if ($conf->monprojet->enabled)
	{
		$projectstatic=new Projectext($db);
		$projectsListId = $projectstatic->getMonProjectsAuthorizedForUser($user,($mine?$mine:(empty($user->rights->projet->all->lire)?0:2)),1);
	}
	$nProject = 0;
	if (!empty($projectsListId))
	{
		$aProject = explode(',',$projectsListId);
		$nProject = count($aProject);
	}
	$aDepartament = array();
	$origin = GETPOST('origin');
	if (!empty($origin))
	{
		if ($origin == 'solalmacen')
		{
			$element = 'almacen';
		}
			// For compatibility
		if ($element == 'almacen')    {
			$subelement = 'solalmacen';
		}
		require_once DOL_DOCUMENT_ROOT.'/'.$element.'/class/'.$subelement.'.class.php';
		$classname = ucfirst($subelement);

		$objectsrc = new $classname($db);
		$objectsrc->fetch(GETPOST('originid'));
		$objectsrc->fetch_thirdparty();
		$_GET['fk_departament'] = $objectsrc->fk_departament;
		$_GET['ref_int'] = $objectsrc->ref;
		$_GET['note_public'] = $objectsrc->description;
		$_GET['date_delivery'] = $objectsrc->date_delivery;

	}

	print load_fiche_titre($langs->trans("Newpurchaserequest"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="origin" value="'.GETPOST('origin').'">';
	print '<input type="hidden" name="originid" value="'.GETPOST('originid').'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td width="19%" class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>'.$langs->trans('Draft').'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref_ext").'</td><td><input class="flat" type="text" name="ref_ext" value="'.GETPOST('ref_ext').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref_int").'</td><td><input class="flat" type="text" name="ref_int" value="'.GETPOST('ref_int').'"></td></tr>';

	if ($conf->monprojet->enabled)
	{
		$langs->load("monprojet");
		print '<tr><td>'.$langs->trans("Fieldfk_projet").'</td><td>';
		$filterkey = '';
		$numprojet = $formproject->select_projects(($user->societe_id>0?$user->societe_id:-1), GETPOST('fk_projet'), 'fk_projet', 0,0,1,0,0,0,0,$filterkey);
		//agregar para seleccionar proyecto
		print '</td></tr>';
	}

	//area solicitante
	print '<tr><td class="fieldrequired">'.$langs->trans('Applicant unit').'</td><td colspan="3">';
	if ($user->admin)
		print $form->select_departament(GETPOST('fk_departament'),'fk_departament',' required ',0,1);
	else
	{

		if ($conf->orgman->enabled)
		{
			$filterarea = $filterarea.($lViewareasup?(!empty($filterarea)?','.$fk_departament_sup:$fk_departament_sup):'');
			$filter = " AND t.rowid IN (".$filterarea.")";
			$resdep = $objDepartament->fetchAll('ASC','label',0,0,array('entity'=>$conf->entity,'active'=>1,'status'=>1), 'AND',$filter);
			$options = '';
			if ($resdep>1) $options.='<option value="">'.$langs->trans('Select').'</option>';
			if ($resdep>0)
			{
				foreach($objDepartament->lines AS $j => $line)
				{
					$selected = '';
					if (GETPOST('fk_departament') == $line->id) $selected = ' selected';
					$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
					$aDepartament[$line->id] = $line->label;
				}
			}

			if (!empty($origin))
			{
				//print '<select name="fk_departament" readonly>'.$options.'</select>';
				print $aDepartament[GETPOST('fk_departament')];
				print '<input type="hidden" name="fk_departament" value="'.GETPOST('fk_departament').'">';
			}
			else
				print '<select name="fk_departament" readonly>'.$options.'</select>';
		}
		else
		{
			if (!empty($user->array_options['options_fk_departament']))
			{

				$getUtil->fetch_departament($user->array_options['options_fk_departament'],'');
				print $getUtil->label;
				print '<input type="hidden" name="fk_departament" value="'.$user->array_options['options_fk_departament'].'">';
			}
			else
				print $langs->trans('NotDefined');
		}
	}
	print '</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldarea_request").'</td><td>';
	//print $form->select_departament(GETPOST('fk_departament'),'fk_departament','','',1);
	//print '</td></tr>';

	//print '<tr><td class="fieldrequired">'.$langs->trans("Delivery date proposals").'</td><td>';
	//if (empty($date_delivery)) $date_delivery = dol_now();
	//print $form->select_date($date_delivery,'dd_',1,1,1);
	//print '</td></tr>';

	print '<tr><td>'.$langs->trans("Fieldnote_private").'</td><td>';
	print '<textarea name="note_private" id="note_private" class="quatrevingtpercent" rows="3" wrap="soft">';
	print GETPOST('note_private');
	print '</textarea>';
	print '</td></tr>';
	if ($conf->global->PURCHASE_INTEGRATED_POA)
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_public").'/'.$langs->trans('Preventivename').' ('.$langs->trans('Object').')'.'</td><td>';
	else
		print '<tr><td>'.$langs->trans("Fieldnote_public").'</td><td>';
	print '<textarea name="note_public" id="note_public" class="quatrevingtpercent" rows="3" wrap="soft" '.($conf->global->PURCHASE_INTEGRATED_POA?'required':'').'>';
	print GETPOST('note_public');
	print '</textarea>';

	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("Purchaserequest"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref_ext").'</td><td><input class="flat" type="text" name="ref_ext" value="'.$object->ref_ext.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref_int").'</td><td><input class="flat" type="text" name="ref_int" value="'.$object->ref_int.'"></td></tr>';

	if ($conf->monprojet->enabled)
	{
		$langs->load("monprojet");
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td>';
		$filterkey = '';
		$numprojet = $formproject->select_projects(($user->societe_id>0?$user->societe_id:-1), $object->fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);
		//agregar para seleccionar proyecto
		print '</td></tr>';
	}

	print '<tr><td class="fieldrequired">'.$langs->trans('Applicant unit').'</td><td colspan="3">';
	if ($conf->orgman->enabled)
	{
		$filterarea = $filterarea.($lViewareasup?(!empty($filterarea)?','.$fk_departament_sup:$fk_departament_sup):'');
		$filter = " AND t.rowid IN (".$filterarea.")";
		$resdep = $objDepartament->fetchAll('ASC','label',0,0,array('entity'=>$conf->entity,'active'=>1,'status'=>1), 'AND',$filter);
		$options = '';
		if ($resdep>1) $options.='<option value="">'.$langs->trans('Select').'</option>';
		if ($resdep>0)
		{
			foreach($objDepartament->lines AS $j => $line)
			{
				$selected = '';
				if ((GETPOST('fk_departament')?GETPOST('fk_departament'):$object->fk_departament) == $line->id) $selected = ' selected';
					$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
					$aDepartament[$line->id] = $line->label;
				}
			}

			if (!empty($origin))
			{
				//print '<select name="fk_departament" readonly>'.$options.'</select>';
				print $aDepartament[GETPOST('fk_departament')];
				print '<input type="hidden" name="fk_departament" value="'.GETPOST('fk_departament').'">';
			}
			else
				print '<select name="fk_departament" readonly>'.$options.'</select>';
		}
		else
		{
			if (!empty($user->array_options['options_fk_departament']))
			{

				$getUtil->fetch_departament($user->array_options['options_fk_departament'],'');
				print $getUtil->label;
				print '<input type="hidden" name="fk_departament" value="'.$user->array_options['options_fk_departament'].'">';
			}
			else
				print $langs->trans('NotDefined');
		}
		print '</td></tr>';

	//print '<tr><td class="fieldrequired">'.$langs->trans("Delivery date proposals").'</td><td>';
	//print $form->select_date($object->date_delivery,'dd_',1,1,1);
	//print '</td></tr>';

		print '<tr><td>'.$langs->trans("Fieldnote_private").'</td><td>';
		print '<textarea name="note_private" id="note_private" class="quatrevingtpercent" rows="3" wrap="soft">';
		print $object->note_private;
		print '</textarea>';
		print '</td></tr>';
		if ($conf->global->PURCHASE_INTEGRATED_POA)
			print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_public").'/'.$langs->trans('Nombre del preventivo').'</td><td>';
		else
			print '<tr><td>'.$langs->trans("Fieldnote_public").'</td><td>';
		print '<textarea name="note_public" id="note_public" class="quatrevingtpercent" rows="3" wrap="soft" '.($conf->global->PURCHASE_INTEGRATED_POA?'required':'').'>';
		print $object->note_public;
		print '</textarea>';

		print '</td></tr>';

		print '</table>'."\n";

		dol_fiche_end();

		print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
		print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
		print '</div>';

		print '</form>';
	}



// Part to show record
//if ($id && (empty($action) || $action == 'createval' || $action == 'view' || $action == 'delete' || $action == 'editline' || $action == 'validate' || $action == 'refresh'))
	if ($id>0 && $action != 'create' && $action != 'edit')
	{
	//print load_fiche_titre($langs->trans("Purchaserequest"));
		$head=purchase_request_prepare_head($object);
		dol_fiche_head($head, 'card', $langs->trans("Purchaserequest"),0,'purchaserequest');


	// Confirmation to delete line
		if ($action == 'ask_deleteline')
		{
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$lineid, $langs->trans('DeleteProductLine'), $langs->trans('ConfirmDeleteProductLine'), 'confirm_deleteline', '', 0, 1);
			print $formconfirm;
		}

		if ($action == 'delete') {
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeletePurchaserequest'), $langs->trans('ConfirmDeletePurchaserequest').' '.$object->ref, 'confirm_delete', '', 0, 1);
			print $formconfirm;
		}

		if ($action == 'validate') {
			$aPost[$id] = $_POST;
			$_SESSION['aPurchaseValidate'] = serialize($aPost);
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Validatepurchaserequest'), $langs->trans('ConfirmValidatepurchaserequest'), 'confirm_validate', '', 1,2);
			print $formconfirm;
		}
		if (!$conf->global->PURCHASE_INTEGRATED_POA)
		{
			if ($action == 'approve') {
				$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Approvepurchaserequest'), $langs->trans('ConfirmApprovepurchaserequest'), 'confirm_approve', '', 0, 1);
				print $formconfirm;
			}
		}
		if ($action == 'valplan' && $conf->poa->enabled) {
			$aPost[$id] = $_POST;
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id.'&idr='.GETPOST('idr'), $langs->trans('Validatepreventiveplanification'), $langs->trans('ConfirmValidatepreventiveplanification'), 'confirm_valplan', '', 0, 1);
			print $formconfirm;
		}
		if ($action == 'valpres' && $conf->poa->enabled) {
			$aPost[$id] = $_POST;
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id.'&idr='.GETPOST('idr'), $langs->trans('Validatepreventivebudget'), $langs->trans('ConfirmValidatepreventivebudget'), 'confirm_valpres', '', 0, 1);
			print $formconfirm;
		}

		print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
		print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldref_ext").'</td><td>'.$object->ref_ext.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldref_int").'</td><td>'.$object->ref_int.'</td></tr>';
		if ($monprojet->enabled)
		{
			$langs->load("monprojet");
			print '<tr><td>'.$langs->trans("Fieldfk_projet").'</td><td>';
			$projectstatic->fetch($object->fk_projet);
			$projectstatic->getNomUrl(1);
			print '</td></tr>';
		}
		print '<tr><td>'.$langs->trans("Fieldarea_request").'</td><td>';
		if ($conf->orgman->enabled)
		{
			$objDepartament->fetch($object->fk_departament);
			print $objDepartament->getNomUrl(1);
		}
		else
		{
			if ($object->fk_departament)
			{
				$getUtil->fetch_departament($object->fk_departament);
				print $getUtil->ref;
			}
			else
				print '';
		}
		print '</td></tr>';

	//print '<tr><td>'.$langs->trans("Delivery date proposals").'</td><td>';
	//print dol_print_date($object->date_delivery,'dayhour');
	//print '</td></tr>';

		print '<tr><td>'.$langs->trans("Fieldfk_user_author").'</td><td>';
		$objuser->fetch($object->fk_user_author);
		print $objuser->getNomUrl(1);
		print '</td></tr>';
		if ($conf->global->PURCHASE_INTEGRATED_POA)
		{
			if ($conf->poa->enabled)
			{
				if ($object->fk_poa_prev > 0)
				{
					$objPoaprev = new Poaprevext($db);
					$objPoaprev->fetch($object->fk_poa_prev);
					print '<tr><td>'.$langs->trans("Preventive").'</td><td>';
					print $objPoaprev->getNomUrl();
					print '</td></tr>';

				}
			}
		}

	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_modif").'</td><td>$object->fk_user_modif</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_valid").'</td><td>$object->fk_user_valid</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_cloture").'</td><td>$object->fk_user_cloture</td></tr>';
		if ($user->rights->purchase->req->viewnp)
		{
			print '<tr><td>'.$langs->trans("Fieldnote_private").'</td><td>'.$object->note_private.'</td></tr>';
		}
		print '<tr><td>'.$langs->trans("Fieldnote_public").($conf->global->PURCHASE_INTEGRATED_POA?'/'.$langs->trans('Nombre Preventivo'):'').'</td><td>'.$object->note_public.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_shipping_method").'</td><td>$object->fk_shipping_method</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldimport_key").'</td><td>$object->import_key</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldextraparams").'</td><td>$object->extraparams</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>';

		$status = $object->getLibStatut(6);
		if ($object->status>0 && $object->status_process >0)
		{
			$status.= '&nbsp;-&nbsp;'.$object->getLibStatutpurchase(1);
		}
		print $status;
		print '</td></tr>';

		print '</table>';

		dol_fiche_end();

	/*
	 * Lines REGISTRO DE DETALLE
	 */
	//$result = $object->getLinesArray();
	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			$("#select_type").change(function() {
				document.addproduct.action.value="refresh";
				document.addproduct.submit();
			});
			$("#fk_projet").change(function() {
				document.addproduct.action.value="refresh";
				document.addproduct.submit();
			});
			$("#fk_jobs").change(function() {
				document.addproduct.action.value="refresh";
				document.addproduct.submit();
			});
			$("#idprodfournprice").change(function() {
				document.addproduct.action.value="refresh";
				document.addproduct.submit();
			});
			$("#fk_poa").change(function() {
				document.addproduct.action.value="refresh";
				document.addproduct.submit();
			});
			$("#partida").change(function() {
				document.addproduct.action.value="refresh";
				document.addproduct.submit();
			});
		});';
		print '</script>'."\n";
	}
	if ($action =='refresh')
	{
		if (!empty($lineid)) $action = 'editline';
	}
	print '	<form name="addproduct" id="addproduct" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.(($action != 'editline')?'#add':'#line_'.GETPOST('lineid')).'" method="POST">
	<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">
	<input type="hidden" name="action" value="' . (($action != 'editline') ? 'addline' : 'updateline') . '">
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="id" value="'.$object->id.'">
	<input type="hidden" name="socid" value="'.$societe->id.'">
	';
	print '<input type="hidden" name="lineid" value="'.GETPOST('lineid').'">';
	print '<script type="text/javascript">	jQuery(document).ready(function() { 		jQuery("#idprodfournprice").change(function() { 			if (jQuery("#idprodfournprice").val() > 0) jQuery("#dp_desc").focus(); 		}); }); </script>';
	if (! empty($conf->use_javascript_ajax) && $object->statut == 0) {
		include DOL_DOCUMENT_ROOT . '/core/tpl/ajaxrow.tpl.php';
	}
	dol_fiche_head();
	print '<table id="tablelines" class="noborder noshadow" width="100%">';

	// Add free products/services form
	global $forceall, $senderissupplier, $dateSelector;
	$forceall=1; $senderissupplier=1; $dateSelector=0;


	// Show object lines
	$filterstatic = " AND t.fk_purchase_request = ".$object->id;
	$res = $objectdet->fetchAll('ASC', 'rang', 0,0,array(),'AND',$filterstatic);
	if ($res > 0)
		$object->lines = $objectdet->lines;
	$inputalsopricewithtax=0;
	// Form to add new line
	if ($object->status == 0 && $user->rights->purchase->req->creer)
	{
		if ($action != 'editline')
		{
			$var = true;
			$lAdd = true;
			if ($object->originid>0)
			{
				if($conf->global->PURCHASE_INTEGRATED_POA)
					$lAdd = false;
			}
			// Add free products/services
			if ($lAdd)
				$object->formAddObjectLineadd(1, $societe, $mysoc);

			$parameters = array();
			$reshook = $hookmanager->executeHooks('formAddObjectLine', $parameters, $object, $action);
			// Note that $action and $object may have been modified by hook
		}
	}
	if(! empty($object->lines))
		$ret = $object->printObjectLinesadd($action, $societe, $mysoc, $lineid, 1);

	$num = count($object->lines);

	print '</table>';
	dol_fiche_end();

	print '</form>';


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
	 // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{

		if ($object->status == 0)
		{
			if ($user->rights->purchase->req->mod)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
			}
			if ($user->rights->purchase->req->del)
			{
				print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
			}
			if (!$conf->global->PURCHASE_INTEGRATED_POA)
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a></div>'."\n";

		}


		if ($object->status == 1)
		{
			if (!$conf->global->PURCHASE_INTEGRATED_POA)
			{
				if ($object->fk_poa_prev <=0 && empty($object->status_process))
				{
					if ($user->rights->purchase->req->app)
						print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=approve">'.$langs->trans("Approve").'</a></div>'."\n";
				}
				//createcommande
				if ($object->status_process== 1)
				{
					print '<div class="inline-block divButAction"><a class="butAction" href="'.DOL_URL_ROOT.'/purchase/commande/card.php?action=create&originid='.$object->id.'&origin=purchaserequest">'.$langs->trans("Createcommande")."</a></div>";
				}
			}

			if ($user->rights->purchase->req->del)
			{
				$lDel = true;
					//verificamos el preventivo creado ya tiene inicio de proceso
				if ($conf->poa->enabled)
				{
					if ($conf->global->PURCHASE_INTEGRATED_POA)
					{
						$objPoaprocess = new Poaprocessext($db);
						$filterprocess = " AND t.fk_poa_prev = ".$object->fk_poa_prev;
						$res = $objPoaprocess->fetchAll('','',0,0,array(1=>1),'AND',$filterprocess,true);
						if ($res > 0)
						{
							if ($objPoaprocess->statut == 2) $lDel = false;
						}
						if ($lDel)
							print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans("Delete").'</a></div>'."\n";
					}
				}
			}
		}
	}
	print '</div>'."\n";

	$lVal = true;
	if ($conf->global->PURCHASE_INTEGRATED_POA)
	{
		$lVal = false;
		$aPoa = array();
		if ($conf->poa->enabled)
		{
				//obtenemos las reformulaciones
				//require_once DOL_DOCUMENT_ROOT.'/poa/class/poareformulatedext.class.php';

			$objPoareformulated = new Poareformulatedext($db);
			$objPoa = new Poapoaext($db);

			list($aOfa,$aOfonea,$aOfrefa) = $objPoareformulated ->reformulated($period_year);

			$objPartida = new Cpartida($db);
			$partidaproduct = new Partidaproduct($db);
			$objpoa = new Poapoaext($db);
			$objPoaprev = new Poaprevext($db);

			$objPoastructure = new Poastructureext($db);
			$objPoaobjetive = new Poaobjetiveext($db);

			$aArray = explode(',',$conf->global->POA_SERVICE_TYPE_CODEPARTIDA);
			foreach ((array) $aArray AS $j => $value)
			{
				$aTypeservice[$value] = $value;
			}
			if ($object->fk_poa_prev > 0)
			{
					//recuperamos el que esta registrado
				$res = $objPoaprev->fetch($object->fk_poa_prev);
				$res = $objPoaprev->fetch_lines();
				if ($res > 0)
				{
					$lines = $objPoaprev->lines;
					foreach ($lines AS $j => $line)
					{
						$aStructure[$line->fk_structure] = $line->fk_structure;
						$aPoa[$line->fk_poa] = $line->fk_poa;
						//$aObjetive[$line->fk_poa_objetive] = $line->fk_poa_objetive;
						$fk_structure = $line->fk_structure;
						$fk_poa = $line->fk_poa;
						$aPoa[$line->fk_poa] = $line->fk_poa;
					}
					if (count($aStructure)==1)
					{
						foreach ($aPoa AS $fk_poa)
						{
							$res = $objPoa->fetch($fk_poa);
							if ($res ==1)
							{
								$res = $objPoaobjetive->fetch($objPoa->fk_poa_objetive);
								$level = $objPoaobjetive->level;
								//$aObjetive[$level] = array('label'=>$objPoaobjetive->label,'sigla'=>$objPoaobjetive->sigla);
								$aObjetive[$level][$objPoaobjetive->sigla]['label']=$objPoaobjetive->label;

								$fk_father = $objPoaobjetive->fk_father;
								if ($fk_father>0)
								{
									$lLoop = true;
									while ($lLoop==true)
									{
										$res = $objPoaobjetive->fetch($fk_father);
										if ($res <=0)
										{
											$lLoop = false;
											setEventMessages($objPoaobjetive->error,$objPoaobjetive->errors,'errors');
										}
										$level = $objPoaobjetive->level;
										//$aObjetive[$level] = array('label'=>$objPoaobjetive->label,'sigla'=>$objPoaobjetive->sigla);
										$aObjetive[$level][$objPoaobjetive->sigla]['label']=$objPoaobjetive->label;
										$fk_father = $objPoaobjetive->fk_father;
										if (empty($fk_father)) $lLoop = false;
									}
								}
							}
						}
					}
				}
					//mostramos tanto para planificación y para presupuestos
				if ($user->rights->poa->prev->valplan && $objPoaprev->status_plan == 0)
				{
					print '	<form name="valplan" id="valplan" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="POST">
					<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">
					<input type="hidden" name="action" value="valplan">
					<input type="hidden" name="mode" value="">
					<input type="hidden" name="id" value="'.$object->id.'">
					<input type="hidden" name="idr" value="'.$object->fk_poa_prev.'">
					';
				}
				dol_fiche_head();
				print '<table width="100%">';
				print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans('Planning'),$_SERVER['PHP_SELF'],'','',$params,'align="center" colspan="6"',$sortfield,$sortorder);
				print '</tr>';
				print '<tr>';
				print '<td colspan="6">';
				print $langs->trans('Certifica que la actividad de referencia está acorde a lo programado en el POA').' '.$period_year.' '.$langs->trans(', bajo las siguientes características:');
				print '</td>';
				print '</tr>';
				print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans('Obj.Gestion'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Obj.Especific'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('CodeOperation'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Detail'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Statut'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Action'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
				print '</tr>';
				print '<tr>';
				$aArray = $aObjetive[1];
				$text = '';
				foreach ((array) $aArray AS $siglaObjetive => $data)
				{
					if (!empty($text)) $text.= '<br>';
					$text.= $siglaObjetive;
				}
				print '<td>';
				print $text;
				print '</td>';
				$aArray = $aObjetive[2];
				$text = '';
				foreach ((array) $aArray AS $siglaObjetive => $data)
				{
					if (!empty($text)) $text.= '<br>';
					$text.= $siglaObjetive;
				}
				print '<td>';
				print $text;
				print '</td>';
				$aArray = $aObjetive[3];
				$text = '';
				foreach ((array) $aArray AS $siglaObjetive => $data)
				{
					if (!empty($text)) $text.= '<br>';
					$text.= $siglaObjetive;
				}
				print '<td>';
				print $text;
				print '</td>';
				$text = '';
				foreach ((array) $aArray AS $siglaObjetive => $data)
				{
					if (!empty($text)) $text.= '<br>';
					$text.= $data['label'];
				}

				print '<td>';
				print $text;
				print '</td>';
				print '<td>';
				print $objPoaprev->getLibStatutplan(2);
				print '</td>';
				print '<td align="right">';
				if ($user->rights->poa->prev->valplan && $objPoaprev->status_plan == 0)
					print '<input type="submit" name="submit" value="'.$langs->trans('Approve').'">';
				print '</td>';

				print '</tr>';
				print '</table>';
				dol_fiche_end();
				if ($user->rights->poa->prev->valplan && $objPoaprev->status_plan == 0) print '</form>';

					//presupuestario
				if ($user->rights->poa->prev->valpres && $objPoaprev->status_pres == 0)
				{

					print '	<form name="valpres" id="valpres" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="POST">
					<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">
					<input type="hidden" name="action" value="valpres">
					<input type="hidden" name="mode" value="">
					<input type="hidden" name="id" value="'.$object->id.'">
					<input type="hidden" name="idr" value="'.$object->fk_poa_prev.'">
					';
				}

				dol_fiche_head();
				print '<table width="100%">';
				print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans('Budgeting'),$_SERVER['PHP_SELF'],'','',$params,'align="center" colspan="8"',$sortfield,$sortorder);
				print '</tr>';
				print '<tr>';
				print '<td colspan="8">';
				print $langs->trans('Certifica que las siguientes partidas tienen saldos disponibles:');
				print '</td>';
				print '</tr>';
				print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans('Preventive'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Name'),$_SERVER['PHP_SELF'],'','',$params,'colspan="2"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Pac'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Total'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Statut'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Action'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
				print '</tr>';
				print '<tr>';
				print '<td>'.$objPoaprev->getNomUrl();
					//verificamos si existe el archivo pdf
				$dir = $conf->poa->dir_output .'/'.$period_year. "/preventive/".$objPoaprev->nro_preventive;
				$file = $dir . "/".$objPoaprev->nro_preventive.".pdf";
				if (file_exists($dir))
				{
					$url = DOL_URL_ROOT.'/documents/poa/'.$period_year.'/preventive/'.$objPoaprev->nro_preventive.'/'.$objPoaprev->nro_preventive.'.pdf';
					$file = $objPoaprev->gestion.'/preventive/'.$objPoaprev->nro_preventive.'/'.$objPoaprev->nro_preventive.'.pdf';
					$url = DOL_URL_ROOT.'/document.php?modulepart=poa&file='.$file;
		   			//mostramos el archivo
					if (file_exists($dir))
					{
						print '&nbsp;&nbsp;';
						print '<a href="'.$url.'" target="_blank">'.img_picto($langs->trans('PDF'),'pdf2').'</a>';
					}
				}
				print '</td>';
				print '<td>'.dol_print_date($objPoaprev->date_preventive,'day').'</td>';
				print '<td colspan="2">'.$objPoaprev->label.'</td>';
				print '<td>'.$objPoaprev->fk_pac.'</td>';
				print '<td align="right">'.price($objPoaprev->amount).'</td>';
				print '<td>';
				print $objPoaprev->getLibStatutpres(2);
				print '</td>';
				print '<td align="right">';
				if ($user->rights->poa->prev->valpres && $objPoaprev->status_pres == 0)
					print '<input type="submit" name="submit" value="'.$langs->trans('Approve').'">';
				print '</td>';
				print '</tr>';
				print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans('Catprog'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Partida'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Label'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Approved'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Preventive'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Balance'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
				print '</tr>';
				foreach ($objPoaprev->lines AS $j => $line)
				{
					$objPoastructure->fetch($line->fk_structure);
					$objPartida->fetch(0,$line->partida,$objPoaprev->gestion);
					$objPoa->fetch($line->fk_poa);
					$nPresup = $objPoa->amount;
					$nReformap = $aOfa[$line->fk_structure][$line->fk_poa][$line->partida];
					$nTotalAp = $nPresup+$nReformap;
					//$res = $objPoaprev->get_sum_catprog_partida($period_year, $line->fk_structure,$line->partida,1);
					$res = $objPoaprev->get_sum_str_poa_partida($period_year, $line->fk_structure,$line->fk_poa,$line->partida,1);
					if ($res < 0)
					{
						$error++;
						setEventMessages($objPoaprev->error,$objPoaprev->errors,'errors');
					}
					$nPreventive = $objPoaprev->aSum[$line->fk_structure][$line->partida]+0;

					print '<tr>';
					print '<td>'.$objPoastructure->sigla.'</td>';
					print '<td>'.$line->partida.'</td>';
					print '<td>'.$objPartida->label.'</td>';
					print '<td>'.price($nTotalAp).'</td>';
					print '<td>'.price($nPreventive).'</td>';
					print '<td>'.price(price2num($nTotalAp-$nPreventive,'MT')).'</td>';
					if (price2num($nTotalAp-$nPreventive,'MT')-$line->amount < 0)
						print '<td align="right" class="textcolors">'.price($line->amount).'</td>';
					else
						print '<td align="right">'.price($line->amount).'</td>';
					print '<td>'.'</td>';
					print '</tr>';

				}
				//recuperamos las lineas del preventivo

				print '</table>';
				dol_fiche_end();
				if ($user->rights->poa->prev->valpres && $objPoaprev->status_pres == 0) print '</form>';
			}
			else
			{
				$codePartida = '';
				$aStructure = array();
				$aType = array();
				$aPartidalabel = array();
				$lCateg = true;
				//mostramos un resumen de las partidas afectadas
				if (count($object->lines)>0)
				{
					//agrupamos por partida presupuestaria
					foreach ($object->lines AS $j => $line)
					{
						//$partidaproduct->fetch($line->fk_product);
						$respar = $objPartida->fetch(0,$line->partida,$period_year);
						if ($respar>0)
						{
							$aType[$objPartida->type] = $objPartida->type;
							$aPartidalabel[$line->partida] = $objPartida->label;
						}
						$aPartida[$line->partida]+= $line->total_ttc;
						if (!empty($codePartida)) $codePartida.= ',';
						$codePartida.= "'".$line->partida."'";
						//creamos una variable para insertar en poapartidapredet
						$aPartidadet[$line->partida][$line->fk_product] += $line->total_ttc;
						$aPoapartidadet[$line->fk_poa][$line->partida][$line->fk_product] += $line->total_ttc;
						$aPartidaorig[$line->partida][$line->fk_product][$line->id] = $line->total_ttc;
						$aStructure[$line->fk_structure] = $line->fk_structure;
						$aPoa[$line->fk_poa] = $line->fk_poa;
						$aPoapartida[$line->fk_poa][$line->partida]+= $line->total_ttc;
						$aPoapartidaorig[$line->fk_poa][$line->partida][$line->fk_product][$line->id] = $line->total_ttc;
						$respoa = $objPoa->fetch($line->fk_poa);
						if ($respoa==1)
							$aObjetive[$objPoa->fk_poa_objetive] = $objPoa->fk_poa_objetive;
					}
					if (count($aStructure)==1)
					{
						foreach ($aStructure AS $fk_structure)
						{
							//se recupera el fk_structure
						}
					}
					else
					{
						setEventMessages($langs->trans('No se puede validar la Solicitud de Compra ya que se esta utilizando diferentes categorias programaticas'),null,'warnings');
						$lCateg = false;
					}

					//if (count($aPoa)==1)
					//{
					//	foreach ($aPoa AS $fk_poa)
					//	{
					//		//
					//	}
					//}
					//else
					//{
					//	setEventMessages($langs->trans('No se puede validar la Solicitud de Compra ya que se esta utilizando diferentes poas'),null,'warnings');
					//	$lCateg = false;
					//}
					if (count($aType)>1)
					{
						setEventMessages($langs->trans('No se puede validar la Solicitud de Compra ya que se esta utilizando diferentes tipos de partidas'),null,'warnings');
						$lCateg = false;
					}
					else
					{
						foreach ($aType AS $type)
						{
							//
						}
						//vamos a determinar que tipo de proceso es 0=servicio 1=bienes
						if ($aTypeservice[$type]) $aSeltype = $aTypeprocess[0];
						else $aSeltype= $aTypeprocess[1];
					}
					if ($object->status == 0)
					//	print load_fiche_titre($langs->trans("Resumen"));
					//if ($object->status == 1)
						print load_fiche_titre($langs->trans("Generación Certificación Presupuestaria"));

					//vamos a buscar la categoria programatica que tenga los recursos
					//$filterpoa = " AND t.partida IN (".$codePartida.")";
					//$filterpoa.= " AND t.fk_area = ".$object->fk_departament;
					//$filterpoa.= " AND t.fk_structure = ".$fk_structure;
					//$respoa = $objpoa->fetchAll('ASC', 'sigla', 0, 0,array('entity'=>$conf->entity,'gestion'=>$period_year),'AND',$filterpoa);

					$options = '';
					if ($lCateg)
					{
						$selected = '';
						if (count($aPoa) > 1)
						{
							$options.= '<option value="0">'.$langs->trans('Select').'</option>';
						}
						else
						{
							foreach ($aPoa AS $i)
							{
								$respoa = $objpoa->fetch($i);
								if ($respoa == 1)
								{
									$fk_poa = $objpoa->id;
									$objPoastructure->fetch($objpoa->fk_structure);
									if (GETPOST('fk_poa') == $i) $selected = ' selected="selected"';
									$options.= '<option value="'.$i.'" '.$selected.'>'.$objPoastructure->sigla.' - '.$objpoa->label.'</option>';
								}
							}
						}
					}
					//nueva ocpion
					//mostramos la estructura para seleccionar
					$options = '';
					if ($lCateg)
					{
						$selected = '';
						if (count($aStructure) > 1)
						{
							$options.= '<option value="0">'.$langs->trans('Select').'</option>';
						}
						else
						{
							foreach ($aStructure AS $i)
							{
								$resstr = $objPoastructure->fetch($i);
								if ($resstr == 1)
								{
									$fk_structure = $objPoastructure->id;
									if (GETPOST('fk_structure') == $i) $selected = ' selected="selected"';
									$options.= '<option value="'.$i.'" '.$selected.'>'.$objPoastructure->sigla.'</option>';
								}
							}
						}
						//mostramos la estructura para seleccionar

					}
					//if ($respoa>1)
					//	$fk_poa = GETPOST('fk_poa','int');
					if ($lCateg)
					{
						if (! empty($conf->use_javascript_ajax))
						{
							print "\n".'<script type="text/javascript">';
							print '$(document).ready(function () {
								$("#fk_poaval").change(function() {
									document.addprev.action.value="createval";
									document.addprev.submit();
								});
							});';
							print '</script>'."\n";
						}
						print '	<form name="addprev" id="addprev" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="POST">';
						print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
						print '<input type="hidden" name="action" value="createval">';
						print '<input type="hidden" name="id" value="'.$object->id.'">';
						print '<input type="hidden" name="socid" value="'.$societe->id.'">';
							//asignamos en una variable el o los poas seleccionado
						foreach ($aPoa AS $i)
						{
							print '<input type="hidden" name="aPoa['.$i.']" value="'.$i.'">';
						}
						dol_fiche_head();
						print '<table width="100%">';
						print '<tr class="liste_titre">';
						print '<td>'.$langs->trans('Cat.Prog.').'</td>';
						print '<td colspan="4">'.'<select id="fk_structureval" name="fk_structure">'.$options.'</select></td>';
						print '</tr>';

						//requirementtype
						print '<tr><td class="fieldrequired">'.$langs->trans('Requirementtype').'</td><td colspan="2">';
						//print select_requirementtype($object->code_requirement,'code_requirement','',1,0,'code');
						print $form->selectarray('code_requirement',$aSeltype,GETPOST('code_requirement'),(count($aSeltype)>1?1:0));
						print '</td></tr>';
						dol_include_once('/poa/class/poastructureext.class.php');

						print '<tr class="liste_titre">';
						print '<td>'.$langs->trans('Obj. Gestion').'</td>';
						print '<td>'.$langs->trans('Obj. Especifico').'</td>';
						print '<td>'.$langs->trans('Operation').'</td>';
						print '<td>'.$langs->trans('Cat.Prog.').'</td>';
						print '<td>'.$langs->trans('Nombre.').'</td>';
						print '</tr>';

							//buscamos los objetivos
							//$objpoa->fetch($fk_poa);
						//$objpoaobj = new Poaobjetiveext($db);
						//$objpoaobjtmp = new Poaobjetiveext($db);
						//$objpoastr = new Poastructureext($db);

						/*
						//$resstr = $objPoastructure->fetch($objpoa->fk_structure);
						//$resobj = $objPoaobjetive->fetch($objpoa->fk_poa_objetive);
						//if ($resobj>0)
						//{
							//buscamos todos los level
							$cOperation = $objPoaobjetive->sigla .' : '.$objPoaobjetive->label;
							$fk_father = $objPoaobjetive->fk_father;
							$lLoop = true;
							while ($lLoop)
							{
								if ($fk_father >0)
								{
									$resobj = $objPoaobjetive->fetch($fk_father);
									if ($resobj>0)
									{
										if ($objPoaobjetive->level == 2)
											$cUnidad = '<br>'.$objPoaobjetive->sigla.' : '.$objPoaobjetive->label;
										elseif($objPoaobjetive->level == 1)
											$cGestion = '<br>'.$objPoaobjetive->sigla.' : '.$objPoaobjetive->label;
										$fk_father = $objPoaobjetive->fk_father;
									}
									else
									{
										$lLoop = false;
									}
								}
								else
								{
									$lLoop = false;
								}
							}
						}
						*/
						foreach ((array) $aObjetive AS $i)
						{
							$resobj = $objPoaobjetive->fetch($i);
							if ($resobj>0)
							{
								//buscamos todos los level
								$cOperation = $objPoaobjetive->sigla .' : '.$objPoaobjetive->label;
								$fk_father = $objPoaobjetive->fk_father;
								$lLoop = true;
								while ($lLoop)
								{
									if ($fk_father >0)
									{
										$resobj = $objPoaobjetive->fetch($fk_father);
										if ($resobj>0)
										{
											if ($objPoaobjetive->level == 2)
												$cUnidad = '<br>'.$objPoaobjetive->sigla.' : '.$objPoaobjetive->label;
											elseif($objPoaobjetive->level == 1)
												$cGestion = '<br>'.$objPoaobjetive->sigla.' : '.$objPoaobjetive->label;
											$fk_father = $objPoaobjetive->fk_father;
										}
										else
										{
											$lLoop = false;
										}
									}
									else
									{
										$lLoop = false;
									}
								}
							}
							print '<tr>';
							print '<td>'.$cGestion.'</td>';
							print '<td>'.$cUnidad.'</td>';
							print '<td>'.$cOperation.'</td>';
							print '<td>'.$objPoastructure->sigla.'</td>';
							print '<td>'.$objPoastructure->label.'</td>';
							print '</tr>';

						}


						print '<tr class="liste_titre">';
						print '<td>'.$langs->trans('Partida').'</td>';
						print '<td align="right">'.$langs->trans('Presupuesto').'</td>';
						print '<td align="right">'.$langs->trans('Preventivo').'</td>';
						print '<td align="right">'.$langs->trans('Saldo').'</td>';
						print '<td align="right">'.$langs->trans('Amount').'</td>';
						print '</tr>';
						$lValidate = true;
						if (count($aPoapartida)<=0) $lValidate= false;
						//foreach ($aPartida AS $codepartida => $value)
						foreach ($aPoapartida AS $fk_poa => $aPart)
						{
							foreach ($aPart AS $codepartida => $value)
							{
								if ($fk_poa>0)
								{
									$objpoa->fetch($fk_poa);
									$nPresup = $objpoa->amount;
									$nReformap = $aOfa[$objpoa->fk_structure][$objpoa->id][$codepartida];
									$nTotalAp = $nPresup+$nReformap;

								//if (!empty($codepartida)) $objpartida->fetch(0,$codepartida,$period_year);
									$respoaprev = $objPoaprev->get_sum_str_poa_partida($period_year, $fk_structure,$fk_poa,$codepartida,1);
									if ($respoaprev<=0)
									{
										$error++;
										setEventMessages($objPoaprev->error,$objPoaprev->errors,'errors');
									}
									print '<tr>';
								//print '<td>'.$linepoa->sigla.' '.$linepoa->label.'</td>';
									if (!empty($codepartida))
									{
									//$objpartida->fetch(0,$codepartida,$period_year);
										print '<td>'.$codepartida.' - '.$aPartidalabel[$codepartida].'</td>';
									}
									else
										print '<td>'.$langs->trans('No definido').'</td>';
								//determinaremos el saldo que existe por catprog y partida
									print '<td align="right">'.price($nTotalAp).'</td>';
									print '<td align="right">'.price($objPoaprev->aSum[$line->fk_structure][$objpoa->id][$codepartida]).'</td>';
									print '<td align="right">'.price($nTotalAp-$objPoaprev->aSum[$objpoa->fk_structure][$objpoa->id][$codepartida]).'</td>';
									$balance = $nTotalAp-$objPoaprev->aSum[$objpoa->fk_structure][$objpoa->id][$codepartida]-$value;
									if ($balance <0) $lValidate = false;
									print '<td align="right" '.(!$lValidate?' class="textcolors"':'').'>'.price($value).'</td>';
									print '</tr>';
									//agregamos en input para guardar en poapartidapre
									print '<input type="hidden" name="aPartida['.$codepartida.']" value="'.$value.'">';
									print '<input type="hidden" name="aPoapartida['.$fk_poa.']['.$codepartida.']" value="'.$value.'">';
									//agregamos en input para guardar en poapartidapredet
									foreach ($aPartidadet[$codepartida] AS $fk_product => $valuedet)
									{
										print '<input type="hidden" name="aPartidadet['.$codepartida.']['.$fk_product.']" value="'.$valuedet.'">';
									}
									foreach ($aPoapartidadet[$fk_poa][$codepartida] AS $fk_product => $valuedet)
									{
										print '<input type="hidden" name="aPoapartidadet['.$fk_poa.']['.$codepartida.']['.$fk_product.']" value="'.$valuedet.'">';
									}
									foreach ($aPartidaorig[$codepartida] AS $fk_product => $aLine)
									{
										foreach ($aLine AS $fk_line => $value)
										{
											print '<input type="hidden" name="aPartidaorig['.$codepartida.']['.$fk_product.']['.$fk_line.']" value="'.$value.'">';
										}
									}
									foreach ($aPoapartidaorig[$fk_poa][$codepartida] AS $fk_product => $aLine)
									{
										foreach ($aLine AS $fk_line => $value)
										{
											print '<input type="hidden" name="aPoapartidaorig['.$fk_poa.']['.$codepartida.']['.$fk_product.']['.$fk_line.']" value="'.$value.'">';
										}
									}
								}
								else
								{
								//if (!empty($codepartida)) $objpartida->fetch(0,$codepartida,$period_year);
									print '<tr>';
									print '<td>'.$langs->trans('Sin categoria programatica').'</td>';
									if (!empty($codepartida))
									{
									//$objpartida->fetch(0,$codepartida,$period_year);
										print '<td>'.$codepartida.' - '.$aPartidalabel[$codepartida].'</td>';
									}
									else
										print '<td>'.$langs->trans('No definido').'</td>';
									print '<td align="right">'.price(0).'</td>';
									print '<td align="right">'.price(0).'</td>';
									print '<td align="right">'.price(0).'</td>';
									print '<td align="right">'.price($value).'</td>';

									print '</tr>';

								}
							}
						}

						print '</table>';
						dol_fiche_end();

						if ($user->rights->purchase->req->val)
						{
							print '<div class="center">';
							if ($lValidate)
								print '<input type="submit" class="butAction" name="addvalidate" value="'.$langs->trans("Validate").'">';
							else
								setEventMessages($langs->trans('No existe saldo suficiente para validar.'),null,'warnings');

							print ' &nbsp; <a href="'.DOL_URL_ROOT.'/purchase/request/list.php'.'" name="return" class="butAction">'.$langs->trans("Return").'</a>';
							print '</div>';
						}

						print '</form>';
					}
				}
			}
			// Buttons
			print '<div class="tabsAction">'."\n";

			if ($conf->global->PURCHASE_INTEGRATED_POA)
			{
				if ($object->status == 1 && empty($object->status_process))
				{
					if ($objPoaprev->status_plan && $objPoaprev->status_pres)
					{
						if ($user->rights->poa->proc->write)
						{
						//verificamos si el preventivo ya tiene inicio de proceso
							$filterprocess = " AND t.fk_poa_prev =".$objPoaprev->id;
							$objPoaprocess = new Poaprocessext($db);
							$resproc=$objPoaprocess->fetchAll('','',0,0,array(1=>1),'AND',$filterprocess,true);
							if (empty($resproc))
							{
								$lnk = DOL_URL_ROOT.'/poa/process/process.php?id='.$object->id.'&fk_poa_prev='.$objPoaprev->id.'&action=create';
								print '<div class="inline-block divButAction"><a class="butAction" href="'.$lnk.'">'.$langs->trans("Start hiring process").'</a></div>'."\n";
							}
						}
					}
				}
			}
			print '</div>'."\n";

		}
		else
		{
			setEventMessages($langs->trans('Debe activar el modulo POA'),null,'warnings');
		}

	}



	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

	print '<div class="fichecenter"><div class="fichehalfleft">';

			// Linked object block
			//$type='source'
			//$type='target'
	$res = $getUtil->get_element_element($object->id,'purchaserequest',$type='target');
	if ($res > 0)
	{
		$object->type_element = 'source';
		$object->listObject = $getUtil->lines;
	}
	$somethingshown = $form->showLinkedObjectBlockpurchase($object);
	print '</div></div>';
}


// End of page
llxFooter();
$db->close();
