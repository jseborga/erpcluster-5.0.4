<?php
require_once DOL_DOCUMENT_ROOT.'/fichinter/class/fichinter.class.php';

class Fichinterext extends Fichinter
{

	/**
	 *	Return clicable name (with picto eventually)
	 *
	 *	@param		int		$withpicto		0=_No picto, 1=Includes the picto in the linkn, 2=Picto only
	 *	@param		string	$option			Options
	 *	@return		string					String with URL
	 */
	function getNomUrladd($withpicto=0,$option='')
	{
		global $langs;

		$result='';
        $label = '<u>' . $langs->trans("ShowIntervention") . '</u>';
        if (! empty($this->ref))
            $label .= '<br><b>' . $langs->trans('Ref') . ':</b> '.$this->ref;

        $link = '<a href="'.DOL_URL_ROOT.'/sales/fichinter/card.php?id='.$this->id.'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
		$linkend='</a>';

		$picto='intervention';


        if ($withpicto) $result.=($link.img_object($label, $picto, 'class="classfortooltip"').$linkend);
		if ($withpicto && $withpicto != 2) $result.=' ';
		if ($withpicto != 2) $result.=$link.$this->ref.$linkend;
		return $result;
	}

		/**
	 * 	Return HTML table table of source object lines
	 *  TODO Move this and previous function into output html class file (htmlline.class.php).
	 *  If lines are into a template, title must also be into a template
	 *  But for the moment we don't know if it's possible as we keep a method available on overloaded objects.
	 *
	 *  @return	void
	 */
	function printOriginLinesListadd()
	{
		global $langs, $hookmanager, $conf;

		print '<tr class="liste_titre">';
		print '<td>'.$langs->trans('Ref').'</td>';
		print '<td>'.$langs->trans('Description').'</td>';
		print '<td align="right">'.$langs->trans('VAT').'</td>';
		print '<td align="right">'.$langs->trans('PriceUHT').'</td>';
		if (!empty($conf->multicurrency->enabled)) print '<td align="right">'.$langs->trans('PriceUHTCurrency').'</td>';
		print '<td align="right">'.$langs->trans('Qty').'</td>';
		if($conf->global->PRODUCT_USE_UNITS)
		{
			print '<td align="left">'.$langs->trans('Unit').'</td>';
		}
		print '<td align="right">'.$langs->trans('ReductionShort').'</td></tr>';

		$var = true;
		$i	 = 0;

		foreach ($this->lines as $line)
		{
			$var=!$var;

			if (is_object($hookmanager) && (($line->product_type == 9 && ! empty($line->special_code)) || ! empty($line->fk_parent_line)))
			{
				if (empty($line->fk_parent_line))
				{
					$parameters=array('line'=>$line,'var'=>$var,'i'=>$i);
					$action='';
					$hookmanager->executeHooks('printOriginObjectLine',$parameters,$this,$action);    // Note that $action and $object may have been modified by some hooks
				}
			}
			else
			{
				$this->printOriginLineadd($line,$var);
			}

			$i++;
		}
	}
	/**
	 * 	Return HTML with a line of table array of source object lines
	 *  TODO Move this and previous function into output html class file (htmlline.class.php).
	 *  If lines are into a template, title must also be into a template
	 *  But for the moment we don't know if it's possible as we keep a method available on overloaded objects.
	 *
	 * 	@param	CommonObjectLine	$line		Line
	 * 	@param	string				$var		Var
	 * 	@return	void
	 */
	function printOriginLineadd($line,$var)
	{
		global $langs, $conf;

		//var_dump($line);
		if (!empty($line->date_start))
		{
			$date_start=$line->date_start;
		}
		else
		{
			$date_start=$line->date_debut_prevue;
			if ($line->date_debut_reel) $date_start=$line->date_debut_reel;
		}
		if (!empty($line->date_end))
		{
			$date_end=$line->date_end;
		}
		else
		{
			$date_end=$line->date_fin_prevue;
			if ($line->date_fin_reel) $date_end=$line->date_fin_reel;
		}

		$this->tpl['label'] = '';
		if (! empty($line->fk_parent_line)) $this->tpl['label'].= img_picto('', 'rightarrow');

		if (($line->info_bits & 2) == 2)  // TODO Not sure this is used for source object
		{
			$discount=new DiscountAbsolute($this->db);
			$discount->fk_soc = $this->socid;
			$this->tpl['label'].= $discount->getNomUrl(0,'discount');
		}
		else if (! empty($line->fk_product))
		{
			$productstatic = new Product($this->db);
			$productstatic->id = $line->fk_product;
			$productstatic->ref = $line->ref;
			$productstatic->type = $line->fk_product_type;
			$this->tpl['label'].= $productstatic->getNomUrl(1);
			$this->tpl['label'].= ' - '.(! empty($line->label)?$line->label:$line->product_label);
			// Dates
			if ($line->product_type == 1 && ($date_start || $date_end))
			{
				$this->tpl['label'].= get_date_range($date_start,$date_end);
			}
		}
		else
		{
			$this->tpl['label'].= ($line->product_type == -1 ? '&nbsp;' : ($line->product_type == 1 ? img_object($langs->trans(''),'service') : img_object($langs->trans(''),'product')));
			if (!empty($line->desc)) {
				$this->tpl['label'].=$line->desc;
			}else {
				$this->tpl['label'].= ($line->label ? '&nbsp;'.$line->label : '');
			}
			// Dates
			if ($line->product_type == 1 && ($date_start || $date_end))
			{
				$this->tpl['label'].= get_date_range($date_start,$date_end);
			}
		}

		if (! empty($line->desc))
		{
			if ($line->desc == '(CREDIT_NOTE)')  // TODO Not sure this is used for source object
			{
				$discount=new DiscountAbsolute($this->db);
				$discount->fetch($line->fk_remise_except);
				$this->tpl['description'] = $langs->transnoentities("DiscountFromCreditNote",$discount->getNomUrl(0));
			}
			elseif ($line->desc == '(DEPOSIT)')  // TODO Not sure this is used for source object
			{
				$discount=new DiscountAbsolute($this->db);
				$discount->fetch($line->fk_remise_except);
				$this->tpl['description'] = $langs->transnoentities("DiscountFromDeposit",$discount->getNomUrl(0));
			}
			else
			{
				$this->tpl['description'] = dol_trunc($line->desc,60);
			}
		}
		else
		{
			$this->tpl['description'] = '&nbsp;';
		}

		$this->tpl['vat_rate'] = vatrate($line->tva_tx, true);
		$this->tpl['price'] = price(price2num($line->total_ttc/$line->qty,'MU'));
		$this->tpl['multicurrency_price'] = price($line->multicurrency_subprice);
		$this->tpl['qty'] = (($line->info_bits & 2) != 2) ? $line->qty : '&nbsp;';
		if($conf->global->PRODUCT_USE_UNITS) $this->tpl['unit'] = $line->getLabelOfUnit('long');
		$this->tpl['remise_percent'] = (($line->info_bits & 2) != 2) ? vatrate($line->remise_percent, true) : '&nbsp;';

		// Output template part (modules that overwrite templates must declare this into descriptor)
		// Use global variables + $dateSelector + $seller and $buyer
		$dirtpls=array_merge($conf->modules_parts['tpl'],array('/core/tpl'));
		foreach($dirtpls as $reldir)
		{
			$tpl = dol_buildpath($reldir.'/originproductline.tpl.php');
			if (empty($conf->file->strict_mode)) {
				$res=@include $tpl;
			} else {
				$res=include $tpl; // for debug
			}
			if ($res) break;
		}
	}

}
?>