<?php

require_once (DOL_DOCUMENT_ROOT .'/assistance/class/licences.class.php');

class Licencesext extends Licences
{

	function getNextNumRef($soc)
	{
		global $db, $langs, $conf;
		$langs->load("assistance@assistance");

		$dir = DOL_DOCUMENT_ROOT . "/assistance/core/modules";

		if (! empty($conf->global->ASSISTANCE_ADDON))
		{
			$file = $conf->global->ASSISTANCE_ADDON.".php";
			// Chargement de la classe de numerotation
			$classname = $conf->global->ASSISTANCE_ADDON;
			$result=include_once $dir.'/'.$file;
			if ($result)
			{
				$obj = new $classname();
				$numref = "";
				$numref = $obj->getNextValue($soc,$this);
				if ( $numref != "")
				{
					return $numref;
				}
				else
				{
					dol_print_error($db,"Assistance::getNextNumRef ".$obj->error);
					return "";
				}
			}
			else
			{
				print $langs->trans("Error")." ".$langs->trans("Error_ASSISTANCE_ADDON_NotDefined");
				return "";
			}
		}
		else
		{
			print $langs->trans("Error")." ".$langs->trans("Error_ASSISTANCE_ADDON_NotDefined");
			return "";
		}
	}

	public function generateDocument($modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
	{
		global $conf, $user, $langs;

		$langs->load("suppliers");

		// Sets the model on the model name to use
		if (! dol_strlen($modele))
		{
			if (! empty($conf->global->ASSISTANCE_ADDON_PDF))
			{
				$modele = $conf->global->ASSISTANCE_ADDON_PDF;
			}
			else
			{
				$modele = 'drrhh';
			}
		}

		$modelpath = "assistance/core/modules/doc/";
		return $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref);
	}

	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->statut,$mode);
	}

	/**
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	function LibStatut($status,$mode=0)
	{
		global $langs;
		//licencias
		if ($mode == 0)
		{
			$prefix='';
			if ($status == 5) return $langs->trans('Concluded');
			if ($status == 4) return $langs->trans('Inprogress');
			if ($status == 3) return $langs->trans('Approved');
			if ($status == 2) return $langs->trans('Reviewed');
			if ($status == 1) return $langs->trans('Validated');
			if ($status == 0) return $langs->trans('Draft');
			if ($status == -1) return $langs->trans('Annulled');
		}
		if ($mode == 1)
		{
			if ($status == 5) return $langs->trans('Concluded');
			if ($status == 4) return $langs->trans('Inprogress');
			if ($status == 3) return $langs->trans('Approved');
			if ($status == 2) return $langs->trans('Reviewed');
			if ($status == 1) return $langs->trans('Validated');
			if ($status == 0) return $langs->trans('Draft');
			if ($status == -1) return $langs->trans('Annulled');
		}
		if ($mode == 2)
		{
			if ($status == 5) return img_picto($langs->trans('Concluded'),'statut7').' '.$langs->trans('Concluded');
			if ($status == 4) return img_picto($langs->trans('Inprogress'),'statut6').' '.$langs->trans('Inprogress');
			if ($status == 3) return img_picto($langs->trans('Approved'),'statut4').' '.$langs->trans('Approved');
			if ($status == 2) return img_picto($langs->trans('Reviewed'),'statut3').' '.$langs->trans('Reviewed');
			if ($status == 1) return img_picto($langs->trans('Validated'),'statut1').' '.$langs->trans('Validated');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut0').' '.$langs->trans('Draft');
			if ($status == -1) return img_picto($langs->trans('Annulled'),'statut8').' '.$langs->trans('Annulled');
		}
		if ($mode == 3)
		{
			if ($status == 5) return img_picto($langs->trans('Concluded'),'statut7');
			if ($status == 4) return img_picto($langs->trans('Inprogress'),'statut6');
			if ($status == 3) return img_picto($langs->trans('Approved'),'statut4');
			if ($status == 2) return img_picto($langs->trans('Reviewed'),'statut3');
			if ($status == 1) return img_picto($langs->trans('Validated'),'statut1');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut0');
			if ($status == -1) return img_picto($langs->trans('Annulled'),'statut8');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == 5) return $langs->trans('Concluded').' '.img_picto($langs->trans('Concluded'),'statut7');
			if ($status == 4) return $langs->trans('Inprogress').' '.img_picto($langs->trans('Inprogress'),'statut6');
			if ($status == 3) return $langs->trans('Approved').' '.img_picto($langs->trans('Approved'),'statut4');
			if ($status == 2) return $langs->trans('Reviewed').' '.img_picto($langs->trans('Reviewed'),'statut3');
			if ($status == 1) return $langs->trans('Validated').' '.img_picto($langs->trans('Validated'),'statut1');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut0');
			if ($status == -1) return $langs->trans('Annulled').' '.img_picto($langs->trans('Annulled'),'statut8');
		}


	}
}
?>
