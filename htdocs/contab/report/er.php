<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/contab/report/er.php
 *      \ingroup    Contab balance de comprobacion
 *      \brief      Page liste des balance comprobacion
*/

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

require_once(DOL_DOCUMENT_ROOT."/contab/class/contabaccountingext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabseatdetext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabseatext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabvisionext.class.php");

require_once(DOL_DOCUMENT_ROOT."/contab/class/accountingaccountext.class.php");

require_once(DOL_DOCUMENT_ROOT."/contab/lib/contab.lib.php");
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php');
include_once(DOL_DOCUMENT_ROOT.'/fiscal/class/entity.class.php');

//excel para una versión anterior
$file = DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
$ver = 0;
if (file_exists($file))
{
	$ver = 1;
	require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
}
$file = DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';
if (file_exists($file))
	include_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';

//excel para version 4 o sup
$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
if (file_exists($file))
{
	$ver = 2;
	require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
}
$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
if (file_exists($file))
	include_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

$langs->load("contab");

// if (!$user->rights->contab->report->leer)
//   accessforbidden();
$year_current = strftime("%Y",dol_now());
$pastmonth = strftime("%m",dol_now());
$pastmonthyear = $year_current;
if ($pastmonth == 0)
{
	$pastmonth = 12;
	$pastmonthyear--;
}
$year = GETPOST('date_endyear');
$rep = GETPOST('rep');
$printledger  = GETPOST('printledger');
$printaccount = GETPOST('printaccount');
$closingseat = GETPOST('closingseat');

if (empty($year)) $year = $year_current;
$date_ini  = dol_mktime(0, 0, 1, $conf->global->SOCIETE_FISCAL_MONTH_START,  1,  $year);
$date_end  = dol_mktime(23, 59, 59, GETPOST('date_endmonth'),  GETPOST('date_endday'),  GETPOST('date_endyear'));
$id     = GETPOST('id','int');
$action = GETPOST('action','alpha');

if (!isset($_SESSION['period_year'])) $_SESSION['period_year'] = date('Y');
$period_year = $_SESSION['period_year'];

if (empty($date_end))
{
	$date_end=dol_get_last_day($period_year,($period_year < $pastmonthyear?12:$pastmonth),false);
}

$page = $_GET["page"];
if (empty($page) || $page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$object = new Contabvisionext($db);
$objAccounting = new Accountingaccountext($db);
$objEntity = new Entity($db);

$form = new Form($db);
$formfile = new Formfile($db);

if ($id>0)
{
	$result = $object->fetch($id);
	if ($result > 0)
		$ref = $object->ref;
}
else
{
	if ($rep == 'bg' && $conf->global->CONTAB_CODE_VISION_BG)
	{
		$filter = " AND t.ref = '".$conf->global->CONTAB_CODE_VISION_BG."'";
		$filter.= " AND t.sequence = 1 AND t.line = '001'";
		$filter.= " AND t.entity = ".$conf->entity;
		$res = $object->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
		if ($res == 1) $id = $object->id;

	}
	if ($rep == 'er' && $conf->global->CONTAB_CODE_VISION_ER)
	{
		$filter = " AND t.ref = '".$conf->global->CONTAB_CODE_VISION_ER."'";
		$filter.= " AND t.sequence = 1 AND t.line = '001'";
		$filter.= " AND t.entity = ".$conf->entity;
		$res = $object->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
		if ($res == 1) $id = $object->id;
	}
}
if ($action == 'generate' && $id<=0)
{
	setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Vision")), null, 'errors');
	$action = '';
}

	// Remove file in doc form
if ($action == 'remove_file')
{
		require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

		$langs->load("other");
		$upload_dir = $conf->contab->dir_output;
		//. '/' . dol_sanitizeFileName($objectdoc->ref);

		$file = $upload_dir . '/' . GETPOST('file');
		$ret = dol_delete_file($file, 0, 0, 0, $product);
		if ($ret)
			setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else
			setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
		$action = '';
}
if ($action == 'excel')
{
	$aLineas = unserialize($_SESSION['aLineas']);
	$aDatos = unserialize($_SESSION['aDatos']);
	$nTitles = unserialize($_SESSION['column_vision'])+2;
	$aABCD = array (1=>"A",2=>"B",3=>"C",4=>"D",5=>"E",6=>"F",7=>"G",8=>"G",9=>"I",10=>"J",11=>"K",12=>"L",13=>"M",14=>"N",15=>"O",16=>"P",17=>"Q",18=>"R",19=>"S",20=>"T",21=>"U",22=>"V",23=>"W",24=>"X",25=>"Y",26=>"Z",27=>"AA",28=>"AB",29=>"AC",30=>"AD",31=>"AE",32=>"AF",33=>"AG",34=>"AH",35=>"AI",36=>"AJ",37=>"AK",38=>"AL",39=>"AM",40=>"AN",41=>"AO",42=>"AP",43=>"AQ",44=>"AR",45=>"AS",46=>"AT",47=>"AU",48=>"AV",49=>"AW",50=>"AX",51=>"AY",52=>"AZ",53=>"BA",54=>"BB",55=>"BC",56=>"BD",57=>"BE");

		//Manejo de estilos para las celdas
	$styleThickBrownBorderOutline = array(
		'borders' => array(
			'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THICK,
				'color' => array('argb' => 'FFA0A0A0'),
			),
		),
	);
		//PROCESO 1
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
		//armamos la cabecera
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A2')->getAlignment()->applyFromArray(
		array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
	);


		//titulos
	if($aDatos['printaccount'] == 1){
		//$objPHPExcel->getActiveSheet()->SetCellValue('A7',$langs->trans('acount'));
		$objPHPExcel->getActiveSheet()->SetCellValue('A7',$langs->trans('Accountingaccount'));
	}else{
		$objPHPExcel->getActiveSheet()->SetCellValue('A7',$langs->trans('Accountingaccount'));
	}


		//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C7:D7');

		//FORMATO
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);



		//cambiamos de fila
	$line = 8;
	$nI = 1;
	$nSw = 0;
	$sw = 0;
		//echo $aABCD[$nI].$line;exit;
	foreach ((array)$aLineas AS $j => $row)
	{
		$lTitle=true;
		foreach ($row as $key => $value)
		{
			if(is_array($row[$key]))
			{
				$nI = 1;
				$line++;
				foreach ($value as $k => $val) {
					$objPHPExcel->getActiveSheet()->SetCellValue($aABCD[$nI].$line,$val);
					$nI++;
				}
			}else{
				$aPart = explode("/",$value);
				if($aPart[1] == "L" && $sw == 0){
					$nSw = 1;
					$sw = 1;

				}else{
					$nSw = 0;
				}
				if($sw == 1)
				{
					if ($nI==2 && $lTitle)
					{
						//echo  '<hr>'.$nI.' '.$aPart[0].' '.$line;
						$objPHPExcel->getActiveSheet()->SetCellValue($aABCD[1].$line,$aPart[0]);
						$lTitle=false;
						$nI=1;
					}
					else
					{
						//echo  '<hr>'.$nI.' '.$aPart[0].' '.$line;
						if ($nI>=2)
						{
							if (!is_numeric($aPart[0])) $aPart[0] = '';
							if (!empty($aPart[0]))
							{
								$objPHPExcel->getActiveSheet()->SetCellValue($aABCD[$nI].$line,$aPart[0]);
								$objPHPExcel->getActiveSheet()->getStyle($aABCD[$nI].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
							}
							//else $objPHPExcel->getActiveSheet()->SetCellValue($aABCD[$nI].$line,'');
						}
						else
						{

							$objPHPExcel->getActiveSheet()->SetCellValue($aABCD[$nI].$line,$aPart[0]);
						}
					}
					$objPHPExcel->getActiveSheet()->getStyle($aABCD[$nI].$line)->getFont()->setBold(true);
				}else{
					if ($nI>=2)
					{
						if (!is_numeric($aPart[0])) $aPart[0] = '';
						if (!empty($aPart[0]))
						{
							$objPHPExcel->getActiveSheet()->SetCellValue($aABCD[$nI].$line,$aPart[0]);
							$objPHPExcel->getActiveSheet()->getStyle($aABCD[$nI].$line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
						}
							//else $objPHPExcel->getActiveSheet()->SetCellValue($aABCD[$nI].$line,'');
					}
					else
						$objPHPExcel->getActiveSheet()->SetCellValue($aABCD[$nI].$line,$aPart[0]);
				}
					//$objPHPExcel->getActiveSheet()->SetCellValue($aABCD[$nI].$line,$row[$key]);
				$nI++;
				$aPart = "";
			}

		}
		$sw = 0;
		$nI = 1;
		$line++;

	}


	//empresa
	$lineHead = 2;
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$lineHead,$langs->trans($aDatos['entity']));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$lineHead.':'.$aABCD[$nTitles].$lineHead);
	$lineHead++;
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$lineHead,$langs->trans($aDatos['title']));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$lineHead.':'.$aABCD[$nTitles].$lineHead);
	$lineHead++;
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$lineHead,$langs->trans('Tothe').': '.dol_print_date($aDatos['dateend'],'daytext'));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$lineHead.':'.$aABCD[$nTitles].$lineHead);
	$lineHead++;
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$lineHead,$langs->trans($aDatos['expressed']));
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$lineHead.':'.$aABCD[$nTitles].$lineHead);
	$objPHPExcel->getActiveSheet()->getStyle('A2:'.$aABCD[$nTitles].$lineHead)->applyFromArray(
		array(
			'font'    => array(
				'bold'      => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		)
	);

	$objPHPExcel->getActiveSheet()->getStyle('A7:'.$aABCD[$nTitles].'7')->applyFromArray(
		array(
			'font'    => array(
				'bold'      => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'top'     => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'fill' => array(
				'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				'rotation'   => 90,
				'startcolor' => array(
					'argb' => 'FFA0A0A0'
				),
				'endcolor'   => array(
					'argb' => 'FFFFFFFF'
				)
			)
		)
	);

	if($aDatos['printaccount'] == 1){
		for ($q=2; $q <= $nTitles ; $q++) {
			if ($q == $nTitles)
				$objPHPExcel->getActiveSheet()->SetCellValue($aABCD[$q].'7',$langs->trans('Total'));
			else
				$objPHPExcel->getActiveSheet()->SetCellValue($aABCD[$q].'7',$langs->trans('Parciales'));
			$objPHPExcel->getActiveSheet()->getColumnDimension($aABCD[$q])->setAutoSize(true);
			$nTi--;
		}
	}else{
		for ($qq=2; $qq <= $nTitles ; $qq++) {
			if ($qq == $nTitles)
				$objPHPExcel->getActiveSheet()->SetCellValue($aABCD[$qq].'7',$langs->trans('Total'));
			else
				$objPHPExcel->getActiveSheet()->SetCellValue($aABCD[$qq].'7',$langs->trans('Parcial'));
			$objPHPExcel->getActiveSheet()->getColumnDimension($aABCD[$qq])->setAutoSize(true);
			$nTii--;
		}
	}
	for ($q=2; $q <= $nTitles ; $q++)
		$objPHPExcel->getActiveSheet()->getStyle($aABCD[$q])->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("excel/repEESS.xlsx");

	header('Location: '.DOL_URL_ROOT.'/contab/report/fiche_export.php?archive=repEESS.xlsx');
}

$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementaccounting"),$help_url);

print_barre_liste(($rep=='er'?$langs->trans("Incomestatement"):$langs->trans("Generalbalancesheet")), $page, "bc.php", "", $sortfield, $sortorder,'',$num);

print "<form action=\"er.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="generate">';
print '<input type="hidden" name="rep" value="'.$rep.'">';
dol_htmloutput_mesg($mesg);
print '<table class="border" width="100%">';

// date seat
print '<tr><td width="20%" class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
$form->select_date($date_end,'date_end','','','',"crea_er",1,1);
print '</td></tr>';

// vision
print '<tr><td class="fieldrequired">'.$langs->trans('Vision').'</td><td colspan="2">';
print $object->select_vision($id,'id','',0,1);
print '</td></tr>';

// imprimir cuentas vision
print '<tr><td class="fieldrequired">'.$langs->trans('Printaccountsvision').'</td><td colspan="2">';
print $form->selectyesno('printaccount',(GETPOST('printaccount')?GETPOST('printaccount'):1),1);
print '</td></tr>';

// imprimir detalle cuentas
print '<tr><td class="fieldrequired">'.$langs->trans('Printledgeraccounts').'</td><td colspan="2">';
print $form->selectyesno('printledger',(GETPOST('printledger')?GETPOST('printledger'):1),1);
print '</td></tr>';

// si esta definidio code cierre
if ($conf->global->CONTAB_CODE_TRANSACTION_CLOSE_SEAT)
{
	print '<tr><td class="fieldrequired">'.$langs->trans('Includeclosingseat').'</td><td colspan="2">';
	print $form->selectyesno('closingseat',(GETPOST('closingseat')?GETPOST('closingseat'):0),1);
	print '</td></tr>';
}

print '</table>';
print '<center><br><input type="submit" class="button" value="'.$langs->trans("Generate").'"></center>';

print '</form>';

if ($action == 'generate')
{
	$column = 0;

	//proceso de consulta
	$closing_code = '';

	if (!$closingseat)
		$closing_code = $conf->global->CONTAB_CODE_TRANSACTION_CLOSE_SEAT;
	$filter = " AND t.entity = ".$conf->entity;
	$filter.= " AND t.ref = '".$ref."' ";
	$filter.= " AND t.rowid != ".$id;
	$res = $object->fetchAll('ASC','t.ref,t.sequence,t.line',0,0,array(1=>1),'AND',$filter);
	if ($res)
	{
		//preparamos los arrays
		$aArraycta = array();
		$aArrayOrd = array();
		$num = $res;
		$lines = $object->lines;
		$i = 0;
		$objectseatdet = new Contabseatdetext($db);
		$var=True;
		foreach ($lines AS $j => $objp)
		{
			if ($column <= $objp->cta_column) $column = $objp->cta_column;
			$aArrayOrd[$objp->sequence] = $objp->account;
			$aArrayCta[$objp->account]['label']       = $objp->detail_managment;
			$aArrayCta[$objp->account]['class']       = $objp->cta_class;
			$aArrayCta[$objp->account]['normal']      = $objp->cta_normal;
			$aArrayCta[$objp->account]['balance']     = $objp->cta_balances;
			if ($objp->line == '001')
				$aArrayCta[$objp->account]['column']      = $objp->cta_column;
			$aArrayCta[$objp->account]['operation']   = $objp->cta_operation;
			$aArrayCta[$objp->account]['identifier']  = $objp->cta_identifier;
			$aArrayCta[$objp->account]['account_sup'] = $objp->account_sup;
			if ($objp->cta_class == 2)
			{
					//buscar valores de las cuentas que afecta
				$accountini = '';
				$accountfin = '';
				$fk_accountini = $objp->fk_accountini;
				$fk_accountfin = $objp->fk_accountfin;
				if ($fk_accountini>0)
				{
					$resc = $objAccounting->fetch($fk_accountini);
					if ($resc > 0) $accountini = $objAccounting->account_number;
				}
				if ($fk_accountfin>0)
				{
					$resc = $objAccounting->fetch($fk_accountfin);
					if ($resc > 0) $accountfin = $objAccounting->account_number;
				}
					//buscamos el array del rango de cuentas
				$resacc = $objAccounting->list_account($accountini,$accountfin);
				if ($resacc>0)
				{
					$aListAccount = $objAccounting->aArray;
					//recorremos las cuentas para sumar
					foreach ((array) $aListAccount AS $account => $cta_normal)
					{
						//list($aArr,$aArrDet)

						$ressd = $objectseatdet->fetch_list_account($account,$date_ini,$date_end,$closing_code);
						if ($ressd>0)
						{
							$aArr = $objectseatdet->aArray;
							$aArrDet = $objectseatdet->aArrayDet;
							$aArrayCta[$objp->account]['debit_amount']  += $aArr['debit_amount'];
							$aArrayCta[$objp->account]['credit_amount'] += $aArr['credit_amount'];
							$aArrayCta[$objp->account]['accountsheet'][$account]['debit_amount'] += $aArr['debit_amount'];
							$aArrayCta[$objp->account]['accountsheet'][$account]['credit_amount'] += $aArr['credit_amount'];
							//account sup
							$aArrayCta[$objp->account_sup+0]['debit_amount'] += $aArr['debit_amount'];
							$aArrayCta[$objp->account_sup+0]['credit_amount'] += $aArr['credit_amount'];
							if ($cta_normal == 1)
							{
								//deudor
								$aArrayCta[$objp->account]['valor']+=price2num($aArr['debit_amount']-$aArr['credit_amount'],'MT');
								$aArrayCta[$objp->account]['accountcont'][$account]+=price2num($aArr['debit_amount']-$aArr['credit_amount'],'MT');
								//sumando para la cuenta superior
								$aArrayCta[$objp->account_sup+0]['valor']+=$aArr['debit_amount']-$aArr['credit_amount'];
							}
							else
							{
								$aArrayCta[$objp->account]['valor']+=price2num($aArr['credit_amount']-$aArr['debit_amount'],'MT');
								$aArrayCta[$objp->account]['accountcont'][$account]+=price2num($aArr['credit_amount']-$aArr['debit_amount'],'MT');
								//sumando para la cuenta superior
								$aArrayCta[$objp->account_sup+0]['valor']+=$aArr['credit_amount']-$aArr['debit_amount'];
							}
						}
					}
				}
			}
			else
			{
				$aArrayCta[($objp->account_sup?$objp->account_sup:0)]['debit_amount']+=$aArrayCta[$objp->account]['debit_amount'];
				$aArrayCta[($objp->account_sup?$objp->account_sup:0)]['credit_amount']+=$aArrayCta[$objp->account]['credit_amount'];

			}
			$i++;

		}

		ksort($aArrayOrd);
		//imprimiendo el resultado

		$a=1;
		$aLineas = array();

		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
		//if ($printaccount == 1)
			//print_liste_field_titre($langs->trans("Account"),"", "","","","");
		print_liste_field_titre($langs->trans("Name"),"", "","","","");
		for ($i = $column; $i >=0 ; $i--)
		{
			print_liste_field_titre("","", "","","",'align="right"');
		}
		print "</tr>\n";

		foreach((array) $aArrayOrd AS $ord => $account)
		{
			$lSumaTotal = False;
			$lSinValor  = False;
			$aData = $aArrayCta[$account];
			$cBold ="";
			$var=!$var;
			if ($aData['class'] == 1)
			{
				$lPrinttr = true;
				$aIdentifier = explode('|',$aData['identifier']);
				foreach((array) $aIdentifier AS $i => $value)
				{
					if ($value == 1){
						$lPrinttr = false;
						print '<tr '.$bc[$var].' style="font-weight:bold;">';
						$cBold = "/L";
						$aLineas[$a][]=$cBold;
					}
					if ($value == 2) $lSumaTotal = True;
					if ($value == 3) $lSinValor = True;
				}
				if ($lPrinttr)
					print '<tr '.$bc[$var].'>';
			}
			else
			{
				$lPrinttr = true;
				$aIdentifier = explode('|',$aData['identifier']);
				foreach((array) $aIdentifier AS $i => $value)
				{
					if ($value == 1){
						$lPrinttr=false;
						print '<tr '.$bc[$var].' style="font-weight:bold;">';
						$cBold = "/L";
						$aLineas[$a][]=$cBold;
					}
					//if ($value == 2) $lSumaTotal = True;
					if ($value == 3) $lSinValor = True;
				}
				if ($lPrinttr)
					print '<tr '.$bc[$var].'>';
			}
			//buscamos la cuenta
			//if ($printaccount == 1){
				//print '<td>'.$account.'</td>';
				//$aLineas[$a][]=$account.$cBold;//Liet
				//$aLineas[$a][]=$account;
				//echo "</br>Negrillas";
			//}

			print '<td>'.($printaccount==1?$account.' ':'').$aData['label'].'</td>';
			$aLineas[$a][]=($printaccount==1?$account.' ':'').$aData['label'];
			if ($aData['normal'] == 1)
			{
				$sumaFila = $aData['debit_amount']-$aData['credit_amount'];
			}
			else
			{
				$sumaFila = $aData['credit_amount']-$aData['debit_amount'];
			}
			if ($aData['class'] == 1)
			{
				if ($lSumaTotal)
				{
					for ($j = $column; $j>=0; $j--)
					{
						if ($j == $aData['column']){
							print '<td align="right">'.price(price2num($sumaFila,'MT')).'</td>';
							//print '<td align="right">'.$sumaFila.'</td>';
							$aLineas[$a][] = price2num($sumaFila,'MT');
						}
						else{
							$aLineas[$a][]="";
							print '<td>&nbsp;</td>';
						}
					}
				}
				else
					for ($j = $column; $j>=0; $j--)
					{
						print '<td>&nbsp;</td>';
						$aLineas[$a][]="";
					}
				}
				else
				{
					for ($j = $column; $j>=0; $j--)
					{
						if ($j == $aData['column'])
						{
							if (!$lSinValor)
							{
								print '<td align="right">'.price(price2num($sumaFila,'MT')).'</td>';
								$aLineas[$a][]=price(price2num($sumaFila.'MT'));
							}
							else
							{
								print '<td align="right">'.'</td>';
								$aLineas[$a][]='';
							}

						}
						else{
							print '<td>&nbsp;</td>';
							$aLineas[$a][]='';
						}
					}
				}

				print "</tr>\n";
				if ($printledger == 1)
				{
					//impresion de cuentas contables
					$b = 1;
					$aCuentas = array();
					foreach ((array) $aData['accountsheet'] AS $accountsheet => $aRow)
					{
						if ($aData['normal'] == 1)
						{
							$sumaFila = $aRow['debit_amount']-$aRow['credit_amount'];
						}
						else
						{
							$sumaFila = $aRow['credit_amount']-$aRow['debit_amount'];
						}
						if ($sumaFila <> 0)
						{
							$resaccount = $objAccounting->fetch('',$accountsheet,1);
							$accountname = '';
							if ($resaccount>0)
								$accountname = $objAccounting->label;
							print "<tr $bc[$var]>";
							if ($printaccount == 1)
							{
								//print '<td>'.$accountsheet.'</td>';
								//$aCuentas[] = $accountsheet;
								print '<td>'.($accountsheet?$accountsheet.' ':'').$accountname.'</td>';
								$aCuentas[] = ($accountsheet?$accountsheet.' ':'').$accountname;
							}
							else
							{
								print '<td>'.$accountsheet.' '.$accountname.'</td>';
								$aCuentas[]=$accountsheet." ".$accountname;
							}
							print '<td align="right">'.price(price2num($sumaFila,'MT')).'</td>';
							$aCuentas[] = price2num($sumaFila,'MT');
							//$aCuentas[] = price(price2num($sumaFila,'MT'));
							//$aCuentas = $sumaFila;
							print '</tr>';
						}
						$b++;
						$aLineas [$a][] = $aCuentas;
						unset($aCuentas);
						$sumaFila = 0;
					}
				}
				$a++;
			}
			//$db->free($result);
			print "</table>";
		}
		else
		{
			dol_print_error($db);
		}


		$nI = 1;
		$ni = 1;
		$aIndices =array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19,20=>20);
		$aReport = array();
		foreach ((array)$aLineas AS $j => $row)
		{
			$ni = 1;
			foreach ($row as $key => $value)
			{
				$nTitles = count($row);
				if(is_array($row[$key]))
				{
					$nI++;
					$ni = 1;
					foreach ($value as $k => $val)
					{
						if($value != "/L" && $ni == 1){
							$aReport[$nI][$ni]="";
							$ni++;
						}
						$aReport[$nI][$ni]=$val;
						$ni++;
					}
				}else{
					if($value != "/L" && $ni == 1){
						$aReport[$nI][$aIndices[$ni]] = " ";
						$ni++;
					}
					$aReport[$nI][$aIndices[$ni]] = $value;
					$ni++;
				}
			}
			$nI++;
		}



		$nN = count($aReport[1])-1;
		//Liet
		if($printaccount == 1){
			$aDimension = array(0=>11,1=>135);
			$aHeader = array(1=>$langs->trans('Account'),2=>$langs->trans('Name'));
			$nR = 134/($nN-1);
			$nH = $nN - 1;
			for ($c=2; $c <= $nN ; $c++) {
				$nNn = $aDimension[$c-1] + $nR;
				$aDimension[$c]=$nNn;
				$aHeader[$c] = $nH-1;
				$nH--;
			}
		}else{
			$aDimension = array(0=>11,1=>135);
			$aHeader = array(1=>"name");
			$nR = 134/($nN-1);
			$nH = $nN - 1;
			for ($c=2; $c <= $nN ; $c++) {
				$nNn = $aDimension[$c-1] + $nR;
				$aDimension[$c]=$nNn;
				$aHeader[$c] = $nH-1;
				$nH--;
			}
		}

		$aDimension[$nN+1]=269;

		print '<div class="tabsAction">'."\n";

		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Hoja Electronica").'</a>';

		$parameters=array();
		$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
		// Note that $action and $object may have been modified by hook
		//if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
		//print "<div class=\"tabsAction\">\n";
		$result = $object->fetch($id);
		if ($result > 0){
			$label = $object->name_vision;
		}else{
			$label = "No exist Title";
		}
		$objEntity->fetch($conf->entity);
		$aDatos = array("title"=>$label,'ref'=>$object->ref,"dateend"=>$date_end,"printaccount"=>$printaccount,'entity'=>$objEntity->label,'expressed'=>'('.$langs->trans('Expressedinbolivianos').')');
		$_SESSION['aDatos'] = serialize($aDatos);
		$_SESSION['aLineas'] = serialize($aLineas);
		$_SESSION['aReport'] = serialize($aReport);
		$_SESSION['aDimension'] = serialize($aDimension);
		$_SESSION['aHeader'] = serialize($aHeader);
		$_SESSION['column_vision'] = serialize($column);


		$modelpdf = "eeff";
		$outputlangs = $langs;
		$newlang = '';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
		if (! empty($newlang)) {
			$outputlangs = new Translate("", $conf);
			$outputlangs->setDefaultLang($newlang);
		}
			//$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		$result=$objAccounting->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		if ($result < 0) dol_print_error($db,$result);
	}


	$filename='contab/'.$period_year.'/eeff';
	$filedir=$conf->contab->dir_output.'/contab/'.$period_year.'/eeff';

	$urlsource=$_SERVER['PHP_SELF'];
	$genallowed=$user->rights->contab->ef->read;
	$genallowed = false;
	$delallowed=$user->rights->contab->ef->del;
	print $formfile->showdocuments('contab',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);

	$somethingshown=$formfile->numoffiles;

	print '</td></tr></table>';
		//print '</div>';

	print '</div>'."\n";

	llxFooter();
	$db->close();
	?>