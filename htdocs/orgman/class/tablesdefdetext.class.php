<?php
require_once DOL_DOCUMENT_ROOT.'/orgman/class/tablesdefdet.class.php';

class Tablesdefdetext extends Tablesdefdet
{
	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *	@param	int		$withpicto			Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option				On what the link point to
	 *  @param	int  	$notooltip			1=Disable tooltip
	 *  @param	int		$maxlen				Max length of visible user name
	 *  @param  string  $morecss            Add more css on link
	 *	@return	string						String with URL
	 */
	function getNomUrltpl($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $db, $conf, $langs;
		global $dolibarr_main_authentication, $dolibarr_main_demo;
		global $menumanager;

		if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

		$result = '';
		$companylink = '';

		$label = '<u>' . $langs->trans("Tabledefdet") . '</u>';
		$label.= '<br>';
		$label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

		$url = DOL_URL_ROOT.'/orgman/tables/'.'carddet.php?id='.$this->fk_table_def.'&idr='.$this->id;

		$linkclose='';
		if (empty($notooltip))
		{
			if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
			{
				$label=$langs->trans("ShowProject");
				$linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
			}
			$linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
			$linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
		}
		else
			$linkclose = ($morecss?' class="'.$morecss.'"':'');

		$linkstart = '<a href="'.$url.'"';
		$linkstart.=$linkclose.'>';
		$linkend='</a>';

		if ($withpicto)
		{
			$result.=($linkstart.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
			if ($withpicto != 2) $result.=' ';
		}
		$result.= $linkstart . $this->ref . $linkend;
		return $result;
	}

	function select_tables($selected='',$htmlname='fk_tables',$htmloption='',$showempty=0,$showlabel=0,$fk_table_def,$amount=0,$help=0,$campo='rowid')
	{
		global $db, $langs, $conf,$user;
		$sql = "SELECT f.rowid, f.range_ini, f.range_fin, f.ref, f.label AS libelle FROM ".MAIN_DB_PREFIX."tables_def_det AS f ";
		$sql.= " WHERE ";
		$sql.= " f.active = 1";
		$sql.= " AND f.fk_table_def = ".$fk_table_def;
		$sql.= " ORDER BY f.label";
		$resql = $db->query($sql);
		$html = '';

		if ($selected <> 0 && $selected == '-1')
		{
			if ($showlabel > 0)
			{
				return $langs->trans('Tobedefined');
			}
		}

		if ($resql)
		{
			$html.= '<select class="form-control" name="'.$htmlname.'" id="select'.$htmlname.'" '.$htmloption.'>';
			if ($showempty)
			{
				$html.= '<option value="0">&nbsp;</option>';
			}

			$num = $db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $db->fetch_object($resql);

					if (!empty($selected) && $selected == $obj->$campo)
					{
						if ($showlabel)
						{
							return $obj->libelle;
						}
						if (!empty($amount))
						{
							if ($amount > $obj->range_ini && $amount <= $obj->range_fin)
								$html.= '<option value="'.$obj->rowid.'" selected="selected">'.$obj->libelle.'</option>';
						}
						else
							$html.= '<option value="'.$obj->rowid.'" selected="selected">'.$obj->libelle.'</option>';
					}
					else
					{
						if (!empty($amount))
						{
							if ($amount > $obj->range_ini && $amount <= $obj->range_fin)
								$html.= '<option value="'.$obj->rowid.'">'.$obj->libelle.'</option>';
						}
						else
							$html.= '<option value="'.$obj->rowid.'">'.$obj->libelle.'</option>';
					}
					$i++;
				}
			}
			$html.= '</select>';
			if ($user->admin && $help) $html.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
			return $html;
		}
	}
}
?>