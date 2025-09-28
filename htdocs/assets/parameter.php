<?php
/* Copyright (C) 2001-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/product/index.php
 *  \ingroup    product
 *  \brief      Page accueil des produits et services
 */

require '../main.inc.php';
//require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobs.class.php';
// require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/user/class/usergroup.class.php';

// require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapre.class.php';
// require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidacom.class.php';
// require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidadev.class.php';

$type=isset($_GET["type"])?$_GET["type"]:(isset($_POST["type"])?$_POST["type"]:'');

// Security check
//$result=restrictedArea($user,'contab');

$gestion = GETPOST('gestion');
if (isset($gestion))
  $_SESSION['gestion'] = $gestion;
if (empty($_SESSION['gestion']))
  $_SESSION['gestion'] = date('Y');
$gestion = $_SESSION['gestion'];

$langs->load("assets@assets");

//$object = new Mjobs($db);
// $objpre = new Poapartidapre($db);
// $objcom = new Poapartidacom($db);
// $objdev = new Poapartidadev($db);

//determinamos a que gruipo pertenece
// $objusrgroup = new Usergroup($db);
// $aGroup = $objusrgroup->listGroupsForUser($user->id);
// foreach((array) $aGroup AS $i => $objgroup)
// {
//   $arrayGroup[$objgroup->name] = $objgroup->name;
// }

/*
 * View
 */

$transAreaType = $langs->trans("Fixedasset");
$helpurl='';
$helpurl='EN:Module_Assets|FR:Module_Assets|ES:M&oacute;dulo_Assets';


llxHeader("",$langs->trans("Fixedasset"),$helpurl);

print_fiche_titre($transAreaType);


//print '<table border="0" width="100%" class="notopnoleftnoright">';
//print '<tr><td valign="top" width="30%" class="notopnoleft">';
print '<div class="fichecenter"><div class="fichethirdleft">';


/*
 * Zone recherche produit/service
 */
$rowspan=2;
// if (! empty($conf->barcode->enabled)) $rowspan++;
// print '<form method="post" action="'.DOL_URL_ROOT.'/contab/accounts/liste.php">';
// print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
// print '<table class="noborder nohover" width="100%">';
// print "<tr class=\"liste_titre\">";
// print '<td colspan="3">'.$langs->trans("Search").'</td></tr>';
// print "<tr ".$bc[false]."><td>";
// print $langs->trans("Ref").':</td><td><input class="flat" type="text" size="14" name="sref"></td>';
// print '<td rowspan="'.$rowspan.'"><input type="submit" class="button" value="'.$langs->trans("Search").'"></td></tr>';
// if (! empty($conf->barcode->enabled))
// {
// 	print "<tr ".$bc[false]."><td>";
// 	print $langs->trans("BarCode").':</td><td><input class="flat" type="text" size="14" name="sbarcode"></td>';
// 	//print '<td><input type="submit" class="button" value="'.$langs->trans("Search").'"></td>';
// 	print '</tr>';
// }
// print "<tr ".$bc[false]."><td>";
// print $langs->trans("Other").':</td><td><input class="flat" type="text" size="14" name="sall"></td>';
// //print '<td><input type="submit" class="button" value="'.$langs->trans("Search").'"></td>';
// print '</tr>';
// print "</table></form><br>";



/*
 * Statistics area
 */

$third = array(
	       'prev' => 0,
	       'comp' => 0,
	       'deve' => 0,
	       'paid' => 0
);
$total=0;

// $sql = "SELECT s.rowid AS id , s.fk_structure, s.partida, s.version, s.statut, s.amount";
// $sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as s";
// $sql.= ' WHERE s.entity IN ('.getEntity('poa_prev', 1).')';
// $sql.= " AND s.gestion = ".$gestion;

// // $sql = "SELECT s.rowid, s.statut, s.amount";
// // $sql.= " FROM ".MAIN_DB_PREFIX."poa_prev as s";
// // $sql.= ' WHERE s.entity IN ('.getEntity('poa_prev', 1).')';

// // if ($socid)	$sql.= " AND s.rowid = ".$socid;
// // if (! $user->rights->fournisseur->lire) $sql.=" AND (s.fournisseur <> 1 OR s.client <> 0)";    // client=0, fournisseur=0 must be visible
// //print $sql;

// $result = $db->query($sql);
// if ($result)
//   {
//     while ($objpoa = $db->fetch_object($result))
//       {
// 	$found=0;
// 	// if ($objpoa->version == 0)
// 	//   {
// 	//     $found = 1;
// 	//     $third['pres']+= $objpoa->amount;
// 	//   }
// 	//buscamos el preventivo, comprometido, devengado
// 	$objpre->getsum_str_part($gestion,$objpoa->fk_structure,$objpoa->id,$objpoa->partida);
// 	$third['prev']+=$objpre->total;
// 	$objcom->getsum_str_part($gestion,$objpoa->fk_structure,$objpoa->id,$objpoa->partida);
// 	$third['comp']+=$objcom->total;
// 	$objdev->getsum_str_part($gestion,$objpoa->fk_structure,$objpoa->id,$objpoa->partida);
// 	$third['deve']+=$objdev->total;

// 	// if ($objp->statut == 1)
// 	//   {
// 	//     $found = 1;
// 	//     $third['prev']+=$objp->amount;
// 	//   }
// 	// if ($objp->statut == 2)
// 	//   {
// 	//     $found = 1;
// 	//     $third['comp']+=$objp->amount;
// 	//   }
// 	// if ($objp->statut == 3)
// 	//   {
// 	//     $found = 1;
// 	//     $third['deve']+=$objp->amount;
// 	//   }
// 	// if ($objp->statut == 4)
// 	//   {
// 	//     $found = 1;
// 	//     $third['paid']+=$objp->amount;
// 	//   }
// 	if ($objpoa->version == 0) $total+=$objpoa->amount;
//       }
//   }
//  else 
//    dol_print_error($db);

// print '<table class="noborder" width="100%">';
// print '<tr class="liste_titre"><th colspan="2">'.$langs->trans("Statistics").'</th></tr>';
// if (! empty($conf->use_javascript_ajax) && 
//     ((round($third['prev'])?1:0)+(round($third['comp'])?1:0)+(round($third['deve'])?1:0)+(round($third['paid'])?1:0)))
//   {
//     print '<tr><td align="center">';
//     // $dataseries=array();
//     // $dataseries[]=array('label'=>$langs->trans("Budget"),'data'=>round($third['pres']));
//     $dataseries[]=array('label'=>$langs->trans("Preventive"),'data'=>round($third['prev']));
//     $dataseries[]=array('label'=>$langs->trans("Committed"),'data'=>round($third['comp']));
//     $dataseries[]=array('label'=>$langs->trans("Accrued"),'data'=>round($third['deve']));
//     $dataseries[]=array('label'=>$langs->trans("Paid"),'data'=>round($third['paid']));
//     $data=array('series'=>$dataseries);
//     dol_print_graph('stats',300,180,$data,1,'pie',0);
//     print '</td></tr>';
//   }
//  else
//    {
//      $statstring = "<tr $bc[0]>";
//      $statstring.= '<td><a href="'.DOL_URL_ROOT.'/poa/execution/liste.php">'.$langs->trans("Budget").'</a></td><td align="right">'.round($third['pres']).'</td>';
//      $statstring.= "</tr>";

//      $statstring = "<tr $bc[0]>";
//      $statstring.= '<td><a href="'.DOL_URL_ROOT.'/poa/execution/liste.php">'.$langs->trans("Preventive").'</a></td><td align="right">'.round($third['prev']).'</td>';
//      $statstring.= "</tr>";

//      $statstring.= "<tr $bc[1]>";
//      $statstring.= '<td><a href="'.DOL_URL_ROOT.'/poa/execution/liste.php">'.$langs->trans("Committed").'</a></td><td align="right">'.round($third['comp']).'</td>';
//      $statstring.= "</tr>";

//      $statstring2 = "<tr $bc[0]>";
//      $statstring2.= '<td><a href="'.DOL_URL_ROOT.'/poa/execution/liste.php">'.$langs->trans("Accrued").'</a></td><td align="right">'.round($third['deve']).'</td>';
//      $statstring2.= "</tr>";

//      $statstring2 = "<tr $bc[0]>";
//      $statstring2.= '<td><a href="'.DOL_URL_ROOT.'/poa/execution/liste.php">'.$langs->trans("Paid").'</a></td><td align="right">'.round($third['paid']).'</td>';
//      $statstring2.= "</tr>";

//     print $statstring;
//     print $statstring2;
// }
// print '<tr class="liste_total"><td>'.$langs->trans("Total POA").'</td><td align="right">';
// print number_format(price2num($total,'MT'),2);
// print '</td></tr>';
// print '</table>';


// // //print '</td><td valign="top" width="70%" class="notopnoleftnoright">';
// // print '</div>';
// // print '<div class="fichethirdleft">';


// // //estadisticas fin

// // /*
// //  * Statistics area 2
// //  */
// // //tipos de mantenimiento
// // $third2 = array(
// // 		'CORR' => 0,
// // 		'PREV' => 0,
// // 		'CONT' => 0,
// // 		'OTRO' => 0
// // );
// // $total=0;

// // $sql = "SELECT s.rowid, s.typemant";
// // $sql.= " FROM ".MAIN_DB_PREFIX."m_jobs as s";
// // $sql.= ' WHERE s.entity IN ('.getEntity('m_jobs', 1).')';

// // $result = $db->query($sql);
// // if ($result)
// //   {
// //     while ($objp = $db->fetch_object($result))
// //       {
// // 	$found=0;
	
// // 	if ($objp->typemant == 'MANT_CORR')
// // 	  {
// // 	    $found = 1;
// // 	    $third2['CORR']++;
// // 	  }
// // 	elseif ($objp->typemant == 'MANT_PREV')
// // 	  {
// // 	    $found = 1;
// // 	    $third2['PREV']++;
// // 	  }
// // 	elseif ($objp->typemant == 'MANT_CONT')
// // 	  {
// // 	    $found = 1;
// // 	    $third2['CONT']++;
// // 	  }
// // 	else
// // 	  {
// // 	    $found = 1;
// // 	    $third2['OTRO']++;
// // 	  }
// //         if ($found) $total++;
// //       }
// //   }
// //  else 
// //    dol_print_error($db);

// // print '<table class="noborder" width="100%">';
// // print '<tr class="liste_titre"><th colspan="2">'.$langs->trans("Statistics").'</th></tr>';
// // if (! empty($conf->use_javascript_ajax) && 
// //     ((round($third2['CORR'])?1:0)+(round($third2['PREV'])?1:0)+(round($third2['CONT'])?1:0)+(round($third2['OTRO'])?1:0) >= 2))
// //   {
// //     print '<tr><td align="center">';
// //     $dataseries=array();
// //     $dataseries[]=array('label'=>$langs->trans("Corrective"),'data'=>round($third2['CORR']));
// //     $dataseries[]=array('label'=>$langs->trans("Preventive"),'data'=>round($third2['PREV']));
// //     $dataseries[]=array('label'=>$langs->trans("Control"),'data'=>round($third2['CONT']));
// //     $dataseries[]=array('label'=>$langs->trans("Others"),'data'=>round($third2['OTRO']));
// //     $data=array('series'=>$dataseries);
// //     dol_print_graph('stats',300,180,$data,1,'pie',0);
// //     print '</td></tr>';
// //   }
// //  else
// //    {
// //      $statstring = "<tr $bc[0]>";
// //      $statstring.= '<td><a href="'.DOL_URL_ROOT.'/comm/prospect/list.php">'.$langs->trans("Corrective").'</a></td><td align="right">'.round($third2['CORR']).'</td>';
// //      $statstring.= "</tr>";

// //      $statstring.= "<tr $bc[1]>";
// //      $statstring.= '<td><a href="'.DOL_URL_ROOT.'/comm/list.php">'.$langs->trans("Preventive").'</a></td><td align="right">'.round($third2['PREV']).'</td>';
// //      $statstring.= "</tr>";

// //      $statstring2 = "<tr $bc[0]>";
// //      $statstring2.= '<td><a href="'.DOL_URL_ROOT.'/fourn/liste.php">'.$langs->trans("Control").'</a></td><td align="right">'.round($third2['CONT']).'</td>';
// //      $statstring2.= "</tr>";

// //      $statstring2 = "<tr $bc[0]>";
// //      $statstring2.= '<td><a href="'.DOL_URL_ROOT.'/fourn/liste.php">'.$langs->trans("Others").'</a></td><td align="right">'.round($third2['OTRO']).'</td>';
// //      $statstring2.= "</tr>";

// //     print $statstring;
// //     print $statstring2;
// // }
// // print '<tr class="liste_total"><td>'.$langs->trans("Total type").'</td><td align="right">';
// // print $total;
// // print '</td></tr>';
// // print '</table>';


// //print '</td><td valign="top" width="70%" class="notopnoleftnoright">';
// print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';

// //estadisticas fin2


// /*
//  * Last modified products
//  */
// $max=15;
// // $sql = "SELECT p.rowid as id, p.ref, p.email, p.detail_problem, p.date_create, p.date_ini, p.date_fin, p.statut ";
// // $sql.= " FROM ".MAIN_DB_PREFIX."m_jobs p ";
// // $sql.= ' WHERE p.entity IN ('.getEntity($product_static->element, 1).')';
// // $sql.= " AND  p.statut > 0 ";
// // if ($user->societe_id)
// //   $sql.= " AND p.fk_soc = ".$user->societe_id;

// // $sql.= " ORDER BY p.tms DESC ";
// // $sql.= $db->plimit($max,0);

// // //print $sql;
// // $result = $db->query($sql);
// if ($result)
//   {
//     $num = $db->num_rows($result);
//     $i = 0;
//     // if ($num > 0)
//     //   {
//     // 	$transRecordedType = $langs->trans("Ticket modified",$max);
//     // 	print '<table class="noborder" width="100%">';
//     // 	$colnb=7;
//     // 	if (empty($conf->global->PRODUIT_MULTIPRICES)) $colnb++;	
//     // 	print '<tr class="liste_titre">';
//     // 	print '<td colspan="'.$colnb.'" align="center">'.$transRecordedType.'</td>';
//     // 	print '</tr>';

//     // 	print "<tr class=\"liste_titre\">";
//     // 	print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","",$sortfield,$sortorder);

//     // 	print_liste_field_titre($langs->trans("Date"),"liste.php", "p.date_create","","","",$sortfield,$sortorder);
//     // 	print_liste_field_titre($langs->trans("Email"),"liste.php", "p.email","","","",$sortfield,$sortorder);
//     // 	print_liste_field_titre($langs->trans("Description"),"liste.php", "p.detail_problem","","","",$sortfield,$sortorder);
//     // 	print_liste_field_titre($langs->trans("Dateini"),"", "","","","");
//     // 	print_liste_field_titre($langs->trans("Datefin"),"", "","","","");
//     // 	print_liste_field_titre($langs->trans("Status"),"liste.php", "p.statut",'','','align="right"',$sortfield,$sortorder);
//     // 	print "</tr>\n";

//     // 	$var=True;
//     // 	while ($i < $num)
//     // 	  {
//     // 	    $objp = $db->fetch_object($result);

//     // 	    print "<tr $bc[$var]>";
//     // 	    print '<td><a href="fiche.php?id='.$objp->id.'">'. $objp->ref.'</a></td>';
	    
//     // 	    print '<td>'.dol_print_date($db->jdate($objp->date_create),'day').'</td>';
//     // 	    print '<td>'.$objp->email.'</td>';
//     // 	    print '<td>'.$objp->detail_problem.'</td>';
//     // 	    print '<td>'.dol_print_date($db->jdate($objp->date_ini),'day').'</td>';
//     // 	    print '<td>'.dol_print_date($db->jdate($objp->date_fin),'day').'</td>';
//     // 	    print '<td align="right">'.$object->LibStatut($objp->statut,6).'</td>';
//     // 	    print '</tr>';

//     // 	    $i++;
//     // 	  }

//     // 	$db->free();
	
//     // 	print "</table>";
//     //   }
//   }
//  else
//    {
//      dol_print_error($db);
//    }

//print '</td></tr></table>';
print '</div></div></div>';

llxFooter();

$db->close();


?>
