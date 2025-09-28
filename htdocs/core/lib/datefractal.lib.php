<?php

/**
 *	Function to return number of working days (and text of units) between two dates (working days)
 *
 *	@param	   	int			$timestampStart     Timestamp for start date (date must be UTC to avoid calculation errors)
 *	@param	   	int			$timestampEnd       Timestamp for end date (date must be UTC to avoid calculation errors)
 *	@param     	int			$inhour             0: return number of days, 1: return number of hours
 *	@param		int			$lastday            We include last day, 0: no, 1:yes
 *  @param		int			$halfday			Tag to define half day when holiday start and end
 *  @param      string		$country_code       Country code (company country code if not defined)
 *	@return    	int								Number of days or hours
 *  @see also num_between_day, num_public_holiday
 */
function num_open_day_fractal($timestampStart, $timestampEnd, $inhour=0, $lastday=0, $halfday=0, $country_code='')
{
	global $langs,$mysoc;

	if (empty($country_code)) $country_code=$mysoc->country_code;

	dol_syslog('num_open_day timestampStart='.$timestampStart.' timestampEnd='.$timestampEnd.' bit='.$lastday.' country_code='.$country_code);

	// Check parameters
	if (! is_int($timestampStart) && ! is_float($timestampStart)) return 'ErrorBadParameter_num_open_day';
	if (! is_int($timestampEnd) && ! is_float($timestampEnd)) return 'ErrorBadParameter_num_open_day';

	//print 'num_open_day timestampStart='.$timestampStart.' timestampEnd='.$timestampEnd.' bit='.$lastday;
	if ($timestampStart < $timestampEnd)
	{
		$numdays = num_between_day($timestampStart, $timestampEnd, $lastday);
		$numholidays = num_public_holiday_fractal($timestampStart, $timestampEnd, $country_code, $lastday);
		$nbOpenDay = $numdays - $numholidays;
		$nbOpenDay.= " " . $langs->trans("Days");
		if ($inhour == 1 && $nbOpenDay <= 3) $nbOpenDay = $nbOpenDay*24 . $langs->trans("HourShort");
		return $nbOpenDay - (($inhour == 1 ? 12 : 0.5) * abs($halfday));
	}
	elseif ($timestampStart == $timestampEnd)
	{
		$nbOpenDay=$lastday;
		if ($inhour == 1) $nbOpenDay = $nbOpenDay*24 . $langs->trans("HourShort");
		return $nbOpenDay - (($inhour == 1 ? 12 : 0.5) * abs($halfday));
	}
	else
	{
		return $langs->trans("Error");
	}
}

/**
 *  Fonction retournant le nombre de jour feries, samedis et dimanches entre 2 dates entrees en timestamp. Dates must be UTC with hour, day, min to 0
 *  Called by function num_open_day
 *
 *  @param      int         $timestampStart     Timestamp de debut
 *  @param      int         $timestampEnd       Timestamp de fin
 *  @param      string      $countrycode        Country code
 *  @param      int         $lastday            Last day is included, 0: no, 1:yes
 *  @return     int                             Nombre de jours feries
 *  @see num_between_day, num_open_day
 */
function num_public_holiday_fractal($timestampStart, $timestampEnd, $countrycode='BO', $lastday=0)
{
	global $db;
	$nbFerie = 0;

	// Check to ensure we use correct parameters

	if ((($timestampEnd - $timestampStart) % 86400) != 0) return 'ErrorDates must use same hours and must be GMT dates';

	$i=0;
	while (( ($lastday == 0 && $timestampStart < $timestampEnd) || ($lastday && $timestampStart <= $timestampEnd) )
		&& ($i < 50000))
	// Loop end when equals (Test on i is a security loop to avoid infinite loop)
	{
		$ferie=false;
		$countryfound=0;

		$jour  = date("d", $timestampStart);
		$mois  = date("m", $timestampStart);
		$annee = date("Y", $timestampStart);

		$countryfound=1;

			// Definition des dates feriees fixes
		if($jour == 1 && $mois == 1)   $ferie=true;
			// Año nuevo
		if($jour == 1 && $mois == 5)   $ferie=true;
			// 1 Mayo
		if($jour == 25 && $mois == 12) $ferie=true;
			// 25 diciembre navidad

			// Calcul día de Pascua
		$date_paques = easter_date($annee);
		$jour_paques = date("d", $date_paques);
		$mois_paques = date("m", $date_paques);
		if($jour_paques == $jour && $mois_paques == $mois) $ferie=true;
			// Paques

			// Viernes Santo
		$date_viernes = mktime(
			date("H", $date_paques),
			date("i", $date_paques),
			date("s", $date_paques),
			date("m", $date_paques),
			date("d", $date_paques) -2,
			date("Y", $date_paques)
		);
		$jour_viernes = date("d", $date_viernes);
		$mois_viernes = date("m", $date_viernes);
		if($jour_viernes == $jour && $mois_viernes == $mois) $ferie=true;
			//Viernes Santo

			// Calul des samedis et dimanches
		$jour_julien = unixtojd($timestampStart);
		$jour_semaine = jddayofweek($jour_julien, 0);
		if($jour_semaine == 0 || $jour_semaine == 6) $ferie=true;
			//Samedi (6) et dimanche (0)

			//revisamos la tabla de configuracion de p_holiday
		require_once DOL_DOCUMENT_ROOT.'/orgman/class/pholiday.class.php';
		$objHoliday = new Pholiday($db);
		$res = $objHoliday->fetchAll('','',0,0,array('t.status'=>1),'AND');
		if ($res > 0)
		{
			foreach ($objHoliday->lines AS $j => $line)
			{

				if ($line->type == 1)
				{

					if ($jour == $line->date_day && $mois == $line->date_month && $annee == $line->date_year) $ferie=true;
				}
				else
				{
					if ($jour == $line->date_day && $mois == $line->date_month) $ferie=true;
				}
			}
		}

			// On incremente compteur
		if ($ferie) $nbFerie++;

		// Increase number of days (on go up into loop)
		$timestampStart=dol_time_plus_duree($timestampStart, 1, 'd');
		//var_dump($jour.' '.$mois.' '.$annee.' '.$timestampStart);

		$i++;
	}

	return $nbFerie;
}

?>