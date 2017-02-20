<?php
/*
 * vim: set expandtab tabstop=4 shiftwidth=2 softtabstop=4 foldmethod=marker:
 *
 * tag.class.php
 *
 * Started: Sunday 19 February 2017, 08:59:12
 * Last Modified: Sunday 19 February 2017, 09:03:07
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

/*
 * creates a html tag easily
 */
class Tag
{
    private $attr;
    private $content;
    private $name;
    private $newline;
    private $autoclass;
    private $noclose;
    public function __construct($name,$content="",$attr="",$newline=true,$autoclass=false,$noclose=false)
    {// {{{
        $this->setName($name);
        $this->setContent($content);
        $this->setAtts($attr);
        $this->setNewLine($newline);
        $this->setautoclass($autoclass);
        $this->noclose=$noclose;
    }// }}}
    public function __destruct()/*{{{*/
    {
    }/*}}}*/
    public function setAutoClass($autoclass=true)
    {// {{{
        $this->autoclass=$autoclass;
    }// }}}
    public function setNewLine($newline=true)
    {// {{{
        $this->newline=$newline;
    }// }}}
    public function setContent($content)
    {// {{{
        $this->content=$content;
    }// }}}
    public function setName($name)
    {// {{{
        $this->name=$name;
    }// }}}
    public function setAtts($atts)
    {// {{{
        if(is_array($atts)){// {{{
            if(count($atts)){// {{{
                $this->attr=$atts;
            }// }}}
        }else{
            $this->attr=$atts;
        }// }}}
        // if($this->autoclass){// {{{
            // $this->attr=array("class"=>"x" . $this->name);
        // }// }}}
    }// }}}
    public function makeAtts()
    {// {{{
        $ret="";
        if(is_array($this->attr)){
            reset($this->attr);
            // debug("definately an array",$this->attr);
            while(list($key,$val)=each($this->attr)){
                if(strlen($ret)){
                    $ret.=" " . $key . "='" . $val . "'";
                }else{
                    $ret=$key . "='" . $val . "'";
                }
            }
        }else{
            if(strlen($this->attr)){
                $ret=$this->attr;
            }
        }
        if(strlen($ret)){
            $ret=" " . $ret . " ";
        }
        return $ret;
    }// }}}
    public function makeTag()
    {// {{{
        $op="<" . $this->name;
        $op.=$this->makeAtts();
        $op.=">";
        if($this->noclose){// {{{
            if($this->newline){// {{{
                $op.="\n";
            }// }}}
            return $op;
        }// }}}
        $op.=$this->content;
        $op.="</" . $this->name . ">";
        if($this->newline){// {{{
            $op.="\n";
        }// }}}
        return $op;
    }// }}}
}
?>
