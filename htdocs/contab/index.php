<?php

require '../main.inc.php';
//require_once DOL_DOCUMENT_ROOT.'/contab/class/contabaccountingext.class.php';


require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatdetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabperiodoext.class.php';
require_once(DOL_DOCUMENT_ROOT."/contab/class/accountingaccountext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/accountingaccountadd.class.php");

require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");


$type=isset($_GET["type"])?$_GET["type"]:(isset($_POST["type"])?$_POST["type"]:'');

// Security check
//$result=restrictedArea($user,'contab');

$langs->load("contab");
$action = GETPOST('action');


//$product_static = new Contabaccountingext($db);
//$accounting     = new Contabaccountingext($db);
$period = new Contabperiodo($db);

$objAccountingAccount    = new Accountingaccountext($db);
$objAccountingAccountAdd = new Accountingaccountadd($db);
$objContabSeat           = new Contabseatext       ($db);
$objContabSeatDet        = new Contabseatdetext    ($db);

if (isset($_POST['year'])) $_SESSION['period_year'] = $_POST['year'];


/*
 * View
 */

$transAreaType = $langs->trans("ContabArea");
$helpurl='';
$helpurl='EN:Module_Contab|FR:Module_Contab|ES:M&oacute;dulo_Contabilidad';


llxHeader("",$langs->trans("Contab"),$helpurl);

print_fiche_titre($transAreaType);


if (!isset($_SESSION['period_year']) || $action == 'modify' || $action == 'create')
{
	$options = '';
	$filterstatic = '';
	$period->fetchAll('ASC', 'period_year', 0, 0, array('statut'=>1), 'AND');
	$aYear = array();
	$yearmin = date('Y')-1;
	$yearmax = date('Y')+1;
	foreach ((array) $period->lines AS $j => $line)
	{
		if ($yearmin > $line->period_year) $yearmin = $line->period_year;
		if ($yearmax < $line->period_year) $yearmax = $line->period_year;
		$aYear[$line->period_year] = $line->period_year;
	}
	//agregamos limites menor y mayor
	$aYear[$yearmin-1] = $yearmin-1;
	$aYear[$yearmax+1] = $yearmax+1;

	ksort($aYear);

	if (empty($aYear)) $aYear[date('Y')] = date('Y');
	if (empty($_SESSION['period_year'])) $_SESSION['period_year'] = date('Y');
	$yearmin--;
	$yearmax++;
	for ($year = $yearmin; $year <= $yearmax; $year++)
	{
		$selected = '';
		if ($_SESSION['period_year'] == $year) $selected = 'selected';
		$options.= '<option value="'.$year.'" '.$selected.'>'.$year.'</option>';

	}
	//foreach ((array) $aYear AS $year)
	//{
	//	$selected = '';
	//	if ($_SESSION['period_year'] == $year) $selected = 'selected';
	//	$options.= '<option value="'.$year.'" '.$selected.'>'.$year.'</option>';
	//}

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Select').' '.$langs->trans("Year").'</td><td>';
	print '<select name="year">'.$options.'</select>';
	print '</td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';

}
else
{
	$period_year = $_SESSION['period_year'];

	print '<div><h2>'.$langs->trans('Selectedyear').': '.$period_year.'</h2></div>';
	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=modify">'.$langs->trans("Changeyear").'</a></div>'."\n";

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
$period_year = $_SESSION['period_year'];
$date_ini = dol_get_first_day($period_year,1,$gm=false);
$date_fin = dol_get_last_day($period_year,12,$gm=false);
$objAccounting = new Accountingaccountext($db);
$filter = " AND t.entity = ".$conf->entity;
$res = $objAccounting->fetchAll('ASC','t.account_number',20,0,array(1=>1),$filter);

if ($res >0)
{
	foreach ($objAccounting->lines AS $j => $objp)
	{
		//buscando el saldo de la cuenta
		$objcontabdet = new Contabseatdetext($db);
		$resdet = $objcontabdet->fetch_list_account($objp->account_number,$date_ini,$date_fin);
		$aArray = $objcontabdet->aArray;
		$aArrayDet = $objcontabdet->aArrayDet;
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
			$prodser[$objp->account_number] = array('cta_name'=>$objp->label,
				'id' => $objp->id,
				'ref' => $objp->account_number,
				'saldo'=>price2num($saldo));
	}
}
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("Accountbalances").'</td></tr>';
foreach ((array) $prodser AS $i => $data)
{
	$objAccounting->id = $data['id'];
	$objAccounting->account_number = $data['ref'];
	$objAccounting->label = $data['cta_name'];

	$statProducts.= "<tr $bc[0]>";
	$statProducts.= '<td>';
	$statProducts.= $objAccounting->getNomUrl(1).' '.$data['cta_name'];
	$statProducts.= '</td><td align="right">'.price(price2num($data['saldo'],'MT')).'</td>';
	$statProducts.= "</tr>";
	$total += $data['saldo'];
}
print $statProducts;
print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td align="right">';
print price(price2num($total.'MT'));
print '</td></tr>';
print '</table>';

//estadisticas fin


//print '</td><td valign="top" width="70%" class="notopnoleftnoright">';
print '</div>';

/*****************************************************************************/
/*                          Vista de la derecha                              */
/*****************************************************************************/
print '<div class="fichetwothirdright"><div class="ficheaddleft">';

//$MasterAccount = $conf->global->CONSOLIDATION_MASTER_CHART;

$filterstatic = " AND ta.level = 1";
$ress = $objAccountingAccount->fetchAll('','',0,0,array(),'AND', $filterstatic,false,1);


if (! $ress){
	dol_print_error($db);print load_fiche_titre($langs->trans("EL PLAN DE CUENTAS MAESTRO NO SE ENCUENTRA"));exit;
}else{

	$num = count($objAccountingaccount->lines);
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("Nro cuentas").'</td>';
	print '<td colspan="2">'.$langs->trans("Label").'</td>';
	print '<td colspan="2">'.$langs->trans("Total").'</td>';
	print '</tr>';

	$lines = $objAccountingAccount->lines;
	foreach ($lines as $i => $obj)
	{

		$var =!$var;
		print "<tr ".$bc[$var].">";
		print "<td>".$obj->account_number."</td>";
		$aPrueba = get_son($obj->account_number, $obj->fk_pcg_version);
		$cta_normal = $obj->cta_normal;
		print "<td>".$obj->label."</td>";

		$sumaTotal     = 0;
		$cuentaNormal  = 0;


		foreach ($aPrueba as $cuenta => $value)
		{
			$sumaDebito    = 0;
			$sumaCredito   = 0;


				/*****************************************/
				$qur  = "SELECT t.fk_contab_seat, t.debit_account, t.credit_account, t.amount ";
				$qur .= " FROM llx_contab_seat_det as t , llx_contab_seat as s";
				$qur .= " WHERE t.fk_contab_seat=s.rowid AND (t.debit_account ='".$cuenta."' OR t.credit_account = '".$cuenta."') AND s.seat_year =".$period_year. " AND s.entity = ".$conf->entity;


				$rest = $db->query($qur);
				//Fin de la Consulta


				if (! $rest){
					dol_print_error($db);print load_fiche_titre($langs->trans("LAS CUENTAS MAESTRO NO SE ENCUENTRA"));exit;
				}else{
					//Aqui vamos a recorrer todo lo del la cuenta maestra cuenta por cuenta
					$nume = $db->num_rows($rest);
					$i=0;
					while($i < $nume)
					{
						$obje = $db->fetch_object($rest);
						if(!is_null($obje->debit_account) && !empty($obje->debit_account)){
							$sumaDebito+=$obje->amount;
						}
						if(!is_null($obje->credit_account) && !empty($obje->credit_account)){
							$sumaCredito+=$obje->amount;
						}
						$i++;
					}

					if($cta_normal == 1){
						$sumaTotal+=($sumaDebito-$sumaCredito);
					}

					if($cta_normal == 2){
						$sumaTotal+=($sumaCredito-$sumaDebito);
					}
				}
				/******************************************/


			//}
		}

		print "<td colspan ='2' align='right'>".price(price2num($sumaTotal,'MT'))."</td>";

		print "</tr>";
		$n++;
		//recorremos cuenta por cuenta y verificamos si exite relacion con todas las empresas
	}
	/*print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td align="right">';
	print '</td></tr>';*/
	print '</table>';
}


print '</div></div></div>';
}

function get_son($account_number,$MasterAccount, array $aSon=array())
{
	global $langs,$conf,$objAccountingAccount;
	$aSon = $aSon;

	$filter = " AND t.account_parent = '".$account_number."'";
	$filter.= " AND t.fk_pcg_version = '".$MasterAccount."'";
	$res = $objAccountingAccount->fetchAll('','',0,0,array(1=>1),'AND',$filter);

	if ($res>0)
	{
		$lines = $objAccountingAccount->lines;
		foreach ($lines AS $j => $line)
		{
			//$aSon[$line->account_parent][$line->account_number]=$line->id;
			//$aSon = get_son($line->account_number,$MasterAccount,$aSon);
			$aSon[$line->account_number]=$line->id;
			$aSon = get_son($line->account_number,$MasterAccount,$aSon);
		}
	}
	return $aSon;
}

llxFooter();

$db->close();
?>
