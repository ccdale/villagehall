<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker:
 *
 * index.php
 *
 * Started: Saturday 19 November 2016, 15:35:53
 * Last Modified: Sunday 18 December 2016, 11:24:59
 *
 * Copyright (c) 2016 Chris Allison chris.allison@hotmail.com
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

/*
 * layout of this application
 * (adjust the vars below accordingly if this changes)
 *
 * root/
 *     |
 *     - villagehall-config.php
 *     - app
 *     - lib
 *     - www (or public dir)
 */

date_default_timezone_set("Europe/London");

/*
 * strip this many directories off of the current
 * path, to find the root
 */
$stripcn=1;

/*
 * work out where we are in the file system
 */
$publicpath=getcwd();
$ppatha=explode("/",$publicpath);
$cn=count($ppatha);
$pvpatha=array();
if($cn>=$stripcn){
    for($i=0;$i<$cn-$stripcn;$i++){
        $pvpatha[$i]=$ppatha[$i];
    }
}
$vn=count($pvpatha);
$tmpa=$pvpatha;
$tmpa[$vn]="lib";
$libpath=implode("/",$tmpa);
$tmpa=$pvpatha;
$tmpa[$vn]="app";
$apppath=implode("/",$tmpa);
$pvpath=implode("/",$pvpatha);

unset($ppatha);
unset($cn);
unset($pvpatha);
unset($i);
unset($vn);
unset($tmpa);

set_include_path($libpath . PATH_SEPARATOR . get_include_path());

include $pvpath . "/villagehall-config.php";
include $apppath . "/" . $appname . ".php";
?>
