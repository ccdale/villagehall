<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Saturday 25 March 2017, 12:02:15
 * Last Modified: Sunday 23 July 2017, 10:58:54
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
require_once "room.class.php";
require_once "HTML/link.class.php";
require_once "HTML/tag.class.php";

class Calendar extends Base
{
  private $db=false;
  private $bookings=false;
  private $hall=false;
  private $rooms=false;
  private $numrooms=0;
  private $session=false;

  public function __construct($logg=false,$db=false,$hall=false,$session=false)/*{{{*/
  {
    parent::__construct($logg);
    $this->db=$db;
    $this->bookings=new Bookings($logg,$db);
    $this->hall=$hall;
    $this->session=$session;
    $this->getRooms();
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    $this->bookings=null;
    $this->rooms=false;
    $this->hall=null;
    parent::__destruct();
  }/*}}}*/
  public function calendarDiv($monthoffset=0,$year=0,$month=0,$day=0,$starthour=8,$slotlength=4)/*{{{*/
  {
    $row="";
    $thismonth=date("n");
    $thisyear=date("Y");
    $year=$year==0?$thisyear:$year;
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
    $midnight=mktime(0,0,0,$month,$day,$year);
    $strip=$this->roomBookingsDiv($midnight,$year,$month,$day,$starthour,$slotlength);
    for($x=0;$x<3;$x++){
      if($x>0){
        $xday=0;
      }
      $showyear=$year>$thisyear?true:false;
      $tag=new Tag("div",$this->singleCalendar($month,$year,$xday,$showyear),array("class"=>"col-md-4"));
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
    return $buttons . $cal . $key . $strip;
  }/*}}}*/
  public function roomBookingsDiv($midnight,$year,$month,$day,$start=8,$length=4)/*{{{*/
  {
    $table="<col style='width:20%'>\n";
    $iwidth=intval(80/$this->numrooms);
    for($x=0;$x<$this->numrooms;$x++){
      $table.="<col style='width:" . $iwidth . "%'>\n";
    }
    $dayheader=date("l jS F Y",$midnight);
    $tag=new Tag("th",$dayheader,array("class"=>"bookingdate"));
    $row=$tag->makeTag();
    for($x=0;$x<$this->numrooms;$x++){
      $tag=new Tag("th",$this->rooms[$x]->getField("name"),array("class"=>"roomname"));
      $row.=$tag->makeTag();
    }
    $tag=new Tag("tr",$row);
    $table.=$tag->makeTag();
    while($start<(25-$length)){
      $row="";
      $dtime=$start<10?"0" . $start:$start;
      $dtime.=":00";
      $tag=new Tag("td",$dtime,array("class"=>"roomtimestrip"));
      $row.=$tag->makeTag();
      $tm=$midnight+($start*3600);
      $tme=$tm+($length*3600);
      for($x=0;$x<$this->numrooms;$x++){
        $link=true;
        $class="roombookingcell";
        $cn=$this->bookings->getRoomBookings($this->rooms[$x]->getId(),$tm,$length*3600);
        $txt="&nbsp;";
        if($cn){
          $link=false;
          $booking=$this->bookings->nextBooking();
          $starttime=$booking->getField("date");
          $shour=date("H",$starttime);
          $smin=date("i",$starttime);
          $txt=$shour . ":" . $smin . " - " . $this->secToHMSString($booking->getField("length"));
          $status=$booking->getField("status");
          switch($status){
          case 3:
            $class.=" calnodeposit";
            break;
          case 2:
            $class.=" caldeposit";
            break;
          case 1:
            $class.=" calpaid";
            break;
          }
        }else{
          /* make the unbooked time cell clickable */
          $atts=array("a"=>1,
            "roomid"=>$this->rooms[$x]->getId(),
            "start"=>$start,
            "year"=>$year,
            "month"=>$month,
            "day"=>$day);
          $linkcell=new ALink($atts,"click to book","","roomcelllink");
          $txt=$linkcell->makeLink();
        }
        $tag=new Tag("td",$txt,array("class"=>$class));
        $row.=$tag->makeTag();
      }
      $tag=new Tag("tr",$row,array("class"=>"roombookingrow"));
      $table.=$tag->makeTag();
      $start+=$length;
    }
    $tag=new Tag("table",$table,array("class"=>"table roombookingtable"));
    $table=$tag->makeTag();
    $tag=new Tag("div",$table,array("class"=>"table.responsive"));
    $table=$tag->makeTag();
    $tag=new Tag("div",$table,array("class"=>"col-md-12 gap"));
    /*
    $table=$tag->makeTag();
    $tag=new Tag("div","Bookings",array("class"=>"panel-heading"));
    $tmp=$tag->makeTag();
    $tag=new Tag("div",$tmp . $table,array("class"=>"panel panel-primary"));
     */
    return $tag->makeTag();
  }/*}}}*/
  private function singleCalendar($month, $year,$day=0,$showyear=false)/*{{{*/
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
      $tag=new ALink("",$chevl->makeTag(),"","btn btn-space btn-default disabled");
    }else{
      $tag=new ALink(array("monthoffset"=>$monthoffset-3),$chevl->makeTag(),"","btn btn-space btn-default");
    }
    $leftb=$tag->makeLink();
    $tag=new ALink(array("monthoffset"=>0),"Today","","btn btn-space btn-default");
    $middleb=$tag->makeLink();
    $chevr=new Tag("span","",array("class"=>"glyphicon glyphicon-chevron-right"));
    $tag=new ALink(array("monthoffset"=>$monthoffset+3),$chevr->makeTag(),"","btn btn-space btn-default");
    $rightb=$tag->makeLink();
    $buttons=$leftb . $middleb . $rightb;
    $tag=new Tag("div",$buttons,array("class"=>"col-md-12 text-center gap"));
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
      $tag=new Tag("div",$table,array("class"=>"col-md-4 gap text-center"));
      $line.=$tag->makeTag();
    }
    $tag=new Tag("div",$line,array("class"=>"col-md-12 gap"));
    $line=$tag->makeTag();
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
    $thismonth=date("n");
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
              /* show today */
              $tag=new Tag("td",$xdays,array("class"=>"todaymark"));
            }elseif($days<$day && $month==$thismonth){
              /* disable days previous to today */
              $tag=new Tag("td",$xdays,array("class"=>"disabledday"));
            }else{
              $class=isset($barr[$days])?$barr[$days]["class"]:"";
              $link=new ALink(array("a"=>0,"year"=>$year,"month"=>$month,"day"=>$days),$xdays,"","caldaylink");
              $tag=new Tag("td",$link->makeLink(),array("class"=>$class));
            }
            $sop.=$tag->makeTag();
            $days++;
          }else{
            $tag=new Tag("td","&nbsp;");
            $sop.=$tag->makeTag();
          }
        }
        $idx++;
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
    /* TODO: this function needs finishing, or deleting */
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
  private function getRooms()/*{{{*/
  {
    if(false!==$this->hall){
      $this->rooms=$this->hall->getRooms();
      $this->numrooms=$this->hall->numRooms();
    }
  }/*}}}*/
}
