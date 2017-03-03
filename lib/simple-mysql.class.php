<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * simple-mysql.class.php
 *
 * Started: Monday 23 July 2012, 13:41:11
 * Last Modified: Friday  3 March 2017, 09:09:46
 *
 * Copyright (c) 2014 Chris Allison chris.charles.allison+vh@gmail.com
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

/** MySql Class
 * simple class to connect and do db stuff
 * with mysqli
 */
class MySql extends Base
{
  private $conn;
  private $rs;
  private $dbhost;
  private $dbdb;
  private $dbuser;
  private $dbpass;
  private $canconnect=false;
  private $connected=false;
  private $selected=true;
  private $mysqlerror=array();

  public function __construct($logg=false,$host="",$user="",$pass="",$db="",$force=false) /*{{{*/
  {
    parent::__construct($logg);
    $this->resetErrors();
    if($this->ValidStr($host)){
      $this->dbhost=$host;
    }elseif(defined("MYSQLHOST")){
      $this->dbhost=MYSQLHOST;
    }else{
      $this->dbhost="localhost";
    }
    if($this->ValidStr($user)){
      $this->dbuser=$user;
    }elseif(defined("MYSQLUSER")){
      $this->dbuser=MYSQLUSER;
    }else{
      $this->dbuser="";
    }
    if($this->ValidStr($pass)){
      $this->dbpass=$pass;
    }elseif(defined("MYSQLPASS")){
      $this->dbpass=MYSQLPASS;
    }else{
      $this->dbpass="";
    }
    if($this->ValidStr($db)){
      $this->dbdb=$db;
    }elseif(defined("MYSQLDB")){
      $this->dbdb=MYSQLDB;
    }else{
      $this->dbdb="";
    }
    if($this->ValidStr($this->dbhost) && $this->ValidStr($this->dbuser) && $this->ValidStr($this->dbpass) && $this->ValidStr($this->dbdb)){
      $this->canconnect=true;
    }elseif($this->force){
      $this->canconnect=true;
    }
    try {
      $this->conn=mysqli_connect($this->dbhost,$this->dbuser,$this->dbpass);
      $this->connected=true;
      $this->debug("Connected to db host ok: " . $this->dbhost);
    }catch (Exception $e){
      $this->error('Caught exception when connecting to db (' . $this->dbhost . '): ' .  $e->getMessage());
      $this->connected=false;
    }
    try {
      mysqli_select_db($this->conn,$this->dbdb);
      $this->selected=true;
      $this->debug("DB selected ok: " . $this->dbdb);
    }catch (Exception $e){
      $this->error('Caught exception in selecting db (' . $this->dbdb . '): ' .  $e->getMessage());
    }
  } // }}}
  public function __destruct() /*{{{*/
  {
    $this->closedb();
    parent::__destruct();
  } // }}}
  private function resetErrors()/*{{{*/
  {
    $this->mysqlerror=array(
      "errno"=>0,
      "error"=>""
    );
  }/*}}}*/
  public function closedb() // {{{
  {
    if($this->conn){
      try{
        @mysqli_close($this->conn);
      }catch(Exception $e){
        $this->debug($e->getMessage());
      }
    }
  } // }}}
  public function amOK() // {{{
  {
    if($this->connected && $this->selected){
      if($this->mysqlerror["errno"]==0){
        return true;
      }else{
        return false;
      }
    }
    return false;
  } // }}}
  public function getErrors()/*{{{*/
  {
    return $this->mysqlerror;
  }/*}}}*/
  public function query($sql="") // {{{
  {
    $this->rs=null;
    $this->resetErrors();
    if($this->amOK() && $this->ValidStr($sql)){
      $this->debug("Query: $sql");
      $this->rs=mysqli_query($this->conn,$sql);
      if(false===$this->rs){
        $this->error("Query error: $sql");
        $this->mysqlerror["errno"]=mysqli_errno($this->conn);
        $this->mysqlerror["error"]=mysqli_error($this->conn);
        $this->error("mysql said: " . $this->mysqlerror["errno"] . ": " . $this->mysqlerror["error"]);
      }
    }else{
      $this->warning("mysql class not ok, or sql not a valid str");
      $tmp=print_r($sql,true);
      $this->warning("print_r(\$sql): $tmp");
    }
    return $this->rs;
  } // }}}
  public function insertQuery($sql="") // {{{
  {
    /*
     * returns insert id or false for insert queries
     */
    $ret=$this->query($sql);
    if($ret){
      $ret=mysqli_insert_id($this->conn);
    }else{
      $str=mysqli_error($this->conn);
      $this->error($str);
    }
    return $ret;
  } // }}}
  public function deleteQuery($sql="")/*{{{*/
  {
    /*
     * returns the number of rows deleted or false for delete queries
     */
    $ret=$this->query($sql);
    if($ret){
      $ret=mysqli_affected_rows($this->conn);
    }else{
      $str=mysqli_error($this->conn);
      $this->error($str);
    }
  }/*}}}*/
  public function arrayQuery($sql="") // {{{
  {
    $ret=false;
    $this->query($sql);
    if($this->rs){
      $cn=mysqli_num_rows($this->rs);
      $this->debug("$cn rows returned");
      if($cn>0){
        $ret=array();
        while(false!=($arr=mysqli_fetch_assoc($this->rs))){
          $ret[]=$arr;
        }
      }
    }
    return $ret;
  } // }}}
  public function escape($str="")/*{{{*/
  {
    return mysqli_real_escape_string($this->conn,$str);
  }/*}}}*/
  public function makeFieldString($val)/*{{{*/
  {
    if(false!==($cn=$this->ValidString($val))){
      $op="'$val'";
    }else{
      $op=$val;
    }
    return $op;
  }/*}}}*/
  public function insertCheck($table,$fields)/*{{{*/
  {
    $ret=false;
    $carr=$farr=$varr=array();
    $sql=$sqli=$ssql=$fs=$vs="";
    foreach($fields as $field=>$value){
      $tmp=$this->makeFieldString($value);
      $ssql.=$field . "=" . $tmp . " and ";
      $fs.=$field . ",";
      $vs.=$tmp . ",";
    }
    $ssql=substr($ssql,0,-5);
    $fs=substr($fs,0,-1);
    $vs=substr($vs,0,-1);
    $sql="select * from $table where $ssql";
    $sqli="insert into $table ($fs) values ($vs)";
    $rarr=$this->arrayQuery($sql);
    if(false!==($cn=$this->ValidArray($rarr)) && $cn>0){
      // record already exists
      $this->debug("record already exists ($sql)");
      $ret=true;
    }else{
      $this->debug("record doesn't exist, inserting :$sqli:");
      $iid=$this->insertQuery($sqli);
      $ret=$iid;
    }
    return $ret;
  }/*}}}*/
  public function insertUpdate($table,$fields,$id=false)/*{{{*/
  {
    $isql=$usql=$fs=$vs="";
    foreach($fields as $field=>$value){
      $tval=$this->makeFieldString($value);
      if(strlen($usql)){
        $usql.=" and " . $field . "=" . $tval;
        $fs.="," . $field;
        $vs.="," . $tval;
      }else{
        $usql=$field . "=" . $tval;
        $fs=$field;
        $vs=$tval;
      }
    }
  }/*}}}*/
}
?>
