<?php

namespace Eventor\Test;

use Eventor\Event;

class EventTest extends \PHPUnit_Framework_TestCase
{
    function testNonPersistentEnable()
    {
        $event = new Event(STDIN, Event::READ, function() {});
        $event->setNonPersistent();

        $this->assertEquals(Event::READ, $event->events);
    }

    function testNonPersistentDisable()
    {
        $event = new Event(STDIN, Event::READ, function() {});

        $event->setNonPersistent();
        $event->setNonPersistent(false);

        $this->assertEquals(Event::READ | Event::PERSIST, $event->events);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    function testThrowsInvalidArgumentExceptionWhenInvalidCallback()
    {
        new Event(STDIN, Event::READ, null);
    }
}
