<?php
  //utilitarios
function reArrayFiles(&$file_post,$aCode)
{
	$file_ary = array();
	$file_count = count($file_post['name']);
	$file_keys = array_keys($file_post);
	$lLoop = true;
	foreach((array) $aCode AS $i => $label)
	{
		foreach ($file_keys AS $key)
			$file_ary[$i][$key] = $file_post[$key][$i];
	}
	return $file_ary;
}
function img_print($file,$title='',$module='',$img='')
{
	$aFile = explode('.',$file);
	$filext = '';
	$nLoop = count($aFile)-1;
	$fileext = STRTOUPPER($aFile[$nLoop]);
	if ($fileext == 'DOC' || $fileext == 'DOCX')
		$fext = 'doc.png';
	if ($fileext == 'XLS' || $fileext == 'XLSX')
		$fext = 'xls.png';
	if ($fileext == 'PNG' || $fileext == 'JPG' || $fileext == 'JPEG' || $fileext == 'BMP' || $fileext == 'GIF')
		$fext = 'img.png';

	if (STRTOUPPER($fileext) == 'PDF')
		$fext = 'pdf.png';
	return img_picto($title,($module?DOL_URL_ROOT.'/'.$module.($img?'/'.$img:'').'/':'').$fext,'',($module?1:''));
}

/**
 * Remove a non empty directory
 * @param string $path Folder Path
 * @return bool
 */
function removeDirectory($path)
{
	$path = rtrim( strval( $path ), '/' ) ;

	$d = dir( $path );

	if( ! $d )
		return false;

	while ( false !== ($current = $d->read()) )
	{
		if( $current === '.' || $current === '..')
			continue;

		$file = $d->path . '/' . $current;

		if( is_dir($file) )
			removeDirectory($file);

		if( is_file($file) )
			unlink($file);
	}

	rmdir( $d->path );
	$d->close();
	return true;
}

/*
Funcion para envio de correos
*/
function send_email($emailfrom,$emailto,$tmpsujet,$tmpbody)
{
	global $conf,$langs;
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

  //$tmpsujet = $langs->trans('Generation of ticket');
	$sendto   = $emailto;
	if (empty($emailfrom))
		$email_from = $conf->global->MAIN_MAIL_EMAIL_FROM;
	else
		$email_from = $emailfrom;
  //$tmpbody = htmlsendemail($id,$code,$url);
	$msgishtml = 1;
	$email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
	$arr_css = array('bgcolor' => '#FFFFCC');
	$mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,'', '', 0, $msgishtml,$email_errorsto,$arr_css);
	$result=$mailfile->sendfile();
	if ($result)
	{
		$mesg='<div class="ok">'.$langs->trans("MailSuccessfulySent",$mailfile->getValidAddress($object->email_from,2),$mailfile->getValidAddress($object->sendto,2)).'</div>';
	  // header("Location: ficheemail.php?id=".$id.'&action=edit&code='.$code);
	  // exit;
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("ResultKo").'<br>'.$mailfile->error.' '.$result.'</div>';
		$action = 'create';
	}
	return array($result,$mesg);
}

/*cuerpo de correo para validacion de solicitud por item*/

function htmlsendapprovallicence($id='',$code='',$url='')
{
	global $object,$langs,$adherent,$user;
  //  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/assistance/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= UTF-8>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('La solicitud de licencia/permiso Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('a sido aprobado por : ').$user->lastname.' '.$user->firstname.'.</p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

/*cuerpo de correo para validacion de solicitud por item*/
function htmlsendnovalidrequest($id='',$code='',$url='')
{
	global $object,$langs,$objAdherent,$user;
  //  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/request/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha devuelto la solicitud por item Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('por el usuario : ').$user->lastname.' '.$user->firstname.'.</p>';
	$html.= '<p>'.$code.'.</p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
  //$html.= '<p>'.$langs->trans('SSA Ingenieria').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

/*cuerpo de correo para aceptacion de solicitud por item*/
function htmlsendacceptrequest($id='',$code='',$url='')
{
	global $object,$langs,$objAdherent,$user;
  //  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/request/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha aprobado la solicitud por item Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$code.',</p>';
	$html.= '<p>'.$langs->trans('por el usuario : ').$user->lastname.' '.$user->firstname.'.</p>';
	$html.= '<p>'.$langs->trans('Favor verifique y proceda con los trabajos programados').'.</p>';
	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
  //$html.= '<p>'.$langs->trans('SSA Ingenieria').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function generarcodigo($longitud)
{
	$key = '';
	$pattern = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$max = strlen($pattern)-1;
	for($i=0; $i < $longitud; $i++)
	{
		$key .= $pattern{mt_rand(0,$max)};
	}
	return $key;
}

function zerofill($valor, $longitud)
{
	$res = str_pad($valor, $longitud, '0', STR_PAD_LEFT);
	return $res;
}

//calculo de vacaciones por miembro
//$id = id del miembro
function calc_vacation(User $user, $id)
{
	global $langs, $conf, $db;
	$now = dol_now();
	dol_include_once('/assistance/class/membercas.class.php');
	dol_include_once('/salary/class/pcontractext.class.php');
	$objMembercas = new Membercas($db);
	//primero verificamos el registro del cas
	$res = $objMembercas->fetch(0,$id);
	$nYear =0;
	$nMonth = 0;
	$nDay = 0;
	if ($res > 0)
	{
		//convertimos el tiempo calificado en dias
		$nYear = $objMembercas->number_year;
		$nMonth = $objMembercas->number_month;
		$nDay = $objMembercas->number_day;
	}
	//calculamos el tiempo actual de trabajo con el ultimo contrato
	$objContract = new Pcontractext($db);
	$res = $objContract->fetch_vigent($id,1);
	$days = 0;
	$date_ini = 0;
	if ($res>0)
	{
		$date_ini = $objContract->date_ini;
		//calculamos el tiempo trabajado hasta la fecha
		$days = num_between_day($date_ini, $now, 1);
	}
	$lVacation = false;
	//verificamos si corresponde dar vacacion
	$nDaydef = ($conf->global->ASSISTANCE_NUMBER_DAY_FOR_GESTION?$conf->global->ASSISTANCE_NUMBER_DAY_FOR_GESTION:365);
	if ($days>$nDaydef)
		$lVacation = true;
	//actualizamos y verificamos los valores
	$nYearcas = $nYear;
	$nYearcas+= $nDay/$nDaydef;
	$nYearcas+=$nMonth/12;
	$nYearcas+=$days/$nDaydef;
	return array($lVacation,$nYearcas,$days,$date_ini);
}
?>