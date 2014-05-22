<?php

/**
 * @author Rasmus Schultz <http://blog.mindplay.dk/>
 */

namespace mindplay\funnel;

use ReflectionParameter;
use Closure;

/**
 * This class implements an event sink, through which arbitrary event
 * objects can be distributed to a list of registered event listeners.
 */
class EventSink
{
    /**
     * @type (callable[])[] lists of event listeners, indexed by class-name
     */
    protected $listeners = array();

    /**
     * @type string pattern for parsing an argument type from a ReflectionParameter string
     * @see getArgumentType()
     */
    const ARG_PATTERN = '/.*\[\s*(?:\<required\>|\<optional\>)\s*([^\s]+)/';

    /**
     * Extract the argument type (class name) from the first argument of a given function
     *
     * @param callable $func
     * @return string class name
     */
    protected function getArgumentType($func)
    {
        $param = new ReflectionParameter($func, 0);

        preg_match(self::ARG_PATTERN, $param->__toString(), $matches);

        return $matches[1];
    }

    /**
     * Register a new event listener
     *
     * @param callable $func event listener function with precisely one argument
     */
    public function register($func)
    {
        $this->listeners[$this->getArgumentType($func)][] = $func;
    }

    /**
     * Submit an event object for distribution to registered event listeners
     *
     * Alternatively, use a "proxy" function for the initialization of an event
     * object, as an optimization - if no listeners for the given type of event
     * have been registered, the proxy function will be "short circuited", that
     * is, it will not execute, and the event type (class) will never even load.
     *
     * Note that, for the latter to work, the event type (class) must have an
     * empty constructor.
     *
     * @param object|callable $event event object or proxy function
     */
    public function submit($event)
    {
        if ($event instanceof Closure) {
            $proxy = $event;
            $type = $this->getArgumentType($proxy);

            if (! isset($this->listeners[$type])) {
                return; // short circuit - no registered listeners
            }

            $event = new $type;

            call_user_func($proxy, $event);
        } else {
            $type = get_class($event);
        }

        if (isset($this->listeners[$type])) {
            foreach ($this->listeners[$type] as $func) {
                $returned = call_user_func($func, $event);

                if ($returned) {
                    $this->submit($returned);
                }
            }
        }
    }
}
