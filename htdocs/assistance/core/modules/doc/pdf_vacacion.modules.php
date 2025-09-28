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

require_once DOL_DOCUMENT_ROOT.'/assistance/core/modules/assistance/modules_assistance.php';
//require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
//require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf_fractal.lib.php';


/**
 *	Class to manage PDF invoice template Crabe
 */
class pdf_vacacion extends ModelePDFAssistance
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

		$langs->load("main");
		$langs->load("bills");

		$this->db = $db;
		$this->name = $titulo;
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

			$this->posxdate=$this->marge_gauche+1;
			$this->posxdesc=35;
			$this->posxuse=96;
			$this->posxent=111;
			$this->posxsal=124;
			$this->posxval=138;
			$this->posxpu=152;
			$this->posxentv=162;
			$this->posxsalv=176;
			$this->posxvalv=190;

		}
		else
		{
			$this->posxdpc = $this->marge_gauche+2;
            $this->posxcar = 82;
            $this->posxex  = 135;
            $this->posxre  = 149;
            $this->posxne  = 168;
            $this->posxnr  = 180;
            $this->posxin  = 199;
            $this->posxfi  = 219;
            $this->posxca  = 239;
            $this->posxend = 270;
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
	function write_file($object,$outputlangs,$srctemplatepath='',$hidedetails=0,$hidedesc=0,$hideref=0,$hookmanager=false)
	{
		global $user,$langs,$conf,$mysoc,$db;

		//$product = new Product($this->db);
		if (! is_object($outputlangs)) $outputlangs=$langs;
		// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
		if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output='ISO-8859-1';

		$outputlangs->load("assistance");
		$outputlangs->load("main");
		$outputlangs->load("dict");
		$outputlangs->load("companies");
		$outputlangs->load("bills");

		//Array para el reporte de Vacaciones
		$arrayReporte = unserialize($_SESSION['arrayReporte']);
		$dato_fk_member = $object->fk_member;
		$valref = $object->ref;
		$period_year = $_SESSION['period_year'];
		//var_dump($arrayReporte);exit;
		if ($conf->assistance->dir_output)
		{
			$object->fetch_thirdparty();

			// Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->assistance->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{

				$period_year = $_SESSION['period_year'];
				$dir = $conf->assistance->dir_output . "/assitance/".$period_year.'/'.$dato_fk_member.'/rrhh';
				$file = $dir ."/".$valref.".pdf";

				//echo $file;
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
				$pdf->SetSubject($outputlangs->transnoentities("ASSISTANCE@ASSISTANCE"));
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
				//$pdf->SetFillColor(220, 255, 220);
				$pdf->SetFillColor(100, 149, 273);



				$curY = $nexY;
				//$pdf->SetFont('','', $default_font_size - 4);
				//$pdf->SetFont('times', '', 9);
				$pdf->SetFont('','', $default_font_size-1);
				$pdf->SetTextColor(0,0,0);
				$pdf->setTopMargin($tab_top_newpage);
				$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);
				$pageposbefore=$pdf->getPage();
				// $curX sera la poccion del curso de donde se pondra en el eje x
				$curX = $this->posxdpc-1;
				$showpricebeforepagebreak=1;
				$posxmargen=18;
				// 1. Lugar y Fecha

				$lyf ='Fecha: '.dol_print_date($arrayReporte['fecha'],'daytext');
				$pdf->SetXY(90,36);
				$pdf->MultiCell(100, 1, $lyf, 0, 'R',0,0,'','',false);


				$nexY = $pdf->GetY()+8;
				// 3. Nombres
				$nom = 'Nombre y Apellidos : '. $arrayReporte['nombre'];
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(210, 1, $nom, 0, 'L',0);

				$nexY = $pdf->GetY()+3;

				// 4. Cargo

				$car = 'Cargo : '. $arrayReporte['area'];
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(210, 1, $car, 0, 'L',0);
				$nexY = $pdf->GetY()+6;

				// 5. Gestion Vacacion

				$gv = 'Gestion Vacacion : '. $period_year;
				$pdf->SetXY($posxmargen+36, $nexY);
				$pdf->MultiCell(60, 1, $gv, 0, 'L',0);

				$this->printRect($pdf,40,60, 11, 6, $hidetop, $hidebottom);
				//$this->printRect($pdf,40,70, 11, 6, $hidetop, $hidebottom);

				// 6. Dias
				$d = 'dias : '.$arrayReporte['days'];
				$pdf->SetXY($posxmargen+136, $nexY);
				$pdf->MultiCell(60, 1, $d, 0, 'L',0);

				$nexY = $pdf->GetY()+16;

				// 7. Licencia Especial

				/*$gv = 'Licencia Especial : ';
				$pdf->SetXY($posxmargen+36, $nexY);
				$pdf->MultiCell(210, 1, $gv, 0, 'L',0);
				$nexY = $pdf->GetY()+6;*/


				// 8. Fecha de Salida

				$fs = 'Fecha de Salida : '.dol_print_date($arrayReporte['fechaini'],'day').' - '.html_entity_decode($arrayReporte['halfdayininame']);
				$pdf->SetXY($posxmargen+36, $nexY);
				$pdf->MultiCell(90, 1, $fs, 0, 'L',0);

				// 9. Fecha de Retorno
				$fr = 'Fecha de Retorno : '.dol_print_date($arrayReporte['fechafin'],'day').' - '.html_entity_decode($arrayReporte['halfdayfinname']);
				$pdf->SetXY($posxmargen+117, $nexY);
				$pdf->MultiCell(90, 1, $fr, 0, 'L',0);
				$nexY = $pdf->GetY()+20;

				// 10. Firma del Solicitante
				$jrh = 'Firma del Solicitante';
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(100, 1, $jrh, 0, 'C',0);

				// 11. Firma del Director
				$vb = 'Firma del Director';
				$pdf->SetXY($this->page_largeur/2, $nexY);
				$pdf->MultiCell($this->posxend-$this->posxca-1, 1, $vb, 0, 'C',0);
				$nexY = $pdf->GetY()+4;

				// 12. texto
				$txt = $outputlangs->transnoentities('TheHeadofPersonnelafterverificationandfeasibilityofthe').': ';
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(200, 1, $txt, 0, 'L',0);
				$nexY = $pdf->GetY()+6;
				$nexYotro = $nexY;

				$this->printRect($pdf,40,122, 11, 6, $hidetop, $hidebottom);
				//$this->printRect($pdf,40,132, 11, 6, $hidetop, $hidebottom);

				// 13. Vacacion Anual
				$vaca = $outputlangs->transnoentities('annualvacation');
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(100, 1, $vaca, 0, 'C',0);
				$nexY = $pdf->GetY()+6;

				// 14. Licencia Especial
				/*$lice = 'Licencia Especial';
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(100, 1, $lice, 0, 'C',0);*/

				// 15. Solicitada por
				$sol = $outputlangs->transnoentities('requestedby').': '.$arrayReporte['nombre'];
				$pdf->SetXY(($this->page_largeur/2)+10, $nexYotro);
				$pdf->MultiCell(($this->page_largeur/2)-22, 1, $sol, 0, 'L',0);

				// 16. Autorizado a partir del
				$apd = 'Autorizado a partir del : '.dol_print_date($arrayReporte['fechaDesde'],'daytext');
				$pdf->SetXY($posxmargen, $nexY+8);
				$pdf->MultiCell(100, 1, $apd, 0, 'L',0);
				$nexY = $pdf->GetY()+6;

				// 17. Hasta
				$hasta = 'Hasta : '.dol_print_date($arrayReporte['fechaHasta'],'daytext');
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(100, 1, $hasta, 0, 'L',0);
				$nexY = $pdf->GetY()+6;

				// 18. Correspondiente a la gestion
				$ges = 'Correspondiente a la Gestion : '.$period_year;
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(100, 1,$ges, 0, 'L',0);
				$nexY = $pdf->GetY()+14;

				// 15. Jefe de Personal
				$sol = 'Jefe de Personal';
				$pdf->SetXY(($this->page_largeur/2)+10, $nexY);
				$pdf->MultiCell(($this->page_largeur/2)-22, 1, $sol, 0, 'C',0);
				$nexY = $pdf->GetY()+11;

				// 16. Fecha
				$freg = 'Fecha. '.dol_print_date($arrayReporte['fechaReg'],'daytext');
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(100, 1, $freg, 0, 'L',0);

				// 17. Registro
				$regf = 'Registro. ';
				$pdf->SetXY($this->page_largeur/2, $nexY);
				$pdf->MultiCell($this->posxend-$this->posxca-1, 1, $regf, 0, 'L',0);
				$nexY = $pdf->GetY()+6;

				// 18. texto 2
				$txt = 'El Sr. Gerente General de "COFADENA", en mérito del informe que antecede';
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(200, 1, $txt, 0, 'L',0);
				$nexY = $pdf->GetY()+6;
				$nexYotro = $nexY;

				$this->printRect($pdf,56,212, 11, 6, $hidetop, $hidebottom);
				//$this->printRect($pdf,56,224, 11, 6, $hidetop, $hidebottom);

				// 18.1. texto 3
				$txt3 = 'Autoriza ';
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(60, 1, $txt3, 0, 'L',0);
				// 18.2. texto 4
				$txt4 = 'la ';
				$pdf->SetXY($posxmargen+60, $nexY);
				$pdf->MultiCell(60, 1, $txt4, 0, 'L',0);
				//$nexY = $pdf->GetY()+6;

				// 19. Vacacion Anual
				$tvaca = 'Vacacion Anual';
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(160, 1, $tvaca, 0, 'C',0);
				$nexY = $pdf->GetY()+8;

				// 20. Licencia Especial
				/*$tlice = 'Licencia Especial';
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(160, 1, $tlice, 0, 'C',0);*/

				$nexY = $pdf->GetY()+8;

				// 21. texto 2 de
				$txtde = 'De: ' . $arrayReporte['nombre'];
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(200, 1, $txtde, 0, 'L',0);
				$nexY = $pdf->GetY()+6;

				// 22. texto 2 observaciones
				$txt = 'OBSERVACIONES : ';
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(200, 1, $txt, 0, 'L',0);
				$nexY = $pdf->GetY()+6;

				// 23. texto 2 fecha
				$txt = 'Fecha : '.dol_print_date($arrayReporte['fechaApro'],'daytext');
				$pdf->SetXY($posxmargen, $nexY);
				$pdf->MultiCell(100, 1, $txt, 0, 'L',0);
				$nexY = $pdf->GetY();

				// 24. GERENTE GENERAL DE "COFADENA"
				$sol = 'GERENTE GENERAL DE "COFADENA"';
				$pdf->SetXY(($this->page_largeur/2)+10, $nexY+4);
				$pdf->MultiCell(($this->page_largeur/2)-22, 1, $sol, 0, 'R',0);

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
					$pdf->SetFont('','', $default_font_size - 1);
					// Add line Adicionamos la linea de abajo de la tabla
					/*if (! empty($conf->global->MAIN_PDF_DASH_BETWEEN_LINES) && $j < ($nblignes - 1))
					{
					$pdf->SetLineStyle(array('dash'=>'1,1','color'=>array(210,210,210)));
					//$pdf->SetDrawColor(190,190,200);
					$pdf->line($this->marge_gauche, $nexY+1, $this->page_largeur - $this->marge_droite, $nexY+1);
					$pdf->SetLineStyle(array('dash'=>0));
					}*/


					/* Aqui incrementamos en 2 para poder volver a escribir una nueva linea*/
					$nexY+=1;

					// Detect if some page were added automatically and output _tableau for past pages
					//Algunos detectar la página de tejo se añadieron de forma automática y usa el la tabla de salida para las páginas anteriores
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
						// La única función para editar el margen inferior de la página actual para configurarlo.
						if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD))
							$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager);
						}

						// Aca tener especial cuidado para poder por que si se recorre una pagina completa se debera recorrer nuestro pie de pagina de firmas esto depende de la poscicion actual de Y y el alto de la pagina
						if ($nexY > $this->page_hauteur+20)

						{

							if ($pagenb == 1)
							{
								$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1);

							}
							else
							{

								$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 0, 1);

							}

							$this->_pagefoot($pdf,$object,$outputlangs,1);
							// Adicionamos una nueva pagina
							$pdf->AddPage();

							if (! empty($tplidx)) $pdf->useTemplate($tplidx);
							$pagenb++;
							if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD))
								$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager);
							$curY = $tab_top + 11;
							$nexY = $tab_top + 11;
						}
				if ($pagenb == 1)
				{
					$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfreetext - $heightforfooter-11, 0, $outputlangs, 0, 0);
					$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
				}
				else
				{
					$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforinfotot - $heightforfreetext - $heightforfooter-11, 0, $outputlangs, 0, 0);
					$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
				}


				// Pied de page
				$this->_pagefoot($pdf,$object,$outputlangs);
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
			$this->printRect($pdf,$this->marge_gauche, 32, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height+26, $hidetop, $hidebottom);
            //$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);
		}
		else
		{
			$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);
		}

		if (empty($hidetop))
		{
			//linea que separa el cuerpo de la solicitud
			$pdf->line($this->marge_gauche, 110, $this->page_largeur-$this->marge_haute, 110);		// line prend une position y en 2eme param et 4eme param
			$pdf->line($this->marge_gauche, 200, $this->page_largeur-$this->marge_haute, 200);		// line prend une position y en 2eme param et 4eme param
		}
	}

	function _pagehead(&$pdf, $object, $showaddress, $outputlangs, $hookmanager)
	{
		global $conf,$langs;

		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("companies");
		$outputlangs->load("assistance@assistance");

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

		// Show Draft Watermark
		if($object->statut==0 && (! empty($conf->global->ASSISTANCE_DRAFT_WATERMARK)) )
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
				$pdf->Image($logo, $this->marge_gauche, $posy, 0, $height);
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

		$pdf->SetFont('','B', $default_font_size);
		$pdf->SetXY($posx-55,$posy);
		$pdf->SetTextColor(0,0,60);
		//$title=$outputlangs->transnoentities("Determinacion de Servicios");
		$title=$outputlangs->transnoentities("SOLICITUD DE VACACION ANUAL");
		$pdf->MultiCell(98, 3, $title, '', 'C');

		$pdf->SetFont('','B',$default_font_size);

		//Numero de papeleta
		$posy+=4;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Nro.")." : " . $object->ref, '', 'R');
		$posy+=1;

	// Show list of linked objects
		$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size, $hookmanager);
	}

	function _pagefoot(&$pdf,$object,$outputlangs,$hidefreetext=0)
	{
		return pdf_pagefoot($pdf,$outputlangs,'ASSISTANCE_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
		//return pdf_pagefoot_fractal($pdf,$outputlangs,'ASSISTANCE_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
	}
}
?>
