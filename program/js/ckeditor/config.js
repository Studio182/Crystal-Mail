/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	 config.uiColor = '#E4EBF5';

};
   var editor = CKEDITOR.replace("ckeditor", { 
      on : {
         // maximize the editor on startup
         'instanceReady' : function( evt ) {
            evt.editor.resize("100%", editorElem.clientHeight);
         }
      }
   });