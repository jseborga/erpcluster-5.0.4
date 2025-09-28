<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
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
 */

/**
 *	\file       htdocs/projet/ganttview.php
 *	\ingroup    projet
 *	\brief      Gantt diagramm of a project
 */

require ("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formfile.class.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskdepends.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';

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


$id=GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$project_id = $id;
$mode = GETPOST('mode', 'alpha');
$mine = ($mode == 'mine' ? 1 : 0);
//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$projectstatic = new Project($db);
$object = new Task($db);
$taskadd = new Taskext($db);
$projettaskadd = new Projettaskadd($db);
$mobject = new Taskext($db);

$objdoc = new Projettasktimedoc($db);

if ($id || $ref)
{
    $projectstatic->fetch($id,$ref);
    $id=$projectstatic->id;
}

// Security check
$socid=0;
if ($user->societe_id > 0) $socid=$user->societe_id;
$result = restrictedArea($user, 'projet', $id);

$langs->load("users");
$langs->load("projects");
$langs->load("monprojet@monprojet");

/*
 * Actions
 */

// None


/*
 * View
 */

$form=new Form($db);
$formother     = new FormOther($db);
$userstatic    = new User($db);
$companystatic = new Societe($db);
$object        = new Task($db);
$extrafields   = new ExtraFields($db);

$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

$arrayofcss=array('/monprojet/css/style.css','/monprojet/css/jsgantt.css');

if (! empty($conf->use_javascript_ajax))
{
    $arrayofjs=array(
		     '/monprojet/js/jsgantt.js',
		     '/monprojet/js/graphics.js',

		     '/projet/jsgantt_language.js.php?lang='.$langs->defaultlang
		     );
}




/*
 * Summary
 */

//recuperamos las fotos
if ($projectstatic->id == $id)
{
    $date_start = $_SESSION['date_startexp'];
    $date_end   = $_SESSION['date_endexp'];

    include_once DOL_DOCUMENT_ROOT.'/monprojet/lib/format_excel.lib.php';

    //PROCESO 1
    $objPHPExcel = new PHPExcel();

    $aNew = array();

    //cabecera
    $pos = 0;
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->SetCellValue('A1',$langs->trans('Project'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B1',$projectstatic->ref);
    $objPHPExcel->getActiveSheet()->SetCellValue('C1',$projectstatic->title);

    $objPHPExcel->getActiveSheet()->SetCellValue('A2',$langs->trans('Datestart'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B2',dol_print_date($_SESSION['date_startexp'],'day'));

    $objPHPExcel->getActiveSheet()->SetCellValue('A3',$langs->trans('Dateend'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B3',dol_print_date($_SESSION['date_endexp'],'day'));

    $objPHPExcel->getActiveSheet()->SetCellValue('A4',$langs->trans('Date'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B4',html_entity_decode($langs->trans('Fielddescription')));
    $objPHPExcel->getActiveSheet()->SetCellValue('C4',$langs->trans('Unit'));
    $objPHPExcel->getActiveSheet()->SetCellValue('D4',$langs->trans('Advance'));
    $objPHPExcel->getActiveSheet()->SetCellValue('E4',$langs->trans('Total'));
    $objPHPExcel->getActiveSheet()->SetCellValue('F4',$langs->trans('Photo'));
    $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->applyFromArray($styleArray);

    $row = 5;
    $aNumTask = $_SESSION['aNumTaskexport'];
    $aSumTaskid = $_SESSION['aSumTaskid'];
    foreach ((array) $_SESSION['aTaskexport'] AS $idTask => $aData)
    {
        if ($aNumTask[$idTask]>0)
        {
        $taskadd->fetch($idTask);
        $projettaskadd->fetch('',$idTask);
        //units
        $unitname = '';
        $unit = $taskadd->getLabelOfUnit('',$projettaskadd->fk_unit);
        if ($unit !== '') $unitname = $langs->trans($unit);

        //mostramos la tarea
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,$taskadd->label);
        $objPHPExcel->setActiveSheetIndex($pos)->mergeCells('A'.$row.':F'.$row);

        $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->applyFromArray($styleArrayTitle);
        $row++;
        //ordenamos $aData
        KSORT($aData);
        //vamos a dejar en cero el contador
        $nTotal = 0;
        $var = true;
        $nMaxcol = 0;
        $aCol = array('F','G','H','I','J','K','L','M','N','O','P','Q');

        foreach ($aData AS $j => $aTask)
        {
            $var = !$var;
            $nTaskTotal = 0;
            foreach ($aTask AS $idTime => $data)
            {
                //echo '<hr>'.$data['newdatehour'].' >= '.$date_start.' && '.$data['newdatehour'].' <= '.$date_end;
                if ($data['newdatehour'] >= $date_start && $data['newdatehour'] <= $date_end)
                {
                    $nTaskTotal += $data['quant'];
                    $objdoc->fetch('',$idTime);
                    $aDate = dol_getdate($data['date']);
                    $t_date = PHPExcel_Shared_Date::FormattedPHPToExcel($aDate['year'],$aDate['mon'],$aDate['mday']);

                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,$t_date);
                    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow('A',$row)->getNumberFormat()->setFormatCode('[$-c09]d/mm/yyyy;@');

                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,$data['note']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row,$unitname);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,$data['quant']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,$aSumTaskid[$idTask][$idTime]);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row,$langs->trans('Photo'));
                    $objPHPExcel->getActiveSheet()->getStyle('D'.$row.':E'.$row)->applyFromArray($stylenumber);
                    $objPHPExcel->getActiveSheet()->getStyle('D'.$row.':E'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

                    //imagen
                    //buscamos si tiene imagenes
                    $objdoc->fk_task_time.' == '.$idTime.' && '.$objdoc->document;
                    if ($objdoc->fk_task_time == $idTime && !empty($objdoc->document))
                    {
                        $aPhoto = explode(';',$objdoc->document);
                        $ncol = 0;
                        $lImage = false;
                        foreach ((array) $aPhoto AS $j => $doc)
                        {
                            $aFile = explode('.',$doc);
                            //extension
                            $docext = STRTOUPPER($aFile[count($aFile)-1]);
                            $typedoc = '';
                            if ($docext == 'BMP' || $docext == 'GIF' ||$docext == 'JPEG' || $docext == 'JPG' || $docext == 'PNG' || $docext == 'TIF')
                                $typedoc = 'img';
                            if ($typedoc == 'img')
                            {
                                //direccion
                                $direction = DOL_DATA_ROOT.'/projet/'.$projectstatic->ref.'/'.$taskadd->ref.'/'.$idTime.'/'.$doc;

                                $objDraw = new PHPExcel_Worksheet_Drawing();
                                $objDraw->setPath($direction);
                                //$objDraw->setHeight(50);
                                $objDraw->setCoordinates($aCol[$ncol].$row);
                                $objDraw->setOffsetX(7);
                                $objDraw->setOffsetY(10);
                                $objDraw->setWidthAndHeight(60,60);
                                $objDraw->setResizeProportional(true);
                                $objDraw->setWorksheet($objPHPExcel->getActiveSheet());
                                $lImage = true;
                                $ncol++;
                                $texto = '                    ';
                                $objPHPExcel->getActiveSheet()->SetCellValue($aCol[$ncol].$row,$texto);
                            }
                        }
                        if ($nMaxcol <= $ncol) $nMaxcol = $ncol;
                        if ($lImage)
                        {
                        $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(50);
                        }
                    }
                    if ($var)
                    {
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->applyFromArray($styleArrayPar);
                        $objPHPExcel->getActiveSheet()->getStyle('D'.$row.':E'.$row)->applyFromArray($styleArrayParn);
                    }
                    else
                    {
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->applyFromArray($styleArrayImpar);
                        $objPHPExcel->getActiveSheet()->getStyle('D'.$row.':E'.$row)->applyFromArray($styleArrayImparn);
                    }
                    $row++;
                }
                else
                {
                    $nTaskTotal += $data['quant'];
                }
            }
        }
        }
    }
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    //for ($j=0; $j<=$nMaxcol; $j++)
    //    $objPHPExcel->getActiveSheet()->getColumnDimension($aCol[$j])->setAutoSize(true);

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save("excel/exportsummary.xlsx");
    $_SESSION['docsave'] = 'exportsummary.xlsx';
    header('Location: '.DOL_URL_ROOT.'/monprojet/fiche_export.php');
}
else
{
	print $langs->trans("NoTasks");
}


llxFooter();

$db->close();
