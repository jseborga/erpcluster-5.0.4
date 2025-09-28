<?php
require_once DOL_DOCUMENT_ROOT.'/productext/class/productadd.class.php';
class Productaddext extends Productadd
{
	/* This is to show add lines */

	/**
   *	Show add predefined products/services form
   *  TODO Edit templates to use global variables and include them directly in controller call
   *  But for the moment we don't know if it's possible as we keep a method available on overloaded objects.
   *
   *  @param  int	    		$dateSelector       1=Show also date range input fields
   *  @param	Societe			$seller				Object thirdparty who sell
   *  @param	Societe			$buyer				Object thirdparty who buy
   *	@param	HookManager		$hookmanager		Hook manager instance
   *	@return	void
   *	@deprecated
   *  Sin DESCUENTO
	*/
	function formAddPredefinedProduct_sd($dateSelector,$seller,$buyer,$hookmanager=false,$id='')
	{
		global $conf,$langs,$object,$objunits;
		global $form,$bcnd,$var;
		if (!empty($id))
			$this->id = $id;
		// Use global variables + $dateSelector + $seller and $buyer
		include(DOL_DOCUMENT_ROOT.'/productext/tpl/predefinedproductline_create_sd.tpl.php');
	}

	/**
	 *	Show add predefined products/services form
	 *  TODO Edit templates to use global variables and include them directly in controller call
	 *  But for the moment we don't know if it's possible as we keep a method available on overloaded objects.
	 *
	 *  @param  int	    		$dateSelector       1=Show also date range input fields
	 *  @param	Societe			$seller				Object thirdparty who sell
	 *  @param	Societe			$buyer				Object thirdparty who buy
	 *	@param	HookManager		$hookmanager		Hook manager instance
	 *	@return	void
	 *	@deprecated
	 */
	function formAddPredefinedProductSon($dateSelector,$seller,$buyer,$hookmanager=false,$id='')
	{
		global $conf,$langs,$object,$objunits;
		global $form,$bcnd,$var;
		if (!empty($id))
			$this->id = $id;
	// Use global variables + $dateSelector + $seller and $buyer
		include(DOL_DOCUMENT_ROOT.'/productext/tpl/predefinedproductline_create_son.tpl.php');
	}
	/* This is to show add lines */

	/**
	 *	Show add predefined products/services form
	 *  TODO Edit templates to use global variables and include them directly in controller call
	 *  But for the moment we don't know if it's possible as we keep a method available on overloaded objects.
	 *
	 *  @param  int	    		$dateSelector       1=Show also date range input fields
	 *  @param	Societe			$seller				Object thirdparty who sell
	 *  @param	Societe			$buyer				Object thirdparty who buy
	 *	@param	HookManager		$hookmanager		Hook manager instance
	 *	@return	void
	 *	@deprecated
	 */
	function formAddPredefinedProduct_lm($dateSelector,$seller,$buyer,$hookmanager=false,$id='')
	{
		global $conf,$langs,$object,$objunits;
		global $form,$bcnd,$var;
		if (!empty($id))
			$this->id = $id;
	// Use global variables + $dateSelector + $seller and $buyer
		include(DOL_DOCUMENT_ROOT.'/productext/tpl/predefinedproductline_create_lm.tpl.php');
	}

  /**
   *	Show add predefined products/services form
   *  TODO Edit templates to use global variables and include them directly in controller call
   *  But for the moment we don't know if it's possible as we keep a method available on overloaded objects.
   *
   *  @param  int	    		$dateSelector       1=Show also date range input fields
   *  @param	Societe			$seller				Object thirdparty who sell
   *  @param	Societe			$buyer				Object thirdparty who buy
   *	@param	HookManager		$hookmanager		Hook manager instance
   *	@return	void
   *	@deprecated
   */
  function formAddPredefinedProductAlt($dateSelector,$seller,$buyer,$hookmanager=false,$id='')
  {
  	global $conf,$langs,$object,$objunits;
  	global $form,$bcnd,$var;
  	if (!empty($id))
  		$this->id = $id;
	// Use global variables + $dateSelector + $seller and $buyer
  	include(DOL_DOCUMENT_ROOT.'/productext/tpl/predefinedproductline_create_alt.tpl.php');
  }

}

?>