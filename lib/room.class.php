<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker:
 *
 * room.class.php
 *
 * Copyright (c) 2017 Chris Allison chris.charles.allison+vh@gmail.com
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
 * Started: Saturday 11 February 2017, 10:19:15
 * Last Modified: Tuesday 15 April 2014, 07:27:19
 */

class Room extends Data
{
    public function __construct($logg=false,$db=false,$id=false)/*{{{*/
    {
        if(is_int($id)){
            $id="$id";
        }
        parent::__construct($logg,$db,"rooms","id",$id);
        $this->debug("class room, id: $id");
        $this->debug("room name: " . $this->getName());
    }/*}}}*/
    public function __destruct()/*{{{*/
    {
        parent::__destruct();
    }/*}}}*/
    public function getName()/*{{{*/
    {
        return $this->getField("name");
    }/*}}}*/
    public function getSize()/*{{{*/
    {
        return $this->getField("size");
    }/*}}}*/
}
?>
