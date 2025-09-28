<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       assets/assetsmov_list.php
 *		\ingroup    assets
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-01-25 09:23
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)

// Load traductions files requiredby by page
$langs->load("assets");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('assetsmovlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('assets');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/


/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();

$form=new Form($db);

// Put here content of your page


if ($resm>0)
{


	print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';

    // Fields title
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Coste'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Residual'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Periodo depr.'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Saldo periodo.'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Actualización del Costo'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Valor Actualizado'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Depr. del periodo'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Depr. acumulada'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Act. depr.acumulada'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Valor Actualizado Depr.'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Valor Neto'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);

	print '</tr>'."\n";


	$i=0;
	$var=true;
	$totalarray=array();
	$consumed = 0;
	foreach ($objmov->lines AS $i => $obj)
	{
		if ($obj)
		{
			if ($obj->movement_type == 'DEPR')
			{
				$consumed+=$obj->time_consumed;
			}
			else
			{
				$consumed = $obj->time_consumed;
			}
			if ($obj->movement_type == 'DEPR')
			{
			$var = !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$obj->ref.'</td>';
			print '<td align="right">'.price($obj->coste).'</td>';
			print '<td align="right">'.price($obj->coste_residual).'</td>';
			print '<td align="right">'.price($consumed).'</td>';
			print '<td align="right">'.price($obj->month_depr-$consumed).'</td>';
			print '<td align="right">'.price($obj->amount_update).'</td>';
			print '<td align="right">'.price($obj->amount_balance).'</td>';
			print '<td align="right">'.price($obj->amount_depr).'</td>';
			print '<td align="right">'.price($obj->amount_depr_acum).'</td>';
			print '<td align="right">'.price($obj->amount_depr_acum_update).'</td>';
			print '<td align="right">'.price($obj->amount_balance_depr).'</td>';
			print '<td align="right">'.price($obj->amount_balance-$obj->amount_balance_depr).'</td>';
			//print '<td align="right">'.price($obj->amount_balance-$obj->amount_balance_depr+$obj->coste_residual).'</td>';
			print '</tr>';
			}
		}
		$i++;
	}
}
