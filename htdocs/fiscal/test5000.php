<?php
/* Copyright (C) 2007-2008 Jeremie Ollivier    <jeremie.o@laposte.net>
 * Copyright (C) 2011      Laurent Destailleur <eldy@users.sourceforge.net>
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
require '../main.inc.php';

//require_once(DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php');

//require_once(DOL_DOCUMENT_ROOT.'/ventas/lib/ventas.lib.php');
//require_once(DOL_DOCUMENT_ROOT.'/ventas/class/vfiscal.class.php');
//require_once(DOL_DOCUMENT_ROOT.'/ventas/class/subsidiary.class.php');
//require_once(DOL_DOCUMENT_ROOT.'/ventas/class/vdosing.class.php');
require_once(DOL_DOCUMENT_ROOT.'/user/class/user.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php');

include DOL_DOCUMENT_ROOT."/fiscal/lib/phpqrcode/qrlib.php";    


$langs->load("main");
$langs->load('ventas@ventas');


$facid=GETPOST('facid','int');
$vfiscalid=GETPOST('vf','int');
$action =GETPOST('action','alpha');

//$object        = new Facture($db);
//$objectvf      = new Vfiscal($db);
//$objectvd      = new Vdosing($db);
//$objsubsidiary = new Subsidiary($db);
$facuser       = new User($db);
//$res = $object->fetch($facid);
//if (!$res)
 // exit;
//action
$date  = dol_mktime(12, 0, 0, GETPOST('remonth'),  GETPOST('reday'),  GETPOST('reyear'));
$datelimit  = dol_mktime(12, 0, 0, GETPOST('dlmonth'),  GETPOST('dlday'),  GETPOST('dlyear'));

$nit = trim(GETPOST('nit'));
$newnumfac= trim(GETPOST('newnumfac'));
$razsoc = trim(GETPOST('razsoc','alpha'));
$amount = trim(GETPOST('amount'));
$llave = trim(GETPOST('llave'));
$numaut = trim(GETPOST('numaut'));


/*
 view
*/

$form = new Form($db);
llxHeader("",$langs->trans("ApplicationsWarehouseCard"),$help_url);

if ($action == 'create' || $action == 'view')
  {
    print_fiche_titre($langs->trans('Invoicetest'));

    dol_htmloutput_mesg($mesg, $mesgs, 'error');

    print '<form  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
    print '<input type="hidden" name="action" value="view">';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    print '<input type="hidden" name="id" value="'.$id.'">';
    print '<input type="hidden" name="idc" value="'.$idc.'">';

    
    print '<table class="border centpercent">'."\n";
    print '<tr><td width="15%" class="fieldrequired">'.$langs->trans("File").'</td><td>';
    print '<input type="file" class="flat" name="archivo" id="archivo" required>';
    print '</td></tr>';

    print '</table>';
    
    print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Upload").'">';
    print '&nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
    print '</center>';
    
    print '</form>';
  }


// Part to create upload
if ($action == 'view')
  {
    $aHead = array(0=>'numaut',
		   1=>'numfact',
		   2=>'nit',
		   3=>'date',
		   4=>'amount',
		   5=>'llave',
		   6=>'verhoeff',
		   8=>'sumatoria',
		   9=>'base64',
		   10=>'codigo');
    //verificacion
    $nombre_archivo = $_FILES['archivo']['name'];
    $tipo_archivo = $_FILES['archivo']['type'];
    $tamano_archivo = $_FILES['archivo']['size'];
    $tmp_name = $_FILES['archivo']['tmp_name'];
    $separator = GETPOST('separator','alpha');
    $tempdir = DOL_DOCUMENT_ROOT."/fiscal/tmp/";
    
    //    if(move_uploaded_file($tmp_name, $tempdir.$nombre_archivo))
    if(dol_move_uploaded_file($tmp_name, $tempdir.$nombre_archivo,1,10,0,$nombre_archivo))
      {
	
    	//  echo "file uploaded<br>"; 
      }
    else
      {
    	echo 'no se puede mover';
    	exit;
      }
    $separator = '|';
    
    $csvfile = $tempdir.$nombre_archivo;
    $fh = fopen($csvfile, 'r');
    $headers = fgetcsv($fh);
    $aHeaders = explode($separator,$headers[0]);
    $data = array();
    $aData = array();
    while (! feof($fh))
      {
	$row = fgetcsv($fh,'','^');
        if (!empty($row))
	  {
	    $aData = explode($separator,$row[0]);
	    $obj = new stdClass;
	    $obj->none = "";
	    foreach ($aData as $i => $value)
	      {
		$key = $aHead[$i];
		if (!empty($key))
		  $obj->$key = $value;
		else
		  $obj->none = $value." xx";
	      }
	    $data[] = $obj;
	  }
      }
    fclose($fh);
    
    $c=0;
    $action = "verifup";
    print '<table class="border centpercent">';
    $j = 1;
    foreach ($data AS $i => $obj)
      {
	$numaut = $obj->numaut;
	$newnumfac = $obj->numfact;
	$nit = $obj->nit;
	$aDate = explode('/',$obj->date);
	//$aDate = dol_getdate($obj->date);
	$nowtext = $aDate['year'].(strlen($aDate['mon'])==1?'0'.$aDate['mon']:$aDate['mon']).(strlen($aDate['mday'])==1?'0'.$aDate['mday']:$aDate['mday']);
	$nowtext = $aDate[0].(strlen($aDate[1])==1?'0'.$aDate[1]:$aDate[1]).(strlen($aDate[2])==1?'0'.$aDate[2]:$aDate[2]);
	$amount = $obj->amount;
	$amount = str_replace(',','.',$amount);
	$llave = $obj->llave;
	
	require_once DOL_DOCUMENT_ROOT.'/ventas/factura/cc.php';
	//$nowtext = date('Y').'/'.date('m').'/'.date('d');
	//$nowtext = GETPOST('reyear').GETPOST('remonth').GETPOST('reday');
	if (empty($nit))
	  {
	    $nit = 0;
	    $razsoc = $langs->trans('Sin Nombre');
	  }
	//	echo '<hr>aut '.$numaut.' |fac '.$newnumfac.' |nit '.$nit.' |fec '.$nowtext.' |monto '.$amount.' |llave:  '.$llave.' | ';
	$CodContr = new CodigoControl($numaut,$newnumfac,$nit,$nowtext,$amount,$llave);
	$codigocontrol = $CodContr->generar();
	
	
	// NIT
	print '<tr><td class="fieldrequired" width="15%">' . $langs->trans('Codigo control').' '.$j . '</td>';
	print '<td style="bacground:#ff0000;">';
	print $codigocontrol;
	print '</td>';
	print '<td>';
	print $obj->verhoeff;
	print '</td>';
	print '<td>';
	print $obj->sumatoria;
	print '</td>';
	print '<td>';
	print $obj->base64;
	print '</td>';
	
	print '<td>';
	print $obj->codigo;
	print '</td>';
	print '<td>';
	if ($codigocontrol == $obj->codigo)
	  print 'OK';
	else
	  print 'KO';
	print '</td>';
	print '</tr>';
	
	//armamos el textqr
	$textqr = '';
	$textqr.=GETPOST('nitsociete');
	$textqr.='|';
	$textqr.=GETPOST('razsociete');
	$textqr.='|';
	
	$textqr.=$newnumfac;
	$textqr.='|';
	$textqr.=$numaut;
	$textqr.='|';
	
	$textqr.=dol_print_date($date,'day');
	$textqr.='|';
	
	
	$textqr.=round($amount);
	$textqr.='|';
	
	
	$textqr.=$codigocontrol;
	$textqr.='|';
	$textqr.=dol_print_date($datelimit,'day');
	$textqr.='|';
	$textqr.=$nit;
	$textqr.='|';
	$textqr.=$razsoc;
	
	
	// // //generacion de codigoqr
	// include DOL_DOCUMENT_ROOT.'/ventas/lib/qrcopylinea.php';
	
	// // qrcode
	// print '<tr><td width="15%">' . $langs->trans('Codigo control') . '</td>';
	// print '<td colspan="2">';
	// print $linea;
	// print '</td></tr>';
	$j++;
      }
    print '</table>';
    
  }
$db->close();
//fin escribir en un archivo
?>