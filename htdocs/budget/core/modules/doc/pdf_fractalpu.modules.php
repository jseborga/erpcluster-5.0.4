<?php
/* Copyright (C) 2004-2014 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2008      Raphael Bertrand     <raphael.bertrand@resultic.fr>
 * Copyright (C) 2010-2015 Juanjo Menent	    <jmenent@2byte.es>
 * Copyright (C) 2012      Christophe Battarel   <christophe.battarel@altairis.fr>
 * Copyright (C) 2012      Cedric Salvador      <csalvador@gpcsolutions.fr>
 * Copyright (C) 2015      Marcos García        <marcosgdf@gmail.com>
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
 *	\file       htdocs/core/modules/propale/doc/pdf_azur.modules.php
 *	\ingroup    propale
 *	\brief      Fichier de la classe permettant de generer les propales au modele Azur
 */
require_once DOL_DOCUMENT_ROOT.'/core/modules/propale/modules_propale.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/budget/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskaddext.class.php';
require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetgeneral.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/budget/class/cunits.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf_fractal.lib.php';

/**
 *	Class to generate PDF proposal Azur
 */
class pdf_fractalpu extends ModelePDFPropales
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
		$langs->load("budget@budget");

		$this->db = $db;
		$this->name = "fractalpu";
		$this->description = $langs->trans('DocModelFractalPriceUnitDescription');

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
		$this->option_draft_watermark = 1;		   //Support add of a watermark on drafts

		$this->franchise=!$mysoc->tva_assuj;

		// Get source company
		$this->emetteur=$mysoc;
		if (empty($this->emetteur->country_code)) $this->emetteur->country_code=substr($langs->defaultlang,-2);    // By default, if was not defined

		// Define position of columns
		$this->posxnro=$this->marge_gauche+1;
		$this->posxdes=20;
		$this->posxuni=90;
		$this->posxqty=100;
		$this->posxpor=118;
		$this->posxnpri=138;
		$this->posxpri=160;
		$this->posxpar=178;

		if (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_WITHOUT_VAT)) $this->posxtva=$this->posxup;
		$this->posxpicture=$this->posxtva - (empty($conf->global->MAIN_DOCUMENTS_WITH_PICTURE_WIDTH)?20:$conf->global->MAIN_DOCUMENTS_WITH_PICTURE_WIDTH);	// width of images
		if ($this->page_largeur < 210) // To work with US executive format
		{
			$this->posxpicture-=20;
			$this->posxtva-=20;
			$this->posxup-=20;
			$this->posxqty-=20;
			$this->posxunit-=20;
			$this->posxdiscount-=20;
			$this->postotalht-=20;
		}

		$this->tva=array();
		$this->localtax1=array();
		$this->localtax2=array();
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
	 *  @return     int             				1=OK, 0=KO
	 */
	function write_file($object,$outputlangs,$srctemplatepath='',$hidedetails=0,$hidedesc=0,$hideref=0)
	{
		global $user,$langs,$conf,$mysoc,$db,$hookmanager;


		$id = $object->id;
		$idr = $object->idr;
		$objbt = new Budgettaskaddext($this->db);
		$societe = new Societe($this->db);
		$general = new Budgetgeneral($this->db);
		$general->fetch(0,$object->id);


		$objectdet = new Budgettaskext($this->db);
		$objectdetadd 	= new Budgettaskaddext($this->db);

		$objUnit = new Cunits($this->db);



		if (! is_object($outputlangs)) $outputlangs=$langs;
		// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
		if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output='ISO-8859-1';

		$outputlangs->load("main");
		$outputlangs->load("dict");
		$outputlangs->load("companies");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("products");
		$outputlangs->load("budget");
		$outputlangs->load("orders");
		$aLines = $_SESSION['linesrep'];
		$lines = $aLines[$object->id]['lines'];
		$nblignes = count($lines);
		$title 	= $aLines[$object->id]['title'];
		$seltype = $aLines[$object->id]['seltype'];
		$refitem = $aLines[$object->id]['ref'];
		$labeldoc = '';
		if ($seltype =='MA') $labeldoc='_material';
		if ($seltype =='MO') $labeldoc='_manoobra';
		if ($seltype =='MQ') $labeldoc='_maquinaria';
		if ($seltype =='RUB') $labeldoc='_rubros';
		if ($seltype =='PU') $labeldoc='_'.$refitem.'_pu';
		$object->seltype = $seltype;
		$object->refitem = $refitem;
		$object->labelitem = $aLines[$object->id]['labelitem'];
		// Loop on each lines to detect if there is at least one image to show

		//if (count($realpatharray) == 0) $this->posxpicture=$this->posxtva;

		if ($conf->budget->dir_output)
		{
			$object->fetch_thirdparty();

			$deja_regle = 0;

			// Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->budget->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
				$objectref = dol_sanitizeFileName($object->ref);
				$dir = $conf->budget->dir_output . "/" . $objectref;

				$file = $dir . "/" . $objectref.$labeldoc . ".pdf";
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
				// Add pdfgeneration hook
				if (! is_object($hookmanager))
				{
					include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
					$hookmanager=new HookManager($this->db);
				}
				$hookmanager->initHooks(array('pdfgeneration'));
				$parameters=array('file'=>$file,'object'=>$object,'outputlangs'=>$outputlangs);
				global $action;
				$reshook=$hookmanager->executeHooks('beforePDFCreation',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks

				// Create pdf instance
				$pdf=pdf_getInstance($this->format);
				$default_font_size = pdf_getPDFFontSize($outputlangs);	// Must be after pdf_getInstance
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


				// precio unitario



				$res = $object->fetch($id);
				if($res>0)
				{
					$cTitle=$object->title;
					$cRef=$object->ref;
					//$cAmountpres=$$object->budget_amount;
					//vamos a recuperar cada uno de los items cargados
					$filter = " AND t.fk_budget =".$id;
					if ($idr>0) $filter.= " AND t.rowid = ".$idr;
					$res = $objectdet->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);
					if ($res >0)
					{
						$lines = $objectdet->lines;
						foreach ($lines AS $j => $line)
						{

							$resd = $objectdetadd->fetch(0,$line->id);

							if($resd==1 && $objectdetadd->c_grupo==0)
							{
								//procesamos
								$sum = $objectdetadd->procedure_calc($id,$line->id,true);
								$aReport[] = $objectdetadd->aSpread;
							}
						}
					}
				}

				$nrotask = 0;


				foreach ($aReport AS $j => $aDatatask)
				{
					foreach ($aDatatask AS $fk_task => $lines)
					{

						//if ($j == 0)
						$pdf->AddPage();
						//$pageposbefore=$pdf->getPage();

						//if ($j == 0)
						$posy=$this->marge_haute;
						//else $posy=$this->marge_haute+130;
						$posx=$this->page_largeur-$this->marge_droite-100;

						$object->ref = $aPlanillauser['ref'];
						$object->period_year = $aPlanillauser['period_year'];
							//$this->_pagehead($pdf, $object, 1, $outputlangs, $hookmanager,$posx,$posy);


						$pdf->SetFont('','', $default_font_size - 3);
						$pdf->MultiCell(0, 3, '');
							// Set interline to 3
						$pdf->SetTextColor(0,0,0);
							//$tab_top = 60;
						$tab_top = 60;
							//if ($j == 1) $tab_top+=130;
							//if ($j == 1) $tab_top+=130;
						$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?60:10);
										//$tab_height = 130;
						$tab_height = 120;
						$tab_height_newpage = 120;

						$iniY = $tab_top +8;
						$curY = $tab_top+8;
						$nexY = $tab_top +8;


						$objectdet->fetch($fk_task);
						$objectdetadd->fetch(0,$fk_task);



						// unidad
						$resadd = $objUnit->fetch($objectdetadd->fk_unit);
						if($resadd>0)$cUnidad=$objUnit->code;

						$general->fetch($objectdet->fk_budget);
						// unidad
						$object->label_det=$objectdet->label;
						$object->unit_det=$cUnidad;
						$object->cant_det=$objectdetadd->unit_budget;
						$object->base_currency_det=$general->base_currency;

						// cantidad unidad y total budget_task_add

						$nDecimal_quant=0;
						$nDecimal_pu=0;
						$nDecimal_total=0;


						$nDecimal_quant=$general->decimal_quant;
						$nDecimal_pu=$general->decimal_pu;
						$nDecimal_total=$general->decimal_total;




						$this->_pagehead($pdf, $object, 1, $outputlangs,$societe);

						//$pdf->SetFont();
						$pdf->SetFont('','', $default_font_size - 2);
						// imprimimos subtitulos
						//
						$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1, $object->multicurrency_code,$seltype);

						$sw=0;

			
						foreach ($lines AS $k => $aData)
						{
							$newNexY=0;
							foreach ($aData AS $nom => $row)
							{
								if ($lines[$i]->format == 'group') $nrotask++;
								$curY = $nexY;
									//$pdf->SetFont('','', $default_font_size - 4);
									//$pdf->SetTextColor(0,0,0);
								$pdf->SetFillColor(220, 220, 80);


								if($nom == 'datag')
								{
									foreach ($row AS $nReg => $value)
									{

										$pdf->SetTextColor(0,0,0);
										//$pdf->SetFillColor(60,180,190);
										$pdf->SetFillColor(234,236,238);

										if($nReg==1)
										{
											//$pdf->SetXY($this->posxdes, $curY);
											//$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxdes, 3, $value, 0, 'L',1);
											$pdf->SetXY($this->posxnro, $curY);
											$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxnro, 3, $value, 0, 'L',1);
										}
									}

								}
								elseif($nom == 'data')
								{
									foreach ($row AS $nReg => $value)
									{
										if($nReg==1)
										{
											$value= trim($value);
											//$pdf->SetXY($this->posxdes, $curY);
											//$pdf->MultiCell($this->posxuni-$this->posxdes, 3, $value, 0, 'L');
											$pdf->SetXY($this->posxnro, $curY);
											$pdf->MultiCell($this->posxuni-$this->posxnro, 3, $value, 0, 'L');
											$newNexY= $pdf->GetY();
										}
										if($nReg==2)
										{
											$pdf->SetXY($this->posxuni, $curY);
											$pdf->MultiCell($this->posxqty-$this->posxuni, 3, html_entity_decode($outputlangs->transnoentities($value)), 0, 'C');
										}

										if($nReg==3)
										{
											$pdf->SetXY($this->posxqty, $curY);
											$pdf->MultiCell($this->posxpor-$this->posxqty-1, 3, price($value,0,'',0,$nDecimal_quant), 0, 'R');
										}



										if($nReg==4)
										{
											$pdf->SetXY($this->posxpor, $curY);
											$pdf->MultiCell($this->posxnpri-$this->posxpor-1, 3, price($value,0,'',0,$nDecimal_total), 0, 'R');
										}



										if($nReg==5)
										{
											$pdf->SetXY($this->posxnpri, $curY);
											$pdf->MultiCell($this->posxpri-$this->posxnpri-1, 3, price($value,0,'',0,$nDecimal_total), 0, 'R');
										}

										if($nReg==6)
										{
											$pdf->SetXY($this->posxpri, $curY);
											$pdf->MultiCell($this->posxpar-$this->posxpri, 3, price($value,0,'',0,$nDecimal_pu), 0, 'R');
										}


										if($nReg==7)
										{
											$pdf->SetXY($this->posxpar, $curY);
											$pdf->MultiCell($this->page_largeur-$this->posxpar-$this->marge_droite, 3, price($value,0,'',0,$nDecimal_total), 0, 'R');
										}
									}
								}
								elseif($nom == 'total')
								{
									foreach ($row AS $nReg => $value)
									{
										//$pdf->SetFillColor(40,120,150);
										//$pdf->SetFillColor(60,180,190);


										$pdf->SetFillColor(171, 178, 185);
										$pdf->SetFont('','B');

										if($nReg==1 )
										{
												//$pdf->SetXY($this->posxdes, $curY);
												//$pdf->MultiCell($this->page_largeur-$this->posxdes-$this->marge_droite, 3, $value, 0, 'L',1);
											$pdf->SetXY($this->posxnro, $curY);
											$pdf->MultiCell($this->page_largeur-$this->posxnro-$this->marge_droite, 3, $value, 0, 'L',1);
										}

										if($nReg==7)
										{
											$pdf->SetXY($this->posxpar, $curY);
											$pdf->MultiCell($this->page_largeur-$this->posxpar-$this->marge_droite, 3, price($value,0,'',0,$nDecimal_total), 0, 'R');

										}
										$pdf->SetFont('');
									}
								}
								elseif($nom == 'totalf')
								{
									foreach ($row AS $nReg => $value)
									{
										if($nReg==7)
										{
											$nTotalpu=$value;
										}
									}
								}
								if ($newNexY>0 && $newNexY>$curY)	$nexY = $newNexY;
								else $nexY = $pdf->GetY();
								$nexY+=1;


								if ($nom != 'totalf' && $nexY> 240)
								{
									// Pied de page
									$this->_pagefoot($pdf,$object,$outputlangs);
									if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

									$pdf->AddPage();

									$posy=$this->marge_haute;
									$posx=$this->page_largeur-$this->marge_droite-100;

									$pdf->SetFont('','', $default_font_size - 3);
									$pdf->MultiCell(0, 3, '');
								// Set interline to 3
									$pdf->SetTextColor(0,0,0);
								//$tab_top = 60;
									$tab_top = 60;
								//if ($j == 1) $tab_top+=130;
								//if ($j == 1) $tab_top+=130;
									$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?60:10);
										//$tab_height = 130;
									$tab_height = 120;
									$tab_height_newpage = 120;

									$iniY = $tab_top +8;
									$curY = $tab_top+8;
									$nexY = $tab_top +8;

									$this->_pagehead($pdf, $object, 1, $outputlangs,$societe);

									//$pdf->SetFont();
									$pdf->SetFont('','', $default_font_size - 2);
									// imprimimos subtitulos
									//
									$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1, $object->multicurrency_code,$seltype);
								}
							}

						}

						//$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1, $object->multicurrency_code,$seltype);

						$object->total_ttc= $nTotalpu;
						$posy=$this->_tableau_tot($pdf, $object, 0, $bottomlasttab, $outputlangs,$general,$seltype);
						// Pied de page
						$this->_pagefoot($pdf,$object,$outputlangs);
						if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

					}
				}


				// Customer signature area
				if (empty($conf->global->PROPAL_DISABLE_SIGNATURE))
				{
					//$posy=$this->_signature_area($pdf, $object, $posy, $outputlangs);
				}



				$pdf->Close();

				$pdf->Output($file,'F');

				//Add pdfgeneration hook
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
			$this->error=$langs->trans("ErrorConstantNotDefined","PROP_OUTPUTDIR");
			return 0;
		}

		$this->error=$langs->trans("ErrorUnknown");
		return 0;   // Erreur par defaut
	}

	/**
	 *  Show payments table
	 *
	 *  @param	TCPDF		$pdf           Object PDF
	 *  @param  Object		$object         Object proposal
	 *  @param  int			$posy           Position y in PDF
	 *  @param  Translate	$outputlangs    Object langs for output
	 *  @return int             			<0 if KO, >0 if OK
	 */
	function _tableau_versements(&$pdf, $object, $posy, $outputlangs)
	{

	}


	/**
	 *   Show miscellaneous information (payment mode, payment term, ...)
	 *
	 *   @param		TCPDF		$pdf     		Object PDF
	 *   @param		Object		$object			Object to show
	 *   @param		int			$posy			Y
	 *   @param		Translate	$outputlangs	Langs object
	 *   @return	void
	 */
	function _tableau_info(&$pdf, $object, $posy, $outputlangs)
	{
		global $conf;
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

		$posxval=52;

		// Show shipping date
		if (! empty($object->date_livraison))
		{
			$outputlangs->load("sendings");
			$pdf->SetFont('','B', $default_font_size - 2);
			$pdf->SetXY($this->marge_gauche, $posy);
			$titre = $outputlangs->transnoentities("DateDeliveryPlanned").':';
			$pdf->MultiCell(80, 4, $titre, 0, 'L');
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($posxval, $posy);
			$dlp=dol_print_date($object->date_livraison,"daytext",false,$outputlangs,true);
			$pdf->MultiCell(80, 4, $dlp, 0, 'L');

			$posy=$pdf->GetY()+1;
		}
		elseif ($object->availability_code || $object->availability)    // Show availability conditions
		{
			$pdf->SetFont('','B', $default_font_size - 2);
			$pdf->SetXY($this->marge_gauche, $posy);
			$titre = $outputlangs->transnoentities("AvailabilityPeriod").':';
			$pdf->MultiCell(80, 4, $titre, 0, 'L');
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($posxval, $posy);
			$lib_availability=$outputlangs->transnoentities("AvailabilityType".$object->availability_code)!=('AvailabilityType'.$object->availability_code)?$outputlangs->transnoentities("AvailabilityType".$object->availability_code):$outputlangs->convToOutputCharset($object->availability);
			$lib_availability=str_replace('\n',"\n",$lib_availability);
			$pdf->MultiCell(80, 4, $lib_availability, 0, 'L');

			$posy=$pdf->GetY()+1;
		}

		// Show payments conditions
		if (empty($conf->global->PROPALE_PDF_HIDE_PAYMENTTERMCOND) && ($object->cond_reglement_code || $object->cond_reglement))
		{
			$pdf->SetFont('','B', $default_font_size - 2);
			$pdf->SetXY($this->marge_gauche, $posy);
			$titre = $outputlangs->transnoentities("PaymentConditions").':';
			$pdf->MultiCell(80, 4, $titre, 0, 'L');

			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($posxval, $posy);
			$lib_condition_paiement=$outputlangs->transnoentities("PaymentCondition".$object->cond_reglement_code)!=('PaymentCondition'.$object->cond_reglement_code)?$outputlangs->transnoentities("PaymentCondition".$object->cond_reglement_code):$outputlangs->convToOutputCharset($object->cond_reglement_doc);
			$lib_condition_paiement=str_replace('\n',"\n",$lib_condition_paiement);
			$pdf->MultiCell(80, 4, $lib_condition_paiement,0,'L');

			$posy=$pdf->GetY()+3;
		}

		if (empty($conf->global->PROPALE_PDF_HIDE_PAYMENTTERMCOND))
		{
			// Check a payment mode is defined
			/* Not required on a proposal
			if (empty($object->mode_reglement_code)
			&& ! $conf->global->FACTURE_CHQ_NUMBER
			&& ! $conf->global->FACTURE_RIB_NUMBER)
			{
				$pdf->SetXY($this->marge_gauche, $posy);
				$pdf->SetTextColor(200,0,0);
				$pdf->SetFont('','B', $default_font_size - 2);
				$pdf->MultiCell(90, 3, $outputlangs->transnoentities("ErrorNoPaiementModeConfigured"),0,'L',0);
				$pdf->SetTextColor(0,0,0);

				$posy=$pdf->GetY()+1;
			}
			*/

			// Show payment mode
			if ($object->mode_reglement_code
				&& $object->mode_reglement_code != 'CHQ'
				&& $object->mode_reglement_code != 'VIR')
			{
				$pdf->SetFont('','B', $default_font_size - 2);
				$pdf->SetXY($this->marge_gauche, $posy);
				$titre = $outputlangs->transnoentities("PaymentMode").':';
				$pdf->MultiCell(80, 5, $titre, 0, 'L');
				$pdf->SetFont('','', $default_font_size - 2);
				$pdf->SetXY($posxval, $posy);
				$lib_mode_reg=$outputlangs->transnoentities("PaymentType".$object->mode_reglement_code)!=('PaymentType'.$object->mode_reglement_code)?$outputlangs->transnoentities("PaymentType".$object->mode_reglement_code):$outputlangs->convToOutputCharset($object->mode_reglement);
				$pdf->MultiCell(80, 5, $lib_mode_reg,0,'L');

				$posy=$pdf->GetY()+2;
			}

			// Show payment mode CHQ
			if (empty($object->mode_reglement_code) || $object->mode_reglement_code == 'CHQ')
			{
				// Si mode reglement non force ou si force a CHQ
				if (! empty($conf->global->FACTURE_CHQ_NUMBER))
				{
					$diffsizetitle=(empty($conf->global->PDF_DIFFSIZE_TITLE)?3:$conf->global->PDF_DIFFSIZE_TITLE);

					if ($conf->global->FACTURE_CHQ_NUMBER > 0)
					{
						$account = new Account($this->db);
						$account->fetch($conf->global->FACTURE_CHQ_NUMBER);

						$pdf->SetXY($this->marge_gauche, $posy);
						$pdf->SetFont('','B', $default_font_size - $diffsizetitle);
						$pdf->MultiCell(100, 3, $outputlangs->transnoentities('PaymentByChequeOrderedTo',$account->proprio),0,'L',0);
						$posy=$pdf->GetY()+1;

						if (empty($conf->global->MAIN_PDF_HIDE_CHQ_ADDRESS))
						{
							$pdf->SetXY($this->marge_gauche, $posy);
							$pdf->SetFont('','', $default_font_size - $diffsizetitle);
							$pdf->MultiCell(100, 3, $outputlangs->convToOutputCharset($account->owner_address), 0, 'L', 0);
							$posy=$pdf->GetY()+2;
						}
					}
					if ($conf->global->FACTURE_CHQ_NUMBER == -1)
					{
						$pdf->SetXY($this->marge_gauche, $posy);
						$pdf->SetFont('','B', $default_font_size - $diffsizetitle);
						$pdf->MultiCell(100, 3, $outputlangs->transnoentities('PaymentByChequeOrderedTo',$this->emetteur->name),0,'L',0);
						$posy=$pdf->GetY()+1;

						if (empty($conf->global->MAIN_PDF_HIDE_CHQ_ADDRESS))
						{
							$pdf->SetXY($this->marge_gauche, $posy);
							$pdf->SetFont('','', $default_font_size - $diffsizetitle);
							$pdf->MultiCell(100, 3, $outputlangs->convToOutputCharset($this->emetteur->getFullAddress()), 0, 'L', 0);
							$posy=$pdf->GetY()+2;
						}
					}
				}
			}

			// If payment mode not forced or forced to VIR, show payment with BAN
			if (empty($object->mode_reglement_code) || $object->mode_reglement_code == 'VIR')
			{
				if (! empty($object->fk_account) || ! empty($object->fk_bank) || ! empty($conf->global->FACTURE_RIB_NUMBER))
				{
					$bankid=(empty($object->fk_account)?$conf->global->FACTURE_RIB_NUMBER:$object->fk_account);
					if (! empty($object->fk_bank)) $bankid=$object->fk_bank;   // For backward compatibility when object->fk_account is forced with object->fk_bank
					$account = new Account($this->db);
					$account->fetch($bankid);

					$curx=$this->marge_gauche;
					$cury=$posy;

					$posy=pdf_bank($pdf,$outputlangs,$curx,$cury,$account,0,$default_font_size);

					$posy+=2;
				}
			}
		}

		return $posy;
	}


	/**
	 *	Show total to pay
	 *
	 *	@param	PDF			$pdf            Object PDF
	 *	@param  Facture		$object         Object invoice
	 *	@param  int			$deja_regle     Montant deja regle
	 *	@param	int			$posy			Position depart
	 *	@param	Translate	$outputlangs	Objet langs
	 *	@return int							Position pour suite
	 */
	function _tableau_tot(&$pdf, $object, $deja_regle, $posy, $outputlangs,$general)
	{
		global $conf,$mysoc;
		$default_font_size = pdf_getPDFFontSize($outputlangs);

		$tab2_top = 250;
		$tab2_hl = 4;
		$pdf->SetFont('','', $default_font_size - 1);

		// Tabelau total
		$col1x = 120; $col2x = 170;
		if ($this->page_largeur < 210) // To work with US executive format
		{
			$col2x-=20;
		}
		$largcol2 = ($this->page_largeur - $this->marge_droite - $col2x);
		$useborder=0;
		$index = 0;
		// Show VAT by rates and total
		$pdf->SetFillColor(144,144,32);

		$this->atleastoneratenotnull=0;
		if (empty($conf->global->MAIN_GENERATE_DOCUMENTS_WITHOUT_VAT))
		{

				// Total TTC
			$index++;
			$pdf->SetXY($col1x, $tab2_top + $tab2_hl * $index);
			$pdf->SetFont('','B', $default_font_size - 1);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,0);
			$pdf->MultiCell($col2x-$col1x, $tab2_hl, $outputlangs->transnoentities("TotalTTC"), $useborder, 'L', 1);

			$total_ttc = number_format($object->total_ttc,$general->decimal_total);
			$pdf->SetXY($col2x, $tab2_top + $tab2_hl * $index);
			$pdf->MultiCell($largcol2, $tab2_hl, $total_ttc, $useborder, 'R', 1);

			$pdf->SetFont('');
		}
		$pdf->SetTextColor(0,0,0);

		if ($deja_regle > 0 && $abc)
		{
			$index++;

			$pdf->SetXY($col1x, $tab2_top + $tab2_hl * $index);
			$pdf->MultiCell($col2x-$col1x, $tab2_hl, $outputlangs->transnoentities("AlreadyPaid"), 0, 'L', 0);

			$pdf->SetXY($col2x, $tab2_top + $tab2_hl * $index);
			$pdf->MultiCell($largcol2, $tab2_hl, price($deja_regle, 0, $outputlangs), 0, 'R', 0);


			$index++;

		}

		$index++;
		return ($tab2_top + ($tab2_hl * $index));
	}

	/**
	 *   Show table for lines
	 *
	 *   @param		PDF			$pdf     		Object PDF
	 *   @param		string		$tab_top		Top position of table
	 *   @param		string		$tab_height		Height of table (rectangle)
	 *   @param		int			$nexY			Y (not used)
	 *   @param		Translate	$outputlangs	Langs object
	 *   @param		int			$hidetop		1=Hide top bar of array and title, 0=Hide nothing, -1=Hide only title
	 *   @param		int			$hidebottom		Hide bottom bar of array
	 *   @param		string		$currency		Currency code
	 *   @return	void
	 */
	function _tableau(&$pdf, $tab_top, $tab_height, $nexY, $outputlangs, $hidetop=0, $hidebottom=0, $currency='',$seltype='')
	{
		global $conf;

		// Force to disable hidetop and hidebottom

		$tab_height=190;
		//
		//
		$hidebottom=0;
		if ($hidetop) $hidetop=-1;

		$currency = !empty($currency) ? $currency : $conf->currency;
		$default_font_size = pdf_getPDFFontSize($outputlangs);

		// Amount in (at tab_top - 1)
		//$pdf->SetTextColor(0,0,0);
		//$pdf->SetFont('','',$default_font_size - 2);

		//$pdf->SetDrawColor(128,128,128);

		// Amount in (at tab_top - 1)
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('','', $default_font_size - 2);


		$pdf->SetFillColor(171, 178, 185);
		//$pdf->SetFillColor(132, 179, 229);

		$pdf->SetFont('','', $default_font_size - 1);


		$pdf->SetFont('','',$default_font_size - 1);
		$this->printRect($pdf,$this->marge_gauche, $tab_top-5, $this->page_largeur-$this->marge_gauche-$this->marge_droite, 5, $hidetop, $hidebottom);
		//$pdf->SetXY($this->posxpor-1, $tab_top-4);
		//$pdf->MultiCell($this->posxnpri-$this->posxpar-1,2, $outputlangs->transnoentities("Priceunit"),'','C',1);
		//$pdf->line($this->posxnpri, $tab_top-5, $this->posxnpri, $tab_top);
		//$pdf->line($this->posxpar, $tab_top-5, $this->posxpar, $tab_top);



		$pdf->SetXY($this->posxnro-1, $tab_top-5);
		$pdf->MultiCell($this->page_largeur-$this->posxnro-$this->marge_droite+1,2, $outputlangs->transnoentities(""),'','L',1);
		// precio unitario
		$pdf->SetXY($this->posxpor-1, $tab_top-5);
		$pdf->MultiCell($this->posxnpri-$this->posxpar,2, $outputlangs->transnoentities("Priceunit"),'','C');

		$pdf->line($this->posxnpri, $tab_top-5, $this->posxnpri, $tab_top);
		$pdf->line($this->posxpar, $tab_top-5, $this->posxpar, $tab_top);



		// Output Rect
		$this->printRect($pdf,$this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $hidetop, $hidebottom);	// Rect prend une longueur en 3eme param et 4eme param

		if (empty($hidetop))
		{
			$pdf->line($this->marge_gauche, $tab_top+5, $this->page_largeur-$this->marge_droite, $tab_top+5);	// line prend une position y en 2eme param et 4eme param

			$pdf->SetXY($this->posxnro-1, $tab_top+1);
			//$pdf->MultiCell(108,2, $outputlangs->transnoentities("Nro"),'','L');
		}

		//$pdf->line($this->posxdes-1, $tab_top, $this->posxdes-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxnro-1, $tab_top+1);
			$pdf->MultiCell($this->posxuni-$this->posxnro,2, $outputlangs->transnoentities("Description"),'','C',1);
		}


		$pdf->line($this->posxuni-1, $tab_top, $this->posxuni-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxuni-1, $tab_top+1);
			$pdf->MultiCell($this->posxqty-$this->posxuni,2, $outputlangs->transnoentities("Unid"),'','C',1);
		}

		$pdf->line($this->posxqty-1, $tab_top, $this->posxqty-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxqty-1, $tab_top+1);
			$pdf->MultiCell(($seltype=='PU'?$this->posxpor:$this->pri)-$this->posxqty,2, $outputlangs->transnoentities("Cantidad"),'','C',1);
		}
		if ($seltype == 'PU')
		{
			$pdf->line($this->posxpor-1, $tab_top, $this->posxpor-1, $tab_top + $tab_height);
			if (empty($hidetop))
			{
				$pdf->SetXY($this->posxpor-1, $tab_top+1);
				$pdf->MultiCell($this->posxnpri-$this->posxpor,2, $outputlangs->transnoentities("Percentproductivity"),'','C',1);
			}

			$pdf->line($this->posxnpri, $tab_top, $this->posxnpri, $tab_top + $tab_height);
			if (empty($hidetop))
			{
				$pdf->SetXY($this->posxnpri-1, $tab_top+1);
				$pdf->MultiCell($this->posxpri-$this->posxnpri,2, $outputlangs->transnoentities("Amountnoprod"),'','C',1);
			}
		}

		$pdf->line($this->posxpri-1, $tab_top, $this->posxpri-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxpri-1, $tab_top+1);
			$pdf->MultiCell($this->posxpar-$this->posxpri+1,2, $outputlangs->transnoentities("P.U"),'','C',1);
		}

		$pdf->line($this->posxpar, $tab_top, $this->posxpar, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxpar, $tab_top+1);
			$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxpar,2, $outputlangs->transnoentities("Costtotal"),'','C',1);
		}


	}

	/**
	 *  Show top header of page.
	 *
	 *  @param	PDF			$pdf     		Object PDF
	 *  @param  Object		$object     	Object to show
	 *  @param  int	    	$showaddress    0=no, 1=yes
	 *  @param  Translate	$outputlangs	Object lang for output
	 *  @return	void
	 */


	function _pagehead(&$pdf, $object, $showaddress, $outputlangs, $hookmanager)
	{
		global $conf,$langs;

		$outputlangs->load("budget@budget");

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);
		//$inventorysel = unserialize($_SESSION['inventorysel']);
		//$entrepot = new Entrepot($this->db);
		//$entrepot->fetch($inventorysel['fk_entrepot']);

		// Show Draft Watermark
		if($object->statut==0 && (! empty($conf->global->POA_DRAFT_WATERMARK)) )
		{
			pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->POA_DRAFT_WATERMARK);
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
		$pdf->SetXY($this->posxnro,$posy);
		$pdf->SetTextColor(0,0,60);
		$title=$outputlangs->transnoentities("Unitpriceanalysis");
		$pdf->MultiCell($this->page_largeur-$this->marge_gauche-$this->posxnro, 3, $title, '', 'C');

		$pdf->SetFont('','B',$default_font_size);



		// Proyecto
		$posy+=25;
		$pdf->SetXY($this->posxnro,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(300, 3, $outputlangs->transnoentities("Project").":", '', 'L');
		$pdf->SetXY($this->posxnro+20,$posy);
		$pdf->MultiCell(300, 3, $object->title, '', 'L');
		//Actividad
		$posy+=4;
		$pdf->SetXY($this->posxnro,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(300, 3, $outputlangs->transnoentities("Activity").":", '', 'L');
		$pdf->SetXY($this->posxnro+20,$posy);
		$pdf->MultiCell(300, 3, $object->label_det, '', 'L');

		// cantidad
		$posy+=4;
		$pdf->SetXY($this->posxnro,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(300, 3, $outputlangs->transnoentities("Quantity").':', '', 'L');
		$pdf->SetXY($this->posxnro+20,$posy);
		$pdf->MultiCell(300, 3, $object->cant_det, '', 'L');
		// unidad
		$posy+=4;
		$pdf->SetXY($this->posxnro,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(300, 3, $outputlangs->transnoentities("Unit").':', '', 'L');
		$pdf->SetXY($this->posxnro+20,$posy);
		$pdf->MultiCell(300, 3, $object->unit_det, '', 'L');
		$posy+=4;
		$pdf->SetXY($this->posxnro,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(300, 3, $outputlangs->transnoentities("Currencybase").':', '', 'L');
		$pdf->SetXY($this->posxnro+20,$posy);
		$pdf->MultiCell(300, 3, $object->base_currency_det, '', 'L');

		//Area
		/*$posy+=4;
		$pdf->SetXY($posxx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Area")." : " . $object->area, '', 'R');*/



		$posy+=1;

		// Show list of linked objects
		$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size, $hookmanager);
	}



	/**
	 *   	Show footer of page. Need this->emetteur object
	 *
	 *   	@param	PDF			$pdf     			PDF
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
		//return  pdf_pagefoot_fractal(&$pdf,$outputlangs,'POA_FREE_TEXT',$fromcompany,$marge_basse,$marge_gauche,$page_hauteur,$object,$showdetails=0,$hidefreetext=0);
	}
	/**
	 *	Show area for the customer to sign
	 *
	 *	@param	PDF			$pdf            Object PDF
	 *	@param  Facture		$object         Object invoice
	 *	@param	int			$posy			Position depart
	 *	@param	Translate	$outputlangs	Objet langs
	 *	@return int							Position pour suite
	 */
	function _signature_area(&$pdf, $object, $posy, $outputlangs)
	{
		$default_font_size = pdf_getPDFFontSize($outputlangs);
		$tab_top = $posy + 4;
		$tab_hl = 4;

		$posx = 120;
		$largcol = ($this->page_largeur - $this->marge_droite - $posx);
		$useborder=0;
		$index = 0;
		// Total HT
		$pdf->SetFillColor(255,255,255);
		$pdf->SetXY($posx, $tab_top + 0);
		$pdf->SetFont('','', $default_font_size - 2);
		$pdf->MultiCell($largcol, $tab_hl, $outputlangs->transnoentities("ProposalCustomerSignature"), 0, 'L', 1);

		$pdf->SetXY($posx, $tab_top + $tab_hl);
		$pdf->MultiCell($largcol, $tab_hl*3, '', 1, 'R');

		return ($tab_hl*7);
	}
}

