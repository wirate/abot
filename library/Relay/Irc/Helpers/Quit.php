<?php

require_once 'Relay/Irc/Helpers/Abstract.php';

class Relay_Irc_Helpers_Quit extends Relay_Irc_Helpers_Abstract
{
    public function quit($message)
    {
        $msg = new Relay_Protocol_Message('QUIT');
        return $msg->setTrail($message);
    }
}
