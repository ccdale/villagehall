<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Tuesday 21 February 2017, 06:02:54
 * Last Modified: Saturday 25 February 2017, 00:43:08
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

$footcontent="Copyright © 2016-" . date("Y") . " Chris Allison";
$tag=new Tag("div",$footcontent,array("class"=>"centre"));
$footcontent=$tag->makeTag();
$tmp=new Tag("div",$footcontent,array("id"=>"footer"));
$bfooter=$tmp->makeTag();
/*
 * close the container div
 */
$bfooter.="\n</div>\n";
?>
