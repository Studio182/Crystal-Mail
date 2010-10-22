/**
 * RoundCube Calendar
 *
 * Plugin to add a calendar to RoundCube.
 *
 * @version 0.2 BETA 2
 * @author Lazlo Westerhof
 * @author Roland Liebl
 * @url http://rc-calendar.lazlo.me
 * @licence GNU GPL
 * @copyright (c) 2010 Lazlo Westerhof - Netherlands
 *
 **/

/* calendar initialization */
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
      left: 'prev,next today',
      center: 'title',
      right: 'agendaDay ,agendaWeek, month'
    },
    height : $(window).height() - 100,

    editable: true,

    events: "./?_task=dummy&_action=plugin.getEvents",
    
    monthNames : response.settings['months'],
    monthNamesShort : response.settings['months_short'],
    dayNames : response.settings['days'],
    dayNamesShort : response.settings['days_short'],
    firstDay : response.settings['first_day'],
    firstHour : response.settings['first_hour'],
    slotMinutes : 60/response.settings['timeslots'],
    timeFormat: response.settings['time_format'],
    axisFormat : response.settings['time_format'],
    defaultView: response.settings['default_view'],
    allDayText: cmail.gettext('all-day', 'calendar'),

    buttonText: {
      today: response.settings['today'],
      day: cmail.gettext('day', 'calendar'),
      week: cmail.gettext('week', 'calendar'),
      month: cmail.gettext('month', 'calendar')
    },

    loading : function(isLoading) {
      if(isLoading) {
        cmail.enable_command('plugin.calendar_print', false);
        cmail.set_busy(true,'loading');
      } else {
        cmail.set_busy(false,'loading');
      }
    },    
    eventRender: function(event, element, view) {
      cmail.enable_command('plugin.calendar_print', true);
      if(view.name != "month") {
        if (event.className) {
          if(!event.allDay)
            element.find('span.fc-event-title').after("<span class=\"fc-event-categories\">"+cmail.gettext(event.className, 'calendar')+"</span>");
        }
        if (event.location) {
          element.find('span.fc-event-title').after("<span class=\"fc-event-location\">@"+event.location+"</span>");
        }
        if (event.description) {
          if(!event.allDay){
            element.find('span.fc-event-title').after("<span class=\"fc-event-description\">"+event.description+"</span>");
          }
        }
      }
    },
    eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
      if(event.end == null) {
        event.end = event.start;
      }
      // send request to RoundCube
      cmail.http_post('plugin.moveEvent', '_event_id='+event.id+'&_start='+event.start.getTime()/1000+'&_end='+event.end.getTime()/1000+'&_allDay='+allDay);
    },
    eventResize : function(event, delta) {
      // send request to RoundCube
      cmail.http_post('plugin.resizeEvent', '_event_id='+event.id+'&_start='+event.start.getTime()/1000+'&_end='+event.end.getTime()/1000);
    },
    dayClick: function(date, allDay, jsEvent, view) {
         var $dialogContent = $("#event");
         resetForm($dialogContent);
         var summary = $dialogContent.find("input[name='summary']");
         var description = $dialogContent.find("textarea[name='description']");
         var categories = $dialogContent.find("select[name='categories']");
         var location = $dialogContent.find("input[name='location']");

         var save = cmail.gettext('save', 'calendar');
         var cancel = cmail.gettext('cancel', 'calendar');
         var buttons = {};
         buttons[save] = function() {
           // send request to RoundCube
           cmail.http_post('plugin.newEvent', '_start='+date.getTime()/1000+'&_summary='+summary.val()+'&_description='+description.val()+'&_location='+location.val()+'&_categories='+categories.val()+'&_allDay='+allDay);

           $dialogContent.dialog("close");
         };
         buttons[cancel] = function() {
           $dialogContent.dialog("close");
         };

         $dialogContent.dialog({
            modal: true,
            title: cmail.gettext('new_event', 'calendar'),
            close: function() {
               $dialogContent.dialog("destroy");
               $dialogContent.hide();
            },
            buttons: buttons
         }).show();
      },
      eventClick : function(event) {
         var $dialogContent = $("#event");
         resetForm($dialogContent);
         var summary = $dialogContent.find("input[name='summary']").val(event.title);
         var description = $dialogContent.find("textarea[name='description']").val(event.description);
         var location = $dialogContent.find("input[name='location']").val(event.location);
         var categories = $dialogContent.find("select[name='categories']").val(event.className);

         var save = cmail.gettext('save', 'calendar');
         var remove = cmail.gettext('remove', 'calendar');
         var cancel = cmail.gettext('cancel', 'calendar');
         var buttons = {};
         buttons[save] = function() {
          event.title = summary.val();
          event.description = description.val();
          event.location = location.val();
          event.className = categories.val();

          // send request to RoundCube
          cmail.http_post('plugin.editEvent', '_event_id='+event.id+'&_summary='+event.title+'&_description='+description.val()+'&_location='+location.val()+'&_categories='+categories.val());

          $('#calendar').fullCalendar('updateEvent', event);
          $dialogContent.dialog("close");
         };
         buttons[remove] = function() {
          // send request to RoundCube
          cmail.http_post('plugin.removeEvent', '_event_id='+event.id);

          $('#calendar').fullCalendar('removeEvents', event.id);

          $dialogContent.dialog("close");
         };
         buttons[cancel] = function() {
           $dialogContent.dialog("close");
         };

         $dialogContent.dialog({
            modal: true,
            title: cmail.gettext('edit_event', 'calendar'),
            close: function() {
               $dialogContent.dialog("destroy");
               $dialogContent.hide();
            },
            buttons: buttons
         }).show();
      }
    });
    $('#toolbar').show();
  }
  
  // reload calendar
  function reloadCalendar() {
    $('#calendar').fullCalendar( 'refetchEvents');
  }
  
  // reset form
  function resetForm($dialogContent) {
    $dialogContent.find("input").val("");
    $dialogContent.find("textarea").val("");
    $dialogContent.find("select").val("");
  }
  
  /* enable GUI commands */
  /* export events */
  function exportEvents() {
    return true;
  }
  cmail.register_command('plugin.exportEvents', exportEvents, true);
  
  /* print events */
  var calpopup;
  function previewPrintEvents(){
    var url = './?_task=dummy&_action=plugin.calendar_print';
    url = url + '&_view='  + escape($('#calendar').fullCalendar('getView').name.replace('agenda','basic'));
    url = url + '&_date='   + escape($('#calendar').fullCalendar('getDate'));
    calpopup = window.open(url, "Print", "width=720,height=740,location=0,resizable=1,scrollbars=1");
    calpopup.focus();
    return true;
  }
  cmail.register_command('plugin.calendar_print', previewPrintEvents);

});