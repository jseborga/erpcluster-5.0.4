<?php



class Formadd extends Form
{

  /**
   *  Output html form to select a members
   *
   *	@param	string	$selected       Preselected type
   *	@param  string	$htmlname       Name of field in form
   *  @param  string	$filter         optional filters criteras (example: 's.rowid <> x')
   *	@param	int		$showempty		Add an empty field
   * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
   * 	@param	int		$forcecombo		Force to use combo box
   *  @param	array	$events			Event options to run on change. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
   *	@param	int		$limit			Maximum number of elements
   * 	@return	string					HTML string with
   *  @deprecated						Use select_thirdparty instead
   */
  function select_member($selected='', $htmlname='fk_member', $filter='', $showempty=0, $showtype=0, $forcecombo=0, $events=array(), $limit=0)
  {
  	return $this->select_member_list($selected, $htmlname, $filter, $showempty, $showtype, $forcecombo, $events, '', 0, $limit);
  }

  /**
   *  Output html form to select a members
   *
   *	@param	string	$selected       Preselected type
   *	@param  string	$htmlname       Name of field in form
   *  @param  string	$filter         optional filters criteras (example: 's.rowid <> x')
   *	@param	int		$showempty		Add an empty field
   * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
   * 	@param	int		$forcecombo		Force to use combo box
   *  @param	array	$events			Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
   *  @param	string	$filterkey		Filter on key value
   *  @param	int		$outputmode		0=HTML select string, 1=Array
   *  @param	int		$limit			Limit number of answers
   * 	@return	string					HTML string with
   */
  function select_member_list($selected='',$htmlname='fk_member',$filter='',$showempty=0, $showtype=0, $forcecombo=0, $events=array(), $filterkey='', $outputmode=0, $limit=0)
  {
  	global $conf,$user,$langs;

  	$out=''; $num=0;
  	$outarray=array();
	//sql
  	$sql = "SELECT d.rowid, d.ref_ext, d.civility as civility_id, d.firstname, d.lastname, d.societe as company, d.fk_soc, d.statut, d.public, d.address, d.zip, d.town, d.note,";
  	$sql.= " d.email, d.skype, d.phone, d.phone_perso, d.phone_mobile, d.login, d.pass,";
  	$sql.= " d.photo, d.fk_adherent_type, d.morphy, d.entity,";
  	$sql.= " d.datec as datec,";
  	$sql.= " d.tms as datem,";
  	$sql.= " d.datefin as datefin,";
  	$sql.= " d.birth as birthday,";
  	$sql.= " d.datevalid as datev,";
  	$sql.= " d.country,";
  	$sql.= " d.state_id,";
  	$sql.= " c.rowid as country_id, c.code as country_code, c.label as country,";
  	$sql.= " dep.nom as state, dep.code_departement as state_code,";
  	$sql.= " t.libelle as type, t.cotisation as cotisation,";
  	$sql.= " u.rowid as user_id, u.login as user_login";
  	$sql.= " FROM ".MAIN_DB_PREFIX."adherent_type as t, ".MAIN_DB_PREFIX."adherent as d";
  	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON d.country = c.rowid";
  	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements as dep ON d.state_id = dep.rowid";
  	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON d.rowid = u.fk_member";
  	$sql.= " WHERE d.fk_adherent_type = t.rowid";
  	if ($filter) $sql.= " AND (".$filter.")";
  	$sql.= " AND d.entity IN (".getEntity().")";
  	if (! empty($conf->global->MEMBER_HIDE_INACTIVE_IN_COMBOBOX)) $sql.= " AND d.statut<>0 ";
	// Add criteria
  	if ($filterkey && $filterkey != '')
  	{
  		$sql.=" AND (";
	if (! empty($conf->global->MEMBER_DONOTSEARCH_ANYWHERE))   // Can use index
	{
		$sql.="(d.lastname LIKE '".$this->db->escape($filterkey)."%')";
	}
	else
	{
		// For natural search
		$scrit = explode(' ', $filterkey);
		foreach ($scrit as $crit)
		{
			$sql.=" AND (";
			$sql.=" d.lastname LIKE '%".$this->db->escape($crit)."%'";
			$sql.= " OR d.firstname LIKE '%".$this->db->escape($crit)."%'";
			$sql.= " OR d.login LIKE '%".$this->db->escape($crit)."%'";
			$sql.= " OR d.email LIKE '%".$this->db->escape($crit)."%'";
			$sql.= " OR d.skype LIKE '%".$this->db->escape($crit)."%'";
			$sql.= " OR d.phone LIKE '%".$this->db->escape($crit)."%'";
			$sql.= " OR d.phone_perso LIKE '%".$this->db->escape($crit)."%'";
			$sql.= " OR d.phone_mobile LIKE '%".$this->db->escape($crit)."%'";
			$sql.=")";
		}
	}
	$sql.=")";
}
	//fin sql
$sql.=$this->db->order("d.lastname","ASC");
if ($limit > 0) $sql.=$this->db->plimit($limit);

dol_syslog(get_class($this)."::select_member_list", LOG_DEBUG);
$resql=$this->db->query($sql);
if ($resql)
{
	if (! empty($conf->use_javascript_ajax))
	{
		if (! empty($conf->global->MEMBER_USE_SEARCH_TO_SELECT) && ! $forcecombo)
		{
			include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
			$out.= ajax_combobox($htmlname, $events, $conf->global->MEMBER_USE_SEARCH_TO_SELECT);
		}
		else
		{
		if (count($events))		// Add management of event
		{
			$out.='<script type="text/javascript">
			$(document).ready(function() {
				jQuery("#'.$htmlname.'").change(function () {
					var obj = '.json_encode($events).';
					$.each(obj, function(key,values) {
						if (values.method.length) {
							runJsCodeForEvent'.$htmlname.'(values);
						}
					});
				});

				function runJsCodeForEvent'.$htmlname.'(obj) {
					var id = $("#'.$htmlname.'").val();
					var method = obj.method;
					var url = obj.url;
					var htmlname = obj.htmlname;
					var showempty = obj.showempty;
					$.getJSON(url,
					{
						action: method,
						id: id,
						htmlname: htmlname,
						showempty: showempty
					},
					function(response) {
						$.each(obj.params, function(key,action) {
							if (key.length) {
								var num = response.num;
								if (num > 0) {
									$("#" + key).removeAttr(action);
								} else {
									$("#" + key).attr(action, action);
								}
							}
						});
						$("select#" + htmlname).html(response.value);
						if (response.num) {
							var selecthtml_str = response.value;
							var selecthtml_dom=$.parseHTML(selecthtml_str);
							$("#inputautocomplete"+htmlname).val(selecthtml_dom[0][0].innerHTML);
						} else {
							$("#inputautocomplete"+htmlname).val("");
						}
						$("select#" + htmlname).change();	/* Trigger event change */
					});
				}
			})
		</script>';
	}
}
}

	// Construct $out and $outarray
$out.= '<select id="'.$htmlname.'" class="flat" name="'.$htmlname.'">'."\n";

$textifempty='';
	// Do not use textempty = ' ' or '&nbsp;' here, or search on key will search on ' key'.
	//$textifempty=' ';
	//if (! empty($conf->use_javascript_ajax) || $forcecombo) $textifempty='';
if ($showempty) $out.= '<option value="-1">'.$textifempty.'</option>'."\n";

$num = $this->db->num_rows($resql);
$i = 0;
if ($num)
{
	while ($i < $num)
	{
		$obj = $this->db->fetch_object($resql);
		$label='';
		$label=$obj->lastname.' '.$obj->firstname.' - '.$obj->login.': '.$langs->trans('Phone').': '.$obj->phone;

		// if ($showtype)
		//   {
		//     if ($obj->client || $obj->fournisseur) $label.=' (';
		//     if ($obj->client == 1 || $obj->client == 3) $label.=$langs->trans("Customer");
		//     if ($obj->client == 2 || $obj->client == 3) $label.=($obj->client==3?', ':'').$langs->trans("Prospect");
		//     if ($obj->fournisseur) $label.=($obj->client?', ':'').$langs->trans("Supplier");
		//     if ($obj->client || $obj->fournisseur) $label.=')';
		//   }
		if ($selected > 0 && $selected == $obj->rowid)
		{
			$out.= '<option value="'.$obj->rowid.'" selected="selected">'.$label.'</option>';
		}
		else
		{
			$out.= '<option value="'.$obj->rowid.'">'.$label.'</option>';
		}

		array_push($outarray, array('key'=>$obj->rowid, 'value'=>$obj->name, 'label'=>$obj->name));

		$i++;
		if (($i % 10) == 0) $out.="\n";
	}
}
$out.= '</select>'."\n";
}
else
{
	dol_print_error($this->db);
}

$this->result=array('nbofmember'=>$num);

if ($outputmode) return $outarray;
return $out;
}


  /**
   *  Output html form to select a user
   *
   *	@param	string	$selected       Preselected type
   *	@param  string	$htmlname       Name of field in form
   *  @param  string	$filter         optional filters criteras (example: 's.rowid <> x')
   *	@param	int		$showempty		Add an empty field
   * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
   * 	@param	int		$forcecombo		Force to use combo box
   *  @param	array	$events			Event options to run on change. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
   *	@param	int		$limit			Maximum number of elements
   * 	@return	string					HTML string with
   *  @deprecated						Use select_thirdparty instead
   */
  function select_use($selected='', $htmlname='userid', $filter='', $showempty=0, $showtype=0, $forcecombo=0, $events=array(), $limit=0)
  {
  	return $this->select_use_list($selected, $htmlname, $filter, $showempty, $showtype, $forcecombo, $events, '', 0, $limit);
  }

  /**
   *  Output html form to select a user
   *
   *	@param	string	$selected       Preselected type
   *	@param  string	$htmlname       Name of field in form
   *  @param  string	$filter         optional filters criteras (example: 's.rowid <> x')
   *	@param	int		$showempty		Add an empty field
   * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
   * 	@param	int		$forcecombo		Force to use combo box
   *  @param	array	$events			Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
   *  @param	string	$filterkey		Filter on key value
   *  @param	int		$outputmode		0=HTML select string, 1=Array
   *  @param	int		$limit			Limit number of answers
   * 	@return	string					HTML string with
   */
  function select_use_list($selected='',$htmlname='userid',$filter='',$showempty=0, $showtype=0, $forcecombo=0, $events=array(), $filterkey='', $outputmode=0, $limit=0)
  {
  	global $conf,$user,$langs;

  	$out=''; $num=0;
  	$outarray=array();
	//sql
  	$sql = "SELECT u.rowid, u.lastname, u.firstname, u.email, u.job, u.skype, u.signature, u.office_phone, u.office_fax, u.user_mobile,";
  	$sql.= " u.admin, u.login, u.note,";
  	$sql.= " u.pass, u.pass_crypted, u.pass_temp,";
	//$sql.= " u.fk_societe, ";
  	$sql.= " u.fk_socpeople, u.fk_member, u.fk_user, u.ldap_sid,";
  	$sql.= " u.statut, u.lang, u.entity,";
  	$sql.= " u.datec as datec,";
  	$sql.= " u.tms as datem,";
  	$sql.= " u.datelastlogin as datel,";
  	$sql.= " u.datepreviouslogin as datep,";
  	$sql.= " u.photo as photo,";
  	$sql.= " u.openid as openid,";
  	$sql.= " u.accountancy_code,";
  	$sql.= " u.thm,";
  	$sql.= " u.tjm,";
  	$sql.= " u.salary,";
  	$sql.= " u.salaryextra,";
  	$sql.= " u.weeklyhours,";
  	$sql.= " u.color,";
  	$sql.= " u.ref_int, u.ref_ext";
  	$sql.= " FROM ".MAIN_DB_PREFIX."user as u";
  	if ((empty($conf->multicompany->enabled) || empty($conf->multicompany->transverse_mode)) && (! empty($user->entity)))
  	{
  		$sql.= " WHERE u.entity IN (0,".$conf->entity.")";
  	}
  	else
  	{
  		$sql.= " WHERE u.entity IS NOT NULL";
  	}
  	if ($filter) $sql.= " AND (".$filter.")";
  	if (! empty($conf->global->USER_HIDE_INACTIVE_IN_COMBOBOX)) $sql.= " AND u.statut<>0 ";
	// Add criteria
  	if ($filterkey && $filterkey != '')
  	{
  		$sql.=" AND (";
	if (! empty($conf->global->USER_DONOTSEARCH_ANYWHERE))   // Can use index
	{
		$sql.="(u.lastname LIKE '".$this->db->escape($filterkey)."%'";
		$sql.= " OR u.firstname LIKE '%".$this->db->escape($crit)."%'";
		$sql.= " OR u.login LIKE '%".$this->db->escape($crit)."%'";
		$sql.= " OR u.email LIKE '%".$this->db->escape($crit)."%'";
		$sql.= " OR u.skype LIKE '%".$this->db->escape($crit)."%'";
		$sql.= " OR u.office_phone LIKE '%".$this->db->escape($crit)."%'";
		$sql.= " OR u.user_mobile LIKE '%".$this->db->escape($crit)."%'";
		$sql.=")";

	}
	else
	{
		// For natural search
		$scrit = explode(' ', $filterkey);
		foreach ($scrit as $crit)
		{
			$sql.=" AND (";
			$sql.=" u.lastname LIKE '%".$this->db->escape($crit)."%'";
			$sql.= " OR u.firstname LIKE '%".$this->db->escape($crit)."%'";
			$sql.= " OR u.login LIKE '%".$this->db->escape($crit)."%'";
			$sql.= " OR u.email LIKE '%".$this->db->escape($crit)."%'";
			$sql.= " OR u.skype LIKE '%".$this->db->escape($crit)."%'";
			$sql.= " OR u.office_phone LIKE '%".$this->db->escape($crit)."%'";
			$sql.= " OR u.user_mobile LIKE '%".$this->db->escape($crit)."%'";
			$sql.=")";
		}
	}
	$sql.=")";
}
	//fin sql
$sql.=$this->db->order("u.lastname","ASC");
if ($limit > 0) $sql.=$this->db->plimit($limit);

dol_syslog(get_class($this)."::select_user_list", LOG_DEBUG);
$resql=$this->db->query($sql);
if ($resql)
{
	if (! empty($conf->use_javascript_ajax))
	{
		if (! empty($conf->global->USER_USE_SEARCH_TO_SELECT) && ! $forcecombo)
		{
			include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
			$out.= ajax_combobox($htmlname, $events, $conf->global->MEMBER_USE_SEARCH_TO_SELECT);
		}
		else
		{
		if (count($events))		// Add management of event
		{
			$out.='<script type="text/javascript">
			$(document).ready(function() {
				jQuery("#'.$htmlname.'").change(function () {
					var obj = '.json_encode($events).';
					$.each(obj, function(key,values) {
						if (values.method.length) {
							runJsCodeForEvent'.$htmlname.'(values);
						}
					});
				});

				function runJsCodeForEvent'.$htmlname.'(obj) {
					var id = $("#'.$htmlname.'").val();
					var method = obj.method;
					var url = obj.url;
					var htmlname = obj.htmlname;
					var showempty = obj.showempty;
					$.getJSON(url,
					{
						action: method,
						id: id,
						htmlname: htmlname,
						showempty: showempty
					},
					function(response) {
						$.each(obj.params, function(key,action) {
							if (key.length) {
								var num = response.num;
								if (num > 0) {
									$("#" + key).removeAttr(action);
								} else {
									$("#" + key).attr(action, action);
								}
							}
						});
						$("select#" + htmlname).html(response.value);
						if (response.num) {
							var selecthtml_str = response.value;
							var selecthtml_dom=$.parseHTML(selecthtml_str);
							$("#inputautocomplete"+htmlname).val(selecthtml_dom[0][0].innerHTML);
						} else {
							$("#inputautocomplete"+htmlname).val("");
						}
						$("select#" + htmlname).change();	/* Trigger event change */
					});
				}
			})
		</script>';
	}
}
}

	// Construct $out and $outarray
$out.= '<select id="'.$htmlname.'" class="flat" name="'.$htmlname.'">'."\n";

$textifempty='';
	// Do not use textempty = ' ' or '&nbsp;' here, or search on key will search on ' key'.
	//$textifempty=' ';
	//if (! empty($conf->use_javascript_ajax) || $forcecombo) $textifempty='';
if ($showempty) $out.= '<option value="-1">'.$textifempty.'</option>'."\n";

$num = $this->db->num_rows($resql);
$i = 0;
if ($num)
{
	while ($i < $num)
	{
		$obj = $this->db->fetch_object($resql);
		$label='';
		$label=$obj->lastname.' '.$obj->firstname.' - '.$obj->login.': '.$langs->trans('Phone').': '.$obj->office_phone;

		// if ($showtype)
		//   {
		//     if ($obj->client || $obj->fournisseur) $label.=' (';
		//     if ($obj->client == 1 || $obj->client == 3) $label.=$langs->trans("Customer");
		//     if ($obj->client == 2 || $obj->client == 3) $label.=($obj->client==3?', ':'').$langs->trans("Prospect");
		//     if ($obj->fournisseur) $label.=($obj->client?', ':'').$langs->trans("Supplier");
		//     if ($obj->client || $obj->fournisseur) $label.=')';
		//   }
		if ($selected > 0 && $selected == $obj->rowid)
		{
			$out.= '<option value="'.$obj->rowid.'" selected="selected">'.$label.'</option>';
		}
		else
		{
			$out.= '<option value="'.$obj->rowid.'">'.$label.'</option>';
		}

		array_push($outarray, array('key'=>$obj->rowid, 'value'=>$obj->name, 'label'=>$obj->name));

		$i++;
		if (($i % 10) == 0) $out.="\n";
	}
}
$out.= '</select>'."\n";
}
else
{
	dol_print_error($this->db);
}

$this->result=array('nbofuser'=>$num);

if ($outputmode) return $outarray;
return $out;
}

}
?>