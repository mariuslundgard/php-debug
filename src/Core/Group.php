<?php

namespace Core;

class Group
{
    protected $gid;
    protected $processes;

    public function __construct($gid)
    {
        $this->gid = $gid;
        $this->processes = [];
    }

    public function registerProcess(Process $process)
    {
        $this->processes[$process->pid] = $process;
        return $this;
    }

    public function getInfo()
    {
        return posix_getgrgid($this->gid);
    }
}
