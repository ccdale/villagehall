<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * villagehall.php
 *
 * Started: Sunday 20 November 2016, 08:04:47
 * Last Modified: Sunday 26 March 2017, 05:25:20
 *
 * Copyright (c) 2016 Chris Allison chris.charles.allison+vh@gmail.com
 *
 * This file is part of villagehall.
 * 
 * villagehall is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * villagehall is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with villagehall.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once "www.php";
require_once "calendar.class.php";

$mo=getDefaultInt("monthoffset",0);
$day=getDefaultInt("day",0);
$month=getDefaultInt("month",0);
$year=getDefaultInt("year",0);

$hall=new Hall($logg,$db,$hallname);

$cal=new Calendar($logg,$db,$hall->getId());
$content=$cal->calendarDiv($mo,$year,$month,$day);

$headfn=$apppath . DIRECTORY_SEPARATOR . $appname . "-header.php";
$footfn=$apppath . DIRECTORY_SEPARATOR . $appname . "-footer.php";

$pagetitle=$hallname . " " . $displayname;

include $headfn;
include $footfn;

$tag=new Tag("div",$content,array("id"=>"body"));
$bodytag=$tag->makeTag();
$tag=new Tag("body",$bheader . $bodytag . $bfooter);
$body=$tag->makeTag();
$tag=new Tag("html",$head . $body,array("lang"=>"en"));
$html=" <!DOCTYPE html>\n" . $tag->makeTag();
echo $html;
?>
