<?php
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabaccounting.class.php';

class Contabaccountingext extends Contabaccounting
{
	/**
     *  Return combo list of activated countries, into language of user
     *
     *  @param	string	$selected       Id or Code or Label of preselected country
     *  @param  string	$htmlname       Name of html select object
     *  @param  string	$htmloption     Options html on select object
     *  @param	string	$maxlength		Max length for labels (0=no limit)
     *  @return string           		HTML string with select
    */
	function select_account($selected='',$htmlname='fk_account',$htmloption='',$maxlength=0,$showempty=0,$type=0,$mode=1)
	{
		global $conf,$langs;

		$langs->load("contab@contab");

		$out='';
		$countryArray=array();
		$label=array();
		if ($mode == 1)
			$sql = "SELECT rowid, ref as code_iso, cta_name as label";
		else
			$sql = "SELECT rowid, cta_name as code_iso, ref as label";

		$sql.= " FROM ".MAIN_DB_PREFIX."contab_accounting";
		$sql.= " WHERE statut = 1";
		if ($type)
			$sql.= " AND cta_class = ".$type;
		$sql.= " ORDER BY ref ASC";
        
		dol_syslog(get_class($this)."::select_account sql=".$sql);
		$resql=$this->db->query($sql);

		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
			if ($showempty)
			{
				$out.= '<option value="-1"';
				if ($selected == -1) $out.= ' selected="selected"';
				$out.= '>&nbsp;</option>';
			}

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				$foundselected=false;

				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$countryArray[$i]['rowid'] 		= $obj->rowid;
					$countryArray[$i]['code_iso'] 	= $obj->code_iso;
					$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Accounting".$obj->code_iso)!="Accounting".$obj->code_iso?$langs->transnoentitiesnoconv("Accounting".$obj->code_iso):($obj->label!='-'?$obj->label:''));
					$label[$i] 	= $countryArray[$i]['label'];
					$i++;
				}

				array_multisort($label, SORT_ASC, $countryArray);

				foreach ($countryArray as $row)
				{
                    //print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
					if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
					{
						$foundselected=true;
						$out.= '<option value="'.$row['rowid'].'" selected="selected">';
					}
					else
					{
						$out.= '<option value="'.$row['rowid'].'">';
					}
					$out.= dol_trunc($row['label'],$maxlength,'middle');
					if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
					$out.= '</option>';
				}
			}
			$out.= '</select>';
		}
		else
		{
			dol_print_error($this->db);
		}

		return $out;
	}



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
    	$sql = "SELECT ca.rowid, ca.ref, ca.cta_name";
    	$sql.= " FROM ".MAIN_DB_PREFIX."contab_accounting AS ca ";
    	$sql.= " ORDER BY ca.ref ";

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
    				$ga[$obj->rowid] = $obj->ref.' '.$obj->cta_name;
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

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$account    account
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_account($account)
    {
    	global $langs,$conf;
    	$sql = "SELECT";
    	$sql.= " t.rowid,";

    	$sql.= " t.ref,";
    	$sql.= " t.entity,";
    	$sql.= " t.cta_class,";
    	$sql.= " t.cta_normal,";
    	$sql.= " t.cta_top,";
    	$sql.= " t.cta_name,";
    	$sql.= " t.tms,";
    	$sql.= " t.statut";


    	$sql.= " FROM ".MAIN_DB_PREFIX."contab_accounting as t";
    	$sql.= " WHERE t.ref = ".$account;
    	$sql.= " AND entity = ".$conf->entity;
    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
    	$resql=$this->db->query($sql);
    	if ($resql)
    	{
    		if ($this->db->num_rows($resql))
    		{
    			$obj = $this->db->fetch_object($resql);

    			$this->id    = $obj->rowid;

    			$this->ref = $obj->ref;
    			$this->entity = $obj->entity;
    			$this->cta_class = $obj->cta_class;
    			$this->cta_normal = $obj->cta_normal;
    			$this->cta_top = $obj->cta_top;
    			$this->cta_name = $obj->cta_name;
    			$this->tms = $obj->tms;
    			$this->statut = $obj->statut;
    		}
    		$this->db->free($resql);
    		return 1;
    	}
    	else
    	{
    		$this->error="Error ".$this->db->lasterror();
    		dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
    		return -1;
    	}
    }

    function list_account($accountini,$accountfin)
    {
    	global $conf;
    	$sql = "SELECT ca.ref, ca.cta_normal ";
    	$sql.= " FROM ".MAIN_DB_PREFIX."contab_accounting AS ca ";
    	$sql.= " WHERE ca.entity = ".$conf->entity;
    	$sql.= " AND ca.ref BETWEEN '".$accountini."' AND '".$accountfin."' ";
    	$sql.= " ORDER BY ca.ref ";
    	$aArray = array();
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
    				$aArray[$obj->ref] = $obj->cta_normal;
    				$i++;
    			}
    		}
    		return $aArray;
    	}
    	else
    	{
    		dol_print_error($this->db);
    		return -1;
    	}
    }

}
?>