<?php

require_once 'Relay/Irc/Helpers/Abstract.php';

class Relay_Irc_Helpers_User extends Relay_Irc_Helpers_Abstract
{
    public function user($nickname, $hostname, $servername, $realname)
    {
        return new Relay_Protocol_Message('', 'USER', array($nickname, $hostname, $servername), $realname);
    }
}
