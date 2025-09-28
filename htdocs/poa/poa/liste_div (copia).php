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

$langs->load("poa@poa");

if (!$user->rights->poa->poa->leer)
  accessforbidden();

$object = new Poapoa($db);
$objprev = new Poapartidapre($db);

$id = GETPOST('id');
$action = GETPOST('action');
if (isset($_GET['mostrar']))
  $_SESSION['opver'] = '';
if (isset($_GET['opver']))
  $_SESSION['opver'] = $_GET['opver'];
$opver = $_SESSION['opver'];

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
if (! $sortfield) $sortfield="p.ref";
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

if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    $aArrcss= array('poa/css/style.css');
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
    
    //inicio
    print '<div class="master">';
    print '<section id="section-head">';

    //init comandos ocultar mostrar
    // print '<td><input type="button" value="ocultar" onclick="ocultarColumna(0,false)"</td>';
    print '<span>';
    print '<div id="meta" class="left title">&nbsp;</div>';
    if (empty($opver))
      {
      print '<div id="label" class="left title">';
      print '<a href="#" style="cursor:pointer;" onclick="ocultarColumna(2,false)">'.img_picto('','off').'</a>';
      print '&nbsp;';
      print '<a href="#" style="cursor:pointer;" onclick="ocultarColumna(2,true)">'.img_picto('','on').'</a>';

      print '</div>';
      }
    // print '<td><input type="button" onclick="ocultarColumna(2,false)" src="'.img_picto($langs->trans("Ocult"),'refresh.png','','',1).'"></td>';

    // print '<td><input type="image" onclick="ocultarColumna(1,true)" src="'.img_picto($langs->trans("Ocult"),'refresh.png','','',1).'"></td>';
      print '<div id="pseudo" name="div" class="left title">';
      print '<a href="#" style="cursor:pointer;" onclick="ocultarColumna(1,true)">'.img_picto('','off').'</a>';
      print '&nbsp;';
      print '<a href="#" style="cursor:pointer;" onclick="ocultarColumna(1.false)">'.img_picto('','on').'</a>';
      print '</div>';
      print '<div id="partida"  name="div" class="left title">&nbsp;</div>';
      print '<div id="amount"  name="div" class="left title">&nbsp;</div>';

      print '<div id="amount"  name="div" class="left title">&nbsp;</div>';
      print '<div id="amount"  name="div" class="left title">&nbsp;</div>';
      print '<div id="action"  name="div" class="left title">&nbsp;</div>';
    print '<div class="clear"></div>';
    print '</span>';
    //fin comandos ocultar mostrar    
    print '<span>';
    print '<div id="meta" class="left title">'.$langs->trans("Meta").'</div>';
    if (empty($opver))
      print '<div id="label" class="left title">'.$langs->trans("Label").'</div>';
    print '<div id="pseudo" class="left title">'.$langs->trans("Pseudonym").'</div>';
    print '<div id="partida" class="left title">'.$langs->trans("Partida").'</div>';
    print '<div id="amount" class="left title">'.$langs->trans("Amount").'</div>';
    $aMark = array();
    $aMark[date('m')*1] = ' style="background:#989ea5; color:#ffffff;"';
    if ($opver == 1)
      {
	print '<div id="amount" class="left title">'.$langs->trans("Jan").'</div>';
	print '<div id="amount" class="left title">'.$langs->trans("Feb").'</div>';
	print '<div id="amount" class="left title">'.$langs->trans("Mar").'</div>';
	print '<div id="amount" class="left title">'.$langs->trans("Apr").'</div>';
	print '<div id="amount" class="left title">'.$langs->trans("May").'</div>';
	print '<div id="amount" class="left title">'.$langs->trans("Jun").'</div>';
	print '<div id="amount" class="left title">'.$langs->trans("Jul").'</div>';
	print '<div id="amount" class="left title">'.$langs->trans("Aug").'</div>';
	print '<div id="amount" class="left title">'.$langs->trans("Sep").'</div>';
	print '<div id="amount" class="left title">'.$langs->trans("Oct").'</div>';
	print '<div id="amount" class="left title">'.$langs->trans("Nov").'</div>';
	print '<div id="amount" class="left title">'.$langs->trans("Dec").'</div>';
	
      }
    print '<div id="amount" class="left title">'.$langs->trans("Preventive").'</div>';
    print '<div id="amount" class="left title">'.$langs->trans("Balance").'</div>';
    print '<div id="action" class="left title">'.$langs->trans("Action").'</div>';
    print '<div class="clear"></div>';
    // print_liste_field_titre($langs->trans("Sigla"),$_SERVER["PHP_SELF"], "s.sigla","","",'width="10%"');
    // print_liste_field_titre($langs->trans("Label"),$_SERVER["PHP_SELF"], "p.label","","","");
    // print_liste_field_titre($langs->trans("Pseudonym"),$_SERVER["PHP_SELF"], "p.pseudonym","","","");
    // print_liste_field_titre($langs->trans("Partida"),$_SERVER["PHP_SELF"], "p.partida","","","");
    // print_liste_field_titre($langs->trans("Amount"),$_SERVER["PHP_SELF"], "p.amount","","","");

    // print_liste_field_titre($langs->trans("Action"),"", "","","","");
   
    $parameters=array();
    $formconfirm=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
    
    //print '</tr>';
    print '</span>';

    print '<span>';    
    print '<div id="meta" class="left title"><input type="text" class="flat" size="5" name="search_sigla" value="'.$search_sigla.'"></div>';
    if (empty($opver))
      print '<div id="label" class="left title"><input type="text" class="flat" name="search_label" value="'.$search_label.'"></div>';
    
    print '<div id="pseudo" class="left title"><input type="text" class="flat" name="search_pseudonym" value="'.$search_pseudonym.'"></div>';
    
    print '<div id="partida" class="left title">';
    print '<input class="flat" type="text" size="5" name="search_partida" value="'.$search_partida.'">';
    print '</div>';
    
    print '<div id="amount" class="left title">';
    print '<input class="flat" type="text" size="7" name="search_amount" value="'.$search_amount.'">';
    print '</div>';
    if ($opver == 1)
      {
    	print '<div id="amount" class="left title">&nbsp;</div>';
    	print '<div id="amount" class="left title">&nbsp;</div>';
    	print '<div id="amount" class="left title">&nbsp;</div>';
    	print '<div id="amount" class="left title">&nbsp;</div>';
    	print '<div id="amount" class="left title">&nbsp;</div>';
    	print '<div id="amount" class="left title">&nbsp;</div>';
    	print '<div id="amount" class="left title">&nbsp;</div>';
    	print '<div id="amount" class="left title">&nbsp;</div>';
    	print '<div id="amount" class="left title">&nbsp;</div>';
    	print '<div id="amount" class="left title">&nbsp;</div>';
    	print '<div id="amount" class="left title">&nbsp;</div>';
    	print '<div id="amount" class="left title">&nbsp;</div>';
      }
    print '<div id="amount" class="left title">&nbsp;</div>';
    print '<div id="amount" class="left title">&nbsp;</div>';
    print '<div id="amount" class="left title">';
    print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
    print '&nbsp;';
    print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
    print '</div>';
    
    $parameters=array();
    $formconfirm=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
    print '<div class="clear"></div>';
    print '</span>';
    print '</section>';
    print '<section id="section-body">';
    print '<aside id="aside-body">';
    if ($num)
      {
    	$var=True;
    	while ($i < min($num,$limit))
    	  {	      
    	    $obj = $db->fetch_object($result);	
    	    $var=!$var;
	    if ($var) $backg = "pair"; else $backg = "impair";
    	    print "<span>";
    	    print '<div id="meta" class="left '.$backg.'">'.$obj->sigla.'</div>';
    	    if (empty($opver))
    	      print '<div id="label" class="left '.$backg.'">'.(strlen($obj->label)>45?'<a href="#" title="'.$obj->label.'">'.substr($obj->label,0,45).'...</a>':$obj->label).'</div>';
    	    print '<div id="pseudo" class="left '.$backg.'">'.(strlen($obj->pseudonym)>30?'<a href="#" title="'.$obj->pseudonym.'">'.substr($obj->pseudonym,0,30).'...</a>':$obj->pseudonym).'</div>';
    	    print '<div id="partida" class="left '.$backg.'">'.$obj->partida.'</div>';
    	    print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($obj->amount,'MT'),2).'</div>';
    	    if ($opver == 1)
    	      {
    		print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($obj->m_jan,'MT'),2).'</div>';
    		print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($obj->m_feb,'MT'),2).'</div>';
    		print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($obj->m_mar,'MT'),2).'</div>';
    		print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($obj->m_apr,'MT'),2).'</div>';
    		print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($obj->m_may,'MT'),2).'</div>';
    		print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($obj->m_jun,'MT'),2).'</div>';
    		print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($obj->m_jul,'MT'),2).'</div>';
    		print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($obj->m_aug,'MT'),2).'</div>';
    		print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($obj->m_sep,'MT'),2).'</div>';
    		print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($obj->m_oct,'MT'),2).'</div>';
    		print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($obj->m_nov,'MT'),2).'</div>';
    		print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($obj->m_dec,'MT'),2).'</div>';
    	      }

    	    if ($objprev->getsum_str_part($obj->gestion,$obj->fk_structure,$obj->id, $obj->partida))
    	      {
    		$total = $objprev->total;
    		print '<div id="amount" class="left '.$backg.'">'.number_format(price2num($objprev->total,'MT'),2).'</div>';
    	      }
    	    else
    	      {
    		$total = 0;
    		print '<div id="amount" class="left '.$backg.'">&nbsp;</div>';
    	      }
    	    $balance = $obj->amount - $total;

    	    print '<div id="amount" class="left '.$backg.'">'.number_format($balance,2).'</div>';

    	    print '<div id="amount" class="left '.$backg.'"><a href="fiche.php?id='.$obj->id.'">'.img_picto($langs->trans('Edit'),'edit').'</a></div>';
	    print '<div class="clear"></div>';
    	    print '</span>';
    	    $i++;
    	  }
      }
  
    print '</aside>';
    print '</section>';
    print '</div>';
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
