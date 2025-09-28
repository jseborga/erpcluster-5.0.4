<?php
require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';

//Importar clases
//dol_include_once('/assets/class/assetscontador.class.php');
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetscontador.class.php';
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

$langs->load("assets");

$action=GETPOST('action');


$id         = GETPOST("rowid");
$rid        = GETPOST("rid");
$fk_period  = GETPOST("fk_period");
$fk_concept = GETPOST("fk_concept");
$docum      = GETPOST('docum');
$selrow     = GETPOST('selrow');
$camposdate = GETPOST('camposdate');
$separator  = GETPOST('separator');
$mesg = '';

/*No One*/
//Mis Valores rescatados
$fk_ref  = GETPOST('fk_ref');
$aImport = array();

//Mis Objetos Declarados
$objAssConta  = new Assetscontador($db);
$objAssets    = new Assetsext($db);

//Array de titulos de tabla(campos)
$aCampostabla = array(1=>"ref",2=>"mes",3=>"anio",4=>"total");
$aHeaders     = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z',27=>'AA',28=>'AB');

/*End No One*/

$objUser  = new User($db);

 /*
 * Actions
 */

// save $#$#$# no
if ($action == 'add' && GETPOST('save') == $langs->trans('Save'))
{
	/*verificamos los tipos*/
	$nro_add = GETPOST('nro');
	$ref_add = GETPOST('fk_ref');
	$nroEx = 0;

	$error = 0;
	$aImport   = unserialize($_SESSION['aImport']);

	//Preguntamos que  tipo de referencia es
	if($ref_add == 1){
		$valor_ref = "ref_ext";
	}elseif($ref_add == 2){
		$valor_ref = "ref";
	}

	$db->begin();

	foreach ($aImport AS $r => $data)
	{
			$fk_asset = $data['fk_asset'];
			//$res = $objAssets->fetchAll('','',0,0,array(1=>1),'AND',"AND t.".$valor_ref." = '".trim($data['ref'])."'",true);
			if($fk_asset > 0)
			{

				$filter = " AND t.fk_asset = ".$fk_asset ." AND t.period_month = ".$data['mes']." AND t.period_year = ".$data['anio'];
				$resPre = $objAssConta->fetchAll('','',0,0,array(1=>1),'AND',$filter);
				//echo "Verificador : ".$resPre;
				if($resPre == 0){
					$objAssConta->fk_asset       = $fk_asset;
					$objAssConta->period_month   = $data['mes'];
					$objAssConta->period_year    = $data['anio'];
					$objAssConta->quant          = $data['total'];
					$objAssConta->fk_user_create = $user->id;
					$objAssConta->fk_user_mod    = $user->id;
					$objAssConta->datec          = dol_now();
					$objAssConta->datem          = dol_now();
					$objAssConta->tms            = dol_now();
					$objAssConta->status         = 1;

					$resQ = $objAssConta->create($user);

					if($resQ <=0){
						$error++;
						setEventMessages($objAssConta->error,$objAssConta->errors,'errors');
					}else{
						$nroEx++;
					}
				}elseif ($resPre<0){
					setEventMessages($objAssets->error,$objAssets->errors,'errors');
				}
			}else{

			}


	}

	if (!$error)
	{
		$nro_add--;
		setEventMessages($langs->trans('Satisfactoryprocess').' '.$nroEx.' '.$langs->trans('to')." ".$nro_add,null,'mesgs');
		$db->commit();
		$action = '';
	}else
	{
		$action = '';
		$db->rollback();
	}

}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

/*
 * View
 */

$form=new Form($db);
$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
$morecss = array('/assets/css/style.css');
llxHeader("",$langs->trans("Managementsalary"),$help_url,'','','','',$morecss);

if ($action == 'create' || empty($action) && $user->rights->assets->dep->uploadace)
{

	$aReference = array(1=>$langs->trans('Externalreference'),2=>$langs->trans('Internalreference'));
	$opcRef = "";
	foreach ($aReference as $key => $value) {
		if(GETPOST('fk_ref') == $key )
			$opcRef .= '<option value="'.$key.'" selected>'.$value.'</option>';
		else
			$opcRef .= '<option value="'.$key.'">'.$value.'</option>';
	}

	print_fiche_titre($langs->trans("Uploadarchive"));
	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="read">';
	dol_htmloutput_mesg($mesg);
	print '<table class="border" width="100%">';
	print '<tr><td>';
	print $langs->trans('Selectarchiv');
	print '</td>';
	print '<td>';
	print '<input type="file" name="archivo" size="40">';
	print '</td></tr>';
	print '<tr><td>'.$langs->trans('Reference').'</td>';
	print '<td><select name="fk_ref">'.$opcRef.'</select></td></tr>';
	print '<tr><td>';
	print $langs->trans('Firstrowistitle');
	print '</td>';
	print '<td>';
	print $form->selectyesno('selrow',(GETPOST('selrow')?GETPOST('selrow'):1),1);
	print '</td></tr>';
	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Upload").'"></center>';

	print '</form>';
	print '<br><br>';
	print '<a href="'.DOL_URL_ROOT.'/assets/upload/tmp/contador.xlsx'.'">'.$langs->trans('Downloadexample').'</a>';
}
else
{

}


if ($action == 'read')
{
	$nombre_archivo = $_FILES['archivo']['name'];
	$tipo_archivo = $_FILES['archivo']['type'];
	$tamano_archivo = $_FILES['archivo']['size'];
	$tmp_name = $_FILES['archivo']['tmp_name'];

	$fk_ref = GETPOST('fk_ref');

	$tempdir = "tmp/";
	//extension es correcta?


	if(move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
	{
		//  echo "file uploaded<br>";
		//setEventMessages($langs->trans('Fileuploadedsuccessful'), null, 'mesgs');
	}
	else
	{
		echo 'no se puede mover';
		exit;
	}
	$nTipeSheet = substr($nombre_archivo,-3);


	$objPHPExcel = new PHPExcel();
	$type = '';
	if ($nTipeSheet =='lsx')
	{
		$type = 'spreedsheat';
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	}
	elseif ($nTipeSheet =='xls')
	{
		$type = 'spreedsheat';
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	}
	elseif ($nTipeSheet =='csv')
	{
		$type = 'csv';
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	}
	else
	{
		echo "Documento no valido verifique que sea el correcto para la importacion";
		print "<a href=".DOL_URL_ROOT."/assets/upload/contador.php>Volver</a>";
		exit;
	}

	/*reading of sheet electronic*/
	$aImport = array();
	if ($type == 'spreedsheat')
	{
		$objPHPExcel = $objReader->load('tmp/'.$nombre_archivo);
		$objReader->setReadDataOnly(true);

		$nOk = 0;
		$nLoop = 4;
		$nLine=1;


		if ($selrow == 1)
		{
			$nLine++;
		}

		$lLoop = true;
		$i = 0;
		while ($lLoop == true)
		{
			if (!empty($objPHPExcel->getActiveSheet()->getCell($aHeaders[1].$nLine)->getValue()))
			{
				for ($a = 1; $a <= $nLoop; $a++)
				{
					$dato = $objPHPExcel->getActiveSheet()->getCell($aHeaders[$a].$nLine)->getValue();
					$aImport[$i][$aCampostabla[$a]] = $dato;
				}
				$i++;
			}
			elseif(empty($objPHPExcel->getActiveSheet()->getCell($aHeaders[1].$nLine)->getFormattedValue()))
			{
				$lLoop = false;
			}
			$nLine++;
		}
	}

	if($nLine > 1){
		print '<table class="noborder" width="100%">';
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Nro"),"","","","","");
		print_liste_field_titre($langs->trans("Reference"),"","","","","");
		print_liste_field_titre($langs->trans("Month"),"","","","","");
		print_liste_field_titre($langs->trans("Year"),"","","","","");
		print_liste_field_titre($langs->trans("Total"),"","","","","");
		print '</tr>';

		$var = true;
		$nro = 1;
		$lAdd = true;
		foreach ($aImport as $key => $value)
		{
			$mark = '';
			if ($fk_ref==1)
			{
				//externo
				$filter = " AND t.ref_ext = '".trim($value['ref'])."'";
				$res = $objAssets->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
				if ($res<=0)
				{
					$lAdd = false;
					$mark = 'class="markerror"';
				}
				else
					$aImport[$key]['fk_asset'] = $objAssets->id;
			}
			else
			{
				//interno
				$res = $objAssets->fetch(0,trim($value['ref']));
				if ($res<=0)
				{
					$lAdd = false;
					$mark = 'class="markerror"';
				}
				else
					$aImport[$key]['fk_asset'] = $objAssets->id;
			}
			if (!empty($mark))
				print '<tr al="22"'.$mark.'>';
			else
				print '<tr'.$bc[$var].'>';
			print '<td>'.$nro.'</td>';
			print '<td>'.$value['ref'].'</td>';
			print '<td>'.$value['mes'].'</td>';
			print '<td>'.$value['anio'].'</td>';
			print '<td>'.$value['total'].'</td>';
			print '</tr>';
			$var = !$var;
			$nro++;
		}
		print '</table>';
		if (!$lAdd)
			setEventMessages($langs->trans('Thereisnooneoftherecords'),null,'errors');
		if ($lAdd)
		{
			$_SESSION['aImport'] = serialize($aImport);

			print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="add">';
			print '<input type="hidden" name="nro" value="'.$nro.'">';
			print '<input type="hidden" name="fk_ref" value="'.$fk_ref.'">';
			print '<center><br><input type="submit" class="butAction" name="save" value="'.$langs->trans("Save").'">';
			print '&nbsp;<input type="submit" class="butActionDelete" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
			print '</form>';
		}

	}

}


llxFooter();
$db->close();
?>
