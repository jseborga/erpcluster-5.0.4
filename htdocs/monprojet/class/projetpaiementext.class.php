<?php
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpaiement.class.php';

class Projetpaiementext extends Projetpaiement
{
	public function fetch_lines()
	{
		global $conf,$langs;

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_projet_paiement,";
		$sql .= " t.ref,";
		$sql .= " t.date_paiement,";
		$sql .= " t.fk_projet_task,";
		$sql .= " t.fk_object,";
		$sql .= " t.object,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_product,";
		$sql .= " t.fk_facture_fourn,";
		$sql .= " t.detail,";
		$sql .= " t.fk_unit,";
		$sql .= " t.qty_ant,";
		$sql .= " t.qty,";
		$sql .= " t.subprice,";
		$sql .= " t.price,";
		$sql .= " t.total_ht,";
		$sql .= " t.total_ttc,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . 'projet_paiementdet' . ' as t';
		$sql .= ' WHERE t.fk_projet_paiement = ' . $this->id;
		$resql = $this->db->query($sql);
		if ($resql) 
		{
			$i = 0;
			$num = $this->db->num_rows($resql);
			if ($num) 
			{
				require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpaiementdet.class.php';

				while ($i < $num)
				{
					$line = new ProjetpaiementdetLine($this->db);

					$obj = $this->db->fetch_object($resql);

					$line->id = $obj->rowid;

					$line->fk_projet_paiement = $obj->fk_projet_paiement;
					$line->ref = $obj->ref;
					$line->date_paiement = $this->db->jdate($obj->date_paiement);
					$line->fk_projet_task = $obj->fk_projet_task;
					$line->fk_object = $obj->fk_object;
					$line->object = $obj->object;
					$line->fk_user_create = $obj->fk_user_create;
					$line->fk_user_mod = $obj->fk_user_mod;
					$line->fk_product = $obj->fk_product;
					$line->fk_facture_fourn = $obj->fk_facture_fourn;
					$line->detail = $obj->detail;
					$line->fk_unit = $obj->fk_unit;
					$line->qty_ant = $obj->qty_ant;
					$line->qty = $obj->qty;
					$line->subprice = $obj->subprice;
					$line->price = $obj->price;
					$line->total_ht = $obj->total_ht;
					$line->total_ttc = $obj->total_ttc;
					$line->datec = $this->db->jdate($obj->datec);
					$line->datem = $this->db->jdate($obj->datem);
					$line->tms = $this->db->jdate($obj->tms);
					$line->status = $obj->status;

					$this->lines[$i] = $line;
					$i++;
				}
			}
			$this->db->free($resql);

			if ($num) {
				return $num;
			} else {
				return 0;
			}		

		}
	}
}
?>