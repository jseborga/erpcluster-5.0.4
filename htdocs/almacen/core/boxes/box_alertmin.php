<?php
/* Copyright (C) 2010 Regis Houssin  <regis.houssin@capnetworks.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 		\file       htdocs/core/boxes/box_contracts.php
 * 		\ingroup    contracts
 * 		\brief      Module de generation de l'affichage de la box contracts
 */
include_once DOL_DOCUMENT_ROOT.'/core/boxes/modules_boxes.php';


/**
 * Class to manage the box to show last thirdparties
 */
class box_alertmin extends ModeleBoxes
{
	var $boxcode="alertmin";
	var $boximg="object_warehouseorder";
	var $boxlabel="BoxAlertmin";
	var $depends = array("product");	// conf->contrat->enabled

	var $db;
	var $param;

	var $info_box_head = array();
	var $info_box_contents = array();


	/**
	 *  Load data for box to show them later
	 *
	 *  @param	int		$max        Maximum number of records to load
	 *  @return	void
	 */
	function loadBox($max=5)
	{
		global $user, $langs, $db, $conf;

		$this->max=$max;

		include_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		$objProduct=new Product($db);

		$this->info_box_head = array('text' => $langs->trans("BoxTitleAlertmin",$max));
		if ($user->rights->produit->lire)
		{
			$sql = " SELECT p.rowid, p.ref, p.label, ";
			$sql.= " ps.reel, ps.fk_entrepot, ";
			$sql.= " e.label AS entrepotlabel ";

			$sql.= " FROM ".MAIN_DB_PREFIX."product as p ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product_stock AS ps ON p.rowid = ps.fk_product";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."entrepot AS e ON ps.fk_entrepot = e.rowid";
			$sql.= " WHERE ps.reel <= p.seuil_stock_alerte ";
			$sql.= " AND p.entity = ".$conf->entity;
			$sql.= " ORDER BY ps.reel DESC, p.ref ";
			$sql.= $db->plimit($max, 0);

			$resql = $db->query($sql);
			if ($resql)
			{
				$num = $db->num_rows($resql);
				$now=dol_now();

				$i = 0;

				while ($i < $num)
				{
					$objp = $db->fetch_object($resql);
					$datec=$db->jdate($objp->datec);
					$late = '';

					// fin_validite is no more on contract but on services
					// if ($objp->fk_statut == 1 && $dateterm < ($now - $conf->contrat->cloture->warning_delay)) { $late = img_warning($langs->trans("Late")); }

					$this->info_box_contents[$i][0] = array('td' => 'align="left" width="16"',
					'text' => ($objp->ref?$objp->ref:$objp->rowid),
					'logo' => $this->boximg,
					'url' => DOL_URL_ROOT."/product/card.php?id=".$objp->rowid);

					$this->info_box_contents[$i][1] = array('td' => 'align="left"',
					'text' => $objp->label);
					$this->info_box_contents[$i][2] = array('td' => 'align="left"',
					'text' => $objp->entrepotlabel,
					'url' => DOL_URL_ROOT."/almacen/local/fiche.php?id=".$objp->fk_entrepot);

					$this->info_box_contents[$i][3] = array('td' => 'align="left"',
					'text' => $objp->reel);

					//$this->info_box_contents[$i][2] = array('td' => 'align="left" width="16"',
					//	'logo' => 'company',
					//	'url' => DOL_URL_ROOT."/comm/fiche.php?socid=".$objp->socid);

					//$this->info_box_contents[$i][3] = array('td' => 'align="left"',
					//	'text' => dol_trunc($objp->nom,40),
					//	'url' => DOL_URL_ROOT."/comm/fiche.php?socid=".$objp->socid);

					$this->info_box_contents[$i][4] = array('td' => 'align="right"',
						'text' => dol_print_date($datec,'day'));

					//$this->info_box_contents[$i][5] = array('td' => 'align="right" class="nowrap"',
					//	'text' => $contractstatic->getLibStatut(6),
					//	'asis'=>1
					//	);

					$i++;
				}

				if ($num==0) $this->info_box_contents[$i][0] = array('td' => 'align="center"','text'=>$langs->trans("NoRecordedContracts"));

				$db->free($resql);
			}
			else
			{
				$this->info_box_contents[0][0] = array(  'td' => 'align="left"',
					'maxlength'=>500,
					'text' => ($db->error().' sql='.$sql));
			}
		}
		else
		{
			$this->info_box_contents[0][0] = array('td' => 'align="left"',
				'text' => $langs->trans("ReadPermissionNotAllowed"));
		}
	}

	/**
	 *	Method to show box
	 *
	 *	@param	array	$head       Array with properties of box title
	 *	@param  array	$contents   Array with properties of box lines
	 *	@return	void
	 */
	function showBox($head = null, $contents = null,$nooutput=0)
	{
		parent::showBox($this->info_box_head, $this->info_box_contents);
	}

}

