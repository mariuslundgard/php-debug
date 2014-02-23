<?php

// function inspect()
// {

// }

function debug()
{
    call_user_func_array(Debug\Debugger::get(), func_get_args());
}

function inspect($obj, $depth = null, $mem = [])
{
    $mem += [ 'objects' => [] ];

    if (0 === $depth) {
        return;
        // return '<div>' . $name . ' = [nesting stopped]</div>';
    }

    $depth--;

    $type = null;
    $info = [
        'type' => null,
        'isClass' => false,
    ];

    if (is_object($obj)) {

        //
        $info = [
            'type' => 'object',
            'name' => null,
            'methods' => null,
            'isClass' => false,
            'recursion' => false,
        ] + $info;

        // prevent recursion
        foreach ($mem['objects'] as $o) {
            if (spl_object_hash($o) === spl_object_hash($obj)) {
                $info['recursion'] = true;
                // return;
            }
        }
        $mem['objects'][] = $obj;

        //
        if (get_class($obj)) {
            $refl = new ReflectionClass($obj);

            $info['name'] = $refl->getName();
            $info['isClass'] = true;

            if (!$info['recursion']) {

                if ($constants = $refl->getConstants()) {
                    $info['constants'] = $constants;
                }

                if ($properties = $refl->getProperties()) {
                    $info['properties'] = [];
                    foreach ($refl->getProperties() as $property) {
                        $property->setAccessible(true);
                        $info['properties'][$property->name] = inspect($property->getValue($obj), $depth, $mem);
                        $property->setAccessible(false);
                    }
                }

                if ($methods = $refl->getMethods()) {
                    $info['methods'] = [];
                    foreach ($refl->getMethods() as $method) {
                        $info['methods'][] = $method->name;
                    }
                }
            }
        } else {
            if (!$info['recursion']) {
                $info['properties'] = [];
                foreach (get_object_vars($obj) as $k => $v) {
                    $info['properties'][$k] = inspect($v, $depth, $mem);
                }
            }
        }

    } elseif (is_array($obj)) {
        $info['type'] = 'array';
        $info['properties'] = [];
        foreach ($obj as $k => $v) {
            $info['properties'][$k] = inspect($v, $depth, $mem);
        }
    }

    elseif (null === $obj) {
        $info['type'] = 'null';
    }

    elseif (false === $obj || true === $obj) {
        $info['type'] = 'boolean';
        $info['data'] = $obj ? 'TRUE' : 'FALSE';
    }

    elseif (is_int($obj)) {
        $info['type'] = 'int';
        $info['data'] = $obj;
    }

    elseif (is_string($obj)) {
        $info['type'] = 'string';
        $info['data'] = $obj;
    }

    return $info;
}

/**
 * Dumps any object to a readable string
 *
 * @param mixed  $obj  The object to dump
 * @param string $func The function to use for object dumping
 *
 * @return string The output string
 */
function dump($obj, $func = 'print_r')
{
    // Assume the object provided is a string
    $str = $obj;
    $func = 'print_r';

    // Check if the string is in fact a class instance
    if (is_object($str) and get_class($str)) {
        $str = print_r($str, true);

    } elseif (is_array($str) or is_object($str)) {

        // Arrays and objects
        switch ($func) {
            case 'print_r':
                $str = print_r($str, true);
                break;

            case 'json_encode':
                $str = jsonFormat(json_encode($str));
                break;
        }
    }

    return $str ? $str : '(empty)';
}

function inspect_html_render($varName, $info)
{
    $buf = [];

    switch ($info['type']) {
        
        case 'object':
            $buf[] = '<details style="padding: 0 13px;">';
            $buf[] = '<summary style="outline: none; cursor: pointer;">';
            $buf[] = '<span class="type" style="color: #999;">' . ($info['isClass'] ? 'class' : 'object') . '</span> ';
            $buf[] = ($varName !== null ? '' . $varName . ' = ' : '') . $info['name'] . '</summary>';

            if ($info['recursion']) {
                $buf[] = '<span style="display: block; padding: 0 13px;">(recursion)</span>';
            } else {

                if ($info['methods']) {

                    // methods
                    $buf[] = '<details style="margin-left: 10px;">';
                    $buf[] = '<summary style="outline: none; cursor: pointer;">methods</summary>';
                    $buf[] = '<ul style="list-style-type: none; padding: 0 0 0 13px; margin: 0">';
                    foreach ($info['methods'] as $name) {
                        $buf[] = '<li style="color: #999;">' . $name . '()</li>';
                    }
                    $buf[] = '</ul>';
                    $buf[] = '</details>';
                }

                if ($info['isClass']) {
                    $buf[] = '<details style="margin-left: 10px;">';
                    $buf[] = '<summary style="outline: none; cursor: pointer;">properties</summary>';
                }

                // properties
                if (isset($info['properties'])) {

                    foreach ($info['properties'] as $k => $v) {
                        $buf[] = inspect_html_render($k, $v);
                    }

                }

                if ($info['isClass']) {
                    $buf[] = '</details>';
                }
            }

            $buf[] = '</details>';
            break;
        
        case 'array':
            $buf[] = '<details style="padding: 0 13px;">';
            $buf[] = '<summary style="outline: none; cursor: pointer;">';
            $buf[] = '<span class="type" style="color: #999;">' . $info['type'] . '</span>';
            $buf[] = ($varName ? ' ' . $varName : '');
            $buf[] = '</summary>';

            foreach ($info['properties'] as $k => $v) {
                // echo '<div>' . $k . '</div>';
                $buf[] = inspect_html_render($k, $v);
            }

            if ($info['isClass']) {
                $buf[] = '</details>';
            }

            $buf[] = '</details>';
            break;
        
        case 'string':
            $buf[] = '<div style="padding: 0 13px 0 26px;">';
            $buf[] = '<span class="type" style="color: #999;">' . $info['type'] . '</span>';
            $buf[] = '' . ($varName !== null ? ' '.$varName . '' : '') . ' <span class="string" style="color: #f00;">\'' . htmlspecialchars($info['data']) . '\'</span>';
            $buf[] = '</div>';
            break;

        case 'int':
            $buf[] = '<div style="padding: 0 13px 0 26px;">';
            $buf[] = '<span class="type" style="color: #999;">' . $info['type'] . '</span>';
            $buf[] = '' . ($varName !== null ? ' '.$varName . '' : '') . ' <span class="string" style="color: #00f;">' . $info['data'] . '</span>';
            $buf[] = '</div>';
            break;

        case 'boolean':
            $buf[] = '<div style="padding: 0 13px 0 26px;">';
            // $buf[] = '<span class="type" style="color: #999;">' . $info['type'] . '</span>';
            $buf[] = '' . ($varName !== null ? ' '.$varName . '' : '') . ' <span class="string" style="color: ' . ($info['data'] === 'TRUE' ? '#0c0' : '#c00' ) . ';">' . $info['data'] . '</span>';
            $buf[] = '</div>';
            break;

        case 'null':
            $buf[] = '<div style="padding: 0 13px 0 26px;">';
            // $buf[] = '<span class="type" style="color: #999;">' . $info['type'] . '</span>';
            $buf[] = '' . ($varName !== null ? ' '.$varName . '' : '') . ' <span class="string" style="color: #ccc;">NULL</span>';
            $buf[] = '</div>';
            break;

        default:
            # code...
            break;
    }

    return implode('', $buf);
}

function inspect_html($name, $obj, $maxDepth = 30)
{
    $buf[] = inspect_html_render($name, inspect($obj, $maxDepth));

    return implode('', $buf);
}

// function for debugging data structures
function d()
{
    // debug(func_get_args());

    $buf = '<div style="font-weight: bold; position: relative; z-index: 1; font-family: \'Menlo\', monospace; font-size: 13px; line-height: 18px; padding: 10px 0; background: rgba(240, 240, 240, .9);">';

    foreach (func_get_args() as $arg) {
        // echo '+';
        $buf .= inspect_html(null, $arg);
    }

    $buf .= '<a href="#" style="color: #eee; text-decoration: none; position: absolute; right: 8px; top: 8px; width: 18px; text-align: center; padding: 2px; background: #ccc; font-size: 15px; border-radius: 14px;" onclick="javascript:this.parentNode.parentNode.removeChild(this.parentNode);return false;">&times</a></div>';

    echo $buf;
}
