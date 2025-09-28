<?php
/* Copyright (C) 2001-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/product/index.php
 *  \ingroup    product
 *  \brief      Page accueil des produits et services
 */

require '../../main.inc.php';
if ($conf->contab->enabled)
  {
    require_once DOL_DOCUMENT_ROOT.'/contab/class/contabaccounting.class.php';
    require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatdet.class.php';
  }
$type=isset($_GET["type"])?$_GET["type"]:(isset($_POST["type"])?$_POST["type"]:'');

// Security check
//$result=restrictedArea($user,'contab');

$langs->load("contab");

if ($conf->contab->enabled)
  {
    $product_static = new Contabaccounting($db);
    $accounting     = new Contabaccounting($db);
  }

/*
 * View
 */

$transAreaType = $langs->trans("ContabArea");
$helpurl='';
$helpurl='EN:Module_Contab|FR:Module_Contab|ES:M&oacute;dulo_Contabilidad';


llxHeader("",$langs->trans("Contab"),$helpurl);

print_fiche_titre($transAreaType);

if (!$conf->contab->enabled)
  {
    $mesgExit = '<div>'.$langs->trans('Error, not accounting module enabled, please check the email or contact info@ubuntu-bo.com').'</div>';
    print $mesgExit;
    exit;
  }


//print '<table border="0" width="100%" class="notopnoleftnoright">';
//print '<tr><td valign="top" width="30%" class="notopnoleft">';
print '<div class="fichecenter"><div class="fichethirdleft">';


/*
 * Zone recherche produit/service
 */
$rowspan=2;
if (! empty($conf->barcode->enabled)) $rowspan++;
print '<form method="post" action="'.DOL_URL_ROOT.'/contab/accounts/liste.php">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<table class="noborder nohover" width="100%">';
print "<tr class=\"liste_titre\">";
print '<td colspan="3">'.$langs->trans("Search").'</td></tr>';
print "<tr ".$bc[false]."><td>";
print $langs->trans("Ref").':</td><td><input class="flat" type="text" size="14" name="sref"></td>';
print '<td rowspan="'.$rowspan.'"><input type="submit" class="button" value="'.$langs->trans("Search").'"></td></tr>';
if (! empty($conf->barcode->enabled))
{
	print "<tr ".$bc[false]."><td>";
	print $langs->trans("BarCode").':</td><td><input class="flat" type="text" size="14" name="sbarcode"></td>';
	//print '<td><input type="submit" class="button" value="'.$langs->trans("Search").'"></td>';
	print '</tr>';
}
print "<tr ".$bc[false]."><td>";
print $langs->trans("Other").':</td><td><input class="flat" type="text" size="14" name="sall"></td>';
//print '<td><input type="submit" class="button" value="'.$langs->trans("Search").'"></td>';
print '</tr>';
print "</table></form><br>";


/*
 * estadisticas inicio
 */
$prodser = array();

$sql = "SELECT p.rowid as id, p.ref, p.cta_name, p.cta_normal ";
$sql.= " FROM ".MAIN_DB_PREFIX."contab_accounting as p ";
$sql.= ' WHERE p.entity IN ('.getEntity($product_static->element, 1).')';
$sql.= " AND  cta_class = 2";
$sql.= " ORDER BY p.ref ";
$result = $db->query($sql);
while ($objp = $db->fetch_object($result))
  {
    //buscando el saldo de la cuenta
    $objcontabdet = new Contabseatdet($db);
    list($aArray,$aArrayDet) = $objcontabdet->get_list_account($objp->ref);
    $saldo = 0;
    $saldoD = 0;
    $saldoC = 0;
    foreach ((array) $aArray AS $typeaccount => $value)
      {
	if ($typeaccount == 'debit_amount')
	  {
	    $saldoD += $value;
	  }
	else
	  {
	    $saldoC += $value;
	  }
      }
    if ($objp->cta_normal == 1)
      {
	$saldo = $saldoD - $saldoC;
      }
    else
      {
	$saldo = $saldoC - $saldoD;
      }
    
    if (price2num($saldo) <> 0)
      $prodser[$objp->ref] = array('cta_name'=>$objp->cta_name,
				   'id' => $objp->id,
				   'ref' => $objp->ref,
				   'saldo'=>price2num($saldo));
  }
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("Accountbalances").'</td></tr>';
foreach ((array) $prodser AS $i => $data)
{
  
	$statProducts.= "<tr $bc[0]>";
	$statProducts.= '<td><a href="'.DOL_URL_ROOT.'/contab/accounts/fiche.php?id='.$data['id'].'">'.$langs->trans("Accounts").' '.$data['ref'].' '.$data['cta_name'].'</a></td><td align="right">'.price($data['saldo']).'</td>';
	$statProducts.= "</tr>";
	$total += $data['saldo'];
}
print $statProducts;
print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td align="right">';
print price($total);
print '</td></tr>';
print '</table>';

//estadisticas fin


//print '</td><td valign="top" width="70%" class="notopnoleftnoright">';
print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';


/*
 * Last modified products
 */
$max=15;
$sql = "SELECT p.rowid, p.cta_name, p.cta_top, p.ref, p.cta_normal, ";
$sql.= " p.tms ";
$sql.= " FROM ".MAIN_DB_PREFIX."contab_accounting as p";
$sql.= " WHERE p.entity IN (".getEntity($product_static->element, 1).")";
$sql.= $db->order("p.tms","DESC");
$sql.= $db->plimit($max,0);

//print $sql;
$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);

	$i = 0;

	if ($num > 0)
	{
		$transRecordedType = $langs->trans("LastModifiedAccounting",$max);

		print '<table class="noborder" width="100%">';

		$colnb=5;
		if (empty($conf->global->PRODUIT_MULTIPRICES)) $colnb++;

		print '<tr class="liste_titre"><td colspan="'.$colnb.'">'.$transRecordedType.'</td></tr>';

		$var=True;

		while ($i < $num)
		{
			$objp = $db->fetch_object($result);

			// //Multilangs
			// if (! empty($conf->global->MAIN_MULTILANGS))
			// {
			// 	$sql = "SELECT label";
			// 	$sql.= " FROM ".MAIN_DB_PREFIX."product_lang";
			// 	$sql.= " WHERE fk_product=".$objp->rowid;
			// 	$sql.= " AND lang='". $langs->getDefaultLang() ."'";

			// 	$resultd = $db->query($sql);
			// 	if ($resultd)
			// 	{
			// 		$objtp = $db->fetch_object($resultd);
			// 		if ($objtp && $objtp->label != '') $objp->label = $objtp->label;
			// 	}
			// }

			$var=!$var;

			print "<tr ".$bc[$var].">";
			print '<td class="nowrap">';
			$product_static->id=$objp->rowid;
			$product_static->ref=$objp->ref;
			$product_static->cta_top=$objp->cta_top;

			$accounting->fetch($product_static->cta_top);
			if ($accounting->id == $product_static->cta_top)
			  $product_static->top=$accounting->cta_name;
			else
			  $product_static->top= '';
			//print $product_static->getNomUrl(1,'',16);
			print $product_static->ref;
			print "</td>\n";
			print '<td>'.dol_trunc($objp->cta_name,32).'</td>';
			print '<td>'.dol_trunc($product_static->top,32).'</td>';
			print "<td>";
			print dol_print_date($db->jdate($objp->tms),'day');
			print "</td>";
	    // 		// Sell price
	    // 		if (empty($conf->global->PRODUIT_MULTIPRICES))
	    // 		{
	    // 			print '<td align="right">';
    	    // 		if ($objp->price_base_type == 'TTC') print price($objp->price_ttc).' '.$langs->trans("TTC");
    	    // 		else print price($objp->price).' '.$langs->trans("HT");
    	    // 		print '</td>';
	    // 		}
	    // 		print '<td align="right" class="nowrap">';
	    // 		print $product_static->LibStatut($objp->tosell,5,0);
	    // 		print "</td>";
            // print '<td align="right" class="nowrap">';
            // print $product_static->LibStatut($objp->tobuy,5,1);
            // print "</td>";
			print "</tr>\n";
			$i++;
		}

		$db->free();

		print "</table>";
	}
}
else
{
	dol_print_error($db);
}

//print '</td></tr></table>';
print '</div></div></div>';

llxFooter();

$db->close();
?>
