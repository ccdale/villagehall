<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * bookings.class.php
 *
 * Started: Tuesday 22 November 2016, 10:15:38
 * Last Modified: Sunday 26 March 2017, 06:25:44
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

class Bookings extends Base
{
  private $db=false;
  private $bookinglist=false;
  private $bookings=false;
  private $numbookings=0;
  private $currentbooking=0;

  public function __construct($logg=false,$db=false)/*{{{*/
  {
    parent::__construct($logg);
    $this->db=$db;
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    $this->bookings=false;
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
      foreach($this->bookinglist as $b){
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
    $this->bookinglist=false;
    $this->numbookings=0;
    $tmp=$this->db->arrayQuery($sql);
    if(false!==($tcn=$this->ValidArray($tmp))){
      $this->bookinglist=$tmp;
      $this->numbookings=$tcn;
      $this->createBookings();
    }
  }/*}}}*/
}
?>
