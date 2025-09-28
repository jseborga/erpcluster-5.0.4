<?php

require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementadd.class.php';

class Stockmouvementadde extends Stockmouvementadd
{

	/**
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into ALMACEN_ADDON
	 *
	 *  @param	Societe		$soc  	Object thirdparty
	 *  @return string      		Order free reference
	 */
	function getNextNumRef($soc)
	{
		global $db, $langs, $conf;
		$langs->load("almacen@almacen");

		$dir = DOL_DOCUMENT_ROOT . "/almacen/core/modules";

		if (! empty($conf->global->ALMACEN_ADDON))
		{
			$file = $conf->global->ALMACEN_ADDON.".php";
			// Chargement de la classe de numerotation
			$classname = $conf->global->ALMACEN_ADDON;
			//cambiamos a uno fijo
			$file = 'mod_almacen_ubuntubo_sol.php';
			$classname = 'mod_almacen_ubuntubo_sol';
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
					dol_print_error($db,"Stockmouvementadde::getNextNumRef ".$obj->error);
					return "";
				}
			}
			else
			{
				print $langs->trans("Error")." ".$langs->trans("Error_ALMACEN_ADDON_SOLALMACEN_NotDefined");
				return "";
			}
		}
		else
		{
			print $langs->trans("Error")." ".$langs->trans("Error_ALMACEN_ADDON_SOLALMACEN_NotDefined");
			return "";
		}
	}

}
?>