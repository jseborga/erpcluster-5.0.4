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
 *   	\file       priceunits/budget_card.php
 *		\ingroup    priceunits
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-10-04 12:08
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
dol_include_once('/priceunits/class/budget.class.php');
dol_include_once('/user/class/user.class.php');
dol_include_once('/product/class/product.class.php');
dol_include_once('/priceunits/lib/priceunits.lib.php');
dol_include_once('/priceunits/class/items.class.php');
dol_include_once('/priceunits/class/budgettaskext.class.php');
dol_include_once('/priceunits/class/budgettaskaddext.class.php');
dol_include_once('/priceunits/class/budgettaskresource.class.php');
dol_include_once('/priceunits/class/pustructureext.class.php');
dol_include_once('/priceunits/class/html.formv.class.php');
dol_include_once('/priceunits/lib/calcunit.lib.php');
dol_include_once('/categories/class/categorie.class.php');
dol_include_once('/priceunits/class/puoperatorext.class.php');
dol_include_once('/priceunits/class/pustructureext.class.php');
dol_include_once('/priceunits/class/pustructuredetext.class.php');
dol_include_once('/priceunits/class/productbudgetext.class.php');
dol_include_once('/priceunits/class/putypestructureext.class.php');

// Load traductions files requiredby by page
$langs->load("priceunits");
$langs->load("other");
$langs->load("products");
if (! empty($conf->stock->enabled)) $langs->load("stocks");
if (! empty($conf->facture->enabled)) $langs->load("bills");
if (! empty($conf->productbatch->enabled)) $langs->load("productbatch");
// Get parameters
$id			= GETPOST('id','int');
$idg		= GETPOST('idg','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$sesbudget = unserialize($_SESSION['sesbudget']);
//guardamos en session si se selecciona
if (isset($_POST['id'])||isset($_GET['id']))
	$sesbudget['id_presup'] = GETPOST('id');
$id = $sesbudget['id_presup'];
if (isset($_POST['idg'])||isset($_GET['idg']))
	$sesbudget[$id]['idg'] = GETPOST('idg');

$idg = $sesbudget[$id]['idg'];

$search_fk_soc=GETPOST('search_fk_soc','int');
$search_ref=GETPOST('search_ref','alpha');
$search_entity=GETPOST('search_entity','int');
$search_title=GETPOST('search_title','alpha');
$search_description=GETPOST('search_description','alpha');
$search_fk_user_creat=GETPOST('search_fk_user_creat','int');
$search_public=GETPOST('search_public','int');
$search_fk_statut=GETPOST('search_fk_statut','int');
$search_fk_opp_status=GETPOST('search_fk_opp_status','int');
$search_opp_percent=GETPOST('search_opp_percent','alpha');
$search_fk_user_close=GETPOST('search_fk_user_close','int');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_opp_amount=GETPOST('search_opp_amount','alpha');
$search_budget_amount=GETPOST('search_budget_amount','alpha');
$search_model_pdf=GETPOST('search_model_pdf','alpha');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if ($page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.rowid"; // Set here default search field
if (! $sortorder) $sortorder="ASC";
$params='';
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Budget($db);
$objecttmp=new Budget($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objuser 		= new User($db);
$objectdet 		= new Budgettaskext($db);
$objectdettmp 	= new Budgettaskext($db);
$objectdettmp0	= new Budgettaskext($db);
$objectdetadd 	= new Budgettaskaddext($db);
$objectdetaddtmp= new Budgettaskaddext($db);
$objectbtr 		= new Budgettaskresource($db);
$objectbtrtmp	= new Budgettaskresource($db);
$pustr 			= new Pustructureext($db);
$objstr			= new Pustructureext($db);
$objstrdet		= new Pustructuredetext($db);
$objprodb		= new Productbudgetext($db);
$objprodbtmp	= new Productbudgetext($db);
$items 			= new Items($db);
$itemstmp 		= new Items($db);
$product 		= new Product($db);
$categorie 		= new Categorie($db);
$typestr		= new Putypestructureext($db);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budget'));
$extrafields = new ExtraFields($db);

//armamos la estructura para futuras acciones
//if (!isset($_SESSION['aStrbudget']))
//{
if ($id)
{
	$filter = array(1=>1);
	$filterstatic = " AND t.type_structure = '".$object->type_structure."'";
	$filterstatic.= " AND t.fk_categorie > 0";
		//$filterstatic.= " AND t.ordby = 1";
	$pustr->fetchAll('ASC', 'ordby', 0, 0, $filter, 'AND',$filterstatic,false);
	foreach((array) $pustr->lines AS $i => $linestr)
	{
		$aStrid[$linestr->id] = $linestr->id;
		$aStridcat[$linestr->id] = $linestr->fk_categorie;
		$aStrcatid[$linestr->fk_categorie] = $linestr->id;
		$aStrcatcode[$linestr->fk_categorie] = $linestr->ref;
		$aStr[$linestr->ref] = $linestr->ref;
		$aStrref[$linestr->ref] = $linestr->detail;
		$aStrlabel[$linestr->fk_categorie] = $linestr->detail;
	}
	$_SESSION['aStrbudget'] = serialize(array($id=>array('aStrid'=>$aStrid,'aStridcat'=>$aStridcat,'aStrcatid'=>$aStrcatid,'aStr'=>$aStr,'aStrref'=>$aStrref,'aStrlabel'=>$aStrlabel,'aStrcatcode'=>$aStrcatcode)));
		//$_SESSION['aStrref'] = serialize($aStrref);
		//$_SESSION['aStrlabel'] = serialize($aStrlabel);
}
//}
$aStrbudget = unserialize($_SESSION['aStrbudget']);

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
	$objectdetn = new Budgettaskext($db);
	//clone budget
	$aSt = array();
	if ($action == 'confirm_clon')
	{
		//creando una copia del presupuesto
		//verificamos el maximo numero de version existente
		$filterstatic = " AND t.ref = '".$object->ref."'";
		$filterstatic.= " AND t.version = '".GETPOST('version')."'";
		$res = $object->fetchAll('','', 0, 0, array(1=>1), 'AND',$filterstatic);		
		if ($res>0)
		{
			$error++;
			setEventMessages($langs->trans('The selected version exists'),null,'errors');
		}
		elseif($res<0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');			
		}
		if (!$error)
		{
			$version = GETPOST('version');
			$object->id = 0;
			$object->datec = dol_now();
			$object->tms = dol_now();
			$object->version = $version;
			$object->fk_user_creat = $user->id;
			$nid = $object->create($user);
			if ($nid<=0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
			else
			{
				//recuperamos todos los grupos e items
				$filterstatic = " AND t.fk_budget = ".$id;
				//$filterstatic.= " AND t.fk_task_parent = ".$idg;
				$res = $objectdet->fetchAll('ASC', 'ref', 0, 0, $filter, 'AND',$filterstatic);		
				$tasks = $objectdet->lines;
				foreach ((array) $tasks AS $t => $task)
				{
					//recorremos cada grupo o item
					$idg = $task->fk_task_parent;
					//para obtener el parent debemos mejorar 
					if (!empty($task->fk_task_parent))
					{
						$aRef = explode('.',$task->ref);
						$len = count($aRef)-1;
						if ($len > 0)
						{
							for ($j=0; $j < $len; $j++)
							{
								if ($newref) $newref = '.';
								$newref = $aRef[$j];
							}
						}
						$idg = $aSt[$newref];
					}
					$fk = $task->id;
				//recuperamos el item/tarea
					$objectdet->fetch($fk);
					$objectdetadd->fetch(0,$fk);

					$objectdet->id 			= 0;
					$objectdet->fk_budget 	= $nid;
					$objectdet->fk_task_parent = $idg;
					$objectdet->datec 		= dol_now();
					$objectdet->tms 		= dol_now();
					$fknew = $objectdet->create($user);
					$aSt[$objectdet->ref] = $fknew;
					if ($fknew<=0)
					{
						$error++;
						setEventMessages($objectdet->error,$objectdet->errors,'errors');
					}
					if (!$error)
					{
					//agregamos en la tabla adicional
						$objectdetadd->id = 0;
						$objectdetadd->fk_budget_task = $fknew;
						$objectdetadd->level = $level+0;
						$residadd = $objectdetadd->create($user);
						if ($residadd<=0)
						{
							$error++;
							setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
						}

						if (!$error)
						{
						//registramos todos los recursos del item
						//recuperamos los recursos del item original
							$filtertmp = " AND t.fk_budget_task = ".$fk;
							$resbtr = $objectbtrtmp->fetchAll('ASC','rang',0,0,array(1=>1),'AND',$filtertmp);
							if ($resbtr>0)
							{
								$rang = 1;
								foreach ($objectbtrtmp->lines AS $j => $line)
								{
								//recuperamos el registro para crear como nuevo
									$objectbtr->fetch($line->id);
								//buscamos el producto en product_budget
									$respb = $objprodb->fetch($line->fk_product_budget);
									if ($respb>0)
									{
									//convertimos el ref para el nuevo budget
										$aRef = explode('|',$objprodb->ref);
										$ref = $id.'|'.$aRef[1];
									//buscamos el producto con ref y el fk_budget
										$filterdb = " AND t.fk_budget = ".$nid;
										$filterdb.= " AND t.ref = '".trim($ref)."'";
										$resdb = $objprodbtmp->fetchAll('','',0,0,array(1=>1),'AND',$filterdb,true);
										if ($resdb>0)
										{
											$objectbtr->fk_product_budget = $objprodbtmp->id;
										}
										else
										{
										//creamos el nuevo registro de product_budget
											$objprodb->id = 0;
											$objprodb->fk_budget = $nid;
											$objprodb->ref = $ref;
											$objprodb->fk_user_create = $user->id;
											$objprodb->fk_user_mod = $user->id;
											$objprodb->date_create = dol_now();
											$objprodb->date_mod = dol_now();
											$objprodb->tms = dol_now();
											$resdb = $objprodb->create($user);
											if ($resdb<=0)
											{
												$error++;
												setEventMessages($objprodb->error,$objprodb->errors,'errors');
											}
											$objectbtr->fk_product_budget = $resdb;
										}
									//armamos el nuevo
										$objectbtr->id = 0;
										$objectbtr->fk_budget_task = $fknew;
										$objectbtr->ref = '(PROV)';
										$objectbtr->fk_user_create = $user->id;
										$objectbtr->fk_user_mod = $user->id;
										$objectbtr->rang = $rang;
										$objectbtr->date_create = dol_now();
										$objectbtr->date_mod = dol_now();
										$objectbtr->tms = dol_now();
										$resbtr = $objectbtr->create($user);
										if ($resbtr<=0)
										{
											$error++;
											setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
										}
									//actualizamos el registro para cambiar el ref
										$objectbtr->ref.= $objectbtr->id;
										$resup = $objectbtr->update($user);
										if ($resup<=0)
										{
											$error++;
											setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
										}
										$rang++;
									}
									else
									{
									//no se hace nada
									}
								}
							}
						}
					}
				}
				if ($error)exit;
				if (!$error)
				{
					$db->commit();
					setEventMessages($langs->trans('Clonsucessfull'),null,'mesgs');
					header("Location: ".dol_buildpath('/priceunits/budget/card.php?id='.$nid,1));
					exit;
				}
				else
				{
					$db->rollback();
					$action = 'viewit';
				}
			}
		}
	}



	//import-group
	if ($action == 'import_group')
	{
		print_r($_POST);
		$sel = GETPOST('sel');
		$fk_budget = GETPOST('fk_budget');
		$idg = GETPOST('idg','int');

		$db->begin();		
		foreach ((array) $sel AS $fk => $value)
		{

			//recuperamos el item/tarea
			$objectdet->fetch($fk);
			$objectdetadd->fetch(0,$fk);
			//recuperamos los items que tiene el grupo seleccionado
			$filterstatic = " AND t.fk_budget = ".$id;
			$filterstatic.= " AND t.fk_task_parent = ".$idg;
			$res = $objectdet->fetchAll($sortorder, $sortfield, 0, 0, $filter, 'AND',$filterstatic);		
			$max = $objectdettmp0->max_group($id,$idg,0);
			if ($idg>0)
			{
				$objectdettmp->fetch($idg);
				$objectdetaddtmp->fetch(0,$idg);
				//echo '<hr>numero gruipo '.$objectdettmp->ref;
				$level = $objectdetaddtmp->level+1;
				$max = $objectdettmp->ref.'.'.$max;
			}
			$objectdet->id = 0;
			$objectdet->fk_budget = $id;
			$objectdet->ref = $max;
			$objectdet->fk_task_parent = $idg;
			$objectdet->datec = dol_now();
			$objectdet->tms = dol_now();
			$fknew = $objectdet->create($user);
			if ($fknew<=0)
			{
				$error++;
				setEventMessages($objectdet->error,$objectdet->errors,'errors');
			}
			if (!$error)
			{
				//agregamos en la tabla adicional
				$objectdetadd->id = 0;
				$objectdetadd->fk_budget_task = $fknew;
				$objectdetadd->level = $level+0;
				$residadd = $objectdetadd->create($user);
				if ($residadd<=0)
				{
					$error++;
					setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
				}
				if (!$error)
				{
					//registramos todos los recursos del item
					//recuperamos los recursos del item original
					$filtertmp = " AND t.fk_budget_task = ".$fk;
					$resbtr = $objectbtrtmp->fetchAll('ASC','rang',0,0,array(1=>1),'AND',$filtertmp);
					if ($resbtr>0)
					{
						$rang = 1;
						foreach ($objectbtrtmp->lines AS $j => $line)
						{
							//recuperamos el registro para crear como nuevo
							$objectbtr->fetch($line->id);
							//buscamos el producto en product_budget
							$respb = $objprodb->fetch($line->fk_product_budget);
							if ($respb>0)
							{
								//convertimos el ref para el nuevo budget
								$aRef = explode('|',$objprodb->ref);
								$ref = $id.'|'.$aRef[1];
								//buscamos el producto con ref y el fk_budget
								$filterdb = " AND t.fk_budget = ".$id;
								$filterdb.= " AND t.ref = '".trim($ref)."'";
								$resdb = $objprodbtmp->fetchAll('','',0,0,array(1=>1),'AND',$filterdb,true);
								if ($resdb>0)
								{
									$objectbtr->fk_product_budget = $objprodbtmp->id;
								}
								else
								{
									//creamos el nuevo registro de product_budget
									$objprodb->id = 0;
									$objprodb->fk_budget = $id;
									$objprodb->ref = $ref;
									$objprodb->fk_user_create = $user->id;
									$objprodb->fk_user_mod = $user->id;
									$objprodb->date_create = dol_now();
									$objprodb->date_mod = dol_now();
									$objprodb->tms = dol_now();
									$resdb = $objprodb->create($user);
									if ($resdb<=0)
									{
										$error++;
										setEventMessages($objprodb->error,$objprodb->errors,'errors');
									}
									$objectbtr->fk_product_budget = $resdb;
								}
								//armamos el nuevo
								$objectbtr->id = 0;
								$objectbtr->fk_budget_task = $fknew;
								$objectbtr->ref = '(PROV)';
								$objectbtr->fk_user_create = $user->id;
								$objectbtr->fk_user_mod = $user->id;
								$objectbtr->rang = $rang;
								$objectbtr->date_create = dol_now();
								$objectbtr->date_mod = dol_now();
								$objectbtr->tms = dol_now();
								$resbtr = $objectbtr->create($user);
								if ($resbtr<=0)
								{
									$error++;
									setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
								}
								//actualizamos el registro para cambiar el ref
								$objectbtr->ref.= $objectbtr->id;
								$resup = $objectbtr->update($user);
								if ($resup<=0)
								{
									$error++;
									setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
								}
								$rang++;
							}
							else
							{
								//no se hace nada
							}
						}
					}
				}
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Importsucessfull'),null,'mesgs');
			header("Location: ".dol_buildpath('/priceunits/budget/card.php?id='.$id.'&action=viewit',1));
			exit;
		}
		else
		{
			$db->rollback();
			$action = 'viewit';
		}
	}

	//importresurce
	if ($action == 'import_resource')
	{
		//$_POST = unserialize($_SESSION['aPost']);
		$sel = GETPOST('sel');
		$fk_budget = GETPOST('fk_budget');
		$db->begin();
		foreach ((array) $sel AS $fk => $value)
		{
			$res = $objprodb->fetch($fk);
			if ($res>0)
			{
				$objprodb->id = 0;
				$objprodb->fk_budget = $id;
				$aRef = explode('|',$objprodb->ref);
				$objprodb->ref = $id.'|'.$aRef[1];
				$resadd = $objprodb->create($user);
				if ($resadd<=0)
				{
					$error++;
					setEventMessages($objprodb->error,$objprodb->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages($objprodb->error,$objprodb->errors,'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Importsucessfull'),null,'mesgs');
			header("Location: ".dol_buildpath('/priceunits/budget/card.php?id='.$id.'&action=viewre',1));
			exit;
		}
		else
		{
			$db->rollback();
			$action = 'viewre';
		}
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/priceunits/budget/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$object->fk_soc=GETPOST('fk_soc','int')+0;
		$object->fk_budget_parent = -1;
		$object->ref=GETPOST('ref','alpha');
		$object->entity=$conf->entity;
		$object->title=GETPOST('title','alpha');
		$object->type_structure=GETPOST('type_structure','alpha');
		$object->description=GETPOST('description','alpha');
		$object->fk_user_creat=$user->id;
		$object->fk_user_valid=0;
		$object->public=0;
		$object->version = 0;
		$object->fk_statut=0;
		$object->fk_opp_status=0;
		$object->opp_percent=0;
		$object->fk_user_close=0;
		$object->note_private='';
		$object->note_public='';
		$object->opp_amount=0;
		$object->budget_amount=0;
		$object->model_pdf='budget';
		$object->rang=0;
		$object->datec = dol_now();
		$object->dateo = dol_now();
		$object->tms = dol_now();


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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/priceunits/budget/card.php?id='.$result,1);
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

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;


		$object->ref=GETPOST('ref','alpha');
		$object->title=GETPOST('title','alpha');
		$object->description=GETPOST('description','alpha');
		$object->type_structure=GETPOST('type_structure','alpha');

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($object->title))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Title")), null, 'errors');
		}
		if (empty($object->type_structure))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Typestructure")), null, 'errors');
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
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/priceunits/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}

	if ($action == 'additem')
	{
		$fk_item 	 	= GETPOST('itemid','int');
		$search_itemid 	= GETPOST('search_itemid','alpha');
		$refsearch 	 	= GETPOST('refsearch');
		$subaction 	 	= GETPOST('subaction');
		$ref 			= GETPOST('ref');
		//verificamos la numeracion que se dara
		$fk_father		= GETPOST('fk_father');
		$subaction   	= GETPOST('subaction');
		$level = 0;
		$max = $objectdet->max_group($id,($fk_father>0?$fk_father:0),($subaction?0:1));

		if ($fk_father>0)
		{
			$objectdettmp->fetch($fk_father);
			$objectdetaddtmp->fetch(0,$fk_father);
			$level = $objectdetaddtmp->level+1;
			$max = $objectdettmp->ref.'.'.$max;
		}

		//echo '<hr>max '. $max.' level '.$level;exit;
		$db->begin();
		if (empty($fk_item))
		{
			$items->fetch(0,trim($search_itemid));
			if ($items->ref == $search_itemid)
			{
				$fk_item = $items->id;
				$label = $items->detail;
			}
			if (empty($fk_item) && (!empty($refsearch) || !empty(GETPOST('search_itemid'))))
			{
				//agregamos como item nuevo
				$label = ($refsearch?STRTOUPPER($refsearch):STRTOUPPER(GETPOST('search_itemid')));
				$items->entity=$conf->entity;
				$items->ref='(PROV)';
				$items->ref_ext='';
				$items->fk_user_create=$user->id;
				$items->fk_user_mod=$user->id;
				$items->fk_type_item=GETPOST('fk_type_item','int')+0;
				$items->detail=$refsearch;
				$items->fk_unit=GETPOST('unitid')+0;
				$items->especification='';
				$items->plane='';
				$items->amount=GETPOST('amount','int')+0;
				$items->date_create = dol_now();
				$items->status=0;
				$fk_item = $items->create($user);
				if ($fk_item>0)
				{
					$itemstmp->fetch($fk_item);
					$itemstmp->ref = '(PROV)'.$itemstmp->id;
					$resup = $itemstmp->update($user);
					if ($resup <=0)
					{
						$error++;
						$action = 'viewit';
						setEventMessages($itemstmp->error,$itemstmp->errors,'errors');
					}
				}
				else
				{
					$error++;
					$action = 'viewit';
					setEventMessages($items->error,$items->errors,'errors');
				}
				$fk_item = 0;
			}
			else
			{
				$label = ($refsearch?STRTOUPPER($refsearch):STRTOUPPER(GETPOST('search_itemid')));
			}
		}
		else
		{
			$items->fetch($fk_item);
			if ($items->id == $fk_item)
				$label = $items->detail;
			else
				$label = ($refsearch?STRTOUPPER($refsearch):STRTOUPPER(GETPOST('search_itemid')));
		}
		$objectdet->fk_budget = $object->id;
		$objectdet->fk_task = $fk_item+0;
		$objectdet->entity = $object->entity;
		$objectdet->ref = $max;
		$objectdet->fk_task_parent = GETPOST('fk_father')+0;
		$objectdet->datec = dol_now();
		$objectdet->tms = dol_now();
		$objectdet->label = $label;
		$objectdet->fk_statut = 0;

		$resdet = $objectdet->create($user);
		if ($resdet<=0)
		{
			$error++;
			$action = 'viewit';
			setEventMessages($objectdet->error,$objectdet->errors,'errors');
		}
		else
		{
			//$objectdettmp = new Budgettask($db);
			//$objectdettmp->fetch($resdet);
			//$objectdettmp->ref = '(PROV)'.$objectdettmp->id;
			//$restmp = $objectdettmp->update($user);
			//if ($restmp <=0)
			//{
			//	$error++;
			//	$action = 'viewit';
			//	setEventMessages($objectdettmp->error,$objectdettmp->errors,'errors');	
			//}
		}
		$objectdetadd->fk_budget_task = $resdet;
		$objectdetadd->level = $level+0;	
		$objectdetadd->c_grupo = GETPOST('c_grupo');
		$objectdetadd->fk_unit = GETPOST('unitid')+0;
		$objectdetadd->unit_budget = GETPOST('quant','int')+0;
		$objectdetadd->unit_amount = GETPOST('amount','int')+0;
		$objectdetadd->fk_user_create = $user->id;
		$objectdetadd->fk_user_mod = $user->id;
		$objectdetadd->date_create = dol_now();
		$objectdetadd->tms = dol_now();
		$objectdetadd->status = 0;
		$resdetadd=$objectdetadd->create($user);

		if ($resdetadd>0)
		{
			unset($_POST['ref']);
			unset($_POST['label']);
			//setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			$action = (GETPOST('c_grupo')?'viewgr':'viewit');
		}
		else
		{
			$error++;
			setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
			$action = (GETPOST('c_grupo')?'creategr':'viewit');
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			unset($_POST['refsearch']);
			unset($_POST['itemid']);
			unset($_POST['search_itemid']);
		}
		else
			$db->rollback();
	}
	if ($action == 'updateitem' && $user->rights->priceunits->budi->mod)
	{
		$error = 0;
		$res = $objectdet->fetch(GETPOST('idr'));
		if ($res <= 0)
		{
			$error++;
			setEventMessages($objectdet->error,$objectdet->errors,'errors');
		}
		$res = $objectdetadd->fetch(0,$objectdet->id);
		if ($res <= 0)
		{
			$error++;
			setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
		}

		$fk_item 	 	= GETPOST('itemid','int');
		$search_itemid 	= GETPOST('search_itemid','alpha');
		$refsearch 	 	= GETPOST('refsearch');
		$subaction 	 	= GETPOST('subaction');
		$ref 			= GETPOST('ref');
		//verificamos la numeracion que se dara
		$fk_father		= GETPOST('fk_father');
		$subaction   	= GETPOST('subaction');

		//echo '<hr>max '. $max.' level '.$level;exit;
		$db->begin();
		if (empty($fk_item))
		{
			$items->fetch(0,trim($search_itemid));
			if ($items->ref == $search_itemid)
			{
				$fk_item = $items->id;
				$label = $items->detail;
			}
			if (empty($fk_item) && !empty($refsearch))
			{
				//agregamos como item nuevo
				$label = STRTOUPPER($refsearch);
				$items->entity=$conf->entity;
				$items->ref='(PROV)';
				$items->ref_ext='';
				$items->fk_user_create=$user->id;
				$items->fk_user_mod=$user->id;
				$items->fk_type_item=GETPOST('fk_type_item','int')+0;
				$items->detail=$refsearch;
				$items->fk_unit=GETPOST('unitid')+0;
				$items->especification='';
				$items->plane='';
				$items->amount=GETPOST('amount','int')+0;
				$items->date_create = dol_now();
				$items->status=0;
				$fk_item = $items->create($user);
				if ($fk_item>0)
				{
					$itemstmp->fetch($fk_item);
					$itemstmp->ref = '(PROV)'.$itemstmp->id;
					$resup = $itemstmp->update($user);
					if ($resup <=0)
					{
						$error++;
						$action = 'viewit';
						setEventMessages($itemstmp->error,$itemstmp->errors,'errors');
					}
				}
				else
				{
					$error++;
					$action = 'viewit';
					setEventMessages($items->error,$items->errors,'errors');
				}
				$fk_item = 0;
			}
		}
		else
		{
			$items->fetch($fk_item);
			if ($items->id == $fk_item)
				$label = $items->detail;
		}
		if (empty($fk_item)) $label = GETPOST('search_itemid');

		if (!$error)
		{
			$objectdet->fk_budget = $object->id;
			$objectdet->fk_task = $fk_item+0;
			//$objectdet->fk_task_parent = GETPOST('fk_father')+0;
			$objectdet->tms = dol_now();
			$objectdet->label = $label;
			//$objectdet->fk_statut = 0;

			$resdet = $objectdet->update($user);
			if ($resdet<=0)
			{
				$error++;
				$action = 'viewit';
				setEventMessages($objectdet->error,$objectdet->errors,'errors');
			}
			//actualizamos en la tabla adicional
			//$objectdetadd->c_grupo = GETPOST('c_grupo');
			$objectdetadd->fk_unit = GETPOST('unitid')+0;
			$objectdetadd->unit_budget = GETPOST('quant','int')+0;
			$objectdetadd->unit_amount = GETPOST('amount','int')+0;
			$objectdetadd->fk_user_mod = $user->id;
			$objectdetadd->tms = dol_now();
			//$objectdetadd->status = 0;
			$resdetadd=$objectdetadd->update($user);
			if ($resdetadd>0)
			{
				unset($_POST['ref']);
				unset($_POST['label']);
				//setEventMessages($langs->trans('Saverecord'),null,'mesgs');
				$action = (GETPOST('c_grupo')?'viewgr':'viewit');
			}
			else
			{
				$error++;
				setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
				$action = (GETPOST('c_grupo')?'creategr':'viewit');
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			unset($_POST['refsearch']);
			unset($_POST['itemid']);
			unset($_POST['search_itemid']);
		}
		else
			$db->rollback();
	}

	if ($action == 'addresource')
	{
		$aStruct = $aStrbudget[$id];

		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/priceunits/budget/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;
		$db->begin();
		//revisamos el producto
		$fk_unit = GETPOST('fk_unit');
		$code_structure = GETPOST('code_structure');
		$price = GETPOST('price');
		if (GETPOST('fk_product_budget'))
		{
			//se selecciono un producto del budget
			$fk_product_budget = GETPOST('fk_product_budget');
			$objprodb->fetch(GETPOST('fk_product_budget'));
			$ref_product = $objprodb->ref;
			$fk_product = $objprodb->fk_product;
			$fk_unit = $objprodb->fk_unit;
			$detail = $objprodb->label;
			$price = $objprodb->amount;
			$code_structure = $objprodb->code_structure;
			$price = $objprodb->amount;
		}
		else
		{
			/* object_prop_getpost_prop */			
			$fk_product = GETPOST('product');
			$detail = GETPOST('refsearch');
			if ($fk_product>0 && $product->fetch($fk_product)>0)
			{
				//buscamos la categoria a la que pertenece
				$detail = $product->label;
				$ref_product = $product->ref;
				$fk_unit = $product->fk_unit;
				$aCat = $categorie->containing($fk_product, 'product', 'id');
				foreach ((array) $aCat AS $i => $fk)
				{
					if ($aStruct['aStrcatcode'][$fk])
						$code_structure = $aStruct['aStrcatcode'][$fk];
				}
			}
			else
			{
				$fk_product = 0;
				$ref_product = '';
				$code_structure = GETPOST('code_structure');
				$detail = GETPOST('search_product');
			}
			$detail = STRTOUPPER($detail);
			//verificamos si existe en producto budget
			$filter = array('UPPER(ref)'=>$detail,'UPPER(label)'=>$detail);
			$filter = array(1=>1);
			$filterstatic = '';
			if ($ref_product)
			{
				$filterstatic = ' AND (';
				$filterstatic.= " UPPER(ref) = '".$ref_product."'";
			}
			if (!empty($filterstatic))
			{
				$filterstatic.= " OR ";
			}
			else
				$filterstatic.= " AND (";
			$filterstatic.= " UPPER(label) = '".$detail."'";
			$filterstatic.= ")";
			$filterstatic.= " AND t.fk_budget =".$object->id;
			$res = $objprodb->fetchAll('','',0,0,$filter,'OR',$filterstatic,true);
			if (empty($res))
			{
				$lUpdate = false;
				if (empty($ref_product)) $lUpdate = true;
				$objprodb->fk_product = $fk_product+0;
				$objprodb->fk_budget = $object->id;
				$objprodb->ref = $object->id.'|'.$ref_product;
				$objprodb->label = $detail;
				$objprodb->fk_unit = $fk_unit;
				$objprodb->code_structure = $code_structure;
				$objprodb->quant = GETPOST('quant')+0;
				$objprodb->amount = $price;
				$objprodb->fk_user_create = $user->id;
				$objprodb->fk_user_mod = $user->id;
				$objprodb->date_create = dol_now();
				$objprodb->date_mod = dol_now();
				$objprodb->tms = dol_now();
				$objprodb->status = 1;
				$fk_product_budget = $objprodb->create($user);
				if ($fk_product_budget<=0)
				{
					setEventMessages($objprodb->error,$objprodb->errors,'errors');
					$error++;
				}
					//actualizamos si lUpdate == true
				if ($lUpdate == true)
				{
					$objprodbtmp->fetch($fk_product_budget);
					$objprodbtmp->ref .= '(PROV)'.$objprodb->id;
					$res = $objprodbtmp->update($user);
					if ($res <=0)
					{
						setEventMessages($objprodbtmp->error,$objprodbtmp->errors,'errors');
						$error++;							
					}
				}
			}
			elseif($res==1)
			{
				$fk_product_budget = $objprodb->id;
				$ref_product = $objprodb->ref;
				$fk_product = $objprodb->fk_product;
				$fk_unit = $objprodb->fk_unit;
				$detail = $objprodb->label;
				$code_structure = $objprodb->code_structure;
			}
			else
			{
				$error++;
				if ($res<0)
					setEventMessages($objprodb->error,$objprodb->errors,'errors');
				else
					setEventMessages($langs->trans('Existe muchos registros'),null,'errors');
			}
		}
		$objectbtr->fk_budget_task = GETPOST('idr');
		$objectbtr->ref = '(PROV)';
		$objectbtr->code_structure = $code_structure;
		$objectbtr->fk_product = $fk_product;
		$objectbtr->detail = $detail;
		$objectbtr->fk_product_budget = $fk_product_budget+0;
		$objectbtr->fk_unit = $fk_unit+0;
		$objectbtr->quant = GETPOST('quant');
		$objectbtr->amount = $price+0;
		$objectbtr->rang = 1;
		$objectbtr->date_create = dol_now();
		$objectbtr->date_mod = dol_now();
		$objectbtr->tms = dol_now();
		$objectbtr->fk_user_create = $user->id;
		$objectbtr->fk_user_mod = $user->id;
		$objectbtr->status = 1;
		$result=$objectbtr->create($user);
		if ($result<=0)
		{
			$error++;
				// Creation KO
			setEventMessages($objectbtr->error, $objectbtr->errors, 'errors');
			$action='viewit';
			$_GET['idr'] = $_POST['idr'];
		}
		if (! $error)
		{
			//actualizamos el ref
			$objectbtr->ref = '(PROV)'.$objectbtr->id;
			$resup = $objectbtr->update($user);
			if ($resup<=0)
			{
				$error++;
				// Creation KO
				setEventMessages($objectbtr->error, $objectbtr->errors, 'errors');
				$action='viewit';
				$_GET['idr'] = $_POST['idr'];
			}
			//procedemos al calculo del costo unitario
			$sumaunit = procedure_calc($id,GETPOST('idr'));
			$objectdetadd->fetch(0,GETPOST('idr'));
			$objectdetadd->unit_amount = $sumaunit * $objectdetadd->unit_budget;
			$resdet = $objectdetadd->update($user);
			if ($resdet<=0)
			{
				$error++;
				setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
			}
			if (!$error)
			{
				setEventMessages($langs->trans('Saverecord').' con valor de '.$sumaunit,null,'mesgs');
				// Creation OK
				unset($_POST['detail']);
				unset($_POST['product']);
				unset($_POST['refsearch']);
				unset($_POST['fk_product_budget']);


			}
			else
			{
				$action='viewit';
				$_GET['idr'] = $_POST['idr'];
			}
		}
		else
		{
			setEventMessages($objectdet->error, null, 'errors');
			$action='viewit';
			$_GET['idr'] = $_POST['idr'];
		}
		if (!$error)
		{
			$db->commit();
			$urltogo=$backtopage?$backtopage:dol_buildpath('/priceunits/budget/card.php?id='.$id.'&idr='.GETPOST('idr').'&action=viewit',1);
			header("Location: ".$urltogo);
			exit;
			$action = 'viewit';
		}
		else
		{
			$db->rollback();
			$action = 'viewit';
		}
	}
	//updateressource
	if ($action == 'updateresource')
	{
		$aStruct = $aStrbudget[$id];

		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/priceunits/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		//print_r($_POST);exit;
		$error=0;
		$res = $objectbtr->fetch(GETPOST('idreg'));
		if ($res<=0)
		{
			$error++;
			setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
		}

		$db->begin();
		//revisando la nueva opcion
		//revisamos el producto
		$fk_unit = GETPOST('fk_unit');
		$code_structure = GETPOST('code_structure');
		//$price = GETPOST('price');
		if (GETPOST('fk_product_budget'))
		{
			//se selecciono un producto del budget
			$fk_product_budget = GETPOST('fk_product_budget');
			$objprodb->fetch(GETPOST('fk_product_budget'));
			$ref_product = $objprodb->ref;
			$fk_product = $objprodb->fk_product;
			$fk_unit = $objprodb->fk_unit;
			$detail = $objprodb->label;
			$price = $objprodb->amount;
			$code_structure = $objprodb->code_structure;
			$price = $objprodb->amount;
		}
		else
		{
			/* object_prop_getpost_prop */			
			$fk_product = GETPOST('product');
			$detail = GETPOST('refsearch');
			if ($fk_product>0 && $product->fetch($fk_product)>0)
			{
				//buscamos la categoria a la que pertenece
				$detail = $product->label;
				$ref_product = $product->ref;
				$fk_unit = $product->fk_unit;
				$aCat = $categorie->containing($fk_product, 'product', 'id');
				foreach ((array) $aCat AS $i => $fk)
				{
					if ($aStruct['aStrcatcode'][$fk])
						$code_structure = $aStruct['aStrcatcode'][$fk];
				}
			}
			else
			{
				$fk_product = 0;
				$ref_product = '';
				$code_structure = GETPOST('code_structure');
				$detail = GETPOST('search_product');
			}
			$detail = STRTOUPPER($detail);
			//verificamos si existe en producto budget
			$filter = array('UPPER(ref)'=>$detail,'UPPER(label)'=>$detail);
			$filter = array(1=>1);
			$filterstatic = '';
			if ($ref_product)
			{
				$filterstatic = ' AND (';
				$filterstatic.= " UPPER(ref) = '".$ref_product."'";
			}
			if (!empty($filterstatic))
			{
				$filterstatic.= " OR ";
			}
			else
				$filterstatic.= " AND (";
			$filterstatic.= " UPPER(label) = '".$detail."'";
			$filterstatic.= ")";
			$filterstatic.= " AND t.fk_budget =".$object->id;
			$res = $objprodb->fetchAll('','',0,0,$filter,'OR',$filterstatic,true);
			if (empty($res))
			{
				$lUpdate = false;
				if (empty($ref_product)) $lUpdate = true;
				$objprodb->fk_product = $fk_product+0;
				$objprodb->fk_budget = $object->id;
				$objprodb->ref = $object->id.'|'.$ref_product;
				$objprodb->label = $detail;
				$objprodb->fk_unit = $fk_unit;
				$objprodb->code_structure = $code_structure;
				$objprodb->quant = GETPOST('quant')+0;
				$objprodb->amount = $price;
				$objprodb->fk_user_create = $user->id;
				$objprodb->fk_user_mod = $user->id;
				$objprodb->date_create = dol_now();
				$objprodb->date_mod = dol_now();
				$objprodb->tms = dol_now();
				$objprodb->status = 1;
				$fk_product_budget = $objprodb->create($user);
				if ($fk_product_budget<=0)
				{
					setEventMessages($objprodb->error,$objprodb->errors,'errors');
					$error++;
				}
					//actualizamos si lUpdate == true
				if ($lUpdate == true)
				{
					$objprodbtmp->fetch($fk_product_budget);
					$objprodbtmp->ref .= '(PROV)'.$objprodb->id;
					$res = $objprodbtmp->update($user);
					if ($res <=0)
					{
						setEventMessages($objprodbtmp->error,$objprodbtmp->errors,'errors');
						$error++;							
					}
				}
			}
			elseif($res==1)
			{
				$fk_product_budget = $objprodb->id;
				$ref_product = $objprodb->ref;
				$fk_product = $objprodb->fk_product;
				$fk_unit = $objprodb->fk_unit;
				$price = $objprodb->amount;
				$detail = $objprodb->label;
				$code_structure = $objprodb->code_structure;
			}
			else
			{
				$error++;
				if ($res<0)
					setEventMessages($objprodb->error,$objprodb->errors,'errors');
				else
					setEventMessages($langs->trans('Existe muchos registros'),null,'errors');
			}
		}
		//fin nueva opcion
		if (!$error)
		{
			$objectbtr->fk_budget_task = GETPOST('idr');
			$objectbtr->code_structure = $code_structure;
			$objectbtr->fk_product = $fk_product;
			$objectbtr->detail = $detail;
			$objectbtr->fk_unit = $fk_unit+0;
			$objectbtr->quant = GETPOST('quant');
			$objectbtr->amount = $price+0;
			//$objectbtr->rang = 1;
			$objectbtr->date_mod = dol_now();
			$objectbtr->tms = dol_now();
			$objectbtr->fk_user_mod = $user->id;
			$objectbtr->status = 1;
			$result=$objectbtr->update($user);
			if ($result<=0)
			{
				$error++;
				setEventMessages($objectbtr->error, $objectbtr->errors, 'errors');	
			}
			//procedemos al calculo del costo unitario
			if (!$error)
			{
				$sumaunit = procedure_calc($id,GETPOST('idr'));
				$objectdetadd->fetch(0,GETPOST('idr'));
				$objectdetadd->unit_amount = $sumaunit;
				$resdet = $objectdetadd->update($user);
				if ($resdet<=0)
				{
					$error++;
					setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
				}
			}
		}
		if (!$error)
		{
			setEventMessages($langs->trans('Saverecord').' con valor de '.$sumaunit,null,'mesgs');
				// Creation OK
			unset($_POST['detail']);
			unset($_POST['product']);
			unset($_POST['refsearch']);
			$db->commit();

			$urltogo=$backtopage?$backtopage:dol_buildpath('/priceunits/budget/card.php?id='.$id.'&idr='.GETPOST('idr').'&action=viewit',1);
			header("Location: ".$urltogo);
			exit;
		}
		else
		{
			$db->rollback();
			$action='viewit';
			$_GET['idr'] = $_POST['idr'];
		}
	}

	if ($action == 'deleteres' && $user->rights->priceunits->budr->del)
	{
		$objectbtr->fetch(GETPOST('idreg'));
		if (GETPOST('idr') == $objectbtr->fk_budget_task)
		{
			$error=0;
			$db->begin();
			$res = $objectbtr->delete($user);
			if ($res>0)
			{
				//procedemos al calculo del costo unitario
				$sumaunit = procedure_calc($id,GETPOST('idr'));
				$objectdetadd->fetch(0,GETPOST('idr'));
				$objectdetadd->unit_amount = $sumaunit * $objectdetadd->unit_budget;
				$resdet = $objectdetadd->update($user);
				if ($resdet<=0)
				{
					$error++;
					setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
				}
				setEventMessages($langs->trans('Recorddeleted'),null,'mesgs');
			}
			else
			{
				$error++;
				setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
			}
			if (!$error) $db->commit();
			else $db->rollback();
		}
		unset($_GET['idreg']);
		$action = 'viewit';
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

//llxHeader('','MyPageName','');
//$morejs = array('/priceunits/js/bootstrap.min.js');
$link1 ='../https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css';
$link2='../https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css';
$link3 = '';
if ($action == 'viewit')
	$link3 = '/priceunits/js/edit.js';
$morejs = array('/priceunits/js/priceunit.js',$link3);
$morecss = array('/priceunits/css/style.css','/priceunits/css/bootstrap.min.css','/priceunits/plugins/datatables/dataTables.bootstrap.css','/priceunits/plugins/datatables/jquery.dataTables.css',);
llxHeader('',$title,'','','','',$morejs,$morecss,0,0);




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
	$form=new Formv($db);
	print load_fiche_titre($langs->trans("Newbudget"));

	dol_htmloutput_events();

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtitle").'</td><td><input class="flat" type="text" name="title" value="'.GETPOST('title').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.GETPOST('description').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_structure").'</td><td>';
	$filterstatic = "";
	$typestr->fetchAll('ASC', 'label', 0, 0, array(1=>1), 'AND',$filterstatic);
	print $typestr->putype_select('','type_structure','',0,$campo='code');
	print '</td></tr>';
	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	$form=new Formv($db);
	print load_fiche_titre($langs->trans("Budget"));
	dol_htmloutput_events();

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.$object->fk_soc.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$object->entity.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtitle").'</td><td><input class="flat" type="text" name="title" value="'.$object->title.'"></td></tr>';
	print '<tr><td >'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.$object->description.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creat").'</td><td><input class="flat" type="text" name="fk_user_creat" value="'.$object->fk_user_creat.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpublic").'</td><td><input class="flat" type="text" name="public" value="'.$object->public.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_statut").'</td><td><input class="flat" type="text" name="fk_statut" value="'.$object->fk_statut.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_opp_status").'</td><td><input class="flat" type="text" name="fk_opp_status" value="'.$object->fk_opp_status.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldopp_percent").'</td><td><input class="flat" type="text" name="opp_percent" value="'.$object->opp_percent.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_close").'</td><td><input class="flat" type="text" name="fk_user_close" value="'.$object->fk_user_close.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_private").'</td><td><input class="flat" type="text" name="note_private" value="'.$object->note_private.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_public").'</td><td><input class="flat" type="text" name="note_public" value="'.$object->note_public.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldopp_amount").'</td><td><input class="flat" type="text" name="opp_amount" value="'.$object->opp_amount.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbudget_amount").'</td><td><input class="flat" type="text" name="budget_amount" value="'.$object->budget_amount.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.$object->model_pdf.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_structure").'</td><td>';
	$filterstatic = "";
	$typestr->fetchAll('ASC', 'label', 0, 0, array(1=>1), 'AND',$filterstatic);
	print $typestr->putype_select($object->type_structure,'type_structure','',0,$campo='code');
	print '</td></tr>';

	print '</table>';
	
	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
//if ($id && (empty($action) || $action == 'viewit' || $action == 'creategr' || $action == 'viewgr' || $action == 'view' || $action == 'delete' || $action=='editres' || $action=='edititem' || $action=='viewre' || $action == 'confimportresource'))
if ($id && $action != 'edit')
{
	print load_fiche_titre($langs->trans("Budget"));
	dol_htmloutput_events();

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	if ($action == 'confclon') {
		$formquestion = array(array('type'=>'text','label'=>$langs->trans('Newversion'),'size'=>5,'name'=>'version','value'=>$object->version));
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Clonebudget'), $langs->trans('ConfirmCloneBudget').': '.$object->ref, 'confirm_clon', $formquestion, 0, 1);
		print $formconfirm;
	}

	$form=new Formv($db);


	print '<table class="table border centpercent">'."\n";
	if ($action != 'editres' && $action != 'viewit' && $action != 'viewgr'&& $action != 'creategr' && $action != 'edititem' && $action != 'viewre')
	{
		// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
		// 
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td>'.$object->fk_soc.'</td></tr>';
        //$linkback = '<a href="'.DOL_URL_ROOT.'/priceunits/budget/list.php">'.$langs->trans("BackToList").'</a>';

        //dol_banner_tab($object, 'socid', $linkback, ($user->societe_id?0:1), 'rowid', 'nom');


		print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td>'.$object->entity.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldtitle").'</td><td>'.$object->title.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fielddescription").'</td><td>'.$object->description.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldversion").'</td><td>'.$object->version.'</td></tr>';
		$objuser->fetch($object->fk_user_creat);
		print '<tr><td>'.$langs->trans("Fieldfk_user_creat").'</td><td>'.$objuser->getNomUrl(1).'</td></tr>';
		//print '<tr><td>'.$langs->trans("Fieldpublic").'</td><td>'.$object->public.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldfk_statut").'</td><td>'.$object->getLibStatut().'</td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_opp_status").'</td><td>'.$object->fk_opp_status.'</td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldopp_percent").'</td><td>'.$object->opp_percent.'</td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_close").'</td><td>'.$object->fk_user_close.'</td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_private").'</td><td>'.$object->note_private.'</td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_public").'</td><td>'.$object->note_public.'</td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldopp_amount").'</td><td>'.$object->opp_amount.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldbudget_amount").'</td><td>'.price($object->budget_amount).'</td></tr>';
	}
	else
	{
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans('Fieldref'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Fieldversion'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Fieldtitle'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Fieldamount'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print '</tr>';
		print '<tr>';
		print '<td>'.$object->getNomUrl(1).'</td>';
		print '<td>'.$object->version.'</td>';
		print '<td>'.$object->title.'</td>';
		print '<td align="right">'.price($object->amount).'</td>';
		print '</tr>';
	}
	print '</table>';
	
	dol_fiche_end();

	//armamos el navegador del presupuesto
	$array = array();
	if ($idg && $action !='viewgr')
	{
		$objectdet->fetch($idg);
		$array[] = array('fklnk'=>'idg','fk'=>$objectdet->id,'action'=>'viewit','head1'=>$objectdet->label,'head2'=>'g'.$objectdet->id);
		$getcard = 'g'.$idg;
	}
	if (GETPOST('idr'))
	{
		$objectdet->fetch(GETPOST('idr'));
		$array[] = array('fklnk'=>'idr','fk'=>$objectdet->id,'action'=>'viewit','head1'=>$objectdet->label,'head2'=>'t'.$objectdet->id);
		$getcard = 't'.$objectdet->id;
	}

	$head = priceunits_budget_prepare_head($object, $user, $array);
	$titre=$langs->trans("Budget");
	$picto='budget';

	dol_fiche_head($head, $getcard, $titre, 0, $picto);

	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($action != 'edititem' && $action != 'viewit' && $action != 'viewgr' && $action != 'viewre' && $action != 'pdfitem')
		{
			if ($user->rights->priceunits->bud->mod)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
			}

			if ($user->rights->priceunits->bud->del)
			{
				print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
			}

			if ($user->rights->priceunits->budi->crear)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=confclon">'.$langs->trans("Clone").'</a></div>'."\n";
			}
		}
		if ($action != 'viewit' && $action != 'pdfitem')
		{
			if ($user->rights->priceunits->budi->leer)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=viewgr">'.$langs->trans("Module").'</a></div>'."\n";
			}
			if ($user->rights->priceunits->budi->leer)
			{
			//print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=viewit">'.$langs->trans("Items").'</a></div>'."\n";
			}
			if ($user->rights->priceunits->budi->leer)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=viewre">'.$langs->trans("Resources").'</a></div>'."\n";
			}
		}
		if ($action == 'viewit' && $abc)
		{
			print '<a data-toggle="modal" href="#presup" class="btn btn-primary btn-large">'.$langs->trans('Selectitem').'</a>';

			print '<div id="presup" class="modal fade in" style="display: none;">';
			print '<div class="modal-dialog">';
			print '<div class="modal-content">';
			print '<div class="modal-header">';
			print '<a data-dismiss="modal" class="close">×</a>';
			print '<h3>'.$langs->trans('Presupuestos').'</h3>';
			print '</div>';
			print '<div class="modal-body">';
			print '<h4>'.$langs->trans('Seleccione los items').'</h4>';
			include DOL_DOCUMENT_ROOT.'/priceunits/tpl/listp.tpl.php';
			print '</div>';
			print '<div class="modal-footer">';
			print '<a href="index.html" class="btn btn-success">Guardar</a>';
			print '<a href="#" data-dismiss="modal" class="btn">Cerrar</a>';
			print '</div>';
			print '</div>';
			print '</div>';
			print '</div>';
		}
	}
	print '</div>'."\n";

	if ($action != 'pdfitem')
		include DOL_DOCUMENT_ROOT.'/priceunits/budget/tpl/group_task.tpl.php';
	else
		include DOL_DOCUMENT_ROOT.'/priceunits/budget/tpl/pdf_item.tpl.php';
	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

}
$_SESSION['sesbudget']= serialize($sesbudget);
if ($action == 'viewgr' || $action == 'viewit' || $action=='viewre')
{
	print '
	<!-- ./wrapper -->

	<!-- Bootstrap 3.3.6 -->

	<script src="../js/bootstrap.min.js"></script>
	<!-- FastClick -->
	<script src="../plugins/fastclick/fastclick.js"></script>
	<!-- AdminLTE App -->
	<script src="../js/app.min.js"></script>
	<!-- Sparkline -->
	<script src="../plugins/sparkline/jquery.sparkline.min.js"></script>
	<!-- jvectormap -->
	<script src="../plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
	<script src="../plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
	<!-- SlimScroll 1.3.0 -->
	<script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- ChartJS 1.0.1 -->
	<script src="../plugins/chartjs/Chart.min.js"></script>
	<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
	<script src="../js/pages/dashboard2.js"></script>
	<!-- AdminLTE for demo purposes -->
	<script src="../js/demo.js"></script>
	';
}
// End of page
llxFooter();
$db->close();
