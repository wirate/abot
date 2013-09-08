<?php

require_once 'Relay/Adapter/Interface.php';

class Relay_Adapter_LowSocket implements Relay_Adapter_Interface
{

    protected $resource = null;

    public function connect($host, $port, $contype = AF_INET,
                            $protocol = SOL_TCP)
    {
        $resource = socket_create($contype, SOCK_STREAM, $protocol);

        if ($resource === false) {
            throw new Exception(socket_strerror(socket_last_error()));
        }

        if (socket_connect($resource, $host, $port) === false) {
            $message = socket_strerror(socket_last_error());
            throw new Exception("Socket connection failed: " . $message);
        }

        if (!socket_set_nonblock($resource)) {
            $message = socket_strerror(socket_last_error());
            throw new Exception("Failed to set block mode: " . $message);
        }

        $this->resource = $resource;
    }

    public function write($data)
    {
        return @socket_write($this->resource, $data);
    }

    public function read($bytes = 1024)
    {
        $sock = array($this->resource);
        $s = socket_select($sock, $n = null, $n = null, 300);

        if ($s === false) {
            $this->disconnect();
            die("socket_select error" . socket_strerror(socket_last_error()) . "\n");
        }

        if (socket_recv($sock[0], $buffer, $bytes, 0) === 0) {
            $this->disconnect();
            die("socket disconnected: \n");
        }

        return $buffer;
    }

    public function disconnect()
    {
        if ($this->resource !== null) {
            socket_close($this->resource);
            $this->resource = null;
        }
    }

    public function __destruct()
    {
        $this->disconnect();
    }

}