<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsregion.class.php';

class Itemsregionext extends Itemsregion

{

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
			if ($status == 1) return $langs->trans('Validated');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Validated');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Validated'),'statut4').' '.$langs->trans('Validated');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Validated'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Validated'),'statut4').' '.$langs->trans('Validated');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Validated').' '.img_picto($langs->trans('Validated'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == 1) return $langs->trans('Validated').' '.img_picto($langs->trans('Validated'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut5');
		}
	}
}


?>