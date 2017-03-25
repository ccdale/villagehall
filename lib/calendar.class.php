<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Saturday 25 March 2017, 12:02:15
 * Last Modified: Saturday 25 March 2017, 20:35:15
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

require_once "base.class.php";
require_once "bookings.class.php";
require_once "HTML/link.class.php";
require_once "HTML/tag.class.php";

class Calendar extends Base
{
  private $db=false;
  private $bookings=false;

  public function __construct($logg=false,$db=false)/*{{{*/
  {
    parent::__construct($logg);
    $this->db=$db;
    $this->bookings=new Booking($logg,$db);
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    $this->bookings=null;
    parent::__destruct();
  }/*}}}*/
  public function calendarDiv($monthoffset=0,$year=0,$month=0,$day=0)/*{{{*/
  {
    $row="";
    $thismonth=date("n");
    $thisyear=$year=date("Y");
    $thisday=date("j");
    if($year>0 && $month>0 && $day>0){
      $xday=$day;
      $monthoffset=(($thisyear-$year)*12)+$month-3;
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
      $tag=new Tag("div",$this->singleCalendar($month,$year,$xday,$showyear),array("class"=>"col-sm-4"));
      $row.=$tag->makeTag();
      $month++;
      if($month>12){
        $month=1;
        $year++;
      }
    }
    $cdiv=new Tag("div",$row,array("class"=>"row","name"=>"calendar"));
    $cal=$cdiv->makeTag();
    $buttons=$this->nextMonthButton($monthoffset);
    $key=$this->tableKey();
    return $buttons . $cal . $key;
  }/*}}}*/
  public function singleCalendar($month, $year,$day=0,$showyear=false)/*{{{*/
  {
    $months=array("padding","January","February","March","April","May","June","July","August","September","October","November","December");
    $op="";
    $op.=$this->weekDays();
    $op.=$this->calDays($month,$year,$day);
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
  private function nextMonthButton($monthoffset)/*{{{*/
  {
    $chevl=new Tag("span","",array("class"=>"glyphicon glyphicon-chevron-left"));
    if($monthoffset==0){
      $tag=new ALink("",$chevl->makeTag(),"","btn btn-default disabled");
    }else{
      $tag=new ALink(array("monthoffset"=>$monthoffset-3),$chevl->makeTag(),"","btn btn-default");
    }
    $leftb=$tag->makeLink();
    $tag=new ALink(array("monthoffset"=>0),"Today","","btn bth-primary");
    $middleb=$tag->makeLink();
    $chevr=new Tag("span","",array("class"=>"glyphicon glyphicon-chevron-right"));
    $tag=new ALink(array("monthoffset"=>$monthoffset+3),$chevr->makeTag(),"","btn btn-default");
    $rightb=$tag->makeLink();
    $buttons=$leftb . $middleb . $rightb;
    $tag=new Tag("div",$buttons,array("class"=>"col-sm-12 text-center"));
    return $tag->makeTag();
  }/*}}}*/
  private function tableKey()/*{{{*/
  {
    $today=date("j");
    if($today<10){
      $today="&nbsp;$today";
    }
    $line="";
    $stuff=array(
      array("class"=>"tdkey calnodeposit","txt"=>"Booked: Deposit not yet paid."),
      array("class"=>"tdkey caldeposit","txt"=>"Booked: Deposit paid."),
      array("class"=>"tdkey calpaid","txt"=>"Booked: Fully paid.")
    );
    foreach($stuff as $val){
      $tag=new Tag("td",$today,array("class"=>$val["class"]));
      $cells=$tag->makeTag();
      $tag=new Tag("td",$val["txt"],array("class"=>"tdkey"));
      $cells.=$tag->makeTag();
      $tag=new Tag("tr",$cells);
      $row=$tag->makeTag();
      $tag=new Tag("tbody",$row);
      $tbody=$tag->makeTag();
      $tag=new Tag("table",$tbody);
      $table=$tag->makeTag();
      $tag=new Tag("div",$table,array("class"=>"col-sm-4"));
      $line.=$tag->makeTag();
    }
    return $line;
  }/*}}}*/
  private function calDays($month,$year,$day)/*{{{*/
  {
    $barr=$this->transformMonthBookingsArray($month,$year);
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
              $class=isset($barr[$days])?$barr[$days]["class"]:"";
              $link=new ALink(array("year"=>$year,"month"=>$month,"day"=>$days),$xdays,"","caldaylink");
              $tag=new Tag("td",$link->makeLink(),array("class"=>$class));
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
  private function weekDays()/*{{{*/
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
  private function transformMonthBookingsArray($month,$year)/*{{{*/
  {
    $barr=array();
    $numbookings=$this->bookings->getBookingsForMonth($month,$year);
    if($numbookings>0){
      $blist=$this->bookings->getBookingList();
      foreach($blist as $b){
        $day=date("j",$b["date"]);
        /* statuses:
         *   not yet paid deposit: 3 (overrides all others for display)
         *   paid deposit: 2 (overrides 1 for display)
         *   paid in full: 1
         *   not booked: 0
         */
        $status=$b["status"];
        $cstatus=isset($barr[$day])?$barr[$day]["status"]:0;
        if($cstatus>$status){
          $status=$cstatus;
        }
        switch($status){
        case 3:
          $class="calnodeposit";
          break;
        case 2:
          $class="caldeposit";
          break;
        case 1:
          $class="calpaid";
          break;
        default:
          $class="";
        }
        $barr[$day]["status"]=$status;
        $barr[$day]["class"]=$class;
      }
    }
    return $barr;
  }/*}}}*/
  private function transformDayBookingsArray($day,$month,$year)/*{{{*/
  {
    $barr=array();
    $numbookings=$this->bookings->getBookingsForDay($day,$month,$year);
    if($numbookings>0){
      $blist=$this->bookings->getBookingList();
      foreach($blist as $b){
        $starthour=date("G",$b["date"]);
        $startminute=date("i",$b["date"]);
      }
    }
    return $barr;
  }/*}}}*/
}
