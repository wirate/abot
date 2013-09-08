<?php

require_once 'Relay/Event/Command.php';
require_once 'models/Release.php';

class ReleaseCommand extends Relay_Event_Command
{
	public function process()
	{
	    $msg = new Relay_Protocol_Message('PRIVMSG');
	    $msg->setParam($this->message->getParam(0));
	    
	    $search = $this->request->getAll();
	    unset($search[0], $search[1]);
	    $search = implode(' ', $search);
	    
	    if(strlen($search) == 0) {
	        $msg->setTrail('Not enough parameters');
	        $this->caller->write($msg->getMessage());
	        return;
	    }
	    
		$model = new ReleaseModel();
		
		$r = $model->getRelease($search);
		
		if(empty($r)) {
		    $msg->setTrail("No release found");
		} else {
		    if(count($r) > 3) {
			$msg->resetParams();
			$nick = $this->message->getPrefix();
			$nick = substr($nick, 0, strpos($nick, '!'));
		        $msg->setParam($nick);
		    }

	 	    foreach($r as $row) {
			
			if($row['complete'] == "0") {
				$str = '[' . chr(2).chr(3) . '4I' . chr(2).chr(3) . '] ';
			} else {
				$str = '[' . chr(2).chr(3) . '3C' . chr(2).chr(3) . '] ';
			}

			$str .= "/mnt/downloads/" . $row['path'];

		    	$msg->setTrail($str);
			$this->caller->write($msg->getMessage());
		    }
		    return;
		}
		
		$this->caller->write($msg->getMessage());
	}
}
