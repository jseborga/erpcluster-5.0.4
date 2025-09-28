<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/mant/lib/mant.lib.php
 *	\ingroup    Librerias
 *	\brief      Page fiche mantenimiento
 */

function select_working_class($selected='',$htmlname='working_class',$htmloption='',$showempty=0,$showlabel=0)
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_working_class AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($selected <> 0 && $selected == '-1')
	{
		if ($showlabel > 0)
		{
			return $langs->trans('To be defined');
		}
	}

	if ($resql)
	{
		$html.= '<select class="flat" name="'.$htmlname.'">';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}
		if ($selected <> 0 && $selected == '-1')
		{
			$html.= '<option value="-1" selected="selected">'.$langs->trans('To be defined').'</option>';
			if ($showlabel)
			{
				return $langs->trans('To be defined');
			}
		}
		if (empty($selected) && $showlabel)
			return '';
	  // else
	  // 	$html.= '<option value="-1">'.$langs->trans('To be defined').'</option>';

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->code)
				{
					$html.= '<option value="'.$obj->code.'" selected="selected">'.$obj->libelle.'</option>';
					if ($showlabel)
					{
						return $obj->libelle;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->code.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		$html.= '</select>';
		if ($showlabel)
			return $langs->trans('To be defined');

		return $html;
	}
}

function select_speciality($selected='',$htmlname='fk_speciality',$htmloption='',$showempty=0,$showlabel=0,$campo='code',array $aColor=array())
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_especiality AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($selected <> 0 && $selected == '-1')
	{
		if ($showlabel > 0)
		{
			return $langs->trans('To be defined');
		}
	}

	if ($resql)
	{
		$html.= '<select class="flat" name="'.$htmlname.'">';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}
		if ($selected <> 0 && $selected == '-1')
		{
			$class = '';
			$html.= '<option value="-1" '.$class.' selected="selected">'.$langs->trans('To be defined').'</option>';
			if ($showlabel)
			{
				return $langs->trans('To be defined');
			}
		}
		if (empty($selected) && $showlabel)
			return '';

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$class = '';
				if (count($aColor)>0)
				{
					if ($aColor[$obj->$campo])
					{
						$class = ' class="selmark" ';
					}
				}
				if (!empty($selected) && $selected == $obj->$campo)
				{

					$html.= '<option '.$class.' value="'.$obj->$campo.'" selected="selected">'.$obj->libelle.'</option>';
					if ($showlabel)
					{
						return $obj->libelle;
					}
				}
				else
				{
					$html.= '<option '.$class.' value="'.$obj->$campo.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		$html.= '</select>';
		if ($showlabel)
			return $langs->trans('to be defined');
		return $html;
	}
}

function select_typemant($selected='',$htmlname='typemant',$htmloption='',$showempty=0,$showlabel=0,$required='')
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_typemant AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';
	if ($required)
		$required = 'required="required"';
	if ($resql)
	{
		$html.= '<select class="flat" name="'.$htmlname.'" '.$required.'>';
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
				if (!empty($selected) && $selected == $obj->code)
				{
					$html.= '<option value="'.$obj->code.'" selected="selected">'.$obj->libelle.'</option>';
					if ($showlabel)
					{
						return $obj->libelle;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->code.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		$html.= '</select>';
		if ($showlabel)
			return $langs->trans('to be defined');

		return $html;
	}
}

function select_frequency($selected='',$htmlname='frequency',$htmloption='',$showempty=0,$showlabel=0,$required='')
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_frequency AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';
	if ($required) $required = 'required="required"';

	if ($resql)
	{
		$html.= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'" '.$required.'>';

		if ($showempty) $html.= '<option value="0">&nbsp;</option>';

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->code)
				{
					$html.= '<option value="'.$obj->code.'" selected="selected">'.$obj->libelle.'</option>';
					if ($showlabel)
					{
						print $obj->libelle;
						return;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->code.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		$html.= '</select>';
		return $html;
	}
}

function select_type_campo($selected='',$htmlname='type_campo',$htmloption='',$showempty=0,$showlabel=0)
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_type_campo AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($resql)
	{
		$html.= '<select class="flat" name="'.$htmlname.'">';
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
				if (empty($selected) && $showlabel)
				{
					print '&nbsp;';
					return;
				}
				if (!empty($selected) && $selected == $obj->code)
				{
					$html.= '<option value="'.$obj->code.'" selected="selected">'.$obj->libelle.'</option>';
					if ($showlabel)
					{
						print $obj->libelle;
						return;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->code.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		$html.= '</select>';
		return $html;
	}
}

function select_generic($resql,$showempty='',$htmlname='',$htmloption='',$campo='',$selected='',$nodefined='')
{
	global $db,$langs,$conf;
	$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
	if ($showempty)
	{
		$out.= '<option value="-1"';
		if ($selected == -1) $out.= ' selected="selected"';
		$out.= '>&nbsp;</option>';
	}
	if ($nodefined)
	{
		$out.= '<option value="-2"';
		if ($selected == -2) $out.= ' selected="selected"';
		$out.= '>'.$langs->trans('Internalassignment').'</option>';
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

function generarcodigo($longitud)
{
	$key = '';
	$pattern = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$max = strlen($pattern)-1;
	for($i=0; $i < $longitud; $i++)
	{
		$key .= $pattern{mt_rand(0,$max)};
	}
	return $key;
}

function param_email()
{
	global $conf;
  //parametros de correos email
	$lUseMailEmp = $conf->global->MANT_USE_EXTENSION_MAIL_COMPANY;
	$mailDefault = $conf->global->MANT_EXTENSION_MAIL_DEFAULT;
	if ($lUseMailEmp == 1)
	{
		$_SESSION['mailEmp'] = true;
		$amailsociete = explode('@',$conf->global->MAIN_INFO_SOCIETE_MAIL);
		$_SESSION['mailDefault'] = $amailsociete[1];
	}
	else
	{
		if (!empty($mailDefault))
		{
			$_SESSION['mailDefault'] = $mailDefault;
		}
		else
		{
			unset($_SESSION['mailDefault']);
		}
	}
	$mailDefault = $_SESSION['mailDefault'];
	return $mailDefault;
}

function fetch_typent($id)
{
	global $db, $langs, $conf;
	$sql = "SELECT f.id, f.code, f.libelle FROM ".MAIN_DB_PREFIX."c_typent AS f ";
	$sql.= " WHERE ";
	$sql.= " f.id = ".$id;

	$resql = $db->query($sql);

	if ($resql)
	{
		if ($db->num_rows($resql))
		{
			$obj = $db->fetch_object($resql);
			return $obj;
		}
		return 0;
	}
	return -1;
}

function htmlsendemail($id,$code,$url)
{
	global $object,$langs,$objAdherent;
  //  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha generado el Ticket Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('Para el usuario con correo: ').$object->email.',</p>';
  //  $html.= '<p>'.$langs->trans('Interno: ').$object->internal.'.</p>';

  //  $html.= '<p>'.$langs->trans('En un momento se contactará el técnico de turno.').'</p>';

	$html.='<p>'.$langs->trans('Si no ha llenado sus datos, puede acceder al formulario correspondiente a travez del siguiente enlace').' <a href="'.$url.'/mant/jobs/ficheemail.php?action=edit&id='.$object->id.'&code='.$code.'">'.$langs->trans('Ticket').'</a></p>';

	$html.='<br><p>'.$langs->trans('Para hacer seguimiento, por favor haga uso del siguiente enlace').' <a href="'.$url.'/mant/jobs/ficheseek.php?action=search&ref='.$object->ref.'&code='.$code.'">'.$langs->trans('Tickettracing').'</a></p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function htmlsendemailrech($id,$text,$url)
{
	global $object,$langs,$objAdherent;
  //  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha rechazado la Orden de Trabajo Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('Para el usuario con correo: ').$object->email.',</p>';

	$html.= '<p>'.$text.'</p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function htmlsendemailfile($id,$url)
{
	global $conf,$object,$langs,$objAdherent;
  //  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Para su conocimiento').', ';
	$html.= '<p>'.$langs->trans('El usuario: ').$object->sendUser.', ';
	$html.= $langs->trans('ha realizado la subida de un archivo o creado un vinculo a un archivo o documento.').',</p>';
	$html.= '<p>'.$langs->trans('Presione el siguiente link para ver la Orden de Trabajo: ').'<a href="'.$conf->global->MANT_LINK_WEB.'/mant/jobs/document.php?id='.$object->id.'">'.$object->ref.'<a/></p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('La Gerencia').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}
//envio de correo asignacion trabajoa tecnico interno
function htmlsendemailassignti($id,$url)
{
	global $object,$langs,$objAdherent;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha asignado el ticket Nro.: ').$object->ref.', '.$langs->trans('que detalle el siguiente problema').':</p>';
	$html.= '<p>'.$object->detail_problem.'</p>';

	$html.= '<p>'.$langs->trans('Se ruega proceder a la programacion del mismo').'.</p>';
	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

//envio de correo asignacion trabajoa tecnico interno
function htmlsendemailassignte($id,$url,$aTick=array())
{
	global $objwork,$langs,$objAdherent;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha asignado el ticket Nro.: ').$object->ref.', '.$langs->trans('que detalle el siguiente problema').':</p>';
	$html.= '<p>'.$object->detail_problem.'</p>';

	$html.= '<p>'.$langs->trans('Se ruega proceder a la programacion del mismo').'.</p>';
	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function htmlsendemailprog($id,$text,$url)
{
	global $object,$langs,$objAdherent;
  //  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha programado la Orden de Trabajo Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('Para el usuario con correo: ').$object->email.',</p>';

	$html.= $text;

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function htmlsendemailassign($id,$text,$url)
{
	global $object,$langs,$objAdherent;
  //  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha asignado la Orden de Trabajo Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('Para el usuario solicitante con correo: ').$object->email.',</p>';

	$html.= $text;

	$html.='<br><p>'.$langs->trans('Hacer clic en el siguiente enlace').' <a href="'.$url.'/mant/jobs/fiche.php?ref='.$object->ref.'">'.$langs->trans('Jobs').'</a></p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function htmlsendemailjob($id,$text,$url)
{
	global $object,$langs,$objAdherent;
  //  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha concluido la Orden de Trabajo Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('Para el usuario con correo: ').$object->email.',</p>';

	$html.= $text;

	$html.= '<br><p>'.$langs->trans('Para conformidad, rogamos hacer clic en los siguientes enlaces').'</p>';
	$html.= '<p><a href="'.$url.'/mant/jobs/ficheemail.php?action=confirm&description_confirm=confirmado&statut_job=1&ref='.$object->ref.'&code='.$object->tokenreg.'">'.$langs->trans('Compliance work order').'</a></p>';
	$html.= '<p><a href="'.$url.'/mant/jobs/ficheemail.php?action=confirm&ref='.$object->ref.'&code='.$object->tokenreg.'">'.$langs->trans('No Compliance work order').'</a></p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function htmlsendemailconfirm($id,$text,$url)
{
	global $object,$langs,$objAdherent;
  //  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	if ($object->statut_job == 1)
		$html.= '<p>'.$langs->trans('Compliance work order').': '.$object->ref.',</p>';
	if ($object->statut_job == 2)
		$html.= '<p>'.$langs->trans('Nonconformity of the work order').': '.$object->ref.',</p>';

	$html.= '<p>'.$langs->trans('del usuario con correo: ').$object->email.',</p>';

	$html.= $text;

	$html.='<br><p>'.$langs->trans('Hacer clic en el siguiente enlace').' <a href="'.$url.'/mant/jobs/fiche.php?ref='.$object->ref.'">'.$langs->trans('Jobs').'</a></p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function jobs_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('mant');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/mant/jobs/card.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Fiche");
	$head[$h][2] = 'card';
	$h++;
	if (!is_null($object->fk_soc))
	{
		if ($object->fk_soc > 0 && $object->status>0)
		{
			$head[$h][0] = dol_buildpath("/mant/jobs/cardext.php?id=".$object->id,1);
			$head[$h][1] = $langs->trans("Externalassignment");
			$head[$h][2] = 'ext';
			$h++;
		}
		if ($object->fk_soc < 0 && $object->status>0)
		{
			$head[$h][0] = dol_buildpath("/mant/jobs/cardint.php?id=".$object->id,1);
			$head[$h][1] = $langs->trans("Internalassignment");
			$head[$h][2] = 'int';
			$h++;
		}
	}
	if ($object->status >= 3)
	{
		$head[$h][0] = dol_buildpath("/mant/jobs/cardprog.php?id=".$object->id,1);
		$head[$h][1] = $langs->trans("Programming");
		$head[$h][2] = 'prog';
		$h++;
	}
	if ($object->status >= 4)
	{
		$head[$h][0] = dol_buildpath("/mant/jobs/cardejec.php?id=".$object->id,1);
		$head[$h][1] = $langs->trans("Execution");
		$head[$h][2] = 'exec';
		$h++;
	}
	if ($object->status >= 3)
	{
		$head[$h][0] = dol_buildpath("/mant/jobs/cardmat.php?id=".$object->id,1);
		$head[$h][1] = $langs->trans("Resources");
		$head[$h][2] = 'mat';
		$h++;
	}
	//subida de documentos
	$head[$h][0] = dol_buildpath("/mant/jobs/document.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Documents");
	$head[$h][2] = 'documents';
	$h++;

	return $head;
}

function equipment_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('mant');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/mant/equipment/card.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Fiche");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath("/mant/equipment/program.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Programming");
	$head[$h][2] = 'program';
	$h++;

	$head[$h][0] = dol_buildpath("/mant/equipment/history.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Log");
	$head[$h][2] = 'log';
	$h++;
	return $head;
}

function groups_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('mant');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/mant/groups/card.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Fiche");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath("/mant/groups/program.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Programming");
	$head[$h][2] = 'program';
	$h++;

	return $head;
}

function repair_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('mant');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/mant/repair/card.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Fiche");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath("/mant/repair/carddet.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Detail");
	$head[$h][2] = 'det';
	$h++;

	return $head;
}

function typerepair_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('mant');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/mant/typerepair/card.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Fiche");
	$head[$h][2] = 'card';
	$h++;

	return $head;
}


function select_month($selected='',$htmlname='mes',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;

	$langs->load("contab@contab");

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
	$countryArray[$i]['label'] = $langs->trans('September');
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

function select_days($selected='',$htmlname='days',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;

	$langs->load("poa@poa");

	$out='';
	$countryArray=array();
	$label=array();
	$i = 1;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Monday');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Tuesday');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Wednesday');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Thursday');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Friday');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Saturday');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Sunday');
	$label[$i] = $countryArray[$i]['rowid'];

	if ($showLabel)
		return $countryArray[$selected]['label'];
	$out = print_select($selected,$htmlname,$htmloption,$maxlength,$showempty,$showLabel,$countryArray,$label);
	return $out;
}

function print_select($selected='',$htmlname='status',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0,$countryArray,$label,$loked=0)
{
	$lMultiple = false;
	if ($loked)
		$htmlloked = 'disabled="disabled"';
	if ($htmloption == 'multiple')
	{
		$lMultiple = true;
		$htmloption = 'multiple size=8';
		$htmlname = $htmlname.'[]';
	}
	$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.' '.$htmlloked.'>';
	if ($showempty)
	{
		$out.= '<option value="-1"';
		if ($selected == -1) $out.= ' selected="selected"';
		$out.= '>&nbsp;</option>';
	}
	array_multisort($label, SORT_ASC, $countryArray);

	foreach ($countryArray as $row)
	{
		if (is_array($selected) && $lMultiple)
		{
	  //print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
	  // print '<hr>'.$selected[$row['rowid']].' || '.$selected[$row['label']].' '.$row['id'].' '.$row['label'];
			if ($selected[$row['rowid']] || $selected[$row['label']] )
			{
				$foundselected=true;
				$out.= '<option value="'.$row['rowid'].'" selected="selected">';
			}
			else
			{
				$out.= '<option value="'.$row['rowid'].'">';
			}
		}
		else
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
		}
		$out.= dol_trunc($row['label'],$maxlength,'middle');
		$out.= '</option>';
	}
	$out.= '</select>';

	return $out;
}

//return array speciality
//$llave = rowid, code
function list_speciality($llave)
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_especiality AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$array = array();
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$array[$obj->$llave] = $obj->libelle;
				$i++;
			}
		}
		return $array;
	}
	return array();
}
//funcion que valida la integración de equipos con activos
function integrated_asset($db,$action='')
{
	global $conf,$user;
	if ($conf->global->MANT_EQUIPMENT_INTEGRATED_WITH_ASSET)
	{
		if ($conf->assets->enabled)
		{
			$lUpdate = true;
			if (!isset($_SESSION['dateupdate'])) $_SESSION['dateupdate'] = dol_now();
			else
			{
				$hour = 1;
				require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
				$datenew = dol_time_plus_duree($_SESSION['dateupdate'], $hour, 'h');
				if ($datenew > dol_now()) $lUpdate = false;
			}
			if ($user->rights->mant->update && $action == 'update')
			{
				$_SESSION['dateupdate'] = dol_now();
				$lUpdate = true;
			}

			if ($lUpdate)
			{
			//cada equipo esta totalmente integrado con activo
			//primero verificamos los grupos
				require_once DOL_DOCUMENT_ROOT.'/mant/class/mgroups.class.php';
				require_once DOL_DOCUMENT_ROOT.'/mant/class/mequipmentext.class.php';
				require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';
				require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
				$objGroup = new Mgroups($db);
				$objMequipment = new Mequipmentext($db);
				$objCassetsgroup = new Cassetsgroup($db);
				$objAssets = new Assetsext($db);
			//vamos recorriendo cada grupo del activo
				$res = $objCassetsgroup->fetchAll('','',0,0,array('active'=>1),'AND');
				$aGroup = array();
				if ($res > 0)
				{
					$lines = $objCassetsgroup->lines;
					foreach ($lines AS $j => $line)
					{
						$aGroup[$line->code] = $line->id;
						//buscamos el grupo en mgroup
						$resg = $objGroup->fetch($line->id);
						if ($resg==0)
						{
							//creamos
							$objGroup->entity = $line->entity;
							$objGroup->ref = $line->code;
							$objGroup->fk_asset_group = $line->id;
							$objGroup->label = $line->label;
							$objGroup->description = $line->description;
							$objGroup->useful_life = $line->useful_life;
							$objGroup->percent = $line->percent;
							$objGroup->account_accounting = $line->account_accounting;
							$objGroup->account_spending = $line->account_spending;
							$objGroup->active = $line->active;
							$resg = $objGroup->create($user);
							if ($resg<=0)
							{
								$error++;
								setEventMessages($objGroup->error,$objGroup->errors,'errors');
							}
						}
						elseif ($resg>0)
						{
							//actualizamos
							$objGroup->entity = $line->entity;
							$objGroup->ref = $line->code;
							$objGroup->fk_asset_group = $line->id;
							$objGroup->label = $line->label;
							$objGroup->description = $line->description;
							$objGroup->useful_life = $line->useful_life;
							$objGroup->percent = $line->percent;
							$objGroup->account_accounting = $line->account_accounting;
							$objGroup->account_spending = $line->account_spending;
							$objGroup->active = $line->active;
							$resg = $objGroup->update($user);
							if ($resg<=0)
							{
								$error++;
								setEventMessages($objGroup->error,$objGroup->errors,'errors');
							}
						}
					}
				}
				//vamos recorriendo cada grupo del activo
				$res = $objAssets->fetchAll('','',0,0,array(1=>1),'AND');
				if ($res > 0)
				{
					$lines = $objAssets->lines;
					foreach ($lines AS $j => $line)
					{
						//buscamos el equipo
						$resg = $objMequipment->fetch(0,null,$line->id);
						if ($resg==0)
						{
							//creamos
							$objMequipment->entity = $line->entity;
							$objMequipment->ref = $line->ref;
							$objMequipment->ref_ext = $line->ref_ext;
							$objMequipment->fk_asset = $line->id;

							$objMequipment->label = $line->descrip;
							$objMequipment->metered = $line->metered;
							if (empty($objMequipment->metered)) $objMequipment->metered = 0;
							$objMequipment->accountant = $line->accountant;
							$objMequipment->accountant_last = $line->accountant_last;
							$objMequipment->accountant_mant = $line->accountant_mant;
							$objMequipment->accountant_mante = $line->accountant_mante;
							if (empty($objMequipment->accountant)) $objMequipment->accountant = 0;
							if (empty($objMequipment->accountant_last)) $objMequipment->accountant_last = 0;
							if (empty($objMequipment->accountant_mant)) $objMequipment->accountant_mant = 0;
							if (empty($objMequipment->accountant_mante)) $objMequipment->accountant_mante = 0;

							$objMequipment->fk_unit = $line->fk_unit;
							if (empty($objMequipment->fk_unit)) $objMequipment->fk_unit = 0;
							$objMequipment->margin = $line->margin;
							if (empty($objMequipment->margin)) $objMequipment->margin = 0;
							$objMequipment->trademark = $line->trademark;
							$objMequipment->model = $line->model;
							$objMequipment->anio = $line->anio;
							$objMequipment->fk_location = $line->fk_location;
							$objMequipment->fk_group = $aGroup[$line->type_group];
							$objMequipment->hour_cost = $line->hour_cost;
							$objMequipment->code_program = $line->code_program;
							$objMequipment->fk_equipment_program = $line->fk_equipment_program;
							$objMequipment->fk_user_create = $line->fk_user_create;
							$objMequipment->fk_user_mod = $line->fk_user_mod;
							$objMequipment->datec = $line->datec;
							$objMequipment->datem = $line->datem;
							$objMequipment->active = $line->active;
							if (empty($objMequipment->active)) $objMequipment->active = 1;
							$objMequipment->status = $line->statut;
							$resg = $objMequipment->create($user);
							if ($resg<=0)
							{
								$error++;
								setEventMessages($objMequipment->error,$objMequipment->errors,'errors');
							}
						}
						elseif ($resg>0)
						{
							if ($objMequipment->status != $line->statut || $objMequipment->fk_group != $aGroup[$line->type_group])
							{
								//actualizamos
								$objMequipment->entity = $line->entity;
								$objMequipment->ref = $line->ref;
								$objMequipment->ref_ext = $line->ref_ext;
								$objMequipment->fk_asset = $line->id;

								$objMequipment->label = $line->descrip;
								//$objMequipment->metered = $line->metered;
								//if (empty($objMequipment->metered)) $objMequipment->metered = 0;
								//$objMequipment->accountant = $line->accountant;
								//$objMequipment->accountant_last = $line->accountant_last;
								//$objMequipment->accountant_mant = $line->accountant_mant;
								//$objMequipment->accountant_mante = $line->accountant_mante;
								//if (empty($objMequipment->accountant)) $objMequipment->accountant = 0;
								//if (empty($objMequipment->accountant_last)) $objMequipment->accountant_last = 0;
								//if (empty($objMequipment->accountant_mant)) $objMequipment->accountant_mant = 0;
								//if (empty($objMequipment->accountant_mante)) $objMequipment->accountant_mante = 0;

								//$objMequipment->fk_unit = $line->fk_unit;
								//if (empty($objMequipment->fk_unit)) $objMequipment->fk_unit = 0;
								//$objMequipment->margin = $line->margin;
								//if (empty($objMequipment->margin)) $objMequipment->margin = 0;
								$objMequipment->trademark = $line->trademark;
								$objMequipment->model = $line->model;
								$objMequipment->anio = $line->anio;
								$objMequipment->fk_location = $line->fk_location;
								$objMequipment->fk_group = $aGroup[$line->type_group];
								$objMequipment->hour_cost = $line->hour_cost;
								//$objMequipment->code_program = $line->code_program;
								//$objMequipment->fk_equipment_program = $line->fk_equipment_program;
								//$objMequipment->fk_user_create = $line->fk_user_create;
								$objMequipment->fk_user_mod = $line->fk_user_mod;
								//$objMequipment->datec = $line->datec;
								$objMequipment->datem = dol_now();
								$objMequipment->active = $line->active;
								if (empty($objMequipment->active)) $objMequipment->active = 1;
								$objMequipment->status = $line->statut;
								$resg = $objMequipment->update($user);
								if ($resg<=0)
								{
									$error++;
									setEventMessages($objMequipment->error,$objMequipment->errors,'errors');
								}
							}
						}
					}
				}
			}
		}
	}
}
?>
