<?php
/* Copyright (C) 2017	L.Miguel Mendoza	<l.mendoza.liet@gmail.com>
 * Modelo de Reporte Vertical
 */

require_once DOL_DOCUMENT_ROOT.'/contab/core/modules/contab/modules_contab.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/utils.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf_fractal.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/utils.lib.php';

/*require_once DOL_DOCUMENT_ROOT.'/poa/class/poareformulatedext.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/class/poareformulateddetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/class/poaobjetiveext.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/cpartidaext.class.php';*/

class pdf_seat extends ModelePDFContab
{
	var $db;
	var $name;
	var $um;
	var $description;
	var $type;

	var $phpmin = array(4,3,0);
   // Minimum version of PHP required by module
	var $version = 'dolibarr';

	var $page_largeur;
	var $page_hauteur;
	var $format;
	var $marge_gauche;
	var $marge_droite;
	var $marge_haute;
	var $marge_basse;

	var $emetteur;

	//Pocion que se mostrara el reporte 0 = Horizontal y 1 = Vertical
	var $type_page = 1;

	function __construct($db)
	{
		global $conf,$langs,$mysoc;

		$langs->load("main");
		$langs->load("bills");
		$titulo = "FORMULARIO PARA SOLICITAR MODIFICACIONES EN LA PROGRAMACION DEL POA";
		$this->db = $db;
		$this->name = "seat";
		$this->description = $langs->trans('PDFDeterminationserver');

		// Dimension page pour format A4
		if($this->type_page==1)
		{
			$this->type = 'pdf';
			$formatarray=pdf_getFormat();
			$this->page_largeur = $formatarray['width'];
			//$this->page_hauteur = (int)($formatarray['height']/2);
			$this->page_hauteur = ($formatarray['height']);
		}
		else
		{

			$this->type = 'pdf';
			$formatarray=pdf_getFormat();
			$this->page_hauteur = $formatarray['width'];
			//$this->page_hauteur = (int)($formatarray['height']/2);
			$this->page_largeur = ($formatarray['height']);
		}

		$this->format = array($this->page_largeur,$this->page_hauteur);
		$this->marge_gauche=isset($conf->global->MAIN_PDF_MARGIN_LEFT)?$conf->global->MAIN_PDF_MARGIN_LEFT:10;
		$this->marge_droite=isset($conf->global->MAIN_PDF_MARGIN_RIGHT)?$conf->global->MAIN_PDF_MARGIN_RIGHT:10;
		$this->marge_haute =isset($conf->global->MAIN_PDF_MARGIN_TOP)?$conf->global->MAIN_PDF_MARGIN_TOP:10;
		$this->marge_basse =isset($conf->global->MAIN_PDF_MARGIN_BOTTOM)?$conf->global->MAIN_PDF_MARGIN_BOTTOM:10;

		$this->option_logo = 1;
		// Affiche logo
		// $this->option_tva = 1;
		// Gere option tva FACTURE_TVAOPTION
		$this->option_modereg = 1;
		// Affiche mode reglement
		$this->option_condreg = 1;
		// Affiche conditions reglement
		$this->option_codeproduitservice = 1;
		// Affiche code produit-service
		$this->option_multilang = 1;
		// Dispo en plusieurs langues
		$this->option_escompte = 1;
		// Affiche si il y a eu escompte
		$this->option_credit_note = 1;
		// Support credit notes
		$this->option_freetext = 1;
		// Support add of a personalised text
		$this->option_draft_watermark = 1;
		// Support add of a watermark on drafts

		$this->franchise=!$mysoc->tva_assuj;

		// Get source company
		$this->emetteur=$mysoc;
		if (empty($this->emetteur->country_code)) $this->emetteur->country_code=substr($langs->defaultlang,-2);
		// By default, if was not defined

		// Define position of columns
		if($this->type_page==1)
		{
			//Posiciones para mostrar los textos de manera Vertical
			$this->posxini=$this->marge_gauche+1;
			$this->posxcodi = 35;
			$this->posxdesc = 120;
			$this->posxparc = 150;
			$this->posxdebe = 180;
			$this->posxfin = 207;
		}
		else
		{
			//Posiciones para mostrar los textos de manera horizontal
			$this->posxini=$this->marge_gauche+1;
			$this->posxcodi = 45;
			$this->posxdesc = 150;
			$this->posxparc = 190;
			$this->posxdebe = 230;
			$this->posxfin = 270;
		}


		if($this->type_page==1)
		{


			if ($this->page_largeur < 210)
		// To work with US executive format
			{
				$this->posxuse-=10;
				$this->posxent-=10;
				$this->posxsal-=10;
				$this->posxval-=10;
			}

		}
		else
		{

			if ($this->page_largeur < 297)
		//if ($this->page_largeur < 297)
		// To work with US executive format
			{
				$this->posxqty-=10;
				$this->posxdesc-=10;
				$this->posxnpu-=10;
				$this->posxnpr-=10;
			}

		}

		$this->atleastoneratenotnull=0;
		$this->atleastonediscount=0;
	}

	function write_file($object,$outputlangs,$srctemplatepath='',$hidedetails=0,$hidedesc=0,$hideref=0,$hookmanager=false)
	{
		global $user,$langs,$conf,$mysoc,$db;

		//$product = new Product($this->db);
		if (! is_object($outputlangs)) $outputlangs=$langs;
		// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
		if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output='ISO-8859-1';

		$outputlangs->load("main");
		$outputlangs->load("dict");
		$outputlangs->load("companies");
		$outputlangs->load("bills");
		$outputlangs->load("products");
		$outputlangs->load("poa@poa");

		if ($conf->contab->dir_output)
		{
			// Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->contab->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
				$period_year = $_SESSION['period_year'];
				$dir = $conf->contab->dir_output . "/contab/".$period_year.'/seat/'.$object->id;
				if (empty($object->status))
					$file = $dir . "/seat.pdf";
				else
					$file = $dir . "/seat_".$object->ref.".pdf";
			}
			if (! file_exists($dir))
			{
				if (dol_mkdir($dir) < 0)
				{
					$this->error=$outputlangs->transnoentitiesnoentities("ErrorCanNotCreateDir",$dir);
					return 0;
				}
			}

			//Rescatamos la sesiones enviadas
			$aReporte = unserialize($_SESSION['aReporte']);

			if (file_exists($dir))
			{
				$nblignes = count($aReporte[2]);
				//echo "entra aqui si exixte file";
				if($this->type_page==1)
				{
					$pdf=pdf_getInstance($this->format);
				}
				else
				{
					$pdf=pdf_getInstance($this->format,'mm','L');
				}


				$default_font_size = pdf_getPDFFontSize($outputlangs);
				// Must be after pdf_getInstance
				$heightforinfotot = 1;
				// 50 Height reserved to output the info and total part
				$heightforfreetext= (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT)?$conf->global->MAIN_PDF_FREETEXT_HEIGHT:5);
				// Height reserved to output the free text on last page
				$heightforfooter = $this->marge_basse + 60;
				//2  Height reserved to output the footer (value include bottom margin)
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

				$pdf->SetTitle($outputlangs->convToOutputCharset($object->ref));
				$pdf->SetSubject($outputlangs->transnoentities("Contab"));
				$pdf->SetCreator("Dolibarr ".DOL_VERSION);
				$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
				$pdf->SetKeyWords($outputlangs->convToOutputCharset($object->ref));
				if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);

				$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);
				// New page


				if($this->type_page==1)
				{
					$pdf->AddPage();
				}
				else
				{
					$pdf->AddPage('L');
				}

				//$pdf->AddPage();
				if (! empty($tplidx)) $pdf->useTemplate($tplidx);
				$pagenb++;

				$this->_pagehead($pdf, $object, 1, $outputlangs, $hookmanager,1);


				//$pdf->SetFont('','', $default_font_size - 1);
				$pdf->SetFont('','', 9);
				$pdf->MultiCell(0, 3, '');
				// Set interline to 3
				$pdf->SetTextColor(0,0,0);

				//linea para el cuerpo
				$tab_top = 60;
				$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?60:10);
				//38
				$tab_height = 150;
				//130
				$tab_height_newpage = 150;
				//150


				$iniY = $tab_top +4;
				//4
				$curY = $tab_top +12;
				//4
				$nexY = $tab_top +12;
				//4
				// Loop on each lines
				$sumaTotal = 0;
				//$pdf->SetFillColor(220, 255, 220);
				$pdf->SetFillColor(100, 149, 273);

				$j = 0;

				foreach ((array) $aReporte[2] as $j => $line)
				{
					$curY = $nexY;
					$pdf->SetFont('','', $default_font_size-2);
						// Into loop to work with multipage
					$pdf->SetTextColor(0,0,0);
					$pdf->setTopMargin($tab_top_newpage);
					$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);
						// The only function to edit the bottom margin of current page to set it.
					$pageposbefore=$pdf->getPage();
							// date
						//$curX = $this->posxdate-1;
					$showpricebeforepagebreak=1;

					$pdf->SetXY($this->posxini, $curY);
					$pdf->MultiCell($this->posxcodi-$this->posxini-1, 1, $line['cuenta'], 0, 'L',0);

					$pdf->SetXY($this->posxcodi, $curY);
					$pdf->MultiCell($this->posxdesc-$this->posxcodi-1, 1, dol_trunc($line['label'],40), 0, 'L',0);

					if($line['partial'] > 0){
						$pdf->SetXY($this->posxdesc, $curY);
						$pdf->MultiCell($this->posxparc-$this->posxdesc-1, 1, price(price2num($line['partial'],'MT')), 0, 'R',0);
					}else{
						$pdf->SetXY($this->posxdesc, $curY);
						$pdf->MultiCell($this->posxparc-$this->posxdesc-1, 1, '', 0, 'R',0);
					}

					if($line['montoDebito'] > 0){
						$pdf->SetXY($this->posxparc, $curY);
						$pdf->MultiCell($this->posxdebe-$this->posxparc-1, 1, price(price2num($line['montoDebito'],'MT')), 0, 'R',0);
					}else{
						$pdf->SetXY($this->posxparc, $curY);
						$pdf->MultiCell($this->posxdebe-$this->posxparc-1, 1, $line['montoDebito'], 0, 'R',0);
					}

					if($line['montoCredito'] > 0){
						$pdf->SetXY($this->posxdebe, $curY);
						$pdf->MultiCell($this->posxhabe-$this->posxdebe-1, 1,price(price2num($line['montoCredito'],'MT')), 0, 'R',0);
					}else{
						$pdf->SetXY($this->posxdebe, $curY);
						$pdf->MultiCell($this->posxfin-$this->posxdebe-1, 1,$line['montoCredito'], 0, 'R',0);
					}



					$nexY = $pdf->GetY();
						//Aqui termina de escribir y hacemos el salto de linea

						//$nexY = $max;
					$pageposafter=$pdf->getPage();
					$pdf->setPage($pageposbefore);
					$pdf->setTopMargin($this->marge_haute);
					$pdf->setPageOrientation('', 1, 0);
						// The only function to edit the bottom margin of current page to set it.
							// We suppose that a too long description is moved completely on next page
					if ($pageposafter > $pageposbefore && empty($showpricebeforepagebreak)) {
						$pdf->setPage($pageposafter); $curY = $tab_top_newpage;
					}
					$pdf->SetFont('','', $default_font_size-2);
						// On repositionne la police par defaut
					$nexY+=2;
						// Passe espace entre les lignes
							// Detect if some page were added automatically and output _tableau for past pages
					while ($pagenb < $pageposafter)
					{
						$pdf->setPage($pagenb);
						if ($pagenb == 1)
						{
							$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1);
						}
						else
						{
							$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 0, 1);
						}
						$this->_pagefoot($pdf,$object,$outputlangs,1);
						$pagenb++;
						$pdf->setPage($pagenb);
						$pdf->setPageOrientation('', 1, 0);
							// The only function to edit the bottom margin of current page to set it.
						if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD))
							$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager,1);
					}

					if ($nexY+80 > $this->page_hauteur)
					{
						if ($pagenb == 1)
						{
							$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1);
						}
						else
						{
							$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 0, 1);
							$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
						}
						$this->_pagefoot($pdf,$object,$outputlangs,1);
							// New page
						if($this->type_page==1)
						{
							$pdf->AddPage();
						}
						else
						{
							$pdf->AddPage('L');
						}
						if (! empty($tplidx)) $pdf->useTemplate($tplidx);
						$pagenb++;
						if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD))
							$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager,1);
						$curY = $tab_top-12;
							//7 -2
						$nexY = $tab_top+12;
							//7 -2
					}
				}
				//fin foreach
				// Show square
				if ($pagenb == 1)
				{
					$this->_tableau($pdf, $tab_top, 145, 0, $outputlangs, 0, 0);
					$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
					//	$this->pdf_pie_total($pdf, $object, 151, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page,$bottomlasttab);
				}
				else
				{
					$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 0, 0);
					$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
					//$this->pdf_pie_total($pdf, $object, $this->page_hauteur - $tab_top_newpage - $heightforinfotot - $heightforfreetext - $heightforfooter, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page,$bottomlasttab);
				}

					// Pied de page
				$this->_pagefoot($pdf,$object,$outputlangs);
				$this->pdf_pie_total($pdf, $object, 151, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page,$bottomlasttab);

				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

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
				$reshook=$hookmanager->executeHooks('afterPDFCreation',$parameters,$this,$action);

					// Note that $action and $object may have been modified by some hooks

				if (! empty($conf->global->MAIN_UMASK))
					@chmod($file, octdec($conf->global->MAIN_UMASK));

				return 1;
			}
			else
			{
				$this->error=$outputlangs->transnoentities("ErrorCanNotCreateDir",$dir);
				return 0;
			}
		}
		else
		{
			$this->error=$outputlangs->transnoentities("ErrorConstantNotDefined","FAC_OUTPUTDIR");
			return 0;
		}
		$this->error=$outputlangs->transnoentities("ErrorUnknown");
		return 0;
			// Erreur par defaut
	}

	function _tableau(&$pdf, $tab_top, $tab_height, $nexY, $outputlangs, $hidetop=0, $hidebottom=0)
	{
		global $conf;
		// Force to disable hidetop and hidebottom
		$hidebottom=0;
		if ($hidetop) $hidetop=-1;

		$default_font_size = pdf_getPDFFontSize($outputlangs);
		// Amount in (at tab_top - 1)
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('','', $default_font_size-1);

		$pdf->SetDrawColor(128,128,128);
		$pdf->SetFont('','', $default_font_size-1);
		//para imprimir una linea restamos el tabtop
		$tab_orig = $tab_top;

		if($type_page==0)
		{
					// La linea rectangular de pagina horizontal
			$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);
		}
		else
		{
						// La linea rectangular de pagina vertical
			$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);
		}

		if (empty($hidetop)){

			$pdf->SetXY($this->posxini-1, $tab_top+2);
			$pdf->MultiCell($this->posxcodi-$this->posxini-1,2, $outputlangs->transnoentities("Codigo Contable"),'','C');
			$pdf->line($this->posxcodi-1, $tab_top, $this->posxcodi-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxcodi-1, $tab_top+2);
			$pdf->MultiCell($this->posxdesc-$this->posxcodi-1,2, $outputlangs->transnoentities("Descripcion de la Cuenta"),'','C');
			$pdf->line($this->posxdesc-1, $tab_top, $this->posxdesc-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxdesc-1, $tab_top+3);
			$pdf->MultiCell($this->posxparc-$this->posxdesc-1,2, $outputlangs->transnoentities("Parciales"),'','C');
			$pdf->line($this->posxparc-1, $tab_top, $this->posxparc-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxparc-1, $tab_top+2);
			$pdf->MultiCell($this->posxdebe-$this->posxparc-1,2, $outputlangs->transnoentities("Debe"),'','C');
			$pdf->line($this->posxdebe-1, $tab_top, $this->posxdebe-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxdebe-1, $tab_top+2);
			$pdf->MultiCell($this->posxfin-$this->posxdebe-1,2, $outputlangs->transnoentities("Haber"),'','C');
			//$pdf->line($this->posxci-1, $tab_top, $this->posxci-1, $tab_top + $tab_height);
			$pdf->line($this->marge_gauche, $tab_top+10, $this->page_largeur-$this->marge_droite, $tab_top+10);
		}
	}

	function _pagehead(&$pdf, $object, $showaddress, $outputlangs, $hookmanager,$typeTitulo)
	{
		global $conf,$langs;

		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("companies");
		$outputlangs->load("almacen@almacen");

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		// Array datos
		$aReporte = unserialize($_SESSION['aReporte']);
		$aDetalle = $aReporte[1];

		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

		// Show Draft Watermark
		if($object->status==0 && (! empty($conf->global->CONTAB_DRAFT_WATERMARK)) )
		{
			pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->CONTAB_DRAFT_WATERMARK);
		}

		$pdf->SetTextColor(0,0,60);
		$pdf->SetFont('','B', $default_font_size+3);

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
				$pdf->Image($logo, $this->marge_gauche, $posy, 0, $height);
				// width=0 (auto)
			}
			else
			{
				$pdf->SetTextColor(200,0,0);
				$pdf->SetFont('','B',$default_font_size+3);
				$pdf->MultiCell(100, 3, $outputlangs->transnoentities("ErrorLogoFileNotFound",$logo), 0, 'L');
				$pdf->MultiCell(100, 3, $outputlangs->transnoentities("ErrorGoToGlobalSetup"), 0, 'L');
			}
		}
		else
		{
			$text=$this->emetteur->name;
			$pdf->MultiCell(100, 4, $outputlangs->convToOutputCharset($text), 0, 'L');
		}

		$titulo = "COMPROBANTE DE CONTABILIDAD";


		$pdf->SetFont('','B', $default_font_size-1);
		$pdf->SetXY($this->posxini+2 ,$posy);
		$pdf->SetTextColor(0,0,60);
		//$title=$outputlangs->transnoentities("Determinacion de Servicios");
		$title=$outputlangs->transnoentities($titulo);
		$pdf->MultiCell(207, 3, $title, '', 'C');

		//$pdf->SetFont('','B',$default_font_size);

		//Lado Derecho

		//Fecha
		$posy+=6;
		$auxX = 11;
		//$pdf->SetXY($posx,$posy);
		//$pdf->SetTextColor(0,0,60);
		//$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Date")." : " . dol_print_date(dol_now(),'daytext'), '', 'R');

		//Dato
		$posy+=6;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Validado Cbte No."), '', 'R');
		//Fecha comprobante
		$posy+=4;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $aDetalle['validado'], '', 'R');

		//Dato
		$posy+=4;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Fecha Comprobante"), '', 'R');

		//Fecha
		$posy+=4;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, dol_print_date($aDetalle['fechaComprobante'],'day'), '', 'R');

		//Fecha
		$posy+=20;
		$pdf->SetXY(30,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(165, 3, $outputlangs->transnoentities("Supportdocument")." : ".$aDetalle['respaldo'], '', 'R');

		//Lado Izquierdo

		//Dato
		$posy = 35;
		$pdf->SetXY($auxX, $posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Executingunit")." : ".$aDetalle['entidad'], '', 'L');
		//Fecha comprobante
		$posy+=4;
		$pdf->SetXY($auxX, $posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(0, 3, $outputlangs->transnoentities("Transactioncode")." : ".html_entity_decode($aDetalle['codTransaccion']).' '.$aDetalle['codTransaccionResp'], '', 'L');
		//Dato
		$posy+=4;
		$pdf->SetXY($auxX, $posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Currency_")." : ".$aDetalle['divisa'] .")", '', 'L');
		//Fecha
		$posy+=4;
		$pdf->SetXY($auxX, $posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(160, 3, $outputlangs->transnoentities("Cod de Transaccion Resp")." : ", '', 'L');
	}

	function _pagefoot(&$pdf,$object,$outputlangs,$hidefreetext=0)
	{
		//return pdf_pagefoot($pdf,$outputlangs,'ALMACEN_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
		return pdf_pagefoot_fractal($pdf,$outputlangs,'CONTAB_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
	}

	function pdf_pie_total(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0,$bottomlasttab)
	{
		global $conf,$langs;
		$default_font_size = pdf_getPDFFontSize($outputlangs);


		$aReporte = unserialize($_SESSION['aReporte']);
		$aDetalle = $aReporte[1];

		//$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('times', '',10);


			// Aqui ponemos donde se mostrara el pie de pagina horizontal
			//$posy=$bottomlasttab-35;
			// declaramos las posciones que nos ayudara a crear las diviciones
		$this->posxini=$this->marge_gauche+1;
		$this->posxcodi = 60;
		$this->posxdesc = 120;
		$this->posxparc = 150;
		$this->posxdebe = 180;
		$this->posxfin = 207;
		$curY = $posy+55;
		$pdf->SetFillColor( 174, 214, 241);

			//$this->printRect($pdf,$this->marge_gauche,$posy, $this->page_largeur-$this->marge_gauche-$this->marge_droite, 37, $hidetop, $hidebottom);
		$pdf->SetXY($this->posxdesc, $curY);
		$pdf->MultiCell($this->posxparc-$this->posxdesc-1, 1, $outputlangs->transnoentities("Total")." : ", 1, 'R',0);
		$pdf->SetXY($this->posxparc, $curY);
		$pdf->MultiCell($this->posxdebe-$this->posxparc-1, 1, price(price2num($aReporte[3],'MT')), 0, 'R',1);
		$pdf->SetXY($this->posxdebe, $curY);
		$pdf->MultiCell($this->posxfin-$this->posxdebe-1, 1, price(price2num($aReporte[4],'MT')), 0, 'R',1);

			//Linea Rectangular del pie de pagina
		$this->printRect($pdf,$this->marge_gauche, $curY + 6, $this->page_largeur-$this->marge_gauche-$this->marge_droite, 50, $hidetop, $hidebottom);

		$cText = num2texto($aReporte[4]);
		$pdf->SetXY($this->posxini,213);
		$pdf->MultiCell($this->posxfin-$this->posxini-1, 1, $outputlangs->transnoentities("Son ")." : ".$cText, 0, 'L',0);

		$pdf->SetXY($this->posxini,225);
		$pdf->MultiCell($this->posxdesc-$this->posxini-1, 1, $outputlangs->transnoentities("Beneficiary")." : ".$aDetalle['beneficiario'] , 0, 'L',0);

		$pdf->SetXY($this->posxdesc,225);
		$pdf->MultiCell($this->posxfin-$this->posxdesc-1, 1, $outputlangs->transnoentities("History")." : ".$aDetalle['glosa'] , 0, 'L',0);

		$curY +=8;
		if (empty($hidetop)){

			$pdf->SetXY($this->posxini-1, $curY+43);
			$pdf->MultiCell($this->posxcodi-$this->posxini-1,2, $outputlangs->transnoentities("Elaborado por"),'','C');
			$pdf->line($this->posxcodi-1, $curY+34, $this->posxcodi-1, $curY+48);

			$pdf->SetXY($this->posxcodi-1, $curY+43);
			$pdf->MultiCell($this->posxdesc-$this->posxcodi-1,2, $outputlangs->transnoentities("REVISADO"),'','C');
			$pdf->line($this->posxdesc-1, $curY+10, $this->posxdesc-1, $curY+48);

			$pdf->SetXY($this->posxdesc-1, $curY+43);
			$pdf->MultiCell($this->posxparc-$this->posxdesc-1,2, $outputlangs->transnoentities("APROBADO"),'','C');
			$pdf->line($this->posxparc-1, $curY+34, $this->posxparc-1, $curY+48);

			$pdf->SetXY($this->posxparc-1,$curY+43);
			$pdf->MultiCell($this->posxdebe-$this->posxparc-1,2, $outputlangs->transnoentities("APROBADO"),'','C');
			$pdf->line($this->posxdebe-1, $curY+34, $this->posxdebe-1, $curY+48);

			$pdf->SetXY($this->posxdebe-1, $curY+43);
			$pdf->MultiCell($this->posxfin-$this->posxdebe-1,2, $outputlangs->transnoentities("APROBADO"),'','C');
				//$pdf->line($this->posxci-1, $curY, $this->posxci-1, $curY + $tab_height);
			$pdf->line($this->marge_gauche, $curY+10, $this->page_largeur-$this->marge_droite, $curY+10);
			$pdf->line($this->marge_gauche, $curY+34, $this->page_largeur-$this->marge_droite, $curY+34);
		}
	}


}



?>