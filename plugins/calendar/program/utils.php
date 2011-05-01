<?php
/**
 * RoundCube Calendar
 *
 * Some utility functions.
 * - Import from and export to iCalendar (.ics) format.
 * - Convert events from backend to JSON.
 *
 * @version 0.2 BETA 2
 * @author Lazlo Westerhof
 * @author Michael Duelli
 * @url http://rc-calendar.lazlo.me
 * @licence GNU GPL
 * @copyright (c) 2010 Lazlo Westerhof - Netherlands
 */
 



 
class Utils
{
  private $cmail = null;
  private $backend = null;

  public function __construct($cmail, $backend) {
    $this->cmail = $cmail;
    $this->backend = $backend;
  }

  /**
   * Import events from iCalendar format
   *
   * @param  array Associative events array
   * @access public
   */
  public function importEvents($events) {
    //TODO
    // for ($events as $event)
    //   $this->backend->newEvent(...);
  }
  
  /**
   * Export events to iCalendar format
   *
   * @param  integer Start time events window ('Y-m-d H:i:s')
   * @param  integer End time events window ('Y-m-d H:i:s')
   * @return string  Events in iCalendar format (http://tools.ietf.org/html/rfc5545)
   * @access public
   */
  public function exportEvents($start, $end) {
    if (!empty($this->cmail->user->ID)) {
      $events = $this->backend->getEvents($start, $end);

      $ical = "BEGIN:VCALENDAR\n";
      $ical .= "VERSION:2.0\n";
      $ical .= "PRODID:-//Crystal Mail//NONSGML Calendar//EN\n";
      foreach ($events as $event) {
        $ical .= "BEGIN:VEVENT\n";
        $ical .= "DTSTART:" . date('Ymd\THis\Z',$event['start'] - date('Z')) . "\n";
        if($start != $end) {
          $ical .= "DTEND:" . date('Ymd\THis\Z',$event['end'] - date('Z')) . "\n";
        }
        $ical .= "SUMMARY:" . $event['summary'] . "\n";
        $ical .= "DESCRIPTION:" . $event['description'] . "\n";
        if(!empty($event['location'])) {
          $ical .= "LOCATION:" . $event['location'] . "\n";
        }
        if(!empty($event['categories'])) {
          $ical .= "CATEGORIES:" . strtoupper($event['categories']) . "\n";
        }
        $ical .= "END:VEVENT\n";
      }
      $ical .= "END:VCALENDAR";

      return $ical;
    }
  }

  /**
   * Get events from database as JSON
   *
   * @param  integer Start time events window ('Y-m-d H:i:s')
   * @param  integer End time events window ('Y-m-d H:i:s')
   * @return string  JSON encoded events
   * @access public
   */
  public function jsonEvents($start, $end) {
    $events = $this->backend->getEvents($start, $end);
    
    $json = array();
    foreach ($events as $event) {
      $json[]=array( 
        'id'    => $event['event_id'],
        'start' => date('c', $event['start']), // ISO 8601 date (added in PHP 5)
        'end'   => date('c', $event['end']), // ISO 8601 date (added in PHP 5)
        'title' => $event['summary'],
        'description'  => $event['description'],
        'location'    => $event['location'],
        'className'  => $event['categories'],
        'allDay'=> ($event['all_day'] == 1)?true:false,
      );
    }
    return json_encode($json);
  }

  /**
   * Get events from database as an associative array
   *
   * @param  integer Start time events window ('Y-m-d H:i:s')
   * @param  integer End time events window ('Y-m-d H:i:s')
   * @return string  Associative events array
   * @access public
   */
  public function arrayEvents($start, $end) {
    $events = $this->backend->getEvents($start, $end);

    return $events;
  }
}
?>