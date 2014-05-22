funnel
======

https://github.com/mindplay-dk/funnel

[![Build Status](https://travis-ci.org/mindplay-dk/funnel.png)](https://travis-ci.org/mindplay-dk/funnel)

This is simple event sink (pub/sub) facility for PHP 5.3+ which attempts to
improve on the performance and robustness of event facilities in general.

See "example.php" in the root-folder for an example of how to use this class.

By using type-hinted closures for event types (classes) as opposed to arbitrary
strings (event names) or literal class names or function names as strings, the
robustness is greatly improved - a modern IDE (such as Php Storm) can perform
meaningful inspections, code can be safely refactored, and you can more easily
navigate the code base e.g. by following real, static type-hints.

A means of optimizing performance is provided, by permitting the use of "proxy"
functions for initialization of events to be submitted - loading and constructing
an event object can be done conditionally, by "short circuiting" the event when
no listeners for that type of event have been registered.
