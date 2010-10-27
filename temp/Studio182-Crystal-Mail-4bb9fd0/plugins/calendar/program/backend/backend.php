<?php
/**
 * RoundCube Calendar
 *
 * Basis for all calendar backends
 *
 * @version 0.2 BETA 2
 * @author Lazlo Westerhof
 * @author Michael Duelli
 * @url http://rc-calendar.lazlo.me
 * @licence GNU GPL
 * @copyright (c) 2010 Lazlo Westerhof - Netherlands
 */
abstract class Backend
{
  /**
   * Add a single event to the database
   *
   * @param  integer Event identifier
   * @param  integer Event's start
   * @param  string  Event's summary
   * @param  string  Event's description
   * @param  string  Event's location
   * @param  string  Event's category
   * @param  integer Event allDay state
   * @access public
   */
  abstract public function newEvent($start, $summary, $description, $location, $categories, $allDay);

  /**
   * Edit a single event
   *
   * @param  integer Event identifier
   * @param  string  Event's title
   * @param  string  Event's location
   * @param  string  Event's category
   * @access public
   */
  abstract public function editEvent($id, $title, $description, $location, $categories);

  /**
   * Move a single event
   *
   * @param  integer Event identifier
   * @param  integer Event's new start
   * @param  integer Event's new end
   * @param  integer Event allDay state
   * @access public
   */
  abstract public function moveEvent($id, $start, $end, $allDay);

  /**
   * Resize a single event
   *
   * @param  integer Event identifier
   * @param  integer Event's new start
   * @param  integer Event's new end
   * @access public
   */
  abstract public function resizeEvent($id, $start, $end);
  
  /**
   * Remove a single event from the database
   * 
   * @param  integer Event identifier
   * @access public
   */
  abstract public function removeEvent($id);
}
?>