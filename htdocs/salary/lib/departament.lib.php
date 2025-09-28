<?php

function departament_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('salary@salary');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/salary/departament/fiche.php?rowid=".$object->id,1);
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;
	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'departament');

	return $head;
}

?>