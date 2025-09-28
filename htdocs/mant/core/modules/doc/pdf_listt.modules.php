<?php
/* Copyright (C) 2004-2014	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012	Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2008		Raphael Bertrand	<raphael.bertrand@resultic.fr>
 * Copyright (C) 2010-2013	Juanjo Menent		<jmenent@2byte.es>
 * Copyright (C) 2012      	Christophe Battarel <christophe.battarel@altairis.fr>
 * Copyright (C) 2012       Cedric Salvador     <csalvador@gpcsolutions.fr>
 * Copyright (C) 2015       Marcos García       <marcosgdf@gmail.com>
 * Copyright (C) 2017       Ferran Marcet       <fmarcet@2byte.es>
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
 *	\file       htdocs/core/modules/commande/doc/pdf_einstein.modules.php
 *	\ingroup    commande
 *	\brief      Fichier de la classe permettant de generer les commandes au modele Einstein
 */
/*aqui la direccion esta media compleja pero se debe hacer asi XD*/


require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestcontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestuser.class.php';
require_once(DOL_DOCUMENT_ROOT."/mant/class/mworkrequestext.class.php");

require_once DOL_DOCUMENT_ROOT.'/mant/class/mequipmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mtyperepair.class.php';


/**
 *	Classe to generate PDF orders with template Einstein
 */
class pdf_listt extends ModelePDFMant
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
	var $type_page;
	var $emetteur;	// Objet societe qui emet

	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		global $conf,$langs,$mysoc;

		$langs->load("unidad");
		//$langs->load("bills");
		//$langs->load("products");

		$this->db = $db;
		$this->name = "Lista de Ticket";
		$this->description = $langs->trans('PDFListtDescription');

		// Dimension page pour format A4
		$this->type = 'pdf';
		$formatarray=pdf_getFormat();
		$this->page_largeur = $formatarray['width'];
		$this->page_hauteur = $formatarray['height'];
		$this->format = array($this->page_largeur,$this->page_hauteur);
		$this->marge_gauche=isset($conf->global->MAIN_PDF_MARGIN_LEFT)?$conf->global->MAIN_PDF_MARGIN_LEFT:10;
		$this->marge_droite=isset($conf->global->MAIN_PDF_MARGIN_RIGHT)?$conf->global->MAIN_PDF_MARGIN_RIGHT:10;
		$this->marge_haute =isset($conf->global->MAIN_PDF_MARGIN_TOP)?$conf->global->MAIN_PDF_MARGIN_TOP:10;
		$this->marge_basse =isset($conf->global->MAIN_PDF_MARGIN_BOTTOM)?$conf->global->MAIN_PDF_MARGIN_BOTTOM:10;

		$this->option_logo = 1;                    // Affiche logo
		$this->option_tva = 1;                     // Gere option tva FACTURE_TVAOPTION
		$this->option_modereg = 1;                 // Affiche mode reglement
		$this->option_condreg = 1;                 // Affiche conditions reglement
		$this->option_codeproduitservice = 1;      // Affiche code produit-service
		$this->option_multilang = 1;               // Dispo en plusieurs langues
		$this->option_escompte = 0;                // Affiche si il y a eu escompte
		$this->option_credit_note = 0;             // Support credit notes
		$this->option_freetext = 1;				   // Support add of a personalised text
		$this->option_draft_watermark = 1;		   // Support add of a watermark on drafts

		$this->franchise=!$mysoc->tva_assuj;

		// Get source company
		$this->emetteur=$mysoc;
		if (empty($this->emetteur->country_code)) $this->emetteur->country_code=substr($langs->defaultlang,-2);    // By default, if was not defined

		//define typepage
		//1= vertical
		//0 = horizontal
		$this->type_page = 1;
		// Define position of columns
		$this->posxcod=$this->marge_gauche+1;
		//numero
		$this->posxnum=12;
		//Referencia
		$this->posxref=37;
		//fecha
		$this->posxfecha=57;
		//solicitante
		$this->posxsol=77;
		//interno
		$this->posxint=97;
		//equipo
		$this->posxeq=117;
		//inmueble
		$this->posxinm=137;
		//tipo de servicio
		$this->posxtipo=157;
		// orden de trabajo
		$this->posxorden=177;
		//estado
		$this->posxstatus=197;



		$this->posxdiscount=162;
		$this->postotalht=174;
		if (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_WITHOUT_VAT)) $this->posxtva=$this->posxup;
		$this->posxpicture=$this->posxtva - (empty($conf->global->MAIN_DOCUMENTS_WITH_PICTURE_WIDTH)?20:$conf->global->MAIN_DOCUMENTS_WITH_PICTURE_WIDTH);	// width of images


		if ($this->page_largeur < 210) // To work with US executive format
		{
			$this->posxuni-=20;
			$this->posxinp-=20;
		}
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
	 *  @return     int             			    1=OK, 0=KO
	 */
	function write_file($object,$outputlangs,$srctemplatepath='',$hidedetails=0,$hidedesc=0,$hideref=0)
	{
		global $user,$langs,$conf,$mysoc,$db,$hookmanager;

		if (! is_object($outputlangs)) $outputlangs=$langs;
		// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
		if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output='ISO-8859-1';


		$aReportdet = unserialize($_SESSION['aReportdet']);

		//$reporteobj = unserialize($_SESSION['rearrayay']);
		//$fk_departament = unserialize($_SESSION['fk_departament']);
		//$objDepartament = new Pdepartament($this->db);
		$date_ini = unserialize($_SESSION['date_inidet']);
		$date_fin = unserialize($_SESSION['date_findet']);
		$level = unserialize($_SESSION['level']);




		if ($conf->mant->dir_output)
		{
			$object->fetch_thirdparty();

			// Definition of $dir and $file
			if ($object->specimen)
			{

				$dir = $conf->mant->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
				$period_year = $_SESSION['period_year'];
				//$objectref = dol_sanitizeFileName($object->ref);
				$dir = $conf->mant->dir_output . "/" . $level.'/inv';
				//$dir = $conf->mant->dir_output . "/objetive/".$period_year.'/inv';
				$file = $dir . "/".'estado-'.$level.".pdf";
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

				$nblignes = count($reporteobj);
				$pdf=pdf_getInstance($this->format);
				$default_font_size = pdf_getPDFFontSize($outputlangs);	// Must be after pdf_getInstance
				$pdf->SetAutoPageBreak(1,0);

				$heightforinfotot = 40;	// Height reserved to output the info and total part
				$heightforfreetext= (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT)?$conf->global->MAIN_PDF_FREETEXT_HEIGHT:5);	// Height reserved to output the free text on last page
				$heightforfooter = $this->marge_basse + 8;	// Height reserved to output the footer (value include bottom margin)

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
				$pdf->SetSubject($outputlangs->transnoentities("Order"));
				$pdf->SetCreator("Dolibarr ".DOL_VERSION);
				$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
				$pdf->SetKeyWords($outputlangs->convToOutputCharset($object->ref)." ".$outputlangs->transnoentities("Order")." ".$outputlangs->convToOutputCharset($object->thirdparty->name));
				if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);

				$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);   // Left, Top, Right


				// New page
				$pdf->AddPage();
				if (! empty($tplidx)) $pdf->useTemplate($tplidx);
				$pagenb++;
				//this->_pagehead($pdf, $object, 1, $outputlangs);
				$this->_pagehead($pdf, $object, 1, $outputlangs, $hookmanager);
				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->MultiCell(0, 3, '');		// Set interline to 3
				$pdf->SetTextColor(0,0,0);


				//linea para el cuerpo del reporte
				$tab_top = 90;
				$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?42:10);
				$tab_height = 130;
				$tab_height_newpage = 150;
				$tab_top = 38;
				$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?38:10);
				$tab_height = 100; //130
				$tab_height_newpage = 100; //150
				$iniY = $tab_top + 7;
				$curY = $tab_top + 7;
				$nexY = $tab_top + 7;
				// Incoterm
				// recorremos el array de objetivos del array $reporteobj

				$j = 1;
				foreach ((array) $aReportdet AS $i => $lines)
				{


					$ref = $lines['ref'];
					$date = $lines['date'];
					$Applicant = $lines['Applicant'];
					$Internal = $lines['Internal'];
					$Equipo = $lines['Equipo'];
					$Inmueble = $lines['Inmueble'];
					$Tipos = $lines['Tipos'];
					$Orden = $lines['Orden'];
					$Estado = $lines['Estado'];

					$curY = $nexY;
					$pdf->SetFont('','', $default_font_size - 2);
					$pdf->SetTextColor(0,0,0);
					$pdf->setTopMargin($tab_top_newpage);
					$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);
					$pageposbefore=$pdf->getPage();
					//
					$curX = $this->posxcod-1;
					$showpricebeforepagebreak=1;

					//Numeracion
					$pdf->SetXY($this->posxnum, $curY);
					$pdf->MultiCell($this->posxref-$this->posxnum-1, 5, $codigo, 0, 'L',0);

					//Referencia
					$ref = $lines['ref'];
					$pdf->SetXY($this->posxref, $curY);
					$pdf->MultiCell($this->posxfecha-$this->posxref-1, 5, $codigo, 0, 'L',0);

					//fecha
					$date = $lines['date'];
					$pdf->SetXY($this->posxfecha, $curY);
					$pdf->MultiCell($this->posxsol-$this->posxfecha-1, 5, $codigo, 0, 'L',0);

					//Solicitante
					$Applicant = $lines['Applicant'];
					$pdf->SetXY($this->posxsol, $curY);
					$pdf->MultiCell($this->posxint-$this->posxsol-1, 5, $codigo, 0, 'L',0);

					//interno
					$Internal = $lines['Internal'];
					$pdf->SetXY($this->posxint, $curY);
					$pdf->MultiCell($this->posxeq-$this->posxint-1, 5, $codigo, 0, 'L',0);

					//equipo
					$Equipo = $lines['Equipo'];
					$pdf->SetXY($this->posxeq, $curY);
					$pdf->MultiCell($this->posxinm-$this->posxeq-1, 5, $codigo, 0, 'L',0);

					//inmueble
					$Inmueble = $lines['Inmueble'];
					$pdf->SetXY($this->posxinm, $curY);
					$pdf->MultiCell($this->posxtipo-$this->posxinm-1, 5, $codigo, 0, 'L',0);

					//tipo de servicio
					$Tipos = $lines['Tipos'];
					$pdf->SetXY($this->posxtipo, $curY);
					$pdf->MultiCell($this->posxorden-$this->posxtipo-1, 5, $desc, 0, 'L',0,0,'','',true);
					//orden de trabajo
					$Orden = $lines['Orden'];
					$pdf->SetXY($this->posxorden, $curY);
					$pdf->MultiCell($this->posxstatus-$this->posxorden-1, 5, $desc, 0, 'L',0,0,'','',true);

					//estado
					$Estado = $lines['Estado'];
					$pdf->SetXY($this->posxstatus, $curY);
					//$pdf->MultiCell($this->posxuni-$this->posxdesc-1, 3, $desc, 0, 'L',0);
					$pdf->MultiCell($this->page_hauteur-$this->marge_haute-$this->posxstatus-1, 5, $desc, 0, 'L',0,0,'','',true);


					$nPos = $pdf->GetY();
					// Resultado operativo
					//$saa = $lines['result'];
					//$pdf->SetXY($this->posxuni, $curY);
					//$pdf->MultiCell($this->posxinp-$this->posxuni-1, 5, $saa, 0, 'L',0);
					//$nPos1 = $pdf->getY();
					//if ($nPos1>$nPos) $nPos = $nPos1;
					// categorias programaticas
					//$inp = $lines['sigla_ope'];
					//$inp = $lines['catprog'];
					//$pdf->SetXY($this->posxinp, $curY);
					//$pdf->MultiCell($this->posxout-$this->posxinp-1, 5, $inp, 0, 'L',0);





					$nexY = $pdf->GetY();
					$pageposafter=$pdf->getPage();
					$pdf->setPage($pageposbefore);
					$pdf->setTopMargin($this->marge_haute);
					$pdf->setPageOrientation('', 1, 0);
						// The only function to edit the bottom margin of current page to set it.

					// We suppose that a too long description is moved completely on next page
					if ($pageposafter > $pageposbefore && empty($showpricebeforepagebreak))
					{
						$pdf->setPage($pageposafter); $curY = $tab_top_newpage;
					}

					$pdf->SetFont('','', $default_font_size - 1);   // On repositionne la police par defaut

					// Add line
					if (! empty($conf->global->MAIN_PDF_DASH_BETWEEN_LINES) && $j < ($nblignes ))
					{
						$pdf->SetLineStyle(array('dash'=>'1,1','color'=>array(210,210,210)));
						//$pdf->SetDrawColor(190,190,200);
						$pdf->line($this->marge_gauche, $nexY+1, $this->page_largeur - $this->marge_droite, $nexY+1);
						$pdf->SetLineStyle(array('dash'=>0));
					}

					$j++;

					if ($nPos > $nexY) $nexY = $nPos+1;
					else $nexY+=1;

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
							$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager);
					}

					if ($nexY+28 > $this->page_hauteur)
					//if (isset($object->lines[$i+1]->pagebreak) && $object->lines[$i+1]->pagebreak)
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
						// New page
						$pdf->AddPage();

						if (! empty($tplidx)) $pdf->useTemplate($tplidx);
						$pagenb++;
						if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD))
							$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager);
						$curY = $tab_top + 7;
						$nexY = $tab_top + 7;
					}
				}
				// Show square
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

				pdf_pie_page_mod1($pdf, $object, $posy, $outputlangs,$this->marge_gauche, $this->page_largeur,$this->marge_droite,$this->page_hauteur,$this->type_page);

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

			$this->error=$langs->trans("ErrorConstantNotDefined","COMMANDE_OUTPUTDIR");
			return 0;
		}
		$this->error=$langs->trans("ErrorUnknown");
		return 0;
	}


	function _pagehead(&$pdf, $object, $showaddress, $outputlangs, $hookmanager)
	{
		global $conf,$langs;

		$outputlangs->load("mant@mant");

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);
		//$inventorysel = unserialize($_SESSION['inventorysel']);
		//$entrepot = new Entrepot($this->db);
		//$entrepot->fetch($inventorysel['fk_entrepot']);

		// Show Draft Watermark
		if($object->statut==0 && (! empty($conf->global->PDF_DRAFT_WATERMARK)) )
		{
			pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->PDF_DRAFT_WATERMARK);
		}

		$pdf->SetTextColor(0,0,60);
		$pdf->SetFont('','B', $default_font_size + 3);

		$posy=$this->marge_haute;
		$posx=$this->page_largeur-$this->marge_droite-130;
		$posxx=$this->page_largeur-$this->marge_droite-100;

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
		//    echo $posx.' '.$posy;exit;
		//$pdf->SetFont('','B', $default_font_size + 4);
		$pdf->SetFont('times', 'B', 14);
		$pdf->SetXY(10,$posy);
		//$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$title=$outputlangs->transnoentities("Objetives by functional area");
		$pdf->MultiCell(200, 3, $title, '', 'C');

		$pdf->SetFont('','B',$default_font_size);

		//Periodo
		$posy+=4;
		$pdf->SetXY($posxx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Period")." : " . $object->period_year, '', 'R');

		//Area
		$posy+=4;
		$pdf->SetXY($posxx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Area")." : " . $object->labeldepartament, '', 'R');

		$posy+=1;

		// Show list of linked objects
		$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size, $hookmanager);
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
		//$pdf->SetFont('','', $default_font_size - 2);
		$pdf->SetFont('times', '', 9);
		$pdf->SetDrawColor(128,128,128);
		//$pdf->SetFont('','', $default_font_size - 5);
		$pdf->SetFont('times', '', 9);
		// Output Rect
		$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);
			// Rect prend une longueur en 3eme param et 4eme param

		if (empty($hidetop))
		{
			$pdf->line($this->marge_gauche, $tab_top+5, $this->page_largeur-$this->marge_droite, $tab_top+5);
					// line prend une position y en 2eme param et 4eme param

			$pdf->SetXY($this->posxnum-1, $tab_top+1);
			$pdf->MultiCell($this->posx-$this->posxnum-1,2, $outputlangs->transnoentities("Num"),'','C');
		}

		$pdf->line($this->posxnum-1, $tab_top, $this->posxnum-1, $tab_top + $tab_height);

		if (empty($hidetop))
		{
			$pdf->line($this->marge_gauche, $tab_top+5, $this->page_largeur-$this->marge_droite, $tab_top+5);
					// line prend une position y en 2eme param et 4eme param

			$pdf->SetXY($this->posxref-1, $tab_top+1);
			$pdf->MultiCell($this->posx-$this->posxnum-1,2, $outputlangs->transnoentities("Ref"),'','C');
		}
		$pdf->line($this->posxref-1, $tab_top, $this->posxref-1, $tab_top + $tab_height);

		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxfecha-1, $tab_top+1);
			$pdf->MultiCell($this->posxuni-$this->posxdesc-1,2, $outputlangs->transnoentities("date"),'','C');
		}

		$pdf->line($this->posxfecha-1, $tab_top, $this->posxfecha-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxsol-1, $tab_top+1);
			$pdf->MultiCell($this->posxinp-$this->posxuni-1,2, $outputlangs->transnoentities("Solicitante"),'','C');
		}

		$pdf->line($this->posxsol-1, $tab_top, $this->posxsol-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxint-1, $tab_top+1);
			$pdf->MultiCell($this->posxout-$this->posxinp-1,2, $outputlangs->transnoentities("Interno"),'','C');
		}


		$pdf->line($this->posxint-1, $tab_top, $this->posxint-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxeq-1, $tab_top+1);
			$pdf->MultiCell($this->posxout-$this->posxinp-1,2, $outputlangs->transnoentities("Equipo"),'','C');
		}


		$pdf->line($this->posxeq-1, $tab_top, $this->posxeq-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxinm-1, $tab_top+1);
			$pdf->MultiCell($this->posxout-$this->posxinp-1,2, $outputlangs->transnoentities("Inmueble"),'','C');
		}

		$pdf->line($this->posxinm-1, $tab_top, $this->posxinm-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxtipo-1, $tab_top+1);
			$pdf->MultiCell($this->posxout-$this->posxinp-1,2, $outputlangs->transnoentities("type of service "),'','C');
		}


		$pdf->line($this->posxtipo-1, $tab_top, $this->posxtipo-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxorden-1, $tab_top+1);
			$pdf->MultiCell($this->posxout-$this->posxinp-1,2, $outputlangs->transnoentities("Orden de trabajo "),'','C');
		}

		$pdf->line($this->posxorden-1, $tab_top, $this->posxorden-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxstatus-1, $tab_top+1);
			$pdf->MultiCell($this->posxout-$this->posxinp-1,2, $outputlangs->transnoentities(" statut"),'','C');

		}

		$pdf->line($this->posxstatus-1, $tab_top, $this->posxstatus-1, $tab_top + $tab_height);

	}


	/**
	 *   	Show footer of page. Need this->emetteur object
	 *
	 *   	@param	TCPDF		$pdf     			PDF
	 * 		@param	Object		$object				Object to show
	 *      @param	Translate	$outputlangs		Object lang for output
	 *      @param	int			$hidefreetext		1=Hide free text
	 *      @return	int								Return height of bottom margin including footer text
	 */
	function _pagefoot(&$pdf,$object,$outputlangs,$hidefreetext=0,$showtva=0,$showcapital=0)
	{
		global $conf;
		$showdetails=$conf->global->MAIN_GENERATE_DOCUMENTS_SHOW_FOOT_DETAILS;
		$showdetails=0;
		return pdf_pagefoot_fractal($pdf,$outputlangs,'POA_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,$showdetails,$hidefreetext,$showtva,$showcapital);
	}

}

