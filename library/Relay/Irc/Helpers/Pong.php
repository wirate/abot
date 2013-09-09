<?php

require_once 'Relay/Irc/Helpers/Abstract.php';

require_once 'Relay/Protocol/Message.php';

class Relay_Irc_Helpers_Pong extends Relay_Irc_Helpers_Abstract
{
    public function pong($str)
    {
        return new Relay_Protocol_Message('', 'PONG', array(), $str);
    }
}
