<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/projet/budget.php
 *	\ingroup    projet
 *	\brief      List all budget of a item
 */
require ("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
//budget
if ($conf->budget->enabled)
{
	//require_once DOL_DOCUMENT_ROOT.'/budget/class/html.formadd.class.php';	
	require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/cunits.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructure.class.php';
	dol_include_once('/budget/class/typeitem.class.php');
}
else
{
	return '';
}
//product
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/productadd.class.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/tabs.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/utils.lib.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetproductassociation.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/productadd.class.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formotheradd.class.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpayment.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskpayment.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

//request
if ($conf->request->enabled)
{
	dol_include_once('/request/class/request.class.php');
	dol_include_once('/request/class/requestitem.class.php');
}

$langs->load("users");
$langs->load("projects");
$langs->load("monprojet@monprojet");
$langs->load("request@request");

$action = GETPOST('action', 'alpha');
$id = GETPOST('id', 'int');
$idpay = GETPOST('idpay', 'int');
$ref = GETPOST('ref', 'alpha');
$backtopage=GETPOST('backtopage','alpha');
$cancel=GETPOST('cancel');
$confirm=GETPOST('confirm');
$newsel=GETPOST('newsel', 'int');
$selstatut=GETPOST('selstatut','int')+0;
if (isset($_GET['selstatut']))
	$_SESSION['selstatut'] = $selstatut+0;

$selstatut = $_SESSION['selstatut'];

$mode = GETPOST('mode', 'alpha');
$mine = ($mode == 'mine' ? 1 : 0);

$table = 'llx_projet_task';
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
//armamos los campos obligatorios
$aHeaderTpl['llx_projet_task'] = array('ref' => 'ref',
	'label' => 'label',
	'hilo' => 'hilo',
	'login' => 'login',
	'fechaini'=>'fechaini',
	'fechafin'=>'fechafin',
	'group'=>'group',
	'type'=>'type',
	'typename'=>'typename',
	'unitprogram'=>'unitprogram',
	'unit'=>'unit',
	'price' => 'price',);
$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');

$aCampodate = array('fechaini' =>'date_start',
	'fechafin' => 'date_end');

//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$object    = new Projectext($db);
$objectadd = new Projectext($db);
$objecttaskadd = new Projettaskadd($db);
$objecttime = new Projettasktimedoc($db);
//productos
$projetprod = new Projetproduct($db);
$ppass = new Projetproductassociation($db);

//regisro de avances
$taskstatic = new Task($db);
$taskadd = new Taskext($db);
 //nueva clase para listar tareas
$objpay  = new Projetpayment($db);
$taskpay = new Projettaskpayment($db);
$objuser = new User($db);
$cunits  = new Cunits($db);
//request
$request = new Request($db);
$requestitem = new Requestitem($db);
//priceunit
$typeitem = new Typeitem($db);

$extrafields_project = new ExtraFields($db);
$extrafields_task = new ExtraFields($db);
$items = new Items($db);
$pustr = new Pustructure($db);
$product = new Productadd($db);

include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once

if ($id > 0 || ! empty($ref))
{
	// fetch optionals attributes and labels
	$extralabels_projet=$extrafields_project->fetch_name_optionals_label($object->table_element);
}
$extralabels_task=$extrafields_task->fetch_name_optionals_label($taskstatic->table_element);
// Security check
$socid=0;
if ($user->societe_id > 0) $socid = $user->societe_id;
if (!$user->rights->monprojet->bud->crear)
	$result = restrictedArea($user, 'projet&monprojet', $id);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('projecttaskcard','globalcard','formObjectOptions'));

$progress=GETPOST('progress', 'int');
$label=GETPOST('label', 'alpha');
$description=GETPOST('description');
$planned_workload=GETPOST('planned_workloadhour')*3600+GETPOST('planned_workloadmin')*60;

$userAccess=0;
$lDisabled = false;
$filterpay = false;

//modifica las fechas de tareas grupo
updatedategroup($id);
if ($user->admin && $conf->global->MONPROJET_ACTUALIZA_ADVANCE && GETPOST('actadv'))
{
	updatetaskadvance($id);
}
//exit;
if ($_GET['upitem'] == 'uup' && $user->admin)
{
	$object->fetch($id);
	$aTasknumref = unserialize($_SESSION['aTasknumref'][$object->id]);
	if ($object->id == $id && count($aTasknumref)>0)
	{
		$fk_projet = $object->id;
		include DOL_DOCUMENT_ROOT.'/monprojet/lib/update_orderref.lib.php';
	}
}
//actualiza($object,$extralabels_task,$objecttaskadd);

/*
 * Actions
 */

if ($user->rights->monprojet->bud->crear)
	$userWrite = true;

if ($action == 'confirm_verifpay' && ($_REQUEST["confirm"] == 'no' || GETPOST('cancel'))) $action='paymen';

//addsupplies
//importar insumos de base de datos principal
if ($action == 'confirm_addsupplies' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->bud->crear)
{
	$product = new Productadd($db);
	$pp = new Projetproduct($db);
	$filter = array(1=>1);
	//items
	$filterstatic = " AND t.entity = ".$conf->entity;
	$filtergroup = " AND c.fk_categorie = ".$conf->global->PRICEUNITS_CODE_ITEM_DEF;
	$res = $product->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic,false,$filtergroup);
	$db->begin();
	if ($res > 0)
	{
		$lines = $product->lines;
		$num = count($lines);
		for ($i=0;$i<$num;$i++)
		{
			//buscamos
			$pp->initAsSpecimen();
			$ress = $pp->fetch('',$id,$lines[$i]->ref);
			if(empty($ress))
			{
				$pp->fk_projet = $id;
				$pp->fk_product = $lines[$i]->id;
				$pp->ref = $lines[$i]->ref;
				$pp->ref_ext = $lines[$i]->ref_ext;
				$pp->datec = $lines[$i]->datec;
				$pp->fk_parent = $lines[$i]->fk_parent;
				$pp->fk_categorie = $conf->global->PRICEUNITS_CODE_ITEM_DEF;
				$pp->label = $lines[$i]->label;
				$pp->description = $lines[$i]->description;
				$pp->fk_country = $lines[$i]->country_id;
				$pp->price = $lines[$i]->price;
				$pp->price_ttc = $lines[$i]->price_ttc;
				$pp->price_min = $lines[$i]->price_min;
				$pp->price_min_ttc = $lines[$i]->price_min_ttc;
				$pp->price_base_type = $lines[$i]->price_base_type;
				$pp->tva_tx = $lines[$i]->tva_tx;
				$pp->pmp = $lines[$i]->pmp;
				$pp->status = $lines[$i]->status;
				$pp->finished = $lines[$i]->finished;
				$pp->date_creation = dol_now();
				$pp->recuperableonly=$lines[$i]->recuperableonly+0;
				$pp->date_modification = $lines[$i]->date_modification;
				$pp->localtax1_tx = $lines[$i]->localtax1_tx;
				$pp->localtax1_type = $lines[$i]->localtax1_type+0;
				$pp->localtax2_tx = $lines[$i]->localtax2_tx;
				$pp->localtax2_type = $lines[$i]->localtax2_type+0;
				$pp->entity = $conf->entity;
				$pp->fk_product_type = $lines[$i]->fk_product_type;
				$pp->fk_unit = $lines[$i]->fk_unit;
				$pp->cost_price = $lines[$i]->cost_price;
				$pp->fk_user_author = $user->id;
				$pp->fk_user_modif = $user->id;

				$res = $pp->create($user);
				if ($res<=0)
				{
					$error++;
					echo 'err item ';
					exit;
				}
			}
		}
	}
	//precios unitarios structura
	$filterstatic = " AND t.entity = ".$conf->entity;
	$filtergroup = " AND c.fk_projet = ".$id;
	$filtergroup = " AND c.fk_categorie > 0 ";
	$res = $pustr->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic,false);
	if ($res>0)
	{
		foreach($pustr->lines AS $j => $linepu)
		{
			$filter = array(1=>1);
			//insumos
			$filterstatic = " AND t.entity = ".$conf->entity;
			$filtergroup = " AND c.fk_categorie = ".$linepu->fk_categorie;
			$res = $product->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic,false,$filtergroup);
			if ($res > 0)
			{
				$lines = $product->lines;
				$num = count($lines);
				for ($i=0;$i<$num;$i++)
				{
					//buscamos
					$pp->initAsSpecimen();
					$ress = $pp->fetch('',$id,$lines[$i]->ref);
					if(empty($ress))
					{
						$pp->fk_projet = $id;
						$pp->fk_product = $lines[$i]->id;
						$pp->ref = $lines[$i]->ref;
						$pp->ref_ext = $lines[$i]->ref_ext;
						$pp->datec = $lines[$i]->datec;
						$pp->fk_parent = $lines[$i]->fk_parent;
						$pp->fk_categorie = $linepu->fk_categorie;
						$pp->label = $lines[$i]->label;
						$pp->description = $lines[$i]->description;
						$pp->fk_country = $lines[$i]->country_id;
						$pp->price = $lines[$i]->price;
						$pp->price_ttc = $lines[$i]->price_ttc;
						$pp->price_min = $lines[$i]->price_min;
						$pp->price_min_ttc = $lines[$i]->price_min_ttc;
						$pp->price_base_type = $lines[$i]->price_base_type;
						$pp->tva_tx = $lines[$i]->tva_tx;
						$pp->pmp = $lines[$i]->pmp;
						$pp->status = $lines[$i]->status;
						$pp->finished = $lines[$i]->finished;
						$pp->date_creation = dol_now();
						$pp->recuperableonly=$lines[$i]->recuperableonly+0;
						$pp->date_modification = $lines[$i]->date_modification;
						$pp->localtax1_tx = $lines[$i]->localtax1_tx;
						$pp->localtax1_type = $lines[$i]->localtax1_type+0;
						$pp->localtax2_tx = $lines[$i]->localtax2_tx;
						$pp->localtax2_type = $lines[$i]->localtax2_type+0;
						$pp->entity = $conf->entity;
						$pp->fk_product_type = $lines[$i]->fk_product_type;
						$pp->fk_unit = $lines[$i]->fk_unit;
						$pp->cost_price = $lines[$i]->cost_price;
						$pp->fk_user_author = $user->id;
						$pp->fk_user_modif = $user->id;

						$resp = $pp->create($user);
						if ($resp<=0)
						{
							$error++;
							echo 'err inss ';

							exit;
						}
					}
				}
			}			
		}
	}
	if(!$error)
		$db->commit();
	else
		$db->rollback();
	$action='supplies';
}
if ($action == 'confirm_addcopysupplies' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->bud->crear)
{
	$selectins = unserialize(($_SESSION['selectins']));
	$aProductact = unserialize($_SESSION['aProductcat']);
	$selectins = implode(',',$selectins);
	$product = new Productadd($db);
	$pp = new Projetproduct($db);
	$filter = array(1=>1);
			//insumos
	$filterstatic = " AND t.entity = ".$conf->entity;
	$filterstatic.= " AND t.rowid IN (".$selectins.")";
	$res = $product->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic,false);
	if ($res > 0)
	{
		$lines = $product->lines;
		$num = count($lines);
		for ($i=0;$i<$num;$i++)
		{
					//buscamos
			$pp->initAsSpecimen();
			$ress = $pp->fetch('',$id,$lines[$i]->ref);
			if(empty($ress))
			{
				$fk_categorie = $aProductact[$lines[$i]->id];
				$pp->fk_projet = $id;
				$pp->fk_product = $lines[$i]->id;
				$pp->ref = $lines[$i]->ref;
				$pp->ref_ext = $lines[$i]->ref_ext;
				$pp->datec = $lines[$i]->datec;
				$pp->fk_parent = $lines[$i]->fk_parent;
				$pp->fk_categorie = $fk_categorie;
				$pp->label = $lines[$i]->label;
				$pp->description = $lines[$i]->description;
				$pp->fk_country = $lines[$i]->country_id;
				$pp->price = $lines[$i]->price;
				$pp->price_ttc = $lines[$i]->price_ttc;
				$pp->price_min = $lines[$i]->price_min;
				$pp->price_min_ttc = $lines[$i]->price_min_ttc;
				$pp->price_base_type = $lines[$i]->price_base_type;
				$pp->tva_tx = $lines[$i]->tva_tx;
				$pp->pmp = $lines[$i]->pmp;
				$pp->status = $lines[$i]->status;
				$pp->finished = $lines[$i]->finished;
				$pp->date_creation = dol_now();
				$pp->recuperableonly=$lines[$i]->recuperableonly+0;
				$pp->date_modification = $lines[$i]->date_modification;
				$pp->localtax1_tx = $lines[$i]->localtax1_tx;
				$pp->localtax1_type = $lines[$i]->localtax1_type+0;
				$pp->localtax2_tx = $lines[$i]->localtax2_tx;
				$pp->localtax2_type = $lines[$i]->localtax2_type+0;
				$pp->entity = $conf->entity;
				$pp->fk_product_type = $lines[$i]->fk_product_type;
				$pp->fk_unit = $lines[$i]->fk_unit;
				$pp->cost_price = $lines[$i]->cost_price;
				$pp->fk_user_author = $user->id;
				$pp->fk_user_modif = $user->id;

				$resp = $pp->create($user);
				if ($resp<=0)
				{
					$error++;
					echo 'err ins ';
					exit;
				}
			}
		}
	}
	if(!$error)
		$db->commit();
	else
		$db->rollback();
	$action='supplies';
}

//additem
if ($action == 'confirm_addcopyitem' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->bud->crear)
{
	$selectins = unserialize(($_SESSION['selectins']));
	$aProductact = unserialize($_SESSION['aProductcat']);
	$selectins = implode(',',$selectins);
	$product = new Productadd($db);
	$pp = new Projetproduct($db);
	$filter = array(1=>1);
			//insumos
	$filterstatic = " AND t.entity = ".$conf->entity;
	$filterstatic.= " AND t.rowid IN (".$selectins.")";
	$res = $product->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic,false);
	if ($res > 0)
	{
		$lines = $product->lines;
		$num = count($lines);
		for ($i=0;$i<$num;$i++)
		{
					//buscamos
			$pp->initAsSpecimen();
			$ress = $pp->fetch('',$id,$lines[$i]->ref);
			if(empty($ress))
			{
				$fk_categorie = $aProductact[$lines[$i]->id];
				$pp->fk_projet = $id;
				$pp->fk_product = $lines[$i]->id;
				$pp->ref = $lines[$i]->ref;
				$pp->ref_ext = $lines[$i]->ref_ext;
				$pp->datec = $lines[$i]->datec;
				$pp->fk_parent = $lines[$i]->fk_parent;
				$pp->fk_categorie = $fk_categorie;
				$pp->label = $lines[$i]->label;
				$pp->description = $lines[$i]->description;
				$pp->fk_country = $lines[$i]->country_id;
				$pp->price = $lines[$i]->price;
				$pp->price_ttc = $lines[$i]->price_ttc;
				$pp->price_min = $lines[$i]->price_min;
				$pp->price_min_ttc = $lines[$i]->price_min_ttc;
				$pp->price_base_type = $lines[$i]->price_base_type;
				$pp->tva_tx = $lines[$i]->tva_tx;
				$pp->pmp = $lines[$i]->pmp;
				$pp->status = $lines[$i]->status;
				$pp->finished = $lines[$i]->finished;
				$pp->date_creation = dol_now();
				$pp->recuperableonly=$lines[$i]->recuperableonly+0;
				$pp->date_modification = $lines[$i]->date_modification;
				$pp->localtax1_tx = $lines[$i]->localtax1_tx;
				$pp->localtax1_type = $lines[$i]->localtax1_type+0;
				$pp->localtax2_tx = $lines[$i]->localtax2_tx;
				$pp->localtax2_type = $lines[$i]->localtax2_type+0;
				$pp->entity = $conf->entity;
				$pp->fk_product_type = $lines[$i]->fk_product_type;
				$pp->fk_unit = $lines[$i]->fk_unit;
				$pp->cost_price = $lines[$i]->cost_price;
				$pp->fk_user_author = $user->id;
				$pp->fk_user_modif = $user->id;

				$resp = $pp->create($user);
				if ($resp<=0)
				{
					$error++;
					echo 'err ins ';
					exit;
				}
			}
		}
	}
	if(!$error)
		$db->commit();
	else
		$db->rollback();
	$action='supplies';
}





//borrar
if ($action == 'confirm_verifpay' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->payp->pay)
{
	$object->fetch($id);

	//generar planilla
	$_POST = $_SESSION['aPost'];
	//este proceso actualiza el time_doc con el pago
	$res = $objpay->fetch(GETPOST('idpay'));
	if ($res>0 && $objpay->id == $idpay)
	{
		$_POST = $_SESSION['aPost'];
		$error = 0;
		//iniciamos
		$db->begin();
		$objpay->ref = $_POST['ref']+0;
		if (!$objpay->ref > 0) $error++;
		$objpay->date_payment = dol_mktime($_POST['dohour'],$_POST['domin'],0,$_POST['domonth'],$_POST['doday'],$_POST['doyear'],'user');
		$objpay->amount = $_POST['amount'];
		$objpay->statut = 2;
		$objpay->detail = GETPOST('detail');
		//validado
		if (!$error)
		{
			$res = $objpay->update($user);
			if (!$res>0) $error++;
		}
		if (!$error)
		{
			//vamos cambiando de estado en taskpay, time_doc
			$aSelpay = $_SESSION['aSelectlast'];
			$statut = 3;
			foreach ((array) $aSelpay AS $fk_pay => $value)
			{
				//buscamos y cambiamos de estado 1 a 2
				$res = $taskpay->fetch($fk_pay);
				if ($res>0 && $taskpay->id == $fk_pay)
				{
					//primero actualizamos la tabla time_doc
					$filter = array(1=>1);
					$filterstatic = " AND p.fk_task = ".$taskpay->fk_task;
					$filterstatic.= " AND t.statut = 1 ";
					$filterstatic.= " AND t.date_create <= ".$db->idate($objpay->date_payment);
					$numdoc = $objecttime->fetchAlltime('', '', 0, 0, $filter,'AND',$filterstatic,false);
					$objdocnew = new Projettasktimedoc($db);
					foreach((array) $objecttime->lines AS $j => $line)
					{
						$resu = $objdocnew->fetch($line->id);
						if ($resu>0 && $objdocnew->id == $line->id)
						{
							$objdocnew->fk_task_payment = $fk_pay;
							$objdocnew->statut = 2;
							$resn = $objdocnew->update($user);
							if (!$resn > 0) $error++;
						}
						else
							$error++;
					}
					//cambiamos de estado
					if (!$error)
					{
						$taskpay->statut = $statut;
						$res = $taskpay->update($user);
						if (!$res>0) $error++;
					}
				}
				else
					$error++;
			}
		  	//foreach aselpay
		}

		if (!$error)
		{
			$db->commit();
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=paymen');
			exit;
		}
		else $db->rollback();
	}
	$action = 'paymen';
}

if ($action == 'confirm_deletetask' && GETPOST('cancel')) $action='list';
//action giveback
if ($action == 'confirm_deletetask' && $_REQUEST["confirm"] == 'yes' && $user->rights->monprojet->task->del)
{
	$objectadd->fetch($id);
	if ($objectadd->id == $id && $object->statut == 0)// activar para validar borrado
	{
		$res = $objectadd->delete_task($user);
		if (!$res > 0)
			$error++;
		if (!$error)
		{
			$db->commit();
			header("Location: ".DOL_URL_ROOT.'/monprojet/tasks.php?id='.$id);
			exit;
		}
	}
	else
	{
		setEventMessages(null,$langs->trans("Errordonotexistproject"),'errors');
	}
	$action = 'list';
}

if ($action == 'selpay' && (isset($_REQUEST['paydis']) || isset($_REQUEST['payapp'])))
{
	$error = 0;
	$aSelpay = GETPOST('selpay');
	$_SESSION['aSelectlast'] = $aSelpay;
	if (empty($aSelpay))
		setEventMessages(null,$langs->trans("Errordonotselectanytasks"),'errors');

	$db->begin();
	foreach ((array) $aSelpay AS $fk_pay => $value)
	{
		if ($_REQUEST['paydis']) $statut = 1;
		if ($_REQUEST['payapp']) $statut = 2;
		if ($_REQUEST['paycreate']) $statut = 3;
		//buscamos y cambiamos de estado 1 a 2
		$res = $taskpay->fetch($fk_pay);
		if ($res>0 && $taskpay->id == $fk_pay)
		{
			$taskpay->statut = $statut;
			$res = $taskpay->update($user);
			if (!$res>0) $error++;
		}
		else
			$error++;
	}
	// //$res = $objecttime->update_approve($fk_task,$statut);
	// $res = $taskpay->update_approve($fk_task,$statut);
	if (!$error)
	{
		$db->commit();
	}
	else
	{
		$db->rollback();
		setEventMessage($langs->trans("Errorapprove",$langs->transnoentitiesnoconv("Items")),'errors');

	}
	$action = 'paymen';
}

if ($action == 'selpay' && isset($_REQUEST['paycreate']))
{
	$aSelpay = GETPOST('selpay');
	$_SESSION['aSelectlast'] = $aSelpay;
	if (empty($aSelpay))
	{
		setEventMessages(null,$langs->trans("Errordonotselectanytasks"),'errors');
		$action = 'paymen';
	}
	else
	{
		$_SESSION['aSelpay'] = GETPOST('selpay');
	//procesamos el pago
		$filterpay = true;
		$action = 'createpay';
	}
}


if ($action == 'addup')
{
	//MODIFICADO
	$error = 0;
	//buscamos el projet
	$res = $object->fetch($id);
	if ($res<=0) $error++;
	//recuperamos el aTasknumref
	$aTasknumref = unserialize($_SESSION['aTasknumref'][$object->id]);
	//revisamos si existe tareas en el proyecto

	$aArrData = $_SESSION['aArrData'];
	$table = GETPOST('table');
	$aNewTask = array();
	$aLevel = array();
	$lUtility = true;
	$db->begin();
	foreach ((array) $aArrData AS $i => $data)
	{
		//vamos verificando la existencia de cada uno
		$fk_task_parent = 0;
		if (!empty($data['hilo']))
		{
			if (!empty($aNewTask[$data['hilo']])) $fk_task_parent = $aNewTask[$data['hilo']];
			else $error++;
		}

		//unit
		$fk_unit = 0;
		if (!empty($data['unit']))
		{
			$cunits->fetch('',$data['unit']);
			if (STRTOUPPER($cunits->code) == STRTOUPPER($data['unit']))
			{
				//recuperamos el id de registro
				$fk_unit = $cunits->id;
			}
			else
			{
				//creamos
				$cunits->initAsSpecimen();
				$cunits->code= $data['unit'];
				$cunits->label= $data['unitlabel'];
				$cunits->short_label= $data['unit'];
				$cunits->active= 1;
				$resunit = $cunits->create($user);
				if ($resunit >0) $fk_unit = $resunit;
				else $error++;
			}
		}
		//verificamos si esta relacionado a un item
		$fk_item = 0;
		if (!empty($data['item']))
		{
			//buscamos
			$resitem = $items->fetch('',$data['item']);
			if ($resitem>0)
			{
				if (STRTOUPPER($items->ref) == STRTOUPPER($data['item']))
				{
					$_POST['options_fk_item'] = $items->id;
					$fk_item = $items->id;
				}
			}
			else
			{
				$error++;
			}
		}
		else
			$_POST['options_fk_item'] = 0;

		//verificamos tipo item
		$fk_type_item = 0;
		if (!empty($data['type']))
		{
			//buscamos
			$restype = $typeitem->fetch('',$data['type']);
			if ($restype>0)
			{
				if (STRTOUPPER($typeitem->ref) == STRTOUPPER($data['type']))
				{
					$_POST['options_fk_type'] = $typeitem->id;
					$fk_type_item = $typeitem->id;
				}
			}
			elseif($restype==0)
			{
				//agregamos
				$typeitem->entity = $conf->entity;
				$typeitem->ref = $data['type'];
				$typeitem->detail = $data['typename'];
				$typeitem->fk_user_create = $user->id;
				$typeitem->fk_user_mod = $user->id;
				$typeitem->date_create = dol_now();
				$typeitem->tms = dol_now();
				$typeitem->statut = 1;
				$rest = $typeitem->create($user);
				if ($rest<=0) $error++;
				else
				{
					$fk_type_item = $rest;
					$_POST['options_fk_type'] = $rest;
				}
			}
			else
			{
				$error++;
			}
		}
		else
			$_POST['options_fk_type'] = 0;
		if ($error)
		{
			if ($lUtility)
			{
				$lUtility = false;
				setEventMessage($langs->trans("Errorutilities",$langs->transnoentitiesnoconv("Items")),'errors');
			}
		}
		//verificamos las fechas
		$date_start = getformatdate($seldate,$data['fechaini']);
		$date_end   = getformatdate($seldate,$data['fechafin']);

		//buscamos si existe la tarea
		$tasknewadd = new Taskext($db);
		$filter = array(1=>1);
		$filterstatic = " AND t.ref = '".$data['ref']."'";
		$filterstatic.= " AND t.fk_projet = ".$object->id;
		$res = $tasknewadd->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
		if ($res>0)
		{
			foreach ($tasknewadd->lines AS $k => $obj)
			{
				if (STRTOUPPER($obj->ref) == STRTOUPPER($data['ref']) &&
					$obj->fk_project == $object->id)
				{
					$task = new Task($db);
					if ($task->fetch($obj->id)>0)
					{
						//buscamos si existe el campo con formato fecha
						foreach((array) $aCampodate AS $k => $value)
						{
							if($data[$k])
							{
								//verificamos y damos formato a la variable
								$resvalue = convertdate($aDatef,$seldate,$data[$k]);
								$task->$value = $resvalue;
							}
						}

						$aNewTask[$data['ref']] = $task->id;
						//actualizamos el valor
						$_POST['options_c_grupo'] = $data['group'];
						$_POST['options_c_view'] = $data['view'];
						$_POST['options_unit_program'] = $data['unitprogram'];
						$_POST['options_fk_unit'] = $fk_unit;
						$_POST['options_unit_amount'] = $data['price'];
						//$task->dateo = $date_start;
						//$task->datee = $date_end;
						$task->fk_task_parent = $fk_task_parent +0;
						$task->ref = $data['ref'];
						$task->label = $data['label'];
						$task->description = $data['detail'];
						$task->priority = $data['priority']+0;
						$task->rang = $i;
						$task->tms = dol_now();
						if (empty($fk_task_parent)) $level = 0;
						else $level = $aLevel[$fk_task_parent]+1;
						$aLevel[$task->id] = $level;
						// Fill array 'array_options' with data from add form
						$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);
						$aTasknumref[$task->id] = array('fk_task_parent'=>$fk_task_parent,'ref'=> $task->ref,'level'=>$level,'reg'=>$i,'group'=>$data['group']);
						if (!$ret > 0) $error++;
						//actualizamos datos adicionales de la tarea
						$res = $objecttaskadd->fetch('',$task->id);
						if ($res>0 && $objecttaskadd->fk_task == $task->id)
						{
							$objecttaskadd->fk_item = $fk_item;
							$objecttaskadd->fk_type_item = $fk_type_item;
							$objecttaskadd->c_grupo = $data['group'];
							$objecttaskadd->level = $level;
							$objecttaskadd->unit_program = $data['unitprogram']+0;
							$objecttaskadd->fk_unit = $fk_unit;
							$objecttaskadd->unit_amount = $data['price']+0;
							$objecttaskadd->fk_user_mod = $user->id;
							$objecttaskadd->tms = dol_now();
							$objecttaskadd->detail_close = '';
							$res = $objecttaskadd->update($user);
							if ($res<=0) $error++;
						}
						else
						{
							$objecttaskadd->fk_task = $task->id;
							$objecttaskadd->fk_item = $fk_item;
							$objecttaskadd->fk_type_item = $fk_type_item;
							$objecttaskadd->c_grupo = $data['group'];
							$objecttaskadd->level = $level;
							$objecttaskadd->unit_program = $data['unitprogram']+0;
							$objecttaskadd->fk_unit = $fk_unit;
							$objecttaskadd->unit_amount = $data['price']+0;
							$objecttaskadd->fk_user_create = $user->id;
							$objecttaskadd->fk_user_mod = $user->id;
							$objecttaskadd->date_create = dol_now();
							$objecttaskadd->tms = dol_now();
							$objecttaskadd->statut = 1;
							$res = $objecttaskadd->create($user);
							if ($res<=0) $error++;
						}
						if (!$error)
						{

							$resup = $task->update($user,true);
							if ($resup<=0) $error++;
						}
					}
				}
			}
		}
		else
		{
			//creamos nuevo
			$_POST['options_c_grupo'] = $data['group'];
			$_POST['options_c_view'] = $data['view'];
			$_POST['options_unit_program'] = $data['unitprogram'];
			$_POST['options_fk_unit'] = $fk_unit;
			$_POST['options_unit_amount'] = $data['price'];
			$task = new Task($db);
			$task->initAsSpecimen();
			//buscamos si existe el campo con formato fecha
			foreach((array) $aCampodate AS $k => $value)
			{
				if($data[$k])
				{
					//verificamos y damos formato a la variable
					$resvalue = convertdate($aDatef,$seldate,$data[$k]);
					$task->$value = $resvalue;
				}
			}

			$task->entity = $conf->entity;
			$task->fk_project = $id;
			$task->fk_task_parent = $fk_task_parent +0;
			$task->ref = $data['ref'];
			$task->label = $data['label'];
			$task->description = $data['detail'];
			$task->fk_user_creat = $user->id;
			$task->priority = $data['priority']+0;
			$task->fk_statut = 1;
			$task->date_c = dol_now();
			$task->tms = dol_now();

			// Fill array 'array_options' with data from add form
			$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);
			//echo '<hr>new '.
			$result = $task->create($user,1);
			if ($result<=0) $error++;
			if ($result>0)
			{
				if (empty($fk_task_parent))
					$level = 0;
				else
					$level = $aLevel[$fk_task_parent]+1;
				$aLevel[$result] = $level;
			}
			$aTasknumref[$result] = array('fk_task_parent'=>$fk_task_parent,'ref'=>$data['ref'],'level'=>$level,'reg'=>$i,'group'=>$data['group']);

			if (!$error)
			{
				$objecttaskadd->fk_task = $result;
				$objecttaskadd->c_grupo = $data['group'];
				$objecttaskadd->level = $level;
				$objecttaskadd->unit_program = $data['unitprogram']+0;
				$objecttaskadd->fk_item = $fk_item;
				$objecttaskadd->fk_type_item = $fk_type_item;
				$objecttaskadd->fk_unit = $fk_unit;
				$objecttaskadd->unit_amount = $data['price']+0;
				$objecttaskadd->fk_user_create = $user->id;
				$objecttaskadd->fk_user_mod = $user->id;
				$objecttaskadd->date_create = dol_now();
				$objecttaskadd->tms = dol_now();
				$objecttaskadd->statut = 1;
				//echo '<br>newadd '.
				$res = $objecttaskadd->create($user);
				if ($res<=0) $error++;
			}

			if (!$error)
			{
				//actualizamos la nueva tarea en el campo rang
				$taskstatic->fetch($result);
				if ($taskstatic->id == $result)
				{
					$taskstatic->rang = $i;
					$res = $taskstatic->update($user,true);
					if ($res<=0) $error++;
				}

				$aNewTask[$data['ref']] = $result;

				//buscamos al usuario contacto que es un array
				$aLogin = explode(';',$data['login']);
				foreach ((array) $aLogin AS $l => $login)
				{
					$resuser = $objuser->fetch('',$login);
					if ($resuser>0)
						$result = $task->add_contact($objuser->id, 'TASKEXECUTIVE', 'internal');
				}
			}
			else
			{
				$error++;
				setEventMessages($task->error,$task->errors,'errors');
			}
		}
	}
	if (empty($error))
	{
		//ordenamos y actualizamos
		$aRef = array();
		$aNumberref = array();
		$aRefnumber = array();
		foreach((array) $aTasknumref AS $i => $data)
		{
		//verificamos el orden donde debe estar la tarea
			list($aRef,$aNumberref,$aRefnumber) = get_orderlastnew($i,$object->id,$data,$aRef,$aNumberref,$aRefnumber);
		}
		foreach ((array) $aNumberref AS $i => $value)
		{
			$objecttaskadd->fetch('',$i);
			if ($objecttaskadd->fk_task == $i)
			{
				//echo '<br>'.$i.' '.$value;
				$objecttaskadd->order_ref = $value;
				$res = $objecttaskadd->update_orderref();
				if ($res < 0) $error++;
			}
			else
			{
				$error++;
				//echo '<br>no encuentra '.$i;
			}
		}
	}
	//ordenamos las tareas por el order_ref
	if (empty($error))
	{
		//echo '<hr>antes de actualizar el order '.$error;
		$tasknewadd->get_ordertask($object->id);
		$taskaddnew = new Taskext($db);
		//echo '<br>cuentalines '.count($taskadd->lines).' del id '.$projectstatic->id;
		if (count($tasknewadd->lines)>0)
		{
			$j = 1;
			foreach($tasknewadd->lines AS $i => $data)
			{
				$fk = $data->id;
				$res = $taskaddnew->fetch($fk);
				if ($res >0)
				{
					//echo '<br>procesando el reemplazo a '.$j .' de '.$taskstatic->rang.' delid '.$data->id.'|'.$fk.' encontrado |'.$taskaddnew->id.'|';
					$taskaddnew->rang = $j;
					$res = $taskaddnew->update_rang($user);
					if ($res <= 0) $error++;
					$j++;
				}
				else
					$error++;
			}
		}
	}
	if (empty($error))
	{
		$db->commit();
	}
	else
	{
		setEventMessage($langs->trans("Errorupload",$langs->transnoentitiesnoconv("Items")),'errors');
		$db->rollback();
	}
	$action = 'list';
	//echo '<br>action '.$action;
}

// Part to create upload
if ($action == 'veriffile')
{
	//verificacion
	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];
	$separator = GETPOST('separator','alpha');
	$tempdir = "tmp/";

	//    if(move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
	if($tmp_name && dol_move_uploaded_file($tmp_name, $tempdir.$nombre_archivo,1,10,0,$nombre_archivo))
	{
		//echo "file uploaded<br>";
	}
	else
	{
		echo 'no se puede mover';
		exit;
	}

	if (file_exists($tempdir.$nombre_archivo))
	{
		$csvfile = $tempdir.$nombre_archivo;
		$fh = fopen($csvfile, 'r');
		$headers = fgetcsv($fh);
		$aHeaders = explode($separator,$headers[0]);
		$data = array();
		$aData = array();
		while (! feof($fh))
		{
			$row = fgetcsv($fh,'','^');
			if (!empty($row))
			{
				$aData = explode($separator,$row[0]);
				$obj = new stdClass;
				$obj->none = "";
				foreach ($aData as $i => $value)
				{
					$key = $aHeaders[$i];
					if (!empty($key))
						$obj->$key = $value;
					else
						$obj->none = $value." xx";
				}
				$data[] = $obj;
			}
		}
		fclose($fh);

		$c=0;
		$action = "verifup";
	}
	else
	{
		echo 'no existe el archivo';
		exit;
	}
}

if ($action == 'createtask' && $user->rights->monprojet->bud->crear)
{
	//MODIFICADO
	$error=0;
	$date_start = dol_mktime($_POST['dateohour'],$_POST['dateomin'],0,$_POST['dateomonth'],$_POST['dateoday'],$_POST['dateoyear'],'user');
	$date_end = dol_mktime($_POST['dateehour'],$_POST['dateemin'],0,$_POST['dateemonth'],$_POST['dateeday'],$_POST['dateeyear'],'user');
	if (! $cancel)
	{
		$ref = GETPOST('ref','alpha');
		if (!empty($newsel))
		{
		//buscamos el item
			$items->fetch('',$ref);
			if ($items->ref == $ref)
			{
				$_POST['options_fk_unit'] = $items->fk_unit;
				$_POST['options_fk_item'] = $items->id;
				$label = $items->detail;
				$ref = $items->ref;
			}
			else
				$error++;
		}
		else
		{
			$ref='';
			$label = GETPOST('label','alpha');
		}
		//le damos un nuevo numero de referencia
		$obj = empty($conf->global->PROJECT_TASK_ADDON)?'mod_task_simple':$conf->global->PROJECT_TASK_ADDON;
		if (! empty($conf->global->PROJECT_TASK_ADDON) && is_readable(DOL_DOCUMENT_ROOT ."/core/modules/project/task/".$conf->global->PROJECT_TASK_ADDON.".php"))
		{
			require_once DOL_DOCUMENT_ROOT ."/core/modules/project/task/".$conf->global->PROJECT_TASK_ADDON.'.php';
			$modTask = new $obj;
			$ref = $modTask->getNextValue($soc,$object);
		}

		if (empty($label))
		{
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("Label")), 'errors');
			$action='create';
			$error++;
		}
		else if (empty($_POST['task_parent']))
		{
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("ChildOfTask")), 'errors');
			$action='create';
			$error++;
		}

		if (! $error)
		{
			$db->begin();
			$tmparray=explode('_',$_POST['task_parent']);
			$projectid=$tmparray[0];
			if (empty($projectid)) $projectid = $id;
		// If projectid is ''
			$task_parent=$tmparray[1];
			if (empty($task_parent)) $task_parent = 0;
		// If task_parent is ''

			$task = new Task($db);

			$task->fk_project = $projectid;
		//$task->ref = GETPOST('ref','alpha');
			$task->ref = $ref;
			$task->label = $label;
			$task->description = $description;
			$task->planned_workload = $planned_workload;
			$task->fk_task_parent = $task_parent;
			$task->date_c = dol_now();
			if (!$_POST['options_c_grupo'])
			{
				$task->date_start = $date_start;
				$task->date_end = $date_end;
				$task->progress = $progress+0;
			}
			else
				$task->date_start = dol_now();
		// Fill array 'array_options' with data from add form
			$ret = $extrafields_task->setOptionalsFromPost($extralabels_task,$task);

			$taskid = $task->create($user);
			if (!$taskid>0)
				$error++;
			if (!$error)
			{
				$objecttaskadd->fk_task = $taskid;
				$objecttaskadd->c_grupo = $_POST['options_c_grupo'];
				$objecttaskadd->c_view = $_POST['options_c_view'];
				$objecttaskadd->unit_program = $_POST['options_unit_program'];
				$objecttaskadd->unit_declared = 0;
				$objecttaskadd->fk_unit = $_POST['options_fk_unit'];
				$objecttaskadd->fk_type = $_POST['options_fk_type'];
				$objecttaskadd->unit_price = $_POST['options_unit_price'];
				$objecttaskadd->fk_user_create = $user->id;
				$objecttaskadd->fk_user_mod = $user->id;
				$objecttaskadd->date_create = dol_now();
				$objecttaskadd->tms = dol_now();
				$objecttaskadd->statut = 1;
				$res = $objecttaskadd->create($user);
				if (!$res>0)
					$error++;

			}
			if (!$error)
			{
				$result = $task->add_contact($_POST["userid"], 'TASKEXECUTIVE', 'internal');
			}
			else
			{
				setEventMessages($task->error,$task->errors,'errors');
			}
		}

		if (! $error)
		{
			$db->commit();
			if (! empty($backtopage))
			{
				header("Location: ".$backtopage);
				exit;
			}
			else if (empty($projectid))
			{
				header("Location: ".DOL_URL_ROOT.'/projet/tasks/index.php'.(empty($mode)?'':'?mode='.$mode));
				exit;
			}
			$id = $projectid;
		}
		else
			$db->rollback();
	}
	else
	{
		if (! empty($backtopage))
		{
			header("Location: ".$backtopage);
			exit;
		}
		else if (empty($id))
		{
		// We go back on task list
			header("Location: ".DOL_URL_ROOT.'/projet/tasks/index.php'.(empty($mode)?'':'?mode='.$mode));
			exit;
		}
	}
}

if ($action == 'createrefr')
{
	//require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	//$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);

	$tmparray['label'] = GETPOST('label');
	$tmparray['ref'] = GETPOST('ref');
	$tmparray['task_parent'] = GETPOST('task_parent');
	$tmparray['userid'] = GETPOST('userid');
	$tmparray['options_c_grupo'] = GETPOST('options_c_grupo');
	$tmparray['options_c_view'] = GETPOST('options_c_view');

	if ($tmparray['options_c_grupo'] == 1)
		$lDisabled = true;
	$options_c_grupo = $tmparray['options_c_grupo'];
	$options_c_view = $tmparray['options_c_view'];
	$taskstatic->ref = $tmparray['ref'];
	$label = $tmparray['label'];
	$taskstatic->task_parent = $tmparray['task_parent'];
	$task_parent = $tmparray['task_parent'];
	$userid = $tmparray['userid'];
	$taskstatic->speciality = $tmparray['speciality'];
	$action='create';
}

/*
 * View
 */

// Example : Adding jquery code

$form=new Formv($db);
//$formadd = new FormAdd($db);
$formother=new FormOther($db);
$taskstatic = new Task($db);
$userstatic=new User($db);

$title=$langs->trans("Project").' - '.$langs->trans("Tasks").' - '.$object->ref.' '.$object->name;
if (! empty($conf->global->MAIN_HTML_TITLE) && preg_match('/projectnameonly/',$conf->global->MAIN_HTML_TITLE) && $object->name) $title=$object->ref.' '.$object->name.' - '.$langs->trans("Tasks");
$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
llxHeader("",$title,$help_url);

if ($id > 0 || ! empty($ref))
{
	$object->fetch($id, $ref);
	$objectadd->fetch($id, $ref);
	$object->fetch_thirdparty();
	$res=$object->fetch_optionals($object->id,$extralabels_projet);
	$res=$objectadd->fetch_optionals($objectadd->id,$extralabels_projet);


	// To verify role of users
	//$userAccess = $object->restrictedProjectArea($user,'read');
	$userWrite  = $objectadd->restrictedProjectAreaadd($user,'write');
	//$userDelete = $object->restrictedProjectArea($user,'delete');
	//print "userAccess=".$userAccess." userWrite=".$userWrite." userDelete=".$userDelete;


	$tab=GETPOST('tab')?GETPOST('tab'):'Budget';

	$head=project_prepare_head($object);
	dol_fiche_head($head, $tab, $langs->trans("Project"),0,($object->public?'projectpub':'project'));

	$param=($mode=='mine'?'&mode=mine':'');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/projet/list.php">'.$langs->trans("BackToList").'</a>';

	// Ref
	print '<tr><td width="30%">';
	print $langs->trans("Ref");
	print '</td><td>';
	// Define a complementary filter for search of next/prev ref.
	if (! $user->rights->projet->all->lire)
	{
		$projectsListId = $object->getProjectsAuthorizedForUser($user,$mine,0);
		$object->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
	}
	print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref', '', $param);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Label").'</td><td>'.$object->title.'</td></tr>';

	//armando la estructura de proyectos



	// Other options
	$parameters=array();
	$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action); // Note that $action and $object may have been modified by hook
	if (empty($reshook) && ! empty($extrafields_project->attribute_label))
	{
		print $object->showOptionals($extrafields_project);
	}
	print '<tr><td>'.$langs->trans("Agenda").'</td><td>';
	print '<a class="button" href="'.DOL_URL_ROOT.'/monprojet/actioncomm.php?action=create&socid='.$object->socid.'&projectid='.$object->id.'&backtopage=1&percentage=1">'.$langs->trans('Nuevo evento').'</a>';
	print '</td></tr>';

	print '</table>';

	dol_fiche_end();

	/*
	* Actions
	*/
	print '<div class="tabsAction">';
	if ($user->rights->monprojet->bug->del && $object->statut == 0) 
	//quitar para activar el borrado
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/monprojet/tasks.php'.'?id='.$object->id.'&action=deletetask'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Deletetasks').'</a>';

	if ($user->rights->projet->all->creer ||
		$user->rights->projet->creer ||
		$user->rights->monprojet->bud->crear)
	{
		if ($user->rights->monprojet->bud->crear) $userWrite = true;
		if ($object->public || $userWrite > 0 && $action != 'createup')
		{
			//print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=supplies'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Supplies').'</a>';

			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=suppliesproj'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Suppliesprojet').'</a>';

			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=copysupplies'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('CopySuppliesToProjet').'</a>';

			//items
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=itemproj'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Itemprojet').'</a>';

			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=copyitem'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('CopyItemToProjet').'</a>';

			//print '<a class="butAction" href="'.DOL_URL_ROOT.'/monprojet/export_excel.php'.'?id='.$object->id.'&action=export'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Toexport').'</a>';
			//print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=createup'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Goup').'</a>';
			//print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=create'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Create').'</a>';
			//print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=create&newsel=1'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Toselect').'</a>';
		}
		else
		{
			print '<a class="butActionRefused" href="#" title="'.$langs->trans("NotOwnerOfProject").'">'.$langs->trans('AddTask').'</a>';
		}
	}

	print '</div>';
	print '<br>';
	//action=supplies
	if ($action == 'supplies')
	{
		include DOL_DOCUMENT_ROOT.'/monprojet/tpl/supplies.tpl.php';
	}
	if ($action == 'suppliesproj' || $action == 'addsupplies')
	{
		  // Confirm addsupplies
		if ($action == 'addsupplies')
		{
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("AddSupplies"),$langs->trans("ConfirmAddSuppliesDB",$object->ref),"confirm_addsupplies",'',0,2);
			if ($ret == 'html') print '<br>';
		}
		include DOL_DOCUMENT_ROOT.'/monprojet/tpl/suppliesproj.tpl.php';
	}
	if ($action == 'copysupplies' || $action == 'selsupplies' || $action == 'delsupplies' || $action == 'addcopysupplies')
	{
		  // Confirm addsupplies
		if ($action == 'delsupplies' && $_REQUEST['saveins'] == $langs->trans('Save'))
		{
			$_SESSION['aPost'] = $_POST;
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("AddSupplies"),$langs->trans("ConfirmAddSuppliesDB",$object->ref),"confirm_addcopysupplies",'',0,2);
			if ($ret == 'html') print '<br>';
		}
		include DOL_DOCUMENT_ROOT.'/monprojet/tpl/copysuppliesproj.tpl.php';
	}
	if ($action == 'itemproj')
	{
		include DOL_DOCUMENT_ROOT.'/monprojet/tpl/itemproj.tpl.php';
	}
	if ($action == 'copyitem' || $action == 'selitem' || $action == 'delitem' || $action == 'addcopyitem')
	{
		  // Confirm addsupplies
		if ($action == 'delitem' && $_REQUEST['saveitem'] == $langs->trans('Save'))
		{
			$_SESSION['aPost'] = $_POST;
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("AddItem"),$langs->trans("ConfirmAddItemProject",$object->ref),"confirm_addcopyitem",'',0,2);
			if ($ret == 'html') print '<br>';
		}
		include DOL_DOCUMENT_ROOT.'/monprojet/tpl/copyitemproj.tpl.php';
	}
}

llxFooter();

$db->close();
