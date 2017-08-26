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

class Data extends Base
{
    private $dirty=false;
    protected $db=false;
    protected $id=false;
    protected $table=false;
    protected $data=false;
    private $xfields=false;

    public function __construct($logg=false,$db=false,$table=false,$field=false,$data=false)/*{{{*/
    {
        parent::__construct($logg);
        $this->db=$db;
        $this->table=$table;
        $this->init($field,$data);
    }/*}}}*/
    public function __destruct()/*{{{*/
    {
        parent::__destruct();
    }/*}}}*/
    private function init($field=false,$data=false)/*{{{*/
    {
        if(is_object($this->db)){
            $class=get_class($this->db);
            if($class=="SSql"){
                $this->getSSqlFields();
                $this->debug("data class: db object is sqlite");
            }elseif($class=="MySql"){
                $this->getMysqlFields();
                $this->debug("data class: db object is mysql");
            }else{
                $this->error("DB class is neither MySql nor SSql");
                return false;
            }
        }else{
            $this->warn("data class: db is not an object");
        }
        if($this->ValidStr($field) && $this->ValidStr($data)){
            $sql="select * from $this->table where $field='" . $this->db->escape($data) . "'";
            $tmp=$this->db->arrayQuery($sql);
            $this->data=$tmp[0];
            $this->id=$this->data["id"];
            unset($this->data["id"]);
        }else{
            if($this->ValidStr($field)){
                $this->debug("data class: \$data '$data' is not a valid string");
            }else{
                $this->debug("data class: \$field: '$field' is not a valid string");
            }
        }
    }/*}}}*/
    private function getMysqlFields()/*{{{*/
    {
        $sql="show columns from " . $this->table;
        if(false!==($colsarr=$this->db->arrayQuery($sql))){
            $this->data=array();
            $this->xfields=array();
            foreach($colsarr as $val){
                $this->data[$val["Field"]]=false;
                if($val["Field"]!=="id"){
                    $this->xfields[]=$val["Field"];
                }
            }
            unset($this->data["id"]);
        }
    }/*}}}*/
    private function getSSqlFields()/*{{{*/
    {
        $sql="PRAGMA table_info(" . $this->table . ")";
        if(false!==($colsarr=$this->db->arrayQuery($sql))){
            $this->data=array();
            $this->xfields=array();
            foreach($colsarr as $val){
                $this->data[$val["name"]]=false;
                if($val["Field"]!=="id"){
                    $this->xfields[]=$val["Field"];
                }
            }
            unset($this->data["id"]);
        }
    }/*}}}*/
    private function insertUpdate($table,$fields,$id=false)/*{{{*/
    {
        $ret=false;
        if(false===$id){
            if(false!==($tmp=$this->insertFields($fields))){
                $sql="insert into " . $table . " " . $tmp;
                if(false!==($tret=$this->db->insertQuery($sql))){
                    $ret=$tret;
                }
            }
        }else{
            if(false!==($tmp=$this->updateFields($fields))){
                $sql="update " . $table . " set " . $tmp . " where id=" . $id;
                if(false!==($tret=$this->db->query($sql))){
                    $ret=$id;
                }
            }
        }
        return $ret;
    }/*}}}*/
    protected function insertFields($fields)/*{{{*/
    {
        $ret=false;
        if(false!==($this->ValidArray($fields))){
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
    protected function updateFields($fields)/*{{{*/
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
    public function selectFromField($field,$operator,$val)/*{{{*/
    {
        $ret=false;
        $sql="select * from " . $this->table . " where " . $field . $operator . $this->db->makeFieldString($val);
        $ret=$this->db->arrayQuery($sql);
    }/*}}}*/
    public function setFromField($field,$val)/*{{{*/
    {
        $ret=false;
        $sql="select * from " . $this->table . " where $field=" . $this->db->makeFieldString($val);
        if(false!==($rarr=$this->db->arrayQuery($sql))){
            $this->data=$rarr[0];
            unset($this->data["id"]);
            $this->id=intval($rarr[0]["id"]);
            $ret=$this->id;
        }
        return $ret;
    }/*}}}*/
    public function setFromFindField($field,$val)/*{{{*/
    {
        $ret=false;
        $sql="select * from " . $this->table . " where $field like " . $this->db->makeFindString($val);
        if(false!==($rarr=$this->db->arrayQuery($sql))){
            $this->data=$rarr[0];
            unset($this->data["id"]);
            $this->id=$rarr[0]["id"];
            $ret=$this->id;
        }
        return $ret;
    }/*}}}*/
    public function update()/*{{{*/
    {
        if($this->dirty){
            if(false!==($tid=$this->insertUpdate($this->table,$this->data,$this->id))){
                $this->dirty=false;
                $this->id=$tid;
            }
        }
    }/*}}}*/
    public function getFieldList()/*{{{*/
    {
        return $this->xfields;
    }/*}}}*/
    public function setField($Field="",$val="") /*{{{*/
    {
        if($this->ValidStr($Field)){
            if($this->ValidStr($val)){
                $this->data[$Field]=$val;
                $this->dirty=true;
                $this->update();
            }
        }
    } /*}}}*/
    public function getField($Field="")/*{{{*/
    {
        if($this->ValidStr($Field) && isset($this->data[$Field])){
            return $this->data[$Field];
        }
        return false;
    }/*}}}*/
    public function setDataA($data=false)/*{{{*/
    {
        if($this->ValidArray($data)){
            $this->data=$data;
            $this->dirty=true;
            $this->update();
        }
    }/*}}}*/
    public function getDataA() /*{{{*/
    {
        return $this->data;
    } /*}}}*/
    public function amDirty()/*{{{*/
    {
        return $this->dirty;
    }/*}}}*/
    public function getId()/*{{{*/
    {
        return $this->id;
    }/*}}}*/
    public function deleteMe()/*{{{*/
    {
        $numrows=false;
        if($this->id){
            $sql="delete from " . $this->table . " where id=" . $this->id;
            if(false==($numrows=$this->db->deleteQuery($sql))){
                $this->error("Failed to delete row from " . $this->table . ": rowid: " . $this->id);
            }else{
                $this->data=false;
                $this->id=false;
            }
        }
        return $numrows;
    }/*}}}*/
}
?>
