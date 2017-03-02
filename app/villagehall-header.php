<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Tuesday 21 February 2017, 06:00:53
 * Last Modified: Saturday 25 February 2017, 01:15:06
 *
 * Copyright © 2017 Chris Allison <chris.charles.allison+vh@gmail.com>
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
$tag=new Tag("title",$pagetitle);
$title=$tag->makeTag();
$cssarray=array("rel"=>"stylesheet","type"=>"text/css","href"=>"css/screen.css","media"=>"screen");
$tag=new Tag("link","",$cssarray,true,false,true);
$css=$tag->makeTag();
$tag=new Tag("head",$title . $css);
$head=$tag->makeTag();
$tag=new Tag("h2",$pagetitle);
$pagetitle=$tag->makeTag();
$tag=new Tag("div",$pagetitle,array("class"=>"centre"));
$pagetitle=$tag->makeTag();
$tag=new Tag("div",$pagetitle,array("id"=>"header"));
$bheader="<div id='container'>\n" . $tag->makeTag();
?>
