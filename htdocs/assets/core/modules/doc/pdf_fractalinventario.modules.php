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

require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';

require_once DOL_DOCUMENT_ROOT.'/assets/core/modules/assets/modules_assets.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsbeen.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';

require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf_fractal.lib.php';


/**
 *	Class to manage PDF invoice template Crabe
 */
class pdf_fractalinventario extends ModelePDFAssets
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
	
	// type_page para cambio formato 1 vertical 0 horizontal
	var $type_page=0;

	var $emetteur;	

  // Objet societe qui emet

  	//**
   //*	Constructor
   //*
   //*  @param		DoliDB		$db      Database handler
   //*
	function __construct($db)
	{
		global $conf,$langs,$mysoc;

		$langs->load("main");
		$langs->load("bills");

		$this->db = $db;
		$this->name = "Inventory";
		$this->description = $langs->trans('PDFAssetInventoryDescription');

	// Dimension page pour format A4
		
		


	// Dimension page pour format A4
		
		if($this->type_page==1)
		{
			$this->type = 'pdf';
			$formatarray=pdf_getFormat();
			$this->page_largeur = $formatarray['width'];
			$this->page_hauteur = (int)($formatarray['height']/2);
			$this->page_hauteur = ($formatarray['height']);

		}
		else
		{
			$this->type = 'pdf';
			$formatarray=pdf_getFormat();
			$this->page_largeur = $formatarray['width'];
			$this->page_hauteur = (int)($formatarray['height']/2);
			$this->page_hauteur = ($formatarray['height']);

			$this->page_hauteur = $formatarray['width'];
			$this->page_hauteur = (int)($formatarray['height']/2);
			$this->page_largeur = ($formatarray['height']);


		}
		
	/*dimension horizontal
		
	*/



	$this->format = array($this->page_largeur,$this->page_hauteur);
	$this->marge_gauche=isset($conf->global->MAIN_PDF_MARGIN_LEFT)?$conf->global->MAIN_PDF_MARGIN_LEFT:10;
	$this->marge_droite=isset($conf->global->MAIN_PDF_MARGIN_RIGHT)?$conf->global->MAIN_PDF_MARGIN_RIGHT:10;
	$this->marge_haute =isset($conf->global->MAIN_PDF_MARGIN_TOP)?$conf->global->MAIN_PDF_MARGIN_TOP:8;
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
	$this->posxcod=$this->marge_gauche+1;
	$this->posxcod1=15;
	$this->posxdesc=52;
	$this->posxun=120;
	$this->posxqty=140;
	$this->posxqtyl=160;
	$this->posxqty2=180;


		// variables de cofadena

	if($this->type_page==1)
	{
		$this->posxresp=11;
		$this->posxnomap=60;
		$this->posxcargo=110;
		$this->posxfir=152;
	}
	else
	{
		$this->posxresp=11+10;
		$this->posxnomap=60+25;
		$this->posxcargo=110+35;
		$this->posxfir=152+50;
	}



	if($this->type_page==1)
	{
		if ($this->page_largeur < 210) 
		// To work with US executive format
		{
			$this->posxqty-=15;
			$this->posxqtyl-=15;
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


  //
   //*  Function to build pdf onto disk
   //*
   //*  @param		Object		$object				Object to generate
   //*  @param		Translate	$outputlangs		Lang output object
   //*  @param		string		$srctemplatepath	Full path of source filename for generator using a template file
   //*  @param		int			$hidedetails		Do not show line details
   //*  @param		int			$hidedesc			Do not show desc
   //*  @param		int			$hideref			Do not show ref
   //*  @param		object		$hookmanager		Hookmanager object
   //*  @return     int         	    			1=OK, 0=KO
   //
function write_file($object,$outputlangs,$srctemplatepath='',$hidedetails=0,$hidedesc=0,$hideref=0,$hookmanager=false)
{
	//$object =  objeto cassetsgroup;
	
	global $user,$langs,$conf,$mysoc,$productunit;
	if (! is_object($outputlangs)) $outputlangs=$langs;
	$product = new Product($this->db);
	$objbeen = new Cassetsbeen($this->db);

	// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
	if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output='ISO-8859-1';

	$outputlangs->load("main");
	$outputlangs->load("dict");
	$outputlangs->load("companies");
	$outputlangs->load("bills");
	$outputlangs->load("products");
	$outputlangs->load("almacen@almacen");
	$outputlangs->load("orders");
	$outputlangs->load("deliveries");

	$objAssets = new Assetsext($this->db);
	$filter = '';
	if ($object->id > 0)
		$filter = " AND t.type_group = '".$object->code."'";

	$res = $objAssets->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filter);
 		//fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)

	if ($conf->assets->dir_output)
	{	
		$object->fetch_thirdparty();

		$deja_regle = 0;

            // Definition of $dir and $file
		if ($object->specimen)
		{
			$dir = $conf->assets->dir_output;
			$file = $dir . "/SPECIMEN.pdf";
		}
		else
		{
			$objectref = 'inventory';
			if (!empty($object->ref))
				$objectref .= dol_sanitizeFileName($object->ref);
			$dir = $conf->assets->dir_output;
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
			$nblignes = count($objAssets->lines);

				//$objbeen   = new Cassetsbeen($this->db);
				//$objgroup  = new Cassetsgroup($this->db);
				//$objpatrim = new Cassetspatrim($this->db);
				// Add pdfgeneration hook
			if (! is_object($hookmanager))
			{
				include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
				$hookmanager=new HookManager($this->db);
			}
			$hookmanager->initHooks(array('pdfgeneration'));
			$parameters=array('file'=>$file,'object'=>$object,'outputlangs'=>$outputlangs);
			global $action;
			$reshook=$hookmanager->executeHooks('beforePDFCreation',$parameters,$object,$action);    
				// Note that $action and $object may have been modified by some hooks

				// Create pdf instance
			if ($this->type_page==1)
				$pdf=pdf_getInstance($this->format);
			else
				$pdf=pdf_getInstance($this->format,'mm','L');

			$default_font_size = pdf_getPDFFontSize($outputlangs);	
				// Must be after pdf_getInstance
			$pdf->SetAutoPageBreak(1,0);

			$heightforinfotot = 40;	
				// Height reserved to output the info and total part
			$heightforfreetext= (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT)?$conf->global->MAIN_PDF_FREETEXT_HEIGHT:5);	
		        // Height reserved to output the free text on last page
			$heightforfooter = $this->marge_basse + 8;	
	            // Height reserved to output the footer (value include bottom margin)

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
			$pdf->SetKeyWords($outputlangs->convToOutputCharset($object->ref)." ".$outputlangs->transnoentities("Assets")." ".$outputlangs->convToOutputCharset($object->thirdparty->name));
			if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);

			$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);   
				// Left, Top, Right




		// New page
			$pdf->AddPage();
			if (! empty($tplidx)) $pdf->useTemplate($tplidx);
			$pagenb++;

			$this->_pagehead_bcb($pdf, $object, 1, $outputlangs, $hookmanager);


			$pdf->SetFont('','', $default_font_size - 1);
			$pdf->MultiCell(0, 3, '');		
		// Set interline to 3
			$pdf->SetTextColor(0,0,0);

		//linea para el cuerpo
			$tab_top = 38;
			$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?38:10);
				//$tab_height = 180;
			$tab_height = 180;
		 //130
			$tab_height_newpage = 100; 
		//150
			$iniY = $tab_top + 5;
			$curY = $tab_top + 5;
			$nexY = $tab_top + 5;
			$Cont = 0;
				// Loop on each lines
				//for ($i = 0; $i < 4 ; $i++)
			foreach ($objAssets->lines AS $i => $line)
			{
				$resb = $objbeen->fetch(0,$line->been);
				$been = '';
				if ($resb>0)
					$been = $objbeen->label;
				$Cont+=1;
				$curY = $nexY;
				$pdf->SetFont('','', $default_font_size - 2);  
		 			// Into loop to work with multipage
				$pdf->SetTextColor(0,0,0);

				$pdf->setTopMargin($tab_top_newpage);
				$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);	
					// The only function to edit the bottom margin of current page to set it.
				$pageposbefore=$pdf->getPage();

					// codigo
				$curX = $this->posxcod-1;
				$showpricebeforepagebreak=1;

				$codigo = $object->lines[$i]->ref;
					//$codigo = $this->arraycod[0];
				$pdf->SetXY(10, $curY);
				$pdf->MultiCell(0, 3, $i+1, 0,'L'); 

					//$codigo = $this->arraycod[0];

					//$pdf->SetXY($this->posxcod1, $curY);
				$pdf->SetXY(15+3, $curY);
				$pdf->MultiCell(0, 3, $codigo, 0,'L'); 

			// Description of product line
				$desc = $object->lines[$i]->descrip;
					//$desc = $this->arraytex[3];
				$pdf->SetXY($this->posxdesc+2, $curY);
					//$pdf->SetXY(42, $curY);
				$pdf->MultiCell($this->posxdesc+40,3,$desc,0,'L','0'); 

			// UM
				$um = $this->arraycodbar[0];
				$pdf->SetXY($this->posxun, $curY);	
					//$pdf->SetXY(120, $curY);
				$pdf->MultiCell($this->posxun-1,3,$um,0,'L'); 
			// qty
					//$qty = $this->arraycodbar[0];
				$qty = $object->lines[$i]->barcode_type;
				$pdf->SetXY($this->posxqty, $curY);
					//$pdf->SetXY(140, $curY);
				$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxqtyl-$this->posxqty-1,3,$qty,0,'L'); 

					// barcode_type
				$qty1 = $object->lines[$i]->barcode_type;
					//$qtyl = $this->arraymarca[0];
				$pdf->SetXY($this->posxqtyl, $curY);
				//	$pdf->SetXY(160, $curY);
				$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxqtyl,3,$qtyl,0,'C'); 


					// Estado del activo
					//$qty2=$this->arrayserie[0];
				$qty2 = $been;
					//$pdf->SetXY($this->posxqty2, $curY);
				$pdf->SetXY(180, $curY);
				$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxqty2,3,$qty2,0,'L'); 


				$nexY = $pdf->GetY();
				$pageposafter=$pdf->getPage();
				$pdf->setPage($pageposbefore);
				$pdf->setTopMargin($this->marge_haute);
				$pdf->setPageOrientation('', 1, 0);	
					// The only function to edit the bottom margin of current page to set it.

					// We suppose that a too long description is moved completely on next page
				if ($pageposafter > $pageposbefore && empty($showpricebeforepagebreak)) {
					$pdf->setPage($pageposafter); $curY = $tab_top_newpage;
				}

				$pdf->SetFont('','', $default_font_size - 1);  
					// On repositionne la police par defaut		

					// Add line
				if (! empty($conf->global->MAIN_PDF_DASH_BETWEEN_LINES) && $i < ($nblignes - 1))
				{
					$pdf->SetLineStyle(array('dash'=>'1,1','color'=>array(210,210,210)));
						//$pdf->SetDrawColor(190,190,200);
					$pdf->line($this->marge_gauche, $nexY+1, $this->page_largeur - $this->marge_droite, $nexY+1);
					$pdf->SetLineStyle(array('dash'=>0));
				}

					// y $nexY+=2; 
				$nexY+=2; 
				//}
				// Passe espace entre les lignes

				// Detect if some page were added automatically and output _tableau for past pages
				while ($pagenb < $pageposafter)
				{

					$pdf->setPage($pagenb);
					if ($pagenb == 1)
					{
						$this->_tableau_bcb($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1,$this->page_largeur);
					}
					else
					{
						$this->_tableau_bcb($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 0, 1,$this->page_largeur);
					}
					$this->_pagefoot($pdf,$object,$outputlangs,1);
					$pagenb++;
					$pdf->setPage($pagenb);
					$pdf->setPageOrientation('', 1, 0);	
						// The only function to edit the bottom margin of current page to set it.
					if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)) 
						$this->_pagehead_bcb($pdf, $object, 0, $outputlangs, $hookmanager);
				}




					//page_hauteur=148

					// y if ($nexY+28 > $this->page_hauteur)
				if ($nexY+28> $this->page_hauteur)
				{

					
					if (isset($object->lines[$i+1]->pagebreak) && $object->lines[$i+1]->pagebreak)
					{
						if ($pagenb == 1)
						{
							$this->_tableau_bcb($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1,$this->page_largeur);
						}
						else
						{
							$this->_tableau_bcb($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 0, 1,$this->page_largeur);
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
						
						// horizontal  
						if (! empty($tplidx)) $pdf->useTemplate($tplidx);
						$pagenb++;
						if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)) 
							$this->_pagehead_bcb($pdf, $object, 0, $outputlangs, $hookmanager);
						// y $curY = $tab_top + 7;
						// y $nexY = $tab_top + 7;
						$curY = $tab_top +7;
						$nexY = $tab_top +7;
					}
					
				}
			}
				// Show square
			$posyx = $this->page_largeur-$this->marge_gauche-$this->marge_droite+31;
			$pdf->SetXY(10,$posyx);
			$pdf->MultiCell(20, 3,$langs->trans("total").": ".trim($Cont));		

			if ($pagenb == 1)
			{
				$this->_tableau_bcb($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 0, 0,$this->page_largeur);
				$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
			}
			else
			{
				$this->_tableau_bcb($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 0, 0,$this->page_largeur);
				$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
			}
				//$bottomlasttab = $pdf->GetY();
			$this->_tableau_total_bcb($pdf, $object, $bottomlasttab, $outputlangs,$this->page_largeur);

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
	$pdf->SetFont('','', $default_font_size - 1);
		//$pdf->SetFillColor(220, 255, 220);
	$pdf->SetFillColor(215, 235, 255);

	// Output Rect
	$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);	



	if (empty($hidetop))
	{
		$pdf->line($this->marge_gauche, $tab_top+5, $this->page_largeur-$this->marge_droite, $tab_top+5);
			//$pdf->line($this->marge_gauche, $tab_top+5, $this->page_largeur-$this->marge_droite, $tab_top+5);	
		
	// line prend une position y en 2eme param et 4eme param

			/*	$this->posxcod=$this->marge_gauche+1;
			$this->posxdesc=40;
			$this->posxun=154;
			$this->posxqty=170;
			$this->posxqtyl=184;*/

			//	referencia	$this->tab_top=38
			$pdf->SetXY($this->posxcod-1, $tab_top+1);
			$pdf->MultiCell($this->posxdesc-$this->posxcod-1,2, $outputlangs->transnoentities("Code"),'','C','1');
			/*
			$pdf->SetXY($this->posxref-1, $tab_top+1);
			$pdf->MultiCell($this->posxentr-$this->posxref-1,2, $outputlangs->transnoentities("Ref"),'','C');

			*/
			
		}	

			// y $pdf->line($this->posxdesc-1, $tab_top, $this->posxdesc-1, $tab_top + $tab_height);


		$pdf->line($this->posxdesc-1, $tab_top, $this->posxdesc-1, $tab_top + $tab_height);

			//$pdf->line($this->posxdesc-1, $tab_top, $this->posxdesc-1, $tab_top + 200);




		
		if (empty($hidetop))
			// yemer
			//$pdf->MultiCell(15, 30, $hidetop, 0, 'L',0);
		{	
			$pdf->SetXY($this->posxdesc-1, $tab_top+1);
			$pdf->MultiCell($this->page_largeur-$this->posxdesc-$this->marge_gauche,2, $outputlangs->transnoentities("Designation"),'','C','1');
		}

		// y $pdf->line($this->posxun-1, $tab_top, $this->posxun-1, $tab_top + $tab_height);
		//$pdf->line($this->posxun-1, $tab_top, $this->posxun-1, $tab_top + 225);

		//$pdf->SetXY($this->posxcod-1, $tab_top+1);
		$pdf->SetXY(0, 150);
		$pdf->line($this->marge_gauche, $tab_top+5, $this->page_largeur-$this->marge_droite, $tab_top+5);	
		//$pdf->SetXY($this->posxcod-1, $tab_top+1);
		//$pdf->MultiCell($this->posxdesc-$this->posxcod-1,2, $outputlangs->transnoentities("Code"),'','C','1');
		
		

		$this->_tableau_total_bcb($pdf, $object, $posy, $outputlangs,$this->page_largeur);
		//pdf_pie_page_mod1($pdf, $object, $posy, $outputlangs,$this->marge_gauche, $this->page_largeur,$this->marge_droite,$this->page_hauteur,$this->type_page);
		pdf_pie_page_mod12($pdf, $object, $posy, $outputlangs,$this->marge_gauche, $this->page_largeur,$this->marge_droite,$this->page_hauteur,$this->type_page);
	}

  //**
   //*  Show top header of page.
   //*
   //*  @param	PDF			&$pdf     		Object PDF
   //*  @param  Object		$object     	Object to show
   //*  @param  int	    	$showaddress    0=no, 1=yes
   //*  @param  Translate	$outputlangs	Object lang for output
   //*  @param	object		$hookmanager	Hookmanager object
   //*  @return	void
   //*

	function _tableau_bcb(&$pdf, $tab_top, $tab_height, $nexY, $outputlangs, $hidetop=0, $hidebottom=0, $page_largeur,$page_hauteur)
	{



		$posxcod=$this->marge_gauche+1;
		$posxcod1=15;
		$posxdesc=55;
		$posxun=120;
		$posxqty=140;
		$posxqtyl=160;
		$posxqty2=180;

		global $conf;
	// Force to disable hidetop and hidebottom
		$hidebottom=0;


		if ($hidetop) $hidetop=-1;

		$default_font_size = pdf_getPDFFontSize($outputlangs);

	// Amount in (at tab_top - 1)
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('','', $default_font_size - 2);

		$pdf->SetDrawColor(128,128,128);
		$pdf->SetFont('','', $default_font_size - 1);
		//$pdf->SetFillColor(220, 255, 220);
		$pdf->SetFillColor(215, 235, 255);

		// Output Rect
		//$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);	
	// Rect prend une longueur en 3eme param et 4eme param
		


		$tab_height+=10	;

		if (empty($hidetop))
		{
			$pdf->line(10, $tab_top, $page_largeur-10, $tab_top);	
			$pdf->line($page_largeur-10, $tab_top , $page_largeur-10,$tab_top + $tab_height );
			$pdf->line(10, $tab_top, 10, $tab_top + $tab_height);	
			$pdf->line(10, $tab_top + $tab_height, $page_largeur-10, $tab_top + $tab_height);	




			$pdf->line(10, $tab_top+5, $page_largeur-10, $tab_top+5);	
	// line prend une position y en 2eme param et 4eme param

			/*$posxcod=$this->marge_gauche+1;
		$posxcod1=15;
		$posxdesc=40;
		$posxun=120;
		$posxqty=140;
		$posxqtyl=160;
		$posxqty2=180;*/



		$pdf->SetXY($posxcod-1, $tab_top+1);
		$pdf->MultiCell(10,3, $outputlangs->transnoentities("Nro"),'','L','1');
	}


		//$pdf->line($posxcod-1, $tab_top, $posxcod-1, $tab_top + $tab_height);
	$pdf->line($posxcod1-1+5, $tab_top, $posxcod1-1+5, $tab_top + $tab_height);
	if (empty($hidetop))
	{	
		$pdf->SetXY($posxcod1+5, $tab_top+1);
		$pdf->MultiCell($posxdesc-$posxcod1-1,3, $outputlangs->transnoentities("Code"),'','C','1');
	}
	$pdf->line($posxdesc-1, $tab_top, $posxdesc-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{	
		$pdf->SetXY($posxdesc-1, $tab_top+1);
		$pdf->MultiCell($posxun-$posxdesc-1,3, $outputlangs->transnoentities("Designation"),'','C','1');
	}

	$pdf->line($posxun-1, $tab_top, $posxun-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{	
		$pdf->SetXY($posxun-1, $tab_top+1);
		$pdf->MultiCell($posxqty-$posxun-1,3, $outputlangs->transnoentities("Barcode"),'','L','1');
	}
	$pdf->line($posxqty-1, $tab_top, $posxqty-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{	
		$pdf->SetXY($posxqty-1, $tab_top+1);
		$pdf->MultiCell($posxqtyl-$posxqty-1,3, $outputlangs->transnoentities("Brand"),'','L','1');
	}

	$pdf->line($posxqtyl, $tab_top, $posxqtyl-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($posxqtyl, $tab_top+1);
			//$pdf->MultiCell($posxqty2-$posxqty1-1,2, $outputlangs->transnoentities("Series"),'','L','1');
		$pdf->MultiCell($posxqty2 - $posxqtyl - 1,3, $outputlangs->transnoentities("Series"),'','L','1');


	}
	$pdf->line($posxqty2-1, $tab_top, $posxqty2-1, $tab_top + $tab_height);
	if (empty($hidetop))
	{
		$pdf->SetXY($posxqty2, $tab_top+1);
		$pdf->MultiCell($page_largeur-$posxqty2-1 -10,3, $outputlangs->transnoentities("Statut"),'','L','1');

	}

}

	/*
	function _pagehead(&$pdf, $object, $showaddress, $outputlangs, $hookmanager)
	{
		global $conf,$langs;

		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("companies");
		$outputlangs->load("almacen@almacen");

		


		$default_font_size = pdf_getPDFFontSize($outputlangs);

		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

		// Show Draft Watermark
		//if($object->statut==0 && (! empty($conf->global->ALMACEN_DRAFT_WATERMARK)) )
		//{
		//	pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->ALMACEN_DRAFT_WATERMARK);
		//}

		$pdf->SetTextColor(0,0,60);
		$pdf->SetFont('','B', $default_font_size + 2);



		$posy=$this->marge_haute;
		// y $posx0=$this->page_largeur-$this->marge_droite-110;
		// y $posx=$this->page_largeur-$this->marge_droite-100;
		// y $posxx=$this->page_largeur-$this->marge_droite-100;
		$posx0=$this->page_largeur-$this->marge_droite-100;
		$posx=$this->page_largeur-$this->marge_droite-100;
		$posxx=$this->page_largeur-$this->marge_droite-100;

		$pdf->SetXY($this->marge_gauche,$posy);

	// y Logo

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
		
	    //echo $posx0.' '.$posy;exit;
		$pdf->SetFont('','B', $default_font_size + 2);
		$pdf->SetXY($posx,$posy);

		$pdf->SetTextColor(0,0,60);
		$title=$outputlangs->transnoentities("Request shop");

		//100y 8
		$pdf->MultiCell(110, 3, $title, '', 'L');
		//$pdf->MultiCell(110, 3, $posx, '', 'L');
		//$pdf->MultiCell(110, 3, $posy, '', 'L');

		$pdf->SetFont('','B',$default_font_size);
		$posx+=45;
		$posy+=4;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(25, 4, $outputlangs->transnoentities("Dateorder"), '1', 'R');
		$posx+=25;
		$pdf->SetXY($posx,$posy);
		$pdf->MultiCell(25, 4, " ".dol_print_date($object->date_creation,"day",false,$outputlangs), '1', 'R');
		$posx-=25;
		$posy+=4;
		$pdf->SetXY($posx,$posy);
		$pdf->MultiCell(25, 4,"Gestion", '1', 'R');
		$posx+=25;
		$pdf->SetXY($posx,$posy);
		$pdf->MultiCell(25, 4, "2016-2020", '1', 'R');
		$posx-=25;
		$posy+=4;
		$pdf->MultiCell(25,4, $outputlangs->transnoentities("Code")." :",'0','C');
		
		$pdf->SetXY($posx,$posy);
		$pdf->MultiCell(25, 4,"Pagina Nro", '1', 'R');
		$posx+=25;
		$pdf->SetXY($posx,$posy);
		//$pdf->MultiCell(25, 4," ".$this->_pagefoot($pdf,$object,$outputlangs,1), '1', 'R');
		$posx+=35;
		$posy+=2;


		// Show list of linked objects
		$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size, $hookmanager);    
	}
	*/

	function _pagehead_bcb(&$pdf, $object, $showaddress, $outputlangs, $hookmanager)
	{
		global $conf,$langs;

		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("companies");
		$outputlangs->load("assets");

		$objUser =new User($this->db);
		$objUser->fetch($object->fk_user);

		//print_r($objUser);

		//print_r($objMlocation);
		//exit;



		//print_r($objUser);
		//exit;

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

		// Show Draft Watermark
		//if($object->statut==0 && (! empty($conf->global->ALMACEN_DRAFT_WATERMARK)) )
		//{
		//	pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->ALMACEN_DRAFT_WATERMARK);
		//}

		$pdf->SetTextColor(0,0,60);
		$pdf->SetFont('','B', $default_font_size + 2);



		$posy=$this->marge_haute;
		// y $posx0=$this->page_largeur-$this->marge_droite-110;
		// y $posx=$this->page_largeur-$this->marge_droite-100;
		// y $posxx=$this->page_largeur-$this->marge_droite-100;
		$posx0=$this->page_largeur-$this->marge_droite-100;
		$posx=$this->page_largeur-$this->marge_droite-100;
		$posxx=$this->page_largeur-$this->marge_droite-100;

		$pdf->SetXY($this->marge_gauche,$posy);

	// y Logo

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
		$pdf->SetFont('','B', $default_font_size + 3);
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$title=$outputlangs->transnoentities("Assets Assigned");
		$pdf->MultiCell(100, 3, $title, '', 'L');

		$pdf->SetFont('','B',$default_font_size-3);

		$posy+=4;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);	



		//$posx-=10;
		$posy+=4;
//		$posx=110;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 4, $outputlangs->transnoentities("Ref")." : " . $outputlangs->convToOutputCharset($object->ref), '', 'R');
		$posy+=4;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Assignment date")." : " . dol_print_date($object->date_assignment,"%d %b %Y",false,$outputlangs,true), '', 'R');
		//$posx=110;
		$posy+=4;
		$pdf->SetXY($posx,$posy);
		$pdf->MultiCell(100,4, $outputlangs->transnoentities("User")." :".$outputlangs->convToOutputCharset($objUser->lastname).$outputlangs->convToOutputCharset($objUser->firstname),'0','R');
		$posy+=4;

		if ($object->fk_property)
		{
			$objMproperty =new Mproperty($this->db);
			$objMproperty->fetch($object->fk_property);

			$pdf->SetXY($posx,$posy);
			$pdf->MultiCell(100,4, $outputlangs->transnoentities("Property")." :".$outputlangs->convToOutputCharset($objMproperty->ref),'0','R');
			$posy+=4;	
		}
		if($object->fk_location)
		{
			$objMlocation =new Mlocation($this->db);
			$objMlocation->fetch($object->fk_location);


			$pdf->SetXY($posx,$posy);
			$pdf->MultiCell(100,4, $outputlangs->transnoentities("Location").":".$outputlangs->convToOutputCharset($objMlocation->detail),'0','R');
			$posy+=4;
		}

		// Show list of linked objects
		$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size, $hookmanager);    
	}



  //**
   //*   	Show footer of page. Need this->emetteur object
   //*
   //*   	@param	PDF			&$pdf     			PDF
   //* 		@param	Object		$object				Object to show
   //*      @param	Translate	$outputlangs		Object lang for output
   //*      @param	int			$hidefreetext		1=Hide free text
   //*      @return	int								Return height of bottom margin including footer text
   //
	function _pagefoot(&$pdf,$object,$outputlangs,$hidefreetext=0)
	{
		return pdf_pagefoot($pdf,$outputlangs,'ALMACEN_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
	}
	function _tableau_total_bcb(&$pdf, $object, $posy, $outputlangs)
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







}

?>
