<?php

    /**
     *	Show a HTML widget to input a date or combo list for day, month, years and optionaly hours and minutes.
     *  Fields are preselected with :
     *            	- set_time date (must be a local PHP server timestamp or string date with format 'YYYY-MM-DD' or 'YYYY-MM-DD HH:MM')
     *            	- local date in user area, if set_time is '' (so if set_time is '', output may differs when done from two different location)
     *            	- Empty (fields empty), if set_time is -1 (in this case, parameter empty must also have value 1)
     *
     *	@param	timestamp	$set_time 		Pre-selected date (must be a local PHP server timestamp), -1 to keep date not preselected, '' to use current date.
     *	@param	string		$prefix			Prefix for fields name
     *	@param	int			$h				1=Show also hours
     *	@param	int			$m				1=Show also minutes
     *	@param	int			$empty			0=Fields required, 1=Empty input is allowed
     *	@param	string		$form_name 		Not used
     *	@param	int			$d				1=Show days, month, years
     * 	@param	int			$addnowbutton	Add a button "Now"
     * 	@param	int			$nooutput		Do not output html string but return it
     * 	@param 	int			$disabled		Disable input fields
     *  @param  int			$fullday        When a checkbox with this html name is on, hour and day are set with 00:00 or 23:59
     * 	@return	mixed						Nothing or string if nooutput is 1
     *  @see	form_date
     */
function select_ndate($set_time='', $prefix='re', $h=0, $m=0, $empty=0, $form_name="", $d=1, $addnowbutton=0, $nooutput=0, $disabled=0, $fullday='',$options='')
    {
        global $conf,$langs;

        $retstring='';

        if($prefix=='') $prefix='re';
        if($h == '') $h=0;
        if($m == '') $m=0;
        if($empty == '') $empty=0;

        if ($set_time === '' && $empty == 0)
        {
        	include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
        	$set_time = dol_now('tzuser')-(getServerTimeZoneInt('now')*3600); // set_time must be relative to PHP server timezone
        }

        // Analysis of the pre-selection date
        if (preg_match('/^([0-9]+)\-([0-9]+)\-([0-9]+)\s?([0-9]+)?:?([0-9]+)?/',$set_time,$reg))
        {
            // Date format 'YYYY-MM-DD' or 'YYYY-MM-DD HH:MM:SS'
            $syear	= (! empty($reg[1])?$reg[1]:'');
            $smonth	= (! empty($reg[2])?$reg[2]:'');
            $sday	= (! empty($reg[3])?$reg[3]:'');
            $shour	= (! empty($reg[4])?$reg[4]:'');
            $smin	= (! empty($reg[5])?$reg[5]:'');
        }
        elseif (strval($set_time) != '' && $set_time != -1)
        {
            // set_time est un timestamps (0 possible)
            $syear = dol_print_date($set_time, "%Y");
            $smonth = dol_print_date($set_time, "%m");
            $sday = dol_print_date($set_time, "%d");
            $shour = dol_print_date($set_time, "%H");
            $smin = dol_print_date($set_time, "%M");
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
                    $retstring.='<input id="'.$prefix.'" name="'.$prefix.'" type="text" size="9" maxlength="11" value="'.$formated_date.'"';
                    $retstring.=($disabled?' disabled="disabled"':'');
                    $retstring.=' onChange="dpChangeDay(\''.$prefix.'\',\''.$langs->trans("FormatDateShortJavaInput").'\'); "';  // FormatDateShortInput for dol_print_date / FormatDateShortJavaInput that is same for javascript
		    if (!empty($options))
		      $retstring.= $options;
                    $retstring.='>';

                    // Icone calendrier
                    if (! $disabled)
                    {
		      $retstring.='<button id="'.$prefix.'Button" type="button" class="dpInvisibleButtons"';
		      $base=DOL_URL_ROOT.'/core/';
		      $retstring.=' onClick="showDP(\''.$base.'\',\''.$prefix.'\',\''.$langs->trans("FormatDateShortJavaInput").'\',\''.$langs->defaultlang.'\');">'
			//$retstring.='>';
			.img_object($langs->trans("SelectDate"),'calendarday','class="datecallink"')
			.'</button>';
                    }
                    else 
		      $retstring.='<button id="'.$prefix.'Button" type="button" class="dpInvisibleButtons">'.img_object($langs->trans("Disabled"),'calendarday','class="datecallink"').'</button>';

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
                // Day
                $retstring.='<select'.($disabled?' disabled="disabled"':'').' class="flat" name="'.$prefix.'day">';

                if ($empty || $set_time == -1)
                {
                    $retstring.='<option value="0" selected="selected">&nbsp;</option>';
                }

                for ($day = 1 ; $day <= 31; $day++)
                {
                    $retstring.='<option value="'.$day.'"'.($day == $sday ? ' selected="selected"':'').'>'.$day.'</option>';
                }

                $retstring.="</select>";

                $retstring.='<select'.($disabled?' disabled="disabled"':'').' class="flat" name="'.$prefix.'month">';
                if ($empty || $set_time == -1)
                {
                    $retstring.='<option value="0" selected="selected">&nbsp;</option>';
                }

                // Month
                for ($month = 1 ; $month <= 12 ; $month++)
                {
                    $retstring.='<option value="'.$month.'"'.($month == $smonth?' selected="selected"':'').'>';
                    $retstring.=dol_print_date(mktime(12,0,0,$month,1,2000),"%b");
                    $retstring.="</option>";
                }
                $retstring.="</select>";

                // Year
                if ($empty || $set_time == -1)
                {
                    $retstring.='<input'.($disabled?' disabled="disabled"':'').' placeholder="'.dol_escape_htmltag($langs->trans("Year")).'" class="flat" type="text" size="3" maxlength="4" name="'.$prefix.'year" value="'.$syear.'">';
                }
                else
                {
                    $retstring.='<select'.($disabled?' disabled="disabled"':'').' class="flat" name="'.$prefix.'year">';

                    for ($year = $syear - 5; $year < $syear + 10 ; $year++)
                    {
                        $retstring.='<option value="'.$year.'"'.($year == $syear ? ' selected="true"':'').'>'.$year.'</option>';
                    }
                    $retstring.="</select>\n";
                }
            }
        }

        if ($d && $h) $retstring.='&nbsp;';

        if ($h)
        {
            // Show hour
            $retstring.='<select'.($disabled?' disabled="disabled"':'').' class="flat '.($fullday?$fullday.'hour':'').'" name="'.$prefix.'hour">';
            if ($empty) $retstring.='<option value="-1">&nbsp;</option>';
            for ($hour = 0; $hour < 24; $hour++)
            {
                if (strlen($hour) < 2) $hour = "0" . $hour;
                $retstring.='<option value="'.$hour.'"'.(($hour == $shour)?' selected="true"':'').'>'.$hour.(empty($conf->dol_optimize_smallscreen)?'':'H').'</option>';
            }
            $retstring.='</select>';
            if (empty($conf->dol_optimize_smallscreen)) $retstring.=":";
        }

        if ($m)
        {
            // Show minutes
            $retstring.='<select'.($disabled?' disabled="disabled"':'').' class="flat '.($fullday?$fullday.'min':'').'" name="'.$prefix.'min">';
            if ($empty) $retstring.='<option value="-1">&nbsp;</option>';
            for ($min = 0; $min < 60 ; $min++)
            {
                if (strlen($min) < 2) $min = "0" . $min;
                $retstring.='<option value="'.$min.'"'.(($min == $smin)?' selected="true"':'').'>'.$min.(empty($conf->dol_optimize_smallscreen)?'':'').'</option>';
            }
            $retstring.='</select>';
        }

        // Add a "Now" button
        if ($conf->use_javascript_ajax && $addnowbutton)
        {
            // Script which will be inserted in the OnClick of the "Now" button
            $reset_scripts = "";

            // Generate the date part, depending on the use or not of the javascript calendar
            if ($usecalendar == "eldy")
            {
                $base=DOL_URL_ROOT.'/core/';
                $reset_scripts .= 'resetDP(\''.$base.'\',\''.$prefix.'\',\''.$langs->trans("FormatDateShortJavaInput").'\',\''.$langs->defaultlang.'\');';
            }
            else
            {
                $reset_scripts .= 'this.form.elements[\''.$prefix.'day\'].value=formatDate(new Date(), \'d\'); ';
                $reset_scripts .= 'this.form.elements[\''.$prefix.'month\'].value=formatDate(new Date(), \'M\'); ';
                $reset_scripts .= 'this.form.elements[\''.$prefix.'year\'].value=formatDate(new Date(), \'yyyy\'); ';
            }
            // Generate the hour part
            if ($h)
            {
                if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
                $reset_scripts .= 'this.form.elements[\''.$prefix.'hour\'].value=formatDate(new Date(), \'HH\'); ';
                if ($fullday) $reset_scripts .= ' } ';
            }
            // Generate the minute part
            if ($m)
            {
                if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
                $reset_scripts .= 'this.form.elements[\''.$prefix.'min\'].value=formatDate(new Date(), \'mm\'); ';
                if ($fullday) $reset_scripts .= ' } ';
            }
            // If reset_scripts is not empty, print the button with the reset_scripts in OnClick
            if ($reset_scripts && empty($conf->dol_optimize_smallscreen))
            {
                $retstring.=' <button class="dpInvisibleButtons datenowlink" id="'.$prefix.'ButtonNow" type="button" name="_useless" value="Now" onClick="'.$reset_scripts.'">';
                $retstring.=$langs->trans("Now");
                $retstring.='</button> ';
            }
        }

        if (! empty($nooutput)) return $retstring;

        print $retstring;
        return;
    }

?>