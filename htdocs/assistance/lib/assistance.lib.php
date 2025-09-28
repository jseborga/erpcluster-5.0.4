<?php

function select_type_licence($selected='',$htmlname='code',$htmloption='',$showempty=0,$showlabel=0,$campo='code',$label='label')
{
	global $db, $langs, $conf,$user;
	$sql = "SELECT f.rowid, f.code, f.label FROM ".MAIN_DB_PREFIX."c_type_licence AS f ";
	$sql.= " WHERE ";
	$sql.= " f.entity = ".$conf->entity;
	$sql.= " AND f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';
  //echo '<br>sel '.$selected;
	if ($selected <> 0 && $selected == '-1')
	{
		if ($showlabel > 0)
		{
			return $langs->trans('To be defined');
		}
	}

	if ($resql)
	{
		$html.= '<select class="flat" name="'.$htmlname.'" id="select'.$htmlname.'">';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->$campo)
				{
					$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->$label.'</option>';
					if ($showlabel)
					{
						return $obj->$label;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->$campo.'">'.$obj->$label.'</option>';
				}
				$i++;
			}
		}
		else
		{
			return '';
		}
		if ($showlabel)
			return '';
		$html.= '</select>';
		if ($user->admin) $html.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);

		return $html;
	}
}
function licence_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('assistance');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/assistance/licence/card.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Fiche");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath("/assistance/licence/log.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Log");
	$head[$h][2] = 'log';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'assistance');

	return $head;
}

function verifica_retraso($aMark,$aDate,$cont,$tolerancia=0,$fk_member=0)
{
	$aInput = array(1=>1,3=>3,5=>5,7=>7,9=>9,11=>11,13=>13,15=>15);
	$aOutput = array(2=>2,4=>4,6=>6,8=>8,10=>10,12=>12,14=>14,16=>16);
	$estado = 'normal';
	if ($aMark[$cont]>0)
	{
		$aDatemark = dol_getdate($aMark[$cont]);
		$timemark = convertTime2Seconds($aDatemark['hours'],$aDatemark['minutes'],$aDatemark['seconds']);
		$timereg = convertTime2Seconds($aDate['hours'],$aDate['minutes'],$aDate['seconds']);
		$timetol = convertTime2Seconds(0,$tolerancia,0);
		if ($aInput[$cont])
		{
			$timemark+= $timetol;
			if ($timereg > $timemark)
			{
				$estado = 'retraso';
				$retraso = $timereg - $timemark+$timetol;
			}
			else
				$retraso = 0;
			$hora = convertSecondToTime($retraso);
		}
		if ($aOutput[$cont])
		{
			if ($timereg < $timemark)
			{
				$estado = 'abandono';
				$retraso = $timemark-$timereg;
			}
			else
				$retraso = 0;
			$hora = convertSecondToTime($retraso);

		}
	}
	return array($estado,$hora,$retraso);
}


function verif_time_range($aMark,$aDate,$tolerancia=0,$fk_member)
{
	$aDatemark = $aMark;
	$timemark = convertTime2Seconds($aDatemark['hours'],$aDatemark['minutes'],$aDatemark['seconds']);
	$timereg = convertTime2Seconds($aDate['hours'],$aDate['minutes'],$aDate['seconds']);
	$min = ($timereg - $timemark)/60;
	$dif = $tolerancia - $min;
	if ($dif < 0) return false;
	else return true;
}

//$id = $id_member
function verif_type_marking($id,$wday)
{
	global $langs, $conf,$db;
	require_once DOL_DOCUMENT_ROOT.'/assistance/class/assistancedef.class.php';
	require_once DOL_DOCUMENT_ROOT.'/assistance/class/typemarkingext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/salary/class/puser.class.php';
	$objAssistancedef = new Assistancedef($db);
	$objTypemarking = new Typemarkingext($db);
	$objPuser = new Puser($db);

	$objPuser->fetch(0,$id);
	//verificamos el marcado definidio para el miembor o el por defecto
	$resdef = $objAssistancedef->fetch(0,$id);
	$type_marking = '';
	if ($resdef>0) $type_marking = $objAssistancedef->type_marking;
	$type_markingdef = $type_marking;
	//verificamos si el tipo de marcación de la fecha es fijo o no
	$lFixeddate = false;
	$sex = 0;
	$restype = $objTypemarking->fetchAll('','',0,0,array('statut'=>1),'AND'," AND t.fixed_date = ".$db->idate($date),true);
	if ($restype == 1)
	{
		$fk_typemarking = $objTypemarking->id;
		$lFixeddate = true;
		$type_marking = $objTypemarking->ref;
		if ($objTypemarking->sex >0) $sex = $objTypemarking->sex;
		//verificamos si le corresponde el horario especial
		if ($sex>0)
		{
			if ($objPuser->sex == -1)
				setEventMessages($langs->trans('Sexisnotdefined').' '.$objPuser->lastname.' '.$objPuser->lastnametwo.' '.$objPuser->firstname,null,'warnings');
			if ($objPuser->sex != $sex)
			{
				$lFixeddate = false;
				$type_marking=$type_markingdef;
			}
		}
	}

	if (!$lFixeddate)
	{
		//si no esta definidio un tipo de marcado se toma el definido por defecto
		if (empty($type_marking)) $type_marking = $conf->global->ASSISTANCE_MARK_DEFAULT;
		$restype = $objTypemarking->fetch(0,$type_marking);

		$fk_typemarking = $objTypemarking->id;
		$aDaytmp = explode(',',$objTypemarking->day_def);

		$aDay = array();
		foreach ((array) $aDaytmp AS $k => $value)
			$aDay[$value] = $value;
		//verfiicamos si corresponde el dia
		if (!$aDay[$wday])
		{
			//se toma el marcado por defecto
			$restype = $objTypemarking->fetch(0,$conf->global->ASSISTANCE_MARK_DEFAULT);
			$fk_typemarking = $objTypemarking->id;
			$type_marking = $conf->global->ASSISTANCE_MARK_DEFAULT;
		}
	}
	$nroMark = $objTypemarking->mark;
	$tolerancia = $objTypemarking->additional_time;
	if ($objAssistancedef->aditional_time) $tolerancia = $objAssistancedef->aditional_time;
	return array($fk_typemarking,$type_marking,$nroMark,$tolerancia,$lFixeddate);
}
?>