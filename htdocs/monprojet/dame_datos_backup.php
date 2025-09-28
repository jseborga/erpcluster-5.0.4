<?php
require ("../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
//priceunits
if ($conf->priceunits->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/priceunits/class/html.formadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/priceunits/items/class/items.class.php';
	require_once DOL_DOCUMENT_ROOT.'/priceunits/class/cunits.class.php';
	require_once DOL_DOCUMENT_ROOT.'/priceunits/class/pustructure.class.php';
}
else
	return '';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/utils.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
dol_include_once('/monprojet/class/html.formtask.class.php');
dol_include_once('/monprojet/class/html.formv.class.php');

if ($conf->monprojet->enabled) $formtask=new FormTask($db);

$projet  = new Project($db);
$task 	 = new Task($db);
$product = new Product($db);
$object = new Project($db);
$categorie = new Categorie($db);
$pustr = new Pustructure($db);

$form=new Formv($db);
$formadd = new FormAdd($db);
$formother=new FormOther($db);
$taskstatic = new Task($db);
$userstatic=new User($db);

$action = GETPOST('action');
$id 	= GETPOST('id');
$idreg 	= GETPOST('idreg');
$ref    = GETPOST('ref');
$aStrref = unserialize($_SESSION['aStrref']);
$aStrlabel = unserialize($_SESSION['aStrlabel']);

if ($id>0) $object->fetch($id);
$aDatamat = unserialize($_SESSION['aDatamat']);

if (isset($_POST['action']))
{
	if ($action == 'addmat')
	{
		if ($_POST['task'])
		{
			$task->fetch('',$_POST['task']);
			if ($task->ref == $_POST['task']) $fk_task = $task->id;
		}
		//buscamos el producto
		$unit = '';
		if ($_POST['product'])
		{
			$product->fetch($_POST['product']);
			//obtenemos la unidad de medida
			$unit = $product->getLabelOfUnit('short');
			//obtenemos la categoria
			/*$data = fetch_categorie_table($_POST['product'],'product');
			if (count($data)>0)
			{
				foreach ($data AS $j => $obj)
				{
					if ($aStrlabel[$obj->fk_categorie])
					{
						$categorie->fetch($obj->fk_categorie);
						if ($categorie->id == $obj->fk_categorie)
							$ref = $categorie->ref;
						else
						{
							echo 'errrrr';exit;
						}
					}
				}
			}
			*/
		}
		$aDatamat[$id][$ref][] = array('fk_task'=> $fk_task,'fk_product'=>$_POST['product'],'qty'=>$_POST['qty'],'unit'=>$unit,'ref'=>$ref);
		//$aDatamat[] = array('fk_task'=> $_POST['task'],'fk_product'=>$_POST['product'],'qty'=>$_POST['qty']);
	}
	if ($action == 'delmat')
	{
		$idreg = substr($idreg,6,10);
		unset($aDatamat[$id][$ref][$idreg]);
	}
	
	$_SESSION['aDatamat'] = serialize($aDatamat);
}
print '<div class="contenedor-tabla">';

print '<div class="contenedor-fila">';
print '<div class="contenedor-columna width25">'.$aStrref[$ref].'</div>';
print '<div class="contenedor-columna width10">'.$langs->trans('Product').'</div>';
print '<div class="contenedor-columna width10">'.$langs->trans('Unit').'</div>';
print '<div class="contenedor-columna width05">'.$langs->trans('Qty').'</div>';
print '<div class="contenedor-columna width05">'.$langs->trans('Action').'</div>';
print '</div>';

$var = true;
//echo '<pre>';
//print_r($_POST);
//print_r($aDatamat);
//echo '</pre>';

foreach ((array) $aDatamat[$id][$ref] AS $i => $data)
{
	$var = !$var;
	print '<div class="contenedor-fila" id="service'.$i.'" data="'.$i.'">';

	$task->fetch($data['fk_task']);
	if ($task->id == $data['fk_task'])
		print '<div class="contenedor-columna">'.$task->ref.'</div>';
	else
		print '<div class="contenedor-columna">&nbsp;</div>';
	$product->fetch($data['fk_product']);
	if ($product->id == $data['fk_product'])
		print '<div class="contenedor-columna">'.$product->label.'</div>';
	else
		print '<div class="contenedor-columna">&nbsp;</div>';
	print '<div class="contenedor-columna" align="right">'.$data['unit'].'</div>';
	print '<div class="contenedor-columna" align="right">'.$data['qty'].'</div>';
	print '<div class="contenedor-columna">';
	print '<a href="#" class="delete" onclick="javascript: borrar_legajo(this,'."'".$data['ref']."'".');" id="delete'.$i.'">'.$langs->trans('Eliminar').'</a>';
	print '</div>';
	//fila
	print '</div>';
}
print '</div>';
?>