<?php
/**
 * accounts plugin
 *
 *
 * @version 1.5 - 23.04.2010
 * @author Roland 'rosali' Liebl
 * @website http://mycrystalmail.googlecode.com
 * @licence GNU GPL
 *
 **/
 
/**
 *
 * Usage: http://mail4us.net/mycrystalmail/
 *
 **/
 
/** REQUIREMENTS: settings plugin
 *               if you don't use it, nav into plugin settings by
 *               ./?_task=settings&_action=plugin.accounts&_framed=1
 *
 * NOTICE: Sending messages will be handled by your smtp service and
 *         not by the remote account.
 *
 **/

class accounts extends crystal_plugin
{

  public $task = 'mail|settings';
  private $imap_open_flag = 'novalidate-cert';

  function init()
  {
    $this->add_texts('localization/');  
    $this->_load_config();
    $this->register_action('plugin.accounts', array($this, 'navigation'));
    $this->add_hook('startup', array($this, 'startup'));
    $this->add_hook('template_object_accounts_form_add', array($this, 'accounts_add'));
    $this->add_hook('template_object_accounts_form_edit', array($this, 'accounts_edit'));
    $this->add_hook('template_object_accounts_list', array($this, 'accounts_list'));
    $this->add_hook('render_page', array($this, 'select_account'));
    $this->add_hook('user_preferences', array($this, 'special_folders_form'));
    $this->add_hook('save_preferences', array($this, 'special_folders_save'));
    $this->add_hook('render_mailboxlist', array($this, 'render_mailboxlist'));
    $this->add_hook('template_object_composeheaders', array($this, 'select_identity')); 
  }
   
  function _load_config()
  {
    $cmail = cmail::get_instance();
    $config = "plugins/accounts/config/config.inc.php";
    if(file_exists($config))
      include $config;
    else if(file_exists($config . ".dist"))
      include $config . ".dist";
    if(is_array($cmail_config)){
      if(is_array($cmail_config['settingsnav']) && is_array($cmail->config->get('settingsnav'))){
        $nav = array_merge($cmail->config->get('settingsnav'), $cmail_config['settingsnav']);
        $cmail_config['settingsnav'] = $nav;
      }
      $arr = array_merge($cmail->config->all(),$cmail_config);
      $cmail->config->merge($arr);
    }
    $this->include_script('accounts.js');
  }
  
  function navigation()
  {
     if(isset($_GET['_switch'])){
      $this->switch_account(get_input_value('_switch', crystal_INPUT_GET));
    }
     
    if(isset($_GET['_add'])){
      $this->add();
    }
    
    if(isset($_GET['_add_do'])){
      $this->add_do();
    }    
    
    if(isset($_GET['_delete'])){
      $this->delete();
    }
    
    if(isset($_GET['_edit'])){
      $this->edit();
    }
    
    if(isset($_GET['_edit_do'])){
      $this->edit_do();
    }
    
    $cmail = cmail::get_instance();
    
    if(isset($_GET['_success'])){
      if($_GET['_success'] == 1)
        $cmail->output->show_message('successfullysaved', 'confirmation');
      else
        $cmail->output->show_message('errorsaving', 'error');
    }
    
    $cmail->output->send("accounts.list");  

  }
  
  function test_connection($username,$password,$imap_host,$imap_port,$prot){
               
    if(function_exists("imap_open")){
      $cmail = cmail::get_instance();
      $flag = $cmail->config->get('imap_open_flag');
      if($flag)
        $this->imap_open_flag = $flag;
      $password = $cmail->decrypt($password);
      if($prot){
        $imap_open = "{" . $imap_host . ":" . $imap_port . "/imap/" . $prot . "/" . $this->imap_open_flag . "}INBOX";
      }
      else{
        $imap_open = "{" . $imap_host . ":" . $imap_port . "/" . $this->imap_open_flag . "}INBOX";
      }
    
      if($res = @imap_open($imap_open, $username, $password)){
        $success = true;
        imap_close($res);
      }
      else{
        $success = false;
      }
    }
    else{
      $success = true;
    }
      
    return $success;
  
  }
  
  function select_identity($args)
  {
    $cmail = cmail::get_instance();  
    if(strtolower($cmail->user->data['username']) != strtolower($_SESSION['username'])){ 
      $user_identities = (array)$cmail->user->list_identities();
      foreach($user_identities as $key => $val){
        if(strtolower($val['email']) == strtolower($_SESSION['username'])){
          $id = $val['identity_id'];
          break;
        }
      }
      if($id){
        $script  = "<script type='text/javascript'>\n";
        $script .= "  document.getElementById('_from').value='" . $id . "';\n";
        $script .= "</script>\n";
        $args['content'] = $args['content'] . $script;
      }
    }
    
    return $args;
    
  }
  
  function render_mailboxlist($args)
  {
    $cmail = cmail::get_instance();
    $archive_folder = $cmail->config->get('archive_mbox');
    if(strtolower($cmail->user->data['username']) != strtolower($_SESSION['username']))
      $cmail->output->set_env('archive_folder', $archive_folder);
    return $args;
  }
  
  function startup($args){
  
    $cmail = cmail::get_instance();
    if($cmail->task == "mail" || $cmail->task == "settings"){
      if($cmail->task == "settings" && isset($_SESSION['remote_aid'])){
        $temparr = $this->get($_SESSION['remote_aid']);
        $_SESSION['username'] = $temparr['account_id'];
      }     
      if(strtolower($cmail->user->data['username']) != strtolower($_SESSION['username'])){
        $query = "SELECT * FROM " . get_table_name('accounts') . " WHERE aid=?";
        $sql = $cmail->db->query($query, $_SESSION['remote_aid']);
        $config = $cmail->db->fetch_assoc($sql);
        if($config)
          $config = unserialize($config['preferences']);
        if(is_array($config)){ 
          $arr = array_merge($cmail->config->all(),$config);
          $cmail->config->merge($arr);
        }
        $cmail->config->set('create_default_folders', FALSE);
        $cmail->config->set('enable_caching', FALSE);
        if(is_object($cmail->imap))
          $cmail->imap->set_caching(false);
        if($cmail->config->get('accounts_smtp_default')){
          if($cmail->config->get('smtp_server') == "%h")
            $cmail->config->set('smtp_server', $cmail->user->data['mail_host']);
          if($cmail->config->get('smtp_user') == "%u")  
            $cmail->config->set('smtp_user', $cmail->user->data['username']);
          if($cmail->config->get('smtp_pass') == "%p")
            $cmail->config->set('smtp_pass', $cmail->decrypt($cmail->user->data['password']));        
        }
      }
    }
    
    return $args;
  
  }
  
  function special_folders_save($args)
  {
    $cmail = cmail::get_instance();
    if((strtolower($cmail->user->data['username']) != strtolower($_SESSION['username'])) && $_POST['_section'] == 'folders'){
      $arr_save['drafts_mbox'] = get_input_value('_drafts_mbox', crystal_INPUT_POST);
      $arr_save['junk_mbox'] = get_input_value('_junk_mbox', crystal_INPUT_POST);
      $arr_save['trash_mbox'] = get_input_value('_trash_mbox', crystal_INPUT_POST);
      $arr_save['sent_mbox'] = get_input_value('_sent_mbox', crystal_INPUT_POST);
      $arr_save['archive_mbox'] = get_input_value('_archive_mbox', crystal_INPUT_POST);
      $arr_save['default_imap_folders'] = array(
        'INBOX',
        $arr_save['drafts_mbox'],
        $arr_save['sent_mbox'],
        $arr_save['junk_mbox'],
        $arr_save['trash_mbox'],
        $arr_save['sent_mbox'],
        $arr_save['archive_mbox']
      );
      $serial = serialize($arr_save);
      $query = "UPDATE " . get_table_name('accounts') . " SET preferences=? WHERE aid=?";
      $cmail->db->query($query, $serial, $_SESSION['remote_aid']); 
      $cmail->output->redirect(array('_task' => 'settings', '_action' => 'edit-prefs', '_section' => 'folders', '_framed' => '1'));
      exit; 
    }

    return $args;
          
  }
  
  function special_folders_form($args)
  {
    $cmail = cmail::get_instance();
    if($args['section'] == 'folders'){
      if(strtolower($cmail->user->data['username']) != strtolower($_SESSION['username'])){
        $query = "SELECT * FROM " . get_table_name('accounts') . " WHERE aid=?";
        $sql = $cmail->db->query($query, $_SESSION['remote_aid']);
        $config = $cmail->db->fetch_assoc($sql);
        if($config)
          $config = unserialize($config['preferences']);
        else
          $config = array();
        
        $args['blocks']['main']['name'] = $this->gettext('remotefolders');
        $cmail->imap_connect();
        $select = cmail_mailbox_select(array('noselection' => '---', 'realnames' => true, 'maxlength' => 30));
        $args['blocks']['main']['options']['drafts_mbox'] = array(
          'title' => Q(crystal_label('drafts')),
          'content' => $select->show($config['drafts_mbox'], array('name' => "_drafts_mbox")),
        );
        $args['blocks']['main']['options']['sent_mbox'] = array(
          'title' => Q(crystal_label('sent')),
          'content' => $select->show($config['sent_mbox'], array('name' => "_sent_mbox")),
        );
        $args['blocks']['main']['options']['junk_mbox'] = array(
          'title' => Q(crystal_label('junk')),
          'content' => $select->show($config['junk_mbox'], array('name' => "_junk_mbox")),
        );
        $args['blocks']['main']['options']['trash_mbox'] = array(
          'title' => Q(crystal_label('trash')),
          'content' => $select->show($config['trash_mbox'], array('name' => "_trash_mbox")),
        );
        $plugins = $cmail->config->get('plugins');
        $plugins = array_flip($plugins);
        if(isset($plugins['archivefolder'])){
          $args['blocks']['main']['options']['archive_mbox'] = array(
            'title' => Q($this->gettext('archivefolder')),
            'content' => $select->show($config['archive_mbox'], array('name' => "_archive_mbox")),
          ); 
        }
      } 
    }
    
    return $args;
    
  }
  
  function switch_account($aid=-1) 
  { 
    $cmail = cmail::get_instance(); 

    $account = $this->get($aid); 

    if($aid == -1){ 
      $_SESSION['username'] = $cmail->user->data['username']; 
      $_SESSION['password'] = $_SESSION['default_account_password']; 
      $_SESSION['imap_host'] = $_SESSION['imap_host_sav']; 
      $_SESSION['imap_port'] = $_SESSION['imap_port_sav']; 
      $_SESSION['imap_root'] = $_SESSION['imap_root_sav']; 
      $_SESSION['imap_delimiter'] = $_SESSION['imap_delimiter_sav'];
      $_SESSION['imap_ssl'] = $_SESSION['imap_ssl_sav'];
      $cmail->session->remove('account_dn'); 
      $cmail->session->remove('remote_aid'); 
      $cmail->output->redirect(array('_task' => 'mail', '_mbox' => 'INBOX'));     
    } 
    else{ 
      $url = parse_url($account['account_host']); 
      if(isset($url['path'])) 
        $imap_host = $url['path']; 
      else 
        $imap_host = $url['host'];       
      if($url['scheme'] == "tls" || $url['scheme'] == "ssl"){ 
        $imap_ssl = TRUE; 
        $prot = $url['scheme']; 
      } 
      else{ 
        $imap_ssl = FALSE; 
        $prot = FALSE; 
      } 
      if(isset($url['port'])) 
        $imap_port = $url['port']; 
      else 
        $imap_port = $cmail->config->get('default_port');       

      $username = $account['account_id']; 
      $password = $account['account_pw']; 

      if($this->test_connection($username,$password,$imap_host,$imap_port,$prot)){ 
        // fake account
        $_SESSION['username'] = $username; 
        if (!isset($_SESSION['remote_aid']))
	        $_SESSION['default_account_password'] = $_SESSION['password'];
        $_SESSION['password'] = $password;
        if(empty($_SESSION['imap_host_sav']))
          $_SESSION['imap_host_sav'] = $_SESSION['imap_host'];
        $_SESSION['imap_host'] = $imap_host;
        if(empty($_SESSION['imap_port_sav']))
          $_SESSION['imap_port_sav'] = $_SESSION['imap_port'];
        $_SESSION['imap_port'] = $imap_port;
        if(!isset($_SESSION['imap_ssl_sav'])) 
          $_SESSION['imap_ssl_sav'] = !empty($_SESSION['imap_ssl']);   
        $_SESSION['imap_ssl'] = $imap_ssl; 
        $_SESSION['remote_aid'] = $aid;
        if(empty($_SESSION['imap_root_sav']))
          $_SESSION['imap_root_sav'] = $_SESSION['imap_root']; 
        $_SESSION['imap_root'] = null;
        if(empty($_SESSION['imap_delimiter_sav']))
          $_SESSION['imap_delimiter_sav'] = $_SESSION['imap_delimiter']; 
        $_SESSION['imap_delimiter'] = null;
        $_SESSION['account_dn'] = $account['account_dn'];        
        $cmail->output->redirect(array('_task' => 'mail', '_mbox' => 'INBOX'));   
      } 
      else{ 
        $cmail->output->show_message("accounts.connectionfailed","error"); 
        $cmail->output->send('mail'); 
      } 
    } 

  }
  
  function select_account($p)
  {
    if($p['template'] != "mail")
      return $p;
      
    $cmail = cmail::get_instance();  

    if(isset($_SESSION['temp']) || isset($_SESSION['terms']) || strtolower($cmail->task) != "mail")
      return $p; 

    $skin  = $cmail->config->get('skin');
    $_skin = get_input_value('_skin', crystal_INPUT_POST);

    if($_skin != "")
      $skin = $_skin;

    // abort if there are no css adjustments
    if($skin == "crystal-3-column") {
    $skin = "crystal";
    } else {
    
    if(!file_exists('plugins/accounts/skins/' . $skin . '/accounts.css')){
      if(!file_exists('plugins/accounts/skins/default/accounts.css'))   
        return $p;
      else
        $skin = "default";
    }
}
    $this->include_stylesheet('skins/' . $skin . '/accounts.css');
    $browser = new crystal_browser;
    if($browser->ie && $browser->ver == 6){
      $this->include_stylesheet('skins/' . $skin . '/ie6.css');	
    }
    
    $link = "#";
    $title = "";
    $plugins = $cmail->config->get("plugins");
    $plugins = array_flip($plugins);
    if(isset($plugins['moreuserinfo'])){
      $link = "./?_task=settings&_action=plugin.moreuserinfo_show";
      $title = $this->gettext('moreuserinfo.userinfo');
    }
    
    $user = $cmail->user->data['username'];
    if(strlen($user) > 20)
      $user = substr($user,0,20) . "...";
    $selected = "";
    if(strtolower($_SESSION['username']) == strtolower($user)){
      $selected = "selected";
    }
    $selector  = "\n";
    $selector .= "<select onchange='switch_account(this.value)'>\n";
    $selector .= "<option $selected value='-1'>$user</option>\n";
    
    $selected = "";
    
    $accounts = $this->accounts_get_sorted_list();

    if(count($accounts) > 0){
      foreach($accounts as $key => $val){
        if(strtolower($_SESSION['account_dn']) == strtolower($val['account_dn'])){
          $selected = "selected";    
        }
        else{
          $selected = "";
        }
        if(strlen($val['account_dn']) > 20)
          $val['account_dn'] = substr($val['account_dn'],0,20) . "...";
        $selector .= "<option $selected value='" . $val['aid'] . "'>" . $val['account_dn'] . "</option>\n";
      }
    }
    
    $selector .= "</select>\n";
    $cmail->output->add_footer('<div id="accounts"><a href="' . $link . '" title="' . $title .'">&nbsp;</a>' . $selector . '</div>');      
    
    return $p;
        
  }
  
  function accounts_list($args)
  { 
    $cmail = cmail::get_instance();
  
    $accounts = $this->accounts_get_sorted_list();

    if(count($accounts) > 0){           
      $out = "";
      $alt = "even";
      $edit = $this->gettext("edit");
      $delete = $this->gettext("delete");
      $skin = $cmail->config->get("skin");
      foreach($accounts as $key => $val){
        if($alt == "even")
          $alt = "odd";
        else
          $alt = "even";

        $out .= sprintf("<tr class=\"" . $alt . "\"><td width=150 class=\"section\" valign=\"top\" class=\"title\"><label for=\"%s\">%s</label></td><td width=250 class=\"section\" >%s</td><td class=\"section\" >&nbsp;&nbsp;<a href=\"%s\"><img alt=\"%s\" title=\"%s\" src=\"./skins/$skin/images/icons/rename.png\" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"%s\"><img alt=\"%s\" title=\"%s\" src=\"./skins/$skin/images/icons/delete.png\" /></a></td><td class=\"section\">&nbsp;</td></tr>\n",
                "acc_" . $val['account_dn'],
                rep_specialchars_output($val['account_dn']),
                rep_specialchars_output($val['account_id']),
                "./?_task=settings&_action=plugin.accounts&_edit=1&_aid=" . $val['aid'] . "&_framed=1",
                $edit,
                $edit,
                "./?_task=settings&_action=plugin.accounts&_delete=1&_aid=" . $val['aid'] . "&_framed=1",
                $delete,
                $delete);    
      }
      
      $args['content'] = $out;
    
    }  
          
    return $args;  
  }
  
  function accounts_get_sorted_list()
  {
    $cmail = cmail::get_instance();  
    $user_id = $cmail->user->data['user_id'];
    $accounts = array();
    $query = "SELECT * FROM " . get_table_name('accounts') . " WHERE user_id=?";
    $sql = $cmail->db->query($query, $user_id);
    while($account = $cmail->db->fetch_assoc($sql))
      $accounts[] = $account;
      
    $temparr = array();
    foreach($accounts as $key => $val){
      $temparr[$key] = $val['account_dn'];
    }
//$temparr[$key+1] = $cmail->user->data['username'];
    
    asort($temparr);
   
    $return = array();
    foreach($temparr as $key => $val){
      $return[] = $accounts[$key];
    }
    
    return $return;  
  }
  
  function accounts_form_content($dn="",$id="",$host="ssl://imap.gmail.com:993")
  { 
    $cmail = cmail::get_instance();
    
    // allow the following attributes to be added to the <table> tag
    $attrib_str = create_attrib_string($attrib, array('style', 'class', 'id', 'cellpadding', 'cellspacing', 'border', 'summary'));

    // return the complete edit form as table
    $out .= '<fieldset><legend>' . $this->gettext('remoteaccounts') . ' ::: ' . $cmail->user->data['username'] . '</legend>' . "\n";
    $out .= '<br />' . "\n";
    $out .= '<table' . $attrib_str . ">\n";

    $field_id = 'account_dn';
    $input_account_dn = new html_inputfield(array('name' => '_account_dn', 'id' => $field_id, 'size' => 30));

    $out .= sprintf("<tr><td valign=\"top\" class=\"title\"><label for=\"%s\">%s</label>:</td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('account_dn')),
                $input_account_dn->show($dn));

    $field_id = 'account_id';
    $input_account_id = new html_inputfield(array('name' => '_account_id', 'id' => $field_id, 'size' => 30));

    $out .= sprintf("<tr><td valign=\"top\" class=\"title\"><label for=\"%s\">%s</label>:</td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('account_id')),
                $input_account_id->show($id));
                
    $field_id = 'account_pw';
    $input_account_pw = new html_passwordfield(array('name' => '_account_pw', 'id' => $field_id, 'size' => 30));

    $out .= sprintf("<tr><td valign=\"top\" class=\"title\"><label for=\"%s\">%s</label>:</td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('account_pw')),
                $input_account_pw->show());
                
    $field_id = 'account_pw_conf';
    $input_account_pw = new html_passwordfield(array('name' => '_account_pw_conf', 'id' => $field_id, 'size' => 30));

    $out .= sprintf("<tr><td valign=\"top\" class=\"title\"><label for=\"%s\">%s</label>:</td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('account_pw_conf')),
                $input_account_pw->show());                
                
    $field_id = 'account_host';
    
    if(count($cmail->config->get('accounts_hosts')) < 1){
      $input_account_host = new html_inputfield(array('name' => '_account_host', 'id' => $field_id, 'size' => 30, 'onclick' => 'this.value="' . $host . '"'));                
                
      $out .= sprintf("<tr><td valign=\"top\" class=\"title\"><label for=\"%s\">%s</label>:</td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('account_host')),
                $input_account_host->show($host));
    }            
    else{
      $select_host = new html_select(array('name' => '_account_host', 'id' => $field_id));
      $hosts = $cmail->config->get('accounts_hosts');
   
      $select_host->add(array_keys($hosts), array_values($hosts));
      $out .= sprintf("<tr><td valign=\"top\" class=\"title\"><label for=\"%s\">%s</label>:</td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('account_host')),
                $select_host->show($host));      
      
    }
    $out .= "\n</table>";
    $out .= '<br />' . "\n";
    $out .= "</fieldset>\n";
    
    return $out;  
  }
  
  function accounts_add($args)
  {
    $cmail = cmail::get_instance();
    // add some labels to client
    $cmail->output->add_label(
      'accounts.dnempty',
      'accounts.userempty',
      'accounts.passwordempty',
      'accounts.passwordnotmatch',
      'accounts.hostempty'
    );
    
    $account_dn = get_input_value('_account_dn', crystal_INPUT_POST);    
    $account_id = get_input_value('_account_id', crystal_INPUT_POST);
    $account_host = get_input_value('_account_host', crystal_INPUT_POST);
        
    $out  = "<form onsubmit='return accounts_validate()' method='post' action='./?_task=settings&_action=plugin.accounts&_add_do=1&_framed=1'>\n";
    
    $out .= $this->accounts_form_content($account_dn, $account_id, $account_host);
    
    $out .= "<input type='hidden' name='_add' id='add' value=1 />\n";               
    $out .= "<input class='button mainaction' type='submit' value ='" . $this->gettext('submit') . "' />";
    $out .= "<span>&nbsp;</span><input type='button' class='button' value='" . $this->gettext('back') . "' onclick='document.location.href=\"./?_task=settings&_action=plugin.accounts&_framed=1\"' />\n";
    $out .= "</form>\n";
    
    $args['content'] = $out;
    
    return $args;
  }
  
  function accounts_edit($args)
  {
    $cmail = cmail::get_instance();
    
    if(isset($_GET['_exists']))
      $cmail->output->show_message('accounts.accountexists', 'error');
    
    $aid = get_input_value('_aid', crystal_INPUT_GET);
    
    $arr = $this->get($aid);
        
    // add some labels to client
    $cmail->output->add_label(
      'accounts.dnempty',
      'accounts.userempty',
      'accounts.passwordempty',
      'accounts.passwordnotmatch',
      'accounts.hostempty'
    );
    
    
    $out  = "<form onsubmit='return accounts_validate()' method='post' action='./?_task=settings&_action=plugin.accounts&_edit_do=1&_framed=1'>\n";
  
    $out .= $this->accounts_form_content($arr['account_dn'], $arr['account_id'], $arr['account_host']);
    
    $out .= "<input type='hidden' name='_aid' value='$aid' />\n";        
    $out .= "<input class='button mainaction' type='submit' value ='" . $this->gettext('submit') . "' />";
    $out .= "<span>&nbsp;</span><input type='button' class='button' value='" . $this->gettext('back') . "' onclick='document.location.href=\"./?_task=settings&_action=plugin.accounts&_framed=1\"' />\n";
    $out .= "</form>\n";
    
    $args['content'] = $out;
    
    return $args;
  } 
  
  function add()
  {
    $cmail = cmail::get_instance();
    $cmail->output->send("accounts.form_add");
 
  }
  
  function add_do()
  {
    $cmail = cmail::get_instance();
    $account_dn = get_input_value('_account_dn', crystal_INPUT_POST);    
    $account_id = get_input_value('_account_id', crystal_INPUT_POST);
    $account_pw = get_input_value('_account_pw', crystal_INPUT_POST);
    if($account_pw != "")
      $account_pw = $cmail->encrypt($account_pw);
    $account_host = get_input_value('_account_host', crystal_INPUT_POST);
    $user_id = $cmail->user->data['user_id'];
    
    $url = parse_url($account_host);
    if(isset($url['path']))
      $imap_host = $url['path'];
    else
      $imap_host = $url['host'];      
    if($url['scheme'] == "tls" || $url['scheme'] == "ssl"){
      $prot = $url['scheme'];
    }
    else{
      $prot = FALSE;
    }
    if(isset($url['port']))
      $imap_port = $url['port'];
    else
      $imap_port = $cmail->config->get('default_port');      

    if(!$this->test_connection($account_id,$account_pw,$imap_host,$imap_port,$prot)){
       $cmail->output->show_message("accounts.connectionfailed","error");
       $this->add();
    }
    else{

    }
    
    $query = "SELECT * FROM " . get_table_name('accounts') . " WHERE user_id=? AND account_id=? AND account_host=?";

    $ret = $cmail->db->query($query,
      $user_id,
      $account_id,
      $account_host);
    
    $arr = $cmail->db->fetch_assoc($ret);  
    
    if(!is_array($arr)){
    
      $query = "INSERT INTO " . get_table_name('accounts') . "(account_dn, account_id, account_pw, account_host, user_id) VALUES (?, ?, ?, ?, ?);";

      $ret = $cmail->db->query($query,
        $account_dn,
        $account_id,
        $account_pw,
        $account_host,
        $user_id);
      
      if($ret){
        if(check_email($account_id)){
          $cmail->db->query("
            SELECT * FROM ".get_table_name('identities')."
        		WHERE  del<>1
            AND    email=?
            AND    name=?
            AND    user_id=?",
            $account_dn,
            $account_id,
            $user_id);
            
          $IDENTITIES = $cmail->db->fetch_assoc();
          if(!is_array($IDENTITIES)){
            // add identity
            $a_cols =	array(
              0 => 'email',
              1 => 'name'
            );
            $a_values = 	array(
              0 => "'" . strtolower($account_id) . "'",
              1 => "'" . $account_dn             . "'"
            );
            $rc = $cmail->db->query("
                INSERT INTO ".get_table_name('identities')."
                		(user_id, ".join(', ', $a_cols).")
                		VALUES (?, ".join(', ', $a_values).")",
                		$user_id);
          }
        }      
        $cmail->output->show_message('successfullysaved', 'confirmation');
      }
      else
        $cmail->output->show_message('errorsaving', 'error');
    }
    else{
      $cmail->output->redirect(array('_action' => 'plugin.accounts', '_edit' => 1, '_aid' => $arr['aid'], '_framed' => 1, '_exists' => 1));
    }
  }
  
  function delete()
  {
    $cmail = cmail::get_instance();
    $aid = get_input_value('_aid', crystal_INPUT_GET);
    $query = "DELETE FROM " . get_table_name('accounts') . " WHERE aid=?";
    $ret = $cmail->db->query($query, $aid);
    if($ret)
      $success = 1;
    else
      $success = 0;      
    
    $cmail->output->redirect(array('_action' => 'plugin.accounts', '_framed' => 1, '_success' => $success)); 

  }
  
  function edit()
  {
    $cmail = cmail::get_instance();
    $cmail->output->send("accounts.form_edit"); 
  }
  
  function edit_do()
  {
    $cmail = cmail::get_instance();
      
    $aid = get_input_value('_aid', crystal_INPUT_POST);
    $account_dn = get_input_value('_account_dn', crystal_INPUT_POST);
    $account_id = get_input_value('_account_id', crystal_INPUT_POST);
    $account_pw = get_input_value('_account_pw', crystal_INPUT_POST);
    $account_host = get_input_value('_account_host', crystal_INPUT_POST);

    if($account_pw == ""){
      $query = "UPDATE " . get_table_name('accounts') . " SET account_dn=?, account_id=?, account_host=? WHERE aid=?";
      $ret = $cmail->db->query($query, $account_dn, $account_id, $account_host, $aid);
    }
    else{
      $account_pw = $cmail->encrypt($account_pw);
      $query = "UPDATE " . get_table_name('accounts') . " SET account_dn=?, account_id=?, account_host=?, account_pw=? WHERE aid=?";
      $ret = $cmail->db->query($query, $account_dn, $account_id, $account_host, $account_pw, $aid);
    }
    
    if($ret)
      $cmail->output->show_message('successfullysaved', 'confirmation');
    else
      $cmail->output->show_message('errorsaving', 'error');
  
  }
  
  function get($aid="")
  {
    $cmail = cmail::get_instance();  
    if($aid == "")
      $cmail->output->send("accounts.form_add");
      
    $query = "SELECT * FROM " . get_table_name('accounts') . " WHERE aid=?";
    
    $ret = $cmail->db->query($query, $aid);
    $sql = $cmail->db->fetch_assoc($ret);
    
    return $sql;
    
  }    

}

?>