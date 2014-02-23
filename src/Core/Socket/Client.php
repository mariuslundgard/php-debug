<?php

namespace Core\Socket;

class Client
{
    protected $address;
    protected $config;
    protected $handle;

    public function __construct($address, array $config = [])
    {
        $this->address = $address;
        $this->config = $config + [
            'errno' => null,
            'errstr' => null,
            'timeout' => ini_get('default_socket_timeout'),
            'flags' => STREAM_CLIENT_CONNECT,
            'context' => null,
        ];
    }

    public function connect()
    {
        if ( ! $this->handle) {
            if ($this->config['context']) {
                $this->handle = stream_socket_client(
                    $this->address,
                    $this->config['errno'],
                    $this->config['errstr'],
                    $this->config['timeout'],
                    $this->config['flags'],
                    $this->config['context']
                );
            } else {
                $this->handle = stream_socket_client(
                    $this->address,
                    $this->config['errno'],
                    $this->config['errstr'],
                    $this->config['timeout'],
                    $this->config['flags']
                );
            }

            if ( ! $this->handle) {
                throw new Exception('Could not connect to socket client: ' . $this->address);
            }

            // echo 'Client connected: ' . $this->address . PHP_EOL;
        }

        return $this->handle;
    }

    public function send($data)
    {
        $this->connect();

        $result = stream_socket_sendto($this->handle, $data);

        return true;

    //     $pkt = stream_socket_recvfrom($socket, 1, 0, $peer);

    //     return $pkt;
    //     // echo "$peer\n";
    //     // stream_socket_sendto($socket, date("D M j H:i:s Y\r\n"), 0, $peer);
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
