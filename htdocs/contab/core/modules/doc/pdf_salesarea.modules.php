<?php
 /* Copyright (C) 2017	L.Miguel Mendoza	<l.mendoza.liet@gmail.com>
  * Modelo de Reporte Vertical
  */

require_once DOL_DOCUMENT_ROOT.'/contab/core/modules/contab/modules_contab.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf_fractal.lib.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/utils.lib.php';

/*require_once DOL_DOCUMENT_ROOT.'/poa/class/poareformulatedext.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/class/poareformulateddetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/class/poaobjetiveext.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/cpartidaext.class.php';*/

class pdf_salesarea extends ModelePDFContab
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
	var $type_page = 0;

	function __construct($db)
	{
		global $conf,$langs,$mysoc;

		$langs->load("main");
		$langs->load("bills");
		$titulo = "salesarea";
		$this->db = $db;
		$this->name = "salesarea";
		$this->description = $langs->trans('PDFDeterminationserver');
		$aReporte = unserialize($_SESSION['aReporte']);
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
			$this->posxini  =$this->marge_gauche+1;
			$this->posxprod = 105;
			$this->posxunid = 125;
			$this->posxvent = 157;
			$this->posxcost = 182;
			$this->posxfin  = 207;
		}
		else
		{
			//Posiciones para mostrar los textos de manera horizontal
			$this->posxini=$this->marge_gauche+1;
			$this->posxrefe = 40;
			$this->posxlabe = 120;
			$this->posxfech = 140;
			$this->posxunid = 155;
			$this->posxdepa = 200;
			$this->posxresp = 240;
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
		$aReporte = unserialize($_SESSION['aReporte']);

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
			//$object->fetch_thirdparty();

			// Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->contab->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
				$period_year = $_SESSION['period_year'];
				$dir = $conf->contab->dir_output."/contab/".$period_year.'/salesarea';
				$file = $dir . "/salesarea.pdf";

			}

			if (! file_exists($dir))
			{
				if (dol_mkdir($dir) < 0)
				{
					$this->error=$langs->transnoentities("ErrorCanNotCreateDir",$dir);
					return 0;
				}
			}

			//Rescatamos la sesiones enviadas
			$aReporte = unserialize($_SESSION['aReporte']);

			if (file_exists($dir))
			{
				//$nblignes = count($aKardex['lines']);
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
				$heightforfooter = $this->marge_basse + 2;
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
				$pdf->SetSubject($outputlangs->transnoentities("ASSISTANCEASSISTANCE"));
				$pdf->SetCreator("Dolibarr ".DOL_VERSION);
				$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
				$pdf->SetKeyWords($outputlangs->convToOutputCharset($object->ref)." ".$outputlangs->transnoentities("Order"));
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
				$tab_top = 35;
				$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?35:10);//38
				$tab_height = 35;
				//130
				$tab_height_newpage = 35;
				//150


				$iniY = $tab_top +4;//4
				$curY = $tab_top +11;//4
				$nexY = $tab_top +11;//4
				// Loop on each lines
				$sumaTotal = 0;
				//$pdf->SetFillColor(220, 255, 220);
				$pdf->SetFillColor(100, 149, 273);

				$idGr = 1;
				$j = 0;
				$aPosY = array();

				foreach ( (array)$aReporte[1] as $j => $line)
				{
					$curY = $nexY;
					$pdf->SetFont('','', $default_font_size-1);
						// Into loop to work with multipage

					$pdf->SetFillColor(142, 208, 251);
					$pdf->SetTextColor(0,0,0);
					$pdf->setTopMargin($tab_top_newpage);
					$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);
					// The only function to edit the bottom margin of current page to set it.
					$pageposbefore=$pdf->getPage();
						// date
					//$curX = $this->posxdate-1;
					$showpricebeforepagebreak=1;

					$pdf->SetXY($this->posxini, $curY);
					$pdf->MultiCell($this->posxrefe-$this->posxini-1, 1, $line['ref'], 0, 'C',0);

					$pdf->SetXY($this->posxrefe, $curY);
					$pdf->MultiCell($this->posxlabe-$this->posxrefe-1, 1, $line['label'], 0, 'L',0);
					$aPosY[] = $pdf->GetY();
					$pdf->SetXY($this->posxlabe, $curY);
					$pdf->MultiCell($this->posxfech-$this->posxlabe-1, 1, dol_print_date($line['fecha'],'day'), 0, 'C',0);

					$pdf->SetXY($this->posxfech, $curY);
					$pdf->MultiCell($this->posxunid-$this->posxfech-1, 1,$line['unidad'], 0, 'C',0);

					$pdf->SetXY($this->posxunid, $curY);
					$pdf->MultiCell($this->posxdepa-$this->posxunid-1, 1,$line['depa'], 0, 'L',0);
					$aPosY[] = $pdf->GetY();
					$pdf->SetXY($this->posxdepa, $curY);
					$pdf->MultiCell($this->posxresp-$this->posxdepa-1, 1, $line['respon'], 0, 'L',0);
					$aPosY[] =$pdf->GetY();
					$pdf->SetXY($this->posxresp, $curY);
					$pdf->MultiCell($this->posxfin-$this->posxresp-1, 1, price(price2num($line['total'],'MT')), 0, 'R',0);

					$nexY = max($aPosY);
					unset($aPosY);
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
					$pdf->SetLineStyle(array('dash'=>'1,1','color'=>array(210,210,210)));
					//$pdf->SetDrawColor(190,190,200);
					$pdf->line($this->marge_gauche, $nexY+1, $this->page_largeur - $this->marge_droite, $nexY+1);
					$pdf->SetLineStyle(array('dash'=>0));
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

					if ($nexY+25 > $this->page_hauteur)
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
						$curY = $tab_top-2;//7 -2
						$nexY = $tab_top+11;//7 -2
					}
				}

			}
			//fin foreach
			// Show square
			if ($pagenb == 1)
			{
				$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter-15, 0, $outputlangs, 0, 0);
				$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
				$this->pdf_pie_total($pdf, $object, $this->page_hauteur - $tab_top - $heightforfooter-5, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page,$bottomlasttab);
			}
			else
			{
				$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter-15, 0, $outputlangs, 0, 0);
				$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
			 	$this->pdf_pie_total($pdf, $object, $this->page_hauteur - $tab_top - $heightforfooter-5, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page,$bottomlasttab);
			}

			// Pied de page
			$this->_pagefoot($pdf,$object,$outputlangs);

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

		//$this->error=$langs->trans("ErrorUnknown");
		//return 0;
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
		{   /* La linea rectangular de pagina horizontal */
			$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);
		}
		else
		{	/* La linea rectangular de pagina vertical */
			$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);
		}

		if (empty($hidetop)){

			$pdf->SetXY($this->posxini-1, $tab_top+2);
			$pdf->MultiCell($this->posxrefe-$this->posxini-1,2, $outputlangs->transnoentities("Ref"),'','C');
			$pdf->line($this->posxrefe-1, $tab_top, $this->posxrefe-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxrefe-1, $tab_top+2);
			$pdf->MultiCell($this->posxlabe-$this->posxrefe-1,2, $outputlangs->transnoentities("Descripcion de compra de producto/servicio"),'','C');
			$pdf->line($this->posxlabe-1, $tab_top, $this->posxlabe-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxlabe-1, $tab_top+2);
			$pdf->MultiCell($this->posxfech-$this->posxlabe-1,2, $outputlangs->transnoentities("Fecha"),'','C');
			$pdf->line($this->posxfech-1, $tab_top, $this->posxfech-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxfech-1, $tab_top+2);
			$pdf->MultiCell($this->posxunid-$this->posxfech-1,2, $outputlangs->transnoentities("Unidad"),'','C');
			$pdf->line($this->posxunid-1, $tab_top, $this->posxunid-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxunid-1, $tab_top+2);
			$pdf->MultiCell($this->posxdepa-$this->posxunid-1,2, $outputlangs->transnoentities("Departamento"),'','C');
			$pdf->line($this->posxdepa-1, $tab_top, $this->posxdepa-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxdepa-1, $tab_top+2);
			$pdf->MultiCell($this->posxresp-$this->posxdepa-1,2, $outputlangs->transnoentities("Solicitante"),'','C');
			$pdf->line($this->posxresp-1, $tab_top, $this->posxresp-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxresp-1, $tab_top+2);
			$pdf->MultiCell($this->posxfin-$this->posxresp-1,2, $outputlangs->transnoentities("Valor Pedido"),'','C');
			//$pdf->line($this->posxci-1, $tab_top, $this->posxci-1, $tab_top + $tab_height);
			$pdf->line($this->marge_gauche, $tab_top+10, $this->page_largeur-$this->marge_droite, $tab_top+10);
		}
	}

	function _pagehead(&$pdf, $object, $showaddress, $outputlangs, $hookmanager,$typeTitulo)
	{
		global $conf;

		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("companies");
		$outputlangs->load("almacen@almacen");

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		// Array datos
		$aReporte = unserialize($_SESSION['aReporte']);


		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

	    // Show Draft Watermark
		if($object->statut==0 && (! empty($conf->global->ALMACEN_DRAFT_WATERMARK)) )
		{
			pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->ALMACEN_DRAFT_WATERMARK);
		}

		$pdf->SetTextColor(0,0,60);
		$pdf->SetFont('','B', $default_font_size+4);

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

		$titulo = "Reporte de gastos por área segun Compras";


		$pdf->SetFont('','B', $default_font_size);
		$pdf->SetXY($this->posxini+2 ,$posy);
		$pdf->SetTextColor(0,0,60);
		//$title=$outputlangs->transnoentities("Determinacion de Servicios");
		$title=$outputlangs->transnoentities($titulo);
		/*Titutlo para hoja horizontal*/
		$pdf->MultiCell(207, 3, $title, '', 'C');
		/*Titutlo para hoja vertical*/
		//$pdf->MultiCell(270, 3, $title, '', 'C');

		//$pdf->SetFont('','B',$default_font_size);

		//Lado Derecho
		$pdf->SetFont('','B', $default_font_size-1);
		//Fecha
		$posy+=6;
		$auxX = 11;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3,$outputlangs->transnoentities('De')." : " . dol_print_date($aReporte[2],'day'), '', 'R');

		//Dato
		$posy+=4;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3,$outputlangs->transnoentities('A')." : " . dol_print_date($aReporte[3],'day'), '', 'R');

		//Departamentos
		$posy+=4;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3,$outputlangs->transnoentities('Alldepartament') , '', 'R');

	}

	function _pagefoot(&$pdf,$object,$outputlangs,$hidefreetext=0)
	{
		//return pdf_pagefoot($pdf,$outputlangs,'ALMACEN_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
		return pdf_pagefoot_fractal($pdf,$outputlangs,'ALMACEN_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
	}

	function pdf_pie_total(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0,$bottomlasttab)
	{
		global $conf,$langs;
		$default_font_size = pdf_getPDFFontSize($outputlangs);


		$aReporte = unserialize($_SESSION['aReporte']);


		//$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('times', '',10);


			// Aqui ponemos donde se mostrara el pie de pagina horizontal
			//$posy=$bottomlasttab-35;
			// declaramos las posciones que nos ayudara a crear las diviciones
			$this->posxini=$this->marge_gauche+1;
			$this->posxrefe = 40;
			$this->posxlabe = 120;
			$this->posxfech = 140;
			$this->posxunid = 155;
			$this->posxdepa = 200;
			$this->posxresp = 240;
			$this->posxfin = 270;
			$curY = $posy+26;
			$pdf->SetFillColor( 174, 214, 241);

			//$this->printRect($pdf,$this->marge_gauche,$posy, $this->page_largeur-$this->marge_gauche-$this->marge_droite, 37, $hidetop, $hidebottom);
			$pdf->SetXY($this->posxdepa, $curY);
			$pdf->MultiCell($this->posxresp-$this->posxdepa-1, 1,$outputlangs->transnoentities("Total")." : ", 1, 'R',0);
			$pdf->SetXY($this->posxresp, $curY);
			$pdf->MultiCell($this->posxfin-$this->posxresp-1, 1, price(price2num($aReporte[5],'MT')), 0, 'R',1);

			//Linea Rectangular del pie de pagina
			//$this->printRect($pdf,$this->marge_gauche, $curY + 6, $this->page_largeur-$this->marge_gauche-$this->marge_droite, 50, $hidetop, $hidebottom);

	}

}
?>