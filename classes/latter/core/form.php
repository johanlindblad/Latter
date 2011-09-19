<?php

/**
 *
 * Class for that represents the form.
 *
 */

class Latter_Core_Form
{ 
	/*
	 * An array containing the fields themselves.
	 */
	protected $_fields = array();
	
	/*
	 * The buttons at the end
	 */
	protected $_buttons = array();
	
	/*
	 * The form action
	 */
	protected $_action = array();
	
	/*
	 * The form method
	 */
	protected $_method = array();
	
	/*
	 * The validation object
	 */
	protected $_validation;
	
	/*
	 * The form's values
	 */
	protected $_values;
	
	/*
	 * The importer used in the form, if any
	 */
	protected $_importer;
	
	/*
	 * Whether the form's valid or not
	 */
	protected $_valid;
	
	/*
	 * Whether the form's sent or not
	 */
	protected $_sent;
	
	/*
	 * Constructor.
	 *
	 * @param string $action action parameter for the form tag
	 * @param string $method method parameter for the form tag
	 */
	public function __construct($action = '', $method = 'POST')
	{
		$this->_action = $action;	
		$this->_method = $method;
		
		$this->_importers = array();
		
		return $this;
	}
	
	public function field($field_name, $type, $params = array())
	{
		$class_name = 'Latter_Field_'.ucfirst($type);
		$this->_fields[$field_name] = new $class_name($field_name, $params);
		return $this;
	}
	
	public function buttons($buttons = array())
	{
		if(empty($buttons))
		{
			$buttons = array('submit' => TRUE);
		}
		
		foreach($buttons as $name => $params)
		{
			// This will be true if the buttons was defined like array('submit', 'cancel')
			if(is_numeric($name) && is_string($params))
			{
				$name = $params;
				$params = array();
			}
			
			$class_name = 'Latter_Button_'.$name;
			
			if( ! is_array($params))
			{
				$params = array();
			}
			
			$this->_buttons[$name] = new $class_name($name, $params);
		}

		return $this;
	}
	
	public function import($from, $data = array())
	{
		if(is_object($from))
		{
			if($from instanceof Mango)
			{
				$data['model'] = $from;
				$from = 'Mango';
			}
			else if($from instanceof ORM)
			{
				$data['model'] = $from;
				$from = 'ORM';
			}
		}

		$class_name = 'Latter_Importer_'.ucfirst($from);
		
		if(class_exists($class_name))
		{
			$this->_importer = $importer = new $class_name();
			$importer->add_fields($data, $this);
		}
		
		return $this;
	}
	
	function values()
	{
		$values = array();
		
		foreach($this->_fields as $name => &$field)
		{
			$values[$name] = $field->value();
		}
		
		$this->_values = $values;
		
		return $this->_values;
	}
	
	function validate()
	{
		if( ! $this->sent)
		{
			return $this;
		}
		
		$this->_validation = Validation::factory($this->values());
		
		foreach($this->_fields as &$field)
		{
			$field->add_rules($this->_validation);
		}
				
		$valid = $this->_valid = $this->_validation->check();
		
		if( ! $valid)
		{
			$errors = $this->_validation->errors();
			foreach($errors as $name => $error)
			{
				$this->_fields[$name]->add_error($error);
			}
		}
		
		try
		{
			$this->_importer->model()->check();
		}
		catch(Validation_Exception $e)
		{
			foreach($e->array->errors('') as $field_name => $error)
			{
				arr::get($this->_fields, $field_name)->add_error($error);
			}
		}
		
		return $this;
	}
	
	public function valid()
	{
		return $this->_valid == TRUE;
	}
	
	public function load($values = NULL)
	{
		if($values == NULL)
		{
			switch($this->_method)
			{
				case 'POST':
					$values = Request::current()->post();
					break;
				case 'GET':
					$values = Request::current()->query();
					break;
				default:
					$values = array();
					break;
			}
		}
				
		$this->sent = ! empty($values);
				
		if( ! $this->sent)
		{
			return $this;
		}

		foreach($this->_fields as $name => $field)
		{
			$field->value(arr::get($values, $name));
		}
		
		if($this->_importer)
		{
			$this->_importer->receive_values();
		}
		
		return $this;
	}
	
	public function sent()
	{
		return $this->sent;
	}
	
	public function render()
	{
		if(empty($this->_buttons))
		{
			$this->buttons();
		}
		
		$view = new View_Latter_Form('latter/form', array('open' => 'latter/open', 'close' => 'latter/close'));

		$view->fields = new Latter_Renderable_Container($this->_fields);
		$view->buttons = new Latter_Renderable_Container($this->_buttons);

		$view->data = $this->arguments();

		return $view;
	}
	
	public function __toString()
	{
		return (String) $this->render();
	}
	
	protected function arguments()
	{
		return array(
			'action' => $this->_action,
			'method' => $this->_method,
		);
	}
}

?>