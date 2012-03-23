<?php

namespace Eventor\Test;

use Eventor\Base;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    function test()
    {
        $pipe = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
        $base = new Base;
        $called = 0;

        $base->read($pipe[0], function($event) use (&$called) {
            $called++;
            fgets($event->fd);

            if ($called === 3) {
                $event->base->halt();
            }
        });

        $pid = pcntl_fork();

        if (!$pid) {
            fwrite($pipe[1], "foo\n");
            usleep(500);
            fwrite($pipe[1], "bar\n");
            usleep(500);
            fwrite($pipe[1], "baz\n");
            usleep(500);

            exit();
        }

        $base->loop();

        pcntl_waitpid($pid, $s);

        $this->assertEquals(3, $called);
    }
}
