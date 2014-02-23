<?php

namespace Debug;

use Core\Socket\Server as SocketServer;
use Core\Socket\Client as SocketClient;

class Debugger
{
    protected $name;
    protected $config;
    protected $callback;

    protected static $server;
    protected $client;

    protected static $debuggers;

    public function __construct($name, array $config = [])
    {
        $this->name = $name;
        $this->config = $config + [
            'output' => true,
            'color' => 'cyan',
        ];
    }

    // protected static function randColor()
    // {
    //     $colors = [
    //         'black',
    //         'dark_gray',
    //         'red',
    //         'green',
    //         'blue',
    //         'purple',
    //         'cyan',
    //         'brown',
    //         'yellow',
    //     ];

    //     return $colors[rand(0, count($colors) - 1)];
    // }

    public function getCallback()
    {
        if (null === $this->callback) {
            $debugger = $this;

            $this->callback = function () use ($debugger) {
                $debugger->_handleCall(func_get_args());
            };
        }

        return $this->callback;
    }

    public static function getServer()
    {
        if (null === static::$server) {
            $port = 1113;

            static::$server = new SocketServer('udp://127.0.0.1:' . $port . '/debug', [
                'flags' => STREAM_SERVER_BIND,
            ]);
        }

        return static::$server;
    }

    public function getClient()
    {
        if (null === $this->client) {
            $port = 1113;

            $this->client = new SocketClient('udp://127.0.0.1:' . $port . '/debug', [
                // 'flags' => STREAM_SERVER_BIND,
            ]);

            // $client->send('http:testing this');
            // $client->close();
        }

        return $this->client;
    }

    protected function _handleCall($args)
    {
        if ($this->config['output']) {
            // dump
            call_user_func_array('d', $args);
        }

        //
        $this->getClient()->send($this->name . ':' . $this->config['color'] . ':' .$args[0]);
        $this->getClient()->close();
    }

    public static function get($name = 'default', array $config = [])
    {
        if (empty(static::$debuggers[$name])) {
            static::$debuggers[$name] = new static($name, $config);
        }

        // d(static::$debuggers[$name]);

        return static::$debuggers[$name]->getCallback();
    }
}
