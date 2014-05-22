<?php

use mindplay\funnel\EventSink;

require __DIR__ . '/mindplay/funnel/EventSink.php';

// ===== EXAMPLE =====

header('Content-type: text/plain');

class Foo
{
    public $bar = 'hello world';
}

$sink = new EventSink();

$sink->register(
    function (Foo $foo) {
        echo "observer #1: {$foo->bar}\n";
        static $i = 0;
        if ($i ++ < 3) {
            $another = new Foo;
            $another->bar = "hello from returned event {$i}";
            return $another;
        }
    }
);

$sink->register(
    function (Foo $foo) {
        echo "observer #2: {$foo->bar}\n";
    }
);

$sink->submit(new Foo);

// lazy event submission:

$sink->submit(
    function (Foo $foo) {
        $foo->bar = 'hello again';
    }
);

/** @noinspection PhpUndefinedClassInspection */
$sink->submit(
    function (Bar $bar) {
        echo "This will never execute and Bar will never load.";
    }
);
