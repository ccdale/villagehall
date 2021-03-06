<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * booking.class.php
 *
 * Started: Tuesday 22 November 2016, 10:15:38
 * Last Modified: Monday 28 August 2017, 12:12:39
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

class Booking extends Data
{
  private $hour=false;
  private $shour="";
  private $minute=false;
  private $sminute="";

  public function __construct($logg=false,$db=false,$dataa=false)/*{{{*/
  {
    if(false!==($junk=$this->ValidArray($dataa)) && isset($dataa["id"])){
      parent::__construct($logg,$db,"booking","id",$dataa["id"]);
      $this->info("accessing booking id: " . $dataa["id"]);
      $this->setupTimes();
    }else{
      parent::__construct($logg,$db,"booking");
      $this->info("New, empty, booking created");
    }
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
  }/*}}}*/
  private function setupTimes()/*{{{*/
  {
    if(isset($this->data["date"])){
      $this->hour=date("G",$this->data["date"]);
      $this->shour=$this->hour<10?"0" . $this->hour:$this->hour;
      $this->minute=intval(date("i",$this->data["date"]));
      $this->sminute=$this->minute<10?"0" . $this->minute:$this->minute;
    }
  }/*}}}*/
  public function bookingTableCell($rowheight=(4*3600))/*{{{*/
  {
    $ret=false;
    if($this->getId()){
      $atts=array("class"=>"roombookingcell");
        switch($this->data["status"]){
          case 3:
            $atts["class"].=" calnodeposit";
            break;
          case 2:
            $atts["class"].=" caldeposit";
            break;
          case 1:
            $atts["class"].=" calpaid";
            break;
        }
      $rows=intval($this->data["length"] / $rowheight);
      $rem=$this->data["length"] % $rowheight;
      if($rem>0){
        $rows++;
      }
      if($rows>1){
        $atts["rowspan"]=$rows;
      }
      $txt=$this->shour . ":" . $this->sminute . " - " . $this->secToHMSString($this->data["length"]);
      $tag=new Tag("td",$txt,$atts);
      $ret=array("rows"=>$rows,"status"=>$this->data["status"],"cell"=>$tag->makeTag());
    }
    return $ret;
  }/*}}}*/
  public function getBookingTime()/*{{{*/
  {
    return array("hour"=>$this->hour,"minute"=>$this->minute);
  }/*}}}*/
  public function createFromArray($arr)/*{{{*/
  {
    $ret=false;
    $this->setDataA($arr);
    if($this->id){
      $this->setupTimes();
      $ret=true;
    }
    return $ret;
  }/*}}}*/
  public function payBooking()/*{{{*/
  {
    $status=$this->getField("status");
    $status-=1;
    if($status<1){
      $status=1;
    }
    $sql="update booking set status=$status where id=" . $this->id;
    $this->db->query($sql);
    if($this->db->amOK()){
      return true;
    }
    return false;
  }/*}}}*/
  public function unPayBooking()/*{{{*/
  {
    $status=$this->getField("status");
    $status+=1;
    if($status>3){
      $status=3;
    }
    $sql="update booking set status=$status where id=" . $this->id;
    $this->db->query($sql);
    if($this->db->amOK()){
      return true;
    }
    return false;
  }/*}}}*/
}
?>
