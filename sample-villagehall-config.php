<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * config.php
 *
 * Started: Tuesday 22 November 2016, 10:26:19
 * Last Modified: Tuesday 27 December 2016, 12:07:51
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

$displayname="Village Hall";

/*
 * mysql
 */
$dbuser="vhuser";
$dbpass="vhpass";
$dbname="villagehall";
$dbhost="localhost";
/*
 * end of mysql
 */
/*
 * sqlite
 */
$dbfn=$pvpath . DIRECTORY_SEPARATOR . "db/villagehall.db";
/*
 * end of sqlite
 */

$dbtype="mysql";
/* $dbtype="sqlite"; */

/*
 * logging
 */
/* $logtype="syslog"; */
$logtype="file";
$logfilename=$pvpath . DIRECTORY_SEPARATOR . "log" . DIRECTORY_SEPARATOR . $appname . ".log";
$loglevel=LOG_DEBUG;
/*
  $loglevel=LOG_INFO;
  $loglevel=LOG_NOTICE;
  $loglevel=LOG_WARNING;
  $loglevel=LOG_ERROR;
 */
/* whether to rotate logs or not */
$logdorotate=true;
/* when to rotate logs */
$logrotate="hourly";
/*
$logrotate="daily";
$logrotate="weekly";
$logrotate="monthly";
 */
/* number of previous log files to keep */
$logkeep=5;
/*
 * set $tracelevel to -1,0,1 or 2
 * then, DEBUG level messages will contain the calling
 * stack trace
 * tracelevel=-1: no output at all apart from the actual debug message
 * tracelevel=0: no stack trace
 * tracelevel=1: caller function/class/file/line number
 * tracelevel=2: full stack trace
 */
$logtracelevel=-1;
?>
