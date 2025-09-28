<?php
require_once DOL_DOCUMENT_ROOT.'/expedition/class/expedition.class.php';

class Expeditionext extends Expedition
{
		/**
     *	Return clicable link of object (with eventually picto)
     *
     *	@param      int			$withpicto      Add picto into link
     *	@param      int			$option         Where point the link
     *	@param      int			$max          	Max length to show
     *	@param      int			$short			Use short labels
     *	@return     string          			String with URL
     */
	function getNomUrladd($withpicto=0,$option=0,$max=0,$short=0)
	{
		global $langs;

		$result='';
        $label = '<u>' . $langs->trans("ShowSending") . '</u>';
        if (! empty($this->ref))
            $label .= '<br><b>' . $langs->trans('Ref') . ':</b> '.$this->ref;

		$url = DOL_URL_ROOT.'/expedition/card.php?id='.$this->id;

		if ($short) return $url;

        $linkstart = '<a href="'.$url.'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
		$linkend='</a>';

		$picto='sending';

		if ($withpicto) $result.=($linkstart.img_object($label, $picto, 'class="classfortooltip"').$linkend);
		if ($withpicto && $withpicto != 2) $result.=' ';
		$result.=$linkstart.$this->ref.$linkend;
		return $result;
	}

}
class ExpeditionLigneext extends ExpeditionLigne
{
	var $rowid;
	var $fk_entrepot;
	var $fk_origin_line;
	var $fk_expedition;
	var $qty;
	var $rang;

	function fetch($id)
	{
		global $conf, $langs;
		if ($id <=0) return -1;
		$sql = " SELECT t.fk_rowid, t.fk_expedition, t.fk_origin_line, t.fk_entrepot, t.qty, t.rang ";
		$sql.= " FROM ".MAIN_DB_PREFIX."expeditiondet AS t";
		$sql.= " WHERE t.fk_rowid = ".$id;
		$resql = $this->db->query($sql);
		dol_syslog(get_class($this)."::fetch", LOG_DEBUG);
		if ($resql)
		{
			$obj = $this->db->fetch_object($resql);
			if ($obj)
			{
				$this->rowid = $obj->rowid;
				$this->fk_expedition = $obj->fk_expedition;
				$this->fk_origin_line = $obj->fk_origin_line;
				$this->fk_entrepot = $obj->fk_entrepot;
				$this->qty = $obj->qty;
				$this->rang = $obj->rang;

			}
			$this->db->free($result);
			return 1;
		}
		else
		{
            $this->error=$this->db->error();
			return -1;
		}
	}
}
?>