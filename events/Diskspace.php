<?php

require_once 'Relay/Event/Command.php';

class DiskspaceCommand extends Relay_Event_Command
{
    public function process()
    {
        $path = '/mnt/downloads';

        $msg = new Relay_Protocol_Message('PRIVMSG');
        $msg->setParam($this->message->getParam(0));

        $total = disk_total_space($path);
        $free = disk_free_space($path);


        $msg->setTrail("$path: {$this->_format($free)} / {$this->_format($total)} (" . floor($free / $total * 100) . "% left)");
        $this->caller->write($msg->getMessage());
    }

    protected function _format($size)
    {
        $suffix = array(
            'T' => 1099511627776,
            'G' => 1073741824,
            'M' => 1048576,
            'K' => 1024,
        );

        foreach($suffix as $u => $v) {
            if ($size < $v) {
                continue;
            }

            $size /= $v;

            if ($size < 10) {
                $dec = 1;
            } else {
                $dec = 0;
            }

            return round($size + ((1 / pow(10, 1 + $dec)) * 5), $dec) . $u;
        }

        return $size;
    }

}
