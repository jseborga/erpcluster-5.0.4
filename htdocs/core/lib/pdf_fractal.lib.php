<?php

/**
 *   	Return a string with full address formated for output on documents
 *
 * 		@param	Translate	$outputlangs		Output langs object
 *   	@param  Societe		$sourcecompany		Source company object
 *   	@param  Societe		$targetcompany		Target company object
 *      @param  Contact		$targetcontact		Target contact object
 * 		@param	int			$usecontact			Use contact instead of company
 * 		@param	int			$mode				Address type ('source', 'target', 'targetwithdetails', 'targetwithdetails_xxx': target but include also phone/fax/email/url)
 *      @param  Object      $object             Object we want to build document for
 * 		@return	string							String with full address
 */
function pdf_fractal_build_address($outputlangs,$sourcecompany,$targetcompany='',$targetcontact='',$usecontact=0,$mode='source',$object=null)
{
	global $conf, $hookmanager;

	if ($mode == 'source' && ! is_object($sourcecompany)) return -1;
	if ($mode == 'target' && ! is_object($targetcompany)) return -1;

	if (! empty($sourcecompany->state_id) && empty($sourcecompany->departement)) $sourcecompany->departement=getState($sourcecompany->state_id); //TODO deprecated
	if (! empty($sourcecompany->state_id) && empty($sourcecompany->state))       $sourcecompany->state=getState($sourcecompany->state_id);
	if (! empty($targetcompany->state_id) && empty($targetcompany->departement)) $targetcompany->departement=getState($targetcompany->state_id); //TODO deprecated
	if (! empty($targetcompany->state_id) && empty($targetcompany->state))       $targetcompany->state=getState($targetcompany->state_id);

	$reshook=0;
	$stringaddress = '';
	if (is_object($hookmanager))
	{
		$parameters = array('sourcecompany'=>&$sourcecompany,'targetcompany'=>&$targetcompany,'targetcontact'=>$targetcontact,'outputlangs'=>$outputlangs,'mode'=>$mode,'usecontact'=>$usecontact);
		$action='';
		$reshook = $hookmanager->executeHooks('pdf_build_address',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
		$stringaddress.=$hookmanager->resPrint;
	}
	if (empty($reshook))
	{
		if ($mode == 'source')
		{
			$withCountry = 0;
			if (!empty($sourcecompany->country_code) && ($targetcompany->country_code != $sourcecompany->country_code)) $withCountry = 1;

			$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->convToOutputCharset(dol_format_address($sourcecompany, $withCountry, "\n", $outputlangs))."\n";

			if (empty($conf->global->MAIN_PDF_DISABLESOURCEDETAILS))
			{
				// Phone
				if ($sourcecompany->phone)
					$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->transnoentities("PhoneShort").": ".$outputlangs->convToOutputCharset($sourcecompany->phone);
						// Fax
				if ($sourcecompany->fax)
					$stringaddress .= ($stringaddress ? ($sourcecompany->phone ? " - " : "\n") : '' ).$outputlangs->transnoentities("Fax").": ".$outputlangs->convToOutputCharset($sourcecompany->fax);
						// EMail
				if ($sourcecompany->email)
					$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->transnoentities("Email").": ".$outputlangs->convToOutputCharset($sourcecompany->email);
							// Web
				if ($sourcecompany->url)
					$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->transnoentities("Web").": ".$outputlangs->convToOutputCharset($sourcecompany->url);
			}
		}

		if ($mode == 'target' || preg_match('/targetwithdetails/',$mode))
		{
			if ($usecontact)
			{
				$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->convToOutputCharset($targetcontact->getFullName($outputlangs,1));

				if (!empty($targetcontact->address)) {
					$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->convToOutputCharset(dol_format_address($targetcontact))."\n";
				}else {
					$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->convToOutputCharset(dol_format_address($targetcompany))."\n";
				}
								// Country
				if (!empty($targetcontact->country_code) && $targetcontact->country_code != $sourcecompany->country_code)
				{
					$stringaddress.=$outputlangs->convToOutputCharset($outputlangs->transnoentitiesnoconv("Country".$targetcontact->country_code))."\n";
				}
				else if (empty($targetcontact->country_code) && !empty($targetcompany->country_code) && ($targetcompany->country_code != $sourcecompany->country_code)) {
					$stringaddress.=$outputlangs->convToOutputCharset($outputlangs->transnoentitiesnoconv("Country".$targetcompany->country_code))."\n";
				}

				if (
					! empty($conf->global->MAIN_PDF_ADDALSOTARGETDETAILS) || preg_match('/targetwithdetails/',$mode))
				{
					// Phone
					if (! empty($conf->global->MAIN_PDF_ADDALSOTARGETDETAILS) || $mode == 'targetwithdetails' || preg_match('/targetwithdetails_phone/',$mode))
					{
						if (! empty($targetcontact->phone_pro)
							|| ! empty($targetcontact->phone_mobile))
							$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->transnoentities("Phone").": ";
						if (! empty($targetcontact->phone_pro))
							$stringaddress .= $outputlangs->convToOutputCharset($targetcontact->phone_pro);
						if (! empty($targetcontact->phone_pro) && ! empty($targetcontact->phone_mobile))
							$stringaddress .= " / ";
						if (! empty($targetcontact->phone_mobile))
							$stringaddress .= $outputlangs->convToOutputCharset($targetcontact->phone_mobile);
					}
					// Fax
					if (! empty($conf->global->MAIN_PDF_ADDALSOTARGETDETAILS) || $mode == 'targetwithdetails' || preg_match('/targetwithdetails_fax/',$mode))
					{
						if ($targetcontact->fax) $stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->transnoentities("Fax").": ".$outputlangs->convToOutputCharset($targetcontact->fax);
						}
					// EMail
						if (! empty($conf->global->MAIN_PDF_ADDALSOTARGETDETAILS)
							|| $mode == 'targetwithdetails' || preg_match('/targetwithdetails_email/',$mode))
						{
							if ($targetcontact->email)
								$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->transnoentities("Email").": ".$outputlangs->convToOutputCharset($targetcontact->email);
						}
					// Web
						if (! empty($conf->global->MAIN_PDF_ADDALSOTARGETDETAILS)
							|| $mode == 'targetwithdetails' || preg_match('/targetwithdetails_url/',$mode))
						{
							if ($targetcontact->url)
								$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->transnoentities("Web").": ".$outputlangs->convToOutputCharset($targetcontact->url);
						}
					}
				}
				else
				{
					$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->convToOutputCharset(dol_format_address($targetcompany))."\n";
								// Country
					if (!empty($targetcompany->country_code) && $targetcompany->country_code != $sourcecompany->country_code) $stringaddress.=$outputlangs->convToOutputCharset($outputlangs->transnoentitiesnoconv("Country".$targetcompany->country_code))."\n";

					if (! empty($conf->global->MAIN_PDF_ADDALSOTARGETDETAILS)
						|| preg_match('/targetwithdetails/',$mode))
					{
										// Phone
						if (! empty($conf->global->MAIN_PDF_ADDALSOTARGETDETAILS)
							|| $mode == 'targetwithdetails'
							|| preg_match('/targetwithdetails_phone/',$mode))
						{
							if (! empty($targetcompany->phone) || ! empty($targetcompany->phone_mobile))
								$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->transnoentities("Phone").": ";
							if (! empty($targetcompany->phone))
								$stringaddress .= $outputlangs->convToOutputCharset($targetcompany->phone);
							if (! empty($targetcompany->phone) && ! empty($targetcompany->phone_mobile))
								$stringaddress .= " / ";
							if (! empty($targetcompany->phone_mobile))
								$stringaddress .= $outputlangs->convToOutputCharset($targetcompany->phone_mobile);
						}
					// Fax
						if (! empty($conf->global->MAIN_PDF_ADDALSOTARGETDETAILS)
							|| $mode == 'targetwithdetails' || preg_match('/targetwithdetails_fax/',$mode))
						{
							if ($targetcompany->fax)
								$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->transnoentities("Fax").": ".$outputlangs->convToOutputCharset($targetcompany->fax);
						}
					// EMail
						if (! empty($conf->global->MAIN_PDF_ADDALSOTARGETDETAILS)
							|| $mode == 'targetwithdetails' || preg_match('/targetwithdetails_email/',$mode))
						{
							if ($targetcompany->email)
								$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->transnoentities("Email").": ".$outputlangs->convToOutputCharset($targetcompany->email);
						}
										// Web
						if (! empty($conf->global->MAIN_PDF_ADDALSOTARGETDETAILS)
							|| $mode == 'targetwithdetails' || preg_match('/targetwithdetails_url/',$mode))
						{
							if ($targetcompany->url)
								$stringaddress .= ($stringaddress ? "\n" : '' ).$outputlangs->transnoentities("Web").": ".$outputlangs->convToOutputCharset($targetcompany->url);
						}
					}
				}

								// Intra VAT
				if (empty($conf->global->MAIN_TVAINTRA_NOT_IN_ADDRESS))
				{
					if ($targetcompany->tva_intra)
						$stringaddress.="\n".$outputlangs->transnoentities("NIT").': '.$outputlangs->convToOutputCharset($targetcompany->tva_intra);
				}

								// Professionnal Ids
				if (! empty($conf->global->MAIN_PROFID1_IN_ADDRESS) && ! empty($targetcompany->idprof1))
				{
					$tmp=$outputlangs->transcountrynoentities("ProfId1",$targetcompany->country_code);
					if (preg_match('/\((.+)\)/',$tmp,$reg)) $tmp=$reg[1];
					$stringaddress.="\n".$tmp.': '.$outputlangs->convToOutputCharset($targetcompany->idprof1);
				}
				if (! empty($conf->global->MAIN_PROFID2_IN_ADDRESS) && ! empty($targetcompany->idprof2))
				{
					$tmp=$outputlangs->transcountrynoentities("ProfId2",$targetcompany->country_code);
					if (preg_match('/\((.+)\)/',$tmp,$reg)) $tmp=$reg[1];
					$stringaddress.="\n".$tmp.': '.$outputlangs->convToOutputCharset($targetcompany->idprof2);
				}
				if (! empty($conf->global->MAIN_PROFID3_IN_ADDRESS) && ! empty($targetcompany->idprof3))
				{
					$tmp=$outputlangs->transcountrynoentities("ProfId3",$targetcompany->country_code);
					if (preg_match('/\((.+)\)/',$tmp,$reg)) $tmp=$reg[1];
					$stringaddress.="\n".$tmp.': '.$outputlangs->convToOutputCharset($targetcompany->idprof3);
				}
				if (! empty($conf->global->MAIN_PROFID4_IN_ADDRESS) && ! empty($targetcompany->idprof4))
				{
					$tmp=$outputlangs->transcountrynoentities("ProfId4",$targetcompany->country_code);
					if (preg_match('/\((.+)\)/',$tmp,$reg)) $tmp=$reg[1];
					$stringaddress.="\n".$tmp.': '.$outputlangs->convToOutputCharset($targetcompany->idprof4);
				}
				if (! empty($conf->global->MAIN_PROFID5_IN_ADDRESS) && ! empty($targetcompany->idprof5))
				{
					$tmp=$outputlangs->transcountrynoentities("ProfId5",$targetcompany->country_code);
					if (preg_match('/\((.+)\)/',$tmp,$reg)) $tmp=$reg[1];
					$stringaddress.="\n".$tmp.': '.$outputlangs->convToOutputCharset($targetcompany->idprof5);
				}
				if (! empty($conf->global->MAIN_PROFID6_IN_ADDRESS) && ! empty($targetcompany->idprof6))
				{
					$tmp=$outputlangs->transcountrynoentities("ProfId6",$targetcompany->country_code);
					if (preg_match('/\((.+)\)/',$tmp,$reg)) $tmp=$reg[1];
					$stringaddress.="\n".$tmp.': '.$outputlangs->convToOutputCharset($targetcompany->idprof6);
				}

								// Public note
				if (! empty($conf->global->MAIN_PUBLIC_NOTE_IN_ADDRESS))
				{
					if ($mode == 'source' && ! empty($sourcecompany->note_public))
					{
						$stringaddress.="\n".dol_string_nohtmltag($sourcecompany->note_public);
					}
					if (($mode == 'target' || preg_match('/targetwithdetails/',$mode)) && ! empty($targetcompany->note_public))
					{
						$stringaddress.="\n".dol_string_nohtmltag($targetcompany->note_public);
					}
				}
			}
		}

		return $stringaddress;
	}

	/**
 *  Show footer of page for PDF generation
 *
 *	@param	TCPDF			$pdf     		The PDF factory
 *  @param  Translate	$outputlangs	Object lang for output
 * 	@param	string		$paramfreetext	Constant name of free text
 * 	@param	Societe		$fromcompany	Object company
 * 	@param	int			$marge_basse	Margin bottom we use for the autobreak
 * 	@param	int			$marge_gauche	Margin left (no more used)
 * 	@param	int			$page_hauteur	Page height (no more used)
 * 	@param	Object		$object			Object shown in PDF
 * 	@param	int			$showdetails	Show company adress details into footer (0=Nothing, 1=Show address, 2=Show managers, 3=Both)
 *  @param	int			$hidefreetext	1=Hide free text, 0=Show free text
 * 	@return	int							Return height of bottom margin including footer text
	 */
	function pdf_pagefoot_fractal(&$pdf,$outputlangs,$paramfreetext,$fromcompany,$marge_basse,$marge_gauche,$page_hauteur,$object,$showdetails=0,$hidefreetext=0,$showtva=0,$showcapital=0,$showuser=1,$showdateprint=1)
	{
		global $conf,$user;

		$outputlangs->load("dict");
		$line='';

		$dims=$pdf->getPageDimensions();

		// Line of free text
		if (empty($hidefreetext) && ! empty($conf->global->$paramfreetext))
		{
		// Make substitution
			$substitutionarray=array(
				'__FROM_NAME__' => $fromcompany->name,
				'__FROM_EMAIL__' => $fromcompany->email,
				'__TOTAL_TTC__' => $object->total_ttc,
				'__TOTAL_HT__' => $object->total_ht,
				'__TOTAL_VAT__' => $object->total_vat
			);
			complete_substitutions_array($substitutionarray,$outputlangs,$object);
			$newfreetext=make_substitutions($conf->global->$paramfreetext,$substitutionarray);
			$line.=$outputlangs->convToOutputCharset($newfreetext);
		}

	// First line of company infos
		$line1=""; $line2=""; $line3=""; $line4="";

		if ($showdetails == 1 || $showdetails == 3)
		{
		// Company name
			if ($fromcompany->name)
			{
				$line1.=($line1?" - ":"").$outputlangs->transnoentities("RegisteredOffice").": ".$fromcompany->name;
			}
		// Address
			if ($fromcompany->address)
			{
				$line1.=($line1?" - ":"").str_replace("\n", ", ", $fromcompany->address);
			}
		// Zip code
			if ($fromcompany->zip)
			{
				$line1.=($line1?" - ":"").$fromcompany->zip;
			}
		// Town
			if ($fromcompany->town)
			{
				$line1.=($line1?" ":"").$fromcompany->town;
			}
		// Phone
			if ($fromcompany->phone)
			{
				$line2.=($line2?" - ":"").$outputlangs->transnoentities("Phone").": ".$fromcompany->phone;
			}
		// Fax
			if ($fromcompany->fax)
			{
				$line2.=($line2?" - ":"").$outputlangs->transnoentities("Fax").": ".$fromcompany->fax;
			}

		// URL
			if ($fromcompany->url)
			{
				$line2.=($line2?" - ":"").$fromcompany->url;
			}
		// Email
			if ($fromcompany->email)
			{
				$line2.=($line2?" - ":"").$fromcompany->email;
			}
		}
		if ($showdetails == 2 || $showdetails == 3 || ($fromcompany->country_code == 'DE'))
		{
		// Managers
			if ($fromcompany->managers)
			{
				$line2.=($line2?" - ":"").$fromcompany->managers;
			}
		}

	// Line 3 of company infos
	// Juridical status
		if ($fromcompany->forme_juridique_code)
		{
			$line3.=($line3?" - ":"").$outputlangs->convToOutputCharset(getFormeJuridiqueLabel($fromcompany->forme_juridique_code));
		}
	// Capital
		if ($fromcompany->capital && $showcapital)
		{
		$tmpamounttoshow = price2num($fromcompany->capital); // This field is a free string
		if (is_numeric($tmpamounttoshow) && $tmpamounttoshow > 0) $line3.=($line3?" - ":"").$outputlangs->transnoentities("CapitalOf",price($tmpamounttoshow, 0, $outputlangs, 0, 0, 0, $conf->currency));
			else $line3.=($line3?" - ":"").$outputlangs->transnoentities("CapitalOf",$tmpamounttoshow,$outputlangs);
			}
	// Prof Id 1
			if ($fromcompany->idprof1 && ($fromcompany->country_code != 'FR' || ! $fromcompany->idprof2))
			{
				$field=$outputlangs->transcountrynoentities("ProfId1",$fromcompany->country_code);
				if (preg_match('/\((.*)\)/i',$field,$reg)) $field=$reg[1];
				$line3.=($line3?" - ":"").$field.": ".$outputlangs->convToOutputCharset($fromcompany->idprof1);
			}
	// Prof Id 2
			if ($fromcompany->idprof2)
			{
				$field=$outputlangs->transcountrynoentities("ProfId2",$fromcompany->country_code);
				if (preg_match('/\((.*)\)/i',$field,$reg)) $field=$reg[1];
				$line3.=($line3?" - ":"").$field.": ".$outputlangs->convToOutputCharset($fromcompany->idprof2);
			}

			if ($showuser)
			{
				$field=$outputlangs->transcountrynoentities("User",$user->login);
				if (preg_match('/\((.*)\)/i',$field,$reg)) $field=$reg[1];
				$line3.=($line3?" - ":"").$field.": ".$outputlangs->convToOutputCharset($user->login);
			}
			if ($showdateprint)
			{
				$now = dol_now();
				$field=$outputlangs->transcountrynoentities("Date",dol_print_date($now,'dayhour'));
				if (preg_match('/\((.*)\)/i',$field,$reg)) $field=$reg[1];
				$line3.=($line3?" - ":"").$field.": ".$outputlangs->convToOutputCharset(dol_print_date($now,'dayhour'));
			}
	// Line 4 of company infos
	// Prof Id 3
			if ($fromcompany->idprof3)
			{
				$field=$outputlangs->transcountrynoentities("ProfId3",$fromcompany->country_code);
				if (preg_match('/\((.*)\)/i',$field,$reg)) $field=$reg[1];
				$line4.=($line4?" - ":"").$field.": ".$outputlangs->convToOutputCharset($fromcompany->idprof3);
			}
	// Prof Id 4
			if ($fromcompany->idprof4)
			{
				$field=$outputlangs->transcountrynoentities("ProfId4",$fromcompany->country_code);
				if (preg_match('/\((.*)\)/i',$field,$reg)) $field=$reg[1];
				$line4.=($line4?" - ":"").$field.": ".$outputlangs->convToOutputCharset($fromcompany->idprof4);
			}
	// Prof Id 5
			if ($fromcompany->idprof5)
			{
				$field=$outputlangs->transcountrynoentities("ProfId5",$fromcompany->country_code);
				if (preg_match('/\((.*)\)/i',$field,$reg)) $field=$reg[1];
				$line4.=($line4?" - ":"").$field.": ".$outputlangs->convToOutputCharset($fromcompany->idprof5);
			}
	// Prof Id 6
			if ($fromcompany->idprof6)
			{
				$field=$outputlangs->transcountrynoentities("ProfId6",$fromcompany->country_code);
				if (preg_match('/\((.*)\)/i',$field,$reg)) $field=$reg[1];
				$line4.=($line4?" - ":"").$field.": ".$outputlangs->convToOutputCharset($fromcompany->idprof6);
			}
	// IntraCommunautary VAT
			if ($fromcompany->tva_intra != '' && $howtva)
			{
				$line4.=($line4?" - ":"").$outputlangs->transnoentities("NIT").": ".$outputlangs->convToOutputCharset($fromcompany->tva_intra);
			}

			$pdf->SetFont('','',7);
			$pdf->SetDrawColor(224,224,224);

	// The start of the bottom of this page footer is positioned according to # of lines
			$freetextheight=0;
	if ($line)	// Free text
	{
		//$line="eee<br>\nfd<strong>sf</strong>sdf<br>\nghfghg<br>";
		if (empty($conf->global->PDF_ALLOW_HTML_FOR_FREE_TEXT))
		{
			$width=20000; $align='L';	// By default, ask a manual break: We use a large value 20000, to not have automatic wrap. This make user understand, he need to add CR on its text.
			if (! empty($conf->global->MAIN_USE_AUTOWRAP_ON_FREETEXT)) {
				$width=200; $align='C';
			}
			$freetextheight=$pdf->getStringHeight($width,$line);
		}
		else
		{
			$freetextheight=pdfGetHeightForHtmlContent($pdf,dol_htmlentitiesbr($line, 1, 'UTF-8', 0));      // New method (works for HTML content)
			//print '<br>'.$freetextheight;exit;
		}
	}

	$marginwithfooter=$marge_basse + $freetextheight + (! empty($line1)?3:0) + (! empty($line2)?3:0) + (! empty($line3)?3:0) + (! empty($line4)?3:0);
	$posy=$marginwithfooter+0;

	if ($line)	// Free text
	{
		$pdf->SetXY($dims['lm'],-$posy);
		if (empty($conf->global->PDF_ALLOW_HTML_FOR_FREE_TEXT))   // by default
		{
			$pdf->MultiCell(0, 3, $line, 0, $align, 0);
		}
		else
		{
			$pdf->writeHTMLCell($pdf->page_largeur - $pdf->margin_left - $pdf->margin_right, $freetextheight, $dims['lm'], $dims['hk']-$marginwithfooter, dol_htmlentitiesbr($line, 1, 'UTF-8', 0));
		}
		$posy-=$freetextheight;
	}

	$pdf->SetY(-$posy);
	$pdf->line($dims['lm'], $dims['hk']-$posy, $dims['wk']-$dims['rm'], $dims['hk']-$posy);
	$posy--;

	if (! empty($line1))
	{
		$pdf->SetFont('','B',7);
		$pdf->SetXY($dims['lm'],-$posy);
		$pdf->MultiCell($dims['wk']-$dims['rm']-$dims['lm'], 2, $line1, 0, 'C', 0);
		$posy-=3;
		$pdf->SetFont('','',7);
	}

	if (! empty($line2))
	{
		$pdf->SetFont('','B',7);
		$pdf->SetXY($dims['lm'],-$posy);
		$pdf->MultiCell($dims['wk']-$dims['rm']-$dims['lm'], 2, $line2, 0, 'C', 0);
		$posy-=3;
		$pdf->SetFont('','',7);
	}

	if (! empty($line3))
	{
		$pdf->SetXY($dims['lm'],-$posy);
		$pdf->MultiCell($dims['wk']-$dims['rm']-$dims['lm'], 2, $line3, 0, 'C', 0);
	}

	if (! empty($line4))
	{
		$posy-=3;
		$pdf->SetXY($dims['lm'],-$posy);
		$pdf->MultiCell($dims['wk']-$dims['rm']-$dims['lm'], 2, $line4, 0, 'C', 0);
	}

	// Show page nb only on iso languages (so default Helvetica font)
	if (strtolower(pdf_getPDFFont($outputlangs)) == 'helvetica')
	{
		$pdf->SetXY(-22,-$posy);
		if ($pdf->PageNo()>100)
		{
			//print 'xxx'.$pdf->PageNo().'-'.$pdf->getAliasNbPages().'-'.$pdf->getAliasNumPage();exit;
		}
		if (empty($conf->global->MAIN_USE_FPDF)) $pdf->MultiCell(15, 2, $pdf->PageNo().'/'.$pdf->getAliasNbPages(), 0, 'R', 0);
		else $pdf->MultiCell(15, 2, $pdf->PageNo().'/{nb}', 0, 'R', 0);
	}

	return $marginwithfooter;
}

/**
 *   Show miscellaneous information (payment mode, payment term, ...)
 *
 *   @param		PDF			$pdf     		Object PDF
 *   @param		Object		$object			Object to show
 *   @param		int			$posy			Y
 *   @param		Translate	$outputlangs	Langs object
 *   @param		type_page	$type_page		Tipo de pagina 0=portroit; 1=Landscape
 *   @return	void
 */

function pdf_pie_page_mod1(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('','', $default_font_size - 2);

	$pdf->SetDrawColor(128,128,128);
	$pdf->SetFont('','', $default_font_size - 1);
		//$pdf->SetFillColor(220, 255, 220);
	$pdf->SetFillColor(215, 235, 255);

	if($type_page==1)
	{
		$posxresp=11;
		$posxnomap=60;
		$posxcargo=110;
		$posxfir=152;
	}
	else
	{
		$posxresp=11+10;
		$posxnomap=60+25;
		$posxcargo=110+35;
		$posxfir=152+50;
	}

	if($type_page==1){
		$posy+=240;
	}
	else
	{
		$posy=$page_hauteur-21;
	}


	$pdf->SetXY(10, $posy);
	$pdf->MultiCell($marge_gauche-$posxnomap-1,3, $outputlangs->transnoentities('Responsible for the information'),'','L','1');
	$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
			//$pdf->line($this->posxdesc-1, $tab_top, $this->posxdesc-1, $tab_top -5+ $tab_height);

	$pdf->SetXY($posxnomap, $posy);
	$pdf->MultiCell($posxcargo-$posxnomap-1,3, $outputlangs->transnoentities('Name and surname'),'','C','1');
	$pdf->SetXY($posxcargo, $posy);
	$pdf->MultiCell($posxfir-$posxcargo-1,3, $outputlangs->transnoentities('Position'),'','C','1');
	$pdf->SetXY($posxfir,$posy);
	$pdf->MultiCell($posxfir-$posxfir-1,3, $outputlangs->transnoentities('Firms'),'','C','1');
	$posy+=5;
	$posysum = 10;
	$pdf->SetXY($posxresp, $posy);
	$pdf->line($marge_gauche,$posy-5 ,$marge_gauche, $posy+ $posysum);
	$pdf->line($posxnomap,$posy-5 ,$posxnomap, $posy+ $posysum);
	$pdf->line($posxcargo,$posy-5 , $posxcargo, $posy+ $posysum);
	$pdf->line($posxfir,$posy-5, $posxfir, $posy+$posysum);
	$pdf->line($page_largeur-$marge_gauche,$posy-5,$page_largeur-$marge_gauche, $posy+ $posysum);
	$pdf->SetXY($posxresp, $posy+2);
	$pdf->MultiCell(50,2, '','','L','0');
	$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);

	$pdf->SetXY($posxnomap, $posy+2);
	$pdf->MultiCell(50,2, '','','L','0');
	$pdf->SetXY($posxcargo, $posy+2);
	$pdf->MultiCell(50,2, '','','L','0');
	$pdf->SetXY($posxfir, $posy+2);
	$pdf->MultiCell(20-1,2, ' ','','L','0');
	$posy+=$posysum;
	$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
}

function pdf_pie_page_mod2(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('','', $default_font_size - 2);

	$pdf->SetDrawColor(128,128,128);
	$pdf->SetFont('','', $default_font_size - 1);
		//$pdf->SetFillColor(220, 255, 220);
	$pdf->SetFillColor(215, 235, 255);


		//$posy=$page_hauteur-10;
	if($type_page==1)
	{
		$posxresp=11;
		$posxnomap=60;
		$posxcargo=110;
		$posxfir=152;

	}
	else
	{
		$posxresp=11+10;
		$posxnomap=60+25;
		$posxcargo=110+35;
		$posxfir=152+50;
	}

	if($type_page==1){
		$posy+=250;
	}
	else
	{
		$posy=$page_hauteur-36;
	}


		//$this->printRect($pdf,$this->marge_gauche, $posy, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);

	$pdf->SetXY($marge_gauche, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$pdf->MultiCell(70-$marge_gauche-1,3, $outputlangs->transnoentities('Approved by'),'','C','1');
		//$pdf->$posy+=1;
	$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
		//$pdf->SetXY(10, $posy);

	$pdf->SetXY(70,$posy);
	$pdf->MultiCell(140-$marge_gauche-60-1,3, $outputlangs->transnoentities('I received as'),'','C','1');
	$pdf->SetXY(140, $posy);
	$pdf->MultiCell($page_largeur-140-$marge_gauche-1,3, $outputlangs->transnoentities('Accounting department'),'','L','1');



	$pdf->SetXY($posxresp, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=5;
	$pdf->line($marge_gauche,$posy-5 ,$marge_gauche, $posy+ 13);
	$pdf->line(70,$posy-5 ,70, $posy+ 13);
	$pdf->line(140,$posy-5, 140, $posy+13);
	$pdf->line($page_largeur-$marge_gauche,$posy-5,$page_largeur-$marge_gauche, $posy+ 13);
	$posy-=3;
	$pdf->SetXY(10, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=3;

	$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);


	$pdf->SetXY($marge_gauche, $posy+8);
	$pdf->MultiCell(50,2, $langs->trans(''),'','L','0');


	$pdf->SetXY(70, $posy+8);
	$pdf->MultiCell(50,2, '','','L','0');
		//$pdf->SetXY($posxcargo, $posy+2);
		//$pdf->MultiCell(50,2, 'director de empresas CL','','L','0');
	$pdf->SetXY($posxfir, $posy+2);
	//$pdf->MultiCell(20-1,2,$page_largeur,'','L','0');
	$posy+=8;
	//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite-60, $posy);
	//$pdf->line($marge_gauche, $posy+5, $page_largeur-$marge_droite, $posy+5);

		//$posy+=1;
}

// pie de pagina de asignacion de activos
function pdf_pie_page_mod3(&$pdf, $object, $posy, $outputlangs,$page_largeur)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('','', $default_font_size - 2);

	$pdf->SetDrawColor(128,128,128);
		//$pdf->SetFillColor(220"100", 255, 220);
	$pdf->SetFillColor(215, 235, 255);

	$posy+=15;
	$pdf->SetXY(10,$posy);
	$pdf->line(10, $posy, 200, $posy);

	$pdf->SetXY(10,$posy);
	$pdf->SetTextColor(0,0,60);

	$pdf->SetXY(10,$posy);
	$pdf->MultiCell(20, 3, $outputlangs->transnoentities("Nota").".- "."" , '', 'L');
	$posy+=8;

	$pdf->MultiCell(20, 3, $outputlangs->transnoentities("Observations") , '', 'L');
	$posy+=8;
	$pdf->SetXY($posxx,$posy);
	$pdf->line(30, $posy, 70, $posy);
	$pdf->line(85, $posy, 130, $posy);
	$pdf->line(150, $posy, 192, $posy);
	$pdf->SetXY(40,$posy);
	$pdf->SetTextColor(0,0,60);
	$pdf->MultiCell(30, 3, $outputlangs->transnoentities("Fixed Asset Officer"), '', 'L');

	$pdf->SetXY(90,$posy);
	$pdf->SetTextColor(0,0,60);
	$pdf->MultiCell(120, 3, $outputlangs->transnoentities("Fixed Asset Supervisor"), '', 'L');

	$pdf->SetXY(170,$posy);
	$pdf->SetTextColor(0,0,60);
	$pdf->MultiCell(50, 3, $outputlangs->transnoentities("Name"), '', 'L');

	$posy=$pdf->GetY()+1;
	return $posy;


}

	// pie de pagina de pedido_modules

function pdf_pie_page_mod4(&$pdf, $object, $posy, $outputlangs)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

	$pdf->SetFont('','', $default_font_size - 1);

	$pdf->SetFont('','B', $default_font_size + 2);
		//ref
		//$posy = $pdf->GetY();
		//$posy+=6;
	$pdf->SetXY($posxx,$posy);
	$pdf->SetTextColor(0,0,60);
	$pdf->MultiCell(70, 3, $outputlangs->transnoentities("Entregue Conforme"), '', 'R');

	$pdf->SetXY($posxx,$posy);
	$pdf->SetTextColor(0,0,60);
	$pdf->MultiCell(180, 3, $outputlangs->transnoentities("Recibi Conforme"), '', 'R');
	$pdf->line(20, $posy, 80, $posy);
	$pdf->line(130, $posy, 192, $posy);

	$posy=$pdf->GetY()+1;
	return $posy;
}

// pie de pagina de pedido_modules
//se debe enviar datos de cada firmante en el object
function pdf_pie_page_mod5(&$pdf, $object, $posy, $outputlangs)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

	$pdf->SetFont('','', $default_font_size - 1);
	$posxx = 10;
	$pdf->SetFont('','B', $default_font_size);
	$pdf->SetXY($posxx,$posy);
	$pdf->SetTextColor(0,0,60);
	$pdf->MultiCell(40, 3, $outputlangs->transnoentities("Solicitante"), '', 'R');
	$posxx+= 10;
	$pdf->SetXY($posxx,$posy);
	$pdf->SetTextColor(0,0,60);
	$pdf->MultiCell(40, 3, $outputlangs->transnoentities("Aprobado por"), '', 'R');
	$pdf->line(20, $posy, 80, $posy);
	$pdf->line(130, $posy, 192, $posy);
	$posxx+= 10;
	$pdf->SetXY($posxx,$posy);
	$pdf->SetTextColor(0,0,60);
	$pdf->MultiCell(40, 3, $outputlangs->transnoentities("Entregado por"), '', 'R');
	$pdf->line(20, $posy, 80, $posy);
	$pdf->line(130, $posy, 192, $posy);

	$posy=$pdf->GetY()+1;
	return $posy;
}
//con posicion fija
function pdf_pie_page_mod6(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0,$bottomlasttab)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('','', $default_font_size - 2);
	$pdf->SetDrawColor(128,128,128);
	$pdf->SetFont('','', $default_font_size - 1);

	if($type_page==1) $posy+=250;
	else $posy=$page_hauteur-36;

	if($type_page==1){
		$posy=250;
	}
	else
	{
		$posy=$bottomlasttab;
	}

	if ($object->labelusersol || $object->labeluserapp || $object->labeluserent)
	{
		//registramos los nombres de cada uno
		$pdf->SetXY($marge_gauche, $posy+1);
		$pdf->MultiCell(70-$marge_gauche-1,3, $outputlangs->transnoentities($object->labelusersol),'','C','0');
		if ($object->labeluserapp)
		{
			$pdf->SetXY(70,$posy+1);
			$pdf->MultiCell(140-$marge_gauche-60-1,3, $outputlangs->transnoentities($object->labeluserapp),'','C','0');
		}
		if ($object->labeluserent)
		{
			$pdf->SetXY(140, $posy+1);
			$pdf->MultiCell($page_largeur-140-$marge_gauche-1,3, $outputlangs->transnoentities($object->labeluserent),'','C','0');
		}
	}
	//cargos
	if ($object->jobusersol || $object->jobuserapp || $object->jobuserent)
	{
		//registramos los nombres de cada uno
		if ($object->jobusersol)
		{
			$pdf->SetXY($marge_gauche, $posy+6);
			$pdf->MultiCell(70-$marge_gauche-1,3, $outputlangs->transnoentities($object->jobusersol),'','C','0');
		}
		if ($object->jobuserapp)
		{
			$pdf->SetXY(70,$posy+6);
			$pdf->MultiCell(140-$marge_gauche-60-1,3, $outputlangs->transnoentities($object->jobuserapp),'','C','0');
		}
		if ($object->jobuserent)
		{
			$pdf->SetXY(140, $posy+6);
			$pdf->MultiCell($page_largeur-140-$marge_gauche-1,3, $outputlangs->transnoentities($object->jobuserent),'','C','0');
		}
	}
	//fondo de color
	$pdf->SetFillColor(215, 235, 255);

	$pdf->SetXY($marge_gauche, $posy+13);
	$pdf->MultiCell(70-$marge_gauche-1,3, $outputlangs->transnoentities('Requestedby'),'','C','1');
	$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);

	$pdf->SetXY(70,$posy+13);
	$pdf->MultiCell(140-$marge_gauche-60-1,3, $outputlangs->transnoentities('Approvedby'),'','C','1');
	$pdf->SetXY(140, $posy+13);
	$pdf->MultiCell($page_largeur-140-$marge_gauche-1,3, $outputlangs->transnoentities('Deliveredby'),'','C','1');

	$pdf->SetXY($posxresp, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=5;
	$pdf->line($marge_gauche,$posy-5 ,$marge_gauche, $posy+ 13);
	$pdf->line(70,$posy-5 ,70, $posy+ 13);
	$pdf->line(140,$posy-5, 140, $posy+13);
	$pdf->line($page_largeur-$marge_gauche,$posy-5,$page_largeur-$marge_gauche, $posy+ 13);
	$posy-=3;
	$pdf->SetXY(10, $posy);
	$posy+=3;

	$pdf->line($marge_gauche, $posy+13, $page_largeur-$marge_droite, $posy+13);

	$pdf->SetXY($marge_gauche, $posy+8);
	$pdf->MultiCell(50,2, $langs->trans(''),'','L','0');


	$pdf->SetXY(70, $posy+8);
	$pdf->MultiCell(50,2, '','','L','0');
	$pdf->SetXY($posxfir, $posy+2);
	$posy+=8;
}

//con posicion fija
//imprime 2 cuadros
//aprobado por
//recibido por
function pdf_pie_page_mod7(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0,$bottomlasttab)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('','', $default_font_size - 2);
	$pdf->SetDrawColor(128,128,128);
	$pdf->SetFont('','', $default_font_size - 1);

	if($type_page==1) $posy+=250;
	else $posy=$page_hauteur-36;

	if($type_page==1){
		$posy=250;
	}
	else
	{
		$posy=$bottomlasttab;
	}
	/*
	if ($object->labelusersol || $object->labeluserapp || $object->labeluserent)
	{
		//registramos los nombres de cada uno
		$pdf->SetXY($marge_gauche, $posy+1);
		$pdf->MultiCell(70-$marge_gauche-1,3, $outputlangs->transnoentities($object->labelusersol),'','C','0');
		if ($object->labeluserapp)
		{
			$pdf->SetXY(70,$posy+1);
			$pdf->MultiCell(140-$marge_gauche-60-1,3, $outputlangs->transnoentities($object->labeluserapp),'','C','0');
		}
		if ($object->labeluserent)
		{
			$pdf->SetXY(140, $posy+1);
			$pdf->MultiCell($page_largeur-140-$marge_gauche-1,3, $outputlangs->transnoentities($object->labeluserent),'','C','0');
		}
	}
	//cargos
	if ($object->jobusersol || $object->jobuserapp || $object->jobuserent)
	{
		//registramos los nombres de cada uno
		if ($object->jobusersol)
		{
			$pdf->SetXY($marge_gauche, $posy+6);
			$pdf->MultiCell(70-$marge_gauche-1,3, $outputlangs->transnoentities($object->jobusersol),'','C','0');
		}
		if ($object->jobuserapp)
		{
			$pdf->SetXY(70,$posy+6);
			$pdf->MultiCell(140-$marge_gauche-60-1,3, $outputlangs->transnoentities($object->jobuserapp),'','C','0');
		}
		if ($object->jobuserent)
		{
			$pdf->SetXY(140, $posy+6);
			$pdf->MultiCell($page_largeur-140-$marge_gauche-1,3, $outputlangs->transnoentities($object->jobuserent),'','C','0');
		}
	}
	*/
	//fondo de color
	$pdf->SetFillColor(215, 235, 255);

	$pdf->SetXY($marge_gauche, $posy+13);
	$pdf->MultiCell(100-$marge_gauche-1,3, $outputlangs->transnoentities('Recibido por'),'','C','1');
	$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);

	$pdf->SetXY(100,$posy+13);
	$pdf->MultiCell($page_largeur-100-$marge_gauche-1,3, $outputlangs->transnoentities('Aprobado por'),'','C','1');

	$pdf->SetXY($posxresp, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=5;
	$pdf->line($marge_gauche,$posy-5 ,$marge_gauche, $posy+ 13);

	$pdf->line(100,$posy-5 ,100, $posy+ 13);
	$pdf->line($page_largeur-$marge_gauche,$posy-5,$page_largeur-$marge_gauche, $posy+ 13);
	$posy-=3;
	$pdf->SetXY(10, $posy);
	$posy+=3;

	$pdf->line($marge_gauche, $posy+13, $page_largeur-$marge_droite, $posy+13);
	$pdf->SetXY($marge_gauche, $posy+8);

	$pdf->SetXY(70, $posy+8);
	$pdf->MultiCell(50,2, '','','L','0');
	$pdf->SetXY($posxfir, $posy+2);
	$posy+=8;
}

//pie de pàgina que imprime 4 cuadros
//Solicitado por
//Autorizado por
//Entregado por
//Revisado por
//
function pdf_pie_page_mod8(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0,$bottomlasttab)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('','', $default_font_size - 2);
	$pdf->SetDrawColor(128,128,128);
	$pdf->SetFont('','', $default_font_size - 1);

	if($type_page==1) $posy+=250;
	else $posy=$page_hauteur-34;

	if($type_page==1){
		$posy=250;
	}
	else
	{
		$posy=$bottomlasttab;
	}

	if ($object->labelusersol || $object->labeluserapp || $object->labeluserent)
	{
		//registramos los nombres de cada uno
		$pdf->SetXY($marge_gauche-1, $posy-4);
		$pdf->MultiCell(65-$marge_gauche-1,3, $outputlangs->transnoentities($object->labelusersol),'','C','0');
		if ($object->labeluserapp)
		{
			$pdf->SetXY(58,$posy-4);
			$pdf->MultiCell(63-$marge_gauche-1,3, $outputlangs->transnoentities($object->labeluserapp),'','C','0');
		}
		if ($object->labeluserent)
		{
			$pdf->SetXY(110, $posy-4);
			$pdf->MultiCell($page_largeur-155-$marge_gauche-1,3, $outputlangs->transnoentities($object->labeluserent),'','C','0');
		}
	}
	//cargos
	if ($object->jobusersol || $object->jobuserapp || $object->jobuserent)
	{
		//registramos los nombres de cada uno
		if ($object->jobusersol)
		{
			$pdf->SetXY($marge_gauche-1, $posy+1);
			$pdf->MultiCell(65-$marge_gauche-1,3, $outputlangs->transnoentities($object->jobusersol),'','C','0');
		}
		if ($object->jobuserapp)
		{
			$pdf->SetXY(58,$posy+1);
			$pdf->MultiCell(63-$marge_gauche-1,3, $outputlangs->transnoentities($object->jobuserapp),'','C','0');
		}
		if ($object->jobuserent)
		{
			$pdf->SetXY(110, $posy+1);
			$pdf->MultiCell($page_largeur-155-$marge_gauche-1,3, $outputlangs->transnoentities($object->jobuserent),'','C','0');
		}
	}
	//fondo de color
	$pdf->SetFillColor(215, 235, 255);

	$pdf->SetXY($marge_gauche, $posy+13);
	$pdf->MultiCell(65-$marge_gauche-1,3, $outputlangs->transnoentities('Requestedby'),'','C','1');
	//linea superior
	$pdf->line($marge_gauche, $posy-5, $page_largeur-$marge_droite, $posy-5);

	$pdf->SetXY(50,$posy+13);
	//$pdf->MultiCell(75-$marge_gauche,3, $outputlangs->transnoentities('Approvedbysystem'),'','C','1');
	$pdf->MultiCell(75-$marge_gauche,3, $outputlangs->transnoentities('Aprobado en sistema por'),'','C','1');

	$pdf->SetXY(75, $posy+13);
	$pdf->MultiCell($page_largeur-85-$marge_gauche-1,3, $outputlangs->transnoentities('Deliveredby'),'','C','1');

	$pdf->SetXY(160, $posy+13);
	$pdf->MultiCell($page_largeur-160-$marge_gauche-1,3, $outputlangs->transnoentities('Reviewedby'),'','C','1');

	$pdf->SetXY($posxresp, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=5;
	$pdf->line($marge_gauche,$posy-10 ,$marge_gauche, $posy+ 13);
	$pdf->line(60,$posy-10 ,60, $posy+ 13);
	$pdf->line(110,$posy-10, 110, $posy+13);
	$pdf->line(160,$posy-10, 160, $posy+13);
	$pdf->line($page_largeur-$marge_gauche,$posy-10,$page_largeur-$marge_gauche, $posy+ 13);
	$posy-=3;
	$pdf->SetXY(10, $posy);
	$posy+=3;

	$pdf->line($marge_gauche, $posy+13, $page_largeur-$marge_droite, $posy+13);

	$pdf->SetXY($marge_gauche, $posy+8);
	$pdf->MultiCell(50,2, $langs->trans(''),'','L','0');


	$pdf->SetXY(70, $posy+8);
	$pdf->MultiCell(50,2, '','','L','0');
	$pdf->SetXY($posxfir, $posy+2);
	$posy+=8;
}

//con posicion fija
//imprime 2 cuadros
//Entregue conforme
//Recibi conforme
function pdf_pie_page_mod9(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0,$bottomlasttab)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('','', $default_font_size - 2);
	$pdf->SetDrawColor(128,128,128);
	$pdf->SetFont('','', $default_font_size - 1);

	if($type_page==1) $posy+=250;
	else $posy=$page_hauteur-36;

	if($type_page==1){
		$posy=250;
	}
	else
	{
		$posy=$bottomlasttab;
	}
	/*
	if ($object->labelusersol || $object->labeluserapp || $object->labeluserent)
	{
		//registramos los nombres de cada uno
		$pdf->SetXY($marge_gauche, $posy+1);
		$pdf->MultiCell(70-$marge_gauche-1,3, $outputlangs->transnoentities($object->labelusersol),'','C','0');
		if ($object->labeluserapp)
		{
			$pdf->SetXY(70,$posy+1);
			$pdf->MultiCell(140-$marge_gauche-60-1,3, $outputlangs->transnoentities($object->labeluserapp),'','C','0');
		}
		if ($object->labeluserent)
		{
			$pdf->SetXY(140, $posy+1);
			$pdf->MultiCell($page_largeur-140-$marge_gauche-1,3, $outputlangs->transnoentities($object->labeluserent),'','C','0');
		}
	}
	//cargos
	if ($object->jobusersol || $object->jobuserapp || $object->jobuserent)
	{
		//registramos los nombres de cada uno
		if ($object->jobusersol)
		{
			$pdf->SetXY($marge_gauche, $posy+6);
			$pdf->MultiCell(70-$marge_gauche-1,3, $outputlangs->transnoentities($object->jobusersol),'','C','0');
		}
		if ($object->jobuserapp)
		{
			$pdf->SetXY(70,$posy+6);
			$pdf->MultiCell(140-$marge_gauche-60-1,3, $outputlangs->transnoentities($object->jobuserapp),'','C','0');
		}
		if ($object->jobuserent)
		{
			$pdf->SetXY(140, $posy+6);
			$pdf->MultiCell($page_largeur-140-$marge_gauche-1,3, $outputlangs->transnoentities($object->jobuserent),'','C','0');
		}
	}
	*/
	//fondo de color
	$pdf->SetFillColor(215, 235, 255);

	$pdf->SetXY($marge_gauche, $posy+13);
	$pdf->MultiCell(100-$marge_gauche-1,3, $outputlangs->transnoentities('Entregue conforme'),'','C','1');
	$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);

	$pdf->SetXY(100,$posy+13);
	$pdf->MultiCell($page_largeur-100-$marge_gauche-1,3, $outputlangs->transnoentities('Recibi conforme'),'','C','1');

	$pdf->SetXY($posxresp, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=5;
	$pdf->line($marge_gauche,$posy-5 ,$marge_gauche, $posy+ 13);

	$pdf->line(100,$posy-5 ,100, $posy+ 13);
	$pdf->line($page_largeur-$marge_gauche,$posy-5,$page_largeur-$marge_gauche, $posy+ 13);
	$posy-=3;
	$pdf->SetXY(10, $posy);
	$posy+=3;

	$pdf->line($marge_gauche, $posy+13, $page_largeur-$marge_droite, $posy+13);
	$pdf->SetXY($marge_gauche, $posy+8);

	$pdf->SetXY(70, $posy+8);
	$pdf->MultiCell(50,2, '','','L','0');
	$pdf->SetXY($posxfir, $posy+2);
	$posy+=8;
}

// Pie de  3 Firmas para nota de entrega sin entrada de datos
// creado por Luis Miguel Mendoza
function pdf_pie_page_mod10(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0,$bottomlasttab)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

		//Asignamos las el color y el tamanio de fuente
	$pdf->SetDrawColor(128,128,128);
	$pdf->SetFont('','', $default_font_size - 1);
	$pdf->SetFillColor(215, 235, 255);

	if($type_page==1) $posy+=250;
	else $posy=$page_hauteur-36;

	if($type_page==1){
		$posy=250;
	}
	else
	{
		$posy=$bottomlasttab;
	}

		//Dibujamos un rectangulo que cubra al pie de firmas
	//printRect($pdf,10,244,$this->page_largeur-$this->marge_droite-10,17);


		//Campo 1
	$pdf->SetXY($marge_gauche+10, $posy+3);
	$pdf->MultiCell(65-$marge_gauche-1,3, $outputlangs->transnoentities('Elaborado por'),'','C','1');

	$pdf->line($marge_gauche, $posy+6, $page_largeur-$marge_droite, $posy+6);

		//Campo 2
	$pdf->SetXY(70,$posy+3);
	$pdf->MultiCell(140-$marge_gauche-60-1,3, $outputlangs->transnoentities('Aprobado por'),'','C','1');

	$pdf->line($marge_gauche,$posy-5 ,$marge_gauche, $posy+ 13);
		//Campo 3
	$pdf->SetXY(140, $posy+3);
	$pdf->MultiCell($page_largeur-140-$marge_gauche-1,3, $outputlangs->transnoentities('Vo.Bo.'),'','C','1');


	$pdf->SetXY($posxresp, $posy);
			//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=4;

	$pdf->line(70,$posy+3 ,70, $posy-14);
	$pdf->line(140,$posy+3, 140, $posy-14);
	$pdf->line($page_largeur-$marge_gauche,$posy-5,$page_largeur-$marge_gauche, $posy+ 13);

}

//con posicion fija
//imprime 3 cuadros
//Entregue conforme
//Recibi conforme
function pdf_pie_page_mod11(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0,$bottomlasttab)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('','', $default_font_size - 2);
	$pdf->SetDrawColor(128,128,128);
	$pdf->SetFont('','', $default_font_size - 1);

	if($type_page==1) $posy+=250;
	else $posy=$page_hauteur-36;

	if($type_page==1){
		$posy=250;
	}
	else
	{
		$posy=$bottomlasttab;
	}

	//fondo de color
	$pdf->SetFillColor(215, 235, 255);

	$pdf->SetXY($marge_gauche, $posy+13);
	$pdf->MultiCell(100-$marge_gauche-1,3, $outputlangs->transnoentities('Elaborado por'),'','C','1');
	$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);

	$pdf->SetXY(100,$posy+13);
	$pdf->MultiCell($page_largeur-100-$marge_gauche-1,3, $outputlangs->transnoentities('Aprobado por'),'','C','1');

	$pdf->SetXY($posxresp, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=5;
	$pdf->line($marge_gauche,$posy-5 ,$marge_gauche, $posy+ 13);

	$pdf->line(100,$posy-5 ,100, $posy+ 13);
	$pdf->line($page_largeur-$marge_gauche,$posy-5,$page_largeur-$marge_gauche, $posy+ 13);
	$posy-=3;
	$pdf->SetXY(10, $posy);
	$posy+=3;

	$pdf->line($marge_gauche, $posy+13, $page_largeur-$marge_droite, $posy+13);
	$pdf->SetXY($marge_gauche, $posy+8);

	$pdf->SetXY(70, $posy+8);
	$pdf->MultiCell(50,2, '','','L','0');
	$pdf->SetXY($posxfir, $posy+2);
	$posy+=8;
}


/*librearias de Luismiguel*/
function pdf_pie_page_modL1(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0,$bottomlasttab)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('','', $default_font_size - 2);
	$pdf->SetDrawColor(128,128,128);
	$pdf->SetFont('','', $default_font_size - 1);
	$pdf->SetFillColor(215, 235, 255);

		//$posy=$page_hauteur-10;
	/*if($type_page==1)
	{
		$posxresp=11;
		$posxnomap=60;
		$posxcargo=110;
		$posxfir=152;
	}
	else
	{
		$posxresp=11+10;
		$posxnomap=60+25;
		$posxcargo=110+35;
		$posxfir=152+50;
	}*/

	if($type_page==1){
		$posy+=250;
	}
	else
	{
		$posy=$page_hauteur-36;
	}
	if($type_page==1){
		//$posy+=250;
		// Aqui ponemos donde se mostrarael pie de pagina
		$posy+=250;
	}
	else
	{

		$posy=$bottomlasttab+52;
	}
	//$this->printRect($pdf,$this->marge_gauche, $posy, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);

	$pdf->SetXY($marge_gauche, $posy+13);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$pdf->MultiCell(70-$marge_gauche-1,3, $outputlangs->transnoentities('Unidad Solicitante'),'','C','1');
		//$pdf->$posy+=1;
	$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
		//$pdf->SetXY(10, $posy);

	$pdf->SetXY(70,$posy+13);
	$pdf->MultiCell(140-$marge_gauche-60-1,3, $outputlangs->transnoentities('Responsable Autorizado'),'','C','1');
	$pdf->SetXY(140, $posy+13);
	$pdf->MultiCell($page_largeur-140-$marge_gauche-1,3, $outputlangs->transnoentities('General Manager'),'','C','1');

	$pdf->SetXY($posxresp, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=5;
	$pdf->line($marge_gauche,$posy-5 ,$marge_gauche, $posy+ 13);
	$pdf->line(70,$posy-5 ,70, $posy+ 13);
	$pdf->line(140,$posy-5, 140, $posy+13);
	$pdf->line($page_largeur-$marge_gauche,$posy-5,$page_largeur-$marge_gauche, $posy+ 13);
	$posy-=3;
	$pdf->SetXY(10, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=3;

	$pdf->line($marge_gauche, $posy+13, $page_largeur-$marge_droite, $posy+13);


	$pdf->SetXY($marge_gauche, $posy+8);
	$pdf->MultiCell(50,2, $langs->trans(''),'','L','0');


	$pdf->SetXY(70, $posy+8);
	$pdf->MultiCell(50,2, '','','L','0');
		//$pdf->SetXY($posxcargo, $posy+2);
		//$pdf->MultiCell(50,2, 'director de empresas CL','','L','0');
	$pdf->SetXY($posxfir, $posy+2);
	//$pdf->MultiCell(20-1,2,$page_largeur,'','L','0');
	$posy+=8;
	//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite-60, $posy);
	//$pdf->line($marge_gauche, $posy+5, $page_largeur-$marge_droite, $posy+5);

		//$posy+=1;
}

function pdf_pie_page_modL2(&$pdf, $object, $posy, $outputlangs)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('','', $default_font_size - 2);
	$pdf->SetDrawColor(128,128,128);
	$pdf->SetFont('','', $default_font_size - 1);
	$pdf->SetFillColor(215, 235, 255);

	$html = '<table border="1" cellspacing="0" cellpadding="0"><tr><th colspan=2 align=center> Certificacion Presupuestaria </th></tr><tr><td>Certifica que la actividad de referencia cuenta con saldo de recursos en la linea de gasto y por tanto es un gasto elegible que esta acorde a lo programado en el POA 2001...., bajo las siguientes caracteristicas:</td><td> xxx </td></tr></table>';


	$pdf->writeHTML($html, true, false, true, false, '');


}

function pdf_pie_page_modL3(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0,$bottomlasttab)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

	//$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('times', 'L',9);
	//$pdf->SetDrawColor(128,128,128);
	//$pdf->SetFillColor(215, 235, 255);
	//$posy=$page_hauteur-10;

	if($type_page==1)
	{
		$posxresp=11;
		$posxnomap=60;
		$posxcargo=110;
		$posxfir=152;
	}
	else
	{
		$posxresp=11+10;
		$posxnomap=60+25;
		$posxcargo=110+35;
		$posxfir=152+50;
	}

	if($type_page==1){
		//$posy+=250;
		// Aqui ponemos donde se mostrarael pie de pagina
		$posy+=250;
	}
	else
	{

		$posy=$page_hauteur-74;
	}
	if($type_page==1){
		//$posy+=250;
		// Aqui ponemos donde se mostrarael pie de pagina
		$posy+=250;
	}
	else
	{

		$posy=$bottomlasttab+32;
	}
	//$this->printRect($pdf,$this->marge_gauche, $posy, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);

	$pdf->SetXY($marge_gauche, $posy+5);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$pdf->MultiCell(110-$marge_gauche-1,3, $outputlangs->transnoentities('Certifica que las partidas detalladas lineas arriba tienen saldos disponibles'),'','L','');
		//$pdf->$posy+=1;

	//Primera linea horizontal
	$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$pdf->SetXY($marge_gauche+1, $posy+1);
	//$pdf->MultiCell($page_largeur-111-$marge_gauche-1,3, $outputlangs->transnoentities('Certificación Presupuestaria'),'','C','0');
	$pdf->MultiCell(180,3, $outputlangs->transnoentities('Certificación Presupuestaria'),'','C','0');
	//Segunda linea horizontal
	$pdf->line($marge_gauche, $posy+5, $page_largeur-$marge_droite, $posy+5);
		//$pdf->SetXY(10, $posy);

	$pdf->SetXY(111, $posy+5);
	$pdf->MultiCell($page_largeur-111-$marge_gauche-1,3, $outputlangs->transnoentities('Firma'),'','C','0');


	$pdf->SetXY($posxresp, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=5;
	$pdf->line($marge_gauche,$posy-5 ,$marge_gauche, $posy+ 13);


	//linea de el medio
	$pdf->line(110,$posy, 110, $posy+13);

	$pdf->line($page_largeur-$marge_gauche,$posy-5,$page_largeur-$marge_gauche, $posy+ 13);
	$posy-=3;
	$pdf->SetXY(10, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=3;

	$pdf->line($marge_gauche, $posy+13, $page_largeur-$marge_droite, $posy+13);


	$pdf->SetXY($marge_gauche, $posy+8);
	$pdf->MultiCell(50,2, $langs->trans(''),'','L','0');


	$pdf->SetXY(70, $posy+8);
	$pdf->MultiCell(50,2, '','','L','0');
		//$pdf->SetXY($posxcargo, $posy+2);
		//$pdf->MultiCell(50,2, 'director de empresas CL','','L','0');
	$pdf->SetXY($posxfir, $posy+2);
	//$pdf->MultiCell(20-1,2,$page_largeur,'','L','0');
	$posy+=8;
	//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite-60, $posy);
	//$pdf->line($marge_gauche, $posy+5, $page_largeur-$marge_droite, $posy+5);

		//$posy+=1;
}


function pdf_pie_page_modL4(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0,$bottomlasttab)
{
	global $conf,$langs;
	$default_font_size = pdf_getPDFFontSize($outputlangs);

	//$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('times', 'L',9);
	//$pdf->SetDrawColor(128,128,128);
	//$pdf->SetFillColor(215, 235, 255);
	//$posy=$page_hauteur-10;

	if($type_page==1)
	{
		$posxresp=11;
		$posxnomap=60;
		$posxcargo=110;
		$posxfir=152;
	}
	else
	{
		$posxresp=11+10;
		$posxnomap=60+25;
		$posxcargo=110+35;
		$posxfir=152+50;
	}

	if($type_page==1){
		//$posy+=250;
		// Aqui ponemos donde se mostrarael pie de pagina
		$posy+=250;
	}
	else
	{

		$posy=$page_hauteur-55;
	}

	if($type_page==1){
		//$posy+=250;
		// Aqui ponemos donde se mostrarael pie de pagina
		$posy+=250;
	}
	else
	{
		$posy=$bottomlasttab+10;
	}
	//$this->printRect($pdf,$this->marge_gauche, $posy, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);

	$pdf->SetXY($marge_gauche, $posy+5);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$pdf->MultiCell(110-$marge_gauche-1,3, $outputlangs->transnoentities('Certifica que la actividad de referencia está acorde a lo programado en el POA').' '.$object->gestion,'','L','');
		//$pdf->$posy+=1;

	//Primera linea horizontal
	$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$pdf->SetXY($marge_gauche+1, $posy+1);
	//$pdf->MultiCell($page_largeur-111-$marge_gauche-1,3, $outputlangs->transnoentities('Certificacion Presupuestaria'),'','C','0');
	$pdf->MultiCell(180,3, $outputlangs->transnoentities('Certificacion Planificacion'),'','C','0');
	//Segunda linea horizontal
	$pdf->line($marge_gauche, $posy+5, $page_largeur-$marge_droite, $posy+5);
		//$pdf->SetXY(10, $posy);

	$pdf->SetXY(111, $posy+5);
	$pdf->MultiCell($page_largeur-111-$marge_gauche-1,3, $outputlangs->transnoentities('Firma'),'','C','0');


	$pdf->SetXY($posxresp, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=5;
	$pdf->line($marge_gauche,$posy-5 ,$marge_gauche, $posy+ 13);


	//linea de el medio
	$pdf->line(110,$posy, 110, $posy+13);

	$pdf->line($page_largeur-$marge_gauche,$posy-5,$page_largeur-$marge_gauche, $posy+ 13);
	$posy-=3;
	$pdf->SetXY(10, $posy);
		//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
	$posy+=3;

	$pdf->line($marge_gauche, $posy+13, $page_largeur-$marge_droite, $posy+13);


	$pdf->SetXY($marge_gauche, $posy+8);
	$pdf->MultiCell(50,2, $langs->trans(''),'','L','0');


	$pdf->SetXY(70, $posy+8);
	$pdf->MultiCell(50,2, '','','L','0');
		//$pdf->SetXY($posxcargo, $posy+2);
		//$pdf->MultiCell(50,2, 'director de empresas CL','','L','0');
	$pdf->SetXY($posxfir, $posy+2);
	//$pdf->MultiCell(20-1,2,$page_largeur,'','L','0');
	$posy+=8;
	//$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite-60, $posy);
	//$pdf->line($marge_gauche, $posy+5, $page_largeur-$marge_droite, $posy+5);

		//$posy+=1;
}

/************************************ Hasta aqui mis librerias ******************************************************/



?>