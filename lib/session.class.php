<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Sunday 26 March 2017, 16:51:58
 * Last Modified: Monday  3 April 2017, 13:45:12
 *
 * session.class.php
 *
 * Copyright © 2017 Chris Allison <chris.charles.allison+vh@gmail.com>
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

require_once "data.class.php";

class Session extends Data
{
  private $expired=true;
  private $sesslen=1200;

  public function __construct($logg=false,$db=false,$id=false)/*{{{*/
  {
    parent::__construct($logg,$db,"session","id",$id);
    if($this->id){
      $now=time();
      $then=intval($this->getField("expires"));
      $this->expired=$now>$then?true:false;
      if(! $this->expired){
        $this->updateExpires();
      }
    }
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
  }/*}}}*/
  public function amOK()/*{{{*/
  {
    return ! $this->expired;
  }/*}}}*/
  public function setUser($userid,$uuid)/*{{{*/
  {
    if(false!==($tmp=$this->ValidString($userid))){
      if(false!==($tmp=$this->ValidString($uuid))){
        $this->setDataA(array("userid"=>$userid,"uuid"=>$uuid));
      }
    }
  }/*}}}*/
  private function updateExpires()/*{{{*/
  {
    $then=$this->sesslen+time();
    $this->setData("expires",$then);
  }/*}}}*/
}
