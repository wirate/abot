<?php 
/**
 * TODO: refactor/tweak
 */
class Relay_Event_Request
{
    protected $parameters = array();

    public function __construct($string, $delimiter = ' ')
    {
        $this->parameters = explode($delimiter, $string);
    }

    public function getAll()
    {
        return $this->parameters;
    }

    public function get($key)
    {
        return (isset($this->parameters[$key]))
            ? $this->parameters[$key] : false;
    }

    public function count()
    {
        return count($this->parameters);
    }
}