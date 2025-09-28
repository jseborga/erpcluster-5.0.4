<?php
/* Copyright (C) 2002-2006 Rodolphe Quiedeville  <rodolphe@quiedeville.org>
 * Copyright (C) 2004      Eric Seigne           <eric.seigne@ryxeo.com>
 * Copyright (C) 2004-2012 Laurent Destailleur   <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Marc Barilley / Ocebo <marc@ocebo.com>
 * Copyright (C) 2005-2013 Regis Houssin         <regis.houssin@capnetworks.com>
 * Copyright (C) 2006      Andre Cianfarani      <acianfa@free.fr>
 * Copyright (C) 2010-2012 Juanjo Menent         <jmenent@2byte.es>
 * Copyright (C) 2012      Christophe Battarel   <christophe.battarel@altairis.fr>
 * Copyright (C) 2013      Florian Henry		  	<florian.henry@open-concept.pro>
 * Copyright (C) 2013      Cédric Salvador       <csalvador@gpcsolutions.fr>
 * Copyright (C) 2013      Ramiro Queso       <ramiroques@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *	\file       htdocs/compta/facture/list.php
 *	\ingroup    facture
 *	\brief      Page to create/see an invoice
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/facture/modules_facture.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/discount.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/paiement/class/paiement.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/invoice.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

require_once DOL_DOCUMENT_ROOT.'/fiscal/class/vfiscalext.class.php';

if (! empty($conf->commande->enabled)) require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
if (! empty($conf->projet->enabled))
{
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
}

$langs->load('fiscal');
$langs->load('bills');
$langs->load('companies');
$langs->load('products');
$langs->load('main');

$sall=trim(GETPOST('sall'));
$projectid=(GETPOST('projectid')?GETPOST('projectid','int'):0);

$id=(GETPOST('id','int')?GETPOST('id','int'):GETPOST('facid','int'));  // For backward compatibility
$ref=GETPOST('ref','alpha');
$socid=GETPOST('socid','int');
$action=GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');
$lineid=GETPOST('lineid','int');
$userid=GETPOST('userid','int');

if (isset($_GET['sf_ref']) || isset($_GET['search_ref']))
{
	$search_ref=GETPOST('sf_ref')?GETPOST('sf_ref','alpha'):GETPOST('search_ref','alpha');
	$_SESSION['f_search_ref'] = $search_ref;
}
if (isset($_GET['search_refcustomer']))
{
	$search_refcustomer=GETPOST('search_refcustomer','alpha');
	$_SESSION['f_search_refcustomer'] = $search_refcustomer;
}
if (isset($_GET['search_societe']))
{
	$search_societe=GETPOST('search_societe','alpha');
	$_SESSION['f_search_societe'] = $search_societe;
}
if (isset($_GET['search_montant_ht']))
{
	$search_montant_ht=GETPOST('search_montant_ht','alpha');
	$_SESSION['f_search_montant_ht'] = $search_montant_ht;
}
if (isset($_GET['search_montant_ttc']))
{
	$search_montant_ttc=GETPOST('search_montant_ttc','alpha');
	$_SESSION['f_search_montant_ttc'] = $search_montant_ttc;
}
if (isset($_GET['search_status']))
{
	$search_status=GETPOST('search_status','alpha');
	$_SESSION['f_search_status'] = $search_status;
}
if (isset($_GET['search_numaut']))
{
	$search_numaut=GETPOST('search_numaut','alpha');
	$_SESSION['f_search_numaut'] = $search_numaut;
}
$typelist = $conf->global->VENTA_DEFAULT_NUMBER_LIST_FACTURE;

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if (empty($page) || $page == -1) {
	$page = 0;
}
$offset = $conf->liste_limit * $page;
if (! $sortorder) $sortorder='DESC';
if (! $sortfield) $sortfield='f.datef';
$limit = $conf->liste_limit;

$pageprev = $page - 1;
$pagenext = $page + 1;


if (isset($_GET['search_user']))
{
	$search_user = GETPOST('search_user','int');
	$_SESSION['f_search_user'] = $search_user;
}
if (isset($_GET['search_sale']))
{
	$search_sale = GETPOST('search_sale','int');
	$_SESSION['f_search_sale'] = $search_sale;
}
if (isset($_GET['search_nfiscal']))
{
	$search_nfiscal = GETPOST('search_nfiscal','alpha');
	$_SESSION['f_search_nfiscal'] = $search_nfiscal;
}
$day	= GETPOST('day','int');
$month	= GETPOST('month','int');
$year	= GETPOST('year','int');
$filtre	= GETPOST('filtre');
$aDatehoy = dol_getdate(dol_now());
if ($typelist == 'year')
{
    if (empty($year)) $year = $aDatehoy['year'];
}
if ($typelist == 'month')
{
    if (empty($year)) $year = $aDatehoy['year'];
    if (empty($month)) $month = $aDatehoy['mon'];
}
if ($typelist == 'day')
{
    if (empty($year)) $year = $aDatehoy['year'];
    if (empty($month)) $month = $aDatehoy['mon'];
    if (empty($day)) $day = $aDatehoy['mday'];
}

//recuperando de session
$search_ref=$_SESSION['f_search_ref'];
$search_refcustomer=$_SESSION['f_search_refcustomer'];
$search_societe=$_SESSION['f_search_societe'];
$search_montant_ht=$_SESSION['f_search_montant_ht'];
$search_montant_ttc=$_SESSION['f_search_montant_ttc'];
$search_status=$_SESSION['f_search_status'];
$search_numaut=$_SESSION['f_search_numaut'];
$search_user = $_SESSION['f_search_user'];
$search_sale = $_SESSION['f_search_sale'];
$search_nfiscal = $_SESSION['f_search_nfiscal'];

//fin recuepra session


$fStatut = array(0=>$langs->trans('Draft'), 1=>$langs->trans('Unpayed'), 2=>$langs->trans('Payed'), 3=>$langs->trans('Abandoned'), 9=>$langs->trans('Por anular') );
// Security check
$fieldid = (! empty($ref)?'facnumber':'rowid');
if (! empty($user->societe_id)) $socid=$user->societe_id;
//$result = restrictedArea($user, 'facture', $id,'','','fk_soc',$fieldid);
if (!$user->rights->sales->lire)
	accessforbidden();

//cambiamos para que guarde con session


$object=new Facture($db);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('invoicelist'));

$now=dol_now();


/*
 * Actions
 */
if ($action == 'confirm_valprint' && $_REQUEST['confirm'] == 'yes')
{
	//seconfirma la impresion de la factura por el vendedor
	$objvfiscal= new Vfiscalext($db);
	$res = $objvfiscal->fetch('',$id);
	//echo '<hr>'.$objvfiscal->fk_facture.' == '.$id;exit;
	if ($res>0 && $objvfiscal->fk_facture == $id)
	{
	//cambiamos el statut_print
		$objvfiscal->statut_print = 1;
		$res = $objvfiscal->update($user);
		if ($res>0)
		{
			header('Location: '.$_SERVER['PHP_SELF']);
			exit;
		}
	}
	$action = '';
}

$parameters=array('socid'=>$socid);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks

// Do we click on purge search criteria ?
if (GETPOST("button_removefilter_x"))
{
	$search_categ='';
	$search_user='';
	$search_sale='';
	$search_ref='';
	$search_refcustomer='';
	$search_societe='';
	$search_montant_ht='';
	$search_montant_ttc='';
	$search_status='';
	$search_nfiscal='';
	$search_numaut='';
	$year='';
	$month='';
}


/*
 * View
 */

llxHeader('',$langs->trans('Bill'),'EN:Customers_Invoices|FR:Factures_Clients|ES:Facturas_a_clientes');

$form = new Form($db);
$formother = new FormOther($db);
$formfile = new FormFile($db);
$bankaccountstatic=new Account($db);
$facturestatic=new Facture($db);

if (! $sall) $sql = 'SELECT';
else $sql = 'SELECT DISTINCT';
$sql.= ' f.rowid as facid, f.facnumber, f.ref_client, f.type, f.note_private, f.increment, f.total as total_ht, f.tva as total_tva, f.total_ttc,';
$sql.= ' f.datef as df, f.date_lim_reglement as datelimite,';
$sql.= ' f.paye as paye, f.fk_statut,';
$sql.= ' s.nom, s.rowid as socid, s.code_client, s.client, ';
$sql.= ' vf.nfiscal, vf.serie, vf.nit, vf.num_autoriz, vf.razsoc, vf.status AS vfiscalstatus, vf.statut_print, vf.fk_user_create AS vfiscal_fk_user_create ';
$sql.= " , fb.label AS labelnul ";
if (! $sall) $sql.= ', SUM(pf.amount) as am';   // To be able to sort on status
$sql.= ' FROM '.MAIN_DB_PREFIX.'societe as s';
$sql.= ' INNER JOIN '.MAIN_DB_PREFIX.'facture as f ON f.fk_soc = s.rowid ';
if (! $sall) $sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'paiement_facture as pf ON pf.fk_facture = f.rowid ';
else $sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'facturedet as fd ON fd.fk_facture = f.rowid ';
// We'll need this table joined to the select in order to filter by sale
if ($search_sale > 0 || (! $user->rights->societe->client->voir && ! $socid))
	$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."societe_commerciaux as sc ON s.rowid = sc.fk_soc ";
if ($search_user > 0)
{
	$sql.=" LEFT JOIN ".MAIN_DB_PREFIX."element_contact as ec ON ec.element_id = f.rowid ";
	$sql.=" LEFT JOIN ".MAIN_DB_PREFIX."c_type_contact as tc ON ec.fk_c_type_contact = tc.rowid ";
}
//tabla v_fiscal
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."v_fiscal AS vf  ON f.rowid = vf.fk_facture ";
//tabla facture_bank_statut
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."facture_bank_status AS fb ON f.rowid = fb.fk_facture ";
//$sql.= ' WHERE f.fk_soc = s.rowid';
$sql.= ' WHERE ';
//$sql.= " AND f.entity = ".$conf->entity;
$sql.= " f.entity = ".$conf->entity;
//if (! $user->rights->societe->client->voir && ! $socid)
//    $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
if ($socid) $sql.= ' AND s.rowid = '.$socid;
if ($userid)
{
	if ($userid == -1) $sql.=' AND f.fk_user_author IS NULL';
	else $sql.=' AND f.fk_user_author = '.$userid;
}
if (!$user->admin)
	$sql.=' AND f.fk_user_author = '.$user->id;

if ($filtre)
{
	$aFilter = explode(',', $filtre);
	foreach ($aFilter as $filter)
	{
		$filt = explode(':', $filter);
		$sql .= ' AND ' . trim($filt[0]) . ' = ' . trim($filt[1]);
	}
}
if ($search_ref)
{
	$sql .= natural_search('f.facnumber', $search_ref);
}
if ($search_refcustomer)
{
	$sql .= natural_search('vf.razsoc', $search_refcustomer);
}
if ($search_societe)
{
	$sql .= natural_search('s.nom', $search_societe);
}
if ($search_nfiscal)
{
	$sql .= " AND vf.nfiscal  = ".$search_nfiscal;
	//$sql .= natural_search('vf.nfiscal', $search_nfiscal);
}
if ($search_numaut)
{
	$sql .= natural_search('vf.num_autoriz', $search_numaut);
}
if ($search_montant_ht)
{
	$sql.= ' AND f.total = \''.$db->escape(price2num(trim($search_montant_ht))).'\'';
}
if ($search_montant_ttc)
{
	$sql.= ' AND f.total_ttc = \''.$db->escape(price2num(trim($search_montant_ttc))).'\'';
}
if ($search_status != '' && $search_status != -1)
{
	if ($search_status == 9)
	{
		$sql.= " AND (fb.statut = '2' OR fb.label != '') ";
		$sql.= " AND f.fk_statut = 2";
	}
	else
	{
		$sql.= " AND f.fk_statut = '".$db->escape($search_status)."'";
	}
}
if ($month > 0)
{
	if ($year > 0 && empty($day))
		$sql.= " AND f.datef BETWEEN '".$db->idate(dol_get_first_day($year,$month,false))."' AND '".$db->idate(dol_get_last_day($year,$month,false))."'";
	else if ($year > 0 && ! empty($day))
		$sql.= " AND f.datef BETWEEN '".$db->idate(dol_mktime(0, 0, 0, $month, $day, $year))."' AND '".$db->idate(dol_mktime(23, 59, 59, $month, $day, $year))."'";
	else
		$sql.= " AND date_format(f.datef, '%m') = '".$month."'";
}
else if ($year > 0)
{
	$sql.= " AND f.datef BETWEEN '".$db->idate(dol_get_first_day($year,1,false))."' AND '".$db->idate(dol_get_last_day($year,12,false))."'";
}
if ($search_sale > 0)
	$sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$search_sale;
if ($search_user > 0)
{
	$sql.= " AND tc.source='internal' AND ec.fk_socpeople = ".$search_user;
}
if (! $sall)
{
	$sql.= ' GROUP BY f.rowid, f.facnumber, f.ref_client, f.type, f.note_private, f.increment, f.total, f.tva, f.total_ttc,';
	$sql.= ' f.datef, f.date_lim_reglement,';
	$sql.= ' f.paye, f.fk_statut,';
	$sql.= ' s.nom, s.rowid, s.code_client, s.client, fb.label ';
}
else
{
	$sql .= natural_search(array('s.nom', 'f.facnumber', 'f.note_public', 'fd.description'), $sall);
}
$sql.= ' ORDER BY ';
$listfield=explode(',',$sortfield);
foreach ($listfield as $key => $value) $sql.= $listfield[$key].' '.$sortorder.',';
$sql.= ' f.rowid DESC ';

$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}

$sql.= $db->plimit($limit+1,$offset);
//echo $sql; exit;

$resql = $db->query($sql);
if ($resql)
{
	$num = $db->num_rows($resql);

	if ($socid)
	{
		$soc = new Societe($db);
		$soc->fetch($socid);
	}

	$param='&socid='.$socid;
	if ($day)              $param.='&day='.$day;
	if ($month)              $param.='&month='.$month;
	if ($year)               $param.='&year=' .$year;
	if ($search_ref)         $param.='&search_ref=' .$search_ref;
	if ($search_nfiscal)     $param.='&search_nfiscal=' .$search_nfiscal;
	if ($search_numaut)      $param.='&search_numaut=' .$search_numaut;
	if ($search_refcustomer) $param.='&search_refcustomer=' .$search_refcustomer;
	if ($search_societe)     $param.='&search_societe=' .$search_societe;
	if ($search_sale > 0)    $param.='&search_sale=' .$search_sale;
	if ($search_user > 0)    $param.='&search_user=' .$search_user;
	if ($search_montant_ht)  $param.='&search_montant_ht='.$search_montant_ht;
	if ($search_montant_ttc) $param.='&search_montant_ttc='.$search_montant_ttc;
	if ($search_status) 	$param.='&search_status='.$search_status;

	print_barre_liste($langs->trans('BillsCustomers').' '.($socid?' '.$soc->nom:''),$page,$_SERVER["PHP_SELF"],$param,$sortfield,$sortorder,'',$num,$nbtotalofrecords);

	$i = 0;
	print '<form method="GET" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<table class="liste" width="100%">';

	// If the user can view prospects other than his'
	$moreforfilter='';
	if ($user->rights->societe->client->voir || $socid)
	{
		$langs->load("commercial");
		$moreforfilter.=$langs->trans('ThirdPartiesOfSaleRepresentative'). ': ';
		$moreforfilter.=$formother->select_salesrepresentatives($search_sale,'search_sale',$user);
		$moreforfilter.=' &nbsp; &nbsp; &nbsp; ';
	}
	// If the user can view prospects other than his'
	if ($user->rights->societe->client->voir || $socid)
	{
		$moreforfilter.=$langs->trans('LinkedToSpecificUsers'). ': ';
		$moreforfilter.=$form->select_dolusers($search_user,'search_user',1);
	}
	if ($moreforfilter)
	{
		print '<tr class="liste_titre">';
		print '<td class="liste_titre" colspan="13">';
		print $moreforfilter;
		print '</td></tr>';
	}

	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'f.facnumber','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Nun.Aut.'),$_SERVER['PHP_SELF'],'vf.num_autoriz','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Facfiscal'),$_SERVER['PHP_SELF'],'vf.nfiscal','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('RefCustomer'),$_SERVER["PHP_SELF"],'f.ref_client','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'f.datef','',$param,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("DateDue"),$_SERVER['PHP_SELF'],"f.date_lim_reglement",'',$param,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Company'),$_SERVER['PHP_SELF'],'s.nom','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('AmountHT'),$_SERVER['PHP_SELF'],'f.total','',$param,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('AmountVAT'),$_SERVER['PHP_SELF'],'f.tva','',$param,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('AmountTTC'),$_SERVER['PHP_SELF'],'f.total_ttc','',$param,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Received'),$_SERVER['PHP_SELF'],'am','',$param,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('N.Fiscal'),$_SERVER['PHP_SELF'],'','',$param,'align="center"');
	print_liste_field_titre($langs->trans('Status'),$_SERVER['PHP_SELF'],'fk_statut,paye,am','',$param,'align="right"',$sortfield,$sortorder);
	//print '<td class="liste_titre">&nbsp;</td>';
	print '</tr>';

	// Filters lines
	print '<tr class="liste_titre">';
	print '<td class="liste_titre" align="left">';
	print '<input class="flat" size="6" type="text" name="search_ref" value="'.$search_ref.'">';
	print '</td>';
	print '<td class="liste_titre" align="left">';
	print '<input class="flat" size="6" type="text" name="search_numaut" value="'.$search_numaut.'">';
	print '</td>';
	print '<td class="liste_titre" align="left">';
	print '<input class="flat" size="6" type="text" name="search_nfiscal" value="'.$search_nfiscal.'">';
	print '</td>';

	print '<td class="liste_titre">';
	print '<input class="flat" size="6" type="text" name="search_refcustomer" value="'.$search_refcustomer.'">';
	print '</td>';
	print '<td class="liste_titre" align="center">';
	if (! empty($conf->global->MAIN_LIST_FILTER_ON_DAY)) print '<input class="flat" type="text" size="1" maxlength="2" name="day" value="'.$day.'">';
	print '<input class="flat" type="text" size="1" maxlength="2" name="month" value="'.$month.'">';
	$formother->select_year($year?$year:-1,'year',1, 20, 5);
	print '</td>';
	print '<td class="liste_titre" align="left">&nbsp;</td>';
	print '<td class="liste_titre" align="left"><input class="flat" type="text" name="search_societe" value="'.$search_societe.'"></td>';
	print '<td class="liste_titre" align="right"><input class="flat" type="text" size="10" name="search_montant_ht" value="'.$search_montant_ht.'"></td>';
	print '<td class="liste_titre" align="right">&nbsp;</td>';
	print '<td class="liste_titre" align="right"><input class="flat" type="text" size="10" name="search_montant_ttc" value="'.$search_montant_ttc.'"></td>';
	print '<td class="liste_titre" align="right">&nbsp;</td>';
	print '<td class="liste_titre" align="right">&nbsp;</td>';
	print '<td class="liste_titre" align="right">';
	print $form->selectarray('search_status', $fStatut, GETPOST('search_status'), 1, 0, 0, '', 1);

	//print '<input type="text" name="search_statut" value="'.$search_statut.'" size="6">';
	print '<input type="image" class="liste_titre" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print "</td></tr>\n";

	if ($action == 'valprint')
	{
		$form = new Form($db);
		$objfact = new Facture($db);
		$objfact->fetch(GETPOST('facid'));
		$objvfis = new Vfiscalext($db);
		$objvfis->fetch('',$objfact->id);
		$lMessage = false;
		$message = false;
		$lMessage = ($objvfis->fk_facture == $objfact->id?true:false);
		if ($lMessage)
			$message =  ' <br>'.$langs->trans('Facture').': <span style="font-size: 2em;">'.$objvfis->nfiscal.'</span> '.$langs->trans('Authorizationnumber').' <span style="font-size: 2em;">'.$objvfis->num_autoriz.'</span>';
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$id,$langs->trans("Authorize printing"),$langs->trans("Confirmauthorizeprinting").' '.$langs->trans('Doc.').' <span style="font-size: 2em;">'.$objfact->ref.'</span>'.($lMessage = true?$message:''),"confirm_valprint",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	if ($num > 0)
	{
		$var=True;
		$total_ht=0;
		$total_tva=0;
		$total_ttc=0;
		$totalrecu=0;

		while ($i < min($num,$limit))
		{
			$objp = $db->fetch_object($resql);
			$var=!$var;

			//$objfiscal = new Vfiscalext($db);
			//$res = $objfiscal->fetch('',$objp->facid);
			$datelimit=$db->jdate($objp->datelimite);

			/*
			if ($objfiscal->fk_facture == $objp->facid && $objfiscal->status == 2)
				print '<tr style="color:#ff0000;">';
			elseif (!empty($objp->labelnul))
				print '<tr style="background:#fff000;">';
			else
				print '<tr '.$bc[$var].'>';
			*/
			if ($objp->vfiscalstatus == 2)
				print '<tr style="color:#ff0000;">';
			elseif (!empty($objp->labelnul))
				print '<tr style="background:#fff000;">';
			else
				print '<tr '.$bc[$var].'>';

			print '<td class="nowrap">';

			$facturestatic->id=$objp->facid;
			$facturestatic->ref=$objp->facnumber;
			$facturestatic->type=$objp->type;
			$notetoshow=dol_string_nohtmltag(($user->societe_id>0?$objp->note_public:$objp->note),1);
			$paiement = $facturestatic->getSommePaiement();

			print '<table class="nobordernopadding"><tr class="nocellnopadd">';

			print '<td class="nobordernopadding nowrap">';
			print '<a href="'.DOL_URL_ROOT.'/fiscal/fichefiscal.php?facid='.$objp->facid.'">'.$objp->facnumber.'</a>';
		//          print $facturestatic->getNomUrl(1,'',200,0,$notetoshow);
			print $objp->increment;
			print '</td>';

			print '<td style="min-width: 20px" class="nobordernopadding nowrap">';
			if (! empty($objp->note_private))
			{
				print ' <span class="note">';
				print '<a href="'.DOL_URL_ROOT.'/compta/facture/note.php?id='.$objp->facid.'">'.img_picto($langs->trans("ViewPrivateNote"),'object_generic').'</a>';
				print '</span>';
			}
			$filename=dol_sanitizeFileName($objp->facnumber);
			$filedir=$conf->facture->dir_output . '/' . dol_sanitizeFileName($objp->facnumber);
			$urlsource=$_SERVER['PHP_SELF'].'?id='.$objp->facid;
			print $formfile->getDocumentsLink($facturestatic->element, $filename, $filedir);
			print '</td>';
			print '</tr>';
			print '</table>';

			print "</td>\n";

			print '<td align="right">';
			//if ($objfiscal->fk_facture == $objp->facid)
			print $objp->num_autoriz;
			//else
			//	print '&nbsp;';
			print '</td>';

			print '<td align="center">';
			//if ($objfiscal->fk_facture == $objp->facid)
			//	print $objfiscal->nfiscal;
			//else
			//	print '&nbsp;';
			print $objp->nfiscal;
			print '</td>';

			// Customer ref
			print '<td class="nowrap">';
			//if ($objfiscal->fk_facture == $objp->facid)
			//	print $objfiscal->razsoc;
			if ($objp->razsoc)
				print $objp->razsoc;
			else
				print $objp->ref_client;
			print '</td>';

			// Date
			print '<td align="center" class="nowrap">';
			print dol_print_date($db->jdate($objp->df),'day');
			print '</td>';

			// Date limit
			print '<td align="center" class="nowrap">'.dol_print_date($datelimit,'day');
			if ($datelimit < ($now - $conf->facture->client->warning_delay) && ! $objp->paye && $objp->fk_statut == 1 && ! $paiement)
			{
				print img_warning($langs->trans('Late'));
			}
			print '</td>';

			print '<td>';
			$thirdparty=new Societe($db);
			$thirdparty->id=$objp->socid;
			$thirdparty->nom=$objp->nom;
			$thirdparty->client=$objp->client;
			$thirdparty->code_client=$objp->code_client;
			print $thirdparty->getNomUrl(1,'customer');
			print '</td>';

			print '<td align="right">'.price($objp->total_ht,0,$langs).'</td>';

			print '<td align="right">'.price($objp->total_tva,0,$langs).'</td>';

			print '<td align="right">'.price($objp->total_ttc,0,$langs).'</td>';

			print '<td align="right">'.(! empty($paiement)?price($paiement,0,$langs):'&nbsp;').'</td>';
			//if ($user->admin || ($objfiscal->status != 2 && 	$objfiscal->fk_facture == $objp->facid))
			if ($user->admin || ($objp->vfiscalstatus != 2))
			{
				//si tiene permisos para habilitar impresion factura
				//if ($user->admin || ($user->rights->ventas->fact->print && ($objp->statut_print <=0 || is_null($objp->statut_print))))
				//	print '<td align="center">'.'<a class="lien1" href="'.$_SERVER['PHP_SELF'].'?action=valprint&facid='.$objp->facid.'">'.img_picto($langs->trans('Authorize printing'),'printer').'</a>'.'</td>';
				print '<td></td>';
				if ($objp->statut_print > 0)
				//if ($objfiscal->statut_print > 0)
					if ($user->admin || $user->id == $objp->vfiscal_fk_user_create)
					//if ($user->admin || $user->id == $objfiscal->fk_user_create)
						print '<td align="center">'.'<a class="lien1" href="'.$_SERVER['PHP_SELF'].'?impfact=1&facid='.$objp->facid.'">'.img_picto($langs->trans('Nota Fiscal'),DOL_URL_ROOT.'/ventas/img/factura.png','',1).'</a>'.'</td>';
				}
				else
					print '<td></td>';
			// Affiche statut de la facture
				print '<td align="right" class="nowrap">';
		//buscamos el vfiscal
				if ($objp->vfiscalstatus == 2)
				//if ($objfiscal->fk_facture == $objp->facid && $objfiscal->status == 2)
					print $langs->trans('Annulled');
				else
					print $facturestatic->LibStatut($objp->paye,$objp->fk_statut,5,$paiement,$objp->type);
				print "</td>";
			//print "<td>&nbsp;</td>";
				print "</tr>\n";
				$total_ht+=$objp->total_ht;
				$total_tva+=$objp->total_tva;
				$total_ttc+=$objp->total_ttc;
				$totalrecu+=$paiement;
				$i++;
			}

			if (($offset + $num) <= $limit)
			{
			// Print total
				print '<tr class="liste_total">';
				print '<td class="liste_total" colspan="5" align="left">'.$langs->trans('Total').'</td>';
				print '<td class="liste_total" align="right">'.price($total_ht,0,$langs).'</td>';
				print '<td class="liste_total" align="right">'.price($total_tva,0,$langs).'</td>';
				print '<td class="liste_total" align="right">'.price($total_ttc,0,$langs).'</td>';
				print '<td class="liste_total" align="right">'.price($totalrecu,0,$langs).'</td>';
				print '<td class="liste_total" align="center">&nbsp;</td>';
				print '</tr>';
			}
		}

		print "</table>\n";
		print "</form>\n";
		$db->free($resql);
	}
	else
	{
		dol_print_error($db);
	}


	if (isset($_GET['impfact']))
	{
	//actualizamos vfiscal
		$lPrint = true;
		$objvfiscal= new Vfiscalext($db);
		$res = $objvfiscal->fetch('',$id);
		if ($res>0 && $objvfiscal->fk_facture == $id)
		{
			if ($objvfiscal->statut_print == 0)
				$lPrint = false;
			$objvfiscal->statut_print = 0;
			$res = $objvfiscal->update($user);
		}

		if ($lPrint)
		{
			?>

			<script type="text/javascript">

				function popupTicket()
				{
					largeur = 350;
					hauteur = 600
					opt = 'width='+largeur+', height='+hauteur+', left='+(screen.width - largeur)/2+', top='+(screen.height-hauteur)/2+'';
					window.open('validation_ticket.php?facid=<?php echo $_GET['facid']; ?>', 'Print ticket', opt);
				}

				popupTicket();

			</script>
			<?php
		}
	}

	llxFooter();
	$db->close();

	?>