<?php
/* Copyright (C) 2014-2017 Ramiro Queso        <ramiro@ubuntu-bo.com>
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

dol_include_once('/orgman/class/cpartida.class.php');
dol_include_once('/orgman/class/crubro.class.php');
dol_include_once('/poa/lib/poa.lib.php');
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
$objpartida   = new CPartida($db);
$objrubro   = new CRubro($db);

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
	$type = GETPOST('type');
	$period_year = GETPOST('period_year');
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
	$line = 1;
	$aColumn = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F');
	$aNameCol= array(1=>'code',2=>'label',3=>'codefather',4=>'typeclass',5=>'type',6=>'period_year');
	$lLoop = true;
	while ($lLoop == true)
	{
		$a = 1;
		for ($a = 1; $a <= 6; $a++)
		{
			//echo '<hr>'.$line.' '.$a.$aColumn[$a];
			$aCurren[$line][$a] = $objPHPExcel->getActiveSheet()->getCell($aColumn[$a].$line)->getFormattedValue();
		}
		if (empty($aCurren[$line][1])) $lLoop = false;
		//determinamos los grupos
		$lAdd = true;
		$d=0;
		for ($b=1; $b<=5; $b++)
		{
			$d--;
			$valor = substr($aCurren[$line][1],$d,1);
			if ($valor > 0 && $lAdd)
			{
				$valoradd='';
				for ($c=1;$c<=$b;$c++)
				{
					$valoradd.= '0';
				}
				$e = $d*-1 ;
				$aPadre[$line][1] = substr($aCurren[$line][1],0,5-$e).$valoradd;
				$aCurren[$line][3] = $aPadre[$line][1];
				$lAdd = false;
			}

		}
		$line++;
		//if ($line > 2000) $lLoop = false;
	}
	//corregimos si se encuentra una coma como separador de decimales
	$aFather = array();
	if (count($aCurren)>0)
	{
		$db->begin();
		$new = dol_now();
		foreach ($aCurren AS $line => $aData)
		{
			
			if (trim($aData[5])=='gasto')
			{
				$lAdd = true;
						//buscamos si existe el registro diario
				$filter = " AND period_year = ".$period_year;
				$filter.= " AND code = '".trim($aData[1])."'";
				$filter.= " AND t.entity = '".$conf->entity."'" ;
				$res = $objpartida->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
				if ($res == 1)
				{
					$lAdd = false;
					$objpartida->label = $aData[2];
					$objpartida->code_father = $aData[3];
					$objpartida->fk_user_mod = $user->id;
					$objpartida->datem = dol_now();
					$res = $objpartida->update($user);
					if ($res <=0)
					{
						echo '<hr>err up part ';
						$error++;
						setEventMessages($objpartida->error,$objpartida->errors,'errors');
					}
				}				
				if ($lAdd)
				{
					$objpartida->entity = $conf->entity;
					$objpartida->period_year = $period_year;
					$objpartida->code = $aData[1];
					$objpartida->label  = $aData[2];
					$objpartida->code_father = $aData[3];
					$objpartida->fk_user_create = $user->id;
					$objpartida->fk_user_mod = $user->id;
					$objpartida->datec = $new;
					$objpartida->datem = $new;
					$objpartida->tms = $new;
					$objpartida->active = 1;
					$res = $objpartida->create($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($objpartida->error,$objpartida->errors,'errors');
					}
				}

			}
			else
			{
				$lAdd = true;
						//buscamos si existe el registro diario
				$filter = " AND period_year = ".$period_year;
				$filter.= " AND code = '".trim($aData[1])."'";
				$filter.= " AND t.entity = '".$conf->entity."'" ;
				$res = $objrubro->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
				if ($res == 1)
				{
					$lAdd = false;
					$objrubro->label = $aData[2];
					$objrubro->code_father = $aData[3];
					$objrubro->fk_user_mod = $user->id;
					$objrubro->datem = dol_now();
					$res = $objrubro->update($user);
					if ($res <=0)
					{
						echo '<hr>errrrr rubro ';
						$error++;
						setEventMessages($objrubro->error,$objrubro->errors,'errors');
					}
				}				
				if ($lAdd)
				{
					$objrubro->entity = $conf->entity;
					$objrubro->period_year = $period_year;
					$objrubro->code = $aData[1];
					$objrubro->label  = $aData[2];
					$objrubro->code_father = $aData[3];
					$objrubro->fk_user_create = $user->id;
					$objrubro->fk_user_mod = $user->id;
					$objrubro->datec = $new;
					$objrubro->datem = $new;
					$objrubro->tms = $new;
					$objrubro->active = 1;
					$res = $objrubro->create($user);
					if ($res <=0)
					{
						echo '<hr>err crea rubro';
						$error++;
						setEventMessages($objrubro->error,$objrubro->errors,'errors');
					}
				}
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Importacion satisfactoria para la gestión ').' '.$period_year,null,'mesgs');
			header('Location: '.DOL_URL_ROOT.'/orgman/partida/list.php');
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
		header('Location: '.DOL_URL_ROOT.'/orgman/partida/import.php?action=create');
		exit;	
	}
}



// print_r($_POST);
// exit;

/*
 * View
 */

$form=new Form($db);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Poa"),$help_url);

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
	print '<input type="year" name="period_year" required>';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Type');
	print '</td>';
	print '<td>';
	print $form->selectarray('type',array(1=>$langs->trans('Partida'),2=>$langs->trans('Rubro')),GETPOST('type'));
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
