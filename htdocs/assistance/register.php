<?php
require("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/assistance/class/assistance.class.php';


$name = date('YmdHis');
$newname="images/".$name.".jpg";
$file = file_put_contents( $newname, file_get_contents('php://input') );
if (!$file)
  {
    print "ERROR: Failed to write data to $filename, check permissions\n";
    exit();
  }
 else
   {
    
     //buscamos el code
     $code = GETPOST('code');
     $datenew = dol_now();
     $adate = dol_getdate($datenew);
     $hour = $adate['hours'];
     $minute = $adate['minutes'];
     $idreg = 0;
     if ($_SESSION['socid'])
       {
	 //buscamos en contact societe
	 require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
	 $objsoc = new Societe($db);
	 $aContact = $objsoc->contact_array($_SESSION['socid']);
	 if ($aContact[$code])
	   $idreg = $code;
	 $_SESSION['namereg'] = $aContact[$code];
       }
     else
       {
	 require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
	 $objadh = new Adherent($db);
	 $objadh->fetch($code);
	 if ($objadh->id == $code)
	   $idreg = $code;
	 $_SESSION['namereg'] = $objadh->lastname.' '.$objadh->firstname;
       }
     $object = new Assistance($db);
     if ($idreg)
       {
	 if (empty($_SESSION['myreg'][$objadh->id]))
	   {
	     $object->entity = $conf->entity;
	     $object->fk_soc = $_SESSION['socid'];
	     $object->fk_member = $idreg;
	     $object->code_activitie = 'ASS';
	     $object->date_ass = dol_now();
	     $object->images = $newname;
	     $object->fk_user_create = $user->id;
	     $object->statut = 1;
	     $result = $object->create($user);
	     if ($result <=0)
	       $mesg='<div class="error">'.$object->error.'</div>';
	     else
	       $_SESSION["myreg"][$idreg]=$idreg;
	   }
	 
	 if ($_SESSION['mytime'] != $hour.':'.$minute)
	   {
	     unset($_SESSION['myreg']);
	     $_SESSION['mytime'] = $hour.':'.$minute;
	   }
       }

     $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $newname;
     print "$url\n";
   }

?>
