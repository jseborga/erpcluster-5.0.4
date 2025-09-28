<?php
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpayment.class.php';

class Projetpaymentext extends Projetpayment
{

	/**
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into ALMACEN_ADDON
	 *
	 *  @param  Societe     $soc    Object thirdparty
	 *  @return string              Order free reference
	 */
	function getNextNumRef($soc)
	{
		global $db, $langs, $conf;
		$langs->load("monprojet");

		$dir = DOL_DOCUMENT_ROOT . "/monprojet/core/modules";

		if (! empty($conf->global->MONPROJET_ADDON))
		{
			$file = $conf->global->MONPROJET_ADDON.".php";
			// Chargement de la classe de numerotation
			$classname = $conf->global->MONPROJET_ADDON;
			//cambiamos a uno fijo
			$file = 'mod_monprojet_fractal_pay.php';
			$classname = 'mod_monprojet_fractal_pay';
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
					dol_print_error($db,"Monprojet::getNextNumRef ".$obj->error);
					return "";
				}
			}
			else
			{
				print $langs->trans("Error")." ".$langs->trans("Error_MONPROJET_ADDON_NotDefinedx");
				return "";
			}
		}
		else
		{
			print $langs->trans("Error")." ".$langs->trans("Error_MONPROJET_ADDON_NotDefined");
			return "";
		}
	}
}
?>