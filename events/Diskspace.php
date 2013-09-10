<?php

require_once 'Relay/Measure/Binary.php';

require_once 'Relay/Event/Command.php';

class DiskspaceCommand extends Relay_Event_Command
{
    public function process()
    {
        $path = '/mnt/downloads';

        $msg = new Relay_Irc_Message('PRIVMSG');
        $msg->setParam($this->message->getParam(0));

        $total = disk_total_space($path);
        $free = disk_free_space($path);

        if ($total == 0) {
            $left = 0;
        } else {
            $left = floor($free / $total * 100);
        }
        
        $total = new Relay_Measure_Binary($total, Relay_Measure_Abstract::OPTIMAL, 2);
        $free = new Relay_Measure_Binary($free, Relay_Measure_Abstract::OPTIMAL, 2);

        $msg->setTrail("$path: {$free} / {$total} ({$left}% left)");
        $this->caller->write($msg->getMessage());
    }
}
