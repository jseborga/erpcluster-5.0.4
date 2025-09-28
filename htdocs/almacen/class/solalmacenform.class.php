<?php

class Solalmacenform
{

	var $db;
	var $error;


	/**
   	*  Constructor
   	*
   	*  @param    DoliDB    $db      Database handler
   	*/
	function __construct($db)
	{
		$this->db = $db;
		return 1;
	}
	/* This is to show add lines */

	/**
	*	Show add predefined products/services form
	*  TODO Edit templates to use global variables and include them directly in controller call
	*  But for the moment we don't know if it's possible as we keep a method available oci_new_collection(connection, tdo) overloaded objects.
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
		global $conf,$langs,$object,$objunits,$objform;
		global $form,$bcnd,$var;
		if (!empty($id))
			$this->id = $id;
		// Use global variables + $dateSelector + $seller and $buyer
		include(DOL_DOCUMENT_ROOT.'/almacen/core/tpl/predefinedproductline_create_sd.tpl.php');
	}

	/**
	*	Show add predefined products/services form
	*  TODO Edit templates to use global variables and include them directly in controller call
	*  But for the moment we don't know if it's possible as we keep a method available oci_new_collection(connection, tdo) overloaded objects.
	*
	*  @param  int	    		$dateSelector       1=Show also date range input fields
	*  @param	Societe			$seller				Object thirdparty who sell
	*  @param	Societe			$buyer				Object thirdparty who buy
	*	@param	HookManager		$hookmanager		Hook manager instance
	*	@return	void
	*	@deprecated
	*  Sin DESCUENTO
	*/
	function formAddPredefinedProduct_fsd($dateSelector,$seller,$buyer,$hookmanager=false,$id='')
	{
		global $conf,$langs,$object,$objunits,$objFabrication,$objform;
		global $form,$bcnd,$var;
		if (!empty($id))
			$this->id = $id;
		// Use global variables + $dateSelector + $seller and $buyer
		include(DOL_DOCUMENT_ROOT.'/almacen/core/tpl/predefinedproductline_create_fsd.tpl.php');
	}
}
?>