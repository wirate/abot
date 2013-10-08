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
     * Static Array containing command codes
     *
     * @see http://www.irchelp.org/irchelp/rfc/chapter6.html
     * @var Array
     */
    static protected $codes = array(
        // Reply
        001 => 'RPL_WELCOME',
        002 => 'RPL_YOURHOST',
        003 => 'RPL_CREATED',
        004 => 'RPL_MYINFO',
        005 => 'RPL_BOUNCE',

        200 => 'RPL_TRACELINK',
        201 => 'RPL_TRACECONNECTING',
        202 => 'RPL_TRACEHANDSHAKE',
        203 => 'RPL_TRACEUNKNOWN',
        204 => 'RPL_TRACEOPERATOR',
        205 => 'RPL_TRACEUSER',
        206 => 'RPL_TRACESERVER',
        207 => 'RPL_TRACESERVICE',
        208 => 'RPL_TRACENEWTYPE',
        209 => 'RPL_TRACECLASS',
        210 => 'RPL_TRACERECONNECT',

        211 => 'RPL_STATSLINKINFO',
        212 => 'RPL_STATSCOMMANDS',
        219 => 'RPL_ENDOFSTATS',
        221 => 'RPL_UMODEIS',
        234 => 'RPL_SERVLIST',
        235 => 'RPL_SERVLISTEND',
        242 => 'RPL_STATSUPTIME',
        243 => 'RPL_STATSOLINE',
        244 => 'RPL_STATSHLINE',
        251 => 'RPL_LUSERCLIENT',
        252 => 'RPL_LUSEROP',
        253 => 'RPL_LUSERUNKOWN',
        254 => 'RPL_LUSERCHANNELS',
        255 => 'RPL_LUSERME',
        256 => 'RPL_ADMINME',
        257 => 'RPL_ADMINLOC1',
        258 => 'RPL_ADMINLOC2',
        259 => 'RPL_ADMINEMAIL',

        261 => 'RPL_TRACELOG',
        262 => 'RPL_TRACEEND',

        // 300 => 'RPL_NONE',
        301 => 'RPL_AWAY',
        302 => 'RPL_USERHOST',
        303 => 'RPL_ISON',
        305 => 'RPL_UNAWAY',
        311 => 'RPL_WHOISUSER',
        312 => 'RPL_WHOISSERVER',
        313 => 'RPL_WHOISOPERATOR',
        314 => 'RPL_WHOWASUSER',
        315 => 'RPL_ENDOFWHO',
        317 => 'RPL_WHOISIDLE',
        318 => 'RPL_ENDOFSHOIS',
        319 => 'RPL_WHOISCHANNELS',
        // 321 => 'RPL_LISTSTART',
        322 => 'RPL_LIST',
        323 => 'RPL_LISTEND',
        324 => 'RPL_CHANNELMODEIS',
        325 => 'RPL_UNIQOPIS',
        331 => 'RPL_NOTOPIC',
        332 => 'RPL_TOPIC',
        341 => 'RPL_INVITING',
        342 => 'RPL_SUMMONING',
        346 => 'RPL_INVITELIST',
        347 => 'RPL_ENDOFINVITELIST',
        348 => 'RPL_EXCEPTLIST',
        349 => 'RPL_ENDOFEXCEPTLIST',
        351 => 'RPL_VERSION',
        352 => 'RPL_WHOREPLY',
        353 => 'RPL_NAMREPLY',
        364 => 'RPL_LINKS',
        365 => 'RPL_ENDOFLINKS',
        366 => 'RPL_ENDOFNAMES',
        367 => 'RPL_BANLIST',
        368 => 'RPL_ENDOFBANLIST',
        369 => 'RPL_ENDOFWHOWAS',
        371 => 'RPL_INFO',
        374 => 'RPL_ENDOFINFO',
        375 => 'RPL_MOTDSTART',
        372 => 'RPL_MOTD',
        376 => 'RPL_ENDOFMOTD',
        381 => 'RPL_YOUREOPER',
        382 => 'RPL_REHASHING',
        383 => 'RPL_YOURESERVICE',
        391 => 'RPL_TIME',
        392 => 'RPL_USERSSTART',
        393 => 'RPL_USERS',
        394 => 'RPL_ENDOFUSERS',
        395 => 'RPL_NOUSERS',
        
        // Error
        401 => 'ERR_NOSUCHNICK',
        402 => 'ERR_NOSUCHSERVER',
        403 => 'ERR_NOSUCHCHANNEL',
        404 => 'ERR_CONNOTSENDTOCHANNEL',
        405 => 'ERR_TOOMANYCHANNELS',
        406 => 'ERR_WASNOSUCHNICK',
        407 => 'ERR_TOOMANYTARGETS',
        409 => 'ERR_NOORGIN',
        411 => 'ERR_NORECIPIENT',
        412 => 'ERR_NOTEXTTOSEND',
        413 => 'ERR_NOTOPLEVEL',
        414 => 'ERR_WILDTOPLEVEL',
        421 => 'ERR_UNKNOWNCOMMAND',
        422 => 'ERR_NOMOTD',
        423 => 'ERR_NOADMININFO',
        424 => 'ERR_FILEERROR',
        431 => 'ERR_NONICKNAMEGIVEN',
        432 => 'ERR_ERRONEUSNICKNAME',
        433 => 'ERR_NICKNAMEINUSE',
        436 => 'ERR_NICKCOLLISION',
        441 => 'ERR_USERNOTINCHANNEL',
        442 => 'ERR_NOTONCHANNEL',
        443 => 'ERR_USERONCHANNEL',
        444 => 'ERR_NOLOGIN',
        445 => 'ERR_SUMMONDISABLED',
        446 => 'ERR_USERDISABLED',
        451 => 'ERR_NOTREGISTERED',
        461 => 'ERR_NEEDMOREPARAMS',
        462 => 'ERR_ALREADYREGISTRED',
        463 => 'ERR_NOPERMFORHOST',
        464 => 'ERR_PASSWDMISMATCH',
        465 => 'ERR_YOUREBANNEDCREEP',
        467 => 'ERR_KEYSET',
        471 => 'ERR_CHANNELISFULL',
        472 => 'ERR_UNKNOWNMODE',
        473 => 'ERR_INVITEONLYCHAN',
        474 => 'ERR_BANNEDFROMCHAN',
        475 => 'ERR_BADCHANNELKEY',
        481 => 'ERR_NOPRIVILEGES',
        482 => 'ERR_CHANOPRIVSNEEDED',
        483 => 'ERR_CANTKILLSERVER',
        491 => 'ERR_NOOPERHOST',
        501 => 'ERR_UMODEUNKOWNFLAG',
        502 => 'ERR_USERSDONTMATCH'
    );

    /**
     * Prefix component
     *
     * @var String
     */
    protected $_prefix;

    /**
     * Command component
     *
     * @var String
     */
    protected $_command;

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
    protected $_trail;

    /**
     * Construct a new Irc message.
     *
     * @param string $command   The command component
     * @return Relay_Irc_Message
     */
    public function __construct($command)
    {
        $this->setCommand($command);

        return $this;
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
        $cmd = $this->_command;
        if (ctype_digit($cmd)) {
            if (array_key_exists($cmd, self::$codes))
                return self::$codes[$cmd];
        }
        return $cmd;
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

        return preg_match('/^[0-9]{1,3}$|^[A-z]+$/', $command) === 1;
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
    static public function fromString($str)
    {
        $str = rtrim($str);
        $p = 0;

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

        $cmd = '';
        for(; $p < strlen($str); $p++) {
            if ($str[$p] == self::SEGM_SEP) {
                break;
            }

            $cmd .= $str[$p];
        }

        $obj = new self($cmd);

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

        $trail = '';
        for(; $p < strlen($str); $p++) {
            $trail .= $str[$p];
        }

        $obj->setParam($params);
        $obj->setPrefix($prefix);
        $obj->setTrail($trail);

        return $obj;
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

