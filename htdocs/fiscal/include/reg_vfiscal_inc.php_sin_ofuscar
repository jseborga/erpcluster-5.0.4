<?php
//variables necesarias
// $fkSubsidiaryid, $series, $modseller
// $numautsel, $numfactsel
// $sum_payment, $balance
// $now
//
if (empty($modseller)) $error++;

$sql = "SELECT t.rowid, t.series, t.num_ini, t.num_fin, t.num_ult, ";
$sql.= " num_autoriz, t.chave ";
$sql.= " FROM ".MAIN_DB_PREFIX."v_dosing AS t ";
$sql.= " WHERE ";
$sql.= " t.entity = ".$conf->entity;
if ($fkSubsidiaryid)
{
    $sql.= " AND t.fk_subsidiaryid = ".$fkSubsidiaryid;
    if ($modseller==2)
    {
        $sql.= " AND t.lote = 2 ";
        $sql.= " AND t.series = '".$series."'";
    }
    if ($modseller==1)
    {
        $sql.= " AND t.lote = 1 ";
    }
}
if ($fk_dosing)
    $sql.= " AND t.rowid = ".$fk_dosing;
$sql.= " AND active = 1 ";

$res1=$db->query($sql);
if ($res1)
{
    if ($db->num_rows($res1))
    {
        //echo '<hr>con dosing ';
        $objd = $db->fetch_object($res1);
        $llave = trim($objd->chave);
        $numaut = $objd->num_autoriz;
        if ($modseller==1)
        {
            $numaut    = $numautsel;
            $newnumfac = $numfactsel;
        }
        if ($modseller==2)
        {
            if ($objd->num_ult)
                $newnumfac = $objd->num_ult + 1;
            else
                $newnumfac = $objd->num_ini;
        }
        // actualizando el valor
        $objdosing = new Vdosing($db);
        $objdosing->fetch($objd->rowid);
        if ($objdosing->id == $objd->rowid && $nTotalTtc > 0)
        {
            if ($modseller==2) $objdosing->num_ult = $newnumfac;
            if ($modseller==1)
            {
                if ($objdosing->num_ult == $newnumfac)
                {
                    $error++;
                    $lInvoicechek = true;
                    $mesg = $langs->trans('Duplicateinvoicepleasecheck');
                }
            }
            if ($objdosing->num_ult < $newnumfac)
                $objdosing->num_ult = $newnumfac;

            $resultdosing = $objdosing->update($user);
            if ($resultdosing < 0)
            {
                $error++;
            }

            //llamando el codigo para generar codigo control
            if ($modseller==2)
            {
                require_once DOL_DOCUMENT_ROOT.'/fiscal/class/cc.php';
                $nowtext = date('Y').date('m').date('d');
                if (empty($nit))
                {
                    $nit = 0;
                                //$razsoc = $langs->trans('Sin Nombre');
                }
                            //$CodContr = new CodigoControl($numaut,$newnumfac,$nit,$nowtext,$obj_facturation->prixTotalTtc(),$llave);
                $CodContr = new CodigoControl(trim($numaut),trim($newnumfac),trim($nit),$nowtext,$nTotalTtc,trim($llave));
                $codigocontrol = $CodContr->generar();
                if (strlen($codigocontrol) > 15)
                {
                    unset($_SESSION['lastidvfiscal']);
                    $error++;
                    $errcc = 1;
                }
            }
            else
                $codigocontrol = '';
            //agregando a libros fiscales
            $objvfis = new Vfiscal($db);
            $objvfis->entity = $conf->entity;
            $objvfis->nfiscal = $newnumfac;
            $objvfis->serie   = $objd->series;
            $objvfis->fk_dosing = $objd->rowid;
            $objvfis->fk_facture = $id;
            $objvfis->fk_cliepro = $thirdpartyid;
            $objvfis->nit = $nit;
            $objvfis->razsoc = $razsoc;
            $objvfis->date_exp = $now;
            $objvfis->type_op = 1;
                        // venta
            $objvfis->num_autoriz = $numaut;
            $objvfis->cod_control = $codigocontrol;

                        //reemplazo

                        //$objvfis->baseimp1 = $obj_facturation->prixTotalTtc();
                        //$objvfis->valimp1 = $obj_facturation->montantTva();
            $objvfis->baseimp1 = $nTotalTtc;
            $objvfis->valimp1 = $nTotalTva;


                        //$objvfis->aliqimp1 = $fk_tva;
            $objvfis->aliqimp1 = empty($tab_tva['taux'])?$fk_tva:$tab_tva['taux'];
                        //agregando cambio y pago
            $objvfis->amount_payment = $sum_payment+0;
            $objvfis->amount_balance = $balance+0;
            $objvfis->date_create=$now;
            $objvfis->fk_user_create = $user->id;
            $objvfis->date_mod=$now;
            $objvfis->fk_user_mod = $user->id;
            $objvfis->tms = $now;
            $objvfis->status = 1;
            if ($modseller==1)
                $objvfis->status_print = 0;
            $idvfiscal = $objvfis->create($user);
            if ($idvfiscal < 0)
            {
                $error++;
                setEventMessages($objvfis->error,$objvfis->errors,'errors');
            }
            if (empty($error)) $_SESSION['lastidvfiscal'] = $idvfiscal;
        }
        else
            $error++;
    }
}

?>