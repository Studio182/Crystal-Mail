// hide or display settings part

if (window.cmail && typeof settings_parts != 'undefined') {
  cmail.addEventListener('init', function(evt) {
    try{
      document.getElementById("userprefscontainer").style.padding = "0px 0 15px 15px";
    }
    catch(e){
    }
    for ( var i in settings_parts ){
      try{
        document.getElementById( settings_parts[i] ).style.display = "none";
      }
      catch(e){
        break;
       }
    }
    if(typeof settings_section != 'undefined'){
      // overwrite by URL &_part value
      var url = document.location.href;
      var temparr = url.split("_part=");
      settings_section = temparr[temparr.length - 1];
      // catch not existing settings_section value
      try{
        document.getElementById( settings_section ).style.display = "block";
        document.getElementById( "rcmfd_settings_section" ).value = settings_section;
      }
      catch(e){
        try{
          part = document.getElementById( "rcmfd_settings_section" ).value;
          if(part == "")
            part = "general";
          document.getElementById( "rcmfd_settings_section" ).value = part;
          document.getElementById( part ).style.display = "block";
        }
        catch(e){
          document.getElementById( settings_parts[0] ).style.display = "block";        
          document.getElementById( "rcmfd_settings_section" ).value = "general";
        }        
      }
    }
    else{     
      try{
        part = document.getElementById( "rcmfd_settings_section" ).value;
        if(part == "")
          part = "general";                
        document.getElementById( "rcmfd_settings_section" ).value = part;
        document.getElementById( part ).style.display = "block";        
      }
      catch(e){
        document.getElementById( settings_parts[0] ).style.display = "block";      
        document.getElementById( "rcmfd_settings_section" ).value = "general";
      }   
    }
  })
}

function settings_hide_parts(){
  if (window.cmail && typeof settings_parts != 'undefined') {
    for ( var i in settings_parts ){
      document.getElementById( settings_parts[i] ).style.display = "none";
    }  
  }
}

function settings_show_part(part){
  settings_hide_parts();
  if (window.cmail && typeof settings_parts != 'undefined') {
    document.getElementById( part ).style.display = "block";
    document.getElementById( "rcmfd_settings_section" ).value = part;
    form.action = form.action + "?_part=" + part;
  }
}