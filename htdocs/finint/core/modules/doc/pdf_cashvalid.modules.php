<?php
/* Copyright (C) 2004-2012	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012	Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2008		Raphael Bertrand	<raphael.bertrand@resultic.fr>
 * Copyright (C) 2010-2012	Juanjo Menent		<jmenent@2byte.es>
 * Copyright (C) 2012      	Christophe Battarel <christophe.battarel@altairis.fr>
 * Copyright (C) 10-01-2018  Yemer Colque laura <locoto1258@gmail.com>
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

require_once DOL_DOCUMENT_ROOT.'/finint/core/modules/finint/modules_finint.php';
require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashext.class.php';
require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashdeplacementext.class.php';
require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashdet.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once(DOL_DOCUMENT_ROOT."/finint/class/accountext.class.php");

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';


/**
 *	Class to manage PDF invoice template Crabe
 */
class pdf_cashvalid extends ModelePDFFinint
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
		$this->name = "Cashvalid";
		$this->description = $langs->trans('PDFCashvalidDescription');

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
		$this->posxtras=$this->marge_gauche+1;
		$this->posxdate=37;
		$this->posxfrom=55;
		$this->posxto=80;
		$this->posxtype=100;
		$this->posxdoc=120;
		$this->posxdes=140;
		$this->posxbal=175;
		$this->posxstatus=195;



		if ($this->page_largeur < 210)
	 	// To work with US executive format
		{
			$this->posxtype-=20;
			$this->posxto-=20;
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
		$outputlangs->load("finint@finint");

		$objuser = new User($this->db);
		$objdeplac = new Requestcashdeplacementext($this->db);
		$objdeplac->getlisttransfer(0,$object->id);

		if ($conf->finint->dir_output)
		{

			$aReport= unserialize($_SESSION['aReportcardet']);
			$nblignes = count($aReport);

			$object->fetch_thirdparty();
			$objdet = new Requestcashdet($db);
			$objproj = new Project($db);
			$objproj->fetch($object->fk_projet);
			$objaccount = new Account($db);

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
				//$nblignes = count($object->linesdet);
				$pdf=pdf_getInstance($this->format);
				$default_font_size = pdf_getPDFFontSize($outputlangs);
				// Must be after pdf_getInstance
				//distancia del pie espachio libre para texto
				//revisamos que altura debe tener
				$minheight = 80;
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
				$pdf->SetSubject($outputlangs->transnoentities("Requestcash"));
				$pdf->SetCreator("Dolibarr ".DOL_VERSION);
				$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
				$pdf->SetKeyWords($outputlangs->convToOutputCharset($object->ref)." ".$outputlangs->transnoentities("Order"));
				if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);

				$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);
		 		// Left, Top, Right

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

				$this->_pagehead($pdf, $object, 1, $outputlangs, $hookmanager,$objproj);

				$pageposafter=$pdf->getPage();


				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->MultiCell(0, 3, '');
				// Set interline to 3
				$pdf->SetTextColor(0,0,0);

				//linea para el cuerpo
				$tab_top = 47;
				$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?47:10);
				$tab_height = 80;
				//130
				$tab_height_newpage = 150;
				//150


				$iniY = $tab_top + 7;
				$curY = $tab_top + 7;
				$nexY = $tab_top + 7;
				// Loop on each lines
				//for ($i = 0; $i < $nblignes; $i++)
				//	{
				$nSumabal=0;
				$nSumabalneg=0;
				$nAux=0;





				foreach ($objdeplac->lines AS $j => $line)
				//foreach ($aReport as $key => $linesdet)
				{
					$fk_user = ($line->fk_user_from?$line->fk_user_from:$object->fk_user_authorized);
					$objuser->fetch($fk_user);
					$usertrans = '';
					$caccountfrom = '';
					$caccountto = '';
					if ($objuser->id == $fk_user)
					{
						$usertrans = $objuser->lastname.' '.$objuser->firstname;
					}
					$fkacc = $line->fk_account_from;
					$objaccount->fetch($fkacc);
					if ($objaccount->id == $fkacc) $caccountfrom= $objaccount->label;

					$fkacc = $line->fk_account_dest;
					$objaccount->fetch($fkacc);
					if ($objaccount->id == $fkacc) $caccountto= $objaccount->label;
					$cType = $langs->trans('PaymentType'.STRTOUPPER(($line->fk_type?$line->fk_type:'LIQ')));
					$cDoc = ($line->num_chq?$line->num_chq:$line->nro_chq);
					$i=0;
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

					// transfiere
					//$qty = price2num($object->linesdet[$i]->quant,'MU');

					$pdf->SetXY($this->posxtras, $curY);
					$pdf->MultiCell($this->posxdate-$this->posxtras, 3, dol_trunc($usertrans,17), 0, 'L',0);
					$i++;

					// Date
					$pdf->SetXY($this->posxdate, $curY);
					$pdf->MultiCell($this->posxfrom-$this->posxdate, 3, dol_print_date($line->dateo,'day'), 0, 'C',0);
					$i++;

					// from
					//$desc = $object->linesdet[$i]->detail;
					$pdf->SetXY($this->posxfrom, $curY);
					$pdf->MultiCell($this->posxto-$this->posxfrom, 3, $caccountfrom, 0, 'L',0);
					$i++;



					// to
					//$pun = price(price2num($object->linesdet[$i]->amount/$qty,'MT'));
					$pdf->SetXY($this->posxto, $curY);
					$pdf->MultiCell($this->posxtype-$this->posxto, 3, $caccountto, 0, 'L',0);
					$i++;


					// Type
					//$pun = price(price2num($object->linesdet[$i]->amount/$qty,'MT'));
					$pdf->SetXY($this->posxtype, $curY);
					$pdf->MultiCell($this->posxdoc-$this->posxtype, 3, $cType, 0, 'L',0);
					$i++;


					// Doc
					$pdf->SetXY($this->posxdoc, $curY);
					$pdf->MultiCell($this->posxdes-$this->posxdoc, 3, $cDoc, 0, 'L',0);
					$i++;


					// Label
					$pdf->SetXY($this->posxdes, $curY);
					$pdf->MultiCell($this->posxbal-$this->posxdes, 3,dol_trunc($line->detail,26), 0, 'L',0);
					$i++;

					// Balanace
					$pdf->SetXY($this->posxbal, $curY);
					$pdf->MultiCell($this->posxstatus-$this->posxbal, 3,price($line->amount), 0, 'R',0);
					$nAux=$linesdet[$i];
					if($line->amount>0)
					{
						$nSumabal+=$line->amount;
					}
					else
					{
						$nSumabalneg+=$line->amount;
					}

					$i++;


					// Status
					$pdf->SetXY($this->posxstatus, $curY);
					$pdf->MultiCell(15, 3,$linesdet[$i], 0, 'C',0);
					if( $nAux>0)
					{
							if($line->status== 4 || $line->status==-1 )
							{
								$nSumabal-=$nAux;
							}

					}
					//$app = price($object->linesdet[$i]->amount);
					//$pdf->SetXY($this->posxtype, $curY);
					//$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxtype, 3, $app, 0, 'R');
					//$sumamount+= $object->linesdet[$i]->amount;
					//$sumamountapp+= $object->linesdet[$i]->amount;

					$object->total_ttc = $nSumabal;

					$nexY = $pdf->GetY();
					$pageposafter=$pdf->getPage();

					$pdf->setPage($pageposbefore);
					$pdf->setTopMargin($this->marge_haute);
					$pdf->setPageOrientation('', 1, 0);
					// The only function to edit the bottom margin of current page to set it.

					// We suppose that a too long description is moved completely on next page
					//if ($pageposafter > $pageposbefore && empty($showpricebeforepagebreak)) {
					//$pdf->setPage($pageposafter); $curY = $tab_top_newpage;
					//}
					//echo '<hr>bbb pagepa  '.$pageposafter.' > pagepb '.$pageposbefore;
					if ($pageposafter > $pageposbefore) {
						$pdf->setPage($pageposafter);
						$curY = $tab_top_newpage;
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

					$nexY+=2;
		 			// Passe espace entre les lignes

					// Detect if some page were added automatically and output _tableau for past pages
					//echo '<hr>rev '.$pagenb.' < '.$pageposafter;
					while ($pagenb < $pageposafter)
					{
						//echo '<br>procesapag '.$pagenb;
						$pdf->setPage($pagenb);
						if ($pagenb == 1)
						{
							$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1);
						}
						else
						{
							$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 1, 1);
						}
						$this->_pagefoot($pdf,$object,$outputlangs,0);
						$pagenb++;
						//echo '<br>cambiando chiclo '.$pagenb;
						$pdf->setPage($pagenb);
						$pdf->setPageOrientation('', 1, 0);
						// The only function to edit the bottom margin of current page to set it.
						if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD))
							$this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager,$objproj);
					}
					if (isset($object->lines[$i+1]->pagebreak) && $object->lines[$i+1]->pagebreak)
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
					$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 1, 0);
					$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
				}

				$this->_tableau_total($pdf, $object, $bottomlasttab, $outputlangs,$sumamount,$sumamountapp);
				// Pied de page
				$this->_pagefoot($pdf,$object,$outputlangs);
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
		// Force to disable hidetop and hidebottom
  		$hidebottom=0;
  		if ($hidetop) $hidetop=-1;
  		$default_font_size = pdf_getPDFFontSize($outputlangs);

		// Amount in (at tab_top - 1)
  		$pdf->SetTextColor(0,0,0);
  		$pdf->SetFont('','', $default_font_size - 2);

  		$pdf->SetDrawColor(128,128,128);
  		$pdf->SetFont('','', $default_font_size - 2);

		// Output Rect
  		$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);
		// Rect prend une longueur en 3eme param et 4eme param

  		// transfiere
  		if (empty($hidetop))
  		{
  			$pdf->line($this->marge_gauche, $tab_top+5, $this->page_largeur-$this->marge_droite, $tab_top+5);
			// line prend une position y en 2eme param et 4eme param

  			$pdf->SetXY($this->posxtras-1, $tab_top+1);
  			$pdf->MultiCell($this->posxdate-$this->posxtras,2, $outputlangs->transnoentities("Usertransfers"),'','C');
  		}
  		// fecha
  		$pdf->line($this->posxdate-1, $tab_top, $this->posxdate-1, $tab_top + $tab_height);
  		if (empty($hidetop))
  		{
  			$pdf->SetXY($this->posxdate-1, $tab_top+1);
  			$pdf->MultiCell($this->posxfrom-$this->posxdate,2, $outputlangs->transnoentities("Date"),'','C');
  		}
  		// DE
  		$pdf->line($this->posxfrom-1, $tab_top, $this->posxfrom-1, $tab_top + $tab_height);
  		if (empty($hidetop))
  		{
  			$pdf->SetXY($this->posxfrom-1, $tab_top+1);
  			$pdf->MultiCell($this->posxto-$this->posxfrom,2, $outputlangs->transnoentities("From"),'','C');
  		}
  		// A
  		$pdf->line($this->posxto-1, $tab_top, $this->posxto-1, $tab_top + $tab_height);
  		if (empty($hidetop))
  		{
  			$pdf->SetXY($this->posxto-1, $tab_top+1);
  			$pdf->MultiCell($this->posxtype-$this->posxto,2, $outputlangs->transnoentities("To"),'','C');
  		}
  		// Tipo
  		$pdf->line($this->posxtype, $tab_top, $this->posxtype, $tab_top + $tab_height);
  		if (empty($hidetop))
  		{
  			$pdf->SetXY($this->posxtype-1, $tab_top+1);
  			$pdf->MultiCell($this->posxdoc-$this->posxtype,2, $outputlangs->transnoentities("Type"),'','C');
  		}
  		// Doc
  		$pdf->line($this->posxdoc, $tab_top, $this->posxdoc, $tab_top + $tab_height);
  		if (empty($hidetop))
  		{
  			$pdf->SetXY($this->posxdoc-1, $tab_top+1);
  			$pdf->MultiCell($this->posxdes-$this->posxdoc,2, $outputlangs->transnoentities("Doc"),'','C');
  		}
  		// Descripcion
  		$pdf->line($this->posxdes, $tab_top, $this->posxdes, $tab_top + $tab_height);
  		if (empty($hidetop))
  		{
  			$pdf->SetXY($this->posxdes-1, $tab_top+1);
  			$pdf->MultiCell($this->posxbal-$this->posxdes,2, $outputlangs->transnoentities("Detail"),'','C');
  		}
  		// Importe
  		$pdf->line($this->posxbal, $tab_top, $this->posxbal, $tab_top + $tab_height);
  		if (empty($hidetop))
  		{
  			$pdf->SetXY($this->posxbal-1, $tab_top+1);
  			$pdf->MultiCell($this->posxstatus-$this->posxbal,2, $outputlangs->transnoentities("Amount"),'','C');
  		}
  		// Status
  		$pdf->line($this->posxstatus, $tab_top, $this->posxstatus, $tab_top + $tab_height);
  		if (empty($hidetop))
  		{
  			$pdf->SetXY($this->posxstatus-1, $tab_top+1);
  			$pdf->MultiCell($this->posxbal-$this->posxstatus,2, $outputlangs->transnoentities("Status"),'','C');
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
  		$pdf->SetXY($posx,$posy);
  		$pdf->SetTextColor(0,0,60);
  		$title=$outputlangs->transnoentities("Requestcash");
  		$pdf->MultiCell(100, 3, $title, '', 'L');

  		$pdf->SetFont('','B',$default_font_size);

  		$posy+=5;
  		$pdf->SetXY($posx,$posy);
  		$pdf->SetTextColor(0,0,60);
  		$pdf->MultiCell(100, 4, $outputlangs->transnoentities("Ref")." : " . $outputlangs->convToOutputCharset($object->ref), '', 'R');

  		/*

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
  		*/

		//dateorder
  		$posy+=4;
  		$pdf->SetXY($posx,$posy);
  		$pdf->SetTextColor(0,0,60);
  		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Date")." : " . dol_print_date($object->date_create,"day",false,$outputlangs), '', 'R');
  		//registramos codigo proyecto y nombre proyecto

  		/*
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
  		*/
		//creado por
  		$objuser = new User($this->db);
  		$objuser->fetch($object->fk_user_create);
  		$posy+=4;
  		$pdf->SetXY($posx,$posy);
  		$pdf->SetTextColor(0,0,60);
  		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("The")." : " . $objuser->lastname.' '.$objuser->firstname, '', 'R');

		//aprobado por
		/*
  		$objuser = new User($this->db);
  		if ($objuser->fetch($object->fk_user_approved)>0)
  		{
  			$posy+=4;
  			$pdf->SetXY($posx,$posy);
  			$pdf->SetTextColor(0,0,60);
  			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Via")." : " . $objuser->lastname.' '.$objuser->firstname, '', 'R');
  		}
  		*/
		//autorizado y desembolsado por
		/*
  		$objuser = new User($this->db);
  		if ($objuser->fetch($object->fk_user_authorized)>0)
  		{
  			$posy+=4;
  			$pdf->SetXY($posx,$posy);
  			$pdf->SetTextColor(0,0,60);
  			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("A")." : " . $objuser->lastname.' '.$objuser->firstname, '', 'R');
  		}

  		$posy+=1;
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

		$this->posxetiqueta=50;
		$this->posxamount=182;
		$this->posxamountpre=230;
		$this->posxamountcomp=270;
		$this->posxamountdev=310;


		$default_font_size = pdf_getPDFFontSize($outputlangs);

		$tab2_top = 170;//$posy+100
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
		$pdf->SetXY($this->posxdes, $tab2_top +15);
		$pdf->MultiCell($this->posxbal-$this->posxdes+32, 5,"Total", 0, 'L',1);

		// Total presupuesto
		$pdf->SetXY($this->posxbal, $tab2_top +15);
		$pdf->MultiCell($this->posxstatus-$this->posxbal, 5,price(price2num($total_ttc,'MT')), 0, 'R',1);

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

}

?>
