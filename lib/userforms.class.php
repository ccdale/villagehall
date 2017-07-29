<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Sunday 16 April 2017, 09:32:28
 * Last Modified: Saturday 29 July 2017, 19:00:03
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
require_once "base.class.php";
require_once "HTML/form.class.php";
require_once "HTML/tag.class.php";
require_once "HTML/select_field.class.php";
require_once "HTML/option_field.class.php";
require_once "room.class.php";

class UForms extends Base
{
  private $logg=false;
  private $db=false;

  public function __construct($logg=false,$db=false)/*{{{*/
  {
    parent::__construct($logg);
    $this->logg=$logg;
    $this->db=$db;
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
  }/*}}}*/
  public function userLoginForm($atts=false)/*{{{*/
  {
    $f=new Form();
    $f->addHidA($atts);
    $f->addRow("Email Address","text","emailaddress");
    $f->addSubmit("submitemail","Send Email");
    $tag=new Tag("p","Please type your email address.  We will send you a link to log you into the site.",array("class"=>"bodypara"));
    $p=$tag->makeTag();
    $tag=new Tag("div",$p . $f->makeForm(),array("class"=>"formdiv"));
    return $tag->makeTag();
  }/*}}}*/
  public function userRegisterForm($fields,$atts=false)/*{{{*/
  {
    $tfields=array();
    /* uppercase the first letter of each field name
     * and set an empty value */
    foreach($fields as $k){
      $tfields[ucfirst($k)]="";
    }
    $f=new Form();
    $f->addHidA($atts);
    $f->arrayToForm($tfields,true,"Register","register");
    $tag=new Tag("p","Please fill in the form.  We will email you a link to log you into the site.",array("class"=>"bodypara"));
    $p=$tag->makeTag();
    $tag=new Tag("div",$p . $f->makeForm(),array("class"=>"formdiv"));
    return $tag->makeTag();
  }/*}}}*/
  public function bookingForm($year,$month,$day,$hour,$roomid)/*{{{*/
  {
    $sql="select * from rooms where roomid=$roomid";
    $rooms=$this->db->arrayQuery($sql);
  }/*}}}*/
  public function preBookingForm($year,$month,$day,$hour,$roomid)/*{{{*/
  {
    $room=new Room($this->logg,$this->db,$roomid);
    $tm=mktime(0,0,0,$month,$day,$year);
    $htag=new Tag("h3","Booking " . $room->getName() . " on " . $this->stringDate($tm));
    $heading=$htag->makeTag();
    $f=new Form();
    $f->addRow("","direct",$this->timeSelector("Booking Start Time",true,$hour);
    $f->addRow("","direct",$this->timeSelector("Booking Length",false,1);
    $div=new Tag("div",$heading . $f->makeForm(),array("class"=>"prebookingformdiv"));
    return $div->makeTag();
  }/*}}}*/
  private function timeSelector($heading="Booking Start Time",$withzero=true,$hour=8)/*{{{*/
  {
    $rows="";
    $cell=new Tag("th",$heading,array("colspan"=>2,"class"=>"timeselectorheadcell"));
    $row=new Tag("tr",$cell->makeTag(),array("class"=>"timeselectorheadrow"));
    $rows.=$row->makeTag();
    $cell=new Tag("th","Hour",array("class"=>"timeselectorheadcell"));
    $row=$cell->makeTag();
    $cell=new Tag("th","Min.",array("class"=>"timeselectorheadcell"));
    $row.=$cell->makeTag();
    $how=new Tag("tr",$row,array("class"=>"timeselectorheadrow"));
    $rows.=$how->makeTag();
    $hsel=new SelectField("starthour");
    $ctxt=$hsel->hourSelector($hour,$withzero);
    $cell=new Tag("td",$ctxt,array("class"=>"timeselectorcell"));
    $row=$cell->makeTag();
    $msel=new SelectField("startmin");
    $ctxt=$msel->minuteSelector(0,$withzero);
    $cell=new Tag("td",$ctxt,array("class"=>"timeselectorcell"));
    $row.=$cell->makeTag();
    $how=new tag("tr",$row,array("class"=>"timeselectorrow"));
    $rows.=$how->makeTag();
    $tab=new Tag("table",$rows,array("class"=>"timeselectortable"));
    return $tab->makeTag():
  }/*}}}*/
}
?>
