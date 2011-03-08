<?php

/**
 * Abstract event class.
 * TODO: design change! update client code
 */
abstract class Relay_Event_Command
{
    protected $caller  = null;
    
    protected $request = null;
    
    protected $message = null;
    
    protected $helpers = array();
    
    /**
     * Make sure constructor is clear of arguments
     *
     * @return void
     */
    final public function __construct()
    {
    }
    
    public function _setCaller(Relay_Client $caller)
    {
        $this->caller = $caller;
    }
    
    public function _setRequest(Relay_Event_Request $request)
    {
    	$this->request = $request;
    }
    
    public function _setMessage(Relay_Protocol_Message $message)
    {
    	$this->message = $message;
    }
    
    public function __call($name, $args)
    {
    	echo "called $name";
    }
    
    public function __autoload($class)
    {
    	$file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    	
    	if(!file_exists($file)) {
    		throw new Exception("'$file' don't exists");
    	}
    	
    	require_once $file;
    }
    
    abstract public function process();
}
