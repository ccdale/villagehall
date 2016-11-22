<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker:
 *
 * index.php
 *
 * Started: Saturday 19 November 2016, 15:35:53
 * Last Modified: Tuesday 22 November 2016, 10:37:44
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

$publicpath=getcwd();
$ppatha=explode("/",$publicpath);
$cn=count($ppatha);
$pvpatha=array();
if($cn>=1){
    for($i=0;$i<$cn-1;$i++){
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

include $pvpath . "/config.php";
include $apppath . "/" . $appname . ".php";
?>
