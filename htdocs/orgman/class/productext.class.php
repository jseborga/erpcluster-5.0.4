<?php
/* Copyright (C) 2017		Ramiro Queso <ramirqoues@gmail.com>
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
 *	\file       htdocs/product/class/product.class.php
 *	\ingroup    produit
 *	\brief      File of class to manage predefined products or services
 */
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';


/**
 * Class to manage products or services
 */
class Productext extends Product
{
	/**
	 *  Load a product in memory from database
	 *
	 *  @param	int		$id      			Id of product/service to load
	 *  @param  string	$ref     			Ref of product/service to load
	 *  @param	string	$ref_ext			Ref ext of product/service to load
     *  @param	int		$ignore_expression  Ignores the math expression for calculating price and uses the db value instead
	 *  @return int     					<0 if KO, 0 if not found, >0 if OK
	 */
	function listAll($filter='')
	{
		include_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

		global $langs, $conf;

		dol_syslog(get_class($this)."::fetch id=".$id." ref=".$ref." ref_ext=".$ref_ext);

		// Check parameters
		$sql = "SELECT rowid, ref, ref_ext, label, description, url, note, customcode, fk_country, price, price_ttc,";
		$sql.= " price_min, price_min_ttc, price_base_type, cost_price, default_vat_code, tva_tx, recuperableonly as tva_npr, localtax1_tx, localtax2_tx, localtax1_type, localtax2_type, tosell,";
		$sql.= " tobuy, fk_product_type, duration, seuil_stock_alerte, canvas,";
		$sql.= " weight, weight_units, length, length_units, surface, surface_units, volume, volume_units, barcode, fk_barcode_type, finished,";
		$sql.= " accountancy_code_buy, accountancy_code_sell, stock, pmp,";
		$sql.= " datec, tms, import_key, entity, desiredstock, tobatch, fk_unit,";
		$sql.= " fk_price_expression, price_autogen";
		$sql.= " FROM ".MAIN_DB_PREFIX."product";
		$sql.= " WHERE entity IN (".getEntity($this->element, 1).")";
		if ($filter)
			$sql.= $filter;

		$resql = $this->db->query($sql);
		if ( $resql )
		{
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($this->db->num_rows($resql) > 0)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$line = new Product($this->db);
					$line->id						= $obj->rowid;
					$line->ref						= $obj->ref;
					$line->ref_ext					= $obj->ref_ext;
					$line->label					= $obj->label;
					$line->description				= $obj->description;
					$line->url						= $obj->url;
					$line->note						= $obj->note;

					$line->type						= $obj->fk_product_type;
					$line->status					= $obj->tosell;
					$line->status_buy				= $obj->tobuy;
					$line->status_batch				= $obj->tobatch;

					$line->customcode				= $obj->customcode;
					$line->country_id				= $obj->fk_country;
					$line->country_code				= getCountry($this->country_id,2,$this->db);
					$line->price					= $obj->price;
					$line->price_ttc				= $obj->price_ttc;
					$line->price_min				= $obj->price_min;
					$line->price_min_ttc			= $obj->price_min_ttc;
					$line->price_base_type			= $obj->price_base_type;
					$line->cost_price    			= $obj->cost_price;
					$line->default_vat_code 		= $obj->default_vat_code;
					$line->tva_tx					= $obj->tva_tx;
				//! French VAT NPR
					$line->tva_npr					= $obj->tva_npr;
				//! Local taxes
					$line->localtax1_tx				= $obj->localtax1_tx;
					$line->localtax2_tx				= $obj->localtax2_tx;
					$line->localtax1_type			= $obj->localtax1_type;
					$line->localtax2_type			= $obj->localtax2_type;
					
					$line->finished					= $obj->finished;
					$line->duration					= $obj->duration;
					$line->duration_value			= substr($obj->duration,0,dol_strlen($obj->duration)-1);
					$line->duration_unit			= substr($obj->duration,-1);
					$line->canvas					= $obj->canvas;
					$line->weight					= $obj->weight;
					$line->weight_units				= $obj->weight_units;
					$line->length					= $obj->length;
					$line->length_units				= $obj->length_units;
					$line->surface					= $obj->surface;
					$line->surface_units			= $obj->surface_units;
					$line->volume					= $obj->volume;
					$line->volume_units				= $obj->volume_units;
					$line->barcode					= $obj->barcode;
					$line->barcode_type				= $obj->fk_barcode_type;

					$line->accountancy_code_buy		= $obj->accountancy_code_buy;
					$line->accountancy_code_sell	= $obj->accountancy_code_sell;

					$line->seuil_stock_alerte		= $obj->seuil_stock_alerte;
					$line->desiredstock             = $obj->desiredstock;
					$line->stock_reel				= $obj->stock;
					$line->pmp						= $obj->pmp;

					$line->date_creation			= $obj->datec;
					$line->date_modification		= $obj->tms;
					$line->import_key				= $obj->import_key;
					$line->entity					= $obj->entity;

					$line->ref_ext					= $obj->ref_ext;
					$line->fk_price_expression		= $obj->fk_price_expression;
					$line->fk_unit					= $obj->fk_unit;
					$line->price_autogen			= $obj->price_autogen;

					$this->lines[]= $line;
					$i++;
				}
				$this->db->free($resql);
				return $num;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}
}
