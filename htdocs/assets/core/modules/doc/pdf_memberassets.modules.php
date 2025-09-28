<?php
 /* Copyright (C) 2017	L.Miguel Mendoza	<l.mendoza.liet@gmail.com>
  *	Copyright (C) 30-10-2017 Yemer Colque<locoto1258@gmail.com>
  * Modelo de Reporte horizontal
  */

require_once(DOL_DOCUMENT_ROOT."/assets/core/modules/assets/modules_assets.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf_fractal.lib.php';

/*require_once DOL_DOCUMENT_ROOT.'/poa/class/poareformulatedext.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/class/poareformulateddetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/class/poaobjetiveext.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/cpartidaext.class.php';*/

class pdf_memberassets extends ModelePDFAssets
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

	var $type_page=0;

	function __construct($db)
	{
		global $conf,$langs,$mysoc;

		$langs->load("main");
		$langs->load("bills");
		$titulo = "";
		$this->db = $db;
		$this->name = "memberassets";
		$this->description = $langs->trans('PDFMemberassets');

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

			//$this->page_hauteur = $formatarray['width'];
			$this->page_hauteur = 216;
			//$this->page_hauteur = (int)($formatarray['height']/2);
			//$this->page_largeur = ($formatarray['height']);
			$this->page_largeur = 356;
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
			//Posiciones para mostrar los textos
			$this->posxini = $this->marge_gauche;
			$this->posxnro = 24;
			$this->posxcod = 53;
			$this->posxcodext = 78;
			$this->posxeti = 180;
			$this->posxfead = 200;
			$this->posxcos  = 225;
			//$this->posxinmu  = 191;
			//$this->posxloca  = 218;
			//$this->posxfeas  = 225;
			$this->posxres = 271;
			//$this->posxcon = 281;
			//valor actualizado
			$this->posxcvaact = 296;
			// depreciacion acumulada
			$this->posxdeacum = 321;
			//$this->posxsal = 331;
			//balance
			$this->posxfin = $this->marge_droite+2;



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

		$aExtras = unserialize($_SESSION['aExtras']);
		$aReportdetasset = unserialize($_SESSION['aReportassetdet']);

		if ($conf->assets->dir_output)
		{
			//$object->fetch_thirdparty();

			// Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->assets->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
				$period_year = $_SESSION['period_year'];
                $dir = $aExtras["filedir"];
                //echo "</br>direccion : ".$dir;
                $file = $dir."/".$aExtras["filename"].".pdf";
                //echo "</br>direccion file reporte : ".$file;
                //$dir = $conf->assets->dir_output . "/licvac/".$period_year.'/report';
				//$file = $dir . "/vacacion.pdf";
			}
			//echo $file;
			if (! file_exists($dir))
			{
				if (dol_mkdir($dir) < 0)
				{
					$this->error=$langs->transnoentities("ErrorCanNotCreateDir",$dir);
					return 0;
				}
			}

			//Rescatamos la sesiones enviadas

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
				$pdf->SetSubject($outputlangs->transnoentities("ASSETSASSETS"));
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
				$tab_top = 60;
				$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?60:10);//38
				$tab_height = 44;
				//130
				$tab_height_newpage = 150;
				//150


				$iniY = $tab_top +4;//4
				$curY = $tab_top +4;//4
				$nexY = $tab_top +7;//4
				// Loop on each lines
				$sumaTotal = 0;
				//$pdf->SetFillColor(220, 255, 220);
				$pdf->SetFillColor(100, 149, 273);

				$j = 0;
				$nro = 1;
				foreach ( (array)$aReportdetasset as $j => $line)
				{
					$curY = $nexY;
					$pdf->SetFont('','', $default_font_size-2);
						// Into loop to work with multipage
						$pdf->SetTextColor(0,0,0);
							$pdf->setTopMargin($tab_top_newpage);
						$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);
						// The only function to edit the bottom margin of current page to set it.
						$pageposbefore=$pdf->getPage();
							// date
						//$curX = $this->posxdate-1;
						$showpricebeforepagebreak=1;

						if(empty($line['Codigo'])){
							$pdf->SetFont('','B', $default_font_size-2);
							$pdf->SetXY($this->posxcodext, $curY);
							$pdf->MultiCell($this->posxeti-$this->posxcodext-1, 1, $line['Etiqueta'], 0, 'L',0);
						}else{

							$pdf->SetXY($this->posxini, $curY);
							$pdf->MultiCell($this->posxnro-$this->posxini-1, 1, $nro, 0, 'C',0);

							$pdf->SetXY($this->posxnro, $curY);
							$pdf->MultiCell($this->posxcod-$this->posxnro-1, 1, $line['Codigo'], 0, 'L',0);

							$pdf->SetXY($this->posxcod, $curY);
							$pdf->MultiCell($this->posxcodext-$this->posxcod-1, 1, $line['Codigoext'], 0, 'L',0);

							$pdf->SetXY($this->posxcodext, $curY);
							$pdf->MultiCell($this->posxeti-$this->posxcodext-1, 1, dol_trunc($line['Etiqueta'],48), 0, 'L',0);
							//$nexYaux = $pdf->GetY();

							$pdf->SetXY($this->posxeti, $curY);
							$pdf->MultiCell($this->posxfead-$this->posxeti-1, 1, dol_print_date($line['FechaAdquisicion'],"day"), 0, 'C',0);

							$pdf->SetXY($this->posxfead, $curY);
							$pdf->MultiCell($this->posxcos-$this->posxfead-1, 1, price(price2num($line['costo'],'MT')), 0, 'R',0);

							/*
							$pdf->SetXY($this->posxcos, $curY);
							$pdf->MultiCell($this->posxinmu-$this->posxcos-1, 1,$line['Inmueble'], 0, 'L',0);


							$pdf->SetXY($this->posxinmu, $curY);
							$pdf->MultiCell($this->posxloca-$this->posxinmu-1, 1, $line['location'], 0, 'L',0);

							$pdf->SetXY($this->posxloca, $curY);
							$pdf->MultiCell($this->posxfeas-$this->posxloca-1, 1, dol_print_date($line['FechaAsignacion'],"day"), 0, 'C',0);
							*/

							$pdf->SetXY($this->posxcos, $curY);
							$pdf->MultiCell($this->posxres-$this->posxcos-1, 1, dol_trunc($line['Responsable'],22), 0, 'L',0);

							//$pdf->SetXY($this->posxres, $curY);
							//$pdf->MultiCell($this->posxcon-$this->posxres-1, 1, $line['Condicion'], 0, 'C',0);

							$pdf->SetXY($this->posxres, $curY);
							$pdf->MultiCell($this->posxcvaact-$this->posxres-1, 1, price(price2num($line['Valoract'],'MT')), 0, 'R',0);

							$pdf->SetXY($this->posxcvaact, $curY);
							$pdf->MultiCell($this->posxdeacum-$this->posxcvaact-1, 1, price(price2num($line['Depreacum'],'MT')), 0, 'R',0);

							//$pdf->SetXY($this->posxdeacum, $curY);
							//$pdf->MultiCell($this->posxsal-$this->posxdeacum-1, 1, price(price2num($line['Balance'],'MT')), 0, 'R',0);


							$pdf->SetXY($this->posxdeacum, $curY);
							$pdf->MultiCell($this->posxfin-$this->posxdeacum-1, 1, price(price2num($line['Balance'],'MT')), 0, 'R',0);
							//$nexYend = $pdf->GetY();
							$nro++;
						}

						//Aqui termina de escribir y hacemos el salto de linea
						$nexY = $pdf->GetY();
						if($nexYaux >= $nexY){
							$nexY = $nexYaux;
						}/*else{
							$nexY = $nexYend;
						}*/


						//$nexY = $max;
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
							$nexY = $tab_top+6;//7 -2
						}
				}
				//fin foreach
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

				// Pied de page
				$this->_pagefoot($pdf,$object,$outputlangs);

				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				$pdf->Close();
				//echo "</br> File output : ".$file;
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

		$aExtras = unserialize($_SESSION['aExtras']);

		// Amount in (at tab_top - 1)
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('','', $default_font_size-1);

		$pdf->SetDrawColor(128,128,128);
		$pdf->SetFont('','', $default_font_size-1);
		//para imprimir una linea restamos el tabtop
		$tab_orig = $tab_top;
		$tab_top -= 6;
		// Output Rect
		//$this->type_page=0;

		/*Aca el Titulo del Reporte con*/
		/*$pdf->SetFillColor(100, 149, 273);
		$pdf->SetXY($this->posxnom-1, $tab_top -4);
		$pdf->MultiCell($this->posxffin-$this->posxnom-1,2, $outputlangs->transnoentities("SOLICITADO"),'','C',1);
		//$pdf->MultiCell($col2x-$col1x, $tab2_hl, $outputlangs->transnoentities("Total"), 0, 'L', 0);
		$pdf->SetXY($this->posxffin, $tab_top -4);
		$pdf->MultiCell($this->posxfrfin-$this->posxffin-1,2, $outputlangs->transnoentities("REGISTRADO"),'','C',1);
		*/
		//$tab_height es la altura del rectangulo

		if($type_page==0)
		{   /* La linea rectangular de pagina horizontal */
			$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);
		}
		else
		{	/* La linea rectangular de pagina vertical */
			$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);
		}

		if (empty($hidetop)){

			$pdf->SetXY($this->posxini-1, $tab_top+3);
			$pdf->MultiCell($this->posxnro-$this->posxini-1,2, $outputlangs->transnoentities("Nro"),'','C');
			$pdf->line($this->posxnro-1, $tab_top, $this->posxnro-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxnro-1, $tab_top+3);
			$pdf->MultiCell($this->posxcod-$this->posxnro-1,2, $outputlangs->transnoentities("Code"),'','C');
			$pdf->line($this->posxcod-1, $tab_top, $this->posxcod-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxcod-1, $tab_top+3);
			$pdf->MultiCell($this->posxcodext-$this->posxcod-1,2, $outputlangs->transnoentities("Fieldref_ext"),'','C');
			$pdf->line($this->posxcodext-1, $tab_top, $this->posxcodext-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxcodext-1, $tab_top+3);
			$pdf->MultiCell($this->posxeti-$this->posxcodext-1,2, $outputlangs->transnoentities("Label"),'','C');
			$pdf->line($this->posxeti-1, $tab_top, $this->posxeti-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxeti-1, $tab_top+2);
			$pdf->MultiCell($this->posxfead-$this->posxeti-1,2, $outputlangs->transnoentities("Dateadq"),'','C');
			$pdf->line($this->posxfead-1, $tab_top, $this->posxfead-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxfead-1, $tab_top+2);
			$pdf->MultiCell($this->posxcos-$this->posxfead-1,2, $outputlangs->transnoentities("Cost"),'','C');
			//$pdf->line($this->posxcos-1, $tab_top, $this->posxcos-1, $tab_top + $tab_height);

			/*
			$pdf->SetXY($this->posxcos-1, $tab_top+2);
			$pdf->MultiCell($this->posxinmu-$this->posxcos-1,2, $outputlangs->transnoentities("Property"),'','C');
			$pdf->line($this->posxinmu-1, $tab_top, $this->posxinmu-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxinmu-1, $tab_top+2);
			$pdf->MultiCell($this->posxloca-$this->posxinmu-1,2, $outputlangs->transnoentities("Location"),'','C');
			$pdf->line($this->posxloca-1, $tab_top, $this->posxloca-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxloca-1, $tab_top+2);
			$pdf->MultiCell($this->posxfeas-$this->posxloca-1,2, $outputlangs->transnoentities("Assigneddate"),'','C');
			*/
			$pdf->line($this->posxcos-1, $tab_top, $this->posxcos-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxcos-1, $tab_top+2);
			$pdf->MultiCell($this->posxres-$this->posxcos-1,2, $outputlangs->transnoentities("Responsable"),'','C');
			$pdf->line($this->posxres-1, $tab_top, $this->posxres-1, $tab_top + $tab_height);

			//$pdf->SetXY($this->posxres-1, $tab_top+2);
			//$pdf->MultiCell($this->posxcon-$this->posxres-1,2, $outputlangs->transnoentities("Condition"),'','C');
			//$pdf->line($this->posxcon-1, $tab_top, $this->posxcon-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxres-1, $tab_top+2);
			$pdf->MultiCell($this->posxcvaact-$this->posxres-1,2, $outputlangs->transnoentities("ValorAct"),'','C');
			$pdf->line($this->posxcvaact-1, $tab_top, $this->posxcvaact-1, $tab_top + $tab_height);

			$pdf->SetXY($this->posxcvaact-1, $tab_top+2);
			$pdf->MultiCell($this->posxdeacum-$this->posxcvaact-1,2, $outputlangs->transnoentities("DepreciationAcum"),'','C');
			$pdf->line($this->posxdeacum-1, $tab_top, $this->posxdeacum-1, $tab_top + $tab_height);

			//$pdf->SetXY($this->posxdeacum-1, $tab_top+2);
			//$pdf->MultiCell($this->posxsal-$this->posxdeacum-1,2, $outputlangs->transnoentities("Balance"),'','C');
			//$pdf->line($this->posxsal-1, $tab_top, $this->posxsal-1, $tab_top + $tab_height);

			// status
			$pdf->SetXY($this->posxdeacum-1, $tab_top+2);
			$pdf->MultiCell($this->posxfin-$this->posxdeacum-1,2, $outputlangs->transnoentities("Balance"),'','C');
			$pdf->line($this->posxci-1, $tab_top, $this->posxci-1, $tab_top + $tab_height);
			$pdf->line($this->marge_gauche, $tab_top+10, $this->page_largeur-$this->marge_droite, $tab_top+10);
		}
	}

	function _pagehead(&$pdf, $object, $showaddress, $outputlangs, $hookmanager,$typeTitulo)
	{
		global $conf,$langs;

		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("companies");
		$outputlangs->load("almacen@almacen");

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		// Array datos
		$aExtras = unserialize($_SESSION['aExtras']);

		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

	    // Show Draft Watermark
		if($object->statut==0 && (! empty($conf->global->ALMACEN_DRAFT_WATERMARK)) )
		{
			pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->ALMACEN_DRAFT_WATERMARK);
		}

		$pdf->SetTextColor(0,0,60);
		$pdf->SetFont('','B', $default_font_size+3);

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

		if(empty($aExtras ['nombre'])){
			$titulo = "TODOS LOS RESPONSABLES";
		}else{
			$titulo = "RESPONSABLE ".$aExtras ['nombre'];
		}


		$pdf->SetFont('','B', $default_font_size+3);
		$pdf->SetXY($this->posxini,$posy);
		$pdf->SetTextColor(0,0,60);
		//$title=$outputlangs->transnoentities("Determinacion de Servicios");
		$title=$outputlangs->transnoentities("Reportassetsresponsible");
		//$pdf->MultiCell(346, 3, $title, '', 'C');
		$pdf->MultiCell($this->page_largeur-$this->marge_haute-$this->posxnum-1, 3, $title, '', 'C');

		$pdf->SetFont('','B', $default_font_size+2);
		$pdf->SetXY($this->posxini,$posy+35);
		$pdf->SetTextColor(0,0,60);
		//$title=$outputlangs->transnoentities("Determinacion de Servicios");
		//$title=$outputlangs->transnoentities($titulo);


		$pdf->SetFont('','B',$default_font_size+1);


			//Formulario
			$posy+=14;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("DESDE FECHA")." : ". dol_print_date($aExtras['date_ini'],'day'), '', 'R');

			//Fecha de elaborarion
			$posy+=7;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("HASTA FECHA")." : " . dol_print_date($aExtras['date_fin'],'day'), '', 'R');




	}

	function _pagefoot(&$pdf,$object,$outputlangs,$hidefreetext=0)
	{
		//return pdf_pagefoot($pdf,$outputlangs,'ALMACEN_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
		return pdf_pagefoot_fractal($pdf,$outputlangs,'ALMACEN_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
	}

	function pdf_pie_page_Ltrescol(&$pdf, $object, $posy, $outputlangs,$marge_gauche, $page_largeur,$marge_droite,$page_hauteur,$type_page=0,$bottomlasttab)
	{
		global $conf,$langs;
		$default_font_size = pdf_getPDFFontSize($outputlangs);

		//$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('times', '',9);

		// Preguntamos donde se pondra el pie de pagina si es vertical o tipo de pagina horizontal
		if($type_page==1){
			//$posy+=250;
			// Aqui ponemos donde se mostrara el pie de pagina vertical
			$posy=60;//100
		}
		else
		{
			// Aqui ponemos donde se mostrara el pie de pagina horizontal
			$posy=$bottomlasttab-35;
			// declaramos las posciones que nos ayudara a crear las diviciones
			$posxini  = $this->marge_gauche;
			$posxuno  = 60;
			$posxdos  = 110;
			$posxtres = 155;
			$posxcua  = 220;
			$posxfin  = 255;
			$this->printRect($pdf,$this->marge_gauche,$posy, $this->page_largeur-$this->marge_gauche-$this->marge_droite, 37, $hidetop, $hidebottom);
		}

		$auxy = $posy;
		$posy = $posy + 6;

		//Lineas de Titulos del pie de firmas
		$pdf->SetFillColor(100, 149, 273);
		$pdf->SetXY($posxini, $auxy-5);
		$pdf->MultiCell(129,3, $outputlangs->transnoentities('RESPONSABLE DE LA INFORMACION'),'','C','1');
		$pdf->SetXY(131, $auxy-5);
		$pdf->MultiCell(138,3, $outputlangs->transnoentities('RESPONSABLE DE LA INFORMACION'),'','C','1');


		//Lineas horizontales
		$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
		$posy += 8;
		$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
		$posy += 8;
		$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);
		$posy += 5;
		$pdf->line($marge_gauche, $posy, $page_largeur-$marge_droite, $posy);

		//Lineas Verticales
		$pdf->line($posxuno, $auxy, $posxuno, $auxy+22);
		$pdf->line($posxdos, $auxy+22, $posxdos, $auxy+37);
		$pdf->line($posxtres, $auxy, $posxtres, $auxy+22);
		$pdf->line($posxcua, $auxy, $posxcua, $auxy+36);

		//Textos
		$pdf->SetXY($posxuno, $auxy+1);
		$pdf->MultiCell(95,3, $outputlangs->transnoentities('GRADO NOMBRE Y APELLIDOS'),'','C','0');

		$pdf->SetXY($posxtres, $auxy+1);
		$pdf->MultiCell(55,3, $outputlangs->transnoentities('CARGO'),'','C','0');

		$pdf->SetXY($posxcua, $auxy+1);
		$pdf->MultiCell(45,3, $outputlangs->transnoentities('FIRMAS'),'','C','0');

		$pdf->SetXY($posxini, $auxy+8);
		$pdf->MultiCell(60,3, $outputlangs->transnoentities('ENCARGADO DE ELABORACION'),'','L','0');

		$pdf->SetXY($posxini, $auxy+16);
		$pdf->MultiCell(60,3, $outputlangs->transnoentities('JEFE DE AREA FUNCIONAL'),'','L','0');

		$pdf->SetXY($posxini, $auxy+22);
		$pdf->MultiCell(110,3, $outputlangs->transnoentities('CERTIFICADO DEL POA'),'','C','0');

		$pdf->SetXY($posxdos, $auxy+22);
		$pdf->MultiCell(110,3, $outputlangs->transnoentities('CERTIFICACION PRESUPUESTO'),'','C','0');

		$pdf->SetXY($posxcua, $auxy+22);
		$pdf->MultiCell(60,3, $outputlangs->transnoentities('VoBo MAXIMA AUTORIDAD'),'','L','0');
	}
}
?>