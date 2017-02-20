<?PHP
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * form.class.php
 *
 * Started: Sunday 19 February 2017, 08:29:43
 * Last Modified: Sunday 19 February 2017, 08:51:50
 *
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
require_once "HTML/class.table.php";
require_once "HTML/input_field.class.php";
class Form extends Base
{
  private $da;
  private $labelCell;
  private $inputCell;

  private $title;

  private $withjavascript;
  private $withcolour;
  private $formname;

  public function __construct($action="",$method="POST",$numcols=2,$title="",$withcolour=false,$withjscript=false,$enctype=false,$name="")/*{{{*/
  {
    $logg=false;
    parent::__construct($logg);
    if($this->ValidStr($action)){
      $this->setAction($action);
    }else{
      $this->setAction($_SERVER["PHP_SELF"]);
    }
    $this->setMethod($method);
    $this->setNumCols($numcols);
    $this->setTitle($title);
    $this->labelCell=array("class"=>"formright");
    $this->inputCell=array("class"=>"formleft");
    $this->withjavascript=$withjscript;
    $this->withcolour=$withcolour;
    $this->setEnctype($enctype);
    $this->setName($name);
  }/*}}}*/
    public function __destruct()/*{{{*/
    {
        parent::__destruct();
    }/*}}}*/
  public function setName($name="")/*{{{*/
  {
    if(is_string($name) && $name){
      $this->formname=$name;
    }else{
      $this->formname="thisForm";
    }
  }/*}}}*/
  public function setEnctype($enctype)/*{{{*/
  {
    $this->da["enctype"]=$enctype;
  }/*}}}*/
  public function getEnctype()/*{{{*/
  {
    $op="";
    if($this->da["enctype"]){
      $op=" enctype='";
      $op.=$this->da["enctype"];
      $op.="'";
    }
    return $op;
  }/*}}}*/
  public function setTitle($title)/*{{{*/
  {
    if(strlen($title))
    {
      $this->title=$title;
      $row=$this->da["table"]->AddRow();
      if($this->withcolour){
        $this->da["table"]->SetFancyRowStyle($row,array("bgcolor"=>HEADCOLOUR));
      }
      $this->da["table"]->SetCellAttributes($row,1,
        array("colspan"=>$this->da["numcols"],"class"=>"formtitle"));
      $this->da["table"]->SetCellContent($row,1,$this->title);
    }
  }/*}}}*/
  public function setAction($action)/*{{{*/
  {
    $this->da["action"]=(strlen($action) ? $action : $_SERVER["PHP_SELF"]);
  }/*}}}*/
  public function getAction()/*{{{*/
  {
    return "Action='" . $this->da["action"] . "'";
  }/*}}}*/
  public function setMethod($method)/*{{{*/
  {
    $this->da["method"]=(strlen($method) ? $method : "POST");
  }/*}}}*/
  public function getMethod()/*{{{*/
  {
    return "Method='" . $this->da["method"] . "'";
  }/*}}}*/
  public function getNumCols()/*{{{*/
  {
    return $this->da["numcols"];
  }/*}}}*/
  public function setNumCols($numcols)/*{{{*/
  {
    $this->da["numcols"]=$numcols;
    $this->da["table"]=new Table();
  }/*}}}*/
  public function addTableRow()/*{{{*/
  {
    $row=$this->da["table"]->AddRow();
    $this->da["table"]->SetCellAttributes($row,1,$this->labelCell);
    $this->da["table"]->SetCellAttributes($row,2,$this->inputCell);
    return $row;
  }/*}}}*/
  public function addRow($label,$type="text",$name="",$value="",$size="",$ext=false,$right_align=false)/*{{{*/
  {
    $row=$this->addTableRow();
    $this->da["table"]->SetCellContent($row,1,$label);
    if($right_align)
    {
      $this->da["table"]->SetCellAttribute($row,2,"class","formright");
    }
    if($type!="direct")
    {
      $ip=new InputField($type,$name,$value,$ext,"",$right_align);
      if(strlen($size))
      {
        $ip->setSize($size);
      }
      if($type=="textarea" && strlen($size))
      {
        $ip->da["cols"]=$size;
        $ip->da["rows"]="20";
      }
      $tmp=$ip->getField();
    }else{
      $tmp=$name;
    }

    $this->da["table"]->SetCellContent($row,2,$tmp);
  }/*}}}*/
  public function addHidden($hid)/*{{{*/
  {
    if(isset($this->da["hidden"]) && strlen($this->da["hidden"]))
    {
      $this->da["hidden"].=$hid;
    }else{
      $this->da["hidden"]=$hid;
    }
  }/*}}}*/
  public function addHid($name,$value)/*{{{*/
  {
    $ip=new InputField();
    $this->addHidden($ip->Hidden($name,$value));
  }/*}}}*/
  public function addHidA($hid_arr="")/*{{{*/
  {
    if(is_array($hid_arr))
    {
      reset($hid_arr);
      while(list($k,$v)=each($hid_arr))
      {
        $this->addHid($k,$v);
      }
    }
  }/*}}}*/
  public function makeForm()/*{{{*/
  {
    $op="<form name='" . $this->formname . "' ";
    if($this->da["enctype"]){
      $op.=$this->getEnctype() . " ";
    }
    $op.=$this->getAction() . " ";
    $op.=$this->getMethod();
    if($this->withjavascript){
      $op.=" onfocus='rcvFocus(event.srcElement);' onclick='rcvFocus(event.srcElement);'";
    }
    $op.=">\n";
    if(isset($this->da["hidden"])){
      $op.=$this->da["hidden"];
    }
    if($this->withcolour){
      if(!defined("ROW1COLOUR")){
        define("ROW1COLOUR","#ccc");
      }
      if(!defined("ROW2COLOUR")){
        define("ROW2COLOUR","#ddd");
      }
      if(strlen($this->title))
      {
        $this->da["table"]->Set2RowColors(ROW1COLOUR,ROW2COLOUR,2);
      }else{
        $this->da["table"]->Set2RowColors(ROW1COLOUR,ROW2COLOUR,1);
      }
    }
    $op.=$this->da["table"]->CompileTable();
    $op.="</form>\n";
    return $op;
  }/*}}}*/
  public function arrayToForm($arr,$withsubmit=true,$submitvalue="Save",$submitname="submit")/*{{{*/
  {
    if(is_array($arr)){
      if($arrcount=count($arr)){
        reset($arr);
        while(list($arrkey,$arrval)=each($arr) ){
          $this->addRow($arrkey,"text",$arrkey,$arrval);
        }
        if($withsubmit){
          $this->addRow("","submit",$submitname,$submitvalue);
        }
      }
    }
  }/*}}}*/
  public function fileUploadForm($maxfilesize=1000000000,$label="Choose a file to upload",$button="Upload File",$withname=false,$withdesc=false)/*{{{*/
  {
    $this->setEnctype("multipart/form-data");
    $this->addHid("MAX_FILE_SIZE",$maxfilesize);
    if(is_string($withname)){
      $this->addRow($withname,"text","filetitle");
    }
    if(is_string($withdesc)){
      $this->addRow($withdesc,"text","filedesc","",50);
    }
    $this->addRow($label,"file","uploadedfile");
    $this->addRow("","submit","submit",$button);
    return $this->makeForm();
  }/*}}}*/
}
