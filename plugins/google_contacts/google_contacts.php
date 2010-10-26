<?php
/**
 * Google Contacts
 *
 * @version 1.5 - 28.09.2010
 * @author Roland 'rosali' Liebl
 * @website http://myroundcube.googlecode.com
 * @licence GNU GPL
 * 
 * TUTORIAL: http://www.ibm.com/developerworks/opensource/library/x-phpgooglecontact/index.html
 *
 **/
 
/**
 * Usage: http://mail4us.net/myroundcube/
 *
 * Requirements:
 * Get Zend GData APIs http://framework.zend.com/download/webservices
 * Copy and paste "Zend" folder into ./plugins/google_contacts
 *         ->  File structure must be: lib
 *                                      - Zend
 *                                        - Loader.php
 *                                        - ... 
 *
 * NOTICE: Patch ./plugins/google_contacts/MDB2.php
 *         http://pear.php.net/bugs/bug.php?id=17039&edit=12&patch=skip_delimited_strings_fix_quoting_array&revision=1264618739
 *         Fixed since Roundcube SVN Trunk 3354 (http://trac.roundcube.net/ticket/1486547)
 *
 **/   

class google_contacts extends crystal_plugin
{

  public $task = "mail|addressbook|settings";
  private $abook_id = 'google_contacts';  
  private $user = false;
  private $pass = false;
  private $contacts;
  private $error = false;
  private $results = null;

  function init()
  {    
    $this->add_texts('localization/', false);

    if(file_exists("./plugins/google_contacts/config/config.inc.php"))
      $this->load_config('config/config.inc.php');
    else
      $this->load_config('config/config.inc.php.dist');
    $cmail = cmail::get_instance();
    $this->user = $cmail->config->get('googleuser');
    $this->pass = $cmail->config->get('googlepass');
    
    if($this->user && $this->pass){
      $this->pass = $cmail->decrypt($this->pass);
      $this->add_hook('addressbooks_list', array($this, 'addressbooks_list'));
      $this->add_hook('addressbook_get', array($this, 'addressbook_get'));
      $this->add_hook('contact_create', array($this, 'contact_create'));    
      $this->add_hook('contact_update', array($this, 'contact_update'));
      $this->add_hook('contact_delete', array($this, 'contact_delete'));
      $this->add_hook('render_page', array($this, 'render_page'));
      
      // use this address book for autocompletion queries
      $config = $cmail->config;
      $sources = $config->get('autocomplete_addressbooks', array('sql'));
        
      if (!in_array($this->abook_id, $sources)){
        $sources[] = $this->abook_id;
        $config->set('autocomplete_addressbooks', $sources);
      }       
    }
    $this->add_hook('preferences_sections_list', array($this, 'addressbooksLink'));    
    $this->add_hook('preferences_list', array($this, 'settings_table'));
    $this->add_hook('preferences_save', array($this, 'save_prefs'));                    
  }

  function render_page($p){
    if($p['template']== 'addressbook'){
      $cmail = cmail::get_instance();
      $script = "cmail.add_onload(\"cmail.command('list',cmail.env.source,false);\");";
      $cmail->output->add_script($script,'foot');
    }
    return $p;
  }
    
  function contact_create($a){
    if($a['source'] == $this->abook_id){
      //@ ToDo: implement google contacts update when RC supports more fields
      $a['abort'] = true;
    }
    return $a;
  }  
  
  function contact_update($a){
    if($a['source'] == $this->abook_id){
      //@ ToDo: implement google contacts update when RC supports more fields
      $cmail = cmail::get_instance(); 
      $cmail->output->show_message('google_contacts.abookreadonly', 'error', null, false);
      cmail_overwrite_action('show');      
      $a['abort'] = true;
    }
    return $a;
  }
  
  function contact_delete($a){
    if($a['source'] == $this->abook_id){
      //@ ToDo: implement google contacts update when RC supports more fields
      $a['abort'] = true;      
    }
    return $a;
  }
    
  function addressbooks_list($p)
  {
    $cmail = cmail::get_instance();
    if ($cmail->config->get('use_google_abook'))
      $p['sources'][$this->abook_id] = 
        array('id' => $this->abook_id, 'name' => Q($this->gettext('googlecontacts')), 'readonly' => true, 'groups' => false);
    return $p;  
  }
  
  function addressbook_get($p)
  {
    $cmail = cmail::get_instance();
    if (($p['id'] === $this->abook_id) && $cmail->config->get('use_google_abook')) {
        require_once(dirname(__FILE__) . '/google_contacts_backend.php');
        $p['instance'] = new google_contacts_backend($cmail->db, $cmail->user->ID);
        $p['instance']->groups = false;
        $this->sync_contacts();
        $cmail->output->command('enable_command','add','import',false);
    }
    else{
      if ($p['id'] == $cmail->config->get('default_addressbook')){
        $cmail->output->command('enable_command','import',true);
      }
    }
    return $p;
  }
  
  function addressbooksLink($args)
  {
    $temp = $args['list']['server'];
    unset($args['list']['server']);
    $args['list']['addressbooks']['id'] = 'addressbooks';
    $args['list']['addressbooks']['section'] = $this->gettext('addressbook');
    $args['list']['server'] = $temp;

    return $args;
  }    
  
  function settings_table($args)
  {
    if ($args['section'] == 'addressbooks') {
      $cmail = cmail::get_instance();    
      $use_google_abook = $cmail->config->get('use_google_abook');
      $field_id = 'rcmfd_use_google_abook';
      $checkbox = new html_checkbox(array('name' => '_use_google_abook', 'id' => $field_id, 'value' => 1));
      $args['blocks']['googlecontacts']['name'] = $this->gettext('googlecontacts');
      $args['blocks']['googlecontacts']['options']['use_google_abook'] = array(
        'title' => html::label($field_id, Q($this->gettext('usegoogleabook'))),
        'content' => $checkbox->show($use_google_abook?1:0),
      );

      $field_id = 'rcmfd_google_user';
      $input_googleuser = new html_inputfield(array('name' => '_googleuser', 'id' => $field_id, 'size' => 35));
      $args['blocks']['googlecontacts']['options']['googleuser'] = array(
        'title' => html::label($field_id, Q($this->gettext('googleuser'))),
        'content' => $input_googleuser->show($cmail->config->get('googleuser')),
      );
      
      $field_id = 'rcmfd_google_pass';
      if($cmail->config->get('googlepass'))
        $title = $this->gettext('googlepassisset');
      else
        $title = $this->gettext('googlepassisnotset');
      $input_googlepass = new html_passwordfield(array('name' => '_googlepass', 'id' => $field_id, 'size' => 35, 'title' => $title));
      $args['blocks']['googlecontacts']['options']['googlepass'] = array(
        'title' => html::label($field_id, Q($this->gettext('googlepass'))),
        'content' => $input_googlepass->show(),
      );      

    }
    return $args;
  }

  function save_prefs($args)
  {
    if ($args['section'] == 'addressbooks') {    
      $cmail = cmail::get_instance();
      $args['prefs']['use_google_abook'] = isset($_POST['_use_google_abook']) ? true : false;
      $args['prefs']['googleuser'] = get_input_value('_googleuser', crystal_INPUT_POST);
      $pass = get_input_value('_googlepass', crystal_INPUT_POST);
      if($pass){
        $args['prefs']['googlepass'] = $cmail->encrypt($pass);
      }
    }
    return $args;
  }
    
  function sync_contacts()
  {
    if(isset($_SESSION['google_contacts_sync']))
      return;
    
    $cmail = cmail::get_instance();    
    require_once(dirname(__FILE__) . '/google_contacts_backend.php');
    $CONTACTS = new google_contacts_backend($cmail->db, $cmail->user->ID);
    
    $this->get_contacts();   
    if(!is_array($this->results)){
      return;
    }
    
    $db_table = $cmail->config->get('db_table_google_contacts');
    $query = "DELETE FROM $db_table WHERE user_id=?";

    $res = $cmail->db->query($query, $cmail->user->ID);
    $obj = (array) $this->results;
    
    foreach($obj as $key => $val){
      $insert_id = array();
      $contacts = array();
      $pref = "PREF;";
      $name = explode(" ", $val->name);
      $vcard  = "BEGIN:VCARD\r\nVERSION:3.0\r\n";
      $vcard .= "N:" . $name[1] . ";" . $name[0] . "\r\n";
      $vcard .= "FN:" . $val->name . "\r\n";
      $vcard .= "X-AB-EDIT:" . $val->edit . "\r\n";
      $vcard .= "ORG:" . $val->orgName . "\r\n";
      $vcard .= "TITLE:" . $val->orgTitle . "\r\n";
      $vcard .= "BDAY:" . $val->birthday . "\r\n";
      if(is_array($val->formattedAddresses)){
        $a_type = array(';TYPE=HOME',';TYPE=WORK');
        $i = -1;
        foreach($val->formattedAddresses as $idx => $address){
          $i++;
          $vcard .= "ADR" . $a_type[$i] . ":;;" . str_replace("\n",";",$address) . "\r\n";
        }
      }
      if(is_array($val->emailAddresses)){           
        foreach($val->emailAddresses as $idx => $address){
          $vcard .= "EMAIL;" . $pref . "INTERNET:" . $address . "\r\n";
          $pref = "";
          if($val->name)
            $sname = $val->name . ' (' . $address . ')';
          else
            $sname = $address;       
          $contact = array(
            'email' => $address,
            'name' => $sname,
            'firstname' => $name[0],
            'surname' => $name[1],
            'vcard' => ''
          );
          $contacts[] = $contact;
          $insert_id[] = $CONTACTS->insert($contact, false);
        }
      }
      if(!$contacts[0]){
        $contact = array(
          'email' => '',
          'name' => $val->name,
          'firstname' => $name[0],
          'surname' => $name[1],
          'vcard' => ''
        );
        $insert_id[] = $CONTACTS->insert($contact, false);
      }
      if(is_array($val->phoneNumbers)){
        $a_type = array(';TYPE=CELL',';TYPE=HOME',';TYPE=HOME;TYPE=FAX',';TYPE=WORK',';TYPE=WORK;TYPE=FAX',';TYPE=PAGER');
        $i = -1;
        foreach($val->phoneNumbers as $idx => $number){
          $i++;
          $vcard .= "TEL" . $a_type[$i] . ":" . $number . "\r\n";
        }
      }
      if(is_array($val->websites)){
        $a_type = array(';TYPE=HOME',';TYPE=WORK');
        $i = -1;
        foreach($val->websites as $idx => $site){
          $i++;
          $vcard .= "URL" . $a_type[$i] . ":" . $site . "\r\n";
        }
      }      
      $vcard .= 'END:VCARD';
      $contacts[0]['vcard'] = $vcard;
      $CONTACTS->update($insert_id[0],$contacts[0]); 
    }
    $_SESSION['google_contacts_sync'] = true;
  }
    
  function get_contacts()
  {
    // set credentials for ClientLogin authentication
    $user = $this->user;
    $pass = $this->pass;
    spl_autoload_unregister('crystal_autoload');        
    try {
      // @ToDo: move to init later if needed by other methods
      // load Zend Gdata libraries
      //spl_autoload_register(array('Zend_Loader', 'autoload'));
      if(!file_exists('./plugins/google_contacts/Zend/Loader.php')){
        write_log('errors', 'Plugin google_contacts: Zend GData API not installed (http://framework.zend.com/download/webservices)');
        $this->results = array();
        return;
      }
      require_once './plugins/google_contacts/Zend/Loader.php';
      
      Zend_Loader::loadClass('Zend_Gdata');
      Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
      Zend_Loader::loadClass('Zend_Http_Client');
      Zend_Loader::loadClass('Zend_Gdata_Query');
      Zend_Loader::loadClass('Zend_Gdata_Feed');
    
      // perform login and set protocol version to 3.0
      $client = Zend_Gdata_ClientLogin::getHttpClient(
        $user, $pass, 'cp');
      $gdata = new Zend_Gdata($client);
      $gdata->setMajorProtocolVersion(3);
      
      $cmail = cmail::get_instance();
      $max = $cmail->config->get('google_contacts_max_results');
      if(empty($max))
        $max = 250;
      // perform query and get result feed
      $query = new Zend_Gdata_Query(
        'http://www.google.com/m8/feeds/contacts/default/full?max-results=' . $max);
        
      $feed = $gdata->getFeed($query);
      $title = $feed->title;
      $totals = $feed->totalResults;

      // parse feed and extract contact information
      // into simpler objects
      foreach($feed as $entry){
        $xml = simplexml_load_string($entry->getXML());
        $obj = new stdClass;
        $obj->name = (string) $entry->title->text;
        $obj->edit = $entry->getEditLink()->href;
        $obj->orgName = (string) $xml->organization->orgName; 
        $obj->orgTitle = (string) $xml->organization->orgTitle;
        $obj->birthday = (string) @$xml->birthday->attributes()->when;
        $obj->phoneNumbers = array();
        foreach ($xml->phoneNumber as $n) {
          $obj->phoneNumbers[] = (string) $n;
        }
        $obj->emailAddresses = array();
        foreach ($xml->email as $e) {
          $obj->emailAddresses[] = (string) $e['address'];
        }
        $obj->formattedAddresses = array();
        foreach ($xml->structuredPostalAddress as $a) {
          $obj->formattedAddresses[] = (string) $a->formattedAddress;
        }
        $obj->phoneNumbers = array();
        foreach ($xml->phoneNumber as $p) {
          $obj->phoneNumbers[] = (string) $p;
        }
        $obj->websites = array();
        foreach ($xml->website as $w) {
          $obj->websites[] = (string) $w['href'];
        }
        
        $results[] = $obj;  
      }
    }
    catch (Exception $e) {
      $this->error = $e->getMessage();  
    }
    spl_autoload_register('crystal_autoload');      
    $this->results = $results;
  }
}
?>
