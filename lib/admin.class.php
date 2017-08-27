<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Sunday 20 August 2017, 05:45:43
 * Last Modified: Sunday 27 August 2017, 09:06:32
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
    if(false!==($junk=$pv->selectByHallAdmin($hall->getId()))){
      $u=new User($this->logg,$this->db,$pv->getField("userid"));
      $pre=new PreBooking($this->logg,$this->db);
      if(false!==($junk=$pre->setupAdminAccess($u))){
        $email=$u->getField("email");
        $guuid=$pre->getField("guuid");
        $this->info("ADMIN userid: " . $u->getId());
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
      }
    }else{
      $this->warning("Failed to find ADMIN PRIV for hall $hallname");
    }
    return $ret;
  }/*}}}*/
  public function processAdminLogin($hall)/*{{{*/
  {
    $hallname=$hall->getField("name");
    $this->info("Processing ADMIN request for hall $hallname");
    $pre=new PreBooking($this->logg,$this->db,$this->admin);
    $valid=$pre->validateAdminGuuid();
    switch($valid){
    case 0:
      /* validated */
      return $this->adminPage($hall);
      break;
    case -1:
      /* incorrect guuid / no guuid */
      $tag=new Tag("p","Invalid attempt to login as Administrator detected.");
      $this->warning("invalid attempt to login as Admin detected");
      $p=$tag->makeTag();
      $tag=new Tag("div",$p);
      return $tag->makeTag();
      break;
    case 2:
      /* timeout on guuid */
      $tag=new Tag("p","That link has timed out, please use the Admin link below to generate another one.");
      $p=$tag->makeTag();
      $tag=new Tag("div",$p);
      return $tag->makeTag();
      break;
    }
  }/*}}}*/
  public function adminPage($hall)/*{{{*/
  {
    $op="<p>No bookings found</p>\n";
    if(is_object($hall) && get_class($hall)=="Hall"){
      $this->archiveBookings();
      $roomids=$hall->getRoomIds();
      $subselect="";
      foreach($roomids as $rid){
        if(strlen($subselect)){
          $subselect.="," . $rid;
        }else{
          $subselect=$rid;
        }
      }
      $sql="select * from booking where roomid in ($subselect) and status>1 order by date asc";
      if(false!==($arr=$this->db->arrayQuery($sql))){
        $tableheadrow=$this->makeAdminTableHead();
        $rows="";
        foreach($arr as $barr){
          $r=new Room($this->logg,$this->db,$barr["roomid"]);
          $u=new User($this->logg,$this->db,$barr["userid"]);
          $rows.=$this->makeAdminRow(array("id"=>$barr["id"],"name"=>$u->getName(),"email"=>$u->getEmail(),"roomname"=>$r->getName(),"date"=>$this->stringDate($barr["date"]) . " at " . $this->stringTime($barr["date"]) . " for " . $this->secToHMSString($barr["length"]),"status"=>$barr["status"]));
        }
        $tag=new Tag("table",$tableheadrow . $rows,array("border"=>1));
        $op=$tag->makeTag();
      }
    }
    return $op;
  }/*}}}*/
  private function makeAdminTableHead()/*{{{*/
  {
    $row=$this->makeTH("Date");
    $row.=$this->makeTH("Name");
    $row.=$this->makeTH("Email Address");
    $row.=$this->makeTH("Room Name");
    $row.=$this->makeTH("Status");
    $tag=new Tag("tr",$row);
    return $tag->makeTag();
  }/*}}}*/
  private function makeAdminRow($arr)/*{{{*/
  {
    $row=$this->makeTD($arr["date"]);
    $row.=$this->makeTD($arr["name"]);
    $row.=$this->makeTD($arr["email"]);
    $row.=$this->makeTD($arr["roomname"]);
    $row.=$this->makeTD($arr["status"]);
    $tag=new Tag("tr",$row);
    return $tag->makeTag();
  }/*}}}*/
  private function makeTD($str)/*{{{*/
  {
    $tag=new Tag("td",$str);
    return $tag->makeTag();
  }/*}}}*/
  private function makeTH($str)/*{{{*/
  {
    $tag=new Tag("th",$str);
    return $tag->makeTag();
  }/*}}}*/
  private function archiveBookings()/*{{{*/
  {
    $ab=new ArchiveBooking($this->logg,$this->db);
    $ab->archiveOldBookings();
  }/*}}}*/
}
?>
