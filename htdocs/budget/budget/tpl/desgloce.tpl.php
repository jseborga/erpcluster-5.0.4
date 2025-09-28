<?php

//print_fiche_titre($langs->trans("Upload"));

//dol_fiche_head();

//print '<form  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
//print '<input type="hidden" name="action" value="veriffile">';
//print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
//print '<input type="hidden" name="id" value="'.$id.'">';




if($action== 'desgloce')
{


	$resb = $object->fetch($id);
	// inicio yemer //
	$aPresupuertodesgloce==array();
	$filter = " AND t.fk_budget = ".$id;
	$resdesgloce = $objectdet->fetchAll('', '', 0, 0, array(), 'AND',$filter);

	$aGroup=array();
	$aLine = array();
	$aTotal=array();
	if($resdesgloce>0)
	{
		$lines = $objectdet->lines;
		foreach ($lines AS $j => $line)
		{
			$res = $objectdetadd->fetch(0,$line->id);

			$aGroup[$line->fk_task_parent][$line->id]=($res==1?$objectdetadd->c_grupo:0);
			$aLine[$line->id] = $line;


			$aPresupuertodesgloce[$line->fk_budget][$line->fk_task_parent][$line->id]['label']=$line->label;
			$aPresupuertodesgloce[$line->fk_budget][$line->fk_task_parent][$line->id]['ref']=$line->ref;

		}
	}
	//echo '<pre>';
	//print_r($aLine);
	//echo '</pre>';


	//echo '<pre>';
	//print_r($aGroup);
	//echo '</pre>';


	//vamos a listar empezando de cero

	//cabecera
	print '<table width="100%" class="border centpercent">';
	//print '<tr colspan="10">';
	print "<tr class=\"liste_titre\">";
	print '<td >'."".'</td>';
	print '<td >'."".'</td>';
	print '<td >'."".'</td>';
	print '<td >'."".'</td>';
	print '<td >'."".'</td>';
	print_liste_field_titre($langs->trans("Material."),"liste.php", "p.table_cod","","",'colspan="2"',$sortfield,$sortorder);

	print_liste_field_titre($langs->trans("Mano de obra."),"liste.php", "p.table_cod","","",'colspan="2" align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Maquinaria."),"liste.php", "p.table_cod","","",'colspan="2"',$sortfield,$sortorder);
	print '<td >'."".'</td>';
	print '</tr>';
	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Item"),"liste.php", "p.table_cod","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Description"),"liste.php", "p.table_name","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Unit"),"liste.php", "p.table_name","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Quantity"),"liste.php", "p.field_name","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Nacional"),"liste.php", "p.field_name","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Importado"),"liste.php", "p.field_name","","","",$sortfield,$sortorder);
					//print_liste_field_titre($langs->trans("Nationality"),"liste.php", "p.sequen","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Especializado"),"liste.php", "p.state","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("No especializado"),"liste.php", "p.state","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Nacional"),"liste.php", "p.state","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Importado"),"liste.php", "p.state","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Total"),"liste.php", "p.state","","","",$sortfield,$sortorder);
	print "</tr>\n";
	$aTitle[1]=$langs->trans('Item');
	$aTitle[2]=$langs->trans('Description');
	$aTitle[3]=$langs->trans('Unit');
	$aTitle[4]=$langs->trans('Quantity');
	$aTitle[5]=$langs->trans('National');
	$aTitle[6]=$langs->trans('Imported');
	$aTitle[7]=$langs->trans('Especializated');
	$aTitle[8]=$langs->trans('Donotespecializated');
	$aTitle[9]=$langs->trans('National');
	$aTitle[10]=$langs->trans('Imported');
	$aTitle[11]=$langs->trans('Total');

	$aTotalresult=array();
	$aPrincipal = $aGroup[0];

	//echo'<pre>';
	//print_r($aGroup);
	//echo'</pre>';
	//exit;

	foreach ($aPrincipal AS $j => $grupo)
	{
		//echo '<hr>prin '.$j;
		//echo ' label '.$aLine[$j]->label;
		if($grupo)
		{
			 //echo ' es grupo';
			print '<tr colspan="10">';
			print '<td >'.$aLine[$j]->ref.'</td>';
			print '<td>'.$aLine[$j]->label.'</td>';
			print '</tr>';
		}
		else
		{
			$res = $objectdetadd->fetch(0,$j);
			if($res>0)
			{
				//echo ' es un ITEM '.$objectdetadd->fk_unit.' cantidad '.$objectdetadd->unit_budget;
				//print '<tr>';
				print '<td>'.$objectdetadd->fk_unit.'</td>';
				print '<td>'.$objectdetadd->unit_budget.'</td>';
				//print '</tr>';
			}
		}
		if ($aGroup[$j])
		{
			$aSec = $aGroup[$j];
			foreach ($aSec AS $k => $grupo2)
			{
				//echo '<hr>sec '.$k;
				//echo ' label '.$aLine[$k]->label;

				print_r($grupo2);



				//echo '<hr>'.$k;
				print '<tr>';
				print '<td>'.$aLine[$k]->ref.'</td>';
				print '<td>'.$aLine[$k]->label.'</td>';
				if($grupo2)
				{
					//print '<tr>';
					//print '<td >'.$aLine[$j]->ref.'</td>';
					//print '<td>'.$aLine[$j]->label.'</td>';

				}
				else
				{
					$res = $objectdetadd->fetch(0,$k);
					if($res>0 )
					{
						print '<td>'.$objectdetadd->fk_unit.'</td>';
						print '<td>'.$objectdetadd->unit_budget.'</td>';
						//echo ' es un ITEM '.$objectdetadd->fk_unit.' cantidad '.$objectdetadd->unit_budget;
						$fk_budget_task=$objectdetadd->fk_budget_task;
						$filter = " AND t.fk_budget_task = ".$fk_budget_task;
						// object budget task resource
						$ressource = $objectbtr->fetchAll('ASC', 't.rowid', 0, 0, array(), 'AND',$filter);
						//echo'<pre>';
						//print_r($objectbtr);
						//echo'</pre>';
						//exit;
						if($ressource>0)
						{
							$aResult = procesa($objectbtr);
							// material
							print '<td align="right">'.price(price2num($aResult['nTotalmatnacional'],'MT')).'</td>';
							print '<td align="right">'.price(price2num($aResult['nTotalmatimportado'],'MT')).'</td>';
							// mano de obra
							print '<td align="right">'.price(price2num($aResult['nTotalespecialista'],'MT')).'</td>';
							print '<td align="right">'.price(price2num($aResult['nTotalnoespecialista'],'MT')).'</td>';
							// maquinaria
							print '<td align="right">'.price(price2num($aResult['nTotalmaqnacional'],'MT')).'</td>';
							print '<td align="right">'.price(price2num($aResult['nTotalmaqimportado'],'MT')).'</td>';
							// total
							print '<td align="right">'.price(price2num($aResult['nTotaltotal'],'MT')).'</td>';

							$aTotalresult['nTotalmatnacional']+=$aResult['nTotalmatnacional'];
							$aTotalresult['nTotalmatimportado']+=$aResult['nTotalmatimportado'];
							$aTotalresult['nTotalespecialista']+=$aResult['nTotalespecialista'];
							$aTotalresult['nTotalnoespecialista']+=$aResult['nTotalnoespecialista'];
							$aTotalresult['nTotalmaqnacional']+=$aResult['nTotalmaqnacional'];
							$aTotalresult['nTotalmaqimportado']+=$aResult['nTotalmaqimportado'];

							$aTotal[5]+=$aResult['nTotalmatnacional'];
							$aTotal[6]+=$aResult['nTotalmatimportado'];
							$aTotal[7]+=$aResult['nTotalespecialista'];
							$aTotal[8]+=$aResult['nTotalnoespecialista'];
							$aTotal[9]+=$aResult['nTotalmaqnacional'];
							$aTotal[10]+=$aResult['nTotalmaqimportado'];
							$aTotal[11]+=$aResult['nTotaltotal'];

							print '</tr>';


						}
					}
				}
				if ($aGroup[$k])
				{
					$aTer = $aGroup[$k];
					foreach ($aTer AS $l => $grupo3)
					{
						//echo '<hr>ter '.$l;
						//echo ' label '.$aLine[$l]->label;

						if($grupo3)
						{
							print '<tr>';
							print '<td>'.$aLine[$l]->ref.'</td>';
							print '<td>'.$aLine[$l]->label.'</td>';

						}
						else
						{
							$res = $objectdetadd->fetch(0,$l);
							if($res>0)
							{
								print '<td>'.$objectdetadd->fk_unit.'</td>';
								print '<td>'.$objectdetadd->unit_budget.'</td>';
								//echo ' es un ITEM '.$objectdetadd->fk_unit.' cantidad '.$objectdetadd->unit_budget;
								$fk_budget_task=$objectdetadd->fk_budget_task;
								$filter = " AND t.fk_budget_task = ".$fk_budget_task;
								// object budget task resource
								$ressource = $objectbtr->fetchAll('ASC', 't.rowid', 0, 0, array(), 'AND',$filter);
								if($ressource>0)
								{
									$aResult = procesa($objectbtr);
									// material
									print '<td align="right">'.price(price2num($aResult['nTotalmatnacional'],'MT')).'</td>';
									print '<td align="right">'.price(price2num($aResult['nTotalmatimportado'],'MT')).'</td>';
									// mano de obra
									print '<td align="right">'.price(price2num($aResult['nTotalespecialista'],'MT')).'</td>';
									print '<td align="right">'.price(price2num($aResult['nTotalnoespecialista'],'MT')).'</td>';
									// maquinaria
									print '<td align="right">'.price(price2num($aResult['nTotalmaqnacional'],'MT')).'</td>';
									print '<td align="right">'.price(price2num($aResult['nTotalmaqimportado'],'MT')).'</td>';
									// total
									print '<td align="right">'.price(price2num($aResult['nTotaltotal'],'MT')).'</td>';

									$aTotalresult['nTotalmatnacional']+=$aResult['nTotalmatnacional'];
									$aTotalresult['nTotalmatimportado']+=$aResult['nTotalmatimportado'];
									$aTotalresult['nTotalespecialista']+=$aResult['nTotalespecialista'];
									$aTotalresult['nTotalnoespecialista']+=$aResult['nTotalnoespecialista'];
									$aTotalresult['nTotalmaqnacional']+=$aResult['nTotalmaqnacional'];
									$aTotalresult['nTotalmaqimportado']+=$aResult['nTotalmaqimportado'];

							$aTotal[5]+=$aResult['nTotalmatnacional'];
							$aTotal[6]+=$aResult['nTotalmatimportado'];
							$aTotal[7]+=$aResult['nTotalespecialista'];
							$aTotal[8]+=$aResult['nTotalnoespecialista'];
							$aTotal[9]+=$aResult['nTotalmaqnacional'];
							$aTotal[10]+=$aResult['nTotalmaqimportado'];
							$aTotal[11]+=$aResult['nTotaltotal'];
									print '</tr>';
								}
							}
						}
						print '</tr>';
						if ($aGroup[$l])
						{
							$aQua = $aGroup[$l];
							foreach ($aQua AS $z => $grupo3)
							{
								//echo '<hr>ter '.$l;
								//echo ' label '.$aLine[$l]->label;
								print '<tr>';
								print '<td>'.$aLine[$l]->ref.'</td>';
								print '<td>'.$aLine[$l]->label.'</td>';
								if($grupo3) echo ' es grupo';
								else
								{
									$res = $objectdetadd->fetch(0,$z);
									if($res>0)
									{
										print '<td>'.$objectdetadd->fk_unit.'</td>';
										print '<td>'.$objectdetadd->unit_budget.'</td>';
										//echo ' es un ITEM '.$objectdetadd->fk_unit.' cantidad '.$objectdetadd->unit_budget;
										$fk_budget_task=$objectdetadd->fk_budget_task;
										$filter = " AND t.fk_budget_task = ".$fk_budget_task;
										// object budget task resource
										$ressource = $objectbtr->fetchAll('ASC', 't.rowid', 0, 0, array(), 'AND',$filter);
										if($ressource>0)
										{
											$aResult = procesa($objectbtr);
											// material
											print '<td align="right">'.price(price2num($aResult['nTotalmatnacional'],'MT')).'</td>';
											print '<td align="right">'.price(price2num($aResult['nTotalmatimportado'],'MT')).'</td>';
											// mano de obra
											print '<td align="right">'.price(price2num($aResult['nTotalespecialista'],'MT')).'</td>';
											print '<td align="right">'.price(price2num($aResult['nTotalnoespecialista'],'MT')).'</td>';
											// maquinaria
											print '<td align="right">'.price(price2num($aResult['nTotalmaqnacional'],'MT')).'</td>';
											print '<td align="right">'.price(price2num($aResult['nTotalmaqimportado'],'MT')).'</td>';
											// total
											print '<td align="right">'.price(price2num($aResult['nTotaltotal'],'MT')).'</td>';
											$aTotalresult['nTotalmatnacional']+=$aResult['nTotalmatnacional'];
											$aTotalresult['nTotalmatimportado']+=$aResult['nTotalmatimportado'];
											$aTotalresult['nTotalespecialista']+=$aResult['nTotalespecialista'];
											$aTotalresult['nTotalnoespecialista']+=$aResult['nTotalnoespecialista'];
											$aTotalresult['nTotalmaqnacional']+=$aResult['nTotalmaqnacional'];
											$aTotalresult['nTotalmaqimportado']+=$aResult['nTotalmaqimportado'];

							$aTotal[5]+=$aResult['nTotalmatnacional'];
							$aTotal[6]+=$aResult['nTotalmatimportado'];
							$aTotal[7]+=$aResult['nTotalespecialista'];
							$aTotal[8]+=$aResult['nTotalnoespecialista'];
							$aTotal[9]+=$aResult['nTotalmaqnacional'];
							$aTotal[10]+=$aResult['nTotalmaqimportado'];
							$aTotal[11]+=$aResult['nTotaltotal'];
											print '</tr>';
										}
									}
								}

								////
								if ($aGroup[$z])
								{
									$aQuinto = $aGroup[$z];
									foreach ($aQua AS $x => $grupo3)
									{
										//echo '<hr>ter '.$l;
										//echo ' label '.$aLine[$l]->label;
										print '<tr>';
										print '<td>'.$aLine[$l]->ref.'</td>';
										print '<td>'.$aLine[$l]->label.'</td>';

										if($grupo3) echo ' es grupo';
										else
										{
											$res = $objectdetadd->fetch(0,$x);
											if($res>0)
											{
												print '<td>'.$objectdetadd->fk_unit.'</td>';
												print '<td>'.$objectdetadd->unit_budget.'</td>';
												//echo ' es un ITEM '.$objectdetadd->fk_unit.' cantidad '.$objectdetadd->unit_budget;
												$fk_budget_task=$objectdetadd->fk_budget_task;
												$filter = " AND t.fk_budget_task = ".$fk_budget_task;
												// object budget task resource
												$ressource = $objectbtr->fetchAll('ASC', 't.rowid', 0, 0, array(), 'AND',$filter);
												if($ressource>0)
												{
													$aResult = procesa($objectbtr);
													// material
													print '<td align="right">'.price(price2num($aResult['nTotalmatnacional'],'MT')).'</td>';
													print '<td align="right">'.price(price2num($aResult['nTotalmatimportado'],'MT')).'</td>';
													// mano de obra
													print '<td align="right">'.price(price2num($aResult['nTotalespecialista'],'MT')).'</td>';
													print '<td align="right">'.price(price2num($aResult['nTotalnoespecialista'],'MT')).'</td>';
													// maquinaria
													print '<td align="right">'.price(price2num($aResult['nTotalmaqnacional'],'MT')).'</td>';
													print '<td align="right">'.price(price2num($aResult['nTotalmaqimportado'],'MT')).'</td>';
													// total
													print '<td align="right">'.price(price2num($aResult['nTotaltotal'],'MT')).'</td>';

													$aTotalresult['nTotalmatnacional']+=$aResult['nTotalmatnacional'];
													$aTotalresult['nTotalmatimportado']+=$aResult['nTotalmatimportado'];
													$aTotalresult['nTotalespecialista']+=$aResult['nTotalespecialista'];
													$aTotalresult['nTotalnoespecialista']+=$aResult['nTotalnoespecialista'];
													$aTotalresult['nTotalmaqnacional']+=$aResult['nTotalmaqnacional'];
													$aTotalresult['nTotalmaqimportado']+=$aResult['nTotalmaqimportado'];

							$aTotal[5]+=$aResult['nTotalmatnacional'];
							$aTotal[6]+=$aResult['nTotalmatimportado'];
							$aTotal[7]+=$aResult['nTotalespecialista'];
							$aTotal[8]+=$aResult['nTotalnoespecialista'];
							$aTotal[9]+=$aResult['nTotalmaqnacional'];
							$aTotal[10]+=$aResult['nTotalmaqimportado'];
							$aTotal[11]+=$aResult['nTotaltotal'];
													print '</tr>';

												}
											}
										}

									}
								}

							}
						}
					}

				}
			}

		}
	}
	//totales
	print '<tr>';
	print '<td>'."TOTALES".'</td>';
	print '<td>'."".'</td>';
	print '<td>'."".'</td>';
	print '<td>'."".'</td>';

	for($a=5; $a<=11;$a++)
	{
		print '<td align="right">'.price(price2num($aTotal[$a],'MT')).'</td>';
	}
	print '</tr>';


	print '</table>';
}
//print '</form>';
function procesa(&$objectbtr)
{
	global $db, $conf,$langs;
	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/productbudgetext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
	//dol_include_once('/categories/class/categorie.class.php');
	//dol_include_once('/budget/class/productbudgetext.class.php');
	//dol_include_once('/product/class/product.class.php');
	$product = new Product($db);
	$objprodb = new Productbudgetext($db);
	$objCategorie = new Categorie($db);
	$aResult=array();
	//variables
	$nImportadomat=0;
	$nNacionalmat=0;
	$nImportadomaquinaria=0;
	$nNacionalmaquinaria=0;
	$nContadorma=0;
	$nContadormo=0;
	$nContadormq=0;
	$nProductivo=0;
	$nInproductivo=0;
	$nEsp=0;
	$nNoesp=0;
	$nTotaltotal=0;
	$nTotalmatnacional=0;
	$nTotalmatimportado=0;
	$lines = $objectbtr->lines;
	foreach ($lines AS $j => $line)
	{
		$fk_product_budget=$line->fk_product_budget;
		// object product budget
		$respb = $objprodb->fetch($fk_product_budget);
		//echo '<hr>'. $line->quant.'*'.$line->percent_prod.'*'.$line->amount.' '.$line->amount_noprod.' percentOrigin '.$objprodb->percent_origin;
		$nProductivo=0;
		$nImproductivo=0;
		// productivo
		$nProductivo=($line->quant*$line->percent_prod*$line->amount)/100;
		// No productivo
		$nImproductivo=($line->quant*(100-$line->percent_prod)*$line->amount_noprod)/100;
		if($objprodb->group_structure=='MA')
			$nTotalma+= $nProductivo+$nImproductivo;
		if($objprodb->group_structure=='MO')
			$nTotalmo+= $nProductivo+$nImproductivo;
		if($objprodb->group_structure=='MQ')
			$nTotalmq+= $nProductivo+$nImproductivo;
		// material
		if($objprodb->group_structure=='MA')
		{
			$fk_product=$objprodb->fk_product;
			$resproduct = $product->fetch($fk_product);
			if($product->country_id==$object->fk_country)
			{
				$nNacionalmat+=$objprodb->percent_origin;
			}
			else
			{
				$nImportadomat+=$objprodb->percent_origin;
			}
			$nContadorma++;
		}
		elseif($objprodb->group_structure=='MO')
		{
			//buscar en que categorias se encuentra
			$fk_product=$objprodb->fk_product;
			$aCat=$objCategorie->containing($fk_product,'product','id');
			if(is_array($aCat)&&count($aCat)>1)
			{
				$nEsp+=$objprodb->percent_origin;
			}
			else
			{
				$nNoesp+=$objprodb->percent_origin;
			}
			$nContadormo++;
		}
		elseif($objprodb->group_structure=='MQ')
		{
			//echo '0';
			$fk_product=$objprodb->fk_product;
			$resproduct = $product->fetch($fk_product);
			//echo '<hr>validacountry '.$product->country_id .'=='. $object->fk_country;
			if($product->country_id == $object->fk_country)
			{
				$nNacionalmaquinaria+=$objprodb->percent_origin;
			}
			else
			{
				//print_r($objprodb->percent_origin);
				$nImportadomaquinaria+=$objprodb->percent_origin;
			}
			$nContadormq++;
		}
	}
	if($nContadorma>0)
	{
		$nMaterialnacional=$nNacionalmat/$nContadorma;
		$nMaterialimportado=$nImportadomat/$nContadorma;

		$nTotalmatnacional=($nMaterialnacional*$nTotalma)/100;
		$nTotalmatimportado=($nMaterialimportado*$nTotalma)/100;
		$nTotaltotal+=$nTotalmatnacional;
		$nTotaltotal+=$nTotalmatimportado;
	}
	else
	{
		$nTotalmatnacional=0;
		$nTotalmatimportado=0;
	}
		// mano de obra
	if($nContadormo>0)
	{
		$nEspecialista=$nEsp/$nContadormo;
		$nNoespecialista=$nNoesp/$nContadormo;

		$nTotalespecialista=($nEspecialista*$nTotalmo)/100;
		$nTotalnoespecialista=($nNoespecialista*$nTotalmo)/100;
		$nTotaltotal+=$nTotalespecialista;
		$nTotaltotal+=$nTotalnoespecialista;


	}
	else
	{
		$nTotalespecialista=0;
		$nTotalnoespecialista=0;
	}
		// maquinaria
	if($nContadormq>0)
	{
		$nMaquinarianacional=$nNacionalmaquinaria/$nContadormq;
		$nMaquinariaimportado=$nImportadomaquinaria/$nContadormq;
		$nTotalmaqnacional=($nTotalmq*$nMaquinarianacional)/100;
		$nTotalmaqimportado=($nTotalmq*$nMaquinariaimportado)/100;

		$nTotaltotal+=$nTotalmaqnacional;
		$nTotaltotal+=$nTotalmaqimportado;


	}
	else
	{
		$nTotalmaqnacional=0;
		$nTotalmaqimportado=0;
	}



	/*

	print '<td align="right">'.price(price2num($nTotalmatimportado,'MT')).'</td>';
	print '<td align="right">'.price(price2num($nTotalmatnacional,'MT')).'</td>';
							// mano de obra
	print '<td align="right">'.price(price2num($nTotalespecialista,'MT')).'</td>';
	print '<td align="right">'.price(price2num($nTotalnoespecialista,'MT')).'</td>';
							// maquinaria
	print '<td align="right">'.price(price2num($nTotalmaqimportado,'MT')).'</td>';
	print '<td align="right">'.price(price2num($nTotalmaqnacional,'MT')).'</td>';
							// total
	print '<td align="right">'.price(price2num($nTotaltotal,'MT')).'</td>';

	*/
	$aResult['nTotalmatnacional'] = $nTotalmatnacional;
	$aResult['nTotalmatimportado'] = $nTotalmatimportado;
	$aResult['nTotalespecialista'] = $nTotalespecialista;
	$aResult['nTotalnoespecialista'] = $nTotalnoespecialista;
	$aResult['nTotalmaqnacional'] = $nTotalmaqnacional;
	$aResult['nTotalmaqimportado'] = $nTotalmaqimportado;

	$aResult['nTotaltotal'] = $nTotaltotal;

	return $aResult;
}

?>