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
 *	\file       htdocs/salary/upload/fiche.php
 *	\ingroup    salary subida archivos
 *	\brief      Page fiche upload
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';

require_once DOL_DOCUMENT_ROOT.'/orgman/class/cregiongeographic.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/caltitude.class.php';
require_once DOL_DOCUMENT_ROOT.'/productext/class/productregionprice.class.php';

require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

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


$langs->load("productext@productext");
$langs->load("members");

$action=GETPOST('action');

$id         = GETPOST("rowid");
$rid        = GETPOST("rid");
$fk_period  = GETPOST("fk_period");
$fk_concept = GETPOST("fk_concept");
$fk_entrepot = GETPOST("fk_entrepot");
$docum      = GETPOST('docum');
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
$selrow = GETPOST('selrow');
$cancel = GETPOST('cancel');
$mesg = '';

$objUser  = new User($db);

$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');
$aCampodate = array('date_commande' =>'date_commande',
	'date_livraison' => 'date_livraison');

$object = new Productregionprice($db);
$objCregiongeographic = new Cregiongeographic($db);
$objCaltitude = new Caltitude($db);
$objProduct = new Product($db);
$objCateg = new Categorie($db);

$aDate = dol_getdate(dol_now());

//params docum
/*
 1 = Id
 2 = Login
 3 = Docum
*/

 $aMonth = array(2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11,13=>12);
 $aHeader = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W');

 $aCampo = array(1=>'id',2=>'region',3=>'altitud',4=>'producto',5=>'cantidad',6=>'precio',7=>'fk_region_geographic',8=>'fk_altitude',9=>'fk_product');
 $aCamporef = array('id'=>1,'region'=>2,'altitud'=>3,'producto'=>4,'cantidad'=>5,'precio'=>6,'fk_region_geographic'=>7,'fk_altitude'=>8,'fk_product'=>9);
 $aCampotab = array('id'=>1,'region'=>2,'altitud'=>3,'producto'=>4,'cantidad'=>'quantity','precio'=>'price','fk_region_geographic'=>'fk_region_geographic','fk_altitude'=>'fk_altitude','fk_product'=>'fk_product');

 $aTypemov['INV. INICIO'] = getpost('code_inv');
 $aTypemov['INGRESO'] = getpost('code_ing');
 $aTypemov['SALIDA'] = getpost('code_sal');


/*
 * Actions
 */

// AddSave
if ($action == 'add' && GETPOST('save') == $langs->trans('Save'))
{
	/*verificamos los tipos*/
	$error = 0;
	$now = dol_now();
	$aArrData   = unserialize($_SESSION['importmov']);
	$llaveid = '';
	$llaveref = '';
	$lEntity = false;
	$fk_entrepot = GETPOST('fk_entrepot');
	foreach ($_POST AS $i => $value)
	{
		$aPost = explode('_',$i);
		if ($aPost[0] == 'fkcampo')
		{
			$_POST['campo'][$aPost[1]] = $aCampo[$value];
			if (trim($aCampo[$value]) == 'rowid') $llaveid = $aPost[1];
			if (trim($aCampo[$value]) == 'ref') $llaveref = $aPost[1];
			if (trim($aCampo[$value]) == 'entity') $lEntity = true;

		}
	}
	if (!$error)
	{
		$db->begin();
		foreach ((array) $aArrData AS $j => $data)
		{
			if (!$error)
			{
				foreach ($aCampotab AS $campo => $labelfield)
				{
					if (!empty($data[$campo]))
					{
						$object->$labelfield=$data[$campo];
					}
				}
				$object->entity = $conf->entity;
				$object->info_bits = 0;
				$object->fk_user=$user->id;
				$object->import_key=$data['id'];
				$object->fk_user_create=$user->id;
				$object->fk_user_mod=$user->id;
				$object->datec=$now;
				$object->datem=$now;
				$object->tms=$now;
				$object->status=1;
				$result=$object->create($user);
				if ($result<=0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
		}
		if (!$error)
		{
			setEventMessages($langs->trans('Satisfactoryimportprocess'),null,'mesgs');
			$db->commit();
			header('Location: '.$_SERVER['PHP_SELF']);
		}
		else
		{
			setEventMessages($langs->trans('La importación tiene errores, revise').' '.$error,null,'errors');
			$db->rollback();
		}
		$action = '';
	}
}






if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}
//campos principales tabla
$aHeaderTpl['llx_accounting_account'] = array('fk_pcg_version' => 'fk_pcg_version',
	'pcg_type' => 'pcg_type',
	'pcg_subtype' => 'pcg_subtype',
	'account_number'=>'account_number',
	'account_parent' => 'account_parent',
	'label'=>'label',
	'fk_accouting_category'=>'fk_accouting_category',
);
$aHeaderTpl['llx_product'] = array('ref' => 'ref',
	'label' => 'label');
$aHeaderTpl['llx_categorie'] = array('label' => 'label',
	'description' => 'description',
	'code_parent' => 'code_parent');
$aHeaderTpl['llx_categorie_product'] = array('code_product' => 'code_product',
	'description' => 'description',
	'code_categorie' => 'code_categorie');
$aHeaderTpl['llx_commande'] = array('ref' => 'ref',
	'fk_soc' => 'fk_soc',
	'date_commande' => 'date_commande');
$aHeaderTpl['llx_commandedet'] = array('fk_commande' => 'fk_commande',
	'fk_product' => 'fk_product',
	'qty' => 'qty');

$aHeaderTpl['llx_c_departements'] = array('code_departement' => 'code_departement',
	'fk_region' => 'fk_region',
	'nom' => 'nom');

$aHeaderTpl['llx_c_partida'] = array('gestion'=>'gestion',
	'code'   => 'code',
	'label'  => 'label',
	'active' => 'active');
$aHeaderTpl['llx_poa_poa'] = array('gestion'=>'gestion',
	'fk_structure' =>'fk_structure',
	'ref' =>'ref',
	'label'=>'label',
	'pseudonym' =>'pseudonym',
	'partida'=>'partida',
	'amount'=>'amount',
	'classification'=>'classification',
	'source_verification'=>'source_verification',
	'unit'=>'unit',
	'responsible'=>'responsible',
	'm_jan'=>'m_jan',
	'm_feb'=>'m_feb',
	'm_mar'=>'m_mar',
	'm_apr'=>'m_apr',
	'm_may'=>'m_may',
	'm_jun'=>'m_jun',
	'm_jul'=>'m_jul',
	'm_aug'=>'m_aug',
	'm_sep'=>'m_sep',
	'm_oct'=>'m_oct',
	'm_nov'=>'m_nov',
	'm_dec'=>'m_dec',
	'p_jan'=>'p_jan',
	'p_feb'=>'p_feb',
	'p_mar'=>'p_mar',
	'p_apr'=>'p_apr',
	'p_may'=>'p_may',
	'p_jun'=>'p_jun',
	'p_jul'=>'p_jul',
	'p_aug'=>'p_aug',
	'p_sep'=>'p_sep',
	'p_oct'=>'p_oct',
	'p_nov'=>'p_nov',
	'p_dec'=>'p_dec',
	'fk_area'=>'fk_area',
	'weighting'=>'weighting',
	'fk_poa_reformulated'=>'fk_poa_reformulated',
	'version'=>'version',
	'statut'=>'statut',
	'statut_ref'=>'statut_ref',
	'active'=>'active',
);

$aTable = array(
	'llx_accounting_account'=>'Accountingaccount',
	'llx_categorie'   => 'Category',
	'llx_product'     => 'Product',
	'llx_categorie_product' => 'Category product',
	'llx_commande'    => 'Pedidos',
	'llx_commandedet' => 'Pedidos productos',
	'llx_c_departements' => 'Departamentos/Provincias',
	'llx_c_partida'   => 'Partidas de Gasto',
	'llx_poa_poa'     => 'Poa');

//$action = "create";

/*
 * View
 */

$form=new Form($db);
$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Importmovements"),$help_url);

// Add
if ($action == 'edit')
{
	$table = GETPOST('table');
	$selrow = GETPOST('selrow');
	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];
	$tempdir = $conf->productext->dir_output.'/tmp';
	if (! file_exists($tempdir))
	{
		if (dol_mkdir($tempdir) < 0)
		{
			setEventMessages($langs->transnoentities("ErrorCanNotCreateDir",$dir),null,'errors');
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
		echo 'no se puede mover';
		exit;
	}

	$objPHPExcel = new PHPExcel();
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');

	$objPHPExcel = $objReader->load($tempdir.$nombre_archivo);

	//$objReader = new PHPExcel_Reader_Excel2007();
	$objReader->setReadDataOnly(true);
	//$objPHPExcel = $objReader->load('C:\xampp\htdocs\hotelplayagolfsitges\wp-content\tesipro.xlsx');
	$aCurren = array();

	$line=0;
	if ($selrow == 1)
	{
		$line++;
		if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeader[1].$line)->getFormattedValue()))
		{
			for ($a = 1; $a <= 22; $a++)
				$aCurrentitle[$line][$aHeader[$a]] = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getFormattedValue();
		}
	}
	$line++;
	$lLoop = true;
	while ($lLoop == true)
	{
		if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeader[2].$line)->getFormattedValue()))
		{
			for ($a = 1; $a <= 21; $a++)
			{
				if ($a == 9)
				{
					$aCurren[$line][$a] = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getValue()+1;
					$timestamp = PHPExcel_Shared_Date::ExcelToPHP($aCurren[$line][$a]);
					$aCurren[$line][$a] = $timestamp;
				}
				else
					$aCurren[$line][$a] = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getFormattedValue();

			}
		}
		else $lLoop = false;
		$line++;
	}
	//corregimos si se encuentra una coma como separador de decimales
	//cabecera
	//print '<table class="noborder" width="100%">';
	//print '<tr class="liste_titre">';
	//foreach ((array) $aCurrentitle AS $j => $data)
	//{
	//	for ($a = 1; $a <= 9; $a++)
	//		print '<th>'.$data[$aHeader[$a]].'</th>';
	//}
	//print '<th align="right">'.$langs->trans('Total').'</th>';
	//print '<tr>';
	//revisamos valores
	$i = 0;
	$j = 0;
	$refni = '';
	$aNewdata = array();

	foreach ($aCurren AS $line => $aData)
	{
		$var =!$var;
		//print '<tr '.$bc[$var].'>';
		$aLines = array();
		for ($a = 1; $a <= 6; $a++)
		{
			$aLines[$a] = $aData[$a];
		}
		$aNewdata[] = $aLines;
		$j++;
		//print '</tr>';

	}
	//print '</table>';
	dol_fiche_end();
	//armamos un nuevo array segun agrupamiento
	foreach ((array) $aNewdata AS $j => $data)
	{
		$row = array();
		$lAddunit = false;
		$lAddproduct = false;
		foreach ($data AS $k => $value)
		{
			$row[$aCampo[$k]] = $value;
			if ($aCampo[$k] == 'region')
			{
				//buscamos la region
				$res = $objCregiongeographic->fetch(0,dol_string_nospecial(trim($value)));
				if ($res == 1)
				{
					$row['fk_region_geographic'] = $objCregiongeographic->id;
				}
				elseif($res == 0)
				{
					$error++;
					setEventMessages($langs->trans('Thereisnoregion').' '.$value,null,'errors');
				}
			}
			if ($aCampo[$k] == 'altitud')
			{
				//buscamos la altitud
				$res = $objCaltitude->fetch(0,dol_string_nospecial(trim($value)));
				if ($res == 1)
				{
					$row['fk_altitude'] = $objCaltitude->id;
				}
				elseif($res == 0)
				{
					$error++;
					setEventMessages($langs->trans('Thereisnoaltitude').' '.$value,null,'errors');
				}
			}
			if ($aCampo[$k] == 'producto')
			{
				//buscamos la altitud
				$res = $objProduct->fetch(0,dol_string_nospecial(trim($value)));
				if ($res == 1)
				{
					$row['fk_product'] = $objProduct->id;
				}
				elseif($res == 0)
				{
					$error++;
					setEventMessages($langs->trans('Thereisnoproduct').' '.$value,null,'errors');
				}
			}
		}

		//verificamos categoria
		//$errorv = verifica_categoria($row['grupo'],$row['fk_product'], $db);
		//if ($errorv<0) $error++;
		$aNew[$j] = $row;
		//verificacion de cantidades
	}
	ksort($aNew);


	print  '<table class="border" width="100%">';
	print '<tr>';
	foreach ($aCampo AS $i => $label)
	{
		print '<td>'.$label.'</td>';
	}
	print '</tr>';

	foreach ((array) $aNew AS $j => $data)
	{
		print '<tr>';
		foreach ($aCamporef AS $label => $i)
		{
			print '<td>'.$data[$label].'</td>';
		}
		print '</tr>';
	}

	print '</table>';
	echo '<br>Errores encontrados '.$error;

	if (!$error)
	{
		$_SESSION['importmov'] = serialize($aNew);
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="step" value="'.$stepnew.'">';
		print '<input type="hidden" name="fk_entrepot" value="'.$fk_entrepot.'">';
		print '<input type="hidden" name="code_inv" value="'.GETPOST('code_inv').'">';
		print '<input type="hidden" name="code_ing" value="'.GETPOST('code_ing').'">';
		print '<input type="hidden" name="code_sal" value="'.GETPOST('code_sal').'">';

		print '<center><br><input type="submit" class="butAction" name="save" value="'.$langs->trans("Save").'">';
		print '&nbsp;<input type="submit" class="butActionDelete" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
		print '</form>';
	}

	$c=0;
}
if ($action == 'create' || empty($action))
{
	print_fiche_titre($langs->trans("Importprice"));
	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">';

	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="edit">';

	dol_htmloutput_mesg($mesg);


	print '<table class="border" width="100%">';

	print '<tr><td>';
	print $langs->trans('Selectarchiv');
	print '</td>';
	print '<td>';
	print '<input type="file" name="archivo" size="40">';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Firstrowmustbetitle');
	print '</td>';
	print '<td>';
	print $langs->trans('Yes');
	print '<input type="hidden" name="selrow" value="1">';
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Upload").'"></center>';

	print '</form>';

	print '<br>';
	$file = 'exampleprice.xlsx';
	print '<a href="'.$file.'" target="_blank">'.$langs->trans('Fieldexample').'</a>';
}

llxFooter();
$db->close();

function convertdate($aDatef,$selvalue,$date)
{
	$sel = $aDatef[$selvalue];
	switch ($sel)
	{
		case 0:
		list($day,$mes,$anio) = explode('/',$date);
		break;
		case 0:
		list($day,$mes,$anio) = explode('-',$date);
		break;
		case 0:
		list($mes,$day,$anio) = explode('/',$date);
		break;
		case 0:
		list($mes,$day,$anio) = explode('-',$date);
		break;
		case 0:
		list($anio,$mes,$day) = explode('/',$date);
		break;
		case 0:
		list($anio,$mes,$day) = explode('-',$date);
		break;
	}
	$newdate = dol_mktime(12, 0, 0, $mes, $day, $anio);
	return $newdate;
}

function verifica_categoria($categorie,$fk_product, $db)
{
	global $langs,$conf;
	//verificamos la categoria
	$newobject = new Product($db);
	$objCateg = new Categorie($db);
	$result = $newobject->fetch($fk_product);
	$elementtype = 'product';
						//buscamos la categoria
	$rescat = $objCateg->fetch(0,$categorie,0);
	if ($rescat>0)
	{
		$rescat = $objCateg->containsObject('product', $fk_product );
		if ($rescat==0)
		{
								// TODO Add into categ
			$result=$objCateg->add_type($newobject,$elementtype);
			if ($result >= 0)
			{
				setEventMessages($langs->trans("WasAddedSuccessfully",$newobject->ref), null, 'mesgs');
			}
			else
			{
				$error=9001;
				setEventMessages($langs->trans("No se agrego la categoria ",$newobject->ref), null, 'mesgs');
			}
		}
	}
	//fin cateogria
	if (!$error) return 1;
	else $error *-1;
}
?>
