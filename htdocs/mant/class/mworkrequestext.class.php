<?php
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequest.class.php';

class Mworkrequestext extends Mworkrequest
{
		//MODIFICACIONES
	/**
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into ALMACEN_ADDON
	 *
	 *  @param	Societe		$soc  	Object thirdparty
	 *  @return string      		Order free reference
	 */
	function getNextNumRef($soc)
	{
		global $db, $langs, $conf;
		$langs->load("mant@mant");

		$dir = DOL_DOCUMENT_ROOT . "/mant/core/modules";

		if (! empty($conf->global->MANT_ADDON))
		{
			$file = $conf->global->MANT_ADDON.".php";
		  // Chargement de la classe de numerotation
			$classname = $conf->global->MANT_ADDON;
			$result=include_once $dir.'/'.$file;
			if ($result)
			{
				$obj = new $classname();
				$numref = "";
				$numref = $obj->getNextValue($soc,$this);

				if ( $numref != "")
				{
					return $numref;
				}
				else
				{
					dol_print_error($db,"Mworkrequest::getNextNumRef ".$obj->error);
					return "";
				}
			}
			else
			{
				print $langs->trans("Error")." ".$langs->trans("Error_MANT_ADDON_NotDefined");
				return "";
			}
		}
		else
		{
			print $langs->trans("Error")." ".$langs->trans("Error_MANT_ADDON_NotDefined");
			return "";
		}
	}

	/**
	 *	Return label of status of object
	 *
	 *	@param      int	$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 *	@param      int	$type       0=Shell, 1=Buy
	 *	@return     string      	Label of status
	 */
	function getLibStatut($mode=0, $type=0)
	{
		if($type==0)
			return $this->LibStatut($this->status,$mode,$type);
		else
			return $this->LibStatut($this->statut_buy,$mode,$type);
	}

	/**
	 *	Return label of a given status
	 *
	 *	@param      int		$status     Statut
	 *	@param      int		$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 *	@param      int		$type       0=Status "to sell", 1=Status "to buy"
	 *	@return     string      		Label of status
	 */
	function LibStatut($status,$mode=0,$type=0)
	{
		global $langs;
		$langs->load('mant@mant');

		if ($mode == 0)
		{
			if ($status == 0) return ($type==0 ? $langs->trans('ProductStatusNotOnSellShort'):$langs->trans('ProductStatusNotOnBuyShort'));
			if ($status == 1) return ($type==0 ? $langs->trans('ProductStatusOnSellShort'):$langs->trans('ProductStatusOnBuyShort'));
		}
		if ($mode == 1)
		{
			if ($status == 0) return ($type==0 ? $langs->trans('ProductStatusNotOnSell'):$langs->trans('ProductStatusNotOnBuy'));
			if ($status == 1) return ($type==0 ? $langs->trans('ProductStatusOnSell'):$langs->trans('ProductStatusOnBuy'));
		}
		if ($mode == 2)
		{
			if ($status == 0) return img_picto($langs->trans('ProductStatusNotOnSell'),'statut5').' '.($type==0 ? $langs->trans('ProductStatusNotOnSellShort'):$langs->trans('ProductStatusNotOnBuyShort'));
			if ($status == 1) return img_picto($langs->trans('ProductStatusOnSell'),'statut4').' '.($type==0 ? $langs->trans('ProductStatusOnSellShort'):$langs->trans('ProductStatusOnBuyShort'));
		}
		if ($mode == 3)
		{
			if ($status == 0) return img_picto(($type==0 ? $langs->trans('ProductStatusNotOnSell') : $langs->trans('ProductStatusNotOnBuy')),'statut5');
			if ($status == 1) return img_picto(($type==0 ? $langs->trans('ProductStatusOnSell') : $langs->trans('ProductStatusOnBuy')),'statut4');
		}
		if ($mode == 4)
		{
			if ($status == 0) return img_picto($langs->trans('ProductStatusNotOnSell'),'statut5').' '.($type==0 ? $langs->trans('ProductStatusNotOnSell'):$langs->trans('ProductStatusNotOnBuy'));
			if ($status == 1) return img_picto($langs->trans('ProductStatusOnSell'),'statut4').' '.($type==0 ? $langs->trans('ProductStatusOnSell'):$langs->trans('ProductStatusOnBuy'));
		}
		if ($mode == 5)
		{
			if ($status == 0) return ($type==0 ? $langs->trans('ProductStatusNotOnSellShort'):$langs->trans('ProductStatusNotOnBuyShort')).' '.img_picto(($type==0 ? $langs->trans('ProductStatusNotOnSell'):$langs->trans('ProductStatusNotOnBuy')),'statut5');
			if ($status == 1) return ($type==0 ? $langs->trans('ProductStatusOnSellShort'):$langs->trans('ProductStatusOnBuyShort')).' '.img_picto(($type==0 ? $langs->trans('ProductStatusOnSell'):$langs->trans('ProductStatusOnBuy')),'statut4');
		}
		if ($mode == 6)
		{
			if ($status == 0) return ($type==0 ? $langs->trans('Pending'):$langs->trans('Pending')).' '.img_picto(($type==0 ? $langs->trans('Pending aprob'):$langs->trans('Pending aprobation')),'statut0');
			if ($status == 1) return ($type==0 ? $langs->trans('Validated'):$langs->trans('Validated')).' '.img_picto(($type==0 ? $langs->trans('Validated jobs'):$langs->trans('Validated jobs order')),'statut1');
			if ($status == 2) return ($type==0 ? $langs->trans('Assigned'):$langs->trans('Assigned')).' '.img_picto(($type==0 ? $langs->trans('Assigned jobs'):$langs->trans('Assigned jobs order')),'statut3');
			if ($status == 3) return ($type==0 ? $langs->trans('Assignedtechnic'):$langs->trans('Assignedtechnic')).' '.img_picto(($type==0 ? $langs->trans('Assignedtechnic ticket'):$langs->trans('Assignedtechnic ticket')),'statut4');

			//if ($status == 4) return ($type==0 ? $langs->trans('Programmed'):$langs->trans('Programmed')).' '.img_picto(($type==0 ? $langs->trans('Programmed jobs'):$langs->trans('Programmed jobs order')),'statut4');
			if ($status == 4) return ($type==0 ? $langs->trans('Validated'):$langs->trans('Validated')).' '.img_picto(($type==0 ? $langs->trans('Validated ticket'):$langs->trans('Validated ticket')),'statut4');

			if ($status == 5) return ($type==0 ? $langs->trans('Inexecution'):$langs->trans('Inexecution')).' '.img_picto(($type==0 ? $langs->trans('Execution approved'):$langs->trans('Execution approved')),'statut5');
			if ($status == 6) return ($type==0 ? $langs->trans('Terminated'):$langs->trans('Terminated')).' '.img_picto(($type==0 ? $langs->trans('Work terminated'):$langs->trans('Order jobs terminated')),'statut6');
			if ($status == 8) return ($type==0 ? $langs->trans('Rejected for other reasons'):$langs->trans('Rejected for other reasons')).' '.img_picto(($type==0 ? $langs->trans('Work rejected'):$langs->trans('Order jobs rejected')),'statut6');
			if ($status == 9) return ($type==0 ? $langs->trans('Refused'):$langs->trans('Refused')).' '.img_picto(($type==0 ? $langs->trans('Work refused'):$langs->trans('Order jobs refused')),'statut8');
		}

		return $langs->trans('Unknown');
	}

	/**
	 *    	Return HTML code to output a photo
	 *
	 *    	@param	string		$modulepart		Key to define module concerned ('societe', 'userphoto', 'memberphoto')
	 *     	@param  Object		$object			Object containing data to retrieve file name
	 * 		@param	int			$width			Width of photo
	 * 	  	@return string    					HTML code to output photo
	 */
	function showphoto($imageview,$object,$width=100)
	{
		global $conf;
		$modulepart = 'mant';
		$entity = (! empty($object->entity) ? $object->entity : $conf->entity);
		$id = (! empty($object->id) ? $object->id : $object->rowid);

		$ret='';$dir='';$file='';$altfile='';$email='';

		if ($imageview == 'ini')
		{
			$dir=$conf->mant->multidir_output[$entity];
			$info_fichero = pathinfo($object->image_ini);
			if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
				$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
			else
				$file= $object->image_ini;
			$file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
			if ($object->image_ini) $file=$id.'/images/thumbs/'.$file;
			$namephoto = 'photoini';
		}
		if ($imageview == 'fin')
		{
			$dir=$conf->mant->multidir_output[$entity];
			$info_fichero = pathinfo($object->image_fin);
			if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
				$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
			else
				$file= $object->image_fin;
			$file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
			if ($object->image_fin) $file=$id.'/images/thumbs/'.$file;
			$namephoto = 'photofin';
		}
		if ($dir)
		{
			$cache='0';
			if ($file && file_exists($dir."/".$file))
			{
				// TODO Link to large image
				$ret.='<a href="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($file).'&cache='.$cache.'">';
				$ret.='<img alt="'.$namephoto.'" id="photologo'.(preg_replace('/[^a-z]/i','_',$file)).'" class="photologo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($file).'&cache='.$cache.'">';
				$ret.='</a>';
			}
			else if ($altfile && file_exists($dir."/".$altfile))
			{
				$ret.='<a href="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($file).'&cache='.$cache.'">';
				$ret.='<img alt="Photo alt" id="photologo'.(preg_replace('/[^a-z]/i','_',$file)).'" class="photologo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($altfile).'&cache='.$cache.'">';
				$ret.='</a>';
			}
			else
			{
				if (! empty($conf->gravatar->enabled) && $email)
				{
					global $dolibarr_main_url_root;
					$ret.='<!-- Put link to gravatar -->';
					$ret.='<img alt="Photo found on Gravatar" title="Photo Gravatar.com - email '.$email.'" border="0" width="'.$width.'" src="http://www.gravatar.com/avatar/'.dol_hash($email).'?s='.$width.'&d='.urlencode(dol_buildpath('/theme/common/nophoto.jpg',2)).'">';
				}
				else
				{
					$ret.='<img alt="No photo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/theme/common/nophoto.jpg">';
				}
			}
		}
		else dol_print_error('','Call of showphoto with wrong parameters');

		return $ret;
	}
}
?>