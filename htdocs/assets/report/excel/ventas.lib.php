<?php
/* Copyright (C) 2008-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2011	   Juanjo Menent        <jmenent@2byte.es>
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
 * or see http://www.gnu.org/
 */

/**
 *  \file		htdocs/core/lib/agenda.lib.php
 *  \brief		Set of function for the agenda module
 */

/**
 * Show filter form in usuarios
 *
 * @param	Object	$form			Form object
 * @param	int		$statut		Can edit filter fields
 * @param	int		$source			Status
 * @param 	int		$list			Year
 * @return	void
 */


	/**
	 *    Get array of all contacts for an object
	 *
	 *    @param	int			$statut		Status of lines to get (-1=all)
	 *    @param	string		$source		Source of contact: external or thirdparty (llx_socpeople) or internal (llx_user)
	 *    @param	int         $list       0:Return array contains all properties, 1:Return array contains just id
	 *    @return	array		            Array of contacts
	 */
	function list_contact($statut=-1,$admin=0)
	{
		global $langs,$db,$conf;

		$tab=array();

		$sql = "SELECT u.rowid, u.rowid ";
		$sql.= " FROM ".MAIN_DB_PREFIX."user AS u";
		$sql.= " WHERE u.entity =".$conf->entity;
		if ($statut >= 0) $sql.= " AND u.statut = '".$statut."'";
		$sql.= " AND u.admin = '".$admin."'";

		$sql.=" ORDER BY u.name ASC";

  //  dol_syslog(get_class($this)."::list_contact sql=".$sql);
		$resql=$db->query($sql);
		if ($resql)
		{
			$num=$db->num_rows($resql);
			$i=0;
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$tab[$i]=$obj->id;
				$i++;
			}
			return $tab;
		}
		else
		{
			$error=$db->error();
			dol_print_error($db);
			return -1;
		}
	}

	function saldoBank($bankid_cash,$userid,$fechaIni,$fechaFin,$cond='act')
	{
		global $db, $conf;
		$sql = "SELECT SUM(b.amount)";
		$sql.= ", date_format(b.dateo,'%Y-%m') as dm";
		$sql.= " FROM ".MAIN_DB_PREFIX."bank as b";
		$sql.= ", ".MAIN_DB_PREFIX."bank_account as ba";
		$sql.= " WHERE b.fk_account = ba.rowid";
		$sql.= " AND ba.entity = ".$conf->entity;
		$sql.= " AND b.amount >= 0";
		if (! empty($bankid_cash))
			$sql .= " AND b.fk_account IN (".$bankid_cash.")";
		if (! empty($userid))
			$sql .= " AND b.fk_user_author IN (".$userid.")";

		if (! empty($fechaIni) && !empty($fechaFin))
		{
			if ($cond == 'ant')
				$sql .= " AND b.dateo < '".$fechaIni."'";
			else
				$sql .= " AND b.dateo BETWEEN '".$fechaIni."' AND '".$fechaFin."'";
		}
		$sql.= " GROUP BY dm";
		$resql=$db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			while ($i < $num)
			{
				$row = $db->fetch_row($resql);
				$encaiss[$row[1]] = $row[0];
				$i++;
			}
		}
		else
		{
			dol_print_error($db);
		}

		$sql = "SELECT SUM(b.amount)";
		$sql.= ", date_format(b.dateo,'%Y-%m') as dm";
		$sql.= " FROM ".MAIN_DB_PREFIX."bank as b";
		$sql.= ", ".MAIN_DB_PREFIX."bank_account as ba";
		$sql.= " WHERE b.fk_account = ba.rowid";
		$sql.= " AND ba.entity = ".$conf->entity;
		$sql.= " AND b.amount <= 0";
		if (! empty($bankid_cash))
			$sql .= " AND b.fk_account IN (".$bankid_cash.")";
		if (! empty($userid))
			$sql .= " AND b.fk_user_author IN (".$userid.")";
		if (! empty($fechaIni) && !empty($fechaFin))
		{
			if ($cond == 'ant')
				$sql .= " AND b.dateo < '".$fechaIni."'";
			else
				$sql .= " AND b.dateo BETWEEN '".$fechaIni."' AND '".$fechaFin."'";
		}
		$sql.= " GROUP BY dm";
		echo '<hr>'.$sql;

		$resql=$db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			while ($i < $num)
			{
				$row = $db->fetch_row($resql);
				$decaiss[$row[1]] = -$row[0];
				$i++;
			}
		}
		else
		{
			dol_print_error($db);
		}
		return array($encaiss,$decaiss);
	}

/*
 * saldo por cada cuenta
*/
function balanceAccount($bankid_cash,$userid,$fechaIni,$fechaFin,$cond='act')
{
	global $db, $conf;
	$sql = "SELECT b.fk_account, SUM(b.amount) AS total";
	$sql.= ", date_format(b.dateo,'%Y-%m') as dm";
	$sql.= " FROM ".MAIN_DB_PREFIX."bank as b";
	$sql.= ", ".MAIN_DB_PREFIX."bank_account as ba";
	$sql.= " WHERE b.fk_account = ba.rowid";
	$sql.= " AND ba.entity = ".$conf->entity;
	$sql.= " AND b.amount >= 0";
	if (! empty($bankid_cash))
		$sql .= " AND b.fk_account IN (".$bankid_cash.")";
	if (! empty($userid))
		$sql .= " AND b.fk_user_author IN (".$userid.")";

	if (! empty($fechaIni) && !empty($fechaFin))
	{
		if ($cond == 'ant')
			$sql .= " AND b.dateo < '".$fechaIni."'";
		else
			$sql .= " AND b.dateo BETWEEN '".$fechaIni."' AND '".$fechaFin."'";
	}
	$sql.= " GROUP BY b.fk_account, dm";
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		while ($i < $num)
		{
			$row = $db->fetch_object($resql);

			$encaiss[$row->fk_account] = $row->total;
			$i++;
		}
	}
	else
	{
		dol_print_error($db);
	}

	$sql = "SELECT b.fk_account, SUM(b.amount) AS total";
	$sql.= ", date_format(b.dateo,'%Y-%m') as dm";
	$sql.= " FROM ".MAIN_DB_PREFIX."bank as b";
	$sql.= ", ".MAIN_DB_PREFIX."bank_account as ba";
	$sql.= " WHERE b.fk_account = ba.rowid";
	$sql.= " AND ba.entity = ".$conf->entity;
	$sql.= " AND b.amount <= 0";
	if (! empty($bankid_cash))
		$sql .= " AND b.fk_account IN (".$bankid_cash.")";
	if (! empty($userid))
		$sql .= " AND b.fk_user_author IN (".$userid.")";
	if (! empty($fechaIni) && !empty($fechaFin))
	{
		if ($cond == 'ant')
			$sql .= " AND b.dateo < '".$fechaIni."'";
		else
			$sql .= " AND b.dateo BETWEEN '".$fechaIni."' AND '".$fechaFin."'";
	}
	$sql.= " GROUP BY b.fk_account, dm";

	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		while ($i < $num)
		{
			$row = $db->fetch_object($resql);
			$decaiss[$row->fk_account] = -$row->total;
			$i++;
		}
	}
	else
	{
		dol_print_error($db);
	}
	return array($encaiss,$decaiss);
}

/**
 *	Return label of statut generico /validate/no validate
 *
 *	@param		int		$state      	Id state
 *	@param      int		$mode        	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
 *  @return     string					Label of statut
 */
function LibStatev($statut,$mode)
{
	global $langs;
  //print 'x'.$statut.'-'.$facturee;
	if ($mode == 0)
	{
		if ($statut==-1) return $langs->trans('StatusCanceled');
		if ($statut==0) return $langs->trans('StatusDraft');
		if ($statut==1) return $langs->trans('StatusValidated');
	}
	elseif ($mode == 1)
	{
		if ($statut==-1) return $langs->trans('StatusCanceled');
		if ($statut==0) return $langs->trans('StatusDraft');
		if ($statut==1) return $langs->trans('StatusValidated');
	}
	elseif ($mode == 2)
	{
		if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceledShort');
		if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraftShort');
		if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1').' '.$langs->trans('StatusOrderValidatedShort');
		if ($statut==2) return img_picto($langs->trans('StatusOrderSent'),'statut3').' '.$langs->trans('StatusOrderSentShort');
		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBillShort');
		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('StatusOrderProcessedShort');
	}
	elseif ($mode == 3)
	{
		if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5');
		if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0');
		if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1');
		if ($statut==2) return img_picto($langs->trans('StatusOrderSentShort'),'statut3');
		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7');
		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6');
	}
	elseif ($mode == 4)
	{
		if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceled');
		if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraft');
		if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1').' '.$langs->trans('StatusOrderValidated');
		if ($statut==2) return img_picto($langs->trans('StatusOrderSentShort'),'statut3').' '.$langs->trans('StatusOrderSent');
		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBill');
		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('StatusOrderProcessed');
	}
	elseif ($mode == 5)
	{
		if ($statut==-1) return $langs->trans('StatusCanceled').' '.img_picto($langs->trans('StatusCanceled'),'statut5');
		if ($statut==0) return $langs->trans('StatusDraft').' '.img_picto($langs->trans('StatusDraft'),'statut0');
		if ($statut==1) return $langs->trans('StatusValidated').' '.img_picto($langs->trans('StatusValidated'),'statut1');
	}
}

function select_yesnov($selected='',$htmlname='print',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;
	$langs->load("ventas@ventas");

	$out='';
	$countryArray=array();
	$label=array();
	$i = 1;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Yes');
	$label[$i] = $countryArray[$i]['label'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Not');
	$label[$i] = $countryArray[$i]['label'];
	$i++;

	if ($showLabel)
		return $countryArray[$selected]['label'];

	$out = print_selectv($selected,$htmlname,$htmloption,$maxlength,
		$showempty,$showLabel,$countryArray,$label);
	return $out;
}

function select_typebill($selected='',$htmlname='type',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;
	$langs->load("ventas@ventas");

	$out='';
	$countryArray=array();
	$label=array();
	$i = 1;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('NF');
	$label[$i] = $countryArray[$i]['label'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('NCI');
	$label[$i] = $countryArray[$i]['label'];
	$i++;

	if ($showLabel)
		return $countryArray[$selected]['label'];

	$out = print_selectv($selected,$htmlname,$htmloption,$maxlength,
		$showempty,$showLabel,$countryArray,$label);
	return $out;
}

function select_lotebill($selected='',$htmlname='type',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;
	$langs->load("ventas");

	$out='';
	$countryArray=array();
	$label=array();
	$i = 1;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Manual');
	$label[$i] = $countryArray[$i]['label'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Automatic');
	$label[$i] = $countryArray[$i]['label'];
	$i++;

	if ($showLabel)
		return $countryArray[$selected]['label'];

	$out = print_selectv($selected,$htmlname,$htmloption,$maxlength,
		$showempty,$showLabel,$countryArray,$label);
	return $out;
}

/**
 *  Return combo list of activated countries, into language of user
 *
 *  @param	string	$selected       Id or Code or Label of preselected country
 *  @param  string	$htmlname       Name of html select object
 *  @param  string	$htmloption     Options html on select object
 *  @return string           		HTML string with select
 */
function select_entrepot($selected='',$htmlname='fk_entrepot',$htmloption='')
{
	global $db,$conf,$langs;

	$langs->load("dict");

	$out='';
	$padreArray=array();
	$label=array();

	$sql = "SELECT rowid, description, label";
	$sql.= " FROM ".MAIN_DB_PREFIX."entrepot AS e ";
	$sql.= " WHERE statut = 1";
	$sql.= " ORDER BY description ASC";

	$resql=$db->query($sql);
	if ($resql)
	{
		$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			$foundselected=false;

			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$countryArray[$i]['rowid'] 	= $obj->rowid;
				$countryArray[$i]['code_iso'] 	= $obj->code_iso;
				$countryArray[$i]['label']	= ($obj->code_iso && $langs->transnoentitiesnoconv("Country".$obj->code_iso)!="Country".$obj->code_iso?$langs->transnoentitiesnoconv("Country".$obj->code_iso):($obj->label!='-'?$obj->label:''));
				$label[$i] 	= $countryArray[$i]['label'];
				$i++;
			}

			array_multisort($label, SORT_ASC, $countryArray);
			$out.='<option value="-1"'.($id==-1?' selected="selected"':'').'>&nbsp;</option>'."\n";

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
				$out.= $row['label'];
				if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
				$out.= '</option>';
			}
		}
		$out.= '</select>';
	}
	else
	{
		dol_print_error($db);
	}

	return $out;
}


function print_selectv($selected='',$htmlname='status',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0,$countryArray,$label)
{

	$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
	if ($showempty)
	{
		$out.= '<option value="-1"';
		if ($selected == -1) $out.= ' selected="selected"';
		$out.= '>&nbsp;</option>';
	}

	array_multisort($label, SORT_ASC, $countryArray);

	foreach ($countryArray as $row)
	{
	  //print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
		if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['label']) )
		{
			$foundselected=true;
			$out.= '<option value="'.$row['rowid'].'" selected="selected">';
		}
		else
		{
			$out.= '<option value="'.$row['rowid'].'">';
		}
		$out.= dol_trunc($row['label'],$maxlength,'middle');
		$out.= '</option>';
	}
	$out.= '</select>';

	return $out;
}

/*numerico a literal DSO*/
function num2texto($numero, $moneda = "Bolivianos", $singular = "Boliviano") {
	/* Obtenida de www.hackingballz.com*/
	/* Si es 0 el número, no tiene caso procesar toda la información */
	if( $numero == 0 || !isset( $numero ) ) {
		return strtoupper( "CERO $moneda 00/100" );
	}
		/* En caso que sea un peso, pues igual que el 0 aparte que no muestre
		  el plural "pesos"
		*/
		  if( $numero == 1 ) {
		  	return strtoupper( "UN $singular 00/100" );
		  }

		//$numeros["unidad"][0][0]="cero";
		  $numeros["unidad"][1][0] = "un";
		  $numeros["unidad"][2][0] = "dos";
		  $numeros["unidad"][3][0] = "tres";
		  $numeros["unidad"][4][0] = "cuatro";
		  $numeros["unidad"][5][0] = "cinco";
		  $numeros["unidad"][6][0] = "seis";
		  $numeros["unidad"][7][0] = "siete";
		  $numeros["unidad"][8][0] = "ocho";
		  $numeros["unidad"][9][0] = "nueve";

		  $numeros["decenas"][1][0] = "diez";
		  $numeros["decenas"][2][0] = "veinte";
		  $numeros["decenas"][3][0] = "treinta";
		  $numeros["decenas"][4][0] = "cuarenta";
		  $numeros["decenas"][5][0] = "cincuenta";
		  $numeros["decenas"][6][0] = "sesenta";
		  $numeros["decenas"][7][0] = "setenta";
		  $numeros["decenas"][8][0] = "ochenta";
		  $numeros["decenas"][9][0] = "noventa";
		  $numeros["decenas"][1][1][0] = "dieci";
		  $numeros["decenas"][1][1][1] = "once";
		  $numeros["decenas"][1][1][2] = "doce";
		  $numeros["decenas"][1][1][3] = "trece";
		  $numeros["decenas"][1][1][4] = "catorce";
		  $numeros["decenas"][1][1][5] = "quince";
		  $numeros["decenas"][2][1] = "veinte y ";
		  $numeros["decenas"][3][1] = "treinta y ";
		  $numeros["decenas"][4][1] = "cuarenta y ";
		  $numeros["decenas"][5][1] = "cincuenta y ";
		  $numeros["decenas"][6][1] = "sesenta y ";
		  $numeros["decenas"][7][1] = "setenta y ";
		  $numeros["decenas"][8][1] = "ochenta y ";
		  $numeros["decenas"][9][1] = "noventa y ";

		  $numeros["centenas"][1][0] = "cien";
		  $numeros["centenas"][2][0] = "doscientos ";
		  $numeros["centenas"][3][0] = "trecientos ";
		  $numeros["centenas"][4][0] = "cuatrocientos ";
		  $numeros["centenas"][5][0] = "quinientos ";
		  $numeros["centenas"][6][0] = "seiscientos ";
		  $numeros["centenas"][7][0] = "setecientos ";
		  $numeros["centenas"][8][0] = "ochocientos ";
		  $numeros["centenas"][9][0] = "novecientos ";
		  $numeros["centenas"][1][1] = "ciento ";

		  $postfijos[1][0] = "";
		  $postfijos[10][0] = "";
		  $postfijos[100][0] = "";
		  $postfijos[1000][0] = " mil ";
		  $postfijos[10000][0] = " mil ";
		  $postfijos[100000][0] = " mil ";
		  $postfijos[1000000][0] = " millon ";
		  $postfijos[10000000][0] = " millon ";
		  $postfijos[100000000][0] = " millon ";
		  $postfijos[1000000][1] = " millones ";
		  $postfijos[10000000][1] = " millones ";
		  $postfijos[100000000][1] = " millones ";

		  $decimal_break = ".";
	//echo "test run on ".$numero."<br>";
		  $entero = strtok( $numero, $decimal_break);
		  $decimal = strtok( $decimal_break );
		  if ( $decimal == "" ) {
		  	$decimal = "00";
		  }
		  if ( strlen( $decimal ) < 2 ) {
		  	$decimal .= "0";
		  }
		  if ( strlen( $decimal ) > 2 ) {
		  	$decimal = substr( $decimal, 0, 2 );
		  }
		  $decimal .= '/100';
		  $entero_breakdown = $entero;

		  $breakdown_key = 1000000000000;
		  $num_string = "";
		  while ( $breakdown_key > 0.5 ) {
		  	$breakdown["entero"][$breakdown_key]["number"] =
		  	floor( $entero_breakdown/$breakdown_key );

		  	if ( $breakdown["entero"][$breakdown_key]["number"] > 0 ) {
		  		$breakdown["entero"][$breakdown_key][100] =
		  		floor( $breakdown["entero"][$breakdown_key]["number"] / 100 );
		  		$breakdown["entero"][$breakdown_key][10] =
		  		floor( ( $breakdown["entero"][$breakdown_key]["number"] % 100 )
		  			/ 10 );
		  		$breakdown["entero"][$breakdown_key][1] =
		  		floor( $breakdown["entero"][$breakdown_key]["number"] % 10 );

		  		$hundreds = $breakdown["entero"][$breakdown_key][100];
				// if not a closed value at hundredths
		  		if ( ( $breakdown["entero"][$breakdown_key][10]
		  			+ $breakdown["entero"][$breakdown_key][1] ) > 0 ) {
		  			$chundreds = 1;
		  	} else {
		  		$chundreds = 0;
		  	}

		  	if ( isset( $numeros["centenas"][$hundreds][$chundreds] ) ) {
		  		$num_string .= $numeros["centenas"][$hundreds][$chundreds];
		  	} else {
		  		if( isset( $numeros["centenas"][$hundreds][0] ) ) {
		  			$num_string .= $numeros["centenas"][$hundreds][0];
		  		}
		  	}

		  	if ( ( $breakdown["entero"][$breakdown_key][1] ) > 0 ) {
		  		$ctens = 1;
		  		$tens = $breakdown["entero"][$breakdown_key][10];
		  		if ( ( $breakdown["entero"][$breakdown_key][10] ) == 1 ) {
		  			if ( ( $breakdown["entero"][$breakdown_key][1] ) < 6 ) {
		  				$cctens = $breakdown["entero"][$breakdown_key][1];
		  				$num_string .=
		  				$numeros["decenas"][$tens][$ctens][$cctens];
		  			} else {
		  				$num_string .= $numeros["decenas"][$tens][$ctens][0];
		  			}
		  		} else {
		  			if( isset( $numeros["decenas"][$tens][$ctens] ) ){
		  				$num_string .= $numeros["decenas"][$tens][$ctens];
		  			}
		  		}
		  	} else {
		  		$ctens = 0;
		  		$tens = $breakdown["entero"][$breakdown_key][10];
		  		if( isset( $numeros["decenas"][$tens][$ctens] ) ) {
		  			$num_string .= $numeros["decenas"][$tens][$ctens];
		  		}
		  	}

		  	if ( !( isset( $cctens ) ) ) {
		  		$ones = $breakdown["entero"][$breakdown_key][1];
		  		if ( isset( $numeros["unidad"][$ones][0] ) ) {
		  			$num_string .= $numeros["unidad"][$ones][0];
		  		}
		  	}

		  	$cpostfijos = -1;
		  	if ( $breakdown["entero"][$breakdown_key]["number"] > 1 ) {
		  		$cpostfijos = 1;
		  	}

		  	if ( isset( $postfijos[$breakdown_key][$cpostfijos] ) ) {
		  		$num_string .= $postfijos[$breakdown_key][$cpostfijos];
		  	} else {
		  		$num_string .= $postfijos[$breakdown_key][0];
		  	}
		  }
		  unset( $cctens );
		  $entero_breakdown %= $breakdown_key;
		  $breakdown_key /= 1000;
		}
		$letras = $num_string . ' ' . $decimal . " $moneda";
		$letras = strtoupper( $letras );
		return $letras;
	}

	function select_monthv($selected='',$htmlname='mes',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
	{
		global $conf,$langs;

		$langs->load("contab@contab");

		$out='';
		$countryArray=array();
		$label=array();
		$i = 1;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Jan');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Feb');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Mar');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Apr');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('May');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Jun');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Jul');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Aug');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Sep');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Oct');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Nov');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Dec');
		$label[$i] = $countryArray[$i]['rowid'];

		if ($showLabel)
			return $countryArray[$selected]['label'];
		$out = print_selectv($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);

		return $out;
	}


/**
 * Initialize the array of tabs for customer invoice
 *
 * @param	Facture		$object		Invoice object
 * @return	array					Array of head tabs
 */
function facture_prepare_head_ventas($object)
{
	global $langs, $conf;
	$h = 0;
	$head = array();

	// $head[$h][0] = DOL_URL_ROOT.'/compta/facture.php?facid='.$object->id;
	// $head[$h][1] = $langs->trans('CardBill');
	// $head[$h][2] = 'compta';
	// $h++;

	// if (empty($conf->global->MAIN_DISABLE_CONTACTS_TAB))
	// {
	// 	$head[$h][0] = DOL_URL_ROOT.'/compta/facture/contact.php?facid='.$object->id;
	// 	$head[$h][1] = $langs->trans('ContactsAddresses');
	// 	$head[$h][2] = 'contact';
	// 	$h++;
	// }

	if (! empty($conf->global->MAIN_USE_PREVIEW_TABS))
	{
		$head[$h][0] = DOL_URL_ROOT.'/compta/facture/apercu.php?facid='.$object->id;
		$head[$h][1] = $langs->trans('Preview');
		$head[$h][2] = 'preview';
		$h++;
	}

	//if ($fac->mode_reglement_code == 'PRE')
	if (! empty($conf->prelevement->enabled))
	{
		$head[$h][0] = DOL_URL_ROOT.'/compta/facture/prelevement.php?facid='.$object->id;
		$head[$h][1] = $langs->trans('StandingOrders');
		$head[$h][2] = 'standingorders';
		$h++;
	}

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname);   												to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'invoice');

	// if (empty($conf->global->MAIN_DISABLE_NOTES_TAB))
	// {
	// 	$nbNote = 0;
	//     if(!empty($object->note_private)) $nbNote++;
	// 		if(!empty($object->note_public)) $nbNote++;
	// 	$head[$h][0] = DOL_URL_ROOT.'/compta/facture/note.php?facid='.$object->id;
	// 	$head[$h][1] = $langs->trans('Notes');
	// 		if($nbNote > 0) $head[$h][1].= ' ('.$nbNote.')';
	// 	$head[$h][2] = 'note';
	// 	$h++;
	// }

	// require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	// $upload_dir = $conf->facture->dir_output . "/" . dol_sanitizeFileName($object->ref);
	// $nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
	// $head[$h][0] = DOL_URL_ROOT.'/compta/facture/document.php?facid='.$object->id;
	// $head[$h][1] = $langs->trans('Documents');
	// if($nbFiles > 0) $head[$h][1].= ' ('.$nbFiles.')';
	// $head[$h][2] = 'documents';
	// $h++;

	// $head[$h][0] = DOL_URL_ROOT.'/compta/facture/info.php?facid='.$object->id;
	// $head[$h][1] = $langs->trans('Info');
	// $head[$h][2] = 'info';
	// $h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'invoice','remove');

	return $head;
}

function search_mode_reglament($obj_facturation,$arrValue,$payment,$note='')
{
	global $langs, $conf,$db;

	switch ( $obj_facturation->getSetPaymentMode($payment) )
	{
		case 'DIF':
		$mode_reglement_id = 0;
		$cond_reglement_id = dol_getIdFromCode($db,'RECEP','cond_reglement','code','rowid');
	//$cond_reglement_id = 0;
		break;
		case 'ESP':
		$mode_reglement_id = dol_getIdFromCode($db,'LIQ','c_paiement');
		$cond_reglement_id = 0;
		$note .= $langs->trans("Cash")."\n";
		$note .= $langs->trans("Received").' : '.$obj_facturation->montantEncaisse()." ".$conf->currency."\n";
		$note .= $langs->trans("Rendu").' : '.$obj_facturation->montantRendu()." ".$conf->currency."\n";
		$note .= "\n";
		$note .= '--------------------------------------'."\n\n";
		break;
		case 'GB':
		$mode_reglement_id = dol_getIdFromCode($db,'LIQ','c_paiement');
		$cond_reglement_id = 0;
		$note .= $langs->trans("Giftcard")."\n";
		$note .= $langs->trans("Received").' : '.$arrValue['codebar'].'-'.$arrValue['codecontrol']."\n";
	// $note .= $langs->trans("Rendu").' : '.$obj_facturation->montantRendu()." ".$conf->currency."\n";
		$note .= "\n";
		$note .= '--------------------------------------'."\n\n";
		break;
		case 'CB':
		$mode_reglement_id = dol_getIdFromCode($db,'CB','c_paiement');
		$cond_reglement_id = 0;
		break;
		case 'CHQ':
		$mode_reglement_id = dol_getIdFromCode($db,'CHQ','c_paiement');
		$cond_reglement_id = 0;
		break;
	}
	if (empty($mode_reglement_id)) $mode_reglement_id=0;

	return array($mode_reglement_id,$cond_reglement_id,$note);
}
//busca el mode reglament en base a un array
function search_mode_reglaments($obj_facturation,$arrValue,$payment,$note='')
{
	global $langs, $conf,$db;

	switch ( $payment )
	{
		case 'DIF':
		$mode_reglement_id = 0;
		$cond_reglement_id = dol_getIdFromCode($db,'RECEP','cond_reglement','code','rowid');
	//$cond_reglement_id = 0;
		break;
		case 'ESP':
		$mode_reglement_id = dol_getIdFromCode($db,'LIQ','c_paiement');
		$cond_reglement_id = 0;
		$note .= $langs->trans("Cash")."\n";
		$note .= $langs->trans("Received").' : '.$obj_facturation->montantEncaisse()." ".$conf->currency."\n";
		$note .= $langs->trans("Rendu").' : '.$obj_facturation->montantRendu()." ".$conf->currency."\n";
		$note .= "\n";
		$note .= '--------------------------------------'."\n\n";
		break;
		case 'GB':
		$mode_reglement_id = dol_getIdFromCode($db,'GB','c_paiement');
		$cond_reglement_id = 0;
		$note .= $langs->trans("Giftcard")."\n";
		$note .= $langs->trans("Received").' : '.$arrValue['codebar'].'-'.$arrValue['codecontrol']."\n";
	// $note .= $langs->trans("Rendu").' : '.$obj_facturation->montantRendu()." ".$conf->currency."\n";
		$note .= "\n";
		$note .= '--------------------------------------'."\n\n";
		break;
		case 'CB':
		$mode_reglement_id = dol_getIdFromCode($db,'CB','c_paiement');
		$cond_reglement_id = 0;
		break;
		case 'CHQ':
		$mode_reglement_id = dol_getIdFromCode($db,'CHQ','c_paiement');
		$cond_reglement_id = 0;
		break;
	}
	if (empty($mode_reglement_id)) $mode_reglement_id=0;

	return array($mode_reglement_id,$cond_reglement_id,$note);
}
//verifica si esta cerrado la caja
function verif_bankclose()
{

}

//lista los pedidos por sucursal
function list_commande_sale($fk_subsidiary,$statut=NULL)
{
	global $db, $langs;
	if ($fk_subsidiary <=0)
		return -1;
	$sql = 'SELECT c.rowid, c.date_creation, c.ref, c.fk_soc, c.fk_user_author, c.fk_statut';
	$sql.= ', c.amount_ht, c.total_ht, c.total_ttc, c.tva as total_tva, c.localtax1 as total_localtax1, c.localtax2 as total_localtax2, c.fk_cond_reglement, c.fk_mode_reglement, c.fk_availability, c.fk_input_reason';
	$sql.= ', c.date_commande';
	$sql.= ', c.date_livraison';
	$sql.= ', c.fk_projet, c.remise_percent, c.remise, c.remise_absolue, c.source, c.facture as billed';
	$sql.= ', c.note_private, c.note_public, c.ref_client, c.ref_ext, c.ref_int, c.model_pdf, c.fk_delivery_address, c.extraparams, c.fk_statut';
	$sql.= ' FROM '.MAIN_DB_PREFIX.'commande as c';
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."commande_sale AS cs ON cs.fk_commande = c.rowid ";
	$sql.= " WHERE cs.fk_subsidiary = ".$fk_subsidiary;
	if (!is_null($statut))
	{
		$statut = $statut + 0;
		$sql.= " AND c.fk_statut = ".$statut;
	}
	//echo $sql;exit;
	$result = $db->query($sql);
	$array = array();
	if ($result)
	{
		$i = 0;
		$num = $db->num_rows($result);
		while ($i < $num)
		{
			$objp = $db->fetch_object($result);
			if ($objp)
			{
				$line = new Commande($db);
				$line->rowid            = $objp->rowid;
				$line->fk_commande      = $objp->fk_commande;
				$line->ref              = $objp->ref;
				$line->fk_soc           = $objp->fk_soc;

				$line->fk_parent_line   = $objp->fk_parent_line;
				$line->label            = $objp->custom_label;
				$line->date_commande    = $objp->date_commande;
				$line->date_livraison   = $objp->date_livraison;
				$line->desc             = $objp->description;
				$line->qty              = $objp->qty;
				$line->price            = $objp->price;
				$line->subprice         = $objp->subprice;
				$line->tva_tx           = $objp->tva_tx;
				$line->localtax1_tx		= $objp->localtax1_tx;
				$line->localtax2_tx		= $objp->localtax2_tx;
				$line->remise           = $objp->remise;
				$line->remise_percent   = $objp->remise_percent;
				$line->fk_remise_except = $objp->fk_remise_except;
				$line->fk_product       = $objp->fk_product;
				$line->product_type     = $objp->product_type;
				$line->info_bits        = $objp->info_bits;
				$line->special_code		= $objp->special_code;
				$line->total_ht         = $objp->total_ht;
				$line->total_tva        = $objp->total_tva;
				$line->total_localtax1  = $objp->total_localtax1;
				$line->total_localtax2  = $objp->total_localtax2;
				$line->total_ttc        = $objp->total_ttc;
				$line->fk_fournprice	= $objp->fk_fournprice;
				$line->note_public	    = $objp->note_public;
				$line->note_private		= $objp->note_private;
				$line->billed           = $objp->billed;
				$line->fk_statut        = $objp->fk_statut;
				$array[$i] = $line;
			}
			$i++;
		}
	}
	return $array;
}

//lista los pedidos por sucursal
function list_commande_entrepot($fk_entrepot,$statut=NULL)
{
	global $db, $langs,$user;
	if ($fk_entrepot <=0)
		return -1;
	$sql = 'SELECT c.rowid, c.date_creation, c.ref, c.fk_soc, c.fk_user_author, c.fk_statut';
	$sql.= ', c.amount_ht, c.total_ht, c.total_ttc, c.tva as total_tva, c.localtax1 as total_localtax1, c.localtax2 as total_localtax2, c.fk_cond_reglement, c.fk_mode_reglement, c.fk_availability, c.fk_input_reason';
	$sql.= ', c.date_commande';
	$sql.= ', c.date_livraison';
	$sql.= ', c.fk_projet, c.remise_percent, c.remise, c.remise_absolue, c.source, c.facture as billed';
	$sql.= ', c.note_private, c.note_public, c.ref_client, c.ref_ext, c.ref_int, c.model_pdf, c.fk_delivery_address, c.extraparams, c.fk_statut';
	$sql.= ' FROM '.MAIN_DB_PREFIX.'commande as c';
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."commande_sale AS cs ON cs.fk_commande = c.rowid ";
	$sql.= " WHERE cs.fk_entrepot_end = ".$fk_entrepot;
	if (!is_null($statut))
	{
		$statut = $statut + 0;
		$sql.= " AND c.fk_statut = ".$statut;
	}
	$sql.= " ORDER BY c.date_livraison DESC";
    //echo $sql;
    	if ($user->login == 'ramiro') echo $sql;
	$result = $db->query($sql);
	$array = array();
	if ($result)
	{
		$i = 0;
		$num = $db->num_rows($result);
		while ($i < $num)
		{
			$objp = $db->fetch_object($result);
			if ($objp)
			{
				$line = new Commande($db);
				$line->rowid            = $objp->rowid;
				$line->fk_commande      = $objp->fk_commande;
				$line->ref              = $objp->ref;
				$line->fk_soc           = $objp->fk_soc;

				$line->fk_parent_line   = $objp->fk_parent_line;
				$line->label            = $objp->custom_label;
				$line->date_commande    = $db->jdate($objp->date_commande);
				$line->date_livraison   = $db->jdate($objp->date_livraison);
				$line->desc             = $objp->description;
				$line->qty              = $objp->qty;
				$line->price            = $objp->price;
				$line->subprice         = $objp->subprice;
				$line->tva_tx           = $objp->tva_tx;
				$line->localtax1_tx     = $objp->localtax1_tx;
				$line->localtax2_tx     = $objp->localtax2_tx;
				$line->remise           = $objp->remise;
				$line->remise_percent   = $objp->remise_percent;
				$line->fk_remise_except = $objp->fk_remise_except;
				$line->fk_product       = $objp->fk_product;
				$line->product_type     = $objp->product_type;
				$line->info_bits        = $objp->info_bits;
				$line->special_code     = $objp->special_code;
				$line->total_ht         = $objp->total_ht;
				$line->total_tva        = $objp->total_tva;
				$line->total_localtax1  = $objp->total_localtax1;
				$line->total_localtax2  = $objp->total_localtax2;
				$line->total_ttc        = $objp->total_ttc;
				$line->fk_fournprice    = $objp->fk_fournprice;
				$line->note_public      = $objp->note_public;
				$line->note_private     = $objp->note_private;
				$line->billed           = $objp->billed;
				$line->fk_statut        = $objp->fk_statut;
				$array[$i] = $line;
			}
			$i++;
		}
	}
	return $array;
}

//adicionar registro en v_fiscal
function add_v_fiscal($obj,$modseller,$nit=0,$razsoc='Sin nombre')
{
	global $conf,$db;
	if (empty($modseller))
		$sql = "SELECT t.rowid, t.series, t.num_ini, t.num_fin, t.num_ult, ";
	$sql.= " num_autoriz, t.chave ";
	$sql.= " FROM ".MAIN_DB_PREFIX."v_dosing AS t ";
	$sql.= " WHERE ";
	$sql.= " t.entity = ".$conf->entity;
	$sql.= " AND t.fk_subsidiaryid = ".$_SESSION['fkSubsidiaryid'];
	if ($modseller==2) $sql.= " AND t.lote = 2 ";
	if ($modseller==1) $sql.= " AND t.lote = 1 ";
	echo $sql.= " AND active = 1 ";

	$res1=$db->query($sql);
	if ($res1)
	{
		if ($db->num_rows($res1))
		{
			$objd = $db->fetch_object($res1);
			$llave = $objd->chave;
			$numaut = $objd->num_autoriz;
			if ($modseller==1)
			{
				$numaut    = $_SESSION['numautsel'];
				$newnumfac = $_SESSION['numfactsel'];
			}
			if ($modseller==2)
			{
				if ($objd->num_ult)
					$newnumfac = $objd->num_ult + 1;
				else
					$newnumfac = $objd->num_ini;
			}
			// actualizando el valor
			require_once DOL_DOCUMENT_ROOT.'/ventas/class/vdosing.class.php';
			$objdosing = new Vdosing($db);
			$objdosing->fetch($objd->rowid);
			if ($objdosing->id == $objd->rowid)
			{
				if ($modseller==2) $objdosing->num_ult = $newnumfac;
				if ($modseller==1)
					if ($objdosing->num_ult == $newnumfac)
					{
						$error++;
						$lInvoicechek = true;
						$mesg = $langs->trans('Duplicateinvoicepleasecheck');
					}
					if ($objdosing->num_ult < $newnumfac)
						$objdosing->num_ult = $newnumfac;

					$resultdosing = $objdosing->update($user);
					if ($resultdosing < 0) $error++;
					//llamando el codigo para generar codigo control
					if ($modseller==2)
					{
						require_once DOL_DOCUMENT_ROOT.'/ventas/factura/cc.php';
						$nowtext = date('Y').date('m').date('d');
						if (empty($nit))
						{
							$nit = 0;
							$razsoc = $langs->trans('Sin Nombre');
						}
						$CodContr = new CodigoControl($numaut,$newnumfac,$nit,$nowtext,$obj_facturation->prixTotalTtc(),$llave);
						$codigocontrol = $CodContr->generar();
					}
					else
						$codigocontrol = '';

					//agregando a libros fiscales
					require_once DOL_DOCUMENT_ROOT.'/ventas/class/vfiscal.class.php';

					$objvfis = new Vfiscal($db);
					$objvfis->entity = $conf->entity;
					$objvfis->nfiscal = $newnumfac;
					$objvfis->serie   = $objd->series;
					$objvfis->fk_dosing = $objd->rowid;
					$objvfis->fk_facture = $id;
					$objvfis->fk_cliepro = $thirdpartyid;
					$objvfis->nit = $nit;
					$objvfis->razsoc = $razsoc;
					$objvfis->date_exp = $now;
					$objvfis->type_op = 1; // venta
					$objvfis->num_autoriz = $numaut;
					$objvfis->cod_control = $codigocontrol;
					$objvfis->baseimp1 = $obj->total_ttc; //valor total
					$objvfis->valimp1 = $obj_facturation->montantTva(); //valor descontado
					//$objvfis->aliqimp1 = $fk_tva;
					$objvfis->aliqimp1 = empty($tab_tva['taux'])?$fk_tva:$tab_tva['taux']; //aliquota

					//agregando cambio y pago
					$objvfis->amount_payment = $_SESSION['sum_payment'];
					$objvfis->amount_balance = $_SESSION['balance'];
					$objvfis->date_create=dol_now();
					$objvfis->fk_user_create = $user->id;
					$objvfis->status = 1;
					$idvfiscal = $objvfis->create($user);
					if ($idvfiscal < 0)
					{
						$error++;
					}
				}
				else
				{
					$error++;
				}
			}
			else
				return -1;
		}
		else
			return -1;
		if ($error>0)
			return -1;
		else
			return 1;
	}

/**
	 *  Return HTML table for object lines
	 *  TODO Move this into an output class file (htmlline.class.php)
	 *  If lines are into a template, title must also be into a template
	 *  But for the moment we don't know if it'st possible as we keep a method available on overloaded objects.
	 *
	 *  @param  string      $action             Action code
	 *  @param  string      $seller             Object of seller third party
	 *  @param  string      $buyer              Object of buyer third party
	 *  @param  string      $selected           Object line selected
	 *  @param  int         $dateSelector       1=Show also date range input fields
	 *  @return void
*/
function printObjectLinesAdd($action, $seller, $buyer, $selected=0, $dateSelector=0)
{
	global $db,$conf,$langs,$user,$object,$hookmanager;

	print '<tr class="liste_titre nodrag nodrop">';

	if (! empty($conf->global->MAIN_VIEW_LINE_NUMBER)) print '<td align="center" width="5">&nbsp;</td>';

		// Description
	print '<td>'.$langs->trans('Description').'</td>';

		// VAT
	print '<td align="right" width="50">'.$langs->trans('VAT').'</td>';

		// Price HT
	print '<td align="right" width="80">'.$langs->trans('PriceUHT').'</td>';

	if ($conf->global->MAIN_FEATURES_LEVEL > 1) print '<td align="right" width="80">&nbsp;</td>';

		// Qty
	print '<td align="right" width="50">'.$langs->trans('Qty').'</td>';

		// Reduction short
	print '<td align="right" width="50">'.$langs->trans('ReductionShort').'</td>';

	if (! empty($conf->margin->enabled) && empty($user->societe_id))
	{
		if ($conf->global->MARGIN_TYPE == "1")
			print '<td align="right" width="80">'.$langs->trans('BuyingPrice').'</td>';
		else
			print '<td align="right" width="80">'.$langs->trans('CostPrice').'</td>';

		if (! empty($conf->global->DISPLAY_MARGIN_RATES) && $user->rights->margins->liretous)
			print '<td align="right" width="50">'.$langs->trans('MarginRate').'</td>';
		if (! empty($conf->global->DISPLAY_MARK_RATES) && $user->rights->margins->liretous)
			print '<td align="right" width="50">'.$langs->trans('MarkRate').'</td>';
	}

	// Total HT
	print '<td align="right" width="50">'.$langs->trans('TotalHTShort').'</td>';

	print '<td></td>';  // No width to allow autodim

	print '<td width="10"></td>';

	print '<td width="10"></td>';

	print "</tr>\n";

	$num = count($object->lines);
	$var = true;
	$i   = 0;

	//Line extrafield
	require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
	$extrafieldsline = new ExtraFields($db);
	$extralabelslines=$extrafieldsline->fetch_name_optionals_label($object->table_element_line);
	foreach ($object->lines as $line)
	{
		//Line extrafield
		$line->fetch_optionals($line->id,$extralabelslines);

		$var=!$var;
		if (is_object($hookmanager) && (($line->product_type == 9 && ! empty($line->special_code)) || ! empty($line->fk_parent_line)))
		{
			if (empty($line->fk_parent_line))
			{
				$parameters = array('line'=>$line,'var'=>$var,'num'=>$num,'i'=>$i,'dateSelector'=>$dateSelector,'seller'=>$seller,'buyer'=>$buyer,'selected'=>$selected, 'extrafieldsline'=>$extrafieldsline);
				$reshook=$hookmanager->executeHooks('printObjectLine', $parameters, $object, $action);    // Note that $action and $object may have been modified by some hooks
			}
		}
		else
		{
			printObjectLineAdd($action,$line,$var,$num,$i,$dateSelector,$seller,$buyer,$selected,$extrafieldsline);
		}

		$i++;
	}
}

	/**
	 *	Return HTML content of a detail line
	 *	TODO Move this into an output class file (htmlline.class.php)
	 *
	 *	@param	string		$action				GET/POST action
	 *	@param	array	    $line		       	Selected object line to output
	 *	@param  string	    $var               	Is it a an odd line (true)
	 *	@param  int		    $num               	Number of line (0)
	 *	@param  int		    $i					I
	 *	@param  int		    $dateSelector      	1=Show also date range input fields
	 *	@param  string	    $seller            	Object of seller third party
	 *	@param  string	    $buyer             	Object of buyer third party
	 *	@param	string		$selected		   	Object line selected
	 *  @param  object		$extrafieldsline	Object of extrafield line attribute
	 *	@return	void
	 */
	function printObjectLineAdd($action,$line,$var,$num,$i,$dateSelector,$seller,$buyer,$selected=0,$extrafieldsline=0)
	{
		global $db,$conf,$langs,$user,$object,$hookmanager;
		global $form,$bc,$bcdd;

		$element=$object->element;
		$subelement = '';
		//cambiando permisos segun ventas
		if ($element == 'commande')
		{
			$subelement = $element;
			$element = 'ventas';
		}

		$text=''; $description=''; $type=0;

		// Show product and description
		$type=(! empty($line->product_type)?$line->product_type:$line->fk_product_type);
		// Try to enhance type detection using date_start and date_end for free lines where type was not saved.
		if (! empty($line->date_start)) $type=1; // deprecated
		if (! empty($line->date_end)) $type=1; // deprecated

		// Ligne en mode visu
		if ($action != 'editline' || $selected != $line->id)
		{
			//cambiando valores para impresion
			if ($conf->global->VENTA_PRICE_TAXES_INCLUDED)
			{
				$line->subprice = price2num($line->total_ttc / $line->qty,'MU');
				$line->total_ht = $line->total_ttc;
			}
			// Product
			if ($line->fk_product > 0)
			{
				$product_static = new Product($db);

				$product_static->type=$line->fk_product_type;
				$product_static->id=$line->fk_product;
				$product_static->ref=$line->ref;
				$text=$product_static->getNomUrl(1);

				// Define output language and label
				if (! empty($conf->global->MAIN_MULTILANGS))
				{
					if (! is_object($object->thirdparty))
					{
						dol_print_error('','Error: Method printObjectLine was called on an object and object->fetch_thirdparty was not done before');
						return;
					}

					$prod = new Product($db);
					$prod->fetch($line->fk_product);

					$outputlangs = $langs;
					$newlang='';
					if (empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
					if (! empty($conf->global->PRODUIT_TEXTS_IN_THIRDPARTY_LANGUAGE) && empty($newlang)) $newlang=$object->thirdparty->default_lang;		// For language to language of customer
					if (! empty($newlang))
					{
						$outputlangs = new Translate("",$conf);
						$outputlangs->setDefaultLang($newlang);
					}

					$label = (! empty($prod->multilangs[$outputlangs->defaultlang]["label"])) ? $prod->multilangs[$outputlangs->defaultlang]["label"] : $line->product_label;
				}
				else
				{
					$label = $line->product_label;
				}

				$text.= ' - '.(! empty($line->label)?$line->label:$label);
				$description=(! empty($conf->global->PRODUIT_DESC_IN_FORM)?'':dol_htmlentitiesbr($line->description));
			}

			// Output template part (modules that overwrite templates must declare this into descriptor)
			// Use global variables + $dateSelector + $seller and $buyer
			$dirtpls=array_merge($conf->modules_parts['tpl'],array('/ventas/tpl'));
			foreach($dirtpls as $reldir)
			{
				$tpl = dol_buildpath($reldir.'/objectline_view.tpl.php');
				if (empty($conf->file->strict_mode)) {
					$res=@include $tpl;
				} else {
					$res=include $tpl; // for debug
				}
				if ($res) break;
			}
		}

		// Ligne en mode update
		if ($object->statut == 0 && $action == 'editline' && $selected == $line->id)
		{
			$label = (! empty($line->label) ? $line->label : (($line->fk_product > 0) ? $line->product_label : ''));
			if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("Label").'"';
			else $placeholder=' title="'.$langs->trans("Label").'"';

			//cambiando valores para impresion
			if ($conf->global->VENTA_PRICE_TAXES_INCLUDED)
			{
				$line->subprice = price2num($line->total_ttc / $line->qty,'MU');
				$line->total_ht = $line->total_ttc;
			}
			if ($conf->global->VENTA_PRICE_TAXES_INCLUDED)
				$pu_ttc = price2num($line->subprice * (1 + ($line->tva_tx/100)), 'MU');

			// Output template part (modules that overwrite templates must declare this into descriptor)
			// Use global variables + $dateSelector + $seller and $buyer
			$dirtpls=array_merge($conf->modules_parts['tpl'],array('/ventas/tpl'));
			foreach($dirtpls as $reldir)
			{
				$tpl = dol_buildpath($reldir.'/objectline_edit.tpl.php');
				if (empty($conf->file->strict_mode)) {
					$res=@include $tpl;
				} else {
					$res=include $tpl; // for debug
				}
				if ($res) break;
			}
		}
	}

	/**
 * Security check when accessing to a document (used by document.php, viewimage.php and webservices)
 *
 * @param	string	$modulepart			Module of document (module, module_user_temp, module_user or module_temp)
 * @param	string	$original_file		Relative path with filename
 * @param	string	$entity				Restrict onto entity
 * @param  	User	$fuser				User object (forced)
 * @param	string	$refname			Ref of object to check permission for external users (autodetect if not provided)
 * @return	mixed						Array with access information : accessallowed & sqlprotectagainstexternals & original_file (as full path name)
 */
	function ventas_check_secure_access_document($modulepart,$original_file,$entity,$fuser='',$refname='')
	{
		global $user, $conf, $db;

		if (! is_object($fuser)) $fuser=$user;

		if (empty($modulepart)) return 'ErrorBadParameter';
		if (empty($entity)) $entity=0;
		dol_syslog('modulepart='.$modulepart.' original_file='.$original_file);
	// We define $accessallowed and $sqlprotectagainstexternals
		$accessallowed=0;
		$sqlprotectagainstexternals='';
		$ret=array();
	// find the subdirectory name as the reference
		if (empty($refname)) $refname=basename(dirname($original_file)."/");

	// Wrapping for some images
		if ($modulepart == 'companylogo')
		{
			$accessallowed=1;
			$original_file=$conf->mycompany->dir_output.'/logos/'.$original_file;
		}
	// Wrapping for users photos
		elseif ($modulepart == 'userphoto')
		{
			$accessallowed=1;
			$original_file=$conf->user->dir_output.'/'.$original_file;
		}
	// Wrapping for members photos
		elseif ($modulepart == 'memberphoto')
		{
			$accessallowed=1;
			$original_file=$conf->adherent->dir_output.'/'.$original_file;
		}
	// Wrapping pour les apercu factures
		elseif ($modulepart == 'apercufacture')
		{
			if ($fuser->rights->facture->lire) $accessallowed=1;
			$original_file=$conf->facture->dir_output.'/'.$original_file;
		}
	// Wrapping pour les apercu propal
		elseif ($modulepart == 'apercupropal')
		{
			if ($fuser->rights->propale->lire) $accessallowed=1;
			$original_file=$conf->propal->dir_output.'/'.$original_file;
		}
	// Wrapping pour les apercu commande
		elseif ($modulepart == 'apercucommande')
		{
			if ($fuser->rights->commande->lire) $accessallowed=1;
			$original_file=$conf->commande->dir_output.'/'.$original_file;
		}
	// Wrapping pour les apercu intervention
		elseif ($modulepart == 'apercufichinter')
		{
			if ($fuser->rights->ficheinter->lire) $accessallowed=1;
			$original_file=$conf->ficheinter->dir_output.'/'.$original_file;
		}
	// Wrapping pour les images des stats propales
		elseif ($modulepart == 'propalstats')
		{
			if ($fuser->rights->propale->lire) $accessallowed=1;
			$original_file=$conf->propal->dir_temp.'/'.$original_file;
		}
	// Wrapping pour les images des stats commandes
		elseif ($modulepart == 'orderstats')
		{
			if ($fuser->rights->commande->lire) $accessallowed=1;
			$original_file=$conf->commande->dir_temp.'/'.$original_file;
		}
		elseif ($modulepart == 'orderstatssupplier')
		{
			if ($fuser->rights->fournisseur->commande->lire) $accessallowed=1;
			$original_file=$conf->fournisseur->dir_output.'/commande/temp/'.$original_file;
		}
	// Wrapping pour les images des stats factures
		elseif ($modulepart == 'billstats')
		{
			if ($fuser->rights->facture->lire) $accessallowed=1;
			$original_file=$conf->facture->dir_temp.'/'.$original_file;
		}
		elseif ($modulepart == 'billstatssupplier')
		{
			if ($fuser->rights->fournisseur->facture->lire) $accessallowed=1;
			$original_file=$conf->fournisseur->dir_output.'/facture/temp/'.$original_file;
		}
	// Wrapping pour les images des stats expeditions
		elseif ($modulepart == 'expeditionstats')
		{
			if ($fuser->rights->expedition->lire) $accessallowed=1;
			$original_file=$conf->expedition->dir_temp.'/'.$original_file;
		}
	// Wrapping pour les images des stats expeditions
		elseif ($modulepart == 'tripsexpensesstats')
		{
			if ($fuser->rights->deplacement->lire) $accessallowed=1;
			$original_file=$conf->deplacement->dir_temp.'/'.$original_file;
		}
	// Wrapping pour les images des stats expeditions
		elseif ($modulepart == 'memberstats')
		{
			if ($fuser->rights->adherent->lire) $accessallowed=1;
			$original_file=$conf->adherent->dir_temp.'/'.$original_file;
		}
	// Wrapping pour les images des stats produits
		elseif (preg_match('/^productstats_/i',$modulepart))
		{
			if ($fuser->rights->produit->lire || $fuser->rights->service->lire) $accessallowed=1;
			$original_file=(!empty($conf->product->multidir_temp[$entity])?$conf->product->multidir_temp[$entity]:$conf->service->multidir_temp[$entity]).'/'.$original_file;
		}
	// Wrapping for products or services
		elseif ($modulepart == 'tax')
		{
			if ($fuser->rights->tax->charges->lire) $accessallowed=1;
			$original_file=$conf->tax->dir_output.'/'.$original_file;
		}
	// Wrapping for products or services
		elseif ($modulepart == 'actions')
		{
			if ($fuser->rights->agenda->myactions->read) $accessallowed=1;
			$original_file=$conf->agenda->dir_output.'/'.$original_file;
		}
	// Wrapping for categories
		elseif ($modulepart == 'category')
		{
			if ($fuser->rights->categorie->lire) $accessallowed=1;
			$original_file=$conf->categorie->multidir_output[$entity].'/'.$original_file;
		}
	// Wrapping pour les prelevements
		elseif ($modulepart == 'prelevement')
		{
			if ($fuser->rights->prelevement->bons->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->prelevement->dir_output.'/'.$original_file;
		}
	// Wrapping pour les graph energie
		elseif ($modulepart == 'graph_stock')
		{
			$accessallowed=1;
			$original_file=$conf->stock->dir_temp.'/'.$original_file;
		}
	// Wrapping pour les graph fournisseurs
		elseif ($modulepart == 'graph_fourn')
		{
			$accessallowed=1;
			$original_file=$conf->fournisseur->dir_temp.'/'.$original_file;
		}
	// Wrapping pour les graph des produits
		elseif ($modulepart == 'graph_product')
		{
			$accessallowed=1;
			$original_file=$conf->product->multidir_temp[$entity].'/'.$original_file;
		}
	// Wrapping pour les code barre
		elseif ($modulepart == 'barcode')
		{
			$accessallowed=1;
		// If viewimage is called for barcode, we try to output an image on the fly,
		// with not build of file on disk.
		//$original_file=$conf->barcode->dir_temp.'/'.$original_file;
			$original_file='';
		}
	// Wrapping pour les icones de background des mailings
		elseif ($modulepart == 'iconmailing')
		{
			$accessallowed=1;
			$original_file=$conf->mailing->dir_temp.'/'.$original_file;
		}
	// Wrapping pour les icones de background des mailings
		elseif ($modulepart == 'scanner_user_temp')
		{
			$accessallowed=1;
			$original_file=$conf->scanner->dir_temp.'/'.$fuser->id.'/'.$original_file;
		}
	// Wrapping pour les images fckeditor
		elseif ($modulepart == 'fckeditor')
		{
			$accessallowed=1;
			$original_file=$conf->fckeditor->dir_output.'/'.$original_file;
		}

	// Wrapping for third parties
		else if ($modulepart == 'company' || $modulepart == 'societe')
		{
			if ($fuser->rights->societe->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->societe->multidir_output[$entity].'/'.$original_file;
			$sqlprotectagainstexternals = "SELECT rowid as fk_soc FROM ".MAIN_DB_PREFIX."societe WHERE rowid='".$db->escape($refname)."' AND entity IN (".getEntity('societe', 1).")";
		}

	// Wrapping for invoices
		else if ($modulepart == 'facture' || $modulepart == 'invoice')
		{
		//print_r($fuser);
			if ($fuser->rights->ventas->fact->leer || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->facture->dir_output.'/'.$original_file;
			$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."facture WHERE facnumber='".$db->escape($refname)."' AND entity=".$conf->entity;
		}

		else if ($modulepart == 'unpaid')
		{
			if ($fuser->rights->facture->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->facture->dir_output.'/unpaid/temp/'.$original_file;
		}

	// Wrapping pour les fiches intervention
		else if ($modulepart == 'ficheinter')
		{
			if ($fuser->rights->ficheinter->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->ficheinter->dir_output.'/'.$original_file;
			$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."fichinter WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
		}

	// Wrapping pour les deplacements et notes de frais
		else if ($modulepart == 'deplacement')
		{
			if ($fuser->rights->deplacement->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->deplacement->dir_output.'/'.$original_file;
		//$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."fichinter WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
		}
	// Wrapping pour les propales
		else if ($modulepart == 'propal')
		{
			if ($fuser->rights->propale->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}

			$original_file=$conf->propal->dir_output.'/'.$original_file;
			$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."propal WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
		}

	// Wrapping pour les commandes
		else if ($modulepart == 'commande' || $modulepart == 'order')
		{
			if ($fuser->rights->ventas->leercommande || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->commande->dir_output.'/'.$original_file;
			$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."commande WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
		}

	// Wrapping pour les projets
		else if ($modulepart == 'project')
		{
			if ($fuser->rights->projet->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->projet->dir_output.'/'.$original_file;
			$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."projet WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
		}
		else if ($modulepart == 'project_task')
		{
			if ($fuser->rights->projet->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->projet->dir_output.'/'.$original_file;
			$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."projet WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
		}

	// Wrapping pour les commandes fournisseurs
		else if ($modulepart == 'commande_fournisseur' || $modulepart == 'order_supplier')
		{
			if ($fuser->rights->fournisseur->commande->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->fournisseur->commande->dir_output.'/'.$original_file;
			$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."commande_fournisseur WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
		}

	// Wrapping pour les factures fournisseurs
		else if ($modulepart == 'facture_fournisseur' || $modulepart == 'invoice_supplier')
		{
			if ($fuser->rights->fournisseur->facture->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->fournisseur->facture->dir_output.'/'.$original_file;
			$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."facture_fourn WHERE facnumber='".$db->escape($refname)."' AND entity=".$conf->entity;
		}

	// Wrapping pour les rapport de paiements
		else if ($modulepart == 'facture_paiement')
		{
			if ($fuser->rights->facture->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			if ($fuser->societe_id > 0) $original_file=$conf->facture->dir_output.'/payments/private/'.$fuser->id.'/'.$original_file;
			else $original_file=$conf->facture->dir_output.'/payments/'.$original_file;
		}

	// Wrapping pour les exports de compta
		else if ($modulepart == 'export_compta')
		{
			if ($fuser->rights->compta->ventilation->creer || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->compta->dir_output.'/'.$original_file;
		}

	// Wrapping pour les expedition
		else if ($modulepart == 'expedition')
		{
			if ($fuser->rights->expedition->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->expedition->dir_output."/sending/".$original_file;
		}

	// Wrapping pour les bons de livraison
		else if ($modulepart == 'livraison')
		{
			if ($fuser->rights->expedition->livraison->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->expedition->dir_output."/receipt/".$original_file;
		}

	// Wrapping pour les actions
		else if ($modulepart == 'actions')
		{
			if ($fuser->rights->agenda->myactions->read || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->agenda->dir_output.'/'.$original_file;
		}

	// Wrapping pour les actions
		else if ($modulepart == 'actionsreport')
		{
			if ($fuser->rights->agenda->allactions->read || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file = $conf->agenda->dir_temp."/".$original_file;
		}

	// Wrapping pour les produits et services
		else if ($modulepart == 'product' || $modulepart == 'produit' || $modulepart == 'service')
		{
			if (($fuser->rights->produit->lire || $fuser->rights->service->lire) || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			if (! empty($conf->product->enabled)) $original_file=$conf->product->multidir_output[$entity].'/'.$original_file;
			elseif (! empty($conf->service->enabled)) $original_file=$conf->service->multidir_output[$entity].'/'.$original_file;
		}

	// Wrapping pour les contrats
		else if ($modulepart == 'contract')
		{
			if ($fuser->rights->contrat->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->contrat->dir_output.'/'.$original_file;
		}

	// Wrapping pour les dons
		else if ($modulepart == 'donation')
		{
			if ($fuser->rights->don->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->don->dir_output.'/'.$original_file;
		}

	// Wrapping pour les remises de cheques
		else if ($modulepart == 'remisecheque')
		{
			if ($fuser->rights->banque->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}

			$original_file=$conf->banque->dir_output.'/bordereau/'.get_exdir(basename($original_file,".pdf"),2,1).$original_file;
		}

	// Wrapping for export module
		else if ($modulepart == 'export')
		{
		// Aucun test necessaire car on force le rep de download sur
		// le rep export qui est propre a l'utilisateur
			$accessallowed=1;
			$original_file=$conf->export->dir_temp.'/'.$fuser->id.'/'.$original_file;
		}

	// Wrapping for import module
		else if ($modulepart == 'import')
		{
		// Aucun test necessaire car on force le rep de download sur
		// le rep export qui est propre a l'utilisateur
			$accessallowed=1;
			$original_file=$conf->import->dir_temp.'/'.$original_file;
		}

	// Wrapping pour l'editeur wysiwyg
		else if ($modulepart == 'editor')
		{
		// Aucun test necessaire car on force le rep de download sur
		// le rep export qui est propre a l'utilisateur
			$accessallowed=1;
			$original_file=$conf->fckeditor->dir_output.'/'.$original_file;
		}

	// Wrapping pour les backups
		else if ($modulepart == 'systemtools')
		{
			if ($fuser->admin)
			{
				$accessallowed=1;
			}
			$original_file=$conf->admin->dir_output.'/'.$original_file;
		}

	// Wrapping for upload file test
		else if ($modulepart == 'admin_temp')
		{
			if ($fuser->admin)
				$accessallowed=1;
			$original_file=$conf->admin->dir_temp.'/'.$original_file;
		}

	// Wrapping pour BitTorrent
		else if ($modulepart == 'bittorrent')
		{
			$accessallowed=1;
			$dir='files';
			if (dol_mimetype($original_file) == 'application/x-bittorrent') $dir='torrents';
			$original_file=$conf->bittorrent->dir_output.'/'.$dir.'/'.$original_file;
		}

	// Wrapping pour Foundation module
		else if ($modulepart == 'member')
		{
			if ($fuser->rights->adherent->lire || preg_match('/^specimen/i',$original_file))
			{
				$accessallowed=1;
			}
			$original_file=$conf->adherent->dir_output.'/'.$original_file;
		}

	// Wrapping for Scanner
		else if ($modulepart == 'scanner_user_temp')
		{
			$accessallowed=1;
			$original_file=$conf->scanner->dir_temp.'/'.$fuser->id.'/'.$original_file;
		}

    // GENERIC Wrapping
    // If modulepart=module_user_temp	Allows any module to open a file if file is in directory called DOL_DATA_ROOT/modulepart/temp/iduser
    // If modulepart=module_temp		Allows any module to open a file if file is in directory called DOL_DATA_ROOT/modulepart/temp
    // If modulepart=module_user		Allows any module to open a file if file is in directory called DOL_DATA_ROOT/modulepart/iduser
    // If modulepart=module				Allows any module to open a file if file is in directory called DOL_DATA_ROOT/modulepart
		else
		{
		// Define $accessallowed
			if (preg_match('/^([a-z]+)_user_temp$/i',$modulepart,$reg))
			{
				if ($fuser->rights->$reg[1]->lire || $fuser->rights->$reg[1]->read || ($fuser->rights->$reg[1]->download)) $accessallowed=1;
				$original_file=$conf->$reg[1]->dir_temp.'/'.$fuser->id.'/'.$original_file;
			}
			else if (preg_match('/^([a-z]+)_temp$/i',$modulepart,$reg))
			{
				if ($fuser->rights->$reg[1]->lire || $fuser->rights->$reg[1]->read || ($fuser->rights->$reg[1]->download)) $accessallowed=1;
				$original_file=$conf->$reg[1]->dir_temp.'/'.$original_file;
			}
			else if (preg_match('/^([a-z]+)_user$/i',$modulepart,$reg))
			{
				if ($fuser->rights->$reg[1]->lire || $fuser->rights->$reg[1]->read || ($fuser->rights->$reg[1]->download)) $accessallowed=1;
				$original_file=$conf->$reg[1]->dir_output.'/'.$fuser->id.'/'.$original_file;
			}
			else
			{
			if (empty($conf->$modulepart->dir_output))	// modulepart not supported
			{
				dol_print_error('','Error call dol_check_secure_access_document with not supported value for modulepart parameter ('.$modulepart.')');
				exit;
			}

			$perm=GETPOST('perm');
			$subperm=GETPOST('subperm');
			if ($perm || $subperm)
			{
				if (($perm && ! $subperm && $fuser->rights->$modulepart->$perm) || ($perm && $subperm && $fuser->rights->$modulepart->$perm->$subperm)) $accessallowed=1;
				$original_file=$conf->$modulepart->dir_output.'/'.$original_file;
			}
			else
			{
				if ($fuser->rights->$modulepart->lire || $fuser->rights->$modulepart->read) $accessallowed=1;
				$original_file=$conf->$modulepart->dir_output.'/'.$original_file;
			}
		}
		if (preg_match('/^specimen/i',$original_file))	$accessallowed=1;    // If link to a specimen
		if ($fuser->admin) $accessallowed=1;    // If user is admin

		// For modules who wants to manage different levels of permissions for documents
		$subPermCategoryConstName = strtoupper($modulepart).'_SUBPERMCATEGORY_FOR_DOCUMENTS';
		if (! empty($conf->global->$subPermCategoryConstName))
		{
			$subPermCategory = $conf->global->$subPermCategoryConstName;
			if (! empty($subPermCategory) && (($fuser->rights->$modulepart->$subPermCategory->lire) || ($fuser->rights->$modulepart->$subPermCategory->read) || ($fuser->rights->$modulepart->$subPermCategory->download)))
			{
				$accessallowed=1;
			}
		}

		// Define $sqlprotectagainstexternals for modules who want to protect access using a SQL query.
		$sqlProtectConstName = strtoupper($modulepart).'_SQLPROTECTAGAINSTEXTERNALS_FOR_DOCUMENTS';
		if (! empty($conf->global->$sqlProtectConstName))	// If module want to define its own $sqlprotectagainstexternals
		{
			// Example: mymodule__SQLPROTECTAGAINSTEXTERNALS_FOR_DOCUMENTS = "SELECT fk_soc FROM ".MAIN_DB_PREFIX.$modulepart." WHERE ref='".$db->escape($refname)."' AND entity=".$conf->entity;
			eval('$sqlprotectagainstexternals = "'.$conf->global->$sqlProtectConstName.'";');
		}
	}

	$ret = array(
		'accessallowed' => $accessallowed,
		'sqlprotectagainstexternals'=>$sqlprotectagainstexternals,
		'original_file'=>$original_file
		);

	return $ret;
}

//proceso de carga de datos para venta con pedido
function add_product_sale($rowid,$action='ajout_article',$selTva,$hdnSource='REF',$txtRef,$txtQte=0,$txtPrixUnit=0,$txtPrixTtc=0,$txtRemise=0,$txtTotal=0,$conf_fkentrepot)
{
	global $db,$conf,$obj_facturation;

	$sql = "SELECT p.rowid, p.ref, p.price, p.tva_tx";
	if ($conf->stock->enabled && !empty($conf_fkentrepot))
		$sql.= ", ps.reel";
	$sql.= " FROM ".MAIN_DB_PREFIX."product as p";
	if ($conf->stock->enabled && !empty($conf_fkentrepot))
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_stock as ps ON p.rowid = ps.fk_product AND ps.fk_entrepot = ".$conf_fkentrepot;
	$sql.= " WHERE p.entity IN (".getEntity('product', 1).")";
	// Recuperation des donnees en fonction de la source (liste deroulante ou champ texte) ...
	//echo $sql;
	//echo '<pre>';
	//print_r($_POST);
	//print_r($_GET);
	//echo '</pre>';
	//exit;

	//$hdnSource = GETPOST("hdnSource");
	//$txtRef = GETPOST("txtRef");
	//$rowid = GETPOST("rowid");
	//$txtPrixUnit=GETPOST("txtPrixUnit");
	//$txtQte=GETPOST("txtQte");
	//$txtRemise=GETPOST("txtRemise");
	//$selTva = GETPOST('selTva');
	$txtQte = (empty($txtQte)?1:$txtQte);
	if ( $hdnSource == 'LISTE' )
	{
		$sql.= " AND p.rowid = ".$_POST['selProduit'];
	}
	else if ( $hdnSource == 'REF' )
	{
		if ( $_POST['modifiSelect'] == '1' )
		{
			if(isset($_POST['bottonCantidad']))
				$_SESSION['MODIFY_SELECT'] = 'c';
			if(isset($_POST['bottonPrecio']))
				$_SESSION['MODIFY_SELECT'] = 'p';

			$sql.= " AND p.ref = '".$txtRef[$rowid]."'";
		}
		else
		{
			$sql.= " AND p.ref = '".$txtRef."'";
		}
	}
	//recuperamos de la sesion el producto
	$dataprod = $_SESSION['data_products_all'][$rowid];
	$result = $db->query($sql);
	$lresult = false;

	if (!empty($dataprod))$lresult = true;
	if ($lresult)
	{
		// // ... et enregistrement dans l'objet
		// if ( $db->num_rows($result) )
		//   {
		$ret=array();
		$ret = $dataprod;
		// $tab = $db->fetch_array($result);
		//     foreach ( $tab as $key => $value )
		//       {
		// 	$ret[$key] = $value;
		//       }

		/** add Ditto for MultiPrix*/
		if ($conf->global->PRODUIT_MULTIPRICES)
		{
			//$thirdpartyid = $_SESSION['CASHDESK_ID_THIRDPARTY'];
			$thirdpartyid = $_SESSION['idClient'];

			$productid = $ret['rowid'];

			// $societe = new Societe($db);
			// $societe->fetch($thirdpartyid);

			// $product = new Product($db);
			// $product->fetch($productid);
			if ($_SESSION['MODIFY_SELECT'] == 'p')
				$ret['price'] = $_POST['txtPrice'];

			// if(isset($product->multiprices[$societe->price_level]))
			//   {
			// 	$ret['price'] = $product->multiprices[$societe->price_level];
			// 	$ret['price_ttc'] = $product->multiprices_ttc[$societe->price_level];
			// 	$ret['tva_tx'] = $product->multiprices_tva_tx[$societe->price_level];
			//   }
			if(isset($dataprod['multiprices'][$_SESSION['pricelevel']]))
			{
				$ret['price'] = $dataprod['multiprices'][$_SESSION['pricelevel']];
				$ret['price_ttc'] = $dataprod['multiprices_ttc'][$_SESSION['pricelevel']];
				$ret['tva_tx'] = $dataprod['multiprices_tva_tx'][$_SESSION['pricelevel']];
			}
		}
		/** end add Ditto */
		$obj_facturation->id($ret['id']);
		$obj_facturation->ref($ret['ref']);
		$obj_facturation->stock($ret['reel']);
		$obj_facturation->prix($ret['price']);

		//$obj_facturation->tva($_SESSION['idTva'][1]);
		$obj_facturation->tva($selTva);



		$_SESSION['MODIFY_SELECT'] = 'p';
		$obj_facturation->prix($txtPrixUnit);
		$obj_facturation->price_ttc = $txtPrixTtc;
		$obj_facturation->qte($txtQte);
		$obj_facturation->tva($selTva);
		//echo '<hr>antes de pasar val <pre>';
		//print_r($obj_facturation);
		//echo '</pre><hr>resultado ';

		$obj_facturation->ajoutArticle(true);




		//	    $obj_facturation->tva($ret['tva_tx']);
		// 	    echo '<pre>';
		// 	    print_r($obj_facturation);
		// 	    echo '</pre>';
		// exit;
		// Definition du filtre pour n'afficher que le produit concerne
		if ( $hdnSource == 'LISTE' )
		{
			$filtre = $ret['ref'];
		}
		else if ( $hdnSource == 'REF')
		{
			if ( $_POST['modifiSelect'] == '1' )
			{
				$filtre = $txtRef[$rowid];
			}
			else
			{
				$filtre = $_POST['txtRef'];
			}
			$redirection = DOL_URL_ROOT.'/ventas/affIndex.php?menu=facturation&filtre='.$filtre.'&modifi='.$_POST['modifi'];
		}
		else
		{
			$obj_facturation->raz();

			if ( $hdnSource == 'REF' )
			{
				if ( $_POST['modifiSelect'] == '1' ){
					$redirection = DOL_URL_ROOT.'/ventas/affIndex.php?menu=facturation&filtre='.$_POST['txtRef'][$_POST['rowid']];
				}else{
					$redirection = DOL_URL_ROOT.'/ventas/affIndex.php?menu=facturation&filtre='.$_POST['txtRef'];
				}
			}
			else
			{
				$redirection = DOL_URL_ROOT.'/ventas/affIndex.php?menu=facturation';
			}
		}
	}
	else
	{
		if ( $db->num_rows($result) )
		{
			$ret=array();
			$tab = $db->fetch_array($result);
			foreach ( $tab as $key => $value )
			{
				$ret[$key] = $value;
			}
			$result = true;
		}
		/** end add Ditto */
		$_SESSION['MODIFY_SELECT'] = 'p';
		$obj_facturation->id($rowid);
		$obj_facturation->ref($ret['ref']);
		$obj_facturation->stock($ret['reel']);
		$obj_facturation->prix($txtPrixUnit);
		$obj_facturation->price_ttc = $txtPrixTtc;
		$obj_facturation->qte($txtQte);
		$obj_facturation->tva($selTva);
		//echo '<hr>antes de pasar val <pre>';
		//print_r($obj_facturation);
		//echo '</pre><hr>resultado ';

		$obj_facturation->ajoutArticle(true);
		//echo '<hr>resultado ';
		//print_r($_SESSION['poscart']);
		// Definition du filtre pour n'afficher que le produit concerne
		if ( $hdnSource == 'LISTE' )
		{
			$filtre = $ret['ref'];
		}
		else if ( $hdnSource == 'REF')
		{
			if ( $_POST['modifiSelect'] == '1' )
			{
				$filtre = $txtRef[$rowid];
			}
			else
			{
				$filtre = $_POST['txtRef'];
			}
			$redirection = DOL_URL_ROOT.'/ventas/affIndex.php?menu=facturation&filtre='.$filtre.'&modifi='.$_POST['modifi'];
		}
		else
		{
			$obj_facturation->raz();

			if ( $hdnSource == 'REF' )
			{
				if ( $_POST['modifiSelect'] == '1' ){
					$redirection = DOL_URL_ROOT.'/ventas/affIndex.php?menu=facturation&filtre='.$_POST['txtRef'][$_POST['rowid']];
				}else{
					$redirection = DOL_URL_ROOT.'/ventas/affIndex.php?menu=facturation&filtre='.$_POST['txtRef'];
				}
			}
			else
			{
				$redirection = DOL_URL_ROOT.'/ventas/affIndex.php?menu=facturation';
			}
		}
	}

	$_SESSION['serObjFacturation'] = serialize($obj_facturation);

	return $error;
}

function verifgetpostnit($mesgmanual='')
{
	global $langs;
	//movemos de validation
	$vernit = substr(GETPOST('nit'),0,1);
	$crazsoc = GETPOST('razsoc');
	//validamos si el primer caracter es numerico
	if (!is_numeric($vernit) && isset($_POST['nit']))
	{
		if (!empty($mesgmanual)) $mesgmanual.='<br>';
		$mesgmanual.= $langs->trans('Nit invalido');
		$action = '';
		unset($_POST['action']);
		unset($_GET['action']);
	}
	//validar texto razsoc
	if ((GETPOST('nit')>0 || GETPOST('nit') == 0) && empty($crazsoc) && isset($_POST['nit']))
	{
		if (!empty($mesgmanual)) $mesgmanual.='<br>';
		$mesgmanual.= $langs->trans('Registre nombre del comprador');
		$action = '';
		unset($_POST['action']);
		unset($_GET['action']);
	}
	return $mesgmanual;
}

/**
 *  Return combo list of activated countries, into language of user
 *
 *  @param	string	$selected       Id or Code or Label of preselected country
 *  @param  string	$htmlname       Name of html select object
 *  @param  string	$htmloption     Options html on select object
 *  @return string           		HTML string with select
 */
function select_posoutputtype($selected='',$htmlname='code',$htmloption='',$nameclass='')
{
	global $db,$conf,$langs;

	$langs->load("dict");

	$out='';
	$padreArray=array();
	$label=array();

	$sql = "SELECT rowid, code AS code_iso, label";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_pos_output_type AS e ";
	$sql.= " WHERE active = 1";
	$sql.= " ORDER BY label ASC";

	$resql=$db->query($sql);
	if ($resql)
	{
		$out.= '<select id="select_'.$htmlname.'" class="selectpays '.$nameclass.'" name="'.$htmlname.'" '.$htmloption.'>';
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			$foundselected=false;

			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$countryArray[$i]['rowid'] 	= $obj->rowid;
				$countryArray[$i]['code_iso'] 	= $obj->code_iso;
				$countryArray[$i]['label']	= $obj->label;
				$label[$i] 	= $countryArray[$i]['label'];
				$i++;
			}

			array_multisort($label, SORT_ASC, $countryArray);
			$out.='<option value="-1"'.($id==-1?' selected="selected"':'').'>&nbsp;</option>'."\n";

			foreach ($countryArray as $row)
			{
				if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
				{
					$foundselected=true;
					$out.= '<option value="'.$row['rowid'].'" selected="selected">';
				}
				else
				{
					$out.= '<option value="'.$row['rowid'].'">';
				}
				$out.= $row['label'];
				//if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
				$out.= '</option>';
			}
		}
		$out.= '</select>';
	}
	else
	{
		dol_print_error($db);
	}

	return $out;
}

/**
 *  Return combo list of activated countries, into language of user
 *
 *  @param	string	$selected       Id or Code or Label of preselected country
 *  @param  string	$htmlname       Name of html select object
 *  @param  string	$htmloption     Options html on select object
 *  @return string           		HTML string with select
 */
function fetch_posoutputtype($selected)
{
	global $db,$conf,$langs;

	$langs->load("dict");

	$out='';
	$padreArray=array();
	$label=array();

	$sql = "SELECT rowid, code AS code_iso, label";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_pos_output_type AS e ";
	$sql.= " WHERE e.rowid = ".$selected;

	$resql=$db->query($sql);
	$aArray = array();
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			$obj = $db->fetch_object($resql);
			$aArray['rowid'] 	= $obj->rowid;
			$aArray['code_iso'] 	= $obj->code_iso;
			$aArray['label']	= $obj->label;
			return $aArray;
		}
		return $aArray;
	}
	else
	{
		return -1;
	}
}

function limpiar($String)
{
	$String = str_replace(array('á','à','â','ã','ª','ä'),"a",$String);
	$String = str_replace(array('Á','À','Â','Ã','Ä'),"A",$String);
	$String = str_replace(array('Í','Ì','Î','Ï'),"I",$String);
	$String = str_replace(array('í','ì','î','ï'),"i",$String);
	$String = str_replace(array('é','è','ê','ë'),"e",$String);
	$String = str_replace(array('É','È','Ê','Ë'),"E",$String);
	$String = str_replace(array('ó','ò','ô','õ','ö','º'),"o",$String);
	$String = str_replace(array('Ó','Ò','Ô','Õ','Ö'),"O",$String);
	$String = str_replace(array('ú','ù','û','ü'),"u",$String);
	$String = str_replace(array('Ú','Ù','Û','Ü'),"U",$String);
	$String = str_replace(array('[','^','´','`','¨','~',']'),"",$String);
	$String = str_replace("ç","c",$String);
	$String = str_replace("Ç","C",$String);
	$String = str_replace("ñ","n",$String);
	$String = str_replace("Ñ","N",$String);
	$String = str_replace("Ý","Y",$String);
	$String = str_replace("ý","y",$String);
	return $String;
}
function limpiarString($texto)
{
	return (preg_replace('[^ A-Za-z0-9_-ñÑ]', '', $texto));
}
?>