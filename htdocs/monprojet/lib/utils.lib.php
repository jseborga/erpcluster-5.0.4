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
	$from = $conf->global->MAIN_MAIL_EMAIL_FROM;
	if (!empty($emailfrom))
		$from = $emailfrom;
  //$tmpbody = htmlsendemail($id,$code,$url);
	$msgishtml = 1;
	$email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
	$arr_css = array('bgcolor' => '#FFFFCC');
	$mailfile = new CMailFile($tmpsujet,$sendto,$from,$tmpbody, $arr_file,$arr_mime,$arr_name,'', '', 0, $msgishtml,$email_errorsto,$arr_css);
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
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha validado la solicitud por item Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('por el usuario : ').$user->lastname.' '.$user->firstname.',</p>';

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

function getformatdate($seldate,$date)
{
	$ampmo = '';
	switch ($seldate)
	{
		case 0:
	  //dd/mm/yyyy
		$aDateo1 = explode(' ',$date);
		$aDateo = explode('/',$aDateo1[0]);
		$aHouro = explode(':',$aDateo1[1]);
		$ampmo = $aDateo1[2];
		if (empty($ampmo))
			$date = dol_mktime($aHouro[0],$aHouro[1],0,$aDateo[1],$aDateo[0],$aDateo[2],'user');
		else
			$date = dol_mktime($aHouro[0]+12,$aHouro[1],0,$aDateo[1],$aDateo[0],$aDateo[2],'user');
		break;
		case 1:
	  //dd-mm-yyyy
		$aDateo1 = explode(' ',$date);
		$aDateo = explode('-',$aDateo1[0]);
		$aHouro = explode(':',$aDateo1[1]);
		$ampmo = $aDateo1[2];
		if (empty($ampmo))
			$date = dol_mktime($aHouro[0],$aHouro[1],0,$aDateo[1],$aDateo[0],$aDateo[2],'user');
		else
			$date = dol_mktime($aHouro[0]+12,$aHouro[1],0,$aDateo[1],$aDateo[0],$aDateo[2],'user');
		break;
		case 2:
	  //mm/dd/yyyy
		$aDateo1 = explode(' ',$date);
		$aDateo = explode('/',$aDateo1[0]);
		$aHouro = explode(':',$aDateo1[1]);
		$ampmo = $aDateo1[2];
		if (empty($ampmo))
			$date = dol_mktime($aHouro[0],$aHouro[1],0,$aDateo[0],$aDateo[1],$aDateo[2],'user');
		else
			$date = dol_mktime($aHouro[0]+12,$aHouro[1],0,$aDateo[0],$aDateo[1],$aDateo[2],'user');
		break;
		case 3:
	  //mm-dd-yyyy
		$aDateo1 = explode(' ',$date);
		$aDateo = explode('-',$aDateo1[0]);
		$aHouro = explode(':',$aDateo1[1]);
		$ampmo = $aDateo1[2];
		if (empty($ampmo))
			$date = dol_mktime($aHouro[0],$aHouro[1],0,$aDateo[0],$aDateo[1],$aDateo[2],'user');
		else
			$date = dol_mktime($aHouro[0]+12,$aHouro[1],0,$aDateo[0],$aDateo[1],$aDateo[2],'user');
		break;
		case 4:
	  //yyyy/mm/dd
		$aDateo1 = explode(' ',$date);
		$aDateo = explode('/',$aDateo1[0]);
		$aHouro = explode(':',$aDateo1[1]);
		$ampmo = $aDateo1[2];
		if (empty($ampmo))
			$date = dol_mktime($aHouro[0],$aHouro[1],0,$aDateo[1],$aDateo[2],$aDateo[0],'user');
		else
			$date = dol_mktime($aHouro[0]+12,$aHouro[1],0,$aDateo[1],$aDateo[2],$aDateo[0],'user');
		break;
		case 5:
	  //yyyy-mm-dd
		$aDateo1 = explode(' ',$date);
		$aDateo = explode('-',$aDateo1[0]);
		$aHouro = explode(':',$aDateo1[1]);
		$ampmo = $aDateo1[2];
		if (empty($ampmo))
			$date = dol_mktime($aHouro[0],$aHouro[1],0,$aDateo[1],$aDateo[2],$aDateo[0],'user');
		else
			$date = dol_mktime($aHouro[0]+12,$aHouro[1],0,$aDateo[1],$aDateo[2],$aDateo[0],'user');
		break;

	}
	return $date;
}

function get_orderref($line,$aOrdernumref,$aOrdernumparent,$aOrdertask)
{
	global $taskstat,$objecttaskadd;
	//recupero los datos adicionales de la tarea
	$objecttaskadd->fetch(0,$line->id);
	$nLevel = 9;
	if ($objecttaskadd->c_grupo == 1)
	{
		//es grupo
		//buscamos que nivel es si fk_parent != 0
		$nParent = $line->fk_task_parent;
		if ($nParent > 0)
		{
			$nLevel = 0;
			while ($nParent != 0)
			{
				$taskstat->fetch($nParent);
				if ($taskstat->id == $nParent)
				{
//					if ($taskstat->fk_task_parent > 0)
//						$nLevel++;
//					else
//					{
					$nLevel++;
					$nParent = $taskstat->fk_task_parent;
//					}
				}
				else
				{
					$error++;
					echo '<hr.error no se encontro '.$error;
					exit;
				}
			}
		}
		else
			$nLevel = 0;
		$nOrder = count($aOrdernumref);
		$aOrdernumref[$nLevel][$line->id] = count($aOrdernumref[$nLevel])+1;
		$aOrdernumparent[$line->id] = $line->fk_task_parent;
	}
	else
	{
		//es tarea
		$aOrdertask[$line->id] = $line->fk_task_parent;
	}
	return array($aOrdernumref,$aOrdernumparent,$aOrdertask);
}

//funcion para asignar el numero de orden de tarea
function get_orderlast($obj,$group)
{
	//echo '<hr>inicia numerar';
	$lGrupo = 0;
	global $taskadd,$taskstat,$objecttaskadd;
	//asigno variable para modulos
	$aModule = array();
	$aLevel = array();
	$nParent = $obj->fk_task_parent;
	$aOrdernumparent[$obj->id] = $obj->fk_task_parent;

	$aOrdertask[$obj->id] = $obj->fk_task_parent;
	$aOrdernumref = array();
	$aOrder_ref = array();
	$aOrdercount = array();
	//recupero los datos adicionales de la tarea
	//$objecttaskadd->fetch(0,$line->id);
	$nLevel = 0;
		//es grupo
		//buscamos que nivel es si fk_parent != 0
		//$aLevel[$nLevel] = $nParent;
		//$aModule[$nParent] = $nLevel;
	if ($nParent > 0)
	{
		while ($nParent != 0)
		{
			$taskstat->fetch($nParent);
			if ($taskstat->id == $nParent)
			{
				$objecttaskadd->fetch(0,$taskstat->id);
				$aOrder_ref[$nParent] = $objecttaskadd->order_ref;
				$aOrdernumref[$nLevel][$obj->id] = count($aOrdernumref[$nLevel])+1;
				$aOrdernumparent[$nParent] = $taskstat->fk_task_parent;
				$aLevel[$nLevel] = $nParent;
				$aModule[$nParent] = $nLevel;
				$nParent = $taskstat->fk_task_parent;
				$nLevel++;
			}
			else
			{
				$error++;
				echo '<hr.error no se encontro '.$error;
				exit;
			}
		}
	}
	else
	{
		echo '<br>es GRUPO';
		$lGrupo = 1;
		$nOrder = count($aOrdernumref);
		$nOrder = 0;
		$aOrdernumref[$nLevel][$obj->id] = count($aOrdernumref[$nLevel])+1;
		$aOrdernumparent[$obj->id] = $nParent;
		$aModule[$nParent] = $nLevel;
	}
	//modificamos el oreden a Module
	$nCount = count($aModule)-1;
	//echo '<br>aModule ';
	//print_r($aModule);
	foreach ($aModule AS $i => $value)
	{
		$aResult[$i] = $nCount;
		$nCount--;
	}
	//echo '<br>aresult ';
	//print_r($aResult);
	//echo '<br>aordernumref ';
	//print_r($aOrdernumref);
	foreach ((array) $aResult AS $i => $nLevel)
	{
			//echo '<br>revisa  '.$i.' ' .$obj->id;
		$taskadd->get_counttask($i,$obj->fk_project,$obj->id);
			//echo '<hr>cuenta tar ';
			//recorremos
		if (count($taskadd->lines)>0)
		{
			foreach((array) $taskadd->lines AS $j => $objdata)
			{
				//buscamos en projettaskadd
				$objecttaskadd->fetch(0,$objdata->id);
				//echo '<br>contando i '.$i.' j '.$j.' id '.$objdata->id.' c_grupo '.$objecttaskadd->c_grupo;
				if ($objecttaskadd->c_grupo == 1)
				{
					$aOrdercount[$i]['g']++;
					$aOrdercount[$i]['og'] = $objecttaskadd->order_ref;
					$aOrdernumref[$nLevel][$i] = $aOrdercount[$i]['g']+1;
				}
				else
				{
					$aOrdercount[$i]['t']++;
					$aOrdercount[$i]['ot'] = $objecttaskadd->order_ref;
					$aOrdernumref[$nLevel][$i] = $aOrdercount[$i]['t']+1;
				}
			}
		}
		else
		{
			if ($lGrupo == 1)
			{
				$aOrdercount[$i]['g']++;
				$aOrdercount[$i]['og'] = 1;
				//$aOrdernumref[$nLevel][$i] = $aOrdercount[$i]['g'];
			}
			else
			{
				$aOrdercount[$i]['t']++;
				$aOrdercount[$i]['ot'] = 1;
				//$aOrdernumref[$nLevel][$i] = $aOrdercount[$i]['t'];
			}
		}
	}
	//echo '<pre>ordernumref';

	//print_r($aOrdernumref);
	//echo 'ordernumparent';
	//print_r($aOrdernumparent);
	//echo 'ordertask';
	//print_r($aOrdertask);
	//echo 'order_ref';
	//print_r($aOrder_ref);
	//echo 'ordercount';
	//print_r($aOrdercount);
	//echo '</pre>';

//	return array($aResult,$aOrdercount);
	return array($aOrdernumref,$aOrdernumparent,$aOrdertask,$aOrder_ref,$aOrdercount);
	//obtenemos cual es el ultimo registro por cada grupo
}

function set_order_ref(array $aOrdernumref=array(),array $aOrdernumparent=array(),$aOrdertask,array $aOrder_ref=array(),array $aOrdercount= array())
{
	//numeracion del order_ref

	$aOrdernew = array();
	$nDigit = 7;
	$nOrder = 1000000;
	$cDigit = '1000000';
	if (count($aOrdernumref)>0)
	{
		$nDigitc = $nDigit;
		foreach ($aOrdernumref AS $i => $aData) //$i == nivel
		{
			$nDigitc = $nDigit - $i - 1;
			foreach ($aData AS $j => $value)//$j == idtask, $value==ultimo valor
			{
				if ($aOrdernumparent[$j])
				{
					$aOrdernew[$i][$j] = $aOrdernew[$i-1][$aOrdernumparent[$j]] + ($value * substr($cDigit,0,$nDigitc));
				}
				else
				{
					$aOrdernew[$i][$j] = $value * $nOrder;
				}
			}
		}
	}
	//print_r($aOrdernew);
	$aOrderref = array();
	foreach ($aOrdernew AS $i => $aData)
	{
		foreach($aData AS $j => $value)
		{
			echo '<br>value '.$value;
			if ($aOrder_ref[$j])
				$aOrderref[$j] = $aOrder_ref[$j];
			else
				$aOrderref[$j] = $value;
		}
	}
	//echo '<br>';
	//print_r($aOrderref);

	//recorremos las tareas para numerar
	$aOrdernumtask = array();
	foreach ((array) $aOrdertask AS $j => $value)
	{
		echo '<br>rec1 '.$j.' '.$value;
		if (!empty($aOrder_ref[$value]))
		{
			$nLen = $aOrdercount[$value]['t']+1;
			$nValueparent = $aOrder_ref[$j];
			//$nLen = $aOrdernumtask[$value]+1;
			$nNew = $nValueparent + $nLen;
			$aOrdernumtask[$value] = $nLen;
			$aOrderref[$j] = $nNew;
		}
		else
		{
			if ($aOrdernumparent[$j] != 0)
			{
				echo '<br>asigna valor '.$value;
				echo '<br>nvalueparent ' .$nValueparent = $aOrderref[$j];
				echo '<br>nlen ' .$nLen = $aOrdernumtask[$value]+1;
				echo '<br>nnew ' .$nNew = $nValueparent + $nLen;
				$aOrdernumtask[$value] = $nLen;
				$aOrderref[$j] = $nNew;
			}
		}
	}
	//echo '<br>resultado';
	//print_r($aOrderref);
	return $aOrderref;
}

//numeracion order ref nuevo
//funcion para asignar el numero de orden de tarea
function get_orderlastnew($id,$fk_project,$data,array $aRef=array(),array $aNumberref=array(),array $aRefnumber=array())
{
	global $taskadd,$taskstat,$objecttaskadd;
	if (empty($data['fk_task_parent'])) $lGrupo = 0;
	//asigno variable para modulos
	$aModule = array();
	$aLevel = array();
	$nParent = $data['fk_task_parent'];
	$aOrdernumparent[$id] = $data['fk_task_parent'];

	$aOrdertask[$id] = $data['fk_task_parent'];
	$aOrdernumref = array();
	$aOrder_ref = array();
	$aOrdercount = array();
	$nDigit = 7;
	$nOrder = 1000000;
	$cDigit = '1000000';
	//recupero los datos adicionales de la tarea
	//$objecttaskadd->fetch(0,$line->id);
	$nLevel = 0;
		//es grupo
		//buscamos que nivel es si fk_parent != 0
		//$aLevel[$nLevel] = $nParent;
		//$aModule[$nParent] = $nLevel;
	//echo '<hr>id '.$id;
	//echo '<br>nparent '.$nParent;
	if ($nParent > 0)
	{
		//recuperamos que numero tiene el padre
		//echo '<br>nPadre '.
		$nPadre = $aNumberref[$nParent];
		if ($data['group'])
		{
			//echo ';  es grupoint ';
			//es grupo procesamos como grupo
			//echo '; nreg= '.
			$nreg = count($aRefnumber[$data['level']-1][$nParent]);
			if (empty($nreg))
			{
				$aRef[$data['level']][$id] = $nPadre + substr($cDigit,0,$nDigit-$data['level']) * 1;
				//echo  '; id= '.$id;
				//echo '; numberref= '.
				$aNumberref[$id] = $aRef[$data['level']][$id];
			}
			else
			{
				$aRef[$data['level']][$id] = $nPadre + substr($cDigit,0,$nDigit-$data['level']) * $nreg;
				//echo '; id = '.$id;
				//echo '; numbreref mult '.
				$aNumberref[$id] = $aRef[$data['level']][$id];
			}
			$aRefnumber[$data['level']-1][$nParent][$id] = $id;
		}
		else
		{
			//echo ' No es grupo;  level '.$data['level'].'; padre= '.$nPadre;
			//NO es grupo procesamos como tarea simple
			//echo '; nreg= '.
			$nreg = count($aRefnumber[$data['level']-1][$nParent]);
			if (empty($nreg))
			{
				$aRef[$data['level']][$id] = $nPadre + 1;
				//echo  '; id= '.$id;
				//echo '; numberref= '.
				$aNumberref[$id] = $aRef[$data['level']][$id];
			}
			else
			{
				$aRef[$data['level']][$id] = $nPadre +$nreg + 1;
				//echo  '; id= '.$id;
				//echo '; numberref= '.
				$aNumberref[$id] = $aRef[$data['level']][$id];
			}
			$aRefnumber[$data['level']-1][$nParent][$id] = $id;
		}
	}
	else
	{
		$lGrupo = 1;
		//echo '<br>grupo ';
		//busco el maximo numero registrado
		//echo ' nreg= '.
		$nreg = count($aRefnumber[$data['level']]);
		if (empty($nreg))
		{
			$aRef[$data['level']][$id] = $nOrder * ($nreg + 1);
			//echo  '; id= '.$id;
			//echo '; numberref= '.
			$aNumberref[$id] = $aRef[$data['level']][$id];
			$aRefnumber[0][$id][0][$id] = array();
		}
		else
		{
			$aRef[$data['level']][$id] = $nOrder * ($nreg+1);
			//echo  '; id= '.$id;
			//echo '; numberref== '.
			$aNumberref[$id] = $aRef[$data['level']][$id];
			$aRefnumber[0][$id][0][$id] = array();
		}
	}
	return array($aRef,$aNumberref,$aRefnumber);
	//obtenemos cual es el ultimo registro por cada grupo
}

function set_order_refnew(array $aOrdernumref=array(),array $aOrdernumparent=array(),$aOrdertask,array $aOrder_ref=array(),array $aOrdercount= array())
{
	//numeracion del order_ref

	$aOrdernew = array();
	$nDigit = 7;
	$nOrder = 1000000;
	$cDigit = '1000000';
	if (count($aOrdernumref)>0)
	{
		$nDigitc = $nDigit;
		foreach ($aOrdernumref AS $i => $aData) //$i == nivel
		{
			$nDigitc = $nDigit - $i - 1;
			foreach ($aData AS $j => $value)//$j == idtask, $value==ultimo valor
			{
				if ($aOrdernumparent[$j])
				{
					$aOrdernew[$i][$j] = $aOrdernew[$i-1][$aOrdernumparent[$j]] + ($value * substr($cDigit,0,$nDigitc));
				}
				else
				{
					$aOrdernew[$i][$j] = $value * $nOrder;
				}
			}
		}
	}
	//print_r($aOrdernew);
	$aOrderref = array();
	foreach ($aOrdernew AS $i => $aData)
	{
		foreach($aData AS $j => $value)
		{
			echo '<br>value '.$value;
			if ($aOrder_ref[$j])
				$aOrderref[$j] = $aOrder_ref[$j];
			else
				$aOrderref[$j] = $value;
		}
	}
	//echo '<br>';
	//print_r($aOrderref);

	//recorremos las tareas para numerar
	$aOrdernumtask = array();
	foreach ((array) $aOrdertask AS $j => $value)
	{
		echo '<br>rec1 '.$j.' '.$value;
		if (!empty($aOrder_ref[$value]))
		{
			$nLen = $aOrdercount[$value]['t']+1;
			$nValueparent = $aOrder_ref[$j];
			//$nLen = $aOrdernumtask[$value]+1;
			$nNew = $nValueparent + $nLen;
			$aOrdernumtask[$value] = $nLen;
			$aOrderref[$j] = $nNew;
		}
		else
		{
			if ($aOrdernumparent[$j] != 0)
			{
				echo '<br>asigna valor '.$value;
				echo '<br>nvalueparent ' .$nValueparent = $aOrderref[$j];
				echo '<br>nlen ' .$nLen = $aOrdernumtask[$value]+1;
				echo '<br>nnew ' .$nNew = $nValueparent + $nLen;
				$aOrdernumtask[$value] = $nLen;
				$aOrderref[$j] = $nNew;
			}
		}
	}
	echo '<br>resultado';
	print_r($aOrderref);
	return $aOrderref;
}

function generarcodigoale($longitud)
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

function fetch_categorie_table($fk_product,$table)
{
	global $db,$conf,$langs;

	$sql = "SELECT fk_categorie FROM " . MAIN_DB_PREFIX . "categorie_" . $table;
	$sql .= " WHERE fk_product = " . $fk_product;
	$resql = $db->query($sql);
	if ($resql) 
	{
		if ($db->num_rows($resql) > 0)
		{
			$i =0;
			$num = $db->num_rows($resql);
			while ($i < $num)
			{
				$data[] = $db->fetch_object($resql);
				$i++;
			}
			return $data;
		} else {
			$error=$db->error().' sql='.$sql;
			return -1;
		}
	}
}

//recupera la categoria para recursos,segun la configuracion
//variable recibida Id del proyecto
//
function get_categorie($id)
{
	global $conf,$db,$langs;
	global $budget;

	$lConfglobalstr = true;
	$fk_budget = 0;
	if ($conf->budget->enabled)
	{
		$filterstatic = " AND t.fk_projet =".$id;
		$nres = $budget->fetchAll('', '',0,0,array(1=>1),'AND',$filterstatic,true);
		if ($nres == 1)
		{
			$lConfglobalstr = false;
			$fk_budget = $budget->id;
			//procesamos para obtener la estructura
			$resstr = get_structure_budget($budget->id);
			if ($resstr <=0)
			{
				setEventMessages($langs->trans('No tiene configurado la estructura del presupuesto'),null,'errors');
			}
			else
			{
				$aStrbudget = unserialize($_SESSION['aStrbudget']);
				$aCat = $aStrbudget[$budget->id]['aStrcatcode'];
			}
		}
		elseif ($nres < 0 || $nres > 1)
		{
			setEventMessages($langs->trans('No tiene configurado la estructura del presupuesto.'),null,'errors');
		}
		else
			$lConfglobalstr = true;
	}
	if ($lConfglobalstr)
	{
		//utilizamos la estructura definida en variables globales
		$aCat[$conf->global->MONPROJET_CODE_CATEGORY_MATERIAL] = '';
		$aCat[$conf->global->MONPROJET_CODE_CATEGORY_WORKFORCE] = '';
		$aCat[$conf->global->MONPROJET_CODE_CATEGORY_MACHINERY] = '';
		//buscamos y reemplazamos los nombres de las categorias
		$aStrgroupcat['MA'] = $conf->global->MONPROJET_CODE_CATEGORY_MATERIAL;
		$aStrgroupcat['MO'] = $conf->global->MONPROJET_CODE_CATEGORY_WORKFORCE;
		$aStrgroupcat['MQ'] = $conf->global->MONPROJET_CODE_CATEGORY_MACHINERY;
		$aStrcatgroup[$conf->global->MONPROJET_CODE_CATEGORY_MATERIAL] = 'MA';	
		$aStrcatgroup[$conf->global->MONPROJET_CODE_CATEGORY_WORKFORCE] = 'MO';	
		$aStrcatgroup[$conf->global->MONPROJET_CODE_CATEGORY_MACHINERY] = 'MQ';	
		$categorie = new Categorie($db);
		foreach ($aCat AS $j => $value)
		{
			if ($categorie->fetch($j)>0)
			{
				$aCat[$j] = $categorie->label;
			}
		}
		$_SESSION['aStrbudget'] = serialize(array($fk_budget=>array('aStrgroupcat'=> $aStrgroupcat, 'aStrcatgroup'=> $aStrcatgroup)));
	}
	$_SESSION['aCat'] = serialize($aCat);
	return array($fk_budget,$aCat);
}

//parametros fijos
function load_type_resource()
{
	global $langs,$conf;
	if ($conf->adherent->enabled && $conf->salary->enabled)
		$array[] = array('code'=>'MOD','label'=>$langs->trans('Workforce'),'object'=>'adherent','fk_object'=>'fk_member','objectdet'=>'pcontrat','fk_objectdet'=>'fk_pcontrat','group'=>'MO');
	if ($conf->purchase->enabled)
		$array[]=array('code'=>'MODext','label'=>$langs->trans('ExternalWorkforce'),'object'=>'societe','fk_object'=>'fk_soc','objectdet'=>'CommandeFournisseurLigne','fk_objectdet'=>'fk_commande','group'=>'MO');
	if ($conf->product->enabled)
		$array[] = array('code'=>'MAT','label'=>$langs->trans('Materials'),'object'=>'product','fk_object'=>'product','objectdet'=>'product','fk_objectdet'=>'product','group'=>'MA');
	if ($conf->purchase->enabled)
		$array[]=array('code'=>'MAText','label'=>$langs->trans('ExternalMaterial'),'object'=>'societe','fk_object'=>'fk_soc','objectdet'=>'CommandeFournisseurLigne','fk_objectdet'=>'fk_commande','group'=>'MA');
	if ($conf->assets->enabled)
		$array[]=array('code'=>'MAQ','label'=>$langs->trans('EquipmentAndMachinery'),'object'=>'assets','fk_object'=>'fk_asset','objectdet'=>'assets','fk_objectdet'=>'fk_asset_det','group'=>'MQ');
	if ($conf->purchase->enabled)
		$array[]=array('code'=>'MAQext','label'=>$langs->trans('ExternalEquipmentAndMachinery'),'object'=>'societe','fk_object'=>'fk_soc','objectdet'=>'CommandeFournisseurLigne','fk_objectdet'=>'fk_commande','group'=>'MQ');

	$aTypeResource = $array;
	/*
	$aTypeResource = array(
		1=>array('code'=>'MOD','label'=>$langs->trans('Workforce'),'object'=>'adherent','fk_object'=>'fk_member','objectdet'=>'pcontrat','fk_objectdet'=>'fk_pcontrat','group'=>'MO'),
		2=>array('code'=>'MODext','label'=>$langs->trans('ExternalWorkforce'),'object'=>'societe','fk_object'=>'fk_soc','objectdet'=>'commande_fournisseurdet','fk_objectdet'=>'fk_commande','group'=>'MO'),
		3=>array('code'=>'MAT','label'=>$langs->trans('Materials'),'object'=>'product','fk_object'=>'product','objectdet'=>'product','fk_objectdet'=>'product','group'=>'MA'),
		4=>array('code'=>'MAText','label'=>$langs->trans('ExternalMaterial'),'object'=>'societe','fk_object'=>'fk_soc','objectdet'=>'commande_fournisseurdet','fk_objectdet'=>'fk_commande','group'=>'MA'),
		5=>array('code'=>'MAQ','label'=>$langs->trans('EquipmentAndMachinery'),'object'=>'assignment','fk_object'=>'fk_assignment','objectdet'=>'assets_assignment_det','fk_objectdet'=>'fk_asset_det','group'=>'MQ'),
		6=>array('code'=>'MAQext','label'=>$langs->trans('ExternalEquipmentAndMachinery'),'object'=>'societe','fk_object'=>'fk_soc','objectdet'=>'commande_fournisseurdet','fk_objectdet'=>'fk_commande','group'=>'MQ'),
		7=>array('code'=>'GG','label'=>$langs->trans('GeneralExpenses'),'object'=>'societe','fk_object'=>'fk_soc','group'=>'OT'),
		);
	*/
	return $aTypeResource;
}

function fetch_unit($id=0,$ref='')
{
	global $langs,$db;
	$sql = " SELECT rowid, code, label, short_label ";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_units";
	if ($id>0)
		$sql.= " WHERE rowid = ".$id;
	else $sql.= " WHERE code = '".$ref."'";
	$sql;
	$resql = $db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		if ($num>0)
		{
			$obj = $db->fetch_object($resql);
			return $obj;
		}
		else
			return $num;
	}
	else
		return -1;
}
?>