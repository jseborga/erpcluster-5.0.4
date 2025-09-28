<?php

function departament_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('orgman@orgman');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/orgman/departament/card.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath("/orgman/departament/listuser.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Member");
	$head[$h][2] = 'user';
	$h++;
	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'orgman');

	return $head;
}


function verif_departament($id=0)
{
	global $db,$langs,$user;

	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentuserext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';

	$objDepartamentuser = new Pdepartamentuserext($db);
	$objDepartament = new Pdepartamentext($db);

	$aAreadirect = array();
	//verificamos que departamentos puede ver
	$res = $objDepartamentuser->getuserarea(($id?$id:$user->id),true);
	$res = $objDepartament->fetch($objDepartamentuser->fk_areaasign);
	//vemos el responsable directo del area
	$fk_user_resp = '';
	$fk_departament_sup='';
	if ($res > 0)
	{
		$fk_user_resp = $objDepartament->fk_user_resp;
		$fk_departament_sup = $objDepartament->fk_father;
	}
	$filterarea = '';
	$aFilterarea = array();
	$fk_areaasign = 0;
	if ($res > 0)
	{
		$aAreadirect = $objDepartamentuser->aAreadirect;
		$fk_areaasign = $objDepartamentuser->fk_areaasign;
		foreach ($objDepartamentuser->aArea AS $j => $data)
		{
			if ($filterarea) $filterarea.= ',';
			$filterarea.= $j;
			$aFilterarea[$j]=$j;
		}
		//solo utilizamos a las areas directas asignadas
		$filterarea = '';
		foreach ((array) $aAreadirect AS $j)
		{
			if ($filterarea) $filterarea.= ',';
			$filterarea.= $j;
			$aFilterarea[$j] = $j;
		}
	}
	//verificamos que departamentos es responsable
	$filter = " AND t.fk_user_resp = ".$user->fk_member." AND t.active = 1 AND t.status = 1";
	$res = $objDepartament->fetchAll('','',0,0,array(1=>1),'AND',$filter);
	if ($res > 0)
	{
		foreach ($objDepartament->lines AS $j => $line)
		$aAreadirect[$line->id] = $line->id;
	}
	return array($aAreadirect,$fk_areaasign,$filterarea,$aFilterarea, $fk_user_resp, $fk_departament_sup);
}
?>