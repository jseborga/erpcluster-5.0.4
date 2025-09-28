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
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

dol_include_once('/multicurren/class/cscurrencytypeext.class.php');
dol_include_once('/multicurren/class/csindexescountryext.class.php');
dol_include_once('/multicurren/lib/multicurrency.lib.php');

//excel
require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
include_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

$langs->load("poa@poa");

$action=GETPOST('action');

$id        = GETPOST("id"); //proceso
$idr       = GETPOST("idr"); //registro de pago
$idc       = GETPOST("idc"); //contrato

$gestion   = GETPOST("gestion");
$fk_poa_prev = GETPOST("fk_poa_prev");
$action    = GETPOST('action');

if (!empty($id))
{
	$ida = $_SESSION['aListip'][$id]['idAct'];
	$idp = $_SESSION['aListip'][$id]['idPrev'];
	$idc = $_SESSION['aListip'][$id]['idContrat'];
	$lAnticipo = $_SESSION['aListip'][$id]['anticipo'];
}

if (empty($gestion)) $gestion = date('Y');
$idArea = 3; //generar funcion para recuperar por usuario

$mesg = '';

$objuser = new User($db);
$object   = new Csindexescountryext($db);
$objectct = new Cscurrencytypeext($db);

//search last exchange rate
$objectcop = new Csindexescountryext($db);

$filterstatic = " AND t.entity = ".$conf->entity;
$objectct->fetchAll('ASC', 'order_currency', 0,0,array(1=>1),'AND',$filterstatic);
$aRegistry = array();
foreach((array) $objectct->lines AS $i => $objdata)
{
	$aRegistry[$objdata->ref] = $objdata->registry;
}

/*
 * Actions
 */


if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}
if ($action == 'add')
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
llxHeader("",$langs->trans("Managementsalary"),$help_url);

if ($action == 'create')
{
	print_fiche_titre($langs->trans("Upload archive"));
	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';
	print '<tr><td>';
	print $langs->trans('Year');
	print '</td>';
	print '<td>';
	print '<input type="year" name="year" required>';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>';
	$form->select_currency(($object->ref?$object->ref:$conf->global->MAIN_MONNAIE),"ref");
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Selectarchiv');
	print '</td>';
	print '<td>';
	print '<input type="file" name="archivo" size="40">';
	print '</td></tr>';
	print '</table>';
	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Upload").'"></center>';
	print '</form>';
}


?>
