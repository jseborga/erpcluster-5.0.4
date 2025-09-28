<?php
require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentuser.class.php';

class Pdepartamentuserext extends Pdepartamentuser
{

	public $aArea;
	public $fk_areaasign;
	public $idsArea;
	public $aAreadirect;
		//modificado
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist($fk_area,$fk_user=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_departament,";
		$sql.= " t.fk_user,";
		$sql.= " t.datec,";
		$sql.= " t.tms,";
		$sql.= " t.active,";
		$sql.= " t.privilege";

		$sql.= " FROM ".MAIN_DB_PREFIX."p_departament_user as t";
		$sql.= " WHERE t.fk_departament = ".$fk_area;
		if ($fk_user>0)
			$sql.= " AND t.fk_user = ".$fk_user;
		dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Pdepartamentuserext($this->db);
					$objnew->id      = $obj->rowid;
					$objnew->fk_area = $obj->fk_departament;
					$objnew->fk_departament = $obj->fk_departament;
					$objnew->fk_user = $obj->fk_user;
					$objnew->datec = $this->db->jdate($obj->datec);
					$objnew->tms         = $this->db->jdate($obj->tms);
					$objnew->active      = $obj->active;
					$objnew->privilege   = $obj->privilege;

					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
			}
			$this->db->free($resql);

			return $num;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
			return -1;
		}
	}


	/**
	 *  Load object in memory from the database
	 *  Areas a las que pertenece el usuario
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getuserarea($fk_user,$lSon=true)
	{
		global $langs,$user;
		$aArea = array();
		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.fk_departament,";
		$sql.= " t.fk_user,";
		$sql.= " t.datec,";
		$sql.= " t.tms,";
		$sql.= " t.active,";
		$sql.= " t.privilege,";
		$sql.= " u.rowid AS rowiduser ";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_departament_user as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent as a ON t.fk_user = a.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON u.fk_member = a.rowid";
		$sql.= " WHERE u.rowid = ".$fk_user;
		$sql.= " AND t.active = 1";

		dol_syslog(get_class($this)."::getuserarea sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->aArea = array();
		$this->aAreadirect = array();
		$this->idsArea = '';
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					include_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
					$obja = new Pdepartamentext($this->db);
					$objatmp = new Pdepartamentext($this->db);
					$this->fk_areaasign = $obj->fk_departament;
					$this->aAreadirect[$obj->fk_departament] = $obj->fk_departament;
					if ($obja->fetch($obj->fk_departament)>0)
					{
						if ($obja->id == $obj->fk_departament)
						{
							$obja->privilege = $obj->privilege;
							$aArea[$obj->fk_departament] = $obja;
							//si tiene padre cargamos
							if ($user->rights->orgman->dpto->viewtop)
							{
								if ($obja->fk_father>0 && $i == 0)
								{
									$lLoop = true;
									$fk_father = $obja->fk_father;
								//verificar si corresponde solo el padre superior directo o todos
								//while ($lLoop)
								//{
									$objatmp->fetch($fk_father);
									$aArea[$fk_father] = $objatmp;
									if (!empty($this->idsArea)) $this->idsArea.= ',';
									$this->idsArea.= $fk_father;
									if ($objatmp->fk_father > 0)
										$fk_father = $objatmp->fk_father;
									else
										$lLoop = false;
								//}
								}
							}
							if (!empty($this->idsArea)) $this->idsArea.= ',';
							$this->idsArea.= $obj->fk_departament;
						}

					}
					if ($lSon)
					{
						$obja->getlist_son($obj->fk_departament);
						if (count($obja->array) > 0)
						{
							foreach ((array) $obja->array AS $j => $objar)
							{
								$objar->privilege = $obj->privilege;
								$aArea[$objar->id] = $objar;
								if (!empty($this->idsArea)) $this->idsArea.= ',';
								$this->idsArea.= $objar->id;

				  				//nuevamente buscamos si tiene hijos del hijo
								$objaa = new Pdepartamentext($this->db);
								$objaa->getlist_son($objar->id);
								if (count($objaa->array) > 0)
								{
									foreach ((array) $objaa->array AS $k => $objara)
									{
										$objara->privilege = $obj->privilege;
										$aArea[$objara->id] = $objara;
										if (!empty($this->idsArea)) $this->idsArea.= ',';
										$this->idsArea.= $objara->id;
									}
								}
							}
						}
					}
					$i++;
				}
				$this->aArea = $aArea;
				return $num;
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getuserarea ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *  Areas a las que pertenece el usuario
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getareauser($fk_area,$order='')
	{
		global $langs;
		$aArea = array();
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_area,";
		$sql.= " t.fk_user,";
		$sql.= " t.datec,";
		$sql.= " t.tms,";
		$sql.= " t.active,";
		$sql.= " t.privilege";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_departament_user as t";
		if ($order == 'user')
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."user AS u ON t.fk_user = u.rowid ";
		$sql.= " WHERE t.fk_departament = ".$fk_area;
		$sql.= " AND t.active = 1";
		if ($order == 'user')
			$sql.= " ORDER BY u.lastname, u.firstname";
		dol_syslog(get_class($this)."::getareauser sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->lines = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$num = $this->db->num_rows($resql);
				while ($obj = $this->db->fetch_object($resql))
				{
					$line = new PdepartamentuserLine();

					$line->id = $obj->rowid;

					$line->fk_area = $obj->fk_departament;
					$line->fk_departament = $obj->fk_departament;
					$line->fk_user = $obj->fk_user;
					$line->datec = $this->db->jdate($obj->datec);
					$line->tms = $this->db->jdate($obj->tms);
					$line->active = $obj->active;
					$line->privilege = $obj->privilege;

					$this->lines[] = $line;
				}
				$this->db->free($resql);
				return $num;
			}
			$this->db->free($resql);
			return $num;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getareauser ".$this->error, LOG_ERR);
			return -1;
		}
	}

	public function updateUserDep(User $user, $notrigger = false)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters

		if (isset($this->fk_departament)) {
			 $this->fk_departament = trim($this->fk_departament);
		}

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		//$sql .= ' rowid = '.(isset($this->rowid)?$this->rowid:"null").',';
		$sql .= ' fk_departament = '.(isset($this->fk_departament)?$this->fk_departament:"null");

		$sql .= ' WHERE rowid=' . $this->id;

		//echo "CONSULTA DE UPDATE DEPARTAMENT : ".$sql;
		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
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
}
?>