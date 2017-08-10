<?php
/*
* select_field.class.php
* Last Modified: Thursday 10 August 2017, 09:57:03
*/
require_once "HTML/option_field.class.php";

class SelectField
{
	private $name;
	private $oarr;
  private $autochange;

	public function __construct($name,$withautochange=false)
	{
		$this->setName($name);
		$this->oarr=array();
    $this->autochange=$withautochange;
	}
	public function setName($name)
	{
		$this->name=$name;
	}
	public function addOption($v,$t,$s=false,$with_auto_submit=false)
	{
		$opt=new OptionField($v,$t,$s,$with_auto_submit);
		$this->oarr[]=$opt->makeOption();
		unset($opt);
	}
	public function makeSelect()
	{
    $op="<select name='$this->name'";
    if($this->autochange){
        $op.=" onchange='selectChanged(this.value);'";
    }
    $op.=">\n";
		$c=count($this->oarr);
		for($i=0;$i<$c;$i++)
		{
			$op.=$this->oarr[$i];
		}
		$op.="</select>\n";
		return $op;
	}
  public function hourSelector($selected=8,$withzero=true,$start=0)/*{{{*/
  {
    for($hour=$start;$hour<24;$hour++){
      $shour=$withzero && $hour<10?"0".$hour:$hour;
      $s=$selected==$hour?true:false;
      $this->addOption($hour,$shour,$s,false);
      if($hour>23){
        break;
      }
    }
    return $this->makeSelect();
  }/*}}}*/
  public function minuteSelector($selected=0,$minskip=30,$withzero=true)/*{{{*/
  {
    for($min=0;$min<60;$min=$min+$minskip){
      $smin=$withzero && $min<10?"0".$min:$min;
      $s=$selected==$min?true:false;
      $this->addOption($min,$smin,$s,false);
      if($min>59){
        break;
      }
    }
    return $this->makeSelect();
  }/*}}}*/
	public function letterSelector($pre_sel="0",$auto_submit=false)
	{
	    for($i=65;$i<91;$i++)
	    {
            $s=($pre_sel==chr($i) ? true : false);
			$this->addOption(chr($i),chr($i),$s,$auto_submit);
	    }
	    return $this->makeSelect();
	}
	public function numberSelector($pre_sel="A",$auto_submit=false)
	{
	    for($i=0;$i<10;$i++)
	    {
	        $tmp=$i . "";
	        $s=($pre_sel==$tmp ? true : false);
	        $this->addOption($tmp,$tmp,$s,$auto_submit);
	    }
	    return $this->makeSelect();
	}
}
