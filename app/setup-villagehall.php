<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker:
 *
 * setup-villagehall.php
 *
 * Started: Saturday 18 February 2017, 09:57:27
 * Last Modified: Sunday 26 February 2017, 09:04:59
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
function pageForm($log,$pagetitle,$fields,$hidden,$last,$radio,$text=false)/*{{{*/
{
    global $apppath,$appname;
    $headfn=$apppath . DIRECTORY_SEPARATOR . $appname . "-header.php";
    $footfn=$apppath . DIRECTORY_SEPARATOR . $appname . "-footer.php";
    include $headfn;
    include $footfn;
    
    $addcontent="";
    $form=new Form();
    foreach($hidden as $ha){
        /*
        if($ha["name"]=="stage"){
            $log->info("Form stage: " . $ha["val"]);
            $tmp=print_r($fields,true);
            $log->info("fields: $tmp");
        }
         */
        $form->addHid($ha["name"],$ha["val"]);
    }
    if(is_array($radio)){
        /* TODO need to add code for radio button handling (db type) */
    }
    if(false!==$text){
        $form->addRow("","submit","submit","continue");
        $addcontent=$text;
    }else{
        if($last){
            foreach($fields as $fa){
                $form->addRow($fa["title"],"direct",$fa["val"]);
            }
            $form->addRow("","submit","correct","Correct");
            $form->addRow("","submit","startagain","Start Again");
        }else{
            foreach($fields as $fa){
                $form->addRow($fa["title"],"text",$fa["name"],$fa["val"]);
            }
            $form->addRow("","submit","submit","continue");
        }
    }
    $tmpform=$form->makeForm();
    $tag=new Tag("div",$addcontent . $tmpform,array("class"=>"centre"));
    $tmpform=$tag->makeTag();
    $tag=new Tag("div",$tmpform,array("id"=>"body"));
    $bodytag=$tag->makeTag();
    $tag=new Tag("body",$bheader . $bodytag . $bfooter);
    $body=$tag->makeTag();
    $tag=new Tag("html",$head . $body);
    $html=" <!DOCTYPE html>\n" . $tag->makeTag();
    return $html;
}/*}}}*/
function validateInputStrings($inputa)/*{{{*/
{
    $ret=false;
    $tmpa=array();
    foreach($inputa as $ip){
        $tmp=GPType($ip);
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
function validateInputRooms()/*{{{*/
{
    $ret=false;
    $tmpa=array();
    $roomscn=getDefaultInt("roomscn",0);
    for($x=0;$x<$roomscn;$x++){
    }
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
importLib("HTML/link.class.php","Link class",$logg);
importLib("HTML/input_field.class.php","Inputfield class",$logg);

$stage=getDefaultInt("stage",0);

$pagetitle="Village Hall - Setup";
$fields=array();
$hidden=array();
$content=false;
$last=false;

/*
 * at each stage below, should the inputs not validate
 * the previous stage is shown again, because if you don't
 * break out of the switch, it falls through to the 
 * next stage
 */
switch($stage){
case 7: /*{{{*/
        $iparr=GPA(array("auname","aupass","vname","roomscn","dbtype","dbhost","dbname","dbuser","dbpass"));
        $tmpip=print_r($iparr,true);
        $configfn=$pvpath . DIRECTORY_SEPARATOR . $appname . "-config.php";
        $config=<<<EOD
<?php

/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * villagehall-config.php
 *
 * Started: Tuesday 22 November 2016, 10:26:19
 * Last Modified: Tuesday 27 December 2016, 13:57:58
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
 */
/* $tmpip */
\$displayname="{$iparr["vname"]}";

/*
 * mysql
 */
\$dbuser="{$iparr["dbuser"]}";
\$dbpass="{$iparr["dbpass"]}";
\$dbname="{$iparr["dbname"]}";
\$dbhost="{$iparr["dbhost"]}";
/*
 * end of mysql
 */
/*
 * sqlite
 */
\$dbfn=\$pvpath . DIRECTORY_SEPARATOR . "db/villagehall.db";
/*
 * end of sqlite
 */
\$dbsetupfn=\$pvpath . DIRECTORY_SEPARATOR . \$appname . ".sql";

\$dbtype="{$iparr["dbtype"]}";
/* \$dbtype="sqlite"; */

/*
 * logging
 */
/* \$logtype="syslog"; */
\$logtype="file";
\$logfilename=\$pvpath . DIRECTORY_SEPARATOR . "log" . DIRECTORY_SEPARATOR . \$appname . ".log";
\$loglevel=LOG_INFO;
/*
  \$loglevel=LOG_DEBUG;
  \$loglevel=LOG_NOTICE;
  \$loglevel=LOG_WARNING;
  \$loglevel=LOG_ERROR;
 */
/* whether to rotate logs or not */
\$logdorotate=true;
/* when to rotate logs */
\$logrotate="weekly";
/*
\$logrotate="hourly";
\$logrotate="daily";
\$logrotate="monthly";
 */
/* number of previous log files to keep */
\$logkeep=5;
/*
 * set \$tracelevel to -1,0,1 or 2
 * then, DEBUG level messages will contain the calling
 * stack trace
 * tracelevel=-1: no output at all apart from the actual debug message
 * tracelevel=0: no stack trace
 * tracelevel=1: caller function/class/file/line number
 * tracelevel=2: full stack trace
 */
\$logtracelevel=-1;
?>
EOD;

        $len=strlen($config);
        header('Content-Description: File Transfer');
        header('Content-Type: text/plain');//<<<<
        header('Content-Disposition: attachment; filename='.basename($configfn));
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $len);
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Pragma: public');
        echo $config;
        exit(0);
        break;
    break;
     /*}}}*/
case 6: /*{{{*/
    if("Correct"==(GP("correct"))){
        /* all done 
         * TODO update db
         */
        $vname=GP("vname");
        $pagetitle=$vname . " Hall - Setup " . $stage;
        $iparr=array("auname","aupass","vname","roomscn","dbtype","dbhost","dbname","dbuser","dbpass");
        $ipret=validateInputStrings($iparr);
        $hidden=$ipret;
        // $fields=array();
        $content="Your configuration is now complete.";
        $tag=new Tag("p",$content);
        $c1=$tag->makeTag();
        $content=" Press the Continue button to download your configuration and store it at the root of the application.";
        $tag=new Tag("p",$content);
        $c1.=$tag->makeTag();
        $al=new Alink("","here");
        $content="Then click " . $al->makeLink() . " to goto the main site.";
        $tag=new Tag("p",$content);
        $c1.=$tag->makeTag();
        $content="If setup starts again then you have put the configuration file in the wrong place.";
        $tag=new Tag("p",$content);
        $c1.=$tag->makeTag();
        $tag=new Tag("div",$c1,array("class"=>"centre"));
        $content=$tag->makeTag();
        $stage+=1;
        $hidden[]=array("name"=>"stage","val"=>$stage);
        break;
    }else{
        $stage=1;
        $hidden[]=array("name"=>"stage","val"=>$stage);
        $fields[]=array("title"=>"Village Name","name"=>"vname","val"=>"");
        break;
    } /*}}}*/
case 5: /*{{{*/
    $iparr=array("auname","aupass","vname","roomscn","dbtype","dbhost","dbname");
    if(false!==($ipret=validateInputStrings($iparr))){
        $ipret[]=array("name"=>"dbuser","val"=>GPType("dbuser"));
        $ipret[]=array("name"=>"dbpass","val"=>GPType("dbpass"));
        $hidden=$ipret;
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
                }elseif($ipa["name"]=="dbhost"){
                    $ipa["title"]="DB Host";
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
            $last=true;
            break;
        }else{
            $stage-=1;
        }
    }else{
        $stage-=1;
    }/*}}}*/
case 4: /*{{{*/
    $iparr=array("auname","aupass","vname","roomscn");
    if(false!==($ipret=validateInputStrings($iparr))){
        $hidden=$ipret;
        $stage+=1;
        $hidden[]=array("name"=>"stage","val"=>$stage);
        $fields[]=array("title"=>"Database Type","name"=>"dbtype","val"=>"mysql");
        $fields[]=array("title"=>"Database Host","name"=>"dbhost","val"=>"localhost");
        $fields[]=array("title"=>"Database Name","name"=>"dbname","val"=>"villagehall");
        $fields[]=array("title"=>"Database User","name"=>"dbuser","val"=>"");
        $fields[]=array("title"=>"Database Password","name"=>"dbpass","val"=>"");
        break;
    }else{
        $stage-=1;
    }/*}}}*/
case 3: /*{{{*/
    $iparr=array("auname","aupass","vname","roomscn");
    if(false!==($ipret=validateInputStrings($iparr))){
        $hidden=$ipret;
        $vname=GP("vname");
        $pagetitle=$vname . " Hall - Setup " . $stage;
        $roomscn=getDefaultInt("roomscn",0);
        for($x=0;$x<$roomscn;$x++){
            $fields[]=array("title"=>"Name Room " . $x+1,"name"=>"roomname[]","val"=>"");
            $fields[]=array("title"=>"Capacity Room " . $x+1,"name"=>"roomsize[]","val"=>"");
            $fields[]=array("title"=>"","name"=>"blankrow","val"=>"");
        }
        $stage+=1;
        break;
    }else{
        $stage-=1;
    } /*}}}*/
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
        $stage-=1;
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
        $stage-=0;
    } /*}}}*/
case 0: /*{{{*/
    $stage+=1;
    $hidden[]=array("name"=>"stage","val"=>$stage);
    $fields[]=array("title"=>"Village Name","name"=>"vname","val"=>"");
    break;
} /*}}}*/
if($stage==6){
}
$logg->info("rendering page");
$html=pageForm($logg,$pagetitle,$fields,$hidden,$last,false,$content);
echo $html;

?>
