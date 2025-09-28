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
 *	\file       htdocs/contab/seats/fiche.php
 *	\ingroup    Asiento manual
 *	\brief      Page fiche contab seats
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/client.class.php';

require_once DOL_DOCUMENT_ROOT.'/contab/class/contabaccountingext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabperiodoext.class.php';

//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

//require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/lib/contab.lib.php");

//asientos
require_once DOL_DOCUMENT_ROOT."/contab/class/contabseatext.class.php";
require_once DOL_DOCUMENT_ROOT."/contab/class/contabseatdetext.class.php";
require_once DOL_DOCUMENT_ROOT."/contab/class/contabseatflagext.class.php";
require_once DOL_DOCUMENT_ROOT."/contab/class/contabtransaction.class.php";
require_once DOL_DOCUMENT_ROOT."/fiscal/class/vdosingext.class.php";
require_once DOL_DOCUMENT_ROOT."/fiscal/class/tvadefext.class.php";
require_once DOL_DOCUMENT_ROOT."/societe/class/societe.class.php";


$langs->load("contab");

if (!$user->rights->contab->seat->read)
	accessforbidden();

$action=GETPOST('action');

$id        = GETPOST("id");

$date_startmonth= GETPOST('date_startmonth');
$date_startday  = GETPOST('date_startday');
$date_startyear = GETPOST('date_startyear');
$date_endmonth  = GETPOST('date_endmonth');
$date_endday    = GETPOST('date_endday');
$date_endyear   = GETPOST('date_endyear');
$group_seat     = GETPOST('group_seat');
$codtr 			= GETPOST('codtr');
$date_seatmonth = GETPOST('date_seatmonth');
$date_seatday   = GETPOST('date_seatday');
$date_seatyear  = GETPOST('date_seatyear');
$history = GETPOST('history');
$fk_v_dosing = GETPOST('fk_v_dosing');
$lProcesaSeat   = false;

// Security check
if ($user->societe_id > 0) $socid = $user->societe_id;
// if (! empty($conf->comptabilite->enabled)) $result=restrictedArea($user,'compta','','','resultat');
// if (! empty($conf->accounting->enabled)) $result=restrictedArea($user,'accounting','','','comptarapport');

$year_current = strftime("%Y",dol_now());
$pastmonth = strftime("%m",dol_now()) - 1;
$pastmonthyear = $year_current;

if ($pastmonth == 0)
{
	$pastmonth = 12;
	$pastmonthyear--;
}

$date_seat=dol_mktime(0, 0, 0, $date_seatmonth, $date_seatday, $date_seatyear);
$date_start=dol_mktime(0, 0, 0, GETPOST('date_startmonth'), GETPOST('date_startday'), GETPOST('date_startyear'));
$date_end=dol_mktime(23, 59, 59, GETPOST('date_endmonth'), GETPOST('date_endday'), GETPOST('date_endyear'));

if (empty($date_start) || empty($date_end))
// We define date_start and date_end
{
	$date_start=dol_get_first_day($pastmonthyear,$pastmonth,false); $date_end=dol_get_last_day($pastmonthyear,$pastmonth,false);
}

$mesg = '';

$object = new Contabseatext($db);
$objVdosing = new Vdosingext($db);
$objTvadef = new Tvadefext($db);
$mysoc = new Societe($db);
$objContabTrans = new Contabtransaction($db);
$form=new Form($db);

$type_seat = 3;
$filter = " AND t.status = 1 AND t.entity = ".$conf->entity;
$filter.= " AND t.type_seat = ".$type_seat;
$res = $objContabTrans->fetchAll('ASC','ref',0,0,array(),'AND',$filter);
$optionstrans = '<option value="">'.$langs->trans('Selecttypetransaction').'</option>';
if ($res >0)
{
	$lines = $objContabTrans->lines;
	foreach ($lines AS $j => $line)
	{
		$selected = '';
		if ($codtr == $line->ref)
		{
			$selected = ' selected';
		}
		$optionstrans.= '<option value="'.$line->ref.'" '.$selected.'>'.$line->ref.' '.$line->label.'</option>';
	}
}

/*
 * Actions
 */
if (($action == 'save' ) && ! isset($_POST["cancel"]) && $user->rights->contab->seat->write)
{
	include_once DOL_DOCUMENT_ROOT."/contab/lib/addseats.lib.php";
	if (empty($error))
	{
		$mesg = '<div>'.$langs->trans('Proceso concluido').'</div>';
		$action = 'fin';
	}
	else
	{
		$mesg = '<div>'.$langs->trans('Error, en el Proceso').'</div>';
		$action = 'create';
	}
}

//generate
if ($action == 'generate' && $user->rights->contab->seat->write)
{

	//validamos el periodo
	$objPeriod = new Contabperiodoext($db);
	$return = $objPeriod->fetch_open($date_seatmonth,$date_seatyear,$date_seat);
	if ($return != 1)
	{
		$error++;
		$mesg='<div class="error">'.$langs->trans('Errorperiodclosenotvalidated').'</div>';
		$action = 'create';
	}
	else
	{

		//objtenemos las cuentas contables de iva y transacciones de la tabla tva_def
		$filtertva = " AND t.entity = ".$conf->entity;
		$filtertva.= " AND t.code_facture = '".$conf->global->FISCAL_CODE_FACTURE_SALES."'";
		$filtertva.= " AND t.active = 1";
		$restva = $objTvadef->fetchAll('','',0,0,array(),'AND',$filtertva);
		if ($restva > 0)
		{
			$lines = $objTvadef->lines;
			foreach ($lines AS $j => $line)
			{
				if ($line->code_tva == 'IVA') $compta_tva = $line->accountancy_code;
				if ($line->code_tva == 'IT')
				{
					$compta_localtax1 = $line->accountancy_code;
					$compta_against_localtax1 = $line->against_account;
				}
			}
		}

		$seat_month = $date_seatmonth;
		$seat_year  = $date_seatyear;
		$lote = '00200';
		$sblote = '001';

		$nom=$langs->trans("SellsJournal");
		$nomlink='';
		$periodlink='';
		$exportlink='';
		$builddate=time();
		$description=$langs->trans("DescSellsJournal").'<br>';
		if (! empty($conf->global->FACTURE_DEPOSITS_ARE_JUST_PAYMENTS)) $description.= $langs->trans("DepositsAreNotIncluded");
		else  $description.= $langs->trans("DepositsAreIncluded");
		$period=$form->select_date($date_start,'date_start',0,0,0,'',1,0,1).' - '.$form->select_date($date_end,'date_end',0,0,0,'',1,0,1);
		//report_header($nom,$nomlink,$period,$periodlink,$description,$builddate,$exportlink);

		$p = explode(":", $conf->global->MAIN_INFO_SOCIETE_COUNTRY);
		$idpays = $p[0];

		$sql = "SELECT f.rowid, f.facnumber, f.type, f.datef, f.ref_client,";
		$sql.= " fd.product_type, fd.total_ht, fd.total_tva, fd.tva_tx, fd.total_ttc, fd.localtax1_tx, fd.localtax2_tx, fd.total_localtax1, fd.total_localtax2,";
		$sql.= " s.rowid as socid, s.nom as name, s.code_compta, s.client,";
		$sql.= " p.rowid as pid, p.ref as pref, p.accountancy_code_sell,";
		$sql.= " ct.accountancy_code_sell as account_tva, ct.recuperableonly";
		$sql.= " FROM ".MAIN_DB_PREFIX."facturedet AS fd";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product AS p ON p.rowid = fd.fk_product";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."facture AS f ON f.rowid = fd.fk_facture";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."v_fiscal AS vf ON f.rowid = vf.fk_facture";

		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."societe s ON s.rowid = f.fk_soc";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_tva ct ON fd.tva_tx = ct.taux AND fd.info_bits = ct.recuperableonly AND ct.fk_pays = '".$idpays."'";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."contab_seat_flag AS csf ON f.rowid = csf.table_id ";

		$sql.= " WHERE f.entity = ".$conf->entity;
		$sql.= " AND f.fk_statut > 0";
		$sql.= " AND ( (csf.table_nom = ''  OR csf.table_nom IS NULL) OR csf.table_nom != '".MAIN_DB_PREFIX."facture' )";

		if (! empty($conf->global->FACTURE_DEPOSITS_ARE_JUST_PAYMENTS)) $sql.= " AND f.type IN (0,1,2)";
		else $sql.= " AND f.type IN (0,1,2,3)";
		$sql.= " AND fd.product_type IN (0,1)";
		if ($date_start && $date_end) $sql .= " AND f.datef >= '".$db->idate($date_start)."' AND f.datef <= '".$db->idate($date_end)."'";
		if ($fk_v_dosing) $sql.= " AND vf.fk_dosing = ".$fk_v_dosing;
		if ($group_seat == 1)
			$sql.= " ORDER BY f.rowid";
		if ($group_seat == 2)
			$sql.= " ORDER BY f.datef";
		//echo $sql;
		dol_syslog("sql=".$sql);
		$result = $db->query($sql);
		if ($result)
		{
		//por dia
			$tabday = array();
			$tabhtday = array();
			$tabtvaday = array();
			$tablocaltax1day = array();
			$tablocaltax2day = array();
			$tabttcday = array();
			$tabcompanyday = array();

		//por periodo
			$tabper          = array();
			$tabhtper        = array();
			$tabtvaper       = array();
			$tablocaltax1per = array();
			$tablocaltax2per = array();
			$tabttcper       = array();
			$tabcompanyper   = array();

		//por documento
			$tabfac = array();
			$tabht = array();
			$tabtva = array();
			$tablocaltax1 = array();
			$tablocaltax2 = array();
			$tabttc = array();
			$tabcompany = array();
			$account_localtax1=0;
			$account_localtax2=0;

			$num = $db->num_rows($result);
			$i=0;
			$resligne=array();
			$_SESSION['table_nom'] = 'facture';
			while ($i < $num)
			{
				$obj = $db->fetch_object($result);
				$mysoc->fetch($obj->socid);
				// les variables
				$cptcli = (! empty($conf->global->ACCOUNTING_ACCOUNT_CUSTOMER)?$conf->global->ACCOUNTING_ACCOUNT_CUSTOMER:$langs->trans("CodeNotDef"));
				$compta_soc = (! empty($obj->code_compta)?$obj->code_compta:$cptcli);
				$compta_prod = $obj->accountancy_code_sell;
				if (empty($compta_prod))
				{
					if($obj->product_type == 0) $compta_prod = (! empty($conf->global->ACCOUNTING_PRODUCT_SOLD_ACCOUNT)?$conf->global->ACCOUNTING_PRODUCT_SOLD_ACCOUNT:$langs->trans("CodeNotDef"));
						else $compta_prod = (! empty($conf->global->ACCOUNTING_SERVICE_SOLD_ACCOUNT)?$conf->global->ACCOUNTING_SERVICE_SOLD_ACCOUNT:$langs->trans("CodeNotDef"));
						}
						$cpttva = (! empty($conf->global->COMPTA_VAT_ACCOUNT)?$conf->global->COMPTA_VAT_ACCOUNT:$langs->trans("CodeNotDef"));
						//$compta_tva = (! empty($obj->account_tva)?$obj->account_tva:$cpttva);
						//echo '<hr>'.$obj->tva_tx;
						$account_localtax1=getLocalTaxesFromRate($obj->tva_tx, 1, $mysoc,$mysoc);

						//$compta_localtax1= (! empty($account_localtax1[3])?$account_localtax1[3]:$langs->trans("CodeNotDef"));
						$account_localtax2=getLocalTaxesFromRate($obj->tva_tx, 2, $mysoc,$mysoc);
						//print_r($account_localtax2);exit;
						$compta_localtax2= (! empty($account_localtax2[3])?$account_localtax2[3]:$langs->trans("CodeNotDef"));

						//la ligne facture
						$tabfac[$obj->rowid]["date"] = $obj->datef;
						$tabfac[$obj->rowid]["ref"] = $obj->facnumber;
						$tabfac[$obj->rowid]["type"] = $obj->type;
						if (! isset($tabttc[$obj->rowid][$compta_soc])) $tabttc[$obj->rowid][$compta_soc]=0;
						if (! isset($tabht[$obj->rowid][$compta_prod])) $tabht[$obj->rowid][$compta_prod]=0;
						if (! isset($tabtva[$obj->rowid][$compta_tva])) $tabtva[$obj->rowid][$compta_tva]=0;
						if (! isset($tablocaltax1[$obj->rowid][$compta_localtax1])) $tablocaltax1[$obj->rowid][$compta_localtax1]=0;
						if (! isset($tablocaltax1aa[$obj->rowid][$compta_against_localtax1])) $tablocaltax1aa[$obj->rowid][$compta_against_localtax1]=0;
						if (! isset($tablocaltax2[$obj->rowid][$compta_localtax2])) $tablocaltax2[$obj->rowid][$compta_localtax2]=0;
						$obj->rowid.' totalttc '.$tabttc[$obj->rowid][$compta_soc] += $obj->total_ttc;
						$tabht[$obj->rowid][$compta_prod] += $obj->total_ht;
						if($obj->recuperableonly != 1) $tabtva[$obj->rowid][$compta_tva] += $obj->total_tva;
						$tablocaltax1[$obj->rowid][$compta_localtax1] += $obj->total_localtax1;
						$tablocaltax1aa[$obj->rowid][$compta_against_localtax1] += $obj->total_localtax1;

						$tablocaltax2[$obj->rowid][$compta_localtax2] += $obj->total_localtax2;
						$tabcompany[$obj->rowid]=array('id'=>$obj->socid, 'name'=>$obj->name, 'client'=>$obj->client);

		//por dia
						$tabday[$obj->datef]['date'] = $obj->datef;
						$tabday[$obj->datef]['type'] = $obj->type;

		// if (! isset($tabttcday[$obj->datef][$compta_soc][$obj->socid])) $tabttcday[$obj->datef][$compta_soc][$obj->socid]=0;
		// if (! isset($tabhtday[$obj->datef][$compta_prod])) $tabhtday[$obj->datef][$compta_prod]=0;
		// if (! isset($tabtvaday[$obj->datef][$compta_tva])) $tabtvaday[$obj->datef][$compta_tva]=0;
		// if (! isset($tablocaltax1day[$obj->datef][$compta_localtax1])) $tablocaltax1day[$obj->datef][$compta_localtax1]=0;
		// if (! isset($tablocaltax2day[$obj->datef][$compta_localtax2])) $tablocaltax2day[$obj->datef][$compta_localtax2]=0;

						if (! isset($tabttcday[$obj->datef][$compta_soc])) $tabttcday[$obj->datef][$compta_soc]=0;
						if (! isset($tabhtday[$obj->datef][$compta_prod])) $tabhtday[$obj->datef][$compta_prod]=0;
						if (! isset($tabtvaday[$obj->datef][$compta_tva])) $tabtvaday[$obj->datef][$compta_tva]=0;
						if (! isset($tablocaltax1day[$obj->datef][$compta_localtax1])) $tablocaltax1day[$obj->datef][$compta_localtax1]=0;
						if (! isset($tablocaltax1dayaa[$obj->datef][$compta_against_localtax1])) $tablocaltax1dayaa[$obj->datef][$compta_against_localtax1]=0;
						if (! isset($tablocaltax2day[$obj->datef][$compta_localtax2])) $tablocaltax2day[$obj->datef][$compta_localtax2]=0;

		// $tabttcday[$obj->datef][$compta_soc][$obj->socid] += $obj->total_ttc;
		// $tabhtday[$obj->datef][$compta_prod] += $obj->total_ht;
		// if($obj->recuperableonly != 1) $tabtvaday[$obj->datef][$compta_tva] += $obj->total_tva;
		// $tablocaltax1day[$obj->datef][$compta_localtax1] += $obj->total_localtax1;
		// $tablocaltax2day[$obj->datef][$compta_localtax2] += $obj->total_localtax2;
		// $tabcompanyday[$obj->datef][$obj->socid]=array('id'=>$obj->socid, 'name'=>$obj->name, 'client'=>$obj->client);

						$tabttcday[$obj->datef][$compta_soc] += $obj->total_ttc;
						$tabhtday[$obj->datef][$compta_prod] += $obj->total_ht;
						if($obj->recuperableonly != 1) $tabtvaday[$obj->datef][$compta_tva] += $obj->total_tva;
						$tablocaltax1day[$obj->datef][$compta_localtax1] += $obj->total_localtax1;
						$tablocaltax1dayaa[$obj->datef][$compta_against_localtax1] += $obj->total_localtax1;
						$tablocaltax2day[$obj->datef][$compta_localtax2] += $obj->total_localtax2;
						$tabcompanyday[$obj->datef][$obj->socid]=array('id'=>$obj->socid, 'name'=>$obj->name, 'client'=>$obj->client);

		//por periodo //agrupado por mes
						list($anio,$mes,$dia) = explode('-',$obj->datef);
						$mesanio = $mes.$anio;
						$tabper[$mesanio]['date'] = $mesanio;
						$tabper[$mesanio]['type'] = $obj->type;
						$tabper[$mesanio]['mes'] = $mes;
						$tabper[$mesanio]['anio'] = $anio;

						if (! isset($tabttcper[$mesanio][$compta_soc])) $tabttcper[$mesanio][$compta_soc]=0;
						if (! isset($tabhtper[$mesanio][$compta_prod])) $tabhtper[$mesanio][$compta_prod]=0;
						if (! isset($tabtvaper[$mesanio][$compta_tva])) $tabtvaper[$mesanio][$compta_tva]=0;
						if (! isset($tablocaltax1per[$mesanio][$compta_localtax1])) $tablocaltax1per[$mesanio][$compta_localtax1]=0;
						if (! isset($tablocaltax1peraa[$mesanio][$compta_against_localtax1])) $tablocaltax1peraa[$mesanio][$compta_against_localtax1]=0;
						if (! isset($tablocaltax2per[$mesanio][$compta_localtax2])) $tablocaltax2per[$mesanio][$compta_localtax2]=0;

						$tabttcper[$mesanio][$compta_soc] += $obj->total_ttc;
						$tabhtper[$mesanio][$compta_prod] += $obj->total_ht;
						if($obj->recuperableonly != 1) $tabtvaper[$mesanio][$compta_tva] += $obj->total_tva;
						$tablocaltax1per[$mesanio][$compta_localtax1] += $obj->total_localtax1;
						$tablocaltax1peraa[$mesanio][$compta_against_localtax1] += $obj->total_localtax1;
						$tablocaltax2per[$mesanio][$compta_localtax2] += $obj->total_localtax2;
						$tabcompanyper[$mesanio][$obj->socid]=array('id'=>$obj->socid, 'name'=>$obj->name, 'client'=>$obj->client);

						if ($group_seat == 1)
							$aArrayTabDoc[$obj->rowid][$obj->rowid] = $obj->rowid;
						elseif ($group_seat == 2)
							$aArrayTabDoc[$obj->datef][$obj->rowid] = $obj->rowid;
						elseif ($group_seat == 3)
							$aArrayTabDoc[$mesanio][$obj->rowid] = $obj->rowid;

		// if (! isset($tabttcper[$mesanio][$compta_soc][$obj->socid])) $tabttcper[$mesanio][$compta_soc][$obj->socid]=0;
		// if (! isset($tabhtper[$mesanio][$compta_prod])) $tabhtper[$mesanio][$compta_prod]=0;
		// if (! isset($tabtvaper[$mesanio][$compta_tva])) $tabtvaper[$mesanio][$compta_tva]=0;
		// if (! isset($tablocaltax1per[$mesanio][$compta_localtax1])) $tablocaltax1per[$mesanio][$compta_localtax1]=0;
		// if (! isset($tablocaltax2per[$mesanio][$compta_localtax2])) $tablocaltax2per[$mesanio][$compta_localtax2]=0;

		// $tabttcper[$mesanio][$compta_soc][$obj->socid] += $obj->total_ttc;
		// $tabhtper[$mesanio][$compta_prod] += $obj->total_ht;
		// if($obj->recuperableonly != 1) $tabtvaper[$mesanio][$compta_tva] += $obj->total_tva;
		// $tablocaltax1per[$mesanio][$compta_localtax1] += $obj->total_localtax1;
		// $tablocaltax2per[$mesanio][$compta_localtax2] += $obj->total_localtax2;
		// $tabcompanyper[$mesanio][$obj->socid]=array('id'=>$obj->socid, 'name'=>$obj->name, 'client'=>$obj->client);

						$i++;
					}
				}
				else {
					dol_print_error($db);
				}
				$action = 'list';
			}
		}
		if ($_POST["cancel"] == $langs->trans("Cancel"))
		{
			$action = '';
			$_GET["id"] = $_POST["id"];
		}


//verificamos parametros que es necesario definir
		if (empty($conf->global->CONTAB_ACCOUNT_CAPITAL) ||
			empty($conf->global->CONTAB_PAYABLES_UNDEFINED) ||
			empty($conf->global->CONTAB_RECEIVABLES_UNDEFINED))
		{
			$mesg = '<div class="error">'.$langs->trans('Youneedtodefinetheparametersforprocessing').' <br>CONTAB_ACCOUNT_CAPITAL<br>CONTAB_PAYABLES_UNDEFINED<br>CONTAB_RECEIVABLES_UNDEFINED'.'</div>';
			dol_htmloutput_mesg($mesg);
			exit;
		}
//revision de numeracion de asiento
		if (empty($conf->global->CONTAB_TSE_TYPENUMERIC) ||
			empty($conf->global->CONTAB_TSE_EGRESO) ||
			empty($conf->global->CONTAB_TSE_INGRESO) ||
			empty($conf->global->CONTAB_TSE_TRASPASO))
		{
			$mesg = '<div class="error">'.$langs->trans('Youneedtodefinetheparametersforprocessing').' <br>CONTAB_TSE_TYPENUMERIC<br>CONTAB_TSE_EGRESO<br>CONTAB_TSE_INGRESO<br>CONTAB_TSE_TRASPASO'.'</div>';
	//dol_htmloutput_mesg($mesg);
	//exit;
		}


/*
 * View
 */

$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementseats"),$help_url);

//variables fijas para asietnos de venta
//$type_seat = 2;
$loked = 1;

$filter = " AND t.status = 1 AND t.entity = ".$conf->entity;
$res = $objVdosing->fetchAll('ASC','num_autoriz',0,0,array(),'AND',$filter);
$options= '<option value="">'.$langs->trans('Select').'</option>';
if ($res >0)
{
	$lines = $objVdosing->lines;
	foreach ($lines AS $j => $line)
	{
		$options.= '<option value="'.$line->id.'">'.$line->num_autoriz.' '.$line->serie.'</option>';
	}
}

if ($action == 'create' && $user->rights->contab->seat->write)
{
	print_fiche_titre($langs->trans("Newseatssales"));

	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="generate">';
	print '<input type="hidden" name="type_seat" value="'.$type_seat.'">';
	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// date seat
	print '<tr><td class="fieldrequired">'.$langs->trans('Dateseat').'</td><td colspan="2">';
	$form->select_date($date_seat,'date_seat','','','',"crea_seat",1,1);
	print '</td></tr>';
	//lote sblote doc
	print '<tr><td class="fieldrequired">'.$langs->trans('Number').'</td><td colspan="2">';
	print $object->lote.' '.$object->sblote.' '.$object->doc;
	print '</td></tr>';

	//group by seats
	print '<tr><td class="fieldrequired">'.$langs->trans('Generatesseat').'</td><td colspan="2">';
	print select_group_seats($group_seat,'group_seat','','',1);
	print '</td></tr>';

	//Dosificación
	print '<tr><td class="fieldrequired">'.$langs->trans('Dosing').'</td><td colspan="2">';
	print '<select name="fk_v_dosing">'.$options.'</select>';
	print '</td></tr>';
	//type transaction
	print '<tr><td class="fieldrequired">'.$langs->trans('Typetransaction').'</td><td colspan="2">';
	print '<select name="codtr" required>'.$optionstrans.'</select>';
	print '</td></tr>';

	//type seat
	print '<tr><td class="fieldrequired">'.$langs->trans('Typeseat').'</td><td colspan="2">';
	print select_type_seat($type_seat,'type_seat','','',1,$loked);
	print '</td></tr>';
	// date start
	print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
	$form->select_date($date_start,'date_start','','','',"crea_seat",1,1);
	print '</td></tr>';
	// date end
	print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
	$form->select_date($date_end,'date_end','','','',"crea_seat",1,1);
	print '</td></tr>';

	//history
	print '<tr><td class="fieldrequired">'.$langs->trans('Glosa').'</td><td colspan="2">';
	print '<input id="history" type="text" value="'.$object->history.'" name="history" size="38">';
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($action == 'list')
	{
		// Show result array
		$lFlag = true;
		//procesar asientos
		$sumaDebe = 0;
		$sumaHaber = 0;
		if ($group_seat == 1)
		{
			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
			//print "<td>".$langs->trans("JournalNum")."</td>";
			print '<td>'.$langs->trans('Date').'</td>';
			print '<td>'.$langs->trans('Piece').' ('.$langs->trans('InvoiceRef').')</td>';
			print '<td>'.$langs->trans('Account').'</td>';
			print '<td>'.$langs->trans('Type').'</td>';
			print '<td align="right">'.$langs->trans('Debit').'</td>';
			print '<td align="right">'.$langs->trans('Credit').'</td>';
			print "</tr>\n";

			$var=true;

			$invoicestatic=new Facture($db);
			$companystatic=new Client($db);

			foreach ($tabfac as $key => $val)
			{
				$invoicestatic->id=$key;
				$invoicestatic->ref=$val["ref"];
				$invoicestatic->type=$val["type"];

				$companystatic->id=$tabcompany[$key]['id'];
				$companystatic->name=$tabcompany[$key]['name'];
				$companystatic->client=$tabcompany[$key]['client'];

				$lines = array(
					array(
						'var' => $tabttc[$key],
						'label' => $langs->trans('ThirdParty'),
						'nomtcheck' => true,
						'inv' => true
					),
					array(
						'var' => $tablocaltax1aa[$key],
						'label' => $langs->trans('LT1gasto'),
						'inv' => true
					),
					array(
						'var' => $tabht[$key],
						'label' => $langs->trans('Products'),
					),
					array(
						'var' => $tabtva[$key],
						'label' => $langs->trans('VAT')
					),
					array(
						'var' => $tablocaltax1[$key],
						'label' => $langs->transcountry('LT1', $mysoc->country_code)
					),
					array(
						'var' => $tablocaltax2[$key],
						'label' => $langs->transcountry('LT2', $mysoc->country_code)
					)
				);

				foreach ($lines as $line)
				{
					foreach ($line['var'] as $k => $mt)
					{
						if (isset($line['nomtcheck']) || $mt)
						{
							print "<tr ".$bc[$var]." >";
							//print "<td>".$conf->global->COMPTA_JOURNAL_SELL."</td>";
							print "<td>".$val["date"]."</td>";
							print "<td>".$invoicestatic->getNomUrl(1)."</td>";
							print "<td>".$k."</td><td>".$line['label']."</td>";

							$aAsiento[$k]['date']      = $val['date'];
							$aAsiento[$k]['groupseat'] = $group_seat;
							$aAsiento[$k]['label']     = $line['label'].' '.$invoicestatic->ref;
							$aAsiento[$k]['account']   = $k;

							if (isset($line['inv']))
							{
								print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
								print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
								$aAsiento[$k]['deudor']   = ($mt>=0?$k:'');
								$aAsiento[$k]['acreedor'] = ($mt<0?$k:'');
								$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MT'):price2num($mt,'MT'));

								$sumaDebe  += ($mt>=0?$mt:0);
								$sumaHaber += ($mt<0?-$mt:0);
							}
							else
							{
								print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
								print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
								$aAsiento[$k]['deudor']   = ($mt<0?$k:'');
								$aAsiento[$k]['acreedor'] = ($mt>=0?$k:'');
								$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MT'):price2num($mt,'MT'));
								$sumaDebe  += ($mt<0?-$mt:0);
								$sumaHaber += ($mt>=0?$mt:0);
							}
							print "</tr>";
						}
					}
				}
				$aArraySeat[$key] = $aAsiento;
				$aAsiento = array();
				$var = !$var;
			}
			$classRead = 'liste_total';
			if (price($sumaDebe) != price($sumaHaber))
			{
				$classRead = 'bgread';
				$sep = True;
				$lProcesaSeat = false;
			}
			else
			{
				$lProcesaSeat = true;
			}
			print '<tr class="'.$classRead.'"><td align="left" colspan="4">';
			if ($sep) print '&nbsp;';
			else print $langs->trans("Equalsums");
			print '</td>';
			print '<td align="right" nowrap>'.price($sumaDebe).'</td>';
			print '<td align="right" nowrap>'.price($sumaHaber).'</td>';
			print '</tr>';

			print "</table>";
		}

		if ($group_seat == 2 )
		{
			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
			//print "<td>".$langs->trans("JournalNum")."</td>";
			print '<td>'.$langs->trans('Date').'</td>';
			//print '<td>'.$langs->trans('Piece').' ('.$langs->trans('InvoiceRef').')</td>';
			print '<td>'.$langs->trans('Account').'</td>';
			print '<td>'.$langs->trans('Type').'</td>';
			print '<td align="right">'.$langs->trans('Debit').'</td>';
			print '<td align="right">'.$langs->trans('Credit').'</td>';
			print "</tr>\n";

			$var=true;

			$invoicestatic=new Facture($db);
			$companystatic=new Client($db);

			foreach ($tabday as $key => $val)
			{
				$invoicestatic->id=$key;
				$invoicestatic->ref=$val["ref"];
				$invoicestatic->type=$val["type"];

				// $companystatic->id=$tabcompany[$key]['id'];
				// $companystatic->name=$tabcompany[$key]['name'];
				// $companystatic->client=$tabcompany[$key]['client'];
				if (count($tabttcday[$key]>1))
				{
					foreach($tabttcday[$key] AS $idx => $aValue)
					{
						$companystatic->id=$tabcompany[$idx]['id'];
						$companystatic->name=$tabcompany[$idx]['name'];
						$companystatic->client=$tabcompany[$idx]['client'];
						// foreach ($aValue AS $idSoc => $value)
						//   {
						//     $aProvis[$idx] = $value;

						$lines = array(
							array(
								'var' => $tabttcday[$key],
								'account' => $idx,
								'label' => $langs->trans('ThirdParty'),
								'nomtcheck' => true,
								'day' => true,
								'inv' => true
							),
							array(
								'var' => $tablocaltax1dayaa[$key],
								'account' => $compta_against_localtax1,
								'label' => $langs->trans('LT1gasto'),
								'day' => true,
								'inv' => true
							),
							array(
								'var' => $tabhtday[$key],
								'label' => $langs->trans('Products'),
							),
							array(
								'var' => $tabtvaday[$key],
								'label' => $langs->trans('VAT')
							),
							array(
								'var' => $tablocaltax1day[$key],
								'label' => $langs->transcountry('LT1', $mysoc->country_code)
							),
							array(
								'var' => $tablocaltax2day[$key],
								'label' => $langs->transcountry('LT2', $mysoc->country_code)
							)
						);
							//			  }
					}
				}
				else
				{
					$lines = array(
						array(
							'var' => $tabttcday[$key],
							'label' => $langs->trans('ThirdParty'),
							'nomtcheck' => true,
							'inv' => true
						),
						array(
							'var' => $tablocaltax1dayaa[$key],
							'label' => $langs->trans('LT1gasto'),
							'inv' => true
						),
						array(
							'var' => $tabhtday[$key],
							'label' => $langs->trans('Products'),
						),
						array(
							'var' => $tabtvaday[$key],
							'label' => $langs->trans('VAT')
						),
						array(
							'var' => $tablocaltax1day[$key],
							'label' => $langs->transcountry('LT1', $mysoc->country_code)
						),
						array(
							'var' => $tablocaltax2day[$key],
							'label' => $langs->transcountry('LT2', $mysoc->country_code)
						)
					);

				}
				foreach ($lines as $line)
				{
					foreach ($line['var'] as $k => $mt)
					{
						if (isset($line['nomtcheck']) || $mt)
						{
							print "<tr ".$bc[$var]." >";
							print "<td>".$val["date"]."</td>";

							$aAsiento[$k]['date']      = $val['date'];
							$aAsiento[$k]['groupseat'] = $group_seat;
							$aAsiento[$k]['label'] = $line['label'].' '.$invoicestatic->ref;
							$aAsiento[$k]['account']   = $k;

							if (isset($line['day']))
							{
								$companystatic->id=$tabcompanyday[$key][$k]['id'];
								$companystatic->name=$tabcompanyday[$key][$k]['name'];
								$companystatic->client=$tabcompanyday[$key][$k]['client'];
								print "<td>".$line['account']."</td>";
								print "<td>".$line['label'].'('.$companystatic->name.')'."</td>";
							}
							else
							{
								print "<td>".$k."</td>";
								print "<td>".$line['label']."</td>";
							}
							if (isset($line['inv']))
							{
								print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
								print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
								$aAsiento[$k]['deudor']   = ($mt>=0?$k:'');
								$aAsiento[$k]['acreedor'] = ($mt<0?$k:'');
								$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MT'):price2num($mt,'MT'));

								$sumaDebe  += ($mt>=0?$mt:0);
								$sumaHaber += ($mt<0?-$mt:0);
							}
							else
							{
								print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
								print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
								$aAsiento[$k]['deudor']   = ($mt<0?$k:'');
								$aAsiento[$k]['acreedor'] = ($mt>=0?$k:'');
								$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MT'):price2num($mt,'MT'));

								$sumaDebe  += ($mt<0?-$mt:0);
								$sumaHaber += ($mt>=0?$mt:0);
							}

							print "</tr>";
						}
					}
				}
				$aArraySeat[$key] = $aAsiento;
				$aAsiento = array();
				$var = !$var;
			}
			$classRead = 'liste_total';
			if (price($sumaDebe) != price($sumaHaber))
			{
				$classRead = 'bgread';
				$sep = True;
				$lProcesaSeat = false;
			}
			else
			{
				$lProcesaSeat = true;
			}
			print '<tr class="'.$classRead.'"><td align="left" colspan="3">';
			if ($sep) print '&nbsp;';
			else print $langs->trans("Equalsums");
			print '</td>';
			print '<td align="right" nowrap>'.price($sumaDebe).'</td>';
			print '<td align="right" nowrap>'.price($sumaHaber).'</td>';
			print '</tr>';

			print "</table>";
		}

		if ($group_seat == 3 )
		{
			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
			print '<td>'.$langs->trans('Month').'</td>';
			print '<td>'.$langs->trans('Account').'</td>';
			print '<td>'.$langs->trans('Type').'</td>';
			print '<td align="right">'.$langs->trans('Debit').'</td>';
			print '<td align="right">'.$langs->trans('Credit').'</td>';
			print "</tr>\n";

			$var=true;

			$invoicestatic=new Facture($db);
			$companystatic=new Client($db);
			foreach ($tabper as $key => $val)
			{
				$invoicestatic->id=$key;
				$invoicestatic->ref=$val["ref"];
				$invoicestatic->type=$val["type"];

		// $companystatic->id=$tabcompany[$key]['id'];
		// $companystatic->name=$tabcompany[$key]['name'];
		// $companystatic->client=$tabcompany[$key]['client'];
				if (count($tabttcper[$key]>1))
				{
					foreach($tabttcper[$key] AS $idx => $aValue)
					{
						$lines = array(
							array(
								'var' => $tabttcper[$key],
								'account' => $idx,
								'label' => $langs->trans('ThirdParty'),
								'nomtcheck' => true,
								'day' => true,
								'inv' => true
							),
							array(
								'var' => $tablocaltax1peraa[$key],
								'account' => $compta_against_localtax1,
								'label' => $langs->trans('LT1gasto'),
								'day' => true,
								'inv' => true
							),
							array(
								'var' => $tabhtper[$key],
								'label' => $langs->trans('Products'),
							),
							array(
								'var' => $tabtvaper[$key],
								'label' => $langs->trans('VAT')
							),
							array(
								'var' => $tablocaltax1per[$key],
								'label' => $langs->transcountry('LT1', $mysoc->country_code)
							),
							array(
								'var' => $tablocaltax2per[$key],
								'label' => $langs->transcountry('LT2', $mysoc->country_code)
							)
						);
					}
				}
				else
				{
					$lines = array(
						array(
							'var' => $tabttcper[$key],
							'label' => $langs->trans('ThirdParty'),
							'nomtcheck' => true,
							'inv' => true
						),
						array(
							'var' => $tablocaltax1peraa[$key],
							'label' => $langs->trans('LT1gasto'),
							'inv' => true
						),
						array(
							'var' => $tabhtper[$key],
							'label' => $langs->trans('Products'),
						),
						array(
							'var' => $tabtvaper[$key],
							'label' => $langs->trans('VAT')
						),
						array(
							'var' => $tablocaltax1per[$key],
							'label' => $langs->transcountry('LT1', $mysoc->country_code)
						),
						array(
							'var' => $tablocaltax2per[$key],
							'label' => $langs->transcountry('LT2', $mysoc->country_code)
						)
					);

				}
				foreach ($lines as $line)
				{
					foreach ($line['var'] as $k => $mt)
					{
						if (isset($line['nomtcheck']) || $mt)
						{
							print "<tr ".$bc[$var]." >";
							print "<td>".$val["mes"].'-'.$val["anio"]."</td>";

							$aAsiento[$k]['date']      = $val['date'];
							$aAsiento[$k]['groupseat'] = $group_seat;
							$aAsiento[$k]['label'] = $line['label'].' '.$invoicestatic->ref;
							$aAsiento[$k]['account']   = $k;

							if (isset($line['day']))
							{
								$companystatic->id=$tabcompanyper[$key][$k]['id'];
								$companystatic->name=$tabcompanyper[$key][$k]['name'];
								$companystatic->client=$tabcompanyper[$key][$k]['client'];
								print "<td>".$line['account']."</td>";
								print "<td>".$line['label'].'('.$companystatic->name.')'."</td>";
							}
							else
							{
								print "<td>".$k."</td>";
								print "<td>".$line['label']."</td>";
							}
							if (isset($line['inv']))
							{
								print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
								print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
								$aAsiento[$k]['deudor']   += ($mt>=0?$k:0);
								$aAsiento[$k]['acreedor'] += ($mt<0?$k:0);
								$aAsiento[$k]['amount']   += ($mt<0?price2num(-$mt,'MT'):price2num($mt,'MT'));

								$sumaDebe  += ($mt>=0?$mt:0);
								$sumaHaber += ($mt<0?-$mt:0);
							}
							else
							{
								print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
								print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
								$aAsiento[$k]['deudor']   += ($mt<0?$k:0);
								$aAsiento[$k]['acreedor'] += ($mt>=0?$k:0);
								$aAsiento[$k]['amount']   += ($mt<0?price2num(-$mt,'MT'):price2num($mt,'MT'));

								$sumaDebe  += ($mt<0?-$mt:0);
								$sumaHaber += ($mt>=0?$mt:0);
							}

							print "</tr>";
						}
					}
				}
				$aArraySeat[$key] = $aAsiento;
				$aAsiento = array();
				$var = !$var;
			}
			$classRead = 'liste_total';
			if (price($sumaDebe) != price($sumaHaber))
			{
				$classRead = 'bgread';
				$sep = True;
				$lProcesaSeat = false;
			}
			else
			{
				$lProcesaSeat = true;
			}
			print '<tr class="'.$classRead.'"><td align="left" colspan="3">';
			if ($sep) print '&nbsp;';
			else print $langs->trans("Equalsums");
			print '</td>';
			print '<td align="right" nowrap>'.price($sumaDebe).'</td>';
			print '<td align="right" nowrap>'.price($sumaHaber).'</td>';
			print '</tr>';

			print "</table>";
		}
		if ($lProcesaSeat == true && !empty($aArraySeat))
		{
			$_SESSION['aArraySeat'] = $aArraySeat;
			$_SESSION['aArrayTabdoc']  = $aArrayTabDoc;
			print '<div>';
			print "<form action=\"fiche.php\" method=\"post\">\n";
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="save">';
			print '<input type="hidden" name="type_seat" value="'.$type_seat.'">';
			print '<input type="hidden" name="date_seat" value="'.$date_seat.'">';
			print '<input type="hidden" name="seat_month" value="'.$seat_month.'">';
			print '<input type="hidden" name="seat_year" value="'.$seat_year.'">';
			print '<input type="hidden" name="lote" value="'.$lote.'">';
			print '<input type="hidden" name="sblote" value="'.$sblote.'">';
			print '<input type="hidden" name="codtr" value="'.$codtr.'">';

			dol_htmloutput_mesg($mesg);

			print '<table class="border" width="100%">';

			//history
			print '<tr><td class="fieldrequired">'.$langs->trans('Glosa').'</td><td colspan="2">';
			print '<input id="history" type="text" value="'.$history.'" name="history" size="38" maxlength="240">';
			print '</td></tr>';

			print '</table>';

			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

			print '</form>';

			print '</div>';
		}

	}
}
if ($action == 'fin')
{
	print_fiche_titre($langs->trans("Newseatssales"));

	dol_htmloutput_mesg($mesg);

	print '<p>';
	print $langs->trans('Accounting entry generated correctly');
	print '</p>';

}

llxFooter();

$db->close();
?>
