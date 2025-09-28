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
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdetext.class.php';

require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsbalanceext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovlogext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetscondition.class.php';

require_once(DOL_DOCUMENT_ROOT."/assets/core/modules/assets/modules_assets.php");

require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsdoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsdoc.class.php';

require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
if ($conf->orgman->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/lib/orgman.lib.php';
}
require_once DOL_DOCUMENT_ROOT.'/assets/lib/assets.lib.php';
require_once DOL_DOCUMENT_ROOT.'/assets/lib/company.lib.php';

//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

//para subida de documentos
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';

$langs->load("assets");

$action = GETPOST('action');
$id     	= GETPOST("id",'int');
$idr     	= GETPOST("idr",'int');
$idmov = GETPOST('idmov','int');
$ref    	= GETPOST('ref');
$dater = dol_mktime(12, 0, 0, GETPOST('dr_month'),GETPOST('dr_day'),GETPOST('dr_year'));

if (isset($_GET['tab']) || isset($_POST['tab']))
	$_SESSION['tabasset'] = (!empty($_GET['tab'])?$_GET['tab']:$_POST['tab']);
$tab = $_SESSION['tabasset'];

$mesg = '';
$object  = new Assetsext($db);
$objAssetbalance = new Assetsbalanceext($db);
$objProperty  = new Mproperty($db);
$objLocation  = new Mlocation($db);
$objassign = new Assetsassignmentext($db);
$objassigndet = new Assetsassignmentdetext($db);
$objUser = new User($db);
$objadh  = new Adherent($db);
$projet = new Project($db);
$objAssetsdoc = new Assetsdoc($db);
$extrafields = new ExtraFields($db);
$objCassetsdoc = new Cassetsdoc($db);

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

	// Remove file in doc form
if ($action == 'confirm_deletefile')
{
	if ($id > 0)
	{
		require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

		$langs->load("other");
		$upload_dir = $conf->assets->dir_output;
		//. '/' . dol_sanitizeFileName($objectdoc->ref);

		$file = $upload_dir . '/' . GETPOST('urlfile');
		$ret = dol_delete_file($file, 0, 0, 0, $product);
		if ($ret)
			setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else
			setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
		$action = '';
	}
}

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
	// Action to add record
if ($action == 'addcond')
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/assets/list.php',1);
		header("Location: ".$urltogo);
		exit;
	}
	$been = GETPOST('been');
	$object->fetch($id);
	$db->begin();
	if (!$error)
	{
		$datelast = GETPOST('datelast');
		if (empty($datelast)) $datelast = 0;
		$dif = $dater - $datelast+0;

		$objCond = new Assetscondition($db);

		$objCond->fk_asset=GETPOST('id','int');
		$objCond->ref=GETPOST('ref','alpha');
		$objCond->fk_user=$user->id;
		$objCond->dater = $dater;
		$objCond->been=GETPOST('been','alpha');
		$objCond->description=GETPOST('description','alpha');
		$objCond->status=GETPOST('status','int');
		if (empty($objCond->status)) $objCond->status = 1;
		if ($been == -2) $objCond->status = 0;
		$objCond->fk_user_create = $user->id;
		$objCond->fk_user_mod = $user->id;
		$objCond->datec = $now;
		$objCond->datem = $now;
		$objCond->tms = $now;

		if ($dater < $datelast)
		{
			$error++;
			setEventMessages($langs->trans("El registro debe tener una fecha igual o superior al ultimo registrado"), null, 'errors');
		}
		if (empty($objCond->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($objCond->been))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldbeen")), null, 'errors');
		}
		if (! $error)
		{
			$result=$objCond->create($user);
			if ($result <= 0)
			{
				$error++;
				if (! empty($objCond->errors)) setEventMessages(null, $objCond->errors, 'errors');
				else  setEventMessages($objCond->error, null, 'errors');
			}
		}
		if (! $error)
		{
			if ($been == -2)
			{
				//se debe procesar el registro de movimiento para cerrar el activo
				$objmovlog 	= new Assetsmovlogext($db);
				$objmov 	= new Assetsmovext($db);
				$new = dol_now();
				//registro de baja por venta
				//buscamos la ultima depreciación
				$res = $objAssetbalance->fetch(0,$id);
				if ($res<=0)
				{
					$error++;
					setEventMessages($objAssetbalance->error,$objAssetbalance->errors,'errors');
				}
				$fk_asset = $id;
				$date_ini = dol_mktime(12, 0, 0, GETPOST('dr_month'),GETPOST('dr_day'),GETPOST('dr_year'));
				$date_end = dol_mktime(12, 0, 0, GETPOST('dr_month'),GETPOST('dr_day'),GETPOST('dr_year'));
				$date_reval = dol_mktime(12, 0, 0, GETPOST('dr_month'),GETPOST('dr_day'),GETPOST('dr_year'));
				$ref = GETPOST('dr_year').(strlen(GETPOST('dr_month'))==1?'0'.GETPOST('dr_month'):GETPOST('dr_month'));

				//actualizamos en asset
				if (!$error)
				{
					$objmovlog->fk_asset = $fk_asset;
					$objmovlog->entity = $conf->entity;
					$objmovlog->ref = $ref;
					$objmovlog->type_group = $object->type_group;
					$objmovlog->date_ini = $date_ini;
					$objmovlog->date_end = $date_end;
					$objmovlog->factor_update = 0;
					$objmovlog->time_consumed = 0;
					$objmovlog->tcini = 0;
					$objmovlog->tcend = 0;
					$objmovlog->month_depr = 0;
					$objmovlog->coste = $objAssetbalance->amount_balance+0;
					$objmovlog->coste_residual = $object->coste_residual+0;
					$objmovlog->amount_base = $objAssetbalance->amount_balance+0;
					$objmovlog->amount_update = $objmovlog->amount_base*-1;
					$objmovlog->amount_depr = 0;
					$objmovlog->amount_depr_acum = $objAssetbalance->amount_balance_depr;
					$objmovlog->amount_depr_acum_update = $objAssetbalance->amount_balance_depr * -1;
					$objmovlog->amount_balance_depr = 0;
					$objmovlog->amount_balance = 0;
					$objmovlog->amount_sale = GETPOST('amount_sale')+0;
					$objmovlog->movement_type = 'SALE';
					$objmovlog->fk_user_create = $user->id;
					$objmovlog->fk_user_mod = $user->id;
					$objmovlog->datec = $new;
					$objmovlog->dateu = $new;
					$objmovlog->tms = $new;
					$objmovlog->status = 1;
					$resml = $objmovlog->create($user);
					if ($resml <=0)
					{
						$error++;
						setEventMessages($objmovlog->error,$objmovlog->errors,'errors');
					}
					if (!$error)
					{
							//misma informacion en la tabla assetsmov de forma unica
							//buscamos el registro
						$filtermov = " AND t.fk_asset = ".$fk_asset;
						$filtermov.= " AND t.ref = '".$ref."'";
						$filtermov.= " AND t.movement_type = 'SALE'";
						$resm = $objmov->fetchAll('','',0,0,array(1=>1),'AND',$filtermov,true);
						if ($resm == 1)
						{
							$idmov = $objmov->id;
								//actualizamos
							$objmov->entity = $conf->entity;
							$objmov->type_group = $object->type_group;
							$objmov->date_ini = $date_ini;
							$objmov->date_end = $date_end;
							$objmov->factor_update = 0;
							$objmov->time_consumed = 0;
							$objmov->tcini = 0;
							$objmov->tcend = 0;
							$objmov->month_depr = 0;
							$objmov->coste = $objAssetbalance->balance+0;
							$objmov->coste_residual = $object->coste_residual;
							$objmov->amount_base = $objAssetbalance->amount_balance+0;
							$objmov->amount_update = $objmov->amount_base*-1;
							$objmov->amount_depr = 0;
							$objmov->amount_depr_acum = $objAssetbalance->amount_balance_depr;
							$objmov->amount_depr_acum_update = $objAssetbalance->amount_balance_depr*-1;
							$objmov->amount_balance_depr = 0;
							$objmov->amount_balance = 0;
							$objmov->amount_sale = GETPOST('amount_sale')+0;
							$objmov->movement_type = 'SALE';
							$objmov->fk_user_mod = $user->id;
							$objmov->dateu = $new;
							$objmov->tms = $new;
							$objmov->status = 0;
							$resm = $objmov->update($user);
							if ($resm <=0)
							{
								$error++;
								setEventMessages($objmov->error,$objmov->errors,'errors');
							}
						}
						elseif($resm == 0)
						{
								//insertamos
							$objmov->fk_asset = $fk_asset;
							$objmov->entity = $conf->entity;
							$objmov->ref = $ref;
							$objmov->type_group = $object->type_group;
							$objmov->date_ini = $date_ini;
							$objmov->date_end = $date_end;
							$objmov->factor_update = 0;
							$objmov->time_consumed = 0;
							$objmov->tcini = 0;
							$objmov->tcend = 0;
							$objmov->month_depr = 0;
							$objmov->coste = $objAssetbalance->amount_balance;
							$objmov->coste_residual = $object->coste_residual;
							$objmov->amount_base = $objAssetbalance->amount_balance+0;
							$objmov->amount_update = $objmov->amount_base*-1;
							$objmov->amount_depr = 0;
							$objmov->amount_depr_acum = $objAssetbalance->amount_balance_depr;
							$objmov->amount_depr_acum_update = $objAssetbalance->amount_balance_depr*-1;
							$objmov->amount_balance_depr = 0;
							$objmov->amount_balance = 0;
							$objmov->amount_sale = GETPOST('amount_sale')+0;
							$objmov->movement_type = 'SALE';
							$objmov->fk_user_create = $user->id;
							$objmov->fk_user_mod = $user->id;
							$objmov->datec = $new;
							$objmov->dateu = $new;
							$objmov->tms = $new;
							$objmov->status = 0;
							$resm = $objmov->create($user);
							if ($resm <=0)
							{
								$error++;
								setEventMessages($objmov->error,$objmov->errors,'errors');
							}
							$idmov = $resm;
						}
						else
						{
							$error++;
							setEventMessages($objmov->error,$objmov->errors,'errors');
						}
					}
				}
			}
		}
	}
	if (!$error)
	{
		$been = GETPOST('been','alpha');
		if ($been != -2) $object->been = GETPOST('been','alpha');
		$object->fk_user_mod = $user->id;
		if ($been == -2)
		{
			$object->amount_sale = GETPOST('amount_sale','int');
			if ($object->amount_sale <=0)
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Amountsale")), null, 'errors');
			}
		}
		$object->fk_asset_mov = $idmov+0;
		$object->datem = dol_now();
		$object->tms = dol_now();
		$res = $object->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
	}

	if (!$error)
	{
		// Creation OK
		$db->commit();
		$urltogo=$_SERVER['PHP_SELF'].'?id='.$id.'&tab=4';
		header("Location: ".$urltogo);
		exit;
	}
	$db->rollback();
	$action = 'createcond';
}

if ($action == 'confirm_annulled' && $_REQUEST['confirm'] == 'yes' && $user->rights->assets->alloc->null)
{
	//buscamos
	$res = $objassigndet->fetch(GETPOST('idr'));
	if ($res>0 && $objassigndet->fk_asset == GETPOST('id'))
	{
		$db->begin();
		$objassigndet->status = -1;
		$res = $objassigndet->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($objassigndet->error,$objassigndet->errors,'errors');
		}
		if(!$error)
		{
			$res = $object->fetch(GETPOST('id'));
			if ($res>0)
			{
				$object->mark = ' ';
				$object->statut = 9;
				$res = $object->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
				if (!$error)
				{
					//guardamos en historial
					add_historial($objassigndet,$user,$db,$object->id,$object->ref,$object->statut,'confirm_annulled');
				}
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Satisfactory update'),$object->errors,'mesgs');
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.GETPOST('id'));
			exit;
		}
		else
		{
			$db->rollback();
			$action = '';
		}
	}
}

// Add
if ($action == 'add' && $user->rights->assets->ass->crear)
{
	$error = 0;
	$quant = GETPOST('quant','int');
	$db->begin();
	$date_adq = dol_mktime(12, 0, 0, GETPOST('da_month'),GETPOST('da_day'),GETPOST('da_year'));
	$date_active = dol_mktime(12, 0, 0, GETPOST('dac_month'),GETPOST('dac_day'),GETPOST('dac_year'));
	$date_day = GETPOST('da_day');
	$date_month = GETPOST('da_month');
	$date_year = GETPOST('da_year');
	$new = dol_now();
	if ($quant > 1)
	{
		$price = GETPOST('coste') / $quant;
			//recorremos y creamos cada uno ya validado
		for ($a=1; $a <= $quant; $a++)
		{
			$object->entity   = $conf->entity;
			$code = generarcodigo(7);
			$object->ref   = '(PROV)'.$code;
			$object->fk_father   = GETPOST('fk_father','int')+0;
			$max = $object->fetch_max(GETPOST('type_group'));
			$object->type_group   	= GETPOST('type_group');
			$object->type_patrim  	= GETPOST('type_patrim');
			$object->item_asset   	= $object->maximo;
			$object->quant        	= 1;
			$object->date_adq 		= $date_adq;
			$object->date_day 		= $date_day;
			$object->date_month		= $date_month;
			$object->date_year 		= $date_year;
			$object->date_active 	= $date_active;
			$object->coste        	= price2num($price,'MU');
			$object->coste_residual	= price2num(GETPOST('coste_residual'),'MU');
			$object->fk_type_adj  	= GETPOST('fk_type_adj');
			$object->descrip      	= GETPOST('descrip');
			$object->number_plaque 	= '';
			$object->useful_life 	= GETPOST('useful_life','int');
			$object->fk_unit 		= GETPOST('fk_unit')+0;
			$object->fk_unit_use	= GETPOST('fk_unit_use')+0;
			$object->coste_unit_use	= GETPOST('coste_unit_use')+0;

			$object->date_create 	= $new;
			$object->date_mod 		= $new;
			$object->fk_user_create = $user->id;
			$object->fk_user_mod 	= $user->id;
			$object->tms 			= $new;
			$object->been 			= 1;
			$object->statut 		= 0;
			if (empty($object->type_group))
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Typegroup")), null, 'errors');
			}
			if (empty($object->descrip))
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Descrip")), null, 'errors');
			}
			//$numref = $object->getNextNumRef($object);
			//if ($numref != -1 && !empty($numref))
			//{
			$id = $object->create($user);
			$aAssetId['id'][$id] = $id;
			if ($id <=0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
			if (!$error)
			{
				//guardamos en historial
				add_historial($object,$user,$db,$object->id,$object->ref,$object->statut,'add');
			}
		//}
		}
		if (!$error)
		{
			$_SESSION['aAssetId'] = serialize($aAssetId);
			$db->commit();
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		else
			$db->rollback();
		$action = 'create';
	}
	else
	{
		$object->entity   = $conf->entity;
		$code = generarcodigo(7);
		$object->ref   = '(PROV)'.$code;
		$object->fk_father   = GETPOST('fk_father','int')+0;

		$object->type_group   	= GETPOST('type_group');
		$object->type_patrim  	= GETPOST('type_patrim');
		$object->item_asset   	= GETPOST('item_asset');
		$object->quant        	= GETPOST('quant');
		$object->date_adq 		= $date_adq;
		$object->date_day 		= $date_day;
		$object->date_month		= $date_month;
		$object->date_year 		= $date_year;
		$object->date_active 	= $date_active;
		$object->coste        	= GETPOST('coste');
		$object->coste_residual = GETPOST('coste_residual');
		$object->fk_type_adj  	= GETPOST('fk_type_adj');
		$object->descrip      	= GETPOST('descrip');
		$object->useful_life 	= GETPOST('useful_life','int');
		$object->number_plaque 	= GETPOST('number_plaque');
		$object->fk_unit 		= GETPOST('fk_unit')+0;
		$object->fk_unit_use	= GETPOST('fk_unit_use')+0;
		$object->coste_unit_use	= GETPOST('coste_unit_use')+0;

		$object->date_create 	= $new;
		$object->date_mod 		= $new;
		$object->fk_user_create = $user->id;
		$object->fk_user_mod 	= $user->id;
		$object->tms 			= $new;

		$object->been = 1;
		$object->statut = 0;
		if (empty($object->type_group))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Typegroup")), null, 'errors');
		}
		if (empty($object->descrip))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Descrip")), null, 'errors');
		}
		if (empty($error))
		{
			$id = $object->create($user);
			if ($id>0)
			{
				//guardamos en historial
				add_historial($object,$user,$db,$object->id,$object->ref,$object->statut,'add');
			}

			if ($id > 0)
			{
				$db->commit();
				setEventMessages($langs->trans('Successfullrecord'), null, 'mesgs');
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
				exit;
			}
			else
				$db->rollback();
		}
		else
			$db->rollback();
		$action = 'create';
		setEventMessages($object->error, $object->errors, 'errors');
	}
}

// Addassign //registro individual de asignacion
if ($action == 'addassign' && $user->rights->assets->alloc->crear)
{
	$date_assig = dol_mktime(12, 0, 0, GETPOST('da_month'),GETPOST('da_day'),GETPOST('da_year'));
	$error = 0;

	$objassign->entity = $conf->entity;
	$objassign->ref = '(PROV)';
	$objassign->detail = $conf->global->ASSETS_ASSIGNMENT_DETAIL_DEFAULT;
	$objassign->date_assignment = $date_assig;
	$objassign->type_assignment = '1'; //tipo de asignacion por un solo item
	$objassign->date_create = dol_now();
	$objassign->fk_user_create = $user->id;
	$objassign->tms = dol_now();
	$objassign->statut = 0;

	$db->begin();
	$idassign = $objassign->create($user);
	if ($idassign > 0)
	{
		//damos de baja la asignacion anterior del activo si existe
		$objassigndet_a = new Assetsassignmentdetext($db);
		$result = $objassigndet_a->fetch_ult($id);
		if ($result > 0)
		{
			$objassigndet_a->statut = 2;
			$objassigndet_a->date_end = dol_now();
			$result = $objassigndet_a->update($user);
		}
		//registramos en assets_assignment_det el activo asignado
		$objassigndet->fk_asset_assignment = $idassign;
		$objassigndet->fk_asset = GETPOST('id');
		$objassigndet->fk_adherent = GETPOST('fk_adherent');
		$objassigndet->fk_property = GETPOST('fk_property');
		$objassigndet->fk_location = GETPOST('fk_location');
		$objassigndet->date_assignment = $date_assig;
		$objassigndet->date_create = dol_now();
		$objassigndet->fk_user_create = $user->id;
		$objassigndet->tms = dol_now();
		$objassigndet->statut = 1;

		$result = $objassigndet->create($user);

		if ($result > 0)
		{
		//cambiamos la numeracion de la asignacion
			$numref = $objassign->getNextNumRef($objassign);
			$objassign->fetch($idassign);
		//cambiando a validado
			$objassign->statut = 1;
			$objassign->ref = $numref;
		//update
			$result = $objassign->update($user);
			if (!$error)
			{
				//guardamos en historial
				add_historial($objassign,$user,$db,$objassigndet->fk_asset,$objassign->ref,$object->statut,'addassign');
			}
			if ($result > 0)
			{
				$db->commit();
				header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
				exit;
			}
			else
			{
				$db->rollback();
				$mesg='<div class="error">'.$objassign->error.'</div>';
				$action='createassign';
			}
		}
		else
		{
			$db->rollback();
			$action = 'createassign';
			$mesg='<div class="error">'.$objassigndet->error.'</div>';
		}
	}
	else
	{
		$db->rollback();
		$action = 'createassign';
		$mesg='<div class="error">'.$objassign->error.'</div>';
	}
}

// Add
if ($action == 'update' && ($user->rights->assets->ass->act || $user->rights->assets->ass->mod))
{
	if ($object->fetch($id)>0)
	{
		$error = 0;
		$object->date_adq = dol_mktime(12, 0, 0, GETPOST('da_month'),GETPOST('da_day'),GETPOST('da_year'));
		$object->date_active = dol_mktime(12, 0, 0, GETPOST('dac_month'),GETPOST('dac_day'),GETPOST('dac_year'));
		$date_day = GETPOST('da_day');
		$date_month = GETPOST('da_month');
		$date_year = GETPOST('da_year');

		$object->date_day 		= $date_day;
		$object->date_month		= $date_month;
		$object->date_year 		= $date_year;

		//$object->ref       		= GETPOST('ref');
		$object->fk_father     	= GETPOST('fk_father','int')+0;
		$object->type_group    	= GETPOST('type_group');
		$object->type_patrim   	= GETPOST('type_patrim');
		$object->item_asset    	= GETPOST('item_asset');
		$object->quant         	= GETPOST('quant');
		//tipo contrato
		$object->coste         	= GETPOST('coste');
		$object->coste_residual	= GETPOST('coste_residual')+0;
		$object->fk_type_adj   	= GETPOST('fk_type_adj');
		$object->descrip       	= GETPOST('descrip');
		$object->number_plaque 	= GETPOST('number_plaque');
		//$object->been 			= GETPOST('been');
		$object->fk_location   	= GETPOST('fk_location')+0;
		$object->useful_life 	= GETPOST('useful_life');
		$object->fk_unit  		= GETPOST('fk_unit')+0;
		$object->fk_unit_use	= GETPOST('fk_unit_use')+0;
		$object->coste_unit_use	= GETPOST('coste_unit_use')+0;
		if (is_null($object->fk_user_create)) $object->fk_user_create = $user->id;
		if (is_null($object->fk_user_mod)) $object->fk_user_mod = $user->id;
		if (is_null($object->date_create)) $object->date_create = dol_now();
		if (is_null($object->date_mod)) $object->date_mod = dol_now();
		$object->tms = dol_now();
		if (empty($object->type_group))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Typegroup")), null, 'errors');
		}
		if (empty($object->descrip))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Descrip")), null,'errors');
		}
		if (empty($error))
		{
			$res = $object->update($user);
			if ($res > 0)
			{
				//actualizamos la ref por el indice
				$object->fetch($id);
				if (substr($object->ref,1,4) == 'PROV')
				{
					$object->ref = '(PROV'.$object->id.')';
					$object->update($user);
				}
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
				exit;
			}
			else
				setEventMessages($object->error,$object->errors,'errors');
			$action = 'edit';
		}
		else
		{
			$action="edit";
		}
	}
}

// confirm validate
if ($action == 'confirm_validate' && $_REQUEST["confirm"] == 'yes' && $user->rights->assets->ass->val)
{
	$aAssetId = unserialize($_SESSION['aAssetId']);
	if (count($aAssetId['id'])>0)
	{
		$aAssetIdcopy = $aAssetId;
		$aAssetIds = $aAssetId;
		$_POST = unserialize($_SESSION['aPost']);
		$aSelid = $_POST['sel'];
		$numberplaque = GETPOST('numberplaque','alpha');
		$separator = GETPOST('separator','alpha');
		if (!empty($numberplaque))
		{
			$aPlaque = explode($separator,$numberplaque);
			if (count($aPlaque) <> count($aAssetId['id']))
			{
				$error++;
				setEventMessages($langs->trans('La cantidad de activos a validar no es igual al numero de plaquetas registradas'),null,'errors');
			}
		}
		$db->begin();
		$a = 1;
		$x = 0;
		$lVerifsel = true;
		if (count($aAssetId['id']) > 500) $lVerifsel = false;
		if (!$error)
		{
			foreach ($aAssetId['id'] AS $id)
			{
				$lReg = false;
				if ($lVerifsel)
				{
					if ($aSelid[$id] == 'on')
					{
						$lReg = true;
					}
					else
					{
						$lReg = false;
					}
				}
				else
				{
					$lReg = true;
				}
				if ($lReg)
				{
					$object->fetch($id);
					//verificamos la numeracion
					$ref = substr($object->ref, 1, 4);
					if ($ref == 'PROV')
					{
						$numref = $object->getNextNumRef($object);
					}
					else
					{
						$numref = $object->ref;
					}
					if ($numref != -1 && !empty($numref))
					{
						//cambiando a validado
						$object->statut = 9;
						if ($aPlaque)
							$object->number_plaque = $aPlaque[$x];
						$object->ref = $numref;
						$a++;
						//update
						$result = $object->update($user);
						if ($result <= 0)
						{
							$error++;
							$mesg.='<div class="error">'.$object->error.'</div>';
							$action='';
						}
						else
							unset($aAssetIds[$id]);
					}
					else
						$error++;
				}
				$x++;
			}
		}
		if (!$error)
		{
			$db->commit();
			unset($_SESSION['aAssetId']);
			unset($aAssetIds);
			setEventMessages($langs->trans('Saverecord').' '.$a,null,'mesgs');
			header("Location: ".DOL_URL_ROOT.'/assets/assets/liste.php');
			exit;
		}
		else
		{
			setEventMessages($langs->trans('Error de registro'),null,'errors');
			$_SESSION['aAssetId'] = serialize($aAssetIdcopy);
			$db->rollback();
			$action = '';
		}
	}
	else
	{
		if ($object->fetch($_REQUEST["id"]))
		{
			//verificamos la numeracion
			$ref = substr($object->ref, 1, 4);
			if ($ref == 'PROV')
			{
				$numref = $object->getNextNumRef($object);
			}
			else
			{
				$numref = $object->ref;
			}
			if ($numref != -1 && !empty($numref))
			{
				//cambiando a validado
				$object->statut = 9;
				$object->ref = $numref;
				//update
				$result = $object->update($user);
				if ($result > 0)
				{
					header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
					exit;
				}
				else
				{
					$mesg='<div class="error">'.$object->error.'</div>';
					$action='';
				}
			}
		}
	}
}

// confirm validate revaluo
if ($action == 'confirm_revalval' && $_REQUEST["confirm"] == 'yes' && $user->rights->assets->reval->val)
{
	$fk_revaluo = GETPOST('fk_revaluo','int');
	$res = $object->fetch($id);
	if ($res > 0)
	{
		$objMov = new Assetsmovext($db);
		$object->status_reval = 2;
		$object->fk_user_mod = $user->id;
		$object->dateu = $now;
		$res = $object->update($user);
		if ($res <= 0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (!$error)
		{
			$res = $objAssetbalance->fetch(0,$object->id);
			if ($res == 1)
			{
				$objAssetbalance->amount_balance = $object->coste_reval;
				$objAssetbalance->amount_balance_depr = 0;
				$objAssetbalance->fk_user_mod = $user->id;
				$objAssetbalance->dateu = $now;
				$objAssetbalance->tms = $now;
				$res = $objAssetbalance->update($user);
				if ($res <= 0)
				{
					$error++;
					setEventMessages($objAssetbalance->error,$objAssetbalance->errors,'errors');
				}
			}
		}
		if (!$error)
		{
			$res = $objMov->fetch($fk_revaluo);
			if ($res == 1)
			{
				$objMov->status = 1;
				$objMov->fk_user_mod = $user->id;
				$objMov->dateu = $now;
				$res = $objMov->update($user);
				if ($res <= 0)
				{
					$error++;
					setEventMessages($objMov->error,$objMov->errors,'errors');
				}
				else
				{
					header('Location: '.$_SERVER['PHP_SELF'].'?id='.$object->id.'&tab=3');
					exit;
				}
			}
			else
			{
				$error++;
				setEventMessages($objMov->error,$objMov->errors,'errors');
			}
		}

	}
	$action = '';
}

// confirm validate sale
if ($action == 'confirm_validatesale' && $_REQUEST["confirm"] == 'yes' && $user->rights->assets->ass->valsale)
{
	$idr = GETPOST('idr','int');
	$idmov = GETPOST('idmov','int');
	$res = $object->fetch($id);
	if ($res > 0)
	{
		$db->begin();
		$objMov = new Assetsmovext($db);
		$object->been = -2;
		$object->fk_user_mod = $user->id;
		$object->dateu = dol_now();
		$object->statut = -1;
		$res = $object->update($user);
		if ($res <= 0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		$objCond = new Assetscondition($db);
		$objCond->fetch($idr);
		$objCond->status = 1;
		$objCond->fk_user_mod = $user->id;
		$objCond->datem = dol_now();
		$objCond->tms = dol_now();
		$res = $objCond->update($user);
		if ($res <= 0)
		{
			$error++;
			setEventMessages($objCond->error,$objCond->errors,'errors');
		}

		if (!$error)
		{
			$res = $objAssetbalance->fetch(0,$object->id);
			if ($res == 1 && $abc)
			{
				//verificar si procede revertir todos los valores del balance
				$objAssetbalance->amount_balance = $object->coste_reval;
				$objAssetbalance->amount_balance_depr = 0;
				$objAssetbalance->fk_user_mod = $user->id;
				$objAssetbalance->dateu = dol_now();
				$objAssetbalance->tms = dol_now();
				$res = $objAssetbalance->update($user);
				if ($res <= 0)
				{
					$error++;
					setEventMessages($objAssetbalance->error,$objAssetbalance->errors,'errors');
				}
			}
		}
		if (!$error)
		{
			$res = $objMov->fetch($object->fk_asset_mov);
			if ($res == 1)
			{
				$objMov->status = 1;
				$objMov->fk_user_mod = $user->id;
				$objMov->dateu = dol_now();
				$res = $objMov->update($user);
				if ($res <= 0)
				{
					$error++;
					setEventMessages($objMov->error,$objMov->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages($objMov->error,$objMov->errors,'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Satisfactory low asset'),null,'mesgs');
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$object->id.'&tab=4');
			exit;
		}
		else
		{
			$db->rollback();
		}
	}
	$action = '';
}
// Delete assets
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->assets->ass->del)
{
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/assets/assets/liste.php');
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}
// Delete assets
if ($action == 'confirm_deletedoc' && $_REQUEST["confirm"] == 'yes' && $user->rights->assets->doc->del)
{
	$resdoc = $objAssetsdoc->fetch($idr);
	$result=$objAssetsdoc->delete($user);
	if ($result > 0)
	{
		header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}


if ( ($action == 'createassignsearch') )
{
	$date_assign = dol_mktime(12, 0, 0, GETPOST('da_month'),GETPOST('da_day'),GETPOST('da_year'));

	$tmparray['fk_adherent'] = GETPOST('fk_adherent');
	$tmparray['fk_property'] = GETPOST('fk_property');
	$tmparray['date_assign'] = $date_assign;

	//buscamos la codificacion del activo
	if ($tmparray['fk_property'])
	{
		$objassigndet->fk_adherent = $tmparray['fk_adherent'];
		$objassigndet->fk_property = $tmparray['fk_property'];
		$objassigndet->date_assignment = $tmparray['date_assign'];
	}
	$action='createassign';
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

if ($action =='addreval' && $user->rights->assets->reval->write)
{
	$objmovlog 	= new Assetsmovlogext($db);
	$objmov 	= new Assetsmovext($db);
	$new = dol_now();
	//registro de revaluo tecnico
	$idr = GETPOST('idreval');
	//buscamos la ultima depreciación
	$objAssetbalance->fetch($idr);

	$fk_asset = $id;
	$date_ini = dol_mktime(12, 0, 0, GETPOST('dr_month'),GETPOST('dr_day'),GETPOST('dr_year'));
	$date_end = dol_mktime(12, 0, 0, GETPOST('dr_month'),GETPOST('dr_day'),GETPOST('dr_year'));
	$date_reval = dol_mktime(12, 0, 0, GETPOST('dr_month'),GETPOST('dr_day'),GETPOST('dr_year'));
	$ref = GETPOST('dr_year').(strlen(GETPOST('dr_month'))==1?'0'.GETPOST('dr_month'):GETPOST('dr_month'));

	$db->begin();
	//actualizamos en asset
	$object->coste_reval = GETPOST('coste_reval','int')+0;
	$object->coste_residual_reval = GETPOST('coste_residual_reval','int')+0;
	$object->date_reval = $date_reval;
	$object->status_reval = 0;
	$resu = $object->update($user);
	if ($resu <=0)
	{
		$error++;
		setEventMessages($object->error,$object->errors,'errors');
	}
	if (!$error)
	{
		$objmovlog->fk_asset = $fk_asset;
		$objmovlog->entity = $conf->entity;
		$objmovlog->ref = $ref;
		$objmovlog->type_group = $object->type_group;
		$objmovlog->date_ini = $date_ini;
		$objmovlog->date_end = $date_end;
		$objmovlog->factor_update = 0;
		$objmovlog->time_consumed = 0;
		$objmovlog->tcini = 0;
		$objmovlog->tcend = 0;
		$objmovlog->month_depr = 0;
		$objmovlog->coste = $objAssetbalance->amount_balance+0;
		$objmovlog->coste_residual = $object->coste_residual+0;
		$objmovlog->amount_base = $objAssetbalance->amount_balance+0;
		$objmovlog->amount_update = ($objmovlog->amount_base - $objAssetbalance->amount_balance_depr - GETPOST('coste_reval','int'))*-1;
		$objmovlog->amount_depr = 0;
		$objmovlog->amount_depr_acum = $objAssetbalance->amount_balance_depr;
		$objmovlog->amount_depr_acum_update = $objAssetbalance->amount_balance_depr * -1;
		$objmovlog->amount_balance_depr = 0;
		$objmovlog->amount_balance = GETPOST('coste_reval','int');
		$objmovlog->movement_type = 'REVAL';
		$objmovlog->fk_user_create = $user->id;
		$objmovlog->fk_user_mod = $user->id;
		$objmovlog->datec = $new;
		$objmovlog->dateu = $new;
		$objmovlog->tms = $new;
		$objmovlog->status = 1;
		$resml = $objmovlog->create($user);
		if ($resml <=0)
		{
			$error++;
			setEventMessages($objmovlog->error,$objmovlog->errors,'errors');
		}
		if (!$error)
		{
							//misma informacion en la tabla assetsmov de forma unica
							//buscamos el registro
			$filtermov = " AND t.fk_asset = ".$fk_asset;
			$filtermov.= " AND t.ref = '".$ref."'";
			$filtermov.= " AND t.movement_type = 'REVAL'";
			$resm = $objmov->fetchAll('','',0,0,array(1=>1),'AND',$filtermov,true);
			if ($resm == 1)
			{
								//actualizamos
				$objmov->entity = $conf->entity;
				$objmov->type_group = $object->type_group;
				$objmov->date_ini = $date_ini;
				$objmov->date_end = $date_end;
				$objmov->factor_update = 0;
				$objmov->time_consumed = 0;
				$objmov->tcini = 0;
				$objmov->tcend = 0;
				$objmov->month_depr = 0;
				$objmov->coste = $objAssetbalance->balance+0;
				$objmov->coste_residual = $object->coste_residual;
				$objmov->amount_base = $objAssetbalance->amount_balance+0;
				$objmov->amount_update = ($objmov->amount_base - $objAssetbalance->amount_balance_depr - GETPOST('coste_reval','int'))*-1;
				$objmov->amount_depr = 0;
				$objmov->amount_depr_acum = $objAssetbalance->amount_balance_depr;
				$objmov->amount_depr_acum_update = $objAssetbalance->amount_balance_depr*-1;
				$objmov->amount_balance_depr = 0;
				$objmov->amount_balance = GETPOST('coste_reval');
				$objmov->movement_type = 'REVAL';
				$objmov->fk_user_mod = $user->id;
				$objmov->dateu = $new;
				$objmov->tms = $new;
				$objmov->status = 0;
				$resm = $objmov->update($user);
				if ($resm <=0)
				{
					$error++;
					setEventMessages($objmov->error,$objmov->errors,'errors');
				}
			}
			elseif($resm == 0)
			{
								//insertamos
				$objmov->fk_asset = $fk_asset;
				$objmov->entity = $conf->entity;
				$objmov->ref = $ref;
				$objmov->type_group = $object->type_group;
				$objmov->date_ini = $date_ini;
				$objmov->date_end = $date_end;
				$objmov->factor_update = 0;
				$objmov->time_consumed = 0;
				$objmov->tcini = 0;
				$objmov->tcend = 0;
				$objmov->month_depr = 0;
				$objmov->coste = $objAssetbalance->amount_balance;
				$objmov->coste_residual = $object->coste_residual;
				$objmov->amount_base = $objAssetbalance->amount_balance+0;
				$objmov->amount_update = ($objmov->amount_base - $objAssetbalance->amount_balance_depr - GETPOST('coste_reval','int'))*-1;
				$objmov->amount_depr = 0;
				$objmov->amount_depr_acum = $objAssetbalance->amount_balance_depr;
				$objmov->amount_depr_acum_update = $objAssetbalance->amount_balance_depr*-1;
				$objmov->amount_balance_depr = 0;
				$objmov->amount_balance = GETPOST('coste_reval');
				$objmov->movement_type = 'REVAL';
				$objmov->fk_user_create = $user->id;
				$objmov->fk_user_mod = $user->id;
				$objmov->datec = $new;
				$objmov->dateu = $new;
				$objmov->tms = $new;
				$objmov->status = 0;
				$resm = $objmov->create($user);
				if ($resm <=0)
				{
					$error++;
					setEventMessages($objmov->error,$objmov->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages($objmov->error,$objmov->errors,'errors');
			}
		}
	}
	if (!$error)
	{
		$db->commit();
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id.'&tab=3');
		exit;
	}
	else
	{
		$db->rollback();
		$action = '';
	}
}
//incorporamos el abm para assetsdoc
include DOL_DOCUMENT_ROOT.'/assets/inc/abm_assetsdoc.inc.php';

//$object = new Assetsdoc($db);
if ($idr > 0)
{
	 if (empty($ref)) $ref = NULL;
	$result = $objAssetsdoc->fetch($idr);

	if (! empty($conf->assets->enabled)) $upload_dir = $conf->assets->multidir_output[$object->entity].'/'.get_exdir(0, 0, 0, 0, $object, 'assets').dol_sanitizeFileName($object->ref);
	$upload_dir.='/'.$idr;
	if (! empty($conf->global->ASSETS_USE_OLD_PATH_FOR_PHOTO))
	    // For backward compatiblity, we scan also old dirs
	{
		if (! empty($conf->assets->enabled)) $upload_dirold = $conf->assets->multidir_output[$object->entity].'/'.substr(substr("000".$object->id, -2),1,1).'/'.substr(substr("000".$object->id, -2),0,1).'/'.$object->id."/photos";
	}
$modulepart='assets';

include DOL_DOCUMENT_ROOT.'/assets/inc/abm_assets_document.inc.php';

}

// print_r($_POST);
// exit;

/*
 * View
 */

$form=new Form($db);
$formfile = new Formfile($db);

$aArrcss= array('assets/css/style.css');
$help_url='EN:Module_Assets_En|FR:Module_Assets|ES:M&oacute;dulo_Assets';
llxHeader("",$langs->trans("Assets"),$help_url,'','','','',$aArrcss);

if ($action == 'create' && $user->rights->assets->ass->crear)
{
	print_fiche_titre($langs->trans("Newasset"));

	$date_adq = dol_mktime(12, 0, 0, GETPOST('da_month'),GETPOST('da_day'),GETPOST('da_year'));
	$date_active = dol_mktime(12, 0, 0, GETPOST('dac_month'),GETPOST('dac_day'),GETPOST('dac_year'));

	$tmparray['type_group'] = GETPOST('type_group');
	$tmparray['type_patrim'] = GETPOST('type_patrim');
	$tmparray['descrip'] = GETPOST('descrip');
	$tmparray['quant'] = GETPOST('quant');
	$tmparray['date_adq'] = $date_adq;
	$tmparray['date_active'] = $date_active;
	$tmparray['number_plaque'] = GETPOST('number_plaque');
	$tmparray['been'] = GETPOST('been');

	//buscamos la codificacion del activo
	if ($tmparray['type_group'] && $object->fetch_max($tmparray['type_group']))
	{
		$objgroup = new Cassetsgroup($db);
		$objgroup->fetch(0,$tmparray['type_group']);
		$object->type_group = $tmparray['type_group'];
		$object->type_patrim = $tmparray['type_patrim'];
		$object->descrip     = $tmparray['descrip'];
		$object->quant       = $tmparray['quant'];
		$object->date_adq    = $tmparray['date_adq'];
		$object->date_active = $tmparray['date_active'];
		$object->number_plaque 	= $tmparray['number_plaque'];
		$object->been 			= $tmparray['been'];
		$object->item_asset  	= $object->maximo;
		$object->useful_life 	= $objgroup->useful_life;
	}


	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
		$("#selecttype_group").change(function() {
			document.fiche_asset.action.value="create";
			document.fiche_asset.submit();
		});

	});';
	print '</script>'."\n";

	print '<form name="fiche_asset" action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" style="min-width=1000px" width="100%">';

	//group type
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Group').'</td><td colspan="2">';
	print select_type_group(GETPOST('type_group'),'type_group','',1,0,'code');
	print '</td></tr>';

	//ref code
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Code').'</td><td colspan="2">';
	print '(PROV)';
	print '<input type="hidden" name="ref" value="'.(empty($object->ref)?'(PROV)':$object->ref).'" size="30">';
	print '</td></tr>';

	//ref ext
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Refext').'</td><td colspan="2">';
	print '<input type="text" name="ref_ext" value="'.(empty($object->ref_ext)?GETPOST('ref_ext'):$object->ref_ext).'" size="30">';
	print '</td></tr>';

	//ref item
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Item').'</td><td colspan="2">';
	print '<input type="text" name="item_asset" value="'.$object->item_asset.'" size="2" readonly>';
	print '</td></tr>';

	//father
	print '<tr><td width="15%">'.$langs->trans('Mainasset').'</td><td colspan="2">';
	print $object->select_assets(GETPOST('fk_father'),'fk_father','',0,1,0,'','','');
	print '</td></tr>';

	//patrim type
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Clasification').'</td><td colspan="2">';
	print select_type_patrim((empty($object->type_patrim)?'N':$object->type_patrim),'type_patrim','',1,0,'code');
	print '</td></tr>';

	//detail
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Detail').'</td><td colspan="2">';
	print '<input type="text" name="descrip" value="'.GETPOST('descrip').'" size="60">';
	print '</td></tr>';

	//quant
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Quantity').'</td><td colspan="2">';
	print '<input type="number" min="1" max="100" name="quant"  value="'.(GETPOST('quant')?GETPOST('quant'):1).'">';
	print '</td></tr>';

	//coste
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('TotalCost').'</td><td colspan="2">';
	print '<input type="number" min="0" step="any" name="coste" value="'.GETPOST('coste').'">';
	$code_iso = $conf->global->ASSETS_CURRENCY_DEFAULT;
	print ' '.currency_name($code_iso,1).' '.$langs->getCurrencySymbol($code_iso);
	print '</td></tr>';
	//coste residual
	print '<tr><td width="15%">'.$langs->trans('Costeresidual').'</td><td colspan="2">';
	print '<input type="number" min="0" step="any" name="coste_residual" value="'.(GETPOST('coste_residual')?GETPOST('coste_residual'):0).'">';
	print ' '.currency_name($code_iso,1).' '.$langs->getCurrencySymbol($code_iso);
	print '</td></tr>';

	//date adq
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Dateacquisition').'</td><td colspan="2">';
	$form->select_date($date_adq,'da_','','','',"date",1,1);
	print '</td></tr>';

	//date adq
	print '<tr><td width="15%">'.$langs->trans('Dateactivation').'</td><td colspan="2">';
	$form->select_date($date_active,'dac_','','',1,"date",1,1);
	print '</td></tr>';

	//number plaque
	print '<tr><td width="15%">'.$langs->trans('Numberplaque').'</td><td colspan="2">';
	print '<input type="text" name="number_plaque" value="'.GETPOST('number_plaque').'" size="27" maxlenght="30">';
	print ' '.$langs->trans('Si la cantidad es mayor a uno, deje en blanco, más adelante se solicitará esta información');
	print '</td></tr>';

	//useful_life
	print '<tr><td width="15%">'.$langs->trans('Usefullife').'</td><td colspan="2">';
	print '<input type="number" min="0"  name="useful_life" value="'.GETPOST('useful_life').'" >';
	print '</td></tr>';

	//fk_unit
	print '<tr><td width="15%">'.$langs->trans('Unitusefullife').'</td><td colspan="2">';
	print $form->selectUnits(GETPOST('fk_unit'),'fk_unit','longs');
	print '</td></tr>';

	//fk_unit_use
	print '<tr><td width="15%">'.$langs->trans('Unitofuse').'</td><td colspan="2">';
	print $form->selectUnits(GETPOST('fk_unit_use'),'fk_unit_use','longs');
	print '</td></tr>';
	//coste_unit_use
	print '<tr><td width="15%">'.$langs->trans('Costperusage').'</td><td colspan="2">';
	print '<input type="number" min="0" step="any" name="coste_unit_use" value="'.GETPOST('coste_unit_use').'" >';
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
	print '</form>';
}
else
{
	$aAssetId = unserialize($_SESSION['aAssetId']);

	if (empty($aAssetId['id']) && ($id || $ref))
	{
		$result = $object->fetch($id,(!empty($ref)?$ref:null));
		if ($result < 0)
		{
			dol_print_error($db);
		}
		require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';
		$objgroup = new Cassetsgroup($db);
		$objgroup->fetch(0,$object->type_group);

		if ( ($action == 'editedit') )
		{
			require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
		 //$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
			$date_adq = dol_mktime(12, 0, 0, GETPOST('da_month'),GETPOST('da_day'),GETPOST('da_year'));

			$tmparray['type_group'] = GETPOST('type_group');
			$tmparray['type_patrim'] = GETPOST('type_patrim');
			$tmparray['descrip'] = GETPOST('descrip');
			$tmparray['quant'] = GETPOST('quant');
			$tmparray['date_adq'] = $date_adq;
			$tmparray['number_plaque'] = GETPOST('number_plaque');
			$tmparray['fk_property'] = GETPOST('fk_property');
			$tmparray['fk_location'] = GETPOST('fk_location');
			$tmparray['been'] = GETPOST('been');


		 //buscamos la codificacion del activo
			if ($tmparray['type_group'] && $object->fetch_max($tmparray['type_group']))
			{
				$object->type_group = $tmparray['type_group'];
				$object->type_patrim = $tmparray['type_patrim'];
				$object->descrip     = $tmparray['descrip'];
				$object->quant       = $tmparray['quant'];
				$object->date_adq    = $tmparray['date_adq'];
				$object->number_plaque = $tmparray['number_plaque'];
				$object->fk_property = $tmparray['fk_property'];
				$object->fk_location = $tmparray['fk_location'];
				$object->been 		 = $tmparray['been'];
				$object->item_asset  = $object->maximo;
			}
			$action=GETPOST('subaction');
		}

		$head=assets_prepare_head($object);
		if ($tab == 0) $tabn = 'assets';
		if ($tab == 1) $tabn = 'depreciation';
		if ($tab == 2) $tabn = 'assignment';
		if ($tab == 3) $tabn = 'revaluo';
		if ($tab == 4) $tabn = 'condition';
		if ($tab == 5) $tabn = 'document';
		dol_fiche_head($head, $tabn, $langs->trans("Assets"),0,($object->public?'projectpub':'project'));

		// Affichage fiche
		if ($action <> 'edit' && $action <> 're-edit')
		{
			// Confirm validate third party
			if ($action == 'annulled')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idr='.GETPOST('idr'),
					$langs->trans("Annulledassignment"),
					$langs->trans("Confirmannulledassignment"),
					"confirm_annulled",
					'',
					0,
					2);
				if ($ret == 'html') print '<br>';
			}

			// Confirm validate third party
			if ($action == 'validate')
			{
				$_SESSION['aPost'] = serialize($_POST);
				//verificamos la cantidad de activos que se insertan

				//$quant = GETPOST('quant');
				$formquestion = '';
				if ($quant > 1)
				{
					$formquestion = array(array('type'=>'textarea','label'=>$langs->trans('Numberplaque'),'size'=>40,'name'=>'numberplaque','value'=>'','placeholder'=>$langs->trans('Registre los numeros de plaquetas separado por un caracter único')),
						array('type'=>'text','label'=>$langs->trans('Separator'),'size'=>5,'name'=>'separator','value'=>'|','placeholder'=>$langs->trans('Separator del texto')));

				}
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
					$langs->trans("Validateasset"),
					$langs->trans("Confirmvalidateasset").': '.$object->ref.' '.$object->descrip,
					"confirm_validate",
					$formquestion,
					0,
					2);
				if ($ret == 'html') print '<br>';
			}

			// Confirm validate revaluo
			if ($action == 'revalval')
			{
				$aPost[$id] = $_POST;
				$_SESSION['aPost'] = serialize($aPost);

				//$quant = GETPOST('quant');
				$formquestion = '';
				$formquestion = array(array('type'=>'textarea','label'=>$langs->trans('Numberplaque'),'size'=>40,'name'=>'numberplaque','value'=>'','placeholder'=>$langs->trans('Registre los numeros de plaquetas separado por un caracter único')),
					array('type'=>'hidden','label'=>$langs->trans('idrevaluo'),'name'=>'fk_revaluo','value'=>$idr));

				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
					$langs->trans("Validateassetreval"),
					$langs->trans("ConfirmValidateassetreval").': '.$object->ref.' '.$object->descrip,
					"confirm_revalval",
					$formquestion,
					0,
					2);
				if ($ret == 'html') print '<br>';
			}

			// Confirm validate revaluo
			if ($action == 'validatesale' && $user->rights->assets->ass->valsale)
			{
				$aPost[$id] = $_POST;
				$_SESSION['aPost'] = serialize($aPost);

				//$quant = GETPOST('quant');
				$formquestion = '';
				$formquestion = array(	array('type'=>'hidden','label'=>$langs->trans('idrevaluo'),'name'=>'idr','value'=>$idr),array('type'=>'hidden','label'=>$langs->trans('idcondition'),'name'=>'idmov','value'=>$idmov));

				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
					$langs->trans("Validateassetsale"),
					$langs->trans("ConfirmValidateassetsale").': '.$object->ref.' '.$object->descrip,
					"confirm_validatesale",
					$formquestion,
					0,
					2);
				if ($ret == 'html') print '<br>';
			}

			// Confirm delete third party
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteprocess"),$langs->trans("Confirmdeleteprocess",$object->ref.' '.$object->detail),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}
			// Confirm cancel proces
			if ($action == 'anulate')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Cancelprocess"),$langs->trans("Confirmcancelprocess",$object->ref.' '.$object->detail),"confirm_cancel",'',0,2);
				if ($ret == 'html') print '<br>';
			}
			if (empty($tab)) include_once DOL_DOCUMENT_ROOT."/assets/assets/tpl/tab0.tpl.php";
			if ($tab == 1) include_once DOL_DOCUMENT_ROOT."/assets/assets/tpl/tab1.tpl.php";
			if ($tab == 2) include_once DOL_DOCUMENT_ROOT."/assets/assets/tpl/tab2.tpl.php";
			if ($tab == 3) include_once DOL_DOCUMENT_ROOT."/assets/assets/tpl/tab3.tpl.php";
			if ($tab == 4) include_once DOL_DOCUMENT_ROOT."/assets/assets/tpl/tab4.tpl.php";
			if ($tab == 5) include_once DOL_DOCUMENT_ROOT."/assets/assets/tpl/tab5.tpl.php";

		}
		dol_fiche_end();

		/* ************************************** */
		/*                                        */
		/* Barre d'action                         */
		/*                                        */
		/* ************************************** */

		print "<div class=\"tabsAction\">\n";

		if ($action == '')
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
					print '<a class="butAction" href="fiche.php?action=createassign&id='.$id.'">'.$langs->trans("Createassignment").'</a>';
			}
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createassignment")."</a>";

		}
		print "</div>";

		if ($tab == 1)
		{
					//armamos las depreciaciones realizadas
			dol_fiche_head();
			require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovext.class.php';
			$objmov = new Assetsmovext($db);
			$asset = new Assets($db);
			$filterstatic = " AND t.fk_asset = ".$object->id;
			$filterstatic.= " AND t.status = 2";
			//$filterstatic.= " AND t.movement_type = 'DEPR'";
			$resm = $objmov->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,false);
			if ($resm >0)
			{
				include DOL_DOCUMENT_ROOT.'/assets/assets/tpl/list.tpl.php';
			}
			dol_fiche_end();
			//calculadoraon line
			if ($objgroup->depreciate || $objgroup->toupdate)
				include DOL_DOCUMENT_ROOT.'/assets/assets/tpl/depreciation.tpl.php';
		}

		if ($tab == 3)
		{
					//armamos los revaluos realizados
			dol_fiche_head();
			require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovext.class.php';
			$objmov = new Assetsmovext($db);
			$asset = new Assets($db);
			$filterstatic = " AND t.fk_asset = ".$object->id;
			//$filterstatic.= " AND t.status = 2";
			$filterstatic.= " AND t.movement_type = 'REVAL'";
			$resm = $objmov->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,false);
			include DOL_DOCUMENT_ROOT.'/assets/assets/tpl/revaluation_list.tpl.php';
			dol_fiche_end();
		}

		if ($tab == 4)
		{
			dol_fiche_head();
			require_once DOL_DOCUMENT_ROOT.'/assets/assets/tpl/condition_list.tpl.php';
			dol_fiche_end();
		}
		if ($tab == 5)
		{
			dol_fiche_head();
			if (empty($action) && empty($idr))
				require_once DOL_DOCUMENT_ROOT.'/assets/assets/tpl/assetsdoc_list.tpl.php';
			else
				include DOL_DOCUMENT_ROOT.'/assets/assets/tpl/assetsdoc_card.tpl.php';
			dol_fiche_end();
		}

		// Edition fiche
		if (($action == 'edit' || $action == 're-edit') && 1)
		{

			print_fiche_titre($langs->trans("Editasset"));

			print "\n".'<script type="text/javascript" language="javascript">';
			print '$(document).ready(function () {
				$("#selecttype_group").change(function() {
					document.fiche_asset.action.value="editedit";
					document.fiche_asset.submit();
				});
				$("#selectfk_property").change(function() {
					document.fiche_asset.action.value="editedit";
					document.fiche_asset.submit();
				});

			});';
			print '</script>'."\n";

			print '<form name="fiche_asset" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="id" value="'.$id.'">';
			print '<input type="hidden" name="subaction" value="'.$action.'">';


			dol_htmloutput_mesg($mesg);

			print '<table class="border" style="min-width=1000px" width="100%">';

		 //group type
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Group').'</td><td colspan="2">';
			print select_type_group($object->type_group,'type_group','',1,0,'code');
			print '</td></tr>';

		 //ref code
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Code').'</td><td colspan="2">';
			print '<input type="text" name="ref" value="'.(empty($object->ref)?'(PROV)':$object->ref).'" size="30">';
			print '</td></tr>';
			 //ref ext
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Refext').'</td><td colspan="2">';
			print '<input type="text" name="ref_ext" value="'.(GETPOST('ref_ext')?GETPOST('ref_ext'):$object->ref_ext).'" size="30" maxlenght="30">';
			print '</td></tr>';

		 //ref item
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Item').'</td><td colspan="2">';
			if ($action == 'edit')
				print '<input type="text" name="item_asset" value="'.$object->item_asset.'" size="2">';
			else
			{
				print $object->item_asset;
				print '<input type="hidden" name="item_asset" value="'.$object->item_asset.'">';
			}
			print '</td></tr>';

		 //father
			print '<tr><td width="15%">'.$langs->trans('Mainasset').'</td><td colspan="2">';
			print $object->select_assets($object->fk_father,'fk_father','',0,1,0,'','','');
			print '</td></tr>';

		 //patrim type
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Clasification').'</td><td colspan="2">';
			if ($action == 'edit')
				print select_type_patrim((empty($object->type_patrim)?'N':$object->type_patrim),'type_patrim','',1,0,'code');
			else
				print select_type_patrim((empty($object->type_patrim)?'N':$object->type_patrim),'type_patrim','',0,1,'code');
			print '</td></tr>';

		 //detail
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Detail').'</td><td colspan="2">';
			print '<input type="text" name="descrip" value="'.$object->descrip.'" size="60">';
			print '</td></tr>';

			//quant
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Quantity').'</td><td colspan="2">';
			print '<input type="number" name="quant" value="'.(empty($object->quant) || $object->quant<=0?1:$object->quant).'">';
			print '</td></tr>';

			//coste
			$code_iso = $conf->global->ASSETS_CURRENCY_DEFAULT;
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('TotalCost').'</td><td colspan="2">';
			print '<input type="number" min="0" step="any" name="coste" value="'.$object->coste.'">';
			print ' '.currency_name($code_iso,1).' '.$langs->getCurrencySymbol($code_iso);
			print '</td></tr>';

			//coste residual
			print '<tr><td width="15%">'.$langs->trans('Costeresidual').'</td><td colspan="2">';
			print '<input type="number" min="0" step="any" name="coste_residual" value="'.$object->coste_residual.'">';
			print ' '.currency_name($code_iso,1).' '.$langs->getCurrencySymbol($code_iso);
			print '</td></tr>';

			//date adq
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Dateacquisition').'</td><td colspan="2">';
			$form->select_date($object->date_adq,'da_','','','',"date",1,1);
			print '</td></tr>';

			//date adq
			print '<tr><td width="15%">'.$langs->trans('Date activation').'</td><td colspan="2">';
			$form->select_date($object->date_active,'dac_','','',1,"date",1,1);
			print '</td></tr>';

			if ($action == 're-edit')
			{
				print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Property').'</td><td colspan="2">';
				$fk_property = GETPOST('fk_property','int')?GETPOST('fk_property','int'):$object->fk_property;
				$filter = " AND t.entity = ".$conf->entity;
				$res = $objProperty->fetchAll('ASC','label',0,0,array('status'=>1),'AND',$filter);
				$options = '<option value="-1">'.$langs->trans('Selectproperty').'</option>';
				$lines =$objProperty->lines;
				foreach ((array) $lines AS $j => $line)
				{
					$selected = '';
					if ($fk_property == $line->id) $selected = ' selected';
					$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.' ('.$line->ref.')'.'</option>';
				}
				print '<select id="fk_property" name="fk_property">'.$options.'</select>';

				//print $objpro->select__property($object->fk_property,'fk_property','',40,1);
				print '</td></tr>';

				print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Location').'</td><td colspan="2">';
				$fk_location = GETPOST('fk_location','int')?GETPOST('fk_location','int'):$object->fk_location;
				$filter = " AND t.fk_property = ".$fk_property;
				$res = $objLocation->fetchAll('ASC','detail',0,0,array('status'=>1),'AND',$filter);
				$options = '';
				$lines =$objLocation->lines;
				foreach ((array) $lines AS $j => $line)
				{
					$selected = '';
					if ($fk_location == $line->id) $selected = ' selected';
					$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->detail.'</option>';
				}
				print '<select id="fk_location" name="fk_location">'.$options.'</select>';

				//print $objloc->select__location($objassigndet->fk_location,'fk_location','',40,1,$object->fk_property);
				print '</td></tr>';
			}

		 //number plaque
			print '<tr><td width="15%">'.$langs->trans('Numberplaque').'</td><td colspan="2">';
			print '<input type="text" name="number_plaque" value="'.$object->number_plaque.'" size="27" maxlenght="30">';
			print '</td></tr>';

			//useful_life
			print '<tr><td width="15%">'.$langs->trans('Usefullife').'</td><td colspan="2">';
			print '<input type="number" min="0" step="any" name="useful_life" value="'.$object->useful_life.'" >';
			print '</td></tr>';

			//fk_unit
			print '<tr><td width="15%">'.$langs->trans('Unitusefullife').'</td><td colspan="2">';
			print $form->selectUnits($object->fk_unit,'fk_unit','longs');
			print '</td></tr>';

			//fk_unit_use
			print '<tr><td width="15%">'.$langs->trans('Unitofuse').'</td><td colspan="2">';
			print $form->selectUnits((GETPOST('fk_unit_use')?GETPOST('fk_unit_use'):$object->fk_unit_use),'fk_unit_use','longs');
			print '</td></tr>';
			//coste_unit_use
			print '<tr><td width="15%">'.$langs->trans('Costperusage').'</td><td colspan="2">';
			print '<input type="number" min="0" step="any" name="coste_unit_use" value="'.(GETPOST('coste_unit_use')?GETPOST('coste_unit_use'):$object->coste_unit_use).'" >';
			print '</td></tr>';


			print '</table>';

			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">';
			print '&nbsp;<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">'.'</center>';
			print '</form>';
		}
	}
	if (count($aAssetId['id'])>0)
	{
		//if (count($aAssetId['id'])>500) $action = 'validategroup';
		if ($action == 'validategroup')
		{
			if ($_REQUEST['Validate']) $confirmval = 'confirm_validate';
			if ($_REQUEST['Delete']) $confirmval = 'confirm_delete';
			if (count($aAssetId['id']) > 500)
				$selid = count($aAssetId['id']);
			else
				$selid = $_POST['sel'];
			$_SESSION['aPost'] = serialize($_POST);
			$formquestion = '';
			$formquestion = array(array('type'=>'text','label'=>$langs->trans('Numberplaque'),'size'=>40,'name'=>'numberplaque','value'=>'','placeholder'=>$langs->trans('Registre los numeros de plaquetas separado por un caracter único')),
				array('type'=>'text','label'=>$langs->trans('Separator'),'size'=>5,'name'=>'separator','value'=>'|','placeholder'=>$langs->trans('Separator del texto')),
				array('type'=>'other','label'=>$langs->trans('To consider'),'name'=>'separator','value'=>$langs->trans('Se almacenara en el mismo orden de aparición')));

			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
				$langs->trans("Validateasset"),
				$langs->trans("Confirmvalidateasset").': '.$selid.' '.$langs->trans('Items'),
				$confirmval,
				$formquestion,
				0,
				2,220);
			if ($ret == 'html') print '<br>';
		}
		print '<form name="fo1" method="POST" id="fo1" action="'.$_SERVER["PHP_SELF"].'">'."\n";
		print '<input type="hidden" name="idot" value="'.$idot.'">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '<input type="hidden" name="action" value="validategroup">';

		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Reg"),"liste.php", "","","","");
		print_liste_field_titre($langs->trans("Code"),"liste.php", "t.ref","","","");
		print_liste_field_titre($langs->trans("Item"),"liste.php", "t.item_asset","","","");
		print_liste_field_titre($langs->trans("Detail"),"liste.php", "t.descrip","","","");
		print_liste_field_titre($langs->trans("Date adq"),"liste.php", "t.date_adq","","","");
		if (count($aAssetId['id'])<=500)
			print_liste_field_titre($langs->trans("Statut"),"", "","","","");
		print "</tr>\n";
		$a = 1;
		foreach((array) $aAssetId['id'] AS $id)
		{
			$object->fetch($id);
			print '<tr>';
			print '<td>'.$a.'</td>';
			print '<td>'.$object->ref.'</td>';
			print '<td>'.$object->item_asset.'</td>';
			print '<td>'.$object->descrip.'</td>';
			print '<td>'.dol_print_date($object->date_adq,'day').'</td>';
			if (count($aAssetId['id'])<=500)
				print '<td>'.'<input type="checkbox" name="sel['.$object->id.']" checked="checked">'.'</td>';
			print '</tr>';
			$a++;
		}
		print '</table>';
		print '<center><br><input type="submit" class="button" name="Validate" value="'.$langs->trans("Validate").'">';
		print '&nbsp;<input type="submit" class="butActionDelete" name="Delete" value="'.$langs->trans("Delete").'">';
		print '&nbsp;<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">'.'</center>';
		print '</form>';
	}
		if ($tab == 0 && empty($action))
		{

			print '<div class="tabsAction">';
			//documents
			print '<table width="100%"><tr><td width="50%" valign="top">';
			print '<a name="builddoc"></a>';
			// ancre
			//$objecten->fetch($fk_entrepot);
			$filename=dol_sanitizeFileName($object->ref);
			//cambiando de nombre al reporte
			$filedir=$conf->assets->dir_output . '/' . dol_sanitizeFileName($object->ref);
			$urlsource=$_SERVER['PHP_SELF'].'?id='.$id;
			$genallowed=$user->rights->assets->ass->crear;
			$delallowed=$user->rights->assets->ass->del;
			$object->modelpdf = 'fractalassets';
			print '<br>';
			print $formfile->showdocuments('assets',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
			$somethingshown=$formfile->numoffiles;
			print '</td></tr></table>';
			print "</div>";
		}

}
llxFooter();

$db->close();
?>
