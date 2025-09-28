<?php
//libreria para envio de correos

function send_email_assignment(&$object,$url='')
{
	global $langs,$conf,$user;
	$emailto = $object->email;
	//$emailto = 'ramiroques@gmail.com';
	$error = 0;
	if ($emailto)
	{
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

		$tmpsujet = $langs->trans('Send email assignment');
		$sendto   = $emailto;
		$email_from = $user->email;
		if (empty($email_from)) $email_from = 'info@cluster.com.bo';
		$email_cc = 'ramiroques@gmail.com';
		if ($object->status == 1)
			$tmpbody = htmlsendemailrequested($id,$text,$url);
		if ($object->status == 2)
			$tmpbody = htmlsendemailassign($id,$text,$url);
		$msgishtml = 1;
		$email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
		$arr_css = array('bgcolor' => '#FFFFCC');
		//echo '<hr>'.$tmpsujet.'<hr>'.$sendto.'<hr>'.$email_from.'<hr>'.$tmpbody.'<hr>'. $arr_file.'<hr>'.$arr_mime.'<hr>'.$arr_name.'<hr>'.''.'<hr>'. ''.'<hr>0<hr>'. $msgishtml.'<hr>'.$email_errorsto.'<hr>'.$arr_css;

		$mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,$email_cc, '', 0, $msgishtml,$email_errorsto,$arr_css);
		if ($conf->global->ASSETS_SEND_EMAIL)
			$result=$mailfile->sendfile();
		else
			$result = 1;
		if ($result)
		{
			$mesg=$langs->trans("MailSuccessfulySent",
				$mailfile->getValidAddress($object->email_from,2),
				$mailfile->getValidAddress($object->sendto,2));
			setEventMessages($mesg,null,'mesgs');
		}
		else
		{
			$error++;
			$mesg=$mailfile->error.' '.$result;
			setEventMessages($mesg,null,'errors');
		}
	}
	return $error * -1;
}

function htmlsendemailassign($id,$text,$url)
{
	global $object,$langs,$conf;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/assets/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha validado la asignacion Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('Para el usuario con correo: ').$object->email.',</p>';

	$html.= $text;

	$html.= '<br><p>'.$langs->trans('Para aceptar, rogamos hacer clic en el siguiente enlace').'</p>';
	$html.= '<p><a class="button" href="'.$url.'?id='.$object->id.'&code='.$object->mark.'">'.$langs->trans('Viewassign').'</a></p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$conf->global->MAIN_INFO_SOCIETE_NOM.'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function htmlsendemailrequested($id,$text,$url)
{
	global $object,$langs,$conf;
	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/assets/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<ul>';
	$html.= '<li>'.$langs->trans('Se ha creado la solicitud de asignacion Nro.: ').$object->ref.',</li>';
	$html.= '<li>'.$langs->trans('Por el usuario con correo: ').$object->email.',</li>';

	$html.= $text;

	$html.= '<br><li>'.$langs->trans('Para validar, rogamos hacer clic en el siguiente enlace').'</li>';
	$html.= '<li><a class="button" href="'.$url.'?id='.$object->id.'&code='.$object->mark.'">'.$langs->trans('Viewrequested').'</a></li>';

	$html.= '<li>'.$langs->trans('Atentamente,').'</li>';
	$html.= '<li>'.$conf->global->MAIN_INFO_SOCIETE_NOM.'</li>';
	$html.= '</ul>';
	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

?>