# Eventor

_A sane interface to PHP's [libevent Extension][]_

[libevent extension]: http://php.net/libevent

## Install

Install it via composer:

	{
		"require": {
			"chh/eventor": "*"
		}
	}

Then do:
	
	# Only if you don't have composer:
	% wget http://getcomposer.org/composer.phar
	% php composer.phar install

## Example

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
		
		printf("You wrote: %s", fgets($event->fd));
	});
	
	$base->loop();

## API

### class Eventor

#### Methods

##### Eventor\Event read(resource $fd, callable $callback)

Creates a new read event and registers it in the event loop with the
given `$callback`.

When data is written to `$fd` the callback is called and receives an instance of `Eventor\Event` as sole argument.

##### Eventor\Event write(resource $fd, callable $callback)

Creates a new write event and registers it in the event loop with the
given `$callback`.

When data can be written to `$fd` the callback is called and receives an instance of
`Eventor\Event` as sole argument.

##### add(Eventor\Event $event)

Register a manually created Event instance in the event loop.

##### delete(Eventor\Event $event)

Delete the event from the event loop, which causes it to trigger
the callback never again.

##### loop()

Starts the event loop, this blocks until all events are dispatched (if the events
are not persistent) or one of the `breakLoop()` or `exit()` methods is called
on the `Eventor\Base`.

##### halt()

Breaks the loop immediately, similar to using the `break` keyword in a loop.

##### shutdown(int $timeout = -1)

Gracefully shut down the event loop by finishing the next dispatch and
exiting after the timeout. The `$timeout` argument receives the time in
microseconds when the loop should exit, when omitted the loop is exited after
the next dispatch.

### class Eventor\Event

The `Event` class is useful for more low level access to the libevent extensions'
functionality and is also passed to the event handlers when they're called.

#### Methods

##### __construct(resource $fd, int $events, callable $callback)

Initializes the event instance with the file descriptor and the
flags. Events is an integer consisting of one or more of these constants
defined in the `Eventor\Event` class:

 * `Event::READ`, listens on writes to the file descriptor.
 * `Event::WRITE`
 * `Event::SIGNAL`, the file descriptor is handled as signal.
 * `Event::TIMEOUT`, triggers the event after a timeout.
 * `Event::PERSIST`, event is not deleted from the base after the
   handler was called. This is set by default, you have to call
   `setNonPersistent()` to disable this flag.

Example:

	<?php
	
	use Eventor\Event,
	    Eventor\Base;
	
	$base = new Base;
	
	$event = new Event(STDIN, Event::READ, function($event) {
		echo fgets($event->fd);
	});
	
	$base->add($event);
	$base->loop();

##### setNonPersistent($enable = true)

Sets the event to non-persistent mode, by registering it without setting
the `Eventor\Event::PERSIST` flag. This causes the event handler to get triggered only once.

#### Properties

##### base

The instance of the `Eventor\Base`, to which this event is bound. Is `null` in the case this event is not bound to any base.

##### callback

The callback which gets called when the event is ready.

##### fd

The `fd` property is the file descriptor, to which this event is bound.

## License

Copyright © 2012 Christoph Hochstrasser

Eventor is licensed under the MIT license which is bundled with this
package in the file `LICENSE.txt`.