<?php
/**
 * RoundCube Calendar
 *
 * CalDAV backend based on exemplary DAViCal / AWL client.
 *
 * @version 0.2 BETA 2
 * @author Michael Duelli
 * @url http://rc-calendar.lazlo.me
 * @licence GNU GPL
 * @copyright (c) 2010 Lazlo Westerhof - Netherlands
 */
require_once('backend.php');
require_once('caldav-client.php');

final class CalDAV extends Backend 
{
  private $cmail = null;
  private $cal = null;
  private $calendar = null;
  
  /**
   * @param object cmail   The RoundCube instance.
   * @param string server   The CalDAV server.
   * @param string user     The user name.
   * @param string pass     The user's password.
   * @param string calendar The user calendar.
   */
  public function __construct($cmail, $server, $user, $pass, $calendar) {
    $this->cmail = $cmail;
    $this->calendar = '/' . $calendar;

    $this->cal = new CalDAVClient($server. "/" . $user, $user, $pass, $calendar /* is ignored currently */);
    $this->cal->setUserAgent('RoundCube');
  }
  
  public function newEvent($start, $summary, $description, $location, $categories, $allDay) {
    // FIXME Implement
  }

  public function editEvent($id, $summary, $description, $location, $categories) {
    // FIXME Implement
  }

  public function moveEvent($id, $start, $end, $allDay) {
    // FIXME Implement. Can be done via editEvent
  }
  
  public function resizeEvent($id, $start, $end) {
    // FIXME Implement. Can be done via editEvent
  }

  public function removeEvent($id) {
    // FIXME Implement.
  }
  
  public function getEvents($start, $end) {
    if (!empty($this->cmail->user->ID)) {
      // Fetch events.
      $result = $this->cal->GetEvents($this->GMT_to_iCalendar($start), $this->GMT_to_iCalendar($end), $this->calendar);

      $events = array();
      foreach ($result as $k => $event) {
        $lines = explode("\n", $event['data']);

        $n = count($lines);
        $eventid = null;

	$flag = true;
	for ($i = 0; $i < $n; $i++) {
	  if ($flag) {
	    if (strpos($lines[$i], "BEGIN:VEVENT") === 0)
	      $flag = false;

	    continue;
	  }

	  if (strpos($lines[$i], "END:VEVENT") === 0)
	    break;

	  if (empty($lines[$i]))
	    continue; // FIXME

	  $tmp = explode(":", $lines[$i]);

	  if (count($tmp) !== 2)
	    continue; // FIXME

	  list($id, $value) = $tmp;

	  if (!isset($id) || !isset($value))
	    continue; // FIXME

	  if (is_null($eventid) && strpos($id, "UID") === 0)
	    $eventid = $value;
	  elseif (!isset($event['start']) && strpos($id, "DTSTART") === 0) {
	    $event['start'] = $this->iCalendar_to_Unix($value);
            
	    // Check for all-day event.
	    $event['all_day'] = (strlen($value) === 8 ? 0 : 1);
	  } elseif (!isset($event['end']) && strpos($id, "DTEND") === 0)
	    $event['end'] = $this->iCalendar_to_Unix($value);
	  elseif (!isset($event['summary']) && strpos($id, "SUMMARY") === 0)
	    $event['summary'] = $value;
	  elseif (!isset($event['description']) && strpos($id, "DESCRIPTION") === 0) {
	    $event['description'] = $value;
	    
	    // FIXME Problem with multiple lines!
//	    if ($i+1 < $n && $lines[$i+1] does not contain keyword...) {
//              Add line to description
//		$i++;
//          }
	  } elseif (!isset($event['location']) && strpos($id, "LOCATION") === 0)
	    $event['location'] = $value;
	  elseif (!isset($event['categories']) && strpos($id, "CATEGORIES") === 0)
	    $event['categories'] = $value;
	}
	
	if (!isset($event['description']))
	  $event['description'] = "";
	if (!isset($event['summary']))
	  $event['summary'] = "";
	if (!isset($event['location']))
	  $event['location'] = "";
	if (!isset($event['categories']))
	  $event['categories'] = "";
        
        $events[]=array( 
          'event_id'    => $eventid,
          'start'       => $event['start'],
          'end'         => $event['end'],
          'summary'     => $event['summary'],
          'description' => $event['description'],
          'location'    => $event['location'],
          'categories'  => $event['categories'],
          'allDay'      => $event['all_day'],
        );
      }

      return $events;
    }
  }

  /**
   * Convert a GMT time stamp ('Y-m-d H:i:s') to the iCalendar format as defined in
   * RFC 5545, Section 3.2.19, http://tools.ietf.org/html/rfc5545#section-3.2.19.
   *
   * @param timestamp A GMT time stamp ('Y-m-d H:i:s')
   * @return An iCalendar time stamp, e.g. yyyymmddThhmmssZ
   */
  private function GMT_to_iCalendar($timestamp) {
    $unix_timestamp = strtotime($timestamp);
    return date("Ymd", $unix_timestamp) . "T" . date("His", $unix_timestamp) . "Z";
  }

  /**
   * Convert a time stamp in iCalendar format as defined in
   * RFC 5545, Section 3.2.19, http://tools.ietf.org/html/rfc5545#section-3.2.19
   * to a Unix time stamp. Further conversion is done in jsonEvents.
   *
   * @param timestamp An iCalendar time stamp, e.g. yyyymmddThhmmssZ
   * @return A Unix time stamp
   */
  private function iCalendar_to_Unix($timestamp) {
    return strtotime($timestamp);
  }
}
?>