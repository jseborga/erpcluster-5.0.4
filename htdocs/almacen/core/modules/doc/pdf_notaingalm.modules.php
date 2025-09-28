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

require_once DOL_DOCUMENT_ROOT.'/almacen/core/modules/almacen/modules_almacen.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/ctypemouvement.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/mouvementstockext.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/csources.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';


/**
 *	Class to manage PDF invoice template Crabe
 */
class pdf_notaingalm extends ModelePDFAlmacen
{
	var $db;
	var $name;
	var $um;
	var $description;
	var $type;

	var $phpmin = array(4,3,0); // Minimum version of PHP required by module
	var $version = 'fractal';

	var $page_largeur;
	var $page_hauteur;
	var $format;
	var $marge_gauche;
	var $marge_droite;
	var $marge_haute;
	var $marge_basse;
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

		$this->db = $db;
		$this->name = "Nota de ingreso";
		$this->description = $langs->trans('PDFNotaingalmDescription');

		// Dimension page pour format A4
		$this->type = 'pdf';
		$formatarray=pdf_getFormat();
		$this->page_largeur = $formatarray['width'];
		$this->page_hauteur = (int)($formatarray['height']/2);
		$this->page_hauteur = ($formatarray['height']);

		$this->format = array($this->page_largeur,$this->page_hauteur);
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
		$this->posxcod=$this->marge_gauche+1;
		$this->posxentr=30;
		$this->posxdesc=60;
		//$this->posxsaa=145;
		$this->posxuni=143;
		$this->posxsen=153;
		$this->posxpu=169;
		$this->posxbal=185;
		if ($this->page_largeur < 210) // To work with US executive format
		{
			//$this->posxsaa-=15;
			$this->posxuni-=10;
			$this->posxpu-=10;
			$this->posxsen-=10;
			$this->posxbal-=10;
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
		if ($conf->almacen->dir_output)
		{

			$product = new Product($this->db);
			$entrepot = new Entrepot($this->db);

			//$productunit = new Productunit($this->db);
			$object->fetch_thirdparty();

			// Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->almacen->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
				if ($object->fk_departament>0)
				{
					$departament = new Pdepartamentext($this->db);
					$departament->fetch($object->fk_departament);
					$object->departament = $departament->label;
				}
				else
					$object->departament = '';
				if ($object->fk_source>0)
				{
					$source = new Csources($this->db);
					$source->fetch($object->fk_source);
					$object->source = $source->label;
				}
				if ($object->fk_type_mov>0)
				{
					$objecttype = new Ctypemouvement($this->db);
					$objecttype->fetch($object->fk_type_mov);
					$object->type_mov = $objecttype->label;
				}
				$objstock = new Mouvementstockext($this->db);
				$filterstatic = " AND fk_stock_mouvement_doc = ".$object->id;
				$res = $objstock->fetchAll('ASC', 'datem', 0, 0, array(1=>1), 'AND', $filterstatic);
				if ($res > 0)
				{
					$lines = $objstock->lines;
				}
				//$object->ref=dol_sanitizeFileName($objecten->libelle);
				$objectref = dol_sanitizeFileName($object->ref);
				$dir = $conf->almacen->dir_output . "/" . $objectref.'/notaing';
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
				$nblignes = count($lines);

				$pdf=pdf_getInstance($this->format);
				$default_font_size = pdf_getPDFFontSize($outputlangs);	// Must be after pdf_getInstance
				//$minheight = 30;
				$heightforinfotot = 9;
				//$heightforinfotot = 1;	// 50 Height reserved to output the info and total part
				$heightforfreetext= (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT)?$conf->global->MAIN_PDF_FREETEXT_HEIGHT:5);	// Height reserved to output the free text on last page
				$heightforfooter = $this->marge_basse + 2;	//2  Height reserved to output the footer (value include bottom margin)
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
				$pdf->AddPage();
				if (! empty($tplidx)) $pdf->useTemplate($tplidx);
				$pagenb++;

				$this->_pagehead($pdf, $object, ($object->fk_soc>0?1:0), $outputlangs, $hookmanager);

				$pdf->SetFont('','', $default_font_size - 2);
				$pdf->MultiCell(0, 3, '');		// Set interline to 3
				$pdf->SetTextColor(0,0,0);

				//linea para el cuerpo
				if ($object->fk_soc)
				{
					$tab_top = 90;
					$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?90:10);
				}
				else
				{
					$tab_top = 38;
					$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?38:10);
				}
				$tab_height = 100; //130
				$tab_height_newpage = 100; //150
				$iniY = $tab_top + 7;
				$curY = $tab_top + 7;
				$nexY = $tab_top + 7;

				// imprimimos los tecnicos encargados
				if ($object->label)
				{
					$pdf->SetFont('','', $default_font_size - 3);
					$pdf->writeHTMLCell(190, 3, $this->posxcod-1, $tab_top-2, $langs->trans('Note'), 0, 1);
					$pdf->writeHTMLCell(0, 3, $this->posxcod+10, $tab_top-2, dol_htmlentitiesbr($object->label), 0, 1);
					$nexY = $pdf->GetY();
					$height_note=$nexY-($tab_top-2);

					// Rect prend une longueur en 3eme param
					$pdf->SetDrawColor(192,192,192);
					$pdf->Rect($this->marge_gauche, $tab_top-3, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $height_note+1);

					$tab_height = $tab_height - $height_note;
					$tab_top = $nexY+5;
					$nexY = $tab_top + 7;
				}


				// Loop on each lines
				//recorremos inventory
				$j = 1;
				foreach ((array) $lines AS $i => $line)
				{
					$curY = $nexY;
					$pdf->SetFont('','', $default_font_size - 3);
					   // Into loop to work with multipage
					$pdf->SetTextColor(0,0,0);
					$pdf->setTopMargin($tab_top_newpage);
					$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);	// The only function to edit the bottom margin of current page to set it.
					$pageposbefore=$pdf->getPage();
					// codigo
					$curX = $this->posxcod-1;
					$showpricebeforepagebreak=1;
					$product->fetch($line->fk_product);

					$codigo = $product->ref;
					$pdf->SetXY($this->posxcod, $curY);
					$pdf->MultiCell(0, 3, $codigo, 0, 'L',0);

					$entrepot->fetch($line->fk_entrepot);
					$warehouse = $entrepot->lieu;
					$pdf->SetXY($this->posxentr, $curY);
					$pdf->MultiCell($this->posxdesc-$this->posxentr-1, 3, $warehouse, 0, 'L',0);

					// Description of product line
					$desc = $product->label;
					$pdf->SetXY($this->posxdesc, $curY);
					$pdf->MultiCell($this->posxuni-$this->posxdesc-1, 3, $desc, 0, 'L',0);

					// unit
					$unit = $product->getLabelOfUnit();
					$pdf->SetXY($this->posxuni, $curY);
					$pdf->MultiCell($this->posxsen-$this->posxuni-1, 3, $unit, 0, 'C',0);

					// quant
					$sent = $line->value;
					$pdf->SetXY($this->posxsen, $curY);
					$pdf->MultiCell($this->posxpu-$this->posxsen-1, 3, $sent, 0, 'R',0);

					// pu
					$pu = price(price2num($line->price,'MU'));
					$pdf->SetXY($this->posxpu, $curY);
					$pdf->MultiCell($this->posxbal-$this->posxpu-1, 3, $pu, 0, 'R',0);

					// balance
					$bal = price(price2num($line->value*$line->price,'MT'));
					$pdf->SetXY($this->posxbal, $curY);
					$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxbal, 3, $bal, 0, 'R',0);

					$sumValor += price2num($line->value*$line->price,'MT');

					$nexY = $pdf->GetY();
					$pageposafter=$pdf->getPage();
					$pdf->setPage($pageposbefore);
					$pdf->setTopMargin($this->marge_haute);
					$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
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
						$pdf->line($this->marge_gauche, $nexY+1, $this->page_largeur - $this->marge_droite, $nexY+1);
						$pdf->SetLineStyle(array('dash'=>0));
					}

					$nexY+=2;    // Passe espace entre les lignes
					$j++;
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
							$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 1, 1);
						}
						$this->_pagefoot($pdf,$object,$outputlangs,1);
						$pagenb++;
						$pdf->setPage($pagenb);
						$pdf->setPageOrientation('', 1, 0);	// The only function to edit the bottom margin of current page to set it.
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
				$object->total_ttc = $sumValor;
				// Affiche zone totaux
				$posy=$this->_tableau_tot($pdf, $object, $deja_regle, $bottomlasttab, $outputlangs);


				///$this->_tableau_total($pdf, $object, $bottomlasttab, $outputlangs);
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
		$pdf->SetFont('','', $default_font_size - 3);

		// Output Rect
		$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);	// Rect prend une longueur en 3eme param et 4eme param

		if (empty($hidetop))
		{
			$pdf->line($this->marge_gauche, $tab_top+5, $this->page_largeur-$this->marge_droite, $tab_top+5);		// line prend une position y en 2eme param et 4eme param

			$pdf->SetXY($this->posxcod-1, $tab_top+1);
			$pdf->MultiCell($this->posxentr-$this->posxcod-1,2, $outputlangs->transnoentities("Code"),'','C');
		}

		$pdf->line($this->posxentr-1, $tab_top, $this->posxentr-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxentr-1, $tab_top+1);
			$pdf->MultiCell($this->posxdesc-$this->posxentr-1,2, $outputlangs->transnoentities("Warehouse"),'','L');
		}

		$pdf->line($this->posxdesc-1, $tab_top, $this->posxdesc-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxdesc-1, $tab_top+1);
			$pdf->MultiCell($this->posxuni-$this->posxdesc-1,2, $outputlangs->transnoentities("Description"),'','L');
		}

		$pdf->line($this->posxuni-1, $tab_top, $this->posxuni-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxuni-1, $tab_top+1);
			$pdf->MultiCell($this->posxsen-$this->posxuni-1,2, $outputlangs->transnoentities("Un."),'','C');
		}

		$pdf->line($this->posxsen-1, $tab_top, $this->posxsen-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxsen-1, $tab_top+1);
			$pdf->MultiCell($this->posxpu-$this->posxsen-1,2, $outputlangs->transnoentities("Qty"),'','C');
		}

		$pdf->line($this->posxpu-1, $tab_top, $this->posxpu-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxpu-1, $tab_top+1);
			$pdf->MultiCell($this->posxbal-$this->posxpu-1,2, $outputlangs->transnoentities("P.U."),'','C');
		}

		$pdf->line($this->posxbal, $tab_top, $this->posxbal, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxbal-1, $tab_top+1);
			$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxbal,2, $outputlangs->transnoentities("Total"),'','R');
		}
	}


	/**
	 *	Show total to pay
	 *
	 *	@param	PDF			$pdf           Object PDF
	 *	@param  Facture		$object         Object invoice
	 *	@param  int			$deja_regle     Montant deja regle
	 *	@param	int			$posy			Position depart
	 *	@param	Translate	$outputlangs	Objet langs
	 *	@return int							Position pour suite
	 */
	function _tableau_tot(&$pdf, $object, $deja_regle, $posy, $outputlangs)
	{
		global $conf,$mysoc;

		$sign=1;
		if ($object->type == 2 && ! empty($conf->global->INVOICE_POSITIVE_CREDIT_NOTE)) $sign=-1;

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		$tab2_top = $posy;
		$tab2_hl = 4;
		$pdf->SetFont('','', $default_font_size - 3);

		// Tableau total
		$col1x = 120; $col2x = 170;
		if ($this->page_largeur < 210) // To work with US executive format
		{
			$col2x-=20;
		}
		$largcol2 = ($this->page_largeur - $this->marge_droite - $col2x);

		$useborder=0;
		$index = 0;

		// Total ttc
		$pdf->SetTextColor(0,0,60);
		$pdf->SetFillColor(224,224,224);
		$pdf->SetXY($col1x, $tab2_top + 0);
		$pdf->MultiCell($col2x-$col1x, $tab2_hl, $outputlangs->transnoentities("Total"), 0, 'L', 1);

		$total_ttc = $object->total_ttc;
		$pdf->SetXY($col2x, $tab2_top + 0);
		$pdf->MultiCell($largcol2, $tab2_hl, price($total_ttc), 0, 'R', 1);


		$pdf->SetTextColor(0,0,0);

		$index++;
		return ($tab2_top + ($tab2_hl * $index));
	}


	/**
	 *  Show top header of page.
	 *
	 *  @param	PDF			$pdf     		Object PDF
	 *  @param  CommandeFournisseur		$object     	Object to show
	 *  @param  int	    	$showaddress    0=no, 1=yes
	 *  @param  Translate	$outputlangs	Object lang for output
	 *  @return	void
	 */
	function _pagehead(&$pdf, $object, $showaddress, $outputlangs)
	{
		global $langs,$conf,$mysoc;

		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("orders");
		$outputlangs->load("companies");
		$outputlangs->load("sendings");

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		// Do not add the BACKGROUND as this is for suppliers
		//pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

		//Affiche le filigrane brouillon - Print Draft Watermark
		/*if($object->statut==0 && (! empty($conf->global->COMMANDE_DRAFT_WATERMARK)) )
		{
            pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->COMMANDE_DRAFT_WATERMARK);
        }*/
		//Print content

        $pdf->SetTextColor(0,0,60);
        $pdf->SetFont('','B',$default_font_size + 2);

        $posx=$this->page_largeur-$this->marge_droite-100;
        $posy=$this->marge_haute;

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
				$pdf->SetFont('','B', $default_font_size - 2);
				$pdf->MultiCell(100, 3, $outputlangs->transnoentities("ErrorLogoFileNotFound",$logo), 0, 'L');
				$pdf->MultiCell(100, 3, $outputlangs->transnoentities("ErrorGoToModuleSetup"), 0, 'L');
			}
		}
		else
		{
			$text=$this->emetteur->name;
			$pdf->MultiCell(100, 4, $outputlangs->convToOutputCharset($text), 0, 'L');
		}

		$pdf->SetFont('', 'B', $default_font_size + 3);
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$title=$outputlangs->transnoentities("Nota de Ingreso");
		$pdf->MultiCell(100, 3, $title, '', 'L');
		$posy+=1;

		if ($object->ref)
		{
			$posy+=3;
			$pdf->SetFont('','B', $default_font_size);
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Ref")." : " . $outputlangs->convToOutputCharset($object->ref), '', 'R');
			$posy+=1;
		}

		$pdf->SetFont('','', $default_font_size -1);

		if (! empty($conf->global->PDF_SHOW_PROJECT))
		{
			$object->fetch_projet();
			if (! empty($object->project->ref))
			{
				$posy+=3;
				$pdf->SetXY($posx,$posy);
				$langs->load("projects");
				$pdf->SetTextColor(0,0,60);
				$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Project")." : " . (empty($object->project->ref)?'':$object->projet->ref), '', 'R');
				$posy+=1;
			}
		}

		if (! empty($object->datem))
		{
			$posy+=3;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Date")." : " . dol_print_date($object->datem,"day",false,$outputlangs,true), '', 'R');
			$posy+=1;
		}
		else
		{
			$posy+=3;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(255,0,0);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Draft"), '', 'R');
			$posy+=1;
		}

		$pdf->SetTextColor(0,0,60);
		$usehourmin='day';
		if (!empty($conf->global->SUPPLIER_ORDER_USE_HOUR_FOR_DELIVERY_DATE)) $usehourmin='dayhour';

		if ($object->fk_departament>0)
		{
			$posy+=3;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Departament")." : " . $outputlangs->transnoentities($object->departament), '', 'R');
			$posy+=1;
		}

		$pdf->SetTextColor(0,0,60);

		if ($object->fk_source>0)
		{
			$posy+=3;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Source")." : " . $outputlangs->transnoentities($object->source), '', 'R');
			$posy+=1;
		}

		if ($object->fk_type_mov>0)
		{
			$posy+=3;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Type")." : " . $outputlangs->transnoentities($object->type_mov), '', 'R');
			$posy+=1;
		}

		$pdf->SetTextColor(0,0,60);

		if ($object->ref_ext)
		{
			$posy+=3;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Document")." : " . $outputlangs->transnoentities($object->ref_ext), '', 'R');
			$posy+=1;
		}

		$pdf->SetTextColor(0,0,60);

		// Show list of linked objects
		$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size);

		if ($showaddress)
		{
			// Sender properties
			$carac_emetteur = pdf_build_address($outputlangs, $this->emetteur, $object->thirdparty);

			// Show sender
			$posy=42;
			$posx=$this->marge_gauche;
			if (! empty($conf->global->MAIN_INVERT_SENDER_RECIPIENT)) $posx=$this->page_largeur-$this->marge_droite-80;
			$hautcadre=40;

			// Show sender frame
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($posx,$posy-5);
			$pdf->MultiCell(66,5, $outputlangs->transnoentities("Company").":", 0, 'L');
			$pdf->SetXY($posx,$posy);
			$pdf->SetFillColor(230,230,230);
			$pdf->MultiCell(82, $hautcadre, "", 0, 'R', 1);
			$pdf->SetTextColor(0,0,60);

			// Show sender name
			$pdf->SetXY($posx+2,$posy+3);
			$pdf->SetFont('','B', $default_font_size);
			$pdf->MultiCell(80, 4, $outputlangs->convToOutputCharset($this->emetteur->name), 0, 'L');
			$posy=$pdf->getY();

			// Show sender information
			$pdf->SetXY($posx+2,$posy);
			$pdf->SetFont('','', $default_font_size - 1);
			$pdf->MultiCell(80, 4, $carac_emetteur, 0, 'L');



			// If BILLING contact defined on order, we use it
			$usecontact=false;
			$arrayidcontact=$object->getIdContact('external','BILLING');
			if (count($arrayidcontact) > 0)
			{
				$usecontact=true;
				$result=$object->fetch_contact($arrayidcontact[0]);
			}

			//Recipient name
			// On peut utiliser le nom de la societe du contact
			if ($usecontact && !empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) {
				$thirdparty = $object->contact;
			} else {
				$thirdparty = $object->thirdparty;
			}

			$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);

			$carac_client=pdf_build_address($outputlangs,$this->emetteur,$object->thirdparty,($usecontact?$object->contact:''),$usecontact,'target',$object);

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
			$pdf->MultiCell($widthrecbox, 5, $outputlangs->transnoentities("Proveedor").":",0,'L');
			$pdf->Rect($posx, $posy, $widthrecbox, $hautcadre);

			// Show recipient name
			$pdf->SetXY($posx+2,$posy+3);
			$pdf->SetFont('','B', $default_font_size);
			$pdf->MultiCell($widthrecbox, 4, $carac_client_name, 0, 'L');

			$posy = $pdf->getY();

			// Show recipient information
			$pdf->SetFont('','', $default_font_size - 1);
			$pdf->SetXY($posx+2,$posy);
			$pdf->MultiCell($widthrecbox, 4, $carac_client, 0, 'L');
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
   		return pdf_pagefoot($pdf,$outputlangs,'ALMACEN_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
   	}
	/**
	 *   Show miscellaneous information (payment mode, payment term, ...)
	 *
	 *   @param		PDF			$pdf     		Object PDF
	 *   @param		Object		$object			Object to show
	 *   @param		int			$posy			Y
	 *   @param		Translate	$outputlangs	Langs object
	 *   @return	void
	 */
	function _tableau_total(&$pdf, $object, $posy, $outputlangs)
	{
		global $conf,$langs;
		$default_font_size = pdf_getPDFFontSize($outputlangs);

		$pdf->SetFont('','', $default_font_size - 1);

		$pdf->SetFont('','B', $default_font_size + 3);
		//ref
		//$posy = $pdf->GetY();
		$posy+=12;
		$pdf->SetXY($posxx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(70, 3, $outputlangs->transnoentities("Entregue Conforme"), '', 'R');

		$pdf->SetXY($posxx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(180, 3, $outputlangs->transnoentities("Recibi Conforme"), '', 'R');


		$posy=$pdf->GetY()+1;
		return $posy;
	}
}

?>
