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
function doc_format_supported($file,$mode=4)
{
    // Case filename is not a format image
  $res = 1;
  switch ($mode)
    {
    case 1://todos
      if (! preg_match('/(\.pdf|\.png|\.jpeg|\.jpg|\.bmp|\.gif|\.doc|\.docx|\.xls|\.xlsx)$/i',$file,$reg))
	$res = -1;
      break;      
    case 2://doc y xls
      if (! preg_match('/(\.pdf|\.png|\.jpeg|\.jpg|\.bmp|\.gif|\.doc|\.docx)$/i',$file,$reg))
	$res = -1;
      break;
    case 3://solo imagenes
      if (! preg_match('/(\.pdf|\.png|\.jpeg|\.jpg|\.bmp|\.gif)$/i',$file,$reg))
	$res = -1;
      break;
    case 4://solo pdf
      if (! preg_match('/(\.pdf)$/i',$file,$reg))
	$res = -1;
      break;
    case 5://todo
      $res = 1;
      break;
    default:
      $res = -1;
      break;
    }
  return $res;
}
