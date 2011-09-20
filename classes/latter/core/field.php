<?php

/*
 *
 * An abstract class that represents a field.
 *
 */

abstract class Latter_Core_Field extends Latter_Control
{
	/*
	 * The field's value
	 */
	protected $_value;
	
	/*
	 * The field's validation rules.
	 */
	protected $_rules;
	
	/*
	 * The field's error message (if any)
	 */
	protected $_error;
	
	/*
	 * Whether or not the field was added by an importer
	 */
	protected $_importer_field;
	
	/*
	 * Whether the field is required or not
	 */
	protected $_required;
	
	/*
	 * Any extra stuff that needs to run after the constructor
	 */
	public function initialize($params)
	{
		$this->_value = arr::get($params, 'value');
		$this->_error = NULL;
		$this->_rules = array();
		$this->_view_class = 'View_Latter_Field';
		
		if($rules = arr::get($params, 'rules'))
		{
			foreach($rules as $rule)
			{
				$this->_rules[] = $rule;
			}
		}
		
		if(arr::get($params, 'required'))
		{
			$this->_required = TRUE;
		}
		
		if(arr::get($params, 'importer_field'))
		{
			$this->_importer_field = TRUE;
		}
	}
	
	public function render()
	{
		$class_name = $this->_view_class;
		$view = new $class_name('latter/field', array('field' => 'latter/field/'.$this->_template));
		
		foreach($this->attributes() as $name => $data)
		{
			$view->$name = $data;
		}
		
		return $view->render();
	}
			
	public function value($set = NULL)
	{
		if(func_num_args() == 0)
		{
			return $this->_value;
		}
		
		$this->_value = $set;
	}

	public function add_rules(Validation &$validation)
	{
		if($this->_importer_field == TRUE)
		{
			return;
		}
		
		foreach($this->_rules as $rule)
		{
			$validation->rule($this->_name, arr::get($rule, 0), arr::get($rule, 1));
		}
	}
	
	public function add_error($error)
	{
		if(is_array($error))
		{
			$this->_error = arr::get($error, 0);
		}
		else if(is_string($error))
		{
			$this->_error = $error;
		}
	}
	
	protected function attributes()
	{
		$attributes = parent::attributes();
		
		$attributes['value'] = $this->_value;
		$attributes['rules'] = $this->_rules;
		$attributes['error'] = $this->_error;
		$attributes['required'] = $this->_required;
		
		return $attributes;
	}
}

?>