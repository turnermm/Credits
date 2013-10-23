<?php


// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();
 
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';

 
class syntax_plugin_credits extends DokuWiki_Syntax_Plugin {
    var $plugins = array();
    var $types = array('admin','helper','syntax','action','renderer');   
    var $keys;
    var $show_descriptions = true;    
 
    function getType(){ return 'substition'; }
    function getAllowedTypes() { return array(); }   
    function getSort(){ return 100; }

    function connectTo($mode) {
         $this->Lexer->addSpecialPattern('<<CREDITS:.*?>>',$mode,'plugin_credits'); }

    function handle($match, $state, $pos, &$handler) {                 
             preg_match('/<<CREDITS:(.*?)>>/', $match,$matches); 
                 
             return array( $state, $matches[1]);
   }
   

  /**
     * Create output
     */
    function render($mode, &$renderer, $data) {
        if($mode == 'xhtml'){
            $this->get_plugin_array();
            list($state, $match) = $data;                     
            $match = trim($match);
            if($match && function_exists($match)) {                
                $match($renderer,$this->plugins);
            }
            else {
                 if($match == '~nodesc~') {
                    $this->show_descriptions = false;
                 }
                 $this->_page($renderer);
            }
            return true;         
         }
        
              
        return false;
    } 
/*
'text_weight'=> 'bold'
'email_text_size'=> '90%'
'font_family' sans-serif
'text_size' 90%
*/
  function _get_configs($which) {
    global $conf;
    $settings = array(
           'background_color'=>'#FFDD99', 'pop_up_color'=>'#006600', 'pop_up_weight'=>'normal',
            'text_weight'=> 'bold','email_text_size'=> '95%','text_size'=>'90%',
             'font_family'=>'Verdana,"Lucida Grande",Lucida,Helvetica,Arial,sans-serif', 
             'email_weight'=>'normal', 'text_color'=>'#333333', 'line_height'=>'1.25',
             'pop_up_family' =>'Helvetica,Arial,sans-serif', 'title_string' => '<h3>Plugins</h3>',
              'subtitle_string'=>'<b style="font-size: 125%; color:black;">Credits</b>',
              'do_desc' => true
            );
    if(isset ($conf['plugin']['credits'][$which]))
           return($conf['plugin']['credits'][$which]);
    else  return $settings[$which];
  }

  function mouseover() {
  $bgcolor = $this->_get_configs('background_color');
  $text = $this->_get_configs('pop_up_color');
  $pop_up_weight = $this->_get_configs('pop_up_weight');
  $family = $this->_get_configs('pop_up_family'); 
  $doc = '<span id="pcredit_mo" ' .
      ' style="visibility:hidden; position:absolute; background-color: ' . $bgcolor . ';' 
      . ' font-family: ' . $family . ';' 
      . ' color: ' . $text . '; height:1em; padding:4px; white-space:nowrap; '
      . '  font-weight:' . $pop_up_weight  . '; font-size:85%;">'
   
  . '</span>';


  return $doc . "\n";


  }

  function _page(&$renderer) {
    $text_weight = $this->_get_configs('text_weight');
    $text_size = $this->_get_configs('text_size');
    $email_size = $this->_get_configs('email_text_size');
    $email_weight=$this->_get_configs('email_weight');
    $line_height=$this->_get_configs('line_height');
    $text_color=$this->_get_configs('text_color');
    $family=$this->_get_configs('font_family');
    $title=$this->_get_configs('title_string');
    $subtitle=$this->_get_configs('subtitle_string');

    if(!$this->show_descriptions) {
         $do_description = false;
    }
    else {
       $do_description=$this->_get_configs('do_desc');
    }
  
            $renderer->doc .= $this->mouseover($renderer);            
            $renderer->doc .= "<div id='credits_d' style='margin:1em; font-size: $text_size; font-weight:$text_weight; ";
            $renderer->doc .= "font-family: $family; line-height: $line_height; color: $text_color; '>\n";
            $renderer->doc .= $title;
            $renderer->doc .= $subtitle;
            $renderer->doc .= "<br>";

            $id_count = 0;
            foreach($this->keys as $name) {
                  $date = isset($this->plugins[$name]['date'])?$this->plugins[$name]['date']:"" ;
                  $author = isset($this->plugins[$name]['author'])?$this->plugins[$name]['author']:"" ; 

                  if(isset($this->plugins[$name]['email'])) { 
                        $remainder = "";
                        $desc = "";
                        if($do_description) {
                            $desc = $this->plugins[$name]['desc'];
                        }
                        if(!empty($desc)) {
                            $remainder = "<br />"; 
                            $remainder_id = "";
                            if($trunc_array = $this->truncate($desc)) {
                                 list($trunc, $rest) = $trunc_array; 
                                 $id_count++;   
                                 $remainder_id = 'remainderid_' . $id_count;
                                 $trunc = "$trunc <a href='javascript:void 0;' "
                                        . " onmouseout='hide_remainder(\"$remainder_id\");' "
                                        . " onmouseover='show_remainder(\"$remainder_id\");' >"                                 
                                        . '&nbsp;&nbsp;->&nbsp;&nbsp;&nbsp;</a>';
                                 $remainder = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id = '$remainder_id' "                                   
                                     . "style='font-size: 90%; color: #436976;font-weight:$text_weight; font-style:normal; "
                                     . " display:none;'>"
                                      . $rest . '</span><br />';                               
                                 $desc = $trunc;
                            }
                        }

                        $address =credits_hide_email($this->plugins[$name]['email']);
                        $email = "&nbsp;&nbsp;<a href='mailto:$address' "
                        . " style='font-size: $email_size; font-weight:$email_weight;  '" 
                        . " onmouseover= 'pcredits_mouseover(event, \"${date}\", \"$remainder_id\");' " 
                        . " onmouseout= 'pcredits_mouseout(\"$remainder_id\");' "
                        . ">$author</a>\n"; 
                  }                  
                  $renderer->doc .= "$name $email<br />";
                  if(!empty($remainder)) {
                      $renderer->doc .= "&nbsp;&nbsp;&nbsp;&nbsp;";
                  }
                  $renderer->doc .= "<span>$desc</span>$remainder";                               
            }
           $renderer->doc .= '</div>';
  }

  function get_plugin_array() {
       foreach($this->types as $type) {
           $this->store_plugins($type);
       }
       $this->keys = array_keys($this->plugins);
       natcasesort($this->keys);       
  }

    function store_plugins($type=""){
        if(!$type) return; 
        $plugins = plugin_list($type);
        foreach($plugins as $p){
            if (!$po =& plugin_load($type,$p)) continue;
            $info = $po->getInfo();           
            $this->plugins[$info['name']] = $info;              
            unset($po); 
        }


    }

    function truncate($str, $len=50) {
        $strL = strlen($str);
        if($strL > $len) {
           $trunc = substr($str,0,$len);
           
           $remainder = substr($str,$len);
           if(strlen($remainder) < 30) {
                  return false;
           }
           return(array($trunc,$remainder));  
        }
        return false;
    }
}

function credits_hide_email($email) {
      $encode = '';
      for ($x=0; $x < strlen($email); $x++) $encode .= '&#x' . bin2hex($email{$x}).';';
      return $encode;
}

function alt_credits(&$renderer,$info) {

  foreach($info as $plugin=>$items) {
       $renderer->doc .= "$plugin<br />";
       foreach($items as $name=>$value) {
         if($name == 'email') $value = credits_hide_email($value);
         $renderer->doc .= "$name: $value<br />";
       }
       $renderer->doc .= '<br />';
  }
   
}
