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
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
| Chris Jones <chris@crystalmail.net>                            |
+----------------------------------------------------------------+
*/


// Smart isn't ready for primetime yet

/*$(function(){
    $(".message-htmlpart").append("<div id='smartmedia'><h1>Auto Detected Media</h1></div>");
   
   $(".message-htmlpart").each(function() {
      // get paragraph text
      var mystring = $(this).text(); 
      // regular expression for a youtube video
      var expression = /http:\/\/(\w{0,3}\.)?youtube\.\w{2,3}\/watch\?v=[\w-]{11}/gi; 
      // get an array of matched video urls
      var videoUrl = mystring.match(expression);
      if (videoUrl !== null) {
         // for each video url change it to embedded
         for(count = 0; count < videoUrl.length; count++) {
            // replace url with embedded video
            mystring = mystring.replace(videoUrl[count], embedVideo(videoUrl[count]));      
         }
      } 
   });
   
   function embedVideo(content) {
      var youtubeUrl = content;
      var youtubeId = youtubeUrl.match(/=[\w-]{11}/);
      var strId = youtubeId[0].replace(/=/,'');
      var result = '<div class="embedded_video">\n';
      result += '<iframe class="youtube-player" type="text/html" width="640" height="385" src="http://www.youtube.com/embed/' + strId + '"frameborder="0"></iframe>\n';
  result += '</div>\n';
      $('#smartmedia').append(result);
   }
   
});
*/