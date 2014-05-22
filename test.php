<?php

require __DIR__ . '/mindplay/funnel/EventSink.php';

use mindplay\funnel\EventSink;

test(
    'EventSink behavior',
    function () {
        // TODO add unit tests
    }
);

exit(status());

// https://gist.github.com/mindplay-dk/4260582

/**
 * @param string   $name     test description
 * @param callable $function test implementation
 */
function test($name, Closure $function)
{
    echo "\n=== $name ===\n\n";

    try {
        $function();
    } catch (Exception $e) {
        ok("UNEXPECTED EXCEPTION:\n\n$e", false);
    }
}

/**
 * @param string $text   description of assertion
 * @param bool   $result result of assertion
 * @param mixed  $value  optional value (displays on failure)
 */
function ok($text, $result, $value = null)
{
    if ($result === true) {
        echo "- PASS: $text\n";
    } else {
        echo "# FAIL: $text" . ($value === null ? '' : ' (' . (is_string($value) ? $value : var_export($value, true)) . ')') . "\n";
        status(false);
    }
}

/**
 * @param string $text   description of assertion
 * @param mixed  $value  value
 * @param mixed  $value  expected value
 */
function eq($text, $value, $expected) {
    ok($text, $value === $expected, "expected: " . var_export($expected, true) . ", got: " . var_export($value, true));
}

/**
 * @param string   $text           description of assertion
 * @param string   $exception_type Exception type name
 * @param callable $function       function expected to throw
 */
function expect($text, $exception_type, Closure $function)
{
    try {
        $function();
    } catch (Exception $e) {
        if ($e instanceof $exception_type) {
            ok("$text (ok: \"{$e->getMessage()}\")", true);
            return;
        } else {
            $actual_type = get_class($e);
            ok("$text (expected $exception_type but $actual_type was thrown)", false);
            return;
        }
    }

    ok("$text (expected exception $exception_type was NOT thrown)", false);
}

/**
 * @param bool|null $status test status
 * @return int number of failures
 */
function status($status = null) {
    static $failures = 0;
    
    if ($status === false) {
        $failures += 1;
    }
    
    return $failures;
}
