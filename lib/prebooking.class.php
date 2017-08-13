<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Saturday 12 August 2017, 10:44:39
 * Last Modified: Sunday 13 August 2017, 08:44:37
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

require_once "data.class.php";
require_once "user.class.php";
require_once "room.class.php";
require_once "hall.class.php";

class PreBooking extends Data
{
  public function __construct($logg=false,$db=false,$guuid=false)/*{{{*/
  {
    if(false!==($junk=$this->ValidStr($guuid))){
      parent::__construct($logg,$db,"prebooking","guuid",$guuid);
    }else{
      parent::__construct($logg,$db,"prebooking");
    }
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
  }/*}}}*/
  private function validateInts($uid,$roomid,$starttime,$length)/*{{{*/
  {
    $invalid=array();
    $arr=array("uid"=>$uid,"roomid"=>$roomid,"starttime"=>$starttime,"length"=>$length);
    foreach($arr as $k=>$v){
      if(!$this->ValidInt($v)){
        $invalid[]=array($k=>$v);
      }
    }
    return $invalid;
  }/*}}}*/
  public function setupPreBooking($username,$emailaddress,$roomid,$starttime,$length)/*{{{*/
  {
    $ret=false;
    $u=new User($this->log,$this->db);
    $u->selectByEmail($emailaddress,$username);
    $uid=$u->getId();
    $invarr=$this->validateInts($uid,$roomid,$starttime,$length);
    if(0==($cn=count($invarr))){
      $this->id=false;
      $arr=array("userid"=>$uid,"guuid"=>$u->createGuid(),"roomid"=>$roomid,"date"=>$starttime,"length"=>$length,"timestamp"=>mktime());
      $this->setDataA($arr);
      if($this->ValidInt($this->id)){
        $this->debug("setup prebooking ok for email: $emailaddress, on $starttime, length: $length in roomid: $roomid");
        $ret=true;
      }else{
        $tmp=print_r($arr,true);
        $this->warning("Failed to generate a prebooking id for $tmp");
      }
    }else{
      $invalid=print_r($invarr,true);
      $this->warning("failed to obtain/generate a userid for username: $username, email: $emailaddress");
      $this->warning("invalid: $invalid");
      $this->warning("not setting up a pre-booking for roomid: $roomid, at: $starttime of length: $length");
    }
    return $ret;
  }/*}}}*/
  public function sendEmail()/*{{{*/
  {
    $ret=false;
    if($this->ValidInt($this->id)){
      $u=new User($this->log,$this->db,$this->getField("userid"));
      $username=$u->getField("name");
      $r=new Room($this->log,$this->db,$this->getField("roomid"));
      $roomname=$r->getField("name");
      $h=new Hall($this->log,$this->db);
      $h->setFromField("id",$r->getField("hallid"));
      $hallservername=$h->getField("servername");
      $hallname=$h->getField("name");
      $guuid=$this->getField("guuid");
      $start=$this->getField("date");
      $length=$this->getField("length");
      $bdate=$this->stringDate($start);
      $btime=$this->stringTime($start);
      $blen=$this->secToHMSString($length);
      $link="https://$hallservername.vhall.uk/index.php?g=" . urlencode($guuid);
      $str="Hello $username\r\n\r\n";
      $str.="Please confirm your booking by clicking the link below, or copying it and pasting it into your browser\r\n\r\n";
      $str.="Details:\r\n";
      $str.="    Hall: $hallname\r\n";
      $str.="    Room: $roomname\r\n";
      $str.="    Date: $bdate\r\n";
      $str.="    Time: $btime\r\n";
      $str.="    Length: $blen\r\n\r\n";
      $str.=$link . "\r\n\r\n";
      if(mail("chris.charles.allison+testvhall@gmail.com","$hallname Booking on $bdate",$str)){
        $this->debug("booking mail sent ok");
        $ret=true;
      }else{
        $this->warning("failed to send booking mail: $str");
      }
    }else{
      $this->warning("pre-booking has not been setup correctly");
    }
    return $ret;
  }/*}}}*/
}
?>
