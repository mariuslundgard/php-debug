<?php

namespace Debug;

use Socket\Server;
use Socket\Client;

class Debugger
{
    protected $name;
    protected $config;
    protected $callback;

    protected static $clients;
    protected static $servers;
    protected static $debuggers;

    public function __construct($name, array $config = [])
    {
        $this->name = $name;

        $this->config = $config + [
            'protocol' => 'udp',
            'host'     => '127.0.0.1',
            'port'     => 1113,
            'output'   => false,
            'color'    => 'cyan',
        ];
    }

    public function getCallback()
    {
        if (null === $this->callback) {
            $debugger = $this;

            $this->callback = function () use ($debugger) {
                $debugger->handleCall(func_get_args());
            };
        }

        return $this->callback;
    }

    public function getServer()
    {
        if (empty(static::$servers[$this->name])) {
            static::$servers[$this->name] = new Server($this->config + array(
                'path' => '/'.$this->name,
                'flags' => STREAM_SERVER_BIND
            ));
        }

        return static::$servers[$this->name];
    }

    public function getClient()
    {
        if (empty(static::$clients[$this->name])) {
            static::$clients[$this->name] = new Client($this->config + array(
                'path' => '/'.$this->name
            ));
        }

        return static::$clients[$this->name];
    }

    public function debug()
    {
        // get the arguments to be debugged
        $args = func_get_args();

        // prepare debug output
        $output = '';
        $first = true;
        foreach ($args as $item) {
            $output .= ($first ? '' : ' ').debug_dump($item);
            $first = false;
        }

        // client-side output?
        if ($this->config['output'] || (($env = getenv('DEBUG')) && (!$env || $env !== 'false'))) {
            $colors = [
                'cyan' => '#0ec',
                'red' => '#f00',
                'green' => '#0c0',
            ];
            $color = isset($colors[$this->config['color']]) ? $colors[$this->config['color']] : '#ccc';
            $prefix = '<span style="color: '.$color.'">'.$this->name.'</span> ';

            echo '<details style="'.
                'background: rgba(127, 127, 127, .1);'.
                'font-family: Menlo, monospace;'.
                'font-size: 14px;'.
                'line-height: 20px;'.
                'margin: 1px 0;'.
                '" open>'.
                '<summary style="padding: 7px 10px; outline: none; cursor: default; display: inline;">'.
                $prefix.
                '</summary><pre style="padding: 0 10px 7px; font-family: inherit; line-height: inherit; margin: 0; display: inline;">'.
                $output.
                '</pre></details>';
        }

        $this->sendToSocketServer($output);
    }

    protected function sendToSocketServer($output, $num = 1, $parts = 1)
    {
        // split the output in to smaller packets
        if (1000 < strlen($output)) {
            $chunks = explode('{||}', chunk_split($output, 1000, '{||}'));
            array_pop($chunks);
            for ($i = 0; $i < count($chunks); $i++) {
                $this->sendToSocketServer($chunks[$i], $i+1, count($chunks));
            }
            return;
        }

        // send debug message to server
        $this->getClient()->send($this->name.':'.$this->config['color'].':'.$num.'of'.$parts.':'.$output);
        $this->getClient()->close();
    }

    protected function handleCall($args)
    {
        call_user_func_array([$this, 'debug'], $args);
    }

    public static function get($name = 'debug', array $config = [])
    {
        if (empty(static::$debuggers[$name])) {
            static::$debuggers[$name] = new static($name, $config);
        }

        return static::$debuggers[$name];
    }
}
