<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Sunday 20 August 2017, 19:16:22
 * Last Modified: Sunday 20 August 2017, 19:33:11
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

class Priv extends Data
{
  protected $logg=false;
  protected $db=false;

  public function __construct($logg=false,$db=false,$privid=false)/*{{{*/
  {
    parent::__construct($logg,$db,"privs","id",$privid);
    $this->logg=$logg;
    $this->db=$db;
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
  }/*}}}*/
  public function selectByHallAdmin($hallid)/*{{{*/
  {
    $ret=false;
    if(false!==($junk=$this->ValidInt($hallid))){
      $sql="select * from privs where hallid=$hallid and level=99";
      if(false!==($arr=$this->db->arrayQuery($sql))){
        if(false!==($junk=$this->ValidArray($arr))){
          $this->id=$arr[0]["id"];
          unset($arr[0]["id"]);
          $this->setDataA($arr[0]);
          $ret=true;
        }
      }
    }
    return $ret;
  }/*}}}*/
}
?>
