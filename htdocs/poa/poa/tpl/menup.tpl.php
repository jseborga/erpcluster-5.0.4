

<?php
print '<nav class="navbar navbar-inverse navbar-fixed-top">';
print '<div class="container">';
print '<div class="navbar-header">';
print '<button class="navbar-toggle collapsed" aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" type="button">';
print '<span class="sr-only">Toggle navigation</span>';
print '<span class="icon-bar"></span>';
print '<span class="icon-bar"></span>';
print '<span class="icon-bar"></span>';
print '</button>';
print '<a class="navbar-brand" href="#">POA</a>';
print '</div>';

print '<div id="navbar" class="navbar-collapse collapse">';
print '<ul class="nav navbar-nav">';

print '<li class=""><a href="'.DOL_URL_ROOT.'/index.php?mainmenu=home'.'">'.$langs->trans('Init').'</a></li>';
print '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">'.$langs->trans('Ver').'<span class="caret"></span>'.'</a>';
print '<ul class="dropdown-menu">';
print '<li><a href="'.$_SERVER['PHP'].'?action=menu1&f11=1'.($search_login?'&search_login='.$search_login:'').'">'.$langs->trans('Metas').'</a></li>';
print '<li><a href="'.$_SERVER['PHP'].'?action=menu1&f1=1'.($search_login?'&search_login='.$search_login:'').'">'.$langs->trans('Insumos').'</a></li>';
print '<li><a href="'.$_SERVER['PHP'].'?action=menu1&f1=2'.($search_login?'&search_login='.$search_login:'').'">'.$langs->trans('Actividadesw').'</a></li>';
print '<li><a href="'.$_SERVER['PHP'].'?action=menu1&f1=3'.($search_login?'&search_login='.$search_login:'').'">'.$langs->trans('Concluidas').'</a></li>';
print '<li><a href="'.$_SERVER['PHP'].'?action=menu1&f1=4'.($search_login?'&search_login='.$search_login:'').'">'.$langs->trans('En curso').'</a></li>';
print '<li><a href="'.$_SERVER['PHP'].'?action=menu1&f1=5'.($search_login?'&search_login='.$search_login:'').'">'.$langs->trans('Demorado').'</a></li>';
print '<li><a href="'.$_SERVER['PHP'].'?action=menu1&f1=6'.($search_login?'&search_login='.$search_login:'').'">'.$langs->trans('Sin cronograma').'</a></li>';
print '</ul>';
print '</li>';

print '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">'.$langs->trans('Vista').'<span class="caret"></span>'.'</a>';
print '<ul class="dropdown-menu">';
print '<li><a href="'.$_SERVER['PHP'].'?action=menu2&r1=1'.($search_login?'&search_login='.$search_login:'').'">'.$langs->trans('Estandard').'</a></li>';
print '<li><a href="'.$_SERVER['PHP'].'?action=menu2&r1=2'.($search_login?'&search_login='.$search_login:'').'">'.$langs->trans('Seguir').'</a></li>';
print '<li><a href="'.$_SERVER['PHP'].'?action=menu2&r1=3'.($search_login?'&search_login='.$search_login:'').'">'.$langs->trans('Nombres').'</a></li>';
print '<li><a href="'.DOL_URL_ROOT.'/poa/structure/liste.php'.'?action=menu2&r1=3">'.$langs->trans('Estructura').'</a></li>';
print '<li><a href="'.DOL_URL_ROOT.'/poa/pac/liste.php'.'?action=menu2&r1=5">'.$langs->trans('PAC').'</a></li>';
print '<li><a href="'.DOL_URL_ROOT.'/poa/area/liste.php'.'?action=menu2&r1=5">'.$langs->trans('Area').'</a></li>';
print '</ul>';
print '</li>';

print '<li class="dropdown">';
//print '<a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="'.$_SERVER['PHP_SELF'].'?action=menu2&mr1=3&m1=3'.'">'.$langs->trans('Filter').'</a>';
$mingest = date('Y') - 5;
$maxgest = date('Y');
print '<div style="margin: 0 auto;padding:1rem;">';
print '<form name="myform" method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="number" min="'.$mingest.'" max="'.$maxgest.'" name="period_year" value="'.$_SESSION['period_year'].'" onchange="formsubmit()">';
print '</form>';
print '</div>';
print '</li>';
print '</ul>';
print '</div>';
print '</div>';
print '</nav>';

?>