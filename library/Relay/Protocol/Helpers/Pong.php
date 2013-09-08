<?php

require_once 'Relay/Protocol/Helpers/Abstract.php';

require_once 'Relay/Protocol/Message.php';

class Relay_Protocol_Helpers_Pong extends Relay_Protocol_Helpers_Abstract
{
    public function pong($str)
    {
        return new Relay_Protocol_Message('', 'PONG', array(), $str);
    }
}