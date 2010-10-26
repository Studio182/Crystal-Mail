/**
 * RoundCube Calendar
 *
 * Plugin to add a calendar to RoundCube.
 *
 * @version 0.2 BETA 2
 * @author Lazlo Westerhof
 * @url http://rc-calendar.lazlo.me
 * @licence GNU GPL
 * @copyright (c) 2010 Lazlo Westerhof - Netherlands
 *
 **/

function PageQuery(q) {
	if(q.length > 1) this.q = q.substring(1, q.length);
	else this.q = null;
	this.keyValuePairs = new Array();
	if(q) {
		for(var i=0; i < this.q.split("&").length; i++) {
			this.keyValuePairs[i] = this.q.split("&")[i];
		}
	}
	this.getKeyValuePairs = function() { return this.keyValuePairs; }
	this.getValue = function(s) {
		for(var j=0; j < this.keyValuePairs.length; j++) {
			if(this.keyValuePairs[j].split("=")[0] == s)
				return this.keyValuePairs[j].split("=")[1];
		}
		return false;
	}
	this.getParameters = function() {
		var a = new Array(this.getLength());
		for(var j=0; j < this.keyValuePairs.length; j++) {
			a[j] = this.keyValuePairs[j].split("=")[0];
		}
		return a;
	}
	this.getLength = function() { return this.keyValuePairs.length; }	
}
function queryString(key){
	var page = new PageQuery(window.location.search); 
	return unescape(page.getValue(key)); 
}

var myevents = new Array();
var idx = -1;

$(document).ready(function() {
  
  // start loading
  cmail.set_busy(true,'loading');

  cmail.addEventListener('plugin.reloadCalendar', reloadCalendar);       
  // get settings
  cmail.addEventListener('plugin.getSettings', setSettings);
  cmail.http_post('plugin.getSettings', '');
  
  function setSettings(response) {
  cmail.set_busy(false,'loading');
  $('#calendar').fullCalendar({
    header: {
      left: '',
      center: 'title',
      right: ''
    }, 
    height : $(window).height() - 130,

    editable: false,

    events: "./?_task=dummy&_action=plugin.getEvents",
    
    monthNames : response.settings['months'],
    monthNamesShort : response.settings['months_short'],
    dayNames : response.settings['days'],
    dayNamesShort : response.settings['days_short'],
    firstDay : response.settings['first_day'],
    firstHour : response.settings['first_hour'],
    timeFormat: response.settings['time_format'],
    axisFormat : response.settings['time_format'],
    defaultView: queryString('_view'),
    allDayText: cmail.gettext('all-day', 'calendar'),

    loading : function(isLoading) {
      if(isLoading) {
        cmail.set_busy(true,'loading');
        cmail.enable_command('plugin.calendar_do_print', false);
        cmail.enable_command('plugin.calendar_toggle_view', false);
      } else {
        cmail.set_busy(false,'loading');
        var mydate = queryString('_date');
        $('#calendar').fullCalendar( 'gotoDate', $.fullCalendar.parseDate(mydate));
        if(myevents.length > 0){
          var agendatable = "<table>\n";
          agendatable = agendatable + "<tr><thead><th colspan='8'>" + $('#calendar').fullCalendar('getView').title + "</th></tr>\n";
          agendatable = agendatable + "<tr>\n";
          agendatable = agendatable + "<th>" + cmail.gettext('day','calendar') + "</th>\n";
          agendatable = agendatable + "<th>" + cmail.gettext('start','calendar') + "</th>\n";
          agendatable = agendatable + "<th>" + cmail.gettext('end','calendar') + "</th>\n";
          agendatable = agendatable + "<th>" + cmail.gettext('location','calendar') + "</th>\n";
          agendatable = agendatable + "<th>" + cmail.gettext('category','calendar') + "</th>\n";
          agendatable = agendatable + "<th>" + cmail.gettext('summary','calendar') + "</th>\n";
          agendatable = agendatable + "<th width='90%'>" + cmail.gettext('description','calendar') + "</th>\n";
          agendatable = agendatable + "</tr></thead><tbody>\n";

          myevents.sort();
          for (var i = 0; i < myevents.length; i++) {
            var temparr = myevents[i].split("_");            
            agendatable = agendatable + myevents[i].replace(temparr[0] + "_", "");
          }
          agendatable = agendatable + "</tbody></table>\n";
          $('#agendalist').html(agendatable);
          $(window).resize(function() {
            $('#print').width($(window).width()-20);
          });
          self.resizeTo(720,800);
        }
      }
    },
    eventRender: function(event, element, view) {
      cmail.enable_command('plugin.calendar_do_print', true);
      cmail.enable_command('plugin.calendar_toggle_view', true);
      var agendalist = "<tr>\n";
      var t_start = $('#calendar').fullCalendar('getView').visStart.getTime();
      var t_end   = $('#calendar').fullCalendar('getView').visEnd.getTime();
      var t_view  = $.fullCalendar.parseDate(queryString('_date'));
          t_view  = t_view.getTime();
      if(t_view >= t_start && t_view <= t_end){
        if(event.start){
          var start = event.start;
          start = $.fullCalendar.parseDate( start );
          var mydate = $.fullCalendar.formatDate( start, "dd MMM yyyy" );
          var myday  = $.fullCalendar.formatDate( start, "ddd" );
          var start  = $.fullCalendar.formatDate( start, "HH:mm" );
        }
        else{
          var mydate = "";
          var myday  = "";
          var start  = "";
        }
        var ldays = new Array();
        ldays['Sun'] = response.settings['days_short'][0];
        ldays['Mon'] = response.settings['days_short'][1];
        ldays['Tue'] = response.settings['days_short'][2];
        ldays['Wed'] = response.settings['days_short'][3];
        ldays['Thu'] = response.settings['days_short'][4];
        ldays['Fri'] = response.settings['days_short'][5];
        ldays['Sat'] = response.settings['days_short'][6];
        agendalist = agendalist + "<td rowspan='2'>&nbsp;" + ldays[myday] + "&nbsp;</td>\n";
        agendalist = agendalist + "<td colspan='2' align='center' nowrap>&nbsp;" + mydate + "&nbsp;</td>\n";
        if(event.end){
          var end = event.end;
          end = $.fullCalendar.parseDate( end );
          end = $.fullCalendar.formatDate( end, "HH:mm" );
        }
        else{
          var end = "&nbsp;";
          end = ($.fullCalendar.parseDate(event.start).getTime() + (120 * 60 * 1000));
          end = $.fullCalendar.formatDate( new Date(end), "HH:mm" );
        }
        agendalist = agendalist + "<td rowspan='2'>" + event.location + "&nbsp;</td>\n";
        agendalist = agendalist + "<td rowspan='2'>" + cmail.gettext(event.className,'calendar') + "&nbsp;</td>\n";
        agendalist = agendalist + "<td rowspan='2'>" + event.title + "&nbsp;</td>\n";
        agendalist = agendalist + "<td rowspan='2' style='WORD-BREAK:BREAK-ALL;'>" + event.description + "&nbsp;</td>\n";
        agendalist = agendalist + "</tr>\n";
        agendalist = agendalist + "<tr>\n";
        if(event.allDay){
          if(event.end)
            agendalist = agendalist + "<td colspan='2' align='center'>&nbsp;" +  $.fullCalendar.formatDate( event.end, "dd MMM yyyy" ) + "&nbsp;</td>\n";
          else
            agendalist = agendalist + "<td colspan='2' align='center'>&nbsp;" +  cmail.gettext('all-day','calendar') + "&nbsp;</td>\n";
        }
        else{
          agendalist = agendalist + "<td>&nbsp;" + start + "&nbsp;</td>\n";
          agendalist = agendalist + "<td>&nbsp;" + end + "&nbsp;</td>\n";
        }
        agendalist = agendalist + "</tr>\n";
        idx = idx + 1;
        myevents[idx] = $.fullCalendar.parseDate( event.start ).getTime() + "_" + agendalist;
      }
    }
    });
    $('#toolbar').show();
  }
  
  // reload calendar
  function reloadCalendar() {
    $('#calendar').fullCalendar( 'refetchEvents');
  }

  // print events
  function printEvents() {
    $('#toolbar').hide();
    self.print();
    $('#toolbar').show();
    return true; 
  }
  cmail.register_command('plugin.calendar_do_print', printEvents);

  // toggle view between agenda and list view
  var curview = 'calendar';
  function toggleView() {
    if(curview == 'agendalist'){
      curview = 'calendar';
      document.location.reload();
    }
    else{
      $('#agendalist').show();
      $('#calendar').hide(); // JS Error on IE7 (addressed to fullcalendar devs)
      curview = 'agendalist';
    }
  }
  cmail.register_command('plugin.calendar_toggle_view', toggleView);
});

  


