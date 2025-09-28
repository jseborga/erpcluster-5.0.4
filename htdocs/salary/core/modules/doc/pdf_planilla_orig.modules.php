<?php
/* Copyright (C) 2004-2012	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012	Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2008		Raphael Bertrand	<raphael.bertrand@resultic.fr>
 * Copyright (C) 2010-2012	Juanjo Menent		<jmenent@2byte.es>
 * Copyright (C) 2012      	Christophe Battarel <christophe.battarel@altairis.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	\file       htdocs/core/modules/facture/doc/pdf_crabe.modules.php
 *	\ingroup    facture
 *	\brief      File of class to generate customers invoices from crabe model
 */

require_once DOL_DOCUMENT_ROOT.'/salary/core/modules/salary/modules_salary.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/report.lib.php';


/**
 *	Class to manage PDF invoice template Crabe
 */
class pdf_planilla_orig extends ModelePDFSalary
{
	var $db;
	var $name;
	var $description;
	var $type;

  var $phpmin = array(4,3,0); // Minimum version of PHP required by module
  var $version = 'dolibarr';

  var $page_largeur;
  var $page_hauteur;
  var $format;
  var $marge_gauche;
  var	$marge_droite;
  var	$marge_haute;
  var	$marge_basse;

  var $emetteur;	// Objet societe qui emet


  /**
   *	Constructor
   *
   *  @param		DoliDB		$db      Database handler
   */
  function __construct($db)
  {
  	global $conf,$langs,$mysoc;

  	$langs->load("main");
  	$langs->load("bills");
  	$langs->load("salary@salary");
  	$this->db = $db;
  	$this->name = "planilla";
  	$this->description = $langs->trans('PDFPlanillaOrigDescription');

	// Dimension page pour format A4
  	$this->type = 'pdf';
  	$formatarray=pdf_getFormat();
  	$this->page_largeur = $formatarray['width'];
  	$this->page_hauteur = $formatarray['height'];
	//modificado para landscape
  	$this->page_hauteur = 215;
  	$this->page_largeur = 415;

  	$this->format = array($this->page_largeur,$this->page_hauteur);
  	$this->marge_gauche=isset($conf->global->MAIN_PDF_MARGIN_LEFT)?$conf->global->MAIN_PDF_MARGIN_LEFT:10;
  	$this->marge_droite=isset($conf->global->MAIN_PDF_MARGIN_RIGHT)?$conf->global->MAIN_PDF_MARGIN_RIGHT:10;
  	$this->marge_haute =isset($conf->global->MAIN_PDF_MARGIN_TOP)?$conf->global->MAIN_PDF_MARGIN_TOP:10;
  	$this->marge_basse =isset($conf->global->MAIN_PDF_MARGIN_BOTTOM)?$conf->global->MAIN_PDF_MARGIN_BOTTOM:10;

	$this->option_logo = 0;                    // Affiche logo
	$this->option_tva = 1;                     // Gere option tva FACTURE_TVAOPTION
	$this->option_modereg = 1;                 // Affiche mode reglement
	$this->option_condreg = 1;                 // Affiche conditions reglement
	$this->option_codeproduitservice = 1;      // Affiche code produit-service
	$this->option_multilang = 1;               // Dispo en plusieurs langues
	$this->option_escompte = 1;                // Affiche si il y a eu escompte
	$this->option_credit_note = 1;             // Support credit notes
	$this->option_freetext = 1;				   // Support add of a personalised text
	$this->option_draft_watermark = 1;		   // Support add of a watermark on drafts

	$this->franchise=!$mysoc->tva_assuj;

	// Get source company
	$this->emetteur=$mysoc;
	if (empty($this->emetteur->country_code)) $this->emetteur->country_code=substr($langs->defaultlang,-2);    // By default, if was not defined

	// Define position of columns
	//posiciones de cada columna
	$this->posxseq=$this->marge_gauche+1;
	$this->posxdoc=19;
	$this->posxnom=34;
	$this->posxcou=68;
	$this->posxnai=75;
	$this->posxsex=90;
	$this->posxcha=96;
	$this->posxdatei=138;
	$this->posxhou=151;
	$this->posxhoud=159;
	$this->posxamo=174;
	$this->posxban=189;
	$this->posxhex=201;
	$this->posxhova=216;
	$this->posxbpro=231;
	$this->posxobon=246;
	$this->posxdom=261;
	$this->posxdomv=276;
	$this->posxtotr=291;
	$this->posxafp=306;
	$this->posxrciva=321;
	$this->posxodesc=336;
	$this->posxtdesc=351;
	$this->posxliq=366;
	$this->posxfirm=381;

	if ($this->page_largeur < 410) // To work with US executive format
	{
		$this->posxgas-=20;
		$this->posxing-=20;
		$this->posxfac-=20;
	}

	$this->tva=array();
	$this->localtax1=array();
	$this->localtax2=array();
	$this->atleastoneratenotnull=0;
	$this->atleastonediscount=0;
}


  /**
   *  Function to build pdf onto disk
   *
   *  @param		Object		$object				Object to generate
   *  @param		Translate	$outputlangs		Lang output object
   *  @param		string		$srctemplatepath	Full path of source filename for generator using a template file
   *  @param		int			$hidedetails		Do not show line details
   *  @param		int			$hidedesc			Do not show desc
   *  @param		int			$hideref			Do not show ref
   *  @param		object		$hookmanager		Hookmanager object
   *  @return     int         	    			1=OK, 0=KO
   */
  function write_file($object,$outputlangs,$srctemplatepath='',$hidedetails=0,$hidedesc=0,$hideref=0,$hookmanager=false,$aList=array())
  {
  	global $user,$langs,$conf,$mysoc,$db,$objectAd,$objectU,$objectCh,$objectCo;
  	if (! is_object($outputlangs)) $outputlangs=$langs;
	// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
  	if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output='ISO-8859-1';

  	$outputlangs->load("main");
  	$outputlangs->load("dict");
  	$outputlangs->load("companies");
  	$outputlangs->load("bills");
  	$outputlangs->load("products");
  	if ($conf->salary->dir_output)
  	{
  		$aParamBoleta = $_SESSION['aParamBoleta'];
  		$fk_period = $aParamBoleta['fk_period'];
	// Definition of $dir and $file
  		if ($object->specimen)
  		{
  			$dir = $conf->facture->dir_output;
  			$file = $dir . "/SPECIMEN.pdf";
  		}
  		else
  		{
  			$objectref = dol_sanitizeFileName($object->codref);
  			$objectrefdoc = dol_sanitizeFileName($object->codref).'PLA';
  			$dir = $conf->salary->dir_output . "/" . $objectref;
  			$file = $dir . "/" . $objectrefdoc . ".pdf";
  		}
  		if (! file_exists($dir))
  		{
  			if (dol_mkdir($dir) < 0)
  			{
  				$this->error=$langs->transnoentities("ErrorCanNotCreateDir",$dir);
  				return 0;
  			}
  		}
  		if (file_exists($dir))
  		{

		//contando los items que tiene cada empleado
  			$nblignessup = count($aList);

  			$pdf=pdf_getInstance($this->format);
		$default_font_size = pdf_getPDFFontSize($outputlangs);	// Must be after pdf_getInstance
		$heightforinfotot = 50;	// Height reserved to output the info and total part
		$heightforfreetext= (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT)?$conf->global->MAIN_PDF_FREETEXT_HEIGHT:5);	// Height reserved to output the free text on last page
		$heightforfooter = $this->marge_basse ;	// Height reserved to output the footer (value include bottom margin)
		$pdf->SetAutoPageBreak(1,0);

		if (class_exists('TCPDF'))
		{
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
		}
		$pdf->SetFont(pdf_getPDFFont($outputlangs));

		// Set path to the background PDF File
		if (empty($conf->global->MAIN_DISABLE_FPDI) && ! empty($conf->global->MAIN_ADD_PDF_BACKGROUND))
		{
			$pagecount = $pdf->setSourceFile($conf->mycompany->dir_output.'/'.$conf->global->MAIN_ADD_PDF_BACKGROUND);
			$tplidx = $pdf->importPage(1);
		}

		$pdf->Open();
		$pagenb=0;
		$pdf->SetDrawColor(128,128,128);

		$pdf->SetTitle($outputlangs->convToOutputCharset($objectref));
		$pdf->SetSubject($outputlangs->transnoentities("Planillasalarial"));
		$pdf->SetCreator("Dolibarr ".DOL_VERSION);
		$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
		$pdf->SetKeyWords($outputlangs->convToOutputCharset($objectref)." ".$outputlangs->transnoentities("Planillamensual"));
		if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);

		$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);   // Left, Top, Right


		//recuperamos el periodo
		$lPeriodClose = false;
		$result = $object->fetch($fk_period);
		if ($result)
		{
			$fk_type_fol = $object->fk_type_fol;
			$fk_proces   = $object->fk_proces;
			$date_close  = $object->date_close;
			if ($object->state == 5 )
				$lPeriodClose = true;
		}
		$i = 0;
		$nblignes = count($aList);

		if ($nblignes > 0)
		{
			$pdf->AddPage();
			if (! empty($tplidx)) $pdf->useTemplate($tplidx);
			$pagenb++;

			$this->_pagehead($pdf, $object, 1, $outputlangs, $hookmanager);

			$pdf->SetFont('','', $default_font_size - 3);
		$pdf->MultiCell(0, 3, '');		// Set interline to 3
		$pdf->SetTextColor(0,0,0);

		$tab_top = 40;
		$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?40:10);
		$tab_height = 130;
		$tab_height_newpage = 150;

		$iniY = $tab_top + 7;
		$curY = $tab_top + 7;
		$nexY = $tab_top + 7;


		$nSumaing = 0;
		$nSumagas = 0;
		$var=True;
		$seq = 1;
		foreach ($aList AS $idUser => $dataUser)
		{
			$objectAd->fetch($idUser);
			$objectU->fetch_user($idUser);
			//$objectCh->fetch($objectU->fk_charge);
			$objectCo->fetch_vigent($idUser,1);
			$objectCh->fetch($objectCo->fk_charge);

			//impresion de secuencial
			if ($objectU->id == $idUser)
			{
				$docum = $objectU->docum;
				$lastnametwo = $objectU->lastnametwo;
			}
			else
			{
				$docum = '';
				$lastnametwo = '';
			}

			//asignando valores del user
			$object->name = $objectAd->lastname.' '.$objectAd->lastnametwo.' '.$objectAd->firstname;


			//basico
			$objres = search_planilla($idUser,'S002',$fk_period,$fk_proces,
				$fk_type_fol,'',$lPeriodClose);



			//revisar lineas
			$curY = $nexY;
			$pdf->SetFont('','', $default_font_size - 3);   // Into loop to work with multipage
			$pdf->SetTextColor(0,0,0);

			$pdf->setTopMargin($tab_top_newpage);
			$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);	// The only function to edit the bottom margin of current page to set it.
			$pageposbefore=$pdf->getPage();
			// Description of product line
			$curX = $this->posxseq-1;

			$showpricebeforepagebreak=1;

			$pdf->startTransaction();
			pdf_writelinedesc($pdf,$object,$i,$outputlangs,$this->posxdoc-$curX,3,$curX,$curY,$hideref,$hidedesc,0,$hookmanager);
			$pageposafter=$pdf->getPage();
			if ($pageposafter > $pageposbefore)	// There is a pagebreak
			{
				$pdf->rollbackTransaction(true);
				$pageposafter=$pageposbefore;
			//print $pageposafter.'-'.$pageposbefore;exit;
			$pdf->setPageOrientation('', 1, $heightforfooter);	// The only function to edit the bottom margin of current page to set it.
			pdf_writelinedesc($pdf,$object,$i,$outputlangs,$this->posxdoc-$curX,4,$curX,$curY,$hideref,$hidedesc,0,$hookmanager);
			$pageposafter=$pdf->getPage();
			$posyafter=$pdf->GetY();
			//var_dump($posyafter); var_dump(($this->page_hauteur - ($heightforfooter+$heightforfreetext+$heightforinfotot))); exit;
			if ($posyafter > ($this->page_hauteur - ($heightforfooter+$heightforfreetext+$heightforinfotot)))	// There is no space left for total+free text
			{
				if ($i == ($nblignes-1))	// No more lines, and no space left to show total, so we create a new page
				{
					$pdf->AddPage('','',true);
					if (! empty($tplidx)) $pdf->useTemplate($tplidx);
					if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)) $this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager);
					$pdf->setPage($pagenb+1);
				}
			}
			else
			{
				// We found a page break
				$showpricebeforepagebreak=0;
			}
		}
			else	// No pagebreak
			{
				$pdf->commitTransaction();
			}

			$nexY = $pdf->GetY();

			$pageposafter=$pdf->getPage();
			$pdf->setPage($pageposbefore);
			$pdf->setTopMargin($this->marge_haute);
			$pdf->setPageOrientation('', 1, 0);	// The only function to edit the bottom margin of current page to set it.

			// We suppose that a too long description is moved completely on next page
			if ($pageposafter > $pageposbefore && empty($showpricebeforepagebreak)) {
				$pdf->setPage($pageposafter); $curY = $tab_top_newpage;
			}
			//fin revisar


			$pdf->SetFont('','', $default_font_size - 3);   // On repositionne la police par defaut
			//numeracion secuencial
			$pdf->SetXY($this->marge_gauche+1, $curY);
			$pdf->MultiCell($this->marge_gauche+1, 3, $seq, 0, 'C', 0);
			$seq++;

			//docum
			$pdf->SetXY($this->posxdoc, $curY);
			$pdf->MultiCell($this->posxnom-$this->posxdoc-1, 3, $docum, 0, 'L', 0);
			//nombre
			$pdf->SetXY($this->posxnom, $curY);
			$pdf->MultiCell($this->posxcou-$this->posxnom-1, 3, $object->name, 0, 'L', 0);

			//countrycode
			$pdf->SetXY($this->posxcou, $curY);
			$pdf->MultiCell($this->posxnai-$this->posxcou-1, 3, $objectAd->country_code, 0, 'L', 0);

			//naiss
			$pdf->SetXY($this->posxnai, $curY);
			$pdf->MultiCell($this->posxsex-$this->posxnai-1, 3, dol_print_date($objectAd->naiss), 0, 'L', 0);
			//sex
			$pdf->SetXY($this->posxsex, $curY);
			$pdf->MultiCell($this->posxcha-$this->posxsex-1, 3, select_sex($objectU->sex,'sex','','',1,1), 0, 'L', 0);
			//charge
			$pdf->SetXY($this->posxcha, $curY);
			$pdf->MultiCell($this->posxdatei-$this->posxcha-1, 3, $objectCh->codref, 0, 'L', 0);
			//date_ini
			$pdf->SetXY($this->posxdatei, $curY);
			$pdf->MultiCell($this->posxhou-$this->posxdatei-1, 3, dol_print_date($objectCo->date_ini), 0, 'L', 0);
			//hours
			$pdf->SetXY($this->posxhou, $curY);
			$pdf->MultiCell($this->posxhoud-$this->posxhou-1, 3, $objres->hours, 0, 'L', 0);
			//hours day
			$pdf->SetXY($this->posxhoud, $curY);
			$pdf->MultiCell($this->posxamo-$this->posxhoud-1, 3, $objres->hoursday, 0, 'L', 0);
			//amount
			$pdf->SetXY($this->posxamo, $curY);
			$pdf->MultiCell($this->posxban-$this->posxamo-1, 3, price($objres->amount), 0, 'R', 0);
			$nTotalRend = $objres->amount;
			//amount
			$pdf->SetXY($this->posxamo, $curY);
			$pdf->MultiCell($this->posxban-$this->posxamo-1, 3, price($objres->amount), 0, 'R', 0);
			//bono antiguedad
			$objres = search_planilla($idUser,'S007',$fk_period,$fk_proces,$fk_type_fol,'',$lPeriodClose);
			$nTotalRend += $objres->amount;
			$pdf->SetXY($this->posxban, $curY);
			$pdf->MultiCell($this->posxhex-$this->posxban-1, 3, price($objres->amount), 0, 'R', 0);
			//horas extras
			$objres = search_planilla($idUser,'S009',$fk_period,$fk_proces,$fk_type_fol,'',$lPeriodClose);
			$pdf->SetXY($this->posxhoin, $curY);
			$pdf->MultiCell($this->posxhova-$this->posxhoin-1, 3, price($objres->hours_info), 0, 'R', 0);
			$pdf->SetXY($this->posxhova, $curY);
			$pdf->MultiCell($this->posxbpro-$this->posxhova-1, 3, price($objres->amount), 0, 'R', 0);
			//bono produccion
			$objres = search_planilla($idUser,'S013',$fk_period,$fk_proces,$fk_type_fol,'',$lPeriodClose);
			$nTotalRend += $objres->amount;
			$pdf->SetXY($this->posxbpro, $curY);
			$pdf->MultiCell($this->posxobon-$this->posxbpro-1, 3, price($objres->amount), 0, 'R', 0);
			//otros bonos
			$objres = search_planilla($idUser,'S003',$fk_period,$fk_proces,$fk_type_fol,'',$lPeriodClose);
			$nTotalRend += $objres->amount;
			$pdf->SetXY($this->posxobon, $curY);
			$pdf->MultiCell($this->posxdom-$this->posxobon-1, 3, price($objres->amount), 0, 'R', 0);
			//dominicales
			$objres = search_planilla($idUser,'S004',$fk_period,$fk_proces,$fk_type_fol,'',$lPeriodClose);
			$nTotalRend += $objres->amount;

			$pdf->SetXY($this->posxdom, $curY);
			$pdf->MultiCell($this->posxdomv-$this->posxdom-1, 3, price($objres->hours_info), 0, 'R', 0);
			$pdf->SetXY($this->posxdomv, $curY);
			$pdf->MultiCell($this->posxtotr-$this->posxdomv-1, 3, price($objres->amount), 0, 'R', 0);

			//total rendimiento
			$pdf->SetXY($this->posxtotr, $curY);
			$pdf->MultiCell($this->posxafp-$this->posxtotr-1, 3, price($nTotalRend), 0, 'R', 0);

			//afp descont
			//afp_riesgo;
			$objres = search_planilla($idUser,'S010',$fk_period,$fk_proces,$fk_type_fol,5,$lPeriodClose);
			$nSumaAfp += $objres->amount;
			$objres = search_planilla($idUser,'S011',$fk_period,$fk_proces,$fk_type_fol,5,$lPeriodClose);
			$nSumaAfp += $objres->amount;
			$objres = search_planilla($idUser,'S012',$fk_period,$fk_proces,$fk_type_fol,5,$lPeriodClose);
			$nSumaAfp += $objres->amount;
			$objres = search_planilla($idUser,'S020',$fk_period,$fk_proces,$fk_type_fol,5,$lPeriodClose);
			$nSumaAfp += $objres->amount;
			$nTotalDesc += $nSumaAfp;
			//descuentos afp
			$pdf->SetXY($this->posxafp, $curY);
			$pdf->MultiCell($this->posxrciva-$this->posxafp-1, 3, price($nSumaAfp), 0, 'R', 0);
			//descuentos rciva
			$objres = search_planilla($idUser,'S040',$fk_period,$fk_proces,$fk_type_fol,5,$lPeriodClose);
			$pdf->SetXY($this->posxrciva, $curY);
			$pdf->MultiCell($this->posxodesc-$this->posxrciva-1, 3, price($objres->amount), 0, 'R', 0);
			$nTotalDesc += $objres->amount;

			//DESCUENTO ANTICIPO
			$objres = search_planilla($idUser,'S041',$fk_period,$fk_proces,$fk_type_fol,5,$lPeriodClose);
			$nSumaDesc += $objres->amount;
			$objres = search_planilla($idUser,'S042',$fk_period,$fk_proces,$fk_type_fol,5,$lPeriodClose);
			$nSumaDesc += $objres->amount;
			$objres = search_planilla($idUser,'S043',$fk_period,$fk_proces,$fk_type_fol,5,$lPeriodClose);
			$nSumaDesc += $objres->amount;
			$objres = search_planilla($idUser,'S044',$fk_period,$fk_proces,$fk_type_fol,5,$lPeriodClose);
			$nSumaDesc += $objres->amount;
			$nTotalDesc += $nSumaDesc;
			//descuentos anticipo
			$pdf->SetXY($this->posxodesc, $curY);
			$pdf->MultiCell($this->posxtdesc-$this->posxodesc-1, 3, price($nSumaDesc), 0, 'R', 0);

			//total descuentos
			$pdf->SetXY($this->posxtdesc, $curY);
			$pdf->MultiCell($this->posxliq-$this->posxtdesc-1, 3, price($nTotalDesc), 0, 'R', 0);

			//liquido
			$pdf->SetXY($this->posxliq, $curY);
			$pdf->MultiCell($this->posxfirm-$this->posxliq-1, 3, price($nTotalRend - $nTotalDesc), 0, 'R', 0);

			//firma
			$pdf->SetXY($this->posxfirm, $curY);
			$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxfirm, 3, '______________', 0, 'L', 0);


			// Add line
			if ( $i < ($nblignes - 1))
			{
				$pdf->SetLineStyle(array('dash'=>'1,1','color'=>array(210,210,210)));
			//$pdf->SetDrawColor(190,190,200);
				$pdf->line($this->marge_gauche, $nexY+1, $this->page_largeur - $this->marge_droite, $nexY+1);
				$pdf->SetLineStyle(array('dash'=>0));
			}

			$nexY+=1;    // Passe espace entre les lignes

			//prueba			      }

			// Show square imprime lineas del cuerpo
			if ($pagenb == 1)
			{
				$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 0, 0);
				$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
			}
			else
			{
				$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 0, 0);
				$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
			}
			$i++;
		}
	}
		// // Affiche zone SUM TOTAL
		// $posy=$this->_tableau_tot($pdf, $object, $deja_regle, $bottomlasttab, $outputlangs,$nSumaing,$nSumagas);

		// Pied de page
	$this->_pagefoot($pdf,$object,$outputlangs);
	$pdf->AliasNbPages();

		//final
	$pdf->Close();

	$pdf->Output($file,'F');

		// Add pdfgeneration hook
	if (! is_object($hookmanager))
	{
		include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
		$hookmanager=new HookManager($this->db);
	}
	$hookmanager->initHooks(array('pdfgeneration'));
	$parameters=array('file'=>$file,'object'=>$object,'outputlangs'=>$outputlangs);
	global $action;
		$reshook=$hookmanager->executeHooks('afterPDFCreation',$parameters,$this,$action);    // Note that $action and $object may have been modified by some hooks

		if (! empty($conf->global->MAIN_UMASK))
			@chmod($file, octdec($conf->global->MAIN_UMASK));

		return 1;   // Pas d'erreur
	}
	else
	{
		$this->error=$langs->trans("ErrorCanNotCreateDir",$dir);
		return 0;
	}
}
else
{
	$this->error=$langs->trans("ErrorConstantNotDefined","FAC_OUTPUTDIR");
	return 0;
}
$this->error=$langs->trans("ErrorUnknown");
	return 0;   // Erreur par defaut
}


  /**
   *   Show miscellaneous information (payment mode, payment term, ...)
   *
   *   @param		PDF			&$pdf     		Object PDF
   *   @param		Object		$object			Object to show
   *   @param		int			$posy			Y
   *   @param		Translate	$outputlangs	Langs object
   *   @return	void
   */
  function _tableau_info(&$pdf, $object, $posy, $outputlangs)
  {
  	global $conf;

  	$default_font_size = pdf_getPDFFontSize($outputlangs);

  	$pdf->SetFont('','', $default_font_size - 1);

	// If France, show VAT mention if not applicable
  	if ($this->emetteur->pays_code == 'FR' && $this->franchise == 1)
  	{
  		$pdf->SetFont('','B', $default_font_size - 2);
  		$pdf->SetXY($this->marge_gauche, $posy);
  		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("VATIsNotUsedForInvoice"), 0, 'L', 0);

  		$posy=$pdf->GetY()+4;
  	}

  	$posxval=52;

	// Show payments conditions
  	if ($object->type != 2 && ($object->cond_reglement_code || $object->cond_reglement))
  	{
  		$pdf->SetFont('','B', $default_font_size - 2);
  		$pdf->SetXY($this->marge_gauche, $posy);
  		$titre = $outputlangs->transnoentities("PaymentConditions").':';
  		$pdf->MultiCell(80, 4, $titre, 0, 'L');

  		$pdf->SetFont('','', $default_font_size - 2);
  		$pdf->SetXY($posxval, $posy);
  		$lib_condition_paiement=$outputlangs->transnoentities("PaymentCondition".$object->cond_reglement_code)!=('PaymentCondition'.$object->cond_reglement_code)?$outputlangs->transnoentities("PaymentCondition".$object->cond_reglement_code):$outputlangs->convToOutputCharset($object->cond_reglement_doc);
  		$lib_condition_paiement=str_replace('\n',"\n",$lib_condition_paiement);
  		$pdf->MultiCell(80, 4, $lib_condition_paiement,0,'L');

  		$posy=$pdf->GetY()+3;
  	}

  	if ($object->type != 2)
  	{
	// Check a payment mode is defined
  		if (empty($object->mode_reglement_code)
  			&& ! $conf->global->FACTURE_CHQ_NUMBER
  			&& ! $conf->global->FACTURE_RIB_NUMBER)
  		{
  			$pdf->SetXY($this->marge_gauche, $posy);
  			$pdf->SetTextColor(200,0,0);
  			$pdf->SetFont('','B', $default_font_size - 2);
  			$pdf->MultiCell(80, 3, $outputlangs->transnoentities("ErrorNoPaiementModeConfigured"),0,'L',0);
  			$pdf->SetTextColor(0,0,0);

  			$posy=$pdf->GetY()+1;
  		}

	// Show payment mode
  		if ($object->mode_reglement_code
  			&& $object->mode_reglement_code != 'CHQ'
  			&& $object->mode_reglement_code != 'VIR')
  		{
  			$pdf->SetFont('','B', $default_font_size - 2);
  			$pdf->SetXY($this->marge_gauche, $posy);
  			$titre = $outputlangs->transnoentities("PaymentMode").':';
  			$pdf->MultiCell(80, 5, $titre, 0, 'L');

  			$pdf->SetFont('','', $default_font_size - 2);
  			$pdf->SetXY($posxval, $posy);
  			$lib_mode_reg=$outputlangs->transnoentities("PaymentType".$object->mode_reglement_code)!=('PaymentType'.$object->mode_reglement_code)?$outputlangs->transnoentities("PaymentType".$object->mode_reglement_code):$outputlangs->convToOutputCharset($object->mode_reglement);
  			$pdf->MultiCell(80, 5, $lib_mode_reg,0,'L');

  			$posy=$pdf->GetY()+2;
  		}

	// Show payment mode CHQ
  		if (empty($object->mode_reglement_code) || $object->mode_reglement_code == 'CHQ')
  		{
		// Si mode reglement non force ou si force a CHQ
  			if (! empty($conf->global->FACTURE_CHQ_NUMBER))
  			{
  				if ($conf->global->FACTURE_CHQ_NUMBER > 0)
  				{
  					$account = new Account($this->db);
  					$account->fetch($conf->global->FACTURE_CHQ_NUMBER);

  					$pdf->SetXY($this->marge_gauche, $posy);
  					$pdf->SetFont('','B', $default_font_size - 3);
  					$pdf->MultiCell(100, 3, $outputlangs->transnoentities('PaymentByChequeOrderedTo',$account->proprio),0,'L',0);
  					$posy=$pdf->GetY()+1;

  					if (empty($conf->global->MAIN_PDF_HIDE_CHQ_ADDRESS))
  					{
  						$pdf->SetXY($this->marge_gauche, $posy);
  						$pdf->SetFont('','', $default_font_size - 3);
  						$pdf->MultiCell(100, 3, $outputlangs->convToOutputCharset($account->adresse_proprio), 0, 'L', 0);
  						$posy=$pdf->GetY()+2;
  					}
  				}
  				if ($conf->global->FACTURE_CHQ_NUMBER == -1)
  				{
  					$pdf->SetXY($this->marge_gauche, $posy);
  					$pdf->SetFont('','B', $default_font_size - 3);
  					$pdf->MultiCell(100, 3, $outputlangs->transnoentities('PaymentByChequeOrderedTo',$this->emetteur->name),0,'L',0);
  					$posy=$pdf->GetY()+1;

  					if (empty($conf->global->MAIN_PDF_HIDE_CHQ_ADDRESS))
  					{
  						$pdf->SetXY($this->marge_gauche, $posy);
  						$pdf->SetFont('','', $default_font_size - 3);
  						$pdf->MultiCell(100, 3, $outputlangs->convToOutputCharset($this->emetteur->getFullAddress()), 0, 'L', 0);
  						$posy=$pdf->GetY()+2;
  					}
  				}
  			}
  		}

	// If payment mode not forced or forced to VIR, show payment with BAN
  		if (empty($object->mode_reglement_code) || $object->mode_reglement_code == 'VIR')
  		{
  			if (! empty($conf->global->FACTURE_RIB_NUMBER))
  			{
  				$account = new Account($this->db);
  				$account->fetch($conf->global->FACTURE_RIB_NUMBER);

  				$curx=$this->marge_gauche;
  				$cury=$posy;

  				$posy=pdf_bank($pdf,$outputlangs,$curx,$cury,$account,0,$default_font_size);

  				$posy+=2;
  			}
  		}
  	}

  	return $posy;
  }


  /**
   *	Show total to pay
   *
   *	@param	PDF			&$pdf           Object PDF
   *	@param  Facture		$object         Object invoice
   *	@param  int			$deja_regle     Montant deja regle
   *	@param	int			$posy			Position depart
   *	@param	Translate	$outputlangs	Objet langs
   *	@return int							Position pour suite
   */
  function _tableau_tot(&$pdf, $object, $deja_regle, $posy, $outputlangs,$sumaing,$sumagas)
  {
  	global $conf,$mysoc;

  	$sign=1;
  	if ($object->type == 2 && ! empty($conf->global->INVOICE_POSITIVE_CREDIT_NOTE)) $sign=-1;

  	$default_font_size = pdf_getPDFFontSize($outputlangs);

  	$tab2_top = $posy;
  	$tab2_hl = 4;
  	$pdf->SetFont('','', $default_font_size - 1);

	// Tableau total
  	$col0x = 100;
  	$col1x = 140;
  	$col2x = 170;
	if ($this->page_largeur < 210) // To work with US executive format
	{
		$col2x-=20;
	}
	$largcol1 = ($this->page_largeur - $this->marge_droite - $col1x);
	$largcol2 = ($this->page_largeur - $this->marge_droite - $col2x);

	$useborder=1;
	$index = 0;

	$widthrecbox = $this->page_largeur-$this->marge_gauche-$this->marge_droite;
	$hautcadre = 5;
	$pdf->Rect($this->marge_gauche, $tab2_top, $widthrecbox, $hautcadre);
	// Total HT
	$pdf->SetFillColor(255,255,255);

	$pdf->SetXY($col0x, $tab2_top + 0);
	$pdf->MultiCell($col0x, $tab2_hl, $outputlangs->transnoentities("Sumas"), 0, 'L', 1);

	$pdf->SetXY($col1x, $tab2_top + 0);
	$pdf->MultiCell($col2x-$col1x-1, $tab2_hl, price($sumaing), 0, 'R', 1);

	$pdf->SetXY($col2x, $tab2_top + 0);
	$pdf->MultiCell($this->page_largeur-$this->marge_droite-$col2x, $tab2_hl, price($sumagas), 0, 'R', 1);

	$index++;
	return ($tab2_top + ($tab2_hl * $index));
}

  /**
   *   Show table for lines
   *
   *   @param		PDF			&$pdf     		Object PDF
   *   @param		string		$tab_top		Top position of table
   *   @param		string		$tab_height		Height of table (rectangle)
   *   @param		int			$nexY			Y (not used)
   *   @param		Translate	$outputlangs	Langs object
   *   @param		int			$hidetop		1=Hide top bar of array and title, 0=Hide nothing, -1=Hide only title
   *   @param		int			$hidebottom		Hide bottom bar of array
   *   @return	void
   */
  function _tableau(&$pdf, $tab_top, $tab_height, $nexY, $outputlangs, $hidetop=0, $hidebottom=0)
  {
  	global $conf;

	// Force to disable hidetop and hidebottom
  	$hidebottom=0;
  	if ($hidetop) $hidetop=-1;

  	$default_font_size = pdf_getPDFFontSize($outputlangs);

	// Amount in (at tab_top - 1)
  	$pdf->SetTextColor(0,0,0);
  	$pdf->SetFont('','', $default_font_size - 3);


  	$pdf->SetDrawColor(128,128,128);
  	$pdf->SetFont('','', $default_font_size - 3);

	// Output Rect
	$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);	// Rect prend une longueur en 3eme param et 4eme param

	// if (empty($hidetop))
	//   {
	// 	$pdf->line($this->marge_gauche, $tab_top+5, $this->page_largeur-$this->marge_droite, $tab_top+5);	// line prend une position y en 2eme param et 4eme param

	// 	$pdf->SetXY($this->posxdesc-1, $tab_top+1);
	// 	$pdf->MultiCell(108,2, $outputlangs->transnoentities("Designation"),'','L');
	//   }


	//seq
	$pdf->line($this->posxseq-1, $tab_top, $this->posxseq-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxseq-1, $tab_top+1);
		$pdf->MultiCell($this->posxdoc-$this->posxseq-1,2, $outputlangs->transnoentities("Seq"),'','C');
	}

	//doc
	$pdf->line($this->posxdoc-1, $tab_top, $this->posxdoc-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxdoc-1, $tab_top+1);
		$pdf->MultiCell($this->posxnom-$this->posxdoc-1,2, $outputlangs->transnoentities("Docum."),'','C');
	}
	//nome
	$pdf->line($this->posxnom-1, $tab_top, $this->posxnom-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxnom-1, $tab_top+1);
		$pdf->MultiCell($this->posxnai-$this->posxnom+1,2, $outputlangs->transnoentities("Name"),'','C');
	}
	//cou
	$pdf->line($this->posxcou-1, $tab_top, $this->posxcou-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxcou-1, $tab_top+1);
		$pdf->MultiCell($this->posxnai-$this->posxcou+1,2, $outputlangs->transnoentities("Pais"),'','C');
	}

	//nai
	$pdf->line($this->posxnai-1, $tab_top, $this->posxnai-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxnai-1, $tab_top+1);
		$pdf->MultiCell($this->posxsex-$this->posxnai+1,2, $outputlangs->transnoentities("Fecha Nac."),'','C');
	}
	//sex
	$pdf->line($this->posxsex-1, $tab_top, $this->posxsex-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxsex-1, $tab_top+1);
		$pdf->MultiCell($this->posxcha-$this->posxsex+1,2, $outputlangs->transnoentities("Sex"),'','C');
	}
	//cha
	$pdf->line($this->posxcha-1, $tab_top, $this->posxcha-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxcha-1, $tab_top+1);
		$pdf->MultiCell($this->posxdatei-$this->posxcha+1,2, $outputlangs->transnoentities("Charge"),'','C');
	}
	//datei
	$pdf->line($this->posxdatei-1, $tab_top, $this->posxdatei-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxdatei-1, $tab_top+1);
		$pdf->MultiCell($this->posxhou-$this->posxdatei+1,2, $outputlangs->transnoentities("Dateing"),'','C');
	}
	//hou
	$pdf->line($this->posxhou-1, $tab_top, $this->posxhou-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxhou-1, $tab_top+1);
		$pdf->MultiCell($this->posxhoud-$this->posxhou+1,2, $outputlangs->transnoentities("Dias"),'','C');
	}
	//houd
	$pdf->line($this->posxhoud-1, $tab_top, $this->posxhoud-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxhoud-1, $tab_top+1);
		$pdf->MultiCell($this->posxamo-$this->posxhoud+1,2, $outputlangs->transnoentities("Horas"),'','C');
	}
	//amount
	$pdf->line($this->posxamo-1, $tab_top, $this->posxamo-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxamo-1, $tab_top+1);
		$pdf->MultiCell($this->posxban-$this->posxamo+1,2, $outputlangs->transnoentities("Basico"),'','C');
	}
	//ban
	$pdf->line($this->posxban-1, $tab_top, $this->posxban-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxban-1, $tab_top+1);
		$pdf->MultiCell($this->posxhex-$this->posxban+1,2, $outputlangs->transnoentities("Bono Antig."),'','C');
	}
	//hex
	$pdf->line($this->posxhex-1, $tab_top, $this->posxhex-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxhex-1, $tab_top+1);
		$pdf->MultiCell($this->posxhova-$this->posxhex+1,2, $outputlangs->transnoentities("Horas Ext."),'','C');
	}
	//hova
	$pdf->line($this->posxhova-1, $tab_top, $this->posxhova-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxhova-1, $tab_top+1);
		$pdf->MultiCell($this->posxbpro-$this->posxhova+1,2, $outputlangs->transnoentities("Horas Ext.V."),'','C');
	}
	//bpro
	$pdf->line($this->posxbpro-1, $tab_top, $this->posxbpro-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxbpro-1, $tab_top+1);
		$pdf->MultiCell($this->posxobon-$this->posxbpro+1,2, $outputlangs->transnoentities("Bono Prod."),'','C');
	}
	//obon
	$pdf->line($this->posxobon-1, $tab_top, $this->posxobon-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxobon-1, $tab_top+1);
		$pdf->MultiCell($this->posxdom-$this->posxobon+1,2, $outputlangs->transnoentities("Otrosbonos"),'','C');
	}
	//dom
	$pdf->line($this->posxdom-1, $tab_top, $this->posxdom-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxdom-1, $tab_top+1);
		$pdf->MultiCell($this->posxdomv-$this->posxdom+1,2, $outputlangs->transnoentities("Dominical"),'','C');
	}
	//domv
	$pdf->line($this->posxdomv-1, $tab_top, $this->posxdomv-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxdomv-1, $tab_top+1);
		$pdf->MultiCell($this->posxtotr-$this->posxdomv+1,2, $outputlangs->transnoentities("Dominicalv"),'','C');
	}
	//totr
	$pdf->line($this->posxtotr-1, $tab_top, $this->posxtotr-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxtotr-1, $tab_top+1);
		$pdf->MultiCell($this->posxafp-$this->posxtotr+1,2, $outputlangs->transnoentities("Totaling"),'','C');
	}
	//afp
	$pdf->line($this->posxafp-1, $tab_top, $this->posxafp-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxafp-1, $tab_top+1);
		$pdf->MultiCell($this->posxrciva-$this->posxafp+1,2, $outputlangs->transnoentities("AFP"),'','C');
	}
	//rciva
	$pdf->line($this->posxrciva-1, $tab_top, $this->posxrciva-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxrciva-1, $tab_top+1);
		$pdf->MultiCell($this->posxodesc-$this->posxrciva+1,2, $outputlangs->transnoentities("RC-IVA"),'','C');
	}
	//odesc
	$pdf->line($this->posxodesc-1, $tab_top, $this->posxodesc-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxodesc-1, $tab_top+1);
		$pdf->MultiCell($this->posxtdesc-$this->posxodesc+1,2, $outputlangs->transnoentities("Otrosdesc"),'','C');
	}
	//tdesc
	$pdf->line($this->posxtdesc-1, $tab_top, $this->posxtdesc-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxtdesc-1, $tab_top+1);
		$pdf->MultiCell($this->posxliq-$this->posxtdesc+1,2, $outputlangs->transnoentities("Totaldesc"),'','C');
	}
	//liq
	$pdf->line($this->posxliq-1, $tab_top, $this->posxliq-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxliq-1, $tab_top+1);
		$pdf->MultiCell($this->posxfirm-$this->posxliq+1,2, $outputlangs->transnoentities("Liquid"),'','C');
	}

	//firma
	$pdf->line($this->posxfirm, $tab_top, $this->posxfirm, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($this->posxfirm-1, $tab_top+1);
		$pdf->MultiCell($this->page_largeur-$this->posxfirm+1,2, $outputlangs->transnoentities("Firma"),'','C');
	}
}

  /**
   *  Show top header of page.
   *
   *  @param	PDF			&$pdf     		Object PDF
   *  @param  Object		$object     	Object to show
   *  @param  int	    	$showaddress    0=no, 1=yes
   *  @param  Translate	$outputlangs	Object lang for output
   *  @param	object		$hookmanager	Hookmanager object
   *  @return	void
   */
  function _pagehead(&$pdf, $object, $showaddress, $outputlangs, $hookmanager)
  {
  	global $conf,$langs;

  	$outputlangs->load("main");
  	$outputlangs->load("bills");
  	$outputlangs->load("propal");
  	$outputlangs->load("companies");

  	$default_font_size = pdf_getPDFFontSize($outputlangs);

  	pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

  	$pdf->SetTextColor(0,0,60);
  	$pdf->SetFont('','B', $default_font_size + 3);

  	$posy=$this->marge_haute;
  	$posx=$this->page_largeur-$this->marge_droite-100;

  	$pdf->SetXY($this->marge_gauche,$posy);

	// Logo
  	$logo=$conf->mycompany->dir_output.'/logos/'.$this->emetteur->logo;
  	if ($this->emetteur->logo)
  	{
  		if (is_readable($logo))
  		{
  			$height=pdf_getHeightForLogo($logo);
		$pdf->Image($logo, $this->marge_gauche, $posy, 0, $height);	// width=0 (auto)
	}
	else
	{
		$pdf->SetTextColor(200,0,0);
		$pdf->SetFont('','B',$default_font_size - 2);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("ErrorLogoFileNotFound",$logo), 0, 'L');
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("ErrorGoToGlobalSetup"), 0, 'L');
	}
}
else
{
	$text=$this->emetteur->name;
	$pdf->MultiCell(100, 4, $outputlangs->convToOutputCharset($text), 0, 'L');
}

$pdf->SetFont('','B', $default_font_size + 3);
$pdf->SetXY($posx,$posy);
$pdf->SetTextColor(0,0,60);
$title=$outputlangs->transnoentities("Salary sould");
$pdf->MultiCell(80, 3, $title, '', 'L');

$pdf->SetFont('','B',$default_font_size);

$posy+=5;
$pdf->SetXY($posx,$posy);
$pdf->SetTextColor(0,0,60);
$pdf->MultiCell(100, 4, $outputlangs->transnoentities("Period")." : " . $outputlangs->convToOutputCharset($object->mes).'/'.$outputlangs->convToOutputCharset($object->anio), '', 'R');

$posy+=1;


	// Show list of linked objects
$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size, $hookmanager);

if ($showaddress)
{
	// Sender properties
	$carac_emetteur = pdf_build_address($outputlangs,$this->emetteur);

	// Show sender
	$posy=42;
	$posx=$this->marge_gauche;
	if (! empty($conf->global->MAIN_INVERT_SENDER_RECIPIENT)) $posx=$this->page_largeur-$this->marge_droite-80;
	$hautcadre=25;

	// Show sender frame
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('','', $default_font_size - 2);
	$pdf->SetXY($posx,$posy-5);
	$pdf->MultiCell(66,5, $outputlangs->transnoentities("Pagadoa").":", 0, 'L');
	$pdf->SetXY($posx,$posy);
	$pdf->SetFillColor(230,230,230);
	$pdf->MultiCell(95, $hautcadre, "", 0, 'R', 1);
	$pdf->SetTextColor(0,0,60);

	// Show sender name
	$pdf->SetXY($posx+2,$posy+3);
	$pdf->SetFont('','B', $default_font_size);
	$pdf->MultiCell(80, 4, $outputlangs->convToOutputCharset($object->name), 0, 'L');

	// Show sender information
	$pdf->SetXY($posx+2,$posy+8);
	$pdf->SetFont('','', $default_font_size - 1);
	$pdf->MultiCell(80, 4, $carac_emetteur, 0, 'L');



	// If BILLING contact defined on invoice, we use it
	$usecontact=false;
	$arrayidcontact=$object->getIdContact('external','BILLING');
	if (count($arrayidcontact) > 0)
	{
		$usecontact=true;
		$result=$object->fetch_contact($arrayidcontact[0]);
	}

	// Recipient name
	if (! empty($usecontact))
	{
		// On peut utiliser le nom de la societe du contact
		if (! empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) $socname = $object->contact->socname;
		else $socname = $object->client->nom;
		$carac_client_name=$outputlangs->convToOutputCharset($socname);
	}
	else
	{
		$carac_client_name=$outputlangs->convToOutputCharset($object->client->nom);
	}

	$carac_client=pdf_build_address($outputlangs,$this->emetteur,$object->client,($usecontact?$object->contact:''),$usecontact,'target');

	// Show recipient
	$widthrecbox=100;
	if ($this->page_largeur < 210) $widthrecbox=84;	// To work with US executive format
	$posy=42;
	$posx=$this->page_largeur-$this->marge_droite-$widthrecbox;
	if (! empty($conf->global->MAIN_INVERT_SENDER_RECIPIENT)) $posx=$this->marge_gauche;

	// Show recipient frame
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('','', $default_font_size - 2);
	$pdf->SetXY($posx+2,$posy-5);
	$pdf->MultiCell($widthrecbox, 5, $outputlangs->transnoentities("Infocontract").":",0,'L');
	$pdf->Rect($posx, $posy, $widthrecbox, $hautcadre);

	// Show recipient name
	$pdf->SetXY($posx+2,$posy+3);
	$pdf->SetFont('','B', $default_font_size);
	$pdf->MultiCell($widthrecbox, 4, 'Contrato '.$carac_client_name, 0, 'L');

	// Show recipient information
	$pdf->SetFont('','', $default_font_size - 1);
	$pdf->SetXY($posx+2,$posy+9+(dol_nboflines_bis($carac_client_name,50)*4));
	$pdf->MultiCell($widthrecbox, 4, 'Fecha '.$carac_client, 0, 'L');
}
}

  /**
   *   	Show footer of page. Need this->emetteur object
   *
   *   	@param	PDF			&$pdf     			PDF
   * 		@param	Object		$object				Object to show
   *      @param	Translate	$outputlangs		Object lang for output
   *      @param	int			$hidefreetext		1=Hide free text
   *      @return	int								Return height of bottom margin including footer text
   */
  function _pagefoot(&$pdf,$object,$outputlangs,$hidefreetext=0)
  {
  	return pdf_pagefoot($pdf,$outputlangs,'MENSAJE LIBRE DE TEXTO',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
  }

}

?>
