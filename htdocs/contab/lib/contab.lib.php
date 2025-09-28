<?php
	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength		Max length for labels (0=no limit)
	 *  @return string           		HTML string with select
	 */

	function verif_year()
	{
		global $conf,$db;
		require_once DOL_DOCUMENT_ROOT.'/contab/class/contabperiodo.class.php';
		$periodo = new Contabperiodo($db);
		$aDate = dol_getdate(dol_now());
		if (!isset($_SESSION['period_year']))
		{
			$filteryear = " AND period_year = ".$aDate['year'];
			$res = $periodo->fetchAll('','',0,0,array(1=>1),'AND',$filteryear);
			if ($res > 0)
				$_SESSION['period_year'] = $aDate['year'];
			else
			{
				header('Location: '.DOL_URL_ROOT.'/contab/index.php?action=create');
				exit;
			}
		}
	}
	function select_cta_normal($selected='',$htmlname='cta_normal',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
	{
		global $conf,$langs;

		$langs->load("contab@contab");

		$out='';
		$countryArray=array();
		$label=array();
		$i = 1;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Deudor');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Acreedor');
		$label[$i] = $countryArray[$i]['label'];
		if ($showLabel)
			return $countryArray[$selected]['label'];
		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);

		return $out;
	}

	function select_month($selected='',$htmlname='mes',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
	{
		global $conf,$langs;

		$langs->load("contab@contab");

		$out='';
		$countryArray=array();
		$label=array();
		$i = 1;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('January');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('February');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('March');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('April');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('May');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('June');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('July');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('August');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('September');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('October');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('November');
		$label[$i] = $countryArray[$i]['rowid'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('December');
		$label[$i] = $countryArray[$i]['rowid'];

		if ($showLabel)
			return $countryArray[$selected]['label'];
		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);

		return $out;
	}

	function select_registry($selected='',$htmlname='registry',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
	{
		global $conf,$langs;

		$langs->load("wages@wages");

		$out='';
		$countryArray=array();
		$label=array();
		$i = 1;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Dialy');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Monthly');
		$label[$i] = $countryArray[$i]['label'];

		if ($showLabel)
			return $countryArray[$selected]['label'];
		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);

		return $out;
	}

	function select_balances($selected='',$htmlname='cta_balances',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
	{
		global $conf,$langs;

		$langs->load("contab@contab");

		$out='';
		$countryArray=array();
		$label=array();
		$i = 1;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Procesamiento');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Saldo anterior');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Saldo actual');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Variacion');
		$label[$i] = $countryArray[$i]['label'];

		if ($showLabel)
			return $countryArray[$selected]['label'];
		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);

		return $out;
	}

	function select_currency($selected='',$htmlname='currency',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
	{
		global $conf,$langs;

		$langs->load("contab@contab");

		$out='';
		$countryArray=array();
		$label=array();
		$i = 1;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Sus');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Bs');
		$label[$i] = $countryArray[$i]['label'];
		if ($showLabel)
			return $countryArray[$selected]['label'];
		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);

		return $out;
	}

	function select_operation($selected='',$htmlname='cta_operation',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
	{
		global $conf,$langs;

		$langs->load("contab");

		$out='';
		$countryArray=array();
		$label=array();
		$i = 1;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Sum');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Subtracts');
		$label[$i] = $countryArray[$i]['label'];
		if ($showLabel)
			return $countryArray[$selected]['label'];
		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);

		return $out;
	}

	function select_cta_clase($selected='',$htmlname='cta_class',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
	{
		global $conf,$langs;
		$langs->load("contab@contab");

		$out='';
		$countryArray=array();
		$label=array();
		$i = 1;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Sinthetic');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Analytical');
		$label[$i] = $countryArray[$i]['label'];
		if ($showLabel)
			return $countryArray[$selected]['label'];

		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);

		return $out;
	}

	function select_cfglan($selected='',$htmlname='cfglan',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
	{
		global $conf,$langs;
		$langs->load("contab@contab");

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
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Singleadministrator');
		$label[$i] = $countryArray[$i]['label'];

		if ($showLabel)
			return $countryArray[$selected]['label'];

		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);
		return $out;
	}

	function select_yesno($selected='',$htmlname='yesno',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
	{
		global $conf,$langs;
		$langs->load("contab@contab");

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

		if ($showLabel)
			return $countryArray[$selected]['label'];

		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);
		return $out;
	}

	function select_status($selected='',$htmlname='status',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
	{
		global $conf,$langs;
		$langs->load("contab@contab");

		$out='';
		$countryArray=array();
		$label=array();
		$i = 1;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Active');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Notactive');
		$label[$i] = $countryArray[$i]['label'];

		if ($showLabel)
			return $countryArray[$selected]['label'];

		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);

		return $out;
	}

	function select_seat($selected='',$htmlname='type_seat',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
	{
		global $conf,$langs;
		$langs->load("contab@contab");

		$out='';
		$countryArray=array();
		$label=array();
		$i = 1;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Debiting');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Accredit');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Doubleentry');
		$label[$i] = $countryArray[$i]['label'];

		if ($showLabel)
			return $countryArray[$selected]['label'];

		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);

		return $out;
	}

	function select_balance($selected='',$htmlname='type_balance',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
	{
		global $conf,$langs;
		$langs->load("contab@contab");

		$out='';
		$countryArray=array();
		$label=array();
		$i = 1;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Real');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Provided');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Frommanagement');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Reserved');
		$label[$i] = $countryArray[$i]['label'];

		if ($showLabel)
			return $countryArray[$selected]['label'];

		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);

		return $out;
	}

	function select_type_seat($selected='',$htmlname='type_seat',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0,$loked=0)
	{
		global $conf,$langs;
		$langs->load("contab@contab");

		$out='';
		$countryArray=array();
		$label=array();
		$i = 1;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Egreso');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Ingreso');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Traspaso');
		$label[$i] = $countryArray[$i]['label'];

		if ($showLabel)
			return $countryArray[$selected]['label'];

		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label,$loked);

		return $out;
	}

	function select_group_seats($selected='',$htmlname='group_seat',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0,$loked=0)
	{
		global $conf,$langs;
		$langs->load("contab@contab");

		$out='';
		$countryArray=array();
		$label=array();
		$i = 1;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Document');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Day');
		$label[$i] = $countryArray[$i]['label'];
		$i++;
		$countryArray[$i]['rowid'] = $i;
		$countryArray[$i]['label'] = $langs->trans('Period');
		$label[$i] = $countryArray[$i]['label'];

		if ($showLabel)
			return $countryArray[$selected]['label'];

		$out = print_select($selected,$htmlname,$htmloption,$maxlength,
			$showempty,$showLabel,$countryArray,$label);

		return $out;
	}

	function print_select($selected='',$htmlname='status',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0,$countryArray,$label,$loked=0)
	{
		if ($loked)
			$htmlloked = 'disabled="disabled"';
		$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.' '.$htmlloked.'>';
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

/**
 *	Return label of statut generico /validate/no validate
 *
 *	@param		int		$state      	Id state
 *	@param      int		$mode        	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
 *  @return     string					Label of statut
 */
function LibState($statut,$mode)
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
		if ($statut==-1) return img_picto($langs->trans('StatusCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceledShort');
		if ($statut==0) return img_picto($langs->trans('StatusDraft'),'statut0').' '.$langs->trans('StatusOrderDraftShort');
		if ($statut==1) return img_picto($langs->trans('StatusValidated'),'statut1').' '.$langs->trans('StatusOrderValidatedShort');
		if ($statut==2) return img_picto($langs->trans('StatusSent'),'statut3').' '.$langs->trans('StatusOrderSentShort');
		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBillShort');
		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('StatusOrderProcessedShort');
	}
	elseif ($mode == 3)
	{
		if ($statut==-1) return img_picto($langs->trans('StatusCanceled'),'statut5');
		if ($statut==0) return img_picto($langs->trans('StatusDraft'),'statut0');
		if ($statut==1) return img_picto($langs->trans('StatusValidated'),'statut1');
		if ($statut==2) return img_picto($langs->trans('StatusSentShort'),'statut3');
		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7');
		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6');
	}
	elseif ($mode == 4)
	{
		if ($statut==-1) return $langs->trans('StatusCanceled').' '.img_picto($langs->trans('StatusCanceled'),'statut5');
		if ($statut==0) return $langs->trans('StatusDraft').' '.img_picto($langs->trans('StatusDraft'),'interrog');
		if ($statut==1) return $langs->trans('StatusValidated').' '.img_picto($langs->trans('StatusValidated'),'tick');
	}
	elseif ($mode == 5)
	{
		if ($statut==-1) return $langs->trans('StatusCanceled').' '.img_picto($langs->trans('StatusCanceled'),'statut5');
		if ($statut==0) return $langs->trans('StatusDraft').' '.img_picto($langs->trans('StatusDraft'),'statut0');
		if ($statut==1) return $langs->trans('StatusValidated').' '.img_picto($langs->trans('StatusValidated'),'statut1');
	}
}

?>