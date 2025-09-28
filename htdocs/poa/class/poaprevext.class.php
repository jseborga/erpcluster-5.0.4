<?php
require_once DOL_DOCUMENT_ROOT.'/poa/class/poaprev.class.php';

class Poaprevext extends Poaprev
{
	var $aSum;
	function get_sum_catprog_partida($period_year, $fk_structure,$partida,$status =1)
	{
		global $conf;
		$sql = "SELECT";
		$sql .= " pp.fk_structure, ";
		$sql.= " pp.partida, ";
		$sql.= " SUM(pp.amount) AS total";
		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_partida_pre AS pp ON pp.fk_poa_prev = t.rowid";
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND t.entity IN (" . getEntity("poaprev", 1) . ")";
		}
		$sql.= " AND t.gestion = ".$period_year;
		if ($fk_structure) $sql.= " AND pp.fk_structure = ".$fk_structure;
		if ($partida) $sql.= " AND pp.partida = '".$partida."'";
		if ($status>0) $sql.= " AND t.statut = ".$status;
		$sql.= " GROUP BY pp.fk_structure, pp.partida";
		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			$i = 0;
			if ($numrows) {
				while ($i < $numrows)
				{
					$obj = $this->db->fetch_object($resql);

					$this->aSum[$obj->fk_structure][$obj->partida]+= $obj->total;
					$i++;
				}
				
			}
			$this->db->free($resql);

			if ($numrows) {
				return $numrows;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	    /**
     *  Load object in memory from the database
     *
     *  @param	int		$gestion    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function getlistfather($fk_father)
    {
      global $langs;
        $sql = "SELECT";
	$sql.= " t.rowid,";

	$sql.= " t.entity,";
	$sql.= " t.gestion,";
	$sql.= " t.fk_pac,";
	$sql.= " t.fk_area,";
	$sql.= " t.code_requirement,";
	$sql.= " t.label,";
	$sql.= " t.pseudonym,";
	$sql.= " t.nro_preventive,";
	$sql.= " t.date_preventive,";
	$sql.= " t.amount,";
	$sql.= " t.priority,";
	$sql.= " t.date_create,";
	$sql.= " t.fk_user_create,";
	$sql.= " t.tms,";
	$sql.= " t.statut,";
	$sql.= " t.active";

	$sql.= " FROM ".MAIN_DB_PREFIX."poa_prev as t";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev_seg AS s ON t.rowid = s.fk_prev";
	$sql.= " WHERE s.fk_father = ".$fk_father;
	$sql.= " AND t.statut > 0";
	dol_syslog(get_class($this)."::getlistfather sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
	$this->arrayf = array();
        if ($resql)
	  {
	    $num = $this->db->num_rows($resql);
            if ($this->db->num_rows($resql))
	      {
		$i = 0;
		while ($i < $num)
		  {
		    $obj = $this->db->fetch_object($resql);
		    $objnew = new Poaprev($this->db);
		    $objnew->id    = $obj->rowid;
		    $objnew->ref   = $obj->rowid;
		    $objnew->entity = $obj->entity;
		    $objnew->gestion = $obj->gestion;
		    $objnew->fk_pac = $obj->fk_pac;
		    $objnew->fk_area = $obj->fk_area;
		    $objnew->code_requirement = $obj->code_requirement;
		    $objnew->label = $obj->label;
		    $objnew->pseudonym = $obj->pseudonym;
		    $objnew->nro_preventive = $obj->nro_preventive;
		    $objnew->date_preventive = $this->db->jdate($obj->date_preventive);
		    $objnew->amount = $obj->amount;
		    $objnew->priority = $obj->priority;
		    $objnew->date_create = $this->db->jdate($obj->date_create);
		    $objnew->fk_user_create = $obj->fk_user_create;
		    $objnew->tms = $this->db->jdate($obj->tms);
		    $objnew->statut = $obj->statut;
		    $objnew->active = $obj->active;

		    $this->arrayf[$obj->rowid] = $objnew;
		    $i++;
		  }
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::getlistfather ".$this->error, LOG_ERR);
            return -1;
        }
    }
}
?>