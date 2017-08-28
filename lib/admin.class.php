<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Sunday 20 August 2017, 05:45:43
 * Last Modified: Monday 28 August 2017, 12:23:20
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
  private $hall=false;
  private $roomids=false;

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
  public function processAdminLogin($hall,$bookingid=0)/*{{{*/
  {
    $undo=false;
    $hallname=$hall->getField("name");
    $this->info("Processing ADMIN request for hall $hallname");
    $pre=new PreBooking($this->logg,$this->db,$this->admin);
    $valid=$pre->validateAdminGuuid();
    switch($valid){
    case 0:
      /* validated */
      if($bookingid>0){
        if(0!==($undo=$this->getDefaultInt("UNDO",0))){
          $this->info("Undoing last Payment action");
          $b=new Booking($this->logg,$this->db,array("id"=>"$bookingid"));
          if(false===($junk=$b->unPayBooking())){
            $this->warning("failed to undo payment for bookingid: $bookingid");
          }else{
            $this->info("Payment UNDONE OK for bookingid: $bookingid");
            $undo=false;
          }
        }else{
          $this->info("Processing Payment for booking id: $bookingid");
          $b=new Booking($this->logg,$this->db,array("id"=>"$bookingid"));
          if(false===($junk=$b->payBooking())){
            $this->warning("failed to update payment for bookingid: $bookingid");
          }else{
            $this->info("Payment processed OK for bookingid: $bookingid");
            $undo=array("bookingid"=>$bookingid);
          }
        }
      }
      return $this->adminPage($hall,$undo);
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
  public function adminPage($hall,$undo=false)/*{{{*/
  {
    $op="<p>No bookings found</p>\n";
    $str=$oop=$cop=$rows="";
    if($this->setHallObject($hall)){
      $this->archiveBookings();
      $tableheadrow=$this->makeAdminTableHead();
      if(false!==($outstanding=$this->getBookingsList())){
        $tag=new Tag("h5","Outstanding Bookings");
        $top=$tag->makeTag();
        foreach($outstanding as $barr){
          $rows.=$this->makeAdminRow($barr);
        }
        if(false!==$undo){
          $rows.=$this->makeUndoRow($undo);
        }
        $tag=new Tag("table",$tableheadrow . $rows,array("border"=>1));
        $top.=$tag->makeTag();
        $tag=new Tag("div",$top);
        $oop=$tag->makeTag();
      }
      $rows="";
      if(false!==($confirmed=$this->getBookingsList(true))){
        $tag=new Tag("h5","Confirmed Bookings");
        $top=$tag->makeTag();
        foreach($confirmed as $barr){
          $rows.=$this->makeAdminRow($barr);
        }
        $tag=new Tag("table",$tableheadrow . $rows,array("border"=>1));
        $top.=$tag->makeTag();
        $tag=new Tag("div",$top);
        $cop=$tag->makeTag();
      }
      $str=$oop . $cop;
      if(strlen($str)){
        $op=$str;
      }
    }else{
      $this->warning("Hall object passed to adminPage is not a Hall, it is " . gettype($hall) . " of value: " . print_r($hall,true));
    }
    return $op;
  }/*}}}*/
  private function generateRoomSubselect()/*{{{*/
  {
    $ret=false;
    if(false!==$this->roomids){
      $subsel="";
      foreach($this->roomids as $rid){
        if(strlen($subsel)){
          $subsel.="," . $rid;
        }else{
          $subsel="$rid";
        }
      }
      $ret=$subsel;
    }else{
      $this->warning("roomids are not set for adminPage");
    }
    return $ret;
  }/*}}}*/
  private function setHallObject($hall)/*{{{*/
  {
    $ret=false;
    if(is_object($hall) && get_class($hall)=="Hall"){
      $this->hall=$hall;
      $this->roomids=$this->hall->getRoomIds();
      $ret=true;
    }
    return $ret;
  }/*}}}*/
  private function getBookingsList($fullypaid=false)/*{{{*/
  {
    $ret=false;
    if(false!==($subselect=$this->generateRoomSubselect())){
      $sql="select * from booking where roomid in ($subselect)";
      if($fullypaid){
        $sql.=" and status=1";
      }else{
        $sql.=" and status>1";
      }
      $sql.=" order by date asc";
      $ret=$this->db->arrayQuery($sql);
    }
    return $ret;
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
  private function makeUndoRow($undo)/*{{{*/
  {
    $ip=new InputField();
    $hidden=$ip->Hidden("y",$this->admin);
    $ip=new InputField();
    $hidden.=$ip->Hidden("bookingid",$undo["bookingid"]);
    $ip=new InputField();
    $hidden.=$ip->Hidden("UNDO",1);
    $ip=new InputField();
    $txt=$ip->Submit("undo","Undo Last Action");
    $arr=array("action"=>$_SERVER['PHP_SELF'],"method"=>"POST");
    $tag=new Tag("form",$hidden . $txt,$arr);
    $txt=$tag->makeTag();
    $tag=new Tag("td",$txt,array("colspan"=>5,"align"=>"right"));
    return $tag->makeTag();
  }/*}}}*/
  private function makeAdminRow($barr)/*{{{*/
  {
    $r=new Room($this->logg,$this->db,$barr["roomid"]);
    $u=new User($this->logg,$this->db,$barr["userid"]);
    $row=$this->makeTD($this->stringDate($barr["date"]) . " at " . $this->stringTime($barr["date"]) . " for " . $this->secToHMSString($barr["length"]));
    $row.=$this->makeTD($u->getName());
    $row.=$this->makeTD($u->getEmail());
    $row.=$this->makeTD($r->getName());
    $row.=$this->makeTD($this->makeStatusForm($barr));
    $tag=new Tag("tr",$row);
    return $tag->makeTag();
  }/*}}}*/
  private function makeStatusForm($barr)/*{{{*/
  {
    $ip=new InputField();
    $hidden=$ip->Hidden("y",$this->admin);
    $ip=new InputField();
    $hidden.=$ip->Hidden("bookingid",$barr["id"]);
    switch($barr["status"]){
    case 1:
      $txt="Fully Paid";
      break;
    case 2:
      $ip=new InputField();
      $txt=$ip->Submit("pay","Pay Full Amount");
      break;
    case 3:
      $ip=new InputField();
      $txt=$ip->Submit("pay","Pay Deposit");
      break;
    default:
      $txt="Unknown";
      break;
    }
    $arr=array("action"=>$_SERVER['PHP_SELF'],"method"=>"POST");
    $tag=new Tag("form",$hidden . $txt,$arr);
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
