<?php
/* Copyright (C) 2001-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2005      Simon TOSSER         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/product/stock/product.php
 *	\ingroup    product stock
 *	\brief      Page to list detailed stock of a product
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

require_once DOL_DOCUMENT_ROOT.'/almacen/class/transf.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtempext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtype.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementdocext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotbanksoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/ctypemouvement.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/mouvementstockext.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';

require_once DOL_DOCUMENT_ROOT.'/almacen/lib/almacen.lib.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/core/modules/almacen/modules_almacen.php");

require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementdoclog.class.php");

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

$langs->load("products");
$langs->load("orders");
$langs->load("bills");
$langs->load("stocks");
$langs->load("almacen@almacen");

//if (!$user->rights->almacen->transf->write) accessforbidden();

$action=GETPOST("action");
$cancel=GETPOST('cancel');
$confirm=GETPOST('confirm');

// Security check
$id    = GETPOST('id')?GETPOST('id'):GETPOST('ref');
$idreg = GETPOST('idreg');
$idr   = GETPOST('idr');
$ref   = GETPOST('ref');
$print = GETPOST('print','int');
$stocklimit = GETPOST('stocklimit');
$fieldid    = isset($_GET["ref"])?'ref':'rowid';

if ($user->societe_id) $socid=$user->societe_id;
//$result=restrictedArea($user,'almacen',$id,'product&product','','',$fieldid);

if($confirm == 'no'){
	$action = '';
	header("Location: ".dol_buildpath('/almacen/transferencia/fiche.php?id='.$id,1));
}

$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
//verificamos el periodo
verif_year($action=='create'?true:($action=='recep'?true:false));

$period_year = $_SESSION['period_year'];
$lAddnew = $_SESSION['lAlmacennew'];

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
$hookmanager=new HookManager($db);
$hookmanager->initHooks(array('ordercard'));

$object = new Stockmouvementtempext($db);
$objecttmp = new Stockmouvementtempext($db);
$objectadd = new Stockmouvementadd($db);
$objectdoc = new Stockmouvementdocext($db);
$objproduct = new Product($db);
$objentrepot = new Entrepotbanksoc($db);
$entrepot = new Entrepot($db);
$objentrepotuser = new Entrepotuserext($db);
$objecttype = new Ctypemouvement($db);
$departament = new Pdepartamentext($db);
$societe = new Societe($db);

$objLog = new Stockmouvementdoclog($db);

$formfile = new Formfile($db);


$aLog = array(-1=>$langs->trans('StatusRejected'),0=>$langs->trans('StatusDraft'),1=>$langs->trans('StatusPending'),2=>$langs->trans('StatusApproved'));


//filtro por usuario
$filteruser = '0';
if ($id)
{
	$res = $objectdoc->fetch($id,$id);
}

//verifica permisos de almacenes
$aFilterent = array();
$aFilterentsol = array();
$filterusersol = '';
$now = dol_now();
if (!$user->admin) list($aFilterent, $filteruser,$aFilterentsol, $filterusersol,$aAreadirect,$fk_areaasign,$filterarea,$aFilterarea, $fk_user_resp,$aExcluded) = verif_accessalm();



if (($id || $ref) && $action != 'transfert_session_up')
{
	$res = $objectdoc->fetch($id,((empty($ref) || $ref == NULL)?NULL:$ref));
	$ref = $objectdoc->ref;
	if ($res <=0)
	{
		$error++;
		header("Location: ".DOL_URL_ROOT."/almacen/transferencia/liste.php");
		exit;
	}
}

/*
 *	Actions
 */

// Action to delete
if ($action == 'draft' && $objectdoc->statut == 1 && $user->rights->almacen->transf->write)
{
	$now = dol_now();
	$objectdoc->statut = 0;
	$objectdoc->fk_user_mod = $user->id;
	$objectdoc->datem = $now;
	$result=$objectdoc->update($user);
	if ($result > 0)
	{
			// Delete OK
		setEventMessages("RecordDraft", null, 'mesgs');
		header("Location: ".dol_buildpath('/almacen/transferencia/card.php?id='.$objectdoc->id,1));
		exit;
	}
	else
	{
		if (! empty($objectdoc->errors)) setEventMessages(null, $objectdoc->errors, 'errors');
		else setEventMessages($objectdoc->error, null, 'errors');
	}
}

if ($action == 'addline' && $user->rights->almacen->transfin->mod)
{
	//modificamos la linea
	$id = GETPOST('id');
	$objectdoc->fetch($id);
	$object->initAsSpecimen();
	$product = new Product($db);
	$res = $product->fetch((!empty(GETPOST('idprod'))?GETPOST('idprod'):''),(!empty(GETPOST('search_idprod'))?GETPOST('search_idprod'):''));
	if ($res > 0)
	{
		//buscamos si existe un producto registrado
		//no puede existir duplicados
		$filterstatic = " AND t.fk_product = ".$product->id;
		$filterstatic.= " AND t.entity = ".$objectdoc->entity;
		$filterstatic.= " AND t.ref = ".$objectdoc->ref;
		$filterstatic.= " AND t.fk_entrepot_from = ".$objectdoc->fk_entrepot_from;
		$res = $object->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);
		if (empty($res))
		{
			$object->entity = $objectdoc->entity;
			$object->ref = $objectdoc->ref;
			$object->tms = dol_now();
			$object->datem = $objectdoc->datem;
			$object->fk_product = $product->id;
			$object->fk_entrepot = $objectdoc->fk_entrepot_from;
			$object->fk_type_mov = ($objectdoc->fk_type_mov?$objectdoc->fk_type_mov:GETPOST('fk_type_mov'));
			$object->value = GETPOST('nbpiece');;
			$object->quant = 0;
			$object->balance_peps = GETPOST('nbpiece');
			$object->balance_ueps = GETPOST('nbpiece');
			if ($conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT)
			{
				$object->price = GETPOST('price')/GETPOST('nbpiece');
				$object->price_peps = GETPOST('price')/GETPOST('nbpiece');
				$object->price_ueps = GETPOST('price')/GETPOST('nbpiece');
			}
			else
			{
				$object->price = GETPOST('price','int');
				$object->price_peps = GETPOST('price','int');
				$object->price_ueps = GETPOST('price','int');
			}
			$object->type_mouvement = 0;
		//movimiento manual
			$object->fk_user_author = $user->id;
			$object->label = $objectdoc->label;
			$object->fk_origin = 0;
			$object->origintype = '';
			$object->inventorycode = '';
			$object->batch = '';
			$object->eatby = '';
			$object->sellby = '';
			$object->statut = 1;

			$res = $object->create($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
			else
			{
				setEventMessages($langs->trans('Record created successfully'),null,'mesgs');
				header('Location: '.$_SERVER['PHP_SELF'].'?ref='.$objectdoc->ref);
				exit;
			}
		}
		else
		{
			$error++;
			setEventMessages($langs->trans('The product').': '.$product->ref.' '.$product->label.', '.$langs->trans('Is registered'),null,'warnings');
			$action = 'createline';
		}
	}
	else
	{
		$error++;
		setEventMessages($product->error,$product->errors,'errors');
	}
	$action = '';
}

if ($action == 'updateline' && $user->rights->almacen->transfin->mod)
{
	//modificamos la linea
	$id = GETPOST('id');

	$res = $object->fetch($idr);
	$product = new Product($db);
	$res = $product->fetch((!empty(GETPOST('idprod'))?GETPOST('idprod'):''),(!empty(GETPOST('search_idprod'))?GETPOST('search_idprod'):''));
	if ($res>0)
	{
		$object->fk_product = $product->id;
		$object->value = GETPOST('nbpiece');
		$object->balance_peps = GETPOST('nbpiece');
		$object->balance_ueps = GETPOST('nbpiece');
		if ($conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT)
		{
			$object->price = GETPOST('price')/GETPOST('nbpiece');
			$object->price_peps = GETPOST('price')/GETPOST('nbpiece');
			$object->price_ueps = GETPOST('price')/GETPOST('nbpiece');
		}
		else
		{
			$object->price = GETPOST('price','int');
			$object->price_peps = GETPOST('price','int');
			$object->price_ueps = GETPOST('price','int');
		}
		$res = $object->updateline($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		else
		{
			setEventMessages($langs->trans('Successfullupdate'),null,'mesgs');
		}
	}
	else
	{
		$error++;
		setEventMessages($product->error,$product->errors,'errors');
	}
	$action = '';
}

if ($action == 'update' && $user->rights->almacen->transfin->mod)
{
	//modificamos la cabecera
	$id = GETPOST('id');
	$res = $objectdoc->fetch($id);
	$datesel  = dol_mktime(GETPOST('dihour'), GETPOST('dimin'), 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));
	if ($res>0 && $objectdoc->id == $id)
	{
		$db->begin();
		$objectdoc->fk_entrepot_from = GETPOST('id_entrepot_source');
		$objectdoc->fk_type_mov = GETPOST('fk_type_mouvement');
		$objectdoc->datem = $datesel;
		$objectdoc->fk_departament = GETPOST('fk_departament')+0;
		$objectdoc->fk_soc = GETPOST('fk_soc')+0;
		$objectdoc->ref_ext = GETPOST('ref_ext');
		$objectdoc->label = GETPOST('label');
		$objectdoc->datem = dol_now();
		$res = $objectdoc->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($objectdoc->error,$objectdoc->errors,'errors');
		}
		else
		{
			setEventMessages($langs->trans('Successfullupdate'),null,'mesgs');
		}
		if(!$error)
		{
			//vamos a realizar el cambio de entrepot segun el ingreso
			$filter = "";
			$lines = $objectdoc->fetchlines($filter);
			if (count($lines) > 0)
			{
				foreach ($lines AS $j => $line)
				{
					$res = $objecttmp->fetch($line->id);
					if ($res == 1)
					{
						$objecttmp->fk_entrepot = $objectdoc->fk_entrepot_from;
						$resup=$objecttmp->update($user);
						if ($resup<=0)
						{
							$error++;
							setEventMessages($objecttmp->error,$objecttmp->errors,'errors');
						}
					}
				}
			}
		}
		if (!$error)
			$db->commit();
		else
			$db->rollback();
	}
	$action = '';
}

if ($action == 'delete')
{
	$ref = GETPOST('ref');
	$res = $objectdoc->fetch('',$ref);
	if ($res>0 && $objectdoc->ref == $ref)
	{
		$db->begin();
		//borramos
		$objectdoc->statut = -1;
		$res = $objectdoc->update($user);
		if ($res <= 0) $error++;
		if (!$error)
		{
			//cambiamos de estado a temp
			$filter = array(1=>1);
			$filterstatic = " AND t.ref = '".$ref."'";
			$res = $object->getlist($ref,1,"",'ref','DESC');
			$tem = new Stockmouvementtemp($db);
			foreach((array) $object->array AS $i => $line)
			{
				$tem->fetch($line->id);
				if($tem->id == $line->id)
				{
					$tem->statut = -1;
					$rest = $tem->update($user);
					if ($rest<= 0) $error++;
				}
			}
		}
		if (empty($error))
		{
			$db->commit();
			header('Location: '.DOL_URL_ROOT.'/almacen/transferencia/liste.php?search_statut=1');
			exit;
		}
		else
		{
			$db->rollback();
		}
	}
}
if ($action == 'builddoc')	// En get ou en post
{
	$objectdoc->fetch($id,trim($ref));
	//$id = $objectdoc->id;
	//$objectdoc->fetch_thirdparty();
	//$objectdoc->fetch_lines();
	$ref = $objectdoc->ref;
	//procesamos la informacion
	$res = $object->getlist($ref,"1,2");
	$aListe = array();
	$cLabel = '';
	$lStatut = true;
	$fk_type_mov = $objectdoc->fk_type_mov;
	if (empty($cLabel))
		$cLabel = '<i>'.$langs->trans('Date').':</i> '.dol_print_date($objectdoc->datem,'day').'; <i>'.$langs->trans('Description').'</i>: '.$objectdoc->label;

	$newReporth[$id]['ref'] = $objectdoc->ref;
	$newReporth[$id]['date'] = $objectdoc->datem;
	$newReporth[$id]['label'] = $objectdoc->label;

	foreach ((array) $object->array AS $i => $obj)
	{
		if (empty($fk_type_mov)) $fk_type_mov = $obj->fk_type_mov+0;
		if ($obj->statut > 1) $lStatut = false;
		if ($obj->type_mouvement == 0)
		{
			$aListef[$obj->ref][$obj->fk_product]['from'] = array('id'=>$obj->id,'fk_entrepot'=>$obj->fk_entrepot,'value'=>$obj->value,'quant'=>$obj->quant,'price'=> $obj->price);
			$aListeentrepot[$obj->fk_entrepot][$obj->id] = $obj->id;
		}
		if ($obj->type_mouvement == 1)
		{
			$aListet[$obj->ref][$obj->fk_product]['to'] = array('id'=>$obj->id,'fk_entrepot'=>$obj->fk_entrepot,'value'=>$obj->value,'quant'=>$obj->quant);
		}
	}
	//recuperamos el type_mouvement
	$objtm = get_type_mouvement($fk_type_mov);
	if ($objtm->rowid == $fk_type_mov)
	{
		$newReporth[$id]['type_mov'] = $objtm->label;

		if ($objectdoc->fk_departament)
		{
			$departament->fetch($objectdoc->fk_departament);
			$newReporth[$id]['departament'] = $departament->ref.' '.$departament->label;
		}
		if ($objectdoc->fk_soc)
		{
			$societe->fetch($objectdoc->fk_soc);
			$newReporth[$id]['soc'] = $societe->nom;
		}
		if ($objectdoc->ref_ext)
		{
			$newReporth[$id]['ref_ext'] = $objectdoc->ref_ext;
		}
	}
	//revisamos que tipo de transaccion es
	//$lTrans = 0 salida y entrada
	//$lTrans = 1 entrada
	//$lTrans = 2 salida
	$lTrans = 0;
	if (count($aListet[$ref])>0 && count($aListef[$ref])>0)
	{
		$LTrans = 0;
		$aListeff = $aListef[$ref];
	}
	if ((count($aListef[$ref])<=0 || empty($aListef[$ref])) && count($aListet[$ref])>0)
	{
		$lTrans = 2;
		$aListeff = $aListet[$ref];
	}
	if ((count($aListet[$ref])<=0 || empty($aListet[$ref])) && count($aListef[$ref])>0)
	{
		$lTrans = 1;
		$aListeff = $aListef[$ref];
	}

	$lOut = false;
	if (count($aListef[$ref])<=0 || empty($aListef[$ref]))
	{
		$lOut = true;
		$aListeff = $aListet[$ref];
	}
	else
		$aListeff = $aListef[$ref];

	$aReport = array();
	$k = 0;
	foreach ((array) $aListeff AS $fk_product => $aData)
	{
		foreach ((array) $aData AS $type => $aValue)
		{
				//determinamos quien envia (si existe)
			$entrepot_to = '';
				//if (!$lOut)
				//{
			$fk_entrepott = $aListet[$ref][$fk_product]['to']['fk_entrepot'];
			if ($fk_entrepott>0)
			{
				$entrepot->fetch($fk_entrepott);
				$entrepot_to = $entrepot->lieu;
			}
				//}
				//quien recibe
			$entrepot->fetch($aValue['fk_entrepot']);
			$entrepot_from = $entrepot->lieu;
				//producto
			$objproduct->fetch($fk_product);


			$aReport[$k]['fk_entrepot_from']=$objectdoc->fk_entrepot_from;
			$aReport[$k]['fk_entrepot_to']=$objectdoc->fk_entrepot_to;
			$aReport[$k]['statut']=$objectdoc->statut;
			$aReport[$k]['ref']=$objproduct->ref;
			$aReport[$k]['desc']=$objproduct->label.' - '.$objproduct->getLabelOfUnit('short');
			if ($objectdoc->fk_entrepot_from && !$objectdoc->fk_entrepot_to)
			{
				$aReport[$k]['entrepot_from']=$entrepot_from;
			}
			elseif($objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
			{
				$aReport[$k]['entrepot_to']=$entrepot_to;
				$aReport[$k]['entrepot_from']=$entrepot_from;
			}
			elseif (!$objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
			{
				$aReport[$k]['entrepot_to']=$entrepot_to;
			}

			if ($objectdoc->fk_entrepot_from && !$objectdoc->fk_entrepot_to)
			{
				if ($objectdoc->statut == 2)
				{
					$aReport[$k]['quant']=$aValue['quant'];
				}
				else
				{
					$aReport[$k]['value']=$aValue['value'];
				}
				if ($conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT)
				{
					$aReport[$k]['total']=price2num($aValue['value']*$aValue['price'],'MT');
				}
				else
				{
					$aReport[$k]['total']=price2num($aValue['price'],'MU');
				}
			}
			elseif($objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
			{
				$aReport[$k]['quant']=$aValue['quant'];
				$aReport[$k]['value']=$aValue['value'];
			}
			elseif (!$objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
			{
				$aReport[$k]['value']=$aValue['value'];
			}




		}
		$k++;
	}


	$newReport[$id] = $aReport;
	$_SESSION['reporttransf'] = serialize($newReport);
	$_SESSION['reporttransfh'] = serialize($newReporth);



	//fin proceso

	if (GETPOST('model'))
	{
		$objectdoc->setDocModel($user, GETPOST('model'));
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
	$result=almacen_pdf_create($db, $objectdoc, $objectdoc->model_pdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
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
	// Remove file in doc form
if ($action == 'remove_file')
{
	if ($objectdoc->id > 0)
	{
		require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

		$langs->load("other");
		$upload_dir = $conf->almacen->dir_output;
		//. '/' . dol_sanitizeFileName($objectdoc->ref);

		$file = $upload_dir . '/' . GETPOST('file');
		$ret = dol_delete_file($file, 0, 0, 0, $objectdoc);
		if ($ret)
			setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else
			setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
		$action = '';
	}
}

if ($cancel)
{
	header("Location: ".DOL_URL_ROOT."/almacen/transferencia/liste.php");
	exit;
}
$itemTransf = array();
$transf = array();
if (! empty($_SESSION['itemTransf'])) $itemTransf=json_decode($_SESSION['itemTransf'],true);
if (! empty($_SESSION['transf'])) $transf=json_decode($_SESSION['transf'],true);
// Set stock limit
if ($action == 'setstocklimit')
{
	$product = new Product($db);
	$result=$product->fetch($id);
	$product->seuil_stock_alerte=$stocklimit;
	$result=$product->update($product->id,$user,1,0,1);
	if ($result < 0)
		setEventMessage($product->error, 'errors');
	$action = '';
}
// action delitem
if ($action == 'delitem' && !empty($idreg))
{
	//unset($_SESSION['itemTransf'][GETPOST("id")]);
	if (! empty($itemTransf[$idreg])) unset($itemTransf[$idreg]);
	if (count($itemTransf) > 0) $_SESSION['itemTransf']=json_encode($itemTransf);
	else unset($_SESSION['itemTransf']);
	header("Location: fiche.php?action=create");
	exit;

}

//transfer session
if ($action == "transfert_session" && ! $cancel)
{
	if (! GETPOST("nbpiece"))
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("NumberOfUnit")), 'errors');
		$error++;
		$action='create';
	}
	else
	{
		//buscamos el producto
		$product = new Product($db);
		$idprod = GETPOST('idprod');
		$search_idprod = GETPOST('search_idprod');
		if (!empty($idprod) || !empty($search_idprod))
		{
			$res = $product->fetch((!empty(GETPOST('idprod'))?GETPOST('idprod'):''),(!empty(GETPOST('search_idprod'))?GETPOST('search_idprod'):''));
			if ($res >0)
			{
				if (count(array_keys($itemTransf)) > 0) $idreg=max(array_keys($itemTransf)) + 1;
				else $idreg=1;
				if ($product->id>0)
				{
				//verificamos que sea un producto unico
					foreach ((array) $itemTransf AS $j => $data)
					{
						if ($product->id == $data['id_product'])
							$idreg = $j;
					}
					$itemTransf[$idreg]=array('id'=>$idreg, 'id_product'=>$product->id, 'qty'=>GETPOST("nbpiece"), 'batch'=>$batch);
					$_SESSION['itemTransf']=json_encode($itemTransf);
				//$_SESSION["itemTransf"][$product->id] = GETPOST("nbpiece");
					header("Location: fiche.php?action=create");
					exit;
				}
				else
				{
					setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), 'errors');
					$action = 'create';
				}
			}
			else
			{
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), 'errors');
				$action = 'create';
			}
		}
		else
		{
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), 'errors');
			$action = 'create';
		}

	}
}

//transfer session
if ($action == "transfert_session_up" && ! $cancel)
{
	if (! GETPOST("nbpiece"))
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("NumberOfUnit")), 'errors');
		$error++;
		$action='edit';
	}
	else
	{
		//buscamos el producto
		$product = new Product($db);
		$res = $product->fetch((!empty(GETPOST('idprod'))?GETPOST('idprod'):''),(!empty(GETPOST('search_idprod'))?GETPOST('search_idprod'):''));
		if ($res>0)
		{
			//editamos el producto en la session
			if ($product->id>0)
			{
				$itemTransf[$idr]['id_product'] = $product->id;
				$itemTransf[$idr]['qty'] = GETPOST('nbpiece');
				$_SESSION['itemTransf']=json_encode($itemTransf);
				//$_SESSION["itemTransf"][$product->id] = GETPOST("nbpiece");
				header("Location: fiche.php?action=create");
				exit;
			}
			else
			{
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), 'errors');
				$action = 'create';
			}
		}
		else
		{
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), 'errors');
			$action = 'create';
		}
	}
}

// Transfer stock from a warehouse to another warehouse
//confirmacion de recepcion
if ($action == "confirm_transf_ok" && $confirm == 'yes' && ! $cancel && $user->rights->almacen->transf->write)
{
	$error = 0;

	if (! empty($_SESSION['aTransf']))
	{
		$aTmp = $_SESSION['aTransf'][$id];
		$transf=json_decode($aTmp,true);
	}
	//recorremos la transferencia con los datos recibidos
	$aQuantr = $transf['aQuantr'];
	$aQuantrd = $transf['aQuantrd'];
	$aQuantd = $transf['aQuantd'];
	$aPriced = $transf['aPriced'];
	$ref     = $transf['ref'];
	$iddoc   = $transf['idStockdoc'];

	$objectdoc->fetch($iddoc);
	//determinamos que tipo de movimiento es
	$objtm = get_type_mouvement($objectdoc->fk_type_mov);

	////////////////////
	////////////////////
	///
	///  SE DEBE CALCULAR CORRECTAMENTE LA SALIDA DE PRODUCTOS
	///  Y VER LA VALORACION PPP Y PEPS
	/////////////////////

	$aProduct = array();
	$db->begin();

	//registro destino
	$aIdsdes = array();
	$aIdsori = array();
	$aProductval = array();
	$fk_entrepot_from = 0;
	$fk_entrepot_to = 0;

	$objsmt = new Stockmouvementtype($db);
	$lTransfer = false;
	$numr = count($aQuantr)+0;
	$numd = count($aQuantd)+0;
	if ($numr>0 && $numd>0 && is_array($aQuantr) && is_array($aQuantd))
	{
		$lTransfer = true;
	}
	if (!$error)
	{
		//registro de salida
		//recorremos el registro de donde sale
		//si es una transferencia lTransfer == true,  se debe obtener el valor de la salida y }
		//pasar el mismo valor para la entrada de cada producto
		//echo '<hr>aQountr';
		foreach ((array) $aQuantr AS $idreg => $nbpiece)
		{
			if ($idreg>0)
			{
				//recuperamos la cantidad que se esta aceptando
				//echo '<hr>idreg '.$idreg.' => '.$nbpiece;
				if (count($aQuantrd[$idreg])>0)
				{
					foreach ($aQuantrd[$idreg] AS $newid => $novalue)
					{
						$nbpiece = $aQuantd[$newid] *-1;
					}
				}
				//echo ' new '.$nbpiece;exit;
				//buscamos el idreg
				$object->fetch($idreg);
				$nbpiece = ($nbpiece < 0?$nbpiece*-1:$nbpiece);
				$fk_type_mov = $object->fk_type_mov;
				//recuperamos la cantidad que se descarga
				//$nbpiece = $aProduct[$object->fk_product];
				$quant = $nbpiece;

				if (is_numeric($nbpiece) && $object->id == $idreg)
				{
					$product = new Product($db);
					$result=$product->fetch($object->fk_product);
					$transf = new Transf($db);
					$resultn=$transf->fetch($object->fk_product);
					$product->load_stock();
					$pricesrc=0;
					if (isset($product->stock_warehouse[$object->fk_entrepot]->pmp))
						$pricesrc=$product->stock_warehouse[$object->fk_entrepot]->pmp;
					if (empty($pricesrc))
						$pricesrc = $product->pmp;

					$pricedest=$pricesrc;

					$aSales = array();
					//valuacion por el metodo peps
					$objMouvement = new MouvementStockext($db);
					$date = dol_now();
					$resmov = $objMouvement->get_value_product($object->fk_entrepot,$date,$object->fk_product,$nbpiece,$typemethod,$pricesrc,$product);
					if ($resmov <= 0)
					{
						$error++;
						setEventMessages($langs->trans('Error en obtener el movimiento..').' '.$resmov,null,'errors');
					}
					$aSales = $objMouvement->aSales;
					if ($lTransfer)
						$aProductval[$object->fk_product] = $aSales;
					//echo '<hr>opcion1 ';

					foreach ((array) $aSales AS $fk_stock => $row)
					{
						$transf->origin = 'stockmouvementtemp';
						$transf->originid = $idreg;
						// Add stock
						//echo '<hr>qty '.$row['qty'].' '.$object->fk_entrepot;
						$result2=$transf->add_transfer_ok($user,$object->fk_entrepot,$row['qty'],1,$object->ref.' '.$object->label,$pricedest,$idreg,$fk_type_mov);
						if ($result2 <= 0)
						{
							$error=101;
							setEventMessage($product->error, 'errors');
						//$db->rollback();
						}
						$fk_entrepot_to = $object->fk_entrepot;
						$aIdsdes[$result2] = $result2;

						//buscamos y actualizamos registro en stock_mouvement_add
						$resadd = $objectadd->fetch(0,$result2);
						if ($resadd==0)
						{
							//echo '<hr>xcreaing '.$result2;
							$now = dol_now();
							$objectadd->fk_stock_mouvement = $result2;
							$objectadd->fk_stock_mouvement_doc = $iddoc;
							$objectadd->period_year = $_SESSION['period_year']+0;
							$objectadd->month_year = $_SESSION['period_month']+0;
							$objectadd->period_month = $_SESSION['period_month']+0;
							$objectadd->fk_facture = 0;
							$objectadd->fk_user_create = $user->id;
							$objectadd->fk_user_mod = $user->id;
							$objectadd->fk_parent_line = $row['id']+0;
							$objectadd->qty = 0;
							$objectadd->date_create = $now;
							$objectadd->date_mod = $now;
							$objectadd->tms = $now;
							$objectadd->balance_peps = 0;
							$objectadd->balance_ueps = 0;
							$objectadd->value_peps = $row['value'];
							$objectadd->value_ueps = 0;
							$objectadd->value_peps_adq = $row['value'];
							$objectadd->value_ueps_adq = 0;
							$objectadd->status = 1;
							$resadd = $objectadd->create($user);
							if ($resadd <=0)
							{
								setEventMessages($objectadd->error,$objectadd->errors,'errors');
								$error=102;
							}
						}
						elseif($resadd==1)
						{
										//echo '<hr>actualizqaing '.$objectadd->id;
							$now = dol_now();
							$objectadd->fk_user_mod = $user->id;
							$objectadd->period_year = $_SESSION['period_year']+0;
							$objectadd->period_month = $_SESSION['period_month']+0;
							$objectadd->month_year = $_SESSION['period_month']+0;
							$objectadd->date_mod = $now;
							$objectadd->tms = $now;
							$objectadd->fk_parent_line = $row['id']+0;
							$objectadd->qty = 0;
							$objectadd->balance_peps = 0;
							$objectadd->balance_ueps = 0;
							$objectadd->value_peps = $row['value'];
							$objectadd->value_ueps = 0;
							$objectadd->value_peps_adq = $row['value'];
							$objectadd->value_ueps_adq = 0;
							$objectadd->status = 1;
							$resadd = $objectadd->update($user);
							if ($resadd<=0)
							{
								setEventMessages($objectadd->error,$objectadd->errors,'errors');
								$error=103;
							}
						}

							//creamos registro en stock_mouvement_type
						$objsmt->fk_stock_mouvement = $result2;
						$objsmt->fk_type_mouvement = $object->fk_type_mov;
						$objsmt->tms = dol_now();
						$objsmt->statut = 1;
						$resmt = $objsmt->create($user);
						if ($resmt <= 0)
						{
							$error=104;
							setEventMessages($objsmt->error,$objsmt->errors,'errors');
						}
						$object->quant = $quant*-1;
						$object->statut = 2;
						$object->fk_origin = $result2;
						$object->origintype = 'stock_mouvement';

						$res0 = $object->update($user);
						if ($res0 <=0)
						{
							setEventMessages($object->error,$object->errors,'errors');
							$error=105;
						}
					}
				}
				else
				{
					echo '<hr>noingresa ';
					$error=106;
				}
			}
		}
	}

	if (!$error)
	{
		//registro de ingresos o salida en negativo
		foreach ((array) $aQuantd AS $idreg => $nbpiece)
		{
			if ($idreg>0)
			{
				//echo '<hr>aQauantd '.$idreg .' => '.$nbpiece;
				$quant = $nbpiece;
				//buscamos el idreg
				$object->fetch($idreg);
				$fk_type_mov = $object->fk_type_mov;
				if (is_numeric($nbpiece) && $object->id == $idreg)
				{
					$product = new Product($db);
					$result=$product->fetch($object->fk_product);
					$transf = new Transf($db);
					$resultn=$transf->fetch($object->fk_product);
					$aProduct[$object->fk_product] = $nbpiece;
					$product->load_stock();
					// Define value of products moved
					$pricesrc=0;
					if ($aPriced[$idreg])
					{
						$pricesrc = $aPriced[$idreg];
					}
					else
					{
						if (isset($product->stock_warehouse[$object->fk_entrepot]->pmp))
						{
							$pricesrc=$product->stock_warehouse[$object->fk_entrepot]->pmp;
						}
					}

					if (empty(price2num($pricesrc)))
					{
						$pricesrc=$product->pmp;
					}
					$pricedest=$pricesrc;
					//echo '<br>'.$lTransfer.' type '.$objtm->type;
					if ($lTransfer || $objtm->type == 'O')
					{
						//SALIDAS SEGUN TIPO ==0
						$type_mouvement_ = 1;
						if ($lTransfer)
						{
							//la salida registrada en el punto anterior se vuelve un ingreso
							$aSales = $aProductval[$object->fk_product];
							$type_mouvement_ = 0;
										//print_r($aSales);
						}
						else
						{
							//al ser salida la cantidad es negativo
							//para el calculo cambiamos a positivo
							$nbpiecemod = $nbpiece * -1;
							$aSales = array();
							//valuacion por el metodo peps
							$objMouvement = new MouvementStockext($db);
							$date = dol_now();
							//echo '<hr>quant '.$nbpiecemod.' '.$typemethod;
							//echo '<hr>resmov '.
							$resmov = $objMouvement->get_value_product($object->fk_entrepot,$date,$object->fk_product,$nbpiecemod,$typemethod,$pricesrc,$product);

							if ($resmov <= 0)
							{
								$error=201;
								setEventMessages($langs->trans('Error en obtener el movimiento'),null,'errors');
							}
							$aSales = $objMouvement->aSales;
							if (count($aSales)<=0)
							{
								$error=201;
								setEventMessages($langs->trans('Error, No existe saldo suficiente en el almacen'),null,'errors');
							}
						}

						//echo '<hr>optionc2 ';
						//SALIDA DE PRODUCTOS
						//para salida de productos el tipo de movimiento es 1
						foreach ((array) $aSales AS $fk_stock => $row)
						{
							$quant_ = $row['qty'];
							if ($objtm->type== 'O')
								$quant_ = $row['qty'];
							$transf->origin = 'stockmouvementtemp';
							$transf->originid = $idreg;
							// Add stock
							//echo '<hr>cantidad '.$quant_.' typemov '.$type_mouvement_.' '.$row['value'];
							$result2=$transf->add_transfer_ok($user,$object->fk_entrepot,$quant_,$type_mouvement_,$object->ref.' '.$object->label,$pricedest,$idreg,$fk_type_mov);
							if ($result2 <= 0)
							{
								$error=202;
								setEventMessage($product->error, 'errors');
							}
							$fk_entrepot_to = $object->fk_entrepot;
							$aIdsdes[$result2] = $result2;

							//buscamos y actualizamos registro en stock_mouvement_add
							$resadd = $objectadd->fetch(0,$result2);
							if ($resadd==0)
							{
								//echo '<br>inserta2A '.$result2;

								$now = dol_now();
								$objectadd->fk_stock_mouvement = $result2;
								$objectadd->fk_stock_mouvement_doc = $iddoc;
								$objectadd->period_year = $_SESSION['period_year']+0;
								$objectadd->period_month = $_SESSION['period_month']+0;
								$objectadd->month_year = $_SESSION['period_month']+0;
								$objectadd->fk_facture = 0;
								$objectadd->fk_user_create = $user->id;
								$objectadd->fk_user_mod = $user->id;
								$objectadd->fk_parent_line = $row['id']+0;
								$objectadd->date_create = $now;
								$objectadd->date_mod = $now;
								$objectadd->tms = $now;
								$objectadd->qty = $row['qty'];
								$objectadd->balance_peps = $row['qty'];
								$objectadd->balance_ueps = $row['qty'];
								$objectadd->value_peps = $row['value'];
								$objectadd->value_ueps = $row['value'];
								$objectadd->value_peps_adq = $row['value'];
								$objectadd->value_ueps_adq = $row['value'];
								$objectadd->status = 1;
								$resadd = $objectadd->create($user);
								if ($resadd <=0)
								{
									setEventMessages($objectadd->error,$objectadd->errors,'errors');
									$error=203;
								}
							}
							elseif($resadd==1)
							{
								//echo '<br>actualiza2A '.$objectadd->id;


								$now = dol_now();
								$objectadd->fk_user_mod = $user->id;
								$objectadd->period_year = $_SESSION['period_year']+0;
								$objectadd->period_month = $_SESSION['period_month']+0;
								$objectadd->month_year = $_SESSION['period_month']+0;
								$objectadd->date_mod = $now;
								$objectadd->tms = $now;
								$objectadd->fk_parent_line = $row['id']+0;
								$objectadd->qty = $row['qty'];
								$objectadd->balance_peps = $row['qty'];
								$objectadd->balance_ueps = $row['qty'];
								$objectadd->value_peps = $row['value'];
								$objectadd->value_ueps = $row['value'];
								$objectadd->value_peps_adq = $row['value'];
								$objectadd->value_ueps_adq = $row['value'];
								$objectadd->status = 1;
								$resadd = $objectadd->update($user);
								if ($resadd<=0)
								{
									setEventMessages($objectadd->error,$objectadd->errors,'errors');
									$error=204;
								}
							}

							//creamos registro en stock_mouvement_type
							$objsmt->fk_stock_mouvement = $result2;
							$objsmt->fk_type_mouvement = $object->fk_type_mov;
							$objsmt->tms = dol_now();
							$objsmt->statut = 1;
							$resmt = $objsmt->create($user);
							if ($resmt <= 0)
							{
								$error=205;
								setEventMessages($objsmt->error,$objsmt->errors,'errors');
							}

							$object->quant = $quant;
							$object->statut = 2;
							$object->fk_origin = $result2;
							$object->origintype = 'stock_mouvement';

							$res0 = $object->update($user);
							if ($res0 <=0)
							{
								setEventMessages($object->error,$object->errors,'errors');
								$error=206;
							}
						}
					}
					else
					{
						//MOVIMIENTO DE INGRESOS
						$priceppp = $object->price;
						$price_peps=$object->price_peps;
						$price_ueps=$object->price_ueps;

						$balance_peps = $nbpiece;
						$balance_ueps = $nbpiece;

						$transf->origin = ($objectdet->element?$objectdet->element:'stockmouvementtemp');
						$transf->originid = $idreg;
						// Add stock
						$result2=$transf->add_transfer_ok($user,$object->fk_entrepot,$nbpiece,0,$object->ref.' '.$object->label,$pricedest,$idreg,$fk_type_mov);
						if ($result2 <= 0)
						{
							$error=207;
							setEventMessages($transf->error,$transf->errors, 'errors');
						//$db->rollback();
						}
						$fk_entrepot_to = $object->fk_entrepot;
						$aIdsdes[$result2] = $result2;

						//buscamos y actualizamos registro en stock_mouvement_add
						$resadd = $objectadd->fetch(0,$result2);
						if ($resadd==0)
						{
							//echo '<hr>quees '.$result2;
							$now = dol_now();
							$objectadd->fk_stock_mouvement = $result2;
							$objectadd->fk_stock_mouvement_doc = $iddoc;
							$objectadd->period_year = $_SESSION['period_year']+0;
							$objectadd->period_month = $_SESSION['period_month']+0;
							if (is_null($objectadd->period_year) || empty($objectadd->period_year)) $objectadd->period_year=date('Y');
							$objectadd->month_year = $_SESSION['period_month'];
							if (is_null($objectadd->month_year) || empty($objectadd->month_year)) $objectadd->month_year=date('m');
							$objectadd->fk_facture = 0;
							$objectadd->fk_user_create = $user->id;
							$objectadd->fk_user_mod = $user->id;
							$objectadd->fk_parent_line = 0;
							$objectadd->date_create = $now;
							$objectadd->date_mod = $now;
							$objectadd->tms = $now;
							$objectadd->qty = $balance_peps;
							$objectadd->balance_peps = $balance_peps;
							$objectadd->balance_ueps = $balance_ueps;
							$objectadd->value_peps = $price_peps;
							$objectadd->value_ueps = $price_ueps;
							$objectadd->value_peps_adq = $price_peps;
							$objectadd->value_ueps_adq = $price_ueps;
							$objectadd->status = 1;
							$resadd = $objectadd->create($user);
							if ($resadd <=0)
							{
								setEventMessages($objectadd->error,$objectadd->errors,'errors');
								$error=208;
							}
						}
						elseif($resadd==1)
						{
							//echo '<hr>actualizaquees '.$objectadd->id;
							$now = dol_now();
							$objectadd->fk_user_mod = $user->id;
							$objectadd->period_year = $_SESSION['period_year']+0;
							$objectadd->period_month = $_SESSION['period_month']+0;
							if (is_null($objectadd->period_year) || empty($objectadd->period_year)) $objectadd->period_year=date('Y');
							$objectadd->month_year = $_SESSION['period_month'];
							if (is_null($objectadd->month_year) || empty($objectadd->month_year)) $objectadd->month_year=date('m');
							$objectadd->date_mod = $now;
							$objectadd->tms = $now;
							$objectadd->qty = $balance_peps;
							$objectadd->balance_peps = $balance_peps;
							$objectadd->balance_ueps = $balance_ueps;
							$objectadd->value_peps = $price_peps;
							$objectadd->value_ueps = $price_ueps;
							$objectadd->value_peps_adq = $price_peps;
							$objectadd->value_ueps_adq = $price_ueps;
							$objectadd->status = 1;
							$resadd = $objectadd->update($user);
							if ($resadd<=0)
							{
								setEventMessages($objectadd->error,$objectadd->errors,'errors');
								$error=209;
							}
						}
					}
					//creamos registro en stock_mouvement_type
					if (!$error)
					{
						$objsmt->fk_stock_mouvement = $result2;
						$objsmt->fk_type_mouvement = $object->fk_type_mov;
						$objsmt->tms = dol_now();
						$objsmt->statut = 1;
						$resmt = $objsmt->create($user);
						if ($resmt <= 0)
						{
							$error=210;
							setEventMessages($objsmt->error,$objsmt->errors,'errors');
						}
					}


				// Add stock
				//$result2=$product->correct_stock($user,$object->fk_entrepot,$nbpiece,0,$object->label,$pricedest);
				//if ($result2<= 0) $error++;
				//cambiamos de estado a object
					if (!$error)
					{
						$object->quant = $quant;
						$object->statut = 2;
						$object->fk_origin = $result2;
						$object->origintype = 'stock_mouvement';
						$res0 = $object->update($user);
						if ($res0 <=0)
						{
							setEventMessages($object->error,$object->errors,'errors');
							$error=211;
						}
					}
				}
			}
		}

	}

	if (empty($error))
	{
		//cambiamos de estado al objectdoc
		//$objectdoc->fk_entrepot_from = $fk_entrepot_from+0;
		//$objectdoc->fk_entrepot_to = $fk_entrepot_to+0;
		if ($objectdoc->fk_entrepot_from && !$objectdoc->fk_entrepot_to)
			$objectdoc->model_pdf = 'inputalm';
		elseif($objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
			$objectdoc->model_pdf = 'transfer';
		elseif (!$objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
			$objectdoc->model_pdf = 'outputalm';
		$objectdoc->statut = 2;
		$res = $objectdoc->update($user);
		if ($res <= 0)
		{
			setEventMessages($objectdoc->error,$objectdoc->errors,'errors');
			$error=301;
		}
	}

	if (empty($error))
	{
		$db->commit();
		//generamos el documento
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
			$model=$objectdoc->model_pdf;
			$ret = $objectdoc->fetch($id);
			// Reload to get new records
			$result=$objectdoc->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
			if ($result < 0) dol_print_error($db,$result);
		}

		//$result=almacen_pdf_create($db, $objectdoc, $objectdoc->model_pdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);

		setEventMessages($langs->trans('Saverecord'),null,'mesgs');
		//header("Location: ".DOL_URL_ROOT."/almacen/transferencia/liste.php");
		header("Location: ".$_SERVER['PHP_SELF'].'?id='.$objectdoc->id);
		exit;
	}
	else
	{
		$action = '';
		setEventMessages($langs->trans('Error'),null,'errors');
		$db->rollback();
	}
}

// Transfer stock from a warehouse to another warehouse
if ($action == "confirm_transfert_stock" && $_REQUEST['confirm'] == 'yes')
{
	$idr = $transf['idr'];
	$idd = $transf['idd'];
	$datem = $transf['datem'];
	$label = $transf['label'];
	$fk_type_mov = $transf['fk_type_mov'];
	$codeinv = '';
	$label = $transf['label'];
	if (! ($idr > 0) || ! ($idd > 0))
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Warehouse")), 'errors');
		$error++;
		$action='transfert';
	}
	if (count($itemTransf)<=0)
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("NumberOfUnit")), 'errors');
		$error++;
		$action='create';
	}
	if (! $error)
	{
		if ($idr <> $idd)
		{
			//buscamos la numeracion para la transferencia
			$ref = 'PROV';
			if ($ref == 'PROV')
				$numref = $objectdoc->getNextNumRef($soc);
			else
				$numref = $objectdoc->ref;
			$db->begin();
			//creamos el registro principal
			$objectdoc->ref = $numref;
			$objectdoc->entity = $conf->entity;
			$objectdoc->fk_entrepot_from = $idr+0;
			$objectdoc->fk_entrepot_to = $idd+0;
			$objectdoc->fk_type_mov = $fk_type_mov+0;
			$objectdoc->datem = $datem;
			$objectdoc->label = $label;
			$objectdoc->date_create = dol_now();
			$objectdoc->date_mod = dol_now();
			$objectdoc->tms = dol_now();
			$objectdoc->model_pdf = 'transfer';
			$objectdoc->fk_user_create = $user->id;
			$objectdoc->fk_user_mod = $user->id;
			$objectdoc->statut = 1;
			$res = $objectdoc->create($user);
			if ($res <=0)
			{
				setEventMessages($objectdoc->error,$objectdoc->errors,'errors');
				$error++;
			}

			foreach ((array) $itemTransf AS $idreg => $aData)
			{
				$id = $aData['id_product'];
				$nbpiece = $aData['qty'];
				if (is_numeric($nbpiece) && $id)
				{
					$product = new Product($db);
					$transf = new Transf($db);
					$result=$transf->fetch($id);
					if ($nbpiece <>0)
					{
						$product->load_stock();//Load array product->stock_warehouse

						// Define value of products moved
						$pricesrc=0;
						if (isset($product->stock_warehouse[$idr]->pmp))
							$pricesrc=$product->stock_warehouse[$idr]->pmp;
						$pricedest=$pricesrc;

						//print 'price src='.$pricesrc.', price dest='.$pricedest;exit;

						// Remove stock
						$result1=$transf->add_transfer($user,$idr,$numref,$nbpiece,1,$label,$pricesrc,$codeinv,$fk_type_mov);

						// Add stock
						$result2=$transf->add_transfer($user,$idd,$numref,$nbpiece,0,$label,$pricedest,$codeinv,$fk_type_mov);
						if ($result1 <= 0 || $result2 <= 0)
						{
							$error++;
							setEventMessages($transf->error,$transf->errors, 'errors');
							$db->rollback();
						}
					}
				}
			}
			if (empty($error))
			{
				setEventMessages($langs->trans('Saverecord'),null,'mesgs');
				$db->commit();
			}
			else
			{
				setEventMessages($langs->trans('Errors'),null, 'errors');
				$db->rollback();
				header("Location: ".$_SERVER['PHP_SELF']."?action=create");
			}
		}
	}
	if (empty($error))
	{
		unset($_SESSION['itemTransf']);
		unset($_SESSION['transf']);
		header("Location: liste.php");
		exit;
	}
}
// action delitem
if ($action == 'clean')
{
	unset($_SESSION['itemTransf']);
	header("Location: ".$_SERVER['PHP_SELF']."?action=create");
	exit;
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = 'create';
	unset($_SESSION['itemTransf']);
	unset($_SESSION['transf']);
}



// Giveback
if ($action == 'confirm_giveback'&& $_REQUEST['confirm'] == 'yes')
{
	$res = $objectdoc->fetch($id);
	//$datesel  = dol_mktime(GETPOST('dihour'), GETPOST('dimin'), 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));
	if ($res>0 && $objectdoc->id == $id)
	{
		$db->begin();
		$objectdoc->statut = 0;
		$res = $objectdoc->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($objectdoc->error,$objectdoc->errors,'errors');
		}
		else
		{
			setEventMessages($langs->trans('SuccessfullReturned'),null,'mesgs');
		}

		if (!$error)
		{
			$objLog->fk_stock_mouvement_doc = $id;
			$objLog->status = $objectdoc->statut;
			$objLog->description = $aLog[$object->statut].' '.GETPOST('motivo');
			$objLog->fk_user_create = $user->id;
			$objLog->fk_user_mod = $user->id;
			$objLog->datec = dol_now();
			$objLog->datem = dol_now();
			$objLog->tms = dol_now();
			$res = $objLog->create($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objLog->error,$objLog->errors,'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
			header("Location: ".dol_buildpath('/almacen/transferencia/fiche.php?id='.$id,1));
		}
		else
			$db->rollback();

		$action = '';
	}
}


// Giveback
if ($action == 'confirm_validate'&& $_REQUEST['confirm'] == 'yes' && $user->rights->almacen->transfin->mod)
{
	$res = $objectdoc->fetch($id);
	//$datesel  = dol_mktime(GETPOST('dihour'), GETPOST('dimin'), 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));
	if ($res>0 && $objectdoc->id == $id)
	{
		$db->begin();
		$objectdoc->statut = 1;
		$res = $objectdoc->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($objectdoc->error,$objectdoc->errors,'errors');
		}
		else
		{
			setEventMessages($langs->trans('Successfullyvalidate'),null,'mesgs');
		}

		if (!$error)
		{
			$objLog->fk_stock_mouvement_doc = $id;
			$objLog->status = $objectdoc->statut;
			$objLog->description = $aLog[$object->statut].' '.GETPOST('motivo');
			$objLog->fk_user_create = $user->id;
			$objLog->fk_user_mod = $user->id;
			$objLog->datec = dol_now();
			$objLog->datem = dol_now();
			$objLog->tms = dol_now();
			$res = $objLog->create($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objLog->error,$objLog->errors,'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
			header("Location: ".dol_buildpath('/almacen/transferencia/fiche.php?id='.$id,1));
		}
		else
			$db->rollback();

		$action = '';
	}
}


/*
 * View
 */

$formproduct=new FormProduct($db);
$formproductout=new FormProduct($db);
$form = new Formv($db);

// if ($ref) $result = $product->fetch('',$ref);
// if ($id > 0) $result = $product->fetch($id);
$arrayofcss=array('/almacen/css/style.css');
//$arrayofjs=array('/almacen/javascript/recargar.js');
$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';

$morejs=array("/almacen/javascript/almacen.js");
llxHeader('',$langs->trans("Transfer"),$help_url,'','','',$morejs,$arrayofcss,0,0);

/*
 * habilitamos una sesion para la carga de items de transferencia
 * $_SESSION['itemTransf'] = array()
*/

// transfer stock
if ($action == 'transfert_stock')
{

	$transf['idr'] = GETPOST('id_entrepot_source');
	$transf['idd'] = GETPOST('id_entrepot_destination');
	$transf['fk_type_mov'] = GETPOST('fk_type_mouvement');
	if ($user->rights->almacen->transf->datem)
	{
		$datem  = dol_mktime(GETPOST('rehour'), GETPOST('remin'), 0, GETPOST('remonth'),  GETPOST('reday'),  GETPOST('reyear'));

		$transf['datem'] = $datem;
	}
	else
		$transf['datem'] = dol_now();
	$transf['label'] = GETPOST('label');
	$_SESSION['transf']=json_encode($transf);

	$ret=$form->form_confirm($_SERVER["PHP_SELF"],$langs->trans("Transferproduct"),$langs->trans("ConfirmTransferproduct",$object->libelle),"confirm_transfert_stock",'',0,2);
	if ($ret == 'html') print '<br>';
}

if ($objectdoc->statut==1 && $print)
{

	include DOL_DOCUMENT_ROOT.'/almacen/transferencia/tpl/popupticket.php';
}

/*
 * create transfert
 */
if ($action == "create" || $action =="endt" || $action == 'edit')
{

	//WYSIWYG Editor
	//require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

	print_fiche_titre($langs->trans('CreateTransfer').' '.$period_year);

	//	print_titre($langs->trans("Transfers"));
	//dol_fiche_head();
	print '<div style="overflow-x: auto; white-space: nowrap;">';

	print '<table class="noborder" width="100%">';
	//if ($action != 'create')
	//{
		//encabezado de registro nuevo
	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Product"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Unit"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Quantity"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Action"),"liste.php", "","","","",$sortfield,$sortorder);
	print '</tr>';
	if (!$lAddnew)
	{
		setEventMessages($langs->trans('It is not allowed to carry out movements in this management'),null,'warnings');
	}
	//}
	if ($action == 'create' && $lAddnew)
	{
		print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$product->id.'" method="post">'."\n";
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="transfert_session">';
		//print '<table class="noborder" width="100%">';

		print '<tr>';
		print '<td width="80%">';
		$i = 1;
		print $form->select_produits_v('','idprod','',$conf->product->limit_size,0,-1,2,'',1,'','');
		print '<input type="text" style="border:none;" id="labelproduct" name="labelproduct" size="90%" value="" readonly>';
		print '</td>';
		print '<td>';
		print '<input type="text" style="border:none;" id="unit" name="unit" value="" readonly>';
		print '</td>';
		print '<td width="15%">';
		print '<input id="nbpiece" class="width50" type="number" min="0" step="any" name="nbpiece" value="'.GETPOST("nbpiece").'" required>';
		print '</td>';
		print '<td width="5%">';
		print '<center><input type="submit" class="button" value="'.$langs->trans('Insert').'"></center>';
		print '</td>';
		print '</tr>';
		//print '</table>';
		print '</form>';
	}

	$aItemRow = $itemTransf;

	//print '<table class="border" width="100%">';
	foreach ((array) $aItemRow AS $idreg => $aReg)
	{
		if (!empty($aReg['id_product']))
		{
			$var = !$var;
			$product = new Product($db);
			$product->fetch($aReg['id_product']);
			if ($idr == $idreg && $action == 'edit')
			{
				print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$product->id.'" method="post">'."\n";
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				print '<input type="hidden" name="action" value="transfert_session_up">';
				print '<input type="hidden" name="idr" value="'.$idreg.'">';
				print '<tr>';
				print '<td width="80%">';
				$i = 1;
				print $form->select_produits_v($aReg['id_product'],'idprod','',$conf->product->limit_size,0,-1,2,'',1,'','');
				print '<input type="text" style="border:none;" id="labelproduct" name="labelproduct" value="" readonly>';
				print '</td>';
				print '<td>';
				print '<input type="text" style="border:none;" id="unit" name="unit" value="" readonly>';
				print '</td>';

				print '<td width="15%" class="fieldrequired">';
				print '<input id="nbpiece" name="nbpiece" size="10" value="'.$aReg['qty'].'">';
				print '</td>';
				print '<td width="5%" class="fieldrequired">';
				print '<center><input type="submit" class="button" value="'.$langs->trans('Save').'"></center>';
				print '</td>';
				print '</tr>';
				print '</form>';

			}
			else
			{
				print "<tr $bc[$var]>";
				print '<td width="78%">';
				print $product->ref.': '.$product->label;
				print '</td>';
				print '<td width="2%">';
				print $product->getLabelOfUnit('short');
				print '</td>';
				print '<td width="15%" align="center">';
				print $aReg['qty'];
				print '</td>';
				print '<td width="5%" align="right">';
				print '<center>';
			//	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
				if ($action != 'endt')
				{
					print '<a href="'.$_SERVER['PHP_SELF'].'?idr='.$idreg.'&action=edit">'.img_picto($langs->trans('Edit'),'edit').'</a>&nbsp;';
					print '<a href="'.DOL_URL_ROOT.'/almacen/transferencia/fiche.php?action=delitem&idreg='.$idreg.'">'.img_picto($langs->trans("Delete"),'delete').'</a>';
				}
				print '</td>';
			}
			print '</tr>';
		}
	}
	print '</table>';
	print '</div>';
	if ($user->rights->almacen->transf->write && $action != 'endt')
	{
		if (count($itemTransf)>0)
		{
			print "<div class=\"tabsAction\">\n";
			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=endt">'.$langs->trans("Createmovement").'</a>';
			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=clean">'.$langs->trans("Clean").'</a>';
			print '</div>';
		}
	}


	if (count($aItemRow)>0 && $action=="endt" && $lAddnew)
	{
		print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="transfert_stock">';
		print '</br>';

		dol_fiche_head();
		print '<table class="noborder" width="100%">';
		print '<tr>';
		print '<td width="20%" class="fieldrequired">'.$langs->trans("WarehouseSource").'</td><td width="20%">';
		if ($user->admin)
		{
			print $formproduct->selectWarehouses(($_GET["dwid"]?$_GET["dwid"]:GETPOST('id_entrepot_source')),'id_entrepot_source','',1);
		}
		else
		{
			//armamos una lista de entrepot

			print $formproductout->selectWarehouses(($_GET["dwid"]?$_GET["dwid"]:(GETPOST('id_entrepot_source')?GETPOST('id_entrepot_source'):$idEntrepotdefault)),'id_entrepot_source','',1,0,0,'',0,0,array(),'',$aExcluded);

			//$checked = '';
			//if (count($aFilterent) == 1) $checked = 'checked="checked"';
			//foreach ($aFilterent AS $fk_entrepot)
			//{
			//	$entrepot->fetch($fk_entrepot);
			//	print '<p>'.$entrepot->lieu.' <input type="radio" '.$checked.' name="id_entrepot_source" value="'.$fk_entrepot.'">'.'</p>';
			//}
		}

		print '</td>';
		print '</tr>';
		print '<tr>';
		print '<td width="20%" class="fieldrequired">'.$langs->trans("WarehouseTarget").'</td>';
		print '<td width="20%">';
		print $formproduct->selectWarehouses(GETPOST('id_entrepot_destination'),'id_entrepot_destination','',1);
		print '</td>';
		print '</tr>';

		print '<tr>';
		print '<td width="20%" class="fieldrequired">'.$langs->trans("Type").'</td><td>';
		$filterstatic = " AND t.type = 'T'";
		$res = $objecttype->fetchAll('ASC', 't.label', 0, 0, array(1=>1),'AND',$filterstatic);
		$options = '';
		if ($res>0)
		{
			foreach ($objecttype->lines AS $j => $line)
			{
				$options.= '<option value="'.$line->id.'" '.(GETPOST('fk_type_mouvement') == $line->id?'selected':'').'>'.$line->label.'</option>';
			}
		}
		print '<select name="fk_type_mouvement">'.$options.'</select>';
		//print select_type_mouvement($selected,'fk_type_mouvement','',0,1);
		print '</td>';
		print '</tr>';

		print '<tr>';
		print '<td width="20%" class="fieldrequired">'.$langs->trans("Date").'</td>';
		print '<td colspan="4">';
		if ($user->rights->almacen->transf->datem)
			$form->select_date((GETPOST('datem')?GETPOST('datem'):dol_now()),'re',1,1,'',"crea_commande",1,1);
		else
		{
			print dol_print_date(dol_now(),'daytext');
		}
		print '</td>';
		print '</tr>';


		print '<tr>';
		print '<td width="20%">'.$langs->trans("Label").'</td>';
		print '<td colspan="4">';
		print '<input type="text" name="label" size="40" value="'.GETPOST("label").'">';
		print '</td>';
		print '</tr>';

		print '</table>';
		dol_fiche_end();
		print '<center><input type="submit" class="button" value="'.$langs->trans('Save').'">&nbsp;';
		print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

		print '</form>';
	}
}

//action mod solo movimientos de entrada
if (($id || $ref) && $action == 'mod')
{
	print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="'.$objectdoc->id.'">';
	print '</br>';

	dol_fiche_head();

	print '<table class="noborder" width="100%">';
	print '<tr>';
	print '<td width="20%" class="fieldrequired">'.$langs->trans("WarehouseEntry").'</td><td width="20%">';
	print $formproduct->selectWarehouses($objectdoc->fk_entrepot_from,'id_entrepot_source','',1);
	print '</td>';
	print '</tr>';
	print '<tr>';
	print '<td width="20%" class="fieldrequired">'.$langs->trans("Type").'</td><td>';
	$filterstatic = " AND t.type = 'E'";
	$res = $objecttype->fetchAll('ASC', 't.label', 0, 0, array(1=>1),'AND',$filterstatic);
	$options = '';
	if ($res>0)
	{
		foreach ($objecttype->lines AS $j => $line)
		{
			$options.= '<option value="'.$line->id.'" '.($objectdoc->fk_type_mov == $line->id?'selected':'').'>'.$line->label.'</option>';
		}
	}
	print '<select name="fk_type_mouvement">'.$options.'</select>';;
	print '</td>';
	print '</tr>';

	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="3">';
	if ($user->rights->almacen->transf->datem)
		$form->select_date(($datesel?$datesel:dol_now()),'di',1,1,'',"crea_commande",1,1);
	else
		print dol_print_date(dol_now());
	print '</td></tr>';
		//unidad ejecutora
	if ($conf->global->ALMACEN_MOUVEMENT_INPUT_DISPLAY_DEPARTAMENT)
	{
		print '<tr>';
		print '<td width="20%">'.$langs->trans('Departament').'/'.$langs->trans("Executing Units").'</td>';
		print '<td colspan="4">';
		print $form->select_departament($objectdoc->fk_departament,'fk_departament','',0,1);
		print '</td>';
		print '</tr>';
	}
		//proveedor
	if ($conf->global->ALMACEN_MOUVEMENT_INPUT_DISPLAY_SOCIETE)
	{
		print '<tr>';
		print '<td width="20%">'.$langs->trans('Supplier').'</td>';
		print '<td colspan="4">';
		$filtertype = 's.client = 1 OR s.client = 3';
		$filtertype = 's.fournisseur = 1';
		print $form->select_company($objectdoc->fk_soc, 'fk_soc', 's.fournisseur = 1', 'SelectThirdParty');
		print '</td>';
		print '</tr>';
	}
	print '<tr>';
	print '<td width="20%">'.$langs->trans("Document").'</td>';
	print '<td colspan="4">';
	print '<input type="text" name="ref_ext" size="10" value="'.$objectdoc->ref_ext.'">';
	print '</td>';
	print '</tr>';

	print '<tr>';
	print '<td width="20%">'.$langs->trans("Label").'</td>';
	print '<td colspan="4">';
	print '<input type="text" name="label" size="40" value="'.$objectdoc->label.'">';
	print '</td>';
	print '</tr>';

	print '</table>';



	dol_fiche_end();

	print "<div class=\"tabsAction\">\n";
	print '<center><input type="submit" class="button" value="'.$langs->trans('Save').'">&nbsp;';
	print '<a href="'.$_SERVER['PHP_SELF'].'?ref='.$objectdoc->ref.'" class="button">'.$langs->trans("Cancel").'</a>';
	print '</div>';

	print '</form>';
}
//edicion
if ((!empty($id) || !empty($ref)) && $action != 'mod')
{
	print_fiche_titre($langs->trans('Transfer').' '.$ref);
	dol_fiche_head();
	$resdoc = $objectdoc->fetch(0,$ref);
	$id = $objectdoc->id;
	//recuperamos el ultimo log
	$reslog = $objLog->fetchAll('DESC','datec',0,0,array(1=>1),'AND'," AND t.fk_stock_mouvement_doc = ".$id);
	$j = 0;
	$motivo = '';
	if ($reslog > 0)
	{
		foreach ($objLog->lines AS $k => $line)
		{
			if (empty($j))
			{
				$motivo = $line->description;
				$j++;
			}
		}
	}
	//verificacion del fk_type_mov
	if ($objectdoc->id > 0 && empty($objectdoc->fk_type_mov))
	{
		//procedemos a actualizar para que este completo si esta relacionado a stock_mouvement_temp
		$filtertmp = " AND t.ref = '".$objectdoc->ref."'";
		$restmp = $objecttmp->fetchAll('','',0,0,array(1=>1),'AND',$filtertmp);
		if ($restmp > 0)
		{
			foreach($objecttmp->lines AS $j => $linetmp)
			{
				$fk_type_mov = $linetmp->fk_type_mov;
			}
			//actualizamos en objectdoc
			if ($fk_type_mov > 0)
			{
				$objectdoc->fk_type_mov = $fk_type_mov;
				$res = $objectdoc->update($user);
				if ($res <=0)
				{
					setEventMessages($objectdoc->error,$objectdoc->errors,'errors');
				}

			}
		}

	}
	if ($action == 'giveback')
	{
		$formquestion = array(
			array('type'=>'text','label'=>$langs->trans('Motivo '),'size'=>40,'name'=>'motivo','value'=>'','placeholder'=>$langs->trans('Ingrese el motivo por el cual tomo la decicion de devolver')),
			array('type'=>'hidden','name'=>'ref','value'=>$ref));


			//$ret=$form->form_confirm($_SERVER["PHP_SELF"],$langs->trans("Transferproduct"),$langs->trans("ConfirmTransferproduct",$object->libelle),"confirm_transfert_stock",'',0,2);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$id,
			$langs->trans('Confirmgiveback'),
			$langs->trans('ConfirmTheTransferOfReturnsWithNoteIncome').' '.$ref,
			'confirm_giveback',
			$formquestion ,
			1,
			2);
		print $formconfirm;
	}

	if ($action == 'addconf')
	{
		unset($_SESSION['aTransf'][$id]);
		$transf = array();
		$transf['aQuantd'] = GETPOST('quantd');
		$transf['aQuantr'] = GETPOST('quantr');
		$transf['aQuantrd'] = GETPOST('quantrd');
		$transf['aPriced'] = GETPOST('priced');
		$transf['idStockdoc'] = $objectdoc->id;
		$transf['ref'] = $ref;
		//$_SESSION['aTransf']=json_encode($transf);
		$aTransf= json_encode($transf);
		$_SESSION['aTransf'][$id]= $aTransf;
		$ret=$form->form_confirm($_SERVER["PHP_SELF"].'?id='.$id,$langs->trans("Oktransfer"),$langs->trans("ConfirmOktransfer",$object->libelle),"confirm_transf_ok",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	if ($action == 'validate')
	{
		$formquestion = array(
			array('type'=>'text','label'=>$langs->trans('Motivo '),'size'=>40,'name'=>'motivo','value'=>'','placeholder'=>$langs->trans('Ingrese el motivo por el cual esta validando')),
			array('type'=>'hidden','name'=>'ref','value'=>$ref));
		$ret=$form->form_confirm($_SERVER["PHP_SELF"].'?id='.$id,$langs->trans("Validatemovement"),$langs->trans("ConfirmValidatemovement",$object->libelle).' '.$objectdoc->ref,"confirm_validate",'',1,2);
		if ($ret == 'html') print '<br>';
	}


	//arreglamos los datos
	//buscamos la seleccion
	$res = $object->getlist($ref,"1,2");
	$aListe = array();
	$cLabel = '';
	$lStatut = true;
	$fk_type_mov = $objectdoc->fk_type_mov;
	if (empty($cLabel)) $cLabel = '<i>'.$langs->trans('Date').':</i> '.dol_print_date($objectdoc->datem,'day').'; <i>'.$langs->trans('Description').'</i>: '.$objectdoc->label;

		$newReporth[$id]['ref'] = $objectdoc->ref;
		$newReporth[$id]['date'] = $objectdoc->datem;
		$newReporth[$id]['label'] = $objectdoc->label;

		foreach ((array) $object->array AS $i => $obj)
		{
			if (empty($fk_type_mov)) $fk_type_mov = $obj->fk_type_mov+0;
			if ($obj->statut > 1) $lStatut = false;
			if (empty($cLabel)) $cLabel = '<i>'.$langs->trans('Date').':</i> '.dol_print_date($obj->datem,'day').'; <i>'.$langs->trans('Description').'</i>: '.$obj->label;

				if ($obj->type_mouvement == 0)
				{
					$aListef[$obj->ref][$obj->fk_product]['from'] = array('id'=>$obj->id,'fk_entrepot'=>$obj->fk_entrepot,'value'=>$obj->value,'quant'=>$obj->quant,'price'=> $obj->price);
					$aListeentrepot[$obj->fk_entrepot][$obj->id] = $obj->id;
				}
				if ($obj->type_mouvement == 1)
				{
					$aListet[$obj->ref][$obj->fk_product]['to'] = array('id'=>$obj->id,'fk_entrepot'=>$obj->fk_entrepot,'value'=>$obj->value,'quant'=>$obj->quant);
					$aListeentrepot[$obj->fk_entrepot][$obj->id] = $obj->id;
				}
			}

			//recuperamos el type_mouvement
			$typemov = '';
			$objtm = get_type_mouvement($fk_type_mov);
			if ($objtm->rowid == $fk_type_mov)
			{
				$typemov = $objtm->type;
				$newReporth[$id]['type_mov'] = $objtm->label;

				$cLabel = '<i>'.$langs->trans('Mouvement').':</i> '.$objtm->label.'; '.$cLabel;
				if ($objectdoc->fk_departament)
				{
					$departament->fetch($objectdoc->fk_departament);
					$cLabel.= '<br><i>'.$langs->trans('Departament').'</i>: '.$departament->getNomUrl(0).'';
					$newReporth[$id]['departament'] = $departament->ref.' '.$departament->label;
				}
				if ($objectdoc->fk_soc)
				{
					$societe->fetch($objectdoc->fk_soc);
					$cLabel.= '<br><i>'.$langs->trans('Supplier').'</i>: '.$societe->getNomUrl(0).'';
					$newReporth[$id]['soc'] = $societe->nom;
				}
				if ($objectdoc->ref_ext)
				{
					$cLabel.= '<br><i>'.$langs->trans('Document').'</i>: '.$objectdoc->ref_ext;
					$newReporth[$id]['ref_ext'] = $objectdoc->ref_ext;
				}
			}
			//revisamos que tipo de transaccion es
			//$lTrans = 0 salida y entrada
			//$lTrans = 1 entrada
			//$lTrans = 2 salida
			$lTrans = 0;
			if (count($aListet[$ref])>0 && count($aListef[$ref])>0)
			{
				$LTrans = 0;
				$aListeff = $aListef[$ref];
			}
			if ((count($aListef[$ref])<=0 || empty($aListef[$ref])) && count($aListet[$ref])>0)
			{
				$lTrans = 2;
				$aListeff = $aListet[$ref];
			}
			if ((count($aListet[$ref])<=0 || empty($aListet[$ref])) && count($aListef[$ref])>0)
			{
				$lTrans = 1;
				$aListeff = $aListef[$ref];
			}
			//recep
			if ($action == 'recep')
			{
				print_titre($cLabel);
				print '<br>';
				//buscamos la seleccion
				//$res = $object->getlist($ref,"1,2");
				print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				print '<input type="hidden" name="action" value="addconf">';
				print '<input type="hidden" name="ref" value="'.$ref.'">';

				print '<table class="border" width="100%">';
				//encabezado de registro nuevo
				print "<tr class=\"liste_titre\">";
				print '<td width="15%">'.$langs->trans("Product").'</td>';
				print '<td width="40%">'.$langs->trans("Description").'</td>';
				if ($lTrans == 2)
					print '<td width="10%">'.$langs->trans("Fromwharehouse").'</td>';
				if ($lTrans == 1)
					print '<td width="10%">'.$langs->trans("Towharehouse").'</td>';
				if (empty($lTrans))
				{
					print '<td width="10%">'.$langs->trans("Fromwharehouse").'</td>';
					print '<td width="10%">'.$langs->trans("Towharehouse").'</td>';
				}
				if ($lTrans == 2)
					print '<td width="15%">'.$langs->trans("Out").'</td>';
				if ($lTrans == 1)
					print '<td width="8%">'.$langs->trans("Received").'</td>';
				if (empty($lTrans))
				{
					print '<td width="15%">'.$langs->trans("Sent").'</td>';
					print '<td width="8%">'.$langs->trans("Received").'</td>';
				}
				print '</tr>';
				$var = true;
				foreach ((array) $aListeff AS $fk_product => $aData)
				{
					foreach ((array) $aData AS $type => $aValue)
					{
				//determinamos quien envia (si existe)
						$entrepot_to = '';
						if (empty($lTrans))
						{
							$aFrom = $aListet[$ref][$fk_product]['to'];
							$fk_entrepott = $aListet[$ref][$fk_product]['to']['fk_entrepot'];
							if ($fk_entrepott>0)
							{
								$entrepot->fetch($fk_entrepott);
								$entrepot_to = $entrepot->lieu;
							}
						}
				//quien recibe
						$entrepot->fetch($aValue['fk_entrepot']);
						$entrepot_from = $entrepot->libelle;
				//producto
						$objproduct->fetch($fk_product);
						$var=!$var;
						print "<tr $bc[$var]>";
						print '<td>'.$objproduct->ref.'</td>';
						print '<td>'.$objproduct->label.' - '.$objproduct->getLabelOfUnit('short').'</td>';
						if ($lTrans>0)
							print '<td>'.$entrepot_from.'</td>';
						else
						{
							print '<td>'.$entrepot_to.'</td>';
							print '<td>'.$entrepot_from.'</td>';
						}
				//valores

						if ($lTrans==2)
						{
							print '<td align="right">'.$aValue['value'].'</td>';
							print '<input type="hidden" name="quantd['.$aValue['id'].']" value="'.$aValue['value'].'">';
						}
						if ($lTrans==1)
						{
							print '<td align="right">'.'<input class="len100" type="number" step="any" name="quantd['.$aValue['id'].']" value="'.$aValue['value'].'">';
					//recibe
							print '<input type="hidden" name="priced['.$aValue['id'].']" value="'.$aValue['price'].'">';
						}
						if (empty($lTrans))
						{
							print '<td align="right">'.$aValue['value'].'</td>';
							print '<input type="hidden" name="quantr['.$aFrom['id'].']" value="'.$aFrom['value'].'">';
					//sale
							print '<input type="hidden" name="quantrd['.$aFrom['id'].']['.$aValue['id'].']" value="'.$aFrom['value'].'">';
					//referencia de entrado a solicitado
							print '<td align="right">'.'<input class="len100" type="number" step="any" name="quantd['.$aValue['id'].']" value="'.$aValue['value'].'">';
					//recibe
						}
						print '</td>';
						print '</tr>';
					}
				}
				print '</table>';
				print '<br><center><input type="submit" class="button" value="'.$langs->trans('Approve').'">&nbsp;';
				print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
				print '</form>';
			}
			else
			{
				print_titre($cLabel);
				print '<br>';
				print '<b><i>'.$langs->trans('Statut').'</i>:</b> '.$objectdoc->getLibStatut(5);
				print '<br>';
				if ($motivo)
				{
					print '<br>';
					print '<b><i>'.$langs->trans('Motivo').'</i>:</b> '.$motivo;
					print '<br>';
				}
				//buscamos la seleccion
				//$res = $object->getlist($ref,"1,2");
				$lOut = false;
				if (count($aListef[$ref])<=0 || empty($aListef[$ref]))
				{
					$lOut = true;
					$aListeff = $aListet[$ref];
				}
				else
					$aListeff = $aListef[$ref];

				if ($action == 'editline' && $user->rights->almacen->transfin->mod)
				{
					print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
					print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
					print '<input type="hidden" name="action" value="updateline">';
					print '<input type="hidden" name="id" value="'.$objectdoc->id.'">';
					print '<input type="hidden" name="idr" value="'.$idr.'">';
				}
				elseif ($action == 'createline' && $user->rights->almacen->transfin->mod)
				{
					print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
					print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
					print '<input type="hidden" name="action" value="addline">';
					print '<input type="hidden" name="id" value="'.$objectdoc->id.'">';
					print '<input type="hidden" name="fk_type_mov" value="'.$fk_type_mov.'">';
				}
				print '<table class="border" width="100%">';
				print "<tr class=\"liste_titre\">";
				print '<td width="9%">'.$langs->trans("Product").'</td>';
				print '<td >'.$langs->trans("Description").'</td>';
				if ($objectdoc->fk_entrepot_from && !$objectdoc->fk_entrepot_to)
				{
					print '<td width="10%">'.$langs->trans("Towharehouse").$lOut.'</td>';
				}
				elseif($objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
				{
					print '<td width="10%">'.$langs->trans("Fromwharehouse").'</td>';
					print '<td width="10%">'.$langs->trans("Towharehouse").'</td>';
				}
				elseif (!$objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
				{
					print '<td width="10%">'.$langs->trans("Fromwharehouse").'</td>';
				}

				if ($objectdoc->fk_entrepot_from && !$objectdoc->fk_entrepot_to)
				{
					print '<td align="right" width="10%">'.$langs->trans("Enters").'</td>';
					if ($conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT)
						print '<td align="right" width="10%">'.$langs->trans("Total").'</td>';
					else
						print '<td align="right" width="10%">'.$langs->trans("P.U.").$lOut.'</td>';
				}
				elseif($objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
				{
					print '<td align="right" width="5%">'.$langs->trans("Sent").'</td>';
					print '<td align="right" width="5%">'.$langs->trans("Received").'</td>';
				}
				elseif (!$objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
					print '<td align="right" width="5%">'.$langs->trans("Output").'</td>';

				if ($user->rights->almacen->transfin->mod && $objectdoc->fk_entrepot_from && empty($objectdoc->fk_entrepot_to))
					print '<td align="center">'.$langs->trans("Action").'</td>';
				print '</tr>';
				$var = true;
				if ($action == 'createline' && $user->rights->almacen->transfin->write)
				{
					print "<tr $bc[$var]>";
					print '<td>';
					print $form->select_produits_v(GETPOST('idprod'),'idprod','',$conf->product->limit_size,0,-1,2,'',1,'','');
					print '</td>';
					print '<td>';
					print '<input type="text" style="border:none;" id="labelproduct" name="labelproduct" value="'.GETPOST('labelproduct').'" readonly>';
					print '<input type="text" style="border:none;" id="unit" name="unit" value="" readonly>';
					print '<input type="hidden" name="fk_entrepot_from" value="'.$objectdoc->fk_entrepot_from.'">';
					print '<input type="hidden" name="fk_entrepot_to" value="'.$objectdoc->fk_entrepot_to.'">';
					print '</td>';
					$entrepot->fetch($objectdoc->fk_entrepot_from);
					$entrepot_from = $entrepot->lieu;

					print '<td>'.$entrepot_from.'</td>';

					print '<td>';
					print '<input id="nbpiece" type="number" min="0" step="any" name="nbpiece" size="10" value="'.GETPOST('value').'" required>';
					print '</td>';
					print '<td>';
					print '<input id="price" type="number" min="0" step="any" name="price" value="'.GETPOST('price').'" required>';
					print '</td>';
					print '<td nowrap>';
					print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
					print '&nbsp;<a class="button" href="'.$_SERVER['PHP_SELF'].'?id='.$objectdoc->id.'">'.$langs->trans('Return').'</a>';
					print '</td>';
					print '</tr>';
				}
				$aReport = array();
				$k = 0;
				foreach ((array) $aListeff AS $fk_product => $aData)
				{
					foreach ((array) $aData AS $type => $aValue)
					{
						//determinamos quien envia (si existe)
						$entrepot_to = '';
						//if (!$lOut)
						//{
						$fk_entrepott = $aListet[$ref][$fk_product]['to']['fk_entrepot'];
						if ($fk_entrepott>0)
						{
							$entrepot->fetch($fk_entrepott);
							$entrepot_to = $entrepot->lieu;
						}
						//}
						//quien recibe
						$entrepot->fetch($aValue['fk_entrepot']);
						$entrepot_from = $entrepot->lieu;
						//producto
						$objproduct->fetch($fk_product);
						$var=!$var;
						print "<tr $bc[$var]>";
						if ($action == 'editline' && $idr == $aValue['id'])
						{
							print '<td>';
							print $form->select_produits_v($fk_product,'idprod','',$conf->product->limit_size,0,-1,2,'',1,'','');
							print '</td>';
							print '<td>';
							print '<input type="text" style="border:none;" id="labelproduct" name="labelproduct" value="'.$objproduct->label.'" readonly>';
							print '<input type="text" style="border:none;" id="unit" name="unit" value="'.$objproduct->getLabelOfUnit('short').'" readonly>';
							print '</td>';
							if ($objectdoc->fk_entrepot_from && !$objectdoc->fk_entrepot_to)
								print '<td>'.$entrepot_from.'</td>';
							elseif($objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
							{
								print '<td>'.$entrepot_to.'</td>';
								print '<td>'.$entrepot_from.'</td>';
							}
							elseif (!$objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
							{
								print '<td>'.$entrepot_to.'</td>';
							}

							print '<td align="right">';
							print '<input id="nbpiece" type="number" min="0" step="any" name="nbpiece" size="10" value="'.$aValue['value'].'" required>';
							print '</td>';
							if ($conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT)
								print '<td align="right" width="10%">'.'<input type="number" min="0" step="any" name="price" value="'.price2num($aValue['price']*$aValue['value'],'MT').'" required></td>';
							else
								print '<td align="right" width="10%">'.'<input type="number" min="0" step="any" name="price" value="'.price2num($aValue['price'],'MU').'" required></td>';
						}
						else
						{
							print '<td>'.$objproduct->getNomUrl(1).'</td>';
							print '<td>'.$objproduct->label.' - '.$objproduct->getLabelOfUnit('short').'</td>';
							$aReport[$k]['fk_entrepot_from']=$objectdoc->fk_entrepot_from;
							$aReport[$k]['fk_entrepot_to']=$objectdoc->fk_entrepot_to;
							$aReport[$k]['statut']=$objectdoc->statut;
							$aReport[$k]['ref']=$objproduct->ref;
							$aReport[$k]['desc']=$objproduct->label.' - '.$objproduct->getLabelOfUnit('short');
							$aReport[$k]['id']= $aValue['id'];
							if ($objectdoc->fk_entrepot_from && !$objectdoc->fk_entrepot_to)
							{
								print '<td>'.$entrepot_from.'</td>';
								$aReport[$k]['entrepot_from']=$entrepot_from;
							}
							elseif($objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
							{
								print '<td>'.$entrepot_to.'</td>';
								print '<td>'.$entrepot_from.'</td>';
								$aReport[$k]['entrepot_to']=$entrepot_to;
								$aReport[$k]['entrepot_from']=$entrepot_from;
							}
							elseif (!$objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
							{
								print '<td>'.$entrepot_to.'</td>';
								$aReport[$k]['entrepot_to']=$entrepot_to;
							}

							if ($objectdoc->fk_entrepot_from && !$objectdoc->fk_entrepot_to)
							{
								if ($objectdoc->statut == 2)
								{
									print '<td align="right">'.$aValue['quant'].'</td>';
									$aReport[$k]['quant']=$aValue['quant'];
								}
								else
								{
									print '<td align="right">'.$aValue['value'].'</td>';
									$aReport[$k]['value']=$aValue['value'];
								}
								if ($conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT)
								{
									if ($objectdoc->statut == 2)
									{
										print '<td align="right" width="10%">'.price(price2num($aValue['quant']*$aValue['price'],'MT')).'</td>';
										$aReport[$k]['total']=price2num($aValue['quant']*$aValue['price'],'MT');
									}
									else
									{
										print '<td align="right" width="10%">'.price(price2num($aValue['value']*$aValue['price'],'MT')).'</td>';
										$aReport[$k]['total']=price2num($aValue['value']*$aValue['price'],'MT');
									}
								}
								else
								{
									print '<td align="right" width="10%">'.price(price2num($aValue['price'],'MU')).'</td>';
									$aReport[$k]['total']=price2num($aValue['price'],'MU');
								}
							}
							elseif($objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
							{
								print '<td align="right">'.$aValue['value'].'</td>';
								print '<td align="right">'.$aValue['quant'].'</td>';
								$aReport[$k]['quant']=$aValue['quant'];
								$aReport[$k]['value']=$aValue['value'];
							}
							elseif (!$objectdoc->fk_entrepot_from && $objectdoc->fk_entrepot_to)
							{
								print '<td align="right">'.$aValue['value'].'</td>';
								$aReport[$k]['value']=$aValue['value'];
							}

						}


				//if ($lOut)
				//	print '<td>'.$entrepot_from.'</td>';
				//else
				//{
				//	print '<td>'.$entrepot_to.'</td>';
				//	print '<td>'.$entrepot_from.'</td>';
				//}

				//if ($lOut)
				//	print '<td>'.$aValue['value'].'</td>';
				//else
				//{
				//	print '<td>'.$aValue['value'].'</td>';
				//	print '<td>'.$aValue['quant'];//recibe
				//}
				//print '</td>';
						if ($user->rights->almacen->transfin->mod && $objectdoc->fk_entrepot_from && empty($objectdoc->fk_entrepot_to))
						{
							print '<td align="center" nowrap>';
							if ($action == 'editline' && $idr == $aValue['id'])
							{
								print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
								print '&nbsp;<a class="button" href="'.$_SERVER['PHP_SELF'].'?id='.$objectdoc->id.'">'.$langs->trans('Return').'</a>';
							}
							else
							{
								if ($objectdoc->statut == 0 && $user->rights->almacen->transfin->mod)
									print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$objectdoc->id.'&idr='.$aValue['id'].'&action=editline">'.img_picto('','edit').'</a>';
							}
							print '</td>';
						}
						$k++;
						print '</tr>';
					}
				}
				print '</table>';
				if ($user->rights->almacen->transfin->mod && ($action == 'editline' || $action == 'createline'))
				{
					print '</form>';
				}

				if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE) && $abc)
				{
					$outputlangs = $langs;
					$newlang = '';
					if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
					if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
					if (! empty($newlang)) {
						$outputlangs = new Translate("", $conf);
						$outputlangs->setDefaultLang($newlang);
					}
					$model=$objectdoc->model_pdf;
			//echo '<hr>ret '.$ret = $objectdoc->fetch($id);
			// Reload to get new records
					$result=$objectdoc->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
					if ($result < 0) dol_print_error($db,$result);
				}

				/* ********************************************* */
				/*                                               */
				/* Barre d'action                                */
				/*                                               */
				/* ********************************************* */

				if (empty($action))
				{
					print "<div class=\"tabsAction\">\n";
					if ($user->rights->almacen->transfin->mod && $objectdoc->statut == 0 && $objectdoc->fk_entrepot_from && !$objectdoc->fk_entrepot_to)
						print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=mod&ref='.$ref.'">'.$langs->trans("Modify").'</a>';

					if ($user->rights->almacen->transfin->mod && $objectdoc->statut == 0 && $objectdoc->fk_entrepot_from && !$objectdoc->fk_entrepot_to)
						print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=validate&ref='.$ref.'">'.$langs->trans("Validate").'</a>';

			//if ($user->rights->almacen->transfin->mod && $objectdoc->statut == 1 && $objectdoc->fk_entrepot_from && !$objectdoc->fk_entrepot_to)
				//print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=createline&ref='.$ref.'">'.$langs->trans("Addproduct").'</a>';
			//Accion de devolver
					if ($user->rights->almacen->transfin->mod && $objectdoc->statut == 1 && $objectdoc->fk_entrepot_from && !$objectdoc->fk_entrepot_to)
			//print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=giveback&ref='.$ref.'">'.$langs->trans("Giveback").'</a>';
						print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=giveback&id='.$id.'">'.$langs->trans("Giveback").'</a>';


			//if ($user->rights->almacen->transf->write && $lStatut)
					if (empty($objectdoc->statut)) $lStatut = false;


					if ($lStatut )
					{
						$lEdit = false;
						foreach ((array) $aListeentrepot AS $j1 => $aJ2)
						{
							if ($aFilterent[$j1]) $lEdit = true;
						}

						if ($user->admin || $lEdit)
						{
							////////////////////////////////////////
							if ($user->rights->almacen->transf->write && $typemov=='T' && $objectdoc->statut==1)
							{
								print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$objectdoc->id.'&print=1&ref='.$ref.'">'.$langs->trans("Printticket").'</a>';
								print '&nbsp;<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=draft&ref='.$ref.'">'.$langs->trans("Backdraft").'</a>';
							}
							if ($user->rights->almacen->transf->app && $typemov=='T' || $user->rights->almacen->transfin->app && $typemov=='E' || $user->rights->almacen->transfout->app && $typemov=='O')
								print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=recep&ref='.$ref.'">'.$langs->trans("Approve").'</a>';
							if ($user->rights->almacen->transf->del)
								print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&ref='.$ref.'">'.$langs->trans("Delete").'</a>';
						}
					}
					print '</div>';
				}

				/* Accion donde armo mi mensaje*/

				/* fin de donde armo el mensaje*/





	/*
		print '<div class="tabsAction">';
		//documents
		print '<table width="100%"><tr><td width="50%" valign="top">';
		print '<a name="builddoc"></a>'; // ancre
		$objectdoc->fetch($id);
		// Documents generes

		$filename=dol_sanitizeFileName($object->ref);
		$filename= 'almacen';
		//cambiando de nombre al reporte
		$filedir=$conf->almacen->dir_output . '/' . dol_sanitizeFileName($object->ref);
		$urlsource=$_SERVER['PHP_SELF'].'?id='.$id;
		$genallowed=$user->rights->almacen->crearpedido;
		$delallowed=$user->rights->almacen->delpedido;
		$genallowed = 1;
		$delallowed = 1;
		$objectdoc->modelpdf = 's_alidaalm';
		print '<br>';
		print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$objectdoc->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
		$somethingshown=$formfile->numoffiles;
		print '</td></tr></table>';
		print "</div>";
*/
		if ($resdoc>0 && $objectdoc->statut >= 1)
		{
			$newReport[$id] = $aReport;
			$_SESSION['reporttransf'] = serialize($newReport);
			$_SESSION['reporttransfh'] = serialize($newReporth);
			print '<div class="tabsAction">';
			//documents
			print '<table width="100%"><tr><td width="50%" valign="top">';
			print '<a name="builddoc"></a>';
			$filename=dol_sanitizeFileName($objectdoc->ref);
			//cambiando de nombre al reporte
			$filedir   =$conf->almacen->dir_output . '/' . dol_sanitizeFileName($objectdoc->ref);
			$urlsource =$_SERVER['PHP_SELF'].'?id='.$id;
			$genallowed=$user->rights->almacen->creardoc;
			$delallowed=$user->rights->almacen->deldoc;
			$objectdoc->modelpdf = $objectdoc->model_pdf;
			//$genallowed=false;
			$delallowed=false;
			//$objectdoc->modelpdf = 'notaingalm';
			print '<br>';
			print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$objectdoc->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
			$somethingshown=$formfile->numoffiles;
			print '</td></tr></table>';

			print "</div>";
		}


	}
	dol_fiche_end();
}
else
{

}




/* ************************************************************************** */
/*                                                                            */
/* Barre d'action                                                             */
/*                                                                            */
/* ************************************************************************** */


if (empty($action) && $product->id)
{
	print "<div class=\"tabsAction\">\n";

	if ($user->rights->almacen->transf->write)
	{
		//print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$product->id.'&amp;action=correction">'.$langs->trans("StockCorrection").'</a>';
	}

	if ($user->rights->stock->mouvement->creer && $objectdoc->statut == 0)
	{
		//print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$product->id.'&amp;action=transfert">'.$langs->trans("StockMovement").'</a>';
	}

	print '</div>';
}



llxFooter();

$db->close();
?>
