
/* Copyright (C) 2007-2008 Jeremie Ollivier <jeremie.o@laposte.net>
 * 2013-2013 Ramiro Queso <ramiro@ubuntu-bo.com>
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

function app2()
{
    document.frm.c_id3.disabled=true;
    if(document.getElementById("c_id2").checked==true)
    {
	document.frm.c_id3.disabled=false;
    }
    if(document.getElementById("c_id2").checked==false)
    {
	document.frm.c_id3.enabled=false;
    }
}
function app3()
{
    document.frm.c_id2.disabled=true;
    if(document.getElementById("c_id3").checked==true)
    {
	document.frm.c_id2.disabled=false;
    }
    if(document.getElementById("c_id3")..checked==false)
    {
	document.frm.c_id2.enabled=false;
    }
}
