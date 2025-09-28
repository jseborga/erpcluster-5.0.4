<?php
require_once DOL_DOCUMENT_ROOT.'/mant/class/mequipment.class.php';

class Mequipmentext extends Mequipment
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
	 *  @param  int     $status         Id status
	 *  @param  int     $mode           0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	 *  @return string                  Label of status
	 */
	static function LibStatut($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 0) return $langs->trans('Pending');
			if ($status == 1) return $langs->trans('Tobeaccepted');
			if ($status == 2) return $langs->trans('Accepted');
			if ($status == 3) return $langs->trans('Accepted');
			if ($status == 9) return $langs->trans('Free');
			if ($status == -1) return $langs->trans('Disabled');
		}
		if ($mode == 1)
		{
			if ($status == 0) return $langs->trans('Pending');
			if ($status == 1) return $langs->trans('Tobeaccepted');
			if ($status == 2) return $langs->trans('Accepted');
			if ($status == 3) return $langs->trans('Accepted');
			if ($status == 9) return $langs->trans('Free');
			if ($status == -1) return $langs->trans('Disabled');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 4)
		{
			//estado del activo
			//0 = Pendiente de recepcion
			//1 = Assignado
			//2 = Aceptado en uso
			//3 = Aceptado en uso
			//9 = Libre
			if ($status == 0) return img_picto($langs->trans('Pending'),'statut7').' '.$langs->trans('Pending');
			if ($status == 1) return img_picto($langs->trans('Tobeaccepted'),'statut3').' '.$langs->trans('Tobeaccepted');
			if ($status == 2) return img_picto($langs->trans('Accepted'),'statut1').' '.$langs->trans('Accepted');
			if ($status == 3) return img_picto($langs->trans('Accepted'),'statut1').' '.$langs->trans('Accepted');
			if ($status == 9) return img_picto($langs->trans('Free'),'statut0').' '.$langs->trans('Free');
			if ($status == -1) return img_picto($langs->trans('Disabled'),'statut0').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == -1) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut0');
			if ($status == 0) return $langs->trans('Pending').' '.img_picto($langs->trans('pending'),'statut7');
			if ($status == 1) return $langs->trans('Tobeaccepted').' '.img_picto($langs->trans('Tobeaccepted'),'statut3');
			if ($status == 2) return $langs->trans('Accepted').' '.img_picto($langs->trans('Accepted'),'statut1');
			if ($status == 3) return $langs->trans('Accepted').' '.img_picto($langs->trans('Accepted'),'statut1');
			if ($status == 9) return $langs->trans('Free').' '.img_picto($langs->trans('Free'),'statut0');
		}
	}
}
?>