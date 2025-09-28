<?php

$aDays = array("domingo","lunes","martes","mi&eacute;rcoles","jueves","viernes","s&aacute;bado");
$aHour = array();
$aMin = array();
$a = 0;
for ($a =0; $a <=23; $a++) $aHour[$a]=(strlen($a)==1?'0'.$a:$a);
	$a = 0;
for ($a =0; $a <=59; $a++) $aMin[$a]=(strlen($a)==1?'0'.$a:$a);

	//$html.=load_fiche_titre($langs->trans("NewMyModule"));

if (! empty($conf->use_javascript_ajax))
{
	$html.= "\n".'<script type="text/javascript">';
	$html.= '$(document).ready(function () {
		id_te_private=8;
		id_ef15=1;

		$("#type_date2").click(function() {
			$(".individualline").hide();
			processcalendar(this);
		});
		$("#type_date1").click(function() {
			$(".individualline").show();
			processcalendar(this);
		});
		$("#type_date3").click(function() {
			$(".individualline").show();
			processcalendar(this);
		});
		$("#fk_calendar").change(function() {
			document.formcal.action.value="viewcalendar";
			document.formcal.submit();
			document.formcal.type_date1.value=1;
		});
	});';
	$html.= '</script>'."\n";

}

$html.='<form name="formcal" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
$html.='<input type="hidden" name="action" value="addconf">';
$html.='<input type="hidden" name="backtopage" value="'.$backtopage.'">';
//$html.='<input type="hidden" name="fk_calendar" value="'.$id.'">';
$html.='<input type="hidden" id="id" name="id" value="'.$id.'">';
$html.='<input type="hidden" name="dayselect" value="'.$dayselect.'">';
$html.='<input type="hidden" id="mes" name="mes" value="'.$mes.'">';
$html.='<input type="hidden" id="dia" name="dia" value="'.$dia.'">';
$html.='<input type="hidden" id="ano" name="ano" value="'.$year.'">';

$html.='<table class="border centpercent">'."\n";
	// $html.='<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 

	//mostramos el calendario base
$html.='<tr><td class="fieldrequired">'.$langs->trans("For").'</td><td>';	
$filtercalendar = " AND t.entity = ".$conf->entity;
$objcalendar = new Calendar($db);
$res = $objcalendar->fetchAll('ASC', 'label', 0, 0, array(1=>1), 'AND',$filtercalendar);
$options = '<option value="0">'.$langs->trans('Select').'</option>';
foreach ($objcalendar->lines AS $j => $line)
{
	$selected = '';
	$fk_calendar = (GETPOST('fk_calendar')?GETPOST('fk_calendar'):($fk_calendar?$fk_calendar:$object->fk_calendar));
	if ($fk_calendar == $line->id) $selected = ' selected';
	$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
}
$html.='<select id="fk_calendar" name="fk_calendar">'.$options.'</select>';
$html.='</td></tr>';	
//type_date
$html.='<tr><td class="fieldrequired">'.$langs->trans("Use").'</td><td>';
//$type_date = (GETPOST('type_date')?GETPOST('type_date'):$object->type_date);
if (!isset($type_date))
	$type_date = GETPOST('type_date');
if ($type_date == 2)
{
if (! empty($conf->use_javascript_ajax))
{
	$html.= "\n".'<script type="text/javascript">';
	$html.= '$(document).ready(function () {
		$(".individualline").hide();
	});';
	$html.= '</script>'."\n";
}

}
$html.= '<input id="type_date1" type="radio" name="type_date" value="1" '.($type_date==1?'checked':'').'>'.$langs->trans('Use valor predeterminado');
$html.= '<br><input id="type_date2" type="radio" name="type_date" value="2" '.($type_date==2?'checked':'').'>'.$langs->trans('No laborable');
$html.= '<br><input id="type_date3" type="radio" name="type_date" value="3" '.($type_date==3?'checked':'').'>'.$langs->trans('Laborable no predeterminado');




$htmltitle = '<tr>';
$htmlworkingday = '<tr>';
$htmlnoworkingday = '<tr>';
foreach ($aDays AS $codeday => $value)
{
	$htmltitle.='<td align="center" width="25px">';
	$htmltitle.=$langs->trans($value);
	$htmltitle.='</td>';
	$htmlworkingday.='<td align="center">';
	$htmlworkingday.='<input type="radio" name="working_day['.$codeday.']" value="1">';
	$htmlworkingday.='</td>';
	$htmlnoworkingday.='<td align="center">';
	$htmlnoworkingday.='<input type="radio" name="working_day['.$codeday.']" value="2">';
	$htmlnoworkingday.='</td>';
}
$htmltitle.='</tr>';
$htmlworkingday.='</tr>';
$htmlnoworkingday.='</tr>';
$html.='<tr class="individualline"><td class="fieldrequired">'.$langs->trans("Fieldworking_day_hours").'</td><td>';
$html.='<div class="input-group">';
for ($j=1; $j <=8; $j++)
{
	if ($j == 1)
	{
		$defaulthour = 8;
		$defaultmin = 0;
	}
	if ($j == 2)
	{
		$defaulthour = 12;
		$defaultmin = 0;
	}
	if ($j == 3)
	{
		$defaulthour = 14;
		$defaultmin = 0;
	}
	if ($j == 4)
	{
		$defaulthour = 18;
		$defaultmin = 0;
	}
	if ($j == 5)
	{
		$defaulthour = 0;
		$defaultmin = 0;
	}
	if ($j == 6)
	{
		$defaulthour = 0;
		$defaultmin = 0;
	}
	if ($j == 7)
	{
		$defaulthour = 0;
		$defaultmin = 0;
	}
	if ($j == 8)
	{
		$defaulthour = 0;
		$defaultmin = 0;
	}
	$html.='<div class="col-md-4">';
	$html.=$form->selectarray('hour_'.$j,$aHour,(GETPOST('hour_'.$j)?GETPOST('hour_'.$j):$defaulthour));
	$html.=':';
	$html.=$form->selectarray('min_'.$j,$aMin,(GETPOST('min_'.$j)?GETPOST('min_'.$j):$defaultmin));
	$html.='</div>';
	if ($j == 1 ||$j == 3 ||$j == 5 ||$j == 7)
		$html.='<div class="col-md-1">'.$langs->trans('To').'</div>';
	if ($j == 2 ||$j == 4 ||$j == 6 ||$j == 8)
		$html.='</div>';
}
$html.='</div>';
$html.='</td></tr>';

$html.='</table>'."\n";

$html.='</form>';
$html.='<div id="listtask"></div>';
?>