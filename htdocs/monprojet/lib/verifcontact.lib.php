<?php
function verifcontacttask($user,$object,$ret = 'res',$mode=0)
{
	global $langs,$conf;
	$res = false;
	foreach(array('internal','external') as $source)
	{
		$tab = $object->liste_contact(-1,$source);
		$num=count($tab);
		$i = 0;
		while ($i < $num)
		{
			if ($tab[$i]['source']=='internal')
			{
				$userid=$tab[$i]['id'];
				$usersend['email'][$tab[$i]['email']] = $tab[$i]['email'];
				$users['user'][$tab[$i]['id']] = $tab[$i]['id'];

				if ($user->id == $userid)
					$res = true;
			}
			if ($tab[$i]['source']=='external')
			{
				$contactstaticid=$tab[$i]['id'];
				$usersend['email'][$tab[$i]['email']] = $tab[$i]['email'];
				$users['contact'][$tab[$i]['id']] = $tab[$i]['id'];

				if ($user->contactid == $contactstaticid)
					$res = true;
			}
			$i ++;
		}
	}
	if ($mode == 0)
		return $res;
	else
		return array($res,$usersend,$users);
}

//permisos por contactos del proyecto
//no validamos si el proyecto pertenece a una empresa
function verifcontactprojet($user,$object,$ret='res')
{
	global $langs,$conf,$db;
	$res = false;
	dol_include_once('/contact/class/contact.class.php');
	dol_include_once('/user/class/user.class.php');

	$companystatic=new Societe($db);
	$aUser = array();
	$aContact = array();
	$usersend = array();
	$aPerm = array();
	$arrayofsource=array('internal','external');	
  	// Show both link to user and thirdparties contacts
	foreach($arrayofsource as $source)
	{
		$tmpobject=$object;
		if ($object->element == 'shipping' && is_object($objectsrc)) $tmpobject=$objectsrc;
		$tab = $tmpobject->liste_contact(4,$source);
		$num=count($tab);
		$i = 0;
		while ($i < $num)
		{
			$var = !$var;
			$aPerm[$tab[$i]['id']][$tab[$i]['source']] = $tab[$i]['code'];

			$statusofcontact = $tab[$i]['status'];

			if ($tab[$i]['source']=='internal')
			{
				if ($user->id == $tab[$i]['id'])
					$res = true;
				$userstatic=new User($db);

				$userstatic->id=$tab[$i]['id'];
				$userstatic->lastname=$tab[$i]['lastname'];
				$userstatic->firstname=$tab[$i]['firstname'];
				$userstatic->lastname=$tab[$i]['lastname'];
				$userstatic->code=$tab[$i]['code'];
				$userstatic->email=$tab[$i]['email'];
				$userstatic->user_mobile=$tab[$i]['user_mobile'];
				$userstatic->getNomUrl(1);
				$aUser[$tab[$i]['id']] = $userstatic;
				$usersend['email'][$tab[$i]['email']] = $tab[$i]['email'];
				$usersource[$tab[$i]['email']] = $tab[$i]['source'];
			}
			if ($tab[$i]['source']=='external')
			{
				if ($user->contact_id == $tab[$i]['id'])
					$res = true;
				$contactstatic=new Contact($db);
				$contactstatic->id=$tab[$i]['id'];
				$contactstatic->lastname=$tab[$i]['lastname'];
				$contactstatic->firstname=$tab[$i]['firstname'];
				$contactstatic->code=$tab[$i]['code'];
				$contactstatic->email=$tab[$i]['email'];
				$contactstatic->phone_mobile=$tab[$i]['phone_mobile'];
				$contactstatic->getNomUrl(1);
				$aContact[$tab[$i]['id']] = $contactstatic;

				$usersend['email'][$tab[$i]['email']] = $tab[$i]['email'];
				$usersource[$tab[$i]['email']] = $tab[$i]['source'];
			}
			$i++;
		}
	}
  // echo '<hr>resultado '.$res;
  // print_r($aPerm);
	return array($res,$userstatic,$contactstatic,$usersend,$usersource,$aPerm,$aUser,$aContact);
}

function verifcontactprojet_original($user,$object,$ret='res')
{
	global $langs,$conf,$db;
	$res = false;
	dol_include_once('/contact/class/contact.class.php');
	dol_include_once('/user/class/user.class.php');

	$contactstatic=new Contact($db);
	$userstatic=new User($db);
	$companystatic=new Societe($db);
	$usersend = array();
	$aPerm = array();
  $arrayofsource=array('internal','external');	// Show both link to user and thirdparties contacts
  foreach($arrayofsource as $source)
  {
  	$tmpobject=$object;
  	if ($object->element == 'shipping' && is_object($objectsrc)) $tmpobject=$objectsrc;
  	$tab = $tmpobject->liste_contact(4,$source);
      // echo '<hr>project '.$object->id ;
      // echo '<pre>';
      // print_r($tab);
      // echo '</pre>';
  	$num=count($tab);
  	$i = 0;
  	while ($i < $num)
  	{
  		$var = !$var;
  		$aPerm[$tab[$i]['id']][$tab[$i]['source']] = $tab[$i]['code'];

	  if ($tab[$i]['socid'] > 0) //tiene asignado societe
	  {
	      //echo '<hr>tabsocid '.$tab[$i]['socid'];
	  	$result = $companystatic->fetch($tab[$i]['socid']);
	  	if (!$user->admin)
	  	{
		  //echo '<br>company |'.$companystatic->id.' == '.$user->societe_id.'|';
	  		if ($result > 0 && $companystatic->id == $user->societe_id)
	  			$res = true;
	  		if ($user->array_options['options_view_projet'])
	  		{
	  			if ($user->contact_id == $tab[$i]['id'])
	  			{
	  				$res = true;
	  			}
	  		}
	  	}
	  }
	  else //no tiene societe
	  {
	  	if ($tab[$i]['id'] == $user->id )
	  	{
		  //		  $aPerm[$tab[$i]['id']] = $object->id;
	  		$res = true;
	  	}
	  }
	  $statusofcontact = $tab[$i]['status'];
	  
	  if ($tab[$i]['source']=='internal')
	  {
	  	$userstatic->id=$tab[$i]['id'];
	  	$userstatic->lastname=$tab[$i]['lastname'];
	  	$userstatic->firstname=$tab[$i]['firstname'];
	  	$userstatic->lastname=$tab[$i]['lastname'];
	  	$userstatic->code=$tab[$i]['code'];
	  	$userstatic->email=$tab[$i]['email'];
	  	$userstatic->getNomUrl(1);

	  	$usersend['email'][$tab[$i]['email']] = $tab[$i]['email'];
	  	$usersource[$tab[$i]['email']] = $tab[$i]['source'];
	  }
	  if ($tab[$i]['source']=='external')
	  {
	  	$contactstatic->id=$tab[$i]['id'];
	  	$contactstatic->lastname=$tab[$i]['lastname'];
	  	$contactstatic->firstname=$tab[$i]['firstname'];
	  	$contactstatic->code=$tab[$i]['code'];
	  	$contactstatic->email=$tab[$i]['email'];
	  	$contactstatic->getNomUrl(1);

	  	$usersend['email'][$tab[$i]['email']] = $tab[$i]['email'];
	  	$usersource[$tab[$i]['email']] = $tab[$i]['source'];
	  }
	  $i++;
	}
}
  // echo '<hr>resultado '.$res;
  // print_r($aPerm);
return array($res,$userstatic,$contactstatic,$usersend,$usersource,$aPerm);
}