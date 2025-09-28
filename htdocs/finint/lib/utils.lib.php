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

function htmlsendvalidrequest($id='',$code='',$url='')
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
	$html.= '<p>'.$langs->trans('Se ha validado la solicitud por item Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('por el usuario : ').$user->lastname.' '.$user->firstname.'.</p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
  //$html.= '<p>'.$langs->trans('SSA Ingenieria').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

/*cuerpo de correo para no validacion de solicitud por item*/
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

/*cuerpo de correo para no validacion de solicitud por item*/
function htmlsendcloserequest($id='',$code='',$url='')
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
	$html.= '<p>'.$langs->trans('Se ha cerrado la solicitud por item Nro.: ').$object->ref.',</p>';
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

function array_projet($ids='')
{
	global $db;
	$sql = "SELECT f.rowid, f.ref,f.title ";
	$sql.= " FROM ".MAIN_DB_PREFIX."projet AS f";
	$sql.= " WHERE 1 ";
	if ($ids) $sql.= " AND f.rowid IN (".$ids.")";
	$resql = $db->query($sql);
	$aArray = array();
	if ($resql)
	{
		$num = $db->num_rows($resql);
		while ($obj = $db->fetch_object($resql))
		{
			$aArray[$obj->rowid] = $obj->ref.' - '.$obj->title;
		}
	}
	return $aArray;
}

function getselcategorie($selected='')
{
	global $db,$conf;
			//categories
		// Chargement des categories bancaires dans $options
	$nbcategories=0;

	$sql = "SELECT rowid, label";
	$sql.= " FROM ".MAIN_DB_PREFIX."bank_categ";
	$sql.= " WHERE entity = ".$conf->entity;
	$sql.= " ORDER BY label";

	$result = $db->query($sql);
	if ($result)
	{
		$var=True;
		$num = $db->num_rows($result);
		$i = 0;
		$options = '<option value="0" selected>&nbsp;</option>';

		while ($i < $num)
		{
			$obj = $db->fetch_object($result);
			$options.= '<option value="'.$obj->rowid.'" '.($selected == $obj->rowid?'selected':'').'>'.$obj->label.'</option>'."\n";
			$nbcategories++;
			$i++;
		}
		$db->free($result);
	}
	return array($nbcategories,$options);
}

function get_seltypecash($campo='code',$selected='',$active=1)
{
	global $db,$conf;
			//categories
		// Chargement des categories bancaires dans $options
	$nbtypecash=0;

	$sql = "SELECT rowid, code, label";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_type_cash";
	$sql.= " WHERE entity = ".$conf->entity;
	if ($active < 0 || $active > 0)
		$sql.= " AND active = ".$active;
	$sql.= " ORDER BY label";
	$result = $db->query($sql);
	if ($result)
	{
		$var=True;
		$num = $db->num_rows($result);
		$i = 0;
		$options = '<option value="0" selected>&nbsp;</option>';
		while ($i < $num)
		{
			$obj = $db->fetch_object($result);
			$select = '';
			if ($selected && $obj->$campo == $selected)
				$select = 'selected';
			$options.= '<option value="'.$obj->$campo.'" '.$select.'>'.$obj->label.'</option>'."\n";
			$nbtypecash++;
			$i++;
		}
		$db->free($result);
	}
	return array($nbtypecash,$options);
}

function fetch_typecash($id,$code='')
{
	global $db,$conf;
	if (empty($id)) return -1;
	$sql = "SELECT rowid, code, label, recharge";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_type_cash";
	if (!empty($code))
		$sql.= " WHERE code = '".trim($code)."'";
	else
		$sql.= " WHERE rowid = ".$id;

	$result = $db->query($sql);
	if ($result)
	{
		$num = $db->num_rows($result);
		$obj = $db->fetch_object($result);
		$db->free($result);
	}
	return $obj;
}

function get_c_paiement($id='',$code='')
{
	global $db,$conf,$langs,$user;
	$sql = "SELECT id, code, libelle, type, accountancy_code FROM ".MAIN_DB_PREFIX."c_paiement";
	if ($id) $sql.= " WHERE id = ".$id;
	elseif($code) $sql.= " WHERE code LIKE '".TRIM($code)."'";

	$result = $db->query($sql);
	if ($result)
	{
		$num = $db->num_rows($result);
		$obj = $db->fetch_object($result);
		$db->free($result);
		return $obj;
	}
	return 0;
}

?>