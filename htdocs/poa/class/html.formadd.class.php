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
    $sql.= " u.fk_socpeople, u.fk_member, u.fk_user, u.ldap_sid,";
    $sql.= " u.statut, u.lang, u.entity,";
    $sql.= " u.datec as datec,";
    $sql.= " u.tms as datem,";
    $sql.= " u.datelastlogin as datel,";
    $sql.= " u.datepreviouslogin as datep,";
    $sql.= " u.photo as photo,";
    $sql.= " u.openid as openid,";
    $sql.= " u.accountancy_code,";
    // $sql.= " u.thm,";
    // $sql.= " u.tjm,";
    // $sql.= " u.salary,";
    // $sql.= " u.salaryextra,";
    // $sql.= " u.weeklyhours,";
    // $sql.= " u.color,";
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
	    $sql.= " OR u.firstname LIKE '".$this->db->escape($filterkey)."%'";
	    $sql.= " OR u.login LIKE '".$this->db->escape($filterkey)."%'";
	    $sql.= " OR u.email LIKE '".$this->db->escape($filterkey)."%'";
	    $sql.= " OR u.skype LIKE '".$this->db->escape($filterkey)."%'";
	    $sql.= " OR u.office_phone LIKE '".$this->db->escape($filterkey)."%'";
	    $sql.= " OR u.user_mobile LIKE '".$this->db->escape($filterkey)."%'";
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
    //echo '<hr>|'.$conf->use_javascript_ajax.'|'.$conf->global->USER_USE_SEARCH_TO_SELECT;
    //fin sql
    $sql.=$this->db->order("u.lastname","ASC");
    if ($limit > 0) $sql.=$this->db->plimit($limit);

    dol_syslog(get_class($this)."::select_use_list", LOG_DEBUG);
    $resql=$this->db->query($sql);
    if ($resql)
      {
	if (! empty($conf->use_javascript_ajax))
	  {
	    if (! empty($conf->global->USER_USE_SEARCH_TO_SELECT) && ! $forcecombo)
	      {
		include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
		$out.= ajax_combobox($htmlname, $events, $conf->global->USER_USE_SEARCH_TO_SELECT);
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
	$out.= '<select id="'.$htmlname.'" class="form-control" name="'.$htmlname.'">'."\n";

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

		array_push($outarray, array('key'=>$obj->rowid, 'value'=>$obj->lastname.' '.$obj->firstname, 'label'=>$obj->lastname.' '.$obj->firstname));

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

    if ($outputmode)
      {
	return $outarray;
      }

    return $out;
  }

function select_dateboot($htmlname, $value='')
{
$html = '<div class="well well-sm">
  <div class="input-group date">
  <input type="text" name="datepicker" id="datepicker" readonly="readonly" size="12" />
  </div>
</div>';
return $html;
}
    function select_dateadd($set_time='', $prefix='re', $h=0, $m=0, $empty=0, $form_name="", $d=1, $addnowlink=0, $nooutput=0, $disabled=0, $fullday='', $addplusone='', $adddateof='')
    {
        global $conf,$langs;

        $retstring='';

        if($prefix=='') $prefix='re';
        if($h == '') $h=0;
        if($m == '') $m=0;
        $emptydate=0;
        $emptyhours=0;
        if ($empty == 1) { $emptydate=1; $emptyhours=1; }
        if ($empty == 2) { $emptydate=0; $emptyhours=1; }
        $orig_set_time=$set_time;

        if ($set_time === '' && $emptydate == 0)
        {
            include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
            $set_time = dol_now('tzuser')-(getServerTimeZoneInt('now')*3600); // set_time must be relative to PHP server timezone
        }

        // Analysis of the pre-selection date
        if (preg_match('/^([0-9]+)\-([0-9]+)\-([0-9]+)\s?([0-9]+)?:?([0-9]+)?/',$set_time,$reg))
        {
            // Date format 'YYYY-MM-DD' or 'YYYY-MM-DD HH:MM:SS'
            $syear  = (! empty($reg[1])?$reg[1]:'');
            $smonth = (! empty($reg[2])?$reg[2]:'');
            $sday   = (! empty($reg[3])?$reg[3]:'');
            $shour  = (! empty($reg[4])?$reg[4]:'');
            $smin   = (! empty($reg[5])?$reg[5]:'');
        }
        elseif (strval($set_time) != '' && $set_time != -1)
        {
            // set_time est un timestamps (0 possible)
            $syear = dol_print_date($set_time, "%Y");
            $smonth = dol_print_date($set_time, "%m");
            $sday = dol_print_date($set_time, "%d");
            if ($orig_set_time != '')
            {
                $shour = dol_print_date($set_time, "%H");
                $smin = dol_print_date($set_time, "%M");
            }
        }
        else
        {
            // Date est '' ou vaut -1
            $syear = '';
            $smonth = '';
            $sday = '';
            $shour = '';
            $smin = '';
        }

        $usecalendar='combo';
        if (! empty($conf->use_javascript_ajax) && (empty($conf->global->MAIN_POPUP_CALENDAR) || $conf->global->MAIN_POPUP_CALENDAR != "none")) $usecalendar=empty($conf->global->MAIN_POPUP_CALENDAR)?'eldy':$conf->global->MAIN_POPUP_CALENDAR;
        if ($conf->browser->phone) $usecalendar='combo';

        if ($d)
        {
            // Show date with popup
            if ($usecalendar != 'combo')
            {
                $formated_date='';
                //print "e".$set_time." t ".$conf->format_date_short;
                if (strval($set_time) != '' && $set_time != -1)
                {
                    //$formated_date=dol_print_date($set_time,$conf->format_date_short);
                    $formated_date=dol_print_date($set_time,$langs->trans("FormatDateShortInput"));  // FormatDateShortInput for dol_print_date / FormatDateShortJavaInput that is same for javascript
                }

                // Calendrier popup version eldy
                if ($usecalendar == "eldy")
                {
                    // Zone de saisie manuelle de la date
                    $retstring.='<input class="flat form-control" id="'.$prefix.'" name="'.$prefix.'" type="text" size="12" maxlength="11" value="'.$formated_date.'"';
                    $retstring.=($disabled?' disabled':'');
                    $retstring.=' onChange="dpChangeDay(\''.$prefix.'\',\''.$langs->trans("FormatDateShortJavaInput").'\'); "';  // FormatDateShortInput for dol_print_date / FormatDateShortJavaInput that is same for javascript
                    $retstring.='>';

                    // Icone calendrier
                    if (! $disabled)
                    {
                        $retstring.='<button id="'.$prefix.'Button" type="button" class="dpInvisibleButtons"';
                        $base=DOL_URL_ROOT.'/core/';
                        $retstring.=' onClick="showDP(\''.$base.'\',\''.$prefix.'\',\''.$langs->trans("FormatDateShortJavaInput").'\',\''.$langs->defaultlang.'\');">'.img_object($langs->trans("SelectDate"),'calendarday','class="datecallink"').'</button>';
                    }
                    else $retstring.='<button id="'.$prefix.'Button" type="button" class="dpInvisibleButtons">'.img_object($langs->trans("Disabled"),'calendarday','class="datecallink"').'</button>';

                    $retstring.='<input type="hidden" id="'.$prefix.'day"   name="'.$prefix.'day"   value="'.$sday.'">'."\n";
                    $retstring.='<input type="hidden" id="'.$prefix.'month" name="'.$prefix.'month" value="'.$smonth.'">'."\n";
                    $retstring.='<input type="hidden" id="'.$prefix.'year"  name="'.$prefix.'year"  value="'.$syear.'">'."\n";
                }
                else
                {
                    print "Bad value of MAIN_POPUP_CALENDAR";
                }
            }
            // Show date with combo selects
            else
            {
                //$retstring.='<div class="inline-block">';
                // Day
                $retstring.='<select'.($disabled?' disabled':'').' class="flat form-control" id="'.$prefix.'day" name="'.$prefix.'day">';

                if ($emptydate || $set_time == -1)
                {
                    $retstring.='<option value="0" selected>&nbsp;</option>';
                }

                for ($day = 1 ; $day <= 31; $day++)
                {
                    $retstring.='<option value="'.$day.'"'.($day == $sday ? ' selected':'').'>'.$day.'</option>';
                }

                $retstring.="</select>";

                $retstring.='<select'.($disabled?' disabled':'').' class="flat" id="'.$prefix.'month" name="'.$prefix.'month">';
                if ($emptydate || $set_time == -1)
                {
                    $retstring.='<option value="0" selected>&nbsp;</option>';
                }

                // Month
                for ($month = 1 ; $month <= 12 ; $month++)
                {
                    $retstring.='<option value="'.$month.'"'.($month == $smonth?' selected':'').'>';
                    $retstring.=dol_print_date(mktime(12,0,0,$month,1,2000),"%b");
                    $retstring.="</option>";
                }
                $retstring.="</select>";

                // Year
                if ($emptydate || $set_time == -1)
                {
                    $retstring.='<input'.($disabled?' disabled':'').' placeholder="'.dol_escape_htmltag($langs->trans("Year")).'" class="flat form-control" type="text" size="3" maxlength="4" id="'.$prefix.'year" name="'.$prefix.'year" value="'.$syear.'">';
                }
                else
                {
                    $retstring.='<select'.($disabled?' disabled':'').' class="flat" id="'.$prefix.'year" name="'.$prefix.'year">';

                    for ($year = $syear - 5; $year < $syear + 10 ; $year++)
                    {
                        $retstring.='<option value="'.$year.'"'.($year == $syear ? ' selected':'').'>'.$year.'</option>';
                    }
                    $retstring.="</select>\n";
                }
                //$retstring.='</div>';
            }
        }

        if ($d && $h) $retstring.='&nbsp;';

        if ($h)
        {
            // Show hour
            $retstring.='<select'.($disabled?' disabled':'').' class="flat form-control '.($fullday?$fullday.'hour':'').'" id="'.$prefix.'hour" name="'.$prefix.'hour">';
            if ($emptyhours) $retstring.='<option value="-1">&nbsp;</option>';
            for ($hour = 0; $hour < 24; $hour++)
            {
                if (strlen($hour) < 2) $hour = "0" . $hour;
                $retstring.='<option value="'.$hour.'"'.(($hour == $shour)?' selected':'').'>'.$hour.(empty($conf->dol_optimize_smallscreen)?'':'H').'</option>';
            }
            $retstring.='</select>';
            if ($m && empty($conf->dol_optimize_smallscreen)) $retstring.=":";
        }

        if ($m)
        {
            // Show minutes
            $retstring.='<select'.($disabled?' disabled':'').' class="flat '.($fullday?$fullday.'min':'').'" id="'.$prefix.'min" name="'.$prefix.'min">';
            if ($emptyhours) $retstring.='<option value="-1">&nbsp;</option>';
            for ($min = 0; $min < 60 ; $min++)
            {
                if (strlen($min) < 2) $min = "0" . $min;
                $retstring.='<option value="'.$min.'"'.(($min == $smin)?' selected':'').'>'.$min.(empty($conf->dol_optimize_smallscreen)?'':'').'</option>';
            }
            $retstring.='</select>';
        }

        // Add a "Now" link
        if ($conf->use_javascript_ajax && $addnowlink)
        {
            // Script which will be inserted in the onClick of the "Now" link
            $reset_scripts = "";

            // Generate the date part, depending on the use or not of the javascript calendar
            $reset_scripts .= 'jQuery(\'#'.$prefix.'\').val(\''.dol_print_date(dol_now(),'day').'\');';
            $reset_scripts .= 'jQuery(\'#'.$prefix.'day\').val(\''.dol_print_date(dol_now(),'%d').'\');';
            $reset_scripts .= 'jQuery(\'#'.$prefix.'month\').val(\''.dol_print_date(dol_now(),'%m').'\');';
            $reset_scripts .= 'jQuery(\'#'.$prefix.'year\').val(\''.dol_print_date(dol_now(),'%Y').'\');';
            /*if ($usecalendar == "eldy")
            {
                $base=DOL_URL_ROOT.'/core/';
                $reset_scripts .= 'resetDP(\''.$base.'\',\''.$prefix.'\',\''.$langs->trans("FormatDateShortJavaInput").'\',\''.$langs->defaultlang.'\');';
            }
            else
            {
                $reset_scripts .= 'this.form.elements[\''.$prefix.'day\'].value=formatDate(new Date(), \'d\'); ';
                $reset_scripts .= 'this.form.elements[\''.$prefix.'month\'].value=formatDate(new Date(), \'M\'); ';
                $reset_scripts .= 'this.form.elements[\''.$prefix.'year\'].value=formatDate(new Date(), \'yyyy\'); ';
            }*/
            // Update the hour part
            if ($h)
            {
                if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
                //$reset_scripts .= 'this.form.elements[\''.$prefix.'hour\'].value=formatDate(new Date(), \'HH\'); ';
                $reset_scripts .= 'jQuery(\'#'.$prefix.'hour\').val(\''.dol_print_date(dol_now(),'%H').'\');';
                if ($fullday) $reset_scripts .= ' } ';
            }
            // Update the minute part
            if ($m)
            {
                if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
                //$reset_scripts .= 'this.form.elements[\''.$prefix.'min\'].value=formatDate(new Date(), \'mm\'); ';
                $reset_scripts .= 'jQuery(\'#'.$prefix.'min\').val(\''.dol_print_date(dol_now(),'%M').'\');';
                if ($fullday) $reset_scripts .= ' } ';
            }
            // If reset_scripts is not empty, print the link with the reset_scripts in the onClick
            if ($reset_scripts && empty($conf->dol_optimize_smallscreen))
            {
                $retstring.=' <button class="dpInvisibleButtons datenowlink" id="'.$prefix.'ButtonNow" type="button" name="_useless" value="now" onClick="'.$reset_scripts.'">';
                $retstring.=$langs->trans("Now");
                $retstring.='</button> ';
            }
        }

        // Add a "Plus one hour" link
        if ($conf->use_javascript_ajax && $addplusone)
        {
            // Script which will be inserted in the onClick of the "Add plusone" link
            $reset_scripts = "";

            // Generate the date part, depending on the use or not of the javascript calendar
            $reset_scripts .= 'jQuery(\'#'.$prefix.'\').val(\''.dol_print_date(dol_now(),'day').'\');';
            $reset_scripts .= 'jQuery(\'#'.$prefix.'day\').val(\''.dol_print_date(dol_now(),'%d').'\');';
            $reset_scripts .= 'jQuery(\'#'.$prefix.'month\').val(\''.dol_print_date(dol_now(),'%m').'\');';
            $reset_scripts .= 'jQuery(\'#'.$prefix.'year\').val(\''.dol_print_date(dol_now(),'%Y').'\');';
            // Update the hour part
            if ($h)
            {
                if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
                $reset_scripts .= 'jQuery(\'#'.$prefix.'hour\').val(\''.dol_print_date(dol_now(),'%H').'\');';
                if ($fullday) $reset_scripts .= ' } ';
            }
            // Update the minute part
            if ($m)
            {
                if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
                $reset_scripts .= 'jQuery(\'#'.$prefix.'min\').val(\''.dol_print_date(dol_now(),'%M').'\');';
                if ($fullday) $reset_scripts .= ' } ';
            }
            // If reset_scripts is not empty, print the link with the reset_scripts in the onClick
            if ($reset_scripts && empty($conf->dol_optimize_smallscreen))
            {
                $retstring.=' <button class="dpInvisibleButtons datenowlink" id="'.$prefix.'ButtonPlusOne" type="button" name="_useless2" value="plusone" onClick="'.$reset_scripts.'">';
                $retstring.=$langs->trans("DateStartPlusOne");
                $retstring.='</button> ';
            }
        }

        // Add a "Plus one hour" link
        if ($conf->use_javascript_ajax && $adddateof)
        {
            $tmparray=dol_getdate($adddateof);
            $retstring.=' - <button class="dpInvisibleButtons datenowlink" id="dateofinvoice" type="button" name="_dateofinvoice" value="now" onclick="jQuery(\'#re\').val(\''.dol_print_date($adddateof,'day').'\');jQuery(\'#reday\').val(\''.$tmparray['mday'].'\');jQuery(\'#remonth\').val(\''.$tmparray['mon'].'\');jQuery(\'#reyear\').val(\''.$tmparray['year'].'\');">'.$langs->trans("DateInvoice").'</a>';
        }

        if (! empty($nooutput)) return $retstring;

        print $retstring;
        return;
    }

    /**
     *  Return HTML code to select a company.
     *
     *  @param      int         $selected               Preselected products
     *  @param      string      $htmlname               Name of HTML select field (must be unique in page)
     *  @param      int         $filter                 Filter on thirdparty
     *  @param      int         $limit                  Limit on number of returned lines
     *  @param      array       $ajaxoptions            Options for ajax_autocompleter
     *  @param      int         $forcecombo             Force to use combo box
     *  @return     string                              Return select box for thirdparty.
     *  @deprecated 3.8 Use select_company instead. For exemple $form->select_thirdparty(GETPOST('socid'),'socid','',0) => $form->select_company(GETPOST('socid'),'socid','',1,0,0,array(),0)
     */
    function select_thirdpartyadd($selected='', $htmlname='socid', $filter='', $limit=20, $ajaxoptions=array(), $forcecombo=0)
    {
        return $this->select_thirdparty_listadd($selected,$htmlname,$filter,1,0,$forcecombo,$ajaxoptions,'',0,$limit,' form-control');
    }


    /**
     *  Output html form to select a third party
     *
     *  @param  string  $selected       Preselected type
     *  @param  string  $htmlname       Name of field in form
     *  @param  string  $filter         optional filters criteras (example: 's.rowid <> x', 's.client in (1,3)')
     *  @param  string  $showempty      Add an empty field (Can be '1' or text to use on empty line like 'SelectThirdParty')
     *  @param  int     $showtype       Show third party type in combolist (customer, prospect or supplier)
     *  @param  int     $forcecombo     Force to use combo box
     *  @param  array   $events         Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
     *  @param  string  $filterkey      Filter on key value
     *  @param  int     $outputmode     0=HTML select string, 1=Array
     *  @param  int     $limit          Limit number of answers
     *  @param  string  $morecss        Add more css styles to the SELECT component
     *  @param  string  $moreparam      Add more parameters onto the select tag. For example 'style="width: 95%"' to avoid select2 component to go over parent container
     *  @return string                  HTML string with
     */
    function select_thirdparty_listadd($selected='',$htmlname='socid',$filter='',$showempty='', $showtype=0, $forcecombo=0, $events=array(), $filterkey='', $outputmode=0, $limit=0, $morecss='minwidth100', $moreparam='')
    {
        global $conf,$user,$langs;

        $out=''; $num=0;
        $outarray=array();

        // On recherche les societes
        $sql = "SELECT s.rowid, s.nom as name, s.name_alias, s.client, s.fournisseur, s.code_client, s.code_fournisseur";
        $sql.= " FROM ".MAIN_DB_PREFIX ."societe as s";
        if (!$user->rights->societe->client->voir && !$user->societe_id) $sql .= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
        $sql.= " WHERE s.entity IN (".getEntity('societe', 1).")";
        if (! empty($user->societe_id)) $sql.= " AND s.rowid = ".$user->societe_id;
        if ($filter) $sql.= " AND (".$filter.")";
        if (!$user->rights->societe->client->voir && !$user->societe_id) $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
        if (! empty($conf->global->COMPANY_HIDE_INACTIVE_IN_COMBOBOX)) $sql.= " AND s.status <> 0";
        // Add criteria
        if ($filterkey && $filterkey != '')
        {
            $sql.=" AND (";
            if (! empty($conf->global->COMPANY_DONOTSEARCH_ANYWHERE))   // Can use index
            {
                $sql.="(s.name LIKE '".$this->db->escape($filterkey)."%')";
            }
            else
            {
                // For natural search
                $scrit = explode(' ', $filterkey);
                foreach ($scrit as $crit) {
                    $sql.=" AND (s.name LIKE '%".$this->db->escape($crit)."%')";
                }
            }
            if (! empty($conf->barcode->enabled))
            {
                $sql .= " OR s.barcode LIKE '".$this->db->escape($filterkey)."%'";
            }
            $sql.=")";
        }
        $sql.=$this->db->order("nom","ASC");
        if ($limit > 0) $sql.=$this->db->plimit($limit);

        dol_syslog(get_class($this)."::select_thirdparty_list", LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($conf->use_javascript_ajax && ! $forcecombo)
            {
                include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
                $comboenhancement =ajax_combobox($htmlname, $events, $conf->global->COMPANY_USE_SEARCH_TO_SELECT);
                $out.= $comboenhancement;
                $nodatarole=($comboenhancement?' data-role="none"':'');
            }

            // Construct $out and $outarray
            if (count($events)>0)
            {
                foreach ($events AS $j => $value)
                    $eventsmod .= ' '.$value;
            }
            $out.= '<select id="'.$htmlname.'" class="'.($morecss?' '.$morecss:'').'"'.($moreparam?' '.$moreparam:'').' name="'.$htmlname.'"'.$nodatarole.' '.$eventsmod.'>'."\n";

            $textifempty='';
            // Do not use textifempty = ' ' or '&nbsp;' here, or search on key will search on ' key'.
            //if (! empty($conf->use_javascript_ajax) || $forcecombo) $textifempty='';
            if (! empty($conf->global->COMPANY_USE_SEARCH_TO_SELECT))
            {
                if ($showempty && ! is_numeric($showempty)) $textifempty=$langs->trans($showempty);
                else $textifempty.=$langs->trans("All");
            }
            if ($showempty) $out.= '<option value="-1">'.$textifempty.'</option>'."\n";

            $num = $this->db->num_rows($resql);
            $i = 0;
            if ($num)
            {
                while ($i < $num)
                {
                    $obj = $this->db->fetch_object($resql);
                    $label='';
                    if ($conf->global->SOCIETE_ADD_REF_IN_LIST) {
                        if (($obj->client) && (!empty($obj->code_client))) {
                            $label = $obj->code_client. ' - ';
                        }
                        if (($obj->fournisseur) && (!empty($obj->code_fournisseur))) {
                            $label .= $obj->code_fournisseur. ' - ';
                        }
                        $label.=' '.$obj->name;
                    }
                    else
                    {
                        $label=$obj->name;
                    }

                    if(!empty($obj->name_alias)) {
                        $label.=' ('.$obj->name_alias.')';
                    }

                    if ($showtype)
                    {
                        if ($obj->client || $obj->fournisseur) $label.=' (';
                        if ($obj->client == 1 || $obj->client == 3) $label.=$langs->trans("Customer");
                        if ($obj->client == 2 || $obj->client == 3) $label.=($obj->client==3?', ':'').$langs->trans("Prospect");
                        if ($obj->fournisseur) $label.=($obj->client?', ':'').$langs->trans("Supplier");
                        if ($obj->client || $obj->fournisseur) $label.=')';
                    }
                    if ($selected > 0 && $selected == $obj->rowid)
                    {
                        $out.= '<option value="'.$obj->rowid.'" selected>'.$label.'</option>';
                    }
                    else
                    {
                        $out.= '<option value="'.$obj->rowid.'">'.$label.'</option>';
                    }

                    array_push($outarray, array('key'=>$obj->rowid, 'value'=>$obj->rowid, 'label'=>$label));

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

        $this->result=array('nbofthirdparties'=>$num);

        if ($outputmode) return $outarray;
        return $out;
    }
    /**
     *    Return a HTML area with the reference of object and a navigation bar for a business object
     *    To add a particular filter on select, you must set $object->next_prev_filter to SQL criteria.
     *
     *    @param    object  $object         Object to show
     *    @param    string  $paramid        Name of parameter to use to name the id into the URL next/previous link
     *    @param    string  $morehtml       More html content to output just before the nav bar
     *    @param    int     $shownav        Show Condition (navigation is shown if value is 1)
     *    @param    string  $fieldid        Nom du champ en base a utiliser pour select next et previous (we make the select max and min on this field)
     *    @param    string  $fieldref       Nom du champ objet ref (object->ref) a utiliser pour select next et previous
     *    @param    string  $morehtmlref    More html to show after ref
     *    @param    string  $moreparam      More param to add in nav link url.
     *    @param    int     $nodbprefix     Do not include DB prefix to forge table name
     *    @param    string  $morehtmlleft   More html code to show before ref
     *    @param    string  $morehtmlright  More html code to show before navigation arrows
     *    @return   string                  Portion HTML avec ref + boutons nav
     */
    function showrefnavpoa($object,$paramid,$morehtml='',$shownav=1,$fieldid='rowid',$fieldref='ref',$morehtmlref='',$moreparam='',$nodbprefix=0,$morehtmlleft='',$morehtmlright='')
    {
        global $langs,$conf;

        $ret='';
        if (empty($fieldid))  $fieldid='rowid';
        if (empty($fieldref)) $fieldref='ref';

        //print "paramid=$paramid,morehtml=$morehtml,shownav=$shownav,$fieldid,$fieldref,$morehtmlref,$moreparam";
        $object->load_previous_next_ref((isset($object->next_prev_filter)?$object->next_prev_filter:''),$fieldid,$nodbprefix);

        //$previous_ref = $object->ref_previous?'<a data-role="button" data-icon="arrow-l" data-iconpos="left" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_previous).$moreparam.'">'.(empty($conf->dol_use_jmobile)?img_picto($langs->trans("Previous"),'previous.png'):'&nbsp;').'</a>':'';
        //$next_ref     = $object->ref_next?'<a data-role="button" data-icon="arrow-r" data-iconpos="right" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_next).$moreparam.'">'.(empty($conf->dol_use_jmobile)?img_picto($langs->trans("Next"),'next.png'):'&nbsp;').'</a>':'';
        $previous_ref = $object->ref_previous?'<a data-role="button" data-icon="arrow-l" data-iconpos="left" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_previous).$moreparam.'">'.(empty($conf->dol_use_jmobile)?'&lt;':'&nbsp;').'</a>':'';
        $next_ref     = $object->ref_next?'<a data-role="button" data-icon="arrow-r" data-iconpos="right" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_next).$moreparam.'">'.(empty($conf->dol_use_jmobile)?'&gt;':'&nbsp;').'</a>':'';

        //print "xx".$previous_ref."x".$next_ref;
        $ret.='<div style="vertical-align: middle">';

        $ret.='<div class="inline-block floatleft">'.$morehtmlleft.'</div>';

        $ret.='<div class="inline-block floatleft valignmiddle refid'.(($shownav && ($previous_ref || $next_ref))?' refidpadding':'').'">';

        // For thirdparty and contact, the ref is the id, so we show something else
        if ($object->element == 'societe')
        {
            $ret.=dol_htmlentities($object->name);
        }
        else if (in_array($object->element, array('contact', 'user', 'member')))
        {
            if ($shownav)
            {
                $ret.= $morehtml;
            }
            else
                $ret.=dol_htmlentities($object->getFullName($langs));
        }
        else $ret.=dol_htmlentities($object->$fieldref);
        if ($morehtmlref)
        {
            $ret.=' '.$morehtmlref;
        }
        $ret.='</div>';

        if ($previous_ref || $next_ref || $morehtml)
        {
            $ret.='<div class="pagination"><ul>';
        }
        if ($morehtml)
        {
        //    $ret.='<li class="noborder litext">'.$morehtml.'</li>';
        }
        if ($shownav && ($previous_ref || $next_ref))
        {
        //    $ret.='<li class="pagination">'.$previous_ref.'</li>';
        //    $ret.='<li class="pagination">'.$next_ref.'</li>';
        }
        if ($previous_ref || $next_ref || $morehtml)
        {
            $ret.='</ul></div>';
        }
        if ($morehtmlright) $ret.='<div class="statusref">'.$morehtmlright.'</div>';
        $ret.='</div>';

        return $ret;
    }

}
?>