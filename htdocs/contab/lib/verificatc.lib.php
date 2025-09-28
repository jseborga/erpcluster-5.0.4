<?php

list($country,$countrycod,$countryname) = explode(':',$conf->global->MAIN_INFO_SOCIETE_COUNTRY) ;
$objectcop = new Csindexesext($db);
$objectcop->fetch_last($country);
if ($objectcop->date_ind <> $db->jdate(date('Y-m-d')))
  {
    header("Location: ".DOL_URL_ROOT.'/contab/exchangerate/fiche.php?action=create');
    exit;
  }
?>