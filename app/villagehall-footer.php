<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Tuesday 21 February 2017, 06:02:54
 * Last Modified: Sunday  3 September 2017, 10:01:05
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
$linksarray=array(
  array("a"=>21,"text"=>"About"),
  array("a"=>22,"text"=>"Contact"),
  array("a"=>23,"text"=>"Pricing"),
  array("a"=>24,"text"=>"Terms and Conditions"),
  array("a"=>25,"text"=>"Privacy")
);

$td=new Tag("td","<hr />",array("colspan"=>count($linksarray)*2));
$row=$td->makeTag();
$hline=new Tag("tr",$row);
$rows=$hline->makeTag();
$row="";
foreach($linksarray as $la){
  $alink=new ALink($la,$la["text"]);
  $td=new Tag("td",$alink->makeLink());
  $row.=$td->makeTag();
  $td=new Tag("td","&nbsp;");
  $row.=$td->makeTag();
}

$tr=new Tag("tr",$row);
$rows.=$tr->makeTag();
$rows.=$hline->makeTag();
$table=new Tag("table",$rows,array("border"=>0));
$tag=new Tag("div",$table->makeTag());
$links=$tag->makeTag();
$nocookies="This website does not set nor read cookies.\n";
$nctag=new Tag("p",$nocookies);
$footcontent="Copyright © 2016-" . date("Y") . " Chris Allison";
$fctag=new Tag("p",$footcontent);
$tag=new Tag("div",$nctag->makeTag() . $fctag->makeTag());
$footcontent=$tag->makeTag();
$tmp=new Tag("footer",$links . $footcontent,array("class"=>"container bg-4 text-center"));
$bfooter.=$tmp->makeTag();
?>
