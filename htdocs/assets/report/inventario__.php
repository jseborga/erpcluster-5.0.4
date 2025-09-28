<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *      \file       htdocs/almacen/liste.php
 *      \ingroup    almacen
 *      \brief      Page liste des solicitudes a almacenes
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");

require_once(DOL_DOCUMENT_ROOT."/assets/core/modules/assets/modules_assets.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/assetsext.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/cassetsgroup.class.php");

require_once(DOL_DOCUMENT_ROOT."/assets/lib/assets.lib.php");


$langs->load("assets");
$langs->load("stocks");

//$langs->load("fabrication@fabrication");

if (!$user->rights->assets->repinv->write) accessforbidden();

$fk_group = GETPOST('fk_group');
$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$action 	= GETPOST('action','alpha');
$sortfield  = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder  = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
$yesnoprice = GETPOST('yesnoprice');
if (! $sortfield) $sortfield="sm.datem";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;



$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;

if (isset($_POST['fk_entrepot']) || isset($_GET['fk_entrepot']))
	$_SESSION['kardexfk_entrepot'] = ($_POST['fk_entrepot']?$_POST['fk_entrepot']:$_GET['fk_entrepot']);
$fk_entrepot = $_SESSION['kardexfk_entrepot'];

//filtramos por almacenes designados segun usuario
$objAssets = new Assetsext($db);
$objGroup = new cAssetsgroup($db);
$objuser = new User($db);

//$periodo = new Contabperiodo($db);

//verificamos el periodo
//verif_year();

$aFilterent = array();
$id = $_SESSION['kardexid'];

$filteruser = '';
if (!$user->admin)
{
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_user = ".$user->id;
	$filterstatic.= " AND t.active = 1";
	$res = $objentrepotuser->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
	//$res = $objentrepot->getlistuser($user->id);
	if ($res > 0)
	{
		$num = count($objentrepotuser->lines);
		$i = 0;
		$line = $objentrepotuser->lines;
		for ($i=0; $i < $num; $i++)
		{
			if (!empty($filteruser))$filteruser.= ',';
			$filteruser.= $line[$i]->fk_entrepot;
			$aFilterent[$line[$i]->fk_entrepot] = $line[$i]->fk_entrepot;
		}
	}
}

//actions
$dateini = dol_now();
$datefin = dol_now();
$dateinisel = dol_now();
$datefinsel = dol_now();
if ($action == 'builddoc')	// En get ou en post
{
	if ($fk_group > 0)
		$res = $objGroup->fetch($fk_group);
	//if (GETPOST('model'))
	//{
	//	$objectUrqEntrepot->setDocModel($user, GETPOST('model'));
	//}
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
	 $objGroup->modelpdf = GETPOST('model');
	$result=assets_pdf_create($db, $objGroup, $objGroup->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);

	if ($result <= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
	else
	{
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id.'&action=edit');
		exit;
	}
}
	// Remove file in doc form
if ($action == 'remove_file') 
{
	if ($id > 0) 
	{
		require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

		$langs->load("other");
		$upload_dir = $conf->almacen->dir_output;
		//. '/' . dol_sanitizeFileName($objectdoc->ref);

		$file = $upload_dir . '/' . GETPOST('file');
		$ret = dol_delete_file($file, 0, 0, 0, $product);
		if ($ret)
			setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else
			setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
		$action = '';
	}
}

if ($action == 'edit')
{
	$error=0;
	if (isset($_POST['diyear']))
	{
		$dimonth = strlen(GETPOST('dimonth'))==1?'0'.GETPOST('dimonth'):GETPOST('dimonth');
		$diday = strlen(GETPOST('diday'))==1?'0'.GETPOST('diday'):GETPOST('diday');
		$diyear = GETPOST('diyear');
		$dateinisel  = dol_mktime(12, 0, 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));
		$aDate = dol_get_prev_day(GETPOST('diday'), GETPOST('dimonth'), GETPOST('diyear'));

		//$aDate = dol_get_prev_day($diday, $dimonth, $diyear);
		$dimonth = strlen($aDate['month'])==1?'0'.$aDate['month']:$aDate['month'];
		$diday = strlen($aDate['day'])==1?'0'.$aDate['day']:$aDate['day'];

		$dateini  = dol_mktime(23, 59, 50, $dimonth, $diday, $aDate['year']);

		$dfmonth = strlen(GETPOST('dfmonth'))==1?'0'.GETPOST('dfmonth'):GETPOST('dfmonth');
		$dfday = strlen(GETPOST('dfday'))==1?'0'.GETPOST('dfday'):GETPOST('dfday');
		$datefin  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
		$datefinsel  = dol_mktime(23, 59, 59, $dfmonth,  $dfday,  GETPOST('dfyear'));
		if ($dateinisel <= $datefinsel)
		{
			$_SESSION['assetsinv']['dateini'] = $dateini;
			$_SESSION['assetsinv']['dateinisel'] = $dateinisel;
			$_SESSION['assetsinv']['datefin'] = $datefin;
			$_SESSION['assetsinv']['datefinsel'] = $datefinsel;
		}
		else
		{
			$error++;
			setEventMessage($langs->trans("Errordatenovalid", GETPOST('id')), 'errors');
		}
		if (empty($error))
			setEventMessage($langs->trans("Proceso satisfactorio", GETPOST('id')));
	}
}
else
	unset($_SESSION['newKardex']);

if (!empty($_SESSION['assetsinv']['dateini'])) $dateini = $_SESSION['kardex']['dateini'];
if (!empty($_SESSION['assetsinv']['dateinisel'])) $dateinisel = $_SESSION['kardex']['dateinisel'];
if (!empty($_SESSION['assetsinv']['datefin'])) $datefin = $_SESSION['kardex']['datefin'];
if (!empty($_SESSION['assetsinv']['datefinsel'])) $datefinsel = $_SESSION['kardex']['datefinsel'];


$formfile = new Formfile($db);
$form = new Formv($db);
if ($fk_entrepot)
{
	$res = $objecten->fetch($fk_entrepot);
}
$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';


//$aArrjs = array('almacen/javascript/recargar.js');
//$aArrcss = array('almacen/css/style.css');
$help_url='EN:Module_Assets_En|FR:Module_Assets|ES:M&oacute;dulo_Assets';

llxHeader("",$langs->trans("Inventario"),$help_url,'','','',$aArrjs,$aArrcss); 

print_barre_liste($langs->trans("Inventario"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
print '<input type="hidden" name="yesnoprice" value="'.$yesnoprice.'">';

print '<table class="border" width="100%">';
// Entrepot Almacen
print '<tr>';
print '<td width="25%" class="fieldrequired">'.$langs->trans('Group').'</td><td colspan="3">';
$res = $objGroup->fetchAll('ASC','code',0,0,array(1=>1),'AND',$filterstatic);
$options = '<option value="0">'.$langs->trans('All').'</option>';
if ($res > 0)
{
	$lines = $objGroup->lines;
	foreach ($lines  AS $J => $line)
	{
		$selected = '';
		if ($fk_group == $line->id) $selected = ' selected';
		$options .= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
	}
}
print '<select name="fk_group">'.$options.'</select>';
print '</td>';
print '</tr>';


// desde fecha
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="3">';
$form->select_date($dateinisel,'di','','','',"crea_commande",1,1);

print '</td></tr>';

// hasta fecha
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="3">';
$form->select_date($datefinsel,'df','','','',"crea_commande",1,1);

print '</td></tr>';

print '</table>';
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';


if (($action == 'edit' || $action=='edits') )
{
	print '<br><table class="noborder" width="100%">';
	
	$objAsset->fetchAll(.,.,,.);
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("Nro").'</td>';
	print '<td>'.$langs->trans("Codeassets").'</td>';
	print '<td>'.$langs->trans("Nameassets").'</td>';

	print '<td align="right">'.$langs->trans("NumberOfUnit").'</td>';
	print '</tr>';
	$lines = $objAsset->lines;
	foreach ($lines AS $j => $line)
	{

		
	}

	if ($user->rights->almacen->kard->lirev)
	{
		if (empty($typemethod))
		{
			print '<td align="right">'.$langs->trans("AverageUnitPricePMPShort").'</td>';
			print '<td align="right">'.$langs->trans("EstimatedStockValueShort").'</td>';
		}
		if ($typemethod==1)
		{
			print '<td align="right">'.$langs->trans("PEPS").'</td>';
			print '<td align="right">'.$langs->trans("EstimatedStockValuePEPS").'</td>';
		}
		if ($typemethod==2)
		{
			print '<td align="right">'.$langs->trans("UEPS").'</td>';
			print '<td align="right">'.$langs->trans("EstimatedStockValueUEPS").'</td>';
		}
		print '<td align="right">'.$langs->trans("SellPricesf").'</td>';
		print '<td align="right">'.$langs->trans("EstimatedStockValueSellShortsf").'</td>';
		print '<td align="right">'.$langs->trans("SellPriceMin").'</td>';
		print '<td align="right">'.$langs->trans("EstimatedStockValueSellShort").'</td>';
	}
	print '</tr>';

	//validamos permisos de lectura
	$product->fetch($id);
	$unit = $product->getLabelOfUnit('short');
	$lView = false;
	if($user->rights->almacen->leersell && $product->status == 1) $lView = true;
	if($user->rights->almacen->leernosell && $product->status_buy == 1) $lView = true;
	if(!$user->rights->almacen->leersell && !$user->rights->almacen->leernosell) $lView = false;
	if ($user->admin) $lView = true;
	if (!$lView)
		setEventMessage($langs->trans("No tiene permisos para ver el producto", GETPOST('urlfile')), 'errors');

	if ($lView)
	{
		$sql = "SELECT e.rowid, e.label, ps.reel, p.pmp ";
		$sql.= " FROM ".MAIN_DB_PREFIX."entrepot as e,";
		$sql.= " ".MAIN_DB_PREFIX."product_stock as ps,";
		$sql.= " ".MAIN_DB_PREFIX."product as p";
		$sql.= " WHERE ps.reel != 0";
		$sql.= " AND ps.fk_entrepot = e.rowid";
		$sql.= " AND ps.fk_product = p.rowid";
		$sql.= " AND e.entity = ".$conf->entity;
		$sql.= " AND ps.fk_product = ".$id;
		$sql.= " ORDER BY e.label";

		$entrepotstatic=new Entrepot($db);
		$total=0;
		$totalvalue=$totalvaluesell=0;
		$aSaldores = array();
		$resql=$db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$total=$totalwithpmp;
			$i=0; 
			$var=false;
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$product     = new Product($db);
				$product->fetch($id);
				$entrepotstatic->id=$obj->rowid;
				$entrepotstatic->libelle=$obj->label;
				print '<tr '.$bc[$var].'>';
				print '<td><a href="kardex.php?id='.$id.'&fk_entrepot='.$obj->rowid.'&action=edits">'.$entrepotstatic->libelle.'</a></td>';

			//	print '<td>'.$entrepotstatic->getNomUrl(1).'</td>';
				print '<td align="right">'.$obj->reel.($obj->reel<0?' '.img_warning():'').'</td>';
				$movement->saldoanterior($obj->rowid,$dateini,$id);
				$aSaldo = $movement->aSaldo[$id];
				$aSaldores[$obj->rowid] = $aSaldo;
				$movement->mouvement_period($obj->rowid,$dateini,$datefin,$id);
				$aIng = $movement->aIng[$id];
				$aSal = $movement->aSal[$id];
				if ($user->rights->almacen->kard->lirev)
				{
					// PMP
					if (empty($typemethod))
					{
						print '<td align="right">'.(price2num($obj->pmp)?price2num($obj->pmp,'MU'):'').'</td>';
			 			// Ditto : Show PMP from movement or from product
						print '<td align="right">'.(price2num($obj->pmp)?price(price2num($obj->pmp*$obj->reel,'MT')):'').'</td>';
					}
					if ($typemethod==1)
					{
						$valueproduct = $aSaldo['value_peps']+$aIng['value_peps']+$aSal['value_peps'];
						print '<td align="right">&nbsp;</td>';
			 			// Ditto : Show PMP from movement or from product
						print '<td align="right">'.(price2num($valueproduct)?price(price2num($valueproduct,'MT')):'').'</td>';
					}
					if ($typemethod==2)
					{
						$valueproduct = $aSaldo['value_peps']+$aIng['value_peps']+$aSal['value_peps'];
						print '<td align="right"></td>';
			 			// Ditto : Show PMP from movement or from product
						print '<td align="right">'.(price2num($valueproduct)?price(price2num($valueproduct,'MT')):'').'</td>';
					}

		 // Ditto : Show PMP from movement or from product
		// Sell price
					print '<td align="right">';
					if (empty($conf->global->PRODUIT_MUTLI_PRICES))
						print price(price2num($product->price,'MU'));
					else
						print $langs->trans("Variable");
					print '</td>';
		// Ditto : Show PMP from movement or from product
					print '<td align="right">';
					if (empty($conf->global->PRODUIT_MUTLI_PRICES)) print price(price2num($product->price*$obj->reel,'MT')).'</td>';
		 // Ditto : Show PMP from movement or from product
					else
						print $langs->trans("Variable");
					print '</td>';
		// Sell price tot
					print '<td align="right">';
					if (empty($conf->global->PRODUIT_MUTLI_PRICES))
						print price(price2num($product->price_ttc,'MU'));
					else
						print $langs->trans("Variable");
					print '</td>';
		 // Ditto : Show PMP from movement or from product
					print '<td align="right">';
					if (empty($conf->global->PRODUIT_MUTLI_PRICES)) print price(price2num($product->price_ttc*$obj->reel,'MT')).'</td>';
		 // Ditto : Show PMP from movement or from product
					else
						print $langs->trans("Variable");
					print '</td>';
				}
				print '</tr>'; ;
				$total += $obj->reel;
				if (price2num($obj->pmp)) $totalwithpmp += $obj->reel;
				$totalvalue = $totalvalue + price2num($obj->pmp*$obj->reel,'MU');
		 // Ditto : Show PMP from movement or from product
				$totalvaluesell = $totalvaluesell + price2num($product->price*$obj->reel,'MU');
		 // Ditto : Show PMP from movement or from product
				$totalvalue1 = $totalvalue1 + price2num($obj->pmp*$obj->reel,'MU');
		 // Ditto : Show PMP from movement or from product
				$totalvaluesell1 = $totalvaluesell1 + price2num($product->price_ttc*$obj->reel,'MU');
		 // Ditto : Show PMP from movement or from product
				$i++;
				$var=!$var;
			}
		}
		else
			dol_print_error($db);

		print '<tr class="liste_total"><td align="right" class="liste_total">'.$langs->trans("Total").':</td>';
		print '<td class="liste_total" align="right">'.$total.'</td>';
		if ($user->rights->almacen->kard->lirev)
		{
			print '<td class="liste_total" align="right">';
			print ($totalwithpmp?price($totalvalue/$totalwithpmp):'&nbsp;');
			print '</td>';
			print '<td class="liste_total" align="right">';
			print price(price2num($totalvalue,'MT'));
			print '</td>';
			print '<td class="liste_total" align="right">';
			if (empty($conf->global->PRODUIT_MUTLI_PRICES))
				print ($total?price($totalvaluesell/$total):'&nbsp;');
			else
				print $langs->trans("Variable");
			print '</td>';
			print '<td class="liste_total" align="right">';
			if (empty($conf->global->PRODUIT_MUTLI_PRICES))
				print price(price2num($totalvaluesell,'MT'));
			else
				print $langs->trans("Variable");
			print '</td>';
			print '<td class="liste_total" align="right">';
			if (empty($conf->global->PRODUIT_MUTLI_PRICES))
				print ($total?price($totalvaluesell1/$total):'&nbsp;');
			else
				print $langs->trans("Variable");
			print '</td>';
			print '<td class="liste_total" align="right">';
			if (empty($conf->global->PRODUIT_MUTLI_PRICES))
				print price(price2num($totalvaluesell1,'MT'));
			else
				print $langs->trans("Variable");
			print '</td>';
		}
		print "</tr>";
		print "</table>";
		//armamos un array para el reporte
		$aKardex['fk_entrepot'] = $fk_entrepot;
		$aKardex['entrepot'] = $objecten->lieu;
		$aKardex['fk_product'] = $id;
		$aKardex['productref'] = $product->ref;
		$aKardex['productlabel'] = $product->label;
		$aKardex['yesnoprice'] = $yesnoprice;
		$aKardex['unit'] = $unit;
		$aKardex['dateini'] = $_SESSION['kardex']['dateinisel'];
		$aKardex['datefin'] = $_SESSION['kardex']['datefinsel'];

		$aKardex['lines'] = array();
		//movimiento del producto
		$sql  = "SELECT sm.rowid, sm.datem AS datem, sm.value, sm.price, sm.type_mouvement, sm.fk_user_author, sm.label, ";
		$sql.= " sm.fk_origin, sm.origintype, ";
		$sql.= " sma.balance_peps, sma.balance_ueps, sma.value_peps, sma.value_ueps ";
		$sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement AS sm";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."stock_mouvement_add AS sma ON sma.fk_stock_mouvement = sm.rowid ";
		$sql.= " WHERE fk_entrepot = ".$fk_entrepot;
		if (! empty($dateini) && !empty($datefin))
		{
			$sql .= " AND UNIX_TIMESTAMP(sm.datem) BETWEEN ".$dateini." AND ".$datefin;

			//$sql .= " AND sm.datem BETWEEN '".$db->idate($dateini)."' AND '".$db->idate($datefin)."'";
		}
		$sql.= " AND fk_product = ".$id;
		$sql.= " ORDER BY $sortfield $sortorder";

		$result = $db->query($sql);

		if ($result)
		{
			$num = $db->num_rows($result);
			$j = 0;
			$i = 0;
			//recuperamos el saldo para el almacen seleccionado
			$aSaldo = $aSaldores[$fk_entrepot];
			print_barre_liste($langs->trans("MovementOfTheStock"), $page, "kardex.php", "", $sortfield, $sortorder,'',$num);

			print '<table class="noborder" width="100%">';

			print "<tr class=\"liste_titre\">";
			print '<th></th>';
			print '<th align="center" colspan="3" class="thlineleft">'.$langs->trans('Fisico').'</th>';
			if ($user->rights->almacen->kard->lirev)
			{
				print '<th align="center" colspan="4" class="thlineleft">'.$langs->trans('Valorado').'</th>';
			}
			print '<th colspan="4" class="thlineleft"></th>';
			print '</tr>';

			print "<tr class=\"liste_titre\">";
			print_liste_field_titre($langs->trans("Date"),"kardex.php", "sm.datem","","","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Entry"),"kardex.php", "sm.value","","",'align="right"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Output"),"kardex.php", "sm.value","","",'align="right"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Balance"),"kardex.php", "sm.value","","",'align="right"',$sortfield,$sortorder);
			if ($user->rights->almacen->kard->lirev && $yesnoprice)
			{
				print_liste_field_titre($langs->trans("P.U."),"kardex.php", "","","",'align="left"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Entry"),"kardex.php", "","","",'align="left"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Output"),"kardex.php", "","","",'align="left"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Balance"),"kardex.php", "","","",'align="left"',$sortfield,$sortorder);

			}
			print_liste_field_titre($langs->trans("Doc"),"kardex.php", "","","",'align="center"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("MovementType"),"kardex.php", "sm.type_mouvement","","",'align="center"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("User"),"kardex.php", "sm.fk_user_author","","",'align="center"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Label"),"kardex.php", "sm.label",'','','align="left"',$sortfield,$sortorder);
			print "</tr>\n";

				//mostramos el saldo anterior
			print "<tr $bc[$var]>";
			print '<td>'.dol_print_date($dateini,'dayhour').'</td>';
			print '<td class="thlineleft">&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print '<td align="right">'.$aSaldo['qty'].'</td>';
			if ($user->rights->almacen->kard->lirev && $yesnoprice)
			{
				print '<td class="thlineleft">&nbsp;</td>';
				print '<td>&nbsp;</td>';
				print '<td>&nbsp;</td>';
				if (empty($typemethod))
				{
					print '<td align="right">'.price(price2num($aSaldo['value_ppp'],'MT')).'</td>';
					$balanceMount+=$aSaldo['value_ppp'];
				}
				if ($typemethod==1) 
				{
					print '<td align="right">'.price(price2num($aSaldo['value_peps'],'MT')).'</td>';
					$balanceMount+=$aSaldo['value_peps'];
				}
				if ($typemethod==2) 
				{
					print '<td align="right">'.price(price2num($aSaldo['value_ueps'],'MT')).'</td>';
					$balanceMount+=$aSaldo['value_ueps'];
				}
			}
			print '<td class="thlineleft">&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print '<td>'.$langs->trans('Saldo anterior').'</td>';
			print "</tr>\n";

			$aKardex['lines'][$j]['date'] = $dateini;
			$aKardex['lines'][$j]['balance'] = $aSaldo['qty']+0;
			$aKardex['lines'][$j]['detail'] = $langs->trans('Saldo anterior');
			$aKardex['lines'][$j]['vbalance'] = $balanceMount;
			if ($num)
			{
				$product     = new Product($db);
				$var=True;
				$balanceQty = 0;
				$balanceMount = 0;


				$balanceQty += $aSaldo['qty'];
				$j++;
				while ($i < $num)
				{
					//actualizando totales
					$objp = $db->fetch_object($result);
					$balanceQty += $objp->value;
					if (empty($typemethod))
					{
						$balanceMount += price2num($objp->value*$objp->price,'MU');
						$price = $objp->price;
					}
					if ($typemethod==1)
					{
						$balanceMount += price2num($objp->value*$objp->value_peps,'MU');
						$price = $objp->value_peps;
					}
					if ($typemethod==2)
					{
						$balanceMount += price2num($objp->value*$objp->value_ueps,'MU');
						$price = $objp->value_ueps;
					}

					$var=!$var;
					print "<tr $bc[$var]>";
					print '<td>'.dol_print_date($db->jdate($objp->datem),'dayhour').'</td>';
					$entrada = 0;
					$salida = 0;
					$ventrada = 0;
					$vsalida = 0;
					if ($objp->value < 0)
					{
						$salida = $objp->value * -1;
						$vSalida = price2num($objp->value * $price,'MU');
						print '<td class="thlineleft">&nbsp;</td>';
						print '<td align="right">'.$salida.'</td>';
					}
					elseif ($objp->value > 0)
					{
						$entrada = $objp->value;
						$vEntrada = price2num($objp->value*$price,'MU');
						print '<td align="right" class="thlineleft">'.$entrada.'</td>';
						print '<td>&nbsp;</td>';
					}
					else
					{
						print '<td class="thlineleft">&nbsp;</td>';
						print '<td>&nbsp;</td>';
					}
					print '<td align="right">'.$balanceQty.'</td>';
					if ($user->rights->almacen->kard->lirev && $yesnoprice)
					{
						print '<td class="thlineleft">'.price($price).'</td>';

						if ($objp->value < 0)
						{
							print '<td>&nbsp;</td>';
							print '<td align="right">'.price($vSalida).'</td>';
						}
						elseif ($objp->value > 0)
						{
							print '<td align="right">'.price($vEntrada).'</td>';
							print '<td>&nbsp;</td>';
						}

						print '<td align="right">'.price($balanceMount).'</td>';
					}
					//documento
					$link = '';
					$linkid = '';
					if ($objp->origintype=='solalmacendet')
					{
						$element = 'almacen';
						$selement = 'solalmacendet';
						$subelement = 'solalmacendet';

						$selementf = 'solalmacen';
						$subelementf = 'solalmacen';
						dol_include_once('/' . $element . '/class/' . $selement . '.class.php');
						$classname = ucfirst($subelement);
						$srcobject = new $classname($db);
						$srcobject->fetch($objp->fk_origin);
						$linkid.= ' '.$objp->fk_origin.'-'.$objp->origintype;
						dol_include_once('/' . $element . '/class/' . $selementf . '.class.php');
						$classname = ucfirst($subelementf);
						$srcobjectf = new $classname($db);
						$srcobjectf->fetch($srcobject->fk_almacen);
						$linkid.= ' alm '.$srcobject->fk_almacen;
						$link = $srcobjectf->getNomUrl(1);
					}
					if ($objp->origintype=='order_supplier')
					{
						$element = 'fourn';
						$selement = 'fournisseur.commande';
						$subelement = 'CommandeFournisseur';
						dol_include_once('/' . $element . '/class/' . $selement . '.class.php');
						$classname = ucfirst($subelement);
						$srcobject = new $classname($db);
						$srcobject->fetch($objp->fk_origin);
						$link = $srcobject->getNomUrl(1);
						$linkid.=' '.$objp->fk_origin.'-'.$objp->origintype;;
					}
					if ($objp->origintype=='stockmouvementtemp')
					{
						$element = 'almacen';
						$selement = 'stockmouvementtempext';
						$subelement = 'stockmouvementdoc';
						dol_include_once('/' . $element . '/class/' . $selement . '.class.php');
						$classname = ucfirst($selement);
						$srcobject = new $classname($db);
						$srcobject->fetch($objp->fk_origin);
						dol_include_once('/' . $element . '/class/' . $subelement . '.class.php');
						$classname = ucfirst($subelement);
						$srcobjectf = new $classname($db);
						$srcobjectf->fetch(0,$srcobject->ref);
						$link = $srcobjectf->getNomUrl(1);
						//$linkid.=' '.$objp->fk_origin.'-'.$objp->origintype;;
					}
					print '<td align="center" class="thlineleft">'.$link.'</td>';


					$mouvement = '';
					if ($objp->type_mouvement == 0) $mouvement = $langs->trans("ManualInput");
					if ($objp->type_mouvement == 1) $mouvement = $langs->trans("ManualOutput");
					if ($objp->type_mouvement == 2) $mouvement = $langs->trans("Output");
					if ($objp->type_mouvement == 3) $mouvement = $langs->trans("Input");
					print '<td align="center">'.$mouvement.'</td>';
					$login = '';
					$objuser->fetch($objp->fk_user_author);
					if ($objuser->id == $objp->fk_user_author)
						$login = $objuser->login;
					print '<td align="center">'.$login.'</td>';
					print '<td>'.$objp->label.'</td>';
					print "</tr>\n";
					$aKardex['lines'][$j]['date'] = $objp->datem;
					$aKardex['lines'][$j]['entrada'] = $entrada;
					$aKardex['lines'][$j]['salida'] = $salida;
					$aKardex['lines'][$j]['balance'] = $balanceQty;
					$aKardex['lines'][$j]['pu'] = $price;
					$aKardex['lines'][$j]['ventrada'] = $ventrada;
					$aKardex['lines'][$j]['vsalida'] = $vsalida;
					$aKardex['lines'][$j]['vbalance'] = $balanceMount;
					$aKardex['lines'][$j]['mouvement'] = $mouvement;
					$aKardex['lines'][$j]['user'] = $login;
					$aKardex['lines'][$j]['detail'] = $objp->label;
					$i++;
					$j++;
				}
			}
			$db->free($result);
			print "</table>";
			$newKardex[$fk_entrepot] = $aKardex;
			$_SESSION['newKardex'] = serialize($newKardex);
		}
		else
		{
			dol_print_error($db);
		}

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

			$model='kardex';
			if ($user->rights->almacen->inv->lirev && $yesnoprice)
				$model='kardexval';
			$objinv->id = $fk_entrepot;
			$objinv->fk_product = $id;
			$result=$objinv->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
			if ($result < 0) dol_print_error($db,$result);
		}
	}
}
print '<div class="tabsAction">';
		//documents
print '<table width="100%"><tr><td width="50%" valign="top">';
print '<a name="builddoc"></a>'; 
		// ancre




$objGroup->fetch($fk_group);
$diradd = '';
$filename = '';
if ($objGroup == $fk_group)
	$filename=dol_sanitizeFileName($objGroup->code).$diradd;
		//cambiando de nombre al reporte
$filedir=$conf->assets->dir_output;
$urlsource=$_SERVER['PHP_SELF'].'?fk_group='.$fk_group;
$genallowed=$user->rights->assets->repinv->write;
$delallowed=$user->rights->assets->repinv->del;
$objGroup->modelpdf = 'fractalinventario';
print '<br>';
print $formfile->showdocuments('assets',$filename,$filedir,$urlsource,$genallowed,$delallowed,$objGroup->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
$somethingshown=$formfile->numoffiles;
print '</td></tr></table>';
print "</div>";

$db->close();

llxFooter();
?>
