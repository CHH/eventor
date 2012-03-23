<?php

if (!extension_loaded("libevent")) {
    throw new \RuntimeException("libevent is not installed.");
}

class Eventor extends \Eventor\Base
{
    const VERSION = "0.1.0";
}
