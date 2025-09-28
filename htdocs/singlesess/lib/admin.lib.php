<?php  

/**
 * 	Purge existing sessions
 *
 * 	@param		int		$mysessionid		To avoid to try to delete my own session
 * 	@return		int							>0 if OK, <0 if KO
 */
function purgeSessions($sessionid)
{
	global $conf;

	$arrayofSessions = array();
	$sessPath = ini_get("session.save_path")."/";
	//$sessPath = '/var/sentora/sessions/';
	dol_syslog('admin.lib:purgeSessions mysessionid='.$sessionid.' sessPath='.$sessPath);

	$error=0;
	$dh = @opendir(dol_osencode($sessPath));
	echo '<hr>antes de ';
	$archivo = $sessPath.'sess_'.$sessionid;
	echo '<hr>resultado '.$res = @unlink($archivo);

	/*
	while(($file = @readdir($dh)) !== false)

	//$dh = opendir($sessPath); //ruta actual
	//echo '<hr>ruta actual '.$dh;
	//while ($file = readdir($dh)) //obtenemos un archivo y luego otro sucesivamente
	{
			//echo ' <hr>file '.$file;
		if ($file != "." && $file != "..")
		{
			$fullpath = $sessPath.$file;
			if(! @is_dir($fullpath))
			{
				//echo '<br>es archivo '.$fullpath;


				$sessValues = file_get_contents($fullpath);	// get raw session data

                if (preg_match('/dol_login/i',$sessValues) && // limit to dolibarr session
                preg_match('/dol_entity\|s:([0-9]+):"('.$conf->entity.')"/i',$sessValues) && // limit to current entity
                preg_match('/dol_company\|s:([0-9]+):"('.$conf->global->MAIN_INFO_SOCIETE_NOM.')"/i',$sessValues)) // limit to company name
                {
                    $tmp=explode('_', $file);
                    $idsess=$tmp[1];
                    // We remove session if it's not ourself
                    if ($idsess == $sessionid)
                    {
                    	//echo '<br>borrando ';
                        $res=@unlink($fullpath);
                        if (! $res) $error++;
                    }
                }


                //$sessValues = file_get_contents($fullpath);
                //$tmpx=explode('_', $file);
                //$idsess = $tmpx[1];
                //echo ' compara '.$idsess.' == '.$sessionid;
                    // We remove session if it's not ourself
                //if ($idsess == $sessionid)
                //{
                //	echo ' borrando ';
                //	$res=@unlink($fullpath);
                //	if (! $res) $error++;
                //}
            }
        }
    }
    @closedir($dh);
	*/
    if (! $error) return 1;
    else return -$error;
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

/*
Funcion para envio de correos 
*/
function send_email($emailfrom,$emailto,$tmpsujet,$tmpbody)
{
	//para envio email
	require_once DOL_DOCUMENT_ROOT.'/core/lib/emailing.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
	require_once DOL_DOCUMENT_ROOT.'/comm/mailing/class/mailing.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

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

function htmlsend($id='',$code='',$url='')
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
	$html.= '<p>'.$langs->trans('Se ha solicitado el reestablecimiento de acceso para : ').$user->lastname.' '.$user->firstname.',</p>';
	$html.= '<p>'.$langs->trans('Accesa a la siguiente direccion para reestablecer : ').'<a href="'.$url.'?id='.$id.'&code='.$code.'">'.$langs->trans('Recuperar').'</a>'.'.</p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
  //$html.= '<p>'.$langs->trans('SSA Ingenieria').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}
?>