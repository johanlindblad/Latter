<?php

/**
 *
 * Class for that represents the form.
 *
 */

class Latter_Core_Form
{ 
	/*
	 * The name of the form
	 */
	protected $_name;
	
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
	 * This is set to the parent form is this is a sub form
	 */
	protected $_sub_form_of = NULL;
	
	/*
	 * Constructor.
	 *
	 * @param string $action action parameter for the form tag
	 * @param string $method method parameter for the form tag
	 */
	public function __construct($action = '', $method = 'POST')
	{
		$this->_name = 'form';
		$this->_action = $action;
		$this->_method = $method;

		$this->_importers = array();
		
		return $this;
	}
	
	public function name($set_to = NULL)
	{
		if($set_to)
		{
			$this->_name = $set_to;
			return $this;
		}
		
		return $this->_name;
	}
	
	public function full_name()
	{
		$name = $this->_name;
		
		if($this->_sub_form_of instanceof Latter_Form)
		{
			$name = strtr(':parent_name[:form_name]', array(
				':parent_name' => $this->_sub_form_of->full_name(),
				':form_name' => $this->name()
			));
		}
		
		return $name;
	}
	
	public function sub_form_of($set_to = NULL)
	{
		if($set_to === NULL)
		{
			return $this->_sub_form_of;
		}
		
		$this->_sub_form_of = $set_to;
		
		return $this;
	}
	
	public function field($field_name, $type, $params = array())
	{
		$class_name = 'Latter_Field_'.ucfirst($type);
		$params['form'] = $this;
		
		$this->_fields[$field_name] = new $class_name($field_name, $params);
		return $this;
	}
	
	public function sub_form(Latter_Form $form)
	{
		$form->sub_form_of($this);
		
		$this->_fields[$form->name()] = $form;
		
		return $this;
	}
	
	public function error($field_name, $error)
	{
		if($field = arr::get($this->_fields, $field_name))
		{
			$field->add_error($error);
		}
		
		$this->_valid = FALSE;
	}
	
	public function buttons($buttons = array())
	{
		if(empty($buttons))
		{
			if( ! $this->sub_form_of())
			{
				$buttons = array('submit' => TRUE);
			}
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
			
			$params['form'] = $this;

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
			if($field instanceof Latter_Form)
			{
				$values[$name] = $field->values();
			}
			else
			{
				$values[$name] = $field->value();
			}
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
		$valid = TRUE;
		
		foreach($this->_fields as $name => &$field)
		{
			if($field instanceof Latter_Form)
			{
				$field->validate();
				
				if( ! $field->valid())
				{
					$valid = FALSE;
				}
			}
			else
			{
				$field->add_rules($this->_validation);
			}
		}

		if( ! $this->_validation->check())
		{
			foreach($this->_validation->errors() as $name => $error)
			{
				if($field = arr::get($this->_fields, $name) AND $field instanceof Latter_Field)
				{
					$field->add_error($error);
				}
			}
		}

		$this->_valid OR $this->_valid = $valid;
		
		if($this->_importer instanceof Latter_Importer)
		{
			$this->_importer->validate();
			$this->_importer->valid() OR $this->_valid = FALSE;
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
					$values = arr::get(Request::current()->post(), $this->name(), array());
					break;
				case 'GET':
					$values = arr::get(Request::current()->query(), $this->name(), array());
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
			if($field instanceof Latter_Form)
			{
				$field->load(arr::get($values, $name));
			}
			else
			{
				$field->value(arr::get($values, $name));
			}
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
			'sub_form' => $this->_sub_form_of !== NULL,
		);
	}
}

?>