<?php

class Relay_Event_Template 
{
	protected $components = array(
		'prefix'  => null,
		'command' => null,
		'params'  => null,
		'trail'   => null
	);
	
	public function __construct(array $rules) 
	{
		foreach($rules as $k => $v) {
			if(array_key_exists($k, $this->components)) {
				$this->components[$k] = $v;
			}
		}
	}

	public function check($values) 
	{
		$array['prefix'] = $values->getPrefix();
		$array['command'] = $values->getCommand();
		$array['params'] = $values->getParam(0);
		$array['trail'] = $values->getTrail();
		
		foreach($array as $comp => $value) {
		    if($this->components[$comp] === null) {
			     continue;
		    }
		
		    if(preg_match("/{$this->components[$comp]}/", $value) === 0) {
		    	return false;
		    }
		}
		
		return true;
	}

}
