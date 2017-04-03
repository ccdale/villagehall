<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * booking.class.php
 *
 * Started: Tuesday 22 November 2016, 10:15:38
 * Last Modified: Monday  3 April 2017, 12:25:37
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
require_once "HTML/link.class.php";
require_once "HTML/tag.class.php";

class Booking extends Data
{

  public function __construct($logg=false,$db=false,$data=false)/*{{{*/
  {
    if(false!==($junk=$this->ValidArray($data)) && isset($data["id"])){
      parent::__construct($logg,$db,"booking","id",$data["id"]);
    }else{
      parent::__construct($logg,$db,"booking");
    }
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
  }/*}}}*/
  public function bookingTableCell($rowheight=(4*3600))/*{{{*/
  {
    $ret=false;
    if(false!==($tmp=$this->ValidArray($this->data)) && isset($this->data["length"])){
      $atts=array("class"=>"roombookingcell");
      if(isset($this->data["status"])){
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
      }
      $rows=intval($this->data["length"] / $rowheight);
      $tmp=$this->data["length"] % $rowheight;
      if($tmp>0){
        $rows+=1;
      }
      if($rows>1){
        $atts["rowspan"]=$rows;
      }
      $shour=date("H",$this->data["date"]);
      $smin=date("i",$this->data["date"]);
      $txt=$shour . ":" . $smin . " - " . $this->secToHMSString($this->data["length"]);
      $tag=new Tag("td",$txt,$atts);
      $ret=array("rows"=>$rows,"status"=>$this->data["status"],"cell"=>$tag->makeTag());
    }
    return $ret;
  }/*}}}*/
}
?>
