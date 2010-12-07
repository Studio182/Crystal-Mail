/*
 +-----------------------------------------------------------------------+
 | crystalmail editor js library                                           |
 |                                                                       |
 | This file is part of the crystalmail web development suite              |
 | Copyright (C) 2006, crystalmail Dev, - Switzerland                      |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Eric Stadtherr <estadtherr@gmail.com>                         |
 +-----------------------------------------------------------------------+

 $Id: editor.js 000 2006-05-18 19:12:28Z crystalmail $
*/

// Initialize HTML editor
function cmail_editor_init(skin_path, editor_lang, spellcheck, mode)
{
  if (mode == 'identity')
    tinyMCE.init({
      mode : 'textareas',
      editor_selector : 'mce_editor',
      apply_source_formatting : true,
      theme : 'advanced',
      skin  : 'o2k7',
      language : editor_lang,
      content_css : skin_path + '/editor_content.css',
      plugins: 'paste',
      theme_advanced_toolbar_location : 'top',
      theme_advanced_toolbar_align : 'left',
      theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,separator,outdent,indent,charmap,hr,link,unlink,code,forecolor',
      theme_advanced_buttons2 : ',fontselect,fontsizeselect',
      theme_advanced_buttons3 : '',
      relative_urls : false,
      remove_script_host : false,
      gecko_spellcheck : true
    });
  else // mail compose
    tinyMCE.init({ 
      mode : 'textareas',
      editor_selector : 'mce_editor',
      accessibility_focus : false,
      apply_source_formatting : true,
      theme : 'advanced',
      skin : 'o2k7',
      language : editor_lang,
      plugins : 'paste,emotions,media,nonbreaking,table,searchreplace,visualchars,directionality' + (spellcheck ? ',spellchecker' : ''),
      theme_advanced_buttons1 : 'bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,outdent,indent,ltr,rtl,blockquote,|,forecolor,backcolor,fontselect,fontsizeselect',
      theme_advanced_buttons2 : 'link,unlink,code,|,emotions,charmap,image,media,|,search' + (spellcheck ? ',spellchecker' : '') + ',undo,redo',
      theme_advanced_buttons3 : '',
      theme_advanced_toolbar_location : 'top',
      theme_advanced_toolbar_align : 'left',
      extended_valid_elements : 'font[face|size|color|style],span[id|class|align|style]',
      content_css : skin_path + '/editor_content.css',
      external_image_list_url : 'program/js/editor_images.js',
      spellchecker_languages : (cmail.env.spellcheck_langs ? cmail.env.spellcheck_langs : 'Dansk=da,Deutsch=de,+English=en,Espanol=es,Francais=fr,Italiano=it,Nederlands=nl,Polski=pl,Portugues=pt,Suomi=fi,Svenska=sv'),
      spellchecker_rpc_url : '?_task=utils&_action=spell&tiny=1',
      gecko_spellcheck : true,
      relative_urls : false,
      remove_script_host : false,
      rc_client : cmail,
      oninit : 'cmail_editor_callback'
    });
}

// react to real individual tinyMCE editor init
function cmail_editor_callback(editor)
{
  var editor, elem = crystal_find_object('_from');
  if (elem && elem.type=='select-one')
    cmail.change_identity(elem);
  // set tabIndex and set focus to element that was focused before
  cmail_editor_tabindex(cmail.env.compose_focus_elem && cmail.env.compose_focus_elem.id == cmail.env.composebody);
}

// set tabIndex on tinyMCE editor
function cmail_editor_tabindex(focus)
{
  if (cmail.env.task == 'mail') {
    var editor = tinyMCE.get(cmail.env.composebody);
    if (editor) {
      var textarea = editor.getElement();
      var node = editor.getContentAreaContainer().childNodes[0];
      if (textarea && node)
        node.tabIndex = textarea.tabIndex;
      if (focus)
        editor.getWin().focus();
    }
  }
}

// switch html/plain mode
function cmail_toggle_editor(select, textAreaId, flagElement)
{
  var flag, ishtml;

  if (select.tagName != 'SELECT')
    ishtml = select.checked;
  else
    ishtml = select.value == 'html';

  var res = cmail.command('toggle-editor', {id:textAreaId, mode:ishtml?'html':'plain'});

  if (ishtml) {
    // #1486593
    setTimeout("cmail_editor_tabindex(true);", 500);
    if (flagElement && (flag = crystal_find_object(flagElement)))
      flag.value = '1';
  }
  else {
    if (!res && select.tagName == 'SELECT')
      select.value = 'html';
    if (flagElement && (flag = crystal_find_object(flagElement)))
      flag.value = '0';

    crystal_find_object(cmail.env.composebody).focus();
  }
}
