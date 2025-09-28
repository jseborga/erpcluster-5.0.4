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

if ($conf->poa->enabled)
	require_once DOL_DOCUMENT_ROOT.'/poa/structure/class/poastructure.class.php';
if ($conf->accounting->enabled)
	require_once DOL_DOCUMENT_ROOT.'/accountancy/class/accountingaccount.class.php';
else
	setEventMessages($langs->trans('Debe habilitar el modulo contabilidad avanzada'),null,'warning');

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

/*
 * Actions
 */

// AddSave
if ($action == 'addSave')
{
	$error = 0;
	$aArrData   = $_SESSION['aArrData'];
	$aArrTable = $_SESSION['aArrTable'];
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
		$aCampo[$i] = 'Normal';
		$aCampolabel['Normal'] = $i;
		$i++;
		$aCampo[$i] = 'Classe';
		$aCampolabel['Classe'] = $i;
	}


	$selrow = GETPOST('selrow');
	foreach ($_POST AS $i => $value)
	{
		$aPost = explode('_',$i);
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
		case MAIN_DB_PREFIX.'product':
		require_once DOL_DOCUMENT_ROOT.'/upload/class/productext.class.php';
		$obj = new Productext($db);
		break;
		case 'llx_categorie';
		require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
		$obj = new Categorie($db);
		break;
		case 'llx_categorie_product':
		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		$objp = new Product($db);
		require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
		$objc = new Categorie($db);
		break;
		case 'llx_commande':
		require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
		$objc = new Commande($db);
		$objd = new Orderline($db);
		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		$objp = new Product($db);
		break;
		case 'llx_commandedet':
		require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
		$objc = new Commande($db);
		$objd = new Orderline($db);
		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		$objp = new Product($db);
		break;
		case 'llx_poa_poa':
		require_once DOL_DOCUMENT_ROOT.'/poa/poa/class/poapoa.class.php';
		$objp = new Poapoa($db);
		break;
		case 'llx_accounting_account':
		require_once DOL_DOCUMENT_ROOT.'/accountancy/class/accountingaccount.class.php';
		require_once DOL_DOCUMENT_ROOT.'/contab/class/accountingaccountadd.class.php';
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
	foreach ((array) $aArrData AS $i => $data)
	{
		$Acampo = GETPOST('campo');
		switch ($table)
		{
			case 'llx_accounting_account':
			if (!$error)
			{
				$objAccounting = new Accountingaccount($db);
				$objAccountingadd = new Accountingaccountadd($db);
				foreach ($aCampo AS $j => $campo)
				{
					if (!$error)
					{
						$row = $data[$j];
						foreach ((array) $row AS $tablecampo => $value)
						{
							$objAccounting->$tablecampo = $value;
							$objAccountingadd->$tablecampo = $value;
						}
					}
				}
				//agregamos campos extras
				//verificamos si existe father
				$len = strlen($objAccounting->account_number);
				$lLoop = true;
				$a = $len;
				while ($lLoop)
				{
					if (!empty($aFather))
					{
						$a--;
						if ($aFather[substr($objAccounting->account_number,0,$a)])
						{
							$objAccounting->account_parent = $aFather[substr($objAccounting->account_number,0,$a)];
							$lLoop = false;
						}
					}
					else $lLoop = false;
					if ($a<=0) $lLoop = false;
				}
				$objAccounting->entity = $conf->entity;
				$objAccounting->datec = $now;
				$objAccounting->fk_accouting_category = 0;
				$objAccounting->fk_user_author = $user->id;
				$objAccounting->fk_user_modif = $user->id;
				$objAccounting->active = 1;
				if (empty($objAccounting->fk_pcg_version)) continue;
				$res = $objAccounting->create($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objAccounting->error,$objAccounting->errors,'errors');
				}
				else
				{
					$aFather[$objAccounting->account_number] = $res;
					//agregamos a la tabla adicional
					$objAccountingadd->rowid = 0;
					$objAccountingadd->fk_accounting_account = $res;
					$objAccountingadd->fk_user_create = $user->id;
					$objAccountingadd->fk_user_mod = $user->id;
					$objAccountingadd->datec = $now;
					$objAccountingadd->datem = $now;
					$objAccountingadd->tms = $now;
					$objAccountingadd->statut = 1;
					$res = $objAccountingadd->create($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($objAccountingadd->error,$objAccountingadd->errors,'errors');
					}
				}
			}
			else
				continue;
			if ($error) $action = 'create';
			break;

			case 'llx_poa_poa':
	    	//recuperamos el fk_structure
			$data['ref'] = str_replace('.','',$data['ref']);

	    	//buscamos la refernecia en estructure
			$objstr = new Poastructure($db);
			if($objstr->fetch_sigla($data['ref'],$data['gestion']))
			{
				if ($objstr->sigla == $data['ref'] && $objstr->gestion == $data['gestion'])
					$data['fk_structure'] = $objstr->id;
				else
				{
					$error++;
					print '<td>'.$langs->trans('Error').' '.$data['ref'].'-'.$data['gestion'].'</td>';
				}
			}
			else
				$error++;
			echo '<hr>fk_st '.$data['fk_structure'].' ref '.$data['ref'].' gestion '.$data['gestion'];
	    //insertamos
			$objnew = new Poapoa($db);
			$objnew->gestion = $data['gestion'];
			$objnew->fk_structure = $data['fk_structure'];
			$objnew->entity = $conf->entity;
			$objnew->ref = $data['ref'];
			$objnew->label = $data['label'];
			$objnew->pseudonym = $data['pseudonym'];
			$objnew->partida = $data['partida'];
			$objnew->amount = $data['amount'];
			$objnew->classification = $data['classificiation'];
			$objnew->source_verification = $data['source_verification'];
			$objnew->unit = $data['unit'];
			$objnew->responsible = $data['responsible'];
			$objnew->m_jan = (!empty($data['m_jan'])?$data['m_jan']:'NULL');
			$objnew->m_feb = (!empty($data['m_feb'])?$data['m_feb']:'NULL');
			$objnew->m_mar = (!empty($data['m_mar'])?$data['m_mar']:'NULL');
			$objnew->m_apr = (!empty($data['m_apr'])?$data['m_apr']:'NULL');
			$objnew->m_may = (!empty($data['m_may'])?$data['m_may']:'NULL');
			$objnew->m_jun = (!empty($data['m_jun'])?$data['m_jun']:'NULL');
			$objnew->m_jul = (!empty($data['m_jul'])?$data['m_jul']:'NULL');
			$objnew->m_aug = (!empty($data['m_aug'])?$data['m_aug']:'NULL');
			$objnew->m_sep = (!empty($data['m_sep'])?$data['m_sep']:'NULL');
			$objnew->m_oct = (!empty($data['m_oct'])?$data['m_oct']:'NULL');
			$objnew->m_nov = (!empty($data['m_nov'])?$data['m_nov']:'NULL');
			$objnew->m_dec = (!empty($data['m_dec'])?$data['m_dec']:'NULL');

			$objnew->p_jan = (!empty($data['p_jan'])?$data['p_jan']:'NULL');
			$objnew->p_feb = (!empty($data['p_feb'])?$data['p_feb']:'NULL');
			$objnew->p_mar = (!empty($data['p_mar'])?$data['p_mar']:'NULL');
			$objnew->p_apr = (!empty($data['p_apr'])?$data['p_apr']:'NULL');
			$objnew->p_may = (!empty($data['p_may'])?$data['p_may']:'NULL');
			$objnew->p_jun = (!empty($data['p_jun'])?$data['p_jun']:'NULL');
			$objnew->p_jul = (!empty($data['p_jul'])?$data['p_jul']:'NULL');
			$objnew->p_aug = (!empty($data['p_aug'])?$data['p_aug']:'NULL');
			$objnew->p_sep = (!empty($data['p_sep'])?$data['p_sep']:'NULL');
			$objnew->p_oct = (!empty($data['p_oct'])?$data['p_oct']:'NULL');
			$objnew->p_nov = (!empty($data['p_nov'])?$data['p_nov']:'NULL');
			$objnew->p_dec = (!empty($data['p_dec'])?$data['p_dec']:'NULL');
			$objnew->fk_area = $data['fk_area'];
			$objnew->weighting = $data['weighting'];
			$objnew->fk_poa_reformulated = $data['fk_poa_reformulated'];
			$objnew->version = $data['version'];
			$objnew->statut = $data['statut'];
			$objnew->statut_ref = $data['statut_ref'];
			$objnew->active = $data['active'];
			$res = $objnew->create($user);
			if ($res <=0)
				$error++;
			break;
			case 'llx_product':
			//llave foranea
			require_once DOL_DOCUMENT_ROOT.'/upload/class/cunits.class.php';
			//las llaves son rowid y ref con entity
			    //buscamos el codigo
			//echo '<hr>id '.$llaveid.' ref '.$llaveref;
			if ($selrow)
				$res = $obj->fetch('',$data['ref']);
			else
				$res = $obj->fetch($data[$llaveid],$data[$llaveref]);
			if ($res>0)
			{
				$campoarray = $_POST['campo'];
				$price_base_type = '';
				$price_ttc = 0;
				$price_ht = 0;
				$tva_tx = 14.9427;
				$tva = 0;
				foreach ($data AS $j => $value)
				{
						//echo '<hr>j '.$j.' value '.$value;
					if ($campoarray[$j])
					{
						$c_campo = $campoarray[$j];
						if ($c_campo == 'fk_unit')
						{
								//dependiendo del tipo de dato
							if (!is_numeric($data[$j]))
							{
								if (is_string($data[$j]))
								{
										//buscamos como referencia
									$unit = new Cunits($db);
									$res = $unit->fetch(0,$data[$j]);
									if ($res > 0)
									{
										$data[$j] = $unit->id;
									}
									elseif ($res == 0)
									{
											//insertamos
										$unit->code = $data[$j];
										$unit->label = $data[$j];
										$unit->short_label = $data[$j];
										$unit->active = 1;
										$resu = $unit->create($user);
										if ($resu >0) $data[$j] = $resu;
										else
										{
											$data[$j] = 0;
												//$error++;
											setEventMessages($unit->error.' '.$langs->trans('Error de registro en unidades con ').' '.$data[$j],$unit->errors,'errors');
										}
									}
								}
							}
						}
						if ($c_campo == 'price_ttc') $price_ttc = $data[$j];
						if ($c_campo == 'price_ht') $price_ht = $data[$j];
						if ($c_campo == 'price_base_type') $price_base_type = $data[$j];
						if ($c_campo == 'tva_tx') $tva_tx = $data[$j];
						$obj->$c_campo = $data[$j];
					}
				}
				if (empty($price_base_type))
				{
					if ($price_ttc >0 && $price_ht <=0)
						$price_base_type = 'TTC';
					if ($price_ttc <=0 && $price_ht >0)
						$price_base_type = 'HT';
				}
					//$obj->$c_campo = $data[$j];
					//if (!isset($objnew->tosell)) $objnew->tosell = 1;
					//if (!isset($objnew->tobuy)) $objnew->tobuy = 1;
					//if (!isset($objnew->status)) $objnew->status = 1;
					//if (!isset($objnew->status_buy)) $objnew->status_buy = 1;
				if ($obj->tosell) $obj->status = 1;
				if ($obj->tobuy) $obj->status_buy = 1;
				$obj->price_ttc = $price_ttc;
				$obj->price_ht = $price_ht;
				$obj->price_base_type = $price_base_type;
				$obj->tva_tx = $tva_tx;
				$obj->fk_user_author = $user->id;
					//echo '<pre>';
					//print_r($objnew);
					//echo '</pre>';
					//echo '<hr>'.
					//$res = $obj->update($user);
				$res = $obj->updateadd($obj->id, $user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($obj->error,$obj->errors,'errors');
				}
			}
			elseif($res == 0)
			{
					//insertamos
				if ($selrow)
				{
					$objnew = new Product($db);
					$objnew->ref = $data['ref'];
					$objnew->label = $data['label'];
					$objnew->libelle = $data['label'];
					$objnew->tosell = 1;
					$objnew->tobuy = 1;
					$objnew->status = 1;
					$objnew->status_buy = 1;
					$objnew->fk_user_author = $user->id;

					$res = $objnew->create($user);
				}
				else
				{
					$campoarray = $_POST['campo'];
					$objnew = new Product($db);
					$price_base_type = '';
					$price_ttc = 0;
					$price_ht = 0;
					$tva_tx = 14.9427;
					$tva = 0;
					foreach ($data AS $j => $value)
					{
						//echo '<hr>j '.$j.' value '.$value;
						if ($campoarray[$j])
						{
							$c_campo = $campoarray[$j];
							if ($c_campo == 'fk_unit')
							{
								//dependiendo del tipo de dato
								if (!is_numeric($data[$j]))
								{
									if (is_string($data[$j]))
									{
										//buscamos como referencia
										$unit = new Cunits($db);
										$res = $unit->fetch(0,$data[$j]);
										if ($res > 0)
										{
											$data[$j] = $unit->id;
										}
										elseif ($res == 0)
										{
											//insertamos
											$unit->code = $data[$j];
											$unit->label = $data[$j];
											$unit->short_label = $data[$j];
											$unit->active = 1;
											$resu = $unit->create($user);
											if ($resu >0) $data[$j] = $resu;
											else
											{
												$data[$j] = 0;
												//$error++;
												setEventMessages($unit->error.' '.$langs->trans('Error de registro en unidades con ').' '.$data[$j],$unit->errors,'errors');
											}
										}
									}
								}
							}
							if ($c_campo == 'price_ttc') $price_ttc = $data[$j];
							if ($c_campo == 'price_ht') $price_ht = $data[$j];
							if ($c_campo == 'price_base_type') $price_base_type = $data[$j];
							if ($c_campo == 'tva_tx') $tva_tx = $data[$j];
							$objnew->$c_campo = $data[$j];
						}
					}
					if (empty($price_base_type))
					{
						if ($price_ttc >0 && $price_ht <=0)
							$price_base_type = 'TTC';
						if ($price_ttc <=0 && $price_ht >0)
							$price_base_type = 'HT';
					}
					//if (!isset($objnew->tossell)) $objnew->tosell = 1;
					//if (!isset($objnew->tobuy)) $objnew->tobuy = 1;
					if ($objnew->tosell) $objnew->status = 1;
					if ($objnew->tobuy) $objnew->status_buy = 1;
					//if (!isset($objnew->status)) $objnew->status = 1;
					//if (!isset($objnew->status_buy)) $objnew->status_buy = 1;
					$objnew->price_ttc = $price_ttc;
					$objnew->price_ht = $price_ht;
					$objnew->price_base_type = $price_base_type;
					$objnew->tva_tx = $tva_tx;
					$objnew->fk_user_author = $user->id;
					//echo '<pre>';
					//print_r($objnew);
					//echo '</pre>';
					//echo '<hr>'.
					$res = $objnew->create($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($objnew->error,$objnew->errors,'errors');
					}
				}
			}

			break;

			case 'llx_productorig':
	    //buscamos el codigo
			$res = $obj->fetch('',$data['ref']);
			if ($res>=0)
			{
				if ($obj->ref == $data['ref'])
				{
		  //nada, se puede actualizar
				}
				else
				{
		  //insertamos
					$objnew = new Product($db);
					$objnew->ref = $data['ref'];
					$objnew->label = $data['label'];
					$objnew->libelle = $data['label'];
					$objnew->tosell = 1;
					$objnew->tobuy = 1;
					$objnew->status = 1;
					$objnew->status_buy = 1;
					$objnew->fk_user_author = $user->id;

					$res = $objnew->create($user);
				}
			}
			break;
			case 'llx_categorie':
	    //buscamos el codigo
			$res = $obj->fetch('',$data['label']);
			if ($res>=0)
			{
				if ($obj->label == $data['label'])
				{
		  //nada, se puede actualizar
		  //buscamos si tiene parent
					if (!empty($data['code_parent']))
					{
						$obj1 = new Categorie($db);
						$res1 = $obj1->fetch('',$data['code_parent']);
						if ($res1 && $obj1->label == $data['code_parent'])
							$obj->fk_parent = $obj1->id;
						else
							$obj->fk_parent = 0;
					}
					$obj->label = $data['label'];
					$obj->description = $data['description'];
					$obj->update($user);
				}
				else
				{
		  //insertamos nuevo
					$objnew = new Categorie($db);

		  //buscamos si tiene parent
					if (!empty($data['code_parent']))
					{
						$res1 = $obj->fetch('',$data['code_parent']);
		      //print_r($obj);exit;
						if ($res1 && $obj->label == $data['code_parent'])
							$objnew->fk_parent = $obj->id;
					}
					$objnew->label = $data['label'];
					$objnew->description = $data['description'];
					$objnew->type = 0;
					$objnew->entity = $conf->entity;
		  //print_r($objnew);exit;
					$res = $objnew->create($user);
				}
			}
			break;

			case 'llx_categorie_product':
	    //buscamos el producto
			$res = $objp->fetch('',$data['code_product']);
			if ($res==1)
			{
				if ($objp->ref == $data['code_product'])
				{
		  //buscamos la categoria
					$res = $objc->fetch('',$data['code_categorie']);
					if ($res==1)
					{
						if ($objc->label == $data['code_categorie'])
						{
							$fk_categorie = $objc->id;
							$objc->add_type($objp,'product');
						}
					}
				}
			}

			break;
			case 'llx_c_departements':
	    //buscamos el pais
			$sql = " SELECT rowid FROM ".MAIN_DB_PREFIX."c_pays";
			$sql.= " WHERE code = '".$data['cod_pais']."'";
			$res = $db->query($sql);
			if ($res)
			{
				$obj = $db->fetch_object($res);
				if (!empty($obj->rowid))
				{
					$idPays = $obj->rowid;
		    //buscamos la region
					$sql = " SELECT rowid FROM ".MAIN_DB_PREFIX."c_regions";
					$sql.= " WHERE code_region = ".$data['fk_region'];
					$sql.=" AND fk_pays = ".$idPays;
					$res = $db->query($sql);
					if ($res)
					{
						$obj = $db->fetch_object($res);
						if (empty($obj->rowid))
						{
			    //registramos la region
							$tableregion = 'llx_c_regions';
							$db->begin();
							$sql = " INSERT INTO ".$tableregion."(rowid,code_region,fk_pays,nom,active,cheflieu,tncc)";
							$sql.= " VALUES ( ";
							$sql.= $data['fk_region'].",";
							$sql.= "'".$db->escape($data['fk_region'])."',";
							$sql.= $idPays.",";
							$sql.= "'".$db->escape($data['dpto'])."',";
							$sql.= "1,";
							$sql.= "'',0";
							$sql.=")";
							$resql = $db->query($sql);
							$idRegion = $data['fk_regions'];
							if ($resql)
								$db->commit();
							else
								$db->rollback();
						}
						else
							$idRegion = $obj->rowid;
			//buscamos el registro en departament
						$sql = " SELECT rowid FROM ".MAIN_DB_PREFIX."c_departements";
						$sql.= " WHERE code_departement = '".$data['code_departement']."'";
						$sql.=" AND fk_region = '".$data['fk_region']."'";
						$res = $db->query($sql);
						if ($res)
						{
							$obj = $db->fetch_object($res);
							if (empty($obj->rowid))
							{
				//registramos
								$db->begin();
								$sql = " INSERT INTO ".$table."(code_departement,fk_region,nom,active)";
								$sql.= " VALUES ( ";
								$sql.= "'".$db->escape($data['code_departement'])."',";
								$sql.= $data['fk_region'].",";
								$sql.= "'".$db->escape($data['nom'])."',";
								$sql.= 1;
								$sql.=")";
								$resql = $db->query($sql);
								if ($resql)
									$db->commit();
								else
									$db->rollback();
							}
						}
					}
				}
			}
			break;
			case 'llx_c_partida':
	    //buscamos el registro
			$sql = " SELECT rowid FROM ".MAIN_DB_PREFIX."c_partida";
			$sql.= " WHERE code = '".$data['code']."'";
			$sql.=" AND gestion = '".$data['gestion']."'";
			$res = $db->query($sql);
			if ($res)
			{
				$obj = $db->fetch_object($res);

				if (empty($obj->rowid))
				{
		    //registramos
					$db->begin();
					$sql = " INSERT INTO ".$table."(gestion,code,label,active)";
					$sql.= " VALUES ( ";
					$sql.= $data['gestion'].",";
					$sql.= "'".$db->escape($data['code'])."',";
					$sql.= "'".$db->escape($data['label'])."',";
					$sql.= $data['active'];
					$sql.=")";
					$resql = $db->query($sql);
					if ($resql)
						$db->commit();
					else
						$db->rollback();
				}
			}
			break;


			case 'llx_commande':

	    //buscamos el pedido
			$res = $objc->fetch('',$data['ref']);
			if ($res==0)
			{
				include_once DOL_DOCUMENT_ROOT . '/core/modules/commande/modules_commande.php';
				$liste = ModelePDFCommandes::liste_modeles($db);
				$cmodel = '';
				foreach((array) $liste AS $model)
					if (empty($cmodel)) $cmodel = $model;
		//buscamos si existe el campo con formato fecha
				foreach((array) $aListdate AS $k => $value)
				{
					if($data[$value])
					{
			//verificamos y damos formato a la variable
						$resvalue = convertdate($aDatef,$seldate,$data[$value]);
						$objc->$value = $resvalue;
					}
				}
		//insetamos
				$db->begin();
				$objc->ref = $data['ref'];
				$objc->ref_ext = $data['ref_ext'];
				$objc->ref_int = $data['ref_int'];
		//$objc->date_commande = $datecommande;
				$objc->socid = $data['fk_soc'];
				$objc->note_private = $data['note_private'];
				$objc->note_public = $data['note_public'];
				$objc->source = $data['source_id'];
				$objc->fk_project = $data['fk_project'];
				$objc->ref_client = $data['ref_client'];
				$objc->modelpdf = $cmodel;
				$objc->cond_reglement_id = $data['cond_reglement_id'];
				$objc->mode_reglement_id = $data['mode_reglement_id'];
				$objc->availability_id = $data['availability_id'];
				$objc->demand_reason_id = $data['demand_reason_id'];
		//$objc->date_livraison = $datelivraison;
				$objc->fk_delivery_address = $data['fk_address'];
				$objc->contactid = $data['contactidp'];
				$objc->fk_statut = 1;
				$objc->fk_user_author = $user->id;
				$objc->date_creation = dol_now();
				$res = $objc->create($user);
				if ($res)
				{
					$db->commit();
		    //actualizamos
					$objup = new Commande($db);
					$objup->fetch($res);
					$objup->ref = $data['ref'];
					$objup->fk_statut = 1;
					$objup->valid($user,0,1);
				}
				else
					$db->rollback();
			}
			break;
			case 'llx_commandedet':
	    //buscamos el pedido
			$res = 0;
			if (!empty($data['fk_commande']))
				$res = $objc->fetch($data['fk_commande']);
			elseif (!empty($data['ref']))
				$res = $objc->fetch('',$data['ref']);
	    //verificamos el producto
			if (empty($data['fk_product']))
			{
				$resp = $objp->fetch('',$data['code_product']);
	       //echo '<hr>result prod '.$resp.' '.$data['code_product'];
				if ($resp && $objp->ref == $data['code_product'])
					$data['fk_product'] = $objp->id;
				else
				{
				//$db->rollback();
		    // echo '<pre>';
		    // print_r($data);
		    // echo '</pre>';
					print $mesg = '<div class="error">' . $langs->trans("ErrorFailedToAddProduct").' code_product |'.$data['code_product'].'| ' . '</div>';
					exit;
				}
			}
			else
			{
			//$db->rollback();
				echo '<hr>tiene producto '.$data['fk_product'];exit;
			}
			if ($res==1)
			{
				$rowid = $objc->id;
				$objd = new Orderline($db);
				$objd->fk_commande = $rowid;
				$objd->qty = $data['qty'];
				$objd->price = $data['price']+0;
				$objd->total_ht = $data['total_ht']+0;
				$objd->total_tva = $data['total_tva']+0;
				$objd->fk_product = $data['fk_product'];
				$objd->description = (!empty($data['description'])?$data['description']:$data['note_public']);
				$objd->tva_tx = $data['tva_tx'];
				$objd->localtax1_tx = $data['localtax1_tx'];
				$objd->localtax2_tx = $data['localtax2_tx'];
				$objd->product_type = $data['product_type'];
				$resp = $objd->insert(1);
				if ($resp < 0)
				{
					echo '<hr><hr>ERROR DE ESCRITURA ';
					print $objd->error;
				//$db->rollback();
					exit;
				}
			}
			else
			{
				print $mesg = '<div class="error">' . $langs->trans("ErrorFailedToAddProduct").' '.$langs->trans('Not exist commande').' code_ref|'.$data['ref'] . '|</div>';
				exit;
			}
			break;
		}
	}
		//echo '<hr>errr '.$error;exit;
	if (!$error)
	{
		$db->commit();
	}
	else
		$db->rollback();

	//echo '<br>table '.$table.' TotalError '.$error;
	if ($table == 'llx_poa_poa')
	{
		if ($error)
		{
			$action = 'create';
			$db->rollback();
			echo $mesg = '<div class="error">' . $langs->trans("ErrorFailedToAddPOA").' '.$langs->trans('Not exist information') . '|</div>';
			exit;
		}
		else
		{
			$action = 'exit';
			$db->commit();
		}
	}
	else
	{
		//$db->commit();
		$action = "exit";
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
			print_r($row[0]);
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
	'gloss' => 'gloss',
	'debit_total'=>'debit_total',
	'credit_total' => 'credit_total',
	);

$aHeaderTpl['llx_accounting_account'] = array('fk_pcg_version' => 'fk_pcg_version',
	'pcg_type' => 'pcg_type',
	'pcg_subtype' => 'pcg_subtype',
	'account_number'=>'account_number',
	'account_parent' => 'account_parent',
	'label'=>'label',
	'fk_accouting_category'=>'fk_accouting_category',
	);
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
);

//$action = "create";

/*
 * View
 */

$form=new Form($db);
$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

if ($action == 'create' || empty($action) && $user->rights->salary->crearuser)
{
	print_fiche_titre($langs->trans("Upload archive"));
	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);


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

	print '<tr><td>';
	print $langs->trans('Primera fila es titulo');
	print '</td>';
	print '<td>';
	print $form->selectyesno('selrow',GETPOST('selrow'),1);
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Separator');
	print '</td>';
	print '<td>';
	print '<input type="text" name="separator" size="2">';
	print '</td></tr>';

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
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">\n';
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
			$aCampolabel['Normal'] = $i;
			$i++;
			$aCampo[$i] = 'cta_class';
			$aCampolabel['Classe'] = $i;
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
			foreach($data AS $key)
			{
				$var=!$var;
				print "<tr $bc[$var]>";
				$c++;
				foreach($aHeaders AS $i => $keyname)
				{
					if (empty($keyname))
						$keyname = "none";
					$phone = $key->$keyname;
					$aArrData[$c][$i][$keyname] = $phone;
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
	 // echo '<pre>';
	 // print_r($aArrData);
	 // echo '</pre>';

		if($lSave)
		{
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
