<?php
/**
 * Settings Menu
 *
 * Adds a navigation link
 *
 * @version 1.4 [2009-11-06]
 * @author Roland Liebl
 * @website http://mycrystalmail.googlecode.com
 * @licence GNU GPL
 */

/**
 *
 * Usage: http://mail4us.net/mycrystalmail
 *
 * Make sure that this plugin is loaded  a f t e r  check_identities plugin
 *
 **/
 
class settings extends crystal_plugin
{
  public $task = 'settings';

  function init(){

    $this->task = 'settings';
    $this->_load_config('settings');

    $cmail = cmail::get_instance();
   
    $this->register_handler('plugin.account_sections', array($this, 'account_sections'));
    $this->register_action('plugin.account', array($this, 'account'));
    $this->add_hook('list_prefs_sections', array($this, 'account_link'));
    $this->add_hook('user_preferences', array($this, 'prefs_table'));
    
    $skin  = $cmail->config->get('skin');
    $_skin = get_input_value('_skin', crystal_INPUT_POST);

    if($_skin != "")
      $skin = $_skin;

    // abort if there are no css adjustments
    if(!file_exists('plugins/settings/skins/' . $skin . '/settings.css')){
      if(!file_exists('plugins/settings/skins/default/settings.css'))   
        return;
      else
        $skin = "default";
    }
   
    $nav_hooks = (array)$cmail->config->get('settingsnav');
    $nav = array();
    foreach($nav_hooks as $key => $val)
      $nav['settingsnav'][] = $val;

    $cmail->config->merge($nav);
    $this->include_stylesheet('skins/' . $skin . '/settings.css');
    $browser = new crystal_browser();
    if($browser->ie){
      if($browser->ver < 8)
        $this->include_stylesheet('skins/' . $skin . '/iehacks.css');
      if($browser->ver < 7)
        $this->include_stylesheet('skins/' . $skin . '/ie6hacks.css');
    }

    $this->add_hook('template_object_userprefs', array($this, 'userprefs'));
    $this->add_texts('localization/'); 
    $cmail->output->add_label('settings.account');

  }

  function _load_config($plugin)
  {
    $cmail = cmail::get_instance();
    $config = "plugins/" . $plugin . "/config/config.inc.php";
    if(file_exists($config))
      include $config;
    else if(file_exists($config . ".dist"))
      include $config . ".dist";
    if(is_array($cmail_config)){
      $cmail->config->merge($cmail_config);
    }
  }  

  function account()
  {
    $cmail = cmail::get_instance();
    $cmail->output->send("settings.account");
    exit;
  }
   
  function account_link($args)
  {
    $temp = array();
    $temp['accountlink']['id'] = 'accountlink';
    $temp['accountlink']['section'] = $this->gettext('settings.account');
    $args['list'] = $args['list'] + $temp;
  
    return $args;    
  } 

  function account_sections()
  {
  
    $cmail = cmail::get_instance();
    //display a message if required by url
    if(isset($_GET['_msg'])){
      $cmail->output->command('display_message', urldecode($_GET['_msg']), $_GET['_type']);
    }
    $parts = (array)$cmail->config->get('settingsnav');
    $pluginsdir = "plugins";
    $out = "<div id=\"userprefs-accountblocks\">\n";
    $ii = 0;
    foreach($parts as $key => $part){
      $i = 0;
      $ii ++;
        if(!empty($part['descr'])){
          $i++;
          $locale = $part['locale'];
          $descr_default = $pluginsdir . "/" . $part['descr'] . "/localization/en_US_plugin_descr.html.dist";
          $descr =         $pluginsdir . "/" . $part['descr'] . "/localization/" . $_SESSION['language'] . "_plugin_descr.html";
          if(file_exists($descr)){
           $descr_content = file_get_contents($descr);
          }
          else{
            if(file_exists($descr_default)){
              $descr_content = file_get_contents($descr_default);
            }
            else{
              $descr_content = "Plugin Settins: Default Localization is missing:<br />$descr_default";
            }
          }
        $out .= "<div class=\"userprefs-accountblock\">\n";
        $out .= "<div class=\"userprefs-accountblock-border\">\n";
        $out .= "&nbsp;&raquo;&nbsp;<a onmouseout=\"document.getElementById('descr_$ii').style.display='none';\" onmouseover=\"document.getElementById('descr_$ii').style.display='block';\" class=\"plugin-description-link\" href=\"" . $part['href'] . "\">" . $this->gettext($locale) . "</a>\n";
        $out .= "<div onmouseout=\"this.style.display='none';\" class=\"plugin-description\" id=\"descr_$ii\" style=\"display:none;\">\n";        


        $out .= $descr_content;

        $out .= "</div>\n";
        $out .= "</div>\n";                   
        $out .= "</div>\n";

        if($i == 2){
          $i = 0;
          $out .= "<div style=\"clear:left\"></div>\n";
        }
      }
    }

    $out .= "</div>\n";
    return $out;

  }

  function prefs_table($args)
  {
    if ($args['section'] == 'accountlink') {
      $args['blocks']['main']['options']['accountlink']['title'] = "";
      $args['blocks']['main']['options']['accountlink']['content'] = $this->account_sections("");
    }

    return $args;

  }

  function userprefs($p)
  {
    $cmail = cmail::get_instance();
    $user = $cmail->user->data['username'];

    (array)$temparr = explode("<fieldset>",$p['content']);
    for($i=1;$i<count($temparr);$i++){
      $langs = $cmail->list_languages();
      $limit_langs = array_flip((array)$cmail->config->get('limit_languages'));      
      if(count($limit_langs) > 0){
        foreach($langs as $key => $val){
          if(!isset($limit_langs[$key])){
            $temparr[$i] = str_replace("<option value=\"$key\">$val</option>\n","",$temparr[$i]);
          }
        }     
      }
      $skins = cmail_get_skins(); 
      $selected_skin = strtolower($cmail->config->get('skin'));  
      $limit_skins = array_flip((array)$cmail->config->get('limit_skins'));
      if(count($limit_skins) > 0){
        foreach($skins as $key => $val){
          if(!isset($limit_skins[$val])){
            $temparr[$i] = str_replace("<option value=\"$val\">$val</option>\n","",$temparr[$i]);
          }
          else{
            $temparr[$i] = str_replace("<option value=\"$val\">$val</option>\n","<option value=\"$val\">" . crystal_label($val,'settings') . "</option>\n",$temparr[$i]);            
          }
          if(strtolower($val) == $selected_skin)
            $selected = "selected=\"selected\"";
          else
            $selected = "";
          $temparr[$i] = str_replace("<option value=\"$val\" selected=\"selected\">$val</option>\n","<option value=\"$val\" $selected>" . crystal_label($val,'settings') . "</option>\n",$temparr[$i]);
        }         
      }
      $temparr[$i] = "<div class=\"settingsplugin\" id=\"" . $parts[$i-1] . "\"><fieldset>" . str_replace("</fieldset>","</fieldset></div>",$temparr[$i]);

      if($_GET['_section'] == "remotefolders" || $_POST['_section'] == "remotefolders"){        
        $temparr[$i] = str_replace("</legend>"," ::: " . $_SESSION['username'] . "</legend>",$temparr[$i]);
        $temparr[$i] = str_replace("remotefolders :::", $this->gettext('remotefolders') . " :::", $temparr[$i]);    
      }
      else if($_GET['_section'] == "accountlink" || $_POST['_section'] == "accountlink"){
        $temparr[$i] = str_replace("<legend />","<legend>" . $this->gettext('settings.account') . " ::: " . $_SESSION['username'] . "</legend>",$temparr[$i]);
      }        
      else{
        $temparr[$i] = str_replace("</legend>"," ::: " . $user . "</legend>",$temparr[$i]);
      }
    }
    
    $p['content'] = implode($temparr);
    if($_GET['_section'] == "accountlink" || $_POST['_section'] == "accountlink"){
      $p['content'] .= 
'<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){ cmail.init(); document.getElementById("formfooter").innerHTML = ""});
/* ]]> */
</script>';
    }
    return $p;

  }
}

?>