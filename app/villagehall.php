<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * villagehall.php
 *
 * Started: Sunday 20 November 2016, 08:04:47
 * Last Modified: Monday 26 December 2016, 06:52:17
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

require_once "base.class.php";
require_once "logging.class.php";
require_once "simple-mysql.class.php";
require_once "simple-sqlite.class.php";
require_once "booking.class.php";

$logg=new Logging(false,"VHPHP",0,LOG_INFO);

/*
 * setup database connection
 */
if($dbtype=="mysql"){
  $db=new MySql($logg,$dbhost,$dbuser,$dbpass,$dbname);
}elseif($dbtype=="sqlite"){
  $db=new SSql($dbfn,$logg);
}


$content=$apppath . "<br>" . $libpath . "<br>" . $pvpath;
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $displayname; ?></title>
</head>
<body>
<div>
<h1><?php echo $displayname; ?></h1>
<p>
<?php echo $content; ?>
</p>
</div>
</body>
</html>
