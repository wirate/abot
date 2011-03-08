<?php

require_once 'Relay/Adapter/Interface.php';

/**
 * Relay_Network_Socket
 * basic non-blocking socket connection. Wrapps PHP's Stream/File functions
 *
 * @see http://docs.php.net/manual/en/function.stream-socket-client.php
 */
class Relay_Adapter_Socket implements Relay_Adapter_Interface
{
    /**
     * Transfer protocol constants
     * @link http://se2.php.net/manual/en/transports.php
     */
    const TCP   = 'tcp';
    const SSL   = 'ssl';
    const SSLV2 = 'sslv2';
    const SSLV3 = 'sslv3';
    const UDP   = 'udp';
    const TLS   = 'tls';

	/**
	 * Socket Resource
	 * @var resource|null
	 */
	protected $resource = null;

	/**
	 * Create and connect socket.
         *
	 * @param String $host
	 * @param Int $port
	 * @param String $protocol
	 */
	public function connect($host, $port, $protocol = self::TCP)
	{
		$resource = @stream_socket_client("$protocol://$host:$port",
										   $errno,
										   $errstr,
										   30,
										   STREAM_CLIENT_CONNECT);

		if($resource === false) {
			require_once 'Relay/Adapter/Exception.php';
			throw new Relay_Adapter_Exception("Adapter error ($protocol://$host): $errstr", $errno);
		}
		
		if(set_socket_blocking($resource, 0) === false) {
		    require_once 'Relay/Adapter/Exception.php';
            throw new Relay_Adapter_Exception("Could not set blocking mode");
		}
		
		$this->resource = $resource;
	}
	
	/**
	 * Write data to stream
     *
	 * @param String $data
	 * @return (bool) true on success, false on failure.
	 */
	public function write($data)
	{
		return @fwrite($this->resource, $data);
	}
	
	/**
	 * Read data from stream.
     * 
	 * @param int $bytes [optional]
	 * @return (String) Data read from socket or (bool) false if no data was read.
	 */
	public function read($bytes = 1024)
	{
		$stream = array($this->resource);
		
		if(stream_select($stream, $n = null, $n = null, 3) === 0) {
			return;
		}

	    if(feof($stream[0])) {
            $this->disconnect();
            require_once 'Relay/Adapter/Exception.php';
            throw new Relay_Adapter_Exception("EOF reached: Connection lost");
        }
		
	    return @fgets($stream[0], $bytes);
	}
	
	/**
	 * Close socket.
	 * @return Void
	 */
	public function disconnect()
	{
		if($this->resource !== null) {
			@fclose($this->resource);
			$this->resource = null;
		}
	}

	/**
	 * Destructor, Make sure the socket closes
	 * @return void
	 */
	public function __destruct()
	{
		$this->disconnect();
	}

}

