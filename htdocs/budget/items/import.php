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
dol_include_once('/budget/class/itemsgroup.class.php');
dol_include_once('/productext/class/productadd.class.php');
dol_include_once('/budget/class/productasset.class.php');
dol_include_once('/budget/class/cunits.class.php');
dol_include_once('/budget/class/ctypeitemext.class.php');
dol_include_once('/budget/class/itemsext.class.php');

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
$hoja = GETPOST('hoja','int');
$fk_period  = GETPOST("fk_period");
$type  = GETPOST("type");
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
$objCunits = new Cunits($db);
$objCtypeitem = new Ctypeitemext($db);
$objCategorie = new Categorie($db);
$objItems = new Itemsext($db);
$objTmp = new Itemsext($db);
$objItemsgroup = new Itemsgroup($db);
$objTmpgroup = new Itemsgroup($db);

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

//params docum
/*
 1 = Id
 2 = Login
 3 = Docum
*/

/*
$res = $objItemsgroup->fetchAll('','',0,0,array(),'AND',$filter);
if ($res >0)
{
	$lines = $objItemsgroup->lines;
	foreach ($lines AS $j => $line)
	{
		$filter = " AND t.detail = '".$line->detail."'";
		$res = $objItems->fetchAll('','',0,0,array(),'AND',$filter,true);
		if ($res == 1)
		{
			$objItemsgroup->fetch($line->id);
			$objItemsgroup->fk_item = $objItems->id;
			$res = $objItemsgroup->update($user);
		}
	}
}
exit;
*/

 $aMonth = array(2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11,13=>12);
 $aHeader = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z',27=>'AA',28=>'AB',29=>'AC',30=>'AD',31=>'AE',32=>'AF',33=>'AG',34=>'AH',35=>'AI',36=>'AJ',37=>'AK',38=>'AL',39=>'AM',40=>'AN',41=>'AO',42=>'AP',43=>'AQ',44=>'AR',45=>'AS',46=>'AT',47=>'AU',48=>'AV',49=>'AW',50=>'AX',51=>'AY',52=>'AZ');
 $aCampoeje = array(1=>'area',2=>'siglaoperation',3=>'description',4=>'siglastructure',5=>'partida',6=>'cuenta',7=>'presupuesto',8=>'modificacion',9=>'aprobado',10=>'ejecutado',11=>'saldo',12=>'fk_objetive',13=>'fk_structure');

 $aCampo= array(1=>'tiempo',2=>'usuario',3=>'nombre',4=>'apellido',5=>'tarjeta',6=>'dispositivo',7=>'punto',8=>'verificacion',9=>'estado',10=>'evento',11=>'nota',12=>'fechagen');
 $aCamporev= array('tiempo'=>1,'usuario'=>2,'nombre'=>3,'apellido'=>4,'tarjeta'=>5,'dispositivo'=>6,'punto'=>7,'verificacion'=>8,'estado'=>9,'evento'=>10,'nota'=>11,'fechagen'=>12);

 $aCampo= array(0=>'id',1=>'ref',2=>'label',3=>'hilo',4=>'item',5=>'login',6=>'fechaini',7=>'fechafin',8=>'detail',9=>'group',10=>'type',11=>'typename',12=>'unitprogram',13=>'unit',14=>'price');
 $aTabcampo = array('rowid'=>'id','ref'=>'ref','detail'=>'label','fk_parent'=>'hilo','item'=>'item','login'=>'login','fecha_ini'=>'fechaini','fecha_fin'=>'fechafin','especification'=>'detail','quant'=>'unitprogram','amount'=>'price','unit'=>'unit','type'=>'group',
 	'fk_type_item'=>'fk_type_item','fk_unit'=>'fk_unit');
 //armamoe aCamporev
 $aCamporev = array();
 foreach ($aCampo AS $j => $value) $aCamporev[$value] = $j;
 foreach ($aTabcampo AS $j => $value) $aTabcamporev[$value] = $j;

//agregamos los ultimos
 $aCamporev['fk_item']=15;
 $aCamporev['fk_type_item']=16;
 $aCamporev['fk_unit']=17;

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
			//echo '<hr>inicia '.$row['id'].' '.$row['hilo'];
			$lAdd = true;
			$lAddg = true;
			$fk_item = 0;
			$fk_item_group = 0;
			$objItems->initAsSpecimen();
			$objItemsgroup->initAsSpecimen();
			if ($row['fk_item']>0)
			{
				$res = $objItems->fetch($row['fk_item']);
				if ($res==1) $lAdd = false;
			}
			//vamos a verificar por el grupo
			//if ($row['group']==1)
			//{
				//echo '<br>jhilo '.$row['id'].' '.$row['hilo'].' t '.$row['type'].' g '.$row['gruop'];
			$filtertmp = " AND t.ref = '".trim($row['ref'])."'";
			$filtertmp.= " AND t.entity = ".$conf->entity;
			$filtertmp.= " AND t.version = 1";

			$restmp = $objItemsgroup->fetchAll('','',0,0,array(),'AND',$filtertmp,true);
			if ($restmp == 1)
			{
				$fk_item_group = $objItemsgroup->id;
				$lAddg = false;
			}
			//}
			//vamos a verificar por el nombre
			if ($lAdd && $row['group']==0)
			{
				//echo '<br>jhilo '.$row['id'].' '.$row['hilo'].' t '.$row['type'].' g '.$row['gruop'];
				$filtertmp= " AND t.detail = '".trim($row['label'])."'";
				$restmp = $objItems->fetchAll('','',0,0,array(),'AND',$filtertmp,true);
				if ($restmp == 1)
				{
					$fk_item = $objItems->id;
					$lAdd = false;
				}
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
						$restmp = $objTmpgroup->fetch(0,$row[$k]);
						if ($restmp==1)
							$objItemsgroup->$campo = $objTmpgroup->id;
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
						if (!$lAdd)
						{
							if ($campo != 'ref') $objItems->$campo = $row[$k];
						}
						else
						{
							$objItems->$campo = $row[$k];
						}
						if (!$lAddg)
						{
							if ($campo != 'ref') $objItemsgroup->$campo = $row[$k];
						}
						else
						{
							$objItemsgroup->$campo = $row[$k];
						}
					}
				}
			}

			//vamos a verificar para itemsgroup

			if (empty($objItemsgroup->fk_parent)) $objItemsgroup->fk_parent = 0;
			if (empty($objItemsgroup->fk_type_item)) $objItemsgroup->fk_type_item = 0;
			if (empty($objItemsgroup->fk_unit)) $objItemsgroup->fk_unit = 0;
			if (empty($objItemsgroup->quant)) $objItemsgroup->quant = 1;
			if (empty($objItemsgroup->amount)) $objItemsgroup->amount = 0;
			if (empty($objItemsgroup->version)) $objItemsgroup->version = 1;
			if (empty($objItemsgroup->manual_performance)) $objItemsgroup->manual_performance = 0;
			$objItemsgroup->status = 0;
			$objItemsgroup->entity = $conf->entity;
			$objItemsgroup->fk_user_create = $user->id;
			$objItemsgroup->fk_user_mod = $user->id;
			$objItemsgroup->datec = $now;
			$objItemsgroup->datem = $now;
			$objItemsgroup->tms = $now;


			//vamos a verificar para items
			if ($row['group']==0)
			{
				if (empty($objItems->fk_parent)) $objItems->fk_parent = 0;
				if (empty($objItems->fk_type_item)) $objItems->fk_type_item = 0;
				if (empty($objItems->fk_unit)) $objItems->fk_unit = 0;
				if (empty($objItems->quant)) $objItems->quant = 1;
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

				if ($lAdd)
				{
					$resq = $objItems->create($user);
					$fk_item = $resq;
				}
				else
				{
					$resq = $objItems->update($user);
				}
				if($resq <=0){
					$error++;
					setEventMessages($objItems->error,$objItems->errors,'errors');
				}
			}

			//para grupos
			//echo '<hr>id '.$row['id'];
			if ($lAddg)
			{
				$objItemsgroup->fk_item = $fk_item;
				if (empty($objItemsgroup->fk_item)) $objItemsgroup->fk_item=0;
				if ($row['group']==0  && empty($objItemsgroup->fk_item))
				{
					$error++;
					setEventmessages($langs->trans('Error en registro de items'),null,'errors');
				}
				$resq = $objItemsgroup->create($user);
				//echo '<br>crea '.$objItems->detail.' = '.$resq;
				//echo '<br>new '.$row['hilo'].' '.$row['id'].' '.$resq;
			}
			else
			{
				$objItemsgroup->fk_item = $fk_item;
				if (empty($objItemsgroup->fk_item)) $objItemsgroup->fk_item=0;
				if ($row['group']==0  && empty($objItemsgroup->fk_item))
				{
					$error++;
					setEventmessages($langs->trans('Error en registro de items'),null,'errors');
				}
				$resq = $objItemsgroup->update($user);
				//echo '<br>actualiza '.$objItems->detail.' == '.$objItems->id;
			}

			if($resq <=0){
				$error++;
				setEventMessages($objItemsgroup->error,$objItemsgroup->errors,'errors');
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
	$pagexls = $hoja-1;
	if ($pagexls<0) $pagexls=0;
	$objPHPExcel->setActiveSheetIndex($pagexls);
	//$objReader = new PHPExcel_Reader_Excel2007();
	$objReader->setReadDataOnly(true);
	//$objPHPExcel = $objReader->load('C:\xampp\htdocs\hotelplayagolfsitges\wp-content\tesipro.xlsx');

	$aCurren = array();
	$nLoop = 14;

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
	$aItemsparent= array();
	foreach ($aCurren AS $j => $data)
	{
		$lUpdateunit = false;
		foreach ($data AS $lm => $value)
		{
			$k = $lm-1;
			$row[$aCampo[$k]] = $value;
			if ($aCampo[$k] == 'group')
			{
				$row['type'] = $value;
			}

			if ($aCampo[$k] == 'ref')
			{
				$aItemsparent[$value] = $value;
				$lAddproduct=false;
				//buscamos al item
				if (!empty($value))
				{
					$res = $objItems->fetch (0,$value);
					if ($res==1)
					{
						$lUpdateunit = false;
						if ($objItems->fk_unit<=0) $lUpdateunit=true;
						$row['fk_item'] = $objItems->id;
					}
					elseif($res==0)
					{
						$row['fk_item']=0;
					}
					else
					{
						$error++;
						setEventMessages($objItems->error,$objItems->errors,'errors');
					}
				}
				else
				{
					$error++;
					setEventMessages($langs->trans('Sin valor en ref').' '.$langs->trans('en la linea').' '.$row['id'],null,'errors');
				}
			}
			if ($aCampo[$k] == 'label')
			{
				//$aItemsparent[$value] = $value;
				$lAddproduct=false;
				//buscamos al item
				//echo '<hr>value '.$value;
				if (!empty($value))
				{
					$filtertmp= " AND t.detail = '".trim($value)."'";
					$restmp = $objItems->fetchAll('','',0,0,array(),'AND',$filtertmp,true);
					if ($restmp == 1)
					{
						$lUpdateunit=false;
						$fk_item = $objItems->id;
						$row['fk_item'] = $objItems->id;
					}
					elseif($restmp==0)
					{
						$row['fk_item']=0;
					}
					else
					{
						$error++;
						setEventMessages($objItems->error,$objItems->errors,'errors');
					}
				}
				else
				{
					$error++;
					setEventMessages($langs->trans('Sin valor en ref').' '.$langs->trans('en la linea').' '.$row['id'],null,'errors');
				}
			}

			if ($aCampo[$k]=='unit')
			{
				//buscamos
				$resunit = $objCunits->fetch(0,$value);
				if ($resunit==1) $row['fk_unit'] = $objCunits->id;
				elseif($resunit==0) $row['fk_unit'] =0;
				else
				{
					$error++;
					setEventMessages($objCunits->error,$objCunits->errors,'errors');
				}
			}
			if ($aCampo[$k] == 'hilo')
			{
				//verificamos si tiene valor debe estar dentro de la lista de aItemsparent
				if (!empty($value) && !$aItemsparent[$value])
				{
					$error++;
					setEventMessages($langs->trans('No esta definido el superior').' '.$value,null,'errors');
				}
			}
			if ($aCampo[$k] == 'tipo')
			{
				//buscamos tipo item en c_type_item
				if (!empty($value))
				{

					$filter = " AND (";
					$filter.= " t.code='".dol_strtoupper(trim($value))."'";
					$filter.= " OR t.code='".strtolower(trim($value))."'";
					$filter.= " OR t.code='".ucfirst(trim($value))."'";
					$filter.= ")";
					$res = $objCtypeitem->fetchAll('','',0,0,array(),'AND',$filter,true);
					if ($res==1)
					{
						$row['fk_type_item'] = $objCtypeitem->id;
					}
					elseif($res==0)
					{
						$row['fk_type_item']=0;
						$error++;
						setEventMessages($langs->trans('no existe el tipo item').' '.$value.' '.$langs->trans('en la linea').' '.$row['id'],null,'errors');
					}
					else
					{
						$error++;
						setEventMessages($objCtypeitem->error,$objCtypeitem->errors,'errors');
					}
				}
				else
				{
					$error++;
					setEventMessages($langs->trans('Sin valor en tipo motor').' '.$langs->trans('en la linea').' '.$row['id'].' '.$row['descripcion'],null,'errors');
				}
			}
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

		$_SESSION['aArraylicence'] = serialize($aArray);
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
	print '<input type="hidden" name="selrow" value="1">';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">';
	print $langs->trans('Numberpage');
	print '</td>';
	print '<td>';
	print '<input type="number" min="1" max="20" name="hoja" value="'.GETPOST('hoja').'">';
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
