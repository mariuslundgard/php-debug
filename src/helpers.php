<?php

/**
 * Debug helpers
 */

function debug()
{
    call_user_func_array(Debug\Debugger::get('debug')->getCallback(), func_get_args());
}

function d()
{
    call_user_func_array('debug', func_get_args());
}

function dd()
{
    call_user_func_array('debug', func_get_args());
    die('');
}

function debug_dump($obj, $func = 'json_encode')
{
    // Check if the objing is in fact a class instance
    if (is_object($obj) && get_class($obj)) {

        $obj = print_r($obj, true);

    } elseif (is_array($obj) || is_object($obj)) {

        // arrays and objects
        switch ($func) {

            case 'json_encode':
                $obj = json_encode($obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                break;

            case 'print_r':
                $obj = print_r($obj, true);
                break;
        }
    }

    return null === $obj ? '(empty)' : (string) $obj;
}
