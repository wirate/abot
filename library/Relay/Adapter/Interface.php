<?php

/**
 * Relay_Adapter_Interface
 * Common interface for network adapter/wrapper classes
 */

interface Relay_Adapter_Interface
{
	/**
	 * Establish connection
	 *
	 * @param string $host
	 * @param int    $port
	 */
	public function connect($host, $port);
	
	/**
	 * Write to stream
     *
	 * @param $data the data to write
	 */
	public function write($data);
	
	/**
	 * Read from stream
	 *
	 * @param int $bytes max numbers of bytes to recive
	 */
	public function read($bytes = 1024);
	
	/**
	 * Close connection
	 */
	public function disconnect();
}

