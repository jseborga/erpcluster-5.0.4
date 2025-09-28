<?php


dol_include_once('/productext/class/bonus.class.php');


class Bonusext extends Bonus
{


	function getNomUrltpl($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $db, $conf, $langs;
		global $dolibarr_main_authentication, $dolibarr_main_demo;
		global $menumanager;

		if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

		$result = '';
		$companylink = '';

		$label = '<u>' . $langs->trans("MyModule") . '</u>';
		$label.= '<br>';
		$label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

		$url = DOL_URL_ROOT.'/productext/bonus/'.'carddet.php?id='.$this->fk_product.'&idr='.$this->idr.'&idrd='.$this->id;

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





	}





	?>