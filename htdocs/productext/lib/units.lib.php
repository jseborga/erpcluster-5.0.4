<?php

function fetch_unit($id,$short='label')
{
	global $db, $conf, $langs;
	$sql = "SELECT rowid, code,label,short_label,active";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_units AS t ";
	$sql.= " WHERE t.rowid = ".$id;
	$result=$db->query($sql);
	if ($result)
	{
		if ($db->num_rows($result) > 0)
		{
			$obj=$db->fetch_object($result);
			return $obj;
		}
		return 0;
		$db->free($result);
	}
	else
	{
        dol_print_error($db);
	}
	return -1;
}
?>