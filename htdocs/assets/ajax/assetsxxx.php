<?php
/* Copyright (C) 2006      Andre Cianfarani     <acianfa@free.fr>
 * Copyright (C) 2005-2013 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2007-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *       \file       htdocs/product/ajax/products.php
 *       \brief      File to return Ajax response on product list request
 */

if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');
if (empty($_GET['keysearch']) && ! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');

require '../../main.inc.php';

$htmlname=GETPOST('htmlname','alpha');
$socid=GETPOST('socid','int');
$type=GETPOST('type','int');
$mode=GETPOST('mode','int');
$status=((GETPOST('status','int') >= 0) ? GETPOST('status','int') : -1);
$outjson=(GETPOST('outjson','int') ? GETPOST('outjson','int') : 0);
$price_level=GETPOST('price_level','int');
$action=GETPOST('action', 'alpha');
$id=GETPOST('id', 'int');
$price_by_qty_rowid=GETPOST('pbq', 'int');

/*
 * View
 */

//print '<!-- Ajax page called with url '.$_SERVER["PHP_SELF"].'?'.$_SERVER["QUERY_STRING"].' -->'."\n";

dol_syslog(join(',',$_GET));
//print_r($_GET);

if (! empty($action) && $action == 'fetch' && ! empty($id))
{
	require DOL_DOCUMENT_ROOT . '/assets/assets/class/assets.class.php';
	
	$outjson=array();
	
	$object = new Assets($db);
	$ret=$object->fetch($id);
	if ($ret > 0)
	{
		$outref=$object->ref;
		$outlabel=$object->descrip;
		$outdesc=$object->descrip;
		$outtype=$object->type;
		$outqty=1;
		$outdiscount=0;
		
		$found=false;
		
		$outjson = array('ref'=>$outref, 'label'=>$outlabel, 'desc'=>$outdesc, 'type'=>$outtype, 'price_ht'=>$outprice_ht, 'price_ttc'=>$outprice_ttc, 'pricebasetype'=>$outpricebasetype, 'tva_tx'=>$outtva_tx, 'qty'=>$outqty, 'discount'=>$outdiscount);
	}
	
	echo json_encode($outjson);
	
}
else
{
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
	
	$langs->load("assets@assets");
	$langs->load("main");
	
	top_httphead();
	
	if (empty($htmlname)) return;
	
	$match = preg_grep('/('.$htmlname.'[0-9]+)/',array_keys($_GET));
	sort($match);
	$fk_equipment = (! empty($match[0]) ? $match[0] : '');
	
	if (! GETPOST($htmlname) && ! GETPOST($fk_equipment)) return;
	
	 // When used from jQuery, the search term is added as GET param "term".
	$searchkey=(GETPOST($fk_equipment)?GETPOST($fk_equipment):(GETPOST($htmlname)?GETPOST($htmlname):''));
	
	$form = new Formv($db);
	$arrayresult=$form->select_assets_list($selected,$htmlname,$type,$limit,$price_level,'',$status,$finished,0,$socid,$filter);
	
	$db->close();
	if ($outjson) print json_encode($arrayresult);
}

?>
