<?php
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobs.class.php';

class Mjobsext extends Mjobs
{
	//MODIFICACIONES

		/**
	 *  Create a document onto disk according to template model.
	 *
	 *  @param	    string		$modele			Force template to use ('' to not force)
	 *  @param		Translate	$outputlangs	Object lang to use for traduction
	 *  @param      int			$hidedetails    Hide details of lines
	 *  @param      int			$hidedesc       Hide description
	 *  @param      int			$hideref        Hide ref
	 *  @return     int          				0 if KO, 1 if OK
	 */
		public function generateDocument($modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
		{
			global $conf, $user, $langs;

			$langs->load("suppliers");

		// Sets the model on the model name to use
			if (! dol_strlen($modele))
			{
				if (! empty($conf->global->MANT_ADDON_PDF))
				{
					$modele = $conf->global->MANT_ADDON_PDF;
				}
				else
				{
					$modele = 'mant';
				}
			}

			$modelpath = "mant/core/modules/doc/";

			return $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref);
		}

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
	  //especifico para registro de ordenes de trabajo
		$namenum = 'mod_mant_numbertwo';

	  //if (! empty($conf->global->MANT_ADDON))
		if (! empty($namenum))
		{
		  //$file = $conf->global->MANT_ADDON.".php";
			$file = $namenum.".php";
		  // Chargement de la classe de numerotation
		  //$classname = $conf->global->MANT_ADDON;
			$classname = $namenum;

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
					dol_print_error($db,"Mjobs::getNextNumRef ".$obj->error);
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
			if ($status == 0)
				return ($type==0 ? $langs->trans('Pending'):$langs->trans('Pending'));
			if ($status == 1)
				return ($type==0 ? $langs->trans('Byassigning'):$langs->trans('Byassigning'));
			if ($status == 2)
				return ($type==0 ? $langs->trans('Assigned'):$langs->trans('Assigned'));
			if ($status == 3)
				return ($type==0 ? $langs->trans('Programmed'):$langs->trans('Programmed'));
			//if ($status == 4) return ($type==0 ? $langs->trans('Ejecution'):$langs->trans('Ejecution')).' '.img_picto(($type==0 ? $langs->trans('Ejecution jobs'):$langs->trans('Ejecution jobs order')),'statut5');

			if ($status == 4)
				return ($type==0 ? $langs->trans('Inexecution'):$langs->trans('Inexecution'));
			if ($status == 5)
				return ($type==0 ? $langs->trans('Terminated'):$langs->trans('Terminated'));
			if ($status == 8)
				return ($type==0 ? $langs->trans('Rejectedforotherreasons'):$langs->trans('Rejected for other reasons'));
			if ($status == 9)
				return ($type==0 ? $langs->trans('Refused'):$langs->trans('Refused'));
		}

		if ($mode == 1)
		{
			if ($status == 0)
				return ($type==0 ? $langs->trans('ProductStatusNotOnSell'):$langs->trans('ProductStatusNotOnBuy'));
			if ($status == 1)
				return ($type==0 ? $langs->trans('ProductStatusOnSell'):$langs->trans('ProductStatusOnBuy'));
		}
		if ($mode == 2)
		{
			if ($status == 0)
				return img_picto($langs->trans('ProductStatusNotOnSell'),'statut5').' '.($type==0 ? $langs->trans('ProductStatusNotOnSellShort'):$langs->trans('ProductStatusNotOnBuyShort'));
			if ($status == 1)
				return img_picto($langs->trans('ProductStatusOnSell'),'statut4').' '.($type==0 ? $langs->trans('ProductStatusOnSellShort'):$langs->trans('ProductStatusOnBuyShort'));
		}
		if ($mode == 3)
		{
			if ($status == 0)
				return img_picto(($type==0 ? $langs->trans('ProductStatusNotOnSell') : $langs->trans('ProductStatusNotOnBuy')),'statut5');
			if ($status == 1)
				return img_picto(($type==0 ? $langs->trans('ProductStatusOnSell') : $langs->trans('ProductStatusOnBuy')),'statut4');
		}
		if ($mode == 4)
		{
			if ($status == 0)
				return img_picto($langs->trans('ProductStatusNotOnSell'),'statut5').' '.($type==0 ? $langs->trans('ProductStatusNotOnSell'):$langs->trans('ProductStatusNotOnBuy'));
			if ($status == 1)
				return img_picto($langs->trans('ProductStatusOnSell'),'statut4').' '.($type==0 ? $langs->trans('ProductStatusOnSell'):$langs->trans('ProductStatusOnBuy'));
		}
		if ($mode == 5)
		{
			if ($status == 0)
				return ($type==0 ? $langs->trans('ProductStatusNotOnSellShort'):$langs->trans('ProductStatusNotOnBuyShort')).' '.img_picto(($type==0 ? $langs->trans('ProductStatusNotOnSell'):$langs->trans('ProductStatusNotOnBuy')),'statut5');
			if ($status == 1)
				return ($type==0 ? $langs->trans('ProductStatusOnSellShort'):$langs->trans('ProductStatusOnBuyShort')).' '.img_picto(($type==0 ? $langs->trans('ProductStatusOnSell'):$langs->trans('ProductStatusOnBuy')),'statut4');
		}
		if ($mode == 6)
		{
			if ($status == 0)
				return ($type==0 ? $langs->trans('Pending'):$langs->trans('Pending')).' '.img_picto(($type==0 ? $langs->trans('Pending aprob'):$langs->trans('Pending aprobation')),'statut0');
			if ($status == 1)
				return ($type==0 ? $langs->trans('Byassigning'):$langs->trans('Byassigning')).' '.img_picto(($type==0 ? $langs->trans('Byassigningjobs'):$langs->trans('Validated jobs order')),'statut1');
			if ($status == 2)
				return ($type==0 ? $langs->trans('Assigned'):$langs->trans('Assigned')).' '.img_picto(($type==0 ? $langs->trans('Assigned jobs'):$langs->trans('Assigned jobs order')),'statut3');
			if ($status == 3)
				return ($type==0 ? $langs->trans('Programmed'):$langs->trans('Programmed')).' '.img_picto(($type==0 ? $langs->trans('Programmed jobs'):$langs->trans('Programmed jobs order')),'statut4');
			//if ($status == 4) return ($type==0 ? $langs->trans('Ejecution'):$langs->trans('Ejecution')).' '.img_picto(($type==0 ? $langs->trans('Ejecution jobs'):$langs->trans('Ejecution jobs order')),'statut5');

			if ($status == 4)
				return ($type==0 ? $langs->trans('Inexecution'):$langs->trans('Inexecution')).' '.img_picto(($type==0 ? $langs->trans('Inexecutionworkorder'):$langs->trans('Inexecutionworkorder')),'statut6');
			if ($status == 5)
				return ($type==0 ? $langs->trans('Terminated'):$langs->trans('Terminated')).' '.img_picto(($type==0 ? $langs->trans('Work terminated'):$langs->trans('Order jobs terminated')),'statut7');

			if ($status == 8)
				return ($type==0 ? $langs->trans('Rejectedforotherreasons'):$langs->trans('Rejectedforotherreasons')).' '.img_picto(($type==0 ? $langs->trans('Work rejected'):$langs->trans('Rejectedforotherreasons')),'statut6');
			if ($status == 9)
				return ($type==0 ? $langs->trans('Refused'):$langs->trans('Refused')).' '.img_picto(($type==0 ? $langs->trans('Work refused'):$langs->trans('Order jobs refused')),'statut8');
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
		elseif ($imageview == 'fin')
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
		else
		{
			if (!empty($object->$imageview))
			{
				$dir=$conf->mant->multidir_output[$entity];
				$img = $object->$imageview;
				$info_fichero = pathinfo( $img );
				if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
				{
					$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
				}
				else
				{
					$file= $object->$imageview;
					$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
				}

				$file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
				if ($object->$imageview) $file=$id.'/images/thumbs/'.$file;
				$namephoto = 'photofin';
			}
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
		//else dol_print_error('','Call of showphoto with wrong parameters');

		return $ret;
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$fk_work_request    Id object work_request
	 *  @param date             $dateini default ''
	 *  @param date             $datefin default ''
	 *  @level int              $level default '' 0=Todos; 2=Validado; 3=Programado; 4=Concluido; 5=Todos los validados con statut >=2
	 *  @return int          	<0 if KO, >0 if OK

	 */
	function getlist($fk_work_request,$dateini='',$datefin='',$level='')
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.ref,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_work_request,";
		$sql.= " t.fk_soc,";
		$sql.= " t.fk_member,";
		$sql.= " t.fk_charge,";
		$sql.= " t.fk_departament,";
		$sql.= " t.fk_equipment,";
		$sql.= " t.fk_property,";
		$sql.= " t.fk_location,";
		$sql.= " t.fk_type_repair,";
		$sql.= " t.email,";
		$sql.= " t.internal,";
		$sql.= " t.speciality,";
		$sql.= " t.detail_problem,";
		$sql.= " t.address_ip,";
		$sql.= " t.fk_user_assign,";
		$sql.= " t.date_assign,";
		$sql.= " t.speciality_assign,";
		$sql.= " t.description_assign,";
		$sql.= " t.description_prog,";
		$sql.= " t.date_ini_prog,";
		$sql.= " t.date_fin_prog,";
		$sql.= " t.speciality_prog,";
		$sql.= " t.fk_equipment_prog,";
		$sql.= " t.fk_property_prog,";
		$sql.= " t.fk_location_prog,";
		$sql.= " t.typemant_prog,";
		$sql.= " t.fk_user_prog,";
		$sql.= " t.date_ini,";
		$sql.= " t.date_fin,";
		$sql.= " t.speciality_job,";
		$sql.= " t.typemant,";
		$sql.= " t.description_job,";
		$sql.= " t.group_task,";
		$sql.= " t.task,";
		$sql.= " t.image_ini,";
		$sql.= " t.image_fin,";
		$sql.= " t.tokenreg,";
		$sql.= " t.tms,";
		$sql.= " t.status,";
		$sql.= " t.description_confirm,";
		$sql.= " t.statut_job";

		$sql.= " FROM ".MAIN_DB_PREFIX."m_jobs as t";

		if ($fk_work_request)
			$sql.= " WHERE t.fk_work_request = ".$fk_work_request;
		elseif ($dateini && $datefin)
		{
			if (empty($level))
				$sql.= " WHERE t.date_create BETWEEN '".$this->db->idate($dateini)."' AND '".$this->db->idate($datefin)."'";
			if ($level == 2)
			{
				//$sql.= " WHERE t.date_ini >= '".$this->db->idate($dateini)."'";
				//$sql.= " AND  t.date_fin <= '".$this->db->idate($datefin)."'";
				$sql.= " WHERE t.date_create BETWEEN '".$this->db->idate($dateini)."' AND '".$this->db->idate($datefin)."'";
				$sql.= " AND t.status IN(1,2) ";
			}
			if ($level == 3)
	  		//programados
			{
		  //	      $sql.= " WHERE t.date_ini_prog >= '".$dateini."' AND t.date_fin_prog <= '".$datefin."'";
		  //revisar la fecha de inicio
				$sql.= " WHERE t.date_ini >= '".$this->db->idate($dateini)."'";
				$sql.= " AND  t.date_fin <= '".$this->db->idate($datefin)."'";
				//$sql.= " AND t.date_fin_prog BETWEEN  '".$this->db->idate($dateini)."' AND  '".$this->db->idate($datefin)."'";
				$sql.= " AND t.status IN (3,4) ";
			}
			if ($level == 4)
	  //concluidos
			{
		  //	      $sql.= " WHERE t.date_ini >= '".$dateini."' AND t.date_fin <= '".$datefin."'";
		  //primera opcion
		  // $sql.= " WHERE t.date_ini BETWEEN  '".$dateini."' AND  '".$datefin."'";
		  // $sql.= " AND t.date_fin BETWEEN  '".$dateini."' AND  '".$datefin."'";
		  //segunda opcion
				$sql.= " WHERE t.date_fin BETWEEN  '".$this->db->idate($dateini)."' AND  '".$this->db->idate($datefin)."'";

				$sql.= " AND t.status = 5";
			}

			if ($level == 5)
			{
				$sql.= " WHERE t.date_create BETWEEN '".$this->db->idate($dateini)."' AND '".$this->db->idate($datefin)."'";
				$sql.= " AND t.status >= 2";
			}
		}
		else
			return -1;

		dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);

		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				$i = 0;
				while ( $i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Mjobs($this->db);

					$objnew->id    = $obj->rowid;
					$objnew->entity = $obj->entity;
					$objnew->ref = $obj->ref;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->fk_work_request = $obj->fk_work_request;
					$objnew->fk_soc = $obj->fk_soc;
					$objnew->fk_member = $obj->fk_member;
					$objnew->fk_charge = $obj->fk_charge;
					$objnew->fk_departament = $obj->fk_departament;
					$objnew->fk_equipment = $obj->fk_equipment;
					$objnew->fk_property = $obj->fk_property;
					$objnew->fk_location = $obj->fk_location;
					$objnew->fk_type_repair = $obj->fk_type_repair;
					$objnew->email = $obj->email;
					$objnew->internal = $obj->internal;
					$objnew->speciality = $obj->speciality;
					$objnew->detail_problem = $obj->detail_problem;
					$objnew->address_ip = $obj->address_ip;
					$objnew->fk_user_assign = $obj->fk_user_assign;
					$objnew->date_assign = $this->db->jdate($obj->date_assign);
					$objnew->speciality_assign = $obj->speciality_assign;
					$objnew->description_assign = $obj->description_assign;
					$objnew->description_prog = $obj->description_prog;
					$objnew->date_ini_prog = $this->db->jdate($obj->date_ini_prog);
					$objnew->date_fin_prog = $this->db->jdate($obj->date_fin_prog);
					$objnew->speciality_prog = $obj->speciality_prog;
					$objnew->fk_equipment_prog = $obj->fk_equipment_prog;
					$objnew->fk_property_prog = $obj->fk_property_prog;
					$objnew->fk_location_prog = $obj->fk_location_prog;
					$objnew->typemant_prog = $obj->typemant_prog;
					$objnew->fk_user_prog = $obj->fk_user_prog;
					$objnew->date_ini = $this->db->jdate($obj->date_ini);
					$objnew->date_fin = $this->db->jdate($obj->date_fin);
					$objnew->speciality_job = $obj->speciality_job;
					$objnew->typemant = $obj->typemant;
					$objnew->description_job = $obj->description_job;
					$objnew->group_task = $obj->group_task;
					$objnew->task = $obj->task;
					$objnew->image_ini = $obj->image_ini;
					$objnew->image_fin = $obj->image_fin;
					$objnew->tokenreg = $obj->tokenreg;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->status = $obj->status;
					$objnew->description_confirm = $obj->description_confirm;
					$objnew->statut_job = $obj->statut_job;

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

}
?>