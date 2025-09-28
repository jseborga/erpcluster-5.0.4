<?php
/* Copyright (C) 2004-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2016 Regis Houssin        <regis.houssin@capnetworks.com>
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
 * or see http://www.gnu.org/
 */

/**
 *		\file       htdocs/viewimage.php
 *		\brief      Wrapper to show images into Dolibarr screens
 *      \remarks    Call to wrapper is '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=diroffile&file=relativepathofofile&cache=0">'
 */

//if (! defined('NOREQUIREUSER'))	define('NOREQUIREUSER','1');	// Not disabled cause need to load personalized language
//if (! defined('NOREQUIREDB'))		define('NOREQUIREDB','1');		// Not disabled cause need to load personalized language
if (! defined('NOREQUIRESOC'))		define('NOREQUIRESOC','1');
if (! defined('NOREQUIRETRAN'))		define('NOREQUIRETRAN','1');
if (! defined('NOCSRFCHECK'))		define('NOCSRFCHECK','1');
if (! defined('NOTOKENRENEWAL'))	define('NOTOKENRENEWAL','1');
if (! defined('NOREQUIREMENU'))		define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))		define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))		define('NOREQUIREAJAX','1');
if (! defined('NOREQUIREHOOK'))		define('NOREQUIREHOOK','1');
	// Disable "main.inc.php" hooks
// Pour autre que companylogo, on charge environnement + info issus de logon comme le user
if ((isset($_GET["modulepart"]) && $_GET["modulepart"] == 'companylogo') && ! defined("NOLOGIN")) define("NOLOGIN",'1');

/**
 * Header empty
 *
 * @return	void
 */
function llxHeader() { }
/**
 * Footer empty
 *
 * @return	void
 */
function llxFooter() { }

//require ("../main.inc.php");
//echo '<hr>root '. DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/mfiles.lib.php';


define ('DOL_DOCUMENT_ROOT','/var/sentora/hostdata/zadmin/public_html/demo_cluster_com_bo');


//define ('DOL_DOCUMENT_ROOT','/var/sentora/hostdata/jseborga/public_html/pmr_ingenieria_com_bo/htdocs');
//define ('DOL_DOCUMENT_ROOT','/var/www/html/pmr');
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/mfiles.lib.php';
//$action=GETPOST('action','alpha');
//$original_file=GETPOST("file");
//$modulepart=GETPOST('modulepart','alpha');
//$urlsource=GETPOST("urlsource");
//$entity=GETPOST('entity')?GETPOST('entity','int'):$conf->entity;

$action=(isset($_GET['action'])?$_GET['action']:'');
$original_file=$_GET["file"];
$modulepart=$_GET['modulepart'];
$urlsource=(isset($_GET["urlsource"])?$_GET['urlsource']:'');
$entity=$_GET['entity']?$_GET['entity']:$conf->entity;
// Security check
if (empty($modulepart)) accessforbidden('Bad value for parameter modulepart');



/*
 * Actions
 */

// None



/*
 * View
 */

if ($_GET["cache"])
{
    // Important: Following code is to avoid page request by browser and PHP CPU at
    // each Dolibarr page access.
    if (empty($dolibarr_nocache))
    {
        header('Cache-Control: max-age=3600, public, must-revalidate');
        header('Pragma: cache'); 
              // This is to avoid having Pragma: no-cache
    }
    else header('Cache-Control: no-cache');
    //print $dolibarr_nocache; exit;
}

// Define mime type
$type = 'application/octet-stream';
if (! empty($_GET["type"])) $type=$_GET["type"];
else $type=dol_mimetype($original_file);

// Suppression de la chaine de caractere ../ dans $original_file
$original_file = str_replace("../","/", $original_file);
// Find the subdirectory name as the reference
$refname=basename(dirname($original_file)."/");
// Security check
if (empty($modulepart)) accessforbidden('Bad value for parameter modulepart');
$conf = DOL_DOCUMENT_ROOT.'/documents/projet';
if ($modulepart == 'monprojet')
    $conf = DOL_DOCUMENT_ROOT.'/documents/'.$modulepart;
$check_access = mdol_check_secure_access_document($modulepart,$original_file,$entity,'',$refname,$conf);
$accessallowed              = $check_access['accessallowed'];
$sqlprotectagainstexternals = $check_access['sqlprotectagainstexternals'];
$original_file              = $check_access['original_file'];
//$accessallowed = 1;
//print_r($check_access);exit;
// Security:
// Limit access if permissions are wrong
if (! $accessallowed)
{
    accessforbidden();
}
// Security:
// On interdit les remontees de repertoire ainsi que les pipe dans les noms de fichiers.
if (preg_match('/\.\./',$original_file) || preg_match('/[<>|]/',$original_file))
{
    dol_syslog("Refused to deliver file ".$original_file, LOG_WARNING);
    // Do no show plain path in shown error message
    dol_print_error(0,'Error: File '.$_GET["file"].' does not exists');
    exit;
}



if ($modulepart == 'barcode')
{
    $generator=GETPOST("generator","alpha");
    $code=GETPOST("code");
    $encoding=GETPOST("encoding","alpha");
    $readable=GETPOST("readable")?GETPOST("readable","alpha"):"Y";

    if (empty($generator) || empty($encoding))
    {
        dol_print_error(0,'Error, parameter "generator" or "encoding" not defined');
        exit;
    }

    $dirbarcode=array_merge(array("/core/modules/barcode/doc/"),$conf->modules_parts['barcode']);

    $result=0;

    foreach($dirbarcode as $reldir)
    {
        $dir=dol_buildpath($reldir,0);
        $newdir=dol_osencode_($dir);

        // Check if directory exists (we do not use dol_is_dir to avoid loading files.lib.php)
        if (! is_dir($newdir)) continue;

        $result=@include_once $newdir.$generator.'.modules.php';
        if ($result) break;
    }

    // Load barcode class
    $classname = "mod".ucfirst($generator);
    $module = new $classname($db);
    if ($module->encodingIsSupported($encoding))
    {
        $result=$module->buildBarCode($code,$encoding,$readable);
    }
}
else					// Open and return file
{
    clearstatcache();
    // Output files on browser
    //dol_syslog("viewimage.php return file $original_file content-type=$type");
    // This test is to avoid error images when image is not available (for example thumbs).
    if (! file_exists($original_file))
    {
        $original_file=DOL_DOCUMENT_ROOT.'/public/theme/common/nophoto.png';
        /*$error='Error: File '.$_GET["file"].' does not exists or filesystems permissions are not allowed';
        dol_print_error(0,$error);
        print $error;
        exit;*/
    }
    // Les drois sont ok et fichier trouve
    if ($type)
    {
        header('Content-Disposition: inline; filename="'.basename($original_file).'"');
        header('Content-type: '.$type);
    }
    else
    {
        header('Content-Disposition: inline; filename="'.basename($original_file).'"');
        header('Content-type: image/png');
    }
    $original_file_osencoded=dol_osencode($original_file);

    readfile($original_file_osencoded);
}

function dol_osencode_($str)
{
    global $conf;
    $tmp=ini_get("unicode.filesystem_encoding");                        // Disponible avec PHP 6.0
    if (empty($tmp) && ! empty($_SERVER["WINDIR"])) $tmp='iso-8859-1';  // By default for windows
    if (empty($tmp)) $tmp='utf-8';                                      // By default for other
    if (! empty($conf->global->MAIN_FILESYSTEM_ENCODING)) $tmp=$conf->global->MAIN_FILESYSTEM_ENCODING;

    if ($tmp == 'iso-8859-1') return utf8_decode($str);
    return $str;
}

/**
 * Return if path is a file
 *
 * @param   string      $pathoffile     Path of file
 * @return  boolean                     True or false
 */
function dol_is_file1($pathoffile)
{
    $newpathoffile=dol_osencode($pathoffile);
    return is_file($newpathoffile);
}
if (is_object($db)) $db->close();

