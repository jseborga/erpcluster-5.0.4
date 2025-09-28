<?php
/* Copyright (C) 2007-2008 Jeremie Ollivier    <jeremie.o@laposte.net>
 * Copyright (C) 2011	   Juanjo Menent   	   <jmenent@2byte.es>
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

/**
 *	\file       htdocs/ventas/index.php
 * 	\ingroup	ventas
 *  \brief      File to login to point of sales
 */

// Set and init common variables
// This include will set: config file variable $dolibarr_xxx, $conf, $langs and $mysoc objects
require_once("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/html.formproduct.class.php");
require_once(DOL_DOCUMENT_ROOT."/ventas/class/Facturation.class.php");
require_once(DOL_DOCUMENT_ROOT."/ventas/permiso/class/entrepotbanksoc.class.php");
require_once(DOL_DOCUMENT_ROOT."/ventas/class/bankstatus.class.php");
require_once(DOL_DOCUMENT_ROOT."/ventas/class/subsidiary.class.php");

$langs->load("admin");
$langs->load("cashdesk");
$langs->load('ventas@ventas');

$_SESSION['DOL_URL_ROOT'] = DOL_URL_ROOT;
// Test if user logged
if ($_GET['action'] == 'close')
{
	print '<script language="javascript">setTimeout("self.close();",100)</script> ';
}
//verificamos si ya esta logeado en el principal
$lValid = true;
if ($user->id > 0 && empty($_SESSION['uid']))
{
	$_SESSION['uid'] = $user->id;
	$_SESSION['login'] = $user->login;
	$lValid = false;
}

//antes//

$usertxt=GETPOST('user','',1);

/*
 * View
 */

$form=new Form($db);
$formproduct=new FormProduct($db);
$Facturation = new Facturation($db);
$ip = $Facturation->verificaIP();
$objsubsidiary = new Subsidiary($db);

//verificamos el modo de permiso
$modPermission = $conf->global->VENTA_PERMISSIONS_PDV_MOD;

//verificando la asignacion de permisos por ip
$sql = " SELECT c.rowid, c.numero_ip as ref, c.fk_user, c.fk_entrepotid, c.fk_socid, c.fk_cajaid, c.fk_subsidiaryid, c.series, c.status";
$sql.= " FROM ".MAIN_DB_PREFIX."entrepot_bank_soc as c";
$sql.= " WHERE c.entity = ".$conf->entity;

if (empty($modPermission))
	$sql.= " AND c.numero_ip LIKE '$ip'";
else
	$sql.= " AND c.fk_user = ".$user->id;
//echo $sql;
$result = $db->query($sql);
//forma de cobro
$idCash      = 0;
$arraycb     = array();
$arraypay    = array();
$arrayaccount= array();
$arraycblang = array();
if ($result)
{
	$num             = $db->num_rows($result);
	$obj             = $db->fetch_object($result);

	$fk_entrepotid   = $obj->fk_entrepotid;
	$fk_socid        = $obj->fk_socid;
	$fk_cajaid       = $obj->fk_cajaid;
	$fk_subsidiaryid = $obj->fk_subsidiaryid;
	$series          = $obj->series;
	if (!empty($fk_cajaid))
	{
		$arraycb[$fk_cajaid] = 'Cash';
		$idCash = $fk_cajaid;
		$arraypay[$fk_cajaid] = 'ESP';
		$arrayaccount[$fk_cajaid] = $fk_cajaid;
		$arraycblang[$fk_cajaid] = $langs->trans('Cash');
	}
	$fk_bankid     = ((GETPOST('bankid_cheque') > 0)?GETPOST('bankid_cheque'):$conf->global->CASHDESK_ID_BANKACCOUNT_CHEQUE);
	$fk_banktcid   = ((GETPOST('bankid_cb') > 0)?GETPOST('bankid_cb'):$conf->global->CASHDESK_ID_BANKACCOUNT_CB);
	$fk_cajagfid   = ((GETPOST('cajagfid_cb') > 0)?GETPOST('cajagfid_cb'):$conf->global->CASHDESK_ID_GIFTCARDS_CB);

	if (!empty($fk_bankid))
	{
		$arraycb[$fk_bankid] = 'Check';
		$arraypay[$fk_bankid] = 'CHQ';
		$arrayaccount[$fk_bankid] = $fk_bankid;
		$arraycblang[$fk_bankid] = $langs->trans('Checks');
	}
	if (!empty($fk_banktcid) )
	{
		$arraycb[$fk_banktcid] = 'Creditcard';
		$arraypay[$fk_banktcid] = 'CB';
		$arrayaccount[$fk_banktcid] = $fk_banktcid;
		$arraycblang[$fk_banktcid] = $langs->trans('Creditcard');
	}
	if (!empty($fk_cajagfid))
	{
		$arraycb[$fk_cajagfid] = 'Giftcard';
		$arraypay[$fk_cajagfid] = 'GB';
		$arrayaccount[$fk_cajagfid] = $fk_cajagfid;
		$arraycblang[$fk_cajagfid] = $langs->trans('Giftcard');
	}
	//echo '|'.$fk_cajaid.' | '.$fk_bankid.' | '.$fk_banktcid.' | '.$fk_cajagfid.'|'.$conf->global->CASHDESK_ID_BANKACCOUNT_CB.'|';
	//print_r($arraycblang);exit;
	//buscando datos de la sucursal
	$objsubsidiary->fetch($fk_subsidiaryid);
	//echo $objsubsidiary->id.' == '.$fk_subsidiaryid;
	if ($objsubsidiary->id == $fk_subsidiaryid && $fk_subsidiaryid > 0)
	{
		$lPermission = false;
		if (empty($modPermission))
		{
			if ($obj->ref == $ip)
				$lPermission = true;
		}
		else
		{
			if ($obj->fk_user == $user->id)
				$lPermission = true;
		}
		if ($lPermission)
			$_SESSION['aArraySubsid'] = array('ref'=>$objsubsidiary->ref,
				'label'=>$objsubsidiary->label,
				'socialreason'=>$objsubsidiary->socialreason,
				'nit'=>$objsubsidiary->nit,
				'address'=>$objsubsidiary->address,
				'phone'=>$objsubsidiary->phone,
				'message'=>$objsubsidiary->message,
				'serie'=>$objsubsidiary->serie,
				'activity'=>$objsubsidiary->activity);
		else
		{
			$_SESSION['aArraySubsid'] = array();
			print 'Comuniquese con el administrador de sistemas para configurar las sucursales.';
			exit;
		}
	}
	else
	{
		$_SESSION['aArraySubsid'] = array();
		print 'Comuniquese con el administrador de sistemas para configurar las sucursales y permisos.';
		exit;
	}

	//verificamos los tipos de factura que tiene habilitada manual/automatica
	$dateact = dol_now();
	$sql = "SELECT t.rowid, t.date_val, t.series, t.num_ini, t.num_fin, t.num_ult, t.lote, ";
	$sql.= " num_autoriz, t.chave ";
	$sql.= " FROM ".MAIN_DB_PREFIX."v_dosing AS t ";
	$sql.= " WHERE ";
	$sql.= " t.entity = ".$conf->entity;
	$sql.= " AND t.fk_subsidiaryid = ".$fk_subsidiaryid;
	$sql.= " AND t.lote IN (1,2) ";
	//factura automatica
	$sql.= " AND t.active = 1 ";
	$sql.= " AND ((t.series = '".$series."' AND t.lote = 2)  " ;
    $sql.= " OR t.lote = 1 ) " ;

	$sql.= " AND status = 1 ";
	// if ($user->admin)
	//   echo $sql;
	$res2=$db->query($sql);
	$arrayDosing = array();
	if ($res2)
	{
		if ($db->num_rows($res2))
		{
			//dejamos como que no existiese
			$arrayDosing['dosing_aut'] = false;
			$arrayDosing['dosing_man'] = false;
			$num =  $db->num_rows($res2);
			$i = 0;
			while ($i < $num)
			{
				$objd = $db->fetch_object($res2);
				if ($objd->lote == 2)
					$arrayDosing['dosing_aut'] = true;
				if ($objd->lote == 1)
					$arrayDosing['dosing_man'] = true;

				//verificacion de numeracion en factura manual
				if ($objd->lote == 1 && $objd->num_ult >= $objd->num_fin)
				{
					//echo ' '.$objd->num_ult .' >= '.$objd->num_fin;
					$arrayofcss=array('/ventas/css/style.css');
					//top_htmlhead('','',0,0,'',$arrayofcss);
					 $mesg='<div class="error">'.'Favor comunicarse con el Administrador del Sistema, ya no tiene numeros disponibles para la impresion de facturas.'.$objd->lote.' '.$objd->num_ult.' >= '.$objd->num_fin.'</div>';
					if ($objd->lote == 2)
					{
						$arrayDosing['dosing_aut'] = false;
						$arrayErrDosing['dosing_aut'] = $mesg;
					}
					if ($objd->lote == 1)
					{
						$arrayDosing['dosing_man'] = false;
						$arrayErrDosing['dosing_man'] = $mesg;
					}
				}

				//verificacion de fecha limite de emision
				$adate = dol_getdate($db->jdate($objd->date_val));
				$dateval  = dol_mktime(0, 0, 0, $adate['mon'], $adate['mday'],  $adate['year']);
				$adate = dol_getdate($dateact);
				$dateact  = dol_mktime(0, 0, 0, $adate['mon'], $adate['mday'],  $adate['year']);
				//echo '<hr>'.dol_print_date($dateval,'day') .' < '.dol_print_date($dateact,'day');
				if ($dateval < $dateact)
				{
					$arrayofcss=array('/cashdesk/css/style.css');
					//top_htmlhead('','',0,0,'',$arrayofcss);
					$mesg='<div class="error">'.'Alerta, paso la fecha limite para la emision de facturas, favor contactese con el Administrador'.'</div>';
					if ($objd->lote == 2)
					{
						$arrayDosing['dosing_aut'] = false;
						$arrayErrDosing['dosing_aut'] = $mesg;
					}
					if ($objd->lote == 1)
					{
						$arrayDosing['dosing_man'] = false;
						$arrayErrDosing['dosing_man'] = $mesg;
					}
					//exit;
				}
				elseif ($dateval == $dateact)
				{
					$arrayofcss=array('/cashdesk/css/style.css');
					//top_htmlhead('','',0,0,'',$arrayofcss);
					$mesg='<div class="error">'.'Alerta, hoy es la ultima fecha para impresion de facturas'.'</div>';
					if ($objd->lote == 2)
					{
						$arrayDosing['dosing_aut'] = true;
						$arrayErrDosing['dosing_aut'] = $mesg;
					}
					if ($objd->lote == 1)
					{
						$arrayDosing['dosing_man'] = true;
						$arrayErrDosing['dosing_man'] = $mesg;
					}
				}
				else
				{
					if ($objd->lote == 2)
						$arrayDosing['dosing_aut'] = true;
					if ($objd->lote == 1)
						$arrayDosing['dosing_man'] = true;
				}
				$i++;
			}
			//revisando los resultados
			if ($arrayDosing['dosing_aut'] == false &&
				$arrayDosing['dosing_man'] == false)
			{
				unset($_SESSION['uid']);
				exit;
			}
		}
		else
		{
			$arrayofcss=array('/cashdesk/css/style.css');
			top_htmlhead('','',0,0,'',$arrayofcss);
			print $mesg='<div class="error">'.'Favor comunicarse con el Administrador del Sistema para Dosificacion de Facturas.'.'</div>';
			exit;
		}
	}
	else
	{
		print 'Favor comunicarse con el Administrador del Sistema para asignacion de facturas.';
		exit;
	}
	$_SESSION['fkEntrepotid']   = $fk_entrepotid;
	$_SESSION['fkCajaid']       = $fk_cajaid;
	$_SESSION['idCash']         = $idCash;
	$_SESSION['fkBankid']       = $fk_bankid;
	$_SESSION['fkBanktcid']     = $fk_banktcid;
	$_SESSION['fkCajagfid']     = $fk_cajagfid;
	$_SESSION['fkSocid']        = $fk_socid;
	$_SESSION['fkSubsidiaryid'] = $fk_subsidiaryid;
	$_SESSION['ivaLocal']       = $conf->global->IVA_BOLIVIA_NOMINAL;
	$_SESSION['selTva']         = '';
	$_SESSION['template']       = $conf->global->VENTA_TEMPLATE_USE;
	$_SESSION['arraycb']        = $arraycb;
	$_SESSION['arraypay']       = $arraypay;
	$_SESSION['arrayaccount']   = $arrayaccount;
	$_SESSION['arraycblang']    = $arraycblang;
	$_SESSION['arrayDosing']    = $arrayDosing;
	$_SESSION['series']         = $series;
	$_SESSION['arrayErrDosing'] = $arrayErrDosing;

	$_SESSION['CASHDESK_ID_WAREHOUSE'] = $fk_entrepotid;

	if (empty($fk_entrepotid) || empty($fk_socid))
	{
		$arrayofcss=array('/cashdesk/css/style.css');
		top_htmlhead('','',0,0,'',$arrayofcss);
		print $mesg='<div class="error">'.'Error, no esta autorizado'.'<br>'.'Contacte con el administrador, para solucionar'.'</div>';
		exit;
	}
}
else
{
	$arrayofcss=array('/cashdesk/css/style.css');
	top_htmlhead('','',0,0,'',$arrayofcss);
	print $mesg='<div class="error">'.'Error, no esta autorizado'.'</div>';
	exit;
}
$arrayofcss=array('/ventas/css/style.css','/ventas/css/normalize.css');
top_htmlhead('','',0,0,'',$arrayofcss);
if ( $_SESSION['uid'] > 0 && $lValid)
{
	header('Location: '.DOL_URL_ROOT.'/ventas/affIndex.php');
	exit;
}

if (!$lValid)
{
	print '<script>window.onload=function(){document.forms["frmLogin"].submit(); }</script> ';

	print '<body class="ventas">';
	print '<section class="loginform cf">';
	print '<h3>'.$langs->trans('Point of sale').'</h3>';
	print '<form id="frmLogin" name="frmLogin" action="index_verif.php" method="POST" accept-charset="utf-8">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
	print '<input type="hidden" name="socid" value="'.$fk_socid.'" />';
	print '<input type="hidden" name="warehouseid" value="'.$fk_entrepotid.'" />';
	print '<input type="hidden" name="selectCASHDESK_ID_BANKACCOUNT_CASH" value="'.$fk_cajaid.'" />';
	print '<input type="hidden" name="selectCASHDESK_ID_BANKACCOUNT_CHEQUE" value="'.$fk_bankid.'" />';
	print '<input type="hidden" name="selectCASHDESK_ID_BANKACCOUNT_CB" value="'.$fk_banktcid.'" />';

	print '<ul>';
	print '<li>';
	print '<label for="txtUsername">'.$langs->trans('User').'</label>';
	print '<input type="text" name="txtUsername" value="'.$user->login.'" placeholder="'.$langs->trans('Your user').'" required>';
	print '</li>';
	print '<li>';
	print '<label for="pwdPassword">'.$langs->trans('Password').'</label>';
	print '<input type="password" name="pwdPassword" value="'.$user->login.'" class="texte_login" placeholder="'.$langs->trans('Your password').'" required>';
	print '</li>';
	print '<br>';
	print '<li>';
	print '<input type="submit" value="'.$langs->trans('Connection').'">';
	print '</li>';
	print '</ul>';
	print '</form>';
	print '</section>';

	print '</body>';
}
print '</html>';
?>