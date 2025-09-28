<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2013      Cédric Salvador      <csalvador@gpcsolutions.fr>
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
 *       \file       htdocs/contrat/liste.php
 *       \ingroup    contrat
 *       \brief      Page liste des contrats
 */

require ("../../main.inc.php");

require_once (DOL_DOCUMENT_ROOT."/poa/appoint/class/poacontratappoint.class.php");
require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocesscontrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidacom.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidadev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprev.class.php';
if ($conf->addendum->enabled)
  require_once DOL_DOCUMENT_ROOT.'/addendum/class/addendum.class.php';

require_once (DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");
require_once (DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

$langs->load("contracts");
$langs->load("products");
$langs->load("companies");
$langs->load("poa@poa");

$sortfield=GETPOST('sortfield','alpha');
$sortorder=GETPOST('sortorder','alpha');
$page=GETPOST('page','int');
if ($page == -1) { $page = 0 ; }
$limit = $conf->liste_limit;
$offset = $limit * $page ;

$search_nom=GETPOST('search_nom');
$search_contract=GETPOST('search_contract');
$search_ref_contrat=GETPOST('search_ref_contrat');
$sall=GETPOST('sall');
$statut=GETPOST('statut')?GETPOST('statut'):1;
$socid=GETPOST('socid');

if (! $sortfield) $sortfield="c.rowid";
if (! $sortorder) $sortorder="DESC";

// Security check
$id=GETPOST('id','int');
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'contrat', $id);

$objappoint = new Poacontratappoint($db);
$staticcontrat=new Contrat($db);
$staticcontratligne=new ContratLigne($db);
$extrafields = new ExtraFields($db);
$objpcon = new Poaprocesscontrat($db);
$objpcom = new Poapartidacom($db);
$objprev = new Poaprev($db);
$objpdev = new Poapartidadev($db);
if ($conf->addendum->enabled)
  $objadd  = new Addendum($db);
// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($staticcontrat->table_element);


/*
 * View
 */

$now=dol_now();

llxHeader();

$sql = 'SELECT';
$sql.= ' SUM('.$db->ifsql("cd.statut=0",1,0).') as nb_initial,';
$sql.= ' SUM('.$db->ifsql("cd.statut=4 AND (cd.date_fin_validite IS NULL OR cd.date_fin_validite >= '".$db->idate($now)."')",1,0).') as nb_running,';
$sql.= ' SUM('.$db->ifsql("cd.statut=4 AND (cd.date_fin_validite IS NOT NULL AND cd.date_fin_validite < '".$db->idate($now)."')",1,0).') as nb_expired,';
$sql.= ' SUM('.$db->ifsql("cd.statut=4 AND (cd.date_fin_validite IS NOT NULL AND cd.date_fin_validite < '".$db->idate($now - $conf->contrat->services->expires->warning_delay)."')",1,0).') as nb_late,';
$sql.= ' SUM('.$db->ifsql("cd.statut=5",1,0).') as nb_closed,';
$sql.= " SUM(cd.total_ht) AS totalht,";
$sql.= " c.rowid as cid, c.ref, c.datec, c.date_contrat, c.statut,";
$sql.= " s.nom, s.rowid as socid";
$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
if (!$user->rights->societe->client->voir && !$socid) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
$sql.= ", ".MAIN_DB_PREFIX."contrat as c";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."contratdet as cd ON c.rowid = cd.fk_contrat";
$sql.= " WHERE c.fk_soc = s.rowid ";
$sql.= " AND c.entity = ".$conf->entity;
if ($socid) $sql.= " AND s.rowid = ".$socid;
if (!$user->rights->societe->client->voir && !$socid) $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
if ($search_nom) {
    $sql .= natural_search('s.nom', $search_nom);
}
if ($search_contract) {
    $sql .= natural_search(array('c.rowid', 'c.ref'), $search_contract);
}
if ($sall) {
    $sql .= natural_search(array('s.nom', 'cd.label', 'cd.description'), $sall);
}
$sql.= " GROUP BY c.rowid, c.ref, c.datec, c.date_contrat, c.statut,";
$sql.= " s.nom, s.rowid";
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($conf->liste_limit + 1, $offset);

$resql=$db->query($sql);
if ($resql)
{
    $num = $db->num_rows($resql);
    $i = 0;

    print_barre_liste($langs->trans("ListOfContracts"), $page, $_SERVER["PHP_SELF"], '&search_contract='.$search_contract.'&search_nom='.$search_nom, $sortfield, $sortorder,'',$num);

    print '<table class="liste" width="100%">';

    print '<tr class="liste_titre">';
    $param='&amp;search_contract='.$search_contract;
    $param.='&amp;search_nom='.$search_nom;
    print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "c.rowid","","$param",'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Refcontrat"), "", "","","",'');
    print_liste_field_titre($langs->trans("Company"), $_SERVER["PHP_SELF"], "s.nom","","$param",'',$sortfield,$sortorder);
    //recorremos los tipos de appoint
    $aAppoint = array_appoint();
    foreach ((array) $aAppoint AS $code => $label)
      {
	print_liste_field_titre($langs->trans($label), '', "","","",'align="center"');    
      }
    print_liste_field_titre($langs->trans("Preventive"), '', "","","",'align="center"');    
    
    //print_liste_field_titre($langs->trans("DateCreation"), $_SERVER["PHP_SELF"], "c.datec","","$param",'align="center"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Total"), $_SERVER["PHP_SELF"], "c.totalht","","$param",'align="center"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Payment"), '', "","","",'align="center"');
    print_liste_field_titre($langs->trans("Balance"), '', "","","",'align="center"');
    
    print_liste_field_titre($langs->trans("DateContract"), $_SERVER["PHP_SELF"], "c.date_contrat","","$param",'align="center"',$sortfield,$sortorder);
    //print_liste_field_titre($langs->trans("Status"), $_SERVER["PHP_SELF"], "c.statut","","$param",'align="center"',$sortfield,$sortorder);
    print '<td class="liste_titre" width="16">'.$staticcontratligne->LibStatut(0,3).'</td>';
    print '<td class="liste_titre" width="16">'.$staticcontratligne->LibStatut(4,3,0).'</td>';
    print '<td class="liste_titre" width="16">'.$staticcontratligne->LibStatut(4,3,1).'</td>';
    print '<td class="liste_titre" width="16">'.$staticcontratligne->LibStatut(5,3).'</td>';
    print "</tr>\n";

    print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<tr class="liste_titre">';
    print '<td class="liste_titre">';
    print '<input type="text" class="flat" size="3" name="search_contract" value="'.$search_contract.'">';
    print '</td>';
    print '<td class="liste_titre">';
    print '<input type="text" class="flat" size="6" name="search_ref_contrat" value="'.$search_ref_contrat.'">';
    print '</td>';
    print '<td class="liste_titre">';
    print '<input type="text" class="flat" size="24" name="search_nom" value="'.$search_nom.'">';
    print '</td>';
    foreach ((array) $aAppoint AS $code => $label)
      {
	print '<td class="liste_titre">&nbsp;</td>';
      }
    print '<td class="liste_titre">&nbsp;</td>';
    //print '<td class="liste_titre">&nbsp;</td>';
    print '<td class="liste_titre">&nbsp;</td>';
    print '<td class="liste_titre">&nbsp;</td>';
    print '<td class="liste_titre">&nbsp;</td>';
    print '<td class="liste_titre">&nbsp;</td>';
    print '<td colspan="4" class="liste_titre" align="right"><input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
    print "</td>";
    print "</tr>\n";
    print '</form>';

    $var=true;
    while ($i < min($num,$limit))
    {
	$lView = true;
        $obj = $db->fetch_object($resql);
	$staticcontrat->fetch($obj->cid);
	//procesos contrato
	$htmlPrev = '';
	$aPrev = array();
	$balance = 0;
	$payment = 0;
	$aPrevlabel = array();
	$res = $objpcon->fetch_contrat($obj->cid);
	if ($res > 0)
	  {
	    $aArray = $objpcon->array;
	    foreach ((array) $aArray AS $k => $objdata)
	      {
		$res1 = $objpcom->fetch_contrat($objdata->id);//poa_process_contrat id
		if ($res1 > 0)
		  {
		    $aComp = $objpcom->array;
		    foreach ((array) $aComp AS $l => $objcom)
		      {
			$res2 = $objprev->fetch($objcom->fk_poa_prev);
			if ($res2 > 0 && $objprev->id == $objcom->fk_poa_prev && $objcom->fk_contrat == $objdata->id)
			  {
			    if (empty($aPrev[$objprev->nro_preventive.'/'.$objprev->gestion]))
			      {
				//tipo de requerimiento
				$requirement = select_requirementtype($objprev->code_requirement,'','',0,1);
				//total pagado
				$objpdev->get_sum_pcp($objcom->fk_poa_prev,$objdata->id);
				
				// if (!empty($htmlPrev)) $htmlPrev.= '<br>';
				// $htmlPrev.= '<a href="#" title="'.$objprev->label.'">'.$objprev->nro_preventive.'/'.$objprev->gestion.' '.$requirement.'</a>';
				
				$aPrev[$objprev->nro_preventive.'/'.$objprev->gestion] += $objpdev->total;
				
				$aPrevlabel[$objprev->nro_preventive.'/'.$objprev->gestion] = array('id'=>$objprev->id,
												    'label' => $objprev->label,
												    'nro'=>$objprev->nro_preventive,
												    'gestion'=>$objprev->gestion);
			      }
			  }
		      }
		    //resultado
		    $htmlPrev = '';
		    $payment = 0;
		    foreach ((array) $aPrev AS $nro_prev => $total)
		      {
			$payment+= $total;
			if (!empty($htmlPrev)) $htmlPrev.= '<br>';
			$htmlPrev.= '<a href="'.DOL_URL_ROOT.'/poa/execution/liste.php?search_nro='.$aPrevlabel[$nro_prev]['nro'].'&search_gestion='.$aPrevlabel[$nro_prev]['gestion'].'" title="'.$aPrevlabel[$nro_prev]['label'].'" target="_blank">'.$nro_prev.' '.$requirement.': '.price($total).'</a>';
		      }
		  }
	      }
	  }
	$filterref = '';
	if (!empty($search_ref_contrat))
	  {
	    $filterref = STRPOS(STRTOUPPER($staticcontrat->array_options['options_ref_contrato']),$search_ref_contrat);
	    if ($staticcontrat->id == $obj->cid && $filterref===false)
	      $lView = false;
	  }
        $var=!$var;
	//verificamos si el contrato es un addendum
	if ($conf->addendum->enabled)
	  {
	    $res = $objadd->getlist_son($obj->cid);
	    if ($res >0)
	      $lView = false;
	  }
	if ($lView)
	  {
	    print '<tr '.$bc[$var].'>';
	    print '<td class="nowrap"><a href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$obj->cid.'">';
	    print img_object($langs->trans("ShowContract"),"contract").' '.(isset($obj->ref) ? $obj->ref : $obj->cid) .'</a>';
	    if ($obj->nb_late) print img_warning($langs->trans("Late"));
	    print '</td>';
	    print '<td>';
	    if ($staticcontrat->id == $obj->cid)
	      print $staticcontrat->array_options['options_ref_contrato'];
	    else
	      print '&nbsp:';
	    print '</td>';
	    
	    print '<td><a href="../../comm/fiche.php?socid='.$obj->socid.'">'.img_object($langs->trans("ShowCompany"),"company").' '.$obj->nom.'</a></td>';
	    $objappoint->getlist($obj->cid);
	    $aCappoint = array();
	    foreach ((array) $objappoint->array AS $j => $objapp)
		$aCappoint[$objapp->code_appoint]++;
	    
	    foreach ((array) $aAppoint AS $code => $label)
	      {
		print '<td align="center">'.$aCappoint[$code].'</td>';
	      }
	    print '<td align="left">'.$htmlPrev.'</td>';

	    //print '<td align="center">'.dol_print_date($obj->datec).'</td>';
	    if ($conf->addendum->enabled)
	      {
		//obtenemos la suma de los contratos y adendums
		$objadd->getlist($obj->cid);
		print '<td align="right">'.price($objadd->aSuma['total_ttc']).'</td>';
	      }
	      else
		print '<td align="right">'.price($obj->totalht).'</td>';
	    print '<td align="right">'.price($payment).'</td>';
	    $balance = $obj->totalht - $payment;
	    print '<td align="right">'.price($balance).'</td>';
	    print '<td align="center">'.dol_print_date($db->jdate($obj->date_contrat)).'</td>';
	    //print '<td align="center">'.$staticcontrat->LibStatut($obj->statut,3).'</td>';
	    print '<td align="center">'.($obj->nb_initial>0?$obj->nb_initial:'').'</td>';
	    print '<td align="center">'.($obj->nb_running>0?$obj->nb_running:'').'</td>';
	    print '<td align="center">'.($obj->nb_expired>0?$obj->nb_expired:'').'</td>';
	    print '<td align="center">'.($obj->nb_closed>0 ?$obj->nb_closed:'').'</td>';
	    print "</tr>\n";
	  }
        $i++;
    }
    $db->free($resql);

    print "</table>";
}
else
{
    dol_print_error($db);
}


llxFooter();
$db->close();
