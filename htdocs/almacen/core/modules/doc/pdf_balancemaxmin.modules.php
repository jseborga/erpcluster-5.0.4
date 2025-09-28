<?php
/* Copyright (C) 2004-2012	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012	Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2008		Raphael Bertrand	<raphael.bertrand@resultic.fr>
 * Copyright (C) 2010-2012	Juanjo Menent		<jmenent@2byte.es>
 * Copyright (C) 2012      	Christophe Battarel <christophe.battarel@altairis.fr>
 * Copyright (C) 2017      	Yemer Colque Laura <locoto1258@gmail.com>
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

require_once DOL_DOCUMENT_ROOT.'/almacen/core/modules/almacen/modules_almacen.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/productunit.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf_fractal.lib.php';


/**
 *	Class to manage PDF invoice template Crabe
 */
class pdf_balancemaxmin extends ModelePDFAlmacen
{
	var $db;
	var $name;
	var $um;
	var $description;
	var $type;

	var $phpmin = array(4,3,0); // Minimum version of PHP required by module
	var $version = 'dolibarr';

	var $page_largeur;
	var $page_hauteur;
	var $format;
	var $marge_gauche;
	var $marge_droite;
	var $marge_haute;
	var $marge_basse;
	var $emetteur;
	var $type_page=0;
	var $pagesize = 0;
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
		$this->name = "balancemaxmin";
		$this->description = $langs->trans('PDFBalancemaxminDescription');

		// Dimension page pour format A4
		$this->type = 'pdf';
		$formatarray=pdf_getFormat();
		$this->page_largeur = $formatarray['width'];
		$this->page_hauteur = ($formatarray['height']);

		if(empty($this->type_page)) $this->pagesize = $formatarray['width'];
		else $this->pagesize = $formatarray['height'];;

		$this->format = array($this->page_largeur,$this->page_hauteur);
		if (empty($this->type_page))
			$this->format = array($this->page_hauteur,$this->page_largeur);

		$this->marge_gauche=isset($conf->global->MAIN_PDF_MARGIN_LEFT)?$conf->global->MAIN_PDF_MARGIN_LEFT:10;
		$this->marge_droite=isset($conf->global->MAIN_PDF_MARGIN_RIGHT)?$conf->global->MAIN_PDF_MARGIN_RIGHT:10;
		$this->marge_haute =isset($conf->global->MAIN_PDF_MARGIN_TOP)?$conf->global->MAIN_PDF_MARGIN_TOP:10;
		$this->marge_basse =isset($conf->global->MAIN_PDF_MARGIN_BOTTOM)?$conf->global->MAIN_PDF_MARGIN_BOTTOM:10;
		$this->option_logo = 1;                    // Affiche logo
		// $this->option_tva = 1;                     // Gere option tva FACTURE_TVAOPTION
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

		/*
		$this->posxcod=$this->marge_gauche+1;
		$this->posxdesc=28;
		$this->posxuni=119;
		$this->posxalm=130;
		$this->posxmin=141;
		$this->posxmax=152;
		$this->posxsal=163;
		$this->posxalmin=174;
		$this->posxalmax=185;
		*/
		$this->posxcod=$this->marge_gauche+1;
		$this->posxdesc=30;
		$this->posxuni=110;
		$this->posxalm=125;
		$this->posxmin=175;
		$this->posxmax=190;
		$this->posxsal=205;
		$this->posxalmin=225;
		$this->posxalmax=245;
		//$this->posxobs=252;

		if ($this->page_largeur > 297) // To work with US executive format
		{


		$this->posxalm-=10;
		$this->posxmin-=10;
		$this->posxmax-=10;
		$this->posxsal-=10;
		$this->posxalmin-=10;
		$this->posxalmax-=10;
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
		//    echo '<pre>';
		//    print_r($object->lines);
		//    echo '</pre>';
		//    exit;
		global $user,$langs,$conf,$mysoc,$db;

		if (! is_object($outputlangs)) $outputlangs=$langs;
		// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
		if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output='ISO-8859-1';

		$outputlangs->load("main");
		$outputlangs->load("dict");
		$outputlangs->load("companies");
		$outputlangs->load("bills");
		$outputlangs->load("products");
		$outputlangs->load("almacen@almacen");
		//echo 'generando ';exit;

		$aAmaxmin = unserialize($_SESSION['aAmaxmindet']);
		//$entrepot->fetch($inventorysel['fk_entrepot']);
		$entrepot = new Entrepot($db);
		$entrepot->fetch($object->id);


		if ($conf->almacen->dir_output)
		{
			$object->fetch_thirdparty();

			// Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->almacen->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
				$objecten = new Entrepot($this->db);


				$object->ref=dol_sanitizeFileName($objecten->libelle);
				$objectref = dol_sanitizeFileName($object->ref);
				$dir = $conf->almacen->dir_output . "/maxmin" . '/'.$period_year;
				$file = $dir . "/" . 'maxmin'.'_'.($objectref?$objectref.'_':'').date('Ym') . ".pdf";

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
				$productunit = new Productunit($this->db);
				$nblignes = count($inventory);
				if($this->type_page==1)
					$pdf=pdf_getInstance($this->format);
				else
					$pdf=pdf_getInstance($this->format,'mm','L');

				$default_font_size = pdf_getPDFFontSize($outputlangs);	// Must be after pdf_getInstance
				$heightforinfotot = 1;	// 50 Height reserved to output the info and total part
				$heightforfreetext= (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT)?$conf->global->MAIN_PDF_FREETEXT_HEIGHT:5);	// Height reserved to output the free text on last page
				$heightforfooter = $this->marge_basse + 5;	//2  Height reserved to output the footer (value include bottom margin)
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
				$pdf->SetSubject($outputlangs->transnoentities("Order"));
				$pdf->SetCreator("Dolibarr ".DOL_VERSION);
				$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
				$pdf->SetKeyWords($outputlangs->convToOutputCharset($object->ref)." ".$outputlangs->transnoentities("Order"));
				if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);

				$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);   // Left, Top, Right

				// // Positionne $this->atleastonediscount si on a au moins une remise
				// for ($i = 0 ; $i < $nblignes ; $i++)
				//   {
				// 	if ($object->lines[$i]->remise_percent)
				// 	  {
				// 	    $this->atleastonediscount++;
				// 	  }
				//   }

				// New page
				if($this->type_page==1)
					$pdf->AddPage();
				else
					$pdf->AddPage('L');

				if (! empty($tplidx)) $pdf->useTemplate($tplidx);
				$pagenb++;

				$this->_pagehead($pdf, $object, 1, $outputlangs, $hookmanager);

				$pdf->SetFont('','', $default_font_size - 3);
				$pdf->MultiCell(0, 3, '');		// Set interline to 3
				$pdf->SetTextColor(0,0,0);

				//linea para el cuerpo
				$tab_top = 40;
				$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?40:10);
				$tab_height = 88; //130
				$tab_height_newpage = 88; //150
				$iniY = $tab_top + 7;
				$curY = $tab_top + 7;
				$nexY = $tab_top + 7;
				// Loop on each lines
				//recorremos inventory
				$j = 1;
				foreach ((array) $aAmaxmin AS $i => $lines)
				{
					$curY = $nexY;
					$pdf->SetFont('','', $default_font_size - 2);
					   // Into loop to work with multipage
					$pdf->SetTextColor(0,0,0);
					$pdf->setTopMargin($tab_top_newpage);
					$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);	// The only function to edit the bottom margin of current page to set it.
					$pageposbefore=$pdf->getPage();

					// codigo
					$curX = $this->posxcod-1;
					$showpricebeforepagebreak=1;
					//buscamos el producto
					$code = $lines['code'];
					$pdf->SetXY($this->posxcod, $curY);
					$pdf->MultiCell(0, 3, $code, 0, 'L',0);

					//$desc = $lines['label'];
					$label = $lines['label'];
					$pdf->SetXY($this->posxdesc, $curY);
					$pdf->MultiCell($this->posxuni-$this->posxdesc-1, 5, $label, 0, 'L',0,0,'','',true);

					//unidad
					$unit = html_entity_decode($lines['unitshort']);
					$pdf->SetXY($this->posxuni, $curY);
					$pdf->MultiCell($this->posxalm-$this->posxuni-1, 3, $unit, 0, 'L',0);

					//Almacen warhouse
					$labelentrepot =$lines['entrepot'];;
					$pdf->SetXY($this->posxalm, $curY);
					$pdf->MultiCell($this->posxmin-$this->posxalm-1, 5, $labelentrepot, 0, 'L',0);

					// minimo
					$min = $lines['min'];
					$pdf->SetXY($this->posxmin, $curY);
					$pdf->MultiCell($this->posxmax-$this->posxmin-1, 3,$min, 0, 'R',0);

					// maximo
					$max = $lines['max'];
					$pdf->SetXY($this->posxmax, $curY);
					$pdf->MultiCell($this->posxsal-$this->posxmax-1, 3,$max , 0, 'R',0);

					//saldo
					$saldoStock = $lines['saldoStock'];
					$pdf->SetXY($this->posxsal, $curY);
					$pdf->MultiCell($this->posxalmin-$this->posxsal-1, 3,$saldoStock, 0, 'R',0);

					//alerta Min
					$AlertMin = $lines['AlertMin'];
					$pdf->SetXY($this->posxalmin, $curY);
					$pdf->MultiCell($this->posxalmax-$this->posxalmin-1, 3,$AlertMin, 0, 'L',0);

					// ALerta Maximo
					$AlertMax =$lines['AlertMax'];
					$pdf->SetXY($this->posxalmax, $curY);
					$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxalmax-1, 3,$AlertMax, 0, 'L',0);

					//$sumValor += $valor;

					$nexY = $pdf->GetY();
					$pageposafter=$pdf->getPage();
					$pdf->setPage($pageposbefore);
					$pdf->setTopMargin($this->marge_haute);
					$pdf->setPageOrientation('', 1, 0);	// The only function to edit the bottom margin of current page to set it.

					// We suppose that a too long description is moved completely on next page
					if ($pageposafter > $pageposbefore && empty($showpricebeforepagebreak))
					{
						$pdf->setPage($pageposafter); $curY = $tab_top_newpage;
					}

					$pdf->SetFont('','', $default_font_size - 1);   // On repositionne la police par defaut

					// Add line
					if (! empty($conf->global->MAIN_PDF_DASH_BETWEEN_LINES) && $j < ($nblignes - 1))
					{
						$pdf->SetLineStyle(array('dash'=>'1,1','color'=>array(210,210,210)));
						//$pdf->SetDrawColor(190,190,200);
						$pdf->line($this->marge_gauche, $nexY+1, $this->page_hauteur - $this->marge_droite, $nexY+1);
						$pdf->SetLineStyle(array('dash'=>0));
					}

					$nexY+=2;
					    // Passe espace entre les lignes
					$j++;
					// Detect if some page were added automatically and output _tableau for past pages
					while ($pagenb < $pageposafter)
					{
						$pdf->setPage($pagenb);
						if ($pagenb == 1)
						{
							$this->_tableau($pdf, $tab_top, $this->pagesize - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1);
						}
						else
						{
							$this->_tableau($pdf, $tab_top_newpage, $this->pagesize - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 0, 1);
						}
						$this->_pagefoot($pdf,$object,$outputlangs,1);
						$pagenb++;
						$pdf->setPage($pagenb);
						$pdf->setPageOrientation('', 1, 0);
						// The only function to edit the bottom margin of current page to set it.
						if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD))
							$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager);
					}
					if ($nexY+30 > $this->page_largeur)
					//if (isset($object->lines[$i+1]->pagebreak) && $object->lines[$i+1]->pagebreak)
					{
						if ($pagenb == 1)
						{
							$this->_tableau($pdf, $tab_top, $this->pagesize - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1);
						}
						else
						{
							$this->_tableau($pdf, $tab_top_newpage, $this->pagesize - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 0, 1);
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
							$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager);
						$curY = $tab_top + 7;
						$nexY = $tab_top + 7;
					}
				}
				// Show square
				if ($pagenb == 1)
				{
					$this->_tableau($pdf, $tab_top, $this->pagesize - $tab_top - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 0, 1);
					$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
				}
				else
				{
					$this->_tableau($pdf, $tab_top_newpage, $this->pagesize - $tab_top_newpage - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 0, 1);
					$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
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
		$pdf->SetFont('','', $default_font_size - 2);

		$pdf->SetDrawColor(128,128,128);
		$pdf->SetFont('','', $default_font_size - 2);
		$tab_orig = $tab_top;
		$tab_top -= 6;

		if($type_page==0)
		{
			$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_hauteur - $this->marge_gauche - $this->marge_droite, $tab_height+6, $hidetop, $hidebottom);

		}
		else
		{
			$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->pagesize-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);


		}

		if (empty($hidetop))
		{
			$pdf->line($this->marge_gauche, $tab_top+5, $this->page_hauteur-$this->marge_droite, $tab_top+5);		// line prend une position y en 2eme param et 4eme param

			$pdf->SetXY($this->posxcod-1, $tab_top+1);
			$pdf->MultiCell($this->posxdesc-$this->posxcod-1,2, $outputlangs->transnoentities("Code"),'','C');
		}

		$pdf->line($this->posxdesc-1, $tab_top, $this->posxdesc-1, $tab_top + $tab_height+6);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxdesc-1, $tab_top+1);
			$pdf->MultiCell($this->posxssa-$this->posxdesc-1,2, $outputlangs->transnoentities("Description"),'','L');
		}

		$pdf->line($this->posxuni-1, $tab_top, $this->posxuni-1, $tab_top + $tab_height+6);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxuni-1, $tab_top+1);
			$pdf->MultiCell($this->posxalm-$this->posxuni-1,2, $outputlangs->transnoentities("Unidad"),'','C');
		}

		$pdf->line($this->posxalm-1, $tab_top, $this->posxalm-1, $tab_top + $tab_height+6);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxalm-1, $tab_top+1);
			$pdf->MultiCell($this->posxmin-$this->posxalm-1,2, $outputlangs->transnoentities("Warehouse"),'','C');
		}

		$pdf->line($this->posxmin-1, $tab_top, $this->posxmin-1, $tab_top + $tab_height+6);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxmin, $tab_top+1);
			$pdf->MultiCell($this->posxmax-$this->posxmin-1,2, $outputlangs->transnoentities("Mínimo"),'','C');
		}

		$pdf->line($this->posxmax-1, $tab_top, $this->posxmax-1, $tab_top + $tab_height+6);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxmax-1, $tab_top+1);
			$pdf->MultiCell($this->posxsal-$this->posxmax-1,2, $outputlangs->transnoentities("Máximo"),'','C');
		}
		$pdf->line($this->posxsal, $tab_top, $this->posxsal, $tab_top + $tab_height+6);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxsal-1, $tab_top+1);
			$pdf->MultiCell($this->posxalmin-$this->posxsal-1,2, $outputlangs->transnoentities("Saldo"),'','C');
		}
		$pdf->line($this->posxalmin, $tab_top, $this->posxalmin, $tab_top + $tab_height+6);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxalmin-1, $tab_top+1);
			$pdf->MultiCell($this->posxalmax-$this->posxalmin-1,2, $outputlangs->transnoentities("Alerta Max"),'','L');
		}
		$pdf->line($this->posxalmax, $tab_top, $this->posxalmax, $tab_top + $tab_height+6);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxalmax-1, $tab_top+1);
			$pdf->MultiCell(30,2, $outputlangs->transnoentities("Alerta Min"),'','L');
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

		$outputlangs->load("almacen");
		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("companies");


		$aAmaxmin = unserialize($_SESSION['aAmaxmindet']);
		//$entrepot->fetch($inventorysel['fk_entrepot']);
		$entrepot = new Entrepot($this->db);
		$entrepot->fetch($object->id);


		$default_font_size = pdf_getPDFFontSize($outputlangs);

		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);


		// Show Draft Watermark
		if($object->statut==0 && (! empty($conf->global->ALMACEN_DRAFT_WATERMARK)) )
		{
			pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->ALMACEN_DRAFT_WATERMARK);
		}

		$pdf->SetTextColor(0,0,60);
		$pdf->SetFont('','B', $default_font_size + 3);

		$posy=$this->marge_haute;
		if ($this->type_page == 1) $posx=$this->page_largeur-$this->marge_droite-100;
		else $posx=$this->page_hauteur-$this->marge_droite-100;

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
		$pdf->SetXY($posx-55,$posy);
		$pdf->SetTextColor(0,0,60);
		$title=$outputlangs->transnoentities("Reportes Saldos Maximos Minimos");
		$pdf->MultiCell(100, 3, $title, '', 'L');

		$pdf->SetFont('','B',$default_font_size);


		//date
		$posy+=6;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Date and time of generation")." : " . dol_print_date(dol_now(),"dayhour",false,$outputlangs), '', 'R');

		//entrepot


			//$entrepot = new Entrepot($this->db);
			//$entrepot->fetch($inventorysel['fk_entrepot']);
		if($entrepot->lieu != '')
		{
			$posy+=4;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Entrepot")." : " . $entrepot->lieu, '', 'R');

		}
		else
		{

			$posy+=4;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Entrepot")." : " . "Todos", '', 'R');


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
		return pdf_pagefoot_fractal($pdf,$outputlangs,'ALMACEN_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
	}
   }

   ?>
		}
