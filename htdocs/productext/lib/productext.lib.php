<?php

function productext_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('productext');

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/product/card.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Product");
	$head[$h][2] = 'Product';
	$h++;
	$head[$h][0] = dol_buildpath("/productext/productlist/fiche.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Material");
	$head[$h][2] = 'material';
	$h++;
	//$h++;
	//$head[$h][0] = dol_buildpath("/fabrication/units/fiche.php?id=".$object->id,1);
	//$head[$h][1] = $langs->trans("Unit");
	//$head[$h][2] = 'unit';


	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'productext');
	return $head;
}

/**
 * Prepare array with list of tabs
 *
 * @param   Object  $object     Object related to tabs
 * @return  array               Array of tabs to show
 */
function promotion_prepare_head($object)
{
    global $langs, $conf;
    $h = 0;
    $head = array();

    $head[$h][0] = DOL_URL_ROOT.'/productext/promotion/card.php?id='.$object->fk_product.'&idr='.$object->id;
    $head[$h][1] = $langs->trans("Fiche");
    $head[$h][2] = 'card';
    $h++;
    $head[$h][0] = DOL_URL_ROOT.'/productext/promotion/carddet.php?id='.$object->fk_product.'&idr='.$object->id;
    $head[$h][1] = $langs->trans("Fichedet");
    $head[$h][2] = 'carddet';
    $h++;

        // Show more tabs from modules
        // Entries must be declared in modules descriptor with line
        // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
        // $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$h,'promotion');

    return $head;
}

/**
 * Prepare array with list of tabs
 *
 * @param   Object  $object     Object related to tabs
 * @return  array               Array of tabs to show
 */


function bonus_prepare_head($object)
{
    global $langs, $conf;
    $h = 0;
    $head = array();

    $head[$h][0] = DOL_URL_ROOT.'/productext/bonus/card.php?id='.$object->fk_product.'&idr='.$object->id;
    $head[$h][1] = $langs->trans("Fiche");
    $head[$h][2] = 'card';
    $h++;
    $head[$h][0] = DOL_URL_ROOT.'/productext/bonus/carddet.php?id='.$object->fk_product.'&idr='.$object->id;
    $head[$h][1] = $langs->trans("Listbonus");
    $head[$h][2] = 'carddet';
    $h++;

        // Show more tabs from modules
        // Entries must be declared in modules descriptor with line
        // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
        // $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$h,'promotion');

    return $head;
}

?>