<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Saturday 12 August 2017, 10:44:39
 * Last Modified: Saturday 12 August 2017, 12:41:47
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

class PreBooking extends Data
{
  public function __construct($logg=false,$db=false,$guid=false)/*{{{*/
  {
    if(false!==($junk=$this->ValidStr($guid))){
      parent::__construct($logg,$db,"prebooking","guid",$guid);
    }else{
      parent::__construct($logg,$db,"prebooking");
    }
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
  }/*}}}*/
  public function setupPreBooking($username,$emailaddress,$roomid,$starttime,$length)/*{{{*/
  {
    $ret=false;
    $u=new User($this->log,$this->db);
    $u->selectByEmail($emailaddress,$username);
    $uid=$u->getId();
    if($this->ValidInt($uid) && $this->ValidInt($roomid) && $this->ValidInt($starttime) && $this->ValidInt($length)){
      $this->id=false;
      $arr=array("uid"=>$uid,"guuid"=>$u->createGuid(),"roomid"=>$roomid,"date"=>$starttime,"length"=>$length);
      $this->setDataA($arr);
      if($this->ValidInt($this->id)){
        $this->debug("setup prebooking ok for email: $emailaddress, on $starttime, length: $length in roomid: $roomid");
        $ret=true;
      }else{
        $tmp=print_r($arr,true);
        $this->warning("Failed to generate a prebooking id for $tmp");
      }
    }else{
      $this->warning("failed to obtain/generate a userid for username: $username, email: $emailaddress");
      $this->warning("not setting up a pre-booking for roomid: $roomid, at: $starttime of length: $length");
    }
    return $ret;
  }/*}}}*/
}
?>
