<?php

/**
 * Describes a IRC message according to RFC 1459/2812. responsable for handle
 * a full correctly structured message string.
 * 
 * @see http://www.faqs.org/rfcs/rfc1459.html
 * @see http://www.faqs.org/rfcs/rfc2812.html
 */
class Relay_Irc_Message
{
    /**
     * constants
     */
    const SEGM_SEP  = ' ';
    const PREFIX    = ':';
    const CHPREFIX  = '#';
    const SEPARATOR = ',';
    const EOL       = "\n\r";

    /**
     * Prefix component
     *
     * @var String
     */
    protected $_prefix = null;

    /**
     * Command component
     *
     * @var String
     */
    protected $_command = null;

    /**
     * Parameters component
     *
     * @var array
     */
    protected $_parameters = array();

    /**
     * Trailing component
     * 
     * @var String
     */
    protected $_trail = null;

    /**
     * Construct a new Irc message.
     *
     * @param string $command   The command component
     */
    public function __construct($command = null)
    {
        if ($command !== null) {
            $this->setCommand($command);
        }
    }

    /**
     * Get the prefix component
     *
     * @return string|bool  string if a prefix exists, false otherwise
     */
    public function getPrefix()
    {
        return (strlen($this->_prefix) > 0) ? $this->_prefix : false;
    }

    /**
     * Set the prefix component
     *
     * @param string $prefix
     * @return Relay_Irc_Message
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = (string) $prefix;

        return $this;
    }

    /**
     * Get the command component
     * 
     * @return string
     */
    public function getCommand()
    {
        return $this->_command;
    }

    /**
     * Set the command component
     *
     * @param string $command
     * @return Relay_Irc_Message
     */
    public function setCommand($command)
    {
        $this->_command = (string) $command;
        return $this;
    }
    
    /**
     * Returns the parameters. 
     * if index is specified and exits the value is returned, false otherwise.
     * 
     * @param $index  (optional) integer
     * @return string|array|false
     * @throws InvalidArgumentException
     */
    public function getParam($index = null)
    {
        if ($index != null) {
            if (!is_integer($index)) {
                $msg = 'Argument must be of type integer, '
                     . gettype($index) . ' given';
                throw new InvalidArgumentException($msg);
            }

            if (array_key_exists($index, $this->_parameters)) {
                return $this->_parameters[$index];
            }
            return false;
        }
        return $this->_parameters;
    }

    public function setParam($param)
    {
        if (is_array($param)) {
            foreach ($param as $v) {
                if (strlen($v) < 0) {
                    continue;
                }

                $this->_parameters[] = $v;
            }
        } else if (is_string($param)) {
            if (strlen($param) > 0) {
                $this->_parameters[] = $param;
            }
        } else {
            throw new InvalidArgumentException(
            'Argument must be a string or array, ' . gettype($param) . ' given.');
        }

        return $this;
    }

    /**
     * Clear the param component.
     *
     * @return array    The old params.
     */
    public function resetParams()
    {
        $params = $this->_parameters;
        $this->_parameters = array();
        return $params;
    }

    /**
     * Set the trailing component.
     *
     * @param string $trail
     * @return Relay_Irc_Message
     */
    public function setTrail($trail)
    {
        $this->_trail = trim($trail);

        return $this;
    }

    /**
     * Get the trailing component.
     *
     * @return string
     */
    public function getTrail()
    {
        return (strlen($this->_trail) > 0) ? $this->_trail : false;
    }

    /**
     * Builds the full IRC string based on the setted components
     *
     * @return string
     */
    public function getMessage()
    {
        $string = '';

        if (strlen($this->_prefix)) {
            $string .= self::PREFIX . $this->_prefix . self::SEGM_SEP;
        }

        $string .= $this->_command;

        foreach ($this->_parameters as $param) {
            $string .= self::SEGM_SEP . $param;
        }

        if (strlen($this->_trail)) {
            $string .= self::SEGM_SEP . self::PREFIX . $this->_trail;
        }

        return $string . self::EOL;
    }

    public function validatePrefix($prefix = null)
    {
        if ($prefix === null) {
            $prefix = $this->_prefix;
        }

        if (strlen($prefix) === 0) {
            return true;
        }

        return preg_match('/^[^:\x20\xD\xA\x0][^\x20\xD\xA\x0]*$/', $prefix) === 1;
    }

    /**
     * method for validating command
     *
     * @param string|int $command
     * @return bool
     */
    public function validateCommand($command = null)
    {
        if ($command === null) {
            $command = $this->_command;
        }

        return preg_match('/^[A-Z]+$/', $command) === 1;
    }

    /**
     * FIXME validateParam()
     */
    public function validateParam($param = null)
    {
        /*
        if (is_string($param)) {
            $param = array($param);
        }

        if ($param === null) {
            $param = $this->parameters;
        }

        foreach($param as $v) {

            if ($v[1] === ':')

            for($i=1; $i < strlen($v); $i++) {

            }
        }

        return preg_match('/^[^:\x20\xD\xA\x0][^\x20\xD\xA\x0]*$/', $param) === 1;
        */
        return true;
    }

    public function validateTrail($trail = null)
    {
        if ($trail === null) {
            $trail = $this->_trail;
        }

        // empty string are considered valid
        if (strlen($trail) === 0) {
            return true;
        }

        return preg_match('/^[^\r\n\x0]*$/', $trail) === 1;
    }

    public function valid()
    {
        return $this->validatePrefix()
            && $this->validateCommand()
            && $this->validateParam()
            && $this->validateTrail();
    }

    /**
     * Parse a message string into components.
     * TODO: ugly as hell ;)
     *
     * @param string $str   The string to parse
     * @return Relay_Irc_Message
     */
    public function parse($str)
    {
        $str = rtrim($str);
        $p = 0;

        // Prefix first.
        $prefix = '';
        if ($str[0] === self::PREFIX) {
            for($p = 1; $p < strlen($str); $p++) {

                if ($str[$p] == self::SEGM_SEP) {
                    $p++;
                    break;
                }

                $prefix .= $str[$p];
            }
        }
        $this->setPrefix($prefix);

        // Command
        $cmd = '';
        for(; $p < strlen($str); $p++) {
            if ($str[$p] == self::SEGM_SEP) {
                break;
            }

            $cmd .= $str[$p];
        }
        $this->setCommand($cmd);

        $params = array();
        $param = false;
        for(; $p < strlen($str); $p++) {

            if ($str[$p] == self::SEGM_SEP) {

                if ($param !== false) {
                    $params[] = $param;
                    $param = false;
                }

                if ($str[$p+1] == self::PREFIX) {
                    $p += 2;
                    break;
                }
            } else {
                $param .= $str[$p];
            }
        }
        $this->setParam($params);

        $trail = '';
        for(; $p < strlen($str); $p++) {
            $trail .= $str[$p];
        }
        $this->setTrail($trail);
    }

    /**
     * Get a string representation of this object.
     * @see Relay_Irc_Message::getMessage()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getMessage();
    }
}

