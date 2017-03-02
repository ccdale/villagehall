#!/usr/bin/php
<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 * Started: Sunday 26 February 2017, 09:25:43
 * Last Modified: Sunday 26 February 2017, 09:55:58
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
$appname="villagehall";
date_default_timezone_set("Europe/London");

/*
 * If you need to put this application into a
 * subdirectory then adjust this variable accordingly.
 * strip this many directories off of the current
 * path, to find the root
 */
$stripcn=1;

/*
 * work out where we are in the file system
 */
$publicpath=getcwd();
$ppatha=explode("/",$publicpath);
$cn=count($ppatha);
$pvpatha=array();
if($cn>=$stripcn){
  for($i=0;$i<$cn-$stripcn;$i++){
    $pvpatha[$i]=$ppatha[$i];
  }
}
$vn=count($pvpatha);
$tmpa=$pvpatha;
$tmpa[$vn]="lib";
$libpath=implode("/",$tmpa);
$tmpa=$pvpatha;
$tmpa[$vn]="app";
$apppath=implode("/",$tmpa);
$pvpath=implode("/",$pvpatha);
$tmpa=$pvpatha;
$tmpa[$vn]="tests";
$testpath=implode("/",$tmpa);

echo "testpath: $testpath\n";

/* logfile test content {{{*/
$content=<<<EOD
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
logfile test content
EOD;
 /*}}}*/

unset($ppatha);
unset($cn);
unset($pvpatha);
unset($i);
unset($vn);
unset($tmpa);

$libcheck=$libpath . DIRECTORY_SEPARATOR . "base.class.php";
if(file_exists($libcheck)){
  set_include_path($libpath . PATH_SEPARATOR . get_include_path());
}else{
  echo "Base Libraries not found";
  exit(128);
}

require_once "logfile.class.php";
$logfilename=$testpath . DIRECTORY_SEPARATOR . "testlogfile.log";
$loglevel=LOG_DEBUG;
$logdorotate=true;
$logrotate="hourly";
$logkeep=5;
$logtracelevel=1;

echo "writing test files\n";
for($x=1;$x<$logkeep;$x++){
  $fn=$logfilename . "." . $x;
  $cn=file_put_contents($fn,$content);
  echo "wrote $cn bytes to $fn\n";
  $content.=$content;
}

$logg=new LogFile($logfilename,$loglevel,$logdorotate,$logkeep,$logrotate,$logtracelevel);
$logg->forceRotate();
?>
