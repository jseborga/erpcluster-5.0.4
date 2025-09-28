<?php
//modif_orderref.tpl.php

//variable fija para ordenacion
$aMultiple = 100000;
$aOrderref = array(1=>100000,2=>110000,3=>111000,4=>111100,5=>111110);
$aOrdernumref = array();
$lines = $tasksarray;
$numlines=count($lines);
$taskstat = new Task($db);
for ($i = 0 ; $i < $numlines ; $i++)
{
	$nLevel = 9;
	$line = $lines[$i];
	list($aOrdernumref,$aOrdernumparent,$aOrdertask) = get_orderref($line,$aOrdernumref,$aOrdernumparent,$aOrdertask);
}

//numeracion del order_ref
$aOrderref = set_order_ref($aOrdernumref,$aOrdernumparent,$aOrdertask);
/*
$aOrdernew = array();
$nDigit = 7;
$nOrder = 1000000;
$cDigit = '1000000';
if (count($aOrdernumref)>0)
{
	$nDigitc = $nDigit;
	foreach ($aOrdernumref AS $i => $aData) //$i == nivel
	{
		$nDigitc = $nDigit - $i - 1;
		foreach ($aData AS $j => $value)
		{
			if ($aOrdernumparent[$j])
			{
				$aOrdernew[$i][$j] = $aOrdernew[$i-1][$aOrdernumparent[$j]] + ($value * substr($cDigit,0,$nDigitc));
			}
			else
			{
				$aOrdernew[$i][$j] = $value * $nOrder;
			}
		}
	}
}
$aOrderref = array();
foreach ($aOrdernew AS $i => $aData)
{
	foreach($aData AS $j => $value)
		$aOrderref[$j] = $value;
}

//recorremos las tareas para numerar
$aOrdernumtask = array();
foreach ((array) $aOrdertask AS $j => $value)
{
	$nValueparent = $aOrderref[$value];
	$nLen = $aOrdernumtask[$value]+1;
	$nNew = $nValueparent + $nLen;
	$aOrdernumtask[$value] = $nLen;
	$aOrderref[$j] = $nNew;

}
*/
foreach ((array) $aOrderref AS $i => $value)
{
	//$res = $objecttaskadd->fetch(0,$i);
	//if ($res > 0 && $objecttaskadd->fk_task == $i)
	//{
	//verifico que tipo de tarea es
		$obj = new Projettaskadd($db);
		$obj->fetch(0,$i);
		if ($obj->fk_task == $i)
		{
			$obj->order_ref = price2num($value,'MT');
			$res = $obj->update_orderref();
			if ($res <=0) $error++;
		}
		else
		{
			echo '<hr>'.$obj->fk_task.' == '.$i;
			echo '<br>error, no idem '.$obj->error;
			exit;
		}
	//}
	//else
	//{
	//	$error++;
	//	exit;
	//}
}
?>