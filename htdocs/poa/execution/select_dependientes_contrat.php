<?php

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocesscontrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

$langs->load("poa@poa");

$objcontrat = new Contrat($db);
$extrafields = new ExtraFields($db);
$objsoc = new Societe($db);
$objpcon = new Poaprocesscontrat($db);

$extralabels=$extrafields->fetch_name_optionals_label($objcontrat->table_element);

// Array que vincula los IDs de los selects declarados en el HTML con el nombre de la tabla donde se encuentra su contenido
$listadoSelects=array(
"socid"=>"lista_socid",
"fk_contrat"=>"lista_contrat",
"fk_contrat_exist"=>"lista_contrat_exist",
);

function validaSelect($selectDestino)
{
	// Se valida que el select enviado via GET exista
	global $listadoSelects;
	if(isset($listadoSelects[$selectDestino])) return true;
	else return false;
}

function validaOpcion($opcionSeleccionada)
{
	// Se valida que la opcion seleccionada por el usuario en el select tenga un valor numerico
	if(is_numeric($opcionSeleccionada)) return true;
	else return false;
}

$selectDestino=$_GET["select"];
$opcionSeleccionada=$_GET["opcion"];
$exist = $_GET['exist'];

if(validaSelect($selectDestino) && validaOpcion($opcionSeleccionada))
{
	$objcontrat->socid = $opcionSeleccionada;
	$aContrat = $objcontrat->getListOfContracts('all');

	//contratos ya registrados
	$objpcon->getidscontrat();
	$aCon = $objpcon->array;
	$lView = true;
	$lControl = true;
	if ($exist) $lControl = false;
	// Comienzo a imprimir el select
	print  '<select class="form-control" name="'.$selectDestino.'" id="'.$selectDestino.'" onChange="cargaPartida(this.id)">';
	print '<option value="0">'.$langs->trans('Select').'</option>';
	foreach((array) $aContrat AS $j => $dataContrat)
	{
		$lView = false;
		if ($lControl)
		{
			if (empty($aCon[$dataContrat->id])) $lView = true;
		}
		else
			$lView = true;
		if ($lView)
		{
			//buscamos el contrato
			$objcont_ = new Contrat($db);
			$objcont_->fetch($dataContrat->id);
			$res=$objcont_->fetch_optionals($objcont_->id,$extralabels);

			if (!empty($objcont_->array_options['options_ref_contrato']))
			{
				$aArray[$dataContrat->id] = $objcont_->array_options['options_ref_contrato'];
				// Convierto los caracteres conflictivos a sus entidades HTML correspondientes para su correcta visualizacion
				$registro[0] = $dataContrat->id;
				$registro[1]=htmlentities(!empty($objcont_->array_options['options_ref_contrato'])?$objcont_->array_options['options_ref_contrato']:$dataContrat->ref);
				// Imprimo las opciones del select
				print '<option value="'.$registro[0].'">'.$registro[1].'</option>';
			}
		}
	}
	print '</select>';
}
?>