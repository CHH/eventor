<?php

namespace Eventor;

class Base
{
    protected
        # Internal: Handle to libevent's event base.
        $handle;

    # Factory for events.
    #
    # fd       - File descriptor which should get watched.
    # events   - One or more event constants. combined with `|`.
    # callback - Gets called when the event happens.
    #
    # Returns a new instance of \Eventor\Event.
    function newEvent($fd, $events = 0, $callback)
    {
        return new Event($fd, $events, $callback);
    }

    function newBuffer($fd, $events = 0)
    {
        $buffer = new Buffer($fd, $events);
        $buffer->base = $this;

        return $buffer;
    }

    # Initializes the event base.
    function __construct()
    {
        $this->handle = event_base_new();
    }

    # Listen for read events on the file descriptor.
    #
    # fd       - Resource, must be castable to file descriptor.
    # callback - Gets called when the event is triggered.
    #
    # Returns an Eventor\Event.
    function read($fd, $callback)
    {
        $event = $this->newEvent($fd, EV_READ, $callback);
        $this->add($event);

        return $event;
    }

    # Listen for write events on the file descriptor.
    #
    # fd       - Resource, must be castable to file descriptor.
    # callback - Gets called when the event is triggered.
    #
    # Returns an Eventor\Event.
    function write($fd, $callback)
    {
        $event = $this->newEvent($fd, EV_WRITE, $callback);
        $this->add($event);

        return $event;
    }

    # Registers the event instance.
    #
    # event - Eventor\Event which should be registered.
    #
    # Returns $this.
    function add($event)
    {
        $ev = event_new();

        event_set($ev, $event->fd, $event->events, function() use ($event) {
            call_user_func($event->callback, $event);
        });

        event_base_set($ev, $this->handle);
        event_add($ev);

        $event->base = $this;
        $event->handle = $ev;

        return $this;
    }

    # Removes the event.
    #
    # event - Eventor\Event
    #
    # Returns $this.
    function remove($event)
    {
        $ev = $event->handle;
        event_del($ev);
        return $this;
    }

    # Starts the event loop.
    #
    # Returns $this.
    function loop()
    {
        event_base_loop($this->handle);
        return $this;
    }

    # Stops the event loop immediately.
    #
    # Returns $this.
    function halt()
    {
        event_base_loopbreak($this->handle);
        return $this;
    }

    # Let's the next event loop iteration complete normally after the
    # timeout expired. Then stops the event loop.
    #
    # timeout - Timeout in microseconds, optional.
    #
    # Returns $this.
    function shutdown($timeout = -1)
    {
        event_base_loopexit($this->handle, $timeout);
        return $this;
    }

    function free()
    {
        event_base_free($this->handle);
    }
}
