<?php
$html =
'<html>
<head>
  <title>Print ticket</title>
  <meta name="tipo_contenido"  content="text/html;" http-equiv="content-type" charset="utf-8">
  <style type="text/css">
   body {
     font-size: 3em;
     position: relative;
     font-family: sans-serif;
   }

   .master { /* 		position: relative; */
     width:350px;
   }

   .entete { /* 		position: relative; */

   }

   .address { /* 			float: left; */
     font-size: 14px;
   }
   .text12 { /* 			float: left; */
     font-size: 12px;
   }
   .text13 { /* 			float: left; */
     font-size: 15px;
   }
   .foother { /* 			float: left; */
     font-size: 12px;
   }
   .textwhite{
     color:#FFFFFF;
   }
   .center
   {
     margin-left:2px;
     margin-right:8px;
     text-align:center;
   }
   .date_heure {
     position: absolute;
     top: 0;
     right: 0;
     font-size: 16px;
   }

   .date_local {
     position: relative;
     font-size: 15px;
   }

   .infos {
     position: relative;
   }

   .liste_articles {
     width: 330px;
     border-bottom: 1px solid #000;
     text-align: center;
     font-size: 14px;
   }

   .liste_articles tr.titres th {
     border-bottom: 1px solid #000;
     font-size: 14px;
   }

   .liste_articles tr.titrestd td {
     font-size: 14px;
   }

   .liste_articles td.total {
     text-align: right;
   }

   .liste_articles tr.total td {
     border-top: 1px solid #000;
     font-size: 14px;
   }

   .totaux {
     font-size: 11px;
     margin-top: 14px;
     width: 30%;
     float: right;
     text-align: right;
   }
   .totauxleft {
     font-size: 13px;
     margin-top: 4px;
     width: 320px;
     float: left;
     text-align: left;
   }

   .lien {
     position: absolute;
     top: 0;
     left: 0;
     display: none;
   }

   @media print {
     .lien {
      display: none;
    }
  }
</style>

</head>
<body onload="setTimeout('."'".'window.close()'."'".',95000)">';
  ?>