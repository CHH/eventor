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
        $handle,
        $base,
        $fd,
        $events = 0,
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
        $this->events = $enable ? $this->events ^ self::PERSIST : $this->events | self::PERSIST;
        return $this;
    }
}
