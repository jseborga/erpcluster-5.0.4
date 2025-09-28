<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskproductivity.class.php';

class Budgettaskproductivityext extends Budgettaskproductivity
{
	public function import_productivity(User $user,$lineid,$resbtr)
	{
		//si tiene en tabla productivity creamos 
		$filter = " AND t.fk_budget_task_resource = ".$lineid;
		$filter.= " AND t.status = 1";
		$resp = $this->fetchAll('','',0,0,array(1=>1),'AND',$filter);
		$this->db->begin();
		if ($resp>0)
		{
			$linesp = $this->lines;
			$objproductivity = new Budgettaskproductivity($this->db);
			foreach ($linesp AS $j => $linep)
			{
				$objproductivity->fetch($linep->id);
				$objproductivity->id = 0;
				$objproductivity->fk_budget_task_resource = $resbtr;
				$objproductivity->fk_user_create = $user->id;
				$objproductivity->fk_user_mod = $user->id;
				$objproductivity->date_create = dol_now();
				$objproductivity->date_mod = dol_now();
				$objproductivity->tms = dol_now();
				$respadd = $objproductivity->create($user);
				if ($respadd<=0)
				{
					$error++;
					setEventMessages($objproductivity->error,$objproductivity->errors,'errors');
				}
			}
		}
		if (!$error)
		{
			$this->db->commit();
			return 1;
		}
		else
		{
			$this->db->rollback();
			return $error*-1;
		}
	}
}
?>