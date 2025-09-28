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
 *	\file       htdocs/poa/process/fiche_exce.php
 *	\ingroup    Process export excel
 *	\brief      Page fiche poa process export excel
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/class/userext.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsorderext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobscontactext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsadvance.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mwctsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mtyperepair.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mequipmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsresource.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';


if ($conf->assets->enabled)
	require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';

//require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';

require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
if ($conf->salary->enabled)
	require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontractext.class.php';
if ($conf->orgman->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pchargeext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentuserext.class.php';
}

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


$langs->load("mant@mant");

$action=GETPOST('action');

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$gestion   = GETPOST("gestion");
$fk_poa_prev = GETPOST("fk_poa_prev");
$action    = GETPOST('action');

if (empty($gestion)) $gestion = date('Y');
$idArea = 3; //generar funcion para recuperar por usuario

$mesg = '';

$object  = new Mjobsext($db);
//$objarea = new Poaarea($db);
$objUser = new Userext($db);
$objadh  = new Adherent($db);
$objord  = new Mjobsorderext($db);
$objjus  = new Mjobsuserext($db);
$objcont = new Mjobscontactext($db);
$objAdvance = new Mjobsadvance($db);
$objResource = new Mjobsresource($db);
$objTyperepair = new Mtyperepair($db);
$objEquipment = new Mequipment($db);
$objProduct = new Product($db);
$objsoc  = new Societe($db);
if ($conf->assets->enabled) $objass  = new Assetsext($db);
$objmwcts= new Mwctsext($db);
if ($conf->salary->enabled) $objPcontract = new Pcontractext($db);
if ($conf->orgman->enabled)
{
	$objPcharge = new Pchargeext($db);
	$objPdepartament = new Pdepartamentext($db);
	$objPdepartamentuser = new Pdepartamentuserext($db);
}
$objAdherent = new Adherent($db);

//echo '<hr>id '.$id;
/*
 * Actions
 */


if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}
// print_r($_POST);
// exit;

/*
 * View
 */

$form=new Form($db);

// $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
// llxHeader("",$langs->trans("POA"),$help_url);

//recuperaos informacion
$object->fetch($id);
$objsoc->fetch($object->fk_soc);
$aContact = $objsoc->contact_array();
//buscamos a la persona
$objadh->fetch($object->fk_member);
$cdpto = '';
$cgerencia = '';
if (!empty($objadh->societe) || $objadh->fk_soc)
{
	if (!empty($objadh->fk_soc))
	{
		$res = $objsoc->fetch($objadh->fk_soc);
		$cdpto = $objsoc->nom;
		$cgerencia = "GERENCIA DE ADMINISTRACION.";
	}
	else
	{
		if (!empty($objadh->societe))
		{
			$cdpto = $objadh->societe;
			$cgerencia = "GERENCIA DE ADMINISTRACION";
		}
	}
}
else
{
	list($login,$login2) = explode('@',$object->email);
	$objadh->fetch_login($login);
	if (STRTOUPPER($login) == 'MANTENIMIENTO')
	{
		$login = 'ICCEG';
		$cdpto = 'TECNICOS MANTENIMIENTO';
		$cgerencia = 'GERENCIA DE ADMINISTRACION';
	}
	if (STRTOUPPER($login) == 'ASANCHEZ')
	{
		$login = 'asanchez';
		$cdpto = 'DMMI';
		$cgerencia = 'GERENCIA DE ADMINISTRACION';
	}
}
//condicional para cargo
$charge = '';
if ($conf->salary->enabled)
{
	$res = $objPcontract->fetch_vigent($object->fk_member);
	if ($res >0)
	{
		$fk_charge = $objPcontract->fk_charge;
		if ($conf->orgman->enabled && $fk_charge>0)
		{
			$objPcharge->fetch($fk_charge);
			$charge = $objPcharge->label;
		}
	}
}
if (empty($charge))
{
	$filter = " AND t.fk_member = ".$object->fk_member;
	$res = $objUser->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
	if ($res >0)
		$charge = $objUser->job;
}
//para departamentos
$fk_departament = 0;
$departament = '';
if ($conf->orgman->enabled)
{
	$filter = " AND t.fk_user = ".$object->fk_member;
	$res = $objPdepartamentuser->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
	if ($res == 1)
	{
		$fk_departament = $objPdepartamentuser->fk_departament;
	}
	else
	{
		foreach ($objPdepartamentuser->lines AS $j => $line)
		{
			if (empty($fk_departament)) $fk_departament = $line->fk_departament;
		}
	}
	//buscamos el departamento
	$res = $objPdepartament->fetch($fk_departament);
	if ($res > 0) $departament = $objPdepartament->label;
}
//ultimo trabajo de avance
$filter = " AND t.fk_jobs = ".$object->id;
$workdetail = '';
$work_date_ini = 0;
$work_date_fin = 0;
$res = $objAdvance->fetchAll('DESC','date_fin',0,0,array(1=>1),'AND',$filter);
if ($res>0)
{
	foreach ($objAdvance->lines AS $j => $line)
	{
		if (empty($workdetail)) $workdetail.= dol_print_date($line->date_fin,'day').' - '.$line->description;
		if (empty($work_date_ini)) $work_date_ini = $line->date_ini;
		if (empty($work_date_fin)) $work_date_fin = $line->date_fin;
		if ($line->date_ini <= $work_date_ini) $work_date_ini = $line->date_ini;
		if ($line->date_fin >= $work_date_fin) $work_date_fin = $line->date_fin;

	}
}
//uso recursos
$filterres = " AND t.fk_jobs = ".$object->id;
$aRresource = '';
$resu = $objResource->fetchAll('','',0,0,array(1=>1),'AND',$filterres);
$listResource = '';
if ($resu >0)
{
	foreach($objResource->lines AS $k => $liner)
	{
		if ($liner->type_cost == 'MA')
		{
			if ($liner->fk_product>0)
			{
				$objProduct->fetch($liner->fk_product);
				$aResource[$liner->type_cost][$objProduct->label]['qty']+=$liner->quant;
				$aResource[$liner->type_cost][$objProduct->label]['total']+=$liner->quant*$liner->price;
			}
			else
			{
				$aResource[$liner->type_cost][$liner->description]['qty']+=$liner->quant;
				$aResource[$liner->type_cost][$liner->description]['total']+=$liner->quant*$liner->price;
			}
		}
		else
		{
			$aResource[$liner->type_cost][$liner->description]['qty']+=$liner->quant;
			$aResource[$liner->type_cost][$liner->description]['total']+=$liner->quant*$liner->price;
		}
	}
	$nLine = 1;
	foreach ($aResource AS $typecost => $aProd)
	{
		foreach ($aProd AS $label => $data)
		{
			$listResource.= ' N. '.$nLine;
			$listResource.= ': Tipo: '.$typecost;
			$listResource.= '; Nom.: '.$label;
			$listResource.= '; Q.: '.$data['qty'];
			$nLine++;
		}
	}
}


$aUser  = $objjus->list_jobsuser($id);

$aOrder = $objord->list_order($id);
$listuser = '';
foreach ((array) $aUser AS $j => $objjUs)
{
	if ($objAdherent->fetch($objjUs->fk_user)>0)
	{
		if ($objAdherent->id == $objjUs->fk_user)
		{
			if (!empty($listuser))$listuser.=', ';
			$listuser.= $objAdherent->firstname.' '.$objAdherent->lastname;
		}
	}
}

$listorder = '';
$aOrd = array();
foreach ((array) $aOrder AS $j => $objjor)
{
	if ($objjor->fk_jobs == $id)
	{
		if ($aOrd[$objjor->order_number]!=$objjor->order_number)
		{
			if (!empty($listorder)) $listorder.=', ';
			$listorder.= $objjor->order_number;
		}
		$aOrd[$objjor->order_number] = $objjor->order_number;
	}
}
if (!empty($listorder))
	$listorder = $langs->trans('Número(s) de Pedido').': '.$listorder;
//contactos
$aJobsContact = $objcont->list_contact($object->id);
//internos
$aJobsUsers   = $objjus->list_jobsuser($object->id);

$listecontact = '';
foreach ((array) $aJobsContact AS $k => $objtmp)
{
	if (!empty($listecontact))
		$listecontact .= ', ';
	$listecontact .= $aContact[$objtmp->fk_contact];
}
foreach ((array) $aJobsUser AS $k => $objtmp)
{
	if (!empty($listecontact))
		$listecontact .= ', ';
	$objt = $aContact[$objtmp->id];
	$listecontact .= $objt->firstname.' '.$objt->lastname;
}
if ($object->fk_equipment)
{
	$res = $objEquipment->fetch($object->fk_equipment);
	if ($res >0)
	{
		$codeequipment.= $objEquipment->ref;
		$nomeequipment = $objEquipment->label;
		if (!empty($objEquipment->ref_ext))
			$codeequipment.= ' - '.$objEquipment->ref_ext;
	}
	else
	{
		$codeequipment = 'NO REGISTRADO.';
		$nomeequipment = 'NO REGISTRADO.';
	}
}
else
{
	$codeequipment = 'NO CORRESPONDE';
	$nomeequipment = 'NO CORRESPONDE';
}
		//PRCESO 1
$objPHPExcel = new PHPExcel();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("excel/form_modelo.xlsx");
$aDate = dol_getdate($object->date_ini);

		//imagen
		//$objDraw = new PHPExcel_Worksheet_Drawing();
		//$objDraw->setPath('../img/bcb.png');
		//$objDraw->setHeight(50);
		//$objDraw->setCoordinates('E2');
		//$objDraw->setOffsetX(10);
		//$objDraw->setWorksheet($objPHPExcel->getActiveSheet());

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->SetCellValue('O2',dol_print_date($object->date_ini,'day'));
//$objPHPExcel->getActiveSheet()->SetCellValue('P2',$aDate['mon']);
//$objPHPExcel->getActiveSheet()->SetCellValue('Q2',$aDate['year']);
$objPHPExcel->getActiveSheet()->SetCellValue('O4',$object->ref);
		//numeracion

$objPHPExcel->getActiveSheet()->SetCellValue('G13',$objadh->firstname.' '.$objadh->lastname);
$objPHPExcel->getActiveSheet()->SetCellValue('G15',$charge);
$objPHPExcel->getActiveSheet()->SetCellValue('G17',$departament);

$objPHPExcel->getActiveSheet()->SetCellValue('O17',$object->internal);
		//armando la especialidad
$res = $objmwcts->fetch_working_class($object->typemant,$object->speciality_job);
$workingclass = $langs->trans('Generic');
if ($res > 0 && $objmwcts->typemant == $object->typemant && $objmwcts->speciality == $object->speciality_job)
	$workingclass = select_working_class($objmwcts->working_class,'','',0,1);
$workingclass.=' - '.select_typemant($object->typemant,'','',0,1);
$workingclass.=' - '.select_speciality($object->speciality_job,'','',0,1);

$restr=$objTyperepair->fetch($object->fk_type_repair);
if ($restr>0)
	$objPHPExcel->getActiveSheet()->SetCellValue('G21',$objTyperepair->ref.' - '.$objTyperepair->label);
//especialidad
$objPHPExcel->getActiveSheet()->SetCellValue('G23',$listuser);
//lista resp
$objPHPExcel->getActiveSheet()->SetCellValue('G25',$codeequipment);
//lista resp
$objPHPExcel->getActiveSheet()->SetCellValue('G27',$nomeequipment);
//lista resp
$g29 = $langs->trans('Problem').': '.$object->detail_problem;
$g31 = $workdetail;
$g33 = $listResource;
$objPHPExcel->getActiveSheet()->SetCellValue('G29',$g29);
$objPHPExcel->getActiveSheet()->SetCellValue('G31',$g31);
$objPHPExcel->getActiveSheet()->SetCellValue('G33',$g33);
$adate = dol_getdate($object->date_ini);
$dateini = $adate['mday'].'/'.$adate['mon'].'/'.$adate['year'];
$horaini = $adate['hours'].':'.$adate['minutes'];
$adate = dol_getdate($object->date_fin);
$datefin = $adate['mday'].'/'.$adate['mon'].'/'.$adate['year'];
$horafin = $adate['hours'].':'.$adate['minutes'];

$objPHPExcel->getActiveSheet()->SetCellValue('G38',dol_print_date($work_date_ini,'day'));
$objPHPExcel->getActiveSheet()->SetCellValue('K38',dol_print_date($work_date_ini,'hour'));

$objPHPExcel->getActiveSheet()->SetCellValue('G40',dol_print_date($work_date_fin,'day'));
$objPHPExcel->getActiveSheet()->SetCellValue('K40',dol_print_date($work_date_fin,'hour'));

$objPHPExcel->getActiveSheet()->getStyle('G31')->getAlignment()->setVertical(PHPExcel_Style_alignment::VERTICAL_TOP);
$objPHPExcel->getActiveSheet()->getStyle('G31')->getAlignment()->setHorizontal(PHPExcel_Style_alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->mergeCells('G31:Q31');

//$objPHPExcel->getActiveSheet()->getStyle('N2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("excel/exportbcb.xlsx");

header('Location: '.DOL_URL_ROOT.'/mant/jobs/fiche_export.php');
// llxFooter();

// $db->close();
?>
