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
dol_include_once('/productext/class/productadd.class.php');
dol_include_once('/budget/class/productasset.class.php');
dol_include_once('/budget/class/cunits.class.php');
dol_include_once('/budget/class/ctypeitemext.class.php');
dol_include_once('/budget/class/itemsext.class.php');
dol_include_once('/orgman/class/cregiongeographic.class.php');
dol_include_once('/orgman/class/cclasfin.class.php');
dol_include_once('/budget/class/productasset.class.php');
dol_include_once('/budget/class/puvariablesext.class.php');

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

//Declaramos los objectos que se manejaran
$objUser  = new User($db);
$objProduct = new Productext($db);
$objProductadd = new Productadd($db);
$objCunits = new Cunits($db);
$objCtypeitem = new Ctypeitemext($db);
$objCategorie = new Categorie($db);
$objItems = new Itemsext($db);
$objTmp = new Itemsext($db);
$objCregiongeographic = new Cregiongeographic($db);
$objCclasfin = new Cclasfin($db);
$objItemsproduct = new Itemsproduct($db);
$objItemsproductregion = new Itemsproductregion($db);
$objProductasset = new Productasset($db);
$objPuvariables = new Puvariablesext($db);

$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');

$aDate = dol_getdate(dol_now());

if (!$user->rights->budget->ite->upload)
{
	accessforbidden();
}


//varaibles definidas
$aParam=array('MA'=>'3.- MATERIALES','MO'=>'2.- MANO DE OBRA','MQ'=>'1.- EQUIPO Y MAQUINARIA','AC'=>'4.- ACTIVIDADES COMPLEMENTARIAS',);
foreach ($aParam AS $j => $value)
	$aParamrev[$value] = $j;

$aTablefield=array('Detalle'=>'label','cantidad'=>'performance','proc_productive'=>'porc_productive','price_improductive'=>'price_improductive','price_productive'=>'price_productive','cost_direct'=>'cost_direct');

$cActividad ='Actividad:';
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
	$aArray   = unserialize($_SESSION['aDatanew']);

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
			foreach ($aResource AS $resourse => $data)
			{
				$objItemsproduct->initAsSpecimen();
				$objItemsproductregion->initAsSpecimen();
				foreach ($aTitle AS $campo)
				{
					$objItemsproduct->$aTablefield[$campo] = $data[$campo];
					$objItemsproductregion->$aTablefield[$campo] = $data[$campo];
					if ($campo == 'fk_product')
					{
						//vamos a buscar la formula
						$resass = $objProductasset->fetch(0,$data[$campo]);
						if ($resass==1) $objItemsproduct->formula = $objProductasset->formula;
					}
				}
				//campos genericos
				$objItemsproduct->active= 1;
				$objItemsproduct->fk_user_create= $user->id;
				$objItemsproduct->fk_user_mod= $user->id;
				$objItemsproduct->datec= $now;
				$objItemsproduct->datem= $now;
				$objItemsproduct->tms= $now;
				$objItemsproduct->status= 1;
				$resip = $objItemsproduct->create($user);
				if ($resip>0)
				{
					$objItemsproductregion->fk_item_product = $resip;
					$objItemsproductregion->fk_region = GETPOST('fk_region');
					$objItemsproductregion->fk_sector = GETPOST('fk_sector');
					$objItemsproductregion->units = GETPOST('units');
					if (empty($objItemsproductregion->units)) $objItemsproductregion->units=1;
					if (empty($objItemsproductregion->price_productive)) $objItemsproductregion->price_productive=0;
					if (empty($objItemsproductregion->price_improductive)) $objItemsproductregion->price_improductive=0;
					if (empty($objItemsproductregion->amount_noprod)) $objItemsproductregion->amount_noprod=0;
					if (empty($objItemsproductregion->amount)) $objItemsproductregion->amount=0;
					if (empty($objItemsproductregion->cost_direct)) $objItemsproductregion->cost_direct=0;
					$objItemsproductregion->commander = 0;
					$objItemsproductregion->fk_user_create= $user->id;
					$objItemsproductregion->fk_user_mod= $user->id;
					$objItemsproductregion->datec= $now;
					$objItemsproductregion->datem= $now;
					$objItemsproductregion->tms= $now;
					$resipr = $objItemsproductregion->create($user);
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
	foreach ($aArray AS $j => $row)
	{
		if (!$error)
		{
			$lAdd = true;
			$objItems->initAsSpecimen();
			if ($row['fk_item']>0)
			{
				$res = $objItems->fetch($row['fk_item']);
				if ($res==1) $lAdd = false;
			}

			$now = dol_now();
			//vamos a crear el item
			foreach ($aCamporev AS $k => $val)
			{
				$campo = $aTabcamporev[$k];
				if ($campo=='fk_parent')
				{
					if (!empty($row[$k]))
					{
					//vamos a buscar el parentesco
						$restmp = $objTmp->fetch(0,$row[$k]);
						if ($restmp==1)
							$objItems->$campo = $objTmp->id;
						else
						{
							$error++;
							setEventMessages($langs->trans('No se encuentra su superior de').' '.$row[$k],null,'errors');
						}
					}
					else
					{
						$objItems->$campo = 0;
					}
				}
				else
				{
					if ($campo != 'id' && !empty($campo))
					{
					//if ($campo != 'formula')
					//{
					//	if ($row[$k]>0)
					//		$objItems->$campo = $row[$k];
					//	else
					//		$objItems->$campo = 0;
					//}
					//else
						$objItems->$campo = $row[$k];
					}
				}
			}
			if (empty($objItems->fk_type_item)) $objItems->fk_type_item = 0;
			if (empty($objItems->fk_unit)) $objItems->fk_unit = 0;
			if (empty($objItems->quant)) $objItems->quant = 0;
			if (empty($objItems->amount)) $objItems->amount = 0;
			if (empty($objItems->version)) $objItems->version = 1;
			if (empty($objItems->manual_performance)) $objItems->manual_performance = 0;
			$objItems->status = 0;
			$objItems->entity = $conf->entity;
			$objItems->fk_user_create = $user->id;
			$objItems->fk_user_mod = $user->id;
			$objItems->datec = $now;
			$objItems->datem = $now;
			$objItems->tms = $now;

			if ($lAdd) $resq = $objItems->create($user);
			else $resq = $objItems->update($user);
			if($resq <=0){
				$error++;
				setEventMessages($objItems->error,$objItems->errors,'errors');
			}
		}
	}
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
//armamos las variables
$resvar = $objPuvariables->fetchAll('','',0,0,array(),'AND',$filter);
if ($resvar >0)
{
	$lines = $objPuvariables->lines;
	foreach ($lines AS $j => $line)
		$aVariables[$line->ref] = $line->ref;
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

	//$objReader = new PHPExcel_Reader_Excel2007();
	$objReader->setReadDataOnly(true);
	//$objPHPExcel = $objReader->load('C:\xampp\htdocs\hotelplayagolfsitges\wp-content\tesipro.xlsx');

	$aCurren = array();
	$nLoop = 10;

	$line=1;
	$nLimitempty = 10;
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
	$cActividad = 'Actividad';
	while ($lLoop == true)
	{
		if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeader[2].$line)->getValue()))
		{
			$nLimit=1;
			for ($a = 1; $a <= $nLoop; $a++)
			{
				$aCurren[$line][$a] = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getFormattedValue();
				if ($a==1)
				{
					$dato = $aCurren[$line][$a];
					$datoAct = substr($dato,0,9);
					//echo '<hr>|'.$datoAct.'| |'.$cActividad.'|';
					if (dol_string_nospecial($datoAct) === $cActividad)
					{
						//echo ' se cumple ';
						//vamos a obtener el valor siguiente
						$itemnew = $objPHPExcel->getActiveSheet()->getCell($aHeader[2].$line)->getFormattedValue();
						if ($cItem != $itemnew)
						{
							$cItem= $itemnew;
							$aItems[$cItem] = array();
						}
					}
					//vamos a verificar si la columna uno tiene algun valor de la variable
					if ($aVariables[trim($dato)])
					{
						//echo ''.$aVariables[trim($dato)].' === '.$dato;
						//estamos en la fila donde se esta en una variable
						for ($b=4; $b <=  $nLoop; $b++)
						{
							$valor = $objPHPExcel->getActiveSheet()->getCell($aHeader[$b].$line)->getFormattedValue();
							$aEquipoper[$cItem][$dato][$b] = $valor;
						}
					}
				}
				elseif($a==2)
				{
					//vamos a verificar la linea de titulos para obtener los equipos
					//echo '<hr>variab '.
					$dato = trim($aCurren[$line][$a]);
					if ($dato== 'VARIABLE')
					{
						$linenext= $line+1;
						//estamos en la fila donde se muestran los equipos
						for ($b=4; $b <=  $nLoop; $b++)
						{
							$equipo = $objPHPExcel->getActiveSheet()->getCell($aHeader[$b].$linenext)->getFormattedValue();
							$aEquipo[$cItem][$b] = $equipo;
						}

					}
				}
				else
				{
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

	$aDatanew = array();
	foreach ($aEquipo AS $cItem => $data)
	{
		//recuperamos las variables
		$aData = $aEquipoper[$cItem];
		//vamos a mejorar el nombre del item
		$cItemtmp = $cItem;
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

		foreach ($aData AS $variable => $datap)
		{
			foreach ($data AS $col => $value)
			{
				if (!empty($value))
					$aDatanew[$cItem][$value][$variable] = $datap[$col];
			}
		}
	}

	echo '<pre>';
	print_r($aDatanew);
	echo '</pre>';

	print '<table class="border centpercent">';
	//cabecera
	print '<tr class="liste_titre">';
	print '<th>'.$langs->trans('Item').'</th>';
	foreach ($aVariables AS $variable)
		print '<th>'.$variable.'</th>';
	print '</tr>';

	foreach ($aDatanew AS $cItem => $data)
	{
			print '<tr>';
			print '<td>'.$cItem.'</td>';
			print '</tr>';
		foreach ($data AS $equipo => $variables)
		{
			print '<tr>';
			print '<td>'.$equipo.'</td>';
			foreach($aVariables AS $variable)
			{
				print '<td>'.$variables[$variable].'</td>';
			}
			print '</tr>';
		}
	}
print '</table>';
exit;
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
		$filterItem = " AND t.label = '".$cItem."'";
		$filterItem.= " AND t.entity = ".$conf->entity;
		$resitem = $objItems->fetchAll('','',0,0,array(),'AND',$filterItem);
		if ($resitem==1) $fk_item = $objItems->id;
		foreach ($aItem AS $cGroup => $aResource)
		{
			foreach ($aResource AS $resource => $data)
			{
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
				if (!empty($data['unidad']))
				{
					//vamos a buscar la unidad
					$fk_unit = 0;
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
	foreach ($aDatanew AS $cItem => $datag)
	{
		print '<tr>';
		print '<td colspan="'.$colspan.'">'.$cItem.'</td>';
		print '</tr>';
		$lUpdateunit = false;
		foreach ($datag AS $cGroup => $aResource)
		{
			print '<tr>';
			print '<td colspan="'.$colspan.'">'.$cGroup.'</td>';
			print '</tr>';
			foreach ($aResource AS $resourse => $data)
			{
				print '<tr>';
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
						setEventMessages($langs->trans('No existe el insumo').' '.$resource,null,'errors');
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

	/*echo ('<pre>');
	var_dump($aArray);
	echo ('</pre>');*/

	if (!$error)
	{

		$_SESSION['aDatanew'] = serialize($aDatanew);
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="step" value="'.$stepnew.'">';
		print '<input type="hidden" name="type" value="'.$type.'">';
		print '<input type="hidden" name="status" value="'.$status.'">';
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
