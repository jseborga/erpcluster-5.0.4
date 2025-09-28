<?php

print '<table class="border" style="min-width=1000px" width="100%">';

// ref

$linkback = '<a href="'.DOL_URL_ROOT.'/assets/assets/liste.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';


print '<tr><td width="15%">'.$langs->trans('Code').'</td>';
print '<td colspan="2">';
print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref','');
print '</td>';
print '</tr>';
//ref_ext
print '<tr><td width="15%">'.$langs->trans('Refext').'</td>';
print '<td colspan="2">';
print $object->ref_ext;
print '</td>';
print '</tr>';

//group type
print '<tr><td width="15%">'.$langs->trans('Group').'</td><td colspan="2">';
print select_type_group($object->type_group,'type_group','',1,1,'code');
print '</td></tr>';

//ref item
print '<tr><td width="15%">'.$langs->trans('Item').'</td><td colspan="2">';
print $object->item_asset;
print '</td></tr>';

//patrim type
print '<tr><td width="15%">'.$langs->trans('Clasification').'</td><td colspan="2">';
if (!empty($object->type_patrim))
	print select_type_patrim($object->type_patrim,'type_patrim','',0,1,'code');
else
	print '&nbsp;';
print '</td></tr>';

//detail
print '<tr><td width="15%">'.$langs->trans('Detail').'</td><td colspan="2">';
print $object->descrip;
print '</td></tr>';

//quant
print '<tr><td width="15%">'.$langs->trans('Quantity').'</td><td colspan="2">';
print $object->quant;
print '</td></tr>';

//coste

if ($user->rights->assets->fin->leer)
{
	$code_iso = $conf->global->ASSETS_CURRENCY_DEFAULT;
	$code_iso = $conf->currency;
	print '<tr><td width="15%">'.$langs->trans('Coste').'</td><td colspan="2">';
	print price($object->coste);
	print ' - '.currency_name($code_iso,1).' '.$langs->getCurrencySymbol($code_iso);
	print '</td></tr>';
	print '<tr><td width="15%">'.$langs->trans('Costeresidual').'</td><td colspan="2">';
	print price($object->coste_residual);
	print ' - '.currency_name($code_iso,1).' '.$langs->getCurrencySymbol($code_iso);
	print '</td></tr>';
}
//date adq
print '<tr><td width="15%">'.$langs->trans('Dateacquisition').'</td><td colspan="2">';
print dol_print_date($object->date_adq,'day');
print '</td></tr>';

//number plaque
print '<tr><td width="15%">'.$langs->trans('Numberplaque').'</td><td colspan="2">';
print $object->number_plaque;
print '</td></tr>';

//useful_life
print '<tr><td width="15%">'.$langs->trans('Usefullife').'</td><td colspan="2">';
print $object->useful_life;
print '</td></tr>';

//fk_unit
print '<tr><td width="15%">'.$langs->trans('Unitusefullife').'</td><td colspan="2">';
print $object->getLabelOfUnit();
print '</td></tr>';

//fk_unit_use
print '<tr><td width="15%">'.$langs->trans('Unitofuse').'</td><td colspan="2">';
$object->fk_unit = $object->fk_unit_use;
print $object->getLabelOfUnit();
print '</td></tr>';
//coste_unit_use
print '<tr><td width="15%">'.$langs->trans('Costperusage').'</td><td colspan="2">';
print price($object->coste_unit_use);
print '</td></tr>';

//been
print '<tr><td width="15%">'.$langs->trans('Been').'</td><td colspan="2">';
//primera forma
require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsbeen.class.php';
$objBeen = new Cassetsbeen($db);
$res = $objBeen->fetch(0,$object->been);
if ($res > 0)
{
	print $objBeen->label;
}
else
{
	print 'no definido';
}
//segunda forma
//$objbeen = fetch_been('',$object->been);
//print $langs->trans($objbeen->libelle);
print '</td></tr>';


//ultima asignacion
print '<tr><td width="15%">'.$langs->trans('Ultimo').'</td><td colspan="2">';

require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdetext.class.php';
$objAss = new Assetsassignmentext($db);
$objAssdet = new Assetsassignmentdetext($db);
$filter = " AND t.fk_asset = ".$id;
$res = $objAssdet->fetchAll('DESC','t.date_assignment',0,0,array(1=>1),'AND',$filter,true);
if ($res == 1)
{
	$objAss->fetch($objAssdet->fk_asset_assignment);
	print $objAss->ref;
}
elseif ($res > 1)
{
	$fk_asset_assignment = 0;
	foreach($objAssdet->lines AS $j => $line)
	{
		if (empty($fk_asset_assignment))
			$fk_asset_assignment = $line->fk_asset_assignment;
		else
			continue;
	}
	$objAss->fetch($fk_asset_assignment);
	print $objAss->ref;
}

print '</td></tr>';

//para el usuario
print '<tr><td width="15%">'.$langs->trans('Usuario').'</td><td colspan="2">';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
$objUser = new User($db);
$res = $objUser->fetch($objAss->fk_user);
if ($res > 0)
{
	print $objUser->getNomUrl(1);
	print $objUser->lastname.' '.$objUser->firstname;
}

print '</td></tr>';


//Status
print '<tr><td width="15%">'.$langs->trans('Statut').'</td><td colspan="2">';
print $object->getLibStatut(4);
print '</td></tr>';

print '</table>';

?>