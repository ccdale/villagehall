<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * booking.class.php
 *
 * Started: Tuesday 22 November 2016, 10:15:38
 * Last Modified: Saturday 25 March 2017, 13:31:16
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

require_once "data.class.php";

class Booking extends Data
{
  private $fields=false;
  private $bookinglist=false;
  private $numbookings=0;

  public function __construct($logg=false,$db=false)/*{{{*/
  {
    parent::__construct($logg,$db,"booking");
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
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
  private function getBookings($starttm,$length=86400)/*{{{*/
  {
    $sql="select * from booking where date>=$starttm and date<=($starttm+$length) order by date asc";
    $this->updateBookingList($sql);
  }/*}}}*/
  private function updateBookingList($sql)/*{{{*/
  {
    $tmp=$this->db->arrayQuery($sql);
    if(false!==($tcn=$this->ValidArray($tmp))){
      $this->bookinglist=$tmp;
      $this->numbookings=$tcn;
    }else{
      $this->bookinglist=false;
      $this->numbookings=0;
    }
  }/*}}}*/
}
?>
