<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker:
 *
 * lidlington.php
 *
 * Started: Sunday 20 November 2016, 08:04:47
 * Last Modified: Tuesday 22 November 2016, 10:38:51
 *
 * Copyright (c) 2016 Chris Allison chris.allison@hotmail.com
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
require_once "booking.class.php";

date_default_timezone_set("Europe/London");

$logg=new Logging(false,"VHPHP",0,LOG_INFO);

$content=$apppath;
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
