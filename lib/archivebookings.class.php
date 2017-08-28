<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Saturday 26 August 2017, 16:27:41
 * Last Modified: Sunday 27 August 2017, 18:26:43
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

class ArchiveBooking extends Data
{
  protected $logg=false;
  protected $db=false;

  public function __construct($logg=false,$db=false,$dataid=false)/*{{{*/
  {
    $this->logg=$logg;
    $this->db=$db;
    if($this->ValidInt($dataid)){
      parent::__construct($logg,$db,"archivebooking","id",$dataid);
    }else{
      parent::__construct($logg,$db,"archivebooking");
    }
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
  }/*}}}*/
  public function moveBookingIdToArchive($bid)/*{{{*/
  {
    $ret=false;
    if($this->ValidInt($bid)){
      $this->debug("attempting to move booking id $bid to the archive");
      $b=new xBooking($this->logg,$this->db,array("id"=>$bid));
      $this->debug("booking object is now type " . gettype($b) . " and class: " . get_class($b));
      if(false!==($da=$b->getDataA())){
        if(isset($da["roomid"]) && $da["roomid"]){
          /* $this->debug("booking data returned type: " . gettype($da) . " and val: " . print_r($da,true)); */
          $this->debug("Archiving booking for room " . $da["roomid"] . " on " . $this->stringDate($da["date"]));
          $this->setDataA($da);
          $this->id=false;
          $b->deleteMe();
          $ret=true;
        }else{
          $this->warning("Seems we have an empty array returned from booking class: " . print_r($da,true));
        }
      }
    }
    return $ret;
  }/*}}}*/
  public function archiveOldBookings()/*{{{*/
  {
    $cn=0;
    $yesterday=mktime()-(24*3600);
    $sql="select * from booking where date<$yesterday";
    if(false!==($arr=$this->db->arrayQuery($sql))){
      foreach($arr as $v){
        if(false!==($junk=$this->moveBookingIdToArchive(intval($v["id"])))){
          $cn+=1;
        }else{
          $this->warning("Failed to archive booking id: " . $v["id"]);
        }
      }
      if($cn==1){
        $str="booking";
      }else{
        $str="bookings";
      }
      $this->info("$cn $str archived");
    }
  }/*}}}*/
}
?>
