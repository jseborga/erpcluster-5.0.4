<?php
/*
* Author Ramiro Queso ramiroques@gmail.com
*/

require_once DOL_DOCUMENT_ROOT.'/almacen/class/solalmacendetfabrication.class.php';

class Solalmacendetfabricationext extends Solalmacendetfabrication
{

    var $lines;
    /**
     * Load object in memory from the database
     *
     * @param string $sortorder Sort Order
     * @param string $sortfield Sort field
     * @param int    $limit     offset limit
     * @param int    $offset    offset limit
     * @param array  $filter    filter array
     * @param string $filtermode filter mode (AND or OR)
     *
     * @return int <0 if KO, >0 if OK
     */
    public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='')
    {
        dol_syslog(__METHOD__, LOG_DEBUG);

        global $langs;
        $sql = "SELECT";
        $sql.= " t.rowid,";
        $sql.= " t.fk_almacendet,";
        $sql.= " t.fk_fabricationdet,";
        $sql.= " t.qty,";
        $sql.= " t.price";

        $sql.= " FROM ".MAIN_DB_PREFIX."sol_almacendet_fabrication as t";

        // Manage filter
        $sqlwhere = array();
        if (count($filter) > 0) {
            foreach ($filter as $key => $value) {
                $sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
            }
        }
        if (count($sqlwhere) > 0) {
            $sql .= ' WHERE ' . implode(' '.$filtermode.' ', $sqlwhere);
        }
        if ($filterstatic)
          $sql.= $filterstatic;

        if (!empty($sortfield)) {
            $sql .= ' ORDER BY ' . $sortfield . ' ' . $sortorder;
        }
        if (!empty($limit)) {
         $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
        }
        $this->lines = array();
        $resql = $this->db->query($sql);
        if ($resql) {
            $num = $this->db->num_rows($resql);

            while ($obj = $this->db->fetch_object($resql)) {
                $line = new Solalmacendetfabricationline();

                $line->id    = $obj->rowid;
                $line->fk_almacendet = $obj->fk_almacendet;
                $line->fk_fabricationdet = $obj->fk_fabricationdet;
                $line->qty = $obj->qty;
                $line->price = $obj->price;

                $this->lines[] = $line;
            }
            $this->db->free($resql);

            return $num;
        } else {
            $this->errors[] = 'Error ' . $this->db->lasterror();
            dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

            return - 1;
        }
    }

    /**
     *  Delete object in database
     *
     *  @param  User    $id        Register from almacendet
     */
    function delete_almacendet($id)
    {
        global $conf, $langs;
        $error=0;

        $this->db->begin();

        if (! $error)
        {
            $sql = "DELETE FROM ".MAIN_DB_PREFIX."sol_almacendet_fabrication";
            $sql.= " WHERE fk_almacendet=".$id;

            dol_syslog(get_class($this)."::delete_almacendet sql=".$sql);
            $resql = $this->db->query($sql);
            if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        }

        // Commit or rollback
        if ($error)
        {
            foreach($this->errors as $errmsg)
            {
                dol_syslog(get_class($this)."::delete_almacendet ".$errmsg, LOG_ERR);
                $this->error.=($this->error?', '.$errmsg:$errmsg);
            }
            $this->db->rollback();
            return -1*$error;
        }
        else
        {
            $this->db->commit();
            return 1;
        }
    }

}

class Solalmacendetfabricationline
 // extends CommonObject
{

    var $id;
    var $fk_almacendet;
    var $fk_fabricationdet;
    var $qty;
    var $qty_livree;
    var $price;
}
?>