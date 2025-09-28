<?php
require ("../../main.inc.php");

dol_include_once('/budget/class/budgettaskduration.class.php');
dol_include_once('/budget/class/budgettask.class.php');
dol_include_once('/budget/class/budget.class.php');
dol_include_once('/core/lib/date.lib.php');

$objdur = new Budgettaskduration($db);
$objdet = new Budgettask($db);
$budget = new Budget($db);
$budget->fetch($id);
$dateini = $budget->dateo;

$action = GETPOST('action');
$id 	= GETPOST('id');
$fk_budget_task = GETPOST('fk_budget_task');
$duration = GETPOST('duration');
$successor = GETPOST('successor');
$predecessor = GETPOST('predecessor');
$aArray = $_SESSION['taskduration'][$id];
$aArrayt = $_SESSION['taskdurationt'][$id];
if ($successor)
{
	$aSuccesarr = array();
	//; es separador
	//+ tiempo adicional
	//FS FF SF SS son tipos de dependencia por defechto FS
	$aSucess = explode(';',$successor);
	foreach ((array) $aSuccess AS $j => $data)
	{
		$numero = limpiatexto($data);
		//$aDatamas = explode('+',$data);
		//$adatamenos = explode('-',$data);
		
		//recupero el id de la tarea a que se referencia
		$aTask = $aArray[$id][$numero];
		$fktaskreemplaza = $aTask['rowid'];
		$campo = 'predecessor';
	}
}

//buscamos
$budget->fetch($id);
$res = $objdur->fetch(0, $fk_budget_task);
if ($res == 0)
{
	//creamos
	$objdur->fk_budget_task = $fk_budget_task;
	$objdur->duration = $duration+0;
	$newsuccessor = $objdur->successor;
	if ($sucessor)
	{
		$newsuccessor.= ($newsuccessor?';':'').$aArray[$successor]['rowid'];
	}
	$objdur->successor = $newsuccessor;
	$newpredecessor = $objdur->predecessor;
	if ($predecessor)
	{
		$newpredecessor.= ($newpredecessor?';':'').$aArray[$predecessor]['rowid'];
	}
	$objdur->predecessor = $newpredecessor;
	
	$objdur->fk_user_create = $user->id;
	$objdur->fk_user_mod = $user->id;
	$objdur->datec = dol_now();
	$objdur->datem = dol_now();
	$objdur->tms = dol_now();
	$objdur->status = 1;
	$res = $objdur->create($user);
	if ($res <=0)
	{
		$result = -1;
	}
	else
		$result = 1;
}
elseif($res == 1)
{
	//actualziamos
	$objdur->fk_budget_task = $fk_budget_task;
	$objdur->duration = $duration+0;
	$newsuccessor = $objdur->successor;
	$aSuccess = explode(';',$newsuccessor);
	//vamos a buscar si esta ya definido
	$lAdd = true;
	foreach ((array) $aSuccess AS $j => $value)
	{
		if ($value = $aArray[$successor]['rowid'])
			$lAdd = false;
	}
	if ($sucessor)
	{
		$newsuccessor.= ($newsuccessor?';':'').$aArray[$successor]['rowid'];
	}
	if ($lAdd)
		$objdur->successor = $newsuccessor;

	$newpredecessor = $objdur->predecessor;
	$aSuccess = explode(';',$newpredecessor);
	//vamos a buscar si esta ya definido
	$lAdd = true;
	
	foreach ((array) $aSuccess AS $j => $value)
	{
		if ($value>0 && $value == $aArray[$predecessor]['rowid'])
			$lAdd = false;
	}
	if ($predecessor)
	{
		$newpredecessor.= ($newpredecessor?';':'').$aArray[$predecessor]['rowid'];
	}
	if ($lAdd)
		$objdur->predecessor = $newpredecessor;
	$objdur->fk_user_mod = $user->id;
	$objdur->datem = dol_now();
	$objdur->tms = dol_now();
	$objdur->status = 1;
	$res = $objdur->update($user);
	if ($res <=0)
	{
		$result = -1;
	}
	else
		$result = 1;
	
}
$dateini = $budget->dateo;
$objdet->fetch($fk_budget_task);
//reemplazamos la fecha en la tarea principal
$newdate = dol_time_plus_duree($dateini, $duration, 'd');
$objdet->dateo = $dateini;
$objdet->datee = $newdate;
$res = $objdet->update($user);
if ($res <=0)
	$result = -1;
//para reemplazar los sucessores
if ($newpredecessor)
{
	$aSuccesarr = array();
	//; es separador
	//+ tiempo adicional
	//FS FF SF SS son tipos de dependencia por defechto FS
	$aSucess = explode(';',$newpredecessor);

	foreach ((array) $aSucess AS $j => $data)
	{
		//$numero = limpiatexto($data);
		$numero = $data;
		//$aDatamas = explode('+',$data);
		//$adatamenos = explode('-',$data);
		
		//recupero el id de la tarea a que se referencia
		$campo = 'successor';
		if ($numero>0)
		{
			//echo ' numero '.$numero;
			$objdur->fetch(0,$numero);

			$newsuccessor = $objdur->successor;
			$aSuccess = explode(';',$newsuccessor);
			//vamos a buscar si esta ya definido
			$lAdd = true;
			foreach ((array) $aSuccess AS $i => $value)
			{
				if ($value>0 && $value == $fk_budget_task)
					$lAdd = false;
			}
			if ($lAdd)
			{
				$newsuccessor.= ($newsuccessor?';':'').$fk_budget_task;
				$objdur->successor = $newsuccessor;
				$res = $objdur->update($user);
			}
			if ($res <=0)
				$result = -1;
		}
	}
}
print '<script type="text/javascript">';
print ' window.parent.document.getElementById('."'di_".$fk_budget_task."'".').value = "'. dol_print_date($dateini,'day').'"';
print '</script>';

print '<script type="text/javascript">';
print ' window.parent.document.getElementById('."'df_".$fk_budget_task."'".').value = "'. dol_print_date($newdate,'day').'"';
print '</script>';

if ($result <=0)
	echo $langs->trans('Error de registro');
else
	echo $langs->trans('Successfull');

?>