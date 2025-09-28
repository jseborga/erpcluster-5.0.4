<?php



class FormAdd extends Form
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
   	function select_item($selected='', $htmlname='ref', $filter='', $showempty=0, $showtype=0, $forcecombo=0, $events=array(), $limit=0,$required='')
   	{
   		return $this->select_item_list($selected, $htmlname, $filter, $showempty, $showtype, $forcecombo, $events, '', 0, $limit,$required);
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
   	function select_item_list($selected='',$htmlname='ref',$filter='',$showempty=0, $showtype=0, $forcecombo=0, $events=array(), $filterkey='', $outputmode=0, $limit=0, $required='')
   	{
   		global $conf,$user,$langs;
   		if (!empty($required))
   		{
   			$forcefocus = 1;
   			$required = ' required="required"';
   		}
   		$out=''; $num=0;
   		$outarray=array();
	//sql
   		$sql = "SELECT";
   		$sql.= " t.rowid,";

   		$sql.= " t.entity,";
   		$sql.= " t.ref,";
   		$sql.= " t.fk_user_create,";
   		$sql.= " t.fk_user_mod,";
   		$sql.= " t.fk_type_item,";
   		$sql.= " t.fk_unit,";
   		$sql.= " t.detail,";
   		$sql.= " t.especification,";
   		$sql.= " t.plane,";
   		$sql.= " t.date_create,";
   		$sql.= " t.date_mod,";
   		$sql.= " t.tms,";
   		$sql.= " t.status";    

   		$sql.= " FROM ".MAIN_DB_PREFIX."items as t";
   		$sql.= " WHERE t.entity IN (".getEntity().")";    
   		if ($filter) $sql.= " AND (".$filter.")";
   		if (! empty($conf->global->MONPROJET_HIDE_INACTIVE_IN_COMBOBOX)) $sql.= " AND t.status<>0 ";
	// Add criteria
   		if ($filterkey && $filterkey != '')
   		{
   			$sql.=" AND (";
   			if (! empty($conf->global->MONPROJET_DONOTSEARCH_ANYWHERE)) 
	  // Can use index
   			{
   				$sql.="(t.ref LIKE '".$this->db->escape($filterkey)."%')";
   			}
   			else
   			{
		// For natural search
   				$scrit = explode(' ', $filterkey);
   				foreach ($scrit as $crit)
   				{
   					$sql.=" AND (";
   					$sql.=" t.ref LIKE '%".$this->db->escape($crit)."%'";
   					$sql.= " OR t.detail LIKE '%".$this->db->escape($crit)."%'";
   					$sql.=")";
   				}
   			}
   			$sql.=")";
   		}
	//fin sql
   		$sql.=$this->db->order("t.detail","ASC");
   		if ($limit > 0) $sql.=$this->db->plimit($limit);
	
   		dol_syslog(get_class($this)."::select_item_list", LOG_DEBUG);
   		$resql=$this->db->query($sql);
   		if ($resql)
   		{
   			if (! empty($conf->use_javascript_ajax))
   			{
   				if (! empty($conf->global->MONPROJET_USE_SEARCH_TO_SELECT) && ! $forcecombo)
   				{
   					include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
   					$out.= ajax_combobox($htmlname, $events, $conf->global->MONPROJET_USE_SEARCH_TO_SELECT,$forcefocus);
   				}
   				else
   				{
   					if (count($events))
				// Add management of event
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
   		$out.= '<select id="'.$htmlname.'" class="flat" name="'.$htmlname.'" '.$required.'>'."\n";

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
   				$label=$obj->ref.' '.dol_trunc($obj->detail,90);
   				
		// if ($showtype)
		//   {
		//     if ($obj->client || $obj->fournisseur) $label.=' (';
		//     if ($obj->client == 1 || $obj->client == 3) $label.=$langs->trans("Customer");
		//     if ($obj->client == 2 || $obj->client == 3) $label.=($obj->client==3?', ':'').$langs->trans("Prospect");
		//     if ($obj->fournisseur) $label.=($obj->client?', ':'').$langs->trans("Supplier");
		//     if ($obj->client || $obj->fournisseur) $label.=')';
		//   }
   				if (!empty($selected) && STRTOUPPER(trim($selected)) == STRTOUPPER(trim($obj->ref)))
   				{
   					$out.= '<option value="'.$obj->ref.'" selected="selected">'.$label.'</option>';
   				}
   				else
   				{
   					$out.= '<option value="'.$obj->ref.'">'.$label.'</option>';
   				}
   				
   				array_push($outarray, array('key'=>$obj->ref, 'value'=>dol_trunc($obj->detail,70), 'label'=>dol_trunc($obj->detail,70)));
   				
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

   	$this->result=array('nbofitem'=>$num);

   	if ($outputmode) return $outarray;
   	return $out;
   }

}
?>