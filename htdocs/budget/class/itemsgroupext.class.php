<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsgroup.class.php';

class Itemsgroupext extends Itemsgroup
{

	public function form_select($selected='',$htmlname='fk_item',$htmloption='',$showempty=0,$campo='rowid')
	{
		global $user;
		if (count($this->lines)>0)
		{
			$html.= '<select class="flat" name="'.$htmlname.'" '.$htmloption.'>';
			if ($showempty)
			{
				$html.= '<option value="0">&nbsp;</option>';
			}
			if ($selected <> 0 && $selected == '-1')
			{
				$html.= '<option value="-1" selected="selected">'.$langs->trans('To be defined').'</option>';
			}
			$num = count($this->lines);
			$i = 0;
			if ($num)
			{
				foreach ($this->lines AS $j => $obj)
				{
					if (!empty($selected) && $selected == $obj->$campo)
					{
						$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->ref.' v.'.$obj->version.' - '.$obj->detail.'</option>';
					}
					else
					{
						$html.= '<option value="'.$obj->$campo.'">'.$obj->ref.' v.'.$obj->version.' - '.$obj->detail.'</option>';
					}
					$i++;
				}
			}
			$html.= '</select>';
			return $html;
		}
	}


	function getNomUrladd($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $db, $conf, $langs;
		global $dolibarr_main_authentication, $dolibarr_main_demo;
		global $menumanager;

		if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

		$result = '';
		$companylink = '';

		$label = '<u>' . $langs->trans("Item") . '</u>';
		$label.= '<br>';
		$label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;
		$label.= '<br>';
		$label.= '<b>' . $langs->trans('Label') . ':</b> ' . $this->detail;

		$url = DOL_URL_ROOT.'/budget/items/'.'card.php?id='.$this->id;

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
		else $linkclose = ($morecss?' class="'.$morecss.'"':'');

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

	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->status,$mode);
	}

	/**
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatut($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 2) return $langs->trans('Approved');
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Draft');
			if ($status == -1) return $langs->trans('Disabled');
		}
		if ($mode == 1)
		{
			if ($status == 2) return $langs->trans('Approved');
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Draft');
			if ($status == -1) return $langs->trans('Disabled');
		}
		if ($mode == 2)
		{
			if ($status == 2) return img_picto($langs->trans('Approved'),'statut7').' '.$langs->trans('Approved');
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut0').' '.$langs->trans('Draft');
			if ($status == -1) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 3)
		{
			if ($status == 2) return img_picto($langs->trans('Approved'),'statut7');
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut0');
			if ($status == -1) return img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 2) return img_picto($langs->trans('Approved'),'statut7').' '.$langs->trans('Approved');
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
			if ($status == -1) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 2) return $langs->trans('Approved').' '.img_picto($langs->trans('Approved'),'statut7');
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut0');
			if ($status == -1) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == 2) return $langs->trans('Approved').' '.img_picto($langs->trans('Approved'),'statut7');
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut5');
			if ($status == -1) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
	}

}

class ItemsgroupLineext extends CommonObjectLine
{

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
	}
}
