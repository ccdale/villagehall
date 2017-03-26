<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * villagehall.php
 *
 * Started: Sunday 20 November 2016, 08:04:47
 * Last Modified: Sunday 26 March 2017, 17:34:09
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

session_start();
$session=false;
$user=false;
if(isset($_SESSION["sessionid"])){
  $session=new Session($logg,$db,$_SESSION["sessionid"]);
  if(false==$session->amOK()){
    $session->deleteMe();
    $session=false;
    session_unset();
    session_destroy();
    session_start();
  }else{
    $user=new User($logg,$db,$session->getField("userid"));
  }
}
if(false!==($uuid=GP("uuid"))){
  $sql="select id,userid,expires from session where uuid='$uuid'";
  if(false!==($arr=$db->arrayQuery($sql))){
    if(isset($arr[0]) && isset($arr[0]["userid"]) && isset($arr[0]["expires"])){
      $session->deleteMe();
      $session=new Session($logg,$db);
      $session->setDataA(array("userid"=>$arr[0]["userid"],"expires"=>time(),"uuid"=>$uuid));
      $session->update();
      session_unset();
      session_destroy();
      session_start();
      if(false!==$session->getId()){
        $_SESSION["sessionid"]=$session->getId();
      }
      $user=new User($logg,$db,$arr[0]["userid"]);
    }
  }
}

$mo=getDefaultInt("monthoffset",0);
$day=getDefaultInt("day",0);
$month=getDefaultInt("month",0);
$year=getDefaultInt("year",0);

$hall=new Hall($logg,$db,$hallname);

$cal=new Calendar($logg,$db,$hall);
$content=$cal->calendarDiv($mo,$year,$month,$day);

$headfn=$apppath . DIRECTORY_SEPARATOR . $appname . "-header.php";
$footfn=$apppath . DIRECTORY_SEPARATOR . $appname . "-footer.php";

$pagetitle=$hallname . " " . $displayname;

include $headfn;
include $footfn;

$tag=new Tag("div",$content,array("id"=>"body"));
$bodytag=$tag->makeTag();
$tag=new Tag("body",$bheader . $bodytag . $bfooter);
$body=$tag->makeTag();
$tag=new Tag("html",$head . $body,array("lang"=>"en"));
$html=" <!DOCTYPE html>\n" . $tag->makeTag();
echo $html;
?>
