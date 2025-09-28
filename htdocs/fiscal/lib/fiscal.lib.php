<?php
//fiscal.lib.php

function fetch_type_facture($id,$ref=null)
{
	global $db,$conf,$langs;
	$sql = "SELECT t.rowid, t.code, t.label, t.detail, t.type_fact, t.nit_required, t.active";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_type_facture AS t";
	if ($ref!=null)
		$sql.= " WHERE t.code = '".trim($ref)."'";
	else
		$sql.= " WHERE t.rowid = ".$id;

	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		if ($num)
		{
			$obj = $db->fetch_object($resql);
			return $obj;
		}
		return 0;
	}
	return -1;
}
function fetch_type_tva($id,$ref=null)
{
	global $db,$conf,$langs;
	$sql = "SELECT rowid, code, label, active";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_type_tva";
	if ($ref!=null)
		$sql.= " WHERE code = '".trim($ref)."'";
	else
		$sql.= " WHERE rowid = ".$id;
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		if ($num)
		{
			$obj = $db->fetch_object($resql);
			return $obj;
		}
		return 0;
	}
	return -1;
}

function select_type_facture($selected='',$htmlname='fk_type_facture',$type=0,$htmloption='',$maxlength=0,$showempty=0,$campo='rowid',$loked=0)
{
	global $db,$conf,$langs;
	$sql = "SELECT t.rowid, t.code AS code_iso, t.label, t.detail, t.active";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_type_facture AS t";
	$sql.= " WHERE active = 1";
	if ($type !=9)
		$sql.= " AND type_fact = ".$type;
	$sql.= " ORDER BY label ASC";
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			$foundselected=false;
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$countryArray[$i]['rowid']    = $obj->rowid;
				$countryArray[$i]['code_iso']   = trim($obj->code_iso);
				$countryArray[$i]['label']    = trim($obj->label);
				$label[$i]  = $countryArray[$i]['label'];
				$i++;
			}
			$out = print_select($selected,$htmlname,$htmloption,$maxlength,$showempty,$countryArray,$campo,$loked,$label);
		}
	}
	return $out;
}

function print_select($selected='',$htmlname='status',$htmloption='',$maxlength=0,$showempty=0,$countryArray,$campo='rowid',$loked=0,$label='')
{
	if ($loked)
		$htmlloked = 'disabled="disabled"';
	$out.= '<select id="select'.$htmlname.'" class="flat form-control selectpays" name="'.$htmlname.'" '.$htmloption.' '.$htmlloked.'>';
	if ($showempty)
	{
		$out.= '<option value="-1"';
		if ($selected == -1) $out.= ' selected="selected"';
		$out.= '>&nbsp;</option>';
	}

	array_multisort($label, SORT_ASC, $countryArray);

	foreach ($countryArray as $row)
	{
		if ($selected && $selected != '-1' && ($selected == $row[$campo] || $selected == $row['label']) )
		{
			$foundselected=true;
			$out.= '<option value="'.$row[$campo].'" selected="selected">';
		}
		else
		{
			$out.= '<option value="'.$row[$campo].'">';
		}
		$out.= dol_trunc($row['label'],$maxlength,'middle');
		$out.= '</option>';
	}
	$out.= '</select>';

	return $out;
}

function select_yesno($selected='',$htmlname='yesno',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;
	$langs->load("almacen@almacen");

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

	if ($showLabel)
		return $countryArray[$selected]['label'];

	$out = print_select($selected,$htmlname,$htmloption,$maxlength,
		$showempty,$showLabel,$countryArray,$label);
	return $out;
}

function select_typebill($selected='',$htmlname='type',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;
	$langs->load("ventas@ventas");

	//TIPO DE DOSIFICACION
	//1=NF => Nota Fiscal (Factura)
	//2=NCI => Nota credito interno
	//3=RA => Recibo de Alquiler
	$out='';
	$countryArray=array();
	$label=array();
	$i = 1;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('NF');
	$label[$i] = $countryArray[$i]['label'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('NFS');
	$label[$i] = $countryArray[$i]['label'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('RA');
	$label[$i] = $countryArray[$i]['label'];
	$i++;

	if ($showLabel)
		return $countryArray[$selected]['label'];

	$out = print_selectv($selected,$htmlname,$htmloption,$maxlength,$showempty,$showLabel,$countryArray,$label);
	return $out;
}

function get_name_typebill($fk)
{
	global $conf,$langs;
	$aNametype = array(1=>$langs->trans('Invoicefiscal'),2=>$langs->trans('Internalcreditnote'),3=>$langs->trans('Rentreceipt'));
	return $aNametype[$fk];
}


function select_lotebill($selected='',$htmlname='type',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;
	$langs->load("ventas");

	$out='';
	$countryArray=array();
	$label=array();
	$i = 1;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Manual');
	$label[$i] = $countryArray[$i]['label'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Automatic');
	$label[$i] = $countryArray[$i]['label'];
	$i++;

	if ($showLabel)
		return $countryArray[$selected]['label'];

	$out = print_selectv($selected,$htmlname,$htmloption,$maxlength,
		$showempty,$showLabel,$countryArray,$label);
	return $out;
}

function print_selectv($selected='',$htmlname='status',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0,$countryArray,$label)
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
?>