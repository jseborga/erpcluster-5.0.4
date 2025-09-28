<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *   	\file       singlesess/userrestore_card.php
 *		\ingroup    singlesess
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-11-25 14:26
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)
define("NOLOGIN",1);		// This means this output page does not require to be logged.
define("NOCSRFCHECK",1);	// We accept to go on this page from external web site.

// For MultiCompany module.
// Do not use GETPOST here, function is not defined and define must be done before including main.inc.php
// TODO This should be useless. Because entity must be retrieve from object ref and not from url.
$entity=(! empty($_GET['entity']) ? (int) $_GET['entity'] : (! empty($_POST['entity']) ? (int) $_POST['entity'] : 1));
if (is_numeric($entity)) define("DOLENTITY", $entity);

require '../main.inc.php';

// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/singlesess/class/userrestore.class.php');
dol_include_once('/singlesess/class/usersession.class.php');
dol_include_once('/user/class/user.class.php');

// Load traductions files requiredby by page
$langs->load("singlesess");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$code		= GETPOST('code','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_user=GETPOST('search_fk_user','int');
$search_coderest=GETPOST('search_coderest','alpha');
$search_status=GETPOST('search_status','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Userrestore($db);
if ($id > 0)
{
	$objuser = new User($db);
	$objsess = new Usersession($db);
	$objtmp  = new Usersession($db);
	$result=$object->fetch($id);
	$error = 0;
	if ($result < 0) dol_print_error($db);
	if ($object->coderest == $code)
	{
		$res = $objuser->fetch($object->fk_user);
		
		if ($res > 0)
		{
			$filterstatic = " AND t.fk_user = ".$object->fk_user;
			$res = $objsess->fetchAll('','',0,0,array(1=>1),'AND', $filterstatic);
			if ($res>0)
			{

				require_once DOL_DOCUMENT_ROOT.'/singlesess/lib/admin.lib.php';	

				foreach($objsess->lines AS $j => $line)
				{
					$objtmp->fetch($line->id);
					//$resdel = purgeSessions($line->sessionid);
					$objtmp->status = 3;
					$resc = $objtmp->update($objuser);
            		if ($resc<=0) $error++;
				}
			}
		}
		else $error++;
	}
	else $error++;
}
if (!$error)
{
	if (ini_get("session.use_cookies")) 
	{
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]);
	}
	$_SESSION = array();
	session_unset();
	session_destroy();
	$parametros_cookies = session_get_cookie_params(); 
	setcookie(session_name(),0,1,$parametros_cookies["path"]);

	header('Location: '.DOL_URL_ROOT.'/index.php');
	exit;
}

$_SESSION = array();
session_unset();
session_destroy();
$parametros_cookies = session_get_cookie_params(); 
setcookie(session_name(),0,1,$parametros_cookies["path"]);
echo 'No esta permitido para ingresar ';
exit;
?>
