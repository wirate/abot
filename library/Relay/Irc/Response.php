<?php

require_once 'Relay/Irc/Message.php';

class Relay_Irc_Response extends Relay_Irc_Message
{
    /**
     * Static Array containing command codes
     *
     * @see http://www.irchelp.org/irchelp/rfc/chapter6.html
     * @var Array
     */
    static protected $codes = array(
        // Reply
        '001' => 'RPL_WELCOME',
        '002' => 'RPL_YOURHOST',
        '003' => 'RPL_CREATED',
        '004' => 'RPL_MYINFO',
        '005' => 'RPL_BOUNCE',

        '200' => 'RPL_TRACELINK',
        '201' => 'RPL_TRACECONNECTING',
        '202' => 'RPL_TRACEHANDSHAKE',
        '203' => 'RPL_TRACEUNKNOWN',
        '204' => 'RPL_TRACEOPERATOR',
        '205' => 'RPL_TRACEUSER',
        '206' => 'RPL_TRACESERVER',
        '207' => 'RPL_TRACESERVICE',
        '208' => 'RPL_TRACENEWTYPE',
        '209' => 'RPL_TRACECLASS',
        '210' => 'RPL_TRACERECONNECT',

        '211' => 'RPL_STATSLINKINFO',
        '212' => 'RPL_STATSCOMMANDS',
        '219' => 'RPL_ENDOFSTATS',
        '221' => 'RPL_UMODEIS',
        '234' => 'RPL_SERVLIST',
        '235' => 'RPL_SERVLISTEND',
        '242' => 'RPL_STATSUPTIME',
        '243' => 'RPL_STATSOLINE',
        '244' => 'RPL_STATSHLINE',
        '251' => 'RPL_LUSERCLIENT',
        '252' => 'RPL_LUSEROP',
        '253' => 'RPL_LUSERUNKOWN',
        '254' => 'RPL_LUSERCHANNELS',
        '255' => 'RPL_LUSERME',
        '256' => 'RPL_ADMINME',
        '257' => 'RPL_ADMINLOC1',
        '258' => 'RPL_ADMINLOC2',
        '259' => 'RPL_ADMINEMAIL',

        '261' => 'RPL_TRACELOG',
        '262' => 'RPL_TRACEEND',

        // '300' => 'RPL_NONE',
        '301' => 'RPL_AWAY',
        '302' => 'RPL_USERHOST',
        '303' => 'RPL_ISON',
        '305' => 'RPL_UNAWAY',
        '311' => 'RPL_WHOISUSER',
        '312' => 'RPL_WHOISSERVER',
        '313' => 'RPL_WHOISOPERATOR',
        '314' => 'RPL_WHOWASUSER',
        '315' => 'RPL_ENDOFWHO',
        '317' => 'RPL_WHOISIDLE',
        '318' => 'RPL_ENDOFSHOIS',
        '319' => 'RPL_WHOISCHANNELS',
        // '321' => 'RPL_LISTSTART',
        '322' => 'RPL_LIST',
        '323' => 'RPL_LISTEND',
        '324' => 'RPL_CHANNELMODEIS',
        '325' => 'RPL_UNIQOPIS',
        '331' => 'RPL_NOTOPIC',
        '332' => 'RPL_TOPIC',
        '341' => 'RPL_INVITING',
        '342' => 'RPL_SUMMONING',
        '346' => 'RPL_INVITELIST',
        '347' => 'RPL_ENDOFINVITELIST',
        '348' => 'RPL_EXCEPTLIST',
        '349' => 'RPL_ENDOFEXCEPTLIST',
        '351' => 'RPL_VERSION',
        '352' => 'RPL_WHOREPLY',
        '353' => 'RPL_NAMREPLY',
        '364' => 'RPL_LINKS',
        '365' => 'RPL_ENDOFLINKS',
        '366' => 'RPL_ENDOFNAMES',
        '367' => 'RPL_BANLIST',
        '368' => 'RPL_ENDOFBANLIST',
        '369' => 'RPL_ENDOFWHOWAS',
        '371' => 'RPL_INFO',
        '374' => 'RPL_ENDOFINFO',
        '375' => 'RPL_MOTDSTART',
        '372' => 'RPL_MOTD',
        '376' => 'RPL_ENDOFMOTD',
        '381' => 'RPL_YOUREOPER',
        '382' => 'RPL_REHASHING',
        '383' => 'RPL_YOURESERVICE',
        '391' => 'RPL_TIME',
        '392' => 'RPL_USERSSTART',
        '393' => 'RPL_USERS',
        '394' => 'RPL_ENDOFUSERS',
        '395' => 'RPL_NOUSERS',

        // Error
        '401' => 'ERR_NOSUCHNICK',
        '402' => 'ERR_NOSUCHSERVER',
        '403' => 'ERR_NOSUCHCHANNEL',
        '404' => 'ERR_CONNOTSENDTOCHANNEL',
        '405' => 'ERR_TOOMANYCHANNELS',
        '406' => 'ERR_WASNOSUCHNICK',
        '407' => 'ERR_TOOMANYTARGETS',
        '409' => 'ERR_NOORGIN',
        '411' => 'ERR_NORECIPIENT',
        '412' => 'ERR_NOTEXTTOSEND',
        '413' => 'ERR_NOTOPLEVEL',
        '414' => 'ERR_WILDTOPLEVEL',
        '421' => 'ERR_UNKNOWNCOMMAND',
        '422' => 'ERR_NOMOTD',
        '423' => 'ERR_NOADMININFO',
        '424' => 'ERR_FILEERROR',
        '431' => 'ERR_NONICKNAMEGIVEN',
        '432' => 'ERR_ERRONEUSNICKNAME',
        '433' => 'ERR_NICKNAMEINUSE',
        '436' => 'ERR_NICKCOLLISION',
        '441' => 'ERR_USERNOTINCHANNEL',
        '442' => 'ERR_NOTONCHANNEL',
        '443' => 'ERR_USERONCHANNEL',
        '444' => 'ERR_NOLOGIN',
        '445' => 'ERR_SUMMONDISABLED',
        '446' => 'ERR_USERDISABLED',
        '451' => 'ERR_NOTREGISTERED',
        '461' => 'ERR_NEEDMOREPARAMS',
        '462' => 'ERR_ALREADYREGISTRED',
        '463' => 'ERR_NOPERMFORHOST',
        '464' => 'ERR_PASSWDMISMATCH',
        '465' => 'ERR_YOUREBANNEDCREEP',
        '467' => 'ERR_KEYSET',
        '471' => 'ERR_CHANNELISFULL',
        '472' => 'ERR_UNKNOWNMODE',
        '473' => 'ERR_INVITEONLYCHAN',
        '474' => 'ERR_BANNEDFROMCHAN',
        '475' => 'ERR_BADCHANNELKEY',
        '481' => 'ERR_NOPRIVILEGES',
        '482' => 'ERR_CHANOPRIVSNEEDED',
        '483' => 'ERR_CANTKILLSERVER',
        '491' => 'ERR_NOOPERHOST',
        '501' => 'ERR_UMODEUNKOWNFLAG',
        '502' => 'ERR_USERSDONTMATCH'
    );

    /**
     * Set the command component
     *
     * @param int|string $command
     * @return Relay_Irc_Response
     */
    public function setCommand($command)
    {
        if (is_int($command)) {
            if ($command < 10) {
                $command = '00' . $command;
            } else if ($command < 100) {
                $command = '0' . $command;
            }
        }
        return parent::setCommand($command);
    }

    public function getCommandName()
    {
        if (array_key_exists($this->_command, self::$codes)) {
            return self::$codes[$this->_command];
        }
        return $this->_command;
    }

    public function setCommandName($command)
    {
        $key = array_search($command, self::$codes);
        if ($key === false) {
            throw new InvalidArgumentException("Invalid command '$command'");
        }
        return parent::setCommand($key);
    }

    public function validateCommand($command = null)
    {
        if ($command === null) {
            $command = $this->_command;
        }

        return preg_match('/^[0-9]{1,3}$|^[A-z]+$/', $command) === 1;
    }
}