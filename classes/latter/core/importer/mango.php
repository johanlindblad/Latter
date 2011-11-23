<?php

class Latter_Core_Importer_Mango extends Latter_Importer
{
	/*
	 * The fields this importer cares about
	 */
	protected $_fields = array();
	
	/*
	 * The model that's being imported
	 */
	protected $_model;
	
	/*
	 * Add the fields from the model into the form
	 * 
	 * @param An array containing data about the import. For now only the 'model' key will be recognized
	 * 		  but in the future more options may become available.
	 * @param The form to add the fields to.
	 */
	public function add_fields($data, Latter_Form $parent_form)
	{
		$this->_parent_form = $parent_form;
		
		// The importer creates its own sub form
		$form = Latter::factory()
				->name(arr::get($data, 'name', 'mango'));
		
		$model = $this->_model = arr::get($data, 'model');
		
		if( ! $model instanceof Mango)
		{
			return;
		}

		$fields = $model->fields();
		$rules = $model->rules();
		
		// Flip the fields and ignore_fields arrays so that we can look for field names in the array keys.
		foreach(array('fields', 'ignored_fields') as $key)
		{
			if(arr::get($data, $key))
			{
				$data[$key] = array_flip($data[$key]);
			}
		}
		
		foreach($fields as $name => $extra)
		{
			if(arr::get($data, 'fields') && arr::path($data, 'fields.'.$name) === NULL)
			{
				continue;
			}
			
			if(arr::get($data, 'ignored_fields') && arr::path($data, 'ignored_fields.'.$name) !== NULL)
			{
				continue;
			}
			
			$options = array();
			if(arr::get($extra, 'required') == TRUE)
			{
				$options['required'] = TRUE;
			}
			
			$options['rules'] = arr::get($rules, $name);
			$options['value'] = $model->$name;
			$options['importer_field'] = TRUE;
			
			switch(arr::get($extra, 'type'))
			{
				case 'string':
					$this->_fields[] = $name;
					$type = 'text';
					
					if(($name == 'password' && arr::get($extra, 'password') !== FALSE) || arr::get($extra, 'password') === TRUE)
					{
						if($model->loaded())
						{
							unset($options['required']);
						}
						unset($options['value']);
						$type = 'password';
					}
					
					$form->field($name, $type, $options);
					break;
					
				case 'email':
					$this->_fields[] = $name;
					$form->field($name, 'email', $options);
					break;
				
				case 'int':
					$this->_fields[] = $name;
					$type = arr::get($extra, 'input_type', 'number');
					
					if($type == 'range' && $step = arr::get($extra, 'step'))
					{
						$options['step'] = $step;
					}
					
					$form->field($name, $type, $options);
					break;
					
				case 'date':
					$this->_fields[] = $name;
					
					if($options['value'] instanceof MongoDate)
					{
						$options['value'] = Date('Y-m-d', $options['value']->sec);
					}
					
					$form->field($name, 'date', $options);
					break;
				
				case 'enum':
					if(is_array($values = arr::get($extra, 'values')))
					{
						$this->_fields[] = $name;
						$options['options'] = array_flip($values);
						$options['value'] = array_search($options['value'], arr::get($extra, 'values'));
						$form->field($name, 'select', $options);
					}
					break;
				
				case 'has_one':
					$form->import($model->$name, array('name' => $name));
					break;
			}
		}
		
		$this->_parent_form->sub_form($form);
		$this->_form =& $form;
	}
	
	/*
	 * Tell the importer that it's okay to load back the values from the form into the model.
	 */
	public function receive_values()
	{
		foreach($this->_form->values() as $field => $value)
		{
			if(in_array($field, $this->_fields))
			{
				/*
				 * Skip the field if it's called password and it's empty. This is so that if you do
				 * password hashing in create()/update(), the hash will only be changed if the form
				 * field contained something.
				 * Todo: also listen to the password-parameter used in the add_fields method.
				 */
				if($field == 'password' && empty($value))
				{
					continue;
				}
				
				$fields = $this->_model->fields();
				if($fields[$field]['type'] == 'enum')
				{
					$this->_model->$field = arr::get($fields[$field]['values'], $value);
					continue;
				}
				
				$this->_model->$field = $value;
			}
		}
	}
	
	/*
	 * Validate and return any errors
	 */
	public function validate()
	{
		$errors = array();
		
		try
		{
			$this->_model->check();
		}
		catch(Mango_Validation_Exception $e)
		{
			foreach($e->array->errors('') as $field_name => $error)
			{
				if(in_array($field_name, $this->_fields))
				{
					$this->_form->error($field_name, $error);
				}
			}
		}
	}
	
	public function valid()
	{
		return $this->_form->valid();
	}
}

?>