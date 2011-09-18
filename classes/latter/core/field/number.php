<?php

class Latter_Core_Field_Number extends Latter_Field_Input
{
	public function initialize($params)
	{
		$this->_type = 'number';
		parent::initialize($params);
		$this->_template = 'number';
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
		
		return $array;
	}
}

?>