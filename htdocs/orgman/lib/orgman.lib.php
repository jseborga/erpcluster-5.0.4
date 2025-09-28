<?php

/**
 * Prepare array with list of tabs
 *
 * @param   Object  $object   Object related to tabs
 * @return  array       Array of tabs to shoc
 */
function property_prepare_head($object)
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();
	$head[$h][0] = DOL_URL_ROOT.'/orgman/property/fiche.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Property");
	$head[$h][2] = 'card';
	$h++;
	$head[$h][0] = DOL_URL_ROOT.'/orgman/property/permission.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Permissions");
	$head[$h][2] = 'permission';
	$h++;

	//assetspropcomplete_head_from_modules($conf,$langs,$object,$head,$h,'assets');


	//assetspropcomplete_head_from_modules($conf,$langs,$object,$head,$h,'assets','remove');

	return $head;
}
function getRealIP() {
	if (!empty($_SERVER['HTTP_CLIENT_IP']))
		return $_SERVER['HTTP_CLIENT_IP'];

	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		return $_SERVER['HTTP_X_FORWARDED_FOR'];

	return $_SERVER['REMOTE_ADDR'];
}

function filter_departament_user($id,$lview=false)
{
	global $db,$user;

	require_once(DOL_DOCUMENT_ROOT."/orgman/class/pdepartamentuserext.class.php");

	$aArea = array();
	$idsArea = '';
	if (!$user->admin || $lview)
	{
		$objareauser = new Pdepartamentuserext($db);
		$res = $objareauser->getuserarea($id);
		foreach((array) $objareauser->aArea AS $j => $objarea)
		{

			if (!empty($idsArea)) $idsArea.=',';
			$idsArea.= $objarea->id;
		}
	}
	return $idsArea;
}

function tables_prepare_head($object)
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();
	$head[$h][0] = DOL_URL_ROOT.'/orgman/tables/card.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';

	$h++;
	$head[$h][0] = DOL_URL_ROOT.'/orgman/tables/carddet.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Carddet");
	$head[$h][2] = 'carddet';
	$h++;

	//assetspropcomplete_head_from_modules($conf,$langs,$object,$head,$h,'assets');


	//assetspropcomplete_head_from_modules($conf,$langs,$object,$head,$h,'assets','remove');

	return $head;
}
?>