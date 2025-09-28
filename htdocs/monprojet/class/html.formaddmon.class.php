<?php
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';


class FormAddmon extends Form
{
    /**
     *    Return a HTML area with the reference of object and a navigation bar for a business object
     *    To add a particular filter on select, you must set $object->next_prev_filter to SQL criteria.
     *
     *    @param	object	$object			Object to show
     *    @param	string	$paramid   		Name of parameter to use to name the id into the URL link
     *    @param	string	$morehtml  		More html content to output just before the nav bar
     *    @param	int		$shownav	  	Show Condition (navigation is shown if value is 1)
     *    @param	string	$fieldid   		Nom du champ en base a utiliser pour select next et previous (we make the select max and min on this field)
     *    @param	string	$fieldref   	Nom du champ objet ref (object->ref) a utiliser pour select next et previous
     *    @param	string	$morehtmlref  	Code html supplementaire a afficher apres ref
     *    @param	string	$moreparam  	More param to add in nav link url.
     *	  @param	int		$nodbprefix		Do not include DB prefix to forge table name
     * 	  @return	string    				Portion HTML avec ref + boutons nav
     */
  function showrefnavadd ($object,$paramid,$morehtml='',$shownav=1,$fieldid='rowid',$fieldref='ref',$morehtmlref='',$moreparam='',$nodbprefix=0)
  {
    global $langs,$conf,$db;

        $ret='';
        if (empty($fieldid))  $fieldid='rowid';
        if (empty($fieldref)) $fieldref='ref';
        $refstatic = $object->$fieldref;
        //print "paramid=$paramid,morehtml=$morehtml,shownav=$shownav,$fieldid,$fieldref,$morehtmlref,$moreparam";
        $object->load_previous_next_refadd((isset($object->next_prev_filter)?$object->next_prev_filter:''),$fieldid,$object,$nodbprefix);

        //$previous_ref = $object->ref_previous?'<a data-role="button" data-icon="arrow-l" data-iconpos="left" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_previous).$moreparam.'">'.(empty($conf->dol_use_jmobile)?img_picto($langs->trans("Previous"),'previous.png'):'&nbsp;').'</a>':'';
        //$next_ref     = $object->ref_next?'<a data-role="button" data-icon="arrow-r" data-iconpos="right" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_next).$moreparam.'">'.(empty($conf->dol_use_jmobile)?img_picto($langs->trans("Next"),'next.png'):'&nbsp;').'</a>':'';
        $previous_ref = $object->ref_previous?'<a data-role="button" data-icon="arrow-l" data-iconpos="left" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_previous).$moreparam.'">'.(empty($conf->dol_use_jmobile)?'<':'&nbsp;').'</a>':'';
        $next_ref     = $object->ref_next?'<a data-role="button" data-icon="arrow-r" data-iconpos="right" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_next).$moreparam.'">'.(empty($conf->dol_use_jmobile)?'>':'&nbsp;').'</a>':'';

        //print "xx".$previous_ref."x".$next_ref;
        $ret.='<div style="vertical-align: middle"><div class="inline-block floatleft refid'.(($shownav && ($previous_ref || $next_ref))?' refidpadding':'').'">';
        $ret.=dol_htmlentities($refstatic);
        if ($morehtmlref)
        {
            $ret.=' '.$morehtmlref;
        }
			$ret.='</div>';

        if ($previous_ref || $next_ref || $morehtml)
        {
        	$ret.='<div class="pagination"><ul>';
        }
        if ($morehtml)
        {
            //$ret.='</td><td class="paddingrightonly" align="right">'.$morehtml;
            $ret.='<li class="noborder litext">'.$morehtml.'</li>';
        }
        if ($shownav && ($previous_ref || $next_ref))
        {
            //$ret.='</td><td class="nobordernopadding" align="center" width="20">'.$previous_ref.'</td>';
            //$ret.='<td class="nobordernopadding" align="center" width="20">'.$next_ref;
            $ret.='<li class="pagination">'.$previous_ref.'</li>';
            $ret.='<li class="pagination">'.$next_ref.'</li>';
        }
        if ($previous_ref || $next_ref || $morehtml)
        {
            //$ret.='</td></tr></table>';
            $ret.='</ul></div>';
        }
		$ret.='</div>';

        return $ret;
    }
}
?>