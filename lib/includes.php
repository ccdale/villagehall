<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 * Started: Sunday  5 March 2017, 11:17:25
 * Last Modified: Monday 28 August 2017, 12:56:31
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

switch($logtype){
case "file":
  require_once "logfile.class.php";
  $logg=new LogFile($logfilename,$loglevel,$logdorotate,$logkeep,$logrotate,$logtracelevel);
  break;
case "syslog":
  require_once "logging.class.php";
  $logg=new Logging(false,"VHPHP",0,$loglevel,false,false,$logtracelevel);
  break;
}
/*
 * setup database connection
 */
if($dbtype=="mysql"){
  require_once "simple-mysql.class.php";
  $db=new MySql($logg,$dbhost,$dbuser,$dbpass,$dbname);
}elseif($dbtype=="sqlite"){
  require_once "simple-sqlite.class.php";
  $db=new SSql($dbfn,$logg);
}
$sql="select * from hall";
$rarr=$db->arrayQuery($sql);
if(!$db->amOK()){
  $earr=$db->getErrors();
  if($earr["errno"]!=0){
    /* database not set up */
    $sqlstr=file_get_contents($dbsetupfn);
    if($result=$db->query($sqlstr)){
      $db->info($appname . " database setup ok.");
    }else{
      $db->error("failed to setup database for " . $appname);
      exit(1);
    }
  }
}

require_once "data.class.php";
require_once "bookings.class.php";
require_once "booking.class.php";
require_once "room.class.php";
require_once "hall.class.php";
require_once "calendar.class.php";
require_once "userforms.class.php";
require_once "user.class.php";
require_once "hall.class.php";
require_once "prebooking.class.php";
require_once "switchboard.class.php";
require_once "archivebookings.class.php";
require_once "HTML/link.class.php";
require_once "HTML/tag.class.php";
require_once "HTML/form.class.php";
require_once "HTML/select_field.class.php";
require_once "HTML/option_field.class.php";
require_once "HTML/class.table.php";
?>
