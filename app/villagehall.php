<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * villagehall.php
 *
 * Started: Sunday 20 November 2016, 08:04:47
 * Last Modified: Monday 20 March 2017, 06:56:05
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

function importLib($libfn,$desc,$log)/*{{{*/
{
    $log->debug("Importing $desc from $libfn");
    require_once $libfn;
}/*}}}*/
function calendarDiv($monthoffset=0)/*{{{*/
{
  $thismonth=date("n");
  $month=$thismonth+$monthoffset;
  $thisyear=$year=date("Y");
  $day=date("j");
  $xday=$thismonth==$month && $thisyear==$year?$day:0;
  $tag=new Tag("div",singleCalendar($month,$year,$xday),array("class"=>"col-sm-4"));
  $row=$tag->makeTag();
  $month++;
  if($month>12){
    $month=1;
    $year++;
  }
  $xday=$thismonth==$month && $thisyear==$year?$day:0;
  $tag=new Tag("div",singleCalendar($month,$year,$xday),array("class"=>"col-sm-4"));
  $row.=$tag->makeTag();
  $month++;
  if($month>12){
    $month=1;
    $year++;
  }
  $xday=$thismonth==$month && $thisyear==$year?$day:0;
  $tag=new Tag("div",singleCalendar($month,$year,$xday),array("class"=>"col-sm-4"));
  $row.=$tag->makeTag();
  $cdiv=new Tag("div",$row,array("class"=>"row","name"=>"calendar"));
  return $cdiv->makeTag();
}/*}}}*/
function singleCalendar($month, $year,$day=0)/*{{{*/
{
  $months=array("padding","January","February","March","April","May","June","July","August","September","October","November","December");
  $op="";
  $op.=weekDays();
  $op.=calDays($month,$year,$day);
  $tag=new Tag("table",$op,array("class"=>"table"));
  $op=$tag->makeTag();
  $tag=new Tag("div",$op,array("class"=>"panel-body"));
  $op=$tag->makeTag();
  $tag=new Tag("div",$months[$month],array("class"=>"panel-heading"));
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
            $tag=new Tag("td",$xdays,array("class"=>"info"));
          }else{
            $tag=new Tag("td",$xdays);
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

importLib("www.php","GP funcs",$logg);
importLib("HTML/form.class.php","Form Class",$logg);
importLib("HTML/tag.class.php","TAG class",$logg);
importLib("HTML/link.class.php","Link class",$logg);
importLib("HTML/input_field.class.php","Inputfield class",$logg);

// $un=new User($logg,$db,"chris.allison@hotmail.com","somepassword");

$mo=GP("monthoffset");
$content=calendarDiv($mo);

$headfn=$apppath . DIRECTORY_SEPARATOR . $appname . "-header.php";
$footfn=$apppath . DIRECTORY_SEPARATOR . $appname . "-footer.php";

$pagetitle="Lidlington Village Hall";

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
