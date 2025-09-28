<?php
/* Copyright (C) 2004-2012	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012	Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2008		Raphael Bertrand	<raphael.bertrand@resultic.fr>
 * Copyright (C) 2010-2012	Juanjo Menent		<jmenent@2byte.es>
 * Copyright (C) 2012      	Christophe Battarel <christophe.battarel@altairis.fr>
 * Copyright (C) 2017      Yemer Colque         <locoto1258@gmail.com>
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
class pdf_fractalassetref extends ModelePDFAssets
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
	var $paper_format=1;
	//paper_format 0 es carta
	//paper_format 1 es officio

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
		$this->name = "assetsass";
		$this->description = $langs->trans('PDFassetsassDescription');

		// Dimension page pour format A4
		$this->type = 'pdf';
		$formatarray=pdf_getFormat();
		$this->page_largeur = $formatarray['width'];
		$this->page_hauteur = ($formatarray['height']);

		if(empty($this->type_page)) $this->pagesize = $formatarray['width'];
		else $this->pagesize = $formatarray['height'];;
		if ($this->paper_format == 1)
		{
			$this->page_hauteur = 356;
			$this->page_largeur = 216;
		}
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
		//$this->posxcod=$this->marge_gauche+1;

		if (empty($this->paper_format))
		{
			$this->posxnum=10+1;
		// code
			$this->posxcod=20;
		//etiqueta
			$this->posxlabel=45;
		//fecha de adquisicion
			$this->posxfadq=125;
		// costo
			$this->posxcos=141;
		//inmueble
			$this->posxinm=162;
		//localizacion
			$this->posxloc=182;
		//fecha asignacion
			$this->posxfasig=197;
		//responsable
			$this->posxresp=213;
		//condicion
			$this->posxcond=240;
		// estado
			$this->posxest=258;
		//$this->posxout=157;
		/*
		$this->posxdesc=30;
		$this->posxuni=120;
		$this->posxsaa=132;
		$this->posxinp=152;
		$this->posxout=172;
		$this->posxbal=192;
		$this->posxcco=212;
		$this->posxdif=232;
		$this->posxobs=252;
		*/
		if ($this->page_largeur > 297) // To work with US executive format
		{
			$this->posxsaa-=10;
			$this->posxinp-=10;
			$this->posxout-=10;
			$this->posxbal-=10;
			$this->posxcco-=10;
			$this->posxdif-=10;
			$this->posxobs-=10;
		}
	}
	else
	{
		$this->posxnum=10+1;
		// code
		$this->posxcod=20;
		//etiqueta
		$this->posxlabel=45;
		//fecha de adquisicion
		$this->posxfadq=150;
		// costo
		$this->posxcos=170;
		//inmueble
		$this->posxinm=195;
		//localizacion
		$this->posxloc=225;
		//fecha asignacion
		$this->posxfasig=255;
		//responsable
		$this->posxresp=275;
		//condicion
		$this->posxcond=305;
		// estado
		$this->posxest=326;
		//$this->posxout=157;
		/*
		$this->posxdesc=30;
		$this->posxuni=120;
		$this->posxsaa=132;
		$this->posxinp=152;
		$this->posxout=172;
		$this->posxbal=192;
		$this->posxcco=212;
		$this->posxdif=232;
		$this->posxobs=252;
		*/
		if ($this->page_largeur > 297) // To work with US executive format
		{
			$this->posxsaa-=10;
			$this->posxinp-=10;
			$this->posxout-=10;
			$this->posxbal-=10;
			$this->posxcco-=10;
			$this->posxdif-=10;
			$this->posxobs-=10;
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

	global $user,$langs,$conf,$mysoc,$db,$hookmanager;

	if (! is_object($outputlangs)) $outputlangs=$langs;
		// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
	if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output='ISO-8859-1';

	$level = GETPOST('level');
	$aReportdetasset = unserialize($_SESSION['aReportassetdet']);
	$date_ini = unserialize($_SESSION['date_inidet']);
	$date_fin = unserialize($_SESSION['date_findet']);
	$level = unserialize($_SESSION['levelass']);


	$filter = '';
		//armamos todos los grupos
		//fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	echo 'ddddddddddddddddddd';exit;
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
			$dir = $conf->assets->dir_output.'/ref';
			echo 	$file = $dir . "/" . 'assets' . ".pdf";exit;
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
			$nblignes = count($aReportdetasset);
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

				$pdf->SetFont('','', $default_font_size - 1);
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
				$j = 1;
				$Num=0;
				foreach ((array) $aReportdetasset AS $i => $lines)
				{

					$curY = $nexY;
					$pdf->SetFont('','', $default_font_size - 2);
					   // Into loop to work with multipage
					$pdf->SetTextColor(0,0,0);
					$pdf->setTopMargin($tab_top_newpage);
					$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);
					// The only function to edit the bottom margin of current page to set it.
					$aReportdetasset = unserialize($_SESSION['aReportassetdet']);
					//$fk_group = GETPOST('fk_group');
					$Codigo = $lines['Codigo'];
					$Etiqueta = $lines['Etiqueta'];
					$FechaAdquisicion = $lines['FechaAdquisicion'];
					$costo = $lines['costo'];
					$Inmueble = $lines['Inmueble'];
					$location = $lines['location'];
					$FechaAsignacion = $lines['FechaAsignacion'];
					$Responsable = $lines['Responsable'];
					$Condicion = $lines['Condicion'];
					$Estado = $lines['Estado'];
					$type = $lines['type'];
					$pageposbefore=$pdf->getPage();
					//
					$curX = $this->posxcod-1;
					$showpricebeforepagebreak=1;
					//Numero
					if($type=='G')
					{
						// color de grupo
						$pdf->SetFont('times', 'B',9);
						$pdf->SetXY($posx,$posy);
						$pdf->SetTextColor(0,0,60);

						//grupo
						$Etiqueta = $lines['Etiqueta'];
						$pdf->SetXY($this->posxlabel, $curY);
						$pdf->MultiCell($this->posxda-$this->posxlabel-1, 5, $Etiqueta, 0, 'L',0);
					}
					if($type=='D')
					{
						$Num++;
						$pdf->SetXY($this->posxnum, $curY);
						$pdf->MultiCell($this->posxcod-$this->posxnum-1, 5, $Num, 0, 'L',0);

						//code
						$Codigo = $lines['Codigo'];
						$pdf->SetXY($this->posxcod, $curY);
						$pdf->MultiCell($this->posxlabel-$this->posxcod-1, 5, $Codigo, 0, 'L',0);

						//etiqueta
						$Etiqueta = $lines['Etiqueta'];
						$pdf->SetXY($this->posxlabel, $curY);
						$pdf->MultiCell($this->posxda-$this->posxlabel-1, 5, dol_trunc($Etiqueta,59), 0, 'L',0);

						//fecha de adquisicion
						$FechaAdquisicion = $lines['FechaAdquisicion'];
						$pdf->SetXY($this->posxfadq, $curY);
						$pdf->MultiCell($this->posxcos-$this->posxfadq-1, 5, dol_print_date($FechaAdquisicion,'day'), 0, 'C',0);
						//costo
						$costo = $lines['costo'];
						$pdf->SetXY($this->posxcos, $curY);
						$pdf->MultiCell($this->posxinm-$this->posxcos-1, 5, price(price2num($costo)), 0, 'R',0);
						//inmueble
						$Inmueble = $lines['Inmueble'];
						$pdf->SetXY($this->posxinm, $curY);
						$pdf->MultiCell($this->posxloc-$this->posxinm-1, 5, $Inmueble, 0, 'L',0);
						//localizacion
						$location = $lines['location'];
						$pdf->SetXY($this->posxloc, $curY);
						$pdf->MultiCell($this->posxfasig-$this->posxloc-1, 5, $location, 0, 'L',0);
						//fecha asignacion
						$FechaAsignacion = $lines['FechaAsignacion'];
						$pdf->SetXY($this->posxfasig, $curY);
						$pdf->MultiCell($this->posxresp-$this->posxfasig-1, 5, dol_print_date($FechaAsignacion,'day'), 0, 'C',0);
						//responsable
						$Responsable = $lines['Responsable'];
						$pdf->SetXY($this->posxresp, $curY);
						//$pdf->MultiCell($this->posxuni-$this->posxdesc-1, 3, $desc, 0, 'L',0);
						$pdf->MultiCell($this->posxcond-$this->posxresp-1, 5, $Responsable, 0, 'L',0,0,'','',false);

						//condicion
						$Condicion = $lines['Condicion'];
						$pdf->SetXY($this->posxcond, $curY);
						//$pdf->MultiCell($this->posxuni-$this->posxdesc-1, 3, $desc, 0, 'L',0);
						$pdf->MultiCell($this->posxest-$this->posxcond-1, 5, $Condicion, 0, 'L',0,0,'','',false);


						// estado
						$Estado = $lines['Estado'];
						$pdf->SetXY($this->posxest, $curY);
						$pdf->MultiCell($this->page_largeur-$this->marge_haute-$this->posxest-1, 5, $Estado, 0, 'L',0);

					}

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

					$nexY+=1;
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
					if ($nexY+28 > $this->page_largeur)
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

					//function _tableau(&$pdf, $tab_top, $tab_height, $nexY, $outputlangs, $hidetop=0, $hidebottom=0)

				}
				$object->total_ttc = $total;
				//$this->_tableau_total($pdf, $object, $outputlangs, $bottomlasttab);

				//pdf_pie_page_mod2($pdf, $object, $posy, $outputlangs,$this->marge_gauche, $this->page_largeur,$this->marge_droite,$this->page_hauteur,$this->type_page);
				// Pied de page
				//pdf_pie_page_modL4($pdf, $object,$posy, $outputlangs,$this->marge_gauche, $this->page_largeur,$this->marge_droite,$this->page_hauteur,$this->type_page,$bottomlasttab);

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

	function _tableau(&$pdf, $tab_top, $tab_height, $nexY, $outputlangs, $hidetop=0, $hidebottom=0)
	{

		global $conf;
		// Force to disable hidetop and hidebottom
		$hidebottom=0;
		if ($hidetop) $hidetop=-1;

		$default_font_size = pdf_getPDFFontSize($outputlangs);



		// Amount in (at tab_top - 1)
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('','', $default_font_size - 1);

		$pdf->SetDrawColor(128,128,128);
		$pdf->SetFont('','', $default_font_size - 1);
		$tab_orig = $tab_top;
		$tab_top -= 6;

		if($type_page==0)
		{
			$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_hauteur - $this->marge_gauche - $this->marge_droite, $tab_height, $hidetop, $hidebottom);
		}
		else
		{
			$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);
		}

		if (empty($hidetop))
		{
			$pdf->line($this->marge_gauche, $tab_top+10, $this->page_hauteur-$this->marge_droite, $tab_top+10);		// line prend une position y en 2eme param et 4eme param

			$pdf->SetXY($this->posxnum-1, $tab_top+1);
			$pdf->MultiCell($this->posxcod-$this->posxnum-1,2, $outputlangs->transnoentities("Nro"),'','L');
		}

		$pdf->line($this->posxnum-1, $tab_top, $this->posxnum-1, $tab_top + $tab_height);

		if (empty($hidetop))
		{
			if($level==0)
			{
				$pdf->SetXY($this->posxcod-1, $tab_top+1);
				$pdf->MultiCell($this->posxlabel-$this->posxcod-1,2, $outputlangs->transnoentities("Code"),'','L');

			}
			else
			{
				$pdf->SetXY($this->posxcod-1, $tab_top+1);
				$pdf->MultiCell($this->posxlabel-$this->posxcod-1,2, $outputlangs->transnoentities("Fieldref_ext"),'','L');

			}

		}

		$pdf->line($this->posxcod-1, $tab_top, $this->posxcod-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxlabel-1, $tab_top+1);
			$pdf->MultiCell($this->posxfadq-$this->posxlabel-1,2, $outputlangs->transnoentities("Label"),'','C');
		}

		$pdf->line($this->posxlabel-1, $tab_top, $this->posxlabel-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxfadq-1, $tab_top+1);
			$pdf->MultiCell($this->posxcos-$this->posxfadq-1,2, $outputlangs->transnoentities("Fielddate_adq"),'','C');
		}
		$pdf->line($this->posxfadq-1, $tab_top, $this->posxfadq-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxcos-1, $tab_top+1);
			$pdf->MultiCell($this->posxinm-$this->posxcos-1,2, $outputlangs->transnoentities("Fieldcoste"),'','C');
		}

		$pdf->line($this->posxcos-1, $tab_top, $this->posxcos-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxinm-1, $tab_top+1);
			$pdf->MultiCell($this->posxloc-$this->posxinm-1,2, $outputlangs->transnoentities("Property"),'','C');
		}

		$pdf->line($this->posxinm-1, $tab_top, $this->posxinm-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxloc-1, $tab_top+1);
			$pdf->MultiCell($this->posxfasig-$this->posxloc-1,2, $outputlangs->transnoentities("Location"),'','C');
		}
		$pdf->line($this->posxloc-1, $tab_top, $this->posxloc-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxfasig-1, $tab_top+1);
			$pdf->MultiCell($this->posxresp-$this->posxfasig-1,2, $outputlangs->transnoentities("Assignmentdate"),'','C');
		}
		$pdf->line($this->posxfasig-1, $tab_top, $this->posxfasig-1, $tab_top + $tab_height);

		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxresp-1, $tab_top+1);
			$pdf->MultiCell($this->posxcond-$this->posxresp-1,2, $outputlangs->transnoentities("Responsible"),'','C');
		}
		$pdf->line($this->posxresp-1, $tab_top, $this->posxresp-1, $tab_top + $tab_height);

		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxcond-1, $tab_top+1);
			$pdf->MultiCell($this->posxest-$this->posxcond-1,2, $outputlangs->transnoentities("Been"),'','C');
		}

		$pdf->line($this->posxcond-1, $tab_top, $this->posxcond-1, $tab_top + $tab_height);

		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxest-1, $tab_top+1);
			$pdf->MultiCell($this->page_largeur-$this->marge_haute-$this->posxest-1,2, $outputlangs->transnoentities("Status"),'','C');
		}
		$pdf->line($this->posxest-1, $tab_top, $this->posxest-1, $tab_top + $tab_height);
	}

	function _pagehead(&$pdf, $object, $showaddress, $outputlangs, $hookmanager)
	{
		global $conf,$langs;
		$outputlangs->load("almacen");
		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("companies");

		$aReportdetasset = unserialize($_SESSION['aReportassetdet']);
		$date_ini = unserialize($_SESSION['date_inidet']);
		$date_fin = unserialize($_SESSION['date_findet']);
		$fk_group = GETPOST('fk_group');
		$cGroup= unserialize($_SESSION['cGroupdet']);

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

		//$fk_departament=unserialize($_SESSION['fk_departament']);
		// Show Draft Watermark
		if($object->statut==0 && (! empty($conf->global->ASETS_DRAFT_WATERMARK)) )
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
		$pdf->SetXY($this->posxnum+1,$posy);
		$pdf->SetTextColor(0,0,60);
		$title=$outputlangs->transnoentities("Reportgroupasset");
		$pdf->MultiCell($this->page_hauteur-$this->marge_haute-$this->posxnum-1, 3, $title, '', 'C');

		$pdf->SetFont('','B',$default_font_size);


		//date
		$posy+=6;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Date and time of generation")." : " . dol_print_date(dol_now(),"dayhour",false,$outputlangs), '', 'R');


		//date inicial
		$posy+=4;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Dateini")." : " . dol_print_date($date_ini,"day",false,$outputlangs), '', 'R');

		// date final
		$posy+=4;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Datefin")." : " . dol_print_date($date_fin,"day",false,$outputlangs), '', 'R');

		//$objDepartament = new Pdepartament($this->db);
		//$objDepartament->fetch($fk_departament);
		// Grupos
		if($level==0)
		{
			$posy+=4;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Asset")." : " . "Ref.", '', 'R');
		}
		else
		{
			$posy+=4;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Asset")." : " ."Ref. Ext.", '', 'R');

		}

		// Show list of linked objects
		$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size, $hookmanager);
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

	function _tableau_total(&$pdf, $object, $outputlangs , $bottomlasttab)
	{
		global $conf,$mysoc;

		$sign=1;

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		$tab2_top = 175;//$posy+100
		$tab2_hl = 4;
		$pdf->SetFont('','', $default_font_size-2);

		// Tableau total
		$col1x = 190;
		$col2x = 260;
		$largcol2 = ($this->page_largeur - $this->marge_droite - $col2x);

		$useborder=0;
		$index = 0;
		// Total ttc
		$pdf->SetTextColor(0,0,60);
		//$pdf->SetFillColor(224,224,224);
		$pdf->SetFillColor(100, 149, 273);

		$pdf->SetXY($col1x, $tab2_top +15);
		$pdf->MultiCell($col2x-$col1x, $tab2_hl, $outputlangs->transnoentities("Total"), 0, 'L', 1);

		$total_ttc =$object->total_ttc;
		$pdf->SetXY($col2x-32, $tab2_top +15);
		$pdf->MultiCell($largcol2, $tab2_hl, price($total_ttc), 0, 'R', 1);
		$pdf->SetTextColor(0,0,0);
		$index++;
		return ($tab2_top + ($tab2_hl * $index));
	}

	function _pagefoot(&$pdf,$object,$outputlangs,$hidefreetext=0)
	{
		return pdf_pagefoot_fractal($pdf,$outputlangs,'ASSETS_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
	}






}

?>
