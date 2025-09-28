<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *  \file       dev/skeletons/poapac.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-04-02 13:53
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/poa/pac/class/poapac.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poapacemail extends Poapac
{
    // 	var $db;							//!< To store db handler
    // 	var $error;							//!< To return error code (or message)
    // 	var $errors=array();				//!< To return several error codes (or messages)
    // 	var $element='poa_pac';			//!< Id that identify managed objects
    // 	var $table_element='poa_pac';		//!< Name of table without prefix where object is stored

    // var $id;
    
    // 	var $entity;
    // 	var $fk_poa;
    // 	var $gestion;
    // 	var $fk_type_modality;
    // 	var $fk_type_object;
    // 	var $ref;
    // 	var $nom;
    // 	var $fk_financer;
    // 	var $month_init;
    // 	var $month_public;
    // 	var $partida;
    // 	var $amount;
    // 	var $fk_user_resp;
    // 	var $responsible;
    // 	var $tms='';
    // 	var $statut;
    // 	var $array;


    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
        return 1;
    }

    /**
     *  Load object in memory from the database 
     * para envio de email
     *  @param	int		$fk_poa    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function list_pac_email($gestion='')
    {
      global $langs,$conf;
      if (empty($gestion))
	$gestion = date('Y');
      if (empty($month))
	$month = date('m')*1;

      $sql = "SELECT";
      $sql.= " t.rowid AS id,";
      
      $sql.= " t.entity,";
      $sql.= " t.fk_poa,";
      $sql.= " t.gestion,";
      $sql.= " t.fk_type_modality,";
      $sql.= " t.fk_type_object,";
      $sql.= " t.ref,";
      $sql.= " t.nom,";
      $sql.= " t.fk_financer,";
      $sql.= " t.month_init,";
      $sql.= " t.month_public,";
      $sql.= " t.partida,";
      $sql.= " t.amount,";
      $sql.= " t.fk_user_resp,";
      $sql.= " t.responsible,";
      $sql.= " t.tms,";
      $sql.= " t.statut";
      
      
      $sql.= " FROM ".MAIN_DB_PREFIX."poa_pac as t";
      $sql.= " WHERE t.gestion = ".$gestion;
      $sql.= " AND t.month_init < ".$month;
      $sql.= " AND t.entity = ".$conf->entity;
      $sql.= " AND t.statut = 1";
      echo $sql.= " ORDER BY t.fk_type_modality ASC, t.ref ASC";
      dol_syslog(get_class($this)."::list_pac_email sql=".$sql, LOG_DEBUG);
      $resql=$this->db->query($sql);
      $this->array = array();
      $monthactual = date('m');
      if ($resql)
	{
	  $num = $this->db->num_rows($resql);
	  if ($this->db->num_rows($resql))
	    {
	      $i = 0;
	      while ($i < $num)
		{
		  //variables para envio correo
		  $detail = '';
		  $lInstruction = false;

		  $obj = $this->db->fetch_object($resql);

		  // buscamos los preventivos por pac
		  //si no existe preventivo y la fecha del pac es pasado se envia mail
		  require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprev.class.php';
		  $objprev = new Poaprev($this->db);
		  $objprev->fetch_pac($obj->id);
		  // echo '<hr>idpac '.$obj->id;
		  // echo '<br>preventivos '.count($objprev->array);
		  if (count($objprev->array) > 0)
		    {
		      //existe preventivos no se hace nada
		    }
		  else
		    {
		      //Generar instruction
		      $lInstruction = true;
		    }
		  //verificamos si esta activo poai
		  if ($conf->poai->enabled)
		    {
		      //si es verdadero la emision de instruccion
		      if ($lInstruction)
			{
			  //verificamos si se genero alguna instruccion para el pac
			  require_once DOL_DOCUMENT_ROOT.'/poai/instruction/class/poaiinstruction.class.php';
			  $objinst = new Poaiinstruction($this->db);
			  $objinst->getlist($obj->id,'PAC');
			  if (count($objinst->array) > 0)
			    $lInstruction = false; //existe, entonces no se genera instruccion
			}
		    }
		  $lEnvio = false;
		  echo '<br>resultado '.$lInstruction;
		  $ltrue = false;
		  if ($lInstruction)
		    {
		      //creamos la instruccion para hacer seguimiento
		      //buscamos el rating por defecto
		      require_once DOL_DOCUMENT_ROOT.'/poai/poai/class/poaipoai.class.php';
		      require_once DOL_DOCUMENT_ROOT.'/poai/poai/class/poaipoairating.class.php';
		      require_once DOL_DOCUMENT_ROOT.'/poai/poai/class/poaipoairatingdet.class.php';

		      require_once DOL_DOCUMENT_ROOT.'/poai/lib/poai.lib.php';

		      $objrat = new Poaipoairating($this->db);
		      $objratdet = new Poaipoairatingdet($this->db);
		      $objpoai   = new Poaipoai($this->db);

		      $objratdet->fetch_default($obj->fk_user_resp,$obj->gestion);
		      if ($objratdet->fk_user == $obj->fk_user_resp &&
			  $objratdet->gestion == $obj->gestion)
			{
			  $detail = $langs->trans('El PAC ').': '.$obj->nom;
			  $detail.= '; '.$langs->trans('Monthinit').': '.$obj->month_init;
			  $detail.= '; '.$langs->trans('NO se encuentra iniciado, favor rogamos informar el estado del mismo');
			  $fk_poai_rating_det = $objratdet->id;
			  //buscamos el siguiente numero
			  $objinst->getmaxref($obj->gestion,$obj->fk_user_resp);
			  $maximo = $objinst->maximo;
			  if (empty($maximo) || is_null($maximo))
			    $maximo = 1;
			  else
			    $maximo++;
		      
			  $object = new Poaiinstruction($this->db);
			  $error = 0;
			  $commitment_date = dol_mktime(12, 0, 0, date('m'),date('d'),date('Y'));
		      
			  $object->type_instruction = 'PAC';
			  $object->fk_id = $obj->id;
			  $object->fk_poai_rating_det = $fk_poai_rating_det;
			  $object->ref = $maximo;

			  $object->detail = $detail;
			  $object->commitment_date = $commitment_date;
			  $object->date_create = date('Y-m-d');
			  $object->fk_user_create = $user->id + 0;
			  $object->tms = date('YmdHis');
			  $object->statut = 0;
			  //generando codigo unico de longitud 40 
			  $object->tokenreg = generarcodigopoai(40);

			  $this->db->begin();
			  echo '<hr>newid '.$idnew = $object->create($user);
			  if ($idnew > 0)
			    {
			      $error = '';
			      //creamos el registro en agenda
			      require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
			      require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
			      require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
			      require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
			      require_once DOL_DOCUMENT_ROOT.'/user/class/usergroup.class.php';
			      require_once DOL_DOCUMENT_ROOT.'/comm/action/class/cactioncomm.class.php';
			      require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
			      require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
			      require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

			      $cactioncomm = new CActionComm($this->db);
			      $actioncomm  = new ActionComm($this->db);
			      $objuser   = new User($this->db);
			      $extrafields = new ExtraFields($this->db);

			      $object->fetch($idnew);
			      $type_inst = $object->type_instruction;
			      $idp = $object->fk_id;
			      echo '<hr>ratdet '.$idrr = $object->fk_poai_rating_det;

			      $objratdet->fetch($object->fk_poai_rating_det);
			      echo '<hr>ratindi '.$objratdet->fk_poai_poai_rating;
			      $objrat->fetch($objratdet->fk_poai_poai_rating);
			      $idr = $objratdet->fk_poai_poai_rating;
			      $objpoai->fetch($objrat->fk_poai_poai);
			      echo '<hr>poai '.$objrat->fk_poai_poai;
			      echo '<hr>userid '.$affectedto = $objpoai->fk_user;

			      if ($contactid)
				{
				  $result=$contact->fetch($contactid);
				}
			      
			      $fulldayevent=1;
			      $percentage=in_array(0,array(0,100))?0:GETPOST("percentage");	// If status is -1 or 100, percentage is not defined and we must use status
			      
			      // Clean parameters
			      $datep=dol_mktime($fulldayevent?'00':$_POST["aphour"], $fulldayevent?'00':$_POST["apmin"], 0, date('m',$object->commitment_date), date('d',$object->commitment_date), date('Y',$object->commitment_date));
			      $datef=dol_mktime($fulldayevent?'23':$_POST["p2hour"], $fulldayevent?'59':$_POST["p2min"], $fulldayevent?'59':'0', $_POST["p2month"], $_POST["p2day"], $_POST["p2year"]);
			      // Check parameters
			      if (! $datef && $percentage == 100)
				{
				  $error++;
				  $action = '';
				  $mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("DateEnd")).'</div>';
				}
			      
			      if (empty($conf->global->AGENDA_USE_EVENT_TYPE) && ! $object->detail)
				{
				  $error++;
				  $action = '';
				  $mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Title")).'</div>';
				}
			      
			      // Initialisation objet cactioncomm
			      // if (! GETPOST('actioncode'))
			      //   {
			      // 	$error++;
			      // 	$action = 'create';
			      // 	$mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Type")).'</div>';
			      //   }
			      // else
			      //   {
			      $result=$cactioncomm->fetch('AC_OTH');
			      // }
			      
			      // Initialisation objet actioncomm
			      $actioncomm->type_id = $cactioncomm->id;
			      $actioncomm->type_code = $cactioncomm->code;
			      $actioncomm->priority = GETPOST("priority")?GETPOST("priority"):0;
			      $actioncomm->fulldayevent = (! empty($fulldayevent)?1:0);
			      $actioncomm->location = GETPOST("location");
			      $actioncomm->transparency = (GETPOST("transparency")=='on'?1:0);
			      $actioncomm->label = trim($object->detail);
			      
			      $actioncomm->label.= ' '.$obj->nom;
			      
			      $actioncomm->fk_element = GETPOST("fk_element");
			      $actioncomm->elementtype = GETPOST("elementtype");
			      if (! $object->detail)
				{
				  if (GETPOST('actioncode') == 'AC_RDV' && $contact->getFullName($langs))
				    {
				      $actioncomm->label = $langs->transnoentitiesnoconv("TaskRDVWith",$contact->getFullName($langs));
				    }
				  else
				    {
				      if ($langs->trans("Action".$actioncomm->type_code) != "Action".$actioncomm->type_code)
					{
					  $actioncomm->label = $langs->transnoentitiesnoconv("Action".$actioncomm->type_code)."\n";
					}
				      else $actioncomm->label = $cactioncomm->libelle;
				    }
				}
			      $actioncomm->fk_project = isset($_POST["projectid"])?$_POST["projectid"]:0;
			      $actioncomm->datep = $datep;
			      $actioncomm->datef = $datef;
			      $actioncomm->percentage = $percentage;
			      $actioncomm->duree=((float) (GETPOST('dureehour') * 60) + (float) GETPOST('dureemin')) * 60;
			      
			      $usertodo=new User($this->db);
			      if ($affectedto > 0)
				{
				  $usertodo->fetch($affectedto);
				}
			      $actioncomm->usertodo = $usertodo;
			      $userdone=new User($this->db);
			      if ($_POST["doneby"] > 0)
				{
				  $userdone->fetch($_POST["doneby"]);
				}
			      $actioncomm->userdone = $userdone;
			      
			      if ($type_inst == 'POA')
				if ($idp == $objpoa->id)
				  {
				    $actioncomm->note = $objpoa->pseudonym;
				    $actioncomm->note .= ' : '.trim($object->detail);
				  }
				else
				  $actioncomm->note = trim($object->detail);
			      
			      $actioncomm->note = trim($object->detail);
			      
			      // if (isset($_POST["contactid"])) $actioncomm->contact = $contact;
			      // if (GETPOST('socid','int') > 0)
			      // {
			      // 	$societe = new Societe($db);
			      // 	$societe->fetch(GETPOST('socid','int'));
			      // 	$actioncomm->societe = $societe;
			      // }
			      
			      // Special for module webcal and phenix
			      // TODO external modules
			      if (! empty($conf->webcalendar->enabled) && GETPOST('add_webcal') == 'on') $actioncomm->use_webcal=1;
			      if (! empty($conf->phenix->enabled) && GETPOST('add_phenix') == 'on') $actioncomm->use_phenix=1;
			      
			      // Check parameters
			      if ($actioncomm->type_code == 'AC_RDV' && ($datep == '' || ($datef == '' && empty($fulldayevent))))
				{
				  $error++;
				}
			      if (! empty($datea) && GETPOST('percentage') == 0)
				{
				  $error++;
				}

			      $ret = $extrafields->setOptionalsFromPost($extralabels,$actioncomm);
			      
			      if (! $error)
				{
				  
				  //$object->fetch(GETPOST('id'));
				  //cambiando a validado
				  $object->statut = 1;
				  //update
				  $object->update($user);
				  
				  // //enviamos correo
				  // $emailfrom = '';
				  // if ($objuser->fetch($user->id))
				  //   $emailfrom = $objuser->email;
				  
				  // $emailbcc = '';
				  // $email = '';
				  // if ($objuser->fetch($affectedto))
				  //   $email = $objuser->email;
				  // $emailcc = '';
				  // $idUserSup = $objuser->fk_user;
				  // //buscamos a su superior
				  // if ($idUserSup)
				  //   {
				  //     if ($objuser->fetch($idUserSup))
				  // 	$emailcc = $objuser->email;
				  //   }
				  // sendmail($id, $email, $emailfrom, $emailcc, $emailbcc);
				  
				  // On cree l'action
				  $idaction=$actioncomm->add($user);
				  
				  if ($idaction > 0)
				    {
				      if (! $actioncomm->error)
					{
					  $this->db->commit();
					}
				      else
					{
					  // If error
					  $this->db->rollback();
					}
				    }
				  else
				    {
				      $this->db->rollback();
				    }
				}
			      //fin agenda
			    }
			  else
			    $this->db->rollback();
			}
		    }

		  //para enviar correo
		  if ($lEnvio && $lTrue = true)
		    {
		      //		      echo '<br>enviando correo ';
		      //echo '<br>user resp '.$obj->fk_user_resp.' REF '.$obj->ref.' NOMBRE '.$obj->nom;
		      //direccion
		      $url = $dolibarr_main_url_root;
		      //correo para quien
		      
		      $emailto = '';
		      //para envio email
		      require_once DOL_DOCUMENT_ROOT.'/core/lib/emailing.lib.php';
		      require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
		      require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
		      //require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
		      require_once DOL_DOCUMENT_ROOT.'/comm/mailing/class/mailing.class.php';
		      require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
		      require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
		      require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';
		      require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
		      
		      //buscamos a quien enviamos
		      $fkUsersup = '';
		      $nameto = '';
		      $emailcc = '';
		      $emailbcc = '';
		      $objuser = new User($this->db);
		      $objuser->fetch($obj->fk_user_resp);
		      if ($objuser->id == $obj->fk_user_resp)
			{
			  $emailto = $objuser->email;
			  $nameto = $objuser->firstname.' '.$objuser->lastname;
			  $fkUsersup = $objuser->fk_user;
			}
		      //$emailto = 'rqueso@bcb.gob.bo';
		      // copia a su superior
		      if ($fkUsersup)
			{
			  $objuser->fetch($fkUsersup);
			  if ($objuser->id == $fkUsersup)
			    $emailcc = $objuser->email;
			}
		      // echo '<br>emailto '.$emailto;
		      // echo '<br>copia a '.$emailcc;
		      $aArray[$obj->id] = array('email' => $emailto,
						'pac' => $obj->nom,
						'mes' => $obj->month_init);

		      //$emailcc = '';
		      //parametros de envio email
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
		      //informacion del correo
		      $arr_file = array();
		      $arr_mime = array();
		      $arr_name = array();
		      $arr_mime[] = 'text/html';
		      
		      $sendto   = $emailto;
		      $email_from = $conf->global->MAIN_MAIL_EMAIL_FROM;
		      $tmpsujet = $obj->nom.'; '.$langs->trans('Month').': '.$obj->month_init;
		      $tmpbody = bodypacemail($obj,$url,$nameto);
		      $msgishtml = 1;
		      $email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
		      $arr_css = array('bgcolor' => '#FFFFCC');
		      $mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,$emailcc, $emailbcc, 0, $msgishtml,$email_errorsto,$arr_css);
		      $result=$mailfile->sendfile();
		    }
		  $i++;
		}
	    }

	  //armando como excel el array
	  print '<html>';
	  print '<head>';
	  print '<meta charset="UTF8">';
	  print '</head>';
	  print '<body>';
	  print '<table>';
	  print '<tr>';
	  print '<td>email</td>';
	  print '<td>pac</td>';
	  print '<td>mes</td>';

	  foreach((array) $aArray AS $k => $aData)
	    {
	      print '<tr>';
	      print '<td>'.$aData['email'].'</td>'; 
	      print '<td>'.$aData['pac'].'</td>'; 
	      print '<td>'.$aData['mes'].'</td>'; 
	      print '</tr>';
	    }
	  print '</table>';
	  print '</body>';
	  print '<html>';
	  $this->db->free($resql);
	  
	  return 1;
	}
      else
	{
	  $this->error="Error ".$this->db->lasterror();
	  dol_syslog(get_class($this)."::list_pac_email ".$this->error, LOG_ERR);
	  return -1;
	}
    }

}
?>
