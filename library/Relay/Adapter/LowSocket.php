<?php

require_once 'Relay/Adapter/Interface.php';

class Relay_Adapter_LowSocket implements Relay_Adapter_Interface
{
    /**
     * Socket domain (IPv4, IPv6 or local)
     *
     * @var int
     */
    protected $_domain;

    /**
     * Socket type.
     *
     * @var int
     */
    protected $_type;

    /**
     * Protocol the socket to use. (TCP, UDP, Raw)
     *
     * @var int
     */
    protected $_protocol;

    /**
     * The Socket resource
     *
     * @var resource
     */
    protected $resource = null;

    /**
     * Default is a IPv4 stream socket using the TCP protocol.
     *
     * @param int $domain
     * @param int $type
     * @param int $protocol
     */
    public function __construct($domain = AF_INET, $type = SOCK_STREAM,
                                $protocol = SOL_TCP)
    {
        $this->_domain = $domain;
        $this->_type = $type;
        $this->_protocol = $protocol;
    }

    /**
     * Establish connection
     *
     * @param string $host
     * @param int    $port
     */
    public function connect($host, $port)
    {
        // disconnect first.
        $this->disconnect();

        // Create a socket.
        $resource = socket_create($this->_domain, $this->_type, $this->_protocol);

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