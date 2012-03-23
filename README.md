# Eventor

_A sane interface to PHP's [libevent Extension][]_

[libevent extension]: http://php.net/libevent

## Intro

	<?php
	
	$base = new Eventor\Base;
	$fd = STDIN;
	
	# Call the callback every time data is
	# written to $fd.
	$event = $base->read($fd, function($event) {
		static $requests = 0;
		
		$requests++;
		
		if ($requests == 10) {
			$event->base->exit();
		}
		
		echo fgets($event->fd);
	});
	
	$base->loop();

## class Eventor

### Methods

#### Eventor\Event read(resource $fd, callable $callback)

Creates a new read event and registers it in the event loop with the
given `$callback`.

When data is written to `$fd` the callback is called and receives an instance of `Eventor\Event` as sole argument.

All events are by default persistent and can be made non-persistent
by calling the returned event's `setNonPersistent()` method (the same
applies when using `write()`).

#### Eventor\Event write(resource $fd, callable $callback)

Creates a new write event and registers it in the event loop with the
given `$callback`.

When data can be written to `$fd` the callback is called and receives an instance of
`Eventor\Event` as sole argument.

#### add(Eventor\Event $event)

Register a manually created Event instance in the event loop.

#### delete(Eventor\Event $event)

Delete the event from the event loop, which causes it to trigger
the callback never again.

#### loop()

Starts the event loop, this blocks until all events are dispatched (if the events
are not persistent) or one of the `breakLoop()` or `exit()` methods is called
on the `Eventor\Base`.

#### stop()

Breaks the loop immediately, similar to using the `break` keyword in a loop.

#### shutdown(int $timeout = -1)

Gracefully shut down the event loop by finishing the next dispatch and
exiting after the timeout. The `$timeout` argument receives the time in
microseconds when the loop should exit, when omitted the loop is exited after
the next dispatch.

## class Eventor\Event

The `Event` class is useful for more low level access to the libevent extensions'
functionality and is also passed to the event handlers when they're called.

### Methods

#### __construct(resource $fd, int $flags, callable $callback)

Initializes the event instance with the file descriptor and the
flags. Flags are constants of the libevent extension.

Example:

	<?php
	
	$base = new \Eventor\Base;
	
	$event = new \Eventor\Event(STDIN, EV_READ | EV_PERSIST, function($event) {
		echo fgets($event->fd);
	});
	
	$base->add($event);
	$base->loop();

#### setNonPersistent($enable = true)

Sets the event to non-persistent mode, by registering it without setting
the `EV_PERSIST` flag. This causes the event handler to get triggered only once.

### Properties

#### base

The instance of the `Eventor\Base`, to which this event is bound. Is `null` in the case this event is not bound to any base.

#### callback

The callback which gets called when the event is ready.

#### fd

The `fd` property is the file descriptor, to which this event is bound.