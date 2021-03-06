<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker:
 *
 * hall.class.php
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

class Hall extends Data
{
    private $numrooms=false;
    private $rooms=false;

    public function __construct($logg=false,$db=false,$name=false)/*{{{*/
    {
        parent::__construct($logg,$db,"hall","name",$name);
        $this->readRooms();
    }/*}}}*/
    public function __destruct()/*{{{*/
    {
        $this->rooms=false;
        parent::__destruct();
    }/*}}}*/
    public function findHall($tmphallname)/*{{{*/
    {
        if(false!==($id=$this->setFromField("servername",$tmphallname))){
            $this->readRooms();
            $this->debug("hall found: " . $this->getName());
            return true;
        }else{
            $this->warning("Failed to set the hall from name: $tmphallname");
            return false;
        }
    }/*}}}*/
    public function numRooms()/*{{{*/
    {
        return $this->numrooms;
    }/*}}}*/
    public function getRooms()/*{{{*/
    {
        return $this->rooms;
    }/*}}}*/
    public function getRoomIds()/*{{{*/
    {
        $roomids=false;
        if(false!==$this->rooms){
            $roomids=array();
            foreach($this->rooms as $room){
                $roomids[]=$room->getId();
            }
        }
        return $roomids;
    }/*}}}*/
    public function getName()/*{{{*/
    {
        return $this->getField("name");
    }/*}}}*/
    private function readRooms()/*{{{*/
    {
        if(false!==$this->id){
            $sql="select id from rooms where hallid=" . $this->id;
            $tmp=$this->db->arrayQuery($sql);
            if(false!==($this->numrooms=$this->ValidArray($tmp))){
                $this->rooms=array();
                foreach($tmp as $r){
                    $this->rooms[]=new Room($this->log,$this->db,$r["id"]);
                }
            }
        }
    }/*}}}*/
}
?>
