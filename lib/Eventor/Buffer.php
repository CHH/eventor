<?php

namespace Eventor;

class Buffer
{
    public
        $fd,
        $events = 0,
        $read,
        $write,
        $error,
        $handle,
        $base;

    function __construct($fd, $events)
    {
        $this->fd = $fd;
        $this->events = $events;
    }

    function read($size)
    {
        return event_buffer_read($this->handle, $size);
    }

    function write($data, $size = -1)
    {
        return event_buffer_write($this->handle, $data, $size);
    }

    function setWaterMark($events, $low, $high)
    {
    }

    function setPriority($priority)
    {
    }

    function setTimeout($timeout)
    {
    }

    function enable()
    {
        $eb = event_buffer_new(
            $this->fd,
            $this->getEventHandler($this->read),
            $this->getEventHandler($this->write),
            $this->getEventHandler($this->error)
        );

        event_buffer_base_set($eb, $this->base->handle);
        event_buffer_enable($this->events);
    }

    function disable()
    {
        event_buffer_disable($this->events);
    }

    function free()
    {
        event_buffer_free($this->handle);
    }

    function __destruct()
    {
        $this->free();
    }

    protected function getEventHandler($callback)
    {
        if ($callback === null) {
            return;
        }

        $buffer = $this;

        return function() use ($callback, $buffer) {
            return call_user_func($callback, $buffer);
        };
    }
}
