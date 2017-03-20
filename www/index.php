<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * index.php
 *
 * Started: Saturday 19 November 2016, 15:35:53
 * Last Modified: Sunday  5 March 2017, 11:22:34
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

/*
 * layout of this application
 * (adjust the vars below accordingly if this changes)
 *
 * root/
 *     |
 *     - villagehall-config.php
 *
 *     - app
 *          |
 *          villagehall.php
 *
 *     - db
 *          |
 *          villagehall.db
 *
 *     - log
 *          |
 *          villagehall.log
 *          villagehall.log.1
 *
 *     - lib
 *          |
 *          php classes
 *
 *     - www (or public dir)
 *          |
 *          index.php
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

unset($ppatha);
unset($cn);
unset($pvpatha);
unset($i);
unset($vn);
unset($tmpa);

/*
 * run some checks
 */
$libcheck=$libpath . DIRECTORY_SEPARATOR . "base.class.php";
if(file_exists($libcheck)){
  set_include_path($libpath . PATH_SEPARATOR . get_include_path());
}else{
  echo "Base Libraries not found";
  exit(128);
}

/*
 * if we have the find the config and application files
 * include them (switch to the app file).
 * otherwise, if the config file is not there
 * run setup,
 * otherwise complain and stop
 */
$configfn=$pvpath . DIRECTORY_SEPARATOR . $appname . "-config.php";
$appfn=$apppath . DIRECTORY_SEPARATOR . $appname . ".php";
$includesfn=$libpath . DIRECTORY_SEPARATOR . "includes.php";
if(!file_exists($configfn)){
  $setupfn=$apppath . DIRECTORY_SEPARATOR . "setup-" . $appname . ".php";
  if(!file_exists($setupfn)){
    echo "setup not found.";
    exit(128);
  }
  include $setupfn;
}else{
  if(!file_exists($includesfn)){
    echo "Libraries not found";
    exit(128);
  }
  if(!file_exists($appfn)){
    echo "Application not found";
    exit(128);
  }

  include $configfn;
  include $includesfn;
  include $appfn;
}
?>
