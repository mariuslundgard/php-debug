<?php

/**
 *
 * debug helpers
 *
 */

/**
 * dump anything
 */
function debug()
{
    $debug = Debug\Debugger::get('debug');

    call_user_func_array($debug, func_get_args());
}

function d()
{
    call_user_func_array('debug', func_get_args());
}

/**
 * dump + die anything
 */
function dd()
{
    call_user_func_array('debug', func_get_args());
    die('');
}

/**
 * inspect anything
 */
function inspect()
{
    return call_user_func_array(['Debug\Debugger', 'inspect'], func_get_args());
}
