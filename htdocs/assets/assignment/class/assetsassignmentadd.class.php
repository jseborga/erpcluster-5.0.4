<?php
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignment.class.php';

class Assetsassignmentadd extends Assetsassignment
{
		//MODIFICADO
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
        $langs->load("assets@assets");
	//modelo fijo de numeracion;
	$modelnum = 'mod_assets_ubuntubo_assign';
        $dir = DOL_DOCUMENT_ROOT . "/assets/core/modules";

        //if (! empty($conf->global->ASSETS_ADDON))
	if (! empty($modelnum))
	  {
            //$file = $conf->global->ASSETS_ADDON.".php";
	    $file = $modelnum.".php";
            // Chargement de la classe de numerotation
	    //$classname = $conf->global->ASSETS_ADDON;
	    $classname = $modelnum;
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
                    dol_print_error($db,"Assetsassignment::getNextNumRef ".$obj->error);
                    return "";
		  }
	      }
            else
	      {
                print $langs->trans("Error")." ".$langs->trans("Error_ASSETS_ADDON_NotDefined");
                return "";
	      }
	  }
        else
	  {
            print $langs->trans("Error")." ".$langs->trans("Error_ASSETS_ADDON_NotDefined");
            return "";
	  }
    }


}
?>