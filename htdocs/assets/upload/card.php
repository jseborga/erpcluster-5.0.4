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
 *	\file       htdocs/salary/upload/fiche.php
 *	\ingroup    salary subida archivos
 *	\brief      Page fiche upload
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';


require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/lib/assets.lib.php';


//excel para version 4 o sup
$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
if (file_exists($file))
{
	$ver = 2;
	require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
}
$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
if (file_exists($file))
	include_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

$langs->load("assets");

$action=GETPOST('action');

$id         = GETPOST("rowid");
$rid        = GETPOST("rid");
$fk_period  = GETPOST("fk_period");
$fk_concept = GETPOST("fk_concept");
$docum      = GETPOST('docum');
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
$table = GETPOST('table');
$typeformat = GETPOST('typeformat');
$date_dep = dol_mktime(0,0,0,GETPOST('dd_month'),GETPOST('dd_day'),GETPOST('dd_year'));
$mesg = '';

$objUser  = new User($db);

$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');
$aCampodate = array('date_commande' =>'date_commande',
	'date_livraison' => 'date_livraison');

//params docum
/*
 1 = Id
 2 = Login
 3 = Docum
*/

 $seq = 1;
 for($i=65; $i<=90; $i++) {
 	$letra = chr($i);
 	$aHeader[$seq]= $letra;
 	$seq++;
 }

/*
 * Actions
 */

// AddSave
if ($action == 'addSave')
{
	$error = 0;
	$aArrData   = $_SESSION['aArrData'];
	$aArrTable = $_SESSION['aArrTable'];
	$aHeaders = $_SESSION['aHeaders'];
	$date_dep = GETPOST('date_dep');
	$aCampo = array();
	$llaveid = '';
	$llaveref = '';
	$lEntity = false;
	foreach ($aArrTable AS $i => $dat)
	{
		$aCampo[$i] = $dat[0];
	}
	$table = GETPOST('table');
	if ($table == 'llx_assets_low')
		$infotable = $db->DDLInfoTable('llx_assets');
	else
		$infotable = $db->DDLInfoTable($table);
	$aCampo = array();
	$aCampolabel = array();

	foreach ($infotable AS $i => $dat)
	{
		$aCampo[$i] = $dat[0];
		$aCampolabel[$dat[0]] = $i;
	}
	//agregamos campos adicionales si es accountancy
	if ($table == 'llx_accounting_account')
	{
		$i++;
		$aCampo[$i] = 'cta_normal';
		$aCampolabel['cta_normal'] = $i;
		$i++;
		$aCampo[$i] = 'cta_class';
		$aCampolabel['cta_class'] = $i;
	}
	if ($table == 'llx_contab_seat_det')
	{
		$i++;
		$aCampo[$i] = 'amount_debit';
		$aCampolabel['Amountdebit'] = $i;
		$i++;
		$aCampo[$i] = 'amount_credit';
		$aCampolabel['Amountcredit'] = $i;
	}


	$selrow = GETPOST('selrow');
	foreach ($_POST AS $i => $value)
	{
		$aPost = explode('__',$i);
		if ($aPost[0] == 'fkcampo')
		{
			$_POST['campo'][$aPost[1]] = $aCampo[$value];
			if (trim($aCampo[$value]) == 'rowid') $llaveid = $aPost[1];
			if (trim($aCampo[$value]) == 'ref') $llaveref = $aPost[1];
			if (trim($aCampo[$value]) == 'entity') $lEntity = true;
		}
	}
	//echo '<hr>res id '.$llaveid.' ref '.$llaveref.' ent '.$lEntity;
	switch ($table)
	{
		case 'llx_assets':
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';
		require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
		break;
		case 'llx_assets_low':
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetslowext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetscondition.class.php';
		require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
		break;
		case 'llx_c_assets_group':
		require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';
		break;
		case 'llx_c_low':
		require_once DOL_DOCUMENT_ROOT.'/assets/class/clow.class.php';
		break;
		case 'llx_c_clasfin':
		require_once DOL_DOCUMENT_ROOT.'/orgman/class/cclasfin.class.php';
		break;
		case 'llx_assets_balance':
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsbalanceext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovlogext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/multicurren/class/csindexescountryext.class.php';
		break;
		case 'llx_assets_mov':
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsbalanceext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovlogext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/multicurren/class/csindexescountryext.class.php';
		break;
	}
	//listamos los campos date
	$aListdate = explode(';',$camposdate);
	$aList = array();
	foreach((array) $aListdate AS $j => $value)
		$aList[$value] = $value;
	$db->begin();
	$now = dol_now();
	$aFather = array();
	$b =1;
	$aCampo = GETPOST('campo');
	foreach ((array) $aArrData AS $i => $data)
	{
		switch ($table)
		{
			//revaluos
			case 'llx_assets_mov':
			if (!$error)
			{
				$objAssets = new Assetsext($db);
				$objAssetsmov = new Assetsmovext($db);
				$objCsindexes = new Csindexescountryext($db);
				$objTmp = new Assetsext($db);
				//obtenemos la referencia del activo importado
				$ref = '';
				$day = '';
				$month = '';
				$year = '';
				$movement_type = 'REVAL';
				foreach ($_POST['campo'] AS $j => $campo)
				{
					if (!empty($campo))
					{
						if ($campo == 'fk_asset') $ref = $data[$j];
						if ($campo == 'date_reval_day') $day = $data[$j];
						if ($campo == 'date_reval_month') $month = $data[$j];
						if ($campo == 'date_reval_year') $year = $data[$j];
						//if ($campo == 'movement_type') $movement_type = $data[$j];
					}
				}

				//verificamos si existe el registro
				if (!empty($ref))
				{
					$resAsset = $objAssets->fetch(0,$ref);
					if ($resAsset==0)
					{
						$filtertmp = " AND t.ref_ext = '".$ref."'";
						$resAsset = $objAssets->fetchAll('','',0,0,array(1=>1),'AND',$filtertmp,true);
					}
				}
				//buscamos el registro del revaluo si existe
				$refmov = $year.(strlen($month)==1?'0'.$month:$month);
				$resMov=0;
				if ($objAssets->id>0)
				{
					$filtermov = " AND t.fk_asset = ".$objAssets->id;
					$filtermov.= " AND t.ref = '".$refmov."'";
					$filtermov.= " AND t.movement_type = '".$movement_type."'";
					$resMov = $objAssetsmov->fetchAll('','',0,0,array(1=>1),'AND',$filtermov,true);
				}
				foreach ($_POST['campo'] AS $j => $campo)
				{
					if (!$error)
					{
						$row = $data[$j];
						if (!empty($campo))
						{
							$objAssetsmov->$campo = $data[$j];
						}
					}
				}
				//buscamos el activo
				//$res = $objAssets->fetch(0,$objAssetsmov->fk_asset);
				if ($resAsset>0)
				{
					$objAssetsmov->fk_asset = $objAssets->id;
					$objAssetsmov->type_group = $objAssets->type_group;
				}
				else
				{
					setEventMessages($langs->trans('No existe el activo, revise').' '.$objAssetsmov->fk_asset,null,'warnings');
					continue;
				}
				$objAssetsmov->ref = $i;
				$ref = '';
				$date_active = 0;
				if ($objAssetsmov->date_reval_day>0 && $objAssetsmov->date_reval_month>0 && $objAssetsmov->date_reval_year>0)
				{
					$date_reval = dol_mktime(0,0,0,$objAssetsmov->date_reval_month,$objAssetsmov->date_reval_day,$objAssetsmov->date_reval_year);
					$ref = $objAssetsmov->date_reval_year.(strlen($objAssetsmov->date_reval_month)==1?'0'.$objAssetsmov->date_reval_month:$objAssetsmov->date_reval_month);
					$aDate = dol_get_next_day( $objAssetsmov->date_reval_day, $objAssetsmov->date_reval_month, $objAssetsmov->date_reval_year );
					$date_active = dol_mktime(0,0,0,$aDate['month'],$aDate['day'],$aDate['year']);
				}
				else
				{
					$error=301;
					setEventMessages($langs->trans('No esta definido fecha revaloriación'),null,'errors');
				}
				//buscamos el tipo de cambio de la fecha de corte
				$res = $objCsindexes->fetch(0,$conf->global->ASSETS_CURRENCY_DEFAULT,$db->idate($date_reval));
				if ($res >0)
				{
					$objAssetsmov->tcend = $objCsindexes->amount;
					$objAssetsmov->tcini = $objCsindexes->amount;
				}

				$objAssetsmov->tcini +=0;
				$objAssetsmov->tcend +=0;
				$objAssetsmov->entity = $conf->entity;
				$objAssetsmov->ref = $ref;

				$objAssetsmov->date_ini = $date_reval;
				$objAssetsmov->date_end = $date_reval;
				$objAssetsmov->date_reval = $date_reval;
				$objAssetsmov->month_depr = $objAssets->useful_life*365;
				//$objAssetsmov->coste = $objAssets->coste+0;
				$objAssetsmov->coste_residual = $objAssets->coste_residual+0;
				$objAssetsmov->amount_base += 0;
				$objAssetsmov->factor_update += 0;
				$objAssetsmov->time_consumed +=0;
				$objAssetsmov->amount_update +=0;
				$objAssetsmov->amount_depr +=0;
				$objAssetsmov->amount_depr_acum+= 0;
				$objAssetsmov->amount_balance_depr +=0;
				$objAssetsmov->amount_balance +=0;
				$objAssetsmov->amount_sale = 0;
				$objAssetsmov->movement_type = 'REVAL';
				$objAssetsmov->fk_user_create = $user->id;
				$objAssetsmov->fk_user_mod = $user->id;
				$objAssetsmov->datec = $date_reval;
				$objAssetsmov->dateu = $date_reval;
				$objAssetsmov->status = 3;

				if ($resMov==1)
				{
					$res = $objAssetsmov->update($user);
				}
				elseif($resMov==0)
				{
					$res = $objAssetsmov->create($user);
				}
				else
				{
					$error=1201;
					setEventMessages($objAssetsmov->error,$objAssetsmov->errors,'errors');
				}
				if ($res <=0)
				{
					$error=1202;
					setEventMessages($objAssetsmov->error,$objAssetsmov->errors,'errors');
				}
				//actualziamos el assets
				$objAssets->status_reval = 6;
				$objAssets->date_reval = $date_active;
				$objAssets->coste_reval = $objAssets->coste;
				$objAssets->useful_life_reval = $objAssets->useful_life;
				$objAssets->coste_residual_reval = $objAssets->coste_residual;
				if ($objAssets->date_active < $date_active && $date_active > 0)
					$objAssets->date_active = $date_active;
				$res = $objAssets->update($user);

				if ($res <=0)
				{
					$error=1203;
					setEventMessages($objAssets->error,$objAssets->errors,'errors');
				}

			}
			break;

			case 'llx_assets_balance':
			if (!$error)
			{
				$objAssetsbalance = new Assetsbalanceext($db);
				$objAssets = new Assetsext($db);
				$objAssetsmov = new Assetsmovext($db);
				$objAssetsmovlog = new Assetsmovlogext($db);
				$objCsindexes = new Csindexescountryext($db);
				foreach ($_POST['campo'] AS $j => $campo)
				{
					if (!$error)
					{
						$row = $data[$j];
						if (!empty($campo))
						{
							$objAssetsbalance->$campo = $data[$j];
							$objAssetsmov->$campo = $data[$j];
							$objAssetsmovlog->$campo = $data[$j];
						}
					}
				}
				//buscamos el activo
				$res = $objAssets->fetch(0,$objAssetsbalance->fk_asset);
				if ($res>0)
				{
					$objAssetsbalance->fk_asset = $objAssets->id;
					$objAssetsbalance->type_group = $objAssets->type_group;

					$objAssetsmov->fk_asset = $objAssets->id;
					$objAssetsmov->type_group = $objAssets->type_group;

					$objAssetsmovlog->fk_asset = $objAssets->id;
					$objAssetsmovlog->type_group = $objAssets->type_group;

				}
				else
				{
					setEventMessages($langs->trans('No existe el activo, revise').' '.$objAssetsbalance->fk_asset,null,'warnings');
					continue;
				}
				$objAssetsbalance->ref = $i;
				$objAssetsmov->ref = $i;
				$objAssetsmovlog->ref = $i;
				if ($objAssetsbalance->date_mig_day>0 && $objAssetsbalance->date_mig_month>0 && $objAssetsbalance->date_mig_year>0)
					$date_ini = dol_mktime(0,0,0,$objAssetsbalance->date_mig_month,$objAssetsbalance->date_mig_day,$objAssetsbalance->date_mig_year);
				elseif ($objAssetsbalance->date_ini_day>0 && $objAssetsbalance->date_ini_month>0 && $objAssetsbalance->date_ini_year>0)
					$date_ini = dol_mktime(0,0,0,$objAssetsbalance->date_ini_month,$objAssetsbalance->date_ini_day,$objAssetsbalance->date_ini_year);
				else
				{
					$error=301;
					setEventMessages($langs->trans('No esta definido fecha'),null,'errors');
				}
				//buscamos el tipo de cambio de la fecha de corte
				$res = $objCsindexes->fetch(0,$conf->global->ASSETS_CURRENCY_DEFAULT,$db->idate($date_dep));
				if ($res >0)
				{
					$objAssetsbalance->tcend = $objCsindexes->amount;
					$objAssetsmov->tcend = $objCsindexes->amount;
					$objAssetsmovlog->tcend = $objCsindexes->amount;
				}

				$objAssetsbalance->entity = $conf->entity;
				$objAssetsbalance->date_ini = $date_ini;
				$objAssetsbalance->date_end = $date_dep;
				$objAssetsbalance->month_depr = $objAssets->useful_life*365;
				$objAssetsbalance->coste = $objAssets->coste+0;
				$objAssetsbalance->coste_residual = $objAssets->coste_residual;
				$objAssetsbalance->amount_base = 0;
				$objAssetsbalance->amount_update = $objAssetsbalance->amount_balance;
				$objAssetsbalance->amount_depr = 0;
				$objAssetsbalance->amount_depr_acum+= 0;
				$objAssetsbalance->amount_balance_depr = $objAssetsbalance->amount_depr_acum_update;
				$objAssetsbalance->amount_sale = 0;
				$objAssetsbalance->movement_type = 'DEPR';
				$objAssetsbalance->fk_user_create = $user->id;
				$objAssetsbalance->fk_user_mod = $user->id;
				$objAssetsbalance->datec = $date_dep;
				$objAssetsbalance->dateu = $date_dep;
				$objAssetsbalance->status = 2;

				$objAssetsmov->entity = $conf->entity;
				$objAssetsmov->date_ini = $date_ini;
				$objAssetsmov->date_end = $date_dep;
				$objAssetsmov->month_depr = $objAssets->useful_life*365;
				$objAssetsmov->coste = $objAssets->coste+0;
				$objAssetsmov->coste_residual = $objAssets->coste_residual;
				$objAssetsmov->amount_base = 0;
				$objAssetsmov->amount_update = $objAssetsbalance->amount_balance;
				$objAssetsmov->amount_depr = 0;
				$objAssetsmov->amount_depr_acum+= 0;
				$objAssetsmov->amount_balance_depr = $objAssetsmov->amount_depr_acum_update;
				$objAssetsmov->amount_sale = 0;
				$objAssetsmov->movement_type = 'DEPR';
				$objAssetsmov->fk_user_create = $user->id;
				$objAssetsmov->fk_user_mod = $user->id;
				$objAssetsmov->datec = $date_dep;
				$objAssetsmov->dateu = $date_dep;
				$objAssetsmov->status = 2;

				$objAssetsmovlog->entity = $conf->entity;
				$objAssetsmovlog->date_ini = $date_ini;
				$objAssetsmovlog->date_end = $date_dep;
				$objAssetsmovlog->month_depr = $objAssets->useful_life*365;
				$objAssetsmovlog->coste = $objAssets->coste+0;
				$objAssetsmovlog->coste_residual = $objAssets->coste_residual;
				$objAssetsmovlog->amount_base = 0;
				$objAssetsmovlog->amount_update = $objAssetsbalance->amount_balance;
				$objAssetsmovlog->amount_depr = 0;
				$objAssetsmovlog->amount_depr_acum+= 0;
				$objAssetsmovlog->amount_balance_depr = $objAssetsmovlog->amount_depr_acum_update;
				$objAssetsmovlog->amount_sale = 0;
				$objAssetsmovlog->movement_type = 'DEPR';
				$objAssetsmovlog->fk_user_create = $user->id;
				$objAssetsmovlog->fk_user_mod = $user->id;
				$objAssetsmovlog->datec = $date_dep;
				$objAssetsmovlog->dateu = $date_dep;
				$objAssetsmovlog->status = 2;

				$res = $objAssetsbalance->create($user);
				if ($res <=0)
				{
					$error=201;
					setEventMessages($objAssetsbalance->error,$objAssetsbalance->errors,'errors');
				}
				$res = $objAssetsmov->create($user);
				if ($res <=0)
				{
					$error=1201;
					setEventMessages($objAssetsmov->error,$objAssetsmov->errors,'errors');
				}
				$res = $objAssetsmovlog->create($user);
				if ($res <=0)
				{
					$error=2201;
					setEventMessages($objAssetsmovlog->error,$objAssetsmovlog->errors,'errors');
				}


			}
			break;
			case 'llx_assets':
			if (!$error)
			{
				$objAssets = new Assetsext($db);
				$objAssetsgroup = new Cassetsgroup($db);
				$objTmp = new Assetsext($db);
				//obtenemos la referencia del activo importado
				$ref = '';
				$ref_ext = '';
				foreach ($_POST['campo'] AS $j => $campo)
				{
					if (!empty($campo))
					{
						if ($campo == 'ref') $ref = $data[$j];
						if ($campo == 'ref_ext') $ref_ext = $data[$j];
					}
				}
				//verificamos si existe el registro
				if (!empty($ref))
				{
					$resAsset = $objAssets->fetch(0,$ref);
				}
				elseif (!empty($ref_ext))
				{
					$filtertmp = " AND t.ref_ext = '".$ref_ext."'";
					$resAsset = $objAssets->fetchAll('','',0,0,array(1=>1),'AND',$filtertmp,true);
				}

				//cargamos la informacion
				foreach ($_POST['campo'] AS $j => $campo)
				{
					if (!$error)
					{
						$row = $data[$j];
						if (!empty($campo))
						{
							$objAssets->$campo = $data[$j];
						}
					}
				}
				if ($typeformat == 1)
				{

					if (empty($objAssets->type_group))
					{
						if ($objAssets->codcont)
						{
							$objAssets->type_group = $objAssets->codcont;
							$objAssetslow->type_group = $objAssets->codcont;
						}
					}
					$objTmp->fetch_max($objAssets->type_group);
					$objAssets->item_asset = $objTmp->maximo;
					if (empty($objAssets->date_adq))
					{
						if ($objAssets->date_day>0 && $objAssets->date_month>0 && $objAssets->date_year>0)
						{
							$objAssets->date_adq = dol_mktime(12,0,0,$objAssets->date_month,$objAssets->date_day,$objAssets->date_year);
						}
					}
				}
				elseif ($typeformat == 2)
				{
					//formato que debe hacer varias verificaciones en grupo contable, oficina, responsable
					//
					if (empty($objAssets->type_group))
					{
						if ($objAssets->codcont)
						{
							$aCodcont = explode('-',$objAssets->codcont);
						 	//buscamos en group
							$resg = $objAssetsgroup->fetch(0,trim($aCodcont[0]));
							if ($resg>0)
							{
								$objAssets->type_group = $objAssetsgroup->code;
								$objAssets->codcont = $objAssetsgroup->code;
							}
							else
							{
								$error=501;
								setEventMessages($langs->trans('No se encontro el grupo'),null,'errors');
							}
						}
					}
					if (!empty($objAssets->fk_departament))
					{
						$aDepartament = explode('-',$objAssets->fk_departament);
						 	//buscamos en group
						$objDepartament = new Pdepartamentext($db);
						$filterdep = " AND UPPER(t.label) like '%".TRIM($aDepartament[1]."%'");
						$resg = $objDepartament->fetchAll('','',0,0,array(1=>1),'AND',$filterdep,true);
						if ($resg>0)
							$objAssets->fk_departament = $objDepartament->id;
						elseif($resg == 0)
						{
							//agregamos a Pdepartament,
							$aRef = explode(' ',$aDepartament[1]);
							$txtRef = '';
							foreach ($aRef AS $k => $valuedep)
							{
								$txtRef.= substr($valuedep,0,1);
							}
							//buscamos nuevamente por la referencia
							$resg = $objDepartament->fetch(0,$txtRef);
							if ($resg == 0)
							{
								$objDepartament->ref = $txtRef;
								$objDepartament->label = $aDepartament[1];
								$objDepartament->fk_user_create = $user->id;
								$objDepartament->fk_user_mod = $user->id;
								$objDepartament->entity       = $conf->entity;
								$objDepartament->fk_father    = GETPOST("fk_father")+0;
								$objDepartament->fk_user_resp = GETPOST("fk_user_resp")+0;
								$objDepartament->datec = dol_now();
								$objDepartament->datem = dol_now();
								$objDepartament->tms = dol_now();
								$objDepartament->active = 1;
								$objDepartament->status = 1;
								if ($objDepartament->ref)
								{
									echo '<hr>iddep '.$fk_departament = $objDepartament->create($user);
									if ($fk_departament >0)
										$objAssets->fk_departament = $fk_departament;
								}
							}
							else
							{
								setEventMessages($langs->trans('Existe la referencia para el departamento, revise').' '.$txtRef,null.'warnings');
								$objAssets->fk_departament = 0;
							}
						}
						else
						{
							$error=502;
							setEventMessages($langs->trans('No se encontro el departamento').' '.$aDepartament[1],null,'errors');
						}
					}
					if (!empty($objAssets->fk_resp))
						$objAssets->fk_resp = 0;
					if (!empty($objAssets->codaux))
						$objAssets->codaux = 0;
				}
				if ($resAsset==0)
				{
					$objTmp->fetch_max($objAssets->type_group);
					$objAssets->item_asset = $objTmp->maximo;
				}
				if (empty($objAssets->date_adq))
				{
					if ($objAssets->date_day>0 && $objAssets->date_month>0 && $objAssets->date_year>0)
						$objAssets->date_adq = dol_mktime(12,0,0,$objAssets->date_month,$objAssets->date_day,$objAssets->date_year);
					else
					{
						setEventMessages($langs->trans('El activo').' '.$objAssets->ref.' '.$langs->trans('No tiene fecha') ,null,'warnings');
						continue;
					}
				}

				if (empty($objAssets->ref)) $objAssets->ref = '(PROV)';
				if (empty($objAssets->type_patrim)) $objAssets->type_patrim = 'AF';
				if (empty($objAssets->quant)) $objAssets->quant = 1;
				if (empty($objAssets->date_active)||is_null($objAssets->date_active))
				{
					$objAssets->date_active = $objAssets->date_adq;
				}
				if (!empty($objAssets->status_reval))
				{
					$objAssets->date_reval = $objAssets->date_active;
					$objAssets->coste_reval = $objAssets->coste;
					$objAssets->coste_residual_reval = $objAssets->coste_residual;
				}

				//datos fijos
				$objAssets->entity = $conf->entity;
				$objAssets->fk_user_mod = $user->id;
				if ($resAsset==0)
				{
					$objAssets->fk_user_create = $user->id;
					$objAssets->date_create = $now;
					$objAssets->active = 1;
					$objAssets->statut = 9;
				}
				$objAssets->date_mod = $now;
				$objAssets->status_reval+=0;
				$ref = substr($objAssets->ref, 1, 4);
				if ($ref == 'PROV') $objAssets->ref = $objTmp->getNextNumRef($objAssets);
				if ($resAsset>0)
				{
					$res = $objAssets->update($user);
				}
				else
				{
					$res = $objAssets->create($user,1);
				}
				if ($res <=0)
				{
					$error=601;
					setEventMessages($objAssets->error,$objAssets->errors,'errors');
				}
			}
			else
				continue;
			if ($error)
				$action = 'create';
			break;

			case 'llx_assets_low':
			if (!$error)
			{
				$objAssets = new Assetsext($db);
				$objAssetslow = new Assetslowext($db);
				$objAssetsgroup = new Cassetsgroup($db);
				$objCond = new Assetscondition($db);
				//para baja el estado del bien es -1
				$objTmp = new Assetsext($db);
				$fk_asset = 0;
				//obtenemos la referencia del activo importado
				$ref = '';
				$ref_ext = '';
				foreach ($_POST['campo'] AS $j => $campo)
				{
					if (!empty($campo))
					{
						if ($campo == 'ref') $ref = $data[$j];
						if ($campo == 'ref_ext') $ref_ext = $data[$j];
					}
				}
				//verificamos si existe el registro
				if (!empty($ref))
				{
					$resAsset = $objAssets->fetch(0,$ref);
				}
				elseif (!empty($ref_ext))
				{
					$filtertmp = " AND t.ref_ext = '".$ref_ext."'";
					$resAsset = $objAssets->fetchAll('','',0,0,array(1=>1),'AND',$filtertmp,true);
				}
				if ($resAsset>0)
				{
					$fk_asset = $objAssets->id;
					$objAssetslow->type_group = $objAssets->type_group;
				}
				//verificamos si esta registrado en assets_low
				if (!empty($ref))
				{
					$resAssetlow = $objAssetslow->fetch(0,$ref);
				}
				elseif (!empty($ref_ext))
				{
					$filtertmp = " AND t.ref_ext = '".$ref_ext."'";
					$resAssetlow = $objAssetslow->fetchAll('','',0,0,array(1=>1),'AND',$filtertmp,true);
				}

				//cargamos la informacion
				foreach ($_POST['campo'] AS $j => $campo)
				{
					if (!$error)
					{
						$row = $data[$j];
						if (!empty($campo))
						{
							$objAssets->$campo = $data[$j];
							$objAssetslow->$campo = $data[$j];
						}
					}
				}
				if ($typeformat == 1)
				{
					if (empty($objAssets->type_group))
					{
						if ($objAssets->codcont)
						{
							$objAssets->type_group = $objAssets->codcont;
							$objAssetslow->type_group = $objAssets->codcont;
						}
						else
						{
							if ($fk_assets>0) $objAssetslow->type_group = $objAssets->type_group;
						}
					}
					$objTmp->fetch_max($objAssets->type_group);
					$objAssets->item_asset = $objTmp->maximo;
					if (empty($objAssets->date_adq))
					{
						if ($objAssets->date_day>0 && $objAssets->date_month>0 && $objAssets->date_year>0)
						{
							$objAssets->date_adq = dol_mktime(12,0,0,$objAssets->date_month,$objAssets->date_day,$objAssets->date_year);
							$objAssetslow->date_adq = dol_mktime(12,0,0,$objAssets->date_month,$objAssets->date_day,$objAssets->date_year);
						}
					}
				}
				elseif ($typeformat == 2)
				{
					//formato que debe hacer varias verificaciones en grupo contable, oficina, responsable
					//
					if (empty($objAssets->type_group))
					{
						if ($objAssets->codcont)
						{
							$aCodcont = explode('-',$objAssets->codcont);
						 	//buscamos en group
							$resg = $objAssetsgroup->fetch(0,trim($aCodcont[0]));
							if ($resg>0)
							{
								$objAssets->type_group = $objAssetsgroup->code;
								$objAssetslow->type_group = $objAssetsgroup->code;
								$objAssets->codcont = $objAssetsgroup->code;
								$objAssetslow->codcont = $objAssetsgroup->code;
							}
							else
							{
								$error=501;
								setEventMessages($langs->trans('No se encontro el grupo'),null,'errors');
							}
						}
					}
					if (!empty($objAssets->fk_departament))
					{
						$aDepartament = explode('-',$objAssets->fk_departament);
						 	//buscamos en group
						$objDepartament = new Pdepartamentext($db);
						$filterdep = " AND UPPER(t.label) like '%".TRIM($aDepartament[1]."%'");
						$resg = $objDepartament->fetchAll('','',0,0,array(1=>1),'AND',$filterdep,true);
						if ($resg>0)
						{
							$objAssets->fk_departament = $objDepartament->id;
							$objAssetslow->fk_departament = $objDepartament->id;
						}
						elseif($resg == 0)
						{
							//agregamos a Pdepartament,
							$aRef = explode(' ',$aDepartament[1]);
							$txtRef = '';
							foreach ($aRef AS $k => $valuedep)
							{
								$txtRef.= substr($valuedep,0,1);
							}
							//buscamos nuevamente por la referencia
							$resg = $objDepartament->fetch(0,$txtRef);
							if ($resg == 0)
							{
								$objDepartament->ref = $txtRef;
								$objDepartament->label = $aDepartament[1];
								$objDepartament->fk_user_create = $user->id;
								$objDepartament->fk_user_mod = $user->id;
								$objDepartament->entity       = $conf->entity;
								$objDepartament->fk_father    = GETPOST("fk_father")+0;
								$objDepartament->fk_user_resp = GETPOST("fk_user_resp")+0;
								$objDepartament->datec = dol_now();
								$objDepartament->datem = dol_now();
								$objDepartament->tms = dol_now();
								$objDepartament->active = 1;
								$objDepartament->status = 1;
								if ($objDepartament->ref)
								{
									$fk_departament = $objDepartament->create($user);
									if ($fk_departament >0)
									{
										$objAssets->fk_departament = $fk_departament;
										$objAssetslow->fk_departament = $fk_departament;
									}
								}
							}
							else
							{
								setEventMessages($langs->trans('Existe la referencia para el departamento, revise').' '.$txtRef,null.'warnings');
								$objAssets->fk_departament = 0;
								$objAssetslow->fk_departament = 0;
							}
						}
						else
						{
							$error=502;
							setEventMessages($langs->trans('No se encontro el departamento').' '.$aDepartament[1],null,'errors');
						}
					}
					if (!empty($objAssets->fk_resp))
					{
						$objAssets->fk_resp = 0;
						$objAssetslow->fk_resp = 0;
					}
					if (!empty($objAssets->codaux))
					{
						$objAssets->codaux = 0;
						$objAssetslow->codaux = 0;
					}
				}
				if ($resAsset==0)
				{
					//echo '<hr>tuypegroup '.$objAssets->type_group;
					//echo '<hr>mx'.
					$objTmp->fetch_max($objAssets->type_group);
					$objAssets->item_asset = $objTmp->maximo;
					$objAssetslow->item_asset = $objTmp->maximo;
				}
				if (empty($objAssets->date_adq))
				{
					if ($objAssets->date_day>0 && $objAssets->date_month>0 && $objAssets->date_year>0)
					{
						$objAssets->date_adq = dol_mktime(12,0,0,$objAssets->date_month,$objAssets->date_day,$objAssets->date_year);
						$objAssetslow->date_adq = dol_mktime(12,0,0,$objAssets->date_month,$objAssets->date_day,$objAssets->date_year);
					}
					else
					{
						setEventMessages($langs->trans('El activo').' '.$objAssets->ref.' '.$langs->trans('No tiene fecha') ,null,'warnings');
						continue;
					}
				}
					if (empty($objAssets->date_baja))
					{
						if ($objAssets->baja_day>0 && $objAssets->baja_month>0 && $objAssets->baja_year>0)
						{
							$objAssets->date_baja = dol_mktime(12,0,0,$objAssets->baja_month,$objAssets->baja_day,$objAssets->baja_year);
							$objAssetslow->date_baja = dol_mktime(12,0,0,$objAssets->baja_month,$objAssets->baja_day,$objAssets->baja_year);
						}
					}

				if (empty($objAssets->ref))
				{
					$objAssets->ref = '(PROV)';
					$objAssetslow->ref = '(PROV)';
				}
				if (empty($objAssets->type_patrim))
				{
					$objAssets->type_patrim = 'AF';
				}
				if (empty($objAssets->quant))
				{
					$objAssets->quant = 1;
					$objAssetslow->quant = 1;
				}
				if (empty($objAssets->date_active)||is_null($objAssets->date_active))
				{
					$objAssets->date_active = $objAssets->date_adq;
					$objAssetslow->date_active = $objAssets->date_adq;
				}
				//datos fijos
				$objAssets->entity = $conf->entity;
				$objAssetslow->entity = $conf->entity;
				$objAssets->fk_user_mod = $user->id;
				$objAssetslow->fk_user_mod = $user->id;
				$objAssets->been = -1;
				$objAssets->statut = -1;
				if ($resAsset==0)
				{
					$objAssets->fk_user_create = $user->id;
					$objAssets->date_create = $now;
					$objAssets->active = 1;
					$objAssets->statut = 1;
				}
				$objAssets->date_mod = $now;
				$objAssets->status_reval+=0;
				$objAssetslow->date_mod = $now;
				$objAssetslow->status_reval+=0;
				$ref = substr($objAssets->ref, 1, 4);
				if ($ref == 'PROV') $objAssets->ref = $objTmp->getNextNumRef($objAssets);
				if ($resAsset>0) $res = $objAssets->update($user);
				else
				{
					$res = $objAssets->create($user,1);
					$fk_asset = $res;
				}
				if ($res <=0)
				{
					$error=601;

					setEventMessages($objAssets->error,$objAssets->errors,'errors');
				}
				if (!$error)
				{
					//vamos a crear el mismo registro en assets_low
					$infotable = $db->DDLInfoTable('llx_assets_low');


				}
				$objAssetslow->type_patrim = $objAssets->type_patrim;
				$objAssetslow->ref = $objAssets->ref;
				$objAssetslow->item_asset = $objAssets->item_asset;
				$objAssetslow->quant = $objAssets->quant;
				$objAssetslow->date_adq = dol_mktime(12,0,0,$objAssetslow->date_month,$objAssetslow->date_day,$objAssetslow->date_year);
				$objAssetslow->date_baja = dol_mktime(12,0,0,$objAssetslow->baja_month,$objAssetslow->baja_day,$objAssetslow->baja_year);
				$objAssetslow->fk_user_create = $user->id;
				$objAssetslow->date_create = $now;
				$objAssetslow->active = 1;
				$objAssetslow->statut = -1;
				$objAssetslow->been = -1;
				//verificamos que exista el activo
				if ($resAssetlow>0) $reslow = $objAssetslow->update($user);
				else
				{
					$objAssetslow->fk_asset = $fk_asset;
					$reslow = $objAssetslow->create($user,1);
				}
				if ($reslow <=0)
				{
					$error=602;
					setEventMessages($objAssetslow->error,$objAssetslow->errors,'errors');
				}
				//creamos la condicion
				$objCond->fk_asset = $fk_asset;
				$objCond->ref=$objAssetslow->baja_resolution;
				$objCond->fk_user=$user->id;
				$objCond->dater = $objAssetslow->date_baja;
				$objCond->been=-1;
				$objCond->description=$objAssetslow->baja_motive;
				$objCond->status=1;
				$objCond->fk_user_create = $user->id;
				$objCond->fk_user_mod = $user->id;
				$objCond->datec = $now;
				$objCond->datem = $now;
				$objCond->tms = $now;
				$res = $objCond->create($user);
				if ($res <=0)
				{
					$error=603;
					setEventMessages($objCond->error,$objCond->errors,'errors');
				}

			}
			else
				continue;
			if ($error)
			{
				$action = 'create';
			}

			break;

			case 'llx_c_assets_group':
			if (!$error)
			{
				$obj = new Cassetsgroup($db);
				$code = '';
				foreach ($_POST['campo'] AS $j => $campo)
				{
					if (!empty($campo))
						if ($campo == 'code') $code = $data[$j];
				}
				//buscamos
				$res = $obj->fetch(0,$code);

				foreach ($_POST['campo'] AS $j => $campo)
				{
					if (!$error)
					{
						$row = $data[$j];
						if (!empty($campo))
						{
							$obj->$campo = $data[$j];
						}
					}
				}
				$obj->depreciate+=0;
				$obj->toupdate+=0;
				$obj->entity = $conf->entity;
				$obj->fk_user_create = $user->id;
				$obj->fk_user_mod = $user->id;
				$obj->datec = $now;
				$obj->datem = $now;
				$obj->active = 1;
				$obj->statut = 1;
				if ($res == 1)
					$res = $obj->update($user,1);
				elseif($res == 0)
					$res = $obj->create($user,1);
				else
				{
					$error=603;
					setEventMessages($obj->error,$obj->errors,'errors');
				}
				if ($res <=0)
				{
					$error=602;
					setEventMessages($obj->error,$obj->errors,'errors');
				}
			}
			else
				continue;
			if ($error)
				$action = 'create';
			break;
			case 'llx_c_low':
			$obj = new Clow($db);
			$objTmp = new Clow($db);
			include DOL_DOCUMENT_ROOT.'/assets/upload/inc/default.inc.php';
			break;
			case 'llx_c_clasfin':
			$obj = new Cclasfin($db);
			$objTmp = new Cclasfin($db);
			include DOL_DOCUMENT_ROOT.'/assets/upload/inc/default.inc.php';
			break;
		}
	}
	//echo '<hr>fin '.$error;exit;
	if (!$error)
	{
		setEventMessages($langs->trans('Successfullupload'),null,'mesgs');
		$db->commit();
		header('Location: '.$_SERVER['PHP_SELF'].'?action=create');
		exit;
	}
	else
	{
		$db->rollback();
		$action = 'create';
	}

}
$aHeaderTpl['llx_c_assets_group'] = array('code' => 'code',
	'label' => 'label',
);
$aHeaderTpl['llx_c_low'] = array('ref' => 'ref',
	'label' => 'label',
);
$aHeaderTpl['llx_c_clasfin'] = array('ref' => 'ref',
	'label' => 'label',
);
$aHeaderTpl['llx_contab_seat_det'] = array('debit_account' => 'debit_account',
	'amount' => 'amount',
);

$aHeaderTpl['llx_accounting_account'] = array('fk_pcg_version' => 'fk_pcg_version',
	'pcg_type' => 'pcg_type',
	'pcg_subtype' => 'pcg_subtype',
	'account_number'=>'account_number',
	'account_parent' => 'account_parent',
	'label'=>'label',
	'fk_accouting_category'=>'fk_accouting_category',
);
$aHeaderTpl['llx_assets'] = array('ref' => 'ref',
	'type_group' => 'type_group',
	'date_adq' => 'date_adq',
	'quant'=>'quant',
	'coste' => 'coste',
	'been' => 'been',
);
$aHeaderTpl['llx_assets_low'] = array('ref' => 'ref',
	'type_group' => 'type_group',
	'date_adq' => 'date_adq',
	'quant'=>'quant',
	'coste' => 'coste',
	'been' => 'been',
);
$aHeaderTpl['llx_assets_balance'] = array('fk_assets' => 'fk_assets',
	'factor_update' => 'factor_update',
);
$aHeaderTpl['llx_assets_mov'] = array('fk_assets' => 'fk_assets',
	'date_adq' => 'date_adq',
);

// Add
if ($action == 'add')
{
	$table = GETPOST('table');
	$selrow = GETPOST('selrow');
	$tempdir = "tmp/";

	if (($table == 'llx_assets'||$table == 'llx_assets_low') && !$confg->global->ASSETS_INTEGRATION_WITH_DEPARTAMENT_MEMBER)
	{
		//procesamos la subida de oficina y responsable
		//oficinas
		$nombre_archivo = $_FILES['archivodep']['name'];
		$tipo_archivo   = $_FILES['archivodep']['type'];
		$tamano_archivo = $_FILES['archivodep']['size'];
		$tmp_name       = $_FILES['archivodep']['tmp_name'];
		list($aDetalle,$aHeaders,$aDepartament) = procesa_upload_files($aHeader, $aHeaderTpl, $table, $nombre_archivo, $tmp_name, $tempdir, $selrow, 'departament');
		//member
		$nombre_archivo = $_FILES['archivoresp']['name'];
		$tipo_archivo   = $_FILES['archivoresp']['type'];
		$tamano_archivo = $_FILES['archivoresp']['size'];
		$tmp_name       = $_FILES['archivoresp']['tmp_name'];
		list($aDetalle,$aHeaders,$aMember) = procesa_upload_files($aHeader, $aHeaderTpl, $table, $nombre_archivo, $tmp_name, $tempdir, $selrow, 'member');
	}

	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];

    //compruebo si la extension es correcta
	if(move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
	{

	//  echo "file uploaded<br>";
	}
	else
	{
		echo 'no se puede mover';
		exit;
	}


	$nTipeSheet = substr($nombre_archivo,-3);


	$objPHPExcel = new PHPExcel();
	$type = '';
	if ($nTipeSheet =='lsx')
	{
		$type = 'spreedsheat';
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	}
	elseif ($nTipeSheet =='xls')
	{
		$type = 'spreedsheat';
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	}
	elseif ($nTipeSheet =='csv')
	{
		$type = 'csv';
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	}
	else
	{
		echo "Documento no valido verifique que sea el correcto para la importacion";
		print "<a href=".DOL_URL_ROOT."/eva/import_eva.php>Volver</a>";
		exit;
	}

	if ($type == 'spreedsheat')
	{
		$objPHPExcel = $objReader->load('tmp/'.$nombre_archivo);
		$objReader->setReadDataOnly(true);

		$nOk = 0;
		$nLoop = 26;
		$nLine=1;
		if ($selrow)
		{
			for ($a = 1; $a <= $nLoop; $a++)
			{
				$dato = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$nLine)->getValue();
				$aHeaders[$a]=$dato;
			}
			$nLine++;
			if (($table == 'llx_assets'|| $table == 'llx_assets_low') && !$confg->global->ASSETS_INTEGRATION_WITH_DEPARTAMENT_MEMBER)
			{
				$aHeaders[$a]= 'NOMOFIC';
				$a++;
				$aHeaders[$a]= 'NOMRESP';
			}
		}


		$lLoop = true;
		$i = 0;
		while ($lLoop == true)
		{
			if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeader[1].$nLine)->getValue()))
			{
				for ($a = 1; $a <= $nLoop; $a++)
				{
					$aCampo = explode(',',$aHeaders[$a]);
					if ($aCampo[0] == 'FECHA')
					{
						$dato = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$nLine)->getFormattedValue();
						$dato = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$nLine)->getValue()+1;
						$timestamp = PHPExcel_Shared_Date::ExcelToPHP($dato);
						$dato = $timestamp;
					}
					else
						$dato = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$nLine)->getValue();
					$aDetalle[$i][$a]=$dato;
				}
				$i++;
			}
			elseif(empty($objPHPExcel->getActiveSheet()->getCell($aHeader[1].$nLine)->getFormattedValue()))
			{
				$lLoop = false;
			}
			$nLine++;
		}
	}
	elseif ($type == 'csv')
	{
		$csvfile = $tempdir.$nombre_archivo;

		$fh = fopen($csvfile, 'r');
		$headers = fgetcsv($fh);
		$aHeaders = explode($separator,$headers[0]);
		$data = array();
		$aData = array();
		while (! feof($fh))
		{
			$row = fgetcsv($fh,'','^');
			if (!empty($row))
			{
				$aData = explode($separator,$row[0]);
				$obj = new stdClass;
				if (!is_object($objheader))
					$objheader = new stdClass;
				$obj->none = "";
				foreach ($aData as $i => $value)
				{
					$key = $aHeaders[$i];
					if (!empty($key))
					{
						$obj->$key = $value;
						$objheader->$i = $key;
					}
					else
						$obj->none = $value." xx";
				}
				if (!$selrow)
					$data[] = $aData;
				else
					$data[] = $obj;
			}
		}
		print_r($objheader);
		fclose($fh);
	}
	$c=0;
	$action = "edit";
}




if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}
//campos principales tabla


$aTable = array(
	'llx_accounting_account'=>'Accountingaccount',
	'llx_categorie'   => 'Category',
	'llx_product'     => 'Product',
	'llx_categorie_product' => 'Category product',
	'llx_commande'    => 'Pedidos',
	'llx_commandedet' => 'Pedidos productos',
	'llx_c_departements' => 'Departamentos/Provincias',
	'llx_c_partida'   => 'Partidas de Gasto',
	'llx_poa_poa'     => 'Poa');

$aTable = array(
	'llx_c_assets_group' => $langs->trans('Groupassets'),
	'llx_assets'=>$langs->trans('Assets'),
	'llx_c_low'=>$langs->trans('Codelows'),
	//'llx_c_clasfin'=>$langs->trans('Institutionalclassifier'),
	//'llx_assets_balance' => $langs->trans('Balanceassets'),
	//'llx_assets_mov' => $langs->trans('Revaluation'),
	'llx_assets_low' => $langs->trans('Assetsinlowstatus'),
);
$aCampolabelsigep['llx_assets_mov'] = array('CODIGO'=>'fk_asset', 'COSTO'=>'coste','DEPACU_ANT'=>'amount_depr_acum_update','D_REVAL'=>'date_reval_day','M_REVAL'=>'date_reval_month','A_REVAL'=>'date_reval_year','VIDAUTIL'=>'useful_life','RESOLUCION'=>'doc_reval','OBSERV'=>'detail','DEPACU'=>'amount_depr_acum','ACTUA'=>'amount_update','DEPGESTION'=>'amount_depr');
$aCampolabelsigep['llx_assets'] = array(1=> array('CODIGO'=>'ref_ext','CODCONT'=>'codcont','CODBAJA'=>'fk_low','CODAUX'=>'codaux','VIDAUTIL'=>'useful_life','DESCRIP'=>'descrip', 'COSTO'=>'coste','DEPACU'=>'dep_acum','MES'=>'date_month','ANO'=>'date_year','DIA'=>'date_day','CODOFIC'=>'fk_departament','CODRESP'=>'fk_resp','OBSERV'=>'detail','COD_RUBE'=>'cod_rube','ORG_FIN'=>'orgfin','B_REV'=>'status_reval','NOMOFIC'=>'departament_name','NOMRESP'=>'resp_name'),2=>array());

$aCampolabelsigep['llx_assets_low'] = array(1=> array('CODIGO'=>'ref_ext','CODCONT'=>'codcont','CODBAJA'=>'fk_low','CODAUX'=>'codaux','VIDAUTIL'=>'useful_life','DESCRIP'=>'descrip', 'COSTO'=>'coste','DEPACU'=>'dep_acum','MES'=>'date_month','ANO'=>'date_year','DIA'=>'date_day','CODOFIC'=>'fk_departament','CODRESP'=>'fk_resp','OBSERV'=>'detail','COD_RUBE'=>'cod_rube','ORG_FIN'=>'orgfin','B_REV'=>'status_reval','NOMOFIC'=>'departament_name','NOMRESP'=>'resp_name','D_BAJA'=>'baja_day','M_BAJA'=>'baja_month','A_BAJA'=>'baja_year','RESOLUCION'=>'baja_resolution','OBSERV'=>'baja_observation','MOTIVO'=>'baja_motive','ACTUA'=>'baja_amount_act','DEPGESTION'=>'baja_amount_depgestion'),2=>array());
$aCampolabelsigep['llx_c_assets_group'] = array('CODCONT'=>'code', 'NOMBRE'=>'label','VIDAUTIL'=>'useful_life','OBSERV'=>'description','DEPRECIAR'=>'depreciate','ACTUALIZAR'=>'toupdate',);
$aCampolabelsigep['llx_c_low'] = array('CODBAJA'=>'ref', 'DESCBAJA'=>'label',);

//$action = "create";

/*
 * View
 */

$form=new Form($db);
$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Uploadarchive"),$help_url);

if ($action == 'create' || empty($action) && $user->rights->assets->upload->write)
{
	print_fiche_titre($langs->trans("Uploadarchive"));

	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript" language="javascript">';
		print '$(document).ready(function () {
			$("#table").change(function() {
				document.upload.action.value="create";
				document.upload.submit();
			});
		})';
		print '</script>'."\n";
	}

	print '<form action="'.$_SERVER['PHP_SELF'].'" name="upload" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';
	print '<tr><td width="20%">';
	print $langs->trans('Select');
	print '</td>';
	print '<td>';
	print $form->selectarray('table',$aTable,GETPOST('table'),1);
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Selectarchiv');
	print '</td>';
	print '<td>';
	print '<input type="file" name="archivo" size="40">';
	print '</td></tr>';

	if (($table == 'llx_assets'|| $table == 'llx_assets_low') && !$conf->global->ASSETS_INTEGRATION_WITH_DEPARTAMENT_MEMBER)
	{
		print '<tr><td>';
		print $langs->trans('Selectarchiv').' '.$langs->trans('Offices');
		print '</td>';
		print '<td>';
		print '<input type="file" name="archivodep" size="40" required>';
		print '</td></tr>';

		print '<tr><td>';
		print $langs->trans('Selectarchiv').' '.$langs->trans('Responsible');
		print '</td>';
		print '<td>';
		print '<input type="file" name="archivoresp" size="40" required>';
		print '</td></tr>';
	}
	/*
	print '<tr><td>';
	print $langs->trans('Dateformat');
	print '</td>';
	print '<td>';
	print $form->selectarray('seldate',$aDatef,'',1);
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Camposdate');
	print '</td>';
	print '<td>';
	print '<input type="text" name="camposdate" size="50">';
	print '</td></tr>';
	*/
	print '<tr><td>';
	print $langs->trans('Firstrowistitle');
	print '</td>';
	print '<td>';
	print $form->selectyesno('selrow',(GETPOST('selrow')?GETPOST('selrow'):1),1);
	print '</td></tr>';

	if ($table == 'llx_assets' || $table == 'llx_assets_low')
	{
		$aTypeformat = array(1=>$langs->trans('Base de datos exportado a hoja electronica'),2=>$langs->trans('Exportación del SIAF'));
		print '<tr><td>';
		print $langs->trans('Typeformat');
		print '</td>';
		print '<td>';
		print $form->selectarray('typeformat',$aTypeformat,GETPOST('typeformat'),1);
		print '</td></tr>';
	}

	if ($table == 'llx_assets_balance')
	{
		print '<tr><td>';
		print $langs->trans('Lastdatedepreciation');
		print '</td>';
		print '<td>';
		print $form->select_date($date_dep,'dd_',0,0);
		print '</td></tr>';
	}

	/*
	print '<tr><td>';
	print $langs->trans('Separator');
	print '</td>';
	print '<td>';
	print '<input type="text" name="separator" size="2">';
	print '</td></tr>';
	*/
	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Upload").'"></center>';

	print '</form>';
}
else
{
	if ($action == 'exit')
	{
		print_barre_liste($langs->trans("Subida de archivo exitoso"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);
		print '<table class="noborder" width="100%">';
	 //encabezado
		print '<tr class="liste_titre">';

		print '</tr>';
		print '</table>';
	}
	else
	{
		print_barre_liste($langs->trans("Uploadarchive"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="addSave">';
		print '<input type="hidden" name="table" value="'.$table.'">';
		print '<input type="hidden" name="seldate" value="'.$seldate.'">';
		print '<input type="hidden" name="camposdate" value="'.$camposdate.'">';
		print '<input type="hidden" name="separator" value="'.$separator.'">';
		print '<input type="hidden" name="selrow" value="'.$selrow.'">';
		print '<input type="hidden" name="typeformat" value="'.$typeformat.'">';
		print '<input type="hidden" name="date_dep" value="'.$date_dep.'">';

		print '<table class="noborder" width="100%">';
		if ($table == 'llx_assets_low')
			$infotable = $db->DDLInfoTable('llx_assets');
		else
			$infotable = $db->DDLInfoTable($table);
		$aCampo = array();
		$aCampolabel = array();
		//$aCampolabel = $aCampolabelsigep[$table];
		foreach ($infotable AS $i => $dat)
		{
			$aCampo[$i] = $dat[0];
			$aCampolabel[$dat[0]] = $i;
		}
		//agregamos campos adicionales si es accountancy
		if ($table == 'llx_accounting_account')
		{
			$i++;
			$aCampo[$i] = 'cta_normal';
			$aCampolabel['cta_normal'] = $i;
			$i++;
			$aCampo[$i] = 'cta_class';
			$aCampolabel['cta_class'] = $i;
		}
		if ($table == 'llx_contab_seat_det')
		{
			$i++;
			$aCampo[$i] = 'amount_debit';
			$aCampolabel['Amountdebit'] = $i;
			$i++;
			$aCampo[$i] = 'amount_credit';
			$aCampolabel['Amountcredit'] = $i;
		}
		//encabezado
		foreach($aHeaders AS $i => $value)
		{
			$aHeadersOr[trim($value)] = trim($value);
		}
		$aValHeader = array();
		foreach($aHeaderTpl[$table] AS $i => $value)
		{
			if (!$aHeadersOr[trim($value)])
				$aValHeader[$value] = $value;
		}
		print '<tr class="liste_titre">';
		if ($selrow)
		{
			foreach($aHeaders AS $i => $value)
			{
				print_liste_field_titre($langs->trans($value),'fiche.php','','','','');
			}
		}
		print '</tr>';
		//para nombres de campo
		print '<tr class="liste_titre">';
		foreach($aHeaders AS $i => $value)
		{
			$aTemp = explode(',',$value);
			if (count($aTemp)>0)
			{
				$valuenew = trim($aTemp[0]);
				if ($table== 'llx_assets' || $table== 'llx_assets_low')
				{
					print '<td>'.$form->selectarray('fkcampo__'.$i,$aCampo,$aCampolabel[$aCampolabelsigep[$table][$typeformat][$valuenew]],1).'</td>';
				}
				else
					print '<td>'.$form->selectarray('fkcampo__'.$i,$aCampo,$aCampolabel[$aCampolabelsigep[$table][$valuenew]],1).'</td>';
			}
			else
				print '<td>'.$form->selectarray('fkcampo__'.$i,$aCampo,$aCampolabel[$value],1).'</td>';
		}
		print '</tr>';
		//if (!empty($aValHeader))
		//{
		//	$lSave = false;
		//	print "<tr class=\"liste_titre\">";
		//	print '<td>'.$langs->trans('Missingfields').'</td>';
		//	foreach ((array) $aValHeader AS $j => $value)
		//	{
		//		print '<td>'.$value.'</td>';
		//	}
		//	print '</tr>';
		//}
		//else
		//{
		$lSave = true;
		$var=True;
		$c = 0;
		if ($selrow)
		{
			foreach((array) $aDetalle AS $j => $data)
			{
				$var=!$var;
				print "<tr $bc[$var]>";
				$c++;
				$fk_departament = 0;
				$fk_resp = 0;
				foreach($aHeaders AS $i => $keyname)
				{
					$aKey = explode(',',$keyname);
					if (empty($keyname))
						$keyname = "none";
					$phone = $data[$i];
					if (($table == 'llx_assets' || $table == 'llx_assets_low') && !$confg->global->ASSETS_INTEGRATION_WITH_DEPARTAMENT_MEMBER)
					{
						if ($aKey[0]=='CODOFIC') $fk_departament = $phone;
						if ($aKey[0]=='CODRESP') $fk_resp = $phone;
					}

					if ($aKey[0]=='FECHA')
					{
						$aArrData[$c][$i] = $phone;
						$phone = dol_print_date($phone,'day');
					}
					elseif ($aKey[0]=='NOMOFIC')
					{
						//agregamos el valor de la oficina, segun el array aDepartament
						$aArrData[$c][$i] = $aDepartament[$fk_departament];
						$phone = $aDepartament[$fk_departament];
					}
					elseif ($aKey[0]=='NOMRESP')
					{
						//agregamos el valor de la oficina, segun el array aDepartament
						$aArrData[$c][$i] = $aMember[$fk_departament][$fk_resp];
						$phone = $aMember[$fk_departament][$fk_resp];
					}
					else
						$aArrData[$c][$i] = $phone;
					print '<td>'.$phone.'</td>';
				}
				print '</tr>';

			}
		}
		else
		{
			foreach($data AS $key => $dataval)
			{
				$var=!$var;
				print "<tr $bc[$var]>";
				$c++;
				foreach($aHeaders AS $i => $keyname)
				{
					$value = $dataval[$i];
					$aArrData[$c][$i] = $value;
					print '<td>'.$value.'</td>';
				}
				print '</tr>';
			}
		}

		//}
		print '</table>';
		//echo '<pre>';
		//print_r($aArrData);
		//echo '</pre>';

		if($lSave && count($aDetalle)>0)
		{
			$_SESSION['aHeaders'] = $aHeaders;
			$_SESSION['aArrTable'] = $infotable;
			$_SESSION['aArrData'] = $aArrData;
			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';
			print '</form>';
		}
	 //validando el encabezado
	}
}
llxFooter();
$db->close();

function convertdate($aDatef,$selvalue,$date)
{
	$sel = $aDatef[$selvalue];
	switch ($sel)
	{
		case 0:
		list($day,$mes,$anio) = explode('/',$date);
		break;
		case 0:
		list($day,$mes,$anio) = explode('-',$date);
		break;
		case 0:
		list($mes,$day,$anio) = explode('/',$date);
		break;
		case 0:
		list($mes,$day,$anio) = explode('-',$date);
		break;
		case 0:
		list($anio,$mes,$day) = explode('/',$date);
		break;
		case 0:
		list($anio,$mes,$day) = explode('-',$date);
		break;
	}
	$newdate = dol_mktime(12, 0, 0, $mes, $day, $anio);
	return $newdate;
}
?>
