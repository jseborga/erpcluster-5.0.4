<?php
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalarypresent.class.php';

class Psalarypresentext extends Psalarypresent
{
		//MODIFICACIONES

	/**
   *  Create a document onto disk according to template module.
   *
   *  @param      string    $modele     Force model to use ('' to not force)
   *  @param    Translate $outputlangs  Object langs to use for output
   *  @param      int     $hidedetails    Hide details of lines
   *  @param      int     $hidedesc       Hide description
   *  @param      int     $hideref        Hide ref
   *  @return     int                 0 if KO, 1 if OK
   */
	public function generateDocument($modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
	{
		global $conf,$user,$langs;

		$langs->load("salary");

		// Positionne le modele sur le nom du modele a utiliser
		if (! dol_strlen($modele))
		{
				$modele = 'boleta';
		}

		$modelpath = "salary/core/modules/doc/";

		return $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref);
	}
	/**
	*  Load object in memory from the database
	*
	*  @param	int 		$user    Id user
	*  @param  int 		$fk_period    Id period
	*  @param  int 		$fk_proces    Id proces
	*  @param  int 		$fk_type_fol  Id type fol
	*  @param  int 		$fk_concept   Id concept
	*  @return int          	<0 if KO, >0 if OK
	*/
	function fetch_salary_concept($fk_user,$fk_period,$fk_proces,$fk_type_fol,$code_concept,$state=0)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.fk_proces,";
		$sql.= " t.fk_type_fol,";
		$sql.= " t.fk_concept,";
		$sql.= " t.fk_period,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_cc,";
		$sql.= " t.fk_account,";
		$sql.= " t.sequen,";
		$sql.= " t.type,";
		$sql.= " t.cuota,";
		$sql.= " t.semana,";
		$sql.= " t.amount_inf,";
		$sql.= " t.amount,";
		$sql.= " t.hours_info,";
		$sql.= " t.hours,";
		$sql.= " t.date_reg,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.date_mod,";
		$sql.= " t.fk_user_mod,";
		$sql.= " t.state,";
		$sql.= " c.ref AS ref_concept,";
		$sql.= " c.detail AS detail_concept ";

		$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_present as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_concept AS c ON t.fk_concept = c.rowid";
		$sql.= " WHERE t.fk_user = ".$fk_user;
		$sql.= " AND t.fk_period = ".$fk_period;
		$sql.= " AND t.fk_proces = ".$fk_proces;
		$sql.= " AND t.fk_type_fol = ".$fk_type_fol;
		$sql.= " AND c.ref = ".$code_concept;
		//$sql.= " AND t.state <> ".$state; //revisar el state

		dol_syslog(get_class($this)."::fetch_salary_concept sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->entity = $obj->entity;
				$this->fk_proces = $obj->fk_proces;
				$this->fk_type_fol = $obj->fk_type_fol;
				$this->fk_concept = $obj->fk_concept;
				$this->fk_period = $obj->fk_period;
				$this->fk_user = $obj->fk_user;
				$this->fk_cc = $obj->fk_cc;
				$this->fk_account = $obj->fk_account;
				$this->sequen = $obj->sequen;
				$this->type = $obj->type;
				$this->cuota = $obj->cuota;
				$this->semana = $obj->semana;
				$this->amount_inf = $obj->amount_inf;
				$this->amount = $obj->amount;
				$this->hours_info = $obj->hours_info;
				$this->hours = $obj->hours;
				$this->date_reg = $this->db->jdate($obj->date_reg);
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_user_create = $obj->fk_user_create;
				$this->date_mod = $this->db->jdate($obj->date_mod);
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->state = $obj->state;
			}
			else
			{
				$this->db->free($resql);
				return 0;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_salary_concept ".$this->error, LOG_ERR);
			return -1;
		}
	}
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int 		$user    Id user
	 *  @param  int 		$fk_period    Id period
	 *  @param  int 		$fk_proces    Id proces
	 *  @param  int 		$fk_type_fol  Id type fol
	 *  @param  int 		$fk_concept   Id concept

	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_salary_p($fk_user,$fk_period,$fk_proces,$fk_type_fol,$fk_concept,$state)
	{
		global $langs;

		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.fk_proces,";
		$sql.= " t.fk_type_fol,";
		$sql.= " t.fk_concept,";
		$sql.= " t.fk_period,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_cc,";
		$sql.= " t.fk_account,";
		$sql.= " t.sequen,";
		$sql.= " t.type,";
		$sql.= " t.cuota,";
		$sql.= " t.semana,";
		$sql.= " t.amount_inf,";
		$sql.= " t.amount,";
		$sql.= " t.hours_info,";
		$sql.= " t.hours,";
		$sql.= " t.date_reg,";
		$sql.= " t.date_create,";
		$sql.= " t.date_mod,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.fk_user_mod,";
		$sql.= " t.state";


		$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_present as t";
		$sql.= " WHERE t.fk_user = ".$fk_user;
		$sql.= " AND t.fk_period = ".$fk_period;
		$sql.= " AND t.fk_proces = ".$fk_proces;
		$sql.= " AND t.fk_type_fol = ".$fk_type_fol;
		$sql.= " AND t.fk_concept = ".$fk_concept;
	  //$sql.= " AND t.state <> ".$state; //revisar el state

		dol_syslog(get_class($this)."::fetch_salary_p sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->entity = $obj->entity;
				$this->fk_proces = $obj->fk_proces;
				$this->fk_type_fol = $obj->fk_type_fol;
				$this->fk_concept = $obj->fk_concept;
				$this->fk_period = $obj->fk_period;
				$this->fk_user = $obj->fk_user;
				$this->fk_cc = $obj->fk_cc;
				$this->fk_account = $obj->fk_account;
				$this->sequen = $obj->sequen;
				$this->type = $obj->type;
				$this->cuota = $obj->cuota;
				$this->semana = $obj->semana;
				$this->amount_inf = $obj->amount_inf;
				$this->amount = $obj->amount;
				$this->hours_info = $obj->hours_info;
				$this->hours = $obj->hours;
				$this->date_reg = $this->db->jdate($obj->date_reg);
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->date_mod = $this->db->jdate($obj->date_mod);
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->state = $obj->state;
			}
			else
			{
				$this->db->free($resql);
				return 0;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_salary_p ".$this->error, LOG_ERR);
			return -1;
		}
	}

	//actualización del estado
	function update_state($fk_period,$fk_proces, $fk_type_fol, $state)
	{
		global $conf, $langs;
				//actualizamos el state
		$sql = " UPDATE ".MAIN_DB_PREFIX."p_salary_present ";
		$sql.= " SET state = ".$state;
		$sql.= " WHERE ";
		$sql.= " fk_period = ".$fk_period." AND ";
		$sql.= " fk_proces = ".$fk_proces." AND ";
		$sql.= " fk_type_fol = ".$fk_type_fol;

		$this->db->begin();
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		if (!$error && !$notrigger) {
			// Uncomment this and change MYOBJECT to your own tag if you
			// want this action calls a trigger.

			//// Call triggers
			//$result=$this->call_trigger('MYOBJECT_MODIFY',$user);
			//if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
			//// End call triggers
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();
			return - 1 * $error;
		} else {
			$this->db->commit();
			return 1;
		}
	}

	function delete_group($fk_period,$fk_proces, $fk_type_fol)
	{
		global $conf,$langs;
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."p_salary_present ";
		$sql.= " WHERE fk_period = ".$fk_period;
		$sql.= " AND fk_proces = ".$fk_proces;
		$sql.= " AND fk_type_fol = ".$fk_type_fol;
		$this->db->begin();
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		if (!$error && !$notrigger) {
			// Uncomment this and change MYOBJECT to your own tag if you
			// want this action calls a trigger.

			//// Call triggers
			//$result=$this->call_trigger('MYOBJECT_MODIFY',$user);
			//if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
			//// End call triggers
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();
			return - 1 * $error;
		} else {
			$this->db->commit();
			return 1;
		}
	}

	//registro final
	function registry_end($fk_period, $fk_proces,$fk_type_fol,$state)
	{
		global $conf,$user,$lang;
		//,$db,$objectsh,$objectsp;
		$lOk = false;
		//verificamos el estado
		$filter = " AND t.fk_period = ".$fk_period;
		$filter.= " AND t.fk_proces = ".$fk_proces;
		$filter.= " AND t.fk_type_fol = ".$fk_type_fol;
		$filter.= " AND t.state = ".$state;
		$num = $this->fetchAll('ASC','fk_user',0,0,array(1=>1),'AND',$filter);
		if ($num > 0)
		{
			$lines = $this->lines;
			require_once DOL_DOCUMENT_ROOT.'/salary/class/pperiodext.class.php';
			require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryhistoryext.class.php';
			$objectsh = new Psalaryhistoryext($this->db);
			$objTmp = new Psalaryhistoryext($this->db);
			$objPeriod = new Pperiodext($this->db);
			//obtenemos la gestión para la numeración sequencial
			$period_year = date('Y');
			$resp = $objPeriod->fetch($fk_period);
			if ($resp>0) $period_year = $objPeriod->anio;
			$now = dol_now();
			$this->db->begin();
			$i = 0;
			$fk_user = 0;
			$ref = 0;
			foreach ($lines AS $I => $obj)
			{
				//vamos a verificar el numero que corresponde segun el member y el period_year
				if ($fk_user != $obj->fk_user)
				{
					$restmp = $objTmp->get_salaryhistorynext($period_year);
					if ($restmp>0) $ref = $objTmp->max;
					$fk_user = $obj->fk_user;
				}
		  		//buscamos si existe el registro en history
				//$objectsh->fetch_salary_p($obj->fk_user,$fk_period,$fk_proces,$fk_type_fol,$obj->fk_concept,$state);
				$filter = " AND t.fk_user = ".$obj->fk_user;
				$filter.= " AND t.fk_period = ".$fk_period;
				$filter.= " AND t.fk_proces = ".$fk_proces;
				$filter.= " AND t.fk_type_fol = ".$fk_type_fol;
				$filter.= " AND t.fk_concept = ".$obj->fk_concept;
				$filter.= " AND t.state = ".$state;
				$ressh = $objectsh->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
				//if ($objectsh->fk_user == $obj->fk_user && $objectsh->fk_period == $obj->fk_period && $objectsh->fk_proces == $obj->fk_proces && $objectsh->fk_type_fol == $obj->fk_type_fol && $objectsh->fk_concept == $obj->fk_concept)
				if ($ressh==1)
				{
		  			//actualizacion
					$objectsh->entity = $conf->entity;
					$objectsh->fk_salary_present = $obj->id;
					$objectsh->fk_proces = $fk_proces;
					$objectsh->fk_type_fol = $fk_type_fol;
					$objectsh->fk_concept = $obj->fk_concept;
					$objectsh->fk_period = $fk_period;
					$objectsh->fk_user = $obj->fk_user;
					$objectsh->fk_cc = $obj->cc+0;
					$objectsh->sequen = $obj->sequen+0;
					$objectsh->type = $obj->type;
		  			//revisar
					$objectsh->cuota = $obj->cuota;
					$objectsh->semana = $obj->semana;
					$objectsh->amount_inf = $obj->amount_inf;
					$objectsh->amount = $obj->amount;
					$objectsh->hours_info = $obj->hours_info;
					$objectsh->fk_account = $obj->fk_account +0;
					$objectsh->payment_state = $obj->payment_state +0;
					$objectsh->hours = $obj->hours;
					$objectsh->date_reg = $obj->data_reg;
					//$objectsh->date_create = $now;
					$objectsh->date_mod = $now;
					//$objectsh->fk_user_create = $user->id;
					$objectsh->fk_user_mod = $user->id;
					$objectsh->state = $obj->state;
					$res = $objectsh->update($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($objectsh->error,$objectsh->errors,'errors');
					}
				}
				else
				{
		  			//registro nuevo
					$objectsh->initAsSpecimen();
					$objectsh->entity             = $conf->entity;
					$objectsh->period_year = $period_year;
					$objectsh->ref = $ref;
					$objectsh->fk_salary_present  = $obj->id;
					$objectsh->fk_proces   	= $fk_proces;
					$objectsh->fk_type_fol 	= $fk_type_fol;
					$objectsh->fk_concept  	= $obj->fk_concept;
					$objectsh->fk_period   	= $fk_period;
					$objectsh->fk_user     	= $obj->fk_user;
					$objectsh->fk_cc  		= $obj->cc+0;
					$objectsh->sequen  		= $obj->sequen+0;
					$objectsh->type   		= $obj->type;
					$objectsh->fk_account   = $obj->fk_account + 0;
					$objectsh->payment_state = $obj->payment_state +0;
					//revisar
					$objectsh->cuota  		= $obj->cuota+0;
					$objectsh->semana 		= $obj->semana+0;
					$objectsh->amount_inf 	= $obj->amount_inf+0;
					$objectsh->amount     	= $obj->amount+0;
					$objectsh->hours_info 	= $obj->hours_info+0;
					$objectsh->hours      	= $obj->hours+0;
					$objectsh->date_reg   	= $obj->date_reg;
					$objectsh->date_create 	= $now;
					$objectsh->date_mod    	= $now;
					$objectsh->fk_user_create 	= $user->id;
					$objectsh->fk_user_mod 	= $user->id;
					$objectsh->state       	= $obj->state;
					$res = $objectsh->create($user);
					if ($res <= 0)
					{
						$error++;
						setEventMessages($objectsh->error,$objectsh->errors,'errors');
					}
				}
			}

			if (!$error)
			{
				$this->db->commit();
				return true;
			}
			else
			{
				$this->db->rollback();
				return $error*-1;
			}
		}
		elseif($num <0)
			return -1;
	}
}
?>