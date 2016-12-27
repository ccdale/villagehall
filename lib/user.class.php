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

require_once "data.class.php";

class User extends Data
{
    public function __construct($logg=false,$db=false,$email=false,$password=false)/*{{{*/
    {
        parent::__construct($logg,$db,"user","email",$email);
        $this->validate($password);
    }/*}}}*/
    public function __destruct()/*{{{*/
    {
        parent::__destruct();
    }/*}}}*/
    private function validate($incomingpass=false)/*{{{*/
    {
        $ret=false;
        if(false!==($junk=$this->ValidStr($incomingpass))){
            if(false!==($junk=$this->ValidArray($this->data))){
                if(isset($this->data["password"])){
                    if($ret=password_verify($incomingpass,$this->data["password"])){
                        $this->debug("User class validate: Password correct!");
                    }else{
                        $this->debug("User class validate: Password not correct");
                    }
                }else{
                    $this->debug("User class validate: Password field not set in data");
                }
            }else{
                $this->debug("User class validate: User data is not set");
            }
        }else{
            $this->debug("User class validate: incoming password is not set");
        }
        return $ret;
    }/*}}}*/
}
?>
