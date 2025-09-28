<?php
/* Copyright (C) 2018-2018
 *
 * Importar los datos de un excel al modulo de budget
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';

//require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

dol_include_once('/budget/class/productext.class.php');
dol_include_once('/budget/class/productasset.class.php');
dol_include_once('/productext/class/productadd.class.php');
dol_include_once('/budget/class/cunits.class.php');
dol_include_once('/budget/class/ctypeitemext.class.php');
dol_include_once('/budget/class/itemsext.class.php');
dol_include_once('/budget/class/itemsgroupext.class.php');
dol_include_once('/budget/class/itemsregion.class.php');
dol_include_once('/orgman/class/cregiongeographic.class.php');
dol_include_once('/orgman/class/cclasfin.class.php');

dol_include_once('/budget/class/itemsproduct.class.php');
dol_include_once('/budget/class/itemsproductregion.class.php');

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


$langs->load("budget");
$langs->load("product");

$action=GETPOST('action');

$id         = GETPOST("rowid");
$rid        = GETPOST("rid");
$fk_period  = GETPOST("fk_period");
$hoja = GETPOST('hoja','int');
$type  = GETPOST("type");
$fk_categorie = GETPOST('fk_categorie');
$fk_region = GETPOST('fk_region','int');
$fk_sector = GETPOST('fk_sector','int');
$selrow = GETPOST('selrow');
$cancel = GETPOST('cancel');
$memberkey = GETPOST('memberkey','int');
$mesg = '';
if (!isset($_SESSION['period_year'])) $_SESSION['period_year']= date('Y');
$period_year = $_SESSION['period_year'];
if (GETPOST('period_year')) $period_year = GETPOST('period_year');

$dateimport  = dol_mktime(0,0,0,GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

$aDateimport = dol_getdate($dateimport);
$nDecimal = ($conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL?$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL:8);

//Declaramos los objectos que se manejaran
$objUser  = new User($db);
$objProduct = new Productext($db);
$objProductadd = new Productadd($db);
$objCunits = new Cunits($db);
$objCtypeitem = new Ctypeitemext($db);
$objCategorie = new Categorie($db);
$objItems = new Itemsext($db);
$objItemsgroup = new Itemsgroupext($db);
$objTmp = new Itemsext($db);
$objCregiongeographic = new Cregiongeographic($db);
$objCclasfin = new Cclasfin($db);
$objItemsproduct = new Itemsproduct($db);
$objItemsproductregion = new Itemsproductregion($db);
$objProductasset = new Productasset($db);
$objItemsregion = new Itemsregion($db);

$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');

$aDate = dol_getdate(dol_now());

if (!$user->rights->budget->ite->upload) accessforbidden();

//varaibles definidas
$aParamlabel=array('MA'=>'MATERIALES','MO'=>'MANO DE OBRA','MQ'=>'HERRAMIENTAS Y EQUIPO','AC'=>'GASTOS GENERALES',);
$aParam=array('MA'=>'1.- MATERIALES','MO'=>'2.- MANO DE OBRA','MQ'=>'3.- HERRAMIENTAS Y EQUIPO','AC'=>'4.- GASTOS GENERALES',);
if ($action == 'edit')
{
	//armamos los valores
	foreach($aParamlabel AS $code => $value)
	{
		$aParam[$code] = GETPOST($code);
	}
}
foreach ($aParam AS $j => $value)
	$aParamrev[$value] = $j;

$aTablefield=array('Detalle'=>'label','cantidad'=>'performance','porc_productive'=>'price_productive','price_improductive'=>'amount_noprod','price_productive'=>'amount','cost_direct'=>'cost_direct','ref'=>'ref','fk_item'=>'fk_item','group_structure'=>'group_structure','fk_unit'=>'fk_unit','fk_product'=>'fk_product','unidad'=>'unidad', 'fk_item_product'=>'fk_item_product');

$exchange_rate = $conf->global->ITEMS_DEFAULT_EXCHANGE_RATE;
$lCurrency = false;
if ($conf->global->ITEMS_DEFAULT_BASE_CURRENCY != $conf->currency) $lCurrency=true;

//params docum
/*
 1 = Id
 2 = Login
 3 = Docum
*/

 $aMonth = array(2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11,13=>12);
 $aHeader = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z',27=>'AA',28=>'AB',29=>'AC',30=>'AD',31=>'AE',32=>'AF',33=>'AG',34=>'AH',35=>'AI',36=>'AJ',37=>'AK',38=>'AL',39=>'AM',40=>'AN',41=>'AO',42=>'AP',43=>'AQ',44=>'AR',45=>'AS',46=>'AT',47=>'AU',48=>'AV',49=>'AW',50=>'AX',51=>'AY',52=>'AZ');


/************************
 *       Actions        *
 ************************/
$now = dol_now();
// AddSave
if ($action == 'add' && GETPOST('save') == $langs->trans('Save') && $user->rights->budget->asset->upload)
{
	/*verificamos los tipos*/

	$error = 0;
	$aDatanew   = unserialize($_SESSION['aDatanew']);
	$aTitle   = unserialize($_SESSION['aTitle']);

	//$valorPrimero = $arrayAssistance[1][2];
	$indMarcacion = 1;
	$c=0;
	$swValorPrimero = 0;
	$valorPrimero = 0;
	$feAcceso = 0;

	$db->begin();
	foreach ($aDatanew AS $cItem => $datag)
	{
		foreach ($datag AS $cGroup => $aResource)
		{
			foreach ($aResource AS $resource => $data)
			{
				if (!$error)
				{
					$objItemsproduct->initAsSpecimen();
					$objItemsproductregion->initAsSpecimen();
					//vamos a crear un registro en itemsregion
					$objItemsregion->initAsSpecimen();
					$fk_item = $data['fk_item'];
					$resitem = $objItemsregion->fetch(0,$fk_item,$fk_region,$fk_sector);
					if ($resitem==0)
					{
						//creamos
						$objItemsregion->fk_item = $fk_item;
						$objItemsregion->fk_region = $fk_region;
						$objItemsregion->fk_sector = $fk_sector;
						$objItemsregion->hour_production = 0;
						$objItemsregion->amount_noprod = 0;
						$objItemsregion->amount = 0;
						$objItemsregion->active = 1;
						$objItemsregion->fk_user_create= $user->id;
						$objItemsregion->fk_user_mod= $user->id;
						$objItemsregion->datec= $now;
						$objItemsregion->datem= $now;
						$objItemsregion->tms= $now;
						$objItemsregion->status= 1;
						$resitem = $objItemsregion->create($user);
						if ($resitem<=0)
						{
							$error++;
							setEventMessages($objItemsregion->error,$objItemsregion->errors,'errors');
						}
					}
					$lAdd=true;
					if ($data['fk_item_product']>0)
					{
						//buscamos
						$result = $objItemsproduct->fetch($data['fk_item_product']);
						if ($result==1)
						{
							$lAdd=false;
							$resip = $objItemsproduct->id;
						}
					}
					$cost_pu_productive = 0;
					foreach ($aTitle AS $campo)
					{
						$namefield = $aTablefield[$campo];
						$objItemsproduct->$namefield = $data[$campo];
						$objItemsproductregion->$namefield = $data[$campo];
						if ($campo == 'fk_product')
						{
							//vamos a buscar la region
							$resprod = $objProduct->fetch($data[$campo]);
							if ($resprod)
							{
								$objItemsproductregion->fk_origin = $objProduct->country_id;
							}
							//vamos a buscar la formula
							$resass = $objProductasset->fetch(0,$data[$campo]);
							if ($resass==1)
							{
								$objItemsproduct->formula = $objProductasset->formula;
								if ($lCurrency)
									$cost_pu_productive = $objProductasset->cost_pu_productive / $exchange_rate;
								else
									$cost_pu_productive = $objProductasset->cost_pu_productive;
							}
						}
					}
					//campos genericos
					$objItemsproduct->label = $resource;
					$objItemsproduct->active= 1;
					$objItemsproduct->fk_user_create= $user->id;
					$objItemsproduct->fk_user_mod= $user->id;
					$objItemsproduct->datec= $now;
					$objItemsproduct->datem= $now;
					$objItemsproduct->tms= $now;
					$objItemsproduct->status= 1;
					if ($lAdd)
						$resip = $objItemsproduct->create($user);
					else
						$result = $objItemsproduct->update($user);
					if ($resip>0)
					{
						//vamos a buscar si existe
						$lAdd=true;
						$result = $objItemsproductregion->fetch(0,$resip,$fk_region,$fk_sector);
						if($result==1)
						{
							$lAdd=false;
							foreach ($aTitle AS $campo)
							{
								$namefield = $aTablefield[$campo];
								$objItemsproductregion->$namefield = $data[$campo];
								//echo '<hr>id '.$objItemsproductregion->id.' '.$campo.' '.$namefield.' '.$data[$campo];
							}
						}
						$objItemsproductregion->fk_item_product = $resip;
						$objItemsproductregion->fk_region = GETPOST('fk_region');
						$objItemsproductregion->fk_sector = GETPOST('fk_sector');
						$objItemsproductregion->units = GETPOST('units');
						if (empty($objItemsproductregion->units)) $objItemsproductregion->units=1;
						$objItemsproductregion->commander = 0;
						if ($objItemsproduct->group_structure!='MQ')
						{
							$objItemsproductregion->price_productive=100;
							$objItemsproductregion->price_improductive=0;
						}
						else
						{
							if (empty($objItemsproductregion->price_productive)) $objItemsproductregion->price_productive=0;
							$objItemsproductregion->price_improductive=100-$objItemsproductregion->price_productive;
							if (price2num($objItemsproductregion->price_productive,'MT')==100) $objItemsproductregion->commander=1;
						}

						if (empty($objItemsproductregion->amount_noprod)) $objItemsproductregion->amount_noprod=0;
						//$objItemsproductregion->amount = $objItemsproductregion->price_productive;
						if (empty($objItemsproductregion->amount)) $objItemsproductregion->amount=0;
						if (empty($objItemsproductregion->cost_direct)) $objItemsproductregion->cost_direct=0;
						if (empty($objItemsproductregion->performance)) $objItemsproductregion->performance=0;

						if (empty($objItemsproductregion->fk_origin)) $objItemsproductregion->fk_origin=0;
						if (empty($objItemsproductregion->percent_origin)) $objItemsproductregion->percent_origin=100;

						$objItemsproductregion->fk_user_create= $user->id;
						$objItemsproductregion->fk_user_mod= $user->id;
						$objItemsproductregion->datec= $now;
						$objItemsproductregion->datem= $now;
						$objItemsproductregion->tms= $now;
						$objItemsproductregion->status= 1;
						//echo '<pre>';
						//print_r($objItemsproductregion);
						//echo '</pre>';
						if ($lAdd) $resipr = $objItemsproductregion->create($user);
						else $resipr = $objItemsproductregion->update($user);
						if ($resipr<=0)
						{
							$error++;
							setEventMessages($objItemsproductregion->error,$objItemsproductregion->errors,'errors');
						}
					}
					else
					{
						$error++;
						setEventMessages($objItemsproduct->error,$objItemsproduct->errors,'errors');
					}
				}
			}
		}
	}
	//exit;
	if (!$error)
	{
		setEventMessages($langs->trans('Proceso satisfactorio de importación'),null,'mesgs');
		$db->commit();
		$action = '';
		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
	else
	{
		setEventMessages($langs->trans('Se ha encontrado errores al importar').' '.$error,null,'errors');
		$action = '';
		$db->rollback();
	}
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

//armamos las regiones en un array
$filter='';
$res = $objCregiongeographic->fetchAll('ASC','t.label',0,0,array('status'=>1),'AND,$filter');
if ($res>0)
{
	$lines = $objCregiongeographic->lines;
	foreach ($lines AS $j => $line)
		$aRegiongeographic[$line->id] = $line->label.' ('.$line->ref.')';
}
//armamos las instituiones en un array
$res = $objCclasfin->fetchAll('ASC','t.label',0,0,array('active'=>1),'AND,$filter');
if ($res>0)
{
	$lines = $objCclasfin->lines;
	foreach ($lines AS $j => $line)
		$aInstitutional[$line->id] = $line->label.' ('.$line->ref.')';
}

/********************************************
 * View
 */

$form=new Form($db);
$formOther = new FormOther($db);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Importfile"),$help_url);

// Add
if ($action == 'edit')
{
	$selrow = GETPOST('selrow');
	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];

	$tempdir = $conf->budget->dir_output."/tmp/";
	if (! file_exists($tempdir))
	{
		if (dol_mkdir($tempdir) < 0)
		{
			echo ' no se creo ';
			setEventMessages($langs->trans('ErrorCanNotCreateDir'),null,'errors');
			$error++;
		}
	}


	//compruebo si la extension es correcta
	if(move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
	{

		//  echo "file uploaded<br>";
	}
	else
	{
		setEventMessages($langs->trans('Cannotmovethefile'),null,'errors');
		$error++;
	}

	$objPHPExcel = new PHPExcel();
	if ($type==1)
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	else
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	//phpinfo();
	//echo "Nombre de Archivo". $nombre_archivo;
	$objPHPExcel = $objReader->load($tempdir.$nombre_archivo);

	$pagexls = $hoja-1;
	if ($pagexls<0) $pagexls=0;
	$objPHPExcel->setActiveSheetIndex($pagexls);

	//$objReader = new PHPExcel_Reader_Excel2007();
	$objReader->setReadDataOnly(true);
	//$objPHPExcel = $objReader->load('C:\xampp\htdocs\hotelplayagolfsitges\wp-content\tesipro.xlsx');

	$aCurren = array();
	$nLoop = 7;

	$line=1;
	$nLimitempty = 3;
	$nLimit = 1;
	$lLoop = true;
	$aHeaders = array();
	if ($selrow)
	{
		for ($a = 1; $a <= $nLoop; $a++)
		{
			$dato = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getValue();
			if (!empty($dato))
			{
				$aHeaders[$a-1]=$dato;
			}
		}
		$line++;
	}
	$cItem = '';
	$cGroup='';
	$label = '';
	while ($lLoop == true)
	{
		if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeader[1].$line)->getValue()))
		{
			$nLimit=1;
			for ($a = 1; $a <= $nLoop; $a++)
			{
				$aCurren[$line][$a] = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getFormattedValue();
				if ($a==1)
				{
					//buscamos si la columna A tiene el valor de actividad
					$pos = strpos($aCurren[$line][$a],'Actividad:');
					if ($pos === false)
					{
					}
					else
					{
						if ($cItem != $aCurren[$line][$a])
						{
							$aItems[$aCurren[$line][$a]]=array();
							$cItem = $aCurren[$line][$a];
						}
					}
					$pos = strpos($aCurren[$line][$a],'Unidad:');
					if ($pos === false)
					{
					}
					else
					{
						if (!empty($cItem))
						{
							$aCitem = explode('-',$cItem);
							$len = count($aCitem);
							if ($len == 1) $cItemtmp = $cItem;
							else
							{
								$cItemtmp='';
								for($b=1; $b<$len;$b++)
								{
									if (!empty($cItemtmp)) $cItemtmp.=' - ';
									$cItemtmp.=trim($aCitem[$a]);
								}
							}
							$aItemsunidad[$cItemtmp]=substr($aCurren[$line][$a],8,200);
						}
					}

					//echo '<hr>'.$aCurren[$line][$a];
					if ($aParamrev[$aCurren[$line][$a]])
					{
						if ($cGroup != $aCurren[$line][$a])
						{
							$cGroup = $aCurren[$line][$a];
						}
					}
					if (!empty($cItem) && !empty($cGroup))
					{
						$label = $aCurren[$line][$a];
						//$aContenido[$cItem][$cGroup][$label] = array();
					}
				}
				else
				{
					$valuetmp = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getValue();
					if (empty($valuetmp)) $valuetmp=0;
					elseif (is_numeric($valuetmp)) $valuetmp=$valuetmp * 100;

					if ($a==2) $aContenido[$cItem][$cGroup][$label]['unidad'] = $aCurren[$line][$a];
					if ($a==3) $aContenido[$cItem][$cGroup][$label]['cantidad'] = $aCurren[$line][$a];
					if ($a==4) $aContenido[$cItem][$cGroup][$label]['porc_productive'] = (is_numeric($valuetmp)?price2num($valuetmp,$nDecimal):$valuetmp);
					if ($a==5) $aContenido[$cItem][$cGroup][$label]['price_improductive'] = $aCurren[$line][$a];
					if ($a==6) $aContenido[$cItem][$cGroup][$label]['price_productive'] = $aCurren[$line][$a];
					if ($a==7) $aContenido[$cItem][$cGroup][$label]['cost_direct'] = $aCurren[$line][$a];
				}
			}
		}
		else
		{
			if ($nLimit > $nLimitempty)
				$lLoop = false;
			else
				$nLimit++;
		}
		$line++;
	}
//echo '<pre>';
//print_r($aContenido);
//echo '</pre>';
	$aDatanew = array();
	$aTitle = array();
	//vamos a depurar el resultado
	foreach ($aContenido AS $cItemtmp => $aItem)
	{
		$fk_item = 0;
		//vamos a mejorar el nombre del item
		$aCitem = explode('-',$cItemtmp);
		$len = count($aCitem);
		if ($len == 1) $cItem = $cItemtmp;
		else
		{
			$cItem='';
			for($a=1; $a<$len;$a++)
			{
				if (!empty($cItem)) $cItem.=' - ';
				$cItem.=trim($aCitem[$a]);
			}
		}
		//vamos a buscar al item
		$filterItem = " AND t.detail = '".trim($cItem)."'";
		$filterItem.= " AND t.entity = ".$conf->entity;
		$filterItem.= " AND t.status >= 0";

		$resitem = $objItems->fetchAll('','',0,0,array(),'AND',$filterItem,true);
		if ($resitem==1) $fk_item = $objItems->id;

		foreach ($aItem AS $cGroup => $aResource)
		{
			foreach ($aResource AS $resource => $data)
			{

				$fk_item_product=0;
				$fk_product=0;
				$ref = '';
				//vamos a buscar el resource en productos
				$filter = " AND t.label ='".$resource."'";
				$resprod = $objProduct->fetchAll('','',0,0,array(),'AND',$filter,true);
				if ($resprod==1)
				{
					$fk_product= $objProduct->id;
					$ref = $objProduct->ref;
				}
				//vamos a buscar el producto en el item
				$resip = $objItemsproduct->fetch(0,null,$fk_item,$fk_product,'');
				if ($resip==1) $fk_item_product = $objItemsproduct->id;
				if (!empty($data['unidad']))
				{
					//vamos a buscar la unidad
					$fk_unit = 0;
					$unidadtmp = substr($data['unidad'],0,2);
					if ($unidadtmp == 'HR') $data['unidad'] = 'HR';
					$resunits = $objCunits->fetch(0,$data['unidad']);
					if ($resunits==1) $fk_unit = $objCunits->id;
					$lAdd=false;
					foreach ($data AS $campo => $value)
					{
						if (!empty($value) && !empty($cGroup)) $lAdd=true;
						if ($aParamrev[$cGroup] == 'AC') $lAdd=false;
						if ($lAdd)
						{
							$aDatanew[$cItem][$cGroup][$resource][$campo]= $value;
							$aTitle[$campo] = $campo;
						}
					}
					if ($aParamrev[$cItem] == 'AC') $lAdd=false;
					if ($lAdd)
					{
						$aDatanew[$cItem][$cGroup][$resource]['fk_product'] = $fk_product;
						$aTitle['fk_product'] = 'fk_product';
						$aDatanew[$cItem][$cGroup][$resource]['ref'] = $ref;
						$aTitle['ref'] = 'ref';
						$aDatanew[$cItem][$cGroup][$resource]['fk_item'] = $fk_item;
						$aTitle['fk_item'] = 'fk_item';
						$aDatanew[$cItem][$cGroup][$resource]['group_structure'] = $aParamrev[$cGroup];
						$aTitle['group_structure'] = 'group_structure';
						$aDatanew[$cItem][$cGroup][$resource]['fk_unit'] = $fk_unit;
						$aTitle['fk_unit'] = 'fk_unit';
						$aDatanew[$cItem][$cGroup][$resource]['fk_item_product'] = $fk_item_product;
						$aTitle['fk_item_product'] = 'fk_item_product';
					}
				}
			}
		}
	}

	$aNewdata = array();
	$aDate = $aDateimport;
	$aItemsparent= array();

	print '<table class="border centpercent">';
	//cabecera
	print '<tr class="liste_titre">';
	print '<th>'.$langs->trans('Detalle').'</th>';
	foreach ($aTitle AS $campo)
	{
		print '<th>'.$campo.'</th>';
	}
	print '</tr>';
	$colspan = count($aTitle)+1;
	$error=0;
	foreach ($aDatanew AS $cItem => $datag)
	{
		$var = !$var;
		print '<tr '.$bc[$var].'>';
		print '<td colspan="'.$colspan.'">'.$cItem.'</td>';
		print '</tr>';
		$lUpdateunit = false;
		foreach ($datag AS $cGroup => $aResource)
		{
			$var = !$var;
			print '<tr '.$bc[$var].'>';
			print '<td colspan="'.$colspan.'">'.$cGroup.'</td>';
			print '</tr>';
			foreach ($aResource AS $resourse => $data)
			{
				$var = !$var;
				print '<tr '.$bc[$var].'>';
				print '<td>'.$resourse.'</td>';
				foreach ($aTitle AS $campo)
				{
					print '<td>'.$data[$campo].'</td>';
					if ($campo=='fk_unit' && empty($data[$campo]))
					{
						$error++;
						setEventMessages($langs->trans('No existe la unidad').' '.$data['unidad'],null,'errors');
					}
					if ($campo=='fk_item' && empty($data[$campo]))
					{
						$error++;
						setEventMessages($langs->trans('No existe el item').' '.$cItem,null,'errors');
					}
					if ($campo=='fk_product' && empty($data[$campo]))
					{
						$error++;
						setEventMessages($langs->trans('No existe el insumo').' '.$resourse,null,'errors');
					}
				}
				print '</tr>';
			}
		}
	}
	print '</table>';


	echo '<br>Errores encontrados : '.$error;
	//echo '<br>Numero de registros : '.;

	/*foreach ($aArray AS $r => $data)
	{


		foreach ($data 	AS $fecha => $row)
		{
			$i =0;
			foreach ($row 	AS $campo => $value)
			{
				if($i == 1){
					print('-> '.$value);
				}
				$i++;
			}

		}
	}*/

	//echo ('<pre>');
	//print_r($aDatanew);
	//echo ('</pre>');

	if (!$error)
	{

		$_SESSION['aDatanew'] = serialize($aDatanew);
		$_SESSION['aTitle'] = serialize($aTitle);
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="step" value="'.$stepnew.'">';
		print '<input type="hidden" name="type" value="'.$type.'">';
		print '<input type="hidden" name="status" value="'.$status.'">';
		print '<input type="hidden" name="hoja" value="'.$hoja.'">';
		print '<input type="hidden" name="memberkey" value="'.$memberkey.'">';
		print '<input type="hidden" name="typeobjetive" value="'.GETPOST('typeobjetive').'">';
		print '<input type="hidden" name="finality" value="'.GETPOST('finality').'">';
		print '<input type="hidden" name="fk_departament" value="'.GETPOST('fk_departament').'">';
		print '<input type="hidden" name="fk_region" value="'.GETPOST('fk_region').'">';
		print '<input type="hidden" name="fk_sector" value="'.GETPOST('fk_sector').'">';
		print '<input type="hidden" name="dateimport" value="'.$dateimport.'">';
		print '<center><br><input type="submit" class="butAction" name="save" value="'.$langs->trans("Save").'">';
		print '&nbsp;<input type="submit" class="butActionDelete" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
		print '</form>';
	}
	dol_fiche_end();

}

$aType = array(1=>$langs->trans('XLSX'),2=>$langs->trans('XLS'));
$aFinality = array(0=>$langs->trans('Gastos'));
$aApprove=array(0=>$langs->trans('Tobeapproved'),1=>$langs->trans('Approved'));
if ($action == 'create' || empty($action))
{
	$var = false;
	print_fiche_titre($langs->trans("Importproduct"));
	print '<form name="formrep" action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">';

	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="edit">';

	dol_fiche_head();
	print '<table class="border centpercent">'."\n";

	print '<tr><td class="fieldrequired">';
	print $langs->trans('Firstrowistitle');
	print '</td>';
	print '<td>';
	//print $form->selectyesno('selrow',(GETPOST('selrow')?GETPOST('selrow'):1),1);
	print $langs->trans('Yes');
	print '<input type="hidden" name="selrow" value="0">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">';
	print $langs->trans('Numberpage');
	print '</td>';
	print '<td>';
	print '<input type="number" min="1" max="20" name="hoja" value="'.GETPOST('hoja').'">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">';
	print $langs->trans('Fieldfk_region');
	print '</td>';
	print '<td>';
	print $form->selectarray('fk_region',$aRegiongeographic,GETPOST('fk_region'),1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">';
	print $langs->trans('Fieldfk_sector');
	print '</td>';
	print '<td>';
	print $form->selectarray('fk_sector',$aInstitutional,GETPOST('fk_sector'),1);
	print '</td></tr>';

	foreach ($aParamlabel AS $code => $label)
	{
	print '<tr><td class="fieldrequired">';
	print $langs->trans('Registertext').' '.$label;
	print '</td>';
	print '<td>';
	print '<input type="text" name="'.$code.'" value="'.$aParam[$code].'" size="40">';
	print '</td></tr>';

	}

	print '<tr><td class="fieldrequired">';
	print $langs->trans('Typefile');
	print '</td>';
	print '<td>';
	print $form->selectarray('type',$aType,GETPOST('type'),0);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">';
	print $langs->trans('Selectarchiv');
	print '</td>';
	print '<td>';
	print '<input type="file" name="archivo" size="40">';
	print '</td></tr>';
	print '</table>';
	dol_fiche_end();
	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Upload").'"></center>';
	print '</form>';
	print '<br><br>';
	print '<a href="'.DOL_URL_ROOT.'/budget/items/tmp/items.xlsx'.'">'.$langs->trans('Downloadexample').'</a>';
}

llxFooter();
$db->close();

?>
