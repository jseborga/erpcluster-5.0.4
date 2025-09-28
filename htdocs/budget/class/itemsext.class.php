<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';

class Itemsext extends Items
{

	public function form_select($selected='',$htmlname='fk_item',$htmloption='',$showempty=0,$campo='rowid')
	{
		global $user;
		if (count($this->lines)>0)
		{
			$html.= '<select class="flat" name="'.$htmlname.'" '.$htmloption.'>';
			if ($showempty)
			{
				$html.= '<option value="0">&nbsp;</option>';
			}
			if ($selected <> 0 && $selected == '-1')
			{
				$html.= '<option value="-1" selected="selected">'.$langs->trans('To be defined').'</option>';
			}
			$num = count($this->lines);
			$i = 0;
			if ($num)
			{
				foreach ($this->lines AS $j => $obj)
				{
					if (!empty($selected) && $selected == $obj->$campo)
					{
						$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->ref.' v.'.$obj->version.' - '.$obj->detail.'</option>';
					}
					else
					{
						$html.= '<option value="'.$obj->$campo.'">'.$obj->ref.' v.'.$obj->version.' - '.$obj->detail.'</option>';
					}
					$i++;
				}
			}
			$html.= '</select>';
			return $html;
		}
	}

	function clone_items($user,$fk_budget,$fk_budget_task,$fk_region,$fk_sector)
	{
		global $conf,$langs;
		$now = dol_now();
		$aStrbudget = unserialize($_SESSION['aStrbudget']);

		$aStruct = $aStrbudget[$fk_budget];
		//primero recuperamos la lista de items_product
		require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsgroupext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsregion.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsproduct.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsproductregion.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsproduction.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskproduct.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskproduction.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/productbudget.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/productasset.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskresourceext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetgeneral.class.php';
		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

		$objItemsgroup = new Itemsgroupext($this->db);
		$objItemsregion = new Itemsregion($this->db);
		$objItemsproduct = new Itemsproduct($this->db);
		$objItemsproductregion = new Itemsproductregion($this->db);
		$objItemsproduction = new Itemsproduction($this->db);
		$objBudgettaskproduct = new Budgettaskproduct($this->db);
		$objBudgettaskproduction = new Budgettaskproduction($this->db);
		$objProductbudget = new Productbudget($this->db);
		$objBudgettaskresource = new Budgettaskresourceext($this->db);
		$objProductasset = new Productasset($this->db);
		$objProduct = new Product($this->db);
		$objCategorie = new Categorie($this->db);
		$objGeneral = new Budgetgeneral($this->db);

		$res = $objItemsregion->fetch(0,$this->id,$fk_region,$fk_sector);
		if ($res==0)
		{
			setEventMessages($langs->trans('Newitempendingvalidation'),null,'warnings');
			return 1;
		}
		elseif ($res<0)
		{
			$error++;
			$error=97;
			echo ' id '.$this->id.' '.$fk_region.' '.$fk_sector;
			setEventMessages($objItemsregion->error,$objItemsregion->errors,'errors');
		}
		$filter = " AND t.fk_item = ".$this->id;
		$filter.= " AND t.active = 1";
		$res = $objItemsproduct->fetchAll('','',0,0,array(),'AND',$filter);
		//$this->db->begin();
		if ($res>0)
		{
			$lines = $objItemsproduct->lines;
			foreach ($lines AS $j => $line)
			{
				//recuperamos información de itemsproduct region
				$objItemsproductregion->fetch(0,$line->id,$fk_region,$fk_sector);
				//tenemos que buscar si el fk_product o label esta dentro de los productos del proyecto
				$fk_product = $line->fk_product;
				$detail = $line->label;
				$group_structure = $line->group_structure;

				$code_structure = $aStruct['aStrgroupcat'][$group_structure];
				$fk_unit = 0;
				if ($fk_product>0)
				{
					$ref_product = $line->ref;
					if($objProduct->fetch($fk_product)>0)
					{
						//buscamos la categoria a la que pertenece
						$detail = $objProduct->label;
						$ref_product = $objProduct->ref;
						$fk_unit = $objProduct->fk_unit;
						$aCat = $objCategorie->containing($fk_product, 'product', 'id');
						foreach ((array) $aCat AS $i => $fk)
						{
							if ($aStruct['aStrcatcode'][$fk])
							{
								$code_structure = $aStruct['aStrcatcode'][$fk];
								//cambiamos para que guarde la categoria del producto
								$code_structure = $fk;
							}
						}
						if (empty($code_structure) || $code_structure <0)
						{
							setEventMessages($langs->trans('Error, el producto no tiene categoría'),null,'errors');
							$error++;
							$error=101;
						}
					}
				}
				else
				{
					$fk_product = 0;
					$ref_product = $line->ref;
					$detail = $line->label;
				}
				//verificamos si existe en producto budget

				//$filterstatic = " ";
				//$lClose=false;
				$newRefproduct = $fk_budget."|".$ref_product;
				//if ($ref_product)
				//{
				//	$lClose = true;
				//	$filterstatic = " AND UPPER(ref) = '". $fk_budget."|".$ref_product."' ";
				//}
				//if (!empty($filterstatic)) $filterstatic.= " OR ";
				//else $filterstatic.= " ";
				//$filterstatic.= " UPPER(label) = '".$detail."'";
				//$filterstatic.= " ";
				//if ($lClose) $filterstatic.=")";
				
				$filterstatic.= " AND t.fk_budget =".$fk_budget;

				$respb = $objProductbudget->fetchAll('','',0,0,array($newRefproduct,$detail),'OR',$filterstatic,true);
				if (empty($respb))
				{
					if (empty($code_structure) || $code_structure <0)
					{
						setEventMessages($langs->trans('Error, el producto no tiene categoría'),null,'errors');
						$error++;
						$error=98;
					}
					if (!$error)
					{
						$lUpdate = false;
						if (empty($ref_product)) $lUpdate = true;
						$objProductbudget->fk_product = $fk_product+0;
						$objProductbudget->fk_budget = $fk_budget;
						$objProductbudget->ref = $fk_budget.'|'.$ref_product;
						$objProductbudget->label = $detail;
						$objProductbudget->fk_unit = $fk_unit+0;
						$objProductbudget->code_structure = $code_structure;
						$objProductbudget->group_structure = $group_structure;
						$objProductbudget->quant = 0;
						$objProductbudget->percent_prod = ($objItemsproductregion->price_productive?$objItemsproductregion->price_productive:100);
						$objProductbudget->amount_noprod = ($objItemsproductregion->amount_noprod?$objItemsproductregion->amount_noprod:0);
						$objProductbudget->amount = ($objItemsproductregion->amount?$objItemsproductregion->amount:0);
						$objProductbudget->commander = ($objItemsproductregion->commander?$objItemsproductregion->commander:0);
						$objProductbudget->performance = ($objItemsproductregion->performance?$objItemsproductregion->performance:0);
						$objProductbudget->units = ($objItemsproductregion->units?$objItemsproductregion->units:0);
						$objProductbudget->price_productive = ($objItemsproductregion->price_productive?$objItemsproductregion->price_productive:0);
						$objProductbudget->price_improductive = ($objItemsproductregion->price_improductive?$objItemsproductregion->price_improductive:0);
						$objProductbudget->fk_origin = ($objItemsproductregion->fk_origin?$objItemsproductregion->fk_origin:0);
						$objProductbudget->percent_origin = ($objItemsproductregion->percent_origin?$objItemsproductregion->percent_origin:100);

						$objProductbudget->fk_user_create = $user->id;
						$objProductbudget->fk_user_mod = $user->id;
						$objProductbudget->date_create = $now;
						$objProductbudget->date_mod = $now;
						$objProductbudget->tms = $now;
						$objProductbudget->status = 1;
						$fk_product_budget = $objProductbudget->create($user);
						if ($fk_product_budget<=0)
						{
							setEventMessages($objProductbudget->error,$objProductbudget->errors,'errors');
							$error++;
							$error=102;
						}
						//actualizamos si lUpdate == true
						if ($lUpdate == true)
						{
							$objProductbudget->fetch($fk_product_budget);
							$objProductbudget->ref .= '(PROV)'.$fk_product_budget;
							$respb = $objProductbudget->update($user);
							if ($respb <=0)
							{
								setEventMessages($objprodbtmp->error,$objprodbtmp->errors,'errors');
								$error++;
								$error=103;
							}
						}
					}
				}
				elseif($respb==1)
				{
					$fk_product_budget = $objProductbudget->id;
					$ref_product = $objProductbudget->ref;
					$fk_product = $objProductbudget->fk_product;
					$fk_unit = $objProductbudget->fk_unit;
					$detail = $objProductbudget->label;
					$price = $objProductbudget->amount;
					$code_structure = $objProductbudget->code_structure;
				}
				else
				{
					$error++;
					$error=99;
					if ($respb<0)
					setEventMessages($objProductbudget->error,$objProductbudget->errors,'errors');
					else
					setEventMessages($langs->trans('Existe muchos registros'),null,'errors');
				}

				//creamos un registro en budgettaskresource
				if (!$error)
				{
					$codenew = generarcodigo(6);
					$objBudgettaskresource->fk_budget_task = $fk_budget_task;
					$objBudgettaskresource->ref = '(PROV)'.$codenew;
					$objBudgettaskresource->code_structure = $code_structure;
					$objBudgettaskresource->fk_product = $fk_product+0;
					$objBudgettaskresource->detail = $line->label;
					$objBudgettaskresource->formula = $line->formula;
					//se carga de objItemsproductregion
					$objBudgettaskresource->commander = ($objItemsproductregion->commander?$objItemsproductregion->commander:0);
					$objBudgettaskresource->performance = ($objItemsproductregion->performance?$objItemsproductregion->performance:0);
					$objBudgettaskresource->price_productive = ($objItemsproductregion->price_productive?$objItemsproductregion->price_productive:0);
					$objBudgettaskresource->price_improductive = ($objItemsproductregion->price_improductive?$objItemsproductregion->price_improductive:0);

					$objBudgettaskresource->fk_product_budget = $fk_product_budget+0;
					$objBudgettaskresource->fk_unit = $fk_unit+0;
					$objBudgettaskresource->quant = price2num(($objItemsproductregion->performance?$objItemsproductregion->performance:0),$objGeneral->decimal_quant);
					$objBudgettaskresource->percent_prod = ($objItemsproductregion->price_productive>0?$objItemsproductregion->price_productive:100);
					$objBudgettaskresource->amount_noprod = ($objItemsproductregion->amount_noprod?$objItemsproductregion->amount_noprod:0);
					$objBudgettaskresource->amount = ($objItemsproductregion->amount?$objItemsproductregion->amount:0);
					$objBudgettaskresource->rang = 1;
					$objBudgettaskresource->priority = 0;
					$objBudgettaskresource->date_create = dol_now();
					$objBudgettaskresource->date_mod = dol_now();
					$objBudgettaskresource->tms = dol_now();
					$objBudgettaskresource->fk_user_create = $user->id;
					$objBudgettaskresource->fk_user_mod = $user->id;
					$objBudgettaskresource->status = 1;

					$result=$objBudgettaskresource->create($user);
					if ($result<=0)
					{
						$error++;
						$error=104;
						// Creation KO
						setEventMessages($objBudgettaskresource->error, $objBudgettaskresource->errors, 'errors');
					}
				}
				//echo '<hr>result '.$result;exit;
				//buscamos em itemproduction
				$filterp = " AND t.fk_item = ".$this->id;
				$filterp.= " AND t.active = 1";
				$filterp.= " AND t.fk_items_product = ".$line->id;
				$filterp.= " AND t.fk_region = ".$fk_region;
				$filterp.= " AND t.fk_sector = ".$fk_sector;
				$resp = $objItemsproduction->fetchAll('','',0,0,array(),'AND',$filterp);

				if ($resp>0)
				{
					//creamos
					$linesp = $objItemsproduction->lines;
					foreach ($linesp AS $j => $linep)
					{
						//vamos a buscar en budget_task_production
						$resbtp = $objBudgettaskproduction->fetch(0,$fk_budget_task,$linep->fk_variable,$fk_product_budget);
						if (empty($resbtp))
						{
							$objBudgettaskproduction->initAsSpecimen();
							$objBudgettaskproduction->fk_budget_task = $fk_budget_task;
							$objBudgettaskproduction->fk_variable = $linep->fk_variable;
							$objBudgettaskproduction->fk_product_budget = $fk_product_budget;
							$objBudgettaskproduction->quantity = $linep->quantity;
							$objBudgettaskproduction->active = ($linep->active?$linep->active:1);
							$objBudgettaskproduction->fk_object = $linep->id;
							$objBudgettaskproduction->fk_user_create = $user->id;
							$objBudgettaskproduction->fk_user_mod = $user->id;
							$objBudgettaskproduction->datec = $now;
							$objBudgettaskproduction->datem = $now;
							$objBudgettaskproduction->tms = $now;
							$objBudgettaskproduction->status = 1;
							$resp = $objBudgettaskproduction->create($user);
							if ($resp<=0)
							{
								$error++;
								$error=105;
								setEventMessages($objBudgettaskproduction->error,$objBudgettaskproduction->errors,'errors');
							}
						}
					}
				}
			}
		}

		if (!$error) return 1;
		else return $error * -1;
	}

	function getNomUrladd($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $db, $conf, $langs;
		global $dolibarr_main_authentication, $dolibarr_main_demo;
		global $menumanager;

		if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

		$result = '';
		$companylink = '';

		$label = '<u>' . $langs->trans("Item") . '</u>';
		$label.= '<br>';
		$label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;
		$label.= '<br>';
		$label.= '<b>' . $langs->trans('Label') . ':</b> ' . $this->detail;

		$url = DOL_URL_ROOT.'/budget/items/'.'card.php?id='.$this->id;

		$linkclose='';
		if (empty($notooltip))
		{
			if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
			{
				$label=$langs->trans("ShowProject");
				$linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
			}
			$linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
			$linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
		}
		else $linkclose = ($morecss?' class="'.$morecss.'"':'');

		$linkstart = '<a href="'.$url.'"';
		$linkstart.=$linkclose.'>';
		$linkend='</a>';

		if ($withpicto)
		{
			$result.=($linkstart.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
			if ($withpicto != 2) $result.=' ';
		}
		$result.= $linkstart . $this->ref . $linkend;
		return $result;
	}

	/**
	*  Retourne le libelle du status d'un user (actif, inactif)
	*
	*  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	*  @return	string 			       Label of status
	*/
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->status,$mode);
	}

	/**
	*  Return the status
	*
	*  @param	int		$status        	Id status
	*  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	*  @return string 			       	Label of status
	*/
	static function LibStatut($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 2) return $langs->trans('Approved');
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Draft');
			if ($status == -1) return $langs->trans('Disabled');
		}
		if ($mode == 1)
		{
			if ($status == 2) return $langs->trans('Approved');
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Draft');
			if ($status == -1) return $langs->trans('Disabled');
		}
		if ($mode == 2)
		{
			if ($status == 2) return img_picto($langs->trans('Approved'),'statut7').' '.$langs->trans('Approved');
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut0').' '.$langs->trans('Draft');
			if ($status == -1) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 3)
		{
			if ($status == 2) return img_picto($langs->trans('Approved'),'statut7');
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut0');
			if ($status == -1) return img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 2) return img_picto($langs->trans('Approved'),'statut7').' '.$langs->trans('Approved');
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
			if ($status == -1) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 2) return $langs->trans('Approved').' '.img_picto($langs->trans('Approved'),'statut7');
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut0');
			if ($status == -1) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == 2) return $langs->trans('Approved').' '.img_picto($langs->trans('Approved'),'statut7');
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut5');
			if ($status == -1) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
	}

}
