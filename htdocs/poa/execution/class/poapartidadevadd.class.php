<?php
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidadev.class.php';
class poapartidadevadd extends poapartidadev
{
	public $aCount;
	public $aSum;
	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
		return 1;
	}

	function actualizadev($gestion)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);
		global $conf,$objcon,$user; 

		$sql  = " SELECT ";
		$sql .= " t.rowid,";
		$sql .= " t.fk_poa_partida_com,";
		$sql .= " t.gestion,";
		$sql .= " t.fk_poa_prev,";
		$sql .= " t.fk_structure,";
		$sql .= " t.fk_poa,";
		$sql .= " t.fk_contrat,";
		$sql .= " t.fk_contrato,";
		$sql .= " t.type_pay,";
		$sql .= " t.nro_dev,";
		$sql .= " t.date_dev,";
		$sql .= " t.partida,";
		$sql .= " t.invoice,";
		$sql .= " t.amount,";
		$sql .= " t.date_create,";
		$sql .= " t.tms,";
		$sql .= " t.statut,";
		$sql .= " t.active";

		$sql .= " FROM " . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql .= " WHERE t.gestion = '".$gestion."'";
		dol_syslog(get_class($this)."::actualizadev sql=".$sql, LOG_DEBUG);
		$resql = $this->db->query( $sql );
		if ($resql) 
		{
			$i = 0;
			$numrows = $this->db->num_rows($resql);
			if ($numrows) 
			{
				while ($i < $numrows)
				{
					$obj = $this->db->fetch_object($resql);
					//buscamos el contrato
					$objcon->fetch($obj->fk_contrato);
					$objcon->fetch_optionals($obj->fk_contrato,$extralabels);
					$obj->rowid.' '.$objcon->array_options['options_advance'];
					if ($objcon->array_options['options_advance']==1)
					{
						if (empty($obj->type_pay) && (empty($obj->invoice) || is_null($obj->invoice)))
						{
							//buscamos
							$this->fetch($obj->rowid);
							$this->type_pay = 1;
							$res = $this->update($user);
						}
					}
					$i++;
				}
			}
		}
	}
	/*
	* sumar y contar las actividades por usuario
	*/
	function resume_deve_user($gestion,$fk_user=0,$fk_structure=0,$fk_poa=0,$partida='',$type=false)
	{
		global $langs,$conf,$user;
		//agregamos poapartidapre
		//require_once DOL_DOCUMENT_ROOT.'/poa/class/poaprevext.class.php';
		$res = $this->getsum_str_part($gestion,$fk_structure,$fk_poa,$partida,$fk_user,$type);
		$this->aCount= array();
		$this->aSum = array();
		if ($res>0)
		{
			if ($type == true)
			{
				foreach ((array) $this->aTotal AS $t => $value)
				{
					if ($t == 0)
						$this->aSum[$fk_user] += $value;
				}
			}
			else
				$this->aSum[$fk_user] = $this->total;
			return $res;
		}
		return $res;
	}
}
?>