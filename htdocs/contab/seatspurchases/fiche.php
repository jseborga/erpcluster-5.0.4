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
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.class.php';

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
$date_seatmonth = GETPOST('date_seatmonth');
$date_seatday   = GETPOST('date_seatday');
$date_seatyear  = GETPOST('date_seatyear');
$history = GETPOST('history');

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
$date_start=dol_mktime(0, 0, 0, $date_startmonth, $date_startday, $date_startyear);
$date_end=dol_mktime(23, 59, 59, $date_endmonth, $date_endday, $date_endyear);

if (empty($date_start) || empty($date_end)) // We define date_start and date_end
{
	$date_start=dol_get_first_day($pastmonthyear,$pastmonth,false); $date_end=dol_get_last_day($pastmonthyear,$pastmonth,false);
}

$mesg = '';

$object = new Contabseatext($db);
$mysoc = new Societe ($db);

$form=new Form($db);


$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementseats"),$help_url);

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
if ($action == 'generate' && $user->rights->contab->seat->wirte)
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
		$seat_month = $date_seatmonth;
		$seat_year  = $date_seatyear;
		$lote = '00100';
		$sblote = '001';

		$nom=$langs->trans("PurchasesJournal");
		$nomlink='';
		$periodlink='';
		$exportlink='';
		$builddate=time();
		$description=$langs->trans("DescPurchaseJournal").'<br>';

		if (! empty($conf->global->FACTURE_DEPOSITS_ARE_JUST_PAYMENTS)) $description.= $langs->trans("DepositsAreNotIncluded");
		else  $description.= $langs->trans("DepositsAreIncluded");
		$period=$form->select_date($date_start,'date_start',0,0,0,'',1,0,1).' - '.$form->select_date($date_end,'date_end',0,0,0,'',1,0,1);
	//    report_header($nom,$nomlink,$period,$periodlink,$description,$builddate,$exportlink);

		$p = explode(":", $conf->global->MAIN_INFO_SOCIETE_COUNTRY);
		$idpays = $p[0];

		$sql = "SELECT f.rowid, f.ref_supplier, f.type, f.datef, f.libelle,";
		$sql.= " fd.total_ttc, fd.tva_tx, fd.total_ht, fd.tva as total_tva, fd.product_type, fd.localtax1_tx, fd.localtax2_tx, fd.total_localtax1, fd.total_localtax2,";
		$sql.= " s.rowid as socid, s.nom as name, s.code_compta_fournisseur,";
		$sql.= " p.rowid as pid, p.ref as ref, p.accountancy_code_buy,";
		$sql.= " ct.accountancy_code_buy as account_tva, ct.recuperableonly";
		$sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn_det fd";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_tva ct ON fd.tva_tx = ct.taux AND fd.info_bits = ct.recuperableonly AND ct.fk_pays = '".$idpays."'";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product p ON p.rowid = fd.fk_product";
		$sql.= " JOIN ".MAIN_DB_PREFIX."facture_fourn f ON f.rowid = fd.fk_facture_fourn";
		$sql.= " JOIN ".MAIN_DB_PREFIX."societe s ON s.rowid = f.fk_soc" ;
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."contab_seat_flag AS csf ON f.rowid = csf.table_id ";

		$sql.= " WHERE f.fk_statut > 0 AND f.entity = ".$conf->entity;
		$sql.= " AND ( (csf.table_nom = ''  OR csf.table_nom IS NULL) OR csf.table_nom != '".MAIN_DB_PREFIX."facture_fourn' )";

		if (! empty($conf->global->FACTURE_DEPOSITS_ARE_JUST_PAYMENTS)) $sql.= " AND f.type IN (0,1,2)";
		else $sql.= " AND f.type IN (0,1,2,3)";
		if ($date_start && $date_end) $sql .= " AND f.datef >= '".$db->idate($date_start)."' AND f.datef <= '".$db->idate($date_end)."'";
		if ($group_seat == 1)
			$sql.= " ORDER BY f.rowid ";
		if ($group_seat == 2 || $group_seat == 3)
			$sql.= " ORDER BY f.datef ";
	//echo $sql;
		dol_syslog("sql=".$sql);
		$result = $db->query($sql);

		if ($result)
		{

	    // les variables
			$cptfour = (! empty($conf->global->COMPTA_ACCOUNT_SUPPLIER)?$conf->global->COMPTA_ACCOUNT_SUPPLIER:$langs->trans("CodeNotDef"));
			$cpttva = (! empty($conf->global->COMPTA_VAT_ACCOUNT)?$conf->global->COMPTA_VAT_ACCOUNT:$langs->trans("CodeNotDef"));

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
			$_SESSION['table_nom'] = 'facture_fourn';

			while ($i < $num)
			{
				$obj = $db->fetch_object($result);
				$mysoc->fetch($obj->socid);
				// contrôles
				$compta_soc = (! empty($obj->code_compta_fournisseur)?$obj->code_compta_fournisseur:$cptfour);
				$compta_prod = $obj->accountancy_code_buy;
				if (empty($compta_prod))
				{
					if($obj->product_type == 0) $compta_prod = (! empty($conf->global->COMPTA_PRODUCT_BUY_ACCOUNT)?$conf->global->COMPTA_PRODUCT_BUY_ACCOUNT:$langs->trans("CodeNotDef"));
					else $compta_prod = (! empty($conf->global->COMPTA_SERVICE_BUY_ACCOUNT)?$conf->global->COMPTA_SERVICE_BUY_ACCOUNT:$langs->trans("CodeNotDef"));
				}
				$compta_tva = (! empty($obj->account_tva)?$obj->account_tva:$cpttva);
				$compta_localtax1 = (! empty($obj->account_localtax1)?$obj->account_localtax1:$langs->trans("CodeNotDef"));
				$compta_localtax2 = (! empty($obj->account_localtax2)?$obj->account_localtax2:$langs->trans("CodeNotDef"));

				$account_localtax1=getLocalTaxesFromRate($obj->tva_tx, 1, $mysoc,$mysoc);
				$compta_localtax1= (! empty($account_localtax1[2])?$account_localtax1[2]:$langs->trans("CodeNotDef"));
				$account_localtax2=getLocalTaxesFromRate($obj->tva_tx, 2, $mysoc,$mysoc);
				$compta_localtax2= (! empty($account_localtax2[2])?$account_localtax2[2]:$langs->trans("CodeNotDef"));

		//la ligne facture
				$tabfac[$obj->rowid]["date"] = $obj->datef;
				$tabfac[$obj->rowid]["ref"] = $obj->ref_supplier;
				$tabfac[$obj->rowid]["type"] = $obj->type;
				$tabfac[$obj->rowid]["lib"] = $obj->libelle;
				$tabttc[$obj->rowid][$compta_soc] += $obj->total_ttc;
				$tabht[$obj->rowid][$compta_prod] += $obj->total_ht;
				if ($obj->recuperableonly != 1) $tabtva[$obj->rowid][$compta_tva] += $obj->total_tva;
				$tablocaltax1[$obj->rowid][$compta_localtax1] += $obj->total_localtax1;
				$tablocaltax2[$obj->rowid][$compta_localtax2] += $obj->total_localtax2;
				$tabcompany[$obj->rowid]=array('id'=>$obj->socid,'name'=>$obj->name);

		//por dia
				$tabday[$obj->datef]["date"] = $obj->datef;
				$tabday[$obj->datef]["type"] = $obj->type;
				$tabday[$obj->datef]["lib"] = $obj->libelle;
				$tabttcday[$obj->datef][$compta_soc] += $obj->total_ttc;
				$tabhtday[$obj->datef][$compta_prod] += $obj->total_ht;
				if ($obj->recuperableonly != 1) $tabtvaday[$obj->datef][$compta_tva] += $obj->total_tva;
				$tablocaltax1day[$obj->datef][$compta_localtax1] += $obj->total_localtax1;
				$tablocaltax2day[$obj->datef][$compta_localtax2] += $obj->total_localtax2;
				$tabcompanyday[$obj->datef][$obj->socid]=array('id'=>$obj->socid,'name'=>$obj->name);


		//por periodo //agrupado por mes

				list($anio,$mes,$dia) = explode('-',$obj->datef);
				$mesanio = $mes.$anio;

				$tabper[$mesanio]["date"] = $mesanio;
				$tabper[$mesanio]["type"] = $obj->type;
				$tabper[$mesanio]["lib"] = $obj->libelle;
				$tabttcper[$mesanio][$compta_soc] += $obj->total_ttc;
				$tabhtper[$mesanio][$compta_prod] += $obj->total_ht;
				if ($obj->recuperableonly != 1) $tabtvaper[$mesanio][$compta_tva] += $obj->total_tva;
				$tablocaltax1per[$mesanio][$compta_localtax1] += $obj->total_localtax1;
				$tablocaltax2per[$mesanio][$compta_localtax2] += $obj->total_localtax2;
				$tabcompanyper[$mesanio][$obj->socid]=array('id'=>$obj->socid,'name'=>$obj->name);

				if ($group_seat == 1)
					$aArrayTabDoc[$obj->rowid][$obj->rowid] = $obj->rowid;
				elseif ($group_seat == 2)
					$aArrayTabDoc[$obj->datef][$obj->rowid] = $obj->rowid;
				elseif ($group_seat == 3)
					$aArrayTabDoc[$mesanio][$obj->rowid] = $obj->rowid;

				$i++;
			}
		}
		else
		{
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
	dol_htmloutput_mesg($mesg);
	exit;
}

/*
 * View
 */

//variables fijas por venta
$type_seat = 1;
$loked = 1;

if ($action == 'create' && $user->rights->contab->seat->write)
{
	print_fiche_titre($langs->trans("Newseatspurchases"));

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
	print '<input id="history" type="text" value="'.$object->history.'" name="history" size="38" maxlength="40">';
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($action == 'list')
	{
	/*
	 * Show result array
	 */
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
		$invoicestatic=new FactureFournisseur($db);
		$companystatic=new Fournisseur($db);

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
							print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
							print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
							$aAsiento[$k]['deudor']   = ($mt<0?$k:'');
							$aAsiento[$k]['acreedor'] = ($mt>=0?$k:'');
							$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MT'):price2num($mt,'MT'));

							$sumaDebe  += ($mt<0?-$mt:0);
							$sumaHaber += ($mt>=0?$mt:0);
						}
						else
						{
							print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
							print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
							$aAsiento[$k]['deudor']   = ($mt>=0?$k:'');
							$aAsiento[$k]['acreedor'] = ($mt<0?$k:'');
							$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MT'):price2num($mt,'MT'));

							$sumaDebe  += ($mt>=0?$mt:0);
							$sumaHaber += ($mt<0?-$mt:0);
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

		$invoicestatic=new FactureFournisseur($db);
		$companystatic=new Fournisseur($db);

		foreach ($tabday as $key => $val)
		{
			$invoicestatic->id=$key;
			$invoicestatic->ref=$val["ref"];
			$invoicestatic->type=$val["type"];

			if (count($tabttcday[$key]>1))
			{
				foreach($tabttcday[$key] AS $idx => $aValue)
				{
					$lines = array(
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
							),
						array(
							'var' => $tabttcday[$key],
							'account' => $idx,
							'label' => $langs->trans('ThirdParty'),
							'nomtcheck' => true,
							'day' => true,
							'inv' => true
							)
						);
				}
			}
			else
			{
				$lines = array(
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
						),
					array(
						'var' => $tabttcday[$key],
						'label' => $langs->trans('ThirdParty').' ('.$companystatic->getNomUrl(0, 'customer', 16).')',
						'nomtcheck' => true,
						'inv' => true
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
							print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
							print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
							$aAsiento[$k]['deudor']   = ($mt<0?$k:'');
							$aAsiento[$k]['acreedor'] = ($mt>=0?$k:'');
							$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MT'):price2num($mt,'MT'));

							$sumaDebe  += ($mt<0?-$mt:0);
							$sumaHaber += ($mt>=0?$mt:0);
						}
						else
						{
							print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
							print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
							$aAsiento[$k]['deudor']   = ($mt>=0?$k:'');
							$aAsiento[$k]['acreedor'] = ($mt<0?$k:'');
							$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MT'):price2num($mt,'MT'));

							$sumaDebe  += ($mt>=0?$mt:0);
							$sumaHaber += ($mt<0?-$mt:0);
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

		$invoicestatic=new FactureFournisseur($db);
		$companystatic=new Fournisseur($db);

		foreach ($tabper as $key => $val)
		{
			$invoicestatic->id=$key;
			$invoicestatic->ref=$val["ref"];
			$invoicestatic->type=$val["type"];

			if (count($tabttcper[$key]>1))
			{
				foreach($tabttcper[$key] AS $idx => $aValue)
				{
					$lines = array(
						array(
					     'var' => $tabttcper[$key], //$aValue,
					     'account' => $idx,
					     'label' => $langs->trans('ThirdParty'),
					     'nomtcheck' => true,
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
							print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
							print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
							$aAsiento[$k]['deudor']   = ($mt<0?$k:'');
							$aAsiento[$k]['acreedor'] = ($mt>=0?$k:'');
							$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MT'):price2num($mt,'MT'));

							$sumaDebe  += ($mt<0?-$mt:0);
							$sumaHaber += ($mt>=0?$mt:0);
						}
						else
						{
							print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
							print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
							$aAsiento[$k]['deudor']   = ($mt>=0?$k:'');
							$aAsiento[$k]['acreedor'] = ($mt<0?$k:'');
							$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MT'):price2num($mt,'MT'));

							$sumaDebe  += ($mt>=0?$mt:0);
							$sumaHaber += ($mt<0?-$mt:0);
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
	print_fiche_titre($langs->trans("Newseatspurchases"));

	dol_htmloutput_mesg($mesg);

	print '<p>';
	print $langs->trans('Accounting entry generated correctly');
	print '</p>';

}

llxFooter();

$db->close();
?>
