<?php

class Latter_Mango extends Mango_Core
{
	public function fields()
	{
		return $this->_fields;
	}
	
	/*
	 *
	 * This is taken from the _check() method
	 *
	 */
	public function rules()
	{
		$data = array();	
		
		foreach ($this->_fields as $name => $field)
		{
			$rules = array();
			if ( $field['type'] === 'email')
			{
				$rules[] = array('email');
			}

			if ( Arr::get($field,'required'))
			{
				$rules[] = array('not_empty');
			}

			if ( Arr::get($field,'unique'))
			{
				$rules[] = array(array(':model', '_is_unique'), array(':validation', $name));
			}

			foreach ( array('min_value','max_value','min_length','max_length') as $rule)
			{
				if ( Arr::get($field, $rule) !== NULL)
				{
					$rules[] = array($rule, array(':value', $field[$rule]));
				}
			}

			if ( isset($field['rules']))
			{
				foreach($field['rules'] as $current_rule)
				{
					$rules[] = $current_rule;
				}
			}
			
			$data[$name] = $rules;
		}

		return $data;
	}
}

?>