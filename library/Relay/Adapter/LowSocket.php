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
            require_once 'Relay/Adapter/Exception.php';
            throw new Relay_Adapter_Exception(socket_strerror(socket_last_error()));
        }

        if (socket_connect($resource, $host, $port) === false) {
            $message = socket_strerror(socket_last_error());
            require_once 'Relay/Adapter/Exception.php';
            throw new Relay_Adapter_Exception('Socket connection failed: ' . $message);
        }

        if (!socket_set_nonblock($resource)) {
            $message = socket_strerror(socket_last_error());
            require_once 'Relay/Adapter/Exception.php';
            throw new Relay_Adapter_Exception("Failed to set block mode: " . $message);
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
            $message = socket_strerror(socket_last_error());
            require_once 'Relay/Adapter/Exception.php';
            throw new Relay_Adapter_Exception('Socket select error: ' . $message);
        }

        if (socket_recv($sock[0], $buffer, $bytes, 0) === 0) {
            $this->disconnect();
            require_once 'Relay/Adapter/Exception.php';
            throw new Relay_Adapter_Exception('Socket disconnected: ' . $message);
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