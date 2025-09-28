<?php
/* Copyright (C) 2005		Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2006-2014	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2010-2012	Regis Houssin			<regis.houssin@capnetworks.com>
 * Copyright (C) 2011		Juanjo Menent			<jmenent@2byte.es>
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
 *	\file       htdocs/projet/tasks/time.php
 *	\ingroup    project
 *	\brief      Page to add new time spent on a task
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/unit/class/units.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/doc.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/utils.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formaddmon.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskresource.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/productprojetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskresourcealmacendetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
//require DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/contratext.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
if ($conf->categorie->enabled)
	dol_include_once('/categories/class/categorie.class.php');
else
	exit;
if ($conf->budget->enabled)
{
	//require_once DOL_DOCUMENT_ROOT.'/budget/class/html.formadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
	dol_include_once('/budget/class/budget.class.php');
	dol_include_once('/budget/class/pustructureext.class.php');
	dol_include_once('/budget/class/pustructuredetext.class.php');
	dol_include_once('/budget/class/productbudgetext.class.php');
	dol_include_once('/budget/class/putypestructureext.class.php');
}
	//dol_include_once('/budget/class/html.formv.class.php');

if ($conf->request->enabled)
	require_once DOL_DOCUMENT_ROOT.'/request/class/requestitem.class.php';
if ($conf->product->enabled)
	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
if ($conf->almacen->enabled)
	require_once DOL_DOCUMENT_ROOT.'/almacen/local/class/entrepotrelationext.class.php';
if ($conf->assets->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignment.class.php';
	require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
}
//images
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

$langs->load('admin');
$langs->load('orders');
$langs->load('sendings');
$langs->load('companies');
$langs->load('bills');
$langs->load('propal');
$langs->load('supplier_proposal');
$langs->load('deliveries');
$langs->load('products');
$langs->load('stocks');
$langs->load('projects');
$langs->load('monprojet@monprojet');

$id=GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$lineid=GETPOST('lineid','int');
$action=GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');
$withproject=GETPOST('withproject','int');
$project_ref=GETPOST('project_ref','alpha');
$riid=GETPOST('riid','int'); //request_item_id
$fk_contrat=GETPOST('fk_contrat','int');

// Security check
$socid=0;
$lDisabled = false;
if ($user->societe_id > 0) $socid = $user->societe_id;
if (!$user->rights->projet->lire) accessforbidden();

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
//echo '<hr>enviando';
$hookmanager->initHooks(array('projecttaskcard','globalcard','monprojet','doActions'));

$object 	 	= new Task($db);
$objadd 	 	= new Taskext($db);
$objecttaskadd 	= new Projettaskadd($db);
$objectptr 	 	= new Projettaskresource($db);
$objectptralmd 	= new Projettaskresourcealmacendetext($db);
$mobject 	 	= new Taskext($db);
$projectstatic 	= new Projectext($db);
$projectadd  	= new Projetadd($db);
$objdoc 	 	= new Projettasktimedoc($db);
$unit 		 	= new Units($db);
$extrafields 	= new ExtraFields($db);
if ($conf->budget->enabled)
	$items 		= new Items($db);
$objectpp 	 	= new Productprojetext($db);
$objectpptmp 	= new Productprojetext($db);
$adherent    	= new Adherent($db);
$societe 	 	= new Societe($db);
$contrat 		= new Contratext($db);

if ($conf->budget->enabled) $budget = new Budget($db);
if ($conf->product->enabled) $product = new Product($db);
if ($conf->categorie->enabled) $categorie = new Categorie($db);
if ($conf->assets->enabled)
{
	$assignment = new Assetsassignment($db);
	$assets = new Assetsext($db);
}
// fetch optionals attributes and labels
$extralabels_project=$extrafields->fetch_name_optionals_label($projectstatic->table_element);

$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

//recuperamos array type_resource
$aTypeResource = load_type_resource();
//verificamos la estructura si tiene budget

	//verificamos si tiene budget
list($fk_budget,$aCat) = get_categorie($id);
$aStrbudget = unserialize($_SESSION['aStrbudget']);
$aStrgroupcat = $aStrbudget[$fk_budget]['aStrgroupcat'];

if (!empty(GETPOST('type_resource')))
{
	$aStrbudget = unserialize($_SESSION['aStrbudget']);
	$aStrgroupcat = $aStrbudget[$fk_budget]['aStrgroupcat'];
	foreach ($aTypeResource AS $j => $data)
	{
		if (GETPOST('type_resource') == $data['code'])
		{
			$nom_object = $data['object'];
			$group_resource = $data['group'];
			$fk_object = GETPOST($data['fk_object']);
			$code_structure = $aStrgroupcat[$data['group']];
			$nom_objectdet = $data['objectdet'];
			$fk_objectdet = GETPOST($data['fk_objectdet']);
		}
	}
}



if ($id > 0 || $ref != NULL)
{
	$res = $object->fetch($id,(!empty($ref)?$ref:NULL));
	if ($res > 0)
	{
		$projectstatic->fetch($object->fk_project);
		$projectadd->fetch(0,$object->fk_project);
		$res=$projectstatic->fetch_optionals($projectstatic->id,$extralabels_project);
	}
}
$lResource = true;
$use_resource = $projectstatic->array_options['options_use_resource'];
if ($use_resource == 1 && !$conf->almacen->enabled)
{
	$lResource = false;
	setEventMessages($langs->trans('Error, El proyecto esta configurado para hacer uso del módulo de almacenes, favor solicitar la activación del mismo'),null,'errors');

}
//$projectstatic->id;

/*
 * Actions
 */

if ($action =='addresource')
{
	//print_r($_POST);
	//exit;
	/* object_prop_getpost_prop */
	$fk_product = GETPOST('product');
	$fk_product_projet = GETPOST('fk_product_projet','int');
	$fk_unit = GETPOST('fk_unit','int');
	$detail = GETPOST('search_product');
	$quant = GETPOST('quant');
	$date_resource=dol_mktime(12,0,0,$_POST["drmonth"],$_POST["drday"],$_POST["dryear"]);

	$ref_product = '';
	$db->begin();
	if ($fk_product_projet<=0)
	{
		//falta la asignacion de detail, ref_product de la maquinaria contratada a externos
		//primero verificamos de acuerdo a la busqueda search_product

		if (GETPOST('type_resource') == 'MOD')
		{
			require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontract.class.php';
			require_once DOL_DOCUMENT_ROOT.'/orgman/class/pchargeext.class.php';
			$pcontract 	= new Pcontract($db);
			$pcharge 	= new Pchargeext($db);
			$pcontract->fetch($fk_objectdet);
			$amount = 0;
			if ($pcontract->id == $fk_objectdet)
			{
				$ref_product = $pcontract->ref;
				$res = $pcharge->fetch($pcontract->fk_charge);
				$detail = $pcharge->label;
				//tomamos el valor total
				/////////////////////////////////////////////REVISAR
				//$pu = $pcontract->basic_fixed / 30 / $pcontract->hours;
				$pu = $pcontract->unit_cost+0;
				$amount = price2num($pu * $quant,'MT');
				if ($amount<=0)
				{
					$error++;
					setEventMessages($langs->trans('Theunitcostperhourisnotdefinedcheck'),null,'errors');
				}
			}
			else
			{
				setEventMessages($langs->trans('There is no record'),null,'errors');
				$error++;
			}
		}
		elseif (GETPOST('type_resource') == 'MAQext' || GETPOST('type_resource') == 'MODext' || GETPOST('type_resource') == 'MAText')
		{
			require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
			$commandefourndet 	= new CommandeFournisseurLigne($db);
			$commandefourn 		= new CommandeFournisseur($db);
			$res = $commandefourndet->fetch($fk_objectdet);
			$amount = 0;
			if ($commandefourndet->rowid == $fk_objectdet)
			{
				$commandefourn->fetch($commandefourndet->fk_commande);
				if ($commandefourndet->fk_product)
					$product->fetch($commandefourndet->fk_product);
				$fk_product  = $product->id+0;
				$ref_product = ($commandefourndet->ref?$commandefourndet->ref:$product->ref);
				$detail = ($commandefourndet->label?$commandefourndet->label:$product->label);
				$fk_object = $commandefourn->socid;
				//tomamos el valor total
				$pu = $commandefourndet->total_ht / $commandefourndet->qty;
				//echo '<hr>. '.$pu.' = '.$commandefourn->total_ht.' / '.$commandefourn->qty;
				$amount = price2num($pu * $quant,'MT');
			}
			else
			{
				setEventMessages($langs->trans('There is no record'),null,'errors');
				$error++;
			}
		}
		elseif (GETPOST('type_resource') == 'MAQ')
		{
			//require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdet.class.php';
			//require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignment.class.php';

			//require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';

			//$assignmentdet 	= new Assetsassignmentdet($db);
			//$assignment 	= new Assetsassignment($db);

			$fk_asset_det = GETPOST('fk_asset_det');
			$search_fk_asset_det = GETPOST('search_fk_asset_det');
			$assets 		= new Assetsext($db);
			if ($fk_asset_det>0)
				echo '<hr>reafk '.$resa = $assets->fetch($fk_objectdet);
			else
			{
				if (!empty($search_fk_asset_det))
				{
					echo '<hr>reasearc '.$resa = $assets->fetch(0,$search_fk_asset_det);

				}
			}
			if (($assets->id == $fk_asset_det && $fk_asset_det>0) || (!empty($search_fk_asset_det) && $assets->ref == $search_fk_asset_det))
			{
				$fk_object = $assets->id;
				$pu = $assets->calc_cost_unit();
				$amount = 0;
				if ($pu >0) $amount = price2num($pu * $quant,'MT');
				$ref_product = $assets->ref;
				$detail = $assets->descrip;
			}
			else
			{
				setEventMessages($langs->trans('Thereisnorecord'),null,'errors');
				$error++;
			}
		}
		else
			$detail = STRTOUPPER($detail);


		//verificamos si existe en producto projet
		$filter = array(1=>1);
		$filterstatic = '';
		if ($detail)
		{
			$filterstatic = ' AND (';
			$filterstatic.= " UPPER(ref) = '".$projectstatic->id.'|'.STRTOUPPER($detail)."'";
			$filterstatic.= " OR UPPER(ref) = '".STRTOUPPER($detail)."'";
		}
		if (!empty($filterstatic))
		{
			$filterstatic.= " OR ";
		}
		else
			$filterstatic.= " AND (";
		$filterstatic.= " UPPER(label) = '".STRTOUPPER($detail)."'";
		$filterstatic.= ")";
		$filterstatic.= " AND t.fk_projet =".$projectstatic->id;

		$res = $objectpp->fetchAll('','',0,0,$filter,'OR',$filterstatic,true);
		if ($res>0)
		{
			$fk_product 		= $objectpp->fk_product;
			$fk_product_projet 	= $objectpp->id;
			$fk_unit 			= $objectpp->fk_unit;
			$detail 			= $objectpp->label;
			$code_structure 	= $objectpp->code_structure;
			if ($fk_product>0 && $product->fetch($fk_product)>0)
			{
				$pu = $product->pmp;
				if (GETPOST('type_resource') == 'MAT')
					$amount = price2num($pu * GETPOST('quant'),'MT');
			}
		}
		else
		{
			if ($fk_product>0 && $product->fetch($fk_product)>0)
			{
				//buscamos la categoria a la que pertenece
				$detail = $product->label;
				$ref_product = $product->ref;
				$pu = $product->pmp;
				if (GETPOST('type_resource') == 'MAT')
					$amount = price2num($pu * GETPOST('quant'),'MT');
				$fk_unit = ($product->fk_unit?$product->fk_unit:$fk_unit);
				$aCate = $categorie->containing($fk_product, 'product', 'id');

				foreach ((array) $aCate AS $i => $fk)
				{
					if ($aStruct['aStrcatcode'][$fk])
					{
						$code_structure = $aStruct['aStrcatcode'][$fk];
						//cambiamos para que guarde la categoria del producto
						$code_structure = $fk;
					}
				}
				if (empty($code_structure) || $code_structure <0)
				{
					setEventMessages($langs->trans('Error, el producto no tiene categoría'),null,'errors');
					$error++;
				}
			}
			else
			{
				$fk_product = 0;
			}
		}
		if (empty($res))
		{
			if (empty($code_structure) || $code_structure < 0)
			{
				setEventMessages($langs->trans('Error, el producto no tiene categoría'),null,'errors');
				$error++;
			}
			if (!$error)
			{
				$lUpdate = false;
				if (empty($ref_product)) $lUpdate = true;
				$objectpp->fk_product = $fk_product+0;
				$objectpp->fk_projet = $projectstatic->id;
				$objectpp->ref = $projectstatic->id.'|'.$ref_product;
				$objectpp->label = $detail;
				$objectpp->fk_unit = $fk_unit;
				$objectpp->type_resource 	= GETPOST('type_resource','alpha');
				$objectpp->group_resource = $group_resource;
				$objectpp->code_structure = $code_structure;
				$objectpp->quant = price2num(GETPOST('quant'),'MT')+0;
				$objectpp->percent_prod = (GETPOST('percent_prod')?GETPOST('percent_prod'):100);
				$objectpp->amount_noprod = price2num(GETPOST('amount_noprod'),'MT')+0;
				$objectpp->amount = $price+0;
				$objectpp->fk_user_create = $user->id;
				$objectpp->fk_user_mod = $user->id;
				$objectpp->date_create = dol_now();
				$objectpp->date_mod = dol_now();
				$objectpp->tms = dol_now();
				$objectpp->status = 1;
				$fk_product_projet = $objectpp->create($user);
				if ($fk_product_projet<=0)
				{
					setEventMessages($objectpp->error,$objectpp->errors,'errors');
					$error++;
				}
					//actualizamos si lUpdate == true
				if ($lUpdate == true)
				{
					$objectpptmp->fetch($fk_product_projet);
					$objectpptmp->ref .= '(PROV)'.$objectpptmp->id;
					$res = $objectpptmp->update($user);
					if ($res <=0)
					{
						setEventMessages($objectpptmp->error,$objectpptmp->errors,'errors');
						$error++;
					}
				}
			}
		}
	}
	else
	{
		$objectpp->fetch($fk_product_projet);
		$fk_product 		= $objectpp->fk_product;
		$fk_product_projet 	= $objectpp->id;
		$fk_unit 			= $objectpp->fk_unit;
		$detail 			= $objectpp->label;
		$code_structure 	= $objectpp->code_structure;
	}

	//revisamos el modelo de registro de productos
	if (GETPOST('type_resource') == 'MAT' )
	{
		if ($use_resource == 1)
			include DOL_DOCUMENT_ROOT.'/monprojet/include/balance_almacendet_resource.inc.php';
	}
	if (!$error)
	{
		$objectptr->fk_projet_task 	= GETPOST('id');
		$objectptr->ref 			= '(PROV)';
		$objectptr->ref_ext 		= GETPOST('ref_ext','alpha');
		$objectptr->date_resource	= $date_resource;
		$objectptr->group_resource 	= $group_resource;
		$objectptr->type_resource 	= GETPOST('type_resource','alpha');
		$objectptr->fk_projet 		= $projectstatic->id;
		$objectptr->fk_object 		= $fk_object;
		$objectptr->object 			= $nom_object;
		$objectptr->fk_objectdet 	= $fk_objectdet;
		if (empty($objectptr->fk_objectdet)) $objectptr->fk_objectdet = 0;
		$objectptr->objectdet 		= $nom_objectdet;
		$objectptr->fk_user_create 	= $user->id;
		$objectptr->fk_user_mod 	= $user->id;
		$objectptr->fk_stock_mouvement =0;
		$objectptr->code_structure 	= $code_structure;
		$objectptr->fk_product 		= $fk_product+0;
		$objectptr->fk_product_projet = $fk_product_projet+0;
		$objectptr->fk_product_budget = GETPOST('fk_product_budget')+0;
		$objectptr->detail 			= $detail;
		$objectptr->fk_unit 		= $fk_unit;
		$objectptr->quant 			= $quant+0;
		$objectptr->percent_prod 	= 100;
		$objectptr->amount_noprod 	= 0;
		$objectptr->amount 			= $amount+0;
		$objectptr->rang 			= 0;
		$objectptr->date_create 	= dol_now();
		$objectptr->date_mod 		= dol_now();
		$objectptr->tms 			= dol_now();
		$objectptr->status 			= 1;
	echo '<hr>fin '.	$res = $objectptr->create($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($objectptr->error,$objectptr->errors,'errors');
			$action = '';
		}
		else
		{
			$objectptr->ref .= $objectptr->id;
			$res = $objectptr->update($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objectptr->error,$objectptr->errors,'errors');
			}
		}
		//si el proyecto tiene uso de recursos en 2 y es material
		if (GETPOST('type_resource') == 'MAT' )
		{
			if ($use_resource == 2)
				include DOL_DOCUMENT_ROOT.'/monprojet/include/mouvement_stock_resource.inc.php';
		}
		if ($fk_stock_mouvement)
		{
			//actualizo
			$objectptr->fk_stock_mouvement .= $fk_stock_mouvement;
			$res = $objectptr->update($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objectptr->error,$objectptr->errors,'errors');
			}
		}
		if (!$error)
		{
			//registramos el uso de recirso
			foreach ((array) $aDischargalmdet AS $fk => $row)
			{
				$objectptralmd->fk_projet_task = $id;
				$objectptralmd->fk_projet_task_resource = $objectptr->id;
				$objectptralmd->fk_sol_almacen_det = $fk;
				$objectptralmd->fk_product = $objectptr->fk_product;
				$objectptralmd->quant = $row['quant'];
				$objectptralmd->subprice = $row['subprice'];
				$objectptralmd->fk_user_create = $user->id;
				$objectptralmd->fk_user_mod = $user->id;
				$objectptralmd->date_create = dol_now();
				$objectptralmd->date_mod = dol_now();
				$objectptralmd->tms = dol_now();
				$objectptralmd->status = 1;
				$res = $objectptralmd->create($user);
				if ($res<=0)
				{
					$error++;
					setEventMessages($objectptralmd->error,$objectptralmd->errors,'errors');
				}
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			header ('Location: '.$_SERVER['PHP_SELF'].'?id='.$id.'&withproject=1');
			exit;
		}
		else
			$db->rollback();
	}
	else
		$db->rollback();
	$action = '';
}

//updateresource
if ($action =='updateresource')
{
	//recuperamos el registro
	$objectptr->fetch(GETPOST('idreg'));
	if ($objectptr->id == GETPOST('idreg') && $objectptr->fk_projet_task == GETPOST('id'))
	{
		/* object_prop_getpost_prop */
		$fk_product = GETPOST('product');
		$fk_product_projet = GETPOST('fk_product_projet','int');
		$fk_unit = GETPOST('fk_unit','int');
		$detail = GETPOST('search_product');
		$ref_product = '';
		$db->begin();
		if ($fk_product_projet<=0)
		{
			//falta la asignacion de detail, ref_product de la maquinaria contratada a externos
			//primero verificamos de acuerdo a la busqueda search_product
			if (GETPOST('type_resource') == 'MOD')
			{
				require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontract.class.php';
				require_once DOL_DOCUMENT_ROOT.'/salary/class/pcharge.class.php';
				$pcontract 	= new Pcontract($db);
				$pcharge 	= new Pcharge($db);
				$pcontract->fetch($fk_objectdet);
				if ($pcontract->id == $fk_objectdet)
				{
					$ref_product = $pcontract->ref;
					$pcharge->fetch($pcontract->fk_charge);
					$detail = $pcharge->label;
				}
				else
				{
					setEventMessages($langs->trans('There is no record'),null,'errors');
					$error++;
				}
			}
			elseif (GETPOST('type_resource') == 'MAQext' || GETPOST('type_resource') == 'MODext' || GETPOST('type_resource') == 'MAText')
			{
				require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
				$commandefourn 	= new CommandeFournisseurLigne($db);
				$res = $commandefourn->fetch($fk_objectdet);

				if ($commandefourn->rowid == $fk_objectdet)
				{
					if ($commandefourn->fk_product)
						$product->fetch($commandefourn->fk_product);
					$fk_product = $product->id+0;
					$ref_product = ($commandefourn->ref?$commandefourn->ref:$product->ref);
					$detail = ($commandefourn->label?$commandefourn->label:$product->label);
				}
				else
				{
					setEventMessages($langs->trans('Thereisnorecord'),null,'errors');
					$error++;
				}
			}
			elseif (GETPOST('type_resource') == 'MAQ')
			{
				//require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdet.class.php';
				//require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignment.class.php';
				//require_once DOL_DOCUMENT_ROOT.'/assets/assets/class/assets.class.php';
				$assignmentdet 	= new Assetsassignmentdet($db);
				$assignment 	= new Assetsassignment($db);
				$assets 		= new Assets($db);
				$assignmentdet->fetch($fk_objectdet);
				if ($assignmentdet->id == $fk_objectdet)
				{
					$assignment->fetch($assignmentdet->fk_asset_assignment);
					$fk_object = $assignment->id;
					$assets->fetch($assignmentdet->fk_asset);
					$ref_product = $assets->ref;
					$detail = $assets->descrip;
				}
				else
				{
					setEventMessages($langs->trans('Thereisnorecord'),null,'errors');
					$error++;
				}
			}
			else
				$detail = STRTOUPPER($detail);

			//verificamos si existe en producto projet
			$filter = array(1=>1);
			$filterstatic = '';
			if ($detail)
			{
				$filterstatic = ' AND (';
				$filterstatic.= " UPPER(ref) = '".$projectstatic->id.'|'.STRTOUPPER($detail)."'";
				$filterstatic.= " OR UPPER(ref) = '".STRTOUPPER($detail)."'";
			}
			if (!empty($filterstatic))
			{
				$filterstatic.= " OR ";
			}
			else
				$filterstatic.= " AND (";
			$filterstatic.= " UPPER(label) = '".STRTOUPPER($detail)."'";
			$filterstatic.= ")";
			$filterstatic.= " AND t.fk_projet =".$projectstatic->id;

			$res = $objectpp->fetchAll('','',0,0,$filter,'OR',$filterstatic,true);
			if ($res>0)
			{
				$fk_product 		= $objectpp->fk_product;
				$fk_product_projet 	= $objectpp->id;
				$fk_unit 			= $objectpp->fk_unit;
				$detail 			= $objectpp->label;
				$code_structure 	= $objectpp->code_structure;
			}
			else
			{
				if ($fk_product>0 && $product->fetch($fk_product)>0)
				{
				//buscamos la categoria a la que pertenece
					$detail = $product->label;
					$ref_product = $product->ref;
					$fk_unit = ($product->fk_unit?$product->fk_unit:$fk_unit);
					$aCate = $categorie->containing($fk_product, 'product', 'id');

					foreach ((array) $aCate AS $i => $fk)
					{
						if ($aStruct['aStrcatcode'][$fk])
						{
							$code_structure = $aStruct['aStrcatcode'][$fk];
						//cambiamos para que guarde la categoria del producto
							$code_structure = $fk;
						}
					}
					if (empty($code_structure) || $code_structure <0)
					{
						setEventMessages($langs->trans('Errortheproducthasnocategory'),null,'errors');
						$error++;
					}
				}
				else
				{
					$fk_product = 0;
				}
			}
			if (empty($res))
			{
				if (empty($code_structure) || $code_structure < 0)
				{
					setEventMessages($langs->trans('Errortheproducthasnocategory'),null,'errors');
					$error++;
				}
				if (!$error)
				{
					$lUpdate = false;
					if (empty($ref_product)) $lUpdate = true;
					$objectpp->fk_product = $fk_product+0;
					$objectpp->fk_projet = $projectstatic->id;
					$objectpp->ref = $projectstatic->id.'|'.$ref_product;
					$objectpp->label = $detail;
					$objectpp->fk_unit = $fk_unit;
					$objectpp->group_resource = $group_resource;
					$objectpp->code_structure = $code_structure;
					$objectpp->quant = price2num(GETPOST('quant')+0,'MT');
					$objectpp->percent_prod = (GETPOST('percent_prod')?GETPOST('percent_prod'):100);
					$objectpp->amount_noprod = price2num(GETPOST('amount_noprod')+0,'MT');
					$objectpp->amount = $price+0;
					$objectpp->fk_user_create = $user->id;
					$objectpp->fk_user_mod = $user->id;
					$objectpp->date_create = dol_now();
					$objectpp->date_mod = dol_now();
					$objectpp->tms = dol_now();
					$objectpp->status = 1;
					$fk_product_projet = $objectpp->create($user);
					if ($fk_product_projet<=0)
					{
						setEventMessages($objectpp->error,$objectpp->errors,'errors');
						$error++;
					}
					//actualizamos si lUpdate == true
					if ($lUpdate == true)
					{
						$objectpptmp->fetch($fk_product_projet);
						$objectpptmp->ref .= '(PROV)'.$objectpptmp->id;
						$res = $objectpptmp->update($user);
						if ($res <=0)
						{
							setEventMessages($objectpptmp->error,$objectpptmp->errors,'errors');
							$error++;
						}
					}
				}
			}
		}
		else
		{
			$objectpp->fetch($fk_product_projet);
			$fk_product 		= $objectpp->fk_product;
			$fk_product_projet 	= $objectpp->id;
			$fk_unit 			= $objectpp->fk_unit;
			$detail 			= $objectpp->label;
			$code_structure 	= $objectpp->code_structure;
		}

		//$objectptr->fk_projet_task 	= GETPOST('id');
		//$objectptr->ref 			= '(PROV)';
		$objectptr->ref_ext 		= GETPOST('ref_ext','alpha');
		$objectptr->group_resource 	= $group_resource;
		$objectptr->type_resource 	= GETPOST('type_resource','alpha');
		$objectptr->fk_projet 		= $projectstatic->id;
		$objectptr->fk_object 		= $fk_object;
		$objectptr->object 			= $nom_object;
		$objectptr->fk_objectdet 	= $fk_objectdet;
		$objectptr->objectdet 		= $nom_objectdet;
		$objectptr->fk_user_create 	= $user->id;
		$objectptr->fk_user_mod 	= $user->id;
		$objectptr->code_structure 	= $code_structure;
		$objectptr->fk_product 		= $fk_product+0;
		$objectptr->fk_product_projet = $fk_product_projet+0;
		$objectptr->fk_product_budget = GETPOST('fk_product_budget')+0;
		$objectptr->detail 			= $detail;
		$objectptr->fk_unit 		= $fk_unit;
		$objectptr->quant 			= GETPOST('quant');
		$objectptr->percent_prod 	= 100;
		$objectptr->amount_noprod 	= 0;
		$objectptr->amount 			= GETPOST('amount')+0;
		$objectptr->rang 			= 0;
		//$objectptr->date_create 	= dol_now();
		$objectptr->date_mod 		= dol_now();
		$objectptr->tms 			= dol_now();
		$objectptr->status 			= 1;
		$res = $objectptr->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($objectptr->error,$objectptr->errors,'errors');
			$action = '';
		}
		else
		{
			$objectptr->ref .= $objectptr->id;
			$res = $objectptr->update($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objectptr->error,$objectptr->errors,'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			header ('Location: '.$_SERVER['PHP_SELF'].'?id='.$id.'&withproject=1');
			exit;
		}
		else
			$db->rollback();
	}
	$action = '';
}

if ($action == 'deleteres')
{


	$objectptr->fetch(GETPOST('idreg'));
	if ($objectptr->fk_projet_task == $id && $user->rights->monprojet->con->del)
	{
		$fk_entrepot = 0;
		if ($conf->almacen->enabled)
		{
			if ($projectadd->fk_entrepot <=0)
			{
				require_once DOL_DOCUMENT_ROOT.'/almacen/local/class/entrepotrelationext.class.php';
				$objrel = new Entrepotrelationext($db);
				$res = $objrel->fetchAll('','',0,0,array(1=>1),'AND'," AND fk_projet = ".$projectstatic->id,true);
				$fk_entrepot=$objrel->id;
				if (!$fk_entrepot)
				{
					$error++;
					setEventMessages($langs->trans('Error, no esta definido un almacen para el proyecto'),null,'errors');
					exit;
				}
			}
			else $fk_entrepot = $projectadd->fk_entrepot;

		}
		else
		{
			setEventMessages($langs->trans('Error, no esta activado modulo de almacen'),null,'errors');
			exit;
		}

		$db->begin();
		$fk_stock = $objectptr->fk_stock_mouvement;
		$res = $objectptr->delete($user);
		if ($res <=0)
			setEventMessages($objectptr->error,$objectptr->errors,'errors');
		//eliminamos el movimiento si corresponde
		if ($fk_stock && $use_resource == 2)
		{
			require_once DOL_DOCUMENT_ROOT.'/monprojet/class/mouvementstockext.class.php';
			$objstock = new Mouvementstockext($db);
			$resdel = $objstock->_delete($fk_stock,$fk_entrepot,$objectptr->fk_product,$objectptr->quant);
			if ($resdel<=0)
			{
				$error++;
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Successfulldeletion'),null,'mesgs');
		}
		else
			$db->rollback();
	}
	unset($_GET['idreg']);
	$action ='';
}

if ($action == 'deldoc' && $user->rights->monprojet->payp->mod)
{
	$objdoc->fetch(GETPOST('linedoc'));
	$object->fetch($id);
	$error=0;
	if ($objdoc->id == GETPOST('linedoc'))
	{
		$namefile = GETPOST('namedoc');
	   	//buscamos
		$db->begin();

		$aDoc = explode(';',$objdoc->document);
		$document = '';
		foreach ((array) $aDoc AS $i => $name)
		{
			if ($name != $namefile)
			{
				if ($document) $document.=';';
				$document .= $name;
			}
		}
		$objdoc->document = $document;
		$res = $objdoc->update($user);
		if (!$res>0) $error++;

	    //del photo
		$dirproj = $projectstatic->ref;
		$dir    = $conf->projet->multidir_output[$conf->entity].'/'.$projectstatic->ref.'/'.$object->ref.'/'.$objdoc->id;

		$fileimg=$dir.'/'.$namefile;
		dol_delete_file($fileimg);
		if (!$error)
			$db->commit();
		else
			$db->rollback();
	}
	$action = '';
}

if ($action == 'addtimespent' && $user->rights->monprojet->timed->crear)
{
	$error=0;

	$timespent_durationhour = GETPOST('timespent_durationhour','int');
	$timespent_durationmin = GETPOST('timespent_durationmin','int');
	// if (empty($timespent_durationhour) && empty($timespent_durationmin))
	//   {
	// 	setEventMessage($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Duration")),'errors');
	// 	$error++;
	//   }
	if (empty($_POST["userid"]))
	{
		$langs->load("errors");
		setEventMessage($langs->trans('ErrorUserNotAssignedToTask'),'errors');
		$error++;
	}
	if (! $error)
	{
		$object->fetch($id, $ref);
		$rang = $object->rang;
		$res=$object->fetch_optionals($object->id,$extralabels);

		$object->fetch_projet();
		if (empty($object->projet->statut))
		{
			setEventMessage($langs->trans("ProjectMustBeValidatedFirst"),'errors');
			$error++;
		}
		else
		{

			$db->begin();
			$object->rang = $rang;
			$object->timespent_note = $_POST["timespent_note"];
		//$object->progress = GETPOST('progress', 'int')+0;
			$object->timespent_duration = $_POST["timespent_durationhour"]*60*60;
		// We store duration in seconds
			$object->timespent_duration+= $_POST["timespent_durationmin"]*60;
		// We store duration in seconds
			$object->timespent_date = dol_mktime(12,0,0,$_POST["timemonth"],$_POST["timeday"],$_POST["timeyear"]);
			if (!$user->admin)
				$object->timespent_date = dol_now();

			$object->timespent_fk_user = $_POST["userid"];
			$result=$object->addTimeSpent($user,1);
			if ($result >= 0)
			{
				setEventMessage($langs->trans("RecordSaved"));
			}
			else
			{
				setEventMessage($langs->trans($object->error),'errors');
				$error++;
			}

		//add photo/////////////////////////////////////////
		// Logo/Photo save
			$newDir = $result;
			$dirtask = $object->ref;
			$projectstatic->fetch($object->fk_project);
			$dirproj = $projectstatic->ref;
		//$dir     = $conf->projet->multidir_output[$conf->entity].'/'.$dirproj.'/'.$dirtask;
			$dira    = $conf->projet->multidir_output[$conf->entity].'/'.$dirproj;
			$dirb    = $conf->projet->multidir_output[$conf->entity].'/'.$dirproj.'/'.$dirtask;
			$dir     = $conf->projet->multidir_output[$conf->entity].'/'.$dirproj.'/'.$dirtask.'/'.$newDir;

			$namefile = dol_sanitizeFileName($_FILES['docpdf']['name']);
			$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
			if ($file_OKfin)
			{
		//verificamos permisos para el modo de subida de archivos
				$mode = 0;
				$mode = $user->rights->monprojet->pho->up4;
				if ($user->rights->monprojet->pho->up3) $mode = 3;
				if ($user->rights->monprojet->pho->up2) $mode = 2;
				if ($user->rights->monprojet->pho->up1) $mode = 1;
				if ($user->rights->monprojet->pho->up5) $mode = 5;


				if (GETPOST('deletedocfin'))
				{
					$fileimg=$dir.'/'.$namefile;
					$dirthumbs=$dir.'/thumbs';
					dol_delete_file($fileimg);
					dol_delete_dir_recursive($dirthumbs);
				}
				if (doc_format_supported($_FILES['docpdf']['name'],$mode) > 0)
				{
					dol_mkdir($dira);
					dol_mkdir($dirb);
					dol_mkdir($dir);
					if (@is_dir($dir))
					{
						$aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
						$file = '';
						foreach ((array) $aFile AS $j => $val)
						{
							if (empty($file))
								$file = $newDir;
							else
								$file.= '.'.$val;
						}
			//buscamos el archivo
						$objdoc->fetch('',$newDir);
						if ($objdoc->fk_task_time == $newDir)
						{
				//modificamos
							$objdoc->document = $file;
							$objdoc->unit_declared = GETPOST('unit_declared','int');
							$objdoc->tms = dol_now();
							$objdoc->statut = 1;
							$res = $objdoc->update($user);

						}
						else
						{
				//agregamos el archivo
							$objdoc->fk_task_time = $newDir;
							$objdoc->fk_request_item = $riid;
							$objdoc->document = $file;
							$objdoc->unit_declared = GETPOST('unit_declared','int');
							$objdoc->fk_user_create = $user->id;
							$objdoc->date_create = dol_now();
							$objdoc->tms = dol_now();
							$objdoc->statut = 1;
							$res = $objdoc->create($user);
							if (! $res >0)
								$error++;
						}
			//$newfile=$dir.'/'.dol_sanitizeFileName($_FILES['docpdf']['name']);
			//$newfile=$dir.'/'.dol_sanitizeFileName($id.'.pdf');
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
				switch($_FILES['docpdf']['error'])
				{
					case 1:
		  //uploaded file exceeds the upload_max_filesize directive in php.ini
					case 2:
		   //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
					$errors[] = "ErrorFileSizeTooLarge";
					$error++;
					break;
					case 3:
		  //uploaded file was only partially uploaded
					$error++;
					$errors[] = "ErrorFilePartiallyUploaded";
					break;
				}
			}
			else
			{
		//creamos el registro el time_doc es la extension al task_time y unico
		//buscamos el archivo
				$objdoc->fetch('',$newDir);
				if ($objdoc->fk_task_time == $newDir)
				{
			//modificamos
					$objdoc->document = '';
					$objdoc->unit_declared = GETPOST('unit_declared','int');
					$objdoc->tms = dol_now();
					$objdoc->statut = 1;
					$res = $objdoc->update($user);
					if (! $res >0)
						$error++;
				}
				else
				{
			//agregamos el archivo
					$objdoc->fk_task_time = $newDir;
					$objdoc->fk_task_payment = 0;
					$objdoc->fk_request_item = $riid+0;
					$objdoc->document = '';
					$objdoc->unit_declared = GETPOST('unit_declared','int');
					$objdoc->fk_user_create = $user->id;
					$objdoc->date_create = dol_now();
					$objdoc->tms = dol_now();
					$objdoc->statut = 1;
					$res = $objdoc->create($user);
					if (! $res >0)
						$error++;
				}
			}

		//fin photo
		//actualizamos el registro en ejecutado
			$objdoc->getsum($object->id);
		//recuperamos nuevamente
		// $object->fetch($id, $ref);
		// $res=$object->fetch_optionals($object->id,$extralabels);
		//actualizamos el progress
		//total
			$_POST['options_unit_declared'] = $objdoc->total+0;
		//mismos valores
			$_POST['options_fk_contrat'] = $object->array_options['options_fk_contrat']+0;
			$_POST['options_c_grupo'] = $object->array_options['options_c_grupo'];
			$_POST['options_unit_program'] = $object->array_options['options_unit_program'];
			$_POST['options_fk_unit'] = $object->array_options['options_fk_unit'];
			$_POST['options_fk_item'] = $object->array_options['options_fk_item']+0;
			$_POST['options_unit_amount'] = $object->array_options['options_unit_amount']+0;
			$_POST['options_unit_ejecuted'] = $object->array_options['options_unit_ejecuted']+0;
			$objecttaskadd->fetch('',$object->id);
			if ($objecttaskadd->fk_task == $object->id)
			{
				$objecttaskadd->unit_declared = $objdoc->total+0;
				$res = $objecttaskadd->update($user);
				if (!$res>0)
					$error++;
			}
			$progress = 0;
			if ($objecttaskadd->unit_program>0)
				$object->progress = round($objdoc->total / $objecttaskadd->unit_program*100);
			$object->rang = $rang;
		// Fill array 'array_options' with data from add form
			$ret = $extrafields->setOptionalsFromPost($extralabels,$object);
			if ($ret < 0) $error++;
			if (! $error)
			{
				$result=$object->update($user,1);
				if ($result < 0)
				{
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
		}
		if (!$error)
		{
			$db->commit();
		// update OK
			$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/task/time.php?id='.$object->id.'&withproject=1',1);
			header("Location: ".$urltogo);
			exit;
		}
		else
			$db->rollback();
	}
	else
	{
		$action='';
	}
}

if ($action == 'adddoc' && $user->rights->monprojet->timed->crear)
{
	$error=0;
	if (! $error)
	{
		$object->fetch($id, $ref);
		$res=$object->fetch_optionals($object->id,$extralabels);

		$object->fetch_projet();

		if (empty($object->projet->statut))
		{
			setEventMessage($langs->trans("ProjectMustBeValidatedFirst"),'errors');
			$error++;
		}
		else
		{
		//buscamos
			$lineid = GETPOST('lineid');
			$db->begin();

		//add photo
		// Logo/Photo save
			$code = generarcodigo(3);
			$newDir = $lineid.$code;
			$dirtask = $object->ref;
			$projectstatic->fetch($object->fk_project);
			$dirproj = $projectstatic->ref;
		//$dir     = $conf->projet->multidir_output[$conf->entity].'/'.$dirproj.'/'.$dirtask;
			$dira    = $conf->projet->multidir_output[$conf->entity].'/'.$dirproj;
			$dirb    = $conf->projet->multidir_output[$conf->entity].'/'.$dirproj.'/'.$dirtask;
			$dir     = $conf->projet->multidir_output[$conf->entity].'/'.$dirproj.'/'.$dirtask.'/'.$lineid;

			$namefile = dol_sanitizeFileName($_FILES['docpdf']['name']);
			$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
			if ($file_OKfin)
			{
		//verificamos permisos para el modo de subida de archivos
				$mode = 0;
				$mode = $user->rights->monprojet->pho->up4;
				if ($user->rights->monprojet->pho->up3) $mode = 3;
				if ($user->rights->monprojet->pho->up2) $mode = 2;
				if ($user->rights->monprojet->pho->up1) $mode = 1;
				if ($user->rights->monprojet->pho->up5) $mode = 5;

				if (GETPOST('deletedocfin'))
				{
					$fileimg=$dir.'/'.$namefile;
					$dirthumbs=$dir.'/thumbs';
					dol_delete_file($fileimg);
					dol_delete_dir_recursive($dirthumbs);
				}
				if (doc_format_supported($_FILES['docpdf']['name'],$mode) > 0)
				{
					dol_mkdir($dira);
					dol_mkdir($dirb);
					dol_mkdir($dir);
					if (@is_dir($dir))
					{
						$aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
						$file = '';
						foreach ((array) $aFile AS $j => $val)
						{
							if (empty($file))
								$file = $newDir;
							else
								$file.= '.'.$val;
						}
			//buscamos el archivo
						$objdoc->fetch('',$lineid);
						if ($objdoc->fk_task_time == $lineid)
						{
				//modificamos
							if (empty($objdoc->document))
								$objdoc->document = $file;
							else
								$objdoc->document.=';'.$file;
							$objdoc->document;
							$objdoc->tms = dol_now();
							$objdoc->statut = 1;
							$res = $objdoc->update($user);
							if (!$res>0)
								$error++;
						}
						else
						{
							$error++;
						}
			//$newfile=$dir.'/'.dol_sanitizeFileName($_FILES['docpdf']['name']);
			//$newfile=$dir.'/'.dol_sanitizeFileName($id.'.pdf');
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
		}
	}
	else
	{
		$action='';
	}
	if (!$error)
		$db->commit();
	else
		$db->rollback();
}


if ($action == 'updateline' && ! $_POST["cancel"] && $user->rights->monprojet->timed->mod)
{
	$error=0;

	// if (empty($_POST["new_durationhour"]) && empty($_POST["new_durationmin"]))
	// {
	// 	setEventMessage($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Duration")),'errors');
	// 	$error++;
	// }

	if (! $error)
	{
		$db->begin();
		$object->fetch($id, $ref);
		$res=$object->fetch_optionals($object->id,$extralabels);

		$object->timespent_id = $_POST["lineid"];
		$object->timespent_note = $_POST["timespent_note_line"];
		$object->timespent_old_duration = $_POST["old_duration"];
		$object->timespent_duration = $_POST["new_durationhour"]*60*60;	// We store duration in seconds
		$object->timespent_duration+= $_POST["new_durationmin"]*60;		// We store duration in seconds
		$object->timespent_date = dol_mktime(12,0,0,$_POST["timelinemonth"],$_POST["timelineday"],$_POST["timelineyear"]);
		$object->timespent_fk_user = $_POST["userid_line"];

		$result=$object->updateTimeSpent($user);
		if ($result >= 0)
		{
			setEventMessage($langs->trans("RecordSaved"));
		}
		else
		{
			setEventMessage($langs->trans($object->error),'errors');
			$error++;
		}

		//creamos el registro el time_doc
		//buscamos el archivo
		if (empty($error))
		{
			$lineid = GETPOST('lineid');
			$res = $objdoc->fetch('',$lineid);
			if ($res > 0 && $objdoc->fk_task_time == $lineid)
			{
				//modificamos
				//$objdoc->document = '';
				$objdoc->unit_declared = GETPOST('unit_declared','int');
				$objdoc->tms = dol_now();
				$objdoc->statut = 1;
				$res = $objdoc->update($user);
				if (! $res >0)
					$error++;
			}
			else
			{
				//agregamos el archivo
				$objdoc->fk_task_time = $lineid;
				$objdoc->fk_task_payment = 0;
				$objdoc->document = '';
				$objdoc->unit_declared = GETPOST('unit_declared','int');
				$objdoc->fk_user_create = $user->id;
				$objdoc->date_create = dol_now();
				$objdoc->tms = dol_now();
				$objdoc->statut = 1;
				$res = $objdoc->create($user);
				if (! $res >0)
					$error++;
			}
			$objdoc->getsum($object->id);
			//post
			$_POST['options_unit_declared'] = $objdoc->total+0;
			//mismos valores
			$_POST['options_fk_contrat'] = $object->array_options['options_fk_contrat']+0;
			$_POST['options_c_grupo'] = $object->array_options['options_c_grupo'];
			$_POST['options_unit_program'] = $object->array_options['options_unit_program'];
			$_POST['options_fk_unit'] = $object->array_options['options_fk_unit'];
			$_POST['options_fk_item'] = $object->array_options['options_fk_item'];
			$_POST['options_unit_amount'] = $object->array_options['options_unit_amount'];
			$_POST['options_unit_ejecuted'] = $object->array_options['options_unit_ejecuted']+0;

			//actualizamos
			$objecttaskadd->fetch('',$object->id);
			if ($objecttaskadd->fk_task == $object->id)
			{
				$objecttaskadd->unit_declared = $objdoc->total+0;
				$res = $objecttaskadd->update($user);
				if (!$res>0)
					$error++;
			}
		// Fill array 'array_options' with data from add form
			$ret = $extrafields->setOptionalsFromPost($extralabels,$object);
			if ($ret < 0) $error++;
			$progress = 0;
			if ($objecttaskadd->unit_program>0)
				$object->progress = round($objdoc->total / $objecttaskadd->unit_program*100);

			if (! $error)
			{
				$result=$object->update($user,1);
				if ($result < 0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
		}
		if (empty($error))
			$db->commit();
		else
			$db->rollback();
	}
	else
	{
		$action='';
	}
}

if ($action == 'confirm_delete' && $confirm == "yes" && $user->rights->monprojet->timed->del)
{
	$db->begin();
	$object->fetch($id, $ref);
	$res=$object->fetch_optionals($object->id,$extralabels);
	$object->fetchTimeSpent($_GET['lineid']);
	$result = $object->delTimeSpent($user);

	$res=$objdoc->fetch('',GETPOST('lineid'));
	if ($res>0 && $objdoc->fk_task_time == GETPOST('lineid'))
	{
		$res = $objdoc->delete($user);
		if (!$res>0)
			$error++;
	}
	//actualizamos el registro en ejecutado
	$objdoc->getsum($object->id);
	//recuperamos nuevamente
	// $object->fetch($id, $ref);
	// $res=$object->fetch_optionals($object->id,$extralabels);
	//actualizamos el progress
	$progress = 0;
	if ($object->array_options['options_unit_program']>0)
		$object->progress = round($objdoc->total / $object->array_options['options_unit_program']*100);
	//total
	$_POST['options_unit_declared'] = $objdoc->total+0;
	//mismos valores
	$_POST['options_fk_contrat'] = $object->array_options['options_fk_contrat']+0;
	$_POST['options_c_grupo'] = $object->array_options['options_c_grupo'];
	$_POST['options_unit_program'] = $object->array_options['options_unit_program'];
	$_POST['options_fk_unit'] = $object->array_options['options_fk_unit'];
	$_POST['options_fk_item'] = $object->array_options['options_fk_item']+0;
	$_POST['options_unit_amount'] = $object->array_options['options_unit_amount']+0;
	$_POST['options_unit_ejecuted'] = $object->array_options['options_unit_ejecuted']+0;

	//actualizamos
	$objecttaskadd->fetch('',$object->id);
	if ($objecttaskadd->fk_task == $object->id)
	{
		$objecttaskadd->unit_declared = $objdoc->total+0;
		$res = $objecttaskadd->update($user);
		if (!$res>0)
			$error++;
	}

	// Fill array 'array_options' with data from add form
	$ret = $extrafields->setOptionalsFromPost($extralabels,$object);
	if ($ret < 0) $error++;
	$progress = 0;
	if ($objecttaskadd->unit_program>0)
		$object->progress = round($objdoc->total / $objecttaskadd->unit_program*100);

	if (! $error)
	{
		$result=$object->update($user,1);
		if ($result < 0)
		{
			setEventMessages($object->error,$object->errors,'errors');
			$error++;
			$action='';
		}
	}
	if (!$error)
		$db->commit();
	else
		$db->rollback();
}

// Retreive First Task ID of Project if withprojet is on to allow project prev next to work
if (! empty($project_ref) && ! empty($withproject))
{
	if ($projectstatic->fetch(0,$project_ref) > 0)
	{
		$tasksarray=$object->getTasksArray(0, 0, $projectstatic->id, $socid, 0);
		if (count($tasksarray) > 0)
		{
			$id=$tasksarray[0]->id;
		}
		else
		{
			header("Location: ".DOL_URL_ROOT.'/projet/tasks.php?id='.$projectstatic->id.($withproject?'&withproject=1':'').(empty($mode)?'':'&mode='.$mode));
			exit;
		}
	}
}


/*
 * View
 */

$morejs = array();
$morecss = array('/monprojet/css/style.css',);
llxHeader('',$title,'','','','',$morejs,$morecss,0,0);

$form = new Formv($db);
$formother = new FormOther($db);
$userstatic = new User($db);

if ($id > 0 || ! empty($ref))
{
	/*
	 * Fiche projet en mode visu
	 */
	if ($object->fetch($id, $ref) >= 0)
	{
		$objecttaskadd->fetch('',$id);
		if ($objecttaskadd->fk_task == $id)
		{
			//volvemos a sumar los declarados
			//$objdoc->getadvance($id);
			//$totaladvance = 0;
			//foreach ((array) $objdoc->aArray AS $statutad => $value)
			//	$totaladvance+=$value;
			//if ($totaladvance != $objecttaskadd->unit_declared)
			//{
				//actualizamos
			//	$objtemp = new Projettaskadd($db);
			//	$objtemp->fetch($objecttaskadd->id);
			//	if ($objtemp->id == $objecttaskadd->id)
			//	{
			//		$objtemp->unit_dclared = $totaladvance;
			//		$res = $objtemp->update($user,0);
			//		$objecttaskadd->unit_declared = $totaladvance;
			//	}
			//}
		}
		$objadd->fetch($id,$ref);
		$res=$objadd->fetch_optionals($objadd->id,$extralabels);
		$res=$object->fetch_optionals($object->id,$extralabels);
		if ($object->array_options['options_c_grupo'] == 1)
		  $lDisabled = true; //es grupo y se deshabilita para registro de avance
		$mobject->fetch($id, $ref);

		$result=$projectstatic->fetch($object->fk_project);
		$res=$projectstatic->fetch_optionals($projectstatic->id,$extralabels_project);
		$projectadd->fetch(0,$object->fk_projet);
		//verificamos que modelo de buscador utilizara en productos
		$lFilterentrepot = false;
		if ($projectstatic->array_options['options_use_resource'] == 1)
			$lFilterentrepot = true;
		if (! empty($projectstatic->socid)) $projectstatic->fetch_thirdparty();

		$object->project = dol_clone($projectstatic);

		$userWrite = $projectstatic->restrictedProjectArea($user,'write');
		if ($withproject)
		{
			// Tabs for project
			$tab='tasks';
			$head=project_prepare_head($projectstatic);
			dol_fiche_head($head, $tab, $langs->trans("Project"),0,($projectstatic->public?'projectpub':'project'));

			$param=($mode=='mine'?'&mode=mine':'');

			print '<table class="border" width="100%">';

			// Ref
			print '<tr><td width="30%">';
			print $langs->trans("Ref");
			print '</td><td>';
			// Define a complementary filter for search of next/prev ref.
			if (! $user->rights->projet->all->lire)
			{
				$projectsListId = $projectstatic->getProjectsAuthorizedForUser($user,$mine,0);
				$projectstatic->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
			}
			print $form->showrefnav($projectstatic,'project_ref','',1,'ref','ref','',$param.'&withproject=1');
			print '</td></tr>';

			// Label
			print '<tr><td>'.$langs->trans("Label").'</td><td>'.$projectstatic->title.'</td></tr>';
			print '</table>';
			dol_fiche_end();
		}

		$head=task_prepare_head($object);
		dol_fiche_head($head, 'resource', $langs->trans("Task"),0,'projecttask');

		if ($action == 'deleteline')
		{
			print $form->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&lineid='.$_GET["lineid"].($withproject?'&withproject=1':''),$langs->trans("DeleteATimeSpent"),$langs->trans("ConfirmDeleteATimeSpent"),"confirm_delete",'','',1);
		}

		print '<table class="border" width="100%">';

		$param=($withproject?'&withproject=1':'');
		$linkback=$withproject?'<a href="'.DOL_URL_ROOT.'/monprojet/tasks.php?id='.$projectstatic->id.'">'.$langs->trans("BackToList").'</a>':'';

		// Ref
		print '<tr><td width="30%">';
		print $langs->trans("Ref");
		print '</td><td colspan="3">';
		if (! GETPOST('withproject') || empty($projectstatic->id))
		{
			$projectsListId = $projectstatic->getProjectsAuthorizedForUser($user,$mine,1);
			$object->next_prev_filter=" fk_projet in (".$projectsListId.")";
			$objadd->next_prev_filter=" fk_projet in (".$projectsListId.")";
		}
		else
		{
			$object->next_prev_filter=" fk_projet = ".$projectstatic->id;
			$objadd->next_prev_filter=" fk_projet = ".$projectstatic->id;
		}
		print $form->showrefnavadd($objadd,'id',$linkback,1,'ref','ref','',$param);
		print '</td></tr>';

		// Label
		print '<tr><td>'.$langs->trans("Label").'</td><td colspan="3">'.$object->label.'</td></tr>';

		// Other options
		if (!$lDisabled)
		{
			$parameters=array('newaction'=>'viewprogram');
			//$reshook=$hookmanager->executeHooks('doActions',$parameters,$objadd,$action); // Note that $action and $object may have been modified by hook
			if (empty($reshook) && ! empty($extrafields->attribute_label))
			{
				//print $object->showOptionals($extrafields);
			}
		}
		print '</table>';

		dol_fiche_end();
		$lRegprogram = true;
		//verificamos si el proyecto require registro de programaciones
		if ($projectadd->programmed)
		{
			$lRegprogram = false;
			//verificamos si esta en solicitudes aprobadas vigentes
			$requestitem = new Requestitem($db);
			if (empty($riid))
			{
				$filter = array('fk_statut'=>2);
				$filterstatic = " AND t.ref = '".$object->ref."'";
				$numreg = $requestitem->fetchAll('ASC', 't.datev', 0, 0, $filter, 'AND',$filterstatic,true);
				if ($numreg > 0)
				{
					$riid = ($riid?$riid:$requestitem->id);
					$lRegprogram = true;
				}
				else
					$lRegprogram = false;
			}
			else
				$lRegprogram = true;
		}
		else
		{
			$riid = 0;
			$lRegprogram = true;
		}

		//echo '<hr>lReprogram'.$lRegprogram;
		/*
		 *verificamos si esta habiitado para la carga de datos en la tarea
		 */
		if (!$user->admin)
			$lregtask = verifcontacttask($user,$object);
		else
			$lregtask = true;

		if ($user->rights->monprojet->con->crear && $projectstatic->statut == 1 &&
			$object->fk_statut < 2)
		{
			if (! empty($conf->use_javascript_ajax))
			{
				print "\n".'<script type="text/javascript">';
				print '$(document).ready(function () {
					$("#type_resource").change(function() {
						document.addr.action.value="createaddr";
						document.addr.optsel.value="r";
						document.addr.submit();
					});
					$("#fk_soc").change(function() {
						document.addr.action.value="createaddr";
						document.addr.optsel.value="s";
						document.addr.submit();
					});
					$("#fk_member").change(function() {
						document.addr.action.value="createaddr";
						document.addr.optsel.value="m";
						document.addr.submit();
					});
					$("#fk_commande").change(function() {
						document.addr.action.value="createaddr";
						document.addr.optsel.value="c";
						document.addr.submit();
					});
					$("#fk_asset_det").change(function() {
						document.addr.action.value="createaddr";
						document.addr.optsel.value="a";
						document.addr.submit();
					});
					$("#search_product").change(function() {
						document.addr.action.value="createaddr";
						document.addr.optsel.value="p";
						document.addr.submit();
					});
					$("#fk_contrat").change(function() {
						document.addr.action.value="createaddr";
						document.addr.optsel.value="p";
						document.addr.submit();
					});
				});';
				print '</script>'."\n";
			}
			print '<form id="addr" name="addr" method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			if ($action == 'editres' ||($action=='createaddr' && GETPOST('idreg')))
			{
				print '<input type="hidden" name="action" value="updateresource">';
				print '<input type="hidden" name="idreg" value="'.GETPOST('idreg').'">';
			}
			else
				print '<input type="hidden" name="action" value="addresource">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			//request_item_id
			print '<input type="hidden" name="withproject" value="'.$withproject.'">';
			print '<input type="hidden" name="optsel" value="">';
		}
		if ($lResource)
			include DOL_DOCUMENT_ROOT.'/monprojet/task/tpl/list_resource.tpl.php';
		else
			print '<h3>'.$langs->trans('Error, revisar configuración del proyecto').'</h3>';
		if ($user->rights->monprojet->con->crear)
		{
			print '</form>';
		}

		$lDisabled= true;
		/*
		 * Add time spent
		 */
		if (!$lDisabled && $user->rights->monprojet->timed->crear &&
			$lregtask && $projectstatic->statut == 1 &&
			$object->fk_statut < 2 && $action != 'editlinep' && $lRegprogram)
		{
			print '<br>';

			print '<form enctype="multipart/form-data" method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="addtimespent">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="riid" value="'.$riid.'">';
			//request_item_id
			print '<input type="hidden" name="withproject" value="'.$withproject.'">';

			print '<table class="noborder" width="100%">';

			print '<tr class="liste_titre">';
			print '<td width="100">'.$langs->trans("Date").'</td>';
			print '<td>'.$langs->trans("By").'</td>';
			print '<td>'.$langs->trans("Note").'</td>';
			//print '<td>'.$langs->trans("ProgressDeclared").'</td>';
			print '<td>'.$langs->trans("Declared").'</td>';
			print '<td align="center">'.$langs->trans("Photo").'</td>';

			print '<td align="right" colspan="2">'.$langs->trans("Action").'</td>';
			print "</tr>\n";

			print '<tr '.$bc[false].'>';

			// Date
			print '<td class="nowrap">';
			$newdate=dol_mktime(12,0,0,$_POST["timemonth"],$_POST["timeday"],$_POST["timeyear"]);
			if ($user->admin)
				print $form->select_date($newdate,'time','','','',"timespent_date");
			else
				print dol_print_date(dol_now(),'day');
			print '</td>';

			// Contributor
			print '<td class="nowrap">';
			print img_object('','user','class="hideonsmartphone"');
			$contactsoftask=$object->getListContactId('internal');
			if (count($contactsoftask)>0)
			{
				if ($user->admin)
				{
					$userid=$contactsoftask[0];
					print $form->select_dolusers((GETPOST('userid')?GETPOST('userid'):$userid),'userid',0,'',0,'',$contactsoftask);
				}
				else
				{
					print $user->login;
					print '<input type="hidden" name="userid" value="'.$user->id.'">';
				}
			}
			else
			{
				print img_error($langs->trans('FirstAddRessourceToAllocateTime')).$langs->trans('FirstAddRessourceToAllocateTime');
			}
			print '</td>';

			// Note
			print '<td class="nowrap">';
			print '<textarea name="timespent_note" cols="20" rows="'.ROWS_2.'">'.($_POST['timespent_note']?$_POST['timespent_note']:'').'</textarea>';
			print '</td>';

			// // Progress declared
			// print '<td class="nowrap">';
			// print $formother->select_percent(GETPOST('progress')?GETPOST('progress'):$object->progress,'progress');
			// print '</td>';

			// unit declared
			print '<td class="nowrap">';
			print '<input type="number" step="any" min="0" class="len100" name="unit_declared" value="">';
			print '</td>';

			//Photo
			print '<td nowrap align="right" class="SI-FILES-STYLIZED">';
			print '<label class="cabinet">';
			include DOL_DOCUMENT_ROOT.'/monprojet/task/tpl/adddoc.tpl.php';
			print '</label>';
			print '</td>';

			// Duration - Time spent
			print '<td class="nowrap" align="right">';
			// print $form->select_duration('timespent_duration', ($_POST['timespent_duration']?$_POST['timespent_duration']:''), 0, 'text');
			print '</td>';

			print '<td align="center">';
			print '<input type="submit" class="button" value="'.$langs->trans("Add").'">';
			print '</td></tr>';

			print '</table></form>';
		}

		print '<br>';

		/*
		 *  List of time spent
		 */
		$sql = "SELECT t.rowid, t.task_date, t.task_duration, t.fk_user, t.note";
		$sql.= ", u.lastname, u.firstname";
		$sql .= " FROM ".MAIN_DB_PREFIX."projet_task_time as t";
		$sql .= " , ".MAIN_DB_PREFIX."user as u";
		$sql .= " WHERE t.fk_task =".$object->id;
		$sql .= " AND t.fk_user = u.rowid";
		$sql .= " ORDER BY t.task_date DESC, t.rowid DESC";

		$var=true;
		$resql = $db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			$tasks = array();
			while ($i < $num)
			{
				$row = $db->fetch_object($resql);
				$tasks[$i] = $row;
				$i++;
			}
			$db->free($resql);
		}
		else
		{
			dol_print_error($db);
		}
		if (!$lDisabled)
		{
			if ($action == 'editline')
				print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';
			elseif ($action == 'editlinep')
				print '<form  enctype="multipart/form-data" method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="lineid" value="'.$lineid.'">';
			print '<input type="hidden" name="withproject" value="'.$withproject.'">';

			if ($action == 'editline')
				print '<input type="hidden" name="action" value="updateline">';
			elseif ($action == 'editlinep')
				print '<input type="hidden" name="action" value="adddoc">';

			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
			print '<td width="10%">'.$langs->trans("Date").'</td>';
			print '<td width="20%">'.$langs->trans("By").'</td>';
			print '<td width="35%" align="left">'.$langs->trans("Note").'</td>';
		//		print '<td align="right">'.$langs->trans("TimeSpent").'</td>';
			print '<td width="6%" align="right">'.$langs->trans("Declared").'</td>';
			print '<td width="25%" align="center">'.$langs->trans("Photo").'</td>';
			print '<td width="4%">&nbsp;</td>';
			print "</tr>\n";

			$total = 0;
			$totaldeclared = 0;
			foreach ($tasks as $task_time)
			{
				$var=!$var;
				print "<tr ".$bc[$var].">";

			// Date
				print '<td>';
				if ($_GET['action'] == 'editline' && $_GET['lineid'] == $task_time->rowid)
				{
					print $form->select_date($db->jdate($task_time->task_date),'timeline','','','',"timespent_date");
				}
				else
				{
					print dol_print_date($db->jdate($task_time->task_date),'day');
				}
				print '</td>';
			// User
				print '<td>';
				if ($_GET['action'] == 'editline' && $_GET['lineid'] == $task_time->rowid)
				{
					$contactsoftask=$object->getListContactId('internal');
					if (!in_array($task_time->fk_user,$contactsoftask)) {
						$contactsoftask[]=$task_time->fk_user;
					}
					if (count($contactsoftask)>0) {
						print $form->select_dolusers($task_time->fk_user,'userid_line',0,'',0,'',$contactsoftask);
					}else {
						print img_error($langs->trans('FirstAddRessourceToAllocateTime')).$langs->trans('FirstAddRessourceToAllocateTime');
					}
				}
				else
				{
					$userstatic->id         = $task_time->fk_user;
					$userstatic->lastname	= $task_time->lastname;
					$userstatic->firstname 	= $task_time->firstname;
					print $userstatic->getNomUrl(1);
				}
				print '</td>';

			// Note
				print '<td align="left">';
				if ($_GET['action'] == 'editline' && $_GET['lineid'] == $task_time->rowid)
				{
					print '<textarea name="timespent_note_line" cols="40" rows="'.ROWS_2.'">'.$task_time->note.'</textarea>';
				}
				else
				{
					print dol_nl2br($task_time->note);
				}
				print '</td>';

			// Time spent
			//print '<td align="right">';
			// if ($_GET['action'] == 'editline' && $_GET['lineid'] == $task_time->rowid)
			// {
			// 	print '<input type="hidden" name="old_duration" value="'.$task_time->task_duration.'">';
			// 	print $form->select_duration('new_duration',$task_time->task_duration,0,'text');
			// }
			// else
			// {
			// 	print convertSecondToTime($task_time->task_duration,'allhourmin');
			// }
			//print '</td>';
			//unidades declaradas
				$objdoc->fetch('',$task_time->rowid);
				print '<td align="right">';
				if ($objdoc->fk_task_time == $task_time->rowid)
				{
					$totaldeclared += $objdoc->unit_declared;
					if ($_GET['action'] == 'editline' && $_GET['lineid'] == $task_time->rowid)
					{
						print '<input type="number" min="0" step="any" name="unit_declared" value="'.$objdoc->unit_declared.'">';
					}
					else
						print price($objdoc->unit_declared);
				}
				else
				{
					if ($_GET['action'] == 'editline' && $_GET['lineid'] == $task_time->rowid)
					{
						print '<input type="number" min="0" step="any" name="unit_declared" value="">';
					}
					else
						print '&nbsp;';
				}
				print '</td>';

			//photo
				print '<td align="center" nowrap class="SI-FILES-STYLIZED">';
			//$objdoc->fetch('',$task_time->rowid);
				if ($objdoc->fk_task_time == $task_time->rowid &&
					!empty($objdoc->document))
				{
				//recuperamos los nombres de archivo
					$aDoc = explode(';',$objdoc->document);
					foreach ((array) $aDoc AS $k => $doc)
					{
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

						if ($action != 'editlinep')
						{
							//print '&nbsp;'.$mobject->showphoto($typedoc,$task_time,$doc,$object,$projectstatic, 100,$docext);
							$modulepart = 'projet';
							print '&nbsp;'.$mobject->showphoto($typedoc,$doc,$task_time,$modulepart, $object,$projectstatic, 100, 0, 0, 'photowithmargin', 'small', 1, 0,$docext);
							if ($user->rights->monprojet->timed->del)
								print '&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&linedoc='.$objdoc->id.'&namedoc='.$doc.'&action=deldoc'.'">'.img_picto($langs->trans('Deleteattachment'),'edit_remove').'</a>';

						}
					}
					if ($action != 'editlinep')
					{
				//revisar permiso
						if ($lregtask)
							print '&nbsp;&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&lineid='.$task_time->rowid.'&action=editlinep'.'">'.img_picto($langs->trans('Newdoc'),'edit_add').'</a>';
					}
				}
				else
				{
					print '&nbsp;';
					if ($action != 'editlinep')
					{
						if ($lregtask)
							print '&nbsp;&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&lineid='.$task_time->rowid.'&action=editlinep'.'">'.img_picto($langs->trans('Newdoc'),'edit_add').'</a>';
					}
				}
			//para subir nuevo archivo
				if ($action == 'editlinep'  && $_GET['lineid'] == $task_time->rowid)
				{
					print '<label class="cabinet">';

					include DOL_DOCUMENT_ROOT.'/monprojet/task/tpl/adddoc.tpl.php';
					print '</label>';
				}

				print '</td>';

			// Edit and delete icon
				print '<td align="center" valign="middle" width="80">';
				if ($action == 'editline' && $_GET['lineid'] == $task_time->rowid)
				{
					print '<input type="hidden" name="lineid" value="'.$_GET['lineid'].'">';
					print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
					print '<br>';
					print '<input type="submit" class="button" name="cancel" value="'.$langs->trans('Cancel').'">';
				}
				elseif ($action == 'editlinep' && $_GET['lineid'] == $task_time->rowid)
				{
					print '<input type="hidden" name="lineid" value="'.$_GET['lineid'].'">';
					print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
					print '<br>';
					print '<input type="submit" class="button" name="cancel" value="'.$langs->trans('Cancel').'">';
				}
				else if ($user->rights->monprojet->timed->mod && $object->fk_statut < 2)
				{
					print '&nbsp;';
					print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$task_time->rowid.($withproject?'&amp;withproject=1':'').'">';
					print img_edit();
					print '</a>';

				}
				if ($action != 'editlinep' && $user->rights->monprojet->timed->del && $object->fk_statut < 2)
				{

					print '&nbsp;';
					print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=deleteline&amp;lineid='.$task_time->rowid.($withproject?'&amp;withproject=1':'').'">';
					print img_delete();
					print '</a>';
				}
				print '</td>';

				print "</tr>\n";
				$total += $task_time->task_duration;
			}
			print '<tr class="liste_total"><td colspan="3" class="liste_total">'.$langs->trans("Total").'</td>';
			print '<td align="right" class="nowrap liste_total">'.price($totaldeclared).'</td><td>&nbsp;</td>';
		//		print '<td align="right" class="nowrap liste_total">'.convertSecondToTime($total,'allhourmin').'</td><td>&nbsp;</td>';
			print '</tr>';

			print "</table>";
			print "</form>";
		}
	}
}


llxFooter();
$db->close();
