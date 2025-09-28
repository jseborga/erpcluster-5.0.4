<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *	\file       htdocs/almacen/fiche.php
 *	\ingroup    Almacen
 *	\brief      Page fiche fabrication
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
dol_include_once('/assistance/core/modules/assistance/modules_assistance.php');
dol_include_once('/assistance/class/licencesext.class.php');
dol_include_once('/assistance/class/licenceslog.class.php');
dol_include_once('/assistance/class/ctypelicenceext.class.php');
dol_include_once('/assistance/class/membervacationext.class.php');
dol_include_once('/assistance/class/membervacationdet.class.php');
dol_include_once('/assistance/class/html.formadd.class.php');
dol_include_once('/assistance/lib/assistance.lib.php');
dol_include_once('/assistance/lib/utils.lib.php');

dol_include_once('/core/lib/datefractal.lib.php');

dol_include_once('/adherents/class/adherent.class.php');
dol_include_once('/orgman/class/pdepartament.class.php');
dol_include_once('/orgman/class/pdepartamentuser.class.php');
dol_include_once('/orgman/lib/departament.lib.php');

dol_include_once('/salary/class/pgenerictableext.class.php');
dol_include_once('/salary/class/pgenericfieldext.class.php');

$langs->load("almacen");
$langs->load("products");
$langs->load("stocks");
$langs->load("companies");

if ($conf->fabrication->enabled)
	$langs->load("fabrication@fabrication");

$action=GETPOST('action');

$id = GETPOST('id');
$warehouseid    = GETPOST("warehouseid");
$fk_fabrication = GETPOST("fk_fabrication");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
//verificamos el periodo


// Load object if id or ref is provided as parameter
$object=new Licencesext($db);
$objLicenceLog = new Licenceslog($db);
$objDepartament = new Pdepartament($db);
$objDeptUser = new Pdepartamentuser($db);
$objCtypelicence = new Ctypelicenceext($db);
$objMembervacation = new Membervacationext($db);
$objMembervacationdet = new Membervacationdet($db);
$adherent = new Adherent($db);
$objUser = new User($db);
if (!empty($id))
	$object->fetch($id);


$aFilterent = array();
$aFilterentsol = array();
$filterusersol = '';
$now = dol_now();
if (!$user->admin) list($aFilterent, $filteruser,$aFilterentsol, $filterusersol,$aAreadirect,$fk_areaasign,$filterarea,$aFilterarea, $fk_user_resp,$aExcluded) = verif_accessalm();

$aLog = array(-1=>$langs->trans('StatusOrderCanceledShort'),0=>$langs->trans('StatusOrderDraftShort'),1=>$langs->trans('StatusOrderValidated'),6=>$langs->trans('StatusOrderApproved'),2=>$langs->trans('StatusOrderSent'),3=>$langs->trans('StatusOrderToBillShort'),4=>$langs->trans('StatusOrderProcessed'),5=>$langs->trans('StatusOrderoutofstock'));
$aLog = array(-2=>$langs->trans('Rejected'),
	-1=>$langs->trans('Annulled'),
	0=>$langs->trans('Draft'),
	6=>$langs->trans('Approved'),
	1=>$langs->trans('Validated'),
	2=>$langs->trans('Delivered'),
	5=>$langs->trans('StatusOrderoutofstock'));
$listhalfday=array('morning'=>$langs->trans("Morning"),"afternoon"=>$langs->trans("Afternoon"));

/*
 * View
 */

$form=new Formv($db);


$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
//llxHeader("",$langs->trans("ApplicationsWarehouseCard"),$help_url);
$morejs=array("/almacen/javascript/almacen.js");
llxHeader('',$langs->trans("ApplicationsWarehouseCard"),$help_url,'','','',$morejs,'',0,0);
// Parte de Validar y Aprobacion una licencia solicitada
if ($id>0)
{
	$error=0;
	$lHour = true;
	$res = $objCtypelicence->fetch(0,$object->type_licence);
	if ($res <=0)
		setEventMessages($objCtypelicence->error,$objCtypelicence->errors,'errors');
	elseif($objCtypelicence->type == 'V') $lHour = false;

	//Parte de rechazar una solicitud
	if($action == 'refuse'){
		$formquestion = array(
			array('type'=>'text','label'=>$langs->trans('Motivo de rechazo'),'size'=>40,'name'=>'refuse','value'=>'','placeholder'=>$langs->trans('Escriba el motivo del rechazo'))
		);
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Refuse"),$langs->trans("Confirmrefuse",$object->ref),"confirm_refuse",$formquestion,1,2);
		if ($ret == 'html') print '<br>';
	}

	if ($action == 'validate')
	{
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Validate"),$langs->trans("Confirmvalidate",$object->ref),"confirm_validate",'',0,2);
		if ($ret == 'html') print '<br>';
	}

	//formato de fechas
	if ($lHour)
	{
		$object->date_ini = $db->jdate($object->dateini);
		$object->date_fin = $db->jdate($object->datefin);
	}
	else
	{
		$aDateini = dol_getdate($object->dateini);
		$object->date_ini_gmt = $db->jdate($object->dateini,1);
		$object->date_fin_gmt = $db->jdate($object->datefin,1);
	}
	//dias solicitados
	$halfday = $object->halfday;
	$days = num_open_day_fractal($object->date_ini_gmt, $object->date_fin_gmt, 0, 1, $object->halfday);
	 // Confirm validate request
	if ($action == 'approval' || $action == 'approvaltwo')
	{
		$formquestion = '';
		$lStatus = true;
		if ($objCtypelicence->type == 'V')
		{
		//verificamos si tiene aprobado las vacaciones por miembro
			$filter = " AND t.fk_member = ".$object->fk_member;
			$filter.= " AND t.status >=0";
			$res = $objMembervacation->fetchAll('ASC','t.period_year',0,0,array(1=>1),'AND',$filter);
			$nVac =0;
			if ($res >0)
			{
				foreach ($objMembervacation->lines AS $j => $line)
				{
					if ($line->status == 0) $lStatus = false;
					$nVacation = $line->days_assigned;
					$nUsed = 0;
				//obtenemos cuanto se utilizara con esta vacacion asignada
					$filterdet = " AND t.fk_member_vacation = ".$line->id;
					$resdet = $objMembervacationdet->fetchAll('','',0,0,array(1=>1),'AND',$filterdet);
					if ($resdet)
					{
						foreach ($objMembervacationdet->lines AS $k => $linek)
						{
							$nUsed+= $linek->day_used;
						}
					}
					$nVacation-=$nUsed;

					$formquestion[$nVac] = array('type'=>"other",'label'=>$langs->trans('Gestion').' '.$line->period_year.' '.$langs->trans('Availabledays'),'value'=>$nVacation);
					$nVac++;
				}
			}
			else
				$lStatus=false;
		}
		if ($conf->global->ASSISTANCE_MESSAGE_SENDMAIL)
			$formquestion[$nVac]= array('type'=>'checkbox','name'=>'se','label'=>$langs->trans('Sendemail'));
		$actionnext = 'confirm_approval';
		$titleHead = $langs->trans("ConfirmApprove").' '.$object->ref;
		if (!$lStatus)
		{
			if ($action != 'approvaltwo')
			{
				$actionnext = 'approvaltwo';
				$formquestion = array();
				$nVac=0;
				$formquestion[$nVac]= array('type'=>'other','label'=>$langs->trans('Warnings'),'value'=>$langs->trans('No estan verificados las vacaciones, solicite la verificación al area de Recursos Humanos'));
				$titleHead = $langs->trans('Areyousuretocontinuewiththeapproval');
			}
		}
		$nVac++;
		$formquestion[$nVac]=array('type'=>'hidden','name'=>'nDays','value'=>$days);
			//$formquestion = array($aQuestion);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
			($objCtypelicence->type == 'L'?$langs->trans("Licence"):$langs->trans('Vacation')),
			$titleHead,
			$actionnext,
			$formquestion,
			1,2);
		if ($ret == 'html') print '<br>';
	}

	$head = licence_prepare_head($object);
	dol_fiche_head($head, 'log', $langs->trans("Licences"), 0, '');

	$starthalfday=($object->halfday == -1 || $object->halfday == 2)?'afternoon':'morning';
	$endhalfday=($object->halfday == 1 || $object->halfday == 2)?'morning':'afternoon';

	print '<table class="border centpercent">'."\n";

	print '<tr><td width="15%">'.$langs->trans("Ref").'</td><td colspan="2">';
	print $object->ref;
	print '</td></tr>';

	print '<tr><td width="15%">'.$langs->trans("Name").'</td><td colspan="2">';
	$adherent->fetch($object->fk_member);
	print $adherent->getNomUrl(1).' '.$adherent->lastname.' '.$adherent->firstname;
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Type").'</td><td colspan="2">';
	print select_type_licence($object->type_licence,'type_licence','',0,1,'code','label');
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Justification").'</td><td colspan="2">';
	print $object->detail;
	print '</td></tr>';
	$viewHour = 1;
	if (!$lHour) $viewHour = 0;
	$object->lHour = $lHour;

	print '<tr><td>'.$langs->trans("Dateini").'</td><td>';
	print dol_print_date($object->date_ini,($viewHour?'dayhour':'day'));
	if (!$lHour)
	{
		print ' &nbsp; &nbsp; ';
		print $langs->trans($listhalfday[$starthalfday]);
		$object->halfdayininame = $langs->trans($listhalfday[$starthalfday]);
	}

	print '</td>';

	if ($object->statut>1)
		print '<td> Registro de Salida  : '.dol_print_date($object->date_ini_ejec,'dayhour').'</td>';
	print '</tr>';

	print '<tr><td>'.$langs->trans("Datefin").'</td><td>';
	print dol_print_date($object->date_fin,($viewHour?'dayhour':'day'));
	if (!$lHour)
	{
		print ' &nbsp; &nbsp; ';
		print $langs->trans($listhalfday[$endhalfday]);
		$object->halfdayfinname = $langs->trans($listhalfday[$endhalfday]);
	}
	print '</td>';
	if ($object->statut>1)
		print '<td> Registro de Regreso : '.dol_print_date($object->date_fin_ejec,'dayhour').'</td>';

	if (!$lHour && $object->date_ini && $object->date_fin)
	{
		print '<tr><td>'.$langs->trans("Daysrequested").'</td><td>';
		print $days;
		$object->days = $days;
		print '</td>';
	}

	print '</tr>';

	print '<tr><td>'.$langs->trans("Statut").'</td><td colspan="2">';
	print $object->getLibStatut(3);
	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();
		//print_r($object);
		//Variables para generar el reporte

	$filename='assitance/'.$period_year.'/'.$object->fk_member.'/rrhh';
	$filedir=$conf->assistance->dir_output.'/assitance/'.$period_year.'/'.$object->fk_member.'/rrhh';

	$resType=$objCtypelicence->fetchAll('','',0,0,array(1=>1),'AND',"AND t.code ='".$object->type_licence."'", true);
	if($resType > 0)
	{
				//echo "valor permiso: ".$objCtypelicence->type;
		if($objCtypelicence->type === 'V'){
			$modelpdf = 'vacacion';
		}
		if($objCtypelicence->type === 'L'){
			$modelpdf = 'licencia';
		}
	}
	else{
		setEventMessages("Error al genera los reporte de tipo",$resType->errors,'errors');
	}


}
print '<br>';
include DOL_DOCUMENT_ROOT.'/assistance/licence/tpl/licencelog_list.tpl.php';


llxFooter();

$db->close();
?>
