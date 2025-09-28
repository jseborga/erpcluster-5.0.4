<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *	\file       htdocs/almacen/fiche.php
 *	\ingroup    Almacen
 *	\brief      Page fiche fabrication
 */

require("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
//unico archivo extension del html.form
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formv.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.getutil.class.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacenext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendetext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendetfabricationext.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/solalmacenlog.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/lib/almacen.lib.php");

require_once(DOL_DOCUMENT_ROOT."/core/lib/stock.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
if ($conf->projet->enabled && $conf->monprojet->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formprojetext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';
}
if ($conf->orgman->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
}

$langs->load("almacen");
$langs->load("products");
$langs->load("stocks");
$langs->load("companies");

if ($conf->fabrication->enabled)
	$langs->load("fabrication@fabrication");

$action=GETPOST('action');

$id = GETPOST('id');
$warehouseid    = GETPOST("warehouseid");
$fk_fabrication = GETPOST("fk_fabrication");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
//verificamos el periodo
verif_year();


$formproduct=new FormProduct($db);
//$objCommande = new Commande($db);
//if ($conf->fabrication->enabled)
//	$objUnits = new Units($db);
  //llx_units
$object = new Solalmacenext($db);
$objentrepotuser = new Entrepotuserext($db);
$objDepartament = new Pdepartamentext($db);

$entrepot = new Entrepot($db);
$objuser = new User($db);
$objUser = new User($db);

//verificamos saldos de productos
$objSollog = new Solalmacenlog($db);
$extrafields = new ExtraFields($db);
// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);
$extralabelsuser=$extrafields->fetch_name_optionals_label($objuser->table_element);
if (!empty($id))
	$object->fetch($id);


$aFilterent = array();
$aFilterentsol = array();
$filterusersol = '';
$now = dol_now();
if (!$user->admin) list($aFilterent, $filteruser,$aFilterentsol, $filterusersol,$aAreadirect,$fk_areaasign,$filterarea,$aFilterarea, $fk_user_resp,$aExcluded) = verif_accessalm();

$aLog = array(-1=>$langs->trans('StatusOrderCanceledShort'),0=>$langs->trans('StatusOrderDraftShort'),1=>$langs->trans('StatusOrderValidated'),6=>$langs->trans('StatusOrderApproved'),2=>$langs->trans('StatusOrderSent'),3=>$langs->trans('StatusOrderToBillShort'),4=>$langs->trans('StatusOrderProcessed'),5=>$langs->trans('StatusOrderoutofstock'));
$aLog = array(-2=>$langs->trans('Rejected'),
	-1=>$langs->trans('Annulled'),
	0=>$langs->trans('Draft'),
	6=>$langs->trans('Approved'),
	1=>$langs->trans('Validated'),
	2=>$langs->trans('Delivered'),
	5=>$langs->trans('StatusOrderoutofstock'));

/*
 * View
 */

$form=new Formv($db);
$getUtil = new getUtil($db);
$formcompany=new FormCompany($db);
if ($conf->projet->enabled && $conf->monprojet->enabled)
	$formproject = new FormProjetsext($db);

$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
//llxHeader("",$langs->trans("ApplicationsWarehouseCard"),$help_url);
$morejs=array("/almacen/javascript/almacen.js");
llxHeader('',$langs->trans("ApplicationsWarehouseCard"),$help_url,'','','',$morejs,'',0,0);

if ($id)
{
	dol_htmloutput_mesg($mesg);
	$result = $object->fetch($id);
	if ($result < 0)
	{
		dol_print_error($db);
	}
		//verificamos si tiene orden de producttion
	if ($object->fk_fabrication > 0)
	{
			//buscamos si existe pedidos a almacen con orden de produccion
		$res = $object->getlist_op($object->fk_fabrication);
			//echo ' res '.$res .' >0 && '.count($object->linealm).' > 0';
		if ($res > 0 && count($object->linealm) > 0)
			$lAdditem = false;
	}

	$objectdet = new Solalmacendetext($db);

		//incluimos si corresponde
	$aArrayItem = $objectdet->list_item($_GET["id"]);
	if ($lAdditem && empty($aArrayItem))
	{
		include DOL_DOCUMENT_ROOT.'/almacen/includes/addlines.php';
		$aArrayItem = $objectdet->list_item($_GET["id"]);
	}

	if (count($aArrayItem) <=0 || $_SESSION['cargaFabrication'][$_GET['id']] <=1)
	{
			//include DOL_DOCUMENT_ROOT.'/almacen/includes/addlines.php';
			//$aArrayItem = $objectDet->list_item($_GET["id"]);
	}
	$numLinesItem = count($aArrayItem);

	  //$commande = new Commande($db);
	$aItemf = array();
	$objEntrepot = new Entrepot($db);

	if ($conf->fabrication->enabled)
	{
	//	$objFabrication = new Fabrication($db);
	//	if ($object->fk_fabrication>0)
	//	{
	//		$objFabrication->fetch($object->fk_fabrication);
	//		$objFabrication->fetch_lines();
	//		if ($objFabrication->lines)
	//		{
	//			foreach ($objFabrication->lines AS $j => $line)
	//			{
	//				$aItemf[$line->rowid] = $line->libelle;
	//			}
	//		}
	//	}
	}

	  // Affichage fiche
	if ($action <> 'edit' && $action <> 're-edit')
	{

		$head = solalmacen_prepare_head($object);
		dol_fiche_head($head, 'log', $langs->trans("Warehouse orders"), 0, DOL_URL_ROOT.'/almacen/img/order.png',1);
		//verificamos que pertenezca al usuario
		if (!$user->admin && $user->id != $object->fk_user_create)
		{
			if (!$user->rights->almacen->pedido->app && !$user->rights->almacen->pedido->ent)
			{
				if (!$aFilterent[$object->fk_entrepot])
				{
					$error++;
				}
			}
			if ($user->rights->almacen->pedido->val)
			{
				$aDepartamentval = $objDepartament->verif_accessresp($user->fk_member);
				if (!empty($aAreadirect))
				{
					foreach ($aAreadirect AS $j)
						$aDepartamentval[$j] = $j;
				}
				$error=0;
				if (!$aDepartamentval[$object->fk_departament]) $error++;
			}
			if ($error)
			{
				print $mesg = '<div class="error">'.$langs->trans('No esta permitido para ver').'</div>';
				exit;
			}
		}
		print '<table class="border" width="100%">';

			// Ref
		print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td colspan="3">';
		print $object->ref;
		print '</td></tr>';

			//Entrepot source
		$objEntrepot->fetch($object->fk_entrepot_from);
		if ($object->fk_entrepot_from>0)
		{
			print '<tr><td>'.$langs->trans("Entrepotfrom").'</td><td colspan="3">';
			print $objEntrepot->libelle." - ".$objEntrepot->lieu;
			print '</td></tr>';
		}
			//else
			//{
			//	print $langs->trans('Sin almacen destino');
			//}

			//Entrepot
		$objEntrepot->fetch($object->fk_entrepot);
		print '<tr><td>'.$langs->trans("Deliver by").'</td><td colspan="3">';
		print $objEntrepot->getNomUrl(1);
		print '</td></tr>';

			// Fabrication
		if ($conf->fabrication->enabled)
		{
		//	$objFabrication->fetch($object->fk_fabrication);
		//	print '<tr><td width="25%">'.$langs->trans('OrderProduction').'</td><td colspan="3">';
		//	print $objFabrication->ref;
		//	print '</td></tr>';
		}
		  //user
		$objuser->fetch($object->fk_user);
		print '<tr><td width="25%">'.$langs->trans('Applicant').'</td><td colspan="3">';
		if ($objuser->id == $object->fk_user)
			print $objuser->getNomUrl(1);
		else
			print '&nbsp;';
		print '</td></tr>';

		if ($conf->projet->enabled && $conf->monprojet->enabled)
		{
			$projet = new Projectext($db);
			$projet->fetch($object->fk_projet);
			print '<tr><td>'.$langs->trans("Project").'</td><td colspan="3">';
			if ($projet->id == $object->fk_projet)
				print $projet->getNomUrladd(1);
			print '</td></tr>';

		}
			//area solicitante
		if ($object->fk_departament>0)
		{
			print '<tr><td width="25%">'.$langs->trans('Departament').'</td><td colspan="3">';

			$getUtil->fetch_departament($object->fk_departament,'');
			print $getUtil->label;
			print '</td></tr>';
		}
			//fecha creacion
		print '<tr><td>'.$langs->trans("Date").'</td><td colspan="3">';
		print $object->date_creation ? dol_print_date($object->date_creation,'day') : '&nbsp;';

		print '</td></tr>';

			//fecha delivery
		print '<tr><td>'.$langs->trans("Datedelivery").'</td><td colspan="3">';
		print $object->date_delivery ? dol_print_date($object->date_delivery,'day') : '&nbsp;';

		print '</td></tr>';

			// Description
		print '<tr><td valign="top">'.$langs->trans("Description").'</td><td colspan="3">'.nl2br($object->description).'</td></tr>';


			// Statut
			// 4 por defecto
			// para emavias 6
		print '<tr><td>'.$langs->trans("Status").'</td><td colspan="3">'.$object->getLibStatut(4).'</td></tr>';


		print "</table>";

		dol_fiche_end();

			//listamos el log
		dol_fiche_head();
		include_once DOL_DOCUMENT_ROOT.'/almacen/tpl/solalmacenlog_list.tpl.php';
		dol_fiche_end();

	}
}



llxFooter();

$db->close();
?>
