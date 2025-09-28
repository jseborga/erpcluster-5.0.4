<?php
require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefourndetfiscal.class.php';

class Facturefourndetfiscalext extends Facturefourndetfiscal
{
	var $aData;
	var $aDatadet;
	var $aDataid;
	//funcion para sumar los impuestos registrados
	//se debe considerar que aparte del IVA los otros impuestos
	//se guardaran en localtax1, localtax2, localtax3 etc
	function get_sum_taxes($id)
	{
		$sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_facture_fourn_det,";
		$sql.= " t.code_tva,";
		$sql.= " t.tva_tx,";
		$sql.= " t.total_tva,";
		$sql.= " t.total_ht,";
		$sql.= " t.total_ttc,";
		$sql.= " t.amount_base,";
		$sql.= " t.amount_ice,";
		$sql.= " t.discount ";
		
		$sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn_det_fiscal as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."facture_fourn_det as d ON t.fk_facture_fourn_det = d.rowid ";
		$sql.= " WHERE d.fk_facture_fourn = ".$id;

		dol_syslog(get_class($this)."::get_sum_taxes sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->aData = array();
		$this->aDataid = array();
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$i = 0;
				$num = $this->db->num_rows($resql);
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->aDataid[$obj->fk_facture_fourn_det] = $obj->fk_facture_fourn_det;
					if (!empty($obj->tva_tx))
						$this->aData[$obj->code_tva]['tva_tx'] = $obj->tva_tx;
					$this->aData[$obj->code_tva]['total_tva']+= $obj->total_tva;
					$this->aData[$obj->code_tva]['total_ht']+= $obj->total_ht;
					$this->aData[$obj->code_tva]['total_ttc']+= $obj->total_ttc;
					$this->aData[$obj->code_tva]['total_amountfiscal']+= $obj->amount_base;
					$this->aData[$obj->code_tva]['total_amountice']+= $obj->amount_ice;
					$this->aData[$obj->code_tva]['total_amountbase']+= $obj->amount_base;
					$this->aData[$obj->code_tva]['total_discount']+= $obj->discount;
					if (empty($obj->total_tva))
					{
						//$this->aData[$obj->code_tva]['total_amountnofiscal']+= $obj->total_ttc;
					}
					else
					{
						//$this->aData[$obj->code_tva]['total_amountfiscal']+= $obj->total_ttc;
					}
					$i++;
				}
				return $num;
			}
			else
				return 0;
		}
		return -1;
	}

	//suma por linea det
	function get_sum_taxesdet($id)
	{
		$sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_facture_fourn_det,";
		$sql.= " t.code_tva,";
		$sql.= " t.tva_tx,";
		$sql.= " t.total_tva,";
		$sql.= " t.total_ht,";
		$sql.= " t.total_ttc,";
		$sql.= " t.amount_base,";
		$sql.= " t.amount_ice,";
		$sql.= " t.discount ";
		
		$sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn_det_fiscal as t";
		$sql.= " WHERE t.fk_facture_fourn_det = ".$id;

		dol_syslog(get_class($this)."::get_sum_taxesdet sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->aDatadet = array();
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$i = 0;
				$num = $this->db->num_rows($resql);
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					if (!empty($obj->tva_tx))
						$this->aData[$obj->code_tva]['tva_tx'] = $obj->tva_tx;
					$this->aDatadet[$obj->code_tva]['total_tva']+= $obj->total_tva;
					$this->aDatadet[$obj->code_tva]['total_ht']+= $obj->total_ht;
					$this->aDatadet[$obj->code_tva]['total_ttc']+= $obj->total_ttc;
					$this->aDatadet[$obj->code_tva]['total_amountfiscal']+= $obj->amount_base;
					$this->aDatadet[$obj->code_tva]['total_amountice']+= $obj->amount_ice;
					$this->aDatadet[$obj->code_tva]['total_discount']+= $obj->discount;
					if (empty($obj->total_tva))
					{
						//$this->aData[$obj->code_tva]['total_amountnofiscal']+= $obj->total_ttc;
					}
					else
					{
						//$this->aData[$obj->code_tva]['total_amountfiscal']+= $obj->total_ttc;
					}
					$i++;
				}
				return $num;
			}
			else
				return 0;
		}
		return -1;
	}
}
?>