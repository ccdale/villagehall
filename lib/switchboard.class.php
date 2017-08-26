<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Saturday 19 August 2017, 09:03:04
 * Last Modified: Saturday 26 August 2017, 08:06:46
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

class Switchboard extends Base
{
  private $db=false;
  private $hall=false;
  private $logg=false;
  private $action=0;
  private $mo=0;
  private $day=0;
  private $month=0;
  private $year=0;
  private $starttime=0;
  private $roomid=0;
  private $guuid=false;
  private $admin=false;

  public function __construct($logg=false,$db=false,$hall=false)/*{{{*/
  {
    parent::__construct($logg);
    $this->logg=$logg;
    $this->db=$db;
    $this->hall=$hall;
    $this->mo=$this->getDefaultInt("monthoffset",0);
    $this->day=$this->getDefaultInt("day",0);
    $this->month=$this->getDefaultInt("month",0);
    $this->year=$this->getDefaultInt("year",0);
    $this->starttime=$this->getDefaultInt("start",0);
    $this->roomid=$this->getDefaultInt("roomid",0);
    $this->action=$this->getDefaultInt("a",0);
    $this->guuid=$this->GP("g");
    if(false!==($cn=$this->ValidStr($this->guuid))){
      $this->action=3;
    }
    $this->admin=$this->GP("z");
    if(false!==($cn=$this->ValidStr($this->admin))){
      $this->action=99;
    }
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
  }/*}}}*/
  public function doAction()/*{{{*/
  {
    $op="<p class='errortext'>Error: Action not found.</p>\n";
    switch($this->action){
    case 1:
      if($this->roomid>0){
        $room=new Room($this->logg,$this->db,$this->roomid);
        $u=new UForms($this->logg,$this->db);
        $op=$u->preBookingForm();
      }else{
        $this->error("Room ID not set in Get params for pre Booking");
        $op="<div class='error'><p>An Error occurred obtaining room details</p></div>\n";
      }
      break;
    case 2:
      $b=new Bookings($this->logg,$this->db);
      $op=$b->processBookingForm();
      break;
    case 3:
      $b=new Bookings($this->logg,$this->db);
      $op=$b->processGuuid($this->guuid);
      break;
    case 21:
      $op="<p class='bodytext'>This is the booking system for the Village Hall</p>\n";
      break;
    case 22:
      $op="<p class='bodytext'>The Secretary can be contacted at lidlington.vhall.uk@gmail.com</p>\n";
      break;
    case 99:
      /*
       * initiate an admin login
       */
      require_once "privileges.class.php";
      require_once "admin.class.php";
      $ad=new Admin($this->logg,$this->db);
      if(false!==($junk=$ad->initSendEmail($this->hall))){
        $op="<p>An email has been sent to allow the Administrator to logon</p>\n";
      }
      break;
    default:
      $cal=new Calendar($this->logg,$this->db,$this->hall);
      $op=$cal->calendarDiv($this->mo,$this->year,$this->month,$this->day,8,2);
      break;
    }
    return $op;
  }/*}}}*/
}
?>
