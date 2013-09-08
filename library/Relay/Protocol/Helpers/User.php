<?php

require_once 'Relay/Protocol/Helpers/Abstract.php';

class Relay_Protocol_Helpers_User extends Relay_Protocol_Helpers_Abstract
{
    public function user($nickname, $hostname, $servername, $realname)
    {
        return new Relay_Protocol_Message('', 'USER', array($nickname, $hostname, $servername), $realname);
    }
}