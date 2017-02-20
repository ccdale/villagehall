<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker:
 *
 * setup-villagehall.php
 *
 * Started: Saturday 18 February 2017, 09:57:27
 * Last Modified: Monday 20 February 2017, 06:13:10
 *
 * Copyright (c) 2017 Chris Allison chris.charles.allison+vh@gmail.com
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

function importLib($libfn,$desc,$log)/*{{{*/
{
    $log->debug("Importing $desc from $libfn");
    require_once $libfn;
}/*}}}*/
function pageForm($pagetitle,$fields,$hidden,$last,$radio)/*{{{*/
{
    $tag=new Tag("title",$pagetitle);
    $title=$tag->makeTag();
    $tag=new Tag("head",$title);
    $head=$tag->makeTag();
    $tag=new Tag("h2",$pagetitle);
    $pagetitle=$tag->makeTag();
    $form=new Form();
    foreach($hidden as $ha){
        $form->addHid($ha["name"],$ha["val"]);
    }
    if(is_array($radio)){
        /* TODO need to add code for radio button handling (db type) */
    }
    foreach($fields as $fa){
        $form->addRow($fa["title"],"text",$fa["name"],$fa["val"]);
    }
    if($last){
        $form->addRow("","submit","correct","Correct");
        $form->addRow("","submit","startagain","Start Again");
    }else{
        $form->addRow("","submit","submit","continue");
    }
    $tmpform=$form->makeForm();
    $tag=new Tag("body",$pagetitle . $tmpform);
    $body=$tag->makeTag();
    $tag=new Tag("html",$head . $body);
    $html=$tag->makeTag();
    return $html;
}/*}}}*/
function validateInputStrings($inputa)/*{{{*/
{
    $ret=false;
    $tmpa=array();
    foreach($inputa as $ip){
        $tmp=GP($ip);
        if(!is_string($tmp)){
            return $ret;
        }
        if(0==($len=strlen($tmp))){
            return $ret;
        }
        $tmpa[]=array("name"=>$ip,"val"=>$tmp);
    }
    return $tmpa;
}/*}}}*/
require_once "logfile.class.php";
$logfilename=$pvpath . DIRECTORY_SEPARATOR . "log" . DIRECTORY_SEPARATOR . $appname . ".log";
$loglevel=LOG_DEBUG;
$logdorotate=false;
$logrotate="hourly";
$logkeep=5;
$logtracelevel=1;
$logg=new LogFile($logfilename,$loglevel,$logdorotate,$logkeep,$logrotate,$logtracelevel);

importLib("www.php","GP funcs",$logg);
importLib("HTML/form.class.php","Form Class",$logg);
importLib("HTML/tag.class.php","TAG class",$logg);

$stage=getDefaultInt("stage",0);

$pagetitle="Village Hall - Setup";
$fields=array();
$hidden=array();

/*
 * at each stage below, should the inputs not validate
 * the previous stage is shown again, because if you don't
 * break out of the switch, it falls through to the 
 * next stage
 */
switch($stage){
case 5: /*{{{*/
    if("Correct"==(GP("correct"))){
        /* all done */
    }else{
        $stage=0;
        $stage+=1;
        $hidden[]=array("name"=>"stage","val"=>$stage);
        $fields[]=array("title"=>"Village Name","name"=>"vname","val"=>"");
        break;
    } /*}}}*/
case 4:
        /* TODO need to add dbtype and if mysql dbname, dbuser and dbpass */
    $stage+=1;
case 3: /*{{{*/
    $iparr=array("auname","aupass","vname","roomscn");
    if(false!==($ipret=validateInputStrings($iparr))){
        // $hidden=$ipret;
        $vname=GP("vname");
        $pagetitle=$vname . " Hall - Setup " . $stage;
        $roomscn=getDefaultInt("roomscn",0);
        if($roomscn>0){
            foreach($ipret as $ipa){
                if($ipa["name"]=="roomscn"){
                    $ipa["title"]="Number of Rooms";
                }elseif($ipa["name"]=="vname"){
                    $ipa["title"]="Hall Name";
                }elseif($ipa["name"]=="aupass"){
                    $ipa["title"]="Admin Password";
                }elseif($ipa["name"]=="auname"){
                    $ipa["title"]="Admin User Name";
                }elseif($ipa["name"]=="dbtype"){
                    $ipa["title"]="DB Type";
                }elseif($ipa["name"]=="dbname"){
                    $ipa["title"]="DB Name";
                }elseif($ipa["name"]=="dbuser"){
                    $ipa["title"]="DB User Name";
                }elseif($ipa["name"]=="dbpass"){
                    $ipa["title"]="DB User Password";
                }
                $fields[]=$ipa;
            }
            $stage+=1;
            $hidden[]=array("name"=>"stage","val"=>$stage);
            break;
        }else{
            $stage=2;
        }
    }else{
        $stage=2;
    }/*}}}*/
case 2: /*{{{*/
    $iparr=array("auname","aupass","vname");
    if(false!==($ipret=validateInputStrings($iparr))){
        $hidden=$ipret;
        $vname=GP("vname");
        $pagetitle=$vname . " Hall - Setup " . $stage;
        $stage+=1;
        $hidden[]=array("name"=>"stage","val"=>$stage);
        $fields[]=array("title"=>"Number of Rooms","name"=>"roomscn","val"=>"");
        break;
    }else{
        $stage=1;
    } /*}}}*/
case 1: /*{{{*/
    $iparr=array("vname");
    if(false!==($ipret=validateInputStrings($iparr))){
        $vname=$ipret[0]["val"];
        $hidden=$ipret;
        $hidden[]=array("name"=>"submit","val"=>GP("submit"));
        $pagetitle=$vname . " Hall - Setup " . $stage;
        $stage+=1;
        $hidden[]=array("name"=>"stage","val"=>$stage);
        $fields[]=array("title"=>"Admin User Name","name"=>"auname","val"=>"");
        $fields[]=array("title"=>"Admin Password","name"=>"aupass","val"=>"");
        break;
    }else{
        $stage=0;
    } /*}}}*/
case 0: /*{{{*/
    $stage+=1;
    $hidden[]=array("name"=>"stage","val"=>$stage);
    $fields[]=array("title"=>"Village Name","name"=>"vname","val"=>"");
    break;
} /*}}}*/
$last=false;
if($stage==4){
    $last=true;
}
$html=pageForm($pagetitle,$fields,$hidden,$last);
echo $html;

?>
