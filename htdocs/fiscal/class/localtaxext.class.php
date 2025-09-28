<?php
require_once DOL_DOCUMENT_ROOT.'/compta/localtax/class/localtax.class.php';

class Localtaxext extends Localtax
{
	/**
	 *	Returns clickable name
	 *
	 *	@param		int		$withpicto		0=Link, 1=Picto into link, 2=Picto
	 *	@param		string	$option			Sur quoi pointe le lien
	 *	@return		string					Chaine avec URL
	 */
	function getNomUrladd($withpicto=0, $option='')
	{
		global $langs;

		$result='';
		$label=$langs->trans("ShowVatPayment").': '.$this->ref;

        $link = '<a href="'.DOL_URL_ROOT.'/fiscal/localtax/card.php?id='.$this->id.'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
		$linkend='</a>';

		$picto='payment';

        if ($withpicto) $result.=($link.img_object($label, $picto, 'class="classfortooltip"').$linkend);
		if ($withpicto && $withpicto != 2) $result.=' ';
		if ($withpicto != 2) $result.=$link.$this->ref.$linkend;
		return $result;
	}
}
?>