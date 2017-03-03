<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker:
 *
 * data.class.php
 *
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
 * Started: Tuesday 27 December 2016, 09:25:58
 * Last Modified: Tuesday 15 April 2014, 07:27:19
 */

require_once "base.class.php";

class Data extends Base
{
    private $db=false;
    private $dirty=false;
    protected $id=false;
    protected $table=false;
    protected $data=false;

    public function __construct($logg=false,$db=false,$table=false,$field=false,$data=false)/*{{{*/
    {
        parent::__construct($logg);
        $this->db=$db;
        $this->table=$table;
        $this->init($this->table,$field,$data);
    }/*}}}*/
    public function __destruct()/*{{{*/
    {
        parent::__destruct();
    }/*}}}*/
    private function init($table=false,$field=false,$data=false)/*{{{*/
    {
        if($this->db){
            if(!$this->ValidStr($table)){
                $this->debug("Data class: Table string not set");
                return false;
            }
            if(!$this->ValidStr($field)){
                $this->debug("Data class: Field string not set");
                return false;
            }
            if(!$this->ValidStr($data)){
                $this->debug("Data class: Data string not set");
                return false;
            }
            $sql="select * from $table where $field='" . $this->db->escape($data) . "'";
            $this->data=$this->db->arrayQuery($sql);
            $this->id=$this->data["id"];
            unset($this->data["id"]);
        }
    }/*}}}*/
    private function insertFields($fields)/*{{{*/
    {
        $ret=false;
        if(false!==($this->ValidArray())){
            $fs=$vs="";
            foreach($fields as $field=>$value){
                if(strlen($fs)){
                    $fs.="," . $field;
                    $vs.="," . $this->db->makeFieldString($value);
                }else{
                    $fs=$field;
                    $vs=$this->db->makeFieldString($value);
                }
            }
            $ret="(" . $fs . ") values (" . $vs . ")";
        }
        return $ret;
    }/*}}}*/
    private function updateFields($fields)/*{{{*/
    {
        $ret=false;
        if(false!==($this->ValidArray($fields))){
            $tstr="";
            foreach($fields as $field=>$value){
                if(strlen($tstr)){
                    $tstr.=" and " . $field . "=" . $this->db->makeFieldString($value);
                }else{
                    $tstr=$field . "=" . $this->db->makeFieldString($value);
                }
            }
            $ret=$tstr;
        }
        return $ret;
    }/*}}}*/
    public function update()/*{{{*/
    {
        if($this->dirty){
            if(false!==($sql=$this->insertUpdate($this->table,$this->data,$this->id))){
            }
        }
    }/*}}}*/
    public function insertUpdate($table,$fields,$id=false)/*{{{*/
    {
        $ret=false;
        if(false===$id){
            if(false!==($tmp=$this->insertFields($fields))){
                $sql="insert into " . $table . " " . $tmp;
                $ret=$this->db->insertQuery($sql);
            }
        }else{
            if(false!==($tmp=$this->updateFields($fields))){
                $sql="update " . $table . " set " . $tmp . " where id=" . $id;
                $ret=$this->db->query($sql);
            }
        }
        return $ret;
    }/*}}}*/
}
?>
