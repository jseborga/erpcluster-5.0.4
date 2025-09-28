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
 *   	\file       /request/card_page.php
 *		\ingroup
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-10-13 18:11
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
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");

// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php');

dol_include_once('/finint/class/requestcashext.class.php');
dol_include_once('/finint/class/requestcashdeplacementext.class.php');


// Load traductions files requiredby by page
$langs->load("finint@finint");
$langs->load("companies");
$langs->load("other");
$langs->load("errors");
$langs->load("admin");
$langs->load("holiday");
$langs->load("bills");
$langs->load('banks');
$langs->load('main');



// Load object if id or ref is provided as parameter
$object 		= new Requestcashext($db);
$deplacement 	= new Requestcashdeplacementext($db);

$morecss=array('/finint/css/style.css','/finint/css/bootstrap.min.css');
$morecss=array('/finint/css/style.css');
$morejs=array("/finint/js/finint.js");
llxHeader('',$langs->trans('Request'),'','','','',$morejs,$morecss,0,0);

$form=new Formv($db);
$formfile=new Formfile($db);

//vamos a actualizar la información de padre e hijo
$aDischarg=array();
$aDischarg_last=array();
$filter = ' AND t.rowid = 1';
$filter='';
$res = $object->fetchAll('ASC','t.rowid',0,0,array(),'AND',$filter);
if ($res >0)
{
	$lines = $object->lines;
	foreach ($lines AS $j => $line)
	{
		$filter = " AND t.fk_request_cash = ".$line->id;
		$filter.= " OR t.fk_request_cash_dest= ".$line->id; 
		$res = $deplacement->fetchAll('ASC','t.date_create, t.fk_request_cash, t.fk_request_cash_dest',0,0,array(),'AND',$filter);
		if ($res>0)
		{
			$linesdet = $deplacement->lines;
			$fk = 0;
			$fk_discharg_last=0;
			$fk_reembolso=0;
			foreach ($linesdet AS $k => $linedet)
			{
				if (empty($fk_reembolso)) $fk_reembolso = $linedet->id;
				else
				{
					if ($fk_reembolso != $linedet->id && $linedet->fk_request_cash_dest>0)
						$fk_reembolso = $linedet->id;
				}
				//$aDischarg[$line->id][$linedet->concept][$linedet->id] = array('fk_request_cash'=>$linedet->fk_request_cash,					'fk_request_cash_dest'=>$linedet->fk_request_cash_dest,'fk_parent_app'=>$linedet->fk_parent_app);
				$aDischarg[$line->id][$linedet->concept][$linedet->fk_parent_app+0][$linedet->id] = $linedet->id;
				if ($fk_discharg_last != $linedet->fk_parent_app)
				{
					$aDischarg_last[$line->id][$fk_reembolso] = $linedet->fk_parent_app;
					$fk_discharg_last = $linedet->fk_parent_app;
				}
				else
				{
					$aDischarg_last[$line->id][$fk_reembolso] = $fk_discharg_last;
				}
				//if (empty($linedet->fk_request_cash) && $linedet->fk_request_cash > 0) $fk = $linedet->id;

			}
		}
	}
}
echo '<pre>discharglast';
print_r($aDischarg_last);
echo '<hr>discharg';
print_r($aDischarg);

//vamos a actualizar la base
$aNewdata = array();
foreach ($aDischarg_last AS $j => $aData)
{
	foreach ($aData AS $k => $l)
	{
		if ($k != $l)
		{
		//vamos a reemplazar $k buscando el valor de $l en $aDischarg
		//echo '<hr>BUSCAMOS '.$j.' deplacement '.$l;
			if (empty($l))
			{
				$aTmp = $aDischarg[$j]['deplacement'][$k];
				if (is_array($aTmp) && count($aTmp)>0)
				{
					foreach ((array) $aTmp AS $m=>$n)
					{
						$aNewdatatmp[$m] = $k;
					}
				}
			}
			else
			{
				$aTmp = $aDischarg[$j]['deplacement'][$l];
				if (is_array($aTmp) && count($aTmp)>0)
				{
					foreach ((array) $aTmp AS $m=>$n)
					{
						$aNewdata[$m] = $k;
					}
				}
			}
		}

	}
}

echo '<hr>resultado';
print_r($aNewdata);
echo '<hr>resultadotmp ';
print_r($aNewdatatmp);
//echo '</pre>';
//exit;
//vamos a actualizar la información
if (is_array($aNewdata) && count($aNewdata)>0 && $abc)
{
	$db->begin();
	foreach ($aNewdata AS $j => $k)
	{
		if (!$error)
		{
			$res = $deplacement->fetch($j);
			if ($res == 1)
			{
				if ($deplacement->concept == 'deplacement')
				{
					$deplacement->fk_parent = $k;
					echo '<hr>'.' '.$j.' => '.$res = $deplacement->update($user);
					if ($res<=0)
					{
						$error++;
						setEventMessages($deplacement->error,$deplacement->errors,'errors');
					}

				}
			}
		}

	}
	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Proceso concluido'),null,'mesgs');
	}
	else
	{
		$db->rollback();
		setEventMessages($langs->trans('Proceso NO concluido'),null,'errors');	
	}
}
if (is_array($aNewdatatmp) && count($aNewdatatmp)>0)
{
	$db->begin();
	foreach ($aNewdatatmp AS $j => $k)
	{
		if (!$error)
		{
			$res = $deplacement->fetch($j);
			if ($res == 1)
			{
				if ($deplacement->concept == 'deplacement')
				{
					if (empty($deplacement->fk_parent))
					{
						$deplacement->fk_parent = $k;
						echo '<hr>'.' '.$j.' => '.$res = $deplacement->update($user);
						if ($res<=0)
						{
							$error++;
							setEventMessages($deplacement->error,$deplacement->errors,'errors');
						}
					}
					else
					{
						echo '<hr>tiene registro '.$j.' con fk_parent '.$deplacement->fk_parent.' intenntado reemplazr '.$k;
					}
				}
			}
		}

	}
	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Proceso concluido'),null,'mesgs');
	}
	else
	{
		$db->rollback();
		setEventMessages($langs->trans('Proceso NO concluido'),null,'errors');	
	}
}

// End of page
llxFooter();
$db->close();
