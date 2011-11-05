<?php

class Latter_Core_Field_Range extends Latter_Field_Input
{
	protected $_step;
	
	public function initialize($params)
	{
		$this->_type = 'range';
		parent::initialize($params);
		$this->_template = 'range';
		$this->_step = arr::get($params, 'step', FALSE);
	}
	
	function attributes()
	{
		$array = parent::attributes();
		
		foreach($this->_rules as $rule)
		{
			$rule_name = arr::get($rule, 0);
			if(in_array($rule_name, array('min_value', 'max_value')))
			{
				$array[$rule_name] = arr::get(arr::get($rule, 1), 1);
			}
		}
		
		if($this->_step)
		{
			$array['step'] = $this->_step;
		}
		
		return $array;
	}
}

?>