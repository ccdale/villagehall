<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * user.class.php
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
 * 
 *
 * Started: Monday 26 December 2016, 07:14:35
 * Last Modified: Tuesday 15 April 2014, 07:27:19
 */

require_once "base.class.php";

class User extends Base
{
    private $db=false;
    private $dirty=false;
    private $data=false;
    private $id=false;

    public function __construct($logg=false,$db=false)/*{{{*/
    {
        parent::__construct($logg);
        $this->db=$db;
        $this->data=array(
            "name"=>"",
            "password"=>"",
            "email"=>"",
            "phone"=>"",
            "address1"=>"",
            "address2"=>"",
            "town"=>"",
            "postcode"=>""
        );
    }/*}}}*/
    public function __destruct()/*{{{*/
    {
        parent::__destruct();
        $this->db=null;
    }/*}}}*/
}
?>
