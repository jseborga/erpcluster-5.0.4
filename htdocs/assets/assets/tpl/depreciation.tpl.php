<?php

$day = GETPOST('dc_day');
$month = GETPOST('dc_month');
$year = GETPOST('dc_year');
if (empty($day))
{
	$aDate = dol_getdate(dol_now());
	$day = $aDate['mday'];
	$month = $aDate['mon'];
	$year = $aDate['year'];
}
$date_cal = dol_mktime(0,0,0,$month,$day,$year);

print load_fiche_titre($langs->trans("DepreciationCalculator"));

if (! empty($conf->use_javascript_ajax))
{
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () {
		$("#dc_").change(function() {
			document.formdep.action.value="process";
			document.formdep.submit();
		});
	});';
	print '</script>'."\n";

}
print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'" name="formdep">';
print '<input type="hidden" name="action" value="process">';
print '<input type="hidden" name="id" value="'.$id.'">';
print '<input type="hidden" name="tab" value="'.$tab.'">';
print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

dol_fiche_head();

print '<table class="border" width="100%">';


print '<tr '.$bc[$var].'><td >'.$langs->trans('Date').'</td>';
print '<td >';
if (!empty($object->date_baja) && $object->date_baja < $date_cal)
{
	$date_cal = $object->date_baja;
	$aDatecalc = dol_getdate($date_cal);
	$month = $aDatecalc['mon'];
	$year = $aDatecalc['year'];
	$day = $aDatecalc['mday'];
}
print $form->select_date($date_cal,'dc_',0,0,1);
print '</td>';
print '<td >';
print '<input type="submit" value="'.$langs->trans('Process').'">';
print '</td>';

print '</tr>';
print '</table>';
dol_fiche_end();
print '</form>';

//resultado
//vamos a calcular
$country = $conf->global->ASSETS_CURRENCY_DEFAULT;
$type_group = $object->type_group;
$objmov     = new Assetsmovext($db);
//echo '<hr>'.$month.' '.$year.' '.$country.' '.$type_group.' '.$day.' '.$object->id;
$res = $objmov->process_depr($month,$year,$country,$type_group,$day,$object->id);
if ($res > 0)
{
	$aArray = $objmov->array[$object->id];

	dol_fiche_head();
	print '<table class="border" width="100%">';
	$var = !$var;
	print '<tr '.$bc[$var].'><td>'.$langs->trans('Dateadq').'</td>';
	print '<td>'.dol_print_date($aArray['date_adq'],'day').'</td></tr>';
	$var = !$var;
	print '<tr '.$bc[$var].'><td>'.$langs->trans('Dateactivation').'</td>';
	print '<td>'.dol_print_date($aArray['date_active'],'day').'</td></tr>';
	if ($object->date_baja)
	{
		$var = !$var;
		print '<tr '.$bc[$var].'><td>'.$langs->trans('Datebaja').'</td>';
		print '<td>'.dol_print_date($object->date_baja,'day').'</td></tr>';
	}
	$var = !$var;
	print '<tr '.$bc[$var].'><td>'.$langs->trans('Fieldtimeconsumed').'</td>';
	print '<td align="right">'.(empty($aArray['time_consumed'])?'0':$aArray['time_consumed']).' '.$langs->trans('Days').'</td></tr>';
	$var = !$var;
	print '<tr '.$bc[$var].'><td>'.$langs->trans('Costehistoric').'</td>';
	print '<td align="right">'.price(price2num($aArray['coste'],'MT')).'</td></tr>';
	$var = !$var;
	print '<tr '.$bc[$var].'><td>'.$langs->trans('Amountupdate').'</td>';
	print '<td align="right">'.price(price2num($aArray['amount_update'],'MT')).'</td></tr>';
	//$var = !$var;
	//print '<tr '.$bc[$var].'><td>'.$langs->trans('Depreciation').'</td>';
	//print '<td align="right">'.price(price2num($aArray['amount_depr'],'MT')).'</td></tr>';
	//$var = !$var;
	//print '<tr '.$bc[$var].'><td>'.$langs->trans('Depreciationacumulated').'</td>';
	//print '<td align="right">'.price(price2num($aArray['amount_depr_acum'],'MT')).'</td></tr>';
	//$var = !$var;
	//print '<tr '.$bc[$var].'><td>'.$langs->trans('Depreciationacumulatedupdate').'</td>';
	//print '<td align="right">'.price(price2num($aArray['amount_depr_acum_update'],'MT')).'</td></tr>';
	$var = !$var;
	print '<tr '.$bc[$var].'><td>'.$langs->trans('Amountbalance').'</td>';
	print '<td align="right">'.price(price2num($aArray['amount_balance'],'MT')).'</td></tr>';
	$var = !$var;
	print '<tr '.$bc[$var].'><td>'.$langs->trans('Amountbalancedepr').'</td>';
	print '<td align="right">'.price(price2num($aArray['amount_balance_depr'],'MT')).'</td></tr>';
	$var = !$var;
	print '<tr '.$bc[$var].'><td>'.$langs->trans('Balance').'</td>';
	print '<td align="right">'.price(price2num($aArray['amount_balance']-$aArray['amount_balance_depr'],'MT')).'</td></tr>';

	print '</td>';
	print '</table>';
	dol_fiche_end();
}
?>