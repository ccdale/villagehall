<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Saturday 12 August 2017, 10:44:39
 * Last Modified: Monday 28 August 2017, 13:15:32
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

class PreBooking extends Data
{
  protected $logg=false;
  protected $db=false;
  private $prebtimeout=0;
  private $admintimeout=0;

  public function __construct($logg=false,$db=false,$guuid=false)/*{{{*/
  {
    if(false!==($junk=$this->ValidStr($guuid))){
      parent::__construct($logg,$db,"prebooking","guuid",$guuid);
    }else{
      parent::__construct($logg,$db,"prebooking");
    }
    $this->logg=$logg;
    $this->db=$db;
    /* pre-booking timeout == 1 day */
    $this->setPrebtimeout(24*3600);
    /* admin access timeout == 15 minutes */
    $this->setAdmintimeout(15*60);
    $this->cleanUpGuuids();
    $this->cleanUpAdminGuuids();
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
  }/*}}}*/
  private function cleanUpAdminGuuids()/*{{{*/
  {
    $cn=0;
    $tarr=$this->arrayToday();
    $older=$tarr["timestamp"]-$this->admintimeout;
    $sql="select * from prebooking where timestamp<$older and roomid=0 and date=0 and length=0";
    if(false!==($arr=$this->db->arrayQuery($sql))){
      if(false!==($cn=$this->ValidArray($arr))){
        foreach($arr as $v){
          if($v["id"]!=$this->getId()){
            $pb=new PreBooking($this->logg,$this->db,$v["guuid"]);
            $pb->deleteMe();
          }else{
            $this->deleteMe();
          }
        }
      }
    }
    if($cn>0){
      $this->info("Cleaned up $cn expired admin login rows.");
    }
  }/*}}}*/
  private function cleanUpGuuids()/*{{{*/
  {
    $cn=0;
    $tarr=$this->arrayToday();
    $older=$tarr["timestamp"]-$this->prebtimeout;
    $selection=$this->selectFromField("timestamp","<",$older);
    if(false!==($cn=$this->ValidArray($selection))){
      foreach($selection as $v){
        if($v["id"]!=$this->getId()){
          $pb=new PreBooking($this->logg,$this->db,$v["guuid"]);
          $pb->deleteMe();
        }else{
          $this->deleteMe();
        }
      }
    }
    if($cn>0){
      $this->info("Cleaned up $cn expired prebooking rows.");
    }
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
  public function setupAdminAccess($user)/*{{{*/
  {
    $ret=false;
    if($user && is_object($user) && get_class($user)=="User"){
      $this->id=false;
      $arr=array("userid"=>$user->getId(),"guuid"=>$user->createGuid(),"timestamp"=>mktime(),"roomid"=>0,"date"=>0,"length"=>0);
      $this->setDataA($arr);
      if($this->ValidInt($this->id)){
        $this->debug("setup prebooking for admin access");
        $ret=true;
      }else{
        $this->warning("failed to setup pre-booking for admin access");
      }
    }else{
      $this->warning("\$user variable is not a valid user class");
    }
    return $ret;
  }/*}}}*/
  public function sendEmail()/*{{{*/
  {
    $ret=false;
    if($this->ValidInt($this->id)){
      $u=new User($this->logg,$this->db,$this->getField("userid"));
      $username=$u->getField("name");
      $emailaddr=$u->getField("email");
      $r=new Room($this->logg,$this->db,$this->getField("roomid"));
      $roomname=$r->getField("name");
      $h=new Hall($this->logg,$this->db);
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
      if(mail($emailaddr,"$hallname $roomname Booking on $bdate",$str)){
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
  public function validateGuuid()/*{{{*/
  {
    $ret=-1;
    if($this->id){
      $ts=$this->getField("timestamp");
      $ts+=$this->prebtimeout;
      if(mktime()<$ts){
        $arr=$this->getDataA();
        unset($arr["guuid"]);
        unset($arr["timestamp"]);
        $arr["status"]=3;
        $b=new Booking($this->logg,$this->db);
        if(false!==($junk=$b->createFromArray($arr))){
          $this->debug("prebooking transferred to booking");
          $this->debug("deleting prebooking after transfer");
          $this->deleteMe();
          $ret=0;
        }
      }else{
        $this->info("prebooking has expired, deleting");
        $this->deleteMe();
        $ret=-2;
      }
    }
    return $ret;
  }/*}}}*/
  public function validateAdminGuuid()/*{{{*/
  {
    $ret=-1;
    if($this->id){
      $xts=$this->getField("timestamp");
      $xts+=$this->admintimeout;
      $yts=mktime();
      if($yts<$xts){
        $this->setIntField("timestamp",$yts);
        $ret=0;
      }else{
        $this->info("admin timeout has expired, deleting");
        $this->deleteMe();
        $ret=-2;
      }
    }
    return $ret;
  }/*}}}*/
  public function getAdmintimeout() /*{{{*/
  {
    return $this->admintimeout;
  } /*}}}*/
  public function setAdmintimeout($admintimeout="") /*{{{*/
  {
    if($this->ValidInt($admintimeout)){
      $this->admintimeout=$admintimeout;
    }
  } /*}}}*/
  public function getPrebtimeout() /*{{{*/
  {
    return $this->prebtimeout;
  } /*}}}*/
  public function setPrebtimeout($prebtimeout="") /*{{{*/
  {
    if($this->ValidInt($prebtimeout)){
      $this->prebtimeout=$prebtimeout;
    }
  } /*}}}*/
}
?>
