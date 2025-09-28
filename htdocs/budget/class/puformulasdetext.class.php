<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/puformulasdet.class.php';

class Puformulasdetext extends Puformulasdet
{
    /**
     *  Load object in memory from the database
     *
     *  @param  int     $fk_formula    Id object formula
     *  @return int             $max  siguiente valor 
    */
    function sequen_det($ref_formula)
    {
        global $langs,$conf;
        $sql = "SELECT";
        $sql.= " t.sequen ";
        $sql.= " FROM ".MAIN_DB_PREFIX."pu_formulas_det as t";
        $sql.= " WHERE t.ref_formula = '".$ref_formula."' ";
        $sql.= " AND t.entity = ".$conf->entity;
        $sql.= " ORDER BY t.sequen DESC ";

        dol_syslog(get_class($this)."::sequen_det sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
                return $obj->sequen + 1;
            }
            $this->db->free($resql);
            return 1;
        }
        else
        {
            $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::sequen_det ".$this->error, LOG_ERR);
            return -1;
        }
    }

}
?>