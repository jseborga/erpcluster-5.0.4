<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskresource.class.php';

class Budgettaskresourceext extends Budgettaskresource
{

	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *	@param	int		$withpicto			Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option				On what the link point to
     *  @param	int  	$notooltip			1=Disable tooltip
     *  @param	int		$maxlen				Max length of visible user name
     *  @param  string  $morecss            Add more css on link
	 *	@return	string						String with URL
	 */
	function getNomUrladd($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $db, $conf, $langs;
        global $dolibarr_main_authentication, $dolibarr_main_demo;
        global $menumanager;

        if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

        $result = '';
        $companylink = '';

        $label = '<u>' . $langs->trans("Budgettaskresource") . '</u>';
        $label.= '<br>';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $url = DOL_URL_ROOT.'/budget/budget/'.'supplies.php?id='.$this->fk_budget_task.'&idr='.$this->id;

        $linkclose='';
        if (empty($notooltip))
        {
            if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
            {
                $label=$langs->trans("ShowProject");
                $linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
            }
            $linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
        }
        else $linkclose = ($morecss?' class="'.$morecss.'"':'');

		$linkstart = '<a href="'.$url.'"';
		$linkstart.=$linkclose.'>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($linkstart.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
		}
		$result.= $linkstart . $this->ref . $linkend;
		return $result;
	}
	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function update_formula(User $user, $notrigger = false)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters


		if (isset($this->formula)) {
			 $this->formula = trim($this->formula);
		}
		if (isset($this->formula_res)) {
			 $this->formula_res = trim($this->formula_res);
		}
		if (isset($this->formula_quant)) {
			 $this->formula_quant = trim($this->formula_quant);
		}
		if (isset($this->formula_factor)) {
			 $this->formula_factor = trim($this->formula_factor);
		}
		if (isset($this->formula_prod)) {
			 $this->formula_prod = trim($this->formula_prod);
		}

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' formula = '.(isset($this->formula)?"'".$this->db->escape($this->formula)."'":"null").',';
		$sql .= ' formula_res = '.(isset($this->formula_res)?$this->formula_res:"null").',';
		$sql .= ' formula_quant = '.(isset($this->formula_quant)?$this->formula_quant:"null").',';
		$sql .= ' formula_factor = '.(isset($this->formula_factor)?$this->formula_factor:"null").',';
		$sql .= ' formula_prod = '.(isset($this->formula_prod)?$this->formula_prod:"null");

		$sql .= ' WHERE rowid=' . $this->id;

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

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function update_priority(User $user, $fk_budget_task, $value=0,$id='')
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' priority = '.$value;
		$sql .= ' WHERE fk_budget_task=' . $fk_budget_task;
		if ($id) $sql.= " AND rowid = ".$id;
		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
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

	function calculate_performance(User $user, $fk_budget_task,$code)
	{
		global $langs,$conf,$general;
		//determinamos el prioritario
		$filterstatic = " AND t.fk_budget_task=".$fk_budget_task;
		$filterstatic.= " AND t.priority = 1";
		$res = $this->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);
		$budgettask = new Budgettaskresourceext($this->db);
		$formula_res = 0;
		if ($res == 1)
		{
			$rowid = $this->id;
			$formula_res =$this->formula_res;
		}
		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_budget_task,";
		$sql .= " t.ref,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.code_structure,";
		$sql .= " t.fk_product,";
		$sql .= " t.fk_product_budget,";
		$sql .= " t.fk_budget_task_comple,";
		$sql .= " t.detail,";
		$sql .= " t.fk_unit,";
		$sql .= " t.quant,";
		$sql .= " t.percent_prod,";
		$sql .= " t.amount_noprod,";
		$sql .= " t.amount,";
		$sql .= " t.rang,";
		$sql .= " t.priority,";
		$sql .= " t.formula,";
		$sql .= " t.formula_res,";
		$sql .= " t.formula_quant,";
		$sql .= " t.formula_factor,";
		$sql .= " t.formula_prod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.status";
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql .= ' WHERE t.fk_budget_task = ' . $fk_budget_task;
		$sql .= ' AND t.code_structure = ' . $code;

		$resql = $this->db->query($sql);
		$aPriority = array();
		$this->db->begin();
		if ($resql)
		{
			$numrows = $this->db->num_rows($resql);
			if ($numrows)
			{
				while ($i < $numrows)
				{
					$obj = $this->db->fetch_object($resql);
					$budgettask->fetch($obj->rowid);
					//numero de equipos
					$q = 0;
					if ($obj->formula_res > 0) $q = ceil($formula_res / $obj->formula_res);
					//rendimiento
					$rend = 0;
					if ($obj->formula_res > 0) $rend = 1 / $formula_res * $q;
					//productividad percent
					$prod = 0;
					if ($rend > 0 && $obj->formula_res) $prod = 1 / $rend / $obj->formula_res;
					//actualizamos
					$budgettask->formula_quant = price2num($q,$general->decimal_quant);
					$budgettask->formula_factor = $prod;
					$budgettask->formula_prod = price2num($rend,$general->decimal_quant);
					$resup = $budgettask->update_formula($user);
					if ($resup <=0)
						$error++;
						setEventMessages($budgettask->error,$budgettask->errors,'errors');
					$i++;
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
	/**
	 *	Returns the text label from units dictionary
	 *
	 * 	@param	string $type Label type (long or short)
	 *	@return	string|int <0 if ko, label if ok
	*/
	function getLabelOfUnit($type='long')
	{
		global $langs;

		if (!$this->fk_unit) {
			return '';
		}

		$langs->load('products');

		$this->db->begin();

		$label_type = 'label';

		if ($type == 'short')
		{
			$label_type = 'short_label';
		}

		$sql = 'select '.$label_type.' from '.MAIN_DB_PREFIX.'c_units where rowid='.$this->fk_unit;
		$resql = $this->db->query($sql);
		if($resql && $this->db->num_rows($resql) > 0)
		{
			$res = $this->db->fetch_array($resql);
			$label = $langs->trans($res[$label_type]);
			$this->db->free($resql);
			return $label;
		}
		else
		{
			$this->error=$this->db->error().' sql='.$sql;
			dol_syslog(get_class($this)."::getLabelOfUnit Error ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function update_unit_price(User $user, $fk_product_budget, $value,$value_noprod=0)
	{
		$error = 0;
		if ($fk_product_budget <=0)
			return -1;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		$sql .= ' amount = '.$value;
		$sql .= ' , amount_noprod = '.$value_noprod;
		$sql .= ' WHERE fk_product_budget=' . $fk_product_budget;
		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
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

}

/**
 * Class BudgettaskresourceLine
 */
class BudgettaskresourceLineext extends CommonObjectLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $fk_budget_task;
	public $ref;
	public $fk_user_create;
	public $fk_user_mod;
	public $code_structure;
	public $fk_product;
	public $fk_product_budget;
	public $fk_budget_task_comple;
	public $detail;
	public $fk_unit;
	public $quant;
	public $percent_prod;
	public $amount_noprod;
	public $amount;
	public $rang;
	public $priority;
	public $commander;
	public $performance;
	public $price_productive;
	public $price_improductive;
	public $formula;
	public $formula_res;
	public $formula_quant;
	public $formula_factor;
	public $formula_prod;
	public $date_create = '';
	public $date_mod = '';
	public $tms = '';
	public $status;

	/**
	 * @var mixed Sample line property 2
	 */

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
	}

}

?>