<?php
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryhistory.class.php';

class Psalaryhistoryext extends Psalaryhistory
{
	public $max;

	//ultimo registro ref
	public function get_salaryhistorynext($period_year)
	{
		global $conf;

		$sql = 'SELECT';
		$sql .= " MAX(CAST(t.ref AS UNSIGNED)) AS max";

		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE entity = '.$conf->entity;
		$sql.= " AND t.period_year = ".$period_year;
		$this->max = 0;
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num) {
				$obj = $this->db->fetch_object($resql);
				$this->max = $obj->max+1;
			}
			elseif(empty($num))
				$this->max = 1;

			$this->db->free($resql);
			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
			return - 1;
		}
	}


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

		//MODIFICACIONES
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
	function fetch_salary_p($fk_user,$fk_period,$fk_proces,$fk_type_fol,$fk_concept,$state=1)
	{
		global $langs;

		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.fk_salary_present,";
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
		$sql.= " t.state";


		$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_history as t";
		$sql.= " WHERE t.fk_user = ".$fk_user;
		$sql.= " AND t.fk_period = ".$fk_period;
		$sql.= " AND t.fk_proces = ".$fk_proces;
		$sql.= " AND t.fk_type_fol = ".$fk_type_fol;
		$sql.= " AND t.fk_concept = ".$fk_concept;
		//$sql.= " AND t.state = ".$state;

		dol_syslog(get_class($this)."::fetch_salary_p sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;
				$this->fk_salary_present = $obj->fk_salary_present;
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
				$this->db->free($resql);
				return 1;
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_salary_p ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *	Load all detailed lines into this->lines
	 *
	 *	@return     int         1 if OK, < 0 if KO
	 */
	function fetch_lines()
	{
		$this->lines=array();

		$sql = "SELECT p.fk_concept, p.amount, p.hours, p.fk_account, ";
		$sql.= " c.detail, c.print, c.type_cod ";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_history AS p ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_concept AS c ON p.fk_concept = c.rowid ";
		$sql.= " WHERE ";
		$sql.= " p.entity = ".$conf->entity ." AND ";
		$sql.= " p.fk_period = ".$fk_period ." AND ";
		$sql.= " p.fk_proces = ".$fk_proces ." AND ";
		$sql.= " p.fk_type_fol = ".$fk_type_fol ." AND ";
		$sql.= " p.fk_user = ".$idUser ;
		//$sql.= " p.state IN (4,5) ";
		$sql.= " ORDER BY c.type_cod,c.ref ";

		dol_syslog(get_class($this).'::fetch_lines sql='.$sql, LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;
			while ($i < $num)
			{
				$objp = $this->db->fetch_object($result);
				$line = new Solalmacendet($this->db);

				$line->rowid	        = $objp->rowid;
				$line->product_type     = $objp->product_type;
				// Type of line
				$line->product_ref      = $objp->product_ref;
				// Ref product
				$line->libelle          = $objp->product_label;
				// TODO deprecated
				$line->fk_account       = $objp->fk_account;
				$line->product_label	= $objp->product_label;
				// Label product
				$line->product_desc     = $objp->product_desc;
				// Description product
				$line->qty              = $objp->qty;
				$line->qty_livree       = $objp->qty_livree;
				$line->fk_product       = $objp->fk_product;
				$line->date_shipping    = $this->db->jdate($objp->date_shipping);

				// Ne plus utiliser
				//$line->price            = $objp->price;
				//$line->remise           = $objp->remise;

				$this->lines[$i] = $line;

				$i++;
			}
			$this->db->free($result);
			return 1;
		}
		else
		{
			$this->error=$this->db->error();
			dol_syslog(get_class($this).'::fetch_lines '.$this->error,LOG_ERR);
			return -3;
		}
	}

	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength		Max length for labels (0=no limit)
	 *  @return string           		HTML string with select
	 */
	function array_payment($fk_concept,$order='',$filter="")
	{
		global $conf,$langs;
		$langs->load("salary@salary");

		$aArray=array();

		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.fk_concept,";
		$sql.= " t.fk_period,";
		$sql.= " t.fk_user,";
		$sql.= " t.type,";
		$sql.= " t.cuota,";
		$sql.= " t.semana,";
		$sql.= " t.amount_inf,";
		$sql.= " t.amount,";
		$sql.= " t.hours_info,";
		$sql.= " p.anio, p.mes, ";
		$sql.= " a.firstname, a.lastname ";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_history as t ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_period AS p ON t.fk_period = p.rowid ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent AS a ON t.fk_user = a.rowid ";
		$sql.= " WHERE t.entity = ".$conf->entity;
		$sql.= " AND t.fk_concept IN (".$fk_concept.")";

		$sql.= " ORDER BY $order";


		dol_syslog(get_class($this)."::array_payment sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$label = $obj->lastname.' '.$obj->firstname.' '.$obj->anio.'-'.$obj->mes;
					$aArray[$obj->rowid] = $label;
					$i++;
				}
			}
		}
		else
		{
			dol_print_error($this->db);
		}

		return $aArray;
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
		global $langs;

		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.fk_salary_present,";
		$sql.= " t.fk_proces,";
		$sql.= " t.fk_type_fol,";
		$sql.= " t.fk_concept,";
		$sql.= " t.fk_period,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_cc,";
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
		$sql.= " t.fk_account,";
		$sql.= " t.payment_state,";
		$sql.= " t.state,";
		$sql.= " c.ref AS ref_concept,";
		$sql.= " c.detail AS detail_concept ";

		$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_history as t";
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

}
?>