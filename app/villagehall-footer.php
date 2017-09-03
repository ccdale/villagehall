<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Tuesday 21 February 2017, 06:02:54
 * Last Modified: Sunday  3 September 2017, 09:16:48
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

/*
 * close the container div
 */
$bfooter="\n</div>\n";
$about=new ALink(array("a"=>21),"About");
$contact=new ALink(array("a"=>22),"Contact");
/* $admin=new Alink(array("z"=>"admin","a"=>99),"Admin"); */
$td=new Tag("td",$about->makeLink());
$row=$td->makeTag();
$td=new Tag("td","&nbsp;");
$row.=$td->makeTag();
$td=new Tag("td",$contact->makeLink());
$row.=$td->makeTag();
$td=new Tag("td","&nbsp;");
$row.=$td->makeTag();
/* $td=new Tag("td",$admin->makeLink()); */
$td=new Tag("td","&nbsp;");
$row.=$td->makeTag();
$tr=new Tag("tr",$row);
$row=$tr->makeTag();
$table=new Tag("table",$row,array("border"=>0));
$tag=new Tag("div",$table->makeTag());
$links=$tag->makeTag();
$footcontent="Copyright © 2016-" . date("Y") . " Chris Allison";
$tag=new Tag("div",$footcontent);
$footcontent=$tag->makeTag();
$tmp=new Tag("footer",$links . $footcontent,array("class"=>"container bg-4 text-center"));
$bfooter.=$tmp->makeTag();
?>
