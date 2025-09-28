<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/poa/lib/area.lib.php
 *	\ingroup    Librerias
 *	\brief      Page fiche poa area 
 */

  //lista los usuarios del area que tengan el privilegio indicado
  //se debe tener los objetos areauser y user
function area_user_email($fk_area,$privilege=1)
{
  global $db, $langs, $conf,$objareau,$objuser;
  
  $objareau->getlist($fk_area);
  $array = array();
  if (count($objareau->array) > 0)
    foreach ((array) $objareau->array AS $i => $objau)
      if ($objau->privilege == $privilege)
	if ($objuser->fetch($objau->fk_user) > 0)
	  $array[$objau->fk_user] = array('email'=>$objuser->email,
					  'nameto'=>$objuser->firstname.' '.$objuser->lastname); 
  return $array;
}

//envio de email 
function send_email($emailto,$email_from='',$email_cc='',$tmpsujet,$tmpbody)
{
  global $langs,$db,$conf;
  //para envio de email
  require_once DOL_DOCUMENT_ROOT.'/core/lib/emailing.lib.php';
  require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
  require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
  require_once DOL_DOCUMENT_ROOT.'/comm/mailing/class/mailing.class.php';
  require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

  //sendmail
  // Define output language
  $outputlangs = $langs;
  $newlang='';
  if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
  if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
  if (! empty($newlang))
    {
      $outputlangs = new Translate("",$conf);
      $outputlangs->setDefaultLang($newlang);
    }
  
  $arr_file = array();
  $arr_mime = array();
  $arr_name = array();
  $arr_mime[] = 'text/html';
  //$arr_mime[] = 'aplication/rtf';
  
  //$tmpsujet = $langs->trans('Send email request');
  $sendto   = $emailto;
  if (empty($email_from))
    $email_from = $conf->global->MAIN_MAIL_EMAIL_FROM;
  //$tmpbody = htmlsendemailrech($id,$object->description_job,$url);
  $msgishtml = 1;
  $email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
  $arr_css = array('bgcolor' => '#FFFFCC');
  $mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,$email_cc, '', 0, $msgishtml,$email_errorsto,$arr_css);
  $result=$mailfile->sendfile();
  if ($result)
    {
      $mesg='<div class="ok">'.
	$langs->trans("MailSuccessfulySent",
		      $mailfile->getValidAddress($email_from,2),
		      $mailfile->getValidAddress($sendto,2)).
	'</div>';
    }
  else
    {
      $error++;
      $mesg='<div class="error">'.$langs->trans("ResultKo").
	'<br>'.$mailfile->error.' '.$result.'</div>';
    }
  return array($result,$mesg);
}

//texto Asunto para envio de correo
function email_subject($area,$procedure)
{
  global $langs;
  switch ($area)
    {
    case 'SAJU':
      $text = $langs->trans('Envío tramite para su atención: '.$procedure);
      break;
    case 'DCC':
      $text = $langs->trans('Envío tramite para su atención: '.$procedure);
      break;
    case 'DMMI':
      $text = $langs->trans('Envío tramite para su atención: '.$procedure);
      break;
    case 'DASC':
      $text = $langs->trans('Envío tramite para su atención: '.$procedure);
      break;
    default:
      $text = $langs->trans('Notdefined');
      break;
    }
  return $text;
}

function email_body($obj,$id,$idr,$procedure,$detail,$url,$nameto='')
{
  global $langs;
  //  $url = $dolibarr_main_url_root;
  $outputlangs = $langs;
  $url.= '/poa/workflow/ficher.php?id='.$id.'&action=confread&idr='.$idr;
  $html = '<!DOCTYPE HTML>';
  $html.= '<html>';
  $html.= '<head>';
  $html.= '<meta http-equiv="Content-type" content="text/html; charset=UTF-8">';
  $html.= '</head>';

  $html.= '<body>';
  $html.= '<p>'.$langs->trans('Señor(a):').' '.$nameto.'</p>';
  $html.= '<p>'.$langs->trans('En la fecha se envia el tramite con preventivo Nro.').' <span style="color:#1b1bbb;">'.$obj->nro_preventive.'</span> '.$langs->trans(', con nombre ').' <span style="color:#1b1bbb;">'.$obj->label.'</span> '.$langs->trans('solicitando el proceso de: ').$procedure.'.</p>';
  $html.= '<p>'.$langs->trans('Detalle').': <span style="color:#1b1bbb;">'.$detail.'.</span></p>';

  $html.= '<p>'.$langs->trans('Para confirmar su recepcion, rogamos ingresar a la siguiente direccion').': '.'<a href="'.$url.'">'.$langs->trans('DMMI').'</a>'.',</p>';
  $html.= '<p>'.$langs->trans('Atentamente,').'</p>';
  $html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';
  $html.= '<p>'.$langs->trans('DMMI').'</p>';
  $html.= '</body>';

  $html.= '</html>';
  return $html;
}

?>
