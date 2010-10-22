/* Accounts interface */

function accounts_validate(){

    var input_dn = crystal_find_object('account_dn');
    var input_id = crystal_find_object('account_id');
    var input_pw = crystal_find_object('account_pw');
    var input_pw_conf = crystal_find_object('account_pw_conf');
    var input_host = crystal_find_object('account_host');
    var input_add = crystal_find_object('add');
 
    if(input_dn.value == ""){
      parent.cmail.display_message(cmail.gettext('dnempty','accounts'), 'error');    
      input_dn.focus();
      return false;    
    }
    if(input_id.value == ""){
      parent.cmail.display_message(cmail.gettext('userempty','accounts'), 'error');    
      input_id.focus();
      return false;    
    }
    if(input_pw.value == "" && input_add.value == 1){
      parent.cmail.display_message(cmail.gettext('passwordempty','accounts'), 'error');    
      input_pw.focus();
      return false;    
    }
    if(input_pw.value != input_pw_conf.value){
      parent.cmail.display_message(cmail.gettext('passwordnotmatch','accounts'), 'error');    
      input_pw_conf.focus();
      return false;    
    }
    if(input_host.value == ""){
      parent.cmail.display_message(cmail.gettext('hostempty','accounts'), 'error');    
      input_pw.focus();
      return false;    
    }
    return true;    
}

function switch_account(id){

    document.location.href="./?_task=mail&_action=plugin.accounts&_mbox=INBOX&_switch=" + id;
}