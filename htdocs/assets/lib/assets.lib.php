<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/mant/lib/mant.lib.php
 *	\ingroup    Librerias
 *	\brief      Page fiche mantenimiento
 */

/**
 * Prepare array with list of tabs
 *
 * @param   Object  $object   Object related to tabs
 * @return  array       Array of tabs to shoc
 */
function assetsprop_prepare_head($object)
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();
	$head[$h][0] = DOL_URL_ROOT.'/assets/property/fiche.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Property");
	$head[$h][2] = 'property';
	$h++;
	$head[$h][0] = DOL_URL_ROOT.'/assets/permission/permission.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Permissions");
	$head[$h][2] = 'permission';
	$h++;

	//assetspropcomplete_head_from_modules($conf,$langs,$object,$head,$h,'assets');


	//assetspropcomplete_head_from_modules($conf,$langs,$object,$head,$h,'assets','remove');

	return $head;
}

/**
 * Prepare array with list of tabs
 *
 * @param   Object  $object   Object related to tabs
 * @return  array       Array of tabs to shoc
 */
function assetsdelete_prepare_head($object)
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();
	$head[$h][0] = DOL_URL_ROOT.'/assets/assets/fiche.php?id='.$object->id.'&tab=0';
	$head[$h][1] = $langs->trans("Fiche");
	$head[$h][2] = 'card';
	$h++;
	$head[$h][0] = DOL_URL_ROOT.'/assets/assets/fiche.php?id='.$object->id.'&tab=1';
	$head[$h][1] = $langs->trans("Depreciation");
	$head[$h][2] = 'depr';
	$h++;
	$head[$h][0] = DOL_URL_ROOT.'/assets/assets/fiche.php?id='.$object->id.'&tab=2';
	$head[$h][1] = $langs->trans("Assignment");
	$head[$h][2] = 'asig';
	$h++;
	$head[$h][0] = DOL_URL_ROOT.'/assets/assets/fiche.php?id='.$object->id.'&tab=3';
	$head[$h][1] = $langs->trans("Revaluation");
	$head[$h][2] = 'reval';
	$h++;
	$head[$h][0] = DOL_URL_ROOT.'/assets/assets/fiche.php?id='.$object->id.'&tab=4';
	$head[$h][1] = $langs->trans("Condition");
	$head[$h][2] = 'cond';
	$h++;

	//assetspropcomplete_head_from_modules($conf,$langs,$object,$head,$h,'assets');


	//assetspropcomplete_head_from_modules($conf,$langs,$object,$head,$h,'assets','remove');

	return $head;
}


/**
 *  Complete or removed entries into a head array (used to build tabs) with value added by external modules.
 *  Such values are declared into $conf->modules_parts['tab'].
 *
 *  @param	Conf			$conf           Object conf
 *  @param  Translate		$langs          Object langs
 *  @param  object|null		$object         Object object
 *  @param  array			$head          	Object head
 *  @param  int				$h				New position to fill
 *  @param  string			$type           Value for object where objectvalue can be
 *                              			'thirdparty'       to add a tab in third party view
 *		                        	      	'intervention'     to add a tab in intervention view
 *     		                    	     	'supplier_order'   to add a tab in supplier order view
 *          		            	        'supplier_invoice' to add a tab in supplier invoice view
 *                  		    	        'invoice'          to add a tab in customer invoice view
 *                          			    'order'            to add a tab in customer order view
 *                      			        'product'          to add a tab in product view
 *                              			'propal'           to add a tab in propal view
 *                              			'user'             to add a tab in user view
 *                              			'group'            to add a tab in group view
 * 		        	               	     	'member'           to add a tab in fundation member view
 *      		                        	'categories_x'	   to add a tab in category view ('x': type of category (0=product, 1=supplier, 2=customer, 3=member)
 *      									'ecm'			   to add a tab for another ecm view
 *                                          'stock'            to add a tab for warehouse view
 *  @param  string		$mode  	        	'add' to complete head, 'remove' to remove entries
 *	@return	void
 */
function assetspropcomplete_head_from_modules($conf,$langs,$object,&$head,&$h,$type,$mode='add')
{
	if (isset($conf->modules_parts['tabs'][$type]) && is_array($conf->modules_parts['tabs'][$type]))
	{
		foreach ($conf->modules_parts['tabs'][$type] as $value)
		{
			$values=explode(':',$value);

			if ($mode == 'add' && ! preg_match('/^\-/',$values[1]))
			{
				if (count($values) == 6)       // new declaration with permissions:  $value='objecttype:+tabname1:Title1:langfile@mymodule:$user->rights->mymodule->read:/mymodule/mynewtab1.php?id=__ID__'
				{
					if ($values[0] != $type) continue;

					if (verifCond($values[4]))
					{
						if ($values[3]) $langs->load($values[3]);
						if (preg_match('/SUBSTITUTION_([^_]+)/i',$values[2],$reg))
						{
							$substitutionarray=array();
							complete_substitutions_array($substitutionarray,$langs,$object);
							$label=make_substitutions($reg[1], $substitutionarray);
						}
						else $label=$langs->trans($values[2]);

						$head[$h][0] = dol_buildpath(preg_replace('/__ID__/i', ((is_object($object) && ! empty($object->id))?$object->id:''), $values[5]), 1);
						$head[$h][1] = $label;
						$head[$h][2] = str_replace('+','',$values[1]);
						$h++;
					}
				}
				else if (count($values) == 5)       // deprecated
				{
					if ($values[0] != $type) continue;
					if ($values[3]) $langs->load($values[3]);
					if (preg_match('/SUBSTITUTION_([^_]+)/i',$values[2],$reg))
					{
						$substitutionarray=array();
						complete_substitutions_array($substitutionarray,$langs,$object);
						$label=make_substitutions($reg[1], $substitutionarray);
					}
					else $label=$langs->trans($values[2]);

					$head[$h][0] = dol_buildpath(preg_replace('/__ID__/i', ((is_object($object) && ! empty($object->id))?$object->id:''), $values[4]), 1);
					$head[$h][1] = $label;
					$head[$h][2] = str_replace('+','',$values[1]);
					$h++;
				}
			}
			else if ($mode == 'remove' && preg_match('/^\-/',$values[1]))
			{
				if ($values[0] != $type) continue;
				$tabname=str_replace('-','',$values[1]);
				foreach($head as $key => $val)
				{
					$condition = (! empty($values[3]) ? verifCond($values[3]) : 1);
					if ($head[$key][2]==$tabname && $condition)
					{
						unset($head[$key]);
						break;
					}
				}
			}
		}
	}
}

function select_been($selected='',$htmlname='been',$htmloption='',$showempty=0,$showlabel=0,$campoid='rowid')
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code AS code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_assets_been AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " AND f.entity = ".$conf->entity;
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($resql)
		$html = htmlselect($resql,$selected,$htmlname,$htmloption,$showempty,$showlabel,$campoid);
	return $html;
}

function select_type_group($selected='',$htmlname='type_group',$htmloption='',$showempty=0,$showlabel=0,$campoid='rowid')
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code AS code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_assets_group AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($resql)
		$html = htmlselect($resql,$selected,$htmlname,$htmloption,$showempty,$showlabel,$campoid);
	return $html;
}

function select_type_patrim($selected='',$htmlname='type_patrim',$htmloption='',$showempty=0,$showlabel=0,$campoid='rowid')
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code AS code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_assets_patrim AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';
	if ($resql)
		$html = htmlselect($resql,$selected,$htmlname,$htmloption,$showempty,$showlabel,$campoid);
	return $html;
}

// campos obligatorios de la tabla
// rowid
// code
// libelle
function htmlselect($resql,$selected='',$htmlname='type_group',$htmloption='',$showempty=0,$showlabel=0,$campoid='rowid')
{
	global $langs,$db,$conf;
	$html.= '<select class="flat" name="'.$htmlname.'" id="select'.$htmlname.'">';
	if ($showempty)
		$html.= '<option value="0">&nbsp;</option>';
	$num = $db->num_rows($resql);
	$i = 0;
	if ($num)
	{
		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
			if (!empty($selected) && $selected == $obj->$campoid)
			{
				$html.= '<option value="'.$obj->$campoid.'" selected="selected">'.$obj->libelle.'</option>';
				if ($showlabel)
					return $obj->libelle;
			}
			else
			{
				$html.= '<option value="'.$obj->$campoid.'">'.$langs->trans($obj->libelle);
				if (!empty($obj->code) && $campoid == 'rowid')
					$html.= ' ('.$obj->code.')';
				$html.= '</option>';

			}
			$i++;
		}
	}
	$html.= '</select>';
	return $html;

}

function assets_prepare_head($object)
{
	global $langs, $conf,$user;
	$langs->load('assets');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/assets/assets/fiche.php?tab=".$h."&id=".$object->id,1);
	$head[$h][1] = $langs->trans("Fiche");
	$head[$h][2] = 'assets';
	$h++;

	if ($user->rights->assets->teach->lire)
	{
		$head[$h][0] = dol_buildpath("/assets/assets/fiche.php?tab=".$h."&id=".$object->id,1);
		$head[$h][1] = $langs->trans("Depreciation");
		$head[$h][2] = 'depreciation';

	}
	$h++;
	if ($user->rights->assets->hist->read)
	{
		$head[$h][0] = dol_buildpath("/assets/assets/fiche.php?tab=".$h."&id=".$object->id,1);
		$head[$h][1] = $langs->trans("Assignment");
		$head[$h][2] = 'assignment';
	}
	$h++;
	if ($user->rights->assets->reval->read)
	{
		$head[$h][0] = dol_buildpath("/assets/assets/fiche.php?tab=".$h."&id=".$object->id,1);
		$head[$h][1] = $langs->trans("Revaluated");
		$head[$h][2] = 'revaluo';
	}
	$h++;
	if ($user->rights->assets->ass->read)
	{
		$head[$h][0] = dol_buildpath("/assets/assets/fiche.php?tab=".$h."&id=".$object->id,1);
		$head[$h][1] = $langs->trans("Condition");
		$head[$h][2] = 'condition';
	}
	$h++;
	if ($user->rights->assets->doc->read)
	{
		$head[$h][0] = dol_buildpath("/assets/assets/fiche.php?tab=".$h."&id=".$object->id,1);
		$head[$h][1] = $langs->trans("Documents");
		$head[$h][2] = 'document';
	}
	$h++;
	if ($user->rights->assets->dep->ace)
	{
		$head[$h][0] = dol_buildpath("/assets/contador/contador.php?tab=".$h."&id=".$object->id,1);
		$head[$h][1] = $langs->trans("Depracelerate");
		$head[$h][2] = 'depracelerate';
	}
	$h++;
	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'assets');
	return $head;
}

function select_generic($resql,$showempty='',$htmlname='',$htmloption='',$campo='',$selected='')
{
	global $db,$langs,$conf;
	$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
	if ($showempty)
	{
		$out.= '<option value="-1"';
		if ($selected == -1) $out.= ' selected="selected"';
		$out.= '>&nbsp;</option>';
	}
	$num = $db->num_rows($resql);
	$i = 0;
	if ($num)
	{
		$foundselected=false;

		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
			$countryArray[$i]['rowid'] 		= $obj->rowid;
			$countryArray[$i]['code_iso'] 	= $obj->code_iso;
			$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv($campo.$obj->code_iso)!=$campo.$obj->code_iso?$langs->transnoentitiesnoconv($campo.$obj->code_iso):($obj->label!='-'?$obj->label:''));
			$label[$i] 	= $countryArray[$i]['label'];
			$i++;
		}

		array_multisort($label, SORT_ASC, $countryArray);

		foreach ($countryArray as $row)
		{
	  //print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
			if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
			{
				$foundselected=true;
				$out.= '<option value="'.$row['rowid'].'" selected="selected">';
			}
			else
			{
				$out.= '<option value="'.$row['rowid'].'">';
			}
			$out.= dol_trunc($row['label'],$maxlength,'middle');
			if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
			$out.= '</option>';
		}
	}
	$out.= '</select>';
	return $out;
}

function generarcodigo($longitud)
{
	$key = '';
	$pattern = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$max = strlen($pattern)-1;
	for($i=0; $i < $longitud; $i++)
	{
		$key .= $pattern{mt_rand(0,$max)};
	}
	return $key;
}

function userproperty($fk_user)
{
	global $objprouser;
	$filteruser = '';
	$filt = array(1=>1);
	$filterstatic = " AND t.fk_user = ".$fk_user;
	$res = $objprouser->fetchAll('','',0,0,$filt,'AND',$filterstatic,false);
	$aProperty = array();
	if ($res>0)
	{
		foreach ((array) $objprouser->lines AS $j => $line)
		{
			if (!empty($filteruser)) $filteruser.= ',';
			$filteruser.= $line->fk_property;
			$aProperty[$line->fk_property] = $line->fk_property;
		}
	}
	else
		$filteruser = 0;
	return array($filteruser,$aProperty);
}

function fetch_been($id,$code='')
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code AS code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_assets_been AS f ";
	$sql.= " WHERE ";
	$sql.= " f.entity = ".$conf->entity;
	if ($code) $sql.= " AND f.code = '".trim($code)."'";
	else $sql.= " AND f.rowid = ".$id;
	$resql = $db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$obj = $db->fetch_object($resql);
		$db->free($resql);
		return $obj;
	}
	$db->free($resql);
	return 0;

}

function fetch_group($id,$code='')
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code AS code, f.label AS libelle, f.useful_life, f.account_accounting FROM ".MAIN_DB_PREFIX."c_assets_group AS f ";
	$sql.= " WHERE ";
	$sql.= " f.entity = ".$conf->entity;
	if ($code) $sql.= " AND f.code = '".trim($code)."'";
	else $sql.= " AND f.rowid = ".$id;

	$resql = $db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$obj = $db->fetch_object($resql);
		$db->free($resql);
		return $obj;
	}
	$db->free($resql);
	return 0;

}

function add_historial(&$obj,&$user,$db,$id,$ref,$been,$desc)
{
	global $conf;
	$pcip = getRealIPass();
	require_once DOL_DOCUMENT_ROOT.'/assets/class/assethistorial.class.php';
	$objh = new Assethistorial($db);
	$objh->fk_asset = $id;
	$objh->ref_ext = $ref;
	$objh->been = $been;
	$objh->description = $desc;
	$objh->pc_ip = $pcip;
	if (is_object($obj))
	{
		$objh->origin = $obj->table_element;
		$objh->originid = $obj->id;
	}
	$objh->fk_user_create = $user->id;
	$objh->fk_user_mod = $user->id;
	$objh->datec = dol_now();
	$objh->datem = dol_now();
	$objh->tms = dol_now();
	$objh->create($user);
}

function getRealIPass() {
	if (!empty($_SERVER['HTTP_CLIENT_IP']))
		return $_SERVER['HTTP_CLIENT_IP'];

	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		return $_SERVER['HTTP_X_FORWARDED_FOR'];

	return $_SERVER['REMOTE_ADDR'];
}

function isLeapYear($year)
{
	return ((($year%4 == 0) && ($year%100)) || $year%400 == 0)? true: false;
}

function procesa_upload_files(array $aHeader, array $aHeaderTpl, $table,$nombre_archivo, $tmp_name, $tempdir, $selrow, $name='' )
{
	global $db,$langs;
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
		if ($selrow)
		{
			foreach($aHeaders AS $i => $value)
			{
				//print_liste_field_titre($langs->trans($value),'fiche.php','','','','');
			}
		}

		$lSave = true;
		$var=True;
		$c = 0;
		if ($selrow)
		{
			foreach((array) $aDetalle AS $j => $data)
			{
				$c++;
				foreach($aHeaders AS $i => $keyname)
				{
					$aKey = explode(',',$keyname);
					if (empty($keyname)) $keyname = "none";
					$phone = $data[$i];
					if ($name == 'departament')
					{
						if ($aKey[0]=='CODOFIC')
							$aArrData[$c]['fk_departament'] = $phone;
						elseif ($aKey[0]=='NOMOFIC')
							$aArrData[$c]['label'] = $phone;
					}
					elseif ($name == 'member')
					{
						if ($aKey[0]=='CODOFIC')
							$aArrData[$c]['fk_departament'] = $phone;
						elseif ($aKey[0]=='CODRESP')
							$aArrData[$c]['fk_member'] = $phone;
						elseif ($aKey[0]=='NOMRESP')
							$aArrData[$c]['label'] = $phone;
					}
					else
					{
						if ($aKey[0]=='FECHA')
						{
							$aArrData[$c][$i] = $phone;
							$phone = dol_print_date($phone,'day');
						}
						else
							$aArrData[$c][$i] = $phone;
					}
				}
			}
		}
		else
		{
			foreach($data AS $key => $dataval)
			{
				$var=!$var;

				$c++;
				foreach($aHeaders AS $i => $keyname)
				{
					$value = $dataval[$i];
					$aArrData[$c][$i] = $value;

				}
			}
		}
		//vamos a rearmar el array
		if ($name == 'departament')
		{
			$aArraytmp = $aArrData;
			$aArrData = array();
			foreach ($aArraytmp AS $j => $data)
			{
				$aArrData[$data['fk_departament']] = $data['label'];
			}
		}
		if ($name == 'member')
		{
			$aArraytmp = $aArrData;
			$aArrData = array();
			foreach ($aArraytmp AS $j => $data)
			{
				$aArrData[$data['fk_departament']][$data['fk_member']] = $data['label'];
			}
		}
	}
	return array($aDetalle,$aHeaders,$aArrData);
}

	?>
