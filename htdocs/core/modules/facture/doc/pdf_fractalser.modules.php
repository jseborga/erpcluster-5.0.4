<?php
/* Copyright (C) 2017	NO ONE>
 *
 */

/** Descripcion
 *	El siguiente modelo se refiere a la plantilla de facturas sevicios
 */


require_once DOL_DOCUMENT_ROOT.'/core/modules/facture/modules_facture.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/utils.lib.php';

require_once DOL_DOCUMENT_ROOT.'/sales/class/factureext.class.php';

require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entityadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/subsidiaryext.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/vdosing.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/vfiscal.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/lib/fiscal.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entityaddext.class.php';



require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf_fractal.lib.php';

/**
 *	Class to manage PDF invoice template Crabe
 */

class pdf_fractalser extends ModelePDFFactures
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

	var $type_page=1;
  // Objet societe qui emet


	/**
	*	Constructor
	*
	*  @param		DoliDB		$db      Database handler
	*/
	function __construct($db)
	{
		global $conf,$langs,$mysoc;

		$langs->load("sales");
		$langs->load("main");
		$langs->load("bills");

		$this->db = $db;
		$this->name = "fractalser";
		$this->description = $langs->trans('Factura de servicios');

		//Objeto Factura

		$objFactura = new Facture($this->db);


		// Dimension page pour format A4
		if($this->type_page==1)
		{
			$this->type = 'pdf';
			$formatarray=pdf_getFormat();
			//Forsando medidas a la pagina
			$this->page_largeur = $formatarray['width'];
			//$this->page_hauteur = (int)($formatarray['height']/2);
			$this->page_hauteur = ($formatarray['height']);
			//$this->page_hauteur = 170;


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
		$this->posxini = $this->marge_gauche+8;
		$this->posxtota  = 180;
		$this->posxfin = 200;


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
		$outputlangs->load("facture@facture");

		if ($conf->facture->dir_output)
		{
			$object->fetch_thirdparty();

			// Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->facture->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
				$objectref = dol_sanitizeFileName($object->ref);
				$dir = $conf->facture->dir_output . "/" . $objectref;
				$file = $dir . "/" . $objectref . ".pdf";
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
				//$nblignes = count($aKardex['lines']);

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
				$pdf->SetSubject($outputlangs->transnoentities("FACTURE@FACTURE"));
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

				$this->_pagehead($pdf, $object, 1, $outputlangs, $hookmanager);


				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->MultiCell(0, 3, '');
				// Set interline to 3
				$pdf->SetTextColor(0,0,0);

				//linea para el cuerpo
				$tab_top = 45;
				$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?38:10);//38
				$tab_height = 130;
				//130
				$tab_height_newpage = 150;
				//150
				$iniY = $tab_top +4;//4
				$curY = $tab_top +4;//4
				$nexY = $tab_top +4;//4
				// Loop on each lines
				$sumaTotal = 0;
				//$pdf->SetFillColor(220, 255, 220);
				$pdf->SetFillColor(100, 149, 273);

				$pdf->SetFont('','', 8);
				$pdf->SetTextColor(0,0,0);
				$pdf->setTopMargin($tab_top_newpage);
				$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);

				$posxmargen=10;

				$posy = 82;
				$sumaTotal = 0;

				$objProduct = new Product($this->db);
				$objFactura = new Factureext($this->db);
				$res = $objFactura->fetch($object->id);
				$resadd = $objFactura->fetch_linesadd();

				//$i=0;
				for ($i=0; $i < count($objFactura->lines) ; $i++) {

					$pdf->SetFont('times','', 9);
					$pdf->SetTextColor(0,0,0);
					$pageposbefore=$pdf->getPage();
					// $curX sera la poccion del curso de donde se pondra en el eje x
					$curX = $this->posxini-1;
					$showpricebeforepagebreak=1;
					$description = '';
					if ($objFactura->lines[$i]->fk_product>0)
					{
						$objProduct->fetch($objFactura->lines[$i]->fk_product);
						$description = $objProduct->ref.' - '.$objProduct->label;
					}
					if (!empty($objFactura->lines[$i]->description)) $description.= (!empty($description)?' : ':'').$objFactura->lines[$i]->description;
					//Detalle
					$pdf->SetXY($this->posxini-1, $posy);
					$pdf->MultiCell($this->posxtotal-$this->posxini-1,2, $description,'','L');
					$nexYdesc = $pdf->GetY();

					$sumaParcial = $objFactura->lines[$i]->price*$objFactura->lines[$i]->qty;//fk_unit
					//Sub Total
					$pdf->SetXY($this->posxtota-1, $posy);
					$pdf->MultiCell($this->posxfin-$this->posxtota-1,2, price($sumaParcial),'','R');
					$sumaTotal = $sumaTotal + $sumaParcial;

					if($nexYdesc > $posy ){
						$posy = $nexYdesc;
					}
					$posy +=1;

					$nexY = $posy;
					$j++;

				//aqui capturamos donde estamos despues de recorrer una linea
				$nexY = $pdf->GetY();
				$pageposafter=$pdf->getPage();
				$pdf->setPage($pageposbefore);
				$pdf->setTopMargin($this->marge_haute);
				$pdf->setPageOrientation('', 1, 0);
				// We suppose that a too long description is moved completely on next page
				//Suponemos que fue la descripción demasiado tiempo está completamente Call movido en la página siguiente
				if ($pageposafter > $pageposbefore && empty($showpricebeforepagebreak))
				{
					$pdf->setPage($pageposafter);
					$curY = $tab_top_newpage;

				}
					//$pdf->SetFont('','', $default_font_size - 1);

					$nexY+=1;

					// Detect if some page were added automatically and output _tableau for past pages
					//Algunos detectar la página de tejo se añadieron de forma automática y usa el la tabla de salida para las páginas anteriores
					while ($pagenb < $pageposafter)
					{
						$pdf->setPage($pagenb);
						if ($pagenb == 1)
						{
							//$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfreetext - $heightforfooter-90, 0, $outputlangs, 0, 0);
							//$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter-90, 0, $outputlangs, 0, 1);
						}
						else
						{
							//$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfreetext - $heightforfooter-90, 0, $outputlangs, 0, 0);
							//$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter-30, 0, $outputlangs, 0, 1);
						}
						$this->_pagefoot($pdf,$object,$outputlangs,1);
						$pagenb++;
						$pdf->setPage($pagenb);
						$pdf->setPageOrientation('', 1, 0);
						// The only function to edit the bottom margin of current page to set it.
						// La única función para editar el margen inferior de la página actual para configurarlo.
						if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD))
							$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager);
						}

						// Aca tener especial cuidado para poder por que si se recorre una pagina completa se debera recorrer nuestro pie de pagina de firmas esto depende de la poscicion actual de Y y el alto de la pagina
						if ($nexY +20 > $this->page_hauteur)
						{
							if ($pagenb == 1)
							{
								$tab_height = 145;
								$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfreetext - $heightforfooter-30, 0, $outputlangs, 0, 0);
								//$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1);
								//$this->_tableau($pdf, $tab_top, $tab_height, 0, $outputlangs, 0, 1);
							}
							else
							{
								$tab_height = 185;
								$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfreetext - $heightforfooter-30, 0, $outputlangs, 0, 0);
								//$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 0, 1);
								//$this->_tableau($pdf, $tab_top_newpage, $tab_height, 0, $outputlangs, 0, 1);
							}

							$this->_pagefoot($pdf,$object,$outputlangs,1);
							// Adicionamos una nueva pagina
							$pdf->AddPage();

							if (! empty($tplidx)) $pdf->useTemplate($tplidx);
							$pagenb++;
							if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD))
								$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager);
							$posy = 82;
							//$nexY = $tab_top + 11;
						}

					} //obsional Fin While
				if ($pagenb == 1)
				{
					$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfreetext - $heightforfooter-90, 0, $outputlangs, 0, 0);
					$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
				}
				else
				{
					$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforinfotot - $heightforfreetext - $heightforfooter-90, 0, $outputlangs, 0, 0);
					$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
				}

				//}//Fin While
				// Pied de page
				$this->_pagefoot($pdf,$object,$outputlangs);
				$this->_pagefootfractalqr($pdf,$object,$outputlangs,0,$sumaTotal);
				//Tabla de tres columnas para las firmas
				//$this->pdf_pie_page_Ltrescol($pdf, $object, $posy, $outputlangs,$this->marge_gauche, $this->page_largeur,$this->marge_droite,$this->page_hauteur,$this->type_page,$bottomlasttab);
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
				// Pas d'erreur
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
		$pdf->SetFont('','', $default_font_size - 2);

		$pdf->SetDrawColor(128,128,128);
		$pdf->SetFont('','', $default_font_size - 3);
		//para imprimir una linea restamos el tabtop
		$tab_orig = $tab_top;
		$tab_top -= 6;
	 // Output Rect
		//$this->type_page=0;
		if($type_page==0)
		{
			$this->printRect($pdf,$this->marge_gauche+4, 76, $this->page_largeur-$this->marge_gauche-$this->marge_droite-6, $tab_height, $hidetop, $hidebottom);
            //$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);
		}
		else
		{
			$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);
		}

		$pdf->SetFont('times', 'B', 10);
		$pdf->SetDrawColor(128,128,128);
		if (empty($hidetop))
		{
			/*Linea de la cabezera de la tabla de detalle de la Factura*/
			$pdf->line($this->marge_gauche+4, 82, $this->page_largeur-$this->marge_droite-2, 82);		// line prend une position y en 2eme param et 4eme param


			$pdf->SetXY($this->posxini-1, 77);
			$pdf->MultiCell($this->posxtota-$this->posxini-1,2, $outputlangs->transnoentities("Description"),'','C');
			/*Linea de la columna*/
			$pdf->line($this->posxtota-1, 76, $this->posxtota-1, $tab_height+76);

			$pdf->SetXY($this->posxtota-1, 77);
			$pdf->MultiCell($this->posxfin-$this->posxtota-1,2, $outputlangs->transnoentities("SubTotal"),'','C');

		}
	}

	function _pagehead(&$pdf, $object, $showaddress, $outputlangs, $hookmanager)
	{
		global $conf,$langs;
		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("companies");
		$outputlangs->load("facture@facture");

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		$objVfiscal = new Vfiscal($this->db);
		$objSubsidiary = new Subsidiaryext($this->db);
		$objVdosing = new Vdosing($this->db);

		$objEntity = new Entityadd($this->db);
		$resvf = $objVfiscal->fetch(0,$object->id);
		$resvd = $objVdosing->fetch($objVfiscal->fk_dosing);
		$ressd = $objSubsidiary->fetch($objVdosing->fk_subsidiaryid);

		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

		// Show Draft Watermark
		if($object->statut==0 && (! empty($conf->global->ALMACEN_DRAFT_WATERMARK)) )
		{
			pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->ALMACEN_DRAFT_WATERMARK);
		}

		$pdf->SetTextColor(0,0,60);
		$pdf->SetFont('','B', $default_font_size + 2);

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
				$pdf->Image($logo, $this->marge_gauche+4, $posy, 0, $height);

			// width=0 (auto)
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

		$resent = $objEntity->fetchAll('','',0,0,array(1=>1),'AND','AND t.fk_entity = '.$objVfiscal->entity,true);

		if(($resvf+$resvd+$ressd) != 3)
			echo 'Error en la cabecera '.$resvf.' '.$resvd.' '.$ressd;
		setEventMessage('Error en la cabecera de la factura',null,'errors');

		//Detalle de casa matriz
		$pdf->SetFont('','',6);
		$posy = $pdf->GetY();
		$posy = $posy + 23;
		$posadd = 2;
		$pdf->SetXY(10,$posy);
		$pdf->MultiCell(90,3,$objSubsidiary->socialreason,'',"C");
		//$posy = $pdf->GetY();
		$posy+=$posadd;
		$pdf->SetXY(10,$posy);
		$pdf->MultiCell(90,3,"CASA MATRIZ ".$objSubsidiary->matriz_name,'',"C");
		$posy+=$posadd;
		$pdf->SetXY(10,$posy);
		$pdf->MultiCell(90,3,"Zona. ".$objSubsidiary->matriz_zone.' '.$objSubsidiary->matriz_address,'',"C");
		$posy+=$posadd;
		$pdf->SetXY(10,$posy);
		$pdf->MultiCell(90,3,"Telf. ".$objSubsidiary->matriz_phone,'',"C");
		$posy+=$posadd;
		$pdf->SetXY(10,$posy);
		$pdf->MultiCell(90,3,$objSubsidiary->matriz_city. " - BOLIVIA",'',"C");

		if ($objSubsidiary->matriz_def == 0)
		{			//Detalle Sucursal
		 	$posy+=$posadd;
			$pdf->SetXY(10,$posy);
			$pdf->MultiCell(90,3,"Suc. ".$objSubsidiary->label,'',"C");
			$posy+=$posadd;
			$pdf->SetXY(10,$posy);
			$pdf->MultiCell(90,3,"Dir. ".$objSubsidiary->address,'',"C");
			$posy+=$posadd;
			$pdf->SetXY(10,$posy);
			$pdf->MultiCell(90,3,"Telf. ".$objSubsidiary->phone,'',"C");
			$posy+=$posadd;
			$pdf->SetXY(10,$posy);
			$pdf->MultiCell(90,3,$objSubsidiary->city. " - BOLIVIA",'',"C");
		}
			// Rectangulo con curvas circulares
		/*Estilo*/
		$style6 = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,10', 'color' => array(128, 128, 128));

		//Rectangulo Superior Derecha
		$pdf->RoundedRect(125, 5, 80, 26, 3.50, '1111', 'DF',null,array(240, 250, 240));
		/*Contenido del rectangulo superior izquierdo */
		$pdf->SetFont('','B',11);
		//$pdf->SetFont('','',9);
		$pdf->SetXY(126,9);
		$pdf->MultiCell(50,3,"NIT : ".$objEntity->nit,'','L');

		$pdf->SetXY(126,15);
		$pdf->MultiCell(60,3,"Nro FACTURA : ". $objVfiscal->nfiscal,'','L');

		$pdf->SetXY(126,21);
		$pdf->MultiCell(100,3,"AUTORIZACION Nro : ". $objVfiscal->num_autoriz ,'','L');



		$pdf->SetFont('','B', $default_font_size+3);
		//Titulo de Factura
		$pdf->SetXY(126,32);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(70, 3, $outputlangs->transnoentities("ORIGINAL"),'', 'C');
		$posy+=1;

		//Actividad Economica
		$pdf->SetFont('','B', $default_font_size-1);
		$pdf->SetXY(126,40);
		$pdf->SetTextColor(0,0,60);
		//$pdf->MultiCell(100, 3, $outputlangs->transnoentities("economicactivity"), 'L');
		$pdf->MultiCell(70, 3, $outputlangs->transnoentities($objVdosing->activity) ,'','C');

		//Aca capturamos la posicion del ultimo texto que dejo y empesamos a escribir el Siguiente
		$nexY = $pdf->GetY();
		$posy = $nexY + 3;

		//Titulo "Factura"
		$pdf->SetFont('','B',13);
		$pdf->SetXY($posx-55,$posy);//54
		$pdf->SetTextColor(0,0,60);
		//$title=$outputlangs->transnoentities("Determinacion de Servicios");
		//$title=$outputlangs->transnoentities(get_name_typebill($objVdosing->type));
		$title=$outputlangs->transnoentities("FACTURA");
		$pdf->MultiCell(98, 3, $title, '', 'C');

		$nexY = $pdf->GetY();
		$posy = $nexY + 2;

		//Rectangulo para poder el detalle de la Factura
		$pdf->RoundedRect(14, $posy, 190, 14, 2.50, '1111', 'DF',null,array(240, 250, 240));
		$nexY = $pdf->GetY();
		$posy = $nexY + 4;
		/*Contenido del detalle del Cliente*/
		$pdf->SetFont('','',10);
		$pdf->SetXY(17,$posy);
		$pdf->MultiCell(100,3,$outputlangs->transnoentities("Placeanddate").': '.$outputlangs->transnoentities($objSubsidiary->city).', '.dol_print_date($objVfiscal->date_exp,'daytext'),'','L');

		$pdf->SetFont('','',10);
		$pdf->SetXY(140,$posy);
		$pdf->MultiCell(90,3,"NIT/CI : ".$objVfiscal->nit,'','L');

		$posy +=6;

		$pdf->SetFont('','',10);
		$pdf->SetXY(17,$posy);
		$pdf->MultiCell(200,3,$outputlangs->transnoentities("Sir").'(es)'.': '.$objVfiscal->razsoc,'','L');

		$posy +=3;
		// Show list of linked objects
		$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size, $hookmanager);
	}

	function _pagefootfractalqr(&$pdf,$object,$outputlangs,$hidefreetext=0,$sumaTotal)
	{
		/*Contenido del detalle del Cliente*/
		$objVdosing = new Vdosing($this->db);
		$objSubsidiary = new Subsidiaryext($this->db);
		$objVfiscal = new Vfiscal($this->db);
		$resvf = $objVfiscal->fetch(0,$object->id);
		$resvd = $objVdosing->fetch($objVfiscal->fk_dosing);
		$ressd = $objSubsidiary->fetch($objVdosing->fk_subsidiaryid);

		//Linea para dividir el detalle con el total y literal de la tabla de detalle de la Factura

		$pdf->SetFont('','',10);
		$pdf->SetXY($this->posxtota-1, 205);
		$pdf->MultiCell($this->posxfin-$this->posxtota-1,2, price($sumaTotal),'','R');

		$pdf->SetXY($this->posxpreu-1, 205);
		$pdf->MultiCell($this->posxtota-$this->posxpreu-4,2, $outputlangs->transnoentities("Total Bs. "),'','R');

		//Texto de total real

		$pdf->line($this->marge_gauche+4, 211, $this->page_largeur-$this->marge_droite-2,211);
		$pdf->SetXY($this->posxini-1, 205);
		//$pdf->MultiCell($this->posxini-$this->posxfin-1,2, $outputlangs->transnoentities("Son : ".date2numtexto($sumaTotal)),'','L');
		$cText = num2texto($object->total_ttc);
		$pdf->MultiCell($this->posxini-$this->posxfin-1,2, $outputlangs->transnoentities("Son : ").$cText,'','L');

		if($res < 0)
			setEventMessage('consulta no realizada a vfiscal en el pie de factura',null,'errors');

		$pdf->SetFont('','',10);
		$pdf->SetXY(17,213);
		$pdf->MultiCell(100,3,"Codigo de Control :  ".$objVfiscal->cod_control,'','L');

		$posy = 220;

		$pdf->SetFont('','',10);
		$pdf->SetXY(17,$posy);
		$pdf->MultiCell(100,3,$outputlangs->transnoentities('Deadlineemission').': '.dol_print_date($objVdosing->date_val, 'day'),'','L');

		$posy += 30;
		$pdf->SetFont('','',8);
		$pdf->SetXY($this->posxini-4,$posy);
		$pdf->MultiCell(190,3,"ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAIS EL USO ILICITO DE ESTA SERA SANCIONADA DE ACUERDO A LA LEY".$object->entity,'','C');
		$posy += 4;
		$pdf->SetFont('','',8);
		$pdf->SetXY($this->posxini-4,$posy);
		$pdf->MultiCell(190,3,$objVdosing->descrip,'','C');


		$posy +=0;

		//cODIGO PARA GENERAR EL CODIGO QR
		// set style for barcode
		$style = array(
			'border' => 2,
			'vpadding' => 'auto',
			'hpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255)
			'module_width' => 1, // width of a single module in points
			'module_height' => 1 // height of a single module in points
		);
		// QRCODE,Q : QR-CODE Better error correction
		$this->generaQR($pdf,$object,213);
		$objectref = dol_sanitizeFileName($object->ref);
		$PNG_TEMP_DIR = DOL_DOCUMENT_ROOT.'/documents/tmp/fac'.$objectref.'.png';
		$pdf->SetXY(175, 213);
		$pdf->Image($PNG_TEMP_DIR, '', '', 25, 25, '', '', 'T', false, 300, '', false, false, 1, false, false, false);


	}
	function _pagefoot(&$pdf,$object,$outputlangs,$hidefreetext=0)
	{
		return pdf_pagefoot($pdf,$outputlangs,'ASSISTANCE_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
		//return pdf_pagefoot_fractal($pdf,$outputlangs,'ASSISTANCE_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
	}

	function generaQR(&$pdf,$object,$posy){

		include DOL_DOCUMENT_ROOT."/fiscal/lib/phpqrcode/qrlib.php";
		$objEntity = new Entityaddext($this->db);

		$objVdosing = new Vdosing($this->db);
		$objSubsidiary = new Subsidiaryext($this->db);
		$objVfiscal = new Vfiscal($this->db);
		$resvf = $objVfiscal->fetch(0,$object->id);
		$resvd = $objVdosing->fetch($objVfiscal->fk_dosing);
		$ressd = $objSubsidiary->fetch($objVdosing->fk_subsidiaryid);
		$objEntity->fetch(0,$objVfiscal->entity);

		$facid=$object->id;
		//$vfiscalid=GETPOST('vf','int');
		$aQr = array(
			1 =>'nit',
			2 =>'numfact',
			3 =>'numaut',
			4 =>'fechaexp',
			5 =>'total',
			6 =>'totalbase',
			7 =>'codcontrol',
			8 =>'nitrazsoc',
			9 =>'totalice',
			10=>'totalcero',
			11=>'totalnofiscal',
			12=>'desc'
			);
		$aQrn = array(
			'nit'=>'',
			'numfact'=>'',
			'numaut'=>'',
			'fechaexp'=>'',
			'total'=>0,
			'totalbase'=>0,
			'codcontrol'=>'',
			'nitrazsoc'=>'',
			'totalice'=>0,
			'totalcero'=>0,
			'totalnofiscal'=>0,
			'desc'=>12
			);

		$aQrn['nit'] = $objEntity->nit;
		$aQrn['numfact'] = $objVfiscal->nfiscal;
		$aQrn['numaut'] = $objVfiscal->num_autoriz;

		$aQrn['fechaexp'] = dol_print_date($objVfiscal->date_exp,'day');

		$aQrn['total'] = price2num($objVfiscal->baseimp1,'MT');
		$aQrn['totalbase'] = price2num($object->total_ttc,'MT');

		$aQrn['codcontrol'] = $objVfiscal->cod_control;
		$aQrn['nitrazsoc'] = $objVfiscal->nit;
		$aQrn['totalice'] = 0;
		$aQrn['totalcero'] = 0;
		$aQrn['totalnofiscal'] = 0;
		$aQrn['desc'] = 0;
		//armamos el textqr
		$textqr = '';
		foreach ($aQr AS $k => $value)
		{
			if (!empty($textqr)) $textqr.= '|';
			$textqr.= $aQrn[$value];
		}

		//set it to writable location, a place for temp generated PNG files
		//$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
		$PNG_TEMP_DIR = DOL_DOCUMENT_ROOT.'/documents/tmp/';

		//html PNG location prefix
		$PNG_WEB_DIR = 'temp/';

		//ofcourse we need rights to create temp dir
		if (!file_exists($PNG_TEMP_DIR))
			mkdir($PNG_TEMP_DIR);

		$filename = $PNG_TEMP_DIR.'test.png';

		$matrixPointSize = 10;
		$errorCorrectionLevel = 'L';
		$objectref = dol_sanitizeFileName($object->ref);
		$namefac = $objectref.'.png';
		//$namefac = md5($facid).'.png';
		$filename = $PNG_TEMP_DIR.'fac'.$namefac;
		$file = 'fac'.$namefac;
		QRcode::png($textqr, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		if ($user->id == 55)
		{
			//echo '|'.$filename.'| |'.$textqr;exit;
		}
		clearstatcache();
		//$html.= '<img src="'.DOL_URL_ROOT.'/ventas/lib/temp/'.basename($filename).'" width="120" height="120" />';
		//return $textqr;
	}
}
?>
