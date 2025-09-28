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
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockprogramext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockprogramdet.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotext.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/ctypemouvement.class.php';


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

$langs->load("almacen");

$action=GETPOST('action');

$id         = GETPOST("rowid");
$rid        = GETPOST("rid");
$fk_period  = GETPOST("fk_period");
$fk_concept = GETPOST("fk_concept");
$docum      = GETPOST('docum');
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
$table = GETPOST('table');
$typeformat = GETPOST('typeformat');
$fk_type_mouvement = GETPOST('fk_type_mouvement');
$date_program = dol_mktime(0,0,0,GETPOST('dp_month'),GETPOST('dp_day'),GETPOST('dp_year'));
if (empty($date_program)) $date_program = dol_now();
$mesg = '';
if (!$user->rights->almacen->program)
{
	accessforbidden();
}
$objUser  = new User($db);
$objStockprogram = new Stockprogramext($db);
$objEntrepot = new Entrepotext($db);
$objProduct = new Product($db);
$objType = new Ctypemouvement($db);
$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');
$aCampodate = array('date_commande' =>'date_commande',
	'date_livraison' => 'date_livraison');

//params docum
/*
 1 = Id
 2 = Login
 3 = Docum
*/

 $seq = 1;
 for($i=65; $i<=90; $i++) {
 	$letra = chr($i);
 	$aHeader[$seq]= $letra;
 	$seq++;
 }

/*
 * Actions
 */

// AddSave
if ($action == 'addSave')
{
	$error = 0;
	$now = dol_now();
	$aArrData   = $_SESSION['aArrDatap'];
	$aHeaders = $_SESSION['aHeadersp'];

	$aHeadersid = $_SESSION['aHeadersidp'];
	$nLoop = count($aHeadersid);
	$aCampo = array();
	$llaveid = '';
	$llaveref = '';
	$lEntity = false;
	$table = GETPOST('table');
	$infotable = $db->DDLInfoTable($table);
	$aCampo = array();
	$aCampolabel = array();

	foreach ($infotable AS $i => $dat)
	{
		$aCampo[$i] = $dat[0];
		$aCampolabel[$dat[0]] = $i;
	}

	$selrow = GETPOST('selrow');
	foreach ($_POST AS $i => $value)
	{
		$aPost = explode('__',$i);
		if ($aPost[0] == 'fkcampo')
		{
			$_POST['campo'][$aPost[1]] = $aCampo[$value];
			if (trim($aCampo[$value]) == 'rowid') $llaveid = $aPost[1];
			if (trim($aCampo[$value]) == 'ref') $llaveref = $aPost[1];
			if (trim($aCampo[$value]) == 'entity') $lEntity = true;
		}
	}
	//echo '<hr>res id '.$llaveid.' ref '.$llaveref.' ent '.$lEntity;
	switch ($table)
	{
		case 'llx_stock_program':
		require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockprogram.class.php';
		require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockprogramdet.class.php';
		break;
	}
	//listamos los campos date
	$aListdate = explode(';',$camposdate);
	$aList = array();
	foreach((array) $aListdate AS $j => $value)
		$aList[$value] = $value;
	$db->begin();
	$now = dol_now();
	$aFather = array();
	$b =1;
	$aCampo = GETPOST('campo');

	//creamos la tabla cabecera
	$object = new Stockprogramext($db);
	//buscamos el ultimo numero
	$aDate = dol_getdate($date_program);
	$ref = $object->getNextValue($aDate['year']);

	$object->fk_entrepot = GETPOST('fk_entrepot');
	$object->datep = $date_program;
	$object->fk_type_movement = GETPOST('fk_type_mouvement');
	$object->label = GETPOST('label');
	$object->entity = $conf->entity;
	$object->ref = $ref;
	$object->fk_user_create = $user->id;
	$object->fk_user_mod = $user->id;
	$object->datec = $now;
	$object->datem = $now;
	$object->tms = $now;
	$object->status_print = 0;
	$object->status = 0;
	$resid = $object->create($user);

	$objectdet = new Stockprogramdet($db);
	if($resid<=0)
	{
		$error++;
		setEventMessages($object->error,$object->errors,'errors');
	}
	if (!$error)
	{
		foreach ((array) $aArrData AS $i => $data)
		{
			if (!$error)
			{
				foreach ($aHeadersid AS $campo => $fk_entrepot_end)
				{
					if ($data[$campo]>0)
					{
						$objectdet->fk_stock_program = $resid;
						$objectdet->fk_product = $data[1];
						$objectdet->fk_entrepot_end = $fk_entrepot_end;
						$objectdet->qty = $data[$campo];
						$objectdet->fk_user_create = $user->id;
						$objectdet->fk_user_mod = $user->id;
						$objectdet->datec = $now;
						$objectdet->datem = $now;
						$objectdet->tms = $now;
						$objectdet->status = 0;
						$resnew = $objectdet->create($user);
						if ($resnew <=0)
						{
							$error++;
							setEventMessages($objectdet->error,$objectdet->errors,'errors');
						}
					}
				}
			}
		}
	}
	if (!$error)
	{
		setEventMessages($langs->trans('Successfullupload'),null,'mesgs');
		$db->commit();
		header('Location: '.DOL_URL_ROOT.'/almacen/program/card.php?id='.$resid);
		exit;
	}
	else
	{
		$db->rollback();
		$action = 'create';
	}
}

$aHeaderTpl['llx_stock_program'] = array('ref' => 'ref',
);

// Add
if ($action == 'add')
{
	$table = GETPOST('table');
	$selrow = GETPOST('selrow');
	$tempdir = DOL_DOCUMENT_ROOT."/documents/tmp/";

	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];

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


	$nTipeSheet = substr($nombre_archivo,-3);


	$objPHPExcel = new PHPExcel();
	$type = '';
	if ($nTipeSheet =='lsx')
	{
		$type = 'spreedsheat';
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	}
	elseif ($nTipeSheet =='xls')
	{
		$type = 'spreedsheat';
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	}
	elseif ($nTipeSheet =='csv')
	{
		$type = 'csv';
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	}
	else
	{
		echo "Documento no valido verifique que sea el correcto para la importacion";
		print "<a href=".DOL_URL_ROOT."/eva/import_eva.php>Volver</a>";
		exit;
	}

	if ($type == 'spreedsheat')
	{
		$objPHPExcel = $objReader->load($tempdir.$nombre_archivo);
		$objReader->setReadDataOnly(true);

		$nOk = 0;
		$nLoop = 26;
		$nLine=1;
		if ($selrow)
		{
			for ($a = 1; $a <= $nLoop; $a++)
			{
				$dato = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$nLine)->getValue();
				if (!empty($dato))
				{
					if ($a>1)
					{
						//vamos a verificar si el almacen existe y esta activo
						$resent = $objEntrepot->fetch(0,trim($dato));
						if ($resent>0)
						{
							$aHeadersid[$a] = $objEntrepot->id;
							if ($objEntrepot->id == $fk_entrepot)
							{
								$error++;
								setEventMessages($langs->trans('Thesourcewarehouseislocatedinthedestinationwarehouses'),null,'errors');
							}
						}
						else
						{
							$error++;
							setEventMessages($langs->trans('Thereisnowarehouse').' '.$dato,null,'errors');
						}
					}
					$aHeaders[$a]=$dato;
				}
			}
			$nLine++;
		}

		$lLoop = true;
		$i = 0;
		while ($lLoop == true)
		{
			if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeader[1].$nLine)->getValue()))
			{
				for ($a = 1; $a <= $nLoop; $a++)
				{
					$aCampo = explode(',',$aHeaders[$a]);
					if ($aCampo[0] == 'FECHA')
					{
						$dato = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$nLine)->getFormattedValue();
						$dato = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$nLine)->getValue()+1;
						$timestamp = PHPExcel_Shared_Date::ExcelToPHP($dato);
						$dato = $timestamp;
					}
					else
						$dato = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$nLine)->getValue();
					$aDetalle[$i][$a]=$dato;
				}
				$i++;
			}
			elseif(empty($objPHPExcel->getActiveSheet()->getCell($aHeader[1].$nLine)->getFormattedValue()))
			{
				$lLoop = false;
			}
			$nLine++;
		}
	}
	elseif ($type == 'csv')
	{
		$csvfile = $tempdir.$nombre_archivo;

		$fh = fopen($csvfile, 'r');
		$headers = fgetcsv($fh);
		$aHeaders = explode($separator,$headers[0]);
		$data = array();
		$aData = array();
		while (! feof($fh))
		{
			$row = fgetcsv($fh,'','^');
			if (!empty($row))
			{
				$aData = explode($separator,$row[0]);
				$obj = new stdClass;
				if (!is_object($objheader))
					$objheader = new stdClass;
				$obj->none = "";
				foreach ($aData as $i => $value)
				{
					$key = $aHeaders[$i];
					if (!empty($key))
					{
						$obj->$key = $value;
						$objheader->$i = $key;
					}
					else
						$obj->none = $value." xx";
				}
				if (!$selrow)
					$data[] = $aData;
				else
					$data[] = $obj;
			}
		}

		fclose($fh);
	}
	$c=0;
	$action = "edit";
}




if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}
//campos principales tabla


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

$aTable = array(
	'llx_c_assets_group' => $langs->trans('Groupassets'),
	'llx_assets'=>$langs->trans('Assets'),
	'llx_c_low'=>$langs->trans('Codelows'),
	//'llx_c_clasfin'=>$langs->trans('Institutionalclassifier'),
	//'llx_assets_balance' => $langs->trans('Balanceassets'),
	//'llx_assets_mov' => $langs->trans('Revaluation'),
	'llx_assets_low' => $langs->trans('Assetsinlowstatus'),
);
$aCampolabelsigep['llx_assets_mov'] = array('CODIGO'=>'fk_asset', 'COSTO'=>'coste','DEPACU_ANT'=>'amount_depr_acum_update','D_REVAL'=>'date_reval_day','M_REVAL'=>'date_reval_month','A_REVAL'=>'date_reval_year','VIDAUTIL'=>'useful_life','RESOLUCION'=>'doc_reval','OBSERV'=>'detail','DEPACU'=>'amount_depr_acum','ACTUA'=>'amount_update','DEPGESTION'=>'amount_depr');
$aCampolabelsigep['llx_assets'] = array(1=> array('CODIGO'=>'ref_ext','CODCONT'=>'codcont','CODBAJA'=>'fk_low','CODAUX'=>'codaux','VIDAUTIL'=>'useful_life','DESCRIP'=>'descrip', 'COSTO'=>'coste','DEPACU'=>'dep_acum','MES'=>'date_month','ANO'=>'date_year','DIA'=>'date_day','CODOFIC'=>'fk_departament','CODRESP'=>'fk_resp','OBSERV'=>'detail','COD_RUBE'=>'cod_rube','ORG_FIN'=>'orgfin','B_REV'=>'status_reval','NOMOFIC'=>'departament_name','NOMRESP'=>'resp_name'),2=>array());

$aCampolabelsigep['llx_assets_low'] = array(1=> array('CODIGO'=>'ref_ext','CODCONT'=>'codcont','CODBAJA'=>'fk_low','CODAUX'=>'codaux','VIDAUTIL'=>'useful_life','DESCRIP'=>'descrip', 'COSTO'=>'coste','DEPACU'=>'dep_acum','MES'=>'date_month','ANO'=>'date_year','DIA'=>'date_day','CODOFIC'=>'fk_departament','CODRESP'=>'fk_resp','OBSERV'=>'detail','COD_RUBE'=>'cod_rube','ORG_FIN'=>'orgfin','B_REV'=>'status_reval','NOMOFIC'=>'departament_name','NOMRESP'=>'resp_name','D_BAJA'=>'baja_day','M_BAJA'=>'baja_month','A_BAJA'=>'baja_year','RESOLUCION'=>'baja_resolution','OBSERV'=>'baja_observation','MOTIVO'=>'baja_motive','ACTUA'=>'baja_amount_act','DEPGESTION'=>'baja_amount_depgestion'),2=>array());
$aCampolabelsigep['llx_c_assets_group'] = array('CODCONT'=>'code', 'NOMBRE'=>'label','VIDAUTIL'=>'useful_life','OBSERV'=>'description','DEPRECIAR'=>'depreciate','ACTUALIZAR'=>'toupdate',);
$aCampolabelsigep['llx_c_low'] = array('CODBAJA'=>'ref', 'DESCBAJA'=>'label',);

//$action = "create";

$filterstatic = " AND t.type = 'T'";
$res = $objType->fetchAll('ASC', 't.label', 0, 0, array(1=>1),'AND',$filterstatic);
$options = '';
if ($res>0)
{
	foreach ($objType->lines AS $j => $line)
	{
		$options.= '<option value="'.$line->id.'" '.(GETPOST('fk_type_mouvement') == $line->id?'selected':'').'>'.$line->label.'</option>';
	}
}

/*
 * View
 */

$form=new Form($db);
$formproduct=new FormProduct($db);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Uploadarchive"),$help_url);

if ($action == 'create' || empty($action) && $user->rights->almacen->upload->write)
{
	print_fiche_titre($langs->trans("Uploadarchive"));

	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript" language="javascript">';
		print '$(document).ready(function () {
			$("#table").change(function() {
				document.upload.action.value="create";
				document.upload.submit();
			});
		})';
		print '</script>'."\n";
	}

	print '<form action="'.$_SERVER['PHP_SELF'].'" name="upload" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="table" value="llx_stock_program">';

	dol_fiche_head();
	print '<table class="border" width="100%">';

	print '<tr><td width="20%">';
	print $langs->trans('Fromstorage');
	print '</td>';
	print '<td>';
	print $formproduct->selectWarehouses(GETPOST('fk_entrepot'), 'fk_entrepot', 'warehouseopen,warehouseinternal', 1, 0, 0, '', 0, 0, array(), 'minwidth200imp');
	print '</td></tr>';
	print '<tr><td width="20%">';
	print $langs->trans('Dateprogram');
	print '</td>';
	print '<td>';
	print $form->select_date($date_program, 'dp', 0,0,1);
	print '</td></tr>';

	print '<tr><td width="20%">';
	print $langs->trans('Type');
	print '</td>';
	print '<td>';
	print '<select name="fk_type_mouvement">'.$options.'</select>';
	print '</td></tr>';

	print '<tr><td width="20%">';
	print $langs->trans('Selectarchiv');
	print '</td>';
	print '<td>';
	print '<input type="file" name="archivo" size="40">';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Firstrowistitle');
	print '</td>';
	print '<td>';
	print $form->selectyesno('selrow',(GETPOST('selrow')?GETPOST('selrow'):1),1);
	print '</td></tr>';

	print '</table>';
	dol_fiche_end();
	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Upload").'"></center>';

	print '</form>';
}
else
{
	if ($action == 'exit')
	{
		print_barre_liste($langs->trans("Subida de archivo exitoso"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);
		print '<table class="noborder" width="100%">';
	 //encabezado
		print '<tr class="liste_titre">';

		print '</tr>';
		print '</table>';
	}
	else
	{
		print_barre_liste($langs->trans("Uploadarchive"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);
		//print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<form action="'.$_SERVER['PHP_SELF'].'" name="upload" method="post" enctype="multipart/form-data">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="addSave">';
		print '<input type="hidden" name="table" value="'.$table.'">';
		print '<input type="hidden" name="seldate" value="'.$seldate.'">';
		print '<input type="hidden" name="camposdate" value="'.$camposdate.'">';
		print '<input type="hidden" name="separator" value="'.$separator.'">';
		print '<input type="hidden" name="selrow" value="'.$selrow.'">';
		print '<input type="hidden" name="typeformat" value="'.$typeformat.'">';
		print '<input type="hidden" name="date_dep" value="'.$date_dep.'">';

		dol_fiche_head();
		print '<table class="noborder" width="100%">';


		//encabezado
		foreach($aHeaders AS $i => $value)
		{
			$aHeadersOr[trim($value)] = trim($value);
		}
		$aValHeader = array();
		foreach($aHeaderTpl[$table] AS $i => $value)
		{
			if (!$aHeadersOr[trim($value)])
				$aValHeader[$value] = $value;
		}
		print '<tr class="liste_titre">';
		if ($selrow)
		{

			foreach($aHeaders AS $i => $value)
			{
				$value = "'".$value."'";
				print_liste_field_titre($langs->trans($value),'fiche.php','','','','');
			}
		}
		print '</tr>';

		$lSave = true;
		$var=True;
		$c = 0;
		if ($selrow)
		{
			foreach((array) $aDetalle AS $j => $data)
			{
				$var=!$var;
				print "<tr $bc[$var]>";
				$c++;
				$fk_departament = 0;
				$fk_resp = 0;
				foreach($aHeaders AS $i => $keyname)
				{
					$phonelabel = '';
					$aKey = explode(',',$keyname);
					if (empty($keyname))
						$keyname = "none";
					$phone = $data[$i];

					if (dol_strtoupper($aKey[0])=='REF')
					{
						$res = $objProduct->fetch(0,$phone);
						if ($res>0)
							$aArrData[$c][$i] = $objProduct->id;
						else
						{
							setEventMessages($langs->trans('Noexistproduct').' '.$phone,null,'errors');
							$error++;
						}
						$phonelabel = $objProduct->label;
					}
					else
						$aArrData[$c][$i] = $phone;
					print '<td>'.$phone.($phonelabel?' - '.$phonelabel:'').'</td>';
				}
				print '</tr>';

			}
		}
		else
		{
			foreach($data AS $key => $dataval)
			{
				$var=!$var;
				print "<tr $bc[$var]>";
				$c++;
				foreach($aHeaders AS $i => $keyname)
				{
					$value = $dataval[$i];
					$aArrData[$c][$i] = $value;
					print '<td>'.$value.'</td>';
				}
				print '</tr>';
			}
		}

		//}
		print '</table>';
		dol_fiche_end();

		dol_fiche_head();
		print '<table>';
		print '<tr><td width="20%">';
		print $langs->trans('Fromstorage');
		print '</td>';
		print '<td>';
		print $formproduct->selectWarehouses(GETPOST('fk_entrepot'), 'fk_entrepot', 'warehouseopen,warehouseinternal', 1, 0, 0, '', 0, 0, array(), 'minwidth200imp');
		print '</td></tr>';
		print '<tr><td>';
		print $langs->trans('Dateprogram');
		print '</td>';
		print '<td>';
		print $form->select_date($date_program, 'dp', 0,0,1);
		print '</td></tr>';
		print '<tr><td>';
		print $langs->trans('Type');
		print '</td>';
		print '<td>';
		print '<select name="fk_type_mouvement">'.$options.'</select>';
		print '</td></tr>';

		if ($error)
		{
			print '<tr><td width="20%">';
			print $langs->trans('Selectarchiv');
			print '</td>';
			print '<td>';
			print '<input type="file" name="archivo" size="40">';
			print '</td></tr>';
		}
		print '</table>';
		dol_fiche_end();
		if(!$error && count($aDetalle)>0)
		{
			$_SESSION['aHeadersp'] = $aHeaders;
			$_SESSION['aHeadersidp'] = $aHeadersid;
			$_SESSION['aArrDatap'] = $aArrData;
			print '<input type="hidden" name="action" value="addSave">';


			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';

		}
		else
		{
			print '<input type="hidden" name="action" value="add">';
			print '<input type="hidden" name="table" value="llx_stock_program">';
			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Upload").'"></center>';

		}
		print '</form>';
				 //validando el encabezado
	}
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
?>
