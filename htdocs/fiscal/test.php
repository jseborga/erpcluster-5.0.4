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
require_once(DOL_DOCUMENT_ROOT.'/fiscal/class/vfiscalext.class.php');
//require_once(DOL_DOCUMENT_ROOT.'/ventas/class/subsidiary.class.php');
require_once(DOL_DOCUMENT_ROOT.'/fiscal/class/vdosingext.class.php');
require_once(DOL_DOCUMENT_ROOT.'/user/class/user.class.php');

include DOL_DOCUMENT_ROOT."/fiscal/lib/phpqrcode/qrlib.php";    


$langs->load("main");
$langs->load('fiscal@fiscal');


$facid=GETPOST('facid','int');
$vfiscalid=GETPOST('vf','int');
$action =GETPOST('action','alpha');


//$object        = new Facture($db);
$objectvf      = new Vfiscal($db);
$objectvd      = new Vdosing($db);
//$objsubsidiary = new Subsidiary($db);
$facuser       = new User($db);
//$res = $object->fetch($facid);
//if (!$res)
//  exit;
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


    print '<form name="crea_commande" action="' . $_SERVER["PHP_SELF"] . '" method="POST">';
    print '<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">';
    print '<input type="hidden" name="action" value="view">';
    
    print '<table class="border centpercent">';
    //datos empresa
        // NIT
    print '<tr><td class="fieldrequired" colspan="3">'.$langs->trans('Datos empresa').'</td></tr>';
    print '<tr><td width="20%">' . $langs->trans('NIT') . '</td>';
    print '<td colspan="2">';
    print '<input type="text" name="nitsociete" value="'.GETPOST('nitsociete').'"></td>';
    print '</td></tr>';
    
    // name
    print '<tr><td>' . $langs->trans('Name') . '</td><td colspan="2">';
    print '<input type="text" name="razsociete" value="'.GETPOST('razsociete').'"></td>';
    print '</td></tr>';
    // fecha limite emision
    print '<tr><td>' . $langs->trans('Fecha limite emision') . '</td><td colspan="2">';
    $form->select_date($datelimit, 'dl', '', '', '', "crea_commande", 1, 1);
    print '</td></tr>';


    
    //datos del cliente
    print '<tr><td class="fieldrequired" colspan="3">'.$langs->trans('Datos cliente').'</td></tr>';
    // Nro autoriz
    print '<tr><td>' . $langs->trans('Numautoriz'). '</td><td colspan="2">';
    print '<input type="text" name="numaut" value="'.$numaut.'"></td>';
    print '</tr>';
    // Nro facture
    print '<tr><td>' . $langs->trans('Numberinvoice'). '</td><td colspan="2">';
    print '<input type="text" name="newnumfac" value="'.$newnumfac.'"></td>';
    print '</tr>';
    // NIT
    print '<tr><td>' . $langs->trans('NIT') . '</td>';
    print '<td colspan="2">';
    print '<input type="text" name="nit" value="'.$nit.'"></td>';
    print '</td></tr>';
    // name
    print '<tr><td>' . $langs->trans('Name') . '</td><td colspan="2">';
    print '<input type="text" name="razsoc" value="'.$razsoc.'"></td>';
    print '</td></tr>';

    // date
    print '<tr>';
    print '<td>' . $langs->trans('Date'). '</td>';
    print '<td colspan="2">';
    $form->select_date($date, 're', '', '', '', "crea_commande", 1, 1);
    print '</td>';
    print '</tr>' . "\n";
    // amount
    print '<tr><td>' . $langs->trans('Amount') . '</td><td colspan="2">';
    print '<input type="number" min="0" step="any" name="amount" value="'.$amount.'"></td>';
    // llave
    print '<tr><td>' . $langs->trans('Llave'). '</td><td colspan="2">';
    print '<input type="text" name="llave" value="'.$llave.'" size="80"></td>';
    print '</tr>';
    print '</table>';
    
    // Button "Create Draft"
    print '<br><center><input type="submit" class="button" name="bouton" value="' . $langs->trans('CreateDraft') . '"></center>';
    
    print '</form>';
  }

if ($action == 'view')
  {
    require_once DOL_DOCUMENT_ROOT.'/fiscal/factura/cc.php';
    //$nowtext = date('Y').'/'.date('m').'/'.date('d');
    $nowtext = GETPOST('reyear').(strlen(GETPOST('remonth'))==1?'0'.GETPOST('remonth'):GETPOST('remonth')).(strlen(GETPOST('reday'))==1?'0'.GETPOST('reday'):GETPOST('reday'));
    if (empty($nit))
      {
	$nit = 0;
	$razsoc = $langs->trans('Sin Nombre');
      }
    echo '<hr>aut |'.$numaut.'| |<br>fac |'.$newnumfac.'| |<br>nit |'.$nit.'| |<br>fec |'.$nowtext.'| |<br>monto |'.$amount.'| |<br>llave:  |'.$llave.'| ';
    $CodContr = new CodigoControl(trim($numaut),trim($newnumfac),trim($nit),$nowtext,$amount,trim($llave));
    $codigocontrol = $CodContr->generar();

    print '<table class="border centpercent">';

    // NIT
    print '<tr><td class="fieldrequired" width="15%">' . $langs->trans('Codigo control') . '</td>';
    print '<td colspan="2">';
    print $codigocontrol;
    print '</td></tr>';


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

    print '</table>';
    
  }
$db->close();
//fin escribir en un archivo


print $resulthtml;

?>