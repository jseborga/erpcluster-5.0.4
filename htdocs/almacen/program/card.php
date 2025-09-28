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
 *   	\file       almacen/stockprogram_card.php
 *		\ingroup    almacen
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-02-01 15:02
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
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
dol_include_once('/user/class/user.class.php');
dol_include_once('/almacen/class/stockprogramext.class.php');
dol_include_once('/almacen/class/stockprogramdetext.class.php');
dol_include_once('/almacen/class/entrepotext.class.php');

require_once DOL_DOCUMENT_ROOT.'/almacen/class/transf.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtempext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtype.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementdocext.class.php';


// Load traductions files requiredby by page
$langs->load("almacen");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$idr		= GETPOST('idr','int');
$print      = GETPOST('print','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$confirm    = GETPOST('confirm');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_entrepot=GETPOST('search_fk_entrepot','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_fk_user_val=GETPOST('search_fk_user_val','int');
$search_status_print=GETPOST('search_status_print','int');
$search_status=GETPOST('search_status','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if (!$user->rights->almacen->program->read)
{
	accessforbidden();
}
//$result = restrictedArea($user, 'almacen', $id);


$object = new Stockprogramext($db);
$objectdet = new Stockprogramdetext($db);
$extrafields = new ExtraFields($db);
$objUser = new User($db);
$objEntrepot = new Entrepotext($db);

$objStocktemp = new Stockmouvementtempext($db);
$objStockadd = new Stockmouvementadd($db);
$objStockdoc = new Stockmouvementdocext($db);
$objProduct = new Product($db);






// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('stockprogram'));



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
$now = dol_now();
if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/almacen/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}

	// Transfer stock from a warehouse to another warehouse
	if ($action == "confirm_transfer_stock" && $confirm == 'yes' && $user->rights->almacen->program->gen)
	{
		//preparamos la transferencia por almacen destino
		$filter = " AND t.fk_stock_program = ".$id;
		$aTransfer = array();
		$aTransferid = array();
		$res = $objectdet->fetchAll('','',0,0,array(),'AND',$filter);
		if ($res>0)
		{
			$lines = $objectdet->lines;
			foreach ($lines AS $j => $line)
			{
				$aTransfer[$line->fk_entrepot_end][$line->fk_product] = $line->qty;
				$aTransferid[$line->fk_entrepot_end][$line->fk_product] = $line->id;
			}
		}
		$idr = $object->fk_entrepot;
		$idd = $transf['idd'];
		$datem = $object->datep;
		$label = $object->label;
		$fk_type_mov = $object->fk_type_movement;
		$codeinv = '';
		$db->begin();
		if (! $error)
		{
			foreach ($aTransfer AS $idd => $data)
			{
				if ($idr <> $idd)
				{
					//buscamos la numeracion para la transferencia
					$numref = $objStockdoc->getNextNumRef($soc);

					//creamos el registro principal
					$objStockdoc->ref = $numref;
					$objStockdoc->entity = $conf->entity;
					$objStockdoc->fk_entrepot_from = $idr;
					$objStockdoc->fk_entrepot_to = $idd;
					$objStockdoc->fk_type_mov = $fk_type_mov;
					$objStockdoc->datem = $datem;
					$objStockdoc->label = $label;
					$objStockdoc->date_create = $now;
					$objStockdoc->date_mod = $now;
					$objStockdoc->tms = $now;
					$objStockdoc->model_pdf = 'transfer';
					$objStockdoc->fk_user_create = $user->id;
					$objStockdoc->fk_user_mod = $user->id;
					$objStockdoc->statut = 1;
					$resdoc = $objStockdoc->create($user);
					if ($resdoc <=0)
					{
						setEventMessages($objStockdoc->error,$objStockdoc->errors,'errors');
						$error++;
					}
					if (!$error)
					{
						foreach ((array) $data AS $fk_product => $qty)
						{
							$nbpiece = $qty;
							if (is_numeric($nbpiece) && $fk_product)
							{
								//$product = new Product($db);
								$transf = new Transf($db);
								$result=$transf->fetch($fk_product);
								if ($nbpiece <>0)
								{
									$transf->load_stock();
									//Load array product->stock_warehouse

									// Define value of products moved
									$pricesrc=0;
									if (isset($product->stock_warehouse[$idr]->pmp))
										$pricesrc=$transf->stock_warehouse[$idr]->pmp;
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
									}
									//vamos a actualizar el objectdet

									$ress = $objectdet->fetch($aTransferid[$idd][$fk_product]);
									//echo ' <br>actualizandoid '.$aTransferid[$idd][$fk_product].' ress '.$ress;
									if ($ress>0)
									{
										$objectdet->fk_object = $result1;
										$objectdet->object = 'stockmouvementtmp';
										$resup = $objectdet->update($user);
										if ($resup <=0)
										{
											$error++;
											setEventMessages($objectdet->error,$objectdet->errors,'errors');
										}
									}
								}
							}
							else
							{
								$error++;
								setEventMessages($langs->trans('Algo pasa con la cantidad y product').' '.$nbpiece.' '.$fk_product,null,'errors');
							}
						}
					}
				}
				else
				{
					$error++;
					setEventMessages($langs->trans('Algo pasa con el almacen origen destino').' or '.$idr.' des '.$idd,null,'errors');
				}
			}
		}

		//echo '<hr>finerr'.$error;exit;
		if (!$error)
		{
			//cambiamos de estado
			$object->status = 2;
			$res = $object->update($user);
			if ($res<=0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
		}
		if (!$error)
		{


			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			$db->commit();
			$urltogo=$backtopage?$backtopage:dol_buildpath('/almacen/program/card.php?id='.$id.'&$action=print&print=1',1);
			header("Location: ".$urltogo);
			exit;
		}
		else
		{
			setEventMessages($langs->trans('Errors'),null, 'errors');
			$db->rollback();
		}
		$action = '';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/almacen/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$object->entity=GETPOST('entity','int');
		$object->ref=GETPOST('ref','alpha');
		$object->fk_entrepot=GETPOST('fk_entrepot','int');
		$object->fk_user_create=GETPOST('fk_user_create','int');
		$object->fk_user_mod=GETPOST('fk_user_mod','int');
		$object->fk_user_val=GETPOST('fk_user_val','int');
		$object->status_print=GETPOST('status_print','int');
		$object->status=GETPOST('status','int');



		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/almacen/program/list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
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
	}

	// Action to add record
	if ($action == 'clone' && $user->rights->almacen->program->write && $id)
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/almacen/program/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;
		$db->begin();
		$result = $object->createFromCloneadd($id);
		if ($result<=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (!$error)
		{
			//vamos a recuperar y clonar cada registro det
			$filter = " AND t.fk_stock_program = ".$id;
			$res = $objectdet->fetchAll('','',0,0,array(),'AND',$filter);
			if ($res>0)
			{
				$lines = $objectdet->lines;
				foreach ($lines AS $j => $line)
				{
					$objectdet->fk_stock_program = $result;
					$objectdet->fk_entrepot_end=$line->fk_entrepot_end;
					$objectdet->fk_product=$line->fk_product;
					$objectdet->qty=$line->qty;
					$objectdet->fk_user_create=$user->id;
					$objectdet->fk_user_mod=$user->id;
					$objectdet->datec=$now;
					$objectdet->datem=$now;
					$objectdet->tms=$now;
					$objectdet->status=1;
					$res = $objectdet->create($user);
					if ($res<=0)
					{
						$error++;
						setEventMessages($objectdet->error,$objectdet->errors,'errors');
					}
				}
			}
		}
		if (!$error)
		{
			$db->commit();
			// cloneok OK
			setEventMessages("Successfulcloning", null, 'mesgs');
			header("Location: ".dol_buildpath('/almacen/program/card.php?id='.$result,1));
			exit;
		}
		$action = '';
	}
	// Action to update record
	if ($action == 'update')
	{
		$error=0;


		$object->entity=GETPOST('entity','int');
		$object->ref=GETPOST('ref','alpha');
		$object->fk_entrepot=GETPOST('fk_entrepot','int');
		$object->fk_user_create=GETPOST('fk_user_create','int');
		$object->fk_user_mod=GETPOST('fk_user_mod','int');
		$object->fk_user_val=GETPOST('fk_user_val','int');
		$object->status_print=GETPOST('status_print','int');
		$object->status=GETPOST('status','int');



		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
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
	if ($action == 'print')
	{
		$error=0;

		$object->fk_user_mod=GETPOST('fk_user_mod','int');
		$object->status_print=1;

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
				$action='';
			}
		}
		else
		{
			$action='';
		}
	}
	// Action to delete
	if ($action == 'confirm_delete' && $user->rights->almacen->program->del)
	{
		$db->begin();
		$filter = " AND t.fk_stock_program = ".$id;
		$res = $objectdet->fetchAll('','',0,0,array(),'AND',$filter);
		if ($res>0)
		{
			$lines = $objectdet->lines;
			foreach ($lines AS $j => $line)
			{
				$res = $objectdet->fetch($line->id);
				if ($res>0)
				{
					$res = $objectdet->delete($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($objectdet->error,$objectdet->errors,'errors');
					}
				}
			}
		}
		if (!$error)
		{
			//vamos a eliminar la programacion
			$result=$object->delete($user);
			if ($result <=0)
			{
				$error++;
				setEventMessages($objectdet->error,$objectdet->errors,'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/almacen/program/list.php',1));
			exit;
		}
		else
		{
			$db->rollback();
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
	// Action to validate
	if ($action == 'confirm_validate' && $user->rights->almacen->program->val)
	{
		$object->status = 1;
		$object->fk_user_val = $user->id;
		$object->fk_user_mod = $user->id;
		$object->datev = $now;
		$object->datem = $now;
		$result=$object->update($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordValidated", null, 'mesgs');
			header("Location: ".dol_buildpath('/almacen/program/card.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}

	//registro actualizacion y borrado de stock_program_det
	// Action to update record
	if ($action == 'updatedet')
	{
		$objectdet->fetch($idr);
		$error=0;

		$objectdet->fk_stock_program=$id;
		$objectdet->fk_entrepot_end=GETPOST('fk_entrepot_end','int');
		$objectdet->fk_product=GETPOST('fk_product','int');
		$objectdet->qty=GETPOST('qty','alpha');
		$objectdet->fk_user_mod=$user->id;
		$objectdet->datem=$now;
		$objectdet->status=1;


		if (! $error)
		{
			$result=$objectdet->update($user);
			if ($result > 0)
			{
				$search_fk_product = '';
				$action='';
				header("Location: ".dol_buildpath('/almacen/program/card.php?id='.$id,1));
				exit;
			}
			else
			{
				// Creation KO
				if (! empty($objectdet->errors)) setEventMessages(null, $objectdet->errors, 'errors');
				else setEventMessages($objectdet->error, null, 'errors');
				$action='editdet';
			}
		}
		else
		{
			$action='editdet';
		}

	}

	// Action to delete
	if ($action == 'confirm_deletedet')
	{
		$objectdet->fetch($idr);
		$result=$objectdet->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/almacen/program/card.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($objectdet->errors)) setEventMessages(null, $objectdet->errors, 'errors');
			else setEventMessages($objectdet->error, null, 'errors');
		}
	}
}


/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Programationtransfer'),'');

$form=new Formv($db);
$formproduct=new FormProduct($db);

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

if ($object->status==2 && $print)
{
	include DOL_DOCUMENT_ROOT.'/almacen/program/tpl/popupticket.php';
}
// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("Newprogramation"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.GETPOST('entity').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_entrepot").'</td><td><input class="flat" type="text" name="fk_entrepot" value="'.GETPOST('fk_entrepot').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_val").'</td><td><input class="flat" type="text" name="fk_user_val" value="'.GETPOST('fk_user_val').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus_print").'</td><td><input class="flat" type="text" name="status_print" value="'.GETPOST('status_print').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("Programationtransfer"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$object->entity.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_entrepot").'</td><td><input class="flat" type="text" name="fk_entrepot" value="'.$object->fk_entrepot.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_val").'</td><td><input class="flat" type="text" name="fk_user_val" value="'.$object->fk_user_val.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus_print").'</td><td><input class="flat" type="text" name="status_print" value="'.$object->status_print.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$object->status.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $object->fetch_optionals($object->id, $extralabels);


	print load_fiche_titre($langs->trans("Programationtransfer"));

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Deleteprogramationtransfer'), $langs->trans('ConfirmDeleteprogramationtransfer'), 'confirm_delete', '', 0, 2);
		print $formconfirm;
	}
	if ($action == 'validate') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Validateprogramationtransfer'), $langs->trans('ConfirmValidateprogramationtransfer'), 'confirm_validate', '', 0, 2);
		print $formconfirm;
	}
	if ($action == 'transfer_stock') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Generatetransfer'), $langs->trans('ConfirmGeneratetransfer'), 'confirm_transfer_stock', '', 0, 2);
		print $formconfirm;
	}
	if ($action == 'deletedet') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id.'&idr='.$idr, $langs->trans('Deleteprogramationtransferdet'), $langs->trans('ConfirmDeleteprogramationtransferdet'), 'confirm_deletedet', '', 0, 2);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	//
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	$objEntrepot->fetch($object->fk_entrepot);
	print '<tr><td>'.$langs->trans("Fromstorage").'</td><td>'.$objEntrepot->getNomUrl(1).'</td></tr>';
	$objUser->fetch($object->fk_user_create);
	print '<tr><td>'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	$objUser->fetch($object->fk_user_mod);
	print '<tr><td>'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	if ($object->fk_user_val>0)
	{
		$objUser->fetch($object->fk_user_val);
		print '<tr><td>'.$langs->trans("Fieldfk_user_val").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	}
	print '<tr><td>'.$langs->trans("Fieldstatus_print").'</td><td>'.($object->status_print?img_picto('','switch_on'):img_picto('','switch_off')).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$object->getLibStatutadd(6).'</td></tr>';

	print '</table>';

	dol_fiche_end();

	include DOL_DOCUMENT_ROOT.'/almacen/program/tpl/list.tpl.php';
	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->almacen->program->write && $object->status >= 1)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=clone">'.$langs->trans("Clone").'</a></div>'."\n";
		}
		if ($user->rights->almacen->program->gen && $object->status == 2)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;print=1">'.$langs->trans("Printticket").'</a></div>'."\n";
		}
		if ($user->rights->almacen->program->val && $object->status==0)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans("Validate").'</a></div>'."\n";
		}

		if ($user->rights->almacen->program->del && $object->status==0)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
		if ($user->rights->almacen->program->gen && $object->status==1)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=transfer_stock">'.$langs->trans('Generatetransfer').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('stockprogram'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


// End of page
llxFooter();
$db->close();
