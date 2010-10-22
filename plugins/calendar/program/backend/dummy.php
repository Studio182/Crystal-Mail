<?php
/**
 * RoundCube Calendar
 *
 * A dummy backend which simply demonstrates functionality
 * without the need for a fully configured backend.
 *
 * @version 0.2 BETA 2
 * @author Michael Duelli
 * @author Lazlo Westerhof
 * @url http://rc-calendar.lazlo.me
 * @licence GNU GPL
 * @copyright (c) 2010 Lazlo Westerhof - Netherlands
 */
require_once('backend.php');

final class Dummy extends Backend 
{
  public function newEvent($start, $summary, $description, $location, $categories, $allDay) {
  }

  public function editEvent($id, $summary, $description, $location, $categories) {
  }

  public function moveEvent($id, $start, $end, $allDay) {
  }
  
  public function resizeEvent($id, $start, $end) {
  }

  public function removeEvent($id) {
  }
  
  public function getEvents($start, $end) {
    
    $year = date('Y');
    $month = date('n');
    $day = date('j');
    
    $events = array();
    
    $events[]=array( 
      'event_id'    => 0,
      'start'       => mktime(9, 0, 0, $month, $day-1, $year),
      'end'         => mktime(17, 0, 0, $month, $day-1, $year),
      'summary'     => 'Work',
      'description' => 'doing working stuff',
      'location'    => 'the office',
      'categories'  => 'Work',
      'all_day'     => 0,
    );
    
    $events[]=array( 
      'event_id'    => 1,
      'start'       => mktime(0, 0, 0, $month, $day, $year),
      'summary'     => 'Birthday',
      'description' => 'today it\'s my birthday!',
      'location'    => 'home',
      'categories'  => 'Personal',
      'all_day'     => 1,
    );
    
    $events[]=array( 
      'event_id'    => 2,
      'start'       => mktime(9, 0, 0, $month, $day+1, $year),
      'end'         => mktime(17, 0, 0, $month, $day+1, $year),
      'summary'     => 'Work',
      'description' => 'doing working stuff',
      'location'    => 'the office',
      'categories'  => 'Work',
      'all_day'     => 0,
    );
    
    $events[]=array( 
      'event_id'    => 3,
      'start'       => mktime(11, 0, 0, $month, $day+2, $year),
      'end'         => mktime(12, 30, 0, $month, $day+2, $year),
      'summary'     => 'Hair cut',
      'description' => 'getting a new hair cut',
      'location'    => 'barber 2000',
      'categories'  => 'Personal',
      'all_day'     => 0,
    );
    
    $events[]=array( 
      'event_id'    => 4,
      'start'       => mktime(13, 0, 0, $month, $day+2, $year),
      'end'         => mktime(14, 30, 0, $month, $day+2, $year),
      'summary'     => 'Luch',
      'description' => 'lunch with family',
      'location'    => 'lunchroom',
      'categories'  => 'Family',
      'all_day'     => 0,
    );
    
    $events[]=array( 
      'event_id'    => 5,
      'start'       => mktime(0, 0, 0, $month, $day+3, $year),
      'end'         => mktime(0, 0, 0, $month, $day+10, $year),
      'summary'     => 'Holiday',
      'description' => '',
      'location'    => 'Spain',
      'categories'  => 'Holiday',
      'all_day'     => 1,
    );
    
    $events[]=array( 
      'event_id'    => 6,
      'start'       => mktime(16, 0, 0, $month, $day-2, $year),
      'end'         => mktime(23, 0, 0, $month, $day-2, $year),
      'summary'     => 'Doing stuff',
      'description' => 'doing important stuff',
      'location'    => 'the world',
      'all_day'     => 0,
    );

    return $events;
  }
}
?>