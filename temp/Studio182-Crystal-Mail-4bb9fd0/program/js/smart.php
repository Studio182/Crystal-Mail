$(function(){
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