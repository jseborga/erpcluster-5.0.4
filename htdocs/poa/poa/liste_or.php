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
 *      \file       htdocs/poa/poa/liste.php
 *      \ingroup    Plan Operativo Anual
 *      \brief      Page liste des poa
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/poa/poa/class/poapoa.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidapre.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidacom.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poapartidadev.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/reformulated/class/poareformulated.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/reformulated/class/poareformulatedof.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/reformulated/class/poareformulatedto.class.php");

require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");

$langs->load("poa@poa");

if (!$user->rights->poa->poa->leer)
  accessforbidden();

$object = new Poapoa($db);
$objprev = new Poapartidapre($db);
$objcomp = new Poapartidacom($db);
$objdeve = new Poapartidadev($db);
$objrefo = new Poareformulated($db);
$objrefoof = new Poareformulatedof($db);
$objrefoto = new Poareformulatedto($db);

$id = GETPOST('id');
$action = GETPOST('action');
if (isset($_GET['mostrar']))
  $_SESSION['opver'] = '';
if (isset($_GET['opver']))
  $_SESSION['opver'] = $_GET['opver'];
$opver = $_SESSION['opver'];
//gestion
$gestion = GETPOST('gestion');
if (empty($gestion))
  $_SESSION['gestion'] = date('Y');
 else
   $_SESSION['gestion'] = $gestion;
$gestion = $_SESSION['gestion'];


//ocultar columnas
$vercol = GETPOST('vercol');
$aCol = array(1=>2,
	      2=>1,
	      3=>4,
	      4=>3,
	      7=>8,
	      8=>7,
	      9=>array(10,15),
	      10=>array(9,15),
	      15=>array(9,10),
	      11=>array(12,16),
	      12=>array(11,16),
	      16=>array(11,12),
	      13=>array(14,17),
	      14=>array(13,17),
	      17=>array(13,14));

if (empty($_SESSION['numCol']))
  {
    $_SESSION['numCol'] = array(1=>true,
				2=>false,
				3=>true,
				4=>false,
				7=>true,
				8=>false,
				9=>true,
				10=>false,
				11=>true,
				12=>false,
				13=>true,
				14=>false);
    
  }

if(isset($_GET['vercol']))
  {
    $_SESSION['numCol'][$_GET['vercol']] = true;
    if (is_array($aCol[$_GET['vercol']]))
      {
	foreach($aCol[$_GET['vercol']] AS $i1 => $nCol1)
	  {
	    $_SESSION['numCol'][$nCol1] = false;
	  }
      }
    else
      $_SESSION['numCol'][$aCol[$_GET['vercol']]] = false;
  }
if ($_SESSION['numCol'][8] == true)
  $opver = true;
if ($_SESSION['numCol'][7] == true)
  $opver = false;

$nVersion = 0;
$lVersion = false;
$aOf = array();
$aTo = array();

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$search_sigla     = GETPOST("search_sigla");
$search_label     = GETPOST("search_label");
$search_pseudonym = GETPOST("search_pseudonym");
$search_partida   = GETPOST("search_partida");
$search_amount    = GETPOST("search_amount");
if (isset($_GET['nosearch_x']))
  {
    $search_sigla     = '';
    $search_label     = '';
    $search_pseudonym = '';
    $search_partida   = '';
    $search_amount    = '';
  }
$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.version,p.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
if (empty($_GET['top']))
  $_SESSION['arrayPoa'] = array();
if ($_GET['top'] == 1)
  $_SESSION['filterrowid'] = $_GET['id'];

$sql  = "SELECT p.rowid AS id, p.gestion, p.fk_structure, p.label, p.pseudonym, p.partida, p.amount, p.classification, p.source_verification, p.unit,p.statut, ";
$sql.= " p.weighting, p.version, ";
$sql.= " p.m_jan, p.m_feb, p.m_mar, p.m_apr, p.m_may, p.m_jun, p.m_jul, p.m_aug, p.m_sep, p.m_oct, p.m_nov, p.m_dec, ";
$sql.= " s.label AS labelstructure, s.sigla ";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as p ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure AS s ON p.fk_structure = s.rowid ";
$sql.= " WHERE p.entity = ".$conf->entity;
$sql.= " AND p.gestion = ".$gestion;
$sql.= " AND p.statut = 1 "; //solo los aprobados

if ($search_sigla)   $sql .= " AND s.sigla LIKE '%".$db->escape($search_sigla)."%'";
if ($search_label)   $sql .= " AND p.label LIKE '%".$db->escape($search_label)."%'";
if ($search_pseudonym)   $sql .= " AND p.seudonym LIKE '%".$db->escape($search_pseudonym)."%'";
if ($search_partida)   $sql .= " AND p.partida LIKE '%".$db->escape($search_partida)."%'";
if ($search_amount)   $sql .= " AND p.amount LIKE '%".$db->escape($search_amount)."%'";

if ($sall)
{
    $sql.= " AND (p.ref like '%".$sall."%' OR p.label like '%".$sall."%' OR p.active like '%".$sall."%')";
}
$sql.= $db->order($sortfield,$sortorder);
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
$numCol = $_SESSION['numCol'];

if ($result)
  {
    $num = $db->num_rows($result);
    //recuperamos las partidas de la gestion
    $aPartida = get_partida($gestion);
    //verificamos que version de reformulado existe
    $objrefo->fetch_version($gestion);
    if (count($objrefo->aVersion) > 0)
      {
	$lVersion = true;
	foreach($objrefo->aVersion AS $j => $objVer)
	  {
	    $nVersion = $j;
	    foreach((array) $objVer AS $k => $objVersion)
	      {
		$aReform[$k] = $k;
	      }
	  }
	list($aOf,$aTo) = $objrefoof->get_sumaref($aReform);

      }
    $i = 0;
    $aArrcss = array('poa/css/style.css');
    $aArrjs = array('poa/js/config.js');
    $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
    llxHeader("",$langs->trans("Liste POA"),$help_url,'','','',$aArrjs,$aArrcss);
    
    print_barre_liste($langs->trans("Liste POA"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    //filtro
    print '<form method="GET" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">'."\n";

    // Filter on categories
    $moreforfilter='';
    if (! empty($conf->categorie->enabled))
      {
	$moreforfilter.=$langs->trans('Categories'). ': ';
	$moreforfilter.=$htmlother->select_categories(1,$search_categ,'search_categ',1);
	$moreforfilter.=' &nbsp; &nbsp; &nbsp; ';
      }
    if ($moreforfilter)
      {
	print '<div class="liste_titre">';
	print $moreforfilter;
	print '</div>';
      }
    //print '<div class="tableContainer">';
    print '<table class="noline" width="100%" cellspacing="0" cellpadding="0">';

    //init comandos ocultar mostrar
    print '<thead>';
    print '<tr">';
    print '<th>'.$langs->trans("Meta").'</th>';

    if ($numCol[1]==true)
      {
	print '<th>';
	print '<button type="submit" name="vercol" value="2">'.img_picto('','off').'</button>';
	print '<br>';
	print $langs->trans("Label");
	print '<br>';
	print '</th>';
      }
    if ($numCol[2]==true)
      {
	print '<th>';
	print '<button type="submit" name="vercol" value="1">'.img_picto('','on').'</button>';
	print ' ';
	print $langs->trans("Pseudonym");
	print '</th>';
      }
    // if ($numCol[3]==true)
    //   {
    // 	print '<th>';
    // 	print '<button type="submit" name="vercol" value="4">'.img_picto('','off').'</button>';
    // 	print '</th>';
    //   }
    // if ($numCol[4]==true)
    //   {
    // 	print '<th>';
    // 	print '<button type="submit" name="vercol" value="3">'.img_picto('','on').'</button>';
    // 	print '</th>';
    //   }
    print '<th>'.$langs->trans("Partida").'</th>';
    print '<th align="center">'.$langs->trans("Budget").'<br>'.$gestion.'</th>';
    if ($lVersion)
      print '<th align="center">'.$langs->trans("Reformulated").' '.$nVersion.'</th>';


    //7 total presup
    if ($numCol[7]==true)
      {
	print '<th>';
	print '<button type="submit" name="vercol" value="8">'.img_picto('','on').'</button>';
	print ' ';
	print $langs->trans("Approved budget").' '.$nVersion;

	print '</th>';
      }
    else
      {
	print '<th>';
	print '<button type="submit" name="vercol" value="7">'.img_picto('','off').'</button>';
	print ' ';
	print $langs->trans("Approved budget").' '.$nVersion;

	print '</th>';
      }
    if ($opver == true)
      {
	print '<th>&nbsp;</th>';
	print '<th>&nbsp;</th>';
	print '<th>&nbsp;</th>';
	print '<th>&nbsp;</th>';
	print '<th>&nbsp;</th>';
	print '<th>&nbsp;</th>';
	print '<th>&nbsp;</th>';
	print '<th>&nbsp;</th>';
	print '<th>&nbsp;</th>';
	print '<th>&nbsp;</th>';
	print '<th>&nbsp;</th>';
	print '<th>&nbsp;</th>';
      }
    if ($numCol[9]==true)
      {
	print '<th>';
	print '<button type="submit" name="vercol" value="10">'.img_picto('','on').'</button>';
	print '<br>';
	print $langs->trans("Preventive");
	print '</th>';
      }
    if ($numCol[10]==true)
      {
	print '<th>';
	print '<button type="submit" name="vercol" value="15">'.img_picto('','off').'</button>';
	print '<br>';
	print $langs->trans("Preventive").'<br>'.'%';

	print '</th>';
      }
    if ($numCol[15]==true)
      {
	print '<th>';
	print '<button type="submit" name="vercol" value="9">'.img_picto('','off').'</button>';
	print '<br>';
	print $langs->trans("Balance");
	print '</th>';
      }
    if ($numCol[11]==true)
      {
	print '<th>';
	print '<button type="submit" name="vercol" value="12">'.img_picto('','on').'</button>';
	print '<br>';
	print $langs->trans("Committed");

	print '</th>';
      }
    if ($numCol[12]==true)
      {
	print '<th>';
	print '<button type="submit" name="vercol" value="16">'.img_picto('','off').'</button>';
	print '<br>';
	print $langs->trans("Committed").'<br>'.'%';

	print '</th>';
      }
    if ($numCol[16]==true)
      {
	print '<th>';
	print '<button type="submit" name="vercol" value="11">'.img_picto('','on').'</button>';
	print '<br>';
	print $langs->trans("Balancecommitted");

	print '</th>';
      }

    if ($numCol[13]==true)
      {
	print '<th>';
	print '<button type="submit" name="vercol" value="14">'.img_picto('','on').'</button>';
	print '<br>';
	print $langs->trans("Accrued");

	print '</th>';
      }
    if ($numCol[14]==true)
      {
	print '<th>';
	print '<button type="submit" name="vercol" value="17">'.img_picto('','off').'</button>';
	print '<br>';
	print $langs->trans("Accrued").'<br>'.'%';

	print '</th>';
      }
    if ($numCol[17]==true)
      {
	print '<th>';
	print '<button type="submit" name="vercol" value="13">'.img_picto('','on').'</button>';
	print '<br>';
	print $langs->trans("Balanceaccrued");

	print '</th>';
      }
    print '<th>'.$langs->trans("Action").'</th>';    
    print '</tr>';
    //fin comandos ocultar mostrar    




    $aMark = array();
    $aMark[date('m')*1] = ' style="background:#989ea5; color:#ffffff;"';

    $aColImpair = ' class="colimpair"';
    $aColPair   = ' class="colpair"';

    // if ($opver == true)
    //   {
    // 	print '<th align="center"'.(!empty($aMark[1])?$aMark[1]:'').'>'.$langs->trans("Jan").'</th>';
    // 	print '<th align="center"'.(!empty($aMark[2])?$aMark[2]:'').'>'.$langs->trans("Feb").'</th>';
    // 	print '<th align="center"'.(!empty($aMark[3])?$aMark[3]:'').'>'.$langs->trans("Mar").'</th>';
    // 	print '<th align="center"'.(!empty($aMark[4])?$aMark[4]:'').'>'.$langs->trans("Apr").'</th>';
    // 	print '<th align="center"'.(!empty($aMark[5])?$aMark[5]:'').'>'.$langs->trans("May").'</th>';
    // 	print '<th align="center"'.(!empty($aMark[6])?$aMark[6]:'').'>'.$langs->trans("Jun").'</th>';
    // 	print '<th align="center"'.(!empty($aMark[7])?$aMark[7]:'').'>'.$langs->trans("Jul").'</th>';
    // 	print '<th align="center"'.(!empty($aMark[8])?$aMark[8]:'').'>'.$langs->trans("Aug").'</th>';
    // 	print '<th align="center"'.(!empty($aMark[9])?$aMark[9]:'').'>'.$langs->trans("Sep").'</th>';
    // 	print '<th align="center"'.(!empty($aMark[10])?$aMark[10]:'').'>'.$langs->trans("Oct").'</th>';
    // 	print '<th align="center"'.(!empty($aMark[11])?$aMark[11]:'').'>'.$langs->trans("Nov").'</th>';
    // 	print '<th align="center"'.(!empty($aMark[12])?$aMark[12]:'').'>'.$langs->trans("Dec").'</th>';
	
    //   }

    // if ($numCol[13])
    //   print '<th class="coldeve">'.$langs->trans("Accrued").'</th>';
    // if ($numCol[14])
    //   print '<th class="coldeve">'.$langs->trans("Accrued").'<br>'.'%'.'</th>';
    // if ($numCol[17])
    //   print '<th class="coldeve">'.$langs->trans("Balanceaccrued").'</th>';
    // print '<th>'.$langs->trans("Action").'</th>';    
    // print '</tr>';


    $parameters=array();
    $formconfirm=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
   
    print '<tr>';    
    print '<th><input type="text" class="flat" size="5" name="search_sigla" value="'.$search_sigla.'"></th>';

    if ($numCol[1])
      print '<th class="liste_titre"><input type="text" class="flat" name="search_label" value="'.$search_label.'"></th>';
    if ($numCol[2])    
      print '<th class="liste_titre"><input type="text" class="flat" name="search_pseudonym" value="'.$search_pseudonym.'"></th>';
    
    print '<th align="left" class="liste_titre">';
    print '<input class="flat" type="text" size="5" name="search_partida" value="'.$search_partida.'">';
    print '</th>';
    
    print '<th align="left" class="liste_titre">';
    print '<input class="flat" type="text" size="7" name="search_amount" value="'.$search_amount.'">';
    print '</th>';
    if ($lVersion)
      print '<th>&nbsp;</th>';
    print '<th>&nbsp;</th>'; //presup aprobado

    if ($opver == 1)
      {
    	print '<th '.(!empty($aMark[1])?$aMark[1]:'').'>&nbsp;</th>';
    	print '<th '.(!empty($aMark[2])?$aMark[2]:'').'>&nbsp;</th>';
    	print '<th '.(!empty($aMark[3])?$aMark[3]:'').'>&nbsp;</th>';
    	print '<th '.(!empty($aMark[4])?$aMark[4]:'').'>&nbsp;</th>';
    	print '<th '.(!empty($aMark[5])?$aMark[5]:'').'>&nbsp;</th>';
    	print '<th '.(!empty($aMark[6])?$aMark[6]:'').'>&nbsp;</th>';
    	print '<th '.(!empty($aMark[7])?$aMark[7]:'').'>&nbsp;</th>';
    	print '<th '.(!empty($aMark[8])?$aMark[8]:'').'>&nbsp;</th>';
    	print '<th '.(!empty($aMark[9])?$aMark[9]:'').'>&nbsp;</th>';
    	print '<th '.(!empty($aMark[10])?$aMark[10]:'').'>&nbsp;</th>';
    	print '<th '.(!empty($aMark[11])?$aMark[11]:'').'>&nbsp;</th>';
    	print '<th '.(!empty($aMark[12])?$aMark[12]:'').'>&nbsp;</th>';
      }
    print '<th>&nbsp;</th>';
    print '<th>&nbsp;</th>';
    //print '<td>&nbsp;</td>';
    // print '<td>&nbsp;</td>';
    // print '<td>&nbsp;</td>';
    print '<th>&nbsp;</th>';

    print '<th nowrap class="liste_titre" align="right">';
    print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
    print '&nbsp;';
    print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
    print '</th>';
    print '</tr>';
    print '</thead>';

    print '<tbody>';
    $parameters=array();
    $formconfirm=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
    

    if ($num)
      {
	$var=True;
	//totales
	$sumaPresup = 0;
	$sumaRef1 = 0;
	$sumaPrev = 0;
	$sumaComp = 0;
	$sumaDeve = 0;
	$sumaPaid = 0;
	$sumaAprob = 0;
	while ($i < min($num,$limit))
	  {	      
	    $obj = $db->fetch_object($result);	
	    $var=!$var;
	    if ($var) $classvar = 'class="normalRow"'; else $classvar = 'class="alternateRow"';
	    print "<tr $classvar>";
	    print '<td>'.'<a href="#" title="'.$obj->labelstructure.'">'.$obj->sigla.'</a>'.'</td>';
	    if ($numCol[1]==true)
	      print '<td>'.$obj->label.'</td>';
	    if ($numCol[2]==true)
	      print '<td>'.$obj->pseudonym.'</td>';
	    if ($numCol[3]==true)
	    print '<td>'.'<a href="#" title="'.$aPartida[$obj->partida].'">'.$obj->partida.'</a>'.'</td>';
	    // if ($numCol[4]==true)
	    //   print '<td>'.$aPartida[$obj->partida].'</td>';
	    $nPresup = 0;
	    if ($obj->version == 0)
	      {
		print '<td align="right">'.price(price2num($obj->amount,'MT')).'</td>';
		$sumaPresup+=$obj->amount;
		$nPresup = $obj->amount;
	      }
	    else
	      print '<td align="right">&nbsp;</td>';
	    if ($lVersion)
	      {
		//buscamos que suma y que resta		
		$nReform = $aOf[$obj->fk_structure][$obj->id][$obj->partida] - 
		  $aTo[$obj->fk_structure][$obj->id][$obj->partida];
		print '<td align="right">'.price(price2num($nReform,'MT')).'</td>';
		$sumaRef1 += $nReform;
	      }
	    $nTotalAp = $nPresup+$nReform;
	    $sumaAprob+=$nTotalAp;
	    print '<td align="right">'.price(price2num($nTotalAp,'MT')).'</td>';
	    if ($opver == 1)
	      {
		
		print '<td '.(!empty($aMark[1])?$aMark[1]:$aColImpair).' align="right">'.price(price2num($obj->m_jan,'MT')).'</td>';
		print '<td '.(!empty($aMark[2])?$aMark[2]:$aColPair).' align="right">'.price(price2num($obj->m_feb,'MT')).'</td>';
		print '<td '.(!empty($aMark[3])?$aMark[3]:$aColImpair).' align="right">'.price(price2num($obj->m_mar,'MT')).'</td>';
		print '<td '.(!empty($aMark[4])?$aMark[4]:$aColPair).' align="right">'.price(price2num($obj->m_apr,'MT')).'</td>';
		print '<td '.(!empty($aMark[5])?$aMark[5]:$aColImpair).' align="right">'.price(price2num($obj->m_may,'MT')).'</td>';
		print '<td '.(!empty($aMark[6])?$aMark[6]:$aColPair).' align="right">'.price(price2num($obj->m_jun,'MT')).'</td>';
		print '<td '.(!empty($aMark[7])?$aMark[7]:$aColImpair).' align="right">'.price(price2num($obj->m_jul,'MT')).'</td>';
		print '<td '.(!empty($aMark[8])?$aMark[8]:$aColPair).' align="right">'.price(price2num($obj->m_aug,'MT')).'</td>';
		print '<td '.(!empty($aMark[9])?$aMark[9]:$aColImpair).' align="right">'.price(price2num($obj->m_sep,'MT')).'</td>';
		print '<td '.(!empty($aMark[10])?$aMark[10]:$aColPair).' align="right">'.price(price2num($obj->m_oct,'MT')).'</td>';
		print '<td '.(!empty($aMark[11])?$aMark[11]:$aColImpair).' align="right">'.price(price2num($obj->m_nov,'MT')).'</td>';
		print '<td '.(!empty($aMark[12])?$aMark[12]:$aColPair).' align="right">'.price(price2num($obj->m_dec,'MT')).'</td>';
		$sumaEne+=$obj->m_ene;
		$sumaFeb+=$obj->m_feb;
		$sumaMar+=$obj->m_mar;
		$sumaApr+=$obj->m_apr;
		$sumaMay+=$obj->m_may;
		$sumaJun+=$obj->m_jun;
		$sumaJul+=$obj->m_jul;
		$sumaAug+=$obj->m_aug;
		$sumaSep+=$obj->m_sep;
		$sumaOct+=$obj->m_oct;
		$sumaNov+=$obj->m_nov;
		$sumaDec+=$obj->m_dec;

	      }

	    if ($objprev->getsum_str_part($obj->gestion,$obj->fk_structure,$obj->id, $obj->partida))
	      {
		if($numCol[9])
		  {
		    $total = $objprev->total;
		    print '<td class="colprev" align="right">'.price(price2num($objprev->total,'MT')).'</td>';
		  }
		if($numCol[10])
		  {
		    
		    if ($nTotalAp > 0)
		      $total = $objprev->total / $nTotalAp * 100;
		    else
		      $total = 0;
		    print '<td class="colprev" align="right">'.price(price2num($total,'MT')).' %'.'</td>';
		  }
		$balance = $nTotalAp - $total;

		if ($numCol[15])
		  print '<td class="colprev" align="right">'.number_format($balance,2).'</td>';

	      }
	    else
	      {
		$total = 0;
		print '<td class="colprev" align="right">&nbsp;</td>';
	      }
	    $sumaPrev+=$total;
	    $sumaPrevm+= $objprev->total;
	    if ($objcomp->getsum_str_part($obj->gestion,$obj->fk_structure,$obj->id, $obj->partida))
	      {
		if($numCol[11])
		  {
		    $totalc = $objcomp->total;
		    print '<td class="colcomp" align="right">'.price(price2num($objcomp->total,'MT')).'</td>';
		  }
		if($numCol[12])
		  {
		    
		    if ($nTotalAp > 0)
		      $totalc = $objcomp->total / $nTotalAp * 100;
		    else
		      $totalc = 0;		    
		    print '<td class="colcomp" align="right">'.price(price2num($totalc,'MT')).' %'.'</td>';
		  }

		$balancec = $nTotalAp - $totalc;
		if ($numCol[16])
		  print '<td class="colcomp" align="right">'.number_format($balancec,2).'</td>';
	      }
	    else
	      {
		$totalc = 0;
		print '<td class="colcomp" align="right">&nbsp;</td>';
	      }
	    $sumaComp+=$totalc;
	    $sumaCompm+=$objcomp->total;
	    if ($objdeve->getsum_str_part($obj->gestion,$obj->fk_structure,$obj->id, $obj->partida))
	      {
		if($numCol[13])
		  {
		    $totald = $objdeve->total;
		    print '<td class="coldeve" align="right">'.price(price2num($objdeve->total,'MT')).'</td>';
		  }
		if($numCol[14])
		  {
		    
		    if ($nTotalAp > 0)
		      $totald = $objdeve->total / $nTotalAp * 100;
		    else
		      $totald = 0;
		    print '<td class="coldeve" align="right">'.price(price2num($totald,'MT')).' %'.'</td>';
		  }
		$balanced = $nTotalAp - $totald;
		if ($numCol[17])
		  print '<td class="coldeve" align="right">'.number_format($balanced,2).'</td>';
	      }
	    else
	      {
		$totald = 0;
		print '<td class="coldeve" align="right">&nbsp;</td>';
	      }
	    $sumaDeve+=$totald;
	    $sumaDevem+=$objdeve->total;

	    print '<td align="center"><a href="fiche.php?id='.$obj->id.'">'.img_picto($langs->trans('Edit'),'edit').'</a></td>';
	    print '</tr>';
	    $i++;
	  }
      }
    //totales
    print '<tr class="liste_total"><td colspan="3">'.$langs->trans("Total").'</td>';
    print '<td align="right">';
    print price($sumaPresup);
    print '</td>';
    if ($lVersion)
      {
	print '<td align="right">';
	print price($sumaRef1);
	print '</td>';
      }
    print '<td align="right">';
    print price($sumaAprob);
    print '</td>';
    if ($opver == true)
      {
	print '<td class="colimpair" align="right">';
	print price($sumaEne);
	print '</td>';
	print '<td class="colpair" align="right">';
	print price($sumaFeb);
	print '</td>';
	print '<td class="colimpair" align="right">';
	print price($sumaMar);
	print '</td>';
	print '<td class="colpair" align="right">';
	print price($sumaApr);
	print '</td>';
	print '<td class="colimpair" align="right">';
	print price($sumaMay);
	print '</td>';
	print '<td class="colpair" align="right">';
	print price($sumaJun);
	print '</td>';
	print '<td class="colimpair" align="right">';
	print price($sumaJul);
	print '</td>';
	print '<td class="colpair" align="right">';
	print price($sumaAug);
	print '</td>';
	print '<td class="colimpair" align="right">';
	print price($sumaSep);
	print '</td>';
	print '<td class="colpair" align="right">';
	print price($sumaOct);
	print '</td>';
	print '<td class="colimpair" align="right">';
	print price($sumaNov);
	print '</td>';
	print '<td class="colpair" align="right">';
	print price($sumaDec);
	print '</td>';
      }
    print '<td align="right">';
    if ($numCol[9])
      print price(price2num($sumaPrev,'MT'));
    if ($numCol[10])
      {
	if ($sumaAprob>0)
	  print price(price2num($sumaPrevm/$sumaAprob*100,'MT'));
	else
	  print price(0);
      }   
    if ($numCol[15])
      print price(0);
    print '</td>';

    print '<td align="right">';
    if ($numCol[11])
      print price($sumaComp);
    if ($numCol[12])
      {
	if ($sumaAprob>0)
	  print price(price2num($sumaCompm/$sumaAprob*100,'MT'));
	else
	  print price(0);
      }
    if ($numCol[16])
      print price(0);
    print '</td>';

    print '<td align="right">';
    if ($numCol[13])	
      print price($sumaDeve);
    if ($numCol[14])
      {
	if ($sumaAprob>0)
	  print price(price2num($sumaDevem/$sumaAprob*100,'MT'));
	else
	  print price(0);
      }
    if ($numCol[17])
      print price(0);
    print '</td>';

    print '</tr>';
    print '</tbody>';
    
    print '</table>';
    //print '</div>';
    print '</form>';

    $db->free($result);

    print "<div class=\"tabsAction\">\n";
    
    if ($action == '')
      {
	if ($user->rights->poa->poa->crear)
	  print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	else
	  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
      }
    print '</div>';
  }
 else
   {
     dol_print_error($db);
   }


$db->close();

llxFooter();
?>
