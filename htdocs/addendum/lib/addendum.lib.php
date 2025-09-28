<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016 Ramiro Queso        <ramiroques@gmail.com>
 *
 */

/**
 *      \file       htdocs/contratadd/lib/contratadd.lib.php
 *      \ingroup    
 *      \brief      Librerias Adendas al contrato
 */

function select_type_limit($selected='',$htmlname='type_time_limit',$htmloption='',$showempty=0,$showlabel=0)
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code AS code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_type_time_limit AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';
	if ($selected == 0 && $showlabel)
		return '';
	if ($resql)
	{
		$html.= '<select class="flat" name="'.$htmlname.'" id="select'.$htmlname.'">';
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
				if (!empty($selected) && $selected == $obj->rowid)
				{
					$html.= '<option value="'.$obj->rowid.'" selected="selected">'.$obj->libelle.'</option>';
					if ($showlabel)
					{
						return $obj->libelle;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->rowid.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		$html.= '</select>';
		return $html;
	}
}

?>
