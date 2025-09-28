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
dol_include_once('/budget/class/itemsgroupext.class.php');
dol_include_once('/orgman/class/cregiongeographic.class.php');
dol_include_once('/orgman/class/cclasfin.class.php');
dol_include_once('/budget/class/productasset.class.php');
dol_include_once('/budget/class/puvariablesext.class.php');

dol_include_once('/budget/class/itemsproduct.class.php');
dol_include_once('/budget/class/itemsproduction.class.php');
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
$objItemsproduction = new Itemsproduction($db);
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
	$aVariablesid = unserialize($_SESSION['aVariablesid']);

	//$valorPrimero = $arrayAssistance[1][2];
	$indMarcacion = 1;
	$c=0;
	$swValorPrimero = 0;
	$valorPrimero = 0;
	$feAcceso = 0;

	$db->begin();
	foreach ($aArray AS $cItem => $datag)
	{
		if (!$error)
		{
			$fk_item = $datag['fk_item'];
			foreach ($datag['pos'] AS $pos => $aItemproduct)
			{
				foreach ($aItemproduct AS $fk_items_product => $data)
				{
					$resip = $objItemsproduct->fetch($fk_items_product);
					if ($resip==1)
					{
						if (!empty($data['formula']))
						{
							//echo '<hr>'.$fk_items_product;
							//echo ' formula '.
							$objItemsproduct->formula = $data['formula'];
							$resip = $objItemsproduct->update($user);
							if ($resip<=0)
							{
								$error++;
								setEventMessages($objItemsproduct->error,$objItemsproduct->errors,'errors');
							}
						}
					}
					foreach ($aVariablesid AS $variable => $fk_variable)
					{
						if (!$error)
						{
							if ($data['variable'][$variable]>0)
							{
								//buscamos la relacion
								$lAdd = true;
								$objItemsproduction->initAsSpecimen();
								$res = $objItemsproduction->fetch(0,$fk_item,$fk_variable,$fk_items_product,$fk_region,$fk_sector);
								if ($res==1) $lAdd = false;
								$objItemsproduction->fk_variable = $fk_variable;
								$objItemsproduction->quantity = $data['variable'][$variable];

								//campos genericos
								$objItemsproduction->fk_region= $fk_region;
								$objItemsproduction->fk_sector= $fk_sector;
								$objItemsproduction->fk_item= $fk_item;
								$objItemsproduction->fk_items_product= $fk_items_product;
								$objItemsproduction->active= 1;
								$objItemsproduction->fk_user_mod= $user->id;
								$objItemsproduction->datem= $now;
								$objItemsproduction->tms= $now;
								$objItemsproduction->status= 1;
								if ($lAdd)
								{
									$objItemsproduction->datec= $now;
									$objItemsproduction->fk_user_create= $user->id;
									$resip = $objItemsproduction->create($user);
								}
								else
									$resip = $objItemsproduction->update($user);
								if ($resip<=0)
								{
									$error++;
									setEventMessages($objItemsproduct->error,$objItemsproduct->errors,'errors');
								}
							}
						}
					}
				}
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
$aVariablesid=array();
if ($resvar >0)
{
	$lines = $objPuvariables->lines;
	foreach ($lines AS $j => $line)
	{
		$aVariables[$line->ref] = $line->ref;
		$aVariablesid[$line->ref] = $line->id;
	}
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


							$aItems[$cItem] = array();
						}
					}
					//vamos a verificar si la columna uno tiene algun valor de la variable
					if ($aVariables[trim($dato)])
					{
						//estamos en la fila donde se esta en una variable
						for ($b=4; $b <=  $nLoop; $b++)
						{
							$valor = $objPHPExcel->getActiveSheet()->getCell($aHeader[$b].$line)->getValue();
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
							$equipo = $objPHPExcel->getActiveSheet()->getCell($aHeader[$b].$linenext)->getValue();
							$aEquipo[$cItem][$b] = $equipo;
						}
					}
				}
				elseif($a == 4)
				{
					$dato = trim($aCurren[$line][$a]);
					if (substr($dato,0,2) == 'P=')
					{
						$linenext= $line;
						for ($b=4; $b <=  $nLoop; $b++)
						{
							$formula = $objPHPExcel->getActiveSheet()->getCell($aHeader[$b].$linenext)->getValue();
							$aFormula[$cItem][$b] = substr($formula,2,200);
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
		/*
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
		*/
		//vamos a buscar al item
		$fk_item=0;
		$filterItem = " AND t.detail = '".$cItem."'";
		$filterItem.= " AND t.entity = ".$conf->entity;
		$filterItem.= " AND t.status >= 0";
		$resitem = $objItems->fetchAll('','',0,0,array(),'AND',$filterItem,true);
		if ($resitem==1) $fk_item = $objItems->id;
		if ($fk_item >0)
		{
			$aDatanew[$cItem]['fk_item'] = $fk_item;
			foreach ($aData AS $variable => $datap)
			{
				foreach ($data AS $col => $value)
				{
					if (!empty($value))
					{
						$fk_item_product = 0;
						//vamos a buscar al equipo
						$filterEq = " AND t.label = '".$value."'";
						$filterEq.= " AND t.fk_item = ".$fk_item;
						$filterEq.= " AND t.group_structure = 'MQ'";
						$resip = $objItemsproduct->fetchAll('','',0,0,array(),'AND',$filterEq,true);
						if ($resip == 1) $fk_item_product = $objItemsproduct->id;
						//$aDatanew[$cItem]['pos']= $col;
						//$aDatanew[$cItem][$col][$fk_item_product]['fk_item_product'] = $fk_item_product;
						$aDatanew[$cItem]['pos'][$col][$fk_item_product]['equipment'] = $value;
						$aDatanew[$cItem]['pos'][$col][$fk_item_product]['formula'] = $aFormula[$cItem][$col];
						$aDatanew[$cItem]['pos'][$col][$fk_item_product]['variable'][$variable] = $datap[$col];
					}
				}
			}
		}
	}
	//echo '<pre>';
	//print_r($aFormula);
	//print_r($aDatanew);
	//echo '</pre>';
	print '<table class="border centpercent">';
	//cabecera
	print '<tr class="liste_titre">';
	print '<th>'.$langs->trans('Item').'</th>';
	print '<th>'.$langs->trans('Id').'</th>';
	print '<th>'.$langs->trans('Formula').'</th>';
	foreach ($aVariables AS $variable)
		print '<th>'.$variable.'</th>';
	print '</tr>';

	foreach ($aDatanew AS $cItem => $data)
	{
		print '<tr>';
		print '<td>'.$cItem.'</td>';
		print '<td>'.$data['fk_item'].'</td>';
		print '<td>'.'</td>';
		print '</tr>';
		foreach ($data['pos'] AS $pos => $datatwo)
		{
			foreach($datatwo AS $fk_item_product => $variables)
			{
				print '<tr>';
				print '<td>'.$variables['equipment'].'</td>';
				print '<td>'.$fk_item_product.'</td>';
				print '<td>'.$variables['formula'].'</td>';
				if (empty($fk_item_product))
				{
					//echo '<hr>errr '.$cItem.' '.$fk_item_product;
					$error++;
					setEventMessages($langs->trans('No existe el insumo ').' '.$variables['equipment'].' en el item: '.$cItem,null,'errors');
				}
				$dataVariable = $variables['variable'];
				foreach($aVariables AS $variable)
				{
					print '<td>'.$dataVariable[$variable].'</td>';
				}
				print '</tr>';
			}
		}
	}
	print '</table>';

	echo '<br>Errores encontrados : '.$error;

	if (!$error)
	{

		$_SESSION['aDatanew'] = serialize($aDatanew);
		$_SESSION['aVariables'] = serialize($aVariables);
		$_SESSION['aVariablesid'] = serialize($aVariablesid);
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
