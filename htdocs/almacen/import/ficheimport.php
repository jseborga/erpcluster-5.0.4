<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/salary/upload/fiche.php
 *	\ingroup    salary subida archivos
 *	\brief      Page fiche upload
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';


require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/mouvementstockext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementaddext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementdocext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtype.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/ctypemouvement.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/cunitsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/transf.class.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelationext.class.php");

require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/productext.class.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

//excel para una versión anterior
$file = DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
$ver = 0;
if (file_exists($file))
{
	$ver = 1;
	require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
}
$file = DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';
if (file_exists($file))
	include_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel/IOFactory.php';

//excel para version 4 o sup
$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
if (file_exists($file))
{
	$ver = 2;
	require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel.php';
}
$file = DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
if (file_exists($file))
	include_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';


$langs->load("almacen");
$langs->load("members");

$action=GETPOST('action');

$id         = GETPOST("rowid");
$rid        = GETPOST("rid");
$fk_period  = GETPOST("fk_period");
$fk_concept = GETPOST("fk_concept");
$fk_entrepot = GETPOST("fk_entrepot");
$docum      = GETPOST('docum');
$seldate    = GETPOST('seldate');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
$selrow = GETPOST('selrow');
$cancel = GETPOST('cancel');
$mesg = '';

$objUser  = new User($db);

$aDatef = array('dd/mm/yyyy',
	'dd-mm-yyyy',
	'mm/dd/yyyy',
	'mm-dd-yyyy',
	'yyyy/mm/dd',
	'yyyy-mm-dd');
$aCampodate = array('date_commande' =>'date_commande',
	'date_livraison' => 'date_livraison');

$objEntrepotrel = new Entrepotrelationext($db);
$object = new Mouvementstockext($db);
$objectadd = new Stockmouvementadd($db);
$objectdoc = new Stockmouvementdocext($db);
$objectdoctmp = new Stockmouvementdocext($db);
$objProduct = new Productext($db);
$objEntrepot = new Entrepot($db);
$objType = new Ctypemouvement($db);
$objUnit = new Cunitsext($db);
$objTransf = new transf($db);
$transf = new transf($db);
$objCateg = new Categorie($db);
$objSmt = new Stockmouvementtype($db);

$aDate = dol_getdate(dol_now());

//params docum
/*
 1 = Id
 2 = Login
 3 = Docum
*/

 $aMonth = array(2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11,13=>12);
 $aHeader = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W');
 $aCampo = array(1=>'refstock',2=>'refentrepot',3=>'type_mov',4=>'datem',5=>'refdepartament',6=>'refsociete',7=>'ref_ext',8=>'label',9=>'refproduct',10=>'labelproduct',11=>'unit',12=>'qty',13=>'price');

 $aCampo = array(1=>'id',2=>'grupo',3=>'codigo',4=>'partida',5=>'material',6=>'medida',7=>'movimiento',8=>'documento',9=>'fecha',10=>'referencia',11=>'ingreso',12=>'salida',13=>'saldofisico',14=>'preciounitario',15=>'debe',16=>'haber',17=>'saldovalor',18=>'saldofisico1',19=>'saldovalor1',20=>'momento',21=>'comentario',22=>'fk_product');
 $typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
 //$aTypemov = array('INV. INICIO'=>'ent_004','INGRESO'=>'ent_001','SALIDA'=>'sal_001');

 $aTypemov['INV. INICIO'] = getpost('code_inv');
 $aTypemov['INGRESO'] = getpost('code_ing');
 $aTypemov['SALIDA'] = getpost('code_sal');


/*
 * Actions
 */

// AddSave
if ($action == 'add' && GETPOST('save') == $langs->trans('Save'))
{
	/*verificamos los tipos*/
	$error = 0;

	if (empty($aTypemov['INV. INICIO']))
	{
		$error++;
		setEventMessages($langs->trans('No esta definido el tipo de movimiento para inventario inicial'),null,'errors');
	}
	if (empty($aTypemov['INGRESO']))
	{
		$error++;
		setEventMessages($langs->trans('No esta definido el tipo de movimiento para ingresos'),null,'errors');
	}
	if (empty($aTypemov['SALIDA']))
	{
		$error++;
		setEventMessages($langs->trans('No esta definido el tipo de movimiento para salidas'),null,'errors');
	}
	$aArrData   = unserialize($_SESSION['importmov']);
	$llaveid = '';
	$llaveref = '';
	$lEntity = false;
	$fk_entrepot = GETPOST('fk_entrepot');
	foreach ($_POST AS $i => $value)
	{
		$aPost = explode('_',$i);
		if ($aPost[0] == 'fkcampo')
		{
			$_POST['campo'][$aPost[1]] = $aCampo[$value];
			if (trim($aCampo[$value]) == 'rowid') $llaveid = $aPost[1];
			if (trim($aCampo[$value]) == 'ref') $llaveref = $aPost[1];
			if (trim($aCampo[$value]) == 'entity') $lEntity = true;

		}
	}
	if (!$error)
	{
		$db->begin();
		$objEntrepot->fetch($fk_entrepot);
		foreach ((array) $aArrData AS $datestring => $datatype)
		{
			$aDate = dol_getdate($datestring);
			//$date = dol_stringtotime($datestring.' '.$aDate['hours'].':'.$aDate['minutes'].':'.$aDate['seconds']);
			$date = $datestring;
			//echo '<hr>date '.$date.' '.dol_print_date($date,'dayhour');
			ksort($datatype);
			foreach ($datatype AS $type => $datadoc)
			{
				$fk_type_mov = 0;
				$model_pdf = 'inputalm';
			//buscamos la numeracion para la transferencia
				$objectdoc = new Stockmouvementdocext($db);
				$numref = $objectdoc->getNextNumRef($soc);
			//creamos el registro principal
				$objectdoc->ref = $numref;
				$objectdoc->ref_ext = $type;
				$objectdoc->entity = $conf->entity;
				$objectdoc->fk_entrepot_from = $idr+0;
				$objectdoc->fk_entrepot_to = $idd+0;
				$objectdoc->fk_type_mov = $fk_type_mov+0;
				$objectdoc->datem = $date;
				$objectdoc->label = $type;
				$objectdoc->date_create = $date;
				$objectdoc->date_mod = $date;
				$objectdoc->datem = $date;
				$objectdoc->tms = dol_now();
				$objectdoc->model_pdf = 'inputalm';
				$objectdoc->fk_user_create = $user->id;
				$objectdoc->fk_user_mod = $user->id;
				$objectdoc->statut = 1;
				$iddoc = $objectdoc->create($user);
			//echo ' => numref '.$numref;
				if ($iddoc <=0)
				{
					setEventMessages($objectdoc->error,$objectdoc->errors,'errors');
					$error=11;
					echo '<hr>errdoc '.$error;
					exit;
				}
				ksort($datadoc);
				foreach ($datadoc AS $doc => $datarow)
				{
					foreach ($datarow AS $j => $data)
					{
						//echo ' id '.$data['id'];
						$fk_product = $data['fk_product'];
						if (empty($fk_product))
						{
							$objProduct->ref         = trim($data['codigo']);
							$objProduct->fk_parent   = 0;
							$objProduct->libelle     = $data['material'];
							$objProduct->label     = $data['material'];
							$objProduct->description = $data['material'];
							$objProduct->statut      = 1;
							$objProduct->lieu        = $data['material'];
							$objProduct->address     = '';
							$objProduct->zip         = '';
							$objProduct->town        = '';
							$objProduct->country_id  = 52;
							$objProduct->status = 1;
							$objProduct->status_buy = 1;
							$objProduct->cost_price = $data['preciounitario'];
							$objProduct->fk_unit = $data['fk_unit'];

							if (! empty($objProduct->libelle))
							{
								$fk_product = $objProduct->create($user,1);
								if ($fk_product <=0)
								{
									$error=901;
									setEventMessages($objProduct->error, $objProduct->errors,'mesgs');
								}
							}
						}
						//verificamos la categoria
						$newobject = new Product($db);
						$result = $newobject->fetch($fk_product);
						$elementtype = 'product';
						//buscamos la categoria
						$rescat = $objCateg->fetch(0,$data['grupo'],0);
						if ($rescat>0)
						{
							$rescat = $objCateg->containsObject('product', $fk_product );
							if ($rescat==0)
							{
								// TODO Add into categ
								$result=$objCateg->add_type($newobject,$elementtype);
								if ($result >= 0)
								{
									setEventMessages($langs->trans("WasAddedSuccessfully",$newobject->ref), null, 'mesgs');
								}
								else
								{
									$error=9001;
									setEventMessages($langs->trans("No se agrego la categoria ",$newobject->ref), null, 'mesgs');
								}

							}
						}
						//fin cateogria

						$price = $data['preciounitario'];
						$label = 'id='.$data['id'].' doc= '.$data['documento'].($data['referencia']?' '.$langs->trans('Referencia').' '.$data['referencia']:'').($data['momento']?' '.$langs->trans('Momento').' '.$data['momento']:'');
						if ($type == 'SALIDA')
						{
							//echo '<br> procesa SALIDA ';
							$restype = $objType->fetch(0,$aTypemov[$type]);

							if ($restype>0)
							{
								$fk_type_mov = $objType->id;
								$model_pdf = 'outputalm';
							}
							$movement = 1;
							$nbpiece = $data['salida'];
							if (empty($npiece))
							{
							//setEventMessages($langs->trans('La salida no contiene un valor para el producto ').$newobject->ref.' '.$newobject->label,null,'errors');
								//continue;
							}
							$product = new Product($db);
							$result=$product->fetch($data['fk_product']);
							//$transf = new Transf($db);
							//$resultn=$transf->fetch($object->fk_product);
							$product->load_stock();
							$pricesrc=0;
							if (isset($product->stock_warehouse[$fk_entrepot]->pmp))
								$pricesrc=$product->stock_warehouse[$fk_entrepot]->pmp;
							if (empty($pricesrc))
								$pricesrc = $product->pmp;

							$pricedest=$pricesrc;

							$aSales = array();
						//valuacion por el metodo peps
							$objMouvement = new MouvementStockext($db);
						//$date = dol_now();
							$resmov = $objMouvement->get_value_product($fk_entrepot,$date,$data['fk_product'],$nbpiece,$typemethod,$pricesrc,$product);
							if ($resmov <= 0)
							{
								$error=1001;
								setEventMessages($langs->trans('Error en obtener movimiento').' nro. error '.$error.' '.$langs->trans('Fila').' '.$data['id'],null,'errors');

							}
							$aSales = $objMouvement->aSales;
						//echo '<hr>opcion1 ';
						//print_r($aSales);
							foreach ((array) $aSales AS $fk_stock => $row)
							{
								//$transf->origin = 'stockmouvementtemp';
								//$transf->originid = $idreg;
								$transf->id = $fk_product;

								// Add stock
								$qty = $row['qty'];
								$result2=$transf->add_transfer_ok($user,$fk_entrepot,$row['qty'],$movement,$label,$pricedest,$idreg,$fk_type_mov,$date);
								if ($result2 <= 0)
								{
									$error=101;

									//print $langs->trans('Error en obtener movimiento').' nro. error '.$error.' '.$langs->trans('Fila').' '.$data['id'];
									setEventMessages($langs->trans('Error en obtener movimiento').' nro. error '.$error.' '.$langs->trans('Fila').' '.$data['id'],null, 'errors');
									//exit;

								}
								if (!$error)
								{
									//buscamos y actualizamos el product_stock
									$sql = "SELECT rowid, reel FROM ".MAIN_DB_PREFIX."product_stock";
									$sql.= " WHERE fk_entrepot = ".$fk_entrepot." AND fk_product = ".$fk_product;
									$resql=$db->query($sql);
									$alreadyarecord = 0;
									if ($resql)
									{
										$objx = $db->fetch_object($resql);
										if ($objx)
										{
											$alreadyarecord = 1;
											$oldqtywarehouse = $objx->reel;
											$fk_product_stock = $objx->rowid;
										}
										$db->free($resql);
									}
									else
									{
										$error = -2;
									}
									if (! $error)
									{
										if ($alreadyarecord > 0)
										{
											$sql = "UPDATE ".MAIN_DB_PREFIX."product_stock SET reel = reel + ".$qty;
											$sql.= " WHERE fk_entrepot = ".$fk_entrepot." AND fk_product = ".$fk_product;
										}
										else
										{
											$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_stock";
											$sql.= " (reel, fk_entrepot, fk_product) VALUES ";
											$sql.= " (".$qty.", ".$fk_entrepot.", ".$fk_product.")";
										}
										$resql=$db->query($sql);
										if (! $resql)
										{
											$error = -3;
										}
									}
								}
								$fk_entrepot_to = $fk_entrepot;
								$aIdsdes[$result2] = $result2;

								//buscamos y actualizamos registro en stock_mouvement_add
								$resadd = $objectadd->fetch(0,$result2);
								if ($resadd==0)
								{
									//echo '<hr>xcreaing '.$result2;
									$now = dol_now();
									$objectadd->fk_stock_mouvement = $result2;
									$objectadd->fk_stock_mouvement_doc = $iddoc;
									$objectadd->period_year = $_SESSION['period_year']+0;
									$objectadd->month_year = $_SESSION['period_month']+0;
									$objectadd->fk_facture = 0;
									$objectadd->fk_user_create = $user->id;
									$objectadd->fk_user_mod = $user->id;
									$objectadd->fk_parent_line = $row['id']+0;
									$objectadd->qty = 0;
									$objectadd->date_create = $now;
									$objectadd->date_mod = $now;
									$objectadd->tms = $now;
									$objectadd->balance_peps = 0;
									$objectadd->balance_ueps = 0;
									$objectadd->value_peps = $row['value'];
									$objectadd->value_ueps = 0;
									$objectadd->value_peps_adq = $row['value'];
									$objectadd->value_ueps_adq = 0;
									$objectadd->status = 1;
									$resadd = $objectadd->create($user);
									if ($resadd <=0)
									{
										$error=102;
										setEventMessages($objectadd->error.' '.$error,$objectadd->errors,'errors');


									//exit;
									}
								}
								elseif($resadd==1)
								{
									//echo '<hr>actualizqaing '.$objectadd->id;
									$now = dol_now();
									$objectadd->fk_user_mod = $user->id;
									$objectadd->period_year = $_SESSION['period_year']+0;
									$objectadd->month_year = $_SESSION['period_month']+0;
									$objectadd->date_mod = $now;
									$objectadd->tms = $now;
									$objectadd->fk_parent_line = $row['id']+0;
									$objectadd->qty = 0;
									$objectadd->balance_peps = 0;
									$objectadd->balance_ueps = 0;
									$objectadd->value_peps = $row['value'];
									$objectadd->value_ueps = 0;
									$objectadd->value_peps_adq = $row['value'];
									$objectadd->value_ueps_adq = 0;
									$objectadd->status = 1;
									$resadd = $objectadd->update($user);
									if ($resadd<=0)
									{
										$error=103;
										setEventMessages($objectadd->error.' '.$error,$objectadd->errors,'errors');
									//exit;
									}
								}

							//creamos registro en stock_mouvement_type
								$objSmt->fk_stock_mouvement = $result2;
								$objSmt->fk_type_mouvement = $fk_type_mov;
								$objSmt->tms = dol_now();
								$objSmt->statut = 1;
								$resmt = $objSmt->create($user);
								if ($resmt <= 0)
								{
									$error=104;
								//exit;
									setEventMessages($objsmt->error.' '.$error,$objsmt->errors,'errors');
								}
							}
						//echo '<br>nroerroresALIDA '.$error;
							if ($error>0)
							{
							//echo '<hr>errorsal '.$error;
							//	exit;
							}
						}

						if ($type == 'INGRESO')
						{
							//ECHO '<br> INGRESO';
							if ($doc == 'INV. INICIO')
								$restype = $objType->fetch(0,$aTypemov['INV. INICIO']);
							else
								$restype = $objType->fetch(0,$aTypemov[$type]);
							if ($restype>0)
							{
								$fk_type_mov = $objType->id;
								$mode_pdf = 'inputalm';
							}

							$movement = 0;
							$nbpiece = $data['ingreso'];
							if (empty($nbpiece))
							{
								setEventMessages($langs->trans('El ingreso no contiene un valor para el producto ').$newobject->ref.' '.$newobject->label,null,'errors');
							}

						//MOVIMIENTO DE INGRESOS
							$priceppp = $price;
							$price_peps=$price;
							$price_ueps=$price;

							$balance_peps = $nbpiece;
							$balance_ueps = $nbpiece;

						// Add stock
							$transf->id = $fk_product;
							$qty = $nbpiece;
							$result2=$transf->add_transfer_ok($user,$fk_entrepot,$nbpiece,$movement,$label,$priceppp,$idreg,$fk_type_mov,$date);
							if ($result2 <= 0)
							{
							//echo '<hr>fkprod '.$fk_product;
							//echo '<br>prod '.$newobject->ref.' '.$newobject->label;
							//echo '<hr>'.$result2;
								$error=207;
								setEventMessages($error.' |'.$nbpiece.'|',$transf->errors, 'errors');
							}
							if (!$error)
							{
									//buscamos y actualizamos el product_stock
								$sql = "SELECT rowid, reel FROM ".MAIN_DB_PREFIX."product_stock";
								$sql.= " WHERE fk_entrepot = ".$fk_entrepot." AND fk_product = ".$fk_product;
								$resql=$db->query($sql);
								$alreadyarecord = 0;
								if ($resql)
								{
									$objx = $db->fetch_object($resql);
									if ($objx)
									{
										$alreadyarecord = 1;
										$oldqtywarehouse = $objx->reel;
										$fk_product_stock = $objx->rowid;
									}
									$db->free($resql);
								}
								else
								{
									$error = -2;
								}
								if (! $error)
								{
									if ($alreadyarecord > 0)
									{
										$sql = "UPDATE ".MAIN_DB_PREFIX."product_stock SET reel = reel + ".$qty;
										$sql.= " WHERE fk_entrepot = ".$fk_entrepot." AND fk_product = ".$fk_product;
									}
									else
									{
										$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_stock";
										$sql.= " (reel, fk_entrepot, fk_product) VALUES ";
										$sql.= " (".$qty.", ".$fk_entrepot.", ".$fk_product.")";
									}
									$resql=$db->query($sql);
									if (! $resql)
									{
										$error = -3;
									}
								}
							}



							$fk_entrepot_to = $fk_entrepot;
							$aIdsdes[$result2] = $result2;

							//buscamos y actualizamos registro en stock_mouvement_add
							$resadd = $objectadd->fetch(0,$result2);
							if ($resadd==0)
							{
								//echo '<hr>quees '.$result2;
								$now = dol_now();
								$objectadd->fk_stock_mouvement = $result2;
								$objectadd->fk_stock_mouvement_doc = $iddoc;
								$objectadd->period_year = $_SESSION['period_year']+0;
								$objectadd->month_year = $_SESSION['period_month']+0;
								$objectadd->fk_facture = 0;
								$objectadd->fk_user_create = $user->id;
								$objectadd->fk_user_mod = $user->id;
								$objectadd->fk_parent_line = 0;
								$objectadd->date_create = $date;
								$objectadd->date_mod = $date;
								$objectadd->tms = $now;
								$objectadd->qty = $balance_peps;
								$objectadd->balance_peps = $balance_peps;
								$objectadd->balance_ueps = $balance_ueps;
								$objectadd->value_peps = $price_peps;
								$objectadd->value_ueps = $price_ueps;
								$objectadd->value_peps_adq = $price_peps;
								$objectadd->value_ueps_adq = $price_ueps;
								$objectadd->status = 1;
								$resadd = $objectadd->create($user);
								if ($resadd <=0)
								{
									$error=2008;
									setEventMessages($objectadd->error.' '.$error,$objectadd->errors,'errors');

								//exit;
								}
							}
							elseif($resadd==1)
							{
								//echo '<hr>actualizaquees '.$objectadd->id;
								$now = dol_now();
								$objectadd->fk_user_mod = $user->id;
								$objectadd->period_year = $_SESSION['period_year']+0;
								$objectadd->month_year = $_SESSION['period_month']+0;
								$objectadd->date_mod = $date;
								//$objectadd->tms = $date;
								$objectadd->qty = $balance_peps;
								$objectadd->balance_peps = $balance_peps;
								$objectadd->balance_ueps = $balance_ueps;
								$objectadd->value_peps = $price_peps;
								$objectadd->value_ueps = $price_ueps;
								$objectadd->value_peps_adq = $price_peps;
								$objectadd->value_ueps_adq = $price_ueps;
								$objectadd->status = 1;
								$resadd = $objectadd->update($user);
								if ($resadd<=0)
								{
									$error=209;
									setEventMessages($objectadd->error.' '.$error.' linea '.$data['id'],$objectadd->errors,'errors');

								//exit;
								}
							}
							//echo '<br>erroringreso '.$error;
							if ($error>0)
							{
							//echo '<hr>salidaexit '.$error;
							//exit;
							}
						}
							//creamos registro en stock_mouvement_type
						$objSmt->fk_stock_mouvement = $result2;
						$objSmt->fk_type_mouvement = $objType->id;
						$objSmt->tms = dol_now();
						$objSmt->statut = 1;
						$resmt = $objSmt->create($user);
						if ($resmt <= 0)
						{
							$error=210;
							setEventMessages($langs->trans('No se puede crear el movimiento ').' error '.$error.' linea '.$data['id'],$objSmt->errors,'errors');
						}
					}
				}
				//actualizamos el fk_type_mov en stockmouvementdoc
				$restmp = $objectdoctmp->fetch($iddoc);
				if ($restmp == 1)
				{
					$objectdoctmp->fk_type_mov = $fk_type_mov+0;
					$objectdoctmp->model_pdf = $model_pdf;
					$objectdoctmp->ref_ext = $doc;
					$res = $objectdoctmp->update($user);
					if ($res <=0)
					{
						$error=301;
						setEventMessages($objectdoc->error.' '.$error,$objectdoc->errors,'errors');
					}
				}
				else
				{
					$error=302;
					setEventMessages($objectdoctmp->error.' '.$error,$objectdoctmp->errors,'errors');
				}
			}
		}
		if (!$error)
		{
			setEventMessages($langs->trans('Proceso satisfactorio de importación'),null,'mesgs');
			$db->commit();
			header('Location: '.$_SERVER['PHP_SELF']);
		}
		else
		{
			setEventMessages($langs->trans('La importación tiene errores, revise').' '.$error,null,'errors');
			$db->rollback();
		}
		$action = '';
	}
}






if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}
//campos principales tabla
$aHeaderTpl['llx_accounting_account'] = array('fk_pcg_version' => 'fk_pcg_version',
	'pcg_type' => 'pcg_type',
	'pcg_subtype' => 'pcg_subtype',
	'account_number'=>'account_number',
	'account_parent' => 'account_parent',
	'label'=>'label',
	'fk_accouting_category'=>'fk_accouting_category',
	);
$aHeaderTpl['llx_product'] = array('ref' => 'ref',
	'label' => 'label');
$aHeaderTpl['llx_categorie'] = array('label' => 'label',
	'description' => 'description',
	'code_parent' => 'code_parent');
$aHeaderTpl['llx_categorie_product'] = array('code_product' => 'code_product',
	'description' => 'description',
	'code_categorie' => 'code_categorie');
$aHeaderTpl['llx_commande'] = array('ref' => 'ref',
	'fk_soc' => 'fk_soc',
	'date_commande' => 'date_commande');
$aHeaderTpl['llx_commandedet'] = array('fk_commande' => 'fk_commande',
	'fk_product' => 'fk_product',
	'qty' => 'qty');

$aHeaderTpl['llx_c_departements'] = array('code_departement' => 'code_departement',
	'fk_region' => 'fk_region',
	'nom' => 'nom');

$aHeaderTpl['llx_c_partida'] = array('gestion'=>'gestion',
	'code'   => 'code',
	'label'  => 'label',
	'active' => 'active');
$aHeaderTpl['llx_poa_poa'] = array('gestion'=>'gestion',
	'fk_structure' =>'fk_structure',
	'ref' =>'ref',
	'label'=>'label',
	'pseudonym' =>'pseudonym',
	'partida'=>'partida',
	'amount'=>'amount',
	'classification'=>'classification',
	'source_verification'=>'source_verification',
	'unit'=>'unit',
	'responsible'=>'responsible',
	'm_jan'=>'m_jan',
	'm_feb'=>'m_feb',
	'm_mar'=>'m_mar',
	'm_apr'=>'m_apr',
	'm_may'=>'m_may',
	'm_jun'=>'m_jun',
	'm_jul'=>'m_jul',
	'm_aug'=>'m_aug',
	'm_sep'=>'m_sep',
	'm_oct'=>'m_oct',
	'm_nov'=>'m_nov',
	'm_dec'=>'m_dec',
	'p_jan'=>'p_jan',
	'p_feb'=>'p_feb',
	'p_mar'=>'p_mar',
	'p_apr'=>'p_apr',
	'p_may'=>'p_may',
	'p_jun'=>'p_jun',
	'p_jul'=>'p_jul',
	'p_aug'=>'p_aug',
	'p_sep'=>'p_sep',
	'p_oct'=>'p_oct',
	'p_nov'=>'p_nov',
	'p_dec'=>'p_dec',
	'fk_area'=>'fk_area',
	'weighting'=>'weighting',
	'fk_poa_reformulated'=>'fk_poa_reformulated',
	'version'=>'version',
	'statut'=>'statut',
	'statut_ref'=>'statut_ref',
	'active'=>'active',
	);

$aTable = array(
	'llx_accounting_account'=>'Accountingaccount',
	'llx_categorie'   => 'Category',
	'llx_product'     => 'Product',
	'llx_categorie_product' => 'Category product',
	'llx_commande'    => 'Pedidos',
	'llx_commandedet' => 'Pedidos productos',
	'llx_c_departements' => 'Departamentos/Provincias',
	'llx_c_partida'   => 'Partidas de Gasto',
	'llx_poa_poa'     => 'Poa');

//$action = "create";

/*
 * View
 */

$form=new Form($db);
$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Importmovements"),$help_url);

// Add
if ($action == 'edit')
{
	$table = GETPOST('table');
	$selrow = GETPOST('selrow');
	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];
	$tempdir = "tmp/";
	//compruebo si la extension es correcta
	if(move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
	{

		//  echo "file uploaded<br>";
	}
	else
	{
		echo 'no se puede mover';
		exit;
	}

	$objPHPExcel = new PHPExcel();
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	$objPHPExcel = $objReader->load('tmp/'.$nombre_archivo);

	//$objReader = new PHPExcel_Reader_Excel2007();
	$objReader->setReadDataOnly(true);
	//$objPHPExcel = $objReader->load('C:\xampp\htdocs\hotelplayagolfsitges\wp-content\tesipro.xlsx');
	$aCurren = array();

	$line=0;
	if ($selrow == 1)
	{
		$line++;
		if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeader[1].$line)->getFormattedValue()))
		{
			for ($a = 1; $a <= 22; $a++)
				$aCurrentitle[$line][$aHeader[$a]] = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getFormattedValue();
		}
	}
	$line++;
	$lLoop = true;
	while ($lLoop == true)
	{
		if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeader[2].$line)->getFormattedValue()))
		{
			for ($a = 1; $a <= 21; $a++)
			{
				if ($a == 9)
				{
					$aCurren[$line][$a] = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getValue()+1;
					$timestamp = PHPExcel_Shared_Date::ExcelToPHP($aCurren[$line][$a]);
					$aCurren[$line][$a] = $timestamp;
				}
				else
					$aCurren[$line][$a] = $objPHPExcel->getActiveSheet()->getCell($aHeader[$a].$line)->getFormattedValue();

			}
		}
		else $lLoop = false;
		$line++;
	}
	//corregimos si se encuentra una coma como separador de decimales
	//cabecera
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	foreach ((array) $aCurrentitle AS $j => $data)
	{
		for ($a = 1; $a <= 22; $a++)
			print '<th>'.$data[$aHeader[$a]].'</th>';
	}
	//print '<th align="right">'.$langs->trans('Total').'</th>';
	print '<tr>';
	//revisamos valores
	$i = 0;
	$j = 0;
	$refni = '';
	$aNewdata = array();

	foreach ($aCurren AS $line => $aData)
	{
		$var =!$var;
		print '<tr '.$bc[$var].'>';
		$aLines = array();
		for ($a = 1; $a <= 21; $a++)
		{
			if ($a == 9)
				print '<td>'.dol_print_date($aData[$a],'day').'</td>';
			else
				print '<td>'.$aData[$a].'</td>';
			$aLines[$a] = $aData[$a];
		}
		$aNewdata[] = $aLines;
		$j++;
		print '</tr>';

	}
	print '</table>';
	dol_fiche_end();
	//armamos un nuevo array segun agrupamiento
	foreach ((array) $aNewdata AS $j => $data)
	{
		$row = array();
		$lAddunit = false;
		$lAddproduct = false;
		foreach ($data AS $k => $value)
		{
			$row[$aCampo[$k]] = $value;
			if ($aCampo[$k] == 'codigo')
			{
				//buscamos el producto

				$res = $objProduct->fetch(0,dol_string_nospecial(trim($value)));
				if ($res == 1)
				{
					$row['fk_product'] = $objProduct->id;
					$fk_product = $objProduct->id;
					//buscamos y actualizamos el product_stock
					$sql = "SELECT rowid, reel FROM ".MAIN_DB_PREFIX."product_stock";
					$sql.= " WHERE fk_entrepot = ".$fk_entrepot." AND fk_product = ".$fk_product;
					$resql=$db->query($sql);
					$alreadyarecord = 0;
					if ($resql)
					{
						$objx = $db->fetch_object($resql);
						if ($objx)
						{
							$alreadyarecord = 1;
							$oldqtywarehouse = $objx->reel;
							$fk_product_stock = $objx->rowid;
						}
						//$db->free($resql);
					}
					else
					{
						$error = -2;
					}
					if (! $error)
					{
						if ($alreadyarecord <= 0)
						{
							$valcero = 0;
							$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_stock";
							$sql.= " (reel, fk_entrepot, fk_product) VALUES ";
							$sql.= " (".$valcero.", ".$fk_entrepot.", ".$fk_product.")";
						}
						$resql=$db->query($sql);
						if (! $resql)
						{
							$error = -3;
						}
					}
				}
				elseif($res == 0)
					$lAddproduct = true;
			}
			if ($aCampo[$k] == 'medida')
			{
				//buscamos la unidad
				$res = $objUnit->fetch(0,trim(strtoupper($value)));
				if ($res == 1)
				{
					$row['fk_unit'] = $objUnit->id;
				}
				elseif($res == 0)
					$lAddunit = true;
			}
		}
		if ($lAddunit)
		{
			$objUnit->code = $row['medida'];
			$objUnit->label = $row['medida'];
			$objUnit->short_label = $row['medida'];
			$objUnit->active = 1;
			$fk_unit = $objUnit->create($user);
			if ($fk_unit>0)
				$row['fk_unit'] = $fk_unit;
		}
		if ($lAddproduct)
		{
			$objProduct->ref         = trim($row['codigo']);
			$objProduct->fk_parent   = 0;
			$objProduct->libelle     = $row['material'];
			$objProduct->label     = $row['material'];
			$objProduct->description = $row['material'];
			$objProduct->statut      = 1;
			$objProduct->lieu        = $row['material'];
			$objProduct->address     = '';
			$objProduct->zip         = '';
			$objProduct->town        = GETPOST("town");
			$objProduct->country_id  = 52;
			$objProduct->status = 1;
			$objProduct->status_buy = 1;
			$objProduct->cost_price = $row['preciounitario'];
			$objProduct->fk_unit = $row['fk_unit'];

			if (! empty($objProduct->libelle))
			{
				$id = $objProduct->create($user,1);

				$fk_product = $id;
				//buscamos y actualizamos el product_stock
				$sql = "SELECT rowid, reel FROM ".MAIN_DB_PREFIX."product_stock";
				$sql.= " WHERE fk_entrepot = ".$fk_entrepot." AND fk_product = ".$fk_product;
				$resql=$db->query($sql);
				$alreadyarecord = 0;
				if ($resql)
				{
					$objx = $db->fetch_object($resql);
					if ($objx)
					{
						$alreadyarecord = 1;
						$oldqtywarehouse = $objx->reel;
						$fk_product_stock = $objx->rowid;
					}
					$db->free($resql);
				}
				else
				{
					$error = -2;
				}
				if (! $error)
				{
					if ($alreadyarecord <= 0)
					{
						$valcero = 0;
						$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_stock";
						$sql.= " (reel, fk_entrepot, fk_product) VALUES ";
						$sql.= " (".$valcero.", ".$fk_entrepot.", ".$fk_product.")";
					}
					$resql=$db->query($sql);
					if (! $resql)
					{
						$error = -3;
					}
				}


				if ($id > 0)
				{
					$row['fk_product'] = $id;
					//actualizamos valores
					$objProduct->fetch($id);
					if ($objProduct->id == $id)
					{
						$objProduct->status = 1;
						$objProduct->status_buy = 1;
						$objProduct->cost_price = $row['preciounitario'];
						$objProduct->fk_unit = $row['fk_unit'];
						$objProduct->update_add($row['fk_product'],$user,1);
					}
				}
				else
				{
					$row['erroprod'] = $id;
					$row['codigo'].' '.$row['material'];
					$row['erroprodmens'] = $objProduct->error;

				}

			}
		}
		else
		{
			if ($row['fk_product'])
			{
				//actualizamos valores
				$objProduct->fetch($row['fk_product']);
				//echo '<hr>valida '.$objProduct->id.' == '.$row['fk_product'].' && '.$objProduct->label.' != '.$row['material'];
				if (($objProduct->id == $row['fk_product'] && empty($objProduct->fk_unit)) || ($objProduct->id == $row['fk_product'] && trim($objProduct->label) != trim($row['material'])))
				{
					if ($objProduct->id == $row['fk_product'] && trim($objProduct->label) != trim($row['material']))
					{
						$objProduct->label = $row['material'];
					}
					$objProduct->status = 1;
					$objProduct->status_buy = 1;
					$objProduct->cost_price = $row['preciounitario'];
					$objProduct->fk_unit = $row['fk_unit'];
					$objProduct->update_add($row['fk_product'],$user,1);
				}
			}
		}
		//verificamos categoria
		$errorv = verifica_categoria($row['grupo'],$row['fk_product'], $db);
		if ($errorv<0)
			$error++;
		$aDateh = dol_getdate(dol_now());
		$aDate = explode('/',$data[9]);
		$date=dol_mktime($aDateh['hours'],$aDateh['minutes'],$aDateh['seconds'],$aDate[1],$aDate[0],$aDate[2],1);
		$date = $data[9];
		$aNew[$date][$data[7]][$data[8]][$j] = $row;
		//verificacion de cantidades
		if ($row['movimiento'] == 'INGRESO' && empty($row['ingreso']))
		{
			$error++;
			setEventMessages($langs->trans('Error, el ingreso no tiene cantidad en la fila').' '.$row['id'],null,'errors');
		}
		if ($row['movimiento'] == 'SALIDA' && empty($row['salida']))
			setEventMessages($langs->trans('Error, la salida no tiene cantidad en la fila').' '.$row['id'],null,'warnings');
	}
	ksort($aNew);

	if($abc)
	{
		print  '<table>';
		foreach ((array) $aNew AS $datestring => $datatype)
		{
			$aDate = dol_getdate($datestring);
			$date = $datestring;
			ksort($datatype);
			foreach ($datatype AS $doc => $datadoc)
			{
				ksort($datadoc);
				foreach ($datadoc AS $type => $datarow)
				{
					foreach ($datarow AS $j => $data)
					{
						print '<tr>';
						print '<td>'.$data['id'].'</td>';
						print '<td>'.$data['grupo'].'</td>';
						print '<td>'.$data['fk_product'].'</td>';
						print '<td>'.$data['codigo'].'</td>';
						print '<td>'.$data['partida'].'</td>';
						print '<td>'.$data['material'].'</td>';
						print '<td>'.$data['medida'].'</td>';
						print '<td>'.$data['fk_unit'].'</td>';
						print '<td>'.$data['movimiento'].'</td>';
						print '<td>'.$data['documento'].'</td>';
						print '<td>'.dol_print_date($data['fecha'],'day').'</td>';
						print '<td>'.$data['referencia'].'</td>';
						print '<td>'.$data['ingreso'].'</td>';
						print '<td>'.$data['salida'].'</td>';
						print '<td>'.$data['saldofisico'].'</td>';
						print '<td>'.$data['preciounitario'].'</td>';
						print '<td>'.$data['fk_product'].'</td>';
						print '</tr>';
					}
				}
			}
		}
		print '</table>';
	}
	echo '<br>Errores encontrados '.$error;

	if (!$error)
	{
		$_SESSION['importmov'] = serialize($aNew);
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="step" value="'.$stepnew.'">';
		print '<input type="hidden" name="fk_entrepot" value="'.$fk_entrepot.'">';
		print '<input type="hidden" name="code_inv" value="'.GETPOST('code_inv').'">';
		print '<input type="hidden" name="code_ing" value="'.GETPOST('code_ing').'">';
		print '<input type="hidden" name="code_sal" value="'.GETPOST('code_sal').'">';

		print '<center><br><input type="submit" class="butAction" name="save" value="'.$langs->trans("Save").'">';
		print '&nbsp;<input type="submit" class="butActionDelete" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
		print '</form>';
	}

	$c=0;
	//$action = "edit";
}
$res = $objType->fetchAll('ASC','label',0,0,array(1=>1),'AND');
if ($res > 0)
{
	foreach ($objType->lines AS $j => $line)
	{
		$optionstype.= '<option value="'.$line->code.'">'.$line->label.'</option>';
	}
}
if ($action == 'create' || empty($action))
{
	print_fiche_titre($langs->trans("Importmovements"));
	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">';

	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="edit">';

	dol_htmloutput_mesg($mesg);


	print '<table class="border" width="100%">';

	print '<tr><td>';
	print $langs->trans('Selectarchiv');
	print '</td>';
	print '<td>';
	print '<input type="file" name="archivo" size="40">';
	print '</td></tr>';

	// Entrepot Almacen
	print '<tr><td width="25%" >'.$langs->trans('Entrepot').'</td><td colspan="3">';
	print $objEntrepotrel->select_padre($fk_entrepot,'fk_entrepot',1,'',$filteruser);
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Tipo movimiento inventario inicial');
	print '</td>';
	print '<td>';
	print  '<select name="code_inv" required>'.$optionstype.'</select>';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Tipo movimiento ingresos (No inventario Inicial)');
	print '</td>';
	print '<td>';
	print  '<select name="code_ing" required>'.$optionstype.'</select>';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Tipo movimiento salidas');
	print '</td>';
	print '<td>';
	print  '<select name="code_sal" required>'.$optionstype.'</select>';
	print '</td></tr>';

	/*
	print '<tr><td>';
	print $langs->trans('Dateformat');
	print '</td>';
	print '<td>';
	print $form->selectarray('seldate',$aDatef,'',1);
	print '</td></tr>';
	*/
	/*
	print '<tr><td>';
	print $langs->trans('Campos date');
	print '</td>';
	print '<td>';
	print '<input type="text" name="camposdate" size="50">';
	print '</td></tr>';
	*/
	print '<tr><td>';
	print $langs->trans('Primera fila es titulo');
	print '</td>';
	print '<td>';
	print $form->selectyesno('selrow',(GETPOST('selrow')?GETPOST('selrow'):1),1);
	print '</td></tr>';
	/*
	print '<tr><td>';
	print $langs->trans('Separator');
	print '</td>';
	print '<td>';
	print '<input type="text" name="separator" size="2">';
	print '</td></tr>';
	*/
	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Upload").'"></center>';

	print '</form>';
}

llxFooter();
$db->close();

function convertdate($aDatef,$selvalue,$date)
{
	$sel = $aDatef[$selvalue];
	switch ($sel)
	{
		case 0:
		list($day,$mes,$anio) = explode('/',$date);
		break;
		case 0:
		list($day,$mes,$anio) = explode('-',$date);
		break;
		case 0:
		list($mes,$day,$anio) = explode('/',$date);
		break;
		case 0:
		list($mes,$day,$anio) = explode('-',$date);
		break;
		case 0:
		list($anio,$mes,$day) = explode('/',$date);
		break;
		case 0:
		list($anio,$mes,$day) = explode('-',$date);
		break;
	}
	$newdate = dol_mktime(12, 0, 0, $mes, $day, $anio);
	return $newdate;
}

function verifica_categoria($categorie,$fk_product, $db)
{
	global $langs,$conf;
	//verificamos la categoria
	$newobject = new Product($db);
	$objCateg = new Categorie($db);
	$result = $newobject->fetch($fk_product);
	$elementtype = 'product';
						//buscamos la categoria
	$rescat = $objCateg->fetch(0,$categorie,0);
	if ($rescat>0)
	{
		$rescat = $objCateg->containsObject('product', $fk_product );
		if ($rescat==0)
		{
								// TODO Add into categ
			$result=$objCateg->add_type($newobject,$elementtype);
			if ($result >= 0)
			{
				setEventMessages($langs->trans("WasAddedSuccessfully",$newobject->ref), null, 'mesgs');
			}
			else
			{
				$error=9001;
				setEventMessages($langs->trans("No se agrego la categoria ",$newobject->ref), null, 'mesgs');
			}
		}
	}
	//fin cateogria
	if (!$error) return 1;
	else $error *-1;
}
?>
