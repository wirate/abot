<?php

require_once 'Relay/Protocol/Helpers/Abstract.php';

class Relay_Protocol_Helpers_Quit extends Relay_Protocol_Helpers_Abstract
{
    public function quit($message)
    {
        $msg = new Relay_Protocol_Message('QUIT');
        return $msg->setTrail($message);
    }
}