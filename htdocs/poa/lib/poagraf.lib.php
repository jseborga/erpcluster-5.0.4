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
 *	\file       htdocs/poa/lib/poagraf.lib.php
 *	\ingroup    Librerias grafic
 *	\brief      Page fiche POA grafic
 */

function poa_grafic($id)
{
	global $objworkd,$langs;

  //buscamos el contenido del workflow det
	$objworkd->getlist($id);
  //recuperamos el contenido del typeprocedure
	$arraytp = getlist_typeprocedure('code');
	$sequen = 0;
  //array para graficar el avance
	$arraygraf = array();
	if (count($objworkd->array) > 0)
	{
		foreach ($objworkd->array AS $i => $objwd)
		{
			$codearea = $objwd->code_area_next;
			$sequen = $objwd->sequen;

	  //buscamos el procedure en el array
			$objtp = $arraytp[$objwd->code_procedure];
	  //agregamos al array para graficar avance
			$arraygraf[] = array('landmark' => $objtp->landmark,
				'date' => $objwd->date_tracking);
		}
	}
	return $arraygraf;
}

function poa_grafic_color()
{
	$arraytp = getlist_typeprocedure('code');

	foreach((array) $arraytp AS $l => $objtp)
	{
		if ($objtp->landmark > 0)
		{
			$landmark = (float) $objtp->landmark;
			$arrayc[$objtp->landmark] = '#'.$objtp->colour;
			$arrayl[$objtp->landmark] = $objtp->label;
			$arrayk[$l] = $objtp->landmark;
		}
	}
	$_SESSION['arrayc'] = $arrayc;
	$_SESSION['arrayl'] = $arrayl;
	$_SESSION['arrayk'] = $arrayk;
	return;
}

//grafica la ejecucion del workflow
function poa_grafic_executed($arraypri,$arraygraf,$arrayc,$arrayl='')
{
	global $langs;
  //variables
	$landmark = '';
	$datei = '';
	$dateini = '';
	$dateini_ = '';
	$nLoop = 0;
	foreach ($arraygraf AS $j => $data)
	{
		if (empty($datei))
		{
			$landmark = $data['landmark'];
			$datei = $data['date'];
			$arraypri[$landmark] = $landmark;
			if ($_SESSION['actionindex'] == 'view1')
			{
		  //imprimimos el inicio real del mes
				$aDateini = dol_getdate($datei);
				$mesini = $aDateini['mon'];
				for ($j = 1; $j < $mesini; $j++)
				{
					$width = 51;
					$html.= '<div class="height20 borderlr floatleft" style="text-align:center; width:'.($width<=0?35:$width).'px; background-color: #fff;">'.'&nbsp;'.'</a>'.'</div>';

				}
			}
		}
		else
		{
	  //echo '<hr>landmark '.$landmark.' data[landmark] '.$data['landmark'];
			if (empty($data['landmark']))
				$arraypri[$landmark] = $landmark;
			else
				$arraypri[$data['landmark']] = $data['landmark'];

	  //	  if ($landmark != $data['landmark'])
			if ($data['landmark']>0)
			{
		  //echo '<hr>landm '.$landmark.' '.$data['landmark'].' '.$nLoop;
				if ($landmark >0 && $landmark != $data['landmark'])
				{
		  //imprimimos
					$dateini = $data['date'];
					$width = resta_fechas( $datei, $dateini,1 )*2;
					$dias = resta_fechas( $datei, $dateini,1 );
					$colorb = (empty($landmark)?$arrayc[$landmark]:$arrayc[$landmark]);
					$colort = '#'.substr(strrev($colorb),0,4).'00';
					$txtmes = '';
		  // echo '<br>ddd '.$landmark;print_r($arrayc).'<hr>color '.$arrayc[$landmark];
					if ($dias > 30)
						$txtmes = round($dias / 30 , 0);
					$html.= '<div class="height20 floatleft" style="text-align:center; width:'.($width<=0?10:$width).'px; background-color: '.(empty($landmark)?$arrayc[0]:$arrayc[$landmark]).'; color:'.$colort.'">'.'<a href="#" title="'.$arrayl[$landmark].' | '.$langs->trans('From').' '.dol_print_date($datei,'day').' '.$langs->trans('To').' '.dol_print_date($dateini,'day').'">'.$dias.($txtmes>0?' ('.$txtmes.' m.)':'').'</a>'.'</div>';

		  //cambiamos valores
					$datei = $dateini;
					$landmark = $data['landmark'];
		  //$dateini  = $data['date'];
				}
				else
				{
					$dateini = $data['date'];
					$landmark = $data['landmark'];
		  //				 echo '<hr>nLoop '.$nLoop .' = '.count($arraygraf).' '.$dateini;
		  //		  $datei = $dateini;
				}
			}
	  // else
	  //   {
	  //     $dateini  = $data['date'];
	  //   }


		}
		$nLoop++;
		$datefin = $data['date'];
	}

  //cerramos con el ultimo grafico
  //echo '<hr>final '.$nLoop.' == '.count($arraygraf);
	if ($nLoop == count($arraygraf))
	{
	  //imprimimos si no esta cerrado
	  //echo '<hr>statut '.$object->statut;
		if ($object->statut <= 1)
		{
			$dateini = dol_now();
			$width = resta_fechas( $datei, $dateini,1 )*2;
			$dias = resta_fechas( $datei, $dateini,1 );
			$colorb = (empty($data['landmark'])?$arrayc[$landmark]:$arrayc[$data['landmark']]);
			$colort = '#'.substr(strrev($colorb),0,6);

			$txtmes = '';
			if ($dias > 30)
				$txtmes = round($dias / 30 , 0);

			$html.= '<div class="height20 floatleft" style="text-align:center; width:'.($width<=0?35:$width).'px; background-color: '.(empty($data['landmark'])?$arrayc[$landmark]:$arrayc[$data['landmark']]).'; color:'.$colort.';">'.'<a href="#" title="'.$arrayl[$landmark].' | '.$langs->trans('From').' '.dol_print_date($datei,'day').' '.$langs->trans('To').' '.dol_print_date($dateini,'day').'">'.$dias.($txtmes>0?' ('.$txtmes.' m.)':'').'</a>'.'</div>';
		}
		else
		{
			$width = resta_fechas( $datei, $dateini,1 )*2;
			$dias = resta_fechas( $datei, $dateini,1 );
			$colorb = (empty($data['landmark'])?$arrayc[$landmark]:$arrayc[$data['landmark']]);
			$colort = '#'.substr(strrev($colorb),0,6);

			$txtmes = '';
			if ($dias > 30)
				$txtmes = round($dias / 30 , 0);

			$html.= '<div class="height20 floatleft" style="text-align:center; width:'.($width<=0?10:$width).'px; background-color: '.(empty($data['landmark'])?$arrayc[$landmark]:$arrayc[$data['landmark']]).'; color:'.$colort.';">'.'<a href="#" title="'.$arrayl[$landmark].' | '.$langs->trans('From').' '.dol_print_date($datei,'day').' '.$langs->trans('To').' '.dol_print_date($dateini,'day').'">'.$dias.($txtmes>0?' ('.$txtmes.' m.)':'').'</a>'.'</div>';
		}

	}
	$html.= '<div class="clear"></div>';
  //cargamos en memoria el array principal
	$_SESSION['arraypri'] = $arraypri;

	return $html;
}

//prepara el grafico para los labels
function poa_grafic_label($arrayl,$arrayc)
{
	global $langs;
	$arraypri = $_SESSION['arraypri'];
  //imprimir los labels
	$html = '<br><br>';
	$html.= '<div>';
	if (count($arrayl) >0)
		ksort($arrayl);
	$html.= '<div>'.$langs->trans('Legend').'</div>';

	foreach ((array) $arrayl AS $l => $label)
	{
		$al = explode('.',$l);
		if ($al[1]>0)
			$nl = $l;
		else
			$nl = $al[0];
	  // if ($arraypri[$l])
	  // 	{
		$html.= '<div class=" height20 floatleft" style="text-align:center; width:35px; background-color: '.$arrayc[$l].'; height:20px;">';

		$html.= '<a href="#" title="'.$label.'">'.$nl.'</a>'.'</div>';
	  //$html.= '<div class="clear"></div>';
	  //	}
	}
	$html.= '</div>';
	return $html;
}

//prepara el grafico para los labels
function poa_grafic_select($group,$arrayl,$arrayc,$arrayk,$pos,$id,$idp)
{
	global $langs,$objflow;

	$arraypri = $_SESSION['arraypri'];
	$poslk = $arrayk[$pos];

	$objflow->getlist($group);

	foreach ((array) $objflow->array AS $j => $objf)
	{
		$arrayf[$objf->code] = array('label'=>$objf->label,
			'array' => array($objf->code0,$objf->code1,$objf->code2,$objf->code3,$objf->code4));
	}
  //imprimir los labels
	$html = '<br><br>';
  //$html.= '<div>';
	if (count($arrayl) >0)
		ksort($arrayl);
	$html.= '<div>'.$langs->trans('Select').'</div>';
  //arrayf = procedimientos del grupo
  //arrayr = posibles respuestas de accion
	foreach ((array) $arrayf AS $ll => $arrayr)
	{
		$l = $arrayk[$ll];
		$label = $arrayl[$l];
		$al = explode('.',$l);
		if ($al[1]>0)
			$nl = $l;
		else
			$nl = $al[0];
		if ($poslk && $l == $poslk)
		{
			$_SESSION['transfid_'] = $id;
			$_SESSION['transfidp_'] = $idp;
			$_SESSION['transfll_'] = $ll;
			$_SESSION['transgroup_'] = $group;

			$html.= '<a class="linkall" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idp='.$idp.'&action=createtrans'.'" title="'.$label.'">';
		}
		else
			$html.= '<a class="linkall" href="#" title="'.$label.'">';

		$html.= '<div class=" height60 width80 padding1 margin5 floatleftm3" style="text-align:center; background-color: '.$arrayc[$l].'; ';
		if ($poslk && $l == $poslk)
			$html.= ' border-style: solid; border-color:red; border-width: 1px;';
		$html.= '">';
	  //      $html.= $label; //$nl;
		$html.= $arrayr['label'];
		$html.= '</div>';
		$html.= '</a>';
	  //$html.= '<div class=" height100 width10 floatleft">&nbsp;</div>';

	}
  //$html.= '</div>';
	return $html;
}

?>
