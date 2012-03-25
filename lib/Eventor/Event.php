<?php

namespace Eventor;

class Event
{
    const
        READ    = EV_READ,
        WRITE   = EV_WRITE,
        PERSIST = EV_PERSIST,
        SIGNAL  = EV_SIGNAL,
        TIMEOUT = EV_TIMEOUT;

    public
        # Handle to the libevent "event" resource (only available after the
        # event was added to a base).
        $handle,

        # Instance of the event base (only available after the
        # event was added to a base).
        $base,

        # File descriptor which should be watched.
        $fd,

        # Combination of event flags.
        $events = 0,

        # Gets called when the event is triggered.
        $callback;

    function __construct($fd, $listen = 0, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException("No valid callback given.");
        }

        $this->fd = $fd;
        $this->events = $listen | self::PERSIST;
        $this->callback = $callback;
    }

    function setNonPersistent($enable = true)
    {
        if ($enable) {
            $this->events ^= self::PERSIST;
        } else {
            $this->events |= self::PERSIST;
        }
        return $this;
    }

    function free()
    {
        if (!$this->handle) {
            throw new \UnexpectedValueException("Event is not registered in a base.");
        }
        event_free($this->handle);
    }
}
