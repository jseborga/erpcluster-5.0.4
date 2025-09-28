<?php 
define("NOLOGIN",1);		// This means this output page does not require to be logged.
define("NOCSRFCHECK",1);	// We accept to go on this page from external web site.

//require '/var/sentora/hostdata/cluster/public_html/beta_cluster_com_bo/main.inc.php';
require '/home/ramiro/public_html/sales4/htdocs/main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/singlesess/class/usersession.class.php';
$object = new Usersession($db);
$objtmp = new Usersession($db);
$objuser = new User($db);
$filterstatic = " AND t.status = 3";

$res = $object->fetchAll('', '', 0,0,array(1=>1), 'AND', $filterstatic);
if ($res >0)
{
	//$dir = '/var/sentora/sessions/';
	$dir = '/var/lib/php/sessions/';
	//recorremos
	foreach ($object->lines AS $i => $line)
	{
		$objuser->fetch($line->fk_user);
		$objtmp->fetch($line->id);
		$objtmp->tms = dol_now();
		$objtmp->ccode = $objtmp->ccode.' a, ';
		$objtmp->update($objuser);
		echo '<hr>cambiando '.$objtmp->ccode;
		//$res=@unlink($dir.$file);
		//if ($res <=0) $error++;
	}
	echo 'procesado';
	if ($error)
		echo ' CON ERROR ';
	else
	{
		//$objtmp->fetch($line->id);
		//$objuser->fetch($line->fk_user);
		//$objtmp->delete($objuser);
	}
}
echo 'fin';
?>