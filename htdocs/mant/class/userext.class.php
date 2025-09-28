<?php
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

class Userext extends User
{
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
    public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
    {
        dol_syslog(__METHOD__, LOG_DEBUG);

        $sql = 'SELECT';
        $sql .= ' t.rowid,';

        $sql .= " t.entity,";
        $sql .= " t.ref_ext,";
        $sql .= " t.ref_int,";
        $sql .= " t.employee,";
        $sql .= " t.fk_establishment,";
        $sql .= " t.datec,";
        $sql .= " t.tms,";
        $sql .= " t.fk_user_creat,";
        $sql .= " t.fk_user_modif,";
        $sql .= " t.login,";
        $sql .= " t.pass,";
        $sql .= " t.pass_crypted,";
        $sql .= " t.pass_temp,";
        $sql .= " t.api_key,";
        $sql .= " t.gender,";
        $sql .= " t.civility,";
        $sql .= " t.lastname,";
        $sql .= " t.firstname,";
        $sql .= " t.address,";
        $sql .= " t.zip,";
        $sql .= " t.town,";
        $sql .= " t.fk_state,";
        $sql .= " t.fk_country,";
        $sql .= " t.job,";
        $sql .= " t.skype,";
        $sql .= " t.office_phone,";
        $sql .= " t.office_fax,";
        $sql .= " t.user_mobile,";
        $sql .= " t.email,";
        $sql .= " t.signature,";
        $sql .= " t.admin,";
        $sql .= " t.module_comm,";
        $sql .= " t.module_compta,";
        $sql .= " t.fk_soc,";
        $sql .= " t.fk_socpeople,";
        $sql .= " t.fk_member,";
        $sql .= " t.fk_user,";
        $sql .= " t.note_public,";
        $sql .= " t.note,";
        $sql .= " t.datelastlogin,";
        $sql .= " t.datepreviouslogin,";
        $sql .= " t.egroupware_id,";
        $sql .= " t.ldap_sid,";
        $sql .= " t.openid,";
        $sql .= " t.statut,";
        $sql .= " t.photo,";
        $sql .= " t.lang,";
        $sql .= " t.color,";
        $sql .= " t.barcode,";
        $sql .= " t.fk_barcode_type,";
        $sql .= " t.accountancy_code,";
        $sql .= " t.nb_holiday,";
        $sql .= " t.thm,";
        $sql .= " t.tjm,";
        $sql .= " t.salary,";
        $sql .= " t.salaryextra,";
        $sql .= " t.dateemployment,";
        $sql .= " t.weeklyhours";


        $sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';

        // Manage filter
        $sqlwhere = array();
        if (count($filter) > 0) {
            foreach ($filter as $key => $value) {
                $sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
            }
        }
        $sql.= ' WHERE 1 = 1';
        if (! empty($conf->multicompany->enabled)) {
            $sql .= " AND entity IN (" . getEntity("user", 1) . ")";
        }
        if (count($sqlwhere) > 0) {
            $sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
        }
        if ($filterstatic){
            $sql.= $filterstatic;
        }
        if (!empty($sortfield)) {
            $sql .= $this->db->order($sortfield,$sortorder);
        }
        if (!empty($limit)) {
         $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
        }

        $this->lines = array();

        $resql = $this->db->query($sql);
        if ($resql) {
            $num = $this->db->num_rows($resql);

            while ($obj = $this->db->fetch_object($resql)) {
                $line = new UserLine();

                $line->id = $obj->rowid;

                $line->entity = $obj->entity;
                $line->ref_ext = $obj->ref_ext;
                $line->ref_int = $obj->ref_int;
                $line->employee = $obj->employee;
                $line->fk_establishment = $obj->fk_establishment;
                $line->datec = $this->db->jdate($obj->datec);
                $line->tms = $this->db->jdate($obj->tms);
                $line->fk_user_creat = $obj->fk_user_creat;
                $line->fk_user_modif = $obj->fk_user_modif;
                $line->login = $obj->login;
                $line->pass = $obj->pass;
                $line->pass_crypted = $obj->pass_crypted;
                $line->pass_temp = $obj->pass_temp;
                $line->api_key = $obj->api_key;
                $line->gender = $obj->gender;
                $line->civility = $obj->civility;
                $line->lastname = $obj->lastname;
                $line->firstname = $obj->firstname;
                $line->address = $obj->address;
                $line->zip = $obj->zip;
                $line->town = $obj->town;
                $line->fk_state = $obj->fk_state;
                $line->fk_country = $obj->fk_country;
                $line->job = $obj->job;
                $line->skype = $obj->skype;
                $line->office_phone = $obj->office_phone;
                $line->office_fax = $obj->office_fax;
                $line->user_mobile = $obj->user_mobile;
                $line->email = $obj->email;
                $line->signature = $obj->signature;
                $line->admin = $obj->admin;
                $line->module_comm = $obj->module_comm;
                $line->module_compta = $obj->module_compta;
                $line->fk_soc = $obj->fk_soc;
                $line->fk_socpeople = $obj->fk_socpeople;
                $line->fk_member = $obj->fk_member;
                $line->fk_user = $obj->fk_user;
                $line->note_public = $obj->note_public;
                $line->note = $obj->note;
                $line->datelastlogin = $this->db->jdate($obj->datelastlogin);
                $line->datepreviouslogin = $this->db->jdate($obj->datepreviouslogin);
                $line->egroupware_id = $obj->egroupware_id;
                $line->ldap_sid = $obj->ldap_sid;
                $line->openid = $obj->openid;
                $line->statut = $obj->statut;
                $line->photo = $obj->photo;
                $line->lang = $obj->lang;
                $line->color = $obj->color;
                $line->barcode = $obj->barcode;
                $line->fk_barcode_type = $obj->fk_barcode_type;
                $line->accountancy_code = $obj->accountancy_code;
                $line->nb_holiday = $obj->nb_holiday;
                $line->thm = $obj->thm;
                $line->tjm = $obj->tjm;
                $line->salary = $obj->salary;
                $line->salaryextra = $obj->salaryextra;
                $line->dateemployment = $this->db->jdate($obj->dateemployment);
                $line->weeklyhours = $obj->weeklyhours;



                if ($lView && $num == 1) $this->fetch($obj->rowid);

                $this->lines[$line->id] = $line;
            }
            $this->db->free($resql);

            return $num;
        } else {
            $this->errors[] = 'Error ' . $this->db->lasterror();
            dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);

            return - 1;
        }
    }
}

/**
 * Class UserLine
 */
class UserLine
{
    /**
     * @var int ID
     */
    public $id;
    /**
     * @var mixed Sample line property 1
     */

    public $entity;
    public $ref_ext;
    public $ref_int;
    public $employee;
    public $fk_establishment;
    public $datec = '';
    public $tms = '';
    public $fk_user_creat;
    public $fk_user_modif;
    public $login;
    public $pass;
    public $pass_crypted;
    public $pass_temp;
    public $api_key;
    public $gender;
    public $civility;
    public $lastname;
    public $firstname;
    public $address;
    public $zip;
    public $town;
    public $fk_state;
    public $fk_country;
    public $job;
    public $skype;
    public $office_phone;
    public $office_fax;
    public $user_mobile;
    public $email;
    public $signature;
    public $admin;
    public $module_comm;
    public $module_compta;
    public $fk_soc;
    public $fk_socpeople;
    public $fk_member;
    public $fk_user;
    public $note_public;
    public $note;
    public $datelastlogin = '';
    public $datepreviouslogin = '';
    public $egroupware_id;
    public $ldap_sid;
    public $openid;
    public $statut;
    public $photo;
    public $lang;
    public $color;
    public $barcode;
    public $fk_barcode_type;
    public $accountancy_code;
    public $nb_holiday;
    public $thm;
    public $tjm;
    public $salary;
    public $salaryextra;
    public $dateemployment = '';
    public $weeklyhours;

    /**
     * @var mixed Sample line property 2
     */

}

?>