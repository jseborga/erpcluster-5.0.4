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
 *      \file       htdocs/poa/execution/ficheprev.php
 *      \ingroup    Plan Operativo Anual
 *      \brief      Page edit all list workflow
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/poa/class/poastructureext.class.php");
require_once DOL_DOCUMENT_ROOT.'/poa/pac/class/poapac.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/class/poapoaext.class.php';
require_once(DOL_DOCUMENT_ROOT."/poa/class/poaactivityext.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/class/poaprevext.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/class/poapartidapreext.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidacom.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidadev.class.php");
require_once DOL_DOCUMENT_ROOT.'/poa/class/poapartidapredetext.class.php';
require_once(DOL_DOCUMENT_ROOT."/poa/process/class/poaprocess.class.php");
require_once DOL_DOCUMENT_ROOT."/poa/process/class/poaprocesscontrat.class.php";
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflow.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflowdet.class.php");
require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivityworkflow.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaareauser.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/guarantees/class/poaguarantees.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/appoint/class/poacontratappoint.class.php';
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/main.lib.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poagraf.lib.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/contrat.lib.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/doc.lib.php");
require_once(DOL_DOCUMENT_ROOT."/poa/class/html.formadd.class.php");

require_once(DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
if ($conf->addendum->enabled)
	require_once DOL_DOCUMENT_ROOT.'/addendum/class/addendum.class.php';

if ($conf->poai->enabled)
{
	require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaiinstruction.class.php");
	require_once(DOL_DOCUMENT_ROOT."/poai/instruction/class/poaimonitoring.class.php");
}

require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';

$langs->load("poa@poa");

if (!$user->rights->poa->prev->leer) accessforbidden();

$_SESSION['localuri'] = $_SERVER['REQUEST_URI'];
$object  = new Poaprevext($db);
$objact  = new Poaactivityext($db);
$objpoa  = new Poapoaext($db);
$objpac  = new Poapac($db);
$objstr  = new Poastructure($db);
$objproc = new Poaprocess($db);
$objpcon = new Poaprocesscontrat($db);
$objgua  = new Poaguarantees($db);
$objapp  = new Poacontratappoint($db);
$objuser = new User($db);
$objpre  = new Poapartidapreext($db);
$objprevdet = new Poapartidapredetext($db);
$objprev = new Poaprevext($db);
$objcom  = new Poapartidacom($db);
$objdev  = new Poapartidadev($db);
$objarea = new Poaarea($db);
$objareauser = new Poaareauser($db);

//$objpp   = new Poapartidapre($db);
$objppd  = new Poapartidapredet($db);

$objcont = new Contrat($db);
$objsoc  = new Societe($db);
$extrafields = new ExtraFields($db);

$objectw = new Poaactivityworkflow($db);

if ($conf->addendum->enabled) $objadden = new Addendum($db);
$extralabels=$extrafields->fetch_name_optionals_label($objcont->table_element);

//unset($_SESSION['aLisprev']);
if ($conf->poai->enabled)
{
	$objinst = new Poaiinstruction($db);
	$objmoni = new Poaimonitoring($db);
}

//asignando filtro de usuario
assign_filter_user('psearch_user');

$id     = GETPOST('id' ,'int'); //preventivo id
$ida    = GETPOST('ida','int'); //actividad id //esto se recibe
$idppp  = GETPOST('idppp','int'); //productdet
$idpc   = GETPOST('idpc','int');
$idrc   = GETPOST('idrc','int');
$action = GETPOST('action');
$modal  = GETPOST('modal','alpha');
$selidrc = GETPOST('selidrc','int');
$selidc  = GETPOST('selidc','int');
$fk_poa_prev = GETPOST('fk_poa_prev');
$cancel = GETPOST('cancel');
//variables fijas
$aContratpay = array();
$aOrderdoc = array(1=>'prev',2=>'proc',3=>'cont',4=>'desi',5=>'op',6=>'paym',7=>'rece',8=>'guara');
$aOrderimp = array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8);
	//color
poa_grafic_color();

//recuperamos la actividad
if ($ida > 0) $resact = $objact->fetch($ida);
if ($objact->fk_prev)
{
	$id = $objact->fk_prev;
	$fk_poa_prev = $objact->fk_poa;
	//buscando el preventivo
	$resprev = $object->fetch($id);
	if ($object->id == $id) $gestion = $object->gestion;
}
//verifica gestion
if (!isset($_SESSION['gestion']))
	$_SESSION['gestion'] = date('Y');

//si existe ID es el preventivo
if ($id > 0)
{
	//recuperamos el responsable del preventivo
	$objuser->fetch($object->fk_user_create);
	//variable para imprimir inicio proceso despues del preventivo
	$sumapre   = 0;
	$sumacont  = array();
	$sumacom   = array();
	$sumapay   = array();
	$aSocname  = array();
	$aPreve    = array();
	$aContratcode    = array();
	$aProcesscontrat = array();
	$aFactdoc['Payments'] = 0;
	$aFactdoc['anticipo'] = 0;
	$_SESSION['aLisprev'] = prev_ant($id,$_SESSION['aLisprev'],'0,1');
	$data = $_SESSION['aLisprev'][$id];
	foreach((array) $data['idprev'] AS $fk_prev_)
	{
		$objprev->fetch($fk_prev_);
		$aPreve[$fk_prev_] = $objprev->gestion;
	}
	//proceso
	if ($data['idprocessant']) $idProcess = $data['idprocessant'];
	else $idProcess = $data['idprocess'];

	//armamos el array para procesar los contratos, comprometidos, pagos
	unset($_SESSION['aListip'][$idProcess]);
	$_SESSION['aListip'][$idProcess]['idAct']=$ida;
	$_SESSION['aListip'][$idProcess]['gestion']=$objact->gestion;
	$_SESSION['aListip'][$idProcess]['idPrev']=$id;
	$_SESSION['aListip'][$idProcess]['idPrevant'] = $data['idprevant'];
}


/*
ACTIONS
*/
if ($action == 'updateprocescontrat' && $idpc)
{
	$date_order_proceed = dol_mktime(12, 0, 0, GETPOST('op_month'),GETPOST('op_day'),GETPOST('op_year'));

	$objpcon->fetch($idpc);
	if ($objpcon->id == $idpc)
	{
		$objpcon->date_order_proceed = $date_order_proceed;
		$res = $objpcon->update($user);
		if (!$res) $error++;
		else $action = '';
	}
}
if ($cancel == $langs->trans('Cancel'))
{
	header("Location: ".$_SERVER['PHP_SELF'].'?ida='.$ida);
	exit;
}
//action de monitoreo actividad
if ($modal == 'ficheactivity')
	include DOL_DOCUMENT_ROOT.'/poa/activity/lib/crud_activity.lib.php';
//action de procesos
if ($modal == 'ficheprocess')
	include DOL_DOCUMENT_ROOT.'/poa/process/lib/crud_process.lib.php';
//action de preventivos
if ($modal == 'fichepreventive')
	include DOL_DOCUMENT_ROOT.'/poa/execution/lib/crud_preventive.lib.php';
//action de contratos
if ($modal == 'fichecommitted')
	include DOL_DOCUMENT_ROOT.'/poa/execution/lib/crud_committed.lib.php';
//action de devengados
if ($modal == 'ficheaccrued')
	include DOL_DOCUMENT_ROOT.'/poa/process/lib/crud_accrued.lib.php';
//action de devengados
if ($modal == 'fichedesign')
	include DOL_DOCUMENT_ROOT.'/poa/appoint/lib/crud_appoint.lib.php';
//action de devengados
if ($modal == 'ficheorderpro')
	include DOL_DOCUMENT_ROOT.'/poa/process/lib/crud_poaprocesscontrat.lib.php';

//cabecera
//$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
//$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js','poa/js/poa.js','poa/js/scriptajax.js');
//$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
//llxHeader("",$langs->trans("Activity"),$help_url,'','','',$aArrjs,$aArrcss);

//cabecera
header("Content-type: text/html; charset=".$conf->file->character_set_client);

$aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/bootstrap-responsive.min.css','poa/css/style-responsive.css','poa/css/AdminLTE.css');
$aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/dist/css/AdminLTE.css','poa/css/dist/css/AdminLTE.min.css','poa/css/dist/css/skins/_all-skins.min.css','poa/css/select_dependientes.css','poa/css/bootstrapadd.css','poa/css/slider/css/slider.css');
$aArrayofcss= array('poa/bootstrap/css/bootstrap.css','poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/dist/css/AdminLTE.css','poa/dist/css/AdminLTE.min.css','poa/dist/css/skins/_all-skins.min.css','poa/css/select_dependientes.css','poa/css/bootstrapadd.css','poa/css/slider/css/slider.css');

$aArrayofjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js','poa/js/select_dependientes.js','poa/js/fiche_process.js');

top_htmlheadv($head,$langs->trans("POA"),0,0,$aArrayofjs,$aArrayofcss);

//impresion de submenu segun seleccion
include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/menup.tpl.php';

$form=new Form($db);
$formadd = new Formadd($db);

//cuerpo
print '<br><br><br>';
print '<section class="content">';

print '<div class="row">';
print '<div class="col-md-12">';
print '<div class="box">';
print '<h3>'.$objact->label.'</h3>';
print '</div>';
print '</div>';
print '</div>';
//include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/fiche_structure.tpl.php';

//option for info boxes

//vemos la actividad
$objectw->getlist($ida);

print '<div class="row">';
include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/actibox.tpl.php';
print '</div>'; //row
//vemos la actividad
//include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/acti.tpl.php';

if ($id>0)
{
	//proceso
	$resproc = $objproc->fetch($idProcess);
	if ($idProcess > 0 && $resproc > 0 && $objproc->date_process < $object->date_preventive )
	{
		$aOrderimp[1] = 2; //primero proceso
		$aOrderimp[2] = 1; //segundo preventivo
	}
	//verificando tipo de formulario excel
	$aDatelim = dol_mktime(23, 59, 59, 8,31,2015);
	$aDateobj = dol_getdate($objproc->date_process);
	$aDateobj = dol_mktime(0, 0, 1, $aDateobj['mon'],$aDateobj['mday'],$aDateobj['year']);
	if ($aDateobj <= $aDatelim) $lForm = true;
	else $lForm = false;

	$aFact = array();

	//contratos
	$aContrat = array();
	if (count($data['contrat'])>0)
	{
		$lViewcontrat = true;
		foreach ($data['contrat'] AS $fkcontrat => $idc)
		{
			//verificamos la fecha de contrato para el orden de impresion
			$objcont->fetch($fkcontrat);
			if ($objcont->date_contrat < $object->date_preventive)
			{
				$aOrderimp[1] = 2;
				$aOrderimp[2] = 3;
				$aOrderimp[3] = 1;
			}
			$aContrat[$fkcontrat] = $fkcontrat;
			$aProcesscontrat[$fkcontrat] = $idc;
			$_SESSION['aListip'][$idProcess]['idContrat'] = $fkcontrat;
			$_SESSION['aListip'][$idProcess]['idc'] = $idc;
		}
	}

	// if (count($aContrat)>0)
	//   ksort($aContrat);
	$htmlother=new FormOther($db);

	//totales para monto y conteo
	$nTotalPrev = 0;
	$nCountPrev = 0;


	$celcolor1a = ' style="background:'.$_SESSION['arrayc'][$_SESSION['arrayk']['PREVENTIVE']].'; color:#000;"';
	$celcolor2a = ' style="background:'.$_SESSION['arrayc'][$_SESSION['arrayk']['INI_PROCES']].'; color:#000;"';
	$celcolor3a = ' style="background:'.$_SESSION['arrayc'][$_SESSION['arrayk']['RECEP_PRODUCTS']].';color:#FFF;"';
	$celcolor4a = ' style="background:'.$_SESSION['arrayc'][$_SESSION['arrayk']['RECEP_PRODUCTS']].';color:#FFF;"';
	$celcolor5a = ' style="background:'.$_SESSION['arrayc'][$_SESSION['arrayk']['AUT_PAYMENT']].';color:#000;"';
	$celcolor6a = ' style="background:'.$_SESSION['arrayc'][$_SESSION['arrayk']['PARTIAL_REPORT_ACCORDANCE']].';color:#000;"';

	$celcolor1 = ' style="background:#D2E7E7;color:#000;"';
	$celcolor2 = ' style="background:#D7DDDD;color:#000;"';
	$celcolor3 = ' style="background:#84CCE0;color:#000;"';
	$celcolor4 = ' style="background:#73ABCF;color:#000;"';
	$celcolor5 = ' style="background:#6C8DC7;color:#000;"';
	print '<div class="row">';
	foreach ($aOrderimp AS $i1 => $iVal)
	{
		switch ($aOrderdoc[$iVal])
		{
			case 'prev':
			print '<div class="col-md-4 col-sm-6 col-xs-12">';
			include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/prevebox.tpl.php';
			print '</div>';
			break;
			case 'proc':
			print '<div class="col-md-4 col-sm-6 col-xs-12">';
			include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/procebox.tpl.php';
			print '</div>';
			break;
			case 'cont':
				//if (count($data['contrat'])>0)
			print '<div class="col-md-4 col-sm-6 col-xs-12">';

			if ($objproc->statut > 0)
				include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/contrbox.tpl.php';
			print '</div>';
			break;
			default:
				# code...
			break;
		}
	}
	print '</div>';

	//designaciones
	print '<div class="row">';
	if ($resprev>0)
	{
		$nApp = 0;
		require_once DOL_DOCUMENT_ROOT.'/poa/appoint/class/poacontratappoint.class.php';
		$objapp  = new Poacontratappoint($db);
		foreach((array) $aContrat AS $i => $ni)
		{
			$objapp->getlist($i);
			$nApp+=count($objapp->array);
		}
	}
	print '<div class="col-md-4">';
	print '<div class="box box-primary direct-chat direct-chat-primary collapsed-box">';
	print '<div class="box-header with-border">';
	print '<h3 class="box-title">'.$langs->trans('Designations').'</h3>';
	print '<div class="box-tools pull-right">';
	print '<span data-toggle="tooltip" title="'.$nApp.' '.$langs->trans('Desigments').'" class="badge bg-red">'.$nApp.'</span>';
	print '<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>';
	print '</div>';
	print '</div>';
	print '<div class="box-body" style="display: none;">';
	include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/desigbox.tpl.php';
	print '</div>'; //box-body
	print '</div>'; //box
	print '</div>'; //col-md-4

	//pagos
	if ($idProcess > 0 &&abcdef)
	{
		$lfpay = 0;
		$nPay = 0;
		$nSumpay = 0;
		$nSumpayadv = 0;
		foreach((array) $aContrat AS $i => $ni)
		{
			$objcont->fetch($i);
			$lAdvance = false;
			$res=$objcont->fetch_optionals($i,$extralabels);
			if ($objcont->array_options['options_advance']) $lAdvance = true;

			$objdev->getlist2($object->id,$i);
			$nPay+= count($objdev->array);
			foreach ((array) $objdev->array AS $j=> $objd)
			{
				if ($objd->gestion == $_SESSION['gestion'])
				{
					if ($lAdvance)
					{
						if (!empty($objd->invoice))
							$nSumpay+=$objd->amount;
						else
							$nSumpayadv+=$objd->amount;
					}
					else
						$nSumpay+=$objd->amount;
				}
				$tagid = 'fichepay'.$objd->id;
				$iddev = $objd->id;
				//incluimos el registro para ver
				$action = '';
				include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/fiche_payment.tpl.php';
			}
			//para un registro nuevo
			if ($user->rights->poa->deve->crear)
			{
				$tagid = 'fichepay0_'.$i;
				//$iddev = $objd->id;
				//incluimos el registro para ver
				$action = 'create';
				include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/fiche_payment.tpl.php';
			}
			$lfpay++;
		}
	}

	//orden de proceder
	print '<div class="col-md-4">';
	print '<div class="box box-primary direct-chat direct-chat-primary collapsed-box">';
	print '<div class="box-header with-border">';
	print '<h3 class="box-title">'.$langs->trans('Orderproceed').'</h3>';
	print '<div class="box-tools pull-right">';
	//print '<span data-toggle="tooltip" title="'.$nApp.' '.$langs->trans('Desigments').'" class="badge bg-red">'.$nApp.'</span>';
	print '<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>';
	//	print '<button class="btn btn-box-tool" data-toggle="tooltip" title="'.$langs->trans('Messages').'" data-widget="chat-pane-toggle"><i class="fa fa-comments"></i></button>';
	print '</div>';
	print '</div>';
	print '<div class="box-body" style="display: none;">';
	include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/orderprobox.tpl.php';
	print '</div>'; //box-body
	print '</div>'; //box
	print '</div>'; //col-md-4

	if ($idProcess>0)
	{
		foreach((array) $aContrat AS $i => $ni)
		{
			$idrcreg = $aProcesscontrat[$i];
			$objpcon->fetch($idrcreg);
			$objcont->fetch($i);
			$res=$objcont->fetch_optionals($i,$extralabels);
			$tagid = 'ficheorderpro'.$aProcesscontrat[$i];
			$tagidp = 'ficheorderprop'.$aProcesscontrat[$i];
			$tagidd = 'ficheorderprod'.$aProcesscontrat[$i];
			include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/fiche_orderpro.tpl.php';
			include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/fiche_orderprop.tpl.php';
			include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/fiche_orderprod.tpl.php';
		}
	}

	
	//pagos
	print '<div class="col-md-4">';
	print '<div class="box box-primary direct-chat direct-chat-primary collapsed-box">';
	print '<div class="box-header with-border">';
	print '<h3 class="box-title">'.$langs->trans('Payments').'</h3>';
	print '<div class="box-tools pull-right">';
	print '<span data-toggle="tooltip" title="'.$nPay.' '.$langs->trans('Payments').' '.$langs->trans('Total').' '.price($nSumpay).' '.$langs->trans('Advance').' '.price($nSumpayadv).'" class="badge bg-red">'.$nPay.'</span>';
	print '<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>';
	//print '<button class="btn btn-box-tool" data-toggle="tooltip" title="'.$langs->trans('Messages').'" data-widget="chat-pane-toggle"><i class="fa fa-comments"></i></button>';
	print '</div>';
	print '</div>';
	print '<div class="box-body" style="display: none;">';
	include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/paybox.tpl.php';
	print '</div>'; //box-body
	print '</div>'; //box
	print '</div>'; //col-md-4

	print '</div>'; //row

	//option for timeline
	//print '<div class="row">';
	//print '<div class="col-md-12">';
	//print '<ul class="timeline">';

	//designations
	if ($idProcess > 0 && $lViewcontrat && $abc)
	{
		print '<li>';
		print '<div class="timeline-item">';
		print '<div class="box box-solid bg-light-blue">';
		print '<h3>'.$langs->trans('Designations').'</h3>';
		print '<div class="inner">';

		print '<table class="table">';
		print '<thead>';
		print '<tr>';
		print_liste_field_titre($langs->trans("Type"),"", "","","",'align="center" ');
		print_liste_field_titre($langs->trans("Name"),"", "","","",'align="center" ');
		print_liste_field_titre($langs->trans("Date"),"", "","","",'align="center" ');
		print '</tr>';
		print '</thead>';
		print '<tbody>';
		$a = true;
		foreach((array) $aContrat AS $i => $ni)
		{
			//contrato
			$objcont->fetch($i);
			$a = !$a;
			print "<tr>";
			print '<td colspan="3">';
			if (!empty($aContratname[$i]))
				print '<a  class="btn btn-primary btn-sm bg-light-blue" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$aContratname[$i].'</a>';
			else
				print '<a  class="btn btn-primary btn-sm bg-light-blue" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$obj->ref.'</a>';
			print '&nbsp;';
			print $aSocname[$objcont->fk_soc];
			if ($user->rights->poa->appoint->crear)
			{
				if ($user->admin || $objact->statut>0 && $objact->statut < 9)
				{
					print '&nbsp;<a  class="btn btn-primary btn-sm bg-light-blue" href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&idc='.$i.'	&idpro='.$idProcess.'&action=createdesign'.'&dol_hide_leftmenu=1">'.img_picto($langs->trans('New'),'edit_add').'</a>';
				}
			}
			print '</td>';
			print '</tr>';

			//lista designaciones appoint
			$objapp->getlist($i);
			if (count($objapp->array)>0) $a = !$a;
			foreach ((array) $objapp->array AS $j=> $objg)
			{
				//type guarantee
				print "<tr>";
				print '<td>'. select_code_appoint($objg->code_appoint,'code_appoint','',0,1);
				print '</td>';
				//user
				print '<td>';
				$res = $objuser->fetch($objg->fk_user);
				if ($res > 0 && $objuser->id == $objg->fk_user)
					print '<a  class="btn btn-primary btn-sm bg-light-blue" href="'.DOL_URL_ROOT.'/poa/appoint/fiche.php?id='.$objg->id.'&idpro='.$idProcess.'">'.$objuser->lastname.' '.$objuser->firstname.'</a>';
				print '</td>';
				//date
				print '<td>';
				print dol_print_date($objg->date_appoint,'day');
				print '</td>';
				print '</tr>';
			}
			if ($user->rights->poa->appoint->crear && $action == 'createdesign')
			{
				if ($user->admin || $objact->statut>0 && $objact->statut < 9)
				{
					$action = create;
					include DOL_DOCUMENT_ROOT.'/poa/appoint/tpl/fiche.tpl.php';
				}
			}
		}
		print '</tbody>';
		print '</table>';
		print '</div>';
		print '</div>';
		print '</div>';
	}


	//receptions
	if ($idProcess > 0 && $lViewcontrat && $abc)
	{
		$a = true;
		$lAddcontrat = false;
		if (count($aContrat) <= 0)
			if ($objproc->statut > 0) $lAddcontrat = true;

		print '<li>';
		print '<div class="timeline-item">';
		print '<div class="box box-solid bg-purple">';
		print '<h3>'.$langs->trans('Reception').'</h3>';
		print '<div class="table-responsive">';
		print '<table class="table table-condensed">';

		foreach((array) $aContrat AS $i => $ni)
		{
			$objcont->fetch($i);
			$objcont->fetch_lines();
			$a = !$a;
			$lClosecontrat = true;
			$date_cloture = '';
			foreach ((array) $objcont->lines AS $k => $objl)
			{
				$objcontline = new Contratligne($db);
				$objcontline->fetch($objl->id);
				if ($objcontline->id == $objl->id)
				{
					$fk_cl = $objcontline->id;
					$date_ouverture = $objcontline->date_ouverture;
					$date_fin_validite = $objcontline->date_fin_validite;
					$date_cloture = $objcontline->date_cloture;
					if (empty($objcontline->date_cloture) || is_null($objcontline->date_cloture))
						$lClosecontrat = false;
				}
				else
					$lClosecontrat = false;
			}
			if ($lClosecontrat)
			{
				//imprimimos
			}

			print "<tr>";
			print '<td>';
			if (!empty($aContratname[$i]))
				print '<a class="btn btn-primary btn-sm bg-purple" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$aContratname[$i].'</a>';
			else
				print '<a class="btn btn-primary btn-sm btn.bg-purple" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$objcont->ref.'</a>';
			print '</td>';
			print '<td width="20%">';
			print $langs->trans('Ini').' '.dol_print_date($date_ouverture,'day');
			print '</td>';
			print '<td width="20%">';
			print $langs->trans('Fin').' '.dol_print_date($date_fin_validite,'day');
			print '</td>';
			print '<td width="20%">';
			print $langs->trans('Recep.').' '.dol_print_date($date_cloture,'day');
			print '</td>';
			print '</tr>';
		}
		print '</table>';
		print '</div>';
		print '</div>';
		print '</div>';
		print '</li>';
	}

	//guarantees
	if ($idProcess > 0 && $lViewcontrat && $abc)
	{
		print '<li>';
		print '<div class="timeline-item">';
		print '<div class="box box-solid bg-navy">';
		print '<h3>'.$langs->trans('Guarantees').'</h3>';
		print '<div class="inner">';
		print '<table class="table">';
		print '<thead>';
		print '<tr>';
		print_liste_field_titre($langs->trans("Type"),"", "","","",'align="center" ');
		print_liste_field_titre($langs->trans("Ref"),"", "","","",'align="center" ');
		print_liste_field_titre($langs->trans("Issuer"),"", "","","",'align="center" ');
		print_liste_field_titre($langs->trans("Dateinicio"),"", "","","",'align="center" ');
		print_liste_field_titre($langs->trans("Datefinal"),"", "","","",'align="center" ');
		print_liste_field_titre($langs->trans("Amount"),"", "","","",'align="center" ');
		print '</tr>';
		print '</thead>';
		print '<tbody>';
		$a = true;
		foreach((array) $aContrat AS $i => $ni)
		{
			//contrato
			$objcont->fetch($i);
			$a = !$a;
			$aContratname[$i] = $objcont->array_options['options_ref_contrato'];
			$aContratcode[$i] = $contratAdd;
			print "<tr>";
			print '<td colspan="6">';
			if (!empty($aContratname[$i]))
				print '<a class="btn btn-primary btn-sm bg-navy" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$aContratname[$i].'</a>';
			else
				print '<a class="btn btn-primary btn-sm bg-navy" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$obj->ref.'</a>';
			print '&nbsp;';
			print $aSocname[$objcont->fk_soc];
			//agregamos new para crear
			if ($user->rights->poa->guar->crear)
			{
				if ($user->admin || $objact->statut>0 && $objact->statut < 9)
				{
					print '&nbsp;<a class="btn btn-primary btn-sm bg-navy" href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&idpro='.$idProcess.'&fk_contrat='.$i.'&action=createguarantee'.'&dol_hide_leftmenu=1">'.img_picto($langs->trans('New'),'edit_add').'</a>';
				}
			}

			print '</td>';
			print '</tr>';
			//lista las garantias
			//garantias
			$objgua->getlist($i);
			if (count($objgua->array)>0) $a = !$a;
			foreach ((array) $objgua->array AS $j=> $objg)
			{
				//type guarantee
				print "<tr>";
				print '<td>'. select_code_guarantees($objg->code_guarantee,'code_guarantee','',0,1);
				print '</td>';
				//Ref
				print '<td>'.'<a class="btn btn-primary btn-sm bg-navy" href="'.DOL_URL_ROOT.'/poa/guarantees/fiche.php?id='.$objg->id.'&idpro='.$idProcess.'">'.$objg->ref.'</a>';
				print '</td>';
				//Issuer
				print '<td>'.$objg->issuer;
				print '</td>';
				// //dateini
				print '<td>'.dol_print_date($objg->date_ini,'day');
				print '</td>';
				//datefin
				print '<td>'.dol_print_date($objg->date_fin,'day');
				print '</td>';
				//amount
				print '<td align="right">'.price($objg->amount);
				print '</td>';
				print '</tr>';
			}
			//registro nuevo
			if ($action == 'createguarantee' && $i == GETPOST('fk_contrat'))
			{
				include DOL_DOCUMENT_ROOT.'/poa/guarantees/tpl/fiche.tpl.php';
			}
		}
		print '</tbody>';
		print '</table>';
		print '</div>';
		print '</div>';
		print '</div>';
		print '</li>';
	}
}
else
{
	if ($objact->amount > 0 && $objact->statut == 1)
	{
		print '<!-- Apply any bg-* class to to the info-box to color it -->';
		print '<div class="info-box bg-red">';
		print '<span class="info-box-icon"><i class="fa fa-cog fa-spin fa-1x fa-fw"></i>';
		print '</span>';
		print '<div class="info-box-content">';
		print '<span class="info-box-text">';
		print '<button class="btn btn-primary btn-lg bg-red" href="#fichepreventive" role="button" data-toggle="modal">'.$langs->trans('New').'</button>';
		print ' '.$langs->trans('Preventive');
		print '</span>';
		print '<!-- The progress section is optional -->';
		print '</div>';
		print '</div><!-- /.info-box-content -->';
		print '</div><!-- /.info-box -->';
		//insertamos el formulario para cargar contratos
		include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/fiche_preventive.tpl.php';
		print '</div>';
	}
}

print '</section>';

include DOL_DOCUMENT_ROOT.'/poa/execution/lib/js.lib.php';


$db->close();


print '<form method="GET" action="'.DOL_URL_ROOT.'/poa/ind.php'.'">';
print '<button class="botonF1">';
print '<span>'.$langs->trans('Return').'</span>';
print '</button>';
print '</form>';

llxFooter();

?>
