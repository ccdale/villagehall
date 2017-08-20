<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Tuesday 21 February 2017, 06:00:53
 * Last Modified: Sunday 20 August 2017, 08:36:44
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
$bootstrap=<<<EOBS

<!-- Bootstrap -->
<meta charset='utf-8' >
<meta name='viewport' content='width=device-width, initial-scale=1' >
<!-- Latest compiled and minified CSS -->

<!-- commented out
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css">
-->

<!-- colour and footer overrides -->
<!--
This is not affective, so is commented out
<link rel="stylesheet" type="text/css" media="screen" href="css/override_default.css">
-->

<!-- jQuery library -->

<!-- commented out
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
-->

<!-- Latest compiled JavaScript -->
<!-- commented out
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
-->
<!-- /Bootstrap -->

EOBS;
$tag=new Tag("title",$pagetitle);
$title=$tag->makeTag();
$cssarray=array("rel"=>"stylesheet","type"=>"text/css","href"=>"css/villagehall.css","media"=>"screen");
$tag=new Tag("link","",$cssarray,true,false,true);
$css=$tag->makeTag();
/* $tag=new Tag("head",$bootstrap . $title . $css); */
$tag=new Tag("head",$title . $css);
$head=$tag->makeTag();
$link=new ALink("",$pagetitle);
$tag=new Tag("h2",$link->makeLink());
$pagetitle=$tag->makeTag();
$tag=new Tag("div",$pagetitle,array("class"=>"center"));
$pagetitle=$tag->makeTag();
$tag=new Tag("div",$pagetitle,array("id"=>"header"));
$bheader="\n<div class='container'>\n" . $tag->makeTag();
?>
