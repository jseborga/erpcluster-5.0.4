<?php 
require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetconcept.class.php';
$concept = new Budgetconcept($db);
$now = dol_now();
$aData = array(
	'BENESOC'=>array('ref'=>'BENESOC','label'=>$langs->trans('BUDGET_BENESOC'),'amount'=> 71.18),
	'UTILITY'=>array('ref'=>'UTILITY','label'=>$langs->trans('BUDGET_UTILITY'),'amount'=> 7),
	'TAXES_IVA'=>array('ref'=>'TAXES_IVA','label'=>$langs->trans('BUDGET_TAXES_IVA'),'amount'=> 14.94),
	'TAXES'=>array('ref'=>'TAXES','label'=>$langs->trans('BUDGET_TAXES'),'amount'=> 3.09),
	'TOOLS'=>array('ref'=>'TOOLS','label'=>$langs->trans('BUDGET_TOOLS'),'amount'=> 5),
	'EXPENSES'=>array('ref'=>'EXPENSES','label'=>$langs->trans('BUDGET_GENERALEXPENSES'),'amount'=> 11),
	);


if ($action == 'confirm_clon')
{
	$filtercon = " AND t.fk_budget = ".$id;
	$nb = $concept->fetchAll('', '',0,0,array(1=>1),'AND',$filterstatic,false);
	if ($nb >0)
	{
		$lines = $concept->lines;
		//recorremos y guardamos
		foreach ($lines AS $j => $line)
		{
			$concept->fetch($line->id);
			$concept->id = 0;
			$concept->fk_budget = $idBudget;
			$concept->fk_user_create = $user->id;
			$concept->fk_user_mod = $user->id;
			$concept->date_create = $now;
			$concept->date_mod = $now;
			$concept->tms = $now;
			$res = $concept->create($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($concept->error,$concept->errors,'errors');
			}
		}
	}
}
else
{
	require_once DOL_DOCUMENT_ROOT.'/budget/class/puformulasext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/puformulasdetext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/parametercalculation.class.php';
	$formula = new Puformulasext($db);
	$formuladet = new Puformulasdetext($db);
	$objParametercalculation = new Parametercalculation($db);
	//verificamos la estructura
	$typestr->fetch(0,$object->type_structure);
	$filter = " AND t.type_structure = '".$object->type_structure."'";
	$filter.= " AND t.entity = ".$conf->entity;
	//$objstr->fetchAll();
	$rest = $objstrdet->fetchAll('','',0,0,array(),'AND', $filter);
	if ($rest > 0)
	{
		foreach ($objstrdet->lines AS $j => $line)
		{
			$cformula = $line->formula;
			$filterf = " AND t.ref_formula = '".$cformula."'";
			$filterf.= " AND t.entity = ".$conf->entity;
			$resd = $formuladet->fetchAll('','',0,0,array(1=>1),'AND',$filterf);
			if ($resd>0)
			{
				foreach ($formuladet->lines AS $k => $linek)
				{
					if ($linek->type == 'parameter_calculation')
					{
						$aText = explode('|',$linek->changefull);
						//vamos a buscar dentro de parameter calculation
						$restmp = $objParametercalculation->fetch(0,$aText[1]);
						if ($restmp==1)
						{
							$concept->amount_def = $objParametercalculation->amount;
							$concept->amount = $objParametercalculation->amount;
						}
						else
							$concept->amount = 0;	
						$concept->fk_budget = $idBudget;
						$concept->ref = $aText[1];
						$concept->label = $line->detail;
						$concept->fk_user_create = $user->id;
						$concept->fk_user_mod = $user->id;
						$concept->date_create = $now;
						$concept->date_mod = $now;
						$concept->tms = $now;
						$concept->status = 1;
						$resc = $concept->create($user);
						if ($resc<=0)
						{
							$error++;
							setEventMessages($concept->error,$concept->errors,'errors');
						}						
					}
				}
			}
		}
	}
	
	if ($abc)
	{
		foreach ($aData AS $j => $data)
		{
			$concept->fk_budget = $idBudget;
			$concept->ref = $data['ref'];
			$concept->label = $data['label'];
			$concept->amount = $data['amount'];
			$concept->fk_user_create = $user->id;
			$concept->fk_user_mod = $user->id;
			$concept->date_create = dol_now();
			$concept->date_mod = dol_now();
			$concept->tms = dol_now();
			$concept->status = 1;
			$res = $concept->create($user);
			if ($res<=0)
			{
				$error++;
				setEventMessages($concept->error,$concept->errors,'errors');
			}
		}
	}
}

?>