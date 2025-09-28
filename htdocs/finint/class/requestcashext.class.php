<?php
REQUIRE_ONCE DOL_DOCUMENT_ROOT.'/finint/class/requestcash.class.php';

class Requestcashext extends Requestcash
{
	public $statuts_long;
	public $statuts_short;

		/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function update_user_assigned(User $user, $notrigger = false)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters

		if (isset($this->fk_user_assigned)) {
			$this->fk_user_assigned = trim($this->fk_user_assigned);
		}


		// Check parameters
		// Put here code to add a control on parameters values

		// Update finint
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		$sql .= ' fk_user_assigned = '.(isset($this->fk_user_assigned)?$this->fk_user_assigned:"null").'';

		$sql .= ' WHERE rowid=' . $this->id;

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		if (!$error && !$notrigger) {
			// Uncomment this and change MYOBJECT to your own tag if you
			// want this action calls a trigger.

			//// Call triggers
			//$result=$this->call_trigger('MYOBJECT_MODIFY',$user);
			//if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
			//// End call triggers
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}

		//MODIFICADO
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
		$langs->load("finint@finint");

		$dir = DOL_DOCUMENT_ROOT . "/finint/core/modules";
		//$conf->global->FININT_CASH_ADDON = 'mod_finint_cash_ubuntubo';
		if (! empty($conf->global->FININT_ADDON))
		{
			$file = $conf->global->FININT_ADDON.'.php';
		  // Chargement de la classe de numerotation
			$classname = 'mod_finint_cash_ubuntubo';
			$classname = $conf->global->FININT_ADDON;
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
					dol_print_error($db,"Requestcash::getNextNumRef ".$obj->error);
					return "";
				}
			}
			else
			{
				print $langs->trans("Error")." ".$langs->trans("Error_FININT_CASH_ADDON_NotDefined");
				return "";
			}
		}
		else
		{
			print $langs->trans("Error")." ".$langs->trans("Error_FININT_CASH_ADDON_NotDefined");
			return "";
		}
	}

	/**
	 * 	Return clicable name (with picto eventually)
	 *
	 * 	@param	int		$withpicto		0=No picto, 1=Include picto into link, 2=Only picto
	 * 	@param	string	$option			Variant ('', 'nolink')
	 * 	@param	int		$addlabel		0=Default, 1=Add label into string, >1=Add first chars into string
	 *  @param	string	$moreinpopup	Text to add into popup
	 *  @param	string	$sep			Separator between ref and label if option addlabel is set
	 * 	@return	string					Chaine avec URL
	*/
	function getNomUrlxxx($withpicto=0, $option='', $addlabel=0, $moreinpopup='', $sep=' - ')
	{
		global $langs;

		$result = '';
		$link = '';
		$linkend = '';
		$label='';
		//echo $this->ref.' '.$this->id;
		if ($option != 'nolink') $label = '<u>' . $langs->trans("Showfinintcash") . '</u>';
		if (! empty($this->ref))
			$label .= ($label?'<br>':'').'<b>' . $langs->trans('Ref') . ': </b>' . $this->ref;	// The space must be after the : to not being explode when showing the title in img_picto
		if (! empty($this->detail))
			$label .= ($label?'<br>':'').'<b>' . $langs->trans('Label') . ': </b>' . $this->detail;	// The space must be after the : to not being explode when showing the title in img_picto
		if ($moreinpopup) $label.='<br>'.$moreinpopup;
		$linkclose = '" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';

		if ($option != 'nolink')
		{
			if (preg_match('/\.php$/',$option)) {
				$link = '<a href="' . dol_buildpath($option,1) . '?id=' . $this->id . $linkclose;
				$linkend = '</a>';
			} else {
				$link = '<a href="' . DOL_URL_ROOT . '/finint/request/card.php?id=' . $this->id . $linkclose;
				$linkend = '</a>';
			}
		}

		$picto = 'req';
		if ($withpicto) $result.=($link . img_picto($label, DOL_URL_ROOT.'/finint/img/'.$picto, 'class="classfortooltip"',1) . $linkend);
		if ($withpicto && $withpicto != 2) $result.=' ';
		if ($withpicto != 2) $result.=$link . $this->ref . $linkend . (($addlabel && $this->title) ? $sep . dol_trunc($this->title, ($addlabel > 1 ? $addlabel : 0)) : '');
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
	 *  Renvoi le libelle d'un status donne
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return string 			       	Label of status
	 */
	function LibStatut($status,$mode=0)
	{
		global $langs;

		$statuts_short = array(-1=> $langs->trans('Torefused'),0 => $langs->trans('Draft'), 1 => $langs->trans('Validated'), 2 => $langs->trans('Approved'),3=>$langs->trans('Disbursed'),4=>$langs->trans('Approverecharge'),5=>$langs->trans('Closed'));
		$statuts_long = array(-1=> $langs->trans('Torefused'),0 => $langs->trans('Draft'), 1 => $langs->trans('Validated'), 2 => $langs->trans('Approved'),3=>$langs->trans('Disbursed'),4=>$langs->trans('Approveclosure'),5=>$langs->trans('Closed'));
		if ($mode == 0)
		{
			$prefix='';
			return $statuts_long[$status];
		}
		if ($mode == 1)
		{
			return $statuts_long[$status];
		}
		if ($mode == 2)
		{
			return $statuts_long[$status];
		}
		if ($mode == 3)
		{
			return $statuts_short[$status];
		}
		if ($mode == 4)
		{
			return $statuts_long[$status];
		}
		if ($mode == 5)
		{
			return $statuts_long[$status];
		}
	}
	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLib_Statutxx($mode=0)
	{
		return $this->Lib_Statut($this->status,$mode);
	}

	/**
	 *  Renvoi le libelle d'un status donne
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return string 			       	Label of status
	 */
	function Lib_Statutxx($status,$mode=0)
	{
		global $langs;
		$this->statuts_short = array(-1=> 'Torefused',0 => 'Draft', 1 => 'Validated', 2 => 'Approved',3=>'Closed');
  		$this->statuts_long = array(-1=> 'Torefused', 0 => 'Draft', 1 => 'Validated', 2 => 'Approved',3=>'Closed');

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 6)
		{
	  		return $langs->trans($this->statuts_short[$status]);
		}
		if ($mode == 7)
		{
	  		return $langs->trans($this->statuts_long[$status]);
		}
	}

	/**
	 *    	Return HTML code to output a photo
	 *
	 *    	@param	string		$modulepart		Key to define module concerned ('societe', 'userphoto', 'memberphoto')
	 *     	@param  Object		$object			Object containing data to retrieve file name
	 * 		@param	int			$width			Width of photo
	 * 	  	@return string    					HTML code to output photo
	 */
	function showphoto($imageview,$object,$document,$width=100,$docext='')
	{
		global $conf;
		$modulepart = 'finint';
		$entity = (! empty($object->entity) ? $object->entity : $conf->entity);
		$id = (! empty($object->id) ? $object->id : $object->rowid);
		$ret='';$dir='';$file='';$altfile='';$email='';
		if ($imageview == 'ini')
		{
			$dir=$conf->finint->multidir_output[$entity];
			$dir.= '/'.$object->id.'/cash/';
			$dirfile = $object->id.'/cash/';
			$info_fichero = pathinfo($document);
			if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
				$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
			else
				$file= $document;
			$file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
			if ($id) $file=$id.'/images/thumbs/'.$file;
			$namephoto = 'photoini';
		}
		if ($imageview == 'doc')
		{
			$dir=$conf->finint->multidir_output[$entity];
			$dir.= '/'.$object->id.'/cash/';
			$dirfile = $object->id.'/cash/';
			$info_fichero = pathinfo($document);
			if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
				$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
			else
				$file= $document;
		  //	      $file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
		  //if ($id) $file=$id.'/'.$file;
			$namephoto = ($docext?$docext:$imageview);
		}
		if ($imageview == 'fin')
		{
			$dir=$conf->finint->multidir_output[$entity];
			$dir.= '/'.$object->id.'/cash/';
			$dirfile = $object->id.'/cash/';
			$info_fichero = pathinfo($document);
			if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
				$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
			else
				$file= $document;
			$file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
			if ($id) $file='/thumbs/'.$file;
			$namephoto = 'photofin';
		}
		if ($dir)
		{
			$cache='0';
			if ($file && file_exists($dir.$file))
			{
				$dirfile.= $file;
				// TODO Link to large image
				$ret.='<a href="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($dirfile).'&cache='.$cache.'">';
				$ret.='<img alt="'.$namephoto.'" id="photologo'.(preg_replace('/[^a-z]/i','_',$dirfile)).'" class="photologo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($dirfile).'&cache='.$cache.'">';
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

	function fetch_lines()
	{
		global $conf,$langs;
		require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashdet.class.php';
		$objdet = new Requestcashdet($this->db);
		$filter = array(1=>1);
		$filterstatic = " AND t.fk_request = ".$this->id;
		$res = $objdet->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
		if ($res > 0)
		{
			$this->linesdet = $objdet->lines;
		}
		return $res;
	}


	function fetch_lines_io()
	{
		global $conf,$langs;
		require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashdeplacement.class.php';
		$objdet = new Requestcashdeplacement($this->db);
		$filter = array(1=>1);
		$filterstatic = " AND t.fk_finint_cash = ".$this->id;
		$filterstatic.= " AND t.statut = 1";
		$res = $objdet->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
		$this->linesio = array();
		if ($res > 0)
		{
			for ($i = 0; $i < count($objdet->lines); $i++)
			{
				$line = $objdet->lines[$i];
				$line->typeop = 0;
				//armamos en un array para ordenar
				$aArray[($line->dateo>0?$line->dateo:$line->date_create)][$line->id] = $line;
			}
		}
		$filter = array(1=>1);
		$filterstatic = " AND t.fk_finint_cash_dest = ".$this->id;
		$res = $objdet->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
		$this->linesinp = array();
		if ($res > 0)
		{
			for ($i = 0; $i < count($objdet->lines); $i++)
			{
				$line = $objdet->lines[$i];
				$line->typeop = 1;
				//armamos en un array para ordenar
				$aArray[($line->dateo>0?$line->dateo:$line->date_create)][$line->id] = $line;
			}
		}
		$aResult = array();
		if (count($aArray)>0)
		{
			kSort($aArray);
			foreach ($aArray AS $d => $data)
			{
				foreach ($data AS $e => $row)
				{
					$aResult[] = $row;
				}
			}
		}
		$this->linesio = $aResult;
		return $res;
	}

}
?>
