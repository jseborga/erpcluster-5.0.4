<?php
/* Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2007 Regis Houssin        <regis.houssin@capnetworks.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *  \file		htdocs/core/lib/images.lib.php
 *  \brief		Set of function for manipulating images
 */

// Define size of logo small and mini
$maxwidthsmall=270;$maxheightsmall=150;
$maxwidthmini=128;$maxheightmini=72;
$quality = 80;
function doc_format_supported($file)
{
    // Case filename is not a format image
  //    if (! preg_match('/(\.doc|\.docx|\.pdf)$/i',$file,$reg)) return -1;
      if (! preg_match('/(\.pdf)$/i',$file,$reg)) return -1;
    // // Case filename is a format image but not supported by this PHP
    // $imgfonction='';
    // if (strtolower($reg[1]) == '.gif')  $imgfonction = 'imagecreatefromgif';
    // if (strtolower($reg[1]) == '.png')  $imgfonction = 'imagecreatefrompng';
    // if (strtolower($reg[1]) == '.jpg')  $imgfonction = 'imagecreatefromjpeg';
    // if (strtolower($reg[1]) == '.jpeg') $imgfonction = 'imagecreatefromjpeg';
    // if (strtolower($reg[1]) == '.bmp')  $imgfonction = 'imagecreatefromwbmp';
    // if ($imgfonction)
    // {
    //     if (! function_exists($imgfonction))
    //     {
    //         // Fonctions de conversion non presente dans ce PHP
    //         return 0;
    //     }
    // }

    // Filename is a format image and supported by this PHP
    return 1;
}
function doc_format_supported_activity($file)
{
    // Case filename is not a format image
  if (! preg_match('/(\.xlsx|\.xls|\.doc|\.docx|\.pdf)$/i',$file,$reg)) return -1;
  //if (! preg_match('/(\.pdf)$/i',$file,$reg)) return -1;
    // // Case filename is a format image but not supported by this PHP
    // $imgfonction='';
    // if (strtolower($reg[1]) == '.gif')  $imgfonction = 'imagecreatefromgif';
    // if (strtolower($reg[1]) == '.png')  $imgfonction = 'imagecreatefrompng';
    // if (strtolower($reg[1]) == '.jpg')  $imgfonction = 'imagecreatefromjpeg';
    // if (strtolower($reg[1]) == '.jpeg') $imgfonction = 'imagecreatefromjpeg';
    // if (strtolower($reg[1]) == '.bmp')  $imgfonction = 'imagecreatefromwbmp';
    // if ($imgfonction)
    // {
    //     if (! function_exists($imgfonction))
    //     {
    //         // Fonctions de conversion non presente dans ce PHP
    //         return 0;
    //     }
    // }

    // Filename is a format image and supported by this PHP
    return 1;
}
