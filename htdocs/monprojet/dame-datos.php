<?php
require ("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';

require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

//budget
if ($conf->budget->enabled)
{
	//require_once DOL_DOCUMENT_ROOT.'/budget/class/html.formadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/items/class/items.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/cunits.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructure.class.php';
}
else
	return '';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/utils.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
dol_include_once('/monprojet/class/html.formtask.class.php');

if ($conf->monprojet->enabled) $formtask=new FormTask($db);

$projet  = new Projectext($db);
$task 	 = new Taskext($db);
$product = new Product($db);
$object = new Projectext($db);
$categorie = new Categorie($db);
$pustr = new Pustructure($db);

$form=new Formv($db);
//$formadd = new FormAdd($db);
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
print '<table class="noborder centpercent">';

print '<tr class="liste_titre">';
print '<td class="width25">'.$aStrref[$ref].'</td>';
print '<td class="width10">'.$langs->trans('Product').'</td>';
print '<td class="width10">'.$langs->trans('Unit').'</td>';
print '<td class="width05">'.$langs->trans('Qty').'</td>';
print '<td class="width05">'.$langs->trans('Action').'</td>';
print '</tr>';

$var = true;
//echo '<pre>';
//print_r($_POST);
//print_r($aDatamat);
//echo '</pre>';

foreach ((array) $aDatamat[$id][$ref] AS $i => $data)
{
	$var = !$var;
	print '<tr class="'.$bc[$var].'">';

	$task->fetch($data['fk_task']);
	if ($task->id == $data['fk_task'])
		print '<td>'.$task->ref.'</td>';
	else
		print '<td>&nbsp;</td>';
	$product->fetch($data['fk_product']);
	if ($product->id == $data['fk_product'])
		print '<td>'.$product->label.'</td>';
	else
		print '<td>&nbsp;</td>';
	print '<td>'.$data['unit'].'</td>';
	print '<td>'.$data['qty'].'</td>';
	print '<td>';
	print '<a href="#" class="delete" onclick="javascript: borrar_legajo(this,'."'".$data['ref']."'".');" id="delete'.$i.'">'.$langs->trans('Eliminar').'</a>';
	print '</td>';
	//fila
	print '</tr>';
}
print '</table>';
?>