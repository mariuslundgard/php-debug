<?php

namespace Core\Socket;

class Server
{
    protected $address;
    protected $config;
    protected $handle;

    protected $path;

    public function __construct($address, array $config = [])
    {
        $this->address = $address;
        $this->config = $config + [
            'errno' => null,
            'errstr' => null,
            'timeout' => ini_get('default_socket_timeout'),
            'flags' => STREAM_SERVER_BIND | STREAM_SERVER_LISTEN,
            'context' => null,
        ];
    }

    public function connect()
    {
        if (null === $this->handle) {

            if ($this->config['context']) {
                $this->handle = stream_socket_server(
                    $this->address,
                    $this->config['errno'],
                    $this->config['errstr'],
                    $this->config['flags'],
                    $this->config['context']
                );
            } else {
                $this->handle = stream_socket_server(
                    $this->address,
                    $this->config['errno'],
                    $this->config['errstr'],
                    $this->config['flags']
                );
            }

            if ( ! $this->handle) {
                throw new Exception('Could not connect to socket server: ' . $this->address);
            }
        }

        return $this->handle;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function receive()
    {
        $ret = '';

        if ($this->hasPacket()) {
            $ret = $this->consume();
        }

        return strlen($ret) ? $ret : null;
    }

    public function consume()
    {
        $this->connect();

        if ($char = stream_socket_recvfrom($this->handle, 5600, 0, $peer)) {
            $this->path = $peer;
            return $char;
        }

        return null;
    }

    public function hasPacket()
    {
        $this->connect();

        if (stream_socket_recvfrom($this->handle, 5600, STREAM_PEEK, $peer)) {
            $this->path = $peer;
            return true;
        }
    }

    public function peek()
    {
        $this->connect();

        if ($char = stream_socket_recvfrom($this->handle, 5600, STREAM_PEEK, $peer)) {
            $this->path = $peer;
            return $char;
        }

        return null;
    }

    public function close()
    {
        if ($this->handle) {
            fclose($this->handle);
            $this->handle = null;
            return true;
        }

        return false;
    }
}
