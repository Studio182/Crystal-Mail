<?php header("Content-type: text/javascript"); ?>
/*
+----------------------------------------------------------------+
| ./program/js/smart.php                                         |
|                                                                |
| This file is part of the Crystal Mail Client                   |
| Copyright (C) 2010, Crystal Mail Dev. Team - United States     |
|                                                                |
| Licensed under the GNU GPL                                     |
|                                                                |
| PURPOSE:                                                       |
|   Adds Crystal Mail Smart URL Decoder                          |
|   (More info at http://tracker.cystalmail.net/wiki/smart/)     |
|                                                                |
| KNOWN ISSUES:													 |
| 	Causes SSL warning when non-ssl url is embedded				 |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
| Chris Jones <chris@crystalmail.net>                            |
+----------------------------------------------------------------+
*/

// encoding: utf-8
// $.fn.linkify 1.0 - MIT/GPL Licensed - More info: http://github.com/maranomynet/linkify/
(function(b){var x=/(?:^|["'(\s]|&lt;)(www\..+?\..+?)(?:(?:[:?]|\.+)?(?:\s|$)|&gt;|[)"',])/g,y=/(?:^|["'(\s]|&lt;)((?:(?:https?|ftp):\/\/|mailto:).+?)(?:(?:[:?]|\.+)?(?:\s|$)|&gt;|[)"',])/g,z=function(h){return h.replace(x,'<a href="<``>://$1">$1</a>').replace(y,'<a href="$1">$1</a>').replace(/"<``>/g,'"http')},s=b.fn.linkify=function(c){if(!b.isPlainObject(c)){c={use:(typeof c=='string')?c:undefined,handleLinks:b.isFunction(c)?c:arguments[1]}}var d=c.use,k=s.plugins||{},l=[z],f,m=[],n=c.handleLinks;if(d==undefined||d=='*'){for(var i in k){l.push(k[i])}}else{d=b.isArray(d)?d:b.trim(d).split(/ *, */);var o,i;for(var p=0,A=d.length;p<A;p++){i=d[p];o=k[i];if(o){l.push(o)}}}this.each(function(){var h=this.childNodes,t=h.length;while(t--){var e=h[t];if(e.nodeType==3){var a=e.nodeValue;if(a.length>1&&/\S/.test(a)){var q,r;f=f||b('<div/>')[0];f.innerHTML='';f.appendChild(e.cloneNode(false));var u=f.childNodes;for(var v=0,g;(g=l[v]);v++){var w=u.length,j;while(w--){j=u[w];if(j.nodeType==3){a=j.nodeValue;if(a.length>1&&/\S/.test(a)){r=a;a=a.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');a=b.isFunction(g)?g(a):a.replace(g.re,g.tmpl);q=q||r!=a;r!=a&&b(j).after(a).remove()}}}}a=f.innerHTML;if(n){a=b('<div/>').html(a);m=m.concat(a.find('a').toArray().reverse());a=a.contents()}q&&b(e).after(a).remove()}}else if(e.nodeType==1&&!/^(a|button|textarea)$/i.test(e.tagName)){arguments.callee.call(e)}}});n&&n(b(m.reverse()));return this};s.plugins={mailto:{re:/(?:^|["'(\s]|&lt;)([^"'(\s&]+?@.+\.[a-z]{2,7})(?:([:?]|\.+)?(\s|$)|&gt;|[)"',])/i,tmpl:'<a href="mailto:$1">$1</a>'}}})(jQuery);

jQuery('body').linkify();

/**
 * jquery.youtubin.js
 * Copyright (c) 2009 Jon Raasch (http://jonraasch.com/)
 * Licensed under the Free BSD License (see http://dev.jonraasch.com/youtubin/docs#licensing)
 * 
 * @author Jon Raasch
 *
 * @projectDescription    jQuery plugin to allow simple and unobtrusive embedding of youtube videos with a variety of options
 * 
 * @documentation http://dev.jonraasch.com/youtubin/docs
 *
 * @version 1.2
 * 
 * @requires jquery.js (tested with v 1.3.2)
 * 
 * @optional SwfObject 2
 * 
 * NOT AFFILIATED WITH YOUTUBE
 */


( function( $ ) {    
    var youtubinCount = 0;
    var youtubinMode  = 0;
    
    $.youtubin = function(options, box) {
        var options = options || {};
        
        // if iphone and iphoneBoot not set or true, just die so youtube link can stay
        if ( (typeof options.iphoneBoot == 'undefined' || options.iphoneBoot )  && ( (navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) ) ) return false;
        
        // if first time
        if ( !youtubinMode ) {
            if ( typeof( swfobject ) == 'undefined' ) youtubinMode = 'noScript';
            else youtubinMode = '2';
        }
        
        if ( typeof( box ) == 'undefined' || !box ) {
            options.scope = options.scope || $('body');
            $('a[href^=http://www.youtube.com/watch?v=]', options.scope).youtubin(options);
            
            return false;
        }
        
        // define options
        options.swfWidth  = options.swfWidth || "425";
        options.swfHeight = options.swfHeight || "344";
        options.flashVersion = options.flashVersion || "8";
        options.expressInstall = options.expressInstall || "";
        
        options.flashvars = options.flashvars || {};
        options.params    = options.params || {
            menu : "false",
            loop : "false",
            wmode : "opaque"
        };
        
        options.replaceTime = options.replaceTime || 'auto';
        options.keepLink = options.keepLink || (options.replaceTime == 'click');
        options.wrapper = options.wrapper || '<div class="youtubin-video"></div>';
        
        options.autoplay = typeof options.autoplay != 'undefined' ? options.autoplay : ( options.replaceTime == 'click' );
        
        options.srcOptions = options.srcOptions || '?hl=en&fs=1' + ( options.autoplay ? '&autoplay=1' : '' );
        options.method = options.method || 'href';
        
        options.target = options.target || false;
        
        
        var $box = $(box);
        
        // depending on replaceTime trigger replacement or attach click event
        if (options.replaceTime == 'auto') replaceIt();
        else if (options.replaceTime == 'click') $box.click( function(ev) { ev.preventDefault(); replaceIt(); });
        
        function replaceIt() {
            function checkId($tgt) {
                var boxId = $tgt.attr('id');
                if ( !boxId.length ) {
                    boxId = getNewId();
                    $tgt.attr('id', boxId);
                }
                
                return boxId;
            }
            
            function getNewId() {
                var boxId = 'youtubin-' + youtubinCount;
                youtubinCount++;
                
                return boxId;
            }
            
            var src = $box.attr(options.method);
    
            // build iframe url from youtube link
  if ( src.substr(0,31) == 'http://www.youtube.com/watch?v=' ) src = 'http://www.youtube.com/embed/' + src.substr(31) + options.srcOptions;
        
            // set the target
            if ( options.target ) {
                var $tgt = options.target;
                var boxId = checkId($tgt);
            }
            else if ( options.keepLink ) {
                var boxId = getNewId();
                $box.after($('<div id="'+boxId+'"></div>'));
                
                var $tgt = $('#'+boxId);
                $tgt.css('clear', 'both');
            }
            else {
                var $tgt = $box;
                var boxId = checkId($tgt);
            }
            
            // embed the swf according to youtubinMode
            switch(youtubinMode) {
                case '2' :
                    swfobject.embedSWF(src, boxId, options.swfWidth, options.swfHeight, options.flashVersion, options.expressInstall, options.flashvars, options.params);
                break;
                
                default : 
                    $tgt.html('<iframe frameborder="0" width="' + options.swfWidth + '" height="' + options.swfHeight + '"src="' + src + '"/>');
                break;
            }
            
            // (hack) must redefine boxId here or it will cause error in IE
            //if (options.wrapper) $('#'+boxId).wrap(options.wrapper);
        }
        
    };
    
    $.fn.youtubin = function(options) {
        this.each( function() {new $.youtubin( options, this );});
        return this;
    };
})( jQuery );

//Same as above just modded for vimeo

( function( $ ) {    
    var vimeoinCount = 0;
    var vimeoinMode  = 0;
    
    $.vimeoin = function(options, box) {
        var options = options || {};
        
        // if iphone and iphoneBoot not set or true, just die so vimeo link can stay
        if ( (typeof options.iphoneBoot == 'undefined' || options.iphoneBoot )  && ( (navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) ) ) return false;
        
        // if first time
        if ( !vimeoinMode ) {
            if ( typeof( swfobject ) == 'undefined' ) vimeoinMode = 'noScript';
            else vimeoinMode = '2';
        }
        
        if ( typeof( box ) == 'undefined' || !box ) {
            options.scope = options.scope || $('body');
            $('a[href^=http://vimeo.com/]', options.scope).vimeoin(options);
            
            return false;
        }
        
        // define options
        options.swfWidth  = options.swfWidth || "425";
        options.swfHeight = options.swfHeight || "344";
        options.flashVersion = options.flashVersion || "8";
        options.expressInstall = options.expressInstall || "";
        
        options.flashvars = options.flashvars || {};
        options.params    = options.params || {
            menu : "false",
            loop : "false",
            wmode : "opaque"
        };
        
        options.replaceTime = options.replaceTime || 'auto';
        options.keepLink = options.keepLink || (options.replaceTime == 'click');
        options.wrapper = options.wrapper || '<div class="vimeoin-video"></div>';
        
        options.autoplay = typeof options.autoplay != 'undefined' ? options.autoplay : ( options.replaceTime == 'click' );
        
        options.srcOptions = options.srcOptions || '' + ( options.autoplay ? '' : '' );
        options.method = options.method || 'href';
        
        options.target = options.target || false;
        
        
        var $box = $(box);
        
        // depending on replaceTime trigger replacement or attach click event
        if (options.replaceTime == 'auto') replaceIt();
        else if (options.replaceTime == 'click') $box.click( function(ev) { ev.preventDefault(); replaceIt(); });
        
        function replaceIt() {
            function checkId($tgt) {
                var boxId = $tgt.attr('id');
                if ( !boxId.length ) {
                    boxId = getNewId();
                    $tgt.attr('id', boxId);
                }
                
                return boxId;
            }
            
            function getNewId() {
                var boxId = 'vimeoin-' + vimeoinCount;
                vimeoinCount++;
                
                return boxId;
            }
            
            var src = $box.attr(options.method);

var src=src.split("/");
var src= 'http://player.vimeo.com/video/' + src[3];

            // set the target
            if ( options.target ) {
                var $tgt = options.target;
                var boxId = checkId($tgt);
            }
            else if ( options.keepLink ) {
                var boxId = getNewId();
                $box.after($('<div id="'+boxId+'"></div>'));
                
                var $tgt = $('#'+boxId);
                $tgt.css('clear', 'both');
            }
            else {
                var $tgt = $box;
                var boxId = checkId($tgt);
            }
            
            // embed the swf according to vimeoinMode
            switch(vimeoinMode) {
                case '2' :
                    swfobject.embedSWF(src, boxId, options.swfWidth, options.swfHeight, options.flashVersion, options.expressInstall, options.flashvars, options.params);
                break;
                
                default : 
                    $tgt.html('<iframe frameborder="0" width="' + options.swfWidth + '" height="' + options.swfHeight + '"src="' + src + '"/>');
                break;
            }
            
            // (hack) must redefine boxId here or it will cause error in IE
            //if (options.wrapper) $('#'+boxId).wrap(options.wrapper);
        }
        
    };
    
    $.fn.vimeoin = function(options) {
        this.each( function() {new $.vimeoin( options, this );});
        return this;
    };
})( jQuery );

$(function() {
    $.youtubin();
    $.vimeoin();
});
