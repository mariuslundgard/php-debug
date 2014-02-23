<?php

namespace Debug;

use Util\Dictionary;

class Client
{
    protected $conn;

    public function __construct(array $config = [])
    {
        $this->config = new Dictionary([
            'name' => 'system',
        ]);

        $this->config->merge($config);
    }

    public function getConnection()
    {
        if (null === $this->conn) {
            $this->conn = new Connection($this->config->get());
        }

        return $this->conn;
    }

    public function send(Message $message)
    {
        return $this->getConnection()->send($message->getBody());
    }
}
