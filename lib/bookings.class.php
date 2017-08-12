<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * bookings.class.php
 *
 * Started: Tuesday 22 November 2016, 10:15:38
 * Last Modified: Saturday 12 August 2017, 13:26:43
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

require_once "base.class.php";
require_once "booking.class.php";
require_once "prebooking.class.php";
require_once "www.php";
require_once "HTML/link.class.php";
require_once "HTML/tag.class.php";
require_once "HTML/form.class.php";

class Bookings extends Base
{
  private $db=false;
  private $bookinglist=false;
  private $bookings=false;
  private $rbookings=false;
  private $numbookings=0;
  private $currentbooking=0;
  private $bookingstable=false;

  public function __construct($logg=false,$db=false)/*{{{*/
  {
    parent::__construct($logg);
    $this->db=$db;
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    $this->bookings=false;
    $this->db=false;
    parent::__destruct();
  }/*}}}*/
  public function nextBooking()/*{{{ simple iterator */
  {
    $ret=false;
    if($this->numbookings>0){
      $this->currentbooking++;
      if($this->currentbooking>$this->numbookings){
        $this->currentbooking=1;
      }
      $ret=$this->bookings[$this->currentbooking-1];
    }
    return $ret;
  }/*}}}*/
  public function getRoomBookings($room,$starttime,$length)/*{{{*/
  {
    $this->getBookings($starttime,$length,$room);
    return $this->numbookings;
  }/*}}}*/
  public function getBookingsForDay($day,$month,$year)/*{{{*/
  {
    $tm=mktime(0,0,0,$month,$day,$year);
    $this->getBookings($tm);
    return $this->numbookings;
  }/*}}}*/
  public function getBookingsForMonth($month,$year)/*{{{*/
  {
    $tm=mktime(0,0,0,$month,1,$year);
    $d=cal_days_in_month(CAL_GREGORIAN,$month,$year);
    $d*=86400; /* number of seconds in month */
    $this->getBookings($tm,$d);
    return $this->numbookings;
  }/*}}}*/
  public function getBookingList()/*{{{*/
  {
    return $this->bookinglist;
  }/*}}}*/
  private function createBookings()/*{{{*/
  {
    if($this->numbookings>0){
      $this->bookings=array();
      $this->rbookings=array();
      foreach($this->bookinglist as $b){
        $booking=new Booking($this->log,$this->db,$b);
        $this->rbookings[$booking->getField("roomid")][]=$booking;
        $this->bookings[]=new Booking($this->log,$this->db,$b);
      }
    }
  }/*}}}*/
  private function getBookings($starttm,$length=86400,$room=false)/*{{{*/
  {
    $sql="select * from booking where date>=$starttm and date<=($starttm+$length)";
    if(false!==$room){
      $sql.=" and roomid=$room";
    }
    $sql.=" order by date asc";
    $this->updateBookingList($sql);
  }/*}}}*/
  private function updateBookingList($sql)/*{{{*/
  {
    $this->bookings=false;
    $this->rbookings=false;
    $this->bookinglist=false;
    $this->numbookings=0;
    $this->currentbooking=0;
    $tmp=$this->db->arrayQuery($sql);
    if(false!==($tcn=$this->ValidArray($tmp))){
      $this->bookinglist=$tmp;
      $this->numbookings=$tcn;
      $this->createBookings();
    }
  }/*}}}*/
  private function dayBookingsTableHead($rooms)/*{{{*/
  {
    $table=false;
    if(false!==($numrooms=$this->ValidArray($rooms))){
      $tag=new Tag("th","Bookings",array("colspan"=>$numrooms+1));
      $row=$tag->makeTag();
      $tag=new Tag("tr",$row);
      $table=$tag->makeTag();
      $tag=new Tag("th","Time");
      $row=$tag->makeTag();
      for($x=0;$x<$numrooms;$x++){
        $tag=new Tag("th",$rooms[$x]->getField("name"));
        $row.=$tag->makeTag();
      }
      $tag=new Tag("tr",$row);
      $table.=$tag->makeTag();
    }
    return $table;
  }/*}}}*/
  private function bookingsTimeCell($start)/*{{{*/
  {
    $dtime=$start<10?"0" . $start:$start;
    $dtime.=":00";
    $tag=new Tag("td",$dtime,array("class"=>"roomtimestrip"));
    return $tag->makeTag();
  }/*}}}*/
  private function blankCell($timecell=false)/*{{{*/
  {
    $atts=array("class"=>"roombookingcell");
    if($timecell){
      $atts["class"]="roomtimestrip";
    }
    // $tag=new Tag("td","&nbsp;",$atts);
    $tag=new Tag("td","",$atts);
    return $tag->makeTag();
  }/*}}}*/
  private function dayBookingsArray($day,$month,$year,$rooms,$start)/*{{{*/
  {
    $ret=0;
    $todaytm=mktime(0,0,0,$month,$day,$year);
    $sql="select * from bookings where date>=$todaytm and date<=" . $todaytm+(24*3600) . " order by date asc";
    if(false!==($darr=$this->db->arrayQuery($sql))){
    }
  }/*}}}*/
  private function noBookingsToday()/*{{{*/
  {
    $tag=new Tag("div","no bookings today.");
    return $tag->makeTag();
  }/*}}}*/
  public function loginForm()/*{{{*/
  {
    $f=new Form();
  }/*}}}*/
  public function dayBookingsTable($day,$month,$year,$rooms,$start=8)/*{{{*/
  {
    /* TODO: this function will replace calendar->roomBookingsDiv() */
    if(0!==($tmp=$this->getBookingsForDay($day,$month,$year))){
      if(false!==($table=$this->dayBookingsTableHead($rooms))){
        while($start<(24)){ /*{{{*/
          $row=$this->bookingsTimeCell($start);
          $c=count($rooms);
          for($x=0;$x<$c;$x++){
            $id=$rooms[$x]->getId();
            $sql="select * from bookings where roomid=$id and date>=$start and date<=$start+(" . $length*3600 . ") order by date asc";
            if(false!==($rarr=$this->db->arrayQuery($sql))){

            }else{
              $row.=$this->blankCell();
            }
          }

          $start+=$length;
        } /*}}}*/
      }
    }else{
      $table=$this->noBookingsToday();
    }
    return $table;
  }/*}}}*/
  public function addBookingForm($session,$day,$month,$year)/*{{{*/
  {
    $op="";
    if(false!==$session && $session->amOK()){
      $f=new Form();
      $f->addHid("a",2);
    }else{
      $op=$this->loginForm();
    }
    return $op;
  }/*}}}*/
  private function validBookingForm()/*{{{*/
  {
    $op=array("valid"=>array(),"invalid"=>array(),"invalidcn"=>0);
    $arr=array(
      array("type"=>"str","name"=>"useremailaddress"),
      array("type"=>"str","name"=>"username"),
      array("type"=>"int","name"=>"starthour","default"=>-1),
      array("type"=>"int","name"=>"startmin","default"=>-1),
      array("type"=>"int","name"=>"endhour","default"=>-1),
      array("type"=>"int","name"=>"endmin","default"=>-1),
      array("type"=>"int","name"=>"roomid","default"=>-1),
      array("type"=>"int","name"=>"start","default"=>-1),
      array("type"=>"int","name"=>"year","default"=>-1),
      array("type"=>"int","name"=>"day","default"=>-1),
      array("type"=>"int","name"=>"month","default"=>-1),
    );
    $iparr=$this->validateInputArray($arr);
    foreach($arr as $k=>$v){
      if(isset($iparr[$v["name"]])){
        $op["valid"][$v["name"]]=$iparr[$v["name"]];
      }else{
        $op["invalid"][$v["name"]]=true;
        $this->debug($v["name"] . " is invalid: " . $op["invalid"][$v["name"]]);
        $op["invalidcn"]+=1;
      }
    }
    return $op;
  }/*}}}*/
  public function processBookingForm()/*{{{*/
  {
    $errstr="<p>It is a simple form, do try and fill it in correctly. Click the link above to start again.</p>";
    $input=$this->validBookingForm();
    $debug=print_r($input,true);
    $this->debug($debug);
    if($input["invalidcn"]>0){
      return $errstr;
    }
    $starttm=mktime($input["valid"]["starthour"],$input["valid"]["startmin"],0,$input["valid"]["month"],$input["valid"]["day"],$input["valid"]["year"]);
    $endtm=mktime($input["valid"]["endhour"],$input["valid"]["endmin"],0,$input["valid"]["month"],$input["valid"]["day"],$input["valid"]["year"]);
    if($endtm<=$starttm){
      $str="<p>The ending time cannot be earlier than the starting time.</p>";
      return $str . $errstr;
    }
    $length=$endtm-$starttm;
    if(false===($pos=strpos($input["valid"]["useremailaddress"],"@"))){
      $str="<p>Hmm, that would appear to be an invalid email address.</p>";
      return $str . $errstr;
    }
    /* ok, so every thing checks out */
    $pre=new PreBooking($this->log,$this->db);
    $arr=$input["valid"];
    if(false!==($chk=$pre->setupPreBooking($arr["username"],$arr["emailaddress"],$arr["roomid"],$starttm,$length))){
      $this->debug("Booking Form processed ok");
      $pre->sendEmail();
    }else{
      $this->warning("Failed to process the Booking Form");
    }
  }/*}}}*/
}
?>
