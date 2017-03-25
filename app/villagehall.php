<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * villagehall.php
 *
 * Started: Sunday 20 November 2016, 08:04:47
 * Last Modified: Saturday 25 March 2017, 12:12:36
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
function calendarDiv($monthoffset=0,$year=0,$month=0,$day=0)/*{{{*/
{
  $row="";
  $thismonth=date("n");
  $thisyear=$year=date("Y");
  $thisday=date("j");
  if($year>0 && $month>0 && $day>0){
    $xday=$day;
    $monthoffset=(($thisyear-$year)*12)+$month;
  }else{
    $month=$thismonth+$monthoffset;
    if($month>12){
      $month=$month-12;
      $year++;
    }
    $day=$thisday;
    $xday=$thismonth==$month && $thisyear==$year?$day:0;
  }
  for($x=0;$x<3;$x++){
    if($x>0){
      $xday=0;
    }
    $showyear=$year>$thisyear?true:false;
    $tag=new Tag("div",singleCalendar($month,$year,$xday,$showyear),array("class"=>"col-sm-4"));
    $row.=$tag->makeTag();
    $month++;
    if($month>12){
      $month=1;
      $year++;
    }
  }
  $cdiv=new Tag("div",$row,array("class"=>"row","name"=>"calendar"));
  $cal=$cdiv->makeTag();
  $buttons=nextMonthButton($monthoffset);
  return $cal . $buttons;
}/*}}}*/
function nextMonthButton($monthoffset)/*{{{*/
{
  if($monthoffset==0){
    $tag=new ALink("","<","","btn btn-default disabled");
  }else{
    $tag=new ALink(array("monthoffset"=>$monthoffset-3),"<","","btn btn-default");
  }
  $leftb=$tag->makeLink();
  $tag=new ALink(array("monthoffset"=>0),"Today","","btn bth-primary");
  $middleb=$tag->makeLink();
  $tag=new ALink(array("monthoffset"=>$monthoffset+3),">","","btn btn-default");
  $rightb=$tag->makeLink();
  $buttons=$leftb . $middleb . $rightb;
  $tag=new Tag("div",$buttons,array("class"=>"col-sm-12 text-center"));
  return $tag->makeTag();
}/*}}}*/

importLib("www.php","GP funcs",$logg);
importLib("calendar.class.php","Calendar class",$logg);
importLib("HTML/form.class.php","Form Class",$logg);
importLib("HTML/tag.class.php","TAG class",$logg);
importLib("HTML/link.class.php","Link class",$logg);
importLib("HTML/input_field.class.php","Inputfield class",$logg);

$mo=getDefaultInt("monthoffset",0);
$day=getDefaultInt("day",0);
$month=getDefaultInt("month",0);
$year=getDefaultInt("year",0);
$cal=new Calendar($logg,$db);
$content=$cal->calendarDiv($mo,$year,$month,$day);

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
