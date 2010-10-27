<?php

/*
 +-----------------------------------------------------------------------+
 | program/include/crystal_result_set.php                                  |
 |                                                                       |
 | This file is part of the Crystal Mail client                     |
 | Copyright (C) 2006-2010, Crystal Mail Dev. - United States                 |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Class representing an address directory result set                  |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <roundcube@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id$

*/


/**
 * crystalmail result set class.
 * Representing an address directory result set.
 *
 * @package Addressbook
 */
class crystal_result_set
{
    var $count = 0;
    var $first = 0;
    var $current = 0;
    var $records = array();


    function __construct($c=0, $f=0)
    {
        $this->count = (int)$c;
        $this->first = (int)$f;
    }

    function add($rec)
    {
        $this->records[] = $rec;
    }
  
    function iterate()
    {
        return $this->records[$this->current++];
    }
  
    function first()
    {
        $this->current = 0;
        return $this->records[$this->current++];
    }
  
    // alias for iterate()
    function next()
    {
        return $this->iterate();
    }
  
    function seek($i)
    {
        $this->current = $i;
    }
  
}
