<?php
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

class Projectext extends Project
{
	public $element = 'project';

	/**
	 * Fetches all.
	 *
	 * @param      string   $sortorder     The sortorder
	 * @param      string   $sortfield     The sortfield
	 * @param      integer  $limit         The limit
	 * @param      integer  $offset        The offset
	 * @param      array    $filter        The filter
	 * @param      string   $filtermode    The filtermode
	 * @param      string   $filterstatic  The filterstatic
	 * @param      boolean  $lView         The l view
	 *
	 * @return     integer  ( description_of_the_return_value )
	 */
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		global $conf,$langs;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = "SELECT t.rowid, t.ref, t.title, t.description, t.public, t.datec, t.opp_amount, t.budget_amount,";
		$sql.= " t.tms, t.dateo, t.datee, t.date_close, t.fk_soc, t.fk_user_creat, t.fk_user_close, t.fk_statut, t.fk_opp_status, t.opp_percent, t.note_private, t.note_public, t.model_pdf ";
		if ($conf->monprojet->enabled)
			$sql.= " , a.fk_entrepot, a.programmed, a.fk_contracting, a.fk_supervising, a.use_resource, a.origin, a.originid ";
		$sql.= " FROM " . MAIN_DB_PREFIX . "projet AS t";
		if ($conf->monprojet->enabled)
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet_add AS a ON a.fk_projet = t.rowid";
		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
			$sql .= " AND entity IN (" . getEntity("project", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if ($filterstatic){
			$sql.= $filterstatic;
		}
		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}

		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql)
		{
			$i =0;
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				while ($obj = $this->db->fetch_object($resql))
				{
					$line = new ProjectLine();

					$line->id = $obj->rowid;

					$line->ref = $obj->ref;
					$line->title = $obj->title;
					$line->titre = $obj->title;
                 // TODO deprecated
					$line->description = $obj->description;
					$line->fk_entrepot = $obj->fk_entrepot;
					$line->programmed = $obj->programmed;
					$line->fk_contracting = $obj->fk_contracting;
					$line->fk_supervising = $obj->fk_supervising;
					$line->use_resource = $obj->use_resource;
					$line->origin = $obj->origin;
					$line->originid = $obj->originid;
					$line->date_c = $this->db->jdate($obj->datec);
					$line->datec = $this->db->jdate($obj->datec);
                 // TODO deprecated
					$line->date_m = $this->db->jdate($obj->tms);
					$line->datem = $this->db->jdate($obj->tms);
                  // TODO deprecated
					$line->date_start = $this->db->jdate($obj->dateo);
					$line->date_end = $this->db->jdate($obj->datee);
					$line->date_close = $this->db->jdate($obj->date_close);

					$line->note_private = $obj->note_private;
					$line->note_public = $obj->note_public;
					$line->socid = $obj->fk_soc;
					$line->user_author_id = $obj->fk_user_creat;
					$line->user_close_id = $obj->fk_user_close;
					$line->fk_user_creat = $obj->fk_user_creat;
					$line->fk_user_modif = $obj->fk_user_creat;
					$line->public = $obj->public;
					$line->statut = $obj->fk_statut;
					$line->fk_statut = $obj->fk_statut;
					$line->opp_status = $obj->fk_opp_status;
					$line->opp_amount	= $obj->opp_amount;
					$line->opp_percent	= $obj->opp_percent;
					$line->budget_amount	= $obj->budget_amount;
					$line->modelpdf	= $obj->model_pdf;
					$this->lines[$obj->rowid] = $line;
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
			$this->error = $this->db->lasterror();
			return -1;
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
    function getNomUrladd($withpicto=0, $option='', $addlabel=0, $moreinpopup='', $sep=' - ')
    {
    	global $langs;

    	$result = '';
    	$link = '';
    	$linkend = '';
    	$label='';
    	if ($option != 'nolink') $label = '<u>' . $langs->trans("ShowProject") . '</u>';
    	if (! empty($this->ref))
            $label .= ($label?'<br>':'').'<b>' . $langs->trans('Ref') . ': </b>' . $this->ref;	// The space must be after the : to not being explode when showing the title in img_picto
        if (! empty($this->title))
            $label .= ($label?'<br>':'').'<b>' . $langs->trans('Label') . ': </b>' . $this->title;	// The space must be after the : to not being explode when showing the title in img_picto
        if (! empty($this->dateo))
            $label .= ($label?'<br>':'').'<b>' . $langs->trans('DateStart') . ': </b>' . dol_print_date($this->dateo, 'day');	// The space must be after the : to not being explode when showing the title in img_picto
        if (! empty($this->datee))
            $label .= ($label?'<br>':'').'<b>' . $langs->trans('DateEnd') . ': </b>' . dol_print_date($this->datee, 'day');	// The space must be after the : to not being explode when showing the title in img_picto
        if ($moreinpopup) $label.='<br>'.$moreinpopup;
        $linkclose = '" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';

        if ($option != 'nolink')
        {
        	if (preg_match('/\.php$/',$option)) {
        		$link = '<a href="' . dol_buildpath($option,1) . '?id=' . $this->id . $linkclose;
        		$linkend = '</a>';
        	}
        	else if ($option == 'task')
        	{
        		$link = '<a href="' . DOL_URL_ROOT . '/monprojet/tasks.php?id=' . $this->id . $linkclose;
        		$linkend = '</a>';
        	}
        	else
        	{
        		$link = '<a href="' . DOL_URL_ROOT . '/monprojet/card.php?id=' . $this->id . $linkclose;
        		$linkend = '</a>';
        	}
        }

        $picto = 'projectpub';
        if (!$this->public) $picto = 'project';


        if ($withpicto) $result.=($link . img_object($label, $picto, 'class="classfortooltip"') . $linkend);
        if ($withpicto && $withpicto != 2) $result.=' ';
        if ($withpicto != 2) $result.=$link . $this->ref . $linkend . (($addlabel && $this->title) ? $sep . dol_trunc($this->title, ($addlabel > 1 ? $addlabel : 0)) : '');
        	return $result;
        }

	/**
	 * Return array of projects a user has permission on, is affected to, or all projects
	 *
	 * @param 	User	$user			User object
	 * @param 	int		$mode			0=All project I have permission on, 1=Projects affected to me only, 2=Will return list of all projects with no test on contacts
	 * @param 	int		$list			0=Return array,1=Return string list
	 * @param	int		$socid			0=No filter on third party, id of third party
	 * @return 	array or string			Array of projects id, or string with projects id separated with ","
	 */
	function getMonProjectsAuthorizedForUser($user, $mode=0, $list=0, $socid=0)
	{
		$projects = array();
		$temp = array();
		if ($user->array_options['options_view_projet'])
		{
		//vamos a cambiar ciertos atributos
			$mode = 2;
			$socid = 0;
		}
		$sql = "SELECT ".(($mode == 0 || $mode == 1) ? "DISTINCT " : "")."p.rowid, p.ref";
		$sql.= " FROM " . MAIN_DB_PREFIX . "projet as p";
		if ($mode == 0 || $mode == 1 || $mode == 3)
		{
			$sql.= ", " . MAIN_DB_PREFIX . "element_contact as ec";
			$sql.= ", " . MAIN_DB_PREFIX . "c_type_contact as ctc";
		}
		$sql.= " WHERE p.entity IN (".getEntity('project',1).")";
		// Internal users must see project he is contact to even if project linked to a third party he can't see.
		//if ($socid || ! $user->rights->societe->client->voir)	$sql.= " AND (p.fk_soc IS NULL OR p.fk_soc = 0 OR p.fk_soc = ".$socid.")";
		//if ($socid > 0) $sql.= " AND (p.fk_soc IS NULL OR p.fk_soc = 0 OR p.fk_soc = " . $socid . ")";

		if ($mode == 0)
		{
			$sql.= " AND ec.element_id = p.rowid";
			$sql.= " AND ( p.public = 1";
			$sql.= " OR ( ctc.rowid = ec.fk_c_type_contact";
			$sql.= " AND ctc.element = '" . $this->element . "'";
			$sql.= " AND ( (ctc.source = 'internal' AND ec.fk_socpeople = ".$user->id.")";
			$sql.= " )";
			$sql.= " ))";
		}
		if ($mode == 1)
		{
			$sql.= " AND ec.element_id = p.rowid";
			$sql.= " AND ctc.rowid = ec.fk_c_type_contact";
			$sql.= " AND ctc.element = '" . $this->element . "'";
			$sql.= " AND ( (ctc.source = 'internal' AND ec.fk_socpeople = ".$user->id.")";
			$sql.= " )";
		}
		if ($mode == 2)
		{
			// No filter. Use this if user has permission to see all project
			if (!$user->admin)
			{
		  // if ($socid)
		  // 	$sql.= " AND p.fk_soc = ".$socid;
		  // else
		  // 	{

		  // 	}
		  //$sql.= ")";
			}
		}
		if ($mode == 3)
		{
			$sql.= " AND ec.element_id = p.rowid";
			$sql.= " AND ctc.rowid = ec.fk_c_type_contact";
			$sql.= " AND ctc.element = '" . $this->element . "'";
			$sql.= " AND ( (ctc.source = 'external' AND ec.fk_socpeople = ".$user->id.")";
			$sql.= " )";
		}
		//echo '<hr>mode '.$mode;
		//echo '<hr>'.$sql;
		//exit;
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num)
			{
				$row = $this->db->fetch_row($resql);
				$objet = new Project($this->db);
				$objet->fetch($row[0]);
		//echo '<hr>revisando cada proyecto '.$row[0];
		//verificamos los permisos segun el usuario
				$aRes = verifcontactprojet($user,$objet,$ret='res');
		//echo '<br>para  '.$row[0].' res '.
				$res = $aRes[0];
				if ($user->admin)
				{
					$projects[$row[0]] = $row[1];
					$temp[] = $row[0];
				}
				else
				{
					if ($res >0 && !empty($aRes[5]))
					{
						$projects[$row[0]] = $row[1];
						$temp[] = $row[0];
					}
				}
				$i++;
			}
		// echo '<pre>';
		// print_r($projects);
		// echo '</pre>';
		// exit;
			$this->db->free($resql);
		// echo '<hr>listado temp';
		// print_r($temp);
		// print_r($projects);
		// exit;
			if ($list)
			{
				if (empty($temp)) return '0';
				$result = implode(',', $temp);
				return $result;
			}
		}
		else
		{
			dol_print_error($this->db);
		}

		return $projects;
	}

		/**
	 * 	Check if user has permission on current project
	 *
	 * 	@param	User	$user		Object user to evaluate
	 * 	@param  string	$mode		Type of permission we want to know: 'read', 'write'
	 * 	@return	int					>0 if user has permission, <0 if user has no permission
	 */
		function restrictedProjectAreaadd($user, $mode='read')
		{
		// To verify role of users
			$userAccess = 0;
			if (($mode == 'read' && ! empty($user->rights->projet->all->lire)) || ($mode == 'write' && ! empty($user->rights->projet->all->creer)) || ($mode == 'delete' && ! empty($user->rights->projet->all->supprimer)))
			{
				$userAccess = 1;
			}
			else if ($this->public && (($mode == 'read' && ! empty($user->rights->projet->lire)) || ($mode == 'write' && ! empty($user->rights->projet->creer)) || ($mode == 'delete' && ! empty($user->rights->projet->supprimer))))
			{
				$userAccess = 1;
			}
			else
			{
				foreach (array('internal', 'external') as $source)
				{
					$userRole = $this->liste_contact(4, $source);
		  // echo '<pre>';
		  // print_r($userRole);
		  // echo '</pre>';
					$num = count($userRole);
					$nblinks = 0;
					while ($nblinks < $num)
					{
						if ($source == 'internal' && preg_match('/^PROJECT/', $userRole[$nblinks]['code']) && $user->id == $userRole[$nblinks]['id'])
						{
							if ($mode == 'read'   && $user->rights->monprojet->task->leer)
								$userAccess++;
							if ($mode == 'write'  && $user->rights->monprojet->task->crear)
								$userAccess++;
							if ($mode == 'delete' && $user->rights->monprojet->task->del)
								$userAccess++;
						}
		  //verificamos si dimos permiso a un externo y que lea proyectos de otras empresas
						if ($source == 'external' && preg_match('/^PROJECT/', $userRole[$nblinks]['code']) && $user->contact_id == $userRole[$nblinks]['id'] && $user->array_options['options_view_projet'])
						{
							if ($mode == 'read'   && $user->rights->monprojet->task->leer)
								$userAccess++;
							if ($mode == 'write'  && $user->rights->monprojet->task->crear)
								$userAccess++;
							if ($mode == 'delete' && $user->rights->monprojet->task->del)
								$userAccess++;
						}
						$nblinks++;
					}
				}
	  //if (empty($nblinks))	// If nobody has permission, we grant creator
	  //{
	  //	if ((!empty($this->user_author_id) && $this->user_author_id == $user->id))
	  //	{
	  //		$userAccess = 1;
	  //	}
	  //}
			}

			return ($userAccess?$userAccess:-1);
		}

	/*
	 * Borrado de tareas del proyectos
	 */
	function delete_task($user,$notrigger=0)
	{
		global $langs, $conf;
		$error = 0;

		$this->db->begin();

	  // Delete tasks
		if (! $error)
		{
			$sql = "DELETE FROM " . MAIN_DB_PREFIX . "projet_task_time";
			$sql.= " WHERE fk_task IN (SELECT rowid FROM " . MAIN_DB_PREFIX . "projet_task WHERE fk_projet=" . $this->id . ")";

			$resql = $this->db->query($sql);
			if (!$resql)
			{
				$this->errors[] = $this->db->lasterror();
				$error++;
			}
		}
		if (! $error)
		{
			$sql = "DELETE FROM " . MAIN_DB_PREFIX . "projet_task_extrafields";
			$sql.= " WHERE fk_object IN (SELECT rowid FROM " . MAIN_DB_PREFIX . "projet_task WHERE fk_projet=" . $this->id . ")";

			$resql = $this->db->query($sql);
			if (!$resql)
			{
				$this->errors[] = $this->db->lasterror();
				$error++;
			}
		}
		if (! $error)
		{
			$sql = "DELETE FROM " . MAIN_DB_PREFIX . "projet_task";
			$sql.= " WHERE fk_projet=" . $this->id;

			$resql = $this->db->query($sql);
			if (!$resql)
			{
				$this->errors[] = $this->db->lasterror();
				$error++;
			}
		}
		if (empty($error))
		{
			$this->db->commit();
			return 1;
		}
		else
		{
			foreach ( $this->errors as $errmsg )
			{
				dol_syslog(get_class($this) . "::delete " . $errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			dol_syslog(get_class($this) . "::delete " . $this->error, LOG_ERR);
			$this->db->rollback();
			return -1;
		}
	}
}


/**
 * Class ConcprojetLine
 */
class ProjectLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $fk_soc;
	public $datec = '';
	public $tms = '';
	public $dateo = '';
	public $datee = '';
	public $ref;
	public $entity;
	public $title;
	public $description;
	public $fk_entrepot;
	public $programmed;
	public $fk_contracting;
	public $fk_supervising;
	public $use_resource;
	public $origin;
	public $originid;
	public $fk_user_creat;
	public $fk_user_modif;
	public $public;
	public $fk_statut;
	public $fk_opp_status;
	public $opp_percent;
	public $date_close = '';
	public $fk_user_close;
	public $note_private;
	public $note_public;
	public $opp_amount;
	public $budget_amount;
	public $model_pdf;
	public $import_key;

	/**
	 * @var mixed Sample line property 2
	 */

}
?>