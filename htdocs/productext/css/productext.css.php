<?php
/* Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    htdocs/modulebuilder/template/css/mymodule.css.php
 * \ingroup mymodule
 * \brief   CSS file for module MyModule.
 */

//if (! defined('NOREQUIREUSER')) define('NOREQUIREUSER','1');	// Not disabled because need to load personalized language
//if (! defined('NOREQUIREDB'))   define('NOREQUIREDB','1');	// Not disabled. Language code is found on url.
if (! defined('NOREQUIRESOC'))    define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN')) define('NOREQUIRETRAN','1');	// Not disabled because need to do translations
if (! defined('NOCSRFCHECK'))     define('NOCSRFCHECK',1);
if (! defined('NOTOKENRENEWAL'))  define('NOTOKENRENEWAL',1);
if (! defined('NOLOGIN'))         define('NOLOGIN',1);          // File must be accessed by logon page so without login
//if (! defined('NOREQUIREMENU'))   define('NOREQUIREMENU',1);  // We need top menu content
if (! defined('NOREQUIREHTML'))   define('NOREQUIREHTML',1);
if (! defined('NOREQUIREAJAX'))   define('NOREQUIREAJAX','1');

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/../main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/../main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

session_cache_limiter(FALSE);

// Load user to have $user->conf loaded (not done by default here because of NOLOGIN constant defined) and load permission if we need to use them in CSS
/*if (empty($user->id) && ! empty($_SESSION['dol_login']))
{
	$user->fetch('',$_SESSION['dol_login']);
	$user->getrights();
}*/


// Define css type
header('Content-type: text/css');
// Important: Following code is to cache this file to avoid page request by browser at each Dolibarr page access.
// You can use CTRL+F5 to refresh your browser cache.
if (empty($dolibarr_nocache))
	header('Cache-Control: max-age=3600, public, must-revalidate');
else
	header('Cache-Control: no-cache');

?>

/* Copyright (C) 2007-2008 Jeremie Ollivier <jeremie.o@laposte.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

 /* formato login*/
 label {
 	display: block;
 	color: #999;
 }
 .cf:before,
 .cf:after {
 	content: "";
 	display: table;
 }

 .cf:after {
 	clear: both;
 }
 .cf {
 	*zoom: 1;
 }
 :focus {
 	outline: 0;
 }
 .loginform {
 	width: 410px;
 	margin: 50px auto;
 	padding: 25px;
 	background-color: rgba(250,250,250,0.5);
 	border-radius: 5px;
 	box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.2),
 	inset 0px 1px 0px 0px rgba(250, 250, 250, 0.5);
 	border: 1px solid rgba(0, 0, 0, 0.3);
 }
 .loginform ul {
 	padding: 0;
 	margin: 0;
 }
 .loginform li {
 	display: inline-table;
 	/*	float: left;*/
 }
 .loginform input:not([type=submit]) {
 	padding: 5px;
 	margin-right: 10px;
 	border: 1px solid rgba(0, 0, 0, 0.3);
 	border-radius: 3px;
 	box-shadow: inset 0px 1px 3px 0px rgba(0, 0, 0, 0.1),
 	0px 1px 0px 0px rgba(250, 250, 250, 0.5) ;
 }
 .loginform input[type=submit] {
 	border: 1px solid rgba(0, 0, 0, 0.3);
 	background: #64c8ef; /* Old browsers */
 	background: -moz-linear-gradient(top,  #64c8ef 0%, #00a2e2 100%); /* FF3.6+ */
 	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#64c8ef), color-stop(100%,#00a2e2)); /* Chrome,Safari4+ */
 	background: -webkit-linear-gradient(top,  #64c8ef 0%,#00a2e2 100%); /* Chrome10+,Safari5.1+ */
 	background: -o-linear-gradient(top,  #64c8ef 0%,#00a2e2 100%); /* Opera 11.10+ */
 	background: -ms-linear-gradient(top,  #64c8ef 0%,#00a2e2 100%); /* IE10+ */
 	background: linear-gradient(to bottom,  #64c8ef 0%,#00a2e2 100%); /* W3C */
 	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#64c8ef', endColorstr='#00a2e2',GradientType=0 ); /* IE6-9 */
 	color: #fff;
 	padding: 5px 15px;
 	margin-right: 0;
 	margin-top: 15px;
 	border-radius: 3px;
 	text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.3);
 }
 /*fin formato login*/


 p {
 	margin: 0;
 }

 [class*="grid-"] {
 	float: left;
 	padding-right: 20px;
 	position: relative;
 }
 .grid-1-3 {
 	width: 21%;
 }

 #grid-2-3 {
 	width: 75%;
 }

 .conteneur {
	position:absolute;
	width:100%;
	top:1px;
	bottom:1px;
}

.pageBox { position:absolute; top:1px; left:1px; right:1px; bottom:1px; min-width:200px}

.conteneur_img_gauche {
	background: url("../img/bg_conteneur_gauche.png") top left repeat-y;
}

.conteneur_img_droite {
	background: url("../img/bg_conteneur_droite.png") top right repeat-y;
}



.menu_bloc {
	margin-left: 3px;
	width:40%;
	min-width:655px;
	float:left;
}
.menu_balance {
	margin-left: 12px;
	margin-top:4px;
	width:60%;
	float:left;
	font-size:14px;
	height:20px;
}
.menu_bloc_user {
	margin-left: 1px;
	width:40%;
	min-width:300px;
	float:left;
}
.menu_blocall {
	margin-left: 3px;
	width:49%;
	min-width:655px;
	float:left;
}
.menu_bloc_userall {
	margin-left: 1px;
	width:50%;
	min-width:300px;
	float:left;
}
.menu_sell {
	margin: 0;
	padding: 0;
	list-style-type: none;
	padding-top: 2px;
}

.menu_sell li {
	float: left;
}
.menu_sell li.der {
	float: right;
}
.menu_sell a {
	display: block;
	color: #fff;
	text-decoration: none;
	width: 60px;
	padding-left: 2px;
	height: 48px;
}

.menu_choixg a {
	display: block;
	color: #fff;
	text-decoration: none;
	width: 100px;
	padding-left: 54px;
	height: 48px;
	background: url('../img/gasto.png') top left no-repeat;
}
.menu_choixt a {
	display: block;
	color: #fff;
	text-decoration: none;
	width: 100px;
	padding-left: 54px;
	height: 48px;
	background: url('../img/transfer.png') top left no-repeat;
}
.menu_report a {
	display: block;
	color: #fff;
	text-decoration: none;
	width: 100px;
	padding-left: 54px;
	height: 48px;
	background: url('../img/report.png') top left no-repeat;
}

.menu_choix1 a {
	background: url('../img/new.png') top left no-repeat;
}
.menu_choixp a {
	background: url('../img/newpedido.png') top left no-repeat;
}
.menu_choixg a {
	background: url('../img/gasto.png') top left no-repeat;
}
.menu_choixt a {
	background: url('../img/transfer.png') top left no-repeat;
}
.menu_report a {
	background: url('../img/report.png') top left no-repeat;
}

.menu_choix2 a {
	background: url('../img/gescom.png') top left no-repeat;
}

.menu_choix1 a:hover,.menu_choix2 a:hover,.menu_choixp a:hover,.menu_choixg a:hover,.menu_choixt a:hover {
	color: #6d3f6d;
}


.menu_choix0 {
	font-size: 14px;
	text-align: right;
	font-style: italic;
	font-weight: bold;
	display: table-row;
	color: #333;
	text-decoration: none;
	padding-left: 5px;
}

.menu_choix0 a {
	font-weight: normal;
	text-decoration: none;
}

.menu_choixf {
	font-size: 14px;
	text-align: right;
	font-style: italic;
	font-weight: bold;
	color:#000000;
	display: block;
	text-decoration: none;
	padding-left: 5px;
}

.menu_choixf a {
	font-weight: normal;
	text-decoration: none;
}



/* ------------------- R�capitulatif des articles ------------------- */
.contentBox {
	position:absolute;
	width:100%;
	top:1px;
	bottom:0px;
}
.contentBox1 {
	position:absolute;
	width:100%;
	top:95px;
	bottom:0px;
}
.headerBox {

    height: 110px;
    width:100%;
    left:0px;
    top:0;
    position:fixed;
    padding-left:310px;

}
.headerBoxall {

    height: 100px;
    width:100%;
    min-width:1320px;
    left:0;
    top:0;
    position:fixed;
    margin: 0 1px;
}
/*formato para categorias*/
.headerBox1 {
	background-color: #33afff;
	position:absolute;
	top:111px;
	left:310px;
	width:77%;
	height:100px;
}
.headerBox2 {
	position:absolute;
	top:115px;
	width:100%;
	height:92px;
}

.headerCBoxl {
	position:relative;
	top:5px;
	width:49%;
	float: left;
	height:150px;
}
.headerCBoxr { position:relative;
	top:5px;
	width:49%;
	height:120px;
	float: right;
}

.headerBoxClient {
	position:absolute;
	top:71px;
	width:100%;
	left:5px;
	height:25px;
}

.headerBoxClient2 {
	position:absolute;
	top:75px;
	width:100%;
	left:5px;
	height:90px;
}

.footerBox {
	position:absolute;
	width:81%;
	left:271px;
	height:25%;
	background-color:#94ccef;
	bottom:5px;
}
.footerBox2 {
	position:absolute;
	width:99%;
	left:5px;
	height:25%;/*200px;*/
	background-color:#94ccef;
	bottom:5px;
}

.liste_articles {
	width: 270px;
	position:absolute;
	height:90%;
}
.liste_prodventa {
	float:lef;
	width: 97%;
	position:absolute;
	overflow-y:auto;
	height:100%;
	bottom:2px;
}

.liste_resumen {
	background-color: #47abfd;
	width: 302px;
	position:absolute;
	bottom:0px;
	left:2px;
	right:10px;
	top:2px;
	height: 168px;
}

.col_logo {
	width: 200px;
	position:absolute;
	height:23%;
}
.col_logo1 {
	width: 270px;
	position:absolute;
	height:16%;
}

.liste_menu {
	position:fixed;
	width:81%;
	left:0px;
	height:32%;
}

.liste_menu2 {
	position:absolute;
	width:86%;
	left:200px;
	height:23%;
}

.liste_product {
	top:211px;
	position:absolute;
	width:85%;
	left:270px;
	height:50%;
	background-color:white;
}
.liste_product2 {
	top:23%;
	position:absolute;
	font-size: 1.2em;
	width:99%;
	left:5px;
	height:3%;
	min-height:28px;
	background-color:white;
}
.liste_product3 {
	top:28%;
	position:absolute;
	width:99%;
	left:5px;
	height:40%;
	overflow-y:auto;
	background-color:white;
}
.liste_product4 {
	top:68%;
	position:absolute;
	width:99%;
	left:5px;
	height:30px;
	background-color:white;
}
.recarga_item {
	padding: 0 10px 0 10px;
	width: 97%;
	height:100%;
	position: absolute;
	top:124px;
}
.calc{
	padding: 0 3px;
	width: 90%;
	position: relative;
	height:30px;
	top:1px;

}
.recarga_cat {
	box-shadow: 0 2px 5px #666666;
	border-radius: 8px 8px 8px 8px;
	margin: auto;
	position: absolute;
	top:1px;
	left:1px;
	width: 100%;
	padding-left:1px;
	padding-top:1px;

}

.contentBox1,
.liste_articles1,
.liste_product,liste_menu { overflow:auto; overflow-x:hidden; }

.liste_articles_haut,
.liste_articles_det,
.recarga_item { overflow:auto; overflow-x:hidden; }
.recarga_cat { overflow:auto; overflow-y:hidden; }



.cadre_article {
	margin: 0 auto;
	font-size: 1.2em;
	width: 285px;
	text-align: left;
	margin: auto 5px;
	padding-bottom: 5px;
	border-bottom: 1px solid #eee;
}

.cadre_article p {
	color: #5ca64d;
}


.cadre_article p a {
	color: #333;
	font-size: 0.95em;
	text-decoration: none;
	padding-right: 25px;
	background: url('../img/basket_delete.png') top right no-repeat;
}

.cadre_article p a:hover {
	color: #6d3f6d;
}

.cadre_aucun_article {
	text-align: center;
	font-style: italic;
}

.cadre_prix_total {
	text-align: center;
	font-weight: bold;
	font-size: 1.4em;
	color: #6d3f6d;
	padding-top: 10px;
	padding-bottom: 10px;
	margin-left: 20px;
	margin-right: 20px;
	border: 1px dotted #6d3f6d;
}

.cadre_vent_total {
	font-weight: bold;
	font-size: 1.5em;
	background: #5EB0E5;
	color:#FFFFFF;
	padding-top: 10px;
	padding-bottom: 10px;
	margin-left: 20px;
	margin-right: 20px;
	border: 1px dotted #6d3f6d;
}


.bouton_venta {
	//background: #fff;
	//border: 1px solid #6d3f6d;
	width:105px;
	height:100px;
	background: none repeat scroll 0 0 #F7F7FF;
	border: 1px solid #999999;
	font-size:1.1em;
}

#principal {
	float: left;
	margin: 0 5px;
	padding: 0;
	min-width:280px;
	width:100%;
	font-size:1em;
	position:relative;
}

.titre1 {
	font-weight: bold;
	color: #0009ff; #ff9900;
	margin: 0;
	font-size: 1.4em;
}

.label1 {
	color: #333;
	font-size: 1.1em;
}

.cadre_facturation {
	padding: 2px 2px;
}
.cadre_facturation50 {
	border: 2px solid #ddd;
	margin-bottom: 5px;
	padding: 1px 1px;
	min-width:100px;
}
.cadre_facturation45 {
	border: 1px solid #ddd;
	margin-bottom: 5px;
	padding: 10px 10px;
	min-width:232px;
}

.principal p {
	padding-left: 10px;
	padding-right: 10px;
}

.lien1 {
	color: #333;
	font-size: 1.1em;
	text-decoration: underline;
}

.lien1:hover {
	color: #6d3f6d;
}

/* Formulaires */
.formulaire1 {
	padding: 0;
}

.resultats_dhtml {
	width: 400px;
	position: absolute;
}

/* --------------------- Combo lists ------------------- */
.select_design {
	width: 370px;
	overflow: auto;
}

.select_design select {
	border: 1px solid #6d3f6d;
	font: 12px verdana,arial,helvetica;
	background: #fff;
}

.select_tva select {
	width: 60px;
	border: 1px solid #6d3f6d;
	background: #fff;
}

.top_liste {
	font-style: italic;
	text-align: center;
	color: #aaa;
}

/* --------------- Champs texte ---------------- */
.texte_ref,.texte1,.texte1_off,.texte2,.texte2_off,.texte3_num,.textetc,.textetc_off,.textarea_note {
	padding-left: 2px;
	padding-right: 2px;
	font-size:20px;
}
.texte_ref,.texte3,.texte3_off,.textarea_note {
	padding-left: 2px;
	padding-right: 2px;
	font-size:20px;
	color:#ff0000;
}
/*tipo de cambio*/
.tc3_off
{
	padding-left: 2px;
	padding-right: 2px;
	font-size:18px;
	color:#000;
}
.texte_ref,.texte1,.texte2,.textetc,.textarea_note {
	background: #fff;
	border: 1px solid #6d3f6d;
}

.texte1_off,.texte2_off {
	color: #000;
	border: 1px solid #eee;
	background: #eee;
}

.texte_ref {
	width: 80px;
}

.texte1,.texte1_off {
	width: 60px;
}

.texte2,.texte2_off {
	width: 50px;
	font-size:15px;
}
.textetc,.textetc_off {
	font-size:30px;
}
.texte3,.texte3_off {
	width: 60px;
	font-size:25px;
}
.texte3_num {
	width:100px;
	font-size: 40px;
}
/* ------------------- */
.textarea_note {
	width: 300px;
	height: 80px;
	padding: 2px 2px;
}

/* -------------- Boutons --------------------- */
.bouton_ajout_article,.bouton_mode_reglement,.bouton_validation {
	border: 1px solid #999;
	background: #f7f7ff;
}

.bouton_ajout_article:hover,.bouton_mode_reglement:hover,.bouton_validation:hover
{
	background: #e7e7ff;
}

.bouton_ajout_article {
	margin-top: 10px;
	width: 100%;
	height: 40px;
}

.bouton_mode_reglement {
	width: 225px;
	height: 40px;
}
.bouton_mode_reglement2 {
	width: 80px;
	height: 40px;
}
.bouton_mode_reglement3 {
	width: 238px;
	height: 40px;
	font-size:33px;
}
.bouton_mode_reglement4 {
	width: 79px;
	height: 40px;
	font-size:20px;
}
.bouton_mode_reglement5 {
	width: 110px;
	height: 30px;
	font-size:25px;
	background-color:#388eC4;
}
.bouton_mode_reglement6 {
	width: 127px;
	height: 40px;
	font-size:20px;
}
.bouton_mode_reglement7 {
	width: 127px;
	height: 40px;
	font-size:20px;
}
.bouton_mode_reglement8 {
	width: 240px;
	height: 50px;
	font-size:35px;
}

.bouton_color_fin {
	background-color:#008000;
	color:#ffffff;
}
.bouton_color_esp {
	background-color:#008000;
	color:#ffffff;
}
.bouton_color_cb {
	background-color:#ffcc00;
	color:#ffffff;
}
.bouton_color_dif {
	background-color:#333333;
	color:#ffffff;
}
.bouton_color_gif {
	background-color:#AA3CAD;
	color:#ffffff;
}

.bouton_validation { /* 			width: 80px; */
	margin-left: 10px;
	margin-top: 20px;
	margin-bottom: 10px;
}

.formulaire2 {
	padding: 0;
	width: 100%;
}

.table_resume {
	width: 100%;
}

.table_resume tr {
	background: #eee;
}

.table_resume td {
	padding-left: 8px;
}

.resume_label,.note_label {
	width: 200px;
	font-weight: bold;
	font-size: 1.1em;
}

.note_label {
	padding-top: 20px;
}

/* ------------------- Pied de page ------------------- */
.pied {
	clear: both;
	height: 15px;
	background: url('../img/bg_pied.png') no-repeat bottom left;
}

/* ------------------- Param�tres communs (messages d'erreur, informations, etc...) ------------------- */
.msg_err1 {
	color: #c00;
}

/* Messages d'erreur */
.cadre_err1 {
	margin-right: 10px;
	margin-bottom: 10px;
	padding: 10px 10px;
	border: 1px solid #c00;
	background: #feffac;
	color: #c00;
}

/* Titre */
.err_titre {
	font-weight: bold;
	margin: 0;
	margin-bottom: 10px;
	padding: 0;
}

/* Description */
.err_desc {
	margin: 0;
	padding: 0;
}

/* Messages d'information */
.cadre_msg1 {
	margin-right: 10px;
	margin-bottom: 10px;
	padding: 10px 10px;
	border: 1px solid #070;
	background: #e8f8da;
	color: #070;
}

/* Titre */
.msg_titre {
	font-weight: bold;
	margin: 0;
	margin-bottom: 10px;
	padding: 0;
}

/* Description */
.msg_desc {
	margin: 0;
	padding: 0;
}

/* Affichage de la liste des resultats */
.dhtml_bloc {
	margin: 0;
	padding: 3px;
	font-size: 13px;
	font-family: arial, sans-serif;
	border: 1px solid #000;
	z-index: 1;
	width: 455px;
	max-height: 500px;
	overflow: auto;
	position: absolute;
	background-color: white;
}

.dhtml_defaut {
	list-style-type: none;
	display: block;
	height: 16px;
	overflow: hidden;
}

.dhtml_selection {
	background-color: #3366cc;
	color: white ! important;
}

#column-left {
	box-shadow: 0 2px 5px #666666;
	border-radius: 8px 8px 8px 8px;
	float: left;
	min-height: 525px;
	margin-bottom: 10px;
	margin-right: 10px;
	overflow: hidden;
	text-align: center;
	min-width:255px;
	width: 23%;
}

#column-right {
	box-shadow: 0 2px 5px #666666;
	border-radius: 8px 8px 8px 8px;
	float: left;
	min-height: 225px;
	margin-bottom: 10px;
	padding: 10px;
	width: 74%;
}

.fieldset-total
{
	widht : 99px;
}

/*cajas para venta*/
.cajatop {
	box-shadow: 0 2px 5px #666666;
	border-radius: 8px 8px 8px 8px;
	float: left;
	min-height: 125px;
	margin-bottom: 10px;
	margin-right: 10px;
	overflow: hidden;
	min-width:255px;
	width: 100%;
	height:125px;
	position: absolute;
}

/*
#liste_articles {
	float: left;
	min-height: 325px;
	margin-bottom: 10px;
	padding: 10px;
	width: 270px;
	position:absolute;
	left:1px;
	top:10px;
}
*/
.liste_articles_haut {
	top:223;
	position:absolute;
	width:97%;
	height:55%;
}
.liste_articles_det {
	top:223;
	position:absolute;
	width:97%;
	height:99%;
}


.cajaleft {
	float: left;
	min-height: 325px;
	margin-bottom: 10px;
	padding: 10px;
	width: 270px;
	position:absolute;
	left:1px;
	top:10px;
}
.dcajaright {
	box-shadow: 0 2px 5px #666666;
	border-radius: 8px 8px 8px 8px;
	float: left;
	height: 100%;
	margin-bottom: 10px;
	padding: 10px;
	width: 75%;
	position: absolute;
	left:300px;
	top:10px;
}


.cajatotal {
	box-shadow: 0 2px 5px #666666;
	border-radius: 8px 8px 8px 8px;
	float: left;
	min-height: 110px;
	margin-bottom: 1px;
	padding-top: 2px;
	padding-left: 2px;
	width: 260px;
	position:absolute;
	top:1px;
	left:2px;
}
.cajatotal1 {
	box-shadow: 0 2px 5px #666666;
	border-radius: 8px 8px 8px 8px;
	float: left;
	font-size: 15pt;
	margin-bottom: 1px;
	padding-top: 2px;
	padding-left: 2px;
	width: 100%;
	top:1px;
	left:2px;
}
.cajatotal2 {
	box-shadow: 0 2px 5px #666666;
	border-radius: 8px 8px 8px 8px;
	float: left;
	min-height: 64px;
	margin-bottom: 1px;
	padding-top: 2px;
	padding-left: 2px;
	width: 24%;
	top:1px;
	left:2px;
}
.resumen {
	min-height: 64px;
}

.cajatotal2left {
	box-shadow: 0 2px 5px #666666;
	border-radius: 8px 8px 8px 8px;
	float: left;
	min-height: 64px;
	margin-bottom: 1px;
	padding-top: 2px;
	padding-left: 2px;
	width: 24%;
	top:1px;
	left:2px;
	float:left;
}
.cajasidebar {
	box-shadow: 0 2px 5px #666666;
	border-radius: 8px 8px 8px 8px;
	float: left;
	margin-bottom: 1px;
	padding-top: 2px;

	width: 308px;
	bottom: 1px;
	position:absolute;
	left:1px;
	top:120px;
	width:100%
}

.cajaliste {/*no se modifico*/
	box-shadow: 0 2px 5px #666666;
	border-radius: 8px 8px 8px 8px;
	float: left;
	margin-bottom: 1px;
	padding-top: 2px;

	width: 308px;
	bottom: 170px;
	overflow-y:auto;
	position:absolute;
	left:1px;
	top:120px;
	width:100%
}
.cajalistestd {/*no se modifico*/
	box-shadow: 0 2px 5px #666666;
	border-radius: 8px 8px 8px 8px;
	float: left;
	margin-bottom: 1px;
	padding-top: 2px;

	width: 308px;
	bottom: 170px;
	overflow-y:auto;
	position:absolute;
	left:1px;
	top:1px;
	width:100%
}

.cajalistegif {/*no se modifico*/
	box-shadow: 0 2px 5px #666666;
	border-radius: 8px 8px 8px 8px;
	float: left;
	margin-bottom: 1px;
	padding-top: 2px;
	padding-left: 10px;
	width: 253px;
	height: 82%;
	position:absolute;
	left:1px;
	top:111px;
}
.cambio{
	font-size: 15px;
	color:#ff0000;
}

.boxprod {
	float: left;
	margin-bottom: 5px;
	padding: 5px;
	width: 100px;
	height: 100px;
	position:relative;
}
.cajaInline{
	display:inline-block;
	width:200px;
	margin: 5px 8px;
	color:#fff;
}
.cajaone{
	float:left;
	width:390px;
	height:40px;
	margin: 5px 8px;
	color:#000;
	font-size:20px;
}
.cajatwo{
	float:left;
	height:50px;
	margin: 5px 8px;
	color:#000;
	font-size:25px;
}
.cajathree{
	float:left;
	width:450px;
	height:40px;
	margin: 5px 8px;
	color:#000;
	font-size:20px;
}
.cajaonea{
	float:left;
	width:390px;
	height:40px;
	margin: 5px 8px;
	color:#000;
	font-size:40px;
}
.cajatwoa{
	float:left;
	height:50px;
	margin: 5px 8px;
	color:#000;
	font-size:25px;
}
.cajathreea{
	float:left;
	width:450px;
	height:40px;
	margin: 5px 8px;
	color:#000;
	font-size:40px;
}
.cajaoneb{
	float:left;
	width:290px;
	height:40px;
	margin: 5px 8px;
	color:#000;
	font-size:40px;
}
.cajatwob{
	float:left;
	height:50px;
	margin: 5px 8px;
	color:#000;
	font-size:35px;
}
.cajathreeb{
	float:left;
	width:450px;
	height:40px;
	margin: 5px 8px;
	color:#000;
	font-size:40px;
}

.clear{
	clear:both;
}
.tred{
	color:#ff0000;
}
.table_fact{
	font-size:25px;
}
.table_fact tr{
	background:#f0f0f0;
}
.theight15{
	font-size:15px;
}
.theight20{
	font-size:18px;
}
.theightv{
	font-size:18px;
}
.trmark{
	color:#FF0000;
}
.trselect{
	background-color:#bad5ff !important;
}
.terror{
	background:#FF0000;
}

select.flat1 {
	background: #fdfdfd none repeat scroll 0 0;
	border: 1px solid #c0c0c0;
	font-family: arial,tahoma,verdana,helvetica;
	margin: 0;
}

input[type="number"].mod::webkit-outer-spin-button,
input[type="number"].mod::webkit-outer-spin-button {
	-webkit-appearance: none;
	position:absolute;
	width:2em;
	border-left:1px solid #888;
	opacity: .5;
}

input.len50[type="number"] {
	width:50px;
	border-left:1px solid #888;
	opacity: .5;
}
input.len60[type="number"] {
	width:60px;
	border-left:1px solid #08088a;
	opacity: .5;
}
input.len70[type="number"] {
	width:70px;
	border-left:1px solid #888;
	opacity: .5;
}
input.len80[type="number"] {
	width:80px;
	border-left:1px solid #888;
	opacity: .5;
}
input.len90[type="number"] {
	width:90px;
	border-left:1px solid #888;
	opacity: .5;
}
input.len100[type="number"] {
	width:100px;
	border-left:1px solid #888;
	opacity: .5;
}
.boxcategorie50{
	width:50px;
	height:50px;
	position:relative;
	float:left;
	padding-left:3px;
	border: 1px solid #999999;
	font-size:29px;
 	vertical-align:middle !important;
}

div.boxcategorie50{
	width:50px;
	height:50px;
	float:left;
	position:relative;
	padding-left:3px;
	border: 1px solid #999999;
	font-size:29px;
 	vertical-align:middle !important;
}
div.boxcategorie50 div{
	width:50px;
	height:50px;
	position:absolute;
	padding-left:3px;
	border: 1px solid #ccfffff;
	font-size:29px;
 	vertical-align:middle !important;
}


.backgroundnf{
	background: #94ccef none repeat scroll 0 0;
}
.backgroundyellow{
	background: #F3FF35 none repeat scroll 0 0;
}
.resumenBox1 {

	width:99%;
	left:145px;
	float: left;
}
.resumenBox2 {
	position:relative;
	width:70%;
	left:1px;
	float: left;
}
.errorfooter {
	z-index: 1900;
	background-color: #fff82a none repeat scroll 0 0;
}

p{margin:0 10px 10px}
div#header h1{height:80px;line-height:80px;margin:0;
padding-left:10px;background: #EEE;color: #79B30B}
div#content p{line-height:1.4}
div#contentall p{line-height:1.4}
div#navigation{background:#B9CAFF}
div#extra{background:#FF8539}
div#footer{background: #333;color: #FFF}
div#footer p{margin:0;padding:5px 10px}

div#wrapper{float:left;width:100%}
div#content{position: absolute; top:215px; left:310px; bottom: 150px; width:77%; }
div#contentall{position: absolute; top:112px; left:2px; right:2px; bottom: 150px; width:99%;min-height:440px; }
div#navigation{float:left;width:200px;margin-left:-100%}
div#extra{float:left;width:200px;margin-left:-200px}
div#footer{clear:left;width:100%}

#main-header1 {
background-color:<?php echo ($conf->global->VENTA_MAIN_HEADER1?$conf->global->VENTA_MAIN_HEADER1:'27CCCF'); ?>;
color: <?php echo ($conf->global->VENTA_MAIN_HEADER1F?$conf->global->VENTA_MAIN_HEADER1F:'CCF'); ?> !important;
height: 110px;
width:100%;
left:0;
top:0;
position:fixed;
margin: 0 310px;
}
#main-header2 {
background-color:<?php echo ($conf->global->VENTA_MAIN_HEADER2?$conf->global->VENTA_MAIN_HEADER2:'27CCCF'); ?>;
color: <?php echo ($conf->global->VENTA_MAIN_HEADER2F?$conf->global->VENTA_MAIN_HEADER2F:'CCF'); ?> !important;
height: 110px;
width:100%;
left:0;
top:0;
position:fixed;
margin: 0 280px;
}
#main-header3 {
background-color:<?php echo ($conf->global->VENTA_MAIN_HEADER3?$conf->global->VENTA_MAIN_HEADER3:'27CCCF'); ?>;
color: <?php echo ($conf->global->VENTA_MAIN_HEADER3F?$conf->global->VENTA_MAIN_HEADER3F:'CCF'); ?> !important;
height: 110px;
width:100%;
left:0;
top:0;
position:fixed;
margin: 0 280px;
}
#main-header4 {
background-color:<?php echo ($conf->global->VENTA_MAIN_HEADER4?$conf->global->VENTA_MAIN_HEADER4:'27CCCF'); ?>;
color: <?php echo ($conf->global->VENTA_MAIN_HEADER4F?$conf->global->VENTA_MAIN_HEADER4F:'CCF'); ?> !important;
height: 110px;
width:100%;
left:0;
top:0;
position:fixed;
margin: 0 280px;
}

#main-headerall1 {
background-color:<?php echo ($conf->global->VENTA_MAIN_HEADER1?$conf->global->VENTA_MAIN_HEADER1:'27CCCF'); ?>;
color: <?php echo ($conf->global->VENTA_MAIN_HEADER1F?$conf->global->VENTA_MAIN_HEADER1F:'CCF'); ?> !important;
height: 110px;
width:100%;
min-width:1100px;
left:0;
top:0;
position:fixed;
}
#main-headerall2 {
background-color:<?php echo ($conf->global->VENTA_MAIN_HEADER2?$conf->global->VENTA_MAIN_HEADER2:'27CCCF'); ?>;
color: <?php echo ($conf->global->VENTA_MAIN_HEADER2F?$conf->global->VENTA_MAIN_HEADER2F:'CCF'); ?> !important;
height: 110px;
width:100%;
min-width:1100px;
left:1;
right:1;
top:0;
position:fixed;
}
#main-headerall3 {
background-color:<?php echo ($conf->global->VENTA_MAIN_HEADER3?$conf->global->VENTA_MAIN_HEADER3:'27CCCF'); ?>;
color: <?php echo ($conf->global->VENTA_MAIN_HEADER3F?$conf->global->VENTA_MAIN_HEADER3F:'CCF'); ?> !important;
height: 110px;
width:100%;
min-width:1100px;
left:1;
right:1;
top:0;
position:fixed;
}
#main-headerall4 {
background-color:<?php echo ($conf->global->VENTA_MAIN_HEADER4?$conf->global->VENTA_MAIN_HEADER4:'27CCCF'); ?>;
color: <?php echo ($conf->global->VENTA_MAIN_HEADER4F?$conf->global->VENTA_MAIN_HEADER4F:'CCF'); ?> !important;
height: 110px;
width:100%;
min-width:1100px;
left:1;
right:1;
top:0;
position:fixed;
}
#main-headercommande {
background-color:<?php echo ($conf->global->VENTA_MAIN_HEADER_COMMANDE?$conf->global->VENTA_MAIN_HEADER_COMMANDE:'27CCCF'); ?>;
color: <?php echo ($conf->global->VENTA_MAIN_HEADERF?$conf->global->VENTA_MAIN_HEADERF:'CCF'); ?> !important;
height: 110px;
width:100%;
min-width:1100px;
left:1;
right:1;
top:0;
position:fixed;
}
#main-headerdeplacement {
background-color:<?php echo ($conf->global->VENTA_MAIN_HEADER_DEPLACEMENT?$conf->global->VENTA_MAIN_HEADER_DEPLACEMENT:'27CCCF'); ?>;
color: <?php echo ($conf->global->VENTA_MAIN_HEADER_DEPLACEMENTF?$conf->global->VENTA_MAIN_HEADER_DEPLACEMENTF:'CCF'); ?> !important;
height: 110px;
width:100%;
min-width:1100px;
left:1;
right:1;
top:0;
position:fixed;
}
#main-headertransfer {
background-color:<?php echo ($conf->global->VENTA_MAIN_HEADER_TRANSFER?$conf->global->VENTA_MAIN_HEADER_TRANSFER:'27CCCF'); ?>;
color: <?php echo ($conf->global->VENTA_MAIN_HEADER_TRANSFERF?$conf->global->VENTA_MAIN_HEADER_TRANSFERF:'CCF'); ?> !important;
height: 110px;
width:100%;
min-width:1100px;
left:1;
right:1;
top:0;
position:fixed;
}
#main-headerportion {
background-color:<?php echo ($conf->global->VENTA_MAIN_HEADER_PORTION?$conf->global->VENTA_MAIN_HEADER_PORTION:'27CCCF'); ?>;
color: <?php echo ($conf->global->VENTA_MAIN_HEADER_PORTIONF?$conf->global->VENTA_MAIN_HEADER_PORTIONF:'CCF'); ?> !important;
!important;
height: 110px;
width:100%;
min-width:1100px;
left:1;
right:1;
top:0;
position:fixed;
}
#main-headerresumen {
background-color:<?php echo ($conf->global->VENTA_MAIN_HEADER_RESUMEN?$conf->global->VENTA_MAIN_HEADER_RESUMEN:'27CCCF'); ?>;
color: <?php echo ($conf->global->VENTA_MAIN_HEADER_RESUMENF?$conf->global->VENTA_MAIN_HEADER_RESUMENF:'CCF'); ?> !important;
!important;
height: 110px;
width:100%;
min-width:1100px;
left:1;
right:1;
top:0;
position:fixed;
}
#main-headerblack {
background-color:#000000;
color: #000 !important;
height: 110px;
width:100%;
min-width:1100px;
left:1;
right:1;
top:0;
position:fixed;
}

#main-headercat {
background:#fff;
color: black;
height: 80px;
width:100%;
left:0;
top:100 !important;
position:absolute;
margin: 0 280px;
}

#main-header a {
color: white;
}

#main-footer {
background-color:#47ABFD;
position: fixed;
bottom: 0;
left:310px;
width: 100%;
height: 150px;
color: white;
z-index:100;
}
.main-footersidebar {
background-color:#47ABFD;
position: absolute;
bottom: 0;
left:0px;
width: 100%;
height: 167px;
color: white;
z-index:101;
}

#main-footer1 {
background-color:#<?php echo ($conf->global->VENTA_MAIN_HEADER1?$conf->global->VENTA_MAIN_HEADER1:'27CCCF'); ?>;
position: fixed;
bottom: 0;
width: 100%;
height: 150px;
color: white;
}
#main-footer2 {
background-color:#<?php echo ($conf->global->VENTA_MAIN_HEADER2?$conf->global->VENTA_MAIN_HEADER2:'27CCCF'); ?>;
position: fixed;
bottom: 0;
width: 100%;
height: 150px;
color: white;
}
#main-footer3 {
background-color:#<?php echo ($conf->global->VENTA_MAIN_HEADER3?$conf->global->VENTA_MAIN_HEADER3:'27CCCF'); ?>;
position: fixed;
bottom: 0;
width: 100%;
height: 150px;
color: white;
}
#main-footer4 {
background-color:#<?php echo ($conf->global->VENTA_MAIN_HEADER4?$conf->global->VENTA_MAIN_HEADER4:'27CCCF'); ?>;
position: fixed;
bottom: 0;
width: 100%;
height: 150px;
color: white;
}

.sidenav {
height: 100%; /* Full-height: remove this if you want "auto" height */
width: 309px; /* Set the width of the sidebar */
position: fixed; /* Fixed Sidebar (stay in place on scroll) */
z-index: 1; /* Stay on top */
top: 0; /* Stay at the top */
left: 0;

overflow-x: hidden; /* Disable horizontal scroll */
padding-top: 20px;
}

/* The navigation menu links */
.sidenav a {
padding: 6px 8px 6px 16px;
text-decoration: none;
font-size: 20px;
color: #0e2326 ;
display: block;
}

/* When you mouse over the navigation links, change their color */
.sidenav a:hover {
color: #ff0000;
}

/* When you mouse over the navigation links, change their color */
.sidenav input.inputdesc {
background: #bceaff;
}

span.sizetotal{
	font-size:20px;
}

.selectAltura {
  display:block;
  height:50px;
  width:200px;
}