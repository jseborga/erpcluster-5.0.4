<?php
include_once DOL_DOCUMENT_ROOT.'/budget/class/incidentsdet.class.php';

class Incidentsdetext extends Incidentsdet
{
}
class IncidentsdetLineext extends CommonObjectLine
{
    /**
     * Constructor
     *
     * @param DoliDb $db Database handler
     */
    public function __construct(DoliDB $db)
    {
        $this->db = $db;
    }
}
?>