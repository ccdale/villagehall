<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * simple-sqlite.class.php
 *
 * Started: Sunday  2 August 2015, 12:29:49
 * Last Modified: Sunday 20 August 2017, 08:47:12
 * 
 * Copyright (c) 2015 Chris Allison chris.charles.allison+vh@gmail.com
 *
 * This file is part of cristel.
 * 
 * cristel is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * cristel is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with cristel.  If not, see <http://www.gnu.org/licenses/>.
 */
/** SSql Class
 * simple class to do db stuff with sqlite3 databases
 */

class SSql extends Base
{
  private $db=false;
  private $connected=false;
  private $rs=false;
  private $sqlerror=array();

  public function __construct($fqfn=false,$logg=false)/*{{{*/
  {
    parent::__construct($logg);
    $this->resetErrors();
    if(false!==$fqfn){
      if(file_exists($fqfn)){
        try {
          $this->db=new SQLite3($fqfn);
          $this->connected=true;
          $this->debug("opened db file $fqfn");
        }catch (Exception $e){
          $this->error($e->getMessage());
        }
      }else{
        $this->error("DB file does not exist: $fqfn");
      }
    }
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
    if($this->amOK()){
      $this->db->close();
      $this->connected=false;
    }
  }/*}}}*/
  private function resetErrors()/*{{{*/
  {
    $this->sqlerror=array(
      "errno"=>0,
      "error"=>""
    );
  }/*}}}*/
  public function amOK()/*{{{*/
  {
    if($this->connected){
      if($this->sqlerror["errno"]==0){
        return true;
      }
    }
    return false;
  }/*}}}*/
  public function getErrors()/*{{{*/
  {
    return $this->sqlerror;
  }/*}}}*/
  public function query($sql)/*{{{*/
  {
    $this->rs=false;
    if($this->amOK() && $this->ValidStr($sql)){
      $this->debug("Query: $sql");
      try{
        $this->rs=$this->db->query($sql);
      }catch (Exception $e){
        $this->sqlerror["errno"]=1;
        $this->sqlerror["error"]=$e->getMessage();
        $this->error("Query Error: " . $this->sqlerror["error"]);
        $this->error("Query: $sql");
      }
    }else{
      $this->warning("Not connected or invalid query string");
      $tmp=print_r($sql,true);
      $this->warning("print_r(\$sql): $tmp");
    }
    return $this->rs;
  }/*}}}*/
  public function insertQuery($sql="") // {{{
  {
    /*
     * returns insert id or false for insert queries
     */
    $this->debug("insert query: $sql");
    $ret=$this->query($sql);
    if($ret){
      $ret=$this->db->lastInsertRowID();
      $this->debug("insert id: $ret");
    }else{
      $this->warning("insert query ($sql) failed.");
    }
    return $ret;
  } // }}}
  public function updateQuery($sql="")/*{{{*/
  {
    /*
     * returns the number of rows updated or false for update queries
     */
    $ret=$this->query($sql);
    if($ret){
      $ret=$this->db->changes();
    }
    return $ret;
  }/*}}}*/
  public function deleteQuery($sql="")/*{{{*/
  {
    /*
     * returns the number of rows deleted or false for delete queries
     */
    $ret=$this->query($sql);
    if($ret){
      $ret=$this->db->changes();
    }
    return $ret;
  }/*}}}*/
  public function arrayQuery($sql="") // {{{
  {
    $ret=false;
    $this->query($sql);
    if(false!==$this->rs){
      $ret=array();
      while($arr=$this->rs->fetchArray(SQLITE3_ASSOC)){
        $ret[]=$arr;
      }
    }else{
      $this->warning("arrayQuery: recordset is false");
    }
    return $ret;
  } // }}}
  public function escape($str="")/*{{{*/
  {
    return sqlite_escape_string($str);
  }/*}}}*/
  public function makeFieldString($val)/*{{{*/
  {
    if(false!==($cn=$this->ValidString($val))){
      $op="'" . $this->escape($val) . "'";
    }else{
      $op=$val;
    }
    return $op;
  }/*}}}*/
  public function makeFindString($val)/*{{{*/
  {
    $op=false;
    if(false!==($cn=$this->ValidString($val))){
      $op="'%" . $this->escape($val) . "%'";
    }
    return $op;
  }/*}}}*/
  public function insertCheck($table,$fields,$extrainsert=false)/*{{{*/
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
    $rarr=$this->arrayQuery($sql);
    if(false!==($cn=$this->ValidArray($rarr)) && $cn>0){
      // record already exists
      $this->debug("record already exists ($sql)");
      $ret=true;
    }else{
      $this->debug("record doesn't exist, inserting :$sqli:");
      if(false!==$extrainsert){
        if(is_array($extrainsert)){
          foreach($extrainsert as $field=>$value){
            $tmp=$this->makeFieldString($value);
            $fs.="," . $field;
            $vs.="," . $tmp;
          }
        }
      }
      $sqli="insert into $table ($fs) values ($vs)";
      $iid=$this->insertQuery($sqli);
      $ret=$iid;
    }
    return $ret;
  }/*}}}*/
}

?>
