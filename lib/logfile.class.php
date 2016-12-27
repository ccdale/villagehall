<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * logfile.class.php
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
 * Started: Tuesday 27 December 2016, 10:21:14
 * Last Modified: Tuesday 15 April 2014, 07:27:19
 */

class LogFile
{
  private $fp=false;
  private $fn=false;
  private $rotate=false;
  private $keep=false;
  private $frequency="daily";
  private $minlevel=LOG_DEBUG;
  private $tracelevel=-1;

  public function __construct($fn,$minlevel=LOG_DEBUG,$rotate=true,$keep=5,$frequency="daily",$tracelevel=-1)/*{{{*/
  {
    $this->minlevel=$minlevel;
    $this->rotate=$rotate;
    $this->keep=$keep;
    $this->frequency=$frequency;
    /*
     * set $tracelevel to 0,1 or 2
     * then, DEBUG level messages will contain the calling
     * stack trace
     * tracelevel=-1: no output at all apart from the actual debug message
     * tracelevel=0: no stack trace
     * tracelevel=1: caller function/class/file/line number
     * tracelevel=2: full stack trace
     */
    $this->tracelevel=$tracelevel;
    $this->fn=$fn;
    $this->fp=fopen($this->fn,"a");
  }/*}}}*/
  public function __destruct()/*{{{*/
  {
    if($this->fp){
      fclose($this->fp);
    }
  }/*}}}*/
  public function message($msg="",$level=LOG_INFO)/*{{{*/
  {
    $this->checkRotate();
    if($this->fp){
      $msg=$this->formatMessage($msg,$level);
      if($level<=$this->minlevel){
        if($level==LOG_DEBUG){
          $trace=debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,0);
          if(false!==$trace){
            $str=$this->formatStackTrace($trace);
            $msg.="\n" . $str;
          }
        }
        $tmp=date("D d H:i:s");
        fwrite($this->fp,$tmp . " " . $msg);
        fflush($this->fp);
      }
    }
  }/*}}}*/
  public function info($msg) // {{{
  {
    $this->message($msg,LOG_INFO);
  } // }}}
  public function notice($msg)/*{{{*/
  {
    $this->message($msg,LOG_NOTICE);
  }/*}}}*/
  public function debug($msg) // {{{
  {
    $this->message($msg,LOG_DEBUG);
  } // }}}
  public function warn($msg) // {{{
  {
    $this->message($msg,LOG_WARNING);
  } // }}}
  public function warning($msg) // {{{
  {
    $this->message($msg,LOG_WARNING);
  } // }}}
  public function error($msg) // {{{
  {
    $this->message($msg,LOG_ERR);
  } // }}}
  private function formatMessage($msg,$level) // {{{
  {
    if(!is_string($msg)){
      $msg=print_r($msg,true);
    }
    if($level==LOG_DEBUG){
      $msg="    " . $msg;
    }
    return $msg;
  } // }}}
  /* pretty print the stack trace */
  private function formatStackTrace($trace)/*{{{*/
  {
    // remove the first 2 entries in the array as they 
    // refer to this file and the base.class.php that
    // called it
    $junk=array_shift($trace);
    $junk=array_shift($trace);
    $caller=array_shift($trace);
    $op="";
    if($this->tracelevel>-1){
      if(isset($caller["function"])){
        $op="In: " . $caller["function"];
        if(isset($caller["class"]) && strlen($caller["class"])){
          $op.=" in class: " . $caller["class"];
        }
        $op.=" Line: " . $caller["line"];
        $op.=" file: " . $caller["file"] . PHP_EOL;
        $cn=count($trace);
        if($cn && $this->tracelevel>1){
          foreach($trace as $k=>$v){
            if(isset($v["class"])){
              $op.="   func: " . $v["function"] . " class: " . $v["class"] . " line: " . $v["line"] . " file: " . $v["file"] . PHP_EOL;
            }else{
              $op.="   func: " . $v["function"] . " line: " . $v["line"] . " file: " . $v["file"] . PHP_EOL;
            }
          }
        }
      }
    }
    return $op;
  }/*}}}*/
  private function checkRotate()/*{{{*/
  {
    $tago=86400;
    if($this->fp){
      $now=time();
      $tfn=filectime($this->fn);
      switch($this->frequency){
      case "monthly":
        $tago=$tago*30;
        break;
      case "weekly":
        $tago=$tago*7;
        break;
      case "hourly":
        $tago=3600;
        break;
      }
      $then=$now-$tago;
      if($tfn<$then){
        $this->rotateFiles();
      }
    }
  }/*}}}*/
  private function rotateFiles()/*{{{*/
  {
    if($this->fp){
      if($this->keep){
        $tmp=date("D d H:i:s");
        fwrite($this->fp,$tmp . " Log file rotation starting.");
        fflush($this->fp);
        fclose($this->fp);
        $xfn=$this->fn . "." . $this->keep;
        if(file_exists($xfn)){
          unlink($xfn);
        }
        for($x=$this->keep-1;$x>0;$x--){
          $dx=$x+1;
          $ofn=$this->fn . "." . $x;
          if(file_exists($ofn)){
            $nfn=$this->fn . "." . $dx;
            rename($ofn,$nfn);
          }
        }
        $this->fp=fopen($this->fn,"w");
        $this->message("Log file rotation completed.",LOG_INFO);
      }
    }
  }/*}}}*/
}
?>
