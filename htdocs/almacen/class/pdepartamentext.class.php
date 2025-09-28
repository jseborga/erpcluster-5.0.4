<?php
require_once DOL_DOCUMENT_ROOT.'/almacen/class/pdepartament.class.php';

class Pdepartamentext extends Pdepartament
{
	
	/**
	 *  Return list of orders (eventuelly filtered on a user) into an array
	 *
	 *  @param      int		$brouillon      0=non brouillon, 1=brouillon
	 *  @param      User	$user           Objet user de filtre
	 *  @return     int             		-1 if KO, array with result if OK
	 */
	function liste_array($empty="")
	{
		global $conf,$langs;

		$ga = array();
		if ($empty == 1)
			$ga[0] = $langs->trans("Select");
		$sql = "SELECT p.rowid, p.ref ";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_departament AS p ";
		$sql.= " WHERE p.entity =".$conf->entity;
		$sql.= " ORDER BY p.ref ";

		$result=$this->db->query($sql);
		if ($result)
		{
			$numc = $this->db->num_rows($result);
			if ($numc)
			{
				$i = 0;
				while ($i < $numc)
				{
					$obj = $this->db->fetch_object($result);
					$ga[$obj->rowid] = $obj->ref.' ';
					$i++;
				}
			}
			return $ga;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}


}
?>