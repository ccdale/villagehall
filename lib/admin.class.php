<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Sunday 20 August 2017, 05:45:43
 * Last Modified: Sunday 20 August 2017, 19:50:11
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

class Admin extends Base
{
  protected $logg=false;
  protected $db=false;
  private $admin=false;

  public function __construct($logg=false,$db=false,$admin=false)/*{{{*/
  {
    parent::__construct($logg);
    $this->logg=$logg;
    $this->db=$db;
    $this->admin=$admin;
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
  }/*}}}*/
  public function initSendEmail($hall)/*{{{*/
  {
    $ret=false;
    $hallname=$hall->getField("name");
    $this->info("Init ADMIN request for hall $hallname");
    $pv=new Priv($this->logg,$this->db);
    if(false!==($junk=$pv->selectByHallAdmin($hall->id))){
      $u=new User($this->logg,$this->db,$pv->getField("userid"));
      $this->info("ADMIN userid: " . $u->id);
      $email=$u->getField("email");
      $guuid=$u->createGuid();
      if(false!==($junk=$this->ValidString($email))){
        $this->info("ADMIN email: $email");
        $hallservername=$hall->getField("servername");
        $link="https://$hallservername.vhall.uk/index.php?y=" . urlencode($guuid);
        if(mail($email,"$hallname ADMIN login request",$link)){
          $ret=true;
        }else{
          $this->warning("Failed to send init email for hall $hallname to $email");
        }
      }else{
        $this->warning("Failed to find ADMIN email address for hall $hallname");
      }
    }else{
      $this->warning("Failed to find ADMIN PRIV for hall $hallname");
    }
    return $ret;
  }/*}}}*/
}
?>
