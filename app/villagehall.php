<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * villagehall.php
 *
 * Started: Sunday 20 November 2016, 08:04:47
 * Last Modified: Saturday 12 August 2017, 11:24:51
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


require_once "www.php";
require_once "session.class.php";
require_once "calendar.class.php";
require_once "userforms.class.php";
require_once "room.class.php";

session_start();
$session=false;
if(isset($_SESSION["sessionid"])){
  $session=new Session($logg,$db,$_SESSION["sessionid"]);
  if(false==$session->amOK()){
    $session->deleteMe();
    $session=false;
    resetPHPSession();
  }
}
if(false!==($uuid=GP("uuid"))){
  resetPHPSession();
  $session=new Session($logg,$db);
  if(false!==($id=$session->setFromUUID($uuid))){
    $_SESSION["sessionid"]=$id;
  }else{
    $session=false;
  }
}

$mo=getDefaultInt("monthoffset",0);
$day=getDefaultInt("day",0);
$month=getDefaultInt("month",0);
$year=getDefaultInt("year",0);
$starttime=getDefaultInt("start",0);
$roomid=getDefaultInt("roomid",0);
$action=getDefaultInt("a",0);
/* $emailaddress=GP("useremailaddress"); */

$hall=new Hall($logg,$db);
$hall->findHall($thallname);

switch($action){
case 0:
  $cal=new Calendar($logg,$db,$hall,$session);
  $content=$cal->calendarDiv($mo,$year,$month,$day,8,2);
  break;
case 1:
  if($roomid>0){
    $room=new Room($logg,$db,$roomid);
    $u=new UForms($logg,$db);
    $content=$u->preBookingForm();
  }else{
    $logg->error("Room ID not set in Get params for pre Booking");
    $content="<div class='error'><p>An Error occurred obtaining room details</p></div>\n";
  }
  break;
case 2:
  $b=new Bookings($logg,$db);
  $content=$b->processBookingForm();
}

$headfn=$apppath . DIRECTORY_SEPARATOR . $appname . "-header.php";
$footfn=$apppath . DIRECTORY_SEPARATOR . $appname . "-footer.php";

$pagetitle=$hall->getName() . " " . $displayname;

include $headfn;
include $footfn;

/*
$tag=new Tag("div",$content,array("id"=>"body"));
$bodytag=$tag->makeTag();
 */
$tag=new Tag("body",$bheader . $content . $bfooter);
$body=$tag->makeTag();
$tag=new Tag("html",$head . $body,array("lang"=>"en"));
$html="<!DOCTYPE html>" . $tag->makeTag();
echo $html;
?>
