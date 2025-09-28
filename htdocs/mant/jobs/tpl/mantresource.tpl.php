<?php


/*
 * View
 */

print_barre_liste($langs->trans("Resourcesforjobs"), $page, "", "", $sortfield, $sortorder,'',$num);


dol_htmloutput_mesg($mesg);

$aLineas = array();


$sql = " SELECT ";
$sql.= " jr.dater, jr.type_cost, jr.description, jr.quant ,jr.fk_unit, jr.price, jr.status, ";
$sql.= " e.ref,e.label,";
$sql.= " a.ref AS refasset,a.descrip AS descripasset,";
$sql.= " j.rowid,j.ref as refequi,j.date_ini,j.date_fin,j.detail_problem,j.description_job,j.status, j.rowid";

$sql.= " FROM ".MAIN_DB_PREFIX."m_equipment  as e ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."assets as a ON a.rowid = e.fk_asset";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."m_jobs as j ON j.fk_equipment = e.rowid ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."m_jobs_resource as jr ON jr.fk_jobs = j.rowid ";

$sql.= " WHERE e.entity = ".$conf->entity;
$sql .= " AND a.rowid = ".$id;
    //
$sql.= " ORDER BY e.ref ASC";
	//echo $sql;
	//exit;

$resql=$db->query($sql);
if (! $res)
{
	dol_print_error($db);
	exit;
}

$num = $db->num_rows($resql);

print '<table class="noborder" width="100%">';

print "<tr class=\"liste_titre\">";
		//print_liste_field_titre($langs->trans("Equipment"),"", "","","","");
		//print_liste_field_titre($langs->trans("Assets"),"", "","","","");
print_liste_field_titre($langs->trans("Workorder"),"", "","","","");
print_liste_field_titre($langs->trans("Date"),"", "","","","");
print_liste_field_titre($langs->trans("Type"),"", "",'','','');
print_liste_field_titre($langs->trans("Description"),"", "",'','','');
print_liste_field_titre($langs->trans("Quantity"),"", "","","","");
print_liste_field_titre($langs->trans("Unit"),"", "","","","");
print_liste_field_titre($langs->trans("Price"),"", "","","",'align="center"');
print_liste_field_titre($langs->trans("Status"),"", "",'','','align="center"');
print "</tr>\n";

if ($num)
{
		//Para los Tabs del sistema
		//dol_fiche_head($head, 'card', $langs->trans("mants"), 0, 'Maintenance');


	$i=0;
	$var=true;
	$totalarray=array();
	while ($i < $num)
	{
		$obj = $db->fetch_object($resql);
		if ($obj)
		{
			$var = !$var;
			print '<tr '.$bc[$var].'>';

			$objectDos->id = $obj->rowid;
			$objectDos->ref = $obj->refequi;
			$objectDos->detail_problem = $obj->detail_problem;
			print '<td>'.$objectDos->getNomUrl(1).'</td>';
				//print '<td>'.$obj->refequi.'</td>';
			$aLineas[$i]['refequi'] = $obj->refequi;

			print '<td>'.dol_print_date($obj->dater,'day').'</td>';
			$aLineas[$i]['date_ini'] = $obj->dater;

			print '<td>'.$obj->type_cost.'</td>';
			$aLineas[$i]['tipo'] = $obj->type_cost;

			print '<td>'.$obj->description.'</td>';
			$aLineas[$i]['descripcion'] = $obj->description;

			print '<td align = "center">'.$obj->quant.'</td>';
			$aLineas[$i]['cantidad'] = $obj->quant;


			$MjobsresourceLine->fk_unit = $obj->fk_unit;
			if($MjobsresourceLine-> getLabelOfUnit('short') != -1){
				print '<td align = "center">'.$MjobsresourceLine-> getLabelOfUnit('short').'</td>';
					//print '<td>'.$obj->fk_unit.'</td>';
				$aLineas[$i]['unidad'] = $MjobsresourceLine-> getLabelOfUnit('short');
			}else{
				print '<td align = "center">'.'</td>';
				$aLineas[$i]['unidad'] = "";
			}



			print '<td align = "center">'.price2num($obj->price).'</td>';
			$aLineas[$i]['precio'] = $obj->price;

			$objectDos->status = $obj->status;
			print '<td align = "center">'.$objectDos->getLibStatut(0).'</td>';
			$aLineas[$i]['estado'] = $objectDos->getLibStatut(0);
			$var = true;
		}
		$i++;
	}
		//$db->free($result);


	$aReporte = array(1=>$aLineas,2=>$date_ini,3=>$aEstados[$fk_estado],4=>$aTipos[$fk_tipo]);
	$_SESSION['aReporte'] = serialize($aReporte);

}
else
{
	setEventMessages($langs->trans('No existe registros de mantenimiento'),null,'warnings');
}

print "</table>";


?>