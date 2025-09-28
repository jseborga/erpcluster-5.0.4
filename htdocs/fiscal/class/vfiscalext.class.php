<?php
include_once DOL_DOCUMENT_ROOT.'/fiscal/class/vfiscal.class.php';

class Vfiscalext extends Vfiscal
{
    /**
     *  Load object in memory from the database
     *
     *  @param  int     $id    Id object
     *  @return int             <0 if KO, >0 if OK
     */
    function getlist($fk_user,$statutprint=1)
    {
        global $langs,$conf;
        $sql = "SELECT";
        $sql.= " t.rowid,";

        $sql.= " t.entity,";
        $sql.= " t.nfiscal,";
        $sql.= " t.serie,";
        $sql.= " t.fk_dosing,";
        $sql.= " t.fk_facture,";
        $sql.= " t.fk_cliepro,";
        $sql.= " t.nit,";
        $sql.= " t.razsoc,";
        $sql.= " t.date_exp,";
        $sql.= " t.type_op,";
        $sql.= " t.num_autoriz,";
        $sql.= " t.cod_control,";
        $sql.= " t.baseimp1,";
        $sql.= " t.baseimp2,";
        $sql.= " t.baseimp3,";
        $sql.= " t.baseimp4,";
        $sql.= " t.baseimp5,";
        $sql.= " t.aliqimp1,";
        $sql.= " t.aliqimp2,";
        $sql.= " t.aliqimp3,";
        $sql.= " t.aliqimp4,";
        $sql.= " t.aliqimp5,";
        $sql.= " t.valimp1,";
        $sql.= " t.valimp2,";
        $sql.= " t.valimp3,";
        $sql.= " t.valimp4,";
        $sql.= " t.valimp5,";
        $sql.= " t.valret1,";
        $sql.= " t.valret2,";
        $sql.= " t.valret3,";
        $sql.= " t.valret4,";
        $sql.= " t.valret5,";
        $sql.= " t.amount_payment,";
        $sql.= " t.amount_balance,";
        $sql.= " t.date_create,";
        $sql.= " t.fk_user_create,";
        $sql.= " t.status,";
        $sql.= " t.statut_print";


        $sql.= " FROM ".MAIN_DB_PREFIX."v_fiscal as t";
        if (!empty($fk_user))
        {
            $sql.= " WHERE t.fk_user_create = ".$fk_user;
            $sql.= " AND t.statut_print = ".$statutprint;
        }
        else
            return -1;

        dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $num = $this->db->num_rows($resql);
                $i = 0;
                while ($i < $num)
                {
                    $obj = $this->db->fetch_object($resql);
                    $this->array[$obj->rowid] = $obj;
                    $i++;
                }
            }
            $this->db->free($resql);
            return 1;
        }
        else
        {
            $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *  Load object in memory from the database
     *
     *  @param  int     $id    Id object
     *  @return int             <0 if KO, >0 if OK
     */
    function fetch_num_fac($num,$fac)
    {
        global $langs,$conf;
        $sql = "SELECT";
        $sql.= " t.rowid,";

        $sql.= " t.entity,";
        $sql.= " t.nfiscal,";
        $sql.= " t.serie,";
        $sql.= " t.fk_dosing,";
        $sql.= " t.fk_facture,";
        $sql.= " t.fk_cliepro,";
        $sql.= " t.nit,";
        $sql.= " t.razsoc,";
        $sql.= " t.date_exp,";
        $sql.= " t.type_op,";
        $sql.= " t.num_autoriz,";
        $sql.= " t.cod_control,";
        $sql.= " t.baseimp1,";
        $sql.= " t.baseimp2,";
        $sql.= " t.baseimp3,";
        $sql.= " t.baseimp4,";
        $sql.= " t.baseimp5,";
        $sql.= " t.aliqimp1,";
        $sql.= " t.aliqimp2,";
        $sql.= " t.aliqimp3,";
        $sql.= " t.aliqimp4,";
        $sql.= " t.aliqimp5,";
        $sql.= " t.valimp1,";
        $sql.= " t.valimp2,";
        $sql.= " t.valimp3,";
        $sql.= " t.valimp4,";
        $sql.= " t.valimp5,";
        $sql.= " t.valret1,";
        $sql.= " t.valret2,";
        $sql.= " t.valret3,";
        $sql.= " t.valret4,";
        $sql.= " t.valret5,";
        $sql.= " t.amount_payment,";
        $sql.= " t.amount_balance,";
        $sql.= " t.date_create,";
        $sql.= " t.fk_user_create,";
        $sql.= " t.status,";
        $sql.= " t.statut_print";


        $sql.= " FROM ".MAIN_DB_PREFIX."v_fiscal as t";
        if (!empty($num) && !empty($fac))
        {
            $sql.= " WHERE t.nfiscal = ".$fac;
            $sql.= " AND t.num_autoriz = '".$num."'";
        }
        else
            return -1;
        dol_syslog(get_class($this)."::fetch_num_fac sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;

                $this->entity = $obj->entity;
                $this->nfiscal = $obj->nfiscal;
                $this->serie = $obj->serie;
                $this->fk_dosing = $obj->fk_dosing;
                $this->fk_facture = $obj->fk_facture;
                $this->fk_cliepro = $obj->fk_cliepro;
                $this->nit = $obj->nit;
                $this->razsoc = $obj->razsoc;
                $this->date_exp = $this->db->jdate($obj->date_exp);
                $this->type_op = $obj->type_op;
                $this->num_autoriz = $obj->num_autoriz;
                $this->cod_control = $obj->cod_control;
                $this->baseimp1 = $obj->baseimp1;
                $this->baseimp2 = $obj->baseimp2;
                $this->baseimp3 = $obj->baseimp3;
                $this->baseimp4 = $obj->baseimp4;
                $this->baseimp5 = $obj->baseimp5;
                $this->aliqimp1 = $obj->aliqimp1;
                $this->aliqimp2 = $obj->aliqimp2;
                $this->aliqimp3 = $obj->aliqimp3;
                $this->aliqimp4 = $obj->aliqimp4;
                $this->aliqimp5 = $obj->aliqimp5;
                $this->valimp1 = $obj->valimp1;
                $this->valimp2 = $obj->valimp2;
                $this->valimp3 = $obj->valimp3;
                $this->valimp4 = $obj->valimp4;
                $this->valimp5 = $obj->valimp5;
                $this->valret1 = $obj->valret1;
                $this->valret2 = $obj->valret2;
                $this->valret3 = $obj->valret3;
                $this->valret4 = $obj->valret4;
                $this->valret5 = $obj->valret5;
                $this->amount_payment = $obj->amount_payment;
                $this->amount_balance = $obj->amount_balance;
                $this->date_create = $this->db->jdate($obj->date_create);
                $this->fk_user_create = $obj->fk_user_create;
                $this->status = $obj->status;
                $this->statut_print = $obj->statut_print;


            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
            $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch_num_fac ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *  Load object in memory from the database
     *
     *  @param  int     $id    Id object
     *  @return int             <0 if KO, >0 if OK
     */
    function getlastfiscal($fk_subsidiaryid)
    {
        global $langs,$conf;
        $sql = "SELECT";
        $sql.= " t.rowid,";
        $sql.= " MAX(t.nfiscal) AS mfiscal";

        $sql.= " FROM ".MAIN_DB_PREFIX."v_fiscal as t";
        $sql.= " INNER JOIN ".MAIN_DB_PREFIX."v_dosing AD d ON t.fk_dosing = d.rowid";
        $sql.= " WHERE t.entity = ".$conf->entity;
        $sql.= " AND d.fk_subsidiaryid = ".$fk_subsidiaryid;
        $sql.= " GROUP BY mfiscal";
        if (!empty($fk_user))
        {
            $sql.= " WHERE t.fk_user_create = ".$fk_user;
            $sql.= " AND t.statut_print = ".$statutprint;
        }
        else
            return -1;

        dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $num = $this->db->num_rows($resql);
                $i = 0;
                while ($i < $num)
                {
                    $obj = $this->db->fetch_object($resql);
                    $this->array[$obj->rowid] = $obj;
                    $i++;
                }
            }
            $this->db->free($resql);
            return 1;
        }
        else
        {
            $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }

}
?>