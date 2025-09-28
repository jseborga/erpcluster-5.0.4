<?php
require ("../../main.inc.php");

dol_include_once('/budget/class/calendarspecial.class.php');
dol_include_once('/budget/class/budget.class.php');
dol_include_once('/core/lib/date.lib.php');

$objcal = new Calendarspecial($db);
$budget = new Budget($db);

$action = GETPOST('action');
$id 	= GETPOST('id');
$fk_calendar = GETPOST('fk_calendar');
$mes = GETPOST('mes');
$dia = GETPOST('dia');
$ano = GETPOST('ano');
$type_date = GETPOST('type_date');
$working_day_hour = GETPOST('working_day_hour');
$url    = $_SESSION['url'];

//buscamos
$budget->fetch($id);
$filtercal = " AND t.object = '".$budget->element."'";
$filtercal.= " AND t.fk_object = ".$budget->id;
$dayselect = dol_mktime(0,0,0,$mes,$dia,$ano);

$string = $ano.'-'.(strlen($mes)==1?'0'.$mes:$mes).'-'.(strlen($dia)==1?'0'.$dia:$dia);
$date = dol_stringtotime($string);
$filtercal.= " AND month(t.dateo) =".$mes;
$filtercal.= " AND year(t.dateo) =".$ano;
$filtercal.= " AND day(t.dateo) =".$dia;
$res = $objcal->fetchAll('','',0,0,array(1=>1),'AND',$filtercal,true);
if ($res == 0)
{
	$objcal->fk_object = $id;
	$objcal->object = 'budget';
	$objcal->dateo = $dayselect;
	$objcal->fk_calendar = $fk_calendar;
	$objcal->type_date = $type_date;
	$objcal->fk_user_create = $user->id;
	$objcal->fk_user_mod = $user->id;
	$objcal->datec = dol_now();
	$objcal->datem = dol_now();
	$objcal->tms = dol_now();
	$objcal->status = 1;
	$res = $objcal->create($user);
	if ($res <=0)
	{
		print $langs->trans('Error de registro');
	}
	else
		print $langs->trans('Proceso satisfactorio');
}
elseif($res == 1)
{
	//actualziamos
	$objcal->object = $budget->element;
	$objcal->dateo = $dayselect;
	$objcal->working_day_hour = $working_day_hour;
	$objcal->fk_calendar = $fk_calendar;

	$objcal->type_date = $type_date;
	$objcal->fk_user_mod = $user->id;
	$objcal->datem = dol_now();
	$objcal->tms = dol_now();
	$objcal->status = 1;
	$res = $objcal->update($user);
	if ($res <=0)
	{
		print $langs->trans('Error de registro');
	}
	else
		print $langs->trans('actualizado');
	
}
?>