<?php
/**
 * RoundCube Calendar
 *
 * Database backend
 *
 * @version 0.2 BETA 2
 * @author Lazlo Westerhof
 * @author Michael Duelli
 * @url http://rc-calendar.lazlo.me
 * @licence GNU GPL
 * @copyright (c) 2010 Lazlo Westerhof - Netherlands
 */
require_once('backend.php');

final class Database extends Backend 
{
  private $cmail;
  
  public function __construct($cmail) {
    $this->cmail = $cmail;
  }
  
  public function newEvent($start, $summary, $description, $location, $categories, $allDay) {
    if (!empty($this->cmail->user->ID)) {
      $query = $this->cmail->db->query(
        "INSERT INTO events
         (user_id, start, end, summary, description, location, categories, all_day)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        $this->cmail->user->ID,
        $start,
        $start,
        $summary,
        $description,
        $location,
        $categories,    
        $allDay
      );
      $this->cmail->db->insert_id('events');
    }
  }

  public function editEvent($id, $summary, $description, $location, $categories) {
    if (!empty($this->cmail->user->ID)) {
      $query = $this->cmail->db->query(
        "UPDATE events 
         SET summary=?, description=?, location=?, categories=?
         WHERE event_id=?
         AND user_id=?",
        $summary,
        $description,
        $location,
        $categories,
        $id,
        $this->cmail->user->ID
      );
    }
  }

  public function moveEvent($id, $start, $end, $allDay) {
    if (!empty($this->cmail->user->ID)) {
      $query = $this->cmail->db->query(
        "UPDATE events 
         SET start=?, end=?, all_day=?
         WHERE event_id=?
         AND user_id=?",
        $start,
        $end,
        $allDay,
        $id,
        $this->cmail->user->ID
      );
    }
  }
  
  public function resizeEvent($id, $start, $end) {
    if (!empty($this->cmail->user->ID)) {
      $query = $this->cmail->db->query(
        "UPDATE events 
         SET start=?, end=?
         WHERE event_id=?
         AND user_id=?",
        $start,
        $end,
        $id,
        $this->cmail->user->ID
      );
    }
  }

  public function removeEvent($id) {
    if (!empty($this->cmail->user->ID)) {
      $query = $this->cmail->db->query(
        "DELETE FROM events
         WHERE event_id=?
         AND user_id=?",
         $id,
         $this->cmail->user->ID
      );
    }
  }
  
  public function getEvents($start, $end) {
    if (!empty($this->cmail->user->ID)) {

      $result = $this->cmail->db->query(
        "SELECT * FROM events 
         WHERE user_id=?",
         $this->cmail->user->ID
       );

      $events = array(); 
      while ($result && ($event = $this->cmail->db->fetch_assoc($result))) {
        $events[]=array( 
          'event_id'    => (int) $event['event_id'], 
          'start'       => (string) $this->fromGMT($event['start']), 
          'end'         => (string) $this->fromGMT($event['end']), 
          'summary'     => (string) $event['summary'], 
          'description' => (string) $event['description'],
          'location'    => (string) $event['location'],
          'categories'  => (string) $event['categories'],
          'all_day'     => (int) $event['all_day'],
        ); 
      }

      return $events;
    }
  }
  
  private function fromGMT($datetime) {
    if ($this->cmail->config->get('timezone') === "auto") {
      $tz = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : date('Z')/3600;
    } else {
      $tz = $this->cmail->config->get('timezone');
      if($this->cmail->config->get('dst_active')) {
        $tz++;
      }
    }
    
    $timestamp = strtotime($datetime) + ($tz * 3600);
    
    return $timestamp;
  }
}
?>