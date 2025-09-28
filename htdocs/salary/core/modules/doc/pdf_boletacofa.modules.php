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

require_once DOL_DOCUMENT_ROOT.'/salary/core/modules/salary/modules_salary.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';


/**
 *	Class to manage PDF invoice template Crabe
 */
class pdf_boletacofa extends ModelePDFSalary
{


	var $db;
	var $name;
	var $description;
	var $type;

	var $phpmin = array(4,3,0);
  // Minimum version of PHP required by module
	var $version = 'dolibarr';

	var $page_largeur;
	var $page_hauteur;
	var $format;
	var $marge_gauche;
	var	$marge_droite;
	var	$marge_haute;
	var	$marge_basse;

	var $emetteur;
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
		$langs->load("salary@salary");

		$this->db = $db;
		$this->name = "boletacofa";
		$this->description = $langs->trans('PDFBoletacofaDescription');

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

		$this->option_logo = 1;
					// Affiche logo
		$this->option_tva = 1;
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
		$this->posxnom=$this->marge_gauche+1;
		$this->posxpat=$this->marge_gauche+1;

		$this->posxdes=$this->marge_gauche+1;
		$this->posxfac=110;
		$this->posxing=140;
		$this->posxgas=170;

		$this->postotalht=174;
		if ($this->page_largeur < 210)
	 // To work with US executive format
		{
			$this->posxgas-=20;
			$this->posxing-=20;
			$this->posxfac-=20;
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
	*  @param		object		$hookmanager		Hookmanager object
	*  @return     int         	    			1=OK, 0=KO
	*/
	function write_file(&$object,$outputlangs,$srctemplatepath='',$hidedetails=0,$hidedesc=0,$hideref=0,$hookmanager=false,$aList=array())
	{
		global $user,$langs,$conf,$mysoc,$db,$objectAd,$objectU,$objectCh;
		if (! is_object($outputlangs)) $outputlangs=$langs;
	// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
		if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output='ISO-8859-1';

		$outputlangs->load("main");
		$outputlangs->load("dict");
		$outputlangs->load("companies");
		$outputlangs->load("bills");
		$outputlangs->load("products");

		if ($conf->salary->dir_output)
		{
			$aParamBoleta = $_SESSION['aParamBoleta'];
	// $object->fetch_thirdparty();

	// $deja_regle = $object->getSommePaiement();
	// $amount_credit_notes_included = $object->getSumCreditNotesUsed();
	// $amount_deposits_included = $object->getSumDepositsUsed();

	// Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->salary->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
		// $objectref = dol_sanitizeFileName($aParamBoleta['anio'].$aParamBoleta['mes']);
				$objectref = dol_sanitizeFileName('boleta'.$object->anio.(strlen($object->mes)==1?'0'.$object->mes:$object->mes));

				$dir = $conf->salary->dir_output.'/boleta';
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

		//contando los items que tiene cada empleado
				$nblignessup = count($aList);

				$pdf=pdf_getInstance($this->format);
				$default_font_size = pdf_getPDFFontSize($outputlangs);
			// Must be after pdf_getInstance
				$heightforinfotot = 10;
		// Height reserved to output the info and total part
				$heightforfreetext= (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT)?$conf->global->MAIN_PDF_FREETEXT_HEIGHT:5);
			// Height reserved to output the free text on last page
				$heightforfooter = $this->marge_basse + 90;
			// Height reserved to output the footer (value include bottom margin)
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

				$pdf->SetTitle($outputlangs->convToOutputCharset($objectref));
				$pdf->SetSubject($outputlangs->transnoentities("Boletapago"));
				$pdf->SetCreator("Dolibarr ".DOL_VERSION);
				$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
				$pdf->SetKeyWords($outputlangs->convToOutputCharset($objectref)." ".$outputlangs->transnoentities("Boletapago"));
				if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);

				$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);
						// Left, Top, Right
				foreach ($aList AS $idUser => $dataUser)
				{
					for ($j = 0; $j < 2; $j++)
					{
						if ($j == 0) $pdf->AddPage();

						$objectAd->fetch($idUser);
						$objectU->fetch_user($idUser);
						$objectCh->fetch($objectU->fk_charge);

						//asignando valores del user
						$object->name = $objectAd->lastname.' '.$objectAd->lastnametwo.' '.$objectAd->firstname;

						$nblignes = count($object->lines);

						$sql = "SELECT p.fk_concept, p.amount, p.hours, ";
						$sql.= " c.detail, c.print, c.type_cod ";
						$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_history AS p ";
						$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_concept AS c ON p.fk_concept = c.rowid ";
						$sql.= " WHERE ";
						$sql.= " p.entity = ".$conf->entity ." AND ";
						$sql.= " p.fk_period = ".$aParamBoleta['fk_period'] ." AND ";
						$sql.= " p.fk_proces = ".$aParamBoleta['fk_proces'] ." AND ";
						$sql.= " p.fk_type_fol = ".$aParamBoleta['fk_type_fol'] ." AND ";
						$sql.= " p.fk_user = ".$idUser ;
						$sql.= " ORDER BY c.type_cod,c.ref ";
						$resql = $db->query($sql);
						if ($resql)
						{
							$num = $db->num_rows($resql);
							$nblignes = $num;

							$i = 0;
							if ($num)
							{
								if (! empty($tplidx)) $pdf->useTemplate($tplidx);
								$pagenb++;
								if ($j == 0) $posy=$this->marge_haute;
								else $posy=$this->marge_haute+130;
								$posx=$this->page_largeur-$this->marge_droite-100;

								$this->_pagehead($pdf, $object, 1, $outputlangs, $hookmanager,$posx,$posy);

								$pdf->SetFont('','', $default_font_size - 1);
								$pdf->MultiCell(0, 3, '');
								// Set interline to 3
								$pdf->SetTextColor(0,0,0);

								$tab_top = 60;
								if ($j == 1) $tab_top+=130;
								$tab_top_newpage = (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)?60:10);
								$tab_height = 130;
								$tab_height_newpage = 150;

								$iniY = $tab_top + 7;
								$curY = $tab_top + 7;
								$nexY = $tab_top + 7;
									//titulos
								$pdf->SetFont('','', $default_font_size - 1);
								$pdf->writeHTMLCell(190, 3, $this->posxdes+1, $tab_top-2, $langs->trans('Concept yemer'), 0, 1);
								//$pdf->writeHTMLCell(0, 3, $this->posxfac+1, $tab_top-2, $langs->trans('Factor'), 0, 1);
								$pdf->writeHTMLCell(0, 3, $this->posxing+1, $tab_top-2, $langs->trans('Ingresos'), 0, 1);
								$pdf->writeHTMLCell(0, 3, $this->posxgas+1, $tab_top-2, $langs->trans('Descuentos'), 0, 1);
								$nexY = $pdf->GetY()+2;
								$height_note=$nexY-($tab_top-2);

								// Rect prend une longueur en 3eme param
								$pdf->SetDrawColor(192,192,192);
								$pdf->Rect($this->marge_gauche, $tab_top-3, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $height_note+1);

								$tab_height = $tab_height - $height_note;
								$tab_top = $nexY+5;


								$nSumaing = 0;
								$nSumagas = 0;
								$var=True;


								$pdf->SetFont('','', $default_font_size - 1);
								$pdf->SetTextColor(0,0,0);

								$pdf->setTopMargin($tab_top_newpage);
								$pdf->setPageOrientation('', 1, $heightforfooter+$heightforfreetext+$heightforinfotot);
								$pageposbefore=$pdf->getPage();

								$curX = $this->posxdes-1;

								$showpricebeforepagebreak=1;

								$pdf->startTransaction();
								pdf_writelinedesc($pdf,$obj,$i,$outputlangs,$this->posxfac-$curX,3,$curX,$curY,$hideref,$hidedesc,0,$hookmanager);
								$pageposafter=$pdf->getPage();
								if ($pageposafter > $pageposbefore)
										// There is a pagebreak
								{
									$pdf->rollbackTransaction(true);
									$pageposafter=$pageposbefore;
											//print $pageposafter.'-'.$pageposbefore;exit;
									$pdf->setPageOrientation('', 1, $heightforfooter);
											// The only function to edit the bottom margin of current page to set it.
									pdf_writelinedesc($pdf,$object,$i,$outputlangs,$this->posxfac-$curX,4,$curX,$curY,$hideref,$hidedesc,0,$hookmanager);
									$pageposafter=$pdf->getPage();
									$posyafter=$pdf->GetY();
											//var_dump($posyafter); var_dump(($this->page_hauteur - ($heightforfooter+$heightforfreetext+$heightforinfotot))); exit;
									if ($posyafter > ($this->page_hauteur - ($heightforfooter+$heightforfreetext+$heightforinfotot)))
												// There is no space left for total+free text
									{
										if ($i == ($nblignes-1))
												// No more lines, and no space left to show total, so we create a new page
										{
											$pdf->AddPage('','',true);
											if (! empty($tplidx)) $pdf->useTemplate($tplidx);
											if (empty($conf->global->MAIN_PDF_DONOTREPEAT_HEAD)) $this->_pagehead($pdf, $object, 0, $outputlangs, $hookmanager,$posx,$posy);
											$pdf->setPage($pagenb+1);
										}
									}
									else
									{
											// We found a page break
										$showpricebeforepagebreak=0;
									}
								}
								else
										// No pagebreak
								{
										//	$pdf->commitTransaction();
								}

								//recorriendo los conceptos
								while ($i < $num)
								{
									$obj = $db->fetch_object($resql);

									if ( $obj->print == 1 && $obj->amount > 0)
									{
										//revisar lineas
										$curY = $nexY;
										$pageposafter=$pdf->getPage();
										$pdf->setPage($pageposbefore);
										$pdf->setTopMargin($this->marge_haute);
										$pdf->setPageOrientation('', 1, 0);
										//if ($pageposafter > $pageposbefore && empty($showpricebeforepagebreak))
										//{
										//	$pdf->setPage($pageposafter); $curY = $tab_top_newpage;
										//}
										//fin revisar


										$pdf->SetFont('','', $default_font_size - 1);
				 						// On repositionne la police par defaut
										//detalle del concepto
										$pdf->SetXY($this->posxdes, $curY);
										$pdf->MultiCell($this->posxfac-$this->posxdes-1, 3, $obj->detail, 0, 'L', 0);

										//$pdf->SetXY($this->posxfac, $curY);
										//$pdf->MultiCell($this->posxing-$this->posxfac-1, 3, $obj->hours, 0, 'C', 0);

										if ($obj->type_cod == 1)
										{
											$pdf->SetXY($this->posxing, $curY);
											$pdf->MultiCell($this->posxgas-$this->posxing-1, 3, $obj->amount, 0, 'R', 0);
											$nSumaing += $obj->amount;
										}
										if ($obj->type_cod == 2)
										{
											$pdf->SetXY($this->posxgas, $curY);
											$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxgas, 3, $obj->amount, 0, 'R', 0);
											$nSumagas += $obj->amount;
										}
										// Add line
										if ( $i < ($nblignes - 1))
										{
											$pdf->SetLineStyle(array('dash'=>'1,1','color'=>array(210,210,210)));
											$pdf->SetDrawColor(190,190,200);
											$pdf->line($this->marge_gauche, $nexY+4, $this->page_largeur - $this->marge_droite, $nexY+4);
											$pdf->SetLineStyle(array('dash'=>0));
										}
										$nexY = $pdf->GetY();
										$nexY+=2;
									}
									$i++;
								}
								$curY = $nexY;
								//imprimimos los totales
								$pdf->SetXY($this->posxfac, $curY);
								$pdf->MultiCell($this->posxing-$this->posxfac-1, 3, $langs->trans('Total'), 0, 'R', 0);

								$pdf->SetXY($this->posxing, $curY);
								$pdf->MultiCell($this->posxgas-$this->posxing-1, 3, price($nSumaing), 0, 'R', 0);

								$pdf->SetXY($this->posxgas, $curY);
								$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxgas, 3, price($nSumagas), 0, 'R', 0);

								//imprimimos la diferencia
								$nexY = $pdf->GetY();
								$curY = $nexY+2;
								$balance = $nSumaing-$nSumagas;
								$pdf->SetXY($this->posxfac, $curY);
								$pdf->MultiCell($this->posxing-$this->posxfac-1, 3, $langs->trans('Pagable'), 0, 'R', 0);
								if ($balance>0)
								{
									$pdf->SetXY($this->posxing, $curY);
									$pdf->MultiCell($this->posxgas-$this->posxing-1, 3, price($balance), 0, 'R', 0);
								}
								else
								{
									$pdf->SetXY($this->posxgas, $curY);
									$pdf->MultiCell($this->page_largeur-$this->marge_droite-$this->posxgas, 3, price($balance), 0, 'R', 0);
								}
								$nexY = $pdf->GetY();
								$curY = $nexY+20;
								//imprimimos para la firma
								$pdf->SetFont('','', $default_font_size - 1);
								$pdf->writeHTMLCell(190, 3, $this->posxdes+30, $curY, $langs->trans('Firma Empleador'), 0, 1);
								$pdf->writeHTMLCell(0, 3, $this->posxing+1, $curY, $langs->trans('Firma empleado'), 0, 1);
								$nexY = $pdf->GetY();

								// Rect prend une longueur en 3eme param
								//$pdf->SetDrawColor(192,192,192);
								//$pdf->Rect($this->marge_gauche, $tab_top-3, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $height_note+1);



								// Show square imprime lineas del cuerpo
								if ($pagenb == 1)
								{
											//$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 0, 0);
									$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
								}
								else
								{
											//$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 0, 0);
									$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
								}
							}
						}
					}
						// Pied de page
					$this->_pagefoot($pdf,$object,$outputlangs);
				}
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


	/**
	*   Show miscellaneous information (payment mode, payment term, ...)
	*
	*   @param		PDF			&$pdf     		Object PDF
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
		if ($this->emetteur->pays_code == 'FR' && $this->franchise == 1)
		{
			$pdf->SetFont('','B', $default_font_size - 2);
			$pdf->SetXY($this->marge_gauche, $posy);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("VATIsNotUsedForInvoice"), 0, 'L', 0);

			$posy=$pdf->GetY()+4;
		}

		$posxval=52;

	// Show payments conditions
		if ($object->type != 2 && ($object->cond_reglement_code || $object->cond_reglement))
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

		if ($object->type != 2)
		{
	// Check a payment mode is defined
			if (empty($object->mode_reglement_code)
				&& ! $conf->global->FACTURE_CHQ_NUMBER
				&& ! $conf->global->FACTURE_RIB_NUMBER)
			{
				$pdf->SetXY($this->marge_gauche, $posy);
				$pdf->SetTextColor(200,0,0);
				$pdf->SetFont('','B', $default_font_size - 2);
				$pdf->MultiCell(80, 3, $outputlangs->transnoentities("ErrorNoPaiementModeConfigured"),0,'L',0);
				$pdf->SetTextColor(0,0,0);

				$posy=$pdf->GetY()+1;
			}

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
					if ($conf->global->FACTURE_CHQ_NUMBER > 0)
					{
						$account = new Account($this->db);
						$account->fetch($conf->global->FACTURE_CHQ_NUMBER);

						$pdf->SetXY($this->marge_gauche, $posy);
						$pdf->SetFont('','B', $default_font_size - 3);
						$pdf->MultiCell(100, 3, $outputlangs->transnoentities('PaymentByChequeOrderedTo',$account->proprio),0,'L',0);
						$posy=$pdf->GetY()+1;

						if (empty($conf->global->MAIN_PDF_HIDE_CHQ_ADDRESS))
						{
							$pdf->SetXY($this->marge_gauche, $posy);
							$pdf->SetFont('','', $default_font_size - 3);
							$pdf->MultiCell(100, 3, $outputlangs->convToOutputCharset($account->adresse_proprio), 0, 'L', 0);
							$posy=$pdf->GetY()+2;
						}
					}
					if ($conf->global->FACTURE_CHQ_NUMBER == -1)
					{
						$pdf->SetXY($this->marge_gauche, $posy);
						$pdf->SetFont('','B', $default_font_size - 3);
						$pdf->MultiCell(100, 3, $outputlangs->transnoentities('PaymentByChequeOrderedTo',$this->emetteur->name),0,'L',0);
						$posy=$pdf->GetY()+1;

						if (empty($conf->global->MAIN_PDF_HIDE_CHQ_ADDRESS))
						{
							$pdf->SetXY($this->marge_gauche, $posy);
							$pdf->SetFont('','', $default_font_size - 3);
							$pdf->MultiCell(100, 3, $outputlangs->convToOutputCharset($this->emetteur->getFullAddress()), 0, 'L', 0);
							$posy=$pdf->GetY()+2;
						}
					}
				}
			}

	// If payment mode not forced or forced to VIR, show payment with BAN
			if (empty($object->mode_reglement_code) || $object->mode_reglement_code == 'VIR')
			{
				if (! empty($conf->global->FACTURE_RIB_NUMBER))
				{
					$account = new Account($this->db);
					$account->fetch($conf->global->FACTURE_RIB_NUMBER);

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
	*	@param	PDF			&$pdf           Object PDF
	*	@param  Facture		$object         Object invoice
	*	@param  int			$deja_regle     Montant deja regle
	*	@param	int			$posy			Position depart
	*	@param	Translate	$outputlangs	Objet langs
	*	@return int							Position pour suite
	*/
	function _tableau_tot(&$pdf, $object, $deja_regle, $posy, $outputlangs,$sumaing,$sumagas)
	{
		global $conf,$mysoc;

		$sign=1;
		if ($object->type == 2 && ! empty($conf->global->INVOICE_POSITIVE_CREDIT_NOTE)) $sign=-1;

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		$tab2_top = $posy;
		$tab2_hl = 4;
		$pdf->SetFont('','', $default_font_size - 1);

	// Tableau total
		$col0x = 100;
		$col1x = 140;
		$col2x = 170;
		if ($this->page_largeur < 210)
	// To work with US executive format
		{
			$col2x-=20;
		}
		$largcol1 = ($this->page_largeur - $this->marge_droite - $col1x);
		$largcol2 = ($this->page_largeur - $this->marge_droite - $col2x);

		$useborder=1;
		$index = 0;

		$widthrecbox = $this->page_largeur-$this->marge_gauche-$this->marge_droite;
		$hautcadre = 5;
		$pdf->Rect($this->marge_gauche, $tab2_top, $widthrecbox, $hautcadre);
	// Total HT
		$pdf->SetFillColor(255,255,255);

		$pdf->SetXY($col0x, $tab2_top + 0);
		$pdf->MultiCell($col0x, $tab2_hl, $outputlangs->transnoentities("Sumas"), 0, 'L', 1);

		$pdf->SetXY($col1x, $tab2_top + 0);
		$pdf->MultiCell($col2x-$col1x-1, $tab2_hl, price($sumaing), 0, 'R', 1);

		$pdf->SetXY($col2x, $tab2_top + 0);
		$pdf->MultiCell($this->page_largeur-$this->marge_droite-$col2x, $tab2_hl, price($sumagas), 0, 'R', 1);

		$index++;
		return ($tab2_top + ($tab2_hl * $index));
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
		$pdf->SetFont('','', $default_font_size - 1);

		//concepto
		$pdf->line($this->posxdes-1, $tab_top, $this->posxdes-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxdes-1, $tab_top+1);
			$pdf->MultiCell($this->posxfac-$this->posxdes-1,2, $outputlangs->transnoentities("Concept HOLA"),'','C');
		}

		//factor
		//$pdf->line($this->posxfac-1, $tab_top, $this->posxfac-1, $tab_top + $tab_height);
		//if (empty($hidetop))
		//{
		//	$pdf->SetXY($this->posxfac-1, $tab_top+1);
		//	$pdf->MultiCell($this->posxing-$this->posxfac-1,2, $outputlangs->transnoentities("Factor"),'','C');
		//}
		//
		//ingreso
		$pdf->line($this->posxing-1, $tab_top, $this->posxing-1, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxing-1, $tab_top+1);
			$pdf->MultiCell($this->posxgas-$this->posxing+1,2, $outputlangs->transnoentities("Totaling"),'','R');
		}
		if ($this->atleastonediscount)
		{
			$pdf->line($this->postotalht, $tab_top, $this->postotalht, $tab_top + $tab_height);
		}
		//gasto
		$pdf->line($this->posxgas, $tab_top, $this->posxgas, $tab_top + $tab_height);
		if (empty($hidetop))
		{
			$pdf->SetXY($this->posxgas-1, $tab_top+1);
			$pdf->MultiCell($this->page_largeur-$this->posxgas+1,2, $outputlangs->transnoentities("Totalgas"),'','R');
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
	function _pagehead(&$pdf, $object, $showaddress, $outputlangs, $hookmanager,$posx,$posy)
	{
		global $conf,$langs;

		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("companies");

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

		$pdf->SetTextColor(0,0,60);
		$pdf->SetFont('','B', $default_font_size + 3);


		$pdf->SetXY($this->marge_gauche,$posy);


		$pdf->SetFont('','B', $default_font_size + 3);
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$title=$outputlangs->transnoentities("Payslip");
		$pdf->MultiCell(80, 3, $title, '', 'L');

		$pdf->SetFont('','B',$default_font_size);

		$posy+=5;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 4, $outputlangs->transnoentities("Period")." : " . $outputlangs->convToOutputCharset((strlen($object->mes)==1?'0'.$object->mes:$object->mes)).'/'.$outputlangs->convToOutputCharset($object->anio), '', 'R');

		$posy+=5;
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$pdf->MultiCell(100, 4, $outputlangs->transnoentities("Dateimpress")." : " . $outputlangs->convToOutputCharset(dol_print_date(dol_now(),'dayhour')), '', 'R');
		$posy+=5;
		// Show list of linked objects
		//$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size, $hookmanager);



		$pdf->MultiCell(66,5, $outputlangs->transnoentities("NOMBRE Y APELLIDO").":", 0, 'L');
		$pdf->MultiCell(66,5, $outputlangs->transnoentities("NIVEL").":", 0, 'L');
		$posy+=4;
		$pdf->MultiCell(66,5, $outputlangs->transnoentities("CI").":", 0, 'L');
		$pdf->MultiCell(66,5, $outputlangs->transnoentities("ITEM").":", 0, 'L');
		$posy+=4;
		$pdf->MultiCell(66,5, $outputlangs->transnoentities("CARGO").":", 0, 'L');
		$pdf->MultiCell(66,5, $outputlangs->transnoentities("MATRICULA").":", 0, 'L');




		/*

		if ($showaddress)
		{
			// Member properties
			//$carac_emetteur = pdf_build_address_member($outputlangs,$this->emetteur);

			// Show sender
			//$posy=42;
			$posx=$this->marge_gauche;
			if (! empty($conf->global->MAIN_INVERT_SENDER_RECIPIENT)) $posx=$this->page_largeur-$this->marge_droite-80;
			$hautcadre=25;

			// Show sender frame
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($posx,$posy-5);
			$pdf->MultiCell(66,5, $outputlangs->transnoentities("Pagado a").":", 0, 'L');
			$pdf->SetXY($posx,$posy);
			$pdf->SetFillColor(230,230,230);
			$pdf->MultiCell(95, $hautcadre, "", 0, 'R', 1);
			$pdf->SetTextColor(0,0,60);

			// Show sender name
			$pdf->SetXY($posx+2,$posy+3);
			$pdf->SetFont('','B', $default_font_size);
			$pdf->MultiCell(80, 4, $outputlangs->convToOutputCharset($object->name), 0, 'L');

			// Show sender information
			$pdf->SetXY($posx+2,$posy+8);
			$pdf->SetFont('','', $default_font_size - 1);
			$pdf->MultiCell(80, 4, $carac_emetteur, 0, 'L');

		}

		*/
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
		return pdf_pagefoot($pdf,$outputlangs,'MENSAJE LIBRE DE TEXTO',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,0,$hidefreetext);
	}

}

?>
