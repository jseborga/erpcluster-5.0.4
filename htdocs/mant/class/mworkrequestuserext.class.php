<?php
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestuser.class.php';

class Mworkrequestuserext extends Mworkrequestuser
{
    //modificado
    /**
     *  Load object in memory from the database
     *
     *  @param  int     $id    Id object
     *  @return int             <0 if KO, >0 if OK
     */
    function list_requestuser($id)
    {
        global $langs;
        $sql = "SELECT";
        $sql.= " t.rowid,";

        $sql.= " t.fk_work_request,";
        $sql.= " t.fk_user,";
        $sql.= " t.fk_user_create,";
        $sql.= " t.date_create,";
        $sql.= " t.tms,";
        $sql.= " t.statut";


        $sql.= " FROM ".MAIN_DB_PREFIX."m_work_request_user as t";
        $sql.= " WHERE t.fk_work_request = ".$id;
        dol_syslog(get_class($this)."::list_requestuser sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        $aArray = array();
        if ($resql)
        {
        $num = $this->db->num_rows($resql);
            if ($num)
          {
        $i = 0;
        while ($i < $num)
          {
            $obj = $this->db->fetch_object($resql);
            $objnew = new Mworkrequestuser($this->db);

            $objnew->id    = $obj->rowid;

            $objnew->fk_jobs = $obj->fk_jobs;
            $objnew->fk_user = $obj->fk_user;
            $objnew->fk_user_create = $obj->fk_user_create;
            $objnew->date_create = $this->db->jdate($obj->date_create);
            $objnew->tms = $this->db->jdate($obj->tms);
            $objnew->statut = $obj->statut;
            $aArray[$obj->rowid] = $objnew;
            $i++;
          }
        $this->db->free($resql);
        return $aArray;
          }
            $this->db->free($resql);
            return $aArray;
        }
        else
        {
            $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::list_jobsuser ".$this->error, LOG_ERR);
            return -1;
        }
    }
    }
?>