<?php 
define("NOLOGIN",1);		// This means this output page does not require to be logged.
define("NOCSRFCHECK",1);	// We accept to go on this page from external web site.

//require '/var/sentora/hostdata/cluster/public_html/pruebavisual_cluster_com_bo/main.inc.php';
require '/home/ramiro/public_html/sales4/htdocs/main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/singlesess/class/usersession.class.php';
$object = new Usersession($db);
$objtmp = new Usersession($db);
$objuser = new User($db);
$filterstatic = " AND t.status = 3";

$res = $object->fetchAll('', '', 0,0,array(1=>1), 'AND', $filterstatic);
if ($res > 0)
{
	//$dir = '/var/sentora/sessions/';
	$dir = '/var/lib/php/sessions/';
	//recorremos
	$error=0;
	foreach ($object->lines AS $i => $line)
	{
		if ($line->status == 3)
		{
			$file = 'sess_'.$line->sessionid;
			$res = @unlink($dir.$file);
			if ($res <0) $error++;
		}
	}
	if (!$error)
	{
		foreach ($object->lines AS $i => $line)
		{
			$objtmp->fetch($line->id);
			$objuser->fetch($line->fk_user);
			$objtmp->delete($objuser);
		}
	}
}
?>