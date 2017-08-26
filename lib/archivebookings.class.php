<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Saturday 26 August 2017, 16:27:41
 * Last Modified: Saturday 26 August 2017, 16:39:37
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

class ArchiveBooking extends Data
{
  protected $logg=false;
  protected $db=false;

  public function __construct($logg=false,$db=false,$dataid=false)/*{{{*/
  {
    if($this->ValidInt($dataid)){
      parent::__construct($logg,$db,"archivebooking","id",$dataid);
    }else{
      parent::__construct($logg,$db,"archivebooking");
    }
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
  }/*}}}*/
  public function moveBookingIdToArchive($bid)/*{{{*/
  {
    $ret=false;
    if($this->ValidInt($bid)){
      $b=new Booking($this->logg,$this->db,array("id"=>$bid));
      if(false!==($da=$b->getDataA())){
        $this->setDataA($da);
        $b->deleteMe();
        $ret=true;
      }
    }
    return $ret;
  }/*}}}*/
}
?>
