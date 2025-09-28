<?php
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';

class FormProductext extends FormProduct
{
		/**
	 *  Return list of warehouses
	 *
	 *  @param	int		$selected       Id of preselected warehouse ('' for no value, 'ifone'=select value if one value otherwise no value)
	 *  @param  string	$htmlname       Name of html select html
	 *  @param  string	$filtertype     For filter, additional filter on status other then 1
	 *  @param  int		$empty			1=Can be empty, 0 if not
	 * 	@param	int		$disabled		1=Select is disabled
	 * 	@param	int		$fk_product		Add quantity of stock in label for product with id fk_product. Nothing if 0.
	 *  @param	string	$empty_label	Empty label if needed (only if $empty=1)
	 *  @param	int		$showstock		1=show stock count
	 *  @param	int		$forcecombo		force combo iso ajax select2
	 *  @param	array	$events			events to add to select2
	 *  @param  string  $morecss        Add more css classes
	 * 	@return	string					HTML select
	 */
	function selectWarehousesadd($selected='',$htmlname='idwarehouse',$filtertype='',$empty=0,$disabled=0,$fk_product=0,$empty_label='', $showstock=0, $forcecombo=0, $events=array(), $morecss='minwidth200',$eventsel='')
	{
		global $conf,$langs,$user;

		dol_syslog(get_class($this)."::selectWarehouses $selected, $htmlname, $filtertype, $empty, $disabled, $fk_product, $empty_label, $showstock, $forcecombo, $morecss",LOG_DEBUG);

		$out='';

		$this->loadWarehouses($fk_product, '', $filtertype);
		// filter on numeric status
		$nbofwarehouses=count($this->cache_warehouses);

		if ($conf->use_javascript_ajax && ! $forcecombo)
		{
		//	include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
		//	$comboenhancement = ajax_combobox($htmlname, $events);
		//	$out.= $comboenhancement;
		//	$nodatarole=($comboenhancement?' data-role="none"':'');
		}

		$out.='<select class="flat'.($morecss?' '.$morecss:'').'"'.($disabled?' disabled':'').' id="'.$htmlname.'" name="'.($htmlname.($disabled?'_disabled':'')).'"'.$nodatarole.' '.$eventsel.'>';
		if ($empty) $out.='<option value="-1">'.($empty_label?$empty_label:'&nbsp;').'</option>';
		foreach($this->cache_warehouses as $id => $arraytypes)
		{
			$out.='<option value="'.$id.'"';
			if ($selected == $id || ($selected == 'ifone' && $nbofwarehouses == 1)) $out.=' selected';
			$out.='>';
			$out.=$arraytypes['label'];
			if (($fk_product || ($showstock > 0)) && ($arraytypes['stock'] != 0 || ($showstock > 0))) $out.=' ('.$langs->trans("Stock").':'.$arraytypes['stock'].')';
			$out.='</option>';
		}
		$out.='</select>';
		if ($disabled) $out.='<input type="hidden" name="'.$htmlname.'" value="'.(($selected>0)?$selected:'').'">';

		return $out;
	}

}

?>