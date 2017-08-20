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

class User extends Data
{
    public function __construct($logg=false,$db=false,$id=false)/*{{{*/
    {
        parent::__construct($logg,$db,"user","id",$id);
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
    public function createGuid()/*{{{*/
    {
      /* function from http://php.net/manual/en/function.com-create-guid.php
       * only works on linux
       */ 
      $data = openssl_random_pseudo_bytes(16);
      $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
      $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
      return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }/*}}}*/
    public function selectByEmail($emailaddress,$username)/*{{{*/
    {
      if(false!==($id=$this->setFromField("email",$emailaddress))){
        $this->debug("returning user id: " . $this->id . " with name " . $this->getField("name"));
      }else{
        $arr=array("name"=>$username,"email"=>$emailaddress);
        $this->setDataA($arr);
        $this->debug("new user id: " . $this->id);
      }
    }/*}}}*/
}
?>
