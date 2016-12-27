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
    protected $data=false;

    public function __construct($logg=false,$db=false,$table=false,$field=false,$data=false)/*{{{*/
    {
        parent::__construct($logg);
        $this->db=$db;
        $this->init($table,$field,$data);
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
        }
    }/*}}}*/
}
?>
