<?php
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';

class Entrepotext extends Entrepot
{
	public $lines;

	/**
	 *	Return clickable name (possibility with the pictogram)
	 *
	 *	@param		int		$withpicto		with pictogram
	 *	@param		string	$option			Where the link point to
	 *  @param      int     $showfullpath   0=Show ref only. 1=Show full path instead of Ref (this->fk_parent must be defined)
	 *  @param	    int   	$notooltip		1=Disable tooltip
	 *	@return		string					String with URL
	 */
		function getNomUrladd($withpicto=0, $option='',$showfullpath=0, $notooltip=0)
		{
			global $conf, $langs;
			$langs->load("stocks");

		if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

		$result='';
		$label = '';

		$label = '<u>' . $langs->trans("ShowWarehouse").'</u>';
		$label.= '<br><b>' . $langs->trans('Ref') . ':</b> ' . (empty($this->label)?$this->libelle:$this->label);
		if (! empty($this->lieu))
			$label.= '<br><b>' . $langs->trans('LocationSummary').':</b> '.$this->lieu;

		$url = DOL_URL_ROOT.'/almacen/local/fiche.php?id='.$this->id;

		$linkclose='';
		if (empty($notooltip))
		{
			if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
			{
				$label=$langs->trans("ShowWarehouse");
				$linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
			}
			$linkclose.= ' title="'.dol_escape_htmltag($label, 1).'"';
			$linkclose.=' class="classfortooltip"';
		}

		$linkstart = '<a href="'.$url.'"';
		$linkstart.=$linkclose.'>';
		$linkend='</a>';

		if ($withpicto) $result.=($link.img_object(($notooltip?'':$label), 'stock', ($notooltip?'':'class="classfortooltip"'), 0, 0, $notooltip?0:1).$linkend.' ');
		$result.=$linkstart.($showfullpath ? $this->get_full_arbo() : (empty($this->label)?$this->libelle:$this->label)).$linkend;
		return $result;
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param string $sortorder Sort Order
	 * @param string $sortfield Sort field
	 * @param int    $limit     offset limit
	 * @param int    $offset    offset limit
	 * @param array  $filter    filter array
	 * @param string $filtermode filter mode (AND or OR)
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND', $filterstatic='', $lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);
		global $conf;

		$sql  = "SELECT t.rowid, t.fk_parent, t.label, t.description, t.statut, t.lieu, t.address, t.zip, t.town, t.fk_pays as country_id";
		$sql .= " FROM ".MAIN_DB_PREFIX."entrepot AS t ";

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("solalmacendet", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if ($filterstatic) $sql.= $filterstatic;

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}

		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new Entrepot($this->db);

				$line->id             = $obj->rowid;
				$line->fk_parent      = $obj->fk_parent;
				$line->ref            = $obj->rowid;
				$line->label          = $obj->label;
				$line->libelle        = $obj->label;            // deprecated
				$line->description    = $obj->description;
				$line->statut         = $obj->statut;
				$line->lieu           = $obj->lieu;
				$line->address        = $obj->address;
				$line->zip            = $obj->zip;
				$line->town           = $obj->town;
				$line->country_id     = $obj->country_id;

				include_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
				$tmp=getCountry($line->country_id,'all');
				$line->country=$tmp['label'];
				$line->country_code=$tmp['code'];

				if ($lView && $num == 1) $this->fetch($obj->rowid);

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}
}
?>