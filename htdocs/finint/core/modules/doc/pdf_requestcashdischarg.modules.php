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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See The
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

require_once DOL_DOCUMENT_ROOT.'/finint/core/modules/finint/modules_finint.php';
require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashext.class.php';
require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashdet.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';


/**
 *	Class to manage PDF invoice template Crabe
 */
class pdf_requestcashdischarg extends ModelePDFFinint
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
		$this->name = "Requestcashdischarg";
		$this->description = $langs->trans('PDFRequestCashDischargDescription');

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
		$this->posxref=$this->marge_gauche+1;
		$this->posxdat=35;
		$this->posxcat=55;
		//$this->posxpro=125;
		//$this->posxdoc=64;
		//
		//$this->posxdet=85;
		//$this->posxpro=155;
		
		$this->posxpro=70;
		$this->posxdet=100;
		


		//$this->posxtot=178;
		$this->posxtot=170;
		if ($this->page_largeur < 210)
	 // To work with US executive format
		{
			$this->posxtot-=20;
			$this->posxdet-=20;
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
		require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashdeplacementext.class.php';


		$id	= GETPOST('id','int');
		$idd =GETPOST('idd','int');
		if ($conf->projet->enabled)
		{
			require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
			$projet = new Project($this->db);
		}
		$deplacement 	= new Requestcashdeplacementext($db);
		$deplacementtmp = new Requestcashdeplacementext($db);

		//lista los desembolsos
		//$filterstatic = " AND t.fk_account_from = ".$object->fk_account_from;
		//$filterstatic.= " AND t.fk_projet = ".$object->fk_projet;
		$filterstatic= " AND t.fk_request_cash_dest = ".$object->id;
		$filterstatic.= " AND t.entity = ".$object->entity;
		
		//yemer
		$filterstatic.= " AND t.status = 1";
		//
		
		//if ($object->idd>0)
		//	$filterstatic.= " AND t.rowid <= ".$object->idd;
		//echo $filterstatic;
		$res = $deplacement->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterstatic,false);

		$sumatotal = 0;
		$nTotaldep=0;
		$sumadischarg=0;
		$aRecharge = array();
		$fk_recharge = 0;
		if ($res > 0)
		{
			$lines = $deplacement->lines;
			foreach ($lines AS $j => $line)
			{

				

				if (empty($fk_recharge))
				{
					$fk_recharge = $line->id;
					$aRecharge[$line->id] = $line->id;
				}
				else
				{
					if ($fk_recharge != $line->id)
					{
						$aRecharge[$fk_recharge] = $line->id;
						$fk_recharge = $line->id;
					}
				}

				if ($line->id == $object->idd)
				{

					$sumatotal+= $line->amount;
					//vamos a sumar los descargos
					//lista los descargos
					$filterstatic = " AND t.fk_account_from = ".$object->fk_account;
					$filterstatic.= " AND t.fk_request_cash = ".$object->id;
					$filterstatic.= " AND t.entity = ".$object->entity;
					$filterstatic.= " AND t.status = 3";
					$filterstatic.= " AND t.fk_parent = ".$line->id;
					if ($object->idd>0)
						$filterstatic.= " AND t.fk_parent != ".$object->idd;

					$res = $deplacement->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterstatic,false);
					if ($res>0)
					{
						$linesdep = $deplacement->lines;
						foreach ($linesdep AS $k => $linedep)
						{							
							$sumadischarg+= $linedep->amount;
						}
					}
				}
				$nTotaldep+=$line->amount;

			}
			$object->totaldesembolso = $sumatotal;
		}

		
		//pasamos al objeto la recarga si existe
		if ($aRecharge[$object->idd])
		{
			$objtmp = new Requestcashdeplacementext($this->db);
			$restmp = $objtmp->fetch($aRecharge[$object->idd]);
			if ($restmp == 1)
			{
				$object->fk_account_from_recharge = $objtmp->fk_account_from;
				$object->fk_account_dest_recharge = $objtmp->fk_account_dest;
				$object->nro_chq_recharge = $objtmp->nro_chq;
				$object->detail_recharge = $objtmp->detail;
				$object->amount_recharge = $objtmp->amount;
				$object->fk_recharge = $objtmp->id;
			}
		}

		//lista los descargos
		$filterstatic = " AND t.fk_account_from = ".$object->fk_account;
		//$filterstatic.= " AND t.fk_projet = ".$object->fk_projet;
		$filterstatic.= " AND t.fk_request_cash = ".$object->id;
		$filterstatic.= " AND t.entity = ".$object->entity;
		$filterstatic.= " AND t.status = 3 ";
		if ($object->idd>0)
			$filterstatic.= " AND t.fk_parent = ".$object->idd;

		$res = $deplacement->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterstatic,false);
		$lines = $deplacement->lines;

		if (! is_object($outputlangs)) $outputlangs=$langs;
		// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
		if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output='ISO-8859-1';

		$outputlangs->load("finint");
		$outputlangs->load("main");
		$outputlangs->load("dict");
		$outputlangs->load("companies");
		$outputlangs->load("bills");
		$outputlangs->load("products");
		if ($conf->finint->dir_output)
		{
			$object->fetch_thirdparty();
			$objdet = new Requestcashdet($db);
			$objproj = new Project($db);
			$objproj->fetch($object->fk_projet);
			// Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->almacen->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
				$objectref = dol_sanitizeFileName($object->ref);
				$dir = $conf->finint->dir_output . "/" . $objectref;
				$file = $dir . "/" . $objectref.'dis'.($object->idd?'_'.$object->document:'') . ".pdf";
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
				$default_font_size = pdf_getPDFFontSize($outputlangs);
				// Must be after pdf_getInstance
				//distancia del pie espachio libre para texto
				//revisamos que altura debe tener
				$minheight = 20;
				$heightforinfotot = 220;	
				//echo '<hr>ini '.$heightforinfotot;
				if ($nblignes == 1)
					$heightforinfotot = 220;	
				else
				{
					for ($x1=0; $x1 < $nblignes; $x1++)
					{
						$heightforinfotot -= 5;

					}
					if ($heightforinfotot < $minheight)
						$heightforinfotot = $minheight;
				}	
				$heightforinfotot = $minheight;	
				
				//echo $heightforinfotot.' | '.$x1.' | '.$nblignes;exit;
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
				$pdf->SetSubject($outputlangs->transnoentities("Requestcashdischarg"));
				$pdf->SetCreator("Dolibarr ".DOL_VERSION);
				$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
				$pdf->SetKeyWords($outputlangs->convToOutputCharset($object->ref)." ".$outputlangs->transnoentities("Order"));
				if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);

				$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);  
				// New page
				$pdf->AddPage();
				if (! empty($tplidx)) $pdf->useTemplate($tplidx);
				$pagenb++;

				$this->_pagehead($pdf, $object, 1, $outputlangs, $hookmanager,$objproj);

				$pageposafter=$pdf->getPage();


				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->MultiCell(0, 3, '');		
				// Set interline to 3
				$pdf->SetTextColor(0,0,0);

				//linea para el cuerpo
				$tab_top = 47;
				$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?47:10);
				//$tab_height = 120; 
				$tab_height = 100; 
				//130
				$tab_height_newpage = 150; 
				//150

				$iniY = $tab_top + 7;
				$curY = $tab_top + 7;
				$nexY = $tab_top + 7;
				// Loop on each lines
				$i=0;
				//for ($i = 0; $i < $nblignes; $i++)
				$nSumaamount=0;
				if ($object->idd>0 && $sumadischarg>0)
				{
					$curY = $nexY;
					$pdf->SetFont('','', $default_font_size - 3);  
		 			// Into loop to work with multipage
					$pdf->SetTextColor(0,0,0);

					$pdf->setTopMargin($tab_top_newpage);
					$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);	
					// The only function to edit the bottom margin of current page to set it.
					$pageposbefore=$pdf->getPage();

					// codigo
					$curX = $this->posxcod-1;
					$showpricebeforepagebreak=1;

					//ref
					$ref = '';
					$pdf->SetXY($this->posxref, $curY);
					$pdf->MultiCell($this->posxdat-$this->posxref-1, 3, $ref, 0, 'C',0); 
					//$pdf->MultiCell(55, 5,$ref, 1, 'L', 1, 0, '', '', true);
					
					//date
					$date = '';
					$pdf->SetXY($this->posxdat, $curY);
					$pdf->MultiCell($this->posxcat-$this->posxdat-1, 3, $date, 0, 'C',0); 

					//type
					$type = ($line->type_operation == 1?$langs->trans('F'):$langs->trans('R'));
					$pdf->SetXY($this->posxcat, $curY);
					$pdf->MultiCell($this->posxdoc-$this->posxcat-1, 3, $type, 0, 'L',0); 

					//doc
					//$doc = '';
					//$pdf->SetXY($this->posxdoc, $curY);
					//$pdf->MultiCell($this->posxdet-$this->posxdoc-1, 3, $doc, 0, 'L',0); 

					// Description of product line
					$desc = $outputlangs->transnoentities('Gastos anteriores');
					$pdf->SetXY($this->posxdet, $curY);
					$pdf->MultiCell(($conf->project->enabled?$this->posxpro:$this->tot)-$this->posxdet-1, 3, $desc, 0, 'L',0); 

					//total
					$app = price($sumadischarg);
					$pdf->SetXY($this->posxtot, $curY);
					$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxtot, 3, $app, 0, 'R'); 
					//$sumamount+= $object->linesdet[$i]->amount;
					//$sumamountapp+= $object->linesdet[$i]->amount;
					$nSumaamount+=$sumadischarg;	
					$nexY+=5;			

				}
				
				foreach((array) $lines AS $j => $line)
				{									
					//PARA RESUMEN
					
					
					$aResumen[$line->fk_projet_dest]+=$line->amount;

					
					$curY = $nexY;
					$pdf->SetFont('','', $default_font_size - 3);  
		 			// Into loop to work with multipage
					$pdf->SetTextColor(0,0,0);

					$pdf->setTopMargin($tab_top_newpage);
					$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);	
					// The only function to edit the bottom margin of current page to set it.
					$pageposbefore=$pdf->getPage();

					// codigo
					$curX = $this->posxcod-1;
					$showpricebeforepagebreak=1;

					//ref
					$ref = $line->ref;
					$pdf->SetXY($this->posxref, $curY);
					$pdf->MultiCell($this->posxdat-$this->posxref-1, 3, $ref, 0, 'C',0); 
					//$pdf->MultiCell(55, 5,$ref, 1, 'L', 1, 0, '', '', true);
					
					//date
					$date = dol_print_date($line->dateo,'day');
					$pdf->SetXY($this->posxdat, $curY);
					$pdf->MultiCell($this->posxcat-$this->posxdat-1, 3, $date, 0, 'C',0); 

					//type
					$type = ($line->type_operation == 1?$langs->trans('F'):$langs->trans('R'));
					$pdf->SetXY($this->posxcat, $curY);
					$pdf->MultiCell($this->posxdoc-$this->posxcat-1, 3, $type, 0, 'L',0); 

					//doc
					//$doc = $line->nro_chq;
					//$pdf->SetXY($this->posxdoc, $curY);
					//$pdf->MultiCell($this->posxdet-$this->posxdoc-1, 3, $doc, 0, 'L',0); 

					if ($conf->projet->enabled && $line->fk_projet_dest)
					{
						// Description of product line
						$projet->fetch($line->fk_projet_dest);
						
						$refproj = $projet->ref;
						$pdf->SetXY($this->posxpro, $curY);
						$pdf->MultiCell($this->posxtot-$this->posxpro-1, 3, $refproj, 0, 'L',0); 
					}


					// Description of product line
					$desc = dol_trunc($line->detail,40);
					$pdf->SetXY($this->posxdet, $curY);
					$pdf->MultiCell(($conf->project->enabled?$this->posxpro:$this->tot)-$this->posxdet-1, 3, $desc, 0, 'L',0); 

					
					//total
					$app = price($line->amount);
					$pdf->SetXY($this->posxtot, $curY);
					$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxtot, 3, $app, 0, 'R'); 
					//$sumamount+= $object->linesdet[$i]->amount;
					//$sumamountapp+= $object->linesdet[$i]->amount;
					$nSumaamount+=$line->amount;

					$nexY = $pdf->GetY();
					$pageposafter=$pdf->getPage();
					
					$pdf->setPage($pageposbefore);
					$pdf->setTopMargin($this->marge_haute);
					$pdf->setPageOrientation('', 1, 0);	
					// The only function to edit the bottom margin of current page to set it.

					// We suppose that a too long description is moved completely on next page
					//					if ($pageposafter > $pageposbefore && empty($showpricebeforepagebreak)) {
					//						$pdf->setPage($pageposafter); $curY = $tab_top_newpage;
					//					}
					//echo '<hr>bbb pagepa  '.$pageposafter.' > pagepb '.$pageposbefore;
					if ($pageposafter > $pageposbefore) {
						$pdf->setPage($pageposafter); 
						$curY = $tab_top_newpage;
					}
					//echo ' '.$curY;
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

					$nexY+=1;   
		 			// Passe espace entre les lignes

					// Detect if some page were added automatically and output _tableau for past pages
					//echo '<hr>rev '.$pagenb.' < '.$pageposafter;
					//echo '<hr>altura '.$this->page_hauteur .'-'. $tab_top .'-'. $heightforfooter;
					while ($pagenb < $pageposafter)
					{
						//echo '<br>procesapag '.$pagenb;
						$pdf->setPage($pagenb);
						if ($pagenb == 1)
						{
							$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfooter, 0, $outputlangs, 0, 1);
						}
						else
						{
							$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforinfotot - $heightforfooter, 0, $outputlangs, 1, 1);
						}
						$this->_pagefoot($pdf,$object,$outputlangs,0);
						$pagenb++;
						//echo '<br>cambiando chiclo '.$pagenb;
						$pdf->setPage($pagenb);
						$pdf->setPageOrientation('', 1, 0);	
						// The only function to edit the bottom margin of current page to set it.
						if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)) 
							$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager,$objproj);
						
						$iniY = $tab_top + 7;
						$curY = $tab_top + 7;
						$nexY = $tab_top + 7;

					}
					//echo '<hr>'. $nexY.' > '.$this->page_hauteur;
					if ($nexY+50 > $this->page_hauteur)
					{
						if ($pagenb == 1)
						{
							$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfooter, 0, $outputlangs, 0, 1);
						}
						else
						{
							$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforinfotot - $heightforfooter, 0, $outputlangs, 0, 1);
						}
						$this->_pagefoot($pdf,$object,$outputlangs,1);
						// New page
						$pdf->AddPage();

						if (! empty($tplidx)) $pdf->useTemplate($tplidx);
						$pagenb++;
						if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD))
							$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager,$objproj);
						$curY = $tab_top + 7;
						$nexY = $tab_top + 7;

					}

					if (isset($line->pagebreak) && $line->pagebreak && $abc)
					{
						if ($pagenb == 1)
						{
							$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1);
						}
						else
						{
							$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 1, 1);
						}
						$this->_pagefoot($pdf,$object,$outputlangs,1);
						// New page
						$pdf->AddPage();
						if (! empty($tplidx)) $pdf->useTemplate($tplidx);
						$pagenb++;
						if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)) 
							$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager,$objproj);
						//echo '<hr>b1 '.$nexY;
						$iniY = $tab_top + 7;
						$curY = $tab_top + 7;
						$nexY = $tab_top + 7;

					}
					$i++;
				}

				
				$object->total_ttc = $nSumaamount;

				// Show square

				if ($pagenb == 1)
				{
					$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 0, 0);
					$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
				}
				else
				{
					$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 1, 0);
					$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
					$iniY = $tab_top + 7;
					$curY = $tab_top + 7;
					$nexY = $tab_top + 7;
				}


				$this->_tableau_total($pdf, $object, $bottomlasttab, $outputlangs,$sumamount,$sumamountapp);				
				// Pied de page
				$this->_pagefoot($pdf,$object,$outputlangs);

				// nueva pagina para saldo //////				
				if(empty($object->idd))
				{
					$object->nTotaldep = $nTotaldep;
					$object->aResumen = $aResumen;
					$pdf->AddPage();
					$this->_pagehead($pdf, $object, 1, $outputlangs, $hookmanager,$objproj);
					//$this->_tableau_total_deplacement($pdf, $object, $bottomlasttab, $outputlangs,$sumamount,$sumamountapp);
					$this->_tableau_total_deplacement($pdf, $tab_top, $tab_height, $nexY, $outputlangs, $hidetop=0, $hidebottom=0,$aResumen,$nTotaldep);
					$this->_pagefoot($pdf,$object,$outputlangs);
				}
				
				/////////////////////////

				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				$pdf->Close();
				//exit;
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
			$this->error=$langs->trans("ErrorConstantNotDefined","FININT_OUTPUTDIR");
			return 0;
		}
		$this->error=$langs->trans("ErrorUnknown");
		return 0; 
	  	// Erreur par defaut
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
  		//echo ' <hr>'.$tab_top.' '.$tab_height.' '.$nexY;
		// Force to disable hidetop and hidebottom


  		$tab_height-=10;

  		$hidebottom=0;
  		if ($hidetop) $hidetop=-1;
  		$default_font_size = pdf_getPDFFontSize($outputlangs);

		// Amount in (at tab_top - 1)
  		$pdf->SetTextColor(0,0,0);
  		$pdf->SetFont('','', $default_font_size - 2);

  		$pdf->SetDrawColor(127, 127, 255);
  		$pdf->SetFillColor(0, 0, 255);
  		$pdf->SetTextColor(0, 0, 255);
		//$pdf->SetDrawColor(128,128,128);
  		$pdf->SetFont('','', $default_font_size - 2);

		// Output Rect
  		$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);	
		// Rect prend une longueur en 3eme param et 4eme param

  		if (empty($hidetop))
  		{
  			$pdf->line($this->marge_gauche, $tab_top+5, $this->page_largeur-$this->marge_droite, $tab_top+5);	
			// line prend une position y en 2eme param et 4eme param

  			$pdf->SetXY($this->posxref-1, $tab_top+1);
  			$pdf->MultiCell($this->posxdat-$this->posxref-1,2, $outputlangs->transnoentities("Ref"),'','C');
  		}
  		$pdf->line($this->posxdat-1, $tab_top, $this->posxdat-1, $tab_top + $tab_height);
  		if (empty($hidetop))
  		{	
  			$pdf->SetXY($this->posxdat-1, $tab_top+1);
  			$pdf->MultiCell($this->posxcat-$this->posxdat-1,2, $outputlangs->transnoentities("Date"),'','C');
  		}
  		$pdf->line($this->posxcat-1, $tab_top, $this->posxcat-1, $tab_top + $tab_height);
  		if (empty($hidetop))
  		{	
  			$pdf->SetXY($this->posxcat-1, $tab_top+1);
  			$pdf->MultiCell($this->posxdoc-$this->posxcat-1,2, $outputlangs->transnoentities("Type"),'','L');
  		}
  		//$pdf->line($this->posxdoc-1, $tab_top, $this->posxdoc-1, $tab_top + $tab_height);
  		//if (empty($hidetop))
  		//{	
  			//$pdf->SetXY($this->posxdoc-1, $tab_top+1);
  			//$pdf->MultiCell($this->posxdet-$this->posxdoc-1,2, $outputlangs->transnoentities("Doc."),'','L');
  		//}

  		if ($conf->projet->enabled)
  		{
  			$pdf->line($this->posxpro-1, $tab_top, $this->posxpro-1, $tab_top + $tab_height);
  			if (empty($hidetop))
  			{	
  				$pdf->SetXY($this->posxpro-1, $tab_top+1);
  				$pdf->MultiCell($this->posxtot-$this->posxpro-1,2, $outputlangs->transnoentities("Project"),'','L');
  			}
  		}


  		$pdf->line($this->posxdet-1, $tab_top, $this->posxdet-1, $tab_top + $tab_height);
  		if (empty($hidetop))
  		{	
  			$pdf->SetXY($this->posxdet-1, $tab_top+1);
  			$pdf->MultiCell(($conf->project->enabled?$this->posxpro:$this->tot)-$this->posxdet-1,2, $outputlangs->transnoentities("Detail"),'','L');
  		}

  		

  		$pdf->line($this->posxtot, $tab_top, $this->posxtot, $tab_top + $tab_height);
  		if (empty($hidetop))
  		{
  			$pdf->SetXY($this->posxtot-1, $tab_top+1);
  			$pdf->MultiCell(20,2, $outputlangs->transnoentities("Amount"),'','C');
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
  	function _pagehead(&$pdf, $object, $showaddress, $outputlangs, $hookmanager,$objproj)
  	{
  		global $conf,$langs;

  		$outputlangs->load("main");
  		$outputlangs->load("bills");
  		$outputlangs->load("propal");
  		$outputlangs->load("companies");
  		$outputlangs->load("request@request");

  		$default_font_size = pdf_getPDFFontSize($outputlangs);

  		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

	// Show Draft Watermark
  		if($object->statut==0 && (! empty($conf->global->REQUEST_DRAFT_WATERMARK)) )
  		{
  			pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->REQUEST_DRAFT_WATERMARK);
  		}

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
  		$pdf->SetFont('','B', $default_font_size + 3);
  		$pdf->SetXY($posx-20,$posy);
  		$pdf->SetTextColor(0,0,60);
  		$title=$outputlangs->transnoentities("Expensereport");
  		$pdf->MultiCell(100, 3, $title, '', 'L');

  		$pdf->SetFont('','B',$default_font_size);

  		$posy+=5;
  		$pdf->SetXY($posx,$posy);
  		$pdf->SetTextColor(0,0,60);
  		$pdf->MultiCell(100, 4, $outputlangs->transnoentities("Ref")." : " . $outputlangs->convToOutputCharset($object->ref), '', 'R');

  		$posy+=1;
  		$pdf->SetFont('','', $default_font_size - 2);
		//entrepot
  		if ($object->fk_entrepot)
  		{
  			$objEntrepot = new Entrepot($this->db);
  			$objEntrepot->fetch($object->fk_entrepot);
  			$posy+=4;
  			$pdf->SetXY($posx,$posy);
  			$pdf->SetTextColor(0,0,60);
  			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Entrepot")." : " . $outputlangs->convToOutputCharset($objEntrepot->lieu), '', 'R');
  		}
		//number fabrication
  		if ($object->fk_fabrication)
  		{
  			$objFab = new Fabrication($this->db);
  			$objFab->fetch($object->fk_fabrication);
  			$posy+=4;
  			$pdf->SetXY($posx,$posy);
  			$pdf->SetTextColor(0,0,60);
  			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Productionnumber")." : " . $outputlangs->convToOutputCharset($objFab->ref), '', 'R');
  		}

		//dateorder
  		$posy+=4;
  		$pdf->SetXY($posx,$posy);
  		$pdf->SetTextColor(0,0,60);
  		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Date")." : " . dol_print_date($object->date_create,"day",false,$outputlangs), '', 'R');
  		//registramos codigo proyecto y nombre proyecto
  		if ($object->fk_projet)
  		{
  			$objproj = new Project($this->db);
  			$objproj->fetch($object->fk_projet);
  			$posy+=4;
  			$pdf->SetXY($posx,$posy);
  			$pdf->SetTextColor(0,0,60);
  			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Cod. Proyecto")." : " . $objproj->ref, '', 'R');
  			$posy+=4;
  			$pdf->SetXY($posx,$posy);
  			$pdf->SetTextColor(0,0,60);
  			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Name")." : " . $objproj->title, '', 'R');

  		}
		//creado por
  		$objuser = new User($this->db);
  		$objuser->fetch($object->fk_user_create);
  		$posy+=4;
  		$pdf->SetXY($posx,$posy);
  		$pdf->SetTextColor(0,0,60);
  		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("The")." : " . $objuser->lastname.' '.$objuser->firstname, '', 'R');

		//aprobado por
  		$objuser = new User($this->db);
  		if ($objuser->fetch($object->fk_user_approved)>0)
  		{
  			$posy+=4;
  			$pdf->SetXY($posx,$posy);
  			$pdf->SetTextColor(0,0,60);
  			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Via")." : " . $objuser->lastname.' '.$objuser->firstname, '', 'R');
  		}
		//autorizado y desembolsado por
  		$objuser = new User($this->db);
  		if ($objuser->fetch($object->fk_user_authorized)>0)
  		{
  			$posy+=4;
  			$pdf->SetXY($posx,$posy);
  			$pdf->SetTextColor(0,0,60);
  			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("A")." : " . $objuser->lastname.' '.$objuser->firstname, '', 'R');
  		}

  		/*
  		$nCash= unserialize($_SESSION['aReportSuma']);
  		$nCash = $object->totaldesembolso;
  		$posy+=4;
  		$pdf->SetXY($posx,$posy);
  		$pdf->SetTextColor(0,0,60);
  		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Cash")." : " . price($nCash), '', 'R');
		*/
  		
  		


	// Show list of linked objects
  		$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size, $hookmanager);
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
  		return pdf_pagefoot($pdf,$outputlangs,'REQUEST_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
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

	/*
	function _tableau_total(&$pdf, $object, $posy, $outputlangs,$total1=0,$total2=0)
	{
		global $conf,$langs;
		$default_font_size = pdf_getPDFFontSize($outputlangs);

		$pdf->SetFont('','', $default_font_size - 1);

        // If France, show VAT mention if not applicable
		if ($this->emetteur->country_code == 'FR' && $this->franchise == 1)
		{
			$pdf->SetFont('','B', $default_font_size - 2);
			$pdf->SetXY($this->marge_gauche, $posy);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("VATIsNotUsedForInvoice"), 0, 'L', 0);

			$posy=$pdf->GetY()+4;
		}

		$posxval=32;
		//52
		if (! empty($total2))
		{
			$outputlangs->load("sendings");
			$pdf->SetFont('','B', $default_font_size - 2);
			$pdf->SetXY($this->marge_gauche, $posy);
			$titre = $outputlangs->transnoentities("Total").':';
			$pdf->MultiCell($this->qty, 4, $titre, 0, 'L');
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($posxval, $posy);
			$total2 = price($total2);
			$pdf->MultiCell($this->tot-1, 4, $total2, 0, 'R');
			$posy=$pdf->GetY()+1;
		}
		//registramos texto de Solicitante
		$posy += 15;
		$titre = $outputlangs->transnoentities("Solicitante").':';
		$pdf->SetXY($posxval, $posy);
		$pdf->MultiCell($this->qty, 4, $titre, 0, 'L');
		$pdf->Line(65,$posy,125,$posy);

		//registramos texto de Solicitante
		$posy += 15;
		$pdf->SetXY($posxval, $posy);
		$titre = $outputlangs->transnoentities("Desembolsado").': ';
		$pdf->MultiCell($this->qty, 4, $titre, 0, 'L');
		$pdf->Line(65,$posy,125,$posy);
		//registramos texto de Solicitante
		$posy += 15;
		$pdf->SetXY($posxval, $posy);
		$titre = $outputlangs->transnoentities("Cuenta").': ';
		$objaccount = new Account($this->db);
		$objaccount->fetch($object->fk_account);
		if ($objaccount->id == $object->fk_account)
		{
			$pdf->MultiCell($this->qty, 4, $titre.$objaccount->label, 0, 'L');
		}
		//registramos la fecha autorizada
		$posy += 4;
		$pdf->SetXY($posxval, $posy);
		$titre = $outputlangs->transnoentities("Date").': ';
		$pdf->MultiCell($this->qty, 4, $titre.dol_print_date($object->date_authorized,'dayhour'), 0, 'L');
		//registramos el monto autorizado
		$cName = 'Operacion';
		if ($object->fk_type)
		{
			$cName = $langs->transnoentities('PaymentType'.STRTOUPPER($object->fk_type));
			//$sql = "SELECT code, libelle";
			//$sql.= " FROM ".MAIN_DB_PREFIX."c_paiement";
			//$sql.= " WHERE code = '".$object->fk_type."'";
			//$result = $this->db->query($sql);
			//if ($result)
			//{
			//	$num = $this->db->num_rows($result);
			//	if ($num>0)
			//		$obj = $this->db->fetch_object($result);
			//	$cName = $obj->libelle;
			//}
			//$this->db->free($result);
		}
		$posy += 4;
		$pdf->SetXY($posxval, $posy);
		$titre = $cName.': '.$object->nro_chq.'; ';
		$pdf->MultiCell($this->qty, 4, $titre, 0, 'L');

		$posy=$pdf->GetY()+1;
		return $posy;
	}
	*/

	function _tableau_total(&$pdf, $object, $posy, $outputlangs,$total1=0,$total2=0)
	{

		global $conf,$mysoc;

		$sign=1;
		
		$nCash= unserialize($_SESSION['aReportSuma']);
		$nCash = $object->totaldesembolso;


		$default_font_size = pdf_getPDFFontSize($outputlangs);

		$tab2_top = 230;//$posy+100
		$tab2_hl = 4;
		$pdf->SetFont('','', $default_font_size-2);

		// Tableau total
		$col1x = 190;
		$col2x = 260;
		$largcol2 = ($this->page_hauteur - $this->marge_droite - $col2x);

		$useborder=0;
		$index = 0;

		// Total ttc
		$pdf->SetTextColor(0,0,60);
		//$pdf->SetFillColor(224,224,224);
		$pdf->SetFillColor(100, 149, 273);
		$total_ttc =$object->total_ttc;

		/****************************************************************** */
		
		// Texto Total
		$pdf->SetXY($this->posxdet, $tab2_top +15);
		$pdf->MultiCell($this->posxtot-$this->posxdet, 5,"Total", 0, 'L',1);
		
		// Total presupuesto
		$pdf->SetXY($this->posxtot, $tab2_top +15);
		$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxtot, 5,price(price2num($total_ttc,'MT')), 0, 'R',1);
		
		if ($object->fk_recharge)
		{
			$objAccount = new Account($this->db);
			$objAccount->fetch($object->fk_account_from_recharge);

			//transfer of
			$pdf->SetXY($this->posxref, $tab2_top +21);
			$pdf->MultiCell($this->posxcat-$this->posxref, 5,$outputlangs->transnoentities("Transferof"), 0, 'L',0);

			$pdf->SetXY($this->posxpro, $tab2_top +21);
			$pdf->MultiCell($this->posxtot-$this->posxpro, 5,$objAccount->label, 0, 'L',0);
			$objAccount->fetch($object->fk_account_dest_recharge);			
			// deposited to
			$pdf->SetXY($this->posxref, $tab2_top +24);
			$pdf->MultiCell($this->posxcat-$this->posxref, 5,$outputlangs->transnoentities("Depositedto"), 0, 'L',0);
			$pdf->SetXY($this->posxpro, $tab2_top +24);
			$pdf->MultiCell($this->posxtot-$this->posxpro, 5,$objAccount->label, 0, 'L',0);
			// amount
			$pdf->SetXY($this->posxref, $tab2_top +27);
			$pdf->MultiCell($this->posxcat-$this->posxref, 5,$outputlangs->transnoentities("Total"), 0, 'L',0);
			$pdf->SetXY($this->posxpro, $tab2_top +27);
			$pdf->MultiCell($this->posxtot-$this->posxpro, 5,price(price2num($object->amount_recharge,'MT')), 0, 'L',0);

			// transaction number
			$pdf->SetXY($this->posxref, $tab2_top +30);
			$pdf->MultiCell($this->posxcat-$this->posxref, 5,$outputlangs->transnoentities("Transactionnumber"), 0, 'L',0);
			$pdf->SetXY($this->posxpro, $tab2_top +30);
			$pdf->MultiCell($this->posxtot-$this->posxpro, 5,$object->nro_chq_recharge, 0, 'L',0);
			// detail
			$pdf->SetXY($this->posxref, $tab2_top +33);
			$pdf->MultiCell($this->posxcat-$this->posxref, 5,$outputlangs->transnoentities("Detail"), 0, 'L',0);
			$pdf->SetXY($this->posxpro, $tab2_top +33);
			$pdf->MultiCell($this->posxtot-$this->posxpro, 5,$object->detail_recharge, 0, 'L',0);

		}

		


		//$nSaldo=$nCash-$total_ttc;
		// Texto Total
		//$pdf->SetXY($this->posxpro, $tab2_top +21);
		//$pdf->MultiCell($this->posxtot-$this->posxpro+27, 5,"Saldo", 0, 'L',1);
		
		// Total presupuesto
		//$pdf->SetXY($this->posxtot, $tab2_top +21);
		//$pdf->MultiCell($this->posxtot-$this->posxpro, 5,price(price2num($nSaldo,'MT')), 0, 'R',1);

		//// Total preventivo
		//$pdf->SetXY($this->posxamountpre, $tab2_top +15);
		//$pdf->MultiCell($this->posxamountcomp-$this->posxamountpre-1, 5,price(price2num($total_ttpre,'MT')), 0, 'R',1);

		// Total compremetido
		//$pdf->SetXY($this->posxamountcomp, $tab2_top +15);
		//$pdf->MultiCell($this->posxamountdev-$this->posxamountcomp-1, 5,price(price2num($total_ttcomp,'MT')), 0, 'R',1);

		// Total devengado
		//$pdf->SetXY($this->posxamountdev, $tab2_top +15);
		//$pdf->MultiCell($this->page_hauteur-$this->marge_droite-$this->posxamountdev-1, 5,price(price2num($total_ttdev,'MT')), 0, 'R',1);

		$pdf->SetTextColor(0,0,0);

		$index++;
		return ($tab2_top + ($tab2_hl * $index));





	}

	function _tableau_total_deplacement(&$pdf, $tab_top, $tab_height, $nexY, $outputlangs, $hidetop=0, $hidebottom=0,$aResumen,$nTotaldep)
	{
		global $conf,$outputlangs,$mysoc;

		require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
		$projet = new Project($this->db);
		//$objtmp = new Requestcashdeplacementext($this->db);


		$tab_height-=10;
		$hidebottom=0;
		if ($hidetop) $hidetop=-1;
		$default_font_size = pdf_getPDFFontSize($outputlangs);
		// Amount in (at tab_top - 1)
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('','', $default_font_size - 2);
		$pdf->SetDrawColor(127, 127, 255);
		$pdf->SetFillColor(0, 0, 255);
		$pdf->SetTextColor(0, 0, 255);

		$pdf->SetFont('','', $default_font_size - 1);
		$pdf->MultiCell(0, 3, '');		
		// Set interline to 3
		$pdf->SetTextColor(0,0,0);

		//$pdf->SetDrawColor(128,128,128);
		$pdf->SetFont('','', $default_font_size - 2);
		$pdf->SetXY($this->posxref-1, $tab_top-5);
		$pdf->MultiCell($this->page_largeur-$this->posxref-1-$this->marge_droite,2, $outputlangs->transnoentities("Expensesummary"),'','C');
		if (empty($hidetop))
		{
			$pdf->line($this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_droite, $tab_top);	
			$pdf->line($this->marge_gauche, $tab_top+5, $this->page_largeur-$this->marge_droite, $tab_top+5);	
			// line prend une position y en 2eme param et 4eme param

			$pdf->SetXY($this->posxref-1, $tab_top+1);
			$pdf->MultiCell($this->posxdat-$this->posxref-1,2, $outputlangs->transnoentities("Ref"),'','C');
		}


		if (empty($hidetop))
		{
			
			$pdf->line($this->marge_gauche, $tab_top+5, $this->page_largeur-$this->marge_droite, $tab_top+5);	
			// line prend une position y en 2eme param et 4eme param

			$pdf->SetXY($this->posxdat-1, $tab_top+1);
			$pdf->MultiCell($this->posxdet-$this->posxdat-1,2, $outputlangs->transnoentities("Project"),'','C');
		}


  		/*
  		$pdf->line($this->posxdat-1, $tab_top, $this->posxdat-1, $tab_top + $tab_height);
  		if (empty($hidetop))
  		{	
  			$pdf->SetXY($this->posxdat-1, $tab_top+1);
  			$pdf->MultiCell($this->posxcat-$this->posxdat-1,2, $outputlangs->transnoentities("Date"),'','C');
  		}
		*/
  		
  		if (empty($hidetop))
  		{	
  			$pdf->SetXY($this->posxtot-1, $tab_top+1);
  			$pdf->MultiCell($this->posxdoc-$this->posxtot-1,2, $outputlangs->transnoentities("Amount"),'','L');
  		}
  		
  		$nY=$tab_top+2;
  		

  		$nTotaldeplacement=0;
  		foreach ($aResumen as $fk_projet => $value) 
  		{
  			if ($fk_projet>0)
  			{
  				$projet->fetch($fk_projet);
  				$refproj = $projet->ref;
  				$title = $projet->title;
  			}
  			else
  			{
  				$refproj = '';
  				$title = $outputlangs->transnoentities('Withoutproject');
  			}
				//refproject
  			$nY+=4;
  			$pdf->SetXY($this->posxref-1, $nY);
  			$pdf->MultiCell($this->posxdat-$this->posxref-1,2, dol_trunc($refproj,9),'','L');
  				//title
  			$pdf->SetXY($this->posxdat-1, $nY);
  			$pdf->MultiCell($this->posxtot-$this->posxdat-1,2,dol_trunc($title,55),'','L');
  				//amount
  			$pdf->SetXY($this->posxtot-1, $nY);
  			$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxtot,2,price($value),'','R');
  			$nTotaldeplacement+=$value;
  			if ($nY >= $this->page_hauteur-30)
  			{
  				$pdf->line($this->posxtot-1, $tab_top, $this->posxtot-1, $nY);
  				$pdf->line($this->posxref-1, $tab_top, $this->posxref-1, $nY);
  				$pdf->line($this->page_largeur-$this->marge_droite, $tab_top, $this->page_largeur-$this->marge_droite, $nY);
  					// linea horizontal  				
  				$pdf->line($this->marge_gauche, $nY, $this->page_largeur-$this->marge_droite, $nY);	
  				$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager,$objproj);
  				$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfooter, 0, $outputlangs, 0, 1);
  					//nueva hoja
  				$pdf->AddPage();  
  				$nY = $tab_top + 2;
  					//nueva linea
  				$pdf->line($this->marge_gauche, $nY, $this->page_largeur-$this->marge_droite, $nY);	
  			}


  		}

  		//total rendicion
  		$nY+=4;
  		$pdf->SetTextColor(0,0,60);
		//$pdf->SetFillColor(224,224,224);
  		$pdf->SetFillColor(100, 149, 273);
  		$pdf->SetXY($this->posxref, $nY);
  		$pdf->MultiCell($this->posxtot-2-$this->posxref, 2,"Total", 0, 'L',1);
		// Total rendicion
  		$pdf->SetXY($this->posxtot-1, $nY);
  		$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxtot, 2,price($nTotaldeplacement), '', 'R',1);


  		$pdf->SetFont('','', $default_font_size - 2);
  		$pdf->MultiCell(0, 3, '');		
		// Set interline to 3
  		$pdf->SetTextColor(0,0,0);

  		$nY+=5;
  		// lineas verticales 
  		$pdf->line($this->posxref-1, $tab_top, $this->posxref-1, $nY);
  		$pdf->line($this->posxdat-1, $tab_top, $this->posxdat-1, $nY);
  		$pdf->line($this->posxtot-1, $tab_top, $this->posxtot-1, $nY);
  		
  		$pdf->line($this->page_largeur-$this->marge_droite, $tab_top, $this->page_largeur-$this->marge_droite, $nY);
  		// linea horizontal
  		$pdf->line($this->marge_gauche, $nY, $this->page_largeur-$this->marge_droite, $nY);	

  		$nY+=1;
  		$pdf->SetXY($this->posxref-1, $nY);
  		$pdf->MultiCell($this->posxcat-$this->posxref-1,2, $outputlangs->transnoentities("TOTAL TRANSFERIDO"),'','L');
  		$pdf->SetXY($this->posxtot-1, $nY);
  		$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxtot,2, price($nTotaldep),'','R');
  		$nY+=3;
  		$pdf->SetXY($this->posxref-1, $nY);
  		$pdf->MultiCell($this->posxcat-$this->posxref-1,2, $outputlangs->transnoentities("TOTAL RENDIDO"),'','L');
  		$pdf->SetXY($this->posxtot-1, $nY);
  		$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxtot,2, price($nTotaldeplacement),'','R');
  		$nSaldo=0;
  		$nSaldo=$nTotaldep-$nTotaldeplacement;
  		$nY+=3;
  		$pdf->SetXY($this->posxref-1, $nY);
  		$pdf->MultiCell($this->posxcat-$this->posxref-1,2, $outputlangs->transnoentities("TOTAL SALDO"),'','L');
  		$pdf->SetXY($this->posxtot-1, $nY);
  		$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxtot,2, price($nSaldo),'','R');
  	}
  } 
  ?>
