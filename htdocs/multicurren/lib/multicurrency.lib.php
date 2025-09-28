<?php
/* Copyright (C) 2013 Ramiro Queso  <ramiro@ubuntu-bo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * or see http://www.gnu.org/
 */

/**
 *	\file       projetmonitoring/lib/projetmonitoring.lib.php
 *	\brief      Ensemble de fonctions de base pour le module Project Monitoring
 * 	\ingroup	projetmonitoring
 */

function dv($r)
{
  $s=1;
  for($m=0;$r!=0;$r/=10)
    $s=($s+$r%10*(9-$m++%6))%11;
  // echo 'El digito verificador del rut ingresado es '.chr($s?$s+47:75);
  return chr($s?$s+47:75);
}

function select_registry($selected='',$htmlname='registry',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
  global $conf,$langs;
  
  $langs->load("wages@wages");
  
  $out='';
  $countryArray=array();
  $label=array();
  $i = 1;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Dialy');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Monthly');
  $label[$i] = $countryArray[$i]['label'];

  if ($showLabel)
    return $countryArray[$selected]['label'];
  $out = print_select($selected,$htmlname,$htmloption,$maxlength,
		      $showempty,$showLabel,$countryArray,$label);

  return $out;
}

function select_mes($nMes)
{
  $nMes = $nMes * 1;
  switch ($nMes)
    {
    case 1:
      $cMes = 'Enero';
      break;
    case 2:
      $cMes = 'Febrero';
      break;
    case 3:
      $cMes = 'Marzo';
      break;
    case 4:
      $cMes = 'Abril';
      break;
    case 5:
      $cMes = 'Mayo';
      break;
    case 6:
      $cMes = 'Junio';
      break;
    case 7:
      $cMes = 'Julio';
      break;
    case 8:
      $cMes = 'Agosto';
      break;
    case 9:
      $cMes = 'Septiembre';
      break;
    case 10:
      $cMes = 'Octubre';
      break;
    case 11:
      $cMes = 'Noviembre';
      break;
    case 12:
      $cMes = 'Diciembre';
      break;
    otherwise:
      $cMes = $nMes;
    }
  return $cMes;
}
function select_cta_normal($selected='',$htmlname='cta_normal',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
  global $conf,$langs;
  
  $langs->load("salary@salary");
  
  $out='';
  $countryArray=array();
  $label=array();
  $i = 1;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Deudor');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Acreedor');
  $label[$i] = $countryArray[$i]['label'];
  if ($showLabel)
    return $countryArray[$selected]['label'];
  $out = print_select($selected,$htmlname,$htmloption,$maxlength,
		      $showempty,$showLabel,$countryArray,$label);

  return $out;
}

function select_sex($selected='',$htmlname='sex',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
  global $conf,$langs;
  
  $langs->load("salary@salary");
  
  $out='';
  $countryArray=array();
  $label=array();
  $i = 1;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Male');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Female');
  $label[$i] = $countryArray[$i]['label'];
  if ($showLabel)
    return $countryArray[$selected]['label'];
  $out = print_select($selected,$htmlname,$htmloption,$maxlength,
		      $showempty,$showLabel,$countryArray,$label);

  return $out;
}

function select_typevalue($selected='',$htmlname='type_value',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
  global $conf,$langs;
  
  $langs->load("salary@salary");
  
  $out='';
  $countryArray=array();
  $label=array();
  $i = 1;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('From');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('To');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Result');
  $label[$i] = $countryArray[$i]['label'];

  if ($showLabel)
    return $countryArray[$selected]['label'];
  $out = print_select($selected,$htmlname,$htmloption,$maxlength,
		      $showempty,$showLabel,$countryArray,$label);

  return $out;
}

function select_typecod($selected='',$htmlname='type_cod',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
  global $conf,$langs;
  
  $langs->load("salary@salary");
  
  $out='';
  $countryArray=array();
  $label=array();
  $i = 1;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Performance').' ('.$langs->trans('Perception').')';
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Discount').' ('.$langs->trans('Deduction').')';
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Base Performance');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Base Discount');
  $label[$i] = $countryArray[$i]['label'];

  if ($showLabel)
    return $countryArray[$selected]['label'];
  $out = print_select($selected,$htmlname,$htmloption,$maxlength,
		      $showempty,$showLabel,$countryArray,$label);

  return $out;
}

function select_month($selected='',$htmlname='mes',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
  global $conf,$langs;
  
  $langs->load("salary@salary");
  
  $out='';
  $countryArray=array();
  $label=array();
  $i = 1;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('January');
  $label[$i] = $countryArray[$i]['rowid'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('February');
  $label[$i] = $countryArray[$i]['rowid'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('March');
  $label[$i] = $countryArray[$i]['rowid'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('April');
  $label[$i] = $countryArray[$i]['rowid'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('May');
  $label[$i] = $countryArray[$i]['rowid'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('June');
  $label[$i] = $countryArray[$i]['rowid'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('July');
  $label[$i] = $countryArray[$i]['rowid'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('August');
  $label[$i] = $countryArray[$i]['rowid'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Septembre');
  $label[$i] = $countryArray[$i]['rowid'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('October');
  $label[$i] = $countryArray[$i]['rowid'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('November');
  $label[$i] = $countryArray[$i]['rowid'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('December');
  $label[$i] = $countryArray[$i]['rowid'];

  if ($showLabel)
    return $countryArray[$selected]['label'];
  $out = print_select($selected,$htmlname,$htmloption,$maxlength,
		      $showempty,$showLabel,$countryArray,$label);

  return $out;
}

function select_typemov($selected='',$htmlname='type_mov',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
  global $conf,$langs;
  
  $langs->load("salary@salary");
  
  $out='';
  $countryArray=array();
  $label=array();
  $i = 1;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Hours');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Value');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Days');
  $label[$i] = $countryArray[$i]['label'];
  $i++;

  if ($showLabel)
    return $countryArray[$selected]['label'];
  $out = print_select($selected,$htmlname,$htmloption,$maxlength,
		      $showempty,$showLabel,$countryArray,$label);

  return $out;
}

function select_yesno($selected='',$htmlname='print',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
  global $conf,$langs;
  $langs->load("salary@salary");
  
  $out='';
  $countryArray=array();
  $label=array();
  $i = 1;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Yes');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Not');
  $label[$i] = $countryArray[$i]['label'];
  $i++;

  if ($showLabel)
    return $countryArray[$selected]['label'];
  
  $out = print_select($selected,$htmlname,$htmloption,$maxlength,
		      $showempty,$showLabel,$countryArray,$label);
  return $out;
}

function select_andor($selected='',$htmlname='andor',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
  global $conf,$langs;
  $langs->load("salary@salary");
  
  $out='';
  $countryArray=array();
  $label=array();
  $i = 1;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('And');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Or');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('End');
  $label[$i] = $countryArray[$i]['label'];

  if ($showLabel)
    return $countryArray[$selected]['label'];
  
  $out = print_select($selected,$htmlname,$htmloption,$maxlength,
		      $showempty,$showLabel,$countryArray,$label);
  return $out;
}

function select_typeapprov($selected='',$htmlname='type',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
  global $conf,$langs;
  $langs->load("salary@salary");
  
  $out='';
  $countryArray=array();
  $label=array();
  $i = 1;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Employee');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Charge');
  $label[$i] = $countryArray[$i]['label'];
  $i++;

  if ($showLabel)
    return $countryArray[$selected]['label'];
  
  $out = print_select($selected,$htmlname,$htmloption,$maxlength,
		      $showempty,$showLabel,$countryArray,$label);
  return $out;
}

function select_incometax($selected='',$htmlname='income_tax',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
  global $conf,$langs;
  $langs->load("salary@salary");
  
  $out='';
  $countryArray=array();
  $label=array();
  $i = 1;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = '100% '.$langs->trans('Recorded');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = '100% '.$langs->trans('Exempt');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Not applicable IR');
  $label[$i] = $countryArray[$i]['label'];
  $i++;

  if ($showLabel)
    return $countryArray[$selected]['label'];
  
  $out = print_select($selected,$htmlname,$htmloption,$maxlength,
		      $showempty,$showLabel,$countryArray,$label);
  return $out;
}

function print_select($selected='',$htmlname='status',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0,$countryArray,$label)
{

  $out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
  if ($showempty)
    {
      $out.= '<option value="-1"';
      if ($selected == -1) $out.= ' selected="selected"';
      $out.= '>&nbsp;</option>';
    }

  array_multisort($label, SORT_ASC, $countryArray);
  
  foreach ($countryArray as $row)
    {
      //print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
      if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['label']) )
	{
	  $foundselected=true;
	  $out.= '<option value="'.$row['rowid'].'" selected="selected">';
	}
      else
	{
	  $out.= '<option value="'.$row['rowid'].'">';
	}
      $out.= dol_trunc($row['label'],$maxlength,'middle');
      $out.= '</option>';
    }
  $out.= '</select>';

  return $out;
}

function salary_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('salary@salary');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/adherents/fiche.php?rowid=".$object->id,1);
	$head[$h][1] = $langs->trans("Ficha miembro");
	$head[$h][2] = 'Ficha miembro';
	$h++;

	$head[$h][0] = dol_buildpath("/adherents/card_subscriptions.php?rowid=".$object->id,1);
	$head[$h][1] = $langs->trans("Afiliaciones");
	$head[$h][2] = 'Afiliaciones';
	$h++;

	$head[$h][0] = dol_buildpath("/adherents/agenda.php?rowid=".$object->id,1);
	$head[$h][1] = $langs->trans("Agenda");
	$head[$h][2] = 'Agenda';
	$h++;

	$head[$h][0] = dol_buildpath("/categories/categorie.php?id=".$object->id."&type=3",1);
	$head[$h][1] = $langs->trans("Categorias");
	$head[$h][2] = 'Categorias';
	$h++;



    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'member');

	$head[$h][0] = dol_buildpath("/adherents/note.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Note");
	$head[$h][2] = 'Note';
	$h++;

	$head[$h][0] = dol_buildpath("/adherents/document.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Documents");
	$head[$h][2] = 'Documents';
	$h++;

	$head[$h][0] = dol_buildpath("/adherents/info.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Log");
	$head[$h][2] = 'Log';
	$h++;

	return $head;
}

function concept_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('salary@salary');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/salary/concept/fiche.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Ficha");
	$head[$h][2] = 'Ficha';
	$h++;

	$head[$h][0] = dol_buildpath("/salary/concept/incident.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Incidents");
	$head[$h][2] = 'Incidents';
	$h++;


	return $head;
}

function listColumn($table,$conf_db_type)
{
  global $db;
  $arrayTable = array();
  If ($conf_db_type == 'pgsql')
    {
      $cQuery = "SELECT c.column_name AS column_name, c.data_type,c.is_nullable AS nullable";
      $cQuery.=" FROM information_schema.columns c LEFT JOIN information_schema.element_types e ON ";
      $cQuery.= " c.table_catalog = e.object_catalog AND c.table_schema = e.object_schema AND ";
      $cQuery.= " c.table_name = e.object_name AND 'TABLE' = e.object_type ";
      $cQuery.= " WHERE UPPER(c.table_name) = upper( '$table' ) ";
      $cQuery.= " ORDER BY c.ordinal_position ";
      $resql=$db->query($cQuery);
      $num = $db->num_rows($cQuery);
      $i = 0;
      if ($num)
	{
	  $foundselected=false;      
	  while ($i < $num)
	    {
	      $obj = $db->fetch_object($resql);
	      $arrayTable[$obj->column_name] = 
		array('column_name' => $obj->column_name,
		      'data_type' => $obj->data_type,
		      'nullable' => $obj->nullable);
	      $i++;
	    }
	}
    }
  If ($conf_db_type == 'mysql')
    {
      $arrayTable = obtieneCampo($table);
    }
  return $arrayTable;
}

function obtieneCampo ( $tabla ) {
  global $db;
  $arrayTable = array();
  $cQuery = "
	 SHOW FULL FIELDS FROM
	  $tabla
	 ";
  $resql = $db->query($cQuery);

  $num = $db->num_rows($cQuery);
  $i = 0;
  if ($num)
    {
      while ($i < $num)
	{
	  $obj = $db->fetch_object($resql);
	  $arrayTable[$obj->Field] = array(
					   'column_name' => $obj->Field,
					   'data_type' => $obj->Type,
					   'nullable' => $obj->Null);
	  $i++;
	}
    }
  return $arrayTable;
}

function select_generic($resql,$showempty='',$htmlname='',$htmloption='',$campo='',$selected='')
{
  global $db,$langs,$conf;
  $out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
  if ($showempty)
    {
      $out.= '<option value="-1"';
      if ($selected == -1) $out.= ' selected="selected"';
      $out.= '>&nbsp;</option>';
    }
  $num = $db->num_rows($resql);
  $i = 0;
  if ($num)
    {
      $foundselected=false;
      
      while ($i < $num)
	{
	  $obj = $db->fetch_object($resql);
	  $countryArray[$i]['rowid'] 		= $obj->rowid;
	  $countryArray[$i]['code_iso'] 	= $obj->code_iso;
	  $countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv($campo.$obj->code_iso)!=$campo.$obj->code_iso?$langs->transnoentitiesnoconv($campo.$obj->code_iso):($obj->label!='-'?$obj->label:''));
	  $label[$i] 	= $countryArray[$i]['label'];
	  $i++;
	}
      
      array_multisort($label, SORT_ASC, $countryArray);
      
      foreach ($countryArray as $row)
	{
	  //print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
	  if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
	    {
	      $foundselected=true;
	      $out.= '<option value="'.$row['rowid'].'" selected="selected">';
	    }
	  else
	    {
	      $out.= '<option value="'.$row['rowid'].'">';
	    }
	  $out.= dol_trunc($row['label'],$maxlength,'middle');
	  if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
	  $out.= '</option>';
	}
    }
  $out.= '</select>';
  return $out;
}

//select departament
function select_departament($selected='',$country_codeid=0, $htmlname='issued_in')
{
  if(empty($country_codeid)) $country_codeid = 0;
  print select_regiondep($selected,$country_codeid, $htmlname);
}

function select_regiondep($selected='',$country=0,$htmlname='issued_in')
{
  global $conf,$langs,$db;
  $langs->load("dict");
  
  $sql = "SELECT r.rowid, r.code_region as code, r.nom as libelle, r.active, p.code as country_code, p.libelle as country FROM ".MAIN_DB_PREFIX."c_regions as r, ".MAIN_DB_PREFIX."c_pays as p";
  $sql.= " WHERE r.fk_pays=p.rowid AND r.active = 1 and p.active = 1";
  $sql.= " AND p.rowid = ".$country;
  $sql.= " ORDER BY p.code, p.libelle ASC";
  
  //  dol_syslog(get_class($this)."::select_region sql=".$sql);
  $resql=$db->query($sql);
  if ($resql)
    {
      print '<select class="flat" name="'.$htmlname.'">';
      $num = $db->num_rows($resql);
      $i = 0;
      if ($num)
	{
	  $country='';
	  while ($i < $num)
	    {
	      $obj = $db->fetch_object($resql);
	      if ($obj->code == 0) {
		print '<option value="0">&nbsp;</option>';
	      }
	      else {
		if ($country == '' || $country != $obj->country)
		  {
		    // Show break
		    $key=$langs->trans("Country".strtoupper($obj->country_code));
		    $valuetoshow=($key != "Country".strtoupper($obj->country_code))?$obj->country_code." - ".$key:$obj->country;
		    print '<option value="-1" disabled="disabled">----- '.$valuetoshow." -----</option>\n";
		    $country=$obj->country;
		  }
		
		if ($selected > 0 && $selected == $obj->code)
		  {
		    print '<option value="'.$obj->code.'" selected="selected">'.$obj->libelle.'</option>';
		  }
		else
		  {
		    print '<option value="'.$obj->code.'">'.$obj->libelle.'</option>';
		  }
	      }
	      $i++;
	    }
	}
      print '</select>';
    }
  else
    {
      dol_print_error($db);
    }
}

/**
 *  Affiche formulaire de selection des p_civility
 *
 *  @param	int		$page        	Page
 *  @param  int		$selected    	Id or code preselected
 *  @param  string	$htmlname   	Nom du formulaire select
 *	@param	int		$empty			Add empty value in list
 *	@return	void
 */
function form_p_civility($selected='', $htmlname='state_marital', $empty=0)
{
  global $langs,$db;
  
  print '<select class="flat" name="'.$htmlname.'">';
  if ($empty) print '<option value="">&nbsp;</option>';
  
  //dol_syslog(get_class($this).'::form_prospect_level',LOG_DEBUG);
  $sql = "SELECT code, label";
  $sql.= " FROM ".MAIN_DB_PREFIX."p_civility";
  $sql.= " WHERE active > 0";
  $sql.= " ORDER BY label";
  $resql = $db->query($sql);
  if ($resql)
    {
      $num = $db->num_rows($resql);
      $i = 0;
      while ($i < $num)
	{
	  $obj = $db->fetch_object($resql);
	  
	  print '<option value="'.$obj->code.'"';
	  if ($selected == $obj->code) print ' selected="selected"';
	  print '>';
	  $level=$langs->trans($obj->code);
	  if ($level == $obj->code) $level=$langs->trans($obj->label);
	  print $level;
	  print '</option>';
	  
	  $i++;
	}
    }
  else dol_print_error($db);
  print '</select>';
}

/**
 *  Affiche formulaire de selection des p_blood_type
 *
 *  @param	int		$page        	Page
 *  @param  int		$selected    	Id or code preselected
 *  @param  string	$htmlname   	Nom du formulaire select
 *	@param	int		$empty			Add empty value in list
 *	@return	void
 */
function form_p_blood($selected='', $htmlname='blood_type', $empty=0)
{
  global $langs,$db;
  
  print '<select class="flat" name="'.$htmlname.'">';
  if ($empty) print '<option value="">&nbsp;</option>';
  
  //dol_syslog(get_class($this).'::form_prospect_level',LOG_DEBUG);
  $sql = "SELECT rowid, code, label";
  $sql.= " FROM ".MAIN_DB_PREFIX."p_blood_type";
  $sql.= " WHERE active > 0";
  $sql.= " ORDER BY label";
  $resql = $db->query($sql);
  if ($resql)
    {
      $num = $db->num_rows($resql);
      $i = 0;
      while ($i < $num)
	{
	  $obj = $db->fetch_object($resql);
	  print '<option value="'.$obj->code.'"';
	  if ($selected == $obj->code) print ' selected="selected"';
	  print '>';
	  $level=$langs->trans($obj->code);
	  if ($level == $obj->code) $level=$langs->trans($obj->label);
	  print $level;
	  print '</option>';
	  
	  $i++;
	}
    }
  else dol_print_error($db);
  print '</select>';
}

function select_depto($selected='',$htmlname='fk_departament',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0,$campo='nom',$fk_region='')
{
  global $conf,$langs,$db;
  
  $langs->load("salary@salary");

  $out = '';
  $out.= '<select class="flat" name="'.$htmlname.'">';
  if ($empty) $out.= '<option value="">&nbsp;</option>';

  //dol_syslog(get_class($this).'::form_prospect_level',LOG_DEBUG);
  $sql = "SELECT rowid, nom AS label, code_departement AS code";
  $sql.= " FROM ".MAIN_DB_PREFIX."c_departements";
  $sql.= " WHERE active > 0";
  if (!empty($fk_region))
    $sql.= " AND fk_region = ".$fk_region;
   $sql.= " ORDER BY nom";
  $resql = $db->query($sql);
  if ($resql)
    {
      $num = $db->num_rows($resql);
      $i = 0;
      while ($i < $num)
	{
	  $obj = $db->fetch_object($resql);
	  if ($showLabel > 0 )
	    {
	      if ($obj->rowid == $selected)
		return $obj->$campo;
	      else
		return '';
	    }
	  else
	    {
	      $out.= '<option value="'.$obj->rowid.'"';
	      if ($selected == $obj->rowid) $out.= ' selected="selected"';
	      $out.= '>';
	      $level=trim($langs->trans($obj->code).' '.$langs->trans($obj->label));
	      if ($level == $obj->code) $level=$langs->trans($obj->label);
	      $out.= $level;
	      $out.= '</option>';
	    }	  
	  $i++;
	}
    }
  else dol_print_error($db);
  print '</select>';
  return $out;
}

/**
 *	Return label of statut generico /validate/no validate
 *
 *	@param		int		$state      	Id state
 *	@param      int		$mode        	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
 *  @return     string					Label of statut
 */
function LibState($statut,$mode)
{
  global $langs;
  //print 'x'.$statut.'-'.$facturee;
  if ($mode == 0)
    {
      if ($statut==-1) return $langs->trans('StatusCanceled');
      if ($statut==0) return $langs->trans('StatusDraft');
      if ($statut==1) return $langs->trans('StatusValidated');
    }
  elseif ($mode == 1)
    {
      if ($statut==-1) return $langs->trans('StatusCanceled');
      if ($statut==0) return $langs->trans('StatusDraft');
      if ($statut==1) return $langs->trans('StatusValidated');
    }
  elseif ($mode == 2)
    {
      if ($statut==-1) return img_picto($langs->trans('StatusCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceledShort');
      if ($statut==0) return img_picto($langs->trans('StatusDraft'),'statut0').' '.$langs->trans('StatusOrderDraftShort');
      if ($statut==1) return img_picto($langs->trans('StatusValidated'),'statut1').' '.$langs->trans('StatusOrderValidatedShort');
      if ($statut==2) return img_picto($langs->trans('StatusSent'),'statut3').' '.$langs->trans('StatusOrderSentShort');
      if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBillShort');
      if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('StatusOrderProcessedShort');
    }
  elseif ($mode == 3)
    {
      if ($statut==-1) return img_picto($langs->trans('StatusCanceled'),'statut5');
      if ($statut==0) return img_picto($langs->trans('StatusDraft'),'statut0');
      if ($statut==1) return img_picto($langs->trans('StatusValidated'),'statut1');
      if ($statut==2) return img_picto($langs->trans('StatusSentShort'),'statut3');
      if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7');
      if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6');
    }
  elseif ($mode == 4)
    {
      if ($statut==-1) return $langs->trans('StatusCanceled').' '.img_picto($langs->trans('StatusCanceled'),'statut5');
      if ($statut==0) return $langs->trans('StatusDraft').' '.img_picto($langs->trans('StatusDraft'),'interrog');
      if ($statut==1) return $langs->trans('StatusValidated').' '.img_picto($langs->trans('StatusValidated'),'tick');
    }
  elseif ($mode == 5)
    {
      if ($statut==-1) return $langs->trans('StatusCanceled').' '.img_picto($langs->trans('StatusCanceled'),'statut5');
      if ($statut==0) return $langs->trans('StatusDraft').' '.img_picto($langs->trans('StatusDraft'),'statut0');
      if ($statut==1) return $langs->trans('StatusValidated').' '.img_picto($langs->trans('StatusValidated'),'statut1');
    }
}

//opcion para subir archivos
//tres tipos id, login, documento

function select_updoc($selected='',$htmlname='docum',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
  global $conf,$langs;
  
  $langs->load("salary@salary");
  
  $out='';
  $countryArray=array();
  $label=array();
  $i = 1;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Id');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Login');
  $label[$i] = $countryArray[$i]['label'];
  $i++;
  $countryArray[$i]['rowid'] = $i;
  $countryArray[$i]['label'] = $langs->trans('Docum');
  $label[$i] = $countryArray[$i]['label'];
  if ($showLabel)
    return $countryArray[$selected]['label'];
  $out = print_select($selected,$htmlname,$htmloption,$maxlength,
		      $showempty,$showLabel,$countryArray,$label);

  return $out;
}

function regHistory()
{
  global $db, $conf, $user;
  dol_include_once('/salaries/class/phistory.class.php');
  $objHistory = new Phistory($db);
  $objHistory->initAsSpecimen();
  $mesg = '';
  $objHistory->entity = $conf->entity;
  $objHistory->fk_user = $user->id;
  $objHistory->url_visit = substr($_SERVER['REQUEST_URI'],1,200);
  $objHistory->date_visit = date('YmdHis');
  $objHistory->tms = date('YmdHis');
  $db->begin();
  $result = $objHistory->create($user);
  If ($result < 0)
    {
      $mesg='<div class="error">'.$objHistory->error.'</div>';
      $db->rollback();
    }
  else
    $db->commit();

  return $mesg;
}
?> 