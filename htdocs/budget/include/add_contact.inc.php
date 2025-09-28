<?php

foreach ((array) $aContact AS $source => $aData)
{
	foreach ($aData AS $j => $row)
	{
		$resc = $objtmp->add_contact($row['id'], $row['fk_c_type_contact'], $source,1);
		if ($resc<=0)
		{
			$error++;
			setEventMessages($langs->trans('Error en registro contactos'),null,'errors');
		}
	}
}
?>