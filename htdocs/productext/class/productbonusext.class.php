<?php
require_once DOL_DOCUMENT_ROOT.'/productext/class/productbonus.class.php';

class Productbonusext extends Productbonus
{
    /**
     *  Return a link to the object card (with optionaly the picto)
     *
     *  @param  int     $withpicto          Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
     *  @param  string  $option             On what the link point to
     *  @param  int     $notooltip          1=Disable tooltip
     *  @param  int     $maxlen             Max length of visible user name
     *  @param  string  $morecss            Add more css on link
     *  @return string                      String with URL
     */
    function getNomUrl($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
    {
        global $db, $conf, $langs;
        global $dolibarr_main_authentication, $dolibarr_main_demo;
        global $menumanager;

        if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

        $result = '';
        $companylink = '';

        $label = '<u>' . $langs->trans("Productbonus") . '</u>';
        $label.= '<br>';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $url = DOL_URL_ROOT.'/productext/bonus/'.'card.php?id='.$this->fk_product.'&idr='.$this->id;

        $linkclose='';
        if (empty($notooltip))
        {
            if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
            {
                $label=$langs->trans("ShowProject");
                $linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
            }
            $linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
        }
        else $linkclose = ($morecss?' class="'.$morecss.'"':'');

        $linkstart = '<a href="'.$url.'"';
        $linkstart.=$linkclose.'>';
        $linkend='</a>';

        if ($withpicto)
        {
            $result.=($linkstart.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
        }
        $result.= $linkstart . $this->ref . $linkend;
        return $result;
    }
 }

?>