<?php

/**
 *    Return country label, code or id from an id, code or label
 *
 *    @param      int		$searchkey      Id or code of country to search
 *    @param      int		$withcode   	'0'=Return label,
 *    										'1'=Return code + label,
 *    										'2'=Return code from id,
 *    										'3'=Return id from code,
 * 	   										'all'=Return array('id'=>,'code'=>,'label'=>)
 *    @param      DoliDB	$dbtouse       	Database handler (using in global way may fail because of conflicts with some autoload features)
 *    @param      Translate	$outputlangs	Langs object for output translation
 *    @param      int		$entconv       	0=Return value without entities and not converted to output charset, 1=Ready for html output
 *    @param      int		$searchlabel    Label of country to search (warning: searching on label is not reliable)
 *    @return     mixed       				String with country code or translated country name or Array('id','code','label')
 */
function getCountry_($searchkey,$withcode='',$dbtouse=0,$outputlangs='',$entconv=1,$searchlabel='')
{
    global $db,$langs;

    // Check parameters
    if (empty($searchkey) && empty($searchlabel))
    {
    	if ($withcode === 'all') return array('id'=>'','code'=>'','label'=>'');
    	else return '';
    }
    if (! is_object($dbtouse)) $dbtouse=$db;
    if (! is_object($outputlangs)) $outputlangs=$langs;

    $sql = "SELECT rowid, code, code_iso, label FROM ".MAIN_DB_PREFIX."c_country";
    if (is_numeric($searchkey)) $sql.= " WHERE rowid=".$searchkey;
    elseif (! empty($searchkey)) $sql.= " WHERE code='".$db->escape($searchkey)."'";
    else $sql.= " WHERE label='".$db->escape($searchlabel)."'";

    dol_syslog("Company.lib::getCountry_", LOG_DEBUG);
    $resql=$dbtouse->query($sql);
    if ($resql)
    {
        $obj = $dbtouse->fetch_object($resql);
        if ($obj)
        {
            $label=((! empty($obj->label) && $obj->label!='-')?$obj->label:'');
            $code_iso = $obj->code_iso;
            if (is_object($outputlangs))
            {
                $outputlangs->load("dict");
                if ($entconv) $label=($obj->code && ($outputlangs->trans("Country".$obj->code)!="Country".$obj->code))?$outputlangs->trans("Country".$obj->code):$label;
                else $label=($obj->code && ($outputlangs->transnoentitiesnoconv("Country".$obj->code)!="Country".$obj->code))?$outputlangs->transnoentitiesnoconv("Country".$obj->code):$label;
            }
            if ($withcode == 1) return $label?"$obj->code - $label":"$obj->code";
            else if ($withcode == 2) return $obj->code;
            else if ($withcode == 3) return $obj->rowid;
            else if ($withcode == 4) return $obj->code_iso;
            else if ($withcode === 'all') return array('id'=>$obj->rowid,'code'=>$obj->code,'label'=>$label,'code_iso'=>$code_iso);
            else return $label;
        }
        else
        {
            return 'NotDefined';
        }
        $dbtouse->free($resql);
    }
    else dol_print_error($dbtouse,'');
    return 'Error';
}
?>