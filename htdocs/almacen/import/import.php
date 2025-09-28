<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/poa/process/fiche_autpay.php
 *	\ingroup    Process export excel authorization payment
 *	\brief      Page fiche poa process export excel
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/lib/expimp.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';


dol_include_once('/almacen/class/solalmacenext.class.php');
dol_include_once('/almacen/class/solalmacendetext.class.php');

dol_include_once('/almacen/class/stockmouvementdocext.class.php');
dol_include_once('/almacen/class/stockmouvementadde.class.php');
dol_include_once('/almacen/class/mouvementstockext.class.php');
dol_include_once('/almacen/class/entrepotext.class.php');
dol_include_once('/almacen/class/ctypemouvement.class.php');
dol_include_once('/almacen/class/contabperiodoext.class.php');
dol_include_once('/almacen/class/cunitsext.class.php');

if ($conf->orgman->enabled)
	dol_include_once('/orgman/class/pdepartamentext.class.php');
dol_include_once('/societe/class/societe.class.php');
dol_include_once('/product/class/product.class.php');

//excel para la version dol 5 o superior
require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
include_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

$langs->load("almacen");

$action=GETPOST('action');
$step=GETPOST('step','int');
if (empty($step)) $step = 1;
$gestion = $_SESSION['period_year'];

if (!empty($id))
{
	$ida = $_SESSION['aListip'][$id]['idAct'];
	$idp = $_SESSION['aListip'][$id]['idPrev'];
	$idc = $_SESSION['aListip'][$id]['idContrat'];
	$lAnticipo = $_SESSION['aListip'][$id]['anticipo'];
}

if (empty($gestion)) $gestion = date('Y');
$idArea = 3;
//generar funcion para recuperar por usuario

$mesg = '';

$objuser = new User($db);
$object   = new Stockmouvementdocext($db);
$objectdet = new Mouvementstockext($db);
$objType = new Ctypemouvement($db);
$objSociete = new Societe($db);
$objProduct = new Product($db);
$objDepartament = new Pdepartamentext($db);
$objEntrepot = new Entrepotext($db);
$objPeriod = new Contabperiodoext($db);
$objCunit = new Cunitsext($db);

$aOption = array(
	1=>array('label'=>$langs->trans('Note of entry'),'detail'=> $langs->trans('Notas de ingreso /Almacen, Proveedor, Fecha, Productos, unidad, cantidad ')),
	2=>array('label'=> $langs->trans('Note output'),'detail'=>$langs->trans('Notas de salida / Destino, solicitante, fecha, productos, unidad, cantidad solicitada, cantidad entregada')));

/*
 * Actions
 */


if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}
if ($action == 'confirm_add')
{
	$year = GETPOST('year');
	$ref  = GETPOST('ref');
	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];

	$tempdir = "tmp/";
    //compruebo si la extension es correcta

	//if(dol_move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
	//{
	if (dol_move_uploaded_file ($tmp_name,$tempdir.$nombre_archivo,	1))
	{
	//  echo "file uploaded<br>";
	}
	else
	{
		echo 'no se puede mover';
		exit;
	}

	$objPHPExcel = new PHPExcel();
	$objReader = PHPExcel_IOFactory::createReader('Excel5');
	$objPHPExcel = $objReader->load('tmp/'.$nombre_archivo);

	//$objReader = new PHPExcel_Reader_Excel2007();
	$objReader->setReadDataOnly(true);
	//$objPHPExcel = $objReader->load('C:\xampp\htdocs\hotelplayagolfsitges\wp-content\tesipro.xlsx');
	$aCurren = array();
	$aMonth = array(2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11,13=>12);
	$aMonthtex = array(2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M');
	for ($a = 7; $a <= 37; $a++)
	{
		for ($b=2; $b<=13; $b++)
		{
			$aCurren[$aMonth[$b]][$objPHPExcel->getActiveSheet()->getCell('A'.$a)->getFormattedValue()] = $objPHPExcel->getActiveSheet()->getCell($aMonthtex[$b].$a)->getFormattedValue();
		}
	}
	//corregimos si se encuentra una coma como separador de decimales
	$newCurren = array();
	foreach ($aCurren AS $month => $aDay)
	{
		foreach ($aDay AS $day => $value)
		{
			$newvalue = str_replace(",", ".", $value);
			$newCurren[$month][$day] = $newvalue;
		}
	}
	if (count($newCurren)>0)
	{
		if ($aRegistry[$ref])
		{
			$db->begin();
			$new = dol_now();
			foreach ($newCurren AS $month => $aDay)
			{
				foreach ($aDay AS $day => $value)
				{
					if ($value > 0)
					{
						$lAdd = true;
						$date_ind=dol_mktime(12, 0, 0, $month, $day, $year);

						//buscamos si existe el registro diario
						$filter = " AND MONTH(t.date_ind) = ".$month;
						$filter.= " AND YEAR(t.date_ind) = ".$year;
						$filter.= " AND DAY(t.date_ind) = ".$day;
						$filter.= " AND t.ref = '".$ref."'" ;
						$filter.= " AND t.entity = '".$conf->entity."'" ;
						$res = $object->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
						if ($res == 1)
						{
							$lAdd = false;
							$object->amount = $value;
							$object->fk_user_mod = $user->id;
							$object->dateu = $new;
							$res = $object->update($user);
							if ($res <=0)
							{
								$error++;
								setEventMessages($object->error,$object->errors,'errors');
							}
						}
						if ($lAdd)
						{
							$object->entity = $conf->entity;
							$object->ref = $ref;
							$object->date_ind  = $date_ind;
							$object->amount = $value;
							$object->fk_user_create = $user->id;
							$object->fk_user_mod = $user->id;
							$object->datec = $new;
							$object->dateu = $new;
							$object->tms = $new;
							$object->status = 1;
							$res = $object->create($user);
							if ($res <=0)
							{
								$error++;
								setEventMessages($object->error,$object->errors,'errors');
							}
						}
					}
				}
			}
			if (!$error)
			{
				$db->commit();
				setEventMessages($langs->trans('Importacion satisfactoria para la gestión ').' '.$year,null,'mesgs');
				header('Location: '.DOL_URL_ROOT.'/multicurren/exchangerate/list.php');
				exit;
			}
			else
			{
				$db->rollback();
				$action = 'create';
			}
		}
		else
		{
			setEventMessages($langs->trans('Seleccione el tipo de moneda registrada'),null,'errors');
			header('Location: '.DOL_URL_ROOT.'/multicurren/exchangerate/card.php?action=import');
			exit;
		}
	}
}


// print_r($_POST);
// exit;

/*
 * View
 */

$form=new Form($db);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
$morecss = array('/almacen/css/styleexpimp.css');
llxHeader('',$langs->trans("Export/Import"),'','','','',$morejs,$morecss,0,0);

if ($action == 'create' && $step>=1 )
{
	$stepnew = $step+1;
	print_fiche_titre($langs->trans("Upload archive"));
	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	if ($stepnew < 3)
		print '<input type="hidden" name="action" value="create">';
	else
		print '<input type="hidden" name="action" value="edit">';
	print '<input type="hidden" name="step" value="'.$stepnew.'">';
	dol_htmloutput_mesg($mesg);
	$param='&action='.$action;
	$head = import_prepare_head($param, $step);

	dol_fiche_head($head, 'step'.$step, $langs->trans("NewImport"));

	if ($step == 1)
	{
		print '<table class="noborder" width="100%">';
		print '<tr class="liste_titre">';
		print '<th>'.$langs->trans('Module').'</th>';
		print '<th>'.$langs->trans('Conjunto de datos importables').'</th>';
		print '<th>&nbsp;</th>';
		print '</tr>';
		$stepnew = $step+1;
		$var = true;
		foreach ($aOption AS $j => $data)
		{
			$var = !$var;
			print '<tr '.$bc[$var].'><td>';
			print $langs->trans('Almacen');
			print '</td>';
			print '<td>';
			print $data['label'].' '.$data['detail'];
			print '</td>';
			print '<td><a href="'.$_SERVER['PHP_SELF'].'?id='.$j.'&step='.$stepnew.'&action=create">'.img_picto('','filenew').'</a></td>';
			print '</tr>';
		}
		print '</table>';
	}
	elseif($step == 2)
	{
		print '<table class="border" width="100%">';
		print '<tr><td>';
		print $langs->trans('Year');
		print '</td>';
		print '<td>';
		print '<input type="number" name="year"  value"'.$gestion.'" required>';
		print '</td></tr>';

		print '<tr><td>';
		print $langs->trans('Selectarchiv');
		print '</td>';
		print '<td>';
		print '<input type="file" name="archivo" size="40">';
		print '</td></tr>';
		print '</table>';
	}

	dol_fiche_end();
	if ($step == 2)
	{
		print '<center><br><input type="submit" class="button" value="'.$langs->trans("Upload").'"></center>';
	}
	print '</form>';
}

if ($step== 3 && $action == 'edit' )
{

	$param='&action=create';
	$head = import_prepare_head($param, $step);

	dol_fiche_head($head, 'step'.$step, $langs->trans("NewImport"));

	$year = GETPOST('year');
	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];

	$tempdir = "tmp/";
	//compruebo si la extension es correcta

	//if(dol_move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
	//{
	if (dol_move_uploaded_file ($tmp_name,$tempdir.$nombre_archivo,	1))
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
	$objPHPExcel = $objReader->load('tmp/'.$nombre_archivo);

	//$objReader = new PHPExcel_Reader_Excel2007();
	$objReader->setReadDataOnly(true);
	//$objPHPExcel = $objReader->load('C:\xampp\htdocs\hotelplayagolfsitges\wp-content\tesipro.xlsx');
	$aCurren = array();
	$aMonth = array(2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11,13=>12);
	$aHeader = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M');
	$aCampo = array(1=>'refstock',2=>'refentrepot',3=>'type_mov',4=>'datem',5=>'refdepartament',6=>'refsociete',7=>'ref_ext',8=>'label',9=>'refproduct',10=>'labelproduct',11=>'unit',12=>'qty',13=>'price');
	$line = 1;
	if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeader[1].$line)->getFormattedValue()))
	{
		for ($a = 1; $a <= 13; $a++)
			$aCurrentitle[$line][$aHeader[$a]] = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getFormattedValue();
	}

	$line = 2;
	$lLoop = true;
	while ($lLoop == true)
	{
		if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeader[1].$line)->getFormattedValue()))
		{
			for ($a = 1; $a <= 13; $a++)
				$aCurren[$line][$aHeader[$a]] = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getFormattedValue();
		}
		else $lLoop = false;
		$line++;
	}
	//corregimos si se encuentra una coma como separador de decimales
	//cabecera
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	foreach ($aCurrentitle AS $j => $data)
	{
		for ($a = 1; $a <= 13; $a++)
			print '<th>'.$data[$aHeader[$a]].'</th>';
	}
	print '<th align="right">'.$langs->trans('Total').'</th>';
	print '<tr>';
	//revisamos valores
	$i = 0;
	$j = 0;
	$refni = '';
	foreach ($aCurren AS $line => $aData)
	{
		$var =!$var;
		print '<tr '.$bc[$var].'>';
		for ($a = 1; $a <= 13; $a++)
		{
			$mark = '';
			//revisamos la nota de ingreso
			if ($aCampo[$a] == 'refstock')
			{
				$ref = $aData[$aHeader[$a]];
				if ($refni != $ref)
				{
					$refni = $ref;
					$i++;
				}
				$res = $object->fetch(0,$ref);
				if ($res == 0) $mark = '';
				if ($res >0)
				{
					$mark = 'class="tdlineerr"';
					$error++;
				}
				if ($res == 0) $aImport[$i]['ref'] = $ref;
			}
			elseif ($aCampo[$a] == 'refentrepot')
			{
				$ref = $aData[$aHeader[$a]];
				$res = $objEntrepot->fetch(0,$ref);
				if ($res ==1) $mark = '';
				else
				{
					$mark = 'class="tdlineerr"';
					$error++;
				}
				if ($res==1) $aImport[$i]['fk_entrepot_from'] = $objEntrepot->id;

			}
			elseif ($aCampo[$a] == 'type_mov')
			{
				$ref = $aData[$aHeader[$a]];
				$res = $objType->fetchAll('','',0,0,array('active'=>1),'AND'," AND t.label = '".$ref."'",1);
				if ($res ==1) $mark = '';
				else
				{
					$mark = 'class="tdlineerr"';
					$error++;
				}
				if ($res==1) $aImport[$i]['fk_type_mov'] = $objType->id;
			}
			elseif ($aCampo[$a] == 'datem')
			{
				//revisamos si esta habilitado el periodo para ese mes y año
				$date = dol_stringtotime($aData[$aHeader[$a]], 1);
				$aDate = dol_getdate($date);
				$month = $aDate['mon'];
				$yeardate = $aDate['year'];
				if ($yeardate != $year) 	$mark = 'class="tdlineerr"';

				$res = $objPeriod->fetchAll('','',0,0,array('status_al'=>1,'statut'=>1),'AND'," AND t.period_month = ".$month." AND t.period_year = ".$yeardate,1);
				if ($res ==1 && empty($mark))
					$mark = '';
				else
				{
					$mark = 'class="tdlineerr"';
					$error++;
				}
				if ($res==1) $aImport[$i]['datem'] = $date;

			}
			elseif ($aCampo[$a] == 'refdepartament')
			{
				$ref = $aData[$aHeader[$a]];
				if ($ref)
				{
					$res = $objDepartament->fetch('',$ref);
					if ($res ==1) $mark = '';
					else
					{
						$mark = 'class="tdlineerr"';
						$error++;
					}
					if ($res==1) $aImport[$i]['fk_departament'] = $objDepartament->id;
				}
			}
			elseif ($aCampo[$a] == 'refsociete')
			{
				$ref = $aData[$aHeader[$a]];
				if ($ref)
				{
					$res = $objSociete->fetch(0, $ref);
					if ($res ==1) $mark = '';
					else
					{
						$mark = 'class="tdlineerr"';
						$error++;
					}
					if ($res==1) $aImport[$i]['fk_soc'] = $objSociete->id;
				}
			}
			elseif ($aCampo[$a] == 'refproduct')
			{
				$ref = $aData[$aHeader[$a]];
				if ($ref)
				{
					$res = $objProduct->fetch(0, $ref);
					if ($res ==1) $mark = '';
					else
					{
						$mark = 'class="tdlineerr"';
						$error++;
					}
					if ($res==1) $aImport[$i]['lines'][$j]['fk_product'] = $objProduct->id;
				}
				else
				{
					$mark = 'class="tdlineerr"';
					$error++;
				}
			}
			elseif ($aCampo[$a] == 'unit')
			{
				$unit =$aData[$aHeader[$a]];
				//buscamos el producto con columna 11
				$ref = $aData[$aHeader[11]];
				if ($ref)
				{
					$res = $objProduct->fetch(0, $ref);
					if ($res ==1)
					{
						//verificamos que unidad tiene
						$unitprod = $objProduct->getLabelOfUnit();
						if ($unitprod == $unit)
							 $mark = '';
						else
						{
							$mark = 'class="tdlineerr"';
							$error++;
						}
						if ($res==1)
						{
							$objCunit->fetch(0,$unitprod);
							$aImport[$i]['lines'][$j]['fk_unit'] = $objCunit->id;
						}
					}
					else
					{
						$mark = 'class="tdlineerr"';
						$error++;
					}
				}
				else
				{
					$mark = 'class="tdlineerr"';
					$error++;
				}
			}
			elseif ($aCampo[$a] == 'qty' || $aCampo[$a] == 'price')
			{
				$valor = $aData[$aHeader[$a]];
				$pos = strpos($valor,',');
				if ($pos === false)
				{
					if ($aCampo[$a] == 'qty') $aImport[$i]['lines'][$j]['value'] = $valor;
					if ($aCampo[$a] == 'price') $aImport[$i]['lines'][$j]['price'] = $valor;
				}
				else
				{
					$mark = 'class="tdlineerr"';
					$error++;
				}
			}

			print '<td '.$mark.'>'.$aData[$aHeader[$a]].'</td>';

		}
		$j++;
		print '<td align="right">'.($aData[$aHeader[12]]*$aData[$aHeader[13]]).'</td>';
		print '</tr>';

	}
	print '</table>';
	dol_fiche_end();
	echo '<pre>';
	print_r($aImport);
	echo '</pre>';
	if ($error)
	{
		$_SESSION['importNi'] = serialize($aImport);
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="step" value="'.$stepnew.'">';
		print '<center><br><input type="submit" class="butAction" name="save" value="'.$langs->trans("Save").'">';
		print '&nbsp;<input type="submit" class="butActionDelete" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
		print '</form>';
	}
}
?>
