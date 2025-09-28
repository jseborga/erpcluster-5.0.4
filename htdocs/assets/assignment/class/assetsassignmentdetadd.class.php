<?php
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdet.class.php';

class Assetsassignmentdetadd extends Assetsassignmentdet
{
	public $array;
	public $aAsset;
		//modificado
    /**
     *  Load object in memory from the database segun fk_asset
     *
     *  @param	int		$fk_asset   Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function getlist($fk_asset)
    {
    	global $langs;
    	$sql = "SELECT";
    	$sql.= " t.rowid,";
    	$sql.= " t.fk_asset_assignment,";
    	$sql.= " t.fk_asset,";
    	$sql.= " t.date_assignment,";
    	$sql.= " t.date_end,";
    	$sql.= " t.date_create,";
    	$sql.= " t.fk_user_create,";
    	$sql.= " t.fk_user_mod,";
    	$sql.= " t.date_mod,";
    	$sql.= " t.tms,";
    	$sql.= " t.active,";
    	$sql.= " t.statut";


    	$sql.= " FROM ".MAIN_DB_PREFIX."assets_assignment_det as t";
        $sql.= " INNER JOIN ".MAIN_DB_PREFIX."assets_assignment as p ON t.fk_asset_assignment = p.rowid";
    	$sql.= " WHERE t.fk_asset = ".$fk_asset;
    	dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
    	$resql=$this->db->query($sql);
    	$this->array = array();
    	if ($resql)
    	{
    		$num = $this->db->num_rows($resql);
    		if ($this->db->num_rows($resql))
    		{
    			$i = 0;
    			while ($i < $num)
    			{
    				$obj = $this->db->fetch_object($resql);
    				$objnew = new Assetsassignment($this->db);

    				$objnew->id    = $obj->rowid;		    
    				$objnew->fk_asset = $obj->fk_asset;
    				$objnew->fk_asset_assignment = $obj->fk_asset_assignment;

    				$objnew->date_assignment = $this->db->jdate($obj->date_assignment);
    				$objnew->date_end = $this->db->jdate($obj->date_end);
    				$objnew->date_create = $this->db->jdate($obj->date_create);
    				$objnew->fk_user_create = $obj->fk_user_create;
    				$objnew->fk_user_mod = $obj->fk_user_mod;
    				$objnew->date_mod = $this->db->jdate($obj->date_mod);
    				$objnew->tms = $this->db->jdate($obj->tms);
    				$objnew->active = $obj->active;
    				$objnew->statut = $obj->statut;

    				$this->array[$obj->rowid] = $objnew;
    				$i++;
    			}
    		}
    		$this->db->free($resql);
    		return 1;
    	}
    	else
    	{
    		$this->error="Error ".$this->db->lasterror();
    		dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
    		return -1;
    	}
    }
    /**
     *  Load object in memory from the database segun fk_asset_assignment
     *
     *  @param  int     $id    Id object
     *  @return int             <0 if KO, >0 if OK
     */
    function getlistassignmentact($fk_asset_assignment)
    {
        global $langs;
        //statut 0 pendiente
        //statut 1 asignado
        //statut 2 fin asignacion
        $sql = "SELECT";
        $sql.= " t.rowid,";
        $sql.= " t.fk_asset_assignment,";
        $sql.= " t.fk_asset,";
        $sql.= " t.date_assignment,";
        $sql.= " t.date_end,";
        $sql.= " t.date_create,";
        $sql.= " t.fk_user_create,";
        $sql.= " t.fk_user_mod,";
        $sql.= " t.date_mod,";
        $sql.= " t.tms,";
        $sql.= " t.active,";
        $sql.= " t.statut";

        $sql.= " FROM ".MAIN_DB_PREFIX."assets_assignment_det as t";
        $sql.= " WHERE t.fk_asset_assignment = ".$fk_asset_assignment;
        dol_syslog(get_class($this)."::getlistassignmentact sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        $this->array = array();
        
        if ($resql)
        {
            $num = $this->db->num_rows($resql);
            if ($this->db->num_rows($resql))
            {
                $i = 0;
                while ($i < $num)
                {
                    $obj = $this->db->fetch_object($resql);
                    $objnew = new Assetsassignment($this->db);

                    $objnew->id    = $obj->rowid;           
                    $objnew->fk_asset = $obj->fk_asset;
                    $objnew->fk_asset_assignment = $obj->fk_asset_assignment;

                    $objnew->date_assignment = $this->db->jdate($obj->date_assignment);
                    $objnew->date_end = $this->db->jdate($obj->date_end);
                    $objnew->date_create = $this->db->jdate($obj->date_create);
                    $objnew->fk_user_create = $obj->fk_user_create;
                    $objnew->fk_user_mod = $obj->fk_user_mod;
                    $objnew->date_mod = $this->db->jdate($obj->date_mod);
                    $objnew->tms = $this->db->jdate($obj->tms);
                    $objnew->active = $obj->active;
                    $objnew->statut = $obj->statut;

                    $this->array[$obj->rowid] = $objnew;
                    $i++;
                }
            }
            $this->db->free($resql);
            return 1;
        }
        else
        {
            $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::getlistassignmentact ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *  Load object in memory from the database segun fk_asset_assignment
     *
     *  @param  int     $id    Id object
     *  @return int             <0 if KO, >0 if OK
     */
    function getlistassetsassignment($fk_property=0,$statut=0)
    {
        global $langs;
        //statut 0 pendiente
        //statut 1 asignado
        //statut 2 fin asignacion
        $sql = "SELECT";
        $sql.= " t.rowid,";
        $sql.= " t.fk_asset_assignment,";
        $sql.= " t.fk_asset,";
        $sql.= " t.date_assignment,";
        $sql.= " t.date_end,";
        $sql.= " t.date_create,";
        $sql.= " t.fk_user_create,";
        $sql.= " t.fk_user_mod,";
        $sql.= " t.date_mod,";
        $sql.= " t.tms,";
        $sql.= " t.active,";
        $sql.= " t.statut";

        $sql.= " FROM ".MAIN_DB_PREFIX."assets_assignment_det as t";
        $sql.= " INNER JOIN ".MAIN_DB_PREFIX."assets_assignment as a ON t.fk_asset_assignment = a.rowid";
        $sql.= " WHERE t.statut IN (".$statut.")";
        if ($fk_property > 0) $sql.= " AND a.fk_property = ".$fk_property;
        //if ($fk_property < 0) $sql.= " AND a.fk_property = ".$fk_property;
        dol_syslog(get_class($this)."::getlistassetsassignment sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        //echo '<hr>'.$sql;
        $this->array = array();
        
        if ($resql)
        {
            $num = $this->db->num_rows($resql);
            if ($this->db->num_rows($resql))
            {
                $i = 0;
                while ($i < $num)
                {
                    $obj = $this->db->fetch_object($resql);
                    $objnew = new Assetsassignment($this->db);

                    $objnew->id    = $obj->rowid;           
                    $objnew->fk_asset = $obj->fk_asset;
                    $objnew->fk_asset_assignment = $obj->fk_asset_assignment;

                    $objnew->date_assignment = $this->db->jdate($obj->date_assignment);
                    $objnew->date_end = $this->db->jdate($obj->date_end);
                    $objnew->date_create = $this->db->jdate($obj->date_create);
                    $objnew->fk_user_create = $obj->fk_user_create;
                    $objnew->fk_user_mod = $obj->fk_user_mod;
                    $objnew->date_mod = $this->db->jdate($obj->date_mod);
                    $objnew->tms = $this->db->jdate($obj->tms);
                    $objnew->active = $obj->active;
                    $objnew->statut = $obj->statut;

                    $this->array[$obj->rowid] = $objnew;
                    $i++;
                }
            }
            $this->db->free($resql);
            return 1;
        }
        else
        {
            $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::getlistassignment ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *  Load object in memory from the database segun fk_asset_assignment
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function getlistassignment($fk_asset_assignment,$fk_property=0,$statut=0)
    {
    	global $langs;
		//statut 0 pendiente
		//statut 1 asignado
		//statut 2 fin asignacion
    	$sql = "SELECT";
    	$sql.= " t.rowid,";
    	$sql.= " t.fk_asset_assignment,";
    	$sql.= " t.fk_asset,";
    	$sql.= " t.date_assignment,";
    	$sql.= " t.date_end,";
    	$sql.= " t.date_create,";
    	$sql.= " t.fk_user_create,";
    	$sql.= " t.fk_user_mod,";
    	$sql.= " t.date_mod,";
    	$sql.= " t.tms,";
    	$sql.= " t.active,";
    	$sql.= " t.statut";

    	$sql.= " FROM ".MAIN_DB_PREFIX."assets_assignment_det as t";
        $sql.= " INNER JOIN ".MAIN_DB_PREFIX."assets_assignment as a ON t.fk_asset_assignment = a.rowid";
    	$sql.= " WHERE t.fk_asset_assignment = ".$fk_asset_assignment;
        if ($fk_property) $sql.= " AND a.fk_property = ".$fk_property;
    	$sql.= " AND t.statut IN (".$statut.")";
    	dol_syslog(get_class($this)."::getlistassignment sql=".$sql, LOG_DEBUG);
    	$resql=$this->db->query($sql);
        //echo '<hr>'.$sql;
    	$this->array = array();
    	
    	if ($resql)
    	{
    		$num = $this->db->num_rows($resql);
    		if ($this->db->num_rows($resql))
    		{
    			$i = 0;
    			while ($i < $num)
    			{
    				$obj = $this->db->fetch_object($resql);
    				$objnew = new Assetsassignment($this->db);

    				$objnew->id    = $obj->rowid;		    
    				$objnew->fk_asset = $obj->fk_asset;
    				$objnew->fk_asset_assignment = $obj->fk_asset_assignment;

    				$objnew->date_assignment = $this->db->jdate($obj->date_assignment);
    				$objnew->date_end = $this->db->jdate($obj->date_end);
    				$objnew->date_create = $this->db->jdate($obj->date_create);
    				$objnew->fk_user_create = $obj->fk_user_create;
    				$objnew->fk_user_mod = $obj->fk_user_mod;
    				$objnew->date_mod = $this->db->jdate($obj->date_mod);
    				$objnew->tms = $this->db->jdate($obj->tms);
    				$objnew->active = $obj->active;
    				$objnew->statut = $obj->statut;

    				$this->array[$obj->rowid] = $objnew;
    				$i++;
    			}
    		}
    		$this->db->free($resql);
    		return 1;
    	}
    	else
    	{
    		$this->error="Error ".$this->db->lasterror();
    		dol_syslog(get_class($this)."::getlistassignment ".$this->error, LOG_ERR);
    		return -1;
    	}
    }

    /**
     *  Load object in memory from the database segun fk_asset_assignment
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function getlistassignment_user($user,$fk_property=0,$statut=0)
    {
    	global $langs;
    	$sql = "SELECT";
    	$sql.= " t.rowid,";
    	$sql.= " t.fk_asset_assignment,";
    	$sql.= " t.fk_asset,";
    	$sql.= " t.date_assignment,";
    	$sql.= " t.date_end,";
    	$sql.= " t.date_create,";
    	$sql.= " t.fk_user_create,";
    	$sql.= " t.fk_user_mod,";
    	$sql.= " t.date_mod,";
    	$sql.= " t.tms,";
    	$sql.= " t.active,";
    	$sql.= " t.statut";

    	$sql.= " FROM ".MAIN_DB_PREFIX."assets_assignment_det as t";
    	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."assets_assignment as a ON t.fk_asset_assignment = a.rowid";
    	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."m_property_user as u ON u.fk_property = a.fk_property";
    	$sql.= " WHERE u.fk_user = ".$user->id;
        if ($fk_property) $sql.= " AND a.fk_property IN (".$fk_property.")";
    	$sql.= " AND t.statut IN (".$statut.")";
    	dol_syslog(get_class($this)."::getlistassignment_user sql=".$sql, LOG_DEBUG);
    	$resql=$this->db->query($sql);
    	$this->array = array();
    	
    	$this->aAsset = array();
    	if ($resql)
    	{
    		$num = $this->db->num_rows($resql);
    		if ($this->db->num_rows($resql))
    		{
    			$i = 0;
    			while ($i < $num)
    			{
    				$obj = $this->db->fetch_object($resql);
    				$objnew = new Assetsassignment($this->db);

    				$objnew->id    = $obj->rowid;		    
    				$objnew->fk_asset = $obj->fk_asset;
    				$objnew->fk_asset_assignment = $obj->fk_asset_assignment;

    				$objnew->date_assignment = $this->db->jdate($obj->date_assignment);
    				$objnew->date_end = $this->db->jdate($obj->date_end);
    				$objnew->date_create = $this->db->jdate($obj->date_create);
    				$objnew->fk_user_create = $obj->fk_user_create;
    				$objnew->fk_user_mod = $obj->fk_user_mod;
    				$objnew->date_mod = $this->db->jdate($obj->date_mod);
    				$objnew->tms = $this->db->jdate($obj->tms);
    				$objnew->active = $obj->active;
    				$objnew->statut = $obj->statut;

    				$this->array[$obj->rowid] = $objnew;
    				$this->aAsset[$obj->fk_asset] = $obj->fk_asset;
    				$i++;
    			}
    		}
    		$this->db->free($resql);
    		return 1;
    	}
    	else
    	{
    		$this->error="Error ".$this->db->lasterror();
    		dol_syslog(get_class($this)."::getlistassignment_user ".$this->error, LOG_ERR);
    		return -1;
    	}
    }

    /**
     *  Load object in memory from the database
     * ultimo registro activo
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_ult($fk_asset,$statut = 1,$limit=1)
    {
    	global $langs;
    	$sql = "SELECT";
    	$sql.= " t.rowid,";

    	$sql.= " t.fk_asset_assignment,";
    	$sql.= " t.fk_asset,";
    	$sql.= " t.date_assignment,";
    	$sql.= " t.date_end,";
    	$sql.= " t.date_create,";
    	$sql.= " t.fk_user_create,";
    	$sql.= " t.fk_user_mod,";
    	$sql.= " t.date_mod,";
    	$sql.= " t.tms,";
    	$sql.= " t.active,";
    	$sql.= " t.statut";
    	$sql.= " FROM ".MAIN_DB_PREFIX."assets_assignment_det as t";
    	$sql.= " WHERE t.fk_asset = ".$fk_asset;
    	$sql.= " AND t.statut IN (".$statut.")";
        $sql.= " ORDER BY t.date_assignment DESC, t.rowid DESC";
        $sql.= " LIMIT ".$limit;
        //echo '<hr>'.$sql;
    	dol_syslog(get_class($this)."::fetch_ult sql=".$sql, LOG_DEBUG);
    	$resql=$this->db->query($sql);
    	if ($resql)
    	{
            $num = $this->db->num_rows($resql);
    		if ($this->db->num_rows($resql))
    		{
    			$obj = $this->db->fetch_object($resql);

    			$this->id    = $obj->rowid;

    			$this->fk_asset_assignment = $obj->fk_asset_assignment;
    			$this->fk_asset = $obj->fk_asset;
    			$this->date_assignment = $this->db->jdate($obj->date_assignment);
    			$this->date_end = $this->db->jdate($obj->date_end);
    			$this->date_create = $this->db->jdate($obj->date_create);
    			$this->fk_user_create = $obj->fk_user_create;
   				$this->fk_user_mod = $obj->fk_user_mod;
   				$this->date_mod = $this->db->jdate($obj->date_mod);
    			$this->tms = $this->db->jdate($obj->tms);
   				$this->active = $obj->active;
    			$this->statut = $obj->statut;


    		}
    		$this->db->free($resql);

    		return $num;
    	}
    	else
    	{
    		$this->error="Error ".$this->db->lasterror();
    		dol_syslog(get_class($this)."::fetch_ult ".$this->error, LOG_ERR);
    		return -1;
    	}
    }

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$fk_asset    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_active($fk_asset)
    {
    	global $langs;
    	$sql = "SELECT";
    	$sql.= " t.rowid,";
    	$sql.= " t.fk_asset_assignment,";
    	$sql.= " t.fk_asset,";
    	$sql.= " t.date_assignment,";
    	$sql.= " t.date_end,";
    	$sql.= " t.date_create,";
    	$sql.= " t.fk_user_create,";
    	$sql.= " t.fk_user_mod,";
    	$sql.= " t.date_mod,";
    	$sql.= " t.tms,";
    	$sql.= " t.active,";
    	$sql.= " t.statut";


    	$sql.= " FROM ".MAIN_DB_PREFIX."assets_assignment_det as t";
    	$sql.= " WHERE t.fk_asset = ".$fk_asset;
    	$sql.= " AND t.statut = 1";
    	dol_syslog(get_class($this)."::fetch_active sql=".$sql, LOG_DEBUG);
    	$resql=$this->db->query($sql);
    	if ($resql)
    	{
            $num = $this->db->num_rows($resql);
    		if ($this->db->num_rows($resql))
    		{
    			$obj = $this->db->fetch_object($resql);

    			$this->id    = $obj->rowid;

    			$this->fk_asset_assignment = $obj->fk_asset_assignment;
    			$this->fk_asset = $obj->fk_asset;
    			$this->date_assignment = $this->db->jdate($obj->date_assignment);
    			$this->date_end = $this->db->jdate($obj->date_end);
    			$this->date_create = $this->db->jdate($obj->date_create);
    			$this->fk_user_create = $obj->fk_user_create;
   				$this->fk_user_mod = $obj->fk_user_mod;
   				$this->date_mod = $this->db->jdate($obj->date_mod);
    			$this->tms = $this->db->jdate($obj->tms);
   				$this->active = $obj->active;
    			$this->statut = $obj->statut;
    		}
    		$this->db->free($resql);

    		return $num;
    	}
    	else
    	{
    		$this->error="Error ".$this->db->lasterror();
    		dol_syslog(get_class($this)."::fetch_active ".$this->error, LOG_ERR);
    		return -1;
    	}
    }
}
?>