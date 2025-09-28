<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/poa/lib/execution.lib.php
 *      \ingroup    Plan Operativo Anual
 *      \brief      Page index des poa
 */


if ($result)
{

	$form=new Form($db);

	$objwork = new Poaworkflow($db);
	$objworkd = new Poaworkflowdet($db);
	//rango de colores error para retraso
	$cRangecolors = $conf->global->POA_COLORS_RANGE_DAYS_LATE;
	list($cDays,$cColors) = explode('|',$conf->global->POA_COLORS_RANGE_DAYS_LATE);
	list($cDaysall,$cColorsall) = explode('|',$conf->global->POA_COLORS_RANGE_DAYS_LATE_ALL);
	$acDays = explode(',',$cDays);
	$acColors = explode(',',$cColors);
	$acDaysall = explode(',',$cDaysall);
	$acColorsall = explode(',',$cColorsall);
	$nDia = 0;
	//definiciones de array
	$aRetraso = array();
	$nTotal = 0;
	$aEstado = array();
	$aPreventivo = array();
	$nDiamax = 0;
	foreach ((array) $acDays AS $j => $nDay)
	{
		$aRetraso[$nDay] = 0;
		$aColors[$nDay] = $acColors[$j];
		$aDays[$nDay] = array(1=>$nDia,2=>$nDay);
		$nDia = $nDay+1;
		$nDiamax = $nDay;
	}
	$nDiamax+=1;
	$aRetraso[$nDiamax];
	$aColors[$nDiamax] = 'FF0000';
	$nDiaall = 0;
	foreach ((array) $acDaysall AS $j => $nDay)
	{
		$aRetrasoall[$nDay] = 0;
		$aColorsall[$nDay] = $acColorsall[$j];
		$aDaysall[$nDay] = array(1=>$nDiaall,2=>$nDay);
		$nDiaall = $nDay+1;
		$nDiamaxall = $nDay;
	}
	$nDiamaxall+=1;
	$aRetrasoall[$nDiamaxall];
	$aColorsall[$nDiamaxall] = 'FF0000';

	//array preventivo
	//    $aPreventivo[-1] = 0;
	$aPreventivo[0] = 0;
	$aPreventivo[1] = 0;
	$aPreventivo[2] = 0;
	$aPreventivo[3] = 0;

	$num = $db->num_rows($result);
	$i = 0;

	if ($num)
	{
		$sumaAmount = 0;
		$var=True;
		while ($i < $num)
		{
			$obj = $db->fetch_object($result);
			$newNombre = '';
			$nombre = '';
			if ($objuser->fetch($obj->fk_user_create))
			{
				$nombre = $objuser->firstname.' '.$objuser->lastname;
				$aNombre = explode(' ',$nombre);
				foreach($aNombre AS $k => $value)
				{
					$newNombre .= substr($value,0,1);
				}
			}
			$lContinue = true;
			if (!empty($search_user) &&
				$search_user != $newNombre)
				$lContinue = false;

			if ($lContinue)
			{
				$var=!$var;

				$nTotal++;
		//workflow
				$daydelay = 0;
				$daydelayall = 0;
				$date_tracking = '';
				$date_workflow = $db->jdate($obj->date_preventive);
				$cArea = '';
				$objworkact = '';
				$iniproceso = false;
				$iniwork    = false;

		//buscamos el workflow
				if ($objwork->fetch_prev($obj->id)>0)
				{
					if ($objwork->fk_poa_prev == $obj->id)
					{
			//buscamos el ultimo registro workflowdet
						$objworkd->getlist($objwork->id,1);
						foreach((array) $objworkd->array AS $l => $objWorkDet)
						{
							if (empty($date_tracking))
							{
								$date_tracking = $objWorkDet->date_tracking;
								$cArea = $objWorkDet->code_area_next;
								$objworkact = $objWorkDet;
							}
						}
			//determinamos el tiempo transcurrido
						$daydelayall = resta_fechas($date_workflow,dol_now(),1);
						if ($objwork->statut < 2)
							$daydelay    = resta_fechas($date_tracking,dol_now(),1);
						if ($objwork->fk_poa_prev == $obj->id)
						{
							if (is_null($objwork->contrat))
								$iniwork = 2;
							if ($objwork->contrat == '1' || $objwork->contrat == '0')
								$iniwork = 3;
				//si no pertenece al usuario
							if ($user->id != $obj->fk_user_create)
							{
								$iniwork = false;
								if ($objwork->fk_poa_prev == $obj->id)
									$iniwork = 3;
							}

						}
						else
							$iniwork = 1;
					}
					else
					{
						if ($user->admin || ($user->id == $obj->fk_user_create))
							$iniwork = 1;
					}
				}
				else
					$iniwork = 1;
				$cClass = "";
				foreach ((array) $aDays AS $nDay => $aDay)
				{
					if ($daydelay >= $aDay[1] && $daydelay <= $aDay[2])
					{
						$cClass = $aColors[$nDay];
						$aRetraso[$nDay]++;
					}
				}
		// if ($daydelay <= 0)
		//   $aRetraso[0]++;
				if ($daydelay > $nDiamax)
					$aRetraso[$nDiamax]++;

		//all
				foreach ((array) $aDaysall AS $nDay => $aDay)
				{
					if ($daydelayall >= $aDay[1] && $daydelayall <= $aDay[2])
					{
						$aRetrasoall[$nDay]++;
					}
				}
		// if ($daydelayall <= 0)
		//   $aRetrasoall[0]++;
				if ($daydelayall > $nDiamaxall)
					$aRetrasoall[$nDiamaxall]++;

		//fin workflow

				if (empty($cClass) && $daydelay > $nDay) $cClass = 'ffaaaa';
				$aPreventivo[$obj->statut]++;

				if ($idp)
					$sumaAmount += $obj->amountpartida;

		//instruction
		//buscamos la ultima instruccion si existe para el poa seleccionado
				$addClase = '';
				$addMessage = '';
				if ($conf->poai->enabled)
				{
					$objinst->fetch_pre($obj->id);
					if ($objinst->fk_id == $obj->id)
					{
						$objinst->fk_id.' '.$obj->id;

						$idInst = $objinst->id;
						$newClaseor = $newClase;
						$detail = $objinst->detail;
			//verificamos si tiene monitoreo por revisar
						if ($objmoni->fetch_ult($obj->id,'PRE'))
						{
							if ($objmoni->fk_id == $obj->id)
							{
								$idInst = $objmoni->fk_poai_instruction;
								$addMessage = '&#13;'.$langs->trans('Monitoring').': '.$objmoni->detail;
								if ($lStyle)
									$newClase.= ' background:#12e539;';
								else
									$newClase.= '" style="background:#12e539;';
							}
						}
						$newClase = $newClaseor;
					}
				}

		//----------------------------------//
		//action
		//		print '<td align="right" nowrap>';
		//se movio a la parte inicial del workflow

				$message = $daydelay .'/'.$daydelayall;
				if ($objwork->statut == 2 && $objwork->fk_poa_prev == $obj->id)
				{
					$daydelay = 0;
				}
				else
				{
					if ($daydelay > $nDay)
						$cClass = $aColors[$nDay];
				}
		//buscamos el proceso
				if ($obj->statut != -1)
				{
					$objproc->fetch_prev($obj->id);
					if ($objproc->fk_poa_prev == $obj->id)
					{
						$addMessage = '&nbsp;'.$langs->trans('Doc').': '.$objproc->ref.'/'.$objproc->gestion;
					}
					else
						if ( $user->admin ||
							(!$user->admin && $user->id == $obj->fk_user_create))
						{
							$iniproceso = true;
						}
					}
				}
				$i++;
			}
		}

	//armamos el resumen por individual
		$htmlindividual = '';

		$min = 0;
		$seq = count($aColors);
		$x=0;
		foreach((array) $aColors AS $nD1 => $cColor)
		{
			$x++;
			$textind = '';
			$htmlindividual.= '<div class="refcolors" style="background:#'.$cColor.';">';
			if ($x == $seq)
				$textind = $langs->trans('Mayor o igual a').' '.$nD1.' '.$langs->trans('Days');
			else
	  // if ($nD1 == 0)
	  //   $textind = $nD1.' '.$langs->trans('Days');
	  // else
				$textind =  $min .' '.$langs->trans('To').' '.$nD1.' '.$langs->trans('Days');
			$rango = 0;
			if ($nTotal)
				$rango = $aRetraso[$nD1] / $nTotal * 100;
			$htmlindividual.= '<table style="width:'.ceil($rango).'%;" class="tdblock" >';
			$htmlindividual.= '<tr><td id="fondo1">';
			$htmlindividual.= '<a href="liste.php?filterw=n'.$nD1.'" title="'.$textind.'">';
			$htmlindividual.= $aRetraso[$nD1];
			$htmlindividual.= '</a>';
			$htmlindividual.= '</td></tr>';
			$htmlindividual.= '</table>';
			$htmlindividual.= '</div>';
			$min = $nD1+1;
		}

		$htmltotal = '<div style="clear:both;"></div>';

		$min = 0;
		$seq = count($aColorsall);
		$x=0;
		foreach((array) $aColorsall AS $nD1 => $cColor)
		{
			$x++;
			$textall = '';
			$htmltotal.= '<div class="refcolors" style="background:#'.$cColor.';">';

			if ($x == $seq)

				$textall = $langs->trans('Mayor o igual a').' '.$nD1.' '.$langs->trans('Days');
			else
	  // if ($nD1 == 0)
	  //   $textall = $nD1.' '.$langs->trans('Days');
	  //   else
				$textall =  $min .' '.$langs->trans('To').' '.$nD1.' '.$langs->trans('Days');

			$rango = 0;
			if ($nTotal)
				$rango = $aRetrasoall[$nD1] / $nTotal * 100;

			$htmltotal.= '<table style="width:'.ceil($rango).'%;" class="tdblock" >';
			$htmltotal.= '<tr><td id="fondo1">';

			$htmltotal.= '<a href="liste.php?filterx=n'.$nD1.'" title="'.$textall.'">';

			$htmltotal.= $aRetrasoall[$nD1];
			$htmltotal.= '</a>';
			$htmltotal.= '</td></tr>';
			$htmltotal.= '</table>';

			$htmltotal.= '</div>';
			$min = $nD1+1;
		}

	//presupuesto
		$htmlpre = '';

		$min = 0;
		$seq = count($aColorsall);
		$x=0;
		foreach((array) $aPreventivo AS $i => $valor)
		{
			if ($i == -1) {$cColor = 'FF0000';$cImg = 'anu.png';$cText=$langs->trans('Canceled');}
			if ($i == 0) {$cColor = 'FF7700';$cImg = 'pen.png';$cText=$langs->trans('Pending');}
			if ($i == 1) {$cColor = 'FFF600';$cImg = 'pre.png';$cText=$langs->trans('Preventive');}
			if ($i == 2) {$cColor = '0030FF';$cImg = 'com.png';$cText=$langs->trans('Committed');}
			if ($i == 3) {$cColor = '1BBDD5';$cImg = 'dev.png';$cText=$langs->trans('Accrued');}
			if ($i == 4) {$cColor = '1ABD21';$cImg = 'pag.png';$cText=$langs->trans('All');}
			$htmlpre.= '<div class="refejec">';

			$x++;

			$htmlpre.= '<table class="tdblock" >';
			$htmlpre.= '<tr><td id="fondo1">';
			$htmlpre.= '<a href="liste.php?filters=n'.$i.'">';
			$htmlpre.= img_picto($langs->trans($cText),DOL_URL_ROOT.'/poa/img/'.$cImg,'',1);
			$htmlpre.= '</a>';
			$htmlpre.= '</td></tr>';
			$htmlpre.= '</table>';
			$htmlpre.= '</div>';

		// $rango = $valor / $nTotal * 100;

		// print '<div id="divl" class="size15">';
		// print $valor;
		// print '</a>';
		// print '</td></tr>';
		// print '</table>';
		// print '</div>';
			$min = $i+1;
	//    	print '<div style="clear:both;"></div>';
		}
	// //total
	// print '<div id="divl"  class="imgall tright ">';
	// print $langs->trans('Total');
	// print '</div>';
	// print '<div id="divl" class="tleft size15">';
	// print '<a href="liste.php?nosearch_x=n">';

	// print $nTotal;
	// PRINT '</a>';
	// print '</div>';
	// print '<div style="clear:both;"></div>';

	// print '</div>';
	// print '</div>';


	// print '<div style="clear:both;"></div>';

	// print '</section>';


	//$db->free($result);

	}
	else
	{
		dol_print_error($db);
	}


//$db->close();

	?>
