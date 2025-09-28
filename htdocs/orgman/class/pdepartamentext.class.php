<?php
require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartament.class.php';

class Pdepartamentext extends Pdepartament
{

	var $arrayson;
	public $array;
	public $cats = array();			// Categories table in memory
	public $motherof = array();
	var $aDepartamentsup;
	var $aDepartamentson;
	var $aResp;

	/**
	 *  Return a link to the user card (with optionaly the picto)
	 * 	Use this->id,this->lastname, this->firstname
	 *
	 *	@param	int		$withpicto			Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option				On what the link point to
	 *  @param	integer	$notooltip			1=Disable tooltip
	 *  @param	int		$maxlen				Max length of visible user name
	 *  @param  string  $morecss            Add more css on link
	 *  @param integer $type 				0= ref and label; 1=ref
	 *	@return	string						String with URL
	 */
	function getNomUrladd($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='',$type=0)
	{
		global $langs, $conf, $db;
		global $dolibarr_main_authentication, $dolibarr_main_demo;
		global $menumanager;


		$result = '';
		$companylink = '';

		$label = '<u>' . $langs->trans("Departament") . '</u>';
		$label.= '<div width="100%">';
		$label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;
		$label.= '<br><b>' . $langs->trans('Label') . ':</b> ' . $this->label;
		$link = '<a href="'.DOL_URL_ROOT.'/orgman/departament/card.php?id='.$this->id.'"';
		$link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
		$link.= ' target="blank_">';
		$linkend='</a>';

		if ($withpicto)
		{
			$result.=($link.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
			if ($withpicto != 2) $result.=' ';
		}
		if ($type == 1)
		{
			$result.= $link . $this->ref. $linkend;
		}
		else
			$result.= $link . $this->ref. $linkend;
		return $result;
	}
	/**
	 *  Return list of orders (eventuelly filtered on a user) into an array
	 *
	 *  @param      int		$brouillon      0=non brouillon, 1=brouillon
	 *  @param      User	$user           Objet user de filtre
	 *  @return     int             		-1 if KO, array with result if OK
	 */
	function liste_array($empty="")
	{
		global $conf,$langs;

		$ga = array();
		if ($empty == 1)
			$ga[0] = $langs->trans("Select");
		$sql = "SELECT p.rowid, p.ref ";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_departament AS p ";
		$sql.= " WHERE p.entity =".$conf->entity;
		$sql.= " ORDER BY p.ref ";

		$result=$this->db->query($sql);
		if ($result)
		{
			$numc = $this->db->num_rows($result);
			if ($numc)
			{
				$i = 0;
				while ($i < $numc)
				{
					$obj = $this->db->fetch_object($result);
					$ga[$obj->rowid] = $obj->ref.' ';
					$i++;
				}
			}
			return $ga;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	/**
	 *  Return list departament son (eventuelly filtered on a user) into an array
	 *
	 *  @param      int		$brouillon      0=non brouillon, 1=brouillon
	 *  @param      User	$user           Objet user de filtre
	 *  @return     int             		-1 if KO, array with result if OK
	 */
	function liste_son($id)
	{
		global $conf,$langs;

		$sql = "SELECT p.rowid ";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_departament AS p ";
		$sql.= " WHERE p.entity =".$conf->entity;
		$sql.= " AND p.fk_father = ".$id;

		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($result);
					$this->arrayson[$obj->rowid] = $obj->rowid;
					//buscamos si tiene hijos
					$res = $this->liste_son($obj->rowid);
					if ($res>0)
					{
						foreach ($this->arrayson AS $j)
						{
							$this->arrayson[$j] = $j;
						}
					}
					$i++;
				}
			}
			return $num;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	 /**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	 function getlist_son($fk_father,array $array=array())
	 {
	 	global $langs,$conf;

	 	$sql = "SELECT";
	 	$sql.= " t.rowid,";

	 	$sql.= " t.entity,";
	 	$sql.= " t.fk_father,";
	 	$sql.= " t.ref,";
	 	$sql.= " t.label,";
	 	$sql.= " t.active";

	 	$sql.= " FROM ".MAIN_DB_PREFIX."p_departament as t";
	 	$sql.= " WHERE t.fk_father in (".$fk_father.")";
	 	dol_syslog(get_class($this)."::getlist_son sql=".$sql, LOG_DEBUG);
	 	$resql=$this->db->query($sql);
	 	if ($resql)
	 	{
	 		$num = $this->db->num_rows($resql);
	 		if ($this->db->num_rows($resql))
	 		{
	 			$i = 0;
	 			while ($i < $num)
	 			{
	 				$obj = $this->db->fetch_object($resql);
	 				$objnew = new Pdepartament($this->db);

	 				$objnew->id    		= $obj->rowid;
	 				$objnew->entity 	= $obj->entity;
	 				$objnew->fk_father 	= $obj->fk_father;
	 				$objnew->ref 		= $obj->ref;
	 				$objnew->label 	= $obj->label;
	 				$objnew->active 	= $obj->active;
	 				$array[$obj->rowid] = $objnew;
	 				//buscamos si tiene hijos
	 				$array = $this->getlist_son($objnew->id,$array);
	 				$i++;
	 			}
	 		}
	 		$this->array = $array;
	 		$this->db->free($resql);
	 		return $array;
	 	}
	 	else
	 	{
	 		$this->error="Error ".$this->db->lasterror();
	 		dol_syslog(get_class($this)."::getlist_son ".$this->error, LOG_ERR);
	 		return -1;
	 	}
	 }


	 /**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	 function getlist_sondirect($fk_father,array $array=array())
	 {
	 	global $langs,$conf;

	 	$sql = "SELECT";
	 	$sql.= " t.rowid,";

	 	$sql.= " t.entity,";
	 	$sql.= " t.fk_father,";
	 	$sql.= " t.ref,";
	 	$sql.= " t.label,";
	 	$sql.= " t.active";

	 	$sql.= " FROM ".MAIN_DB_PREFIX."p_departament as t";
	 	$sql.= " WHERE t.fk_father in (".$fk_father.")";
	 	dol_syslog(get_class($this)."::getlist_son sql=".$sql, LOG_DEBUG);
	 	$resql=$this->db->query($sql);
	 	if ($resql)
	 	{
	 		$num = $this->db->num_rows($resql);
	 		if ($this->db->num_rows($resql))
	 		{
	 			$i = 0;
	 			while ($i < $num)
	 			{
	 				$obj = $this->db->fetch_object($resql);
	 				$objnew = new Pdepartament($this->db);

	 				$objnew->id    		= $obj->rowid;
	 				$objnew->entity 	= $obj->entity;
	 				$objnew->fk_father 	= $obj->fk_father;
	 				$objnew->ref 		= $obj->ref;
	 				$objnew->label 	= $obj->label;
	 				$objnew->active 	= $obj->active;
	 				$array[$obj->rowid] = $objnew;
	 				//buscamos si tiene hijos
	 				//$array = $this->getlist_son($objnew->id,$array);
	 				$i++;
	 			}
	 		}
	 		$this->array = $array;
	 		$this->db->free($resql);
	 		return $array;
	 	}
	 	else
	 	{
	 		$this->error="Error ".$this->db->lasterror();
	 		dol_syslog(get_class($this)."::getlist_son ".$this->error, LOG_ERR);
	 		return -1;
	 	}
	 }
	/**
	 * Rebuilding the category tree as an array
	 * Return an array of table('id','id_mere',...) trie selon arbre et avec:
	 *                id = id de la categorie
	 *                id_mere = id de la categorie mere
	 *                id_children = tableau des id enfant
	 *                label = nom de la categorie
	 *                fulllabel = nom avec chemin complet de la categorie
	 *                fullpath = chemin complet compose des id
	 *
	 * @param   string $type        Type of categories ('customer', 'supplier', 'contact', 'product', 'member').
	 *                              Old mode (0, 1, 2, ...) is deprecated.
	 * @param   int    $markafterid Removed all categories including the leaf $markafterid in category tree.
	 *
	 * @return  array               Array of categories. this->cats and this->motherof are set.
	 */
	function get_full_arbo($markafterid=0, $filter='')
	{
		global $conf, $langs;

		// For backward compatibility

		$this->cats = array();

		// Init this->motherof that is array(id_son=>id_parent, ...)
		$this->load_motherof();
		$current_lang = $langs->getDefaultLang();

		// Init $this->cats array
		$sql = "SELECT DISTINCT c.rowid, c.label AS label, c.label AS description, c.fk_father as fk_parent";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_departament as c";
		$sql .= " WHERE c.entity IN (" . getEntity( 'p_departament', 1 ) . ")";
		$sql.= " AND c.active = 1";
		if ($filter) $sql.= $filter;
		//$sql .= " AND c.type = " . $this->MAP_ID[$type];

		dol_syslog(get_class($this)."::get_full_arbo get departament list", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$i=0;
			while ($obj = $this->db->fetch_object($resql))
			{
				$this->cats[$obj->rowid]['rowid'] = $obj->rowid;
				$this->cats[$obj->rowid]['id'] = $obj->rowid;
				$this->cats[$obj->rowid]['fk_parent'] = $obj->fk_parent;
				$this->cats[$obj->rowid]['label'] = $obj->label;
				$this->cats[$obj->rowid]['description'] = $obj->description;
				$this->cats[$obj->rowid]['color'] = $obj->color;
				$i++;
			}
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}

		// We add the fullpath property to each elements of first level (no parent exists)
		dol_syslog(get_class($this)."::get_full_arbo call to build_path_from_id_categ", LOG_DEBUG);
		foreach($this->cats as $key => $val)
		{
			//print 'key='.$key.'<br>'."\n";
			$this->build_path_from_id_dep($key,0);	// Process a branch from the root category key (this category has no parent)
		}

		// Exclude leaf including $markafterid from tree
		if ($markafterid)
		{
			//print "Look to discard category ".$markafterid."\n";
			$keyfilter1='^'.$markafterid.'$';
			$keyfilter2='_'.$markafterid.'$';
			$keyfilter3='^'.$markafterid.'_';
			$keyfilter4='_'.$markafterid.'_';
			foreach($this->cats as $key => $val)
			{
				if (preg_match('/'.$keyfilter1.'/',$val['fullpath']) || preg_match('/'.$keyfilter2.'/',$val['fullpath'])
					|| preg_match('/'.$keyfilter3.'/',$val['fullpath']) || preg_match('/'.$keyfilter4.'/',$val['fullpath']))
				{
					unset($this->cats[$key]);
				}
			}
		}

		dol_syslog(get_class($this)."::get_full_arbo dol_sort_array", LOG_DEBUG);
		$this->cats=dol_sort_array($this->cats, 'fulllabel', 'asc', true, false);

		//$this->debug_cats();

		return $this->cats;
	}

	/**
	 *	For category id_categ and its childs available in this->cats, define property fullpath and fulllabel
	 *
	 * 	@param		int		$id_categ		id_categ entry to update
	 * 	@param		int		$protection		Deep counter to avoid infinite loop
	 *	@return		void
	 */
	function build_path_from_id_dep($id_categ,$protection=1000)
	{
		dol_syslog(get_class($this)."::build_path_from_id_dep id_categ=".$id_categ." protection=".$protection, LOG_DEBUG);

		if (! empty($this->cats[$id_categ]['fullpath']))
		{
			// Already defined
			dol_syslog(get_class($this)."::build_path_from_id_dep fullpath and fulllabel already defined", LOG_WARNING);
			return;
		}

		// First build full array $motherof
		//$this->load_motherof();	// Disabled because already done by caller of build_path_from_id_dep

		// Define fullpath and fulllabel
		$this->cats[$id_categ]['fullpath'] = '_'.$id_categ;
		$this->cats[$id_categ]['fulllabel'] = $this->cats[$id_categ]['label'];
		$i=0; $cursor_categ=$id_categ;
		//print 'Work for id_categ='.$id_categ.'<br>'."\n";
		while ((empty($protection) || $i < $protection) && ! empty($this->motherof[$cursor_categ]))
		{
			//print '&nbsp; cursor_categ='.$cursor_categ.' i='.$i.' '.$this->motherof[$cursor_categ].'<br>'."\n";
			$this->cats[$id_categ]['fullpath'] = '_'.$this->motherof[$cursor_categ].$this->cats[$id_categ]['fullpath'];
			$this->cats[$id_categ]['fulllabel'] = $this->cats[$this->motherof[$cursor_categ]]['label'].' >> '.$this->cats[$id_categ]['fulllabel'];
			//print '&nbsp; Result for id_categ='.$id_categ.' : '.$this->cats[$id_categ]['fullpath'].' '.$this->cats[$id_categ]['fulllabel'].'<br>'."\n";
			$i++; $cursor_categ=$this->motherof[$cursor_categ];
		}
		//print 'Result for id_categ='.$id_categ.' : '.$this->cats[$id_categ]['fullpath'].'<br>'."\n";

		// We count number of _ to have level
		$this->cats[$id_categ]['level']=dol_strlen(preg_replace('/[^_]/i','',$this->cats[$id_categ]['fullpath']));

		return;
	}

	/**
	 * 	Load this->motherof that is array(id_son=>id_parent, ...)
	 *
	 *	@return		int		<0 if KO, >0 if OK
	 */
	private function load_motherof()
	{
		global $conf;

		$this->motherof=array();

		// Load array[child]=parent
		$sql = "SELECT fk_father as id_parent, rowid as id_son";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_departament";
		$sql.= " WHERE fk_father != 0";
		$sql.= " AND entity IN (".getEntity('p_departament',1).")";

		dol_syslog(get_class($this)."::load_motherof", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
		{
			while ($obj= $this->db->fetch_object($resql))
			{
				$this->motherof[$obj->id_son]=$obj->id_parent;
			}
			return 1;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}
	function verif_accessresp($userid)
	{
		global $db,$langs;

		//verificamos que departamentos puede ver
		$filter = " AND t.status = 1 AND t.active = 1";
		$filter.= " AND t.fk_user_resp = ".$userid;
		$res = $this->fetchAll('','',0,0,array(1=>1),'AND',$filter);
		//vemos el responsable directo del area
		$aDepartametresp = array();
		if ($res > 0)
		{
			foreach ($this->lines AS $j => $line)
			{
				$aDepartamentresp[$line->id] = $line->id;
			}
		}
		return $aDepartamentresp;
	}

	function obtenerHijos($fk_father,array $array=array())
	{
		global $langs,$conf;

		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.fk_father,";
		$sql.= " t.ref,";
		$sql.= " t.label,";
		$sql.= " t.active";

		$sql.= " FROM ".MAIN_DB_PREFIX."p_departament as t";
		$sql.= " WHERE t.fk_father in (".$fk_father.")";

		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Pdepartament($this->db);

					$objnew->id    		= $obj->rowid;
					$objnew->entity 	= $obj->entity;
					$objnew->fk_father 	= $obj->fk_father;
					$objnew->ref 		= $obj->ref;
					$objnew->label 		= $obj->label;
					$objnew->active 	= $obj->active;
				   //$array[$i] = $objnew;
					$array[$obj->rowid] = $objnew;
					$i++;
				}
			}
			$this->array = $array;
			$this->db->free($resql);
			return $array;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			return -1;
		}
	}

	function get_userarea()
	{
		global $langs,$conf;
		require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
		$objAdherent = new Adherent($this->db);
		$VoBo = 0;
		$this->aResp = array();
		$this->aaDepartamentson = array();
		$this->aaDepartamentsup = array();

		$this->aDepartamentson  =  $this->obtenerHijos($this->id);

		if(count($this->aDepartamentson)>0) $VoBo ++;

		if ($this->fk_user_resp>0)
		{
			$rAh = $objAdherent->fetch($this->fk_user_resp);
			if($rAh == 1)
			{
				$this->aResp['id']=$objAdherent->id;
				$this->aResp['lastname']=$objAdherent->lastname;
				$this->aResp['firstname']=$objAdherent->firstname;
			}
		}
		if(count($this->aResp)>0) $VoBo ++;

		//vamos a encontrar el inmediato superior
		$fk_father = $this->fk_father;
		if ($fk_father>0)
		{
			$objTmp = new Pdepartament($this->db);
			$objTmp->fetch($fk_father);
			$this->aDepartamentsup['id'] = $objTmp->id;
			$this->aDepartamentsup['label'] = $objTmp->label;
		}

		if(count($this->aDepartamentsup)>0) $VoBo ++;

		return $VoBo;
	}
}
?>