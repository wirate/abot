<?php

require_once 'Relay/Irc/Helpers/Abstract.php';

class Relay_Irc_Helpers_Join extends Relay_Irc_Helpers_Abstract
{
    public function join($channels)
    {
        if(!is_array($channel)) {
            $channels = explode(',', $channels);
        }
        
        foreach($channels as &$val) {
        	if($val[0] !== '#' && $val[0] !== '$')
        		$val = '#' . $val;
        }
        
        $channels = implode(',', $channels);
        
        $msg = new Relay_Protocol_Message('JOIN');
        
        return $msg->setParam($channels);
    }
}
