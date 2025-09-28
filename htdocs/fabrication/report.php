<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2015-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/fabrication/report.php
 *      \ingroup    Fabrication
 *      \brief      Page report fabrication date ini date fin,
 */

require("../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabrication.class.php");
require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabricationdet.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/mouvementstock.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabrication.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelation.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/core/modules/almacen/modules_almacen.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/lib/almacen.lib.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/stock.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

$langs->load("stocks");
$langs->load("almacen@almacen");

if (!$user->rights->almacen->leerinventario)
  accessforbidden();

$id = GETPOST('id','int');
$yesno = GETPOST('yesno');
$zeroyesno = GETPOST('zeroyesno');
$action = GETPOST('action');
if (empty($action))
  $action= 'edit';
if (empty($yesno))
  $yesno = $_SESSION['selyesno'];
if (empty($yesno))
  $yesno = 2;
if (empty($zeroyesno))
  $zeroyesno = $_SESSION['selzeroyesno'];
if (empty($zeroyesno))
  $zeroyesno = 2;
if (empty($id))
  $id = $_SESSION['idEntrepot'];
$dateini = dol_now();
$datefin = dol_now();
if ($action == 'edit')
  {
    $dateini  = dol_mktime(12, 0, 0, GETPOST('dimonth'),  GETPOST('diday'),  GETPOST('diyear'));
    $datefin  = dol_mktime(23, 59, 59, GETPOST('dfmonth'),  GETPOST('dfday'),  GETPOST('dfyear'));

  }
$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="f.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$object  = new Fabrication($db);
$objectd = new Fabricationdet($db);

//$object = new Entrepotrelation($db);
$objecten = new Entrepot($db);

$form = new Form($db);
$formfile = new Formfile($db);
$movement=new MouvementStock($db);
//$objinv = new Inventario($db);

$hookmanager->initHooks(array('almacen'));
//print_r($hookmanager);
/*
Actions
*/
if ($action == 'builddoc')	// En get ou en post
  {
    // $object->fetch($id);
    // if (empty($object->id))
    //   $object->id = $id;
    // $object->fetch_thirdparty();
    // $object->fetch_lines();
    // if (GETPOST('model'))
    //   {
    //     $object->setDocModel($user, GETPOST('model'));
    //   }
    
    // Define output language
    $outputlangs = $langs;
    $newlang='';
    if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
    if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
    if (! empty($newlang))
      {
        $outputlangs = new Translate("",$conf);
        $outputlangs->setDefaultLang($newlang);
      }
    $result=almacen_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
    if ($result <= 0)
      {
        dol_print_error($db,$result);
        exit;
      }
    else
      {
        header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id.'&action=edit');
        exit;
      }
  }


$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
llxHeader("",$langs->trans("Manufacturingreport"),$help_url);

print_fiche_titre($langs->trans("Manufacturingreport"));

print '<div>';
print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';

print '<table class="border" width="100%">';
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="3">';
$form->select_date($dateini,'di','','','',"crea_commande",1,1);

print '</td></tr>';

// hasta fecha
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="3">';
$form->select_date($datefin,'df','','','',"crea_commande",1,1);

print '</td></tr>';

// detallado Si / resumido NO
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Detailed').'</td><td colspan="3">';
print select_yesno($yesno,'yesno','',0,0);
print '</td></tr>';
// // mostrar 
// print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Showzerobalances').'</td><td colspan="3">';
// print select_yesno($zeroyesno,'zeroyesno','',0,0);
// print '</td></tr>';
print '</table>';
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';
print '</div>';
print '<br>';
if ($action == 'edit')
  {
    $_SESSION['idEntrepot'] = $id;
    $_SESSION['selyesno'] = $yesno;
    $_SESSION['selzeroyesno'] = $zeroyesno;
    $aRowid = array();

    //movimiento de salidas y entradas
    // if ($yesno == 1)
    //   $object->fetch_lines();

    //listamos todos los productos
    //movimiento del producto
    $sql  = "SELECT f.rowid, f.ref, f.description, f.date_creation, f.date_delivery, f.date_init, f.date_finish ";
    if ($yesno == 1)
      {
	$sql.= " , d.qty, d.qty_decrease, d.qty_first, d.qty_second, d.price, d.price_total, ";
	$sql.= " p.ref AS ref_product, p.label AS label_product";
      }
    $sql.= " FROM ".MAIN_DB_PREFIX."fabrication AS f";
    if ($yesno == 1)
      {
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."fabricationdet AS d ON d.fk_fabrication = f.rowid";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON d.fk_product = p.rowid ";
      }
    $sql.= " WHERE ";
    $sql.= " f.entity = ".$conf->entity;
    $sql.= " ORDER BY $sortfield $sortorder";
    //$sql.= $db->plimit($limit+1, $offset);

    $result = $db->query($sql);
    if ($result)
      {
    	$num = $db->num_rows($result);

	// print_barre_liste($langs->trans("Currentbalances"), $page, "inventario.php", "", $sortfield, $sortorder,'',$num);
	print '<div>';
	print '<table class="noborder" width="100%">';
	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"inventario.php", "p.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Dateini"),"inventario.php", "p.label","","",'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Datedelivery"),"inventario.php", "","","",'align="right"');
	print_liste_field_titre($langs->trans("Datefin"),"inventario.php", "","","",'align="right"');
	if ($yesno == 1)
	  {
	    print_liste_field_titre($langs->trans("Product"),"inventario.php", "","","",'align="left"');
	    print_liste_field_titre($langs->trans("Label"),"inventario.php", "","","",'align="left"');
	    print_liste_field_titre($langs->trans("Qty"),"inventario.php", "","","",'align="right"');
	    print_liste_field_titre($langs->trans("Qtydecrease"),"inventario.php", "","","",'align="right"');
	    print_liste_field_titre($langs->trans("Qtyfirst"),"inventario.php", "","","",'align="right"');
	    print_liste_field_titre($langs->trans("Qtysecond"),"inventario.php", "","","",'align="right"');
	    print_liste_field_titre($langs->trans("Price"),"inventario.php", "","","",'align="right"');
	  }
	print_liste_field_titre($langs->trans("Pricetotal"),"inventario.php", "","","",'align="right"');	
	print "</tr>\n";

    	$i = 0;
	while ( $i < $num)
	  {
	    $print = true;
	    //recorriendo los productos
	    $obj = $db->fetch_object($result);
	    //obtemenos la lista de items y el total
	    $objectd->getlist($obj->rowid);
	    //echo '<hr>'.$obj->rowid.' | '.$obj->produit.' | ';
	    //$objProduct = new Product($db);
	    //$objProduct->fetch($obj->rowid);
	    //leemos saldos
	    //$objProduct->load_stock();
	    //sumamos saldos del almacen seleccionado
	    // $saldoStock = 0;
	    // foreach ($aEntrepot AS $idEntrepot)
	    //   {
	    // 	$saldoStock += price2num($objProduct->stock_warehouse[$idEntrepot]->real,'MT');
	    //   }

	    //obtenemos todo el movimiento
	    //include DOL_DOCUMENT_ROOT.'/almacen/inventario/includes/mouvement.php';
	    //fin obtener movimiento
 
	    if ($yesno == 1)
	      {
		$saldoMov = $object->linesprod[$obj->rowid]->saldo;
	      }
	    // if ($saldoStock == 0 && $saldoMov == 0)
	    //   if ($zeroyesno == 2)
	    // 	$print = false;
	    if ($print)
	      {
		//imprimimos
		$var=!$var;
		print "<tr $bc[$var]>";
		print '<td widht="3%">'.$obj->ref.'</td>';
		print '<td widht="5%" align="right">'.dol_print_date($db->jdate($obj->date_init),'day').'</td>';
		print '<td widht="5%" align="right">'.dol_print_date($db->jdate($obj->date_delivery),'day').'</td>';
		print '<td widht="5%" align="right">'.dol_print_date($db->jdate($obj->date_finish),'day').'</td>';
		if ($yesno == 1)
		  {
		    print '<td align="left" widht="25%">'.$obj->ref_product.'</td>';
		    print '<td align="left" widht="25%">'.$obj->label_product.'</td>';
		    print '<td align="right" widht="5%">'.$obj->qty.'</td>';
		    print '<td align="right" widht="5%">'.$obj->qty_decrease.'</td>';
		    print '<td align="right" widht="5%">'.$obj->qty_first.'</td>';
		    print '<td align="right" widht="5%">'.$obj->qty_second.'</td>';
		    print '<td align="right" widht="5%">'.$obj->price.'</td>';
		    print '<td align="right" widht="5%">'.price(price2num($obj->qty_first * $obj->price,'MT')).'</td>';

		  }
		else
		  print '<td align="right" widht="5%">'.price(price2num($objectd->total,'MT')).'</td>';
		print "</tr>\n";
	      }
	    $i++;
	  }
	//      }
    
    // //revisando si el local es padre de otros almacenes
    // //si es afirmativo se debe sumar todas las cantidades de sus hijos incluso del padre
    // $sql  = "SELECT er.rowid,er.rowid AS id ";
    // $sql.= " FROM ".MAIN_DB_PREFIX."entrepot_relation AS er";
    // $sql.= " WHERE er.fk_entrepot_father = ".$id;
    // $sql.= " GROUP BY er.rowid ";
    // $result = $db->query($sql);
    // $ids = '';
    // $filtroEntrepot = " sm.fk_entrepot = ".$id;
    // if ($result)
    //   {
    // 	$num = $db->num_rows($result);
    // 	$i = 0;
    // 	$aRowid[$id] = $id;
    // 	while ($i < min($num,100))
    // 	  {
    // 	    $objp = $db->fetch_object($result);
    // 	    $aRowid[$obj->rowid] = $obj->rowid;
    // 	    $i++;
    // 	  }
    // 	$ids = implode(',',$aRowid);
    // 	$filtroEntrepot = " sm.fk_entrepot IN(".$ids.")";
    //   }
    // //movimiento del producto
    // $sql  = "SELECT p.rowid, p.ref, p.label, SUM(sm.value) AS saldo, SUM(sm.value*sm.price) AS total ";
    // $sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement AS sm";
    // $sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p";
    // $sql.= " ON sm.fk_product = p.rowid ";
    // $sql.= " WHERE ";
    // $sql.= $filtroEntrepot;
    // $sql.= " GROUP BY p.rowid, p.ref ";
    // $sql.= " ORDER BY $sortfield $sortorder";
    // $sql.= $db->plimit($limit+1, $offset);

    // $result = $db->query($sql);
    // if ($result)
    //   {
    // 	$num = $db->num_rows($result);
    // 	$i = 0;





    // $nblignes = count($object->lines);
    // if ($nblignes > 0)
    //   {
      
    // 	print_barre_liste($langs->trans("Currentbalances"), $page, "inventario.php", "", $sortfield, $sortorder,'',$num);

    // 	print '<table class="noborder" width="100%">';
    // 	print "<tr class=\"liste_titre\">";
    // 	print_liste_field_titre($langs->trans("Ref"),"kardex.php", "sm.datem","","","",$sortfield,$sortorder);
    // 	print_liste_field_titre($langs->trans("Label"),"kardex.php", "sm.value","","",'',$sortfield,$sortorder);
    // 	print_liste_field_titre($langs->trans("Quantity"),"kardex.php", "sm.value","","",'align="right"',$sortfield,$sortorder);
    // 	print_liste_field_titre($langs->trans("Value"),"kardex.php", "sm.value","","",'align="right"',$sortfield,$sortorder);
    // 	print "</tr>\n";
    // 	$i = 0;
    // 	$var=True;
    // 	$balanceSum = 0;

    // 	for ($i = 0; $i < $nblignes; $i++)
    // 	  {
    // 	      //actualizando totales
    // 	      //$balanceMount += $obj->total;

    // 	      // $var=!$var;
    // 	      // print "<tr $bc[$var]>";
    // 	      // print '<td widht="10%">'.$obj->ref.'</td>';
    // 	      // print '<td widht="60%">'.$obj->label.'</td>';
    // 	      // print '<td widht="10%" align="right">'.price($obj->saldo).'</td>';
    // 	      // print '<td widht="10%" align="right">'.price($obj->total).'</td>';
    // 	      // print "</tr>\n";
	      
    // 	    $balanceMount += $object->lines[$i]->total;

    // 	    $var=!$var;
    // 	    print "<tr $bc[$var]>";
    // 	    print '<td widht="10%">'.$object->lines[$i]->ref.'</td>';
    // 	    print '<td widht="60%">'.$object->lines[$i]->label.'</td>';
    // 	    print '<td widht="10%" align="right">'.price(price2num($object->lines[$i]->saldo,'MT')).'</td>';
    // 	    print '<td widht="10%" align="right">'.price(price2num($object->lines[$i]->total,'MT')).'</td>';
    // 	    print "</tr>\n";
    // 	  }
	print "</table>";
	print '</div>';
	// print '<div class="tabsAction">';
	// //documents
	//     print '<table width="100%"><tr><td width="50%" valign="top">';
	//     print '<a name="builddoc"></a>'; // ancre
	//     $objecten->fetch($id);
	//     /*
	//      * Documents generes
	//      */
	//     $filename=dol_sanitizeFileName($objecten->libelle);
	//     //cambiando de nombre al reporte
	//     $filedir=$conf->almacen->dir_output . '/' . dol_sanitizeFileName($objecten->libelle);
	//     $urlsource=$_SERVER['PHP_SELF'].'?id='.$id;
	//     $genallowed=$user->rights->almacen->crearpedido;
	//     $delallowed=$user->rights->almacen->delpedido;
	//     print '<br>';
	//     print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
	//     $somethingshown=$formfile->numoffiles;
	//     print '</td></tr></table>';
	    
	// print "</div>";
	
      }
    // else
    //   {
    // 	dol_print_error($db);
    //   }
  }

$db->close();

llxFooter();
?>
