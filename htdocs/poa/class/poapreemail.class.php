<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2014 Ramiro Queso <ramiroques@gmail.com>
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
 *  \file       poa/class/poapreemail.class.php
 *  \ingroup    poa 
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *		Initialy built by build_class_from_table on 2014-04-02 13:53
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poaprev.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poapreemail extends Poaprev
{

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
    function list_pre_email($gestion='')
    {
      global $langs,$conf;
      if (empty($gestion))
	$gestion = date('Y');
      if (empty($month))
	$month = date('m')*1;

      $sql = "SELECT";
      $sql.= " t.rowid,";
      $sql.= " t.rowid AS id,";
      $sql.= " t.entity,";
      $sql.= " t.gestion,";
      $sql.= " t.fk_pac,";
      $sql.= " t.fk_area,";
      $sql.= " t.label,";
      $sql.= " t.nro_preventive,";
      $sql.= " t.date_preventive,";
      $sql.= " t.amount,";
      $sql.= " t.date_create,";
      $sql.= " t.fk_user_create,";
      $sql.= " t.tms,";
      $sql.= " t.statut,";
      $sql.= " t.active";
      
      $sql.= " FROM ".MAIN_DB_PREFIX."poa_prev as t";
      $sql.= " WHERE t.gestion = ".$gestion;
      $sql.= " AND t.entity = ".$conf->entity;
      $sql.= " AND t.statut = 1";
      $sql.= " ORDER BY t.nro_preventive ASC";
      dol_syslog(get_class($this)."::list_pre_email sql=".$sql, LOG_DEBUG);
      $resql=$this->db->query($sql);
      if ($resql)
	{
	  $num = $this->db->num_rows($resql);
	  if ($this->db->num_rows($resql))
	    {
	      $i = 0;
	      $numDay = $conf->global->POA_PREVENTIVE_DAY_DELAY;
	      while ($i < $num)
		{
		  //variables para envio correo
		  $lEnvio = false;
		  $lInstruction = false;

		  $obj = $this->db->fetch_object($resql);
		  // buscamos los preventivos por pac
		  //si no existe preventivo y la fecha del pac es pasado se envia mail
		  require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';
		  $objproc = new Poaprocess($this->db);
		  $objproc->fetch_prev($obj->rowid);
		  if ($objproc->fk_poa_prev == $obj->rowid)
		    {
		      //analizamos si se cumplio la fecha
		      //existe el inicio de proceso
		      echo '<br>EXISTE';
		    }
		  else
		    {
		      echo '<br>se registra';
		      //revisamos fecha de creacion del preventivo
		      $date1 = strtotime($obj->date_preventive);
		      $date2 = strtotime("+$numDay day",$date1);
		      $dateact = strtotime(date('Y-m-d'));
		      //echo '<hr>dif '.$dateact.' '.$date2;
		      if ($dateact > $date2)
			{
			  $lEnvio = true;
			  $lInstruction = true;
			}
		    }
		  //verificamos si esta activo poai
		  if ($conf->poai->enabled && $lInstruction)
		    {
		      //verificamos si se genero alguna instruccion para el preventivo
		      require_once DOL_DOCUMENT_ROOT.'/poai/instruction/class/poaiinstruction.class.php';
		      $objinst = new Poaiinstruction($this->db);
		      $objinst->getlist($obj->id,'PRE');
		      if (count($objinst->array) > 0)
			$lInstruction = false;
		      else
			$lInstruction = true;
		    }
		  else
		    $lInstruction = false;
		  echo '<br>resultado '.$lInstruction;
		  //////instruction//////////////
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

		      $objratdet->fetch_default($obj->fk_user_create,$obj->gestion);
		      if ($objratdet->fk_user == $obj->fk_user_create &&
			  $objratdet->gestion == $obj->gestion)
			{
			  $detail = $langs->trans('Preventivo sin movimiento: ');
			  $detail.= '<br>'.$langs->trans('Nro. preventivo').': '.$obj->nro_preventive.': '.$obj->label;
			  $detail.= '; '.$langs->trans('Date create').': '.dol_print_date($obj->date_preventive,'day');
			  $detail.= '<br>'.$langs->trans('En la fecha no tiene movimiento alguno, favor rogamos revisar y enviar respuesta respecto al estado del mismo');
			  $fk_poai_rating_det = $objratdet->id;
			  echo $detail;
			  //buscamos el siguiente numero
			  $objinst->getmaxref($obj->gestion,$obj->fk_user_create);
			  $maximo = $objinst->maximo;
			  if (empty($maximo) || is_null($maximo))
			    $maximo = 1;
			  else
			    $maximo++;
		      
			  $object = new Poaiinstruction($this->db);
			  $error = 0;
			  $commitment_date = dol_mktime(12, 0, 0, date('m'),date('d'),date('Y'));
		      
			  $object->type_instruction = 'PRE';
			  $object->fk_id = $obj->id;
			  $object->fk_poai_rating_det = $fk_poai_rating_det;
			  $object->ref = $maximo;

			  $object->detail = $detail;
			  $object->commitment_date = $commitment_date;
			  $object->date_create = dol_now();
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
			      $idrr = $object->fk_poai_rating_det;

			      $objratdet->fetch($object->fk_poai_rating_det);
			      $objrat->fetch($objratdet->fk_poai_poai_rating);
			      $idr = $objratdet->fk_poai_poai_rating;
			      $objpoai->fetch($objrat->fk_poai_poai);
			      $affectedto = $objpoai->fk_user;

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
			      $actioncomm->label = $langs->trans('Preventivo sin movimiento').': ';
			      
			      $actioncomm->label.= $obj->nro_preventive.': '.$obj->label;
			      
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
		  ///////fin instruction/////////

		  if ($lEnvio && $lTrue == true)
		    {
		      //exit;
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
		      $objuser->fetch($obj->fk_user_create);
		      if ($objuser->id == $obj->fk_user_create)
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
		      $tmpbody = bodyprevemail($obj,$url,$nameto);
		      $msgishtml = 1;
		      $email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
		      $arr_css = array('bgcolor' => '#A5FFAE');
		      $tmpsujet = $obj->nro_preventive.': '.$obj->label;
		      $mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,$emailcc, $emailbcc, 0, $msgishtml,$email_errorsto,$arr_css);
		      $result=$mailfile->sendfile();
		      if ($result <= 0)
			{
			  echo '<hr>error, revisar';
			  exit;
			}
		    }
		  $i++;
		}
	    }
	  $this->db->free($resql);
	  
	  return 1;
	}
      else
	{
	  $this->error="Error ".$this->db->lasterror();
	  dol_syslog(get_class($this)."::list_pre_email ".$this->error, LOG_ERR);
	  return -1;
	}
    }
	
}
?>
