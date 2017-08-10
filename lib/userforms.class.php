<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * Started: Sunday 16 April 2017, 09:32:28
 * Last Modified: Thursday 10 August 2017, 09:57:18
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
require_once "base.class.php";
require_once "HTML/form.class.php";
require_once "HTML/tag.class.php";
require_once "HTML/select_field.class.php";
require_once "HTML/option_field.class.php";
require_once "HTML/class.table.php";
require_once "room.class.php";

class UForms extends Base
{
  private $logg=false;
  private $db=false;
  private $year=0;
  private $month=0;
  private $day=0;
  private $hour=0;
  private $roomid=0;
  private $room=false;

  public function __construct($logg=false,$db=false)/*{{{*/
  {
    parent::__construct($logg);
    $this->logg=$logg;
    $this->db=$db;
    $this->year=$this->getDefaultInt("year",0);
    $this->month=$this->getDefaultInt("month",0);
    $this->day=$this->getDefaultInt("day",0);
    $this->hour=$this->getDefaultInt("start",0);
    $this->roomid=$this->getDefaultInt("roomid",0);
    $this->room=new Room($this->logg,$this->db,$this->roomid);
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    parent::__destruct();
  }/*}}}*/
  public function userLoginForm($atts=false)/*{{{*/
  {
    $f=new Form();
    $f->addHidA($atts);
    $f->addRow("Email Address","text","emailaddress");
    $f->addSubmit("submitemail","Send Email");
    $tag=new Tag("p","Please type your email address.  We will send you a link to log you into the site.",array("class"=>"bodypara"));
    $p=$tag->makeTag();
    $tag=new Tag("div",$p . $f->makeForm(),array("class"=>"formdiv"));
    return $tag->makeTag();
  }/*}}}*/
  public function userRegisterForm($fields,$atts=false)/*{{{*/
  {
    $tfields=array();
    /* uppercase the first letter of each field name
     * and set an empty value */
    foreach($fields as $k){
      $tfields[ucfirst($k)]="";
    }
    $f=new Form();
    $f->addHidA($atts);
    $f->arrayToForm($tfields,true,"Register","register");
    $tag=new Tag("p","Please fill in the form.  We will email you a link to log you into the site.",array("class"=>"bodypara"));
    $p=$tag->makeTag();
    $tag=new Tag("div",$p . $f->makeForm(),array("class"=>"formdiv"));
    return $tag->makeTag();
  }/*}}}*/
  public function preBookingForm()/*{{{*/
  {
    $attrow=array("class"=>"row");
    $attcol8=array("class"=>"col-8");
    $this->debug("in preBookingForm y,m,d:" . $this->year . "," . $this->month . "," . $this->day . " hour:" . $this->hour . ", room:" . $this->room->getName());
    $tm=mktime(0,0,0,$this->month,$this->day,$this->year);
    $heading="Booking the " . $this->room->getName() . " on " . $this->stringDate($tm);
    $rows=$this->subheadingRow($heading);
    /* $rows.=$this->bookingTimeSelector($this->hour); */
    $rows.=$this->bookingTimeTable($this->hour);
    $rows.=$this->hiddenFieldsRow(array("a"=>2));
    $rows.=$this->blankRow();
    $rows.=$this->emailRow("useremailaddress");
    $rows.=$this->blankRow();
    $rows.=$this->submitRow("Book " . $this->room->getName());
    $rows.=$this->blankRow();
    $fdiv=new Tag("form",$rows,array("name"=>"prebookingForm","Action"=>$_SERVER['PHP_SELF'],"Method"=>"POST"));
    $f=$fdiv->makeTag();
    $div=new Tag("div",$f,array("class"=>"prebookingformdiv"));
    return $div->makeTag();
  }/*}}}*/
  private function subheadingRow($heading,$level=5,$width=array("class"=>"col-8"))/*{{{*/
  {
    $attrow=array("class"=>"row");
    $htag=new Tag("h$level",$heading);
    $hdiv=new Tag("div",$htag->makeTag(),$width);
    $rdiv=new Tag("div",$hdiv->makeTag(),$attrow);
    return $rdiv->makeTag();
  }/*}}}*/
  private function hiddenFieldsRow($hidarray)/*{{{*/
  {
    $attrow=array("class"=>"row");
    $op="";
    $sop="";
    if($this->ValidArray($hidarray)){
      foreach($hidarray as $k=>$v){
        $hid=new Tag("input","",array("Type"=>"hidden","name"=>$k,"value"=>$v),true,false,true);
        $sop.=$hid->makeTag() . PHP_EOL;
      }
      $rdiv=new Tag("div",$sop,$attrow);
      $op=$rdiv->makeTag();
    }
    return $op;
  }/*}}}*/
  private function submitRow($buttontext,$name="bookingsubmit")/*{{{*/
  {
    $attrow=array("class"=>"row");
    $attcol8=array("class"=>"col-8");
    $submit=new Tag("input","",array("type"=>"submit","name"=>$name,"value"=>$buttontext),true,false,true);
    $d=new Tag("div",$submit->makeTag(),$attcol8);
    $sop=$d->makeTag();
    $rdiv=new Tag("div",$sop,$attrow);
    return $rdiv->makeTag();
  }/*}}}*/
  private function emailRow($name="useremailaddress")/*{{{*/
  {
    $attrow=array("class"=>"row");
    $attcol8=array("class"=>"col-8");
    $attcol12=array("class"=>"col-12");
    $em=new InputField();
    $d=new Tag("div",$em->Text($name,"",20,"Email Address"),$attcol12);
    $sop=$d->makeTag();
    $rdiv=new Tag("div",$sop,$attrow);
    return $rdiv->makeTag();
  }/*}}}*/
  private function blankRow()/*{{{*/
  {
    $attrow=array("class"=>"row");
    $attcol12=array("class"=>"col-12");
    $d=new Tag("div","&nbsp;",$attcol12);
    $rdiv=new tag("div",$d->makeTag(),$attrow);
    return $rdiv->makeTag();
  }/*}}}*/
  private function xbookingTimeSelector($hour=8)/*{{{*/
  {
    $table=new Table();
    $row=$table->AddRow();
    $table->SetCellContent($row,1,"Start Time");
    $table->SetCellColSpan($row,1,2);
    $table->SetCellContent($row,3,"");
    $table->SetCellContent($row,4,"End Time");
    $table->SetCellColSpan($row,4,2);
    $row=$table->AddRow();
    $table->SetCellContent($row,1,"hour");
    $table->SetCellContent($row,2,"min.");
    $table->SetCellContent($row,3,"");
    $table->SetCellContent($row,4,"hour");
    $table->SetCellContent($row,5,"min.");
    $row=$table->AddRow();
    $hsel=new SelectField("starthour");
    $table->SetCellContent($row,1,$hsel->hourSelector($hour));
    $msel=new SelectField("startmin");
    $table->SetCellContent($row,2,$msel->minuteSelector(0,15,true));
    $table->SetCellContent($row,3,"");
    $hsel=new SelectField("endhour");
    $table->SetCellContent($row,4,$hsel->hourSelector($hour+2));
    $msel=new SelectField("endmin");
    $table->SetCellContent($row,5,$msel->minuteSelector(0,15,true));
    return $table->CompileTable();
  }/*}}}*/
  private function bookingTimeTable($hour=8)/*{{{*/
  {
    $btthead=array("class"=>"bttheadcell");
    $btthead2=array("class"=>"bttheadcell","colspan"=>2);
    $bttcell=array("class"=>"bttcell");
    $btthrow=array("class"=>"bttheadrow");
    $bttrow=array("class"=>"bttrow");
    $th=new Tag("th","Start Time",$btthead2);
    $row=$th->makeTag();
    $th=new Tag("th","End Time",$btthead2);
    $row.=$th->makeTag();
    $tr=new Tag("tr",$row,$btthrow);
    $rows=$tr->makeTag();
    $th=new Tag("td","Hour",$btthead);
    $row=$th->makeTag();
    $td=new Tag("td","Min.",$btthead);
    $row.=$td->makeTag();
    $td=new Tag("td","Hour",$btthead);
    $row.=$td->makeTag();
    $td=new Tag("td","Min.",$btthead);
    $row.=$td->makeTag();
    $tr=new Tag("tr",$row,$btthrow);
    $rows.=$tr->makeTag();
    $sel=new SelectField("starthour");
    $starthour=$sel->hourSelector($hour);
    $td=new Tag("td",$starthour,$bttcell);
    $row=$td->makeTag();
    $sel=new SelectField("startmin");
    $startmin=$sel->minuteSelector(0,15,true);
    $td=new Tag("td",$startmin,$bttcell);
    $row.=$td->makeTag();
    $sel=new SelectField("endhour");
    $endhour=$sel->hourSelector($hour+2,true,$hour);
    $td=new Tag("td",$endhour,$bttcell);
    $row.=$td->makeTag();
    $sel=new SelectField("endmin");
    $endmin=$sel->minuteSelector(0,15,true);
    $td=new Tag("td",$endmin,$bttcell);
    $row.=$td->makeTag();
    $tr=new Tag("tr",$row,$btthrow);
    $rows.=$tr->makeTag();
    $tt=new Tag("table",$rows,array("class"=>"btttable"));
    return $tt->makeTag();
  }/*}}}*/
  private function bookingTimeSelector($hour=8) /*{{{*/
  {
    $attcol1=array("class"=>"col-1");
    $attcol2=array("class"=>"col-2");
    $attrow=array("class"=>"row");
    $headcelll=new Tag("div","Start Time",$attcol2);
    $headcellr=new Tag("div","End Time",$attcol2);
    $d=new Tag("div",$headcelll->makeTag() . $headcellr->makeTag(),$attrow);
    $rows=$d->makeTag();
    $uheadcelll=new Tag("div","Hour",$attcol1);
    $uheadcellr=new Tag("div","Min.",$attcol1);
    $subrow=$uheadcelll->makeTag() . $uheadcellr->makeTag();
    $d=new Tag("div",$subrow . $subrow,$attrow);
    $rows.=$d->makeTag();
    $sel=new SelectField("starthour");
    $starthour=$sel->hourSelector($hour);
    $sd=new Tag("div",$starthour,$attcol1);
    $row=$sd->makeTag();
    $sel=new SelectField("startmin");
    $startmin=$sel->minuteSelector(0,15,true);
    $sd=new Tag("div",$startmin,$attcol1);
    $row.=$sd->makeTag();
    $sel=new SelectField("endhour");
    $endhour=$sel->hourSelector($hour+2);
    $sd=new Tag("div",$endhour,$attcol1);
    $row.=$sd->makeTag();
    $sel=new SelectField("endmin");
    $endmin=$sel->minuteSelector(0,15,true);
    $sd=new Tag("div",$endmin,$attcol1);
    $row.=$sd->makeTag();
    $d=new Tag("div",$row,$attrow);
    $rows.=$d->makeTag();
    return $rows;
  } /*}}}*/
  private function startTimeSelector($heading="Booking Start Time",$withzero=true,$hour=8)/*{{{*/
  {
    $rows="";
    $cell=new Tag("th",$heading,array("colspan"=>2,"class"=>"timeselectorheadcell"));
    $row=new Tag("tr",$cell->makeTag(),array("class"=>"timeselectorheadrow"));
    $rows.=$row->makeTag();
    $cell=new Tag("th","Hour",array("class"=>"timeselectorheadcell"));
    $row=$cell->makeTag();
    $cell=new Tag("th","Min.",array("class"=>"timeselectorheadcell"));
    $row.=$cell->makeTag();
    $how=new Tag("tr",$row,array("class"=>"timeselectorheadrow"));
    $rows.=$how->makeTag();
    $hsel=new SelectField("starthour");
    $ctxt=$hsel->hourSelector($hour,$withzero);
    $cell=new Tag("td",$ctxt,array("class"=>"timeselectorcell"));
    $row=$cell->makeTag();
    $msel=new SelectField("startmin");
    $ctxt=$msel->minuteSelector(0,15,$withzero);
    $cell=new Tag("td",$ctxt,array("class"=>"timeselectorcell"));
    $row.=$cell->makeTag();
    $how=new tag("tr",$row,array("class"=>"timeselectorrow"));
    $rows.=$how->makeTag();
    $tab=new Tag("table",$rows,array("class"=>"timeselectortable"));
    return $tab->makeTag();
  }/*}}}*/
  private function endTimeSelector($heading="Booking End Time",$withzero=true,$hour=8)/*{{{*/
  {
    $rows="";
    $cell=new Tag("th",$heading,array("colspan"=>2,"class"=>"timeselectorheadcell"));
    $row=new Tag("tr",$cell->makeTag(),array("class"=>"timeselectorheadrow"));
    $rows.=$row->makeTag();
    $cell=new Tag("th","Hour",array("class"=>"timeselectorheadcell"));
    $row=$cell->makeTag();
    $cell=new Tag("th","Min.",array("class"=>"timeselectorheadcell"));
    $row.=$cell->makeTag();
    $how=new Tag("tr",$row,array("class"=>"timeselectorheadrow"));
    $rows.=$how->makeTag();
    $hsel=new SelectField("starthour");
    $ctxt=$hsel->hourSelector($hour,$withzero);
    $cell=new Tag("td",$ctxt,array("class"=>"timeselectorcell"));
    $row=$cell->makeTag();
    $msel=new SelectField("startmin");
    $ctxt=$msel->minuteSelector(0,15,$withzero);
    $cell=new Tag("td",$ctxt,array("class"=>"timeselectorcell"));
    $row.=$cell->makeTag();
    $how=new tag("tr",$row,array("class"=>"timeselectorrow"));
    $rows.=$how->makeTag();
    $tab=new Tag("table",$rows,array("class"=>"timeselectortable"));
    return $tab->makeTag();
  }/*}}}*/
}
?>
