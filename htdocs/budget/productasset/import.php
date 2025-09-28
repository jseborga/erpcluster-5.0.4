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
require_once DOL_DOCUMENT_ROOT.'/budget/lib/utils.lib.php';

dol_include_once('/budget/class/productext.class.php');
dol_include_once('/productext/class/productadd.class.php');
dol_include_once('/budget/class/productasset.class.php');
dol_include_once('/budget/class/cunits.class.php');
dol_include_once('/budget/class/ctypeengine.class.php');

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
$typeproduct = GETPOST('typeproduct');
$fk_categorie = GETPOST('fk_categorie');
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
$objProductasset = new Productasset($db);
$objCunits = new Cunits($db);
$objCtypeengine = new Ctypeengine($db);
$objCategorie = new Categorie($db);

$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');

$aDate = dol_getdate(dol_now());
$aTypeproduct=array(0=>$langs->trans('Product'),1=>$langs->trans('Service'));
if (!$user->rights->budget->asset->upload)
{
	accessforbidden();
}

//params docum
/*
 1 = Id
 2 = Login
 3 = Docum
*/

 $aMonth = array(2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11,13=>12);
 $aHeader = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z',27=>'AA',28=>'AB',29=>'AC',30=>'AD',31=>'AE',32=>'AF',33=>'AG',34=>'AH',35=>'AI',36=>'AJ',37=>'AK',38=>'AL',39=>'AM',40=>'AN',41=>'AO',42=>'AP',43=>'AQ',44=>'AR',45=>'AS',46=>'AT',47=>'AU',48=>'AV',49=>'AW',50=>'AX',51=>'AY',52=>'AZ');
 $aCampoeje = array(1=>'area',2=>'siglaoperation',3=>'description',4=>'siglastructure',5=>'partida',6=>'cuenta',7=>'presupuesto',8=>'modificacion',9=>'aprobado',10=>'ejecutado',11=>'saldo',12=>'fk_objetive',13=>'fk_structure');

 $aCampo= array(1=>'tiempo',2=>'usuario',3=>'nombre',4=>'apellido',5=>'tarjeta',6=>'dispositivo',7=>'punto',8=>'verificacion',9=>'estado',10=>'evento',11=>'nota',12=>'fechagen');
 $aCamporev= array('tiempo'=>1,'usuario'=>2,'nombre'=>3,'apellido'=>4,'tarjeta'=>5,'dispositivo'=>6,'punto'=>7,'verificacion'=>8,'estado'=>9,'evento'=>10,'nota'=>11,'fechagen'=>12);

 $aCampo= array(0=>'id',1=>'tipo',2=>'ref',3=>'descripcion',4=>'formula',5=>'unidad',6=>'costoadquisicion',7=>'potencia_motor',8=>'tipo_motor',9=>'costo_llanta',10=>'vida_util_llantas',11=>'vida_util_anio',12=>'vida_util_hora',13=>'valor_residual',14=>'reparacion',15=>'interes',16=>'consumo_diesel',17=>'lubicante_diesel',18=>'consumo_gasolina',19=>'libricante_gasolina',20=>'costo_diesel',21=>'costo_gasolina',22=>'energia_kw',23=>'depreciacion',24=>'interes_seguro',25=>'consumo_combustible',26=>'consumo_lubricante',27=>'reposicion_llanta',28=>'reparacion_repuesto',29=>'costo_productivo',30=>'costo_improductivo',31=>'descripcion_larga');
 $aTabcampo= array('id'=>'id','type'=>'tipo','ref'=>'ref','description'=>'descripcion_larga','label'=>'descripcion','formula'=>'formula','unidad'=>'unidad','cost_acquisition'=>'costoadquisicion','engine_power'=>'potencia_motor','tipo_motor'=>'tipo_motor','cost_tires'=>'costo_llanta','useful_life_tires'=>'vida_util_llantas','useful_life_year'=>'vida_util_anio','useful_life_hours'=>'vida_util_hora',
 	'percent_residual_value'=>'valor_residual',
 	'percent_repair'=>'reparacion',
 	'percent_interest'=>'interes','diesel_consumption'=>'consumo_diesel',
 	'diesel_lubricants'=>'lubicante_diesel','gasoline_consumption'=>'consumo_gasolina',
 	'gasoline_lubricants'=>'libricante_gasolina','cost_diesel'=>'costo_diesel','cost_gasoline'=>'costo_gasolina',
 	'energy_kw'=>'energia_kw','cost_depreciation'=>'depreciacion','cost_interest'=>'interes_seguro',
 	'cost_fuel_consumption'=>'consumo_combustible','cost_lubricants'=>'consumo_lubricante',
 	'cost_tires_replacement'=>'reposicion_llanta','cost_repair'=>'reparacion_repuesto',
 	'cost_pu_productive'=>'costo_productivo','cost_pu_improductive'=>'costo_improductivo','fk_unit'=>'fk_unit',
 	'fk_product'=>'fk_product','fk_product_add'=>'fk_product_add','fk_type_engine'=>'fk_type_engine');
 //armamoe aCamporev
 $aCamporev = array();
 foreach ($aCampo AS $j => $value) $aCamporev[$value] = $j;
 foreach ($aTabcampo AS $j => $value) $aTabcamporev[$value] = $j;

//agregamos los ultimos
 $aCamporev['fk_unit']=33;
 $aCamporev['fk_product']=34;
 $aCamporev['fk_product_asset']=35;
 $aCamporev['fk_type_engine']=36;

 $aKey=array(1=>$langs->trans('CI'),2=>$langs->trans('Id'),3=>$langs->trans('Login'));

/************************
 *       Actions        *
 ************************/
$now = dol_now();
// AddSave
if ($action == 'add' && GETPOST('save') == $langs->trans('Save') && $user->rights->budget->asset->upload)
{
	/*verificamos los tipos*/

	$error = 0;
	$aArray   = unserialize($_SESSION['aArraylicence']);

	//$valorPrimero = $arrayAssistance[1][2];
	$indMarcacion = 1;
	$c=0;
	$swValorPrimero = 0;
	$valorPrimero = 0;
	$feAcceso = 0;

	$db->begin();

	foreach ($aArray AS $j => $row)
	{
		if (!$error)
		{
			$lAdd = true;
			$objProductasset->initAsSpecimen();
			if ($row['fk_product']>0)
			{
				$res = $objProductasset->fetch(0,$row['fk_product']);
				if ($res==1) $lAdd = false;
			}
			$now = dol_now();
			//vamos a crear el producto
			foreach ($aCamporev AS $k => $val)
			{
				$campo = $aTabcamporev[$k];
				if ($campo != 'id' && !empty($campo))
				{
					if ($campo == 'description')
					{
						$objProductasset->description = $row[$k];
					}
					elseif ($campo != 'formula')
					{
						if ($row[$k]>0)
							$objProductasset->$campo = $row[$k];
						else
							$objProductasset->$campo = 0;
					}
					else
						$objProductasset->$campo = $row[$k];
				}
			}
			if (empty($objProductasset->cost_hour_improductive))$objProductasset->cost_hour_improductive=0;
			if (empty($objProductasset->cost_hour_productive))$objProductasset->cost_hour_productive=0;
			$objProductasset->status = 1;
			$objProductasset->fk_user_create = $user->id;
			$objProductasset->fk_user_mod = $user->id;
			$objProductasset->datec = $now;
			$objProductasset->datem = $now;
			$objProductasset->tms = $now;

			if ($lAdd) $resq = $objProductasset->create($user);
			else $resq = $objProductasset->update($user);
			if($resq <=0){
				$error++;
				setEventMessages($objProductasset->error,$objProductasset->errors,'errors');
			}
			if (!$error)
			{
				//vamos a actualizar el producto
				$res = $objProduct->fetch($objProductasset->fk_product);
				if ($res == 1)
				{
					if ($objProduct->description != $objProductasset->description && !empty($objProductasset->description))
					{
						$objProduct->description = $objProductasset->description;
						$resup = $objProduct->update($objProductasset->fk_product,$user);
						if ($resup<=0)
						{
							$error++;
							setEventMessages($objProduct->error,$objProduct->errors,'errors');
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
	$nLoop = 32;

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

	while ($lLoop == true)
	{
		if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeader[1].$line)->getValue()))
		{
			$nLimit=1;
			for ($a = 1; $a <= $nLoop; $a++)
			{
				$aCurren[$line][$a] = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getFormattedValue();
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
	$aNewdata = array();
	$aDate = $aDateimport;

	foreach ($aCurren AS $j => $data)
	{
		$lUpdateunit = false;
		foreach ($data AS $lm => $value)
		{
			$k = $lm-1;
			$row[$aCampo[$k]] = $value;

			if ($aCampo[$k] == 'ref')
			{
				$lRef=true;
				$lAddproduct=false;
				//buscamos al producto
				if (!empty($value))
				{
					$res = $objProduct->fetch (0,$value);
					if ($res==1)
					{
						$lUpdateunit = false;
						if ($objProduct->fk_unit<=0) $lUpdateunit=true;
						$row['fk_product'] = $objProduct->id;
					}
					elseif($res==0)
					{
						$lAddproduct=true;
						$lUpdateunit=true;
					}
					else
					{
						$error++;
						setEventMessages($objProduct->error,$objProduct->errors,'errors');
					}
				}
				else
				{
					$lRef=false;
					//$error++;
					//setEventMessages($langs->trans('Sin valor en ref').' '.$langs->trans('en la linea').' '.$row['id'],null,'errors');
				}
			}
			if ($aCampo[$k] == 'descripcion')
			{
				if (!$lRef)
				{
					$lAddproduct=false;
					//buscamos al producto
					if (!empty($value))
					{
						$filter = " AND t.label = '".$value."'";
						$res = $objProduct->fetchAll ('','',0,0,array(),'AND',$filter,true);
						if ($res==1)
						{
							$lRef=true;
							$lUpdateunit = false;
							if ($objProduct->fk_unit<=0) $lUpdateunit=true;
							$row['fk_product'] = $objProduct->id;
						}
						elseif($res==0)
						{
							$lAddproduct=true;
							$lUpdateunit=true;
							$row['fk_product']=0;
						}
						elseif ($res>1)
						{
							$row['fk_product']=0;
							$error++;
							$err=100;
							setEventMessages($langs->trans('Existe mas de un producto con ese nombre').$value,null,'errors');
						}
						else
						{
							$row['fk_product']=0;
							$error++;
							$err=101;
							setEventMessages($objProduct->error,$objProduct->errors,'errors');
						}
					}
					else
					{
						$row['fk_product']='';
						$lref=false;
						$error++;
						$err=102;
						setEventMessages($langs->trans('Sin valor en ref').' '.$langs->trans('en la linea').' '.$row['id'],null,'errors');
					}
				}
			}
			if ($aCampo[$k] == 'unidad')
			{
				$lAddunit=false;
				//buscamos la unidad
				if (!empty($value))
				{
					$res = $objCunits->fetch (0,$value);
					if ($res==1)
					{
						$row['fk_unit'] = $objCunits->id;
					}
					elseif($res==0)
					{
						$lAddunit=true;
					}
					else
					{
						$error++;
						$err=103;
						setEventMessages($objCunits->error,$objCunits->errors,'errors');
					}
				}
				else
				{
					$error++;
					$err=104;
					setEventMessages($langs->trans('Sin valor de unidad').' |'.$value.'|'.' '.$langs->trans('para el insumo').' '.$row['descripcion'],null,'errors');
				}
			}
			if ($aCampo[$k] == 'tipo_motor')
			{
				//buscamos la tipo motor
				if (!empty($value))
				{
					$value= trim($value);
					$filter = " AND (";
					$filter.= " t.code='".strtoupper($value)."'";
					$filter.= " OR t.code='".strtolower($value)."'";
					$filter.= " OR t.code='".ucfirst($value)."'";
					$filter.= " OR t.label='".strtoupper($value)."'";
					$filter.= " OR t.label='".strtolower($value)."'";
					$filter.= " OR t.label='".ucfirst($value)."'";
					$filter.= ")";

					$res = $objCtypeengine->fetchAll('','',0,0,array(),'AND',$filter,true);
					if ($res==1)
					{
						$row['fk_type_engine'] = $objCtypeengine->id;
					}
					elseif($res==0)
					{
						$row['fk_type_engine']='';
						$error++;
						$err=105;
						setEventMessages($langs->trans('no existe en tipo motor').' '.$value.' '.$langs->trans('en la linea').' '.$row['id'],null,'errors');
					}
					else
					{
						$error++;
						$err=106;

						setEventMessages($objCtypeengine->error,$objCtypeengine->errors,'errors');
					}
				}
				else
				{
					$error++;
					$err=107;

					setEventMessages($langs->trans('Sin valor en tipo motor').' '.$langs->trans('en la linea').' '.$row['id'].' '.$row['descripcion'],null,'errors');
				}
			}
		}

		if ($lAddunit)
		{
			$objCunits->initAsSpecimen();
			$objCunits->code=$row['unidad'];
			$objCunits->label=$row['unidad'];
			$objCunits->short_label=$row['unidad'];
			$objCunits->active=1;
			$resun = $objCunits->create($user);
			if ($resun<=0)
			{
				$error++;
				$err=108;

				setEventMessages($objCunits->error,$objCunits->errors,'errors');
			}
			else
				$row['fk_unit'] = $resun;
		}

		if ($lAddproduct)
		{
			$objProduct->initAsSpecimen();
			if (empty($row['ref'])) $objProduct->ref = '(PROV)'.generarcodigo(5);
			else $objProduct->ref = $row['ref'];
			$objProduct->label = $row['descripcion'];
			$objProduct->description = $row['descripcion_larga'];
			$objProduct->statut = 0;
			$objProduct->statut_buy=1;
			$objProduct->type=$typeproduct+0;
			$objProduct->fk_unit = $row['fk_unit'];
			$resc = $objProduct->create($user);
			if ($resc <=0)
			{
				$error++;
				$err=109;
				setEventMessages($objProduct->error,$objProduct->errors,'errors');

			}
			else
			{
				$row['fk_product'] = $resc;
			}
			//vamos a unir con la categoria
			if ($fk_categorie>0)
			{
				if ($resc>0)
				{
					$objProduct->fetch($resc);
					$objProduct->setCategories($fk_categorie);
				}
			}
		}
		else
		{
			//vamos a registrar el producto con la categoria seleccionada
			if ($fk_categorie>0)
			{
				if ($row['fk_product']>0)
				{
					$objProduct->fetch($row['fk_product']);
					$objProduct->setCategories($fk_categorie);
				}
			}

		}
		if ($lUpdateunit)
		{
			//vamos a actualizar si la unidad es correcta
			if ($row['fk_unit']>0)
			{
				if ($row['fk_product']>0)
				{
					$objProduct->fetch($row['fk_product']);
					$objProduct->fk_unit = $row['fk_unit'];
					$resp = $objProduct->update_unit();
					if ($resp <=0)
					{
						$error++;
						$err=110;
						setEventMessages($objProduct->error,$objProduct->errors,'errors');
					}
				}
			}
		}

		//verificamos si existe la informacion de product asset
		$resadd = $objProductasset->fetch(0,$row['fk_product']);
		if ($resadd==1)
		{
			$row['fk_product_asset'] = $objProductasset->id;
		}
		elseif(empty($resadd))
			$row['fk_product_asset'] = 0;
		else
		{
			$error++;
			$err=112;
			setEventMessages($objProductasset->error,$objProductasset->errors,'errors');
		}
		$aNew[$j] = $row;
	}
	$aArray = $aNew;
	//tabla ordenada

	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '</tr>';
	print '<tr class="liste_titre">';

	foreach ($aCamporev AS $k => $val)
	{
		print '<th>'.($aHeaders[$val]?$aHeaders[$val]:$k).'</th>';
	}

	print '</tr>';
	$nro = 1;
	foreach ($aArray AS $j => $row)
	{
		//vamos a validar las fechas
		if ($row['date_fin']<$row['date_ini'])
		{
			$error++;
			setEventMessages($langs->trans('Thefinaldatecannotbelessthantheinitialdate').' '.$langs->trans('Verifytherecord').' '.$row['id'],null,'errors');
		}
		$var =!$var;
		print '<tr '.$bc[$var].'>';
		foreach ($aCamporev AS $k => $val)
		{
			print '<td>'.$row[$k].'</td>';
		}
	}
	print '</table>';


	print  '<br>Errores encontrados : '.$error;


	if (!$error)
	{
		$_SESSION['aArraylicence'] = serialize($aArray);
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="step" value="'.$stepnew.'">';
		print '<input type="hidden" name="type" value="'.$type.'">';
		print '<input type="hidden" name="status" value="'.$status.'">';
		print '<input type="hidden" name="memberkey" value="'.$memberkey.'">';
		print '<input type="hidden" name="typeobjetive" value="'.GETPOST('typeobjetive').'">';
		print '<input type="hidden" name="finality" value="'.GETPOST('finality').'">';
		print '<input type="hidden" name="typeproduct" value="'.GETPOST('typeproduct').'">';
		print '<input type="hidden" name="fk_departament" value="'.GETPOST('fk_departament').'">';
		print '<input type="hidden" name="dateimport" value="'.$dateimport.'">';
		print '<input type="hidden" name="fk_categorie" value="'.$fk_categorie.'">';
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
	print '<input type="hidden" name="selrow" value="1">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">';
	print $langs->trans('Producto o Servicio');
	print '</td>';
	print '<td>';
	print $form->selectarray('typeproduct',$aTypeproduct,GETPOST('typeproduct'),1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">';
	print $langs->trans('Categorie');
	print '</td>';
	print '<td>';
	print $formOther->select_categories(0,GETPOST('fk_categorie'),'fk_categorie',0,1);
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
}

llxFooter();
$db->close();

?>
