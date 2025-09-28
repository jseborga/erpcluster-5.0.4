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
 *   	\file       assets/contabperiodo_card.php
 *		\ingroup    assets
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-01-24 17:17
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
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php');
dol_include_once('/assets/class/assetsext.class.php');
dol_include_once('/assets/class/assetsmovext.class.php');
dol_include_once('/assets/class/assetsmovlogext.class.php');
dol_include_once('/assets/class/assetsbalanceext.class.php');
dol_include_once('/assets/class/contabperiodoext.class.php');
dol_include_once('/assets/lib/assets.lib.php');
if ($conf->multicurren->enabled)
	dol_include_once('/multicurren/class/cscurrencytypeext.class.php');
else
	setEventMessages($langs->trans('Habilite el modulo multicurren'),null,'errors');

// Load traductions files requiredby by page
$langs->load("assets");
$langs->load("multicurren");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$month = GETPOST('period_month','int');
$year = GETPOST('period_year','int');
$ref = $year.(strlen($month)==1?'0'.$month:$month);
$type_group = GETPOST('type_group');
$country = GETPOST('country');

$search_entity=GETPOST('search_entity','int');
$search_period_month=GETPOST('search_period_month','int');
$search_period_year=GETPOST('search_period_year','int');
$search_statut=GETPOST('search_statut','int');
$search_status_af=GETPOST('search_status_af','int');

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Assetsmovext($db);
$asset = new Assetsext($db);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('contabperiodo'));
$extrafields = new ExtraFields($db);

if (empty($_SESSION['period_year'])) $_SESSION['period_year'] = date('Y');
$period_year = $_SESSION['period_year'];

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	// Action to validate
	if (($action == 'confirm_validate' && $_REQUEST['confirm'] == 'yes' && $user->rights->assets->dep->val) ||
		($action == 'confirm_approve' && $_REQUEST['confirm'] == 'yes' && $user->rights->assets->dep->apr))
	{
		$ref = GETPOST('ref');
		$aDepr = unserialize($_SESSION['depr']);
		$month = $aDepr[$ref]['month'];
		$year = $aDepr[$ref]['year'];
		$type_group = $aDepr[$ref]['type_group'];
		$country = $aDepr[$ref]['country'];
		if ($action == 'confirm_validate') $status = 1;
		if ($action == 'confirm_approve') $status = 2;
		$objmov = new Assetsmovext($db);
		$filterstatic = " AND t.ref = ".$ref;
		$filterstatic.= " AND t.entity = ".$conf->entity;
		$filterstatic.= " AND t.movement_type = 'DEPR'";
		$resm = $objmov->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,false);
		if ($resm >0)
		{
			$db->begin();
			$lines = $objmov->lines;
			$new = dol_now();
			foreach ($lines AS $j => $line)
			{
				$res = $objmov->fetch($line->id);
				if ($res>0)
				{
					$objmov->status = $status;
					$resm = $objmov->update($user);
					if ($resm <=0)
					{
						$error++;
						setEventMessages($objmov->error,$objmov->errors,'errors');
					}
					if ($status == 2 && $action == 'confirm_approve')
					{
						$balance = new Assetsbalanceext($db);
						//buscamos en la tabla resumen
						$resb = $balance->fetch(0,$line->fk_asset);
						if ($resb > 0)
						{
							//actualizamos
							$balance->ref = $line->ref;
							$balance->type_group = $line->type_group;
							$balance->date_ini = $line->date_ini;
							$balance->date_end = $line->date_end;
							$balance->factor_update = $line->factor_update;
							$balance->time_consumed = $line->time_consumed;
							$balance->tcini = $line->tcini+0;
							$balance->tcend = $line->tcend+0;
							$balance->month_depr = $line->month_depr+0;
							$balance->coste = $line->coste;
							$balance->coste_residual = $line->coste_residual;
							$balance->amount_base = $line->amount_base;
							$balance->amount_update = $line->amount_update;
							$balance->amount_depr = $line->amount_depr;
							$balance->amount_depr_acum = $line->amount_depr_acum;
							$balance->amount_depr_acum_update = $line->amount_depr_acum_update;
							$balance->amount_balance_depr = $line->amount_balance_depr;
							$balance->amount_balance = $line->amount_balance;
							$balance->movement_type = $line->movement_type;
							$balance->fk_user_mod = $user->id;
							$balance->dateu = $new;
							$balance->tms = $new;
							$balance->status = 1;
							$resm = $balance->update($user);
							if ($resm <=0)
							{
								$error++;
								setEventMessages($balance->error,$balance->errors,'errors');
							}

						}
						else
						{
							//insertamos en la tabla resumen
							$balance->fk_asset = $line->fk_asset;
							$balance->ref = $line->ref;
							$balance->type_group = $line->type_group;
							$balance->date_ini = $line->date_ini;
							$balance->date_end = $line->date_end;
							$balance->factor_update = $line->factor_update;
							$balance->time_consumed = $line->time_consumed;
							$balance->tcini = $line->tcini+0;
							$balance->tcend = $line->tcend+0;
							$balance->month_depr = $line->month_depr+0;
							$balance->coste = $line->coste;
							$balance->coste_residual = $line->coste_residual;
							$balance->amount_base = $line->amount_base;
							$balance->amount_update = $line->amount_update;
							$balance->amount_depr = $line->amount_depr;
							$balance->amount_depr_acum = $line->amount_depr_acum;
							$balance->amount_depr_acum_update = $line->amount_depr_acum_update;
							$balance->amount_balance_depr = $line->amount_balance_depr;
							$balance->amount_balance = $line->amount_balance;
							$balance->movement_type = $line->movement_type;
							$balance->fk_user_create = $user->id;
							$balance->fk_user_mod = $user->id;
							$balance->datec = $new;
							$balance->dateu = $new;
							$balance->tms = $new;
							$balance->status = 1;
							$resm = $balance->create($user);
							if ($resm <=0)
							{
								$error++;
								setEventMessages($balance->error,$balance->errors,'errors');
							}
						}
					}
				}
			}
			if (!$error && $status == 2)
			{
				//bloqueamos el periodo
				$period = new Contabperiodo($db);
				$resp = $period->fetch(0,$month,$year);
				if ($resp>0 && $period->period_month == $month && $period->period_year == $year)
				{
					$period->status_af = 0;
					$resp = $period->update($user);
					if ($resp <=0)
					{
						$error++;
						setEventMessages($period->error,$period->errors,'errors');
					}
				}
			}
			if (!$error)
			{
				$db->commit();
				if ($status == 1)
					setEventMessages($langs->trans('Satisfactory validation'),null,'mesgs');
				if ($status == 2)
					setEventMessages($langs->trans('Satisfactory approval'),null,'mesgs');

				header('Location: '.$_SERVER['PHP_SELF'].'?ref='.$ref.'&action=list');
				exit;
			}
			else
			{
				$db->rollback();
			}
		}
	}

	//Action to process
	if ($action == 'process' && GETPOST('cancel'))
	{
		header('Location: '.DOL_URL_ROOT.'/assets/index.php');
		exit;
	}
	if ($action == 'process' && isset($_REQUEST['read']))
	{
		$period = new Contabperiodo($db);
		$resp = $period->fetch(0,$month,$year);
		if ($resp && $period->status_af)
		{
			$error++;
			setEventMessages($langs->trans('Periodo ya procesado y aprobado, verifique'),null,'errors');
			$action = 'create';
		}
		if (!$error)
		{
			$aRef = array($ref =>array('month'=>$month,'year'=>$year,'type_group'=>$type_group,'country'=>$country));
			$_SESSION['depr'] = serialize($aRef);
			header('Location: '.$_SERVER['PHP_SELF'].'?ref='.$ref.'&action=list');
			exit;
		}
		$action = 'create';
	}

	if ($action == 'process' && $_REQUEST['add'])
	{
		$period = new Contabperiodo($db);
		$resp = $period->fetch(0,$month,$year);
		if ($resp && !$period->status_af)
		{
			$error++;
			setEventMessages($langs->trans('Periodo ya procesado y aprobado, verifique'),null,'errors');
			$action = 'create';
		}
		if (!$error)
		{
			//echo '<hr>'.$month.' '.$year,' '.$country.' '.$type_group;
			$res = $object->process_depr($month,$year,$country,$type_group);
			//registramos en el log cada uno de los procesos ejecutados
			if ($res > 0)
			{
				$db->begin();
				//creamos en contabperiodo
				$resp = $period->fetch(0,$month,$year);

				if (!$resp)
				{
					$period->entity = $conf->entity;
					$period->period_month = $month;
					$period->period_year = $year;
					$period->date_ini = dol_get_first_day($year,$month);
					$period->date_fin = dol_get_last_day($year,$month);
					$period->statut = 1;
					$period->status_af = 1;
					$period->status_al = 1;
					$resp = $period->create($user);
					if ($resp <=0)
					{
						$error++;
						setEventMessages($period->error,$period->errors,'errors');
					}
				}
				if (!$error && $resp>0)
				{
					$array = $object->array;
					$objmovlog = new Assetsmovlogext($db);
					$objmov = new Assetsmovext($db);
					$new = dol_now();
					foreach ($array AS $fk_asset => $data)
					{
						$objmovlog->fk_asset = $fk_asset;
						$objmovlog->entity = $conf->entity;
						$objmovlog->ref = $ref;
						$objmovlog->type_group = $data['type_group'];
						$objmovlog->date_ini = $data['date_active'];
						$objmovlog->date_end = $data['date_end'];
						$objmovlog->factor_update = $data['factor_update'];
						$objmovlog->time_consumed = $data['time_consumed'];
						$objmovlog->tcini = $data['tcini']+0;
						$objmovlog->tcend = $data['tcend']+0;
						$objmovlog->month_depr = $data['month_depr']+0;
						$objmovlog->coste = $data['coste'];
						$objmovlog->coste_residual = $data['coste_residual'];
						$objmovlog->amount_base = $data['amount_base']+0;
						$objmovlog->amount_update = $data['amount_update'];
						$objmovlog->amount_depr = $data['amount_depr'];
						$objmovlog->amount_depr_acum = $data['amount_depr_acum_ant'];
						$objmovlog->amount_depr_acum_update = $data['amount_depr_acum_update'];
						$objmovlog->amount_balance_depr = $data['amount_balance_depr'];
						$objmovlog->amount_balance = $data['amount_balance'];
						$objmovlog->movement_type = $data['movement_type'];
						$objmovlog->fk_user_create = $user->id;
						$objmovlog->fk_user_mod = $user->id;
						$objmovlog->datec = $new;
						$objmovlog->dateu = $new;
						$objmovlog->tms = $new;
						$objmovlog->status = 1;
						$resml = $objmovlog->create($user);
						if ($resml <=0)
						{
							$error++;
							setEventMessages($objmovlog->error,$objmovlog->errors,'errors');
						}
						if (!$error)
						{
							//misma informacion en la tabla assetsmov de forma unica
							//buscamos el registro
							$filtermov = " AND t.fk_asset = ".$fk_asset;
							$filtermov.= " AND t.ref = '".$ref."'";
							$filtermov.= " AND t.movement_type = 'DEPR'";
							$resm = $objmov->fetchAll('','',0,0,array(1=>1),'AND',$filtermov,true);
							if ($resm == 1)
							{
								//actualizamos
								$objmov->entity = $conf->entity;
								$objmov->type_group = $data['type_group'];
								$objmov->date_ini = $data['date_active'];
								$objmov->date_end = $data['date_end'];
								$objmov->factor_update = $data['factor_update'];
								$objmov->time_consumed = $data['time_consumed'];
								$objmov->tcini = $data['tcini']+0;
								$objmov->tcend = $data['tcend']+0;
								$objmov->month_depr = $data['month_depr']+0;
								$objmov->coste = $data['coste'];
								$objmov->coste_residual = $data['coste_residual'];
								$objmov->amount_base = $data['amount_base']+0;
								$objmov->amount_update = $data['amount_update'];
								$objmov->amount_depr = $data['amount_depr'];
								$objmov->amount_depr_acum = $data['amount_depr_acum_ant'];
								$objmov->amount_depr_acum_update = $data['amount_depr_acum_update'];
								$objmov->amount_balance_depr = $data['amount_balance_depr'];
								$objmov->amount_balance = $data['amount_balance'];
								$objmov->movement_type = $data['movement_type'];
								$objmov->fk_user_mod = $user->id;
								$objmov->dateu = $new;
								$objmov->tms = $new;
								$objmov->status = 0;
								$resm = $objmov->update($user);
								if ($resm <=0)
								{
									$error++;
									setEventMessages($objmov->error,$objmov->errors,'errors');
								}
							}
							elseif($resm == 0)
							{
								//insertamos
								$objmov->fk_asset = $fk_asset;
								$objmov->entity = $conf->entity;
								$objmov->ref = $ref;
								$objmov->type_group = $data['type_group'];
								$objmov->date_ini = $data['date_active'];
								$objmov->date_end = $data['date_end'];
								$objmov->factor_update = $data['factor_update'];
								$objmov->time_consumed = $data['time_consumed'];
								$objmov->tcini = $data['tcini']+0;
								$objmov->tcend = $data['tcend']+0;
								$objmov->month_depr = $data['month_depr']+0;
								$objmov->coste = $data['coste'];
								$objmov->coste_residual = $data['coste_residual'];
								$objmov->amount_base = $data['amount_base']+0;
								$objmov->amount_update = $data['amount_update'];
								$objmov->amount_depr = $data['amount_depr'];
								$objmov->amount_depr_acum = $data['amount_depr_acum_ant'];
								$objmov->amount_depr_acum_update = $data['amount_depr_acum_update'];
								$objmov->amount_balance_depr = $data['amount_balance_depr'];
								$objmov->amount_balance = $data['amount_balance'];
								$objmov->movement_type = $data['movement_type'];
								$objmov->fk_user_create = $user->id;
								$objmov->fk_user_mod = $user->id;
								$objmov->datec = $new;
								$objmov->dateu = $new;
								$objmov->tms = $new;
								$objmov->status = 0;
								$resm = $objmov->create($user);
								if ($resm <=0)
								{
									$error++;
									setEventMessages($objmov->error,$objmov->errors,'errors');
								}
							}
							else
							{
								$error++;
								setEventMessages($objmov->error,$objmov->errors,'errors');
							}
						}
					}
				}
				else
				{
					$error++;
					setEventMessages($period->error,$period->errors,'errors');
				}
				if (!$error)
				{
					$aRef = array($ref =>array('month'=>$month,'year'=>$year,'type_group'=>$type_group,'country'=>$country));
					$_SESSION['depr'] = serialize($aRef);
					$db->commit();
					setEventMessages($langs->trans('Successfullprocess'),null,'mesgs');
					header('Location: '.$_SERVER['PHP_SELF'].'?ref='.$ref.'&action=list');
					exit;
				}
				else
				{
					$db->rollback();
					$action = '';
				}
			}
			else
			{

				setEventMessages($object->error,$object->errors,'errors');
			}
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Depreciationassets'),'');

$aYear = array();
$aDate = dol_getdate(dol_now());
$month = $aDate['mon'];
$year = $aDate['year']-5;
for($i = $year; $i <= $aDate['year']; $i++)
	$aYear[$i] = $i;

$form=new Form($db);
$formother = new FormOther($db);

// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


// Part to create
if ($action == 'create')
{
	//verificamos el ultimo periodo cerrado

	//verifiamos los periodos abiertos
	$objperiod = new Contabperiodoext($db);

	$filterperiod = "";
	$period_month = 0;
	$period_year = $_SESSION['period_year'];
	$lPeriod = false;
	//$filterperiod.=" AND t.period_year = ".$period_year;
	$resper = $objperiod->fetchAll('ASC', $sortfield='t.period_year,t.period_month', 0,0,array('entity'=>$conf->entity,'status_af'=>1,'statut'=>1),'AND',$filterperiod);
	if ($resper==0)
	{
		$lPeriod = true;
	}
	elseif ($resper>0)
	{
		foreach ($objperiod->lines AS $j => $line)
		{
			if (empty($period_month))
			{
				$period_month = $line->period_month;
				//if ($line->period_month == 12)
				//{
				//	$period_month = 1;
				//	$period_year = $line->period_year+1;
				//}
				//else
				$period_year = $line->period_year;
			}
		}
	}
	else
	{
		setEventMessages($objperiod->error,$objperiod->errors,'errors');
	}
	//$objperiod->fetchAll('ASC', $sortfield='t.period_month', 0,0,array('entity'=>$conf->entity,'status_af'=>1,'statut'=>1),'AND',$filterperiod);
	$aMonth = monthArray($langs,0);

	print load_fiche_titre($langs->trans("Newdepreciationprocess"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="process">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldperiod_month").'</td><td>';

	if (!$lPeriod)
	{
		print $aMonth[$period_month];
		print '<input type="hidden" name="period_month" value="'.$period_month.'">';
	}
	else
		print $formother->select_month((GETPOST('period_month')?GETPOST('period_month'):$month),'period_month',1,1);
	//print '<select name="period_month">'.$optionmonth.'</select>';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldperiod_year").'</td><td>';
	//print $form->selectarray('period_year',$aYear,(GETPOST('period_year')?GETPOST('period_year'):$aDate['year']));
	if (!$lPeriod)
	{
		if ($period_year == $_SESSION['period_year'])
		{
			print $period_year;
			print '<input type="hidden" name="period_year" value="'.$period_year.'">';
		}
		else
		{
			setEventMessages($langs->trans('Changemanagementtoprocess'),null,'warnings');
			print $period_year;
		}
	}
	else
		print $form->selectarray('period_year',$aYear,(GETPOST('period_year')?GETPOST('period_year'):$aDate['year']));
	//print '<select name="period_year">'.$optionyear.'</select>';
	print '</td></tr>';
	print '<tr><td >'.$langs->trans("Fieldassetgroup").'</td><td>';
	print select_type_group((GETPOST('type_group')?GETPOST('type_group'):''),'type_group','',1,0,'code');
	print '</td></tr>';
	if ($conf->multicurren->enabled)
	{
		$cscurrency = new Cscurrencytypeext($db);
		$filterstatic = " AND t.entity = ".$conf->entity;
		$filterstatic.= " AND t.status = 1";
		$resc = $cscurrency->fetchAll('ASC', 'label', 0,0,array(1=>1),'AND',$filterstatic);
		if ($resc>0)
		{
			foreach ($cscurrency->lines AS $j => $line)
			{
				$aCurrency[$line->ref] = $line->label;
			}
			print '<tr><td class="fieldrequired">'.$langs->trans("Exchangerate").'</td><td>';
			print $form->selectarray('country',$aCurrency,GETPOST('country'));
			print '</td></tr>';
		}
	}
	print '</table>'."\n";

	dol_fiche_end();

	if ($period_year == $_SESSION['period_year'])
	{
		print '<div class="center">';
		print '<input type="submit" class="button" name="read" value="'.$langs->trans("Recover").'">';
		print '<input type="submit" class="button" name="add" value="'.$langs->trans("Process").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
		print '</div>';
	}

	print '</form>';
}

if ($action == 'list' || $action == 'validate' || $action == 'approve')
{
	//verificamos si esta la totalidad de activos
	$assets = new Assetsext($db);
	//variables construidas
	$ref = GETPOST('ref');
	$aDepr = unserialize($_SESSION['depr']);
	$month = $aDepr[$ref]['month'];
	$year = $aDepr[$ref]['year'];
	$type_group = $aDepr[$ref]['type_group'];
	$country = $aDepr[$ref]['country'];


	$date_end = dol_get_last_day($year,$month);
	//echo '<hr>inicia '.dol_print_date($date_end,'day');
	$filterstatic = '';
	$filterstatic.= " AND t.entity = ".$conf->entity;
	$filterstatic.= " AND t.statut >= 1";
	$filterstatic.= " AND t.date_active >0";
	$filterstatic.= " AND t.date_active <=".$db->idate($date_end);
	$nb = $assets->fetchAll('ASC', 'rowid', 0, 0, array(1=>1), 'AND', $filterstatic);
	if ($nb>0)
	{
		//determinamos la cantidad de activos a depreciar para ese periodo
		foreach ($assets->lines AS $k => $linek)
		{
			$lDepr = true;
			$date_active = $linek->date_active;
			$aDate = dol_getdate($linek->date_baja);
			$date_baja = dol_mktime(23, 59, 59, $aDate['mon'],$aDate['mday'],$aDate['year']);

			//$date_baja = $linek->date_baja;
			//echo '<hr>'.$linek->id.' '.dol_print_date($date_active,'day').' '.dol_print_date($date_end,'day').' baja '.$date_baja.' |'.dol_print_date($date_baja,'day').'| ';
			if ($date_active < $date_end)
			{
				//echo ' si ';
				//echo dol_print_date($date_baja,'day').' < '.dol_print_date($date_end,'day').' '.$date_baja.' '.$date_end;
				if ($date_baja == $date_end)
				{
					$lDepr = true;
				}
				elseif (!empty($date_baja) && $date_baja < $date_end)
				{
					//echo 'la baja es mayor a la fecha';
					$lDepr = false;
				}
			}
			else
			{
				//echo ' NO ';
				$lDepr = false;
			}
			if ($lDepr) $aActivenb[$linek->id] = $linek->id;
		}
	}
	$nb = count($aActivenb);
	$objmov = new Assetsmovext($db);
	$filterstatic = " AND t.ref = ".$ref;
	$filterstatic.= " AND t.movement_type = 'DEPR'";
	if ($type_group)
		$filterstatic.= " AND t.type_group = '".$type_group."'";
	$resm = $objmov->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,false);
	$lValid = true;
	//echo $nb.' '.$resm;
	if ($nb <> $resm) $lValid = false;
	if ($resm >0)
	{
		include DOL_DOCUMENT_ROOT.'/assets/depreciation/tpl/list.tpl.php';
	}
	else
	{
		print dol_fiche_head();
		print '<h3>'.$langs->trans('No existe proceso de depreciación para el periodo').': '.$month.'/'.$year;
		if ($type_group)
		{
			dol_include_once('/assets/class/cassetsgroup.class.php');
			$group = new Cassetsgroup($db);
			$res = $group->fetch(0,$type_group);
			if ($res>0)
				print ' '.$langs->trans('Andgroup').': '.$group->label;
		}
		print '</h3>';
		print dol_fiche_end();
	}
}


// End of page
llxFooter();
$db->close();
