<?php

namespace Core;

use Exception;


// Signal       Default Action                          Description
// SIGABRT      A                                       Process abort signal
// SIGALRM      T                                       Alarm clock
// SIGBUS       A                                       Access to an undefined portion of a memory object
// SIGCHLD      I - Ignore the Signal                   Child process terminated, stopped,
// SIGCONT      C - Continue the process                Continue executing, if stopped.
// SIGFPE       A                                       Erroneous arithmetic operation.
// SIGHUP       T                                       Hangup.
// SIGILL       A                                       Illegal instruction.
// SIGINT       T                                       Terminal interrupt signal.
// SIGKILL      T                                       Kill (cannot be caught or ignored).
// SIGPIPE      T - Abnormal termination of the process Write on a pipe with no one to read it.
// SIGQUIT      A - Abnormal termination of the process Terminal quit signal.
// SIGSEGV      A                                       Invalid memory reference.
// SIGSTOP      S - Stop the process                    Stop executing (cannot be caught or ignored).
// SIGTERM      T                                       Termination signal.
// SIGTSTP      S                                       Terminal stop signal.
// SIGTTIN      S                                       Background process attempting read.
// SIGTTOU      S                                       Background process attempting write.
// SIGUSR1      T                                       User-defined signal 1.
// SIGUSR2      T                                       User-defined signal 2.
// SIGPOLL      T                                       Pollable event.
// SIGPROF      T                                       Profiling timer expired.
// SIGSYS       A                                       Bad system call.
// SIGTRAP      A                                       Trace/breakpoint trap.
// SIGURG       I                                       High bandwidth data is available at a socket.
// SIGVTALRM    T                                       Virtual timer expired.
// SIGXCPU      A                                       CPU time limit exceeded.
// SIGXFSZ      A                                       File size limit exceeded

class Process
{
    protected $pid;
    protected $group;

    protected static $sigNum = [
        'hangup'          => SIGHUP,
        'restart'         => SIGHUP,  // = hangup
        'shutdown'        => SIGTERM, // Termination signal
        'interrupt'       => SIGINT,  // Terminal interrupt signal
        'user1'           => SIGUSR1, // User-defined signal 1
        'terminate child' => SIGCHLD, // Child process terminated, stopped

        // not supported by PHP
        'kill' => SIGKILL,

        // constants not supported by PHP
        // 'poll' => SIGPOLL,
    ];

    protected static $unsupportedSigNum = [ SIGKILL ];

    protected static $root;

    public function __construct($pid = null)
    {
        $this->pid = $pid;

        // echo 'PROCESS #' . $this->pid . PHP_EOL;
    }

    public function __get($property)
    {
        switch ($property) {
            case 'pid':
                return $this->pid;
            
            default:
                return null;
        }
    }

    public function getGroups()
    {
        $gids = posix_getgroups();
        $ret = [];

        foreach ($gids as $gid) {
            $ret[] = new Group($gid);
            // $this->group = new Group($gid);
            // $this->group->registerProcess($this);
        }

        return $ret;
    }

    public function getGroup()
    {
        if (null === $this->group) {
            $this->group = new Group($this->getGroupId());
            $this->group->registerProcess($this);
        }

        return $this->group;
    }

    public function getPid()
    {
        if (null === $this->pid) {
            $this->pid = getmypid(); // posix_getpid() does not work on windows...
        }

        return $this->pid;
    }

    public function getGroupId()
    {
        return posix_getegid(); // effective group id
        // return posix_getgid(); // get real group id
        // return posix_getpgid($this->pid); // get group id of current process (not working?)
    }

    public function hangup()
    {
        $this->sendSignal('hangup');
        return $this;
    }

    public function shutdown()
    {
        $this->sendSignal('shutdown');
        return $this;
    }

    public function sendSignal($type)
    {
        if ( ! isset(static::$sigNum[$type])) {
            throw new Exception('Cannot send: unknown signal type: ' . $type);
        }

        if (posix_kill($this->getPid(), static::$sigNum[$type])) {
            pcntl_signal_dispatch();
            return true;
        }

        return false;
    }

    public static function get()
    {
        if (! static::$root) {
            static::$root = new static(getmypid());
        }

        return static::$root;
    }

    public function on($type, $callback, $restartSyscalls = true)
    {
        if ( ! isset(static::$sigNum[$type])) {
            throw new Exception('Cannot add signal listener: unknown signal type: ' . $type);
        }

        if (in_array(static::$sigNum[$type], static::$unsupportedSigNum)) {
            throw new Exception('Cannot add signal listener: the signal type is not supported: ' . $type);
        }

        // print_r('SIGNAL TYPE: ' . static::$sigNum[$type] . PHP_EOL);

        // $callback->bindTo($this);

        pcntl_signal(static::$sigNum[$type], $callback, $restartSyscalls);

        return $this;
    }

    // public function addSignalHandler($signo, $handler, $restartSyscalls = true)
    // {
    //     
    // }

    public function fork()
    {
        declare(ticks=1);

        // the actual forking
        $this->pid = pcntl_fork();

        if ($pid == -1) {
            throw new Exception('Could not fork'); 
        } else if ($pid) {
            exit(); // we are the parent 
        } else {
            // we are the child
        }

        // detatch from the controlling terminal
        if (posix_setsid() == -1) {
            throw new Exception('Could not detach from terminal');
        }

        // setup signal handlers
        pcntl_signal(SIGTERM, 'sig_handler');
        pcntl_signal(SIGHUP, 'sig_handler');

        // loop forever performing tasks
        // while (1) {

        //     // do something interesting here

        // }
    }
}
