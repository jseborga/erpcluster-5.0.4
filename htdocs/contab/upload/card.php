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

if ($conf->poa->enabled)
	require_once DOL_DOCUMENT_ROOT.'/poa/structure/class/poastructure.class.php';
if ($conf->accounting->enabled)
	require_once DOL_DOCUMENT_ROOT.'/accountancy/class/accountingaccount.class.php';
//else
//	setEventMessages($langs->trans('Debe habilitar el modulo contabilidad avanzada'),null,'warnings');

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

$langs->load("contab");
$langs->load("members");

$action=GETPOST('action');

$id         = GETPOST("rowid");
$rid        = GETPOST("rid");
$fk_period  = GETPOST("fk_period");
$fk_concept = GETPOST("fk_concept");
$docum      = GETPOST('docum');
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
$mesg = '';
if (empty($_SESSION['period_year'])) $_SESSION['period_year'] = date('Y');
$period_year = $_SESSION['period_year'];
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
	$aPeriod = $_SESSION['aPerioddata'];
	$aCampo = array();
	$llaveid = '';
	$llaveref = '';
	$lEntity = false;
	foreach ($aArrTable AS $i => $dat)
	{
		$aCampo[$i] = $dat[0];
	}
	$table = GETPOST('table');
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
		$i++;
		$aCampo[$i] = 'level';
		$aCampolabel['level'] = $i;
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
		case 'llx_accounting_account_aux':
		require_once DOL_DOCUMENT_ROOT.'/accountancy/class/accountingaccount.class.php';
		require_once DOL_DOCUMENT_ROOT.'/contab/class/accountingaccountaux.class.php';
		break;
		case 'llx_accounting_account':
		require_once DOL_DOCUMENT_ROOT.'/accountancy/class/accountingaccount.class.php';
		require_once DOL_DOCUMENT_ROOT.'/contab/class/accountingaccountadd.class.php';
		break;
		case 'llx_contab_seat':
		require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatdetext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/contab/class/contabperiodoext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/contab/class/contabtransaction.class.php';

		$obj = new Contabseatext($db);
		$objdet = new Contabseatdetext($db);
		$objContabtransaction = new Contabtransaction($db);
		//vamos a eliminar información de asientos y asientos det
		//obtenemos todos los ids de la gestion
		$filterseat = " AND t.seat_year = ".$period_year;
		$res = $obj->fetchAll('','',0,0,array(1=>1),'AND',$filterseat);
		$idsSeat = '';
		if ($res>0)
		{
			$lines = $obj->lines;
			$db->begin();
			foreach ($lines AS $j => $line)
			{
				//eliminamos sus hijos
				$resdet = $objdet->delete_block($line->id);
				if ($resdet<=0)
				{
					$error++;
					setEventMessages($objdet->error,$objdet->errors,'errors');
				}
				if (!$error)
				{
					$obj->fetch($line->id);
					$ress = $obj->delete($user);
					if ($ress<=0)
					{
						$error++;
						setEventMessages($obj->error,$obj->errors,'errors');
					}
				}
			}
			if (!$error) $db->commit();
			else $db->rollback();
		}
		//vamos a crear los periodos contables
		if (!empty($aPeriod))
		{
			$obj = new Contabperiodoext($db);
			foreach ($aPeriod AS $year => $data)
			{
				foreach ($data AS $month)
				{
					$filterperiod = " AND t.period_month = ".$month." AND t.period_year = ".$year." AND t.entity = ".$conf->entity;
					$res = $obj->fetchAll('','',0,0,array(1=>1),'AND',$filterperiod,true);
					if ($res == 0)
					{
						//agregamos
						$obj->entity = $conf->entity;
						$obj->period_month = $month;
						$obj->period_year = $year;
						$obj->date_ini = dol_get_first_day($year,$month);
						$obj->date_fin = dol_get_last_day($year,$month);
						$obj->statut = 1;
						$obj->status_af = 1;
						$obj->status_al = 1;
						$obj->status_co = 1;
						$resp = $obj->create ($user);
						if ($resp<=0)
						{
							$error++;
							setEventMessages($obj->error,$obj->errors,'errors');
						}
					}
				}
			}
		}
		break;
		case 'llx_contab_seat_det':
		require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatdetext.class.php';
		break;
		case 'llx_contab_transaction':
		require_once DOL_DOCUMENT_ROOT.'/contab/class/contabtransaction.class.php';
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
			case 'llx_accounting_account_aux':
			if (!$error)
			{
				$objAccounting = new Accountingaccount($db);
				$obj = new Accountingaccountaux($db);
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
				//verificamos el campo fk_accounting_account
				$res = $objAccounting->fetch(null, $obj->fk_accounting_account, 1);
				if ($res>0) $obj->fk_accounting_account = $objAccounting->id;
				else
				{
					$error++;
					setEventMessages($objAccounting->error,$objAccounting->errors,'errors');
				}
				$obj->entity = $conf->entity;
				$obj->datec = $now;
				$obj->datem = $now;
				$obj->fk_user_create = $user->id;
				$obj->fk_user_mod = $user->id;
				$obj->active = 1;
				$obj->status = 1;

				$res = $obj->create($user,1);
				if ($res <=0)
				{
					$error=101;
					setEventMessages($obj->error,$obj->errors,'errors');
				}
			}
			else
				continue;
			if ($error)
				$action = 'create';
			break;


			case 'llx_contab_transaction':
			$lAdd = true;
			//buscamos si existe
			$obj = new Contabtransaction($db);
			foreach ($_POST['campo'] AS $j => $campo)
			{
				if (!$error)
				{
					if (!empty($campo) && $campo == 'ref')
					{
						$ref = $data[$j];
					}
				}
			}
			//buscamos la referencias
			$filter = " AND t.entity = ".$conf->entity." AND t.ref = '".$ref."'";
			$res = $obj->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
			if ($res >0) $lAdd = false;
			if (!$error)
			{
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


				$obj->entity = $conf->entity;
				$obj->datem = $now;
				$obj->fk_user_mod = $user->id;
				$obj->active = 1;
				$obj->status = 1;
				if ($lAdd)
				{
					$obj->datec = $now;
					$obj->fk_user_create = $user->id;
				}
				if ($lAdd) $res = $obj->create($user,1);
				else $res = $obj->update($user);
				if ($res <=0)
				{
					$error=101;
					setEventMessages($obj->error,$obj->errors,'errors');
				}
			}
			else
				continue;
			if ($error)
				$action = 'create';
			break;

			case 'llx_accounting_account':
			$lAdd = true;
			$lAdd_ = true;
			$fk_accounting = 0;
				//vamos a verificar si existe o no la cuenta
			$fk_pcg_version = '';
			$account_number = '';
			foreach ($_POST['campo'] AS $j => $campo)
			{
				if (!$error)
				{
					if (!empty($campo))
					{
						if (trim($campo) == 'fk_pcg_version' && empty($fk_pcg_version))
							$fk_pcg_version = $data[$j];
						if (trim($campo) == 'account_number') $account_number = $data[$j];
					}
				}
			}
			//vamos a buscar en accounting_system
			$sql = " SELECT rowid FROM ".MAIN_DB_PREFIX."accounting_system WHERE pcg_version = '".$fk_pcg_version."'";
			$resql = $db->query($sql);
			if ($resql)
			{
				$objtmp = $db->fetch_object($resql);
				if ($conf->global->CHARTOFACCOUNTS != $objtmp->rowid)
				{
					$error++;
					setEventMessages($langs->trans('El plan de cuentas a subir es diferente al predefinido en el sistema'),null,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages($db->error,null,'errors');
			}


			if (!$error)
			{
				$objAccounting = new Accountingaccount($db);
				$objAccountingadd = new Accountingaccountadd($db);
				//vamos a realizar la busqueda
				$resacc = $objAccounting->fetch(null, $account_number, 1);
				if ($resacc>0 && $objAccounting->fk_pcg_version == $fk_pcg_version)
				{
					$lAdd = false;
					$fk_accounting = $objAccounting->id;
					$resadd = $objAccountingadd->fetch(0,$objAccounting->id);
					if ($resadd >0)
						$lAdd_=false;
				}

				foreach ($_POST['campo'] AS $j => $campo)
				{
					if (!$error)
					{
						$row = $data[$j];
						if (!empty($campo))
						{
							$objAccounting->$campo = $data[$j];
							$objAccountingadd->$campo = $data[$j];
						}
					}
				}

				//agregamos campos extras
				//verificamos si existe father
				if (empty($objAccounting->account_parent))
				{
					//primera forma cuando la cuenta viene con la misma cantidad de digitos
					$len = strlen($objAccounting->account_number);
					$lLoop = true;
					$a = $len;
					$b = 0;
					while ($lLoop)
					{
						if (!empty($aFather))
						{
							$a--;
							$b++;
							$text = '';
							for ($c=1;$c<=$b;$c++) $text.='0';
								$tmp = substr($objAccounting->account_number,0,$a).$text ;
							if ($aFather[$tmp])
							{
								$objAccounting->account_parent = $tmp;
								$lLoop = false;
							}
						}
						else $lLoop = false;
						if ($a<=0) $lLoop = false;
					}
					if (empty($objAccounting->account_parent))
					{
						//segunda forma cuando la cuenta viene con diferentes tamaños
						$len = strlen($objAccounting->account_number);
						$lLoop = true;
						$a = $len;
						while ($lLoop)
						{
							if (!empty($aFather))
							{
								$a--;
								$tmp = substr($objAccounting->account_number,0,$a);
								if ($aFather[$tmp])
								{
									$objAccounting->account_parent = $tmp;
									$lLoop = false;
								}
							}
							else $lLoop = false;
							if ($a<=0) $lLoop = false;
						}
					}
				}
				$objAccounting->entity = $conf->entity;
				$objAccounting->datec = $now;
				$objAccounting->fk_accounting_category = 0;
				$objAccounting->account_category =0;
				$objAccounting->fk_user_author = $user->id;
				$objAccounting->fk_user_modif = $user->id;
				$objAccounting->active = 1;

				if (empty($objAccounting->fk_pcg_version)) continue;
				if ($lAdd)
				{
					$res = $objAccounting->create($user,1);
					$fk_accounting = $res;
				}
				else
				{
					$res = $objAccounting->update($user,1);
				}
				if ($res <=0)
				{
					$error=101;
					setEventMessages($objAccounting->error,$objAccounting->errors,'errors');
				}
				else
				{
					$aFather[$objAccounting->account_number] = $fk_accounting;
					//agregamos a la tabla adicional
					//$objAccountingadd->rowid = 0;
					$objAccountingadd->fk_accounting_account = $fk_accounting;
					$objAccountingadd->level = $objAccounting->level;
					$objAccountingadd->fk_user_mod = $user->id;
					$objAccountingadd->datem = $now;
					$objAccountingadd->tms = $now;
					$objAccountingadd->statut = 1;
					if ($lAdd_)
					{
						$objAccountingadd->fk_user_create = $user->id;
						$objAccountingadd->datec = $now;
					}
					if ($lAdd_) $res = $objAccountingadd->create($user);
					else $res = $objAccountingadd->update($user);
					if ($res <=0)
					{
						$error=102;
						setEventMessages($objAccountingadd->error,$objAccountingadd->errors,'errors');
					}
				}
			}
			else
				continue;
			if ($error)
				$action = 'create';
			break;

			case 'llx_contab_seat':
			if (!$error)
			{
				$obj = new Contabseat($db);
				$object = new Contabseatext($db);
				$lAdd = true;
				foreach ($_POST['campo'] AS $j => $campo)
				{
					if (!$error)
					{
						$row = $data[$j];
						if (!empty($campo))
						{
							//verificamos la llave principal de la importación
							//entity y ref
							if ($campo == 'ref') $ref = $data[$j];
						}
					}
				}
				//buscamos con la llave
				$filterseat = " AND t.entity = ".$conf->entity;
				$filterseat.= " AND t.ref = '".$ref."'";
				$filterseat.= " AND t.seat_year = ".$period_year;
				$res = $obj->fetchAll('','',0,0,array(1=>1),'AND',$filterseat,true);
				if ($res<0)
				{
					$error++;
					setEventMessages($obj->error,$obj->errors,'errors');
				}
				if ($res==1) $lAdd= false;

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

				$aDate = dol_getdate($obj->date_seat);
				$obj->spending+=0;
				$obj->resource+=0;
				$obj->accountant+=0;
				$obj->seat_month = $aDate['mon'];
				$obj->seat_year = $aDate['year'];
				//verificamos el tipo de asiento segun el tipo de transaccion
				if (empty($obj->type_seat) || is_null($obj->type_seat))
				{
					if ($obj->codtr)
					{
						$restran = $objContabtransaction->fetch(0,$obj->codtr);
						if ($restran>0)
						{
							if ($objContabtransaction->type_seat>0)
								$obj->type_seat = $objContabtransaction->type_seat;
						}
						else
							$obj->type_seat = 3;
					}
				}
				if ($lAdd)
				{
					$obj->lote   = "(PROV)";
					$obj->sblote = "(PROV)";
					$obj->doc    = "(PROV)";

					$obj->currency = $conf->currency;
					//$obj->type_seat = 3;
					$obj->type_numeric = rand(1,99);
					$obj->sequential = '0';
					$obj->manual = '1';
					$obj->history = html_entity_decode($obj->history);
					$obj->entity = $conf->entity;
					$obj->datec = $now;
					$obj->fk_user_create = $user->id;
				}
				$obj->datem = $now;
				$obj->fk_accouting_category = 0;

				$obj->fk_user_mod = $user->id;
				$obj->status = 1;
				if ($lAdd)
				{
					$fk_seat = $obj->create($user);
					$res = $fk_seat;
				}
				else
				{
					$fk_seat = $obj->id;
					$res = $obj->update($user);
				}
				//echo '<hr>res y fk '.$res.' '.$fk_seat.' '.$obj->ref;
				if ($res <=0)
				{
					$error=101;
					setEventMessages($obj->error,$obj->errors,'errors');
				}
				//validamos
				if (!$error && $fk_seat>0)
				{
					$res = $object->fetch($fk_seat);
					$object->get_next_typenumeric($object->type_seat,$object->seat_month,$object->seat_year);
					$ref = substr($object->lote, 1, 4);
					if ($ref == 'PROV')
					{
						list($numlote,$numsblote,$numdoc) = $object->getNextNumRef($object);
					}
					else
					{
						$numlote   = $object->lote;
						$numsblote = $object->sblote;
						$numdoc    = $object->doc;
					}

					//cambiando a validado
					$object->ref_ext = $object->ref;
					$object->state = 1;
					$object->lote = $numlote;
					$object->sblote = $numsblote;
					$object->doc = $object->ref;
					$object->sequential = $object->sequential;

					if (empty($object->lote))
					{
						$error=102;
						setEventMessages($langs->trans('No esta activo la numeracion de asientos, revise'),null,'errors');
					}
					if (!$error)
					{
						$res = $object->update($user);
						if ($res <=0)
						{
							$error=103;
							setEventMessages($object->error,$object->errors,'errors');
						}
					}
				}
			}
			else
				continue;
			if ($error)
			{
				//echo '<hr>'.$error;
				$action = 'create';
				//exit;
			}
			break;

			case 'llx_contab_seat_det':
			if (!$error)
			{
				$objSeat = new Contabseatext($db);
				$obj = new Contabseatdetext($db);
				$object = new Contabseatdetext($db);
				$obj->ctadebito =0;
				$obj->ctacredito =0;
				$debit_account = '';
				$credit_account = '';
				$lAdd = true;
				foreach ($_POST['campo'] AS $j => $campo)
				{
					if ($campo == 'fk_contab_seat') $fk_seat = $data[$j];
					if ($campo == 'debit_account') $debit_account = $data[$j];
					if ($campo == 'credit_account') $credit_account = $data[$j];
				}
				//vamos a realizar la busqueda
				if (!empty($debit_account)) $filter = " AND t.debit_account = '".$debit_account."' AND t.fk_contab_seat = ".$fk_seat;
				if (!empty($credit_account)) $filter = " AND t.credit_account = '".$credit_account."' AND t.fk_contab_seat = ".$fk_seat;
				$res = $obj->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
				if ($res==1) $lAdd = false;
				foreach ($_POST['campo'] AS $j => $campo)
				{
					if (!$error)
					{
						$row = $data[$j];
						if (!empty($campo))
						{
							if ($campo == 'fk_contab_seat')
							{
								//buscamos el asiento principal
								if ($data[$j]>0)
								{
									//buscar por la referencia y la gestion
									$res = $objSeat->fetch(0,$data[$j],$period_year);
									if ($res == 1)
									{
										//verificamos que sea de la gestion actual
										if ($objSeat->seat_year != $period_year)
										{
											$error=201;
											setEventMessages($langs->trans('Theaccountingentriesdonotcorrespondtotheselectedmanagement'),null,'errors');
										}
										$obj->$campo = $objSeat->id;
									}
									else
									{
										$error=201;
										setEventMessages($objSeat->error,$objSeat->errors,'errors');
									}
								}
								else
									continue;
							}
							elseif ($campo == 'amount')
							{
								$obj->amount = $data[$j];
								if(!empty($data[$j]))
								{
									$obj->ctadebito = 1;
									$obj->ctacredito = 2;
								}
							}
							elseif ($campo == 'value02')
							{
								if ($obj->ctadebito ==0)
								{
									$obj->amount = $data[$j];
									if(!empty($data[$j]))
									{
										$obj->ctadebito = 2;
										$obj->ctacredito = 1;
									}
								}
							}
							else
								$obj->$campo = $data[$j];
						}
					}
				}
				$aDate = dol_getdate($obj->date_seat);
				if ($obj->ctacredito==1)
				{
					$obj->credit_account = $obj->debit_account;
					$obj->debit_account = '';
					$obj->ref_ext_auxc = $obj->ref_ext_auxd;
					$obj->ref_ext_auxd = '';
					$obj->credit_detail = $objSeat->label;
				}
				//elseif($obj->ctadebito==1)
				//{
				//	$obj->debit_detail = $objSeat->label;
				//}
				$obj->fk_parent_auxd+=0;
				$obj->fk_parent_auxc+=0;
				$obj->history = dol_trunc($objSeat->label,235);
				$obj->fk_standard_seat=0;
				$obj->type_seat=$objSeat->type_seat;
				$obj->routines = 'N';
				$obj->date_rate = $objSeat->date_seat;
				$obj->rate   = 0;
				$obj->fk_user_create = $user->id;
				$obj->fk_user_mod = $user->id;
				$obj->datec = dol_now();
				$obj->datem = dol_now();
				$obj->status = 1;
				if (!empty($obj->fk_contab_seat))
				{
					if ($lAdd) $res = $obj->create($user);
					else $res = $obj->update($user);
					if ($res <=0)
					{
						$error=202;
						setEventMessages($obj->error,$obj->errors,'errors');
					}
				}
			}
			else
				continue;
			if ($error)
			{
				$action = 'create';
			}
			break;
		}
	}

	//exit;
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

// Add
if ($action == 'add')
{
	$table = GETPOST('table');
	$selrow = GETPOST('selrow');
	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];

	$tempdir = "tmp/";
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
	elseif ($nTipeSheet =='dbf'|| $nTipeSheet =='DBF')
	{
		$type = 'dbf';
		//$objReader = PHPExcel_IOFactory::createReader('Excel5');
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
	elseif($type == 'dbf')
	{
		$dbffile = $tempdir.$nombre_archivo;
		$conex       = dbase_open($dbffile, 0);
		if($conex)
		{
			$arrData = array();
			$total_registros = dbase_numrecords($conex);
			for ($i = 1; $i <= $total_registros; $i++)
			{
				$arrData[] = dbase_get_record($conex,$i);
			}
		}
		else
		{
			echo 'No se pudo acceder al fichero dbf';
		}


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

$aHeaderTpl['llx_contab_seat'] = array('ref' => 'ref',
	'date_seat' => 'date_seat',
	'history' => 'history',
	'debit_total'=>'debit_total',
	'credit_total' => 'credit_total',
);
$aHeaderTpl['llx_contab_seat_det'] = array('debit_account' => 'debit_account',
	'amount' => 'amount',
);
$aHeaderTpl['llx_contab_transaction'] = array('ref' => 'ref',
	'label' => 'label','type'=>'type'
);

$aHeaderTpl['llx_accounting_account'] = array('fk_pcg_version' => 'fk_pcg_version',
	'pcg_type' => 'pcg_type',
	'pcg_subtype' => 'pcg_subtype',
	'account_number'=>'account_number',
	'account_parent' => 'account_parent',
	'label'=>'label',
	'fk_accouting_category'=>'fk_accouting_category',
);
$aHeaderTpl['llx_accounting_account_aux'] = array('ref' => 'ref',
	'label' => 'label');
$aHeaderTpl['llx_product'] = array('ref' => 'ref',
	'label' => 'label');
$aHeaderTpl['llx_categorie'] = array('label' => 'label',
	'description' => 'description',
	'code_parent' => 'code_parent');
$aHeaderTpl['llx_categorie_product'] = array('code_product' => 'code_product',
	'description' => 'description',
	'code_categorie' => 'code_categorie');
$aHeaderTpl['llx_commande'] = array('ref' => 'ref',
	'fk_soc' => 'fk_soc',
	'date_commande' => 'date_commande');
$aHeaderTpl['llx_commandedet'] = array('fk_commande' => 'fk_commande',
	'fk_product' => 'fk_product',
	'qty' => 'qty');

$aHeaderTpl['llx_c_departements'] = array('code_departement' => 'code_departement',
	'fk_region' => 'fk_region',
	'nom' => 'nom');

$aHeaderTpl['llx_c_partida'] = array('gestion'=>'gestion',
	'code'   => 'code',
	'label'  => 'label',
	'active' => 'active');
$aHeaderTpl['llx_poa_poa'] = array('gestion'=>'gestion',
	'fk_structure' =>'fk_structure',
	'ref' =>'ref',
	'label'=>'label',
	'pseudonym' =>'pseudonym',
	'partida'=>'partida',
	'amount'=>'amount',
	'classification'=>'classification',
	'source_verification'=>'source_verification',
	'unit'=>'unit',
	'responsible'=>'responsible',
	'm_jan'=>'m_jan',
	'm_feb'=>'m_feb',
	'm_mar'=>'m_mar',
	'm_apr'=>'m_apr',
	'm_may'=>'m_may',
	'm_jun'=>'m_jun',
	'm_jul'=>'m_jul',
	'm_aug'=>'m_aug',
	'm_sep'=>'m_sep',
	'm_oct'=>'m_oct',
	'm_nov'=>'m_nov',
	'm_dec'=>'m_dec',
	'p_jan'=>'p_jan',
	'p_feb'=>'p_feb',
	'p_mar'=>'p_mar',
	'p_apr'=>'p_apr',
	'p_may'=>'p_may',
	'p_jun'=>'p_jun',
	'p_jul'=>'p_jul',
	'p_aug'=>'p_aug',
	'p_sep'=>'p_sep',
	'p_oct'=>'p_oct',
	'p_nov'=>'p_nov',
	'p_dec'=>'p_dec',
	'fk_area'=>'fk_area',
	'weighting'=>'weighting',
	'fk_poa_reformulated'=>'fk_poa_reformulated',
	'version'=>'version',
	'statut'=>'statut',
	'statut_ref'=>'statut_ref',
	'active'=>'active',
);

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
	'llx_accounting_account'=>$langs->trans('Accountingaccount'),
	'llx_contab_seat'=>$langs->trans('Seating'),
	'llx_contab_seat_det'=>$langs->trans('Detailseats'),
	'llx_contab_transaction'=>$langs->trans('Typetransaction'),
	'llx_accounting_account_aux'=>$langs->trans('Auxiliaryaccounts'),
);

$aCampolabelsigep['llx_accounting_account'] = array('fk_pcg_version'=>'fk_pcg_version', 'pcg_type'=>'pcg_type','pcg_subtype'=>'pcg_subtype','account_parent'=>'account_parent','account_number'=>'account_number','Nombre'=>'label','label'=>'label','cta_normal'=>'cta_normal','cta_class'=>'cta_class','NIVEL'=>'level',);
$aCampolabelsigep['llx_contab_seat'] = array('UNIDAD'=>'entity', 'CBTE'=>'ref','FECHA'=>'date_seat','CODTR'=>'codtr','DOCRES'=>'document_backing','CBTER'=>'cbter','CODTR1'=>'codtrone','TIPO'=>'cbttipo','BENE'=>'beneficiary','GLOSA'=>'history','GAS'=>'spending','REC'=>'resource','CON'=>'accountant','TDBB'=>'debit_total','THBB'=>'credit_total',);
$aCampolabelsigep['llx_contab_seat_det'] = array('UNIDAD'=>'entity', 'CBTE'=>'fk_contab_seat','LINEA'=>'sequence','FECHA'=>'datec','CODTR'=>'codtr','CODTR1'=>'codtr1','GARECO'=>'gareco','CTA'=>'debit_account','AUX'=>'ref_ext_auxd','OEC'=>'oec','FF'=>'fuefin','OF'=>'otherfin','DBB'=>'amount','HBB'=>'value02',);
$aCampolabelsigep['llx_contab_transaction'] = array('CODTR'=>'ref', 'DES'=>'label','T_COD'=>'type','BITS'=>'bits',);
$aCampolabelsigep['llx_accounting_account_aux'] = array('CTA'=>'fk_accounting_account', 'AUX'=>'ref','PADREA'=>'code_father','RAZON'=>'label',);

//$action = "create";

/*
 * View
 */

$form=new Form($db);
$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementseats"),$help_url);

if ($action == 'create' || empty($action) && $user->rights->salary->crearuser)
{
	print_fiche_titre($langs->trans("Upload archive").' : '.$langs->trans('Gestion').' : '.$period_year);
	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';


	print '<table class="border" width="100%">';
	print '<tr><td width="20%">';
	print $langs->trans('Table');
	print '</td>';
	print '<td>';
	print $form->selectarray('table',$aTable,'',1);
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Selectarchiv');
	print '</td>';
	print '<td>';
	print '<input type="file" name="archivo" size="40">';
	print '</td></tr>';

	/*
	print '<tr><td>';
	print $langs->trans('Dateformat');
	print '</td>';
	print '<td>';
	print $form->selectarray('seldate',$aDatef,'',1);
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Campos date');
	print '</td>';
	print '<td>';
	print '<input type="text" name="camposdate" size="50">';
	print '</td></tr>';
	*/
	print '<tr><td>';
	print $langs->trans('Primera fila es titulo');
	print '</td>';
	print '<td>';
	print $form->selectyesno('selrow',(GETPOST('selrow')?GETPOST('selrow'):1),1);
	print '</td></tr>';

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
		print_barre_liste($langs->trans("Uploadarchive").' '.$langs->trans('Gestion').' : '.$period_year, $page, "fiche.php", "", $sortfield, $sortorder,'',$num);
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="addSave">';
		print '<input type="hidden" name="table" value="'.$table.'">';
		print '<input type="hidden" name="seldate" value="'.$seldate.'">';
		print '<input type="hidden" name="camposdate" value="'.$camposdate.'">';
		print '<input type="hidden" name="separator" value="'.$separator.'">';
		print '<input type="hidden" name="selrow" value="'.$selrow.'">';

		print '<table class="noborder" width="100%">';

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
			$i++;
			$aCampo[$i] = 'level';
			$aCampolabel['level'] = $i;
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
		$lGestion = true;
		$var=True;
		$aPeriod = array();
		$c = 0;
		if ($selrow)
		{
			foreach($aDetalle AS $j => $data)
			{
				$var=!$var;
				print "<tr $bc[$var]>";
				$c++;
				foreach($aHeaders AS $i => $keyname)
				{
					$aKey = explode(',',$keyname);
					if (empty($keyname))
						$keyname = "none";
					$phone = $data[$i];
					if ($aKey[0]=='FECHA')
					{
						$aArrData[$c][$i] = $phone;
						$aDateseat = dol_getdate($phone);
						if ($aDateseat['year'] != $period_year)
						{
							$lSave = false;
							$lGestion = false;
						}
						$phone = dol_print_date($phone,'day');
						if ($table == 'llx_contab_seat')
						{
							//armamos los periodos contables
							$aPeriod[$aDateseat['year']][$aDateseat['mon']] = $aDateseat['mon'];
						}
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
	//  echo '<pre>';
	//  print_r($aArrData);
	//  echo '</pre>';
		if (!$lGestion)
		{
			setEventMessages($langs->trans('Theaccountingentriesdonotcorrespondtotheselectedmanagement'),null,'errors');
		}
		if($lSave)
		{
			$_SESSION['aHeaders'] = $aHeaders;
			$_SESSION['aArrTable'] = $infotable;
			$_SESSION['aArrData'] = $aArrData;
			$_SESSION['aPerioddata'] = $aPeriod;
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
