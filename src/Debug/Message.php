<?php

namespace Debug;

class Message
{
    protected $body;
    protected $client;

    public function __construct($body = '')
    {
        $this->body = $body;
    }

    public function write($data)
    {
        $this->body .= $data;
    }

    public function getClient()
    {
        if (null === $this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }

    public function send()
    {
        return $this->getClient()->send($this);
    }

    public function getBody()
    {
        return $this->body;
    }
}
