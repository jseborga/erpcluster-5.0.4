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
//camibamos de sesion entity
$_SESSION['dol_entity'] = 1;

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdetext.class.php';
require_once(DOL_DOCUMENT_ROOT."/assets/core/modules/assets/modules_assets.php");

require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

require_once DOL_DOCUMENT_ROOT.'/assets/lib/assets.lib.php';
require_once DOL_DOCUMENT_ROOT.'/assets/lib/company.lib.php';


//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
if ($conf->product->enabled)
	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
if ($conf->orgman->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
}
if ($conf->purchase->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseur.factureext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefourndetadd.class.php';
}
else
	setEventMessages($langs->trans('Active el modulo purchase'),null,'warnings');

$langs->load("assets");


$action = GETPOST('action','alpha');
$id     = GETPOST("id",'int');
$ref    = GETPOST('ref','alpha');
$fk_facture_fourn = GETPOST('fk_facture_fourn','int');
$lineid = GETPOST('lineid','int');

if (isset($_GET['tab']) || isset($_POST['tab']))
	$_SESSION['tabasset'] = (!empty($_GET['tab'])?$_GET['tab']:$_POST['tab']);
$tab = $_SESSION['tabasset'];

$mesg = '';

$object  = new Assetsext($db);
$objProperty  = new Mproperty($db);
$objLocation  = new Mlocation($db);
$objassign = new Assetsassignmentext($db);
$objassigndet = new Assetsassignmentdetext($db);
$objuser = new User($db);
$objadh  = new Adherent($db);
$projet = new Project($db);
$objSociete = new Societe($db);

if ($id || $ref)
{
	$res = $object->fetch($id,(!empty($ref)?$ref:null));
	if ($res>0) $id = $object->id;
}
if ($action == 'search')
	$action = 'createedit';

/*
 * Actions
 */

if ($action == 'create' && $_REQUEST['noitem'] == $langs->trans('Donotactive'))
{
	//accion para desactivar el item como no activable
	$action = 'createf';
	$subaction = 'confirm';
}

if ($action == 'confirm_noactivate' && $_REQUEST['confirm'] == 'yes')
{
	$aPost = unserialize($_SESSION['notActive']);
	$_POST = $aPost[$fk_facture_fourn];
	$fkfacturefourn = GETPOST('fk_facture_fourn');
	$line_id = GETPOST('lineid');
	if ($fkfacturefourn == $fk_facture_fourn && $line_id == $lineid)
	{
		$objfacturefourndet = new Facturefourndetadd($db);
		$objfacturefourndet->fetch(0,$lineid);
		$objfacturefourndet->fk_asset = -1;
		$res = $objfacturefourndet->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($objfacturefourndet->error,$objfacturefourndet->errors,'errors');
		}
		else
			setEventMessages($langs->trans('Successfullupdate'),null,'mesgs');
	}
	$action = 'createf';
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
	if ($lineid>0)
	{
		$objfacturefourn = new FactureFournisseurext($db);
		$objfacturefourn->fetch($fk_facture_fourn);
		$objfacturefourndet = new Facturefourndetadd($db);
		$objfacturefourndet->fetch(0,$lineid);
	}
	$error = 0;
	$quant = GETPOST('quant','int');
	$db->begin();
	$date_adq = $objfacturefourn->date;
	$aDateadq = dol_getdate($date_adq);
	$date_active = dol_mktime(12, 0, 0, GETPOST('dac_month'),GETPOST('dac_day'),GETPOST('dac_year'));
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
			$object->fk_facture_fourn = $fk_facture_fourn+0;
			$max = $object->fetch_max(GETPOST('type_group'));
			$object->type_group   	= GETPOST('type_group');
			$object->type_patrim  	= GETPOST('type_patrim');
			$object->item_asset   	= $object->maximo;
			$object->quant        	= 1;
			$object->date_adq 		= $date_adq;
			$object->date_day = $aDateadq['mday'];
			$object->date_month = $aDateadq['mon'];
			$object->date_year = $aDateadq['year'];
			$object->date_active 	= $date_active;
			$object->coste        	= price2num($price,'MU');
			$object->coste_residual	= price2num(GETPOST('coste_residual'),'MU');
			$object->fk_type_adj  	= GETPOST('fk_type_adj');
			$object->descrip      	= GETPOST('descrip');
			$object->number_plaque 	= '';
			$object->useful_life 	= GETPOST('useful_life');
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
			$id = $object->create($user);
			$aAssetId['id'][$id] = $id;
			if ($id <=0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
		}
		if (!$error)
		{
			$_SESSION['aAssetId'] = serialize($aAssetId);
		}
	}
	else
	{
		$object->entity   = $conf->entity;
		$code = generarcodigo(7);
		$object->ref   = '(PROV)'.$code;
		$object->fk_father   = GETPOST('fk_father','int')+0;
		$object->fk_facture_fourn = $fk_facture_fourn+0;
		$object->type_group   	= GETPOST('type_group');
		$object->type_patrim  	= GETPOST('type_patrim');
		$object->item_asset   	= GETPOST('item_asset');
		$object->quant        	= GETPOST('quant');
		$object->date_adq 		= $date_adq;
		$object->date_day = $aDateadq['mday'];
		$object->date_month = $aDateadq['mon'];
		$object->date_year = $aDateadq['year'];
		$object->date_active 	= $date_active;
		$object->coste        	= GETPOST('coste');
		$object->coste_residual = GETPOST('coste_residual');
		$object->fk_type_adj  	= GETPOST('fk_type_adj');
		$object->descrip      	= GETPOST('descrip');
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
		if (!$error)
		{
			$id = $object->create($user);
			if ($id<=0)
			{
				$error++;
				setEventMessages($object->error, $object->errors, 'errors');
			}
		}
	}
	if (!$error)
	{
		if ($lineid>0)
		{
			$objfacturefourndet->fk_asset = $id;
			$res = $objfacturefourndet->update($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objfacturefourndet->error,$objfacturefourndet->errors,'errors');
			}
		}
	}

	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Successfullrecord'), null, 'mesgs');
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
		exit;
	}
	else
		$db->rollback();
	$action = 'create';
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

if ($action == 'createf' && $user->rights->assets->ass->crear)
{
	print_fiche_titre($langs->trans("Newassetunderbill"));
	if ($conf->purchase->enabled)
		$objfacturefourn = new FactureFournisseurext($db);

	$lAdd = true;
	if ($fk_facture_fourn >0)
	{
		$res = $objfacturefourn->fetch($fk_facture_fourn);
		if ($res==1)
			$lAdd = false;
	}
	if ($lAdd)
	{
		$code = $conf->global->ASSETS_CODE_TYPE_PURCHASE;
		$filter = " AND (fk_asset IS NULL OR fk_asset = 0)";
		if ($conf->purchase->enabled)
			$objfacturefourn->getlist_facturefourn_typepurchase($code,1,$filter);
		$aArray = $objfacturefourn->aArray;
		$options = '';
		if (count($aArray)>0)
		{
			print "\n".'<script type="text/javascript" language="javascript">';
			print '$(document).ready(function () {
				$("#selecttype_group").change(function() {
					document.fiche_asset.action.value="createf";
					document.fiche_asset.submit();
				});
			});';
			print '</script>'."\n";

			print '<form name="fiche_asset" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="createf">';

			dol_htmloutput_mesg($mesg);

			dol_fiche_head();
			print '<table class="border" style="min-width=1000px" width="100%">';
			foreach ($aArray AS $idFacture)
			{
				$objfacturefourn->fetch($idFacture);
				//vamos a buscar al proveedor

				$ressoc = $objSociete->fetch($objfacturefourn->socid);

				$options.= '<option value="'.$objfacturefourn->id.'">'.$objfacturefourn->ref.($ressoc==1?' - '.$objSociete->nom:'').'</option>';
			}
    	//select facture
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Invoice').'</td><td colspan="2">';
			print '<select name="fk_facture_fourn">'.$options.'</select>';
			print '</td></tr>';
			print '<table>';
			dol_fiche_end();
			print '<center><input type="submit" class="butAction" value="'.$langs->trans("Select").'"></center>';
			print '</form>';
		}
		else
		{
			print '<p>'.$langs->trans('No existe facturas pendientes').'</p>';
		}
	}
	else
	{
		if ($subaction == 'confirm')
		{
			$aPost[$fk_facture_fourn] = $_POST;
			$_SESSION['notActive'] = serialize($aPost);
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?fk_facture_fourn='.$fk_facture_fourn.'&lineid='.$lineid, $langs->trans('Noitemactivation'), $langs->trans('Confirm not activate item'), 'confirm_noactivate', '', 0, 1);
			print $formconfirm;
		}
		//existe la factura seleccionada y mostramos los items de la factura
		//$objfacturefourn->fetch_lines();
		$lines = $objfacturefourn->lines;

		print "\n".'<script type="text/javascript" language="javascript">';
		print '$(document).ready(function () {
			$("#selecttype_group").change(function() {
				document.fiche_asset.action.value="createf";
				document.fiche_asset.submit();
			});

		});';
		print '</script>'."\n";

		print '<form name="fiche_asset" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="create">';
		print '<input type="hidden" name="fk_facture_fourn" value="'.$fk_facture_fourn.'">';

		dol_htmloutput_mesg($mesg);

		print '<h2>'.$objfacturefourn->ref.'</h2>';
		dol_fiche_head();

		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Ref"),"", "","","","");
		print_liste_field_titre($langs->trans("Label"),"liste.php", "t.ref","","","");
		print_liste_field_titre($langs->trans("Description"),"liste.php", "t.ref","","","");
		print_liste_field_titre($langs->trans("Qty"),"liste.php", "t.item_asset","","","");
		print_liste_field_titre($langs->trans("Select"),"liste.php", "t.descrip","","","");
		print '</tr>';
		$product = new Product($db);
		$objfacturefourndet = new Facturefourndetadd($db);
		foreach ($lines AS $j => $line)
		{
			$objfacturefourndet->fetch(0,$line->rowid);
			if (IS_NULL($objfacturefourndet->fk_asset) || $objfacturefourndet->fk_asset == 0)
			{
				print "<tr $bc[$var]>";
				if ($line->fk_product>0)
				{
					$product->fetch($line->fk_product);
					print '<td>'.$product->getNomUrl(1).'</td>';
					print '<td>'.$product->label.'</td>';
				}
				else
				{
					print '<td>'.'</td>';
					print '<td>'.$line->label.'</td>';
				}
				print '<td>'.$line->description.'</td>';
				print '<td>'.$line->qty.'</td>';
				print '<td>'.'<input type="radio" name="lineid" value="'.$line->rowid.'" '.($lineid == $line->rowid?' checked':'').'>'.'</td>';
				print '</tr>';
			}
		}
		print '<table>';
		dol_fiche_end();

		print '<center><input type="submit" name="submit" class="butAction" value="'.$langs->trans("Create").'">&nbsp;<input type="submit" name="noitem" class="butActionDelete" value="'.$langs->trans("Donotactive").'"></center>';
		print '</form>';
	}

}

if ($action == 'create' && $user->rights->assets->ass->crear && $fk_facture_fourn && $lineid)
{
	$objfacturefourn = new FactureFournisseurext($db);
	$objfacturefourn->fetch($fk_facture_fourn);
	$objfacturefourndet = new SupplierInvoiceLine($db);
	$objfacturefourndetadd = new Facturefourndetadd($db);
	$objfacturefourndet->fetch($lineid);

	print_fiche_titre($langs->trans("Newasset"));

	$date_adq = $objfacturefourn->date;
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
	print '<input type="hidden" name="fk_facture_fourn" value="'.$fk_facture_fourn.'">';
	print '<input type="hidden" name="lineid" value="'.$lineid.'">';
	dol_htmloutput_mesg($mesg);

	print '<table class="border" style="min-width=1000px" width="100%">';

    //facture
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Facture').'</td><td colspan="2">';
	print $objfacturefourn->ref;

	print '</td></tr>';

    //group type
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Group').'</td><td colspan="2">';
	print select_type_group(GETPOST('type_group'),'type_group','',1,0,'code');
	print '</td></tr>';

    //ref code
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Code').'</td><td colspan="2">';
	print '(PROV)';
	print '<input type="hidden" name="ref" value="'.(empty($object->ref)?'(PROV)':$object->ref).'" size="30">';
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
	print $objfacturefourndet->label.' '.$objfacturefourndet->description;
	print '<input type="hidden" name="descrip" value="'.$objfacturefourndet->label.' '.$objfacturefourndet->description.'" readonly>';
	print '</td></tr>';

    //quant
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Quantity').'</td><td colspan="2">';
	print '<input type="number" min="1" max="100" name="quant"  value="'.$objfacturefourndet->qty.'" readonly>';
	print '</td></tr>';

    //coste
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('TotalCost').'</td><td colspan="2">';
	print '<input type="number" min="0" step="any" name="coste" value="'.$objfacturefourndet->total_ht.'" readonly>';
	$code_iso = $conf->global->ASSETS_CURRENCY_DEFAULT;
	print ' '.currency_name($code_iso,1).' '.$langs->getCurrencySymbol($code_iso);
	print '</td></tr>';
    //coste residual
	print '<tr><td width="15%">'.$langs->trans('Costeresidual').'</td><td colspan="2">';
	print '<input type="number" min="0" step="any" name="coste_residual" value="'.(GETPOST('coste_residual')?GETPOST('coste_residual'):0).'">';
	print ' '.currency_name($code_iso,1).' '.$langs->getCurrencySymbol($code_iso);
	print '</td></tr>';

    //date adq
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Date acquisition').'</td><td colspan="2">';
	print dol_print_date($date_adq,'day');
	//$form->select_date($date_adq,'da_','','','',"date",1,1);
	print '</td></tr>';

    //date adq
	print '<tr><td width="15%">'.$langs->trans('Date activation').'</td><td colspan="2">';
	$form->select_date($date_active,'dac_','','',1,"date",1,1);
	print '</td></tr>';

    //number plaque
	print '<tr><td width="15%">'.$langs->trans('Number plaque').'</td><td colspan="2">';
	print '<input type="text" name="number_plaque" value="'.GETPOST('number_plaque').'" size="27" maxlenght="30">';
	print ' '.$langs->trans('Si la cantidad es mayor a uno, deje en blanco, más adelante se solicitará esta información');
	print '</td></tr>';

    //useful_life
	print '<tr><td width="15%">'.$langs->trans('Usefullife').'</td><td colspan="2">';
	print '<input type="number" min="0"  name="useful_life" value="'.$object->useful_life.'" >';
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
					$formquestion = array(array('type'=>'textarea','label'=>$langs->trans('Number plaque'),'size'=>40,'name'=>'numberplaque','value'=>'','placeholder'=>$langs->trans('Registre los numeros de plaquetas separado por un caracter único')),
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
			if ($user->rights->assets->ass->crear && ($tab <= 1))
				print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
			else
				print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
			if ($user->rights->assets->ass->mod)
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

		}
		print "</div>";

		if ($tab == 0)
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
		if ($tab == 1)
		{
					//armamos las depreciaciones realizadas
			dol_fiche_head();
			require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovext.class.php';
			$objmov = new Assetsmovext($db);
			$asset = new Assets($db);
			$filterstatic = " AND t.fk_asset = ".$object->id;
			$filterstatic.= " AND t.status = 2";
			$resm = $objmov->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,false);
			if ($resm >0)
			{
				include DOL_DOCUMENT_ROOT.'/assets/assets/tpl/list.tpl.php';
			}
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
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Date acquisition').'</td><td colspan="2">';
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
			print '<tr><td width="15%">'.$langs->trans('Number plaque').'</td><td colspan="2">';
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
			$formquestion = array(array('type'=>'text','label'=>$langs->trans('Number plaque'),'size'=>40,'name'=>'numberplaque','value'=>'','placeholder'=>$langs->trans('Registre los numeros de plaquetas separado por un caracter único')),
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
}
llxFooter();

$db->close();
?>
