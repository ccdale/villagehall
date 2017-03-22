<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * calendar.php
 *
 * Started: Tuesday 21 March 2017, 08:25:47
 * Last Modified: Wednesday 22 March 2017, 07:29:04
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
function singleCalendar($month, $year,$day=0,$showyear=false)/*{{{*/
{
  $months=array("padding","January","February","March","April","May","June","July","August","September","October","November","December");
  $op="";
  $op.=weekDays();
  $op.=calDays($month,$year,$day);
  $tag=new Tag("table",$op,array("class"=>"table"));
  $op=$tag->makeTag();
  $tag=new Tag("div",$op,array("class"=>"panel-body"));
  $op=$tag->makeTag();
  $mstr=$showyear?$months[$month] . " " . $year:$months[$month];
  $tag=new Tag("div",$mstr,array("class"=>"panel-heading"));
  $tmp=$tag->makeTag();
  $tag=new Tag("div",$tmp . $op,array("class"=>"panel panel-primary"));
  return $tag->makeTag();
}/*}}}*/
function calDays($month,$year,$day)/*{{{*/
{
  $op="";
  $rows=0;
  $d=cal_days_in_month(CAL_GREGORIAN,$month,$year);
  $jd=gregoriantojd($month,1,$year);
  $dow=jddayofweek($jd,0);
  $days=1;
  $firstrow=true;
  while($days<=$d){
    $sop="";
    $idx=0;
    while($idx<7){
      if($firstrow && $idx<$dow){
        $tag=new Tag("td","&nbsp;");
        $sop.=$tag->makeTag();
      }else{
        if($days<=$d){
          $xdays=$days<10?"&nbsp;$days":"$days";
          if($days==$day){
            $tag=new Tag("td",$xdays,array("class"=>"todaymark"));
          }else{
            $link=new ALink(array("year"=>$year,"month"=>$month,"day"=>$days),$xdays,"","caldaylink");
            $tag=new Tag("td",$link->makeLink());
          }
          $sop.=$tag->makeTag();
          $days+=1;
        }else{
          $tag=new Tag("td","&nbsp;");
          $sop.=$tag->makeTag();
        }
      }
      $idx+=1;
    }
    $firstrow=false;
    $tag=new Tag("tr",$sop);
    $op.=$tag->makeTag();
    $rows++;
  }
  if($rows<6){
    $tmp="";
    for($x=0;$x<7;$x++){
      $tag=new Tag("td","&nbsp;");
      $tmp.=$tag->makeTag();
    }
    $tag=new Tag("tr",$tmp);
    $op.=$tag->makeTag();
  }
  $tag=new Tag("tbody",$op);
  return $tag->makeTag();
}/*}}}*/
function weekDays()/*{{{*/
{
  $op="";
  $days=array("Su","Mo","Tu","We","Th","Fr","Sa");
  foreach($days as $day){
    $tag=new Tag("th",$day);
    $op.=$tag->makeTag();
  }
  $tag=new Tag("tr",$op);
  $op=$tag->makeTag();
  $tag=new Tag("thead",$op);
  return $tag->makeTag();
}/*}}}*/
?>
