<?php
/* Copyright (C) 2004-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2010      Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2014      Cedric Gross         <c.gross@kreiz-it.fr>
 *
 * This	program	is free	software; you can redistribute it and/or modify
 * it under the	terms of the GNU General Public	License	as published by
 * the Free Software Foundation; either	version	2 of the License, or
 * (at your option) any later version.
 *
 * This	program	is distributed in the hope that	it will	be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	\file      htdocs/fourn/commande/dispatch.php
 *	\ingroup   commande
 *	\brief     Page to dispatch receiving
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/supplier_order/modules_commandefournisseur.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';

require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.dispatch.class.php';
require_once DOL_DOCUMENT_ROOT.'/purchase/class/html.formproductext.class.php';
require_once DOL_DOCUMENT_ROOT.'/purchase/class/commandefournisseuradd.class.php';
require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseurcommandeext.class.php';

require_once DOL_DOCUMENT_ROOT.'/purchase/class/unitconv.class.php';
require_once DOL_DOCUMENT_ROOT.'/purchase/lib/purchase.lib.php';
require_once DOL_DOCUMENT_ROOT.'/purchase/lib/units.lib.php';

if ($conf->almacen->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtype.class.php';
	//require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementdocext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/almacen/class/ctypemouvement.class.php';
	require_once DOL_DOCUMENT_ROOT.'/almacen/class/contabperiodo.class.php';
	require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/almacen/lib/almacen.lib.php';
}
else
{
	require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
	setEventMessages($langs->trans('Habilitar el modulo de almacenes, para recepción de los productos'),null,'warnings');
}
if ($conf->poa->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/poa/class/poapoaext.class.php';
}
//require_once DOL_DOCUMENT_ROOT.'/purchase/class/html.formext.class.php';

if (! empty($conf->projet->enabled))	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

$langs->load('orders');
$langs->load('sendings');
$langs->load('companies');
$langs->load('bills');
$langs->load('deliveries');
$langs->load('products');
$langs->load('stocks');
$langs->load('purchase@purchase');

if (! empty($conf->productbatch->enabled)) $langs->load('productbatch');

// Security check
$id = GETPOST("id",'int');
$lineid = GETPOST('lineid', 'int');
$action = GETPOST('action');
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'fournisseur', $id, '', 'commande');

if (empty($conf->stock->enabled))
{
	accessforbidden();
}

$prodtmp = new Product($db);
$unitconv = new Unitconv($db);
$objectadd = new CommandeFournisseuradd($db);
if ($conf->almacen->enabled)
	$objEntrepot = new Entrepotext($db);
else
	$objEntrepot = new Entrepot($db);

// Recuperation	de l'id	de projet
$projectid =	0;
if ($_GET["projectid"]) $projectid = GETPOST("projectid",'int');

$mesg='';
if ($conf->almacen->enabled)
{
	if (!$user->admin) list($aFilterent, $filteruser,$aFilterentsol, $filterusersol,$aAreadirect,$fk_areaasign,$filterarea,$aFilterarea, $fk_user_resp,$aExcluded) = verif_accessalm();
}


/*
 * Actions
 */

if ($action == 'checkdispatchline' &&
	! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->fournisseur->commande->receptionner))
		|| (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->fournisseur->commande_advance->check)))
)
{
	$supplierorderdispatch = new CommandeFournisseurDispatch($db);
	$result=$supplierorderdispatch->fetch($lineid);
	if (! $result) dol_print_error($db);
	$result=$supplierorderdispatch->setStatut(1);
	if ($result < 0)
	{
		setEventMessages($supplierorderdispatch->error, $supplierorderdispatch->errors, 'errors');
		$error++;
		$action='';
	}
}

if ($action == 'uncheckdispatchline' &&
	! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->fournisseur->commande->receptionner))
		|| (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->fournisseur->commande_advance->check)))
)
{
	$supplierorderdispatch = new CommandeFournisseurDispatch($db);
	$result=$supplierorderdispatch->fetch($lineid);
	if (! $result) dol_print_error($db);
	$result=$supplierorderdispatch->setStatut(0);
	if ($result < 0)
	{
		setEventMessages($supplierorderdispatch->error, $supplierorderdispatch->errors, 'errors');
		$error++;
		$action='';
	}
}

if ($action == 'denydispatchline' &&
	! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->fournisseur->commande->receptionner))
		|| (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->fournisseur->commande_advance->check)))
)
{
	$supplierorderdispatch = new CommandeFournisseurDispatch($db);
	$result=$supplierorderdispatch->fetch($lineid);
	if (! $result) dol_print_error($db);
	$result=$supplierorderdispatch->setStatut(2);
	if ($result < 0)
	{
		setEventMessages($supplierorderdispatch->error, $supplierorderdispatch->errors, 'errors');
		$error++;
		$action='';
	}
}

if ($action == 'dispatch' && $user->rights->fournisseur->commande->receptionner)
{
	$commande = new FournisseurCommandeext($db);
	$commande->fetch($id);
	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
	$error=0;

	$db->begin();

	$pos=0;
	$objline = new CommandeFournisseurLigne($db);
	$iddoc = 0;

	if ($conf->almacen->enabled)
	{
		if ($user->rights->almacen->transf->datem)
			$datesel  = dol_mktime(12, 0, 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));
		else
			$datesel = dol_now();
		$objectdoc = new Stockmouvementdocext($db);

		//buscamos la numeracion para la transferencia
		$ref = 'PROV';
		if ($ref == 'PROV')
			$numref = $objectdoc->getNextNumRef($soc);
		else
			$numref = $objectdoc->ref;

			//creamos el registro principal
		$objectdoc->ref = $numref;
		$objectdoc->entity = $conf->entity;
		$objectdoc->fk_entrepot_from = $idr+0;
		$objectdoc->fk_entrepot_to = $idd+0;
		$objectdoc->fk_departament = GETPOST('fk_departament')+0;
		$objectdoc->fk_soc = $commande->socid+0;
		$objectdoc->fk_type_mov = GETPOST('fk_type_mouvement')+0;
		$objectdoc->fk_source = GETPOST('fk_source')+0;
		$objectdoc->ref_ext = GETPOST('ref_ext');
		$objectdoc->datem = $datesel;
		$objectdoc->label = GETPOST('comment');
		$objectdoc->date_create = dol_now();
		$objectdoc->date_mod = dol_now();
		$objectdoc->tms = dol_now();
		$objectdoc->model_pdf = 'inputalm';
		$objectdoc->fk_user_create = $user->id;
		$objectdoc->fk_user_mod = $user->id;
		$objectdoc->statut = 1;
		$iddoc = $objectdoc->create($user);
		//para enviar al interface90
		$_POST['iddoc'] = $iddoc;
		if ($iddoc <=0)
		{
			$error=101;
			setEventMessages($objectdoc->error,$objectdoc->errors,'errors');
		}
	}
	if (!$error)
	{
		$x = 0;
		foreach($_POST as $key => $value)
		{
			if (preg_match('/^product_([0-9]+)_([0-9]+)$/i', $key, $reg))
			// without batch module enabled
			{
				//print_r($reg);
				$pos++;

				//$numline=$reg[2] + 1;	// line of product
				$numline=$pos;
				$prod = "product_".$reg[1].'_'.$reg[2];
				$qty = "qty_".$reg[1].'_'.$reg[2];
				$ent = "entrepot_".$reg[1].'_'.$reg[2];
				$pu = "pu_".$reg[1].'_'.$reg[2];
				// This is unit price including discount
				//echo '<hr>qty '.$qty.' pu '.$pu;
				$fk_commandefourndet = "fk_commandefourndet_".$reg[1].'_'.$reg[2];

				if (GETPOST($qty) > 0)
				// We ask to move a qty
				{
					if (! (GETPOST($ent,'int') > 0))
					{
						dol_syslog('No dispatch for line '.$key.' as no warehouse choosed');
						$text = $langs->transnoentities('Warehouse').', '.$langs->transnoentities('Line').' ' .($numline);
						setEventMessages($langs->trans('ErrorFieldRequired',$text), null, 'errors');
						$error=102;
					}
					//revisamos si tiene conversion
					$objline->fetch(GETPOST($fk_commandefourndet, 'int'));
					$prodtmp->fetch(GETPOST($prod));
					$lContinue = false;
					if ($prodtmp->fk_unit == $objline->fk_unit)
						$lContinue = true;
					//buscamos la conversion
					$filter = array(1=>1);
					$filterstatic = " AND t.fk_product = ".GETPOST($prod);
					//echo '<hr>tiene conversiones '.
					$resconv = $unitconv->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
					foreach ((array) $unitconv->lines AS $j => $lin)
					{
						//echo '<hr>revisa y compara '.$lin->fk_unit_ext.' con '.$objline->fk_unit.' tconv  '.$lin->type_fc.' '.$lin->fc;
						if ($lin->fk_unit_ext == $objline->fk_unit)
						{
							if ($lin->type_fc == 'M')
							{
								$_POST[$qty] = $_POST[$qty] * $lin->fc;
								$_POST[$pu] = $_POST[$pu] / $lin->fc;
							}
							if ($lin->type_fc == 'D')
							{
								$_POST[$qty] = $_POST[$qty] / $lin->fc;
								$_POST[$pu] = $_POST[$pu] * $lin->fc;
							}
						}
					}

					if (! $error)
					{
						//asignamos valor para fk_stock_mouvement_doc
						$commande->iddoc = $iddoc;
						$result = $commande->dispatchProductadd($user, GETPOST($prod,'int'), GETPOST($qty), GETPOST($ent,'int'), GETPOST($pu), GETPOST('comment'), '', '', '', GETPOST($fk_commandefourndet, 'int'), $notrigger);
						if ($result < 0)
						{
							setEventMessages($commande->error, $commande->errors, 'errors');
							$error=103;
						}
					}
				}
			}
			/*
			if (preg_match('/^product_batch_([0-9]+)_([0-9]+)$/i', $key, $reg))
			// with batch module enabled
			{
				$pos++;

			//eat-by date dispatch
			//$numline=$reg[2] + 1;	// line of product
				$numline=$pos;
				$prod = 'product_batch_'.$reg[1].'_'.$reg[2];
				$qty = 'qty_'.$reg[1].'_'.$reg[2];
				$ent = 'entrepot_'.$reg[1].'_'.$reg[2];
				$pu = 'pu_'.$reg[1].'_'.$reg[2];
				$fk_commandefourndet = 'fk_commandefourndet_'.$reg[1].'_'.$reg[2];
				$lot = 'lot_number_'.$reg[1].'_'.$reg[2];
				$dDLUO = dol_mktime(12, 0, 0, $_POST['dluo_'.$reg[1].'_'.$reg[2].'month'], $_POST['dluo_'.$reg[1].'_'.$reg[2].'day'], $_POST['dluo_'.$reg[1].'_'.$reg[2].'year']);
				$dDLC = dol_mktime(12, 0, 0, $_POST['dlc_'.$reg[1].'_'.$reg[2].'month'], $_POST['dlc_'.$reg[1].'_'.$reg[2].'day'], $_POST['dlc_'.$reg[1].'_'.$reg[2].'year']);

				$fk_commandefourndet = 'fk_commandefourndet_'.$reg[1].'_'.$reg[2];
				//echo '<hr>qtyenviado '.GETPOST('qty');
				if (GETPOST($qty) > 0)
				// We ask to move a qty
				{
					if (! (GETPOST($ent,'int') > 0))
					{
						dol_syslog('No dispatch for line '.$key.' as no warehouse choosed');
						$text = $langs->transnoentities('Warehouse').', '.$langs->transnoentities('Line').' ' .($numline).'-'.($reg[1]+1);
						setEventMessages($langs->trans('ErrorFieldRequired',$text), null, 'errors');
						$error++;
					}

					if (! (GETPOST($lot, 'alpha') || $dDLUO || $dDLC))
					{
						dol_syslog('No dispatch for line '.$key.' as serial/eat-by/sellby date are not set');
						$text = $langs->transnoentities('atleast1batchfield').', '.$langs->transnoentities('Line').' ' .($numline).'-'.($reg[1]+1);
						setEventMessages($langs->trans('ErrorFieldRequired',$text), null, 'errors');
						$error=104;
					}

					if (! $error)
					{
						$result = $commande->dispatchProductadd($user, GETPOST($prod,'int'), GETPOST($qty), GETPOST($ent,'int'), GETPOST($pu), GETPOST('comment'), $dDLC, $dDLUO, GETPOST($lot, 'alpha'), GETPOST($fk_commandefourndet, 'int'), $notrigger);
						if ($result < 0)
						{
							setEventMessages($commande->error, $commande->errors, 'errors');
							$error=105;
						}
					}
				}
			}
			*/
		}
	}

	if (! $notrigger && ! $error)
	{
		global $conf, $langs, $user;
        // Call trigger

		$result = $commande->call_trigger('ORDER_SUPPLIER_DISPATCH', $user);
        // End call triggers

		if ($result < 0)
		{
			setEventMessages($commande->error, $commande->errors, 'errors');
			$error=106;
		}
	}
	//echo '<hr>err fin '.$error;

	if ($result >= 0 && ! $error)
	{
		//verificar estado
		$result = $commande->calcAndSetStatusDispatch($user);
		if ($result < 0) {
			setEventMessages($commande->error, $commande->errors, 'errors');
			$error ++;
			$action = '';
		}
		if (!$error)
		{
			$db->commit();

			//verificamos el estado del pedido
			$product_commande = $commande->commande_getsum();
			$product_dispatch = $commande->commande_dispatch();
			$statusDispatch = 0;
			if (count($product_dispatch)>0) $statusDispatch = 2;
			foreach ($product_commande AS $fk => $value)
			{
				$ndif = price2num($value - ((float) $product_dispatch[$fk]), 5);
				if ($ndif>0) $statusDispatch = 1;
			}
			//actualizamos el estado
			if ($statusDispatch > 0)
			{
				$status = '';
				if ($statusDispatch==1) $status = 'par';
				if ($statusDispatch==2) $status = 'tot';
				$date_liv = dol_mktime(GETPOST('rehour'),GETPOST('remin'),GETPOST('resec'),GETPOST("remonth"),GETPOST("reday"),GETPOST("reyear"));
				//fin veriricar estado
				if ($iddoc>0)
				{
					$url = $_SERVER['PHP_SELF'].'?id='.$id;
					header("Location: ".DOL_URL_ROOT."/almacen/mouvement/card.php?id=".$iddoc.'&url='.$url);
					exit;
				}
				else
				{
					header("Location: card.php?id=".$id.'&action=livraison&type='.$status.'&subaction=1');
					exit;
				}
			}
		}
		header("Location: dispatch.php?id=".$id);
		exit;
	}
	else
	{
		$db->rollback();
	}
}


/*
 * View
 */

$form =	new Form($db);
$formproduct = new FormProductext($db);
$warehouse_static = new Entrepot($db);
$supplierorderdispatch = new CommandeFournisseurDispatch($db);


$help_url='EN:Module_Suppliers_Orders|FR:CommandeFournisseur|ES:Módulo_Pedidos_a_proveedores';
llxHeader('',$langs->trans("Order"),$help_url,'',0,0,array('/fourn/js/lib_dispatch.js','/purchase/js/purchase.js'));

$now=dol_now();

$form =	new	Formv($db);
//$formf = new Formfad_d($db);

$id = GETPOST('id','int');
$ref= GETPOST('ref');
if ($id > 0 || ! empty($ref))
{
	//if ($mesg) print $mesg.'<br>';

	$commande = new CommandeFournisseur($db);
	$result=$commande->fetch($id,$ref);
	if ($result >= 0)
	{
		$objectadd->fetch(0,$commande->id);
		$lReception = true;
		if (empty($conf->global->PURCHASE_DESTINATION_CODE_PRODUCT))
			setEventMessages($langs->trans('Thepurchasedestinationvariableforproductsisnotdefined').' '.($user->admin?'<a href="'.DOL_URL_ROOT.'/purchase/admin/purchase.php'.'">'.$langs->trans('Configpurchase').'</a>':''),null,'warnings');

		if ($objectadd->code_type_purchase != $conf->global->PURCHASE_DESTINATION_CODE_PRODUCT)
		{
			$lReception = false;
		}
		$soc = new Societe($db);
		$soc->fetch($commande->socid);

		$author = new User($db);
		$author->fetch($commande->user_author_id);

		$head = purchase_prepare_head($commande);

		$title=$langs->trans("SupplierOrder");
		dol_fiche_head($head, 'dispatch', $title, 0, 'order');

		/*
		 *	Commande
		 */
		print '<table class="border" width="100%">';

		// Ref
		print '<tr><td class="titlefield">'.$langs->trans("Ref").'</td>';
		print '<td colspan="2">';
		print $form->showrefnav($commande,'ref','',1,'ref','ref');
		print '</td>';
		print '</tr>';

	//mostrar que tipo de doc fiscal
		print '<tr><td>'.$langs->trans("Typefiscal")."</td>";
		$form->load_type_facture('type_facture', $objectadd->code_facture,0,$campo='code', true);
		foreach ((array) $form->type_facture_code AS $j => $code)
		{
			if ($code == $objectadd->code_facture)
				print '<td colspan="2">'.$form->type_facture_label[$j].'</td>';
		}
		print '</tr>';
		print '<tr><td>'.$langs->trans("Purchasedestination")."</td>";
		$form->load_type_purchase('type_purchase', $objectadd->code_type_purchase,0,$campo='code', true);
		foreach ((array) $form->type_purchase_code AS $j => $code)
		{
			if ($code == $objectadd->code_type_purchase)
				print '<td colspan="2">'.$form->type_purchase_label[$j].'</td>';
		}
		print '</tr>';
		// Fournisseur
		print '<tr><td>'.$langs->trans("Supplier")."</td>";
		print '<td colspan="2">'.$soc->getNomUrl(1,'supplier').'</td>';
		print '</tr>';

		// Statut
		print '<tr>';
		print '<td>'.$langs->trans("Status").'</td>';
		print '<td colspan="2">';
		print $commande->getLibStatut(4);
		print "</td></tr>";

		// Date
		if ($commande->methode_commande_id > 0)
		{
			print '<tr><td>'.$langs->trans("Date").'</td><td colspan="2">';
			if ($commande->date_commande)
			{
				print dol_print_date($commande->date_commande,"dayhourtext")."\n";
			}
			print "</td></tr>";

			if ($commande->methode_commande)
			{
				print '<tr><td>'.$langs->trans("Method").'</td><td colspan="2">'.$commande->methode_commande.'</td></tr>';
			}
		}

		// Auteur
		print '<tr><td>'.$langs->trans("AuthorRequest").'</td>';
		print '<td colspan="2">'.$author->getNomUrl(1).'</td>';
		print '</tr>';

		print "</table>";

		//if ($mesg) print $mesg;
		print '<br>';


		$disabled=1;
		if (! empty($conf->global->STOCK_CALCULATE_ON_SUPPLIER_DISPATCH_ORDER)) $disabled=0;

		/*
		 * Lignes de commandes
		 */
		if ($commande->statut <= 2 || $commande->statut >= 6)
		{
			print $langs->trans("OrderStatusNotReadyToDispatch");
		}
		//echo '<hr>antesde'.$user->rights->fournisseur->commande->receptionner.' | '.$lReception.' | '.$commande->statut;
		if ($user->rights->fournisseur->commande->receptionner && $lReception && ($commande->statut == 3 || $commande->statut == 4 || $commande->statut == 5))
		{
			//echo ' adentro';
			$entrepot = new Entrepot($db);
			$listwarehouses=$entrepot->list_array(1);

			if (! empty($conf->use_javascript_ajax))
			{
				print "\n".'<script type="text/javascript">';
				print '$(document).ready(function () {
					$("#entrepotall").change(function() {
						document.formdis.action.value="rech";
						document.formdis.submit();
					});
				});';
				print '</script>'."\n";
			}


			print '<form method="POST" id="formdis" name="formdis" action="dispatch.php?id='.$commande->id.'">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="dispatch">';
			print '<table class="noborder" width="100%">';

			// Set $products_dispatched with qty dispatched for each product id
			$products_dispatched = array();
			$sql = "SELECT l.rowid, cfd.fk_product, l.fk_unit, sum(cfd.qty) as qty";
			$sql.= " FROM ".MAIN_DB_PREFIX."commande_fournisseur_dispatch as cfd";
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."commande_fournisseurdet as l on l.rowid = cfd.fk_commandefourndet";
			$sql.= " WHERE cfd.fk_commande = ".$commande->id;
			$sql.= " GROUP BY l.rowid, cfd.fk_product, l.fk_unit ";

			$resql = $db->query($sql);
			if ($resql)
			{
				$num = $db->num_rows($resql);
				$i = 0;

				if ($num)
				{
					while ($i < $num)
					{
						$objd = $db->fetch_object($resql);

						$prodtmp->fetch($objd->fk_product);
						$lConvert = false;
						if ($prodtmp->fk_unit != $objd->fk_unit)
							$lConvert = true;
						if ($lConvert)
						{
							//buscamos la conversion
							$filter = array(1=>1);
							$filterstatic = " AND t.fk_product = ".$objd->fk_product;
							$unitconv->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
							foreach ((array) $unitconv->lines AS $j => $lin)
							{
								if ($lin->fk_unit_ext == $objd->fk_unit)
								{
									if ($lin->type_fc == 'M')
									{
										$objd->qty = $objd->qty / $lin->fc;
									}
									if ($lin->type_fc == 'D')
									{
										$objd->qty = $objd->qty * $lin->fc;
									}
								}
							}
						}

						$products_dispatched[$objd->rowid] = price2num($objd->qty, 5);
						$i++;
					}
				}
				$db->free($resql);
			}
			$sql = "SELECT l.rowid, l.fk_unit, l.fk_product, l.subprice, l.remise_percent, SUM(l.qty) as qty,";
			$sql.= " p.ref, p.label, p.tobatch ";
			$sql.= " , la.fk_poa, la.partida ";
			$sql.= " FROM ".MAIN_DB_PREFIX."commande_fournisseurdet as l";
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."commande_fournisseurdet_add as la ON la.fk_commande_fournisseurdet = l.rowid";
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product as p ON l.fk_product=p.rowid";
			$sql.= " WHERE l.fk_commande = ".$commande->id;
			if(empty($conf->global->STOCK_SUPPORTS_SERVICES)) $sql.= " AND l.product_type = 0";
			$sql.= " GROUP BY p.ref, p.label, p.tobatch, l.rowid, l.fk_unit,l.fk_product, l.subprice, l.remise_percent";
			// Calculation of amount dispatched is done per fk_product so we must group by fk_product
			$sql.= " ORDER BY p.ref, p.label";

			$resql = $db->query($sql);
			if ($resql)
			{
				$num = $db->num_rows($resql);
				$i = 0;

				if ($num)
				{
					print '<tr class="liste_titre">';

					print '<td>'.$langs->trans("Description").'</td>';
					print '<td></td>';
					print '<td></td>';
					print '<td></td>';
					print '<td align="right">'.$langs->trans("QtyOrdered").'</td>';
					print '<td align="right">'.$langs->trans("QtyDispatchedShort").'</td>';
					print '<td align="right">'.$langs->trans("QtyToDispatchShort").'</td>';
					print '<td align="right">';
					print $langs->trans("Warehouse");

					//print $formproduct->selectWarehousesadd(GETPOST("entrepotall"), "entrepotall",'',1,0,$objp->fk_product,'',0,0,array(),'','onChange="reemplazasel(this,'.$num.');"');
					if ($user->admin)
						print $formproduct->selectWarehousesadd(GETPOST("entrepotall"), "entrepotall",'',1,0,0,'',0,0,array(),'','onChange="reemplazasel(this,'.$num.');"');
					else
					{
						$entrepotIds = '';
						foreach ($aFilterent AS $j)
						{
							if (!empty($entrepotIds)) $entrepotIds.=',';
							$entrepotIds.= $j;
						}

						if ($conf->almacen->enabled)
						{
							$filterEntrepot = " AND t.rowid IN (".$entrepotIds.")";
							$resentrepot = $objEntrepot->fetchAll('ASC','label',0,0,array(1=>1),'AND',$filterEntrepot);
							if ($resentrepot>0)
							{
								foreach ($objEntrepot->lines AS $j => $line)
								{
									$optionsEntrepot.= '<option value="'.$line->id.'">'.$line->label.'</option>';
								}
							}
							print '<select id="entrepotall" name="entrepotall">'.$optionsEntrepot.'</select>';
						}
						else
						{
							//armamos manualmente
							$sqlent = "SELECT t.rowid, t.label ";
							$sqlent.= " FROM ".MAIN_DB_PREFIX."entrepot AS t";
							$sqlent.= " WHERE t.entity = ".$conf->entity;
							$sqlent.= " AND t.rowid IN (".$entrepotIds.")";
							$sqlent.= " ORDER BY t.label";
							$resqlent = $db->query($sqlent);
							if ($resqlent)
							{
								$nument = $db->num_rows($resqlent);
								$m = 0;
								while ($m < $nument)
								{
									$objEnt = $db->fetch_object($resqlent);
									$optionsEntrepot.= '<option value="'.$objEnt->rowid.'">'.$objEnt->label.'</option>';
								}
							}
						}
					}
					print '</td>';
					print "</tr>\n";

					if (! empty($conf->productbatch->enabled))
					{
						print '<tr class="liste_titre">';
						print '<td></td>';
						print '<td>'.$langs->trans("batch_number").'</td>';
						print '<td>'.$langs->trans("l_eatby").'</td>';
						print '<td>'.$langs->trans("l_sellby").'</td>';
						print '<td colspan="4">&nbsp;</td>';
						print "</tr>\n";
					}

				}

				$nbfreeproduct=0;
				$nbproduct=0;
				$var=false;
				$fk_source = 0;
				while ($i < $num)
				{
					$objp = $db->fetch_object($resql);
					//vamos a determinar fk_source
					if ($conf->poa->enabled)
					{
						$fk_poa = $objp->fk_poa;
						$objPoa = new Poapoaext($db);
						$respoa = $objPoa->fetch($fk_poa);
						if ($respoa>0) $fk_source = $objPoa->fk_fuefin;
					}
					// On n'affiche pas les produits personnalises
					if (! $objp->fk_product > 0)
					{
						$nbfreeproduct++;
					}
					else
					{

						$remaintodispatch=price2num($objp->qty - ((float) $products_dispatched[$objp->rowid]), 5);
							// Calculation of dispatched
						if ($remaintodispatch < 0) $remaintodispatch=0;
						//variable para cambiar de estado parcialmente recibido
						if ($remaintodispatch > 0) $statusDispatch = 1;
						if ($remaintodispatch || empty($conf->global->SUPPLIER_ORDER_DISABLE_STOCK_DISPATCH_WHEN_TOTAL_REACHED))
						{
							$nbproduct++;

							$var=!$var;

							// To show detail cref and description value, we must make calculation by cref
							//print ($objp->cref?' ('.$objp->cref.')':'');
							//if ($objp->description) print '<br>'.nl2br($objp->description);
							$suffix='_0_'.$i;

							print "\n";
							print '<!-- Line '.$suffix.' -->'."\n";
							print "<tr ".$bc[$var].">";

							$linktoprod='<a href="'.DOL_URL_ROOT.'/product/fournisseurs.php?id='.$objp->fk_product.'">'.img_object($langs->trans("ShowProduct"),'product').' '.$objp->ref.'</a>';
							$linktoprod.=' - '.$objp->label."\n";

							if (! empty($conf->productbatch->enabled))
							{
								if ($objp->tobatch)
								{
									print '<td colspan="4">';
									print $linktoprod;
									print "</td>";
								}
								else
								{
									print '<td>';
									print $linktoprod;
									print "</td>";
									print '<td colspan="3">';
									print $langs->trans("ProductDoesNotUseBatchSerial");
									print '</td>';
								}
							}
							else
							{
								print '<td colspan="4">';
								print $linktoprod;
								print "</td>";
							}

							$var=!$var;
							$up_ht_disc=$objp->subprice;
							if (! empty($objp->remise_percent) && empty($conf->global->STOCK_EXCLUDE_DISCOUNT_FOR_PMP)) $up_ht_disc=price2num($up_ht_disc * (100 - $objp->remise_percent) / 100, 'MU');


							// unit
							$labelunit = '';
							if ($objp->fk_unit>0)
							{
								$objunit = fetch_unit($objp->fk_unit,'label');
								$labelunit.= ' '.$langs->trans($objunit->label);
							}
							else
							{
								$labelunit = $langs->trans('Nodefined');
							}
							// Qty ordered
							print '<td align="right">'.$objp->qty.($labelunit?' '.$labelunit:'').'</td>';

							// Already dispatched
							print '<td align="right">'.$products_dispatched[$objp->rowid].'</td>';

							if (! empty($conf->productbatch->enabled) && $objp->tobatch==1)
							{
								$type = 'batch';
								print '<td align="right">'.img_picto($langs->trans('AddDispatchBatchLine'),'split.png','onClick="addDispatchLine('.$i.',\''.$type.'\')"').'</td>';
								// Dispatch column
								print '<td></td>';																													// Warehouse column
								print '</tr>';

								print '<tr '.$bc[$var].' name="'.$type.$suffix.'">';
								print '<td>';
								print '<input name="fk_commandefourndet'.$suffix.'" type="hidden" value="'.$objp->rowid.'">';
								print '<input name="product_batch'.$suffix.'" type="hidden" value="'.$objp->fk_product.'">';
								print '<input name="pu'.$suffix.'" type="hidden" value="'.$up_ht_disc.'"><!-- This is a up including discount -->';
								// hidden fields for js function
								print '<input id="qty_ordered'.$suffix.'" type="hidden" value="'.$objp->qty.'">';
								print '<input id="qty_dispatched'.$suffix.'" type="hidden" value="'.(float) $products_dispatched[$objp->rowid].'">';
								print '</td>';

								print '<td>';
								print '<input type="text" id="lot_number'.$suffix.'" name="lot_number'.$suffix.'" size="40" value="'.GETPOST('lot_number'.$suffix).'">';
								print '</td>';
								print '<td>';
								$dlcdatesuffix=dol_mktime(0, 0, 0, GETPOST('dlc'.$suffix.'month'), GETPOST('dlc'.$suffix.'day'), GETPOST('dlc'.$suffix.'year'));
								$form->select_date($dlcdatesuffix,'dlc'.$suffix,'','',1,"");
								print '</td>';
								print '<td>';
								$dluodatesuffix=dol_mktime(0, 0, 0, GETPOST('dluo'.$suffix.'month'), GETPOST('dluo'.$suffix.'day'), GETPOST('dluo'.$suffix.'year'));
								$form->select_date($dluodatesuffix,'dluo'.$suffix,'','',1,"");
								print '</td>';
								print '<td colspan="2">&nbsp</td>';
								// Qty ordered + qty already dispatached
							}
							else
							{
								$type = 'dispatch';
								print '<td align="right">'.img_picto($langs->trans('AddStockLocationLine'),'split.png','onClick="addDispatchLine('.$i.',\''.$type.'\')"').'</td>';	// Dispatch column
								print '<td></td>';
								print '</tr>';
								print '<tr '.$bc[$var].' name="'.$type.$suffix.'">';
								print '<td colspan="6">';
								print '<input name="fk_commandefourndet'.$suffix.'" type="hidden" value="'.$objp->rowid.'">';
								print '<input name="product'.$suffix.'" type="hidden" value="'.$objp->fk_product.'">';
								print '<input name="pu'.$suffix.'" type="hidden" value="'.$up_ht_disc.'"><!-- This is a up including discount -->';
								// hidden fields for js function
								print '<input id="qty_ordered'.$suffix.'" type="hidden" value="'.$objp->qty.'">';
								print '<input id="qty_dispatched'.$suffix.'" type="hidden" value="'.(float) $products_dispatched[$objp->rowid].'">';
								print '</td>';
							}
							// Dispatch
							print '<td align="right">';
							//print '<input id="qty'.$suffix.'" name="qty'.$suffix.'" type="text" size="8" value="'.(GETPOST('qty'.$suffix)!='' ? GETPOST('qty'.$suffix) : $remaintodispatch).'">';
							print '<input id="qty'.$suffix.'" name="qty'.$suffix.'" type="text" size="8" value="'.$remaintodispatch.'">';
							print '</td>';

							// Warehouse
							print '<td align="right">';
							if ($user->admin)
							{
								if (count($listwarehouses)>1)
								{
									print $formproduct->selectWarehousesadd((GETPOST("entrepotall")?GETPOST("entrepotall"):GETPOST("entrepot".$suffix)), "entrepot".$suffix,'',1,0,$objp->fk_product);
								}
								elseif  (count($listwarehouses)==1)
								{
									print $formproduct->selectWarehousesadd((GETPOST("entrepotall")?GETPOST("entrepotall"):GETPOST("entrepot".$suffix)), "entrepot".$suffix,'',0,0,$objp->fk_product);
								}
								else
								{
									print $langs->trans("NoWarehouseDefined");
								}
							}
							else
							{

								print '<select id="entrepot'.$suffix.'" name="entrepot'.$suffix.'">'.$optionsEntrepot.'</select>';
							}
							print "</td>\n";

							print "</tr>\n";
						}
					}
					$i++;
				}
				$db->free($resql);
			}
			else
			{
				dol_print_error($db);
			}

			print "</table>\n";
			print "<br/>\n";

			//vamos a actualizar el pedido segun la variable statusDispatch
			if (!$conf->almacen->enabled) $nbproduct = 0;
			if ($nbproduct)
			{
				//creamos mas datos para el ingreso del material
				$objecttype = new Ctypemouvement($db);


				print '<table>';

				print '<tr>';
				print '<td width="20%" class="fieldrequired">'.$langs->trans("Type").'</td><td>';
				$filterstatic = " AND t.type = 'E'";
				$res = $objecttype->fetchAll('ASC', 't.label', 0, 0, array(1=>1),'AND',$filterstatic);
				$options = '';
				if ($res>0)
				{
					foreach ($objecttype->lines AS $j => $line)
					{
						$options.= '<option value="'.$line->id.'" '.(GETPOST('fk_type_mouvement') == $line->id?'selected':'').'>'.$line->label.'</option>';
					}
				}
				print '<select name="fk_type_mouvement" required>'.$options.'</select>';;
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
					print $form->select_departament((GETPOST('fk_departament')?GETPOST('fk_departament'):$objectadd->fk_departament),'fk_departament','',0,1);
					print '</td>';
					print '</tr>';
				}

				//fuente financiamiento
				if ($conf->global->ALMACEN_FOUNDING_SOURCE)
				{
					print '<tr>';
					print '<td width="20%">'.$langs->trans('Foundingsource').'</td>';
					print '<td colspan="4">';
					print $form->select_founding_source((GETPOST('fk_source')?GETPOST('fk_source'):$fk_source),'fk_source','',0,1,'');
					print '</td>';
					print '</tr>';
				}

				print '<input type="hidden" name="fk_soc" value="'.$object->fk_soc.'">';

				print '<tr>';
				print '<td width="20%">'.$langs->trans("Document").'</td>';
				print '<td colspan="4">';
				print '<input type="text" name="ref_ext" size="10" value="'.GETPOST("ref_ext").'">';
				print '</td>';
				print '</tr>';

				print '</table>';

				print $langs->trans("Comment").' : ';
				print '<input type="text" size="60" maxlength="128" name="comment" value="';
				print $_POST["comment"]?GETPOST("comment"):$langs->trans("DispatchSupplier",$commande->ref).' '.$commande->ref;
				// print ' / '.$commande->ref_supplier;	// Not yet available
				print '" class="flat"> &nbsp; ';

				//print '<div class="center">';
				print '<input type="submit" class="button" value="'.$langs->trans("DispatchVerb").'"';
				if (count($listwarehouses) <= 0) print ' disabled';
				print '>';
				//print '</div>';
			}
			if (! $nbproduct && $nbfreeproduct)
			{
				print $langs->trans("NoPredefinedProductToDispatch");
			}

			print '</form>';
		}

		dol_fiche_end();


		// List of lines already dispatched
		$sql = "SELECT p.ref, p.label,";
		$sql.= " e.rowid as warehouse_id, e.label as entrepot,";
		$sql.= " cfd.rowid as dispatchlineid, cfd.fk_product, cfd.qty, cfd.eatby, cfd.sellby, cfd.batch, cfd.comment, cfd.status,";
		$sql.= " cfda.ref AS ref_ext, cfda.datec AS date_c";
		$sql.= " FROM ".MAIN_DB_PREFIX."product as p,";
		$sql.= " ".MAIN_DB_PREFIX."commande_fournisseur_dispatch as cfd";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."entrepot as e ON cfd.fk_entrepot = e.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."commande_fournisseur_dispatch_add as cfda ON cfd.rowid = cfda.fk_commande_fournisseur_dispatch";
		$sql.= " WHERE cfd.fk_commande = ".$commande->id;
		$sql.= " AND cfd.fk_product = p.rowid";
		$sql.= " ORDER BY cfd.rowid ASC";

		$resql = $db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;

			if ($num > 0)
			{
				print "<br/>\n";

				print load_fiche_titre($langs->trans("ReceivingForSameOrder"));

				print '<table class="noborder" width="100%">';

				print '<tr class="liste_titre">';
				print '<td>'.$langs->trans("Refext").'</td>';
				print '<td>'.$langs->trans("Date").'</td>';
				print '<td>'.$langs->trans("Description").'</td>';
				if (! empty($conf->productbatch->enabled))
				{
					print '<td>'.$langs->trans("batch_number").'</td>';
					print '<td>'.$langs->trans("l_eatby").'</td>';
					print '<td>'.$langs->trans("l_sellby").'</td>';
				}
				print '<td align="right">'.$langs->trans("QtyDispatched").'</td>';
				print '<td></td>';
				print '<td>'.$langs->trans("Warehouse").'</td>';
				print '<td>'.$langs->trans("Comment").'</td>';
				if (! empty($conf->global->SUPPLIER_ORDER_USE_DISPATCH_STATUS)) print '<td align="center" colspan="2">'.$langs->trans("Status").'</td>';
				print "</tr>\n";

				$var=false;

				while ($i < $num)
				{
					$objp = $db->fetch_object($resql);
					//vamos a buscar el documento con el que salio

					print "<tr ".$bc[$var].">";
					print '<td>'.$objp->ref_ext.'</td>';
					print '<td>'.dol_print_date($objp->date_c,'day').'</td>';
					print '<td>';
					print '<a href="'.DOL_URL_ROOT.'/product/fournisseurs.php?id='.$objp->fk_product.'">'.img_object($langs->trans("ShowProduct"),'product').' '.$objp->ref.'</a>';
					print ' - '.$objp->label;
					print "</td>\n";

					if (! empty($conf->productbatch->enabled))
					{
						print '<td>'.$objp->batch.'</td>';
						print '<td>'.dol_print_date($db->jdate($objp->eatby),'day').'</td>';
						print '<td>'.dol_print_date($db->jdate($objp->sellby),'day').'</td>';
					}

					// Qty
					$prodtmp->fetch($objp->fk_product);
					print '<td align="right">'.$objp->qty.' '.$prodtmp->getLabelOfUnit('short').'</td>';
					print '<td>&nbsp;</td>';

					// Warehouse
					print '<td>';
					$warehouse_static->id=$objp->warehouse_id;
					$warehouse_static->libelle=$objp->entrepot;
					print $warehouse_static->getNomUrl(1);
					print '</td>';

					// Comment
					print '<td>'.dol_trunc($objp->comment).'</td>';

					// Status
					if (! empty($conf->global->SUPPLIER_ORDER_USE_DISPATCH_STATUS))
					{
						print '<td align="right">';
						$supplierorderdispatch->status = (empty($objp->status)?0:$objp->status);
						//print $supplierorderdispatch->status;
						print $supplierorderdispatch->getLibStatut(5);
						print '</td>';

						// Add button to check/uncheck disaptching
						print '<td align="center">';
						if ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->fournisseur->commande->receptionner))
							|| (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->fournisseur->commande_advance->check))
						)
						{
							if (empty($objp->status))
							{
								print '<a class="button buttonRefused" href="#">'.$langs->trans("Approve").'</a>';
								print '<a class="button buttonRefused" href="#">'.$langs->trans("Deny").'</a>';
							}
							else
							{
								print '<a class="button buttonRefused" href="#">'.$langs->trans("Disapprove").'</a>';
								print '<a class="button buttonRefused" href="#">'.$langs->trans("Deny").'</a>';
							}
						}
						else
						{
							$disabled='';
							if ($commande->statut == 5) $disabled=1;
							if (empty($objp->status))
							{
								print '<a class="button'.($disabled?' buttonRefused':'').'" href="'.$_SERVER["PHP_SELF"]."?id=".$id."&action=checkdispatchline&lineid=".$objp->dispatchlineid.'">'.$langs->trans("Approve").'</a>';
								print '<a class="button'.($disabled?' buttonRefused':'').'" href="'.$_SERVER["PHP_SELF"]."?id=".$id."&action=denydispatchline&lineid=".$objp->dispatchlineid.'">'.$langs->trans("Deny").'</a>';
							}
							if ($objp->status == 1)
							{
								print '<a class="button'.($disabled?' buttonRefused':'').'" href="'.$_SERVER["PHP_SELF"]."?id=".$id."&action=uncheckdispatchline&lineid=".$objp->dispatchlineid.'">'.$langs->trans("Reinit").'</a>';
								print '<a class="button'.($disabled?' buttonRefused':'').'" href="'.$_SERVER["PHP_SELF"]."?id=".$id."&action=denydispatchline&lineid=".$objp->dispatchlineid.'">'.$langs->trans("Deny").'</a>';
							}
							if ($objp->status == 2)
							{
								print '<a class="button'.($disabled?' buttonRefused':'').'" href="'.$_SERVER["PHP_SELF"]."?id=".$id."&action=uncheckdispatchline&lineid=".$objp->dispatchlineid.'">'.$langs->trans("Reinit").'</a>';
								print '<a class="button'.($disabled?' buttonRefused':'').'" href="'.$_SERVER["PHP_SELF"]."?id=".$id."&action=checkdispatchline&lineid=".$objp->dispatchlineid.'">'.$langs->trans("Approve").'</a>';
							}
						}
						print '</td>';
					}

					print "</tr>\n";

					$i++;
					$var=!$var;
				}
				$db->free($resql);

				print "</table>\n";
			}
		}
		else
		{
			dol_print_error($db);
		}
	}
	else
	{
		// Commande	non	trouvee
		dol_print_error($db);
	}
}


llxFooter();

$db->close();
