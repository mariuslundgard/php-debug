<?php

namespace Debug;

use Util\Dictionary;

class Connection
{
    protected $handle;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function connect()
    {
        if (null === $this->handle) {

            // exit;
            // $this->handle = new Handle($this->config['name']);
            if ($this->handle = fopen('php://memory/debug', 'w+')) {
                return true;
            }
            // $this->handle = new SAMHandle();
        }

        return false;

        // return $this->handle;
    }

    public function close()
    {
        if ($this->handle) {
            fclose($this->handle);
            $this->handle = null;
        }

        return true;
    }

    public function send()
    {
        if ($this->connect()) {

            echo dump($this);

            $this->close();
            exit;
        }
        // d($this->getHandle());
        // exit;
    }

    // public function send(Message $message)
    // {
    //     $handle = $this->getHandle();
    //     d('send', $message, $handle);
    //     // exit;
    // }
}
